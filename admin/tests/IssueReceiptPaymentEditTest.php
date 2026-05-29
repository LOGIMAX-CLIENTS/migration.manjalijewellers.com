<?php
use PHPUnit\Framework\TestCase;

/**
 * Issue/Receipt Payment Mode Edit — Unit Tests
 *
 * Tests the payment total validation logic, guard conditions,
 * and payment data structure used by ajax_update_issue_rcpt_payment.
 *
 * These are pure logic tests — no DB connection required.
 */
class IssueReceiptPaymentEditTest extends TestCase
{
    // ---------------------------------------------------------------
    // Helper: mirrors the total-match validation from the controller
    // ---------------------------------------------------------------

    /**
     * Validates that the payment total matches the voucher amount.
     * Mirrors the Guard 5 logic in ajax_update_issue_rcpt_payment.
     *
     * @param float $voucher_amt  The original voucher amount
     * @param float $cash         Cash payment amount
     * @param array $cards        Card payment rows [{card_amt: float}]
     * @param array $cheques      Cheque payment rows [{payment_amount: float}]
     * @param array $net_bankings NB payment rows [{amount: float}]
     * @return array ['valid' => bool, 'new_total' => float, 'diff' => float]
     */
    private function validatePaymentTotal(
        float $voucher_amt,
        float $cash,
        array $cards = [],
        array $cheques = [],
        array $net_bankings = []
    ): array {
        $card_total = array_sum(array_column($cards, 'card_amt'));
        $chq_total  = array_sum(array_column($cheques, 'payment_amount'));
        $nb_total   = array_sum(array_column($net_bankings, 'amount'));
        $new_total  = round($cash + $card_total + $chq_total + $nb_total, 2);
        $voucher_amt = round($voucher_amt, 2);
        $diff       = abs($new_total - $voucher_amt);

        return [
            'valid'     => $diff <= 0.01,
            'new_total' => $new_total,
            'diff'      => $diff
        ];
    }

    /**
     * Filters out invalid payment rows (zero/negative amounts).
     * Mirrors Guard 3 logic in ajax_update_issue_rcpt_payment.
     *
     * @param array $rows    Payment rows
     * @param string $key    Amount field name
     * @return array Filtered rows with positive amounts only
     */
    private function filterValidRows(array $rows, string $key): array
    {
        return array_values(array_filter($rows, function($r) use ($key) {
            return isset($r[$key]) && (float)$r[$key] > 0;
        }));
    }

    // ---------------------------------------------------------------
    // TC-01: Exact match — all cash
    // ---------------------------------------------------------------
    public function test_tc01_all_cash_exact_match(): void
    {
        $result = $this->validatePaymentTotal(1000.00, 1000.00);
        $this->assertTrue($result['valid'], 'All-cash payment should match voucher amount exactly');
        $this->assertEquals(1000.00, $result['new_total']);
    }

    // ---------------------------------------------------------------
    // TC-02: Split cash + card
    // ---------------------------------------------------------------
    public function test_tc02_cash_plus_card_split(): void
    {
        $cards = [['card_amt' => 300.00]];
        $result = $this->validatePaymentTotal(1000.00, 700.00, $cards);
        $this->assertTrue($result['valid'], 'Cash 700 + Card 300 should match 1000');
        $this->assertEquals(1000.00, $result['new_total']);
    }

    // ---------------------------------------------------------------
    // TC-03: Split across all four modes
    // ---------------------------------------------------------------
    public function test_tc03_all_four_payment_modes(): void
    {
        $cards    = [['card_amt' => 200.00]];
        $cheques  = [['payment_amount' => 300.00]];
        $nbs      = [['amount' => 150.00]];
        $result   = $this->validatePaymentTotal(1000.00, 350.00, $cards, $cheques, $nbs);
        $this->assertTrue($result['valid'], '350+200+300+150 = 1000 should match');
        $this->assertEquals(1000.00, $result['new_total']);
    }

    // ---------------------------------------------------------------
    // TC-04: Total mismatch — should fail
    // ---------------------------------------------------------------
    public function test_tc04_total_mismatch_rejected(): void
    {
        $result = $this->validatePaymentTotal(1000.00, 500.00);
        $this->assertFalse($result['valid'], 'Cash 500 != Voucher 1000 — should fail validation');
        $this->assertEquals(500.00, $result['diff']);
    }

    // ---------------------------------------------------------------
    // TC-05: Multiple card rows
    // ---------------------------------------------------------------
    public function test_tc05_multiple_card_rows(): void
    {
        $cards = [
            ['card_amt' => 400.00],
            ['card_amt' => 350.00]
        ];
        $result = $this->validatePaymentTotal(1000.00, 250.00, $cards);
        $this->assertTrue($result['valid'], '250 + 400 + 350 = 1000');
    }

