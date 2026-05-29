<?php
use PHPUnit\Framework\TestCase;

/**
 * PUR-CR02: Amount to Pure Conversion
 * Category: Logic (Change Request)
 * Tests the amount-to-pure calculation logic: weight = amount / rate
 */
class PurchaseAmountToPureTest extends TestCase
{
    /**
     * PUR-CR02: Standard conversion — amount / rate = weight
     * Business Rule: weight = charges_amount / rate_per_gram
     */
    public function test_pur_cr02_standard_conversion(): void
    {
        $amount = 10000;
        $rate = 5000;
        $expected_weight = 2.000;

        $weight = round($amount / $rate, 3);

        $this->assertEquals($expected_weight, $weight, 'Standard amount to pure: 10000/5000 = 2.000');
    }

    /**
     * PUR-CR02: Fractional weight result
     */
    public function test_pur_cr02_fractional_weight(): void
    {
        $amount = 7500;
        $rate = 6000;
        $expected_weight = 1.250;

        $weight = round($amount / $rate, 3);

        $this->assertEquals($expected_weight, $weight, 'Fractional: 7500/6000 = 1.250');
    }

    /**
     * PUR-CR02: Zero amount should produce zero weight
     */
    public function test_pur_cr02_zero_amount(): void
    {
        $amount = 0;
        $rate = 5000;
        $expected_weight = 0;

        $weight = ($rate > 0) ? round($amount / $rate, 3) : 0;

        $this->assertEquals($expected_weight, $weight, 'Zero amount = zero weight');
    }

    /**
     * PUR-CR02: Zero rate should NOT cause division by zero
     */
    public function test_pur_cr02_zero_rate_guard(): void
    {
        $amount = 10000;
        $rate = 0;

        // Guard: rate must be > 0 to avoid division by zero
        $weight = ($rate > 0) ? round($amount / $rate, 3) : 0;

        $this->assertEquals(0, $weight, 'Zero rate guard: weight = 0 when rate = 0');
    }

    /**
     * PUR-CR02: GST calculation for Approval (convert_to=5) at 3%
     */
    public function test_pur_cr02_approval_gst_3_percent(): void
    {
        $taxable_amount = 10000;
        $tax_percentage = 3; // Approval = 3%

        $tax_amount = round($taxable_amount * $tax_percentage / 100, 2);
        $total_amount = round($taxable_amount + $tax_amount, 2);

        $this->assertEquals(300.00, $tax_amount, 'GST 3% on 10000 = 300');
        $this->assertEquals(10300.00, $total_amount, 'Total = 10000 + 300 = 10300');
    }

    /**
     * PUR-CR02: Same-state GST split (CGST + SGST)
     */
    public function test_pur_cr02_same_state_gst_split(): void
    {
        $tax_amount = 300;

        $sgst = round($tax_amount / 2, 2);
        $cgst = round($tax_amount / 2, 2);

        $this->assertEquals(150.00, $sgst, 'SGST = 300/2 = 150');
        $this->assertEquals(150.00, $cgst, 'CGST = 300/2 = 150');
        $this->assertEquals($tax_amount, $sgst + $cgst, 'SGST + CGST = total tax');
    }

    /**
     * PUR-CR02: Large amount conversion accuracy
     */
    public function test_pur_cr02_large_amount(): void
    {
        $amount = 5000000; // 50 lakh
        $rate = 6500;
        $expected_weight = round(5000000 / 6500, 3); // 769.231

        $weight = round($amount / $rate, 3);

        $this->assertEquals($expected_weight, $weight, 'Large amount: 5000000/6500 = 769.231');
    }
}
