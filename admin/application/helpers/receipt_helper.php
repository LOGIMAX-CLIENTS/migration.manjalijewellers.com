<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function get_receipt_data($id, $type = "", $is_softcopy = false)
{
    $CI = &get_instance();
    $CI->load->model('ret_billing_model');
    
    $data = array();
    $data['type'] = $type;
    $data['is_softcopy'] = $is_softcopy;
    
    $data['billing'] = $CI->ret_billing_model->getBillingDetails($id, $type);
    $data['payment'] = $CI->ret_billing_model->getPaymentDetails($id);
    $data['metal_rate'] = $CI->ret_billing_model->getBillingMetalrate($data['billing']['id_branch'], $data['billing']['bill_date']);
    $data['est_other_item'] = $CI->ret_billing_model->getOtherEstimateItemsDetails($id, $data['billing']['bill_type'], $type);
    $data['comp_details'] = $CI->ret_billing_model->getCompanyDetails($data['billing']['id_branch']);
    $data['settings'] = $CI->ret_billing_model->get_retSettings();
    $data['receiptDetails'] = $CI->ret_billing_model->get_billing_advance_details($id, $data['billing']['created_on']);
    
    $CI->load->model('admin_settings_model');
    $dCData = $CI->admin_settings_model->getBranchDayClosingData($data['billing']['id_branch']);
    $date = date_create($dCData['entry_date']);
    $date = date_format($date, "d-m-Y");

    if ($data['billing']['bill_date'] == $date) {
        $data['billing']['is_today'] = 1;
    } else {
        $data['billing']['is_today'] = 0;
    }
    
    // Add background image for softcopy
    if ($is_softcopy) {
        $data['background_image'] = base_url() . 'assets/img/receipt_background.png';
    }
    
    // Type determination logic
    if (sizeof($data['est_other_item']['item_details']) == 0 && $data['type'] == '') {
        if (sizeof($data['est_other_item']['old_matel_details']) > 0) {
            $data['type'] = 'p';
        } else if (sizeof($data['est_other_item']['return_details']) > 0 || (isset($data['est_other_item']['sales_ret_trans_details']) && sizeof($data['est_other_item']['sales_ret_trans_details']) > 0)) {
            $data['type'] = 'sr';
        } else if ($data['billing']['bill_type'] == 5) {
            $data['type'] = 'od';
        }
    }
    
    // Additional data processing for both views
    $data['invoicesplit'] = array();
    foreach ($data['est_other_item']['item_details'] as $key => $item) {
        $data['invoicesplit'][$item['invoiceno']][$key] = $item;
    }
    ksort($data['invoicesplit'], SORT_NUMERIC);
    
    $data['oldmetalsplit'] = array();
    foreach ($data['est_other_item']['old_matel_details'] as $key => $item) {
        $data['oldmetalsplit'][$item['pur_refId']][$key] = $item;
    }
    ksort($data['oldmetalsplit'], SORT_NUMERIC);
    
    return $data;
}

function generate_qr_code($bill_id)
{
    $CI = &get_instance();
    $CI->load->library('phpqrcode/qrlib');
    
    $SERVERFILEPATH = 'bill_qrcode';
    if (!is_dir($SERVERFILEPATH)) {
        mkdir($SERVERFILEPATH, 0777, TRUE);
    }
    
    $content = base_url() . "index.php/admin_app_api/printbill/" . $bill_id;
    $file_name = $SERVERFILEPATH . '/' . $bill_id . ".png";
    
    QRcode::png($content, $file_name);
    return $bill_id;
}

