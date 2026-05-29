<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Custom Prestashop 1.7 API's
*/
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require(APPPATH.'libraries/REST_Controller.php'); 
class Presta_api extends REST_Controller
{
	const PRESTA_MODEL = "prestaapi_model"; 
	function __construct()
	{
		parent::__construct();
		$this->response->format = 'json';
		$this->load->model(self::PRESTA_MODEL);
		$this->load->model('prestaapi_model');
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
		
	/**
	* Prestashop 1.7 API functions 
	*/
	
	function getProdsByCatId_post()
	{		 
		$model = self::PRESTA_MODEL;
		$postData = $this->get_values();
		$catId = $postData['id'];
		/*$assCat = implode(",",$postData['ass_cat']);
		$concat = sizeof($postData['ass_cat']) == 0?'':','.$assCat;
		$categIds = $catId.''.$concat; */
	    $data = $this->$model->fetchProdsByCatId($catId);  
		$this->response($data,200);	
	}
	
	function getNewProducts_get()
	{		 
		$model = self::PRESTA_MODEL;
		$postData = $this->get_values(); 
	    $data = $this->$model->fetchNewProds();  
		$this->response($data,200);	
	}
	function getOfferProducts_get()
	{		 
		$model = self::PRESTA_MODEL;
		$postData = $this->get_values(); 
	    $data = $this->$model->fetchofferProds();  
		$this->response($data,200);	
	}
	
	function getHomePgProds_get()
	{		 
		$model = self::PRESTA_MODEL;
		$postData = $this->get_values(); 
	    $data = $this->$model->fetchHomePgProds();  
		$this->response($data,200);	
	}
	
    function getProdFeatures_get()
	{		 
		$model = self::PRESTA_MODEL; 
		$lang_id = 1;
	    $data = $this->$model->fetchProdFeatures($lang_id);  
		$this->response($data,200);	
	}
    
    
    
}	
?>