<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_tester extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('ret_reports_model');
    }

    public function run() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');
        ob_implicit_flush(1);

        $log_out = "============================================\n";
        $log_out .= "   UNIT TESTING STOCK REPORT QUERIES\n";
        $log_out .= "============================================\n";

        $base_data = [
            'dt_range' => '03/04/2026 - 03/04/2026',
            'id_branch' => '4,6',
            'id_section' => '1,2',
            'id_metal' => '1',
            'status' => '0',
            'deliver_status' => '0',
            'purity' => '1',
            'wt_range' => '', 
            'id_weight' => '',
            'id_category' => ['1', '2'], // array tests
            'id_product' => ['1', '2', '3'],
            'id_design' => ['4', '5'],
            'id_sub_design' => ['6', '7'],
            'prod_group_id' => ''
        ];

        $groupings = [1, 2, 3, 4];
        $modes = [1 => 'Tag', 2 => 'Non-Tag'];

        $total = 0;
        $passed = 0;
        $failed = 0;

        foreach ($modes as $list_by => $mode_name) {
            foreach ($groupings as $group_by) {
                foreach (['Array', 'Scalar'] as $format) {
                    $total++;
                    
                    $data = $base_data;
                    $data['group_by'] = $group_by;
                    $data['list_by'] = $list_by;

                    if ($group_by == 3) {
                        $data['id_weight'] = '1,2,3';
                        $data['wt_range'] = 'wt';
                    } else {
                        // Reset them so group by 1,2,4 don't fail for unexpected reasons
                        $data['id_weight'] = '';
                        $data['wt_range'] = '';
                    }

                    if ($format === 'Scalar') {
                        $data['id_category'] = '1,2';
                        $data['id_product'] = '1,2,3';
                        $data['id_design'] = '4,5';
                        $data['id_sub_design'] = '6,7';
                    }

                    $log_out .= "[TEST $total] Format: $format, Mode: $mode_name, GroupBy: $group_by ... ";

                    $this->db->db_debug = TRUE;

                    try {
                        if ($list_by == 1) {
                            $res = $this->ret_reports_model->get_stock_details_v1($data);
                        } else {
                            $res = $this->ret_reports_model->get_nontag_stock_details_v1($data);
                        }

                        $err_msg = isset($this->db->error) && is_callable([$this->db, 'error']) ? $this->db->error() : (method_exists($this->db, '_error_message') ? ['code' => $this->db->_error_number(), 'message' => $this->db->_error_message()] : ['code' => 0]);
                        if (!empty($err_msg['code']) && $err_msg['code'] !== 0) {
                            $log_out .= "FAIL\n   -> DB Error " . $err_msg['code'] . ": " . $err_msg['message'] . "\n";
                            $failed++;
                        } else {
                            $log_out .= "PASS (" . (is_array($res) ? count($res) : 0) . " rows)\n";
                            $passed++;
                        }
                    } catch (Exception $e) {
                        $log_out .= "FAIL (Exception: " . $e->getMessage() . ")\n";
                        $failed++;
                    }
                    
                    $this->db->db_debug = TRUE; // restore
                    echo $log_out; $log_out = '';
                }
            }
        }

        $log_out .= "============================================\n";
        $log_out .= "Results: $passed Passed, $failed Failed. Total: $total\n";
        $log_out .= "============================================\n";

        echo $log_out;
        echo "Tests complete. Check application/logs/test_results.log";
    }
}
