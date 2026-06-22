---
description: Fixes discrepancy where looseDia tag total gross weight was mapped to diamond weight column instead of gross weight column.
---

# Recipe: LooseDia Weight Mismatch in Section-wise Stock Report

## Metadata
- **Pattern ID**: SEC-STOCK-001
- **Severity**: HIGH
- **Modules Affected**: Reports
- **Auto-fixable**: NO

## Client Scope
- **Applies to**: ALL Clients
- **Framework**: CodeIgniter 3

## Created By
- **Developer**: Antigravity
- **Client**: erp.sriammanjewellers.in
- **Date**: 2026-06-22

## Symptom
The Section-wise Stock Report shows inflated values for diamond weight and 0 for gross weight for `stone_type = 2` (Loose Diamond) products. For example, if a tag has 58.59 gross weight and 7.67 stone weight, the report shows 66.26 diamond weight and 0 gross weight.

## Root Cause
In `get_section_wise_stock_inout_details()`, the query splits tag logic into subqueries by `stone_type`. For `stone_type = 2`, the subquery `looseDia_*` sums the **tag's gross weight** into `gross_wt`. The outer SELECT then incorrectly routes `looseDia_*.gross_wt` into the **diamond weight** column (e.g., `op_blc_diawt`) instead of the gross weight column. Diamond stone weight is actually supposed to come only from the `stn_*` subquery which queries the `ret_taging_stone` table.

## Detection
Run this in `admin/application/models/`:
```bash
grep -rn "+ IFNULL(looseDia_blc.gross_wt,0)) as op_blc_diawt" ret_reports_model.php
```

## Files
- `admin/application/models/ret_reports_model.php`

## Fix
Move `looseDia_*.gross_wt/net_wt/piece` to the main gross/net/piece columns and remove them from the diamond weight column. Apply this across all 7 movement types (opening balance, inward, sold, branch out, section out, purchase return, karigar issue).

### Before (Opening Balance example)
```sql
(IFNULL(blc.piece,0)) as op_blc_pcs,
(IFNULL(blc.gross_wt,0)) as op_blc_gwt, (IFNULL(blc.net_wt,0)) as op_blc_nwt,
(IFNULL(stn_blc.dia_wt,0) + IFNULL(looseDia_blc.gross_wt,0)) as op_blc_diawt,
```

### After (Opening Balance example)
```sql
(IFNULL(blc.piece,0) + IFNULL(looseDia_blc.piece,0)) as op_blc_pcs,
(IFNULL(blc.gross_wt,0) + IFNULL(looseDia_blc.gross_wt,0)) as op_blc_gwt, (IFNULL(blc.net_wt,0) + IFNULL(looseDia_blc.net_wt,0)) as op_blc_nwt,
(IFNULL(stn_blc.dia_wt,0)) as op_blc_diawt,
```

## Verification
1. Open the Section-wise Stock Report for a `stone_type = 2` product.
2. Confirm the gross weight correctly shows the tag gross weight, and the diamond weight correctly shows only the stone weight.
3. Compare with the Stock In/Out Summary report; the figures should match.
