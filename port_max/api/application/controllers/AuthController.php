<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends MY_Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
        $this->load->library('session');
        $this->load->helper('log_helper');
    }

    public function connDB() {
        // Read JSON input
        
        $input = json_decode(file_get_contents("php://input"), true);

        $host = $input['host'] ?? '';
        $db   = $input['database'] ?? '';
        $user = $input['username'] ?? '';
        $pass = $input['password'] ?? '';

        if (!$host || !$db || !$user) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid DB credentials'
            ]);
            exit;
        }

        // Dynamic DB config
        $config = [
            'hostname' => $host,
            'username' => $user,
            'password' => $pass,
            'database' => $db,
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => FALSE, // IMPORTANT: hide errors
            'cache_on' => FALSE,
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci'
        ];

        try {
            // $dbConn = $this->load->database($config, TRUE);
            $this->db = $this->load->database($config, TRUE);
            if (!$this->db->conn_id) {
                echo json_encode([
                'success' => false,
                'message' => 'Unable to connect database'
            ]);
            exit;
            }

            // Save DB config in session
            $this->session->set_userdata('db_config', $config);

            $this->db->select('company_name');
            $this->db->from('company');
            $client_name = $this->db->get()->row()->company_name;

            echo json_encode([
                'success' => true,
                'client' => $client_name,
                'message' => 'Database connected'
            ]);exit;

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);exit;
        }
    }

    public function login()
    {
        // $this->loadClientDB();
        // Read JSON input
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['username']) || empty($input['password'])) {
            $res = array(
                'page' => 'login',
                'success' => false,
                'message' => 'Username and password required'
            );
            LogHelper::activityLog($res['message']);
            echo json_encode($res);
            return;
        }

        $username = trim($input['username']);
        $password = $input['password'];

        // Validate against rest_valid_logins config
        $this->config->load('rest', TRUE);
        $valid_logins = $this->config->item('rest_valid_logins', 'rest');

        if (isset($valid_logins[$username]) && $valid_logins[$username] === $password) {

            // Save login session
            $this->session->set_userdata([
                'logged_in' => true,
                'username'  => $username
            ]);

            $this->db->select('company_name');
            $this->db->from('company');
            $client_name = $this->db->get()->row()->company_name;
            $res = array(
                'page' => 'login',
                'success' => true,
                'client' => $client_name,
                'database' => $this->db->database,
                'message' => 'Login successful'
            );
            LogHelper::activityLog($res);
            echo json_encode($res);exit;
        } else {
            $res = array(
                'page' => 'login',
                'success' => false,
                'message' => 'Invalid username or password',
                'username' => $username
            );
            LogHelper::activityLog($res);
            echo json_encode($res);exit;
        }
    }

    public function logout()
    {
        // Destroy session
        $this->session->sess_destroy();

        // Optional: explicitly close DB
        if (isset($this->db)) {
            $this->db->close();
        }

        $res = array(
            'page' => 'logout',
            'success' => true,
            'message' => 'Disconnected(Logout) successfully'
        );
        LogHelper::activityLog($res);

        // JSON response for frontend
        echo json_encode($res);
        exit;
    }
}
?> 