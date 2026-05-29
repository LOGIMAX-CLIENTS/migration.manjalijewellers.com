# Issue/Receipt Payment Edit — Playwright MCP Test Suite

> **Config**: Read `test_config.json` before executing.  
> **Pre-requisite**: Login must be completed first (see LOGIN procedure below).

---

## LOGIN (run once per session)

```
STEP 1: browser_navigate → {base_url}{login.url_path}
STEP 2: browser_fill_form → {login.selectors.username} = {credentials.username}
STEP 3: browser_fill_form → {login.selectors.password} = {credentials.password}
STEP 4: browser_click → {login.selectors.submit}
STEP 5: browser_wait_for → selector: {login.success_indicator}, timeout: {timeouts.page_load_ms}
STEP 6: browser_take_screenshot → verify dashboard loaded
VERIFY: Screenshot shows logged-in dashboard
```

---

## AT-1: Page loads from issue list edit icon

**Purpose**: Verify edit page loads correctly when navigating via URL  
**Test Data**: `issue_with_cash` (ID: 5039)

```
STEP 1: browser_navigate → {base_url}{urls.issue_edit} (replace {id} with 5039)
STEP 2: browser_wait_for → selector: {selectors.payment_section}, timeout: {timeouts.page_load_ms}
         (wait for payment section to become visible)
STEP 3: browser_take_screenshot → capture loaded state

ASSERT 1: Payment section (#ir_payDet) is visible
ASSERT 2: Voucher info bar (#ir_voucher_info_row) is visible
ASSERT 3: No error messages on page
```

---

## AT-2: Voucher info bar shows correct data

**Purpose**: Verify bill no, type, party, date, amount display correctly  
**Test Data**: `issue_with_cash` (ID: 5039)

```
STEP 1: browser_navigate → {base_url}{urls.issue_edit} (replace {id} with 5039)
STEP 2: browser_wait_for → selector: {selectors.voucher_info}:not([style*='display:none'])

STEP 3: browser_evaluate → document.querySelector('{selectors.info_type}').textContent
         ASSERT: contains "Issue"

STEP 4: browser_evaluate → document.querySelector('{selectors.info_amount}').textContent
         ASSERT: equals "1000.00"

STEP 5: browser_evaluate → document.querySelector('{selectors.info_billno}').textContent
         ASSERT: not empty, contains bill no

STEP 6: browser_take_screenshot → visual verification
```

---

## AT-3: Existing payments pre-populated correctly

**Purpose**: Verify cash field shows current payment value  
**Test Data**: `issue_with_cash` (ID: 5039, Cash: 1000)

```
STEP 1: browser_navigate → {base_url}{urls.issue_edit} (replace {id} with 5039)
STEP 2: browser_wait_for → selector: {selectors.payment_section}:not([style*='display:none'])

STEP 3: browser_evaluate → document.querySelector('{selectors.cash_payment}').value
         ASSERT: equals "1000"

STEP 4: browser_evaluate → document.querySelector('{selectors.voucher_amount}').value
         ASSERT: equals "1000.00"

STEP 5: browser_evaluate → document.querySelector('{selectors.card_total}').value
         ASSERT: empty or "0.00" (no card payments exist)
```

---

## AT-4: Cash → Cash + Card split (core test)

**Purpose**: Change payment from all-cash to cash+card split and verify it saves  
**Test Data**: `issue_with_cash` (ID: 5039, Amount: 1000, Current: Cash:1000)

```
STEP 1: browser_navigate → {base_url}{urls.issue_edit} (replace {id} with 5039)
STEP 2: browser_wait_for → selector: {selectors.payment_section}:not([style*='display:none'])

-- Change cash to 700 --
STEP 3: browser_click → {selectors.cash_payment}  (focus the field)
STEP 4: browser_evaluate → document.querySelector('{selectors.cash_payment}').value = ''
STEP 5: browser_type → "700" (into focused cash field)

-- Add card 300 --
STEP 6: browser_click → {selectors.card_modal_btn}
STEP 7: browser_wait_for → selector: {selectors.card_modal}.in, timeout: {timeouts.modal_ms}
STEP 8: browser_click → {selectors.add_card_btn}
STEP 9: browser_evaluate →
         var rows = document.querySelectorAll('#ir_card_details tbody tr');
         var lastRow = rows[rows.length - 1];
         var amtInput = lastRow.querySelector('.ir_card_amt');
         amtInput.value = '300';
         amtInput.dispatchEvent(new Event('input'));
STEP 10: browser_click → {selectors.save_card_btn}

-- Verify card total updated --
STEP 11: browser_evaluate → document.querySelector('{selectors.card_total}').value
          ASSERT: equals "300.00"

-- Save --
STEP 12: browser_click → {selectors.save_btn}
STEP 13: browser_wait_for → timeout: 3000 (wait for AJAX response)
STEP 14: browser_take_screenshot → capture result

ASSERT: Success toast visible OR page redirected
```

