# Coverage Tracker: Customer Order

---

## Round Summary

| Round | Focus | Notes |
|---|---|---|
| R1 | Initial skeleton | 13% |
| R2 | Full method index (58 ctrl + 114 model) | 55% |
| R3 | Hidden fields, INVARIANT_MATRIX, FORENSIC_TEMPLATE | 72% |
| R4 | JS AJAX map, 18-file view catalog, 4 modals | 93% |
| R5 | Remaining model methods, 13 business rules | 100% |
| R6 | TX-1→5, 10 anti-patterns | Quality ✅ |
| R7 | TX-6→7, VALIDATION_GAPS, column schema | Deep ✅ |
| R8 | TX-8→10, BUG_CANDIDATES 29 bugs | Forensic ✅ |
| R9 | TX-11→15, HOTSPOT_MAP | Complete ✅ |
| R10 | State machine, FIX_GUIDE, 34 bugs | FINAL ✅ |
| R11 | CROSS_MODULE_MAP enriched, EDGE_CASES 20 items | Platinum ✅ |
| R12 | Sprint Plan, XSS print view, model layer confirmed clean | Production ✅ |
| R13 | Model SQL injections found (L160-500), KNOWLEDGE_GAPS | Diamond ✅ |
| R14 | Model SQL injections L500-900, REMEDIATION_PLAN | Master ✅ |
| R15 | Refresh — taxGroupItems() discovered, neworders() corrected, AP-11 advance-reversal | Refresh ✅ |
| R16 | FLOW_RISK_MATRIX built (was MISSING), TX-4 updated, BUG-024 AP-11 cross-referenced | Forensic ✅ |
| R17 | ret_issue_receipt in SCHEMA_ANALYSIS, FIX-012, KG-16–19 in KNOWLEDGE_GAPS, R16 block inserted | Forensic ✅ |

---

## Round 14 — Model Deep Scan + Remediation Plan (2026-03-14)

| Metric | Total | Notes |
|---|---|---|
| New SQL injections (L500-900) | 15 | Including column-name injection in `getIssueTaggingBySearch` |
| Total model methods with injections | **29+** | Both int-concat and string-concat vectors |
| Remediation items documented | 47 | Across 4 priority tiers |
| Estimated dev time to fix all | ~14 hrs | P0 today=35 min, P1 model=5 hrs, P2-4=8 hrs |
| New files | 1 | `REMEDIATION_PLAN.md` ★ |

### Round 14 Key Findings
- ★ **Column-name injection**: `getIssueTaggingBySearch()` L726 — `$searchField` used raw in `tag.$searchField LIKE ...` → attacker can inject any column/expression
- ★ `get_repair_orders_list()` L880-887 — 7 raw params in the main repair listing query
- ★ `ajax_getRepairOrders()` L774-777 — 4 raw params
- ★ All `get_*_by_id()` helpers (11 methods) safely fixable with `(int)` casts only
- **REMEDIATION_PLAN.md** ★ NEW — master entry point: 47 fixes, CVSS-ordered, code template included

---

## Round 16 — Forensic (2026-03-24)

> **Finding**: `FLOW_RISK_MATRIX.md` was **MISSING** on disk despite being listed as 🔵 Verified. Built from scratch.

| Metric | Value |
|---|---|
| Contract gaps found | 15 (6 categories) |
| QA test scenarios | 18 (12 currently failing) |
| Reversal checks | 17 across cancel-order, cancel-item, delete-order |
| Inbound contracts (no code check) | 5 of 7 |
| Outbound contract gaps | 4 of 8 |

### Round 16 Key Findings
- ⭐ **FLOW_RISK_MATRIX.md CREATED** — state machine, inbound/outbound contracts, reversal contracts, QA checklist
- ⭐ **TRANSACTION_TRACE TX-4** updated with full advance-reversal flow and AP-11 flag
- ⭐ **BUG-024 cross-referenced** to AP-11 with active impact note (now inserts spurious receipt)
- ⭐ **joborder not restored on cancel** — confirmed gap in reversal contract
- ⭐ **delete order** — confirmed 4 child-table orphan gaps + tag not freed

### Files Updated This Round
| File | Change |
|---|---|
| `FLOW_RISK_MATRIX.md` | ⭐ CREATED (was missing) — 141 lines |
| `TRANSACTION_TRACE.md` | TX-4 enhanced with AP-11 and advance-reversal detail |
| `BUG_CANDIDATES.md` | Header + BUG-024 AP-11 cross-reference |
| `COVERAGE_TRACKER.md` | R16 added, file count → 20 |

---

## Round 15 — Refresh Scan (2026-03-24)

