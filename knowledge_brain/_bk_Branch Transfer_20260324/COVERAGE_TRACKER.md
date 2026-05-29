# Branch Transfer Module — Brain Coverage Tracker

> **Module**: Branch Transfer
> **Last Updated**: 2026-03-24 — Round 9 (Contract Gap Documentation)

---

## Coverage Summary

| Metric | Documented | Total | Coverage | Status |
|---|---|---|---|---|
| Controller methods | 12 | 12 | 100% | 🔵 Verified R7 |
| Controller sub-routes | 18 | 18 | 100% | 🔵 Verified |
| Model methods | 56 | 56 | 100% | 🔵 Verified R7 |
| JS named functions | 51 | 51 | 100% | 🔵 Verified R7 ← JS 8,786 lines (was 8,879 − 93 lines cleaned) |
| JS AJAX `url:` patterns | 38 | 38 | 100% | 🔵 Verified R7 |
| DB tables (owned) | 5 | 5 | 100% | 🔵 Verified |
| DB tables (referenced) | 28 | 28 | 100% | 🔵 Updated R9 ← was 26; added ret_billing_item_stones + ret_partlysold |
| Table→Method reverse map | 30 | 30 | 100% | 🔵 Verified |
| Business rules | 15 | — | — | 🟢 Updated R9 ← was 13; added BRT-014, BRT-015 |
| Views/templates | 8 | 8 | 100% | 🔵 Verified R6 |
| Hidden fields (form.php) | 10 | 10 | 100% | 🔵 ← was 9, found duplicate `id_product` |
| Hidden fields (approval_list.php) | 21 | 21 | 100% | 🔵 ← was 20 |
| Settings keys | 3 | 3 | 100% | 🔵 Verified |
| Data flows | 6 | 6 | 100% | 🔵 Verified |
| Cross-module deps | 7 | 7 | 100% | 🔵 Verified |
| Mermaid diagram | 1 | 1 | 100% | 🟢 Complete |
| Flow risk contracts | 15 | 15 | 100% | 🟢 Complete (R8) |
| Security risks | 6 | — | — | 🟢 Complete |
| Forensic template layers | 9 | 9 | 100% | 🟢 Complete |
| Invariant dimensions | 7 | 7 | 100% | 🟢 Complete |
| Cross-product grids | 2 | 2 | 100% | 🟢 Complete |
| DB diagnostic queries | 7 | 7 | 100% | 🟢 Complete |
| Anti-patterns register | 8 | — | — | 🟢 Complete |
| **Overall (weighted)** | — | — | **100%** | 🔵 |

---

## Round History (Summary)

| Round | Focus | Key Finds |
|---|---|---|
| 1 | Initial brain creation | Wrongcounts — see R3 |
| 2 | Bug audit | No brain changes |
| 3 | Deep rebuild | Corrected JS 1,047→8,879 lines, 54→56 model methods, 10→37 AJAX, 1→29 hidden fields |
| 4 | Gap closure | DATA_FLOW 3→6, BUSINESS_RULES 6→13, mermaid diagram |
| 5 | Polish & hardening | FORENSIC_TEMPLATE 49→180 lines, INVARIANT_MATRIX 30→100 lines, Anti-Patterns 2→8 |

### Round 6 — 2026-03-11 (JS Deep Analysis)
**Focus**: Systematic JS function audit, codebase count verification, discrepancy hunting

| Finding | Before | After | Impact |
|---|---|---|---|
| JS named functions | ~40 (estimated) | **51** (verified) | Corrected total, all 51 now indexed |
| JS functions documented | 18 (in 3c only) | **51** (across 3a+3b+3c) | 100% coverage |
| Missing from 3c | — | + `remove_row`, `set_brantranOrders` | +2 utility functions |
| JS AJAX `url:` patterns | 37 | **38** | `send_otp()` has 2 AJAX blocks (row 8+9) |
| Hidden fields (form.php) | 9 | **10** | ⚠️ **Duplicate `id_product`** at L267 AND L288 |
| Hidden fields (approval_list.php) | 20 | **21** | +1 previously missed field |

