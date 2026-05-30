# Recipe: Billing Financial Year Sequence Corruption

## Metadata
- **Pattern ID**: PAT-BIL-FY01
- **Severity**: CRITICAL
- **Modules Affected**: Billing (ret_billing), Issue/Receipt (ret_issue_receipt), Customer Orders (customerorder)
- **Auto-fixable**: No (requires manual SQL execution with backup)

## Client Scope
- **Applies to**: ALL
- **Reason**: Any client where fin_year_code is accidentally changed mid-year in ret_financial_year (fin_status toggled incorrectly)

## Created By
- **Developer**: Antigravity AI
- **Client**: chinnannan jewellery (www.chinnannanjewellery.com)
- **Date**: 2026-04-09
- **Source Bug ID**: N/A

---

## Symptom

Two types of accidental modifications to `fin_year_code` in `ret_financial_year` (fin_status toggled wrong during active billing hours) caused:

1. Transactions recorded with the **wrong FY code** (old FY instead of current FY)
2. Billing sequences (`bill_no`, `sales_ref_no`, `pur_ref_no`, etc.) **became contaminated** — old FY numbers continued into new FY records and new FY numbers started from 1 mid-sequence
3. `customerorder.order_no` has FY code **embedded in the string** (`GNM25-OR-01053`) so wrong orders are visible with wrong year label
4. **Duplicate key error** `#1062` when trying to renumber — unique index on `(fin_year_code, bill_no, id_branch, is_eda)` causes mid-UPDATE collisions

---

## Root Cause

- `ret_financial_year.fin_status` was toggled incorrectly during active billing sessions on two occasions
- `get_FinancialYear()` / `get_last_code_no()` / `generateRefNo()` / `generateOrderNo()` all query `fin_status=1` to determine current FY at save time
- Once fin_status is wrong, ALL subsequent bills/orders/receipts are tagged to wrong FY
- Sequence generators use `ORDER BY bill_id DESC LIMIT 1` filtered by `fin_year_code` — so corrupted FY records break the sequence for the correct FY

---

## Sequence Key Partitions (Critical for Fix Design)

### ret_billing
| Sequence | Key Columns | Notes |
|---|---|---|
| `bill_no` | `(fin_year_code, is_eda, id_branch)` | SHARED across ALL bill_types |
| `sales_ref_no` | `(fin_year_code, field, is_eda, id_branch)` | Independent per field |
| `pur_ref_no` | same | Independent |
| `order_adv_ref_no` | same | Independent |
| `s_ret_refno` | same | Independent |
| `credit_coll_refno` | same | Independent |
| `chit_preclose_refno` | same | Independent |
| `repair_del_ref_no` | same | Independent |
| `approval_ref_no` | same | Independent |

**`metal_type` does NOT partition sequences** — PHP loose comparison `0 == ''` means `metal_type=0` is treated as empty and excluded from the SQL WHERE clause.

### ret_issue_receipt
| Sequence | Key Columns |
|---|---|
| `bill_no` | `(fin_year_code, is_eda, id_branch)` |

### customerorder
| Sequence | Key Columns | Format |
|---|---|---|
| `order_no` | `(fin_year_code, order_type, order_from)` | `{branch_code}{fin_year_code}-{type_code}-{5digits}` e.g. `GNM26-OR-00001` |

---

## Detection

```sql
-- Check for wrong-FY records after April 1 (new FY start)
SELECT fin_year_code, DATE(bill_date), COUNT(*), MIN(bill_id), MAX(bill_id)
FROM ret_billing
WHERE bill_date >= '{NEW_FY_START_DATE}'
GROUP BY fin_year_code, DATE(bill_date)
ORDER BY DATE(bill_date), fin_year_code;

-- Same for customerorder
SELECT fin_year_code, DATE(order_date), COUNT(*)
FROM customerorder
WHERE order_date >= '{NEW_FY_START_DATE}'
GROUP BY fin_year_code, DATE(order_date)
ORDER BY DATE(order_date);
```

