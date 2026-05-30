# Recipe: Section Stock Inout — AJAX Infinite Load (Date Parsing + Null Category)

## Metadata
- **Pattern ID**: PAT-RPT-002
- **Severity**: P1
- **Modules Affected**: Ret_Reports (Section Stock In/Out Report)
- **Auto-fixable**: Yes

## Client Scope
- Applies to: ALL
- Reason: `get_section_wise_stock_inout_details()` uses fragile `date_create()` + unguarded `implode` on missing POST key — both are systemic model bugs

## Created By
- Developer: Antigravity
- Client: etail_development_src
- Date: 2026-04-03
- Source Bug ID: RPT-CLT05

## Symptom
- Section-wise Stock Report (`/admin_ret_reports/section_stock_inout/list`) loads indefinitely
- Network tab shows AJAX request to `section_stock_inout/ajax?nocache=...` as **pending** forever
- No data appears in the report table

## Root Cause
Two bugs in `ret_reports_model.php::get_section_wise_stock_inout_details()`:

1. **Date parsing bug**: Uses `date_create($FromDt)` which cannot parse `DD-MM-YYYY` format. Returns `FALSE`. Subsequent `date_format(FALSE, "Y-m-d")` causes a PHP warning/error that corrupts the AJAX JSON response.

2. **Null `id_category` bug**: JS AJAX data payload does NOT include `id_category`. The model does `implode(',', $data['id_category'])` where `$data['id_category']` is `null`. PHP 8 throws `TypeError`.

## Detection
```powershell
findstr /n "function get_section_wise_stock_inout_details" "admin\application\models\ret_reports_model.php"
```
Then view the function body and check for bare `date_create()` and bare `implode(',',$data['id_category'])`.

## Files
- `admin/application/models/ret_reports_model.php` — function `get_section_wise_stock_inout_details()` (~L22858)

## Fix

### Before (date parsing, ~L22861-22866):
```php
$d1 = date_create($FromDt);
$d2 = date_create($ToDt);
$FromDt = date_format($d1,"Y-m-d");
$ToDt   = date_format($d2,"Y-m-d");
```

### After:
```php
$d1 = date_create_from_format("d-m-Y", $FromDt);
$FromDt = $d1 ? date_format($d1, "Y-m-d") : date('Y-m-d', strtotime($FromDt));
$d2 = date_create_from_format("d-m-Y", $ToDt);
$ToDt = $d2 ? date_format($d2, "Y-m-d") : date('Y-m-d', strtotime($ToDt));
```

### Before (id_category, ~L22884-22892):
```php
$multiple_id_cat = implode(',',$data['id_category']);
...
$id_category = $data['id_category'];
```

### After:
```php
$multiple_id_cat = (!empty($data['id_category']) && is_array($data['id_category'])) ? implode(',',$data['id_category']) : (string)($data['id_category'] ?? '');
...
$id_category = '';
```

## Verification
1. Navigate to `section_stock_inout/list`
2. Select a date range and click Search
3. Report table populates — no infinite spinner
4. `php -l admin/application/models/ret_reports_model.php` → No syntax errors

## Notes
- `get_nontag_section_details()` in the same file already uses the correct `date_create_from_format` pattern — this fix mirrors it.
- JS does NOT send `id_category` — model must handle null gracefully.
