# Recipe: Tally API — Inconsistent VoucherNumber Format Across APIs

## Metadata
- **Pattern ID**: PAT-TALLY-001
- **Severity**: HIGH
- **Modules Affected**: Tally API Integration (`ret_tally_api_model.php`)
- **Auto-fixable**: Yes — string concatenation delimiter change + fin_year_code injection

## Client Scope
- **Applies to**: ALL clients using the Tally API integration
- **Reason**: Any client where `ret_tally_api_model.php` exists and exports to Tally. The VoucherNumber format must be consistent across all APIs for Tally to match/deduplicate vouchers correctly.

## Created By
- **Developer**: Abinaya (abinaya@logimaxindia.com)
- **Client**: karpagamjewels.com
- **Date**: 2026-05-08
- **Source Bug ID**: Commit `1d439965` — branch `bugfix/b780095f-83de-485d-b5d5-f1402012eac0/tally-api-uniform-voucher-number-format`

## Symptom

Tally sync fails to correctly identify/match vouchers because the VoucherNumber format is inconsistent across API endpoints:

1. **Sales API** (`getSalesNewVoucherList`): Used `-` (dash) as separator instead of `/`
2. **Repair Sales API** (`getrepairsalesList`): Missing `fin_year_code` in VoucherNumber — only had `brcode-RO-bill_no`
3. **Sales Return (old) API** (`getsalesreturnList`): Missing `fin_year_code` — only had `brcode-SR-metal_code-invoice_no`

Tally expects the format `brcode/fin_year_code/invoice_no` (slash-separated). Dash-separated variants and missing year codes cause voucher mismatches and duplicate creation in Tally.

## Root Cause

1. **Wrong delimiter** — Sales API used `"-"` instead of `"/"` as separator between `brcode`, `fin_year_code`, and `cat_split_invoice_no`.
2. **Missing fin_year_code in SELECT** — Repair Sales SQL query did not include `b.fin_year_code` in the SELECT clause, so the value was unavailable when building `$invoice_string`.
3. **Missing fin_year_code in VoucherNumber** — Sales Return (old) API built the string without the financial year code segment, breaking the `brcode/year/ref` contract.

The standard format established across the new voucher-list POST APIs is:
```
brcode / fin_year_code / specific_invoice_ref
```
The old GET-based APIs were never updated to match this standard when `fin_year_code` was introduced.

## Detection

```powershell
# Find APIs still using dash-separated VoucherNumber with brcode (without fin_year_code)
Select-String -Path "application\models\ret_tally_api_model.php" `
  -Pattern '\$invoice_string\s*=\s*\$row->brcode\."\\-"' | Select LineNumber, Line

# Find APIs missing fin_year_code in invoice_string construction
Select-String -Path "application\models\ret_tally_api_model.php" `
  -Pattern 'invoice_string.*brcode.*(?!fin_year_code)' | Select LineNumber, Line
```

## Files

- `application/models/ret_tally_api_model.php`

## Fix

### Fix 1 — Sales API: Wrong delimiter (`getSalesNewVoucherList` — ~L643)

**Before:**
```php
$invoice_string = $row->brcode."-".$row->fin_year_code."-".$row->cat_split_invoice_no;
```

**After:**
```php
$invoice_string = $row->brcode."/".$row->fin_year_code."/".$row->cat_split_invoice_no;
```

---

### Fix 2 — Repair Sales API: Missing fin_year_code (`getrepairsalesList` — ~L3020)

**Before:**
```php
$invoice_string = $row->brcode."-"."RO"."-".$row->bill_no;
```

**After:**
```php
$invoice_string = $row->brcode."/".$row->fin_year_code."/RO-".$row->bill_no;
```

> Also requires adding `b.fin_year_code as fin_year_code` to the SELECT clause of the repair sales query (see Fix 2b below).

**Fix 2b — Add fin_year_code to SELECT in `getrepairsalesList` SQL (~L22301):**

**Before:**
```php
'' as SERVICEAMT,'Sundry Debtors' as LedgerParent, b.id_branch , b.bill_id, br.short_name as brcode, br.sort,
```

**After:**
```php
'' as SERVICEAMT,'Sundry Debtors' as LedgerParent, b.id_branch , b.bill_id, br.short_name as brcode, br.sort, b.fin_year_code as fin_year_code,
```

---

### Fix 3 — Sales Return (old) API: Missing fin_year_code (`getsalesreturnList` — ~L22361)

**Before:**
```php
$invoice_string = $row->brcode."-"."SR"."-".$row->metal_code."-".$row->INVOICENO;
```

**After:**
```php
$invoice_string = $row->brcode . "-" . $row->fin_year_code . "-" . "SR" . "-" . $row->INVOICENO;
```

> Note: This API uses `-` as separator (not `/`), consistent with the old GET API convention. The key fix here is adding `fin_year_code` to prevent year-blind voucher IDs. If this client migrates to slash format, change `-` to `/`.

## Verification

1. **Call each affected endpoint** and inspect the `VoucherNumber` field in the JSON response:
   - `GET /tally_app_api/importsales` → VoucherNumber should be `{brcode}/{fin_year_code}/{cat_split_invoice_no}` e.g. `KJH/2526/1423`
   - `GET /tally_app_api/importrepairsales` → VoucherNumber should be `{brcode}/{fin_year_code}/RO-{bill_no}` e.g. `KJH/2526/RO-203`
   - `GET /tally_app_api/importsalesreturn` → VoucherNumber should contain `fin_year_code` segment e.g. `KJH-2526-SR-GL-1234`

2. **Verify no double-prefix** — The `cat_split_invoice_no`, `bill_no`, `INVOICENO` columns should NOT already contain `brcode` or year prefix embedded; the model prepends these dynamically.

3. **Check Tally import log** — After sync, verify Tally does not create duplicate vouchers for the same transaction.

4. **Syntax check:**
```powershell
& "C:\laragon\bin\php\php8.2.26\php.exe" -l application/models/ret_tally_api_model.php
```
Expected: `No syntax errors detected`

## Notes

- The **standard VoucherNumber format** for all Tally API endpoints is: `{brcode}/{fin_year_code}/{invoice_ref}` using **forward slash** as separator. Only legacy GET-based APIs (pre-2024) may still use dash (`-`).
- `fin_year_code` is stored on `ret_billing.fin_year_code` and comes as a 4-digit code like `2526` (FY 2025–26). Use `RIGHT(b.fin_year_code, 2)` if only last 2 digits are needed.
- The new POST-based APIs (`importsalesvouchersList_post`, `importpurchasevouchersList_post`) already use the correct slash format — only the older GET APIs were affected.
- This fix affects **3 API functions** in the same file. All changes are in `ret_tally_api_model.php` only — no controller changes required.
- Related reference: `voucher_number_api_reference.md` in the project brain documents all 23 Tally endpoints and their VoucherNumber formats.
