# Recipe: Credit Pending Report — Inverted Type Key in getCreditCollection

## Metadata
- **Pattern ID**: PAT-RPT-KEYVAL-001
- **Severity**: MEDIUM
- **Modules Affected**: Reports (Credit Pending)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: The bug exists in the source repository — all clients inheriting getCreditCollection have it.

## Created By
- **Developer**: Antigravity
- **Client**: pondy (pondythangamaaligai.com)
- **Date**: 2026-04-18
- **Source Bug ID**: N/A

## Symptom
When clicking a bill number in the Credit Pending report's "paid details" expanded row, the browser navigates to a URL containing "undefined" (e.g., `admin_ret_reports/credit_pending/undefined`) instead of opening the actual billing invoice.

## Root Cause
In `getCreditCollection()`, the PHP array has an inverted key-value pair:
```php
'0' => 'type'
```
This creates a numeric key `0` with string value `"type"`. When JSON-encoded, it becomes `{"0":"type",...}`.

The JavaScript function `fnFormatRowCreditDetails` checks `val.type == 0` to determine the URL, but `val.type` is `undefined` because the JSON property is `"0"` (not `"type"`). Since neither `val.type == 0` nor `val.type == 1` matches, the `url` variable is never assigned, resulting in `href="undefined"`.

## Detection
```command
grep -rn "'0'.*=>.*'type'" admin/application/models/ret_reports_model.php
```

## Files
- `admin/application/models/ret_reports_model.php` — `getCreditCollection()` method

## Fix

### Before
```php
$return_data[]=array(
                     '0'                =>'type',
                     'bill_no'          =>$item['bill_no'],
```

### After
```php
$return_data[]=array(
                     'type'             =>0,
                     'bill_no'          =>$item['bill_no'],
```

## Verification
1. Navigate to Credit Pending report (`admin_ret_reports/credit_pending/list`)
2. Search for records with credit collections
3. Click the expand chevron (▼) on any row with paid details
4. Click a bill number in the expanded sub-table
5. Verify it opens the correct billing invoice URL (`admin_ret_billing/billing_invoice/{bill_id}`)
6. Confirm the URL does NOT contain "undefined"

## Notes
- This is a latent bug present in BOTH source (etail_development_src) and client repos
- The key was likely a typo during original development — `'0' => 'type'` vs `'type' => 0`
- The `fnFormatRowCreditDetails` JS function is shared across multiple versions of the credit pending DataTable, so fixing the model fixes all of them
