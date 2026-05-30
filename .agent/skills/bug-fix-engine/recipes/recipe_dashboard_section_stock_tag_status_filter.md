# Recipe: Dashboard Section-Wise Stock Missing tag_status Filter

## Metadata
- **Pattern ID**: PAT-DSH-001
- **Severity**: P1
- **Modules Affected**: Dashboard (Stock Chart — Section-Wise Stock)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: `get_section_stock()` in `ret_dashboard_api_model.php` is missing `tag.tag_status = 0` in both the main WHERE and the stone subquery WHERE — systemic omission present across all clients using this codebase.

## Created By
- **Developer**: Antigravity
- **Client**: etail_development_src
- **Date**: 2026-04-27
- **Source Bug ID**: DSH-S01

## Symptom
On the Dashboard → Stock Chart tab, the **Section-Wise Stock** grand totals (Pcs, GWT, NWT) are **higher/different** compared to **Product-Wise Stock** grand totals for the same branch and metal filter.

Section-wise totals include sold/billed/returned items, not just current in-stock items.

## Root Cause
`get_section_stock()` in `ret_dashboard_api_model.php` is missing `tag.tag_status = 0` in:
1. The **main outer WHERE clause** — only had `tag.id_section IS NOT NULL`, so it counted ALL tagged items regardless of sale status.
2. The **diamond stone subquery WHERE** — only had `st.stone_type = 1`, so diamond weights of sold items were included.

Compare with `get_product_stock()` which correctly has `tag.tag_status = 0` in both locations.

`tag_status` values:
- `0` = In Stock (physically on rack, available for sale)
- `1` = Sold (billed to customer)
- `2` = Deleted/Cancelled

## Detection
```powershell
# Find get_section_stock function and check for missing tag_status filter
findstr /n "tag_status" "admin\application\models\ret_dashboard_api_model.php"
# If line ~979 shows only "tag.id_section IS NOT NULL" with no tag_status = 0 → bug present
```

Or grep for the function block and look at the WHERE clause:
```powershell
findstr /n "function get_section_stock" "admin\application\models\ret_dashboard_api_model.php"
```

## Files
- `admin/application/models/ret_dashboard_api_model.php` — function `get_section_stock()` (~L942)

## Fix

### Before (stone subquery WHERE, ~L973):
```php
Where st.stone_type = 1
```

### After:
```php
Where st.stone_type = 1 and tag.tag_status = 0
```

---

### Before (main outer WHERE, ~L978-979):
```php
WHERE
     tag.id_section IS NOT NULL
```

### After:
```php
WHERE
     tag.tag_status = 0 and tag.id_section IS NOT NULL
```

## Verification
1. Open Dashboard → Stock Chart tab
2. Compare **Product-Wise Stock** grand total GWT vs **Section-Wise Stock** grand total GWT
3. After fix: both totals should match (or differ only by items with no section assigned)
4. Test with metal filter set to "Gold" — both sections should update consistently
5. Run PHP syntax check: `& "D:\XAMPP\php\php.exe" -l "admin\application\models\ret_dashboard_api_model.php"` → No syntax errors

## Notes
- `get_product_stock()` in the same file was written correctly from the start — this fix mirrors its exact pattern.
- If a tag has `id_section IS NULL` (no section assigned), it correctly won't appear in section-wise totals. This is expected behaviour — such items appear only in product-wise totals, so a small residual difference is normal.
- Diamond/stone weight (`dia_wt`) subquery also needed the fix to avoid inflated stone weights from sold items.
