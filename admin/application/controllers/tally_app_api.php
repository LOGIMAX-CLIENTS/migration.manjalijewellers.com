<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Retail Admin app api's
*/
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require(APPPATH.'libraries/REST_Controller.php');

class Tally_app_api extends REST_Controller
{
	const ADM_MODEL = "ret_tally_api_model";
	const EST_MODEL = "ret_estimation_model";
	
	function __construct()
	{
		parent::__construct();
		$this->response->format = 'json';
	
		$this->load->model('ret_tally_api_model');
		$this->load->model('ret_estimation_model');
		ini_set('date.timezone', 'Asia/Calcutta');
	}

	/**
	* General functions
	*/
    //funtion to get post values
    function get_values()
    {
		return (array)json_decode(file_get_contents('php://input'));

	}

	//array sorting
	function array_sort($array, $on, $order=SORT_ASC){
		$new_array = array();
		$sortable_array = array();
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
			foreach ($sortable_array as $k => $v) {
				//$new_array[$k] = $array[$k];
				$new_array[] = $array[$k];
			}
		}
		return $new_array;
	}

	public function __encrypt($str)
	{
		return base64_encode($str);
	}
	
	function branchtransferdetails_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getbranchtransferInitiatedList();
		$responsedata = array('status' => true,'transferdata'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function importsales_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getsalesList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
}
?>