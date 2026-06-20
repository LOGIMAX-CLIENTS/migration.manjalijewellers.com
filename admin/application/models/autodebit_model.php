<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Autodebit Model
 * Handles all database operations for Cashfree Subscription-based auto-debit.
 * Table: auto_debit_subscription (new), scheme_account.auto_debit_status (new column)
 */
class Autodebit_model extends CI_Model
{
    const SUB_TABLE = 'auto_debit_subscription';
    const PAY_TABLE = 'payment';
    const ACC_TABLE = 'scheme_account';
    const SCH_TABLE = 'scheme';
    const CUS_TABLE = 'customer';
    const PMD_TABLE = 'payment_mode_details';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Phase 1: Get all data needed to create a Cashfree subscription.
     * Returns scheme, customer, gateway, and existing subscription info in one query.
     */
    function getSubscriptionData($id_scheme_account)
    {
        $sql = "SELECT
                    sa.id_scheme_account,
                    sa.id_customer,
                    sa.id_scheme,
                    sa.id_branch,
                    sa.start_date,
                    sa.scheme_acc_number,
                    sa.group_code,
                    sa.active,
                    sa.is_closed,
                    sa.auto_debit_status,
                    sa.account_name,
                    s.scheme_name,
                    s.code,
                    s.amount AS scheme_amount,
                    s.scheme_type,
                    s.total_installments,
                    s.auto_debit_plan_type,
                    s.sync_scheme_code,
                    s.min_amount,
                    c.firstname,
                    c.lastname,
                    c.mobile,
                    c.email,
                    CONCAT(IFNULL(c.firstname,''), ' ', IFNULL(c.lastname,'')) AS customer_name
                FROM " . self::ACC_TABLE . " sa
                LEFT JOIN " . self::SCH_TABLE . " s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN " . self::CUS_TABLE . " c ON (c.id_customer = sa.id_customer)
                WHERE sa.id_scheme_account = ?";

        return $this->db->query($sql, array($id_scheme_account))->row_array();
    }

    /**
     * Get gateway credentials for Cashfree (pg_code = 4).
     * Reuses existing pattern from payment_model.
     */
    function getGatewayData($id_branch = '')
    {
        $pg_code = 4; // Cashfree pg_code
        $sql = "SELECT id_pg, param_1, param_2, param_3, param_4, pg_code, api_url
                FROM gateway
                WHERE is_default = 1 AND active = 1 AND pg_code = " . intval($pg_code)
                . ($id_branch != '' ? " AND id_branch = " . intval($id_branch) : '');

        return $this->db->query($sql)->row_array();
    }

    /**
     * Insert a new subscription record.
     * Returns insert_id on success.
     */
    function insertSubscription($data)
    {
        $this->db->insert(self::SUB_TABLE, $data);
        return $this->db->insert_id();
    }

    /**
     * Update subscription by id_subscription (primary key).
     */
    function updateSubscriptionById($id_subscription, $data)
    {
        $this->db->where('id_subscription', $id_subscription);
        return $this->db->update(self::SUB_TABLE, $data);
    }

    /**
     * Update subscription by subscription_id (our generated unique ID).
     */
    function updateSubscriptionBySubId($subscription_id, $data)
    {
        $this->db->where('subscription_id', $subscription_id);
        return $this->db->update(self::SUB_TABLE, $data);
    }

    /**
     * Update subscription by Cashfree's sub_reference_id.
     */
    function updateSubscriptionBySubRefId($sub_reference_id, $data)
    {
        $this->db->where('sub_reference_id', $sub_reference_id);
        return $this->db->update(self::SUB_TABLE, $data);
    }

    /**
     * Update scheme_account.auto_debit_status.
     * Status values: 0=None, 1=Pending, 2=Active, 3=Paused, 4=Cancelled, 5=Completed
     */
    function updateSchemeAccountAutoDebitStatus($id_scheme_account, $status)
    {
        $this->db->where('id_scheme_account', $id_scheme_account);
        return $this->db->update(self::ACC_TABLE, array(
            'auto_debit_status' => intval($status)
        ));
    }

