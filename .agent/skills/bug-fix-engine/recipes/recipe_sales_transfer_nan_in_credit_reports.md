# Recipe: Sales Transfer Bills Causing NaN in Credit Issued & Cash Abstract Reports

## Metadata
- **Pattern ID**: PAT-RPT-ST-001
- **Severity**: HIGH
- **Modules Affected**: admin_ret_reports (Credit Issued, Cash Abstract, Credit History)
- **Auto-fixable**: Yes (SQL WHERE clause addition, no schema change)

## Client Scope
- **Applies to**: ALL retail jewellery clients using the Sales Transfer module
- **Reason**: Sales Transfer bills are created with `is_credit=1` and `bill_type=13` (Sales Return Transfer = 14) in `ret_billing`, but they have NO customer data (`bill_cus_id=NULL`, `cus_name=NULL`, `mobile=NULL`). Credit report queries filter by `is_credit=1` but previously only excluded `bill_type=8` and `bill_type=12`, allowing sales transfer records to leak into credit reports.

## Created By
- **Developer**: Antigravity
- **Client**: navratnajewellery (Navratna Jewellery LLP)
- **Date**: 2026-04-20
- **Source Bug ID**: RPT-CRD-001

## Symptom
1. **Credit Issued Report** (`/admin_ret_reports/credit_issued/list`) shows NaN values in Bill Amount, Paid Amount, Balance Amount columns.
2. **Cash Abstract Report** (`/admin_ret_reports/cash_abstract/list`) shows NaN in the Credit Issued section.
3. **Credit History Report** may also show NaN for the same reason (preventively fixed).
4. NaN appears specifically for dates/branches where sales transfers occurred.

## Root Cause
Sales Transfer bills are inserted into `ret_billing` with:
- `bill_type = 13` (Sales Transfer) / `14` (Sales Return Transfer)
- `is_credit = 1` (set so that credit tracking can occur between branches)
- `credit_status = 2`
- `bill_cus_id = NULL` — no customer linked (internal branch-to-branch transfer)
- No customer name, mobile, address populated

The credit report queries filter `WHERE b.is_credit=1` to find credit sales. The existing `bill_type!=8 AND bill_type!=12` exclusions correctly handle Credit Collections and another type, but do NOT exclude `bill_type=13` and `bill_type=14`.

When the JS processes these rows:
```javascript
bill_amount = parseFloat(bill_amount) + parseFloat(item.tot_bill_amount);
// tot_bill_amount can be NULL or the due_amt calculation returns NULL -> NaN
bal_amount = parseFloat(bal_amount) + parseFloat(item.due_amt);
```
The NULL customer fields cascade into the `due_amt` calculation, making JS emit `NaN` for totals.

## Detection
```bash
# Check if any sales transfer bills appear in credit report for a date range
# In MySQL:
SELECT bill_no, bill_type, is_credit, bill_cus_id, tot_bill_amount
FROM ret_billing
WHERE is_credit=1 AND bill_type IN (13, 14) AND bill_status=1;
# If rows returned -> this recipe applies
```

## Files
- `admin/application/models/ret_reports_model.php` — Three WHERE clause fixes

## Fix

### Fix 1: Cash Abstract — `getBillDetails` method (due_details query)

**Before** (~line 769):
```php
WHERE b.bill_id is not null and b.bill_type!=8 and b.bill_status=1 and b.is_credit=1
```

**After**:
```php
WHERE b.bill_id is not null and b.bill_type!=8 and b.bill_type!=13 and b.bill_type!=14 and b.bill_status=1 and b.is_credit=1
```

### Fix 2: Credit Issued Report — `getcreditBill` method (report_type==1, sql1 query)

**Before** (~line 1974):
```php
where  b.bill_id is not null and b.bill_status=1 and b.is_credit=1  and b.bill_type!=8 and b.bill_type!=12 
```

**After**:
```php
where  b.bill_id is not null and b.bill_status=1 and b.is_credit=1  and b.bill_type!=8 and b.bill_type!=12 and b.bill_type!=13 and b.bill_type!=14 
```

### Fix 3: Credit History Report — `getcreditBill_history` method (preventive)

**Before** (~line 2060):
```php
where  b.bill_id is not null and b.is_credit=1 and b.is_to_be=0 and b.bill_status=1  and b.bill_type!=8 and b.bill_type!=12
```

**After**:
```php
where  b.bill_id is not null and b.is_credit=1 and b.is_to_be=0 and b.bill_status=1  and b.bill_type!=8 and b.bill_type!=12 and b.bill_type!=13 and b.bill_type!=14
```

## Verification
1. Navigate to `/admin_ret_reports/credit_issued/list`
2. Select a date range and branch that previously showed NaN (e.g., 18-04-2026, Navratna Pattukkottai branch)
3. Click Search — confirm no NaN values appear in any column
4. Navigate to `/admin_ret_reports/cash_abstract/list`
5. Select same date + branch — confirm Credit Issued section shows valid numbers or zero (if no genuine credit sales)
6. Navigate to `/admin_ret_reports/credit_history/list` — confirm no NaN

## Notes
- `bill_type=13` = Sales Transfer (from `admin_ret_sales_transfer.php` line 120)
- `bill_type=14` = Sales Return Transfer (from `admin_ret_sales_transfer.php` line 320)
- Both are created with `is_credit=1` and `billing_for=3` (branch-to-branch), never tied to a customer
- The JavaScript NaN occurs because `parseFloat(null)` returns `NaN` in JavaScript, and once NaN enters a running total, all subsequent additions are also NaN
- This fix has zero risk — it adds a narrowing filter only, and sales transfers are internal operations that should never appear in customer-facing credit reports
