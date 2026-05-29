<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// ═══════════════════════════════════════════════════════════════════════════
// Template Receipt Helper — Variable mapping for template-based printing
// Split from receipt_helper.php to isolate template logic from legacy views
// ═══════════════════════════════════════════════════════════════════════════
function tpl_map_billing_data_to_template($data) {
    // ═══════════════════════════════════════════════════════════════════════
    // VARIABLE REGISTRY — Every template variable MUST be declared here.
    // Do NOT create new $mapped['key'] inline without adding it here first.
    // Variables grouped by default type. Use {{var_name}} in templates.
    // ═══════════════════════════════════════════════════════════════════════

    // ── Text Variables (default: '') ────────────────────────────────────
    // {{invoice_no}} {{bill_no}} {{bill_date}} {{bill_time}} {{title}}
    // {{fin_year}} {{billed_by}} {{sales_emp}} {{counter_name}}
    // {{sales_ref_no}} {{pur_ref_no}} {{remark}} {{terms_condition}}
    // {{irnno}} {{ack_no}} {{amount_in_words}}
    // {{customer_name}} {{customer_mobile}} {{customer_address}}
    // {{customer_city}} {{customer_pincode}} {{customer_state}}
    // {{customer_gstin}} {{customer_pan}} {{customer_adhar}} {{customer_passport}}
    // {{company_name}} {{company_address}} {{company_mobile}} {{company_email}}
    // {{company_gstin}} {{company_pan}} {{company_state}} {{company_state_code}}
    // {{branch_address}} {{branch_phone}} {{branch_gstin}}
    // {{card_no}} {{card_date}} {{c_card_date}} {{appr_code}}
    // {{cheque_no}} {{cheque_date}} {{payment_mode}} {{bank_name}}
    // {{purchase_invoice_no}} {{s_ret_invoice_no}} {{exchange_ref_bill_no}} {{order_no}}
    // {{credit_ref_bill_no}} {{credit_ref_bill_date}} {{return_ref_bill_no}} {{return_ref_bill_date}}
    // {{lbl_sales_amount}} {{lbl_exchange_amount}} {{lbl_net_amount}} {{lbl_return_amount}}
    // {{qr_code_url}} {{qr_code_base64}} {{qr_code_image}} {{company_logo_url}} {{app_qrcode}}
    $text_keys = [
        'invoice_no','bill_no','bill_date','bill_time','title','fin_year',
        'billed_by','sales_emp','counter_name','sales_ref_no','pur_ref_no',
        'remark','terms_condition','irnno','ack_no','amount_in_words',
        'customer_name','customer_mobile','customer_address','customer_city',
        'customer_pincode','customer_state','customer_gstin','customer_pan',
        'customer_adhar','customer_passport',
        'company_name','company_address','company_mobile','company_email',
        'company_gstin','company_pan','company_state','company_state_code',
        'branch_address','branch_phone','branch_gstin',
        'card_no','card_date','c_card_date','appr_code',
        'cheque_no','cheque_date','payment_mode','bank_name',
        'purchase_invoice_no','s_ret_invoice_no','exchange_ref_bill_no','exchange_ref_line','order_no',
        'credit_ref_bill_no','credit_ref_bill_date','return_ref_bill_no','return_ref_bill_date',
        'lbl_sales_amount','lbl_exchange_amount','lbl_net_amount','lbl_return_amount',
        'qr_code_url','qr_code_base64','qr_code_image','company_logo_url','app_qrcode','bill_count','max_va_percent'
    ];

    // ── Money Variables (default: '0.00') ──────────────────────────────
    // {{sub_total}} {{subtotal}} {{sgst_amount}} {{cgst_amount}} {{igst_amount}}
    // {{sgst}} {{cgst}} {{igst}} {{round_off}} {{handling_charges}}
    // {{grand_total}} {{net_amount}} {{net_payable}} {{net_total}} {{exchange_amount}}
    // {{gold_rate}} {{gold_rate_18ct}} {{gold_rate_24ct}} {{silver_rate}}
    // {{paid_amount}} {{total_paid}} {{balance_amount}} {{cash_received}}
    // {{cash_amount}} {{card_amount}} {{upi_amount}} {{cheque_amount}}
    // {{pay_cash}} {{pay_cheque}} {{pay_card}} {{pay_credit_card}} {{pay_debit_card}}
    // {{pay_rtgs}} {{pay_imps}} {{pay_neft}} {{pay_upi}} {{pay_due}}
    // {{total_mc_amount}} {{total_taxable}} {{discount_amount}} {{tcs_amount}} {{tds_amount}}
    // {{sales_sub_total}} {{sales_total_amount}} {{total_sales_amount}}
    // {{total_old_amount}} {{total_old_metal_amount}} {{purchase_total_amount}} {{purchase_cash_paid}}
    // {{total_return_amount}} {{total_return_amount_with_gst}} {{return_sub_total}}
    // {{total_return_cgst}} {{total_return_sgst}} {{total_return_igst}} {{total_return_tax}}
    // {{sales_return_total}} {{total_transfer_amount}}
    // {{advance_adj}} {{advance_adjustment}} {{order_advance_adj}} {{chit_adj}} {{receipt_adj}}
    // {{chit_general_total}} {{chit_pre_close_total}} {{repair_order_total}}
    // {{receipt_adj_total_amount}} {{receipt_adj_total_utilized}} {{receipt_adj_total_refund}} {{receipt_adj_total_balance}}
    // {{credit_balance_amount}} {{credit_collection_total}}
    // {{order_amount}} {{od_total_amount}} {{od_approx_total}}
    $money_keys = [
        'sub_total','subtotal','sgst_amount','cgst_amount','igst_amount',
        'sgst','cgst','igst','round_off','handling_charges',
        'grand_total','net_amount','net_payable','net_total','exchange_amount',
        'gold_rate','gold_rate_18ct','gold_rate_24ct','silver_rate',
        'paid_amount','total_paid','balance_amount','due_amount','cash_received',
        'cash_amount','card_amount','upi_amount','cheque_amount',
        'pay_cash','pay_cheque','pay_card','pay_credit_card','pay_debit_card',
        'pay_rtgs','pay_imps','pay_neft','pay_upi','pay_due','pay_advance_adj','pay_advance_adjustment',
        'total_mc_amount','total_taxable','discount_amount','tcs_amount','tds_amount',
        'sales_sub_total','sales_total_amount','total_sales_amount',
        'total_old_amount','total_old_metal_amount','purchase_total_amount','purchase_cash_paid',
        'total_return_amount','total_return_amount_with_gst','return_sub_total',
        'total_return_cgst','total_return_sgst','total_return_igst','total_return_tax',
        'sales_return_total','total_transfer_amount',
        'advance_adj','advance_adjustment','advance_deposit','order_advance_adj','chit_adj','receipt_adj',
        'chit_general_total','chit_pre_close_total','repair_order_total',
        'receipt_adj_total_amount','receipt_adj_total_utilized','receipt_adj_total_refund','receipt_adj_total_balance',
        'credit_balance_amount','credit_collection_total',
        'order_amount','od_total_amount','od_approx_total',
        'rate_benefit','chit_benefit','chit_benefit_before_gst',
        'apx_scheme_discount','chit_general_payable_total','chit_general_rate_benefit_total','item_total','total_va_content','sales_total_item_total',
        'total_stone_amount',
    ];

    // ── Weight Variables (default: '0.000') ────────────────────────────
    // {{total_gross_wt}} {{total_net_wt}}
    // {{sales_total_gross_wt}} {{sales_total_net_wt}} {{total_sales_gross_wt}} {{total_sales_net_wt}}
    // {{total_old_gross_wt}} {{total_old_net_wt}} {{total_old_metal_gross_wt}} {{total_old_metal_net_wt}}
    // {{total_return_gross_wt}} {{total_return_net_wt}} {{return_total_gross_wt}} {{return_total_net_wt}}
    // {{total_transfer_gross_wt}} {{total_transfer_net_wt}}
    // {{od_total_gross_wt}} {{od_total_net_wt}}
    $weight_keys = [
        'total_gross_wt','total_net_wt',
        'sales_total_gross_wt','sales_total_net_wt','total_sales_gross_wt','total_sales_net_wt',
        'total_old_gross_wt','total_old_net_wt','total_old_metal_gross_wt','total_old_metal_net_wt',
        'total_return_gross_wt','total_return_net_wt','return_total_gross_wt','return_total_net_wt',
        'total_transfer_gross_wt','total_transfer_net_wt',
        'od_total_gross_wt','od_total_net_wt',
        'total_stone_wt',
    ];

    // ── Integer Variables (default: 0) ─────────────────────────────────
    // {{total_items_count}} {{total_qty}} {{total_items}} {{total_pieces}}
    // {{sales_total_qty}} {{total_sales_qty}} {{total_old_qty}} {{total_old_metal_qty}}
    // {{total_return_qty}} {{return_total_qty}} {{total_transfer_qty}} {{od_total_pcs}}
    $int_keys = [
        'total_items_count','total_qty','total_items','total_pieces',
        'sales_total_qty','total_sales_qty','total_old_qty','total_old_metal_qty',
        'total_return_qty','return_total_qty','total_transfer_qty','od_total_pcs',
    ];

    // ── Boolean Flags (default: false) ─────────────────────────────────
    // {{is_sales_only}} {{is_purchase_only}} {{is_sales_and_purchase}}
    // {{is_sales_return}} {{is_sales_purchase_return}} {{is_order_receipt}}
    // {{is_order_delivery}} {{is_chit_preclose}} {{is_credit_collection}} {{is_short_bill}} {{is_long_bill}}
    // {{has_items}} {{has_sales_items}} {{has_purchase_items}} {{has_return_items}}
    // {{has_old_metal}} {{has_transfer_items}} {{has_order_advance_entries}}
    // {{has_repair_items}} {{has_chit_items}} {{has_credit_collection_history}}
    // {{has_payment_details}} {{has_advance_adj}} {{has_advance_adjustment}} {{has_order_advance_adj}}
    // {{has_chit_adj}} {{has_receipt_adj}} {{has_receipt_adjustment}}
    // {{has_sales_return}} {{has_tax_detail_items}} {{has_multiple_pages}} {{show_sales_title}}
    $flag_keys = [
        'is_sales_only','is_purchase_only','is_sales_and_purchase',
        'is_sales_return','is_sales_purchase_return','is_order_receipt',
        'is_order_delivery','is_chit_preclose','is_credit_collection','is_short_bill','is_long_bill',
        'has_items','has_sales_items','has_purchase_items','has_return_items',
        'has_old_metal','has_transfer_items','has_order_advance_entries',
        'has_repair_items','has_chit_items','has_credit_collection_history',
        'has_payment_details','has_advance_adj','has_advance_adjustment','has_order_advance_adj',
        'has_chit_adj','has_receipt_adj','has_receipt_adjustment',
        'has_sales_return','has_tax_detail_items','has_multiple_pages','show_sales_title',
        'has_chit_benefit',
        'has_apx_scheme_discount',
    ];

    // ── Array / Loop Variables (default: []) ───────────────────────────
    // {{#items}} {{#sales_items}} {{#sales}} {{#order_items}} {{#purchase_items}}
    // {{#old_metal_items}} {{#return_items}} {{#return_details}} {{#transfer_items}}
    // {{#order_advance_entries}} {{#advance_details}} {{#repair_order_items}}
    // {{#chit_general_items}} {{#chit_pre_close_items}} {{#credit_collection_history}}
    // {{#sales_return_items}} {{#order_delivery_items}} {{#receipt_adjustment_items}}
    // {{#tax_detail_items}} {{#payment_methods}} {{#payment_breakdown}}
    $array_keys = [
        'items','sales_items','sales','order_items','purchase_items',
        'old_metal_items','return_items','return_details','transfer_items',
        'order_advance_entries','advance_details','repair_order_items',
        'chit_general_items','chit_pre_close_items','credit_collection_history',
        'sales_return_items','order_delivery_items','receipt_adjustment_items',
        'tax_detail_items','payment_methods','payment_breakdown',
    ];

    // Build $mapped with correct defaults per type
    $mapped = array_fill_keys($text_keys, '')
            + array_fill_keys($money_keys, '0.00')
            + array_fill_keys($weight_keys, '0.000')
            + array_fill_keys($int_keys, 0)
            + array_fill_keys($flag_keys, false)
            + array_fill_keys($array_keys, []);
    // Special defaults
    $mapped['title'] = 'Tax-Invoice';
    $mapped['terms_condition'] = 'Subject to Terms & Conditions';
    $mapped['is_short_bill'] = true;

    // ═══════════════════════════════════════════════════════════════════════
    // SOURCE DATA — extract from the input $data array
    // ═══════════════════════════════════════════════════════════════════════
    $billing = $data['billing'];

    // ── Stone type label lookup (loaded once, used per stone row) ──
    // Maps ret_stone.stone_type (numeric) → ret_stone_type.stone_type (label)
    // e.g. 1 → 'Diamond', 2 → 'Gem Stones', 3 → 'Others'
    static $stone_type_map = null;
    if ($stone_type_map === null) {
        $stone_type_map = [];
        $CI_st = &get_instance();
        $st_q = $CI_st->db->select('id_stone_type, stone_type, stone_code')
                         ->where('status', 1)
                         ->get('ret_stone_type');
        if ($st_q && $st_q->num_rows() > 0) {
            foreach ($st_q->result_array() as $st_row) {
                $stone_type_map[$st_row['id_stone_type']] = $st_row['stone_type'];
            }
        }
    }
    // stone_cal_type: 1 = Weight-based, 2 = Fixed/Piece-based
    $cal_type_labels = [1 => 'Weight', 2 => 'Fixed'];
    $company = $data['comp_details'];
    $items_data = $data['est_other_item']; // Access the full items array


    // -- Company Details --
    $mapped['company_name'] = $company['company_name'];
    $mapped['company_address'] = $company['company_address'];
    $mapped['company_mobile'] = $company['mobile'];
    $mapped['company_email'] = $company['email'];
    $mapped['company_gstin'] = $company['gst_number'];
    $mapped['company_pan'] = $company['pan_no'];
    $mapped['company_state'] = $company['state'];
    $mapped['company_state_code'] = $company['state_code'];
    $mapped['place_of_supply'] = strtoupper($company['state'] . (!empty($company['state_code']) ? '-' . $company['state_code'] : ''));

    // -- Payment Details (cheque, card, etc.) --
    $payment_data = isset($data['payment']['pay_details']) ? $data['payment']['pay_details'] : [];
    if (!empty($payment_data)) {
        // Scan ALL payment records for mode-specific fields (bill_format_2.php loops by mode)
        foreach ($payment_data as $pay_rec) {
            $mode = strtoupper(trim($pay_rec['payment_mode'] ?? ''));
            // Cheque details — from CHQ record
            if ($mode === 'CHQ') {
                if (empty($mapped['cheque_no']) && !empty($pay_rec['cheque_no'])) {
                    $mapped['cheque_no'] = $pay_rec['cheque_no'];
                }
                if (empty($mapped['cheque_date']) && !empty($pay_rec['cheque_date'])) {
                    $mapped['cheque_date'] = $pay_rec['cheque_date'];
                }
                if (empty($mapped['bank_name']) && !empty($pay_rec['bank_name'])) {
                    $mapped['bank_name'] = $pay_rec['bank_name'];
                }
            }
            // Card details — from CC/DC record
            if ($mode === 'CC' || $mode === 'DC') {
                if (empty($mapped['card_no']) && !empty($pay_rec['card_no'])) {
                    $mapped['card_no'] = $pay_rec['card_no'];
                }
                if (empty($mapped['card_date']) && !empty($pay_rec['card_date'])) {
                    $mapped['card_date'] = $pay_rec['card_date'];
                }
                if (empty($mapped['appr_code']) && !empty($pay_rec['appr_code'])) {
                    $mapped['appr_code'] = $pay_rec['appr_code'];
                }
            }
            // General payment mode (first non-empty)
            if (empty($mapped['payment_mode']) && !empty($pay_rec['payment_mode'])) {
                $mapped['payment_mode'] = $pay_rec['payment_mode'];
            }
        }
    }

    // -- Bill Details Logic (Replicated from receipt_billing.php) --
    $type = $data['type'] ?? '';
    // Invoice Number Logic
    $invoice_no = '';
    if ($type == '') { // SALES BILL
        if ($billing['bill_type'] == 15) {
            $invoice_no = $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['approval_ref_no'];
        } else if ($billing['bill_type'] == 8) { // CREDIT COLLECTION
            $invoice_no = $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['credit_coll_refno'];
        } else if ($billing['bill_type'] == 10) { // CHIT PRE CLOSE
            $invoice_no = $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['chit_preclose_refno'];
        } else {
            $invoice_no = $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['sales_ref_no'];
        }
    } else if ($type == 'p') { // OLD METAL
        $invoice_no = $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['pur_ref_no'];
    } else if ($type == 'sr') { // SALES RETURN
        $invoice_no = $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['s_ret_refno'];
    } else if ($type == 'od') { // ORDER ADVANCE
        $invoice_no = $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['order_adv_ref_no'];
    } else {
        $invoice_no = $billing['bill_no'];
    }
    $mapped['invoice_no'] = $invoice_no;
    $mapped['bill_date'] = date('d-m-Y', strtotime($billing['bill_date']));
    $mapped['bill_time'] = date('h:i A'); // Current print time, or fetch create time if available
    
    


    // -- Customer Details --
    $mapped['customer_name'] = $billing['customer_name'];
    $mapped['customer_mobile'] = $billing['mobile'];
    $mapped['customer_address'] = $billing['address1'] . ($billing['address2'] ? ', ' . $billing['address2'] : '');
    $mapped['customer_city'] = $billing['city'];
    $mapped['customer_pincode'] = $billing['pincode'];
    $mapped['customer_state'] = $billing['cus_state'];
    $mapped['customer_gstin'] = $billing['gst_number'];
    $mapped['customer_pan'] = $billing['pan_no'];
    $mapped['customer_adhar'] = $billing['adhar_no'] ?? '';
    $mapped['customer_passport'] = $billing['passport_no'] ?? '';
    
    // -- E-Invoice / Official Details --
    $mapped['irnno'] = $billing['irnno'] ?? '';
    $mapped['ack_no'] = $billing['ack_no'] ?? '';
    $mapped['qr_code_image'] = $billing['bqrcodeimage'] ?? ''; // E-Invoice base64 QR data

    // -- QR Code Generation (matches bill_format_2.php line 480-492) --
    // Priority: E-Invoice QR (irnno + bqrcodeimage) > Bill QR Code (file-based)
    if (!empty($billing['is_eda']) && $billing['is_eda'] == 1 && !empty($billing['irnno']) && !empty($billing['bqrcodeimage'])) {
        // E-Invoice QR — base64 encoded, 125x125px
        $mapped['qr_code_base64'] = 'data:image/png;base64,' . $billing['bqrcodeimage'];
        $mapped['qr_code_url'] = ''; // Not file-based
    } else {
        // Bill QR code — generate and save to file
        $CI_qr = &get_instance();
        if (function_exists('generate_qr_code')) {
            $qr_filename = generate_qr_code($billing['bill_id']);
        } else {
            // Inline fallback if receipt_helper not loaded
            $CI_qr->load->library('phpqrcode/qrlib');
            $qr_dir = 'bill_qrcode';
            if (!is_dir($qr_dir)) mkdir($qr_dir, 0777, TRUE);
            $qr_content = base_url() . "index.php/admin_app_api/printbill/" . $billing['bill_id'];
            $qr_file = $qr_dir . '/' . $billing['bill_id'] . '.png';
            QRcode::png($qr_content, $qr_file);
            $qr_filename = $billing['bill_id'];
        }
        $qr_file_path = 'bill_qrcode/' . $qr_filename . '.png';
        $mapped['qr_code_url'] = base_url() . $qr_file_path;
        // Also create base64 for DomPDF inline rendering
        $abs_qr_path = FCPATH . $qr_file_path;
        if (file_exists($abs_qr_path)) {
            $mapped['qr_code_base64'] = 'data:image/png;base64,' . base64_encode(file_get_contents($abs_qr_path));
        } else {
            $mapped['qr_code_base64'] = '';
        }
    }

    // -- Metal Rates --
    $mapped['gold_rate'] = moneyFormatIndia(number_format(($billing['goldrate_22ct'] > 0 ? $billing['goldrate_22ct'] : ($data['metal_rate']['goldrate_22ct'] ?? 0)), 2, '.', ''));
    $mapped['gold_rate_18ct'] = moneyFormatIndia(number_format(($billing['goldrate_18ct'] > 0 ? $billing['goldrate_18ct'] : ($data['metal_rate']['goldrate_18ct'] ?? 0)), 2, '.', ''));
    $mapped['gold_rate_24ct'] = moneyFormatIndia(number_format(($billing['goldrate_24ct'] > 0 ? $billing['goldrate_24ct'] : ($data['metal_rate']['goldrate_24ct'] ?? 0)), 2, '.', ''));
    $mapped['silver_rate'] = moneyFormatIndia(number_format(($billing['silverrate_1gm'] > 0 ? $billing['silverrate_1gm'] : ($data['metal_rate']['silverrate_1gm'] ?? 0)), 2, '.', ''));



    // Title Logic & Boolean Flags for Template Rendering
    $title = "Tax-Invoice";
    $mapped['is_sales_only'] = false;
    $mapped['is_sales_and_purchase'] = false;
    $mapped['is_sales_purchase_return'] = false;
    $mapped['is_purchase_only'] = false;
    $mapped['is_order_receipt'] = false;
    $mapped['is_sales_return'] = false;
    $mapped['is_credit_collection'] = false;
    $mapped['is_order_delivery'] = false;
    $mapped['is_chit_preclose'] = false;
    
    switch ($billing['bill_type']) {
        case 1: 
            $title = "Sales Bill"; 
            $mapped['is_sales_only'] = true;
            break;
        case 2: 
            $title = "Sales & Purchase Bill"; 
            $mapped['is_sales_and_purchase'] = true;
            break;
        case 3: 
            $title = "Sales, Purchase & Return Bill"; 
            $mapped['is_sales_purchase_return'] = true;
            break;
        case 4: 
            $title = "Purchase Bill"; 
            $mapped['is_purchase_only'] = true;
            break;
        case 5: 
            $title = "Order Receipt"; 
            $mapped['is_order_receipt'] = true;
            break;
        case 6: 
            $title = "Advance"; 
            break;
        case 7: 
            $title = "Sales Return Bill"; 
            $mapped['is_sales_return'] = true;
            break;
        case 8: 
            $title = "Credit Collection"; 
            $mapped['is_credit_collection'] = true;
            break;
        case 9: 
            $title = "Order Delivery"; 
            $mapped['is_order_delivery'] = true;
            break;
        case 10: 
            $title = "Chit Pre Close"; 
            $mapped['is_chit_preclose'] = true;
            break;
        case 11: 
            $title = "Repair Order Delivery"; 
            $mapped['is_order_delivery'] = true;
            break;
        case 12: 
            $title = "Supplier Sales Bill"; 
            $mapped['is_purchase_only'] = true;
            break;
        case 13: 
            $title = "Sales Transfer"; 
            $mapped['is_sales_only'] = true;
            break;
        case 14: 
            $title = "Sales Return Transfer"; 
            $mapped['is_sales_return'] = true;
            break;
        case 15: 
            $title = "Approval Sales Bill"; 
            $mapped['is_sales_only'] = true;
            break;
        default: 
            $title = "Tax-Invoice"; 
            break;
    }
    $mapped['title'] = $title;

    // Composite flag: true when the bill has an items table to show
    // Covers: Sales, Sales+Purchase, Sales+Purchase+Return, Order Receipt,
    // Order Delivery, Sales Transfer, Approval — basically anything with item rows
    $mapped['has_items'] = (
        $mapped['is_sales_only'] || 
        $mapped['is_sales_and_purchase'] || 
        $mapped['is_sales_purchase_return'] || 
        $mapped['is_order_receipt'] || 
        $mapped['is_order_delivery']
    );

    // Composite flag: true when the bill has old metal purchase rows
    $mapped['has_old_metal'] = (
        $mapped['is_purchase_only'] || 
        $mapped['is_sales_and_purchase'] || 
        $mapped['is_sales_purchase_return']
    );

    // Composite flag: true when the bill has sales return rows
    $mapped['has_sales_return'] = (
        $mapped['is_sales_return'] || 
        $mapped['is_sales_purchase_return']
    );

    // -- Employee Details --
    $mapped['billed_by'] = $billing['emp_name'] ?? '';
    $mapped['sales_emp'] = ''; // Initialize
    if (!empty($items_data['item_details']) && isset($items_data['item_details'][0]['esti_emp_name'])) {
         $mapped['sales_emp'] = $items_data['item_details'][0]['esti_emp_name'];
    }

    // -- Items Mapping --
    $mapped_items = []; // Simplified merged array for backward compatibility
    
    // Categorized arrays for specific table blocks
    $sales_items = [];
    $return_items = [];
    $old_metal_items = [];
    $transfer_items = [];
    $stone_items = [];

    $mapped['items'] = []; // Default empty
    $total_gross_wt = 0;
    $total_net_wt = 0;
    $total_pieces = 0;
    
    // Aggregates for footer
    $total_taxable_amt = 0;
    $total_sgst = 0;
    $total_cgst = 0;
    $total_igst = 0;
    $sum_va_costs = 0;
    $sales_total_item_total = 0;
    $max_va_percent = 0;
    $total_stone_wt = 0;
    $total_stone_amount = 0;

    // Per-table-type totals for accurate Konva footer rendering
    $sales_total_gross_wt = 0;
    $sales_total_net_wt = 0;
    $sales_total_pieces = 0;
    $sales_sub_total = 0;
    $return_total_gross_wt = 0;
    $return_total_net_wt = 0;
    $return_total_pieces = 0;
    $return_sub_total = 0;
    
    // 1. Standard Sales Items
    // Fix: The model's return_item_cost filter (ret_billing_model line ~1788)
    // moves items with return_item_cost > 0 from item_details → return_details.
    // For bill types that need a sales items table (1,2,3,9,11,15), if this filter
    // emptied item_details, merge return_details back so items render in the table.
    // Bill type 7 (Sales Return) is excluded — its items belong in return_details.
    $sales_bill_types = [1, 2, 3, 9, 11, 13, 14, 15];
    // Fix for bill_type 13/14: model stores items in 'sales_trasnfer_details' (grouped by category)
    // instead of 'item_details'. Map them back so the template can render rows.
    if (in_array($billing['bill_type'], [13, 14]) && !empty($items_data['sales_trasnfer_details']) && empty($items_data['item_details'])) {
        foreach ($items_data['sales_trasnfer_details'] as $tItem) {
            $items_data['item_details'][] = array(
                'product_name'    => $tItem['category_name'] ?? '-',
                'product_short_code' => $tItem['cat_code'] ?? '-',
                'hsn_code'        => $tItem['hsn_code'] ?? '',
                'piece'           => $tItem['piece'] ?? 0,
                'gross_wt'        => $tItem['gross_wt'] ?? 0,
                'net_wt'          => $tItem['net_wt'] ?? 0,
                'item_cost'       => $tItem['item_cost'] ?? 0,
                'item_total_tax'  => $tItem['item_total_tax'] ?? 0,
                'total_cgst'      => $tItem['total_cgst'] ?? 0,
                'total_sgst'      => $tItem['total_sgst'] ?? 0,
                'total_igst'      => $tItem['total_igst'] ?? 0,
                'tax_percentage'  => $tItem['tax_percentage'] ?? 0,
                'rate_per_grm'    => $tItem['rate_per_grm'] ?? 0,
                'calculation_based_on' => 0,
                'wastage_percent' => 0,
                'mc_value'        => 0,
                'mc_type'         => 0,
                'bill_det_id'     => $tItem['bill_det_id'] ?? '',
                'huid'            => $tItem['huid'] ?? '',
            );
        }
    }
    // Capture return reference data BEFORE any clearing — needed for s_ret_invoice_no / exchange_ref_bill_no
    $original_return_details = $items_data['return_details'] ?? [];

    if (in_array($billing['bill_type'], $sales_bill_types) && $billing['bill_type'] != 14 && !empty($items_data['return_details']) && empty($items_data['item_details'])) {
        $items_data['item_details'] = $items_data['return_details'];
        $items_data['return_details'] = []; // Clear to prevent double-counting in return processing
    }
    if (!empty($items_data['item_details'])) {
        foreach ($items_data['item_details'] as $item) {
            // Calculations from view
            $item_total_tax = $item['item_total_tax'];
            $item_cost = $item['item_cost'];
            $item_taxable = $item_cost - $item_total_tax;

            $sales_total_item_total += floatval($item_cost);
            $max_va_percent = max($max_va_percent, floatval($item['wastage_percent'] ?? 0));
            
            // Wastage & MC Logic
             $wastge_wt = 0;
             $mc = 0;
             if ($item['calculation_based_on'] == 0) {
                $wastge_wt = ($item['gross_wt'] * ($item['wastage_percent'] / 100));
                $mc = ($item['mc_type'] == 2 ? ($item['mc_value'] * $item['gross_wt']) : ($item['mc_value'] * 1));
            } else if ($item['calculation_based_on'] == 1) { // Net wt
                 $wastge_wt = ($item['net_wt'] * ($item['wastage_percent'] / 100));
                 $mc = ($item['mc_type'] == 2 ? ($item['mc_value'] * $item['net_wt']) : ($item['mc_value'] * 1));
            } else if ($item['calculation_based_on'] == 2) { // Pure wt (logic similar to view)
                 $wastge_wt = ($item['net_wt'] * ($item['wastage_percent'] / 100)); // Approximation
                 $mc = ($item['mc_type'] == 2 ? ($item['mc_value'] * $item['gross_wt']) : ($item['mc_value'] * 1));
            }
            
            $wastge_amt = $wastge_wt * $item['rate_per_grm'];
            $sum_va_costs += floatval($wastge_amt);
            $va_text = '-';
            if ($wastge_amt > 0) {
                 // Logic to match "1150/g" style if applicable
                 // Assuming it's based on the weight used for calculation
                 $calc_wt = ($item['net_wt'] > 0 ? $item['net_wt'] : $item['gross_wt']);
                 if ($calc_wt > 0) {
                     $va_rate = $wastge_amt / $calc_wt;
                     $va_text = number_format($va_rate, 2) . '/g';
                 } else {
                     $va_text = number_format($wastge_amt, 2);
                 }
            } else {
                $va_text = '-';
            }

            // Map Main Item
            $item_data = [
                'type' => 'Sale',
                'sno' => count($mapped_items) + 1,
                'hsn_code' => $item['hsn_code'],
                'description' => $item['product_name'] . ($item['size_name'] ? '-' . $item['size_name'] : ''),
                'purity' => $item['purname'],
                'qty' => $item['piece'],
                'gross_wt' => number_format($item['gross_wt'], 3),
                'net_wt' => number_format($item['net_wt'], 3),
                'va_percent' => $item['wastage_percent'],
                'va_content' => $va_text,
                'mc' => moneyFormatIndia(number_format($mc, 2, '.', '')),
                'rate' => moneyFormatIndia(number_format($item['rate_per_grm'], 2, '.', '')),
                'amount' => moneyFormatIndia(number_format($item_taxable, 2, '.', '')), // Taxable amount (item_cost - tax) — matches legacy view
                'taxable_amount' => moneyFormatIndia(number_format($item_taxable, 2, '.', '')),
                'cgst_amt' => moneyFormatIndia(number_format($item['total_cgst'], 2, '.', '')),
                'sgst_amt' => moneyFormatIndia(number_format($item['total_sgst'], 2, '.', '')),
                'igst_amt' => moneyFormatIndia(number_format($item['total_igst'], 2, '.', '')),
                'item_total' => (floatval($item_cost) > 0) ? moneyFormatIndia(number_format(floatval($item_cost), 2, '.', '')) : ""
            ];

            // ── Stone aggregate variables at item level ──
            // Compute totals from stone_details so {{stone_wt}}, {{stone_amount}}, {{stone_pieces}}
            // can be used as regular columns in the items table (not just in sub-rows).
            $item_stone_wt = 0;
            $item_stone_amt = 0;
            $item_stone_pcs = 0;
            if (!empty($item['stone_details'])) {
                foreach ($item['stone_details'] as $_sd) {
                    $item_stone_wt  += floatval($_sd['wt'] ?? 0);
                    $item_stone_amt += floatval($_sd['amount'] ?? 0);
                    $item_stone_pcs += intval($_sd['pieces'] ?? 0);
                }
            }
            $item_data['stone_wt']     = number_format($item_stone_wt, 3);
            $item_data['stone_amount'] = moneyFormatIndia(number_format($item_stone_amt, 2, '.', ''));
            $item_data['stone_pieces'] = $item_stone_pcs;

            // Accumulate bill-level stone totals
            $total_stone_wt += $item_stone_wt;
            $total_stone_amount += $item_stone_amt;
            
            // Build nested _sub_rows from stone_details for master-detail table rendering
            // Stones are ONLY rendered via sub-rows (not appended as flat rows to the items array)
            $item_sub_rows = [];
            if (!empty($item['stone_details'])) {
                $stone_sno = 1;
                foreach ($item['stone_details'] as $stone) {
                    $s_pcs  = $stone['pieces'];
                    $s_name = $stone['stone_name'];
                    $s_wt   = number_format($stone['wt'], 3);
                    $s_uom  = $stone['uom_short_code'] ?? '';
                    $s_rate = number_format($stone['rate_per_gram'], 2, '.', '');
                    $s_amt  = number_format($stone['amount'], 2, '.', '');

                    // Formatted description: "1 Pcs Diamond 0.020 CT x 10,000.00=200.00"
                    $stone_desc = $s_pcs . ' Pcs ' . $s_name . ' ' . $s_wt . ' ' . $s_uom
                                . ' x ' . moneyFormatIndia($s_rate) . '=' . moneyFormatIndia($s_amt);

                    $st_type_val = $stone['stone_type'] ?? '';
                    $st_label = $stone_type_map[$st_type_val] ?? $st_type_val;
                    $stone_type_label = '';
                    if ($st_label !== '') {
                        $stone_type_label = (strtolower($st_label) === 'diamond') ? 'dia' : 'stone';
                    }

                    $sub_row = [
                        'stone_name'           => $s_name,
                        'stone_pieces'         => $s_pcs,
                        'stone_wt'             => $s_wt,
                        'stone_rate'           => moneyFormatIndia($s_rate),
                        'stone_amount'         => moneyFormatIndia($s_amt),
                        'stone_uom'            => $s_uom,
                        'stone_cal_type'       => $stone['stone_cal_type'] ?? '',
                        'stone_cal_type_label' => $cal_type_labels[$stone['stone_cal_type'] ?? ''] ?? '',
                        'stone_type'           => $stone['stone_type'] ?? '',
                        'stone_type_label'     => $stone_type_label,
                        'stone_description'    => $stone_desc,
                    ];
                    $item_sub_rows[] = $sub_row;

                    // Also collect into flat stone_items array for standalone Stone Details table
                    $stone_items[] = [
                        'stone_sno'        => $stone_sno++,
                        'item_sno'         => count($mapped_items) + 1,
                        'item_description' => $item['product_name'] . ($item['size_name'] ? '-' . $item['size_name'] : ''),
                        'stone_name'           => $s_name,
                        'stone_pieces'         => $s_pcs,
                        'stone_wt'             => $s_wt,
                        'stone_rate'           => moneyFormatIndia($s_rate),
                        'stone_amount'         => moneyFormatIndia($s_amt),
                        'stone_uom'            => $s_uom,
                        'stone_cal_type'       => $stone['stone_cal_type'] ?? '',
                        'stone_cal_type_label' => $cal_type_labels[$stone['stone_cal_type'] ?? ''] ?? '',
                        'stone_type'           => $stone['stone_type'] ?? '',
                        'stone_type_label'     => $stone_type_label,
                        'stone_description'    => $stone_desc,
                    ];
                }
            }
            $item_data['_sub_rows'] = $item_sub_rows;
            
            $sales_items[] = $item_data;
            $mapped_items[] = $item_data;

            $total_gross_wt += $item['gross_wt'];
            $total_net_wt += $item['net_wt'];
            $total_pieces += $item['piece'];
            // Sales-only totals for Konva footer
            $sales_total_gross_wt += $item['gross_wt'];
            $sales_total_net_wt += $item['net_wt'];
            $sales_total_pieces += $item['piece'];
            $sales_sub_total += $item_taxable;
            
            $total_taxable_amt += $item_taxable;
            $total_sgst += $item['total_sgst'];
            $total_cgst += $item['total_cgst'];
            $total_igst += $item['total_igst'];
        }
    }

    // -- Order Number for Order Delivery (bill_type 9, 11) --
    // order_no in ret_bill_details is often NULL; fetch from customerorder via id_orderdetails
    if (in_array($billing['bill_type'], [9, 11])) {
        $CI =& get_instance();
        $oq = $CI->db->query("SELECT co.order_no 
            FROM ret_bill_details bd
            INNER JOIN customerorderdetails cod ON cod.id_orderdetails = bd.id_orderdetails
            INNER JOIN customerorder co ON co.id_customerorder = cod.id_customerorder
            WHERE bd.bill_id = " . intval($billing['bill_id']) . " 
            AND bd.id_orderdetails IS NOT NULL LIMIT 1");
        $orow = $oq->row_array();
        if (!empty($orow['order_no'])) {
            $mapped['order_no'] = $orow['order_no'];
            $mapped['has_order_no'] = true;
        }
    }

    // 2. Sales Return Items (Credit Note) - Corrected Key from View
    // NOTE: sales_ret_trans_details is grouped by category — no per-item stone details.
    // Prefer return_details (processed in block at ~line 1461) which has per-item rtn_stone_details.
    if (!empty($items_data['sales_ret_trans_details']) && empty($items_data['return_details'])) {
        foreach ($items_data['sales_ret_trans_details'] as $item) {
            // Skip phantom rows from LEFT JOIN (all NULLs/zeros when no actual returns exist)
            if (empty($item['item_cost']) && empty($item['gross_wt'])) continue;
            
            $item_total_tax = $item['item_total_tax']; 
            $item_cost = $item['item_cost']; 
             
            $return_data = [
                'type' => 'Return',
                // Unprefixed keys — used by V2 Konva table columns ({{sno}}, {{description}}, etc.)
                'sno' => count($return_items) + 1,
                'hsn_code' => $item['hsn_code'],
                'description' => $item['category_name'],
                'purity' => '', 
                'qty' => $item['piece'],
                'gross_wt' => number_format($item['gross_wt'], 3),
                'net_wt' => number_format($item['net_wt'], 3),
                'va_percent' => 0, 
                'va_content' => '-',
                'mc' => 0,
                'rate' => moneyFormatIndia(number_format($item['rate_per_grm'], 2, '.', '')), 
                'amount' => moneyFormatIndia(number_format($item_cost - $item_total_tax, 2, '.', '')),
                'taxable_amount' => moneyFormatIndia(number_format($item_cost - $item_total_tax, 2, '.', '')),
                'cgst_amt' => moneyFormatIndia(number_format($item['total_cgst'] ?? 0, 2, '.', '')),
                'sgst_amt' => moneyFormatIndia(number_format($item['total_sgst'] ?? 0, 2, '.', '')),
                'igst_amt' => moneyFormatIndia(number_format($item['total_igst'] ?? 0, 2, '.', '')),
                // Prefixed keys — backward compatibility with V1/legacy templates
                'return_sno' => count($return_items) + 1,
                'return_hsn_code' => $item['hsn_code'],
                'return_description' => $item['category_name'],
                'return_purity' => '', 
                'return_qty' => $item['piece'],
                'return_gross_wt' => number_format($item['gross_wt'], 3),
                'return_net_wt' => number_format($item['net_wt'], 3),
                'return_va_percent' => 0, 
                'return_va_content' => '-',
                'return_mc' => 0,
                'return_rate' => moneyFormatIndia(number_format($item['rate_per_grm'], 2, '.', '')), 
                'return_amount' => moneyFormatIndia(number_format($item_cost - $item_total_tax, 2, '.', '')),
                'return_taxable_amount' => moneyFormatIndia(number_format($item_cost - $item_total_tax, 2, '.', '')),
                'return_sub_total' => moneyFormatIndia(number_format($item_cost - $item_total_tax, 2, '.', '')),
                'return_total_amount' => moneyFormatIndia(number_format($item_cost, 2, '.', '')),
                'return_cgst_amt' => moneyFormatIndia(number_format($item['total_cgst'] ?? 0, 2, '.', '')),
                'return_sgst_amt' => moneyFormatIndia(number_format($item['total_sgst'] ?? 0, 2, '.', '')),
                'return_igst_amt' => moneyFormatIndia(number_format($item['total_igst'] ?? 0, 2, '.', '')),
                // Stone aggregate variables (from summary join data if available)
                'stone_wt' => number_format($item['stn_wgt'] ?? $item['stone_wt'] ?? 0, 3),
                'stone_amount' => moneyFormatIndia(number_format($item['stone_price'] ?? $item['stone_amount'] ?? 0, 2, '.', '')),
                'return_stone_wt' => number_format($item['stn_wgt'] ?? $item['stone_wt'] ?? 0, 3),
                'return_stone_amount' => moneyFormatIndia(number_format($item['stone_price'] ?? $item['stone_amount'] ?? 0, 2, '.', '')),
                // Stone sub-row variables with return_ prefix
                // Note: sales_ret_trans_details is grouped by category — no individual stone rows available.
                // _sub_rows is set to empty; for bill_types 3/7 the return_details block populates them.
                'return_stone_pieces' => 0,
                'return_stone_name' => '',
                'return_stone_rate' => '',
                'return_stone_uom' => '',
                'return_stone_cal_type' => '',
                'return_stone_type' => '',
                'return_stone_description' => '',
            ];
            // Empty _sub_rows — bill_type 14 uses grouped data (no per-item stone rows)
            $return_data['_sub_rows'] = [];
            
            $return_items[] = $return_data;
            $mapped_items[] = $return_data;

            // Accumulate tax totals from return items
            $total_sgst += $item['total_sgst'] ?? 0;
            $total_cgst += $item['total_cgst'] ?? 0;
            $total_igst += $item['total_igst'] ?? 0;
            $total_taxable_amt += ($item_cost - $item_total_tax);
            $total_gross_wt += $item['gross_wt'] ?? 0;
            $total_net_wt += $item['net_wt'] ?? 0;
            $total_pieces += $item['piece'] ?? 0;
            // Return-only totals for Konva footer
            $return_total_gross_wt += $item['gross_wt'] ?? 0;
            $return_total_net_wt += $item['net_wt'] ?? 0;
            $return_total_pieces += $item['piece'] ?? 0;
            $return_sub_total += ($item_cost - $item_total_tax);
        }
    }

    // 3. Old Metal Purchase
    if (!empty($items_data['old_matel_details'])) {
        foreach ($items_data['old_matel_details'] as $item) {
            $old_metal_data = [
                'old_metal_type' => 'Old Metal',
                // Unprefixed keys — V2 Konva table columns ({{sno}}, {{description}}, etc.)
                'sno' => count($old_metal_items) + 1,
                'hsn_code' => '71080000',
                'description' => $item['old_metal_type'],
                'purity' => $item['touch'],
                'qty' => 1,
                'gross_wt' => number_format($item['gross_wt'], 3),
                'net_wt' => number_format($item['net_wt'], 3),
                'va_percent' => 0,
                'va_content' => '-',
                'mc' => 0,
                'rate' => moneyFormatIndia(number_format($item['rate_per_gram'], 2, '.', '')), 
                'amount' => moneyFormatIndia(number_format($item['amount'], 2, '.', '')),
                'taxable_amount' => 0,
                // Prefixed keys — backward compatibility with V1/legacy templates
                'old_metal_sno' => count($old_metal_items) + 1,
                'old_metal_hsn_code' => '71080000',
                'old_metal_description' => $item['old_metal_type'],
                'old_metal_purity' => $item['touch'],
                'old_metal_qty' => 1,
                'old_metal_gross_wt' => number_format($item['gross_wt'], 3),
                'old_metal_net_wt' => number_format($item['net_wt'], 3),
                'old_metal_va_percent' => 0,
                'old_metal_va_content' => '-',
                'old_metal_mc' => 0,
                'old_metal_rate' => moneyFormatIndia(number_format($item['rate_per_gram'], 2, '.', '')), 
                'old_metal_amount' => moneyFormatIndia(number_format($item['amount'], 2, '.', '')),
                'old_metal_taxable_amount' => 0,
                'stone_wt' => number_format($item['stone_wt'] ?? 0, 3),
                'dust_wt'  => number_format($item['dust_wt']  ?? 0, 3),
                'old_metal_stone_wt' => number_format($item['stone_wt'] ?? 0, 3),
                'old_metal_dust_wt'  => number_format($item['dust_wt']  ?? 0, 3),
            ];

            // Build nested _sub_rows from stone_details for old gold items
            $om_sub_rows = [];
            if (!empty($item['stone_details'])) {
                foreach ($item['stone_details'] as $stone) {
                    $s_pcs  = $stone['pieces'] ?? 0;
                    $s_name = $stone['stone_name'] ?? 'Stone';
                    $s_wt   = number_format($stone['wt'] ?? 0, 3);
                    $s_uom  = $stone['uom_short_code'] ?? '';
                    $s_rate = number_format($stone['rate_per_gram'] ?? 0, 2, '.', '');
                    $s_amt  = number_format($stone['price'] ?? 0, 2, '.', '');

                    $stone_desc = $s_pcs . ' Pcs ' . $s_name . ' ' . $s_wt . ' ' . $s_uom
                                . ' x ' . moneyFormatIndia($s_rate) . '=' . moneyFormatIndia($s_amt);

                    $om_sub_rows[] = [
                        'old_metal_stone_name'        => $s_name,
                        'old_metal_stone_pieces'      => $s_pcs,
                        'old_metal_stone_wt'          => $s_wt,
                        'old_metal_stone_rate'        => moneyFormatIndia($s_rate),
                        'old_metal_stone_amount'      => moneyFormatIndia($s_amt),
                        'old_metal_stone_uom'         => $s_uom,
                        'old_metal_stone_cal_type'    => $stone['stone_cal_type'] ?? '',
                        'old_metal_stone_type'        => $stone['stone_type'] ?? '',
                        'old_metal_stone_description' => $stone_desc,
                    ];
                }
            }
            $old_metal_data['_sub_rows'] = $om_sub_rows;
            // Aggregate stone total for old gold item
            $om_stone_total = 0;
            if (!empty($item['stone_details'])) {
                foreach ($item['stone_details'] as $stone) {
                    $om_stone_total += floatval($stone['price'] ?? 0);
                }
            }
            $old_metal_data['stone_amount'] = moneyFormatIndia(number_format($om_stone_total, 2, '.', ''));
            $old_metal_data['old_metal_stone_amount'] = $old_metal_data['stone_amount'];

            $old_metal_items[] = $old_metal_data;
            $mapped_items[] = $old_metal_data;
        }
    }
    
    // 4. Sales Transfer
    if (!empty($items_data['sales_trasnfer_details'])) {
         foreach ($items_data['sales_trasnfer_details'] as $item) {
            $item_total_tax = $item['item_total_tax'];
            $item_cost = $item['item_cost'];
            $transfer_data = [
                'type' => 'Transfer',
                'sno' => count($transfer_items) + 1,
                'hsn_code' => $item['hsn_code'],
                'description' => $item['category_name'] . ' (Transfer)',
                'purity' => '',
                'qty' => $item['piece'],
                'gross_wt' => number_format($item['gross_wt'], 3),
                'net_wt' => number_format($item['net_wt'], 3),
                'va_percent' => 0,
                'va_content' => '-',
                'mc' => 0,
                'rate' => moneyFormatIndia(number_format($item['rate_per_grm'], 2, '.', '')),
                'amount' => moneyFormatIndia(number_format($item_cost, 2, '.', '')),
                'taxable_amount' => moneyFormatIndia(number_format($item_cost - $item_total_tax, 2, '.', ''))
            ];
            
            $transfer_items[] = $transfer_data;
            $mapped_items[] = $transfer_data;
         }
    }


    $mapped['items'] = $mapped_items;
    $mapped['sales_items'] = $sales_items;
    $mapped['sales'] = $sales_items; // Alias for user's template change
    $mapped['stone_items'] = $stone_items;
    $mapped['order_items'] = $sales_items; // Alias for Order Receipt

    // -- Order Advance (bill_type=5): Map order_details from customerorderdetails --
    if ($billing['bill_type'] == 5 && !empty($items_data['order_details'])) {
        $order_mapped_items = [];
        $od_total_pcs = 0;
        $od_total_gross_wt = 0;
        $od_total_net_wt = 0;
        $od_total_amount = 0;
        $od_total_cgst = 0;
        $od_total_sgst = 0;
        $od_total_igst = 0;
        $sno = 1;

        foreach ($items_data['order_details'] as $od) {
            // Calculate VA (wastage)
            $od_wastage_wt = ($od['net_wt'] * ($od['wast_percent'] / 100));
            $od_wastage_amt = $od_wastage_wt * $od['rate_per_gram'];

            $sum_va_costs += floatval($od_wastage_amt);
            $sales_total_item_total += floatval($od['rate']);
            $max_va_percent = max($max_va_percent, floatval($od['wast_percent'] ?? 0));

            // Calculate MC based on calculation_based_on
            $mc = 0;
            $calc_based = isset($od['calculation_based_on']) ? $od['calculation_based_on'] : 0;
            $mc_type = isset($od['id_mc_type']) ? $od['id_mc_type'] : 2;
            if ($calc_based == 0) {
                $mc = ($mc_type == 2 ? ($od['mc'] * $od['weight']) : ($od['mc'] * $od['totalitems']));
            } else if ($calc_based == 1) {
                $mc = ($mc_type == 2 ? ($od['mc'] * $od['net_wt']) : ($od['mc'] * $od['totalitems']));
            } else if ($calc_based == 2) {
                $mc = ($mc_type == 2 ? ($od['mc'] * $od['weight']) : ($od['mc'] * $od['totalitems']));
            }

            // VA content display
            $va_text = '-';
            if ($od_wastage_amt > 0 || $mc > 0) {
                $calc_wt = ($od['net_wt'] > 0 ? $od['net_wt'] : $od['weight']);
                if ($calc_wt > 0) {
                    $va_weight = ($od_wastage_amt + $mc) / $od['rate_per_gram'];
                    $va_text = number_format($va_weight, 3);
                }
            }

            // Rate without GST
            $item_tax = $od['total_cgst'] + $od['total_sgst'] + $od['total_igst'];
            $rate_without_gst = $od['rate'] - $item_tax;

            // Stone amount
            $stone_amount = 0;
            if (!empty($od['stones'])) {
                foreach ($od['stones'] as $stone) {
                    $stone_amount += $stone['st_price'];
                }
            }

            $description = !empty($od['sub_design_name']) ? $od['sub_design_name'] : $od['product_name'];
            if (!empty($od['size_name'])) {
                $description .= '-' . $od['size_name'];
            }

            $item_data = [
                'type' => 'Order',
                'sno' => $sno,
                'hsn_code' => $od['hsn_code'],
                'description' => $description,
                'purity' => '',
                'qty' => $od['totalitems'],
                'gross_wt' => number_format($od['weight'], 3),
                'net_wt' => number_format($od['net_wt'], 3),
                'va_percent' => $od['wast_percent'],
                'va_content' => $va_text,
                'mc' => moneyFormatIndia(number_format($mc, 2, '.', '')),
                'rate' => moneyFormatIndia(number_format($od['rate_per_gram'], 2, '.', '')),
                'amount' => moneyFormatIndia(number_format($rate_without_gst - $stone_amount, 2, '.', '')),
                'taxable_amount' => moneyFormatIndia(number_format($rate_without_gst, 2, '.', '')),
                'cgst_amt' => moneyFormatIndia(number_format($od['total_cgst'], 2, '.', '')),
                'sgst_amt' => moneyFormatIndia(number_format($od['total_sgst'], 2, '.', '')),
                'igst_amt' => moneyFormatIndia(number_format($od['total_igst'], 2, '.', '')),
                'item_total' => (floatval($od['rate']) > 0) ? moneyFormatIndia(number_format(floatval($od['rate']), 2, '.', '')) : ""
            ];

            // Add stone sub-rows
            $item_data['_sub_rows'] = [];
            if (!empty($od['stones'])) {
                foreach ($od['stones'] as $stone) {
                    $item_data['_sub_rows'][] = [
                        'type' => 'Stone',
                        'sno' => '',
                        'hsn_code' => '',
                        'description' => $stone['stone_name'] ?? $stone['st_name'] ?? 'Stone',
                        'purity' => '',
                        'qty' => $stone['pieces'] ?? $stone['st_pieces'] ?? '',
                        'gross_wt' => '',
                        'net_wt' => number_format($stone['wt'] ?? $stone['st_wt'] ?? 0, 3),
                        'va_percent' => '',
                        'va_content' => '',
                        'mc' => '',
                        'rate' => moneyFormatIndia(number_format($stone['rate_per_gram'] ?? $stone['st_rate'] ?? 0, 2, '.', '')),
                        'amount' => moneyFormatIndia(number_format($stone['amount'] ?? $stone['st_price'] ?? 0, 2, '.', '')),
                        'taxable_amount' => '',
                    ];
                }
            }
            $order_mapped_items[] = $item_data;

            $od_total_pcs += $od['totalitems'];
            $od_total_gross_wt += $od['weight'];
            $od_total_net_wt += $od['net_wt'];
            $od_total_amount += $rate_without_gst;
            $od_total_cgst += $od['total_cgst'];
            $od_total_sgst += $od['total_sgst'];
            $od_total_igst += $od['total_igst'];
            $sno++;
        }

        // Override the empty arrays with order data
        $mapped['items'] = $order_mapped_items;
        $mapped['sales_items'] = $order_mapped_items;
        $mapped['sales'] = $order_mapped_items;
        $mapped['order_items'] = $order_mapped_items;
        $mapped_items = $order_mapped_items;
        $sales_items = $order_mapped_items;

        // Update totals
        $total_pieces = $od_total_pcs;
        $total_gross_wt = $od_total_gross_wt;
        $total_net_wt = $od_total_net_wt;
        $total_taxable_amt = $od_total_amount;
        $total_sgst = $od_total_sgst;
        $total_cgst = $od_total_cgst;
        $total_igst = $od_total_igst;

        // Extract order_no from advance_details
        if (!empty($items_data['advance_details'])) {
            $mapped['order_no'] = $items_data['advance_details'][0]['order_no'] ?? '';
        }

        // Order-specific totals for display
        $mapped['od_total_pcs'] = $od_total_pcs;
        $mapped['od_total_gross_wt'] = number_format($od_total_gross_wt, 3);
        $mapped['od_total_net_wt'] = number_format($od_total_net_wt, 3);
        $mapped['od_total_amount'] = moneyFormatIndia(number_format($od_total_amount, 2, '.', ''));
        $mapped['od_approx_total'] = moneyFormatIndia(number_format($od_total_amount + $od_total_cgst + $od_total_sgst + $od_total_igst, 2, '.', ''));
    }
    
    // -- Secondary Totals Calculation --
    // Returns
    $ret_qty = 0; $ret_gwt = 0; $ret_nwt = 0; $ret_amt = 0;
    foreach ($return_items as $item) {
        $ret_qty += $item['return_qty'];
        $ret_gwt += (float)$item['return_gross_wt'];
        $ret_nwt += (float)$item['return_net_wt'];
        $ret_amt += (float)str_replace(',','',$item['return_amount']);
    }
    $mapped['return_items'] = $return_items;
    $mapped['return_details'] = $return_items; // Alias for print template {{#return_details}} block
    $mapped['total_return_qty'] = $ret_qty;
    $mapped['total_return_gross_wt'] = number_format($ret_gwt, 3);
    $mapped['total_return_net_wt'] = number_format($ret_nwt, 3);
    $mapped['total_return_amount'] = moneyFormatIndia(number_format($ret_amt, 2, '.', ''));

    // Old Metal
    $om_qty = 0; $om_gwt = 0; $om_nwt = 0; $om_amt = 0;
    foreach ($old_metal_items as $item) {
        $om_qty += $item['old_metal_qty'];
        $om_gwt += (float)$item['old_metal_gross_wt'];
        $om_nwt += (float)$item['old_metal_net_wt'];
        $om_amt += (float)str_replace(',','',$item['old_metal_amount']);
    }
    $mapped['old_metal_items'] = $old_metal_items;
    $mapped['total_old_metal_qty'] = $om_qty;
    $mapped['total_old_metal_gross_wt'] = number_format($om_gwt, 3);
    $mapped['total_old_metal_net_wt'] = number_format($om_nwt, 3);
    $mapped['total_old_metal_amount'] = moneyFormatIndia(number_format($om_amt, 2, '.', ''));

    // Transfer
    $tr_qty = 0; $tr_gwt = 0; $tr_nwt = 0; $tr_amt = 0;
    foreach ($transfer_items as $item) {
        $tr_qty += $item['qty'];
        $tr_gwt += (float)$item['gross_wt'];
        $tr_nwt += (float)$item['net_wt'];
        $tr_amt += (float)str_replace(',','',$item['amount']);
    }
    $mapped['transfer_items'] = $transfer_items;
    $mapped['total_transfer_qty'] = $tr_qty;
    $mapped['total_transfer_gross_wt'] = number_format($tr_gwt, 3);
    $mapped['total_transfer_net_wt'] = number_format($tr_nwt, 3);
    $mapped['total_transfer_amount'] = moneyFormatIndia(number_format($tr_amt, 2, '.', ''));

    // -- Order / Advance Totals --
    $mapped['order_amount'] = moneyFormatIndia(number_format($billing['tot_bill_amount'], 2, '.', ''));
    $mapped['total_paid'] = moneyFormatIndia(number_format($billing['tot_amt_received'], 2, '.', ''));
    // balance_amount / due_amount are calculated later with credit-only logic (matching bill_format_2.php)

    // -- Payment Method Breakdown --
    // Aggregate payment amounts by mode from payment records
    $pay_by_mode = [];
    $payment_records = isset($data['payment']['pay_details']) ? $data['payment']['pay_details'] : [];
    if (!empty($payment_records)) {
        foreach ($payment_records as $pay) {
            $mode = strtoupper(trim($pay['payment_mode'] ?? ''));
            // For Net Banking, use the more specific transfer_type (UPI/NEFT/IMPS/RTGS)
            if ($mode === 'NB' && !empty($pay['transfer_type'])) {
                $mode = strtoupper(trim($pay['transfer_type']));
            }
            $amt = abs(floatval($pay['payment_amount'] ?? 0));
            if ($mode !== '' && $amt > 0) {
                if (!isset($pay_by_mode[$mode])) $pay_by_mode[$mode] = 0;
                $pay_by_mode[$mode] += $amt;
            }
        }
    }

    // Map mode codes to labels and template placeholders
    $mode_map = [
        'CASH' => ['label' => 'Cash',            'key' => 'pay_cash'],
        'CHQ'  => ['label' => 'Cheque',           'key' => 'pay_cheque'],
        'UPI'  => ['label' => 'UPI',              'key' => 'pay_upi'],
        'CC'   => ['label' => 'Credit Card',      'key' => 'pay_credit_card'],
        'DC'   => ['label' => 'Debit Card',       'key' => 'pay_debit_card'],
        'NB'   => ['label' => 'Net Banking',      'key' => 'pay_neft'],
        'NEFT' => ['label' => 'NEFT',             'key' => 'pay_neft'],
        'IMPS' => ['label' => 'IMPS',             'key' => 'pay_imps'],
        'RTGS' => ['label' => 'RTGS',             'key' => 'pay_rtgs'],
    ];

    // Set all payment placeholders (empty if no value)
    foreach ($mode_map as $code => $info) {
        $mapped[$info['key']] = '';
    }

    // Build payment_methods array — ONE ROW PER TRANSACTION (not grouped by mode)
    // Uses $payment_records (already fetched at line 1108) — each entry is one payment transaction.
    $payment_methods = [];
    foreach ($payment_records as $pay) {
        $raw_mode   = strtoupper(trim($pay['payment_mode'] ?? ''));
        $nb_type    = strtoupper($pay['NB_type'] ?? $pay['transfer_type'] ?? '');
        $amt        = abs(floatval($pay['payment_amount'] ?? 0));
        $pay_date   = $pay['payment_date'] ?? $pay['cheque_date'] ?? '';
        $appr_code  = $pay['payment_ref_number'] ?? '';
        $cheque_no  = $pay['cheque_no'] ?? '';
        $cheque_dt  = $pay['cheque_date'] ?? '';
        $bank_name  = $pay['bank_name'] ?? '';
        $card_no    = $pay['card_no'] ?? '';

        if ($amt <= 0) continue;

        // ── Reference string format per mode ──────────────────────────────
        if ($raw_mode === 'CHQ') {
            $label = 'Cheque';
            $ref_parts = [];
            if ($cheque_no) $ref_parts[] = 'Chq No: '   . $cheque_no;
            if ($cheque_dt) $ref_parts[] = 'Date: '     . $cheque_dt;
            $ref = implode(' / ', $ref_parts);
        } elseif ($raw_mode === 'DC') {
            $label = 'Debit Card';
            $ref_parts = [];
            if ($card_no)   $ref_parts[] = 'Card No: '  . $card_no;
            if ($appr_code) $ref_parts[] = 'Appr code: '. $appr_code;
            $ref = implode(' / ', $ref_parts);
        } elseif ($raw_mode === 'CC') {
            $label = 'Credit Card';
            $ref_parts = [];
            if ($card_no)   $ref_parts[] = 'Card No: '  . $card_no;
            if ($appr_code) $ref_parts[] = 'Appr code: '. $appr_code;
            $ref = implode(' / ', $ref_parts);
        } elseif ($raw_mode === 'NB') {
            $label = $nb_type !== '' ? $nb_type : 'Net Banking';
            $ref   = $appr_code ? 'Ref: ' . $appr_code : '';
        } else {
            $label = ucfirst(strtolower($raw_mode));
            $ref   = '';
        }

        $payment_methods[] = [
            'pay_method_label'  => $label,
            'pay_method_amount' => moneyFormatIndia(number_format($amt, 2, '.', '')),
            'pay_method_ref'    => $ref,            // combined formatted reference string

            // ── Individual variables (usable as separate template columns) ──
            'pay_mode_raw'      => $raw_mode,       // CHQ / DC / CC / NB / CASH
            // Cheque
            'pay_cheque_no'     => $cheque_no,      // e.g. 2201
            'pay_cheque_date'   => $cheque_dt,      // e.g. 27-05-2026
            'pay_bank_name'     => $bank_name,      // e.g. HDFC Bank
            // Card
            'pay_card_no'       => $card_no,        // e.g. 1234
            'pay_card_date'     => $pay_date,       // e.g. 27-05-2026
            'pay_appr_code'     => $appr_code,      // e.g. 909
            // Net banking / UPI
            'pay_nb_type'       => $nb_type,        // UPI / NEFT / IMPS / RTGS
            'pay_ref_number'    => $appr_code,      // alias for NB/UPI ref
            // Universal
            'pay_date'          => $pay_date,       // payment date for any mode
        ];

        // Also update aggregated scalar placeholders ({{pay_cash}}, {{pay_cheque}} etc.)
        $info2 = isset($mode_map[$raw_mode]) ? $mode_map[$raw_mode] : null;
        if ($info2) {
            $prev = floatval(str_replace(',', '', $mapped[$info2['key']] ?? '0'));
            $mapped[$info2['key']] = moneyFormatIndia(number_format($prev + $amt, 2, '.', ''));
        }
    }
    $mapped['payment_methods'] = $payment_methods;
    $mapped['has_payment_details'] = (count($payment_methods) > 0);


    // -- Totals (combined across all item types) --
    $mapped['total_qty'] = $total_pieces;
    $mapped['total_gross_wt'] = number_format($total_gross_wt, 3);
    $mapped['total_net_wt'] = number_format($total_net_wt, 3);
    // -- Sales-only totals (for Konva Sales table footer) --
    $mapped['sales_total_qty'] = $sales_total_pieces;
    $mapped['sales_total_gross_wt'] = number_format($sales_total_gross_wt, 3);
    $mapped['sales_total_net_wt'] = number_format($sales_total_net_wt, 3);
    $mapped['sales_sub_total'] = moneyFormatIndia(number_format($sales_sub_total, 2, '.', ''));
    // -- Return-only totals (for Konva Return table footer) --
    $mapped['return_total_qty'] = $return_total_pieces;
    $mapped['return_total_gross_wt'] = number_format($return_total_gross_wt, 3);
    $mapped['return_total_net_wt'] = number_format($return_total_net_wt, 3);
    $mapped['return_sub_total'] = moneyFormatIndia(number_format($return_sub_total, 2, '.', ''));
    
    $mapped['sub_total'] = moneyFormatIndia(number_format($total_taxable_amt, 2, '.', ''));
    $mapped['sgst_amount'] = moneyFormatIndia(number_format($total_sgst, 2, '.', ''));
    $mapped['cgst_amount'] = moneyFormatIndia(number_format($total_cgst, 2, '.', ''));
    $mapped['igst_amount'] = moneyFormatIndia(number_format($total_igst, 2, '.', ''));

    // Handle other charges
    $round_off = $billing['round_off_amt'];
    $handling_charges = $billing['handling_charges'] ?? 0;
    $mapped['round_off'] = moneyFormatIndia(number_format($round_off, 2, '.', ''));
    $mapped['handling_charges'] = moneyFormatIndia(number_format($handling_charges, 2, '.', ''));

    $grand_total = $total_taxable_amt + $total_sgst + $total_cgst + $total_igst + $round_off + $handling_charges;
    $mapped['grand_total'] = moneyFormatIndia(number_format($grand_total, 2, '.', ''));
    $mapped['remark'] = $billing['remark'] ?? '';

    // $mapped['grand_total'] = number_format($billing['tot_bill_amount'], 2);
    $mapped['amount_in_words'] = tpl_convert_number_to_words($billing['tot_bill_amount']);
    
    // Signature / Footer text
    $mapped['terms_condition'] = $billing['note'] ?? "Subject to Terms & Conditions"; 

    // -- Payment Details --
    $pay_cash = 0;
    $pay_card = 0;
    $pay_upi = 0;
    $pay_cheque = 0;
    $pay_neft = 0;
    $pay_rtgs = 0;
    $pay_imps = 0;
    
    // -- Detailed Payment Breakdown --
    $payment_breakdown = [];
    if (isset($data['payment']['pay_details']) && is_array($data['payment']['pay_details'])) {
        foreach ($data['payment']['pay_details'] as $pay) {
            $mode_label = strtoupper($pay['payment_mode']);
            $ref_info = '';

            if ($mode_label == 'CHQ') {
                $mode_label = 'CHEQUE';
                $ref_info = ($pay['cheque_no'] ? 'Chq No:' . $pay['cheque_no'] : '') . 
                            ($pay['cheque_date'] ? ' Dt:' . $pay['cheque_date'] : '') . 
                            ($pay['bank_name'] ? ' Bank:' . $pay['bank_name'] : '');
            } elseif ($mode_label == 'DC') {
                $mode_label = 'DEBIT CARD';
                $ref_info = ($pay['card_no'] ? 'Ref:' . $pay['card_no'] : '');
            } elseif ($mode_label == 'CC') {
                $mode_label = 'CREDIT CARD';
                $ref_info = ($pay['card_no'] ? 'Ref:' . $pay['card_no'] : '');
            } elseif ($mode_label == 'NB') {
                 $mode_label = strtoupper($pay['transfer_type'] ?? 'NET BANKING');
                 $ref_info = ($pay['payment_ref_number'] ? 'Ref:' . $pay['payment_ref_number'] : '');
            }

            $payment_breakdown[] = [
                'mode' => $mode_label,
                'amount' => moneyFormatIndia(number_format($pay['payment_amount'], 2, '.', '')),
                'reference' => $ref_info,
                'card_no' => $pay['card_no'] ?? '',
                'card_date' => $pay['payment_date'] ?? '',
                'appr_code' => $pay['payment_ref_number'] ?? ''
            ];
            
            // Aggregates (existing logic preserved)
            $mode = strtoupper($pay['payment_mode']);
            $type = strtoupper($pay['NB_type'] ?? $pay['transfer_type'] ?? '');
            $amt = $pay['payment_amount'];
            
            if ($mode == 'CASH') $pay_cash += $amt;
            elseif ($mode == 'CHQ') $pay_cheque += $amt;
            elseif ($mode == 'DC' || $mode == 'CC') $pay_card += $amt;
            elseif ($mode == 'NB') {
                if ($type == 'UPI') $pay_upi += $amt;
                elseif ($type == 'NEFT') $pay_neft += $amt;
                elseif ($type == 'RTGS') $pay_rtgs += $amt;
                elseif ($type == 'IMPS') $pay_imps += $amt;
                else $pay_imps += $amt; // Fallback
            }
        }
    }
    
    // Due Amount — bill_format_2.php lines 350-367: only for credit sales (is_credit==1) or credit collection (bill_type==8)
    $due_amt = 0;
    if ($billing['bill_type'] == 8) {
        $due_amt = $billing['tot_bill_amount'] - $billing['tot_amt_received'];
    } else if (!empty($billing['is_credit']) && $billing['is_credit'] == 1) {
        $due_amt = $billing['tot_bill_amount'] - $billing['tot_amt_received'];
    }
    // Advance Deposit — bill_format_2.php line 3097: if ($billing['advance_deposit'] != 0)
    $adv_dep = isset($billing['advance_deposit']) ? floatval($billing['advance_deposit']) : 0;
    if ($adv_dep != 0) {
        $payment_breakdown[] = [
            'mode' => 'ADVANCE',
            'amount' => moneyFormatIndia(number_format($adv_dep, 2, '.', '')),
            'reference' => ''
        ];
    }

    // Advance Adjustment — bill_format_2.php line 2905: $billing['adv_adj_amt']
    $adv_adj_amt = isset($billing['adv_adj_amt']) ? floatval($billing['adv_adj_amt']) : 0;
    if ($adv_adj_amt > 0) {
        $payment_breakdown[] = [
            'mode' => 'ADVANCE ADJ',
            'amount' => moneyFormatIndia(number_format($adv_adj_amt, 2, '.', '')),
            'reference' => ''
        ];
    }

    // Receipt Advance Adjustment — bill_format_2.php line 370-376 ($adj_amt from receiptDetails)
    // Sum of adjuseted_amt from all receipt details entries
    $receipt_adv_adj_total = 0;
    if (!empty($data['receiptDetails'])) {
        foreach ($data['receiptDetails'] as $rrow) {
            $receipt_adv_adj_total += floatval($rrow['adjuseted_amt'] ?? 0);
        }
    }
    if ($receipt_adv_adj_total > 0) {
        $payment_breakdown[] = [
            'mode' => 'ADVANCE ADJUSTMENT',
            'amount' => moneyFormatIndia(number_format($receipt_adv_adj_total, 2, '.', '')),
            'reference' => ''
        ];
    }

    if ($due_amt > 0) {
        $payment_breakdown[] = [
            'mode' => 'DUE AMOUNT',
            'amount' => moneyFormatIndia(number_format($due_amt, 2, '.', '')),
            'reference' => 'Due Date: ' . ($billing['credit_due_date'] ?? '-')
        ];
    }
    
    $mapped['payment_breakdown'] = $payment_breakdown;
    
    $mapped['pay_cash'] = moneyFormatIndia(number_format($pay_cash, 2, '.', ''));
    $mapped['pay_card'] = moneyFormatIndia(number_format($pay_card, 2, '.', ''));
    $mapped['pay_upi'] = moneyFormatIndia(number_format($pay_upi, 2, '.', ''));
    $mapped['pay_cheque'] = moneyFormatIndia(number_format($pay_cheque, 2, '.', ''));
    $mapped['pay_neft'] = moneyFormatIndia(number_format($pay_neft, 2, '.', ''));
    $mapped['pay_rtgs'] = moneyFormatIndia(number_format($pay_rtgs, 2, '.', ''));
    $mapped['pay_imps'] = moneyFormatIndia(number_format($pay_imps, 2, '.', ''));
    
    $mapped['pay_due'] = ($due_amt > 0) ? moneyFormatIndia(number_format($due_amt, 2, '.', '')) : '';

    // Advance Adjustment standalone variables for direct template use
    $adv_adj_amt_val = isset($billing['adv_adj_amt']) ? floatval($billing['adv_adj_amt']) : 0;
    $mapped['pay_advance_adj'] = ($adv_adj_amt_val > 0) ? moneyFormatIndia(number_format($adv_adj_amt_val, 2, '.', '')) : '';
    
    // Receipt-based advance adjustment (sum of adjuseted_amt from receiptDetails)
    $rcpt_adj_sum = 0;
    if (!empty($data['receiptDetails'])) {
        foreach ($data['receiptDetails'] as $rr) {
            $rcpt_adj_sum += floatval($rr['adjuseted_amt'] ?? 0);
        }
    }
    $mapped['pay_advance_adjustment'] = ($rcpt_adj_sum > 0) ? moneyFormatIndia(number_format($rcpt_adj_sum, 2, '.', '')) : '';
    $mapped['advance_adjustment'] = ($rcpt_adj_sum > 0) ? moneyFormatIndia(number_format($rcpt_adj_sum, 2, '.', '')) : '';

    // -- Card Details Mapping (Top-level placeholders) --
    $mapped['card_no'] = '';
    $mapped['card_date'] = '';
    $mapped['appr_code'] = '';
    foreach ($payment_breakdown as $pb) {
        if (($pb['mode'] == 'DEBIT CARD' || $pb['mode'] == 'CREDIT CARD') && empty($mapped['card_no'])) {
            $mapped['card_no'] = $pb['card_no'];
            $mapped['card_date'] = $pb['card_date'];
            $mapped['c_card_date'] = $pb['card_date']; // User specific placeholder
            $mapped['appr_code'] = $pb['appr_code'];
        }
    }

    // Paid and Balance amounts
    $mapped['paid_amount'] = moneyFormatIndia(number_format($billing['tot_amt_received'], 2, '.', ''));
    
    // Due/Balance Amount — bill_format_2.php lines 350-367:
    // Only calculated when is_credit == 1 (credit sale). Otherwise stays 0.
    $due_amount = 0;
    if ($billing['bill_type'] == 8) {
        // Credit Collection — always has due amount
        $due_amount = $billing['tot_bill_amount'] - $billing['tot_amt_received'];
    } else if ($billing['bill_type'] == 5) {
        // Order Receipt — use grand_total since tot_bill_amount is 0
        $due_amount = $grand_total - $billing['tot_amt_received'];
    } else {
        // All other bill types — only show due when it's a credit sale
        if (!empty($billing['is_credit']) && $billing['is_credit'] == 1) {
            $due_amount = $billing['tot_bill_amount'] - $billing['tot_amt_received'];
        }
    }
    $mapped['balance_amount'] = ($due_amount > 0) ? moneyFormatIndia(number_format($due_amount, 2, '.', '')) : '';
    $mapped['due_amount'] = ($due_amount > 0) ? moneyFormatIndia(number_format($due_amount, 2, '.', '')) : '';

    // -- Order Delivery Specifics --
    $mapped['order_delivery_items'] = $sales_items; // Reuse sales items (contains MC)
    
    // Map Advance Details (History of advances utilized)
    $advance_details = [];
    $total_advance_adjusted = 0;

    // 1. advance_deposit from billing table — bill_format_2.php line 3097
    $adv_dep_val = isset($billing['advance_deposit']) ? floatval($billing['advance_deposit']) : 0;
    if ($adv_dep_val != 0) {
        $advance_details[] = [
            'date'   => date('d-m-Y', strtotime($billing['bill_date'])),
            'amount' => moneyFormatIndia(number_format($adv_dep_val, 2, '.', ''))
        ];
        $total_advance_adjusted += $adv_dep_val;
    }

    // 2. adv_adj_amt from billing table (general advance adjustment)
    $adv_adj_val = isset($billing['adv_adj_amt']) ? floatval($billing['adv_adj_amt']) : 0;
    if ($adv_adj_val > 0) {
        $advance_details[] = [
            'date'   => date('d-m-Y', strtotime($billing['bill_date'])),
            'amount' => moneyFormatIndia(number_format($adv_adj_val, 2, '.', ''))
        ];
        $total_advance_adjusted += $adv_adj_val;
    }

    // 3. receiptDetails — historical advance adjustments from get_billing_advance_details()
    if (!empty($data['receiptDetails'])) {
        foreach ($data['receiptDetails'] as $row) {
            $amt = $row['adjuseted_amt'];
            $advance_details[] = [
                'date' => $row['bill_date'],
                'amount' => moneyFormatIndia(number_format($amt, 2, '.', ''))
            ];
            $total_advance_adjusted += $amt;
        }
    }
    $mapped['advance_details'] = $advance_details;
    $mapped['order_advance_adj'] = moneyFormatIndia(number_format($total_advance_adjusted, 2, '.', ''));
    
    // Net Cash Received (Total Paid - Advance Adjusted)
    // Assuming tot_amt_received includes the utilized advance amount (which is standard in many billing systems)
    // If tot_amt_received is PURELY the new payment, then this logic might need adjustment.
    // However, usually 'Total Paid' = Sum of all receipts (Cash + Card + Adv Adj).
    // So New Cash = Total Paid - Adv Adj.
    $cash_received_val = $billing['tot_amt_received'] - $total_advance_adjusted;
    $mapped['cash_received'] = moneyFormatIndia(number_format($cash_received_val, 2, '.', ''));

    // -- Exchange Amount (alias for old metal total — legacy compatibility) --
    $mapped['exchange_amount'] = moneyFormatIndia(number_format($om_amt, 2, '.', ''));



    // -- Credit Collection Specifics --
    if ($billing['bill_type'] == 8) {
        $CI = &get_instance();
        $credit_hist = $CI->ret_billing_model->get_credit_history_for_print($billing['bill_id']);
        
        $history_lines = [];
        $balance = 0;

        if (!empty($credit_hist)) {
            $orig = $credit_hist['original'];
            
            // Format Original Ref No
            // Assuming branch_code logic needs to be replicated or simplified.
            // Using a simple guess for now or fetching branch code if possible.
            // Actually, we can just use the sales_ref_no if available or construct it.
            // Let's use the helper's existing $invoice_no logic if applicable, but for the REFERENCE bill.
            // Accessing branch/fin_year from the original bill data.
            // For simplicity, just using sales_ref_no directly or a simple format.
            $orig_ref = $orig['sales_ref_no'];  // Simplification, ideally should perform the LMX-.. formatting
            
            // Opening Entry
            $opening_amt = $orig['tot_bill_amount'];
            $balance = $opening_amt;
            
            $history_lines[] = [
                'label' => 'Opening Credit Ref Bill No. ' . $orig_ref,
                'date' => date('d-m-Y', strtotime($orig['bill_date'])),
                'amount' => moneyFormatIndia(number_format($opening_amt, 2, '.', ''))
            ];

            // Collections
            if (!empty($credit_hist['collections'])) {
                foreach ($credit_hist['collections'] as $coll) {
                    $amt = $coll['tot_amt_received'];
                    $balance -= $amt;
                    $history_lines[] = [
                        'label' => 'Credit Collection Ref Bill No. ' . $coll['credit_coll_refno'],
                        'date' => date('d-m-Y', strtotime($coll['bill_date'])),
                        'amount' => moneyFormatIndia(number_format($amt, 2, '.', ''))
                    ];
                }
            }
            
            // Add reference details for the MAIN table row
            $mapped['credit_ref_bill_no'] = $orig_ref;
            $mapped['credit_ref_bill_date'] = date('d-m-Y', strtotime($orig['bill_date']));
        }
        
        $mapped['credit_collection_history'] = $history_lines;
        $mapped['credit_balance_amount'] = moneyFormatIndia(number_format($balance, 2, '.', ''));
    }

    // -- Aliases for Print Designer Template Compatibility --

    // -- Aliases for Print Designer Template Compatibility --
    $mapped['branch_address'] = $mapped['company_address'];
    $mapped['branch_phone'] = $mapped['company_mobile'];
    $mapped['branch_gstin'] = $mapped['company_gstin'];
    
    $mapped['bill_no'] = $mapped['invoice_no'];
    
    $mapped['subtotal'] = $mapped['sub_total'];
    $mapped['cgst'] = $mapped['cgst_amount'];
    $mapped['sgst'] = $mapped['sgst_amount'];
    $mapped['igst'] = $mapped['igst_amount']; // Just in case



    // -- Purchase Bill Specifics --
    $purchase_items = [];
    $total_purchase_amt = 0;
    if (!empty($items_data['old_matel_details'])) {
        $sno = 1;
        foreach ($items_data['old_matel_details'] as $item) {
            $amt = $item['amount'];
            $total_purchase_amt += $amt;
            
            $desc = $item['metal_name'];
            if (!empty($item['old_metal_type'])) {
                $desc .= ' ' . $item['old_metal_type'];
            }

            $purchase_items[] = [
                'purchase_sno' => $sno++,
                'purchase_description' => $desc,
                'purchase_qty' => $item['piece'],
                'purchase_hsn_code' => '',
                'purchase_gross_wt' => number_format($item['gross_wt'], 3),
                'purchase_stn_less' => number_format($item['stone_wt'], 3),
                'purchase_mtl_less' => number_format($item['dust_wt'], 3),
                'purchase_net_wt' => number_format($item['net_wt'], 3),
                'purchase_rate' => moneyFormatIndia(number_format($item['rate_per_gram'], 2, '.', '')),
                'purchase_amount' => moneyFormatIndia(number_format($amt, 2, '.', ''))
            ];
        }
    }
    $mapped['total_old_qty'] = $items_data['total_old_qty'];
    $mapped['total_old_gross_wt'] = $items_data['total_old_gross_wt'];
    $mapped['total_old_net_wt'] = $items_data['total_old_net_wt'];
    $mapped['total_old_amount'] = $items_data['total_old_amount'];
    $mapped['purchase_items'] = $purchase_items;
    $mapped['purchase_total_amount'] = moneyFormatIndia(number_format($total_purchase_amt, 2, '.', ''));
    // Cash Paid for purchase is typically the total amount received/paid out.
    // In Purchase bills, tot_amt_received is often used to track what we paid the customer.
    // If it's negative or positive depends on implementation, usually positive in the field but logically out.
    // We'll use the mapped total_paid or just reuse total_purchase_amt if full payment is assumed, 
    // but better to use the actual payment record.
    $mapped['purchase_cash_paid'] = moneyFormatIndia(number_format($billing['tot_amt_received'], 2, '.', ''));

    // -- Sales & Purchase Combined Logic --
    // Sales Amount is essentially the Grand Total of the sales bill
    $mapped['sales_total_amount'] = $mapped['grand_total']; 
    
    // Purchase Amount is total_purchase_amt calculated above
    
    // Net Amount
    // If bill type has exchange, usually 'tot_amt_received' is the final amount paid by customer.
    // But we might want to show the specific calculation: Sales - Purchase.
    $net_amount_val = $billing['tot_bill_amount'] - $total_purchase_amt; 
    // Note: tot_bill_amount in DB is usually the final bill amount after additions/deductions. 
    // If this is a Sales Bill, tot_bill_amount is the Sales Total.
    // Let's rely on the explicit difference for display transparency.
    $sales_val = $billing['tot_bill_amount']; 
    // net_amount will be calculated after return items are processed (below)
    $mapped['net_amount'] = '0.00'; // placeholder, recalculated after return loop
    
    // Purchase Invoice No — only when bill actually has a purchase ref number
    // bill_format_2.php only shows PU invoice when old_matel_details > 0
    if (!empty($billing['pur_ref_no'])) {
        // Use the pre-formatted purchase_ref_no from model (CONCAT(branch,fin_year,'-',pur_ref_no))
        // or fall back to get_bill_no_format_detail for the configured format
        if (!empty($billing['purchase_ref_no'])) {
            $mapped['purchase_invoice_no'] = $billing['purchase_ref_no'];
        } else {
            $CI_pur = &get_instance();
            $mapped['purchase_invoice_no'] = $CI_pur->ret_billing_model->get_bill_no_format_detail($billing['bill_id'], 'p');
        }
    }

    // -- Chit Pre Close Specifics --
    $chit_pre_close_items = [];
    $chit_pre_close_total = 0;
    
    // Check if chit_details exists in items_data (passed from controller/model)
    // If not found, check if it's in a different key or needs to be fetched.
    // Only populate pre-close details if this is a chit pre-close bill (bill_type == 10)
    if ($billing['bill_type'] == 10 && !empty($items_data['chit_details'])) {
        $sno = 1;
        foreach ($items_data['chit_details'] as $item) {
            $amt = isset($item['amount']) ? $item['amount'] : (isset($item['utilized_amt']) ? $item['utilized_amt'] : 0);
            $chit_pre_close_total += $amt;
            
            $ref = isset($item['chit_ref_no']) ? $item['chit_ref_no'] : (isset($item['ref_no']) ? $item['ref_no'] : '-');
            
            $chit_pre_close_items[] = [
                'sno' => $sno++,
                'ref_no' => $ref,
                'amount' => moneyFormatIndia(number_format($amt, 2, '.', ''))
            ];
        }
    }
    
    $mapped['chit_pre_close_items'] = $chit_pre_close_items;
    $mapped['chit_pre_close_total'] = moneyFormatIndia(number_format($chit_pre_close_total, 2, '.', ''));

    // -- Sales Return Specifics --
    $sales_return_items = [];
    $sales_return_total = 0;
    
    // Check for return_details (from ret_bill_return_details linkage or fallback from item_details)
    // Skip if sales_ret_trans_details already populated return_items (prevents double-counting)
    if (!empty($items_data['return_details']) && empty($return_items)) {
         $sno = count($return_items) + 1; // Continue numbering from items already added
         foreach ($items_data['return_details'] as $item) {
             // Use field names that match the item_details format from the model
             $qty = isset($item['piece']) ? $item['piece'] : (isset($item['quantity']) ? $item['quantity'] : 0);
             $gross_wt = isset($item['gross_wt']) ? $item['gross_wt'] : 0;
             $net_wt = isset($item['net_wt']) ? $item['net_wt'] : 0;
             $rate = isset($item['rate_per_grm']) ? $item['rate_per_grm'] : (isset($item['rate']) ? $item['rate'] : 0);
             $item_cost = isset($item['item_cost']) ? $item['item_cost'] : (isset($item['total_amount']) ? $item['total_amount'] : 0);
             $item_total_tax = isset($item['item_total_tax']) ? $item['item_total_tax'] : 0;
             $amount = $item_cost - $item_total_tax;
             
             // Per-item tax values
             $item_sgst = $item['total_sgst'] ?? 0;
             $item_cgst = $item['total_cgst'] ?? 0;
             $item_igst = $item['total_igst'] ?? 0;
             
             // Description logic
             $desc = isset($item['product_name']) ? $item['product_name'] : '';
             if (isset($item['size_name']) && $item['size_name']) {
                 $desc .= '-' . $item['size_name'];
             }
             
             $sales_return_total += $amount;

             $return_data = [
                 'type' => 'Return',
                 // Unprefixed keys — V2 Konva table columns
                 'sno' => $sno++,
                 'description' => $desc,
                 'hsn_code' => isset($item['hsn_code']) ? $item['hsn_code'] : '',
                 'qty' => $qty,
                 'gross_wt' => number_format($gross_wt, 3),
                 'net_wt' => number_format($net_wt, 3),
                 'va_percent' => 0,
                 'va_content' => '-',
                 'mc' => 0,
                 'rate' => moneyFormatIndia(number_format($rate, 2, '.', '')),
                 'amount' => moneyFormatIndia(number_format($amount, 2, '.', '')),
                 'taxable_amount' => moneyFormatIndia(number_format($amount, 2, '.', '')),
                 'cgst_amt' => moneyFormatIndia(number_format($item_cgst, 2, '.', '')),
                 'sgst_amt' => moneyFormatIndia(number_format($item_sgst, 2, '.', '')),
                 'igst_amt' => moneyFormatIndia(number_format($item_igst, 2, '.', '')),
                 // Prefixed keys — backward compatibility
                 'return_sno' => $sno - 1,
                 'return_description' => $desc,
                 'return_hsn_code' => isset($item['hsn_code']) ? $item['hsn_code'] : '',
                 'return_qty' => $qty,
                 'return_gross_wt' => number_format($gross_wt, 3),
                 'return_net_wt' => number_format($net_wt, 3),
                 'return_va_percent' => 0,
                 'return_va_content' => '-',
                 'return_mc' => 0,
                 'return_rate' => moneyFormatIndia(number_format($rate, 2, '.', '')),
                 'return_amount' => moneyFormatIndia(number_format($amount, 2, '.', '')),
                 'return_taxable_amount' => moneyFormatIndia(number_format($amount, 2, '.', '')),
                 'return_total_amount' => moneyFormatIndia(number_format($item_cost, 2, '.', '')),
                 'return_cgst_amt' => moneyFormatIndia(number_format($item_cgst, 2, '.', '')),
                 'return_sgst_amt' => moneyFormatIndia(number_format($item_sgst, 2, '.', '')),
                 'return_igst_amt' => moneyFormatIndia(number_format($item_igst, 2, '.', '')),
                 // Stone aggregate variables
                 'stone_wt' => number_format($item['stn_wgt'] ?? $item['stone_wt'] ?? 0, 3),
                 'stone_amount' => moneyFormatIndia(number_format($item['stone_price'] ?? $item['stone_amount'] ?? 0, 2, '.', '')),
                 'return_stone_wt' => number_format($item['stn_wgt'] ?? $item['stone_wt'] ?? 0, 3),
                 'return_stone_amount' => moneyFormatIndia(number_format($item['stone_price'] ?? $item['stone_amount'] ?? 0, 2, '.', '')),
             ];

             // Build nested _sub_rows from rtn_stone_details for return items
             $rtn_sub_rows = [];
             $rtn_stones = $item['rtn_stone_details'] ?? $item['stone_details'] ?? [];
             if (!empty($rtn_stones)) {
                 foreach ($rtn_stones as $stone) {
                     $s_pcs  = $stone['pieces'] ?? 0;
                     $s_name = $stone['stone_name'] ?? 'Stone';
                     $s_wt   = number_format($stone['wt'] ?? 0, 3);
                     $s_uom  = $stone['uom_short_code'] ?? '';
                     $s_rate = number_format($stone['rate_per_gram'] ?? 0, 2, '.', '');
                     $s_amt  = number_format($stone['price'] ?? $stone['amount'] ?? 0, 2, '.', '');

                     $stone_desc = $s_pcs . ' Pcs ' . $s_name . ' ' . $s_wt . ' ' . $s_uom
                                 . ' x ' . moneyFormatIndia($s_rate) . '=' . moneyFormatIndia($s_amt);

                     $rtn_sub_rows[] = [
                         'return_stone_name'        => $s_name,
                         'return_stone_pieces'      => $s_pcs,
                         'return_stone_wt'          => $s_wt,
                         'return_stone_rate'        => moneyFormatIndia($s_rate),
                         'return_stone_amount'      => moneyFormatIndia($s_amt),
                         'return_stone_uom'         => $s_uom,
                         'return_stone_cal_type'    => $stone['stone_cal_type'] ?? '',
                         'return_stone_type'        => $stone['stone_type'] ?? '',
                         'return_stone_description' => $stone_desc,
                     ];
                 }
             }
             $return_data['_sub_rows'] = $rtn_sub_rows;

             $sales_return_items[] = $return_data;
             // Also add to the main return_items array if not already there
             $return_items[] = $return_data;
             
             // Accumulate tax totals from return items
             $total_sgst += $item_sgst;
             $total_cgst += $item_cgst;
             $total_igst += $item_igst;
             $total_taxable_amt += $amount;
             $total_gross_wt += $gross_wt;
             $total_net_wt += $net_wt;
             $total_pieces += $qty;
         }
    }
    
    $mapped['sales_return_items'] = $sales_return_items;
    $mapped['sales_return_total'] = moneyFormatIndia(number_format($sales_return_total, 2, '.', ''));
    
    // Update return_items and return_details with any newly added items
    $mapped['return_items'] = $return_items;
    $mapped['return_details'] = $return_items; // Alias for print template {{#return_details}} block
    
    // Recalculate return totals with all return items (including GST)
    $ret_qty = 0; $ret_gwt = 0; $ret_nwt = 0; $ret_amt = 0;
    $ret_cgst = 0; $ret_sgst = 0; $ret_igst = 0; $ret_amt_with_gst = 0;
    foreach ($return_items as $item) {
        $ret_qty += $item['return_qty'];
        $ret_gwt += (float)$item['return_gross_wt'];
        $ret_nwt += (float)$item['return_net_wt'];
        $ret_amt += (float)str_replace(',','',$item['return_amount']);
        $ret_cgst += (float)str_replace(',','',$item['return_cgst_amt']);
        $ret_sgst += (float)str_replace(',','',$item['return_sgst_amt']);
        $ret_igst += (float)str_replace(',','',$item['return_igst_amt']);
        $ret_amt_with_gst += (float)str_replace(',','',$item['return_total_amount'] ?? $item['return_amount']);
    }
    $mapped['total_return_qty'] = $ret_qty;
    $mapped['total_return_gross_wt'] = number_format($ret_gwt, 3);
    $mapped['total_return_net_wt'] = number_format($ret_nwt, 3);
    $mapped['total_return_amount'] = moneyFormatIndia(number_format($ret_amt, 2, '.', ''));
    $mapped['total_return_cgst'] = moneyFormatIndia(number_format($ret_cgst, 2, '.', ''));
    $mapped['total_return_sgst'] = moneyFormatIndia(number_format($ret_sgst, 2, '.', ''));
    $mapped['total_return_igst'] = moneyFormatIndia(number_format($ret_igst, 2, '.', ''));
    $mapped['total_return_tax'] = moneyFormatIndia(number_format($ret_cgst + $ret_sgst + $ret_igst, 2, '.', ''));
    $mapped['total_return_amount_with_gst'] = moneyFormatIndia(number_format($ret_amt_with_gst, 2, '.', ''));
    $mapped['sales_return_total'] = moneyFormatIndia(number_format($sales_return_total, 2, '.', ''));
    
    // Re-assign tax totals after return_details processing
    // (return_details items accumulate tax AFTER the initial assignment at line ~692)
    $mapped['sub_total'] = moneyFormatIndia(number_format($total_taxable_amt, 2, '.', ''));
    $mapped['sgst_amount'] = moneyFormatIndia(number_format($total_sgst, 2, '.', ''));
    $mapped['cgst_amount'] = moneyFormatIndia(number_format($total_cgst, 2, '.', ''));
    $mapped['igst_amount'] = moneyFormatIndia(number_format($total_igst, 2, '.', ''));
    $mapped['total_qty'] = $total_pieces;
    $mapped['total_gross_wt'] = number_format($total_gross_wt, 3);
    $mapped['total_net_wt'] = number_format($total_net_wt, 3);
    

    // Net Amount Calculation for "Sales Return Invoice"
    // Sales Amount - Exchange Amount
    // We need 'sales_total_amount' if not already present. 'total_amount' is likely the BILL total.
    // If it's a "Sales Return Invoice", the main bill might be the sales bill, and return items are subtracted?
    // Or is it a separate return bill? The image says "SALES RETURN INVOICE NO...".
    // And "EXCHANGE (REFER SALE BILL NO...)".
    // It seems to be a Sales Bill where items are SOLD (top table) and items are RETURNED/EXCHANGED (bottom table).
    // So 'total_amount' of the bill (view) effectively might be (Sales - Return).
    // Let's create specific keys for the summary block in the image.
    
    $sales_amt = 0;
    $sales_qty = 0;
    $sales_gross_wt = 0;
    $sales_net_wt = 0;
    $calc_sales_total = 0;
    
    // Calculate sales amount from 'items' explicitly
    if (!empty($mapped['items'])) {
        foreach($mapped['items'] as $m_item) {
             $sales_qty += (float)$m_item['qty'];
             $sales_gross_wt += (float)$m_item['gross_wt'];
             $sales_net_wt += (float)$m_item['net_wt'];
             $calc_sales_total += (float)str_replace(',', '', $m_item['amount']);
        }
    }
    
    $mapped['total_sales_qty'] = $sales_qty;
    $mapped['total_sales_gross_wt'] = number_format($sales_gross_wt, 3);
    $mapped['total_sales_net_wt'] = number_format($sales_net_wt, 3);
    $mapped['total_sales_amount'] = moneyFormatIndia(number_format($grand_total, 2, '.', ''));
    
    $mapped['lbl_sales_amount'] = ($grand_total > 0) ? moneyFormatIndia(number_format($grand_total, 2, '.', '')) : '';
    $mapped['lbl_exchange_amount'] = ($ret_amt_with_gst > 0) ? moneyFormatIndia(number_format($ret_amt_with_gst, 2, '.', '')) : '';
    $mapped['lbl_return_amount'] = ($ret_amt > 0) ? moneyFormatIndia(number_format($ret_amt, 2, '.', '')) : '';
    
    // Recalculate net_amount now that $ret_amt is available
    // bill_format_2.php line 2205: only shown when $tot_sales_amt > 0
    // bill_format_2.php line 2211: ($tot_sales_amt + handling_charges) - $total_return - $pur_total_amt + $round_off - $return_round_off_amt
    $return_round_off_amt = isset($billing['return_round_off_amt']) ? floatval($billing['return_round_off_amt']) : 0;
    
    // NOTE: For bill_type 7 (Sales Return), return items are copied into item_details at line ~455,
    // so $grand_total and item counts are unreliable for detecting "no sales". Use bill_type directly.
    if ($billing['bill_type'] == 7) {
        // Pure Sales Return — no net amount, all return totals = full bill amount with tax
        $mapped['net_amount'] = '';
        $mapped['sales_return_total'] = moneyFormatIndia(number_format($billing['tot_bill_amount'], 2, '.', ''));
        $mapped['total_return_amount'] = moneyFormatIndia(number_format($billing['tot_bill_amount'], 2, '.', ''));
        $mapped['total_return_amount_with_gst'] = moneyFormatIndia(number_format($billing['tot_bill_amount'], 2, '.', ''));
    } else if ($ret_amt > 0 && $grand_total > 0) {
        // Combined bill (sales + returns): Net = Sales - Returns - Purchase + round_off
        $net_amount_val = ($grand_total + $handling_charges) - $ret_amt - $total_purchase_amt + $round_off - $return_round_off_amt;
        $mapped['net_amount'] = moneyFormatIndia(number_format($net_amount_val, 2, '.', ''));
    } else if ($grand_total > 0) {
        // Sales only: net = grand_total - purchase
        $net_amount_val = $grand_total - $total_purchase_amt;
        $mapped['net_amount'] = moneyFormatIndia(number_format($net_amount_val, 2, '.', ''));
    } else {
        $mapped['net_amount'] = '';
    }
    
    $net_amt = $calc_sales_total - $sales_return_total;
    // If tax is involved, it complicates. The image shows "Total" 82,143 (Sales)
    // Then Exchange 47,792.
    // Net Amount 34,350.
    // 82143 - 47792 = 34351. Matches roughly.
    $mapped['lbl_net_amount'] = moneyFormatIndia(number_format($net_amt, 2, '.', ''));
    
    // Sales Return Invoice No — formatted via get_bill_no_format_detail like bill_format_2.php line 1057
    // This produces the full formatted number e.g. "BRANCH-FY-SR-123"
    if (!empty($billing['s_ret_refno'])) {
        $CI_ref = &get_instance();
        $mapped['s_ret_invoice_no'] = $CI_ref->ret_billing_model->get_bill_no_format_detail($billing['bill_id'], 'sr');
    }

    // Exchange Reference Bill No — the original sale bill that the exchange refers to
    // Matches bill_format_2.php line 1087: get_bill_no_format_detail(return_details[0]['ret_bill_id'])
    // Use $original_return_details (captured before any clearing at line ~430) as primary source
    $ret_source = !empty($original_return_details) ? $original_return_details
                : (!empty($items_data['return_details']) ? $items_data['return_details'] : []);

    if (!empty($ret_source) && !empty($ret_source[0]['ret_bill_id'])) {
        $CI_ref = &get_instance();
        $mapped['exchange_ref_bill_no'] = $CI_ref->ret_billing_model->get_bill_no_format_detail($ret_source[0]['ret_bill_id']);
    } elseif (!empty($items_data['sales_ret_trans_details']) && !empty($items_data['sales_ret_trans_details'][0]['ret_bill_id'])) {
        $CI_ref = &get_instance();
        $mapped['exchange_ref_bill_no'] = $CI_ref->ret_billing_model->get_bill_no_format_detail($items_data['sales_ret_trans_details'][0]['ret_bill_id']);
    } elseif (!empty($billing['ref_bill_id'])) {
        // Fallback: bill_type 7 (Sales Return) — return items are in item_details, not return_details
        // Use billing['ref_bill_id'] which the model sets at line 933 for all referenced bills
        $CI_ref = &get_instance();
        $mapped['exchange_ref_bill_no'] = $CI_ref->ret_billing_model->get_bill_no_format_detail($billing['ref_bill_id']);
    }

    // Raw return reference fields from the model query (ref_bill.sales_ref_no, ref_bill.bill_date)
    // Available as simpler fallback variables without needing get_bill_no_format_detail
    if (!empty($ret_source[0]['ref_bill_no'])) {
        $mapped['return_ref_bill_no'] = $ret_source[0]['ref_bill_no'];
    } elseif (!empty($billing['ref_bill_no'])) {
        $mapped['return_ref_bill_no'] = $billing['ref_bill_no'];
    }
    if (!empty($ret_source[0]['ref_bill_date'])) {
        $mapped['return_ref_bill_date'] = $ret_source[0]['ref_bill_date'];
    }

    // Composite line: "Exchange (Refer sale bill no : LMX-25-SA-00015)" — bill_format_2.php line 1085
    if (!empty($mapped['exchange_ref_bill_no'])) {
        $mapped['exchange_ref_line'] = 'Exchange (Refer sale bill no : ' . $mapped['exchange_ref_bill_no'] . ')';
    } else {
        $mapped['exchange_ref_line'] = '';
    }

    // -- Repair Order Details --
    $repair_order_items = [];
    $total_repair_amount = 0;
    if (!empty($items_data['repair_order_details'])) {
        $sno = 1;
        foreach ($items_data['repair_order_details'] as $item) {
             $repair_order_items[] = [
                'sno' => $sno++,
                'description' => $item['product_name'],
                'repair_wt' => $item['weight'],
                'completed_wt' => $item['completed_weight'],
                'rate' => moneyFormatIndia(number_format($item['rate'], 2, '.', '')),
                'amount' => moneyFormatIndia(number_format($item['rate'] - $item['repair_tot_tax'], 2, '.', '')) // Taxable? Or total? View uses rate - tax
             ];
             $total_repair_amount += ($item['rate'] - $item['repair_tot_tax']);
        }
    }
    $mapped['repair_order_items'] = $repair_order_items;
    $mapped['repair_order_total'] = moneyFormatIndia(number_format($total_repair_amount, 2, '.', ''));

    // -- Order Advance Adjustments (Order Adj) --
    $order_advance_entries = [];
    $order_advance_total = 0;
    if (!empty($items_data['order_adj'])) {
        foreach ($items_data['order_adj'] as $ord) {
            $amt = ($ord['store_as'] == 1 ? $ord['advance_amount'] : ($ord['received_weight'] * $ord['rate_per_gram']));
            $wt = ($ord['store_as'] == 1 ? ($ord['advance_amount'] / $ord['rate_per_gram']) : $ord['received_weight']);
            
            if ($amt > 0 || $wt > 0) {
                $order_advance_entries[] = [
                    'date' => $ord['bill_date'],
                    'rate' => moneyFormatIndia($ord['rate_per_gram']),
                    'weight' => number_format($wt, 3),
                    'amount' => moneyFormatIndia(number_format($amt, 2, '.', ''))
                ];
                $order_advance_total += $amt;
            }
        }
    }
    $mapped['order_advance_entries'] = $order_advance_entries;
    $mapped['order_advance_total'] = moneyFormatIndia(number_format($order_advance_total, 2, '.', ''));
    
    // -- Chit Details (Generic) --
    $chit_general_items = [];
    $chit_general_total = 0;
    $total_rate_benefit = 0;
    $total_amount_payable = 0;
    $total_apx_benefit = 0;

    $is_split_bill = 0;
    if (!empty($items_data['item_details'])) {
        foreach ($items_data['item_details'] as $items) {
            if (isset($items['isTagsplitted']) && $items['isTagsplitted'] == 1) {
                $is_split_bill = 1;
            }
        }
    }

    // Only populate chit adjustment details for non-pre-close bills (bill_type != 10)
    if ($billing['bill_type'] != 10 && !empty($items_data['chit_details'])) {
        $sno = 1;
        foreach ($items_data['chit_details'] as $chit) {
            $scheme_benefit = 0;
            $is_weight = 1;
            if (isset($chit['new_scheme_type']) && $chit['new_scheme_type'] == 0) {
                $is_weight = 0;
            } else if (isset($chit['new_scheme_type']) && $chit['new_scheme_type'] == 3) {
                if (in_array($chit['flexible_sch_type'], [1, 6]) || ($chit['flexible_sch_type'] == 2 && $chit['wgt_convert'] == 2)) {
                    $is_weight = 0;
                } else if (in_array($chit['flexible_sch_type'], [3, 4, 5, 7, 8]) || ($chit['flexible_sch_type'] == 2 && in_array($chit['wgt_convert'], [0, 1]))) {
                    $is_weight = 1;
                }
            } else if (isset($chit['new_scheme_type']) && in_array($chit['new_scheme_type'], [1, 2])) {
                $is_weight = 1;
            }

            if ($is_weight == 1 && isset($chit['closing_weight']) && $chit['closing_weight'] > 0) {
                $rate_per_gram = (isset($chit['rate_per_gram']) && $chit['rate_per_gram'] > 0 ? $chit['rate_per_gram'] : $billing['goldrate_22ct']);
                $closing_weight = number_format($chit['closing_weight'], 3, '.', '');
                $closing_amount = $chit['closing_amount'];
                
                $base_benefit = ($closing_weight * $rate_per_gram) - $closing_amount;
                
                if ($is_split_bill == 0) {
                    $savings_in_making_charge = isset($chit['savings_in_making_charge']) ? $chit['savings_in_making_charge'] : 0;
                    $savings_in_wastage = (isset($chit['savings_in_wastage']) ? $chit['savings_in_wastage'] : 0) * $rate_per_gram;
                    $base_benefit += $savings_in_making_charge + $savings_in_wastage;
                } else {
                    $savings_in_making_charge = isset($chit['savings_in_making_charge']) ? $chit['savings_in_making_charge'] : 0;
                    $savings_in_wastage = (isset($chit['savings_in_wastage']) ? $chit['savings_in_wastage'] : 0) * $rate_per_gram;
                    $total_apx_benefit += $savings_in_making_charge + $savings_in_wastage;
                }
                
                $scheme_benefit = floatval(number_format(max(0, $base_benefit), 0, '.', ''));
            } else if ($is_weight == 0 && isset($chit['closing_amount']) && $chit['closing_amount'] > 0) {
                $base_benefit = isset($chit['closing_benefits']) ? $chit['closing_benefits'] : 0;
                $scheme_benefit = floatval(number_format(max(0, $base_benefit), 0, '.', ''));
            }

            $payable_amount = $chit['utilized_amt'] - $scheme_benefit;
            $row_total = $chit['utilized_amt'];

            $chit_general_items[] = [
                'sno' => $sno++,
                'ref_no' => $chit['scheme_acc_number'],
                'amount' => moneyFormatIndia(number_format($payable_amount, 2, '.', '')),
                'rate_benefit' => moneyFormatIndia(number_format($scheme_benefit, 2, '.', '')),
                'total' => moneyFormatIndia(number_format($row_total, 2, '.', ''))
            ];

            $total_amount_payable += $payable_amount;
            $total_rate_benefit += $scheme_benefit;
            $chit_general_total += $row_total;
        }
    }
    $mapped['chit_general_items'] = $chit_general_items;
    $mapped['chit_general_total'] = moneyFormatIndia(number_format($chit_general_total, 2, '.', ''));
    $mapped['chit_general_payable_total'] = moneyFormatIndia(number_format($total_amount_payable, 2, '.', ''));
    $mapped['chit_general_rate_benefit_total'] = moneyFormatIndia(number_format($total_rate_benefit, 2, '.', ''));
    
    // Approx discount benefits (for split bills)
    $mapped['apx_scheme_discount'] = moneyFormatIndia(number_format($total_apx_benefit, 2, '.', ''));
    $mapped['has_apx_scheme_discount'] = ($is_split_bill == 1 && $total_apx_benefit > 0);

    // -- Item Count Flags for Dynamic Scaling --
    $sales_count = !empty($mapped['sales_items']) ? count($mapped['sales_items']) : 0;
    $return_count = !empty($mapped['return_items']) ? count($mapped['return_items']) : 0;
    $old_metal_count = !empty($mapped['old_metal_items']) ? count($mapped['old_metal_items']) : 0;
    $transfer_count = !empty($mapped['transfer_items']) ? count($mapped['transfer_items']) : 0;
    
    $total_items = $sales_count + $return_count + $old_metal_count + $transfer_count;
    
    $mapped['total_items_count'] = $total_items;
    $mapped['is_long_bill'] = ($total_items > 12); // Adjustable threshold for compact mode
    $mapped['is_short_bill'] = !$mapped['is_long_bill'];
    $mapped['has_multiple_pages'] = ($total_items > 20);

    // Modular Table Flags
    $mapped['show_sales_title'] = ($return_count > 0 || $old_metal_count > 0 || $transfer_count > 0);
    $mapped['has_sales_items'] = ($sales_count > 0);
    $mapped['has_purchase_items'] = ($old_metal_count > 0);
    $mapped['has_return_items'] = ($return_count > 0);
    $mapped['has_transfer_items'] = ($transfer_count > 0);
    $mapped['has_chit_items'] = !empty($mapped['chit_general_items']) || !empty($mapped['chit_pre_close_items']);
    $mapped['has_order_advance_entries'] = !empty($mapped['order_advance_entries']);
    $mapped['has_credit_collection_history'] = !empty($mapped['credit_collection_history']);
    $mapped['has_repair_items'] = !empty($mapped['repair_order_items']);

    // -- Receipt Adjustment Items (for Konva receipt_adjustment table type) --
    // Maps the raw receiptDetails array into a flat structure for table rendering
    // Matches bill_format_2.php lines 3559-3596
    $receipt_adj_items = [];
    $receipt_adj_total_amt = 0;
    $receipt_adj_total_utilized = 0;
    $receipt_adj_total_refund = 0;
    $receipt_adj_total_balance = 0;
    if (!empty($data['receiptDetails'])) {
        foreach ($data['receiptDetails'] as $val) {
            $receipt_adj_items[] = [
                'receipt_no'      => $val['bill_no'] ?? '',
                'receipt_date'    => $val['bill_date'] ?? '',
                'receipt_amount'  => moneyFormatIndia(number_format($val['tot_receipt_amount'] ?? 0, 2, '.', '')),
                'adjusted_amount' => moneyFormatIndia(number_format($val['adjuseted_amt'] ?? 0, 2, '.', '')),
                'utilized_amount' => moneyFormatIndia(number_format($val['tot_utilized_amt'] ?? 0, 2, '.', '')),
                'refund_amount'   => moneyFormatIndia(number_format($val['refund_amt'] ?? 0, 2, '.', '')),
                'balance_amount'  => moneyFormatIndia(number_format($val['bal_amt'] ?? 0, 2, '.', '')),
            ];
            $receipt_adj_total_amt += floatval($val['tot_receipt_amount'] ?? 0);
            $receipt_adj_total_utilized += floatval($val['tot_utilized_amt'] ?? 0);
            $receipt_adj_total_refund += floatval($val['refund_amt'] ?? 0);
            $receipt_adj_total_balance += floatval($val['bal_amt'] ?? 0);
        }
    }
    $mapped['receipt_adjustment_items'] = $receipt_adj_items;
    $mapped['receipt_adj_total_amount'] = moneyFormatIndia(number_format($receipt_adj_total_amt, 2, '.', ''));
    $mapped['receipt_adj_total_utilized'] = moneyFormatIndia(number_format($receipt_adj_total_utilized, 2, '.', ''));
    $mapped['receipt_adj_total_refund'] = moneyFormatIndia(number_format($receipt_adj_total_refund, 2, '.', ''));
    $mapped['receipt_adj_total_balance'] = moneyFormatIndia(number_format($receipt_adj_total_balance, 2, '.', ''));
    $mapped['has_receipt_adjustment'] = (count($receipt_adj_items) > 0);



    $mapped['has_payment_details'] = (
        floatval(str_replace(',', '', $mapped['pay_cash'] ?? '0')) > 0 ||
        floatval(str_replace(',', '', $mapped['pay_cheque'] ?? '0')) > 0 ||
        floatval(str_replace(',', '', $mapped['pay_upi'] ?? '0')) > 0 ||
        floatval(str_replace(',', '', $mapped['pay_credit_card'] ?? '0')) > 0 ||
        floatval(str_replace(',', '', $mapped['pay_debit_card'] ?? '0')) > 0 ||
        floatval(str_replace(',', '', $mapped['pay_neft'] ?? '0')) > 0 ||
        floatval(str_replace(',', '', $mapped['pay_imps'] ?? '0')) > 0 ||
        floatval(str_replace(',', '', $mapped['pay_rtgs'] ?? '0')) > 0
    );

    // -- Adjustment Summary Variables (shown after NET PAYABLE) --
    // Chit Adjustment: total chit utilized amount in this bill
    $mapped['chit_adj'] = moneyFormatIndia(number_format($chit_general_total, 2, '.', ''));
    $mapped['has_chit_adj'] = ($chit_general_total > 0);
    
    // Advance Adjustment: advance deposit utilized from customer's advance account
    $advance_deposit_val = isset($billing['advance_deposit']) ? floatval($billing['advance_deposit']) : 0;
    $mapped['advance_adj'] = moneyFormatIndia(number_format($advance_deposit_val, 2, '.', ''));
    $mapped['advance_deposit'] = moneyFormatIndia(number_format($advance_deposit_val, 2, '.', '')); // Direct alias
    $mapped['has_advance_adj'] = ($advance_deposit_val > 0);
    $mapped['has_advance_adjustment'] = ($rcpt_adj_sum > 0); // Receipt-based advance adjustment flag
    // Order Advance Adjustment: already mapped as order_advance_adj (from receiptDetails)
    $mapped['order_advance_adj'] = moneyFormatIndia(number_format($total_advance_adjusted, 2, '.', ''));
    $mapped['has_order_advance_adj'] = ($total_advance_adjusted > 0);
    
    // Receipt Adjustment: total issue/receipt (IR) adjustment amount
    $receipt_adj_val = isset($billing['tot_amt_received']) ? floatval($billing['tot_amt_received']) : 0;
    // Only show receipt adj if there's a meaningful amount and it's different from what we already show
    $mapped['receipt_adj'] = moneyFormatIndia(number_format($receipt_adj_val, 2, '.', ''));
    $mapped['has_receipt_adj'] = ($receipt_adj_val > 0);
    // === KEY ALIASES for global template loop tags ===
    // Template uses {{#sales_items}}, already set at line 429
    if (!isset($mapped['sales_items'])) {
        $mapped['sales_items'] = [];
    }

    // Template uses {{#purchase_items}} with purchase_sno, purchase_description, etc.
    // Helper stores as old_metal_items with old_metal_sno, old_metal_description, etc.
    // We remap each row's keys from old_metal_ prefix to purchase_ prefix.
    $purchase_items_remapped = [];
    if (!empty($mapped['old_metal_items'])) {
        foreach ($mapped['old_metal_items'] as $om) {
            $purchase_items_remapped[] = [
                'purchase_sno'         => $om['old_metal_sno']         ?? '',
                'purchase_description' => $om['old_metal_description']  ?? '',
                'purchase_gross_wt'    => $om['old_metal_gross_wt']     ?? '',
                'purchase_stn_less'    => $om['stone_wt']               ?? '',
                'purchase_mtl_less'    => $om['dust_wt']                ?? '',
                'purchase_net_wt'      => $om['old_metal_net_wt']       ?? '',
                'purchase_rate'        => $om['old_metal_rate']          ?? '',
                'purchase_amount'      => $om['old_metal_amount']        ?? '',
            ];
        }
    }
    $mapped['purchase_items'] = $purchase_items_remapped;

    // Template uses {{#return_items}} — already set with return_ prefixed keys
    if (!isset($mapped['return_items'])) {
        $mapped['return_items'] = [];
    }

    // ── Tax Detail Breakdown (HSN-wise tax split) ─────────────────────
    // Matches bill_format_2.php line 2640: only shown when 2+ different tax groups exist
    // Model populates est_other_item['tax_details'] grouped by tax_group_id
    // Each row has: tax_percentage, hsn_code, item_cost, item_total_tax, total_sgst, total_cgst, total_igst
    $tax_detail_mapped = [];
    if (!empty($items_data['tax_details']) && count($items_data['tax_details']) > 1) {
        $td_sno = 1;
        foreach ($items_data['tax_details'] as $td) {
            $td_item_cost = floatval($td['item_cost']);
            $td_item_total_tax = floatval($td['item_total_tax']);
            $td_taxable = $td_item_cost - $td_item_total_tax;
            $td_tax_pct = floatval($td['tax_percentage']);
            $td_cgst = floatval($td['total_cgst']);
            $td_sgst = floatval($td['total_sgst']);
            $td_igst = floatval($td['total_igst']);

            $tax_detail_mapped[] = [
                'tax_sno'            => $td_sno,
                'tax_hsn_code'       => $td['hsn_code'] ?? '',
                'tax_taxable_value'  => moneyFormatIndia(number_format($td_taxable, 2, '.', '')),
                'tax_cgst_rate'      => ($td_cgst > 0 ? number_format($td_tax_pct / 2, 1) . '%' : '0'),
                'tax_cgst_amount'    => moneyFormatIndia(number_format($td_cgst, 2, '.', '')),
                'tax_sgst_rate'      => ($td_sgst > 0 ? number_format($td_tax_pct / 2, 1) . '%' : '0'),
                'tax_sgst_amount'    => moneyFormatIndia(number_format($td_sgst, 2, '.', '')),
                'tax_igst_rate'      => ($td_igst > 0 ? number_format($td_tax_pct, 1) . '%' : '0'),
                'tax_igst_amount'    => moneyFormatIndia(number_format($td_igst, 2, '.', '')),
                'tax_total_tax'      => moneyFormatIndia(number_format($td_item_total_tax, 2, '.', '')),
            ];
            $td_sno++;
        }
        $mapped['has_tax_detail_items'] = true;
    }
    $mapped['tax_detail_items'] = $tax_detail_mapped;

    // -- [2042] Chit Rate Benefit (rate difference benefit before GST) --
    // rate_benefit per chit = (closing_weight × current_gold_rate) - closing_amount
    // Total chit_benefit_before_gst is the sum of all rate_benefit values
    $chit_rate_benefit_total = 0;
    if (!empty($items_data['chit_details'])) {
        $gold_rate = floatval($billing['goldrate_22ct']);
        foreach ($items_data['chit_details'] as $chit) {
            // Use stored rate_benefit if available (from DB), else compute
            if (isset($chit['rate_benefit']) && floatval($chit['rate_benefit']) > 0) {
                $chit_rate_benefit_total += floatval($chit['rate_benefit']);
            } else {
                // Fallback: compute from closing_weight and closing_amount
                $cw = isset($chit['closing_weight']) ? floatval($chit['closing_weight']) : 0;
                $ca = isset($chit['closing_amount']) ? floatval($chit['closing_amount']) : 0;
                if ($cw > 0 && $gold_rate > 0) {
                    $benefit = ($cw * $gold_rate) - $ca;
                    $chit_rate_benefit_total += ($benefit > 0 ? $benefit : 0);
                }
            }
        }
    }
    // Also check if chit_benefit_before_gst was stored directly in ret_billing
    if ($chit_rate_benefit_total == 0 && isset($billing['chit_benefit_before_gst']) && floatval($billing['chit_benefit_before_gst']) > 0) {
        $chit_rate_benefit_total = floatval($billing['chit_benefit_before_gst']);
    }
    $mapped['rate_benefit'] = number_format($chit_rate_benefit_total, 2);
    $mapped['chit_benefit'] = number_format($chit_rate_benefit_total, 2);
    $mapped['chit_benefit_before_gst'] = number_format($chit_rate_benefit_total, 2);
    $mapped['has_chit_benefit'] = ($chit_rate_benefit_total > 0);

    $mapped['max_va_percent'] = ($max_va_percent > 0) ? number_format($max_va_percent, 2, '.', '') : "";
    $mapped['total_va_content'] = ($sum_va_costs > 0) ? moneyFormatIndia(number_format($sum_va_costs, 2, '.', '')) : "";
    $mapped['sales_total_item_total'] = ($sales_total_item_total > 0) ? moneyFormatIndia(number_format($sales_total_item_total, 2, '.', '')) : "";
    $mapped['total_stone_wt'] = ($total_stone_wt > 0) ? number_format($total_stone_wt, 3, '.', '') : "";
    $mapped['total_stone_amount'] = ($total_stone_amount > 0) ? moneyFormatIndia(number_format($total_stone_amount, 2, '.', '')) : "";

    // ═══════════════════════════════════════════════════════════════════════
    // VALIDATION GATE — Catch undeclared variables
    // Compares final $mapped keys against the registry arrays declared above.
    // Any key NOT in the registry triggers a log_message('error') warning.
    // ═══════════════════════════════════════════════════════════════════════
    $registry_keys = array_merge($text_keys, $money_keys, $weight_keys, $int_keys, $flag_keys, $array_keys);

    $undeclared = array_diff(array_keys($mapped), $registry_keys);
    if (!empty($undeclared)) {
        log_message('error', '[RECEIPT_HELPER] UNDECLARED template variables detected: ' . implode(', ', $undeclared) . 
            ' — Add these to the VARIABLE REGISTRY at the top of tpl_map_billing_data_to_template()');
    }

    return $mapped;
}


if (!function_exists('moneyFormatIndia')) {
    function moneyFormatIndia($num) {
        // Separate decimal part first (fixes bug where 150000.00 became 15,00,00,.00)
        $decimal = '';
        $num = (string)$num;
        if (strpos($num, '.') !== false) {
            $parts = explode('.', $num);
            $num = $parts[0];
            $decimal = '.' . $parts[1];
        }
        // Remove any existing commas
        $num = str_replace(',', '', $num);
        
        $explrestunits = "";
        if (strlen($num) > 3) {
            $lastthree = substr($num, -3);
            $restunits = substr($num, 0, strlen($num) - 3);
            $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
            $expunit = str_split($restunits, 2);
            for ($i = 0; $i < sizeof($expunit); $i++) {
                if ($i == 0) {
                    $explrestunits .= (int)$expunit[$i] . ",";
                } else {
                    $explrestunits .= $expunit[$i] . ",";
                }
            }
            $thecash = $explrestunits . $lastthree;
        } else {
            $thecash = $num;
        }
        return $thecash . $decimal; 
    }
}

function tpl_convert_number_to_words($number) {
    // Sanitise: strip commas (in case a formatted string is passed) and take absolute value
    $number = abs(floatval(str_replace(',', '', $number)));

    if ($number == 0) {
        return 'Zero Rupees Only';
    }

    $no = floor($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen((string)$no);
    $i = 0;
    $str = array();
    $words = array('0' => '', '1' => 'One', '2' => 'Two',
        '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
        '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
        '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
        '13' => 'Thirteen', '14' => 'Fourteen',
        '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
        '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
        '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
        '60' => 'Sixty', '70' => 'Seventy',
        '80' => 'Eighty', '90' => 'Ninety');
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred :
                $words[floor($number / 10) * 10] . " " . $words[$number % 10] .
                " " . $digits[$counter] . $plural . " " . $hundred;
        } else $str[] = null;
    }
    $str = array_reverse($str);
    $result = trim(implode('', $str));

    // Build paise part
    $paise = '';
    if ($point > 0) {
        $paise_words = '';
        if ($point < 21) {
            $paise_words = $words[$point];
        } else {
            $paise_words = $words[floor($point / 10) * 10] . ' ' . $words[$point % 10];
        }
        $paise = ' and ' . trim($paise_words) . ' Paise';
    }

    return $result . " Rupees" . $paise . " Only";
}

