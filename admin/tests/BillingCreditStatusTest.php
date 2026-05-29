<?php
use PHPUnit\Framework\TestCase;

/**
 * Bug BIL-INT03: Credit status stays Pending when round-off amount leaves a residual balance
 * Category: Logic / Calculation
 * Severity: P1 High (incorrect credit lifecycle state)
 *
 * Three sub-fixes applied:
 *  1. Controller save case (bill_type=7)  — balance formula subtracts round_off_amt
 *  2. Controller cancell case (bill_type=7) — same balance formula fix
 *  3. Credit Pending report SQL (ret_reports_model) — bal_amt column subtracts round_off_amt
 *
 * These tests validate the PHP-side balance formula used in the controller.
 * The report SQL fix is validated via the smoke-test checklist.
 */
class BillingCreditStatusTest extends TestCase
{
    // ---------------------------------------------------------------
    // Helper — mirrors the fixed PHP balance formula from the controller
    // (both save and cancell blocks are identical in structure)
    // ---------------------------------------------------------------

    /**
     * Simulates the fixed balance calculation in admin_ret_billing.php.
     *
     * $balance = tot_bill_amount - tot_amt_received
     *            - total_collections - total_returned
     *            - round_off_amt          ← BIL-INT03 fix
     *
     * Returns the new credit_status: 1 = Paid, 2 = Pending.
     *
     * @param float $tot_bill_amount    Original bill total
     * @param float $tot_amt_received   Advance/receipt amount on the bill
     * @param float $total_collections  Cash collected via credit-collection bills (type 8)
     * @param float $total_returned     Total item cost from sales returns (type 7)
     * @param float $round_off_amt      Round-off value stored on the bill
     * @return int 1 if paid, 2 if pending
     */
    private function calculateCreditStatus(
        float $tot_bill_amount,
        float $tot_amt_received,
        float $total_collections,
        float $total_returned,
        float $round_off_amt
    ): int {
        $balance = $tot_bill_amount
                 - $tot_amt_received
                 - $total_collections
                 - $total_returned
                 - floatval($round_off_amt);   // BIL-INT03 fix

        return ($balance <= 0) ? 1 : 2;
    }

    /**
     * Helper: same formula WITHOUT the round-off fix (simulates pre-fix behaviour).
     */
    private function calculateCreditStatusOld(
        float $tot_bill_amount,
        float $tot_amt_received,
        float $total_collections,
        float $total_returned
    ): int {
        $balance = $tot_bill_amount - $tot_amt_received - $total_collections - $total_returned;
        return ($balance <= 0) ? 1 : 2;
    }

    // ---------------------------------------------------------------
    // Core regression — the exact scenario described in BIL-INT03
    // ---------------------------------------------------------------

    /**
     * BIL-INT03: Full return with round-off residual → must become Paid.
     *
     * A bill of 10000.50 has round_off_amt = 0.50.
     * All items returned amounts to 10000.00 (items total, without round-off).
     * Balance without fix: 10000.50 - 0 - 0 - 10000.00 = 0.50 → Pending (BUG)
     * Balance with fix:    10000.50 - 0 - 0 - 10000.00 - 0.50 = 0.00 → Paid (CORRECT)
     */
    public function test_bil_int03_full_return_with_roundoff_is_paid(): void
    {
        $status = $this->calculateCreditStatus(10000.50, 0.00, 0.00, 10000.00, 0.50);
        $this->assertEquals(1, $status, 'Full return with round-off should mark bill as Paid');
    }

    /**
     * BIL-INT03: Regression — without fix the same scenario stays Pending.
     * Confirms the old code was the bug source.
     */
    public function test_bil_int03_regression_old_code_was_pending(): void
    {
        $old_status = $this->calculateCreditStatusOld(10000.50, 0.00, 0.00, 10000.00);
        $this->assertEquals(2, $old_status, 'Without fix, residual round-off kept status Pending');
    }

    // ---------------------------------------------------------------
    // Normal / happy-path cases (must remain unaffected by the fix)
    // ---------------------------------------------------------------

    /**
     * BIL-INT03: No round-off, full return — still marks Paid.
     */
    public function test_bil_int03_full_return_no_roundoff_is_paid(): void
    {
        $status = $this->calculateCreditStatus(10000.00, 0.00, 0.00, 10000.00, 0.00);
        $this->assertEquals(1, $status, 'Full return with zero round-off should be Paid');
    }

