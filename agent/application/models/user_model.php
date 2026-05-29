<?php
class User_model extends CI_Model {
	var $table_name = 'customer';		//Initialize table Name
	public function __construct() {
		$this->load->database();
		$this->load->model('sms_model');
	}	
	// General Functions
    public function insertData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert($table, $data);
		return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	}
	public function updateData($data, $id_field, $id_value, $table)
    {    
	    $edit_flag = 0;
	    $this->db->where($id_field, $id_value);
		$edit_flag = $this->db->update($table,$data);
		return ($edit_flag==1?$id_value:0);
	}	 
	public function deleteData($id_field,$id_value,$table)
    {
        $this->db->where($id_field, $id_value);
        $status= $this->db->delete($table); 
		return $status;
	}
	
	function empty_record()
	{	
		$records[] = array(
					'username' 	=> NULL, 
					'passwd' 	=> NULL
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
	function validateUser()
	{		
		$query_validate = $this->db->query('SELECT ag.kyc_status,b.name as branch,ag.firstname, ag.passwd,ag.id_branch, ag.id_agent,ag.firstname  
                                    		FROM agent ag
                                    		LEFT JOIN branch b on (ag.id_branch=b.id_branch)
                                    		WHERE ag.active=1 AND ag.mobile = "'.($_POST['username']).'" AND ag.passwd = "'.($this->__encrypt($_POST['passwd'])).'"');

		if($query_validate->num_rows() > 0)
		{
			$flag = 1; 		
			foreach($query_validate->result() as $row)
			{
				$cus_id = $row->id_agent;
				$display_name = $row->firstname;
				$id_branch = $row->id_branch;
                $branch = $row->branch;
                $kyc = $row->kyc_status;
                $agent_code = $row->agent_code;
                
                $check_kyc_added = $this->db->query('SELECT count(*) as kyc from kyc where id_agent ='.$row->id_agent);
                $kycs_count = $check_kyc_added->row_array();
			}
		}
		else
		{
			$flag = 0;
			$kycs_count= 0;
			$cus_id = '';
			$display_name = '';
			$id_branch = '';
			$kyc = $row->kyc_status;
			$agent_code = $row->agent_code;
		}
		$query_validate->free_result();
		return array('status' => $flag,'id_branch'=>$id_branch,'kyc_count' => $kycs_count['kyc'], 'branch_name'=>$branch,'cus_id' => $cus_id,'display_name' => $display_name,'kyc_status' => $kyc,'agent_code' => $agent_code);
	}
	function validateActiveUser()
	{		
    	$query_validate = $this->db->query('SELECT username, passwd, id_customer,firstname  FROM customer WHERE active=0 AND mobile = "'.($_POST['username']).'" AND passwd = "'.($this->__encrypt($_POST['passwd'])).'"');
		if($query_validate->num_rows() > 0)
		{
			return true;
		}
	}
	function check_scheme($cus_id)
	{
		$scheme_selected = 0;
		$query_scheme = $this->db->query('SELECT * FROM scheme_account WHERE is_closed = 0 AND id_customer='.$cus_id);
				if($query_scheme->num_rows() > 0)
				{
					$scheme_selected = 1;
				}
			$query_scheme->free_result();
		return $scheme_selected;
	}
	function forgetUser($mble)
	{
		$query = $this->db->query('SELECT firstname, mobile,email, passwd  FROM customer WHERE  mobile = "'.$mble.'"');
			if($query->num_rows() > 0)
			{
					$row = $query->row(); 
					$this->session->unset_userdata("OTP");
					$OTP = mt_rand(1001,9999);
					$this->session->set_userdata('OTP',$OTP);
					 $mobile = $row->mobile;
					 $message="Dear ".$row->firstname.", Your otp to reset password is ".$OTP." ";
				//	 $this->send_sms($mobile,$message);
					 
					 if($this->config->item('sms_gateway') == '1'){
								$this->sms_model->sendSMS_MSG91($mobile,$message,'','');		
							}
							elseif($this->config->item('sms_gateway') == '2'){
								$this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
							}
					 
				/*	$to = $row->email;
					$data['otp']=$OTP;
					$data['type'] = 0;
					$data['company_details'] = $this->comp;
					$data['name'] = $row->firstname;
					$subject = "Reg: ".$this->comp['company_name']." saving scheme forgot password";
					$message = $this->load->view('include/emailAccount',$data,true);
					$this->load->model('email_model');
					$sendEmail = $this->email_model->send_email($to,$subject,$message);*/
			return 1;
			}
			else{
			       return 0;
			}
	}
	function forgot_pswd_reset($mobile)
	{
		$resultset = $this->db->query("UPDATE customer SET passwd ='".$this->__encrypt($this->input->post('passwd'))."' WHERE  mobile='".$mobile."'");
		if ($this->db->affected_rows() > 0)	
		{
			return 1;
		}
		else	
		{
			return 0;
		}
	}
	/*function metal_rates()
	{
		$last_row=$this->db->select('*')->order_by('id_metalrates',"desc")->limit(1)->get('metal_rates')->row_array();
		 return $last_row;
	}*/
	function metal_rates()
	{
		$id_branch=$this->session->userdata('id_branch');
		$data=$this->get_settings();
		if($data['is_branchwise_rate']==1 &&$id_branch!='')
		{
			$sql="select * from metal_rates m
	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
	   		where br.id_branch=".$id_branch." order by  br.id_metalrate desc limit 1";
		}
		else if($data['is_branchwise_rate']==1)
		{
			$sql="select * from metal_rates 
			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates 
			where br.status=1";
		}
		else
		{
			$sql="select * from metal_rates 
			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates order by id_metalrates desc limit 1";
		}
		$result = $this->db->query($sql);	
		// print_r($sql);exit;
		return $result->row_array();
	}
	function get_settings()
	{
		$sql="select * from chit_settings cs";
	//	print_r($sql);exit;
		$result = $this->db->query($sql);	
		return $result->row_array();
	}
 // scheme group//
	function company_details()
	{
		$sql = "Select  c.id_company,c.company_name,c.short_code,cs.enable_dth,c.pincode,c.mobile,c.mobile1,c.phone,c.phone1,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name,cs.mob_code,cs.mob_no_len,cs.has_lucky_draw,c.tollfree1,
		cs.allow_wallet,cs.enableGoldrateDisc,cs.enableSilver_rateDisc,cs.is_branchwise_cus_reg,cs.branch_settings,cs.is_branchwise_rate,cs.enable_coin_enq,cs.cusName_edit,cs.vs_enable,cs.is_multi_commodity,comp_name_in_sms
				from company c
					join chit_settings cs
					left join country cy on (c.id_country=cy.id_country)
					left join state s on (c.id_state=s.id_state)
					left join city ct on (c.id_city=ct.id_city) ";
		$result = $this->db->query($sql);	
//		print_r($result->row_array());die;
		return $result->row_array();
	}
	function discSettings(){
		$sql = "Select cs.enableGoldrateDisc,cs.enableSilver_rateDisc,cs.is_branchwise_cus_reg,cs.branch_settings,cs.is_branchwise_rate
				from company c
					join chit_settings cs
					left join country cy on (c.id_country=cy.id_country)
					left join state s on (c.id_state=s.id_state)
					left join city ct on (c.id_city=ct.id_city) ";
		$result = $this->db->query($sql);	
//		print_r($result->row_array());die;
		return $result->row_array();
	}
	function customer_data($mobile)
	{
		$sql = " Select  * from customer where mobile=".$mobile;
		$result = $this->db->query($sql);	
		return $result->row_array();
	}
	function site_mode()
	{
		$sql   = "Select maintenance_mode,maintenance_text from chit_settings";
	    return $this->db->query($sql)->row_array(); 
	}
	//Promotion sms and otp setting	
	function send_sms($mobile,$message)	
	{
		$url = $this->sms_data['sms_url'];		
		$senderid  = $this->sms_data['sms_sender_id'];
		if(($this->sms_chk['debit_sms']!=0)){	
		$arr = array("@customer_mobile@" => $mobile,"@message@" => str_replace(array("\n","\r"), '', $message),"@senderid@" => $senderid);		
		$user_sms_url = strtr($url,$arr); 
		$ch = curl_init();	
		curl_setopt($ch, CURLOPT_URL, $user_sms_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);	
		$result = curl_exec($ch);	
		curl_close($ch);
		unset($ch);	
		$status=$this->update_otp();	
		if($status==1){	
		return TRUE;	
		}		
		return FALSE;	
		}else{	
			return FALSE;
			}	
	}	
		function update_otp()  {
			$query_validate=$this->db->query('UPDATE sms_api_settings SET debit_sms = debit_sms - 1 				
			WHERE id_sms_api =1 and debit_sms > 0'); 
			if($query_validate>0){			
			return true;
			}else{
				return false;
				} 
		     } 
	//Promotion sms and otp setting		
// branch_settings
	function branch_settings()
	{
		$sql = "SELECT is_kyc_required,is_agent_kyc_required,branch_settings,branchWiseLogin,is_branchwise_cus_reg,branchwise_scheme,cost_center FROM chit_settings  where id_chit_settings =1";
	    return $this->db->query($sql)->row_array(); 
	}
// branch_settings
      function get_branchcompany($id)
	  {
			$sql="SELECT b.id_branch, b.name, b.active, b.short_name, b.metal_rate_type,b.address1,cs.currency_symbol, b.address2, b.phone, b.mobile, b.pincode,c.name as country,s.name as state, ct.name as city FROM branch b
									 LEFT JOIN country c on c.id_country= b.id_country
									 LEFT JOIN state s on s.id_state= b.id_state
									 LEFT JOIN city ct on ct.id_city= b.id_city
									 JOIN chit_settings cs
									 where b.id_branch=".$id."";
			$result=  $this->db->query($sql)->row_array('id');
			  return $result;
	  }
    //rate history
    function rate_history($type='')
	{
		$from_date =isset($_POST['from_date']) ? $_POST['from_date'] : "";
	 	$To_date =isset($_POST['To_date']) ? $_POST['To_date'] : "";
	 	$id_branch =isset($_POST['id_branch']) ? $_POST['id_branch'] : "";
	    /*$from_date_validate=strtotime('01/01/2020');
	    $to_date_validate=strtotime($from_date);
		if($from_date_validate<=$to_date_validate)*/
		if($type == 'lmx')
		{
    		$data=$this->get_settings();
    		if($data['is_branchwise_rate']==1 && $id_branch!='' )
    		{
    			$sql="select date_format(updatetime,'%d-%m-%Y %H:%i:%s') as updatetime,goldrate_22ct,silverrate_1gm,platinum_1g from metal_rates m
    	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
    	   		where br.id_branch=".$id_branch." and date(updatetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($To_date))."' order by  br.id_metalrate desc ";
    		}
    		else if($data['is_branchwise_rate']==1)
    		{
    			$sql="select date_format(updatetime,'%d-%m-%Y %H:%i:%s') as updatetime,goldrate_22ct,silverrate_1gm,platinum_1g from metal_rates 
    			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates 
    			where br.status=1 and date(updatetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($To_date))."'";
    		}
    		else
    		{
    			$sql="select date_format(updatetime,'%d-%m-%Y %H:%i:%s') as updatetime,goldrate_22ct,silverrate_1gm,platinum_1g from metal_rates 
    			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates where date(updatetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($To_date))."'order by id_metalrates desc ";
    		}
	    }elseif($type == 'ej')
	    {
	        $sql="select TRANSDATE as updatetime,if(m.Metaltype='Gold',m.RATE,'0.00')as goldrate_22ct,if(m.Metaltype='Silver',m.Rate,'0.00')as silverrate_1gm,
	                     if(m.Metaltype='Platinum',m.RATE,'0.00')as platinum_1g 
	                     from ej_metalratehistory m
	                     where (date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($To_date))."' ".($id_branch!='' ? " and m.id_branch=".$id_branch."" :'')." ORDER by TRANSDATE ASC";
	           // echo $sql;exit;
	    }  
	    else{
	        return array();
	    }
		$result = $this->db->query($sql);
		return $result->result_array();
	}
//branch rate login page header
	function metal_ratess($id_branch)
	{
		//$from_date = $_POST['from_date']);
		//$To_date = $_POST['To_date']);
		//$id_branch=$this->session->userdata('id_branch');
		$data=$this->get_settings();
		//print_r($id_branch);exit;
		if($data['branch_settings']==1 && $id_branch!='' )
		{
			$sql="select * from metal_rates m
	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
	   		where br.id_branch=".$id_branch." order by  br.id_metalrate desc limit 1";
		}
		else if($data['branch_settings']==1)
		{
			$sql="select * from metal_rates 
			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates 
			where br.status=1";
		}
		else
		{
			$sql="select * from metal_rates 
			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates order by id_metalrates desc limit 1";
		}
		$result = $this->db->query($sql);	
		//print_r($this->db->last_query());exit;
		return $result->row_array();
	}
	//branch rate login page header
	function getModules(){
	    $modules = [];
	    $sql = $this->db->query("SELECT m_code,m_active,m_web FROM modules");
	    foreach($sql->result_array() as $r){
	        if($r['m_active'] == 1 && $r['m_web'] == 1){
                $modules[$r['m_code']] = 1;
	        }else{
	            $modules[$r['m_code']] = 0;
	        }
        }
        //echo "<pre>";print_r($modules);exit;
        return $modules;
	}
	
	function genTicketNo()
	{
		$sql="select max(ticket_no) as last_ticket_no from cust_enquiry";
		$result = $this->db->query($sql);	
	    $last_ticket_no = $result->row('last_ticket_no');
	    if($last_ticket_no == NULL){
	        $ticket_no = 1;
	        return str_pad($ticket_no, 6, '0', STR_PAD_LEFT);
	    }else{
	        $ticket_no = $last_ticket_no+1;
	        return str_pad($ticket_no, 6, '0', STR_PAD_LEFT);
	    }
	}
	
	function insCusFeedback($data)  
    {
		//print_r($data);exit;
		$status = $this->db->insert('cust_enquiry',$data);
		return array('status' => $status, 'insertID' => $this->db->insert_id());
	}
	
	function pageContent(){
		$sql="select name,content from general where type=4";
		$result = $this->db->query($sql);	
		return $result->row_array();
	}
	

	function agent_details(){
		$username = $this->session->userdata('username');
		$sql="select * from agent ag LEFT JOIN address ad ON (ag.id_agent = ad.id_agent) where ag.mobile =".$username;
		$result = $this->db->query($sql);	
		return $result->row_array();
	}
   function update_agent($data,$id)
   {
       
       
   	 $status=0;
   	  $this->db->where("id_agent",$id);	
   	  $status=$this->db->update("agent",$data);
   	///  print_r($this->db->last_query());exit;
   	 
   	 return ($status == 1 ?$id:0);
   } 
   function update_agent_address($data,$id)
   {
	   $status=0;
		$this->db->where('id_agent',$id);
		$q = $this->db->get('address');
		
		if ($q->num_rows() > 0) {
			$this->db->where('id_agent',$id);
			$status = $this->db->update('address',$data);
		}else{
			$this->db->insert('address',$data);
			$id_address=$this->db->insert_id();
			$add_id = array("id_address" => $id_address);
			$this->db->where("id_agent",$id);	
			$status=$this->db->update("agent",$add_id);
		} 

		//print_r($this->db->last_query());exit;
		return ($status == 1 ?$id:0);
   } 
   
   function checkAgentKycStatus()
   {
       $username = $this->session->userdata('username');
       if($username != '')
       {
    		$sql="select ag.kyc_status,count(k.id_agent) as kyc_count from agent ag 
    		LEFT JOIN kyc k ON (k.id_agent = ag.id_agent) where ag.mobile =".$username;
    		$result = $this->db->query($sql);	
    		return $result->row_array();
       }else{
           $result = array('kyc_status'=> '','kyc_count' => '');
           return $result;
       }
   }

}
?>