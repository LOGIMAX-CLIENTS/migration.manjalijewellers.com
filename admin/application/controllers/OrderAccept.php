<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderAccept extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('ret_order_model');
        $this->load->model('ret_purchase_approval_model');
        $this->load->library('session');
    }

    public function index($token=null)
    {
        if(!$token) {
            $this->show_message('Invalid Access', 'The link you are trying to access is invalid or incomplete.', 'danger');
            return;
        }

        $log = $this->ret_order_model->get_email_log_by_token($token);
        if($log && $log['status'] > 1) {
            $this->show_message('Link Not Valid', 'This link is invalid or has expired. Please contact the sender if you need assistance.', 'danger');
            return;
        }

        $data['log'] = $log;
        $data['order'] = $this->ret_purchase_approval_model->get_karigar_order_details($log['id_customerorder']);
        
        // Fetch company details for header
        $company = $this->db->query("SELECT comp_name_in_sms, concat(address1, address2) as address, phone, email FROM company LIMIT 1")->row_array();
        $data['company'] = $company;
        
        // Load public layout or simple view
        $this->load->view('order/order_accept_public', $data);
    }

    private function show_message($title, $message, $type = 'info')
    {
        $data['title'] = $title;
        $data['message'] = $message;
        $data['type'] = $type; // success, danger, info
        $this->load->view('order/order_message', $data);
    }

    public function accept()
    {
        $token = $this->input->post('token');
        $due_date = $this->input->post('due_date');

        if(!$token || !$due_date) {
            echo json_encode(array('status' => false, 'msg' => 'Missing data'));
            return;
        }

        $log = $this->ret_order_model->get_email_log_by_token($token);
        if(!$log) {
            echo json_encode(array('status' => false, 'msg' => 'Invalid link'));
            return;
        }

        $status = $this->ret_order_model->update_order_acceptance($log['id_customerorder'], $due_date, $log['id']);
        if($status) {
            echo json_encode(array('status' => true, 'msg' => 'Order accepted successfully'));
        } else {
            echo json_encode(array('status' => false, 'msg' => 'Failed to update order'));
        }
    }

    public function reject()
    {
        $token = $this->input->post('token');
        $reason = $this->input->post('reason');

        if(!$token) {
            echo json_encode(array('status' => false, 'msg' => 'Missing data'));
            return;
        }

        if(!$reason || trim($reason) == '') {
            echo json_encode(array('status' => false, 'msg' => 'Rejection reason is required'));
            return;
        }

        $log = $this->ret_order_model->get_email_log_by_token($token);
        if(!$log) {
            echo json_encode(array('status' => false, 'msg' => 'Invalid link'));
            return;
        }

        $status = $this->ret_order_model->update_order_rejection($log['id_customerorder'], $log['id'], $reason);
        if($status) {
            echo json_encode(array('status' => true, 'msg' => 'Order rejected successfully'));
        } else {
            echo json_encode(array('status' => false, 'msg' => 'Failed to reject order'));
        }
    }
}
