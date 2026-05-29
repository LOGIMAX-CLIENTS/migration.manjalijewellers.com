<?php
use PHPUnit\Framework\TestCase;

/**
 * PUR-INT02: Duplicate Total Value Display in Supplier Payment Reports
 * Category: Database / Query Logic
 *
 * Tests that the get_po_payments() query returns per-PO allocation amounts
 * from po_bill.bill_amount instead of duplicating d.payment_amount across PO rows.
 *
 * NOTE: These are unit tests that verify the SQL structure by parsing the
 * generated query string. They do NOT require a database connection.
 */
class PurchaseQueryLogicTest extends TestCase
{
    /**
     * Bug PUR-INT02: Verify the fix uses po_bill.bill_amount instead of d.payment_amount
     * Tests that the SELECT clause references po_bill.bill_amount for per-PO allocation.
     */
    public function test_pur_int02_query_uses_bill_amount_not_payment_amount(): void
    {
        // Read the actual model file to check the query structure
        $modelPath = __DIR__ . '/../application/models/ret_reports_model.php';
        $this->assertFileExists($modelPath, 'ret_reports_model.php must exist');

        $content = file_get_contents($modelPath);

        // Find the get_po_payments function
        $funcStart = strpos($content, 'function get_po_payments($data)');
        $this->assertNotFalse($funcStart, 'get_po_payments() function must exist in ret_reports_model.php');

        // Extract the function body (up to the next function or closing brace pattern)
        $funcBody = substr($content, $funcStart, 3000); // first 3000 chars should cover the query

        // The SELECT clause should use po_bill.bill_amount for payment_amount (the fix)
        $this->assertStringContainsString(
            'po_bill.bill_amount',
            $funcBody,
            'PUR-INT02 FIX: get_po_payments() must use po_bill.bill_amount for per-PO allocation instead of d.payment_amount'
        );
    }

    /**
     * Bug PUR-INT02: Verify the purewt calculation also uses po_bill.bill_amount
     * The pure weight calculation must use the per-PO amount, not the full payment total.
     */
    public function test_pur_int02_purewt_uses_bill_amount(): void
    {
        $modelPath = __DIR__ . '/../application/models/ret_reports_model.php';
        $content = file_get_contents($modelPath);

        $funcStart = strpos($content, 'function get_po_payments($data)');
        $funcBody = substr($content, $funcStart, 3000);

        // The purewt calculation should reference po_bill.bill_amount, not just d.payment_amount
        // Pattern: ROUND(IFNULL(po_bill.bill_amount, d.payment_amount) / NULLIF(...)
        $this->assertStringContainsString(
            'IFNULL(po_bill.bill_amount, d.payment_amount)',
            $funcBody,
            'PUR-INT02 FIX: purewt calculation must use IFNULL(po_bill.bill_amount, d.payment_amount) for correct per-PO weight conversion'
        );
    }

    /**
     * Bug PUR-INT02: Verify the query still joins ret_po_bill_payment_details
     * The JOIN structure must be preserved — each PO should show as a separate row.
     */
    public function test_pur_int02_preserves_po_bill_join(): void
    {
        $modelPath = __DIR__ . '/../application/models/ret_reports_model.php';
        $content = file_get_contents($modelPath);

        $funcStart = strpos($content, 'function get_po_payments($data)');
        $funcBody = substr($content, $funcStart, 3000);

        // Must still JOIN ret_po_bill_payment_details for individual PO rows
        $this->assertStringContainsString(
            'ret_po_bill_payment_details',
            $funcBody,
            'get_po_payments() must JOIN ret_po_bill_payment_details for per-PO row display'
        );

        // Must still JOIN ret_purchase_order to get PO details
        $this->assertStringContainsString(
            'ret_purchase_order po',
            $funcBody,
            'get_po_payments() must JOIN ret_purchase_order for PO reference numbers'
        );
    }

    /**
     * Bug PUR-INT02: Verify fallback — advance payments without PO allocation
     * The IFNULL pattern ensures d.payment_amount is used as fallback when po_bill.bill_amount is NULL.
     */
    public function test_pur_int02_fallback_to_payment_amount(): void
    {
        $modelPath = __DIR__ . '/../application/models/ret_reports_model.php';
        $content = file_get_contents($modelPath);

        $funcStart = strpos($content, 'function get_po_payments($data)');
        $funcBody = substr($content, $funcStart, 3000);

        // Count occurrences of IFNULL(po_bill.bill_amount, d.payment_amount)
        // Should appear at least twice: once for payment_amount alias, once for purewt calc
        $count = substr_count($funcBody, 'IFNULL(po_bill.bill_amount, d.payment_amount)');
        $this->assertGreaterThanOrEqual(
            2,
            $count,
            'PUR-INT02 FIX: IFNULL(po_bill.bill_amount, d.payment_amount) must appear at least twice — once for payment_amount and once for purewt calculation'
        );
    }
}
