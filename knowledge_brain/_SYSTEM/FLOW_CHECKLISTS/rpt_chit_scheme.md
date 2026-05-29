# Report Accuracy Checklist: Chit / Scheme Reports

> **Last Updated:** 2026-03-27
> **Model:** `chit_utilize_details()`, `get_staff_chit_incentive_details()`, `getSchemeClosedDetails()`

---

## Accuracy Checks

| # | Check | Status |
|---|---|---|
| 1 | Utilization total = sum of utilized scheme accounts | ⬜ |
| 2 | Installment count matches payment records | ⬜ |
| 3 | Maturity amount = paid + bonus | ⬜ |
| 4 | Pre-closed accounts calculated correctly (deductions applied) | ⬜ |
| 5 | Staff incentive matches utilization records | ⬜ |
| 6 | Cancelled utilizations excluded | ⬜ |
| 7 | Duplicate `chit_utilize_details()` — legacy at L2289, active at L2335 | ⬜ |

## Known Risks

| Risk | Severity |
|---|---|
| Duplicate model method — may call wrong version | 🟡 MED |
| Pre-close deduction formula variance across clients | 🟡 MED |