> **Trigger**: Source files modified post-R14 — Controller 2026-03-23, Model 2026-03-21, JS 2026-03-21.
> **Mode**: Refresh (same version, method counts unchanged, code changed internally)

| Metric | R14 Baseline | R15 Actual | Delta |
|---|---|---|---|
| Controller methods | 58 | 58 (+1 discovered) | +1 |
| Model methods | 112 | 112 | 0 |
| JS AJAX endpoints | 109 | 109 | 0 |
| View files | 18 | 19 | +1 |
| New bugs found | 44 | 45 | +1 |
| Remediation fixes | 47 | 48 | +1 |

### Round 15 Key Findings
- ⭐ **`taxGroupItems()` DISCOVERED** (L76) — AJAX method calling `getAvailableTaxGroupItems($tgrp_id)` was present since ≥R14 but missing from brain. Added to METHOD_INDEX + MODULE_BRAIN route table.
- ⭐ **`neworders()` ROUTING CORRECTED** — Old version at L840 (→`order/neworder/list`) was commented out in 2026-03-23 edit. New active `neworders()` at L849 routes to `order/repair_order/neworders`. METHOD_INDEX corrected.
- ⭐ **`ajax_order_cancel` ENHANCED** — Now includes advance-payment reversal: inserts into `ret_issue_receipt` (type=2, receipt_type=5) if advance > 0.
- ⭐ **AP-11 ADDED** — Advance-reversal guard `if($order_advance > 0)` compares **array** to int. Non-empty array is always truthy in PHP → inserts zero-amount receipt even when no advance exists. P0-5 added to REMEDIATION_PLAN.
- ✅ All R14 anti-patterns (debug leak L649, image double-string L470, SQL injections L660-661) confirmed still present / unfixed.

### Files Updated This Round
| File | Change |
|---|---|
| `METHOD_INDEX.md` | Added `taxGroupItems()` (R15 new), corrected `neworders()` L849, documented `ajax_order_cancel` advance-reversal |
| `MODULE_BRAIN.md` | R15 header, added `taxGroupItems()` route, `ret_issue_receipt` table note, AP-11 |
| `REMEDIATION_PLAN.md` | Added P0-5 (advance-reversal guard), 48 fixes total |
| `COVERAGE_TRACKER.md` | This file |

---

## Complete Brain File Listing (20 files)

> ⭐ R16: **FLOW_RISK_MATRIX.md was MISSING** despite being listed as 🔵 in previous rounds. Now created.

| File | Status | Key Metric |
|---|---|---|
| `MODULE_BRAIN.md` | 🔵 | 10 anti-patterns |
| `METHOD_INDEX.md` | 🔵 | 172 methods |
| `DATA_FLOW.md` | 🔵 | 18 views, 4 modals |
| `BUSINESS_RULES.md` | 🔵 | 13 rules |
| `CROSS_MODULE_MAP.md` | 🔵 | 15 integrations |
| `SCHEMA_ANALYSIS.md` | 🔵 | 5 tables, 60+ cols |
| `INVARIANT_MATRIX.md` | 🔵 | 5 dimensions |
| `FORENSIC_TEMPLATE.md` | 🔵 | 8 layers |
| `FLOW_RISK_MATRIX.md` | 🔵 | ⭐ R16 CREATED — 8 states, 7 inbound, 8 outbound, 3 reversal matrices, 18 QA tests, 15 contract gaps |
| `TRANSACTION_TRACE.md` | 🔵 | 15 TX blocks (TX-4 updated R16) |
| `VALIDATION_GAPS.md` | 🔵 | 35 fields |
| `BUG_CANDIDATES.md` | 🔵 | 45 bugs (AP-11 added R15) |
| `HOTSPOT_MAP.md` | 🔵 | 10 hotspots |
| `ORDER_STATE_MACHINE.md` | 🔵 | 8 states, 12 transitions |
| `FIX_GUIDE.md` | 🔵 | 11 patches |
| `EDGE_CASES.md` | 🔵 | 20 edge cases |
| `SPRINT_PLAN.md` | 🔵 | 13 stories |
| `KNOWLEDGE_GAPS.md` | 🔵 | 19 gaps (KG-16–19 added R17) |
| `REMEDIATION_PLAN.md` | 🔵 | 48 fixes, 4 tiers (P0-5 added R15) |
| `COVERAGE_TRACKER.md` | 🔵 | 17 rounds |

> **🔵 Brain status: MASTER (Refreshed R17)** — 20 files, 44 bugs, 19 knowledge gaps, 12 FIX patches, 15 contract gaps, 48 remediation items. Run KG-16/17 queries to assess AP-11 & tag-orphan blast radius before sprint planning.
