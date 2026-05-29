<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Partly Sale Controller
 * 
 * 9 endpoints for the Partly Sale module.
 * Handles partly sale form, list, AJAX operations for parts execution,
 * admin weight modification, manual revert, and history queries.
 * 
 * @module    Partly Sale
 * @version   2.0 (Phase 4)
 * @date      April 2026
 */
class admin_ret_tag_parts extends CI_Controller
{

    const SETT_MOD = "admin_settings_model";

    function __construct()
    {
        parent::__construct();

        ini_set('date.timezone', 'Asia/Calcutta');

        $this->load->model('ret_tag_parts_model');
        $this->load->model('ret_tag_model');
        $this->load->model('admin_settings_model');
        $this->load->model('log_model');

        if (!$this->session->userdata('is_logged')) {
            redirect('admin/login');
        } elseif ($this->session->userdata('access_time_from') != NULL && $this->session->userdata('access_time_from') != "") {
            $now = time();
            $from = $this->session->userdata('access_time_from');
            $to = $this->session->userdata('access_time_to');
            $allowedAccess = ($now > $from && $now < $to) ? TRUE : FALSE;
            if ($allowedAccess == FALSE) {
                $this->session->set_flashdata('login_errMsg', 'Exceeded allowed access time!!');
                redirect('chit_admin/logout');
            }
        }
    }

    // =========================================================================
    // PAGE: Partly Sale Form
    // =========================================================================

    /**
     * GET /tag_parts/form
     * Load the partly sale form view.
     */
    public function form()
    {
        $SETT_MOD = self::SETT_MOD;
        $data['access'] = $this->$SETT_MOD->get_access('admin_ret_tag_parts/form');
        $data['customer'] = array(
            'gender' => 0, 'date_of_birth' => '', 'date_of_wed' => '',
            'cus_img' => '', 'customer_img' => ''
        );
        $data['main_content'] = "tag_parts/form";
        $this->load->view('layout/template', $data);
        $this->load->view('layout/common_customerslider');
    }

    // =========================================================================
    // PAGE: Partly Sale History List
    // =========================================================================

    /**
     * GET /tag_parts/list_view
     * Load the partly sale history view.
     */
    public function list_view()
    {
        $SETT_MOD = self::SETT_MOD;
        $data['access'] = $this->$SETT_MOD->get_access('admin_ret_tag_parts/list_view');
        $data['main_content'] = "tag_parts/list";
        $this->load->view('layout/template', $data);
    }

    // =========================================================================
    // AJAX: Get Base Tag Details
    // =========================================================================

