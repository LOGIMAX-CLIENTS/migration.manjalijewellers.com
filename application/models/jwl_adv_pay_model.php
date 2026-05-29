<?php
class Jwl_adv_pay_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	function insertData($insData,$table)
    {
		$status = $this->db->insert($table,$insData); 
		return array('status' => $status, 'insertID' => $this->db->insert_id());
	}
	
	public function updateData($data,$id_field,$id_value,$table)
    {    
	    $edit_flag = 0;
	    $this->db->where($id_field,$id_value);
		$edit_flag = $this->db->update($table,$data);
		return ($edit_flag==1?$id_value:0);
	}
	
	function getSettings($m_code)
    {
		$sql = $this->db->query("SELECT m_active,m_web,m_app FROM modules where m_code='".$m_code."'"); 
		return $sql->row_array();
	} 
	
	function getCusByMob($mobile)
	{
		$sql = $this->db->query("
			SELECT 
				title,firstname,c.mobile,c.email,c.id_branch
			FROM customer c 
			WHERE c.active=1 AND c.mobile =".$mobile 
		); 
	    return $sql->row_array(); 
 	} 
 	
 	function insert_data($insData)
	{  
		if($this->db->insert('purchase_customer', $insData))
		{
		    $insertID = $this->db->insert_id();
            $status = array("status" => true, "insertID" => $insertID);
		}
		else
		{
			$status = array("status" => false, "insertID" => '');
		}
		return $status;
	}
	
 	
 	function check_mobileno($mobile)
	{
		$query = $this->db->query("SELECT mobile,firstname,title,email,id_branch,alter_mobile FROM purchase_customer WHERE mobile=".$mobile);
		if($query->num_rows() > 0)
		{
			return array('status' => false, 'data' => $query->row_array());
		}
		else
		{
			return array('status' => true, 'data' => []);
		}
	}
	
	function getCustomPurchasePlans($id){
		$sql = $this->db->query(" select id_purch_payment,type,no_of_month,offer_name,adv_paid_percent,disc_mc_percent,DATE_FORMAT(date_add,'%d-%m-%Y') as date_add,mobile,ref_trans_id,payment_amount,metal_weight,metal_rate,payment_status as status from purchase_payment where (payment_status=1 or payment_status=2 ) and mobile=".$this->session->userdata('jap_mobile')." ".
		($id != '' ? 'and id_purch_payment='.$id:''));
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}
	
	function get_currency($id_branch)
	{
		if($id_branch != '')
		{
			$sql = "SELECT  m.goldrate_22ct,m.silverrate_1gm 
                FROM metal_rates m 
                LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
                left join branch b on b.id_branch=br.id_branch
                ".($id_branch!='' ?" WHERE br.id_branch=".$id_branch."" :'')." ORDER by br.id_metalrate desc LIMIT 1";
                
            	$rate = $this->db->query($sql);	  
		    $result['metal_rates'] = $rate->row_array();
		}
		else
		{
		    $result['metal_rates']['goldrate_22ct'] =0.00;
		    $result['metal_rates']['silverrate_1gm'] =0.00;
		}
		return $result;
	}
	
	function getBranchGatewayData($branch_id,$pg_code){
		if($branch_id == ''){
			$sql="SELECT id_pg,param_1,param_2,param_3,param_4,pg_code from gateway where is_default=1 and pg_code=".$pg_code;
		}else{
			$sql="SELECT id_pg,param_1,param_2,param_3,param_4,pg_code from gateway where is_default=1 and pg_code=".$pg_code." and id_branch=".$branch_id;
		}
   		
		//print_r($sql);exit;
		$result=  $this->db->query($sql)->row_array();
		return $result;   	
   }
    
    //update gateway response
	function updateGatewayResponse($data,$txnid)
	{
		$this->db->where('ref_trans_id',$txnid); 
		$status = $this->db->update('purchase_payment',$data);	 
		$result=array(
		              'status' => $status,
		             'payData' => $this->get_PayDataById($txnid) 
		              );
		
		return $result;
	}
	function get_PayDataById($txnid)
	{
		$this->db->select('type,metal_weight,payment_amount,metal_rate');  
		$this->db->where('ref_trans_id',$txnid); 
		$payid = $this->db->get('purchase_payment');	
		return $payid->row_array();
	}
	
	function get_branch()
    {		
	    $branch=$this->db->query("SELECT b.name,b.id_branch,b.address1,b.address2,b.active FROM branch b where (show_to_all=0 or show_to_all=1 or show_to_all=2 or active=0)");
		return $branch->result_array();	
	}
    
    function get_branch_by_id($id_branch)
    {		
	    $branch=$this->db->query("SELECT b.name,b.id_branch FROM branch b where b.id_branch=".$id_branch."");
		return $branch->row_array();	
	}
	
	function get_ATmailData($txt_id)
    {
    	 $sql="select id_purch_payment, c.firstname as name,c.mobile,c.id_purch_customer,if(p.type=1,'Amount','Weight')as type_value,type,
    	 p.payment_amount,IFNULL(metal_weight,'-') as metal_weight,psm.payment_status as payment_status,p.payment_status as pay_status,ref_trans_id,p.remark,c.email,
    	 date_format(p.date_add,'%d-%m-%Y') as date_add,iFNULL(p.id_transaction,'-') as id_transaction,p.payment_mode,c.id_branch,b.name as branch_name,IFNULL(p.payment_ref_number,'-')as payment_ref_number,p.metal_rate,IFNULL(pan_no,'-') as pan_no
    	 from purchase_customer c
    	 left join purchase_payment p on p.mobile=c.mobile
    	 left join branch b on b.id_branch=c.id_branch
    	 Left Join payment_status_message psm On p.payment_status=psm.id_status_msg
    	 Where p.ref_trans_id='".$txt_id."'";
    	 //echo $sql;exit;
    	 return $this->db->query($sql)->row_array();
    }
	
	
}	
?>