/**
 * Format number in Indian currency style (e.g. 1,01,643.49)
 */
if (!function_exists('moneyFormatIndia')) {
    function moneyFormatIndia($num) {
        return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\\d+)?/i", "$1,", $num);
    }
}

/**
 * Map estimation data to flat template variables for the print template engine.
 * Mirrors map_billing_data_to_template() but for estimation-specific data.
 *
 * @param array $data   The full data array with keys: estimation, est_other_item, comp_details, metal_rates, filename
 * @return array        Flat key-value map for template rendering
 */
function tpl_map_estimation_data_to_template($data) {
    $mapped = [];
    $estimation = $data['estimation'];
    $company    = $data['comp_details'];
    $items_data = $data['est_other_item'];

    // -- Company Details --
    $mapped['company_name']    = $company['company_name'] ?? '';
    $mapped['company_address'] = $company['company_address'] ?? '';
    $mapped['company_mobile']  = $company['mobile'] ?? '';
    $mapped['company_email']   = $company['email'] ?? '';
    $mapped['company_gstin']   = $company['gst_number'] ?? '';
    $mapped['company_pan']     = $company['pan_no'] ?? '';
    $mapped['company_state']   = $company['state'] ?? '';
    $mapped['company_state_code'] = $company['state_code'] ?? '';
    $mapped['company_city']    = $company['city'] ?? '';
    $mapped['company_pincode'] = $company['pincode'] ?? '';

    // -- Estimation Header --
    $mapped['title']         = 'Estimation';
    $mapped['esti_no']       = $estimation['esti_no'];
    $mapped['estimation_no'] = $estimation['esti_no']; // alias used in templates
    $mapped['invoice_no']    = $estimation['esti_no']; // alias for common template field
    $mapped['est_date']      = date('d-m-Y', strtotime($estimation['estimation_datetime']));
    $mapped['bill_date']     = $mapped['est_date']; // alias
    $mapped['est_time']      = date('h:i:s A', strtotime($estimation['estimation_datetime']));
    $mapped['bill_time']     = $mapped['est_time']; // alias
    $mapped['est_employee']  = $estimation['emp_name'] ?? '';
    $mapped['emp_code']      = $estimation['emp_code'] ?? '';
    $mapped['billed_by']     = $estimation['emp_name'] ?? '';
    $mapped['branch_name']   = $estimation['short_name'] ?? '';
    $mapped['village_name']  = $estimation['village_name'] ?? '';
    $mapped['customer_village'] = $estimation['village_name'] ?? ''; // alias
    $mapped['emp_name']      = $estimation['emp_name'] ?? '';
    $mapped['print_time']    = date('h:i A');

    // -- Customer Details --
    $mapped['customer_name']    = $estimation['customer_name'] ?? '';
    $mapped['customer_mobile']  = $estimation['mobile'] ?? '';
    $mapped['customer_address'] = trim(
        ($estimation['address1'] ?? '') .
        (!empty($estimation['address2']) ? ', ' . $estimation['address2'] : '') .
        (!empty($estimation['address3']) ? ', ' . $estimation['address3'] : '')
    );
    $mapped['customer_city']    = $estimation['city_name'] ?? '';
    $mapped['customer_pincode'] = $estimation['pincode'] ?? '';
    $mapped['customer_gstin']   = '';
    $mapped['customer_pan']     = '';

    // -- Metal Rates --
    $mapped['gold_rate']       = number_format($estimation['goldrate_22ct'], 2, '.', '');
    $mapped['gold_rate_22ct']  = number_format($estimation['goldrate_22ct'], 2, '.', '');
    $mapped['gold_rate_18ct']  = number_format($estimation['goldrate_18ct'], 2, '.', '');
    $mapped['gold_rate_14ct']  = number_format($estimation['goldrate_14ct'], 2, '.', '');
    $mapped['gold_rate_9ct']   = number_format($estimation['goldrate_9ct'], 2, '.', '');
    $mapped['silver_rate']     = number_format($estimation['silverrate_1gm'], 2, '.', '');

    // -- QR Code --
    $mapped['qr_code_image'] = $data['filename'] ?? '';

    // -- Items Mapping --
    $estimation_items = [];
    $total_pieces   = 0;
    $total_gwt      = 0;
    $total_nwt      = 0;
    $total_cost     = 0;
    $total_tax      = 0;
    $sub_total      = 0;
    $sno = 1;

    $sum_va_costs = 0;
    $sales_total_item_total = 0;
    $max_va_percent = 0;
    $total_stone_wt = 0;
    $total_stone_amount = 0;

    if (!empty($items_data['item_details'])) {
        foreach ($items_data['item_details'] as $item) {
            $item_total_tax = floatval($item['item_total_tax'] ?? 0);
            $item_cost      = floatval($item['item_cost'] ?? 0);
            $payable_no_tax = $item_cost - $item_total_tax;

            $sales_total_item_total += floatval($item_cost);
            $max_va_percent = max($max_va_percent, floatval($item['wastage_percent'] ?? 0));

            // Wastage & MC calculation (mirrors est_print_2.php logic)
            $wast_wgt = 0;
            $mc = 0;
            if ($item['calculation_based_on'] == 0) {
                $wast_wgt = ($item['gross_wt'] * ($item['wastage_percent'] / 100));
                $mc = ($item['mc_type'] == 2 ? $item['gross_wt'] * $item['mc_value'] : $item['mc_value'] * $item['piece']);
            } else if ($item['calculation_based_on'] == 1) {
                $wast_wgt = ($item['net_wt'] * ($item['wastage_percent'] / 100));
                $mc = ($item['mc_type'] == 2 ? $item['net_wt'] * $item['mc_value'] : $item['mc_value'] * $item['piece']);
            } else if ($item['calculation_based_on'] == 2) {
                $wast_wgt = ($item['net_wt'] * ($item['wastage_percent'] / 100));
                $mc = ($item['mc_type'] == 2 ? $item['gross_wt'] * $item['mc_value'] : $item['mc_value'] * 1);
            }

            $wastge_amt = $wast_wgt * floatval($item['est_rate_per_grm'] ?? 0);
            $sum_va_costs += floatval($wastge_amt);

            // VA percent
            $va_percent = 0;
            if ($item['net_wt'] > 0) {
                $va_percent = round(($wast_wgt / $item['net_wt']) * 100, 2);
            }

            // Stone amount
            $stone_amount = 0;
            if (!empty($item['stone_details'])) {
                foreach ($item['stone_details'] as $stone) {
                    $s_amt = floatval($stone['price'] ?? 0);
                    $stone_amount += $s_amt;
                    $total_stone_wt += floatval($stone['wt'] ?? 0);
                    $total_stone_amount += $s_amt;
                }
            }

            // Charge amount
            $charge_amount = 0;
            if (!empty($item['charges'])) {
                foreach ($item['charges'] as $charge) {
                    $charge_amount += floatval($charge['amount'] ?? 0);
                }
            }

            $estimation_items[] = [
                'sno'             => $sno,
                'product_name'    => $item['product_name'] ?? '',
                'sub_design_name' => $item['sub_design_name'] ?? '',
                'tag_code'        => $item['tag_code'] ?? '',
                'purity'          => $item['purity'] ?? '',
                'qty'             => $item['piece'],
                'gross_wt'        => number_format($item['gross_wt'], 3, '.', ''),
                'net_wt'          => number_format($item['net_wt'], 3, '.', ''),
                'rate'            => number_format($item['est_rate_per_grm'], 2, '.', ''),
                'va_percent'      => $va_percent > 0 ? $va_percent . '%' : '',
                'mc'              => moneyFormatIndia(number_format($mc, 2, '.', '')),
                'amount'          => moneyFormatIndia(number_format($item_cost, 2, '.', '')),
                'stone_amount'    => moneyFormatIndia(number_format($stone_amount, 2, '.', '')),
                'charge_amount'   => moneyFormatIndia(number_format($charge_amount, 2, '.', '')),
                'taxable_amount'  => moneyFormatIndia(number_format($payable_no_tax, 2, '.', '')),
                'tax_amount'      => moneyFormatIndia(number_format($item_total_tax, 2, '.', '')),
                'metal_name'      => $item['metal_name'] ?? '',
                'wastage_percent' => $item['wastage_percent'],
                'description'     => ($item['product_name'] ?? '') . ' ' . ($item['sub_design_name'] ?? ''),
                'has_charges'     => ($charge_amount > 0),
                'item_total'      => ($item_cost > 0) ? moneyFormatIndia(number_format($item_cost, 2, '.', '')) : ""
            ];

            $total_pieces += $item['piece'];
            $total_gwt    += $item['gross_wt'];
            $total_nwt    += $item['net_wt'];
            $total_cost   += $item_cost;
            $total_tax    += $item_total_tax;
            $sub_total    += $payable_no_tax;
            $sno++;
        }
    }

    $mapped['estimation_items'] = $estimation_items;
    $mapped['items']            = $estimation_items; // alias for common templates

    // -- Old Metal / Purchase Items --
    $estimation_purchase_items = [];
    $total_old_metal = 0;
    $old_sno = 1;
    if (!empty($items_data['old_matel_details'])) {
        foreach ($items_data['old_matel_details'] as $om) {
            $estimation_purchase_items[] = [
                'sno'             => $old_sno,
                'metal_name'      => $om['metal'] ?? '',
                'old_metal_type'  => $om['old_metal_type'] ?? '',
                'piece'           => $om['piece'] ?? 1,
                'gross_wt'        => number_format($om['gross_wt'], 3, '.', ''),
                'stone_wt'        => number_format($om['stone_wt'], 3, '.', ''),
                'net_wt'          => number_format($om['net_wt'], 3, '.', ''),
                'purity'          => $om['purname'] ?? '',
                'wastage_percent' => $om['wastage_percent'] ?? 0,
                'wastage_wt'      => number_format($om['wastage_wt'] ?? 0, 3, '.', ''),
                'rate_per_gram'   => moneyFormatIndia(number_format($om['rate_per_gram'], 2, '.', '')),
                'amount'          => moneyFormatIndia(number_format($om['amount'], 2, '.', '')),
                'purpose'         => $om['purpose'] ?? '',
                'less_wt'         => number_format(floatval($om['stone_wt'] ?? 0) + floatval($om['dust_wt'] ?? 0), 3),
                'description'     => ($om['metal'] ?? '') . ' ' . ($om['old_metal_type'] ?? ''),
            ];
            $total_old_metal += floatval($om['amount']);
            $old_sno++;
        }
    }
    $mapped['estimation_purchase_items'] = $estimation_purchase_items;
    $mapped['purchase_items']            = $estimation_purchase_items; // alias
    $mapped['est_old_metal']             = $estimation_purchase_items; // alias used in templates
    $mapped['total_old_metal']           = moneyFormatIndia(number_format($total_old_metal, 2, '.', ''));
    $mapped['purchase_total']            = moneyFormatIndia(number_format($total_old_metal, 2, '.', '')); // alias
    $mapped['total_old_gross_wt']        = number_format(array_sum(array_column($items_data['old_matel_details'] ?? [], 'gross_wt')), 3, '.', '');
    $mapped['has_old_metal']             = !empty($estimation_purchase_items);

    // -- Chit Details --
    $estimation_chit_items = [];
    $total_chit_amount = 0;
    $chit_sno = 1;
    if (!empty($items_data['chit_details'])) {
        foreach ($items_data['chit_details'] as $chit) {
            $estimation_chit_items[] = [
                'sno'                => $chit_sno,
                'scheme_acc_number'  => $chit['scheme_acc_number'] ?? '',
                'closing_weight'     => $chit['closing_weight'] ?? 0,
                'utl_amount'         => number_format($chit['utl_amount'], 2),
                'rate_per_gram'      => $chit['rate_per_gram'] ?? '',
            ];
            $total_chit_amount += floatval($chit['utl_amount']);
            $chit_sno++;
        }
    }
    $mapped['estimation_chit_items'] = $estimation_chit_items;
    $mapped['chit_items']            = $estimation_chit_items; // alias
    $mapped['total_chit_amount']     = moneyFormatIndia(number_format($total_chit_amount, 2, '.', ''));
    $mapped['chit_amount']           = moneyFormatIndia(number_format($total_chit_amount, 2, '.', '')); // alias
    $mapped['has_chit_details']      = !empty($estimation_chit_items);

    // -- Summary Totals --
    $mapped['total_pieces']   = $total_pieces;
    $mapped['total_gross_wt'] = number_format($total_gwt, 3, '.', '');
    $mapped['total_net_wt']   = number_format($total_nwt, 3, '.', '');
    $mapped['sub_total']      = moneyFormatIndia(number_format($sub_total, 2, '.', ''));
    $mapped['total_tax']      = moneyFormatIndia(number_format($total_tax, 2, '.', ''));
    $mapped['grand_total']    = moneyFormatIndia(number_format($total_cost, 2, '.', ''));
    $mapped['net_payable']    = moneyFormatIndia(number_format($total_cost, 2, '.', ''));
    $mapped['discount']       = moneyFormatIndia(number_format($estimation['discount'] ?? 0, 2, '.', ''));

    // Tax split (CGST/SGST/IGST) — use same/different state logic
    $is_same_state = true;
    if (!empty($company['id_state']) && !empty($estimation['id_state'])) {
        $is_same_state = ($company['id_state'] == $estimation['id_state']);
    }
    if ($is_same_state) {
        $mapped['cgst_amount']  = moneyFormatIndia(number_format($total_tax / 2, 2, '.', ''));
        $mapped['sgst_amount']  = moneyFormatIndia(number_format($total_tax / 2, 2, '.', ''));
        $mapped['igst_amount']  = '0.00';
        $mapped['cgst_percent'] = '1.5';
        $mapped['sgst_percent'] = '1.5';
        $mapped['igst_percent'] = '0';
    } else {
        $mapped['cgst_amount']  = '0.00';
        $mapped['sgst_amount']  = '0.00';
        $mapped['igst_amount']  = moneyFormatIndia(number_format($total_tax, 2, '.', ''));
        $mapped['cgst_percent'] = '0';
        $mapped['sgst_percent'] = '0';
        $mapped['igst_percent'] = '3';
    }

    // Amount in words
    if (function_exists('tpl_convert_number_to_words')) {
        $mapped['amount_in_words'] = tpl_convert_number_to_words($total_cost);
    } else {
        $mapped['amount_in_words'] = '';
    }

    // Advance details
    $advance_amount = 0;
    if (!empty($items_data['advance_details'])) {
        foreach ($items_data['advance_details'] as $adv) {
            $advance_amount += floatval($adv['amount'] ?? 0);
        }
    }
    $mapped['advance_paid']  = moneyFormatIndia(number_format($advance_amount, 2, '.', ''));
    $mapped['has_advance']   = ($advance_amount > 0);

    $mapped['max_va_percent'] = ($max_va_percent > 0) ? number_format($max_va_percent, 2, '.', '') : "";
    $mapped['total_va_content'] = ($sum_va_costs > 0) ? moneyFormatIndia(number_format($sum_va_costs, 2, '.', '')) : "";
    $mapped['sales_total_item_total'] = ($sales_total_item_total > 0) ? moneyFormatIndia(number_format($sales_total_item_total, 2, '.', '')) : "";
    $mapped['total_stone_wt'] = ($total_stone_wt > 0) ? number_format($total_stone_wt, 3, '.', '') : "";
    $mapped['total_stone_amount'] = ($total_stone_amount > 0) ? moneyFormatIndia(number_format($total_stone_amount, 2, '.', '')) : "";

    return $mapped;
}

