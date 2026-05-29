<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * IRN E-Invoice Settings Model
 * Handles global IRN settings (ret_settings), per-branch GSP credentials,
 * and IRN activity log queries
 */
class Irn_settings_model extends CI_Model
{
    /**
     * Get all IRN-related settings from ret_settings as key=>value map
     */
    function get_irn_settings()
    {
        $irn_keys = array(
            'is_auto_gen_irn', 'is_production', 'production_base_url',
            'gsp_auth_base_url', 'gsp_einvoice_base_url', 'irn_error_email',
            'usp_id', 'ci_password',
            'sandbox_auth_url', 'sandbox_einvoice_url'
        );
        $this->db->where_in('name', $irn_keys);
        $result = $this->db->get('ret_settings')->result_array();
        $settings = array();
        foreach ($result as $row) {
            $settings[$row['name']] = $row['value'];
        }
        return $settings;
    }

    /**
     * Update a single IRN setting by name
     */
    function update_irn_setting($name, $value)
    {
        $this->db->where('name', $name);
        return $this->db->update('ret_settings', array('value' => $value));
    }

    /**
     * Bulk update multiple IRN settings (only GSP config, not mode/environment)
     */
    function update_irn_settings($settings)
    {
        $allowed_keys = array(
            'gsp_auth_base_url', 'gsp_einvoice_base_url',
            'irn_error_email', 'usp_id', 'ci_password',
            'sandbox_auth_url', 'sandbox_einvoice_url'
        );
        $success = true;
        foreach ($settings as $key => $value) {
            if (in_array($key, $allowed_keys)) {
                if (!$this->update_irn_setting($key, $value)) {
                    $success = false;
                }
            }
        }
        return $success;
    }

    /**
     * Get all branches with GSP credential columns
     */
    function get_branches_gsp()
    {
        $sql = "SELECT b.id_branch, b.name, b.gst_number,
                       IFNULL(b.aspid,'') as aspid,
                       IFNULL(b.gsp_password,'') as gsp_password,
                       IFNULL(b.gsp_user_name,'') as gsp_user_name,
                       IFNULL(b.eInvPwd,'') as eInvPwd,
                       IFNULL(b.authtoken,'') as authtoken,
                       IFNULL(b.is_ho,'0') as is_ho
                FROM branch b
                ORDER BY b.is_ho DESC, b.id_branch ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Update GSP credentials for a specific branch
     */
    function update_branch_gsp($id_branch, $data)
    {
        $allowed_cols = array('aspid', 'gsp_password', 'gsp_user_name', 'eInvPwd');
        $safe_data = array();
        foreach ($allowed_cols as $col) {
            if (isset($data[$col])) {
                $safe_data[$col] = $data[$col];
            }
        }
        if (empty($safe_data)) return false;
        $this->db->where('id_branch', $id_branch);
        return $this->db->update('branch', $safe_data);
    }

    /**
     * Get recent IRN activity — bills where IRN generation was attempted
     * Returns both successful and failed IRN attempts for the activity log
     */
    function get_recent_irn_activity($limit = 30)
    {
        $sql = "SELECT 
                    rb.bill_id,
                    rb.bill_no,
                    rb.bill_date,
                    rb.bill_type,
                    IFNULL(rb.cusdel_irn, '') as irn_number,
                    IFNULL(rb.qrcodeimage, '') as has_qr,
                    IFNULL(c.firstname, IFNULL(b2.name, 'N/A')) as customer_name,
                    b.name as branch_name,
                    CASE 
                        WHEN rb.cusdel_irn IS NOT NULL AND rb.cusdel_irn != '' THEN 'success'
                        ELSE 'pending'
                    END as irn_status
                FROM ret_billing rb
                LEFT JOIN customer c ON c.id_customer = rb.bill_cus_id
                LEFT JOIN branch b ON b.id_branch = rb.id_branch
                LEFT JOIN branch b2 ON b2.id_branch = rb.to_branch
                WHERE rb.billing_for = 2 AND rb.bill_type IN (1, 2, 4, 9) AND rb.is_eda != 2
                ORDER BY rb.bill_date DESC, rb.bill_id DESC
                LIMIT ?";
        return $this->db->query($sql, array($limit))->result_array();
    }
}
