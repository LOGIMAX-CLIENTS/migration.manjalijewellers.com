<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Errors extends CI_Controller {
const VIEW_FOLDER = 'chitscheme/';
 public function __construct() 
    {
        parent::__construct(); 
    } 

    public function index() 
    { 
        $this->output->set_status_header('404'); 
		$data['content'] = ''; 
		$pageType = array('page' => 'errors');
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'errors';
		$this->load->view('layout/template', $data);
    } 
}

/* End of file errors.php */
/* Location: ./application/controllers/welcome.php */