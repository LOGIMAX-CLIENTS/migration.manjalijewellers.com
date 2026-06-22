# Recipe: Billing Eda Tax Calc Strict Mode Fix

## Metadata
- **Pattern ID**: PAT-DB-001
- **Severity**: HIGH
- **Modules Affected**: Ret_Billing
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Common SQL strict mode error for integer columns when empty string is passed instead of 0 or NULL.

## Created By
- **Developer**: Antigravity
- **Client**: erp.sriganeshjewels.com
- **Date**: 2026-06-15
- **Source Bug ID**: N/A

## Symptom
Saving a bill with bill_type = 2 fails with:
`Error Number: 1366`
`Incorrect integer value: '' for column 'eda_tax_calc' at row 1`

## Root Cause
In `admin_ret_billing.php` at line 1238, the value of `'eda_tax_calc'` is mapped directly to `$addData['is_eda_tax_calc']` without empty check. Under SQL strict mode, MySQL rejects inserting `''` (empty string) into an integer column `eda_tax_calc`.

## Detection
```command
grep -rn "'eda_tax_calc'      => \$addData\['is_eda_tax_calc'\]," admin/application/controllers/
```

## Files
- `admin/application/controllers/admin_ret_billing.php`

## Fix

### Before
```php
									'eda_tax_calc'      => $addData['is_eda_tax_calc'],
```

### After
```php
									'eda_tax_calc'      => ($addData['is_eda_tax_calc'] != '' ? $addData['is_eda_tax_calc'] : 0),
```

## Verification
1. Open the Billing screen.
2. Complete a transaction with bill_type = 2 where is_eda_tax_calc is empty/not active.
3. Save the bill and verify that it inserts successfully without strict mode warnings or errors.

## Notes
Similar to previous strict mode bugs on integer fields (e.g. metal_type).
