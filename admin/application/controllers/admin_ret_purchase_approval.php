<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/dompdf/autoload.inc.php');

use Dompdf\Dompdf;

class Admin_ret_purchase_approval extends CI_Controller {

	const VIEW_FOLDER = 'ret_purchase/';

	const RET_PUR_ORDER_MODEL = 'ret_purchase_approval_model'; 

	const SETT_MOD	= "admin_settings_model";

	const IMG_PATH  = 'assets/img/';

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model(self::RET_PUR_ORDER_MODEL); 

		$this->load->model("admin_settings_model"); 

		$this->load->model(self::SETT_MOD);

		$this->load->model("log_model");

		$this->load->model("admin_usersms_model");

		$this->load->model('email_model');

		$this->load->model("sms_model");

		$this->load->model("ret_reports_model");

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

		$set_model          ="admin_settings_model";

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'purchase_entry_list';

        			$this->load->view('layout/template', $data);

			break;

			

			case 'pur_order':

					$data['main_content'] = self::VIEW_FOLDER.'pur_order_list';

        			$this->load->view('layout/template', $data);

			break;

			

			case 'add':

			    

			        $data['po_item']=$this->$model->get_empty_record();

					$data['main_content'] = self::VIEW_FOLDER.'order_form';

        			$this->load->view('layout/template', $data);

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

		    //print_r($_POST);exit;

            $addData   = $_POST['order'];

            $order_details   = $_POST['order_details'];

            

			$fin_year       = $this->$model->get_FinancialYear();

			$order_no       = $this->$model->generatePurNo();



 			$order = array( 

 			    'fin_year_code'     => $fin_year['fin_year_code'],

 				'pur_no'            => $order_no,

 				'order_status'		=> 3,

 				'order_type'		=> 1,

 				'order_pcs'			=> $addData['order_pcs'],

 				'order_approx_wt'	=> $addData['order_wt'],

 				'order_for'			=> $addData['order_for'],

 				'id_karigar'		=> $addData['id_karigar'],

 				'cus_ord_ref'		=> ($addData['id_customer_order']!='' && $addData['id_customer_order']!='null' ? $addData['id_customer_order'] :NULL),

 				'order_date'		=> date("Y-m-d H:i:s"),

				'createdon'         => date("Y-m-d H:i:s"),

				'order_taken_by'    => $this->session->userdata('uid')

			);

			$this->db->trans_begin();

			$insOrder = $this->$model->insertData($order,'customerorder');

		    //print_r($this->db->last_query());exit;

		    if($insOrder)

