# Recipe: Order Advance Bill Copy — IGST Shown for Local Tamil Nadu Address

## Metadata
- **Pattern ID**: BIL-OA-001
- **Severity**: P1 (Customer-facing wrong tax display)
- **Modules Affected**: Billing (bill_type == 5 — Order Advance)
- **Auto-fixable**: Yes
- **Files**: `billing/print/billing_soft_copy.php`, `billing/print/receipt_billing.php`

## Client Scope
- **Applies to**: ALL clients running Logimax ERP where Order Advance bills are used
- **Reason**: Shared template logic in all builds

## Created By
- **Developer**: Antigravity AI
- **Client**: etail_development_src (source repo)
- **Date**: 2026-05-02
- **Source Bug**: Order Advance bill copy IGST shown for local Tamil Nadu addresses

## Symptom
Client reports: "In the Order Advance bill copy, when the address is within Tamil Nadu (local address), IGST is sometimes being applied in the bill."

Specifically, when printing the Order Advance bill (bill_type == 5), the IGST row appears even when the customer's state matches the company/branch state (i.e., intra-state transaction). This is a GST compliance violation — intra-state transactions must use CGST + SGST, never IGST.

## Root Cause
In both `billing_soft_copy.php` and `billing/print/receipt_billing.php`, the Order Advance section (guarded by `if ($billing['bill_type'] == 5)`) shows IGST using a simple value-based check:

```php
// BEFORE (WRONG)
<?php if ($od_total_igst > 0) { ?>
    <td>IGST</td><td><?php echo $od_total_igst; ?></td>
<?php } ?>
```

This means: if any IGST was ever stored in the order details (e.g., from old data or a JS tax calculation bug at order creation), it will display on the bill — regardless of the customer's state. There is **no state-based check** to enforce the GST rule: intra-state = CGST + SGST, inter-state = IGST.

## Detection
```powershell
# Find affected files
Select-String -Path "admin/application/views/billing/print/billing_soft_copy.php",
               "admin/application/views/billing/print/receipt_billing.php" `
              -Pattern "od_total_igst > 0" | Select-Object Path, LineNumber, Line
```

**Confirmed vulnerable** if the match does NOT have `&& !$is_local_state` in the condition.

## Files
- `admin/application/views/billing/print/billing_soft_copy.php`
- `admin/application/views/billing/print/receipt_billing.php`

## Fix

### Step 1: Add `$is_local_state` flag (inside the Order Advance foreach variable init block)

In the variable initialization block (just before `foreach ($est_other_item['order_details'] as $items)`), add:

```php
// AFTER (CORRECT)
// Determine if local (intra-state) or inter-state for GST display
// Uses state name comparison — 'cus_state' and comp_details['state'] are unambiguous, always-populated fields
$is_local_state = (!empty($billing['cus_state']) && !empty($comp_details['state'])
    && strtoupper(trim($billing['cus_state'])) === strtoupper(trim($comp_details['state'])));
```

**Why `cus_state` not `state_code`?**  
The billing SQL query has TWO JOINs that both alias as `state_code` — `s.state_code` (customer) and `st.state_code` (delivery address). MySQL returns the last one, which may be NULL. `cus_state` (customer state name) and `comp_details['state']` (company state name) are unambiguous, always populated string fields.

### Step 2: Add IGST redistribution + guard the display (after the foreach loop closes)

Replace the CGST+SGST+IGST display block:

```php
// BEFORE
<?php if ($od_total_cgst > 0 || $od_total_sgst > 0) { ?>
    <tr><td>CGST ...</td><td><?php echo $od_total_cgst; ?></td></tr>
    <tr><td>SGST ...</td><td><?php echo $od_total_sgst; ?></td></tr>
<?php } ?>
<?php if ($od_total_igst > 0) { ?>
    <tr><td>IGST ...</td><td><?php echo $od_total_igst; ?></td></tr>
<?php } ?>
```

```php
// AFTER
<?php
// For local (intra-state) transactions: if IGST was mistakenly saved, redistribute into CGST+SGST for display only
if ($is_local_state && $od_total_igst > 0) {
    $od_display_cgst = $od_total_cgst + ($od_total_igst / 2);
    $od_display_sgst = $od_total_sgst + ($od_total_igst / 2);
} else {
    $od_display_cgst = $od_total_cgst;
    $od_display_sgst = $od_total_sgst;
}
?>
<?php if ($od_display_cgst > 0 || $od_display_sgst > 0) { ?>
    <tr><td>CGST ...</td><td><?php echo $od_display_cgst; ?></td></tr>
    <tr><td>SGST ...</td><td><?php echo $od_display_sgst; ?></td></tr>
<?php } ?>
<?php if ($od_total_igst > 0 && !$is_local_state) { ?>
    <tr><td>IGST ...</td><td><?php echo $od_total_igst; ?></td></tr>
<?php } ?>
```

## Verification

1. Open an Order Advance bill copy for a **Tamil Nadu** customer → IGST should NOT appear. CGST + SGST should appear (with correct totals including any redistributed IGST).
2. Open an Order Advance bill copy for an **out-of-state** customer → IGST should appear normally. CGST + SGST should NOT appear.
3. Run PHP syntax check on both files:
   ```powershell
   php -l admin/application/views/billing/print/billing_soft_copy.php
   php -l admin/application/views/billing/print/receipt_billing.php
   ```
   Expected: `No syntax errors detected`

## Notes
- **Scope**: Fix is entirely within `if ($billing['bill_type'] == 5)` blocks. All other bill types (Sales, Purchase, Return, Credit Collection, etc.) are completely untouched.
- **DB not touched**: This is a display-layer fix only. No changes to how taxes are stored.
- **Legacy data**: The IGST redistribution (`$od_display_cgst = $od_total_cgst + ($od_total_igst / 2)`) handles existing orders that were saved with IGST for a Tamil Nadu customer. The totals remain balanced for display purposes.
- **Root fix**: The correct long-term fix is also to ensure the Order form JavaScript calculates CGST+SGST (not IGST) when the customer's state matches the company state at order creation time. However, the display-layer fix alone ensures the printed bill is always correct.
