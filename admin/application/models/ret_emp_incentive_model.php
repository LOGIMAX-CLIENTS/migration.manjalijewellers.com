<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ret_emp_incentive_model extends CI_Model
{
    const CONFIG_TABLE = 'ret_emp_incentive_config';
    const RANGES_TABLE = 'ret_emp_incentive_ranges';
    const LOG_TABLE    = 'ret_emp_incentive_log';

    function __construct()
    {
        parent::__construct();
    }

    // ============================================================
    // DROPDOWN HELPERS
    // ============================================================

    public function get_branches()
    {
        $sql = "SELECT id_branch, name FROM branch WHERE active = 1 ORDER BY name";
        return $this->db->query($sql)->result_array();
    }

    public function get_metals()
    {
        $sql = "SELECT id_metal, metal FROM metal WHERE metal_status = 1 ORDER BY metal";
        return $this->db->query($sql)->result_array();
    }

    public function get_categories($metal_id = '')
    {
        $where = '';
        if (!empty($metal_id)) {
            // Support comma-separated metal IDs for multi-select
            $ids = array_map('intval', explode(',', $metal_id));
            $ids = array_filter($ids);
            if (!empty($ids)) {
                $where = " AND id_metal IN (" . implode(',', $ids) . ")";
            }
        }
        $sql = "SELECT id_ret_category, name FROM ret_category WHERE status = 1" . $where . " ORDER BY name";
        return $this->db->query($sql)->result_array();
    }

    public function get_products($cat_id = '')
    {
        $where = '';
        if (!empty($cat_id)) {
            $where = " WHERE cat_id = " . intval($cat_id);
        }
        $sql = "SELECT pro_id, product_name, product_short_code FROM ret_product_master" . $where . " ORDER BY product_name";
        return $this->db->query($sql)->result_array();
    }

    public function get_designs($product_id = '')
    {
        $where = '';
        if (!empty($product_id)) {
            $where = " AND p.pro_id = " . intval($product_id);
        }
        $sql = "SELECT des.design_no, des.design_name, des.design_code
                FROM ret_product_mapping p
                LEFT JOIN ret_design_master des ON des.design_no = p.id_design
                WHERE des.design_no IS NOT NULL" . $where . "
                GROUP BY des.design_no
                ORDER BY des.design_name";
        return $this->db->query($sql)->result_array();
    }

    public function get_sub_designs($design_id = '', $product_id = '')
    {
        $where = '';
        if (!empty($design_id)) {
            $where .= " AND s.id_design = " . intval($design_id);
        }
        if (!empty($product_id)) {
            $where .= " AND s.id_product = " . intval($product_id);
        }
        $sql = "SELECT d.id_sub_design, d.sub_design_name, d.sub_design_code
                FROM ret_sub_design_mapping s
                LEFT JOIN ret_sub_design_master d ON d.id_sub_design = s.id_sub_design
                WHERE s.id_sub_design IS NOT NULL" . $where . "
                GROUP BY d.id_sub_design
                ORDER BY d.sub_design_name";
        return $this->db->query($sql)->result_array();
    }

    public function get_employees()
    {
        $sql = "SELECT id_employee, CONCAT(firstname, ' ', lastname) as emp_name, emp_code 
                FROM employee WHERE active = 1 ORDER BY firstname";
        return $this->db->query($sql)->result_array();
    }

    // ============================================================
    // CONFIG CRUD
    // ============================================================

    /**
     * List all incentive configs with related names
     * Handles comma-separated multi-select scope fields
     */
    public function get_all_configs()
    {
        $sql = "SELECT c.*,
                    CONCAT(e.firstname, ' ', e.lastname) AS created_by_name,
                    CASE c.calc_basis
                        WHEN 1 THEN 'Per Gram'
                        WHEN 2 THEN 'Per Carat'
                        WHEN 3 THEN '% of Sales Value'
                        WHEN 4 THEN 'Age Based'
                    END AS calc_basis_label,
                    CASE c.rate_type
                        WHEN 1 THEN 'Fixed'
                        WHEN 2 THEN 'Weight Range'
                        WHEN 3 THEN 'Carat Range'
                        WHEN 4 THEN 'Age Based'
                    END AS rate_type_label
                FROM " . self::CONFIG_TABLE . " c
                LEFT JOIN employee e ON e.id_employee = c.created_by
                ORDER BY c.id DESC";
        $configs = $this->db->query($sql)->result_array();

        // Resolve multi-select names for each config
        foreach ($configs as &$c) {
            $c['branch_name']     = $this->resolve_names('branch', 'id_branch', 'name', $c['id_branch']);
            $c['metal_name']      = $this->resolve_names('metal', 'id_metal', 'metal', $c['id_metal']);
            $c['category_name']   = $this->resolve_names('ret_category', 'id_ret_category', 'name', $c['id_category']);
            $c['product_name']    = $this->resolve_names('ret_product_master', 'pro_id', 'product_name', $c['id_product']);
            $c['design_name']     = $this->resolve_names('ret_design_master', 'design_no', 'design_name', $c['id_design']);
            $c['sub_design_name'] = $this->resolve_names('ret_sub_design_master', 'id_sub_design', 'sub_design_name', $c['id_sub_design']);
        }
        unset($c);

        return $configs;
    }

    /**
     * Resolve comma-separated IDs to names from a table
     */
    private function resolve_names($table, $id_col, $name_col, $csv_ids)
    {
        if (empty($csv_ids)) return 'All';

        $ids = array_map('intval', explode(',', $csv_ids));
        $ids = array_filter($ids);
        if (empty($ids)) return 'All';

        $this->db->select($name_col);
        $this->db->where_in($id_col, $ids);
        $rows = $this->db->get($table)->result_array();

        if (empty($rows)) return 'All';

        $names = array_column($rows, $name_col);
        return implode(', ', $names);
    }

    /**
     * Get single config with its ranges
     */
    public function get_config($id)
    {
        $this->db->where('id', $id);
        $config = $this->db->get(self::CONFIG_TABLE)->row_array();

        if ($config) {
            $config['ranges'] = $this->get_ranges($id);
        }

        return $config;
    }

    /**
     * Get range slabs for a config
     */
    public function get_ranges($config_id)
    {
        $this->db->where('config_id', $config_id);
        $this->db->order_by('range_from', 'ASC');
        return $this->db->get(self::RANGES_TABLE)->result_array();
    }

    /**
     * Check if a conflicting active config already exists for the same scope.
     * Two configs "conflict" when their scope fields (branch, metal, category, 
     * product, design, sub_design) overlap and they have a different calc_basis.
     * 
     * Returns array of conflicting config IDs + details, or empty array if no conflict.
     * 
     * @param array    $data       Config data to check
     * @param int|null $exclude_id Config ID to exclude (for updates)
     * @return array   Array of conflicting configs
     */
    public function check_duplicate_config($data, $exclude_id = null)
    {
        // Build conditions: for each scope field, a match occurs when either side is NULL (All)
        // or both sides share at least one value (FIND_IN_SET for CSV overlap)
        $scope_fields = array('id_branch', 'id_metal', 'id_category', 'id_product', 'id_design', 'id_sub_design');
        
        $conditions = array();
        foreach ($scope_fields as $field) {
            $val = isset($data[$field]) ? $data[$field] : null;
            if (empty($val)) {
                // New config has "All" for this field — matches any existing config
                $conditions[] = "1=1";
            } else {
                // Check if existing config is also "All" (NULL) or overlaps
                $ids = array_map('intval', explode(',', $val));
                $find_conditions = array();
                foreach ($ids as $single_id) {
                    $find_conditions[] = "FIND_IN_SET({$single_id}, c.{$field})";
                }
                $conditions[] = "(c.{$field} IS NULL OR (" . implode(' OR ', $find_conditions) . "))";
            }
        }
        
        $sql = "SELECT c.id, c.calc_basis, c.rate_type, c.incentive_value,
                    c.id_branch, c.id_category, c.id_product
                FROM " . self::CONFIG_TABLE . " c
                WHERE c.status = 1
                AND " . implode(" AND ", $conditions);
        
        if ($exclude_id) {
            $sql .= " AND c.id != " . intval($exclude_id);
        }
        
        $sql .= " LIMIT 5";
        
        return $this->db->query($sql)->result_array();
    }

    /**
     * Insert or update a config + its ranges (transactional)
     */
    public function save_config($data, $ranges = array(), $id = null)
    {
        $this->db->trans_begin();

        if ($id) {
            // Update
            $this->db->where('id', $id);
            $this->db->update(self::CONFIG_TABLE, $data);

            // Delete old ranges and re-insert
            $this->db->where('config_id', $id);
            $this->db->delete(self::RANGES_TABLE);
        } else {
            // Insert
            $this->db->insert(self::CONFIG_TABLE, $data);
            $id = $this->db->insert_id();
        }

        // Insert ranges if rate_type is range-based
        if (!empty($ranges) && $data['rate_type'] != 1) {
            foreach ($ranges as $range) {
                $range_data = array(
                    'config_id'       => $id,
                    'range_from'      => $range['range_from'],
                    'range_to'        => $range['range_to'],
                    'incentive_value' => $range['incentive_value']
                );
                $this->db->insert(self::RANGES_TABLE, $range_data);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return 0;
        }

        $this->db->trans_commit();
        return $id;
    }

    /**
     * Soft delete (set status = 0)
     */
    public function delete_config($id)
    {
        $this->db->where('id', $id);
        return $this->db->update(self::CONFIG_TABLE, array('status' => 0));
    }

    /**
     * Toggle status (Active <-> Inactive)
     */
    public function toggle_status($id)
    {
        $this->db->where('id', $id);
        $config = $this->db->get(self::CONFIG_TABLE)->row_array();
        if (!$config) return false;

        $new_status = ($config['status'] == 1) ? 0 : 1;
        $this->db->where('id', $id);
        $this->db->update(self::CONFIG_TABLE, array('status' => $new_status));
        return $new_status;
    }

    // ============================================================
    // RULE MATCHING ENGINE
    // ============================================================

    /**
     * Find the most specific matching active incentive rule.
     * Specificity score: sub_design(16) + design(8) + product(4) + category(2) + branch(1)
     * The rule with highest specificity wins.
     */
    public function find_matching_rule($branch_id, $category_id, $product_id, $design_id, $sub_design_id)
    {
        $branch_id    = intval($branch_id);
        $category_id  = intval($category_id);
        $product_id   = intval($product_id);
        $design_id    = intval($design_id);
        $sub_design_id = intval($sub_design_id);

        $sql = "SELECT c.*,
                (
                    IF(c.id_sub_design IS NOT NULL AND FIND_IN_SET({$sub_design_id}, c.id_sub_design), 16, 0) +
                    IF(c.id_design IS NOT NULL AND FIND_IN_SET({$design_id}, c.id_design), 8, 0) +
                    IF(c.id_product IS NOT NULL AND FIND_IN_SET({$product_id}, c.id_product), 4, 0) +
                    IF(c.id_category IS NOT NULL AND FIND_IN_SET({$category_id}, c.id_category), 2, 0) +
                    IF(c.id_branch IS NOT NULL AND FIND_IN_SET({$branch_id}, c.id_branch), 1, 0)
                ) AS specificity
                FROM " . self::CONFIG_TABLE . " c
                WHERE c.status = 1
                AND (c.id_branch IS NULL OR FIND_IN_SET({$branch_id}, c.id_branch))
                AND (c.id_category IS NULL OR FIND_IN_SET({$category_id}, c.id_category))
                AND (c.id_product IS NULL OR FIND_IN_SET({$product_id}, c.id_product))
                AND (c.id_design IS NULL OR FIND_IN_SET({$design_id}, c.id_design))
                AND (c.id_sub_design IS NULL OR FIND_IN_SET({$sub_design_id}, c.id_sub_design))
                ORDER BY specificity DESC
                LIMIT 1";

        $result = $this->db->query($sql)->row_array();

        if ($result && $result['rate_type'] != 1) {
            // Attach range slabs
            $result['ranges'] = $this->get_ranges($result['id']);
        }

        return $result;
    }

    // ============================================================
    // INCENTIVE CALCULATION
    // ============================================================

    /**
     * Calculate incentive amount based on a matched rule.
     *
     * @param array  $rule            The matching config rule
     * @param float  $net_wt          Net weight in grams
     * @param float  $stone_carat_wt  Total stone weight in carats
     * @param float  $item_cost       Total item cost (sales value)
     * @return array ['rate' => applied rate, 'amount' => calculated amount, 'base_qty' => quantity used]
     */
    public function calculate_incentive($rule, $net_wt = 0, $stone_carat_wt = 0, $item_cost = 0)
    {
        if (empty($rule)) {
            return array('rate' => 0, 'amount' => 0, 'base_qty' => 0);
        }

        $calc_basis = intval($rule['calc_basis']);
        $rate_type  = intval($rule['rate_type']);

        // Determine base quantity based on calc_basis
        switch ($calc_basis) {
            case 1: // Per Gram
                $base_qty = floatval($net_wt);
                break;
            case 2: // Per Carat
                $base_qty = floatval($stone_carat_wt);
                break;
            case 3: // Percentage of Total Sales Value
                $base_qty = floatval($item_cost);
                break;
            default:
                $base_qty = 0;
        }

        if ($base_qty <= 0) {
            return array('rate' => 0, 'amount' => 0, 'base_qty' => 0);
        }

        // Determine rate based on rate_type
        $rate = 0;
        switch ($rate_type) {
            case 1: // Fixed
                $rate = floatval($rule['incentive_value']);
                break;

            case 2: // Weight Range
            case 3: // Carat Range
                if (!empty($rule['ranges'])) {
                    // The range qty depends on rate_type:
                    // Weight Range uses gram weight, Carat Range uses carat weight
                    $range_qty = ($rate_type == 2) ? floatval($net_wt) : floatval($stone_carat_wt);

                    foreach ($rule['ranges'] as $slab) {
                        if ($range_qty >= floatval($slab['range_from']) && $range_qty <= floatval($slab['range_to'])) {
                            $rate = floatval($slab['incentive_value']);
                            break;
                        }
                    }
                }
                break;
        }

        if ($rate <= 0) {
            return array('rate' => 0, 'amount' => 0, 'base_qty' => $base_qty);
        }

        // Calculate final amount
        if ($calc_basis == 3) {
            // Percentage: amount = (base_qty * rate) / 100
            $amount = ($base_qty * $rate) / 100;
        } else {
            // Per Gram / Per Carat: amount = base_qty * rate
            $amount = $base_qty * $rate;
        }

        return array(
            'rate'     => round($rate, 4),
            'amount'   => round($amount, 4),
            'base_qty' => round($base_qty, 4)
        );
    }

    // ============================================================
    // INCENTIVE LOGGING
    // ============================================================

    /**
     * Insert a record into the incentive log
     */
    public function log_incentive($data)
    {
        $this->db->insert(self::LOG_TABLE, $data);
        return $this->db->insert_id();
    }

    // ============================================================
    // REPORT QUERIES
    // ============================================================

    /**
     * Live-calculate employee sales incentive from billing data.
     *
     * Flow: ret_billing → ret_estimation → ret_estimation_items
     *       → match against active config rules → calculate incentive
     *       → JOIN with log table for is_paid status
     *
     * Employee source: item_emp_id (per-item) primary, fallback to
     *                  ret_billing.id_employee (comma-separated split)
     */
    public function get_incentive_report($filters)
    {
        $date_from   = isset($filters['date_from']) ? $filters['date_from'] : '';
        $date_to     = isset($filters['date_to']) ? $filters['date_to'] : '';
        $id_branch   = isset($filters['id_branch']) ? intval($filters['id_branch']) : 0;
        $id_employee = isset($filters['id_employee']) ? intval($filters['id_employee']) : 0;
        $pay_status  = isset($filters['pay_status']) ? $filters['pay_status'] : '';

        // Step 1: Get all billed estimation items within the date range
        $sql = "SELECT
                    b.bill_id,
                    b.bill_date,
                    b.sales_ref_no,
                    b.id_branch,
                    b.id_employee AS bill_employee,
                    br.name AS branch_name,
                    ei.est_item_id,
                    ei.esti_id,
                    ei.item_emp_id,
                    ei.product_id,
                    ei.design_id,
                    ei.id_sub_design,
                    ei.net_wt,
                    ei.gross_wt,
                    ei.item_cost,
                    pm.cat_id AS id_category,
                    IFNULL(cat.name, '') AS category_name,
                    IFNULL(pm.product_name, '') AS product_name,
                    IFNULL(dm.design_name, '') AS design_name,
                    IFNULL(sd.sub_design_name, '') AS sub_design_name
                FROM ret_billing b
                JOIN ret_estimation e ON e.estbillid = b.bill_id
                JOIN ret_estimation_items ei ON ei.esti_id = e.estimation_id
                LEFT JOIN branch br ON br.id_branch = b.id_branch
                LEFT JOIN ret_product_master pm ON pm.pro_id = ei.product_id
                LEFT JOIN ret_category cat ON cat.id_ret_category = pm.cat_id
                LEFT JOIN ret_design_master dm ON dm.design_no = ei.design_id
                LEFT JOIN ret_sub_design_master sd ON sd.id_sub_design = ei.id_sub_design
                WHERE b.bill_status = 1
                AND b.bill_type = 1";

        if (!empty($date_from)) {
            $sql .= " AND DATE(b.bill_date) >= '" . $this->db->escape_str($date_from) . "'";
        }
        if (!empty($date_to)) {
            $sql .= " AND DATE(b.bill_date) <= '" . $this->db->escape_str($date_to) . "'";
        }
        if ($id_branch > 0) {
            $sql .= " AND b.id_branch = " . $id_branch;
        }

        $sql .= " ORDER BY b.bill_date DESC, b.bill_id, ei.est_item_id";

        $items = $this->db->query($sql)->result_array();

        if (empty($items)) {
            return array();
        }

        // Step 2: Get all log entries for matching (to check is_paid status)
        $paid_map = $this->get_paid_log_map($date_from, $date_to, $id_branch);

        // Step 3: For each item, resolve employee(s) and calculate incentive
        $result = array();

        foreach ($items as $item) {
            // Determine employee IDs — item-level first, then bill-level (comma split)
            $emp_ids = array();
            if (!empty($item['item_emp_id']) && $item['item_emp_id'] > 0) {
                $emp_ids[] = intval($item['item_emp_id']);
            } elseif (!empty($item['bill_employee'])) {
                // Handle comma-separated employee IDs
                $parts = explode(',', $item['bill_employee']);
                foreach ($parts as $p) {
                    $eid = intval(trim($p));
                    if ($eid > 0) $emp_ids[] = $eid;
                }
            }

            if (empty($emp_ids)) continue;

            // Find matching active incentive config rule
            $branch_id     = intval($item['id_branch']);
            $category_id   = intval($item['id_category']);
            $product_id    = intval($item['product_id']);
            $design_id     = intval($item['design_id']);
            $sub_design_id = intval($item['id_sub_design']);

            $rule = $this->find_matching_rule($branch_id, $category_id, $product_id, $design_id, $sub_design_id);

            // If no active rule matches, incentive = 0
            $incentive_amt  = 0;
            $incentive_rate = 0;
            $base_qty       = 0;
            $calc_basis     = 0;
            $config_id      = 0;

            if (!empty($rule)) {
                $stone_carat = $this->get_stone_carat_weight_by_est_item($item['est_item_id']);
                $calc = $this->calculate_incentive(
                    $rule,
                    floatval($item['net_wt']),
                    $stone_carat,
                    floatval($item['item_cost'])
                );
                $incentive_amt  = $calc['amount'];
                $incentive_rate = $calc['rate'];
                $base_qty       = $calc['base_qty'];
                $calc_basis     = intval($rule['calc_basis']);
                $config_id      = intval($rule['id']);
            }

            // For each employee on this item
            foreach ($emp_ids as $emp_id) {
                // Check paid status from log
                $log_key     = $item['bill_id'] . '_' . $item['est_item_id'] . '_' . $emp_id;
                $is_paid     = isset($paid_map[$log_key]) ? intval($paid_map[$log_key]['is_paid']) : 0;
                $log_id      = isset($paid_map[$log_key]) ? intval($paid_map[$log_key]['id']) : 0;
                $paid_amount = isset($paid_map[$log_key]) ? floatval($paid_map[$log_key]['paid_amount']) : 0;

                // Apply pay_status filter
                if ($pay_status === '1' && $is_paid != 1) continue;
                if ($pay_status === '0' && $is_paid != 0) continue;

                // Apply employee filter
                if ($id_employee > 0 && $emp_id != $id_employee) continue;

                // Get employee name
                $emp = $this->get_employee_name($emp_id);

                $calc_basis_label = '';
                switch ($calc_basis) {
                    case 1: $calc_basis_label = 'Per Gram'; break;
                    case 2: $calc_basis_label = 'Per Carat'; break;
                    case 3: $calc_basis_label = '% of Value'; break;
                }

                $result[] = array(
                    'bill_id'          => $item['bill_id'],
                    'est_item_id'      => $item['est_item_id'],
                    'bill_date'        => date('Y-m-d', strtotime($item['bill_date'])),
                    'sales_ref_no'     => $item['sales_ref_no'],
                    'id_employee'      => $emp_id,
                    'emp_name'         => $emp ? $emp['emp_name'] : 'Unknown',
                    'emp_code'         => $emp ? $emp['emp_code'] : '',
                    'id_branch'        => $item['id_branch'],
                    'branch_name'      => $item['branch_name'],
                    'category_name'    => $item['category_name'],
                    'product_name'     => $item['product_name'],
                    'design_name'      => $item['design_name'],
                    'sub_design_name'  => $item['sub_design_name'],
                    'gross_wt'         => $item['gross_wt'],
                    'less_wt'          => floatval($item['gross_wt']) - floatval($item['net_wt']),
                    'net_wt'           => $item['net_wt'],
                    'item_cost'        => $item['item_cost'],
                    'calc_basis_label' => $calc_basis_label,
                    'base_qty'         => $base_qty,
                    'incentive_rate'   => $incentive_rate,
                    'incentive_amount' => $incentive_amt,
                    'config_id'        => $config_id,
                    'is_paid'          => $is_paid,
                    'paid_amount'      => $paid_amount,
                    'log_id'           => $log_id
                );
            }
        }

        return $result;
    }

    /**
     * Get paid/unpaid log map keyed by bill_id_estItemId_empId
     */
    private function get_paid_log_map($date_from, $date_to, $id_branch)
    {
        $sql = "SELECT id, bill_id, bill_det_id, id_employee, is_paid, IFNULL(paid_amount, 0) AS paid_amount
                FROM " . self::LOG_TABLE . " WHERE 1=1";

        if (!empty($date_from)) {
            $sql .= " AND bill_date >= '" . $this->db->escape_str($date_from) . "'";
        }
        if (!empty($date_to)) {
            $sql .= " AND bill_date <= '" . $this->db->escape_str($date_to) . "'";
        }
        if ($id_branch > 0) {
            $sql .= " AND id_branch = " . $id_branch;
        }

        $rows = $this->db->query($sql)->result_array();
        $map  = array();

        foreach ($rows as $row) {
            $key = $row['bill_id'] . '_' . $row['bill_det_id'] . '_' . $row['id_employee'];
            $map[$key] = $row;
        }

        return $map;
    }

    /**
     * Get employee name and code
     */
    private function get_employee_name($emp_id)
    {
        static $cache = array();
        if (isset($cache[$emp_id])) return $cache[$emp_id];

        $sql = "SELECT CONCAT(firstname, ' ', lastname) AS emp_name, emp_code
                FROM employee WHERE id_employee = " . intval($emp_id);
        $row = $this->db->query($sql)->row_array();
        $cache[$emp_id] = $row;
        return $row;
    }

    /**
     * Get stone carat weight from estimation item stones
     */
    public function get_stone_carat_weight_by_est_item($est_item_id)
    {
        $sql = "SELECT IFNULL(SUM(wt), 0) AS total_carat
                FROM ret_estimation_item_stones
                WHERE est_item_id = " . intval($est_item_id);
        $result = $this->db->query($sql)->row();
        return $result ? floatval($result->total_carat) : 0;
    }

    /**
     * Mark incentive entries as paid (Given).
     * Inserts into log if not exists, updates is_paid if exists.
     *
     * @param array $items  Array of items with bill_id, est_item_id, id_employee, config_id, etc.
     * @param int   $user_id  Current user marking as paid
     * @return int  Number of records marked
     */
    public function mark_incentive_paid($items, $user_id)
    {
        $count = 0;
        $now   = date('Y-m-d H:i:s');

        foreach ($items as $item) {
            $bill_id      = intval($item['bill_id']);
            $est_item_id  = intval($item['est_item_id']);
            $emp_id       = intval($item['id_employee']);

            // Check if log entry exists
            $this->db->where('bill_id', $bill_id);
            $this->db->where('bill_det_id', $est_item_id);
            $this->db->where('id_employee', $emp_id);
            $existing = $this->db->get(self::LOG_TABLE)->row();

            if ($existing) {
                // Update existing
                $this->db->where('id', $existing->id);
                $this->db->update(self::LOG_TABLE, array(
                    'is_paid'   => 1,
                    'paid_date' => $now,
                    'paid_by'   => $user_id
                ));
            } else {
                // Insert new log with is_paid = 1
                $this->db->insert(self::LOG_TABLE, array(
                    'bill_id'          => $bill_id,
                    'bill_det_id'      => $est_item_id,
                    'id_employee'      => $emp_id,
                    'config_id'        => intval($item['config_id']),
                    'id_branch'        => intval($item['id_branch']),
                    'id_category'      => isset($item['id_category']) ? intval($item['id_category']) : null,
                    'id_product'       => isset($item['id_product']) ? intval($item['id_product']) : null,
                    'id_design'        => isset($item['id_design']) ? intval($item['id_design']) : null,
                    'id_sub_design'    => isset($item['id_sub_design']) ? intval($item['id_sub_design']) : null,
                    'calc_basis'       => intval($item['calc_basis']),
                    'base_qty'         => floatval($item['base_qty']),
                    'incentive_rate'   => floatval($item['incentive_rate']),
                    'incentive_amount' => floatval($item['incentive_amount']),
                    'bill_date'        => $item['bill_date'],
                    'is_paid'          => 1,
                    'paid_date'        => $now,
                    'paid_by'          => $user_id
                ));
            }
            $count++;
        }

        return $count;
    }

    /**
     * Save partial payment for an employee's incentive within a period.
     * Upserts a summary-level log entry per employee per period.
     */
    public function save_partial_payment($data, $user_id)
    {
        $emp_id      = intval($data['id_employee']);
        $paid_amount = floatval($data['paid_amount']);
        $remarks     = isset($data['remarks']) ? $data['remarks'] : '';
        $id_branch   = intval($data['id_branch']);
        $date_from   = $data['date_from'];
        $date_to     = $data['date_to'];
        $now         = date('Y-m-d H:i:s');

        // Get all unpaid/partially paid log entries for this employee in the period
        $sql = "SELECT id, incentive_amount, paid_amount 
                FROM " . self::LOG_TABLE . "
                WHERE id_employee = {$emp_id}
                AND bill_date BETWEEN '{$this->db->escape_str($date_from)}' AND '{$this->db->escape_str($date_to)}'"
                . ($id_branch > 0 ? " AND id_branch = {$id_branch}" : '') .
                " AND is_paid = 0
                ORDER BY bill_date ASC";

        $rows = $this->db->query($sql)->result_array();
        $remaining = $paid_amount;
        $count = 0;

        foreach ($rows as $row) {
            if ($remaining <= 0) break;

            $item_incentive = floatval($row['incentive_amount']);
            $already_paid   = floatval($row['paid_amount']);
            $item_balance   = $item_incentive - $already_paid;

            if ($item_balance <= 0) continue;

            $pay_this = min($remaining, $item_balance);
            $new_paid = $already_paid + $pay_this;
            $is_fully_paid = ($new_paid >= $item_incentive) ? 1 : 0;

            $this->db->where('id', $row['id']);
            $this->db->update(self::LOG_TABLE, array(
                'paid_amount' => $new_paid,
                'is_paid'     => $is_fully_paid,
                'paid_date'   => $now,
                'paid_by'     => $user_id,
                'remarks'     => $remarks
            ));

            $remaining -= $pay_this;
            $count++;
        }

        // If there are items with no log entries yet, we need to create them
        // Get the incentive report to find items without log entries
        if ($remaining > 0) {
            $filters = array(
                'date_from'   => $date_from,
                'date_to'     => $date_to,
                'id_branch'   => $id_branch,
                'id_employee' => $emp_id,
                'pay_status'  => '0'
            );
            $report = $this->get_incentive_report($filters);

            foreach ($report as $item) {
                if ($remaining <= 0) break;
                if ($item['log_id'] > 0) continue; // Already has a log entry

                $item_incentive = floatval($item['incentive_amount']);
                if ($item_incentive <= 0) continue;

                $pay_this = min($remaining, $item_incentive);
                $is_fully_paid = ($pay_this >= $item_incentive) ? 1 : 0;

                $this->db->insert(self::LOG_TABLE, array(
                    'bill_id'          => intval($item['bill_id']),
                    'bill_det_id'      => intval($item['est_item_id']),
                    'id_employee'      => $emp_id,
                    'config_id'        => intval($item['config_id']),
                    'id_branch'        => intval($item['id_branch']),
                    'calc_basis'       => 0,
                    'base_qty'         => floatval($item['base_qty']),
                    'incentive_rate'   => floatval($item['incentive_rate']),
                    'incentive_amount' => $item_incentive,
                    'paid_amount'      => $pay_this,
                    'bill_date'        => $item['bill_date'],
                    'is_paid'          => $is_fully_paid,
                    'paid_date'        => $now,
                    'paid_by'          => $user_id,
                    'remarks'          => $remarks
                ));

                $remaining -= $pay_this;
                $count++;
            }
        }

        return array('count' => $count, 'applied' => $paid_amount - $remaining);
    }

    /**
     * Get employee-wise incentive summary (live calculated)
     */
    public function get_incentive_summary($filters)
    {
        $report = $this->get_incentive_report($filters);
        $summary = array();

        foreach ($report as $row) {
            $emp_id = $row['id_employee'];
            if (!isset($summary[$emp_id])) {
                $summary[$emp_id] = array(
                    'id_employee'        => $emp_id,
                    'emp_name'           => $row['emp_name'],
                    'emp_code'           => $row['emp_code'],
                    'total_transactions' => 0,
                    'total_incentive'    => 0,
                    'total_sales_value'  => 0,
                    'total_gross_wt'     => 0,
                    'total_less_wt'      => 0,
                    'total_net_wt'       => 0,
                    'total_paid_amount'  => 0,
                    'total_pending'      => 0
                );
            }
            $amt = floatval($row['incentive_amount']);
            $paid_amt = floatval($row['paid_amount']);
            $summary[$emp_id]['total_transactions']++;
            $summary[$emp_id]['total_incentive']    += $amt;
            $summary[$emp_id]['total_sales_value']   += floatval($row['item_cost']);
            $summary[$emp_id]['total_gross_wt']      += floatval($row['gross_wt']);
            $summary[$emp_id]['total_less_wt']       += floatval($row['less_wt']);
            $summary[$emp_id]['total_net_wt']        += floatval($row['net_wt']);
            $summary[$emp_id]['total_paid_amount']   += $paid_amt;
            $summary[$emp_id]['total_pending']       += ($amt - $paid_amt);
        }

        return array_values($summary);
    }

    // ============================================================
    // UTILITY: Get stone carat weight for a tag/bill detail
    // ============================================================

    /**
     * Get total stone carat weight for a bill detail item
     */
    public function get_stone_carat_weight($bill_det_id)
    {
        $sql = "SELECT IFNULL(SUM(wt), 0) AS total_carat_wt 
                FROM ret_billing_item_stones 
                WHERE bill_det_id = " . intval($bill_det_id);
        $result = $this->db->query($sql)->row();
        return $result ? floatval($result->total_carat_wt) : 0;
    }

    /**
     * Get category ID for a product
     */
    public function get_category_for_product($product_id)
    {
        $sql = "SELECT cat_id FROM ret_product_master WHERE pro_id = " . intval($product_id);
        $result = $this->db->query($sql)->row();
        return $result ? intval($result->cat_id) : 0;
    }

    // ============================================================
    // INCENTIVE PAYMENT LOG (Partial Payments)
    // ============================================================

    const PAYMENT_LOG_TABLE = 'ret_emp_incentive_payment_log';

    /**
     * Save a partial incentive payment log entry
     */
    public function save_payment_log($data, $user_id)
    {
        $insert = array(
            'id_employee'     => intval($data['id_employee']),
            'id_branch'       => isset($data['id_branch']) ? intval($data['id_branch']) : null,
            'date_from'       => $data['date_from'],
            'date_to'         => $data['date_to'],
            'original_amount' => floatval($data['original_amount']),
            'given_amount'    => floatval($data['given_amount']),
            'remarks'         => isset($data['remarks']) ? $data['remarks'] : '',
            'created_by'      => intval($user_id),
            'created_at'      => date('Y-m-d H:i:s'),
            'status'          => 1
        );

        $this->db->insert(self::PAYMENT_LOG_TABLE, $insert);
        return $this->db->insert_id();
    }

    /**
     * Get all payment log entries for an employee within a date range
     */
    public function get_payment_logs($id_employee, $date_from, $date_to, $id_branch = 0)
    {
        $sql = "SELECT pl.*, CONCAT(e.firstname, ' ', e.lastname) AS created_by_name
                FROM " . self::PAYMENT_LOG_TABLE . " pl
                LEFT JOIN employee e ON e.id_employee = pl.created_by
                WHERE pl.id_employee = " . intval($id_employee) . "
                AND pl.status = 1
                AND (
                    (pl.date_from <= '" . $this->db->escape_str($date_to) . "' AND pl.date_to >= '" . $this->db->escape_str($date_from) . "')
                )";

        if ($id_branch > 0) {
            $sql .= " AND pl.id_branch = " . intval($id_branch);
        }

        $sql .= " ORDER BY pl.created_at DESC";

        return $this->db->query($sql)->result_array();
    }

    /**
     * Get total already-given amount for an employee in a date range
     */
    public function get_total_given($id_employee, $date_from, $date_to, $id_branch = 0)
    {
        $sql = "SELECT IFNULL(SUM(given_amount), 0) AS total_given
                FROM " . self::PAYMENT_LOG_TABLE . "
                WHERE id_employee = " . intval($id_employee) . "
                AND status = 1
                AND (
                    date_from <= '" . $this->db->escape_str($date_to) . "' AND date_to >= '" . $this->db->escape_str($date_from) . "'
                )";

        if ($id_branch > 0) {
            $sql .= " AND id_branch = " . intval($id_branch);
        }

        $result = $this->db->query($sql)->row();
        return $result ? floatval($result->total_given) : 0;
    }

    /**
     * Soft-delete a payment log entry
     */
    public function delete_payment_log($id)
    {
        $this->db->where('id', intval($id));
        return $this->db->update(self::PAYMENT_LOG_TABLE, array('status' => 0));
    }
}
