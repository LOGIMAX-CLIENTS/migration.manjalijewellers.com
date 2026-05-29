<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Crm_mas_scheme_model extends CI_Model
{
	function __construct()
    {
        parent::__construct();
    }
    
    public function get_schemes_list()
	{
		$this->db->select("id_scheme,scheme_name,code,sync_scheme_code,total_installments,active,date_add,
	(CASE
		WHEN scheme_type = 0 THEN 'Fixed Amount'
	    WHEN scheme_type = 1 THEN 'Weight'
	    WHEN scheme_type = 2 THEN 'Amount to weight'
	    WHEN scheme_type = 3 THEN 'Flexible'
	END) as scheme_type,
	(CASE
	    WHEN flexible_sch_type = 1 THEN 'Amt'
	    WHEN flexible_sch_type = 2 THEN 'Amt to Wgt [Limit by Amt]'
	    WHEN flexible_sch_type = 3 THEN 'Amt to Wgt [Limit by Wgt]'
	    WHEN flexible_sch_type = 4 THEN 'Wgt [Limit by Wgt]'
	    WHEN flexible_sch_type = 5 THEN 'Wgt [Limit by Amt]'
	    WHEN flexible_sch_type = 6 THEN 'Amt [Partly Flexible]'
	    WHEN flexible_sch_type = 7 THEN 'Wgt [Partly Flexible]'
	END) as flexible_sch_type,
	(CASE
	    WHEN maturity_type = 1 THEN 'Flexible [Can pay installments and close]'
	    WHEN maturity_type = 2 THEN 'Fixed [Based on Maturity Days]'
	    WHEN maturity_type = 3 THEN 'Fixed Flexible [Increase maturity if has Default]'
	END) as maturity_type
	");
		$data=$this->db->get('scheme');
		//echo "<pre>".$this->db->last_query();
		//echo "<pre>".$this->db->_error_message();exit;
		return $data->result_array();
	} 
	
	function scheme_count()
	{
		$sql = "SELECT id_scheme FROM scheme";
		return $this->db->query($sql)->num_rows();
	}
	
	public function empty_record()
    {
		$data=array();
		$data['common']  = array(
								"id_scheme"				=>	NULL,
								"scheme_name"			=>	NULL,
								"code"					=>	NULL,
								"sync_scheme_code"		=>	NULL,
								"visible"				=>	1, // Show to all
								"active"				=>	1, // Active
								"is_enquiry"			=>	0, // Not enquiry, can join
								"has_gift"				=>	0, // No gift
								"show_ins_type"			=>	1, // paid / total
								"restrict_payment"		=>	0, // ins count
								"ins_type"				=>	1, // 1 - Monthly, 2 - Daily
								"total_installments"	=>	NULL, // 1 - Monthly, 2 - Daily
							//PAN / Aadhar No./Nominee Settings
								"is_pan_required"		=> 0, //Not Required
								"pan_req_amt"			=> NULL,
								"is_nominee_required"	=> 0, //Not Required
								"is_aadhaar_required"	=> 0, //Not Required
								"aadhaar_required_amt"	=> NULL,
							// Lucky Draw
								"is_lucky_draw"			=> 0, 
								"has_prize"				=> 0, 
								"max_members"			=> 0, 
							// Payment Charges
								"charge_head"			=> 0, 
								"charge_type"			=> 0, 
								"charge"				=> 0.00, 
								"noti_msg"				=> 0.00, 
								"description"			=> 0.00, 
								);
		return $data;
	}
}
?>