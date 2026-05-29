<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Global Controller for PAN Verification
 * Provides a centralized endpoint for all modules.
 */
class Admin_pan_verification extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Ensure user is logged in
        if (!$this->session->userdata('is_logged')) {
            $result = [
                'status' => false,
                'message' => 'Unauthorized access. Please login.'
            ];
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
    }

    /**
     * verify action
     * Triggered via AJAX from any module's JS.
     */
    public function verify()
    {
        $pan  = strtoupper(trim($this->input->post('pan')));
        $name = trim($this->input->post('name'));

        if (empty($pan)) {
            $result = [
                'status' => false,
                'message' => 'PAN number is required'
            ];
            header('Content-Type: application/json');
            echo json_encode($result);
            return;
        }

        require_once APPPATH . 'services/PanVerificationService.php';
        $service = new PanVerificationService();
        $result  = $service->verify_pan($pan, $name);

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
