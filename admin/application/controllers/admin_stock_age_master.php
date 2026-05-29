<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_stock_age_master extends CI_Controller
{
    const VIEW_FOLDER = 'master/stock_age_master/';
    const MODEL = 'ret_stock_age_master_model';
    const SET_MODEL = 'admin_settings_model';

    function __construct()
    {
        parent::__construct();
        ini_set('date.timezone', 'Asia/Calcutta');
        $this->load->model(self::MODEL);
        $this->load->model(self::SET_MODEL);

        if (!$this->session->userdata('is_logged')) {
            redirect('admin/login');
        }
    }

    /**
     * Display list of stock age masters
     */
    public function index()
    {
        $data['access'] = $this->session->userdata('access_level');
        $data['main_content'] = self::VIEW_FOLDER . 'list';
        $this->load->view('layout/template', $data);
    }

    /**
     * Get list data via AJAX (for DataTable)
     */
    public function ajax_get_list()
    {
        $model = self::MODEL;
        
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'] ?? '';
        
        $order_column = $this->input->post('order')[0]['column'] ?? 0;
        $order_dir = $this->input->post('order')[0]['dir'] ?? 'DESC';
        $columns = ['id', 'age_from', 'age_to', 'value', 'status'];
        $order_by = $columns[$order_column] ?? 'id';

        $total = $this->$model->get_total_count('');
        $items = $this->$model->get_paginated_list($search, $order_by, $order_dir, $length, $start);
        $filtered = $this->$model->get_total_count($search);

        $data = [];
        foreach ($items as $item) {
            $actions = '<a href="' . base_url('index.php/admin_stock_age_master/form/' . $item['id']) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a> ';
            $actions .= '<button class="btn btn-xs btn-danger delete-record" data-id="' . $item['id'] . '"><i class="fa fa-trash"></i> Delete</button>';
            
            $data[] = [
                $item['id'],
                $item['age_from'],
                $item['age_to'],
                $item['value'],
                ($item['status'] == 1)
                    ? '<button class="btn btn-xs btn-success toggle-status" data-id="' . $item['id'] . '"><i class="fa fa-check-circle"></i> Active</button>'
                    : '<button class="btn btn-xs btn-danger toggle-status" data-id="' . $item['id'] . '"><i class="fa fa-times-circle"></i> Inactive</button>',
                $actions
            ];
        }

        echo json_encode([
            'draw'            => intval($draw),
            'recordsTotal'    => intval($total),
            'recordsFiltered' => intval($filtered),
            'data'            => $data
        ]);
    }

    /**
     * Display form for add/edit
     */
    public function form($id = '')
    {
        $model = self::MODEL;
        
        if (!empty($id)) {
            $data['record'] = $this->$model->get_by_id($id);
            if (empty($data['record'])) {
                $this->session->set_flashdata('error', 'Record not found');
                redirect('admin_stock_age_master');
            }
            $data['process_type'] = 'Edit';
        } else {
            $data['record'] = [
                'id'       => '',
                'age_from' => '',
                'age_to'   => '',
                'value'    => '',
                'status'   => 1
            ];
            $data['process_type'] = 'Add';
        }

        $data['main_content'] = self::VIEW_FOLDER . 'form';
        $this->load->view('layout/template', $data);
    }

    /**
     * Save form data
     */
    public function save()
    {
        $this->form_validation->set_rules('age_from', 'Age From', 'required|numeric|min_length[1]');
        $this->form_validation->set_rules('age_to', 'Age To', 'required|numeric|min_length[1]');
        $this->form_validation->set_rules('value', 'Value', 'required|min_length[1]|max_length[255]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin_stock_age_master');
            return;
        }

        $model = self::MODEL;
        $age_from = $this->input->post('age_from');
        $age_to = $this->input->post('age_to');
        $id = $this->input->post('id');

        // Validate age range
        if ($age_to <= $age_from) {
            $this->session->set_flashdata('error', 'Age To must be greater than Age From');
            redirect('admin_stock_age_master/form/' . $id);
            return;
        }

        $data = [
            'age_from'   => $age_from,
            'age_to'     => $age_to,
            'value'      => $this->input->post('value'),
            'status'     => $this->input->post('status', true),
            'updated_by' => $this->session->userdata('id'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        if (empty($id)) {
            // Check for duplicate display value
            if ($this->$model->check_value_exists($data['value'])) {
                $this->session->set_flashdata('error', 'This display value already exists. Please use a unique value.');
                redirect('admin_stock_age_master/form');
                return;
            }
            // Check for overlapping age range
            if ($this->$model->check_age_range_exists($age_from, $age_to)) {
                $this->session->set_flashdata('error', 'This age range overlaps with an existing range. Please use a non-overlapping range.');
                redirect('admin_stock_age_master/form');
                return;
            }
            $data['created_by'] = $this->session->userdata('id');
            $data['created_date'] = date('Y-m-d H:i:s');
            
            if ($this->$model->insert_age_master($data)) {
                $this->session->set_flashdata('success', 'Stock Age Master created successfully');
            } else {
                $this->session->set_flashdata('error', 'Failed to create record');
            }
        } else {
            // Check for duplicate display value (excluding self)
            if ($this->$model->check_value_exists($data['value'], $id)) {
                $this->session->set_flashdata('error', 'This display value already exists. Please use a unique value.');
                redirect('admin_stock_age_master/form/' . $id);
                return;
            }
            if ($this->$model->check_age_range_exists($age_from, $age_to, $id)) {
                $this->session->set_flashdata('error', 'This age range overlaps with an existing range. Please use a non-overlapping range.');
                redirect('admin_stock_age_master/form/' . $id);
                return;
            }
            
            if ($this->$model->update_age_master($id, $data) || true) {
                $this->session->set_flashdata('success', 'Stock Age Master updated successfully');
            } else {
                $this->session->set_flashdata('error', 'Failed to update record');
            }
        }

        redirect('admin_stock_age_master');
    }

    /**
     * Delete record
     */
    public function delete()
    {
        $id = $this->input->post('id');
        $model = self::MODEL;

        $result = $this->$model->delete_age_master($id);
        if ($result === -1) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete: This age range is currently used by active incentive configuration(s). Please deactivate or remove the incentive rules first.']);
        } elseif ($result > 0) {
            echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete record']);
        }
    }

    /**
     * Toggle status (Active <-> Inactive) via AJAX
     */
    public function toggle_status()
    {
        $id = $this->input->post('id');
        $model = self::MODEL;

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            return;
        }

        $record = $this->$model->get_by_id($id);
        if (empty($record)) {
            echo json_encode(['success' => false, 'message' => 'Record not found']);
            return;
        }

        $new_status = ($record['status'] == 1) ? 0 : 1;
        $update_data = [
            'status'       => $new_status,
            'updated_by'   => $this->session->userdata('id'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        if ($this->$model->update_age_master($id, $update_data) !== false) {
            $status_label = ($new_status == 1) ? 'Active' : 'Inactive';
            echo json_encode([
                'success'    => true,
                'message'    => 'Status updated to ' . $status_label,
                'new_status' => $new_status
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Get dropdown data (for use in other forms)
     */
    public function get_dropdown_data()
    {
        $model = self::MODEL;
        echo json_encode($this->$model->get_dropdown_data());
    }
}
