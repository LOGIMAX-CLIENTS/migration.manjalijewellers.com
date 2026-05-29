<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require_once(APPPATH.'libraries/REST_Controller.php');
require_once(APPPATH.'libraries/payu.php');

class Kyc extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->response->format = 'json';
        $this->load->model('kyc_model');
    }

    function get_values()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function save_dynamic_post() {
        $data = $this->get_values();
        if (empty($data)) {
            $data = $this->post();
        }
        $cus_id = isset($data['id_customer']) ? $data['id_customer'] : null;
        $kyc_dynamic = isset($data['kyc_dynamic']) ? $data['kyc_dynamic'] : null;
        
        $id_scheme = isset($data['id_scheme']) ? $data['id_scheme'] : 0;
        $pay_amount = isset($data['pay_amount']) ? $data['pay_amount'] : 0;
        $id_scheme_account = isset($data['id_scheme_account']) ? $data['id_scheme_account'] : 0;

        if($cus_id && $kyc_dynamic !== null) {
            if ($id_scheme > 0 || $pay_amount > 0 || $id_scheme_account > 0) {
                $res = $this->kyc_model->save_scheme_account_kyc($cus_id, $id_scheme, $pay_amount, is_array($kyc_dynamic) ? $kyc_dynamic : array(), $id_scheme_account);
            } else {
                $res = $this->kyc_model->save_customer_kyc($cus_id, is_array($kyc_dynamic) ? $kyc_dynamic : array());
            }

            if (isset($res['status']) && $res['status'] === false) {
                $this->response(['status' => 'error', 'message' => $res['message']], 200);
            } else {
                $this->response(['status' => 'success'], 200);
            }
        } else {
            // Also validate if we have no dynamic parameters passed in at all
            if ($id_scheme > 0 || $pay_amount > 0 || $id_scheme_account > 0) {
                $res = $this->kyc_model->save_scheme_account_kyc($cus_id, $id_scheme, $pay_amount, array(), $id_scheme_account);
            } else {
                $res = $this->kyc_model->save_customer_kyc($cus_id, array());
            }

            if (isset($res['status']) && $res['status'] === false) {
                $this->response(['status' => 'error', 'message' => $res['message']], 200);
            } else {
                $this->response(['status' => 'error', 'message' => 'Missing data'], 200);
            }
        }
    }

    public function get_dynamic_kyc_data_get($id_scheme = 0, $id_customer = 0, $pay_amount = 0, $id_scheme_account = 0)
    {
        $id_scheme = $this->get('id_scheme') !== null ? $this->get('id_scheme') : $id_scheme;
        $id_customer = $this->get('id_customer') !== null ? $this->get('id_customer') : $id_customer;
        $pay_amount = $this->get('pay_amount') !== null ? $this->get('pay_amount') : $pay_amount;
        $id_scheme_account = $this->get('id_scheme_account') !== null ? $this->get('id_scheme_account') : $id_scheme_account;

        $data = $this->kyc_model->get_unified_dynamic_kyc_data($id_scheme, $id_customer, $pay_amount, $id_scheme_account);
        $this->response($data, 200);
    }
    
    public function index_options() {
        $this->response(['status' => true], 200);
    }
}
