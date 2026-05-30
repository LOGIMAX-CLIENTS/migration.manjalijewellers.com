# Recipe: GRN Diamond Weight Not Displayed in Purchase Bill Summary

## Metadata
- **Pattern ID**: PAT-CALC-DIA-001
- **Severity**: HIGH
- **Modules Affected**: Purchase (Supplier Bill Entry)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core purchase module logic used by all clients

## Created By
- **Developer**: Antigravity AI
- **Client**: Source (etail_development_src)
- **Date**: 2026-04-03
- **Source Bug ID**: PUR-DIAWT-01

## Symptom
In Supplier Bill Entry → Summary tab, the "GRN Entry Details" table shows `0.000` in the **DIA WT** column even when diamond stones exist in the GRN, or when the bill has diamond weight that should show as a difference. The **LESS WT** difference may also be incorrect due to diamond weight being double-subtracted.

## Root Cause
Two issues:

1. **SQL (Model)**: Diamond weight subquery filtered by UOM short code `'CT'` via a JOIN to `ret_uom`. But `ret_grn_item_stone.uom_id` is frequently NULL — the actual UOM is stored in the parent `ret_stone` table. This caused all stones with NULL `uom_id` to be excluded.

2. **JS (Calculation)**: The difference calculation in `calculate_purchase_item_details` incorrectly:
   - Subtracted `bill_diawt` from `bill_lwt` before calculating Less Wt difference (double-subtraction — fields are already separate)
   - Used an `if (grn_dia_wt > 0)` guard that forced Dia Wt difference to `0.000` when GRN had no stones

## Detection
```powershell
# Check for UOM short code filter in stone subquery
Select-String -Pattern "uom_short_code.*CT" -Path "admin/application/models/ret_purchase_order_model.php"

# Check for the guard clause in JS
Select-String -Pattern "grn_dia_wt > 0" -Path "admin/assets/js/ret_purchase_order.js"
```

## Files
- `admin/application/models/ret_purchase_order_model.php` — `getGRNsCatDetailsbyGRNId()` method
- `admin/assets/js/ret_purchase_order.js` — `calculate_purchase_item_details()` function

## Fix

### Model Fix — Before
```php
LEFT JOIN (SELECT IFNULL(SUM(s.wt),0) as dia_wt, s.grn_item_id
           FROM ret_grn_item_stone s
           LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
           LEFT JOIN ret_uom m ON m.uom_id=s.uom_id
           WHERE m.uom_short_code ='CT'
           GROUP BY s.grn_item_id) as dia ON dia.grn_item_id = grnitm.grn_item_id
```

### Model Fix — After
```php
LEFT JOIN (SELECT IFNULL(SUM(s.wt),0) as dia_wt, s.grn_item_id
           FROM ret_grn_item_stone s
           LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
           WHERE (s.uom_id = 6 OR st.uom_id = 6 OR st.stone_type = 1)
           GROUP BY s.grn_item_id) as dia ON dia.grn_item_id = grnitm.grn_item_id
```

### JS Fix — Before
```javascript
var bill_diawt = parseFloat($('.tot_diawt').html()) || 0;
var bill_lwt = parseFloat($('.total_lwt').html()) || 0;
var net_bill_lwt = bill_lwt - bill_diawt;

var total_lwt = (parseFloat($('.total_gt_lwt').html()) || 0) - net_bill_lwt;

var total_nwt = (parseFloat($('.total_gt_nwt').html()) || 0) - (parseFloat($('.total_nwt').html()) || 0);

var grn_dia_wt = parseFloat($('.total_gt_diawt').html()) || 0;
var total_gdiawt = 0;
if (grn_dia_wt > 0) {
    total_gdiawt = grn_dia_wt - bill_diawt;
}
```

### JS Fix — After
```javascript
var total_lwt = (parseFloat($('.total_gt_lwt').html()) || 0) - (parseFloat($('.total_lwt').html()) || 0);

var total_nwt = (parseFloat($('.total_gt_nwt').html()) || 0) - (parseFloat($('.total_nwt').html()) || 0);

var total_gdiawt = (parseFloat($('.total_gt_diawt').html()) || 0) - (parseFloat($('.tot_diawt').html()) || 0);
```

## Verification
1. Open Supplier Bill Entry → add items → go to Summary tab
2. Verify GRN Entry Details shows correct **DIA WT** from stone records
3. Verify **DIFFERENCE** row shows actual `GRN - Bill` for both Less Wt and Dia Wt
4. Verify bill can be saved/approved without being blocked by Dia Wt discrepancy
5. Test with a GRN that has stones AND a GRN that has no stones

## Notes
- `ret_grn_item_stone.uom_id` is frequently NULL — always fall back to `ret_stone.uom_id` or `ret_stone.stone_type`
- Bill `total_lwt` and `total_diawt` are ALWAYS separate fields — never subtract one from the other before doing difference calculations
- The difference pattern for ALL columns should be identical: `GRN_TOTAL - BILL_TOTAL`
