<?php

class Mock_Loader {
    public function model($model_name) {
        $ci =& get_instance();
        // Simple mock: assumes the test will inject the mock model manually
        // or we just define a property on the controller
        if (!isset($ci->$model_name)) {
            $ci->$model_name = new stdClass();
        }
    }
    public function view($view, $vars = []) {}
    public function library($lib) {}
}

class Mock_Session {
    public $userdata = ['is_logged' => true, 'uid' => 1, 'profile' => 1];
    public function userdata($key) {
        return isset($this->userdata[$key]) ? $this->userdata[$key] : null;
    }
    public function set_flashdata($key, $val) {}
}

class Mock_DB {
    public function trans_begin() {}
    public function trans_status() { return true; }
    public function trans_commit() {}
    public function trans_rollback() {}
    public function last_query() { return ""; }
}

class CI_Controller {
    public $load;
    public $session;
    public $db;
    public $input;
    private static $instance;

    public function __construct() {
        self::$instance =& $this;
        $this->load = new Mock_Loader();
        $this->session = new Mock_Session();
        $this->db = new Mock_DB();
        $this->input = new stdClass(); // Simplified input mock
    }

    public static function &get_instance() {
        return self::$instance;
    }
}

function &get_instance() {
    return CI_Controller::get_instance();
}

function redirect($url) {
    // Mock redirect
}

if (!defined('APPPATH')) define('APPPATH', 'application/');
if (!defined('BASEPATH')) define('BASEPATH', 'system/');
