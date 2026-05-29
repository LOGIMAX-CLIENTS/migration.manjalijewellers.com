<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require(APPPATH.'libraries/REST_Controller.php');
class Payment extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("payment_model");
	}
		
	function payDuesData_get()
    {
        $resultArr = array();
        try 
        {
            if ($this->get('id_customer'))
            {          
            	$success 	= true;
                $message 	= "Records retrieved successfully";    
                //$resultArr 	= $this->payment_model->get_payment_details($this->get('id_customer'));
                $resultArr 	= $this->payment_model->getCusAccounts($this->get('id_customer'));
            }
            else
            {
                $errArr  = $form_validation->error_array();
                $success = false;
                $message = reset($errArr);
            }
        }
        catch(Exception $e) 
        {
            $this->db->trans_rollback();
            $success = false;
            $message = $e->getMessage();
        }
        $result = array("success" => $success, "message" => $message, 'data' => $resultArr);
        $this->response($result, 200);
    }
    
}
?>