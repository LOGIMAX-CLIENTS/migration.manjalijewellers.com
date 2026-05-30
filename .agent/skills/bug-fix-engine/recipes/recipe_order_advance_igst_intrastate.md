# Recipe: Order Advance Bill — IGST Shown for Intra-State Customers

## Metadata
- **Recipe ID**: RCP-BIL-IGST-ORDERADV-001
- **Module**: Billing / Print Templates
- **Bug Category**: Tax Display (Print Layer)
- **Track**: B (Business Logic)
- **Severity**: P2
- **First Fixed**: 2026-05-02
- **Affected Files**:
  - `admin/application/views/billing/print/receipt_billing.php`
  - `admin/application/views/billing/print/billing_soft_copy.php`

---

## Symptom
On the Order Advance bill copy (`bill_type = 5`), IGST is sometimes printed for customers
in the **same state as the company** (intra-state). This happens because legacy data saved
with non-zero `total_igst` values is not filtered at the display layer.

---

## Root Cause
Both print templates (`receipt_billing.php` and `billing_soft_copy.php`) render the IGST row
for the Order Advance section with only:
```php
<?php if ($od_total_igst > 0) { ?>
```
There is **no inter-state guard** — so even if IGST was mistakenly stored, it appears on print
for intra-state orders.

---

## Detection
Search for the Order Advance IGST block:
```bash
grep -n "od_total_igst" admin/application/views/billing/print/receipt_billing.php
grep -n "od_total_igst" admin/application/views/billing/print/billing_soft_copy.php
```
If the guard `$_od_is_interstate` is **not** present on the same or adjacent line → bug exists.

---

## Fix

### Before (`receipt_billing.php` and `billing_soft_copy.php`)
```php
<?php if ($od_total_igst > 0) { ?>
    <tr>
        ...IGST row...
    </tr>
<?php } ?>
```

### After (both files)
```php
<?php
// BUG-FIX: Show IGST only for inter-state transactions
$_od_is_interstate = (strtolower(trim($billing['cus_state'])) !== strtolower(trim($comp_details['state'])));
?>
<?php if ($od_total_igst > 0 && $_od_is_interstate) { ?>
    <tr>
        ...IGST row...
    </tr>
<?php } ?>
```

### Key Points
- `$billing['cus_state']` = customer state (available from `getBillingDetails()` query)
- `$comp_details['state']` = company/branch state (available from `getCompanyDetails()` query)
- Both are always available in the `$data` array passed to both views via `get_receipt_data()`
- Case-insensitive + trim comparison prevents false positives from whitespace/casing differences

---

## Verification Steps
1. **Intra-state customer (e.g. Tamil Nadu)**: Print Order Advance bill → IGST row **must NOT appear**
2. **Inter-state customer (e.g. Kerala)**: Print Order Advance bill → IGST row **must appear** if `total_igst > 0`
3. CGST + SGST rows are unaffected — they still show for intra-state when non-zero

---

## Notes
- This bug was identified across multiple client environments
- The same IGST guard is also needed for Sales Bill / Sales Return sections (separate recipe)
- Do NOT redistribute IGST into CGST/SGST at print layer — just suppress display