**The cutoff ID** = lowest `bill_id` that belongs to the new FY (first correct new-FY bill).

---

## Affected Tables (Dependency Map)

| Table | Column | Fix Type |
|---|---|---|
| `ret_billing` | `fin_year_code`, all seq fields | PRIMARY — fix FY + regenerate sequences |
| `ret_issue_receipt` | `fin_year_code`, `bill_no` | Fix FY + regenerate bill_no |
| `customerorder` | `fin_year_code`, `order_no` | Fix FY + regenerate order_no string |
| `customerorderdetails` | `orderno` | CASCADE — update string prefix to match parent |
| `ret_billing_advance` | `order_no` | CASCADE — update string to match parent |
| `ret_bill_details` | `order_no` | Check — usually 0 rows affected |
| `ret_grn_entry`, `ret_lot_inwards`, `ret_estimation_items` | various | Check — usually 0 rows affected |

**Do NOT change**: `ref_bill_id` (stores `bill_id` PK — not a sequence number)

---

## Critical Fix Technique: Two-Pass Update

> **Direct renumbering fails** with `#1062 Duplicate entry` because the unique index `(fin_year_code, bill_no, id_branch, is_eda)` fires mid-UPDATE when a target number already exists in a later row.

**Solution: Shift to safe temp range first, then renumber.**

Safe temp range = `90000+` (max historical bill_no is ~35000, so no collision)

```sql
-- PASS 1: shift to temp range (no collision possible)
SET @row_num = 90000;
UPDATE ret_billing 
SET bill_no = LPAD(@row_num := @row_num + 1, 5, '0')
WHERE bill_id >= {CUTOFF_BILL_ID} AND is_eda = {EDA} AND id_branch = {BRANCH}
ORDER BY bill_id;

-- PASS 2: renumber from 1 (temp range has no conflicts with itself)
SET @row_num = 0;
UPDATE ret_billing 
SET bill_no = LPAD(@row_num := @row_num + 1, 5, '0')
WHERE bill_id >= {CUTOFF_BILL_ID} AND is_eda = {EDA} AND id_branch = {BRANCH}
ORDER BY bill_id;
```

Apply same two-pass pattern to ALL ref_no fields and `ret_issue_receipt.bill_no`.

---

## Fix Steps (Ordered — Do NOT Skip or Reorder)

> **BACKUP DATABASE FIRST. All steps must run in this exact order.**

### Phase 1: ret_billing