    /**
     * Lookup subscription by Cashfree's sub_reference_id (used by webhooks).
     */
    function getSubscriptionBySubRefId($sub_reference_id)
    {
        $sql = "SELECT sub.*, sa.id_branch, sa.id_customer, sa.id_scheme, sa.scheme_acc_number,
                       s.scheme_name, s.code, s.amount AS scheme_amount, s.scheme_type,
                       s.total_installments, s.auto_debit_plan_type, s.min_amount,
                       c.firstname, c.lastname, c.mobile, c.email,
                       CONCAT(IFNULL(c.firstname,''), ' ', IFNULL(c.lastname,'')) AS customer_name
                FROM " . self::SUB_TABLE . " sub
                LEFT JOIN " . self::ACC_TABLE . " sa ON (sa.id_scheme_account = sub.id_scheme_account)
                LEFT JOIN " . self::SCH_TABLE . " s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN " . self::CUS_TABLE . " c ON (c.id_customer = sa.id_customer)
                WHERE sub.sub_reference_id = ?";

        return $this->db->query($sql, array($sub_reference_id))->row_array();
    }

    /**
     * Lookup subscription by our generated subscription_id (used by callback).
     */
    function getSubscriptionBySubscriptionId($subscription_id)
    {
        $sql = "SELECT sub.*, sa.id_branch, sa.id_customer, sa.id_scheme, sa.scheme_acc_number,
                       s.scheme_name, s.code, s.amount AS scheme_amount, s.scheme_type,
                       s.total_installments, s.auto_debit_plan_type, s.min_amount,
                       c.firstname, c.lastname, c.mobile, c.email,
                       CONCAT(IFNULL(c.firstname,''), ' ', IFNULL(c.lastname,'')) AS customer_name
                FROM " . self::SUB_TABLE . " sub
                LEFT JOIN " . self::ACC_TABLE . " sa ON (sa.id_scheme_account = sub.id_scheme_account)
                LEFT JOIN " . self::SCH_TABLE . " s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN " . self::CUS_TABLE . " c ON (c.id_customer = sa.id_customer)
                WHERE sub.subscription_id = ?";

        return $this->db->query($sql, array($subscription_id))->row_array();
    }

    /**
     * Get active subscription for a scheme account.
     * Only returns if status is INITIALIZED, ACTIVE, or BANK_APPROVAL_PENDING.
     */
    function getActiveSubscription($id_scheme_account)
    {
        $this->db->where('id_scheme_account', $id_scheme_account);
        $this->db->where_in('status', array('INITIALIZED', 'ACTIVE', 'BANK_APPROVAL_PENDING'));
        return $this->db->get(self::SUB_TABLE)->row_array();
    }

    /**
     * Duplicate payment check before inserting auto-debit payment.
     * Checks by payment_ref_number to prevent double-processing of webhooks.
     */
    function isPaymentExists($payment_ref_number)
    {
        $this->db->where('payment_ref_number', $payment_ref_number);
        $count = $this->db->count_all_results(self::PAY_TABLE);
        return ($count > 0);
    }

    /**
     * Get current metal rate for the branch.
     * Returns the latest 22ct gold rate.
     */
    function getCurrentMetalRate($id_branch = '')
    {
        $sql = "SELECT mr.rate
                FROM metal_rates mr
                LEFT JOIN metal m ON (m.id_metal = mr.id_metal)
                WHERE m.purity_code = '22' AND mr.active = 1"
                . ($id_branch != '' ? " AND mr.id_branch = " . intval($id_branch) : '')
                . " ORDER BY mr.id_metal_rate DESC LIMIT 1";

        $result = $this->db->query($sql)->row_array();
        return isset($result['rate']) ? $result['rate'] : 0;
    }

    /**
     * Insert payment record for auto-debit.
     * Returns insert_id.
     */
    function insertPayment($data)
    {
        $this->db->insert(self::PAY_TABLE, $data);
        return $this->db->insert_id();
    }

    /**
     * Insert payment mode detail for auto-debit payment.
     */
    function insertPaymentModeDetail($data)
    {
        $this->db->insert(self::PMD_TABLE, $data);
        return $this->db->insert_id();
    }

    /**
     * Get last paid due_month/due_year for calculating next due.
     */
    function getLastDueMonthYear($id_scheme_account)
    {
        $sql = "SELECT due_month, due_year, installment
                FROM " . self::PAY_TABLE . "
                WHERE id_scheme_account = ?
                AND payment_status = 1
                ORDER BY due_year DESC, due_month DESC, id_payment DESC
                LIMIT 1";

        return $this->db->query($sql, array($id_scheme_account))->row_array();
    }

