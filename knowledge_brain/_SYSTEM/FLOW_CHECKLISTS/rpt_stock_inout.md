# Report Accuracy Checklist: Stock In/Out & HO Daily Stock Book

> **Last Updated:** 2026-03-27
> **Model:** `get_section_wise_stock_inout_details()`, `get_stock_details_v1()`, `ho_daily_stock_book()`

---

## Data Sources

| Data | Tables |
|---|---|
| Opening stock | `ret_taging` WHERE tag_status=0, created_date < period start |
| Inward (purchase) | `ret_taging` WHERE created_date in period |
| Inward (transfer in) | `ret_branch_transfer` recipient |
| Outward (sales) | `ret_bill_details` linked tags |
| Outward (transfer out) | `ret_branch_transfer` sender |
| Closing stock | Opening + Inward - Outward |
| Section-wise | Grouped by `id_section` |

## Accuracy Checks

| # | Check | Status |
|---|---|---|
| 1 | Opening + Inward - Outward = Closing | ⬜ |
| 2 | Section totals = branch total | ⬜ |
| 3 | Transfer in at dest = Transfer out at source | ⬜ |
| 4 | Cancelled bills NOT counted as outward | ⬜ |
| 5 | In-transit tags excluded from both branches | ⬜ |
| 6 | `ho_daily_stock_book` ~400 lines in controller (should be in model) | ⬜ |
| 7 | `get_section_wise_stock_inout_details` has 4 date-suffixed copies | ⬜ |

## Known Bugs

| Bug | Severity |
|---|---|
| `ho_daily_stock_book` business logic in controller (L8686-9087) — 400 lines, hard to test | 🟡 MED |
| 4 date-suffixed model method copies — potential version mismatch | 🟡 MED |
| In-transit tag counting varies between reports | 🟡 MED |
