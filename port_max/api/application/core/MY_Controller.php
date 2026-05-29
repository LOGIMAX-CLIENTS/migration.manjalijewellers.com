<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct(){
        parent::__construct();
    }
    /* protected function loadClientDB()
    {
        $this->load->library('session');
        $config = $this->session->userdata('db_config');
        // print_r($config);exit;
        if (empty($config)) {
            echo json_encode([
                'success' => false,
                'message' => 'Database not connected'
            ]);
            exit;
        }

        $this->db = $this->load->database($config, TRUE);
    } */
}