    /**
     * POST /tag_parts/get_tag_details
     * Fetch base tag by scan code; returns tag weights + cost calc params.
     */
    public function get_tag_details()
    {
        $tag_code  = $this->input->post('tag_code');
        $branch_id = $this->session->userdata('id_branch');

        if (empty($tag_code)) {
            echo json_encode(array('status' => false, 'message' => 'Tag code is required'));
            return;
        }

        $tag = $this->ret_tag_parts_model->get_tag_details($tag_code, $branch_id);

        if (!$tag) {
            echo json_encode(array('status' => false, 'message' => 'Tag not found or not available'));
            return;
        }

        // Check for active parts FIRST — status 15 is expected for base tags
        if ($this->ret_tag_parts_model->check_active_parts($tag['tag_id'])) {
            // Fetch existing parts details
            $active_record = $this->db->query(
                "SELECT parts_id FROM ret_tag_parts_master WHERE base_tag_id = ? AND status = 1",
                array($tag['tag_id'])
            )->row();
            $child_rows = $this->db->query(
                "SELECT d.child_tag_id, d.child_gwt, d.child_nwt, d.is_billed, t.tag_code
                 FROM ret_tag_parts_detail d
                 LEFT JOIN ret_taging t ON t.tag_id = d.child_tag_id
                 WHERE d.parts_id = ?
                 ORDER BY d.child_order ASC",
                array($active_record->parts_id)
            )->result_array();
            $child_codes = array_column($child_rows, 'tag_code');

            // Calculate billed vs unbilled balance
            $billed_gwt = 0; $billed_count = 0; $unbilled_count = 0;
            foreach ($child_rows as $cr) {
                if ($cr['is_billed'] == 1) {
                    $billed_gwt += floatval($cr['child_gwt']);
                    $billed_count++;
                } else {
                    $unbilled_count++;
                }
            }

            echo json_encode(array(
                'status'         => true,
                'already_parted'  => true,
                'parts_id'       => $active_record->parts_id,
                'child_codes'    => $child_codes,
                'children'       => $child_rows,
                'data'           => $tag,
                'billed_count'   => $billed_count,
                'unbilled_count' => $unbilled_count,
                'billed_gwt'     => round($billed_gwt, 4),
                'balance_gwt'    => round(floatval($tag['gross_wt']) - $billed_gwt, 4)
            ));
            return;
        }

        // Validation: tag_status must be 0 (Available) — same as estimation tag scan
        if ($tag['tag_status'] != 0) {
            $status_labels = array(0 => 'Available', 1 => 'Billed', 2 => 'Sold', 15 => 'Partly Sold');
            $label = isset($status_labels[$tag['tag_status']]) ? $status_labels[$tag['tag_status']] : 'Not Available';
            echo json_encode(array('status' => false, 'message' => 'Tag is ' . $label . ' — not eligible'));
            return;
        }

        // Validation: tag must not be reserved for a customer order
        if (!empty($tag['id_orderdetails'])) {
            echo json_encode(array('status' => false, 'message' => 'Tag is reserved for a customer order'));
            return;
        }

        // Validation: product must allow partly sale
        if ($tag['ag_parts_enabled'] != 1) {
            echo json_encode(array('status' => false, 'message' => 'Product not configured for Partly Sale'));
            return;
        }

        // ------------------------------------------------------------------
        // Base cost: prefer sell_rate (matches estimation), fallback to dynamic calc
        // ------------------------------------------------------------------
        $base_cost = floatval($tag['sell_rate']);
        $gold_rate = 0;
        $calc_based = intval($tag['calculation_based_on']);
        $cost_limit = floatval($this->admin_settings_model->get_ret_settings('max_cash_amt'));
        if ($cost_limit <= 0) $cost_limit = 200000; // fallback

        $wastage_pct = floatval($tag['retail_max_wastage_percent']);
        $mc_type_id  = intval($tag['tag_mc_type']);
        $mc_value    = floatval($tag['tag_mc_value']);

        // Block MRP items (calc_based 3,4) from partly sale
        if (in_array($calc_based, array(3, 4))) {
            echo json_encode(array('status' => false, 'message' => 'MRP-based items cannot be partly sold'));
            return;
        }

        // Fetch gold rate (needed for fallback calc + JS legend)
        if (in_array($calc_based, array(0, 1, 2))) {
            $gold_rate = $this->ret_tag_parts_model->get_current_gold_rate($tag['current_branch']);

            // Fallback: if sell_rate is 0/NULL, compute cost dynamically
            if ($base_cost <= 0 && $gold_rate > 0) {
                $base_gwt   = floatval($tag['gross_wt']);
                $base_nwt   = floatval($tag['net_wt']);
                $base_stone = floatval($tag['stone_wt']) + floatval($tag['dia_wt']);

                $b_wast_basis = ($calc_based == 0) ? $base_gwt : $base_nwt;
                $b_wast_amt   = ($b_wast_basis * ($wastage_pct / 100)) * $gold_rate;
                $b_mc_amt     = 0;
                if ($mc_type_id == 1) $b_mc_amt = $mc_value;
                elseif ($mc_type_id == 2) $b_mc_amt = $mc_value * (($calc_based == 1) ? $base_nwt : $base_gwt);
                elseif ($mc_type_id == 3) $b_mc_amt = ($gold_rate * ($base_nwt + ($b_wast_basis * ($wastage_pct / 100)))) * ($mc_value / 100);
                $base_cost = ($gold_rate * $base_nwt) + $b_wast_amt + $b_mc_amt + $base_stone;
            }
        }

        // ------------------------------------------------------------------
        // Auto-calculate parts weights (default 2 parts)
        // ------------------------------------------------------------------
        $default_parts = 2;
        $children = $this->ret_tag_parts_model->calculate_parts_weights($tag, $default_parts);
        if ($children === false) {
            echo json_encode(array('status' => false, 'message' => 'Weight calculation failed'));
            return;
        }

        // Generate child tag codes
        $child_codes = $this->ret_tag_parts_model->generate_child_tag_codes($tag['tag_code'], $default_parts);
        foreach ($children as $idx => &$child) {
            $child['tag_code'] = $child_codes[$idx];
        }

        echo json_encode(array(
            'status'       => true,
            'data'         => $tag,
            'base_cost'    => round($base_cost, 0),
            'cost_limit'   => $cost_limit,
            'gold_rate'    => $gold_rate,
            'wastage_pct'  => $wastage_pct,
            'mc_type'      => $mc_type_id,
            'mc_value'     => $mc_value,
            'calc_based'   => $calc_based,
            'children'     => $children,
            'base'         => array(
                'gwt'      => $tag['gross_wt'],
                'nwt'      => $tag['net_wt'],
                'stone_wt' => $tag['stone_wt'],
                'dia_wt'   => $tag['dia_wt'],
                'less_wt'  => $tag['less_wt']
            )
        ));
    }

