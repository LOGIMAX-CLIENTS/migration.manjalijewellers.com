<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require(APPPATH.'libraries/REST_Controller.php');
class Purchase_plan extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		//$this->load->model("purchaseplan_model");
	}
	
	
	function createPlan_post()
    {
        $resultArr = array();
        try 
        {
            $postData	= get_values();
            $pb			= $postData['basic'];
			$pln_set	= $postData['plan_settings'];
            /*$form_validation = $this->createPlan_form_validation($postData);
            var_dump($form_validation->run()); 
            print_r($form_validation->error_array());exit;*/
            //if ($form_validation->run() == TRUE)
            if (TRUE)
            {
               /*plan basic settings array*/-
			    
			    $plan_data["basic"] = array(
			        "active"                => (!isValidVal($pb->active)? $pb->active : 1),
			        "visible"               => (!isValidVal($pb->visible)? $pb->visible : 1),
			        "is_enquiry"            => (!isValidVal($pb->is_enquiry)? $pb->is_enquiry : 0),
			        "restrict_payment_in"   => (!isValidVal($pb->restrict_payment_in)?$pb->restrict_payment_in : 0),
			        "id_metal"              => (!isValidVal($pb->id_metal)?$pb->id_metal:NULL),
			        "id_purity"             => (!isValidVal($pb->id_purity)?$pb->id_purity:NULL),
			        "pp_name"				=> (!isValidVal($pb->pp_name)?$pb->pp_name:NULL),
			        "code"                  => (!isValidVal($pb->code)?$pb->code:NULL),
			        "sync_scheme_code"      => (!isValidVal($pb->sync_scheme_code)?$pb->sync_scheme_code:NULL),
			        "sch_img"               => (!isValidVal($pb->sch_img)?$pb->sch_img:NULL),
			     	
			     	/*plan settings array*/  
			         
			        "plan_type"             => (!isValidVal($pln_set->plan_type)? $pln_set->plan_type : 0),
			        "pp_payable_type"       => (!isValidVal($pln_set->pp_payable_type)? $pln_set->pp_payable_type : 1),
			        "installment_cycle"     => (!isValidVal($pln_set->installment_cycle)? $pln_set->installment_cycle : 1),
			        "grace_days"            => (!isValidVal($pln_set->grace_days)? $pln_set->grace_days : NULL),
			        "total_installments"    => (!isValidVal($pln_set->total_installments)? $pln_set->total_installments : NULL),
			        "ins_cycle_days"        => (!isValidVal($pln_set->ins_cycle_days)? $pln_set->ins_cycle_days : NULL),
			        "show_ins_type"         => (!isValidVal($pln_set->show_ins_type)? $pln_set->show_ins_type : 1),
			        "allow_advance"         => (!isValidVal($pln_set->allow_advance)? $pln_set->allow_advance : 0),
			        "advance_months"        => (!isValidVal($pln_set->advance_months)? $pln_set->advance_months : 0),
			        "allow_advance_in"      => (!isValidVal($pln_set->allow_advance_in)? $pln_set->allow_advance_in : 0),
			        "allow_preclose"        => (!isValidVal($pln_set->allow_preclose)? $pln_set->allow_preclose : 0),
			        "preclose_months"       => (!isValidVal($pln_set->preclose_months)? $pln_set->preclose_months : 0),
			        "apply_benefit_min_ins" => (!isValidVal($pln_set->apply_benefit_min_ins)? $pln_set->apply_benefit_min_ins : 0),
			        
			        /*plan grouping array*/  
			         
			        "plan_grouping"         => (!isValidVal($pln_set->plan_grouping)? $pln_set->plan_grouping : 2),
			        "group_type"            => (!isValidVal($pln_set->group_type)? $pln_set->group_type : NULL),
			        "group_max_members"     => (!isValidVal($pln_set->group_max_members)? $pln_set->group_max_members : NULL),
			        "group_creation"        => (!isValidVal($pln_set->group_creation)? $pln_set->group_creation : 1),
			        
			        /*payment chances array*/  
			         
			        "payment_chances"       => (!isValidVal($pln_set->payment_chances)? $pln_set->payment_chances : 0),
			        "payment_attempt_limit" => (!isValidVal($pln_set->payment_attempt_limit)? $pln_set->payment_attempt_limit : 2),
			        "min_chance"            => (!isValidVal($pln_set->min_chance)? $pln_set->min_chance : 1),
			        "max_chance"            => (!isValidVal($pln_set->max_chance)? $pln_set->max_chance : 1),
			    );
				/*			    
			    $this->db->trans_begin();
                $insert_status = $this->curd_model->insertData($plan_data); 
                if($insert_status['success']) {
                    $this->db->trans_commit();
                    $success = true;
                    $message = "Plan created successfully.";
                    $insert_id = $insert_status['data']['insert_id'];
                } else {
                    $this->db->trans_rollback();
                    $success = false;
                    $message = "Error occured. Something went wrong. Please try again.";
                }
                */
                print_r($plan_data);exit;
                $resultArr = $plan_data;
            }
            else
            {
                $errArr  = $form_validation->error_array();
                $success = false;
                $message = reset($errArr);
            }
        }
        catch(Exception $e) 
        {
            $this->db->trans_rollback();
            $success = false;
            $message = $e->getMessage();
        }
        $result = array("success" => $success, "message" => $message, 'data' => $resultArr);
        $this->response($result, 200);
    }
	
	function createPlan_form_validation($data)
    {
    	$this->form_validation->set_data($data['basic']);
    	$config = array(
	        array(
	                'field' => 'code',
	                'label' => 'Purchase Plan Code',
	                'rules' => 'required'
	        ),
	        /*array(
	                'field' => 'password',
	                'label' => 'Password',
	                'rules' => 'required',
	                'errors' => array(
	                        'required' => 'You must provide a %s.',
	                ),
	        ),*/
	        array(
	                'field' => 'sync_scheme_code',
	                'label' => 'sync_scheme_code Confirmation',
	                'rules' => 'required'
	        ),
	        /*array(
	                'field' => 'email',
	                'label' => 'Email',
	                'rules' => 'required'
	        )*/
		);

		$this->form_validation->set_rules($config);
        return $this->form_validation;
    	
    	$ps = $plan['plan_settings'];
        $this->form_validation->set_rules('basic[code]','Purchase Plan Code','trim|required|min_length[6]|max_length[45]||is_unique[pp_purchase_plan.code]');
        $this->form_validation->set_rules('sync_scheme_code','Sync Scheme Code','trim|required|min_length[6]|max_length[45]||is_unique[pp_purchase_plan.sync_scheme_code]');
        $this->form_validation->set_rules('pp_name','Plan name','trim|required');
        $this->form_validation->set_rules('plan_type','Plan Type','trim|required');
        $this->form_validation->set_rules('pp_payable_type','Plan Payable Type','trim|required');
        $this->form_validation->set_rules('installment_cycle','Ins. Cycle','trim|required');
        if($ps->installment_cycle != 4 && $ps->installment_cycle != 5){
        	$this->form_validation->set_rules('total_installments','Ins. cycle Days','trim|required');
    	}
        if($ps->installment_cycle == 3){
			$this->form_validation->set_rules('ins_cycle_days','Ins. cycle Days','trim|required');
		}    
        //$this->form_validation->set_rules('pincode','Pincode','trim|required|numeric|exact_length[6]');
        $this->form_validation->set_message('is_unique', '%s is already available');
        return $this->form_validation;
    }
    
}
?>