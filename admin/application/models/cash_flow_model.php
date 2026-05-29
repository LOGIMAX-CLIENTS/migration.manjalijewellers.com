<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cash Flow Model
 * 
 * Unified, modular cash flow calculation engine.
 * Provides atomic sub-functions for each cash movement type.
 * All functions share a common signature pattern for reusability.
 * 
 * Usage:
 *   $this->load->model('cash_flow_model');
 *   $balance = $this->cash_flow_model->get_cash_in_hand($branch_id, '2024-01-15');
 *   $billing = $this->cash_flow_model->get_billing_cash('2024-01-01', '2024-01-15', $branch_id);
 * 
 * Created: 2026-05-23 | Task: 60757ac5135b (Opening Balance Integration)
 */
class Cash_flow_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // =========================================================================
    // 1. OPENING BALANCE — from opening_master table
    // =========================================================================

    /**
     * Get the cash opening balance for a branch from opening_master.
     * Falls back to branch.opening_bal if no entry in opening_master.
     * 
     * @param int $branch_id  Branch ID (0 = all branches)
     * @return array ['amount' => float, 'opening_date' => 'Y-m-d']
     */
    public function get_opening_balance($branch_id = 0)
    {
        // Try opening_master first (type=1 = Cash)
        $this->db->select('SUM(amount) as amount, MIN(opening_date) as opening_date');
        $this->db->from('opening_master');
        $this->db->where('type', 1); // 1 = Cash
        $this->db->where('status', 1);
        if ($branch_id > 0) {
            $this->db->where('id_branch', $branch_id);
        }
        $row = $this->db->get()->row();

        if ($row && floatval($row->amount) > 0) {
            return array(
                'amount'       => floatval($row->amount),
                'opening_date' => $row->opening_date
            );
        }

        // Fallback to branch.opening_bal
        $this->db->select('SUM(opening_bal) as amount');
        $this->db->from('branch');
        $this->db->where('active', 1);
        if ($branch_id > 0) {
            $this->db->where('id_branch', $branch_id);
        }
        $fallback = $this->db->get()->row();

        return array(
            'amount'       => floatval($fallback->amount),
            'opening_date' => null // No specific date in old system
        );
    }

    // =========================================================================
    // 2. BILLING CASH — Sales, Advances, Credit Collections
    // =========================================================================

    /**
     * Get total cash received from billing transactions.
     * Includes: sales payments, order advances (bill_type=5), credit collections (bill_type=8)
     * Excludes: sales refunds (type=2, payment_for=3)
     * 
     * @param string $from_date  Start date (Y-m-d)
     * @param string $to_date    End date (Y-m-d)
     * @param int    $branch_id  Branch ID (0 = all)
     * @param string $payment_mode  Payment mode filter (default: 'CASH')
     * @param int    $allow_bill_type  EDA filter: 1=EDA, 2=non-EDA, 3=all (default: 3)
     * @return float Total billing cash received
     */
    public function get_billing_cash($from_date, $to_date, $branch_id = 0, $payment_mode = 'CASH', $allow_bill_type = 3)
    {
        $result = $this->db->query("
            SELECT IFNULL(SUM(p.payment_amount), 0) AS total
            FROM ret_billing_payment p
            INNER JOIN ret_billing b ON b.bill_id = p.bill_id
            WHERE b.bill_status = 1
                AND b.bill_type != 13
                AND NOT (p.type = 2 AND p.payment_for = 3)
                AND p.payment_mode = ?
                AND DATE(b.bill_date) >= ?
                AND DATE(b.bill_date) < ?
                AND (? = 0 OR b.id_branch = ?)
                AND (
                    ? = 3 OR 
                    (? = 1 AND b.is_eda = 1) OR 
                    (? = 2 AND b.is_eda = 2)
                )
        ", array(
            $payment_mode,
            $from_date,
            $to_date,
            $branch_id, $branch_id,
            $allow_bill_type, $allow_bill_type, $allow_bill_type
        ))->row();

        return floatval($result->total);
    }

    // =========================================================================
    // 3. RECEIPT CASH IN — General advances, Petty cash returns
    // =========================================================================

    /**
     * Get total cash received from issue/receipt transactions (receipts = cash IN).
     * Includes: type=2 (receipts) — general advances, petty cash returns, credit collections
     * 
     * @param string $from_date  Start date (Y-m-d)
     * @param string $to_date    End date (Y-m-d)
     * @param int    $branch_id  Branch ID (0 = all)
     * @param string $payment_mode  Payment mode filter (default: 'Cash')
     * @param int    $allow_bill_type  EDA filter (default: 3)
     * @return float Total receipt cash received
     */
    public function get_receipt_cash_in($from_date, $to_date, $branch_id = 0, $payment_mode = 'Cash', $allow_bill_type = 3)
    {
        $result = $this->db->query("
            SELECT IFNULL(SUM(p.payment_amount), 0) AS total
            FROM ret_issue_rcpt_payment p
            INNER JOIN ret_issue_receipt r ON r.id_issue_receipt = p.id_issue_rcpt
            WHERE p.payment_status = 1
                AND r.bill_status = 1
                AND p.payment_mode = ?
                AND r.type = 2
                AND p.payment_amount != 0
                AND DATE(r.bill_date) >= ?
                AND DATE(r.bill_date) < ?
                AND (? = 0 OR r.id_branch = ?)
                AND (
                    ? = 3 OR 
                    (? = 1 AND r.is_eda = 1) OR 
                    (? = 2 AND r.is_eda = 2)
                )
        ", array(
            $payment_mode,
            $from_date,
            $to_date,
            $branch_id, $branch_id,
            $allow_bill_type, $allow_bill_type, $allow_bill_type
        ))->row();

        return floatval($result->total);
    }

    // =========================================================================
    // 4. RECEIPT CASH OUT — Expenses, Issues
    // =========================================================================

    /**
     * Get total cash paid out via issue/receipt transactions (issues = cash OUT).
     * Includes: type=1 (issues) — expenses, petty cash, advance refunds
     * 
     * @param string $from_date  Start date (Y-m-d)
     * @param string $to_date    End date (Y-m-d)
     * @param int    $branch_id  Branch ID (0 = all)
     * @param string $payment_mode  Payment mode filter (default: 'Cash')
     * @param int    $allow_bill_type  EDA filter (default: 3)
     * @return float Total cash paid out (positive number)
     */
    public function get_receipt_cash_out($from_date, $to_date, $branch_id = 0, $payment_mode = 'Cash', $allow_bill_type = 3)
    {
        $result = $this->db->query("
            SELECT IFNULL(SUM(p.payment_amount), 0) AS total
            FROM ret_issue_rcpt_payment p
            INNER JOIN ret_issue_receipt r ON r.id_issue_receipt = p.id_issue_rcpt
            WHERE p.payment_status = 1
                AND r.bill_status = 1
                AND p.payment_mode = ?
                AND r.type = 1
                AND DATE(r.bill_date) >= ?
                AND DATE(r.bill_date) < ?
                AND (? = 0 OR r.id_branch = ?)
                AND (
                    ? = 3 OR 
                    (? = 1 AND r.is_eda = 1) OR 
                    (? = 2 AND r.is_eda = 2)
                )
        ", array(
            $payment_mode,
            $from_date,
            $to_date,
            $branch_id, $branch_id,
            $allow_bill_type, $allow_bill_type, $allow_bill_type
        ))->row();

        return floatval($result->total);
    }

    // =========================================================================
    // 5. CHIT CASH — Chit/CRM installment collections
    // =========================================================================

    /**
     * Get total cash received from chit (CRM) collections.
     * 
     * @param string $from_date  Start date (Y-m-d)
     * @param string $to_date    End date (Y-m-d)
     * @param int    $branch_id  Branch ID (0 = all)
     * @return float Total chit cash collected
     */
    public function get_chit_cash($from_date, $to_date, $branch_id = 0)
    {
        $result = $this->db->query("
            SELECT IFNULL(SUM(
                CASE WHEN pmd.payment_mode != 'FP' THEN pmd.payment_amount ELSE 0 END
            ), 0) AS total
            FROM payment_mode_details pmd
            INNER JOIN payment p ON p.id_payment = pmd.id_payment
            WHERE pmd.payment_status = 1
                AND p.payment_status = 1
                AND pmd.is_active = 1
                AND pmd.payment_mode = 'CSH'
                AND DATE(COALESCE(p.custom_entry_date, pmd.payment_date)) >= ?
                AND DATE(COALESCE(p.custom_entry_date, pmd.payment_date)) < ?
                AND (? = 0 OR p.id_branch = ?)
        ", array(
            $from_date,
            $to_date,
            $branch_id, $branch_id
        ))->row();

        return floatval($result->total);
    }

    // =========================================================================
    // 6. BANK DEPOSITS — Cash moved to/from bank
    // =========================================================================

    /**
     * Get net bank deposit amount (cash removed from hand to bank).
     * dep_type=1 (Credit) = cash goes OUT to bank (positive)
     * dep_type=0 (Debit)  = cash comes IN from bank (negative for net deposit)
     * 
     * @param string $from_date  Start date (Y-m-d)
     * @param string $to_date    End date (Y-m-d)
     * @param int    $branch_id  Branch ID (0 = all)
     * @return float Net deposits (positive = cash left hand to bank)
     */
    public function get_bank_deposit_total($from_date, $to_date, $branch_id = 0)
    {
        $result = $this->db->query("
            SELECT IFNULL(SUM(
                CASE WHEN dep_type = 1 THEN dep_amount
                     WHEN dep_type = 0 THEN -dep_amount
                     ELSE 0 END
            ), 0) AS total
            FROM ret_bank_deposit
            WHERE DATE(dep_date) >= ?
                AND DATE(dep_date) < ?
                AND (? = 0 OR dep_branch = ?)
        ", array(
            $from_date,
            $to_date,
            $branch_id, $branch_id
        ))->row();

        return floatval($result->total);
    }

    // =========================================================================
    // 7. SALES REFUNDS — Cash returned to customers
    // =========================================================================

    /**
     * Get total cash paid as sales refunds.
     * These are billing payments where type=2, payment_for=3
     * 
     * @param string $from_date  Start date (Y-m-d)
     * @param string $to_date    End date (Y-m-d)
     * @param int    $branch_id  Branch ID (0 = all)
     * @param string $payment_mode  Payment mode filter (default: 'CASH')
     * @param int    $allow_bill_type  EDA filter (default: 3)
     * @return float Total refund cash paid out (positive number)
     */
    public function get_sales_refund_total($from_date, $to_date, $branch_id = 0, $payment_mode = 'CASH', $allow_bill_type = 3)
    {
        $result = $this->db->query("
            SELECT IFNULL(SUM(p.payment_amount), 0) AS total
            FROM ret_billing_payment p
            INNER JOIN ret_billing b ON b.bill_id = p.bill_id
            WHERE b.bill_status = 1
                AND b.bill_type != 5 AND b.bill_type != 8
                AND p.type = 2 AND p.payment_for = 3
                AND p.payment_mode = ?
                AND DATE(b.bill_date) >= ?
                AND DATE(b.bill_date) < ?
                AND (? = 0 OR b.id_branch = ?)
                AND (
                    ? = 3 OR 
                    (? = 1 AND b.is_eda = 1) OR 
                    (? = 2 AND b.is_eda = 2)
                )
        ", array(
            $payment_mode,
            $from_date,
            $to_date,
            $branch_id, $branch_id,
            $allow_bill_type, $allow_bill_type, $allow_bill_type
        ))->row();

        return floatval($result->total);
    }

    // =========================================================================
    // 8. CASH ADJUSTMENTS — Manual corrections
    // =========================================================================

    /**
     * Get net cash adjustment amount.
     * adj_type=1 = Credit (cash added), adj_type=2 = Debit (cash removed)
     * 
     * @param string $from_date  Start date (Y-m-d)
     * @param string $to_date    End date (Y-m-d)
     * @param int    $branch_id  Branch ID (0 = all)
     * @return array ['credit' => float, 'debit' => float, 'net' => float]
     */
    public function get_cash_adjustment_total($from_date, $to_date, $branch_id = 0)
    {
        $result = $this->db->query("
            SELECT 
                IFNULL(SUM(CASE WHEN adj_type = 1 THEN amount ELSE 0 END), 0) AS total_credit,
                IFNULL(SUM(CASE WHEN adj_type = 2 THEN amount ELSE 0 END), 0) AS total_debit
            FROM ret_cash_adjustment
            WHERE status = 1
                AND adj_date >= ?
                AND adj_date < ?
                AND (? = 0 OR id_branch = ?)
        ", array(
            $from_date,
            $to_date,
            $branch_id, $branch_id
        ))->row();

        $credit = floatval($result->total_credit);
        $debit  = floatval($result->total_debit);

        return array(
            'credit' => $credit,
            'debit'  => $debit,
            'net'    => $credit - $debit
        );
    }

    // =========================================================================
    // 9. MASTER: GET CASH IN HAND
    // =========================================================================

    /**
     * Calculate total cash-in-hand for a branch as of a given date.
     * This is the SINGLE SOURCE OF TRUTH for cash balance.
     * 
     * Formula:
     *   Cash = Opening
     *        + Billing Cash IN
     *        + Receipt Cash IN
     *        - Receipt Cash OUT
     *        + Chit Cash IN
     *        - Bank Deposits (net)
     *        - Sales Refunds
     *        + Cash Adjustments (net)
     * 
     * @param int    $branch_id       Branch ID (0 = all)
     * @param string $as_of_date      Calculate balance as of this date (Y-m-d), exclusive
     * @param int    $allow_bill_type EDA filter (default: 3 = all)
     * @param bool   $include_breakdown If true, returns detailed breakdown
     * @return float|array Cash-in-hand balance (or breakdown array if requested)
     */
    public function get_cash_in_hand($branch_id = 0, $as_of_date = null, $allow_bill_type = 3, $include_breakdown = false)
    {
        if ($as_of_date === null) {
            $as_of_date = date('Y-m-d');
        }

        // 1. Get opening balance and start date
        $opening = $this->get_opening_balance($branch_id);
        $opening_amount = $opening['amount'];
        $from_date = $opening['opening_date'];

        // If no opening date found, there's nothing to calculate from
        if (empty($from_date)) {
            $from_date = '2000-01-01'; // Effectively "from the beginning"
        }

        // 2. Calculate each component
        $billing_cash     = $this->get_billing_cash($from_date, $as_of_date, $branch_id, 'CASH', $allow_bill_type);
        $receipt_cash_in  = $this->get_receipt_cash_in($from_date, $as_of_date, $branch_id, 'Cash', $allow_bill_type);
        $receipt_cash_out = $this->get_receipt_cash_out($from_date, $as_of_date, $branch_id, 'Cash', $allow_bill_type);
        $chit_cash        = $this->get_chit_cash($from_date, $as_of_date, $branch_id);
        $bank_deposits    = $this->get_bank_deposit_total($from_date, $as_of_date, $branch_id);
        $sales_refunds    = $this->get_sales_refund_total($from_date, $as_of_date, $branch_id, 'CASH', $allow_bill_type);
        $adjustments      = $this->get_cash_adjustment_total($from_date, $as_of_date, $branch_id);

        // 3. Compose final balance
        $cash_in_hand = $opening_amount
                      + $billing_cash
                      + $receipt_cash_in
                      - $receipt_cash_out
                      + $chit_cash
                      - $bank_deposits
                      - $sales_refunds
                      + $adjustments['net'];

        if ($include_breakdown) {
            return array(
                'opening_amount'    => $opening_amount,
                'opening_date'      => $from_date,
                'as_of_date'        => $as_of_date,
                'branch_id'         => $branch_id,
                'billing_cash'      => $billing_cash,
                'receipt_cash_in'   => $receipt_cash_in,
                'receipt_cash_out'  => $receipt_cash_out,
                'chit_cash'         => $chit_cash,
                'bank_deposits'     => $bank_deposits,
                'sales_refunds'     => $sales_refunds,
                'adj_credit'        => $adjustments['credit'],
                'adj_debit'         => $adjustments['debit'],
                'adj_net'           => $adjustments['net'],
                'cash_in_hand'      => $cash_in_hand
            );
        }

        return $cash_in_hand;
    }

    // =========================================================================
    // CONVENIENCE: Retail vs Chit split (for deposit page compatibility)
    // =========================================================================

    /**
     * Get cash balances split by retail and chit (for deposit page).
     * Retail = billing + issue/receipt
     * Chit   = chit collections
     * 
     * @param int    $branch_id  Branch ID (0 = all)
     * @param string $as_of_date Date to calculate up to (Y-m-d), exclusive
     * @param int    $allow_bill_type EDA filter (default: 3)
     * @return array ['retail_cash' => float, 'chit_cash' => float, 'total_pay' => float]
     */
    public function get_cash_split($branch_id = 0, $as_of_date = null, $allow_bill_type = 3)
    {
        if ($as_of_date === null) {
            $as_of_date = date('Y-m-d', strtotime('+1 day')); // Include today
        }

        $opening = $this->get_opening_balance($branch_id);
        $from_date = $opening['opening_date'];
        if (empty($from_date)) {
            $from_date = '2000-01-01';
        }

        // Retail = opening + billing + receipts - refunds - adjustments
        $billing_cash     = $this->get_billing_cash($from_date, $as_of_date, $branch_id, 'CASH', $allow_bill_type);
        $receipt_cash_in  = $this->get_receipt_cash_in($from_date, $as_of_date, $branch_id, 'Cash', $allow_bill_type);
        $receipt_cash_out = $this->get_receipt_cash_out($from_date, $as_of_date, $branch_id, 'Cash', $allow_bill_type);
        $sales_refunds    = $this->get_sales_refund_total($from_date, $as_of_date, $branch_id, 'CASH', $allow_bill_type);
        $adjustments      = $this->get_cash_adjustment_total($from_date, $as_of_date, $branch_id);

        $retail_cash = $opening['amount']
                     + $billing_cash
                     + $receipt_cash_in
                     - $receipt_cash_out
                     - $sales_refunds
                     + $adjustments['net'];

        // Chit = chit collections only
        $chit_cash = $this->get_chit_cash($from_date, $as_of_date, $branch_id);

        return array(
            'retail_cash' => $retail_cash,
            'chit_cash'   => $chit_cash,
            'total_pay'   => $retail_cash + $chit_cash
        );
    }

    // =========================================================================
    // CONVENIENCE: Deposit-available split (for deposit page)
    // =========================================================================

    /**
     * Get cash available for deposit, split by retail and chit.
     * This is get_cash_split() MINUS bank deposits since opening date.
     * 
     * The deposit page needs:
     *   Retail available = retail_cash - retail_deposits_since_opening
     *   Chit available   = chit_cash - chit_deposits_since_opening
     * 
     * @param int    $branch_id  Branch ID (0 = all)
     * @param int    $cash_type  1=retail, 2=chit, 0=both
     * @return array ['retail_available' => float, 'chit_available' => float, 'total_available' => float]
     */
    public function get_deposit_available($branch_id = 0, $cash_type = 0)
    {
        $as_of_date = date('Y-m-d', strtotime('+1 day')); // Include today

        $opening = $this->get_opening_balance($branch_id);
        $from_date = $opening['opening_date'];
        if (empty($from_date)) {
            $from_date = '2000-01-01';
        }

        // Get cash split (without deposits)
        $split = $this->get_cash_split($branch_id, $as_of_date);

        // Get deposits since opening date, split by type
        // type=1 is retail deposits, type=2 is chit deposits
        $retail_deposits = $this->_get_deposit_sum_by_type($from_date, $as_of_date, $branch_id, 1);
        $chit_deposits   = $this->_get_deposit_sum_by_type($from_date, $as_of_date, $branch_id, 2);

        $retail_available = $split['retail_cash'] - $retail_deposits;
        $chit_available   = $split['chit_cash'] - $chit_deposits;

        return array(
            'retail_available' => $retail_available,
            'chit_available'   => $chit_available,
            'total_available'  => $retail_available + $chit_available,
            // Also expose raw totals for the controller
            'retail_cash'      => $split['retail_cash'],
            'chit_cash'        => $split['chit_cash'],
            'retail_deposits'  => $retail_deposits,
            'chit_deposits'    => $chit_deposits,
            'from_date'        => $from_date
        );
    }

    /**
     * Get sum of bank deposits by cash type (retail/chit) with date filtering.
     * 
     * @param string $from_date  Start date (Y-m-d)
     * @param string $to_date    End date (Y-m-d)
     * @param int    $branch_id  Branch ID (0 = all)
     * @param int    $cash_type  1=retail, 2=chit
     * @return float Net deposit amount
     */
    private function _get_deposit_sum_by_type($from_date, $to_date, $branch_id = 0, $cash_type = 0)
    {
        $result = $this->db->query("
            SELECT IFNULL(SUM(
                CASE WHEN dep_type = 1 THEN dep_amount
                     WHEN dep_type = 0 THEN -dep_amount
                     ELSE 0 END
            ), 0) AS total
            FROM ret_bank_deposit
            WHERE DATE(dep_date) >= ?
                AND DATE(dep_date) < ?
                AND (? = 0 OR dep_branch = ?)
                AND (? = 0 OR type = ?)
        ", array(
            $from_date,
            $to_date,
            $branch_id, $branch_id,
            $cash_type, $cash_type
        ))->row();

        return floatval($result->total);
    }
}
