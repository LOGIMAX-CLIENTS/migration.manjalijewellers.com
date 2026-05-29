<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_emp_incentive extends CI_Controller
{
    const VIEW_FOLDER    = 'master/emp_incentive/';
    const REPORT_FOLDER  = 'ret_reports/';
    const INCENTIVE_MODEL = 'ret_emp_incentive_model';
    const SET_MODEL       = 'admin_settings_model';

    function __construct()
    {
        parent::__construct();
        ini_set('date.timezone', 'Asia/Calcutta');
        $this->load->model(self::INCENTIVE_MODEL);
        $this->load->model(self::SET_MODEL);
        $this->load->model('ret_stock_age_master_model');

        if (!$this->session->userdata('is_logged')) {
            redirect('admin/login');
        }
    }

    // ============================================================
    // CONFIG LIST
    // ============================================================

    public function config_list()
    {
        $model = self::INCENTIVE_MODEL;
        $data['branches']   = $this->$model->get_branches();
        $data['metals']     = $this->$model->get_metals();
        $data['categories'] = $this->$model->get_categories();
        $data['main_content'] = self::VIEW_FOLDER . 'list';
        $this->load->view('layout/template', $data);
    }

    public function ajax_get_configs()
    {
        $model = self::INCENTIVE_MODEL;
        $set_model = self::SET_MODEL;
        $access = $this->$set_model->get_access('emp_incentive');

        $items = $this->$model->get_all_configs();

        $data = array(
            'access' => $access,
            'data'   => $items
        );

        echo json_encode($data);
    }

    // ============================================================
    // CONFIG FORM (ADD / EDIT)
    // ============================================================

    public function config_form($process_type = "", $id = "")
    {
        $model = self::INCENTIVE_MODEL;

        $data['branches']   = $this->$model->get_branches();
        $data['metals']     = $this->$model->get_metals();
        $data['categories'] = $this->$model->get_categories(); // All categories initially
        $data['products']   = array();
        $data['designs']    = array();
        $data['sub_designs'] = array();
        $data['stock_age_masters'] = $this->ret_stock_age_master_model->get_dropdown_data();
        // Stone types for Carat Range
        $data['stone_types'] = $this->db->get_where('ret_stone_type', array('status' => 1))->result_array();

        switch ($process_type) {
            case 'Add':
                $data['config'] = array(
                    'id'                      => '',
                    'id_branch'               => '',
                    'id_metal'                => '',
                    'id_category'             => '',
                    'id_product'              => '',
                    'id_design'               => '',
                    'id_sub_design'           => '',
                    'calc_basis'              => 1,
                    'rate_type'               => 1,
                    'incentive_value'         => '',
                    'id_stock_age_master'     => '',
                    'stone_type'              => '',
                    'status'                  => 1,
                    'ranges'                  => array()
                );
                $data['process_type'] = 'Add';
                $data['main_content'] = self::VIEW_FOLDER . 'form';
                $this->load->view('layout/template', $data);
                break;

            case 'Edit':
                $config = $this->$model->get_config($id);
                if (empty($config)) {
                    $this->session->set_flashdata('chit_info', array(
                        'message' => 'Record not found',
                        'class'   => 'danger',
                        'title'   => 'Error'
                    ));
                    redirect('emp_incentive');
                    return;
                }

                // Load cascading dropdowns for existing multi-select values
                // Filter categories by metal if metal is specified
                if (!empty($config['id_metal'])) {
                    $data['categories'] = $this->$model->get_categories($config['id_metal']);
                }

                if (!empty($config['id_category'])) {
                    $cat_ids = explode(',', $config['id_category']);
                    foreach ($cat_ids as $cid) {
                        $prods = $this->$model->get_products(trim($cid));
                        $data['products'] = array_merge($data['products'], $prods);
                    }
                    // Remove duplicates by pro_id
                    $data['products'] = array_values(array_column(
                        array_reverse($data['products']), null, 'pro_id'
                    ));
                }
                if (!empty($config['id_product'])) {
                    $prod_ids = explode(',', $config['id_product']);
                    foreach ($prod_ids as $pid) {
                        $des = $this->$model->get_designs(trim($pid));
                        $data['designs'] = array_merge($data['designs'], $des);
                    }
                    $data['designs'] = array_values(array_column(
                        array_reverse($data['designs']), null, 'design_no'
                    ));
                }
                if (!empty($config['id_design'])) {
                    $des_ids = explode(',', $config['id_design']);
                    $first_prod = !empty($config['id_product']) ? explode(',', $config['id_product'])[0] : '';
                    foreach ($des_ids as $did) {
                        $sds = $this->$model->get_sub_designs(trim($did), $first_prod);
                        $data['sub_designs'] = array_merge($data['sub_designs'], $sds);
                    }
                    $data['sub_designs'] = array_values(array_column(
                        array_reverse($data['sub_designs']), null, 'id_sub_design'
                    ));
                }

                $data['config'] = $config;
                $data['process_type'] = 'Edit';
                $data['main_content'] = self::VIEW_FOLDER . 'form';
                $this->load->view('layout/template', $data);
                break;
        }
    }

    // ============================================================
    // CONFIG SAVE
    // ============================================================

    public function config_post($process_type = "", $id = "")
    {
        $model = self::INCENTIVE_MODEL;
        $post  = $this->input->post('config');

        $config_data = array(
            'id_branch'       => (!empty($post['id_branch']) ? (is_array($post['id_branch']) ? implode(',', $post['id_branch']) : $post['id_branch']) : NULL),
            'id_metal'        => (!empty($post['id_metal']) ? (is_array($post['id_metal']) ? implode(',', $post['id_metal']) : $post['id_metal']) : NULL),
            'id_category'     => (!empty($post['id_category']) ? (is_array($post['id_category']) ? implode(',', $post['id_category']) : $post['id_category']) : NULL),
            'id_product'      => (!empty($post['id_product']) ? (is_array($post['id_product']) ? implode(',', $post['id_product']) : $post['id_product']) : NULL),
            'id_design'       => (!empty($post['id_design']) ? (is_array($post['id_design']) ? implode(',', $post['id_design']) : $post['id_design']) : NULL),
            'id_sub_design'   => (!empty($post['id_sub_design']) ? (is_array($post['id_sub_design']) ? implode(',', $post['id_sub_design']) : $post['id_sub_design']) : NULL),
            'calc_basis'      => intval($post['calc_basis']),
            'rate_type'       => intval($post['rate_type']),
            'incentive_value' => 0,
            'stone_type'      => (intval($post['rate_type']) == 3 && !empty($post['stone_type'])) ? intval($post['stone_type']) : NULL,
            'status'          => isset($post['status']) ? intval($post['status']) : 1,
        );

        // Set incentive_value based on rate_type
        if (intval($post['rate_type']) == 1 && !empty($post['incentive_value'])) {
            $config_data['incentive_value'] = $post['incentive_value'];
        }

        // Save id_stock_age_master when calc_basis = Age Based (4)
        if (intval($post['calc_basis']) == 4 && !empty($post['id_stock_age_master'])) {
            $config_data['id_stock_age_master'] = intval($post['id_stock_age_master']);
        } else {
            $config_data['id_stock_age_master'] = NULL;
        }

        // Parse range slabs
        $ranges = array();
        if (intval($post['rate_type']) != 1 && isset($post['ranges'])) {
            foreach ($post['ranges'] as $range) {
                if ($range['range_from'] !== '' && $range['range_to'] !== '' && $range['incentive_value'] !== '') {
                    $ranges[] = array(
                        'range_from'      => floatval($range['range_from']),
                        'range_to'        => floatval($range['range_to']),
                        'incentive_value' => floatval($range['incentive_value'])
                    );
                }
            }
        }

        // Check for duplicate/conflicting configs
        $exclude_id = ($process_type == 'Edit') ? $id : null;
        $conflicts = $this->$model->check_duplicate_config($config_data, $exclude_id);
        if (!empty($conflicts)) {
            $conflict_ids = array_column($conflicts, 'id');
            $calc_labels = array(1 => 'Per Gram', 2 => 'Per Carat', 3 => '% of Sales Value', 4 => 'Age Based');
            $conflict_details = array();
            foreach ($conflicts as $cf) {
                $conflict_details[] = 'Rule #' . $cf['id'] . ' (' . (isset($calc_labels[$cf['calc_basis']]) ? $calc_labels[$cf['calc_basis']] : 'Unknown') . ')';
            }
            $this->session->set_flashdata('chit_info', array(
                'message' => 'Conflicting incentive rule(s) already exist for the same scope: ' . implode(', ', $conflict_details) . '. Please deactivate existing rules first or adjust the scope.',
                'class'   => 'danger',
                'title'   => 'Duplicate Configuration'
            ));
            redirect('emp_incentive');
            return;
        }

        switch ($process_type) {
            case 'Add':
                $config_data['created_by'] = $this->session->userdata('uid');
                $config_data['created_on'] = date("Y-m-d H:i:s");

                $result = $this->$model->save_config($config_data, $ranges);

                if ($result) {
                    $this->session->set_flashdata('chit_info', array(
                        'message' => 'Incentive rule added successfully',
                        'class'   => 'success',
                        'title'   => 'Add Incentive Config'
                    ));
                } else {
                    $this->session->set_flashdata('chit_info', array(
                        'message' => 'Failed to add incentive rule',
                        'class'   => 'danger',
                        'title'   => 'Error'
                    ));
                }
                redirect('emp_incentive');
                break;

            case 'Edit':
                $config_data['updated_by'] = $this->session->userdata('uid');
                $config_data['updated_on'] = date("Y-m-d H:i:s");

                $result = $this->$model->save_config($config_data, $ranges, $id);

                if ($result) {
                    $this->session->set_flashdata('chit_info', array(
                        'message' => 'Incentive rule updated successfully',
                        'class'   => 'success',
                        'title'   => 'Update Incentive Config'
                    ));
                } else {
                    $this->session->set_flashdata('chit_info', array(
                        'message' => 'Failed to update incentive rule',
                        'class'   => 'danger',
                        'title'   => 'Error'
                    ));
                }
                redirect('emp_incentive');
                break;
        }
    }

    // ============================================================
    // BATCH SAVE (Add multiple configs at once)
    // ============================================================

    public function config_batch_save()
    {
        $model = self::INCENTIVE_MODEL;

        // Read JSON body
        $json = file_get_contents('php://input');
        $payload = json_decode($json, true);

        if (empty($payload['items']) || !is_array($payload['items'])) {
            echo json_encode(array('status' => false, 'message' => 'No items received'));
            return;
        }

        $user_id = $this->session->userdata('uid');

        // ---- Pre-save validation: check cross-item conflicts within batch ----
        $items = $payload['items'];
        $calc_labels = array(1 => 'Per Gram', 2 => 'Per Carat', 3 => '% of Sales Value', 4 => 'Age Based');
        
        // Check each item against all other items in the batch
        for ($i = 0; $i < count($items); $i++) {
            for ($j = $i + 1; $j < count($items); $j++) {
                $a = $items[$i];
                $b = $items[$j];
                // Check if scopes overlap AND calc_basis differs
                if ($this->_scopes_overlap($a, $b) && intval($a['calc_basis']) != intval($b['calc_basis'])) {
                    $label_a = isset($calc_labels[intval($a['calc_basis'])]) ? $calc_labels[intval($a['calc_basis'])] : 'Unknown';
                    $label_b = isset($calc_labels[intval($b['calc_basis'])]) ? $calc_labels[intval($b['calc_basis'])] : 'Unknown';
                    echo json_encode(array(
                        'status'  => false,
                        'message' => 'Conflict between Item #' . ($i+1) . ' (' . $label_a . ') and Item #' . ($j+1) . ' (' . $label_b . '). Same scope cannot have different calculation bases.'
                    ));
                    return;
                }
            }
        }

        // Check each item against existing DB configs
        foreach ($items as $idx => $item) {
            $check_data = array(
                'id_branch'    => !empty($item['id_branch']) ? $item['id_branch'] : null,
                'id_metal'     => !empty($item['id_metal']) ? $item['id_metal'] : null,
                'id_category'  => !empty($item['id_category']) ? $item['id_category'] : null,
                'id_product'   => !empty($item['id_product']) ? $item['id_product'] : null,
                'id_design'    => !empty($item['id_design']) ? $item['id_design'] : null,
                'id_sub_design'=> !empty($item['id_sub_design']) ? $item['id_sub_design'] : null,
            );
            $conflicts = $this->$model->check_duplicate_config($check_data);
            if (!empty($conflicts)) {
                // Check if any conflict has a different calc_basis
                foreach ($conflicts as $cf) {
                    if (intval($cf['calc_basis']) != intval($item['calc_basis'])) {
                        $label_new = isset($calc_labels[intval($item['calc_basis'])]) ? $calc_labels[intval($item['calc_basis'])] : 'Unknown';
                        $label_existing = isset($calc_labels[intval($cf['calc_basis'])]) ? $calc_labels[intval($cf['calc_basis'])] : 'Unknown';
                        echo json_encode(array(
                            'status'  => false,
                            'message' => 'Item #' . ($idx+1) . ' (' . $label_new . ') conflicts with existing Rule #' . $cf['id'] . ' (' . $label_existing . '). Same scope cannot have different calculation bases.'
                        ));
                        return;
                    }
                }
            }
        }

        // ---- All checks passed — proceed to save ----
        $success_count = 0;
        $fail_count = 0;

        foreach ($payload['items'] as $item) {
            $config_data = array(
                'id_branch'       => !empty($item['id_branch']) ? $item['id_branch'] : NULL,
                'id_metal'        => !empty($item['id_metal']) ? $item['id_metal'] : NULL,
                'id_category'     => !empty($item['id_category']) ? $item['id_category'] : NULL,
                'id_product'      => !empty($item['id_product']) ? $item['id_product'] : NULL,
                'id_design'       => !empty($item['id_design']) ? $item['id_design'] : NULL,
                'id_sub_design'   => !empty($item['id_sub_design']) ? $item['id_sub_design'] : NULL,
                'calc_basis'      => intval($item['calc_basis']),
                'rate_type'       => intval($item['rate_type']),
                'incentive_value' => floatval($item['incentive_value']),
                'stone_type'      => (intval($item['rate_type']) == 3 && !empty($item['stone_type'])) ? intval($item['stone_type']) : NULL,
                'id_stock_age_master' => NULL,
                'status'          => isset($item['status']) ? intval($item['status']) : 1,
                'created_by'      => $user_id,
                'created_on'      => date("Y-m-d H:i:s"),
            );

            // Age Based calc_basis — set age master
            if (intval($item['calc_basis']) == 4 && !empty($item['id_stock_age_master'])) {
                $config_data['id_stock_age_master'] = intval($item['id_stock_age_master']);
            }

            // Parse ranges (only for non-Fixed rate types)
            $ranges = array();
            if (intval($item['rate_type']) != 1 && !empty($item['ranges'])) {
                foreach ($item['ranges'] as $range) {
                    if ($range['range_from'] !== '' && $range['range_to'] !== '' && $range['incentive_value'] !== '') {
                        $ranges[] = array(
                            'range_from'      => floatval($range['range_from']),
                            'range_to'        => floatval($range['range_to']),
                            'incentive_value' => floatval($range['incentive_value'])
                        );
                    }
                }
            }

            $result = $this->$model->save_config($config_data, $ranges);
            if ($result) {
                $success_count++;
            } else {
                $fail_count++;
            }
        }

        if ($success_count > 0) {
            $this->session->set_flashdata('chit_info', array(
                'message' => $success_count . ' incentive rule(s) added successfully' . ($fail_count > 0 ? ' (' . $fail_count . ' failed)' : ''),
                'class'   => 'success',
                'title'   => 'Batch Save'
            ));
        }

        echo json_encode(array(
            'status'  => ($success_count > 0),
            'message' => $success_count . ' rule(s) saved' . ($fail_count > 0 ? ', ' . $fail_count . ' failed' : ''),
            'count'   => $success_count
        ));
    }

    // ============================================================
    // DELETE CONFIG
    // ============================================================

    public function ajax_delete_config()
    {
        $model = self::INCENTIVE_MODEL;
        $id = $this->input->post('id');

        $status = $this->$model->delete_config($id);

        echo json_encode(array(
            'status'  => $status ? TRUE : FALSE,
            'message' => $status ? 'Rule deactivated successfully' : 'Failed to deactivate rule'
        ));
    }

    // ============================================================
    // TOGGLE STATUS
    // ============================================================

    public function ajax_toggle_status()
    {
        $model = self::INCENTIVE_MODEL;
        $id = $this->input->post('id');

        $new_status = $this->$model->toggle_status($id);

        echo json_encode(array(
            'status'     => ($new_status !== false) ? TRUE : FALSE,
            'new_status' => $new_status,
            'message'    => ($new_status !== false)
                ? ($new_status == 1 ? 'Rule activated' : 'Rule deactivated')
                : 'Failed to toggle status'
        ));
    }

    // ============================================================
    // DUPLICATE CONFIG CHECK (AJAX)
    // ============================================================

    public function ajax_check_duplicate()
    {
        $model = self::INCENTIVE_MODEL;
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data)) {
            echo json_encode(array('status' => false, 'conflicts' => array()));
            return;
        }

        $exclude_id = isset($data['exclude_id']) ? intval($data['exclude_id']) : null;
        $config_data = array(
            'id_branch'    => !empty($data['id_branch']) ? $data['id_branch'] : null,
            'id_metal'     => !empty($data['id_metal']) ? $data['id_metal'] : null,
            'id_category'  => !empty($data['id_category']) ? $data['id_category'] : null,
            'id_product'   => !empty($data['id_product']) ? $data['id_product'] : null,
            'id_design'    => !empty($data['id_design']) ? $data['id_design'] : null,
            'id_sub_design'=> !empty($data['id_sub_design']) ? $data['id_sub_design'] : null,
        );

        $conflicts = $this->$model->check_duplicate_config($config_data, $exclude_id);

        $calc_labels = array(1 => 'Per Gram', 2 => 'Per Carat', 3 => '% of Sales Value', 4 => 'Age Based');
        $details = array();
        foreach ($conflicts as $cf) {
            $details[] = array(
                'id'         => $cf['id'],
                'calc_basis' => isset($calc_labels[$cf['calc_basis']]) ? $calc_labels[$cf['calc_basis']] : 'Unknown',
                'rate_type'  => $cf['rate_type']
            );
        }

        echo json_encode(array(
            'status'    => !empty($conflicts),
            'conflicts' => $details,
            'message'   => !empty($conflicts)
                ? 'Conflicting rule(s) found: ' . implode(', ', array_map(function($d) { return 'Rule #' . $d['id'] . ' (' . $d['calc_basis'] . ')'; }, $details))
                : ''
        ));
    }

    // ============================================================
    // CASCADING DROPDOWN AJAX
    // ============================================================

    public function get_categories_by_metal()
    {
        $model = self::INCENTIVE_MODEL;
        $metal_id = $this->input->post('metal_id');
        $data = $this->$model->get_categories($metal_id);
        echo json_encode($data);
    }

    public function get_products_by_category()
    {
        $model = self::INCENTIVE_MODEL;
        $cat_id = $this->input->post('cat_id');
        $data = $this->$model->get_products($cat_id);
        echo json_encode($data);
    }

    public function get_designs_by_product()
    {
        $model = self::INCENTIVE_MODEL;
        $product_id = $this->input->post('product_id');
        $data = $this->$model->get_designs($product_id);
        echo json_encode($data);
    }

    public function get_sub_designs_by_design()
    {
        $model = self::INCENTIVE_MODEL;
        $design_id = $this->input->post('design_id');
        $product_id = $this->input->post('product_id');
        $data = $this->$model->get_sub_designs($design_id, $product_id);
        echo json_encode($data);
    }

    // ============================================================
    // INCENTIVE REPORT
    // ============================================================

    public function incentive_report($type = "")
    {
        $model = self::INCENTIVE_MODEL;

        switch ($type) {
            case 'list':
                $data['branches']  = $this->$model->get_branches();
                $data['employees'] = $this->$model->get_employees();
                $data['main_content'] = self::REPORT_FOLDER . 'emp_sales_incentive_report';
                $this->load->view('layout/template', $data);
                break;

            case 'ajax':
                $list = $this->$model->get_incentive_report($_POST);

                $access = $this->admin_settings_model->get_access('admin_emp_incentive/incentive_report/list');

                $data = array(
                    'list'   => $list,
                    'access' => $access
                );

                echo json_encode($data);
                break;

            case 'summary':
                $list = $this->$model->get_incentive_summary($_POST);
                echo json_encode($list);
                break;
        }
    }

    /**
     * AJAX: Mark selected incentive entries as "Given" (paid)
     */
    public function ajax_mark_paid()
    {
        $model = self::INCENTIVE_MODEL;
        $items = $this->input->post('items');

        if (empty($items) || !is_array($items)) {
            echo json_encode(array('status' => 'error', 'message' => 'No items selected'));
            return;
        }

        $user_id = $this->session->userdata('id_user');
        $count = $this->$model->mark_incentive_paid($items, $user_id);

        echo json_encode(array(
            'status'  => 'success',
            'message' => $count . ' incentive(s) marked as given',
            'count'   => $count
        ));
    }

    // ============================================================
    // PRIVATE HELPERS
    // ============================================================

    /**
     * Check if two batch items have overlapping scope fields
     */
    private function _scopes_overlap($a, $b)
    {
        $fields = array('id_branch', 'id_metal', 'id_category', 'id_product', 'id_design', 'id_sub_design');
        foreach ($fields as $field) {
            $val_a = isset($a[$field]) ? $a[$field] : '';
            $val_b = isset($b[$field]) ? $b[$field] : '';
            if (!$this->_csv_values_overlap($val_a, $val_b)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if two CSV strings share at least one value.
     * Empty string = "All" = always overlaps.
     */
    private function _csv_values_overlap($csv_a, $csv_b)
    {
        if (empty($csv_a) || empty($csv_b)) return true; // "All" matches everything
        $arr_a = explode(',', $csv_a);
        $arr_b = explode(',', $csv_b);
        return !empty(array_intersect($arr_a, $arr_b));
    }
}