function map_billing_data_to_template($data) {
    // Flatten the complex $data array function map_billing_data_to_template($data) {
    $mapped = [];
    $billing = $data['billing'];
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
    $mapped['qr_code_image'] = $billing['bqrcodeimage'] ?? ''; // Base64 image data

    // -- Metal Rates --
    $mapped['gold_rate'] = number_format(($billing['goldrate_22ct'] > 0 ? $billing['goldrate_22ct'] : ($data['metal_rate']['goldrate_22ct'] ?? 0)), 2);
    $mapped['gold_rate_18ct'] = number_format(($billing['goldrate_18ct'] > 0 ? $billing['goldrate_18ct'] : ($data['metal_rate']['goldrate_18ct'] ?? 0)), 2);
    $mapped['gold_rate_24ct'] = number_format(($billing['goldrate_24ct'] > 0 ? $billing['goldrate_24ct'] : ($data['metal_rate']['goldrate_24ct'] ?? 0)), 2);
    $mapped['silver_rate'] = number_format(($billing['silverrate_1gm'] > 0 ? $billing['silverrate_1gm'] : ($data['metal_rate']['silverrate_1gm'] ?? 0)), 2);



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

    $mapped['items'] = []; // Default empty
    $total_gross_wt = 0;
    $total_net_wt = 0;
    $total_pieces = 0;
    
    // Aggregates for footer
    $total_taxable_amt = 0;
    $total_sgst = 0;
    $total_cgst = 0;
    $total_igst = 0;

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
    if (in_array($billing['bill_type'], $sales_bill_types) && !empty($items_data['return_details']) && empty($items_data['item_details'])) {
        $items_data['item_details'] = $items_data['return_details'];
        $items_data['return_details'] = []; // Clear to prevent double-counting in return processing
    }
    if (!empty($items_data['item_details'])) {
        foreach ($items_data['item_details'] as $item) {
            // Calculations from view
            $item_total_tax = $item['item_total_tax'];
            $item_cost = $item['item_cost'];
            $item_taxable = $item_cost - $item_total_tax;
            
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
                'description' => $item['product_name'] . ($item['size_name'] ? '-' . $item['size_name']."royal enfield nilkkfjnekrjf kjenrkvj" : ''),
                'purity' => $item['purname'],
                'qty' => $item['piece'],
                'gross_wt' => number_format($item['gross_wt'], 3),
                'net_wt' => number_format($item['net_wt'], 3),
                'va_percent' => $item['wastage_percent'],
                'va_content' => $va_text,
                'mc' => number_format($mc, 2),
                'rate' => number_format($item['rate_per_grm'], 2),
                'amount' => number_format($item_taxable, 2), // Taxable amount (item_cost - tax) — matches legacy view
                'taxable_amount' => number_format($item_taxable, 2),
                'cgst_amt' => number_format($item['total_cgst'], 2),
                'sgst_amt' => number_format($item['total_sgst'], 2),
                'igst_amt' => number_format($item['total_igst'], 2)
            ];
            
            $sales_items[] = $item_data;
            $mapped_items[] = $item_data;

            // Map Stone Details (Flattened as rows, typical for invoices)
            if (!empty($item['stone_details'])) {
                foreach ($item['stone_details'] as $stone) {
                     $stone_data = [
                        'type' => 'Stone',
                        'sno' => '',
                        'hsn_code' => '',
                        'description' => $stone['stone_name'],
                        'purity' => '',
                        'qty' => $stone['pieces'], // or pieces
                        'gross_wt' => '',
                        'net_wt' => number_format($stone['wt'], 3),
                        'va_percent' => '',
                        'va_content' => '',
                        'mc' => '',
                        'rate' => number_format($stone['rate_per_gram'], 2),
                        'amount' => number_format($stone['amount'], 2),
                        'taxable_amount' => ''
                    ];
                    
                    $sales_items[] = $stone_data;
                    $mapped_items[] = $stone_data;
                }
            }

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

    // 2. Sales Return Items (Credit Note) - Corrected Key from View
    if (!empty($items_data['sales_ret_trans_details'])) {
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
                'rate' => number_format($item['rate_per_grm'], 2), 
                'amount' => number_format($item_cost - $item_total_tax, 2),
                'taxable_amount' => number_format($item_cost - $item_total_tax, 2),
                'cgst_amt' => number_format($item['total_cgst'] ?? 0, 2),
                'sgst_amt' => number_format($item['total_sgst'] ?? 0, 2),
                'igst_amt' => number_format($item['total_igst'] ?? 0, 2),
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
                'return_rate' => number_format($item['rate_per_grm'], 2), 
                'return_amount' => number_format($item_cost - $item_total_tax, 2),
                'return_taxable_amount' => number_format($item_cost - $item_total_tax, 2),
                'return_sub_total' => number_format($item_cost - $item_total_tax, 2),
                'return_total_amount' => number_format($item_cost, 2),
                'return_cgst_amt' => number_format($item['total_cgst'] ?? 0, 2),
                'return_sgst_amt' => number_format($item['total_sgst'] ?? 0, 2),
                'return_igst_amt' => number_format($item['total_igst'] ?? 0, 2)
            ];
            
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
                'rate' => number_format($item['rate_per_gram'], 2), 
                'amount' => number_format($item['amount'], 2),
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
                'old_metal_rate' => number_format($item['rate_per_gram'], 2), 
                'old_metal_amount' => number_format($item['amount'], 2),
                'old_metal_taxable_amount' => 0,
                'stone_wt' => number_format($item['stone_wt'] ?? 0, 3),
                'dust_wt'  => number_format($item['dust_wt']  ?? 0, 3),
            ];
            
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
                'rate' => number_format($item['rate_per_grm'], 2),
                'amount' => number_format($item_cost, 2),
                'taxable_amount' => number_format($item_cost - $item_total_tax, 2)
            ];
            
            $transfer_items[] = $transfer_data;
            $mapped_items[] = $transfer_data;
         }
    }


    $mapped['items'] = $mapped_items;
    $mapped['sales_items'] = $sales_items;
    $mapped['sales'] = $sales_items; // Alias for user's template change
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
                'mc' => number_format($mc, 2),
                'rate' => number_format($od['rate_per_gram'], 2),
                'amount' => number_format($rate_without_gst - $stone_amount, 2),
                'taxable_amount' => number_format($rate_without_gst, 2),
                'cgst_amt' => number_format($od['total_cgst'], 2),
                'sgst_amt' => number_format($od['total_sgst'], 2),
                'igst_amt' => number_format($od['total_igst'], 2),
            ];

            // Add stone sub-rows
            $order_mapped_items[] = $item_data;
            if (!empty($od['stones'])) {
                foreach ($od['stones'] as $stone) {
                    $order_mapped_items[] = [
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
                        'rate' => number_format($stone['rate_per_gram'] ?? $stone['st_rate'] ?? 0, 2),
                        'amount' => number_format($stone['amount'] ?? $stone['st_price'] ?? 0, 2),
                        'taxable_amount' => '',
                    ];
                }
            }

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
        $mapped['od_total_amount'] = number_format($od_total_amount, 2);
        $mapped['od_approx_total'] = number_format($od_total_amount + $od_total_cgst + $od_total_sgst + $od_total_igst, 2);
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
    $mapped['total_return_amount'] = number_format($ret_amt, 2);

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
    $mapped['total_old_metal_amount'] = number_format($om_amt, 2);

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
    $mapped['total_transfer_amount'] = number_format($tr_amt, 2);

    // -- Order / Advance Totals --
    $mapped['order_amount'] = number_format($billing['tot_bill_amount'], 2);
    $mapped['total_paid'] = number_format($billing['tot_amt_received'], 2);
    if($billing['tot_amt_received']!=0){
        $mapped['balance_amount'] = number_format($billing['tot_bill_amount'] - $billing['tot_amt_received'], 2);
    }else{
        $mapped['balance_amount'] = 0;
    }

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
    // This ensures two cheques appear as two separate rows in the template loop.
    // Scalar {{pay_cheque}}, {{pay_cash}} etc. retain the aggregated values (set below).
    $payment_methods = [];
    $payment_records2 = isset($data['payment']['pay_details']) ? $data['payment']['pay_details'] : [];
    foreach ($payment_records2 as $pay) {
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

        // ── Reference string format per mode (matches screenshot) ──────────
        if ($raw_mode === 'CHQ') {
            $label = 'Cheque';
            $ref_parts = [];
            if ($cheque_no)  $ref_parts[] = 'Chq No: '   . $cheque_no;
            if ($cheque_dt)  $ref_parts[] = 'Date: '     . $cheque_dt;
            $ref = implode(' / ', $ref_parts);
        } elseif ($raw_mode === 'DC') {
            $label = 'Debit Card';
            $ref_parts = [];
            if ($card_no)    $ref_parts[] = 'Card No: '  . $card_no;
            if ($appr_code)  $ref_parts[] = 'Appr code: '. $appr_code;
            $ref = implode(' / ', $ref_parts);
        } elseif ($raw_mode === 'CC') {
            $label = 'Credit Card';
            $ref_parts = [];
            if ($card_no)    $ref_parts[] = 'Card No: '  . $card_no;
            if ($appr_code)  $ref_parts[] = 'Appr code: '. $appr_code;
            $ref = implode(' / ', $ref_parts);
        } elseif ($raw_mode === 'NB') {
            $label = $nb_type !== '' ? $nb_type : 'Net Banking';
            $ref   = $appr_code ? 'Ref: ' . $appr_code : '';
        } else {
            $label = ucfirst(strtolower($raw_mode));
            $ref   = '';
        }

        $payment_methods[] = [
            'pay_method_label'    => $label,
            'pay_method_amount'   => number_format($amt, 2),
            'pay_method_ref'      => $ref,            // combined formatted reference string

            // ── Individual variables (usable as separate template columns) ──
            'pay_mode_raw'        => $raw_mode,       // CHQ / DC / CC / NB / CASH
            // Cheque
            'pay_cheque_no'       => $cheque_no,      // e.g. 2201
            'pay_cheque_date'     => $cheque_dt,      // e.g. 27-05-2026
            'pay_bank_name'       => $bank_name,      // e.g. HDFC Bank
            // Card
            'pay_card_no'         => $card_no,        // e.g. 111
            'pay_card_date'       => $pay_date,       // e.g. 27-05-2026
            'pay_appr_code'       => $appr_code,      // e.g. 909
            // Net banking / UPI
            'pay_nb_type'         => $nb_type,        // UPI / NEFT / IMPS / RTGS
            'pay_ref_number'      => $appr_code,      // same as appr_code for NB/UPI
            // Universal
            'pay_date'            => $pay_date,       // payment date for any mode
        ];

        // Set aggregated scalar placeholders (backward compat for {{pay_cheque}} etc.)
        $info2 = isset($mode_map[$raw_mode]) ? $mode_map[$raw_mode] : null;
        if ($info2) {
            $prev = floatval(str_replace(',', '', $mapped[$info2['key']] ?? '0'));
            $mapped[$info2['key']] = number_format($prev + $amt, 2);
        } elseif ($raw_mode === 'NB' && $nb_type !== '') {
            $nb_key = 'pay_' . strtolower($nb_type);
            $prev = floatval(str_replace(',', '', $mapped[$nb_key] ?? '0'));
            $mapped[$nb_key] = number_format($prev + $amt, 2);
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
    $mapped['sales_sub_total'] = number_format($sales_sub_total, 2);
    // -- Return-only totals (for Konva Return table footer) --
    $mapped['return_total_qty'] = $return_total_pieces;
    $mapped['return_total_gross_wt'] = number_format($return_total_gross_wt, 3);
    $mapped['return_total_net_wt'] = number_format($return_total_net_wt, 3);
    $mapped['return_sub_total'] = number_format($return_sub_total, 2);
    
    $mapped['sub_total'] = number_format($total_taxable_amt, 2);
    $mapped['sgst_amount'] = number_format($total_sgst, 2);
    $mapped['cgst_amount'] = number_format($total_cgst, 2);
    $mapped['igst_amount'] = number_format($total_igst, 2);

    // Handle other charges
    $round_off = $billing['round_off_amt'];
    $handling_charges = $billing['handling_charges'] ?? 0;
    $mapped['round_off'] = number_format($round_off, 2);
    $mapped['handling_charges'] = number_format($handling_charges, 2);

    $grand_total = $total_taxable_amt + $total_sgst + $total_cgst + $total_igst + $round_off + $handling_charges;
    $mapped['grand_total'] = number_format($grand_total, 2);
    $mapped['remark'] = $billing['remark'] ?? '';

    // $mapped['grand_total'] = number_format($billing['tot_bill_amount'], 2);
    $mapped['amount_in_words'] = convert_number_to_words($billing['tot_bill_amount']);
    
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
                'mode'        => $mode_label,
                'amount'      => number_format($pay['payment_amount'], 2),
                'reference'   => $ref_info,
                // Cheque fields
                'cheque_no'   => $pay['cheque_no'] ?? '',
                'cheque_date' => $pay['cheque_date'] ?? '',
                'bank_name'   => $pay['bank_name'] ?? '',
                'bank_branch' => $pay['bank_branch'] ?? '',
                // Card fields
                'card_no'     => $pay['card_no'] ?? '',
                'card_date'   => $pay['payment_date'] ?? '',
                'appr_code'   => $pay['payment_ref_number'] ?? '',
                // Net banking fields
                'nb_type'     => strtoupper($pay['NB_type'] ?? $pay['transfer_type'] ?? ''),
                'ref_number'  => $pay['payment_ref_number'] ?? '',
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
    
    // Add Due Amount if any
    $due_amt = $billing['tot_bill_amount'] - $billing['tot_amt_received'];
    if ($due_amt > 0) {
        $payment_breakdown[] = [
            'mode' => 'DUE AMOUNT',
            'amount' => number_format($due_amt, 2),
            'reference' => 'Due Date: ' . ($billing['credit_due_date'] ?? '-')
        ];
    }
    
    $mapped['payment_breakdown'] = $payment_breakdown;
    
    $mapped['pay_cash'] = number_format($pay_cash, 2);
    $mapped['pay_card'] = number_format($pay_card, 2);
    $mapped['pay_upi'] = number_format($pay_upi, 2);
    $mapped['pay_cheque'] = number_format($pay_cheque, 2);
    $mapped['pay_neft'] = number_format($pay_neft, 2);
    $mapped['pay_rtgs'] = number_format($pay_rtgs, 2);
    $mapped['pay_imps'] = number_format($pay_imps, 2);
    
    $mapped['pay_due'] = number_format($due_amt, 2);

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
    $mapped['paid_amount'] = number_format($billing['tot_amt_received'], 2);
    // For bill_type 5 (Order Receipt), tot_bill_amount is 0, so use grand_total instead
    if ($billing['bill_type'] == 5) {
        $balance_val = $grand_total - $billing['tot_amt_received'];
    } else {
        $balance_val = $billing['tot_bill_amount'] - $billing['tot_amt_received'];
    }
    $mapped['balance_amount'] = number_format($balance_val, 2);

    // -- Order Delivery Specifics --
    $mapped['order_delivery_items'] = $sales_items; // Reuse sales items (contains MC)
    
    // Map Advance Details (History of advances utilized)
    $advance_details = [];
    $total_advance_adjusted = 0;
    if (!empty($data['receiptDetails'])) {
        foreach ($data['receiptDetails'] as $row) {
            $amt = $row['adjuseted_amt'];
            $advance_details[] = [
                'date' => $row['bill_date'], // Already formatted in model query as d-m-Y? query says date_format(...,'%d-%m-%Y')
                'amount' => number_format($amt, 2)
            ];
            $total_advance_adjusted += $amt;
        }
    }
    $mapped['advance_details'] = $advance_details;
    $mapped['order_advance_adj'] = number_format($total_advance_adjusted, 2);
    
    // Net Cash Received (Total Paid - Advance Adjusted)
    // Assuming tot_amt_received includes the utilized advance amount (which is standard in many billing systems)
    // If tot_amt_received is PURELY the new payment, then this logic might need adjustment.
    // However, usually 'Total Paid' = Sum of all receipts (Cash + Card + Adv Adj).
    // So New Cash = Total Paid - Adv Adj.
    $cash_received_val = $billing['tot_amt_received'] - $total_advance_adjusted;
    $mapped['cash_received'] = number_format($cash_received_val, 2);

    // -- Exchange Amount (alias for old metal total — legacy compatibility) --
    $mapped['exchange_amount'] = number_format($om_amt, 2);



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
                'amount' => number_format($opening_amt, 2)
            ];

            // Collections
            if (!empty($credit_hist['collections'])) {
                foreach ($credit_hist['collections'] as $coll) {
                    $amt = $coll['tot_amt_received'];
                    $balance -= $amt;
                    $history_lines[] = [
                        'label' => 'Credit Collection Ref Bill No. ' . $coll['credit_coll_refno'],
                        'date' => date('d-m-Y', strtotime($coll['bill_date'])),
                        'amount' => number_format($amt, 2)
                    ];
                }
            }
            
            // Add reference details for the MAIN table row
            $mapped['credit_ref_bill_no'] = $orig_ref;
            $mapped['credit_ref_bill_date'] = date('d-m-Y', strtotime($orig['bill_date']));
        }
        
        $mapped['credit_collection_history'] = $history_lines;
        $mapped['credit_balance_amount'] = number_format($balance, 2);
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
                'purchase_rate' => number_format($item['rate_per_gram'], 2),
                'purchase_amount' => number_format($amt, 2)
            ];
        }
    }
    $mapped['total_old_qty'] = $items_data['total_old_qty'];
    $mapped['total_old_gross_wt'] = $items_data['total_old_gross_wt'];
    $mapped['total_old_net_wt'] = $items_data['total_old_net_wt'];
    $mapped['total_old_amount'] = $items_data['total_old_amount'];
    $mapped['purchase_items'] = $purchase_items;
    $mapped['purchase_total_amount'] = number_format($total_purchase_amt, 2);
    // Cash Paid for purchase is typically the total amount received/paid out.
    // In Purchase bills, tot_amt_received is often used to track what we paid the customer.
    // If it's negative or positive depends on implementation, usually positive in the field but logically out.
    // We'll use the mapped total_paid or just reuse total_purchase_amt if full payment is assumed, 
    // but better to use the actual payment record.
    $mapped['purchase_cash_paid'] = number_format($billing['tot_amt_received'], 2);

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
    
    // Purchase Invoice No
    // Often for exchange, there isn't a separate purchase bill NO unless generated.
    // We will use a placeholder or derived format if not found.
    // Assuming it might be same as invoice no or related.
    $mapped['purchase_invoice_no'] = isset($billing['purchase_ref_no']) ? $billing['purchase_ref_no'] : $invoice_no . '-P'; 

    // -- Chit Pre Close Specifics --
    $chit_pre_close_items = [];
    $chit_pre_close_total = 0;
    
    // Check if chit_details exists in items_data (passed from controller/model)
    // If not found, check if it's in a different key or needs to be fetched.
    // Assuming items_data['chit_details'] is populated as per Ret_billing_model snippet.
    if (!empty($items_data['chit_details'])) {
        $sno = 1;
        foreach ($items_data['chit_details'] as $item) {
            $amt = isset($item['amount']) ? $item['amount'] : (isset($item['utilized_amt']) ? $item['utilized_amt'] : 0);
            $chit_pre_close_total += $amt;
            
            $ref = isset($item['chit_ref_no']) ? $item['chit_ref_no'] : (isset($item['ref_no']) ? $item['ref_no'] : '-');
            
            $chit_pre_close_items[] = [
                'sno' => $sno++,
                'ref_no' => $ref,
                'amount' => number_format($amt, 2)
            ];
        }
    }
    
    $mapped['chit_pre_close_items'] = $chit_pre_close_items;
    $mapped['chit_pre_close_total'] = number_format($chit_pre_close_total, 2);

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
                 'rate' => number_format($rate, 2),
                 'amount' => number_format($amount, 2),
                 'taxable_amount' => number_format($amount, 2),
                 'cgst_amt' => number_format($item_cgst, 2),
                 'sgst_amt' => number_format($item_sgst, 2),
                 'igst_amt' => number_format($item_igst, 2),
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
                 'return_rate' => number_format($rate, 2),
                 'return_amount' => number_format($amount, 2),
                 'return_taxable_amount' => number_format($amount, 2),
                 'return_total_amount' => number_format($item_cost, 2),
                 'return_cgst_amt' => number_format($item_cgst, 2),
                 'return_sgst_amt' => number_format($item_sgst, 2),
                 'return_igst_amt' => number_format($item_igst, 2)
             ];
             
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
    $mapped['sales_return_total'] = number_format($sales_return_total, 2);
    
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
    $mapped['total_return_amount'] = number_format($ret_amt, 2);
    $mapped['total_return_cgst'] = number_format($ret_cgst, 2);
    $mapped['total_return_sgst'] = number_format($ret_sgst, 2);
    $mapped['total_return_igst'] = number_format($ret_igst, 2);
    $mapped['total_return_tax'] = number_format($ret_cgst + $ret_sgst + $ret_igst, 2);
    $mapped['total_return_amount_with_gst'] = number_format($ret_amt_with_gst, 2);
    $mapped['sales_return_total'] = number_format($sales_return_total, 2);
    
    // Re-assign tax totals after return_details processing
    // (return_details items accumulate tax AFTER the initial assignment at line ~692)
    $mapped['sub_total'] = number_format($total_taxable_amt, 2);
    $mapped['sgst_amount'] = number_format($total_sgst, 2);
    $mapped['cgst_amount'] = number_format($total_cgst, 2);
    $mapped['igst_amount'] = number_format($total_igst, 2);
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
    $mapped['total_sales_amount'] = moneyFormatIndia(number_format($calc_sales_total, 2, '.', ''));
    
    $mapped['lbl_sales_amount'] = ($grand_total > 0) ? number_format($grand_total, 2) : '';
    $mapped['lbl_exchange_amount'] = ($ret_amt_with_gst > 0) ? number_format($ret_amt_with_gst, 2) : '';
    $mapped['lbl_return_amount'] = ($ret_amt > 0) ? number_format($ret_amt, 2) : '';
    
    // Recalculate net_amount now that $ret_amt is available
    // For Order Delivery (bill_type 9), use return amount WITH GST since grand_total includes GST
    if ($billing['bill_type'] == 9) {
        $net_amount_val = $grand_total - $total_purchase_amt - $ret_amt_with_gst;
    } else {
        $net_amount_val = $grand_total - $total_purchase_amt - $ret_amt;
    }
    $mapped['net_amount'] = number_format($net_amount_val, 2);
    
    $net_amt = $calc_sales_total - $sales_return_total;
    // If tax is involved, it complicates. The image shows "Total" 82,143 (Sales)
    // Then Exchange 47,792.
    // Net Amount 34,350.
    // 82143 - 47792 = 34351. Matches roughly.
    $mapped['lbl_net_amount'] = number_format($net_amt, 2);
    
    // Sales Return Invoice No - might be a separate field or same invoice?
    // User image: SALES RETURN INVOICE NO : LE ZILVER...-SR-...
    // This implies the current bill document IS the sales return invoice?
    // Or is it an invoice that HAS a sales return?
    // If bill_type specific? 
    // Let's assume the variable $billing['s_ret_refno'] exists.
    $mapped['s_ret_invoice_no'] = isset($billing['s_ret_refno']) ? $billing['s_ret_refno'] : '';

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
                'rate' => number_format($item['rate'], 2),
                'amount' => number_format($item['rate'] - $item['repair_tot_tax'], 2) // Taxable? Or total? View uses rate - tax
             ];
             $total_repair_amount += ($item['rate'] - $item['repair_tot_tax']);
        }
    }
    $mapped['repair_order_items'] = $repair_order_items;
    $mapped['repair_order_total'] = number_format($total_repair_amount, 2);

    // -- Order Advance Adjustments (Order Adj) --
    $order_advance_entries = [];
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
            }
        }
    }
    $mapped['order_advance_entries'] = $order_advance_entries;
    
    // -- Chit Details (Generic) --
    $chit_general_items = [];
    $chit_general_total = 0;
    if (!empty($items_data['chit_details'])) {
        $sno = 1;
        foreach ($items_data['chit_details'] as $chit) {
            $amt = $chit['utilized_amt'];
            $chit_general_items[] = [
                'sno' => $sno++,
                'ref_no' => $chit['scheme_acc_number'],
                'amount' => moneyFormatIndia($amt)
            ];
             $chit_general_total += $amt;
        }
    }
    $mapped['chit_general_items'] = $chit_general_items;
    $mapped['chit_general_total'] = moneyFormatIndia($chit_general_total);

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
                'receipt_amount'  => number_format($val['tot_receipt_amount'] ?? 0, 2),
                'adjusted_amount' => number_format($val['adjuseted_amt'] ?? 0, 2),
                'utilized_amount' => number_format($val['tot_utilized_amt'] ?? 0, 2),
                'refund_amount'   => number_format($val['refund_amt'] ?? 0, 2),
                'balance_amount'  => number_format($val['bal_amt'] ?? 0, 2),
            ];
            $receipt_adj_total_amt += floatval($val['tot_receipt_amount'] ?? 0);
            $receipt_adj_total_utilized += floatval($val['tot_utilized_amt'] ?? 0);
            $receipt_adj_total_refund += floatval($val['refund_amt'] ?? 0);
            $receipt_adj_total_balance += floatval($val['bal_amt'] ?? 0);
        }
    }
    $mapped['receipt_adjustment_items'] = $receipt_adj_items;
    $mapped['receipt_adj_total_amount'] = number_format($receipt_adj_total_amt, 2);
    $mapped['receipt_adj_total_utilized'] = number_format($receipt_adj_total_utilized, 2);
    $mapped['receipt_adj_total_refund'] = number_format($receipt_adj_total_refund, 2);
    $mapped['receipt_adj_total_balance'] = number_format($receipt_adj_total_balance, 2);
    $mapped['has_receipt_adjustment'] = (count($receipt_adj_items) > 0);

    // -- Tax Detail Breakdown (HSN-wise tax split) --
    // Matches bill_format_2.php lines 2688-2730
    $tax_detail_items = [];
    if (!empty($items_data['tax_details'])) {
        $sno = 1;
        foreach ($items_data['tax_details'] as $item) {
            $item_taxable = number_format((float)$item['item_cost'] - $item['item_total_tax'], 2, '.', '');
            $tax_pct = $item_taxable > 0 ? number_format(($item['item_total_tax'] * 100) / $item_taxable, 2) : '0.00';
            $tax_detail_items[] = [
                'tax_sno'       => $sno++,
                'tax_hsn'       => $item['hsn_code'] ?? '',
                'taxable_value' => number_format((float)$item_taxable, 2),
                'cgst_rate'     => ($item['total_cgst'] > 0 ? number_format($tax_pct / 2, 2) . '%' : '0'),
                'cgst_amount'   => number_format($item['total_cgst'], 2),
                'sgst_rate'     => ($item['total_sgst'] > 0 ? number_format($tax_pct / 2, 2) . '%' : '0'),
                'sgst_amount'   => number_format($item['total_sgst'], 2),
                'igst_rate'     => ($item['total_igst'] > 0 ? number_format($tax_pct, 2) . '%' : '0'),
                'igst_amount'   => number_format($item['total_igst'], 2),
                'tax_total'     => number_format($item['item_cost'], 2),
            ];
        }
    }
    $mapped['tax_detail_items'] = $tax_detail_items;
    $mapped['has_tax_detail_items'] = (count($tax_detail_items) > 0);

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
    $mapped['chit_adj'] = number_format($chit_general_total, 2);
    $mapped['has_chit_adj'] = ($chit_general_total > 0);
    
    // Advance Adjustment: advance deposit utilized from customer's advance account
    $advance_deposit_val = isset($billing['advance_deposit']) ? floatval($billing['advance_deposit']) : 0;
    $mapped['advance_adj'] = number_format($advance_deposit_val, 2);
    $mapped['has_advance_adj'] = ($advance_deposit_val > 0);
    
    // Order Advance Adjustment: already mapped as order_advance_adj (from receiptDetails)
    $mapped['order_advance_adj'] = number_format($total_advance_adjusted, 2);
    $mapped['has_order_advance_adj'] = ($total_advance_adjusted > 0);
    
    // Receipt Adjustment: total issue/receipt (IR) adjustment amount
    $receipt_adj_val = isset($billing['tot_amt_received']) ? floatval($billing['tot_amt_received']) : 0;
    // Only show receipt adj if there's a meaningful amount and it's different from what we already show
    $mapped['receipt_adj'] = number_format($receipt_adj_val, 2);
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

