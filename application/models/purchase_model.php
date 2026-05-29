<?php
class Purchase_model extends CI_Model {
	var $table_name = 'customer';						//Initialize table Name
	const CUS_IMG_PATH ='admin/assets/img/customer/';
	const DEF_CUS_IMG_PATH = 'admin/assets/img/default.png/';
	const DEF_IMG_PATH = 'admin/assets/img/no_image.png/';
	const CUS_IMG= 'customer.jpg';
	const PAN_IMG = 'pan.jpg';
	const RATION_IMG = 'rationcard.jpg';
	const VOTERID_IMG = 'voterid.jpg';
	function empty_record()
	{	
		$records[] = array(
		            'title'         =>NULL,
					'firstname' 	=> NULL, 
					'lastname' 		=> NULL,
					'email' 		=> NULL, 
					'passwd' 		=> NULL,
					'mobile' 		=> NULL
					);	
		return $records;
	}
	
		//encrypt
	public function __encrypt($str)
	{
		return base64_encode($str);		
	}	
	
	//decrypt
	public function __decrypt($str)
	{
		return base64_decode($str);		
	}
	
	//sort array
	function array_sort($array, $on, $order=SORT_ASC){
		$new_array = array();
		$sortable_array = array();
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
				
			}
			
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
			foreach ($sortable_array as $k => $v) {
				//$new_array[$k] = $array[$k];
				$new_array[] = $array[$k];
			}
		}
		return $new_array;
	}
	
	
	
	function check_mobileno($mobile)
	{
		$query = $this->db->query("SELECT mobile,firstname,title,email FROM purchase_customer WHERE mobile=".$mobile);
		if($query->num_rows() > 0)
		{
			return array('status' => false, 'data' => $query->row_array());
		}
		else
		{
			return array('status' => true, 'data' => []);
		}
	} 
	function clientEmail($id) 
	{
		$resultset = $this->db->query("select email from purchase_customer where email='".$id."'");
		if ($resultset->num_rows() > 0)	
		{
			return 1;
		}
		else	
		{
			return 0;
		}
	} 
	
	function insertdata($insData,$table)
    {
		$status = $this->db->insert($table,$insData); 
		return array('status' => $status, 'insertID' => $this->db->insert_id());
	}
	
	function insert_data($code="")
	{  

		$cusInsert  = array(
			'info'=>array(	//"title" => $this->input->post('title'),
							//"firstname" => ucfirst($this->input->post('firstname')),
							"mobile" => trim($this->input->post('mobile')),
							//"email" => $this->input->post('email'),  
							"verified_otp" => $this->input->post('otp'),  
							"created_on" => date('Y-m-d H:i:s'), 
			) 
		);
		
		if($this->db->insert('purchase_customer', $cusInsert['info']))
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
	
	function update_data($title,$firstname,$otp,$mobile)
	{		
		if($title != '' )
		$updData["title"] = $title;
		if($firstname != '' )
		$updData["firstname"] = $firstname;
		if($otp != '' )
		$updData["verified_otp"] = $otp;
		
		$updData["updated_on"] = date('Y-m-d H:i:s');
		$this->db->where('mobile', $mobile);
		$status = $this->db->update('purchase_customer', $updData);
		return $status;
	}
	
	
	function getCustomPurchasePlans(){
		$sql = $this->db->query(" select DATE_FORMAT(date_add,'%d-%m-%Y') as date_add,mobile,ref_trans_id,payment_amount,metal_weight,metal_rate,payment_status as status from purchase_payment where payment_status=1 and mobile=".$this->session->userdata('purch_mbl'));
		return $sql->result_array();
	}
	
	/*function getWgtWithRate($branch){
		$weights = array(); 
		$weights_data = $this->get_weights();
		$metalrates = $this->get_currency($branch);	
		
		foreach($weights_data as $weight) 
	    {
	    	$rate = (float) $metalrates['metal_rates']['goldrate_22ct'] * (float) $weight['weight'];
			$weights[]=array(
								'id_weight' => $weight['id_weight'],
								'weight'    => $weight['weight'],
								'rate'      => number_format($rate,2,'.','')
							);
		}
		$weights = $this->array_sort($weights, 'id_weight',SORT_DESC);
		return $weights;
	}
	
	function get_weights()
	{
		$this->db->select('*');
		$this->db->where('active',1);
		return $this->db->get('weight')->result_array();
	}*/
	
	function get_currency($id_branch)
	{
		if($id_branch != ''){
			$sql = "SELECT  m.goldrate_22ct 
                FROM metal_rates m 
                LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
                left join branch b on b.id_branch=br.id_branch
                ".($id_branch!='' ?" WHERE br.id_branch=".$id_branch."" :'')." ORDER by br.id_metalrate desc LIMIT 1";
		}else{ 
			$sql ="SELECT m.goldrate_22ct FROM metal_rates m 
				WHERE m.id_metalrates=( SELECT max(m.id_metalrates) FROM metal_rates m )"; 
		}
		
		$rate = $this->db->query($sql);	  
		$result['metal_rates'] = $rate->row_array();
		 
		return $result;
	}
	
	function getBranchGatewayData($branch_id,$pg_code){
		if($branch_id == ''){
			$sql="SELECT param_1,param_2,param_3,param_4,pg_code from gateway where is_default=1 and pg_code=".$pg_code;
		}else{
			$sql="SELECT param_1,param_2,param_3,param_4,pg_code from gateway where is_default=1 and pg_code=".$pg_code." and id_branch=".$branch_id;
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
    
    
    
}
?>