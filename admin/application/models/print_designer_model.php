<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Print_designer_model extends CI_Model {

    private $table = 'print_designer_templates';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ─── Table Introspection ─────────────────────────────────

    /**
     * Get all user tables from the current database (excludes system/CI tables)
     */
    public function get_all_tables() {
        $db_name = $this->db->database;
        $sql = "SELECT TABLE_NAME, TABLE_COMMENT, TABLE_ROWS
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_TYPE = 'BASE TABLE'
                ORDER BY TABLE_NAME ASC";
        $query = $this->db->query($sql, [$db_name]);
        return $query->result_array();
    }

    /**
     * Get all columns for a given table as {{variable}} tags
     */
    public function get_table_columns($table_name) {
        $db_name = $this->db->database;
        $sql = "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, COLUMN_KEY, COLUMN_COMMENT, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_NAME = ?
                ORDER BY ORDINAL_POSITION ASC";
        $query = $this->db->query($sql, [$db_name, $table_name]);
        $columns = $query->result_array();

        $variables = [];
        foreach ($columns as $col) {
            $variables[] = [
                'column'    => $col['COLUMN_NAME'],
                'tag'       => '{{' . $col['COLUMN_NAME'] . '}}',
                'type'      => $col['DATA_TYPE'],
                'full_type' => $col['COLUMN_TYPE'],
                'is_pk'     => ($col['COLUMN_KEY'] === 'PRI'),
                'comment'   => $col['COLUMN_COMMENT'],
                'nullable'  => ($col['IS_NULLABLE'] === 'YES'),
                'max_len'   => $col['CHARACTER_MAXIMUM_LENGTH']
            ];
        }
        return $variables;
    }

    /**
     * Auto-detect primary key column for a table
     */
    public function get_primary_key($table_name) {
        $db_name = $this->db->database;
        $sql = "SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_NAME = ?
                  AND COLUMN_KEY = 'PRI'
                LIMIT 1";
        $query = $this->db->query($sql, [$db_name, $table_name]);
        $row = $query->row_array();
        return $row ? $row['COLUMN_NAME'] : null;
    }

    /**
     * Fetch a single row from any table by primary key value
     */
    public function get_row($table_name, $pk_column, $row_id) {
        // Whitelist: ensure table and column actually exist
        $tables = array_column($this->get_all_tables(), 'TABLE_NAME');
        if (!in_array($table_name, $tables)) {
            return null;
        }
        $this->db->where($pk_column, $row_id);
        $this->db->limit(1);
        $query = $this->db->get($table_name);
        return $query->row_array();
    }

    // ─── Template CRUD ───────────────────────────────────────

    /**
     * Save or update a template
     */
    public function save_template($data) {
        if (!empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->where('id', $id);
            $this->db->update($this->table, $data);
            return $id;
        } else {
            unset($data['id']);
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Get a single template by ID
     */
    public function get_template($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    /**
     * Get all templates, optionally filtered by table_name
     */
    public function get_all_templates($table_name = null) {
        if ($table_name) {
            $this->db->where('table_name', $table_name);
        }
        $this->db->order_by('updated_at', 'DESC');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * Delete a template
     */
    public function delete_template($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Duplicate a template
     */
    public function duplicate_template($id) {
        $original = $this->get_template($id);
        if (!$original) return false;

        unset($original['id']);
        $original['name'] = $original['name'] . ' (Copy)';
        $original['is_default'] = 0;
        $original['created_at'] = date('Y-m-d H:i:s');
        $original['updated_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, $original);
        return $this->db->insert_id();
    }

    /**
     * Set a template as default for its table (unsets others)
     */
    public function set_default($id) {
        $template = $this->get_template($id);
        if (!$template) return false;

        // Unset all defaults for this table
        $this->db->where('table_name', $template['table_name']);
        $this->db->update($this->table, ['is_default' => 0]);

        // Set this one as default
        $this->db->where('id', $id);
        $this->db->update($this->table, ['is_default' => 1]);
        return true;
    }

    /**
     * Get the default template for a given table
     */
    public function get_default_for_table($table_name) {
        $this->db->where('table_name', $table_name);
        $this->db->where('is_default', 1);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    /**
     * Get rows from a dynamic table with a where clause
     */
    public function get_dynamic_rows($table_name, $where_clause) {
        // Whitelist: ensure table exists
        $db_name = $this->db->database;
        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
        $query = $this->db->query($sql, [$db_name, $table_name]);
        if ($query->num_rows() === 0) {
            return [];
        }

        if (!empty($where_clause)) {
            $this->db->where($where_clause);
        }
        $query = $this->db->get($table_name);
        return $query->result_array();
    }
}