```sql
-- Step 1: Fix fin_year_code (MUST be first)
UPDATE ret_billing 
SET fin_year_code = '{CORRECT_FY_CODE}' 
WHERE bill_id >= {CUTOFF_BILL_ID} AND fin_year_code = '{WRONG_FY_CODE}';

-- Step 2a/2b: bill_no for is_eda=1 (two-pass)
SET @row_num = 90000;
UPDATE ret_billing SET bill_no = LPAD(@row_num := @row_num + 1, 5, '0')
WHERE bill_id >= {CUTOFF_BILL_ID} AND is_eda = 1 AND id_branch = {BRANCH} ORDER BY bill_id;

SET @row_num = 0;
UPDATE ret_billing SET bill_no = LPAD(@row_num := @row_num + 1, 5, '0')
WHERE bill_id >= {CUTOFF_BILL_ID} AND is_eda = 1 AND id_branch = {BRANCH} ORDER BY bill_id;

-- Step 3a/3b: bill_no for is_eda=2 (two-pass) — if applicable
SET @row_num = 90000;
UPDATE ret_billing SET bill_no = LPAD(@row_num := @row_num + 1, 5, '0')
WHERE bill_id >= {CUTOFF_BILL_ID} AND is_eda = 2 AND id_branch = {BRANCH} ORDER BY bill_id;

SET @row_num = 0;
UPDATE ret_billing SET bill_no = LPAD(@row_num := @row_num + 1, 5, '0')
WHERE bill_id >= {CUTOFF_BILL_ID} AND is_eda = 2 AND id_branch = {BRANCH} ORDER BY bill_id;

-- Steps 4-14: Repeat two-pass pattern for each ref_no field:
-- sales_ref_no (eda=1), sales_ref_no (eda=2),
-- pur_ref_no (eda=1), pur_ref_no (eda=2),
-- order_adv_ref_no, s_ret_refno, credit_coll_refno (eda=1),
-- credit_coll_refno (eda=2), chit_preclose_refno, repair_del_ref_no, approval_ref_no
-- Template for each:
SET @row_num = 90000;
UPDATE ret_billing SET {REF_FIELD} = LPAD(@row_num := @row_num + 1, 5, '0')
WHERE bill_id >= {CUTOFF_BILL_ID} AND is_eda = {EDA} AND id_branch = {BRANCH} 
  AND {REF_FIELD} IS NOT NULL ORDER BY bill_id;

SET @row_num = 0;
UPDATE ret_billing SET {REF_FIELD} = LPAD(@row_num := @row_num + 1, 5, '0')
WHERE bill_id >= {CUTOFF_BILL_ID} AND is_eda = {EDA} AND id_branch = {BRANCH} 
  AND {REF_FIELD} IS NOT NULL ORDER BY bill_id;
```

### Phase 2: ret_issue_receipt

```sql
-- Step 15: Fix fin_year_code
UPDATE ret_issue_receipt 
SET fin_year_code = '{CORRECT_FY_CODE}' 
WHERE fin_year_code = '{WRONG_FY_CODE}' AND bill_date >= '{NEW_FY_START_DATE}';

-- Steps 16a/16b: Regenerate bill_no (two-pass, per is_eda partition)
SET @row_num = 90000;
UPDATE ret_issue_receipt SET bill_no = LPAD(@row_num := @row_num + 1, 5, '0')
WHERE fin_year_code = '{CORRECT_FY_CODE}' AND is_eda = 1 AND id_branch = {BRANCH}
ORDER BY id_issue_receipt;

SET @row_num = 0;
UPDATE ret_issue_receipt SET bill_no = LPAD(@row_num := @row_num + 1, 5, '0')
WHERE fin_year_code = '{CORRECT_FY_CODE}' AND is_eda = 1 AND id_branch = {BRANCH}
ORDER BY id_issue_receipt;
```

### Phase 3: customerorder + cascades

```sql
-- Step 17: Fix fin_year_code using cutoff ID (catches any newly added wrong records)
UPDATE customerorder 
SET fin_year_code = '{CORRECT_FY_CODE}' 
WHERE id_customerorder >= {CUTOFF_ORDER_ID} AND fin_year_code = '{WRONG_FY_CODE}';

-- Step 18: Regenerate order_no (note: FY code embedded in the string)
SET @row_num = 0;
UPDATE customerorder 
SET order_no = CONCAT('{BRANCH_CODE}{CORRECT_FY_CODE}-OR-', LPAD(@row_num := @row_num + 1, 5, '0'))
WHERE id_customerorder >= {CUTOFF_ORDER_ID} AND order_from = {BRANCH} AND order_type = 2
ORDER BY id_customerorder;

-- Step 19: Cascade to customerorderdetails (orderno = order_no + '-' + item_index)
UPDATE customerorderdetails cod
INNER JOIN customerorder co ON co.id_customerorder = cod.id_customerorder
SET cod.orderno = CONCAT(co.order_no, '-', SUBSTRING_INDEX(cod.orderno, '-', -1))
WHERE co.id_customerorder >= {CUTOFF_ORDER_ID};

-- Step 20: Cascade to ret_billing_advance
UPDATE ret_billing_advance ba
INNER JOIN customerorder co ON co.id_customerorder = ba.id_customerorder
SET ba.order_no = co.order_no
WHERE ba.id_customerorder >= {CUTOFF_ORDER_ID};
```

