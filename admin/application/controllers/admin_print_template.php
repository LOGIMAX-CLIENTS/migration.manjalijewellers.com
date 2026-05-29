<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class admin_print_template extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        // Load database libraries if not autoloaded
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('print_template_model');
        
        // Auth check - required for layout/header which queries access table using session profile
        if(!$this->session->userdata('is_logged')){ redirect('admin/login'); }
    }

    // List all templates
    public function index() {
        $data['title'] = 'Print Templates';
        $data['templates'] = $this->print_template_model->get_all();
        $data['categories'] = $this->print_template_model->get_template_categories();
        
        // Assuming a standard layout structure
        $this->load->view('layout/header', $data);
        // We'll create a list view that doesn't depend on too many external partials for safety
        $this->load->view('print_templates/list', $data);
        $this->load->view('layout/footer');
    }

    // Ajax list for DataTables (if needed)
    public function ajax_list() {
        $templates = $this->print_template_model->get_all_ajax();
        echo json_encode(['data' => $templates]);
    }

    // Add new template
    public function add() {
        $data['categories'] = $this->print_template_model->get_template_categories();
        $data['paper_sizes'] = ['A4','A5','Letter','Thermal-58mm','Thermal-80mm','Custom'];
        
        $this->load->view('layout/header');
        $this->load->view('print_templates/form', $data);
        $this->load->view('layout/footer');
    }

    // Save new template from form
    public function save() {
        $data = [
            'template_code' => $this->input->post('template_code'),
            'template_name' => $this->input->post('template_name'),
            'template_category' => $this->input->post('template_category'),
            'paper_size' => $this->input->post('paper_size'),
            'page_orientation' => $this->input->post('page_orientation'),
            'id_branch' => $this->input->post('id_branch') ?: null,
            // 'created_by' => $this->session->userdata('id_employee')
        ];
        
        // Initial GrapesJS data (empty)
        $data['gjs_data'] = json_encode([]);
        $data['template_html'] = '<div style="padding: 20px;">New Template</div>';
        
        $id = $this->print_template_model->insert($data);
        redirect('print-templates/designer-v2/' . $id);
    }

    // Designer Interface
    public function designer($id) {
        $data['template'] = $this->print_template_model->get_by_id($id);
        if (!$data['template']) {
            redirect('print-templates');
        }
        $this->load->view('print_templates/designer', $data);
    }

    // AJAX: Load design for Block Editor
    public function load_design($id) {
        $template = $this->print_template_model->get_by_id($id);
        if (!$template) {
            echo json_encode(null);
            return;
        }

        // Return parsed gjs_data if it exists
        if (!empty($template['gjs_data'])) {
            $data = json_decode($template['gjs_data'], true);
            if ($data && isset($data['blocks'])) {
                // New block editor format
                echo json_encode($data);
                return;
            }
            if ($data) {
                // Legacy GrapesJS format
                echo json_encode($data);
                return;
            }
        }

        // No gjs_data — if there IS template_html, wrap it as a custom-html block
        if (!empty($template['template_html'])) {
            $html = $template['template_html'];
            $css  = $template['template_css'] ?? '';
            $full_html = ($css ? '<style>' . $css . '</style>' : '') . $html;
            echo json_encode([
                'version' => 1,
                'blocks'  => [[
                    'id'         => 'auto-html-block-1',
                    'type'       => 'custom-html',
                    'enabled'    => true,
                    'order'      => 0,
                    'content'    => $html,
                    'css'        => $css,
                    'custom_html'=> $full_html,
                    'settings'   => []
                ]]
            ]);
            return;
        }

        // Completely empty template
        echo json_encode(null);
    }

    // AJAX: Save design from Block Editor or GrapesJS
    // Each save creates a new versioned record and deactivates the previous one.
    public function save_design($id) {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'No data']);
            return;
        }

        // Fetch the existing template to copy its meta fields
        $current = $this->print_template_model->get_by_id($id);
        if (!$current) {
            echo json_encode(['success' => false, 'message' => 'Template not found']);
            return;
        }

        // Determine the grouping code: use existing `code` column, fallback to template_code
        $group_code = !empty($current['code']) ? $current['code'] : $current['template_code'];

        // Generate a UNIQUE template_code for this new version (avoids UNIQUE constraint violation)
        $new_template_code = $group_code . '_' . time();

        $new_record = [
            'template_code'     => $new_template_code,
            'code'              => $group_code,  // Same grouping code across all versions
            'template_name'     => $current['template_name'],
            'template_category' => $current['template_category'],
            'paper_size'        => isset($data['paper_size']) ? $data['paper_size'] : $current['paper_size'],
            'page_orientation'  => isset($data['orientation']) ? $data['orientation'] : (isset($current['page_orientation']) ? $current['page_orientation'] : null),
            'id_branch'         => isset($current['id_branch']) ? $current['id_branch'] : null,
            'is_default'        => 0,
            'is_active'         => 1,
            'version_number'    => (isset($current['version_number']) ? (int)$current['version_number'] : 1) + 1,
            'parent_template_id' => (int)$id,
            'gjs_data'          => json_encode($data),
        ];

        // Persist print margins from Konva designer
        if (isset($data['margins'])) {
            $new_record['margin_top']    = (float)($data['margins']['top'] ?? 0);
            $new_record['margin_right']  = (float)($data['margins']['right'] ?? 0);
            $new_record['margin_bottom'] = (float)($data['margins']['bottom'] ?? 0);
            $new_record['margin_left']   = (float)($data['margins']['left'] ?? 0);
        }

        // Check if this is the new Block Editor format (version:1 with blocks array)
        if (isset($data['version']) && isset($data['blocks'])) {
            // Compile blocks into template_html for the render engine
            $compiled_html = $this->_compile_blocks($data['blocks']);
            $compiled_css  = '';

            // Extract CSS from custom-html blocks
            foreach ($data['blocks'] as $block) {
                if (!empty($block['css'])) {
                    $compiled_css .= $block['css'] . "\n";
                }
            }

            $new_record['template_html'] = $compiled_html;
            $new_record['template_css']  = $compiled_css;
        } else {
            // Legacy GrapesJS format
            $new_record['template_html'] = isset($data['gjs-html']) ? $data['gjs-html'] : '';
            $new_record['template_css']  = isset($data['gjs-css']) ? $data['gjs-css'] : '';
        }

        // Deactivate all records with the same grouping code (using `code` column)
        $this->print_template_model->deactivate_by_group_code($group_code);

        // Insert the new version as the active one
        $new_id = $this->print_template_model->insert($new_record);

        // Migrate computed variables from old template to new template
        if ($new_id && $new_id != $id) {
            $old_computed_vars = $this->print_template_model->get_computed_vars((int)$id);
            foreach ($old_computed_vars as $cv) {
                $cv_record = [
                    'id_template' => $new_id,
                    'var_name'    => $cv['var_name'],
                    'var_label'   => $cv['var_label'],
                    'formula'     => $cv['formula'],
                ];
                $this->print_template_model->save_computed_var($cv_record);
            }
        }

        echo json_encode(['success' => true, 'new_id' => $new_id, 'code' => $group_code]);
    }

    // AJAX: Activate a specific template version and deactivate all others with the same code
    public function toggle_active($id) {
        $result = $this->print_template_model->activate_and_deactivate_others($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$result]);
    }

    /**
     * Compile an array of blocks into a single HTML string for rendering.
     * Each enabled block contributes its HTML (custom_html or block template).
     */
    private function _compile_blocks($blocks) {
        // Sort blocks by order
        usort($blocks, function($a, $b) {
            return ($a['order'] ?? 0) - ($b['order'] ?? 0);
        });

        $html = '';
        $block_templates = $this->_get_block_template_map();
        
        // Block types that should always use settings-based compilation
        // so that settings toggles (show_borders, etc.) take effect
        $settings_driven_types = [
            'customer-info', 'company-header', 'items-table', 'tax-summary',
            'payment-info', 'signature-block', 'amount-words', 'remark-block',
            'custom-text', 'spacer', 'page-break', 'advance-payment',
            'purchase-table', 'sales-return-table', 'credit-collection-table',
            'chit-preclose-table', 'divider', 'conditional-section',
            'line-solid', 'line-dashed', 'line-dotted', 'line-dashdot', 'divider-line'
        ];

        foreach ($blocks as $block) {
            // Skip disabled blocks
            if (isset($block['enabled']) && !$block['enabled']) {
                continue;
            }

            $type = $block['type'] ?? '';
            $settings = $block['settings'] ?? [];
            $block_html = '';

            // For known block types: use settings-based generation (respects toggles)
            // For custom-html/custom-saved-block: use custom_html/content directly
            if (in_array($type, $settings_driven_types)) {
                // If this block has custom_html from Customize, strip borders if show_borders is false
                if (!empty($block['custom_html'])) {
                    $block_html = $block['custom_html'];
                    // Embed custom_css as a <style> block if present (for styles not inlined by JS)
                    if (!empty($block['custom_css'])) {
                        $block_html = '<style>' . $block['custom_css'] . '</style>' . "\n" . $block_html;
                    }
                    // Apply settings toggles to customized HTML
                    if (isset($settings['show_borders']) && !$settings['show_borders']) {
                        // Remove border styles from the custom HTML
                        $block_html = preg_replace('/border-right:\s*\d+px\s+solid\s+[^;]+;?/', '', $block_html);
                        $block_html = preg_replace('/border-left:\s*\d+px\s+solid\s+[^;]+;?/', '', $block_html);
                    }
                    if (isset($settings['border_bottom']) && !$settings['border_bottom']) {
                        $block_html = preg_replace('/border-bottom:\s*\d+px\s+solid\s+[^;]+;?/', '', $block_html);
                    }
                } else {
                    // No custom HTML — generate from settings
                    $block_html = $this->_compile_block_from_settings($type, $settings);
                }
            } else {
                // Custom blocks: use custom_html or content directly
                if (!empty($block['custom_html'])) {
                    $block_html = $block['custom_html'];
                    // Embed custom_css if present
                    if (!empty($block['custom_css'])) {
                        $block_html = '<style>' . $block['custom_css'] . '</style>' . "\n" . $block_html;
                    }
                } elseif (!empty($block['content'])) {
                    $block_html = $block['content'];
                } elseif (isset($block_templates[$type])) {
                    $block_html = $block_templates[$type];
                } else {
                    $block_html = $this->_compile_block_from_settings($type, $settings);
                }
            }

            // Auto-wrap with conditional tags if condition_key is set
            // (skip 'conditional-section' type since it handles its own wrapping)
            if ($type !== 'conditional-section' && !empty($settings['condition_key']) && $settings['condition_key'] !== 'none') {
                $ck = $settings['condition_key'];
                $block_html = '{{#' . $ck . '}}' . $block_html . '{{/' . $ck . '}}';
            }

            $html .= $block_html . "\n";
        }

        return $html;
    }

    /**
     * Map of block type to default HTML template
     */
    private function _get_block_template_map() {
        return [
            'company-header' => '
                <div class="print-header" style="text-align:center; font-size:12px; padding-bottom:10px; margin-bottom:10px;">
                    <div style="font-size:20px; font-weight:bold; margin-top:5px;">{{company_name}}</div>
                    <div style="font-size:12px;"><strong>{{company_address}} | {{company_city}} - {{company_pincode}} | Phone : {{company_mobile}}</strong></div>
                    <div style="font-size:12px;"><strong>GSTIN : {{company_gstin}} | PAN : {{company_pan}}</strong></div>
                </div>',
            'spacer' => '<div style="height:20px;"></div>',
            'page-break' => '<div style="page-break-after: always;"></div>',
        ];
    }

    /**
     * Compile a block from its settings (for blocks without custom_html or template).
     * This handles the settings-driven block types like customer-info, items-table, etc.
     */
    private function _compile_block_from_settings($type, $settings) {
        $fs = $settings['font_size'] ?? '12px';

        switch ($type) {
            case 'customer-info':
                return $this->_compile_customer_info($settings, $fs);
            case 'items-table':
                return $this->_compile_items_table($settings, $fs);
            case 'tax-summary':
                return $this->_compile_tax_summary($settings, $fs);
            case 'payment-info':
                return $this->_compile_payment_info($settings, $fs);
            case 'signature-block':
                return $this->_compile_signature($settings, $fs);
            case 'amount-words':
                return $this->_compile_amount_words($settings, $fs);
            case 'remark-block':
                $show_label = ($settings['show_label'] ?? true) ? '<strong>Remark: </strong>' : '';
                return '<div style="font-size:'.$fs.'; margin-top:10px;">'.$show_label.'{{remark}}</div>';
            case 'custom-text':
                $content = $settings['content'] ?? '';
                $align = $settings['text_align'] ?? 'left';
                $bold = !empty($settings['bold']) ? 'font-weight:bold;' : '';
                return '<div style="font-size:'.$fs.'; text-align:'.$align.'; '.$bold.'">'.$content.'</div>';
            case 'spacer':
                $h = $settings['height'] ?? '20px';
                return '<div style="height:'.$h.';"></div>';
            case 'page-break':
                return '<div style="page-break-after: always;"></div>';
            case 'divider':
                $content = $settings['content'] ?? '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -';
                $divFs = $settings['font_size'] ?? '12px';
                $align = $settings['text_align'] ?? 'center';
                $mt = $settings['margin_top'] ?? '5px';
                $mb = $settings['margin_bottom'] ?? '5px';
                return '<div style="margin-top:'.$mt.'; margin-bottom:'.$mb.'; text-align:'.$align.'; font-size:'.$divFs.'; overflow:hidden; white-space:nowrap; letter-spacing:2px;">'.$content.'</div>';
            case 'line-solid':
            case 'line-dashed':
            case 'line-dotted':
            case 'line-dashdot':
            case 'divider-line':
                $style = $settings['line-style'] ?? (($type === 'line-dashed') ? 'dashed' : (($type === 'line-dotted') ? 'dotted' : 'solid'));
                if ($type === 'line-dashdot') $style = 'dashed'; // Fallback for mPDF
                $color = $settings['line-color'] ?? $settings['color'] ?? '#000000';
                $thick = $settings['line-thickness'] ?? $settings['thickness'] ?? '1';
                $width = $settings['width'] ?? '100';
                $mt    = $settings['margin_top'] ?? '5px';
                $mb    = $settings['margin_bottom'] ?? '5px';
                $border_style = ($style === 'dash-dot') ? 'dashed' : $style;
                return '<div style="margin-top:'.$mt.'; margin-bottom:'.$mb.'; text-align:center;"><div style="display:inline-block; width:'.$width.'%; border-top:'.$thick.'px '.$border_style.' '.$color.';"></div></div>';
            case 'conditional-section':
                $key = $settings['condition_key'] ?? 'has_sales_items';
                $content = $settings['content'] ?? '';
                return '<!-- {{#'.$key.'}} -->' . $content . '<!-- {{/'.$key.'}} -->';
            default:
                return '<!-- Block type: ' . htmlspecialchars($type) . ' -->';
        }
    }

    /**
     * Compile customer-info block from field settings
     */
    private function _compile_customer_info($s, $fs) {
        $fields = $s['fields'] ?? [];
        $layout = $s['layout'] ?? 'two-column';
        $show_borders = $s['show_borders'] ?? true;
        $cust_fields = array_filter($fields, function($f) { return ($f['group'] ?? '') === 'customer'; });
        $inv_fields = array_filter($fields, function($f) { return ($f['group'] ?? '') === 'invoice'; });

        $html = '<table style="width:100%; border-collapse:collapse; font-size:'.$fs.'; font-weight:bold; margin-bottom:10px;"><tr>';
        
        // Customer column — border-right only if show_borders is true
        $border_style = $show_borders ? 'border-right:2px solid #000; padding-right:10px;' : 'padding-right:10px;';
        $html .= '<td style="width:50%; vertical-align:top; '.$border_style.'"><table style="width:100%; line-height:1.4;">';
        foreach ($cust_fields as $f) {
            $lbl = !empty($f['label']) ? $f['label'] . ' ' : '';
            $suf = $f['suffix'] ?? '';
            $html .= '<tr><td>' . $lbl . '{{' . $f['placeholder'] . '}}' . $suf . '</td></tr>';
        }
        $html .= '</table></td>';

        // Invoice column
        $html .= '<td style="width:50%; vertical-align:top; padding-left:10px;"><table style="width:100%; line-height:1.4;">';
        foreach ($inv_fields as $f) {
            $lbl = $f['label'] ?? '';
            $suf = $f['suffix'] ?? '';
            $html .= '<tr><td style="width:40%;">' . $lbl . '</td><td style="width:5%;">:</td><td style="width:55%;">{{' . $f['placeholder'] . '}}' . $suf . '</td></tr>';
        }
        $html .= '</table></td>';
        
        $html .= '</tr></table>';
        return $html;
    }

    /**
     * Compile items-table block from column settings
     */
    private function _compile_items_table($s, $fs) {
        $columns = $s['columns'] ?? [];
        
        $html = '<table style="width:100%; border-collapse:collapse; font-size:'.$fs.';">';
        $html .= '<thead><tr>';
        
        foreach ($columns as $col) {
            if (!empty($col['visible'])) {
                $align = $col['align'] ?? 'left';
                $html .= '<th style="padding:4px; text-align:'.$align.';">' . ($col['label'] ?? '') . '</th>';
            }
        }
        $html .= '</tr></thead>';
        
        $html .= '<tbody><!-- {{#sales}} --><tr>';
        foreach ($columns as $col) {
            if (!empty($col['visible'])) {
                $align = $col['align'] ?? 'left';
                $html .= '<td style="padding:4px; text-align:'.$align.';">{{' . ($col['field'] ?? $col['label'] ?? '') . '}}</td>';
            }
        }
        $html .= '</tr><!-- {{/sales}} --></tbody>';
        
        $html .= '<tfoot><tr style="font-weight:bold;">';
        $html .= '<td colspan="' . max(1, count(array_filter($columns, function($c) { return !empty($c['visible']); }))) . '" style="padding:4px; text-align:right;">Total: {{sub_total}}</td>';
        $html .= '</tr></tfoot></table>';
        
        return $html;
    }

    /**
     * Compile tax-summary block
     */
    private function _compile_tax_summary($s, $fs) {
        $html = '<table style="width:100%; font-size:'.$fs.'; border-collapse:collapse;">';
        
        if ($s['show_subtotal'] ?? true) {
            $html .= '<tr><td style="text-align:right; padding:2px;">Subtotal:</td><td style="text-align:right; padding:2px; width:120px;">₹{{sub_total}}</td></tr>';
        }
        if ($s['show_cgst'] ?? true) {
            $html .= '<tr><td style="text-align:right; padding:2px;">CGST:</td><td style="text-align:right; padding:2px;">₹{{cgst_amount}}</td></tr>';
        }
        if ($s['show_sgst'] ?? true) {
            $html .= '<tr><td style="text-align:right; padding:2px;">SGST:</td><td style="text-align:right; padding:2px;">₹{{sgst_amount}}</td></tr>';
        }
        if ($s['show_igst'] ?? true) {
            $html .= '<tr><td style="text-align:right; padding:2px;">IGST:</td><td style="text-align:right; padding:2px;">₹{{igst_amount}}</td></tr>';
        }
        if ($s['show_round_off'] ?? true) {
            $html .= '<tr><td style="text-align:right; padding:2px;">Round Off:</td><td style="text-align:right; padding:2px;">₹{{round_off}}</td></tr>';
        }
        $html .= '<tr><td style="text-align:right; padding:4px; font-weight:bold;">Grand Total:</td><td style="text-align:right; padding:4px; font-weight:bold;">₹{{grand_total}}</td></tr>';
        
        if ($s['show_amount_words'] ?? true) {
            $html .= '<tr><td colspan="2" style="padding:4px; font-style:italic;">{{amount_in_words}}</td></tr>';
        }
        $html .= '</table>';
        return $html;
    }

    /**
     * Compile payment-info block
     */
    private function _compile_payment_info($s, $fs) {
        $html = '<div style="font-size:'.$fs.'; margin-top:10px;"><strong>Payment Details</strong><table style="width:100%; border-collapse:collapse; margin-top:5px;">';
        
        if ($s['show_cash'] ?? true) $html .= '<tr><td style="padding:2px;">Cash:</td><td style="padding:2px; text-align:center; width:5%;">₹</td><td style="text-align:right; padding:2px; width:25%;">{{cash_amount}}</td></tr>';
        if ($s['show_card'] ?? true) $html .= '<tr><td style="padding:2px;">Card:</td><td style="padding:2px; text-align:center; width:5%;">₹</td><td style="text-align:right; padding:2px; width:25%;">{{card_amount}}</td></tr>';
        if ($s['show_upi'] ?? true) $html .= '<tr><td style="padding:2px;">UPI:</td><td style="padding:2px; text-align:center; width:5%;">₹</td><td style="text-align:right; padding:2px; width:25%;">{{upi_amount}}</td></tr>';
        if ($s['show_cheque'] ?? true) $html .= '<tr><td style="padding:2px;">Cheque:</td><td style="padding:2px; text-align:center; width:5%;">₹</td><td style="text-align:right; padding:2px; width:25%;">{{cheque_amount}}</td></tr>';
        if ($s['show_balance'] ?? true) $html .= '<tr style="font-weight:bold;"><td style="padding:2px;">Balance:</td><td style="padding:2px; text-align:center; width:5%;">₹</td><td style="text-align:right; padding:2px; width:25%;">{{balance_amount}}</td></tr>';
        
        $html .= '</table></div>';
        return $html;
    }

    /**
     * Compile signature block
     */
    private function _compile_signature($s, $fs) {
        $html = '<div style="display:flex; justify-content:space-between; margin-top:' . ($s['spacing_top'] ?? '40px') . '; text-align:center;">';
        
        if ($s['show_customer_sign'] ?? true) {
            $html .= '<div><div style="width:150px; margin:0 auto;"></div><div style="font-size:11px; margin-top:4px;">Customer Signature</div></div>';
        }
        if (!empty($s['show_cashier'])) {
            $html .= '<div><div style="width:150px; margin:0 auto;"></div><div style="font-size:11px; margin-top:4px;">Cashier</div></div>';
        }
        if ($s['show_auth_sign'] ?? true) {
            $html .= '<div><div style="width:150px; margin:0 auto;"></div><div style="font-size:11px; margin-top:4px;">Authorized Signatory</div></div>';
        }
        
        $html .= '</div>';
        return $html;
    }

    /**
     * Compile amount-words block
     */
    private function _compile_amount_words($s, $fs) {
        $prefix = ($s['show_prefix'] ?? true) ? 'Rupees ' : '';
        $bold = !empty($s['font_weight']) ? 'font-weight:bold;' : '';
        return '<div style="font-size:'.$fs.'; '.$bold.'">' . $prefix . '{{amount_in_words}}</div>';
    }

    // AJAX: Load design for GrapesJS
    // public function load_design($id) {
    //     $template = $this->print_template_model->get_by_id($id);
    //     // Check if gjs_data is valid JSON
    //     $gjs_data = json_decode($template['gjs_data'], true);
        
    //     header('Content-Type: application/json');
    //     echo json_encode($gjs_data ?: []);
    // }

    // AJAX: Get available fields/placeholders
    public function get_fields($category) {
        $fields = $this->print_template_model->get_placeholders($category);
        header('Content-Type: application/json');
        echo json_encode($fields);
    }

    // AJAX: Get custom block templates (HTML)
    public function get_block_templates() {
        $templates = [];

        // Company Header
        $templates['company-header'] = '
            <div data-gjs-type="text" class="print-header" style="text-align:center; margin:-20px 0 0 0; font-size:12px; padding-bottom:10px; margin-bottom:10px;">
                <img src="{{company_logo_url}}" style="width:auto;height:50px;">
                <div style="font-size:20px; font-weight:bold; margin-top:5px;">{{company_name}}</div>
                <div style="font-size:12px;"><strong>{{company_address}} | {{company_city}} - {{company_pincode}} | Phone : {{company_mobile}}</strong><br></div>
                <div style="font-size:12px;"><strong>GSTIN : {{company_gstin}} | PAN : {{company_pan}}</strong><br></div>
            </div>';

        // Customer Info Block
        $templates['customer-info'] = '
            <table data-gjs-type="resizable-block" class="customer-info" style="width:100%; border-collapse:collapse; margin-top:0px; font-size: 12px; font-weight: bold; margin-bottom: 10px;">
                <tr>
                    <td style="width:33%; border:none; vertical-align:top; padding-right:10px;">
                        <table style="width:100%; line-height: 1.4;">
                            <tr><td><span style="font-weight:bold; font-size:14px; text-transform:uppercase">{{customer_name}}</span></td></tr>
                            <tr><td>{{customer_address}}</td></tr>
                            <tr><td>{{customer_city}} - {{customer_pincode}}</td></tr>
                            <tr><td>Mobile : {{customer_mobile}}</td></tr>
                            <tr><td>GSTIN : {{customer_gstin}}</td></tr>
                            <tr><td>PAN : {{customer_pan}}</td></tr>
                        </table>
                    </td>
                    <td style="width:33%; border:none; vertical-align:top; text-align:center;"></td>
                    <td style="width:34%; border:none; vertical-align:top; padding-left:10px;">
                        <table style="width:100%; line-height: 1.4;">
                            <tr><td style="width:40%;">Invoice No</td><td style="width:5%;">:</td><td style="width:55%;">{{invoice_no}}</td></tr>
                            <tr><td>Date</td><td>:</td><td>{{bill_date}}</td></tr>
                            <tr><td>Time</td><td>:</td><td>{{bill_time}}</td></tr>
                            <tr><td>GOLD 22-KT</td><td>:</td><td>{{gold_rate}}/GM</td></tr>
                            <tr><td>GOLD 18-KT</td><td>:</td><td>{{gold_rate_18ct}}/GM</td></tr>
                            <tr><td>SILVER</td><td>:</td><td>{{silver_rate}}/GM</td></tr>
                        </table>
                    </td>
                </tr>
            </table>';



        $templates['order-receipt-table'] = '
            <div class="table-wrapper" data-gjs-type="table-wrapper" style="display:block; padding:5px; ">
                <div style="text-align:center; font-weight:bold; margin-bottom:5px;">ORDER RECEIPT</div>
                <div style="text-align:center; margin-bottom:10px;">Order No: {{invoice_no}}</div>
                <table class="order-receipt-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="">
                            <th style="padding:4px; text-align:center;">S.NO</th>
                            <th style="padding:4px; text-align:left;">DESCRIPTION</th>
                            <th style="padding:4px; text-align:center;">HSN</th>
                            <th style="padding:4px; text-align:center;">PCS</th>
                            <th style="padding:4px; text-align:right;">GRS.WT</th>
                            <th style="padding:4px; text-align:right;">NET.WT</th>
                            <th style="padding:4px; text-align:right;">V.A</th>
                            <th style="padding:4px; text-align:right;">RATE</th>
                            <th style="padding:4px; text-align:right;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- {{#order_items}} -->
                        <tr>
                            <td style="padding:4px; text-align:center;">{{sno}}</td>
                            <td style="padding:4px;">{{description}}</td>
                             <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                             <td style="padding:4px; text-align:center;">{{qty}}</td>
                            <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{va_content}}</td>
                            <td style="padding:4px; text-align:right;">{{rate}}</td>
                            <td style="padding:4px; text-align:right;">{{amount}}</td>
                        </tr>
                        <!-- {{/order_items}} -->
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:bold;">
                            <td colspan="3">Total</td>
                            <td style="padding:4px; text-align:center;">{{total_qty}}</td>
                            <td style="padding:4px; text-align:right;">{{total_gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{total_net_wt}}</td>
                            <td colspan="2"></td>
                            <td style="padding:4px; text-align:right;">{{order_amount}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>';

        // Advance Payment Block
        $templates['advance-payment-block'] = '
            <div data-gjs-type="resizable-block" class="advance-payment-block" style="padding:10px; font-size:12px; font-weight:bold; text-align:right;">
                <div style="margin-bottom:5px;">
                    <span>SUB TOTAL</span>
                    <span style="margin-left:20px;">₹ {{sub_total}}</span>
                </div>
                <div style="margin-bottom:10px; padding-bottom: 5px;">
                    <span>Approx Total Amount</span>
                    <span style="margin-left:20px;">₹ {{order_amount}}</span>
                </div>
                
                 <div style="margin-bottom:5px;">
                    <span>Order Amount</span>
                    <span style="margin-left:20px;">₹ {{order_amount}}</span>
                </div>
                 <div style="margin-bottom:5px;">
                    <span>Advance Amount</span>
                    <span style="margin-left:20px;">₹ {{total_paid}}</span>
                </div>
                
                <!-- {{#payment_breakdown}} -->
                <div style="margin-bottom:5px;">
                    <span>{{mode}}</span>
                    <span style="margin-left:20px;">₹ {{amount}}</span>
                </div>
                <!-- {{/payment_breakdown}} -->

                <div data-gjs-type="resizable-block" class="advance-payment-block" style="margin-top:10px; padding-top: 5px; font-size: 14px;">
                    <span>Approx Balance Amount</span>
                    <span style="margin-left:20px;">₹ {{balance_amount}}</span>
                </div>
            </div>';

        // Order Delivery Table
        $templates['order-delivery-table'] = '
            <div data-gjs-type="resizable-block" class="table-wrapper" style="display:block; padding:5px; ">
                <div style="text-align:center; font-weight:bold; margin-bottom:5px;">ORDER DELIVERY</div>
                <div style="text-align:center; margin-bottom:10px;">Order No: {{invoice_no}}</div>
                <table class="order-delivery-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="">
                            <th style="padding:4px; text-align:center;">S.NO</th>
                            <th style="padding:4px; text-align:left;">DESCRIPTION</th>
                            <th style="padding:4px; text-align:center;">HSN</th>
                            <th style="padding:4px; text-align:center;">PCS</th>
                            <th style="padding:4px; text-align:right;">GRS.WT</th>
                            <th style="padding:4px; text-align:right;">NET.WT</th>
                            <th style="padding:4px; text-align:right;">V.A</th>
                            <th style="padding:4px; text-align:right;">MC</th>
                            <th style="padding:4px; text-align:right;">RATE</th>
                            <th style="padding:4px; text-align:right;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- {{#order_delivery_items}} -->
                        <tr>
                            <td style="padding:4px; text-align:center;">{{sno}}</td>
                            <td style="padding:4px;">{{description}}</td>
                             <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                             <td style="padding:4px; text-align:center;">{{qty}}</td>
                            <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{va_content}}</td>
                            <td style="padding:4px; text-align:right;">{{mc}}</td>
                            <td style="padding:4px; text-align:right;">{{rate}}</td>
                            <td style="padding:4px; text-align:right;">{{amount}}</td>
                        </tr>
                        <!-- {{/order_delivery_items}} -->
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:bold;">
                            <td colspan="3">Total</td>
                            <td style="padding:4px; text-align:center;">{{total_qty}}</td>
                            <td style="padding:4px; text-align:right;">{{total_gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{total_net_wt}}</td>
                            <td colspan="4"></td>
                            <td style="padding:4px; text-align:right;">{{grand_total}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>';

        // Order Advance Details Block
        $templates['order-advance-details-block'] = '
            <div class="order-advance-details-block" style="display:flex; justify-content:space-between; margin-top:10px; font-size:12px;">
                <!-- Left: Advance History -->
                <div data-gjs-type="resizable-block" class="order-advance-details-block" style="width:45%;">
                    <div style="font-weight:bold; margin-bottom:5px;">Order Advance Details</div>
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="">
                                <th style="text-align:left; padding:2px;">Date</th>
                                <th style="text-align:right; padding:2px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- {{#advance_details}} -->
                            <tr>
                                <td style="padding:2px;">{{date}}</td>
                                <td style="padding:2px; text-align:right;">{{amount}}</td>
                            </tr>
                            <!-- {{/advance_details}} -->
                        </tbody>
                        <tfoot>
                            <tr style="font-weight:bold;">
                                <td style="padding:2px;">Total</td>
                                <td style="padding:2px; text-align:right;">{{order_advance_adj}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Right: Totals & Net Pay -->
                <div data-gjs-type="resizable-block" class="order-advance-details-block" style="width:45%; text-align:right;">
                     <div style="margin-bottom:2px;">
                        <span style="display:inline-block; width:120px;">SUB TOTAL</span>
                        <span style="font-weight:bold;">₹ {{sub_total}}</span>
                    </div>
                     <div style="margin-bottom:2px;">
                        <span style="display:inline-block; width:120px;">SGST</span>
                        <span style="font-weight:bold;">₹ {{sgst}}</span>
                    </div>
                     <div style="margin-bottom:2px;">
                        <span style="display:inline-block; width:120px;">CGST</span>
                        <span style="font-weight:bold;">₹ {{cgst}}</span>
                    </div>
                     <div style="margin-bottom:5px;">
                        <span style="display:inline-block; width:120px;">Round Off</span>
                        <span style="font-weight:bold;">₹ {{round_off}}</span>
                    </div>
                    <div style="margin-bottom:10px; padding-top:2px;">
                        <span style="display:inline-block; width:120px; font-weight:bold;">TOTAL</span>
                        <span style="font-weight:bold;">₹ {{grand_total}}</span>
                    </div>
                    
                    <div style="margin-bottom:5px; font-weight:bold;">
                        <span style="display:inline-block; width:150px;">ORDER ADVANCE ADJ</span>
                        <span>₹ {{order_advance_adj}}</span>
                    </div>
                    <div style="font-weight:bold; font-size:14px;">
                        <span style="display:inline-block; width:150px;">Cash Received</span>
                        <span>₹ {{cash_received}}</span>
                    </div>
                </div>
            </div>';



        // Credit Collection Table
        $templates['credit-collection-table'] = '
            <div class="table-wrapper" data-gjs-type="table-wrapper" style="display:block; padding:5px; ">
                <div style="text-align:center; font-weight:bold; margin-bottom:5px;">CREDIT COLLECTION</div>
                <table class="credit-collection-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="">
                            <th style="padding:4px; text-align:left;">Description</th>
                            <th style="padding:4px; text-align:right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:10px 4px;">Received with thanks from {{customer_name}} Towards Credit Bill No : {{credit_ref_bill_no}} Dated : {{credit_ref_bill_date}}</td>
                            <td style="padding:10px 4px; text-align:right;">{{grand_total}}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:bold;">
                            <td style="padding:4px; text-align:right;">Cash Received</td>
                            <td style="padding:4px; text-align:right;">₹ {{grand_total}}</td>
                        </tr>
                    </tfoot>
                </table>
                 <div style="margin-top:10px; font-style:italic;">
                    ({{amount_in_words}})
                </div>
            </div>';

        // Credit Collection Details Block (History)
        $templates['credit-collection-details-block'] = '
            <div data-gjs-type="resizable-block" class="credit-collection-details-block" style="margin-top:10px; font-size:12px;">
                <div style="font-weight:bold; margin-bottom:5px;">Credit Collection Details :</div>
                <table style="width:100%; border-collapse:collapse;">
                    <tbody>
                        <!-- {{#credit_collection_history}} -->
                        <tr>
                            <td style="padding:2px;">{{label}}</td>
                            <td style="padding:2px;">Dated : {{date}}</td>
                            <td style="padding:2px; text-align:right;">Rs.</td>
                            <td style="padding:2px; text-align:right;">{{amount}}</td>
                        </tr>
                        <!-- {{/credit_collection_history}} -->
                    </tbody>
                    <tfoot>
                         <tr style="">
                            <td colspan="2" style="padding:4px; font-weight:bold;">Balance Credit Amount</td>
                            <td style="padding:4px; text-align:right; font-weight:bold;">Rs.</td>
                            <td style="padding:4px; text-align:right;">{{credit_balance_amount}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>';

        // Purchase Bill Table
        $templates['purchase-bill-table'] = '
             <div class="table-wrapper" data-gjs-type="table-wrapper" style="display:block; padding:5px; ">
                <div style="text-align:center; font-weight:bold; margin-bottom:5px;">PURCHASE BILL</div>
                <table class="purchase-bill-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="">
                            <th style="padding:4px; text-align:center;">S.NO</th>
                            <th style="padding:4px; text-align:left;">DESCRIPTION</th>
                            <th style="padding:4px; text-align:right;">GRSWT</th>
                            <th style="padding:4px; text-align:right;">STN LESS</th>
                            <th style="padding:4px; text-align:right;">MTL LESS</th>
                            <th style="padding:4px; text-align:right;">NETWT</th>
                            <th style="padding:4px; text-align:right;">RATE</th>
                            <th style="padding:4px; text-align:right;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- {{#purchase_items}} -->
                        <tr>
                            <td style="padding:4px; text-align:center;">{{sno}}</td>
                            <td style="padding:4px;">{{description}}</td>
                            <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{stn_less}}</td>
                            <td style="padding:4px; text-align:right;">{{mtl_less}}</td>
                            <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{rate}}</td>
                            <td style="padding:4px; text-align:right;">{{amount}}</td>
                        </tr>
                        <!-- {{/purchase_items}} -->
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:bold;">
                            <td colspan="2">Total</td>
                            <td style="padding:4px; text-align:right;">{{total_gross_wt}}</td>
                            <td colspan="2"></td>
                            <td style="padding:4px; text-align:right;">{{total_net_wt}}</td>
                            <td></td>
                            <td style="padding:4px; text-align:right;">{{purchase_total_amount}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>';

        // Purchase Amount Block
        $templates['purchase-amount-block'] = '
            <div data-gjs-type="resizable-block" class="purchase-amount-block" style="text-align:right; margin-top:10px; font-size:12px; font-weight:bold;">
                <div style="margin-bottom:5px;">
                    <span style="display:inline-block; width:150px;">Purchase Amount</span>
                    <span style="margin-left:20px;">₹ {{purchase_total_amount}}</span>
                </div>
                <div>
                     <span style="display:inline-block; width:150px;">Cash Paid</span>
                    <span style="margin-left:20px;">₹ {{purchase_cash_paid}}</span>
                </div>
            </div>';

        // Sales & Purchase Bill Table (Combined)
        $templates['sales-purchase-table'] = '
            <div data-gjs-type="table-wrapper" class="table-wrapper" style="display:block; padding:5px; ">
                <div style="text-align:center; font-weight:bold; margin-bottom:5px;">SALES AND PURCHASE BILL</div>
                
                <!-- Sales Section — only shown when this bill has sales items -->
                <!-- {{#has_sales_items}} -->
                <table class="sales-table" style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:10px;">
                    <thead>
                        <tr style="">
                            <th style="padding:4px; text-align:center;">S.NO</th>
                            <th style="padding:4px; text-align:left;">DESCRIPTION</th>
                            <th style="padding:4px; text-align:center;">HSN</th>
                            <th style="padding:4px; text-align:center;">PCS</th>
                            <th style="padding:4px; text-align:right;">GRS.WT</th>
                            <th style="padding:4px; text-align:right;">NET.WT</th>
                            <th style="padding:4px; text-align:right;">V.A</th>
                            <th style="padding:4px; text-align:right;">MC</th>
                            <th style="padding:4px; text-align:right;">RATE</th>
                            <th style="padding:4px; text-align:right;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- {{#order_delivery_items}} -->
                        <tr>
                            <td style="padding:4px; text-align:center;">{{sno}}</td>
                            <td style="padding:4px;">{{description}}</td>
                             <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                             <td style="padding:4px; text-align:center;">{{qty}}</td>
                            <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{va_content}}</td>
                            <td style="padding:4px; text-align:right;">{{mc}}</td>
                            <td style="padding:4px; text-align:right;">{{rate}}</td>
                            <td style="padding:4px; text-align:right;">{{amount}}</td>
                        </tr>
                        <!-- {{/order_delivery_items}} -->
                    </tbody>
                     <tfoot>
                        <tr style="font-weight:bold;">
                            <td colspan="2">Total</td>
                            <td colspan="2"></td>
                            <td style="padding:4px; text-align:right;">{{total_gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{total_net_wt}}</td>
                            <td colspan="3"></td>
                            <td style="padding:4px; text-align:right;">{{sales_total_amount}}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Sales Totals -->
                <div data-gjs-type="resizable-block" style="text-align:right; font-size:12px; margin-bottom:15px;">
                     <div style="margin-bottom:2px;">
                        <span style="display:inline-block; width:120px;">DISCOUNT</span>
                        <span style="font-weight:bold;">₹ {{discount}}</span>
                    </div>
                    <div style="margin-bottom:2px;">
                        <span style="display:inline-block; width:120px;">SUB TOTAL</span>
                        <span style="font-weight:bold;">₹ {{sub_total}}</span>
                    </div>
                     <div style="margin-bottom:2px;">
                        <span style="display:inline-block; width:120px;">SGST</span>
                        <span style="font-weight:bold;">₹ {{sgst}}</span>
                    </div>
                     <div style="margin-bottom:2px;">
                        <span style="display:inline-block; width:120px;">CGST</span>
                        <span style="font-weight:bold;">₹ {{cgst}}</span>
                    </div>
                    <div style="padding-top:2px;">
                        <span style="display:inline-block; width:120px; font-weight:bold;">TOTAL</span>
                        <span style="font-weight:bold;">₹ {{sales_total_amount}}</span>
                    </div>
                </div>
                <!-- {{/has_sales_items}} -->

                <!-- Purchase Section -->
                <div data-gjs-type="resizable-block" style="font-weight:bold; margin-bottom:5px; padding-top:10px;">PURCHASE INVOICE NO : {{purchase_invoice_no}}</div>
                 <table class="purchase-bill-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="">
                            <th style="padding:4px; text-align:center;">S.NO</th>
                            <th style="padding:4px; text-align:left;">DESCRIPTION</th>
                            <th style="padding:4px; text-align:right;">GRSWT</th>
                            <th style="padding:4px; text-align:right;">STN LESS</th>
                            <th style="padding:4px; text-align:right;">MTL LESS</th>
                            <th style="padding:4px; text-align:right;">NETWT</th>
                            <th style="padding:4px; text-align:right;">RATE</th>
                            <th style="padding:4px; text-align:right;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- {{#purchase_items}} -->
                        <tr>
                            <td style="padding:4px; text-align:center;">{{sno}}</td>
                            <td style="padding:4px;">{{description}}</td>
                            <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{stn_less}}</td>
                            <td style="padding:4px; text-align:right;">{{mtl_less}}</td>
                            <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{rate}}</td>
                            <td style="padding:4px; text-align:right;">{{amount}}</td>
                        </tr>
                        <!-- {{/purchase_items}} -->
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:bold;">
                            <td colspan="2">Total</td>
                            <td style="padding:4px; text-align:right;">{{purchase_total_gross_wt}}</td> <!-- Assuming need logic for this or reuse sales total var if collision, need separate var -->
                             <td colspan="2"></td>
                            <td style="padding:4px; text-align:right;">{{purchase_total_net_wt}}</td> <!-- Schema collision? Helper needs distinct total vars? Helper uses `purchase_items` which is unique. But `total_gross_wt` is generic. Helper calculates purchase specific totals? Yes, lets update check. -->
                            <td></td>
                            <td style="padding:4px; text-align:right;">{{purchase_total_amount}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>';

        // Amount Received Details Block (New)
        $templates['amount-received-details'] = '
            <div data-gjs-type="resizable-block" class="amount-received-details-block" style="padding:5px; margin-top:10px; font-size:12px; display:flex; justify-content:space-between;">
                <!-- Left Side: Detailed References -->
                <div style="flex:1; padding-right:10px;">
                     <!-- {{#payment_details_full}} -->
                    <div style="margin-bottom:3px;">
                        <span style="font-weight:bold;">{{mode_label}}</span> {{details}}
                    </div>
                     <!-- {{/payment_details_full}} -->
                </div>
                
                <!-- Right Side: Amount Summary -->
                <div style="width:250px; text-align:right;">
                    <!-- {{#payment_breakdown}} -->
                    <div style="display:flex; justify-content:space-between; margin-bottom:3px;">
                        <span style="font-weight:bold;">{{mode}}</span>
                        <span>₹ {{amount}}</span>
                    </div>
                    <!-- {{/payment_breakdown}} -->
                </div>
            </div>';

        // Purchase Invoice Block (Summary)
        $templates['purchase-invoice-block'] = '
            <div data-gjs-type="resizable-block" class="purchase-invoice-block" style="text-align:right; margin-top:10px; font-size:12px; font-weight:bold;">
                <!-- {{#has_sales_items}} -->
                <div style="border-top:1px dashed #000; margin-bottom:5px; padding-top:5px;"></div>
                <div style="margin-bottom:5px;">
                    <span style="display:inline-block; width:150px;">Sales Amount</span>
                    <span style="margin-left:20px;">₹ {{sales_total_amount}}</span>
                </div>
                <!-- {{/has_sales_items}} -->
                <div style="margin-bottom:5px;">
                    <span style="display:inline-block; width:150px;">Purchase Amount</span>
                    <span style="margin-left:20px;">₹ {{purchase_total_amount}}</span>
                </div>
                <div style="margin-bottom:5px;">
                    <span style="display:inline-block; width:150px;">Return Amount</span>
                    <span style="margin-left:20px;">₹ {{lbl_return_amount}}</span>
                </div>
                 <div style="margin-bottom:5px;">
                    <span style="display:inline-block; width:150px;">Round Off</span>
                    <span style="margin-left:20px;">₹ {{round_off}}</span>
                </div>
                <div style="padding-top:5px; margin-bottom:10px;">
                     <span style="display:inline-block; width:150px;">Net Amount</span>
                    <span style="margin-left:20px;">₹ {{net_amount}}</span>
                </div>
                
                <!-- Payment Modes -->
                <!-- {{#payment_breakdown}} -->
                <div style="margin-bottom:2px; font-size:11px;">
                    <span style="display:inline-block; width:150px;">{{mode}}</span>
                    <span style="margin-left:20px;">₹ {{amount}}</span>
                </div>
                <!-- {{/payment_breakdown}} -->
            </div>';

        // Chit Pre Close Table
        $templates['chit-pre-close-table'] = '  
            <div data-gjs-type="table-wrapper" class="table-wrapper" style="display:block; padding:5px; ">
                <table class="chit-pre-close-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="">
                            <th style="padding:4px; text-align:left;">S.No</th>
                            <th style="padding:4px; text-align:center;">Ref No</th>
                            <th style="padding:4px; text-align:right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- {{#chit_pre_close_items}} -->
                        <tr>
                            <td style="padding:4px; text-align:left;">{{sno}}</td>
                            <td style="padding:4px; text-align:center;">{{ref_no}}</td>
                            <td style="padding:4px; text-align:right;">{{amount}}</td>
                        </tr>
                        <!-- {{/chit_pre_close_items}} -->
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:bold; ">
                            <td colspan="2" style="padding:4px; text-align:right;">Advance</td>
                            <td style="padding:4px; text-align:right;">₹ {{chit_pre_close_total}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>';

        // Sales Return Table (Exchange)
        $templates['sales-return-table'] = '
            <div data-gjs-type="table-wrapper" class="sales-return-wrapper" style="display:block; padding:5px; ">
                <!-- Header Section -->
                <div style="font-weight:bold; margin-bottom:5px; padding-top:5px;">
                    SALES RETURN INVOICE NO : {{s_ret_invoice_no}}
                </div>
                
                <table class="sales-return-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="">
                            <th style="padding:4px; text-align:left;">S.NO</th>
                            <th style="padding:4px; text-align:left;">DESCRIPTION</th>
                            <th style="padding:4px; text-align:center;">HSN</th>
                            <th style="padding:4px; text-align:center;">PCS</th>
                            <th style="padding:4px; text-align:right;">GRS.WT</th>
                            <th style="padding:4px; text-align:right;">NET.WT</th>
                            <th style="padding:4px; text-align:right;">V.A</th>
                            <th style="padding:4px; text-align:right;">MC</th>
                            <th style="padding:4px; text-align:right;">RATE</th>
                            <th style="padding:4px; text-align:right;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- {{#sales_return_items}} -->
                        <tr>
                            <td style="padding:4px; text-align:left;">{{sno}}</td>
                            <td style="padding:4px; text-align:left;">{{description}}</td>
                            <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                            <td style="padding:4px; text-align:center;">{{qty}}</td>
                            <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                             <td style="padding:4px; text-align:right;">{{va}}</td>
                            <td style="padding:4px; text-align:right;">{{mc}}</td>
                            <td style="padding:4px; text-align:right;">{{rate}}</td>
                            <td style="padding:4px; text-align:right;">{{amount}}</td>
                        </tr>
                        <!-- {{/sales_return_items}} -->
                    </tbody>
                    <tfoot>
                         <tr style="font-weight:bold;">
                            <td colspan="9" style="text-align:right; padding:4px;">TOTAL</td>
                            <td style="text-align:right; padding:4px;">₹ {{sales_return_total}}</td>
                        </tr>
                    </tfoot>
                </table>
                
                <!-- Summary Section matching image -->
                 <div style="margin-top:10px; text-align:right; font-weight:bold; font-size:12px;">
                    <div style="margin-bottom:2px;">
                        <span style="display:inline-block; width:120px;">Sales Amount</span>
                        <span style="display:inline-block; width:100px;">₹ {{lbl_sales_amount}}</span>
                    </div>
                    <div style="margin-bottom:2px;">
                        <span style="display:inline-block; width:120px;">Exchange Amount</span>
                         <span style="display:inline-block; width:100px;">₹ {{lbl_exchange_amount}}</span>
                    </div>
                    <div style="margin-bottom:5px; display:inline-block;">
                         <span style="display:inline-block; width:120px;">Net Amount</span>
                        <span style="display:inline-block; width:100px;">₹ {{lbl_net_amount}}</span>
                    </div>
                     <div>
                        <span style="display:inline-block; width:120px;">Cash Received</span>
                        <span style="display:inline-block; width:100px;">₹ {{cash_received}}</span>
                    </div>
                </div>

            </div>';

        // Sales Return Tax Table (With Taxes & Advance)
        $templates['sales-return-tax-table'] = '
            <div class="sales-return-tax-wrapper" data-gjs-type="table-wrapper" style="display:block; padding:5px; ">
                 <div style="text-align:center; font-weight:bold; margin-bottom:10px;">SALES RETURN</div>
                 <div style="font-weight:bold; margin-bottom:5px; padding-bottom:5px;">
                    SALES RETURN INVOICE NO : {{s_ret_invoice_no}}
                </div>
                 <!-- Optional Reference line if needed -->
                 <div style="font-weight:bold; margin-bottom:5px; font-size:12px;">
                    EXCHANGE (REFER SALE BILL NO : {{sales_ref_no}})
                 </div>

                <table class="sales-return-tax-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="">
                            <th style="padding:4px; text-align:left;">S.NO</th>
                            <th style="padding:4px; text-align:left;">DESCRIPTION</th>
                            <th style="padding:4px; text-align:center;">HSN</th>
                            <th style="padding:4px; text-align:center;">PCS</th>
                            <th style="padding:4px; text-align:right;">GRS.WT</th>
                            <th style="padding:4px; text-align:right;">NET.WT</th>
                            <th style="padding:4px; text-align:right;">V.A</th>
                            <th style="padding:4px; text-align:right;">MC</th>
                            <th style="padding:4px; text-align:right;">RATE</th>
                            <th style="padding:4px; text-align:right;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- {{#sales_return_items}} -->
                        <tr>
                            <td style="padding:4px; text-align:left;">{{sno}}</td>
                            <td style="padding:4px; text-align:left;">{{description}}</td>
                            <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                            <td style="padding:4px; text-align:center;">{{qty}}</td>
                            <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                             <td style="padding:4px; text-align:right;">{{va}}</td>
                            <td style="padding:4px; text-align:right;">{{mc}}</td>
                            <td style="padding:4px; text-align:right;">{{rate}}</td>
                            <td style="padding:4px; text-align:right;">{{amount}}</td>
                        </tr>
                         <!-- {{/sales_return_items}} -->
                    </tbody>
                    <tfoot>
                         <tr style="font-weight:bold;">
                            <td colspan="9" style="text-align:right; padding:4px;">Total</td>
                            <td style="text-align:right; padding:4px;">{{sales_return_total}}</td>
                        </tr>
                        <tr>
                             <td colspan="9" style="text-align:right; padding:4px;">SGST {{sgst_percent}}%</td>
                             <td style="text-align:right; padding:4px;">₹ {{sgst}}</td>
                        </tr>
                        <tr>
                             <td colspan="9" style="text-align:right; padding:4px;">CGST {{cgst_percent}}%</td>
                             <td style="text-align:right; padding:4px;">₹ {{cgst}}</td>
                        </tr>
                         <tr style="font-weight:bold; ">
                             <td colspan="9" style="text-align:right; padding:4px;">TOTAL</td>
                             <td style="text-align:right; padding:4px;">₹ {{total_amount}}</td>
                        </tr>
                        <tr style="font-weight:bold;">
                             <td colspan="9" style="text-align:right; padding:4px;">Advance</td>
                             <td style="text-align:right; padding:4px;">₹ {{total_amount}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>';

        // Bill Footer V2
        $templates['bill-footer-v2'] = '
             <div class="bill-footer-v2-block" data-gjs-type="resizable-text-block" style="margin-top:10px; padding: 5px; display:flex; justify-content:space-between; align-items:flex-start; font-size:12px;">
                    <table style="width:100%; border-collapse:collapse; font-size:inherit;">
                        <tr>
                            <td style="padding:2px; text-align:right; width: 70%;">Subtotal:</td>
                            <td style="padding:2px; text-align:center; width: 5%;">₹</td>
                            <td style="padding:2px; text-align:right; font-weight:bold; width: 25%;">{{sub_total}}</td>
                        </tr>
                        <tr>
                            <td style="padding:2px; text-align:right;">CGST:</td>
                            <td style="padding:2px; text-align:center;">₹</td>
                            <td style="padding:2px; text-align:right;">{{cgst_amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding:2px; text-align:right;">SGST:</td>
                            <td style="padding:2px; text-align:center;">₹</td>
                            <td style="padding:2px; text-align:right;">{{sgst_amount}}</td>
                        </tr>
                         <tr>
                            <td style="padding:2px; text-align:right;">IGST:</td>
                            <td style="padding:2px; text-align:center;">₹</td>
                            <td style="padding:2px; text-align:right;">{{igst_amount}}</td>
                        </tr>
                         <tr>
                            <td style="padding:2px; text-align:right;">Round Off:</td>
                            <td style="padding:2px; text-align:center;">₹</td>
                            <td style="padding:2px; text-align:right;">{{round_off}}</td>
                        </tr>
                        <tr style="">
                            <td style="padding:4px; text-align:right; font-weight:bold;">Grand Total:</td>
                            <td style="padding:4px; text-align:center; font-weight:bold;">₹</td>
                            <td style="padding:4px; text-align:right; font-weight:bold;">{{grand_total}}</td>
                        </tr>
                    </table>
            </div>';

        // Tax Summary / Grand Total
        $templates['tax-summary'] = '
            <div data-gjs-type="resizable-block" class="tax-summary-block" style="font-size:12px; padding:5px;">
                <table style="width:100%; border-collapse:collapse; font-size:inherit;">
                    <tr>
                        <td style="padding:2px; text-align:right; width:70%;">Subtotal:</td>
                        <td style="padding:2px; text-align:center; width:5%;">₹</td>
                        <td style="padding:2px; text-align:right; font-weight:bold; width:25%;">{{sub_total}}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px; text-align:right;">CGST @{{cgst_percent}}%:</td>
                        <td style="padding:2px; text-align:center;">₹</td>
                        <td style="padding:2px; text-align:right;">{{cgst_amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px; text-align:right;">SGST @{{sgst_percent}}%:</td>
                        <td style="padding:2px; text-align:center;">₹</td>
                        <td style="padding:2px; text-align:right;">{{sgst_amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px; text-align:right;">IGST @{{igst_percent}}%:</td>
                        <td style="padding:2px; text-align:center;">₹</td>
                        <td style="padding:2px; text-align:right;">{{igst_amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px; text-align:right;">Round Off:</td>
                        <td style="padding:2px; text-align:center;">₹</td>
                        <td style="padding:2px; text-align:right;">{{round_off}}</td>
                    </tr>
                    <tr style="">
                        <td style="padding:4px; text-align:right; font-weight:bold;">Grand Total:</td>
                        <td style="padding:4px; text-align:center; font-weight:bold;">₹</td>
                        <td style="padding:4px; text-align:right; font-weight:bold;">{{grand_total}}</td>
                    </tr>
                </table>
                <div style="margin-top:5px; font-style:italic;">{{amount_in_words}}</div>
            </div>';

        // Payment Modes
        $templates['payment-info'] = '
            <div data-gjs-type="resizable-block" class="payment-info-block" style="font-size:12px; padding:5px;">
                <div style="font-weight:bold; margin-bottom:5px;">Payment Details</div>
                <table style="width:100%; border-collapse:collapse; font-size:inherit;">
                    <tr>
                        <td style="padding:2px; width:70%;">Cash:</td>
                        <td style="padding:2px; text-align:center; width:5%;">₹</td>
                        <td style="padding:2px; text-align:right; width:25%;">{{cash_amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px;">Card:</td>
                        <td style="padding:2px; text-align:center;">₹</td>
                        <td style="padding:2px; text-align:right;">{{card_amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px;">UPI:</td>
                        <td style="padding:2px; text-align:center;">₹</td>
                        <td style="padding:2px; text-align:right;">{{upi_amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px;">Cheque:</td>
                        <td style="padding:2px; text-align:center;">₹</td>
                        <td style="padding:2px; text-align:right;">{{cheque_amount}}</td>
                    </tr>
                    <tr style="font-weight:bold;">
                        <td style="padding:2px;">Balance:</td>
                        <td style="padding:2px; text-align:center;">₹</td>
                        <td style="padding:2px; text-align:right;">{{balance_amount}}</td>
                    </tr>
                </table>
            </div>';

        echo json_encode($templates);
    }

    // AJAX: Live preview for the designer
    public function preview_live($id) {
        $json = file_get_contents('php://input');
        $config = json_decode($json, true);
        
        $template = $this->print_template_model->get_by_id($id);
        if (!$template) {
            echo json_encode(['html' => '<p>Template not found</p>']);
            return;
        }
        
        // Compile from POSTed config (current in-memory state) instead of stale DB data
        $compiled_html = '';
        $compiled_css = '';
        if ($config && isset($config['blocks'])) {
            $compiled_html = $this->_compile_blocks($config['blocks']);
            foreach ($config['blocks'] as $block) {
                if (!empty($block['css'])) {
                    $compiled_css .= $block['css'] . "\n";
                }
            }
        } else {
            // Fallback to DB if no config POSTed
            $compiled_html = $template['template_html'];
            $compiled_css = $template['template_css'];
        }
        
        // Render preview: show raw {{placeholder}} tags as styled blue chips
        // instead of replacing with sample data — so designers can see all variables
        $html = $this->print_template_model->render_preview_template($compiled_html);
        $css = $compiled_css;
        
        $full_html = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        $full_html .= '<style>body{margin:10px;font-family:Arial,sans-serif;font-size:13px;}table{border-collapse:collapse;width:100%;}td,th{padding:4px;}' . $css . '</style>';
        $full_html .= '</head><body>' . $html . '</body></html>';
        
        header('Content-Type: application/json');
        echo json_encode(['html' => $full_html]);
    }

    // AJAX: Get user-saved custom blocks
    public function get_custom_blocks() {
        header('Content-Type: application/json');
        
        // Check if table exists, return empty array if not
        if (!$this->db->table_exists('print_template_custom_blocks')) {
            echo json_encode([]);
            return;
        }
        
        $blocks = $this->db->order_by('id', 'DESC')
                           ->get('print_template_custom_blocks')
                           ->result_array();
        
        $result = [];
        foreach ($blocks as $b) {
            $result[] = [
                'id'    => $b['id'],
                'label' => $b['block_name'],
                'content' => $b['block_html']
            ];
        }
        echo json_encode($result);
    }

    // AJAX: Save a custom block from the Block Designer
    public function save_custom_block() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data['name']) || empty($data['html'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Name and HTML are required']);
            return;
        }

        // Create table if it doesn't exist
        if (!$this->db->table_exists('print_template_custom_blocks')) {
            $this->db->query("CREATE TABLE print_template_custom_blocks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                block_name VARCHAR(255) NOT NULL,
                block_html LONGTEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }

        $insert = [
            'block_name' => $data['name'],
            'block_html' => $data['html']
        ];

        $this->db->insert('print_template_custom_blocks', $insert);
        $id = $this->db->insert_id();

        header('Content-Type: application/json');
        if ($id) {
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database insert failed']);
        }
    }

    // Preview
    public function preview($id) {
        $template = $this->print_template_model->get_by_id($id);
        $sample_data = $this->print_template_model->get_sample_data($template['template_category']);
        
        // Render
        $html = $this->print_template_model->render_string_template($template['template_html'], $sample_data);
        // Append CSS
        $full_html = "<style>" . $template['template_css'] . "</style>" . $html;
        
        $data['html'] = $full_html;
        $data['paper_size'] = $template['paper_size'];
        $this->load->view('print_templates/preview', $data);
    }


    
    public function delete($id) {
        $this->print_template_model->delete($id);
        redirect('print-templates');
    }

    /**
     * AJAX: Bulk delete templates (max 100 at a time).
     * Before deletion, backs up gjs_data to:
     *   template/{template_code}/{template_name}/{created_datetime}_{id}.txt
     */
    public function bulk_delete() {
        header('Content-Type: application/json');

        $ids = $this->input->post('ids');
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['success' => false, 'message' => 'No templates selected.']);
            return;
        }

        // Sanitize IDs to integers
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, function($id) { return $id > 0; });

        if (count($ids) > 100) {
            echo json_encode(['success' => false, 'message' => 'Maximum 100 records allowed per bulk delete.']);
            return;
        }

        // Fetch all selected templates
        $templates = $this->print_template_model->get_by_ids($ids);
        if (empty($templates)) {
            echo json_encode(['success' => false, 'message' => 'No matching templates found.']);
            return;
        }

        // Backup gjs_data to files before deletion
        $backup_base = FCPATH . 'template';
        $backed_up = 0;

        foreach ($templates as $t) {
            $template_code = $this->_sanitize_folder_name($t['template_code'] ?: 'no_code');
            $template_name = $this->_sanitize_folder_name($t['template_name'] ?: 'no_name');
            $created_at    = $t['created_at'] ?: date('Y-m-d H:i:s');
            $tpl_id        = $t['id_template'];

            // Build folder path: template/{template_code}/{template_name}/
            $folder_path = $backup_base . DIRECTORY_SEPARATOR . $template_code . DIRECTORY_SEPARATOR . $template_name;

            // Create directory if it doesn't exist
            if (!is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            // Build filename: {datetime}_{id}.txt  (e.g. 2026-05-09_11-13-58_466.txt)
            $datetime_str = str_replace([':', ' '], ['-', '_'], $created_at);
            $filename = $datetime_str . '_' . $tpl_id . '.txt';
            $file_path = $folder_path . DIRECTORY_SEPARATOR . $filename;

            // Write gjs_data content to file
            $gjs_data = $t['gjs_data'] ?: '';
            file_put_contents($file_path, $gjs_data);
            $backed_up++;
        }

        // Delete all selected templates from DB
        $deleted = $this->print_template_model->delete_by_ids($ids);

        echo json_encode([
            'success'       => true,
            'deleted_count' => $deleted,
            'backed_up'     => $backed_up,
            'message'       => "Deleted {$deleted} template(s). Backed up {$backed_up} file(s)."
        ]);
    }

    /**
     * Sanitize a string for use as a folder name (remove unsafe characters).
     */
    private function _sanitize_folder_name($name) {
        // Replace spaces with underscores, remove anything not alphanumeric/underscore/hyphen/dot
        $name = preg_replace('/\s+/', '_', trim($name));
        $name = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $name);
        // Limit length to avoid filesystem issues
        if (strlen($name) > 100) {
            $name = substr($name, 0, 100);
        }
        return $name ?: 'unknown';
    }
    
    public function set_default($id) {
        $this->print_template_model->set_default($id);
        redirect('print-templates');
    }

    // ═══════════════════════════════════════════════════════════
    // V2 DESIGNER METHODS
    // ═══════════════════════════════════════════════════════════

    /**
     * V2 Designer Interface — standalone page without GrapesJS
     */
    public function designer_v2($id) {
        $data['template'] = $this->print_template_model->get_by_id($id);
        if (!$data['template']) {
            redirect('print-templates');
        }
        $data['categories'] = $this->print_template_model->get_template_categories();
        $this->load->view('print_templates/designer_v2', $data);
    }

    /**
     * AJAX: Enhanced field registry for V2 Field Picker
     */
    public function get_fields_v2($category) {
        header('Content-Type: application/json');
        $fields = $this->print_template_model->get_placeholders_v2($category);
        echo json_encode($fields);
    }

    /**
     * AJAX: Get MC/VA configuration
     */
    public function get_mc_va_config($doc_type) {
        header('Content-Type: application/json');
        $branch_id = $this->input->get('branch_id');
        $config = $this->print_template_model->get_mc_va_config($branch_id, $doc_type);
        echo json_encode($config ?: [
            'mc_mode' => 'per_gram', 'mc_label' => 'MC',
            'va_mode' => 'percentage', 'va_label' => 'VA %'
        ]);
    }

    /**
     * AJAX: Save MC/VA configuration
     */
    public function save_mc_va_config() {
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data || !isset($data['document_type'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }

        $config = [
            'id_branch'     => isset($data['id_branch']) ? $data['id_branch'] : null,
            'document_type' => $data['document_type'],
            'mc_mode'       => $data['mc_mode'] ?? 'per_gram',
            'mc_label'      => $data['mc_label'] ?? 'MC',
            'va_mode'       => $data['va_mode'] ?? 'percentage',
            'va_label'      => $data['va_label'] ?? 'VA %',
        ];

        $result = $this->print_template_model->save_mc_va_config($config);
        echo json_encode(['success' => (bool)$result]);
    }

    /**
     * AJAX: Load default template blocks for a category
     */
    public function load_default_template($category) {
        header('Content-Type: application/json');
        $defaults = $this->print_template_model->get_default_templates($category);
        
        // If no DB defaults, generate a standard layout programmatically
        if (empty($defaults)) {
            $blocks = $this->_generate_standard_template($category);
            echo json_encode([
                ['id' => 0, 'template_name' => 'Standard Layout', 'blocks_json' => json_encode($blocks)]
            ]);
            return;
        }
        
        echo json_encode($defaults);
    }

    /**
     * Generate a standard template layout for a category (when no DB defaults exist)
     */
    private function _generate_standard_template($category) {
        $blocks = [];
        $order = 0;

        // Company Header — always present
        $blocks[] = [
            'id' => 'def_' . ($order),
            'type' => 'company-header',
            'enabled' => true,
            'order' => $order++,
            'settings' => [
                'show_logo' => true, 'font_size' => '14px', 'text_align' => 'center',
                'border_bottom' => true, 'repeat_every_page' => true,
                'fields' => [
                    ['label' => '', 'placeholder' => 'company_name', 'group' => 'company', 'style' => 'title'],
                    ['label' => '', 'placeholder' => 'company_address', 'group' => 'company'],
                    ['label' => 'Ph:', 'placeholder' => 'company_mobile', 'group' => 'company'],
                    ['label' => 'GSTIN:', 'placeholder' => 'company_gstin', 'group' => 'company'],
                ]
            ]
        ];

        // Bill Title
        $blocks[] = [
            'id' => 'def_' . ($order),
            'type' => 'custom-text',
            'enabled' => true,
            'order' => $order++,
            'settings' => [
                'content' => '{{title}}', 'font_size' => '16px', 'text_align' => 'center', 'bold' => true
            ]
        ];

        // Customer Info
        $blocks[] = [
            'id' => 'def_' . ($order),
            'type' => 'customer-info',
            'enabled' => true,
            'order' => $order++,
            'settings' => [
                'layout' => 'two-column', 'show_borders' => true, 'font_size' => '12px',
                'fields' => [
                    ['label' => 'To:', 'placeholder' => 'customer_name', 'group' => 'customer'],
                    ['label' => '',    'placeholder' => 'customer_address', 'group' => 'customer'],
                    ['label' => '',    'placeholder' => 'customer_city', 'group' => 'customer', 'suffix' => ' - {{customer_pincode}}'],
                    ['label' => 'Ph:', 'placeholder' => 'customer_mobile', 'group' => 'customer'],
                    ['label' => 'INVOICE NO', 'placeholder' => 'invoice_no', 'group' => 'invoice'],
                    ['label' => 'DATE', 'placeholder' => 'bill_date', 'group' => 'invoice'],
                    ['label' => 'TIME', 'placeholder' => 'bill_time', 'group' => 'invoice'],
                    ['label' => 'GOLD 22-KT', 'placeholder' => 'gold_rate', 'group' => 'invoice', 'suffix' => '/GM'],
                    ['label' => 'SILVER', 'placeholder' => 'silver_rate', 'group' => 'invoice', 'suffix' => '/GM'],
                ]
            ]
        ];

        // Items Table (for categories that have items)
        if (in_array($category, [1,2,3,5,9,11,12,13,14,15,0])) {
            $blocks[] = [
                'id' => 'def_' . ($order),
                'type' => 'items-table',
                'enabled' => true,
                'order' => $order++,
                'settings' => [
                    'font_size' => '12px', 'show_footer_totals' => true,
                    'loop_key' => 'items', 'condition_key' => 'has_sales_items',
                    'columns' => ['sno','description','hsn_code','qty','gross_wt','net_wt','rate','amount']
                ]
            ];
        }

        // Tax Summary
        if (in_array($category, [1,2,3,7,9,11,12,13,14,15,0])) {
            $blocks[] = [
                'id' => 'def_' . ($order),
                'type' => 'tax-summary',
                'enabled' => true,
                'order' => $order++,
                'settings' => [
                    'font_size' => '12px', 'show_subtotal' => true,
                    'show_cgst' => true, 'show_sgst' => true, 'show_igst' => false,
                    'show_round_off' => true, 'show_amount_words' => true
                ]
            ];
        }

        // Payment (for bill categories)
        if (in_array($category, [1,2,3,6,8,9,0])) {
            $blocks[] = [
                'id' => 'def_' . ($order),
                'type' => 'payment-info',
                'enabled' => true,
                'order' => $order++,
                'settings' => [
                    'font_size' => '12px', 'show_cash' => true, 'show_card' => true,
                    'show_upi' => true, 'show_cheque' => true, 'show_balance' => true,
                    'condition_key' => 'has_payment_details'
                ]
            ];
        }

        // Signature
        $blocks[] = [
            'id' => 'def_' . ($order),
            'type' => 'signature-block',
            'enabled' => true,
            'order' => $order++,
            'settings' => [
                'layout' => 'two-column', 'show_customer_sign' => true,
                'show_auth_sign' => true, 'show_cashier' => false, 'spacing_top' => '40px'
            ]
        ];

        return ['version' => 1, 'blocks' => $blocks];
    }

    /**
     * AJAX: Save design from V2 Block Editor (versioned save, same as V1)
     */
    public function save_design_v2($id) {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No data']);
            return;
        }

        $current = $this->print_template_model->get_by_id($id);
        if (!$current) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Template not found']);
            return;
        }

        $group_code = !empty($current['code']) ? $current['code'] : $current['template_code'];
        $new_template_code = $group_code . '_' . time();

        // Extract paper settings if provided
        $paper_settings = isset($data['paper_settings']) ? $data['paper_settings'] : [];
        $blocks_data = isset($data['blocks']) ? $data : $data;

        $new_record = [
            'template_code'     => $new_template_code,
            'code'              => $group_code,
            'template_name'     => $current['template_name'],
            'template_category' => $current['template_category'],
            'paper_size'        => $paper_settings['paper_size'] ?? $current['paper_size'],
            'page_orientation'  => $paper_settings['page_orientation'] ?? ($current['page_orientation'] ?? 'portrait'),
            'margin_top'        => $paper_settings['margin_top'] ?? ($current['margin_top'] ?? 10),
            'margin_right'      => $paper_settings['margin_right'] ?? ($current['margin_right'] ?? 10),
            'margin_bottom'     => $paper_settings['margin_bottom'] ?? ($current['margin_bottom'] ?? 10),
            'margin_left'       => $paper_settings['margin_left'] ?? ($current['margin_left'] ?? 10),
            'id_branch'         => $current['id_branch'] ?? null,
            'is_default'        => 0,
            'is_active'         => 1,
            'version_number'    => (isset($current['version_number']) ? (int)$current['version_number'] : 1) + 1,
            'parent_template_id' => (int)$id,
            'gjs_data'          => json_encode($blocks_data),
        ];

        // Compile blocks into template_html
        if (isset($blocks_data['blocks'])) {
            $compiled_html = $this->_compile_blocks($blocks_data['blocks']);
            $compiled_css  = '';

            foreach ($blocks_data['blocks'] as $block) {
                if (!empty($block['css'])) {
                    $compiled_css .= $block['css'] . "\n";
                }
            }

            $new_record['template_html'] = $compiled_html;
            $new_record['template_css']  = $compiled_css;
        }

        // Deactivate previous versions
        $this->print_template_model->deactivate_by_group_code($group_code);

        // Insert new version
        $new_id = $this->print_template_model->insert($new_record);

        // Migrate computed variables from old template to new template
        if ($new_id && $new_id != $id) {
            $old_computed_vars = $this->print_template_model->get_computed_vars((int)$id);
            foreach ($old_computed_vars as $cv) {
                $cv_record = [
                    'id_template' => $new_id,
                    'var_name'    => $cv['var_name'],
                    'var_label'   => $cv['var_label'],
                    'formula'     => $cv['formula'],
                ];
                $this->print_template_model->save_computed_var($cv_record);
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'new_id' => $new_id, 'code' => $group_code]);
    }

    /**
     * AJAX: Live preview for V2 designer — compile blocks + render with sample data
     */
    public function preview_live_v2($id) {
        $template = $this->print_template_model->get_by_id($id);
        if (!$template) {
            echo '<p style="color:red;">Template not found</p>';
            return;
        }

        // Check if request has POST data (direct blocks from designer)
        $json = file_get_contents('php://input');
        $request_data = json_decode($json, true);

        if ($request_data && isset($request_data['blocks'])) {
            // Compile blocks from request
            $html = $this->_compile_blocks($request_data['blocks']);
            $css = '';
            foreach ($request_data['blocks'] as $block) {
                if (!empty($block['css'])) {
                    $css .= $block['css'] . "\n";
                }
            }
        } else {
            // Use stored template
            $html = $template['template_html'] ?? '';
            $css = $template['template_css'] ?? '';
        }

        // Get render mode from query string
        $mode = $this->input->get('mode') ?? 'preview';
        $sample_data = $this->print_template_model->get_sample_data($template['template_category']);

        // Get paper/margin settings
        $paper_size = $request_data['paper_settings']['paper_size'] ?? $template['paper_size'] ?? 'A4';
        $orientation = $request_data['paper_settings']['page_orientation'] ?? $template['page_orientation'] ?? 'portrait';
        $mt = $request_data['paper_settings']['margin_top'] ?? $template['margin_top'] ?? 10;
        $mr = $request_data['paper_settings']['margin_right'] ?? $template['margin_right'] ?? 10;
        $mb = $request_data['paper_settings']['margin_bottom'] ?? $template['margin_bottom'] ?? 10;
        $ml = $request_data['paper_settings']['margin_left'] ?? $template['margin_left'] ?? 10;

        if ($mode === 'chips') {
            // Preview mode with blue placeholder chips (field names visible)
            $rendered_html = $this->print_template_model->render_preview_template($html);
        } else {
            // Full preview with sample data
            $rendered_html = $this->print_template_model->render_string_template($html, $sample_data);
        }

        // Build page-size CSS
        $page_size = ($paper_size == 'A4' || $paper_size == 'A5' || $paper_size == 'Letter') 
            ? $paper_size : 'auto';
        if ($orientation == 'landscape') $page_size .= ' landscape';

        echo '<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Preview</title>
<style>
    @page { size: ' . $page_size . '; margin: ' . $mt . 'mm ' . $mr . 'mm ' . $mb . 'mm ' . $ml . 'mm; }
    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; width: 100%; font-family: Arial, sans-serif; font-size: 13px; }
    body { padding: ' . $mt . 'mm ' . $mr . 'mm ' . $mb . 'mm ' . $ml . 'mm; }
    table { border-collapse: collapse; width: 100%; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    tr { page-break-inside: avoid; }
    @media print { html, body { width: 100%; padding: 0; margin: 0; } }
    ' . $css . '
</style></head><body>' . $rendered_html . '</body></html>';
    }

    // ═══ COMPUTED VARIABLES ═══════════════════════════════════════════════

    /**
     * AJAX: Get computed variables for a template
     */
    public function get_computed_vars($template_id) {
        header('Content-Type: application/json');
        $vars = $this->print_template_model->get_computed_vars($template_id);
        echo json_encode(['success' => true, 'vars' => $vars]);
    }

    /**
     * AJAX: Save a computed variable
     */
    public function save_computed_var($template_id) {
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || empty($data['var_name']) || empty($data['formula'])) {
            echo json_encode(['success' => false, 'message' => 'Variable name and formula are required']);
            return;
        }

        // Sanitize var_name: lowercase, underscores only
        $var_name = preg_replace('/[^a-z0-9_]/', '_', strtolower(trim($data['var_name'])));
        if (empty($var_name)) {
            echo json_encode(['success' => false, 'message' => 'Invalid variable name']);
            return;
        }

        $record = [
            'id_template' => (int)$template_id,
            'var_name'    => $var_name,
            'var_label'   => trim($data['var_label'] ?? $var_name),
            'formula'     => trim($data['formula']),
        ];

        $id = $this->print_template_model->save_computed_var($record);
        echo json_encode(['success' => true, 'id' => $id, 'var_name' => $var_name]);
    }

    /**
     * AJAX: Delete a computed variable
     */
    public function delete_computed_var($template_id, $var_id) {
        header('Content-Type: application/json');
        $this->print_template_model->delete_computed_var($var_id, $template_id);
        echo json_encode(['success' => true]);
    }
}
