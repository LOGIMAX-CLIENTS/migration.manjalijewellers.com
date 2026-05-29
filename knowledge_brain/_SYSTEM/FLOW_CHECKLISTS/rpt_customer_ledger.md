# Report Accuracy Checklist: Customer Ledger & Credit

> **Last Updated:** 2026-03-27
> **Model:** `getreceiptDetails()`, `getCreditPendingDetails()`, `getcreditBill_history()`, `get_advance_details()`

---

## Accuracy Checks

| # | Check | Status |
|---|---|---|
| 1 | Customer balance = sum(bills) - sum(payments) - sum(returns) | ⬜ |
| 2 | Credit pending = bills where credit_status=2 | ⬜ |
| 3 | Cancelled collections excluded from payment total | ⬜ |
| 4 | Advance history = issue/receipts for customer | ⬜ |
| 5 | Old metal adjustments deducted from balance | ⬜ |
| 6 | Chit utilization deducted from balance | ⬜ |
| 7 | Multiple branches: per-branch vs consolidated ledger | ⬜ |

## Known Risks

| Risk | Severity |
|---|---|
| `getCreditPendingDetails_14_02_2024()` — old version may be called instead | 🟡 MED |
| No statement generation for customer-facing printout | 🟡 MED |
