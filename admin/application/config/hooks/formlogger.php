<?php
class Formlogger {

    private $CI;

    public function __construct() {
        
        $this->CI = &get_instance();
        
        $this->CI->load->database(); // Load the database library

        $this->CI->load->library('input');
        
        $this->CI->load->library('user_agent');
    
    }

    public function logFormData() {

        $formIdentifier = $this->CI->uri->uri_string();

        $logData = array(
        
            'log_datetime'  => date('Y-m-d H:i:s'),
        
            'log_emp_id'    => $this->CI->session->userdata('uid'),

            'log_form'      => $formIdentifier,
        
            'log_url'       => $formIdentifier,

            'log_ip'        => $this->input->ip_address(),

            'log_useragent' => $this->agent->agent_string(),

            'log_data'      => json_encode($data),
        
        );

        $insertResult = $this->CI->db->insert("form_logger", $logData);

    }

}
