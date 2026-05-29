<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/dompdf/autoload.inc.php');

use Dompdf\Dompdf;

class Admin_ret_purchase extends CI_Controller {

	const VIEW_FOLDER = 'ret_purchase/';

	const RET_PUR_ORDER_MODEL = 'ret_purchase_order_model';

	const SETT_MOD	= "admin_settings_model";

	const IMG_PATH  = 'assets/img/';

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model(self::RET_PUR_ORDER_MODEL);

		$this->load->model("admin_settings_model");

		$this->load->model("log_model");

		$this->load->model("admin_usersms_model");

		$this->load->model('email_model');

		$this->load->model("sms_model");

		$this->load->model("ret_reports_model");

		$this->load->model("ret_catalog_model");

		$this->load->model(self::SETT_MOD);

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

    function check_cheque_number_exist()
    {
        // Clear any previous output
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');

        try {
            $cheque_no = $this->input->post('cheque_no');
            
            if(!empty($cheque_no)) {
                // Ensure model is loaded
                if (!isset($this->ret_purchase_order_model)) {
                     $this->load->model(self::RET_PUR_ORDER_MODEL);
                }

                $exists = $this->ret_purchase_order_model->check_cheque_number_exist($cheque_no);
                
                if ($exists) {
                    echo json_encode(array('status' => false, 'message' => 'Cheque number already exists!'));
                } else {
                    echo json_encode(array('status' => true));
                }
            } else {
                // No cheque number provided, treat as valid (or handle error)
                echo json_encode(array('status' => true)); 
            }
        } catch (Exception $e) {
            log_message('error', 'check_cheque_number_exist Error: '.$e->getMessage());
            // Return failure response in case of exception
            echo json_encode(array('status' => true)); 
        }
        exit;
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



    //Order Description

	function order_description($type="",$id="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;



		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'order_description/list';
					
					$data['access'] = $this->$SETT_MOD->get_access('admin_ret_purchase/order_description/list');

        			$this->load->view('layout/template', $data);

			break;

			case 'save':

			    $insData=array(

			                  'description'=>$_POST['order_des'],

			                  'created_on' => date("Y-m-d H:i:s"),

				              'created_by' => $this->session->userdata('uid')

			                  );

			     $this->db->trans_begin();

			     $insOrder = $this->$model->insertData($insData,'ret_purchase_order_description');

			    if($this->db->trans_status()===TRUE)

    			{

    				$this->db->trans_commit();

					  $log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'ORDER DESCRIPTION',

						'operation'     => 'Add',

						'record'        => $insOrder,

						'remark'        => 'Order Description added successfully'

						);

					$this->log_model->log_detail('insert','',$log_data);

    				$this->session->set_flashdata('chit_alert',array('message'=>'Order Instructions Added Successfully','class'=>'success','title'=>'Order Instructions'));

    				$return_data=array('status'=>TRUE,'message'=>'Order Instructions Added Successfully..');

    			}

    			else

    			{

    			    echo $this->db->last_query();exit;

    				$this->db->trans_rollback();

    				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Order Instructions'));

    				$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

    			}

    			echo json_encode($return_data);

			break;



			case 'delete':

    			   $this->$model->deleteData('id_order_des',$id,'ret_purchase_order_description');

    	           if($this->db->trans_status()===TRUE)

    			    {

    			    	  $this->db->trans_commit();

						  $log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'ORDER DESCRIPTION',

							'operation'     => 'Delete',

							'record'        =>  $id,

							'remark'        => 'Order Description deleted successfully'

							);

	                         $this->log_model->log_detail('insert','',$log_data);

    					  $this->session->set_flashdata('chit_alert', array('message' => 'Order Instructions deleted successfully','class' => 'success','title'=>'Delete Order Instructions'));

    				}

    			   else

    			    {

    				 $this->db->trans_rollback();

    				 $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Delete Order Instructions'));

    			    }

    			 redirect('admin_ret_purchase/order_description/list');

			 break;



			 case 'edit':

			    $model  =	self::RET_PUR_ORDER_MODEL;

                $data   =  $this->$model->get_orderdescription($id);

                echo json_encode($data);

			 break;



			 case 'update':

			     $updData=array(

			                   'description'=>$_POST['order_des'],

			                   'updated_on' => date("Y-m-d H:i:s"),

				               'updated_by' => $this->session->userdata('uid')

			                   );

			     $this->db->trans_begin();

			     $id_order_des =$this->$model->updateData($updData,'id_order_des',$_POST['id_order_des'],'ret_purchase_order_description');

			    if($this->db->trans_status()===TRUE)

    			{

    				$this->db->trans_commit();


				    $log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'ORDER DESCRIPTION',

						'operation'     => 'Update',

						'record'        =>  $id_order_des,

						'remark'        => 'Order Description Updated successfully'

						);

			          $this->log_model->log_detail('insert','',$log_data);


    				$this->session->set_flashdata('chit_alert',array('message'=>'Order Instructions Updated Successfully','class'=>'success','title'=>'Order Instructions'));

