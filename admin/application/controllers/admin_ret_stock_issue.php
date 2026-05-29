<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/dompdf/autoload.inc.php');

use Dompdf\Dompdf;

class Admin_ret_stock_issue extends CI_Controller

{ 

	const IMG_PATH  = 'assets/img/';

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model('ret_stock_issue_model');

		$this->load->model('admin_settings_model');

		$this->load->model("admin_usersms_model");

		$this->load->model('ret_billing_model');

		$this->load->model('log_model');

		$this->load->model('sms_model');


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

	

	public function isValueset($field)

	{

		$data=($field ? $field:'-');

		return $data;

	}



	//STOCK ISSUE

	function stock_issue($type="",$id="",$received_time="")

	{

	    $model = "ret_stock_issue_model";

		switch($type){

		    

		    case 'list':

					$data['main_content'] = "ret_stock_issue/list" ;

					$this->load->view('layout/template', $data);

		    break;

		    

		    case 'add':

					$data['main_content'] = "ret_stock_issue/form" ;

					// $data['otp_settings'] = $this->$model->get_ret_settings('vendor_approval_otp');
					
					$data['otp_settings']=$this->$model->get_profile_settings($this->session->userdata('profile'));

					$this->load->view('layout/template', $data);

					$this->load->view('layout/common_customerslider');

		    break;


			case 'save':

			    $form_secret    =$_POST['form_secret'];

			    $form_secret    =$_POST['form_secret'];

				$addData        =$_POST['order'];

				$nt_data        =$_POST['nt_data'];

				$tag_details    =$_POST['tag_id'];

				$rate_per_gram  = $_POST['rate_per_gram'];

				$stock_type = $_POST['stock_type'];
				
				// $stock_type = $addData['stock_type'];

				$return_data    =array();

				$allow_submit   = false;

				$fin_year                   = $this->$model->get_FinancialYear();

				// echo "<pre>";print_r($_POST);exit;

				if($this->session->userdata('FORM_SECRET'))

				{

				    if(strcasecmp($form_secret, ($this->session->userdata('FORM_SECRET'))) === 0)

				    {

				        $allow_submit = TRUE;

				    }

				}

				// print_r($allow_submit);exit;

				if($allow_submit)

				{

				    $dCData     = $this->admin_settings_model->getBranchDayClosingData($addData['order_from']);

				    $issue_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

				    // print_r($stock_type);exit;

				    if($stock_type==1){

            			if($addData['issue_receipt_type']==1) // ISSUE

            			{

            

            				$issue_no       = $this->$model->generateIssueNo();

            				//echo "<pre>";print_r($tag_details);exit;

            				    					
            				$insData=array(

                                'issue_no'   => $issue_no,

                                'issue_date' => $issue_date,

                                'status'     => 1,

                                'id_branch'  => $addData['order_from'],

                                'issue_type' => $addData['issue_type'],

    							'issued_to'  => $addData['issued_to'],
								
    							'issue_emp'  => $addData['sel_emp'],

    							'fin_year'  => $fin_year['fin_year_code'],

    							'id_employee' =>($addData['id_employee']!='' ? $addData['id_employee'] : NULL),

    							'id_customer'=> ($addData['cus_id']!='' ? $addData['cus_id'] : NULL),

    							'id_karigar' => ($addData['id_karigar']!='' ? $addData['id_karigar'] : NULL),

                                'remarks'    => ($addData['remark']!='' ? $addData['remark'] : NULL),

                                'form_secret'=> $form_secret,

                                'created_on' => date("Y-m-d H:i:s"),

                                'created_by' => $this->session->userdata('uid'),

                            );

							// print_r($insData);exit;


            			    $this->db->trans_begin();

            				$insId = $this->$model->insertData($insData,'ret_stock_issue');

            				// print_r($this->db->last_query());exit;

            				if($insId)

            				{

            				    $issue_type_det=$this->$model->stock_issue_type_detail($addData['issue_type']);

            				        foreach($tag_details as $tag_id)

            				        {

            				            $tagDetails = $this->$model->getTagDetails($tag_id);

            				                $issueDetail=array(

                                                'id_stock_issue' =>$insId,

                                                'tag_id'         =>$tag_id,

    											'rate_per_gram'  =>$rate_per_gram,

												'gross_wt'       =>$tagDetails['gross_wt'],

												'net_wt'         =>$tagDetails['net_wt'],

												'piece'          =>$tagDetails['piece'],

                                                );

                                            $IssueTagstatus=$this->$model->insertData($issueDetail,'ret_stock_issue_detail');

    										// print_r($this->db->last_query());exit;

                                            if($IssueTagstatus)	

                                            {

                                                $this->$model->updateData(array('tag_status'=>7),'tag_id',$tag_id,'ret_taging');

                                                //Update Tag Log status

                                                if($issue_type_det['is_remove_from_stock']==1)

                                                {

                                                        $tag_log=array(

                                                        'tag_id'	  =>$tag_id,

                                                        'date'		  =>$issue_date,

                                                        'status'	  =>7,

                                                        'from_branch' =>$addData['order_from'],

                                                        'to_branch'	  =>NULL,

                                                        'form_secret' =>$form_secret.'_'.$tag_id,

                                                        'created_on'  =>date("Y-m-d H:i:s"),

                                                        'created_by'  =>$this->session->userdata('uid'),

                                                        );

                                                        $this->$model->insertData($tag_log,'ret_taging_status_log');

                                                        if($tagDetails['id_section']!='')

                                                        {

                                                            $Secttag_log=array(

                                                        	'tag_id'	  =>$tag_id,

                                                        	'date'		  =>$issue_date,

                                                        	'form_secret' =>$form_secret.'_'.$tag_id,

                                                        	'status'	  =>7,

                                                        	'from_branch' =>$addData['order_from'],

                                                        	'to_branch'	  =>NULL,

                                                        	'from_section'=>NULL,

                                                        	'to_section'  =>$tagDetails['id_section'],

                                                        	'created_on'  =>date("Y-m-d H:i:s"),

                                                        	'created_by'  =>$this->session->userdata('uid'),

                                                        	);

                                                        	$this->$model->insertData($Secttag_log,'ret_section_tag_status_log');

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
		
									'module'        => 'Stock Issue',
		
									'operation'     => 'Add',
		
									'record'        =>  $insId,
		
									'remark'        => 'Stock Issue added successfully'
		
									);
		
								$this->log_model->log_detail('insert','',$log_data);

            					$return_data=array('status'=>TRUE,'message'=>'Stock Issued successfully..','id_stock_issue'=>$insId);

            				}

            				else

            				{ 

            			

            				    echo $this->db->last_query();exit;

            					$this->db->trans_rollback();						 	

            					$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

            				}

            		    }

            		    else if($addData['issue_receipt_type']==2)

            		    {

            		        $issue_details=$this->$model->get_IssueItems($addData['issue_id']);

    

            		        $issue_type_det=$this->$model->stock_issue_type_detail($issue_details['issue_type']);

            	            $received_time = time();

            		        foreach($tag_details as $tag_id)

            		        {

            		            if($tag_id!='')

            		            {

                		            $this->db->trans_begin();

                		            $this->$model->updateData(array('tag_status'=>0),'tag_id',$tag_id,'ret_taging');

    								$this->$model->updateData(array('status'=>3,'received_time'=>$received_time,'received_date'=>date("Y-m-d H:i:s"),'updated_by'=>$this->session->userdata('uid'),'received_by'=>$this->session->userdata('uid')),'tag_id',$tag_id,'ret_stock_issue_detail');

                                    //Update Tag Log status

                                    if($issue_type_det['is_remove_from_stock']==1)

                                    {

                                        $tagDetails = $this->$model->getTagDetails($tag_id);

                                        $tag_log=array(

                                        'tag_id'	  =>$tag_id,

                                        'date'		  =>$issue_date,

                                        'form_secret' => $form_secret.'_'.$tag_id,

                                        'status'	  => 0,

                                        'to_branch'   =>$issue_details['id_branch'],

                                        'created_on'  =>date("Y-m-d H:i:s"),

                                        'created_by'  =>$this->session->userdata('uid'),

                                        );

                                        $this->$model->insertData($tag_log,'ret_taging_status_log');

                                        if($tagDetails['id_section']!='')

                                        {

                                            $Secttag_log=array(

                                        	'tag_id'	  =>$tag_id,

                                        	'date'		  =>$issue_date,

                                        	'form_secret' => $form_secret.'_'.$tag_id,

                                        	'status'	  => 0,

                                        	'to_branch'   =>$issue_details['id_branch'],

                                        	'to_section'  =>$tagDetails['id_section'],

                                        	'created_on'  =>date("Y-m-d H:i:s"),

                                        	'created_by'  =>$this->session->userdata('uid'),

                                        	);

                                        	$this->$model->insertData($Secttag_log,'ret_section_tag_status_log');

                                        }

                                    }

            		            }

            		        }

						// Check if ALL detail items for this issue are now received → update parent status to 3
						$issue_id_for_check = $addData['issue_id'];
						$total_items    = $this->db->where('id_stock_issue', $issue_id_for_check)->count_all_results('ret_stock_issue_detail');
						$received_items = $this->db->where('id_stock_issue', $issue_id_for_check)->where('status', 3)->count_all_results('ret_stock_issue_detail');
						if($total_items > 0 && $total_items == $received_items) {
							$this->$model->updateData(array('status' => 3), 'id_stock_issue', $issue_id_for_check, 'ret_stock_issue');
						}

            		        if($this->db->trans_status()===TRUE)

            				{

            					$this->db->trans_commit();

								$log_data = array(

									'id_log'        => $this->session->userdata('id_log'),
		
									'event_date'    => date("Y-m-d H:i:s"),
		
									'module'        => 'Stock Receipt',
		
									'operation'     => 'Add',
		
									'record'        =>  $addData['issue_id'],
		
									'remark'        => 'Stock Receipt added successfully'
		
									);
		
								$this->log_model->log_detail('insert','',$log_data);

            					$return_data=array('status'=>TRUE,'message'=>'Stock Receipt Added successfully..');

            				}

            				else

            				{ 

            			

            				    echo $this->db->last_query();exit;

            					$this->db->trans_rollback();						 	

            					$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

            				}

            		    }

				    }else if($stock_type==2){ //NONTAG ISSUE TYPE

						// 		echo "<pre>";print_r($_POST);exit;

						$issue_type = $_POST['issue_type'];

						$issued_to = $_POST['issued_to'];

						$id_branch = $_POST['branch_select'];

						$remark = $_POST['remark'];

						$id_employee = $_POST['id_employee'];

						$karigar = $_POST['karigar'];

						$cus_id = $_POST['cus_id'];

						$rate_per_gram = $_POST['rate_per_gram'];

						$type_issue = $_POST['type_issue'];

						$issued_type = $_POST['issued_type'];

						$issued_branch = $_POST['issued_branch'];





						if($type_issue==1) // NONTAG ISSUE

						{

			

							$issue_no       = $this->$model->generateIssueNo();

							// echo "<pre>";print_r($issue_no);exit;
													

							$insData=array(

								'issue_no'   	=> $issue_no,

								'stock_type'	=> $stock_type,

								'issue_date' 	=> $issue_date,

								'status'     	=> 1,

								'id_branch'  	=> $id_branch,

								'issue_type' 	=> $issue_type,

								'issued_to'  	=> ($issued_to!='' ? $issued_to : NULL),

								'id_employee'	 =>($id_employee!='' ? $id_employee : NULL),

								'id_customer'	=> ($cus_id!='' ? $cus_id : NULL),

								'id_karigar' 	=> ($karigar!='' ? $karigar : NULL),

								'remarks'    	=> ($remark!='' ? $remark : NULL),

								'form_secret'	=> $form_secret,

								'created_on' 	=> date("Y-m-d H:i:s"),

								'created_by' 	=> $this->session->userdata('uid'),

							);

							$this->db->trans_begin();

							$insId = $this->$model->insertData($insData,'ret_stock_issue');

							// print_r($this->db->last_query());exit;

							if($insId)

							{

								$issue_type_det=$this->$model->stock_issue_type_detail($issue_type);

									foreach($nt_data as $nt)

									{

											$issueDetail=array(

												'id_stock_issue' =>$insId,

												'id_non_tag_item'=>$nt['id_nontag_item'],

												'piece'=>$nt['pieces'],

												'gross_wt'=>$nt['grs_wt'],

												'net_wt'=>$nt['net_wt'],

												'rate_per_gram'  =>$rate_per_gram

												);

											$IssueNtTagstatus=$this->$model->insertData($issueDetail,'ret_stock_issue_detail');

											// print_r($this->db->last_query());exit;

											if($IssueNtTagstatus)	

											{

												// $this->$model->updateData(array('tag_status'=>7),'tag_id',$tag_id,'ret_taging');

												//Update Tag Log status

												if($issue_type_det['is_remove_from_stock']==1)

												{

														$nt_log=array(

															'product'		=> $nt['id_product'],

															'design'		=> $nt['id_design'],

															'id_sub_design'	=> $nt['id_sub_design'],

															'no_of_piece'	=> $nt['pieces'],

															'gross_wt'		=> $nt['grs_wt'],

															'net_wt'		=> $nt['net_wt'],

															"status"		=> 4,

															"from_branch"	=> $id_branch,

															// "to_branch"		=> $nt['to_branch'],

															"created_by"	=> $this->session->userdata('uid'),

															"created_on"	=> date('Y-m-d H:i:s'),

															"date"			=> $issue_date

														);

														$this->$model->insertData($nt_log,'ret_nontag_item_log');



														if($nt['id_section']!='')

														{

															$section_nontag_log=array(

																'product'		=> $nt['id_product'],

																'design'		=> $nt['id_design'],

																'id_sub_design' => $nt['id_sub_design'],

																'no_of_piece'	=> $nt['pieces'],

																'gross_wt'		=> $nt['grs_wt'],

																'net_wt'		=> $nt['net_wt'],

																"status"		=> 4,//

																"from_branch"	=> $id_branch,//

																// "to_branch"		=> $nt['to_branch'],

																"from_section"	=> $nt['id_section'],

																"to_section"    => NULL,		

																"created_by"	=> $this->session->userdata('uid'),

																"created_on"	=> date('Y-m-d H:i:s'),

																"date"			=> $issue_date

															);

															$this->$model->insertData($section_nontag_log,'ret_section_nontag_item_log');

														}



														$ntstock_data = array(

															'branch'		=> $id_branch,

															'product'		=> $nt['id_product'],

															'design'		=> $nt['id_design'],

															'id_sub_design'	=> $nt['id_sub_design'],

															'id_section'	=> $nt['id_section'],

															'no_of_piece'	=> $nt['pieces'],

															'gross_wt'		=> $nt['grs_wt'],

															'net_wt'		=> $nt['net_wt'],  

															'created_by'	=> $this->session->userdata('uid'),

															'created_on'	=> date('Y-m-d H:i:s'),

														);

														if($nt['id_nontag_item'] != ''){ // NOT Head Office

															// Deduct in `FROM BRANCH` STOCK [Only if FROM BRANCH is not Head Office]

															$ntstock_data['id_nontag_item'] = $nt['id_nontag_item'];

															$ntstock_data['updated_by'] = $this->session->userdata('uid');

															$ntstock_data['updated_on'] = date('Y-m-d H:i:s');

															$this->$model->updateNTData($ntstock_data,'-');

															unset($ntstock_data['id_nontag_item']);

														} 



														// $nt_status = $this->$model->insertData($ntstock_data,'ret_nontag_item');

														

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
		
									'module'        => 'Stock Issue',
		
									'operation'     => 'Add',
		
									'record'        =>  $insId,
		
									'remark'        => 'Stock Issue added successfully'
		
									);
		
								$this->log_model->log_detail('insert','',$log_data);

								$return_data=array('status'=>TRUE,'message'=>'Stock Issued successfully..','id_stock_issue'=>$insId);

							}

							else

							{ 

								$this->db->trans_rollback();						 	

								echo $this->db->last_query();exit;

								$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

							}

						}

						else if($type_issue==2)

						{ //NONTAG RECEIPT



							// echo "<pre>";print_r($_POST);exit;



							$this->db->trans_begin();



							$issue_type_det=$this->$model->stock_issue_type_detail($issued_type);

									foreach($nt_data as $nt)

									{

										if($issue_type_det['is_remove_from_stock']==1)

										{

												$nt_log=array(

													'product'		=> $nt['id_product'],

													'design'		=> $nt['id_design'],

													'id_sub_design'	=> $nt['id_sub_design'],

													'no_of_piece'	=> $nt['pieces'],

													'gross_wt'		=> $nt['grs_wt'],

													'net_wt'		=> $nt['net_wt'],

													"status"		=> 0,

													// "from_branch"	=> $nt['issued_branch'],

													"to_branch"		=> $issued_branch,

													"created_by"	=> $this->session->userdata('uid'),

													"created_on"	=> date('Y-m-d H:i:s'),

													"date"			=> $issue_date

												);

												$this->$model->insertData($nt_log,'ret_nontag_item_log');

												// print_r($this->db->last_query());exit;



												if($nt['id_section']!='')

												{

													$section_nontag_log=array(

														'product'		=> $nt['id_product'],

														'design'		=> $nt['id_design'],

														'id_sub_design' => $nt['id_sub_design'],

														'no_of_piece'	=> $nt['pieces'],

														'gross_wt'		=> $nt['grs_wt'],

														'net_wt'		=> $nt['net_wt'],

														"status"		=> 0, //issued item received

														// "from_branch"	=> $nt['issued_branch'],

														"to_branch"		=> $issued_branch, //to issues branch

														"from_section"	=> NULL,

														"to_section"    => $nt['id_section'],		

														"created_by"	=> $this->session->userdata('uid'),

														"created_on"	=> date('Y-m-d H:i:s'),

														"date"			=> $issue_date

													);

													$this->$model->insertData($section_nontag_log,'ret_section_nontag_item_log');

												}



												$ntstock_data = array(

													'product'		=> $nt['id_product'],

													'design'		=> $nt['id_design'],

													'id_sub_design'	=> $nt['id_sub_design'],

													'id_section'	=> $nt['id_section'],

													'no_of_piece'	=> $nt['pieces'],

													'gross_wt'		=> $nt['grs_wt'],

													'net_wt'		=> $nt['net_wt'],

												);

												if($nt['id_nontag_item'] != ''){ // NOT Head Office

													// Deduct in `FROM BRANCH` STOCK [Only if FROM BRANCH is not Head Office]

													$ntstock_data['id_nontag_item'] = $nt['id_nontag_item'];

													$ntstock_data['updated_by'] = $this->session->userdata('uid');

													$ntstock_data['updated_on'] = date('Y-m-d H:i:s');

													$this->$model->updateNTData($ntstock_data,'+');

													unset($ntstock_data['id_nontag_item']);

												} 



												$update_stock_details = array(

													'received_date'=>$issue_date,

													'received_by'=>$this->session->userdata('uid'),

													'status'=>3

												);





												$this->$model->updateData($update_stock_details,'id_stock_issue_detail',$nt['id_stock_issue_detail'],'ret_stock_issue_detail');

												// echo $this->db->last_query();exit;

										}

												

											

									}

							// Check if ALL detail items for this issue are now received → update parent status to 3
							if(!empty($nt_data)) {
								$issue_id_for_check = $nt_data[0]['id_stock_issue'];
								$total_items    = $this->db->where('id_stock_issue', $issue_id_for_check)->count_all_results('ret_stock_issue_detail');
								$received_items = $this->db->where('id_stock_issue', $issue_id_for_check)->where('status', 3)->count_all_results('ret_stock_issue_detail');
								if($total_items > 0 && $total_items == $received_items) {
									$this->$model->updateData(array('status' => 3), 'id_stock_issue', $issue_id_for_check, 'ret_stock_issue');
								}
							}

							if($this->db->trans_status()===TRUE)

							{

								$this->db->trans_commit();

								$log_data = array(

									'id_log'        => $this->session->userdata('id_log'),
		
									'event_date'    => date("Y-m-d H:i:s"),
		
									'module'        => 'Stock Receipt',
		
									'operation'     => 'Add',
		
									'record'        =>  NULL,
		
									'remark'        => 'Stock Receipt added successfully'
		
									);
		
								$this->log_model->log_detail('insert','',$log_data);

								$return_data=array('status'=>TRUE,'message'=>'Stock Receipt successfully..','id_stock_issue'=>$insId);

							}

							else

							{ 

								$this->db->trans_rollback();						 	

								echo $this->db->last_query();exit;

								$return_data=array('status'=>FALSE,'message'=>'Unable to proceed the requested process');

							}

						}

					}

		    }

		    else

		    {

		        $return_data=array('status'=>FALSE,'message'=>'Invalid Form Submit..');

		    }

			echo json_encode($return_data);

					

			break;

			

			case 'issue_print':



                $data['issue'] = $this->$model->get_IssueItems($id);

                $data['item_details']=$this->$model->get_issue_item_details($id,$data['issue']['issue_type'],$data['issue']['repair_type'],$received_time,$data['issue']['stock_type']);

                $data['comp_details']=$this->admin_settings_model->getCompanyDetails("");

				// echo"<pre>";print_r($data);exit;

                $html = $this->load->view('ret_stock_issue/issue_ack', $data,true);

                $this->load->helper(array('dompdf', 'file'));

    			$dompdf = new DOMPDF();

    			$dompdf->load_html($html);

    			$dompdf->set_paper("a4", "portriat" );

    			$dompdf->render();

    			while(ob_get_level()) ob_end_clean();
    			$dompdf->stream("Receipt.pdf",array('Attachment'=>0));

			break;
			case 'issue_print_detail':



                $data['issue'] = $this->$model->get_IssueItems($id);

                $data['item_details']=$this->$model->get_issue_item_tag($id,$data['issue']['issue_type'],$data['issue']['repair_type'],$received_time,$data['issue']['stock_type']);

                $data['comp_details']=$this->admin_settings_model->getCompanyDetails("");

                $html = $this->load->view('ret_stock_issue/issue_ack_det', $data,true);

                $this->load->helper(array('dompdf', 'file'));

    			$dompdf = new DOMPDF();

    			$dompdf->load_html($html);

    			$dompdf->set_paper("a4", "portriat" );

    			$dompdf->render();

    			while(ob_get_level()) ob_end_clean();
    			$dompdf->stream("Receipt.pdf",array('Attachment'=>0));

			break;

			

			default: 

    			$list = $this->$model->ajax_getStockIssueList($_POST);	 

    		  	$access = $this->admin_settings_model->get_access('admin_ret_stock_issue/stock_issue/list');

    	        $data = array(

                            'list'   => $list,

                            'access' => $access

	        			 );  

			echo json_encode($data);

						

		} 

	  	

	}

	

	public function get_tag_scan_details(){

		$model = "ret_stock_issue_model";

		$data = $this->$model->get_tag_scan_details($_POST);	  

		// print_r($this->db->last_query());exit;

		echo json_encode($data);

	}

	

	

	public function get_receipt_tag_scan_details(){

		$model = "ret_stock_issue_model";

		$data = $this->$model->get_receipt_tag_scan_details($_POST);	  

		echo json_encode($data);

	}

	

	

	function get_stock_issue_type()

	{

	    $model = "ret_stock_issue_model";

		$data = $this->$model->get_stock_issue_type();	  

		echo json_encode($data);

	}

	

	

	function get_StockIssuedItems()

	{

	    $model = "ret_stock_issue_model";

		$data = $this->$model->get_StockIssuedItems($_POST);	  

		echo json_encode($data);

	}

	//STOCK ISSUE

	

	//non tag issue



	public function get_nontag_scan_details()

	{

		$model = "ret_stock_issue_model";

		$data = $this->$model->get_nontag_scan_details($_POST);

		echo json_encode($data);

	}



	//non tag issue
	function stock_trans_send_sms($mobile,$message,$dlt_te_id='')

	{

		if($this->config->item('sms_gateway') == '1'){

		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$dlt_te_id);

		}

		elseif($this->config->item('sms_gateway') == '2'){

	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');

		}

	}

	function  stock_issue_sendotp()

	{

		$model="ret_stock_issue_model";

		$mobile_num     = $this->input->post('mobile');

		$send_resend     = $this->input->post('send_resend');

		$sent_otp='';

		if($mobile_num!='')

		{

			$this->db->trans_begin();

			$this->session->unset_userdata("stock_issue_otp");

			$this->session->unset_userdata("stock_issue_otp_exp");

			$OTP = mt_rand(100001,999999);

			$this->session->set_userdata('stock_issue_otp',$OTP);

			$this->session->set_userdata('stock_issue_otp_exp',time()+60);

			$message="Hi Your OTP  For Stock Issue is :  ".$OTP." Will expire within 1 minute.";

			$otp_gen_time = date("Y-m-d H:i:s");

			$insData=array(

			'mobile'=>$mobile_num,

			'otp_code'=>$OTP,

			'otp_gen_time'=>date("Y-m-d H:i:s"),

			'module'=>'Stock Issue',

			'send_resend'=>$send_resend,

			'id_emp'=>$this->session->userdata('uid')

			);

			$insId = $this->$model->insertData($insData,'otp');

		}

	    if($insId)

	  	{

	  			$this->db->trans_commit();

	  			$this->stock_trans_send_sms($mobile_num,$message);

	  			$status=array(  'OTP' => $OTP,'status'=>true,'msg'=>'OTP sent Successfully');
				//   'OTP' => $OTP,

	  	}

	  	else

	  	{

	  			$this->db->trans_rollback();

	  			$status=array('status'=>false,'msg'=>'Unabe To Send Try Again');

	  	}

		echo json_encode($status);

	}
	
	function stock_issue_verify_otp()

	{

		$model="ret_stock_issue_model";

		$post_otp=$this->input->post('otp');

		$session_otp=$this->session->userdata('stock_issue_otp');

		$otp = array(explode(',',$session_otp));

		$this->db->trans_begin();

		if($post_otp!='')

		{

    		foreach($otp[0] as $OTP)

    		{

    			if($OTP==$post_otp)

    			{

    				if(time() >= $this->session->userdata('stock_issue_otp_exp'))

    				{

    					$this->session->unset_userdata('stock_issue_otp');

    					$this->session->unset_userdata('stock_issue_otp_exp');

    					$status=array('status'=>false,'msg'=>'OTP has been expired');

    				}

    				else

    				{

    					$this->db->trans_commit();

    					$updData=array('is_verified'=>1,'verified_time'=>date("Y-m-d H:i:s"));

    					$updStatus=$this->$model->updateData($updData,'otp_code',$post_otp,'otp');

    					$status=array('status'=>true,'msg'=>'OTP Verified Successfully.','verified_otp'=>$post_otp);

    				}

    				break;

    			}

    			else

    			{

    				$status=array('status'=>false,'msg'=>'Please Enter Valid OTP');

    			}

    		}

	    }

	    else{

	       $status=array('status'=>false,'msg'=>'Please Enter Valid OTP');

	   }

	  	echo json_encode($status);

	}	
							
							

}	

?>