# Recipe: Advance Report Negative Balance (5 SQL Subquery Bugs)

## Metadata

| Field | Value |
|---|---|
| Pattern IDs | PAT-RPT-001, PAT-RPT-002 |
| Severity | P1 |
| Modules Affected | Reports (`ret_reports_model.php` — `customerAdvanceReport()`) |
| Auto-fixable | No — requires per-module code review |
| Bug ID | RPT-ADV-001 |

## Client Scope

**Applies to**: Any client using the Advance Total/Detail report (`admin_ret_reports/advance_total_details/list`)

## Created By

| Field | Value |
|---|---|
| Developer | Antigravity |
| Client | NSK Jewels |
| Date | 2026-04-08 |

---

## Symptom

The Advance Total/Detail report shows **negative balance amounts** for some customers, even though their advance receipts and billing records look correct in the database.

Common presentation:
- Customer shows Advance = ₹1,92,000, Utilized = ₹5,22,000, Balance = **−₹3,30,000**
- The negative balance disappears when you manually query the raw tables

---

## Root Cause

**Five separate bugs** in the `customerAdvanceReport()` function, each affecting a different subquery:

```
Balance = Advance − Utilized − Chit Adjustment − Refund − Transfer
```

**Bug 1**: Utilized subquery filtered by `bill.bill_date` instead of `ir.bill_date`
**Bug 2**: chit_adj subquery missing `ir.bill_status=1` — cancelled receipts still counted
**Bug 3**: total_wt subquery had no receipt_type filter — non-advance receipts inflated weight
**Bug 4** (PAT-RPT-001): adv_trns grouped by receiver/destination instead of sender/source customer
**Bug 5** (PAT-RPT-002): Refund joined on `a.id_issue_receipt` (voucher) instead of `a.refund_receipt` (original advance)

---

## Detection

```
grep -n "bill\.bill_date" {MODEL_FILE}           -- Bug 1
grep -n "ir\.bill_status" {MODEL_FILE}           -- Bug 2
grep -n "receipt_type.*weight|tot_wt" {MODEL_FILE}   -- Bug 3
grep -n "ret_advance_transfer" {MODEL_FILE}      -- Bug 4: check GROUP BY
grep -n "ret_advance_refund" {MODEL_FILE}        -- Bug 5: check JOIN column
```

Verify data first:
```sql
SELECT SUM(amount) FROM ret_issue_receipt
WHERE id_customer={ID} AND bill_status=1 AND receipt_type!=1;
-- Compare to report's "Advance" column
```

---

## Files

| File | Function | Lines |
|---|---|---|
| `admin/application/models/ret_reports_model.php` | `customerAdvanceReport()` | ~10480–10545 |

---

## Fix

### Bug 1 — Date Filter

**Before:** `AND (date(bill.bill_date) BETWEEN '{from}' AND '{to}')`

**After:** `AND (date(ir.bill_date) BETWEEN '{from}' AND '{to}')`

---

### Bug 2 — Cancelled Receipts

**Before:** `where p.payment_status=1`

**After:** `where p.payment_status=1 AND ir.bill_status=1`

---

### Bug 3 — Weight Receipt Type

**Before:** No receipt_type filter on total_wt subquery

**After:** Add `WHERE ir.receipt_type IN (2, 3, 4)`

---

### Bug 4 — Transfer Customer (PAT-RPT-001)

**Before:**
```sql
FROM ret_advance_transfer trn
LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = trn.id_issue_receipt
WHERE r.bill_status=1
GROUP BY r.id_customer  -- wrong: groups by destination
```

**After:**
```sql
FROM ret_advance_transfer trn
LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = trn.id_issue_receipt
WHERE r.bill_status=1 AND r.receipt_type != 7
GROUP BY r.id_customer  -- correct: source customer (sender)
```

---

### Bug 5 — Refund JOIN (PAT-RPT-002)

**Before:**
```sql
FROM ret_advance_refund a
LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = a.id_issue_receipt
WHERE r.bill_status=1
GROUP BY r.id_customer
```

**After:**
```sql
FROM (
    SELECT LEAST(SUM(a.refund_amount), MAX(orig_r.amount)) as capped_refund,
           orig_r.id_customer, a.refund_receipt
    FROM ret_advance_refund a
    LEFT JOIN ret_issue_receipt orig_r ON orig_r.id_issue_receipt = a.refund_receipt
    WHERE orig_r.bill_status=1
    GROUP BY a.refund_receipt, orig_r.id_customer
) as per_receipt
GROUP BY per_receipt.id_customer
```

---

## Verification

```sql
-- Should return 0 rows after fix:
SELECT cus.id_customer, cus.firstname,
  IFNULL(adv.adv,0) - IFNULL(util.util,0) - IFNULL(ref.ref,0) as balance
FROM customer cus
LEFT JOIN (SELECT id_customer, SUM(amount) adv FROM ret_issue_receipt WHERE bill_status=1 AND receipt_type!=1 GROUP BY id_customer) adv ON adv.id_customer=cus.id_customer
LEFT JOIN (SELECT ir.id_customer, SUM(u.utilized_amt) util FROM ret_advance_utilized u JOIN ret_issue_receipt ir ON ir.id_issue_receipt=u.id_issue_receipt JOIN ret_billing b ON b.bill_id=u.bill_id WHERE b.bill_status=1 AND u.adjusted_for=1 GROUP BY ir.id_customer) util ON util.id_customer=cus.id_customer
LEFT JOIN (SELECT orig_r.id_customer, SUM(a.refund_amount) ref FROM ret_advance_refund a JOIN ret_issue_receipt orig_r ON orig_r.id_issue_receipt=a.refund_receipt WHERE orig_r.bill_status=1 GROUP BY orig_r.id_customer) ref ON ref.id_customer=cus.id_customer
WHERE adv.adv IS NOT NULL HAVING balance < 0;
```

Also run: `php -l admin/application/models/ret_reports_model.php`
