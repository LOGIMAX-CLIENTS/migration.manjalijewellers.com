# Recipe: Billing VA Weight Invoice Desync — Hidden Form Field Not Synced

## Metadata
- **Pattern ID**: PAT-BIL-VA02
- **Severity**: HIGH
- **Modules Affected**: Billing (Receipt Form), Invoice Print
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients use the same `ret_billing.js` form logic with the dual-input architecture for wastage percentage.

## Created By
- **Developer**: Antigravity
- **Client**: Manepally
- **Date**: 2026-05-16
- **Source Bug ID**: N/A

## Symptom
When the VA (Value Addition / wastage) weight is modified in the billing screen, the printed invoice continues to display the **original estimation value** instead of the updated billing value.

**Example:**
- Estimation VA Weight: 2.1 gms
- Updated VA Weight in Billing: 1.8 gms
- Invoice print displays: 2.1 gms ❌
- Expected: 1.8 gms ✅

## Root Cause
The billing sales table row contains **two hidden inputs** for wastage percentage:

```html
<!-- Visible/editable input — NO name attribute, never submitted -->
<input type="text" class="bill_wastage" value="..." />

<!-- Hidden form field — HAS name attribute, submitted to server -->
<input type="hidden" class="bill_wastage_per" name="sale[wastage][]" value="..." />
```

When the user modifies VA weight, the `bill_wastage` field updates and `calculateSaleBillRowTotal()` reads from it for calculations. However, the `bill_wastage_per` field (which carries `name="sale[wastage][]"` and is actually submitted to the server) is **never synced** with the updated value. The controller saves `$billSale['wastage'][$key]` → `ret_bill_details.wastage_percent`, which retains the stale estimation value.

## Detection
```powershell
# Check if bill_wastage_per is synced inside calculateSaleBillRowTotal
Select-String -Path "admin/assets/js/ret_billing.js" -Pattern "bill_wastage_per.*val.*bill_wastage" | Select-Object LineNumber, Line
```
If no results, the sync is missing. After the fix, you should see 4+ matches.

```powershell
# Verify the dual-input architecture exists
Select-String -Path "admin/assets/js/ret_billing.js" -Pattern "bill_wastage_per.*name.*sale\[wastage\]" | Select-Object LineNumber
```

## Files
- `admin/assets/js/ret_billing.js`
- `admin/application/views/billing/print/receipt_billing.php` (syntax fix only)

## Fix

### ret_billing.js — calculateSaleBillRowTotal() (line ~4303)

#### Before
```javascript
curRow.find('.bill_wastage_wt').val(wast_wgt);
curRow.find('.est_wastage').html(parseFloat(...));
```

#### After
```javascript
curRow.find('.bill_wastage_wt').val(wast_wgt);
curRow.find('.bill_wastage_per').val(curRow.find('.bill_wastage').val()); // Sync VA% to hidden form field for save
curRow.find('.est_wastage').html(parseFloat(...));
```

### ret_billing.js — calculateOrderSaleBillRowTotal() (2 locations, lines ~5291, ~5532)

#### Before
```javascript
curRow.find('.bill_wastage_wt').val(wast_wgt);
curRow.find('.est_wastage_wt').html(wast_wgt);
```

#### After
```javascript
curRow.find('.bill_wastage_wt').val(wast_wgt);
curRow.find('.bill_wastage_per').val(curRow.find('.bill_wastage').val()); // Sync VA% to hidden form field for save
curRow.find('.est_wastage_wt').html(wast_wgt);
```

### ret_billing.js — Bill split calculation (line ~18278)

#### Before
```javascript
curRow.find('.bill_wastage_wt').val(wast_wgt);
if (discount_weight > 0) {
```

#### After
```javascript
curRow.find('.bill_wastage_wt').val(wast_wgt);
curRow.find('.bill_wastage_per').val(curRow.find('.bill_wastage').val()); // Sync VA% to hidden form field for save
if (discount_weight > 0) {
```

### receipt_billing.php — Short open tag fixes (lines 175, 1829)

#### Before
```php
<?php } else { ?><? if ($type == 'od' && sizeof($est_other_item['old_matel_details']) >= 0) { ?>
// ...
<? } ?>
```

#### After
```php
<?php } else { ?><?php if ($type == 'od' && sizeof($est_other_item['old_matel_details']) >= 0) { ?>
// ...
<?php } ?>
```

## Verification
1. Create an estimation with VA weight = 2.1 gms
2. Convert to billing, modify VA weight to 1.8 gms
3. Save the bill
4. Generate invoice print at `admin_ret_billing/billing_invoice/{bill_id}`
5. Verify VA weight shows **1.8 gms** (billing value, not estimation)
6. Verify VA Amount = VA_weight × rate_per_grm matches correctly
7. Verify the sub-row wastage percentage recalculates correctly
8. Test with Order Delivery bill type (bill_type=9) to confirm the order calculation function is also fixed
9. Test with Bill Split flow to confirm the split calculation is also fixed

## Notes
- A `keyup` handler at line ~19047 already syncs `bill_wastage_per` for the bill-split typing flow — the bug only affected the calculation-triggered updates.
- **Existing bills** saved before this fix have incorrect `wastage_percent` in `ret_bill_details`. Those bills must be edited and re-saved, or corrected via direct DB update.
- The print template (`receipt_billing.php`) and model query (`ret_billing_model.php` → `getOtherEstimateItemsDetails`) were already correct — they read from `ret_bill_details.wastage_percent` (alias `d.wastage_percent`).
- Related recipe: `recipe_billing_va_weight_threshold_display.md` (PAT-BIL-VA01) handles VA display format (gm vs %) — this recipe handles the data correctness.
