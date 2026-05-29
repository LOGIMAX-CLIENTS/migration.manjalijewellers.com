<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class admin_invoice_builder extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        // Load database libraries if not autoloaded
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('print_template_model');
        
        // Basic auth check placeholder - assuming session user check exists
        // if(!$this->session->userdata('id_employee')){ redirect('admin/login'); }
    }

    // List all templates
    public function index() {
        $data['title'] = 'Invoice Templates (Builder V2)';
        $data['templates'] = $this->print_template_model->get_all();
        $data['categories'] = $this->print_template_model->get_template_categories();
        
        // Assuming a standard layout structure
        $this->load->view('layout/header', $data);
        // We'll create a list view that doesn't depend on too many external partials for safety
        $this->load->view('invoice_builder/list', $data);
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
        $this->load->view('print_templates/form', $data); // Reuse form
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
        redirect('invoice-builder/designer/' . $id);
    }

    // Designer Interface
    public function designer($id) {
        $data['template'] = $this->print_template_model->get_by_id($id);
        if (!$data['template']) {
            redirect('invoice-builder');
        }
        $this->load->view('invoice_builder/designer', $data);
    }

    // AJAX: Save design from GrapesJS
    public function save_design($id) {
        // GrapesJS sends JSON in body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if ($data) {
            $update = [
                'gjs_data' => json_encode($data),
                'template_html' => isset($data['gjs-html']) ? $data['gjs-html'] : '',
                'template_css' => isset($data['gjs-css']) ? $data['gjs-css'] : '',
                // 'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->print_template_model->update($id, $update);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No data']);
        }
    }

    // AJAX: Load design for GrapesJS
    public function load_design($id) {
        $template = $this->print_template_model->get_by_id($id);
        // Check if gjs_data is valid JSON
        $gjs_data = json_decode($template['gjs_data'], true);
        
        // If GJS data is empty but we have raw HTML, wrap it in GrapesJS project data structure
        if (empty($gjs_data) && !empty($template['template_html'])) {
            $gjs_data = [
                'pages' => [
                    [
                        'frames' => [
                            [
                                'component' => $template['template_html']
                            ]
                        ]
                    ]
                ]
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($gjs_data ?: []);
    }

    // AJAX: Get available fields/placeholders
    public function get_fields($category) {
        $fields = $this->print_template_model->get_placeholders($category);
        header('Content-Type: application/json');
        echo json_encode($fields);
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
        $this->load->view('print_templates/preview', $data); // Reuse preview for now
    }

    // AJAX: Upload Image for Asset Manager
    public function upload_image() {
        $config['upload_path'] = './assets/uploads/print_designer/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|webp|svg';
        $config['max_size'] = 5120; // 5MB limit
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        $this->load->library('upload', $config);
        $uploaded_files = [];

        // GrapesJS sends files in an array named 'files'
        if (isset($_FILES['files']['name']) && is_array($_FILES['files']['name'])) {
            $count = count($_FILES['files']['name']);
            for ($i = 0; $i < $count; $i++) {
                $_FILES['file']['name']     = $_FILES['files']['name'][$i];
                $_FILES['file']['type']     = $_FILES['files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                $_FILES['file']['error']    = $_FILES['files']['error'][$i];
                $_FILES['file']['size']     = $_FILES['files']['size'][$i];

                if ($this->upload->do_upload('file')) {
                    $fileData = $this->upload->data();
                    $uploaded_files[] = base_url('assets/uploads/print_designer/' . $fileData['file_name']);
                }
            }
        } elseif (isset($_FILES['files']['name'])) {
             // Single file fallback
             if ($this->upload->do_upload('files')) {
                 $fileData = $this->upload->data();
                 $uploaded_files[] = base_url('assets/uploads/print_designer/' . $fileData['file_name']);
             }
        }

        // GrapesJS expects JSON response: { data: [ 'url1', 'url2' ] }
        header('Content-Type: application/json');
        echo json_encode(['data' => $uploaded_files]);
    }

    public function delete($id) {
        $this->print_template_model->delete($id);
        redirect('invoice-builder');
    }
    
    public function set_default($id) {
        $this->print_template_model->set_default($id);
        redirect('invoice-builder');
    }
}
