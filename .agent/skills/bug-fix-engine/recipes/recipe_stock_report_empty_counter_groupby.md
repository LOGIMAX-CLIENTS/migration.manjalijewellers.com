# Recipe: Stock Report Shows Empty Counters (group_by=3)

## Metadata
- **Pattern ID**: PAT-RPT-012
- **Severity**: MEDIUM
- **Modules Affected**: Stock Details Report (`admin_ret_reports/stock_details`)
- **Auto-fixable**: Yes (PHP-level filter — safe string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients use the same `get_stock_details()` function in `ret_reports_model.php` with the same `group_by=3` (Counter/Section) grouping logic.

## Created By
- **Developer**: Antigravity AI
- **Client**: avsrjwl
- **Date**: 2026-04-20
- **Source Bug ID**: N/A

## Symptom

When user runs the **Stock Details** report with `group_by = 3` (group by Counter/Section):

- Report shows **multiple counters** (e.g. COUNTER 1, COUNTER 3, COUNTER 4)
- Only **one counter** (e.g. COUNTER 4) actually has data (opening, inward, outward, closing)
- The other counters appear with **completely empty rows** — all zeros, no weight, no pieces
- User expects only counters with real stock activity to appear

## Root Cause

The `get_stock_details()` function in `ret_reports_model.php` builds its SQL query using:

```
FROM ret_taging t        ← BASE TABLE (no date filter)
LEFT JOIN <inward>       ← filtered by date range
LEFT JOIN <sold>         ← filtered by date range
LEFT JOIN <outward>      ← filtered by date range
...
```

**`ret_taging` is the physical master table of all tags** — it holds every tag ever
created across all counters, regardless of any date. The `WHERE` clause on the main
query only filters by `tag_id is not null`, optional product/category/metal —
**never by date or by section/counter**.

Because `LEFT JOIN`s return NULL when there's no match, and all columns use
`IFNULL(..., 0)`, tags from inactive counters still produce rows full of zeros.
These all-zero rows get grouped under their counter name:

```php
// group_by==3 path — no check if the row is empty
$stock_detail[$r['section_name']][$r['product_name']][] = $r;
```

So COUNTER 1 and COUNTER 3 appear as headers in the report even though they have
zero values for everything — because their tags exist in `ret_taging` and
the PHP code blindly adds every row without checking for all-zero data.

## Detection

```bash
grep -n "section_name" admin/application/models/ret_reports_model.php
```

Look for the line inside the `foreach($result as $r)` block in `get_stock_details()`:

```php
$stock_detail[$r['section_name']][$r['product_name']][] = $r;
```

If that line has **no `$hasData` guard** around it, the bug is present.

Also confirm the function's main WHERE clause:

```bash
grep -n "where t.tag_id is not null" admin/application/models/ret_reports_model.php
```

If that's the only filter on the base `ret_taging` table (no date/section condition),
the root cause is confirmed.

## Files

- `admin/application/models/ret_reports_model.php` — function `get_stock_details()`

## Fix

### Before

```php
$result = $sql->result_array();
foreach($result as $r){
    if($group_by==1 || $group_by==2){
        $stock_detail[$r['metal_name']][$r['category_name']][] = $r;
    }else if($group_by==3){
         $stock_detail[$r['section_name']][$r['product_name']][] = $r;
    }
    else{
        $stock_detail[$r['product_name']][$r['design_name']][] = $r;
    }
}
```

### After

```php
$result = $sql->result_array();
foreach($result as $r){
    if($group_by==1 || $group_by==2){
        $stock_detail[$r['metal_name']][$r['category_name']][] = $r;
    }else if($group_by==3){
        // Skip rows where all stock values are zero
        // Prevents blank counters (e.g. COUNTER 1, COUNTER 3) from showing up
        $hasData = (
            ($r['op_blc_pcs']  != 0) ||
            ($r['inw_pcs']     != 0) ||
            ($r['sold_pcs']    != 0) ||
            ($r['br_out_pcs']  != 0) ||
            ($r['closing_pcs'] != 0)
        );
        if($hasData){
            $stock_detail[$r['section_name']][$r['product_name']][] = $r;
        }
    }
    else{
        $stock_detail[$r['product_name']][$r['design_name']][] = $r;
    }
}
```

## Verification

1. Open Stock Details report → select a branch → set `group_by = 3` (Counter/Section)
2. Pick a date range where only **one specific counter** has transactions
3. Confirm that **only that counter** appears in the report — no empty counter headers
4. Also test with a date range where **multiple counters** have data — confirm all
   active counters still show correctly
5. Test `group_by = 1` (Product) and `group_by = 2` (Category) — confirm those views
   are unaffected by this change (the fix is isolated to the `group_by==3` branch)

## Notes

- This fix is applied at the **PHP level** (result processing), not in SQL.
  A `HAVING` clause would be the "correct" SQL fix but the query is hundreds of lines
  with multiple nested sub-queries — modifying it risks breaking other `group_by` modes.
- The `group_by==1` and `group_by==2` branches are **not affected** by this bug because
  their grouping key is `metal_name`/`category_name` — metals/categories with all-zero
  data are typically merged/hidden by the frontend rendering logic.
- The `closing_pcs` check is important: a counter could have opening balance
  (from previous day) with no new inward/outward — that is valid data and must NOT
  be filtered out. The check uses OR (||) so any non-zero value keeps the row.
- Related recipe: `recipe_section_stock_inout_date_and_category_fix.md` — covers
  a similar date-filter gap on the section stock in/out report.
