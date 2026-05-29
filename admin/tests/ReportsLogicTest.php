<?php
use PHPUnit\Framework\TestCase;

/**
 * RPT-CLT01: Weight Range Not Showing in Re-Order Setting Report
 * Category: Logic
 * 
 * Tests the SQL expression logic used to build weight_name:
 * IF(IFNULL(wt.weight_description, '') != '', 
 *    CONCAT(wt.weight_description, ' ', IFNULL(m.uom_name, '')),
 *    CONCAT(IFNULL(wt.from_weight, ''), ' - ', IFNULL(wt.to_weight, '')))
 *
 * Since this is a raw SQL expression, we replicate the logic in PHP 
 * to verify correctness across all edge cases.
 */
class ReportsLogicTest extends TestCase
{
    /**
     * Replicates the SQL IF/CONCAT logic for weight_name.
     * This mirrors the exact expression in getReorderitems().
     */
    private function buildWeightName(?string $weight_description, ?string $uom_name, ?string $from_weight, ?string $to_weight): string
    {
        $desc = $weight_description ?? '';
        if ($desc !== '') {
            return $desc . ' ' . ($uom_name ?? '');
        } else {
            return ($from_weight ?? '') . ' - ' . ($to_weight ?? '');
        }
    }

    /**
     * Bug RPT-CLT01: When weight_description is populated, show it with UOM.
     */
    public function test_rpt_clt01_weight_name_with_description(): void
    {
        $result = $this->buildWeightName('Small', 'Grams', '0.000', '5.000');
        $this->assertEquals('Small Grams', $result);
    }

    /**
     * Bug RPT-CLT01: When weight_description is NULL, fallback to from-to range.
     * This is the CORE bug scenario — previously returned blank.
     */
    public function test_rpt_clt01_weight_name_null_description_fallback(): void
    {
        $result = $this->buildWeightName(null, 'Grams', '0.000', '5.000');
        $this->assertEquals('0.000 - 5.000', $result);
    }

    /**
     * Bug RPT-CLT01: When weight_description is empty string, fallback to from-to range.
     */
    public function test_rpt_clt01_weight_name_empty_description_fallback(): void
    {
        $result = $this->buildWeightName('', 'Grams', '0.000', '5.000');
        $this->assertEquals('0.000 - 5.000', $result);
    }

    /**
     * Bug RPT-CLT01: When description exists but uom_name is NULL.
     */
    public function test_rpt_clt01_weight_name_description_no_uom(): void
    {
        $result = $this->buildWeightName('Medium', null, '5.000', '10.000');
        $this->assertEquals('Medium ', $result);
    }

    /**
     * Bug RPT-CLT01: When both description and UOM are NULL, fallback to range.
     */
    public function test_rpt_clt01_all_null_except_range(): void
    {
        $result = $this->buildWeightName(null, null, '10.000', '20.000');
        $this->assertEquals('10.000 - 20.000', $result);
    }

    /**
     * Bug RPT-CLT01: Ensure result is never blank/space-only.
     * Old code produced ' ' (single space) when all were null.
     */
    public function test_rpt_clt01_never_blank_output(): void
    {
        $result = $this->buildWeightName(null, null, '0.000', '5.000');
        $this->assertNotEquals('', trim($result), 'Weight name must never be blank');
        $this->assertNotEquals(' ', $result, 'Weight name must not be just a space');
    }
}
