# Recipe: get_title Wrong Argument Order Crashes Report DataTable

## Metadata
- **Pattern ID**: PAT-RPT-045
- **Severity**: HIGH
- **Modules Affected**: Reports (Online/Offline Payment Collection)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Any client using the Online/Offline Payment Collection Report will hit this bug

## Created By
- **Developer**: Antigravity
- **Client**: Manjali Jewellers
- **Date**: 2026-06-11
- **Source Bug ID**: N/A

## Symptom
The Online/Offline Payment Collection Report page at `reports/payment_online_offline_collec_data` shows an empty DataTable despite the API (`reports/payment_online_offline_collec_list`) returning valid data. No visible error message is shown to the user — the table silently appears empty after the loading overlay disappears.

## Root Cause
In the `generate_online_offline_collection` function within `reports.js`, the `get_title()` function is called with the wrong argument signature:

- **Expected**: `get_title(from_date, to_date, title)` — 3 parameters
- **Actual call**: `get_title("All Scheme Report As on Date")` — only 1 parameter, passed as `from_date`

This causes the `title` parameter inside `get_title()` to be `undefined`. The function then executes `title.toUpperCase()`, which throws a `TypeError: Cannot read properties of undefined (reading 'toUpperCase')`. This uncaught error kills JavaScript execution inside the AJAX success handler, AFTER the DataTable has already been cleared (`oTable.clear().draw()`), but BEFORE the new DataTable is rebuilt with the API data. Result: a permanently empty table.

## Detection
```command
grep -n "get_title(" admin/assets/js/reports.js | grep -v "function get_title"
```
Look for any `get_title()` calls that pass fewer than 3 arguments or pass the title string as the first argument instead of the third.

## Files
- `admin/assets/js/reports.js`

## Fix

### Before
```javascript
title += get_title("All Scheme Report As on Date");
```

### After
```javascript
title += get_title(selected_date, selected_date, "All Scheme Report As on Date");
```

## Verification
1. Navigate to `reports/payment_online_offline_collec_data`
2. Ensure the date input has a valid date with existing payments
3. The DataTable should populate with payment data grouped by payment mode
4. Check browser console — there should be no `TypeError` related to `toUpperCase`
5. Test both GST-enabled and non-GST scenarios (controlled by `chit_settings.gst_setting`)

## Notes
- The `get_title(from_date, to_date, title)` function is defined in multiple JS files (`reports.js`, `payment.js`, `customer.js`, `scheme_account.js`, `admin_settings.js`). All have the same 3-parameter signature.
- This is a silent failure pattern: the error occurs inside an AJAX success callback, so no visible error appears in the UI. Only the browser console shows the TypeError.
- Similar `get_title()` calls in other report functions should be audited for the same wrong-argument-order pattern.
- The `oTable.clear().draw()` call happening BEFORE the `get_title()` call is what makes this bug appear as "empty table" rather than "stale data" — the table is wiped but never repopulated.
