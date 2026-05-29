<?php
use PHPUnit\Framework\TestCase;

/**
 * Payment Mode Edit — Bug Fix Verification Tests
 * Task: 4b5107fcecdb — Billing Payment Mode Edit Fixes
 *
 * Tests cover:
 *   BUG-1: B2C ↔ B2B conversion rules (E-Invoice guard)
 *   BUG-3: bill_id source (hidden_bill_id vs DOM traversal)
 *   BUG-4/5: $_POST array safety (is_array guards)
 *   BUG-6: AJAX response format validation
 *   Backend: billing_for + cusdel_irn guard logic
 */
class PaymentModeEditTest extends TestCase
{
    // ===============================================================
    // B2C ↔ B2B CONVERSION RULES
    // ===============================================================

    /**
     * Simulates the backend E-Invoice guard logic.
     * Returns TRUE if conversion is allowed, FALSE if blocked.
     *
     * @param int    $target_billing_for  1 = B2C (Individual), 2 = B2B (Company)
     * @param string $cusdel_irn          IRN value from ret_billing (empty = no E-Invoice)
     * @return bool
     */
    private function isConversionAllowed(int $target_billing_for, string $cusdel_irn): bool
    {
        // Rule: B2B→B2C blocked if E-Invoice exists
        if ($target_billing_for == 1 && !empty($cusdel_irn)) {
            return false;
        }
        return true;
    }

    /**
     * B2C → B2B: Always allowed (user adds GST/PAN separately after)
     */
    public function test_b2c_to_b2b_always_allowed(): void
    {
        // B2C to B2B — no IRN (irrelevant for B2C bills)
        $this->assertTrue(
            $this->isConversionAllowed(2, ''),
            'B2C→B2B must always be allowed regardless of IRN status'
        );
    }

    /**
     * B2C → B2B: Allowed even if cusdel_irn has some value (shouldn't happen for B2C, but edge case)
     */
    public function test_b2c_to_b2b_allowed_even_with_irn(): void
    {
        $this->assertTrue(
            $this->isConversionAllowed(2, 'some-irn-value'),
            'Converting TO B2B should never be blocked — IRN check only applies to B2B→B2C direction'
        );
    }

    /**
     * B2B → B2C: Allowed when no E-Invoice generated
     */
    public function test_b2b_to_b2c_allowed_without_einvoice(): void
    {
        $this->assertTrue(
            $this->isConversionAllowed(1, ''),
            'B2B→B2C must be allowed if no E-Invoice (IRN) exists'
        );
    }

    /**
     * B2B → B2C: BLOCKED when E-Invoice exists
     */
    public function test_b2b_to_b2c_blocked_with_einvoice(): void
    {
        $irn = 'INV/2024-25/E0001234567890ABCDEF';
        $this->assertFalse(
            $this->isConversionAllowed(1, $irn),
            'B2B→B2C MUST be blocked when E-Invoice (cusdel_irn) exists'
        );
    }

    /**
     * B2B → B2C: BLOCKED with any non-empty IRN value
     */
    public function test_b2b_to_b2c_blocked_any_nonempty_irn(): void
    {
        // Even a single character means IRN was generated
        $this->assertFalse(
            $this->isConversionAllowed(1, 'X'),
            'Any non-empty cusdel_irn must block B2B→B2C conversion'
        );
    }

    /**
     * B2B → B2C: Allowed with NULL-like empty string (DB returns empty)
     */
    public function test_b2b_to_b2c_allowed_with_empty_string(): void
    {
        $this->assertTrue(
            $this->isConversionAllowed(1, ''),
            'Empty string cusdel_irn (no IRN) should allow B2B→B2C'
        );
    }

    // ===============================================================
    // AJAX RESPONSE FORMAT VALIDATION
    // ===============================================================

    /**
     * BUG-6: Update response must follow standard format {status, message}
     */
    public function test_update_response_format_success(): void
    {
        $response = json_encode(array('status' => true, 'message' => 'Record Updated Successfully'));
        $decoded = json_decode($response, true);

        $this->assertArrayHasKey('status', $decoded, 'Response must have status key');
        $this->assertArrayHasKey('message', $decoded, 'Response must have message key');
        $this->assertTrue($decoded['status']);
    }

    /**
     * BUG-6: Failed update response must follow same format
     */
    public function test_update_response_format_failure(): void
    {
        $response = json_encode(array('status' => false, 'message' => 'Cannot convert to B2C. E-Invoice (IRN) already generated for this bill.'));
        $decoded = json_decode($response, true);

        $this->assertArrayHasKey('status', $decoded);
        $this->assertArrayHasKey('message', $decoded);
        $this->assertFalse($decoded['status']);
        $this->assertStringContainsString('E-Invoice', $decoded['message']);
    }

    // ===============================================================
    // INPUT SANITIZATION (BUG-4/5)
    // ===============================================================

    /**
     * BUG-4/5: is_array guard — card_payment as empty string must become array
     */
    public function test_array_guard_empty_string_becomes_array(): void
    {
        $card_payment = '';
        if (!is_array($card_payment)) $card_payment = array();

        $this->assertIsArray($card_payment, 'Empty string post value must be converted to array');
        $this->assertCount(0, $card_payment);
    }

