<?php
class Formlogger {

    private $CI;

    public function __construct() {

        $this->CI = & get_instance();

    }

    public function logFormData() {

        $uri_data = $this->get_uri();

        $log_form = "";

        $log_operation = "";

        $seg_array = $this->CI->uri->segment_array();

        $request_uri = implode('/', $seg_array);

        $url_match = false;

        foreach($uri_data as $uri) {

            $url_arr = explode('/', $uri['url']);

            $log_form = $uri['form'];

            $log_operation = $uri['operation'];

            $i = 0;

            $segment_match1 = array();

            foreach($url_arr as $url_seg1) {

                if($url_seg1 != "?") {

                    $segment_match1[$i] = false;

                    $j = 0;

                    foreach($seg_array as $segm1) {

                        if($i == $j) {

                            if($url_seg1 == "*" ? true : ($url_seg1 == $segm1)){

                                $segment_match1[$i] = true;

                            }

                            break;

                        }

                        $j++;

                    }

                } else {

                    $segment_match1[$i] = true;

                }

                $i++;

            }


            $i = 0;

            $segment_match2 = array();

            foreach($seg_array as $segm2) {

                $segment_match2[$i] = false;

                $j = 0;

                foreach($url_arr as $url_seg2) {

                    if($i == $j) {

                        if(($url_seg2 == "*" || $url_seg2 == "?") ? true : ($segm2 == $url_seg2)){

                            $segment_match2[$i] = true;

                        }

                        break;

                    }

                    $j++;

                }

                $i++;

            }

            /*print_r($segment_match1);

            print_r($segment_match2);exit;*/

            if(in_array(false, $segment_match1) || in_array(false, $segment_match2)) {

                $url_match = false;

            } else {

                $url_match = true;

                break;

            }

        }

       // echo "url match ".$url_match;exit;

        if ($url_match) {

            $logData = array(

                'log_datetime'  => date('Y-m-d H:i:s'),

                'log_emp_id'    => $this->CI->session->userdata('uid'),

                'log_form'      => $log_form,

                'log_operation' => $log_operation,

                'log_url'       => $request_uri,

                'log_ip'        => $_SERVER['REMOTE_ADDR'],

                'log_useragent' => $_SERVER['HTTP_USER_AGENT'],

                'log_data'      => json_encode(array_merge($_REQUEST, $_FILES)),

            );

            $this->CI->db->insert("form_logger", $logData);

        }

    }

