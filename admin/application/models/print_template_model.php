<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class print_template_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Template Categories Mapping (ID => Name)
    const CATEGORIES = [
        1 => 'Sales',
        2 => 'Sales & Purchase',
        3 => 'Sales & Return',
        4 => 'Purchase',
        5 => 'Order Advance',
        6 => 'Advance',
        7 => 'Sales Return',
        8 => 'Credit Bill Payment',
        9 => 'Order Delivery',
        10 => 'Chit Proclose',
        11 => 'Repair Order Delivery',
        12 => 'Supplier Sales Bill',
        13 => 'Sales Transfer',
        14 => 'Sales Ret Transfer',
        15 => 'Approval stock bill delivery',
        16 => 'Estimation',
        17 => 'Issue Receipt',
        0 => 'Common'
    ];

    public function get_template_categories() {
        return self::CATEGORIES;
    }

    // Get all templates with optional filters
    public function get_all($filters = []) {
        if (!empty($filters['category'])) {
            $this->db->where('template_category', $filters['category']);
        }
        if (isset($filters['is_active'])) {
            $this->db->where('is_active', $filters['is_active']);
        }
        $this->db->order_by('id_template', 'DESC');
        return $this->db->get('print_templates')->result_array();
    }

    // Get table for DataTables (lightweight)
    public function get_all_ajax() {
        $this->db->select('id_template, template_code, template_name, template_category, paper_size, is_default, is_active, updated_at');
        $this->db->order_by('id_template', 'DESC');
        return $this->db->get('print_templates')->result_array();
    }

    public function get_by_id($id) {
        return $this->db->where('id_template', $id)->get('print_templates')->row_array();
    }

    // Get the default template for a category, prioritizing branch-specific
    public function get_default($category, $branch_id = null) {
        $this->db->where('template_category', $category);
        $this->db->where('is_active', 1);
        
        if ($branch_id) {
            $this->db->where("(id_branch = " . $this->db->escape($branch_id) . " OR id_branch IS NULL)", NULL, FALSE);
        } else {
            $this->db->where('id_branch IS NULL');
        }

        $this->db->order_by('is_default', 'DESC'); // Default first
        $this->db->order_by('id_branch', 'DESC');  // Specific branch first
        $this->db->limit(1);
        
        return $this->db->get('print_templates')->row_array();
    }

    public function insert($data) {
        $this->db->insert('print_templates', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $this->db->where('id_template', $id);
        return $this->db->update('print_templates', $data);
    }

    public function delete($id) {
        return $this->db->where('id_template', $id)->delete('print_templates');
    }

    /**
     * Get multiple templates by an array of IDs (for bulk operations).
     */
    public function get_by_ids($ids) {
        if (empty($ids)) return [];
        $this->db->where_in('id_template', $ids);
        return $this->db->get('print_templates')->result_array();
    }

    /**
     * Delete multiple templates by an array of IDs.
     * Returns the number of affected rows.
     */
    public function delete_by_ids($ids) {
        if (empty($ids)) return 0;
        $this->db->where_in('id_template', $ids);
        $this->db->delete('print_templates');
        return $this->db->affected_rows();
    }

    /**
     * Deactivate all templates with the given template_code, except the one with $exclude_id.
     */
    public function deactivate_by_code($template_code, $exclude_id = null) {
        $this->db->where('template_code', $template_code);
        if ($exclude_id) {
            $this->db->where('id_template !=', $exclude_id);
        }
        return $this->db->update('print_templates', ['is_active' => 0]);
    }

    /**
     * Deactivate all templates sharing the same grouping `code`, except the one with $exclude_id.
     * The `code` column links all versions of the same template.
     */
    public function deactivate_by_group_code($group_code, $exclude_id = null) {
        if (empty($group_code)) return false;
        $this->db->where('code', $group_code);
        if ($exclude_id) {
            $this->db->where('id_template !=', $exclude_id);
        }
        return $this->db->update('print_templates', ['is_active' => 0]);
    }

    /**
     * Activate the given template and deactivate all others with the same grouping `code`.
     */
    public function activate_and_deactivate_others($id) {
        $template = $this->get_by_id($id);
        if (!$template) return false;

        // Determine the grouping code (use `code` column, fallback to template_code)
        $group_code = !empty($template['code']) ? $template['code'] : $template['template_code'];

        // Deactivate all versions with the same grouping code
        $this->deactivate_by_group_code($group_code, $id);

        // Activate this one
        return $this->update($id, ['is_active' => 1]);
    }

    // Set a template as default for its category/branch scope
    public function set_default($id) {
        $template = $this->get_by_id($id);
        if (!$template) return false;

        $category = $template['template_category'];
        $branch_id = $template['id_branch'];

        // Scoping: If global (branch null), unset other global defaults. 
        // If branch specific, unset other defaults for that branch.
        $this->db->where('template_category', $category);
        
        if ($branch_id === NULL) {
            $this->db->where('id_branch IS NULL');
        } else {
            $this->db->where('id_branch', $branch_id);
        }

        $this->db->update('print_templates', ['is_default' => 0]);

        // Set the current one as default
        return $this->update($id, ['is_default' => 1]);
    }

    public function get_placeholders($category) {
        $db_placeholders = $this->db->where('category', $category)
                        ->order_by('placeholder_group', 'ASC')
                        ->order_by('display_order', 'ASC')
                        ->get('print_template_placeholders')
                        ->result_array();

        $valid_billing_categories = array_keys(self::CATEGORIES);
        $legacy_types = [
            'billing', 'receipt', 'sales_bill', 'sales_purchase_bill', 
            'sales_purchase_return_bill', 'purchase_bill', 'order_advance', 
            'advance', 'sales_return_bill', 'credit_collection', 
            'order_delivery', 'chit_pre_close', 'repair_order_delivery', 
            'supplier_sales_bill', 'sales_transfer', 'sales_return_transfer', 
            'approval_sales_bill'
        ];

        if (in_array($category, $valid_billing_categories) || in_array($category, $legacy_types)) {
            // Auto-discover scalar fields from sample data (single source of truth)
            $sample = $this->get_sample_data($category);
            $dynamic = [];
            foreach ($sample as $key => $value) {
                if (is_array($value) || is_bool($value)) continue; // Skip loops/flags
                $dynamic[] = [
                    'placeholder_key'   => $key,
                    'placeholder_label' => self::_auto_label($key),
                    'sample_value'      => (string)$value,
                    'category'          => $category,
                    'placeholder_group' => self::_auto_group($key),
                ];
            }

            // Merge: DB placeholders take precedence, dynamic fills gaps
            $existing_keys = array_column($db_placeholders, 'placeholder_key');
            foreach ($dynamic as $d) {
                if (!in_array($d['placeholder_key'], $existing_keys)) {
                    $db_placeholders[] = $d;
                }
            }
        }

        return $db_placeholders;
    }

    /**
     * Auto-generate a human-readable label from a snake_case key.
     * e.g. 'customer_name' → 'Customer Name', 'sgst_amount' → 'SGST Amount'
     */
    public static function _auto_label($key) {
        // Acronyms/abbreviations that should stay uppercase
        $acronyms = ['gstin','pan','hsn','irn','ack','sgst','cgst','igst','upi','tcs','tds','huid','qr','mc','va'];
        $words = explode('_', $key);
        $labeled = [];
        foreach ($words as $w) {
            if (in_array(strtolower($w), $acronyms)) {
                $labeled[] = strtoupper($w);
            } else {
                $labeled[] = ucfirst($w);
            }
        }
        return implode(' ', $labeled);
    }

    /**
     * Auto-detect group from key prefix.
     * e.g. 'customer_name' → 'Customer', 'gold_rate' → 'Rates'
     */
    public static function _auto_group($key) {
        $group_map = [
            'company_' => 'Company', 'branch_' => 'Company',
            'customer_' => 'Customer',
            'invoice_' => 'Bill Details', 'bill_' => 'Bill Details', 'title' => 'Bill Details',
            'irnno' => 'Bill Details', 'ack_' => 'Bill Details', 'billed_' => 'Bill Details',
            'gold_' => 'Rates', 'silver_' => 'Rates',
            'sub_total' => 'Tax & Totals', 'sgst_' => 'Tax & Totals', 'cgst_' => 'Tax & Totals',
            'igst_' => 'Tax & Totals', 'round_' => 'Tax & Totals', 'net_' => 'Tax & Totals',
            'grand_' => 'Tax & Totals', 'amount_in_' => 'Tax & Totals', 'total_' => 'Tax & Totals',
            'tcs_' => 'Tax & Totals', 'tds_' => 'Tax & Totals', 'handling_' => 'Tax & Totals',
            'cash_' => 'Payment', 'card_' => 'Payment', 'upi_' => 'Payment',
            'cheque_' => 'Payment', 'pay_' => 'Payment', 'paid_' => 'Payment', 'balance_' => 'Payment',
            'chit_' => 'Adjustments', 'advance_' => 'Adjustments', 'order_advance_' => 'Adjustments',
            'receipt_' => 'Adjustments', 'exchange_' => 'Adjustments',
            'order_' => 'Order Details', 'delivery_' => 'Order Details',
            'repair_' => 'Repair Details', 'credit_' => 'Credit Details',
            'qr_' => 'Misc', 'app_' => 'Misc', 'remark' => 'Misc',
            'sales_ref_' => 'Reference Numbers', 'pur_ref_' => 'Reference Numbers',
            'counter_' => 'Reference Numbers', 'fin_' => 'Reference Numbers',
        ];
        foreach ($group_map as $prefix => $group) {
            if ($key === $prefix || strpos($key, $prefix) === 0) {
                return $group;
            }
        }
        return 'General';
    }

    // Get variable keys for template designer (field picker / variable panel)
    // No sample data — just returns the declared key names from receipt_helper's registry.
    // The VARIABLE REGISTRY in map_billing_data_to_template() is the single source of truth.
    public function get_sample_data($category) {
        // Load the helper if not already loaded
        $CI =& get_instance();
        $CI->load->helper('receipt');
        $CI->load->helper('template_receipt');

        // Build a minimal mock $data structure so map_billing_data_to_template()
        // runs without errors and returns all declared keys with their defaults.
        $mock_billing = array_fill_keys([
            'bill_id','bill_no','bill_date','bill_type','customer_name','mobile',
            'address1','address2','city','pincode','cus_state','gst_number','pan_no',
            'adhar_no','passport_no','irnno','ack_no','bqrcodeimage',
            'goldrate_22ct','goldrate_18ct','goldrate_24ct','silverrate_1gm',
            'emp_name','round_off_amt','handling_charges','tot_bill_amount',
            'tot_amt_received','note','remark','branch_code','fin_year_code',
            'sales_ref_no','pur_ref_no','s_ret_refno','order_adv_ref_no',
            'approval_ref_no','credit_coll_refno','chit_preclose_refno',
            'id_branch','created_on','credit_due_date','advance_deposit',
            'purchase_ref_no','size_name'
        ], '');
        $mock_billing['bill_type'] = 1;
        $mock_billing['bill_date'] = date('Y-m-d');
        $mock_billing['tot_bill_amount'] = 0;
        $mock_billing['tot_amt_received'] = 0;
        $mock_billing['round_off_amt'] = 0;
        $mock_billing['handling_charges'] = 0;
        $mock_billing['goldrate_22ct'] = 0;
        $mock_billing['goldrate_18ct'] = 0;
        $mock_billing['goldrate_24ct'] = 0;
        $mock_billing['silverrate_1gm'] = 0;
        $mock_billing['id_branch'] = 0;

        $mock_company = array_fill_keys([
            'company_name','company_address','mobile','email',
            'gst_number','pan_no','state','state_code'
        ], '');

        $mock_items = [
            'item_details' => [], 'old_matel_details' => [],
            'return_details' => [], 'sales_trasnfer_details' => [],
            'sales_ret_trans_details' => [], 'order_details' => [],
            'advance_details' => [], 'chit_details' => [],
            'repair_order_details' => [], 'order_adj' => [],
            'tax_details' => [],
            'total_old_qty' => 0, 'total_old_gross_wt' => 0,
            'total_old_net_wt' => 0, 'total_old_amount' => 0,
        ];

        $mock_data = [
            'billing'       => $mock_billing,
            'comp_details'  => $mock_company,
            'est_other_item'=> $mock_items,
            'payment'       => ['pay_details' => [
                ['payment_mode' => 'CASH', 'payment_amount' => 5000, 'transfer_type' => '', 'NB_type' => '', 'cheque_no' => '', 'cheque_date' => '', 'bank_name' => '', 'card_no' => '', 'payment_date' => date('d-m-Y'), 'payment_ref_number' => ''],
                ['payment_mode' => 'NB',   'payment_amount' => 3000, 'transfer_type' => 'UPI', 'NB_type' => 'UPI', 'cheque_no' => '', 'cheque_date' => '', 'bank_name' => '', 'card_no' => '', 'payment_date' => date('d-m-Y'), 'payment_ref_number' => 'REF123'],
                ['payment_mode' => 'CHQ',  'payment_amount' => 10000, 'transfer_type' => '', 'NB_type' => '', 'cheque_no' => '2201', 'cheque_date' => date('d-m-Y'), 'bank_name' => 'HDFC Bank', 'card_no' => '', 'payment_date' => date('d-m-Y'), 'payment_ref_number' => ''],
                ['payment_mode' => 'DC',   'payment_amount' => 4000, 'transfer_type' => '', 'NB_type' => '', 'cheque_no' => '', 'cheque_date' => '', 'bank_name' => '', 'card_no' => '1234', 'payment_date' => date('d-m-Y'), 'payment_ref_number' => '909'],
            ]],

            'metal_rate'    => ['goldrate_22ct'=>0,'goldrate_18ct'=>0,'goldrate_24ct'=>0,'silverrate_1gm'=>0],
            'settings'      => [],
            'receiptDetails'=> [],
            'type'          => '',
        ];

        // Get all declared keys with defaults from the registry
        return tpl_map_billing_data_to_template($mock_data);
    }


    // INTERNAL: Render Engine
    // Can be called by other controllers: $this->print_template_model->render('billing', $data);
    public function render($category, $print_data, $branch_id = null) {
        // Load default template for the specific bill type
        $template = $this->get_default($category, $branch_id);
        
        // Fallback: if no specific template found, try the global/common template (category = 0)
        if (!$template && $category != 0) {
            $template = $this->get_default(0, $branch_id);
        }
        
        if (!$template) {
            return false; // Return false so controller can handle fallback
        }

        
        $html = $this->render_string_template($template['template_html'], $print_data);
        $css = $template['template_css'];
        
        // Determine paper size for @page rule
        $paper_size = isset($template['paper_size']) ? $template['paper_size'] : 'A4';
        $orientation = isset($template['orientation']) ? strtolower($template['orientation']) : 'portrait';
        
        // Build @page size value
        if ($paper_size == 'A4') {
            $page_size = 'A4';
        } else if ($paper_size == 'A5') {
            $page_size = 'A5';
        } else {
            $page_size = 'auto';
        }
        if ($orientation == 'landscape') {
            $page_size .= ' landscape';
        }
        
        // Wrap in a full HTML document with print-ready styles
        $output = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print</title>
    <style>
        @page {
            size: ' . $page_size . ';
            margin: 10mm;
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
        body {
            padding: 10px;
        }
        table { border-collapse: collapse; width: 100%; }
        @media print {
            html, body {
                width: 100%;
                padding: 0;
                margin: 0;
            }
        }
        ' . $css . '
    </style>
</head>
<body>' . $html . '</body>
</html>';
        
        return $output;
    }

    // Helper: Mustache-style replacement
    public function render_string_template($html_template, $data) {
        $html = $html_template;
        
        // 0. Normalize HTML entities — decode numeric entities (&#8377; → ₹, etc.)
        //    and &nbsp; → space, so cleanup regexes can match properly
        $html = preg_replace_callback('/&#(\d+);/', function($m) {
            return mb_chr((int)$m[1], 'UTF-8');
        }, $html);
        $html = preg_replace_callback('/&#x([0-9a-fA-F]+);/', function($m) {
            return mb_chr(hexdec($m[1]), 'UTF-8');
        }, $html);
        $html = str_replace('&nbsp;', ' ', $html);
        
        // Dot-notation alias map: loop key → template prefix
        // e.g. inside {{#items}}, use {{item.sno}} {{item.gross_wt}}
        //      inside {{#purchase_items}}, use {{purchase_item.sno}} etc.
        $loop_aliases = [
            'items'                       => 'item',
            'purchase_items'              => 'purchase_item',
            'return_items'                => 'return_item',
            'order_advance_entries'       => 'order_item',
            'repair_items'                => 'repair_item',
            'chit_items'                  => 'chit_item',
            'credit_collection_history'   => 'credit_item',
            'estimation_items'            => 'est_item',
            'estimation_purchase_items'   => 'est_purchase_item',
            'estimation_chit_items'       => 'est_chit_item',
            'est_old_metal'               => 'est_old_item',
            'issue_receipt_payments'      => 'pay_item',
            'issue_receipt_adv_details'   => 'adv_item',
        ];

        // 1. Handle Loop Sections and Conditionals {{#key}}...{{/key}}
        while (preg_match('/(?:<!--\s*)?\{\{#(\w+)\}\}(?:\s*-->)?(.*?)(?:<!--\s*)?\{\{\/\1\}\}(?:\s*-->)?/s', $html, $match)) {
            $key = $match[1];
            $full_match = $match[0];
            $inner_content = $match[2];

            // Determine the dot-notation alias for this loop (e.g. 'items' → 'item')
            $alias = isset($loop_aliases[$key]) ? $loop_aliases[$key] : $key;

            if (isset($data[$key])) {
                if (is_array($data[$key])) {
                    // Handle Loop
                    $loop_html = '';
                    $i = 1;
                    foreach ($data[$key] as $item) {
                        // Auto-inject sno if missing
                        if (!isset($item['sno'])) {
                            $item['sno'] = $i;
                        }
                        $i++;

                        $row_html = $inner_content;

                        // Pass 1a: dot-notation — {{alias.field}} e.g. {{item.sno}}, {{purchase_item.gross_wt}}
                        foreach ($item as $k => $v) {
                            if (is_string($v) || is_numeric($v)) {
                                $replace_val = (is_numeric($v) && floatval($v) == 0) ? '' : (string)$v;
                                $row_html = str_replace('{{'.$alias.'.'.$k.'}}', $replace_val, $row_html);
                            }
                        }

                        // Pass 1b: plain key — {{sno}}, {{gross_wt}} (backward compatibility inside same loop)
                        foreach ($item as $k => $v) {
                            if (is_string($v) || is_numeric($v)) {
                                $replace_val = (is_numeric($v) && floatval($v) == 0) ? '' : (string)$v;
                                $row_html = str_replace('{{'.$k.'}}', $replace_val, $row_html);
                            }
                        }

                        // Pass 2: apply template functions (money/fixed/upper/lower)
                        $merged_for_func = array_merge($data, $item);
                        $row_html = _tpl_apply_functions($row_html, $merged_for_func);

                        // Pass 3: evaluate math expressions using merged item+global data
                        $merged = array_merge($data, $item);
                        $row_html = preg_replace_callback('/\{\{([a-zA-Z0-9_\s\.+\-*\/()]+)\}\}/', function($m) use ($merged) {
                            $expr = trim($m[1]);
                            if (preg_match('/^[a-zA-Z_][\w\.]*$/', $expr)) return '';
                            if (!preg_match('/[+\-*\/(]/', $expr)) return $m[0];
                            $result = _tpl_eval_expr($expr, $merged);
                            return ($result === null) ? '' : $result;
                        }, $row_html);

                        // Pass 4: replace any remaining global scalar tags (e.g. {{company_name}} in a loop cell)
                        foreach ($data as $gk => $gv) {
                            if ((is_string($gv) || is_numeric($gv)) && !is_array($gv)) {
                                $row_html = str_replace('{{'.$gk.'}}', $gv, $row_html);
                            }
                        }

                        // Pass 5: handle nested boolean conditionals inside loop items
                        // e.g. {{#has_charges}}...{{/has_charges}} where has_charges is a per-item boolean
                        $merged_cond = array_merge($data, $item);
                        while (preg_match('/(?:<!--\s*)?\{\{#(\w+)\}\}(?:\s*-->)?(.*?)(?:<!--\s*)?\{\{\/\1\}\}(?:\s*-->)?/s', $row_html, $cond_match)) {
                            $cond_key = $cond_match[1];
                            $cond_full = $cond_match[0];
                            $cond_inner = $cond_match[2];
                            if (isset($merged_cond[$cond_key])) {
                                if ($merged_cond[$cond_key] === true || $merged_cond[$cond_key] === 1 || $merged_cond[$cond_key] === '1' || (is_string($merged_cond[$cond_key]) && $merged_cond[$cond_key] !== '' && $merged_cond[$cond_key] !== '0')) {
                                    $row_html = str_replace($cond_full, $cond_inner, $row_html);
                                } else {
                                    $row_html = str_replace($cond_full, '', $row_html);
                                }
                            } else {
                                $row_html = str_replace($cond_full, '', $row_html);
                            }
                        }

                        // Wipe unresolvable leftovers
                        $row_html = preg_replace('/\{\{[a-zA-Z0-9_\s\.+\-*\/()]+\}\}/', '', $row_html);
                        $loop_html .= $row_html;
                    }
                    $html = str_replace($full_match, $loop_html, $html);

                } elseif ($data[$key] === true || $data[$key] === 1 || $data[$key] === '1' || (is_string($data[$key]) && $data[$key] !== '' && $data[$key] !== '0')) {
                    // Handle Boolean TRUE / non-empty string (Conditional)
                    $html = str_replace($full_match, $inner_content, $html);
                } else {
                    // Handle Boolean FALSE
                    $html = str_replace($full_match, '', $html);
                }
            } else {
                // Key not set - treat as FALSE
                $html = str_replace($full_match, '', $html);
            }
        }
        
        // 1.5 Handle Template Functions: money({{var}}), fixed({{var}}, N), upper({{var}}), lower({{var}})
        $html = _tpl_apply_functions($html, $data);

        // 2. Handle Simple Placeholders {{key}}
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $replace_val = (is_numeric($value) && floatval($value) == 0) ? '' : (string)$value;
                $html = str_replace('{{'.$key.'}}', $replace_val, $html);
            }
        }

        // 2b. Evaluate math expressions including parentheses:
        //     {{net_wt*8}}, {{(net_wt*2)/rate}}, {{grand_total - sub_total}}
        $html = preg_replace_callback('/\{\{([a-zA-Z0-9_\s\.+\-*\/()]+)\}\}/', function($m) use ($data) {
            $expr = trim($m[1]);
            if (preg_match('/^[a-zA-Z_]\w*$/', $expr)) return $m[0];
            if (!preg_match('/[+\-*\/(]/', $expr)) return $m[0];
            $result = _tpl_eval_expr($expr, $data);
            return ($result === null) ? $m[0] : $result;
        }, $html);
        
        // 3. Clean up any remaining zero values (but preserve lone currency symbols for rows with values)
        $html = preg_replace('/>\s*₹?\s*0+(\.0+)?\s*</u', '><', $html);
        $html = preg_replace('/>\s*-?\s*</u', '><', $html);
        
        // 4. Remove unreplaced {{placeholder}} patterns
        $html = preg_replace('/\{\{[a-zA-Z0-9_]+\}\}/', '', $html);
        // Also clean any expression tokens that still couldn't be resolved (including parens)
        $html = preg_replace('/\{\{[a-zA-Z0-9_\s\.+\-*\/()]+\}\}/', '', $html);
        
        // 4b. Fix base64 image sources: if an <img> src contains raw base64 data (no data: prefix), add it
        $html = preg_replace_callback('/<img([^>]*)\bsrc=["\']([^"\']+)["\']/i', function($m) {
            $src = trim($m[2]);
            // If it looks like raw base64 (long alphanumeric string, no http/data: prefix)
            if (!empty($src) && strpos($src, 'http') !== 0 && strpos($src, 'data:') !== 0 && strlen($src) > 100) {
                return '<img' . $m[1] . 'src="data:image/png;base64,' . $src . '"';
            }
            return $m[0];
        }, $html);
        
        // 4c. Remove <img> tags with empty or missing src attributes
        $html = preg_replace('/<img[^>]*\bsrc=["\'][\s]*["\'][^>]*\/?>/i', '', $html);
        
        // 5. Auto-hide empty rows: remove <tr> rows where value cells are empty
        // Only target INNERMOST <tr> (i.e., those that do NOT contain nested <tr> or <table>)
        $html = preg_replace_callback('/<tr[^>]*>(?:(?!<tr|<\/tr|<table|<\/table).)*<\/tr>/si', function($match) {
            $row = $match[0];
            // Skip header rows
            if (stripos($row, '<th') !== false) return $row;
            preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $row, $tds);
            $td_count = count($tds[1]);
            if ($td_count < 1) return $row;
            
            // Single-cell rows: hide if empty, just a trailing colon label, or label ending with currency symbol
            if ($td_count == 1) {
                $text = trim(strip_tags(html_entity_decode($tds[1][0], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                if ($text === '') return '';
                if (preg_match('/:\s*$/u', $text)) return '';
                // Hide if text ends with : followed by a currency symbol (e.g., "Cash : ₹")
                if (preg_match('/:\s*[₹$€£¥]\s*$/u', $text)) return '';
                return $row;
            }
            
            // Multi-cell rows: check if ALL value cells (index 1+) are effectively empty
            $has_value = false;
            for ($i = 1; $i < $td_count; $i++) {
                $cell_text = trim(strip_tags(html_entity_decode($tds[1][$i], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                // Remove all non-alphanumeric characters (covers ₹, $, -, :, /, spaces, etc.)
                $clean = preg_replace('/[^a-zA-Z0-9.]/u', '', $cell_text);
                // Also remove lone "Gm" unit text
                $clean = preg_replace('/^[Gg][Mm]$/u', '', $clean);
                $clean = trim($clean);
                if ($clean !== '') { $has_value = true; break; }
            }
            return $has_value ? $row : '';
        }, $html);
        
        // 6. Auto-hide empty div rows (flex-based payment lines like "Label : ₹" with no value)
        $html = preg_replace_callback('/<div[^>]*style="[^"]*display\s*:\s*flex[^"]*"[^>]*>.*?<\/div>/si', function($match) {
            $div = $match[0];
            $text = trim(strip_tags(html_entity_decode($div, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            // If text has a currency symbol but no numbers, it's an empty payment row
            if (preg_match('/[₹$€£¥]/u', $text) && !preg_match('/[0-9]/', $text)) {
                return '';
            }
            return $div;
        }, $html);
        
        return $html;
    }

    // ═══════════════════════════════════════════════════════════
    // V2 DESIGNER METHODS
    // ═══════════════════════════════════════════════════════════

    /**
     * Get MC/VA configuration for a branch + document type.
     * Falls back to global config (id_branch=NULL) if no branch-specific one exists.
     */
    public function get_mc_va_config($branch_id = null, $doc_type = null) {
        if ($doc_type) {
            $this->db->where('document_type', $doc_type);
        }
        
        if ($branch_id) {
            // Try branch-specific first
            $this->db->where('id_branch', $branch_id);
            $result = $this->db->get('print_template_mc_va_config')->row_array();
            if ($result) return $result;
            
            // Fallback to global
            $this->db->where('document_type', $doc_type);
            $this->db->where('id_branch IS NULL');
        } else {
            $this->db->where('id_branch IS NULL');
        }
        
        return $this->db->get('print_template_mc_va_config')->row_array();
    }

    /**
     * Get all MC/VA configs (for listing).
     */
    public function get_all_mc_va_configs($branch_id = null) {
        if ($branch_id) {
            $this->db->where("(id_branch = " . $this->db->escape($branch_id) . " OR id_branch IS NULL)", NULL, FALSE);
        }
        $this->db->order_by('document_type', 'ASC');
        return $this->db->get('print_template_mc_va_config')->result_array();
    }

    /**
     * Save (upsert) MC/VA configuration.
     */
    public function save_mc_va_config($data) {
        $branch_id = isset($data['id_branch']) ? $data['id_branch'] : null;
        $doc_type = $data['document_type'];
        
        // Check if config exists
        if ($branch_id) {
            $this->db->where('id_branch', $branch_id);
        } else {
            $this->db->where('id_branch IS NULL');
        }
        $this->db->where('document_type', $doc_type);
        $existing = $this->db->get('print_template_mc_va_config')->row_array();
        
        if ($existing) {
            $this->db->where('id', $existing['id']);
            return $this->db->update('print_template_mc_va_config', $data);
        } else {
            return $this->db->insert('print_template_mc_va_config', $data);
        }
    }

    /**
     * Get default template blocks JSON for a category.
     */
    public function get_default_templates($category) {
        $this->db->where('template_category', $category);
        $this->db->order_by('is_system', 'DESC');
        return $this->db->get('print_template_defaults')->result_array();
    }

    /**
     * Get a single default template by ID.
     */
    public function get_default_template_by_id($id) {
        return $this->db->where('id', $id)->get('print_template_defaults')->row_array();
    }

    /**
     * Enhanced V2 placeholder registry with type, loop, and grouping metadata.
     * Returns all available fields organized by group for the Field Picker UI.
     */
    public function get_placeholders_v2($category) {
        // Start with DB-stored placeholders
        $db_placeholders = $this->db->where('category', $category)
                        ->order_by('placeholder_group', 'ASC')
                        ->order_by('display_order', 'ASC')
                        ->get('print_template_placeholders')
                        ->result_array();

        // Comprehensive dynamic fields for all categories
        $dynamic = $this->_get_dynamic_fields_v2($category);

        // Merge: DB takes precedence
        $existing_keys = array_column($db_placeholders, 'placeholder_key');
        foreach ($dynamic as $d) {
            if (!in_array($d['placeholder_key'], $existing_keys)) {
                $db_placeholders[] = $d;
            }
        }

        // Organize by group
        $grouped = [];
        foreach ($db_placeholders as $p) {
            $group = $p['placeholder_group'] ?? 'General';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = [
                'key'        => $p['placeholder_key'],
                'label'      => $p['placeholder_label'],
                'group'      => $group,
                'sample'     => $p['sample_value'] ?? '',
                'field_type' => $p['field_type'] ?? 'text',
                'is_loop'    => $p['is_loop_field'] ?? 0,
                'parent_loop'=> $p['parent_loop'] ?? null
            ];
        }

        return $grouped;
    }

    /**
     * Get comprehensive dynamic fields for V2 designer, covering all categories.
     */
    private function _get_dynamic_fields_v2($category) {
        $fields = [];
        $sample = $this->get_sample_data($category);

        // Loop alias → group label mapping (for loop field registration)
        $loop_groups = [
            'items'                     => 'Sales Items',
            'purchase_items'            => 'Old/Purchase Metal Items',
            'return_items'              => 'Sales Return Items',
            'order_advance_entries'     => 'Order Advance Items',
            'repair_items'              => 'Repair Items',
            'chit_items'                => 'Chit Items',
            'credit_collection_history' => 'Credit History Items',
            'advance_details'           => 'Advance Details',
            'transfer_items'            => 'Transfer/Karigar Items',
            'old_metal_items'           => 'Old Metal Items',
            'chit_general_items'        => 'Chit Adjustment Items',
            'chit_pre_close_items'      => 'Chit Pre-Close Items',
            'receipt_adjustment_items'  => 'Receipt Adjustment Items',
            'repair_order_items'        => 'Repair Order Items',
            'order_delivery_items'      => 'Order Delivery Items',
            'sales_return_items'        => 'Sales Return Items (V2)',
            'tax_detail_items'          => 'Tax Detail Breakdown',
            'payment_breakdown'         => 'Payment Details',
            'payment_methods'           => 'Payment Methods (per-transaction)',
        ];

        // 1. Register all scalar (non-array, non-bool) fields
        foreach ($sample as $key => $value) {
            if (is_array($value) || is_bool($value)) continue;
            $fields[] = [
                'placeholder_key'   => $key,
                'placeholder_label' => self::_auto_label($key),
                'placeholder_group' => self::_auto_group($key),
                'sample_value'      => (string)$value,
                'category'          => $category,
                'field_type'        => 'text',
                'is_loop_field'     => 0,
                'parent_loop'       => null
            ];
        }

        // 2. Register loop (array) fields by introspecting first row of each loop array
        foreach ($loop_groups as $loop_key => $group_label) {
            if (!isset($sample[$loop_key]) || !is_array($sample[$loop_key]) || empty($sample[$loop_key])) continue;
            $first_row = $sample[$loop_key][0];
            foreach ($first_row as $col_key => $col_val) {
                if (is_array($col_val)) continue;
                $fields[] = [
                    'placeholder_key'   => $col_key,
                    'placeholder_label' => self::_auto_label($col_key),
                    'placeholder_group' => $group_label,
                    'sample_value'      => (string)$col_val,
                    'category'          => $category,
                    'field_type'        => 'text',
                    'is_loop_field'     => 1,
                    'parent_loop'       => $loop_key
                ];
            }
        }

        // 3. Merge extra placeholders from category-specific helpers
        //    (e.g. billing helper registers stone _sub_rows fields that auto-detection skips)
        if (function_exists('tpl_get_extra_placeholders')) {
            $extras = tpl_get_extra_placeholders($category);
            foreach ($extras as $ex) {
                $fields[] = $ex;
            }
        }

        // ── Estimation-specific fields (category 16) ──
        if ($category == 16) {
            // Estimation header scalars
            foreach ([
                ['key'=>'esti_no',         'label'=>'Estimation No',       'group'=>'Estimation Details'],
                ['key'=>'est_date',        'label'=>'Estimation Date',     'group'=>'Estimation Details'],
                ['key'=>'est_time',        'label'=>'Estimation Time',     'group'=>'Estimation Details'],
                ['key'=>'est_employee',    'label'=>'Estimation Employee', 'group'=>'Estimation Details'],
                ['key'=>'emp_code',        'label'=>'Employee Code',       'group'=>'Estimation Details'],
                ['key'=>'branch_name',     'label'=>'Branch Name',         'group'=>'Estimation Details'],
                ['key'=>'village_name',    'label'=>'Village Name',        'group'=>'Estimation Details'],
                ['key'=>'gold_rate_22ct',  'label'=>'Gold Rate 22ct',      'group'=>'Estimation Rates'],
                ['key'=>'gold_rate_18ct',  'label'=>'Gold Rate 18ct',      'group'=>'Estimation Rates'],
                ['key'=>'gold_rate_14ct',  'label'=>'Gold Rate 14ct',      'group'=>'Estimation Rates'],
                ['key'=>'gold_rate_9ct',   'label'=>'Gold Rate 9ct',       'group'=>'Estimation Rates'],
                ['key'=>'silver_rate',     'label'=>'Silver Rate',          'group'=>'Estimation Rates'],
                ['key'=>'total_pieces',    'label'=>'Total Pieces',         'group'=>'Estimation Summary'],
                ['key'=>'total_gross_wt',  'label'=>'Total Gross Wt',      'group'=>'Estimation Summary'],
                ['key'=>'total_net_wt',    'label'=>'Total Net Wt',        'group'=>'Estimation Summary'],
                ['key'=>'sub_total',       'label'=>'Sub Total',            'group'=>'Estimation Summary'],
                ['key'=>'cgst_amount',     'label'=>'CGST Amount',          'group'=>'Estimation Summary'],
                ['key'=>'sgst_amount',     'label'=>'SGST Amount',          'group'=>'Estimation Summary'],
                ['key'=>'igst_amount',     'label'=>'IGST Amount',          'group'=>'Estimation Summary'],
                ['key'=>'total_tax',       'label'=>'Total Tax',            'group'=>'Estimation Summary'],
                ['key'=>'grand_total',     'label'=>'Grand Total',          'group'=>'Estimation Summary'],
                ['key'=>'amount_in_words', 'label'=>'Amount in Words',      'group'=>'Estimation Summary'],
                ['key'=>'discount',        'label'=>'Discount',             'group'=>'Estimation Summary'],
                ['key'=>'total_old_metal', 'label'=>'Total Old Metal Amt',  'group'=>'Estimation Old Metal'],
                ['key'=>'total_chit_amount','label'=>'Total Chit Amount',   'group'=>'Estimation Chit'],
                ['key'=>'qr_code_image',   'label'=>'QR Code Image',        'group'=>'Estimation Misc'],
            ] as $f) {
                $fields[] = ['placeholder_key'=>$f['key'],'placeholder_label'=>$f['label'],'placeholder_group'=>$f['group'],'sample_value'=>'','category'=>$category,'field_type'=>'text','is_loop_field'=>0,'parent_loop'=>null];
            }

            // Estimation items loop ({{#estimation_items}}...{{/estimation_items}})
            $est_item_cols = [
                ['key'=>'sno','label'=>'S.No'],['key'=>'product_name','label'=>'Product Name'],
                ['key'=>'sub_design_name','label'=>'Sub Design'],['key'=>'tag_code','label'=>'Tag Code'],
                ['key'=>'purity','label'=>'Purity'],['key'=>'qty','label'=>'Qty/PCS'],
                ['key'=>'gross_wt','label'=>'Gross Wt'],['key'=>'net_wt','label'=>'Net Wt'],
                ['key'=>'rate','label'=>'Rate (per gram)'],['key'=>'va_percent','label'=>'VA %'],
                ['key'=>'mc','label'=>'Making Charge'],['key'=>'amount','label'=>'Amount'],
                ['key'=>'stone_amount','label'=>'Stone Amount'],['key'=>'charge_amount','label'=>'Charge Amount'],
                ['key'=>'taxable_amount','label'=>'Taxable Amount'],['key'=>'tax_amount','label'=>'Tax Amount'],
                ['key'=>'metal_name','label'=>'Metal Name'],['key'=>'wastage_percent','label'=>'Wastage %'],
            ];
            foreach ($loop_helper($est_item_cols, 'Estimation Items', 'estimation_items', $category) as $f) $fields[] = $f;

            // Estimation old metal / purchase items loop
            $est_old_cols = [
                ['key'=>'sno','label'=>'S.No'],['key'=>'metal_name','label'=>'Metal Name'],
                ['key'=>'old_metal_type','label'=>'Old Metal Type'],['key'=>'piece','label'=>'Pieces'],
                ['key'=>'gross_wt','label'=>'Gross Wt'],['key'=>'stone_wt','label'=>'Stone Wt'],
                ['key'=>'net_wt','label'=>'Net Wt'],['key'=>'purity','label'=>'Purity'],
                ['key'=>'wastage_percent','label'=>'Wastage %'],['key'=>'wastage_wt','label'=>'Wastage Wt'],
                ['key'=>'rate_per_gram','label'=>'Rate / Gram'],['key'=>'amount','label'=>'Amount'],
                ['key'=>'purpose','label'=>'Purpose'],
            ];
            foreach ($loop_helper($est_old_cols, 'Estimation Old Metal Items', 'estimation_purchase_items', $category) as $f) $fields[] = $f;

            // Estimation chit items loop
            $est_chit_cols = [
                ['key'=>'sno','label'=>'S.No'],['key'=>'scheme_acc_number','label'=>'Account No'],
                ['key'=>'closing_weight','label'=>'Closing Weight'],['key'=>'utl_amount','label'=>'Utilized Amount'],
                ['key'=>'rate_per_gram','label'=>'Rate Per Gram'],
            ];
            foreach ($loop_helper($est_chit_cols, 'Estimation Chit Items', 'estimation_chit_items', $category) as $f) $fields[] = $f;
        }

        // ─ Issue Receipt-specific fields (category 17) ─
        if ($category == 17) {
            $fields = array_merge($fields, [
                ['key'=>'customer_name',      'label'=>'Customer Name',       'group'=>'Customer'],
                ['key'=>'customer_mobile',     'label'=>'Customer Mobile',     'group'=>'Customer'],
                ['key'=>'customer_address1',   'label'=>'Address Line 1',     'group'=>'Customer'],
                ['key'=>'customer_address2',   'label'=>'Address Line 2',     'group'=>'Customer'],
                ['key'=>'customer_village',    'label'=>'Village',             'group'=>'Customer'],
                ['key'=>'customer_city',       'label'=>'City',               'group'=>'Customer'],
                ['key'=>'customer_pincode',    'label'=>'Pincode',            'group'=>'Customer'],
                ['key'=>'customer_state',      'label'=>'State',              'group'=>'Customer'],
                ['key'=>'customer_pan',        'label'=>'PAN No',             'group'=>'Customer'],
                ['key'=>'customer_gstin',      'label'=>'GST No',             'group'=>'Customer'],
                ['key'=>'bill_no',             'label'=>'Bill No',            'group'=>'Receipt Details'],
                ['key'=>'date_add',            'label'=>'Date',               'group'=>'Receipt Details'],
                ['key'=>'time_add',            'label'=>'Time',               'group'=>'Receipt Details'],
                ['key'=>'receipt_type_label',  'label'=>'Receipt Type Label', 'group'=>'Receipt Details'],
                ['key'=>'invoice_label',       'label'=>'Invoice Label',      'group'=>'Receipt Details'],
                ['key'=>'state_code',          'label'=>'State Code',         'group'=>'Receipt Details'],
                ['key'=>'description_text',    'label'=>'Description Text',   'group'=>'Receipt Details'],
                ['key'=>'amount',              'label'=>'Amount',             'group'=>'Receipt Details'],
                ['key'=>'amount_in_words',     'label'=>'Amount in Words',    'group'=>'Receipt Details'],
                ['key'=>'narration',           'label'=>'Narration/Remarks',  'group'=>'Receipt Details'],
                ['key'=>'emp_name',            'label'=>'Employee Name',      'group'=>'Receipt Details'],
                ['key'=>'emp_code',            'label'=>'Employee Code',      'group'=>'Receipt Details'],
                ['key'=>'grand_total',         'label'=>'Grand Total',        'group'=>'Payment'],
                ['key'=>'adjusted_amount',     'label'=>'Advance Adj Amount', 'group'=>'Payment'],
                ['key'=>'cash_amount',         'label'=>'Cash Amount',        'group'=>'Payment'],
                ['key'=>'card_amount',         'label'=>'Card Amount',        'group'=>'Payment'],
                ['key'=>'chq_amount',          'label'=>'Cheque Amount',      'group'=>'Payment'],
                ['key'=>'upi_amount',          'label'=>'UPI Amount',         'group'=>'Payment'],
                ['key'=>'rtgs_amount',         'label'=>'RTGS Amount',        'group'=>'Payment'],
                ['key'=>'ref_bill_no',         'label'=>'Ref Bill No',        'group'=>'Payment'],
            ]);

            // Payment loop fields
            $pay_cols = [
                ['key'=>'pay_label', 'label'=>'Payment Mode'],
                ['key'=>'pay_details', 'label'=>'Payment Details'],
                ['key'=>'pay_amount', 'label'=>'Payment Amount'],
            ];
            foreach ($loop_helper($pay_cols, 'Payment Lines', 'issue_receipt_payments', $category) as $f) $fields[] = $f;

            // Advance details loop fields
            $adv_cols = [
                ['key'=>'adv_bill_no', 'label'=>'Receipt No'],
                ['key'=>'adv_bill_date', 'label'=>'Receipt Date'],
                ['key'=>'adv_receipt_amt', 'label'=>'Receipt Amount'],
                ['key'=>'adv_utilized', 'label'=>'Utilized Amount'],
                ['key'=>'adv_refund', 'label'=>'Refund Amount'],
                ['key'=>'adv_balance', 'label'=>'Balance Amount'],
            ];
            foreach ($loop_helper($adv_cols, 'Advance Details', 'issue_receipt_adv_details', $category) as $f) $fields[] = $f;
        }

        // ── Estimation-specific fields (category 16) ──
        if ($category == 16) {
            // Estimation header scalars
            foreach ([
                ['key'=>'esti_no',         'label'=>'Estimation No',       'group'=>'Estimation Details'],
                ['key'=>'est_date',        'label'=>'Estimation Date',     'group'=>'Estimation Details'],
                ['key'=>'est_time',        'label'=>'Estimation Time',     'group'=>'Estimation Details'],
                ['key'=>'est_employee',    'label'=>'Estimation Employee', 'group'=>'Estimation Details'],
                ['key'=>'emp_code',        'label'=>'Employee Code',       'group'=>'Estimation Details'],
                ['key'=>'branch_name',     'label'=>'Branch Name',         'group'=>'Estimation Details'],
                ['key'=>'village_name',    'label'=>'Village Name',        'group'=>'Estimation Details'],
                ['key'=>'gold_rate_22ct',  'label'=>'Gold Rate 22ct',      'group'=>'Estimation Rates'],
                ['key'=>'gold_rate_18ct',  'label'=>'Gold Rate 18ct',      'group'=>'Estimation Rates'],
                ['key'=>'gold_rate_14ct',  'label'=>'Gold Rate 14ct',      'group'=>'Estimation Rates'],
                ['key'=>'gold_rate_9ct',   'label'=>'Gold Rate 9ct',       'group'=>'Estimation Rates'],
                ['key'=>'silver_rate',     'label'=>'Silver Rate',          'group'=>'Estimation Rates'],
                ['key'=>'total_pieces',    'label'=>'Total Pieces',         'group'=>'Estimation Summary'],
                ['key'=>'total_gross_wt',  'label'=>'Total Gross Wt',      'group'=>'Estimation Summary'],
                ['key'=>'total_net_wt',    'label'=>'Total Net Wt',        'group'=>'Estimation Summary'],
                ['key'=>'sub_total',       'label'=>'Sub Total',            'group'=>'Estimation Summary'],
                ['key'=>'cgst_amount',     'label'=>'CGST Amount',          'group'=>'Estimation Summary'],
                ['key'=>'sgst_amount',     'label'=>'SGST Amount',          'group'=>'Estimation Summary'],
                ['key'=>'igst_amount',     'label'=>'IGST Amount',          'group'=>'Estimation Summary'],
                ['key'=>'total_tax',       'label'=>'Total Tax',            'group'=>'Estimation Summary'],
                ['key'=>'grand_total',     'label'=>'Grand Total',          'group'=>'Estimation Summary'],
                ['key'=>'amount_in_words', 'label'=>'Amount in Words',      'group'=>'Estimation Summary'],
                ['key'=>'discount',        'label'=>'Discount',             'group'=>'Estimation Summary'],
                ['key'=>'total_old_metal', 'label'=>'Total Old Metal Amt',  'group'=>'Estimation Old Metal'],
                ['key'=>'total_chit_amount','label'=>'Total Chit Amount',   'group'=>'Estimation Chit'],
                ['key'=>'qr_code_image',   'label'=>'QR Code Image',        'group'=>'Estimation Misc'],
            ] as $f) {
                $fields[] = ['placeholder_key'=>$f['key'],'placeholder_label'=>$f['label'],'placeholder_group'=>$f['group'],'sample_value'=>'','category'=>$category,'field_type'=>'text','is_loop_field'=>0,'parent_loop'=>null];
            }

            // Estimation items loop ({{#estimation_items}}...{{/estimation_items}})
            $est_item_cols = [
                ['key'=>'sno','label'=>'S.No'],['key'=>'product_name','label'=>'Product Name'],
                ['key'=>'sub_design_name','label'=>'Sub Design'],['key'=>'tag_code','label'=>'Tag Code'],
                ['key'=>'purity','label'=>'Purity'],['key'=>'qty','label'=>'Qty/PCS'],
                ['key'=>'gross_wt','label'=>'Gross Wt'],['key'=>'net_wt','label'=>'Net Wt'],
                ['key'=>'rate','label'=>'Rate (per gram)'],['key'=>'va_percent','label'=>'VA %'],
                ['key'=>'mc','label'=>'Making Charge'],['key'=>'amount','label'=>'Amount'],
                ['key'=>'stone_amount','label'=>'Stone Amount'],['key'=>'charge_amount','label'=>'Charge Amount'],
                ['key'=>'taxable_amount','label'=>'Taxable Amount'],['key'=>'tax_amount','label'=>'Tax Amount'],
                ['key'=>'metal_name','label'=>'Metal Name'],['key'=>'wastage_percent','label'=>'Wastage %'],
            ];
            foreach ($loop_helper($est_item_cols, 'Estimation Items', 'estimation_items', $category) as $f) $fields[] = $f;

            // Estimation old metal / purchase items loop
            $est_old_cols = [
                ['key'=>'sno','label'=>'S.No'],['key'=>'metal_name','label'=>'Metal Name'],
                ['key'=>'old_metal_type','label'=>'Old Metal Type'],['key'=>'piece','label'=>'Pieces'],
                ['key'=>'gross_wt','label'=>'Gross Wt'],['key'=>'stone_wt','label'=>'Stone Wt'],
                ['key'=>'net_wt','label'=>'Net Wt'],['key'=>'purity','label'=>'Purity'],
                ['key'=>'wastage_percent','label'=>'Wastage %'],['key'=>'wastage_wt','label'=>'Wastage Wt'],
                ['key'=>'rate_per_gram','label'=>'Rate / Gram'],['key'=>'amount','label'=>'Amount'],
                ['key'=>'purpose','label'=>'Purpose'],
            ];
            foreach ($loop_helper($est_old_cols, 'Estimation Old Metal Items', 'estimation_purchase_items', $category) as $f) $fields[] = $f;

            // Estimation chit items loop
            $est_chit_cols = [
                ['key'=>'sno','label'=>'S.No'],['key'=>'scheme_acc_number','label'=>'Account No'],
                ['key'=>'closing_weight','label'=>'Closing Weight'],['key'=>'utl_amount','label'=>'Utilized Amount'],
                ['key'=>'rate_per_gram','label'=>'Rate Per Gram'],
            ];
            foreach ($loop_helper($est_chit_cols, 'Estimation Chit Items', 'estimation_chit_items', $category) as $f) $fields[] = $f;
        }

        // ─ Issue Receipt-specific fields (category 17) ─
        if ($category == 17) {
            $fields = array_merge($fields, [
                ['key'=>'customer_name',      'label'=>'Customer Name',       'group'=>'Customer'],
                ['key'=>'customer_mobile',     'label'=>'Customer Mobile',     'group'=>'Customer'],
                ['key'=>'customer_address1',   'label'=>'Address Line 1',     'group'=>'Customer'],
                ['key'=>'customer_address2',   'label'=>'Address Line 2',     'group'=>'Customer'],
                ['key'=>'customer_village',    'label'=>'Village',             'group'=>'Customer'],
                ['key'=>'customer_city',       'label'=>'City',               'group'=>'Customer'],
                ['key'=>'customer_pincode',    'label'=>'Pincode',            'group'=>'Customer'],
                ['key'=>'customer_state',      'label'=>'State',              'group'=>'Customer'],
                ['key'=>'customer_pan',        'label'=>'PAN No',             'group'=>'Customer'],
                ['key'=>'customer_gstin',      'label'=>'GST No',             'group'=>'Customer'],
                ['key'=>'bill_no',             'label'=>'Bill No',            'group'=>'Receipt Details'],
                ['key'=>'date_add',            'label'=>'Date',               'group'=>'Receipt Details'],
                ['key'=>'time_add',            'label'=>'Time',               'group'=>'Receipt Details'],
                ['key'=>'receipt_type_label',  'label'=>'Receipt Type Label', 'group'=>'Receipt Details'],
                ['key'=>'invoice_label',       'label'=>'Invoice Label',      'group'=>'Receipt Details'],
                ['key'=>'state_code',          'label'=>'State Code',         'group'=>'Receipt Details'],
                ['key'=>'description_text',    'label'=>'Description Text',   'group'=>'Receipt Details'],
                ['key'=>'amount',              'label'=>'Amount',             'group'=>'Receipt Details'],
                ['key'=>'amount_in_words',     'label'=>'Amount in Words',    'group'=>'Receipt Details'],
                ['key'=>'narration',           'label'=>'Narration/Remarks',  'group'=>'Receipt Details'],
                ['key'=>'emp_name',            'label'=>'Employee Name',      'group'=>'Receipt Details'],
                ['key'=>'emp_code',            'label'=>'Employee Code',      'group'=>'Receipt Details'],
                ['key'=>'grand_total',         'label'=>'Grand Total',        'group'=>'Payment'],
                ['key'=>'adjusted_amount',     'label'=>'Advance Adj Amount', 'group'=>'Payment'],
                ['key'=>'cash_amount',         'label'=>'Cash Amount',        'group'=>'Payment'],
                ['key'=>'card_amount',         'label'=>'Card Amount',        'group'=>'Payment'],
                ['key'=>'chq_amount',          'label'=>'Cheque Amount',      'group'=>'Payment'],
                ['key'=>'upi_amount',          'label'=>'UPI Amount',         'group'=>'Payment'],
                ['key'=>'rtgs_amount',         'label'=>'RTGS Amount',        'group'=>'Payment'],
                ['key'=>'ref_bill_no',         'label'=>'Ref Bill No',        'group'=>'Payment'],
            ]);

            // Payment loop fields
            $pay_cols = [
                ['key'=>'pay_label', 'label'=>'Payment Mode'],
                ['key'=>'pay_details', 'label'=>'Payment Details'],
                ['key'=>'pay_amount', 'label'=>'Payment Amount'],
            ];
            foreach ($loop_helper($pay_cols, 'Payment Lines', 'issue_receipt_payments', $category) as $f) $fields[] = $f;

            // Advance details loop fields
            $adv_cols = [
                ['key'=>'adv_bill_no', 'label'=>'Receipt No'],
                ['key'=>'adv_bill_date', 'label'=>'Receipt Date'],
                ['key'=>'adv_receipt_amt', 'label'=>'Receipt Amount'],
                ['key'=>'adv_utilized', 'label'=>'Utilized Amount'],
                ['key'=>'adv_refund', 'label'=>'Refund Amount'],
                ['key'=>'adv_balance', 'label'=>'Balance Amount'],
            ];
            foreach ($loop_helper($adv_cols, 'Advance Details', 'issue_receipt_adv_details', $category) as $f) $fields[] = $f;
        }

        return $fields;
    }

    /**
     * Update paper/margin settings on a template.
     */
    public function update_paper_settings($id, $settings) {
        $allowed = ['paper_size', 'paper_width', 'paper_height', 'page_orientation', 
                     'margin_top', 'margin_right', 'margin_bottom', 'margin_left'];
        $update = [];
        foreach ($allowed as $key) {
            if (isset($settings[$key])) {
                $update[$key] = $settings[$key];
            }
        }
        if (empty($update)) return false;
        
        $this->db->where('id_template', $id);
        return $this->db->update('print_templates', $update);
    }

    /**
     * Render template with field names shown as visual chips (for "Show Field Names" preview mode).
     * e.g. {{customer_name}} → <span class="field-chip">Customer Name</span>
     */
    public function render_preview_template($html) {
        // Replace {{key}} with styled chips — labels generated dynamically from key
        $html = preg_replace_callback('/\{\{(\w+)\}\}/', function($match) {
            $key = $match[1];
            $label = self::_auto_label($key);
            return '<span style="background:#dbeafe;color:#1e40af;padding:1px 6px;border-radius:3px;font-size:11px;border:1px dashed #93c5fd;white-space:nowrap;">' . htmlspecialchars($label) . '</span>';
        }, $html);

        // Remove Mustache conditional blocks but keep content
        $html = preg_replace('/\{\{#\w+\}\}/', '', $html);
        $html = preg_replace('/\{\{\/\w+\}\}/', '', $html);

        return $html;
    }

    /**
     * Evaluate a formula expression for a calculated column.
     * Supports: field1 * field2, field1 + field2, field1 - field2, field1 / field2
     * @param string $formula e.g. "mc*rate" or "net_wt*rate"
     * @param array $item_data Row data with field values
     * @return float|string
     */
    public function evaluate_formula($formula, $item_data) {
        // Parse the formula: "field1<op>field2"
        if (preg_match('/^(\w+)\s*([+\-*\/])\s*(\w+)$/', $formula, $matches)) {
            $f1 = isset($item_data[$matches[1]]) ? floatval(str_replace(',', '', $item_data[$matches[1]])) : 0;
            $op = $matches[2];
            $f2 = isset($item_data[$matches[3]]) ? floatval(str_replace(',', '', $item_data[$matches[3]])) : 0;

            switch($op) {
                case '+': return number_format($f1 + $f2, 2);
                case '-': return number_format($f1 - $f2, 2);
                case '*': return number_format($f1 * $f2, 2);
                case '/': return ($f2 != 0) ? number_format($f1 / $f2, 2) : '0.00';
                default: return '0.00';
            }
        }
        return '0.00';
    }
    // ═══ COMPUTED VARIABLES ═══════════════════════════════════════════════

    /**
     * Get all computed variables for a template
     */
    public function get_computed_vars($template_id) {
        return $this->db->where('id_template', $template_id)
                        ->order_by('id', 'ASC')
                        ->get('print_template_computed_vars')
                        ->result_array();
    }

    /**
     * Save (insert or update) a computed variable
     */
    public function save_computed_var($data) {
        $id_template = $data['id_template'];
        $var_name    = $data['var_name'];

        // Check if exists
        $existing = $this->db->where('id_template', $id_template)
                             ->where('var_name', $var_name)
                             ->get('print_template_computed_vars')
                             ->row_array();

        $record = [
            'id_template' => $id_template,
            'var_name'    => $var_name,
            'var_label'   => $data['var_label'] ?? $var_name,
            'formula'     => $data['formula'],
        ];

        if ($existing) {
            $this->db->where('id', $existing['id'])->update('print_template_computed_vars', $record);
            return $existing['id'];
        } else {
            $this->db->insert('print_template_computed_vars', $record);
            return $this->db->insert_id();
        }
    }

    /**
     * Delete a computed variable by ID
     */
    public function delete_computed_var($id, $template_id) {
        return $this->db->where('id', $id)
                        ->where('id_template', $template_id)
                        ->delete('print_template_computed_vars');
    }

}  // end class print_template_model

// ═══════════════════════════════════════════════════════════════════════════
// TEMPLATE EXPRESSION EVALUATOR — recursive descent parser
// Supports: variable_name, literals, +, -, *, /, and grouped (expr) sub-exprs
// No eval() used. Full operator precedence.
// ═══════════════════════════════════════════════════════════════════════════

// ─────────────────────────────────────────────────────────────────────────────
// TEMPLATE FUNCTION PROCESSOR
// Handles: money({{var}}), fixed({{var}}, N), upper({{var}}), lower({{var}})
// ─────────────────────────────────────────────────────────────────────────────

if (!function_exists('_tpl_apply_functions')) {
    /**
     * Resolve template function calls in $html using $data values.
     *
     * Supported functions:
     *   money({{field}})              → Indian number_format with 2 decimal places  e.g. 1,23,456.75
     *   fixed({{field}}, 'N')         → number_format with N decimal places          e.g. 3.140
     *   upper({{field}})              → strtoupper / mb_strtoupper
     *   lower({{field}})              → strtolower / mb_strtolower
     *
     * The argument inside the function can be:
     *   - A simple variable:   {{net_amount}}
     *   - A math expression:   {{net_wt * rate}}
     */
    function _tpl_apply_functions($html, $data) {
        // Pattern: funcname( {{...}} ) or funcname( {{...}}, 'args' )
        // Captures: (1) function name, (2) content inside {{}}, (3) optional second arg
        $pattern = '/\b(money|fixed|upper|lower)\s*\(\s*\{\{([^}]+)\}\}\s*(?:,\s*[\'"]?([^\'")\s]+)[\'"]?)?\s*\)/';

        return preg_replace_callback($pattern, function($m) use ($data) {
            $func    = strtolower(trim($m[1]));
            $expr    = trim($m[2]);
            $arg2    = isset($m[3]) ? trim($m[3]) : null;

            // Resolve the value: try data key first, then math expression
            if (isset($data[$expr]) && (is_string($data[$expr]) || is_numeric($data[$expr]))) {
                $raw = $data[$expr];
            } else {
                // Try as math expression
                $raw = _tpl_eval_expr($expr, $data);
            }

            if ($raw === null || $raw === '') return '';

            switch ($func) {
                case 'money':
                    // Indian number format: 2 decimals, comma-separated groups (e.g. 1,23,456.75)
                    $num = floatval(str_replace(',', '', (string)$raw));
                    return _tpl_indian_number_format($num, 2);

                case 'fixed':
                    // Fixed decimal places: default 2, override via second arg
                    $decimals = ($arg2 !== null && is_numeric($arg2)) ? (int)$arg2 : 2;
                    $num = floatval(str_replace(',', '', (string)$raw));
                    return number_format($num, $decimals, '.', '');

                case 'upper':
                    return function_exists('mb_strtoupper') ? mb_strtoupper((string)$raw, 'UTF-8') : strtoupper((string)$raw);

                case 'lower':
                    return function_exists('mb_strtolower') ? mb_strtolower((string)$raw, 'UTF-8') : strtolower((string)$raw);

                default:
                    return (string)$raw;
            }
        }, $html);
    }
}

if (!function_exists('_tpl_indian_number_format')) {
    /**
     * Format a number in Indian number system (e.g. 1,23,456.75).
     * @param float  $num
     * @param int    $decimals
     * @return string
     */
    function _tpl_indian_number_format($num, $decimals = 2) {
        $formatted = number_format(abs($num), $decimals, '.', '');
        $parts = explode('.', $formatted);
        $int_part = $parts[0];
        $dec_part = isset($parts[1]) ? '.' . $parts[1] : '';

        // Apply Indian grouping: last 3 digits, then groups of 2
        if (strlen($int_part) > 3) {
            $last3   = substr($int_part, -3);
            $rest    = substr($int_part, 0, strlen($int_part) - 3);
            $rest    = ltrim($rest, '0') ?: '0';
            // Group the 'rest' in pairs from the right
            $chunks  = [];
            while (strlen($rest) > 2) {
                $chunks[] = substr($rest, -2);
                $rest = substr($rest, 0, strlen($rest) - 2);
            }
            if ($rest !== '') $chunks[] = $rest;
            $chunks   = array_reverse($chunks);
            $int_part = implode(',', $chunks) . ',' . $last3;
        }

        return ($num < 0 ? '-' : '') . $int_part . $dec_part;
    }
}

if (!function_exists('_tpl_tokenize')) {
    function _tpl_tokenize($expr, $data) {
        $tokens = [];
        $i      = 0;
        $len    = strlen($expr);
        while ($i < $len) {
            $ch = $expr[$i];
            if ($ch === ' ' || $ch === "\t") { $i++; continue; }
            if ($ch === '(') { $tokens[] = ['t' => 'lp']; $i++; continue; }
            if ($ch === ')') { $tokens[] = ['t' => 'rp']; $i++; continue; }
            if ($ch === '+' || $ch === '-' || $ch === '*' || $ch === '/') {
                $tokens[] = ['t' => 'op', 'v' => $ch]; $i++; continue;
            }
            if (ctype_digit($ch) || $ch === '.') {
                $num = '';
                while ($i < $len && (ctype_digit($expr[$i]) || $expr[$i] === '.')) $num .= $expr[$i++];
                $tokens[] = ['t' => 'num', 'v' => (float)$num];
                continue;
            }
            if (ctype_alpha($ch) || $ch === '_') {
                $name = '';
                while ($i < $len && (ctype_alnum($expr[$i]) || $expr[$i] === '_')) $name .= $expr[$i++];
                if (isset($data[$name])) {
                    $raw = str_replace(',', '', (string)$data[$name]);
                    $tokens[] = ['t' => 'num', 'v' => is_numeric($raw) ? (float)$raw : 0.0];
                } elseif (is_numeric($name)) {
                    $tokens[] = ['t' => 'num', 'v' => (float)$name];
                } else {
                    return null;
                }
                continue;
            }
            return null;
        }
        return $tokens;
    }
}

if (!function_exists('_tpl_parse_additive')) {
    function _tpl_parse_additive(&$tok, &$pos) {
        $left = _tpl_parse_multiplicative($tok, $pos);
        if ($left === null) return null;
        while ($pos < count($tok) && $tok[$pos]['t'] === 'op'
               && ($tok[$pos]['v'] === '+' || $tok[$pos]['v'] === '-')) {
            $op = $tok[$pos++]['v'];
            $right = _tpl_parse_multiplicative($tok, $pos);
            if ($right === null) return null;
            $left = ($op === '+') ? $left + $right : $left - $right;
        }
        return $left;
    }
}

if (!function_exists('_tpl_parse_multiplicative')) {
    function _tpl_parse_multiplicative(&$tok, &$pos) {
        $left = _tpl_parse_factor($tok, $pos);
        if ($left === null) return null;
        while ($pos < count($tok) && $tok[$pos]['t'] === 'op'
               && ($tok[$pos]['v'] === '*' || $tok[$pos]['v'] === '/')) {
            $op = $tok[$pos++]['v'];
            $right = _tpl_parse_factor($tok, $pos);
            if ($right === null) return null;
            $left = ($op === '*') ? $left * $right : ($right != 0 ? $left / $right : 0.0);
        }
        return $left;
    }
}

if (!function_exists('_tpl_parse_factor')) {
    function _tpl_parse_factor(&$tok, &$pos) {
        if ($pos >= count($tok)) return null;
        if ($tok[$pos]['t'] === 'num') return $tok[$pos++]['v'];
        if ($tok[$pos]['t'] === 'lp') {
            $pos++;
            $val = _tpl_parse_additive($tok, $pos);
            if ($val === null || $pos >= count($tok) || $tok[$pos]['t'] !== 'rp') return null;
            $pos++;
            return $val;
        }
        return null;
    }
}

if (!function_exists('_tpl_eval_expr')) {
    function _tpl_eval_expr($expr, $data) {
        $tokens = _tpl_tokenize(trim($expr), $data);
        if ($tokens === null || count($tokens) === 0) return null;
        $pos    = 0;
        $result = _tpl_parse_additive($tokens, $pos);
        if ($result === null || $pos !== count($tokens)) return null;
        if (is_infinite($result) || is_nan($result)) return null;
        // Integer result → return without decimals
        if ($result == floor($result)) return (string)(int)$result;
        // For small decimals (abs < 0.01), use 4 decimal places to avoid rounding to "0.00"
        // For normal decimals, use 2 decimal places; trim trailing zeros
        $decimals = (abs($result) > 0 && abs($result) < 0.01) ? 4 : 2;
        return rtrim(rtrim(number_format($result, $decimals, '.', ''), '0'), '.');
    }
}


