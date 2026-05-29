<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'libraries/dompdf/autoload.inc.php');
use Dompdf\Dompdf;
class Admin_gift_vocuher extends CI_Controller
{
	
	const CAT_MODEL	= "ret_catalog_model";
	const SETT_MOD	= "admin_settings_model";
	const GIFT_MOD	= "gift_voucher_model";

	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model(self::CAT_MODEL);
		$this->load->model(self::SETT_MOD);
		$this->load->model(self::GIFT_MOD);
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
		//$this->cus_list();
	}
	
	public function getCustomersBySearch(){
		$model=self::GIFT_MOD;
		$data = $this->$model->getAvailableCustomers($_POST['searchTxt']);	  
		echo json_encode($data);
	}
	
	public function getEmployeeBySearch(){
		$model=self::GIFT_MOD;
		$data = $this->$model->getAvailableEmployees($_POST['searchTxt']);	  
		echo json_encode($data);
	}
	
	//Gift Vouucher Settings Start
	
    public function gift_voucher_settings($type="", $id="")
	{
		$model=self::GIFT_MOD;
		switch($type)
		{
			case 'add':
			$data['gift']=$this->$model->get_empty_record();
			$data['main_content'] = "gift_voucher/settings/form" ;
			$this->load->view('layout/template', $data);
			break;

			case 'list':
				$data['main_content'] = "gift_voucher/settings/list" ;
				$this->load->view('layout/template', $data);
			break;
			
			case 'edit':
			    $data['gift']=$this->$model->get_gift_settings($id);
			    $data['gift']['products']=$this->$model->CheckProductAvailability($id);
			    //echo "<pre>"; print_r($data);exit;
			    $data['main_content'] = "gift_voucher/settings/form" ;
			    $this->load->view('layout/template', $data);
			 break;


			case 'save':
			    $addData=$_POST['gift'];
			    //echo "<pre>"; print_r($_POST);exit;
			    $issue_prods = (isset($_POST['issue_pro']) ?$_POST['issue_pro']:'');
			    $utilize_prods = (isset($_POST['utilized_pro']) ?$_POST['utilized_pro']:'');
			    if($addData['id_branch']!='')
			    {
			        $this->$model->update_gift_settings($addData['id_branch']);
			    }
			    
			    $insData=array(
			                        'id_branch'     =>($addData['id_branch']!='' ? $addData['id_branch']:NULL),
			                        'gift_type'     =>$addData['gift_type'],
			                        'calc_type'     =>$addData['calc_type'],
			                        'sale_value'    =>$addData['sale_value'],
			                        'credit_value'  =>$addData['credit_value'],
			                        'validity_days' =>$addData['Validity'],
			                        'utilize_for'   =>$addData['utilized_for'],
			                        'metal'         =>$addData['metal'],
			                        'note'          =>$addData['description'],
			                        'status'        =>1,
			                        'is_default'    =>1,
			                        'created_by' 	=>$this->session->userdata('uid'),
					                'date_add' 	=>date("Y-m-d H:i:s"),
			                      );
			    $this->db->trans_begin();
			    $insId = $this->$model->insertData($insData,'ret_bill_gift_voucher_settings');
			    
			    if($insId)
			    {
			        if(sizeof($issue_prods)>0)
			        {
			            foreach($issue_prods as $pro_id)
			            {
			                   $arrayInsData[]=array('id_gift_voucher'=>$insId,'id_product'=>$pro_id,'issue'=>1);
			            }
			            if(!empty($arrayInsData))
                        {
                            $Insert = $this->$model->insertBatchData($arrayInsData,'ret_gift_issue_redeem_prod');
                        }
			        }
			        if(sizeof($utilize_prods)>0)
			        {
			            foreach($utilize_prods as $pro_id)
			            {
			                $status=$this->$model->get_prod_settings($insId,$pro_id);
			                if($status)
			                {
			                   $this->$model->updateData(array('utilize'=>1),'id_gift_voucher',$insId,'ret_gift_issue_redeem_prod');
			                }else{
			                    $this->$model->insertData(array('id_gift_voucher'=>$insId,'id_product'=>$pro_id,'utilize'=>1),'ret_gift_issue_redeem_prod');
			                }
			            }
			        }
			    }
				if($this->db->trans_status()===TRUE)
					{
						$this->db->trans_commit();
						$this->session->set_flashdata('chit_alert',array('message'=>'Settings Added  successfully','class'=>'success','title'=>'Gift Settings'));
					}
					else
					{
						$this->db->trans_rollback();
						echo $this->db->_error_message()."<br/>";					   
						echo $this->db->last_query();exit;	
						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Gift Settings'));
					}
				redirect('admin_gift_vocuher/gift_voucher_settings/list');	
			break;
			
			case 'update':
			    $addData=$_POST['gift'];
			    $issue_prods = (isset($_POST['issue_pro']) ?$_POST['issue_pro']:'');
			    $utilize_prods = (isset($_POST['utilized_pro']) ?$_POST['utilized_pro']:'');
			    $this->db->trans_begin();
			    $insData=array(
			                        'id_branch'     =>($addData['id_branch']!='' ? $addData['id_branch']:NULL),
			                        'gift_type'     =>$addData['gift_type'],
			                        'sale_value'    =>$addData['sale_value'],
			                        'credit_value'  =>$addData['credit_value'],
			                        'validity_days' =>$addData['Validity'],
			                        'utilize_for'   =>$addData['utilized_for'],
			                        'metal'         =>$addData['metal'],
			                        'note'          =>$addData['description'],
			                        'status'        =>1,
			                        'updated_by' 	=>$this->session->userdata('uid'),
					                'date_upd' 	    =>date("Y-m-d H:i:s"),
			                      );
			    $insId = $this->$model->insertData($insData,'ret_bill_gift_voucher_settings');
			    if($insId)
			    {
			        if(sizeof($issue_prods)>0)
			        {
			            foreach($issue_prods as $pro_id)
			            {
			                   $arrayInsData[]=array('id_gift_voucher'=>$insId,'id_product'=>$pro_id,'issue'=>1);
			            }
			            if(!empty($arrayInsData))
                        {
                            $Insert = $this->$model->insertBatchData($arrayInsData,'ret_gift_issue_redeem_prod');
                        }
			        }
			        if(sizeof($issue_prods)>0)
			        {
			            foreach($utilize_prods as $pro_id)
			            {
			                $status=$this->$model->get_prod_settings($insId);
			                if($status)
			                {
			                   $this->$model->updateData(array('utilize'=>1),'id_gift_voucher',$insId,'ret_gift_issue_redeem_prod');
			                }else{
			                    $this->$model->insertData(array('id_gift_voucher'=>$insId,'id_product'=>$pro_id,'utilize'=>1),'ret_bill_gift_voucher_settings');
			                }
			            }
			        }
			    }
			    //$insId = $this->$model->updateData($insData,'id_set_gift_voucher',$id,'ret_bill_gift_voucher_settings');
				if($this->db->trans_status()===TRUE)
					{
						$this->db->trans_commit();
						$this->session->set_flashdata('chit_alert',array('message'=>'Settings Updated  successfully','class'=>'success','title'=>'Gift Settings'));
					}
					else
					{
						$this->db->trans_rollback();
						echo $this->db->_error_message()."<br/>";					   
						echo $this->db->last_query();exit;	
						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Gift Settings'));
					}
				redirect('admin_gift_vocuher/gift_voucher_settings/list');	
			break;
			case 'delete':
						 $this->db->trans_begin();
						 $this->$model->deleteData('id_set_gift_voucher',$id,'ret_bill_gift_voucher_settings');
				           if( $this->db->trans_status()===TRUE)
						    {
						    	  $this->db->trans_commit();
								  $this->session->set_flashdata('chit_alert', array('message' => 'Gift Deleted successfully','class' => 'success','title'=>'Delete Gift Vocher'));	  
							}			  
						   else
						   {
							 $this->db->trans_rollback();
							 $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Delete Gift Vocher'));
						   }
						 redirect('admin_gift_vocuher/gift_voucher_settings/list');	
			break;
			default: 
					  	$list = $this->$model->ajax_gift_settings();	 
					  	$access = $this->admin_settings_model->get_access('admin_gift_vocuher/gift_voucher_settings/list');
				        $data = array(
				        					'list'  => $list,
											'access'=> $access
				        				);  
						echo json_encode($data);	
		}
	}
	
	function update_gift_settings_status($status,$id)
	{
		$model=self::GIFT_MOD;
		$this->db->trans_begin();
        $data = array(  
        'status'    =>$status,
        'date_upd'  => date("Y-m-d H:i:s"),
        'updated_by'=> $this->session->userdata('uid')
        );
        $this->$model->updateData($data,'id_set_gift_voucher',$id,'ret_bill_gift_voucher_settings');
        
		if($this->db->trans_status()===TRUE)
         {
		 	$this->db->trans_commit();
			$this->session->set_flashdata('chit_alert',array('message'=>'Status updated successfully.','class'=>'success','title'=>'Gift Voucher'));			
		}	
		else
		{
			$this->db->trans_rollback();
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Gift Voucher'));
		}	
	    redirect('admin_gift_vocuher/gift_voucher_settings/list');			
	}
	
	function UpdateGiftSettings($status,$id,$id_branch)
	{
		$model=self::GIFT_MOD;
		$this->db->trans_begin();
		$this->$model->update_gift_settings($id_branch);
        $data = array(  
        'is_default'    =>$status,
        'date_upd'  => date("Y-m-d H:i:s"),
        'updated_by'=> $this->session->userdata('uid')
        );
        $this->$model->updateData($data,'id_set_gift_voucher',$id,'ret_bill_gift_voucher_settings');
        
		if($this->db->trans_status()===TRUE)
         {
		 	$this->db->trans_commit();
			$this->session->set_flashdata('chit_alert',array('message'=>'Status updated successfully.','class'=>'success','title'=>'Gift Voucher'));			
		}	
		else
		{
			$this->db->trans_rollback();
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Gift Voucher'));
		}	
	    redirect('admin_gift_vocuher/gift_voucher_settings/list');			
	}
	
	function getActiveMetal()
	{
	    $model=self::GIFT_MOD;
	    $data=$this->$model->getActiveMetal();
	    echo json_encode($data);
	}
	
    //Gift Vouucher Settings End
		
    
	
	function get_gift_voucher()
	{
	    $model=self::GIFT_MOD;
	    $id_branch=$_POST['id_branch'];
	    $return_data=$this->$model->get_gift_voucher('');
/*	    $return_data=array();
	    foreach($gifts as $items)
	    {
	        
	        if($id_branch!='' && $id_branch>0)
	        {
	            $branches=explode(',',$items['id_branch']);
	            if($branches==NULL)
	            {
	                    $current_date   = strtotime(date("Y-m-d"));
                        $from_date      = strtotime($items['valid_from']); 
                        $to_date        = strtotime($items['valid_to']);
                        if($from_date<=$current_date)
                        {
                            if($from_date<=$to_date)
                            {
                                $return_data[]=array(
                                'id_gift_voucher'=>$items['id_gift_voucher'],
                                'name'           =>$items['name'],
                                );
                            }
                        }
	            }
	            foreach($branches as $branch)
	            {
	               if($branch==$id_branch)
	               {
	                   $current_date   = strtotime(date("Y-m-d"));
	                
                        $from_date      = strtotime($items['valid_from']); 
                        $to_date        = strtotime($items['valid_to']);
                        
                        if($from_date<=$current_date)
                        {
                            if($from_date<=$to_date)
                            {
                                $return_data[]=array(
                                'id_gift_voucher'=>$items['id_gift_voucher'],
                                'name'           =>$items['name'],
                                );
                            }
                        }
	               }
	            }
	        }else{
	                $current_date   = strtotime(date("Y-m-d"));
	                
                    $from_date      = strtotime($items['valid_from']); 
                    $to_date        = strtotime($items['valid_to']);
                    
                    if($from_date<=$current_date)
                    {
                        if($from_date<=$to_date)
                        {
                            $return_data[]=array(
                            'id_gift_voucher'=>$items['id_gift_voucher'],
                            'name'           =>$items['name'],
                            );
                        }
                    }
                    
                    
	        }
	        
	    }*/
	    echo json_encode($return_data);	
	}
	
	
	public function gift_master($type="",$id=""){
		$model=self::GIFT_MOD;
		switch($type)
		{
		    case "add":
		        $data['main_content'] = "gift_voucher/gift_master" ;
    			$this->load->view('layout/template', $data);
		    break;
			case "save":
			    
	 			$addData=$_POST['gift'];
	 			$utilize_prods = (isset($_POST['utilized_pro']) ?$_POST['utilized_pro']:'');
			    $insData=array(
			                        'name'          =>$addData['name'],
			                        'voucher_type'  =>$addData['voucher_type'],
			                        'sale_value'    =>$addData['sale_value'],
			                        'credit_value'  =>$addData['credit_value'],
			                        'credit_type'  =>$addData['credit_type'],
			                        'validity_days' =>$addData['Validity'],
			                        'utilize_for'   =>$addData['utilize_for'],
			                        'description'   =>$addData['description'],
			                        'status'        =>1,
			                        'created_by' 	=>$this->session->userdata('uid'),
					                'created_on' 	=>date("Y-m-d H:i:s"),
			                      );
			    $this->db->trans_begin();
			    $insId = $this->$model->insertData($insData,'ret_gift_voucher_master');
			    if($insId)
			    {
			        if(sizeof($utilize_prods)>0)
			        {
			            foreach($utilize_prods as $pro_id)
			            {
			                $this->$model->insertData(array('id_gift_voucher'=>$insId,'id_product'=>$pro_id,'utilize'=>1),'ret_gift_master_redeem_prod');
			            }
			        }
			    }
				if($this->db->trans_status()===TRUE)
					{
						$this->db->trans_commit();
						$this->session->set_flashdata('chit_alert',array('message'=>'Settings Added  successfully','class'=>'success','title'=>'Gift Settings'));
					}
					else
					{
						$this->db->trans_rollback();
						echo $this->db->_error_message()."<br/>";					   
						echo $this->db->last_query();exit;	
						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Gift Settings'));
					}
				redirect('admin_gift_vocuher/gift_master/list');	
					
	 		break;
	 	case "edit":
	 	    
	 			$data['gift'] = $this->$model->get_gift_voucher($id);
	 			$id_branch=explode(',',$data['gift']['id_branch']);
				$data['gift']['id_branch']=(json_encode($id_branch));
				$data['main_content'] = "gift_voucher/gift_master";
				
				// echo "<pre>"; print_r($data);exit;
				
				$this->load->view('layout/template', $data);
	 	break; 
	 	
	 	case 'update':
			    $addData=$_POST['gift'];
			    //echo "<pre>"; print_r($addData);exit;
			    $this->db->trans_begin();
			    $insData=array(
			                        'name'          =>$addData['name'],
			                        'voucher_type'  =>$addData['voucher_type'],
			                        'sale_value'    =>$addData['sale_value'],
			                        'credit_value'  =>$addData['credit_value'],
			                        'credit_type'  =>$addData['credit_type'],
			                        'validity_days' =>$addData['Validity'],
			                        'utilize_for'   =>$addData['utilize_for'],
			                        'description'   =>$addData['description'],
			                        'updated_by' 	=>$this->session->userdata('uid'),
					                'updated_on' 	=>date("Y-m-d H:i:s"),
			                      );
			    $insId = $this->$model->updateData($insData,'id_gift_voucher',$id,'ret_gift_voucher_master');
				if($this->db->trans_status()===TRUE)
					{
						$this->db->trans_commit();
						$this->session->set_flashdata('chit_alert',array('message'=>'Gift Updated  successfully','class'=>'success','title'=>'Gift'));
					}
					else
					{
						$this->db->trans_rollback();
						echo $this->db->_error_message()."<br/>";					   
						echo $this->db->last_query();exit;	
						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Gift'));
					}
				redirect('admin_gift_vocuher/gift_master/list');	
		break;
	 	
	 	case 'Delete':
						 $this->db->trans_begin();
						 $this->$model->deleteData('id_gift_voucher',$id,'ret_gift_voucher_master');
				           if( $this->db->trans_status()===TRUE)
						    {
						    	  $this->db->trans_commit();
								  $this->session->set_flashdata('chit_alert', array('message' => 'Gift Deleted successfully','class' => 'success','title'=>'Delete Gift Vocher'));	  
							}			  
						   else
						   {
							 $this->db->trans_rollback();
							 $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Delete Gift Vocher'));
						   }
						 redirect('admin_gift_vocuher/gift_master/list');	
				break;
					
	 	
	 		
			case 'list':
						$data['main_content'] = "gift_voucher/list" ;
						$this->load->view('layout/template', $data);
					break;
							
			default:
						$SETT_MOD = self::SETT_MOD;
					  	$gift_det = $this->$model->ajax_get_gift_master();	 
					  	$access = $this->$SETT_MOD->get_access('admin_gift_vocuher/gift_master/list');
				        $data = array(
				        					'gift_det' =>$gift_det,
											'access'=>$access
				        				);  
						echo json_encode($data);
		}
	}
	
	function update_gift_status($status,$id)
	{
		$model=self::GIFT_MOD;
		$this->db->trans_begin();
		$data = array(  
		               'status'         => $status,
					   'updated_on'     => date("Y-m-d H:i:s"),
					   'updated_by'     => $this->session->userdata('uid')
					);
		$status = $this->$model->updateData($data,'id_gift_voucher',$id,'ret_gift_voucher_master');
		//print_r($this->db->last_query());exit;
		if($this->db->trans_status()===TRUE)
         {
		 	$this->db->trans_commit();
			$this->session->set_flashdata('chit_alert',array('message'=>'Gift Voucher updated successfully.','class'=>'success','title'=>'Gift Voucher'));			
		}	
		else
		{
			$this->db->trans_rollback();
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Gift Voucher'));
		}	
	    redirect('admin_gift_vocuher/gift_master/list');			
	}
	
	
	
	//Gift issue
	public function gift_issue($type="", $id="", $billno="")
	{
		$model=self::GIFT_MOD;
		switch($type)
		{
			case 'add':
			//$data['settings']		= $this->$model->get_retSettings();
			$data['main_content'] = "gift_voucher/gift_issue" ;
			$this->load->view('layout/template', $data);
			break;

		


			case 'save':
			    //echo "<pre>"; print_r($_POST);exit;
			    $card_payment=0;
			    
			    $net_payment=0;
			    
				$addData=$_POST['gift'];
				
				$payment=$_POST['payment'];
				
				$card_pay_details	= json_decode($payment['card_pay'],true);
				
				$cheque_details	    = json_decode($payment['chq_pay'],true); 
				
				$net_banking_details = json_decode($payment['net_bank_pay'],true);
				
				$dCData = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);
				
				$bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
				
				$this->db->trans_begin();

			    //$gifts=($addData['gift_type']==3 ? $this->$model->gift_voucher():'');
			    
			    $gifts=($addData['gift_type']==3 ? $this->$model->gift_voucher($addData['id_gift_card']):'');
			    
			    $ref_no='';
			    
			   
			    
			    if($addData['gift_type']==1 || $addData['gift_type']==2)
			    {
			        
			        $code=($addData['gift_for']==2 && ($addData['gift_type']==1 || $addData['gift_type']==2) ? $this->$model->get_customer($addData['id_customer']):$this->$model->get_employee($addData['id_employee']));
				
				    $gift_code = substr(strtoupper($code), 0, 4).mt_rand(1001,9999);
			        
    			    if(sizeof($card_pay_details)>0)
     				{
     					foreach($card_pay_details as $card_pay)
     					{
    						$card_payment+=$card_pay['card_amt'];
     					}
     	
     				}
     				
     				if(sizeof($net_banking_details)>0)
         			{
         				foreach($net_banking_details as $nb_pay)
         				{
        					$net_payment+=$nb_pay['amount'];
         				}
         			}
    			    
                        $insData=array(
                        'id_branch'     =>$addData['id_branch'],
                        'gift_for'      =>$addData['gift_for'],
                        'free_card'     =>$addData['gift_type'],
                        'code'          =>$gift_code,
                        'purchased_by'  =>($addData['gift_for']==1 ? $addData['id_employee'] :$addData['id_customer']),
                        //'purchase_to'   =>($addData['purchase_to']!='' ? $addData['purchase_to']:NULL),
                        'status'        =>0,
                        'id_gift_voucher'       =>($addData['gift_type']!=2 ? $addData['id_gift_card']:null),
                        'emp_created' 	=>$this->session->userdata('uid'),
                        'date_add' 	    =>$bill_date,
                        'valid_from'    =>date("Y-m-d"),
                        'valid_to'      =>($addData['gift_type']==3 ? date("Y-m-d", strtotime($gifts['validity_days'].'days')):null),
                        'amount'        =>($addData['gift_type']==2 ?$payment['cash_payment']+$card_payment+$net_payment : ($addData['gift_type'] ==3 ? $gifts['amount'] :$addData['vocuher_amount'] )),
                        'type'          =>2, //1-Online,2-Manual
                        );
    			         $insId = $this->$model->insertData($insData,'gift_card');
    			         
    			    if($addData['gift_type']==2)
    			    {
    			        if($insId)
    			        {
    			            if(($payment['cash_payment'])>0)
        		 			{
        		 				$pay_data=array(
        		 					'id_gift_card'	=>$insId,
        		 					'amount'        =>$payment['cash_payment'],
        		 					'payment_mode'	=>'Cash',
        		 					'payment_status'=>1,
        		 					'type'			=>1,
        		 					'payment_type'	=>'Manual',
        							'date_add'	    => date("Y-m-d H:i:s"),
        							'added_by'	    => $this->session->userdata('uid')
        		 				);
        		 				$this->$model->insertData($pay_data,'gift_card_payment');
        		 				//print_r($this->db->last_query());exit;
        		 				
        		 			}
        		 			
        		 			if(sizeof($net_banking_details)>0)
                 			{
                 				foreach($net_banking_details as $nb_pay)
                 				{
                					$net_bank_data=array(
        		 					'id_gift_card'	=>$insId,
        		 					'amount'        =>$nb_pay['amount'],
        		 					'payment_mode'	=>'NB',
        		 					'payment_status'=>1,
        		 					'type'			=>1,
        		 					'payment_type'	=>'Manual',
        							'date_add'	    => date("Y-m-d H:i:s"),
        							'added_by'	    => $this->session->userdata('uid')
        		 				    );
        		 				    $this->$model->insertData($net_bank_data,'gift_card_payment');
        		 				    //print_r($this->db->last_query());exit;
                 				}
                 			}
                 			
                 			if(sizeof($card_pay_details)>0)
             				{
             					foreach($card_pay_details as $card_pay)
             					{
            					    $card_pay_det=array(
        		 					'id_gift_card'	=>$insId,
        		 					'amount'        =>$card_pay['card_amt'],
        		 					'payment_mode'	=>($card_pay['card_type']==1 ?'CC':'DC'),
        		 					'payment_status'=>1,
        		 					'type'			=>1,
        		 					'payment_type'	=>'Manual',
        							'date_add'	    => date("Y-m-d H:i:s"),
        							'added_by'	    => $this->session->userdata('uid')
        		 				    );
        		 				    $this->$model->insertData($card_pay_det,'gift_card_payment');
        		 				    //print_r($this->db->last_query());exit;
             					}
             	
             				}
    			        }
    			    }
			    
		        }else if($addData['gift_type']==3)
		        {
		          //  print_r($addData);exit;
		            $no_of_receipts= $addData['no_of_receipts'];
		            
		            $credit_type = $this->gift_voucher_model->getGiftCreditType($addData['id_gift_card']);
		            
		            $ref_no=time();
		            for($i=1;$i<=$no_of_receipts;$i++)
		            {
                        $code = mt_rand(100001,999999);
		                $insData[]=array(
		                        'id_branch'     =>$addData['id_branch'],
		                        'gift_for'      =>$addData['gift_for'],
		                        'free_card'     =>$addData['gift_type'],
		                        'code'          =>$code,
		                        'status'        =>0,
		                        'ref_no'        =>$ref_no,
		                        'credit_type'   =>$credit_type,
		                        'amount'        =>($credit_type == 1 ? $gifts['credit_value'] : 0),
		                        'percentage'    =>($credit_type == 2 ? $gifts['credit_value'] : 0),
		                        'id_gift_voucher' =>$addData['id_gift_card'],
		                        'emp_created' 	=>$this->session->userdata('uid'),
				                'date_add' 	    =>$bill_date,
		                        'valid_from'    =>date("Y-m-d"),
		                        'valid_to'      =>date("Y-m-d", strtotime($gifts['validity_days'].'days')),
		                        'type'          =>2, //1-Online,2-Manual
		                      );
		                
		               
		            }
		            //echo"<pre>"; print_r($insData);exit;
		            $insId = $this->$model->insertBatchData($insData,'gift_card');
		        }
		
				if($this->db->trans_status()===TRUE)
					{
						$this->db->trans_commit();
						$return_data=array('status'=>TRUE,'message'=>'Gift Issued  successfully','id'=>$insId,'gift_type'=>$addData['gift_type'],'insIds'=>$ref_no);
						$this->session->set_flashdata('chit_alert',array('message'=>'Gift Issued  successfully','class'=>'success','title'=>'Add Gift Issued'));
					}
					else
					{
						$this->db->trans_rollback();
						$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
						print_r($this->db->last_query());exit;
						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add Gift Issued'));
					}
					echo json_encode($return_data);
			break;
			case 'receipt_print':
					$data['comp_details']=$this->$model->getCompanyDetails(1);
					$data['issue']=$this->$model->get_receipt_details($id);
					$data['payment'] = $this->$model->get_receipt_payment($id);
					$data['metal_rate']=$this->$model->get_branchwise_rate($data['issue']['id_branch']);
					
				//	echo "<pre>"; print_r($data);exit;
					$this->load->helper(array('dompdf', 'file'));
					$dompdf = new DOMPDF();
					$html = $this->load->view('gift_voucher/issue_print', $data,true);
					$dompdf->load_html($html);
					$dompdf->set_paper("a4", "portriat" );
					$dompdf->render();
					while(ob_get_level()) ob_end_clean();
					$dompdf->stream("Receipt.pdf",array('Attachment'=>0));
			break;
			default: 
					  	$list = $this->$model->ajax_getReceiptlist();	 
					  	$access = $this->admin_settings_model->get_access('admin_ret_billing/receipt/list');
				        $data = array(
				        					'list'  => $list,
											'access'=> $access
				        				);  
						echo json_encode($data);	
		}
	}
	
	function get_issue_receipt()
	{
	    $model=self::GIFT_MOD;
	    $data['ref_no']=$_GET['id_gift'];
	    $data['type']=3;
	    $data['comp_details']=$this->$model->getCompanyDetails(1);
	    $this->load->helper(array('dompdf', 'file'));
		$dompdf = new DOMPDF();
		$html = $this->load->view('gift_voucher/issue_print', $data,true);
		$dompdf->load_html($html);
		$dompdf->set_paper("a4", "portriat" );
		$dompdf->render();
		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("Receipt.pdf",array('Attachment'=>0));
	}
	//Gift issue
	
	
	//Active Product
	
	function get_Activeproduct()
	{
	    $model=self::GIFT_MOD;
	    $data=$this->$model->get_Activeproduct();
	    echo json_encode($data);
	}
	
	//Active Product
}	
?>