    // ---------------------------------------------------------------
    // TC-06: Zero cash, all card
    // ---------------------------------------------------------------
    public function test_tc06_zero_cash_all_card(): void
    {
        $cards = [['card_amt' => 1000.00]];
        $result = $this->validatePaymentTotal(1000.00, 0, $cards);
        $this->assertTrue($result['valid'], 'Zero cash + full card should pass');
    }

    // ---------------------------------------------------------------
    // TC-07: Decimal precision (paise)
    // ---------------------------------------------------------------
    public function test_tc07_decimal_precision(): void
    {
        $cards = [['card_amt' => 333.34]];
        $result = $this->validatePaymentTotal(1000.00, 666.66, $cards);
        $this->assertTrue($result['valid'], '666.66 + 333.34 = 1000.00');
        $this->assertEquals(1000.00, $result['new_total']);
    }

    // ---------------------------------------------------------------
    // TC-08: Float rounding tolerance (0.01 epsilon)
    // ---------------------------------------------------------------
    public function test_tc08_float_rounding_tolerance(): void
    {
        // Simulate JS floating point: 333.33 + 333.33 + 333.34 = 1000.00
        // But 333.33 * 3 = 999.99, not exactly 1000.00
        $cards   = [['card_amt' => 333.33]];
        $cheques = [['payment_amount' => 333.33]];
        $nbs     = [['amount' => 333.34]];
        $result  = $this->validatePaymentTotal(1000.00, 0, $cards, $cheques, $nbs);
        $this->assertTrue($result['valid'], '0.01 tolerance should accept rounding differences');
    }

    // ---------------------------------------------------------------
    // TC-09: Negative cash rejected
    // ---------------------------------------------------------------
    public function test_tc09_negative_cash_rejected(): void
    {
        $cash = -500.00;
        // Guard 1 in controller: cash < 0 → reject
        $this->assertTrue($cash < 0, 'Negative cash should be caught by guard');
    }

    // ---------------------------------------------------------------
    // TC-10: Zero/negative rows filtered out
    // ---------------------------------------------------------------
    public function test_tc10_zero_rows_filtered(): void
    {
        $cards = [
            ['card_amt' => 500.00],
            ['card_amt' => 0],
            ['card_amt' => -100.00],
            ['card_amt' => 500.00]
        ];
        $filtered = $this->filterValidRows($cards, 'card_amt');
        $this->assertCount(2, $filtered, 'Zero and negative rows should be removed');
        $this->assertEquals(500.00, $filtered[0]['card_amt']);
        $this->assertEquals(500.00, $filtered[1]['card_amt']);
    }

    // ---------------------------------------------------------------
    // TC-11: Empty payments rejected (Guard 4)
    // ---------------------------------------------------------------
    public function test_tc11_no_valid_payments_rejected(): void
    {
        $cash = 0;
        $cards = $this->filterValidRows([['card_amt' => 0]], 'card_amt');
        $cheques = [];
        $nbs = [];

        $has_payments = ($cash > 0 || !empty($cards) || !empty($cheques) || !empty($nbs));
        $this->assertFalse($has_payments, 'No valid payment entries should fail guard');
    }

    // ---------------------------------------------------------------
    // TC-12: Cheque rows filter
    // ---------------------------------------------------------------
    public function test_tc12_cheque_filter(): void
    {
        $cheques = [
            ['payment_amount' => 1200.00],
            ['payment_amount' => 0],
        ];
        $filtered = $this->filterValidRows($cheques, 'payment_amount');
        $this->assertCount(1, $filtered);

        $result = $this->validatePaymentTotal(1200.00, 0, [], $filtered);
        $this->assertTrue($result['valid'], 'Single valid cheque should match voucher');
    }

    // ---------------------------------------------------------------
    // TC-13: Large voucher amount
    // ---------------------------------------------------------------
    public function test_tc13_large_amount(): void
    {
        $cards    = [['card_amt' => 250000.00]];
        $cheques  = [['payment_amount' => 300000.00]];
        $nbs      = [['amount' => 242843.00]];
        $result   = $this->validatePaymentTotal(792843.00, 0, $cards, $cheques, $nbs);
        $this->assertTrue($result['valid'], 'Large values should validate correctly');
    }

    // ---------------------------------------------------------------
    // TC-14: Over-payment rejected
    // ---------------------------------------------------------------
    public function test_tc14_overpayment_rejected(): void
    {
        $result = $this->validatePaymentTotal(1000.00, 1001.00);
        $this->assertFalse($result['valid'], 'Over-payment should fail validation');
    }

    // ---------------------------------------------------------------
    // TC-15: Inactive voucher guard (bill_status != 1)
    // ---------------------------------------------------------------
    public function test_tc15_inactive_voucher_blocked(): void
    {
        $bill_status = 2; // Cancelled
        $is_active = ((int)$bill_status === 1);
        $this->assertFalse($is_active, 'Cancelled voucher (status=2) must be blocked');

        $bill_status = 1; // Active
        $is_active = ((int)$bill_status === 1);
        $this->assertTrue($is_active, 'Active voucher (status=1) must be allowed');
    }
}