    // =========================================================================
    // AJAX: Calculate Parts
    // =========================================================================

    /**
     * POST /tag_parts/calculate_parts
     * Re-calculate parts when user increases the count.
     */
    public function calculate_parts()
    {
        $tag_id      = $this->input->post('tag_id');
        $parts_count = intval($this->input->post('parts_count'));
        $min_parts   = intval($this->input->post('min_parts'));
        $branch_id   = $this->session->userdata('id_branch');

        if ($parts_count < 2 || $parts_count > 99) {
            echo json_encode(array('status' => false, 'message' => 'Parts count must be between 2 and 99'));
            return;
        }

        // min_parts is now advisory only — no enforcement
        // User can create any number of parts >= 2

        // Re-fetch fresh tag data
        $tag_code = $this->input->post('tag_code');
        $tag = $this->ret_tag_parts_model->get_tag_details($tag_code, $branch_id);

        if (!$tag) {
            echo json_encode(array('status' => false, 'message' => 'Tag not found'));
            return;
        }

        // Calculate child weights
        $children = $this->ret_tag_parts_model->calculate_parts_weights($tag, $parts_count);

        if (!$children) {
            echo json_encode(array('status' => false, 'message' => 'Weight calculation failed'));
            return;
        }

        // Generate child tag codes for preview
        $child_codes = $this->ret_tag_parts_model->generate_child_tag_codes($tag['tag_code'], $parts_count);

        // Attach codes to children
        foreach ($children as $idx => &$child) {
            $child['tag_code'] = $child_codes[$idx];
        }

        echo json_encode(array(
            'status'   => true,
            'children' => $children,
            'base'     => array(
                'gwt'      => $tag['gross_wt'],
                'nwt'      => $tag['net_wt'],
                'stone_wt' => $tag['stone_wt'],
                'dia_wt'   => $tag['dia_wt'],
                'less_wt'  => $tag['less_wt']
            )
        ));
    }

    // =========================================================================
    // AJAX: Save Partly Sale
    // =========================================================================

    /**
     * POST /tag_parts/save
     * Validate + execute partly sale (transactional).
     */
    public function save()
    {
        $tag_code    = $this->input->post('tag_code');
        $parts_count = intval($this->input->post('parts_count'));
        $children    = $this->input->post('children'); // JSON array
        $session_branch = $this->session->userdata('id_branch');
        $user_id     = $this->session->userdata('uid');

        if (empty($tag_code) || $parts_count < 2) {
            echo json_encode(array('status' => false, 'message' => 'Invalid input'));
            return;
        }

        // Re-fetch fresh tag data (prevent stale data)
        // Pass session branch for filtering only if set; otherwise fetch across all branches
        $tag = $this->ret_tag_parts_model->get_tag_details($tag_code, $session_branch);

        if (!$tag || $tag['tag_status'] != 0) {
            echo json_encode(array('status' => false, 'message' => 'Tag not available'));
            return;
        }

        if ($this->ret_tag_parts_model->check_active_parts($tag['tag_id'])) {
            echo json_encode(array('status' => false, 'message' => 'Tag already has active partly sale'));
            return;
        }

        // Parse children weights
        if (is_string($children)) {
            $children = json_decode($children, true);
        }

        if (!is_array($children) || count($children) != $parts_count) {
            echo json_encode(array('status' => false, 'message' => 'Child weights data mismatch'));
            return;
        }

        // Server-side validation: no negatives, totals match
        foreach ($children as $child) {
            if ($child['gwt'] < 0 || $child['nwt'] < 0 || $child['stone_wt'] < 0 ||
                $child['dia_wt'] < 0 || $child['less_wt'] < 0) {
                echo json_encode(array('status' => false, 'message' => 'Negative weight values not allowed'));
                return;
            }
        }
        // ---------------------------------------------------------------
        // MRP items (calc_based 3,4) — block from partly sale
        // Cost limit is now ADVISORY ONLY (per-row legend in JS)
        // ---------------------------------------------------------------
        $calc_based = intval($tag['calculation_based_on']);
        if (in_array($calc_based, array(3, 4))) {
            echo json_encode(array('status' => false, 'message' => 'MRP-based items cannot be partly sold'));
            return;
        }

        // Get day close entry date for tag_datetime
        $dCData = $this->admin_settings_model->getBranchDayClosingData($session_branch != '' ? $session_branch : $tag['current_branch']);
        $tag_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

        // Branch resolution: base tag's current_branch is authoritative
        // (parts belong to the branch where the tag physically is)
        $branch_id = !empty($tag['current_branch']) ? $tag['current_branch'] : $session_branch;
        if (empty($branch_id) || $branch_id == '' || $branch_id == 0) {
            echo json_encode(array('status' => false, 'message' => 'Branch not determined. Please select a branch.'));
            return;
        }
        $branch_id = intval($branch_id);

        $save_data = array(
            'base_tag'     => $tag,
            'children'     => $children,
            'user_id'      => $user_id,
            'branch_id'    => $branch_id,
            'tag_datetime' => $tag_datetime
        );

        $result = $this->ret_tag_parts_model->save_parts($save_data);

        echo json_encode($result);
    }

