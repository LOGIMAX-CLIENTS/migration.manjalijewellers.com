<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require(APPPATH.'libraries/REST_Controller.php');
class pp_account extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("payment_model");
	}
		
	function sample_post()
    {
        $resultArr = array();
        try 
        {
        	$postData	= get_values();
            if (!empty($postData['id_customer']))
            {          
            	$success 	= true;
                $message 	= "Records retrieved successfully";    
                $resultArr 	= $this->payment_model->getCusAccounts($postData['id_customer'],$postData['id_scheme_account']);
            }
            else
            {
                $success = false;
                $message = "Invalid customer ID";
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