# Tag Profit Report — Diamond Product Wastage/MC Profit Empty Values

> For diamond products, the Purchase V.A weight (purwastagewt), Profit@Wastage, and Profit@Wastage% columns show 0 or incorrect values because the PHP model recalculates the percentage but never recalculates the dependent weight and profit fields.

## Metadata
- **Pattern ID**: PAT-RPT-003
- **Severity**: MEDIUM
- **Modules Affected**: Tag Wise Profit Report (`ret_reports_model`)
- **Auto-fixable**: Yes (string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: Any client with diamond/precious stone products where `ret_purchase_order_items` JOIN returns NULL (no PO match) will hit this bug. The `lot_wastage_percentage` defaults to 0, but `purwastage` gets recalculated from `purchase_touch - ceil(pur_purity)` without updating derived fields.

## Created By
- **Developer**: AI Agent (Antigravity)
- **Client**: erp.srivenkateswari.com (SVJ)
- **Date**: 2026-05-07
- **Source Bug ID**: N/A

## Symptom
In the Tag Wise Profit Report, diamond products show:
- Purchase V.A (GM) column: **0.000** (should show calculated weight)
- Profit@Wastage column: **inflated** (shows full sale wastage weight instead of difference)
- Profit@Wastage% column: **inflated** (shows full sale wastage% instead of difference)

The Purchase V.A (%) column correctly shows the recalculated value (e.g., 17%), but the corresponding weight and profit calculations don't match.

## Root Cause
In `get_wastagewisepandlreport()`:

1. **SQL** calculates `purwastagewt` using `IFNULL(puritm.item_wastage, tag.lot_wastage_percentage)` — for diamond products, `puritm` JOIN fails (NULL), fallback `lot_wastage_percentage` = 0.00 → `purwastagewt` = 0.000
2. **PHP lines ~25270-25277** calculate `wastageprofit` and `profitwastper` using `$items['item_wastage']` (= 0.00 from SQL)
3. **PHP lines ~25341-25343** recalculate `purwastage` percentage = `purchase_touch - ceil(pur_purity)` (e.g., 92 - 75 = 17)
4. **BUT** steps 2 already ran before step 3, so `purwastagewt`, `wastageprofit`, and `profitwastper` still use the old 0% value

The percentage is corrected but the derived calculations are never refreshed.

## Detection
```command
grep -n "purwastage.*purchase_touch.*ceil" admin/application/models/ret_reports_model.php
```
If this pattern exists WITHOUT subsequent recalculation of `purwastagewt`, `wastageprofit`, `profitwastper` — bug is present.

## Files
- `admin/application/models/ret_reports_model.php`

## Fix

### Before
```php
                if ((float)$items['purchase_touch'] != "0.00") {
                    $items['purwastage'] = (float)$items['purchase_touch'] - ceil((float)$items['pur_purity']);
                }
```

### After
```php
                if ((float)$items['purchase_touch'] != "0.00") {
                    $items['purwastage'] = (float)$items['purchase_touch'] - ceil((float)$items['pur_purity']);
                    // Recalculate purchase wastage weight and profit fields to match corrected percentage
                    $items['purwastagewt'] = number_format(($items['net_wt'] * $items['purwastage'] / 100), 3, '.', '');
                    $items['wastageprofit'] = number_format(((float)$items['wastagewt'] - (float)$items['purwastagewt']), 3, '.', '');
                    $items['profitwastper'] = number_format(((float)$items['wastage_percent'] - (float)$items['purwastage']), 2, '.', '');
                }
```

## Verification
1. Open Tag Wise Profit Report, filter for a diamond product (e.g., D-RING-18KT)
2. Check Purchase V.A (GM) column — should NOT be 0.000 for products with non-zero purchase touch
3. Check Profit@Wastage column — should be the DIFFERENCE between sale wastage weight and purchase wastage weight
4. Check Profit@Wastage% column — should be the DIFFERENCE between sale wastage% and purchase wastage%
5. Cross-verify with gold products (non-diamond) — their values should remain unchanged since `puritm` JOIN works for them

## Notes
- This bug only affects products where `ret_purchase_order_items` JOIN returns NULL (typically diamond/precious stone products without PO entries)
- Gold products with valid PO entries are NOT affected because `puritm.item_wastage` provides the correct value directly
- The `purwastage` percentage display was already correct — only the dependent weight/profit calculations were stale
