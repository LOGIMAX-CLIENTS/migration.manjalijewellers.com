# Recipe: Supplier Rate Cut Listing Empty on Page Load (Date Format Bug)

## Metadata
- **Pattern ID**: PAT-JS-DATE-001
- **Severity**: HIGH
- **Modules Affected**: ret_purchase / supplier_rate_cut (Approval to Invoice Conversion)
- **Auto-fixable**: Yes (string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients use the same `ret_purchase_order.js` base pattern for date initialization in listing pages. Any listing page that uses `new Date()` + manual string concatenation for `from_date`/`to_date` is affected.

## Created By
- **Developer**: Antigravity AI
- **Client**: amman (erp.sriammanjewellers.in)
- **Date**: 2026-04-24
- **Source Bug ID**: N/A

## Symptom
The Approval to Invoice Conversion listing page appears **completely empty** on page load. No records show even when data exists in the database. Manually clicking the Search button after selecting a date range works correctly.

## Root Cause
The JS `case 'list':` init block constructs `from_date` and `to_date` using manual string concatenation without zero-padding:

```javascript
var from_date = (firstDay.getDate() + "-" + (firstDay.getMonth() + 1) + "-" + firstDay.getFullYear());
// Output example: "24-4-2026"  ← single-digit month
```

This `D-M-YYYY` string is POSTed to the server and processed by PHP `strtotime()`:

```php
date('Y-m-d', strtotime($data['from_date']))
// strtotime("24-4-2026") → false (cannot parse single-digit month)
// date('Y-m-d', false)   → "1970-01-01"
```

The query then filters `WHERE date(src.date_add) BETWEEN '1970-01-01' AND '1970-01-01'` → zero rows → empty table.

The daterangepicker's callback uses `moment().format('YYYY-MM-DD')` which PHP parses correctly — which is why manual search works but auto-load does not.

## Detection
```powershell
# Find all listing pages with this broken date pattern
Select-String -Path "admin\assets\js\*.js" -Pattern "firstDay\.getDate\(\) \+ ""-"" \+ \(firstDay\.getMonth\(\)" -Recurse
```

Also check model files for unguarded `strtotime()`:
```powershell
Select-String -Path "admin\application\models\*.php" -Pattern "strtotime\(\\\$data\[" -Recurse
```

## Files
- `admin/assets/js/ret_purchase_order.js` (listing init block)
- `admin/application/models/ret_purchase_order_model.php` (defensive server-side fix)

## Fix

### JS Fix — Before
```javascript
var date = new Date();
var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 0, 1);
var from_date = (firstDay.getDate() + "-" + (firstDay.getMonth() + 1) + "-" + firstDay.getFullYear());
var to_date = (date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear());
```

### JS Fix — After
```javascript
// moment.js is already loaded as a dependency of daterangepicker
var from_date = moment().format('DD-MM-YYYY');
var to_date   = moment().format('DD-MM-YYYY');
```

### PHP Model Fix (Defensive) — Before
```php
".($data['from_date']!='' && $data['to_date']!='' ? " and (date(src.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')" :'')."
```

### PHP Model Fix (Defensive) — After
```php
".($data['from_date']!='' && $data['to_date']!='' ? (function() use ($data) {
    $from = DateTime::createFromFormat('d-m-Y', $data['from_date']) ?: new DateTime($data['from_date']);
    $to   = DateTime::createFromFormat('d-m-Y', $data['to_date'])   ?: new DateTime($data['to_date']);
    return " and (date(src.date_add) BETWEEN '".$from->format('Y-m-d')."' AND '".$to->format('Y-m-d')."')";
})() : '')."
```

## Verification
1. Navigate to the listing page **without clicking Search**
2. Verify that today's records load automatically
3. Add a new record → submit → verify it appears in the list immediately without manual search
4. Verify that manually selecting a date range in the daterangepicker still works correctly

## Notes
- `moment.js` is always available on pages that use `daterangepicker` — safe to use without additional import
- The model fix using `DateTime::createFromFormat('d-m-Y', ...)` is unambiguous and will NOT silently fall back to `1970-01-01` the way `strtotime()` does
- This same pattern exists in multiple listing pages in `ret_purchase_order.js` — search for all occurrences when applying
- The daterangepicker callback correctly uses `moment().format('YYYY-MM-DD')` — the bug is ONLY in the initial auto-load block
