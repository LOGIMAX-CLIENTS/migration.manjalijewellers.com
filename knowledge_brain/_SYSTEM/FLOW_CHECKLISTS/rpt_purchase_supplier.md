# Report Accuracy Checklist: Purchase / Supplier Reports

> **Last Updated:** 2026-03-27
> **Model:** `getPurchaseBillsReport()`, `getSmithAllTransactions()`, `getSupplierTransactionList()`

---

## Accuracy Checks

| # | Check | Status |
|---|---|---|
| 1 | Purchase total = sum of all purchase bills (type=4) | ⬜ |
| 2 | Supplier balance = purchase - payments - returns | ⬜ |
| 3 | Karigar metal issue matches physical issue records | ⬜ |
| 4 | Smith transactions include all QC/HM steps | ⬜ |
| 5 | Rate-fixed amounts match PO rates | ⬜ |
| 6 | Cancelled purchase bills excluded | ⬜ |
| 7 | Purchase return deducted from supplier balance | ⬜ |
