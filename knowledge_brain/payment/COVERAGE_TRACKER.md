# Payment Module — Coverage Tracker
> **Updated**: 2026-03-24 | **Round**: 3 | **Status**: ✅ 100% Complete

> **🔄 UPGRADED BRAIN**
> Previous version: Round 2 (2026-03-06)
> Upgraded to: Round 3 (2026-03-24)
> Changes: 0 methods added, 0 removed, 3 code changes. 1 new brain file (FLOW_RISK_MATRIX.md)

## Brain Components

| # | Component | File | Status | Coverage Details |
|---|---|---|---|---|
| 1 | Project Skeleton | [MODULE_BRAIN.md](MODULE_BRAIN.md) §1-3 | ✅ Complete | 27 files mapped, connection flow, constructor (+ metal_wgt_digit helper), 40+ routes |
| 2 | Engine Reverse Engineering | [DATA_FLOW.md](DATA_FLOW.md) | ✅ Complete | 13 flows: CRUD, GA, PDC, Online, Approval, Invoice, OTP, Revert, Settlement, Retry, Cashfree |
| 3 | Flow Risk Matrix | [FLOW_RISK_MATRIX.md](FLOW_RISK_MATRIX.md) | ✅ Complete | 🔄 NEW — 2 state machines, 12 inbound contracts, 8 outbound contracts, 2 reversal analyses, 17 QA test scenarios |
| 4 | Canonical Business Rules | [BUSINESS_RULES.md](BUSINESS_RULES.md) | ✅ Complete | 30 rules covering all payment calculations and behaviors (+2 new: formatMetalWeight, disable_payment) |
| 5 | Cross-Module Mapping | [CROSS_MODULE_MAP.md](CROSS_MODULE_MAP.md) | ✅ Complete | 14 module dependencies, mermaid graph, 12 JS AJAX cross-calls, table map |
| 6 | Invariant Matrix | [INVARIANT_MATRIX.md](INVARIANT_MATRIX.md) | ✅ Complete | 10 dimensions (+installment_cycle), 4 behavior grids, 13 edge cases, config controls |
| 7 | DB Truth Protocol | [DB_TRUTH_PROTOCOL.sql](DB_TRUTH_PROTOCOL.sql) | ✅ Complete | 11 sections, 30+ diagnostic queries |
| 8 | Forensic Template | [FORENSIC_TEMPLATE.md](FORENSIC_TEMPLATE.md) | ✅ Complete | 10 layers, gateway debugging, performance hotspots, 23-row bug lookup (+3 new) |
| — | Method Index (Supplementary) | [METHOD_INDEX.md](METHOD_INDEX.md) | ✅ Complete | 93 controller methods, 278 model methods, 54 JS AJAX calls |
| — | Schema Analysis (Supplementary) | [SCHEMA_ANALYSIS.md](SCHEMA_ANALYSIS.md) | ✅ Complete | 11 owned tables, 15 referenced tables, SQL injection risks |

## Round History

| Round | Date | What Was Done |
|---|---|---|
| 1 | 2026-03-06 | Initial build — all 9 files created with base content |
| 2 | 2026-03-06 | Deep enhancement — DB_TRUTH_PROTOCOL.sql (NEW), +12 business rules, +7 data flows, +3 dimensions, +2 forensic layers, +6 risks, +8 anti-patterns |
| 3 | 2026-03-24 | **UPGRADE** — Diffed 3 commits (d7c9cb60, 45c218ff, 60f1ef8b). New: FLOW_RISK_MATRIX.md. Updated: +2 business rules (PAY-029/030), +1 invariant dimension (installment_cycle=3), +1 risk (disable_payment double-gate), +3 forensic entries, +1 edge case. Constructor: +metal_wgt_digit helper. Model: 8891→8903 lines. Backed up to `_bk_payment_20260324/` |

## File Sizes

