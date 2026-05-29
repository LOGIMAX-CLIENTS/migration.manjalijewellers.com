# Report Accuracy Checklist: Cash Abstract

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_reports.php` → `cash_abstract()` L362
> **Model:** `ret_reports_model.php` → `getBillDetails()`
> **Export:** `generate_cash_abstract()` L406 → PDF via DOMPDF

---

## What This Report Shows

Cash abstract = summary of all cash received/paid by bill type, payment mode, and category for a date range. Clients check this DAILY to reconcile cash drawer.

## Data Sources (SQL Joins)

| Data Point | Table(s) | Join Logic |
|---|---|---|
| Sales totals | `ret_billing` + `ret_bill_details` | WHERE bill_date, bill_status=1 |
| Payment breakdown | `ret_billing_payment` | JOIN on bill_id |
| Return totals | `ret_billing` WHERE bill_type=7 | bill_status=1 |
| Issue/Receipt | `ret_issue_receipt` | WHERE bill_date |
| Credit collection | `ret_billing` WHERE bill_type=8 | bill_status=1 |

## Accuracy Checks

| # | Check | Expected | Status |
|---|---|---|---|
| 1 | Total cash = sum of all cash payments | Match | ⬜ |
| 2 | Total card = sum of all card payments | Match | ⬜ |
| 3 | Total sales = sum of all bill_type 1,2,3 | Match | ⬜ |
| 4 | Cancelled bills excluded | bill_status=2 NOT counted | ⬜ |
| 5 | Returns deducted | bill_type=7 subtracted | ⬜ |
| 6 | Issue/Receipt included | Add issue/receipt amounts | ⬜ |
| 7 | Branch filter | Correct per-branch isolation | ⬜ |
| 8 | Date filter | Correct date range filtering | ⬜ |
| 9 | PDF matches screen | `generate_cash_abstract()` same data | ⬜ |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| RPT-CA-001 | DOMPDF typo: `"portriat"` → `"portrait"` (L440) — may affect layout | 🟡 MED |
| RPT-CA-002 | POS device payments may not be included | ⬜ **VERIFY** |
