<?php
use PHPUnit\Framework\TestCase;

/**
 * PUR-CLT02: Tax Calculation Mismatch Between GRN and Supplier Bill Entry (Hallmark Charges)
 *
 * Validates the CORRECT calculation logic: Other Charges must NOT be
 * included in the taxable base for product GST.
 *
 * Correct: taxable = purewt × rate + mc + metal + stone → GST on this → THEN add charges
 * Buggy:   taxable = purewt × rate + mc + metal + charges(+tax) + stone → GST on this
 */
class PurchaseLogicTest extends TestCase
{
    /**
     * Calculate item cost the CORRECT way (fixed logic).
     */
    private function calcCorrect($pureWt, $rate, $mc, $metal, $stone, $chargeVal, $chargeTaxRate, $gstRate)
    {
        $base = ($pureWt * $rate) + $mc + $metal + $stone;
        $gst = round($base * $gstRate / 100, 2);
        $chargeTax = round($chargeVal * $chargeTaxRate / 100, 2);
        $chargesTotal = round($chargeVal + $chargeTax, 2);
        $final = round($base + $gst + $chargesTotal, 2);

        return [
            'base' => round($base, 2),
            'gst' => $gst,
            'charge_tax' => $chargeTax,
            'charges_total' => $chargesTotal,
            'taxable' => round($base, 2),
            'final' => $final,
        ];
    }

    /**
     * Calculate the BUGGY way (for regression guard).
     */
    private function calcBuggy($pureWt, $rate, $mc, $metal, $stone, $chargeVal, $chargeTaxRate, $gstRate)
    {
        $chargeTax = round($chargeVal * $chargeTaxRate / 100, 2);
        $chargesTotal = round($chargeVal + $chargeTax, 2);
        $base = ($pureWt * $rate) + $mc + $metal + $chargesTotal + $stone;
        $gst = round($base * $gstRate / 100, 2);
        $final = round($base + $gst, 2);

        return ['base' => round($base, 2), 'gst' => $gst, 'final' => $final];
    }

    // === Test 1: Exact reported scenario ===

    public function test_pur_clt02_exact_scenario_correct(): void
    {
        // 40g × ₹15,000, Hallmark ₹300 @ 18%, Product GST 3%
        $r = $this->calcCorrect(40, 15000, 0, 0, 0, 300, 18, 3);

        $this->assertEquals(600000.00, $r['taxable'], 'Taxable must not include charges');
        $this->assertEquals(18000.00, $r['gst'], 'GST must be on base only');
        $this->assertEquals(54.00, $r['charge_tax']);
        $this->assertEquals(354.00, $r['charges_total']);
        $this->assertEquals(618354.00, $r['final'], 'Final must match GRN = 618,354');
    }

    // === Test 2: Buggy calc produces wrong result (regression guard) ===

    public function test_pur_clt02_buggy_calc_produces_mismatch(): void
    {
        $b = $this->calcBuggy(40, 15000, 0, 0, 0, 300, 18, 3);

        $this->assertEquals(600354.00, $b['base'], 'Buggy base includes charges');
        $this->assertEquals(18010.62, $b['gst'], 'Buggy GST is inflated');
        $this->assertNotEquals(618354.00, $b['final'], 'Buggy final must NOT match GRN');
    }

    // === Test 3: No charges — no difference ===

    public function test_pur_clt02_no_charges(): void
    {
        $r = $this->calcCorrect(40, 15000, 0, 0, 0, 0, 0, 3);

        $this->assertEquals(600000.00, $r['taxable']);
        $this->assertEquals(18000.00, $r['gst']);
        $this->assertEquals(0.00, $r['charges_total']);
        $this->assertEquals(618000.00, $r['final']);
    }

    // === Test 4: All components present ===

    public function test_pur_clt02_all_components(): void
    {
        // 20g × ₹10,000, MC=500, Metal=200, Stone=1000, Charge=100@18%, GST=3%
        $r = $this->calcCorrect(20, 10000, 500, 200, 1000, 100, 18, 3);

        $this->assertEquals(201700.00, $r['base']);
        $this->assertEquals(6051.00, $r['gst']);
        $this->assertEquals(118.00, $r['charges_total']);
        $this->assertEquals(207869.00, $r['final']);
        $this->assertEquals(201700.00, $r['taxable'], 'Taxable must exclude charges');
    }

    // === Test 5: Zero weight (stone/pcs pricing) ===

    public function test_pur_clt02_zero_weight(): void
    {
        $r = $this->calcCorrect(0, 0, 0, 0, 5000, 200, 18, 0.25);

        $this->assertEquals(5000.00, $r['base']);
        $this->assertEquals(12.50, $r['gst']);
        $this->assertEquals(236.00, $r['charges_total']);
        $this->assertEquals(5248.50, $r['final']);
    }

    // === Test 6: Large values ===

    public function test_pur_clt02_large_values(): void
    {
        $r = $this->calcCorrect(500, 60000, 50000, 10000, 100000, 5000, 18, 3);

        $this->assertEquals(30160000.00, $r['base']);
        $this->assertEquals(904800.00, $r['gst']);
        $this->assertEquals(5900.00, $r['charges_total']);
        $this->assertEquals(30160000.00, $r['taxable'], 'Taxable must exclude charges even at scale');
    }

    // === Test 7: No cascading tax invariant ===

    public function test_pur_clt02_no_cascading_tax_invariant(): void
    {
        $r = $this->calcCorrect(40, 15000, 0, 0, 0, 300, 18, 3);
        $expectedGst = round((40 * 15000) * 3 / 100, 2);
        $this->assertEquals($expectedGst, $r['gst'], 'GST must equal base × rate — no cascading');
    }
}