| File | Round 1 | Round 2 | Round 3 | Growth |
|---|---|---|---|---|
| MODULE_BRAIN.md | 15.5KB | 20.7KB | 21.6KB | +4% |
| DATA_FLOW.md | 10.7KB | 18.0KB | 18.0KB | — |
| BUSINESS_RULES.md | 6.7KB | 12.4KB | 13.8KB | +11% |
| FLOW_RISK_MATRIX.md | — | — | 8.7KB | 🔄 NEW |
| INVARIANT_MATRIX.md | 4.6KB | 8.4KB | 9.4KB | +12% |
| FORENSIC_TEMPLATE.md | 8.0KB | 13.1KB | 13.6KB | +4% |
| DB_TRUTH_PROTOCOL.sql | — | 19.0KB | 19.0KB | — |
| CROSS_MODULE_MAP.md | 6.0KB | 6.0KB | 6.0KB | — |
| METHOD_INDEX.md | 18.1KB | 18.1KB | 18.1KB | — |
| SCHEMA_ANALYSIS.md | 9.9KB | 9.9KB | 9.9KB | — |
| **TOTAL** | **79.5KB** | **125.6KB** | **141.6KB** | **+13%** |

## Code Changes Since Last Brain (3 Commits)

| # | Commit | Date | Author | Description | Files Changed | Brain Impact |
|---|---|---|---|---|---|---|
| 1 | `60f1ef8b` | 2026-03-09 | Rahul | Disable payment condition updated | `payment_model.php` +6 lines | RULE-PAY-030, Risk #17, Edge case #13 |
| 2 | `45c218ff` | 2026-03-11 | — | Unify metal weight formatting | `admin_payment.php`, `payment.js`, `verify_payment.js` | RULE-PAY-029, Constructor helper, Forensic entry |
| 3 | `d7c9cb60` | 2026-03-13 | Abinaya | Updates from anb — installment_cycle=3 | `payment_model.php` +9/-2 lines | Dimension 10, Forensic entry |

## Bugs Found During Brain Build

| # | Severity | Bug | Location |
|---|---|---|---|
| 1 | 🔴 HIGH | DELETE via GET — CSRF vulnerable | Routes L372 |
| 2 | 🔴 HIGH | SQL Injection — Raw `$id` concatenation | Model throughout |
| 3 | 🔴 HIGH | Array key typo `$pay['payee_ifsc]']` | Controller L308 |
| 4 | 🔴 HIGH | No transaction wrapping on Delete/PDC | Various |
| 5 | 🔴 HIGH | OTP comparison uses `=` not `==` | Controller L5081 |
| 6 | 🔴 HIGH | OTP returned in API response | Controller L5067 |
| 7 | 🔴 HIGH | No receipt number locking (race condition) | Model L42-43 |
| 8 | 🟡 MED | Division by zero in `amount_to_weight()` | Controller L2480-2484 |
| 9 | 🟡 MED | No gateway fallback for unknown `pg_code` | Controller L3788-3803 |
| 10 | 🟡 MED | Revert doesn't cleanup receipt/wallet/referral | Controller L3686-3743 |
| 11 | 🟡 MED | 🔄 R3: `disable_payment` double-gate — dev flagged APN/multi-chance edge cases | Model L2294-2302 |
| 12 | 🟡 MED | 🔄 R3: `installment_cycle=3` monthly calc but daily date match (possible mismatch) | Model L6510,L8093 |

## What's Next

- [ ] Run `/module-bug-audit` to systematically scan for all bugs
- [ ] Use DB_TRUTH_PROTOCOL.sql to validate current data integrity
- [ ] Prioritize 7 HIGH severity bugs for sprint planning
- [ ] Use FORENSIC_TEMPLATE.md Bug Lookup Table for rapid diagnosis
- [ ] Validate `disable_payment` behavior with APN + multiple-chance scenarios
- [ ] Verify `installment_cycle=3` quarterly due calculation is correct
- [ ] Test FLOW_RISK_MATRIX test scenarios FR-PAY-001 through FR-PAY-017
