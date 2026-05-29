<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_ret_eda extends CI_Controller

{
    const REPORT_FOLDER = 'ret_reports/';

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model('ret_eda_model');

		$this->load->model('admin_settings_model');

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

	public function index()

	{	

	}	

	/**

	* EDA Functions Starts

	*/

	public function eda($type="",$id="") {

		$model = "ret_eda_model";

		switch($type) {

			case 'list':

					$data['main_content'] = "estimation/eda/list" ;

					$this->load->view('layout/template', $data);

					break;

			case "update":

					$esti_id = $_POST['esti_id'];

					$type = $_POST['type'];

					if($type == 1) {

						$estimate_final_amt = $_POST['estimate_final_amt'];

						

						$trans_update = FALSE;

						$data	=	array(

							'estimate_final_amt' => $estimate_final_amt,

							'is_eda_approved' => 1

						);

						$this->db->trans_begin();

						

						

						

						$estdetails = $this->$model->getEstDetails($esti_id);

						if(sizeof($estdetails) > 0)

						{

						    $update_status = $this->$model->updateData($data,'estimation_id',$esti_id, 'ret_estimation');

    						foreach($estdetails as $est){

    							if(!empty($est['tag_id'])){

    								$this->$model->updateData(array('tag_status'=> 10),'tag_id',$est['tag_id'], 'ret_taging');

    							

    								//Update Tag Log status

    								$tag_log=array(

    									'tag_id'	  => !empty($est['tag_id']) ? $est['tag_id'] : NULL,

    									'date'		  => date("Y-m-d H:i:s"),

    									'status'	  => 10,

    									'from_branch' => $est['id_branch'],

    									'to_branch'	  =>NULL,

    									'created_on'  =>date("Y-m-d H:i:s"),

    									'created_by'  =>$this->session->userdata('uid'),

    									);

    									$this->$model->insertData($tag_log,'ret_taging_status_log');

    							}else if($est['is_non_tag'] == 1){

    								$existData=array('id_product' => $est['product_id'], 'id_design'=>$est['design_id'],'id_branch'=> $est['id_branch']);

    								$isExist = $this->$model->checkNonTagItemExist($existData);

    								if($isExist['status'] == TRUE)

    								{

    									$nt_data = array(

    										'id_nontag_item'=>$isExist['id_nontag_item'], //product_id, design_id 

    										'no_of_piece'	=> $est['piece'],

    										'gross_wt'		=> $est['gross_wt'],

    										'net_wt'		=> $est['net_wt'],  

    										'less_wt'		=> $est['less_wt'],  

    										'updated_by'	=> $this->session->userdata('uid'),

    										'updated_on'	=> date('Y-m-d H:i:s'),

    									);

    									$this->$model->updateNTData($nt_data,'-');

    									$non_tag_data =array(

    										'from_branch'	=>$est['id_branch'],

    										'to_branch'	    =>NULL,

    										'no_of_piece'   =>$est['piece'], 

    										'less_wt' 		=> $est['less_wt'], 

    										'net_wt' 		=> $est['net_wt'], 

    										'gross_wt' 		=> $est['gross_wt'], 

    										'product'		=> $est['product_id'],

    										'design'		=> $est['design_id'],

    										'date'  	    => $est['est_date'],

    										'created_on'  	=> date("Y-m-d H:i:s"),

    										'created_by'   	=> $this->session->userdata('uid'),	

    										'status'   		=> 10,

    										'bill_id'       => $esti_id

    									);

    									$this->$model->insertData($non_tag_data,'ret_nontag_item_log');

    								}

    							}

    							

    							$trans_update = TRUE;

    							

    						}//print_r($estdetails);echo $trans_update;exit;

                            if($this->db->trans_status()===TRUE) // && $trans_update === TRUE

                            {

                                $return_data['status']=true;

                                $this->db->trans_commit();

                                

                                $log_data = array(

                                'id_log'        => $this->session->userdata('id_log'),

                                'event_date'    => date("Y-m-d H:i:s"),

                                'module'        => 'EDA Estimation',

                                'operation'     => 'Approval',

                                'record'        =>  $esti_id,  

                                'remark'        => 'Record Approved successfully'

                                );

                                $this->log_model->log_detail('insert','',$log_data);

                            

                            } 

                            else 

                            {

                                $this->db->trans_rollback();

                                $return_data['status'] = false;

                                $return_data['message'] = 'Unable to Proceed Your Request..';

                            }

					    }

					    else

					    {

					        $return_data['status'] = false;

					        $return_data['message'] = 'Tag Details Not Found..';

					    }

					} 

					else if($type == 2) {

						$data	=	array(

							'is_eda_approved' => 2

						);

                    

						$update_status = $this->$model->updateData($data,'estimation_id',$esti_id, 'ret_estimation');

						if($update_status) {

							$return_data['status'] = true;

						} else {

							$return_data['status'] = false;

						}

					}

					echo json_encode($return_data);

					break;

			default: 

					$id_branch  = $this->input->post('id_branch');

					$list = $this->$model->ajax_getEdaList($id_branch);	 

					$access = $this->admin_settings_model->get_access('admin_ret_eda/eda/list');

					$data = array(

										'list'  => $list,

										'access'=> $access

									);  

					echo json_encode($data);

		}

	}
	function sales_report($type="")
	{   
	 	$model = "ret_eda_model";
		 
		switch($type)
		 {
			case 'list': 
			        $data['main_content'] = self::REPORT_FOLDER.'eda_reports/list';
        			$this->load->view('layout/template', $data);
			break;
			
			default: 
					$list = $this->$model->ajax_getSalesList($_POST);	 
					$access = $this->admin_settings_model->get_access('admin_ret_eda/sales_report/list');
					$data = array(
										'list'  => $list,
										'access'=> $access
									);  
					echo json_encode($data);
			
		}
    }
    
    
    function purchase_report($type="")
	{   
		$model = "ret_eda_model";
		switch($type)
		{
			case 'list': 
			        $data['main_content'] = self::REPORT_FOLDER.'eda_reports/purchase_eda';
        			$this->load->view('layout/template', $data);
				break;
				default: 
					$list = $this->$model->get_eda_purchase($_POST);
					//print_r($_POST['id_metal']);exit;	 
					$access = $this->admin_settings_model->get_access('admin_ret_eda/purchase_report/purchase_eda');
					$data = array(
										'list'  => $list,
										'access'=> $access
									);  
					echo json_encode($data);
			
		}
    }


    function partly_sold_report($type="")
	{
		$model = "ret_eda_model";
		switch($type)
		{
			case 'list':
				$data['main_content'] = self::REPORT_FOLDER.'eda_reports/partly_sold_eda';
				$this->load->view('layout/template',$data);
			break;
			default: 
					$list = $this->$model->get_eda_partlysold($_POST);	
					
					

					$access = $this->admin_settings_model->get_access('admin_ret_eda/partly_sold_report/partly_sold_eda');
					$data = array(
										'list'  => $list,
										'access'=> $access
									);  
					echo json_encode($data);
			
		}
	}

}	

?>