    // =========================================================================
    // AJAX: Admin Modify Weights
    // =========================================================================

    /**
     * POST /tag_parts/admin_modify
     * Admin updates child weights post-save.
     */
    public function admin_modify()
    {
        $user_id  = $this->session->userdata('uid');
        $role     = $this->session->userdata('role');
        $special  = $this->session->userdata('special_access');

        // Permission gate
        if ($role != 'admin' && $special != 1) {
            echo json_encode(array('status' => false, 'message' => 'Permission denied'));
            return;
        }

        $parts_id = intval($this->input->post('parts_id'));
        $children = $this->input->post('children');

        if (is_string($children)) {
            $children = json_decode($children, true);
        }

        if (empty($parts_id) || !is_array($children)) {
            echo json_encode(array('status' => false, 'message' => 'Invalid input'));
            return;
        }

        $result = $this->ret_tag_parts_model->update_child_weights($parts_id, $children, $user_id);

        echo json_encode($result);
    }

    // =========================================================================
    // AJAX: Manual Revert
    // =========================================================================

    /**
     * POST /tag_parts/manual_revert
     * Manual revert: cancel children, restore base tag.
     */
    public function manual_revert()
    {
        $user_id  = $this->session->userdata('uid');
        $role     = $this->session->userdata('role');
        $special  = $this->session->userdata('special_access');

        // Permission gate: Admin or Super Admin
        if ($role != 'admin' && $special != 1) {
            echo json_encode(array('status' => false, 'message' => 'Permission denied'));
            return;
        }

        $parts_id = intval($this->input->post('parts_id'));

        if (empty($parts_id)) {
            echo json_encode(array('status' => false, 'message' => 'Partly Sale ID required'));
            return;
        }

        $result = $this->ret_tag_parts_model->manual_revert($parts_id, $user_id);

        echo json_encode($result);
    }

    // =========================================================================
    // AJAX: Get Parts History (DataTable source)
    // =========================================================================

    /**
     * GET /tag_parts/get_parts_history
     * Returns JSON for DataTable.
     */
    public function get_parts_history()
    {
        $filters = array(
            'branch_id' => $this->session->userdata('id_branch'),
            'status'    => $this->input->get('status'),
            'from_date' => $this->input->get('from_date'),
            'to_date'   => $this->input->get('to_date')
        );

        $history = $this->ret_tag_parts_model->get_parts_history($filters);

        echo json_encode(array('data' => $history));
    }

    // =========================================================================
    // AJAX: Get Parts Detail
    // =========================================================================

    /**
     * GET /tag_parts/get_parts_detail/{id}
     * Full parts detail for view/modify modal.
     */
    public function get_parts_detail($parts_id = '')
    {
        if (empty($parts_id)) {
            $parts_id = $this->input->get('parts_id');
        }

        $parts_id = intval($parts_id);

        if (empty($parts_id)) {
            echo json_encode(array('status' => false, 'message' => 'Partly Sale ID required'));
            return;
        }

        $detail = $this->ret_tag_parts_model->get_parts_detail($parts_id);

        if (!$detail) {
            echo json_encode(array('status' => false, 'message' => 'Partly sale not found'));
            return;
        }

        echo json_encode(array('status' => true, 'data' => $detail));
    }

