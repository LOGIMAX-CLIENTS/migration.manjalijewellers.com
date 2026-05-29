<?php

class Mock_Ret_Estimation_Model {
    public function get_profile_settings($profile) { return []; }
    public function get_empty_record() { return []; }
    public function get_ret_settings($key) { return 0; }
    public function get_stone_disc() { return 0; }
    public function get_employee_settings($uid) { return ['id_profile' => 1]; }
    public function getUOMDetails() { return []; }
    public function get_FinancialYear() { return ['fin_year_code' => 'FY2324']; }
    public function generateEstiNo($date, $branch) { return 'EST001'; }
    public function insertData($data, $table) { return 1; }
    public function updateData($data, $field, $val, $table) { return true; }
    public function insertBatchData($data, $table) { return true; }
    public function get_charges($id) { return []; }
}

class Mock_Admin_Settings_Model {
    public function profileDB($action, $id) { return []; }
    public function get_access($path) { return true; }
    public function getBranchDayClosingData($branch_id) { 
        return ['entry_date' => date('Y-m-d')]; 
    }
}

class Mock_Log_Model {
    public function user_log($data) {}
    public function log_detail($data) {}
}

class Mock_Ret_Billing_Model {
    // Stub
}
