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
            CURLOPT_TIMEOUT => 0,
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
            return array('status' => FALSE, 'data' => $err);
        } 
        else {
            // echo "<pre>";print_r($postData);echo "<pre>";print_r($response);exit;
            return array('status' => TRUE, 'data' => json_decode($response));
        }
    }
    
    function khimji_curl_test($api,$postData)
    {
    	$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config->item('acme-baseURL')."".$api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            // Getting  server response parameters //
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Content-Type: application/json",
                "X-Key: ".$this->config->item('acme-X-Key'),
                "Authorization: ".$this->config->item('acme-Authorization')
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
            return array('status' => FALSE, 'data' => $err);
        } 
        else {
            // echo "<pre>";print_r($postData);echo "<pre>";print_r($response);exit;
            return array('status' => TRUE, 'data' => json_decode($response));
        }
    }
    
    function getPayData()
	{
	    $from   = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). ' -3 days')); 
	    $to     = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). ' -1 minutes'));
        
    	$sql    = "Select 
        	           c.mobile,c.firstname,p.id_transaction,p.ref_trans_id, p.offline_tran_uniqueid, b.warehouse, p.payment_amount, p.payment_type, p.payment_mode, sa.scheme_acc_number, sa.id_scheme_account, p.id_payment
        			 From payment p
            			 LEFT JOIN scheme_account sa on sa.id_scheme_account=p.id_scheme_account
            			 LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
            			 LEFT JOIN branch b ON b.id_branch = sa.id_branch
            			 LEFT JOIN customer c on c.id_customer = sa.id_customer
            			 join chit_settings cs
        			 Where p.added_by !=0 and p.added_by !=3 and is_offline = 0 and p.payment_status = 1 and receipt_no is null and ( date(date_payment) between '$from' and '$to' )
        			 limit 100";
        			 //echo $sql;exit;
        			 ///( date_payment between '2023-01-13' and '2023-01-13' )
    	return $this->db->query($sql)->result_array();	
	}
	
	function getPayDataById()
	{

    	$sql    = "Select 
        	           c.mobile,c.firstname,p.id_transaction,p.ref_trans_id, p.offline_tran_uniqueid, b.warehouse, p.payment_amount, p.payment_type, p.payment_mode, sa.scheme_acc_number, sa.id_scheme_account, p.id_payment
        			 From payment p
            			 LEFT JOIN scheme_account sa on sa.id_scheme_account=p.id_scheme_account
            			 LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
            			 LEFT JOIN branch b ON b.id_branch = sa.id_branch
            			 LEFT JOIN customer c on c.id_customer = sa.id_customer
            			 join chit_settings cs
        			 Where p.added_by !=0 and p.added_by !=3 and is_offline = 0 and p.payment_status = 1 and receipt_no is null and p.id_payment=".$_POST['payId']."
        			 limit 1";
        			 //echo $sql;exit;
        			 ///( date_payment between '2023-01-13' and '2023-01-13' )
    	return $this->db->query($sql)->result_array();	
	}
	
	function getEmptyCusCodeData($id_customer){
		 $cus = $this->db->query("SELECT 
		 							firstname,reference_no,app_cus_code,mobile,email,pan,ad.address1,ad.address2,
		 							st.name as state,ct.name as city,
		 							kyc.number as aadhaar_no
		 						FROM customer c
		 						LEFT JOIN address ad  ON (c.id_customer = ad.id_customer)
		 						LEFT JOIN state st on (ad.id_state=st.id_state)
								LEFT JOIN city ct  ON (ad.id_city = ct.id_city)
								LEFT JOIN kyc ON kyc.id_customer = c.id_customer and kyc_type = 3
		 						WHERE ".($id_customer > 0 ? '(c.added_by != 1 or c.id_customer ='.$id_customer : 'c.added_by != 1  ').") and (c.app_cus_code is NULL or c.app_cus_code = '')");
		 					//	echo $this->db->last_query();exit;
		 return $cus->result_array();
	}
}
?>