    /**
     * Check if payment already exists for current month.
     */
    function getCurrentMonthPayment($id_scheme_account, $year, $month)
    {
        $sql = "SELECT id_payment
                FROM " . self::PAY_TABLE . "
                WHERE id_scheme_account = ?
                AND due_year = ?
                AND due_month = ?
                AND payment_status IN (1, 2)
                LIMIT 1";

        return $this->db->query($sql, array($id_scheme_account, $year, $month))->row_array();
    }

    /**
     * Get auto-debit status + auth_link for UI rendering on scheme account detail.
     */
    function getSchemeAccountAutoDebitInfo($id_scheme_account)
    {
        $sql = "SELECT sub.id_subscription, sub.subscription_id, sub.cf_subscription_id,
                       sub.sub_reference_id, sub.status, sub.auth_link,
                       sub.plan_amount, sub.plan_type,
                       DATE_FORMAT(sub.first_charge_date, '%d-%m-%Y') AS first_charge_date,
                       DATE_FORMAT(sub.created_at, '%d-%m-%Y %h:%i %p') AS created_date,
                       DATE_FORMAT(sub.updated_at, '%d-%m-%Y %h:%i %p') AS updated_date,
                       sub.failure_reason
                FROM " . self::SUB_TABLE . " sub
                WHERE sub.id_scheme_account = ?
                ORDER BY sub.id_subscription DESC
                LIMIT 1";

        return $this->db->query($sql, array($id_scheme_account))->row_array();
    }

    /**
     * Report: Get autodebit subscription list with filters.
     * Called by admin_reports controller stub.
     */
    function ajax_get_autodebit_subscription($from_date, $to_date, $id_customer = '', $mobile = '')
    {
        $sql = "SELECT sub.id_subscription, sub.subscription_id, sub.cf_subscription_id,
                       sub.sub_reference_id, sub.status, sub.plan_amount, sub.plan_type,
                       DATE_FORMAT(sub.first_charge_date, '%d-%m-%Y') AS first_charge_date,
                       DATE_FORMAT(sub.created_at, '%d-%m-%Y %h:%i %p') AS created_date,
                       sub.failure_reason,
                       sa.scheme_acc_number, sa.id_scheme_account,
                       s.scheme_name, s.code,
                       CONCAT(IFNULL(c.firstname,''), ' ', IFNULL(c.lastname,'')) AS customer_name,
                       c.mobile,
                       IFNULL(b.name, '-') AS branch_name
                FROM " . self::SUB_TABLE . " sub
                LEFT JOIN " . self::ACC_TABLE . " sa ON (sa.id_scheme_account = sub.id_scheme_account)
                LEFT JOIN " . self::SCH_TABLE . " s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN " . self::CUS_TABLE . " c ON (c.id_customer = sa.id_customer)
                LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
                WHERE DATE(sub.created_at) BETWEEN ? AND ?";

        $params = array($from_date, $to_date);

        if ($id_customer != '' && $id_customer > 0) {
            $sql .= " AND sa.id_customer = ?";
            $params[] = $id_customer;
        }

        if ($mobile != '') {
            $sql .= " AND c.mobile LIKE ?";
            $params[] = '%' . $mobile . '%';
        }

        $sql .= " ORDER BY sub.created_at DESC";

        return $this->db->query($sql, $params)->result_array();
    }

    /**
     * Get total paid installments for a scheme account.
     * Simplified version — used to update scheme_account.total_paid_ins.
     */
    function getPaidInstallmentCount($id_scheme_account)
    {
        $sql = "SELECT COUNT(DISTINCT DATE_FORMAT(p.date_payment, '%Y%m')) AS paid_installments
                FROM " . self::PAY_TABLE . " p
                WHERE p.id_scheme_account = ?
                AND p.payment_status = 1";

        $result = $this->db->query($sql, array($id_scheme_account))->row_array();
        return isset($result['paid_installments']) ? $result['paid_installments'] : 0;
    }

    /**
     * Generic update helper for any table.
     */
    function updData($data, $col, $val, $table)
    {
        $this->db->where($col, $val);
        return $this->db->update($table, $data);
    }

    /**
     * Generic insert helper for any table.
     */
    function insertData($data, $table)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }
}
