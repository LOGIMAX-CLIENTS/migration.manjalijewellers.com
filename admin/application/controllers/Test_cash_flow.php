<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cash Flow Test Controller
 * 
 * Comparison test: runs old and new cash methods side by side.
 * URL: /admin_opening_master/test_cash_flow
 * 
 * Created: 2026-05-23 | Task: 60757ac5135b
 */
class Test_cash_flow extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ret_reports_model');
        $this->load->model('ret_catalog_model');
        $this->load->model('cash_flow_model');
        $this->load->database();
    }

    public function index()
    {
        header('Content-Type: text/plain; charset=utf-8');

        echo "================================================================\n";
        echo "  CASH FLOW MODEL - OLD vs NEW COMPARISON TEST\n";
        echo "  Date: " . date('Y-m-d H:i:s') . "\n";
        echo "================================================================\n\n";

        // Get active branches
        $branches = $this->db->query("SELECT id_branch, name FROM branch WHERE active = 1 ORDER BY id_branch")->result_array();

        $test_date = date('d-m-Y');
        $test_date_ymd = date('Y-m-d');

        echo "Test Date: $test_date ($test_date_ymd)\n";
        echo "Branches: " . count($branches) . "\n\n";

        // Get profile settings for EDA filter
        $profile_id = $this->session->userdata('profile');
        if (empty($profile_id)) $profile_id = 1;
        $profile = $this->ret_reports_model->get_profile_settings($profile_id);
        $allow_bill_type = isset($profile['allow_bill_type']) ? $profile['allow_bill_type'] : 3;
        echo "Profile ID: $profile_id, allow_bill_type: $allow_bill_type\n\n";

        // ============================================================
        // TEST 1: Cash Book Opening - Old vs New
        // ============================================================
        echo "==============================================================\n";
        echo "  TEST 1: CASH BOOK OPENING BALANCE\n";
        echo "  Old: ret_reports_model->get_cash_book_opening()\n";
        echo "  New: cash_flow_model->get_cash_in_hand()\n";
        echo "==============================================================\n\n";

        echo str_pad("Branch", 25) . str_pad("OLD Opening", 18) . str_pad("NEW Opening", 18) . str_pad("Diff", 15) . "Status\n";
        echo str_repeat("-", 90) . "\n";

        foreach ($branches as $branch) {
            $bid = $branch['id_branch'];
            $bname = $branch['name'];

            // OLD method
            $old_opening = $this->ret_reports_model->get_cash_book_opening($test_date, $bid);

            // NEW method
            $new_opening = $this->cash_flow_model->get_cash_in_hand($bid, $test_date_ymd, $allow_bill_type);

            $diff = $old_opening - $new_opening;
            $abs_diff = abs($diff);
            $status = $abs_diff < 0.01 ? "MATCH" : "DIFF";

            echo str_pad($bname, 25)
               . str_pad(number_format($old_opening, 2), 18)
               . str_pad(number_format($new_opening, 2), 18)
               . str_pad(number_format($diff, 2), 15)
               . $status . "\n";
        }

        echo "\n";

        // ============================================================
        // TEST 2: Deposit Cash - Old vs New
        // ============================================================
        echo "==============================================================\n";
        echo "  TEST 2: DEPOSIT PAGE CASH BALANCE\n";
        echo "  Old: ret_catalog_model->getall_cashamt()\n";
        echo "  New: cash_flow_model->get_cash_split()\n";
        echo "==============================================================\n\n";

        echo str_pad("Branch", 25) . str_pad("OLD Retail", 15) . str_pad("NEW Retail", 15) . str_pad("OLD Chit", 15) . str_pad("NEW Chit", 15) . "Note\n";
        echo str_repeat("-", 100) . "\n";

        foreach ($branches as $branch) {
            $bid = $branch['id_branch'];
            $bname = $branch['name'];

            $old_cash = $this->ret_catalog_model->getall_cashamt($bid);
            $new_cash = $this->cash_flow_model->get_cash_split($bid);

            $retail_diff = $old_cash['retail_cash'] - $new_cash['retail_cash'];
            $chit_diff = $old_cash['chit_cash'] - $new_cash['chit_cash'];

            $note = '';
            if (abs($retail_diff) >= 0.01) {
                $note = "Retail diff: " . number_format($retail_diff, 2);
            }
            if (abs($chit_diff) >= 0.01) {
                $note .= ($note ? " | " : "") . "Chit diff: " . number_format($chit_diff, 2);
            }
            if (empty($note)) $note = "MATCH";

            echo str_pad($bname, 25)
               . str_pad(number_format($old_cash['retail_cash'], 2), 15)
               . str_pad(number_format($new_cash['retail_cash'], 2), 15)
               . str_pad(number_format($old_cash['chit_cash'], 2), 15)
               . str_pad(number_format($new_cash['chit_cash'], 2), 15)
               . $note . "\n";
        }

        echo "\n";

        // ============================================================
        // TEST 3: Detailed Breakdown for first branch
        // ============================================================
        echo "==============================================================\n";
        echo "  TEST 3: DETAILED BREAKDOWN\n";
        echo "==============================================================\n\n";

        $test_branch = $branches[0]['id_branch'];
        echo "Branch: " . $branches[0]['name'] . " (ID: $test_branch)\n";
        echo "As of: $test_date_ymd\n\n";

        $breakdown = $this->cash_flow_model->get_cash_in_hand($test_branch, $test_date_ymd, $allow_bill_type, true);

        echo str_pad("Component", 25) . str_pad("Amount", 18) . "Direction\n";
        echo str_repeat("-", 60) . "\n";
        echo str_pad("Opening Balance", 25)    . str_pad(number_format($breakdown['opening_amount'], 2), 18)  . "Seed\n";
        echo str_pad("  (from date)", 25)      . str_pad($breakdown['opening_date'], 18)                      . "\n";
        echo str_pad("Billing Cash", 25)       . str_pad(number_format($breakdown['billing_cash'], 2), 18)    . "+ IN\n";
        echo str_pad("Receipt Cash IN", 25)    . str_pad(number_format($breakdown['receipt_cash_in'], 2), 18) . "+ IN\n";
        echo str_pad("Receipt Cash OUT", 25)   . str_pad(number_format($breakdown['receipt_cash_out'], 2), 18). "- OUT\n";
        echo str_pad("Chit Cash", 25)          . str_pad(number_format($breakdown['chit_cash'], 2), 18)       . "+ IN\n";
        echo str_pad("Bank Deposits", 25)      . str_pad(number_format($breakdown['bank_deposits'], 2), 18)   . "- OUT\n";
        echo str_pad("Sales Refunds", 25)      . str_pad(number_format($breakdown['sales_refunds'], 2), 18)   . "- OUT\n";
        echo str_pad("Adj Credit", 25)         . str_pad(number_format($breakdown['adj_credit'], 2), 18)      . "+ IN\n";
        echo str_pad("Adj Debit", 25)          . str_pad(number_format($breakdown['adj_debit'], 2), 18)       . "- OUT\n";
        echo str_repeat("=", 60) . "\n";
        echo str_pad("CASH IN HAND", 25)       . str_pad(number_format($breakdown['cash_in_hand'], 2), 18)    . "TOTAL\n";

        echo "\n";

        // ============================================================
        // TEST 4: Sub-function isolation (current month)
        // ============================================================
        echo "==============================================================\n";
        echo "  TEST 4: SUB-FUNCTION ISOLATION (current month)\n";
        echo "==============================================================\n\n";

        $month_from = date('Y-m-01');
        $month_to   = date('Y-m-d', strtotime('+1 day'));

        echo "Period: $month_from to $month_to\n";
        echo "Branch: " . $branches[0]['name'] . "\n\n";

        echo str_pad("Function", 35) . "Result\n";
        echo str_repeat("-", 65) . "\n";

        echo str_pad("get_billing_cash()", 35) . number_format($this->cash_flow_model->get_billing_cash($month_from, $month_to, $test_branch), 2) . "\n";
        echo str_pad("get_receipt_cash_in()", 35) . number_format($this->cash_flow_model->get_receipt_cash_in($month_from, $month_to, $test_branch), 2) . "\n";
        echo str_pad("get_receipt_cash_out()", 35) . number_format($this->cash_flow_model->get_receipt_cash_out($month_from, $month_to, $test_branch), 2) . "\n";
        echo str_pad("get_chit_cash()", 35) . number_format($this->cash_flow_model->get_chit_cash($month_from, $month_to, $test_branch), 2) . "\n";
        echo str_pad("get_bank_deposit_total()", 35) . number_format($this->cash_flow_model->get_bank_deposit_total($month_from, $month_to, $test_branch), 2) . "\n";
        echo str_pad("get_sales_refund_total()", 35) . number_format($this->cash_flow_model->get_sales_refund_total($month_from, $month_to, $test_branch), 2) . "\n";

        $adj = $this->cash_flow_model->get_cash_adjustment_total($month_from, $month_to, $test_branch);
        echo str_pad("get_cash_adjustment_total()", 35) . "Cr: " . number_format($adj['credit'], 2) . " | Dr: " . number_format($adj['debit'], 2) . " | Net: " . number_format($adj['net'], 2) . "\n";

        echo "\n================================================================\n";
        echo "  TEST COMPLETE\n";
        echo "================================================================\n";

        // ============================================================
        // TEST 5: Deposit Available per Branch
        // ============================================================
        echo "\n==============================================================\n";
        echo "  TEST 5: DEPOSIT AVAILABLE PER BRANCH\n";
        echo "  cash_flow_model->get_deposit_available()\n";
        echo "==============================================================\n\n";

        echo str_pad("Branch", 20) . str_pad("Retail Cash", 15) . str_pad("Ret Deps", 15)
           . str_pad("Ret Avail", 15) . str_pad("Chit Cash", 15) . str_pad("Chit Deps", 12)
           . str_pad("Chit Avail", 12) . "From\n";
        echo str_repeat("-", 120) . "\n";

        foreach ($branches as $branch) {
            $bid = $branch['id_branch'];
            $avail = $this->cash_flow_model->get_deposit_available($bid);

            echo str_pad($branch['name'], 20)
               . str_pad(number_format($avail['retail_cash'], 2), 15)
               . str_pad(number_format($avail['retail_deposits'], 2), 15)
               . str_pad(number_format($avail['retail_available'], 2), 15)
               . str_pad(number_format($avail['chit_cash'], 2), 15)
               . str_pad(number_format($avail['chit_deposits'], 2), 12)
               . str_pad(number_format($avail['chit_available'], 2), 12)
               . $avail['from_date'] . "\n";
        }

        // Show LOGIMAX detail
        echo "\n--- LOGIMAX (Branch 7) Detail ---\n";
        $lmx = $this->cash_flow_model->get_cash_in_hand(7, date('Y-m-d', strtotime('+1 day')), $allow_bill_type, true);
        echo "Cash Book Closing (incl today): " . number_format($lmx['cash_in_hand'], 2) . "\n";
        $lmx_dep = $this->cash_flow_model->get_deposit_available(7);
        echo "Deposit Retail Available:        " . number_format($lmx_dep['retail_available'], 2) . "\n";
        echo "Deposit Chit Available:          " . number_format($lmx_dep['chit_available'], 2) . "\n";
        echo "Deposit Total Available:         " . number_format($lmx_dep['total_available'], 2) . "\n";
        echo "Cash Book = Deposit Total?       " . (abs($lmx['cash_in_hand'] - $lmx_dep['total_available']) < 0.01 ? "YES ✓" : "NO — diff: " . number_format($lmx['cash_in_hand'] - $lmx_dep['total_available'], 2)) . "\n";
    }
}