**New bug discovered:**
> ⚠️ **DUPLICATE DOM ID**: `form.php` has TWO `<input type="hidden" id="id_product">` elements at L267 and L288. This causes jQuery `$('#id_product')` to always return the FIRST element, making the second invisible to JS. Could cause product selection bugs depending on which form section is active.

### Round 7 — 2026-03-24 (Refresh — 13-Day Drift Check)
**Focus**: Re-verify all counts against live codebase (+13 days since R6). Identify code drift.

| Finding | R6 | R7 Actual | Delta | Impact |
|---|---|---|---|---|
| Controller lines | 1,378 | **1,386** | +8 | 2 new thin-wrapper top-level methods added |
| Controller methods | 12 | **12** | 0 | Count correct — only line numbers were stale |
| `get_purchase_items()` ctrl | L1363 (stale) | **L1374** | corrected | Thin wrapper → model::get_purchase_items() |
| `getNonTagReceiptedLots()` ctrl | L1370 (stale) | **L1380** | corrected | Thin wrapper → model::getNonTagReceiptedLots() |
| Model lines | 2,236 | **2,248** | +12 | Dead-code stubs comment-blocked at L1155–L1458 |
| Model active methods | 56 | **56** | 0 | Stubs inside /*...*/ — not callable, not counted |
| JS lines | 8,879 | **8,786** | −93 | Dead/commented JS cleaned up |
| JS named functions | 51 | **51** | 0 | Unchanged ✅ |
| JS AJAX url: | 38 | **38** | 0 | Unchanged ✅ |