    // =========================================================================
    // AJAX: Get EOD Revert Queue
    // =========================================================================

    /**
     * GET /tag_parts/get_eod_queue
     * Returns active parts eligible for EOD revert.
     */
    public function get_eod_queue()
    {
        $branch_id = $this->session->userdata('id_branch');
        $queue = $this->ret_tag_parts_model->get_eod_revert_queue($branch_id);
        echo json_encode(array('data' => $queue));
    }

    // =========================================================================
    // AJAX: Check if tag already has estimation
    // =========================================================================

    /**
     * POST /admin_ret_tag_parts/checkTagEstimation
     * Checks if a child tag_code already has an active estimation.
     * Returns { exists: true/false }
     */
    public function checkTagEstimation()
    {
        $tag_code = $this->input->post('tag_code');
        if (empty($tag_code)) {
            echo json_encode(array('available' => false, 'message' => 'Tag code is required'));
            return;
        }

        $branch_id = $this->session->userdata('id_branch');

        // Fetch tag status, branch and reservation — same checks as estimation's getTaggingScanBySearch
        $result = $this->db->query(
            "SELECT t.tag_id, t.tag_status, t.current_branch, IFNULL(t.id_orderdetails,'') as id_orderdetails
             FROM ret_taging t
             WHERE t.tag_code = ?
             LIMIT 1",
            array($tag_code)
        );

        if ($result->num_rows() == 0) {
            echo json_encode(array('available' => false, 'message' => 'Tag ' . $tag_code . ' not found'));
            return;
        }

        $tag = $result->row_array();

        // Check: tag must be on-sale (status 0)
        if ($tag['tag_status'] != 0) {
            $status_labels = array(0 => 'Available', 1 => 'Billed', 2 => 'Sold', 15 => 'Partly Sold');
            $label = isset($status_labels[$tag['tag_status']]) ? $status_labels[$tag['tag_status']] : 'Not Available';
            echo json_encode(array('available' => false, 'message' => 'Tag ' . $tag_code . ' is ' . $label));
            return;
        }

        // Check: tag must be in current branch
        if (!empty($branch_id) && $tag['current_branch'] != $branch_id) {
            echo json_encode(array('available' => false, 'message' => 'Tag ' . $tag_code . ' is not in current branch'));
            return;
        }

        // Check: tag must not be reserved for a customer order
        if (!empty($tag['id_orderdetails'])) {
            echo json_encode(array('available' => false, 'message' => 'Tag ' . $tag_code . ' is reserved for a customer order'));
            return;
        }

        echo json_encode(array('available' => true));
    }

    // =========================================================================
    // AJAX: Batch check if tags already have estimation
    // =========================================================================

    /**
     * POST /admin_ret_tag_parts/batchCheckTagEstimation
     * Checks multiple tag codes against estimation items in one query.
     * Input:  { tag_codes: ["XX-1", "XX-2", "XX-3"] }
     * Output: { results: { "XX-1": false, "XX-2": true, "XX-3": false } }
     *         (true = already has estimation)
     */
    public function batchCheckTagEstimation()
    {
        $tag_codes = $this->input->post('tag_codes');
        if (empty($tag_codes) || !is_array($tag_codes)) {
            echo json_encode(array('results' => new stdClass()));
            return;
        }

        // Sanitize and escape codes
        $escaped = array();
        foreach ($tag_codes as $code) {
            $code = trim($code);
            if ($code !== '') {
                $escaped[] = $this->db->escape($code);
            }
        }

        if (empty($escaped)) {
            echo json_encode(array('results' => new stdClass()));
            return;
        }

        // Single query: find which codes have estimation items
        $in_list = implode(',', $escaped);
        $result = $this->db->query(
            "SELECT DISTINCT t.tag_code
             FROM ret_estimation_items ei
             LEFT JOIN ret_taging t ON t.tag_id = ei.tag_id
             WHERE t.tag_code IN ($in_list)"
        );

        $estimated_codes = array();
        foreach ($result->result_array() as $row) {
            $estimated_codes[] = $row['tag_code'];
        }

        // Build result map
        $results = array();
        foreach ($tag_codes as $code) {
            $code = trim($code);
            if ($code !== '') {
                $results[$code] = in_array($code, $estimated_codes);
            }
        }

        echo json_encode(array('results' => $results));
    }
}
