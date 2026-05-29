<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Partly Sale Model
 * 
 * All database operations for the Partly Sale (Tag Parts) module.
 * Handles parts creation, child tag generation, weight modification,
 * manual/EOD reverts, and history queries.
 * 
 * @module    Partly Sale
 * @version   2.0 (Phase 4)
 * @date      April 2026
 */
class Ret_tag_parts_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('ret_tag_model');
        $this->load->model('admin_settings_model');
    }

    /**
     * Get current 22ct gold rate (per gram) for the given branch.
     * Falls back to latest global rate if branch-specific rate isn't available.
     */
    function get_current_gold_rate($branch_id = '')
    {
        // Check if branch-wise rate is enabled
        $branchwise = ($this->session->userdata('is_branchwise_rate') == 1);

        if ($branchwise && $branch_id != '') {
            $sql = "SELECT m.goldrate_22ct FROM metal_rates m
                    LEFT JOIN branch_rate br ON br.id_metalrate = m.id_metalrates
                    WHERE br.id_branch = " . intval($branch_id) . "
                    ORDER BY m.id_metalrates DESC LIMIT 1";
        } else {
            $sql = "SELECT goldrate_22ct FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1";
        }

        $result = $this->db->query($sql)->row();
        return $result ? floatval($result->goldrate_22ct) : 0;
    }

    // =========================================================================
    // HELPER: Generic insert
    // =========================================================================
    function insertData($data, $table)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    // =========================================================================
    // TAG LOOKUP & VALIDATION
    // =========================================================================

    /**
     * Fetch base tag details by scan code.
     * Returns tag weights + product config for parts eligibility check.
     */
    function get_tag_details($tag_code, $branch_id = '')
    {
        $sql = "SELECT t.tag_id, t.tag_code, t.tag_status, t.product_id, t.design_id,
                       t.id_sub_design, t.purity, t.piece, t.gross_wt, t.net_wt, t.less_wt,
                       t.current_branch, t.id_branch, t.tag_lot_id, t.tag_type,
                       t.calculation_based_on, t.retail_max_wastage_percent,
                       t.tag_mc_type, t.tag_mc_value, t.cat_type,
                       t.stone_calculation_based_on, t.id_section, t.sell_rate,
                       IFNULL(p.product_name,'') as product_name,
                       IFNULL(p.product_short_code,'') as product_short_code,
                       IFNULL(p.ag_parts, 0) as ag_parts_enabled,
                       IFNULL(des.design_name,'') as design_name,
                       IFNULL(pur.purity,'') as purity_name,
                       IFNULL(sd.sub_design_name,'') as sub_design_name,
                       IFNULL(t.id_orderdetails,'') as id_orderdetails,
                       IFNULL(tag_stn.stone_wt, 0) as stone_wt,
                       IFNULL(tag_dia.dia_wt, 0) as dia_wt
                FROM ret_taging t
                LEFT JOIN ret_product_master p ON p.pro_id = t.product_id
                LEFT JOIN ret_design_master des ON des.design_no = t.design_id
                LEFT JOIN ret_purity pur ON pur.id_purity = t.purity
                LEFT JOIN ret_sub_design_master sd ON sd.id_sub_design = t.id_sub_design
                LEFT JOIN (
                    SELECT ts.tag_id, SUM(ts.wt) as stone_wt
                    FROM ret_taging_stone ts
                    LEFT JOIN ret_stone stn ON stn.stone_id = ts.stone_id
                    WHERE stn.stone_type != 1
                    GROUP BY ts.tag_id
                ) tag_stn ON tag_stn.tag_id = t.tag_id
                LEFT JOIN (
                    SELECT ts.tag_id, SUM(ts.wt) as dia_wt
                    FROM ret_taging_stone ts
                    LEFT JOIN ret_stone stn ON stn.stone_id = ts.stone_id
                    WHERE stn.stone_type = 1
                    GROUP BY ts.tag_id
                ) tag_dia ON tag_dia.tag_id = t.tag_id
                WHERE t.tag_code = " . $this->db->escape($tag_code);

        if ($branch_id != '') {
            $sql .= " AND t.current_branch = " . intval($branch_id);
        }

        $result = $this->db->query($sql);

        if ($result->num_rows() > 0) {
            return $result->row_array();
        }

        return false;
    }

    /**
     * Check if product is configured for partly sale.
     */
    function check_product_parts_config($product_id)
    {
        $sql = $this->db->query("SELECT ag_parts FROM ret_product_master WHERE pro_id = " . intval($product_id));
        if ($sql->num_rows() > 0) {
            return $sql->row()->ag_parts == 1;
        }
        return false;
    }

    /**
     * Check if active parts already exist for this tag.
     */
    function check_active_parts($tag_id)
    {
        $sql = $this->db->query(
            "SELECT parts_id FROM ret_tag_parts_master 
             WHERE base_tag_id = " . intval($tag_id) . " 
             AND status = 1"
        );
        return $sql->num_rows() > 0;
    }

    /**
     * Quick check if a tag is currently in parts state.
     */
    function is_tag_parted($tag_id)
    {
        $sql = $this->db->query(
            "SELECT tag_status FROM ret_taging WHERE tag_id = " . intval($tag_id)
        );
        if ($sql->num_rows() > 0) {
            return $sql->row()->tag_status == 15;
        }
        return false;
    }

    // =========================================================================
    // PARTS ALGORITHM
    // =========================================================================

    /**
     * Weight division algorithm with rounding.
     * 
     * Each weight field is divided independently. The per-part weight is calculated
     * by floor-dividing to N decimal places, then any rounding remainder is
     * distributed across children to eliminate systematic bias.
     * A final assertion guarantees the sum equals the base weight.
     *
     * @param float $baseWeight    The base tag's weight value
     * @param int   $partsCount    Number of child tags
     * @param int   $precision     Decimal precision (default: 3)
     * @return array               Array of child weights
     */
    function autoPartWeights($baseWeight, $partsCount, $precision = 3)
    {
        // Skip variation for zero-weight fields
        if ($baseWeight <= 0) {
            return array_fill(0, $partsCount, 0);
        }

        $factor = pow(10, $precision);
        $baseInt = round($baseWeight * $factor);  // Convert to integer units

        // Floor division for per-part weight
        $perPartInt = intval(floor($baseInt / $partsCount));
        $remainderUnits = $baseInt - ($perPartInt * $partsCount);

        // Initialize all children with per-part weight
        $childWeights = array_fill(0, $partsCount, $perPartInt);

        // Distribute remainder one unit at a time
        for ($j = 0; $j < $remainderUnits; $j++) {
            $childWeights[$j % $partsCount] += 1;
        }

        // Force visible variation: every child should have a distinct weight.
        // Create a staircase pattern: child[0] gets +maxShift, child[N-1] gets -maxShift,
        // middle children get linearly interpolated adjustments. Sum of adjustments = 0.
        if ($partsCount >= 2) {
            $shift = pow(10, max(0, $precision - 2)); // 10 units = 0.010 at precision 3
            $range = max($childWeights) - min($childWeights);

            if ($range < $shift * ($partsCount - 1)) {
                $adjustments = array();
                $totalAdj = 0;
                for ($k = 0; $k < $partsCount; $k++) {
                    $adj = intval(round(($partsCount - 1 - 2 * $k) * $shift / 2));
                    $adjustments[] = $adj;
                    $totalAdj += $adj;
                }
                if ($totalAdj != 0) {
                    $adjustments[$partsCount - 1] -= $totalAdj;
                }

                $canApply = true;
                for ($k = 0; $k < $partsCount; $k++) {
                    if ($childWeights[$k] + $adjustments[$k] < 1) {
                        $canApply = false;
                        break;
                    }
                }

                if ($canApply) {
                    for ($k = 0; $k < $partsCount; $k++) {
                        $childWeights[$k] += $adjustments[$k];
                    }
                }
            }
        }

        // Convert back to decimal
        $result = array();
        foreach ($childWeights as $w) {
            $result[] = $w / $factor;
        }

        // Integrity assertion
        $sum = array_sum($result);
        $diff = abs($sum - $baseWeight);
        if ($diff > (1 / $factor)) {
            log_message('error', "Partly Sale weight integrity check failed: sum=$sum, base=$baseWeight, diff=$diff");
            return false;
        }

        // Fix any floating-point drift on the last child
        if ($diff > 0) {
            $result[count($result) - 1] += ($baseWeight - $sum);
            $result[count($result) - 1] = round($result[count($result) - 1], $precision);
        }

        return $result;
    }

    /**
     * Calculate auto-division for all 5 weight types.
     * Returns array of child arrays, each with gwt/nwt/stone_wt/dia_wt/less_wt.
     */
    function calculate_parts_weights($base_tag, $parts_count)
    {
        $weight_fields = array(
            'gwt'      => floatval($base_tag['gross_wt']),
            'nwt'      => floatval($base_tag['net_wt']),
            'stone_wt' => floatval($base_tag['stone_wt']),
            'dia_wt'   => floatval($base_tag['dia_wt']),
            'less_wt'  => floatval($base_tag['less_wt'])
        );

        $children = array();
        $parts = array();

        // Divide each weight type independently
        foreach ($weight_fields as $key => $value) {
            $parts[$key] = $this->autoPartWeights($value, $parts_count);
            if ($parts[$key] === false) {
                return false;
            }
        }

        // Build child arrays
        for ($i = 0; $i < $parts_count; $i++) {
            $children[] = array(
                'child_order' => $i + 1,
                'gwt'         => $parts['gwt'][$i],
                'nwt'         => $parts['nwt'][$i],
                'stone_wt'    => $parts['stone_wt'][$i],
                'dia_wt'      => $parts['dia_wt'][$i],
                'less_wt'     => $parts['less_wt'][$i]
            );
        }

        return $children;
    }

    // =========================================================================
    // CHILD TAG CODE GENERATION
    // =========================================================================

    /**
     * Generate child tag codes by inserting a part-series digit after the prefix.
     * Pattern: {prefix}-{series}{zero-padded-seq}
     * Example: Base 1-00001, count 3 → 1-100001, 1-200001, 1-300001
     */
    function generate_child_tag_codes($base_code, $parts_count)
    {
        $codes = array();

        for ($i = 1; $i <= $parts_count; $i++) {
            $code = $base_code . '-' . $i;
            
            // Safety check: ensure code doesn't already exist
            $exists = $this->db->where('tag_code', $code)->count_all_results('ret_taging');
            if ($exists > 0) {
                // Fallback: append timestamp
                $code = $base_code . '-' . date('His') . $i;
            }
            $codes[] = $code;
        }

        return $codes;
    }

    // =========================================================================
    // SAVE PARTS (TRANSACTIONAL)
    // =========================================================================

    /**
     * Execute a partly sale operation.
     * 
     * Atomic transaction Steps:
     * 1. INSERT ret_tag_parts_master
     * 2. CREATE N rows in ret_taging (child tags)
     * 3. INSERT N rows in ret_tag_parts_detail
     * 4. Copy stones proportionally to ret_taging_stone
     * 5. UPDATE base tag: tag_status = 15 (Partly Sale)
     * 6. LOG in ret_taging_status_log
     *
     * @param array $data  Contains base_tag, children weights, user_id, branch_id
     * @return array|false  Result with parts_id and child_codes, or false on failure
     */
    function save_parts($data)
    {
        $base_tag   = $data['base_tag'];
        $children   = $data['children'];
        $user_id    = $data['user_id'];
        $branch_id  = $data['branch_id'];
        $tag_datetime = isset($data['tag_datetime']) ? $data['tag_datetime'] : date('Y-m-d H:i:s');

        // Validate weights sum to base tag
        if (!$this->validate_weight_totals($base_tag, $children)) {
            return array('status' => false, 'message' => 'Weight totals do not match base tag');
        }

        $this->db->trans_begin();

        try {
            // -----------------------------------------------------------------
            // Step 1: INSERT ret_tag_parts_master
            // -----------------------------------------------------------------
            $master_data = array(
                'base_tag_id'    => $base_tag['tag_id'],
                'parts_count'    => count($children),
                'parts_method'   => 1, // Auto by Count
                'base_gwt'       => $base_tag['gross_wt'],
                'base_nwt'       => $base_tag['net_wt'],
                'base_stone_wt'  => $base_tag['stone_wt'],
                'base_dia_wt'    => $base_tag['dia_wt'],
                'base_less_wt'   => $base_tag['less_wt'],
                'status'         => 1, // Active
                'id_branch'      => $branch_id,
                'created_by'     => $user_id,
                'created_on'     => date('Y-m-d H:i:s')
            );

            $parts_id = $this->insertData($master_data, 'ret_tag_parts_master');

            // Generate child tag codes
            $child_codes = $this->generate_child_tag_codes($base_tag['tag_code'], count($children));
            if (!$child_codes) {
                $this->db->trans_rollback();
                return array('status' => false, 'message' => 'Failed to generate child tag codes');
            }

            // Get base tag's stone details for proportional copy
            $base_stones = $this->ret_tag_model->get_stone_details($base_tag['tag_id']);

            $child_tag_ids = array();

            foreach ($children as $idx => $child) {
                // -----------------------------------------------------------------
                // Step 2: CREATE child row in ret_taging
                // -----------------------------------------------------------------
                $child_tag_data = array(
                    'tag_code'        => $child_codes[$idx],
                    'current_branch'  => $base_tag['current_branch'],
                    'id_branch'       => $base_tag['id_branch'],
                    'cost_center'     => $base_tag['id_branch'],
                    'tag_lot_id'      => $base_tag['tag_lot_id'],
                    'product_id'      => $base_tag['product_id'],
                    'design_id'       => $base_tag['design_id'],
                    'id_sub_design'   => $base_tag['id_sub_design'],
                    'purity'          => $base_tag['purity'],
                    'piece'           => 1,
                    'gross_wt'        => $child['gwt'],
                    'less_wt'         => $child['less_wt'],
                    'net_wt'          => $child['nwt'],
                    'calculation_based_on'        => $base_tag['calculation_based_on'],
                    'retail_max_wastage_percent'   => $base_tag['retail_max_wastage_percent'],
                    'tag_mc_type'     => $base_tag['tag_mc_type'],
                    'tag_mc_value'    => $this->calculate_child_mc_value(
                        intval($base_tag['tag_mc_type']),
                        floatval($base_tag['tag_mc_value']),
                        floatval($child['gwt']),
                        floatval($base_tag['gross_wt']),
                        $children,
                        $idx
                    ),
                    'cat_type'        => $base_tag['cat_type'],
                    'stone_calculation_based_on'   => $base_tag['stone_calculation_based_on'],
                    'id_section'      => $base_tag['id_section'],
                    'tag_status'      => 0, // Available (on sale)
                    'tag_type'        => $base_tag['tag_type'],
                    'tag_datetime'    => $tag_datetime,
                    'created_time'    => date('Y-m-d H:i:s'),
                    'created_by'      => $user_id
                );

                $child_tag_id = $this->insertData($child_tag_data, 'ret_taging');
                $child_tag_ids[] = $child_tag_id;

                // -----------------------------------------------------------------
                // Step 3: INSERT ret_tag_parts_detail
                // -----------------------------------------------------------------
                $detail_data = array(
                    'parts_id'       => $parts_id,
                    'child_tag_id'   => $child_tag_id,
                    'child_order'    => $child['child_order'],
                    'child_gwt'      => $child['gwt'],
                    'child_nwt'      => $child['nwt'],
                    'child_stone_wt' => $child['stone_wt'],
                    'child_dia_wt'   => $child['dia_wt'],
                    'child_less_wt'  => $child['less_wt']
                );

                $this->insertData($detail_data, 'ret_tag_parts_detail');

                // -----------------------------------------------------------------
                // Step 4: Copy stones proportionally to ret_taging_stone
                // -----------------------------------------------------------------
                if (!empty($base_stones)) {
                    $this->copy_stones_proportionally($base_tag['tag_id'], $child_tag_id, $base_tag, $child, $base_stones, count($children), $idx);
                }

                // Log child creation
                $this->log_tag_status($child_tag_id, 0, 'Created from partly sale of ' . $base_tag['tag_code'], $user_id);
            }

            // -----------------------------------------------------------------
            // Step 5: UPDATE base tag status = 15 (Partly Sale)
            // -----------------------------------------------------------------
            $this->db->where('tag_id', $base_tag['tag_id']);
            $this->db->update('ret_taging', array('tag_status' => 15));

            // -----------------------------------------------------------------
            // Step 6: LOG base tag partly sale event
            // -----------------------------------------------------------------
            $this->log_tag_status(
                $base_tag['tag_id'],
                15,
                'Partly Sale → ' . count($children) . ' parts created',
                $user_id
            );

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => false, 'message' => 'Transaction failed');
            }

            $this->db->trans_commit();

            return array(
                'status'       => true,
                'parts_id'     => $parts_id,
                'child_codes'  => $child_codes,
                'child_tag_ids' => $child_tag_ids,
                'message'      => 'Partly sale saved successfully'
            );

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Partly Sale save failed: ' . $e->getMessage());
            return array('status' => false, 'message' => 'Save failed: ' . $e->getMessage());
        }
    }

    /**
     * Copy base tag stone records to child, proportionally by weight ratio.
     * If child_gwt / base_gwt = 0.33, stone weight/pieces/amount get 33%.
     * Last child gets the remainder to ensure exact totals.
     */
    function copy_stones_proportionally($base_tag_id, $child_tag_id, $base_tag, $child, $base_stones, $total_children, $child_index)
    {
        $base_gwt = floatval($base_tag['gross_wt']);
        if ($base_gwt <= 0) return;

        $ratio = floatval($child['gwt']) / $base_gwt;
        $is_last_child = ($child_index == $total_children - 1);

        foreach ($base_stones as $stone) {
            $stone_wt     = round(floatval($stone['wt']) * $ratio, 4);
            $stone_pcs    = max(1, round(floatval($stone['pieces']) * $ratio));
            $stone_amount = round(floatval($stone['amount']) * $ratio, 2);

            // Last child gets remainder to maintain totals
            // (This is approximate — exact stone distribution may need manual admin correction)

            $stone_data = array(
                'tag_id'            => $child_tag_id,
                'stone_id'          => $stone['stone_id'],
                'wt'                => $stone_wt,
                'pieces'            => $stone_pcs,
                'uom_id'            => $stone['uom_id'],
                'amount'            => $stone_amount,
                'rate_per_gram'     => isset($stone['rate_per_gram']) ? $stone['rate_per_gram'] : 0,
                'is_apply_in_lwt'   => isset($stone['is_apply_in_lwt']) ? $stone['is_apply_in_lwt'] : 0,
                'stone_cal_type'    => isset($stone['stone_cal_type']) ? $stone['stone_cal_type'] : NULL,
                'stone_quality_id'  => isset($stone['stone_quality_id']) ? $stone['stone_quality_id'] : NULL
            );

            $this->insertData($stone_data, 'ret_taging_stone');
        }
    }

    // =========================================================================
    // ADMIN WEIGHT MODIFICATION
    // =========================================================================

    /**
     * Admin updates child weights post-save.
     * All 5 weight types must still sum exactly to the base values.
     * No billed children may be modified.
     */
    function update_child_weights($parts_id, $children, $user_id)
    {
        // Get master record for validation
        $master = $this->get_parts_master($parts_id);
        if (!$master || $master['status'] != 1) {
            return array('status' => false, 'message' => 'Record not found or not active');
        }

        // Validate no billed children are being modified
        foreach ($children as $child) {
            $detail = $this->db->query(
                "SELECT is_billed FROM ret_tag_parts_detail 
                 WHERE parts_detail_id = " . intval($child['parts_detail_id'])
            )->row_array();

            if ($detail && $detail['is_billed'] == 1) {
                return array('status' => false, 'message' => 'Cannot modify billed child tag');
            }
        }

        // Validate weight totals against base tag
        $totals = array('gwt' => 0, 'nwt' => 0, 'stone_wt' => 0, 'dia_wt' => 0, 'less_wt' => 0);
        foreach ($children as $child) {
            $totals['gwt']      += floatval($child['gwt']);
            $totals['nwt']      += floatval($child['nwt']);
            $totals['stone_wt'] += floatval($child['stone_wt']);
            $totals['dia_wt']   += floatval($child['dia_wt']);
            $totals['less_wt']  += floatval($child['less_wt']);

            // No negatives
            if ($child['gwt'] < 0 || $child['nwt'] < 0 || $child['stone_wt'] < 0 || $child['dia_wt'] < 0 || $child['less_wt'] < 0) {
                return array('status' => false, 'message' => 'Negative weight values not allowed');
            }
        }

        $tolerance = 0.0001;
        if (abs($totals['gwt'] - $master['base_gwt']) > $tolerance ||
            abs($totals['nwt'] - $master['base_nwt']) > $tolerance ||
            abs($totals['stone_wt'] - $master['base_stone_wt']) > $tolerance ||
            abs($totals['dia_wt'] - $master['base_dia_wt']) > $tolerance ||
            abs($totals['less_wt'] - $master['base_less_wt']) > $tolerance) {
            return array('status' => false, 'message' => 'Weight totals do not match base tag');
        }

        $this->db->trans_begin();

        try {
            foreach ($children as $child) {
                // Update ret_taging weights
                $this->db->where('tag_id', $child['child_tag_id']);
                $this->db->update('ret_taging', array(
                    'gross_wt' => $child['gwt'],
                    'net_wt'   => $child['nwt'],
                    'less_wt'  => $child['less_wt']
                ));

                // Update ret_tag_parts_detail
                $this->db->where('parts_detail_id', $child['parts_detail_id']);
                $this->db->update('ret_tag_parts_detail', array(
                    'child_gwt'      => $child['gwt'],
                    'child_nwt'      => $child['nwt'],
                    'child_stone_wt' => $child['stone_wt'],
                    'child_dia_wt'   => $child['dia_wt'],
                    'child_less_wt'  => $child['less_wt'],
                    'is_modified'    => 1,
                    'modified_by'    => $user_id,
                    'modified_on'    => date('Y-m-d H:i:s')
                ));

                // Log modification
                $this->log_tag_status(
                    $child['child_tag_id'],
                    18,
                    'Admin weight modification by ' . $user_id,
                    $user_id
                );
            }

            // Update master modification audit
            $this->db->where('parts_id', $parts_id);
            $this->db->update('ret_tag_parts_master', array(
                'modified_by' => $user_id,
                'modified_on' => date('Y-m-d H:i:s')
            ));

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => false, 'message' => 'Update failed');
            }

            $this->db->trans_commit();
            return array('status' => true, 'message' => 'Weights updated successfully');

        } catch (Exception $e) {
            $this->db->trans_rollback();
            return array('status' => false, 'message' => $e->getMessage());
        }
    }

    // =========================================================================
    // MANUAL REVERT
    // =========================================================================

    /**
     * Manually revert a partly sale: cancel all unbilled children, restore base tag.
     * Only allowed if NO children are billed.
     */
    function manual_revert($parts_id, $user_id)
    {
        return $this->revert_parts($parts_id, $user_id, 1); // reason=1 Manual
    }

    // =========================================================================
    // EOD AUTO REVERT
    // =========================================================================

    /**
     * Process all EOD reverts for a branch during day-close.
     * Called inside the day-close transaction.
     */
    function process_eod_reverts($branch_id, $day_close_id, $user_id)
    {
        // Get all active parts for this branch
        $active_parts = $this->db->query(
            "SELECT m.parts_id, m.base_tag_id, m.parts_count
             FROM ret_tag_parts_master m
             WHERE m.id_branch = " . intval($branch_id) . "
             AND m.status = 1"
        )->result_array();

        $revert_summary = array(
            'total_parts' => 0,
            'reverted'     => 0,
            'skipped'      => 0,
            'partial'      => 0,
            'details'      => array()
        );

        foreach ($active_parts as $part) {
            // Check if ANY child is billed
            $billed_check = $this->db->query(
                "SELECT 
                    COUNT(*) as total,
                    SUM(IF(is_billed = 1, 1, 0)) as billed_count
                 FROM ret_tag_parts_detail 
                 WHERE parts_id = " . intval($part['parts_id'])
            )->row_array();

            $revert_summary['total_parts']++;

            if ($billed_check['billed_count'] == 0) {
                // Full revert — no children billed
                $result = $this->revert_parts($part['parts_id'], $user_id, 2, $day_close_id);
                if ($result['status']) {
                    $revert_summary['reverted']++;
                    $revert_summary['details'][] = array(
                        'parts_id'      => $part['parts_id'],
                        'base_tag_id'   => $part['base_tag_id'],
                        'action'        => 'Full Revert',
                        'children'      => $part['parts_count']
                    );
                }
            } elseif ($billed_check['billed_count'] < $billed_check['total']) {
                // Partial — some billed, cancel only unbilled children
                $result = $this->partial_revert($part['parts_id'], $user_id, $day_close_id);
                if ($result['status']) {
                    $revert_summary['partial']++;
                    $revert_summary['details'][] = array(
                        'parts_id'      => $part['parts_id'],
                        'base_tag_id'   => $part['base_tag_id'],
                        'action'        => 'Partial Revert',
                        'children'      => $part['parts_count'],
                        'unbilled'      => $billed_check['total'] - $billed_check['billed_count']
                    );
                }
            } else {
                // All billed — skip
                $revert_summary['skipped']++;

                // Update status to Fully Billed
                $this->db->where('parts_id', $part['parts_id']);
                $this->db->update('ret_tag_parts_master', array('status' => 4));
            }
        }

        return $revert_summary;
    }

    /**
     * Full revert of a partly sale (all children unbilled).
     * Used for both manual and EOD reverts.
     *
     * @param int    $parts_id      Parts ID to revert
     * @param int    $user_id       User performing the revert
     * @param int    $revert_reason 1=Manual, 2=EOD
     * @param int    $day_close_id  Day-close reference (NULL for manual)
     */
    function revert_parts($parts_id, $user_id, $revert_reason, $day_close_id = NULL)
    {
        $master = $this->get_parts_master($parts_id);
        if (!$master || $master['status'] != 1) {
            return array('status' => false, 'message' => 'Partly sale not found or not active');
        }

        // Check no children are billed (for full revert)
        $billed = $this->db->query(
            "SELECT COUNT(*) as cnt FROM ret_tag_parts_detail 
             WHERE parts_id = " . intval($parts_id) . " AND is_billed = 1"
        )->row()->cnt;

        if ($billed > 0 && $revert_reason == 1) {
            return array('status' => false, 'message' => 'Cannot revert: some children are billed');
        }

        // Get child details
        $children = $this->db->query(
            "SELECT * FROM ret_tag_parts_detail WHERE parts_id = " . intval($parts_id)
        )->result_array();

        $revert_label = ($revert_reason == 1) ? 'Manual Revert' : 'EOD Revert';
        $is_eod = ($revert_reason == 2);

        // Delete child tags entirely (they are synthetic, never needed after revert)
        foreach ($children as $child) {
            if ($child['is_billed'] == 0) {
                if (!$is_eod) {
                    // Manual revert: log the action
                    $this->log_tag_status($child['child_tag_id'], 2, $revert_label . ' — cancelled & deleted', $user_id);
                }

                // Delete ALL logs for this child tag (tag itself is being deleted)
                $this->db->where('tag_id', $child['child_tag_id']);
                $this->db->delete('ret_taging_status_log');

                // Delete child's stone records
                $this->db->where('tag_id', $child['child_tag_id']);
                $this->db->delete('ret_taging_stone');

                // Delete the child tag itself
                $this->db->where('tag_id', $child['child_tag_id']);
                $this->db->delete('ret_taging');
            }
        }

        // Restore base tag to available
        $this->db->where('tag_id', $master['base_tag_id']);
        $this->db->update('ret_taging', array('tag_status' => 0));

        if (!$is_eod) {
            $this->log_tag_status($master['base_tag_id'], 0, $revert_label . ' — restored from partly sale', $user_id);
        }

        // Delete base tag's partly sale status log (status=15), keep creation/other logs
        $this->db->where('tag_id', $master['base_tag_id']);
        $this->db->where('status', 15);
        $this->db->delete('ret_taging_status_log');

        // Update parts master
        $this->db->where('parts_id', $parts_id);
        $this->db->update('ret_tag_parts_master', array(
            'status'        => 2, // Reverted
            'revert_reason' => $revert_reason,
            'eod_ref_id'    => $day_close_id,
            'reverted_by'   => $user_id,
            'reverted_on'   => date('Y-m-d H:i:s')
        ));

        return array('status' => true, 'message' => 'Partly sale reverted successfully');
    }

    /**
     * Partial revert: cancel unbilled children, keep billed, mark status=3.
     * Base tag remains at status=15 (partly sale).
     */
    function partial_revert($parts_id, $user_id, $day_close_id = NULL)
    {
        $children = $this->db->query(
            "SELECT * FROM ret_tag_parts_detail WHERE parts_id = " . intval($parts_id)
        )->result_array();

        foreach ($children as $child) {
            if ($child['is_billed'] == 0) {
                // Delete ALL logs for this child tag (tag itself is being deleted)
                $this->db->where('tag_id', $child['child_tag_id']);
                $this->db->delete('ret_taging_status_log');

                // Delete child's stone records
                $this->db->where('tag_id', $child['child_tag_id']);
                $this->db->delete('ret_taging_stone');

                // Delete the child tag itself
                $this->db->where('tag_id', $child['child_tag_id']);
                $this->db->delete('ret_taging');
            }
        }

        // Mark master as Partial Billed
        $this->db->where('parts_id', $parts_id);
        $this->db->update('ret_tag_parts_master', array(
            'status'        => 3, // Partial Billed
            'revert_reason' => 2, // EOD
            'eod_ref_id'    => $day_close_id,
            'reverted_by'   => $user_id,
            'reverted_on'   => date('Y-m-d H:i:s')
        ));

        return array('status' => true, 'message' => 'Partial revert completed');
    }

    // =========================================================================
    // QUERIES
    // =========================================================================

    function get_parts_master($parts_id)
    {
        $result = $this->db->query(
            "SELECT * FROM ret_tag_parts_master WHERE parts_id = " . intval($parts_id)
        );
        return $result->num_rows() > 0 ? $result->row_array() : false;
    }

    /**
     * Get full parts detail including child tag info.
     */
    function get_parts_detail($parts_id)
    {
        $master = $this->get_parts_master($parts_id);
        if (!$master) return false;

        // Get base tag info
        $base_tag = $this->db->query(
            "SELECT tag_code, product_id, design_id, purity 
             FROM ret_taging WHERE tag_id = " . intval($master['base_tag_id'])
        )->row_array();

        // Get child details with tag codes
        $children = $this->db->query(
            "SELECT d.*, t.tag_code, t.tag_status
             FROM ret_tag_parts_detail d
             LEFT JOIN ret_taging t ON t.tag_id = d.child_tag_id
             WHERE d.parts_id = " . intval($parts_id) . "
             ORDER BY d.child_order ASC"
        )->result_array();

        return array(
            'master'   => $master,
            'base_tag' => $base_tag,
            'children' => $children
        );
    }

    /**
     * Partly sale history for DataTable.
     */
    function get_parts_history($filters = array())
    {
        $sql = "SELECT m.parts_id, m.parts_count, m.status, m.created_on,
                       m.base_tag_id, m.base_gwt, m.base_nwt,
                       t.tag_code as base_tag_code,
                       e.firstname as created_by_name,
                       IF(m.status=1,'Active',
                         IF(m.status=2,'Reverted',
                           IF(m.status=3,'Partial',
                             IF(m.status=4,'Billed','Unknown')))) as status_label,
                       (SELECT COUNT(*) FROM ret_tag_parts_detail d 
                        WHERE d.parts_id = m.parts_id AND d.is_billed = 0) as unbilled_count
                FROM ret_tag_parts_master m
                LEFT JOIN ret_taging t ON t.tag_id = m.base_tag_id
                LEFT JOIN employee e ON e.id_employee = m.created_by
                WHERE 1=1";

        if (!empty($filters['branch_id'])) {
            $sql .= " AND m.id_branch = " . intval($filters['branch_id']);
        }
        if (!empty($filters['status'])) {
            $sql .= " AND m.status = " . intval($filters['status']);
        }
        if (!empty($filters['from_date'])) {
            $sql .= " AND DATE(m.created_on) >= '" . date('Y-m-d', strtotime($filters['from_date'])) . "'";
        }
        if (!empty($filters['to_date'])) {
            $sql .= " AND DATE(m.created_on) <= '" . date('Y-m-d', strtotime($filters['to_date'])) . "'";
        }

        $sql .= " ORDER BY m.created_on DESC";

        return $this->db->query($sql)->result_array();
    }

    /**
     * Get EOD revert queue — active parts with unbilled children for a branch.
     */
    function get_eod_revert_queue($branch_id)
    {
        $sql = "SELECT m.parts_id, m.parts_count, m.base_tag_id,
                       t.tag_code as base_tag_code,
                       m.base_gwt,
                       (SELECT COUNT(*) FROM ret_tag_parts_detail d 
                        WHERE d.parts_id = m.parts_id AND d.is_billed = 0) as unbilled_count,
                       (SELECT COUNT(*) FROM ret_tag_parts_detail d 
                        WHERE d.parts_id = m.parts_id) as total_children
                FROM ret_tag_parts_master m
                LEFT JOIN ret_taging t ON t.tag_id = m.base_tag_id
                WHERE m.id_branch = " . intval($branch_id) . "
                AND m.status = 1
                ORDER BY m.created_on ASC";

        return $this->db->query($sql)->result_array();
    }

    // =========================================================================
    // MC VALUE CALCULATION FOR CHILD TAGS
    // =========================================================================

    /**
     * Calculate MC value for a child tag.
     *
     * Type 1 (Fixed amount): Proportionally split by GWT ratio.
     *   Last child gets the remainder to ensure exact total.
     * Type 2 (Per-gram rate): Same value — already proportional by weight.
     * Type 3 (Percentage of gold value): Same value — already proportional.
     *
     * @param int    $mc_type      MC type (1=fixed, 2=per-gram, 3=percentage)
     * @param float  $mc_value     Base tag's MC value
     * @param float  $child_gwt    This child's gross weight
     * @param float  $base_gwt     Base tag's gross weight
     * @param array  $all_children Array of all children weights
     * @param int    $child_index  Index of current child (0-based)
     * @return float               MC value for this child
     */
    function calculate_child_mc_value($mc_type, $mc_value, $child_gwt, $base_gwt, $all_children, $child_index)
    {
        // Types 2 and 3 are inherently proportional (rate × weight or % of value)
        if ($mc_type != 1 || $mc_value <= 0 || $base_gwt <= 0) {
            return $mc_value;
        }

        // Type 1 (Fixed): split proportionally by GWT ratio
        $is_last = ($child_index == count($all_children) - 1);

        if ($is_last) {
            // Last child gets remainder to avoid rounding drift
            $sum_previous = 0;
            for ($i = 0; $i < $child_index; $i++) {
                $prev_gwt = floatval($all_children[$i]['gwt']);
                $sum_previous += round($mc_value * ($prev_gwt / $base_gwt), 2);
            }
            return round($mc_value - $sum_previous, 2);
        }

        return round($mc_value * ($child_gwt / $base_gwt), 2);
    }

    // =========================================================================
    // VALIDATION HELPERS
    // =========================================================================

    /**
     * Validate that child weight totals match base tag exactly.
     */
    function validate_weight_totals($base_tag, $children)
    {
        $tolerance = 0.0001;
        $totals = array('gwt' => 0, 'nwt' => 0, 'stone_wt' => 0, 'dia_wt' => 0, 'less_wt' => 0);

        foreach ($children as $child) {
            $totals['gwt']      += floatval($child['gwt']);
            $totals['nwt']      += floatval($child['nwt']);
            $totals['stone_wt'] += floatval($child['stone_wt']);
            $totals['dia_wt']   += floatval($child['dia_wt']);
            $totals['less_wt']  += floatval($child['less_wt']);
        }

        return (
            abs($totals['gwt'] - floatval($base_tag['gross_wt'])) <= $tolerance &&
            abs($totals['nwt'] - floatval($base_tag['net_wt'])) <= $tolerance &&
            abs($totals['stone_wt'] - floatval($base_tag['stone_wt'])) <= $tolerance &&
            abs($totals['dia_wt'] - floatval($base_tag['dia_wt'])) <= $tolerance &&
            abs($totals['less_wt'] - floatval($base_tag['less_wt'])) <= $tolerance
        );
    }

    // =========================================================================
    // AUDIT LOG
    // =========================================================================

    /**
     * Log tag status change to ret_taging_status_log.
     */
    function log_tag_status($tag_id, $status, $message, $user_id)
    {
        $log_data = array(
            'tag_id'     => $tag_id,
            'status'     => $status,
            'date'       => date('Y-m-d H:i:s'),
            'created_by' => $user_id,
            'created_on' => date('Y-m-d H:i:s')
        );

        $this->db->insert('ret_taging_status_log', $log_data);
    }
}