**New finding (Anti-Pattern #22):**
> 🔍 **DEAD-CODE DUPLICATE STUBS**: Model L1155–L1458 contains ~300 lines of old 3/4-param method versions wrapped in `/*...*/`. Active 5-param versions (with `$bill_type` EDA filter) live at L1462+. Dead stubs are functionally harmless but add confusion and bloat. Logged in Anti-Patterns register.

### Round 9 — 2026-03-24 (Contract Gap Documentation)
**Focus**: Close documentation gaps from R8 FLOW_RISK_MATRIX findings. Add 2 rules, 2 tables, 3 forensic entries.

| Finding | Before | After | Impact |
|---|---|---|---|
| BUSINESS_RULES rules | 13 | **15** | Added RULE-BRT-014 (tag availability client-only) + RULE-BRT-015 (PS/SR no dedup) |
| SCHEMA_ANALYSIS Part B tables | 26 | **28** | Added `ret_billing_item_stones` (active, L1553+) + `ret_partlysold` (dead-code stub, L1317) |
| FORENSIC_TEMPLATE Layer 1 | 9 items | **12 items** | +3 contract gap symptom checks for cancel-reversal, tag availability, PS/SR duplicate |
| FORENSIC_TEMPLATE Layer 4 | 13 trace rows | **16 trace rows** | +3 investigation paths for the 3 HIGH gaps |
| MODULE_BRAIN Section 10 | 1 risk item | **4 risk items** | Cancel-no-reversal + 2 new HIGH gaps explicitly documented |
| MODULE_BRAIN Business Rules count | 13 | **15** | Updated summary paragraph |
| Known Gaps #2, #3, #4 | Open (undocumented) | **Documented** | Now captured in BUSINESS_RULES + FORENSIC_TEMPLATE (design fixes pending) |

### Round 8 — 2026-03-24 (Brain Refresh — Flow Risk Matrix Build)
**Focus**: Gap detection: FLOW_RISK_MATRIX.md found missing. Zero code drift vs R7 confirmed.

| Finding | Before | After | Impact |
|---|---|---|---|
| `FLOW_RISK_MATRIX.md` | ❌ Missing | ✅ Created (6 sections) | State machine, inbound/outbound contracts, reversal gaps, 15 QA scenarios |
| Code drift check | — | ✅ Zero drift | Controller 1,386 / Model 2,248 / JS 8,786 (all identical to R7) |
| Method counts | 12/56/38 | 12/56/38 | No changes |
| Dead-code stubs L1155–L1458 | Still present | Still present | Cleanup candidate (LOW priority) |

**Key FLOW_RISK_MATRIX findings:**
> ⚠️ **CRITICAL REVERSAL GAP confirmed**: Cancel operation (status→3) leaves ALL transit-phase stock changes unreversed — tags stuck at status=4, NT weights not restored, bill flags not cleared, packaging items not released.
> ⚠️ **CONTRACT GAP**: Tag availability (tag_status=0) is NOT validated server-side before creating a transfer — client-side filter only.
> ⚠️ **CONTRACT GAP**: Partly Sale / Sales Return bill_det_id duplicate-transfer check is absent.
> ✅ Confirmed working: NT existence check (checkNonTagItemExist), OTP expiry, scan duplicate detection, Day Close date ordering.

---


## Known Gaps (Remaining)

| # | Area | Gap Description | Priority | Status |
|---|---|---|---|---|
| 1 | Model dead-code stubs | ~300 lines of deprecated method stubs L1155–L1458 (comment-blocked). Safe but bloats file. | LOW | Open — cleanup candidate |
| 2 | Cancel reversal logic | No stock/flag reversal in cancel operation (RULE-BRT-012, FR-BRT-004) | HIGH | 🟡 **Documented** (R9) — in BUSINESS_RULES + FORENSIC_TEMPLATE + MODULE_BRAIN. Code fix needs business design decision first. |
| 3 | Tag availability pre-check | No server-side guard before adding non-zero-status tags (RULE-BRT-014, FR-BRT-001) | HIGH | 🟡 **Documented** (R9) — fix approach defined in RULE-BRT-014. |
| 4 | PS/SR duplicate transfer | No server-side dedup check for bill_det_id (RULE-BRT-015, FR-BRT-002) | HIGH | 🟡 **Documented** (R9) — fix approach defined in RULE-BRT-015. |

> 🟡 3 HIGH gaps are **fully documented** with fix approaches. The gaps remain open at the code level pending business logic decisions and implementation.

---

## Bug Audit Cross-Reference

> Full audit tracker: [COVERAGE_TRACKER.md](file:///c:/xampp_7.4/htdocs/etailv3/bug_report_AI/branch_transfer/COVERAGE_TRACKER.md)

| Metric | Value |
|---|---|
| Audit date | 2026-03-11 (Full Re-Audit + R7-R12 Deep Analysis) |
| Total bugs found | **56** (54 unique; R0-R6: 20 + R7-R12: 36) |
| Severity | 7 P0 · 13 P1 · 33 P2 · 3 P3 |
| Track A / B | 42 / 12 |
| Patterns reused | 8 of 23 scanned |
| KB gaps found | 0 (brain supported full audit) |

---

## Verification Log

| Round | Method | Result |
|---|---|---|
| 3 | PowerShell + grep + view | All metrics corrected |
| 4 | Controller source L795-1148 | +3 flows, +7 rules, +mermaid |
| 5 | FORENSIC/INVARIANT audit | +3 specialized layers, +4 dimensions |
| 6 | `Select-String` count verification | JS fn 51, AJAX 38, form HF 10, appr HF 21. Found duplicate `id_product` DOM ID. |
| Audit | 6-round bug audit | 20 bugs found, 0 KB gaps |
| 7 | `Select-String` re-verification (2026-03-24) | Controller +8 lines (L1374+L1380 wrapper methods). Model +12 lines (dead-code stubs L1155–1458 comment-blocked). JS −93 lines (cleanup). All method/AJAX counts unchanged. Anti-Pattern #22 found. |
| 8 | Gap detection + FLOW_RISK_MATRIX build (2026-03-24) | FLOW_RISK_MATRIX.md created. Zero code drift. 3 HIGH-priority contract gaps identified: cancel reversal, tag availability, PS/SR duplicate check. |
| 9 | Contract gap documentation (2026-03-24) | BUSINESS_RULES: +2 rules (BRT-014, BRT-015). SCHEMA_ANALYSIS: +2 tables (ret_billing_item_stones, ret_partlysold). FORENSIC_TEMPLATE: +3 symptoms +3 trace rows. MODULE_BRAIN: Section 10 expanded to 4 risks. All 3 HIGH gaps documented with fix approaches. |

