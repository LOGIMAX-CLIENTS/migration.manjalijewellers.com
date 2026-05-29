<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * IRN E-Invoice Settings Controller
 * Dashboard for IRN configuration, branch GSP credentials, and activity log
 */
class Admin_irn_settings extends CI_Controller
{
    const VIEW_FOLDER = 'irn_settings/';
    const MODEL       = 'irn_settings_model';

    public function __construct()
    {
        parent::__construct();
        ini_set('date.timezone', 'Asia/Calcutta');
        if (!$this->session->userdata('is_logged')) {
            redirect('admin/login');
        }
        $this->load->model(self::MODEL);
    }

    /**
     * Load the IRN Settings dashboard page
     */
    public function index()
    {
        $data['main_content'] = self::VIEW_FOLDER . 'index';
        $this->load->view('layout/template', $data);
    }

    /**
     * AJAX: Return global IRN settings + all branch GSP credentials + config status
     */
    public function ajax()
    {
        $model = self::MODEL;
        $this->load->helper('lmx/functions/common');
        
        $settings = $this->$model->get_irn_settings();
        $branches = $this->$model->get_branches_gsp();
        
        // Run config status check per branch
        $branch_status = array();
        foreach ($branches as $br) {
            $validation = validateIrnConfig($br);
            $branch_status[$br['id_branch']] = $validation;
        }
        
        // Get current base_url for production check
        $result = array(
            'settings'      => $settings,
            'branches'      => $branches,
            'branch_status' => $branch_status,
            'current_base_url' => base_url()
        );
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * AJAX POST: Save GSP configuration settings (not mode/environment — those go via Retail Settings)
     * Expects POST: settings[gsp_auth_base_url]=...&settings[gsp_einvoice_base_url]=...
     * Note: production_base_url is DB-only (not settable via UI)
     */
    public function save_settings()
    {
        $model = self::MODEL;
        $settings = $this->input->post('settings');
        if (!is_array($settings)) {
            echo json_encode(array('status' => false, 'message' => 'Invalid data'));
            return;
        }
        $success = $this->$model->update_irn_settings($settings);
        echo json_encode(array('status' => $success));
    }

    /**
     * AJAX POST: Save GSP credentials for a single branch
     */
    public function save_branch()
    {
        $model = self::MODEL;
        $id_branch = $this->input->post('id_branch');
        if (empty($id_branch)) {
            echo json_encode(array('status' => false, 'message' => 'Branch ID required'));
            return;
        }
        $gsp_data = array(
            'aspid'         => $this->input->post('aspid'),
            'gsp_password'  => $this->input->post('gsp_password'),
            'gsp_user_name' => $this->input->post('gsp_user_name'),
            'eInvPwd'       => $this->input->post('eInvPwd'),
        );
        $status = $this->$model->update_branch_gsp($id_branch, $gsp_data);
        echo json_encode(array('status' => $status, 'id_branch' => $id_branch));
    }

    /**
     * AJAX: Return recent IRN activity/errors for the log table
     */
    public function irn_activity_ajax()
    {
        $model = self::MODEL;
        $limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 30;
        $result = $this->$model->get_recent_irn_activity($limit);
        header('Content-Type: application/json');
        echo json_encode(array('data' => $result));
    }
}
