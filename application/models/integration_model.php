<?php
class Integration_model extends CI_Model {
    
    
    // General Functions
	public function insertData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert($table,$data);
		return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	}
	
	public function updateData($data,$id_field,$id_value,$table)
    {    
	    $edit_flag = 0;
	    $this->db->where($id_field,$id_value);
		$edit_flag = $this->db->update($table,$data);
		return ($edit_flag==1?$id_value:0);
	}	 
	public function deleteData($id_field,$id_value,$table)
    {
        $this->db->where($id_field, $id_value);
        $status= $this->db->delete($table); 
		return $status;
	} 
	
    function khimji_curl($api,$postData)
    {
    	$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config->item('khimji-baseURL')."".$api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            // Getting  server response parameters //
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Content-Type: application/json",
                "X-Key: ".$this->config->item('khimji-X-Key'),
                "Authorization: ".$this->config->item('khimji-Authorization')
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            if (!file_exists('../log/khimji')) {
                mkdir('../log/khimji', 0777, true);
            }
            $log_path = '../log/khimji/'.date("Y-m-d").'.txt';  
            $logData = "\n".date('d-m-Y H:i:s')."\n API : ".$api." \n POST : ".json_encode($postData,true)."\n Error : ".json_encode($err,true);
		    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
		    //echo "<pre>";print_r($postData);echo "<pre>";print_r($err);exit;
            return array('status' => FALSE, 'data' => $err);
        } 
        else {
            //echo "<pre>";print_r($postData);echo "<pre>";print_r($response);exit;
            return array('status' => TRUE, 'data' => json_decode($response));
        }
    }
    
    function isAccExist($data){
        $sql = $this->db->query("SELECT id_scheme_account from scheme_account where scheme_acc_number='".$data->schemeNo."'");
        if($sql->num_rows() > 0){
            return array("status" => true, "id_scheme_account" => $sql->row()->id_scheme_account);
        }else{
            return array("status" => false, "id_scheme_account" => NULL);
        }
    }
    
    function isPayExist($data){
        $sql = $this->db->query("SELECT id_payment from payment where receipt_no='".$data->voucherNo."'");
        if($sql->num_rows() > 0){
            return array("status" => true, "id_payment" => $sql->row()->id_payment);
        }else{
            return array("status" => false, "id_payment" => NULL);
        }
    }
    
    function getAccInsData($data,$id_customer){
        $cus = $this->db->query("SELECT concat(firstname,' ',if(lastname!=NULL,lastname,'')) as ac_name from customer where id_customer=".$id_customer);
        $result['ac_name'] = $cus->row()->ac_name;
        
        /*$brn = $this->db->query("SELECT id_branch from branch where warehouse=".$data->branchCode);
        $brn->row()->id_branch;*/
        $result['id_branch'] = 1;
        
        $sch = $this->db->query("SELECT id_scheme from scheme where sync_scheme_code='".$data->schemeCode."'");
        if($sch->num_rows() > 0){
            $result['id_scheme'] = $sch->row()->id_scheme;
        }else{
            $result['id_scheme'] = "";
        }
        
        return $result;
    }
}
?>