function convert_number_to_words($number) {
    $no = floor($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen($no);
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
    $result = implode('', $str);
    $points = ($point) ?
        "." . $words[$point / 10] . " " . 
              $words[$point = $point % 10] : '';
    return $result . "Rupees Only";
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
function map_estimation_data_to_template($data) {
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

    if (!empty($items_data['item_details'])) {
        foreach ($items_data['item_details'] as $item) {
            $item_total_tax = floatval($item['item_total_tax'] ?? 0);
            $item_cost      = floatval($item['item_cost'] ?? 0);
            $payable_no_tax = $item_cost - $item_total_tax;

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

            // VA percent
            $va_percent = 0;
            if ($item['net_wt'] > 0) {
                $va_percent = round(($wast_wgt / $item['net_wt']) * 100, 2);
            }

            // Stone amount
            $stone_amount = 0;
            if (!empty($item['stone_details'])) {
                foreach ($item['stone_details'] as $stone) {
                    $stone_amount += floatval($stone['price'] ?? 0);
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
    if (function_exists('convert_number_to_words')) {
        $mapped['amount_in_words'] = convert_number_to_words($total_cost);
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

    return $mapped;
}

/**
 * Map Issue Receipt data to flat template variables for the print template engine.
 *
 * @param array $data  Controller data with keys: issue, comp_details, payment, receipt_adv_details,
 *                     advance_adj_details, deposit_type_bill_no, metal_rate
 * @return array       Flat key-value map for template rendering
 */
function map_issue_receipt_data_to_template($data) {
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