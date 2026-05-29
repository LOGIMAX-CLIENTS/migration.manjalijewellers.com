<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require(APPPATH . 'libraries/REST_Controller.php');
class Digigold extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->response->format = 'json';
		$this->log_dir = 'log/' . date("d-m-Y");
		if (!is_dir($this->log_dir)) {
			mkdir($this->log_dir, 0777, TRUE);
		}
		$this->load->model('digigold_modal');
	}
	function get_values()
	{
		return (array)json_decode(file_get_contents('php://input'));
	}
	/**
	 * To get digi gold savings scheme details and account data customer wise
	 * One customer can have only one active digi gold account. 
	 * If customer has active digi account; allow for making payments
	 * Else allow for new joining
	 */
	function digidata_post()
	{
		$data = $this->get_values();
		$chit = $this->digigold_modal->getCusDigiData(array('mobile' => $data['mobile'], 'id_customer' => $data['id_customer'],'id_scheme' => $data['id_scheme'] , 'id_metal' => $data['id_metal']));
		$this->response($chit,200);	
	}
	function set_digiTarget_post()
	{
		$data = $this->get_values();
		$updData = array("dg_target_value_wgt" => $data['dg_target_value_wgt']);
		$set_target = $this->digigold_modal->updData($updData, 'id_scheme_account', $data['id_scheme_account'], 'scheme_account');
		if ($set_target > 0) {
			$res = array('status' => TRUE, 'msg' => 'Target set successfully...');
		} else {
			$res = array('status' => FALSE, 'msg' => 'Unable to proceed your request...');
		}
		$this->response($res, 200);
	}
	public function content_get()
	{
		try {
			$file_path = FCPATH . 'api/digi_content.json';
			if (!file_exists($file_path)) {
				return $this->response([
					'status' => false,
					'error_code' => 'FILE_NOT_FOUND',
					'message' => 'Content file not found'
				], 404);
			}
			if (!is_readable($file_path)) {
				return $this->response([
					'status' => false,
					'error_code' => 'FILE_NOT_READABLE',
					'message' => 'Content file is not readable'
				], 403);
			}
			$json = file_get_contents($file_path);
			if ($json === false) {
				return $this->response([
					'status' => false,
					'error_code' => 'READ_FAILED',
					'message' => 'Failed to read content file'
				], 500);
			}
			$data = json_decode($json, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				return $this->response([
					'status' => false,
					'error_code' => 'INVALID_JSON',
					'message' => 'Invalid JSON format in content file'
				], 500);
			}
			return $this->response([
				'status' => true,
				'message' => 'Content loaded successfully',
				'data' => $data
			], 200);
		} catch (Exception $e) {
			return $this->response([
				'status' => false,
				'error_code' => 'SERVER_ERROR',
				'message' => 'Something went wrong',
				'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
			], 500);
		}
	}
}
