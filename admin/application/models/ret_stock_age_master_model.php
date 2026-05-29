<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ret_stock_age_master_model extends CI_Model
{
    const TABLE = 'ret_stock_age_master';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all stock age masters
     */
    public function get_all()
    {
        $query = $this->db->where('status', 1)->order_by('age_from', 'ASC')->get(self::TABLE);
        return $query->result_array();
    }

    /**
     * Get stock age master by ID
     */
    public function get_by_id($id)
    {
        $query = $this->db->where('id', $id)->get(self::TABLE);
        return $query->row_array();
    }

    /**
     * Get all stock age masters for dropdown
     */
    public function get_dropdown_data()
    {
        $query = $this->db->where('status', 1)
                         ->order_by('age_from', 'ASC')
                         ->get(self::TABLE);
        $result = array();
        foreach ($query->result_array() as $row) {
            $result[$row['id']] = $row['age_from'] . ' - ' . $row['age_to'] . ' days (' . $row['value'] . ')';
        }
        return $result;
    }

    /**
     * Insert new stock age master
     */
    public function insert_age_master($data)
    {
        $this->db->insert(self::TABLE, $data);
        return $this->db->insert_id();
    }

    /**
     * Update stock age master
     */
    public function update_age_master($id, $data)
    {
        $this->db->where('id', $id)->update(self::TABLE, $data);
        return $this->db->affected_rows();
    }

    /**
     * Check if an age range is referenced by any active incentive config
     * @param int $id  The stock age master ID
     * @return int  Number of active configs using this age range
     */
    public function is_age_range_in_use($id)
    {
        $this->db->where('id_stock_age_master', $id);
        $this->db->where('status', 1);
        return $this->db->count_all_results('ret_emp_incentive_config');
    }

    /**
     * Delete stock age master (only if not in use by incentive configs)
     * Returns: positive number = success, 0 = not found, -1 = in use
     */
    public function delete_age_master($id)
    {
        // Check if in use by incentive configs
        $usage_count = $this->is_age_range_in_use($id);
        if ($usage_count > 0) {
            return -1; // Signal: cannot delete, in use
        }
        $this->db->where('id', $id)->delete(self::TABLE);
        return $this->db->affected_rows();
    }

    /**
     * Check if age range overlaps with any existing range
     * Overlap rule: two ranges [A,B] and [C,D] overlap when A < D AND C < B
     */
    public function check_age_range_exists($age_from, $age_to, $exclude_id = null)
    {
        // Detect any overlap (inclusive boundaries): existing_from <= new_to AND new_from <= existing_to
        $this->db->where('age_from <=', $age_to);
        $this->db->where('age_to >=', $age_from);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get(self::TABLE);
        return $query->num_rows() > 0;
    }

    /**
     * Check if display value already exists
     */
    public function check_value_exists($value, $exclude_id = null)
    {
        $this->db->where('value', $value);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get(self::TABLE);
        return $query->num_rows() > 0;
    }

    /**
     * Get paginated list for DataTable
     */
    public function get_paginated_list($search = '', $order_by = 'id', $order_dir = 'DESC', $limit = 10, $offset = 0)
    {
        $this->db->select('*');
        
        if (!empty($search)) {
            $this->db->like('value', $search);
            $this->db->or_like('age_from', $search);
            $this->db->or_like('age_to', $search);
        }

        $this->db->order_by($order_by, $order_dir);
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get(self::TABLE);
        return $query->result_array();
    }

    /**
     * Get total count
     */
    public function get_total_count($search = '')
    {
        if (!empty($search)) {
            $this->db->like('value', $search);
            $this->db->or_like('age_from', $search);
            $this->db->or_like('age_to', $search);
        }
        return $this->db->count_all_results(self::TABLE);
    }
}