---

## AT-5: Verify persistence after save

**Purpose**: Re-open the same record and verify the new split was saved  
**Test Data**: `issue_with_cash` (ID: 5039, Expected: Cash:700, Card:300 after AT-4)
**Depends on**: AT-4

```
STEP 1: browser_navigate → {base_url}{urls.issue_edit} (replace {id} with 5039)
STEP 2: browser_wait_for → selector: {selectors.payment_section}:not([style*='display:none'])

STEP 3: browser_evaluate → document.querySelector('{selectors.cash_payment}').value
         ASSERT: equals "700"

STEP 4: browser_evaluate → document.querySelector('{selectors.card_total}').value
         ASSERT: equals "300.00"

STEP 5: browser_take_screenshot → visual proof of persistence
```

---

## AT-6: Mismatched total rejected

**Purpose**: Verify server rejects payment total that doesn't match voucher amount  
**Test Data**: `issue_with_cash` (ID: 5039)

```
STEP 1: browser_navigate → {base_url}{urls.issue_edit} (replace {id} with 5039)
STEP 2: browser_wait_for → selector: {selectors.payment_section}:not([style*='display:none'])

-- Set cash to wrong amount --
STEP 3: browser_evaluate → document.querySelector('{selectors.cash_payment}').value = ''
STEP 4: browser_fill_form → {selectors.cash_payment} = "500"

-- Submit --
STEP 5: browser_click → {selectors.save_btn}
STEP 6: browser_wait_for → timeout: 3000

-- Verify error --
STEP 7: browser_take_screenshot → should show error toast/alert
STEP 8: browser_snapshot → look for "does not match" text

ASSERT: Error message displayed, page stays on edit form (not redirected)
```

---

## AT-7: Revert to original state

**Purpose**: Restore test data to original state (all cash) for repeatability  
**Test Data**: `issue_with_cash` (ID: 5039, Revert to: Cash:1000)

```
STEP 1: browser_navigate → {base_url}{urls.issue_edit} (replace {id} with 5039)
STEP 2: browser_wait_for → selector: {selectors.payment_section}:not([style*='display:none'])

-- Set cash to full amount --
STEP 3: browser_evaluate →
         document.querySelector('{selectors.cash_payment}').value = '1000';
         document.querySelector('{selectors.cash_payment}').dispatchEvent(new Event('input'));

-- Clear any card/chq/nb data via JS --
STEP 4: browser_evaluate →
         if (typeof ir_card_rows !== 'undefined') { ir_card_rows = []; ir_chq_rows = []; ir_nb_rows = []; }
         ir_refresh_balance_displays();

-- Save --
STEP 5: browser_click → {selectors.save_btn}
STEP 6: browser_wait_for → timeout: 3000
STEP 7: browser_take_screenshot → verify success

ASSERT: Success message, data reverted
```

---

## AT-8: Receipt edit (not just issue)

**Purpose**: Verify the same edit page works for Receipt type  
**Test Data**: `receipt_with_cash` (ID: 5032, Amount: 2500, Type: Receipt)

```
STEP 1: browser_navigate → {base_url}{urls.receipt_edit} (replace {id} with 5032)
STEP 2: browser_wait_for → selector: {selectors.payment_section}:not([style*='display:none'])

STEP 3: browser_evaluate → document.querySelector('{selectors.info_type}').textContent
         ASSERT: equals "Receipt"

STEP 4: browser_evaluate → document.querySelector('{selectors.info_amount}').textContent
         ASSERT: equals "2500.00"

STEP 5: browser_evaluate → document.querySelector('{selectors.cash_payment}').value
         ASSERT: equals "2500"

STEP 6: browser_take_screenshot → verify receipt edit works
```

---

## Test Execution Summary Template

After running, fill in:

| TC# | Name | Result | Evidence |
|---|---|---|---|
| AT-1 | Page loads | PASS/FAIL | screenshot |
| AT-2 | Info bar correct | PASS/FAIL | screenshot |
| AT-3 | Pre-populated | PASS/FAIL | screenshot |
| AT-4 | Cash→Card split | PASS/FAIL | screenshot |
| AT-5 | Persistence | PASS/FAIL | screenshot |
| AT-6 | Mismatch rejected | PASS/FAIL | screenshot |
| AT-7 | Revert | PASS/FAIL | screenshot |
| AT-8 | Receipt type | PASS/FAIL | screenshot |
