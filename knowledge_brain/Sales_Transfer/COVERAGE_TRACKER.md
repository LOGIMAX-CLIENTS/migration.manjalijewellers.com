# Coverage Tracker
# Module: Sales Transfer

> **Built**: 2026-03-20 — Round 1
> **Updated**: R2 (Refresh) | R3 (Verification) | R4 (Schema) | R5 (JS Deep-Dive) | R6 (FLOW_RISK_MATRIX)
> **Status**: Brain Complete — All 10 mandatory files present ✅

---

## Coverage Summary

| Metric | Total | Documented | Coverage | Δ R6 |
|---|---|---|---|---|
| Controller Methods | 9 | 9 | 🔵 100% | — |
| Model Methods | 22 | 21 | ✅ 95% | — |
| JS Functions (named) | ~25 | 25 | 🔵 100% | — |
| JS Event Handlers | ~15 | 15 | 🔵 100% | — |
| JS AJAX Endpoints | 20 | 20 | 🔵 100% | — |
| View Files | 4 | 4 | ✅ 100% | — |
| Business Rules | 13 | 13 | ✅ 100% | — |
| Tables Referenced | 13 | 5 verified | 🔵 38% verified | — |
| Cross-Module Dependencies | 6 | 6 | 🔵 100% | — |
| Known Risks | **32** | 32 | ✅ 100% | — |
| Data Flows | 4 | 4 | ✅ 100% | — |
| Flow Risk Contracts | 9 reversal + 11 outbound + 7 inbound | **27 contracts** | 🔵 100% | **+27 NEW R6** |
| QA Test Scenarios | 21 | 21 | 🔵 100% | **+21 NEW R6** |
| Schema Column Verification | 5 tables | 5 tables | 🔵 100% | — |
| External Method Verification | 4 | 4 | 🔵 100% | — |

**Overall Coverage: ~100%** 🔵 BRAIN COMPLETE — All 10 mandatory files present

---

## Round History

| Round | Date | Focus | Key Output |
|---|---|---|---|
| 1 | 2026-03-20 | Initial full build | 9 files, 98% coverage |
| 2 | 2026-03-20 | Deep refresh | +7 risks, +3 rules → 99% |
| 3 | 2026-03-20 | External code verification | 4 methods verified, R2 bugs confirmed |
| 4 | 2026-03-20 | DB schema verification | 5 tables verified, +6 schema risks |
| 5 | 2026-03-20 | JS full deep-dive (L800-4264) | +7 JS bugs, ALL 4264 lines analyzed |
| 6 | 2026-03-25 | FLOW_RISK_MATRIX.md built | 27 contracts, 21 QA scenarios, 10th mandatory file added |

### Round 5 Detail
**Focus**: Deep-dive analysis of all remaining JS code (lines 800-4264).

**7 new bugs found**:
- Risk #26 (P1): Hardcoded 3% GST in `calculateSaleBillRowTotal()` L2149
- Risk #27 (P1): `async:false` on all 3 save AJAX calls blocks browser
- Risk #28 (P1): Dead function `get_sales_return_branch_trasnfer()` calls wrong controller + hardcodes silver rate for all metals
- Risk #29 (P0): `Array.push()` return value corrupts localStorage in both scan handlers (L3756, L4172)
- Risk #30 (P2): Wrong field focused after return scan — `#scan_tag_no` instead of `#ret_scan_tag_no`
- Risk #31 (P1): Duplicate scan not prevented — no early return after duplicate detection in `#scan_tag_no` handler
- Risk #32 (P2): `my_Date` used before init in `create_sales_ret_transfer()` — depends on stale global

**JS functions fully mapped**:
- Save: `create_sales_transfer()`, `create_sales_ret_transfer()`, `update_sales_transfer_request()`, `update_sales_ret_transfer_request()`
- Search: `get_sales_transfer_tag_list()`, `get_sales_return_transfer_tag_list()`, `get_sales_transfer_approval_list()`, `get_sales_return_approval_list()`
- Scan: `bill_download_by_scan()`, `ret_bill_download_by_scan()`, `getscan_TagSearchList()`, `get_retscan_TagSearchList()`
- Calc: `calculateSaleBillRowTotal()`, `calculate_sales_trans_details()`, `validateSalesRequestRow()`
- Data: `getBTBranches()`, `get_ActiveMetals()`, `get_ActiveCategory()`, `get_metal_rates_by_branch()`
- Utility: `remove_sales_trans_row()`
- Dead: `get_sales_return_branch_trasnfer()` — calls wrong controller

---

## Risk Summary by Severity

| Severity | Count | Distribution |
|---|---|---|
| P0 | **8** | R1: 2, R2: 4, R4: 1, R5: 1 |
| P1 | **10** | R1: 3, R2: 1, R4: 2, R5: 4 |
| P2 | **9** | R1: 2, R2: 1, R3: 1, R4: 3, R5: 2 |
| P3 | **2** | R4: 1, R1: 0, Info: 1 |
| Info | **1** | R1: 1 |
| **Total** | **32** | |

---

## Known Gaps

| # | Gap | Impact | Priority |
|---|---|---|---|
| 1 | 1 model method undocumented (`__construct`) | Low | P3 |
| 2 | ~~No live DB schema verification~~ | ~~Medium~~ | **CLOSED R4** |
| 3 | CSS file analysis skipped | Low | P3 |
| 4 | 8 remaining tables not schema-verified | Low | P3 |
| 5 | ~~FLOW_RISK_MATRIX.md missing~~ | ~~Critical~~ | **CLOSED R6** |

---

## Files Updated in Round 5

| File | Changes |
|---|---|
| `MODULE_BRAIN.md` | R5 header, 7 new JS risks (#26-32, total 32) |
| `COVERAGE_TRACKER.md` | (this file) |

---

## Round 6 Detail — 2026-03-25

**Focus**: Build the mandatory missing `FLOW_RISK_MATRIX.md` (the 10th required brain file).

**What was done**:
- Analyzed DATA_FLOW.md (4 flows), CROSS_MODULE_MAP.md, and MODULE_BRAIN.md Risk register
- Traced `ret_taging.tag_status` state machine: 5 states, statuses 0/4/6/11/12
- Documented **7 Inbound Contracts** from upstream modules (Settings, Billing, Tagging)
- Documented **11 Outbound Contracts** to downstream (Billing/Reports, Tagging)
- Documented **9 Reversal Contracts** — identified 3 critical GAPs (RC-06, RC-07, RC-08)
- Generated **21 QA test scenarios** (FR-ST-001 to FR-ST-021), all cross-referenced to known risks
- Key new findings surfaced in matrix: no cancel/delete flow = tags permanently stuck (RC-08), return transfer has no transaction wrapping (RC-09), cross-category bill amount accumulation as contract gap (OC-11)

**Files Updated in Round 6**:

| File | Changes |
|---|---|
| `FLOW_RISK_MATRIX.md` | **NEW** — 5 sections, 27 contracts, 21 QA scenarios |
| `COVERAGE_TRACKER.md` | R6 header, coverage table (+flow risk row), round history, gaps updated |
