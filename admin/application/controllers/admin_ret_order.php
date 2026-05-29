<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'libraries/dompdf/autoload.inc.php');
use Dompdf\Dompdf;
class Admin_ret_order extends CI_Controller
{
	const IMG_PATH  = 'assets/img/';
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model('ret_order_model');
		$this->load->model('admin_settings_model');
		$this->load->model("admin_usersms_model");
		$this->load->model("ret_billing_model");
		$this->load->model('log_model');
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
	* Order Functions Starts
	*/
    function shortenurl($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch,CURLOPT_URL,'https://tinyurl.com/api-create.php?url='.$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
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
	public function isValueset($field)
	{
		$data=($field ? $field:'-');
		return $data;
	}
	public function taxGroupItems()
	{
		$tgrp = $this->ret_order_model->getAvailableTaxGroupItems($_POST['tgrp_id']);
		echo json_encode($tgrp);
	}
	public function order($type="",$id="",$order_no=""){
		$model = "ret_order_model";
		$SETT_MOD   =  "admin_settings_model";
		$this->load->model('ret_catalog_model');
		$ret_catalog_model = "ret_catalog_model";
		switch($type)
		{
			case 'getOrderNosBySearch':
				  	$result = $this->$model->getOrderNos($_POST['searchTxt']);
					echo json_encode($result);
				break;
			case 'list':
					$data['main_content'] = "order/list" ;
					$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_order/order/list');


					$this->load->view('layout/template', $data);
				break;
			case 'add':
					$data = $this->$model->empty_rec_order();
					$data['customer_order_description_req'] = $this->$model->get_ret_settings('customer_order_description_req');
					$data['categories'] = $this->ret_order_model->getActiveCategories();
					$data['main_content'] = "order/form" ;
					$this->load->view('layout/template', $data);
					$this->load->view('layout/common_customerslider');
				break;
			case "save":
				$addData = $_POST;
				// echo"<pre>";print_r($addData);exit;
				$order_type = $addData['order']['order_type'];
				$fin_year       = $this->$model->get_FinancialYear();
				$order_from = (!empty($addData['order']['order_from']) ? $addData['order']['order_from'] :NULL );
				$order_no = $this->$model->generateOrderNo($order_from,$order_type);
				$dCData = $this->admin_settings_model->getBranchDayClosingData($addData['order']['order_from']);
				$order_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
				$rate_type = $addData['order']['rate_type'];
				$order = array(
					'fin_year_code'     => $fin_year['fin_year_code'],
					'order_type'		=> $order_type,
					'order_from'		=> $order_from,
					'order_no'          => $order_no,
					'order_for'			=> (!empty($addData['order']['order_for']) ? $addData['order']['order_for'] :NULL ),
					'rate_calc_from'	=> (!empty($addData['order']['rate_calc_from']) ? $addData['order']['rate_calc_from'] :1 ),
					'order_date'		=> $order_datetime,
					'order_to'			=> ($addData['order']['order_for'] == 1 ? $addData['order']['id_branch'] :$addData['order']['order_to'] ),
					'est_id'			=> (!empty($addData['order']['est_no']) ? $addData['order']['est_no'] :NULL ),
				   'balance_type'		=> $addData['order']['balance_type'],
				   'rate_type'         => $rate_type,
				   'createdon'         => date("Y-m-d H:i:s"),
				   'order_taken_by'    => (!empty($addData['order']['order_taken_by']) ? $addData['order']['order_taken_by'] : $this->session->userdata('uid'))
			   	);
				$this->db->trans_begin();
				$insOrder = $this->$model->insertData($order,'customerorder');
				$i = 1;
				foreach ($addData['o_item'] as $d)
				{
					$d['smith_remainder_date'] = (!empty($d['smith_remainder_date']) ?date('Y-m-d',strtotime(str_replace("/","-",$d['smith_remainder_date']))):NULL );
					$d['cus_due_date'] = (!empty($d['cus_due_date']) ?date('Y-m-d',strtotime(str_replace("/","-",$d['cus_due_date']))):NULL );
					$d['smith_due_date'] = (!empty($addData['order']['smith_due_dt']) ?date('Y-m-d',strtotime(str_replace("/","-",$addData['order']['smith_due_dt']))):NULL );
					$tag_id = (!empty($d['tag_id']) ? $d['tag_id'] :NULL );
                    /* if($d['due_date']!='')
                    {
                        $customer_due_date = date('Y-m-d',(strtotime('+'.$d['due_date'].' day')));
                    }else{
                        $customer_due_date = date('Y-m-d',(strtotime('+'.$this->$model->get_ret_settings('customer_due_date').' day')));
                    } */
					$customer_due_date = (!empty($d['due_date']) ?date('Y-m-d',strtotime(str_replace("/","-",$d['due_date']))):NULL );
					$orderDetails = array(
						'orderno'			=> $order_no."-".$i,
						'ortertype'			=> $order_type,
						'id_product'		=> (!empty($d['id_product']) ? $d['id_product'] :NULL ),
						'itemname'			=> (!empty($d['design']) ? $d['design'] :NULL ),
						'design_no'			=> (!empty($d['id_design']) ? $d['id_design'] :NULL ),
						'id_sub_design'	    => (!empty($d['id_sub_design']) ? $d['id_sub_design'] :NULL ),
						'id_purity'			=> (!empty($d['id_purity']) ? $d['id_purity'] :NULL ),
					   'rate_per_gram'		=> (!empty($d['order_rate']) ? $d['order_rate'] :NULL ),
						'totalitems'		=> (!empty($d['totalitems']) ? $d['totalitems'] :0 ),
						'weight'			=> (!empty($d['weight']) ? $d['weight'] :NULL ),
					   'less_wt'			=> (!empty($d['less_wt']) && $d['less_wt'] != "" ? $d['less_wt'] :0 ),
					   'net_wt'			    => (!empty($d['net_wt']) ? $d['net_wt'] :0 ),
						'mc'				=> (!empty($d['mc']) ? $d['mc'] :0 ),
						'id_mc_type'		=> (!empty($d['id_mc_type']) ? $d['id_mc_type'] :2),
						'wast_percent'		=> (!empty($d['wast_percent']) && $d['wast_percent'] != "" ? $d['wast_percent'] :0 ),
					   'stn_amt'			=> ((!empty($d['stn_amt'])&&$d['stn_amt']!=null ) ? $d['stn_amt'] :0 ),
						'charge_value'		=> ((!empty($d['value_charge'])&&$d['value_charge']!=null ) ? $d['value_charge'] :0 ),
						'rate'				=> (!empty($d['rate']) ? $d['rate'] :NULL ),
					   'total_sgst'		   => (!empty($d['sgst']) ? $d['sgst'] :0 ),
					   'total_cgst'		   => (!empty($d['cgst']) ? $d['cgst'] :0 ),
					   'total_igst'		    => (!empty($d['igst']) ? $d['igst'] :0 ),
						'size'				=> (!empty($d['size']) ? $d['size'] :NULL ),
						'order_date'		=> $order_datetime,
						'smith_remainder_date'=> (!empty($d['smith_remainder_date']) ? $d['smith_remainder_date'] :NULL ),
						'cus_due_date'		=> $customer_due_date,
						'smith_due_date'	=> (!empty($d['smith_due_date']) ? $d['smith_due_date'] :NULL ),
						'description'		=> (!empty($d['description']) ? $d['description'] :NULL ),
					   'id_employee'       => $this->session->userdata('uid'),
					   'image'			    => (!empty($d['image']) ? $d['image'] :NULL ),
					   'orderstatus'		=> 0,
					   'tag_id'			=> $tag_id,
					   'tag_name'			=> (!empty($d['tag_name']) ? $d['tag_name'] :NULL )
				   	);
					if($insOrder > 0)
					{
						$orderDetails['id_customerorder'] = $insOrder;
						$insOrderDet = $this->$model->insertData($orderDetails,'customerorderdetails');
                        /*Order Images*/
						$precious_imgs = "";
						$p_ImgData=[];
						$_FILES = [];
						$p_ImgData = json_decode($d['order_img']);
						if(sizeof($p_ImgData) > 0)
						{
						$_FILES['order_images']=[];
							foreach($p_ImgData as $precious)
							{
								$imgFile = $this->base64ToFile($precious->src);
								$_FILES['order_images'][] = $imgFile;
							}
						}
						if(!empty($_FILES))
						{
							$img_arr = array();
							if($addData['order']['order_for']==1)
							{
								$folder =  self::IMG_PATH."stock_order/";
							}else if($addData['order']['order_for']==2){
								$folder =  self::IMG_PATH."orders/";
							}
							if (!is_dir($folder)) {
								mkdir($folder, 0777, TRUE);
							}
							if(isset($_FILES['order_images']))
							{
								foreach($_FILES['order_images'] as $file_key => $file_val){
									if($file_val['name'])
									{
										$img_name =  "t_". mt_rand(120,1230).".jpg";
										$path = $folder."/".$img_name;
										$result = $this->upload_img('image',$path,$file_val['tmp_name']);
										if($result)
										{
											$insImageId = $this->$model->insertData(array('id_orderdetails'=>$insOrderDet,'image'=>$img_name),'customer_order_image');
										}
									}
								}
							}
						}
						$stone_details = json_decode($d['stone_details'],true);
						if(sizeof($stone_details)>0)
						{
							foreach($stone_details as $stone)
							{
								$stone_data=array(
									'order_id'        	=>	$insOrder,
									'order_item_id'   	=>	$insOrderDet,
									'pieces'        	=>	$stone['stone_pcs'],
									'wt'            	=>	$stone['stone_wt'],
									'stone_id'      	=>	$stone['stone_id'],
									'uom_id'      	    =>	$stone['stone_uom_id'],
									'price'         	=>	$stone['stone_price'],
									'is_apply_in_lwt'	=>	$stone['show_in_lwt'],
									'stone_cal_type' 	=>	$stone['stone_cal_type'],
									'rate_per_gram'  	=>	$stone['stone_rate'],
									);
								$stoneInsert = $this->$model->insertData($stone_data,'ret_order_item_stones');
							}
						}
						$charges_details = json_decode($d['charges_details'],true);
						if(sizeof($charges_details)>0)
						{
							foreach($charges_details as $charge)
							{
								$charge_data=array(
								'order_id'  	=>	$insOrder,
								'order_item_id'	=>	$insOrderDet,
								'id_charge'    	=>	$charge['id_charge'],
								'amount'       	=>	$charge['value_charge'],
								);
							$this->$model->insertData($charge_data,'ret_order_other_charges');
							//print_r($this->db->last_query());exit;
							}
						}
						if($order_type == 5 && $tag_id > 0)
						{
							$data = array(
								'id_orderdetails'=> $insOrderDet,
								'updated_time' 	=> date("Y-m-d H:i:s"),
								'updated_by' 	=> $this->session->userdata('uid')
							);
							$status = $this->$model->updateData($data,'tag_id',$tag_id,'ret_taging');
							//print_r($this->db->last_query());exit;
							$this->$model->updateData(array('orderstatus'=>4,'deliverydate'=> date("Y-m-d H:i:s")),'id_orderdetails',$insOrderDet,'customerorderdetails');
							$this->$model->updateData(array('orderstatus'=>4,'deliveredon'=> date("Y-m-d H:i:s")),'id_order',$insOrderDet,'joborder');
							if($status)
							{
								$log_data = array(
											'id_log'        => $this->session->userdata('id_log'),
											'event_date'    => date("Y-m-d H:i:s"),
											'module'        => 'Tagging',
											'operation'     => 'Add',
											'record'        =>  $tag_id,
											'remark'        => 'Order link added successfully through order Form'
										);
								$this->log_model->log_detail('insert','',$log_data);
							}
						}
					}
					$i++;
				}
				if($this->db->trans_status()===TRUE)
				{
					$this->db->trans_commit();
				
					$log_data = array(
						'id_log'        => $this->session->userdata('id_log'),
						'event_date'    => date("Y-m-d H:i:s"),
						'module'        => 'Customer Order',
						'operation'     => 'Add',
						'record'        =>  $insOrderDet,
						'remark'        => 'Customer Order link added successfully'
					);
			       $this->log_model->log_detail('insert','',$log_data);

					$this->session->set_flashdata('chit_alert',array('message'=>'New Order added successfully','class'=>'success','title'=>'Add Order'));
					$return_data=array('status'=>TRUE,'msg'=>'Order Created successfully..','id_customerorder'=>$insOrder,'order_for');
				}
				else
				{
					//echo $this->db->last_query();exit;
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add Order'));
					$return_data=array('status'=>FALSE,'msg'=>'');
				}
				echo json_encode($return_data);
			break;
			case "edit":
				$data['order']  = $this->ret_order_model->getOrder($id);
				$data['categories'] = $this->ret_order_model->getActiveCategories();
				$data['purity'] = $this->ret_catalog_model->getActivePurity();
				$data['main_content'] = "order/form" ;
				//echo "<pre>";print_r($data);exit;
				$this->load->view('layout/template', $data);
				$this->load->view('layout/common_customerslider');
			break;
			case "ordered_list":
				$data  = $this->ret_order_model->get_orderdetails($id);
				//echo "<pre>";print_r($data);exit;
				echo json_encode($data);
			break;

			case "repair_details":
				$data = $this->ret_order_model->get_repair_item_details($_POST['id_orderdetails']);
				//echo "<pre>";print_r($data);exit;
				echo json_encode($data);
			break;

	 		case "estiData":
				//$data = $this->ret_order_model->getOrderDetails($id);
				//echo json_encode($data);
					$estiData['categories'] = $this->ret_order_model->getActiveCategories();
					$estiData['products'] = $this->ret_order_model->getActiveproducts();
					$estiData['designs'] = $this->ret_order_model->getActivedesigns();
					$estiData['subdesigns'] = $this->ret_order_model->getActivesubdesigns();
					$order_detail = $this->ret_order_model->getOrderDetails($id);
				foreach ($order_detail as $orders)
				{
					$estiData['order_det'][]=array(
					'est_no'=>$orders['est_no'],
					'id_orderdetails'=>$orders['id_orderdetails'],
					'orderno'=>$orders['orderno'],
					'ortertype'=>$orders['ortertype'],
					'id_ret_category'=>$orders['id_ret_category'],
					'category'=>$orders['category'],
					'id_purity'=>$orders['id_purity'],
					'purity'=>$orders['purity'],
					'id_product'=>$orders['id_product'],
					'product_name'=>$orders['product_name'],
					'design_no'=>$orders['design_no'],
					'design_name'=>$orders['design_name'],
					'id_sub_design'=>$orders['id_sub_design'],
					'sub_design_name'=>$orders['sub_design_name'],
					'weight'=>$orders['weight'],
					'id_size'=>$orders['size'],
					'size'=>$orders['size_name'],
					'piecs'=>$orders['totalitems'],
					'wastage'=>$orders['wast_percent'],
					'mc_type'=>$orders['mc_type'],
					'mc'=>$orders['mc'],
					'cus_due_date'=>$orders['cus_due_date'],
					'orderstatus'=>$orders['orderstatus'],
					'itemname'=>$orders['itemname'],
					'design_no'=>((isset($orders['design_no'])) ?$orders['design_no'] :''),
					'rate'=>$orders['rate'],
					'status'=>$orders['status'],
					'image'=>$this->$model->get_order_images($orders['id_orderdetails']),
					'purity'=> $this->$ret_catalog_model->getCatPurity($orders['id_ret_category']),
					'sizes' =>$this->$model->get_product_size($orders['id_product']),
					'stn_amt'=>$orders['stn_amt'],
					'charge_value'=>$orders['charge_value'],
					'stone_details' =>$this->$model->get_order_stones($orders['id_orderdetails']),
					'other_charge_details' =>$this->$model->get_order_charges($orders['id_orderdetails']),
					);
				}
				echo json_encode($estiData);
			break;
			case 'delete':
				$this->db->trans_begin();
				$this->$model->deleteData('id_customerorder',$id,'customerorder');
				$this->$model->deleteData('id_customerorder',$id,'customerorderdetails');
				$this->$model->deleteData('order_id',$id,'ret_order_advance_payment');
				if( $this->db->trans_status()===TRUE)
				{
						$this->db->trans_commit();
								
						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),
	
							'event_date'    => date("Y-m-d H:i:s"),
	
							'module'        => 'Customer Order',
	
							'operation'     => 'Delete',
	
							'record'        =>  $id,
	
							'remark'        => 'Customer Order deleted successfully'
	
							);
							
	                    $this->log_model->log_detail('insert','',$log_data);
						$this->session->set_flashdata('chit_alert', array('message' => 'Order deleted successfully','class' => 'success','title'=>'Delete Order'));
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Delete Order'));
				}
				redirect('admin_ret_order/order/list');
			break;
			case "update":
			      //echo"<pre>"; print_r($id);exit;
				  $updData = $_POST;
				  $updData['order']['order_date'] = date('Y-m-d',strtotime(str_replace("/","-",$updData['order']['order_date'])));
 				  $updData['adv']['advance_date'] = (!empty($updData['order']['advance_date']) ?date('Y-m-d',strtotime(str_replace("/","-",$updData['adv']['advance_date']))):NULL );
				  $updData['adv']['cheque_date'] = (!empty($updData['order']['cheque_date']) ?date('Y-m-d',strtotime(str_replace("/","-",$updData['adv']['cheque_date']))):NULL );
				  $id= $updData['order']['order_id'];
				    //echo "<pre>";print_r($updData['o_item']);exit;
					foreach ($updData['o_item'] as $d)
				    {
						$i=1;
						/* if($d['due_date']!='')
                        {
                            $customer_due_date = date('Y-m-d',(strtotime('+'.$d['due_date'].' day')));
                        }else{
                            $customer_due_date = date('Y-m-d',(strtotime('+'.$this->$model->get_ret_settings('customer_due_date').' day')));
                        } */
						//$details=$this->$model->get_orderdetails_by_id($d['id_orderdetails']);
						$d['smith_remainder_date'] = (!empty($d['smith_remainder_date']) ?date('Y-m-d',strtotime(str_replace("/","-",$d['smith_remainder_date']))):NULL );
						$d['due_date'] = (!empty($d['due_date']) ?date('Y-m-d',strtotime(str_replace("/","-",$d['due_date']))):NULL );
						$d['smith_due_date'] = (!empty($d['smith_due_date']) ?date('Y-m-d',strtotime(str_replace("/","-",$d['smith_due_date']))):NULL );
						$customer_due_date = (!empty($d['due_date']) ?date('Y-m-d',strtotime(str_replace("/","-",$d['due_date']))):NULL );
						$d['id_orderdetails'] = (!empty($d['id_orderdetails'])? $d['id_orderdetails'] : NULL );
						$orderDetails = array(
								'ortertype'			=> (!empty($d['order_type']) ?$d['order_type'] :NULL ),
								'id_product'		=> (!empty($d['id_product']) ? $d['id_product'] :NULL ),
								'itemname'			=> (!empty($d['design']) ? $d['design'] :NULL ),
								'design_no'			=> (!empty($d['id_design']) ? $d['id_design'] :NULL ),
								'id_sub_design'		=> (!empty($d['id_sub_design']) ? $d['id_sub_design'] :NULL ),
								'id_purity'			=> (!empty($d['id_purity']) ? $d['id_purity'] :NULL ),
								'totalitems'		=> (!empty($d['totalitems']) ? $d['totalitems'] :NULL ),
								'weight'			=> (!empty($d['weight']) ? $d['weight'] :NULL ),
								'less_wt'			=> (!empty($d['less_wt']) && $d['less_wt'] != "" ? $d['less_wt'] :0 ),
								'net_wt'			=> (!empty($d['net_wt']) ? $d['net_wt'] :0 ),
								'mc'				=> (!empty($d['mc']) ? $d['mc'] :0 ),
								'id_mc_type'		=> (!empty($d['id_mc_type']) ? $d['id_mc_type'] :2),
								'wast_percent'		=> (!empty($d['wast_percent']) && $d['wast_percent'] != "" ? $d['wast_percent'] :0 ),
								'stn_amt'			=> ((!empty($d['stn_amt'])&&$d['stn_amt']!=null ) ? $d['stn_amt'] :0 ),
								'charge_value'		=> ((!empty($d['value_charge'])&&$d['value_charge']!=null ) ? $d['value_charge'] :0 ),
								'rate_per_gram'		=> (!empty($d['order_rate']) ? $d['order_rate'] :NULL ),
								'rate'		        => (!empty($d['rate']) ? $d['rate'] :NULL ),
								'total_sgst'		=> (!empty($d['sgst']) ? $d['sgst'] :0 ),
								'total_cgst'		=> (!empty($d['cgst']) ? $d['cgst'] :0 ),
								'total_igst'		=> (!empty($d['igst']) ? $d['igst'] :0 ),
								'size'				=> (!empty($d['size']) ? $d['size'] :NULL ),
								'cus_due_date'		=> $customer_due_date,
								'smith_remainder_date'=> (!empty($d['smith_remainder_date']) ? $d['smith_remainder_date'] :NULL ),
								'smith_due_date'	=> (!empty($d['smith_due_date']) ? $d['smith_due_date'] :NULL ),
								'description'		=> (!empty($d['description']) ? $d['description'] :NULL ),
								'image'			    => (!empty($d['image']) ?$d['image'].$d['image'] :((!empty($d['image']) ?$d['image'] :NULL ))),
								'tag_id'			=> (!empty($d['tag_id']) ? $d['tag_id'] :NULL ),
								'tag_name'			=> (!empty($d['tag_name']) ? $d['tag_name'] :NULL )
							);
							//echo "<pre>"; print_r($d['id_orderdetails']);exit;
							if(isset($d['id_orderdetails']))
							{
								$orderDetails['id_customerorder'] = $id;
								$this->db->trans_begin();
								$upstataus = $this->$model->updateData($orderDetails,'id_orderdetails',$d['id_orderdetails'],'customerorderdetails');
								//print_r($this->db->last_query());exit;
								$CusOrderImg_delete_id = $this->$model->deleteData("id_orderdetails",$d['id_orderdetails'],'customer_order_image');
								$precious_imgs = "";
								$p_ImgData=[];
								$_FILES = [];
								$p_ImgData = json_decode($d['order_img']);
								if(sizeof($p_ImgData) > 0)
								{
								    $_FILES['order_images']=[];
									foreach($p_ImgData as $precious)
									{
										$imgFile = $this->base64ToFile($precious->src);
										$_FILES['order_images'][] = $imgFile;
									}
								}
								if(!empty($_FILES))
								{
									$img_arr = array();
									$folder =  self::IMG_PATH."orders/";
									if (!is_dir($folder)) {
										mkdir($folder, 0777, TRUE);
									}
									if(isset($_FILES['order_images']))
									{
										foreach($_FILES['order_images'] as $file_key => $file_val){
											if($file_val['name'])
											{
												$img_name =  "t_". mt_rand(120,1230).".jpg";
												$path = $folder."/".$img_name;
												$result = $this->upload_img('image',$path,$file_val['tmp_name']);
												if($result)
												{
													$insImageId = $this->$model->insertData(array('id_orderdetails'=>$d['id_orderdetails'],'image'=>$img_name),'customer_order_image');
												}
											}
										}
									}
								}
								$stone_details = json_decode($d['stone_details'],true);
								//echo "<pre>";print_r($stone_details);exit;
								$order_stone_delete_id = $this->$model->deleteData("order_item_id",$d['id_orderdetails'],'ret_order_item_stones');
								if(sizeof($stone_details)>0)
								{
									foreach($stone_details as $stn)
									{
										$stone_data=array(
											'order_id'          =>   $id,
											'order_item_id'     =>   $d['id_orderdetails'],
											'pieces'            =>   $stn['stone_pcs'],
											'wt'            	=>	$stn['stone_wt'],
											'stone_id'      	=>	$stn['stone_id'],
											'uom_id'      	    =>	$stn['stone_uom_id'],
											'price'         	=>	$stn['stone_price'],
											'is_apply_in_lwt'	=>	$stn['show_in_lwt'],
											'stone_cal_type' 	=>	$stn['stone_cal_type'],
											'rate_per_gram'  	=>	$stn['stone_rate'],
										);
											$stoneInsert = $this->$model->insertData($stone_data,'ret_order_item_stones');
											//print_r($this->db->last_query());exit;
									}
								}
								$charges_details = json_decode($d['charges_details'],true);
								$order_charge_delete_id = $this->$model->deleteData("order_item_id",$d['id_orderdetails'],'ret_order_other_charges');
								if(sizeof($charges_details)>0)
								{
									foreach($charges_details as $charge)
									{
										$charge_data=array(
											'order_id'          =>  $id,
											'order_item_id'     => $d['id_orderdetails'],
											'id_charge'    	=>	$charge['id_charge'],
											'amount'       	=>	$charge['value_charge'],
										);
										$this->$model->insertData($charge_data,'ret_order_other_charges');
									}
								}
							}
							else
							{
								$str = date('m').''.date('y');
								$order_no = str_pad($str,6,"0",STR_PAD_LEFT).'-'.time();
								if($id > 0)
								{
									$orderDetails['orderno'] =$updData['order']['order_no']."-".$i;
									$orderDetails['id_customerorder'] = $id;
									$orderDetails['id_employee'] = $this->session->userdata('uid');
									$this->db->trans_begin();
									$insOrderDet = $this->$model->insertData($orderDetails,'customerorderdetails');
									//print_r($this->db->last_query());exit;
								}
								$stone_details = json_decode($d['stone_details'],true);
								if(sizeof($stone_details)>0)
								{
									foreach($stone_details as $stn)
									{
										$stone_data=array(
												'order_id'          =>   $id,
												'order_item_id'     =>   $insOrderDet,
												'pieces'            =>   $stn['stone_pcs'],
												'wt'            	=>	$stn['stone_wt'],
												'stone_id'      	=>	$stn['stone_id'],
												'price'         	=>	$stn['stone_price'],
												'is_apply_in_lwt'	=>	$stn['show_in_lwt'],
												'stone_cal_type' 	=>	$stn['stone_cal_type'],
												'rate_per_gram'  	=>	$stn['stone_rate'],
											);
											$stoneInsert = $this->$model->insertData($stone_data,'ret_order_item_stones');
											//print_r($this->db->last_query());exit;
									}
								}
								$charges_details = json_decode($d['charges_details'],true);
								if(sizeof($charges_details)>0)
								{
									foreach($charges_details as $charge)
									{
										$charge_data=array(
											'order_id'       =>  $id,
											'order_item_id'  => $insOrderDet,
											'id_charge'    	 =>	$charge['id_charge'],
											'amount'       	 =>	$charge['value_charge'],
										);
										$this->$model->insertData($charge_data,'ret_order_other_charges');
									}
								}
								if($d['order_type'] == 5 && $d['tag_id'] > 0)
								{
									$data = array(
										'id_orderdetails'=> $insOrderDet,
										'updated_time' 	=> date("Y-m-d H:i:s"),
										'updated_by' 	=> $this->session->userdata('uid')
									);
									$status = $this->$model->updateData($data,'tag_id',$tag_id,'ret_taging');
									$this->$model->updateData(array('orderstatus'=>4,'deliverydate'=> date("Y-m-d H:i:s")),'id_orderdetails',$insOrderDet,'customerorderdetails');
									$this->$model->updateData(array('orderstatus'=>4,'deliveredon'=> date("Y-m-d H:i:s")),'id_order',$insOrderDet,'joborder');
									if($status)
									{
										$log_data = array(
													'id_log'        => $this->session->userdata('id_log'),
													'event_date'    => date("Y-m-d H:i:s"),
													'module'        => 'Tagging',
													'operation'     => 'Update',
													'record'        =>  $tag_id,
													'remark'        => 'Order link updated successfully through Order Form'
												);
										$this->log_model->log_detail('insert','',$log_data);
									}
								}
							}
							$i++;
						}
							if($this->db->trans_status()===TRUE)
							{
								$this->db->trans_commit();

								$log_data = array(
									'id_log'        => $this->session->userdata('id_log'),
									'event_date'    => date("Y-m-d H:i:s"),
									'module'        => 'Customer Order',
									'operation'     => 'Update',
									'record'        =>  $upstataus,
									'remark'        => 'Customer Order link updated successfully'
								);
						       $this->log_model->log_detail('insert','',$log_data);

								$this->session->set_flashdata('chit_alert',array('message'=>'Order updated successfully','class'=>'success','title'=>'Update Order'));
								$return_data=array('status'=>TRUE,'msg'=>'Order Created successfully..','id_customerorder'=>$insOrder,'order_for');
							}
							else
							{
								echo $this->db->last_query();exit;
								$this->db->trans_rollback();
								$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Update Order'));
									$return_data=array('status'=>false,'msg'=>'Unable to proceed the requested process','id_customerorder'=>'');
							}
							echo json_encode($return_data);
					break;
		    case 'ajax_order_cancel':
                    $this->db->trans_begin();

                    //update order status as cancel
                    $orderid = $_POST['order_id'];
                    $this->$model->updateData(array('order_status' => 6, 'reject_reason' => $_POST['remarks']),'id_customerorder',$orderid,'customerorder');
                    $this->$model->updateData(array('orderstatus' => 6, 'reject_reason' => $_POST['remarks']),'id_customerorder',$orderid,'customerorderdetails');
                    $orderdetails = $this->$model->get_order_details($orderid);
                    foreach($orderdetails as $okey=>$oval){
                        $this->$model->updateData(array('id_orderdetails' => NULL),'id_orderdetails',$oval['id_orderdetails'],'ret_taging');
                    }
                    $order_advance = $this->$model->get_order_total_advance($orderid);
                    $bill_no = $this->$model->bill_no_generate($orderdetails[0]['order_from'], $order_advance['is_eda']);
                    $fin_year       = $this->$model->get_FinancialYear();
                    if($order_advance > 0){
                        $id_customer = $this->$model->get_order_customer_id($orderid);
                        //Update into issue receipt table
                        $receiptinsData = array(
        					'type'			=>2,
        					'amount'		=>$order_advance['advance_amount'],
        					'weight'		=> 0,
        					'bill_no'		=> $bill_no,
        					'receipt_type'	=>5,
        					'id_branch'		=> $orderdetails[0]['order_from'],
        					'id_customer'	=>$id_customer,
        					'rate_per_gram'	=>NULL,
        					'rate_calc'		=>1,
        					'receipt_as'	=>1,
        					'store_receipt_as'=>1,
        					'pan_no'		=>  NULL ,
        					'narration'	 	=> 'Order Advance Transfer - ' . $orderid,
        					'created_by' 	=>$this->session->userdata('uid'),
        					'counter_id'    => NULL,
        					'created_on' 	=> date("Y-m-d H:i:s"),
        					'bill_date' 	=> date("Y-m-d H:i:s"),
        					'fin_year_code' => $fin_year['fin_year_code'],
        					'id_customerorder' =>$orderid,
							'is_eda'		=> $order_advance['is_eda']
        				);
                        $receiptinsId = $this->$model->insertData($receiptinsData,'ret_issue_receipt');
                    }
                    if( $this->db->trans_status()===TRUE)
				    {
				    	 $this->db->trans_commit();
						 $log_data = array(

							'id_log'        => $this->session->userdata('id_log'),
	
							'event_date'    => date("Y-m-d H:i:s"),
	
							'module'        => 'Customer Order',
	
							'operation'     => 'Cancel',
	
							'record'        =>  $orderid,
	
							'remark'        => 'Customer Order  cancelled successfully'
	
							);
							
	                     $this->log_model->log_detail('insert','',$log_data);
				    	 echo json_encode(array("status" => true, "message" => "Order cancel successfully"));
				    }else{
				         $this->db->trans_rollback();
				         echo json_encode(array("status" => false, "message" => "Could not cancel this order"));
				    }
			break;
			case 'cancel_order_item':
				$this->db->trans_begin();
				$id_orderdetails = $_POST['id_orderdetails'];
				$this->$model->updateData(array('orderstatus' => 6, 'reject_reason' => $_POST['remarks'],'cancelled_by'=>$this->session->userdata('uid'),'order_cancelled_date'=>date("Y-m-d H:i:s")),'id_orderdetails',$id_orderdetails,'customerorderdetails');
					// print_r($this->db->last_query());exit;
				$orderdetails = $this->$model->get_tagorder_details($id_orderdetails);
				if($orderdetails)
				{
					$this->$model->updateData(array('id_orderdetails' => NULL),'id_orderdetails',$id_orderdetails,'ret_taging');
					//print_r($this->db->last_query());exit;
				}
				if($this->db->trans_status()===TRUE)
			    {
			    	 $this->db->trans_commit();

					 $log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'Customer Order',

						'operation'     => 'Cancel',

						'record'        =>  $id_orderdetails,

						'remark'        => 'Customer Order cancelled successfully'

						);
						
					 $this->log_model->log_detail('insert','',$log_data);

			    	 echo json_encode(array("status" => true, "message" => "Order cancel successfully"));
			    }else{
			         $this->db->trans_rollback();
			         echo json_encode(array("status" => false, "message" => "Could not cancel this order"));
			    }
			break;
			case 'delete_order_item':
				$id_orderdetails = (int) $this->input->post('id_orderdetails');

