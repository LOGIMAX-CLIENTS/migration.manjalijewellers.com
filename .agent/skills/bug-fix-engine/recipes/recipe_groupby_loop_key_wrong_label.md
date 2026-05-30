# Recipe: Nested Group Array — Loop Key Used as Data Label (GroupBy Bug)

## Metadata
- **Pattern ID**: PAT-RPT-002
- **Severity**: P2 — Wrong label on PDF/report for GroupBy=3 (design-level)
- **Modules Affected**: Ret_Reports (stock_details_print), any module that flattens nested group arrays
- **Auto-fixable**: Yes

## Client Scope
- Applies to: ALL clients using the Section-wise Stock Details print feature
- Reason: The outer foreach key of the model's grouped result changes meaning depending on GroupBy setting

## Created By
- Developer: Antigravity AI
- Client: erp.sriammanjewellers.in (amman)
- Date: 2026-04-25
- Source Bug ID: STOCK-PRINT-001

## Symptom
When the user prints the Section-wise Stock Details PDF with **GroupBy = 3 (Design-level)**, the section name column on the PDF shows the **product name** instead of the actual section name. For example, rows appear as `"Gold Bangles - Gold Bangles"` instead of `"Counter 1 - Gold Bangles"`.

## Root Cause
The model's `get_section_wise_stock_inout_details()` groups its result array with a key that **changes meaning based on GroupBy**:

```
GroupBy=1 → $stock_detail[$r['section_name']][][] = $r   ← key = section_name ✅
GroupBy=2 → $stock_detail[$r['section_name']][][] = $r   ← key = section_name ✅
GroupBy=3 → $stock_detail[$r['product_name']][$r['design_name']][] = $r  ← key = product_name ❌
```

The controller's flatten loop used the outer foreach key `$section_name` directly as the section label — but when GroupBy=3 that key is the **product name**, not the section name.

The raw `$product` row always contains `section_name` directly from the SQL `SELECT` clause, making the loop key unnecessary for this purpose.

## Detection
```powershell
# Find all places where the outer foreach key of a grouped model result is used as a label
grep -n "foreach.*\$list as \$section_name =>" admin/application/controllers/admin_ret_reports.php
# Then check if $section_name is written into a flattened array's 'section_name' key
grep -n "'section_name' => \$section_name" admin/application/controllers/admin_ret_reports.php
```

## Files
- `admin/application/controllers/admin_ret_reports.php` — `stock_details_print()` method

## Fix

### Before (WRONG — uses loop key which is wrong for GroupBy=3)
```php
$item_details[] = [
    'section_name' => $section_name,  // ← $section_name is the OUTER foreach KEY
    'product_name' => $product_label,
    ...
];
```

### After (CORRECT — reads from the raw model row)
```php
$item_details[] = [
    'section_name' => isset($product['section_name']) ? $product['section_name'] : $section_name,
    'product_name' => $product_label,
    ...
];
```

**Apply the same fix to non-tagged stock flatten block** if present (`$non_tag_items[]` array).

## Verification
1. Select GroupBy = "Design" (value=3) in the Section-wise Stock Details filter
2. Click Print
3. Open the PDF
4. Verify the label reads `"Section Name - Product Name"` (e.g., `"Counter 1 - Gold Chain"`)
5. NOT `"Gold Chain - Gold Chain"` (which would be the bug symptom)

## Notes
- This pattern applies to **any controller that flattens a model's nested group array** where the grouping key changes meaning based on user selection (e.g., GroupBy dropdowns)
- Always prefer reading from `$product['section_name']` (raw SQL row) rather than the PHP array key, since SQL always selects `sec.section_name` regardless of GROUP BY
- The isset() fallback preserves safety for older model versions that may not select `section_name`
