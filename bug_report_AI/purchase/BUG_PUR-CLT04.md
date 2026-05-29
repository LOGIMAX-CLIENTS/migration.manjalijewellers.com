## PUR-CLT04 — getPending_payment_po_bills_for_payment Returns Empty (No Pending Bills on Payment Page)

| Field         | Value                         |
| ------------- | ----------------------------- |
| Severity      | P1                            |
| Track         | A (System)                    |
| Category      | Logic / Variable              |
| Sprint        | Sprint 1                      |
| Pattern Match | PAT-VAR-002 (Undefined Variable Assignment) |
| Module Brain  | ✅ Ready                      |
| Reporter      | Client (Internal)             |
| Source        | Internal                      |
| Status        | ✅ FIXED                      |
| Fixed Date    | 2026-03-21                    |

### Steps to Reproduce

1. Go to Supplier PO Payment page
2. Select a Karigar with pending bills
3. Observe the `#pending_bills_container` — shows "No pending bills found" or remains empty

### Expected Behavior

The pending bills table should populate with all unpaid PO bills for the selected karigar, with checkboxes for bill-wise payment selection.

### Actual Behavior

The AJAX call to `getPending_payment_po_bills_for_payment` returns empty/null response. No pending bills are displayed. The JS receives an unparseable response causing the table to remain empty.

### Root Cause

Two missing lines in the controller function `getPending_payment_po_bills_for_payment()` at `admin_ret_purchase.php` L13618:

1. **`$model` was never initialized** — missing `$model = self::RET_PUR_ORDER_MODEL;`
2. **`$list` was never populated** — never called `$this->$model->getPending_payment_po_bills_for_payment($_POST);`

The model function existed and was correct (L8543), but the controller never called it. The `$list` variable was referenced in `array_merge()` (L13625) and `json_encode()` (L13629) but was always `null/undefined`.

This was a copy-paste oversight — the neighboring function `getPending_payment_po_bills()` (L13610) has both lines correctly.

### Fix Applied

Added 2 lines at the start of the controller function:
```php
$model = self::RET_PUR_ORDER_MODEL;
$list = $this->$model->getPending_payment_po_bills_for_payment($_POST);
```

### Evidence

- File: `admin/application/controllers/admin_ret_purchase.php` L13618-13632
- Model function exists at: `admin/application/models/ret_purchase_order_model.php` L8543
- JS caller: `admin/assets/js/ret_purchase_order.js` L82898
