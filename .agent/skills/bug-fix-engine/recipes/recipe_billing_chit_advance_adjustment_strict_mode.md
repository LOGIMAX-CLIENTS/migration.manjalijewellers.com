# Recipe: Chit Advance Adjustment & Zero Balance Checkout Crash (2 Bugs)

## Metadata

| Field | Value |
|---|---|
| Pattern IDs | PAT-BIL-001, PAT-BIL-002 |
| Severity | P1 |
| Modules Affected | Billing (`ret_billing_model.php` and `payment.js`) |
| Auto-fixable | No |
| Bug ID | BIL-BUG-001 |

## Client Scope

**Applies to**: Any client using the Chit / Advance adjustment modules in Billing checkout.

## Created By

| Field | Value |
|---|---|
| Developer | Antigravity |
| Client | SR Jewellery |
| Date | 2026-05-21 |

---

## Symptoms

1. **Checkout Freeze / Javascript Crash (PAT-BIL-001)**: When clicking the "Advance Details" or checkout-related buttons on the billing payment page for a customer with `0` wallet balance or no advance history, the page completely freezes. The browser console shows:
   `TypeError: Cannot read properties of undefined (reading 'id_ret_wallet')`
2. **Dayclose Query Date Crash (PAT-BIL-002)**: On retrieving advance details, the query fails with database date parsing errors or invalid date comparison errors when `$dayclose_date` is empty or not provided.

---

## Root Causes

1. **Missing AJAX response validations (PAT-BIL-001)**: The client-side payment JS script (`payment.js`) invoked an AJAX request to fetch a customer's wallet or advance details. When the customer's balance was `0`, the server returned an empty array (`[]`). The JS then immediately attempted to access properties of the first element (`data[0].id_ret_wallet`) without verifying if `data` contained any rows, causing a fatal JS crash.
2. **Invalid inline date logic (PAT-BIL-002)**: The model query in `ret_billing_model.php` had hardcoded logic comparing `DATE(ir.bill_date)` with an empty string when `$dayclose_date` was not provided, causing failures or syntax issues under strict database configurations (e.g. `ONLY_FULL_GROUP_BY`).

---

## Detection

1. Check JavaScript for direct property access on unvalidated AJAX payloads:
   ```javascript
   // Search for data[0] references inside payment.js AJAX callbacks:
   grep -n "data\[0\]\." admin/assets/js/payment.js
   ```
2. Check model date comparison logic:
   ```php
   // Check if $dayclose_date is evaluated directly in query string:
   grep -n "dayclose_date" admin/application/models/ret_billing_model.php
   ```

---

## Files

- `admin/assets/js/payment.js`
- `admin/application/models/ret_billing_model.php`

---

## Fix

### JS Fix — Add empty array guards (PAT-BIL-001)

#### Before:
```javascript
function get_advance_details() {
    // ...
    success: function (data) {
        rec_id_ret_wallet = data[0].id_ret_wallet;
        // ...
    }
}
```

#### After:
```javascript
function get_advance_details() {
    // ...
    success: function (data) {
        if (!data || data.length === 0) {
            $.toaster({ priority: 'danger', title: 'Warning!', message: '</br>Your Advance Amount is 0' });
            return;
        }
        rec_id_ret_wallet = data[0].id_ret_wallet;
        // ...
    }
}
```

---

### PHP/SQL Fix — Dynamic dayclose SELECT construction (PAT-BIL-002)

#### Before:
```php
$data = $this->db->query("SELECT ..., ir.bill_date, '".$dayclose_date."' AS dayclose_date, IF('".$dayclose_date."' != '', IF(DATE(ir.bill_date) = '".$dayclose_date."', 1, 0), 0) AS is_currentday_adv FROM ret_issue_receipt ir ...");
```

#### After:
```php
if (!empty($dayclose_date)) {
    $dayclose_select = "'".$dayclose_date."' AS dayclose_date, IF(DATE(ir.bill_date) = '".$dayclose_date."', 1, 0) AS is_currentday_adv";
} else {
    $dayclose_select = "'' AS dayclose_date, 0 AS is_currentday_adv";
}

$data = $this->db->query("SELECT ..., ir.bill_date, " . $dayclose_select . " FROM ret_issue_receipt ir ...");
```

---

## Verification

1. Select a customer with `0` wallet balance in the payment UI and click the advance adjustment button. Verify that the system shows a friendly toaster warning instead of crashing the tab.
2. Run dayclose/checkout flow in `ONLY_FULL_GROUP_BY` environment and ensure no SQL/Date parsing errors occur.
