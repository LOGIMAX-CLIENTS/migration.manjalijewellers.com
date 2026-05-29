<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_ret_services extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("ret_services_model");
		$this->load->model("ret_tag_parts_model");
		$this->date = date('Y-m-d');
		$this->emp_id = ($this->session->userdata("uid") ? $this->session->userdata("uid") : NULL);
		ini_set('date.timezone', 'Asia/Calcutta');
	}
	
	function findArrayIndex($array, $searchKey, $searchVal) {
        foreach ($array as $k => $val) { 
            if ($val[$searchKey] == $searchVal) {
               return $k;
            }
        }
        return -1;
    }
	
	public function php_mail($email_to,$email_subject,$email_message,$email_cc,$email_bcc,$attachment="") 		{ 
		 $config = array();
                $config['useragent']     = "CodeIgniter";
                $config['mailpath']      = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
                $config['protocol']      = "smtp";
                $config['smtp_host']     = "localhost";
                $config['smtp_port']     = "25";
                $config['mailtype']		 = 'html';
                $config['charset'] 		 = 'utf-8';
                $config['newline'] 		 = "\r\n";
                $config['wordwrap']		 = TRUE;
                $this->load->library('email');
                $this->email->initialize($config);
                $this->email->from('noreply@logimax.co.in', 'Retail');
                $this->email->to($email_to); 
				if($email_cc!="")
				{
					$this->email->cc($email_cc); 
				}
             
     			if($email_bcc!="")
				{
                   $this->email->bcc($email_bcc); 
			    }
                $this->email->subject($email_subject);       
            	$this->email->message($email_message);
            	   
           return $this->email->send();           
			 
	}
	
	function sendEmail($subject,$message){
		$this->load->model('email_model');
		$bcc = "";
		$sendEmail = $this->php_mail('karthik@vikashinfosolutions.com',$subject,$message,'',$bcc);
		//echo 1;exit;
		return true;
	}
	
	public function partly_sold($type="",$id_branch="")
	{					  
		$insIds = array(); 
		$id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ;
		$data = $this->ret_services_model->getPartlySold($this->date,$id_branch); 
		$this->db->trans_begin();
		foreach($data as $d){
			$ins_data = array(
						"report_date"		=>	$this->date,
						"id_branch"			=>	$d['id_branch'],
						"tag_id"			=>	$d['tag_id'],
						"product"			=>	$d['product_id'],
						"design"			=>	$d['design_id'],
						"actual_gross_wt"	=>	$d['act_gross_wt'],
						"actual_less_wt"	=>	$d['act_less_wt'],
						"actual_net_wt"		=>	$d['act_net_wt'],
						"sold_gross_wt"		=>	$d['sold_gross_wt'],
						"sold_less_wt"		=>	$d['sold_less_wt'],
						"sold_net_wt"		=>	$d['sold_net_wt'],
						"created_on"		=>	date('Y-m-d H:i:s'), 
						);
			
			$ins_id = $this->ret_services_model->insertData($ins_data,"ret_rep_partlysale");
			array_push($insIds,$ins_id);
		} 
		
		$dc_log_data = array(
					"id_branch"		=>	$id_branch,  
					"type"			=>	isset($POST['type'])?$POST['type']:1,
					"date"			=>	$this->date,
					"service"		=>	"partly_sold",
					"records"       =>  sizeof($insIds),
					"created_by"	=>	$this->emp_id,
					"created_on"	=>	date('Y-m-d H:i:s'), 
					);
		$this->ret_services_model->insertData($dc_log_data,"ret_day_closing_log");
		
		if($this->db->trans_status() === TRUE){
			$this->db->trans_commit();
			$result = array('status'=>TRUE,'message'=>'Service partly_sold : Executes Successfully.'.json_encode($insIds));
		}else{
			$this->db->trans_rollback();
			$result = array('status'=>TRUE,'message'=>"Service partly_sold : Error in executing partly_sold services ".sizeof($insIds),'error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query()); 
			$this->sendEmail("Service : partly_sold - Error",json_encode($result));
		}
		if($type == "autoDC"){
		    return $result;
		}else{
		    echo json_encode($result);
		}
		
	}
	
	function stock_balance($type="",$id_branch="")
    {
        $items = array(); 
        $id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ; 
    	$items = $this->ret_services_model->stock_balance($id_branch,$this->date);
    	//echo $this->db->last_query();    	echo "<pre>";print_r($items);exit;
    	$this->db->trans_begin();
    	$insIds = array();
    	if(sizeof($items)>0)
    	{
    	    
    		foreach($items as $item)
    		{
    			$insData = array(
	    			'id_product' =>$item['product_id'],
	    			'type'       =>1, // Tag item
	    			'date'       =>$this->date,
	    			//'date'       =>'2022-06-13',
	    			'id_branch'  =>$item['current_branch'],
	    			'op_blc_gwt' =>$item['op_blc_gwt'],
	    			'op_blc_nwt' =>$item['op_blc_nwt'],
	    			'op_blc_pcs' =>$item['op_blc_pcs'],
	    			'inw_gwt'	 =>$item['inw_gwt'],
	    			'inw_nwt'	 =>$item['inw_nwt'],
	    			'inw_pcs'	 =>$item['inw_pcs'],
	    			'sold_gwt'	 =>$item['sold_gwt']+$item['br_out_gwt'],
	    			'sold_nwt'	 =>$item['sold_nwt']+$item['br_out_nwt'],
	    			'sold_pcs'	 =>$item['sold_pcs']+$item['br_out_pcs'],
	    			'closing_pcs'=>$item['op_blc_pcs']+$item['inw_pcs']-$item['sold_pcs']-$item['br_out_pcs'],
	    			'closing_gwt'=>$item['op_blc_gwt']+$item['inw_gwt']-$item['sold_gwt']-$item['br_out_gwt'],
	    			'closing_nwt'=>$item['op_blc_nwt']+$item['inw_nwt']-$item['sold_nwt']-$item['br_out_nwt'],
	    			'date_add'	 =>date("Y-m-d H:i:s")
    			);
    			$ins_id = $this->ret_services_model->insertData($insData,'ret_stock_balance');
    			
    			array_push($insIds,$ins_id);
    		}
    		
			$dc_log_data = array(
						"id_branch"		=>	$id_branch,  
						"type"			=>	isset($POST['type'])?$POST['type']:1,
						"date"			=>	$this->date,
						"service"		=>	"stock_balance",
						"records"       =>  sizeof($insIds),
						"created_by"	=>	$this->emp_id,
						"created_on"	=>	date('Y-m-d H:i:s'), 
						);
			$this->ret_services_model->insertData($dc_log_data,"ret_day_closing_log");
		}
    	
		else{
			$data=array('status'=>FALSE,'message'=>'No Records To Update.');
		}
    	if($this->db->trans_status()===TRUE)
		{
			$data=array('status'=>TRUE,'message'=>'Stock Balance Updated Successfully.'.json_encode($insIds));
			$this->db->trans_commit();
		}
		else
		{ 
			$data=array('status'=>FALSE,'message'=>'Service stock_balance : Error in executing stock_balance services','error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query());
			$this->db->trans_rollback();
			$this->sendEmail("Service : stock_balance - Error",json_encode($data));
		}
	    if($type == "autoDC"){
		    return $data;
		}else{
		    echo json_encode($data);
		}
    }
    
    function stock_balance_nontag($type="",$id_branch="")
    {
        $nt_items = array(); 
        $id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ; 
    	$nt_items = $this->ret_services_model->stock_balance_nontag($id_branch,$this->date); 
    	$nt_insIds = array(); 
    	if(sizeof($nt_items)>0)
    	{
    		foreach($nt_items as $item)
    		{
    			$insData = array(
	    			'id_product' =>$item['product_id'],
	    			'type'       =>2, // Non Tag
	    			'date'       =>$this->date,
	    			//'date'       =>'2022-05-23',
	    			'id_branch'  =>$id_branch,
	    			'op_blc_gwt' =>$item['op_blc_gwt'],
	    			'op_blc_nwt' =>$item['op_blc_nwt'],
	    			'op_blc_pcs' =>$item['op_blc_pcs'],
	    			'inw_gwt'	 =>$item['inw_gwt'],
	    			'inw_nwt'	 =>$item['inw_nwt'],
	    			'inw_pcs'	 =>$item['inw_pcs'],
	    			'sold_gwt'	 =>$item['out_gwt']+$item['sales_gwt'],
	    			'sold_nwt'	 =>$item['out_nwt']+$item['sales_nwt'],
	    			'sold_pcs'	 =>$item['out_pcs']+$item['sales_pcs'],
	    			'closing_pcs'=>$item['op_blc_pcs']+$item['inw_pcs']-$item['out_pcs']-$item['sales_pcs'],
	    			'closing_gwt'=>$item['op_blc_gwt']+$item['inw_gwt']-$item['out_gwt']-$item['sales_gwt'],
	    			'closing_nwt'=>$item['op_blc_nwt']+$item['inw_nwt']-$item['out_nwt']-$item['sales_nwt'],
	    			'date_add'	 =>date("Y-m-d H:i:s")
    			);
    			$ins_id = $this->ret_services_model->insertData($insData,'ret_stock_balance');
    			array_push($nt_insIds,$ins_id);
    		} 
		}
    	
		else{
			$data=array('status'=>FALSE,'message'=>'No Records To Update.');
		}
    	if($this->db->trans_status()===TRUE)
		{
			$data=array('status'=>TRUE,'message'=>'Stock Balance Updated Successfully.'.json_encode($nt_insIds));
			$this->db->trans_commit();
		}
		else
		{ 
			$data=array('status'=>FALSE,'message'=>'Service stock_balance : Error in executing stock_balance_non_tag services','error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query());
			$this->db->trans_rollback();
			$this->sendEmail("Service : stock_balance_non_tag - Error",json_encode($data));
		}
	    if($type == "autoDC"){
		    return $data;
		}else{
		    echo json_encode($data);
		}
    }
    
    public function dayClose()
	{					  
		$insIds = array(); 
		$this->db->trans_begin();
		$id_branch = $this->session->userdata("id_branch");
		//print_r($id_branch);exit;
		if($id_branch >0){
			$isDayClosed = $this->ret_services_model->isDayClosed($id_branch); 
	    	if($isDayClosed){
				$result = array('status'=>FALSE,'message'=>"Already Day closed.."); 
			}else{
				$updData = array(
						'is_day_closed'	=>	1,
						"entry_date"	=>	date('Y-m-d',strtotime("+1 days")),
						"updated_on"	=>	date('Y-m-d H:i:s'), 
						); 
				$status = $this->ret_services_model->updateData($updData,'id_branch',$id_branch,'ret_day_closing'); 
				if($status)
				{
				    $this->stock_balance('autoDC',$id_branch);
        			$this->stock_balance_nontag('autoDC',$id_branch);
					// Tag Split EOD Revert
					$this->ret_tag_parts_model->process_eod_reverts($id_branch, null, $this->emp_id);
				}
				if($this->db->trans_status() === TRUE){
					$this->db->trans_commit();
					$result = array('status'=>TRUE,'message'=>'Day closed successfully');
				}else{
					$this->db->trans_rollback();
					$result = array('status'=>FALSE,'message'=>"Error in Day close..",'error_msg'=>$this->db->_error_message()); 
				}
			}
		}else{
			$result = array('status'=>FALSE,'message'=>"Can do day close branch by branch only.."); 
		}
		echo json_encode($result);
	}
	
	// Reset Day Close :: Update is_day_closed as 0
    function resetDayClose()
    {
    	$this->db->trans_begin();
    	$updData = array(    
			    		'is_day_closed'	=>	0,
			    		'updated_on'	=>	date("Y-m-d H:i:s")
			 			);
		$this->ret_services_model->updateData($updData,'is_day_closed',1,'ret_day_closing');
		if($this->db->trans_status()===TRUE)
		{
			$data = array('status' => TRUE,'message' => 'Day Close Resetted Successfully.');
			$this->db->trans_commit();
		}
		else
		{
			$data = array('status' => FALSE,'message' => 'Unable to Reset Day Close','error_msg' => $this->db->_error_message());
			$this->db->trans_rollback();
		}
	    echo json_encode($data);
    }
    
    /*function checkautoDayClose()
    {
        $date1 = DateTime::createFromFormat('h:i a', date("h:i a"));
        $date2 = DateTime::createFromFormat('h:i a', "11:30 pm");
        $date3 = DateTime::createFromFormat('h:i a', "11:59 pm");
        if ($date1 > $date2 && $date1 < $date3)
        {
            $result[] = array('status'=>true); 
        }
        else{
            $result[] = array('status'=>FALSE,'message'=>"Time Mismatch. autoDayClose Service has to be executed between 11:30 - 11:59 PM.."); 
            //$this->sendEmail("Service : Execution Time Mismatch",json_encode($result));
        }
		echo json_encode($result);
    }*/
    
    // Automatically Close Day if not done manually.
    function autoDayClose()
    {
        /*$date1 = DateTime::createFromFormat('h:i a', date("h:i a"));
        $date2 = DateTime::createFromFormat('h:i a', "11:30 pm");
        $date3 = DateTime::createFromFormat('h:i a', "11:59 pm");
        if ($date1 > $date2 && $date1 < $date3)
        {*/
        	$insIds = array(); 
    		$this->db->trans_begin();
    		$branches = $this->ret_services_model->activeBranches();
    		foreach($branches as $branch){ 
        		if($branch['id_branch'] > 0){
        			$isDayClosed = $this->ret_services_model->isDayClosed($branch['id_branch']); 
        	    	if($isDayClosed){
        				$result[] = array('status'=>FALSE,'Branch'=>$branch['branch_name'],'message'=>"Already Day closed.."); 
        			}else{
        				$updData = array(
        						'is_day_closed'	=>	1,
        						"entry_date"	=>	date('Y-m-d',strtotime("+1 days")),
        						"updated_on"	=>	date('Y-m-d H:i:s'), 
        						); 
        				$this->ret_services_model->updateData($updData,'id_branch',$branch['id_branch'],'ret_day_closing'); 
        				if($this->db->trans_status() === TRUE){
        				    $this->db->trans_commit();
        					$this->stock_balance('autoDC',$branch['id_branch']);
        					$this->stock_balance_nontag('autoDC',$branch['id_branch']);
							// Tag Split EOD Revert
							$this->ret_tag_parts_model->process_eod_reverts($branch['id_branch'], null, 0);
        					//$this->stock_balance_packaging_items('autoDC',$branch['id_branch']);
        					/*$this->old_metal_stock_balance('autoDC',$branch['id_branch']);
        					$this->old_metal_process_stock_balance('autoDC',$branch['id_branch']);
    					    $this->sales_return_stock_balance('autoDC',$branch['id_branch']);
    					    $this->partly_sale_stock_balance('autoDC',$branch['id_branch']);
    					    $this->bullion_purchase_stock_balance('autoDC',$branch['id_branch']);*/
        					$result[] = array('status'=>TRUE,'message'=>'Day closed successfully');
        				
        				}else{
        					$this->db->trans_rollback();
        					$result[] = array('status'=>FALSE,'message'=>"Error in Day close..",'error_msg'=>$this->db->_error_message()); 
        					$this->sendEmail("Service : autoDayClose - Error. Branch : ".$branch['branch_name'],json_encode($result));
        				}
        			}
        		}else{
        			$result[] = array('status'=>FALSE,'message'=>"Can do day close branch by branch only.."); 
        		}
    		} 
        /*}
        else{
            $result[] = array('status'=>FALSE,'message'=>"Time Mismatch. autoDayClose Service has to be executed between 11:30 - 11:59 PM.."); 
            $this->sendEmail("Service : Execution Time Mismatch",json_encode($result));
        }*/
		echo json_encode($result);
    }
    
    function stock_balance_old($type="",$id_branch="")
    {
        
        $items = array(); 
        $id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ; 
    	$items = $this->ret_services_model->stock_balance_old($id_branch,"2022-03-31");
    	$this->db->trans_begin();
    	$insIds = array();
        
    	if(sizeof($items)>0)
    	{
    		foreach($items as $item)
    		{
    			$insData = array(
	    			'id_product' =>$item['product_id'],
	    			'date'       =>"2022-03-31",
	    			'id_branch'  =>$item['current_branch'],
	    			'op_blc_gwt' =>$item['op_blc_gwt'],
	    			'op_blc_nwt' =>$item['op_blc_nwt'],
	    			'op_blc_pcs' =>$item['op_blc_pcs'],
	    			'inw_gwt'	 =>$item['inw_gwt'],
	    			'inw_nwt'	 =>$item['inw_nwt'],
	    			'inw_pcs'	 =>$item['inw_pcs'],
	    			'sold_gwt'	 =>$item['sold_gwt'],
	    			'sold_nwt'	 =>$item['sold_nwt'],
	    			'sold_pcs'	 =>$item['sold_pcs'],
	    			'closing_pcs'=>$item['op_blc_pcs']-$item['sold_pcs'],
	    			'closing_gwt'=>$item['op_blc_gwt']-$item['sold_gwt'],
	    			'closing_nwt'=>$item['op_blc_nwt']-$item['sold_nwt'],
	    			'date_add'	 =>date("Y-m-d H:i:s")
    			);
    			//echo"<pre>"; print_r($insData);exit;
    			$ins_id = $this->ret_services_model->insertData($insData,'ret_stock_balance');
    			//print_r($this->db->last_query());exit;
    			array_push($insIds,$ins_id);
    		}
    		
			$dc_log_data = array(
						"id_branch"		=>	$id_branch,  
						"type"			=>	isset($POST['type'])?$POST['type']:1,
						"date"			=>	$this->date,
						"service"		=>	"stock_balance",
						"records"       =>  sizeof($insIds),
						"created_by"	=>	$this->emp_id,
						"created_on"	=>	date('Y-m-d H:i:s'), 
						);
			//$this->ret_services_model->insertData($dc_log_data,"ret_day_closing_log");
		}
    	
		else{
			$data=array('status'=>FALSE,'message'=>'No Records To Update.');
		}
    	if($this->db->trans_status()===TRUE)
		{
			$data=array('status'=>TRUE,'message'=>'Stock Balance Updated Successfully.'.json_encode($insIds));
			$this->db->trans_commit();
		}
		else
		{ 
			$data=array('status'=>FALSE,'message'=>'Service stock_balance : Error in executing stock_balance services','error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query());
			$this->db->trans_rollback();
			$this->sendEmail("Service : stock_balance - Error",json_encode($data));
		}
	    if($type == "autoDC"){
		    return $data;
		}else{
		    echo json_encode($data);
		}
    }
    
    
    //Old Metal Stock Balance
    
    function old_metal_stock_balance($type="",$id_branch="")
    {
        $items = array(); 
        $id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ; 
    	$items = $this->ret_services_model->old_metal_stock_balance($id_branch,$this->date);
        //echo "<pre>";print_r($items);exit;
    	$this->db->trans_begin();
    	$insIds = array();
    	if(sizeof($items)>0)
    	{
    	    
    		foreach($items as $item)
    		{
    			$insData = array(
	    			'id_old_metal_type' =>$item['id_metal_type'],
	    			'stock_type'        =>0, // Old Metal
	    			'id_branch'         =>$id_branch,
	    			'date'              =>$this->date,
	    			'op_blc_gwt'        =>$item['op_blc_gwt'],
	    			'op_blc_nwt'        =>$item['op_blc_nwt'],
	    			'inw_gwt'           =>$item['inw_gwt'],
	    			'inw_nwt'           =>$item['inw_nwt'],
	    			'outward_gwt'       =>$item['br_out_gwt'],
	    			'outward_nwt'       =>$item['br_out_nwt'],
	    			'closing_gwt'       =>($item['op_blc_gwt']+$item['inw_gwt']-$item['br_out_gwt']),
	    			'closing_nwt'       =>($item['op_blc_nwt']+$item['inw_nwt']-$item['br_out_nwt']),
	    			'date_add'	        =>date("Y-m-d H:i:s"),
    			);
    			$ins_id = $this->ret_services_model->insertData($insData,'ret_purchase_item_stock');
    			
    			array_push($insIds,$ins_id);
    		}
    		
                $dc_log_data = array(
                "id_branch"		=>	$id_branch,  
                "type"			=>	isset($POST['type'])?$POST['type']:1,
                "date"			=>	$this->date,
                "service"		=>	"Old_metal_stock_balance",
                "records"       =>  sizeof($insIds),
                "created_by"	=>	$this->emp_id,
                "created_on"	=>	date('Y-m-d H:i:s'), 
                );
                //$this->ret_services_model->insertData($dc_log_data,"ret_day_closing_log");
		}
    	
		else{
			$data=array('status'=>FALSE,'message'=>'No Records To Update.');
		}
    	if($this->db->trans_status()===TRUE)
		{
			$data=array('status'=>TRUE,'message'=>'Old Metal Stock Balance Updated Successfully.'.json_encode($insIds));
			$this->db->trans_commit();
		}
		else
		{ 
			$data=array('status'=>FALSE,'message'=>'Service stock_balance : Error in executing Old Metal stock_balance services','error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query());
			$this->db->trans_rollback();
			$this->sendEmail("Service : stock_balance - Error",json_encode($data));
		}
	    if($type == "autoDC"){
		    return $data;
		}else{
		    echo json_encode($data);
		}
    }
    
    //Old Metal Stock Balance
    
    
    //OLD METAL PROCESS
    //Old Metal Stock Balance
    
    function old_metal_process_stock_balance($type="",$id_branch="")
    {
        $items = array(); 
        $id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ; 
    	$items = $this->ret_services_model->old_metal_process_stock_balance($id_branch,$this->date);
        //echo "<pre>";print_r($items);exit;
    	$this->db->trans_begin();
    	$insIds = array();
    	if(sizeof($items)>0)
    	{
    	    
    		foreach($items as $item)
    		{
    			$insData = array(
	    			'id_ret_category'   =>$item['id_ret_category'],
	    			'stock_type'        =>4, // Old Metal Process
	    			'id_branch'         =>$id_branch,
	    			'date'              =>$this->date,
	    			//'date'              =>'2022-03-11',
	    			'op_blc_gwt'        =>$item['op_blc_gwt'],
	    			'op_blc_nwt'        =>$item['op_blc_nwt'],
	    			'inw_gwt'           =>$item['inw_nwt'],
	    			'inw_nwt'           =>$item['inw_nwt'],
	    			'outward_gwt'       =>$item['out_ward_nwt'],
	    			'outward_nwt'       =>$item['out_ward_nwt'],
	    			'closing_gwt'       =>($item['op_blc_gwt']+$item['inw_nwt']-$item['out_ward_nwt']),
	    			'closing_nwt'       =>($item['op_blc_nwt']+$item['inw_nwt']-$item['out_ward_nwt']),
	    			'date_add'	        =>date("Y-m-d H:i:s"),
    			);
    			$ins_id = $this->ret_services_model->insertData($insData,'ret_purchase_item_stock');
    			
    			array_push($insIds,$ins_id);
    		}
    		
                $dc_log_data = array(
                "id_branch"		=>	$id_branch,  
                "type"			=>	isset($POST['type'])?$POST['type']:1,
                "date"			=>	$this->date,
                "service"		=>	"Old_metal_stock_balance",
                "records"       =>  sizeof($insIds),
                "created_by"	=>	$this->emp_id,
                "created_on"	=>	date('Y-m-d H:i:s'), 
                );
                //$this->ret_services_model->insertData($dc_log_data,"ret_day_closing_log");
		}
    	
		else{
			$data=array('status'=>FALSE,'message'=>'No Records To Update.');
		}
    	if($this->db->trans_status()===TRUE)
		{
			$data=array('status'=>TRUE,'message'=>'Old Metal Stock Balance Updated Successfully.'.json_encode($insIds));
			$this->db->trans_commit();
		}
		else
		{ 
			$data=array('status'=>FALSE,'message'=>'Service stock_balance : Error in executing Old Metal stock_balance services','error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query());
			$this->db->trans_rollback();
			$this->sendEmail("Service : stock_balance - Error",json_encode($data));
		}
	    if($type == "autoDC"){
		    return $data;
		}else{
		    echo json_encode($data);
		}
    }
    //OLD METAL PROCESS
    
    //Sales Return Stock Balance
    function sales_return_stock_balance($type="",$id_branch="")
    {
        $items = array(); 
        $id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ; 
    	$items = $this->ret_services_model->sales_return_stock_balance($id_branch,$this->date);
        //echo "<pre>";print_r($items);exit;
    	$this->db->trans_begin();
    	$insIds = array();
    	if(sizeof($items)>0)
    	{
    	    
    		foreach($items as $item)
    		{
    			$insData = array(
	    			'id_product'        =>$item['pro_id'],
	    			'stock_type'        =>2, // Sales Return
	    			'id_branch'         =>$id_branch,
	    			'date'              =>$this->date,
	    			//'date'              =>'2022-03-11',
	    			'op_blc_pcs'        =>$item['op_blc_pcs'],
	    			'op_blc_gwt'        =>$item['op_blc_gwt'],
	    			'op_blc_nwt'        =>$item['op_blc_nwt'],
	    			'inw_pcs'           =>$item['inw_pcs'],
	    			'inw_gwt'           =>$item['inw_gwt'],
	    			'inw_nwt'           =>$item['inw_nwt'],
	    			'out_ward_pcs'      =>$item['br_out_pcs'],
	    			'outward_gwt'       =>$item['br_out_gwt'],
	    			'outward_nwt'       =>$item['br_out_nwt'],
	    			'closing_pcs'       =>($item['op_blc_pcs']+$item['inw_pcs']-$item['br_out_pcs']),
	    			'closing_gwt'       =>($item['op_blc_gwt']+$item['inw_gwt']-$item['br_out_gwt']),
	    			'closing_nwt'       =>($item['op_blc_nwt']+$item['inw_nwt']-$item['br_out_nwt']),
	    			'date_add'	        =>date("Y-m-d H:i:s"),
    			);
    			$ins_id = $this->ret_services_model->insertData($insData,'ret_purchase_item_stock');
    			
    			array_push($insIds,$ins_id);
    		}
    		
			$dc_log_data = array(
						"id_branch"		=>	$id_branch,  
						"type"			=>	isset($POST['type'])?$POST['type']:1,
						"date"			=>	$this->date,
						"service"		=>	"sales_return_stock_balance",
						"records"       =>  sizeof($insIds),
						"created_by"	=>	$this->emp_id,
						"created_on"	=>	date('Y-m-d H:i:s'), 
						);
			//$this->ret_services_model->insertData($dc_log_data,"ret_day_closing_log");
		}
    	
		else{
			$data=array('status'=>FALSE,'message'=>'No Records To Update.');
		}
    	if($this->db->trans_status()===TRUE)
		{
			$data=array('status'=>TRUE,'message'=>'Old Metal Stock Balance Updated Successfully.'.json_encode($insIds));
			$this->db->trans_commit();
		}
		else
		{ 
			$data=array('status'=>FALSE,'message'=>'Service stock_balance : Error in executing Old Metal stock_balance services','error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query());
			$this->db->trans_rollback();
			$this->sendEmail("Service : stock_balance - Error",json_encode($data));
		}
	    if($type == "autoDC"){
		    return $data;
		}else{
		    echo json_encode($data);
		}
    }
    
    //Sales Return Stock Balance
    
    
    //Partly Sale
    
    function partly_sale_stock_balance($type="",$id_branch="")
    {
        $items = array(); 
        $id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ; 
    	$items = $this->ret_services_model->partly_sale_stock_balance($id_branch,$this->date);
        //echo "<pre>";print_r($items);exit;
    	$this->db->trans_begin();
    	$insIds = array();
    	if(sizeof($items)>0)
    	{
    	    
    		foreach($items as $item)
    		{
    			$insData = array(
	    			'id_product'        =>$item['pro_id'],
	    			'stock_type'        =>3, // Partly Sale
	    			'id_branch'         =>$id_branch,
	    			'date'              =>$this->date,
	    		//	'date'              =>$this->date,
	    			'op_blc_gwt'        =>$item['op_blc_gwt'],
	    			'op_blc_nwt'        =>$item['op_blc_nwt'],
	    			'inw_gwt'           =>$item['inw_gwt'],
	    			'inw_nwt'           =>$item['inw_nwt'],
	    			'outward_gwt'       =>$item['br_out_gwt'],
	    			'outward_nwt'       =>$item['br_out_nwt'],
	    			'closing_gwt'       =>($item['op_blc_gwt']+$item['inw_gwt']-$item['br_out_gwt']),
	    			'closing_nwt'       =>($item['op_blc_nwt']+$item['inw_nwt']-$item['br_out_nwt']),
	    			'date_add'	        =>date("Y-m-d H:i:s"),
    			);
    			$ins_id = $this->ret_services_model->insertData($insData,'ret_purchase_item_stock');
    			array_push($insIds,$ins_id);
    		}
    		
                $dc_log_data = array(
                "id_branch"		=>	$id_branch,  
                "type"			=>	isset($POST['type'])?$POST['type']:1,
                "date"			=>	$this->date,
                "service"		=>	"partly_sale_stock_balance",
                "records"       =>  sizeof($insIds),
                "created_by"	=>	$this->emp_id,
                "created_on"	=>	date('Y-m-d H:i:s'), 
                );
                //$this->ret_services_model->insertData($dc_log_data,"ret_day_closing_log");
		}
    	
		else{
			$data=array('status'=>FALSE,'message'=>'No Records To Update.');
		}
    	if($this->db->trans_status()===TRUE)
		{
			$data=array('status'=>TRUE,'message'=>'Old Metal Stock Balance Updated Successfully.'.json_encode($insIds));
			$this->db->trans_commit();
		}
		else
		{ 
			$data=array('status'=>FALSE,'message'=>'Service stock_balance : Error in executing Old Metal stock_balance services','error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query());
			$this->db->trans_rollback();
			$this->sendEmail("Service : stock_balance - Error",json_encode($data));
		}
	    if($type == "autoDC"){
		    return $data;
		}else{
		    echo json_encode($data);
		}
    }
    
   //Partly Sale
   
   
   //Bullion Purchase
    
    function bullion_purchase_stock_balance($type="",$id_branch="")
    {
        $items = array(); 
        $id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ; 
    	$items = $this->ret_services_model->bullion_purchase_stock_balance($id_branch,$this->date);
        //echo "<pre>";print_r($items);exit;
    	$this->db->trans_begin();
    	$insIds = array();
    	if(sizeof($items)>0)
    	{
    	    
    		foreach($items as $item)
    		{
    			$insData = array(
	    			'id_product'        =>$item['pro_id'],
	    			'stock_type'        =>1, // Bullion Purchase Stock Balance
	    			'id_branch'         =>$id_branch,
	    			//'date'              =>'2022-04-04',
	    			'date'              =>$this->date,
	    			'op_blc_gwt'        =>$item['op_blc_gwt'],
	    			'op_blc_nwt'        =>$item['op_blc_nwt'],
	    			'inw_gwt'           =>$item['inw_gwt'],
	    			'inw_nwt'           =>$item['inw_nwt'],
	    			'outward_gwt'       =>$item['br_out_gwt'],
	    			'outward_nwt'       =>$item['br_out_nwt'],
	    			'closing_gwt'       =>($item['op_blc_gwt']+$item['inw_gwt']-$item['br_out_gwt']),
	    			'closing_nwt'       =>($item['op_blc_nwt']+$item['inw_nwt']-$item['br_out_nwt']),
	    			'date_add'	        =>date("Y-m-d H:i:s"),
    			);
    			$ins_id = $this->ret_services_model->insertData($insData,'ret_purchase_item_stock');
    			//print_r($this->db->last_query());exit;
    			array_push($insIds,$ins_id);
    		}
    		
                $dc_log_data = array(
                "id_branch"		=>	$id_branch,  
                "type"			=>	isset($POST['type'])?$POST['type']:1,
                "date"			=>	$this->date,
                "service"		=>	"partly_sale_stock_balance",
                "records"       =>  sizeof($insIds),
                "created_by"	=>	$this->emp_id,
                "created_on"	=>	date('Y-m-d H:i:s'), 
                );
                //$this->ret_services_model->insertData($dc_log_data,"ret_day_closing_log");
		}
    	
		else{
			$data=array('status'=>FALSE,'message'=>'No Records To Update.');
		}
    	if($this->db->trans_status()===TRUE)
		{
			$data=array('status'=>TRUE,'message'=>'Old Metal Stock Balance Updated Successfully.'.json_encode($insIds));
			$this->db->trans_commit();
		}
		else
		{ 
			$data=array('status'=>FALSE,'message'=>'Service stock_balance : Error in executing Old Metal stock_balance services','error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query());
			$this->db->trans_rollback();
			$this->sendEmail("Service : stock_balance - Error",json_encode($data));
		}
	    if($type == "autoDC"){
		    return $data;
		}else{
		    echo json_encode($data);
		}
    }
    
   //Bullion Purchase
    
    function test($type="",$id_branch="")
    { 
		$items = array(); 
        $id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ; 
    	$items = $this->ret_services_model->stock_balance_details($id_branch,'30-10-2020');
    	//echo $this->db->last_query();    	echo "<pre>";print_r($items);exit;
    	$this->db->trans_begin();
    	$insIds = array();
    	if(sizeof($items)>0)
    	{
    	    
    		foreach($items as $item)
    		{
    			$insData = array(
	    			'id_product' =>$item['product_id'],
	    			'type'       =>1, // Tag item
	    			'date'       =>'2020-11-19',
	    			'id_branch'  =>$item['current_branch'],
	    			'op_blc_gwt' =>$item['op_blc_gwt'],
	    			'op_blc_nwt' =>$item['op_blc_nwt'],
	    			'op_blc_pcs' =>$item['op_blc_pcs'],
	    			'inw_gwt'	 =>$item['inw_gwt'],
	    			'inw_nwt'	 =>$item['inw_nwt'],
	    			'inw_pcs'	 =>$item['inw_pcs'],
	    			'sold_gwt'	 =>$item['sold_gwt']+$item['br_out_gwt'],
	    			'sold_nwt'	 =>$item['sold_nwt']+$item['br_out_nwt'],
	    			'sold_pcs'	 =>$item['sold_pcs']+$item['br_out_pcs'],
	    			'closing_pcs'=>$item['op_blc_pcs']+$item['inw_pcs']-$item['sold_pcs']-$item['br_out_pcs'],
	    			'closing_gwt'=>$item['op_blc_gwt']+$item['inw_gwt']-$item['sold_gwt']-$item['br_out_gwt'],
	    			'closing_nwt'=>$item['op_blc_nwt']+$item['inw_nwt']-$item['sold_nwt']-$item['br_out_nwt'],
	    			'date_add'	 =>date("Y-m-d H:i:s")
    			);
    			
    			$ins_id = $this->ret_services_model->insertData($insData,'ret_stock_balance_new');
    			
    			array_push($insIds,$ins_id);
    		}
    		
		/*	$dc_log_data = array(
						"id_branch"		=>	$id_branch,  
						"type"			=>	isset($POST['type'])?$POST['type']:1,
						"date"			=>	$this->date,
						"service"		=>	"stock_balance",
						"records"       =>  sizeof($insIds),
						"created_by"	=>	$this->emp_id,
						"created_on"	=>	date('Y-m-d H:i:s'), 
						);
			$this->ret_services_model->insertData($dc_log_data,"ret_day_closing_log");*/
		}
    	
		else{
			$data=array('status'=>FALSE,'message'=>'No Records To Update.');
		}
    	if($this->db->trans_status()===TRUE)
		{
			$data=array('status'=>TRUE,'message'=>'Stock Balance Updated Successfully.'.json_encode($insIds));
			$this->db->trans_commit();
		}
		else
		{ 
			$data=array('status'=>FALSE,'message'=>'Service stock_balance : Error in executing stock_balance services','error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query());
			$this->db->trans_rollback();
			$this->sendEmail("Service : stock_balance - Error",json_encode($data));
		}
	    if($type == "autoDC"){
		    return $data;
		}else{
		    echo json_encode($data);
		}
    }
    
    function send_karigar_reminder()
    {
        $service = $this->ret_services_model->get_service_by_code('KAR_REM');
        if($service['serv_whatsapp']==1)
        {
             $sms_data=$this->ret_services_model->Get_service_code_sms('KAR_REM');
             $whatsapp=$this->admin_usersms_model->send_whatsApp_message($sms_data['mobile'],$sms_data['message']);
        }
        
    }
    
    function test_cron(){
        echo date("Y-m-d H:i:s");
    }
    
    function required_otp_approval()	{	
        $otprequired = $this->ret_services_model->get_required_otp_approval();
        echo json_encode($otprequired);
    }
    
    
    
    //Packaging Item Stock Balance
    function stock_balance_packaging_items($type="",$id_branch="")
    {
        $items = array(); 
        $id_branch = ($type == "autoDC" ? $id_branch : $this->session->userdata("id_branch")) ; 
    	$items = $this->ret_services_model->stock_balance_packaging_items($id_branch,$this->date);
    	//echo $this->db->last_query();    	echo "<pre>";print_r($items);exit;
    	$this->db->trans_begin();
    	$insIds = array();
    	if(sizeof($items)>0)
    	{
    	    
    		foreach($items as $item)
    		{
    			$insData = array(
	    			'date'            =>$this->date,
	    			'id_branch'       =>$id_branch,
	    			'id_other_item'   =>$item['id_other_item'],
                    'op_blc_pcs'      =>$item['op_blc_pcs'],
                    'op_blc_amt'      =>$item['op_blc_amt'],
                    'inw_pcs'         =>$item['inw_pcs'],
                    'inw_amt'         =>$item['inw_amount'],
                    'out_pcs'         =>$item['out_pcs'],
                    'out_amt'          =>$item['out_amount'],
                    'closing_pcs'     =>($item['op_blc_pcs']+$item['inw_pcs']-$item['out_pcs']),
                    'closing_amt'     =>number_format($item['op_blc_amt']+$item['inw_amount']-$item['out_amount'],3,'.',''),
    			);
    			$ins_id = $this->ret_services_model->insertData($insData,'ret_other_inventory_stock');
    			//print_r($this->db->last_query());exit;
    			array_push($insIds,$ins_id);
    		}
    		
			$dc_log_data = array(
						"id_branch"		=>	$id_branch,  
						"type"			=>	isset($POST['type'])?$POST['type']:1,
						"date"			=>	$this->date,
						"service"		=>	"Packaging Item stock_balance",
						"records"       =>  sizeof($insIds),
						"created_by"	=>	$this->emp_id,
						"created_on"	=>	date('Y-m-d H:i:s'), 
						);
			$this->ret_services_model->insertData($dc_log_data,"ret_day_closing_log");
		}
    	
		else{
			$data=array('status'=>FALSE,'message'=>'No Records To Update.');
		}
    	if($this->db->trans_status()===TRUE)
		{
			$data=array('status'=>TRUE,'message'=>'Stock Balance Updated Successfully.'.json_encode($insIds));
			$this->db->trans_commit();
		}
		else
		{ 
			$data=array('status'=>FALSE,'message'=>'Service stock_balance : Error in executing stock_balance services','error_msg'=>$this->db->_error_message(),'q'=>$this->db->last_query());
			$this->db->trans_rollback();
			$this->sendEmail("Service : stock_balance - Error",json_encode($data));
		}
	    if($type == "autoDC"){
		    return $data;
		}else{
		    echo json_encode($data);
		}
    }
    //Packaging Item Stock Balance
    
	
}