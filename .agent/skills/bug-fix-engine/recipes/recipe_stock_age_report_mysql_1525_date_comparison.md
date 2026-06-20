# Recipe: Stock Age Report MySQL 1525 — DATE Column Empty String Comparison

> MySQL strict mode rejects `date_column != ''` comparisons on DATE fields, causing Error 1525: Incorrect DATE value: ''.

## Metadata
- **Pattern ID**: PAT-SQL-021
- **Severity**: HIGH
- **Modules Affected**: Reports (Stock Age / Tag Aging)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Any client running MySQL 8.x or strict `sql_mode` (`STRICT_TRANS_TABLES`, `NO_ZERO_DATE`) will hit this. Local XAMPP typically runs lenient mode, masking the bug until production.

## Created By
- **Developer**: AI-Assisted
- **Client**: Thambiyannan
- **Date**: 2026-06-20
- **Source Bug ID**: N/A

## Symptom
Stock Age report (`admin_ret_reports/stock_age/list`) throws:
```
A Database Error Occurred
Error Number: 1525
Incorrect DATE value: ''
```
The report works fine on local XAMPP but fails on the live server.

## Root Cause
The SQL query uses `IF(tag.old_tag_date != '', ...)` to check whether a DATE column has a value. In MySQL strict mode (default in MySQL 8.x), comparing a DATE column to an empty string `''` is invalid — MySQL attempts to cast `''` to a DATE, which fails with Error 1525.

**Why it works locally:** XAMPP MySQL typically runs with an empty or permissive `sql_mode` where `date('')` silently returns NULL.

**Why it fails on production:** Production MySQL has `STRICT_TRANS_TABLES` and/or `NO_ZERO_DATE` enabled, which rejects the implicit cast.

This is a recurring pattern wherever `date_column != ''` or `date_column = ''` appears in SQL queries. See also: `recipe_birthday_report_branchwise_and_mysql_1525_fix.md`.

## Detection
```command
grep -rn "old_tag_date!=''" admin/application/models/
grep -rn "old_tag_date=''" admin/application/models/
grep -rn "!=''" admin/application/models/ret_reports_model.php | grep -i date
```

**Broader scan** — find ALL date columns compared to empty strings:
```command
grep -rn "_date!=''" admin/application/models/
grep -rn "_date=''" admin/application/models/
grep -rn "_datetime!=''" admin/application/models/
```

## Files
- `admin/application/models/ret_reports_model.php`
  - Function: `get_stock_age_tag()` (~line 2540)
  - Function: `get_design_age_analysis_report()` (~line 2575)

## Fix

### Before
```php
if(tag.old_tag_date!='',DATEDIFF(date(now()),date(old_tag_date)),DATEDIFF(date(now()),date(tag_datetime))) AS age
```

### After
```php
if(tag.old_tag_date IS NOT NULL,DATEDIFF(date(now()),date(old_tag_date)),DATEDIFF(date(now()),date(tag_datetime))) AS age
```

### General Rule
| Bad Pattern | Good Replacement |
|---|---|
| `date_column != ''` | `date_column IS NOT NULL` |
| `date_column = ''` | `date_column IS NULL` |
| `IF(date_column != '', ...)` | `IF(date_column IS NOT NULL, ...)` |
| `IFNULL(date_column, '') != ''` | `date_column IS NOT NULL` |

## Verification
1. Open the Stock Age report on the live server: `admin_ret_reports/stock_age/list`
2. Select any branch, category, or product filter and submit
3. Report should load without database errors
4. Verify the age calculation is correct:
   - Tags with `old_tag_date` set: age = days between `old_tag_date` and today
   - Tags without `old_tag_date`: age = days between `tag_datetime` and today
5. Spot-check the tag detail popup to ensure individual tag ages match

## Notes
- The data in `ret_taging.old_tag_date` was verified clean (0 rows with empty strings) — the error is purely a SQL syntax/mode issue.
- This is the **same class of bug** as `recipe_birthday_report_branchwise_and_mysql_1525_fix.md`. Any time a DATE column is compared to `''` in SQL, it will fail on strict MySQL.
- When auditing other reports, scan for `!= ''` on any DATE/DATETIME column and replace with `IS NOT NULL`.