---

## Verification Queries

```sql
-- 1. No wrong-FY April bills
SELECT COUNT(*) as must_be_zero FROM ret_billing 
WHERE bill_date >= '{NEW_FY_START_DATE}' AND fin_year_code != '{CORRECT_FY_CODE}';

-- 2. bill_no starts at 1, max = count (no gaps) per is_eda
SELECT is_eda, MIN(CAST(bill_no AS UNSIGNED)) as min_bn, 
       MAX(CAST(bill_no AS UNSIGNED)) as max_bn, COUNT(*) as total
FROM ret_billing WHERE fin_year_code='{CORRECT_FY_CODE}' AND id_branch={BRANCH}
GROUP BY is_eda;
-- PASS condition: min_bn=1 AND max_bn=total (gapless)

-- 3. Gap check for bill_no
SELECT COUNT(*) as must_be_zero FROM (
  SELECT CAST(bill_no AS UNSIGNED) as bn,
         LAG(CAST(bill_no AS UNSIGNED)) OVER (PARTITION BY is_eda ORDER BY bill_id) as prev
  FROM ret_billing WHERE fin_year_code='{CORRECT_FY_CODE}' AND id_branch={BRANCH}
) t WHERE bn - prev != 1 AND prev IS NOT NULL;

-- 4. customerorder clean and sequential
SELECT COUNT(*) as must_be_zero FROM customerorder
WHERE order_date >= '{NEW_FY_START_DATE}' AND fin_year_code != '{CORRECT_FY_CODE}';

SELECT MIN(order_no), MAX(order_no), COUNT(*) FROM customerorder 
WHERE id_customerorder >= {CUTOFF_ORDER_ID};

-- 5. Cascade consistency
SELECT COUNT(*) as must_be_zero FROM customerorderdetails cod
INNER JOIN customerorder co ON co.id_customerorder = cod.id_customerorder
WHERE co.id_customerorder >= {CUTOFF_ORDER_ID} 
  AND LEFT(cod.orderno, LENGTH(co.order_no)) != co.order_no;

SELECT COUNT(*) as must_be_zero FROM ret_billing_advance ba
INNER JOIN customerorder co ON co.id_customerorder = ba.id_customerorder
WHERE ba.id_customerorder >= {CUTOFF_ORDER_ID} AND ba.order_no != co.order_no;

-- 6. Next-number simulation (run BEFORE first new bill after fix)
SELECT bill_no as last_bill_no, bill_id FROM ret_billing 
WHERE fin_year_code='{CORRECT_FY_CODE}' AND id_branch={BRANCH} AND is_eda=1
ORDER BY bill_id DESC LIMIT 1;
-- Expected: last bill_no = total FY26 bills for that partition
```

---

## Notes

- **Cutoff strategy**: Always use `>= {cutoff_id}` (range), NOT `IN (list of IDs)`. New records added between analysis and execution will be caught automatically.
- **`metal_type` does NOT partition sequences** in this codebase — PHP loose comparison makes `metal_type=0` behave as empty and it's excluded from the SQL WHERE.
- **`ref_bill_id` is NOT a sequence** — it stores `bill_id` PK references and must never be renumbered.
- **`ret_service_bill`** has its own generator (`service_bill_number_generator`) and is NOT affected by billing FY corruption unless service bills were transacted during the wrong-FY window.
- **Unique index name**: `'Bill No'` on `ret_billing` — columns `(fin_year_code, bill_no, id_branch, is_eda)`. This is why two-pass is mandatory.
- **code_number_generator has a setting gate**: `is_metal_for_billing` from `ret_settings`. When `0` (default), bill_no is taken directly. When `1`, it uses `explode('-', bill_no)[1]` for metal-prefixed bill numbers. Check this setting before assuming bill_no format.
