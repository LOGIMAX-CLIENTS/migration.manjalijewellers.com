# Recipe: Advance Refund Type 7 Exclusion

## Metadata

| Field | Value |
|---|---|
| Pattern ID | PAT-BIL-009 |
| Severity | P1 |
| Modules Affected | Billing / Issue Receipt (`ret_billing_model.php` — `get_receipt_refund()`) |
| Auto-fixable | Yes |
| Bug ID | BIL-ADV-009 |

## Client Scope

**Applies to**: All client repositories using the Retail Billing and Issue Receipt refund functionality.

## Created By

| Field | Value |
|---|---|
| Developer | Antigravity |
| Client | Karpagam Jewels |
| Date | 2026-06-22 |

---

## Symptom

When a user selects a customer on the Issue Form (Refund option) who holds an active advance transferred from another receipt/account, the system displays a toaster message `"Success!: No Records Found..."` instead of opening the refund receipt selection modal, preventing the refund from being processed.

---

## Root Cause

The model method `get_receipt_refund()` in `ret_billing_model.php` queries the database for refundable advance receipts belonging to a customer. However, the SQL query filters the receipt types with:
```sql
AND (ir.receipt_type=2 or ir.receipt_type=3 or ir.receipt_type=4 or ir.receipt_type=5)
```
This filter excludes receipt type `7` (Advance Transfer). If the customer's advance balance resides on a type `7` receipt, the query returns no records, resulting in the "No Records Found" message.

---

## Detection

Search `ret_billing_model.php` to see if type `7` is excluded from the query's list of receipt types:
```command
grep -rn "ir\.receipt_type=5" admin/application/models/ret_billing_model.php
```
If the search result does not contain `or ir.receipt_type=7`, the codebase is vulnerable to this bug.

---

## Files

| File | Function | Lines |
|---|---|---|
| `admin/application/models/ret_billing_model.php` | `get_receipt_refund()` | ~5167 |

---

## Fix

### Before:
```php
where ir.id_customer=" . $bill_cus_id . " and ir.bill_status=1 AND (ir.receipt_type=2 or ir.receipt_type=3 or ir.receipt_type=4 or ir.receipt_type=5)
```

### After:
```php
where ir.id_customer=" . $bill_cus_id . " and ir.bill_status=1 AND (ir.receipt_type=2 or ir.receipt_type=3 or ir.receipt_type=4 or ir.receipt_type=5 or ir.receipt_type=7)
```

---

## Verification

1. Go to **Billing** -> **Issue Form** -> Select **Issue Type: Refund** (value = `3`).
2. Search and select the customer having the transferred advance balance (e.g. mobile `8220094914`).
3. Verify that the refund selection modal opens successfully showing the advance transfer details.
4. Verify that the remaining refundable amount matches the expected balance.

---

## Notes

Any advance transfer (type 7) creates a new receipt record that represents a valid advance balance owned by the recipient. Therefore, it must be treated as refundable, just like standard advances (type 2), advance deposits (type 3), opening balances (type 4), and order-to-general advances (type 5).