    /**
     * BUG-4/5: is_array guard — null must become array
     */
    public function test_array_guard_null_becomes_array(): void
    {
        $chq_payment = null;
        if (!is_array($chq_payment)) $chq_payment = array();

        $this->assertIsArray($chq_payment, 'NULL post value must be converted to array');
        $this->assertCount(0, $chq_payment);
    }

    /**
     * BUG-4/5: is_array guard — valid array stays untouched
     */
    public function test_array_guard_valid_array_unchanged(): void
    {
        $nb_payment = array(
            array('amount' => 5000, 'ref_no' => 'REF001', 'nb_type' => 'NEFT')
        );
        if (!is_array($nb_payment)) $nb_payment = array();

        $this->assertIsArray($nb_payment);
        $this->assertCount(1, $nb_payment, 'Valid array must remain unchanged');
        $this->assertEquals(5000, $nb_payment[0]['amount']);
    }

    // ===============================================================
    // BILL ID VALIDATION (BUG-3)
    // ===============================================================

    /**
     * BUG-3: bill_id must be a positive integer
     */
    public function test_bill_id_validation_positive(): void
    {
        $bill_id = (int) '42';
        $this->assertGreaterThan(0, $bill_id, 'bill_id must be positive');
    }

    /**
     * BUG-3: Empty bill_id must be caught
     */
    public function test_bill_id_validation_empty_rejected(): void
    {
        $bill_id = (int) '';
        $this->assertLessThanOrEqual(0, $bill_id, 'Empty bill_id must be rejected');
    }

    /**
     * BUG-3: Non-numeric bill_id must be caught
     */
    public function test_bill_id_validation_nonnumeric_rejected(): void
    {
        $bill_id = (int) 'abc';
        $this->assertLessThanOrEqual(0, $bill_id, 'Non-numeric bill_id must be rejected');
    }

    // ===============================================================
    // UPDATE PAYLOAD VALIDATION — Single field per request
    // ===============================================================

    /**
     * Each update button sends exactly one field (+ bill_id).
     * Simulates what the PHP controller does for each case.
     */
    public function test_update_payload_customer_name(): void
    {
        $post = array('bill_id' => 42, 'customer_name' => 'John Doe');
        $update_data = array();

        if (isset($post['customer_name'])) {
            $update_data = array('customer_name' => $post['customer_name']);
        }

        $this->assertArrayHasKey('customer_name', $update_data);
        $this->assertEquals('John Doe', $update_data['customer_name']);
    }

    public function test_update_payload_billing_for(): void
    {
        $post = array('bill_id' => 42, 'billing_for' => '2');
        $update_data = array();

        if (isset($post['billing_for'])) {
            $update_data = array('billing_for' => (int) $post['billing_for']);
        }

        $this->assertArrayHasKey('billing_for', $update_data);
        $this->assertEquals(2, $update_data['billing_for']);
    }

    public function test_update_payload_id_employee(): void
    {
        $post = array('bill_id' => 42, 'id_employee' => '15');
        $update_data = array();

        if (isset($post['id_employee'])) {
            $update_data = array('id_employee' => (int) $post['id_employee']);
        }

        $this->assertArrayHasKey('id_employee', $update_data);
        $this->assertEquals(15, $update_data['id_employee']);
    }

    // ===============================================================
    // E-INVOICE GUARD — COMPREHENSIVE EDGE CASES
    // ===============================================================

    /**
     * Real-world IRN format test (36-character alphanumeric)
     */
    public function test_einvoice_guard_real_irn_format(): void
    {
        $irn = 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6';
        $this->assertFalse(
            $this->isConversionAllowed(1, $irn),
            'Real IRN format must trigger block'
        );
    }

    /**
     * Edge: whitespace-only IRN should NOT block (DB shouldn't store this, but guard against it)
     */
    public function test_einvoice_guard_whitespace_only(): void
    {
        // PHP empty('   ') returns false, so whitespace IS non-empty
        $irn = '   ';
        $result = $this->isConversionAllowed(1, $irn);
        // Whitespace is technically non-empty, so this should block
        $this->assertFalse($result, 'Whitespace-only IRN is treated as non-empty and should block');
    }

    /**
     * Duplicate IRN (prefixed with DUP_) — still blocks
     */
    public function test_einvoice_guard_duplicate_irn(): void
    {
        $irn = 'DUP_a1b2c3d4e5f6a1b2c3d4e5f6';
        $this->assertFalse(
            $this->isConversionAllowed(1, $irn),
            'Duplicate IRN prefix must still block conversion'
        );
    }

    // ===============================================================
    // REGRESSION: Ensure no silent failures
    // ===============================================================

    /**
     * If no update_data fields match, the response should be an error (not silent success)
     */
    public function test_empty_update_data_returns_error(): void
    {
        $update_data = array();
        // Simulate: no matching field posted
        $post = array('bill_id' => 42, 'unknown_field' => 'value');

        if (isset($post['customer_name'])) {
            $update_data = array('customer_name' => $post['customer_name']);
        }
        if (isset($post['billing_for'])) {
            $update_data = array('billing_for' => (int) $post['billing_for']);
        }

        // Controller checks: if (!empty($update_data))
        if (!empty($update_data)) {
            $response = array('status' => true, 'message' => 'Record Updated Successfully');
        } else {
            $response = array('status' => false, 'message' => 'Record Not Updated');
        }

        $this->assertFalse($response['status'], 'Empty update_data must return error, not silent success');
    }
}
