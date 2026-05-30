# Recipe: Old Metal Report – Duplicate Rows from Multi-Address Customer JOIN

## Metadata
- **Pattern ID**: PAT-RPT-007
- **Severity**: HIGH
- **Modules Affected**: Old Metal Purchase Report (`admin_ret_reports/old_metal_purchase`)
- **Auto-fixable**: Yes (string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: The `address` table allows multiple rows per customer across all clients. Any client using this report with customers having 2+ addresses will see duplicates.

## Created By
- **Developer**: Antigravity
- **Client**: erp.manepally.com (MPJ Panjaguta branch)
- **Date**: 2026-05-16
- **Source Bug ID**: N/A

## Symptom
In the Old Metal Purchase Report (detailed view / `report_type = 2`), a single old metal purchase transaction appears **multiple times** — once for each address record the customer has in the `address` table.

Example: Bill ID 186301 (customer A MANOHAR, id_customer 89656) had 2 address rows → appeared **twice** in the report. Grand Total was also doubled.

## Root Cause
`getOldMetalPurchases()` in `ret_reports_model.php` (`report_type == 2` branch) does a plain `LEFT JOIN address a on a.id_customer=c.id_customer`. Since the `address` table has no unique constraint on `id_customer`, customers with N addresses cause N duplicated rows per old metal transaction.

```
ret_bill_old_metal_sale_details: 1 row (old_metal_sale_id: 65156)
address: 2 rows for id_customer 89656
Result: 1 × 2 = 2 report rows (WRONG — should be 1)
```

The `report_type == 1` (summary) branch is **NOT affected** — it does not join the `address` table at all.

## Detection
```bash
grep -n "LEFT JOIN address a on a.id_customer" admin/application/models/ret_reports_model.php
```
Look for bare `LEFT JOIN address a on a.id_customer=c.id_customer` inside `getOldMetalPurchases()` without a `GROUP BY id_customer` subquery wrapper.

## Files
- `admin/application/models/ret_reports_model.php` — function `getOldMetalPurchases()`, `report_type == 2` branch (~line 101)

## Fix

### Before
```php
            LEFT JOIN customer c ON c.id_customer = bill.bill_cus_id
            LEFT JOIN address a on a.id_customer=c.id_customer
            LEFT JOIN state ste on ste.id_state=a.id_state
```

### After
```php
            LEFT JOIN customer c ON c.id_customer = bill.bill_cus_id
            LEFT JOIN (SELECT id_address, id_customer, address1, address2, address3, pincode, id_state FROM address GROUP BY id_customer) a on a.id_customer=c.id_customer
            LEFT JOIN state ste on ste.id_state=a.id_state
```

The `GROUP BY id_customer` subquery collapses all address rows for a customer into one, eliminating row multiplication. MySQL picks the first address row; address display is preserved.

## Verification
1. Load Old Metal Report (detailed view) for a date that includes a bill from a customer with 2+ addresses
2. Confirm the transaction appears exactly **once**
3. Confirm `cus_address` and `state_name` columns still display customer address data
4. Confirm Grand Total is correct (not doubled)
5. Test `report_type = 1` (summary) — should be unaffected

## Notes
- The `address` table commonly has 2+ rows per customer when customers update their address (old row kept, new row inserted).
- This same pattern risk exists in ANY report query that does a bare `LEFT JOIN address` without `GROUP BY id_customer`. Audit other reports if they show similar symptoms.
- Related pattern: PAT-DT-001 (DataTable footer column index mismatch) — unrelated but also in the reports module.
