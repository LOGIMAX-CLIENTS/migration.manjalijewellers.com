<?php
class Email_model extends CI_Model {
    public $last_error = "";

	public function send_email($email_to,$email_subject,$email_message,$email_cc="",$email_bcc="",$attachment="", $embeddings = array()) {
		// Try to send via Gmail SMTP, fallback to logging if it fails
		return $this->send_via_gmail($email_to,$email_subject,$email_message,$email_cc,$email_bcc,$attachment, $embeddings);
	}
	
	/**
	 * Send email via Gmail SMTP
	 * Uses Gmail's SMTP server with App Password authentication
	 * Credentials are fetched from company table based on branch ID
	 */
	public function send_via_gmail($email_to, $email_subject, $email_message, $email_cc="", $email_bcc="", $attachment="", $embeddings = array()) {
				
		// Fetch company details from company table using id_company
		$company = $this->db->query("SELECT comp_name_in_sms, smtp_user, smtp_pass, smtp_host FROM company limit 1")->row_array();
		
		if(empty($company['smtp_user']) || empty($company['smtp_pass']) || empty($company['smtp_host'])) {
			$this->log_email_only($email_to, $email_subject, $email_message, $email_cc, $email_bcc, $attachment);
			return false;
		}
		
		// Gmail credentials from database
		$gmail_user = trim($company['smtp_user']);
		$gmail_pass = trim($company['smtp_pass']);  // App Password from database
		$smtp_host = trim($company['smtp_host']);   // SMTP host from database
		$from_name = trim($company['comp_name_in_sms']);  // Company name + Branch name
		
		// Gmail SMTP configuration
		$config = Array(
			'protocol'    => 'smtp',
			'smtp_host'   => $smtp_host,
			'smtp_port'   => 587,
			'smtp_user'   => $gmail_user,
			'smtp_pass'   => $gmail_pass,
			'smtp_crypto' => 'tls',
			'mailtype'    => 'html',
			'charset'     => 'utf-8',
			'newline'     => "\r\n",
			'crlf'        => "\r\n",
			'smtp_timeout'=> 30,
			'wordwrap'    => TRUE
		);
		
		$this->load->library('email');
		$this->email->initialize($config);
		
		$this->email->from($gmail_user, $from_name);
		$this->email->to($email_to);
		$this->email->subject($email_subject);
		$this->email->message($email_message);
		
		if($email_cc != "") { $this->email->cc($email_cc); }
		if($email_bcc != "") { $this->email->bcc($email_bcc); }
		if($attachment != "") { $this->email->attach($attachment); }

		// Handle CID (inline) embeddings
		if(!empty($embeddings)) {
			foreach($embeddings as $cid => $file_path) {
				if(file_exists($file_path)) {
					$this->email->attach($file_path, 'inline');
					// CodeIgniter 3.x uses CID based on the filename or explicitly if set
					// Most CI versions set the CID to the basename of the file automatically if 'inline' is provided
				}
			}
		}
		
		if($this->email->send()) {
			return true;
		} else {
			$this->last_error = $this->email->print_debugger();
			
			// Fallback: Log to file if email fails
			$this->log_email_only($email_to, $email_subject, $email_message, $email_cc, $email_bcc, $attachment);
			return false;
		}
	}
	
	/**
	 * Fallback: Log email to file if sending fails
	 */
	private function log_email_only($email_to, $email_subject, $email_message, $email_cc="", $email_bcc="", $attachment="") {
		$log_entry = "\n\n=== EMAIL LOG ===\n";
		$log_entry .= "Date: " . date('Y-m-d H:i:s') . "\n";
		$log_entry .= "To: " . $email_to . "\n";
		$log_entry .= "From: Gmail SMTP\n";
		$log_entry .= "Subject: " . $email_subject . "\n";
		if($email_cc) $log_entry .= "CC: " . $email_cc . "\n";
		if($email_bcc) $log_entry .= "BCC: " . $email_bcc . "\n";
		$log_entry .= "Message:\n" . strip_tags($email_message) . "\n";
		$log_entry .= "Error: " . $this->last_error . "\n";
		$log_entry .= "=================\n";
		
		$log_file = APPPATH . 'logs/email_log.txt';
		@file_put_contents($log_file, $log_entry, FILE_APPEND);
	}
	
	// Keep old methods for backward compatibility
	public function send_smtp_gmail($email_to,$email_subject,$email_message,$email_cc="",$email_bcc="",$attachment="", $embeddings = array())
	{
		return $this->send_via_gmail($email_to,$email_subject,$email_message,$email_cc,$email_bcc,$attachment, $embeddings);
	} 
	
	public function php_mail($email_to, $email_subject, $email_message, $email_cc, $email_bcc, $attachment = "", $embeddings = array()) {
		return $this->send_via_gmail($email_to,$email_subject,$email_message,$email_cc,$email_bcc,$attachment, $embeddings);
	}
}
?>