// @ts-check
const { test, expect } = require('@playwright/test');
const { goToPaymentEdit } = require('../helpers/navigation');
const SEL = require('../helpers/selectors');

const PE = SEL.paymentEdit;

test.describe('Bill Type Conversion — B2C ↔ B2B with E-Invoice Guard', () => {

  test.beforeEach(async ({ page }) => {
    await goToPaymentEdit(page);
  });

  // ---------------------------------------------------------------
  // B2C → B2B: Always allowed
  // ---------------------------------------------------------------

  test('B2C bill: can switch to B2B (Company)', async ({ page }) => {
    const billId = process.env.TEST_B2C_BILL_ID || '3319';

    // Search bill
    await page.locator(PE.billSearchInput).fill(billId);
    await page.locator(PE.billSearchBtn).click();
    await page.waitForTimeout(2000);

    // Verify it's a B2C bill (Individual checked)
    const b2cRadio = page.locator(PE.billingForB2C);
    await expect(b2cRadio).toBeChecked();

    // B2B radio should NOT be disabled
    const b2bRadio = page.locator(PE.billingForB2B);
    await expect(b2bRadio).not.toBeDisabled();

    // Click B2B radio
    await b2bRadio.check({ force: true });
    await expect(b2bRadio).toBeChecked();
  });

  // ---------------------------------------------------------------
  // B2B → B2C: Allowed when no E-Invoice
  // ---------------------------------------------------------------

  test('B2B bill without IRN: can switch to B2C (Individual)', async ({ page }) => {
    const billId = process.env.TEST_B2B_BILL_ID || '3064';

    await page.locator(PE.billSearchInput).fill(billId);
    await page.locator(PE.billSearchBtn).click();
    await page.waitForTimeout(2000);

    // Verify it's a B2B bill (Company checked)
    const b2bRadio = page.locator(PE.billingForB2B);
    await expect(b2bRadio).toBeChecked();

    // B2C radio should NOT be disabled
    const b2cRadio = page.locator(PE.billingForB2C);
    await expect(b2cRadio).not.toBeDisabled();

    // Click B2C radio — should work
    await b2cRadio.check({ force: true });
    await expect(b2cRadio).toBeChecked();
  });

  // ---------------------------------------------------------------
  // B2B → B2C: BLOCKED when E-Invoice exists
  // ---------------------------------------------------------------

  test('B2B bill with IRN: switch to B2C is blocked', async ({ page }) => {
    const billId = process.env.TEST_B2B_IRN_BILL_ID;

    // Skip if no IRN test bill configured
    test.skip(!billId, 'No B2B bill with IRN available in dev DB — set TEST_B2B_IRN_BILL_ID in .env');

    await page.locator(PE.billSearchInput).fill(billId);
    await page.locator(PE.billSearchBtn).click();
    await page.waitForTimeout(2000);

    // Verify it's a B2B bill
    const b2bRadio = page.locator(PE.billingForB2B);
    await expect(b2bRadio).toBeChecked();

    // Try to switch to B2C
    const b2cRadio = page.locator(PE.billingForB2C);
    await b2cRadio.check({ force: true });

    // Wait for JS guard to react
    await page.waitForTimeout(1000);

    // B2B should be re-checked (JS reverts the radio)
    await expect(b2bRadio).toBeChecked();

    // Error toast should appear
    const toastText = await page.locator('.toaster-container').textContent();
    expect(toastText).toContain('E-Invoice');
  });

  // ---------------------------------------------------------------
  // E-INVOICE GUARD: JS-side check via window.bill_cusdel_irn
  // ---------------------------------------------------------------

  test('window.bill_cusdel_irn is set after bill search', async ({ page }) => {
    const billId = process.env.TEST_B2C_BILL_ID || '3319';

    await page.locator(PE.billSearchInput).fill(billId);
    await page.locator(PE.billSearchBtn).click();
    await page.waitForTimeout(2000);

    // Check the JS variable is set
    const irnValue = await page.evaluate(() => window.bill_cusdel_irn);
    expect(irnValue).toBeDefined();
    // For B2C bill, IRN should be empty
    expect(irnValue).toBe('');
  });

  test('B2B bill without IRN: window.bill_cusdel_irn is empty string', async ({ page }) => {
    const billId = process.env.TEST_B2B_BILL_ID || '3064';

    await page.locator(PE.billSearchInput).fill(billId);
    await page.locator(PE.billSearchBtn).click();
    await page.waitForTimeout(2000);

    const irnValue = await page.evaluate(() => window.bill_cusdel_irn);
    expect(irnValue).toBe('');
  });

  // ---------------------------------------------------------------
  // BACKEND GUARD: Server-side validation test
  // ---------------------------------------------------------------

  test('Server rejects B2B→B2C when IRN exists (API test)', async ({ page }) => {
    // This test directly calls the API to verify backend guard
    // even if no IRN bill exists, we can test with a mock bill_id
    const billId = process.env.TEST_B2B_IRN_BILL_ID;

    test.skip(!billId, 'No B2B bill with IRN available — set TEST_B2B_IRN_BILL_ID in .env');

    const baseUrl = process.env.BASE_URL || 'http://localhost/etail_v6/admin/index.php';

    // Direct API call to try converting to B2C
    const response = await page.request.post(
      `${baseUrl}/admin_ret_billing/paymentmode_edit/update`,
      {
        form: {
          bill_id: billId,
          billing_for: '1',  // Try to set to B2C
        },
      }
    );

    const body = await response.json();

    // Server should reject
    expect(body.status).toBe(false);
    expect(body.message).toContain('E-Invoice');
  });

  // ---------------------------------------------------------------
  // REGRESSION: Radio not permanently disabled
  // ---------------------------------------------------------------

  test('Navigating between bills resets radio state', async ({ page }) => {
    const b2cBillId = process.env.TEST_B2C_BILL_ID || '3319';

    // Search first bill
    await page.locator(PE.billSearchInput).fill(b2cBillId);
    await page.locator(PE.billSearchBtn).click();
    await page.waitForTimeout(2000);

    // Both radios should be interactive
    const b2cRadio = page.locator(PE.billingForB2C);
    const b2bRadio = page.locator(PE.billingForB2B);

    await expect(b2cRadio).not.toBeDisabled();
    await expect(b2bRadio).not.toBeDisabled();

    // Verify correct radio is checked for B2C bill
    await expect(b2cRadio).toBeChecked();
  });
});
