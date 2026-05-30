# Recipe: Credit Pending Report — Negative Balance Display + Undefined Bill Link

## Metadata
- **Pattern ID**: PAT-RPT-CRPEND-001
- **Severity**: MEDIUM
- **Modules Affected**: Reports (Credit Pending)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Both bugs exist in the source repository (etail_development_src). All clients inheriting `get_credit_pending_details()` and `getCreditCollection()` have them.

## Created By
- **Developer**: Antigravity
- **Client**: pondy (pondythangamaaligai.com)
- **Date**: 2026-04-18
- **Source Bug ID**: N/A

## Symptom

### Bug 1: Negative balance amounts displayed
The Credit Pending report shows rows with negative balance amounts (where customer has overpaid). Only rows with `bal_amt - paid_amount > 0` should appear.

### Bug 2: Bill link shows "undefined"
Clicking a bill number in the expanded "paid details" sub-table navigates to a URL containing `undefined` (e.g., `admin_ret_reports/credit_pending/undefined`) instead of opening the actual billing invoice.

## Root Cause

### Bug 1: Missing balance filter
In `get_credit_pending_details()`, the `foreach` loop pushes ALL rows into the result array regardless of balance. The source version has an `if(((double)$r['bal_amt'] - (double)$paid_amount) > 0)` guard that filters out fully-paid or overpaid rows. The pondy version was missing this guard.

### Bug 2: Inverted PHP array key-value
In `getCreditCollection()`, the PHP array has:
```php
'0' => 'type'
```
This creates a numeric key `0` with string value `"type"`. JSON output: `{"0":"type",...}`.

The JS function `fnFormatRowCreditDetails` checks `val.type == 0` to build the URL, but `val.type` is `undefined` because the JSON property is keyed as `"0"`, not `"type"`. Since neither condition matches, the `url` variable is never assigned → `href="undefined"`.

## Detection
```command
# Bug 1: Missing balance filter — look for credit_detail[] without if guard
grep -n "credit_detail\[\]" admin/application/models/ret_reports_model.php

# Bug 2: Inverted type key
grep -rn "'0'.*=>.*'type'" admin/application/models/ret_reports_model.php
```

## Files
- `admin/application/models/ret_reports_model.php` — `get_credit_pending_details()` and `getCreditCollection()` methods

## Fix 1: Add Balance Amount Filter

### Before
```php
foreach($result as $r){
    $paid_amount=$this->get_credit_collection_details($r['bill_id']);
    $credit_detail[] = array(
                        'type'              =>0,
                        'bill_no'           =>$r['bill_no'],
                        // ... rest of array ...
                        'credit_collection' =>$this->getCreditCollection($r['bill_id'])
                    );
}
```

### After
```php
foreach($result as $r){
    $paid_amount=$this->get_credit_collection_details($r['bill_id']);
    if(((double)$r['bal_amt'] - (double)$paid_amount) > 0){
    $credit_detail[] = array(
                        'type'              =>0,
                        'bill_no'           =>$r['bill_no'],
                        // ... rest of array ...
                        'credit_collection' =>$this->getCreditCollection($r['bill_id'])
                    );
    }
}
```

## Fix 2: Correct Inverted Type Key

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
2. Search for records — verify **no negative balance amounts** appear in the Balance Amount column
3. Click the expand chevron (▼) on any row with paid details
4. Click a bill number in the expanded sub-table
5. Verify it opens the correct billing invoice URL (`admin_ret_billing/billing_invoice/{bill_id}`)
6. Confirm the URL does **NOT** contain "undefined"

## Notes
- Bug 2 is a latent bug present in BOTH source (etail_development_src) and all client repos
- The key was a typo during original development — `'0' => 'type'` vs `'type' => 0`
- The `fnFormatRowCreditDetails` JS function (line ~13499 in ret_reports.js) is shared across multiple DataTable versions, so fixing the model data fixes all of them
- Both fixes are in the same file (`ret_reports_model.php`) but in different methods
