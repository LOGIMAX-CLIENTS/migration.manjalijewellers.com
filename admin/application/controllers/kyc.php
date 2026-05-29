<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Kyc extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('kyc_model');
        // Load other dependencies if needed (auth checks etc)
    }

    public function index() {
        // KYC features entry point for standalone views if necessary
    }
    
    public function save_dynamic() {
        $cus_id = $this->input->post('id_customer');
        $kyc_dynamic = $this->input->post('kyc_dynamic');
        if($cus_id && $kyc_dynamic) {
            $this->kyc_model->save_customer_kyc($cus_id, $kyc_dynamic);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing data']);
        }
    }

    // Add other KYC endpoints that might be called by AJAX or standalone client uses
    
    // --- KYC Master CRUD section ---
    
    public function master() {
        $data['kyc_masters'] = $this->kyc_model->get_kyc_masters_crud();
        $data['main_content'] = "master/kyc/list";
        $this->load->view('layout/template', $data);
    }

    public function master_add() {
        $data['master'] = null;
        $data['attributes'] = array();
        $data['main_content'] = "master/kyc/form";
        $this->load->view('layout/template', $data);
    }

    public function master_edit($id) {
        $data['master'] = $this->kyc_model->get_master_by_id($id);
        if (!$data['master']) {
            redirect('kyc/master');
        }
        $data['attributes'] = $this->kyc_model->get_attributes_by_master($id);
        $data['main_content'] = "master/kyc/form";
        $this->load->view('layout/template', $data);
    }

    public function master_save() {
        $id = $this->input->post('id_mas_kyc');
        $master_data = array(
            'name' => $this->input->post('name'),
            'short_code' => $this->input->post('short_code'),
            'doc_type' => $this->input->post('doc_type'),
            'is_attachment_req' => $this->input->post('is_attachment_req') ? 1 : 0,
            'is_mandatory' => $this->input->post('is_mandatory') ? 1 : 0,
            'status' => $this->input->post('status') !== null ? (int)$this->input->post('status') : 1,
            'sort' => $this->input->post('sort') ? (int)$this->input->post('sort') : 0,
        );
        if (!$id) {
            $master_data['created_by'] = $this->session->userdata('uid');
        } else {
            $master_data['updated_by'] = $this->session->userdata('uid');
        }

        $attributes_data = array();
        $attr_names = $this->input->post('attr_name');
        if ($attr_names && is_array($attr_names)) {
            $attr_labels = $this->input->post('attr_label');
            $attr_inputs = $this->input->post('attr_input');
            $attr_lengths = $this->input->post('attr_length');
            $attr_mandatory = $this->input->post('attr_mandatory');
            $attr_regex = $this->input->post('attr_regex');
            $attr_id = $this->input->post('id_kyc_attribute');

            foreach ($attr_names as $k => $val) {
                if (trim($val) != '') {
                    $attributes_data[] = array(
                        'id_kyc_attribute' => isset($attr_id[$k]) ? $attr_id[$k] : 0,
                        'attribute' => $val,
                        'attr_label' => isset($attr_labels[$k]) ? $attr_labels[$k] : '',
                        'attr_input' => isset($attr_inputs[$k]) ? $attr_inputs[$k] : 'varchar',
                        'attr_length' => isset($attr_lengths[$k]) ? $attr_lengths[$k] : '',
                        'is_mandatory' => isset($attr_mandatory[$k]) ? $attr_mandatory[$k] : 0,
                        'reg_expression' => isset($attr_regex[$k]) ? $attr_regex[$k] : '',
                        'status' => 1
                    );
                }
            }
        }

        $this->kyc_model->save_master_and_attributes($id, $master_data, $attributes_data);
        
        $this->session->set_flashdata('success', 'KYC Document Profile Saved successfully.');
        redirect('kyc/master');
    }

    public function master_delete($id) {
        if ($this->kyc_model->delete_master($id)) {
            echo json_encode(array('status' => true, 'msg' => 'Deleted Successfully'));
        } else {
            echo json_encode(array('status' => false, 'msg' => 'Failed to delete'));
        }
    }
    
    public function get_dynamic_kyc_data($id_scheme = 0, $id_customer = 0, $pay_amount = 0, $id_scheme_account = 0)
    {
        $id_scheme = $this->input->get('id_scheme') !== null ? $this->input->get('id_scheme') : $id_scheme;
        $id_customer = $this->input->get('id_customer') !== null ? $this->input->get('id_customer') : $id_customer;
        $pay_amount = $this->input->get('pay_amount') !== null ? $this->input->get('pay_amount') : $pay_amount;
        $id_scheme_account = $this->input->get('id_scheme_account') !== null ? $this->input->get('id_scheme_account') : $id_scheme_account;

        $this->load->model('kyc_model');
        $data = $this->kyc_model->get_unified_dynamic_kyc_data($id_scheme, $id_customer, $pay_amount, $id_scheme_account);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
