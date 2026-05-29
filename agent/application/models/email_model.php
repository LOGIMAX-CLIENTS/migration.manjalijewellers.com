<?php
class Email_model extends CI_Model {
// 	function Email_model() {
// 		parent::__construct();
// 	}
	public function send_email($email_to,$email_subject,$email_message,$email_cc="",$email_bcc="",$attachment="") {
	
		 $result = array();
	     $sql="SELECT * FROM company";
	     $result = $this->db->query($sql)->result_array(); 
		 
		 // 0-php mail 1-smtp gmail
		 if($result[0]['send_through']== 0) {
		 	return $this->php_mail($email_to,$email_subject,$email_message,$email_cc="",$email_bcc="",$attachment="");
		 }
		 else{
		 	return $this->send_smtp_gmail($email_to,$email_subject,$email_message,$email_cc="",$email_bcc="",$attachment="");
		 }
		
		
	}
		
	
	public function send_smtp_gmail($email_to,$email_subject,$email_message,$email_cc="",$email_bcc="",$attachment="")
	{
		$company_name = "";
		$mail_server = "";
		$mail_password = "";
		$resultset = $this->db->query("SELECT * FROM company");
		if($resultset->num_rows() >0)
		{
			$company_name	= $resultset->row()->company_name;
			$mail_server	= $resultset->row()->mail_server;
			$mail_password	= $resultset->row()->mail_password;
			
			$server_type	= $resultset->row()->server_type;
			$smtp_host	= $resultset->row()->smtp_host;
			$smtp_user	= $resultset->row()->smtp_user;
			$smtp_pass	= $resultset->row()->smtp_pass;
		}
		
		$resultset->free_result();
    		//NOTE:Shared and Reseller server packages do not support remote smtp. Update the SMTP Host to mi3-lr3.supercp.com and make sure the script is sending from an email address that is hosted on this account.
		$config = Array(
			  'protocol'  => 'sendmail',
			  'mailpath'      => "/usr/sbin/sendmail",
			  'smtp_host' => $smtp_host,
			  'smtp_port' => 465,
			  'smtp_user' => ($server_type == 1 ? $smtp_user : $mail_server),
			  'smtp_pass' => ($server_type == 1 ? $smtp_pass : $mail_password),
			  'mailtype'  => 'html',    
			  'charset'   => 'utf-8'
			);
			$this->load->library('email', $config);
			$this->email->set_newline("\r\n");
			// Set to, from, message, etc.				
			$this->email->from($mail_server, $company_name);
			$this->email->to($email_to); 				
			$this->email->subject($email_subject);
			$this->email->message($email_message);
			if($email_bcc!="")
			{
				$this->email->bcc($email_bcc);	
			}
			if($email_cc!="")
			{
				$this->email->cc($email_cc); 
			}
        
			if($attachment!="")
			{
				$this->email->attach($attachment);
			}
	//echo $this->email->print_debugger();exit;
		     return $this->email->send();
		    
			// echo $this->email->print_debugger();
	} 
	
	
	
		public function php_mail($email_to,$email_subject,$email_message,$email_cc,$email_bcc,$attachment="") {
	     $r = array();
	     $sql="SELECT * FROM company";
	     $r = $this->db->query($sql)->result_array();
		 
		 $config = array();
                $config['useragent']     = "CodeIgniter";
                $config['mailpath']      = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
                $config['protocol']      = "smtp";
                $config['smtp_host']     = "localhost";
                $config['smtp_port']     = "25";
                $config['mailtype']		 = 'html';
                $config['charset'] 		 = 'utf-8';
                $config['newline'] 		 = "\r\n";
                $config['wordwrap']		 = TRUE;
                $this->load->library('email');
                $this->email->initialize($config);
                $this->email->from($r[0]['mail_server'], $r[0]['company_name']);
                $this->email->to($email_to); 
				if($email_cc!="")
				{
					$this->email->cc($email_cc); 
				}
             
     			if($email_bcc!="")
				{

                   $this->email->bcc($email_bcc); 
			    }
                $this->email->subject($email_subject);
       
            $this->email->message($email_message);  
            //$this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
           return $this->email->send();
            
			 
	}
	
}
?>