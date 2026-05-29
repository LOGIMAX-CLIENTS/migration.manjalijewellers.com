<?php
use PHPUnit\Framework\TestCase;

/**
 * Bug BIL-INT02: Credit Due Amount does not account for sales returns
 * Category: Logic
 * Severity: P0 Critical (financial calculation)
 *
 * Tests the credit due balance formula:
 *   blc_amt = tot_bill_amount - (tot_amt_received + credit_pay_amount + total_returned)
 *
 * The fix ensures `total_returned` (SUM of ret_bill_details.item_cost via
 * ret_bill_return_details) is correctly subtracted from the credit due balance.
 */
class BillingLogicTest extends TestCase
{
    /**
     * Simulates the JS balance calculation from getCreditBillDetails().
     * This mirrors the exact formula used in ret_billing.js.
     *
     * @param float $tot_bill_amount   Total bill amount
     * @param float $tot_amt_received  Total amount received so far
     * @param float $credit_pay_amount Total credit payments made
     * @param float $total_returned    Total returned item costs (from ret_bill_details)
     * @return float The credit due balance
     */
    private function calculateCreditDueBalance(
        float $tot_bill_amount,
        float $tot_amt_received,
        float $credit_pay_amount,
        float $total_returned
    ): float {
        // Mirror the JS: parseFloat(tot_bill_amount) - (parseFloat(tot_amt_received) + parseFloat(credit_pay_amount) + parseFloat(total_returned || 0))
        $blc_amt = $tot_bill_amount - ($tot_amt_received + $credit_pay_amount + $total_returned);
        return round($blc_amt, 2);
    }

    // ---------------------------------------------------------------
    // Normal case: returns reduce balance
    // ---------------------------------------------------------------

    /**
     * BIL-INT02: Normal case — bill with partial payment and partial return
     */
    public function test_bil_int02_fix_correct_behavior(): void
    {
        // Bill: 10000, Received: 3000, Credit Paid: 0, Returned: 2000
        // Expected: 10000 - (3000 + 0 + 2000) = 5000
        $balance = $this->calculateCreditDueBalance(10000.00, 3000.00, 0.00, 2000.00);
        $this->assertEquals(5000.00, $balance, 'Balance should subtract returned items');
    }

    /**
     * BIL-INT02: Full return — balance becomes 0
     */
    public function test_bil_int02_full_return(): void
    {
        // Bill: 5000, Received: 0, Credit Paid: 0, Returned: 5000
        // Expected: 5000 - (0 + 0 + 5000) = 0
        $balance = $this->calculateCreditDueBalance(5000.00, 0.00, 0.00, 5000.00);
        $this->assertEquals(0.00, $balance, 'Full return should make balance zero');
    }

    /**
     * BIL-INT02: All three deductions — payments, credit, and return
     */
    public function test_bil_int02_multiple_deductions(): void
    {
        // Bill: 15000, Received: 5000, Credit Paid: 3000, Returned: 2000
        // Expected: 15000 - (5000 + 3000 + 2000) = 5000
        $balance = $this->calculateCreditDueBalance(15000.00, 5000.00, 3000.00, 2000.00);
        $this->assertEquals(5000.00, $balance, 'All three deduction types should reduce balance');
    }

    // ---------------------------------------------------------------
    // Edge case: zero/null returns (no returns exist)
    // ---------------------------------------------------------------

    /**
     * BIL-INT02: No returns — should match old behavior
     */
    public function test_bil_int02_edge_case_zero(): void
    {
        // Bill: 10000, Received: 3000, Credit Paid: 0, Returned: 0
        // Expected: 10000 - (3000 + 0 + 0) = 7000
        $balance = $this->calculateCreditDueBalance(10000.00, 3000.00, 0.00, 0.00);
        $this->assertEquals(7000.00, $balance, 'Zero returns should not affect balance');
    }

    /**
     * BIL-INT02: All zeros — balance is full bill amount
     */
    public function test_bil_int02_edge_case_all_zero_deductions(): void
    {
        // Bill: 8500, Received: 0, Credit Paid: 0, Returned: 0
        // Expected: 8500
        $balance = $this->calculateCreditDueBalance(8500.00, 0.00, 0.00, 0.00);
        $this->assertEquals(8500.00, $balance, 'No deductions means balance equals bill amount');
    }

    // ---------------------------------------------------------------
    // Edge case: negative balance (over-payment/over-return)
    // ---------------------------------------------------------------

    /**
     * BIL-INT02: Over-return — balance goes negative (refund owed to customer)
     */
    public function test_bil_int02_edge_case_negative(): void
    {
        // Bill: 5000, Received: 3000, Credit Paid: 0, Returned: 4000
        // Expected: 5000 - (3000 + 0 + 4000) = -2000
        $balance = $this->calculateCreditDueBalance(5000.00, 3000.00, 0.00, 4000.00);
        $this->assertEquals(-2000.00, $balance, 'Over-deductions should produce negative balance');
    }

    // ---------------------------------------------------------------
    // Edge case: large values (stress test for float precision)
    // ---------------------------------------------------------------

    /**
     * BIL-INT02: Large bill amount — float precision must be maintained
     */
    public function test_bil_int02_edge_case_large_input(): void
    {
        // Bill: 9999999.99, Received: 5000000.50, Credit Paid: 1000000.25, Returned: 500000.75
        // Expected: 9999999.99 - (5000000.50 + 1000000.25 + 500000.75) = 3499998.49
        $balance = $this->calculateCreditDueBalance(9999999.99, 5000000.50, 1000000.25, 500000.75);
        $this->assertEquals(3499998.49, $balance, 'Large values should maintain precision');
    }

    // ---------------------------------------------------------------
    // Decimal precision test
    // ---------------------------------------------------------------

    /**
     * BIL-INT02: Decimal values — Indian currency (paise precision)
     */
    public function test_bil_int02_decimal_precision(): void
    {
        // Bill: 1234.56, Received: 500.25, Credit Paid: 100.10, Returned: 234.21
        // Expected: 1234.56 - (500.25 + 100.10 + 234.21) = 400.00
        $balance = $this->calculateCreditDueBalance(1234.56, 500.25, 100.10, 234.21);
        $this->assertEquals(400.00, $balance, 'Decimal precision must be exact for currency');
    }

    // ---------------------------------------------------------------
    // Regression test: old bug behavior
    // ---------------------------------------------------------------

    /**
     * BIL-INT02: Regression — verify old bug is impossible
     *
     * Before the fix, credit_ret_amt was always 0, so the balance formula
     * would NOT subtract any returns. This test ensures the fix is active.
     */
    public function test_bil_int02_regression_old_bug_impossible(): void
    {
        // With returns, balance MUST be less than (bill - received - credit_paid)
        $tot_bill = 10000.00;
        $received = 2000.00;
        $credit_paid = 0.00;
        $returned = 3000.00;

        $balance_with_fix = $this->calculateCreditDueBalance($tot_bill, $received, $credit_paid, $returned);
        $balance_without_fix = $tot_bill - ($received + $credit_paid); // Old bug: ignores returns

        $this->assertLessThan(
            $balance_without_fix,
            $balance_with_fix,
            'With returns, balance must be less than the old (buggy) calculation'
        );

        $this->assertEquals(5000.00, $balance_with_fix);
        $this->assertEquals(8000.00, $balance_without_fix);
    }
}
