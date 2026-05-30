# Scan Report — Unscanned Filter Missing Date Range

> Unscanned filter in Tag Scanned Details report returns zero results because the NOT IN subquery excludes ALL ever-scanned tags regardless of date selection.

## Metadata
- **Pattern ID**: PAT-RPT-DATEFILTER-001
- **Severity**: HIGH
- **Modules Affected**: Retail Reports (Scan Report / Scanned Details)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core report model bug — any client using the Tag Scanned Details report with the UnScanned filter will be affected.

## Created By
- **Developer**: Antigravity
- **Client**: RTM_newversion (source)
- **Date**: 2026-05-13
- **Source Bug ID**: N/A

## Symptom
On the **Tag Scanned Details** report (`admin_ret_reports/scan_report/scanned_details`):
1. Select a specific date (e.g., 05/05/2026) that has scanned items
2. Set **Report Type** = **Scanned** → Returns correct results (e.g., 94 items)
3. Set **Report Type** = **UnScanned** → Returns **zero results** ("none")
4. However, there ARE unscanned products on that date (e.g., 5 items)

The unscanned filter always shows empty results even when unscanned items exist.

## Root Cause
In `get_TagScannedDetails()`, the scanned query (`report_type==1`) correctly filters by date range:
```php
and (date(s.from_time) BETWEEN "$FromDt" AND "$ToDt")
```

But the unscanned query (`report_type==2` / `else` branch) has a `NOT IN` subquery that is **missing the date range filter**. This causes it to exclude ALL tags that were EVER scanned on ANY date — not just tags scanned within the selected date range.

Additionally, the subquery uses a `LEFT JOIN` which can produce NULL `tag_id` values. In MySQL, `NOT IN` with NULLs in the subquery result returns no rows (because `x NOT IN (1, 2, NULL)` evaluates to UNKNOWN, not TRUE).

**Two issues combined:**
1. Missing date range filter → excludes too many tags
2. Missing `IS NOT NULL` guard → NULL from LEFT JOIN poisons the entire NOT IN result

## Detection
```command
grep -n "tag.tag_id not in" admin/application/models/ret_reports_model.php
```
Look for the `NOT IN` subquery inside `get_TagScannedDetails()` that lacks a `from_time` date filter. The scanned branch (report_type==1) will have `date(s.from_time) BETWEEN` but the else branch will not.

## Files
- `admin/application/models/ret_reports_model.php` — method `get_TagScannedDetails()`

## Fix

### Before
```php
            WHERE tag.tag_id not in(SELECT t.tag_id
            FROM ret_tag_scan s
            LEFT JOIN ret_tag_scanned t ON t.id_scanned=s.id_scanned
            WHERE s.status=1
            ".($id_branch!='' && $id_branch!=0 ? " and s.id_branch in(".$id_branch.")" :'')."
            ".($id_product!='' && $id_product!=0 ? " and s.id_product in (".$id_product.")" :'')."
            ".($id_section!='' && $id_section!=0 ? " and s.id_section in (".$id_section.")" :'')."
```

### After
```php
            WHERE tag.tag_id not in(SELECT t.tag_id
            FROM ret_tag_scan s
            LEFT JOIN ret_tag_scanned t ON t.id_scanned=s.id_scanned
            WHERE s.status=1 AND t.tag_id IS NOT NULL
            ".($FromDt!= '' && $ToDt!='' ? ' and (date(s.from_time) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."
            ".($id_branch!='' && $id_branch!=0 ? " and s.id_branch in(".$id_branch.")" :'')."
            ".($id_product!='' && $id_product!=0 ? " and s.id_product in (".$id_product.")" :'')."
            ".($id_section!='' && $id_section!=0 ? " and s.id_section in (".$id_section.")" :'')."
```

### Changes Summary
| # | Change | Reason |
|---|--------|--------|
| 1 | Added `AND t.tag_id IS NOT NULL` | Prevents NULL tag_ids from LEFT JOIN from poisoning the `NOT IN` clause (MySQL treats `NOT IN` with NULL as UNKNOWN → returns no rows) |
| 2 | Added `date(s.from_time) BETWEEN "$FromDt" AND "$ToDt"` | Filters exclusion to only tags scanned within the selected date range, matching the scanned query's logic |

## Verification
1. Navigate to `admin_ret_reports/scan_report/scanned_details`
2. Select a date that has both scanned and unscanned items
3. Set **Report Type = Scanned** → note the count (e.g., 94)
4. Set **Report Type = UnScanned** → should now show the unscanned items (e.g., 5) instead of "none"
5. **Edge case**: Test with a date range spanning multiple days — both scanned and unscanned should reflect only items within that range
6. **Edge case**: Test with no date range filter cleared — should still work correctly
7. **Edge case**: Test with branch/section/product filters combined with UnScanned — all filters should apply correctly

## Notes
- This is a classic **asymmetric filter** bug: two branches of the same function use different filter criteria. When adding a filter to one code path, always check if the corresponding "inverse" code path needs the same filter.
- The `NOT IN` + `NULL` gotcha is a recurring MySQL antipattern. Any `NOT IN` subquery using LEFT JOIN should always include `IS NOT NULL` on the selected column. Consider this when auditing similar queries.
- Related: `get_unscanned_details()` at line ~4234 has a similar `NOT IN` pattern but it does NOT use date filtering by design (it's for the scan summary tab, not the details tab). Do NOT apply this fix there.
