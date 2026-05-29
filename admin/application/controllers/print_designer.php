<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Print Designer Controller
 * Fabric.js canvas-based dynamic print template designer
 */
class Print_designer extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('print_designer_model');

        // Auth check — same as other controllers
        if (!$this->session->userdata('is_logged')) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            redirect('admin/login');
        }
    }

    /**
     * Main editor page
     */
    public function index() {
        $data['title'] = 'Print Designer';
        $this->load->view('print_designer/index', $data);
    }

    /**
     * AJAX: Get all database tables
     */
    public function get_tables() {
        $tables = $this->print_designer_model->get_all_tables();
        header('Content-Type: application/json');
        echo json_encode($tables);
    }

    /**
     * AJAX: Get variables (columns) for a table.
     * Special source '__bill__' returns the predefined billing variable list
     * defined in application/config/bill.php instead of DB columns.
     */
    public function get_variables($table_name) {
        $table_name = urldecode($table_name);

        // ── Special: Billing (bill_format_2) variable set ──────────
        if ($table_name === '__bill__') {
            require_once APPPATH . 'config/bill.php';
            $variables = get_bill_variables();
            header('Content-Type: application/json');
            echo json_encode($variables);
            return;
        }

        // ── Special: Estimation variable set ──────────────────────
        if ($table_name === '__estimation__') {
            $this->load->model('print_template_model');
            $placeholders = $this->print_template_model->get_placeholders(16);
            $variables = [];
            foreach ($placeholders as $group => $fields) {
                foreach ($fields as $f) {
                    $variables[] = [
                        'name'  => $f['key'],
                        'label' => $f['label'],
                        'group' => $group,
                    ];
                }
            }
            header('Content-Type: application/json');
            echo json_encode($variables);
            return;
        }

        // ── Default: real database table columns ────────────────────
        $variables = $this->print_designer_model->get_table_columns($table_name);
        header('Content-Type: application/json');
        echo json_encode($variables);
    }

    /**
     * AJAX: Get actual row data for live preview.
     * When table_name == '__bill__', row_id is treated as a bill_id and all
     * billing variables are resolved via get_receipt_data() + map_billing_data_to_template().
     */
    public function get_row_data() {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        $table_name = isset($input['table_name']) ? $input['table_name'] : '';
        $row_id     = isset($input['row_id'])     ? $input['row_id']     : '';

        if (!$table_name || !$row_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            return;
        }

        // ── Special: Bill source ───────────────────────────────────
        if ($table_name === '__bill__') {
            header('Content-Type: application/json');
            $result = $this->_get_bill_row((int)$row_id);
            echo json_encode($result);
            return;
        }

        // ── Special: Estimation source ─────────────────────────────
        if ($table_name === '__estimation__') {
            header('Content-Type: application/json');
            $result = $this->_get_estimation_row((int)$row_id);
            echo json_encode($result);
            return;
        }

        // ── Default: database table ─────────────────────────────
        $pk = $this->print_designer_model->get_primary_key($table_name);
        if (!$pk) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No primary key found for table']);
            return;
        }

        $row = $this->print_designer_model->get_row($table_name, $pk, $row_id);
        header('Content-Type: application/json');
        if ($row) {
            echo json_encode(['success' => true, 'data' => $row, 'pk_column' => $pk]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Row not found']);
        }
    }

    /**
     * AJAX: Get full bill data by bill_id (called by JS when Bills source is selected).
     * Returns the same flat variable map used by billing_invoice() so every
     * {{variable}} in the designer template gets a real value.
     */
    public function get_bill_data() {
        $json  = file_get_contents('php://input');
        $input = json_decode($json, true);
        $bill_id = isset($input['bill_id']) ? (int)$input['bill_id'] : 0;

        header('Content-Type: application/json');
        if (!$bill_id) {
            echo json_encode(['success' => false, 'message' => 'bill_id is required']);
            return;
        }

        echo json_encode($this->_get_bill_row($bill_id));
    }

    /**
     * AJAX: Get full estimation data by estimation_id (called by JS when Estimation source is selected).
     * Returns the same flat variable map used by generate_invoice() so every
     * {{variable}} in the designer template gets a real value.
     */
    public function get_estimation_data() {
        $json  = file_get_contents('php://input');
        $input = json_decode($json, true);
        $est_id = isset($input['est_id']) ? (int)$input['est_id'] : 0;

        header('Content-Type: application/json');
        if (!$est_id) {
            echo json_encode(['success' => false, 'message' => 'est_id is required']);
            return;
        }

        echo json_encode($this->_get_estimation_row($est_id));
    }

    /**
     * AJAX: Save template (create or update)
     */
    public function save() {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        if (!$input || empty($input['name']) || empty($input['table_name'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Name and table are required']);
            return;
        }

        $data = [
            'name'          => $input['name'],
            'table_name'    => $input['table_name'],
            'layout_json'   => isset($input['layout_json']) ? $input['layout_json'] : '',
            'page_size'     => isset($input['page_size']) ? $input['page_size'] : 'A4',
            'orientation'   => isset($input['orientation']) ? $input['orientation'] : 'portrait',
            'page_width_mm' => isset($input['page_width_mm']) ? $input['page_width_mm'] : null,
            'page_height_mm'=> isset($input['page_height_mm']) ? $input['page_height_mm'] : null,
            'thumbnail'     => isset($input['thumbnail']) ? $input['thumbnail'] : null,
        ];

        if (!empty($input['id'])) {
            $data['id'] = $input['id'];
        }

        $id = $this->print_designer_model->save_template($data);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'id' => $id]);
    }

    /**
     * AJAX: Load a single template
     */
    public function load($id) {
        $template = $this->print_designer_model->get_template($id);
        header('Content-Type: application/json');
        if ($template) {
            echo json_encode(['success' => true, 'data' => $template]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Template not found']);
        }
    }

    /**
     * AJAX: List all templates
     */
    public function template_list() {
        $templates = $this->print_designer_model->get_all_templates();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $templates]);
    }

    /**
     * AJAX: Delete a template
     */
    public function delete($id) {
        $result = $this->print_designer_model->delete_template($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool) $result]);
    }

    /**
     * AJAX: Duplicate a template
     */
    public function duplicate($id) {
        $new_id = $this->print_designer_model->duplicate_template($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool) $new_id, 'id' => $new_id]);
    }

    /**
     * AJAX: Set template as default for its table
     */
    public function set_default($id) {
        $result = $this->print_designer_model->set_default($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool) $result]);
    }

    /**
     * Clean preview page for printing
     */
    public function preview($template_id, $row_id = 0) {
        $template = $this->print_designer_model->get_template($template_id);
        if (!$template) {
            show_404();
            return;
        }

        $row_data = null;
        if ($row_id > 0 && !empty($template['table_name'])) {
            $pk = $this->print_designer_model->get_primary_key($template['table_name']);
            if ($pk) {
                $row_data = $this->print_designer_model->get_row($template['table_name'], $pk, $row_id);
            }
        }

        $data['template'] = $template;
        $data['row_data'] = $row_data;
        $data['row_id'] = $row_id;
        $this->load->view('print_designer/preview', $data);
    }

    /**
     * Preview PDF: Handles both saved templates (GET) and unsaved changes (POST)
     */
    public function preview_pdf($template_id = null, $row_id = 0) {
        $layout_json = null;
        $table_name = null;
        $page_size = 'A4';
        $orientation = 'portrait';
        $custom_w = null;
        $custom_h = null;

        // 1. Check if it's a POST request (unsaved preview)
        if ($this->input->server('REQUEST_METHOD') === 'POST' && $this->input->post('layout_json')) {
            $layout_json = $this->input->post('layout_json');
            $table_name  = $this->input->post('table_name');
            $row_id      = $this->input->post('row_id') ?: 0;
            $page_size   = $this->input->post('page_size') ?: 'A4';
            $orientation = $this->input->post('orientation') ?: 'portrait';
            $custom_w    = $this->input->post('page_width_mm');
            $custom_h    = $this->input->post('page_height_mm');
        } 
        // 2. Otherwise load from database if template_id is provided
        elseif ($template_id) {
            $template = $this->print_designer_model->get_template($template_id);
            if (!$template) {
                show_error('Template not found');
                return;
            }
            $layout_json = $template['layout_json'];
            $table_name  = $template['table_name'];
            $page_size   = $template['page_size'];
            $orientation = $template['orientation'];
            $custom_w    = $template['page_width_mm'];
            $custom_h    = $template['page_height_mm'];
        }

        if (!$layout_json) {
            show_error('No layout data provided for preview');
            return;
        }

        // 3. Setup mPDF
        $paper_sizes = [
            'A4' => [210, 297], 'A3' => [297, 420], 'A5' => [148, 210],
            'Letter' => [216, 279], 'Legal' => [216, 356],
            '80mm' => [80, 297], '58mm' => [58, 210],
        ];

        if ($page_size === 'Custom' && $custom_w && $custom_h) {
            $w = (float) $custom_w;
            $h = (float) $custom_h;
        } else {
            $dims = isset($paper_sizes[$page_size]) ? $paper_sizes[$page_size] : $paper_sizes['A4'];
            $w = $dims[0];
            $h = $dims[1];
        }

        if ($orientation === 'landscape') {
            $tmp = $w; $w = $h; $h = $tmp;
        }

        $mpdf_path = APPPATH . '../vendor/autoload.php';
        if (!file_exists($mpdf_path)) {
            show_error('mPDF not installed');
            return;
        }
        require_once $mpdf_path;

        try {
            // Prevent caching of PDF output
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [$w, $h],
                'margin_left' => 0, 'margin_right' => 0,
                'margin_top' => 0, 'margin_bottom' => 0,
                'default_font' => 'Arial'
            ]);

            $canvas_data = json_decode($layout_json, true);
            file_put_contents(APPPATH . '../../admin/canvas_debug.json', json_encode($canvas_data, JSON_PRETTY_PRINT));

            // Fetch row data (Bill/Estimation source uses helpers, others use DB)
            $row = null;
            if ($table_name === '__bill__' && $row_id > 0) {
                $result = $this->_get_bill_row((int)$row_id);
                if ($result['success']) {
                    $row = $result['data'];
                }
            } elseif ($table_name === '__estimation__' && $row_id > 0) {
                $result = $this->_get_estimation_row((int)$row_id);
                if ($result['success']) {
                    $row = $result['data'];
                }
            } elseif ($table_name && $row_id > 0) {
                $pk = $this->print_designer_model->get_primary_key($table_name);
                if ($pk) {
                    $row = $this->print_designer_model->get_row($table_name, $pk, $row_id);
                }
            }

            // Render each element using WriteFixedPosHTML for precise positioning
            $this->_render_to_mpdf($mpdf, $canvas_data, $row, $w, $h);
            
            // Output to browser for display in tab
            $mpdf->Output('preview.pdf', \Mpdf\Output\Destination::INLINE);
        } catch (Exception $e) {
            show_error($e->getMessage());
        }
    }

    /**
     * Export PDF via mPDF (Download)
     */
    public function export_pdf() {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        if (!$input) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            return;
        }

        $table_name = $input['table_name'];
        $row_ids = $input['row_ids'];
        $layout_json = $input['layout_json'];
        $page_size = isset($input['page_size']) ? $input['page_size'] : 'A4';
        $orientation = isset($input['orientation']) ? $input['orientation'] : 'portrait';
        $custom_w = isset($input['page_width_mm']) ? $input['page_width_mm'] : null;
        $custom_h = isset($input['page_height_mm']) ? $input['page_height_mm'] : null;

        // Determine paper format for mPDF
        $paper_sizes = [
            'A4' => [210, 297], 'A3' => [297, 420], 'A5' => [148, 210],
            'Letter' => [216, 279], 'Legal' => [216, 356],
            '80mm' => [80, 297], '58mm' => [58, 210],
        ];

        if ($page_size === 'Custom' && $custom_w && $custom_h) {
            $w = (float) $custom_w;
            $h = (float) $custom_h;
        } else {
            $dims = isset($paper_sizes[$page_size]) ? $paper_sizes[$page_size] : $paper_sizes['A4'];
            $w = $dims[0];
            $h = $dims[1];
        }

        if ($orientation === 'landscape') {
            $tmp = $w; $w = $h; $h = $tmp;
        }

        // Require mPDF
        $mpdf_path = APPPATH . '../vendor/autoload.php';
        if (!file_exists($mpdf_path)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'mPDF not installed. Run: composer require mpdf/mpdf']);
            return;
        }
        require_once $mpdf_path;

        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [$w, $h],
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'default_font' => 'Arial'
            ]);

            $canvas_data = json_decode($layout_json, true);

            // For Bill/Estimation source, skip DB primary-key lookup
            $pk = ($table_name !== '__bill__' && $table_name !== '__estimation__')
                ? $this->print_designer_model->get_primary_key($table_name)
                : ($table_name === '__estimation__' ? 'estimation_id' : 'bill_id');

            for ($i = 0; $i < count($row_ids); $i++) {
                $row = null;
                if ($row_ids[$i]) {
                    if ($table_name === '__bill__') {
                        $result = $this->_get_bill_row((int)$row_ids[$i]);
                        if ($result['success']) $row = $result['data'];
                    } elseif ($table_name === '__estimation__') {
                        $result = $this->_get_estimation_row((int)$row_ids[$i]);
                        if ($result['success']) $row = $result['data'];
                    } elseif ($pk) {
                        $row = $this->print_designer_model->get_row($table_name, $pk, $row_ids[$i]);
                    }
                }

                if ($i > 0) {
                    $mpdf->AddPage();
                }

                $this->_render_to_mpdf($mpdf, $canvas_data, $row, $w, $h);
            }

            $mpdf->Output('print-designer-export.pdf', \Mpdf\Output\Destination::DOWNLOAD);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Private helper: load full bill data for a given bill_id.
     * Mirrors the logic in admin_ret_billing::billing_invoice() — calls
     * get_receipt_data() then map_billing_data_to_template() to produce a
     * flat key=>value array where every key is a {{variable}} name.
     *
     * @param  int   $bill_id
     * @return array ['success' => bool, 'data' => flat_array, 'pk_column' => 'bill_id']
     */
    private function _get_bill_row($bill_id) {
        $this->load->helper('receipt');
        $this->load->helper('template_receipt');

        $data = get_receipt_data($bill_id, '', false);

        if (empty($data['billing'])) {
            return ['success' => false, 'message' => 'Bill not found for ID: ' . $bill_id];
        }

        // map_billing_data_to_template() produces all the {{variable}} keys
        $mapped = tpl_map_billing_data_to_template($data);

        // Start with raw billing row so {{bill_no}}, {{bill_id}}, etc. also work
        $flat = $data['billing'];

        // Merge company details (lower priority — let mapped values win)
        if (!empty($data['comp_details']) && is_array($data['comp_details'])) {
            $flat = array_merge($flat, $data['comp_details']);
        }

        // Merge mapped variables last so they take precedence
        $flat = array_merge($flat, $mapped);

        // Scalar-only pass — remove any nested arrays (not useful in templates)
        foreach ($flat as $k => $v) {
            if (is_array($v)) unset($flat[$k]);
        }

        return ['success' => true, 'data' => $flat, 'pk_column' => 'bill_id'];
    }

    /**
     * Private helper: load full estimation data for a given estimation_id.
     * Mirrors _get_bill_row() but for estimation data.
     *
     * @param  int   $est_id
     * @return array ['success' => bool, 'data' => flat_array, 'pk_column' => 'estimation_id']
     */
    private function _get_estimation_row($est_id) {
        $this->load->helper('receipt');
        $this->load->helper('template_receipt');
        $this->load->model('ret_estimation_model');
        $this->load->model('ret_billing_model');

        $data = [];
        $data['estimation'] = $this->ret_estimation_model->get_entry_records($est_id);

        if (empty($data['estimation'])) {
            return ['success' => false, 'message' => 'Estimation not found for ID: ' . $est_id];
        }

        $data['est_other_item'] = $this->ret_estimation_model->getOtherEstimateItemsDetails($est_id);
        $data['metal_rates']    = $this->ret_estimation_model->get_branchwise_rate($data['estimation']['id_branch']);
        $data['comp_details']   = $this->ret_billing_model->getCompanyDetails($data['estimation']['id_branch']);
        $data['filename']       = '';

        // Map using the estimation mapper
        $mapped = tpl_map_estimation_data_to_template($data);

        // Build flat array: raw estimation + company + mapped (mapped wins)
        $flat = $data['estimation'];
        if (!empty($data['comp_details']) && is_array($data['comp_details'])) {
            $flat = array_merge($flat, $data['comp_details']);
        }
        $flat = array_merge($flat, $mapped);

        // Remove nested arrays (not useful for scalar preview)
        foreach ($flat as $k => $v) {
            if (is_array($v)) unset($flat[$k]);
        }

        return ['success' => true, 'data' => $flat, 'pk_column' => 'estimation_id'];
    }

    /**
     * Render Fabric.js canvas objects to mPDF using WriteFixedPosHTML
     * This uses mPDF's native fixed-position API instead of CSS position:absolute
     * which is not supported inside nested elements.
     *
     * @param object $mpdf       mPDF instance
     * @param array  $canvas_data  Decoded Fabric.js canvas JSON
     * @param array  $row          Database row for variable substitution
     * @param float  $page_w_mm   Page width in mm
     * @param float  $page_h_mm   Page height in mm
     */
    private function _render_to_mpdf($mpdf, $canvas_data, $row, $page_w_mm, $page_h_mm) {
        $objects = isset($canvas_data['objects']) ? $canvas_data['objects'] : [];
        $px_per_mm = 3.7795275591; // 96dpi

        // Sort objects by 'top' for sequential rendering and push-down logic
        usort($objects, function($a, $b) {
            $topA = isset($a['top']) ? (float)$a['top'] : 0;
            $topB = isset($b['top']) ? (float)$b['top'] : 0;
            return $topA <=> $topB;
        });

        $current_y_offset_mm = 0;

        foreach ($objects as $obj) {
            $type   = isset($obj['type']) ? $obj['type'] : '';
            $customType = isset($obj['customType']) ? $obj['customType'] : '';
            
            $left   = isset($obj['left']) ? (float)$obj['left'] : 0;
            $top    = isset($obj['top']) ? (float)$obj['top'] : 0;
            $scaleX = isset($obj['scaleX']) ? (float)$obj['scaleX'] : 1;
            $scaleY = isset($obj['scaleY']) ? (float)$obj['scaleY'] : 1;
            $originX = isset($obj['originX']) ? $obj['originX'] : 'left';
            $originY = isset($obj['originY']) ? $obj['originY'] : 'top';

            // Calculate base position in mm (design coordinates)
            // Fabric.js coordinates are from top-left by default, but originX/Y can change this
            $render_left_px = $left;
            $render_top_px  = $top;

            if ($customType === 'data-table') {
                $config = isset($obj['tableConfig']) ? $obj['tableConfig'] : null;
                if (!$config) continue;

                $rows = $this->_get_table_data($config, $row);
                
                // Calculate designed height on canvas (what it looked like in designer)
                $row_h_px = ($config['fontSize'] + ($config['cellPadding'] * 2) + 4);
                $design_rows = ($config['showHeader'] ? 1 : 0) + 3;
                $h_design_mm = ($design_rows * $row_h_px) / $px_per_mm;

                // Render the table and get its actual height
                $x_mm = $render_left_px / $px_per_mm;
                $y_mm = ($render_top_px / $px_per_mm) + $current_y_offset_mm;
                $h_actual_mm = $this->_render_dynamic_table($mpdf, $config, $rows, $x_mm, $y_mm, $row);

                // Update push-down offset for subsequent elements
                if ($h_actual_mm > $h_design_mm) {
                    $current_y_offset_mm += ($h_actual_mm - $h_design_mm);
                }
                continue;
            }

            if ($type === 'textbox' || $type === 'i-text' || $type === 'text') {
                $text = isset($obj['text']) ? $obj['text'] : '';
                if (trim($text) === '' || $text === 'Type here...') {
                    continue;
                }

                $obj_w_px = (isset($obj['width']) ? (float)$obj['width'] : 200) * $scaleX;
                $obj_h_px = (isset($obj['height']) ? (float)$obj['height'] : 30) * $scaleY;
                
                if ($originX === 'center') $render_left_px -= $obj_w_px / 2;
                if ($originY === 'center') $render_top_px  -= $obj_h_px / 2;

                $x_mm = $render_left_px / $px_per_mm;
                $y_mm = ($render_top_px / $px_per_mm) + $current_y_offset_mm;
                $w_mm = $obj_w_px / $px_per_mm;
                $h_mm = $obj_h_px / $px_per_mm;
                
                $fontSize   = isset($obj['fontSize']) ? (int)$obj['fontSize'] : 14;
                $fontFamily = isset($obj['fontFamily']) ? $obj['fontFamily'] : 'Arial';
                $fontWeight = isset($obj['fontWeight']) ? $obj['fontWeight'] : 'normal';
                $fontStyle  = isset($obj['fontStyle']) ? $obj['fontStyle'] : 'normal';
                $textAlign  = isset($obj['textAlign']) ? $obj['textAlign'] : 'left';
                $color      = isset($obj['fill']) ? $obj['fill'] : '#000';

                $html = '<div style="'
                    . 'font-size:' . $fontSize . 'px;'
                    . 'font-family:' . $fontFamily . ';'
                    . 'font-weight:' . $fontWeight . ';'
                    . 'font-style:' . $fontStyle . ';'
                    . 'text-align:' . $textAlign . ';'
                    . 'color:' . $color . ';'
                    . 'line-height:1.16;'
                    . 'overflow:visible;'
                    . '">' . nl2br(htmlspecialchars($this->_substitute_vars($text, $row))) . '</div>';

                $mpdf->WriteFixedPosHTML($html, $x_mm, $y_mm, $w_mm, $h_mm, 'visible');

            } elseif ($type === 'rect') {
                $obj_w_px = (isset($obj['width']) ? (float)$obj['width'] : 100) * $scaleX;
                $obj_h_px = (isset($obj['height']) ? (float)$obj['height'] : 100) * $scaleY;
                
                if ($originX === 'center') $render_left_px -= $obj_w_px / 2;
                if ($originY === 'center') $render_top_px  -= $obj_h_px / 2;

                $x_mm = $render_left_px / $px_per_mm;
                $y_mm = ($render_top_px / $px_per_mm) + $current_y_offset_mm;
                $w_mm = $obj_w_px / $px_per_mm;
                $h_mm = $obj_h_px / $px_per_mm;

                $fill    = isset($obj['fill']) ? $obj['fill'] : 'transparent';
                $stroke  = isset($obj['stroke']) ? $obj['stroke'] : '#000';
                $strokeW = isset($obj['strokeWidth']) ? (float)$obj['strokeWidth'] : 1;
                $rx      = isset($obj['rx']) ? (float)$obj['rx'] : 0;

                $style = 'width:100%; height:100%;'
                    . ' background:' . $fill . ';'
                    . ' border:' . $strokeW . 'px solid ' . $stroke . ';'
                    . ' box-sizing:border-box;';
                if ($rx) $style .= ' border-radius:' . $rx . 'px;';

                $html = '<div style="' . $style . '"></div>';
                $mpdf->WriteFixedPosHTML($html, $x_mm, $y_mm, $w_mm, $h_mm, 'hidden');

            } elseif ($type === 'line') {
                $obj_w_px = (isset($obj['width']) ? (float)$obj['width'] : 200) * $scaleX;
                $obj_h_px = (isset($obj['height']) ? (float)$obj['height'] : 0) * $scaleY;

                if ($originX === 'center') $render_left_px -= $obj_w_px / 2;
                if ($originY === 'center') $render_top_px  -= $obj_h_px / 2;

                $x_mm = $render_left_px / $px_per_mm;
                $y_mm = ($render_top_px / $px_per_mm) + $current_y_offset_mm;
                $w_mm = max($obj_w_px / $px_per_mm, 1);
                $h_mm = max($obj_h_px / $px_per_mm, 0.5);

                $stroke  = isset($obj['stroke']) ? $obj['stroke'] : '#000';
                $strokeW = isset($obj['strokeWidth']) ? (float)$obj['strokeWidth'] : 1;

                // Determine border-style from lineStyle or strokeDashArray
                $lineStyle = isset($obj['lineStyle']) ? $obj['lineStyle'] : 'solid';
                $dashArray = isset($obj['strokeDashArray']) ? $obj['strokeDashArray'] : null;
                if ($dashArray && is_array($dashArray))  {
                    // Map to CSS border-styles supported by mPDF
                    if (count($dashArray) >= 2) {
                        if ($dashArray[0] <= 3) {
                            $borderStyle = 'dotted';
                        } else {
                            $borderStyle = 'dashed';
                        }
                    } else {
                        $borderStyle = 'solid';
                    }
                } else {
                    $borderStyle = 'solid';
                }

                if ($obj_h_px <= $strokeW) {
                    $html = '<div style="border-top:' . $strokeW . 'px ' . $borderStyle . ' ' . $stroke . '; width:100%;"></div>';
                } else {
                    $html = '<div style="border-left:' . $strokeW . 'px ' . $borderStyle . ' ' . $stroke . '; height:100%;"></div>';
                }
                $mpdf->WriteFixedPosHTML($html, $x_mm, $y_mm, $w_mm, $h_mm, 'visible');
            }
        }
    }

    /**
     * Fetch data for a dynamic data table
     */
    private function _get_table_data($config, $mainRow) {
        $sourceTable = isset($config['sourceTable']) ? $config['sourceTable'] : '';
        $filter      = isset($config['sourceFilter']) ? $config['sourceFilter'] : '';

        if (!$sourceTable) return [];

        // Substitute variables in filter
        $actual_filter = $this->_substitute_vars($filter, $mainRow);
        
        // If the filter still contains unresolved variables (e.g. Preview ID was blank), 
        // do not run the query as it will cause a MySQL syntax error.
        if (strpos($actual_filter, '{{') !== false) {
            return [];
        }

        return $this->print_designer_model->get_dynamic_rows($sourceTable, $actual_filter);
    }

    /**
     * Render a dynamic table to HTML and Measure height
     * Returns actual rendered height in mm
     */
    private function _render_dynamic_table($mpdf, $config, $rows, $x_mm, $y_mm, $mainRow = null) {
        $cols = isset($config['columns']) ? $config['columns'] : [];
        if (empty($cols)) return 0;

        $mergedCells = isset($config['mergedCells']) ? $config['mergedCells'] : [];

        $total_w_px = 0;
        foreach ($cols as $c) $total_w_px += $c['width'];
        $total_w_mm = $total_w_px / 3.7795275591;

        $fontSize = !empty($config['fontSize']) ? $config['fontSize'] : 11;
        $borderWidth = isset($config['borderWidth']) ? $config['borderWidth'] : 1;
        $borderColor = !empty($config['borderColor']) ? $config['borderColor'] : '#000';
        $cellPadding = isset($config['cellPadding']) ? $config['cellPadding'] : 4;
        $headerBg = !empty($config['headerBg']) ? $config['headerBg'] : '#333';
        $headerColor = !empty($config['headerColor']) ? $config['headerColor'] : '#fff';

        $borderStr = $borderWidth . 'px solid ' . $borderColor;
        
        $html = '<table style="'
            . 'width: 100%;'
            . 'border-collapse: collapse;'
            . 'font-size: ' . $fontSize . 'px;'
            . 'border: ' . $borderStr . ';'
            . '">';

        // Helper to check for a merged cell starting at (r, c)
        $findMerge = function($r, $c) use ($mergedCells) {
            foreach ($mergedCells as $m) {
                if ($m['row'] == $r && $m['col'] == $c) return $m;
            }
            return null;
        };

        // Helper to check if a cell (r, c) is covered by a merge
        $isCovered = function($r, $c) use ($mergedCells) {
            foreach ($mergedCells as $m) {
                // If it's a merge, it covers from r to r+rs-1 and c to c+cs-1
                if ($r >= $m['row'] && $r < ($m['row'] + $m['rowspan']) &&
                    $c >= $m['col'] && $c < ($m['col'] + $m['colspan'])) {
                    // But the origin cell itself is NOT "covered" (it's the one that renders)
                    if ($r == $m['row'] && $c == $m['col']) return false;
                    return true;
                }
            }
            return false;
        };

        // 1. Header Rows
        $maxHeaderRow = 0; // initialize before the showHeader check
        if (!empty($config['showHeader'])) {
            // Check if there are any merges on row 0 or beyond (multi-line header)
            foreach ($mergedCells as $m) {
                if ($m['row'] + $m['rowspan'] > $maxHeaderRow + 1) {
                    $maxHeaderRow = $m['row'] + $m['rowspan'] - 1;
                }
            }
            
            // Render header rows (from 0 to $maxHeaderRow)
            for ($r = 0; $r <= $maxHeaderRow; $r++) {
                $html .= '<tr style="background:' . $headerBg . '; color:' . $headerColor . ';">';
                for ($c = 0; $c < count($cols); $c++) {
                    if ($isCovered($r, $c)) continue;
                    
                    $m = $findMerge($r, $c);
                    $cs = $m ? $m['colspan'] : 1;
                    $rs = $m ? $m['rowspan'] : 1;
                    
                    // Header text: if it's row 0, use config header, otherwise blank if merge
                    $headerText = ($r == 0) ? htmlspecialchars($cols[$c]['header']) : '';
                    $align = !empty($cols[$c]['align']) ? $cols[$c]['align'] : 'left';

                    $html .= '<th ' . ($cs > 1 ? 'colspan="'.$cs.'"' : '') . ' ' . ($rs > 1 ? 'rowspan="'.$rs.'"' : '') . ' style="'
                        . 'padding: ' . $cellPadding . 'px;'
                        . 'border: ' . $borderStr . ';'
                        . 'text-align: ' . $align . ';'
                        . (isset($cols[$c]['width']) ? 'width: ' . $cols[$c]['width'] . 'px;' : '')
                        . '">' . $headerText . '</th>';
                }
                $html .= '</tr>';
            }
        }

        // 2. Data Rows
        if (empty($rows)) {
            $html .= '<tr><td colspan="' . count($cols) . '" style="padding:10px; text-align:center; color:#999;">No records found</td></tr>';
        } else {
            foreach ($rows as $row) {
                $html .= '<tr>';
                foreach ($cols as $col) {
                    $text = $this->_substitute_vars($col['field'], $row);
                    $align = !empty($col['align']) ? $col['align'] : 'left';
                    $html .= '<td style="'
                        . 'padding: ' . $cellPadding . 'px;'
                        . 'border: ' . $borderStr . ';'
                        . 'text-align: ' . $align . ';'
                        . '">' . nl2br(htmlspecialchars($text)) . '</td>';
                }
                $html .= '</tr>';
            }
        }

        // 3. Footer Row (tfoot)
        $showFooter = !empty($config['showFooter']);
        $footerRow  = !empty($config['footerRow']) ? $config['footerRow'] : [];
        if ($showFooter && !empty($footerRow)) {
            $footerBg    = !empty($config['footerBg'])    ? $config['footerBg']    : '#f0f0f0';
            $footerColor = !empty($config['footerColor']) ? $config['footerColor'] : '#000000';
            $html .= '<tfoot><tr style="background:' . $footerBg . '; color:' . $footerColor . '; font-weight:bold;">';
            foreach ($cols as $ci => $col) {
                $footCell = isset($footerRow[$ci]) ? $footerRow[$ci] : [];
                $cellText = isset($footCell['text']) ? $this->_substitute_vars($footCell['text'], $mainRow ?? []) : '';
                $align    = !empty($footCell['align']) ? $footCell['align'] : (!empty($col['align']) ? $col['align'] : 'left');
                $html .= '<td style="'
                    . 'padding: ' . $cellPadding . 'px;'
                    . 'border: ' . $borderStr . ';'
                    . 'text-align: ' . $align . ';'
                    . '">' . nl2br(htmlspecialchars($cellText)) . '</td>';
            }
            $html .= '</tr></tfoot>';
        }

        $html .= '</table>';

        // Estimate height to prevent mPDF from triggering a massive page break
        // by passing an excessively large height (like 2000).
        $row_h_estimate = ($fontSize + ($cellPadding * 2) + 4) / 3.7795275591;
        
        $headerLineCount = !empty($config['showHeader']) ? ($maxHeaderRow + 1) : 0;
        $footerLineCount = (!empty($config['showFooter']) && !empty($config['footerRow'])) ? 1 : 0;
        $row_count = empty($rows) ? 1 : count($rows);
        $total_rows = $headerLineCount + $row_count + $footerLineCount;
        $estimated_h_mm = $total_rows * $row_h_estimate;

        // Write to PDF using fixed position, giving it exactly the estimated height (plus a tiny buffer)
        $mpdf->WriteFixedPosHTML($html, $x_mm, $y_mm, $total_w_mm, $estimated_h_mm + 5, 'visible');

        return $estimated_h_mm;
    }

    /**
     * Replace {{column_name}} with actual values from row data
     */
    private function _substitute_vars($text, $row) {
        if (!$row) return $text;

        return preg_replace_callback('/\{\{(\w+)\}\}/', function($matches) use ($row) {
            $col = $matches[1];
            if (isset($row[$col])) {
                return $row[$col];
            }
            return $matches[0]; // Keep original if not found
        }, $text);
    }
}