/**
 * Map Issue Receipt data to flat template variables for the print template engine.
 *
 * @param array $data  Controller data with keys: issue, comp_details, payment, receipt_adv_details,
 *                     advance_adj_details, deposit_type_bill_no, metal_rate
 * @return array       Flat key-value map for template rendering
 */
function tpl_map_issue_receipt_data_to_template($data) {
    $mapped = [];
    $issue   = $data['issue'] ?? [];
    $company = $data['comp_details'] ?? [];
    $payment = $data['payment'] ?? [];

    // -- Company Details --
    $mapped['company_name']    = $company['company_name'] ?? '';
    $mapped['company_address'] = $company['company_address'] ?? '';
    $mapped['company_mobile']  = $company['mobile'] ?? '';
    $mapped['company_gstin']   = $company['gst_number'] ?? '';

    // -- Customer / Party Details --
    $title_prefix = !empty($issue['title']) ? $issue['title'] . '. ' : 'Mr/Mrs. ';
    $mapped['customer_title']   = $title_prefix;
    $mapped['customer_name']    = $title_prefix . ($issue['name'] ?? '');
    $mapped['customer_name_raw']= $issue['name'] ?? '';
    $mapped['customer_mobile']  = $issue['mobile'] ?? '';
    $mapped['customer_address1']= $issue['address1'] ?? '';
    $mapped['customer_address2']= $issue['address2'] ?? '';
    $mapped['customer_address3']= $issue['address3'] ?? '';
    $mapped['customer_village'] = $issue['village_name'] ?? '';
    $mapped['customer_city']    = $issue['city_name'] ?? '';
    $mapped['customer_pincode'] = $issue['pincode'] ?? '';
    $mapped['customer_state']   = $issue['cus_state'] ?? '';
    $mapped['customer_pan']     = $issue['pan_no'] ?? '';
    $mapped['customer_gstin']   = $issue['gst_number'] ?? '';
    $mapped['is_eda']           = $issue['is_eda'] ?? 1;
    $mapped['state_code']       = $issue['state_code'] ?? '';

    // -- Invoice / Receipt Details --
    $mapped['bill_no']      = $issue['bill_no'] ?? '';
    $mapped['date_add']     = $issue['date_add'] ?? '';
    $mapped['time_add']     = $issue['time_add'] ?? '';
    $mapped['receipt_type'] = $issue['receipt_type'] ?? '';
    $mapped['issue_type']   = $issue['issue_type'] ?? '';
    $mapped['type']         = $issue['type'] ?? '';
    $mapped['narration']    = $issue['narration'] ?? '';
    $mapped['emp_name']     = $issue['emp_name'] ?? '';
    $mapped['emp_code']     = $issue['emp_code'] ?? '';
    $mapped['rct_type']     = $issue['rct_type'] ?? '';

    // -- Receipt type label --
    $is_eda = $issue['is_eda'] ?? 1;
    $receipt_type_label = $issue['receipt_type'] ?? '';
    if ($receipt_type_label == 'Advance Receipt' && $is_eda == 2) {
        $receipt_type_label = 'ADVANCE PROFORMA';
    } elseif (($issue['issue_type'] ?? '') == 3 && $is_eda == 2) {
        $receipt_type_label = 'ADVANCE REFUND PROFORMA';
    }
    $mapped['receipt_type_label'] = strtoupper($receipt_type_label);

    // -- Invoice label --
    if (($issue['receipt_type'] ?? '') == 'Advance Receipt' && $is_eda == 2) {
        $mapped['invoice_label'] = 'Adv proforma no';
    } elseif (($issue['issue_type'] ?? '') == 3 && $is_eda == 2) {
        $mapped['invoice_label'] = 'Proforma No';
    } else {
        $mapped['invoice_label'] = 'Invoice No';
    }

    // -- Amount --
    $amount = floatval($issue['amount'] ?? 0);
    $mapped['amount']      = moneyFormatIndia(number_format($amount, 2, '.', ''));
    $mapped['amount_raw']  = $amount;

    // -- Description text (depends on issue/receipt type) --
    $desc = '';
    $type = intval($issue['type'] ?? 0);
    $issue_type = intval($issue['issue_type'] ?? 0);
    $rct_type = intval($issue['rct_type'] ?? 0);

    if ($type == 2) { // Receipt
        if ($rct_type == 8) {
            $desc = 'Petty Cash Receipt Against Issue No :' . ($issue['bills'] ?? '') . ' From ' . $mapped['customer_name'] . '-' . $mapped['customer_mobile'];
        } elseif ($is_eda == 1) {
            $desc = 'Received with thanks from ' . $mapped['customer_name'] . ' Towards Advance Bill No : ' . $mapped['bill_no'];
        } else {
            $desc = 'Received with thanks from ' . $mapped['customer_name'] . ' Advance proforma number : ' . $mapped['bill_no'];
        }
    } elseif ($type == 1) { // Issue
        if ($issue_type == 3) {
            $desc = 'Refund to ' . $mapped['customer_name'];
        } elseif ($issue_type == 1) {
            $desc = 'Payment Issue Voucher To ' . $mapped['customer_name'];
        } elseif ($issue_type == 2) {
            $desc = 'Paid to ' . $mapped['customer_name'] . ' Towards Issue Bill No : ' . $mapped['bill_no'];
        }
    }
    $mapped['description_text'] = strtoupper($desc);

    // -- Payment breakdown --
    $cash_amt = 0; $chq_amt = 0; $card_amt = 0;
    $rtgs_amt = 0; $imps_amt = 0; $neft_amt = 0; $upi_amt = 0;
    $total_pay = 0;

    foreach ($payment as $p) {
        $pamt = floatval($p['payment_amount'] ?? 0);
        $total_pay += $pamt;
        $mode = $p['payment_mode'] ?? '';
        $nb_type = $p['nb_type'] ?? '';

        if ($mode == 'Cash')             $cash_amt += $pamt;
        if ($mode == 'CHQ')              $chq_amt  += $pamt;
        if ($mode == 'DC' || $mode == 'CC') $card_amt += $pamt;
        if ($mode == 'NB') {
            if ($nb_type == 'RTGS') $rtgs_amt += $pamt;
            if ($nb_type == 'IMPS') $imps_amt += $pamt;
            if ($nb_type == 'NEFT') $neft_amt += $pamt;
            if ($nb_type == 'UPI')  $upi_amt  += $pamt;
        }
    }

    $mapped['cash_amount'] = moneyFormatIndia(number_format($cash_amt, 2, '.', ''));
    $mapped['chq_amount']  = moneyFormatIndia(number_format($chq_amt, 2, '.', ''));
    $mapped['card_amount'] = moneyFormatIndia(number_format($card_amt, 2, '.', ''));
    $mapped['rtgs_amount'] = moneyFormatIndia(number_format($rtgs_amt, 2, '.', ''));
    $mapped['imps_amount'] = moneyFormatIndia(number_format($imps_amt, 2, '.', ''));
    $mapped['neft_amount'] = moneyFormatIndia(number_format($neft_amt, 2, '.', ''));
    $mapped['upi_amount']  = moneyFormatIndia(number_format($upi_amt, 2, '.', ''));
    $mapped['total_amount']= moneyFormatIndia(number_format($total_pay, 2, '.', ''));

    $mapped['has_cash'] = ($cash_amt > 0);
    $mapped['has_chq']  = ($chq_amt > 0);
    $mapped['has_card'] = ($card_amt > 0);
    $mapped['has_rtgs'] = ($rtgs_amt > 0);
    $mapped['has_imps'] = ($imps_amt > 0);
    $mapped['has_neft'] = ($neft_amt > 0);
    $mapped['has_upi']  = ($upi_amt > 0);

    // -- Payment details loop data --
    $pay_rows = [];
    foreach ($payment as $p) {
        $pamt = floatval($p['payment_amount'] ?? 0);
        if ($pamt <= 0) continue;

        $mode = $p['payment_mode'] ?? '';
        $nb_type = $p['nb_type'] ?? '';
        $label = $mode;
        $details = '';

        if ($mode == 'Cash') {
            $label = ($is_eda == 2) ? 'PAYABLE' : 'CASH';
        } elseif ($mode == 'CHQ') {
            $label = 'CHEQUE';
            $details = 'Chq No: ' . ($p['cheque_no'] ?? '') . ' / ' . ($p['payment_ref_number'] ?? '') . ' Date: ' . ($p['cheque_date'] ?? '');
        } elseif ($mode == 'DC' || $mode == 'CC') {
            $label = 'CARD';
            $parts = [];
            if (!empty($p['card_no'])) $parts[] = 'Card No: ' . $p['card_no'];
            if (!empty($p['payment_ref_number'])) $parts[] = 'Appr code: ' . $p['payment_ref_number'];
            if (!empty($p['payment_date'])) $parts[] = 'Date: ' . $p['payment_date'];
            $details = implode(' / ', $parts);
        } elseif ($mode == 'NB') {
            $label = strtoupper($nb_type);
            $parts = [];
            if (!empty($p['device_name'])) $parts[] = $p['device_name'];
            if (!empty($p['payment_ref_number'])) $parts[] = 'Appr code: ' . $p['payment_ref_number'];
            if (!empty($p['net_banking_date'])) $parts[] = 'Date: ' . $p['net_banking_date'];
            $details = implode(' / ', $parts);
        }

        $pay_rows[] = [
            'pay_label'   => $label,
            'pay_details' => $details,
            'pay_amount'  => moneyFormatIndia(number_format($pamt, 2, '.', '')),
        ];
    }
    $mapped['issue_receipt_payments'] = $pay_rows;
    $mapped['has_payments'] = (count($pay_rows) > 0);

    // -- Advance adjustment --
    $adv_adj = $data['advance_adj_details'] ?? [];
    $adjusted_amt = 0;
    if (is_array($adv_adj)) {
        foreach ($adv_adj as $adj) {
            $adjusted_amt += floatval($adj['adjusted_amt'] ?? 0);
        }
    }
    $mapped['adjusted_amount'] = moneyFormatIndia(number_format($adjusted_amt, 2, '.', ''));
    $mapped['has_advance_adj'] = ($adjusted_amt > 0);

    // -- Grand total (payment + adjustment) --
    $grand_total = $total_pay + $adjusted_amt;
    $mapped['grand_total'] = moneyFormatIndia(number_format($grand_total, 2, '.', ''));

    // -- Amount in words --
    $CI =& get_instance();
    if (method_exists($CI->ret_billing_model ?? new stdClass(), 'no_to_words')) {
        $mapped['amount_in_words'] = 'Rupees ' . $CI->ret_billing_model->no_to_words($amount) . ' Only';
    } else {
        $mapped['amount_in_words'] = '';
    }

    // -- Deposit / Ref bill --
    $deposit = $data['deposit_type_bill_no'] ?? [];
    $mapped['has_ref_bill'] = (!empty($deposit['make_as_advance']) && $deposit['make_as_advance'] == 1 && !empty($deposit['bill_no']));
    $mapped['ref_bill_no']  = $deposit['bill_no'] ?? '';

    // -- Advance details --
    $adv_details = $data['receipt_adv_details'] ?? [];
    $adv_rows = [];
    if (is_array($adv_details) && count($adv_details) > 0) {
        foreach ($adv_details as $adv) {
            $adv_rows[] = [
                'adv_bill_no'     => $adv['bill_no'] ?? '',
                'adv_bill_date'   => $adv['bill_date'] ?? '',
                'adv_receipt_amt' => moneyFormatIndia(number_format(floatval($adv['receipt_amt'] ?? 0), 2, '.', '')),
                'adv_utilized'    => moneyFormatIndia(number_format(floatval($adv['utilized_amt'] ?? 0), 2, '.', '')),
                'adv_refund'      => moneyFormatIndia(number_format(floatval($adv['refund_amount'] ?? 0), 2, '.', '')),
                'adv_balance'     => moneyFormatIndia(number_format(floatval($adv['balance_amount'] ?? 0), 2, '.', '')),
            ];
        }
    }
    $mapped['issue_receipt_adv_details'] = $adv_rows;
    $mapped['has_adv_details'] = (count($adv_rows) > 0);

    // -- Conditional flags --
    $mapped['has_narration']  = (trim($issue['narration'] ?? '') != '' && $is_eda == 1);
    $mapped['has_employee']   = (!empty($issue['emp_name']));
    $mapped['is_eda_1']       = ($is_eda == 1);
    $mapped['is_eda_2']       = ($is_eda == 2);

    return $mapped;
}

