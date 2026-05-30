# Recipe: Credit Pending Bills Hidden by Hardcoded fin_year_code Filter

> Bill appears in DB with credit_status=2 (pending) but does NOT appear in billing form credit collection dropdown. And/or clicking search after selecting a bill gives "No record found for given details".

## Metadata
- **Pattern ID**: PAT-QUERY-002
- **Severity**: HIGH
- **Modules Affected**: Billing (Credit Collection)
- **Auto-fixable**: Yes (remove 3 lines across 2 functions)

## Client Scope
- **Applies to**: skjewels.in (confirmed), scan other clients where EDA clearance was run
- **Reason**: Hardcoded `fin_year_code != 2021` / `fin_year_code = $fin_year_code` filters added during EDA data cleanup work — leaked into production queries

## Created By
- **Developer**: Antigravity
- **Client**: skjewels.in
- **Date**: 2026-04-11
- **Source Bug ID**: N/A (direct report: "pending credit bill not showing in billing form")

## Symptom
**Step 1 symptom**: A specific customer has a pending credit collection bill (`credit_status=2`) but when selecting that customer in the billing form → CREDIT COLLECTION bill type, the bill does NOT appear in the BillNo dropdown. The bill is visible in DB with `bill_status=1`, `is_credit=1`, `credit_status=2`.

**Step 2 symptom** (after Step 1 is fixed OR when the bill's financial year differs from the FY selector): Selecting the bill from the dropdown and clicking the search button returns `{"success":false,"message":"No record found for given details"}`.

## Root Cause

**Fix 1 — `getCreditPending()`**: Two SQL queries (one for `col_type=1` Credit path, one for `col_type=2` Tube/is_to_be path) both contain:
```sql
AND b.fin_year_code != 2021
```
This permanently excludes ALL FY2021 bills from the dropdown, regardless of `credit_status`.

**Fix 2 — `getCreditBillDetails()`**: The search query contains:
```sql
AND b.fin_year_code = $fin_year_code
```
The JS sends `fin_year` from the form's FY selector (e.g., `FY26-27` = code `2027`), but the bill belongs to a different year (e.g., 2021). Year mismatch → no record found. The `fin_year_code` filter is redundant here — `sales_ref_no` + `id_branch` + `is_eda` already uniquely identifies the bill.

**Why these filters existed**: Added during EDA clearance work to prevent stale/ghost FY2021 data from appearing. They outlived their purpose and were never removed after clearance completed.

## Detection
```powershell
# Fix 1 — getCreditPending filter
Select-String -Path "admin\application\models\ret_billing_model.php" -Pattern "fin_year_code != 2021"

# Fix 2 — getCreditBillDetails filter
Select-String -Path "admin\application\models\ret_billing_model.php" -Pattern "fin_year_code=.*fin_year_code" | Where-Object { $_.Line -match "getCreditBillDetails|sales_ref_no" }
```

## Files
- `admin/application/models/ret_billing_model.php`
  - Function `getCreditPending($data)` — Fix 1 (2 removals)
  - Function `getCreditBillDetails(...)` — Fix 2 (1 removal)

## Fix

---

### Fix 1: `getCreditPending()` — col_type=1 path (~line 9240)

**Before:**
```php
where  b.bill_id is not null and b.is_credit=1 and b.is_to_be = 0 and  b.bill_status=1  and b.bill_type!=8 and b.credit_status=2 and b.bill_type !=12

AND b.fin_year_code != 2021

" . ($data['id_branch'] != '' && $data['id_branch'] > 0 ? ' and b.id_branch=' . $data['id_branch'] : '') . "
```

**After:**
```php
where  b.bill_id is not null and b.is_credit=1 and b.is_to_be = 0 and  b.bill_status=1  and b.bill_type!=8 and b.credit_status=2 and b.bill_type !=12

" . ($data['id_branch'] != '' && $data['id_branch'] > 0 ? ' and b.id_branch=' . $data['id_branch'] : '') . "
```

---

### Fix 1b: `getCreditPending()` — col_type=2 path (~line 9295)

**Before:**
```php
where  b.bill_id is not null and b.is_credit=1 and b.is_to_be=1 and  b.bill_status=1  and b.bill_type!=8 and b.credit_status=2 and b.bill_type !=12

AND b.fin_year_code != 2021

" . ($data['id_branch'] != '' && $data['id_branch'] > 0 ? ' and b.id_branch=' . $data['id_branch'] : '') . "
```

**After:**
```php
where  b.bill_id is not null and b.is_credit=1 and b.is_to_be=1 and  b.bill_status=1  and b.bill_type!=8 and b.credit_status=2 and b.bill_type !=12

" . ($data['id_branch'] != '' && $data['id_branch'] > 0 ? ' and b.id_branch=' . $data['id_branch'] : '') . "
```

---

### Fix 2: `getCreditBillDetails()` — SQL WHERE clause (~line 5574)

**Before:**
```php
where b.is_credit=1 AND b.bill_status = 1 and b.bill_type!=12 and b.credit_status=2 and b.sales_ref_no='$bill_no' and b.bill_type!=8 and b.fin_year_code=$fin_year_code

and b.is_eda=$is_eda
```

**After:**
```php
where b.is_credit=1 AND b.bill_status = 1 and b.bill_type!=12 and b.credit_status=2 and b.sales_ref_no='$bill_no' and b.bill_type!=8

and b.is_eda=$is_eda
```

---

## Verification
1. Find a customer with a pending credit bill from FY2021 (`fin_year_code=2021`, `credit_status=2`):
   ```sql
   SELECT bill_id, bill_no, fin_year_code, credit_status, bill_cus_id
   FROM ret_billing
   WHERE is_credit=1 AND bill_status=1 AND credit_status=2 AND fin_year_code=2021;
   ```
2. Open billing form → CREDIT COLLECTION → select that customer
3. Verify bill appears in BillNo dropdown ✅ (Fix 1)
4. Select the bill and click the search (🔍) button
5. Verify bill details load — no "No record found" error ✅ (Fix 2)
6. For skjewels.in: customer JEEVAN (mobile 9944808945), bill_id=29839, branch=VEPPUR — verified working

## Notes
- **Root cause of original filter**: EDA clearance (Aug 2020–Mar 2021) created FY2021 bills tagged as `is_eda=1`. After clearance the developer added `fin_year_code != 2021` to prevent stale cleared data from appearing, but never removed it. 10 genuinely pending FY2021 bills were collateral damage.
- **Why `fin_year_code` is redundant in `getCreditBillDetails`**: `sales_ref_no` is unique per branch per FY, but `is_eda` + `id_branch` already scope it correctly. The FY filter breaks cross-year lookups when the form's FY selector doesn't match the bill's year.
- **Impact on skjewels.in**: 10 pending FY2021 credit bills were invisible before fix. All restored correctly.
- **Fingerprint**: Scan other clients for `fin_year_code != 2021` in `getCreditPending` and `fin_year_code=.*fin_year_code` in `getCreditBillDetails`.