				// Guard 1: Fetch item row
				$item = $this->$model->get_customerorder_details($id_orderdetails);
				if (empty($item)) {
					echo json_encode(array('status' => false, 'message' => 'Order item not found'));
					return;
				}

				// Guard 2: Item must be at Ordered status (0) — orderstatus=0 is the definitive "unassigned" indicator
				if ($item['orderstatus'] != 0) {
					echo json_encode(array('status' => false, 'message' => 'Cannot delete — item is already in progress or assigned'));
					return;
				}

				$id_customerorder = $item['id_customerorder'];

				// Guard 3: Only Customer Order types (2 & 5) — not Repair Orders (3 & 4)
				$header = $this->$model->getOrder($id_customerorder);
				if (!in_array($header['order_type'], array(2, 5))) {
					echo json_encode(array('status' => false, 'message' => 'Delete item is only allowed for Customer Orders'));
					return;
				}

				$this->db->trans_begin();

				// Clear tag reference if this item is linked to a tag
				$tagDetails = $this->$model->get_tagorder_details($id_orderdetails);
				if ($tagDetails) {
					$this->$model->updateData(array('id_orderdetails' => NULL), 'id_orderdetails', $id_orderdetails, 'ret_taging');
				}

				// Delete all child records
				$this->$model->deleteData('order_item_id', $id_orderdetails, 'ret_order_item_stones');
				$this->$model->deleteData('id_orderdetails', $id_orderdetails, 'customer_order_image');
				$this->$model->deleteData('order_item_id', $id_orderdetails, 'ret_order_other_charges');