// ═══════════════════════════════════════════════════════════════════════════
// Extra Placeholder Definitions — Fields that can't be auto-detected from
// sample data (e.g. empty loop arrays, nested _sub_rows).
// Called by print_template_model::_get_dynamic_fields_v2() to supplement
// the auto-introspection results for the designer variable dropdown.
// ═══════════════════════════════════════════════════════════════════════════
function tpl_get_extra_placeholders($category) {
    $fields = [];

    // Helper closure to build field entries
    $make_fields = function($cols, $group, $parent_loop, $cat) {
        $out = [];
        foreach ($cols as $f) {
            $out[] = [
                'placeholder_key'   => $f['key'],
                'placeholder_label' => $f['label'],
                'placeholder_group' => $group,
                'sample_value'      => '',
                'category'          => $cat,
                'field_type'        => 'text',
                'is_loop_field'     => 1,
                'parent_loop'       => $parent_loop
            ];
        }
        return $out;
    };

    // ── Billing categories (0-15) ──
    // Mock data has empty item arrays so auto-detection finds nothing.
    // Register all loop columns explicitly.
    if ($category >= 0 && $category <= 15) {

        // Common item columns (shared by sales_items, items, order_items)
        $item_cols = [
            ['key'=>'sno',            'label'=>'S.No'],
            ['key'=>'type',           'label'=>'Item Type'],
            ['key'=>'hsn_code',       'label'=>'HSN Code'],
            ['key'=>'description',    'label'=>'Description'],
            ['key'=>'purity',         'label'=>'Purity'],
            ['key'=>'qty',            'label'=>'Qty / Pieces'],
            ['key'=>'gross_wt',       'label'=>'Gross Weight'],
            ['key'=>'net_wt',         'label'=>'Net Weight'],
            ['key'=>'stone_wt',       'label'=>'Stone Weight'],
            ['key'=>'va_percent',     'label'=>'VA / Wastage %'],
            ['key'=>'va_content',     'label'=>'VA Content'],
            ['key'=>'mc',             'label'=>'Making Charge'],
            ['key'=>'rate',           'label'=>'Rate'],
            ['key'=>'amount',         'label'=>'Amount'],
            ['key'=>'taxable_amount', 'label'=>'Taxable Amount'],
            ['key'=>'cgst_amt',       'label'=>'CGST Amount'],
            ['key'=>'sgst_amt',       'label'=>'SGST Amount'],
            ['key'=>'igst_amt',       'label'=>'IGST Amount'],
            ['key'=>'item_total',     'label'=>'Item Total (Tax Incl)'],
            ['key'=>'total_stone_wt',     'label'=>'Total Stone Weight (All Items)'],
            ['key'=>'total_stone_amount', 'label'=>'Total Stone Amount (All Items)'],
        ];
        $fields = array_merge($fields, $make_fields($item_cols, 'Sales Items', 'items', $category));

        // Return items (unprefixed + prefixed)
        $return_cols = [
            ['key'=>'return_sno',            'label'=>'Return S.No'],
            ['key'=>'return_hsn_code',       'label'=>'Return HSN Code'],
            ['key'=>'return_description',    'label'=>'Return Description'],
            ['key'=>'return_purity',         'label'=>'Return Purity'],
            ['key'=>'return_qty',            'label'=>'Return Qty'],
            ['key'=>'return_gross_wt',       'label'=>'Return Gross Wt'],
            ['key'=>'return_net_wt',         'label'=>'Return Net Wt'],
            ['key'=>'return_va_percent',     'label'=>'Return VA %'],
            ['key'=>'return_va_content',     'label'=>'Return VA Content'],
            ['key'=>'return_mc',             'label'=>'Return MC'],
            ['key'=>'return_rate',           'label'=>'Return Rate'],
            ['key'=>'return_amount',         'label'=>'Return Amount'],
            ['key'=>'return_taxable_amount', 'label'=>'Return Taxable Amt'],
            ['key'=>'return_sub_total',      'label'=>'Return Sub Total'],
            ['key'=>'return_total_amount',   'label'=>'Return Total Amt'],
            ['key'=>'return_cgst_amt',       'label'=>'Return CGST'],
            ['key'=>'return_sgst_amt',       'label'=>'Return SGST'],
            ['key'=>'return_igst_amt',       'label'=>'Return IGST'],
            ['key'=>'return_stone_wt',          'label'=>'Return Stone Weight'],
            ['key'=>'return_stone_amount',      'label'=>'Return Stone Amount'],
            ['key'=>'return_stone_name',        'label'=>'Return Stone Name'],
            ['key'=>'return_stone_pieces',      'label'=>'Return Stone Pieces'],
            ['key'=>'return_stone_rate',        'label'=>'Return Stone Rate'],
            ['key'=>'return_stone_uom',         'label'=>'Return Stone UOM'],
            ['key'=>'return_stone_cal_type',    'label'=>'Return Stone Calc Type'],
            ['key'=>'return_stone_type',        'label'=>'Return Stone Type'],
            ['key'=>'return_stone_description', 'label'=>'Return Stone Description'],
        ];
        $fields = array_merge($fields, $make_fields($return_cols, 'Return Items', 'return_items', $category));

        // Old metal items (unprefixed + prefixed)
        $old_metal_cols = [
            ['key'=>'old_metal_type',           'label'=>'Old Metal Type'],
            ['key'=>'old_metal_sno',            'label'=>'Old Metal S.No'],
            ['key'=>'old_metal_hsn_code',       'label'=>'Old Metal HSN'],
            ['key'=>'old_metal_description',    'label'=>'Old Metal Description'],
            ['key'=>'old_metal_purity',         'label'=>'Old Metal Purity'],
            ['key'=>'old_metal_qty',            'label'=>'Old Metal Qty'],
            ['key'=>'old_metal_gross_wt',       'label'=>'Old Metal Gross Wt'],
            ['key'=>'old_metal_net_wt',         'label'=>'Old Metal Net Wt'],
            ['key'=>'old_metal_va_percent',     'label'=>'Old Metal VA %'],
            ['key'=>'old_metal_va_content',     'label'=>'Old Metal VA Content'],
            ['key'=>'old_metal_mc',             'label'=>'Old Metal MC'],
            ['key'=>'old_metal_rate',           'label'=>'Old Metal Rate'],
            ['key'=>'old_metal_amount',         'label'=>'Old Metal Amount'],
            ['key'=>'old_metal_taxable_amount', 'label'=>'Old Metal Taxable'],
            ['key'=>'dust_wt',                  'label'=>'Dust Weight'],
            ['key'=>'old_metal_stone_wt',        'label'=>'Old Metal Stone Weight'],
            ['key'=>'old_metal_dust_wt',         'label'=>'Old Metal Dust Weight'],
            ['key'=>'old_metal_stone_amount',       'label'=>'Old Metal Stone Amount'],
            ['key'=>'old_metal_stone_name',         'label'=>'Old Metal Stone Name'],
            ['key'=>'old_metal_stone_pieces',       'label'=>'Old Metal Stone Pieces'],
            ['key'=>'old_metal_stone_rate',         'label'=>'Old Metal Stone Rate'],
            ['key'=>'old_metal_stone_uom',          'label'=>'Old Metal Stone UOM'],
            ['key'=>'old_metal_stone_cal_type',     'label'=>'Old Metal Stone Calc Type'],
            ['key'=>'old_metal_stone_type',         'label'=>'Old Metal Stone Type'],
            ['key'=>'old_metal_stone_description',  'label'=>'Old Metal Stone Description'],
        ];
        $fields = array_merge($fields, $make_fields($old_metal_cols, 'Old Metal Items', 'old_metal_items', $category));

        // Stone Details sub-row variables (_sub_rows inside sales_items)
        $stone_cols = [
            ['key'=>'stone_name',           'label'=>'Stone Name'],
            ['key'=>'stone_pieces',         'label'=>'Stone Pieces'],
            ['key'=>'stone_wt',             'label'=>'Stone Weight'],
            ['key'=>'stone_rate',           'label'=>'Stone Rate'],
            ['key'=>'stone_amount',         'label'=>'Stone Amount'],
            ['key'=>'stone_uom',            'label'=>'Stone UOM'],
            ['key'=>'stone_cal_type',       'label'=>'Stone Calc Type'],
            ['key'=>'stone_cal_type_label', 'label'=>'Stone Calc Type Label (Weight/Fixed)'],
            ['key'=>'stone_type',           'label'=>'Stone Type'],
            ['key'=>'stone_type_label',     'label'=>'Stone Type Label (Diamond/Gem Stones/Others)'],
            ['key'=>'stone_description',    'label'=>'Stone Description (Formatted)'],
        ];
        $fields = array_merge($fields, $make_fields($stone_cols, 'Stone Details (Sub Row)', '_sub_rows', $category));

        // Stone Details standalone table variables (stone_items loop)
        $stone_table_cols = [
            ['key'=>'stone_sno',        'label'=>'Stone S.No'],
            ['key'=>'item_sno',         'label'=>'Parent Item S.No'],
            ['key'=>'item_description', 'label'=>'Parent Item Name'],
            ['key'=>'stone_name',           'label'=>'Stone Name'],
            ['key'=>'stone_pieces',         'label'=>'Stone Pieces'],
            ['key'=>'stone_wt',             'label'=>'Stone Weight'],
            ['key'=>'stone_rate',           'label'=>'Stone Rate'],
            ['key'=>'stone_amount',         'label'=>'Stone Amount'],
            ['key'=>'stone_uom',            'label'=>'Stone UOM'],
            ['key'=>'stone_cal_type',       'label'=>'Stone Calc Type'],
            ['key'=>'stone_cal_type_label', 'label'=>'Stone Calc Type Label (Weight/Fixed)'],
            ['key'=>'stone_type',           'label'=>'Stone Type'],
            ['key'=>'stone_type_label',     'label'=>'Stone Type Label (Diamond/Gem Stones/Others)'],
            ['key'=>'stone_description',    'label'=>'Stone Description (Formatted)'],
        ];
        $fields = array_merge($fields, $make_fields($stone_table_cols, 'Stone Details (Table)', 'stone_items', $category));
    }

    return $fields;
}
