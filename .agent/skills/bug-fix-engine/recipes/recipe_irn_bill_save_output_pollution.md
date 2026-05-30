# Recipe: IRN generateEinvoice() Output Pollutes Bill Save AJAX Response

> When generateEinvoice() is called inline during bill save, its echo output corrupts the JSON response, preventing the bill print copy from opening.

## Metadata
- **Pattern ID**: PAT-BIL-IRN-001
- **Severity**: HIGH
- **Modules Affected**: Billing (ret_billing), IRN E-Invoice
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL (any client with IRN auto-generation enabled during bill save)
- **Reason**: The common_helper generateEinvoice() function uses echo+exit for standalone endpoint responses. When called inline during the bill save AJAX flow, the echoed JSON gets prepended to $return_data, producing invalid concatenated JSON.

## Created By
- **Developer**: Antigravity
- **Client**: vrsjewellery
- **Date**: 2026-04-20
- **Source Bug ID**: N/A

## Symptom
When saving a B2B bill with IRN auto-generation enabled (billing_for=2, bill_type in 1,2,4,9), the user sees "IRN generated successfully" text in the browser response instead of the bill print copy opening in a new tab. The bill is saved and IRN is generated correctly in the database, but the print popup never appears.

## Root Cause
The `generateEinvoice()` function in `common_helper.php` was designed for standalone controller endpoint use (`admin_ret_billing/generateEinvoice/{billId}`). It uses `echo json_encode(...)` to output results directly.

When called inline during the bill save flow in `admin_ret_billing.php` (case "save"), the function echoes its JSON response **before** the controller echoes `$return_data`. This produces concatenated JSON:

```
{"status":"success","message":"IRN generated..."}{"status":true,"id":"123","print_type":1}
```

jQuery's `dataType: "JSON"` cannot parse two concatenated JSON objects → the AJAX error callback fires → the print window (`window.open(...)`) is never called.

On error paths where `generateEinvoice()` calls `exit`, the script terminates entirely and `$return_data` is never echoed at all.

## Detection
```command
grep -n "generateEinvoice" admin/application/controllers/admin_ret_billing.php | grep -v "function\|//\|/\*"
```
Look for calls to `generateEinvoice()` that appear INSIDE the bill save case (between `case "save":` and the `echo json_encode($return_data)` line), where they are NOT wrapped in output buffering.

## Files
- `admin/application/controllers/admin_ret_billing.php` (bill save case — Auto IRN block)

## Fix

### Before
```php
					// Calls common_helper generateEinvoice() which handles:
					// is_auto_gen_irn mode, isProductionEnv(), validateIrnConfig(), per-branch GSP credentials
					generateEinvoice($insId);
```

### After
```php
					// Output buffering: generateEinvoice() echoes JSON for standalone endpoint.
					// Capture and discard to keep bill save AJAX response clean.
					ob_start();
					generateEinvoice($insId);
					ob_end_clean();
```

## Verification
1. Save a B2B bill (billing_for=2, bill_type 1/2/4/9) → bill print copy should open in a new tab
2. Save a B2C bill → print copy should open as before (no IRN triggered, unaffected path)
3. Check `ret_billing` table: `cusdel_irn` and `qrcodeimage` columns should still be populated for the saved bill
4. Test standalone IRN generation from reports page (`admin_ret_billing/generateEinvoice/{billId}`) → should still show JSON response directly (unaffected — standalone endpoint doesn't use output buffering)
5. Test with IRN errors (invalid GSP credentials, network failure) → bill should still save and redirect; IRN error is logged to `irn_error` column

## Notes
- The `ob_start()`/`ob_end_clean()` pattern is the minimal-impact fix. It captures any echo output from `generateEinvoice()` (including success, error, and debug messages) and discards it silently.
- If `generateEinvoice()` calls `exit` on an error path, the output buffer is automatically flushed by PHP's shutdown — but since the bill transaction is already committed before IRN is called, the bill data is safe.
- A more robust long-term fix would refactor `generateEinvoice()` to accept a `$silent` parameter and use `return` instead of `echo`/`exit` when called inline. However, that requires changes to `common_helper.php` which is shared across all clients — higher risk.
- Related pattern: PAT-JS-001 (AJAX form submit 302 redirect) — same class of issue where backend output format doesn't match frontend AJAX expectations.