		    {

		        if($addData['id_customer_order']!='')

		        {

				    $this->$model->updateData(array('orderstatus'=>3),'id_customerorder',$addData['id_customer_order'],'customerorderdetails'); // For Customer Order Status

		        }

		        		

		        if(!empty($order_details))

		        {

		            foreach($order_details['product'] as $key => $val)

		            {

		               

		                $smith_due_date = (!empty($addData['smith_due_dt']) ?date('Y-m-d',strtotime(str_replace("/","-",$addData['smith_due_dt']))):NULL ); 

		                

		                $orderDetail=array(

		                                  'id_customerorder'=> $insOrder, 

		                                  'id_product'      => $order_details['product'][$key], 

		                                  'design_no'       => ($order_details['design'][$key]!='' ? $order_details['design'][$key]:NULL), 

		                                  'id_sub_design'   => ($order_details['sub_design'][$key]!='' ? $order_details['sub_design'][$key]:NULL), 

		                                  'id_weight_range' => ($order_details['weight_range'][$key]!='' && $order_details['weight_range'][$key]!='null' ? $order_details['weight_range'][$key] :NULL), 

		                                  'weight'          => ($order_details['order_wt'][$key]!='' ? $order_details['order_wt'][$key]:NULL), 

		                                  'size'            => ($order_details['size'][$key]!='' && $order_details['size'][$key]!='null' ? $order_details['size'][$key] :NULL), 

		                                  'description'     => ($order_details['description'][$key]!='' ? $order_details['description'][$key] :NULL), 

		                                  'totalitems'      => $order_details['piece'][$key], 

		                                  'smith_due_date'  => $smith_due_date, 

		                                  'orderstatus'    =>3,

		                                  );

		                  $id_order_Details=$this->$model->insertData($orderDetail,'customerorderdetails');

		                  //print_r($this->db->last_query());exit;

		                  if($id_order_Details)

		                  {

		                      

		                    $img_details=json_decode(rawurldecode($order_details['order_images'][$key])); 

		                    $img = json_decode(($img_details));

		                    $_FILES['order_image']    =  array();

		                    foreach($img as $image)

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

			    //echo "<pre>";print_r($_POST);echo "</pre>";exit;

			    $orderData=$_POST['order'];

			    $orderDetails=$_POST['order_item'];

			    

                $ho              = $this->$model->get_headOffice();

                $fin_year       = $this->$model->get_FinancialYear();

                $dCData          = $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

				$bill_date       = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

				

				$karigar_details=$this->$model->get_karigar_details($orderData['id_karigar']);

				

				/*1.IF APPROVAL STOCK -  PA

				2.AGANINST ORDER - IF REGISTERED KARIGAR - P , NON REGISTERED PM*/

				

				if(!empty($orderData['purchase_type'])){

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

				}

				

				

     			$order = array( 

     				'po_ref_no'         => $po_ref_no,

     				'fin_year_code'		=> $fin_year['fin_year_code'],

     				'po_grn_id'         => $orderData['id_grn'],

     				//'purchase_order_no' => ($orderData['purchase_type']==1 ?$orderData['purchase_order_no'] :NULL), //purchase_type=>1-Aganist Order

     				'is_suspense_stock' => ($orderData['purchase_type']==1 ? 0 :$orderData['is_suspense_stock']),

     				'isratefixed'       => $_POST['is_rate_fixed'],

     				'gst_bill_type'     => $karigar_details['karigar_type'],

     				'purchase_type'     => !empty($orderData['purchase_type']) ? $orderData['purchase_type'] : $orderData['po_type'],

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

    				'tds_tax_value'     => $orderData['tds_tax_value']

    			);

    			//echo"<pre>";print_r($orderDetails);exit;

    			$this->db->trans_begin();

    			$insOrder = $this->$model->insertData($order,'ret_purchase_order');

    			if($insOrder)

    			{

    			    

    			     //Update Order Status

    			    if($orderData['purchase_type']==1)

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

    

    			    

    			     if(!empty($orderData['other_charges_details'])){

    			        $charge_details = json_decode($orderData['other_charges_details'],true);

                    	foreach($charge_details as $charges)

                    	{

                        	$charge_data = array(

                            	'pur_othr_po_id'            => $insOrder,

                            	'pur_othr_charge_id'        => $charges['charge_id'],

                            	'pur_othr_charge_value'     => $charges['charge_value'],

                        	);

                        	$stoneInsert = $this->$model->insertData($charge_data, 'ret_purchase_other_charges');

                        	//echo $this->db->last_query();exit;

                    	}

    			        

    			    }

    			    

    			    //print_r($orderDetails);exit;

    			    

    			    if(!empty($orderDetails))

    			    {

    			        	foreach($orderDetails['id_category'] as $key => $val){

    			        	   

    			        	    $arrayItems=array(

    			        	                      'po_item_po_id'       =>$insOrder,

    			        	                      'po_item_cat_id'      =>$orderDetails['id_category'][$key],

    			        	                      'po_order_no'         => $orderDetails['po_order_no'][$key],

    			        	                      'po_purchase_mode'    => $orderDetails['po_purchase_mode'][$key],

    			        	                      'po_item_pro_id'      =>($orderDetails['id_product'][$key] !='' && $orderDetails['id_product'][$key]!='null' ? $orderDetails['id_product'][$key]:NULL),

    			        	                      'po_item_des_id'      =>($orderDetails['id_design'][$key]!='' && $orderDetails['id_design'][$key]!='null' ? $orderDetails['id_design'][$key]:NULL),

    			        	                      'po_item_sub_des_id'  =>($orderDetails['id_sub_design'][$key]!='' && $orderDetails['id_sub_design'][$key]!='null' ? $orderDetails['id_sub_design'][$key]:NULL),

    			        	                      'id_purity'           =>($orderDetails['id_purity'][$key]!='' ? $orderDetails['id_purity'][$key]:NULL),

    			        	                      'gross_wt'            =>$orderDetails['tot_gwt'][$key],

    			        	                      'less_wt'             =>($orderDetails['tot_lwt'][$key]!='' ? $orderDetails['tot_lwt'][$key] :0),

    			        	                      'net_wt'              =>$orderDetails['tot_nwt'][$key],

    			        	                      'item_wastage'        =>($orderDetails['tot_wastage_perc'][$key]!='' ?$orderDetails['tot_wastage_perc'][$key] :NULL),

    			        	                      'no_of_pcs'           =>($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

    			        	                      'is_suspense_stock'   =>$orderDetails['is_suspense_stock'][$key],

    			        	                      'is_halmarked'        =>$orderDetails['is_halmerked'][$key],

    			        	                      'is_halmark_from'     =>($orderDetails['is_halmerked'][$key]==1 ? $orderDetails['is_halmerked'][$key]:NULL),

    			        	                      'is_rate_fixed'       => empty($orderDetails['rate_per_gram'][$key]) ? 0 : $orderDetails['is_rate_fixed'][$key],

    			        	                      'fix_rate_per_grm'    =>($orderDetails['rate_per_gram'][$key]!='' ? $orderDetails['rate_per_gram'][$key] :NULL),

    			        	                      'mc_value'            =>($orderDetails['mc_value'][$key]!='' ? $orderDetails['mc_value'][$key] :NULL),

    			        	                      'purchase_touch'      =>($orderDetails['purchase_touch'][$key]!='' ? $orderDetails['purchase_touch'][$key] :NULL),

    			        	                      'mc_type'             =>$orderDetails['mc_type'][$key],

    			        	                      'item_cost'           =>($orderDetails['item_cost'][$key]!='' ?$orderDetails['item_cost'][$key] :0),

    			        	                      'item_pure_wt'        =>($orderDetails['tot_purewt'][$key]!='' ? $orderDetails['tot_purewt'][$key]:0),

    			        	                      'uom'                 => ($orderDetails['gwt_uom'][$key]!='' ? $orderDetails['gwt_uom'][$key]:0),

    			        	                      'cal_type'            => ($orderDetails['cal_type'][$key]!='' ? $orderDetails['cal_type'][$key]:0),

    			        	                      'remark'              => ($orderDetails['remark'][$key]!='' ? $orderDetails['remark'][$key]:NULL),

    			        	                     );

    			        	    $itemStatus=$this->$model->insertData($arrayItems,'ret_purchase_order_items');

    			        	    

    			        	    if($itemStatus)

    			        	    {

    			        	        

                                      //Bullion Purchase

                    			    if($orderData['po_type']==2)

                    			    {

                    			        

                    			           /*//Insert into Purchase Item Log Table

                                            $salesReturnLog=array(

                                                'id_product'  =>$orderDetails['id_product'][$key],

                                                'piece'       =>$orderDetails['tot_pcs'][$key],

                                                'gross_wt'    =>$orderDetails['tot_gwt'][$key],

                                                'less_wt'     =>($orderDetails['tot_lwt'][$key]!='' ? $orderDetails['tot_lwt'][$key] :0),

                                                'net_wt'      =>($orderDetails['tot_nwt'][$key]!='' ? $orderDetails['tot_nwt'][$key] :0),

                                                'from_branch' =>NULL,

                                                'to_branch'   =>$ho['id_branch'],

                                                'status'      =>1,//Inward

                                                'item_type'   =>6, // Bullion Purchase

                                                'date'        =>$bill_date,

                                                'created_on'  =>date("Y-m-d H:i:s"),

                                                'created_by'  =>$this->session->userdata('uid'),

                                                );

                                            $this->$model->insertData($salesReturnLog,'ret_purchase_items_log');

                                            

                                             //UPDATE INTO PURCHASE ITEM STOCK SUMMARY

    			        	        

    			        	            $purity_details=$this->$model->get_purity_details($orderDetails['id_purity'][$key]);

        			        	        $itemExistData=array('id_product'=>$orderDetails['id_product'][$key],'id_branch'=>$ho['id_branch'],'purity'=>$purity_details['purity']); 

        			        	        $is_po_item_exist = $this->$model->checkPurchaseItemStockExist($itemExistData); //CHECK ITEM EXISTS IN TABLE 

        			        	        //print_r($is_po_item_exist);exit;

        			        	        $pur_item_stock_summary = array(

      										        'id_branch'	        => $ho['id_branch'],

      										        'purity'            => $purity_details['purity'],

      										        'id_product'	    => $orderDetails['id_product'][$key],

      										        'id_ret_category'   => $orderDetails['id_category'][$key],

      										        'pieces'		    => ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),  

      										        'gross_wt'		    => $orderDetails['tot_gwt'][$key],  

      										        'less_wt'		    => 0,  

    												'net_wt'		    => $orderDetails['tot_gwt'][$key],  

    												);

    									if($is_po_item_exist['status']) //IF ITEM EXISTS ALREADY IN TABLE

    									{

    									    	 $pur_item_stock_summary['updated_by']=$this->session->userdata('uid');

    									    	 $pur_item_stock_summary['updated_on']=date('Y-m-d H:i:s');

    											 $pur_stock_Status=$this->$model->updatePurItemData($is_po_item_exist['id_stock_summary'],$pur_item_stock_summary,'+');

    											 $id_stock_summary=$is_po_item_exist['id_stock_summary'];

    											 

    									}

    									else // INSERT INTO PURCHASE ITEM STOCK SUMMARY

    									{

    									    $pur_item_stock_summary['created_by']=$this->session->userdata('uid');

    									    $pur_item_stock_summary['created_on']=date('Y-m-d H:i:s');

    									    $id_stock_summary=$this->$model->insertData($pur_item_stock_summary,'ret_purchase_item_stock_summary');

    									}



    									 if($id_stock_summary)

										 {

										        $stock_log_data=array(

                                                'id_stock_summary'=>$id_stock_summary,

                                                'date_add'        =>date('Y-m-d H:i:s'),

                                                'ref_no'          =>$insOrder,

                                                'piece'           =>($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                'gross_wt'        =>$orderDetails['tot_gwt'][$key],

                                                'net_wt'          =>$orderDetails['tot_gwt'][$key],

                                                'transcation_type'=>0,

                                                'credit_type'     =>1,

                                                'remarks'         =>'FROM SUPPLIER BILL ENTRY'

                                                );

                                                $this->$model->insertData($stock_log_data,'ret_purchase_item_stock_summary_log');

										 }*/

            					    //UPDATE INTO PURCHASE ITEM STOCK SUMMARY

            					    

            					    

            					    $lot_data = array(

                                            'lot_date'				=> date("Y-m-d H:i:s"),

                                            'created_branch'		=> $ho['id_branch'],

                                            'lot_received_at'       => $ho['id_branch'],

                                            'stock_type'            => 2,

                                            'lot_from'              => 2,

                                            'po_id'                 => $insOrder,

                                            'gold_smith'			=> $orderData['id_karigar'],

                                            'id_category'			=> $orderDetails['id_category'][$key],

                                            'id_purity'				=> $orderDetails['id_purity'][$key],

                                            'narration'				=> 'From Supplier Bill Entry',

                                            'created_on'	  		=> date("Y-m-d H:i:s"),

                                            'created_by'      		=> $this->session->userdata('uid')

                                            ); 

                                           $lotId = $this->$model->insertData($lot_data,'ret_lot_inwards');

                                           if($lotId)

                                           {

                                               $item_details = array(

                                                    'lot_no'	    => $lotId,

                                                    'lot_product'   => $orderDetails['id_product'][$key],

                                                    'no_of_piece'   => ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                    'gross_wt'      => $orderDetails['tot_gwt'][$key],

                                                    'less_wt'       => 0,

                                                    'net_wt'        => $orderDetails['tot_gwt'][$key],

                                                ); 

                                                $detail_insId = $this->$model->insertData($item_details,'ret_lot_inwards_detail'); 

                                           }

            					           $existData=array('id_product'=>$orderDetails['id_product'][$key],'id_branch'=>$ho['id_branch']);

                                    						        

            						        $isExist = $this->$model->checkNonTagItemExist($existData);

            						        

            						        if($isExist['status'] == TRUE)

            						        {

            						            $nt_data = array(

                                                'id_nontag_item'=> $isExist['id_nontag_item'],

                                                'gross_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'net_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'no_of_piece'	=> ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                'updated_by'	=> $this->session->userdata('uid'),

                                                'updated_on'	=> date('Y-m-d H:i:s'),

                                                );

                                                $this->$model->updateNTData($nt_data,'+');

                                    													

                                                $non_tag_data=array(

                                                'product'	    => $orderDetails['id_product'][$key],

                                                'gross_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'net_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'no_of_piece'	=> ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                'to_branch'	    => $ho['id_branch'],

                                                'from_branch'	=> NULL,

                                                'ref_no'	    => $insOrder,

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

                                                    'product'	    => $orderDetails['id_product'][$key],

                                                    'gross_wt'		=> $orderDetails['tot_gwt'][$key],

                                                    'net_wt'		=> $orderDetails['tot_gwt'][$key],

                                                    'no_of_piece'	=> ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                    'created_on'    => date("Y-m-d H:i:s"),

                                                    'created_by'    => $this->session->userdata('uid')

                                                );

                                                $this->$model->insertData($nt_data,'ret_nontag_item'); 

                                                

                                                $non_tag_data=array(

                                                'product'	    => $orderDetails['id_product'][$key],

                                                'gross_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'net_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'no_of_piece'	=> ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                'from_branch'	=> NULL,

                                                'to_branch'	    => $ho['id_branch'],

                                                'status'	    => 0,

                                                'ref_no'	    => $insOrder,

                                                'date'          => $bill_date,

                                                'created_on'    => date("Y-m-d H:i:s"),

                                                'created_by'    =>  $this->session->userdata('uid')

                                                );

                                                $this->$model->insertData($non_tag_data,'ret_nontag_item_log'); 

            						        }

            						        

            					    

                    			    }

                    			    //Bullion Purchase

                    			    

                    			    

                    			     //Stone Purchase

                    			    if($orderData['po_type']==3)

                    			    {

                    			        

                    			           //Insert into Purchase Item Log Table

                                            $salesReturnLog=array(

                                                'id_product'  =>$orderDetails['id_product'][$key],

                                                'piece'       =>$orderDetails['tot_pcs'][$key],

                                                'gross_wt'    =>$orderDetails['tot_gwt'][$key],

                                                'less_wt'     =>($orderDetails['tot_lwt'][$key]!='' ? $orderDetails['tot_lwt'][$key] :0),

                                                'net_wt'      =>($orderDetails['tot_nwt'][$key]!='' ? $orderDetails['tot_nwt'][$key] :0),

                                                'from_branch' =>NULL,

                                                'to_branch'   =>$ho['id_branch'],

                                                'status'      =>1,//Inward

                                                'item_type'   =>6, // Stone Purchase

                                                'date'        =>$bill_date,

                                                'created_on'  =>date("Y-m-d H:i:s"),

                                                'created_by'  =>$this->session->userdata('uid'),

                                                );

                                            $this->$model->insertData($salesReturnLog,'ret_purchase_items_log');

                                            

                                             //UPDATE INTO PURCHASE ITEM STOCK SUMMARY

    			        	        

        			        	        $itemExistData=array('id_product'=>$orderDetails['id_product'][$key],'id_branch'=>$ho['id_branch'],'purity'=>''); 

        			        	        $is_po_item_exist = $this->$model->checkPurchaseItemStockExist($itemExistData); //CHECK ITEM EXISTS IN TABLE 

        			        	        //print_r($is_po_item_exist);exit;

        			        	        $pur_item_stock_summary = array(

      										        'id_branch'	        => $ho['id_branch'],

      										        'purity'            => 0,

      										        'id_product'	    => $orderDetails['id_product'][$key],

      										        'id_ret_category'   => $orderDetails['id_category'][$key],

      										        'pieces'		    => ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),  

      										        'gross_wt'		    => $orderDetails['tot_gwt'][$key],  

      										        'less_wt'		    => 0,  

    												'net_wt'		    => $orderDetails['tot_gwt'][$key],  

    												);

    									if($is_po_item_exist['status']) //IF ITEM EXISTS ALREADY IN TABLE

    									{

    									    	 $pur_item_stock_summary['updated_by']=$this->session->userdata('uid');

    									    	 $pur_item_stock_summary['updated_on']=date('Y-m-d H:i:s');

    											 $pur_stock_Status=$this->$model->updatePurItemData($is_po_item_exist['id_stock_summary'],$pur_item_stock_summary,'+');

    											 $id_stock_summary=$is_po_item_exist['id_stock_summary'];

    											 

    									}

    									else // INSERT INTO PURCHASE ITEM STOCK SUMMARY

    									{

    									    $pur_item_stock_summary['created_by']=$this->session->userdata('uid');

    									    $pur_item_stock_summary['created_on']=date('Y-m-d H:i:s');

    									    $id_stock_summary=$this->$model->insertData($pur_item_stock_summary,'ret_purchase_item_stock_summary');

    									}



    									 if($id_stock_summary)

										 {

										        $stock_log_data=array(

                                                'id_stock_summary'=>$id_stock_summary,

                                                'date_add'        =>date('Y-m-d H:i:s'),

                                                'ref_no'          =>$insOrder,

                                                'piece'           =>($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                'gross_wt'        =>$orderDetails['tot_gwt'][$key],

                                                'net_wt'          =>$orderDetails['tot_gwt'][$key],

                                                'transcation_type'=>0,

                                                'credit_type'     =>1,

                                                'remarks'         =>'FROM SUPPLIER BILL ENTRY'

                                                );

                                                $this->$model->insertData($stock_log_data,'ret_purchase_item_stock_summary_log');

										 }

									

            					    //UPDATE INTO PURCHASE ITEM STOCK SUMMARY

            					    

                    			    }

                    			    //Stone Purchase

    			    

    			        	       

            					    

            						

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

                                            	'is_apply_in_lwt'=> $stone['show_in_lwt']

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

        				$responsData=array('status'=>TRUE,'msg'=>'Purchase Entry added successfully..');

        			}

        			else

        			{ 

        		

        			 echo $this->db->last_query();exit;

        				$this->db->trans_rollback();						 	

        				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add Order')); 

        				$responsData=array('status'=>FALSE,'msg'=>'');

        			}

        			echo json_encode($responsData);

    			}

    			    

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

			        $list=$this->$model->get_purchase_entry_details($from_date,$to_date); 

				  	$access = $this->admin_settings_model->get_access('admin_ret_purchase/purchase/list');

			        $data = array(

			        					'list'  => $list,

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

				$data['po_item_details']=$this->$model->getPurchaseOrderItemDet($id);

				$data['comp_details']=$this->ret_reports_model->getCompanyDetails("");

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

					$data['main_content'] = self::VIEW_FOLDER.'approvalstock/approval_entry_list';

					$data['access'] = $this->$SETT_MOD->get_access('admin_ret_purchase_approval/approvalstock/list');
        			$this->load->view('layout/template', $data);

			break;

			

			case 'pur_order':

					$data['main_content'] = self::VIEW_FOLDER.'pur_order_list';

        			$this->load->view('layout/template', $data);

			break;

			

			case 'add':

					$data['main_content'] = self::VIEW_FOLDER.'approvalstock/approvalentry';

        			$this->load->view('layout/template', $data);

			break;

			

			case 'approval_save':

			    $responsData=array();

			    //echo "<pre>";print_r($_POST);echo "</pre>";exit;

			    $orderData=$_POST['order'];

			    $orderDetails=$_POST['order_item'];

			    

                $ho              = $this->$model->get_headOffice();

                $fin_year       = $this->$model->get_FinancialYear();

                $dCData          = $this->admin_settings_model->getBranchDayClosingData($ho['id_branch']);

				$bill_date       = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

				

				$karigar_details=$this->$model->get_karigar_details($orderData['id_karigar']);

				

				/*1.IF APPROVAL STOCK -  PA

				2.AGANINST ORDER - IF REGISTERED KARIGAR - P , NON REGISTERED PM*/

				

			    if($karigar_details['karigar_type']==0)

			    {

			        $gst_bill_type=$karigar_details['karigar_type'];

			        $last_no       = $this->$model->generatePurRefOrderNo(1, $gst_bill_type);

			        $po_ref_no='PA-'.$last_no;

			    }

			    else

			    {

			        $last_no       = $this->$model->generatePurRefOrderNo(1,'');

			        $po_ref_no='PA-'.$last_no;

			    }

			    

			     if(!empty($orderDetails))

			    {

			        	foreach($orderDetails['id_category'] as $key => $val){

			        	    if(empty($orderDetails['rate_per_gram'][$key])){

			        	        $_POST['is_rate_fixed'] = 0;

			        	    }

			        	}

			    }

    				

				

     			$order = array( 

     				'po_ref_no'         => $po_ref_no,

     				'fin_year_code'		=> $fin_year['fin_year_code'],

     				'po_grn_id'         => NULL,

     				//'purchase_order_no' => ($orderData['purchase_type']==1 ?$orderData['purchase_order_no'] :NULL), //purchase_type=>1-Aganist Order

     				'is_suspense_stock' => 1,

     				'isratefixed'       => $_POST['is_rate_fixed'],

     				'gst_bill_type'     => $karigar_details['karigar_type'],

     				'purchase_type'     => !empty($orderData['purchase_type']) ? $orderData['purchase_type'] : $orderData['po_type'],

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

    				'pur_approval_type' => 1,

    			);

    			//echo"<pre>";print_r($orderDetails);exit;

    			$this->db->trans_begin();

    			$insOrder = $this->$model->insertData($order,'ret_purchase_order');

    			if($insOrder)

    			{

    			    

    			     //Update Order Status

    			    if($orderData['purchase_type']==1)

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

    

    			    

    			     if(!empty($orderData['other_charges_details'])){

    			        $charge_details = json_decode($orderData['other_charges_details'],true);

                    	foreach($charge_details as $charges)

                    	{

                        	$charge_data = array(

                            	'pur_othr_po_id'            => $insOrder,

                            	'pur_othr_charge_id'        => $charges['charge_id'],

                            	'pur_othr_charge_value'     => $charges['charge_value'],

                        	);

                        	$stoneInsert = $this->$model->insertData($charge_data, 'ret_purchase_other_charges');

                        	//echo $this->db->last_query();exit;

                    	}

    			        

    			    }

    			    

    			    //print_r($orderDetails);exit;

    			    

    			    if(!empty($orderDetails))

    			    {

    			        	foreach($orderDetails['id_category'] as $key => $val){

    			        	   

    			        	    $arrayItems=array(

    			        	                      'po_item_po_id'       =>$insOrder,

    			        	                      'po_item_cat_id'      =>$orderDetails['id_category'][$key],

    			        	                      'po_order_no'         => $orderDetails['po_order_no'][$key],

    			        	                      'po_purchase_mode'    => $orderDetails['po_purchase_mode'][$key],

    			        	                      'po_item_pro_id'      =>($orderDetails['id_product'][$key] !='' && $orderDetails['id_product'][$key]!='null' ? $orderDetails['id_product'][$key]:NULL),

    			        	                      'po_item_des_id'      =>($orderDetails['id_design'][$key]!='' && $orderDetails['id_design'][$key]!='null' ? $orderDetails['id_design'][$key]:NULL),

    			        	                      'po_item_sub_des_id'  =>($orderDetails['id_sub_design'][$key]!='' && $orderDetails['id_sub_design'][$key]!='null' ? $orderDetails['id_sub_design'][$key]:NULL),

    			        	                      'id_purity'           =>($orderDetails['id_purity'][$key]!='' ? $orderDetails['id_purity'][$key]:NULL),

    			        	                      'gross_wt'            =>$orderDetails['tot_gwt'][$key],

    			        	                      'less_wt'             =>($orderDetails['tot_lwt'][$key]!='' ? $orderDetails['tot_lwt'][$key] :0),

    			        	                      'net_wt'              =>$orderDetails['tot_nwt'][$key],

    			        	                      'item_wastage'        =>($orderDetails['tot_wastage_perc'][$key]!='' ?$orderDetails['tot_wastage_perc'][$key] :NULL),

    			        	                      'no_of_pcs'           =>($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

    			        	                      'is_suspense_stock'   =>$orderDetails['is_suspense_stock'][$key],

    			        	                      'is_halmarked'        =>$orderDetails['is_halmerked'][$key],

    			        	                      'is_halmark_from'     =>($orderDetails['is_halmerked'][$key]==1 ? $orderDetails['is_halmerked'][$key]:NULL),

    			        	                      'is_rate_fixed'       => empty($orderDetails['rate_per_gram'][$key]) ? 0 : $orderDetails['is_rate_fixed'][$key],

    			        	                      'fix_rate_per_grm'    =>($orderDetails['rate_per_gram'][$key]!='' ? $orderDetails['rate_per_gram'][$key] :NULL),

    			        	                      'mc_value'            =>($orderDetails['mc_value'][$key]!='' ? $orderDetails['mc_value'][$key] :NULL),

    			        	                      'purchase_touch'      =>($orderDetails['purchase_touch'][$key]!='' ? $orderDetails['purchase_touch'][$key] :NULL),

    			        	                      'mc_type'             =>$orderDetails['mc_type'][$key],

    			        	                      'item_cost'           =>($orderDetails['item_cost'][$key]!='' ?$orderDetails['item_cost'][$key] :0),

    			        	                      'item_pure_wt'        =>($orderDetails['tot_purewt'][$key]!='' ? $orderDetails['tot_purewt'][$key]:0),

    			        	                      'uom'                 => ($orderDetails['gwt_uom'][$key]!='' ? $orderDetails['gwt_uom'][$key]:0),

    			        	                      'cal_type'            => ($orderDetails['cal_type'][$key]!='' ? $orderDetails['cal_type'][$key]:0),

    			        	                      'remark'              => ($orderDetails['remark'][$key]!='' ? $orderDetails['remark'][$key]:NULL),

    			        	                     );

    			        	    $itemStatus=$this->$model->insertData($arrayItems,'ret_purchase_order_items');

    			        	    

    			        	    if($itemStatus)

    			        	    {

    			        	        

                                      //Bullion Purchase

                    			    if($orderData['po_type']==2)

                    			    {

                    			        

                    			           /*//Insert into Purchase Item Log Table

                                            $salesReturnLog=array(

                                                'id_product'  =>$orderDetails['id_product'][$key],

                                                'piece'       =>$orderDetails['tot_pcs'][$key],

                                                'gross_wt'    =>$orderDetails['tot_gwt'][$key],

                                                'less_wt'     =>($orderDetails['tot_lwt'][$key]!='' ? $orderDetails['tot_lwt'][$key] :0),

                                                'net_wt'      =>($orderDetails['tot_nwt'][$key]!='' ? $orderDetails['tot_nwt'][$key] :0),

                                                'from_branch' =>NULL,

                                                'to_branch'   =>$ho['id_branch'],

                                                'status'      =>1,//Inward

                                                'item_type'   =>6, // Bullion Purchase

                                                'date'        =>$bill_date,

                                                'created_on'  =>date("Y-m-d H:i:s"),

                                                'created_by'  =>$this->session->userdata('uid'),

                                                );

                                            $this->$model->insertData($salesReturnLog,'ret_purchase_items_log');

                                            

                                             //UPDATE INTO PURCHASE ITEM STOCK SUMMARY

    			        	        

    			        	            $purity_details=$this->$model->get_purity_details($orderDetails['id_purity'][$key]);

        			        	        $itemExistData=array('id_product'=>$orderDetails['id_product'][$key],'id_branch'=>$ho['id_branch'],'purity'=>$purity_details['purity']); 

        			        	        $is_po_item_exist = $this->$model->checkPurchaseItemStockExist($itemExistData); //CHECK ITEM EXISTS IN TABLE 

        			        	        //print_r($is_po_item_exist);exit;

        			        	        $pur_item_stock_summary = array(

      										        'id_branch'	        => $ho['id_branch'],

      										        'purity'            => $purity_details['purity'],

      										        'id_product'	    => $orderDetails['id_product'][$key],

      										        'id_ret_category'   => $orderDetails['id_category'][$key],

      										        'pieces'		    => ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),  

      										        'gross_wt'		    => $orderDetails['tot_gwt'][$key],  

      										        'less_wt'		    => 0,  

    												'net_wt'		    => $orderDetails['tot_gwt'][$key],  

    												);

    									if($is_po_item_exist['status']) //IF ITEM EXISTS ALREADY IN TABLE

    									{

    									    	 $pur_item_stock_summary['updated_by']=$this->session->userdata('uid');

    									    	 $pur_item_stock_summary['updated_on']=date('Y-m-d H:i:s');

    											 $pur_stock_Status=$this->$model->updatePurItemData($is_po_item_exist['id_stock_summary'],$pur_item_stock_summary,'+');

    											 $id_stock_summary=$is_po_item_exist['id_stock_summary'];

    											 

    									}

    									else // INSERT INTO PURCHASE ITEM STOCK SUMMARY

    									{

    									    $pur_item_stock_summary['created_by']=$this->session->userdata('uid');

    									    $pur_item_stock_summary['created_on']=date('Y-m-d H:i:s');

    									    $id_stock_summary=$this->$model->insertData($pur_item_stock_summary,'ret_purchase_item_stock_summary');

    									}



    									 if($id_stock_summary)

										 {

										        $stock_log_data=array(

                                                'id_stock_summary'=>$id_stock_summary,

                                                'date_add'        =>date('Y-m-d H:i:s'),

                                                'ref_no'          =>$insOrder,

                                                'piece'           =>($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                'gross_wt'        =>$orderDetails['tot_gwt'][$key],

                                                'net_wt'          =>$orderDetails['tot_gwt'][$key],

                                                'transcation_type'=>0,

                                                'credit_type'     =>1,

                                                'remarks'         =>'FROM SUPPLIER BILL ENTRY'

                                                );

                                                $this->$model->insertData($stock_log_data,'ret_purchase_item_stock_summary_log');

										 }*/

            					    //UPDATE INTO PURCHASE ITEM STOCK SUMMARY

            					    

            					    

            					    $lot_data = array(

                                            'lot_date'				=> date("Y-m-d H:i:s"),

                                            'created_branch'		=> $ho['id_branch'],

                                            'lot_received_at'       => $ho['id_branch'],

                                            'stock_type'            => 2,

                                            'lot_from'              => 2,

                                            'po_id'                 => $insOrder,

                                            'gold_smith'			=> $orderData['id_karigar'],

                                            'id_category'			=> $orderDetails['id_category'][$key],

                                            'id_purity'				=> $orderDetails['id_purity'][$key],

                                            'narration'				=> 'From Supplier Bill Entry',

                                            'created_on'	  		=> date("Y-m-d H:i:s"),

                                            'created_by'      		=> $this->session->userdata('uid')

                                            ); 

                                           $lotId = $this->$model->insertData($lot_data,'ret_lot_inwards');

                                           if($lotId)

                                           {

                                               $item_details = array(

                                                    'lot_no'	    => $lotId,

                                                    'lot_product'   => $orderDetails['id_product'][$key],

                                                    'no_of_piece'   => ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                    'gross_wt'      => $orderDetails['tot_gwt'][$key],

                                                    'less_wt'       => 0,

                                                    'net_wt'        => $orderDetails['tot_gwt'][$key],

                                                ); 

                                                $detail_insId = $this->$model->insertData($item_details,'ret_lot_inwards_detail'); 

                                           }

            					           $existData=array('id_product'=>$orderDetails['id_product'][$key],'id_branch'=>$ho['id_branch']);

                                    						        

            						        $isExist = $this->$model->checkNonTagItemExist($existData);

            						        

            						        if($isExist['status'] == TRUE)

            						        {

            						            $nt_data = array(

                                                'id_nontag_item'=> $isExist['id_nontag_item'],

                                                'gross_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'net_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'no_of_piece'	=> ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                'updated_by'	=> $this->session->userdata('uid'),

                                                'updated_on'	=> date('Y-m-d H:i:s'),

                                                );

                                                $this->$model->updateNTData($nt_data,'+');

                                    													

                                                $non_tag_data=array(

                                                'product'	    => $orderDetails['id_product'][$key],

                                                'gross_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'net_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'no_of_piece'	=> ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                'to_branch'	    => $ho['id_branch'],

                                                'from_branch'	=> NULL,

                                                'ref_no'	    => $insOrder,

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

                                                    'product'	    => $orderDetails['id_product'][$key],

                                                    'gross_wt'		=> $orderDetails['tot_gwt'][$key],

                                                    'net_wt'		=> $orderDetails['tot_gwt'][$key],

                                                    'no_of_piece'	=> ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                    'created_on'    => date("Y-m-d H:i:s"),

                                                    'created_by'    => $this->session->userdata('uid')

                                                );

                                                $this->$model->insertData($nt_data,'ret_nontag_item'); 

                                                

                                                $non_tag_data=array(

                                                'product'	    => $orderDetails['id_product'][$key],

                                                'gross_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'net_wt'		=> $orderDetails['tot_gwt'][$key],

                                                'no_of_piece'	=> ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                'from_branch'	=> NULL,

                                                'to_branch'	    => $ho['id_branch'],

                                                'status'	    => 0,

                                                'ref_no'	    => $insOrder,

                                                'date'          => $bill_date,

                                                'created_on'    => date("Y-m-d H:i:s"),

                                                'created_by'    =>  $this->session->userdata('uid')

                                                );

                                                $this->$model->insertData($non_tag_data,'ret_nontag_item_log'); 

            						        }

            						        

            					    

                    			    }

                    			    //Bullion Purchase

                    			    

                    			    

                    			     //Stone Purchase

                    			    if($orderData['po_type']==3)

                    			    {

                    			        

                    			           //Insert into Purchase Item Log Table

                                            $salesReturnLog=array(

                                                'id_product'  =>$orderDetails['id_product'][$key],

                                                'piece'       =>$orderDetails['tot_pcs'][$key],

                                                'gross_wt'    =>$orderDetails['tot_gwt'][$key],

                                                'less_wt'     =>($orderDetails['tot_lwt'][$key]!='' ? $orderDetails['tot_lwt'][$key] :0),

                                                'net_wt'      =>($orderDetails['tot_nwt'][$key]!='' ? $orderDetails['tot_nwt'][$key] :0),

                                                'from_branch' =>NULL,

                                                'to_branch'   =>$ho['id_branch'],

                                                'status'      =>1,//Inward

                                                'item_type'   =>6, // Stone Purchase

                                                'date'        =>$bill_date,

                                                'created_on'  =>date("Y-m-d H:i:s"),

                                                'created_by'  =>$this->session->userdata('uid'),

                                                );

                                            $this->$model->insertData($salesReturnLog,'ret_purchase_items_log');

                                            

                                             //UPDATE INTO PURCHASE ITEM STOCK SUMMARY

    			        	        

        			        	        $itemExistData=array('id_product'=>$orderDetails['id_product'][$key],'id_branch'=>$ho['id_branch'],'purity'=>''); 

        			        	        $is_po_item_exist = $this->$model->checkPurchaseItemStockExist($itemExistData); //CHECK ITEM EXISTS IN TABLE 

        			        	        //print_r($is_po_item_exist);exit;

        			        	        $pur_item_stock_summary = array(

      										        'id_branch'	        => $ho['id_branch'],

      										        'purity'            => 0,

      										        'id_product'	    => $orderDetails['id_product'][$key],

      										        'id_ret_category'   => $orderDetails['id_category'][$key],

      										        'pieces'		    => ($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),  

      										        'gross_wt'		    => $orderDetails['tot_gwt'][$key],  

      										        'less_wt'		    => 0,  

    												'net_wt'		    => $orderDetails['tot_gwt'][$key],  

    												);

    									if($is_po_item_exist['status']) //IF ITEM EXISTS ALREADY IN TABLE

    									{

    									    	 $pur_item_stock_summary['updated_by']=$this->session->userdata('uid');

    									    	 $pur_item_stock_summary['updated_on']=date('Y-m-d H:i:s');

    											 $pur_stock_Status=$this->$model->updatePurItemData($is_po_item_exist['id_stock_summary'],$pur_item_stock_summary,'+');

    											 $id_stock_summary=$is_po_item_exist['id_stock_summary'];

    											 

    									}

    									else // INSERT INTO PURCHASE ITEM STOCK SUMMARY

    									{

    									    $pur_item_stock_summary['created_by']=$this->session->userdata('uid');

    									    $pur_item_stock_summary['created_on']=date('Y-m-d H:i:s');

    									    $id_stock_summary=$this->$model->insertData($pur_item_stock_summary,'ret_purchase_item_stock_summary');

    									}



    									 if($id_stock_summary)

										 {

										        $stock_log_data=array(

                                                'id_stock_summary'=>$id_stock_summary,

                                                'date_add'        =>date('Y-m-d H:i:s'),

                                                'ref_no'          =>$insOrder,

                                                'piece'           =>($orderDetails['tot_pcs'][$key]!='' ? $orderDetails['tot_pcs'][$key] :0),

                                                'gross_wt'        =>$orderDetails['tot_gwt'][$key],

                                                'net_wt'          =>$orderDetails['tot_gwt'][$key],

                                                'transcation_type'=>0,

                                                'credit_type'     =>1,

                                                'remarks'         =>'FROM SUPPLIER BILL ENTRY'

                                                );

                                                $this->$model->insertData($stock_log_data,'ret_purchase_item_stock_summary_log');

										 }

									

            					    //UPDATE INTO PURCHASE ITEM STOCK SUMMARY

            					    

                    			    }

                    			    //Stone Purchase

    			    

    			        	       

            					    

            						

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

                                            	'is_apply_in_lwt'=> $stone['show_in_lwt']

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

                        	'module'      	=> 'SUPPLIER APPROVAL BILL ENTRY',

                        	'operation'   	=> 'Add',

                        	'record'        =>  $insOrder,  

                        	'remark'       	=> 'Supplier Approval bill Entry Added Successfully..'

                        );

                        $this->log_model->log_detail('insert','',$log_data);

                

        				$this->db->trans_commit();

        				$this->session->set_flashdata('chit_alert',array('message'=>'Purchase Entry added successfully','class'=>'success','title'=>'Purchase Entry')); 

        				$responsData=array('status'=>TRUE,'msg'=>'Purchase Entry added successfully..');

        			}

        			else

        			{ 

        		

        			 echo $this->db->last_query();exit;

        				$this->db->trans_rollback();						 	

        				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add Order')); 

        				$responsData=array('status'=>FALSE,'msg'=>'');

        			}

        			echo json_encode($responsData);

    			}

    			    

			break;

			

			case 'ajax_approval_list':

			        $from_date	= $this->input->post('from_date');

			        $to_date	= $this->input->post('to_date'); 

			        $list=$this->$model->get_approval_purchase_entry_details($from_date,$to_date); 

				  	$access = $this->admin_settings_model->get_access('admin_ret_purchase_approval/approvalstock/list');

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

        //echo "<pre>";print_r($orders);exit;

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

        $data= $this->$model->get_purchase_issue_entry_items($_POST);

        echo json_encode($data);

    }

    

    

    function update_qc_status()

    {

        $model=	self::RET_PUR_ORDER_MODEL;

        $itemDetails=$_POST['order_items'];

        $po_id=$_POST['order']['po_ref_no'];

        $responseData=array();

        $this->db->trans_begin();

        foreach($itemDetails['po_item_id'] as $key => $val)

        {

             $stnRemovewt=0;

                if($itemDetails['stone_details'][$key])

            	{

                	$stone_details=json_decode($itemDetails['stone_details'][$key],true);

                	foreach($stone_details as $stone)

                	{

                    

                    	    $stnRemovewt+=$stone['po_stone_rejected_wt'];

                    	    $this->$model->updateData(

                                	        array(

                                	            'po_stone_rejected_pcs' =>$stone['po_stone_rejected_pcs'],

                                	            'po_stone_rejected_wt'  =>$stone['po_stone_rejected_wt'],

                                	            'po_stone_accepted_pcs' =>$stone['po_stone_accepted_pcs'],

                                	            'po_stone_accepted_wt'  =>$stone['po_stone_accepted_wt'],

                                	        ),

                                	        'po_st_id',$stone['po_st_id'],'ret_po_stone_items');

                	}										

            	}

                $order              = $this->$model->get_pur_order_item_details($itemDetails['po_item_id'][$key]);   

                $item_pure_wt       =(($itemDetails['qc_acc_nwt'][$key]*($order['purchase_touch']+$order['item_wastage']))/100);

                $updData=array(

                                'status'     => 2, // QC Completed

                                'qc_failed_pcs' => $itemDetails['failed_pcs'][$key],

                                'qc_failed_gwt' => $itemDetails['failed_gwt'][$key],

                                'qc_failed_lwt' => $stnRemovewt,

                                'qc_failed_nwt' => ($itemDetails['failed_gwt'][$key]-$stnRemovewt),

                                'qc_passed_pcs' => $itemDetails['qc_acc_pcs'][$key],

                                'qc_passed_gwt' => $itemDetails['qc_acc_gwt'][$key],

                                'qc_passed_lwt' => $itemDetails['qc_acc_lwt'][$key],

                                'qc_passed_nwt' => $itemDetails['qc_acc_nwt'][$key],

                                'item_pure_wt'  => $item_pure_wt,

                                'qc_checked_on' => date("Y-m-d H:i:s"),

                                'qc_checked_by' => $this->session->userdata('uid')

                              );

                              

                

                $status = $this->$model->updateData($updData,'po_item_id',$itemDetails['po_item_id'][$key],'ret_purchase_order_items');

                if($status)

                {

                    $this->$model->updateData(array('status'=>1),'po_item_id',$itemDetails['po_item_id'][$key],'ret_po_qc_issue_details');

                }

                //print_r($this->db->last_query());exit;

        }



        if($this->db->trans_status()===TRUE)

        {

            $this->db->trans_commit();

            

            //Generate LOT

            if($po_id!='')

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

            }

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

    	 	            

    	 	            

    	 	            

    	 	            //Update Rate Fixing

    	 	           

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

                          // print_r($this->db->last_query());exit;

	 	            }

	 	            

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

	// ========== MD Approval Dashboard ==========
	function md_dashboard($type = '', $id = '')
	{
		$model = self::RET_PUR_ORDER_MODEL;

		switch($type)
		{
			case "list":
				$data['page_title'] = "MD Approval Dashboard";
				$this->load->view(self::VIEW_FOLDER.'md_approval_dashboard', $data);
				break;

			case "ajax":
				$orders = $this->$model->get_md_pending_orders();
				$result = array();
				foreach($orders as $order)
				{
					$items = $this->$model->get_md_order_items($order['id_customerorder']);
					$product_list = array();
					$total_pcs = 0;
					$weight_ranges = array();
					$sizes = array();
					foreach($items as $item)
					{
						if(!empty($item['product_name'])) $product_list[] = $item['product_name'];
						$total_pcs += (int)$item['totalitems'];
						if(!empty($item['weight_range'])) $weight_ranges[] = $item['weight_range'];
						if(!empty($item['size'])) $sizes[] = $item['size'];
					}
					$order['products'] = implode(', ', array_unique($product_list));
					$order['total_pcs'] = $total_pcs;
					$order['weight_range'] = implode(', ', array_unique($weight_ranges));
					$order['sizes'] = implode(', ', array_unique($sizes));
					$result[] = $order;
				}
				echo json_encode(array('data' => $result));
				break;

			case "approve":
				$id_customerorder = $this->input->post('id_customerorder');
				if(empty($id_customerorder)) {
					echo json_encode(array('status' => FALSE, 'msg' => 'Invalid order ID'));
					return;
				}
				// Try sending email FIRST — only approve if email succeeds
				$email_result = $this->_send_vendor_email($id_customerorder);
				if(!$email_result['email_status']) {
					echo json_encode(array('status' => FALSE, 'msg' => $email_result['email_error']));
					return;
				}

				$approved = $this->$model->md_approve_order($id_customerorder);
				if($approved) {
					
					echo json_encode(array('status' => TRUE, 'msg' => 'Order approved and email sent to vendor'));
				} else {
					echo json_encode(array('status' => FALSE, 'msg' => 'Failed to approve order'));
				}
				break;

			case "reject":
				$id_customerorder = $this->input->post('id_customerorder');
				$reason = $this->input->post('reason');
				if(empty($id_customerorder)) {
					echo json_encode(array('status' => FALSE, 'msg' => 'Invalid order ID'));
					return;
				}
				if(empty($reason)) {
					echo json_encode(array('status' => FALSE, 'msg' => 'Rejection reason is required'));
					return;
				}

				$rejected = $this->$model->md_reject_order($id_customerorder, $reason);
				if($rejected) {
					echo json_encode(array('status' => TRUE, 'msg' => 'Order rejected'));
				} else {
					echo json_encode(array('status' => FALSE, 'msg' => 'Failed to reject order'));
				}
				break;

			case "bulk_approve":
				$order_ids = $this->input->post('order_ids');
				if(empty($order_ids) || !is_array($order_ids)) {
					echo json_encode(array('status' => FALSE, 'msg' => 'No orders selected'));
					return;
				}
				// Try sending emails FIRST — only approve if all emails succeed
				$email_fail_count = 0;
				$last_email_error = '';
				foreach($order_ids as $oid) {
					$email_result = $this->_send_vendor_email($oid);
					if(!$email_result['email_status']) {
						$email_fail_count++;
						$last_email_error = $email_result['email_error'];
						break; // Stop on first failure
					}
				}
				if($email_fail_count > 0) {
					echo json_encode(array('status' => FALSE, 'msg' => $last_email_error));
					return;
				}

				$approved = $this->$model->md_bulk_approve_orders($order_ids);
				if($approved) {
					echo json_encode(array('status' => TRUE, 'msg' => count($order_ids) . ' order(s) approved and emails sent'));
				} else {
					echo json_encode(array('status' => FALSE, 'msg' => 'Failed to approve orders'));
				}
				break;

			default:
				redirect('admin/index.php/admin_ret_purchase_approval/md_dashboard/list');
				break;
		}
	}

	/**
	 * Private helper: Send vendor email after MD approval
	 */
	private function _send_vendor_email($id_customerorder)
	{
		$model = self::RET_PUR_ORDER_MODEL;

		// Get order details
		$order_row = $this->db->query("SELECT * FROM customerorder WHERE id_customerorder = ".$id_customerorder)->row_array();
		if(empty($order_row)) return array('email_status' => false, 'email_error' => 'Order not found');

		$karigar = $this->$model->get_karigar_details($order_row['id_karigar']);
		if(empty($karigar) || empty($karigar['email'])) {
			// Log if email not available
			$this->$model->save_email_log(array(
				'id_customerorder' => $id_customerorder,
				'id_karigar' => $order_row['id_karigar'],
				'email_id' => isset($karigar['email']) ? $karigar['email'] : NULL,
				'token' => '',
				'status' => 2,
				'error_msg' => 'Email ID not found for this supplier',
				'created_at' => date("Y-m-d H:i:s")
			));
			return array('email_status' => false, 'email_error' => 'Email ID not found for this supplier');
		}

		$token = md5($id_customerorder . time() . uniqid());
		$log_id = $this->$model->save_email_log(array(
			'id_customerorder' => $id_customerorder,
			'id_karigar' => $order_row['id_karigar'],
			'email_id' => $karigar['email'],
			'token' => $token,
			'status' => 0,
			'created_at' => date("Y-m-d H:i:s")
		));

		if($log_id) {
			$accept_url = base_url("index.php/OrderAccept/index/" . $token);

			$orderDetails = $this->$model->get_karigar_order_details($id_customerorder);
			$embeddings = array();

			// Embed logo
			$logo_path = FCPATH . 'assets/img/logo.png';
			if(file_exists($logo_path)) {
				$embeddings['logo_img'] = $logo_path;
				$logo_src = "cid:logo.png";
			} else {
				$logo_src = base_url('assets/img/logo.png');
			}

			if(!empty($orderDetails)) {
				foreach($orderDetails as $itemDet) {
					if(!empty($itemDet['images'])) {
						$img_name = $itemDet['images'][0]['image'];
						$img_path = FCPATH . 'assets/img/order/purchase_order/' . $img_name;
						if(file_exists($img_path)) {
							$embeddings[$img_name] = $img_path;
						} else {
							$img_path = FCPATH . 'assets/img/customer_order/' . $img_name;
							if(file_exists($img_path)) {
								$embeddings[$img_name] = $img_path;
							}
						}
					}
				}
			}

			$subject = "New Purchase Order - #" . $order_row['pur_no'];

			$viewData = array(
				'order' => $order_row,
				'karigar' => $karigar,
				'orderDetails' => $orderDetails,
				'accept_url' => $accept_url,
				'logo_src' => $logo_src
			);

			// Try to load the email template view, fallback to simple message
			if(file_exists(APPPATH . 'views/order/purchase_order_email_template.php')) {
				$message = $this->load->view('order/purchase_order_email_template', $viewData, true);
			} else {
				$message = "
					<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
						<h2 style='color: #4f46e5;'>Purchase Order Approved</h2>
						<p>Hello <b>" . $karigar['karigar_name'] . "</b>,</p>
						<p>Your purchase order <b>#" . $order_row['pur_no'] . "</b> has been approved.</p>
						<p>Please click the button below to view the order details and confirm your delivery date:</p>
						<p style='text-align: center; margin: 30px 0;'>
							<a href='" . $accept_url . "' style='background-color: #6366f1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold;'>View Order</a>
						</p>
						<p>If the button doesn't work, copy and paste this link into your browser:</p>
						<p><a href='" . $accept_url . "'>" . $accept_url . "</a></p>
						<hr style='border: 0; border-top: 1px solid #eee;'>
					</div>";
			}

			$mail_sent = $this->email_model->send_email($karigar['email'], $subject, $message, "", "", "", $embeddings);

			if($mail_sent) {
				$this->$model->update_email_log($log_id, array('status' => 1));
				return array('email_status' => true, 'email_error' => '');
			} else {
				$error_msg = $this->email_model->last_error;
				// Truncate error message to prevent MySQL issues with large SMTP debug output
				$error_msg_db = substr($error_msg, 0, 500);
				// Check for common quota/limit errors
				if(stripos($error_msg, 'quota') !== false || stripos($error_msg, 'limit') !== false || stripos($error_msg, '550') !== false || stripos($error_msg, 'rate') !== false || stripos($error_msg, 'Daily') !== false) {
					$friendly_error = 'Daily email sending quota exceeded. Please try again later or contact administrator.';
				} else {
					$friendly_error = 'Email sending failed. Please check email configuration.';
				}
				$this->$model->update_email_log($log_id, array('status' => 2, 'error_msg' => $error_msg_db));
				return array('email_status' => false, 'email_error' => $friendly_error);
			}
		}
		return array('email_status' => false, 'email_error' => 'Email log could not be created');
	}
	// ========== End MD Approval Dashboard ==========

}

?>