				// Delete the item row itself
				$this->$model->deleteData('id_orderdetails', $id_orderdetails, 'customerorderdetails');

				// Recalculate header totals from remaining non-cancelled items
				$remaining = $this->$model->get_order_item_totals($id_customerorder);
				$this->$model->updateData(
					array(
						'order_pcs' => $remaining['total_pcs'],
						'order_approx_wt' => $remaining['total_wt']
					),
					'id_customerorder',
					$id_customerorder,
					'customerorder'
				);

				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$log_data = array(
						'id_log' => $this->session->userdata('id_log'),
						'event_date' => date("Y-m-d H:i:s"),
						'module' => 'Customer Order',
						'operation' => 'Delete Item',
						'record' => $id_orderdetails,
						'remark' => 'Order item deleted — karigar not assigned (order: ' . $id_customerorder . ')'
					);
					$this->log_model->log_detail('insert', '', $log_data);
					echo json_encode(array('status' => true, 'message' => 'Order item deleted successfully'));
				} else {
					$this->db->trans_rollback();
					echo json_encode(array('status' => false, 'message' => 'Could not delete order item. Please try again.'));
				}
				break;
			case 'getOrderByCus':
						$data = $this->$model->getOrderByCus($_POST['id_customer']);
						echo json_encode($data);
					break;
			case 'update_status':
						$data = array('status_karigar' => $status,
						'updateon'	  => date("Y-m-d H:i:s"),
					    'updatedby'      => $this->session->userdata('uid'));
						$model=self::CAT_MODEL;
						$updstatus = $this->$model->updateData($data,'id_karigar',$id,'ret_karigar');
						if($updstatus)
						{
							$this->session->set_flashdata('chit_alert',array('message'=>'User status updated as '.($status==1 ? 'Approved' : 'Not Approved').' successfully.','class'=>'success','title'=>'User Status'));
						}
						else
						{
							$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'User Status'));
						}
						redirect('admin_ret_order/order/list');
						break;
			case 'repair':
					$data['main_content'] = "order/repair_order/list" ;
					$this->load->view('layout/template', $data);
			break;
			case 'repair_item_details':
				   //order_no as Id_order
					$data=$this->$model->repair_detail($order_no);
					$data['main_content'] = "order/repair_order/item_details" ;
					$this->load->view('layout/template', $data);
			break;
			case 'repair_order_list':
				$data=$this->$model->get_repair_orders_list($_POST);
				echo json_encode($data);
			break;
			default:
						$range['from_date']  = $this->input->post('from_date');
						$range['to_date']  = $this->input->post('to_date');
						$profile=$this->$model->get_profile_settings($this->session->userdata('profile'));
					  	$orders = $this->$model->ajax_getOrders($_POST);
					  	$access = $this->admin_settings_model->get_access('admin_ret_order/order/list');
				        $data = array(
				        					'orders' => $orders,
											'profile' =>$profile,
											'access' => $access
				        			 );
						echo json_encode($data);
		}
	}
	function get_img_by_order_id()
    {
        $model="ret_order_model";
        $data = $this->$model->get_order_images($_POST['order_id']);
        echo json_encode($data);
    }
	function estimation($type=""){
		switch($type){
			case 'getEstiBySearch':
					$data = $this->ret_order_model->fetchEstiBySearch($_POST['searchTxt']);
					echo json_encode($data);
				break;
			case 'getEstiDetails':
					$data = $this->ret_order_model->getEstiDetailsById($_POST['esti_id']);
					echo json_encode($data);
				break;
		}
	}
	function customer_neworders()
	{
	    $model = "ret_order_model";
		$SETT_MOD = "admin_settings_model";
	    $data['order']   = $this->$model->empty_rec_order();
	    $data['branch']  =$data['order']['order']['is_ho_branch'];
		$data['main_content'] = "order/neworder/list" ;
		$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_order/customer_neworders/list');

		$this->load->view('layout/template', $data);
	}
	/*
	function neworders()
	{
	     $model = "ret_order_model";
	    $data['order']       = $this->$model->empty_rec_order();
	    $data['branch']   =$data['order']['order']['is_ho_branch'];
		$data['main_content'] = "order/neworder/list" ;
		$this->load->view('layout/template', $data);
	}
	*/	
	function neworders()

	{

	    $model = "ret_order_model";

	    $data['order']       = $this->$model->empty_rec_order();

	    $data['branch']   =$data['order']['order']['is_ho_branch'];

		$data['main_content'] = "order/repair_order/neworders" ;

		$this->load->view('layout/template', $data);

	}
	function ajax_get_neworder()
	{
		$model = "ret_order_model";
		$from_date=$this->input->post('from_date');
		$to_date=$this->input->post('to_date');
		$id_branch=$this->input->post('id_branch');
		$id_karigar=$this->input->post('id_karigar');
		$order_type=$this->input->post('order_type');
		$repair_type=$this->input->post('repair_type');
		$work_type=$this->input->post('work_type');
		$id_employee=$this->input->post('id_employee');
		$id_counter=$this->input->post('id_counter');
	
		$customer_order=array();
		$order=$this->$model->get_new_orderlist($from_date,$to_date,$id_branch,$id_karigar,$order_type,$repair_type,$work_type,$id_employee,$id_counter);
		foreach ($order as $data) {
			$order_img= array(explode('#',$data['image']));
			if($order_img[0][0])
			{
				 $img=$this->config->item('base_url').'assets/img/orders/'.$order_img[0][0];
			}
			else{
				$img='';
			}
			 $customer_order[]=array(
			 			'orderno'=>$data['orderno'],
			 			'id_orderdetails'=>$data['id_orderdetails'],
			 			'id_customerorder'=>$data['orderno'],
			 			'size'=>$this->isValueset($data['size']),
			 			'totalitems'=>($data['totalitems']),
			 			'weight'=>$this->isValueset($data['weight']),
			 			'description'=>$this->isValueset($data['description']),
			 			'reject_reason'=>$this->isValueset($data['reject_reason']),
			 			'cus_name'=>$this->isValueset($data['cus_name']),
			 			'karigar_name'=>$this->isValueset($data['karigar_name']),
			 			'cus_due_date'=>$this->isValueset($data['cus_due_date']),
			 			'id_employee'=>$this->isValueset($data['id_employee']),
			 			'customer_ref_no'=>$this->isValueset($data['customer_ref_no']),
			 			'id_vendor'=>$this->isValueset($data['id_vendor']),
			 			'emp_name'=>$this->isValueset($data['emp_name']),
			 			'tag_code'=>$this->isValueset($data['tag_code']),
			 			'branch_name'=>$this->isValueset($data['branch_name']),
			 			'repair_type'=>$this->isValueset($data['repair_type']),
			 			'work_at'=>$this->isValueset($data['work_at']),
			 			'id_product'=>$data['id_product'],
						'id_karigar'=>$data['id_karigar'],
			 			'id_category'=>$data['cat_id'],
						'employee_name'=>$data['employee_name'],
			 			'order_img'=>$img,
			 			'product_name'=>$data['product_name'],
			 			'sub_design_name'=>$data['sub_design_name'],
			 			'design_name'=>$data['design_name'],
			 			'ortertype'=>$data['ortertype'],
			 			'cus_mobile'=>$data['mobile'],
			 			'hsn_code'=>$data['hsn_code'],
			 			'product_short_code'=>$data['product_short_code'],
			 			'order_date'=>$data['order_date'],
			 			'orderstatus'=>$data['order_status'],
			 			'color'=>$data['color'],
			 			'cus_ord_status'=>$data['cus_ord_status'],
			 			'smith_due_date'=>$data['smith_due_date'],
			 			'kar_remark'=>$data['kar_remark'],
						'pur_no'=>$data['pur_no'],
			 			'orter_type'=>($data['ortertype']==2 ? 'Customer Order': 'Customer Repair'),
			 );
		}
		echo json_encode($customer_order);
	}
	function assign_customer_order()
	{
		$req_status=$this->input->post('req_status');
		$req_data=$this->input->post('req_data');
		$id_vendor=$this->input->post('id_vendor');
		$id_branch=$this->input->post('id_branch');
		$assign_to 		=$this->input->post('assign_to');
		$id_employee 	=$this->input->post('id_employee');
		$model = "ret_order_model";
		$set_model="admin_settings_model";
		$id_customer_orders='';
    if($req_status==1)
    {
		foreach ($req_data as $data)
		{
			$assigned = TRUE;
			$customer_order_details=$this->$model->get_customerorder_details($data['id_orderdetails']);
		    $smith_due_dt=date_create($data['smith_due_dt']);
			$this->db->trans_begin();
		    $upd_data=array('assign_to'=>$assign_to,
			'id_karigar'=>($assign_to ==1 ? $id_vendor:NULL),
			'emp_id'=>($assign_to ==2 ? $id_employee:NULL),
			'orderstatus'=>3,
			'smith_due_date'=>date_format($smith_due_dt,"Y-m-d"),
			'assigned_by'=>$this->config->item('uid'),
			'assigned_on'=> date("Y-m-d H:i:s"),
			'kar_remark'=> $data['kar_remark'],
			'smith_remainder_date'=>date("Y-m-d", strtotime('+2 days'))); // Work in Progress

			$this->$model->updateData($upd_data,'id_orderdetails',$customer_order_details['id_orderdetails'],'customerorderdetails');
		}
		if($this->db->trans_status()===TRUE)
		{
    		$this->db->trans_commit();
    		$response_data=array('status'=>true,'msg'=>'Karigar Assinged Successfully','id_customerorder'=>$id_customer_orders);
    		$this->session->set_flashdata('chit_alert',array('message'=>'Karigar Assinged Successfully','class'=>'success','title'=>'Assign Karigar'));
		}
		else
		{
    		$this->db->trans_rollback();
    		$response_data=array('status'=>false,'msg'=>'Unable to Procees Your Request');
    		$this->session->set_flashdata('chit_alert',array('message'=>'Unable to Procees Your Request','class'=>'danger','title'=>'Assign Karigar'));
		}
	}
	else
	{
		foreach ($req_data as $data)
		{
			$assigned = TRUE;
			$customer_order_details=$this->$model->get_customerorder_details($data['id_orderdetails']);
			$assign_order=$this->$model->get_joborder_details($customer_order_details['id_orderdetails']);
			$this->db->trans_begin();
					$upd_data=array('orderstatus'=>6);
					$this->$model->updateData($upd_data,'id_orderdetails',$customer_order_details['id_orderdetails'],'customerorderdetails');
					$response_data=array('status'=>true,'msg'=>'New order Rejected Successfully');
					$this->session->set_flashdata('chit_alert',array('message'=>'New order Rejected Successfully','class'=>'success','title'=>'New Order'));
		}
		if($upd_data)
		{
		$this->db->trans_commit();
		$response_data=array('status'=>true,'msg'=>'New order Rejected Successfully');
		$this->session->set_flashdata('chit_alert',array('message'=>'New order Rejected Successfully','class'=>'success','title'=>'New Order'));
		}
		else
		{
		$this->db->trans_rollback();
		$response_data=array('status'=>false,'msg'=>'Already Assigned to Karigar');
						$this->session->set_flashdata('chit_alert',array('message'=>'Already Assigned to Karigar','class'=>'danger','title'=>'New Order'));
		}
	}
	    echo json_encode($response_data);
}
function get_karigar_acknowladgement()
{
	$model = "ret_order_model";
    $set_model="admin_settings_model";
     $service = $this->$set_model->get_service_by_code('KAR_ALOC');
     $id_customer_orders=$_GET['id_order'];
    $orders=explode(',',$id_customer_orders);
    foreach($orders as $items)
    {
        	$data['order']=$this->$model->get_karigar_orders($items);
        	//echo "<pre>";print_r($data);exit;
        	$this->load->helper(array('dompdf', 'file'));
        	$dompdf = new DOMPDF();
            $html = $this->load->view('order/stock_order/print/vendor_ack', $data,true);
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
}
function get_all_branch()
{
	$model="ret_order_model";
	$data=$this->$model->get_all_branch();
	echo json_encode($data);
}
function get_ordersby_id($id)
{
	$model="ret_order_model";
	$data=$this->$model->get_orderdetails_by_id($id);
	$order_img= array(explode('#',$data['image']));
	 $imges=array();
	foreach ($order_img[0] as $img) {
			if($img)
			{
				 $imges[]=$this->config->item('base_url').'assets/img/orders/'.$img;
			}
	}
		 $customer_order=array(
			 			'orderno'=>$data['orderno'],
			 			'id_orderdetails'=>$data['id_orderdetails'],
			 			'id_customerorder'=>$data['orderno'],
			 			'size'=>$this->isValueset($data['size']),
			 			'totalitems'=>$this->isValueset($data['totalitems']),
			 			'weight'=>$this->isValueset($data['weight']),
			 			'description'=>$this->isValueset($data['description']),
			 			'reject_reason'=>$this->isValueset($data['reject_reason']),
			 			'cus_name'=>$this->isValueset($data['cus_name']),
			 			'karigar_name'=>$this->isValueset($data['karigar_name']),
			 			'cus_due_date'=>$this->isValueset($data['cus_due_date']),
			 			'id_employee'=>$this->isValueset($data['id_employee']),
			 			'customer_ref_no'=>$this->isValueset($data['customer_ref_no']),
			 			'id_vendor'=>$this->isValueset($data['id_vendor']),
			 			'emp_name'=>$this->isValueset($data['emp_name']),
			 			'branch_name'=>$this->isValueset($data['branch_name']),
			 			'orter_type'=>($data['ortertype']==1 ? 'Catalog Order' : ($data['ortertype']==2) ? 'Customer Order' : ($data['ortertype']==3 ? 'Repair Order':'') ),
			 			'id_product'=>$data['id_product'],
			 			'id_category'=>$data['cat_id'],
			 			'product_name'=>$data['product_name'],
			 			'ortertype'=>$data['ortertype'],
			 			'mobile'=>$data['mobile'],
			 			'hsn_code'=>$data['hsn_code'],
			 			'product_short_code'=>$data['product_short_code'],
			 			'order_date'=>$data['order_date'],
			 			'order_image'=>$imges,
			 			'orderstatus'=>$data['order_status'],
			 			'color'=>$data['color'],
			 			'purity'=>$this->isValueset($data['purity']),
			 );
	echo json_encode($customer_order);
}
function updatereject_reason()
{
    $model="ret_order_model";
	$upd_data['reject_reason']=$this->input->post('reject_reason');
	$id_orderdetails=$this->input->post('id_orderdetails');
	$this->db->trans_begin();
	if($upd_data['reject_reason']!='')
	{
		$upd_data['orderstatus']=8;
		$customer_order_details=$this->$model->get_customerorder_details($id_orderdetails);
		$status=$this->$model->updateData($upd_data,'id_orderdetails',$id_orderdetails,'customerorderdetails');
	}
	else
	{
		$status=false;
	}
	if($status==true)
	{
	$this->db->trans_commit();
	$this->session->set_flashdata('chit_alert',array('message'=>'New order Rejected  Successfully','class'=>'success','title'=>'New Order'));
	}
	else
	{
	$this->db->trans_rollback();
	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to Proceed Your Request','class'=>'danger','title'=>'New Order'));
	}
	echo true;
}
function get_order_images($orders)
	{
			$order_img= array(explode('#',$orders['image']));
			$images=array();
			foreach ($order_img[0] as $img)
			{
				if($img)
				{
					$images[]=array(
					'src'=>$this->config->item('base_url').'assets/img/orders/'.$img,
					'img_name'=>$img
					);
				}
			}
			return $images;
	}
function upload_orderimg()
{
	$imgpath='assets/img/orders/';
	$model='ret_order_model';
	$file_name='';
	$name='';
	$data=array();
	foreach($_FILES['file']['name'] as $file_key => $files)
	{
	if (!is_dir($imgpath)) {
	mkdir($imgpath, 0777, TRUE);
	}
	if($files)
	{
	$name=time().$files;
	$file_name.=time().$files.'#';
	}
	$img=$_FILES['file']['tmp_name'][$file_key];
	$imgpath='assets/img/orders/'.$name;
	$result = $this->upload_img('orderimg',$imgpath,$img);
	}
	$data=array('msg'=>true,'name'=>$file_name);
	echo json_encode($data);
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
		  imagejpeg($tmp, $dst);
		  return true;
	}
	function remove_img()
	{
		$model="ret_order_model";
		$file=$this->input->post('file');
		$id=$this->input->post('id');
		$images=$this->$model->get_orderdetails_by_id($id);
		$imgs = explode('#',$images['image']);
		unlink(SELF::IMG_PATH.'orders/'.$file);
		$totImgs = sizeof($imgs);
			foreach($imgs as $k => $img){
				if($img == $file) { // img1.jpg#img1.jpg
					if($k == $totImgs-1)
						$newImgValue = str_replace($file, '', $images['image']);
					else
						$newImgValue = str_replace($file."#", '', $images['image']);
				}
			}
			$status=$this->$model->updateData(array('image' =>(!empty($newImgValue)) ? $newImgValue:NULL),'id_orderdetails',$id,'customerorderdetails');
			//print_r($this->db->last_query());exit;
			if($status){
			echo "Picture removed successfully";
		}
	}
	function get_weight_range()
    {
    	$model = "ret_order_model";
    	$data=$this->$model->get_weight_range($_POST['searchTxt']);
    	echo json_encode($data);
    }
    function get_product_size()
    {
    	$model = "ret_order_model";
    	$data=$this->$model->get_product_size($_POST['id_product']);
    	echo json_encode($data);
    }
    public function vendor_acknowladgement($id)
	{
		$set_model = "admin_settings_model";
		$model = "ret_order_model";
		$data['comp_details'] = $this->$set_model->get_company();
	    $data['order']=$this->$model->get_karigar_orders($id);
		$this->load->helper(array('dompdf', 'file'));
	        $dompdf = new DOMPDF();
			$html = $this->load->view('order/stock_order/print/vendor_ack', $data,true);
			$dompdf->load_html($html);
			$dompdf->set_paper('A4', "portriat" );
			$dompdf->render();
			while(ob_get_level()) ob_end_clean();
			$dompdf->stream("VendorAck.pdf",array('Attachment'=>0));
	}
	//Order Images and Description
		public function get_img_by_id()
		{
			$response_data = [];
			$id_orderdetails = $this->input->post('id_orderdetails');
			$model = "ret_order_model";
			$data=$this->$model->get_img_by_id($id_orderdetails);
			$image=explode("##",$data['image']);
			foreach($image as $val)
			{
				if($val!='')
				{
					$response_data[]=array(
						'src'=>$this->config->item('base_url').'assets/img/order/customer_order/'.$id_orderdetails.'/'.$val,
						'id_orderdetails'=>$id_orderdetails,
						'img_name'=>$val,
					);
				}
			}
			echo json_encode($response_data);
		}
		public function update_order_image()
		{
			$response_data=array();
			$img_array = [];
			$model = "ret_order_model";
			$id_orderdetails = $this->input->post('id_orderdetails');
			$image_data = $this->input->post('image');
			$this->db->trans_begin();
			$img_details=$this->$model->get_img_by_id($id_orderdetails);
			foreach($image_data as $image)
			{
				$folder =  self::IMG_PATH."order/customer_order/".$id_orderdetails;
				if (!is_dir($folder)) {
				mkdir($folder, 0777, TRUE);
				}
				$image_parts = explode(";base64,",$image);
				$image_type_aux = explode("image/", $image_parts[0]);
				$image_type = $image_type_aux[1];
				$image_base64 = base64_decode($image_parts[1]);
				$file = $folder.'/'.mt_rand(120,1230).".jpg";
				$fileName = basename($file, ".jpg");
				file_put_contents($file, $image_base64);
				$name = "##".$fileName.'.jpg';
				array_push($img_array, $name);
			 }
			$img_data = implode("",$img_array);
			$data['image']= $img_details['image'].$img_data;
			$status=$this->$model->updateData($data,'id_orderdetails',$id_orderdetails,'customerorderdetails');
			if($this->db->trans_status()===TRUE)
			{
				$this->db->trans_commit();
				$response_data=array('status'=>TRUE,'message'=>'Image Uploaded Successfully');
			}else{
				$this->db->trans_rollback();
				$response_data=array('status'=>FALSE,'message'=>'Unable To Proceed Your Request');
			}
			echo json_encode($response_data);
		}
		public function update_and_retrive_order_image()
		{
			$img_array = [];
			$id_orderdetails = $this->input->post('id_orderdetails');
			$retrive_image = $this->input->post('retrive_image');
			$image_data = $this->input->post('new_image');
			foreach($retrive_image as $value)
			{
				$add_hash = "#".$value;
				array_push($img_array, $add_hash);
			}
			foreach($image_data as $image)
			{
				if($addData['order']['order_for']==1)
					$folder =  self::IMG_PATH."customer_order/";
					if (!is_dir($folder)) {
					mkdir($folder, 0777, TRUE);
					}
				   $image_parts = explode(";base64,",$image);
				   $image_type_aux = explode("image/", $image_parts[0]);
				   $image_type = $image_type_aux[1];
				   $image_base64 = base64_decode($image_parts[1]);
				   $file = mt_rand(120,1230).".jpg";
				   $fileName = basename($file, ".jpg");
				   file_put_contents($file, $image_base64);
				   $name = "##".$fileName;
				   array_push($img_array, $name);
			}
				   $img_data = implode("",$img_array);
				   $data['image'] = $img_data;
				   $model = "ret_order_model";
				   $status=$this->$model->updateData($data,'id_orderdetails',$id_orderdetails,'customerorderdetails');
				   echo json_encode($status);
		}
		public function insert_retrive_img()
		{
			$id_orderdetails = $this->input->post('id_orderdetails');
			echo json_encode(true);
		}
		public function delete_order_img()
		{
			$response_data=array();
			$model= "ret_order_model";
			$id_orderdetails = $this->input->post('id_orderdetails');
			$delete_image = $this->input->post('image');
			$data = $this->$model->get_img_by_id($id_orderdetails);
			$image=explode("##",$data['image']);
			$updData['image']=NULL;
			foreach($image as $val)
			{
				if($val!='')
				{
					if($val!=$delete_image)
					{
						$updData['image'].='##'.$val;
					}
				}
			}
			$this->db->trans_begin();
			$status=$this->$model->updateData($updData,'id_orderdetails',$id_orderdetails,'customerorderdetails');
			unlink(SELF::IMG_PATH.'order/customer_order/'.$id_orderdetails.'/'.$delete_image);
				if($this->db->trans_status()===TRUE)
				{
					$this->db->trans_commit();
					$response_data=array('status'=>TRUE,'message'=>'Image Deleted Successfully');
				}else{
					$this->db->trans_rollback();
					$response_data=array('status'=>FALSE,'message'=>'Unable To Proceed Your Request');
				}
			echo json_encode($response_data);
		}
		public function update_order_des()
		{
			$id_orderdetails = $this->input->post('id_orderdetails');
			$description = $this->input->post('description');
			$model = "ret_order_model";
			$data_des['description'] = $description;
			$status=$this->$model->updateData($data_des,'id_orderdetails',$id_orderdetails,'customerorderdetails');
		    if($status)
		    {
		        $responseData=array('status'=>TRUE,'message'=>'Description Added Successfully..');
		    }else{
		        $responseData=array('status'=>FALSE,'message'=>'Description Added Successfully..');
		    }
		    echo json_encode($responseData);
		}
		public function get_dec_by_id()
		{
			$id_orderdetails = $this->input->post('id_orderdetails');
			$model = "ret_order_model";
			$data=$this->$model->get_dec_by_id($id_orderdetails);
			echo json_encode($data);
		}
	   //Order Images and Description
	   //SUB DESIGNS
	  function get_ActiveSubDesingns()
      {
    	     $model="ret_order_model";
    	     $data=$this->$model->getSearchSubDesign($_POST);
    	     echo json_encode($data);
      }
      function get_ActiveDesingns()
      {
    	     $model="ret_order_model";
    	     $data=$this->$model->getSearchDesign($_POST);
    	     echo json_encode($data);
      }
	   //SUB DESIGNS
	public function active_cat_product_list()
	{
	    $model = "ret_order_model";
		$data = $this->$model->get_active_cat_product_list();
		echo json_encode($data);
	}
	public function getIssueTaggingBySearch(){
		$model = "ret_order_model";
		$data = $this->$model->getIssueTaggingBySearch($_POST['searchTxt'], $_POST['searchField'], $_POST['id_branch']);
		echo json_encode($data);
	}
	public function get_tag_scan_details(){
		$model = "ret_order_model";
		$data = $this->$model->get_tag_scan_details($_POST);
		echo json_encode($data);
	}
	function repair_order($type,$id="")
	{
	    $model = "ret_order_model";
		$this->load->model('ret_catalog_model');
		switch($type)
		{
			case 'list':
					$data['main_content'] = "order/repair_order/list" ;
					$this->load->view('layout/template', $data);
			break;
			case 'add':
					$data['main_content'] = "order/repair_order/form" ;
					$this->load->view('layout/template', $data);
						$this->load->view('layout/common_customerslider');
			break;
			case 'repair_order_status':
			    $data['categories'] = $this->ret_order_model->getActiveCategories();
				$data['main_content'] = "order/repair_order/order_status" ;
				$this->load->view('layout/template', $data);
			break;
			case 'repair_order_list':
				$data=$this->$model->get_repair_orders_list($_POST);
				echo json_encode($data);
			break;
			case "edit":
				$data['order']  = $this->ret_order_model->getOrder($id);
				// print_r($data['order']);exit;
				$data['main_content'] = "order/repair_order/form" ;
				$this->load->view('layout/template', $data);
				$this->load->view('layout/common_customerslider');
			break;
			case "repair_ordered_list":
				$data  = $this->ret_order_model->get_repair_orderdetails($id);
				echo json_encode($data);
			break;
			case 'save':
			    $addData=$_POST['order'];
			    // echo "<pre>";
			    // print_r($_POST);
			    // echo "</pre>"; exit;
		        $fin_year       = $this->$model->get_FinancialYear();
				$order_from     = (!empty($addData['order_from']) ? $addData['order_from'] :NULL );
				$order_no       = $this->$model->generateOrderNo($order_from, $addData['order_type']);
				$dCData         = $this->admin_settings_model->getBranchDayClosingData($addData['order_from']);
				$order_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
 				$order = array(
 				    'fin_year_code'     => $fin_year['fin_year_code'],
	 				'order_type'		=> $addData['order_type'],
					 'work_at'			=> $addData['work_at'],
	 				'order_from'		=> $order_from,
	 				'order_no'          => $order_no,
	 				'order_for'			=> ($addData['order_type']==3 ? 2 :1),
	 				'order_date'		=> $order_datetime,
	 				'order_to'			=> (!empty($addData['order_to']) ? $addData['order_to']: NULL),
					'createdon'         => date("Y-m-d H:i:s"),
					'created_by'         => $this->session->userdata('uid'),
					'order_taken_by'    =>(!empty($addData['id_employee']) ? $addData['id_employee']: NULL),
					'counter_id'        => ($this->session->userdata('counter_id')!='' ? $this->session->userdata('counter_id'):NULL),
				);
				$this->db->trans_begin();
				$insOrder = $this->$model->insertData($order,'customerorder');
			    if($insOrder)
			    {
			        $orderItems = $_POST['order_item'];
			        $i=1;
			        $order_pcs=0;
			        $order_approx_wt=0;
			        foreach($orderItems['id_product'] as $key =>$val)
			        {
			            $order_pcs += $orderItems['piece'][$key];
			            $order_approx_wt+=$orderItems['weight'][$key];
						$orderDetails = array(
							'orderno'			=> $order_no."-".$i,
							'ortertype'		    => $addData['order_type'],
							'id_product'		=> $orderItems['id_product'][$key],
							'design_no'		    => (isset($orderItems['id_design'][$key]) ? $orderItems['id_design'][$key]:NULL),
							'id_sub_design'		=> (isset($orderItems['id_sub_design'][$key]) ? $orderItems['id_sub_design'][$key]:NULL),
							'pure_wt'		    => (isset($orderItems['purewt'][$key]) ? $orderItems['purewt'][$key]:0),
						    'less_wt'			=> (isset($orderItems['less_wt'][$key]) ? $orderItems['less_wt'][$key]:0),
							'net_wt'			=> (isset($orderItems['net_wt'][$key]) ? $orderItems['net_wt'][$key]:0),
						    'tag_id'		    => (isset($orderItems['tag_id'][$key]) ? $orderItems['tag_id'][$key]:NULL),
							'id_repair_master'	=> (isset($orderItems['repair'][$key]) ? $orderItems['repair'][$key]:NULL),
							'totalitems'		=> $orderItems['piece'][$key],
							'weight'			=> $orderItems['weight'][$key],
							'description'		=> ($orderItems['description'][$key]!='' ? $orderItems['description'][$key]:NULL),
							'order_date'		=> $order_datetime,
							'current_branch'	=> $order_from,
							'cus_due_date'		=> ( $orderItems['cus_due_days'][$key]!='' ? date('Y-m-d', strtotime($orderItems['cus_due_days'][$key])) :NULL),
						    'id_employee'       => $this->session->userdata('uid'),
					   );
						if($insOrder > 0){
							$orderDetails['id_customerorder'] = $insOrder;
							$insOrderDet = $this->$model->insertData($orderDetails,'customerorderdetails');
							// print_r($this->db->last_query());exit;
							$stone_details = json_decode($orderItems['stone_details'][$key],true);
							// print_r($stone_details);exit;

							if(sizeof($stone_details)>0)
							{
								foreach($stone_details as $stone)
								{
									$stone_data=array(
										'order_id'        	=>	$insOrder,
										'order_item_id'   	=>	$insOrderDet,
										'pieces'        	=>	$stone['stone_pcs'],
										'wt'            	=>	$stone['stone_wt'],
										'stone_id'      	=>	$stone['stone_id'],
										'uom_id'      	    =>	$stone['stone_uom_id'],
										'price'         	=>	$stone['stone_price'],
										'is_apply_in_lwt'	=>	$stone['show_in_lwt'],
										'stone_cal_type' 	=>	$stone['stone_cal_type'],
										'rate_per_gram'  	=>	$stone['stone_rate'],
										);
									//print_r($stone_data);exit;
									$stoneInsert = $this->$model->insertData($stone_data,'ret_order_item_stones');
								}
							}
							if($insOrderDet && $addData['order_type']==4)
							{
								$this->$model->updateData(array('id_orderdetails'=>$insOrderDet),'tag_id',$orderItems['tag_id'][$key],'ret_taging');
							}
							/* if($insOrderDet && $addData['order_type']==4)
							{
							    $this->$model->updateData(array('tag_status'=>8),'tag_id',$orderItems['tag_id'][$key],'ret_taging');
							    $tag_log=array(
                                            'tag_id'	  =>$orderItems['tag_id'][$key],
                                            'date'		  =>$order_datetime,
                                            'status'	  =>8,
                                            'from_branch' =>$addData['order_from'],
                                            'to_branch'	  =>NULL,
                                            'created_on'  =>date("Y-m-d H:i:s"),
                                            'created_by'  =>$this->session->userdata('uid'),
                                            );
                                            $this->$model->insertData($tag_log,'ret_taging_status_log');
							} */
							// echo"<pre>"; print_r($orderItems['order_img'][$key]);exit;

							//Image Upload
		 					$precious_imgs = "";
		 					$p_ImgData=[];
						    $_FILES = array();
		 					$p_ImgData = json_decode($orderItems['order_img'][$key]);
							if(sizeof($p_ImgData) > 0){
								foreach($p_ImgData as $precious){
									$imgFile = $this->base64ToFile($precious->src);
									$_FILES['order_img'][] = $imgFile;
								}
							}
						//	print_r($_FILES);exit;
							if(!empty($_FILES)){
								$img_arr = array();
							    $folder =  self::IMG_PATH."orders/";
								if (!is_dir($folder)) {
									mkdir($folder, 0777, TRUE);
								}
								if(isset($_FILES['order_img'])){
									$order_images = "";
									foreach($_FILES['order_img'] as $file_key => $file_val){
										if($file_val['name'])
										{
											// unlink($folder."/".$product['image']);
											$img_name =  $insOrderDet."_". mt_rand(100001,999999).".jpg";
											$path = $folder."/".$img_name;
											$result = $this->upload_img('image',$path,$file_val['tmp_name']);
											if($result){
												$insImageId = $this->$model->insertData(array('id_orderdetails'=>$insOrderDet,'image'=>$img_name),'customer_order_image');
												// print_r($this->db->last_query());exit;
											}
										}
									}
								}
							}
						}
						$i++;
			        }
			        $this->$model->updateData(array('order_pcs'=> $order_pcs, 'order_approx_wt' => $order_approx_wt),'id_customerorder',$insOrder,'customerorder');
			        // echo $this->db->last_query();exit;
			    }
			    if($this->db->trans_status()===TRUE)
				{
					$this->db->trans_commit();

					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'Repair Order',

						'operation'     => 'Add',

						'record'        =>  $insOrder,

						'remark'        => 'Repair Order added successfully'

						);

					$this->log_model->log_detail('insert','',$log_data);
					$response_data=array('status'=>TRUE,'message'=>'Order Created Successfully','id_customerorder'=>$insOrder);
					$this->session->set_flashdata('chit_alert',array('message'=>'Order Created Successfully','class'=>'success','title'=>'New Order'));
				}else{
					$this->db->trans_rollback();
					$response_data=array('status'=>FALSE,'message'=>'Unable To Proceed Your Request');
					$this->session->set_flashdata('chit_alert',array('message'=>'Unable To Proceed Your Request','class'=>'danger','title'=>'New Order'));
				}
				echo json_encode($response_data);
			break;

			case 'update':

				// echo "<pre>";
			    // print_r($_POST);
			    // echo "</pre>"; exit; 

			    $addData=$_POST['order'];
		        $fin_year       = $this->$model->get_FinancialYear();
				$order_from     = (!empty($addData['order_from']) ? $addData['order_from'] :NULL );
				$order_no       = $this->$model->generateOrderNo($order_from, $addData['order_type']);
				$dCData         = $this->admin_settings_model->getBranchDayClosingData($addData['order_from']);
				// print_r($dCData['entry_date']);exit;

				$order_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
				// print_r($order_datetime);exit;
				$customer_order  = (!empty($addData['id_customerorder']) ? $addData['id_customerorder'] :NULL );
 				$order = array(
 				    'fin_year_code'     => $fin_year['fin_year_code'],
	 				'order_type'		=> $addData['order_type'],
					'work_at'			=> $addData['work_at'],
	 				'order_from'		=> $order_from,
	 				'order_for'			=> ($addData['order_type']==3 ? 2 :1),
	 				// 'order_date'		=> $order_datetime,
	 				'order_to'			=> (!empty($addData['order_to']) ? $addData['order_to']: NULL),
					'updated_on'         => date("Y-m-d H:i:s"),
					'updated_by'         => $this->session->userdata('uid'),
					'order_taken_by'     => (!empty($addData['id_employee']) ? $addData['id_employee']: NULL),

				);
				$this->db->trans_begin();
				$insOrder = $this->$model->updateData($order,'id_customerorder',$customer_order,'customerorder');
				// print_r($this->db->last_query());exit;
				if($insOrder)
			    {
			        $orderItems = $_POST['order_item'];

					// echo "<pre>"; print_r($orderItems);exit;

			        $i=1;
			        $order_pcs=0;
			        $order_approx_wt=0;
			        foreach($orderItems['id_product'] as $key =>$val)
			        {
			            $order_pcs += $orderItems['piece'][$key];
			            $order_approx_wt+=$orderItems['weight'][$key];
			            $orderDetails = array(
			 				// 'orderno'			=> $order_no."-".$i,
			 				'ortertype'		    => $addData['order_type'],
			 				'id_product'		=> $orderItems['id_product'][$key],
			 				'design_no'		    => (isset($orderItems['id_design'][$key]) ? $orderItems['id_design'][$key]:NULL),
			 				'id_sub_design'		=> (isset($orderItems['id_sub_design'][$key]) ? $orderItems['id_sub_design'][$key]:NULL),
			 				'pure_wt'		    => (isset($orderItems['purewt'][$key]) ? $orderItems['purewt'][$key]:0),
							'less_wt'			=> (isset($orderItems['less_wt'][$key]) ? $orderItems['less_wt'][$key]:0),
				 			'net_wt'			=> (isset($orderItems['net_wt'][$key]) ? $orderItems['net_wt'][$key]:0),
							'tag_id'		    => (isset($orderItems['tag_id'][$key]) ? $orderItems['tag_id'][$key]:NULL),
			 				'id_repair_master'	=> (isset($orderItems['repair'][$key]) ? $orderItems['repair'][$key]:NULL),
			 				'totalitems'		=> $orderItems['piece'][$key],
			 				'weight'			=> $orderItems['weight'][$key],
			 				'description'		=> ($orderItems['description'][$key]!='' ? $orderItems['description'][$key]:NULL),
			 				// 'order_date'		=> $order_datetime,
			 				'current_branch'	=> $order_from,
			 				'cus_due_date'		=> ( $orderItems['cus_due_days'][$key]!='' ? date('Y-m-d', strtotime($orderItems['cus_due_days'][$key])) :NULL),
							'id_employee'       => $this->session->userdata('uid'),
						);
						if($insOrder > 0){
							$orderDetails['id_customerorder'] = $insOrder;
							if($orderItems['id_orderdetails'][$key]!=''){

								$insOrderDet = $this->$model->updateData($orderDetails,'id_orderdetails',$orderItems['id_orderdetails'][$key],'customerorderdetails');
							}else{
								$insOrderDet = $this->$model->insertData($orderDetails,'customerorderdetails');
							}
							// print_r($this->db->last_query());exit;
							$stone_details = json_decode($orderItems['stone_details'][$key],true);

							if($orderItems['id_orderdetails'][$key]!=''){
								$order_stone_delete_id = $this->$model->deleteData("order_item_id",$orderItems['id_orderdetails'][$key],'ret_order_item_stones');
							}
							if(sizeof($stone_details)>0)
							{
								foreach($stone_details as $stone)
								{
									$stone_data=array(
										'order_id'        	=>	$insOrder,
										'order_item_id'   	=>	$orderItems['id_orderdetails'][$key],
										'pieces'        	=>	$stone['stone_pcs'],
										'wt'            	=>	$stone['stone_wt'],
										'stone_id'      	=>	$stone['stone_id'],
										'uom_id'      	    =>	$stone['stone_uom_id'],
										'price'         	=>	$stone['stone_price'],
										'is_apply_in_lwt'	=>	$stone['show_in_lwt'],
										'stone_cal_type' 	=>	$stone['stone_cal_type'],
										'rate_per_gram'  	=>	$stone['stone_rate'],
										);
									$stoneInsert = $this->$model->insertData($stone_data,'ret_order_item_stones');
								}
							}
							if($insOrderDet && $addData['order_type']==4)
							{
							    $this->$model->updateData(array('tag_status'=>8),'tag_id',$orderItems['tag_id'][$key],'ret_taging');
							     $tag_log=array(
                                            'tag_id'	  =>$orderItems['tag_id'][$key],
                                            'date'		  =>$order_datetime,
                                            'status'	  =>8,
                                            'from_branch' =>$addData['order_from'],
                                            'to_branch'	  =>NULL,
                                          //  'updated_on'  =>date("Y-m-d H:i:s"),
                                          //  'updated_by'  =>$this->session->userdata('uid'),
                                            );
											$this->$model->updateData($tag_log,'tag_id',$orderItems['tag_id'][$key],'ret_taging_status_log');
							}
							//Image Upload
							$CusOrderImg_delete_id = $this->$model->deleteData("id_orderdetails",$orderItems['id_orderdetails'][$key],'customer_order_image');
		 					$precious_imgs = "";
							$_FILES = [];
		 					$p_ImgData=[];
		 					$p_ImgData = json_decode($orderItems['order_img'][$key]);
							if(sizeof($p_ImgData) > 0){
								foreach($p_ImgData as $precious){
									$imgFile = $this->base64ToFile($precious->src);
									$_FILES['order_img'][] = $imgFile;
								}
							}
							// print_r($_FILES);exit;
							if(!empty($_FILES)){
								$img_arr = array();
							    $folder =  self::IMG_PATH."orders/";
								if (!is_dir($folder)) {
									mkdir($folder, 0777, TRUE);
								}
								if(isset($_FILES['order_img'])){
									$order_images = "";
									foreach($_FILES['order_img'] as $file_key => $file_val){
										if($file_val['name'])
										{
											// unlink($folder."/".$product['image']);
											$img_name =  $insOrderDet."_". mt_rand(100001,999999).".jpg";
											$path = $folder."/".$img_name;
											$result = $this->upload_img('image',$path,$file_val['tmp_name']);
											
											$file = self::IMG_PATH."orders_img/".$orderItems['id_orderdetails'][$key].".txt";
											$filesData = serialize($_FILES);
											$unserializedData = unserialize($filesData);
											$jsonData = json_encode($unserializedData, JSON_PRETTY_PRINT);

											// print_r($file);exit;
							
											file_put_contents($file, $jsonData);
											if($result){
												$insImageId = $this->$model->insertData(array('id_orderdetails'=>$orderItems['id_orderdetails'][$key],'image'=>$img_name),'customer_order_image');
											}
										}
									}
								}
							}
						}
						$i++;
			        }
			        $this->$model->updateData(array('order_pcs'=> $order_pcs, 'order_approx_wt' => $order_approx_wt),'id_customerorder',$insOrder,'customerorder');
			        //  echo $this->db->last_query();exit;
			    }
			    if($this->db->trans_status()===TRUE)
				{
					$this->db->trans_commit();
					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'Repair Order',

						'operation'     => 'Update',

						'record'        =>  $insOrder,

						'remark'        => 'Repair Order Updated successfully'

						);

			         $this->log_model->log_detail('insert','',$log_data);
					 
					$response_data=array('status'=>TRUE,'message'=>'Order Updated Successfully','id_customerorder'=>$insOrder);
					$this->session->set_flashdata('chit_alert',array('message'=>'Order Update Successfully','class'=>'success','title'=>'Update Order'));
				}else{
					$this->db->trans_rollback();
					$response_data=array('status'=>FALSE,'message'=>'Unable To Proceed Your Request');
					$this->session->set_flashdata('chit_alert',array('message'=>'Unable To Proceed Your Request','class'=>'danger','title'=>'Update Order'));
				}
				echo json_encode($response_data);
			break;

			default:
						$orders = $this->$model->ajax_getRepairOrders($_POST);
					  	$access = $this->admin_settings_model->get_access('admin_ret_order/repair_order/list');
				        $data = array(
				        					'orders' => $orders,
											'access' => $access
				        			 );
						echo json_encode($data);
		}
	}
	function repair_acknowledgement($repair_id)
    {
        $model = "ret_order_model";
        $set_model = "admin_settings_model";

        $data['comp_details'] = $this->$set_model->get_company();
        $data['repair']=$this->$model->repair_detail($repair_id);
        $data['repair_details']=$this->$model->get_repair_details($repair_id);
        // echo "<pre>";print_r($data);exit;
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        $html = $this->load->view('order/repair_order/repair_print', $data,true);
		//	echo $html;exit;
        $dompdf->load_html($html);
        $dompdf->set_paper('A4', "portriat" );
        $dompdf->render();
        while(ob_get_level()) ob_end_clean();
        $dompdf->stream("Receipt.pdf",array('Attachment'=>0));
    }
	function repair_order_status()
	{


		$req_data 		=$this->input->post('req_data');
		$model 			= "ret_order_model";
		foreach($req_data as $order)
		{
			$updData=array(
			"completed_weight"=> $order['completed_weight'],
			"rate"  		 =>  $order['final_amount'],
			"orderstatus" 	 =>  4,
			);
			$this->db->trans_begin();
			$status=$this->$model->updateData($updData,'id_orderdetails',$order['id_orderdetails'],'customerorderdetails');
		}
		if($this->db->trans_status()===TRUE)
		{
    		$this->db->trans_commit();
    		$response_data=array('status'=>true,'msg'=>'Order Status Updated Successfully');
    		$this->session->set_flashdata('chit_alert',array('message'=>'Order Status Updated Successfully','class'=>'success','title'=>'Order Status'));
		}
		else
		{
    		$this->db->trans_rollback();
    		$response_data=array('status'=>false,'msg'=>'Unable to Procees Your Request');
    		$this->session->set_flashdata('chit_alert',array('message'=>'Unable to Procees Your Request','class'=>'danger','title'=>'Order Status'));
		}
	    echo json_encode($response_data);
    }
    function get_repair_damage_master()
    {
        $model = "ret_order_model";
        $data=$this->$model->get_repair_damage_master();
        echo json_encode($data);
    }
      //Cart Items
	   function cart($type="")
	   {
	        $model = "ret_order_model";$SETT_MOD = "admin_settings_model";
			$SETT_MOD = "admin_settings_model";
    		switch($type){
    			case 'list':
    					$data['main_content'] = "order/cart_list" ;
						$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_order/cart/list');

    					$this->load->view('layout/template', $data);
    				break;
    			case 'cart_status':
					$data['main_content'] = "order/cart_status" ;
					$this->load->view('layout/template', $data);
				break;
    			case "order_place":
    			    //echo "<pre>";print_r($_POST);exit;
			        $response_data=array();
					$addData = $_POST;
					$this->db->trans_begin();
					$i=1;
					$row_karigar='';
					$service_id=28;
					$fin_year       = $this->$model->get_FinancialYear();
					// print_r($addData['id_karigar']);exit;
					if($_POST['status']==1)
					{
					    $order_pcs=0;
						$weight_range_value=0;
					    foreach($addData['req_data'] as $d)
					    {
					        $order_pcs+=$d['totalitems'];
							$weight_range_value+=$d['weight_range_value'];
					    }
					    $pur_no = $this->$model->generatePurNo();
						$order = array(
             			    'fin_year_code'     => $fin_year['fin_year_code'],
             				'pur_no'            => $pur_no,
             				'order_status'		=> 10,
             				'order_type'		=> 1,
             				'order_pcs'			=> $order_pcs,
             				'order_approx_wt'	=> number_format(($order_pcs*$weight_range_value),3,'.',''),
             				'order_for'			=> 1,
             				'id_karigar'		=> $addData['id_karigar'],
             				'order_date'		=> date("Y-m-d H:i:s"),
            				'createdon'         => date("Y-m-d H:i:s"),
            				'order_taken_by'    => $this->session->userdata('uid')
            			);
            			$this->db->trans_begin();
                        $insOrder = $this->$model->insertData($order,'customerorder');
						foreach($addData['req_data'] as $d)
						{
						    if($insOrder!='')
							{
							    $smith_due_date        = (!empty($addData['smith_due_dt']) ?date('Y-m-d',strtotime(str_replace("/","-",$addData['smith_due_dt']))):NULL );
								$orderDetails = array(
								'id_customerorder'	=>$insOrder,
								'orderstatus'		=>3,
								'id_weight_range'	=> (!empty($d['id_wt_range']) ? $d['id_wt_range'] :NULL ),
								'id_product'		=> (!empty($d['id_product']) ? $d['id_product'] :NULL ),
								'design_no'			=> (!empty($d['design_no']) ? $d['design_no'] :NULL ),
								'id_sub_design'		=> (!empty($d['id_sub_design']) ? $d['id_sub_design'] :NULL ),
								'totalitems'		=> (!empty($d['totalitems']) ? $d['totalitems'] :NULL ),
								'size'				=> (!empty($d['size']) ? $d['size'] :NULL ),
								'smith_due_date'	=> (!empty($smith_due_date) ? $smith_due_date :NULL ),
								'order_date'		=> date("Y-m-d H:i:s"),
								'id_employee'       => $this->session->userdata('uid'),
								);
								$insOrderdet = $this->$model->insertData($orderDetails,'customerorderdetails');
								if($insOrderdet)
								{
									$updstatus = $this->$model->updateData(array('orderstatus'=>1,'id_orderdetails'=>$insOrderdet),'id_cart_order',$d['id_cart_order'],'order_cart');
								}
								$row_karigar=$addData['id_karigar'];
							}
						}
						if($insOrder)
						{
						    $res_msg='Order Placed Successfully..';
							$response_data=array('status'=>TRUE,'msg'=>$res_msg);

                            // --- Email to Vendor deferred to MD Approval ---
						}
					}
					else
					{
						foreach($addData['req_data'] as $d)
						{
							$updata=$this->$model->updateData(array('orderstatus'=>2,'updated_by'=>$this->session->userdata('uid'),'date_upd'=>date("Y-m-d H:i:s"),
								'reject_reason'=>'Order Rejected by '.$this->session->userdata('uid').''),'id_cart_order',$d['id_cart_order'],'order_cart');
						}
						if($updata)
						{
						    $res_msg='Order Rejected Successfully..';
							$response_data=array('status'=>TRUE,'msg'=>$res_msg,'insIds'=>'');
						}
					}
					if($this->db->trans_status()===TRUE)
					{
						$this->db->trans_commit();
						$this->session->set_flashdata('chit_alert', array('message' => $res_msg,'class' => 'success','title'=>'Order'));
					}
					else
					{
					    //echo $this->db->_error_message();
					    //echo $this->db->last_query();exit;
						$this->db->trans_rollback();
						$this->session->set_flashdata('chit_alert', array('message' => 'Unable to Proceed Your Request..','class' => 'danger','title'=>'Order'));
						$response_data=array('status'=>FALSE,'msg'=>'Unable to Proceed Your Request..');
					}
			        echo json_encode($response_data);
				break;
				case 'order_status':
    				$data=$this->$model->getCartDetails();
    				echo json_encode($data);
    			break;
    			default:
                    $orders = $this->$model->ajax_getCartOrders($_POST);
                    $access = $this->admin_settings_model->get_access('admin_ret_order/cart/list');
                    $data = array(
                    'orders' => $orders,
                    'access' => $access
                    );
                    echo json_encode($data);
    		}
	 }
	   function add_to_cart()
	   {
            $model = "ret_order_model";
            $reqdata   = $this->input->post('req_data');
            $id_branch = $this->input->post('id_branch');
            $this->db->trans_begin();
            foreach($reqdata as $items)
            {
                $data = array(
                'id_branch'  	=> $id_branch,
                'id_product'  	=> $items['product_id'],
                'design_no'  	=> $items['design_id'],
                'id_sub_design' => $items['id_sub_design'],
                'size'  	    => $items['id_size'],
                'id_wt_range'   => $items['id_wt_range'],
                'totalitems'  	=> $items['shortage_pcs'],
                'created_on' 	=> date("Y-m-d H:i:s"),
                'created_by' 	=> $this->session->userdata('uid')
                );
                $insOrder = $this->$model->insertData($data,'order_cart');
            }
        	if($this->db->trans_status()===TRUE)
			{
				$this->db->trans_commit();
				$response_data=array('status'=>TRUE,'message'=>'Item Added to Cart Successfully..');
			}else{
				$this->db->trans_rollback();
				$response_data=array('status'=>FALSE,'message'=>'Unable To Proceed Your Request');
			}
			echo json_encode($response_data);
	   }
	    function karigar_search()
    	{
    		$model = "ret_order_model";
    		$data =$this->$model->karigar_search($_POST['searchTxt']);
    		echo json_encode($data);
    	}
	   //Cart Items
	    function repair_order_acknowladgement($id="")
        {
            $insOrder=$id;
            $model = "ret_order_model";
            $set_model = "admin_settings_model";
            $billmodel="ret_billing_model";
            $data['create_repair_details']=$this->$model->repair_order_acknowladgement($insOrder);
            $data['comp_details'] = $this->$model->getCompanyDetails($data['create_repair_details'][0]['order_from']);
            $data['metal_rate']=$this->$billmodel->get_branchwise_rate($data['create_repair_details']['order_from']);
    	   // echo "<pre>" ;print_r($data);exit;
            $this->load->helper(array('dompdf', 'file'));
            $dompdf = new DOMPDF();
            $html = $this->load->view('order/repair_order/repair_print', $data,true);
            $dompdf->load_html($html);
            $dompdf->set_paper('A4', "portriat" );
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("Receipt.pdf",array('Attachment'=>0));
        }
        function get_metal()
        {
            $model = "ret_order_model";
            $data=$this->$model->get_active_metal();
            echo json_encode($data);
        }
        function get_cus_product()
        {
            $model = "ret_order_model";
            $id_metal   = $this->input->post('id_metal');
            $data=$this->$model->get_cus_product($id_metal);
            echo json_encode($data);
        }
        function get_ActiveProducts()
       {
            $model = "ret_order_model";
            $data=$this->$model->get_ActiveProducts($_POST);
            echo json_encode($data);
       }
       function get_active_design_products()
       {
            $model = "ret_order_model";
            $data=$this->$model->get_active_design_products($_POST);
            echo json_encode($data);
       }
	public function customer_order_acknowladgement($id)
	{
		$set_model = "admin_settings_model";
		$model = "ret_order_model";
		$billmodel="ret_billing_model";
		$data['cus_details'] =$this->$model->get_order_cus_details($id);
	    $data['order']=$this->$model->get_customer_orders($id);
	    $data['comp_details']=$this->$model->getCompanyDetails($data['cus_details']['id_branch']);
	    $data['metal_rate']=$this->$billmodel->get_branchwise_rate($data['cus_details']['id_branch']);
		//echo "<pre>"; print_r($data);exit;
		$this->load->helper(array('dompdf', 'file'));
	        $dompdf = new DOMPDF();
			$html = $this->load->view('order/cus_order_print', $data,true);
			$dompdf->load_html($html);
			$dompdf->set_paper('A4', "portriat" );
			$dompdf->render();
			while(ob_get_level()) ob_end_clean();
			$dompdf->stream("VendorAck.pdf",array('Attachment'=>0));
	}

	function update_repair_order_other_details()
    {
        $req_data 		 = $this->input->post('order_item');
		$model 			 = "ret_order_model";
		$id_orderdetails = $_POST['id_orderdetails'];
		$service_charge = $_POST['service_charge'];
		$this->db->trans_begin();
		$this->$model->deleteData('id_orderdetails',$id_orderdetails,'customer_order_other_details');
		$orderDetails = $this->$model->get_customerorder_details($id_orderdetails);
		foreach($req_data['id_ret_category'] as $key =>$order)
		{
		    $weight+=$req_data['gross_wt'][$key];
		    $metalDetails=array(
		                       'id_orderdetails'=>$id_orderdetails,
		                       'id_ret_category'=>$req_data['id_ret_category'][$key],
		                       'id_purity'      =>$req_data['id_purity'][$key],
		                       'id_product'     =>$req_data['id_product'][$key],
							   'id_design'      =>$req_data['id_design'][$key],
							   'id_sub_design' =>$req_data['id_sub_design'][$key],
		                       'gross_wt'       =>$req_data['gross_wt'][$key],
		                       'less_wt'        =>$req_data['less_wt'][$key],
		                       'net_wt'         =>$req_data['net_wt'][$key],
		                       'wast_percent'   =>$req_data['wast_per'][$key],
		                       'mc_value'       =>$req_data['mc_value'][$key],
		                       'mc_type'        =>$req_data['mc_type'][$key],
							   'item_type'      =>$req_data['item_type'][$key],
							   'id_section'		=>$req_data['id_section'][$key],
							   'pcs'			=>$req_data['pcs'][$key],
		                       );
		    $insId = $this->$model->insertData($metalDetails,'customer_order_other_details');
		    if($req_data['stone_details'][$key])
			{
				$stone_details=json_decode($req_data['stone_details'][$key],true);
				foreach($stone_details as $stone)
				{
					$stone_data=array(
									'id_other_details'      =>$insId,
									'pieces'                =>$stone['stone_pcs'],
									'wt'                    =>$stone['stone_wt'],
									'stone_id'              =>$stone['stone_id'],
									'uom_id'                =>$stone['stone_uom_id'],
									'amount'                =>$stone['stone_price'],
									'rate_per_gram'         =>$stone['stone_rate'],
									'is_apply_in_lwt'       =>$stone['show_in_lwt'],
									'stone_cal_type'        =>$stone['stone_cal_type'],
									);

					// print_r($stone_data);exit;	
					$stoneInsert = $this->$model->insertData($stone_data,'customer_order_stone_details');
				}
			}
		}
		$status=$this->$model->updateData(array("orderstatus"=>4,'rate'=>$service_charge,'completed_weight'=>number_format($weight+$orderDetails['weight'],3,'.','')),'id_orderdetails',$id_orderdetails,'customerorderdetails');
		if($this->db->trans_status()===TRUE)
		{
    		$this->db->trans_commit();
    		$response_data=array('status'=>true,'message'=>'Order Status Updated Successfully');
    		$this->session->set_flashdata('chit_alert',array('message'=>'Order Status Updated Successfully','class'=>'success','title'=>'Order Status'));
		}
		else
		{
    		$this->db->trans_rollback();
    		$response_data=array('status'=>false,'message'=>'Unable to Procees Your Request');
    		$this->session->set_flashdata('chit_alert',array('message'=>'Unable to Procees Your Request','class'=>'danger','title'=>'Order Status'));
		}
	    echo json_encode($response_data);
    }
	function get_metaltype()
	{
		$model = "ret_order_model";
		$id_category = $_POST['id_category'];
		$data = $this->$model->get_metal_for_category($id_category);
		echo json_encode($data);
	}
	function get_cmp_state()
	{
		$model = "ret_order_model";
		$data=$this->$model->get_cmp_state($_POST['id_branch']);
		echo json_encode($data);
	}
	function send_order_cancel_otp()
	{
		$model="ret_order_model";
		//$data           = $this->$model->get_ret_settings('otp_approval_nos');
		$order_id    = $_POST['order_id'];
		$id_branch = $this->$model->get_order($order_id);
		$data = $this->$model->getBrnachOtpRegMobile($id_branch);
		$mobile_num     = array(explode(',',$data));
		$sent_otp='';
		$comp_details=$this->admin_settings_model->get_company();
		// print_r($data);exit;
		foreach($mobile_num[0] as $mobile)
			{
				if($mobile)
				{
					$this->session->unset_userdata("Ordercancel_otp");
					$OTP = mt_rand(1001,9999);
					$sent_otp.=$OTP.',';
					// print_r($sent_otp);exit;
					$this->session->set_userdata('Ordercancel_otp',$sent_otp);
					$this->session->set_userdata('Ordercancel_otp_exp',time()+300);
					$service = $this->admin_settings_model->get_service_by_code('order_cancel_otp');
					$expiry = 5;
					// print_r($service);exit;
					$otp_gen_time = date("Y-m-d H:i:s");
					$insData=array(
						'mobile'        =>$mobile,
						'otp_code'      =>$OTP,
						'otp_gen_time'  =>date("Y-m-d H:i:s"),
						'module'        =>'Order Cancellation',
						'id_emp'        =>$this->session->userdata('uid')
					);
					$this->db->trans_begin();
					$insId = $this->$model->insertData($insData,'otp');
					// print_r($insId);exit;
					 if($insId)
					 {
						if($service['serv_whatsapp'] == 1)
							{
									// $message="Hi Your OTP For Order Cancel is :".$OTP." Will expire within ".$expiry." minute, REGARDS ".strtoupper($comp_details['company_name']).".";
									// $whatsapp=$this->admin_usersms_model->send_whatsApp_message($mobile,$message);
							}
					 }
				}
			}
			if($insId)
			  {
					  $this->db->trans_commit();
					  $status=array('status'=>true,'msg'=>'OTP sent Successfully','OTP'=>$sent_otp);
			  }
			  else
			  {
					  $this->db->trans_rollback();
					  $status=array('status'=>false,'msg'=>'Unabe To Send Try Again');
			  }
			echo json_encode($status);
	}
	function verify_order_cancel_otp()
	{
			$model                ="ret_order_model";
			$post_otp             =$this->input->post('otp');
			$session_otp          =$this->session->userdata('Ordercancel_otp');
			$otp                  = array(explode(',',$session_otp));
			foreach($otp[0] as $OTP)
			{
					if($OTP==$post_otp)
					{
						if(time() >= $this->session->userdata('Ordercancel_otp_exp'))
						{
							$this->session->unset_userdata('Ordercancel_otp');
							$this->session->unset_userdata('Ordercancel_otp_exp');
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

	function repair_deliver_order_status()
	{


		$req_data 		=$this->input->post('req_data');
		$model 			= "ret_order_model";
		foreach($req_data as $order)
		{
			$updData=array(
				'orderstatus'=>5,
				'delivered_date'=>date("Y-m-d H:i:s")
			);
			$this->db->trans_begin();
			$status=$this->$model->updateData($updData,'id_orderdetails',$order['id_orderdetails'],'customerorderdetails');
		}
		if($this->db->trans_status()===TRUE)
		{
    		$this->db->trans_commit();
    		$response_data=array('status'=>true,'msg'=>'Order Status Updated Successfully');
    		$this->session->set_flashdata('chit_alert',array('message'=>'Order Status Updated Successfully','class'=>'success','title'=>'Order Status'));
		}
		else
		{
    		$this->db->trans_rollback();
    		$response_data=array('status'=>false,'msg'=>'Unable to Procees Your Request');
    		$this->session->set_flashdata('chit_alert',array('message'=>'Unable to Procees Your Request','class'=>'danger','title'=>'Order Status'));
		}
	    echo json_encode($response_data);
    }

	
	public function get_estimation_tags(){

		$model = "ret_order_model";

		$data = $this->$model->get_estimation_tags($_POST['id_branch'],$_POST['estiNo']);

		echo json_encode($data);

	}
}
?>