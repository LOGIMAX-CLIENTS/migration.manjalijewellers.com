# Recipe Template

## Metadata
- **Pattern ID**: PAT-RPT-001
- **Severity**: HIGH
- **Modules Affected**: ret_reports, supplier_approval_transaction
- **Auto-fixable**: No

## Client Scope
- **Applies to**: ALL
- **Reason**: Common discrepancy between frontend datepicker defaults and backend parsing methods.

## Created By
- **Developer**: Antigravity
- **Client**: erp.sparqlediamonds.com
- **Date**: 2026-06-16
- **Source Bug ID**: N/A

## Symptom
User gets a database error `Incorrect DATE value: ''` when attempting to load the Supplier Approval Ledger Report.

## Root Cause
The AJAX endpoint `get_SupplierApprovalTransactions` calls two methods in `ret_reports_model.php`:
1. `getSupplierApprovalTransactionList` expected `MM/DD/YYYY`.
2. `getMetalwiseApprovalTransactionList` expected `DD-MM-YYYY`.
Because both methods used the same frontend date payload, satisfying one method caused the other to fail parsing. The failing method triggered `date_format(false, "Y-m-d")` which returned false/empty string, injecting an empty string into the MySQL query.

## Detection
```command
Select-String -Pattern "getSupplierApprovalTransactionList" admin/application/models/ret_reports_model.php
```

## Files
- `admin/application/views/ret_reports/supplier_approval_transaction.php`
- `admin/assets/js/ret_reports.js`
- `admin/application/models/ret_reports_model.php`

## Fix

### Before
```php
        if($_POST['dt_range'] != ''){
            $dateRange = explode('-',$_POST['dt_range']);
            $from = str_replace('/','-',$dateRange[0]);
            $to = str_replace('/','-',$dateRange[1]);
            $d1 = date_create($from);
            $d2 = date_create($to);
            $FromDt = date_format($d1,"Y-m-d");
            $ToDt = date_format($d2,"Y-m-d");
        }
```

### After
```php
        if($_POST['dt_range'] != ''){
            $dateRange = explode(' - ',$_POST['dt_range']);
            $d1 = DateTime::createFromFormat('m/d/Y', trim($dateRange[0]));
            $d2 = DateTime::createFromFormat('m/d/Y', trim($dateRange[1]));
            $FromDt = $d1 ? $d1->format("Y-m-d") : date("Y-m-d");
            $ToDt = $d2 ? $d2->format("Y-m-d") : date("Y-m-d");
        }
```

## Verification
1. Open the Supplier Approval Ledger Report.
2. Select a valid Karigar/Supplier.
3. Click "Search" and verify no `Incorrect DATE value: ''` error is thrown.
4. Verify the dates in the report title match the selected dates.

## Notes
Ensure that the frontend JS handles the date payload reliably. We added logic to intercept empty values and default to today's date in `MM/DD/YYYY - MM/DD/YYYY` format inside `ret_reports.js` before dispatching the AJAX request.
