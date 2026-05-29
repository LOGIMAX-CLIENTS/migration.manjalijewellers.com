<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'libraries/dompdf/autoload.inc.php');
use Dompdf\Dompdf;
class Admin_ret_metal_process extends CI_Controller {
	const VIEW_FOLDER = 'ret_metal_process/';
	const RET_PROCESS_MODEL = 'ret_metal_process_model'; 
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model(self::RET_PROCESS_MODEL); 
		$this->load->model("admin_settings_model"); 
		$this->load->model("log_model");
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

    function get_old_metal_type()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_old_metal_type();
        echo json_encode($data);
    }
    
    
    function metal_process($type="",$id="")
	{   
       
		$model=	self::RET_PROCESS_MODEL;
		switch($type)
		{
			case 'list': 
					$data['main_content'] = self::VIEW_FOLDER.'process_master/list';
        			$this->load->view('layout/template', $data);
			break;
            case 'add': 
                $data['main_content'] = self::VIEW_FOLDER.'process_master/form';
                $this->load->view('layout/template', $data);
            break;
            case 'edit': 
                $data['process']=$this->$model->getMetalProcess($id);
                $data['main_content'] = self::VIEW_FOLDER.'process_master/form';
        		$this->load->view('layout/template', $data);
            break;
			case 'save':
				$addData=$_POST['process'];
				$insData=array(
					'process_name'	=>strtoupper($addData['process_name']),
					'has_charge'    =>$addData['has_charge'],
					'charge_type'   =>$addData['charge_type'],
					'created_by'    =>$this->session->userdata('uid'),
					'created_on'    =>date("Y-m-d H:i:s"),
				);
				$this->db->trans_begin();
				$insId = $this->$model->insertData($insData,'ret_old_metal_process_master');
				if($insId)
				{
				    if($this->db->trans_status()===TRUE)
    				{
    					$this->db->trans_commit();
                        $log_data = array(

                            'id_log'        => $this->session->userdata('id_log'),
    
                            'event_date'    => date("Y-m-d H:i:s"),
    
                            'module'        => 'Metal Process',
    
                            'operation'     => 'Add',
    
                            'record'        =>  $insId,
    
                            'remark'        => 'Metal Process added successfully'
    
                            );
    
                            $this->log_model->log_detail('insert','',$log_data);
                            
                            
    					$this->session->set_flashdata('chit_alert',array('message'=>'Process Created successfully','class'=>'success','title'=>'Add Process'));
    				}
    				else
    				{
    					$this->db->trans_rollback();
    					 $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Add Process'));
    				}
				}
				
			redirect('admin_ret_metal_process/metal_process/list');	
			break;
			
			case 'update':
				$addData=$_POST['process'];
				$insData=array(
					'process_name'	=>strtoupper($addData['process_name']),
					'has_charge'    =>$addData['has_charge'],
					'charge_type'   =>$addData['charge_type'],
					'updated_by'    =>$this->session->userdata('uid'),
					'updated_on'    =>date("Y-m-d H:i:s"),
				);
				$this->db->trans_begin();
				$status = $this->$model->updateData($insData,'id_metal_process',$id,'ret_old_metal_process_master');
				if($status)
				{
				    if($this->db->trans_status()===TRUE)
    				{
    					$this->db->trans_commit();
                       
                        $log_data = array(

                            'id_log'        => $this->session->userdata('id_log'),

                            'event_date'    => date("Y-m-d H:i:s"),

                            'module'        => 'Metal Process',

                            'operation'     => 'Update',

                            'record'        =>  $status,

                            'remark'        => 'Metal Process Updated successfully'

                            );

                           $this->log_model->log_detail('insert','',$log_data);
				 
    					$this->session->set_flashdata('chit_alert',array('message'=>'Process Updated successfully','class'=>'success','title'=>'Add Process'));
    				}
    				else
    				{
    					$this->db->trans_rollback();
    					 $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Add Process'));
    				}
				}
				
			redirect('admin_ret_metal_process/metal_process/list');	
			break;
			
			case 'ajax': 
					$list=$this->$model->ajax_getMetalProcess($_POST); 
				  	$access = $this->admin_settings_model->get_access('admin_ret_metal_process/metal_process/list');
			        $data = array(
		        					'list'  => $list,
									'access'=> $access
		        				 );  
					echo json_encode($data);
				break;
		}
	    
    }
    
    
    function get_ActiveMetalProcess()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data = $this->$model->ajax_getMetalProcess();
        echo json_encode($data);
    }

    function get_melting_issue_details()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data = $this->$model->get_melting_issue_process_details();
        echo json_encode($data);
    }


    function metal_pocket($type="")
	{   
       
		$model=	self::RET_PROCESS_MODEL;
        $SETT_MOD = "admin_settings_model";
		switch($type)
		{
			case 'list': 
					$data['main_content'] = self::VIEW_FOLDER.'pocket/list';

                    $data['access'] = $this->$SETT_MOD ->get_access('admin_ret_metal_process/metal_pocket/list');

        			$this->load->view('layout/template', $data);
			break;
            case 'add': 
                $data['main_content'] = self::VIEW_FOLDER.'pocket/form';
                $this->load->view('layout/template', $data);
            break;
            case 'metal_list': 
                $data=$this->$model->get_metal_stock_list($_POST);
                echo json_encode($data);
            break;
			case 'save':
                $total_pcs = floatval($this->input->post('total_pcs'));
                $total_gross_wt = floatval($this->input->post('total_gross_wt'));
                if ($total_pcs <= 0 || $total_gross_wt <= 0) {
                    echo json_encode(array('status' => FALSE, 'message' => 'Total Pcs and Gross Weight must be greater than 0.'));
                    return;
                }
			    $branchDetails=$this->$model->get_branch_details();
				$addData=$_POST;
				$pocket_no = $this->$model->code_number_generator();   //Bill Number Generate 
				$insData=array(
					'date' 		 => $branchDetails['entry_date'],
					'id_branch'   => $addData['id_branch'],
					'trans_type' => $addData['trans_type'],
                    'is_against_opening' => ($addData['trans_type']==1 ? $addData['is_against_opening'] :0),
					'pocket_no'  => $pocket_no,
					'piece'	    =>$addData['total_pcs'],
					'gross_wt'	=>$addData['total_gross_wt'],
					'net_wt'	=>$addData['total_net_wt'],
                    'dia_wt'    =>$addData['total_dia_wt'],
					'avg_purity'=>$addData['avg_purity_per'],
					'total_purity'=>$addData['total_item_purity'],
					'amount'    =>$addData['total_amount'],
					'created_by'=>$this->session->userdata('uid'),
					'created_on' => date("Y-m-d H:i:s"),
				);
				$this->db->trans_begin();
				$insId = $this->$model->insertData($insData,'ret_old_metal_pocket');
				
				foreach($addData['req_data'] as $val)
				{
				    if($addData['trans_type']==1) //Old Metal
				    {
    					$metal_details=array(
    						'id_metal_pocket'	=>$insId,
    						'id_metal_type'		=>(isset($val['id_old_metal_type']) ? $val['id_old_metal_type']:NULL),
    						'old_metal_sale_id' =>(isset($val['old_metal_sale_id']) ? $val['old_metal_sale_id']:NULL),
                            'id_category'       =>(isset($val['cat_id'])?$val['cat_id']:NULL),
    						'tag_id'            =>(isset($val['tag_id']) ? $val['tag_id']:NULL),
    						'type'			    =>$val['pocket_type'],//1-Old Metal,2-Sales Return
    						'piece'			    =>$val['piece'],
    						'piece'			    =>$val['piece'],
    						'gross_wt'			=>$val['gross_wt'],
    						'net_wt'			=>$val['net_wt'],
    						'item_cost'			=>$val['item_cost'],
    						'purity'			=>$val['touch'],
    						'diawt'			    =>$val['diawt'],
                            'rate_per_gram'     =>$val['rate_per_gram'],
    					);
    					$status=$this->$model->insertData($metal_details,'ret_old_metal_pocket_details');
    					//print_r($this->db->last_query());exit;
    					if($status)
    					{
    					    if($val['pocket_type']==1)
    					    {
    					        $this->$model->updateData(array('is_pocketed'=>1),'old_metal_sale_id',$val['old_metal_sale_id'], 'ret_bill_old_metal_sale_details');
    					    }
    					    else if($val['pocket_type']==2 || $val['pocket_type']==3)
    					    {
    					        $this->$model->updateData(array('is_pocketed'=>1),'tag_id',$val['tag_id'],'ret_taging');
    					    }
    					}
				    }
				    else if($addData['trans_type']==2) 
				    {
				        $metal_details=array(
    						'id_metal_pocket'	=>$insId,
    						'tag_id'            =>(isset($val['tag_id']) ? $val['tag_id']:NULL),
    						'piece'			    =>$val['piece'],
    						'gross_wt'			=>$val['gross_wt'],
    						'net_wt'			=>$val['net_wgt'],
    						'less_wt'			=>($val['less_wt']!='' ? $val['less_wt']:0),
                            'diawt'             =>($val['dia_wgt']!='' ? $val['dia_wgt']:0),
    						'item_cost'			=>$val['item_cost'],
    						'purity'			=>$val['purity_per'],
    					);
    					$status=$this->$model->insertData($metal_details,'ret_old_metal_pocket_details');
    					if($status)
    					{
    					    $this->$model->updateData(array('is_pocketed'=>1,'tag_status'=>13),'tag_id',$val['tag_id'],'ret_taging');
    					    
    					    $log_data=array(
	 					    	'tag_id'	  =>$val['tag_id'],
	 					    	'date'		  =>$branchDetails['entry_date'],
	 					    	'status'	  =>15,
	 					    	'from_branch' =>$addData['id_branch'],
	 					    	'to_branch'	  =>NULL,
	 					    	'created_on'  =>date("Y-m-d H:i:s"),
								'created_by'  =>$this->session->userdata('uid'),
	 					    );
	 					    $this->$model->insertData($log_data,'ret_taging_status_log');

                             $id_section = $this->$model->get_tag_details($val['tag_id']);
                             if($id_section!='')
                             {
                                 $sect_log = array(
                                "tag_id"                => $val['tag_id'],
                                "status"                => 15,
                                "from_branch"            => $addData['id_branch'],
                                "to_branch"                => NULL,
                                "created_by"            => $this->session->userdata('uid'),
                                "created_on"            => date('Y-m-d H:i:s'),
                                "date"                    => $branchDetails['entry_date'],
                                "from_section"          => $id_section ,
                                "to_section"            => NULL,
                                );
                                $this->$model->insertData($sect_log,'ret_section_tag_status_log');
                             }
    					}
				    }
				    else if($addData['trans_type']==3) 
				    {
				        $metal_details=array(
    						'id_metal_pocket'	=>$insId,
    						'id_product'        =>(isset($val['id_product']) ? $val['id_product']:NULL),
    						'id_design'         =>(isset($val['id_design']) ? $val['id_design']:NULL),
    						'id_sub_design'     =>(isset($val['id_sub_design']) ? $val['id_sub_design']:NULL),
    						'piece'			    =>$val['piece'],
    						'gross_wt'			=>$val['gross_wt'],
    						'net_wt'			=>$val['gross_wt'],
    					);
    					$status=$this->$model->insertData($metal_details,'ret_old_metal_pocket_details');
    					if($status)
    					{
    					    $existData=array('id_product'=>$val['id_product'],'id_sub_design'=>$val['id_sub_design'],'design'=>$val['id_design'],'id_branch'=>$addData['id_branch']);
    					    $isExist = $this->$model->checkNonTagItemExist($existData);
    					    
    					    if($isExist['status'])
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
                                    'gross_wt'		=> $val['gross_wt'],
                                    'net_wt'		=> $val['gross_wt'],
                                    'no_of_piece'   => $val['piece'],
                                    'from_branch'	=> $addData['id_branch'],
                                    'to_branch'	    => NULL,
                                    'ref_no'	    => $insId,
                                    'status'	    => 10,
                                    'date'          => $branchDetails['entry_date'],
                                    'created_on'    => date("Y-m-d H:i:s"),
                                    'created_by'    =>  $this->session->userdata('uid')
                                );
                                $this->$model->insertData($non_tag_data,'ret_nontag_item_log');
                            }
    					}
				    }
				}
				if($this->db->trans_status()===TRUE)
				{
					$this->db->trans_commit();
					$response_data=array('status'=>TRUE,'message'=>'Pocket Created successfully..');
				}
				else
				{
					$this->db->trans_rollback();
					$response_data=array('status'=>FALSE,'message'=>'Unable To Proceed Your Request..');
				}
				echo json_encode($response_data);
			break;
			case 'ajax': 
					$list=$this->$model->get_pocket_list($_POST); 
				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_purchase/list');
			        $data = array(
		        					'list'  => $list,
									'access'=> $access
		        				 );  
					echo json_encode($data);
				break;
		}
	    
    }
    
    
    function metal_process_issue($type="")
	{   
       
		$model=	self::RET_PROCESS_MODEL;
        $SETT_MOD = "admin_settings_model";
		switch($type)
		{
			case 'list': 
					$data['main_content'] = self::VIEW_FOLDER.'metal_process/list';
                    $data['access'] = $this->$SETT_MOD ->get_access('admin_ret_metal_process/metal_process_issue/list');
        			$this->load->view('layout/template', $data);
			break;
            case 'add': 
                $branchDetails=$this->$model->get_branch_details();
                $data['branch'] = $branchDetails['id_branch'];
                $data['main_content'] = self::VIEW_FOLDER.'metal_process/form';
                $this->load->view('layout/template', $data);
            break;
            case 'metal_list': 
                $data=$this->$model->get_metal_stock_list($_POST);
                echo json_encode($data);
            break;
            
            case 'save':
                $addData=$_POST['process'];
                $receipt_payment=$_POST['receipt_payment'];
                //echo "<pre>";print_r($_POST);exit;
               
               $ho = $this->$model->get_headOffice();
               
                $this->db->trans_begin();
                $process_no=$this->$model->generate_process_number($addData['process_for'],$addData['id_metal_process']);
                $insData=array(
                                'process_no'        =>$process_no,
                                'process_for'       =>$addData['process_for'], //1-Issue,2-Receipt
                                'id_metal_process'  =>$addData['id_metal_process'],
                                'id_karigar'        =>$addData['id_karigar'],
                                'created_on'        =>date("Y-m-d H:i:s"),
                                'created_by'        =>$this->session->userdata('uid'),
                                'remark'            =>$addData['remark'],
                               );
                $insId = $this->$model->insertData($insData,'ret_old_metal_process');

                // print_r($this->db->last_query());exit;

                if($insId)
                {
                    if($addData['id_metal_process']==1) // MELTING
                    {
                        if($addData['process_for']==1) // Issue
                        {
                                $melting_data=array(
                                                    'id_old_metal_process' =>$insId,
                                                    'piece'          =>(isset($addData['piece']) ? ($addData['melting_trans_type']==1 ? $addData['piece'] :($addData['melting_trans_type']==2 ? $addData['tag_piece'] :$addData['non_tag_piece'])) :0),
                                                    'gross_wt'       =>($addData['melting_trans_type']==1 ? $addData['gross_wt']:($addData['melting_trans_type']==2 ? $addData['tag_gross_wt']:$addData['non_tag_gross_wt'])),
                                                    'net_wt'         =>($addData['melting_trans_type']==1 ? $addData['net_wt']:($addData['melting_trans_type']==2 ? $addData['tag_net_wt']:$addData['non_tag_net_wt'])),
                                                    'dia_wt'         =>($addData['melting_trans_type']==1 ? $addData['dia_wt'] : ($addData['melting_trans_type']==2 ? $addData['tag_dia_wt'] : 0)),
                                                    'amount'         =>($addData['melting_trans_type']==1 ? $addData['amount']:($addData['melting_trans_type']==2 ? $addData['tag_amount']:0)),
                                                    'purity'         =>($addData['melting_trans_type']==1 ? $addData['purity']:($addData['melting_trans_type']==2 ? $addData['tag_purity']:0)),
                                                    'rate'           =>($addData['melting_trans_type']==1 ? $addData['rate']:0),
                                                    'melting_status' =>0,
                                                    'created_on'     =>date("Y-m-d H:i:s"),
                                                    'created_by'     =>$this->session->userdata('uid'),
                                                   );
                              
                                $melting_status = $this->$model->insertData($melting_data,'ret_old_metal_melting');


                                if($melting_status)
                                {
                                    $pocket_details=$_POST['pocket'];

                		 		    if(!empty($pocket_details))
                		 		    {
                		 		        foreach($pocket_details['id_metal_pocket'] as $key => $val)
                		 		        {
                		 		            if($pocket_details['trans_type'][$key]==1)
                		 		            {
                		 		                 $melting_details=array(
                                                    'id_melting'    =>$melting_status,
                                                    'id_pocket'     =>$pocket_details['id_metal_pocket'][$key],
                                                    'id_metal_type' =>$pocket_details['id_metal_type'][$key],
                                                    'id_category'   =>$pocket_details['id_category'][$key] != "" ? $pocket_details['id_category'][$key] : NULL,
                                                    'issue_pcs'     =>$pocket_details['piece'][$key],
                                                    'issue_gwt'     =>$pocket_details['gross_wt'][$key],
                                                    'issue_nwt'     =>$pocket_details['net_wt'][$key],
                                                    'issue_diawt'   =>$pocket_details['dia_wt'][$key],
                                                    'issue_purity'  =>$pocket_details['avg_purity'][$key],
                                                    'issue_rate'    =>$pocket_details['avg_rate_per_gram'][$key],
                                                );

                                                $status = $this->$model->insertData($melting_details,'ret_old_metal_melting_details');
                                               
                                                // }

                                                if($status)
                                                {
                                                    $pocket_update_data = array(
                                                                                'issue_pcs'=>$pocket_details['piece'][$key],
                                                                                'issue_gwt'=>$pocket_details['gross_wt'][$key],
                                                                                'issue_nwt'=>$pocket_details['net_wt'][$key],
                                                                                'issue_diawt'=>$pocket_details['dia_wt'][$key],
                                                                                'issue_purity'=>($pocket_details['avg_purity'][$key]*$pocket_details['piece'][$key]),
                                                                                'updated_on' =>date("Y-m-d H:i:s"),
                                                                                'updated_by' =>$this->session->userdata('uid'),
                                                                                );

                                                        //  print_r($pocket_update_data);exit;                       
                                                     $pocketStatus = $this->$model->updatePocketItem($pocket_details['id_metal_pocket'][$key],$pocket_update_data,'+');
                                                    //  print_r($pocketStatus);exit;

                                                    }
                                                  }
                                                    else
                                                    {
                                                        $melting_details=array(
                                                            'id_melting'        =>$melting_status,
                                                            'id_pocket'         =>$pocket_details['id_metal_pocket'][$key],
                                                            'id_pocket_details' =>$pocket_details['id_pocket_details'][$key],
                                                            'issue_pcs'         =>$pocket_details['piece'][$key],
                                                            'issue_gwt'         =>$pocket_details['gross_wt'][$key],
                                                            'issue_nwt'         =>$pocket_details['net_wt'][$key],
                                                            'issue_diawt'       =>$pocket_details['dia_wt'][$key],
                                                        );
                                                    
                                                        $status=$this->$model->insertData($melting_details,'ret_old_metal_melting_details');
                                                    //  print_r($this->db->last_query());exit;

                                                    }
                                           
                		 		        }
                		 		    }
                		 		    if($this->db->trans_status()===TRUE)
                		 		    {
                		 		        $this->db->trans_commit();
            							 $log_data = array(
                                        'id_log'        => $this->session->userdata('id_log'),
                                        'event_date'    => date("Y-m-d H:i:s"),
                                        'module'        => 'Melting',
                                        'operation'     => 'Add',
                                        'record'        =>  $insId,  
                                        'remark'        => 'Melting added successfully'
                                        );
                                        $this->log_model->log_detail('insert','',$log_data);
                                        $this->session->set_flashdata('chit_alert',array('message'=>'Process Given to Melting successfully','class'=>'success','title'=>'Melting'));
                                        $responseData=array('status'=>TRUE,'message'=>'Process Given to Melting');
                		 		    }else{
                		 		        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Melting'));
                		 		        $responseData=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
                		 		    }
                                }
                            }
                            if($addData['process_for']==2) // Receipt
                            {
                                $receipt_details=$_POST['receipt'];
                                //echo "<pre>";print_r($receipt_payment);exit;
                                if(!empty($receipt_details))
                                {
                                    $branchDetails=$this->$model->get_branch_details();
                                    foreach($receipt_details['is_melting_select'] as $key => $val)
                                    {
                                       if($receipt_details['is_melting_select'][$key]==1)
                                       {
                                            $updMeltingData=array(
                                                            'melting_status'    =>1,
                                                            //'received_category' =>$receipt_details['id_ret_category'][$key],
                                                            'received_wt'       =>$receipt_details['recd_gwt'][$key],
                                                            'received_less_wt'  =>$receipt_details['received_less_wt'][$key],
                                                            'receipt_charges'   =>$receipt_details['charge'][$key],
                                                            'receipt_ref_no'    =>($receipt_details['ref_no'][$key]!='' ? $receipt_details['ref_no'][$key] : NULL),
                                                            'id_old_metal_process_receipt'=>$insId,
                                                            'updated_on'        =>date("Y-m-d H:i:s"),
                                                            'updated_by'        =>$this->session->userdata('uid'),
                                                          );
                                            $melting_receipt_status = $this->$model->updateData($updMeltingData,'id_melting',$receipt_details['id_melting'][$key],'ret_old_metal_melting');
                                            
                                            
                                            
                                            $CategoryDetails=json_decode($receipt_details['category_details'][$key],true);
                                            //print_r($CategoryDetails);exit;
                                                if(!empty($CategoryDetails))
                                                {
                                                    foreach($CategoryDetails as $cat)
                                                    {
                                                        $cateegoryData=array(
                                                                            'id_melting'        =>$receipt_details['id_melting'][$key],
                                                                            'melting_status'    =>1,
                                                                            'received_category' =>$cat['id_ret_category'],
                                                                            'id_section'        =>$cat['id_section'],
                                                                            'id_product'        =>$cat['id_product'],
                                                                            'id_design'         =>$cat['id_design'],
                                                                            'id_sub_design'     =>$cat['id_sub_design'],
                                                                            'recd_pcs'          =>$cat['recd_pcs'],
                                                                            'received_wt'       =>$cat['recd_gross_wt'],
                                                                            );
                                                        $this->$model->insertData($cateegoryData,'ret_old_metal_melting_recd_details');
                                                        
                                                        $categoryDetails=$this->$model->getCategoryDetails($cat['id_ret_category']);
                                                   
                                                        
                                                        $pur_items_log=array(
                                                        'id_old_metal_process'  =>$insId,
                                                        'to_branch'             =>$branchDetails['id_branch'],
                                                        'from_branch'           =>NULL,
                                                        'status'                =>1,
                                                        'item_type'             =>4, // Old Metal Process
                                                        'date'                  =>$branchDetails['entry_date'],
                                                        'created_on'            =>date("Y-m-d H:i:s"),
                                                        'created_by'            =>$this->session->userdata('uid'),
                                                        );
                                                        $this->$model->insertData($pur_items_log,'ret_purchase_items_log');
                                                    }
                                                }
                                                    
                                            //print_r($this->db->last_query());exit;
                                            if($melting_receipt_status)
                                            {
                                                $pur_items_log=array(
                                                'id_old_metal_process'  =>$receipt_details['id_old_metal_process'][$key],
                                                'to_branch'             =>$branchDetails['id_branch'],
                                                'from_branch'           =>NULL,
                                                'status'                =>1,
                                                'item_type'             =>4, // Old Metal Process
                                                'date'                  =>$branchDetails['entry_date'],
                                                'created_on'            =>date("Y-m-d H:i:s"),
                                                'created_by'            =>$this->session->userdata('uid'),
                                                );
                                                $this->$model->insertData($pur_items_log,'ret_purchase_items_log');
                                            }
                                        }
                                    }
                                    if($this->db->trans_status()===TRUE)
                		 		    {
                		 		        $this->db->trans_commit();
            							 $log_data = array(
                                        'id_log'        => $this->session->userdata('id_log'),
                                        'event_date'    => date("Y-m-d H:i:s"),
                                        'module'        => 'Melting',
                                        'operation'     => 'Add',
                                        'record'        =>  $insId,  
                                        'remark'        => 'Melting Receipt successfully'
                                        );
                                        $this->log_model->log_detail('insert','',$log_data);
                                        $this->session->set_flashdata('chit_alert',array('message'=>'Melting Receipt','class'=>'success','title'=>'Melting'));
                                        $responseData=array('status'=>TRUE,'message'=>'Melting Receipt');
                		 		    }else{
                		 		        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Melting'));
                		 		        $responseData=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
                		 		    }
                                }
                                //echo "<pre>";print_r($receipt_details);exit;
                            }
                    }
                    else if($addData['id_metal_process']==2) // TESTING
                    {
                        if($addData['process_for']==1) // Issue
                        {
                             $testing_issue_details=$_POST['testing_issue'];


                             if(!empty($testing_issue_details))
                             {
                                    foreach($testing_issue_details['is_melting_select'] as $key => $val)
                                    {
                                    
                                       if($testing_issue_details['is_melting_select'][$key]==1)
                                       {
                                            $testing_issue_data=array(

                                                'id_old_metal_process' =>$insId,
                                                'id_melting_recd'      =>$testing_issue_details['id_melting_recd'][$key],
                                                'net_wt'               =>$testing_issue_details['weight'][$key],
                                                'amount'               =>$testing_issue_details['amount'][$key],
                                                'purity'               =>$testing_issue_details['purity'][$key],
                                              );

                                            $testing_issue_status = $this->$model->insertData($testing_issue_data,'ret_old_metal_testing');
                                        
                                            //  print_r($this->db->last_query());exit;


                                            if($testing_issue_status)
                                            {
                                                $TestingIssue=$this->$model->updateData(array('melting_status'=>2),'id_melting_recd',$testing_issue_details['id_melting_recd'][$key],'ret_old_metal_melting_recd_details');
                                               
                                                   $branchDetails=$this->$model->get_branch_details();
                                                        $testing_issue_log=array(
                                                        'id_old_metal_process'=>$insId,
                                                        'from_branch'      =>$branchDetails['id_branch'],
                                                        'to_branch'        =>NULL,
                                                        'status'           =>3,
                                                        'item_type'        =>5, // Old Metal Process Outward
                                                        'date'             =>$branchDetails['entry_date'],
                                                        'created_on'       =>date("Y-m-d H:i:s"),
                                                        'created_by'      =>$this->session->userdata('uid'),
                                                        );
                                                        $this->$model->insertData($testing_issue_log,'ret_purchase_items_log');
                                            }
                                       }
                                    }
                                    if($this->db->trans_status()===TRUE)
                		 		    {
                		 		        $this->db->trans_commit();
            							 $log_data = array(
                                        'id_log'        => $this->session->userdata('id_log'),
                                        'event_date'    => date("Y-m-d H:i:s"),
                                        'module'        => 'Testing',
                                        'operation'     => 'Add',
                                        'record'        =>  $insId,  
                                        'remark'        => 'Testing Issue'
                                        );
                                        $this->log_model->log_detail('insert','',$log_data);
                                        $this->session->set_flashdata('chit_alert',array('message'=>'Testing Issue Successfully','class'=>'success','title'=>'Testing'));
                                        $responseData=array('status'=>TRUE,'message'=>'Testing Issue');
                		 		    }else{
                		 		        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Melting'));
                		 		        $responseData=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
                		 		    }
                             }
                        }
                        else if($addData['process_for']==2) // Receipt
                        {

                              $receipt_details=$_POST['testing_receipt'];

                                if(!empty($receipt_details))
                                {
                                    $branchDetails=$this->$model->get_branch_details();

                                    foreach($receipt_details['is_melting_select'] as $key => $val)
                                    {
                                         
                                     if($receipt_details['against_melting'] == 2)
                                     {
                                       if($receipt_details['is_melting_select'][$key]==1)
                                       {
                                           
                                            $updTestingReceiptData=array(
                                                            'received_purity'   =>$receipt_details['received_purity'][$key],
                                                            'received_wt'       =>$receipt_details['recd_gwt'][$key],
                                                            'production_loss'   =>$receipt_details['received_less_wt'][$key],
                                                            'receipt_charges'   =>$receipt_details['receipt_charges'][$key],
                                                            'receipt_ref_no'    =>($receipt_details['receipt_ref_no'][$key]!='' ? $receipt_details['receipt_ref_no'][$key] : NULL),
                                                            'testing_status'    =>1,
                                                            'id_old_metal_process_receipt'=>$insId,
                                                            'updated_on'        =>date("Y-m-d H:i:s"),
                                                            'updated_by'        =>$this->session->userdata('uid'),
                                                          );
                                            $melting_receipt_status = $this->$model->updateData($updTestingReceiptData,'id_metal_testing',$receipt_details['id_metal_testing'][$key],'ret_old_metal_testing');
                                            //print_r($this->db->last_query());exit;
                                            if($melting_receipt_status)
                                            {
                                                
                                                /*$this->$model->updateData(array('melting_status'=>3),'id_melting_recd',$receipt_details['id_melting_recd'][$key],'ret_old_metal_melting_recd_details');
                                                $pur_items_log=array(
                                                'id_old_metal_process'  =>$insId,
                                                'to_branch'             =>$branchDetails['id_branch'],
                                                'from_branch'           =>NULL,
                                                'status'                =>1,
                                                'item_type'             =>4, // Old Metal Process
                                                'date'                  =>$branchDetails['entry_date'],
                                                'created_on'            =>date("Y-m-d H:i:s"),
                                                'created_by'            =>$this->session->userdata('uid'),
                                                );
                                                $this->$model->insertData($pur_items_log,'ret_purchase_items_log');*/
                                            }
                                        }
                                      }
                                     else
                                     {
                                        if ($receipt_details['recd_gwt'][$key] != '' && $receipt_details['recd_gwt'][$key] > 0) {
                                            $insData=array(
                                                'melting_status'       =>($_POST['process']['process_type']==2 ? 6 :3),
                                                'testing_completed_wt'	=>$receipt_details['recd_gwt'][$key],
                                                'tested_purity'         =>$receipt_details['purity'][$key],
                                                'updated_by'            =>$this->session->userdata('uid'),
                                                'updated_on'            =>date("Y-m-d H:i:s"),
                                            );
                                            
                                            $this->$model->updateData($insData,'id_melting_recd',$receipt_details['id_melting_recd'][$key],'ret_old_metal_melting_recd_details'); 
                                            
                                           if($_POST['process']['process_type']==2)
                                           {

                                                $Lotdata = array(
                            							'lot_date'				=> date("Y-m-d H:i:s"),
                            							'lot_from'				=> 5,
                            							'lot_type'				=> 1,
                            							'stock_type'			=> 2,
                            							'gold_smith'            => $addData['id_karigar'],
                            							'lot_received_at'		=> $branchDetails['id_branch'],
                            							'created_branch'		=> $branchDetails['id_branch'],
                            						    'id_category'			=> $receipt_details['received_category'][$key],
                            							//'id_purity'				=> $receipt_details['purity'][$key],
                            							'narration'      		=> 'From Testing Process',
                            							'id_metal_process'      => $insId,
                            							'created_on'	  		=> date("Y-m-d H:i:s"),
                            							'created_by'      		=> $this->session->userdata('uid')
                            						);
                            						$lotId = $this->$model->insertData($Lotdata,'ret_lot_inwards');
                                                

                						        $item_details=array(
                                                    'lot_no'=>$lotId,
                                                    'id_section'=>$receipt_details['id_section'][$key],
                                                    'lot_product'=>$receipt_details['id_product'][$key],
                                                    'lot_id_category'=>$receipt_details['received_category'][$key],
                                                    'lot_id_design'=>$receipt_details['id_design'][$key],
                                                    'id_sub_design'=>$receipt_details['id_sub_design'][$key],
                                                    'no_of_piece'=>$receipt_details['recd_pcs'][$key],
                                                    'gross_wt'=>$receipt_details['recd_gwt'][$key],
                                                    'net_wt'=>$receipt_details['recd_gwt'][$key],
                                                    'lot_id_purity'=>$receipt_details['purity'][$key],
                                                    'pur_wt'=>number_format(($receipt_details['purity'][$key] * $receipt_details['recd_gwt'][$key]) / 100, 3, '.', ''),
                                                    'purchase_touch'=>$receipt_details['purity'][$key],
                                                    'calc_type'=>2,
                                                ); 
                						        
                						        $this->$model->insertData($item_details,'ret_lot_inwards_detail');
                						        
                						        $existData=array('id_section'=>$receipt_details['id_section'][$key],'id_product'=>$receipt_details['id_product'][$key],'design'=>$receipt_details['id_design'][$key],'id_sub_design'=>$receipt_details['id_sub_design'][$key],'id_branch'=>$branchDetails['id_branch']);
                						        
                						        $isExist = $this->$model->checkNonTagItemExist($existData);
                						        
                						        if($isExist['status'] == TRUE)
                						        {
                						            $nt_data = array(
                                                    'id_nontag_item'=> $isExist['id_nontag_item'],
                                                    'no_of_piece'   => $receipt_details['recd_pcs'][$key],
                                                    'gross_wt'		=> $receipt_details['recd_gwt'][$key],
                                                    'net_wt'		=> $receipt_details['recd_gwt'][$key],  
                                                    'updated_by'	=> $this->session->userdata('uid'),
                                                    'updated_on'	=> date('Y-m-d H:i:s'),
                                                    );
                                                    $this->$model->updateNTData($nt_data,'+');
                                        													
                                                    $non_tag_data=array(
                                                    'product'	    => $receipt_details['id_product'][$key],
                                                    'design'	    => $receipt_details['id_design'][$key],
                                                    'id_sub_design'	=> $receipt_details['id_sub_design'][$key],
                                                    'no_of_piece'   => $receipt_details['recd_pcs'][$key],
                                                    'gross_wt'		=> $receipt_details['recd_gwt'][$key],
                                                    'net_wt'		=> $receipt_details['recd_gwt'][$key],
                                                    'from_branch'	=> NULL,
                                                    'to_branch'	    => $branchDetails['id_branch'],
                                                    'status'	    => 0,
                                                    'date'          => $branchDetails['entry_date'],
                                                    'created_on'    => date("Y-m-d H:i:s"),
                                                    'created_by'    =>  $this->session->userdata('uid')
                                                    );
                                                    $this->$model->insertData($non_tag_data,'ret_nontag_item_log');
                                                    
                                                    $section_non_tag_data=array(
                                                    'to_section'	=> $receipt_details['id_section'][$key],
                                                    'from_section'	=> NULL,
                                                    'product'	    => $receipt_details['id_product'][$key],
                                                    'design'	    => $receipt_details['id_design'][$key],
                                                    'id_sub_design'	=> $receipt_details['id_sub_design'][$key],
                                                    'no_of_piece'   => $receipt_details['recd_pcs'][$key],
                                                    'gross_wt'		=> $receipt_details['recd_gwt'][$key],
                                                    'net_wt'		=> $receipt_details['recd_gwt'][$key],
                                                    'from_branch'	=> NULL,
                                                    'to_branch'	    => $branchDetails['id_branch'],
                                                    'status'	    => 0,
                                                    'date'          => $branchDetails['entry_date'],
                                                    'created_on'    => date("Y-m-d H:i:s"),
                                                    'created_by'    =>  $this->session->userdata('uid')
                                                     );
                                                    $this->$model->insertData($section_non_tag_data,'ret_section_nontag_item_log');

                						        }else
                						        {
                						            $nt_data=array(
                                                        'branch'	    => $branchDetails['id_branch'],
                                                        'no_of_piece'   => $receipt_details['recd_pcs'][$key],
                                                        'id_section'    =>$receipt_details['id_section'][$key],
                                                        'product'	    => $receipt_details['id_product'][$key],
                                                        'design'	    => $receipt_details['id_design'][$key],
                                                        'id_sub_design'	=> $receipt_details['id_sub_design'][$key],
                                                        'gross_wt'		=> $receipt_details['recd_gwt'][$key],
                                                        'net_wt'		=> $receipt_details['recd_gwt'][$key],
                                                        'created_on'    => date("Y-m-d H:i:s"),
                                                        'created_by'    => $this->session->userdata('uid')
                                                    );
                                                    $this->$model->insertData($nt_data,'ret_nontag_item'); 
                                                    
                                                    $non_tag_data=array(
                                                    'product'	    => $receipt_details['id_product'][$key],
                                                    'gross_wt'		=> $receipt_details['recd_gwt'][$key],
                                                    'net_wt'		=> $receipt_details['recd_gwt'][$key],
                                                    'no_of_piece'   => $receipt_details['recd_pcs'][$key],
                                                    'from_branch'	=> NULL,
                                                    'to_branch'	    => $branchDetails['id_branch'],
                                                    'status'	    => 0,
                                                    'date'          => $branchDetails['entry_date'],
                                                    'created_on'    => date("Y-m-d H:i:s"),
                                                    'created_by'    =>  $this->session->userdata('uid')
                                                    );
                                                    $this->$model->insertData($non_tag_data,'ret_nontag_item_log');
                                                    
                                                    $section_non_tag_data=array(
                                                    'to_section'	=> $receipt_details['id_section'][$key],
                                                    'from_section'	=> NULL,
                                                    'product'	    => $receipt_details['id_product'][$key],
                                                    'design'	    => $receipt_details['id_design'][$key],
                                                    'id_sub_design'	=> $receipt_details['id_sub_design'][$key],
                                                    'no_of_piece'   => $receipt_details['recd_pcs'][$key],
                                                    'gross_wt'		=> $receipt_details['recd_gwt'][$key],
                                                    'net_wt'		=> $receipt_details['recd_gwt'][$key],
                                                    'from_branch'	=> NULL,
                                                    'to_branch'	    => $branchDetails['id_branch'],
                                                    'status'	    => 0,
                                                    'date'          => $branchDetails['entry_date'],
                                                    'created_on'    => date("Y-m-d H:i:s"),
                                                    'created_by'    =>  $this->session->userdata('uid')
                                                     );
                                                    $this->$model->insertData($section_non_tag_data,'ret_section_nontag_item_log');
                                                    
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
                                        'module'        => 'Melting',
                                        'operation'     => 'Add',
                                        'record'        =>  $insId,  
                                        'remark'        => 'Testing Receipt successfully'
                                        );
                                        $this->log_model->log_detail('insert','',$log_data);
                                        $this->session->set_flashdata('chit_alert',array('message'=>'Testing Receipt','class'=>'success','title'=>'Testing Receipt'));
                                        $responseData=array('status'=>TRUE,'message'=>'Testing Receipt');
                		 		    }else{
                		 		        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Testing Receipt'));
                		 		        $responseData=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
                		 		    }
                                }
                               
                        }
                    }

                    else if($addData['id_metal_process']==3) // Refining
                    {
                        if($addData['process_for']==1) // Issue
                        {
                             $refining_issue_details=$_POST['refining_issue'];
                             if(!empty($refining_issue_details))
                             {
                                    foreach($refining_issue_details['is_melting_select'] as $key => $val)
                                    {
                                       if($refining_issue_details['is_melting_select'][$key]==1)
                                       {
                                            $refining_issue_data=array(
                                                'id_old_metal_process' =>$insId,
                                                'id_melting_recd'     =>$refining_issue_details['id_melting_recd'][$key],
                                                'issue_weight'         =>$refining_issue_details['weight'][$key],
                                                'refining_status'      =>0,
                                            );
                                            $refining_issue_status = $this->$model->insertData($refining_issue_data,'ret_old_metal_refining');
                                            
                                            $this->$model->updateData(array('melting_status'=>4),'id_melting_recd',$refining_issue_details['id_melting_recd'][$key],'ret_old_metal_melting_recd_details');

                                       }
                                    }
                                    if($this->db->trans_status()===TRUE)
                		 		    {
                		 		        $this->db->trans_commit();
            							 $log_data = array(
                                        'id_log'        => $this->session->userdata('id_log'),
                                        'event_date'    => date("Y-m-d H:i:s"),
                                        'module'        => 'Refining',
                                        'operation'     => 'Add',
                                        'record'        =>  $insId,  
                                        'remark'        => 'Refining Issue'
                                        );
                                        $this->log_model->log_detail('insert','',$log_data);
                                        $this->session->set_flashdata('chit_alert',array('message'=>'Refining Issue Successfully','class'=>'success','title'=>'Refining'));
                                        $responseData=array('status'=>TRUE,'message'=>'Refining Issue');
                		 		    }else{
                		 		        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Refining'));
                		 		        $responseData=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
                		 		    }
                             }
                        }
                        else if($addData['process_for']==2) // Receipt
                        {
                             $refining_receipt_details=$_POST['refining_receipt'];
                             //echo "<pre>";print_r($refining_receipt_details);exit;
                              if(!empty($refining_receipt_details))
                                {
                                    $branchDetails=$this->$model->get_branch_details();
                                    foreach($refining_receipt_details['is_melting_select'] as $key => $val)
                                    {
                                       if($refining_receipt_details['is_melting_select'][$key]==1)
                                       {
                                        $updRefiningReceiptData=array(
                                            'refining_status'   =>1,
                                            'id_old_metal_process_receipt'=>$insId,
                                            'receipt_charges'   =>$refining_receipt_details['receipt_charges'][$key],
                                            'receipt_charges_tax_perc'   =>$refining_receipt_details['chg_tax_perc'][$key],
                                            'receipt_charges_tax_amt'   =>$refining_receipt_details['chg_tax_value'][$key],
                                            'receipt_charges_tax_cgst'   =>$addData['chg_tax_type']==1?$refining_receipt_details['chg_tax_value'][$key]/2:0,
                                            'receipt_charges_tax_sgst'   =>$addData['chg_tax_type']==1? $refining_receipt_details['chg_tax_value'][$key]/2:0,
                                            'receipt_charges_tax_igst'   =>$addData['chg_tax_type']==0? $refining_receipt_details['chg_tax_value'][$key]:0,
                                            'receipt_ref_no'    =>($refining_receipt_details['receipt_ref_no'][$key]!='' ? $refining_receipt_details['receipt_ref_no'][$key] : NULL),
                                            'updated_on'        =>date("Y-m-d H:i:s"),
                                            'updated_by'        =>$this->session->userdata('uid'),
                                          );
                                            $refining_receipt_status = $this->$model->updateData($updRefiningReceiptData,'id_metal_refining',$refining_receipt_details['id_metal_refining'][$key],'ret_old_metal_refining');
                                            //print_r($refining_receipt_status);exit;
                                           
        
                                           
                                            if($refining_receipt_status)
                                            {
                                                $CategoryDetails=json_decode($refining_receipt_details['category_details'][$key],true);
                                                
                                                if(!empty($CategoryDetails))
                                                {
                                                    foreach($CategoryDetails as $cat)
                                                    {
                                                        
                                                        $cateegoryData=array(
                                                                            'id_metal_refining' =>$refining_receipt_details['id_metal_refining'][$key],
                                                                            'received_category' =>$cat['id_ret_category'],
                                                                            'piece'             =>1,
                                                                            'id_section'        =>$cat['id_section'],
                                                                            'id_product'        =>$cat['id_product'],
                                                                            'id_design'         =>$cat['id_design'],
                                                                            'id_sub_design'     =>$cat['id_sub_design'],
                                                                            'received_wt'       =>$cat['recd_gross_wt'],
                                                                            'purity'            =>$cat['id_purity'],
                                                                            );
                                                        $insStatus=$this->$model->insertData($cateegoryData,'ret_old_metal_refining_details');
                                                        
                                                        //UPDATE INTO NON TAG ITEM STOCK SUMMARY
                                                        if($insStatus)
                                                        {
                                			        	        
                                                            $existData=array('id_section'=>$cat['id_section'],'id_product'=>$cat['id_product'],'design'=>$cat['id_design'],'id_sub_design'=>$cat['id_sub_design'],'id_branch'=>$branchDetails['id_branch']);
                                                            //print_r($existData);exit;
                                                            $isExist = $this->$model->checkNonTagItemExist($existData);
                                                            
                                                            if($isExist['status'] == TRUE)
                                                            {
                                                                $nt_data = array(
                                                                    'id_nontag_item'=> $isExist['id_nontag_item'],
                                                                    'no_of_piece'   => 1,
                                                                    'gross_wt'		=> $cat['recd_gross_wt'],
                                                                    'net_wt'		=> $cat['recd_gross_wt'],  
                                                                    'updated_by'	=> $this->session->userdata('uid'),
                                                                    'updated_on'	=> date('Y-m-d H:i:s'),
                                                                    );
                                                                    $this->$model->updateNTData($nt_data,'+');
    
                                                                    $non_tag_data=array(
                                                                        'product'	    => $cat['id_product'],
                                                                        'design'	    => $cat['id_design'],
                                                                        'id_sub_design'	=> $cat['id_sub_design'],
                                                                        'no_of_piece'   => 1,
                                                                        'gross_wt'		=> $cat['recd_gross_wt'],
                                                                        'net_wt'		=> $cat['recd_gross_wt'],
                                                                        'from_branch'	=> NULL,
                                                                        'to_branch'	    => $branchDetails['id_branch'],
                                                                        'status'	    => 0,
                                                                        'date'          => $branchDetails['entry_date'],
                                                                        'created_on'    => date("Y-m-d H:i:s"),
                                                                        'created_by'    =>  $this->session->userdata('uid')
                                                                        );
                                                                        $this->$model->insertData($non_tag_data,'ret_nontag_item_log');
                                                                        
                                                                        $section_non_tag_data=array(
                                                                        'to_section'	=> $cat['id_section'],
                                                                        'from_section'	=> NULL,
                                                                        'product'	    => $cat['id_product'],
                                                                        'design'	    => $cat['id_design'],
                                                                        'id_sub_design'	=> $cat['id_sub_design'],
                                                                        'gross_wt'		=> $cat['recd_gross_wt'],
                                                                        'net_wt'		=> $cat['recd_gross_wt'],
                                                                        'from_branch'	=> NULL,
                                                                        'to_branch'	    => $branchDetails['id_branch'],
                                                                        'status'	    => 0,
                                                                        'date'          => $branchDetails['entry_date'],
                                                                        'created_on'    => date("Y-m-d H:i:s"),
                                                                        'created_by'    =>  $this->session->userdata('uid')
                                                                         );
                                                                        $this->$model->insertData($section_non_tag_data,'ret_section_nontag_item_log');
                                                            }
                                                            else
                                                            {
                                                                $nt_data=array(
                                                                    'branch'	    => $branchDetails['id_branch'],
                                                                    'no_of_piece'   => 1,
                                                                    'id_section'	=> $cat['id_section'],
                                                                    'product'	    => $cat['id_product'],
                                                                    'design'	    => $cat['id_design'],
                                                                    'id_sub_design'	=> $cat['id_sub_design'],
                                                                    'gross_wt'		=> $cat['recd_gross_wt'],
                                                                    'net_wt'		=> $cat['recd_gross_wt'],
                                                                    'created_on'    => date("Y-m-d H:i:s"),
                                                                    'created_by'    => $this->session->userdata('uid')
                                                                );
                                                                $this->$model->insertData($nt_data,'ret_nontag_item'); 
                                                                
                                                                $non_tag_data=array(
                                                                'product'	    => $cat['id_product'],
                                                                'design'	    => $cat['id_design'],
                                                                'id_sub_design'	=> $cat['id_sub_design'],
                                                                'gross_wt'		=> $cat['recd_gross_wt'],
                                                                'net_wt'		=> $cat['recd_gross_wt'],
                                                                'no_of_piece'   => 1,
                                                                'from_branch'	=> NULL,
                                                                'to_branch'	    => $branchDetails['id_branch'],
                                                                'status'	    => 0,
                                                                'date'          => $branchDetails['entry_date'],
                                                                'created_on'    => date("Y-m-d H:i:s"),
                                                                'created_by'    =>  $this->session->userdata('uid')
                                                                );
                                                                $this->$model->insertData($non_tag_data,'ret_nontag_item_log'); 
                                                                
                                                                $section_non_tag_data=array(
                                                                'to_section'	=> $cat['id_section'],
                                                                'from_section'	=> NULL,
                                                                'product'	    => $cat['id_product'],
                                                                'design'	    => $cat['id_design'],
                                                                'id_sub_design'	=> $cat['id_sub_design'],
                                                                'gross_wt'		=> $cat['recd_gross_wt'],
                                                                'net_wt'		=> $cat['recd_gross_wt'],
                                                                'from_branch'	=> NULL,
                                                                'to_branch'	    => $branchDetails['id_branch'],
                                                                'status'	    => 0,
                                                                'date'          => $branchDetails['entry_date'],
                                                                'created_on'    => date("Y-m-d H:i:s"),
                                                                'created_by'    =>  $this->session->userdata('uid')
                                                                 );
                                                                $this->$model->insertData($section_non_tag_data,'ret_section_nontag_item_log');
                                                                        
                                                            }	
                                                                
                                                        }
                                                        //UPDATE INTO NON TAG ITEM STOCK SUMMARY
            					    

                                                        
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
                                        'module'        => 'Melting',
                                        'operation'     => 'Add',
                                        'record'        =>  $insId,  
                                        'remark'        => 'Testing Receipt successfully'
                                        );
                                        $this->log_model->log_detail('insert','',$log_data);
                                        $this->session->set_flashdata('chit_alert',array('message'=>'Refining Receipt','class'=>'success','title'=>'Refining Receipt'));
                                        $responseData=array('status'=>TRUE,'message'=>'Refining Receipt');
                		 		    }else{
                		 		        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Refining Receipt'));
                		 		        $responseData=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
                		 		    }
                                }
                        }
                    }
                    else if($addData['id_metal_process']==4) // POLISHING
                    {
                        if($addData['process_for']==1) // Issue
                        {
                                $polishing_data=array(
                                                    'id_old_metal_process' =>$insId,
                                                    'no_of_piece'    =>$addData['piece'],
                                                    'gross_wt'       =>$addData['gross_wt'],
                                                    'net_wt'         =>$addData['net_wt'],
                                                    'amount'         =>0,
                                                    'status'         =>0,
                                                    'created_on'     =>date("Y-m-d H:i:s"),
                                                    'created_by'     =>$this->session->userdata('uid'),
                                                   );
                                $melting_status = $this->$model->insertData($polishing_data,'ret_old_metal_polishing');
                                if($melting_status)
                                {
                                    $pocket_details=$_POST['pocket'];
                		 		    if(!empty($pocket_details))
                		 		    {
                		 		        foreach($pocket_details['id_metal_pocket'] as $key => $val)
                		 		        {
                                            $melting_details=array(
                                                'id_polishing'  =>$melting_status,
                                                'id_pocket'     =>$pocket_details['id_metal_pocket'][$key],
                                                'id_old_metal_type'=>$pocket_details['id_metal_type'][$key],
                                                'issue_pcs'     =>$pocket_details['issue_pcs'][$key],
                                                'issue_gwt'     =>$pocket_details['issue_gwt'][$key],
                                                'issue_nwt'     =>$pocket_details['issue_nwt'][$key],
                                                'issue_purity'  =>$pocket_details['issue_purity'][$key],
                                                'issue_diawt'   =>$pocket_details['diawt'][$key],
                                            );
                                            $status=$this->$model->insertData($melting_details,'ret_old_metal_polishing_details');
                                            if($status)
                                            {
                                                 $pocket_update_data = array(
                                                                            'issue_pcs'         =>$pocket_details['issue_pcs'][$key],
                                                                            'issue_gwt'         =>$pocket_details['issue_gwt'][$key],
                                                                            'issue_nwt'         =>$pocket_details['issue_nwt'][$key],
                                                                            'issue_purity'      =>($pocket_details['issue_purity'][$key]*$pocket_details['issue_pcs'][$key]),
                                                                            'updated_on'        =>date("Y-m-d H:i:s"),
                                                                            'updated_by'        =>$this->session->userdata('uid'),
                                                                            );
                                                 $pocketStatus = $this->$model->updatePocketItem($pocket_details['id_metal_pocket'][$key],$pocket_update_data,'+');
                                                 /*if($pocketStatus)
                                                 {
                                                    $pocketDetails=$this->$model->getPocketingDetails($pocket_details['id_metal_pocket'][$key]); 
                                                    //UPDATEING PURCHASE ITEM LOG 
                                                    if(sizeof($pocketDetails)>0)
                                                    {
                                                        $branchDetails=$this->$model->get_branch_details();
                                                        foreach($pocketDetails as $items)
                                                        {
                                                                $old_metal_log=array(
                                                                'old_metal_sale_id'=>$items['old_metal_sale_id'],
                                                                'from_branch'      =>$branchDetails['id_branch'],
                                                                'to_branch'        =>NULL,
                                                                'status'           =>3,
                                                                'item_type'        =>1, // Old Metal
                                                                'date'             =>$branchDetails['entry_date'],
                                                                'created_on'       =>date("Y-m-d H:i:s"),
                                                                'created_by'      =>$this->session->userdata('uid'),
                                                                );
                                                                $this->$model->insertData($old_metal_log,'ret_purchase_items_log');
                                                        }
                                                    }
                                                    //UPDATEING PURCHASE ITEM LOG 
                                                 }*/
                                            }
                		 		        }
                		 		    }
                		 		    if($this->db->trans_status()===TRUE)
                		 		    {
                		 		        $this->db->trans_commit();
            							 $log_data = array(
                                        'id_log'        => $this->session->userdata('id_log'),
                                        'event_date'    => date("Y-m-d H:i:s"),
                                        'module'        => 'Melting',
                                        'operation'     => 'Add',
                                        'record'        =>  $insId,  
                                        'remark'        => 'Melting added successfully'
                                        );
                                        $this->log_model->log_detail('insert','',$log_data);
                                        $this->session->set_flashdata('chit_alert',array('message'=>'Process Given to Polishing successfully','class'=>'success','title'=>'Polishing'));
                                        $responseData=array('status'=>TRUE,'message'=>'Process Given to Polishing');
                		 		    }else{
                		 		        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Polishing'));
                		 		        $responseData=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
                		 		    }
                                }
                            }
                            if($addData['process_for']==2) // Receipt
                            {
                                $receipt_details=$_POST['polishing_receipt'];
                                if(!empty($receipt_details))
                                {
                                    $branchDetails=$this->$model->get_branch_details();
                                    $total_recd_gwt = 0;
                                    $total_recd_nwt = 0;
                                    $total_no_of_piece = 0;
                                    
                                    
                                    
                                    foreach($receipt_details['is_polishing_select'] as $key => $val)
                                    {
                                       if($receipt_details['is_polishing_select'][$key]==1)
                                       {
                                            $total_recd_gwt+=$receipt_details['recd_gwt'][$key];
                                            $total_recd_nwt+=$receipt_details['recd_nwt'][$key];
                                            $total_no_of_piece+=$receipt_details['recd_pcs'][$key];
                                            $updMeltingData=array(
                                                            'status'            =>1,
                                                            'received_pcs'       =>$receipt_details['recd_pcs'][$key],
                                                            'received_gwt'       =>$receipt_details['recd_gwt'][$key],
                                                            'received_nwt'       =>$receipt_details['recd_nwt'][$key],
                                                          );
                                            $polishing_receipt_status = $this->$model->updateData($updMeltingData,'id_polishing_details',$receipt_details['id_polishing_details'][$key],'ret_old_metal_polishing_details');
                                            $this->$model->updateData(array("id_old_metal_process_receipt"=>$insId,"updated_on"=>date("Y-m-d H:i:s"),"updated_by"=>$this->session->userdata('uid')),'id_polishing',$receipt_details['id_polishing'][$key],'ret_old_metal_polishing');

                                            $CategoryDetails=json_decode($receipt_details['category_details'][$key],true);
                                            
                                            if(!empty($CategoryDetails))
                                            {
                                                $lotDetails=array();
                                                foreach($CategoryDetails as $cat)
                                                {
                                                    $lotDetails [$cat['id_ret_category']][$cat['id_purity']][$cat['is_non_tag']][] = $cat;
                                                }
                                                //echo "<pre>";print_r(($lotDetails));exit;
                                                if(sizeof($lotDetails)>0)
                                                {
                                                    foreach($lotDetails as $k =>$lot)
                                                    {
                                                        foreach($lot as $cat_key => $category)
                                                        {
                                                            foreach($category as $pur_key => $purity)
                                                            {
                                                                $id_category = $k;
                                                                $id_purity = $cat_key;
                                                                $Lotdata = array(
                                        							'lot_date'				=> date("Y-m-d H:i:s"),
                                        							'lot_from'				=> 5,
                                        							'lot_type'				=> 1,
                                        							'gold_smith'            => $addData['id_karigar'],
                                        							'lot_received_at'		=> $branchDetails['id_branch'],
                                        							'created_branch'		=> $id_branch,
                                        						    'id_category'			=> $id_category,
                                        							'id_purity'				=> $cat_key,
                                        							'narration'      		=> 'From Polishing Process',
                                        							'id_metal_process'      => $insId,
                                        							'created_on'	  		=> date("Y-m-d H:i:s"),
                                        							'created_by'      		=> $this->session->userdata('uid')
                                        						);
                                        						$lotId = $this->$model->insertData($Lotdata,'ret_lot_inwards');
                                        						//echo "<pre>";print_r($purity);exit;
                                        						foreach($purity as $product)
                                        						{
                                        						    
                                        						    $recd_details = array(
                                        						                         'id_polishing'     =>$receipt_details['id_polishing'][$key],
                                        						                         'stock_type'       =>($product['is_non_tag']==1 ? 2:1),
                                        						                         'id_category'      =>$id_category,
                                        						                         'id_purity'        =>$id_purity,
                                        						                         'id_product'       =>$product['id_product'],
                                        						                         'id_section'       =>$product['id_section'],
                                        						                         'id_design'        =>($product['id_design']!='' ? $product['id_design']:NULL),
                                        						                         'id_sub_design'    =>($product['id_sub_design']!='' ? $product['id_sub_design']: NULL),
                                        						                         'no_of_piece'      =>$product['recd_pcs'],
                                        						                         'gross_wt'         =>$product['recd_gross_wt'],
                                        						                         'net_wt'           =>$product['recd_nwt'],
                                        						                         'dia_wt'           =>$product['recd_diawt'],
                                        						                         );
                                        						    $this->$model->insertData($recd_details,'ret_old_metal_polishing_recd_details');
                                        						    
                                        						    
                                        						    $item_details=array('lot_no'=>$lotId,'id_section'=>$product['id_section'],'lot_product'=>$product['id_product'],'lot_id_design'=>$product['id_design'],'id_sub_design'=>$product['id_sub_design'],'no_of_piece'=>$product['recd_pcs'],'gross_wt'=>$product['recd_gross_wt'],'net_wt'=>$product['recd_nwt']); 
                                        						        
                                        						    $this->$model->insertData($item_details,'ret_lot_inwards_detail');
                                        						        
                                        						    //print_r($this->db->last_query());exit;
                                        						    if($product['is_non_tag']==1)
                                        						    {
                                        						        $this->$model->updateData(array("stock_type"=>2),'lot_no',$lotId,'ret_lot_inwards');
                                        						        
                                        						        
                                        						        
                                        						        $existData=array('id_section'=>$product['id_section'],'id_product'=>$product['id_product'],'design'=>$product['id_design'],'id_sub_design'=>$product['id_sub_design'],'id_branch'=>$branchDetails['id_branch']);
                                        						        
                                        						        $isExist = $this->$model->checkNonTagItemExist($existData);
                                        						        
                                        						        if($isExist['status'] == TRUE)
                                        						        {
                                        						            $nt_data = array(
                                                                            'id_nontag_item'=>$isExist['id_nontag_item'],
                                                                            'gross_wt'		=> $product['recd_gross_wt'],
                                                                            'net_wt'		=> $product['recd_nwt'],  
                                                                            'no_of_piece'   => $product['recd_pcs'],
                                                                            'updated_by'	=> $this->session->userdata('uid'),
                                                                            'updated_on'	=> date('Y-m-d H:i:s'),
                                                                            );
                                                                            $this->$model->updateNTData($nt_data,'+');
                                                                													
                                                                            $non_tag_data=array(
                                                                            'product'	    => $product['id_product'],
                                                                            'design'	    => $product['id_design'],
                                                                            'id_sub_design'	    => $product['id_sub_design'],
                                                                            'gross_wt'		=> $product['recd_gross_wt'],
                                                                            'net_wt'		=> $product['recd_nwt'],
                                                                            'no_of_piece'   => $product['recd_pcs'],
                                                                            'from_branch'	=> NULL,
                                                                            'to_branch'	    => $branchDetails['id_branch'],
                                                                            'status'	    => 0,
                                                                            'date'          => $branchDetails['entry_date'],
                                                                            'created_on'    => date("Y-m-d H:i:s"),
                                                                            'created_by'    =>  $this->session->userdata('uid')
                                                                            );
                                                                            $this->$model->insertData($non_tag_data,'ret_nontag_item_log');
                                        						        }else
                                        						        {
                                        						            $nt_data=array(
                                                                                'branch'	    => $branchDetails['id_branch'],
                                                                                'id_section'	=> $product['id_section'],
                                                                                'product'	    => $product['id_product'],
                                                                                'design'	    => $product['id_design'],
                                                                                'id_sub_design'	    => $product['id_sub_design'],
                                                                                'gross_wt'		=> $product['recd_gross_wt'],
                                                                                'net_wt'		=> $product['recd_nwt'],
                                                                                'no_of_piece'   => $product['recd_pcs'],
                                                                                'created_on'    => date("Y-m-d H:i:s"),
                                                                                'created_by'    => $this->session->userdata('uid')
                                                                            );
                                                                            $this->$model->insertData($nt_data,'ret_nontag_item'); 
                                                                            
                                                                            $non_tag_data=array(
                                                                            'product'	    => $product['id_product'],
                                                                            'gross_wt'		=> $product['recd_gross_wt'],
                                                                            'net_wt'		=> $product['recd_nwt'],
                                                                            'from_branch'	=> NULL,
                                                                            'no_of_piece'   => $product['recd_pcs'],
                                                                            'to_branch'	    => $branchDetails['id_branch'],
                                                                            'status'	    => 0,
                                                                            'date'          => $branchDetails['entry_date'],
                                                                            'created_on'    => date("Y-m-d H:i:s"),
                                                                            'created_by'    =>  $this->session->userdata('uid')
                                                                            );
                                                                            $this->$model->insertData($non_tag_data,'ret_nontag_item_log'); 
                                        						        }
                                        						        
                                        						        $section_non_tag_data=array(
                                                                        'to_section'	=> $product['id_section'],
                                                                        'from_section'	=> NULL,
                                                                        'product'	    => $product['id_product'],
                                                                        'design'	    => $product['id_design'],
                                                                        'id_sub_design'	=> $product['id_sub_design'],
                                                                        'gross_wt'		=> $product['recd_gross_wt'],
                                                                        'net_wt'		=> $product['recd_nwt'],
                                                                        'no_of_piece'   => $product['recd_pcs'],
                                                                        'from_branch'	=> NULL,
                                                                        'to_branch'	    => $branchDetails['id_branch'],
                                                                        'status'	    => 0,
                                                                        'date'          => $branchDetails['entry_date'],
                                                                        'created_on'    => date("Y-m-d H:i:s"),
                                                                        'created_by'    =>  $this->session->userdata('uid')
                                                                         );
                                                                        $this->$model->insertData($section_non_tag_data,'ret_section_nontag_item_log');
                                                                
                                        						    }
                                        						    
                                        						    
                                        						}
                                        						
                                        						
                                        						
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                //echo "<pre>";print_r($lotDetails);exit;
                                            }
                                      }
                                    }
                                    if($this->db->trans_status()===TRUE)
                		 		    {
                		 		        $this->db->trans_commit();
            							 $log_data = array(
                                        'id_log'        => $this->session->userdata('id_log'),
                                        'event_date'    => date("Y-m-d H:i:s"),
                                        'module'        => 'Polishing',
                                        'operation'     => 'Add',
                                        'record'        =>  $insId,  
                                        'remark'        => 'Polishing Receipt successfully'
                                        );
                                        $this->log_model->log_detail('insert','',$log_data);
                                        $this->session->set_flashdata('chit_alert',array('message'=>'Polishing Receipt','class'=>'success','title'=>'Polishing'));
                                        $responseData=array('status'=>TRUE,'message'=>'Polishing Receipt');
                		 		    }else{
                		 		        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Polishing'));
                		 		        $responseData=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');
                		 		    }
                                }
                                //echo "<pre>";print_r($receipt_details);exit;
                            }
                    }
                    if(!empty($receipt_payment))
		 		    {
		 		        if($receipt_payment['cash_amount']>0)
		 		        {
                            $payData=array(
                                'id_old_metal_process'=>$insId,
                                'type'                =>1,
                                'payment_mode'        =>'Cash',
                                'payment_amount'      =>$receipt_payment['cash_amount'],
                                'payment_date'        =>date("Y-m-d H:i:s"),
                            );
                            $this->$model->insertData($payData,'ret_old_metal_process_payment');
		 		        }
		 		        if($receipt_payment['net_banking_amount']>0)
		 		        {
                            $payData=array(
                                'id_old_metal_process'=>$insId,
                                'type'                =>1,
                                'payment_mode'        =>'NB',
                                'payment_ref_number'  =>($receipt_payment['net_banking_ref_no']!='' ? $receipt_payment['net_banking_ref_no'] :NULL),
                                'payment_amount'      =>$receipt_payment['cash_amount'],
                                'payment_date'        =>date("Y-m-d H:i:s"),
                            );
                            $this->$model->insertData($payData,'ret_old_metal_process_payment');
		 		        }
		 		        
		 		    }
                		 		    
                    echo json_encode($responseData); 
                }
            
            break;
		
			case 'ajax': 
					$list=$this->$model->ajax_get_metal_process($_POST); 
				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_purchase/list');
			        $data = array(
		        					'list'  => $list,
									'access'=> $access
		        				 );  
					echo json_encode($data);
				break;
		}
    }
    
    function process_acknowladgement($id)
	{
		$model=	self::RET_PROCESS_MODEL;
		$data['process'] = $this->$model->get_metal_process($id);
        $data['comp_details']=$this->admin_settings_model->getCompanyDetails("");
		if($data['process']['id_metal_process']==1)
		{
		    if($data['process']['process_for']==1)
		    {
		        $data['melting_details'] = $this->$model->get_melting_issue_details($id);
		    }else
		    {
		        $data['melting_details'] = $this->$model->get_melting_receipt_details($id);
		    }
		    
		}
		
		if($data['process']['id_metal_process']==2)
		{
		    if($data['process']['process_for']==1)
		    {
		        $data['melting_details'] = $this->$model->get_testing_issue_details($id);
		    }
		    else
		    {
		        $data['melting_details'] = $this->$model->get_TestingReceiptAcknowladgement($id);
		    }
		}
		
		if($data['process']['id_metal_process']==3)
		{
		    if($data['process']['process_for']==1)
		    {
		        $data['melting_details'] = $this->$model->get_RefiningIssueAcknowladgement($id);
		    }
		    else
		    {
		        $data['melting_details'] = $this->$model->get_refiningReceiptAcknowladgement($id);
		    }
		}
		
		if($data['process']['id_metal_process']==4)
		{
		    if($data['process']['process_for']==1)
		    {
		        $data['polishing_details'] = $this->$model->get_PolishingIssueAcknowladgement($id);
		    }
		    else
		    {
		        $data['polishing_details'] = $this->$model->get_PolishingReceiptAcknowladgement($id);
		    }
		}
		
		$data['process_payment'] = $this->$model->get_old_metal_process_payment($id);
		//echo "<pre>";print_r($data);exit;
		$this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
		$html = $this->load->view('ret_metal_process/metal_process/process_acknowladgement', $data,true);
	    $dompdf->load_html($html);
		$dompdf->set_paper("a4", "portriat" );
		$dompdf->render();
		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("Receipt.pdf",array('Attachment'=>0));
	}
    
    function metal_process_receipt($type="")
	{   
       
		$model=	self::RET_PROCESS_MODEL;
		switch($type)
		{
			case 'list': 
					$data['main_content'] = self::VIEW_FOLDER.'receipt/list';
        			$this->load->view('layout/template', $data);
			break;
            case 'add': 
                $data['main_content'] = self::VIEW_FOLDER.'receipt/form';
                $this->load->view('layout/template', $data);
            break;
            case 'metal_list': 
                $data=$this->$model->get_metal_stock_list($_POST);
                echo json_encode($data);
            break;
		
			case 'ajax': 
					$list=$this->$model->get_pocket_list($_POST); 
				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_purchase/list');
			        $data = array(
		        					'list'  => $list,
									'access'=> $access
		        				 );  
					echo json_encode($data);
				break;
		}
    }
    
    function get_pocket_details()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_pocket_details($_POST);
        echo json_encode($data);
    }
    
    function get_polish_pocket_details()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_polish_pocket();
        echo json_encode($data);
    }
    
    
    
    //melting receipt
    function get_KarigarMeltingIssueDetilas()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_KarigarMeltingIssueDetilas($_POST);
        echo json_encode($data);
    }
    //melting receipt
    
    
    //Testing Issue
    function get_testing_issue_details()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_melting_details($_POST);
        echo json_encode($data);
    }
    
    function get_testing_receipt_details()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_testing_receipt_details($_POST);
        echo json_encode($data);
    }
    //Testing Issue
    
    //Refining Issue
    function get_RefiningIssueDetails()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_RefiningIssueDetails();
        echo json_encode($data);
    }
    
    function get_RefiningReceiptDetails()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_RefiningReceiptDetails($_POST);
        echo json_encode($data);
    }
    function get_Active_Refining()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_Active_Refining_details($_POST);
        echo json_encode($data);
    }
    
    //Refining Issue
    
    //Polishing
    function get_PolishingReceiptDetails()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_PolishingReceiptDetails($_POST);
        echo json_encode($data);
    }
    //Polishing
    
    
    function get_ActiveCategoryPurity()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_ActiveCategoryPurity();
        echo json_encode($data);
    }
    
    
    
    function process_report($type="",$id="")
	{   
       
		$model=	self::RET_PROCESS_MODEL;
		switch($type)
		{
			case 'list': 
					$data['main_content'] = self::VIEW_FOLDER.'reports/process_report';
        			$this->load->view('layout/template', $data);
			break;
			
			case 'detailed_report': 
					$data['main_content'] = self::VIEW_FOLDER.'reports/detailed_report';
        			$this->load->view('layout/template', $data);
			break;
			
			case 'detail_porcess_report': 
					$data=$this->$model->get_detail_porcess_report($_POST); 
					echo json_encode($data);
			break;
			
			case 'process_details': 
					$data=$this->$model->get_process_pocket_details($_POST); 
					echo json_encode($data);
			break;
         
			case 'ajax': 
					$list=$this->$model->get_metal_process_reports($_POST); 
				  	$access = $this->admin_settings_model->get_access('admin_ret_metal_process/process_report/list');
			        $data = array(
		        					'list'  => $list,
									'access'=> $access
		        				 );  
					echo json_encode($data);
				break;
		}
	    
    }
    
    
    function get_active_design_products()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_active_design_products();
        echo json_encode($data);
    }
    
    function get_active_sub_design_products()
    {
        $model=	self::RET_PROCESS_MODEL;
        $data=$this->$model->get_active_sub_design_products();
        echo json_encode($data);
    }


    function get_opening_metal_stock_list()

    {

        $model=	self::RET_PROCESS_MODEL;

        $data=$this->$model->get_opening_metal_stock_list($_POST);

        echo json_encode($data);

    }

    function get_chg_tax_type(){

        $model=	self::RET_PROCESS_MODEL;

        $karigar_state = $this->$model->get_karigar_state($_POST);

        $data['comp_details']=$this->admin_settings_model->getCompanyDetails("");
        
        $company_state =$data['comp_details'][0]['id_company'] ;

        $tax_type = 0;
        if($karigar_state == $company_state){
            $tax_type = 1;
        }
        echo $tax_type;
    }
    

    function old_metal_pnl_report($type="")
    {   
        $model = self::RET_PROCESS_MODEL;
        switch($type)
        {
            case 'list': 
                    $data['main_content'] = self::VIEW_FOLDER.'reports/old_metal_pnl_report';
                    $this->load->view('layout/template', $data);
            break;
            
            case 'ajax': 
                    $list = $this->$model->get_old_metal_pnl_data($_POST); 
                    $access = $this->admin_settings_model->get_access('admin_ret_metal_process/old_metal_pnl_report/list');
                    $data = array(
                                    'list'  => $list,
                                    'access'=> $access
                                 );  
                    echo json_encode($data);
                break;
        }
    }
    
    function get_repair_pending_order(){
        $model=	self::RET_PROCESS_MODEL;
        $data= $this->$model->get_repair_pending_order($_POST);

        echo json_encode($data);
    }
    

}
?>