    public function get_uri() {

        // form = Form Name, operation = action of that form submit, value = form URI,

        // Dynamic values like update ID or delete ID -> Replace with * , Optional values -> Replace with ?

        // Do not add '/' at the start or ending of the url

        $uri = array(

            // Tagging

            array("form" => "tag", "operation" => "save", "url" => "admin_ret_tagging/tagging/save"),

            array("form" => "bulk tag edit", "operation" => "update", "url" => "admin_ret_tagging/update_tagging_data"),

            // Purchase Order

            array("form" => "purchase order", "operation" => "save", "url" => "admin_ret_purchase/purchase/save"),

            array("form" => "purchase order", "operation" => "cancel", "url" => "admin_ret_purchase/update_order_cancel"),

            array("form" => "purchase order", "operation" => "close", "url" => "admin_ret_purchase/update_order_close"),

            // GRN Entry

            array("form" => "grn entry", "operation" => "save", "url" => "admin_ret_purchase/grnentry/save"),

            array("form" => "grn entry", "operation" => "update", "url" => "admin_ret_purchase/grnentry/update"),

            array("form" => "grn entry", "operation" => "cancel", "url" => "admin_ret_purchase/grnentry/cancel_grn_entry"),
            
            // Supplier Bill Entry

            array("form" => "supplier bill entry", "operation" => "save", "url" => "admin_ret_purchase/purchase/po_entry_save"),

            array("form" => "supplier bill entry", "operation" => "approve", "url" => "admin_ret_purchase/update_po_approval"),

            array("form" => "supplier bill entry", "operation" => "update", "url" => "admin_ret_purchase/purchase/po_entry_update"),

            array("form" => "supplier bill entry", "operation" => "cancel", "url" => "admin_ret_purchase/purchase/cancel_po_entry"),

            // Hallmarking Issue/Receipt

            array("form" => "HM issue / receipt", "operation" => "save", "url" => "admin_ret_purchase/update_halmarking_issue"),

            array("form" => "HM issue / receipt", "operation" => "save", "url" => "admin_ret_purchase/update_halmarking_receipt"),

            // QC Issue/Receipt

            array("form" => "QC issue / receipt", "operation" => "save", "url" => "admin_ret_purchase/qc_issue_update"),

            array("form" => "QC issue / receipt", "operation" => "save", "url" => "admin_ret_purchase/update_qc_issue"),

            array("form" => "QC issue / receipt", "operation" => "status update", "url" => "admin_ret_purchase/update_qc_status"),

            // Rate Fixing

            array("form" => "rate fixing", "operation" => "save", "url" => "admin_ret_purchase/rate_fixing/save"),

            array("form" => "rate fixing", "operation" => "approve", "url" => "admin_ret_purchase/update_ratefix_approval"),

            array("form" => "rate fixing", "operation" => "cancel", "url" => "admin_ret_purchase/rate_fixing/cancel"),

            // Supplier Payment

            array("form" => "supplier payment", "operation" => "save", "url" => "admin_ret_purchase/supplier_po_payment/save"),

            array("form" => "supplier payment", "operation" => "update", "url" => "admin_ret_purchase/supplier_po_payment/update"),

            array("form" => "supplier payment", "operation" => "cancel", "url" => "admin_ret_purchase/supplier_po_payment/cancel_pay_entry"),

            // Supplier return/sales

            array("form" => "supplier return/sales", "operation" => "save", "url" => "admin_ret_purchase/returnpoitems"),

            // Karigar

            array("form" => "karigar", "operation" => "save", "url" => "admin_ret_catalog/karigar_general/save"),

            array("form" => "karigar", "operation" => "update", "url" => "admin_ret_catalog/karigar_general/update/*"),

            array("form" => "karigar", "operation" => "status update", "url" => "admin_ret_catalog/karigar/update_status/*/*"),

            array("form" => "karigar", "operation" => "delete", "url" => "admin_ret_catalog/karigar/delete/*"),

            // Karigar Approval
            
            array("form" => "karigar approval", "operation" => "update", "url" => "admin_ret_catalog/karigar_approval/save"),

            array("form" => "karigar approval", "operation" => "OTP", "url" => "admin_ret_catalog/vendor_sendotp"),

            // Supplier Approval Bill Entry

            array("form" => "supplier approval bill entry", "operation" => "save", "url" => "admin_ret_purchase_approval/approvalstock/approval_save"),

            array("form" => "supplier approval bill entry", "operation" => "update", "url" => "admin_ret_purchase_approval/approvalstock/approval_update"),

            // Purchase Order Description

            array("form" => "purchase order description", "operation" => "save", "url" => "admin_ret_purchase/order_description/save"),

            array("form" => "purchase order description", "operation" => "update", "url" => "admin_ret_purchase/order_description/update"),

            array("form" => "purchase order description", "operation" => "delete", "url" => "admin_ret_purchase/order_description/delete/*"),

            // Smith Metal Issue

            array("form" => "smith metal issue", "operation" => "save", "url" => "admin_ret_purchase/karigarmetalissue/save"),

            array("form" => "smith metal issue", "operation" => "cancel", "url" => "admin_ret_purchase/karigarmetalissue/metalissue_cancel"),

            // Credit / Debit Entry

            array("form" => "credit / debit entry", "operation" => "save", "url" => "admin_ret_purchase/credit_debit_entry/save"),

            array("form" => "credit / debit entry", "operation" => "update", "url" => "admin_ret_purchase/credit_debit_entry/update"),

            // Approval Stock

            array("form" => "approval stock", "operation" => "save", "url" => "admin_ret_purchase/order_place"),

            array("form" => "approval stock", "operation" => "save", "url" => "admin_ret_purchase/convert_to_normal_stock"),

            // Approval to Invoice Conversion

            array("form" => "approval to invoice conversion", "operation" => "save", "url" => "admin_ret_purchase/supplier_rate_cut/save"),

            array("form" => "approval to invoice conversion", "operation" => "cancel", "url" => "admin_ret_purchase/supplier_rate_cut/cancel"),

            // Smith / Company Opening Balance

            array("form" => "smith / company opening balance", "operation" => "save", "url" => "admin_ret_purchase/smith_cmpy_op_bal/save"),

            // MC VA Settings

            array("form" => "mc va settings", "operation" => "save", "url" => "admin_ret_catalog/wastage_mc_settings/save"),

            array("form" => "mc va settings", "operation" => "update", "url" => "admin_ret_catalog/wastage_mc_settings/update"),

            array("form" => "mc va settings", "operation" => "delete", "url" => "admin_ret_catalog/wastage_mc_settings/delete/*"),

            // Sub Design Mapping

            array("form" => "sub design mapping", "operation" => "update", "url" => "admin_ret_catalog/update_subdesign_mapping"),

            array("form" => "sub design mapping", "operation" => "delete", "url" => "admin_ret_catalog/delete_sub_design_mapping"),

            // Design Mapping

            array("form" => "design mapping", "operation" => "update", "url" => "admin_ret_catalog/update_product_design_mapping"),

            array("form" => "design mapping", "operation" => "delete", "url" => "admin_ret_catalog/delete_product_design_mapping"),

            // Sub Design

            array("form" => "sub design", "operation" => "save", "url" => "admin_ret_catalog/ret_sub_design/save"),

            array("form" => "sub design", "operation" => "update", "url" => "admin_ret_catalog/ret_sub_design/update/*"),

            array("form" => "sub design", "operation" => "delete", "url" => "admin_ret_catalog/ret_sub_design/delete/*"),

            array("form" => "sub design", "operation" => "status update", "url" => "admin_ret_catalog/ret_sub_design/update_status/*/*"),

            // Design

            array("form" => "design", "operation" => "save", "url" => "admin_ret_catalog/ret_design/save"),

            array("form" => "design", "operation" => "update", "url" => "admin_ret_catalog/ret_design/update/*"),

            array("form" => "design", "operation" => "delete", "url" => "admin_ret_catalog/ret_design/delete"),

            array("form" => "design", "operation" => "status update", "url" => "admin_ret_catalog/ret_design/update_status/*/*"),

            // Product

            array("form" => "product", "operation" => "save", "url" => "admin_ret_catalog/ret_product/save"),

            array("form" => "product", "operation" => "update", "url" => "admin_ret_catalog/ret_product/update/*"),

            array("form" => "product", "operation" => "delete", "url" => "admin_ret_catalog/ret_product/delete/*"),

            array("form" => "product", "operation" => "status update", "url" => "admin_ret_catalog/ret_product/update_status/*/*"),

            // Product

            array("form" => "product", "operation" => "save", "url" => "admin_ret_catalog/ret_product/save"),

            array("form" => "product", "operation" => "update", "url" => "admin_ret_catalog/ret_product/update/*"),

            array("form" => "product", "operation" => "delete", "url" => "admin_ret_catalog/ret_product/delete/*"),

            array("form" => "product", "operation" => "status update", "url" => "admin_ret_catalog/ret_product/update_status/*/*"),

            // Category

            array("form" => "category", "operation" => "save", "url" => "admin_ret_catalog/category/add"),

            array("form" => "category", "operation" => "update", "url" => "admin_ret_catalog/category/update/*"),

            array("form" => "category", "operation" => "delete", "url" => "admin_ret_catalog/category/delete/*"),

            array("form" => "category", "operation" => "status update", "url" => "admin_ret_catalog/category/update_status/*/*"),

            // Metal

            array("form" => "metal", "operation" => "save", "url" => "admin_ret_catalog/metal/add"),

            array("form" => "metal", "operation" => "update", "url" => "admin_ret_catalog/metal/update/*"),

            array("form" => "metal", "operation" => "delete", "url" => "admin_ret_catalog/metal/delete/*"),

            array("form" => "metal", "operation" => "status update", "url" => "admin_ret_catalog/metal/update_status/*/*"),

            // Stone Rate Settings

            array("form" => "stone rate setting", "operation" => "save", "url" => "admin_ret_catalog/ret_stone_rate_settings/save"),

            array("form" => "stone rate setting", "operation" => "update", "url" => "admin_ret_catalog/ret_stone_rate_settings/update/*"),

            array("form" => "stone rate setting", "operation" => "delete", "url" => "admin_ret_catalog/ret_stone_rate_settings/delete/*"),

            // Collection Master

            array("form" => "collection", "operation" => "save", "url" => "admin_ret_catalog/ret_collection/save"),

            array("form" => "collection", "operation" => "update", "url" => "admin_ret_catalog/ret_collection/update/*"),

            array("form" => "collection", "operation" => "delete", "url" => "admin_ret_catalog/ret_collection/Delete/*"),

            array("form" => "collection", "operation" => "status update", "url" => "admin_ret_catalog/ret_collection/update_status/*/*"),

            // Dynamic Day close

            array("form" => "dynamic day close", "operation" => "save", "url" => "admin_ret_catalog/day_close/update_dayclose"),

            // Clarity

            array("form" => "clarity", "operation" => "save", "url" => "clarity/add"),

            array("form" => "clarity", "operation" => "update", "url" => "clarity/update/*"),

            array("form" => "clarity", "operation" => "delete", "url" => "clarity/delete/*"),

            array("form" => "clarity", "operation" => "status update", "url" => "admin_ret_catalog/clarity_status/*/*"),

            // Color

            array("form" => "color", "operation" => "save", "url" => "color/add"),

            array("form" => "color", "operation" => "update", "url" => "color/update/*"),

            array("form" => "color", "operation" => "delete", "url" => "color/delete/*"),

            array("form" => "color", "operation" => "status update", "url" => "admin_ret_catalog/color_status/*/*"),

            // Counter

            array("form" => "counter", "operation" => "save", "url" => "admin_ret_catalog/floor_counter/add"),

            array("form" => "counter", "operation" => "update", "url" => "admin_ret_catalog/floor_counter/update/*"),

            array("form" => "counter", "operation" => "delete", "url" => "admin_ret_catalog/floor_counter/delete/*"),

            array("form" => "counter", "operation" => "status update", "url" => "admin_ret_catalog/floor_counter/update_status/*/*"),

            // Shape

            array("form" => "shape", "operation" => "save", "url" => "admin_ret_catalog/shape/Add"),

            array("form" => "shape", "operation" => "update", "url" => "admin_ret_catalog/shape/Update/*"),

            array("form" => "shape", "operation" => "delete", "url" => "shape/delete/*"),

            array("form" => "shape", "operation" => "status update", "url" => "admin_ret_catalog/shape_status/*/*"),

            // Cut

            array("form" => "cut", "operation" => "save", "url" => "cut/add"),

            array("form" => "cut", "operation" => "update", "url" => "cut/update/*"),

            array("form" => "cut", "operation" => "delete", "url" => "cut/delete/*"),

            array("form" => "cut", "operation" => "status update", "url" => "admin_ret_catalog/cut_status/*/*"),

            // Financial Year

            array("form" => "financial year", "operation" => "save", "url" => "admin_ret_catalog/financial_year/save"),

            array("form" => "financial year", "operation" => "update", "url" => "admin_ret_catalog/financial_year/update/*"),

            array("form" => "financial year", "operation" => "delete", "url" => "admin_ret_catalog/financial_year/delete/*"),

            array("form" => "financial year", "operation" => "status update", "url" => "admin_ret_catalog/financial_status/*/*"),

            // Branch Floor

            array("form" => "branch floor", "operation" => "save", "url" => "admin_ret_catalog/branch_floor/add"),

            array("form" => "branch floor", "operation" => "update", "url" => "admin_ret_catalog/branch_floor/update/*"),

            array("form" => "branch floor", "operation" => "delete", "url" => "admin_ret_catalog/branch_floor/delete/*"),

            array("form" => "branch floor", "operation" => "status update", "url" => "admin_ret_catalog/branch_floor/update_status/*/*"),

            // Hook

            array("form" => "hook", "operation" => "save", "url" => "admin_ret_catalog/hook/add"),

            array("form" => "hook", "operation" => "update", "url" => "admin_ret_catalog/hook/update/*"),

            array("form" => "hook", "operation" => "delete", "url" => "admin_ret_catalog/hook/delete/*"),

            array("form" => "hook", "operation" => "status update", "url" => "admin_ret_catalog/hook/update_status/*/*"),

            // Making Type

            array("form" => "making type", "operation" => "save", "url" => "admin_ret_catalog/making_type/add"),

            array("form" => "making type", "operation" => "update", "url" => "admin_ret_catalog/making_type/update/*"),

            array("form" => "making type", "operation" => "delete", "url" => "admin_ret_catalog/making_type/delete/*"),

            array("form" => "making type", "operation" => "status update", "url" => "admin_ret_catalog/making_type/update_status/*/*"),

            // Material

            array("form" => "material", "operation" => "save", "url" => "admin_ret_catalog/material/add"),

            array("form" => "material", "operation" => "update", "url" => "admin_ret_catalog/material/update/*"),

            array("form" => "material", "operation" => "delete", "url" => "admin_ret_catalog/material/delete/*"),

            array("form" => "material", "operation" => "status update", "url" => "admin_ret_catalog/material/update_status/*/*"),

            // Material Rate

            array("form" => "material rate", "operation" => "update", "url" => "admin_ret_catalog/update_mrrate_data"),

            // Purity

            array("form" => "purity", "operation" => "save", "url" => "purity/add"),

            array("form" => "purity", "operation" => "update", "url" => "purity/update/*"),

            array("form" => "purity", "operation" => "delete", "url" => "purity/delete/*"),

            array("form" => "purity", "operation" => "status update", "url" => "admin_ret_catalog/purity_status/*/*"),

            // Screw

            array("form" => "screw", "operation" => "save", "url" => "admin_ret_catalog/screw/add"),

            array("form" => "screw", "operation" => "update", "url" => "admin_ret_catalog/screw/update/*"),

            array("form" => "screw", "operation" => "delete", "url" => "admin_ret_catalog/screw/delete/*"),

            array("form" => "screw", "operation" => "status update", "url" => "admin_ret_catalog/screw/update_status/*/*"),

            // Stone

            array("form" => "stone", "operation" => "save", "url" => "admin_ret_catalog/stone/add"),

            array("form" => "stone", "operation" => "update", "url" => "admin_ret_catalog/stone/update/*"),

            array("form" => "stone", "operation" => "delete", "url" => "admin_ret_catalog/stone/delete/*"),

            array("form" => "stone", "operation" => "status update", "url" => "admin_ret_catalog/stone/update_status/*/*"),

            // Tag Type

            array("form" => "tag type", "operation" => "save", "url" => "admin_ret_catalog/tag/add"),

            array("form" => "tag type", "operation" => "update", "url" => "admin_ret_catalog/tag/update/*"),

            array("form" => "tag type", "operation" => "delete", "url" => "admin_ret_catalog/tag/delete/*"),

            array("form" => "tag type", "operation" => "status update", "url" => "admin_ret_catalog/tag/update_status/*/*"),

            // Tax Group

            array("form" => "tax Group", "operation" => "save", "url" => "admin_ret_catalog/tgrp/add"),

            array("form" => "tax Group", "operation" => "update", "url" => "admin_ret_catalog/tgrp/update/*"),

            array("form" => "tax Group", "operation" => "delete", "url" => "admin_ret_catalog/tgrp/delete/*"),

            array("form" => "tax Group", "operation" => "status update", "url" => "admin_ret_catalog/tgrp/update_status/*/*"),

            // Tax

            array("form" => "tax", "operation" => "save", "url" => "admin_ret_catalog/tax/add"),

            array("form" => "tax", "operation" => "update", "url" => "admin_ret_catalog/tax/update/*"),

            array("form" => "tax", "operation" => "delete", "url" => "admin_ret_catalog/tax/delete/*"),

            array("form" => "tax", "operation" => "status update", "url" => "admin_ret_catalog/tax/update_status/*/*"),

            // Metal Type

            array("form" => "metal type", "operation" => "save", "url" => "admin_ret_catalog/metal_type/add"),

            array("form" => "metal type", "operation" => "update", "url" => "admin_ret_catalog/metal_type/update"),

            array("form" => "metal type", "operation" => "delete", "url" => "admin_ret_catalog/metal_type/delete/*"),

            // Theme

            array("form" => "theme", "operation" => "save", "url" => "admin_ret_catalog/theme/add"),

            array("form" => "theme", "operation" => "update", "url" => "admin_ret_catalog/theme/update/*"),

            array("form" => "theme", "operation" => "delete", "url" => "admin_ret_catalog/theme/delete/*"),

            array("form" => "theme", "operation" => "status update", "url" => "admin_ret_catalog/theme/update_status/*/*"),

            // UOM

            array("form" => "uom", "operation" => "save", "url" => "admin_ret_catalog/uom/add"),

            array("form" => "uom", "operation" => "update", "url" => "admin_ret_catalog/uom/update/*"),

            array("form" => "uom", "operation" => "delete", "url" => "admin_ret_catalog/uom/delete/*"),

            array("form" => "uom", "operation" => "status update", "url" => "admin_ret_catalog/uom/update_status/*/*"),

            // Metal Process

            array("form" => "metal process", "operation" => "save", "url" => "admin_ret_metal_process/metal_process/save"),

            array("form" => "metal process", "operation" => "update", "url" => "admin_ret_metal_process/metal_process/update/*"),

            // Weight Range

            array("form" => "weight range", "operation" => "save", "url" => "admin_ret_catalog/weight/Add"),

            array("form" => "weight range", "operation" => "update", "url" => "admin_ret_catalog/weight/Update/*"),

            array("form" => "weight range", "operation" => "delete", "url" => "admin_ret_catalog/weight/Delete/*"),

            // Reorder Settings

            array("form" => "reorder settings", "operation" => "save", "url" => "admin_ret_catalog/reorder_settings/add"),

            array("form" => "reorder settings", "operation" => "update", "url" => "admin_ret_catalog/reorder_settings/update/*"),

            array("form" => "reorder settings", "operation" => "delete", "url" => "admin_ret_catalog/reorder_settings/delete/*"),

            // Delivery Place

            array("form" => "delivery place", "operation" => "save", "url" => "admin_ret_catalog/ret_delivery/Add"),

            array("form" => "delivery place", "operation" => "update", "url" => "admin_ret_catalog/ret_delivery/Update/*"),

            array("form" => "delivery place", "operation" => "delete", "url" => "admin_ret_catalog/ret_delivery/delete/*"),

            array("form" => "delivery place", "operation" => "status update", "url" => "admin_ret_catalog/update_location/*/*"),

            // Size

            array("form" => "size", "operation" => "save", "url" => "admin_ret_catalog/ret_size/Add"),

            array("form" => "size", "operation" => "update", "url" => "admin_ret_catalog/ret_size/Update/*"),

            array("form" => "size", "operation" => "delete", "url" => "admin_ret_catalog/ret_size/delete/*"),

            array("form" => "size", "operation" => "status update", "url" => "admin_ret_catalog/ret_size/*/*"),

            // Section

            array("form" => "section", "operation" => "save", "url" => "admin_ret_catalog/ret_section/Add"),

            array("form" => "section", "operation" => "update", "url" => "admin_ret_catalog/ret_section/Update/*"),

            array("form" => "section", "operation" => "delete", "url" => "admin_ret_catalog/ret_section/delete/*"),

            array("form" => "section", "operation" => "status update", "url" => "admin_ret_catalog/ret_section/*/*"),

            // Old Metal Category

            array("form" => "old metal category", "operation" => "save", "url" => "admin_ret_catalog/old_metal_cat/add"),

            array("form" => "old metal category", "operation" => "update", "url" => "admin_ret_catalog/old_metal_cat/update"),

            array("form" => "old metal category", "operation" => "delete", "url" => "admin_ret_catalog/old_metal_cat/delete/*"),

            // Charges

            array("form" => "charges", "operation" => "save", "url" => "admin_ret_catalog/charges/Add"),

            array("form" => "charges", "operation" => "update", "url" => "admin_ret_catalog/charges/update"),

            array("form" => "charges", "operation" => "delete", "url" => "admin_ret_catalog/charges/delete"),

            // Attributes

            array("form" => "attributes", "operation" => "save", "url" => "admin_ret_catalog/attribute/save"),

            array("form" => "attributes", "operation" => "update", "url" => "admin_ret_catalog/attribute/update/*"),

            array("form" => "attributes", "operation" => "delete", "url" => "admin_ret_catalog/attribute/delete/*"),

            array("form" => "attributes", "operation" => "status update", "url" => "admin_ret_catalog/attribute/update_status/*/*"),

            // Damage Master

            array("form" => "damage master", "operation" => "save", "url" => "admin_ret_catalog/repair_master/save"),

            array("form" => "damage master", "operation" => "update", "url" => "admin_ret_catalog/repair_master/update/*"),

            array("form" => "damage master", "operation" => "delete", "url" => "admin_ret_catalog/repair_master/Delete/*"),

            array("form" => "damage master", "operation" => "status update", "url" => "repair_master/update_status/*/*"),

            // Product Division

            array("form" => "product division", "operation" => "save", "url" => "product_division/add"),

            array("form" => "product division", "operation" => "update", "url" => "product_division/update/*"),

            array("form" => "product division", "operation" => "delete", "url" => "product_division/delete/*"),

            array("form" => "product division", "operation" => "status update", "url" => "admin_ret_catalog/product_division_status/*/*"),

            // Metal Rate Purity

            array("form" => "metal rate purity", "operation" => "save", "url" => "admin_ret_catalog/ret_metalpurity/add"),

            array("form" => "metal rate purity", "operation" => "update", "url" => "admin_ret_catalog/ret_metalpurity/update/*"),

            array("form" => "metal rate purity", "operation" => "delete", "url" => "admin_ret_catalog/ret_metalpurity/delete/*"),
            
            // Cash Deposit

            array("form" => "cash deposit", "operation" => "save", "url" => "deposit/save"),

            array("form" => "cash deposit", "operation" => "update", "url" => "deposit/update/*"),

            array("form" => "cash deposit", "operation" => "delete", "url" => "deposit/delete/*"),

            // Stock Issue Type

            array("form" => "stock issue type", "operation" => "save", "url" => "admin_ret_catalog/stock_issue_type/Add"),

            array("form" => "stock issue type", "operation" => "update", "url" => "admin_ret_catalog/stock_issue_type/Update/*"),

            array("form" => "stock issue type", "operation" => "delete", "url" => "admin_ret_catalog/stock_issue_type/delete/*"),

            array("form" => "stock issue type", "operation" => "status update", "url" => "admin_ret_catalog/stock_issue_type/update_status/*/*"),

            // Supplier Catalog

            array("form" => "supplier catalog", "operation" => "save", "url" => "admin_ret_supp_catalog/supplier_catalog/save"),

            array("form" => "supplier catalog", "operation" => "update", "url" => "admin_ret_supp_catalog/supplier_catalog/update"),

            array("form" => "supplier catalog", "operation" => "delete", "url" => "admin_ret_supp_catalog/supplier_catalog/delete/*"),

            array("form" => "supplier catalog", "operation" => "status update", "url" => "admin_ret_supp_catalog/profile_status/*/*"),

            // Diamond

            array("form" => "diamond", "operation" => "save", "url" => "admin_ret_catalog/diamond/save"),

            array("form" => "diamond", "operation" => "update", "url" => "admin_ret_catalog/diamond/update/*"),

            array("form" => "diamond", "operation" => "delete", "url" => "admin_ret_catalog/diamond/delete/*"),

            array("form" => "diamond", "operation" => "status update", "url" => "admin_ret_catalog/diamond/update_status/*/*"),

            // Diamond Rate

            array("form" => "diamond rate", "operation" => "save", "url" => "admin_ret_catalog/diamond_rate/save"),

            array("form" => "diamond rate", "operation" => "update", "url" => "admin_ret_catalog/diamond_rate/update/*"),

            array("form" => "diamond rate", "operation" => "delete", "url" => "admin_ret_catalog/diamond_rate/delete/*"),

            array("form" => "diamond rate", "operation" => "status update", "url" => "admin_ret_catalog/diamond_rate/update_status/*/*/*"),

            // Selling Diamond Rate

            array("form" => "selling diamond rate", "operation" => "save", "url" => "admin_ret_catalog/selling_diamond_rate/save"),

            array("form" => "selling diamond rate", "operation" => "update", "url" => "admin_ret_catalog/selling_diamond_rate/update/*"),

            array("form" => "selling diamond rate", "operation" => "delete", "url" => "admin_ret_catalog/selling_diamond_rate/delete/*"),

            array("form" => "selling diamond rate", "operation" => "status update", "url" => "admin_ret_catalog/selling_diamond_rate/update_status/*/*/*"),

            // Pay Device

            array("form" => "pay device", "operation" => "save", "url" => "admin_ret_catalog/ret_pay_device/Add"),

            array("form" => "pay device", "operation" => "update", "url" => "admin_ret_catalog/ret_pay_device/Update/*"),

            array("form" => "pay device", "operation" => "delete", "url" => "admin_ret_catalog/ret_pay_device/delete/*"),

            array("form" => "pay device", "operation" => "status update", "url" => "admin_ret_catalog/ret_pay_device/update_status/*/*"),

            // Account Head

            array("form" => "account head", "operation" => "save", "url" => "admin_ret_catalog/ret_account/Add"),

            array("form" => "account head", "operation" => "update", "url" => "admin_ret_catalog/ret_account/Update/*"),

            array("form" => "account head", "operation" => "delete", "url" => "admin_ret_catalog/ret_account/delete/*"),

            array("form" => "account head", "operation" => "status update", "url" => "admin_ret_catalog/ret_account/update_status/*/*"),
            
            //chit account
            
            array("form" => "Chit Scheme Account", "operation" => "save", "url" => "account/add"),

            array("form" => "Chit Scheme Account", "operation" => "update", "url" => "account/edit/*"),
            
            //scheme master
            
            array("form" => "Scheme Master", "operation" => "update", "url" => "scheme/edit/*"),


        );

        return $uri;

    }

}