    				$return_data=array('status'=>TRUE,'message'=>'Order Instructions Added Successfully..');

    			}

    			else

    			{



    			    echo $this->db->last_query();exit;

    				$this->db->trans_rollback();

    				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Order Instructions'));

    				$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

    			}

    			echo json_encode($return_data);

			 break;



		    default:

			        $list=$this->$model->get_order_description();

				  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/order_description/list');

			        $data = array(

			        					'list'  => $list,

										'access'=> $access

			        				);

					echo json_encode($data);

		}

	}

	//Order Description



    //Purchase Order





    function get_customer_order_pending_details()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_customer_order_pending_details($_POST);

        echo json_encode($data);

	}





	function get_stock_repair_order_details()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_stock_repair_order($_POST);

        echo json_encode($data);

	}



    function purchase($type="",$id="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$set_model ="admin_settings_model";

		switch($type)

		{
			case 'list':

			        $this->session->unset_userdata('pur_entry_secret_key');

					$data['main_content'] = self::VIEW_FOLDER.'purchase_entry_list';

					$data['access'] = $this->$set_model->get_access('admin_ret_purchase/purchase/list');

					


        			$this->load->view('layout/template', $data);

			break;

			case 'pur_order':

					$data['main_content'] = self::VIEW_FOLDER.'pur_order_list';

					$data['access'] = $this->$set_model->get_access('admin_ret_purchase/purchase/pur_order');

					
					// print_r($data['access']);exit;

        			$this->load->view('layout/template', $data);

			break;

			case 'add':

			        $data['po_item']=$this->$model->get_empty_record();
                    
                    $data['gold_rate'] = $this->$model->get_gold_rate();

					$data['main_content'] = self::VIEW_FOLDER.'order_form';

        			$this->load->view('layout/template', $data);

			break;

			case 'cancel_po_entry':

                    $updData=array(

                    'bill_status'       => 2,

                    'cancel_reason'     => $_POST['cancel_reason'],

                    'cancelled_date'        => date("Y-m-d H:i:s"),

                    'cancelled_by'        => $this->session->userdata('uid')

                    );

                    $this->db->trans_begin();

                    $this->$model->updateData($updData,'po_id',$_POST['po_id'],'ret_purchase_order');

                    if($this->db->trans_status()===TRUE)

                    {

                        $this->db->trans_commit();

						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'Supplier Bill Entry',

							'operation'     => 'Cancel',

							'record'        =>  $_POST['po_id'],

							'remark'        => 'Supplier Bill Entry Cancelled successfully'

							);

	                    $this->log_model->log_detail('insert','',$log_data);

                        $this->session->set_flashdata('chit_alert',array('message'=>'Supplier Bill Entry Cancelled Successfully','class'=>'success','title'=>'Supplier Bill Entry'));

                        $return_data=array('status'=>TRUE,'message'=>'Supplier Bill Entry Cancelled Successfully');

                    }

                    else

                    {

                        echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Supplier Bill Entry'));

                        $return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

                    }

                    echo json_encode($return_data);

			 break;



			case 'purchase_add':

			        if($id!='')

			        {

			            $data['po_item']=$this->$model->getPurchaseOrderDet($id);

			            $data['po_item_details']=$this->$model->getPurchaseOrderItemDet($id);

			        }

			        else

			        {

			            $data['po_item']=$this->$model->get_empty_record();

			            $data['po_item_details']=[];

			        }

			        //echo "<pre>";print_r($data);exit;

			        $data['comp_details']   = $this->$set_model->get_company();

					$data['main_content'] = self::VIEW_FOLDER.'pur_entry_form';

        			$this->load->view('layout/template', $data);

			break;



			case 'order_status':

					$data['main_content'] = self::VIEW_FOLDER.'order_status';

        			$this->load->view('layout/template', $data);

			break;



			case 'qc_status':

					$data['main_content'] = self::VIEW_FOLDER.'qc_status';

        			$this->load->view('layout/template', $data);

			break;



			case 'order_delivery':

					$data['main_content'] = self::VIEW_FOLDER.'order_delivery';

        			$this->load->view('layout/template', $data);

			break;


		case "save":

		    // print_r($_POST);exit;

            $addData   = $_POST['order'];

            $order_details   = $_POST['order_details'];



			$fin_year       = $this->$model->get_FinancialYear();

			$order_no       = $this->$model->generatePurNo();

 			$order = array(

 			    'fin_year_code'     => $fin_year['fin_year_code'],

 				'pur_no'            => ($fin_year['fin_year_code'].'-'.$order_no),

 				'order_status'		=> 10,

 				'order_type'		=> 1,

 				'order_pcs'			=> $addData['order_pcs'],

 				'order_approx_wt'	=> $addData['order_wt'],

 				'order_for'			=> $addData['order_for'],

 				'id_karigar'		=> $addData['id_karigar'],

 				'cus_ord_ref'		=> ($addData['id_customer_order']!='' && $addData['id_customer_order']!='null' ? $addData['id_customer_order'] :NULL),

 				'order_date'		=> date("Y-m-d H:i:s"),

				'createdon'         => date("Y-m-d H:i:s"),

				'order_taken_by'    => $this->session->userdata('uid'),

                'rate_type'         => (isset($addData['rate_type']) ? $addData['rate_type'] : 2)

			);

			$this->db->trans_begin();

			$insOrder = $this->$model->insertData($order,'customerorder');

		    //print_r($this->db->last_query());exit;

		    if($insOrder)

		    {





		        if(!empty($order_details))

		        {

		            foreach($order_details['product'] as $key => $val)

		            {



		                $smith_due_date = (!empty($addData['smith_due_dt']) ?date('Y-m-d',strtotime(str_replace("/","-",$addData['smith_due_dt']))):NULL );



		                $orderDetail=array(

		                                  'id_customerorder'=> $insOrder,

		                                  'id_product'      => $order_details['product'][$key],

		                                  'cus_orderdet_ref'=> ($order_details['id_orderdetails'][$key]!='' ? $order_details['id_orderdetails'][$key]:NULL),

		                                  'design_no'       => ($order_details['design'][$key]!='' ? $order_details['design'][$key]:NULL),

		                                  'id_sub_design'   => ($order_details['sub_design'][$key]!='' ? $order_details['sub_design'][$key]:NULL),

		                                  'id_weight_range' => ($order_details['weight_range'][$key]!='' && $order_details['weight_range'][$key]!='null' ? $order_details['weight_range'][$key] :NULL),

		                                  'weight'          => ($order_details['order_wt'][$key]!='' ? $order_details['order_wt'][$key]:NULL),

		                                  'size'            => ($order_details['size'][$key]!='' && $order_details['size'][$key]!='null' ? $order_details['size'][$key] :NULL),

										  'less_wt'         => ($order_details['less_wt'][$key]!='' && $order_details['less_wt'][$key]!='null' ? $order_details['less_wt'][$key] :NULL),

		                                  'net_wt'          => ($order_details['net_wt'][$key]!='' && $order_details['net_wt'][$key]!='null' ? $order_details['net_wt'][$key] :NULL),

		                                  'stn_amt'         => ($order_details['stn_amt'][$key]!='' && $order_details['stn_amt'][$key]!='null' ? $order_details['stn_amt'][$key] :0),

		                                  'mc'              => ($order_details['mc_value'][$key]!='' && $order_details['mc_value'][$key]!='null' ? $order_details['mc_value'][$key] :0),

		                                  'wast_percent'    => ($order_details['wast_percent'][$key]!='' && $order_details['wast_percent'][$key]!='null' ? $order_details['wast_percent'][$key] :0),

		                                  'description'     => ($order_details['description'][$key]!='' ? $order_details['description'][$key] :NULL),

		                                  'totalitems'      => $order_details ['piece'][$key],

		                                  'smith_due_date'  => $smith_due_date,

		                                  'orderstatus'     => 3,

 										  'id_karigar'		=> $addData['id_karigar'],

                                          'rate_per_gram'   => ($this->input->post('rate_value')!='' ? $this->input->post('rate_value') : 0),


		                                  );

		                  $id_order_Details=$this->$model->insertData($orderDetail,'customerorderdetails');

		                //   print_r($this->db->last_query());exit;

		                  if($id_order_Details)

		                  {



		                    if($order_details['id_orderdetails'][$key]!='')

            		        {

            				    $this->$model->updateData(array('orderstatus'=>3,'assigned_by'=>$this->config->item('uid'),
								'assigned_on'=> date("Y-m-d H:i:s")),'id_orderdetails',$order_details['id_orderdetails'][$key],'customerorderdetails'); // For Customer Order Status

            		        }



		                    $img_details=json_decode(rawurldecode($order_details['order_images'][$key]));

		                    //$img = json_decode(($img_details));

		                    $_FILES['order_image']    =  array();

		                    foreach($img_details as $image)

		                    {

		                       $imgFile = $this->base64ToFile($image->src);

    						   $_FILES['order_image'][] = $imgFile;

		                    }



    	                    if(!empty($_FILES)) {

        						$folder =  self::IMG_PATH."order/purchase_order/";

        						if (!is_dir($folder)) {

        							mkdir($folder, 0777, TRUE);

        						}

        						if(isset($_FILES['order_image'])){

        							$precious_imgs = "";

        							// Image Table Update

        							$arrayimg_tag   = array();

        							foreach($_FILES['order_image'] as $file_key => $file_val){



        								if($file_val['tmp_name'])

        								{

        									$img_name =  $id_order_Details."_". mt_rand(100001,999999).".jpg";

        									$path = $folder."/".$img_name;

        									$result = $this->upload_img('image',$path,$file_val['tmp_name']);

        									if($result){

        										$precious_imgs = $img_name;

        										$arrayimg_tag = array(

        										 'id_orderdetails' => $id_order_Details,

        										 'image'           => $img_name,

        										);

        									$img_arr[]   = $arrayimg_tag;

        									  $insImageId = $this->$model->insertData($arrayimg_tag,'customer_order_image');

        									}

        								}

        							}

        						}

        					}



		                      $jobOrder=array(

		                                     'id_order'=>$id_order_Details,

		                                     'orderstatus'=>3,

		                                     'assigndate'=>date("Y-m-d H:i:s"),

		                                     'id_vendor'=>$addData['id_karigar'],

		                                     );

		                      $this->$model->insertData($jobOrder,'joborder');

		                  }



		                  if(isset($order_details['id_orderdetails'][$key]))

		                  {

		                        if($order_details['id_orderdetails'][$key]!='' && $order_details['id_orderdetails'][$key]!="undefined")

                		        {

                				    $this->$model->updateData(array('orderstatus'=>3),'id_orderdetails',$order_details['id_orderdetails'][$key],'customerorderdetails'); // For Customer Order Status

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

						'module'        => 'Order',

						'operation'     => 'Add',

						'record'        =>  $insOrder,

						'remark'        => 'Order added successfully'

						);

					$this->log_model->log_detail('insert','',$log_data);

                    // --- Email to Vendor deferred to MD Approval ---

    				$this->session->set_flashdata('chit_alert',array('message'=>'New Order added successfully','class'=>'success','title'=>'Add Order'));

    				$return_data=array('status'=>TRUE,'msg'=>'Order Created successfully..','id_customerorder'=>$insOrder,'order_for');

    			}

    			else

    			{



    			    echo $this->db->last_query();exit;

    				$this->db->trans_rollback();

    				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add Order'));

    				$return_data=array('status'=>FALSE,'msg'=>'');

    			}

    			echo json_encode($return_data);



		    }

			break;

			case 'po_entry_save':

			    $responsData=array();

			   // echo "<pre>";print_r($_POST);echo "</pre>";exit;

			    $orderData=$_POST['order'];

			    $orderDetails=$_POST['order_item'];



                $ho              = $this->$model->get_headOffice();

                $fin_year       = $this->$model->get_FinancialYear();

                $dCData          = $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

				$bill_date       = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);



				$karigar_details=$this->$model->get_karigar_details($orderData['id_karigar']);



				/*1.IF APPROVAL STOCK -  PA

				2.AGANINST ORDER - IF REGISTERED KARIGAR - P , NON REGISTERED PM*/



				/*if(!empty($orderData['purchase_type'])){

    				if($orderData['purchase_type']==1) // Aganist Order

    				{

    				    if($karigar_details['karigar_type']==0)

    				    {

    				        $gst_bill_type=$karigar_details['karigar_type'];

    				        $last_no       = $this->$model->generatePurRefOrderNo(0,$gst_bill_type);

    				        $po_ref_no='PM-'.$last_no;

    				    }

    				    else

    				    {

    				        $last_no       = $this->$model->generatePurRefOrderNo(0,'');

    				        $po_ref_no='P-'.$last_no;

    				    }

    				}

    				else

    				{

    				    $last_no       = $this->$model->generatePurRefOrderNo($orderData['is_suspense_stock'],'');

    				    if($orderData['is_suspense_stock']==0)

        				{

        				    $po_ref_no='P-'.$last_no;

        				}

        				else

        				{

        				    $po_ref_no='PA-'.$last_no;

        				}

    				}

				}else{

				    $last_no       = $this->$model->generatePurRefOrderNo($orderData['is_suspense_stock'],'');

    				    if($orderData['is_suspense_stock']==0)

        				{

        				    $po_ref_no='P-'.$last_no;

        				}

        				else

        				{

        				    $po_ref_no='PA-'.$last_no;

        				}

				}*/

				$is_suspense_stock = (isset($orderData['is_suspense_stock']) ? $orderData['is_suspense_stock'] :0);

				$last_no       = $this->$model->generatePurRefOrderNo($is_suspense_stock,'');

			    if($is_suspense_stock==0)

				{

				    $po_ref_no='P-'.$last_no;

				}

				else

				{

				    $po_ref_no='PA-'.$last_no;

				}





     			$order = array(

     				'po_ref_no'         => $po_ref_no,

     				'fin_year_code'		=> $fin_year['fin_year_code'],

     				'po_grn_id'         => $orderData['id_grn'],

     				'purchase_order_no' => ($orderData['stock_type']==1 ? $orderData['purchase_order_no'] :NULL), //purchase_type=>1-Aganist Order

     				'is_suspense_stock' => $orderData['is_suspense_stock'],

					'is_po_halmarked'   => ($orderData['is_halmerked']),

     				'isratefixed'       => $_POST['is_rate_fixed'],

     				'gst_bill_type'     => $karigar_details['karigar_type'],

     				'purchase_type'     => !empty($orderData['stock_type']) ? $orderData['stock_type'] : $orderData['po_type'],

     				'po_type'           => $orderData['po_type'],

     				'po_karigar_id'     => $orderData['id_karigar'],

     				'id_category'       => $orderData['id_category'],

     				'id_purity'         =>!empty($orderData['id_purity']) ? $orderData['id_purity'] : 0,

     				'ewaybillno'        => ($orderData['ewaybillno']!='' ? $orderData['ewaybillno']:NULL),

     				'po_irnno'          => ($orderData['po_irnno']!='' ? $orderData['po_irnno']:NULL),

     				'po_supplier_ref_no'=> !empty($orderData['po_supplier_ref_no']) ? $orderData['po_supplier_ref_no']:NULL,

     				'po_ref_date'       => !empty($orderData['po_ref_date']) ? date('Y-m-d', strtotime($orderData['po_ref_date'])):NULL,

     				'despatch_through'  => $orderData['despatch_through'],

     				'tot_purchase_amt'  => $orderData['total_cost'],

     				'tot_purchase_wt'   => $orderData['tot_purchase_wt'],

     				'po_date'		    => $bill_date,

    				'created_on'        => date("Y-m-d H:i:s"),

    				'po_othere_charges' => !empty($orderData['other_charges_amount']) ? $orderData['other_charges_amount'] : 0,

    				'po_discount'       => !empty($orderData['discount']) ? $orderData['discount']:0,

    				'created_by'        => $this->session->userdata('uid'),

    				'tcs_percent'       => $orderData['tcs_percent'],

    				'tcs_tax_value'     => $orderData['tcs_tax_value'],

    				'tds_percent'       => $orderData['tds_percent'],

    				'tds_tax_value'     => $orderData['tds_tax_value'],

    				'form_secret'       => $orderData['pur_entry_form_secret'],

					'po_delivery_date'  => $orderData['due_date'],

    			);

    			//echo"<pre>";print_r($orderDetails);exit;

    			$this->db->trans_begin();

    			$insOrder = $this->$model->insertData($order,'ret_purchase_order');

    			if($insOrder)

    			{

    			     //Update Order Status

    			    if($orderData['stock_type']==1)

    			    {

    			        foreach($orderDetails['id_category'] as $okey => $oval){

        			        $PurorderDetails = $this->$model->get_pur_order_det($orderDetails['po_order_no'][$okey]);

        			        if($PurorderDetails['order_pcs'] > 0)

        			        {

        			            $order_details = array('delivered_qty'=>$orderDetails['tot_pcs'][$okey],'delivered_wt'=> $orderDetails['tot_gwt'][$okey],'id_customerorder'=>$orderDetails['po_order_no'][$okey]);

                                $this->$model->updatePurOrderStatus($order_details,'+');

                                $orderdetails = $this->$model->get_cus_order_details($orderDetails['po_order_no'][$okey], $orderDetails['id_product'][$key], $orderDetails['id_design'][$key], $orderDetails['id_sub_design'][$key]);

                        		$updateorderDetail = array('orderstatus'=> 5,'delivered_qty'=> $orderDetails['tot_pcs'][$key]);

                        		$this->$model->updateData($updateorderDetail,'id_orderdetails',$orderdetails['id_orderdetails'],'customerorderdetails');

                                if($PurorderDetails['order_pcs']<=($orderDetails['tot_pcs'][$okey]+$PurorderDetails['delivered_qty']))

                                {

                                    $this->$model->updateData(array('order_status'=> 5),'id_customerorder',$orderDetails['po_order_no'][$okey],'customerorder');

                                }

        			        }

    			        }



    			    }

    			    //Update Order Status





    			    if(!empty($orderDetails))

    			    {

    			        	foreach($orderDetails['id_category'] as $key => $val){



    			        	    $arrayItems=array(

    			        	                      'po_item_po_id'       =>$insOrder,

    			        	                      'po_item_cat_id'      =>$orderDetails['id_category'][$key],

    			        	                      'po_order_no'         =>( $orderDetails['po_order_no'][$key]!='null' ?  $orderDetails['po_order_no'][$key]: NULL),

    			        	                      'po_purchase_mode'    => $orderDetails['po_purchase_mode'][$key],

    			        	                      'po_item_pro_id'      =>($orderDetails['id_product'][$key] !='' && $orderDetails['id_product'][$key]!='null' ? $orderDetails['id_product'][$key]:NULL),

    			        	                      'po_item_des_id'      =>($orderDetails['id_design'][$key]!='' && $orderDetails['id_design'][$key]!='null' ? $orderDetails['id_design'][$key]:NULL),

    			        	                      'po_item_sub_des_id'  =>($orderDetails['id_sub_design'][$key]!='' && $orderDetails['id_sub_design'][$key]!='null' ? $orderDetails['id_sub_design'][$key]:NULL),

    			        	                      'id_purity'           =>($orderDetails['id_purity'][$key]!='' ? $orderDetails['id_purity'][$key]:NULL),

    			        	                      'gross_wt'            =>$orderDetails['tot_gwt'][$key],

    			        	                      'less_wt'             =>($orderDetails['tot_lwt'][$key]!='' ? $orderDetails['tot_lwt'][$key] :0),

    			        	                      'net_wt'              =>$orderDetails['tot_nwt'][$key],

    			        	                      'calculation_based_on'=>$orderDetails['calculation_based_on'][$key],

    			        	                      'item_wastage'        =>($orderDetails['tot_wastage_perc'][$key]!='' ?$orderDetails['tot_wastage_perc'][$key] :NULL),

    			        	                      'no_of_pcs'           =>($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

    			        	                      'is_suspense_stock'   =>$orderDetails['is_suspense_stock'][$key],

    			        	                      'is_halmarked'        =>($orderDetails['is_halmerked'][$key]!='undefined' ? $orderDetails['is_halmerked'][$key]: ''),

    			        	                      'is_halmark_from'     =>($orderDetails['is_halmerked'][$key]==1 ? $orderDetails['is_halmerked'][$key]:NULL),

    			        	                      'is_rate_fixed'       =>$orderDetails['is_rate_fixed'][$key],

    			        	                      'fix_rate_per_grm'    =>($orderDetails['rate_per_gram'][$key]!='' ? $orderDetails['rate_per_gram'][$key] :0),

    			        	                      'mc_value'            =>($orderDetails['mc_value'][$key]!='' ? $orderDetails['mc_value'][$key] :0),

    			        	                      'purchase_touch'      =>($orderDetails['purchase_touch'][$key]!='' ? $orderDetails['purchase_touch'][$key] :0),

    			        	                      'mc_type'             =>$orderDetails['mc_type'][$key],

    			        	                      'item_cost'           =>($orderDetails['item_cost'][$key]!='' ?$orderDetails['item_cost'][$key] :0),

    			        	                      'item_pure_wt'        =>($orderDetails['tot_purewt'][$key]!='' ? $orderDetails['tot_purewt'][$key]:0),

    			        	                      'uom'                 =>($orderDetails['gwt_uom'][$key]!='' ? $orderDetails['gwt_uom'][$key]:0),

    			        	                      'cal_type'            =>($orderDetails['cal_type'][$key]!='' ? $orderDetails['cal_type'][$key]:0),

    			        	                      'total_tax'           =>($orderDetails['total_tax_rate'][$key]!='' && $orderDetails['total_tax_rate'][$key]!=null ? $orderDetails['total_tax_rate'][$key]:0),

    			        	                      'total_cgst'          =>($orderDetails['cgst_cost'][$key]!='' ? $orderDetails['cgst_cost'][$key]:0),

    			        	                      'total_sgst'          =>($orderDetails['sgst_cost'][$key]!='' ? $orderDetails['sgst_cost'][$key]:0),

    			        	                      'total_igst'          =>($orderDetails['igst_cost'][$key]!='' ? $orderDetails['igst_cost'][$key]:0),

    			        	                      'tax_percentage'      =>($orderDetails['tax_percentage'][$key]!='' ? $orderDetails['tax_percentage'][$key]:0),

    			        	                      'tax_group'           =>($orderDetails['tax_group'][$key]!='' ? $orderDetails['tax_group'][$key]:0),

    			        	                      'tax_type'            =>($orderDetails['tax_type'][$key]!='' ? $orderDetails['tax_type'][$key]:0),

    			        	                      'pure_wt_calc_type'   =>($orderDetails['pure_wt_calc_type'][$key]!='' ? $orderDetails['pure_wt_calc_type'][$key]:0),

    			        	                      'remark'              =>($orderDetails['item_remarks'][$key]!='' && $orderDetails['item_remarks'][$key]!= 'undefined' ? $orderDetails['item_remarks'][$key]:NULL),

												  'issue_met_id'        =>($orderDetails['issue_met_id'][$key]!='' ? $orderDetails['issue_met_id'][$key]:NULL),

												  'quality_id'          =>$this->isEmptySetDefault($orderDetails['quality_id'][$key],NULL),

												  'rate_calc_type'      =>$orderDetails['rate_calc_type'][$key],

					  'purity_type'         =>(isset($orderDetails['purity_type'][$key]) && $orderDetails['purity_type'][$key]!='' ? $orderDetails['purity_type'][$key]:999),

    			        	                     );

    			        	    //echo "<pre>";print_r($arrayItems);exit;

    			        	    $itemStatus=$this->$model->insertData($arrayItems,'ret_purchase_order_items');



    			        	    if($itemStatus)

    			        	    {

    			        	        //Supplier Ledger Log

    			        	        if($orderDetails['tot_purewt'][$key]!=''){

    			        	            $ledger_data = array(

    			        	                                'trans_date'        =>$bill_date,

    			        	                                'id_karigar'        =>$orderData['id_karigar'],

    			        	                                'cat_id'            =>$orderDetails['id_category'][$key],

    			        	                                'trans_type'        =>2,//Debit

    			        	                                'trans_rec_type'    =>1,//Weight

    			        	                                'trans_pcs'         =>($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

    			        	                                'trans_grswt'       =>$orderDetails['tot_gwt'][$key],

    			        	                                'trans_netwt'       =>$orderDetails['tot_nwt'][$key],

    			        	                                'trans_purewt'      =>$orderDetails['tot_purewt'][$key],

    			        	                                'trans_screen_id'   =>1,//Supplier Bill entry

    			        	                                'ref_id'            =>$itemStatus,

    			        	                                'remarks'           =>'From Suuplier Bill Entry',

    			        	                                'created_by'        =>$this->session->userdata('uid'),

    			        	                                'created_on'        =>date("Y-m-d H:i:s"),

    			        	                                );

    			        	            $this->$model->insertData($ledger_data,'ret_supplier_ledger_log');

    			        	        }

    			        	        //Supplier Ledger Log



    			        	        $charge_details = json_decode($orderDetails['other_chrg_details'][$key],true);

    			        	        if(sizeof($charge_details)>0)

    			        	        {

                                    	foreach($charge_details as $charges)

                                    	{

                                        	$charge_data = array(

                                            	'pur_othr_po_id'            => $insOrder,

                                            	'pur_po_item_id'            => $itemStatus,

                                            	'pur_othr_charge_id'        => $charges['charge_id'],

                                            	'calc_type'                 => $charges['calc_type'],

                                            	'pur_othr_charge_value'     => $charges['charge_value'],

                                            	'tax_percentage'            => $charges['tax_percentage'],

                                            	'item_total_tax'            => $charges['charge_tax_value'],

                                            	'total_charge_value'        => $charges['total_charge_value'],

                                        	);

                                        	//print_r($charge_data);exit;

                                        	$chrg_status = $this->$model->insertData($charge_data, 'ret_purchase_other_charges');

                                        	if($chrg_status)

                                        	{

                                        	    $ledger_data = array(

    			        	                                'trans_date'        =>$bill_date,

    			        	                                'id_karigar'        =>$orderData['id_karigar'],

    			        	                                'id_charge'         =>$charges['charge_id'],

    			        	                                'trans_type'        =>2,//Debit

    			        	                                'trans_rec_type'    =>2,//Amount

    			        	                                'trans_amount'      =>number_format($total_charge,2,'.',''),

    			        	                                'trans_screen_id'   =>1,//Supplier Bill entry

    			        	                                'ref_id'            =>$itemStatus,

    			        	                                'remarks'           =>'From Suuplier Bill Entry Charges',

    			        	                                'created_by'        =>$this->session->userdata('uid'),

    			        	                                'created_on'        =>date("Y-m-d H:i:s"),

    			        	                                );

    			        	                        $this->$model->insertData($ledger_data,'ret_supplier_ledger_log');

                                        	}

                                    	}

                    			    }

			        	        	if($orderDetails['stone_details'][$key])

                                	{

                                    	$stone_details=json_decode($orderDetails['stone_details'][$key],true);

                                    	foreach($stone_details as $stone)

                                    	{

                                        	$stone_data=array(

                                        	'po_item_id'    =>$itemStatus,

                                        	'po_stone_id'   =>$stone['stone_id'],

                                        	'po_stone_pcs'  =>$stone['stone_pcs'],

                                        	'po_stone_wt'   =>$stone['stone_wt'],

                                        	'po_stone_uom'  =>$stone['stone_uom_id'],

                                        	'po_stone_rate' =>$stone['stone_rate'],

                                        	'po_stone_amount'=>$stone['stone_price'],

                                        	'is_apply_in_lwt'=> $stone['show_in_lwt'],

                                        	'po_stone_calc_based_on'=> $stone['stone_cal_type'],

                                        	'po_quality_id'=>($stone['stone_quality_id']!='' ?  $stone['stone_quality_id'] : NULL),

											'issue_met_id' => ($stone['issue_met_id']!='' ?  $stone['issue_met_id'] : NULL)

                                        	);

                                        	$stoneInsert = $this->$model->insertData($stone_data,'ret_po_stone_items');

                                        	//echo $this->db->last_query();exit;

                                    	}

                                	}



                                	if($orderDetails['other_metal_details'][$key])

                                	{

                                    	$othermetal_details=json_decode($orderDetails['other_metal_details'][$key],true);

                                    	foreach($othermetal_details as $othermetal)

                                    	{

                                        	$other_metal_data = array(

                                    	    'po_item_id'                    =>$itemStatus,

                                        	'po_item_metal'             =>$othermetal['id_metal'],

                                        	'po_other_item_gross_weight'=>$othermetal['gwt'],

                                        	'po_other_item_purity'   =>$othermetal['id_purity'],

                                        	'po_other_item_rate'  =>$othermetal['rate_per_gram'],

                                        	'po_other_item_pcs' =>$othermetal['pcs'],

                                        	'po_other_item_amount'=>$othermetal['amount'],

                                        	);

                                        	$othermetalInsert = $this->$model->insertData($other_metal_data,'ret_po_other_item');

                                        	//echo $this->db->last_query();exit;

                                    	}

                                	}

    			        	    }

    			        	}

    			    }

    			    if($this->db->trans_status()===TRUE)

        			{

        			    $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'SUPPLIER BILL ENTRY',

                        	'operation'   	=> 'Add',

                        	'record'        =>  $insOrder,

                        	'remark'       	=> 'Supplier Entry Added Successfully..'

                        );

                        $this->log_model->log_detail('insert','',$log_data);



        				$this->db->trans_commit();

        				$this->session->set_flashdata('chit_alert',array('message'=>'Purchase Entry added successfully','class'=>'success','title'=>'Purchase Entry'));

        				$responsData=array('status'=>TRUE,'msg'=>'Purchase Entry added successfully..','Insid'=>$insOrder);

        			}

        			else

        			{

        				$this->db->trans_rollback();

        				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add Order'));

        				$responsData=array('status'=>FALSE,'msg'=>'');

        			}

        			echo json_encode($responsData);

    			}



			break;



			case 'po_entry_update':

            	$updateorderData = $_POST['order'];



            	$updateorderDetails = $_POST['order_item'];



            	$id = $updateorderData['po_id'];



            	//echo"<pre>";print_r($_POST);exit;



            	$pur_order = array(

            		'po_grn_id'         => $updateorderData['id_grn'],

            		'is_suspense_stock' => ($updateorderData['stock_type']==1 ? 0 :$updateorderData['is_suspense_stock']),

            		'isratefixed'       => $_POST['is_rate_fixed'],

            		//'gst_bill_type'     => $karigar_details['karigar_type'],

            		'purchase_type'     => !empty($updateorderData['stock_type']) ? $updateorderData['stock_type'] : $updateorderData['po_type'],

					'purchase_order_no' => ($updateorderData['stock_type']==1 ? $updateorderData['purchase_order_no'] :NULL), //purchase_type=>1-Aganist Order

            		'po_type'           => $updateorderData['po_type'],

            		'po_karigar_id'     => $updateorderData['id_karigar'],

            		'id_category'       => $updateorderData['id_category'],

            		'id_purity'         =>!empty($updateorderData['id_purity']) ? $updateorderData['id_purity'] : 0,

            		'ewaybillno'        => ($updateorderData['ewaybillno']!='' ? $updateorderData['ewaybillno']:NULL),

            		'po_irnno'          => ($updateorderData['po_irnno']!='' ? $updateorderData['po_irnno']:NULL),

            		'po_supplier_ref_no'=> !empty($updateorderData['po_supplier_ref_no']) ? $updateorderData['po_supplier_ref_no']:NULL,

            		'po_ref_date'       => !empty($updateorderData['po_ref_date']) ? date('Y-m-d', strtotime($updateorderData['po_ref_date'])):NULL,

            		'despatch_through'  => $updateorderData['despatch_through'],

            		'tot_purchase_amt'  => $updateorderData['total_cost'],

            		'tot_purchase_wt'   => $updateorderData['tot_purchase_wt'],

            		// 'po_date'		    => $bill_date,

            		'updated_on'        => date("Y-m-d H:i:s"),

            		'po_othere_charges' => !empty($updateorderData['other_charges_amount']) ? $updateorderData['other_charges_amount'] : 0,

            		'po_discount'       => !empty($updateorderData['discount']) ? $updateorderData['discount']:0,

            		'updated_by'        => $this->session->userdata('uid'),

            		'tcs_percent'       => $updateorderData['tcs_percent'],

            		'tcs_tax_value'     => $updateorderData['tcs_tax_value'],

            		'tds_percent'       => $updateorderData['tds_percent'],

            		'tds_tax_value'     => $updateorderData['tds_tax_value']

            	);



            	$this->db->trans_begin();



            	$update_status = $this->$model->updateData($pur_order,'po_id',$id, 'ret_purchase_order');





            	if($update_status)

            	{



            		foreach($updateorderDetails['id_category'] as $key => $val)
            		{
            			$arrayItems=array(
            				'po_item_cat_id' =>$updateorderDetails['id_category'][$key],
            				//'po_order_no'         => $updateorderDetails['po_order_no'][$key],
            				'po_purchase_mode'    => $updateorderDetails['po_purchase_mode'][$key],
            				'po_item_pro_id'      =>($updateorderDetails['id_product'][$key] !='' && $updateorderDetails['id_product'][$key]!='null' ? $updateorderDetails['id_product'][$key]:NULL),
            				'po_item_des_id'      =>($updateorderDetails['id_design'][$key]!='' && $updateorderDetails['id_design'][$key]!='null' ? $updateorderDetails['id_design'][$key]:NULL),
            				'po_item_sub_des_id'  =>($updateorderDetails['id_sub_design'][$key]!='' && $updateorderDetails['id_sub_design'][$key]!='null' ? $updateorderDetails['id_sub_design'][$key]:NULL),
            				'id_purity'           =>($updateorderDetails['id_purity'][$key]!='' ? $updateorderDetails['id_purity'][$key]:NULL),
            				'gross_wt'            =>$updateorderDetails['tot_gwt'][$key],
            				'less_wt'             =>($updateorderDetails['tot_lwt'][$key]!='' ? $updateorderDetails['tot_lwt'][$key] :0),
            				'net_wt'              =>$updateorderDetails['tot_nwt'][$key],
            				'item_wastage'        =>($updateorderDetails['tot_wastage_perc'][$key]!='' ?$updateorderDetails['tot_wastage_perc'][$key] :NULL),
            				'no_of_pcs'           =>($updateorderDetails['tot_pcs'][$key]!='' ? $updateorderDetails['tot_pcs'][$key] :0),
            				'is_suspense_stock'   =>$updateorderDetails['is_suspense_stock'][$key],
            				'is_halmarked'        =>isset($updateorderDetails['is_halmerked'][$key]) && $updateorderDetails['is_halmerked'][$key] != 'undefined' ? $updateorderDetails['is_halmerked'][$key] :null,
            				'is_halmark_from'     =>($updateorderDetails['is_halmerked'][$key]==1 ? $updateorderDetails['is_halmerked'][$key]:NULL),
            				'is_rate_fixed'       =>$updateorderDetails['is_rate_fixed'][$key],
            				'fix_rate_per_grm'    =>($updateorderDetails['rate_per_gram'][$key]!='' ? $updateorderDetails['rate_per_gram'][$key] :NULL),
            				'mc_value'            =>($updateorderDetails['mc_value'][$key]!='' ? $updateorderDetails['mc_value'][$key] :NULL),
            				'purchase_touch'      =>($updateorderDetails['purchase_touch'][$key]!='' ? $updateorderDetails['purchase_touch'][$key] :NULL),
            				'mc_type'             =>$updateorderDetails['mc_type'][$key],
            				'item_cost'           =>($updateorderDetails['item_cost'][$key]!='' ?$updateorderDetails['item_cost'][$key] :0),
            				'item_pure_wt'        =>($updateorderDetails['tot_purewt'][$key]!='' ? $updateorderDetails['tot_purewt'][$key]:0),
            				'uom'                 => ($updateorderDetails['gwt_uom'][$key]!='' ? $updateorderDetails['gwt_uom'][$key]:0),
            				'cal_type'            => ($updateorderDetails['cal_type'][$key]!='' ? $updateorderDetails['cal_type'][$key]:0),
            				'remark'              => ($updateorderDetails['remark'][$key]!='' ? $updateorderDetails['remark'][$key]:NULL),
							'quality_id'          =>($updateorderDetails['quality_id'][$key]!='' ? $updateorderDetails['quality_id'][$key]:NULL),
							'rate_calc_type'      => ($updateorderDetails['rate_calc_type'][$key]!='' ? $updateorderDetails['rate_calc_type'][$key]:1),
							'total_tax'           =>($updateorderDetails['total_tax_rate'][$key]!='' && $updateorderDetails['total_tax_rate'][$key]!=null ? $updateorderDetails['total_tax_rate'][$key]:0),
							'total_cgst'          =>($updateorderDetails['cgst_cost'][$key]!='' ? $updateorderDetails['cgst_cost'][$key]:0),
							'total_sgst'          =>($updateorderDetails['sgst_cost'][$key]!='' ? $updateorderDetails['sgst_cost'][$key]:0),
							'total_igst'          =>($updateorderDetails['igst_cost'][$key]!='' ? $updateorderDetails['igst_cost'][$key]:0),
							'tax_percentage'      =>($updateorderDetails['tax_percentage'][$key]!='' ? $updateorderDetails['tax_percentage'][$key]:0),
							'tax_group'           =>($updateorderDetails['tax_group'][$key]!='' ? $updateorderDetails['tax_group'][$key]:0),
							'tax_type'            =>($updateorderDetails['tax_type'][$key]!='' ? $updateorderDetails['tax_type'][$key]:0),
							'purity_type'         =>(isset($updateorderDetails['purity_type'][$key]) && $updateorderDetails['purity_type'][$key]!='' ? $updateorderDetails['purity_type'][$key]:999),
            			);



            			// echo"<pre>";print_r($arrayItems);exit;





            			if(!empty($updateorderDetails['po_item_id'][$key]))

            			{



            				$this->db->trans_begin();



            				$this->$model->updateData($arrayItems,'po_item_id',$updateorderDetails['po_item_id'][$key],'ret_purchase_order_items');

            				//print_r($this->db->last_query());exit;



            				if($updateorderDetails['stone_details'][$key])

            				{

            					$stone_details=json_decode($updateorderDetails['stone_details'][$key],true);



            					$po_stone_delete_id = $this->$model->deleteData("po_item_id",$updateorderDetails['po_item_id'][$key],'ret_po_stone_items');



            					foreach($stone_details as $stone)

            					{

            						$stone_data=array(

            						'po_item_id'    =>$updateorderDetails['po_item_id'][$key],

            						'po_stone_id'   =>$stone['stone_id'],

            						'po_stone_pcs'  =>$stone['stone_pcs'],

            						'po_stone_wt'   =>$stone['stone_wt'],

            						'po_stone_uom'  =>$stone['stone_uom_id'],

            						'po_stone_rate' =>$stone['stone_rate'],

            						'po_stone_amount'=>$stone['stone_price'],

            						'is_apply_in_lwt'=> $stone['show_in_lwt'],

            						'po_quality_id'=> $stone['stone_quality_id']

            						);

            						$stoneInsert = $this->$model->insertData($stone_data,'ret_po_stone_items');

            						//echo $this->db->last_query();exit;

            					}

            				}



            				if($orderDetails['other_metal_details'][$key])

            				{

            					$othermetal_details=json_decode($orderDetails['other_metal_details'][$key],true);



            					$po_othermetal_id = $this->$model->deleteData("po_item_id",$updateorderDetails['po_item_id'][$key],'ret_po_other_item');



            					foreach($othermetal_details as $othermetal)

            					{

            						$other_metal_data = array(

            						'po_item_id'                    =>$itemStatus,

            						'po_item_metal'             =>$othermetal['id_metal'],

            						'po_other_item_gross_weight'=>$othermetal['gwt'],

            						'po_other_item_purity'   =>$othermetal['id_purity'],

            						'po_other_item_rate'  =>$othermetal['rate_per_gram'],

            						'po_other_item_pcs' =>$othermetal['pcs'],

            						'po_other_item_amount'=>$othermetal['amount'],

            						);

            						$othermetalInsert = $this->$model->insertData($other_metal_data,'ret_po_other_item');

            						//echo $this->db->last_query();exit;

            					}

            				}





            			}

            			else

            			{

            				if($id > 0)

            				{

            					$arrayItems['po_item_po_id'] = $id;



            					$this->db->trans_begin();



            					$insPurorderDet = $this->$model->insertData($arrayItems,'ret_purchase_order_items');

            				}



            				$stone_details=json_decode($updateorderDetails['stone_details'][$key],true);



            				if(sizeof($stone_details)>0)

            				{

            					foreach($stone_details as $stone)

            					{

            						$stone_data=array(

            						'po_item_id'    =>$insPurorderDet,

            						'po_stone_id'   =>$stone['stone_id'],

            						'po_stone_pcs'  =>$stone['stone_pcs'],

            						'po_stone_wt'   =>$stone['stone_wt'],

            						'po_stone_uom'  =>$stone['stone_uom_id'],

            						'po_stone_rate' =>$stone['stone_rate'],

            						'po_stone_amount'=>$stone['stone_price'],

            						'is_apply_in_lwt'=> $stone['show_in_lwt']

            						);

            						$stoneInsert = $this->$model->insertData($stone_data,'ret_po_stone_items');

            						//echo $this->db->last_query();exit;

            					}



            				}



            				$othermetal_details=json_decode($orderDetails['other_metal_details'][$key],true);



            				if(sizeof($othermetal_details)>0)

            				{

            					foreach($othermetal_details as $othermetal)

            					{

            						$other_metal_data = array(

            						'po_item_id'                    =>$insPurorderDet,

            						'po_item_metal'             =>$othermetal['id_metal'],

            						'po_other_item_gross_weight'=>$othermetal['gwt'],

            						'po_other_item_purity'   =>$othermetal['id_purity'],

            						'po_other_item_rate'  =>$othermetal['rate_per_gram'],

            						'po_other_item_pcs' =>$othermetal['pcs'],

            						'po_other_item_amount'=>$othermetal['amount'],

            						);

            						$othermetalInsert = $this->$model->insertData($other_metal_data,'ret_po_other_item');

            						//echo $this->db->last_query();exit;

            					}



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

						'module'        => 'Supplier Entry',

						'operation'     => 'Update',

						'record'        =>  $update_status,

						'remark'        => 'Supplier Entry Updated successfully'

						);

			        $this->log_model->log_detail('insert','',$log_data);


            		$this->session->set_flashdata('chit_alert',array('message'=>'Purchase Entry updated successfully','class'=>'success','title'=>'Purchase Entry'));

            		$responsData=array('status'=>TRUE,'msg'=>'Purchase Entry Updated successfully..','Insid'=>$id);



            	}

            	else



            	{



            		//echo $this->db->last_query();exit;



            		$this->db->trans_rollback();



            		$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Purchase Entry'));



            		$responsData=array('status'=>FALSE,'msg'=>'');



            	}



            	echo json_encode($responsData);





            break;



			case 'purchase_order':

			        $list=$this->$model->get_purchase_order_Details($_POST);

				  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/purchase/pur_order');

			        $data = array(

			        					'list'  => $list,

										'access'=> $access

			        				);

					echo json_encode($data);

			break;



			case 'purchase_entry':

			        $from_date	= $this->input->post('from_date');

			        $to_date	= $this->input->post('to_date');

					$profile=$this->$model->get_profile_settings($this->session->userdata('profile'));

			        $list=$this->$model->get_purchase_entry_details($from_date,$to_date);

				  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/purchase/list');

			        $data = array(

			        					'list'  => $list,

										'profile' =>$profile,

										'access'=> $access

			        				);

					echo json_encode($data);

			break;



			case 'active_grns':

		            $list = $this->$model->getActiveGRNsDetails($_POST);

					echo json_encode($list);

			break;



			case 'job_receipt':

				$data['po_item']=$this->$model->getPurchaseOrderDet($id);

				$data['po_item_details']=$this->$model->get_purchase_order_item_det($id);

				$data['comp_details']=$this->admin_settings_model->getCompanyDetails("");

				// echo "<pre>";print_r($data);exit;

				$html = $this->load->view(self::VIEW_FOLDER.'job_receipt', $data,true);

				echo $html;exit;



			break;



			case 'grncatdetailsbygrnid':

			    $catitems = $this->$model->getGRNsCatDetailsbyGRNId($_POST);

				echo json_encode($catitems);

				break;



			case 'ajax':

					$list=$this->$model->getStockAgeDetails($_POST);

				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_age/list');

			        $data = array(

			        					'list'  => $list,

										'access'=> $access

			        				);

					echo json_encode($data);

				break;

		}



    }



    function approvalstock($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'approvalstock/purchase_entry_list';

					$data['access'] = $this->$SETT_MOD->get_access('admin_ret_purchase/approvalstock/list');

        			$this->load->view('layout/template', $data);

			break;



			case 'pur_order':

					$data['main_content'] = self::VIEW_FOLDER.'pur_order_list';

        			$this->load->view('layout/template', $data);

			break;



			case 'add':

					$data['main_content'] = self::VIEW_FOLDER.'order_form';

        			$this->load->view('layout/template', $data);

			break;



			case 'ajax_purchase_list':

			        $list=$this->$model->get_approval_purchase_entry_details();

				  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/approvalstock/list');

			        $data = array(

			        					'list'  => $list,

										'access'=> $access

			        				);

					echo json_encode($data);

			break;

		}



    }





   function send_karigar_sms()

    {



        $responseData=array();

        $model              =	self::RET_PUR_ORDER_MODEL;

        $set_model          =   "admin_settings_model";

        $id_customer_order  =   $_POST['id_customer_order'];

        $mobile             =   $_POST['mobile'];



        $service = $this->$set_model->get_service_by_code('KAR_ALOC');



        $orders=$this->$model->get_karigar_order_details($id_customer_order);







       /* $data['order']['pur_no']=$orders[0]['pur_no'];

        $data['order']['emp_name']=$orders[0]['emp_name'];

        $data['order']['order_date']=$orders[0]['order_date'];

        $data['order']['smith_due_date']=$orders[0]['smith_due_date'];

        $data['order_details']=$this->$model->get_karigar_order_products($id_customer_order);

        $data['orders']=$orders;

        //echo "<pre>";print_r($orders);exit;



        $data['karigar']=$this->$model->get_karigar_details($orders[0]['id_vendor']);

        $data['description']=$this->$model->get_order_description();*/





        $data['order_items']=$orders;

        $data['order']['pur_no']=$orders[0]['pur_no'];

        $data['order']['emp_name']=$orders[0]['emp_name'];

        $data['order']['order_date']=$orders[0]['order_date'];

        $data['order']['order_for']=$orders[0]['order_for'];

        $data['order']['smith_due_date']=$orders[0]['smith_due_date'];



        $data['comp_details'] = $this->$set_model->get_company(1);

        $data['order_details']=$this->$model->get_karigar_order_products($id_customer_order);

        $data['karigar']=$this->$model->get_karigar_details($orders[0]['id_karigar']);

        $data['description']=$this->$model->get_order_description();





		$data['comp_details'] = $this->$set_model->get_company(1);

        $this->load->helper(array('dompdf', 'file'));

        $dompdf = new DOMPDF();

        $html = $this->load->view('ret_purchase/vendor_ack', $data,true);



        $dompdf->load_html($html);

        $dompdf->set_paper('A4', "portriat" );

        $dompdf->render();

        $file = $dompdf->output();





		$folderPath='assets/vendor_ack/'.$data['order']['pur_no'];

		$file_name =$folderPath.'/'.time().'_'.$data['order']['pur_no'].'.pdf';



		$attachementUrl=$file_name;



		if (!is_dir($folderPath)) {

    	mkdir($folderPath, 0777, TRUE);

    	}

		file_put_contents($file_name, $file);





	    //print_r($service);exit;



	    /*if($service['serv_whatsapp'] == 1)

		{*/

			if($mobile!='')

			{

			    $sms_data=$this->admin_usersms_model->Get_service_code_sms('KAR_ALOC',$id_customer_order,$attachementUrl);

			    $message='New Order Received From '.$data['comp_details']['company_name'].'.For More Details Please Find The Below Attachement.';

			    $status=$this->sms_model->sendSMS_MSG91($mobile,$message,'',$dlt_te_id,'');

			    if($attachementUrl!='')

			    {

			        $this->sms_model->sendSMS_MSG91($mobile,$message,'',$dlt_te_id,$this->config->item('base_url').'/'.$attachementUrl,$data['order']['pur_no'].'.pdf');



			    }

			    //$whatsapp=$this->admin_usersms_model->send_whatsApp_message($mobile,"New Order From -".$data['comp_details']['company_name'],$this->config->item('base_url').'/'.$attachementUrl,$data['order']['pur_no'].'.pdf');

			    if($status)

			    {

			        $responseData=array('status'=>true,'message'=>'Order Details Sent Successfully');

			    }

			}

	//	}



		if($service['serv_email'] == 1)

		{

			if($orders[0]['email']!='')

			{

			    $sms_data=$this->admin_usersms_model->Get_service_code_sms('KAR_ALOC',$id_customer_order,$attachement_url);



			    $send_mail_from =$this->$model->get_ret_settings('pur_order_email');



			    if($send_mail_from!='')

			    {

			        $send_mail_to =$orders[0]['email'];



                    $email_subject="New Order -".$data['comp_details']['company_name'];



                    $this->company = $this->admin_settings_model->get_company(1);



                    $email_model = "email_model";





                    $message = $this->load->view('ret_purchase/vendor_ack_email',$orders, true);



                    $sendEmail = $this->$model->send_email($send_mail_from,$send_mail_to,$email_subject,$message,'',$send_mail_from,$attachementUrl);



                    if($sendEmail)

    			    {

    			        $responseData=array('status'=>true,'message'=>'Order Details Sent Successfully');

    			    }

			    }







			}

		}



		echo json_encode($responseData);

    }



    function get_karigar_acknowladgement($id_customer_order)

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $set_model = "admin_settings_model";



        $orders=$this->$model->get_karigar_order_details($id_customer_order);

       // echo "<pre>";print_r($orders);exit;

        $data['order_items']=$orders;

        $data['order']['pur_no']=$orders[0]['pur_no'];

        $data['order']['emp_name']=$orders[0]['emp_name'];

        $data['order']['order_date']=$orders[0]['order_date'];

        $data['order']['order_for']=$orders[0]['order_for'];

        $data['order']['smith_due_date']=$orders[0]['smith_due_date'];



        $data['comp_details'] = $this->$set_model->get_company(1);

        $data['order_details']=$this->$model->get_karigar_order_products($id_customer_order);

        $data['karigar']=$this->$model->get_karigar_details($orders[0]['id_karigar']);

        $data['description']=$this->$model->get_order_description();



        //echo "<pre>";print_r($data);exit;



    	$this->load->helper(array('dompdf', 'file'));

    	$dompdf = new DOMPDF();

        $html = $this->load->view('ret_purchase/vendor_ack', $data,true);

        $dompdf->load_html($html);

        $dompdf->set_paper('A4', "portriat" );



		$dompdf->render();

		 $file = $dompdf->output();

		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("VendorAck.pdf",array('Attachment'=>0));





		$file_name =$order[0]['order_no'].'.pdf';

		if (!is_dir('vendor_ack')) {

    	mkdir('vendor_ack', 0777, TRUE);

    	}

		file_put_contents('vendor_ack/'.$file_name, $file);

	    $attachement_url=$this->shortenurl($this->config->item('base_url').'/vendor_ack/'.$file_name);

    }

    //Purchase Order





    function get_KarigarOrders()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_KarigarOrders($_POST);

        echo json_encode($data);

    }





    function get_ActiveWeightRange()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_ActiveWeightRange($_POST);

        echo json_encode($data);

    }





    function get_pur_order_Details()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_purchase_order_status($_POST);

        echo json_encode($data);

    }





    function get_purchase_issue_entry_items()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_qc_receipt_items($_POST);

        echo json_encode($data);

    }





    function update_qc_status()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $itemDetails=$_POST['order_items'];

        $po_id=$_POST['order']['po_ref_no'];

        //echo "<pre>";print_r($_POST);exit;

        $responseData=array();

        $this->db->trans_begin();

        foreach($itemDetails['id_qc_issue_details'] as $key => $val)

        {

                $updData=array(

                                'status'        => 1, // QC Completed

                                'failed_pcs'    => $itemDetails['failed_pcs'][$key],

                                'failed_gwt'    => $itemDetails['failed_gwt'][$key],

                                 'failed_lwt'    => ($itemDetails['failed_lwt'][$key]!='' ? $itemDetails['failed_lwt'][$key]: 0.000),

                                'failed_nwt'    => $itemDetails['failed_nwt'][$key],

                                'passed_pcs'    => $itemDetails['qc_acc_pcs'][$key],

                                'passed_gwt'    => $itemDetails['qc_acc_gwt'][$key],

                                'passed_lwt'    => $itemDetails['qc_acc_lwt'][$key],

                                'passed_nwt'    => $itemDetails['qc_acc_nwt'][$key],

                              );

                $status = $this->$model->updateData($updData,'id_qc_issue_details',$itemDetails['id_qc_issue_details'][$key],'ret_po_qc_issue_details');



               if($status)

    	       {

    	            //echo "<pre>"; print_r($itemDetails['stone_details'][$key]);exit;

    	          	if(!empty($itemDetails['stone_details'][$key]))

    				{

    					$stone_details = json_decode($itemDetails['stone_details'][$key],true);

    					//echo "<pre>"; print_r($stone_details);exit;

    					foreach($stone_details as $val)

    					{

    						$stone_data = array(

    							'qc_rejected_pcs'     => $val['po_stone_rejected_pcs'],

    							'qc_rejected_wt'      => ($val['po_stone_rejected_wt']!='' ? $val['po_stone_rejected_wt']:0),

    							'qc_passed_pcs'       => $val['po_stone_accepted_pcs'],

    							'qc_passed_wt'        => $val['po_stone_accepted_wt'],

    						);

    						$this->$model->updateData($stone_data,'ret_qc_issue_stn_id',$val['ret_qc_issue_stn_id'],'ret_po_qc_issue_stone_details');

    					}

    				}

    	       }

        }

        if($this->db->trans_status()===TRUE)

        {

            $this->db->trans_commit();

            //Generate LOT

            /*if($po_id!='')

            {

                $LotStatus=TRUE;

                $pur_ord_det=$this->$model->get_purchase_order_qc_details($po_id); // CHECKING QC STATUS AD H.M STATUS FOR LOT GENERATE

                foreach($pur_ord_det as $po_items)

                {

                    if($po_items['status']==0)

                    {

                        $LotStatus=FALSE;

                    }

                }



                if($LotStatus)

                {

                    $this->generate_lot($po_id);

                }

            }*/

            //Generate LOT

            $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'QUALITY CONTROL',

                        	'operation'   	=> 'Update',

                        	'record'        =>  $po_id,

                        	'remark'       	=> 'QC Receipt Updated Successfully..'

                        );

            $this->log_model->log_detail('insert','',$log_data);





            $responseData=array('status'=>true,'message'=>'QC Updated successfully.');

            $this->session->set_flashdata('chit_alert',array('message'=>'QC Updated successfully.','class'=>'success','title'=>'QC Entry'));

        }

        else

        {

            $this->db->trans_rollback();

            $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

            $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'QC Entry'));

        }

        echo json_encode($responseData);

    }





    function generate_lot($po_id)

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $itemDetails = $this->$model->get_purchase_by_category($po_id);

        $ho              =  $this->$model->get_headOffice();

		$dCData          =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

		$bill_date       =  ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

        foreach($itemDetails as $addData)

        {

            $data = array(

            'lot_date'				=> date("Y-m-d H:i:s"),

            'created_branch'		=> (isset($ho['id_branch']) ? $ho['id_branch']:NULL ),

            'lot_received_at'       => $ho['id_branch'],

            'stock_type'            => $addData['stock_type'],

            'lot_from'              => 2,

            'po_id'                 => $po_id,

            'gold_smith'			=> (isset($addData['po_karigar_id']) ? $addData['po_karigar_id'] :NULL ),

            'id_category'			=> (isset($addData['id_category']) ? $addData['id_category'] :NULL ),

            'id_purity'				=> (isset($addData['id_purity']) ? $addData['id_purity'] :NULL ),

            'created_on'	  		=> date("Y-m-d H:i:s"),

            'created_by'      		=> $this->session->userdata('uid')

            );

            $this->db->trans_begin();

    	 	$insId = $this->$model->insertData($data,'ret_lot_inwards');

    	 	if($insId)

    	 	{

    	 	    $po_order_details=$this->$model->get_purchase_orders_by_product($po_id,$addData['id_category'],$addData['id_purity'],$addData['stock_type']);

    	 	    //print_r($po_order_details);exit;

    	 	    foreach($po_order_details as $items)

    	 	    {

    	 	        $item_details = array(

                        'lot_no'	    =>$insId,

                        'lot_product'   =>($items['id_product']!='' ? $items['id_product']:NULL),

                        'no_of_piece'   =>($items['total_pcs']!='' ? $items['total_pcs']:0),

                        'gross_wt'      =>($items['total_gwt']!='' ? $items['total_gwt']:0),

                        'less_wt'       =>($items['total_lwt']!='' ? $items['total_lwt']:0),

                        'net_wt'        =>($items['total_nwt']!='' ? $items['total_nwt']:0),

                    );

                    $detail_insId = $this->$model->insertData($item_details,'ret_lot_inwards_detail');

                    //echo "<pre>"; print_r($this->db->last_query());exit;

                    if($detail_insId)

                    {

                        $this->$model->updateMultipleWhereData(array('is_lot_created'=>1,'lot_no'=>$insId),array('po_item_po_id'=>$po_id,'po_item_pro_id'=>$items['id_product'],'id_purity'=>$items['id_purity']),'ret_purchase_order_items');

                        //echo "<pre>"; print_r($this->db->last_query());exit;

                        if($addData['stock_type']==2)

                        {

                            $purchase_order_details = $this->$model->get_purchase_order_items($po_id,$items['id_product']);

                            foreach($purchase_order_details as $val)

                            {

                                $existData=array('id_product'=>$val['po_item_pro_id'],'design'=>$val['po_item_des_id'],'id_sub_design'=>$val['po_item_sub_des_id'],'id_branch'=>$ho['id_branch']);



						        $isExist = $this->$model->checkNonTagItemExist($existData);



						        if($isExist['status'] == TRUE)

						        {

						            $nt_data = array(

                                    'id_nontag_item'=>$isExist['id_nontag_item'],

                                    'gross_wt'		=> $val['total_gwt'],

                                    'net_wt'		=> $val['total_nwt'],

                                    'no_of_piece'   => $val['total_pcs'],

                                    'updated_by'	=> $this->session->userdata('uid'),

                                    'updated_on'	=> date('Y-m-d H:i:s'),

                                    );

                                    $this->$model->updateNTData($nt_data,'+');



                                    $non_tag_data=array(

                                    'product'	    => $val['po_item_pro_id'],

                                    'design'	    => $val['po_item_des_id'],

                                    'id_sub_design'	=> $val['po_item_sub_des_id'],

                                    'no_of_piece'   => $val['total_pcs'],

                                    'gross_wt'		=> $val['total_gwt'],

                                    'net_wt'		=> $val['total_nwt'],

                                    'to_branch'	    => $ho['id_branch'],

                                    'from_branch'	=> NULL,

                                    'ref_no'	    => $insId,

                                    'status'	    => 0,

                                    'date'          => $bill_date,

                                    'created_on'    => date("Y-m-d H:i:s"),

                                    'created_by'    =>  $this->session->userdata('uid')

                                    );

                                    $this->$model->insertData($non_tag_data,'ret_nontag_item_log');

						        }else

						        {

                                        $nt_data=array(

                                        'branch'	    => $ho['id_branch'],

                                        'product'	    => $val['po_item_pro_id'],

                                        'design'	    => $val['po_item_des_id'],

                                        'id_sub_design'	=> $val['po_item_sub_des_id'],

                                        'no_of_piece'   => $val['total_pcs'],

                                        'gross_wt'		=> $val['total_gwt'],

                                        'net_wt'		=> $val['total_nwt'],

                                        'created_on'    => date("Y-m-d H:i:s"),

                                        'created_by'    => $this->session->userdata('uid')

                                        );

                                        $this->$model->insertData($nt_data,'ret_nontag_item');



                                        $non_tag_data=array(

                                        'product'	    => $val['po_item_pro_id'],

                                        'design'	    => $val['po_item_des_id'],

                                        'id_sub_design'	=> $val['po_item_sub_des_id'],

                                        'no_of_piece'   => $val['total_pcs'],

                                        'gross_wt'		=> $val['total_gwt'],

                                        'net_wt'		=> $val['total_nwt'],

                                        'from_branch'	=> NULL,

                                        'to_branch'	    => $ho['id_branch'],

                                        'ref_no'	    => $insId,

                                        'status'	    => 0,

                                        'date'          => $bill_date,

                                        'created_on'    => date("Y-m-d H:i:s"),

                                        'created_by'    =>  $this->session->userdata('uid')

                                        );

                                        $this->$model->insertData($non_tag_data,'ret_nontag_item_log');

						        }

                            }



                        }



                    }



    	 	    }



    	 	    if($detail_insId)

    	 	    {

    	 	        $order_items        =$this->$model->get_po_purchase_items($po_id,$addData['id_category']);

    	 	        $total_payable_wt   =0;

    	 	        $total_mc_value     =0;

    	 	        $total_weight_amt   =0;

    	 	        $total_making_charge   =0;

    	 	        foreach($order_items as $order)

    	 	        {

    	 	            $total_weight       =($order['item_wastage']>0 ? ((($order['total_nwt']*$order['item_wastage'])/100)+$order['total_nwt']) : $order['total_nwt'] );



    	 	            //$pure_wt          =(($total_weight*22)/24); // For calculating PURE WEIGHT (22CT to 24 CT)



    	 	            $pure_wt            = $order['item_pure_wt'];



    	 	            $total_mc_value+=($order['mc_type']==1 ? ($order['total_gwt']*$order['mc_value']):($order['total_pcs']*$order['mc_value']));



    	 	            $item_mc_value  =($order['mc_type']==1 ? ($order['total_gwt']*$order['mc_value']):($order['total_pcs']*$order['mc_value']));





    	 	            $total_payable_wt+=$pure_wt;







    	 	            /*//Update Rate Fixing



    	 	            if($order['fix_rate_per_grm']>0 && $order['is_rate_fixed']==1)

    	 	            {

    	 	                $total_amount =($order['item_pure_wt']*$order['fix_rate_per_grm']);



    	 	                $tax_amt      = (($total_amount*$order['tax_percentage'])/100);



    	 	                $total_weight_amt+=$tax_amt+$total_amount;



    	 	                $rateFixData=array(

                               'rate_fix_po_item_id'    =>$order['po_item_id'],

                               'rate_fix_type'          =>1,

                               'rate_fix_wt'            =>$order['item_pure_wt'],

                               'tax_group_id'           =>$order['tgrp_id'],

                               'rate_fix_rate'          =>$order['fix_rate_per_grm'],

                               'rate_fix_created_from'  =>2,

                               'total_tax_amount'       =>$tax_amt,

                               'total_amount'           =>($tax_amt+$total_amount),

                               'rate_fix_create_by'     =>$this->session->userdata('uid'),

                               'rate_fix_created_on'    =>date("Y-m-d H:i:s"),

                               );

                               $insertId=$this->$model->insertData($rateFixData,'ret_po_rate_fix');

                              if($insertId)

        			          {

        			               $log_data = array(

                                    	'id_log'        =>  $this->session->userdata('id_log'),

                                    	'event_date'	=>  date("Y-m-d H:i:s"),

                                    	'module'      	=> 'RATE FIXING',

                                    	'operation'   	=> 'insert',

                                    	'record'        =>  $order['po_item_id'],

                                    	'remark'       	=> 'Rate Fixed Successfully..'

                                    );

                                    $this->log_model->log_detail('insert','',$log_data);

        			          }

    	 	            }



    	 	            if($order['mc_value']>0) // Update tax and insert into rate fix table for mc and other charges

    	 	            {

    	 	                $tax_amount=(($item_mc_value*$order['tax_percentage'])/100);



    	 	                $total_making_charge+=$tax_amount+$item_mc_value;



    	 	                $mc_payable_data=array(

                               'rate_fix_po_item_id'    =>$order['po_item_id'],

                               'rate_fix_type'          =>2,

                               'rate_fix_amt'           =>$item_mc_value,

                               'tax_group_id'           =>$order['tgrp_id'],

                               'rate_fix_rate'          =>0,

                               'rate_fix_created_from'  =>2,

                               'total_tax_amount'       =>$tax_amount,

                               'total_amount'           =>($tax_amount+$item_mc_value),

                               'rate_fix_create_by'     =>$this->session->userdata('uid'),

                               'rate_fix_created_on'    =>date("Y-m-d H:i:s"),

                               );

                               $insertId=$this->$model->insertData($mc_payable_data,'ret_po_rate_fix');

                               //print_r($this->db->last_query());exit;

    	 	            }

    	 	            */

    	 	            //Update Rate Fixing



    	 	        }

    	 	        //Update Payable weight and amount

    	 	        $payInsData=array(

    	 	                        'total_payable_wt'=>number_format($total_payable_wt,3,'.',''),

    	 	                        'total_payable_amt'=>number_format($total_weight_amt+$total_making_charge,2,'.',''),

    	 	                        'po_id'=>$po_id,

    	 	                        );



    	 	        $paymentData=$this->$model->update_po_paymentData($payInsData,'+');

    	 	        //Update Payable weight and amount



    	 	    }

                if($this->db->trans_status()===TRUE)

                {



                    $log_data = array(

                    	'id_log'        => $this->session->userdata('id_log'),

                    	'event_date'	=> date("Y-m-d H:i:s"),

                    	'module'      	=> 'Lot',

                    	'operation'   	=> 'Add',

                    	'record'        => $insId,

                    	'remark'       	=> 'Lot added successfully From QC Receipt'

                    );

                    $this->log_model->log_detail('insert','',$log_data);



                    $this->db->trans_commit();

                    $responseData=array('status'=>true,'message'=>'Lot Created successfully.');

                }

                else

                {

                    $this->db->trans_rollback();

                    $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                }



    	 	}

        }

    }





    function generate_lot_from_halmarking($hm_process_id)

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $addData=$this->$model->get_halmarking_purchase_order($hm_process_id);



        $settings = $this->$model->get_ret_settings('lot_recv_branch');

    	if($settings == 1){ // HO Only

			$ho = $this->$model->get_headOffice();

		}



        $data = array(

        'lot_date'				=> date("Y-m-d H:i:s"),

        'created_branch'		=> (isset($ho['id_branch']) ? $ho['id_branch']:NULL ),

        'lot_received_at'       => $ho['id_branch'],

        'stock_type'            => 1,

        'lot_from'              => 2,

        'po_id'                 => $addData['po_id'],

        'gold_smith'			=> (isset($addData['po_karigar_id']) ? $addData['po_karigar_id'] :NULL ),

        'id_category'			=> (isset($addData['id_category']) ? $addData['id_category'] :NULL ),

        'id_purity'				=> (isset($addData['id_purity']) ? $addData['id_purity'] :NULL ),

        'created_on'	  		=> date("Y-m-d H:i:s"),

        'created_by'      		=> $this->session->userdata('uid')

        );

        $this->db->trans_begin();

	 	$insId = $this->$model->insertData($data,'ret_lot_inwards');

	 	//echo "<pre>"; print_r($this->db->last_query());exit;

	 	if($insId)

	 	{

	 	    $po_order_details=$this->$model->get_purchase_orders_by_product($addData['po_id']);

	 	    //echo "<pre>"; print_r($po_order_details);exit;

	 	    foreach($po_order_details as $items)

	 	    {

	 	        $item_details = array(

                    'lot_no'	    =>$insId,

                    'lot_product'   =>($items['id_product']!='' ? $items['id_product']:NULL),

                    'no_of_piece'   =>($items['total_pcs']!='' ? $items['total_pcs']:0),

                    'gross_wt'      =>($items['total_gwt']!='' ? $items['total_gwt']:0),

                    'less_wt'       =>($items['total_lwt']!='' ? $items['total_lwt']:0),

                    'net_wt'        =>($items['total_nwt']!='' ? $items['total_nwt']:0),

                );

                $detail_insId = $this->$model->insertData($item_details,'ret_lot_inwards_detail');

                //echo "<pre>"; print_r($this->db->last_query());exit;

	 	    }



	 	    if($detail_insId)

	 	    {

	 	        $order_items=$this->$model->check_purchase_halmarking_details($addData['po_id']);

	 	        $total_payable_wt=0;

	 	        $total_mc_value=0;

	 	        $total_making_charge   =0;

	 	        foreach($order_items as $order)

	 	        {

	 	            $total_weight=($order['item_wastage']>0 ? ((($order['total_nwt']*$order['item_wastage'])/100)+$order['total_nwt']) : $order['total_nwt'] );



	 	           // $pure_wt=(($total_weight*22)/24); // For calculating PURE WEIGHT (22CT to 24 CT)

	 	            $pure_wt = $order['item_pure_wt'];



	 	            $total_mc_value+=($order['mc_type']==1 ? ($order['total_gwt']*$order['mc_value']):($order['total_pcs']*$order['mc_value']));



	 	            $this->$model->updateData(array('is_lot_created'=>1,'lot_no'=>$insId,'item_pure_wt'=>$pure_wt),'po_item_id',$order['po_item_id'],'ret_purchase_order_items');



	 	            $item_mc_value  =($order['mc_type']==1 ? ($order['total_gwt']*$order['mc_value']):($order['total_pcs']*$order['mc_value']));



	 	            $total_payable_wt+=$pure_wt;





	 	             //Update Rate Fixing



	 	            /*if($order['fix_rate_per_grm']>0 && $order['is_rate_fixed']==1)

	 	            {

	 	                $total_amount =($order['item_pure_wt']*$order['fix_rate_per_grm']);



	 	                $tax_amt      = (($total_amount*$order['tax_percentage'])/100);



	 	                $total_weight_amt+=$tax_amt+$total_amount;



	 	                $rateFixData=array(

                           'rate_fix_po_item_id'    =>$order['po_item_id'],

                           'rate_fix_type'          =>1,

                           'rate_fix_wt'            =>$order['item_pure_wt'],

                           'tax_group_id'           =>$order['tgrp_id'],

                           'rate_fix_rate'          =>$order['fix_rate_per_grm'],

                           'rate_fix_created_from'  =>2,

                           'total_tax_amount'       =>$tax_amt,

                           'total_amount'           =>($tax_amt+$total_amount),

                           'rate_fix_create_by'     =>$this->session->userdata('uid'),

                           'rate_fix_created_on'    =>date("Y-m-d H:i:s"),

                           );

                           $insertId=$this->$model->insertData($rateFixData,'ret_po_rate_fix');

                          if($insertId)

    			          {

    			               $log_data = array(

                                	'id_log'        =>  $this->session->userdata('id_log'),

                                	'event_date'	=>  date("Y-m-d H:i:s"),

                                	'module'      	=> 'RATE FIXING',

                                	'operation'   	=> 'insert',

                                	'record'        =>  $order['po_item_id'],

                                	'remark'       	=> 'Rate Fixed Successfully..'

                                );

                                $this->log_model->log_detail('insert','',$log_data);

    			          }

	 	            }



	 	            if($order['mc_value']>0) // Update tax and insert into rate fix table for mc and other charges

	 	            {

	 	                $tax_amount=(($item_mc_value*$order['tax_percentage'])/100);



	 	                $total_making_charge+=$tax_amount+$item_mc_value;



	 	                $mc_payable_data=array(

                           'rate_fix_po_item_id'    =>$order['po_item_id'],

                           'rate_fix_type'          =>2,

                           'rate_fix_amt'           =>$item_mc_value,

                           'tax_group_id'           =>$order['tgrp_id'],

                           'rate_fix_rate'          =>0,

                           'rate_fix_created_from'  =>2,

                           'total_tax_amount'       =>$tax_amount,

                           'total_amount'           =>($tax_amount+$item_mc_value),

                           'rate_fix_create_by'     =>$this->session->userdata('uid'),

                           'rate_fix_created_on'    =>date("Y-m-d H:i:s"),

                           );

                           $insertId=$this->$model->insertData($mc_payable_data,'ret_po_rate_fix');

                          // print_r($this->db->last_query());exit;

	 	            }

	 	            */

	 	            //Update Rate Fixing



	 	        }



	 	        //Update Payable weight and amount

	 	        $payInsData=array(

	 	                        'total_payable_wt'=>number_format($total_payable_wt,3,'.',''),

	 	                       'total_payable_amt'=>number_format($total_weight_amt+$total_making_charge,2,'.',''),

	 	                        'po_id'=>$addData['po_id'],

	 	                        );

	 	        $paymentData=$this->$model->update_po_paymentData($payInsData,'+');

	 	        //print_r($this->db->last_query());exit;

	 	        //Update Payable weight and amount



	 	    }



            if($this->db->trans_status()===TRUE)

            {



                $log_data = array(

                	'id_log'        => $this->session->userdata('id_log'),

                	'event_date'	=> date("Y-m-d H:i:s"),

                	'module'      	=> 'Lot',

                	'operation'   	=> 'Add',

                	'record'        => $insId,

                	'remark'       	=> 'Lot added successfully From QC Receipt'

                );

                $this->log_model->log_detail('insert','',$log_data);



                $this->db->trans_commit();

                $responseData=array('status'=>true,'message'=>'Lot Created successfully.');

            }

            else

            {

                $this->db->trans_rollback();

                //echo $this->db->last_query();exit;

                $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

            }



	 	}

    }





    function get_qc_status_details()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_qc_status_details();

        echo json_encode($data);

    }


    function get_po_balance()
    {
        $model=	self::RET_PUR_ORDER_MODEL;
        $data= $this->$model->get_po_balance($_POST);
        echo json_encode($data);
    }


    function get_po_balance_details()
    {
        $model=	self::RET_PUR_ORDER_MODEL;
        $data= $this->$model->get_po_balance_details($_POST);
        echo json_encode($data);
    }

    function get_po_ratecut_balance_details()
    {
        $po_id = $this->input->post('po_id');
        $model = self::RET_PUR_ORDER_MODEL;
        $data = $this->$model->get_po_ratecut_balance_details($po_id);
        echo json_encode($data);
    }



    //Lot generate

    function lot_generate($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;


		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'generate_lot/list';

					$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_purchase/lot_generate/list');

        			$this->load->view('layout/template', $data);

			break;

			case 'add':

					$data['financial_year']  = $this->$model->GetFinancialYear();

					$data['main_content'] = self::VIEW_FOLDER.'generate_lot/form';

					$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_purchase/lot_generate/add');
        			$this->load->view('layout/template', $data);

			break;



			case 'po_details':

			    $data= $this->$model->get_qc_receipt_lot();

                echo json_encode($data);

			break;



			case 'qc_item_details':

			    $data= $this->$model->get_lot_generate_item_details($_POST);

                echo json_encode($data);

			break;



			case 'purchase_receipt':

			    $data= $this->$model->get_qc_issue_details();

                echo json_encode($data);

			break;



			case 'qc_entry':

					$data['main_content'] = self::VIEW_FOLDER.'qc_process/qc_entry';

        			$this->load->view('layout/template', $data);

			break;



			case 'ajax':

				$list=$this->$model->get_purchase_qc_details($_POST);

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/qc_issue_receipt/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

	}

    //Lot generate





    //QC Issue / Receipt

    function qc_issue_receipt($type="",$id="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;
		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'qc_process/list';

					$data['access'] = $this->$SETT_MOD->get_access('admin_ret_purchase/qc_issue_receipt/list');

        			$this->load->view('layout/template', $data);

			break;


			case 'add':

					$data['financial_year']  = $this->$model->GetFinancialYear();

					$data['main_content'] = self::VIEW_FOLDER.'qc_process/form';

        			$this->load->view('layout/template', $data);

			break;

			case 'qc_issue_edit':

				$data['main_content'] = self::VIEW_FOLDER.'qc_process/form';

        		$this->load->view('layout/template', $data);

			break;

			case 'qcIssuedEdit':

				$data = $this->$model->get_qcIssuedDetails($_POST);

				echo json_encode($data);

			break;



			case 'qc_receipt_edit':

				$data['main_content'] = self::VIEW_FOLDER.'qc_process/qc_entry';

				$this->load->view('layout/template', $data);

			break;

			case 'qcReceiptEdit':

				$data = $this->$model->get_qcReceiptDetails($_POST);

				echo json_encode($data);

			break;



			case 'purchase_issue':

			    // $data= $this->$model->purchase_issue();
				$data= $this->$model->purchase_issue($_POST['fin_year']);

                echo json_encode($data);

			break;



			case 'qc_item_details':

			    $data= $this->$model->get_purchase_item_details($_POST['po_id'],$_POST['fin_year']);

                echo json_encode($data);

			break;



			case 'purchase_receipt':

			    $data= $this->$model->get_qc_issue_details();

                echo json_encode($data);

			break;



			case 'qc_entry':

					$data['main_content'] = self::VIEW_FOLDER.'qc_process/qc_entry';

        			$this->load->view('layout/template', $data);

			break;

		 case 'cancel_qc_issue':

				$updData=array(

				'qc_status'          => 2,

				'cancel_reason'      => $_POST['cancel_reason'],

				'cancelled_on'        => date("Y-m-d H:i:s"),

				'cancelled_by'        => !empty($this->session->userdata('uid')) ? $this->session->userdata('uid') : NULL

				);

				$this->db->trans_begin();



				$this->$model->updateData($updData,'qc_process_id',$_POST['qc_process_id'],'ret_po_qc_issue_process');

				if($this->db->trans_status()===TRUE)

				{

					$this->db->trans_commit();

					 $log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'QC Issue/ Receipt',

						'operation'     => 'Cancel',

						'record'        =>  $id,

						'remark'        => 'QC Issue/ Receipt Cancelled successfully'

						);

				      $this->log_model->log_detail('insert','',$log_data);

					$this->session->set_flashdata('chit_alert',array('message'=>'QC Issue/ Receipt  Cancelled Successfully','class'=>'success','title'=>'QC Issue/ Receipt '));

					$return_data=array('status'=>TRUE,'message'=>'Order Instructions Added Successfully..');

				}

				else

				{

					echo $this->db->last_query();exit;

					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'QC Issue/ Receipt '));

					$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

				}

				echo json_encode($return_data);

		   break;


			case 'ajax':

				$list=$this->$model->get_purchase_qc_details($_POST);

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/qc_issue_receipt/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

	}



	function update_qc_issue()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $id_employee = $_POST['id_employee'];

	    $total_pcs   = $_POST['total_pcs'];

	    $total_gwt   = $_POST['total_gwt'];

	    $total_lwt   = $_POST['total_lwt'];

	    $total_nwt   = $_POST['total_nwt'];

	    $req_data    = $_POST['req_data'];

	    $ref_no      = $this->$model->get_qc_ref_no();

		$dCData          =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

        $bill_date       =  ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

	    $insData=array(

           'ref_no'         =>$ref_no,

		   'date_add'		=>$bill_date,

           'qc_id_vendor'   =>$id_employee,

           'issue_gross_wt' =>$total_gwt,

           'issue_less_wt'  =>$total_lwt,

           'issue_net_wt'   =>$total_nwt,

           'issue_pcs'      =>$total_pcs,

           'created_at'     =>date("Y-m-d H:i:s"),

	       'created_by'     =>$this->session->userdata('uid')

          );

        $this->db->trans_begin();

        $insId=$this->$model->insertData($insData,'ret_po_qc_issue_process');

        if($insId)

        {

            foreach($req_data as $items)

    	    {

    	        $itemDetails=array(

    	                       'qc_process_id'  =>$insId,

    	                       'po_item_id'     =>$items['po_item_id'],

    	                       'issue_pcs'      =>$items['qc_issue_pcs'],

    	                       'issue_gwt'      =>$items['qc_issue_gross_wt'],

    	                       'issue_lwt'      =>$items['qc_issue_less_wt'],

    	                       'issue_nwt'      =>$items['qc_issue_net_wt'],

    	                      );

    	       $status=$this->$model->insertData($itemDetails,'ret_po_qc_issue_details');

    	       if($status)

    	       {

    	          	if(!empty($items['stone_details']))

    				{

    					$stone_details = json_decode($items['stone_details'],true);

    					foreach($stone_details as $val)

    					{

    						$stone_data = array(

    							'id_qc_issue_details'       => $status,

    							'po_st_id'                  => $val['po_st_id'],

    							'stone_pcs'                 => $val['stone_pcs'],

    							'stone_wt'                  => $val['stone_wt'],

    						);

    						$this->$model->insertData($stone_data, 'ret_po_qc_issue_stone_details');

    						//echo $this->db->last_query();exit;

    					}

    				}

    	       }

    	    }

    	    if($this->db->trans_status()===TRUE)

            {



                $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'QUALITY CONTROL',

                        	'operation'   	=> 'Update',

                        	'record'        =>  NULL,

                        	'remark'       	=> 'QC Issued Successfully..'

                        );

                $this->log_model->log_detail('insert','',$log_data);



                $this->db->trans_commit();

                $responseData=array('status'=>true,'message'=>'QC Issued successfully.');

                $this->session->set_flashdata('chit_alert',array('message'=>'QC Issued successfully.','class'=>'success','title'=>'QC Issue'));

            }

            else

            {

                $this->db->trans_rollback();

                $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'QC Issue'));

            }

            echo json_encode($responseData);

        }

	}

	function qc_issue_update()

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		//echo"<pre>";print_r($_POST);exit;

		$qc_process_id = $_POST['qc_process_id'];

		$id_employee = $_POST['id_employee'];

	    $total_pcs   = $_POST['total_pcs'];

	    $total_gwt   = $_POST['total_gwt'];

	    $total_lwt   = $_POST['total_lwt'];

	    $total_nwt   = $_POST['total_nwt'];

	    $req_data    = $_POST['req_data'];



		$updData=array(

			'qc_id_vendor'   =>$id_employee,

			'issue_gross_wt' =>$total_gwt,

			'issue_less_wt'  =>$total_lwt,

			'issue_net_wt'   =>$total_nwt,

			'issue_pcs'      =>$total_pcs,

			'updated_on'     =>date("Y-m-d H:i:s"),

			'updated_by'     =>$this->session->userdata('uid')

		   );

		$this->db->trans_begin();

		$updstatus=$this->$model->updateData($updData,'qc_process_id',$qc_process_id,'ret_po_qc_issue_process');

		//print_r($this->db->last_query());exit;

		if($updstatus)

		{

			$qcIssueDelete = $this->$model->deleteData("qc_process_id",$qc_process_id,'ret_po_qc_issue_details');

			foreach($req_data as $items)

    	    {

				$itemDetails=array(

					'qc_process_id'  =>$qc_process_id,

					'po_item_id'     =>$items['po_item_id'],

					'issue_pcs'      =>$items['qc_issue_pcs'],

					'issue_gwt'      =>$items['qc_issue_gross_wt'],

					'issue_lwt'      =>$items['qc_issue_less_wt'],

					'issue_nwt'      =>$items['qc_issue_net_wt'],

				);

				$status=$this->$model->insertData($itemDetails,'ret_po_qc_issue_details');

				if($status)

				{

					if(!empty($items['stone_details']))

					{

						$stone_details = json_decode($items['stone_details'],true);

						foreach($stone_details as $val)

						{

							$stone_data = array(

								'id_qc_issue_details'       => $status,

								'po_st_id'                  => $val['po_st_id'],

								'stone_pcs'                 => $val['stone_pcs'],

								'stone_wt'                  => $val['stone_wt'],

							);

							$this->$model->insertData($stone_data, 'ret_po_qc_issue_stone_details');

							//echo $this->db->last_query();exit;

						}

					}

				}

			}

			if($this->db->trans_status()===TRUE)

            {



                $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'QUALITY CONTROL',

                        	'operation'   	=> 'Update',

                        	'record'        =>  NULL,

                        	'remark'       	=> 'QC Updated Successfully..'

                        );

                $this->log_model->log_detail('insert','',$log_data);



                $this->db->trans_commit();

                $responseData=array('status'=>true,'message'=>'QC Updated successfully.');

                $this->session->set_flashdata('chit_alert',array('message'=>'QC Updated successfully.','class'=>'success','title'=>'QC Issue'));

            }

            else

            {

                $this->db->trans_rollback();

                $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'QC Issue'));

            }

            echo json_encode($responseData);

		}

	}







    //QC Issue / Receipt





    //Halmarking issue/receipt

    function halmarking_issue_receipt($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'halmarking_process/list';

					$data['access'] = $this->$SETT_MOD->get_access('admin_ret_purchase/halmarking_issue_receipt/list');
        			$this->load->view('layout/template', $data);

			break;

			case 'add':

					$data['main_content'] = self::VIEW_FOLDER.'halmarking_process/form';

        			$this->load->view('layout/template', $data);

			break;



			case 'get_pending_halmarking_items':

			    $data= $this->$model->get_pending_halmarking_items();

                echo json_encode($data);

			break;



			case 'purchase_receipt':

			    $data= $this->$model->purchase_receipt_orders();

                echo json_encode($data);

			break;



			case 'hm_receipt':

					$data['main_content'] = self::VIEW_FOLDER.'halmarking_process/hm_receipt';

        			$this->load->view('layout/template', $data);

			break;



			case 'get_halmarking_issue_orders':

			    $data= $this->$model->get_halmarking_issue_orders();

                echo json_encode($data);

			break;



			case 'ajax':

				$list=$this->$model->get_halmarking_details($_POST);

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/halmarking_issue_receipt/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

	}





	function update_halmarking_issue()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $id_karigar  = $_POST['id_karigar'];

	    $total_pcs   = $_POST['total_pcs'];

	    $total_gwt   = $_POST['total_gwt'];

	    $total_lwt   = $_POST['total_lwt'];

	    $total_nwt   = $_POST['total_nwt'];

	    $req_data   =$_POST['req_data'];

	    $insData=array(

	       'hm_ref_no'              =>$this->$model->generate_HalmarkingRefNo(),

           'hm_vendor_id'           =>$id_karigar,

           'hm_process_gwt'         =>$total_gwt,

           'hm_process_lwt'         =>$total_lwt,

           'hm_process_nwt'         =>$total_nwt,

           'hm_process_pcs'         =>$total_pcs,

           'hm_process_created_at'  =>date("Y-m-d H:i:s"),

	       'hm_process_created_by'  =>$this->session->userdata('uid')

          );

        $this->db->trans_begin();

        $insId=$this->$model->insertData($insData,'ret_po_halmark_process');

        //print_r($this->db->last_query());exit;

        if($insId)

        {

            foreach($req_data as $items)

    	    {

    	        $itemDetails=array(

    	                       'hm_issue_id'    =>$insId,

    	                       'hm_po_item_id'  =>$items['po_item_id'],

							   'hm_po_qc_issue_id' => $items['id_qc_issue_details'],

							   	'hm_issue_pcs'  =>$items['hm_issue_pcs'],

								'hm_issue_gwt'  =>$items['hm_issue_gwt'],

								'hm_issue_nwt'  =>$items['hm_issue_nwt'],

								'hm_issue_lwt'  =>$items['hm_issue_lwt'],

    	                      );

    	       $status=$this->$model->insertData($itemDetails,'ret_po_hm_process_details');

    	       if($status)

    	       {

    	           $this->$model->updateData(array('status'=>3),'po_item_id',$items['po_item_id'],'ret_purchase_order_items');

				   
				 
				    if(!empty($items['stone_details']))
				    {
					   $stone_details = json_decode($items['stone_details'],true);

					   foreach($stone_details as $val)
					   {
						   $stone_data = array(
							   'ret_hm_receipt_id'       => $status,

							   'po_st_id'                  => $val['po_st_id'],

							   'stone_pcs'                 => $val['stone_pcs'],

							   'stone_wt'                  => $val['stone_wt'],

						    );

						   $this->$model->insertData($stone_data, 'ret_po_hm_issue_stone_details');

						   //echo $this->db->last_query();exit;

					   }

				   }

    	       }

    	    }

    	    if($this->db->trans_status()===TRUE)

            {



                $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'HALMARKING',

                        	'operation'   	=> 'Update',

                        	'record'        =>  NULL,

                        	'remark'       	=> 'hm Issued Successfully..'

                        );

                $this->log_model->log_detail('insert','',$log_data);



                $this->db->trans_commit();

                $responseData=array('status'=>true,'message'=>'Halmarking Issued successfully.');

                $this->session->set_flashdata('chit_alert',array('message'=>'QC Issued successfully.','class'=>'success','title'=>'Halmarking Issue'));

            }

            else

            {

                $this->db->trans_rollback();

                $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Halmarking Issue'));

            }

            echo json_encode($responseData);

        }

	}



	function update_halmarking_receipt()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $req_data   =$_POST['req_data'];

	    $hm_process_id   =$_POST['hm_process_id'];

	    $hm_vendor_ref_id = !empty($_POST['hm_vendor_ref_id']) ? $_POST['hm_vendor_ref_id'] : NULL;

        $this->db->trans_begin();

        $halmarking_status=$this->$model->updateData(array('status'=>2,'hm_received_at'=>date("Y-m-d H:i:s")),'hm_process_id',$hm_process_id,'ret_po_halmark_process');

        if($halmarking_status)

        {

            $total_hm_charges=0;

            foreach($req_data as $items)

    	    {

    	        $total_hm_charges+=$items['halmarking_charges'];

    	        $updData=array(

    	                      'status'              =>4,

    	                      'hm_rejected_pcs'     =>$items['hm_rejected_pcs'],

    	                      'hm_rejected_gwt'     =>$items['hm_rejected_gwt'],

    	                      'hm_rejected_lwt'     =>$items['hm_rejected_lwt'],

    	                      'hm_rejected_nwt'     =>$items['hm_rejected_nwt'],

    	                      'halmarking_charges'  =>$items['halmarking_charges'],

    	                      'is_halmark_from'     =>2,

    	                      'is_halmarked'        =>1

    	                      );



    	       $status=$this->$model->updateData($updData,'po_item_id',$items['po_item_id'],'ret_purchase_order_items');
    	       //print_r($this->db->last_query());exit;

			   $hm_data = array(

					'hm_status'         => 1,  // Completed
					
					'hm_failed_pcs'     =>$items['hm_rejected_pcs'],

					'hm_failed_gwt'     =>$items['hm_rejected_gwt'],

					'hm_failed_lwt'     =>$items['hm_rejected_lwt'],

					'hm_failed_nwt'     =>$items['hm_rejected_nwt'],

					'hm_passed_pcs'     =>$items['hm_received_pcs'],

					'hm_passed_gwt'     =>$items['hm_received_gwt'],

					'hm_passed_nwt'     =>$items['hm_received_nwt'],

					'hm_passed_lwt'     =>$items['hm_received_lwt'],
			   );

			   $hm_status = $this->$model->updateData($hm_data,'hm_receipt_id',$items['hm_receipt_id'],'ret_po_hm_process_details');

			   if($hm_status)
			   {
					if(!empty($items['stone_details'][$key]))
					{

						$stone_details = json_decode($items['stone_details'][$key],true);

						//echo "<pre>"; print_r($stone_details);exit;

						foreach($stone_details as $val)

						{

							$stone_data = array(

								'hm_rejected_pcs'     => $val['po_stone_rejected_pcs'],

								'hm_rejected_wt'      => ($val['po_stone_rejected_wt']!='' ? $val['po_stone_rejected_wt']:0),

								'hm_passed_pcs'       => $val['po_stone_accepted_pcs'],

								'hm_passed_wt'        => $val['po_stone_accepted_wt'],

							);

							$this->$model->updateData($stone_data,'ret_hm_issue_stn_id',$val['ret_hm_issue_stn_id'],'ret_po_hm_issue_stone_details');

						}

					}
			   }

    	    }

    	    if($this->db->trans_status()===TRUE)

            {

                $this->$model->updateData(array('total_hm_charges'=>$total_hm_charges, 'hm_vendor_ref_id' => $hm_vendor_ref_id),'hm_process_id',$hm_process_id,'ret_po_halmark_process');



                //Generate LOT

                /*if($hm_process_id!='')

                {

                    $LotStatus=TRUE;

                    $pur_ord_det=$this->$model->get_purchase_order_hm_details($hm_process_id); // CHECKING QC STATUS AD H.M STATUS

                    foreach($pur_ord_det as $po_items)

                    {

                        if($po_items['status']==0)

                        {

                            $LotStatus=FALSE;

                        }

                    }

                    if($LotStatus)

                    {

                        $this->generate_lot_from_halmarking($hm_process_id);

                    }

                }*/

                //Generate LOT



                $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'HALMARKING',

                        	'operation'   	=> 'Update',

                        	'record'        =>  $hm_process_id,

                        	'remark'       	=> 'HM Updated Successfully..'

                        );

                $this->log_model->log_detail('insert','',$log_data);

                $this->db->trans_commit();

                $responseData=array('status'=>true,'message'=>'Halmarking Receipt added successfully.');

                $this->session->set_flashdata('chit_alert',array('message'=>'HM Receipt successfully.','class'=>'success','title'=>'Halmarking Receipt'));

            }

            else

            {

                $this->db->trans_rollback();

                $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Halmarking Issue'));

            }

	    }

        echo json_encode($responseData);



	}



    //Halmarking issue/receipt





    //Purchase Payment

    /*function supplier_po_payment($type="", $id="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'popayment/list';

        			$this->load->view('layout/template', $data);

			break;

			case 'add':

					$data['main_content'] = self::VIEW_FOLDER.'popayment/form';

        			$this->load->view('layout/template', $data);

			break;



			case 'purchase_payment_details':

			    $data= $this->$model->purchase_payment_details($_POST);

                echo json_encode($data);

			break;



			case 'save':

			    //echo "<pre>";print_r($_POST);echo "</pre>";exit;

			    $billing        = $_POST;



			    if($billing['bill_type']==1)

			    {

			        $orderData  = $this->$model->get_purOrders($billing['po_ref_no']);

			        $pay_po_id  = $orderData['po_id'];

			    }else

			    {

			        $pay_po_id=NULL;

			    }





			    $pay_refno       = $this->$model->generatePaymentNo();



		        $payData=array(

		                  'pay_sup_id'      => $billing['suppay']['pay_sup_id'],

		                  'bill_type'       => $billing['suppay']['bill_type'],

		                  'type'            => 1,

		                  'pay_refno'       => $pay_refno,

		                  'pay_amt'         => $billing['suppay']['received_amount'],

		                  // 'pay_create_on'   => date("Y-m-d H:i:s"),

		                  'pay_created_by'  => $this->session->userdata('uid'),

		                  );



			    $this->db->trans_begin();

			    $insId = $this->$model->insertData($payData,'ret_po_payment');



                if($insId)

                {



                    //insert into po reference table

                    if($billing['suppay']['bill_type'] ==1) // If againist PO

                    {

                        //ret_supplier_pay_po_details

                        foreach( $billing['popay']['po_item_pay'] as $key => $val)

        		        {



        		            $payDetailData = array(

        		                  'po_pay_id'           => $insId,

        		                  'pay_po_ref_id'       => $billing['popay']['po_item_pay'][$key],

        		                  'pay_po_adj_amount'   => $billing['popay']['curpayable'][$key]

        		              );



        		              	$this->$model->insertData($payDetailData,'ret_supplier_pay_po_details');



        		        }





                    }





                    $net_banking_details = json_decode($billing['suppay']['net_banking'],true);

                    $sales_details		 = json_decode($billing['suppay']['sales_details'],true);



                    if($billing['suppay']['bill_type']==2) // If Advance

                    {

                        $wallet = $this->$model->get_retWallet_details($billing['suppay']['pay_sup_id']);

    	 				if($wallet['status'])

    	 				{

    						$insWallet=array(

    						'id_wallet'	=>$wallet['id_wallet'],

    						'po_pay_id'	=>$insId,

    						'amount'	=>$billing['suppay']['received_amount'],

    						'type'	    =>1,

    						'remarks'   =>'Karigar Advace Amount'

    						 );

    						$this->$model->insertData($insWallet,'ret_karigar_wallet_transcation');

    						//echo "<pre>"; print_r($metal_rate);exit;

    	 				}

    	 				else

    	 				{

    	 				   $wallet_acc=array(

        	 				        'id_karigar'    =>$billing['suppay']['pay_sup_id'],

        	 				        'amount'        =>$billing['suppay']['received_amount'],

        	 				   	    'created_by'    =>$this->session->userdata('uid'),

        						    'created_on'    =>date("Y-m-d H:i:s")

    						    );

     					   $insWalletAcc= $this->$model->insertData($wallet_acc,'ret_karigar_wallet');

     					   //print_r($this->db->last_query());exit;

     					   if($insWalletAcc)

     					   {

    	 					   	$insWallet=array(

        						'id_wallet'	=>$insWalletAcc,

        						'po_pay_id'	=>$insId,

        						'amount'	=>$billing['suppay']['received_amount'],

        						'type'	    =>1,

        						'remarks'   =>'Karigar Advace Amount'

        						 );

        						$this->$model->insertData($insWallet,'ret_karigar_wallet_transcation');

        						//print_r($this->db->last_query());exit;

     					   }



    	 				}

                    }



                    if(sizeof($net_banking_details)>0)

                    {

                        foreach($net_banking_details as $pay)

                        {

                            $netBankingPaymentData[]=array(

                                             'pay_id'           =>$insId,

                                             'type'             =>1,

                                             'pay_mode'         =>$pay['nb_type'],

                                             'payment_amount'   =>$pay['pay_amount'],

                                             'ref_no'           =>($pay['ref_no']!='' ? $pay['ref_no']:NULL),

                                            );

                            $this->$model->insertBatchData($netBankingPaymentData,'ret_po_payment_detail');

                        }

                    }



                    if(sizeof($sales_details)>0)

                    {

                        foreach($sales_details as $pay)

                        {

                            $SalesPaymentData[]=array(

                                             'pay_id'         =>$insId,

                                             'type'           =>2,

                                             'payment_amount' =>$pay['pay_amount'],

                                             'bill_id'        =>$pay['bill_id'],

                                            );

                            $this->$model->insertBatchData($SalesPaymentData,'ret_po_payment_detail');

                        }

                    }







                    if($this->db->trans_status()===TRUE)

                    {

                        $log_data = array(

                                	'id_log'        => $this->session->userdata('id_log'),

                                	'event_date'	=> date("Y-m-d H:i:s"),

                                	'module'      	=> 'PURCHASE PAYMENT',

                                	'operation'   	=> 'insert',

                                	'record'        =>  $orderData['po_id'],

                                	'remark'       	=> 'Purchase Payment Successfully..'

                                );

                        $this->log_model->log_detail('insert','',$log_data);







                        $this->db->trans_commit();

                        $responseData=array('status'=>true,'message'=>'Receipt added successfully.');

                        $this->session->set_flashdata('chit_alert',array('message'=>'Receipt successfully.','class'=>'success','title'=>'Purchase Payment'));

                    }

                    else

                    {

                        echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Purchase Payment'));

                    }

                }else

                {

                        echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Purchase Payment'));



                }



                echo json_encode($responseData);

			 break;



			case 'pendingpos':

			    $pendingpos = $this->$model->get_karigarPendingPos($this->input->post('id_karigar'));

			    echo json_encode(array('status' => TRUE, 'pendingpos' => $pendingpos));

			break;



			case 'paymenthistory':

			    $paymenthistory = $this->$model->get_karigarPosPaidHistory($this->input->post('id_karigar'));

			    echo json_encode(array('status' => TRUE, 'payhistory' => $paymenthistory));

			break;



			case 'paymentacknolodgement':

			    $set_model = "admin_settings_model";

        		$data['comp_details']   = $this->$set_model->get_company();

        		$data['paymentdetails'] = $this->$model->get_po_paid_detail($id);



        		//echo "<pre>"; print_r($data);exit;

        		$this->load->helper(array('dompdf', 'file'));

        	        $dompdf = new DOMPDF();

        			$html = $this->load->view(self::VIEW_FOLDER.'popayment/payment_ack', $data,true);

        			$dompdf->load_html($html);

        			$dompdf->set_paper('A4', "portriat" );

        			$dompdf->render();

        			while(ob_get_level()) ob_end_clean();
        			$dompdf->stream("VendorAck.pdf",array('Attachment'=>0));

			break;



			case 'ajax':

				$list=$this->$model->get_PurchaseSupplierPaymentList($_POST);

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/halmarking_issue_receipt/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

	}*/





	function supplier_po_payment($type="", $id="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;


		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'popayment/list';

					$data['access'] = $this->$SETT_MOD->get_access('admin_ret_purchase/supplier_po_payment/list');
        			$this->load->view('layout/template', $data);

			break;

			case 'add':

					$data['main_content'] = self::VIEW_FOLDER.'popayment/form';

        			$this->load->view('layout/template', $data);

			break;



			case 'purchase_payment_details':

			    $data= $this->$model->purchase_payment_details($_POST);

                echo json_encode($data);

			break;



			case 'get_supplier_pay_details':

			    $data= $this->$model->get_supplier_pay_details($_POST);

                echo json_encode($data);

			break;



			case 'supplier_advance_details':

			    $data= $this->$model->get_supplier_advance_details($_POST);

                echo json_encode($data);

			break;



			case 'get_purchase_return_entries':

			    $data= $this->$model->get_purchase_return_entries_for_payment($_POST);

                echo json_encode($data);

			break;



			case 'edit':



            	$data['main_content'] = self::VIEW_FOLDER.'popayment/form';



            	$this->load->view('layout/template', $data);



            break;









            case 'po_pay_details':



            	$data = $this->$model->get_po_payment($id);



            	echo json_encode($data);



            break;



			case 'cancel_pay_entry':

			    $ho              =  $this->$model->get_headOffice();

        		$dCData          =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

        		$bill_date       =  ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

        		$pay_id      = $_POST['pay_id'];

			    $updData=array(

                    'pay_status'        => 2,

                    'cancel_reason'     => $_POST['cancel_reason'],

                    'cancelled_date'    => $bill_date,

                    'cancelled_by'      => $this->session->userdata('uid')

                    );

                    $this->db->trans_begin();

                    $ret_status = $this->$model->updateData($updData,'pay_id',$pay_id,'ret_po_payment');

                    if($this->db->trans_status()===TRUE)

                    {

                        $this->db->trans_commit();

						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'SUPPLIER PAYMENT',

							'operation'     => 'Cancel',

							'record'        =>  $pay_id,

							'remark'        => 'Supplier payment Cancelled successfully'

							);

	                      $this->log_model->log_detail('insert','',$log_data);

                        $this->session->set_flashdata('chit_alert',array('message'=>'Payment Cancelled Successfully','class'=>'success','title'=>'Purchase Payment'));

                        $return_data=array('status'=>TRUE,'message'=>'Order Instructions Added Successfully..');

                    }

                    else

                    {

                        echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Purchase Payment'));

                        $return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

                    }

                echo json_encode($return_data);

			break;



			case 'save':

			    $billing        = $_POST['billing'];

				$net_banking_details = json_decode($billing['net_banking'], true);
				if (!is_array($net_banking_details)) $net_banking_details = [];

				$cheque_details = json_decode($billing['chq_pay'], true);
				if (!is_array($cheque_details)) $cheque_details = [];

				$checked_count = 0;
				$total_entries = isset($billing['total_entries']) ? (int)$billing['total_entries'] : 0;
				for ($i = 0; $i <= $total_entries; $i++)
				{
					if (isset($billing['bill_checked_' . $i]) && (int)$billing['bill_checked_' . $i] == 1)
					{
						$checked_count++;
					}
				}

				if ($checked_count > 1 && (($billing['cash_amount'] && !empty($net_banking_details)) || ($billing['cash_amount'] && !empty($cheque_details)) || (!empty($net_banking_details) && !empty($cheque_details))))
				{
					$responseData = array('status' => false, 'message' => 'Unable to proceed Multiple bills with multiple payment modes');

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed Multiple bills with multiple payment modes', 'class' => 'danger', 'title' => 'Purchase Payment'));

					echo json_encode($responseData);
					return;
				}

				$pay_refno       = $this->$model->generatePaymentNo();

				// Calculate actual payment amount (only cash + net banking + cheque)
				$cash_amt = floatval($billing['cash_amount']);
				$nb_amt = 0;
				$chq_amt = 0;
				$nb_details = json_decode($billing['net_banking'], true);
				if(!empty($nb_details)) {
					foreach($nb_details as $nb) {
						$nb_amt += floatval($nb['pay_amount']);
					}
				}
				$chq_details = json_decode($billing['chq_pay'], true);
				if(!empty($chq_details)) {
					foreach($chq_details as $chq) {
						$chq_amt += floatval($chq['payment_amount']);
					}
				}
				$actual_pay_amt = $cash_amt + $nb_amt + $chq_amt;

				$payData = array(

							'pay_sup_id'      =>$billing['id_karigar'],

							'bill_type'       =>$billing['bill_type'],

							'pay_refno'       =>$pay_refno,

							'pay_amt'         =>$actual_pay_amt,

							'pay_narration'   =>($billing['remark']!='' ? $billing['remark']:NULL),

							'pay_create_on'   =>date("Y-m-d H:i:s"),

							'pay_created_by'=>$this->session->userdata('uid'),

							);



					$this->db->trans_begin();

					$insId=$this->$model->insertData($payData,'ret_po_payment');


					if($insId)

					{


						//$sales_details		 = json_decode($billing['sales_details'],true);

						//$adv_details		 = json_decode($billing['adv_details'],true);



						// if($billing['bill_type']==2)

						// {

						//     $wallet=$this->$model->get_retWallet_details($billing['id_karigar']);

							// if($wallet['status'])

							// {

							//     $updStatus=$this->$model->updateWalletData(array('amount'=>$billing['tot_amt_received'],'id_wallet'=>$wallet['id_wallet']),'+');



							// 	$insWallet=array(

							// 	'id_wallet'	=>$wallet['id_wallet'],

							// 	'po_pay_id'	=>$insId,

							// 	'amount'	=>$billing['tot_amt_received'],

							// 	'type'	    =>1,

							// 	'remarks'   =>($billing['remark']!='' ? $billing['remark']:NULL)

							// 	 );

							// 	$this->$model->insertData($insWallet,'ret_karigar_wallet_transcation');

							// 	//echo "<pre>"; print_r($metal_rate);exit;

							// }

							// else

							// {

							//    $wallet_acc=array(

							// 	        'id_karigar'    =>$billing['id_karigar'],

							// 	        'amount'        =>$billing['tot_amt_received'],

							// 	   	    'created_by'    =>$this->session->userdata('uid'),

							// 		    'created_on'    =>date("Y-m-d H:i:s")

							// 	    );

							//    $insWalletAcc= $this->$model->insertData($wallet_acc,'ret_karigar_wallet');

							//    //print_r($this->db->last_query());exit;

							//    if($insWalletAcc)

							//    {

							// 	   	$insWallet=array(

							// 		'id_wallet'	=>$insWalletAcc,

							// 		'po_pay_id'	=>$insId,

							// 		'amount'	=>$billing['tot_amt_received'],

							// 		'type'	    =>1,

							// 		'remarks'   =>($billing['remark']!='' ? $billing['remark']:NULL)

							// 		 );

							// 		$this->$model->insertData($insWallet,'ret_karigar_wallet_transcation');

							// 		//print_r($this->db->last_query());exit;

							//    }



							// }

						// }

						if($billing['cash_amount']!=0)
						{

							$cash_payment_data = array(

												'pay_id'           =>$insId,

												'type'             =>4,

												'pay_mode'         =>'CSH',

												'payment_amount'   =>$billing['cash_amount'],

												);

							$this->$model->insertData($cash_payment_data,'ret_po_payment_detail');

						}



						if(!empty($net_banking_details))

						{

							foreach($net_banking_details as $pay)

							{

								$netBankingPaymentData[]=array(

												'pay_id'           =>$insId,

												'type'             =>1,

												'pay_mode'         =>$pay['nb_type'],

												'payment_amount'   =>$pay['pay_amount'],

												'ref_no'           =>($pay['ref_no']!='' ? $pay['ref_no']:NULL),

												'id_bank'          =>($pay['id_bank']!='' ? $pay['id_bank']:NULL),

												'ref_date'          =>($pay['nb_date']!='' ? $pay['nb_date']:date("Y-m-d")),

												);

							}

							$this->$model->insertBatchData($netBankingPaymentData,'ret_po_payment_detail');

						}


						if(!empty($cheque_details))

						{
							// flag for update payee name if not exist in karigar table
							$i = 0;

							foreach($cheque_details as $chq_pay)

							{
								$i++;
								$cheque_deposit_date = ($chq_pay['cheque_date']!='' ?date_create($chq_pay['cheque_date']) :NULL);

								$cheque_date = ($chq_pay['cheque_date']!='' ? date_format($cheque_deposit_date,"Y-m-d"):NULL);

								$arraychqPay[]=array(

									'pay_id'           =>$insId,

									'payment_amount'=>$chq_pay['payment_amount'],

									'type'			=>5,

									'cheque_date'		=>($chq_pay['cheque_date']!='' ? $cheque_date:NULL),

									'pay_mode'	=>'CHQ',

									'cheque_no'		=>($chq_pay['cheque_no']!='' ? $chq_pay['cheque_no']:NULL),

									'id_bank'		=>($chq_pay['bank_name']!='' ? $chq_pay['bank_name']:NULL),

									'bank_branch'	=>($chq_pay['bank_branch']!='' ? $chq_pay['bank_branch']:NULL),

									'payee_name'	=>($chq_pay['payee_name']!='' ? $chq_pay['payee_name']:NULL),


								);
								if($chq_pay['payee_name'])
								{
									$data=array(
										'acc_holder_name'         =>$chq_pay['payee_name'],
									);
									$update_status = $this->$model->updateData($data,'id_karigar',$billing['id_karigar'], 'ret_karigar');
								}

								//  print_r($arraychqPay);exit;

							}

							if(!empty($arraychqPay))
							{

								$chqPayInsert = $this->$model->insertBatchData($arraychqPay,'ret_po_payment_detail');



							}

						}

						for($i=0;$i<=$total_entries;$i++)
						{
						
						
								if((int)$billing['bill_checked_'.$i] == 0)
									continue;
								$amount = $billing['amount_paid_'.$i];
							$po_ids = $billing['bill_ref_no_'.$i];

						$opening_payment_id = 0;
						$crdrid_val = NULL;
						if((int)$_POST['opening_checked']==1)
						{
							
							
							$amount = $actual_pay_amt;
							$po_ids = 0;
							$opening_payment_id = $billing['bill_ref_no_'.$i];
						}
						// Handle crdr_credit type
						if(isset($billing['type_'.$i]) && $billing['type_'.$i] == 'crdr_credit')
						{
							$crdrid_val = $billing['bill_ref_no_'.$i];
							$po_ids = 0;
						}
						// Handle rate_fix type
						$rate_fix_id_val = NULL;
						if(isset($billing['type_'.$i]) && $billing['type_'.$i] == 'rate_fix')
						{
							$rate_fix_id_val = $billing['bill_ref_no_'.$i];
							$po_ids = 0;
						}
						$individual_payment=array(
							'pay_id'         				 => $insId,
							'po_id'				 			 => $po_ids,
							'id_supplier_rate_cut'			 => $billing['bill_rate_no_'.$i],
							'rate_fix_id'					 => $rate_fix_id_val,
							'crdrid'						 => $crdrid_val,
							'bill_amount' 					 => $amount,
							'opening_id' 					 => $opening_payment_id,
							'total_amount'   				 => $actual_pay_amt
						);

						$this->$model->insertData($individual_payment,'ret_po_bill_payment_details');
						
					}
					
						// Process debit note adjustments
					if(!empty($billing['debit_adjustments']))
					{
						$debit_adjustments = json_decode($billing['debit_adjustments'], true);
						if(!empty($debit_adjustments))
						{
							foreach($debit_adjustments as $adj)
							{
								// Insert partial adjustment into tracking table
								if(!empty($adj['crdrid']) && floatval($adj['amount']) > 0)
								{
									$adj_data = array(
										'crdrid'          => $adj['crdrid'],
										'po_id'           => NULL,
										'pay_id'          => $insId,
										'adjusted_amount' => floatval($adj['amount']),
										'created_by'      => $this->session->userdata('uid'),
										'created_on'      => date("Y-m-d H:i:s")
									);
									$this->$model->insertData($adj_data, 'ret_crdr_note_po_adj');
								}
							}
						}
							
					}

					// Process purchase return adjustments (NO crdr entry created)
					if(!empty($billing['purchase_return_adjustments']))
					{
						$return_adjustments = json_decode($billing['purchase_return_adjustments'], true);
						if(!empty($return_adjustments))
						{
							foreach($return_adjustments as $ret_adj)
							{
								if(!empty($ret_adj['pur_return_id']) && floatval($ret_adj['amount']) > 0)
								{
									$adj_data = array(
										'pur_return_id'   => $ret_adj['pur_return_id'],
										'po_id'           => NULL,
										'pay_id'          => $insId,
										'adjusted_amount'  => floatval($ret_adj['amount']),
										'created_by'      => $this->session->userdata('uid'),
										'created_on'      => date("Y-m-d H:i:s")
									);
									$this->$model->insertData($adj_data, 'ret_purchase_return_po_adj');
								}
							}
						}
					}
					}
					else
					{
							// echo $this->db->last_query();exit;

							// $this->db->trans_rollback();

							$responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

							$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Purchase Payment'));

					}





                    if($this->db->trans_status()===TRUE)
                    {

                        $log_data = array(

                                	'id_log'        => $this->session->userdata('id_log'),

                                	'event_date'	=> date("Y-m-d H:i:s"),

                                	'module'      	=> 'SUPPLIER  PAYMENT',

                                	'operation'   	=> 'insert',

                                	'record'        =>  $insId,

                                	'remark'       	=> 'Supplier Payment Successfully..'

                                );

                        $this->log_model->log_detail('insert','',$log_data);







                        $this->db->trans_commit();

                        $responseData=array('status'=>true,'message'=>'Receipt added successfully.','id'=>$insId);

                        $this->session->set_flashdata('chit_alert',array('message'=>'Receipt successfully.','class'=>'success','title'=>'Purchase Payment'));

                    }

                    else

                    {

                        $this->db->trans_rollback();

                        $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Purchase Payment'));

                    }

                echo json_encode($responseData);

			 break;



			 case 'update':



            	$updData = $_POST;



            	$id=$updData['billing']['pay_id'];



            	$data=array(



            		'pay_amt'         =>$updData['billing']['tot_amt_received'],



            		'pay_narration'   =>($updData['billing']['remark']!='' ? $updData['billing']['remark']:NULL),



            	);



            	$this->db->trans_begin();





            	$update_status = $this->$model->updateData($data,'pay_id',$id, 'ret_po_payment');





            	//print_r($this->db->last_query());exit;











            	if($update_status)

            	{



            		$net_banking_details = json_decode($updData['billing']['net_banking'],true);



            		$nb_delete_id = $this->$model->deleteData("pay_id",$id,'ret_po_payment_detail');



            		if(sizeof($net_banking_details)>0)

            		{



            			foreach($net_banking_details as $pay)

            			{

            				$netBankingPaymentData[]=array(

            					'pay_id'           =>$id,

            					'type'             =>1,

            					'pay_mode'         =>$pay['nb_type'],

            					'payment_amount'   =>$pay['pay_amount'],

            					'ref_no'           =>($pay['ref_no']!='' ? $pay['ref_no']:NULL),

            					'id_bank'          =>($pay['id_bank']!='' ? $pay['id_bank']:NULL),

            					'ref_date'          =>($pay['nb_date']!='' ? $pay['nb_date']:date("Y-m-d")),

            				);



            			}



            			$this->$model->insertBatchData($netBankingPaymentData,'ret_po_payment_detail');



            		}



            	}





            		if($this->db->trans_status()===TRUE)

                                {



                                    $this->db->trans_commit();

									$log_data = array(

										'id_log'        => $this->session->userdata('id_log'),

										'event_date'    => date("Y-m-d H:i:s"),

										'module'        => 'SUPPLIER PAYMENT',

										'operation'     => 'Update',

										'record'        =>  $update_status,

										'remark'        => 'Supplier payment Updated successfully'

										);

							         $this->log_model->log_detail('insert','',$log_data);

                                    $responseData=array('status'=>true,'message'=>'Receipt updated successfully.');

                                    $this->session->set_flashdata('chit_alert',array('message'=>'Receipt updated successfully.','class'=>'success','title'=>'Purchase Payment'));

                                }

                                else

                                {

                                    echo $this->db->last_query();exit;

                                    $this->db->trans_rollback();

                                    $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                                    $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Purchase Payment'));

                                }





                            echo json_encode($responseData);



            break;



			case 'pendingpos':

			    $pendingpos = $this->$model->get_karigarPendingPos($this->input->post('id_karigar'));

			    echo json_encode(array('status' => TRUE, 'pendingpos' => $pendingpos));

			break;



			case 'paymenthistory':

			    $paymenthistory = $this->$model->get_karigarPosPaidHistory($this->input->post('id_karigar'));

			    echo json_encode(array('status' => TRUE, 'payhistory' => $paymenthistory));

			break;



			case 'paymentacknolodgement':

			    $set_model = "admin_settings_model";

        		$data['comp_details']   = $this->$set_model->get_company();

        		$data['paymentdetails'] = $this->$model->get_po_paid_detail($id);

				$data['payment'] = $this->$model->get_po_paid_payment($id);



        		//echo "<pre>"; print_r($data);exit;

        		$this->load->helper(array('dompdf', 'file'));

        	        $dompdf = new DOMPDF();

        			$html = $this->load->view(self::VIEW_FOLDER.'popayment/payment_ack', $data,true);

        			$dompdf->load_html($html);

        			$dompdf->set_paper('A4', "portriat" );

        			$dompdf->render();

        			while(ob_get_level()) ob_end_clean();
        			$dompdf->stream("VendorAck.pdf",array('Attachment'=>0));

			break;




			case 'verify':
				echo json_encode(array('status' => $this->$model->verify_po_payment($this->input->post('id'))));
			break;

			case 'ajax':

			    $from_date	= $this->input->post('from_date');

			    $to_date	= $this->input->post('to_date');

				$list       = $this->$model->get_PurchaseSupplierPaymentList($from_date, $to_date);

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/supplier_po_payment/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

			case 'print_cheque':

			    $set_model = "admin_settings_model";

        		$data['comp_details']   = $this->$set_model->get_company();

				$this->load->model('ret_billing_model');

        		$data['paymentdetails'] = $this->$model->get_po_paid_detail($id);

				$data['payment'] = $this->$model->get_po_paid_payment($id);

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

		}

	}



    //Purchase Payment









    //Purchase Payment

    function purchase_payment($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'payment/list';

        			$this->load->view('layout/template', $data);

			break;

			case 'add':

					$data['main_content'] = self::VIEW_FOLDER.'payment/form';

        			$this->load->view('layout/template', $data);

			break;



			case 'purchase_payment_details':

			    $data= $this->$model->purchase_payment_details($_POST);

                echo json_encode($data);

			break;



			case 'save':

			    //echo "<pre>";print_r($_POST);exit;

			    $billing        =$_POST['billing'];



			    $pay_refno       = $this->$model->generatePaymentNo();



		        $payData[]=array(

		                  'pay_sup_id'      =>$billing['id_karigar'],

		                  'bill_type'       =>$billing['bill_type'],

		                  'pay_refno'       =>$pay_refno,

		                  'pay_amt'         =>$billing['tot_amt_received'],

		                  'pay_narration'   =>($billing['remark']!='' ? $billing['remark']:NULL),

		                  'pay_create_on'   =>date("Y-m-d H:i:s"),

		                  'pay_created_by'=>$this->session->userdata('uid'),

		                  );



			    $this->db->trans_begin();

			    $insId=$this->$model->insertBatchData($payData,'ret_po_payment');



                if($insId)

                {

                    $net_banking_details = json_decode($billing['net_banking'],true);

                    $sales_details		 = json_decode($billing['sales_details'],true);



                    if($billing['bill_type']==2)

                    {

                        $wallet=$this->$model->get_retWallet_details($billing['id_karigar']);

    	 				if($wallet['status'])

    	 				{

    	 				    $updStatus=$this->$model->updateWalletData(array('amount'=>$billing['tot_amt_received'],'id_wallet'=>$wallet['id_wallet']),'+');



    						$insWallet=array(

    						'id_wallet'	=>$wallet['id_wallet'],

    						'po_pay_id'	=>$insId,

    						'amount'	=>$billing['tot_amt_received'],

    						'type'	    =>1,

    						'remarks'   =>($billing['remark']!='' ? $billing['remark']:NULL)

    						 );

    						$this->$model->insertData($insWallet,'ret_karigar_wallet_transcation');

    						//echo "<pre>"; print_r($metal_rate);exit;

    	 				}

    	 				else

    	 				{

    	 				   $wallet_acc=array(

        	 				        'id_karigar'    =>$billing['id_karigar'],

        	 				        'amount'        =>$billing['tot_amt_received'],

        	 				   	    'created_by'    =>$this->session->userdata('uid'),

        						    'created_on'    =>date("Y-m-d H:i:s")

    						    );

     					   $insWalletAcc= $this->$model->insertData($wallet_acc,'ret_karigar_wallet');

     					   //print_r($this->db->last_query());exit;

     					   if($insWalletAcc)

     					   {

    	 					   	$insWallet=array(

        						'id_wallet'	=>$insWalletAcc,

        						'po_pay_id'	=>$insId,

        						'amount'	=>$billing['tot_amt_received'],

        						'type'	    =>1,

        						'remarks'   =>($billing['remark']!='' ? $billing['remark']:NULL)

        						 );

        						$this->$model->insertData($insWallet,'ret_karigar_wallet_transcation');

        						//print_r($this->db->last_query());exit;

     					   }



    	 				}

                    }



                    if(sizeof($net_banking_details)>0)

                    {

                        foreach($net_banking_details as $pay)

                        {

                            $netBankingPaymentData[]=array(

                                             'pay_id'           =>$insId,

                                             'type'             =>1,

                                             'pay_mode'         =>$pay['nb_type'],

                                             'payment_amount'   =>$pay['pay_amount'],

                                             'ref_no'           =>($pay['ref_no']!='' ? $pay['ref_no']:NULL),

                                            );

                            $this->$model->insertBatchData($netBankingPaymentData,'ret_po_payment_detail');

                        }

                    }



                    if(sizeof($sales_details)>0)

                    {

                        foreach($sales_details as $pay)

                        {

                            $SalesPaymentData[]=array(

                                             'pay_id'         =>$insId,

                                             'type'           =>2,

                                             'payment_amount' =>$pay['pay_amount'],

                                             'bill_id'        =>$pay['bill_id'],

                                            );

                            $this->$model->insertBatchData($SalesPaymentData,'ret_po_payment_detail');

                        }

                    }



                    if($this->db->trans_status()===TRUE)

                    {

                        $log_data = array(

                                	'id_log'        => $this->session->userdata('id_log'),

                                	'event_date'	=> date("Y-m-d H:i:s"),

                                	'module'      	=> 'PURCHASE PAYMENT',

                                	'operation'   	=> 'insert',

                                	'record'        =>  $insId,

                                	'remark'       	=> 'Purchase Payment Successfully..'

                                );

                        $this->log_model->log_detail('insert','',$log_data);







                        $this->db->trans_commit();

                        $responseData=array('status'=>true,'message'=>'Receipt added successfully.');

                        $this->session->set_flashdata('chit_alert',array('message'=>'Receipt successfully.','class'=>'success','title'=>'Purchase Payment'));

                    }

                    else

                    {

                        echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Purchase Payment'));

                    }

                }else

                {

                        echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Purchase Payment'));



                }



                echo json_encode($responseData);

			 break;





			case 'ajax':

				$list=$this->$model->get_PurchasePaymentList($_POST);

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/halmarking_issue_receipt/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

	}

    //Purchase Payment



    //rate fixing



    function update_ratefix_approval()

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$response_data=array();

		$insert_data=$this->input->post('req_data');

		foreach ($insert_data as $value)

		{

            $Data=array(

            'rate_fix_id' => $value['rate_fix_id'],

            'is_approved' 	 => 1,

			'approved_by' 	 =>  $this->session->userdata('uid'),

            'approved_date'  => date("Y-m-d H:i:s"),

            );

            $this->db->trans_begin();

			$rate_fix_id = $this->$model->updateData($Data,'rate_fix_id',$Data['rate_fix_id'],'ret_po_rate_fix');

		}

		if($this->db->trans_status()===TRUE)

        {

		 	$this->db->trans_commit();

			 $log_data = array(

				'id_log'        => $this->session->userdata('id_log'),

				'event_date'    => date("Y-m-d H:i:s"),

				'module'        => 'Rate Fixing',

				'operation'     => 'Update',

				'record'        =>  $rate_fix_id,

				'remark'        => 'Rate Fixing Updated successfully'

				);

			$this->log_model->log_detail('insert','',$log_data);

		 	$response_data=array('status'=>TRUE,'message'=>'Approved Successfully.');

		}

		else

		{

			$this->db->trans_rollback();

			$response_data=array('status'=>FALSE,'message'=>'Unable to proceed Your Request.');

		}

		echo json_encode($response_data);

	}



    function get_rate_fixing_po_no()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_rate_fixing_po_no($_POST);

        echo json_encode($data);

    }



    function get_approvl_ratefix_po()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_approvl_ratefix_po($_POST);

        echo json_encode($data);

    }





    function rate_fixing($type="",$id="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'rate_fixing/list';

					$data['access'] = $this->$SETT_MOD->get_access('admin_ret_purchase/rate_fixing/list');

        			$this->load->view('layout/template', $data);

			break;



			case 'approval_rate_fixing':

					$data['main_content'] = self::VIEW_FOLDER.'rate_fixing/approval_rate_fixing/list';

					$data['access'] = $this->$SETT_MOD->get_access('admin_ret_purchase/rate_fixing/approval_rate_fixing');

        			$this->load->view('layout/template', $data);

			break;



			case 'approval_rate_fixing_add':

					$data['main_content'] = self::VIEW_FOLDER.'rate_fixing/approval_rate_fixing/form';

        			$this->load->view('layout/template', $data);

			break;



			case 'add':

					$ho =  $this->$model->get_headOffice();

					$data['financial_year']  = $this->$model->GetFinancialYear();

					$data['comp_details']=$this->admin_settings_model->getCompanyDetails($ho['id_branch']);

					$data['main_content'] = self::VIEW_FOLDER.'rate_fixing/form';

        			$this->load->view('layout/template', $data);

			break;

			case 'job_receipt':

				$data['rate_fix']=$this->$model->get_rate_fix_details($id);

				$data['comp_details']=$this->admin_settings_model->getCompanyDetails("");

				//echo "<pre>";print_r($data);exit;

				$html = $this->load->view(self::VIEW_FOLDER.'rate_fixing/print', $data,true);

				echo $html;exit;

			break;



			case 'rate_fixing_items':

			    $data= $this->$model->get_rate_fixing_items($_POST);

                echo json_encode($data);

			break;



		    case 'cancel':

        		$ratefix_id      = $_POST['ratefix_id'];

			    $updData=array(

                    'bill_status'       => 2,

                    'cancel_reason'     => $_POST['cancel_reason'],

                    'cancelled_on'      => date("Y-m-d H:i:s"),

                    'cancelled_by'      => $this->session->userdata('uid')

                    );

                    $this->db->trans_begin();

                    $ret_status = $this->$model->updateData($updData,'rate_fix_id',$ratefix_id,'ret_po_rate_fix');

                    if($this->db->trans_status()===TRUE)

                    {

                        $this->db->trans_commit();


						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'RATE FIXING',

							'operation'     => 'Cancel',

							'record'        =>  $ret_status,

							'remark'        => 'Rate Fixed Cancelled successfully'

							);

	                    $this->log_model->log_detail('insert','',$log_data);

                        $this->session->set_flashdata('chit_alert',array('message'=>'Ratefix Cancelled Successfully','class'=>'success','title'=>'Ratefix'));

                        $return_data=array('status'=>TRUE,'message'=>'Ratefix Cancelled Successfully..');

                    }

                    else

                    {

                        echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Ratefix'));

                        $return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

                    }

                echo json_encode($return_data);

			break;



			case 'save':

		        $item_details=$_POST['rate_fixing_items'];



		        if(!empty($item_details))

		        {

    		        foreach($item_details['po_item_id'] as $key => $val)

    		        {

    		            if($item_details['fix_weight'][$key]!='' && $item_details['fix_weight'][$key]>0)

    		            {



    		                //$order              = $this->$model->get_purchase_order_item_search($item_details['po_item_id'][$key]);



    		                $total_amount       = ($item_details['fix_weight'][$key]*$item_details['rate_per_gram'][$key]);

    	 	                $rateFixData=array(

                               'rate_fix_po_item_id'    =>$item_details['po_item_id'][$key],

                               'id_approval_ratecut'    =>(isset($item_details['id_supplier_rate_cut'][$key]) ? ($item_details['id_supplier_rate_cut'][$key]!='' ? $item_details['id_supplier_rate_cut'][$key] :NULL) :NULL),

                               'rate_fix_type'          =>$_POST['rate_fix']['rate_fix_type'],

                               'rate_fix_wt'            =>$item_details['fix_weight'][$key],

                               'rate_fix_rate'          =>$item_details['rate_per_gram'][$key],

                               'rate_fix_created_from'  =>2,

							   'remark'                  =>($_POST['rate_fix']['remark']!='' ? $_POST['rate_fix']['remark']:NULL),

							   'tax_amount'           =>($item_details['taxable_amount'][$key]!='' ? $item_details['taxable_amount'][$key]:0),

							   'tax_percentage'          =>($item_details['tax_percentage'][$key]!='' ? $item_details['tax_percentage'][$key]:0),

							   'cgst_cost'              =>($item_details['cgst'][$key]!='' ? $item_details['cgst'][$key]:0),

							   'sgst_cost'              =>($item_details['sgst'][$key]!='' ? $item_details['sgst'][$key]:0),

							   'igst_cost'               =>($item_details['igst'][$key]!='' ? $item_details['igst'][$key]:0),

                               'total_amount'           =>$item_details['payable_amt'][$key],

                               'rate_fix_create_by'     =>$this->session->userdata('uid'),

                               'rate_fix_created_on'    =>date("Y-m-d H:i:s"),

                               );

                              //echo"<pre>"; print_r($rateFixData);exit;

                              $this->db->trans_begin();

                              $insId=$this->$model->insertData($rateFixData,'ret_po_rate_fix');

    			          if($insId)

    			          {

    			             $log_data = array(

                                	'id_log'        => $this->session->userdata('id_log'),

                                	'event_date'	=> date("Y-m-d H:i:s"),

                                	'module'      	=> 'RATE FIXING',

                                	'operation'   	=> 'insert',

                                	'record'        =>  $insId,

                                	'remark'       	=> 'Rate Fixed added Successfully..'

                                );

                                $this->log_model->log_detail('insert','',$log_data);

    			          }

    		            }

    		        }

    		        if($this->db->trans_status()===TRUE)

                    {

                        $this->db->trans_commit();

                        $responseData=array('status'=>true,'message'=>'Rate Fixed successfully.');

                        $this->session->set_flashdata('chit_alert',array('message'=>'Rate Fixed successfully.','class'=>'success','title'=>'Rate Fixing'));

                    }

                    else

                    {

                        echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Rate Fixing'));

                    }

		        }else

		        {

		            $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

		        }

                echo json_encode($responseData);

			break;



			case 'approval_rate_fix_list':

			    $from_date	= $this->input->post('from_date');

			    $to_date	= $this->input->post('to_date');

				$list       = $this->$model->get_approval_rate_fix_list($from_date, $to_date);

		  	    $access     = $this->admin_settings_model->get_access('admin_ret_purchase/rate_fixing/approval_rate_fixing');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;



			default:

			   	$from_date	= $this->input->post('from_date');

			    $to_date	= $this->input->post('to_date');

				$list       = $this->$model->get_PO_Ratefix_List($from_date, $to_date);

		  	    $access     = $this->admin_settings_model->get_access('admin_ret_purchase/rate_fixing/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

	}

    //rate fixing



	public function getOrderNosBySearch(){

		$model=	self::RET_PUR_ORDER_MODEL;

		$data = $this->$model->getAvailableOrders($_POST['searchTxt'], $_POST['supplierId']);

		echo json_encode($data);

	}





	public function get_bill_details()

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$searchTxt=$this->input->post('searchTxt');

		$data=$this->$model->get_bill_details($searchTxt,$bill_cus_id);

		echo json_encode($data);

	}



	//Purchase return

    function get_supplier_sale(){
        $model =	self::RET_PUR_ORDER_MODEL;
        $data  = $this->$model->get_supplier_sale($_POST);
        echo json_encode($data);
    }


	public function returnpoitems()

	{

		$model =	self::RET_PUR_ORDER_MODEL;

		$item_details = $_POST['returnitems'];



		$item_category_details = $_POST['returnpuritems'];

		$pur_ret_ref_no = $this->$model->pur_ret_refno($_POST['purchase_type']);

		$ho              =  $this->$model->get_headOffice();

		$dCData          =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

		$bill_date       =  ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

		$fin_year       = $this->$model->get_FinancialYear();

		//echo"<pre>";print_r($_POST);exit;

		// Extract po_id from the first item's source PO (if available)
		$return_po_id = NULL;
		if(!empty($item_category_details)) {
			$first_item = reset($item_category_details);
			if(!empty($first_item['po_item_id'])) {
				$po_item = $this->db->query("SELECT po_item_po_id FROM ret_purchase_order_items WHERE po_item_id = ".(int)$first_item['po_item_id']." LIMIT 1")->row_array();
				if(!empty($po_item['po_item_po_id'])) {
					$return_po_id = $po_item['po_item_po_id'];
				}
			}
		}

		$purchasereturn = array(

			"pur_ret_supplier_id"                   => $_POST['id_karigar'],

			"bill_date"                             => $bill_date,

			"id_branch"                             =>$ho['id_branch'],

			"pur_ret_ref_no"                        => ($fin_year['fin_year_code'].'-'.$pur_ret_ref_no),

			"pur_ret_convert_to"                    => $_POST['pur_ret_convert_to'],

			"tag_issue_from"                        => $_POST['tag_issue_from'],

			"nontag_issue_from"                     => $_POST['nontag_issue_from'],

			"pur_ret_reason"                        => $_POST['returnreason'],

			"pur_ret_remark"                        => $_POST['narration'],

			"stock_type"                            => $_POST['stock_type'],

			"purchase_type"                         => $_POST['purchase_type'],

			"return_total_cost"                     => ($_POST['returnamount']!='' ? $_POST['returnamount']:0),

			"pur_ret_round_off"                     => ($_POST['returnroundoff']!='' ? ($_POST['round_off_type']==1 ? '+'.$_POST['returnroundoff'] :'-'.$_POST['returnroundoff']):0),

			"pur_ret_discount"                      => ($_POST['return_discount']!='' ? $_POST['return_discount']:0),

			"pur_ret_other_charges"                 => ($_POST['other_charges']!='' && $_POST['other_charges']!='NaN' ? $_POST['other_charges']:0),

			"pur_ret_tds_percent"                   => ($_POST['tds_percent']!='' ? $_POST['tds_percent']:0),

			"pur_ret_tds_value"                     => ($_POST['tds_tax_value']!='' ? $_POST['tds_tax_value']:0),

			"pur_ret_other_charges_tds_percent"     => ($_POST['charges_tds_percent']!='' ? $_POST['charges_tds_percent']:0),

			"pur_ret_other_charges_tds_value"       => ($_POST['other_charges_tds_tax_value']!='' ? $_POST['other_charges_tds_tax_value']:0),

			"pur_ret_tcs_percent"                   => ($_POST['tcs_percent']!='' ? $_POST['tcs_percent']:0),

			"pur_ret_tcs_value"                     => ($_POST['tcs_tax_value']!='' ? $_POST['tcs_tax_value']:0),

			"pur_ret_created_by"                    => $this->session->userdata('uid'),

			"po_id"                                 => $return_po_id

		);

		$this->db->trans_begin();

		$insId = $this->$model->insertData($purchasereturn,'ret_purchase_return');

		foreach($item_category_details as $key => $val)

		{

			//echo "<pre>"; print_r($val);exit;

			$purchasereturn_items = array(

				"pur_ret_id"            => $insId,

				"return_item_type"      => $val['ret_item_type'],

				"pur_ret_po_item_id"    => $val['po_item_id'],

				"id_qc_issue_details"   => $val['id_qc_issue_details'],

				"tag_id"                => ($val['tag_id']!='' ? $val['tag_id']:NULL),

				"ref_pur_ret_itm_id"    => ($val['ref_pur_ret_itm_id']!='' ? $val['ref_pur_ret_itm_id']:NULL),

				"bill_det_id"           => ($val['bill_det_id']!='' ? $val['bill_det_id']:NULL),

				"branch_trans_id"       => ($val['branch_trans_id']!='' && $val['branch_trans_id']!='undefined' ? $val['branch_trans_id']:NULL),

				'id_section'            => ($val['id_section']!='' && $val['id_section']!='undefined' ? $val['id_section']: NULL ),

				'id_product'            => $val['pro_id'],

				'id_design'             => $val['des_id'],

				'id_sub_design'         => $val['id_sub_design'],

				"pur_ret_pcs"           => $val['purretpcs'],

				"pur_ret_gwt"           => $val['purretgwt'],

				"pur_ret_lwt"           => $val['purretlwt'],

				"pur_ret_nwt"           => $val['purretnwt'],

				"pur_ret_pur_wt"        => $val['purreturnpure'],

				"pur_ret_purchase_touch"=> $val['purreturntouch'],

				"pur_ret_wastage"       => ($val['purreturnwastper']!='' ? $val['purreturnwastper']: NULL),

				"pur_ret_wastage_wt"    => ($val['purreturnwastwgt']!='' ? $val['purreturnwastwgt'] : NULL),

				"pur_ret_mc_type"       => $val['purreturnmctype'],

				"pur_ret_mc_value"      => $val['purreturnmc'],

				"pur_ret_debit_note_amt" => $val['ret_item_cost'],

				"pur_ret_tax_id"         => ($val['return_item_tax_id']!='' && $val['return_item_tax_id']!='undefined' ? $val['return_item_tax_id'] :NULL),

                "pur_ret_tax_percent"    => $val['return_item_tax_percent'],

                "pur_ret_tax_type"       => ($val['return_item_tax_type']!='' && $val['return_item_tax_type']!='undefined' ?  $val['return_item_tax_type'] : NULL),

                "total_cgst_cost"        => $val['total_cgst_cost'],

                "total_sgst_cost"        => $val['total_sgst_cost'],

                "total_igst_cost"        => $val['total_igst_cost'],

                "total_total_tax"        => $val['total_total_tax'],

                "calculation_based_on"   => $val['calculation_based_on'],

                "pure_wt_calc_type"      => $val['pure_wt_calc_type'],

                "pur_ret_rate"           => $val['pur_ret_rate'],

			);

			$insert_item_id = $this->$model->insertData($purchasereturn_items,'ret_purchase_return_items');



			if($insert_item_id)

			{

				$ledger_data = array(

					'trans_date'        =>$bill_date,

					'id_karigar'        =>$_POST['id_karigar'],

					// 'cat_id'         =>$orderDetails['id_category'][$key],

					'trans_type'        =>1,//Credit

					'trans_rec_type'    =>1,//Weight

					'trans_pcs'         =>$val['purretpcs'],

					'trans_grswt'       =>$val['purretgwt'],

					'trans_netwt'       =>$val['purretnwt'],

					'trans_purewt'      =>$val['purreturnpure'],

					'trans_screen_id'   =>4,//Supplier Bill entry

					'ref_id'            =>$insert_item_id,

					'remarks'           =>'From Purchase Return',

					'created_by'        =>$this->session->userdata('uid'),

					'created_on'        =>date("Y-m-d H:i:s"),

				);

				$this->$model->insertData($ledger_data,'ret_supplier_ledger_log');

				if($val['tag_id']!='' && $_POST['tag_issue_from']==1)

				{

					$tagDetails = $this->$model->get_tag_details($val['tag_id']);

		            $dCData = $this->admin_settings_model->getBranchDayClosingData($tagDetails['current_branch']);

		            $bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

                    $tagStatus=$this->$model->updateData(array('tag_status'=>9,'updated_by'=>$this->session->userdata('uid'),'updated_time'=>date("Y-m-d H:i:s")),'tag_id',$val['tag_id'],'ret_taging');

					if($tagStatus)

                    {

                        //Update Tag Log status

                            $tag_log=array(

                            'tag_id'	            =>$val['tag_id'],

                            'date'		            =>$bill_date,

                            'status'	            =>14,

                            'from_branch'           =>$tagDetails['current_branch'],

                            'to_branch'	            =>NULL,

                            'issuspensestock'	    =>$_POST['stock_type'],

                            'created_on'            =>date("Y-m-d H:i:s"),

                            'created_by'            =>$this->session->userdata('uid'),

                            );

                            $this->$model->insertData($tag_log,'ret_taging_status_log');

							// update in section log

							$sectag_data =array(

								'tag_id'	        => $val['tag_id'],

                        		'date'		        => $bill_date,

                        		'status'	        => 14,

                        		'from_branch'       => $tagDetails['current_branch'],

                        		'to_branch'	        => NULL,

                        		'created_on'        => date("Y-m-d H:i:s"),

                        		'created_by'        => $this->session->userdata('uid'),

                        		'issuspensestock'   => $_POST['stock_type'],

                        		'from_section'      => $tagDetails['id_section'],

                        		'to_section'        => NULL,
							);

							$this->$model->insertData($sectag_data,'ret_section_tag_status_log');


                    }

				}

				else

				{
					if($_POST['nontag_issue_from']==1)  // Nontag Available Stock .
					{

						$existData=array('id_section'=>$val['id_section'],'id_product'=>$val['pro_id'],'design'=>$val['des_id'],'id_sub_design'=>$val['id_sub_design'],'id_branch'=>$ho['id_branch']);

						//print_r($existData);exit;

						$isExist = $this->$model->checkNonTagItemExist($existData);

						if($isExist['status'] == TRUE)

						{

							$nt_data = array(

								'id_nontag_item'=>$isExist['id_nontag_item'],

								'gross_wt'		=> $val['purretgwt'],

								'net_wt'		=> $val['purretnwt'],

								'no_of_piece'   => $val['purretpcs'],

								'updated_by'	=> $this->session->userdata('uid'),

								'updated_on'	=> date('Y-m-d H:i:s'),

							);

							$this->$model->updateNTData($nt_data,'-');



							$non_tag_data=array(

								'product'	    => $val['pro_id'],

								'design'	    => $val['des_id'],

								'id_sub_design'	=> $val['id_sub_design'],

								'no_of_piece'   => $val['purretpcs'],

								'gross_wt'		=> $val['purretgwt'],

								'net_wt'		=> $val['purretnwt'],

								'from_branch'	=> $ho['id_branch'],

								'to_branch'	    => NULL,

								'ref_no'	    => $insId,

								'status'	    => 8,//Purchase Return

								'date'          => $bill_date,

								'created_on'    => date("Y-m-d H:i:s"),

								'created_by'    =>  $this->session->userdata('uid')

							);

							$this->$model->insertData($non_tag_data,'ret_nontag_item_log');


							$section_nontag_log = array(

								'product'		=> $val['pro_id'],

								'design'		=> $val['des_id'],

								'id_sub_design' => $val['id_sub_design'],

								'no_of_piece'	=> $val['purretpcs'],

								'gross_wt'		=> $val['purretgwt'],

								'net_wt'		=> $val['purretnwt'],

								"status"		=> 8,

								"from_branch"	=> $ho['id_branch'],

								"to_branch"		=> NULL,

								"from_section"	=> $val['id_section'],

								"to_section"    => NULL,

								"created_by"	=> $this->session->userdata('uid'),

								"created_on"	=> date('Y-m-d H:i:s'),

								"date"			=> $bill_date,

								);

							$this->$model->insertData($section_nontag_log,'ret_section_nontag_item_log');

						}

					}

				}



				if(!empty($val['other_metal_details']))

				{

					$othermetal_details = json_decode($val['other_metal_details'],true);

					foreach($othermetal_details as $om)

					{

						$om_data = array(

							'pur_ret_return_id'         => $insert_item_id,

							'ret_other_itm_metal_id'    => $om['id_metal'],

							'ret_other_itm_pur_id'      => $om['id_purity'],

							'ret_other_itm_grs_weight'  => $om['gwt'],

							//'ret_other_itm_cal_type'    => $om['calc_type'],

							'ret_other_itm_pcs'         => $om['pcs'],

							'ret_other_itm_rate'        => $om['rate_per_gram'],

							'ret_other_itm_amount'      => $om['amount'],



						);

						$returnomInsert = $this->$model->insertData($om_data, 'ret_purchase_return_other_metal');

						//echo $this->db->last_query();exit;

					}

				}

				if(!empty($val['stone_details']))

				{

					$stone_details = json_decode($val['stone_details'],true);

					foreach($stone_details as $om)

					{

						$stone_data = array(

							'pur_ret_return_id'         => $insert_item_id,

							'ret_stone_id'              => $om['stone_id'],

							'ret_stone_pcs'             => $om['stone_pcs'],

							'ret_stone_wt'              => $om['stone_wt'],

							'ret_stone_uom'             => $om['stone_uom_id'],

							'ret_stone_calc_based_on'   => $om['stone_calc_typ'],

							'ret_stone_rate'            => $om['stone_rate'],

							'ret_stone_amount'          => $om['stone_price'],

							'po_st_id'                  => $om['po_st_id'],




						);

						$returnstoneInsert = $this->$model->insertData($stone_data, 'ret_purchase_return_stone_items');

						//echo $this->db->last_query();exit;

					}

				}



				if(!empty($val['other_charges_details']))

				{

					$othercharge_details = json_decode($val['other_charges_details'],true);

					foreach($othercharge_details as $ch)

					{

						$charge_data = array(

							'pur_ret_itm_id'        => $insert_item_id,

							'pur_ret_charge_id'     => $ch['charge_id'],

							'pur_ret_charge_value'  => $ch['charge_value'],

							'tax_percentage'        => $ch['tax_percentage'],

							'item_total_tax'        => $ch['item_total_tax'],

							'cgst_cost'             => $ch['cgst_cost'],

							'sgst_cost'             => $ch['sgst_cost'],

							'igst_cost'             => $ch['igst_cost'],

							'total_charge_value'    => $ch['total_charge_value'],

						);

						$returnchargegsInsert = $this->$model->insertData($charge_data, 'ret_purchase_return_other_charges');

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

				'module'        => 'Purchase Return',

				'operation'     => 'Add',

				'record'        =>  $insId,

				'remark'        => 'Purchase Return Added successfully'

				);

	        $this->log_model->log_detail('insert','',$log_data);

			$responseData=array('success'=>true,'message'=>'Return data updated successfully.','return_id'=>$insId);



		}

		else

		{

			echo $this->db->last_query();exit;

			$this->db->trans_rollback();

			$responseData=array('success' => false,'message'=>'Unable to proceed the requested operation.');

		}

		echo json_encode($responseData);

	}



    function purchasereturn($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$set_model = "admin_settings_model";

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'purchasereturn/list';
					$data['access'] = $this->$set_model->get_access('admin_ret_purchase/purchasereturn/list');        			
					$this->load->view('layout/template', $data);

			break;

			case 'add':

					$data['financial_year']  = $this->$model->GetFinancialYear();

			        $data['comp_details']   = $this->$set_model->get_company();

					$data['enable_pur_return_sales_return'] = $this->$model->get_ret_settings('enable_pur_return_sales_return');

					$data['main_content'] = self::VIEW_FOLDER.'purchasereturn/form';

        			$this->load->view('layout/template', $data);

			break;



			case 'cancel_ret_entry':

			    $ho              =  $this->$model->get_headOffice();

        		$dCData          =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

        		$bill_date       =  ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

				//echo"<pre>";print_r($_POST);exit;

			    $pur_return_id = $_POST['pur_return_id'];

			    $cancel_reason = $_POST['cancel_reason'];

				$tag_issue_from = $_POST['tag_issue_from'];

				$nontag_issue_from = $_POST['nontag_issue_from'];

			    $updData=array(

                    'bill_status'       => 2,

                    'cancel_reason'     => $_POST['cancel_reason'],

                    'cancelled_date'    => $bill_date,

                    'cancelled_by'      => $this->session->userdata('uid')

                    );

                    $this->db->trans_begin();

                    $ret_status = $this->$model->updateData($updData,'pur_return_id',$pur_return_id,'ret_purchase_return');

                    if($ret_status)

                    {

                        $non_tag_details = $this->$model->get_return_non_tag_details($pur_return_id);

                        $ret_tag_details = $this->$model->get_return_tag_details($pur_return_id);

                        if(sizeof(($non_tag_details) > 0) && $nontag_issue_from==1)

                        {

                            foreach($non_tag_details as $non_tag)

                            {

                                $existData=array('id_product'=>$non_tag['id_product'],'design'=>$non_tag['id_design'],'id_sub_design'=>$non_tag['id_sub_design'],'id_branch'=>$ho['id_branch'],'id_section'=>$non_tag['id_section']);



                                	$isExist = $this->$model->checkNonTagItemExist($existData);



                    				if($isExist['status'] == TRUE)

                			        {

                			            $nt_data = array(

                                        'id_nontag_item'=>$isExist['id_nontag_item'],

                                        'gross_wt'		=> $non_tag['pur_ret_gwt'],

                                        'net_wt'		=> $non_tag['pur_ret_gwt'],

                                        'no_of_piece'   => $non_tag['pur_ret_pcs'],

                                        'updated_by'	=> $this->session->userdata('uid'),

                                        'updated_on'	=> date('Y-m-d H:i:s'),

                                        );

                                        $this->$model->updateNTData($nt_data,'+');



                                        $non_tag_data=array(

                                        'product'	    => $non_tag['id_product'],

                                        'design'	    => $non_tag['id_design'],

                                        'id_sub_design'	=> $non_tag['id_sub_design'],

                                        'no_of_piece'   => $non_tag['pur_ret_pcs'],

                                        'gross_wt'		=> $non_tag['pur_ret_gwt'],

                                        'net_wt'		=> $non_tag['pur_ret_gwt'],

                                        'to_branch'	    => $ho['id_branch'],

                                        'from_branch'	=> NULL,

                                        'ref_no'	    => $insId,

                                        'status'	    => 9,//Purchase Return Cancel

                                        'date'          => $bill_date,

                                        'created_on'    => date("Y-m-d H:i:s"),

                                        'created_by'    =>  $this->session->userdata('uid')

                                        );

                                        $this->$model->insertData($non_tag_data,'ret_nontag_item_log');

                			        }

                            }

                        }

                        if(sizeof(($ret_tag_details) > 0) && $tag_issue_from==1)

                        {

                            foreach($ret_tag_details as $val)

                            {

                                $tagStatus=$this->$model->updateData(array('tag_status'=>0,'updated_by'=>$this->session->userdata('uid'),'updated_time'=>date("Y-m-d H:i:s")),'tag_id',$val['tag_id'],'ret_taging');

                                $tag_log=array(

                                'tag_id'	  =>$val['tag_id'],

                                'date'		  =>$bill_date,

                                'status'	  =>0,

                                'from_branch' =>NULL,

                                'to_branch'	  =>$ho['id_branch'],

                                'created_on'  =>date("Y-m-d H:i:s"),

                                'created_by'  =>$this->session->userdata('uid'),

                                );

                                $this->$model->insertData($tag_log,'ret_taging_status_log');

                            }

                        }

                    }

                    if($this->db->trans_status()===TRUE)

                    {

                        $this->db->trans_commit();

						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'Purchase Return Entry',

							'operation'     => 'Cancel',

							'record'        => $ret_status,

							'remark'        => 'Purchase Return Entry Cancelled successfully'

							);

	                    $this->log_model->log_detail('insert','',$log_data);

                        $this->session->set_flashdata('chit_alert',array('message'=>'Purchase Return Entry Cancelled Successfully','class'=>'success','title'=>'Purchase Return'));

                        $return_data=array('status'=>TRUE,'message'=>'Order Instructions Added Successfully..');

                    }

                    else

                    {

                        echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Purchase Return'));

                        $return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

                    }

                echo json_encode($return_data);

			break;



			case 'ajax':

				$profile=$this->$model->get_profile_settings($this->session->userdata('profile'));

				$list=$this->$model->getReturnedRequestList($_POST);

				$access = $this->$set_model->get_access('admin_ret_purchase/purchasereturn/list');
		        $data = array(

		        					'list'  => $list,

									'profile' =>$profile,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

	}

	public function getreturn_po_list(){

		$model=	self::RET_PUR_ORDER_MODEL;

		$data = $this->$model->getRejectedPos($_POST);

		echo json_encode($data);

	}

	public function get_qc_faild_items_by_poid(){

		$model =	self::RET_PUR_ORDER_MODEL;

		$data  = $this->$model->getRejectedItemsByPoId($_POST['porefid'],$_POST['purchase_type'],$_POST['fin_year']);

		echo json_encode($data);

	}

	public function get_qc_faild_items_by_supid(){

		$model =	self::RET_PUR_ORDER_MODEL;

		$data  = $this->$model->getRejectedItemsBySupId($_POST['supid']);

		echo json_encode($data);

	}



	public function updateporeturnitems(){



		$model =	self::RET_PUR_ORDER_MODEL;

		$item_details = $_POST['returnitems'];

		$item_category_details = $_POST['returncatitems'];

		$nontagreturnitemlist = $_POST['nontagreturnitemlist'];

		$returntaggeditemlist = $_POST['returntaggeditemlist'];

		$purchasereturnitemlist = $_POST['purchasereturnitemlist'];



		$pur_ret_ref_no = $this->$model->pur_ret_refno();

		$ho              =  $this->$model->get_headOffice();

		$dCData          =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

		$bill_date       =  ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

		$fin_year       = $this->$model->get_FinancialYear();

		$purchasereturn = array(

		                    "pur_ret_supplier_id"                   => $_POST['id_karigar'],

		                    "bill_date"                             => $bill_date,

		                    "pur_ret_ref_no"                        => ($fin_year['fin_year_code'].'-'.$pur_ret_ref_no),

							"pur_ret_reason"                        => $_POST['returnreason'],

							"pur_ret_remark"                        => $_POST['narration'],

							"stock_type"                            => $_POST['stock_type'],

							"return_total_cost"                     => ($_POST['returnamount']!='' ? $_POST['returnamount']:0),

							"pur_ret_round_off"                     => ($_POST['returnroundoff']!='' ? ($_POST['round_off_type']==1 ? '+'.$_POST['returnroundoff'] :'-'.$_POST['returnroundoff']):0),

							"pur_ret_discount"                      => ($_POST['return_discount']!='' ? $_POST['return_discount']:0),

							"pur_ret_other_charges"                 => ($_POST['other_charges']!='' ? $_POST['other_charges']:0),

							"pur_ret_tds_percent"                   => ($_POST['tds_percent']!='' ? $_POST['tds_percent']:0),

							"pur_ret_tds_value"                     => ($_POST['tds_tax_value']!='' ? $_POST['tds_tax_value']:0),

							"pur_ret_other_charges_tds_percent"     => ($_POST['charges_tds_percent']!='' ? $_POST['charges_tds_percent']:0),

							"pur_ret_other_charges_tds_value"       => ($_POST['other_charges_tds_tax_value']!='' ? $_POST['other_charges_tds_tax_value']:0),

							"pur_ret_tcs_percent"                   => ($_POST['tcs_percent']!='' ? $_POST['tcs_percent']:0),

							"pur_ret_tcs_value"                     => ($_POST['tcs_tax_value']!='' ? $_POST['tcs_tax_value']:0),

							"pur_ret_created_by"                    => $this->session->userdata('uid')

						);

		$this->db->trans_begin();

        $insId = $this->$model->insertData($purchasereturn,'ret_purchase_return');



		foreach($item_category_details as $key => $val)

		{

    			$purchasereturn_items = array(

    							"pur_ret_id"         => $insId,

    							"pur_ret_cat_id"     => $val['cat_id'],

    							"pur_ret_cat_pcs"    => $val['cat_pcs'],

    							"pur_ret_cat_gwt"    => $val['cat_gwt'],

    							"pur_ret_cat_leswt"    => $val['cat_lwt'],

    							"pur_ret_cat_netwt"  => $val['cat_nwt'],

    							"pur_ret_rate"       => $val['pur_ret_rate'],

    							"purreturncaltype"   => $val['purreturncaltype'],

    							"pur_ret_item_cost"  => $val['ret_item_cost'],

    							"pur_ret_tax_rate"   => $val['ret_tax_rate'],

    							"pur_ret_tax_value"  => $val['ret_tax_value'],

    							"pur_ret_cgst"       => $val['ret_cgst_value'],

    							"pur_ret_sgst"       => $val['ret_sgst_value'],

    							"pur_ret_igst"       => $val['ret_igst_value'],

    						);

    		    $insert_item_id = $this->$model->insertData($purchasereturn_items,'ret_purchase_return_cat_items');

    		    if($insert_item_id)

    		    {



        		    if(!empty($val['other_metal_details'])){

                    	$othermetal_details = json_decode($val['other_metal_details'],true);

                    	foreach($othermetal_details as $om)

                    	{

                    		$om_data = array(

                    			'pur_ret_return_id'         => $insert_item_id,

                    			'ret_other_itm_metal_id'    => $om['id_metal'],

                    			'ret_other_itm_pur_id'      => $om['id_purity'],

                    			'ret_other_itm_grs_weight'  => $om['gwt'],

                    			//'ret_other_itm_cal_type'    => $om['calc_type'],

                    			'ret_other_itm_pcs'         => $om['pcs'],

                    			'ret_other_itm_rate'        => $om['rate_per_gram'],

                    			'ret_other_itm_amount'      => $om['amount'],



                    		);

                    		$returnomInsert = $this->$model->insertData($om_data, 'ret_purchase_return_other_metal');

                    		//echo $this->db->last_query();exit;

                    	}

                    }

                    if(!empty($val['stone_details'])){

                    	$stone_details = json_decode($val['stone_details'],true);

                    	foreach($stone_details as $om)

                    	{

                    		$stone_data = array(

                    			'pur_ret_return_id'         => $insert_item_id,

                    			'ret_stone_id'              => $om['stone_id'],

                    			'ret_stone_pcs'             => $om['stone_pcs'],

                    			'ret_stone_wt'              => $om['stone_wt'],

                    			'ret_stone_uom'             => $om['stone_uom_id'],

                    			'ret_stone_calc_based_on'   => $om['stone_cal_type'],

                    			'ret_stone_rate'            => $om['stone_rate'],

                    			'ret_stone_amount'          => $om['stone_price'],



                    		);

                    		$returnstoneInsert = $this->$model->insertData($stone_data, 'ret_purchase_return_stone_items');

                    		//echo $this->db->last_query();exit;

                    	}

                    }

		        }

    	}



    	foreach($returntaggeditemlist as $key => $val)

	    {

	        $purchasereturn_tag_items = array(

				"pur_ret_id"            => $insId,

				"tag_id"                => $val['tag_id'],

				"return_item_type"      => 2,

				"pur_ret_pcs"           => $val['piece'],

				"pur_ret_gwt"           => $val['gross_wt'],

				"pur_ret_nwt"           => $val['net_wt'],

			);

			$this->$model->insertData($purchasereturn_tag_items,'ret_purchase_return_items');

			if($val['tag_id']!='')

		    {

		            $tagDetails = $this->$model->get_tag_details($val['tag_id']);

		            $dCData = $this->admin_settings_model->getBranchDayClosingData($tagDetails['current_branch']);

		            $bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

                    $tagStatus=$this->$model->updateData(array('tag_status'=>9,'updated_by'=>$this->session->userdata('uid'),'updated_time'=>date("Y-m-d H:i:s")),'tag_id',$val['tag_id'],'ret_taging');

                    if($tagStatus)

                    {

                        //Update Tag Log status

                            $tag_log=array(

                            'tag_id'	            =>$val['tag_id'],

                            'date'		            =>$bill_date,

                            'status'	            =>14,

                            'from_branch'           =>$tagDetails['current_branch'],

                            'to_branch'	            =>NULL,

                            'issuspensestock'	    =>$_POST['stock_type'],

                            'created_on'            =>date("Y-m-d H:i:s"),

                            'created_by'            =>$this->session->userdata('uid'),

                            );

                            $this->$model->insertData($tag_log,'ret_taging_status_log');




                    }

		    }

	    }

	    foreach($nontagreturnitemlist as $key => $val)

	    {

	        $purchasereturn_nontag_items = array(

				"pur_ret_id"            => $insId,

				"return_item_type"      => 3,

				'id_product'            => $val['id_product'],

				'id_design'             => $val['id_design'],

				'id_sub_design'         => $val['id_sub_design'],

				"pur_ret_pcs"           => $val['piece'],

				"pur_ret_gwt"           => $val['gross_wt'],

				"pur_ret_lwt"           => 0,

				"pur_ret_nwt"           => $val['gross_wt'],

			);

			$this->$model->insertData($purchasereturn_nontag_items,'ret_purchase_return_items');



            $existData=array('id_product'=>$val['id_product'],'design'=>$val['id_design'],'id_sub_design'=>$val['id_sub_design'],'id_branch'=>$ho['id_branch']);



			$isExist = $this->$model->checkNonTagItemExist($existData);



			if($isExist['status'] == TRUE)

	        {

	            $nt_data = array(

                'id_nontag_item'=>$isExist['id_nontag_item'],

                'gross_wt'		=> $val['gross_wt'],

                'net_wt'		=> $val['gross_wt'],

                'no_of_piece'   => $val['piece'],

                'updated_by'	=> $this->session->userdata('uid'),

                'updated_on'	=> date('Y-m-d H:i:s'),

                );

                $this->$model->updateNTData($nt_data,'-');



                $non_tag_data=array(

                'product'	    => $val['id_product'],

                'design'	    => $val['id_design'],

                'id_sub_design'	=> $val['id_sub_design'],

                'no_of_piece'   => $val['piece'],

                'gross_wt'		=> $val['gross_wt'],

                'net_wt'		=> $val['gross_wt'],

                'from_branch'	=> $ho['id_branch'],

                'to_branch'	    => NULL,

                'ref_no'	    => $insId,

                'status'	    => 8,//Purchase Return

                'date'          => $bill_date,

                'created_on'    => date("Y-m-d H:i:s"),

                'created_by'    =>  $this->session->userdata('uid')

                );

                $this->$model->insertData($non_tag_data,'ret_nontag_item_log');

	        }

	    }

	    foreach($purchasereturnitemlist as $val)

	    {

	        $purchasereturn_po_items = array(

				"pur_ret_id"            => $insId,

				"pur_ret_po_item_id"    => $val['po_item_id'],

				"return_item_type"      => 1,

				"pur_ret_pcs"           => $val['piece'],

				"pur_ret_gwt"           => $val['gross_wt'],

				"pur_ret_pur_wt"        => $val['gross_wt'],

			);

			$this->$model->insertData($purchasereturn_po_items,'ret_purchase_return_items');

	    }



		if(!empty($_POST['other_charges_details'])){

        	$charge_details = json_decode($_POST['other_charges_details'],true);

        	foreach($charge_details as $charges)

        	{



        	    $cgst_cost = 0;

        	    $sgst_cost = 0;

        	    $igst_cost = 0;

        	    $total_tax = number_format(($charges['charge_tax_value']),2,'.','');

        	    if($addData['cmp_country']==$addData['supplier_country'])

        	    {

        	        if($addData['cmp_state']==$addData['supplier_state'])

        	        {

        	            $cgst_cost = ($total_tax/2);

        	            $sgst_cost = ($total_tax/2);

        	        }

        	        else

        	        {

        	            $igst_cost = $total_tax;

        	        }

        	    }

        	    else

        	    {

        	        $cgst_cost = ($total_tax/2);

        	        $sgst_cost = ($total_tax/2);

        	    }



        		$charge_data = array(

        			'pur_ret_id'                => $insId,

        			'pur_ret_charge_id'         => $charges['charge_id'],

        			'pur_ret_charge_value'      => $charges['charge_value'],

        			'pur_ret_charge_tax'        => $charges['charge_tax'],

        			'pur_ret_charge_tax_value'  => $charges['charge_tax_value'],

        			'cgst_cost'                 => $cgst_cost,

        			'sgst_cost'                 => $sgst_cost,

        			'igst_cost'                 => $igst_cost,

        		);

        		$returnchargegsInsert = $this->$model->insertData($charge_data, 'ret_purchase_return_other_charges');

        		//echo $this->db->last_query();exit;

        	}

        }







		if($this->db->trans_status()===TRUE)

		{

			$this->db->trans_commit();

			$responseData=array('success'=>true,'message'=>'Return data updated successfully.','return_id'=>$insId);



		}

		else

		{

			//echo $this->db->last_query();exit;

			$this->db->trans_rollback();

			$responseData=array('success' => false,'message'=>'Unable to proceed the requested operation.');

		}

		echo json_encode($responseData);



	}



	public function return_receipt_acknowladgement($id, $Print_Type = "")

    {

        $model =    self::RET_PUR_ORDER_MODEL;

        $set_model                      = "admin_settings_model";

        $data['comp_details']           = $this->$set_model->get_company(1);

        $data['issue']                  = $this->$model->getReturnReceipt($id);

        $data['receipt_details']        = $this->$model->getReturnReceiptDetails($id);

        $data['gst_details']            = $this->$model->getReturnReceiptGSTDetails($id);

        $data['item_details']          = $this->$model->get_purchaseReturn_item_details($id);

        $data['type'] = $Print_Type; // 1-summary , 2-Detailed

        //echo "<pre>"; print_r($data);exit;

        $this->load->helper(array('dompdf', 'file'));

        $dompdf = new DOMPDF();

        /*if ($data['type'] == 1) {

            // $html = $this->load->view('ret_purchase/purchasereturn/vendor_ack', $data, true);

             $html = $this->load->view(self::VIEW_FOLDER . 'purchasereturn/vendor_ack', $data, true);

        } else {

            $html = $this->load->view('ret_purchase/purchasereturn/vendor_ack_detailed', $data, true);

        }*/



        $html = $this->load->view(self::VIEW_FOLDER . 'purchasereturn/vendor_ack', $data, true);



        echo $html;

        // $dompdf->load_html($html);

        // $dompdf->set_paper('A4', "portriat");

        // $dompdf->render();

        // $dompdf->stream("VendorAck.pdf", array('Attachment' => 0));

    }

    //Purchase return





    //Order cancel and close

    function update_order_close()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $req_data   =$_POST['req_data'];



        foreach($req_data as $items)

	    {

	       $this->db->trans_begin();



	       $this->$model->updateData(array('order_status'=>7,'closed_date'=>date("Y-m-d H:i:s"),'closed_by'=>$this->session->userdata('uid')),'id_customerorder',$items['id_customerorder'],'customerorder');

	    }

    	    if($this->db->trans_status()===TRUE)

            {

                $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'Order Closed',

                        	'operation'   	=> 'Update',

                        	'record'        =>  NULL,

                        	'remark'       	=> 'Order Closed.'

                        );

                $this->log_model->log_detail('insert','',$log_data);



                $this->db->trans_commit();

                $responseData=array('status'=>true,'message'=>'Order Closed successfully.');

            }

            else

            {

                $this->db->trans_rollback();

                $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

            }

            echo json_encode($responseData);

	}

	function update_order_cancel()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $req_data = $_POST['req_data'];


        foreach($req_data as $items)

	    {

	       	$this->db->trans_begin();


	       	$this->$model->updateData(array('order_status'=>6,'cancelled_date'=>date("Y-m-d H:i:s"),'cancelled_by'=>$this->session->userdata('uid')),'id_customerorder',$items['id_customerorder'],'customerorder');

		  	$this->$model->updateData(array('orderstatus'=> 0),'id_customerorder',$items['cus_ord_ref'],'customerorderdetails');

		 	$this->$model->updateData(array('orderstatus'=>6,'id_karigar'=>NULL),'id_customerorder',$items['id_customerorder'],'customerorderdetails');

			// Reset order_cart items linked to this PO back to pending (orderstatus=0) for reassignment
		 	$po_details = $this->db->query("SELECT id_orderdetails FROM customerorderdetails WHERE id_customerorder = ".$items['id_customerorder'])->result_array();
		 	foreach($po_details as $det) {
		 		if(!empty($det['id_orderdetails'])) {
		 			$this->db->where('id_orderdetails', $det['id_orderdetails']);
		 			$this->db->update('order_cart', array('orderstatus' => 0, 'id_orderdetails' => NULL));
		 		}
		 	}

	    }


    	    if($this->db->trans_status()===TRUE)

            {

                $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'Order Closed',

                        	'operation'   	=> 'Update',

                        	'record'        =>  NULL,

                        	'remark'       	=> 'Order Cancelled.'

                        );

                $this->log_model->log_detail('insert','',$log_data);



                $this->db->trans_commit();

                $responseData=array('status'=>true,'message'=>'Order Cancelled successfully.');

            }

            else

            {

                $this->db->trans_rollback();

                $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

            }

            echo json_encode($responseData);

	}



    //Order cancel and close





    //Order Delivery

    function get_karigar_pending_orders()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_karigar_pending_orders($_POST);

        echo json_encode($data);

    }



    function get_karigar_pending_order_details()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_purchase_order_pending_details($_POST);

        echo json_encode($data);

    }



    function update_order_delivery()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $req_data   =$_POST['req_data'];

        foreach($req_data as $items)

	    {

	       $this->db->trans_begin();

	       if($items['order_pcs']>($items['delivered_pcs']+$items['tot_delivered_pcs']))

	       {

                $orderData=array(

                    'delivered_qty'     =>$items['delivered_pcs'],

                    'delivered_wt'      =>($items['delivered_wt']!='' ? $items['delivered_wt']:0),

                    'id_orderdetails'   =>$items['id_orderdetails'],

                    'is_partial_delivery'=>1

                    );

                $status=$this->$model->update_partial_order_delivery($orderData,'+');

                //print_r($this->db->last_query());exit;

                if($status)

                {

                    $insData=array(

                                  'id_joborder' =>$items['id_joborder'],

                                  'delivered_qty'=>$items['delivered_pcs'],

                                  'delivered_date'=>date("Y-m-d H:i:s")

                                  );

                    $insOrder = $this->$model->insertData($insData,'joborder_partial_delivery');



                }

	       }

	       else if($items['order_pcs']<=$items['delivered_pcs']+$items['tot_delivered_pcs'])

	       {

	           $updData=array(

	                          'delivered_date'=>date("Y-m-d H:i:s"),

	                          'orderstatus'   =>5,

	                          'delivered_by'  =>$this->session->userdata('uid'),

	                         );



	           $this->$model->updateData($updData,'id_orderdetails',$items['id_orderdetails'],'customerorderdetails');



	            $orderData=array(

                    'delivered_qty'     =>$items['delivered_pcs'],

                    'delivered_wt'      =>$items['delivered_wt'],

                    'id_orderdetails'   =>$items['id_orderdetails'],

                    );

                $status=$this->$model->update_order_delivery($orderData,'+');



	       }

	    }

    	    if($this->db->trans_status()===TRUE)

            {



                $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'Order Delivery',

                        	'operation'   	=> 'Update',

                        	'record'        =>  NULL,

                        	'remark'       	=> 'Order Delivered.'

                        );

                $this->log_model->log_detail('insert','',$log_data);



                $this->db->trans_commit();

                $responseData=array('status'=>true,'message'=>'Order Delivered successfully.');

            }

            else

            {

                echo $this->db->last_query();exit;

                $this->db->trans_rollback();

                $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

            }

            echo json_encode($responseData);

	}



    //Order Delivery



    //Metal Issue



    public function karigarmetalissue_acknowladgement($id)

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$set_model = "admin_settings_model";

		$data['comp_details'] = $this->$set_model->get_company(1);

		$data['issue']        = $this->$model->getMetalIssue($id);

		$data['issue_details']   = $this->$model->getMetalIssueDetails($id);

		$data['type']=$type;

		// echo "<pre>"; print_r($data);exit;

		$this->load->helper(array('dompdf', 'file'));

	        $dompdf = new DOMPDF();

			$html = $this->load->view('ret_purchase/metalissue/vendor_ack', $data,true);

			$dompdf->load_html($html);

			$dompdf->set_paper('A4', "portriat" );

			$dompdf->render();

			while(ob_get_level()) ob_end_clean();
			$dompdf->stream("VendorAck.pdf",array('Attachment'=>0));

	}



  function karigarmetalissue($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;

		switch($type)

		{
			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'metalissue/list';

					$data['access'] = $this->$SETT_MOD->get_access('admin_ret_purchase/karigarmetalissue/list');

        			$this->load->view('layout/template', $data);

			break;

			case 'add':
					$data['financial_year']  = $this->$model->GetFinancialYear();

                    $data['is_supplierbill_entry_req']      = $this->$model->get_ret_settings('is_supplierbill_entry_req');

					$data['is_against_po_bills']      = $this->$model->get_ret_settings('issue_against_po_bills');

					$data['main_content'] = self::VIEW_FOLDER.'metalissue/form';

        			$this->load->view('layout/template', $data);

			break;


			case 'available_stock_details':

				    $data=$this->$model->get_available_stock_details($_POST);

				    echo json_encode($data);

			break;

			case 'available_metal_opening_stock_details':
				$data=$this->$model->get_opening_metal_stock_list($_POST);
				echo json_encode($data);
		break;

			case 'get_approval_po_bills':
					$data = $this->$model->get_approval_po_bills($_POST);
					echo json_encode($data);
			break;

			case 'search_po':
					$data = $this->$model->get_po_details_by_ref_no($_POST);
					echo json_encode($data);
			break;


			case'save':

			        $responseData = array();

			       // echo "<pre>";print_r($_POST);exit;

			        $addData         =  $_POST['issue'];

			        $ref_no          =  $this->$model->get_metal_issue_ref_no();

			        $ho              =  $this->$model->get_headOffice();

                    $dCData          =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

				    $bill_date       =  ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

					$fin_year       = $this->$model->get_FinancialYear();



					$insData=array(

						'met_issue_karid'     =>$_POST['id_karigar'],

						'id_branch'           =>$ho['id_branch'],

						'issue_aganist'       =>$_POST['is_against_order'],

						'is_against_opening'  =>$_POST['is_against_opening'],

						'issue_from'          =>  ( $_POST['is_stock_repair_order'] == 1 ?  1 : $_POST['issue_from'] ),

						'tag_issue_from'      =>$_POST['tag_issue_from'],

						'nontag_issue_from'   =>$_POST['nontag_issue_from'],

						'issue_against_po'    =>($_POST['issue_against_po']!=''  &&  $_POST['is_stock_repair_order'] !=1 ? $_POST['issue_against_po']:0 ),

						'metalissue_type'    =>($_POST['metal_issue_type']!='' ? $_POST['metal_issue_type'] :0),//1-Normal Issue,2-Approval Issue

						'id_order'            =>(!empty($_POST['id_order']) ? $_POST['id_order']:NULL),

						'met_issue_ref_id'    =>($fin_year['fin_year_code'].'-'.$ref_no),

						'met_issue_date'      =>$bill_date,

						'remark'              => $_POST['remark'],

						'met_issue_created_on'=>date("Y-m-d H:i:s"),

						'met_issue_created_by'=>$this->session->userdata('uid'),

						'po_id'				  =>$_POST['against_po'],

						);

			        $this->db->trans_begin();

			        $insId = $this->$model->insertData($insData,'ret_karigar_metal_issue');

			        if($insId)

			        {

						$metal_details = $_POST['issued_items'];

						foreach($metal_details as $key => $val)

			            {

			                //$purity=$this->$model->get_metal_issue_purity($metal_details['purity'][$key]);

			                $metal_data=array(

								'issue_met_parent_id'      =>$insId,

								'issue_metal'              =>$val['metal'],

								'issue_cat_id'             =>$val['category'],

								'issue_pur_id'             =>$val['purity'],

								'issu_met_pro_id'          =>$val['id_product'],

								'issu_met_id_design'       =>$val['id_design'],

								'issu_met_id_sub_design'   =>$val['id_sub_design'],

								'issue_pcs'                =>$val['pcs'],

								'issue_metal_wt'           =>$val['weight'],

								'issue_uom_id'             =>$val['uom_id'],

								'issue_metal_pur_wt'       =>$val['pur_weight'],

								'issu_met_id_section'      =>$val['id_section'],

								'po_item_id'               =>($val['po_item_id']!='' ? $val['po_item_id'] : NULL),

								'id_smith_company_op_balance' =>($val['id_smith_company_op_balance']!='' ? $val['id_smith_company_op_balance'] : NULL),

								'tag_id'                   =>($val['tag_id']!='' ? $val['tag_id'] : NULL),

								'bill_det_id'              =>($val['bill_det_id']!='' ? $val['bill_det_id'] : NULL),

								'branch_trans_id'          =>($val['branch_trans_id']!='' ? $val['branch_trans_id'] : NULL),
												
								'touch'             		=>$val['touch'],

							);

							if($val['is_repair_item']==1){

								$metal_data['mc']=$val['mc'];
								$metal_data['mc_type']=$val['mc_type'];
								$metal_data['wastage']=$val['issue_va'];
								$metal_data['touch']=$val['issue_touch'];
								$metal_data['calc_type']=$val['calc_type'];
								$metal_data['net_wt']=$val['issue_net_wt'];
								$metal_data['less_wt']=$val['issue_less_wt'];
								$metal_data['is_repair_item']=$val['is_repair_item'];
								

							}

			               $status=$this->$model->insertData($metal_data,'ret_karigar_metal_issue_details');

						  // $stones=[];

						   $stone_details = json_decode($val['stone_details']);
   
						  if(sizeof($stone_details)>0)
   
						  {
   
							  
   
							  foreach($stone_details as $stone)
   
							  {
   
								  $stones=array(
   
									  'issue_met_id'=>$status,
   
									  'stone_pcs'	     => $stone->stone_pcs,
   
									  'stone_wt'		=> $stone->stone_wt,
   
									  'uom_id'		=> $stone->stone_uom_id,
   
									  'stone_id'		=> $stone->stone_id,
   
									  'stone_quality_id'=> $stone->stone_quality_id,
   
									  'is_apply_in_lwt '=> $stone->show_in_lwt,
   
									  'stone_cal_type  '=> $stone->stone_cal_type,
   
									  'rate_per_gram'=> $stone->stone_rate,
   
									  'price'=> $stone->stone_price,
   
								  );
   
								//   echo "<pre>";print_r($stones);exit;
   
								 $this->$model->insertData($stones,'ret_karigar_metal_issue_stone_details');
   
							  }
   
   
   
						  }


						   	if(($status && $_POST['issue_from'] == 1) && ($_POST['tag_issue_from']==1 && $_POST['issue_against_po']!=1) || ($status && $_POST['is_stock_repair_order']==1))  // Issue against Tag
							{
								$tagDetails = $this->$model->get_tag_details($val['tag_id']);

								$dCData = $this->admin_settings_model->getBranchDayClosingData($tagDetails['current_branch']);

								$bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

								$tagStatus=$this->$model->updateData(array('tag_status'=>17,'updated_by'=>$this->session->userdata('uid'),	'updated_time'=>date("Y-m-d H:i:s")),'tag_id',$val['tag_id'],'ret_taging');

								if($tagStatus)
								{
									//Update Tag Log status
									$tag_log=array(
										'tag_id'	            =>$val['tag_id'],
										'date'		            =>$bill_date,
										'status'	            =>17,
										'from_branch'           =>$tagDetails['current_branch'],
										'to_branch'	            =>NULL,
										'issuspensestock'	    =>0,
										'created_on'            =>date("Y-m-d H:i:s"),
										'created_by'            =>$this->session->userdata('uid'),
									);
									$this->$model->insertData($tag_log,'ret_taging_status_log');
									// update in section log

									$sectag_data =array(
										'tag_id'	        => $val['tag_id'],
										'date'		        => $bill_date,
										'status'	        => 17,
										'from_branch'       => $tagDetails['current_branch'],
										'to_branch'	        => NULL,
										'created_on'        => date("Y-m-d H:i:s"),
										'created_by'        => $this->session->userdata('uid'),
										'issuspensestock'   => 0,
										'from_section'      => $tagDetails['id_section'],
										'to_section'        => NULL,
									);

									$this->$model->insertData($sectag_data,'ret_section_tag_status_log');
								}
							}


			               if(($status && $_POST['issue_from']==2) && ($_POST['nontag_issue_from']==1 && $_POST['issue_against_po']!=1))  // Issue against Non Tag

			               {
								$existData=array('id_section'=>$val['id_section'],'id_product'=>$val['id_product'],'design'=>$val['id_design'],'id_sub_design'=>$val['id_sub_design'],'id_branch'=>$ho['id_branch']);

    						    $isExist = $this->$model->checkNonTagItemExist($existData);

    						        if($isExist['status'] == TRUE)

    						        {

    						            $nt_data = array(

											'id_nontag_item'=>$isExist['id_nontag_item'],

											'no_of_piece'	=> $val['pcs'],

											'gross_wt'		=> $val['weight'],

											'net_wt'		=> $val['weight'],

											'updated_by'	=> $this->session->userdata('uid'),

											'updated_on'	=> date('Y-m-d H:i:s'),

                                        );

                                        $this->$model->updateNTData($nt_data,'-');



                                        $non_tag_data=array(

											'product'	    => $val['id_product'],

											'design'	    => $val['id_design'],

											'id_sub_design'	=> $val['id_sub_design'],

											'no_of_piece'	=> $val['pcs'],

											'gross_wt'		=> $val['weight'],

											'net_wt'		=> $val['weight'],

											'from_branch'	=> $ho['id_branch'],

											'to_branch'	    => NULL,

											'ref_no'	    => $insId,

											'status'	    => 7,

											'date'          => $bill_date,

											'created_on'    => date("Y-m-d H:i:s"),

											'created_by'    =>  $this->session->userdata('uid')

                                        );

                                        $this->$model->insertData($non_tag_data,'ret_nontag_item_log');



                                        $section_nontag_log = array(

											'product'		=> $val['id_product'],

                                            'design'		=> $val['id_design'],

                                            'id_sub_design' => $val['id_sub_design'],

                                            'no_of_piece'	=> $val['pcs'],

                                            'gross_wt'		=> $val['weight'],

                                            'net_wt'		=> $val['weight'],

                                            "status"		=> 7,

                                            "from_branch"	=> $ho['id_branch'],

                                            "to_branch"		=> NULL,

                                            "from_section"	=> $val['id_section'],

                                            "to_section"    => NULL,

                                            "created_by"	=> $this->session->userdata('uid'),

                                            "created_on"	=> date('Y-m-d H:i:s'),

                                            "date"			=> $bill_date,

                                            );

                                        $this->$model->insertData($section_nontag_log,'ret_section_nontag_item_log');



    						        }

			               }

			            }



			        }



			            if($this->db->trans_status()===TRUE)

                        {

                            $log_data = array(

                                    	'id_log'        => $this->session->userdata('id_log'),

                                    	'event_date'	=> date("Y-m-d H:i:s"),

                                    	'module'      	=> 'Karigar Metal Issue',

                                    	'operation'   	=> 'Add',

                                    	'record'        =>  $insId,

                                    	'remark'       	=> 'Karigar Metal Issue Added Successfully.'

                                    );

                            $this->log_model->log_detail('insert','',$log_data);



                            $this->db->trans_commit();

                            $responseData=array('status'=>true,'message'=>'Metal Issued successfully.','insId'=>$insId);

                        }

                        else

                        {

                            //echo $this->db->last_query();exit;

                            $this->db->trans_rollback();

                            $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

                        }

                        echo json_encode($responseData);



			break;



			case 'metalissue_cancel':

				$this->db->trans_begin();

				$ho   =  $this->$model->get_headOffice();

				$metal_issue_id = $_POST['metal_issue_id'];

				$this->$model->updateData(array('bill_status' => 2),'met_issue_id',$metal_issue_id,'ret_karigar_metal_issue');

				$issuedet = $this->$model->get_KarigarMetal_issue($metal_issue_id);

				foreach($issuedet as $val)

				{

					$existData=array('id_product'=>$val['pro_id'],'id_branch'=>$ho['id_branch']);

					$isExist = $this->$model->checkNonTagItemExist($existData);

					if($isExist['status'] == TRUE)

					{

						$nt_data = array(

							'id_nontag_item'=> $isExist['id_nontag_item'],

							'gross_wt'		=> $val['weight'],

							'net_wt'		=> $val['weight'],

							'no_of_piece'   =>0,

							'updated_by'	=> $this->session->userdata('uid'),

							'updated_on'	=> date('Y-m-d H:i:s'),

							);

							$id = $this->$model->updateNTData($nt_data,'+');

					}

					if($val['tag_id']!='' )

					{

					   $this->$model->updateData(array('tag_status'=>0,'updated_time'=>date("Y-m-d H:i:s"),'updated_by'=>$this->session->userdata('uid')),'tag_id',$val['tag_id'], 'ret_taging');

					   $log_data=array(

						'tag_id'	  =>$val['tag_id'],

						'date'		  =>date('Y-m-d H:i:s'),

						'status'	  =>6,

						'from_branch' =>$val['id_branch'],

						'to_branch'   =>$val['id_branch'],

						'created_on'  =>date("Y-m-d H:i:s"),

						'created_by'  =>$this->session->userdata('uid'),

						);

						$this->$model->insertData($log_data,'ret_taging_status_log'); //Update Tag lot status

						$tag_status=$this->$model->get_tag_status($val['tag_id']);

						if($tag_status['id_section']!=null && $tag_status['id_section']!='')

						{

							$secttag_log = array(

								'tag_id'	          =>$val['tag_id'],

								'date'		          =>date("Y-m-d H:i:s"),

								'status'	          =>6,

								'from_branch'         =>$val['id_branch'],

								'to_branch'           =>$val['id_branch'],

								'from_section'        =>$tag_status['id_section'],

								'to_section'          =>$tag_status['id_section'],

								'created_on'          =>date("Y-m-d H:i:s"),

								'created_by'          =>$this->session->userdata('uid'),

							);

							$this->$model->insertData($secttag_log,'ret_section_tag_status_log');

						}

					}

				}

				if( $this->db->trans_status()===TRUE)

			    {

			    	 $this->db->trans_commit();

					 $log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'Karigar Metal Issue',

						'operation'     => 'Cancel',

						'record'        => $metal_issue_id,

						'remark'        => 'Karigar Metal Issue cancelled successfully'

						);

				        $this->log_model->log_detail('insert','',$log_data);

			    	    echo json_encode(array("stattus" => true, "message" => "ISSUE cancel successfully"));

			    }else{

			         $this->db->trans_rollback();

			         echo json_encode(array("stattus" => false, "message" => "Could not cancel this issue"));

			    }

            break;



			case 'ajax':

				$list=$this->$model->getKarigarMetalIssueList();

				$profile=$this->$model->get_profile_settings($this->session->userdata('profile'));

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/karigarmetalissue/list');

		        $data = array(

		        					'list'  => $list,

									'profile' =>$profile,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

	}





	function get_karigar_pending_ordes()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $data=$this->$model->get_karigar_pending_ordes($_POST);

	    echo json_encode($data);

	}



	function get_karigar_details(){

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $data = $this->$model->get_karigar_details($_POST['karId']);

	    echo json_encode($data);

	}



	function get_OrderProducts()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $data=$this->$model->get_OrderProducts($_POST);

	    echo json_encode($data);

	}



	function get_OrderProductsDesign()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $data=$this->$model->get_OrderProductsDesign($_POST);

	    echo json_encode($data);

	}



	function get_OrderSubDesigns()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $data=$this->$model->get_OrderSubDesigns($_POST);

	    echo json_encode($data);

	}





		//GRN ENTRY

	function grnentry($type="",$id="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$ret_catalog_model = "ret_catalog_model";

		$set_model = "admin_settings_model";

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'grn_entry/list';

					$data['access'] = $this->$set_model->get_access('admin_ret_purchase/grnentry/list');


				//    print_r($data['access']);exit;


        			$this->load->view('layout/template', $data);

			break;

			case 'add':

			        $data['grn_details']['grn_type'] = 1;

			        $data['grn_details']['grn_karigar_id'] = '';

			        $data['grn_details']['grn_supplier_ref_no'] = '';

			        $data['grn_details']['grn_ewaybillno'] = '';

			        $data['grn_details']['grn_irnno'] = '';

			        $data['grn_details']['grn_ref_date'] = '';

			        $data['comp_details']   = $this->$set_model->get_company();

					$data['main_content'] = self::VIEW_FOLDER.'grn_entry/form';

        			$this->load->view('layout/template', $data);

			break;



			case 'edit':

            	$data['grn_details']    = $this->$model->get_grn_details($id);

            	$data['categories']   = $this->$ret_catalog_model->getActiveCategorymtr($_POST);

            	$data['comp_details']   = $this->$set_model->get_company();

            	//echo "<pre>";print_r($data);exit;

            	$data['main_content'] = self::VIEW_FOLDER.'grn_entry/form';

            	$this->load->view('layout/template', $data);

            break;



			case 'save':


    			$addData   = $_POST['order'];

                $order_details   = $_POST['order_details'];

				//echo "<pre>";print_r($_POST);exit;

				$p_ImgData   = json_decode(rawurldecode($addData['tag_img']));

				//print_r($p_ImgData);exit;

    			$fin_year       = $this->$model->get_FinancialYear();

    			$grn_refno      = $this->$model->generate_grn_refno($addData['grn_type']);



     			$insData = array(

     			    'grn_fin_year_code'     => $fin_year['fin_year_code'],

     			    'grn_karigar_id'        => $addData['id_karigar'],

     			    'grn_date'              => date("Y-m-d H:i:s"),

     				'grn_ref_no'            => $grn_refno,

     				'grn_supplier_ref_no'   => ($addData['po_supplier_ref_no']!='' ? $addData['po_supplier_ref_no']:NULL),

     				'grn_ref_date'          => !empty($addData['po_ref_date']) ? date('Y-m-d', strtotime($addData['po_ref_date'])):NULL,

     				'grn_type'              => $addData['grn_type'],

					'purchase_type' 		=> $this->input->post('purchase_type'),

     				'grn_ewaybillno'        => ($addData['ewaybillno']!='' ? $addData['ewaybillno']:NULL),

     				'grn_despatch_through'  => ($addData['despatch_through']!='' ? $addData['despatch_through']:NULL),

     				'grn_irnno'             => ($addData['invoice_ref_no']!='' ? $addData['invoice_ref_no']:NULL),

     				'grn_other_charges'     => ($addData['other_charges_amount']!='' ? ($addData['other_charges_amount']+$addData['other_charges_tax']):0),

     				'grn_discount'          => ($addData['discount']!='' ? $addData['discount']:0),

     				'grn_purchase_amt'      => ($addData['total_cost']!='' ? $addData['total_cost']:0),

     				'grn_round_off'         => ($addData['round_off']!='' ? ($addData['round_off_type']==1 ? '+'.$addData['round_off'] :'-'.$addData['round_off']):0),

     				'grn_tcs_percent'       => ($addData['tcs_percent']!='' ? $addData['tcs_percent']:0),

     				'grn_tcs_value'         => ($addData['tcs_tax_value']!='' ? $addData['tcs_tax_value']:0),

     				'grn_pay_tds_percent'           => ($addData['tds_percent']!='' ? $addData['tds_percent']:0),

     				'grn_pay_tds_value'             => ($addData['tds_tax_value']!='' ? $addData['tds_tax_value']:0),

     				'grn_other_charges_tds_value'   => ($addData['other_charges_tds_tax_value']!='' ? $addData['other_charges_tds_tax_value']:0),

     				'grn_other_charges_tds_percent' => ($addData['charges_tds_percent']!='' ? $addData['charges_tds_percent']:0),

     				'form_secret'                   => ($addData['form_secret']!='' ? $addData['form_secret']:NULL),

     				'remarks'                       => ($addData['remarks']!='' ? $addData['remarks']:NULL),

    				'created_on'                    => date("Y-m-d H:i:s"),

    				'created_by'                    => $this->session->userdata('uid')

    			);

    			$this->db->trans_begin();

    			$insId = $this->$model->insertData($insData,'ret_grn_entry');

			    if($insId)

			    {

					if (sizeof($p_ImgData) > 0) {
						$_FILES['precious'] = array();

						foreach ($p_ImgData as $precious) {
							// print_r($precious); // Uncomment for debugging
							$imgFile = $this->base64ToFile($precious->src);
							$_FILES['precious'][] = $imgFile;
						}

						$img_arr = array();
                          // print_r($precious);

						if (!empty($_FILES['precious'])) {
							$folder = self::IMG_PATH . "grn_entry/";

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
											'grn_id'   => $insId,
											'image'    => $img_name,
											'date_add' => date("Y-m-d H:i:s"),
										);

										$img_arr[] = $arrayimg_tag;

										$insImageId = $this->$model->insertData($arrayimg_tag, 'ret_grn_images');

										// print_r($this->db->last_query());exit;

									}
								}
							}
						}
					}

			        if($addData['grn_type']==1 || $addData['grn_type']==2 || $addData['grn_type']==4)

			        {

			            $billSale = $_POST['item'];

    			        if(!empty($billSale))

    			        {

    			            foreach($billSale['category'] as $key => $val){



                                $array_item_details = array(

                                'grn_item_grn_id'       =>$insId,

                                'grn_item_cat_id'       =>(($billSale['category'][$key]!='') ? $billSale['category'][$key]:NULL),

                                'grn_gross_wt'          =>(($billSale['gross_wt'][$key]!='') ? $billSale['gross_wt'][$key]:NULL),

                                'grn_less_wt'           =>(($billSale['less_wt'][$key]!='') ? $billSale['less_wt'][$key]:NULL),

                                'grn_net_wt'            =>(($billSale['net_wt'][$key]!='') ? $billSale['net_wt'][$key]:NULL),

                                'grn_wastage'           =>(($billSale['wastage'][$key]!='') ? $billSale['wastage'][$key]:0),

                                'grn_no_of_pcs'         =>(isset($billSale['pcs'][$key]) ? $billSale['pcs'][$key]:NULL),

                                'grn_rate_per_grm'      =>(isset($billSale['rate_per_gram'][$key]) ? $billSale['rate_per_gram'][$key]:NULL),

                                'grn_item_cost'         =>(isset($billSale['item_cost'][$key]) ? $billSale['item_cost'][$key]:NULL),

                                'itemratecaltype'       =>(isset($billSale['rate_type'][$key]) ? $billSale['rate_type'][$key]:NULL),

                                'grn_item_gst_rate'     =>(isset($billSale['item_total_tax'][$key]) ? $billSale['item_total_tax'][$key]:NULL),

                                'grn_item_gst_value'    =>number_format(($billSale['tax_percentage'][$key]),2,'.',''),

                                'grn_item_cgst'         =>number_format(($billSale['item_cgst'][$key]),2,'.',''),

                                'grn_item_sgst'         =>number_format(($billSale['item_sgst'][$key]),2,'.',''),

                                'grn_item_igst'         =>number_format(($billSale['item_igst'][$key]),2,'.',''),

                                );

                                 $grn_item_id = $this->$model->insertData($array_item_details,'ret_grn_items');

                                 if($grn_item_id)

                                 {

                                     if($billSale['stone_details'][$key])

                                     {

                                            $stone_details=json_decode($billSale['stone_details'][$key],true);

                                            //echo "<pre>";print_r($stone_details);exit;

            								foreach($stone_details as $stone)

            								{

            									$stone_data=array(

            									'grn_item_id'    =>$grn_item_id,

            									'pieces'         =>$stone['stone_pcs'],

            									'wt'             =>$stone['stone_wt'],

            									'stone_id'       =>$stone['stone_id'],

            									'amount'          =>$stone['stone_price'],

												'uom_id'         => $stone['stone_uom_id'],

            									'is_apply_in_lwt'=>$stone['show_in_lwt'],

                                            	'stone_cal_type' =>$stone['stone_cal_type'],

                                            	'rate_per_gram'  =>$stone['stone_rate'],

            									);

            									$stoneInsert = $this->$model->insertData($stone_data,'ret_grn_item_stone');

                                                //print_r($this->db->last_query());exit;

            								}

                                     }

                                     if($billSale['other_metal_details'][$key])

                                     {

                                        $othermetal_details=json_decode($billSale['other_metal_details'][$key],true);

                                        foreach($othermetal_details as $othermetal)

                                    	{

                                        	$other_metal_data = array(

                                    	    'grn_itms_id'               =>$grn_item_id,

                                        	'grn_other_itm_metal_id'    =>$othermetal['id_metal'],

                                        	'grn_other_itm_grs_weight'  =>$othermetal['gwt'],

                                        	'grn_other_itm_pur_id'      =>$othermetal['id_purity'],

                                        	'grn_other_itm_wastage'     =>$othermetal['wastage_perc'],

                                        	'grn_other_itm_mc'          =>$othermetal['making_charge'],

                                        	'grn_other_itm_cal_type'    =>$othermetal['calc_type'],

                                        	'grn_other_itm_rate'        =>$othermetal['rate_per_gram'],

                                        	'grn_other_itm_pcs'         =>$othermetal['pcs'],

                                        	'grn_other_itm_amount'      =>$othermetal['amount'],

                                        	);

                                        	$othermetalInsert = $this->$model->insertData($other_metal_data,'ret_grn_other_metals');

                                        	//echo $this->db->last_query();exit;

                                    	}

                                     }

                                 }



    			            }

    			        }

			        }



			        if(!empty($addData['other_charges_details'])){

    			        $charge_details = json_decode($addData['other_charges_details'],true);

                    	foreach($charge_details as $charges)

                    	{

                    	    $cgst_cost = 0;

                    	    $sgst_cost = 0;

                    	    $igst_cost = 0;

                    	    $total_tax = number_format(($charges['char_with_tax'] - $charges['charge_value']),2,'.','');

                    	    if($addData['cmp_country']==$addData['supplier_country'])

                    	    {

                    	        if($addData['cmp_state']==$addData['supplier_state'])

                    	        {

                    	            $cgst_cost = ($total_tax/2);

                    	            $sgst_cost = ($total_tax/2);

                    	        }

                    	        else

                    	        {

                    	            $igst_cost = $total_tax;

                    	        }

                    	    }

                    	    else

                    	    {

                    	        $cgst_cost = ($total_tax/2);

                    	        $sgst_cost = ($total_tax/2);

                    	    }

                        	$charge_data = array(

                            	'grn_id'           => $insId,

                            	'grn_charge_id'    => $charges['charge_id'],

                            	'grn_charge_value' => $charges['charge_value'],

                            	'char_with_tax'    => $charges['char_with_tax'],

                            	'char_tax'         => $charges['charge_tax'],

                            	'total_tax'        => number_format(($charges['char_with_tax'] - $charges['charge_value']),2,'.',''),

                            	'igst_cost'        => $igst_cost,

                            	'cgst_cost'        => $cgst_cost,

                            	'sgst_cost'        => $sgst_cost,

                        	);

                        	$stoneInsert = $this->$model->insertData($charge_data, 'ret_grn_other_charges');

                        	//echo $this->db->last_query();exit;

                    	}



    			    }



			        if($this->db->trans_status()===TRUE)

        			{

        				$this->db->trans_commit();


						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'GRN Entry',

							'operation'     => 'Add',

							'record'        =>  $insId,

							'remark'        => 'GRN Entry added successfully'

							);

						$this->log_model->log_detail('insert','',$log_data);

        				$this->session->set_flashdata('chit_alert',array('message'=>'GRN Entry Added Successfully','class'=>'success','title'=>'GRN Entry'));

        				$return_data=array('status'=>TRUE,'message'=>'GRN Entry Added Successfully..');

        			}

        			else

        			{



        			    // echo $this->db->last_query();exit;

        				$this->db->trans_rollback();

        				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'GRN Entry'));

        				$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

        			}

			    }

			    else

			    {

			            // echo $this->db->last_query();exit;

        				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'GRN Entry'));

        				$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

			    }



    			echo json_encode($return_data);

			break;



			case 'delete':

    			   $this->$model->deleteData('id_order_des',$id,'ret_purchase_order_description');

    	           if($this->db->trans_status()===TRUE)

    			    {

    			    	  $this->db->trans_commit();

						  $log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'GRN Entry',

							'operation'     => 'Delete',

							'record'        =>  $id,

							'remark'        => 'GRN Entry deleted successfully'

							);

	                      $this->log_model->log_detail('insert','',$log_data);

    					  $this->session->set_flashdata('chit_alert', array('message' => 'Order Instructions deleted successfully','class' => 'success','title'=>'Delete Order Instructions'));

    				}

    			   else

    			    {

    				 $this->db->trans_rollback();

    				 $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Delete Order Instructions'));

    			    }

    			 redirect('admin_ret_purchase/order_description/list');

			 break;



			 case 'edit':

			    $model  =	self::RET_PUR_ORDER_MODEL;

                $data   =  $this->$model->get_orderdescription($id);

                echo json_encode($data);

			 break;



			 case 'update':

				$updData = $_POST['order'];

				$id = $updData['grn_id'];

				$p_ImgData   = json_decode(rawurldecode($this->input->post('tag_img')));

				//echo "<pre>";print_r($updData);exit;

				$fin_year       = $this->$model->get_FinancialYear();

				$order = array(

					'grn_fin_year_code'     => $fin_year['fin_year_code'],

					'grn_karigar_id'        => $updData['id_karigar'],

					'grn_supplier_ref_no'   => ($updData['po_supplier_ref_no']!='' ? $updData['po_supplier_ref_no']:NULL),

					'grn_ref_date'          => !empty($updData['po_ref_date']) ? date('Y-m-d', strtotime($updData['po_ref_date'])):NULL,

					'grn_type'              => $updData['grn_type'],

					'grn_ewaybillno'        => ($updData['ewaybillno']!='' ? $updData['ewaybillno']:NULL),

					'grn_despatch_through'  => ($updData['despatch_through']!='' ? $updData['despatch_through']:NULL),

					'grn_irnno'             => ($updData['invoice_ref_no']!='' ? $updData['invoice_ref_no']:NULL),

					'grn_other_charges'     => ($updData['other_charges_amount']!='' ? ($updData['other_charges_amount']+$updData['other_charges_tax']):0),

					'grn_discount'          => ($updData['discount']!='' ? $updData['discount']:0),

					'grn_purchase_amt'      => ($updData['total_cost']!='' ? $updData['total_cost']:0),

					'grn_round_off'         => ($updData['round_off']!='' ? ($updData['round_off_type']==1 ? '+'.$updData['round_off'] :'-'.$updData['round_off']):0),

					'grn_tcs_percent'       => ($updData['tcs_percent']!='' ? $updData['tcs_percent']:0),

					'grn_tcs_value'         => ($updData['tcs_tax_value']!='' ? $updData['tcs_tax_value']:0),

					'grn_pay_tds_percent'           => ($updData['tds_percent']!='' ? $updData['tds_percent']:0),

					'grn_pay_tds_value'             => ($updData['tds_tax_value']!='' ? $updData['tds_tax_value']:0),

					'grn_other_charges_tds_value'   => ($updData['other_charges_tds_tax_value']!='' ? $updData['other_charges_tds_tax_value']:0),

					'grn_other_charges_tds_percent' => ($updData['charges_tds_percent']!='' ? $updData['charges_tds_percent']:0),

					'form_secret'                   => ($updData['form_secret']!='' ? $updData['form_secret']:NULL),

					'remarks'                       => ($updData['remarks']!='' ? $updData['remarks']:NULL),

				   	'updated_on'                    => date("Y-m-d H:i:s"),

				   	'updated_by'                    => $this->session->userdata('uid')

			   );



			   $this->db->trans_begin();



			   $update_status = $this->$model->updateData($order,'grn_id',$id,'ret_grn_entry');

    			if($update_status)

    			{

    				$this->$model->deleteData('grn_item_grn_id', $id, 'ret_grn_items');

					$this->$model->deleteData('grn_id', $id, 'ret_grn_images');

					if (sizeof($p_ImgData) > 0) {



						$_FILES['precious'] = array();

						foreach ($p_ImgData as $precious) {
							// print_r($precious); // Uncomment for debugging
							$imgFile = $this->base64ToFile($precious->src);
							$_FILES['precious'][] = $imgFile;
						}

						$img_arr = array();
                        //   print_r($precious);

						if (!empty($_FILES['precious'])) {
							$folder = self::IMG_PATH . "grn_entry/";

							if (!is_dir($folder)) {
								mkdir($folder, 0777, TRUE);
							}


							foreach ($_FILES['precious'] as $file_key => $file_val) {
								if ($file_val['tmp_name']) {
									$img_name = $id . "_" . mt_rand(100001, 999999) . ".jpg";
									$path = $folder . "/" . $img_name;

									$result = $this->upload_img('image', $path, $file_val['tmp_name']);

									if ($result) {
										$arrayimg_tag = array(
											'grn_id'   => $id,
											'image'    => $img_name,
											'date_add' => date("Y-m-d H:i:s"),

										);

										// print_r($arrayimg_tag); // Uncomment for debugging

										$img_arr[] = $arrayimg_tag;

										$insImageId = $this->$model->insertData($arrayimg_tag, 'ret_grn_images');

									}
								}
							}
						}
					}



    			   	if($updData['grn_type']==1 || $updData['grn_type']==2 || $updData['grn_type']==4)

    				{



    					$updItems = $_POST['item'];



    					foreach($updItems['category'] as $key => $val)

    					{



    						$this->$model->deleteData('grn_item_id', $updItems['grn_item_id'][$key], 'ret_grn_item_stone');



    						$this->$model->deleteData('grn_itms_id', $updItems['grn_item_id'][$key], 'ret_grn_other_metals');





    						$ItemDetails = array(



    							'grn_item_grn_id'       =>$id,

    							'grn_item_cat_id'       =>(isset($updItems['category'][$key]) ? $updItems['category'][$key]:NULL),

    							'grn_gross_wt'          =>(isset($updItems['gross_wt'][$key]) ? $updItems['gross_wt'][$key]:NULL),

    							'grn_less_wt'           =>(isset($updItems['less_wt'][$key]) ? $updItems['less_wt'][$key]:NULL),

    							'grn_net_wt'            =>(isset($updItems['net_wt'][$key]) ? $updItems['net_wt'][$key]:NULL),

    							'grn_wastage'           =>(isset($updItems['wastage'][$key]) ? $updItems['wastage'][$key]:0),

    							'grn_no_of_pcs'         =>(isset($updItems['pcs'][$key]) ? $updItems['pcs'][$key]:NULL),

    							'grn_rate_per_grm'      =>(isset($updItems['rate_per_gram'][$key]) ? $updItems['rate_per_gram'][$key]:NULL),

    							'grn_item_cost'         =>(isset($updItems['item_cost'][$key]) ? $updItems['item_cost'][$key]:NULL),

    							'itemratecaltype'       =>(isset($updItems['rate_type'][$key]) ? $updItems['rate_type'][$key]:NULL),

    							'grn_item_gst_rate'     =>(isset($updItems['item_total_tax'][$key]) ? $updItems['item_total_tax'][$key]:NULL),

    							'grn_item_gst_value'    =>number_format(($updItems['tax_percentage'][$key]),2,'.',''),

    							'grn_item_cgst'         =>number_format(($updItems['item_cgst'][$key]),2,'.',''),

    							'grn_item_sgst'         =>number_format(($updItems['item_sgst'][$key]),2,'.',''),

    							'grn_item_igst'         =>number_format(($updItems['item_igst'][$key]),2,'.',''),

    						);



    						$grn_item_id = $this->$model->insertData($ItemDetails,'ret_grn_items');



    						if($grn_item_id)

    						{



    							if($updItems['stone_details'][$key])

    							{

    									$stone_details=json_decode($updItems['stone_details'][$key],true);

    									//echo "<pre>";print_r($stone_details);exit;

    									foreach($stone_details as $stone)

    									{

    										$stone_data=array(

    										'grn_item_id'    =>$grn_item_id,

    										'pieces'         =>$stone['stone_pcs'],

    										'wt'             =>$stone['stone_wt'],

    										'stone_id'       =>$stone['stone_id'],

    										'amount'          =>$stone['stone_price'],

    										'is_apply_in_lwt'=>$stone['show_in_lwt'],

											'uom_id'         => $stone['stone_uom_id'],

    										'stone_cal_type' =>$stone['stone_cal_type'],

    										'rate_per_gram'  =>$stone['stone_rate'],

    										);

    										$stoneInsert = $this->$model->insertData($stone_data,'ret_grn_item_stone');

    										//print_r($this->db->last_query());exit;

    									}

    							}

    							if($updItems['other_metal_details'][$key])

    							{

    								$othermetal_details=json_decode($updItems['other_metal_details'][$key],true);

    								foreach($othermetal_details as $othermetal)

    								{

    									$other_metal_data = array(

    									'grn_itms_id'               =>$grn_item_id,

    									'grn_other_itm_metal_id'    =>$othermetal['id_metal'],

    									'grn_other_itm_grs_weight'  =>$othermetal['gwt'],

    									'grn_other_itm_pur_id'      =>$othermetal['id_purity'],

    									'grn_other_itm_wastage'     =>$othermetal['wastage_perc'],

    									'grn_other_itm_mc'          =>$othermetal['mc_value'],

    									'grn_other_itm_cal_type'    =>$othermetal['calc_type'],

    									'grn_other_itm_rate'        =>$othermetal['rate_per_gram'],

    									'grn_other_itm_pcs'         =>$othermetal['pcs'],

    									'grn_other_itm_amount'      =>$othermetal['amount'],

    									);

    									$othermetalInsert = $this->$model->insertData($other_metal_data,'ret_grn_other_metals');

    									//echo $this->db->last_query();exit;

    								}

    							}







    						}





    					}

    				}



    					if(!empty($updData['other_charges_details']))

    					{

    						$this->$model->deleteData('grn_id', $id, 'ret_grn_other_charges');



        			        $charge_details = json_decode($updData['other_charges_details'],true);



    						//echo "<pre>";print_r($charge_details);exit;



                        	foreach($charge_details as $charges)

                        	{

                        	    $cgst_cost = 0;

                        	    $sgst_cost = 0;

                        	    $igst_cost = 0;

                        	    $total_tax = number_format(($charges['char_with_tax'] - $charges['charge_value']),2,'.','');

                        	    if($updData['cmp_country']==$updData['supplier_country'])

                        	    {

                        	        if($updData['cmp_state']==$updData['supplier_state'])

                        	        {

                        	            $cgst_cost = ($total_tax/2);

                        	            $sgst_cost = ($total_tax/2);

                        	        }

                        	        else

                        	        {

                        	            $igst_cost = $total_tax;

                        	        }

                        	    }

                        	    else

                        	    {

                        	        $cgst_cost = ($total_tax/2);

                        	        $sgst_cost = ($total_tax/2);

                        	    }

                            	$charge_data = array(

                                	'grn_id'           => $id,

                                	'grn_charge_id'    => $charges['charge_id'],

                                	'grn_charge_value' => $charges['charge_value'],

                                	'char_with_tax'    => $charges['char_with_tax'],

                                	'char_tax'         => $charges['charge_tax'],

                                	'total_tax'        => number_format(($charges['char_with_tax'] - $charges['charge_value']),2,'.',''),

                                	'igst_cost'        => $igst_cost,

                                	'cgst_cost'        => $cgst_cost,

                                	'sgst_cost'        => $sgst_cost,

                            	);

                            	$stoneInsert = $this->$model->insertData($charge_data, 'ret_grn_other_charges');

                            	//echo $this->db->last_query();exit;

                        	}



        			    }





    			}



			    if($this->db->trans_status()===TRUE)

    			{

    				$this->db->trans_commit();

					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'GRN Entry',

						'operation'     => 'Update',

						'record'        => $update_status,

						'remark'        => 'GRN Entry Updated successfully'

						);

			        $this->log_model->log_detail('insert','',$log_data);

    				$this->session->set_flashdata('chit_alert',array('message'=>'GRN Entry Updated Successfully','class'=>'success','title'=>'GRN ENTRY'));

    				$return_data=array('status'=>TRUE,'message'=>'GRN Entry Updated Successfully');

    			}

    			else

    			{



    			    // echo $this->db->last_query();exit;

    				$this->db->trans_rollback();

    				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'GRN ENTRY'));

    				$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

    			}

    			echo json_encode($return_data);

            break;



			 case 'cancel_grn_entry':

                    $updData=array(

                    'grn_bill_status'   => 2,

                    'cancel_reason'     => $_POST['cancel_reason'],

                    'updated_on'        => date("Y-m-d H:i:s"),

                    'updated_by'        => $this->session->userdata('uid')

                    );

                    $this->db->trans_begin();



                    $this->$model->updateData($updData,'grn_id',$_POST['grn_id'],'ret_grn_entry');

                    if($this->db->trans_status()===TRUE)

                    {

                        $this->db->trans_commit();

						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'GRN Entry',

							'operation'     => 'Cancel',

							'record'        => $_POST['grn_id'],

							'remark'        => 'GRN Entry Cancelled successfully'

							);

						 $this->log_model->log_detail('insert','',$log_data);

                         $this->session->set_flashdata('chit_alert',array('message'=>'GRN Entry Cancelled Successfully','class'=>'success','title'=>'GRN Entry'));

                         $return_data=array('status'=>TRUE,'message'=>'Order Instructions Added Successfully..');

                    }

                    else

                    {

                        // echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'GRN Entry'));

                        $return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

                    }

                    echo json_encode($return_data);

			 break;



		    default:

		            $from_date	= $this->input->post('from_date');

			        $to_date	= $this->input->post('to_date');


					$profile=$this->$model->get_profile_settings($this->session->userdata('profile'));

			        $list=$this->$model->ajax_getGrnentrydetails($from_date, $to_date);

				  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/grnentry/list');

			        $data = array(

			        					'list'  => $list,

										'profile' =>$profile,

										'access'=> $access

			        				);

					echo json_encode($data);

		}

	}

	function grn_invoice($id)

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $set_model = "admin_settings_model";

        $data['comp_details']   = $this->$set_model->get_company();

        $data['grn_details']    = $this->$model->get_grn_details($id);

        //echo "<pre>";print_r($data);exit;

        $this->load->helper(array('dompdf', 'file'));

        $dompdf = new DOMPDF();

        $html = $this->load->view(self::VIEW_FOLDER.'grn_entry/invoice', $data,true);

        $dompdf->load_html($html);

        $dompdf->set_paper('A4', "portriat" );

		$dompdf->render();

		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("VendorAck.pdf",array('Attachment'=>0));

	}





	//GRN ENTRY



	//Supplier Update

	function update_karigar(){

		$model=	self::RET_PUR_ORDER_MODEL;

		$updData=array(

			'firstname'=>$_POST['first_name'],

			'pan_no'=>$_POST['pan_no'],

			'gst_number'=>$_POST['gst_no'],

			'address1'=>$_POST['address1'],

			'address2'=>$_POST['address2'],

			'address3'=>$_POST['address3'],

			'pincode'=>$_POST['pin_code_add'],

			'id_city'=>$_POST['id_city'],

			'id_state'=>$_POST['id_state'],

			'id_country'=>$_POST['id_country'],

			);

		  $this->db->trans_begin();

		  $status = $this->$model->updateData($updData,'id_karigar',$_POST['id_karigar'],'ret_karigar');

		  if($this->db->trans_status()===TRUE)

		  {

			  $this->db->trans_commit();

			  $return_data=array('status'=>TRUE,'title'=>'Success','priority'=>'success','msg'=>'Karigar Updated successfully..');

		  }

		  else

		  {



			  $this->db->trans_rollback();

			  $return_data=array('status'=>FALSE,'title'=>'Warning','priority'=>'danger','msg'=>'Unable to proceed your request');

		  }

			  echo json_encode($return_data);

	}

	//Supplier Update







	//approval stock tag list

	function approvalstock_tag($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$set_model = "admin_settings_model";

		switch($type)

		{

			case 'list':

					$data['access'] = $this->$set_model ->get_access('admin_ret_purchase/approvalstock_tag/list');

					$data['main_content'] = self::VIEW_FOLDER.'approval_tag';




        			$this->load->view('layout/template', $data);

			break;



			case 'ajax':

				$list=$this->$model->get_approval_stock_tags($_POST);

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/approvalstock_tag/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access


		        				);

				echo json_encode($data);

			break;

		}

	}



	function order_place()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $fin_year       = $this->$model->get_FinancialYear();

	    $req_data       = $_POST['req_data'];

	    $karigar_details    =[];



        foreach($req_data as $r){

			$karigar_details[$r['id_karigar']][] = $r;

		}

	    foreach($karigar_details as $id_karigar => $tag_details)

	    {

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

            $insOrder = $this->$model->insertData($order,'customerorder');



	        foreach($tag_details as $val)

	        {







	            $tag_det = $this->$model->get_approval_tag_details($val['tag_id']);



	            $total_pcs+=$tag_det['piece'];

	            $total_weight+=$tag_det['gross_wt'];



	            $orderDetails = array(

				'id_customerorder'	=>$insOrder,

				'approval_tagid'	=>$val['tag_id'],

				'orderstatus'		=>3,

				'id_weight_range'	=> NULL,

				'id_product'		=> (!empty($tag_det['product_id']) ? $tag_det['product_id'] :NULL ),

				'design_no'			=> (!empty($tag_det['design_id']) ? $tag_det['design_id'] :NULL ),

				'id_sub_design'		=> (!empty($tag_det['id_sub_design']) ? $tag_det['id_sub_design'] :NULL ),

				'totalitems'		=> (!empty($tag_det['piece']) ? $tag_det['piece'] :NULL ),

				'weight'		    => (!empty($tag_det['gross_wt']) ? $tag_det['gross_wt'] :NULL ),

				'size'				=> (!empty($tag_det['size']) ? $tag_det['size'] :NULL ),

				'smith_due_date'	=> NULL,

				'order_date'		=> date("Y-m-d H:i:s"),

				'id_employee'       => $this->session->userdata('uid'),

				);

				$insOrderdet = $this->$model->insertData($orderDetails,'customerorderdetails');



				if($insOrderdet)

				{

				    $dCData = $this->admin_settings_model->getBranchDayClosingData($val['current_branch']);

    	            $bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);



				    $tag_log=array(

                            'tag_id'	  =>$val['tag_id'],

                            'date'		  =>$bill_date,

                            'status'	  =>13,

                            'from_branch' =>$tag_det['current_branch'],

                            'to_branch'	  =>NULL,

                            'issuspensestock'	  =>1,

                            'created_on'  =>date("Y-m-d H:i:s"),

                            'created_by'  =>$this->session->userdata('uid'),

                            );

                            $this->$model->insertData($tag_log,'ret_taging_status_log');

				}

	        }

	        $this->$model->updateData(array('order_pcs'=>$total_pcs,'order_approx_wt'=>$total_weight),'id_customerorder',$insOrder,'customerorder');

	    }

    	if($this->db->trans_status()===TRUE)

		{

			$this->db->trans_commit();

			$log_data = array(

				'id_log'        => $this->session->userdata('id_log'),

				'event_date'    => date("Y-m-d H:i:s"),

				'module'        => 'Approval Stock',

				'operation'     => 'Add',

				'record'        =>  $insOrder,

				'remark'        => 'Order Placed added successfully'

				);

			$this->log_model->log_detail('insert','',$log_data);

			$this->session->set_flashdata('chit_alert', array('message' => 'Order Placed Successfully','class' => 'success','title'=>'Order'));

			$response_data=array('status'=>TRUE,'msg'=>'Order Placed Successfully..');

		}

		else

		{

		    //echo $this->db->_error_message();

		    echo $this->db->last_query();exit;

			$this->db->trans_rollback();

			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to Proceed Your Request..','class' => 'danger','title'=>'Order'));

			$response_data=array('status'=>FALSE,'msg'=>'Unable to Proceed Your Request..');

		}

        echo json_encode($response_data);

	}





	function convert_to_normal_stock()

	{

	    $model=	self::RET_PUR_ORDER_MODEL;

	    $req_data       = $_POST['req_data'];

	    $karigar_details    =[];

	    $ho              =  $this->$model->get_headOffice();

	    foreach($req_data as $val)

	    {

	        $tag_det = $this->$model->get_approval_tag_details($val['tag_id']);



	        if($tag_det['tag_status']==0)

	        {

    	        $dCData = $this->admin_settings_model->getBranchDayClosingData($tag_det['current_branch']);



    	        $bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);



                $this->$model->updateData(array('is_approval_stock_converted'=>1,'app_stk_converted_date'=>date("Y-m-d H:i:s"),'app_stk_converted_by'=>$this->session->userdata('uid')),'tag_id',$val['tag_id'], 'ret_taging');



                $ho_log_data=array(

                'tag_id'	  =>$val['tag_id'],

                'date'		  =>$bill_date,

                'status'	  =>0,

                'from_branch' =>NULL,

                'to_branch'   =>$ho['id_branch'],

                'created_on'  =>date("Y-m-d H:i:s"),

                'created_by'  =>$this->session->userdata('uid'),

                );

                $this->$model->insertData($ho_log_data,'ret_taging_status_log'); //Update Tag lot status



                if($ho['id_branch']!=$tag_det['current_branch'])

                {

                    $ho_log_data=array(

                    'tag_id'	  =>$val['tag_id'],

                    'date'		  =>$bill_date,

                    'status'	  =>4,

                    'from_branch' =>1,

                    'to_branch'   =>$tag_det['current_branch'],

                    'created_on'  =>date("Y-m-d H:i:s"),

                    'created_by'  =>$this->session->userdata('uid'),

                    );

                    $this->$model->insertData($ho_log_data,'ret_taging_status_log'); //Update Tag lot status



                    $branch_log_data=array(

                    'tag_id'	  =>$val['tag_id'],

                    'date'		  =>$bill_date,

                    'status'	  =>0,

                    'from_branch' =>1,

                    'to_branch'   =>$tag_det['current_branch'],

                    'created_on'  =>date("Y-m-d H:i:s"),

                    'created_by'  =>$this->session->userdata('uid'),

                    );

                    $this->$model->insertData($branch_log_data,'ret_taging_status_log'); //Update Tag lot status

                }

	        }

	    }

    	if($this->db->trans_status()===TRUE)

		{

			$this->db->trans_commit();

			$this->session->set_flashdata('chit_alert', array('message' => 'Tag Status Changed Successfully','class' => 'success','title'=>'Order'));

			$response_data=array('status'=>TRUE,'msg'=>'Tag Status Changed Successfull..');

		}

		else

		{

		    //echo $this->db->_error_message();

		    echo $this->db->last_query();exit;

			$this->db->trans_rollback();

			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to Proceed Your Request..','class' => 'danger','title'=>'Order'));

			$response_data=array('status'=>FALSE,'msg'=>'Unable to Proceed Your Request..');

		}

        echo json_encode($response_data);

	}





	//approval stock tag list





	//Lot generate

	function generateLot()

	{

	    $model           =	self::RET_PUR_ORDER_MODEL;

	    $po_id           =  $_POST['po_id'];

	    $item_details    =  $_POST['req_data'];

	    $remark          =  $_POST['remark'];

	    $lotData = [];

        foreach($item_details as $r){

			$lotData[$r['stock_type']][$r['cat_id']][$r['id_purity']][] = $r;

		}



        $ho              =  $this->$model->get_headOffice();

		$dCData          =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

		$bill_date       =  ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

		$po_details      = $this->$model->get_po_details($po_id);

    	foreach($lotData as $stk_type =>$stock_type)

		{

		    foreach($stock_type as $cat_id =>$category)

		    {

		        foreach($category as $id_purity =>$product_details)

		        {



		            $data = array(

		                'narration'				=> $remark,

                        'lot_date'				=> $bill_date,

                        'created_branch'		=> (isset($ho['id_branch']) ? $ho['id_branch']:NULL ),

                        'lot_received_at'       => $ho['id_branch'],

                        'stock_type'            => $stk_type,

                        'lot_from'              => 2,

                        'po_id'                 => $po_id,

                        'gold_smith'			=> $po_details['po_karigar_id'],

                        'id_category'			=> $cat_id,

                        'id_purity'				=> $id_purity,

                        'created_on'	  		=> date("Y-m-d H:i:s"),

                        'created_by'      		=> $this->session->userdata('uid')

                        );

                        $this->db->trans_begin();

    	 	            $insId = $this->$model->insertData($data,'ret_lot_inwards');

    	 	            if($insId)

    	 	            {


    	 	                foreach($product_details as $items)

    	 	                {

								$item_details      = $this->$model->get_po_item_details($items['po_item_id']);


    	 	                        $lotDetails = array(

                                        'lot_no'	    =>$insId,

                                        'po_item_id'   =>($items['po_item_id']!='' ? $items['po_item_id']:NULL),

                                        'lot_product'   =>($items['pro_id']!='' ? $items['pro_id']:NULL),

                                        'lot_id_design'   =>($items['id_design']!='' ? $items['id_design']:NULL),

                                        'id_sub_design'   =>($items['id_sub_design']!='' ? $items['id_sub_design']:NULL),

                                        'id_section'   =>($items['id_section']!='' ? $items['id_section']:NULL),

                                        'no_of_piece'   =>($items['pcs']!='' ? $items['pcs']:0),

                                        'gross_wt'      =>($items['gross_wt']!='' ? $items['gross_wt']:0),

                                        'less_wt'       =>($items['less_wt']!='' ? $items['less_wt']:0),

                                        'net_wt'        =>($items['net_wt']!='' ? $items['net_wt']:0),

										'wastage_percentage'        =>($item_details['item_wastage']!='' ? number_format((float)$item_details['item_wastage'],3,'.',''):0),

										'mc_type'        =>($item_details['mc_type']!='' ? $item_details['mc_type']:0),

										'making_charge'        =>($item_details['mc_value']!='' ? number_format((float)$item_details['mc_value'],2,'.',''):0),

										'rate'        =>($item_details['fix_rate_per_grm']!='' ? $item_details['fix_rate_per_grm']:0),

										'rate_calc_type'        =>($item_details['rate_calc_type']!='' ? $item_details['rate_calc_type']:0),

										'calc_type'        =>($item_details['pure_wt_calc_type']!='' ? $item_details['pure_wt_calc_type']:0),

										'purchase_touch'        =>($item_details['purchase_touch']!='' ? $item_details['purchase_touch']:0),

										'lot_id_category'=> $cat_id,

										'lot_id_purity'  => $id_purity,

                                    );




                                    $detail_insId = $this->$model->insertData($lotDetails,'ret_lot_inwards_detail');

                                    if($detail_insId)

                                    {

                                        $stone_details = json_decode($items['stone_details'],true);

                                        //echo "<pre>";print_r($items['stone_details']);exit;

                                        if(sizeof($stone_details)>0){

                                            foreach($stone_details as $val)

                                            {

                                                $stnData = array(
                                                        'id_lot_inward_detail'  =>$detail_insId,
                                                        'stone_id'              =>$val['po_stone_id'],
                                                        'uom_id'                =>$val['uom_id'],
                                                        'stone_pcs'             =>$val['qc_passed_pcs'],
                                                        'stone_wt'              =>$val['qc_passed_wt'],
                                                        'is_apply_in_lwt'       =>$val['is_apply_in_lwt'],
                                                        'rate_per_gram'         =>$val['po_stone_rate'],
                                                        'stone_cal_type'        => $val['po_stone_calc_based_on'] != NULL ? $val['po_stone_calc_based_on'] : 1,
                                                        'stone_quality_id'      =>$val['po_quality_id'],
                                                        'price'                 =>($val['po_stone_calc_based_on'] ==2 ? number_format(($val['po_stone_rate']*$val['qc_passed_pcs']),2,'.','')  : number_format(($val['po_stone_rate']*$val['qc_passed_wt']),2,'.','')),
                                                        );

                                                $this->$model->insertData($stnData,'ret_lot_inwards_stone_detail');

                                            }

                                        }



                                    }


    	 	                    //$this->$model->updateData(array('is_lot_created'=>1,'lot_no'=>$insId),'id_qc_issue_details',$items['id_qc_issue_details'],'ret_po_qc_issue_details');

    	 	                }

    	 	            }

		        }

		    }

		}

        if($this->db->trans_status()===TRUE)

        {

            $log_data = array(

            	'id_log'        => $this->session->userdata('id_log'),

            	'event_date'	=> date("Y-m-d H:i:s"),

            	'module'      	=> 'Lot',

            	'operation'   	=> 'Add',

            	'record'        => $insId,

            	'remark'       	=> 'Lot added successfully From QC Receipt'

            );

            $this->log_model->log_detail('insert','',$log_data);

            $this->db->trans_commit();

            $responseData=array('status'=>true,'message'=>'Lot Created successfully.');

        }

        else

        {

            $this->db->trans_rollback();

            $responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

        }

        echo json_encode($responseData);

	}

	//Lot generate







	function update_po_approval()

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$response_data=array();

		$insert_data=$this->input->post('req_data');

		foreach ($insert_data as $value)

		{

            $Data=array(

            'po_id' => $value['id_po'],

            'is_approved' 	 => 1,

			'approved_by' 	 =>  $this->session->userdata('uid'),

			'po_delivery_date'=> $value['due_date'],

            'approved_date'  => date("Y-m-d H:i:s"),

            );

            $this->db->trans_begin();

			$po_id = $this->$model->updateData($Data,'po_id',$value['id_po'],'ret_purchase_order');

		}

		if($this->db->trans_status()===TRUE)

        {

		 	$this->db->trans_commit();

			 $log_data = array(

				'id_log'        => $this->session->userdata('id_log'),

				'event_date'    => date("Y-m-d H:i:s"),

				'module'        => 'Rate Fixing',

				'operation'     => 'Update',

				'record'        =>  $po_id,

				'remark'        => 'Rate Fixing Updated successfully'

				);

			$this->log_model->log_detail('insert','',$log_data);

		 	$response_data=array('status'=>TRUE,'message'=>'Approved Successfully.');

		}

		else

		{

			$this->db->trans_rollback();

			$response_data=array('status'=>FALSE,'message'=>'Unable to proceed Your Request.');

		}

		echo json_encode($response_data);

	}





    //Weight and Amount Conversation

    function get_approval_rate_fixing_po_no()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_approval_rate_fixing_po_no($_POST);

        echo json_encode($data);

    }



    function opening_balance_ratefixing()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->opening_balance_ratefixing($_POST);

        echo json_encode($data);

    }



    function supplier_rate_cut($type = "",$id="",$status="")

	{

		$model =	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;


		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'amt_weight_conversation/list';

				$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_purchase/supplier_rate_cut/list');

				$this->load->view('layout/template', $data);

				break;

			case 'add':

				$ho                  =  $this->$model->get_headOffice();

				$data['financial_year']  = $this->$model->GetFinancialYear();

				$data['comp_details']=$this->admin_settings_model->getCompanyDetails($ho['id_branch']);

				$data['is_against_po_bills']      = $this->$model->get_ret_settings('issue_against_po_bills');

				$data['main_content'] = self::VIEW_FOLDER . 'amt_weight_conversation/form';

				$this->load->view('layout/template', $data);

				break;

			case 'save':

				$addData                =  $_POST['supplier_rate_cut'];

				$billing                =  $_POST['billing'];

				$ho                     =  $this->$model->get_headOffice();

				$entry_date             =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

				$date_add               =  ($entry_date['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $entry_date['entry_date']);

				$ref_no                 = $this->$model->generate_approval_refno();

				$fin_year              = $this->$model->get_FinancialYear();




				$data = array(

				    'ref_no'            => ($fin_year['fin_year_code'].'-'.$ref_no),

					'is_opening_blc'	=> $addData['is_opening_blc'],

					'op_blc_id'	        => $addData['id_smith_company_op_balance'],

					'convert_to'		=> $addData['convert_to'],

					'conversion_type'	=> $addData['conversion_type'],

					'id_branch'         => ($addData['id_branch'] != '' ? $addData['id_branch'] : 1),

					'id_karigar'        => ($addData['id_karigar'] != '' ? $addData['id_karigar'] : NULL),

					'rate_cut_type'     => ($addData['rate_cut_type'] != '' ? $addData['rate_cut_type'] : NULL),

					'id_metal'          => ($addData['id_metal'] != '' ? $addData['id_metal'] : NULL),

					'id_product'        => ($addData['id_product'] != '' ? $addData['id_product'] : NULL),

					'po_id'          	=> ($addData['po_id'] != '' ? $addData['po_id'] : NULL),

					'date_add'          => ($date_add != '' ? $date_add : NULL),

					'charges_amount'    => ($addData['charges_amount'] != '' ? $addData['charges_amount'] : 0),

					'amount'            => ($addData['total_bill_amount'] != '' ? $addData['total_bill_amount'] : 0),

					'weight'            => ($addData['type1_wt'] != '' ? $addData['type1_wt'] : ($addData['type2_wt'] != '' ? $addData['type2_wt'] : 0)),

					'rate_per_gram'     => ($addData['src_rate'] != '' ? $addData['src_rate'] : 0),

					'round_off'     	=> ($addData['round_off'] != '' ? $addData['round_off'] : 0),

					'status'            => 1,

					'narration'         => ($addData['src_remark'] != '' ? $addData['src_remark'] : NULL),
					'is_tds'            => (isset($addData['is_tds']) ? $addData['is_tds'] : 0),
					'tds_percent'       => ($addData['tds_percent'] != '' ? $addData['tds_percent'] : 0),
					'tds_tax_value'     => ($addData['tds_tax_value'] != '' ? $addData['tds_tax_value'] : 0),

					'tax_amount'		=> $this->isEmptySetDefault($addData['tax_amount'],0),
					'tax_percentage'	=> $this->isEmptySetDefault($addData['tax_percentage'],0),
					'sgst_cost'	        => $this->isEmptySetDefault($addData['sgst_cost'],0),
					'cgst_cost'	        => $this->isEmptySetDefault($addData['cgst_cost'],0),
					'igst_cost'	        => $this->isEmptySetDefault($addData['igst_cost'],0),

					'amount_type'       => ($addData['amount_type'] != '' ? $addData['amount_type'] : 1), // 1 = CR, 2 = DR

					'weight_type'       => ($addData['weight_type'] != '' ? $addData['weight_type'] : 1), // 1 = CR, 2 = DR

					'created_on'        => date("Y-m-d H:i:s"),

					'created_by'        => $this->session->userdata('uid'),

				);

				$this->db->trans_begin();

				$insOrder = $this->$model->insertData($data, 'ret_supplier_rate_cut');

				if($insOrder && $addData['rate_cut_type']==1 && $data['amount'] > 0 )

				{

				    $net_banking_details = json_decode($billing['net_banking'],true);

				    $pay_refno       = $this->$model->generatePaymentNo();

				    $payData=array(

				          'id_supplier_rate_cut'=>$insOrder,

		                  'pay_sup_id'      =>$addData['id_karigar'],

		                  'bill_type'       =>3,

		                  'pay_refno'       =>$pay_refno,

		                  'pay_amt'         =>$data['amount'],

		                  'pay_narration'   =>($addData['src_remark'] != '' ? $addData['src_remark'] : NULL),

		                  'pay_create_on'   =>date("Y-m-d H:i:s"),

		                  'pay_created_by'=>$this->session->userdata('uid'),

		                  );



			        $insId=$this->$model->insertData($payData,'ret_po_payment');



				    if($billing['cash_amount']!=0)

                    {

                        $cash_payment_data = array(

                                             'pay_id'           =>$insId,

                                             'type'             =>4,

                                             'pay_mode'         =>'CSH',

                                             'payment_amount'   =>$billing['cash_amount'],

                                            );

                         $this->$model->insertData($cash_payment_data,'ret_po_payment_detail');

                    }



                    if(sizeof($net_banking_details)>0)

                    {

                        foreach($net_banking_details as $pay)

                        {

                            $netBankingPaymentData[]=array(

                                             'pay_id'           =>$insId,

                                             'type'             =>1,

                                             'pay_mode'         =>$pay['nb_type'],

                                             'payment_amount'   =>$pay['pay_amount'],

                                             'ref_no'           =>($pay['ref_no']!='' ? $pay['ref_no']:NULL),

                                             'id_bank'          =>($pay['id_bank']!='' ? $pay['id_bank']:NULL),

                                             'ref_date'          =>($pay['nb_date']!='' ? $pay['nb_date']:date("Y-m-d")),

                                            );

                        }

                        $this->$model->insertBatchData($netBankingPaymentData,'ret_po_payment_detail');

                    }

                    // Insert into ret_po_bill_payment_details so the payment listing can resolve PO ref no
                    $bill_payment_data = array(
                        'pay_id'                => $insId,
                        'po_id'                 => ($addData['po_id'] != '' ? $addData['po_id'] : 0),
                        'id_supplier_rate_cut'  => $insOrder,
                        'bill_amount'           => $data['amount'],
                        'total_amount'          => $data['amount'],
                    );
                    $this->$model->insertData($bill_payment_data, 'ret_po_bill_payment_details');

				}

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'Approval to Invoice Conversion',

						'operation'     => 'Add',

						'record'        =>  $insOrder,

						'remark'        => 'Approval to Invoice Conversion added successfully'

						);

					$this->log_model->log_detail('insert','',$log_data);

					$this->session->set_flashdata('chit_alert', array('message' => 'Added Successfully', 'class' => 'success', 'title' => 'Rate Cut'));

					$return_data = array('status' => TRUE, 'message' => 'Order Instructions Added Successfully..');

				} else {

					echo $this->db->last_query();

					exit;

					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Rate Cut'));

					$return_data = array('status' => FALSE, 'message' => 'Unable to proceed the requested process');

				}

				echo json_encode($return_data);

				break;



			case 'job_receipt':

				$data['rate_cut_details']=$this->$model->get_approval_conversion_details($id);

				$data['comp_details']=$this->admin_settings_model->getCompanyDetails("");

				//echo "<pre>";print_r($data);exit;

				$html = $this->load->view(self::VIEW_FOLDER.'amt_weight_conversation/print', $data,true);

				echo $html;exit;



			break;

			case 'ajax':

				$list = $this->$model->get_supplier_rate_cut_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_purchase/supplier_rate_cut/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;

				case 'cancel':

				    $ho                     =  $this->$model->get_headOffice();

				    $entry_date             =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

				    $date_add               =  ($entry_date['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $entry_date['entry_date']);



					$data = array(

				            'status'            => 2,

					        'cancelled_on'	    => $date_add,

					        'updated_on'	    => date("Y-m-d H:i:s"),

					        'cancelled_by'      => $this->session->userdata('uid'),

					        'updated_by'        => $this->session->userdata('uid'),

					        'cancelled_reason'  => ($_POST['cancel_reason']!='' ? $_POST['cancel_reason'] :NULL),

					        );

					$this->db->trans_begin();

					$updstatus = $this->$model->updateData($data,'id_supplier_rate_cut',$_POST['id_supplier_rate_cut'],'ret_supplier_rate_cut');

    				if ($this->db->trans_status() === TRUE)

    				{

    					$this->db->trans_commit();

						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'Approval to Invoice Conversion',

							'operation'     => 'Cancel',

							'record'        =>  $_POST['id_supplier_rate_cut'],

							'remark'        => 'Approval to Invoice Conversion Cancelled successfully'

							);

	                     $this->log_model->log_detail('insert','',$log_data);

    					$this->session->set_flashdata('chit_alert', array('message' => 'Cancelled Successfully', 'class' => 'success', 'title' => 'Weight Conversion'));

    					$return_data = array('status' => TRUE, 'message' => 'Cancelled Successfully..');

    				}else{

    					$this->db->trans_rollback();

    					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Weight Conversion'));

    					$return_data = array('status' => FALSE, 'message' => 'Unable to proceed the requested process');

    				}

    				echo json_encode($return_data);

				break;

				echo json_encode($data);

		}

	}

    //Weight and Amount Conversation

    function lot_and_tag_wise_report($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$set_model = "admin_settings_model";

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'lot_and_tag_wise_report';

        			$this->load->view('layout/template', $data);

			break;



			case 'ajax':

				$list=$this->$model->get_lot_and_tag_wise_report($_POST);

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/lot_and_tag_wise_report/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

	}



	function headoffice_valut_report($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$set_model = "admin_settings_model";

		switch($type)

		{

			case 'list':
                     $data['is_supplierbill_entry_req']      = $this->$model->get_ret_settings('is_supplierbill_entry_req');
					$data['main_content'] = self::VIEW_FOLDER.'reports/headoffice_valut_report';

        			$this->load->view('layout/template', $data);

			break;



			case 'ajax':

			    $summary['blc_pcs'] = 0;

			    $summary['blc_gwt'] = 0;

			    $summary['blc_nwt'] = 0;

			    $summary['blc_diawt'] = 0;

			    $summary['inw_pcs']    = 0;

			    $summary['inw_gwt']    = 0;

			    $summary['inw_nwt']    = 0;

			    $summary['inw_diawt']    = 0;

			    $summary['issue_pcs']    = 0;

			    $summary['issue_gwt']    = 0;

			    $summary['issue_nwt']    = 0;

			    $summary['issue_diawt']  = 0;

			    $summary['lot_pcs']  = 0;

			    $summary['lot_gwt']  = 0;

			    $summary['lot_nwt']    = 0;

			    $summary['lot_dia_wt']    = 0;

			    $summary['tag_pcs']    = 0;

			    $summary['tag_gwt']    = 0;

			    $summary['tag_nwt']    = 0;

			    $summary['tag_dia_wt']    = 0;

			    $summary['pur_ret_pcs']    = 0;

			    $summary['pur_ret_gwt']    = 0;

			    $summary['pur_ret_nwt']    = 0;

			    $summary['ret_diawt']    = 0;

			    $summary['closing_pcs']    = 0;

			    $summary['closing_gwt']    = 0;

			    $summary['closing_nwt']    = 0;

			    $summary['closing_diawt']    = 0;

				$po_details=$this->$model->get_headoffice_valut_report($_POST);

				foreach($po_details as $val)

				{

				    $summary['blc_pcs']=$summary['blc_pcs']+$val['blc_pcs'];

				    $summary['blc_gwt']=$summary['blc_gwt']+$val['blc_gwt'];

				    $summary['blc_nwt']=$summary['blc_nwt']+$val['blc_nwt'];

				    $summary['blc_diawt']=$summary['blc_diawt']+$val['blc_diawt'];

				    $summary['inw_pcs']=$summary['inw_pcs']+$val['inw_pcs'];

				    $summary['inw_gwt']=$summary['inw_gwt']+$val['inw_gwt'];

				    $summary['inw_nwt']=$summary['inw_nwt']+$val['inw_nwt'];

				    $summary['inw_diawt']=$summary['inw_diawt']+$val['inw_diawt'];

				    $summary['pur_ret_pcs']=$summary['pur_ret_pcs']+$val['pur_ret_pcs'];

				    $summary['pur_ret_gwt']=$summary['pur_ret_gwt']+$val['pur_ret_gwt'];

				    $summary['pur_ret_nwt']=$summary['pur_ret_nwt']+$val['pur_ret_nwt'];

				    $summary['ret_diawt']=$summary['ret_diawt']+$val['ret_diawt'];

				    $summary['issue_pcs']=$summary['issue_pcs']+$val['issue_pcs'];

				    $summary['issue_gwt']=$summary['issue_gwt']+$val['issue_wt'];

				    $summary['issue_nwt']=$summary['issue_nwt']+$val['issue_wt'];

				    $summary['issue_diawt']= $summary['issue_diawt']+$val['issue_diawt'];

				    $summary['lot_pcs']=$summary['lot_pcs']+$val['lot_pcs'];

				    $summary['lot_gwt']=$summary['lot_gwt']+$val['lot_gwt'];

				    $summary['lot_nwt']=$summary['lot_nwt']+$val['lot_nwt'];

				    $summary['lot_dia_wt']=$summary['lot_dia_wt']+$val['lot_dia_wt'];

				    $summary['tag_pcs']=$summary['tag_pcs']+$val['tag_pcs'];

				    $summary['tag_gwt']=$summary['tag_gwt']+$val['tag_gwt'];

				    $summary['tag_nwt']=$summary['tag_nwt']+$val['tag_nwt'];

				    $summary['tag_dia_wt']=$summary['tag_dia_wt']+$val['tag_dia_wt'];

				    $summary['closing_pcs']=$summary['closing_pcs']+$val['closing_pcs'];

				    $summary['closing_gwt']=$summary['closing_gwt']+$val['closing_gwt'];

				    $summary['closing_nwt']=$summary['closing_nwt']+$val['closing_nwt'];

				    $summary['closing_diawt']=$summary['closing_diawt']+$val['closing_diawt'];

				}

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/headoffice_valut_report/list');

		        $data = array(

		        					'list'  => $po_details,

									'access'=> $access,

									'summary'=> $summary

		        				);

				echo json_encode($data);

			break;

		}

	}

	function weight_gain_loss($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$set_model = "admin_settings_model";

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'reports/weight_gain_loss';

        			$this->load->view('layout/template', $data);

			break;



			case 'ajax':

			    $summary['blc_pcs'] = 0;

			    $summary['blc_gwt'] = 0;

			    $summary['blc_nwt'] = 0;

			    $summary['blc_diawt'] = 0;

			    $summary['lot_pcs']  = 0;

			    $summary['lot_gwt']  = 0;

			    $summary['lot_nwt']    = 0;

			    $summary['lot_dia_wt']    = 0;

			    $summary['tag_pcs']    = 0;

			    $summary['tag_gwt']    = 0;

			    $summary['tag_nwt']    = 0;

			    $summary['tag_dia_wt']    = 0;

			    $summary['gl_pcs']    = 0;

			    $summary['gl_gwt']    = 0;

			    $summary['gl_nwt']    = 0;

			    $summary['gl_diawt']    = 0;

				$po_details=$this->$model->get_weight_gain_loss_report($_POST);



				foreach($po_details as $val)

				{

				    $summary['blc_pcs']=$summary['blc_pcs']+$val['blc_pcs'];

				    $summary['blc_gwt']=$summary['blc_gwt']+$val['blc_gwt'];

				    $summary['blc_nwt']=$summary['blc_nwt']+$val['blc_nwt'];

				    $summary['blc_diawt']=$summary['blc_diawt']+$val['blc_diawt'];

				    $summary['lot_pcs']=$summary['lot_pcs']+$val['lot_pcs'];

				    $summary['lot_gwt']=$summary['lot_gwt']+$val['lot_gwt'];

				    $summary['lot_nwt']=$summary['lot_nwt']+$val['lot_nwt'];

				    $summary['lot_dia_wt']=$summary['lot_dia_wt']+$val['lot_diawt'];

				    $summary['tag_pcs']=$summary['tag_pcs']+$val['tag_pcs'];

				    $summary['tag_gwt']=$summary['tag_gwt']+$val['tag_gwt'];

				    $summary['tag_nwt']=$summary['tag_nwt']+$val['tag_nwt'];

				    $summary['tag_dia_wt']=$summary['tag_dia_wt']+$val['tag_diawt'];

				    $summary['gl_pcs']=$summary['gl_pcs']+$val['gl_pcs'];

				    $summary['gl_gwt']=$summary['gl_gwt']+$val['gl_gwt'];

				    $summary['gl_nwt']=$summary['gl_nwt']+$val['gl_nwt'];

				    $summary['gl_diawt']=$summary['gl_diawt']+$val['gl_diawt'];

				}



			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/weight_gain_loss/list');

		        $data = array(

		        					'list'  => $po_details,

									'access'=> $access,

									'summary'=> $summary

		        				);

				echo json_encode($data);

			break;

		}

	}



	public function getpurchase_po_list(){

		$model=	self::RET_PUR_ORDER_MODEL;

		$data = $this->$model->getPurchasePos($_POST);

		echo json_encode($data);

	}

	function get_karigar_SupplierEntrys()

	{

		$model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_karigar_SupplierEntrys($_POST);

        echo json_encode($data);

	}

	function get_Available_SupplierPo()

	{

		$model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->get_Available_SupplierPo($_POST);

        echo json_encode($data);

	}

	function getKarigarIssueRefNo()

	{

		$model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->getKarigarIssueRefNo($_POST);

        echo json_encode($data);

	}

	function getKarigarMetalIssueLooseStones()

	{

		$model=	self::RET_PUR_ORDER_MODEL;

        $data= $this->$model->getKarigarMetalIssueLooseStones($_POST);

        echo json_encode($data);

	}



	 //Unfixing report

    function approval_unfixing_report($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'reports/approval_unfixing_report';

        			$this->load->view('layout/template', $data);

				break;



			case 'ajax':

					$list=$this->$model->get_approval_rate_unfixed_details($_POST);

				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/approval_unfixing_report/list');

			        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				 );

					echo json_encode($data);

				break;

		}



    }

    //Unfixing report



    //Approval rate fixing

    function approval_rate_fixed($type="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'reports/approval_rate_fixed';

        			$this->load->view('layout/template', $data);

				break;



			case 'ajax':

					$list=$this->$model->get_approval_rate_fixed_details($_POST);

				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/approval_rate_fixed/list');

			        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				 );

					echo json_encode($data);

				break;

		}



    }

    //Approval rate fixing

	//Smith / Company Opening Balance

	function smith_cmpy_op_bal($type = "",$id="",$status="")

	{

		$model =	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;


		switch ($type)

		{

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'smith_cmpy_op_bal/list';
				$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_purchase/smith_cmpy_op_bal/list');
				$this->load->view('layout/template', $data);

			break;

			case 'add':

				$data['main_content'] = self::VIEW_FOLDER . 'smith_cmpy_op_bal/form';

				$this->load->view('layout/template', $data);

			break;

			case 'save':

				//echo"<pre>";print_r($_POST);exit;

				$response_data=array();

				$addData   =  $_POST['smth_cmpy_stk'];

				$ref_no    = $this->$model->generate_opblc_refno();

				$op_date = ($addData['date'] != '' ? date_format(date_create($addData['date']),'Y-m-d') : date("Y-m-d H:i:s"));

				$data = array(

					'ref_no'		    => $ref_no,

					'stock_type'		=> $addData['stock_type'],

					'smith_type'        => $addData['smith_type'],

					'balance_type'	    => $addData['bal_type'],

					'metal_type'	    => $addData['metal_type'],

					'id_metal'          => ($addData['id_metal'] != '' ? $addData['id_metal'] : NULL),

					//'id_category'       => ($addData['id_ret_category'] != '' ? $addData['id_ret_category'] : NULL),

					'id_category'       => ($addData['id_ret_category'] != '' ? $addData['id_ret_category'] : ($addData['metal_type']==2 ? ($addData['old_metal_type'] != '' ? $addData['old_metal_type'] : NULL) : NULL)),

					'id_product'        => ($addData['id_product'] != '' ? $addData['id_product'] : NULL),

					'id_karigar'        => ($addData['id_karigar'] != '' ? $addData['id_karigar'] : NULL),

					'pieces'            => ($addData['op_pcs'] != '' ? $addData['op_pcs'] : NULL),

					'weight'            => ($addData['op_wgt'] != '' ? $addData['op_wgt'] : 0),

					'net_wt'            => ($addData['op_net_wgt'] != '' ? $addData['op_net_wgt'] : 0),

					'dia_wt'            => ($addData['op_dia_wgt'] != '' ? $addData['op_dia_wgt'] : 0),

					'pure_wt'            => ($addData['op_pure_wgt'] != '' ? $addData['op_pure_wgt'] : 0),

					'id_old_metal_catid' => ($addData['old_metal_category'] != '' ? $addData['old_metal_category'] : NULL),

					'uom_id'            => ($addData['op_uom'] !='' ? $addData['op_uom'] : NULL),

					'weight_type'       => ($addData['wt_receipt_type']!='' && $addData['stock_type']==2 && $addData['op_wgt']>0  ? $addData['wt_receipt_type'] : NULL),

					'amount'		    => ($addData['op_amt'] != '' ? $addData['op_amt'] : 0),

					'amount_type'       => ($addData['amt_receipt_type']!='' && $addData['stock_type']==2 && $addData['op_amt']> 0 ? $addData['amt_receipt_type'] : NULL),

					'remarks'           => ($addData['remark'] != '' ? $addData['remark'] : NULL),

					// 'createdon'        => date("Y-m-d H:i:s"),

					'createdon'        => ($op_date != '' ? $op_date : date("Y-m-d H:i:s")),

					'createdby'        => $this->session->userdata('uid'),

				);

				$this->db->trans_begin();

			    $insOrder = $this->$model->insertData($data,'smith_company_op_balance');

				//print_r($this->db->last_query());exit;

			    if($this->db->trans_status()===TRUE)

    			{

    				$this->db->trans_commit();

					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'Smith Company Stock',

						'operation'     => 'Add',

						'record'        => $insOrder,

						'remark'        => 'Smith Company Stock added successfully'

						);

						$this->log_model->log_detail('insert','',$log_data);


    				$this->session->set_flashdata('chit_alert',array('message'=>'Smith Company Stock Added Successfully','class'=>'success','title'=>'Smith/company Op Balance'));

    				$return_data=array('status'=>TRUE,'message'=>'Smith Company Stock Added Successfully..');

    			}

    			else

    			{



    			    echo $this->db->last_query();exit;

    				$this->db->trans_rollback();

    				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Order Instructions'));

    				$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

    			}

    			echo json_encode($return_data);

			break;

			case 'ajax':

				$list=$this->$model->get_smith_cmpy_op_bal_details($_POST);

			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/smith_cmpy_op_bal/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

			case 'edit':

				$data['edit_data'] = $this->$model->get_op_bal_by_id($id);
				if (empty($data['edit_data'])) {
					$this->session->set_flashdata('chit_alert', array('message' => 'Record not found', 'class' => 'danger', 'title' => 'Smith/Company Op Balance'));
					redirect('admin_ret_purchase/smith_cmpy_op_bal/list');
				}
				$data['main_content'] = self::VIEW_FOLDER . 'smith_cmpy_op_bal/form';
				$this->load->view('layout/template', $data);

			break;

			case 'update':

				$response_data = array();
				$addData = $this->input->post('smth_cmpy_stk');
				$op_date = ($addData['date'] != '' ? date_format(date_create($addData['date']), 'Y-m-d') : date('Y-m-d H:i:s'));

				$data = array(
					'stock_type'        => $addData['stock_type'],
					'smith_type'        => $addData['smith_type'],
					'balance_type'      => $addData['bal_type'],
					'metal_type'        => $addData['metal_type'],
					'id_metal'          => ($addData['id_metal'] != '' ? $addData['id_metal'] : NULL),
					'id_category'       => ($addData['id_ret_category'] != '' ? $addData['id_ret_category'] : ($addData['metal_type'] == 2 ? ($addData['old_metal_type'] != '' ? $addData['old_metal_type'] : NULL) : NULL)),
					'id_product'        => ($addData['id_product'] != '' ? $addData['id_product'] : NULL),
					'id_karigar'        => ($addData['id_karigar'] != '' ? $addData['id_karigar'] : NULL),
					'pieces'            => ($addData['op_pcs'] != '' ? $addData['op_pcs'] : NULL),
					'weight'            => ($addData['op_wgt'] != '' ? $addData['op_wgt'] : 0),
					'net_wt'            => ($addData['op_net_wgt'] != '' ? $addData['op_net_wgt'] : 0),
					'dia_wt'            => ($addData['op_dia_wgt'] != '' ? $addData['op_dia_wgt'] : 0),
					'pure_wt'           => ($addData['op_pure_wgt'] != '' ? $addData['op_pure_wgt'] : 0),
					'id_old_metal_catid' => ($addData['old_metal_category'] != '' ? $addData['old_metal_category'] : NULL),
					'uom_id'            => ($addData['op_uom'] != '' ? $addData['op_uom'] : NULL),
					'weight_type'       => ($addData['wt_receipt_type'] != '' && $addData['stock_type'] == 2 && $addData['op_wgt'] > 0 ? $addData['wt_receipt_type'] : NULL),
					'amount'            => ($addData['op_amt'] != '' ? $addData['op_amt'] : 0),
					'amount_type'       => ($addData['amt_receipt_type'] != '' && $addData['stock_type'] == 2 && $addData['op_amt'] > 0 ? $addData['amt_receipt_type'] : NULL),
					'remarks'           => ($addData['remark'] != '' ? $addData['remark'] : NULL),
					'createdon'         => ($op_date != '' ? $op_date : date('Y-m-d H:i:s')),
					'updatedby'         => $this->session->userdata('uid'),
				);

				$this->db->trans_begin();
				$this->$model->update_op_bal($id, $data);

				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();

					$log_data = array(
						'id_log'     => $this->session->userdata('id_log'),
						'event_date' => date('Y-m-d H:i:s'),
						'module'     => 'Smith Company Stock',
						'operation'  => 'Edit',
						'record'     => $id,
						'remark'     => 'Smith Company Stock updated successfully'
					);
					$this->log_model->log_detail('insert', '', $log_data);

					$this->session->set_flashdata('chit_alert', array('message' => 'Opening Balance Updated Successfully', 'class' => 'success', 'title' => 'Smith/Company Op Balance'));
					$return_data = array('status' => TRUE, 'message' => 'Opening Balance Updated Successfully.');
				} else {
					$this->db->trans_rollback();
					$return_data = array('status' => FALSE, 'message' => 'Unable to proceed the requested process');
				}

				echo json_encode($return_data);

			break;

			case 'delete':

				$this->db->trans_begin();
				$this->$model->delete_op_bal($id);

				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();

					$log_data = array(
						'id_log'     => $this->session->userdata('id_log'),
						'event_date' => date('Y-m-d H:i:s'),
						'module'     => 'Smith Company Stock',
						'operation'  => 'Delete',
						'record'     => $id,
						'remark'     => 'Smith Company Stock deleted'
					);
					$this->log_model->log_detail('insert', '', $log_data);

					$return_data = array('status' => TRUE, 'message' => 'Opening Balance Deleted Successfully.');
				} else {
					$this->db->trans_rollback();
					$return_data = array('status' => FALSE, 'message' => 'Unable to delete the record');
				}

				echo json_encode($return_data);

			break;

		}

	}

	//Smith / Company Opening Balance

	function CrDr_noteinv($id)

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$set_model = "admin_settings_model";

        $data['comp_details']   = $this->$set_model->get_company();

		$data['crdrdetails'] = $this->$model->get_crdr_detail($id);

		//echo "<pre>"; print_r($data);exit;

		$this->load->helper(array('dompdf', 'file'));

		$dompdf = new DOMPDF();

		$html = $this->load->view(self::VIEW_FOLDER.'crdr_note/inv_ack', $data,true);

		$dompdf->load_html($html);

		$dompdf->set_paper('A4', "portriat" );

		$dompdf->render();

		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("VendorAck.pdf",array('Attachment'=>0));



	}

	// NonTag Lot Generate

	function nontag_lot_generate($type = "",$id="",$status="")

	{

		$model=	self::RET_PUR_ORDER_MODEL;

		switch($type)

		{

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'nontag_lot_generate/list';

				$this->load->view('layout/template', $data);

			break;



			case 'add':

				$data['main_content'] = self::VIEW_FOLDER . 'nontag_lot_generate/form';

				$this->load->view('layout/template', $data);

			break;

			case 'save':



				$nontag_item_details    =  $_POST['nontag_items'];

				$id_karigar = $_POST['id_karigar'];

				$lotData = [];

				foreach($nontag_item_details as $r)

				{

					$lotData[$r['id_category']][$r['id_purity']][] = $r;

				}

				$ho              =  $this->$model->get_headOffice();

				$dCData          =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

				$bill_date       =  ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

				foreach($lotData as $cat_id =>$category)

				{

					foreach($category as $id_purity =>$product_details)

					{

						$data = array(

							'lot_date'				=> $bill_date,

							'created_branch'		=> (isset($ho['id_branch']) ? $ho['id_branch']:NULL ),

							'lot_received_at'       => $ho['id_branch'],

							'stock_type'            => 2,

							'lot_from'              => 8,

							'gold_smith'			=> $id_karigar,

							'id_category'			=> $cat_id,

							'id_purity'				=> $id_purity,

							'created_on'	  		=> date("Y-m-d H:i:s"),

							'created_by'      		=> $this->session->userdata('uid')

						);

						$this->db->trans_begin();

						$insId = $this->$model->insertData($data,'ret_lot_inwards');

						if($insId)

						{

							foreach($product_details as $items)

							{

								//echo"<pre>";print_r($items);exit;



								$non_tag_data= array(

									'lot_no'	    =>$insId,

									'lot_product'   =>($items['id_product']!='' ? $items['id_product']:NULL),

									'no_of_piece'   =>($items['pcs']!='' ? $items['pcs']:0),

									'gross_wt'      =>($items['grswt']!='' ? $items['grswt']:0),

									'less_wt'       =>($items['less_wt']!='' ? $items['less_wt']:0),

									'net_wt'        =>($items['netwt']!='' ? $items['netwt']:0),

									'lot_id_category'=> $cat_id,

									'lot_id_purity'  => $id_purity,

								);

								$detail_insId = $this->$model->insertData($non_tag_data,'ret_lot_inwards_detail');

								if($detail_insId)

								{

									$existData=array('id_section'=>$items['id_section'],'id_product'=>$items['id_product'],'design'=>$items['id_design'],'id_sub_design'=>$items['id_sub_design'],'id_branch'=>$ho['id_branch']);

									$isExist = $this->$model->checkNonTagItemExist($existData);

									if($isExist['status'] == TRUE)

									{

										$nt_data = array(

											'id_nontag_item'=> $isExist['id_nontag_item'],

											'no_of_piece'	=> $items['pcs'],

											'gross_wt'		=> $items['grswt'],

											'net_wt'		=> $items['netwt'],

											'updated_by'	=> $this->session->userdata('uid'),

											'updated_on'	=> date('Y-m-d H:i:s'),

										);

										$this->$model->updateNTData($nt_data,'-');



										$non_tag_data=array(

											'product'	    => $items['id_product'],

											'design'	    => $items['id_design'],

											'id_sub_design'	=> $items['id_sub_design'],

											'no_of_piece'	=> $items['pcs'],

											'gross_wt'		=> $items['grswt'],

											'net_wt'		=> $items['netwt'],

											'from_branch'	=> $ho['id_branch'],

											'to_branch'	    => NULL,

											'ref_no'	    => $insId,

											'status'	    => 4,

											'date'          => $bill_date,

											'created_on'    => date("Y-m-d H:i:s"),

											'created_by'    =>  $this->session->userdata('uid')

										);

										$this->$model->insertData($non_tag_data,'ret_nontag_item_log');

										$section_nontag_log = array(

											'product'		=> $items['id_product'],

											'design'		=> $items['id_design'],

											'id_sub_design' => $items['id_sub_design'],

											'no_of_piece'	=> $items['pcs'],

											'gross_wt'		=> $items['grswt'],

											'net_wt'		=> $items['netwt'],

											"status"		=> 4,

											"from_branch"	=> $ho['id_branch'],

											"to_branch"		=> NULL,

											"from_section"	=> $items['id_section'],

											"to_section"    => NULL,

											"created_by"	=> $this->session->userdata('uid'),

											"created_on"	=> date('Y-m-d H:i:s'),

											"date"			=> $bill_date,

											);

										$this->$model->insertData($section_nontag_log,'ret_section_nontag_item_log');

									}

								}

							}

						}

					}

				}

				if($this->db->trans_status()===TRUE)

				{

					$log_data = array(

								'id_log'        => $this->session->userdata('id_log'),

								'event_date'	=> date("Y-m-d H:i:s"),

								'module'      	=> 'Non Tag Lot Generate',

								'operation'   	=> 'Add',

								'record'        =>  NULL,

								'remark'       	=> 'Non Tag Lot Generate.'

							);

					$this->log_model->log_detail('insert','',$log_data);



					$this->db->trans_commit();

					$responseData=array('status'=>true,'message'=>'Non Tag Lot Generated successfully.','insId'=>$insId);

				}

				else

				{

					//echo $this->db->last_query();exit;

					$this->db->trans_rollback();

					$responseData=array('status'=>false,'message'=>'Unable to proceed the requested operation.');

				}

				echo json_encode($responseData);

			break;

		}

	}

	// NonTag Lot Generate

	// Nontag Lot Receipt
	function nontag_receipt($type = "",$id="",$status="")
	{
		$model=	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;
		switch($type)
		{
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'nontag_receipt/list';
				$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_purchase/nontag_receipt/list');
				$this->load->view('layout/template', $data);
			break;

			case 'add':
				$data['nontag_weight_per'] = $this->$model->get_ret_settings('nontag_weight_per');
				$data['main_content'] = self::VIEW_FOLDER . 'nontag_receipt/form';
				$this->load->view('layout/template', $data);
				//echo"<pre>";print_r($data);exit;
			break;

			case 'get_NontagLots':
				$data= $this->$model->get_NontagLots();
        		echo json_encode($data);
			break;

			case 'save':
				$response_data=array();
				$dCData          = $this->admin_settings_model->getBranchDayClosingData(1);
			    $bill_date       = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
				$addData   =  $_POST['nt_receipt'];
				$nt_receipt_no = $this->$model->getNonTagReceiptNum();
				$lottedWt = $this->$model->getLottedNotTagWt($addData['id_lot'],$addData['id_product'],$addData['id_design'],$addData['id_sub_design']);
				$receiptedNontagWt = $this->$model->getReceiptedNontagWt($addData['id_lot'],$addData['id_product'],$addData['id_design'],$addData['id_sub_design']);
				if(($receiptedNontagWt + $addData['nt_grswt']) >= $lottedWt['gross_wt'])
				{
					$lot['tag_status']=1;
					$tagInsert= $this->$model->updateData($lot,'id_lot_inward_detail',$lottedWt['id_lot_inward_detail'],'ret_lot_inwards_detail');
				}
				$data = array(
					'lot_id'		=> $addData['id_lot'],
					'id_lot_inward_detail'		=> $addData['id_lot_inward_detail'],
					'nt_receipt_no' => $nt_receipt_no,
					'id_branch'     => $addData['id_branch'],
					'id_section'	=> $addData['id_section'],
					'id_product'    => $addData['id_product'],
					'id_design'     => $addData['id_design'],
					'id_sub_design' => $addData['id_sub_design'],
					'pcs'        	=> ($addData['nt_pcs'] != '' ? $addData['nt_pcs'] : NULL),
					'grs_wt'        => ($addData['nt_grswt'] != '' ? $addData['nt_grswt'] : 0),
					'less_wt'       => ($addData['nt_lesswt'] != '' ? $addData['nt_lesswt'] : 0),
					'net_wt'        => ($addData['nt_netwt'] != '' ? $addData['nt_netwt'] : 0),
					'remark'        => ($addData['remark']!='' ? $addData['remark'] : NULL),
					'created_on'    => date("Y-m-d H:i:s"),
					'created_by'    => $this->session->userdata('uid'),
				);
				$this->db->trans_begin();
			    $insOrder = $this->$model->insertData($data,'ret_nontag_receipt');
				if($insOrder)
				{
					$existData=array('id_section'=>$addData['id_section'],'id_product'=>$addData['id_product'],'id_design'=>$addData['id_design'],'id_branch'=>1,'id_sub_design'=>$addData['id_sub_design']);

					$isExist = $this->$model->checkNonTagItemExist($existData);
					if($isExist['status'] == TRUE)
					{
						$nt_data = array(
							'id_nontag_item' => $isExist['id_nontag_item'],
							'no_of_piece'	=> ($addData['nt_pcs']!='' ? $addData['nt_pcs']:0),
							'gross_wt'		=> $addData['nt_grswt'],
							'net_wt'		=> $addData['nt_netwt'],
							'less_wt'		=> 0,
							'updated_by'	=> $this->session->userdata('uid'),
							'updated_on'	=> date('Y-m-d H:i:s'),
						);
						$update_nt = $this->$model->updateNTData($nt_data,'+');
					}
					else
					{
						$non_tag_data_insert=array(
							'branch'        => 1,
							'id_section'    => $addData['id_section'],
							'product'	    => ($addData['id_product']!='' ? $addData['id_product']:NULL),
							'design'	    => ($addData['id_design']!='' ? $addData['id_design']:NULL),
							'id_sub_design'	=> ($addData['id_sub_design']!='' ? $addData['id_sub_design']:NULL),
							'no_of_piece'	=> ($addData['nt_pcs']!='' ? $addData['nt_pcs']:0),
							'gross_wt'		=> ($addData['nt_grswt']!='' ? $addData['nt_grswt']:NULL),
							'net_wt'		=> ($addData['nt_netwt']!=''? $addData['nt_netwt']:NULL),
							'created_on'    => date("Y-m-d H:i:s"),
						'created_by'    => $this->session->userdata('uid')
						);
						$ins_id = $this->$model->insertData($non_tag_data_insert,'ret_nontag_item');
					}

					$non_tag_data=array(
						'product'	    => ($addData['id_product']!='' ? $addData['id_product']:NULL),
						'design'	    => ($addData['id_design']!='' ? $addData['id_design']:NULL),
						'id_sub_design'	=> ($addData['id_sub_design']!='' ? $addData['id_sub_design']:NULL),
						'no_of_piece'	=> ($addData['nt_pcs']!='' ? $addData['nt_pcs']:0),
						'gross_wt'		=> ($addData['nt_grswt']!='' ? $addData['nt_grswt']:NULL),
						'net_wt'		=> ($addData['nt_netwt']!=''? $addData['nt_netwt']:NULL),
						'from_branch'	=> NULL,
						'to_branch'	    => 1,
						'status'	    => 0,
						'date'          => $bill_date,
						'created_on'    => date("Y-m-d H:i:s"),
						'created_by'    => $this->session->userdata('uid')
					);
					$this->$model->insertData($non_tag_data,'ret_nontag_item_log');

					$item_non_tag_data=array(
						'to_section'	=> $addData['id_section'],
						'from_section'	=> NULL,
						'product'	    => ($addData['id_product']!='' ? $addData['id_product']:NULL),
						'design'	    => ($addData['id_design']!='' ? $addData['id_design']:NULL),
						'id_sub_design'	=> ($addData['id_sub_design']!='' ? $addData['id_sub_design']:NULL),
						'no_of_piece'	=> ($addData['nt_pcs']!='' ? $addData['nt_pcs']:0),
						'gross_wt'		=> ($addData['nt_grswt']!='' ? $addData['nt_grswt']:NULL),
						'net_wt'		=> ($addData['nt_netwt']!=''? $addData['nt_netwt']:NULL),
						'from_branch'	=> NULL,
						'to_branch'	    => 1,
						'status'	    => 0,
						'date'          => $bill_date,
						'created_on'    => date("Y-m-d H:i:s"),
						'created_by'    => $this->session->userdata('uid')
						);
					$this->$model->insertData($item_non_tag_data,'ret_section_nontag_item_log');
				}
			    if($this->db->trans_status()===TRUE)
    			{
    				$this->db->trans_commit();

					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'NonTag Receipt',

						'operation'     => 'Add',

						'record'        =>  $insOrder,

						'remark'        => 'NonTag Receipt added successfully'

						);

						$this->log_model->log_detail('insert','',$log_data);

    				$this->session->set_flashdata('chit_alert',array('message'=>'NonTag Receipt Added Successfully','class'=>'success','title'=>'NonTag Receipt'));
    				$return_data=array('status'=>TRUE,'message'=>'NonTag Receipt Added Successfully..');
    			}
    			else
    			{

    			    echo $this->db->last_query();exit;
    				$this->db->trans_rollback();
    				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'NonTag Receipt'));
    				$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
    			}
    			echo json_encode($return_data);
			break;
			case 'ajax':
				$list=$this->$model->get_nontag_receiptedList($_POST);
			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/nontag_receipt/list');
		        $data = array(
								'list'  => $list,
								'access'=> $access
							);
				echo json_encode($data);
			break;
		}
	}
	function get_lot_nontag_details()
	{
		$model=	self::RET_PUR_ORDER_MODEL;
        $data= $this->$model->get_lot_nontag_details($_POST);
        echo json_encode($data);
	}
	function getNonTagLotItemDetails()
	{
		$model=	self::RET_PUR_ORDER_MODEL;
        $data= $this->$model->getNonTagLotItemDetails($_POST);
        echo json_encode($data);
	}
	// Nontag Lot Receipt

	function isEmptySetDefault($value,$default){

	    if($value != '' &&  $value != NULL &&  $value != 'null' && $value != 'undefined'  ){
	        return $value;
	    }else{
	        return $default;
	    }

	}

	function get_tds_percent()
	{

		$model=	self::RET_PUR_ORDER_MODEL;

		$id_kaigar = $_POST['id_karigar'];

		$fin_year = $this->$model->get_FinancialYear();

		// print_r($fin_year);exit;

		$data['settings'] = $this->$model->get_ret_settings('tds_purchase_limit');

        $data['tds_details']=$this->$model->get_karigar_wise_tds_percent($id_kaigar,$fin_year['fin_year_code']);

		// print_r($this->db->last_query());exit;

		echo json_encode($data);

	}


    function credit_debit_entry($type = "",$id="",$status="")
	{
		$model=	self::RET_PUR_ORDER_MODEL;

		$SETT_MOD = self::SETT_MOD;

		switch($type)
		{

			case 'list':
				$data['main_content'] = self::VIEW_FOLDER .'credit_entry/list';
				$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_purchase/credit_debit_entry/list');				
				$this->load->view('layout/template', $data);

			break;

			case 'add':
				$this->db->where('status',1);
				$data['cr_dr_ledger'] = $this->db->get('ret_crdr_ledger')->result_array();
				$data['main_content'] = self::VIEW_FOLDER . 'credit_entry/form';
				$this->load->view('layout/template', $data);
			break;

			case 'edit':
				$this->db->where('status',1);
				$data['cr_dr_ledger'] = $this->db->get('ret_crdr_ledger')->result_array();
				$data['credit'] = $this->$model->get_credit_debit($id);
				$data['main_content'] = self::VIEW_FOLDER . 'credit_entry/form';
				$this->load->view('layout/template', $data);
			break;

			case 'cd_edit':
				$data['credit'] = $this->$model->get_credit_debit($id);
				echo json_encode($data);
			break;
			case 'save':
				$response_data=array();
				if($_POST['transactionin'] == '1')
					$transtype = $_POST['transtype'];
				else
					$transtype = $_POST['wtranstype'];

				$bill_no = $this->$model->code_number_generator();   //Bill Number Generate
				$data = array(
					'transbillno'     => $bill_no,
					'po_id'			=> ($_POST['po_id']!=''? $_POST['po_id']:NULL),
					'id_supplier_rate_cut' => ($_POST['ratecut']!=''? $_POST['ratecut']:NULL),
					'supid'          => ($_POST['karigar']!=''? $_POST['karigar']:NULL),
					'accountto'		 => ($_POST['accountto']!=''? $_POST['accountto']:NULL),
					'transtype'     => $transtype,
					'transamount'	 => ($_POST['transamount']!=''? $_POST['transamount']:NULL),
					'weight'	 => ($_POST['transweight']!=''? $_POST['transweight']:NULL),
					'id_cr_dr_ledger' => (isset($_POST['id_cr_dr_ledger']) && $_POST['id_cr_dr_ledger']!='' ? $_POST['id_cr_dr_ledger']:NULL),
					'naration'       => ($_POST['naration']!=''? $_POST['naration']:NULL),
					'createdon'     => date("Y-m-d H:i:s"),
					'createdby'     => $this->session->userdata('uid'),
				);
				$this->db->trans_begin();
			    $insOrder = $this->$model->insertData($data,'ret_crdr_note');

			    if($this->db->trans_status()===TRUE)
    			{
    				$this->db->trans_commit();

					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'Credit/Debit Entry',

						'operation'     => 'Add',

						'record'        =>  $insOrder,

						'remark'        => 'Credit/Debit Entry added successfully'

						);

					$this->log_model->log_detail('insert','',$log_data);
    				$this->session->set_flashdata('chit_alert',array('message'=>'Credit/Debit Entry Added Successfully','class'=>'success','title'=>'Credit/Debit Entry'));
    				$return_data=array('status'=>TRUE,'message'=>'Credit/Debit Entry Added Successfully..');
    			}
    			else
    			{

    			    echo $this->db->last_query();exit;
    				$this->db->trans_rollback();
    				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Credit/Debit Entry'));
    				$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
    			}
    			echo json_encode($return_data);
			break;

			case 'credit_debit_acknolodgement':

			    $set_model = "admin_settings_model";

        		$data['comp_details']   = $this->$set_model->get_company();

        		$data['paymentdetails'] = $this->$model->get_credit_debit_detail($id);

				// $data['payment'] = $this->$model->get_po_paid_payment($id);
        		// echo "<pre>"; print_r($data);exit;

        		$this->load->helper(array('dompdf', 'file'));

        	        $dompdf = new DOMPDF();

        			$html = $this->load->view(self::VIEW_FOLDER.'credit_entry/print', $data,true);

        			$dompdf->load_html($html);

        			$dompdf->set_paper('A4', "portriat" );

        			$dompdf->render();

        			while(ob_get_level()) ob_end_clean();
        			$dompdf->stream("VendorAck.pdf",array('Attachment'=>0));

			break;

			case 'cancel_crdr_entry':

			    $ho              =  $this->$model->get_headOffice();

        		$dCData          =  $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

        		$bill_date       =  ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

        		$crdrid          = $_POST['crdrid'];

			    $updData=array(

                    'crdr_status'        => 2,

                    'cancel_reason'     => $_POST['cancel_reason'],

                    'cancelled_date'    => $bill_date,

                    'cancelled_by'      => $this->session->userdata('uid')

                    );

                    $this->db->trans_begin();

                    $ret_status = $this->$model->updateData($updData,'crdrid',$crdrid,'ret_crdr_note');

					// print_r($this->db->last_query());exit;

                    if($this->db->trans_status()===TRUE)

                    {

                        $this->db->trans_commit();

                        $this->session->set_flashdata('chit_alert',array('message'=>'Credit/Debit Entry Cancelled Successfully','class'=>'success','title'=>'Credit/Debit Entry'));

                        $return_data=array('status'=>TRUE,'message'=>'Order Instructions Added Successfully..');

                    }

                    else

                    {

                        echo $this->db->last_query();exit;

                        $this->db->trans_rollback();

                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Credit/Debit Entry'));

                        $return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

                    }

                echo json_encode($return_data);

			break;

			case 'update':
				$response_data=array();
				$data = array(
					'supid'          => ($_POST['karigar']!=''? $_POST['karigar']:NULL),
					'accountto'		 => ($_POST['accountto']!=''? $_POST['accountto']:NULL),
					'transtype'     => ($_POST['transtype']!=''? $_POST['transtype']:NULL),
					'transamount'	 => ($_POST['transamount']!=''? $_POST['transamount']:NULL),
					'weight'	 => ($_POST['transweight']!=''? $_POST['transweight']:NULL),
					'id_cr_dr_ledger' => (isset($_POST['id_cr_dr_ledger']) && $_POST['id_cr_dr_ledger']!='' ? $_POST['id_cr_dr_ledger']:NULL),
					'naration'       => ($_POST['naration']!=''? $_POST['naration']:NULL),
					'updatedon'     => date("Y-m-d H:i:s"),
					'updatedby'     => $this->session->userdata('uid'),
				);
				$this->db->trans_begin();
            	$update_status = $this->$model->updateData($data,'crdrid',$_POST['crdrid'],'ret_crdr_note');
				// print_r($this->db->last_query());exit;
			    if($this->db->trans_status()===TRUE)
    			{
    				$this->db->trans_commit();
					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'Credit/Debit Entry',

						'operation'     => 'Update',

						'record'        => $update_status ,

						'remark'        => 'Credit/Debit Entry Updated successfully'

						);

			        $this->log_model->log_detail('insert','',$log_data);
    				$this->session->set_flashdata('chit_alert',array('message'=>'Credit/Debit Entry Added Successfully','class'=>'success','title'=>'Credit/Debit Entry'));
    				$return_data=array('status'=>TRUE,'message'=>'Credit/Debit Entry Updated Successfully..');
    			}
    			else
    			{

    			    echo $this->db->last_query();exit;
    				$this->db->trans_rollback();
    				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Credit/Debit Entry'));
    				$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
    			}
    			echo json_encode($return_data);
			break;

			case 'ajax':
				$list=$this->$model->get_creditdebit_entry($_POST);
			  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/credit_debit_entry/list');
		        $data = array(
								'list'  => $list,
								'access'=> $access
							);
				echo json_encode($data);
			break;
		}

	}


	function get_img_by_grn_id()

    {

	    $model=	self::RET_PUR_ORDER_MODEL;

        $data = $this->$model->get_grn_images($_POST['grn_id']);

        echo json_encode($data);

    }

	// Retagging report
	function retagging_report($type="")
	{
		$model=	self::RET_PUR_ORDER_MODEL;
		switch($type)
		{
			case 'list':
					$data['main_content'] = self::VIEW_FOLDER.'reports/retagging';
        			$this->load->view('layout/template', $data);
				break;
			case 'ajax':
					$list=$this->$model->get_retagging_details($_POST);
				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/retagging/list');
			        $data = array(
		        					'list'  => $list,
									'access'=> $access
		        				 );
					echo json_encode($data);
				break;
		}
    }

		// Retagging report
		function metal_issue_receipt($type="")
		{
			$model=	self::RET_PUR_ORDER_MODEL;
			switch($type)
			{

				case 'add':
						$data['main_content'] = self::VIEW_FOLDER.'metalissue/receipt_form';
						$this->load->view('layout/template', $data);
					break;
				case 'get_metal_issue_details':
					$list=$this->$model->get_metal_issue_details($_POST);
						$access = $this->admin_settings_model->get_access('admin_ret_reports/retagging/list');
					$data = array(
									'list'  => $list,
									'access'=> $access
									);
					echo json_encode($data);
				break;
				case 'getKarigarIssueRefNo':
					$model=	self::RET_PUR_ORDER_MODEL;

					$data= $this->$model->getKarigarIssueRef_No($_POST);

					echo json_encode($data);
				break;
				case 'save':

					$receipt_date = ($_POST['receipt_date']!='' ? date("Y-m-d", strtotime($_POST['receipt_date']))." ".date("H:i:s") :  date("Y-m-d H:i:s"));

					$insData=array(

								  'id_metal_issue'=>$_POST['id_metal_issue'],

								  'receipt_date' => $receipt_date,

								  'created_by' => $this->session->userdata('uid')

								  );

					 $this->db->trans_begin();

					 $insId = $this->$model->insertData($insData,'ret_metai_issue_receipt');

					 foreach($_POST['issue'] as $key => $val)

					 {

						 $order_details=$this->$model->get_order_details($val['issue_met_id']);

						 

						 $metal_data=array(

							 'id_issue_receipt'      =>$insId,

							 'issue_met_id'          =>$val['issue_met_id'],

							 'product_id'          =>$val['id_product'],

							 'design_id'       =>$val['id_design'],

							 'sub_design_id'   =>$val['id_sub_design'],

							 'weight'           =>$val['receipt_gwt'],

							 'net_wt'           =>$val['receipt_nwt'],

							 'less_wt'           =>$val['receipt_lwt'],

							 'piece'           =>$val['receipt_pcs'],


						 );

						$status=$this->$model->insertData($metal_data,'ret_metai_issue_receipt_details');

						
						
						$stones=[];

						$stone_details = json_decode($val['stone_details']);

					   if(sizeof($stone_details)>0)

					   {

						   

						   foreach($stone_details as $stone)

						   {

							   $stones=array(

								   'id_receipt_details'=>$status,

								   'stone_pcs'	     => $stone->stone_pcs,

								   'stone_wt'		=> $stone->stone_wt,

								   'uom_id'		=> $stone->stone_uom_id,

								   'stone_id'		=> $stone->stone_id,

								   'stone_quality_id'=> $stone->stone_quality_id,

								   'is_apply_in_lwt '=> $stone->show_in_lwt,

								   'stone_cal_type  '=> $stone->stone_cal_type,

								   'rate_per_gram'=> $stone->stone_rate,

								   'price'=> $stone->stone_price,

							   );

							  // echo "<pre>";print_r($stones);exit;

							  $this->$model->insertData($stones,'ret_metai_issue_receipt_stone_details');

						   }



					   }


						// $received=$this->$model->get_issue_received_wt($val['issue_met_id']);

						// echo $received;

						// print_r($order_details);exit;


						//  if($order_details['weight']<=$received){
 
							$updData=array(
								"completed_weight"=> $val['receipt_gwt'],
								"orderstatus" 	 =>  4,
								);
								$this->db->trans_begin();
								$status=$this->$model->updateData($updData,'id_orderdetails',$order_details['id_orderdetails'],'customerorderdetails');


						//  }


					 }




					if($this->db->trans_status()===TRUE)

					{

						$this->db->trans_commit();

						  $log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'ORDER DESCRIPTION',

							'operation'     => 'Add',

							'record'        => $insOrder,

							'remark'        => 'Order Description added successfully'

							);

						$this->log_model->log_detail('insert','',$log_data);

						$this->session->set_flashdata('chit_alert',array('message'=>'Order Instructions Added Successfully','class'=>'success','title'=>'Order Instructions'));

						$return_data=array('status'=>TRUE,'message'=>'Receipt Added Successfully..');

					}

					else

					{

						echo $this->db->last_query();exit;

						$this->db->trans_rollback();

						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Order Instructions'));

						$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

					}

					echo json_encode($return_data);

				break;

			}
		}


	function delete_supplier_entry_item()
	{

		$model=	self::RET_PUR_ORDER_MODEL;
		$po_item_id = $this->input->post('po_item_id');

		$this->db->trans_begin();

		$this->$model->deleteData('po_item_id',$po_item_id,'ret_purchase_order_items');
		$this->$model->deleteData('pur_po_item_id',$po_item_id,'ret_purchase_other_charges');
		$this->$model->deleteData('po_item_id',$po_item_id,'ret_po_stone_items');

		

		if($this->db->trans_status()===TRUE)
		{
			$this->db->trans_commit();

			$return_data =  array('status'=>true,'message' => 'Supplier entry deleted successfully','class' => 'success','title'=>'Delete Supplier Entry');

			echo json_encode($return_data);
		}
		else
		{
			$this->db->trans_rollback();

			$return_data = array('status'=>false,'message' => 'Unable to proceed your request','class' => 'danger','title'=>'Delete Supplier Entry');
		
			echo json_encode($return_data);
		}
	}


	function qc_issue_receipt_cancel()
	{
		$model =	self::RET_PUR_ORDER_MODEL;
		$qc_issue_id = $this->input->post('qc_issue_id');
		$id_reason = $this->input->post('id_reason');


		$updData = array(

			'id_reason' => $id_reason,

			'cancelled_on' => date("Y-m-d H:i:s"),

			'cancelled_by' => $this->session->userdata('uid')

		);

		$this->db->trans_begin();

		$id_order_des = $this->$model->updateData($updData, 'qc_process_id', $qc_issue_id, 'ret_po_qc_issue_process');

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$this->session->set_flashdata('chit_alert', array('message' => 'Cancelled Successfully', 'class' => 'success', 'title' => 'Cancel QC Issue/Receipt'));

			$return_data = array('status' => TRUE, 'message' => 'Cancelled Successfully..');
		} else {



			echo $this->db->last_query();
			exit;

			$this->db->trans_rollback();

			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Cancel QC Issue/Receipt'));

			$return_data = array('status' => FALSE, 'message' => 'Unable to proceed the requested process');
		}

		echo json_encode($return_data);
	}

	function karigar_pan_available()
	{

		$model =	self::RET_PUR_ORDER_MODEL;

		$data = $this->$model->karigar_pan_available($_POST['pan_number'], $_POST['id_karigar']);

		echo json_encode($data);
	}

	function karigar_gst_available()
	{

		$model =	self::RET_PUR_ORDER_MODEL;

		$data = $this->$model->karigar_gst_available($_POST['gst_number'], $_POST['id_karigar']);

		echo json_encode($data);
	}

	function karigar_aadhar_available()
	{

		$model =	self::RET_PUR_ORDER_MODEL;

		$data = $this->$model->karigar_aadhar_available($_POST['aadhar_no'], $_POST['id_karigar']);

		echo json_encode($data);
	}
	// Pending payment PO Bills Report
	function getPending_payment_po_bills()
	{
		$model=	self::RET_PUR_ORDER_MODEL;
		// echo "<pre>";print_r($_POST);exit;
		$list = $this->$model->getPending_payment_po_bills($_POST);
		echo json_encode($list);
	}

	function getPending_payment_po_bills_for_payment()
	{
		$model=	self::RET_PUR_ORDER_MODEL;
		$list = $this->$model->getPending_payment_po_bills_for_payment($_POST);
		$kar_det = $this->$model->get_karigar_details($_POST['id_karigar']);
		echo(json_encode($list)."[~s~]".json_encode($kar_det));
	}

	function get_crdr_debit_entries_for_payment()
	{
		$model=	self::RET_PUR_ORDER_MODEL;
		$list = $this->$model->get_crdr_debit_entries_without_po($_POST);
		echo json_encode($list);
	}
	
	function get_pending_po_bills_after_tagging_without_pcs_with_weight()
	{
		$model=	self::RET_PUR_ORDER_MODEL;
		// echo "<pre>";print_r($_POST);exit;
		$list = $this->$model->get_pending_po_bills_after_tagging_without_pcs_with_weight($_POST);
		echo json_encode($list);
	}
}


?>