# Report Accuracy Checklist: Day Transactions

> **Last Updated:** 2026-03-27
> **Model:** `day_transactions_report()`, `detailed_day_transaction_report()`, `get_categorywise_day_transaction()`

---

## Data Sources

| Section | Tables |
|---|---|
| Sales | `ret_billing` + `ret_bill_details` (type 1,2,3) |
| Returns | `ret_billing` (type 7) |
| Credit Collections | `ret_billing` (type 8) |
| Issue/Receipt | `ret_issue_receipt` |
| Payments | `ret_billing_payment` |
| Cancelled | `ret_billing` (bill_status=2) |

## Accuracy Checks

| # | Check | Status |
|---|---|---|
| 1 | All bill types included (1-12) | ⬜ |
| 2 | Cancelled bills shown separately | ⬜ |
| 3 | Date = day_closing entry_date | ⬜ |
| 4 | Branch filter correct | ⬜ |
| 5 | Running total = sum of all line items | ⬜ |
| 6 | Duplicate model methods: active L18312 vs legacy L17474 — verify right one called | ⬜ |
| 7 | Category breakdown matches individual items | ⬜ |

## Known Bugs

| Bug | Severity |
|---|---|
| Duplicate `day_transactions_report()` — legacy code at L17474 (commented) | 🟡 MED |
| `$_POST` direct access without existence check | 🟡 MED |
