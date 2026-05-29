# Report Accuracy Checklist: Branch Transfer Reports

> **Last Updated:** 2026-03-27
> **Model:** `getBranchTransReport()`, `get_categorywise_bt_report()`, `get_stock_issue_report()`, `getSalesTransReport()`

---

## Accuracy Checks

| # | Check | Status |
|---|---|---|
| 1 | Sender outward = Receiver inward (per transfer) | ⬜ |
| 2 | In-transit items counted separately | ⬜ |
| 3 | Cancelled transfers excluded | ⬜ |
| 4 | Category-wise breakdown sums to total | ⬜ |
| 5 | Stock issue separate from branch transfer | ⬜ |
| 6 | Sales transfer (inter-branch sale) counted correctly | ⬜ |
| 7 | `get_categorywise_bt_report` has 4 date-suffixed copies — verify right one | ⬜ |

## Known Risks

| Risk | Severity |
|---|---|
| 4 date-suffixed method copies may calculate differently | 🟡 MED |
| In-transit tags counted in one branch but not both | 🟡 MED |
