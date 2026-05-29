

<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_ret_section_transfer extends CI_Controller

{

    function __construct()

    {

        parent::__construct();

        ini_set('date.timezone', 'Asia/Calcutta');

        $this->load->model('ret_section_transfer_model');

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



    public function ret_section_transfer($type="")

    {

        $model = "ret_section_transfer_model";



        switch($type)

        {

            case 'list':

                $profile  = $this->admin_settings_model->profileDB("get",$this->session->userdata('profile'));

				$data['counter_change_otp']    = $profile['counter_change_otp'];

                $data['main_content'] = "section_transfer/list" ;

                $this->load->view('layout/template', $data);

            break;

            

            case 'getSectionTags':

                $data = $this->$model->getSectionTags($_POST);	 

                echo json_encode($data);

            break;   

            

            case 'save':

                    //echo "<pre>";print_r($_POST);exit;

                    //$addData = $_POST['trans_data'];

                    $transfer_to_section  = $_POST['trans_to_section'];

                    $branch = $_POST['id_branch'];



                

                    $dCData = $this->admin_settings_model->getBranchDayClosingData($branch);

					$datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

                        $this->db->trans_begin();

                    foreach($_POST['trans_data'] as $key => $val)

                    {

                        

                        if($_POST['section_item_type'] == 1)

                        {

                            $tag_details  = $this->$model->get_tag_details($val['tag_id']);

                            if($tag_details['tag_status']==0)

                            {

                                $tag_data['id_section'] = $transfer_to_section;

                                

                                $this->$model->updateData($tag_data,'tag_id',$val['tag_id'],'ret_taging');

    

                                $secTags = array(

        

                                    'tag_id'          =>    $val['tag_id'],

                                    'from_branch'     =>    NULL,

                                    'to_branch'       =>    $val['id_branch'],

                                    'from_section'    =>    ($val['trans_from_section']!='' ? $val['trans_from_section'] :NULL),

                                    'to_section'      =>    $transfer_to_section,

                                    'created_by'	  =>    $this->session->userdata('uid'),

                                    'created_on'	  =>    date('Y-m-d H:i:s'),

                                    'status'          =>    0, 

                                    'date'            =>    $datetime,

                                );

                                $status = $this->$model->insertData($secTags,'ret_section_tag_status_log');

                                $section = $this->$model->sectionData($transfer_to_section);

                                

                                if($section['is_home_bill_counter'] == 1) 

                                {

                                    $section_item = array(     

                                        'id_branch'	      => $val['id_branch'],

                                        'id_section'      => $transfer_to_section,

                                        'id_product'      => $tag_details['product_id'],

                                        'no_of_piece'     => $val['pcs'],

                                        'gross_wt'	      => $val['grs_wt'],

                                        'net_wt'		  => $val['net_wt'],

                                     );

                                  $isExists = $this->$model->checkSectionItemExist($section_item);

                                  if($isExists['id_hometag_item']!='')

                                  { 

                                        $section_item = array(     

                                            'id_branch'	      => $val['id_branch'],

                                            'id_section'      => $transfer_to_section,

                                            'id_product'      => $tag_details['product_id'],

                                            'no_of_piece'     => $val['pcs'],

                                            'gross_wt'	      => $val['grs_wt'],

                                            'net_wt'		  => $val['net_wt'],   

                                            'created_by'	  => $this->session->userdata('uid'),   

                                            'created_on'	  => date('Y-m-d H:i:s'),

                                            'updated_by'	  => $this->session->userdata('uid'),

                                            'updated_on'	 => date('Y-m-d H:i:s'),	

                                         );

                            

                                        if($isExists['id_hometag_item']!='')

                                        {

                                            $section_item['id_hometag_item'] = $isExists['id_hometag_item'];

                                            $nt_status = $this->$model->updatesecNTData($section_item,'-');

                                        }

                                   } 

                            

    

                            

                                   $section_item_log = array(

                                    'id_product'		  => $tag_details['product_id'],

                                    'no_of_piece'	      => $val['pcs'],

                                    'gross_wt'	          => $val['grs_wt'],

                                    'net_wt'		      => $val['net_wt'],

                                    'tag_id'              => $val['tag_id'],

                                    "status"		      => 0,

                                    'from_branch'         => NULL,

                                    'to_branch'           => $val['id_branch'],

                                    "from_section"        => NULL,

                                    "to_section"          => $transfer_to_section,									

                                    "created_by"	      => $this->session->userdata('uid'),

                                    "created_on"          => date('Y-m-d H:i:s'),

                                    "date"		          => $datetime,

                                    );

                                    

                                    $this->$model->insertData($section_item_log,'ret_home_section_item_log');

                                   

                                    $this->$model->updatestatus($val['tag_id'],'tag_id',$val['tag_id'],'ret_taging');

                                    

                                    $taging_status_log=array(

                                        'tag_id'      => $val['tag_id'],

                                        'status'      => 16,

                                        'from_branch' => $val['id_branch'],

                                        'to_branch'   => NULL,

                                        "created_by"  => $this->session->userdata('uid'),

                                        "created_on"  => date('Y-m-d H:i:s'),

                                        "date"		  => $datetime,

                                    );

                                    $this->$model->insertData($taging_status_log,'ret_taging_status_log');

                                }  

                            }

                        }

                        else if($_POST['section_item_type'] == 2)

                        {

                       

                        $nt_data = array(

                            'product'       => $val['product'],     

                            'design'		=> $val['design'],

                            'id_sub_design' => $val['id_sub_design'],

                            'id_section'    => $transfer_to_section,

                            'branch'        => $val['branch'],

                            'gross_wt'		=> $val['gross_wt'],

                            'net_wt'		=> $val['net_wt'],   

                            'no_of_piece'	=> $val['no_of_piece'],   

                            'updated_by'	=> $this->session->userdata('uid'),

                            'updated_on'	=> date('Y-m-d H:i:s'),		

                             );



                           if($val['id_nontag_item'] != ''){ //If BT is ret_nontag_item Table

            				   $nt_data['id_nontag_item'] = $val['id_nontag_item'];    

            				   $this->$model->updateNTData($nt_data,'-');

    

                               $section_nontag_log = array(

                                'product'		  => $val['product'],

                                'design'		  => $val['design'],

                                'id_sub_design'   => $val['id_sub_design'],

                                'no_of_piece'	  => $val['no_of_piece'],

                                'gross_wt'	      => $val['gross_wt'],

                                'net_wt'		  => $val['net_wt'],

                                "status"		  => 4,

                                'from_branch'     => $val['branch'],

                                'to_branch'       => NULL,

                                "from_section"    => ($val['id_section']!='' ? $val['id_section']:NULL),

                                "to_section"      => NULL,									

                                "created_by"	  => $this->session->userdata('uid'),

                                "created_on"      => date('Y-m-d H:i:s'),

                                "date"		      => $datetime,

                                );

                 

                                $this->$model->insertData($section_nontag_log,'ret_section_nontag_item_log'); 

                                //print_r($this->db->last_query());exit;

            				  } 



                          $isExists = $this->$model->checkNonTagItemExist($nt_data);



                            if($isExists['id_nontag_item']!= '')

                            {

                                $nt_data = array(

                                'product'       => $val['product'],     

                                'design'		=> $val['design'],

                                'id_sub_design' => $val['id_sub_design'],

                                'id_section'    => $transfer_to_section,

                                'branch'        => $val['branch'],

                                'gross_wt'		=> $val['gross_wt'],

                                'net_wt'		=> $val['net_wt'],   

                                'no_of_piece'	=> $val['no_of_piece'],   

                                'updated_by'	=> $this->session->userdata('uid'),

                                'updated_on'	=> date('Y-m-d H:i:s'),					

                                );

                                if($val['id_nontag_item'] != '')

                                {

                                    $nt_data['id_nontag_item'] = $isExists['id_nontag_item'];

                                    $nt_status = $this->$model->updateNTData($nt_data,'+');

                                }

                            }       

                            else

                            {

                                $nt_data = array(

                                'product'       => $val['product'],     

                                'design'		=> $val['design'],

                                'id_sub_design' => $val['id_sub_design'],

                                'id_section'    => $transfer_to_section,

                                'branch'        => $val['branch'],

                                'gross_wt'		=> $val['gross_wt'],

                                'net_wt'		=> $val['net_wt'],   

                                'no_of_piece'	=> $val['no_of_piece'],   

                                "created_by"	=> $this->session->userdata('uid'),

                                "created_on"    => date('Y-m-d H:i:s'),				

                                );

                                $nt_status = $this->$model->insertData($nt_data,'ret_nontag_item');

                            }

                          



                                $section_nontag_log = array(

                                'product'		    => $val['product'],

                                'design'		    => $val['design'],

                                'id_sub_design'     => $val['id_sub_design'],

                                'no_of_piece'	    => $val['no_of_piece'],

                                'gross_wt'	        => $val['gross_wt'],

                                'net_wt'		    => $val['net_wt'],

                                "status"		    => 0,

                                'from_branch'       => NULL,

                                'to_branch'         => $val['branch'],

                                "from_section"      => ($val['id_section']!='' ? $val['id_section']:NULL),

                                "to_section"        => $transfer_to_section,									

                                "created_by"	    => $this->session->userdata('uid'),

                                "created_on"        => date('Y-m-d H:i:s'),

                                "date"		        => $datetime,

                                );

                                

                                $this->$model->insertData($section_nontag_log,'ret_section_nontag_item_log'); 



                       }

                    }

                    if($this->db->trans_status()===TRUE)

		             {

					 	$this->db->trans_commit();

					 	$this->session->set_flashdata('chit_alert',array('message'=>'Tag Transfered successfully','class'=>'success','title'=>'Section Transfer'));

					 	$return_data=array('message'=>'Tag transfer successfully','status'=>true);

					 	echo json_encode($return_data);

					 }

					 else

					 {

					 	$this->db->trans_rollback();						 	

					 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Section Transfer'));

					 	$return_data=array('message'=>'Unable to proceed the requested process','status'=>false);

					 	echo json_encode($return_data);

					 }

            break;    

        }



    }

    
 function send_counterchange_otp()
 {  

     $model="ret_section_transfer_model";
     $from_section    = $_POST['from_section'];
     $to_section    = $_POST['to_section'];
     $id_branch    = $_POST['id_branch'];
     $tot_gwt    = $_POST['total_gwt'];
     $tot_pcs    = $_POST['tot_pcs'];
     $data = $this->$model->getBrnachOtpRegMobile($id_branch);
     $mobile_num     = array(explode(',',$data));
     $sent_otp='';
     $comp_details=$this->admin_settings_model->get_company();

     foreach($mobile_num[0] as $mobile)
         {
             if($mobile)
             {
                  $this->session->unset_userdata("counterchange_otp");
                 $OTP = mt_rand(1001,9999);
                 $sent_otp.=$OTP;
                 $this->session->set_userdata('counterchange_otp',$sent_otp);
                 $this->session->set_userdata('counterchange_otp_exp',time()+300);
                 $service = $this->admin_settings_model->get_service_by_code('Counter_Change_Otp');
                 $expiry = 5;                                
                 $otp_gen_time = date("Y-m-d H:i:s");
                 $insData=array(
                     'mobile'        =>$mobile,
                     'otp_code'      =>$OTP,
                     'otp_gen_time'  =>date("Y-m-d H:i:s"),
                     'module'        =>'Counter Change OTP',
                     'id_emp'        =>$this->session->userdata('uid')
                 );
                 $this->db->trans_begin();
                 $insId = $this->$model->insertData($insData,'otp');
                 // print_r($insId);exit;
                  if($insId)
                  {
                     // if($service['serv_whatsapp'] == 1)
                     // 	{
                     // 			$message="Hi Your OTP For Counter Change is :".$OTP." Will expire within ".$expiry." minute, REGARDS ".strtoupper($comp_details['company_name']).".";

                     // 			$whatsapp=$this->admin_usersms_model->send_whatsApp_message(9486528828,$message);
                     // 	}
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

 function verify_counter_change_otp()
 {
         $model                ="ret_section_transfer_model";
         $post_otp             =$this->input->post('otp');
         $session_otp          =$this->session->userdata('counterchange_otp');
         $otp                  = array(explode(',',$session_otp));
         foreach($otp[0] as $OTP)
         {
                 if($OTP==$post_otp)
                 {
                     if(time() >= $this->session->userdata('counterchange_otp_exp'))
                     {
                         $this->session->unset_userdata('counterchange_otp');
                         $this->session->unset_userdata('counterchange_otp_exp');
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
 
 
 

}

?>