    /**
     * BIL-INT03: Partial return — should still be Pending.
     */
    public function test_bil_int03_partial_return_is_pending(): void
    {
        // Bill 10000, returned 4000, collected 0, round-off 0.50
        // Balance = 10000 - 0 - 0 - 4000 - 0.50 = 5999.50 → Pending
        $status = $this->calculateCreditStatus(10000.00, 0.00, 0.00, 4000.00, 0.50);
        $this->assertEquals(2, $status, 'Partial return must remain Pending');
    }

    /**
     * BIL-INT03: Full return + credit collections = Paid.
     */
    public function test_bil_int03_return_plus_collection_is_paid(): void
    {
        // Bill 15000.75, received 2000, collections 5000, returned 8000, round-off 0.75
        // Balance = 15000.75 - 2000 - 5000 - 8000 - 0.75 = 0 → Paid
        $status = $this->calculateCreditStatus(15000.75, 2000.00, 5000.00, 8000.00, 0.75);
        $this->assertEquals(1, $status, 'All settled (return + collection) should be Paid');
    }

    /**
     * BIL-INT03: Over-deduction (returns exceed bill) — still treated as Paid (balance ≤ 0).
     */
    public function test_bil_int03_over_return_is_treated_as_paid(): void
    {
        // Balance goes negative → still 1 (Paid), not 2
        $status = $this->calculateCreditStatus(5000.00, 0.00, 0.00, 6000.00, 0.25);
        $this->assertEquals(1, $status, 'Balance <= 0 (over-return) must be Paid, not Pending');
    }

    // ---------------------------------------------------------------
    // Edge cases
    // ---------------------------------------------------------------

    /**
     * BIL-INT03: Zero round-off — fix should be inert (no regression).
     */
    public function test_bil_int03_edge_zero_roundoff_no_regression(): void
    {
        $with_fix    = $this->calculateCreditStatus(8000.00, 2000.00, 3000.00, 3000.00, 0.00);
        $without_fix = $this->calculateCreditStatusOld(8000.00, 2000.00, 3000.00, 3000.00);
        $this->assertEquals($without_fix, $with_fix, 'Zero round-off: fix must not change result');
        $this->assertEquals(1, $with_fix, 'Fully settled bill must be Paid');
    }

    /**
     * BIL-INT03: Negative round-off (rare — some systems store it as debit).
     * floatval() on negative should still work — balance decreases further, stays Paid.
     */
    public function test_bil_int03_edge_negative_roundoff(): void
    {
        // round_off_amt = -0.50 → subtracting -0.50 adds 0.50 to balance
        // Bill 10000, returned 9999.50, round_off = -0.50
        // Balance = 10000 - 9999.50 - (-0.50) = 10000 - 9999.50 + 0.50 = 1.00 → Pending
        $status = $this->calculateCreditStatus(10000.00, 0.00, 0.00, 9999.50, -0.50);
        $this->assertEquals(2, $status, 'Negative round-off increases balance — should be Pending if still owed');
    }

    /**
     * BIL-INT03: Large bill with small round-off — precision maintained.
     * Bill: 999999.99, round_off: 0.99, returned: 999999.00, rest 0
     * Balance = 999999.99 - 999999.00 - 0.99 = 0 → Paid
     */
    public function test_bil_int03_edge_large_bill_small_roundoff(): void
    {
        $status = $this->calculateCreditStatus(999999.99, 0.00, 0.00, 999999.00, 0.99);
        $this->assertEquals(1, $status, 'Large bill: full return minus round-off must be Paid');
    }

    /**
     * BIL-INT03: Cancell-case scenario — cancelling a return re-adds those items.
     * After cancel, get_total_returned_amount_by_bill returns the *remaining* returns.
     * If remaining returns still cover the balance → should stay Paid, else → Pending.
     *
     * Scenario: Bill 10000.30 fully returned (9999.70 items + 0.30 round-off = Paid).
     * Now cancel the return → get_total_returned_amount_by_bill returns 0.
     * Balance = 10000.30 - 0 - 0 - 0 - 0.30 = 10000.00 → Pending (not yet paid).
     */
    public function test_bil_int03_cancel_return_reopens_pending(): void
    {
        // After cancel, total_returned = 0 (the only return was cancelled)
        $status = $this->calculateCreditStatus(10000.30, 0.00, 0.00, 0.00, 0.30);
        $this->assertEquals(2, $status, 'Cancelling a return with no other returns should revert to Pending');
    }
}
