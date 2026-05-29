# Scheme Module — Coverage Tracker
> **Round**: 2 | **Date**: 2026-03-24 | **Status**: ✅ 100% Coverage

> **🔄 UPGRADED BRAIN**
> Previous version: Round 1 (2026-03-06)
> Upgraded to: Round 2 (2026-03-24)
> Changes: 0 methods added, 0 removed, 1 view fix. 1 new brain file (FLOW_RISK_MATRIX.md)

---

## 1. Coverage Summary

| Metric | Documented | Total | Coverage | Status |
|---|---|---|---|---|
| Controller methods | 24 | 24 | 100% | 🟢 |
| Model methods | 53 | 53 | 100% | 🟢 |
| Routes (mapped) | 15 | 15 | 100% | 🟢 |
| DB tables (owned) | 11 | 11 | 100% | 🟢 |
| DB tables (referenced) | 10 | 10 | 100% | 🟢 |
| Business rules | 21 | — | — | 🟢 |
| Data flows (CRUD+) | 6 | 6 | 100% | 🟢 |
| Views/templates | 2 | 2 | 100% | 🟢 |
| Flow risk contracts | 16 inbound+outbound | — | — | 🟢 |
| QA test scenarios | 16 | — | — | 🟢 |
| Bugs identified | 16 | — | — | 🟢 |
| Edge cases | 10 | — | — | 🟢 |

---

## 2. Brain Component Status

| # | Component | File | Status | Coverage Details |
|---|---|---|---|---|
| 1 | Module Brain | [MODULE_BRAIN.md](MODULE_BRAIN.md) | ✅ Complete | Architecture, 15 routes, 21 tables, 16 bugs, upgrade notice |
| 2 | Method Index | [METHOD_INDEX.md](METHOD_INDEX.md) | ✅ Complete | 24 ctrl + 53 model + reverse map |
| 3 | Data Flow | [DATA_FLOW.md](DATA_FLOW.md) | ✅ Complete | 6 flows: Create, Edit, Delete, Business Data, Active Schemes, Batch GST |
| 4 | Flow Risk Matrix | [FLOW_RISK_MATRIX.md](FLOW_RISK_MATRIX.md) | ✅ Complete | 🔄 NEW — 2 state machines, 8 inbound, 8 outbound, 1 reversal (18%), 16 QA scenarios |
| 5 | Business Rules | [BUSINESS_RULES.md](BUSINESS_RULES.md) | ✅ Complete | 21 rules covering all scheme configurations |
| 6 | Cross-Module Map | [CROSS_MODULE_MAP.md](CROSS_MODULE_MAP.md) | ✅ Complete | 4 outbound, 6 inbound, 21 tables |
| 7 | Schema Analysis | [SCHEMA_ANALYSIS.md](SCHEMA_ANALYSIS.md) | ✅ Complete | 12 tables with full column analysis |
| 8 | Invariant Matrix | [INVARIANT_MATRIX.md](INVARIANT_MATRIX.md) | ✅ Complete | 8 dimensions, 3 grids, 10 edge cases |
| 9 | Forensic Template | [FORENSIC_TEMPLATE.md](FORENSIC_TEMPLATE.md) | ✅ Complete | 8 layers + 3 bug investigations |
| 10 | DB Truth Protocol | [DB_TRUTH_PROTOCOL.sql](DB_TRUTH_PROTOCOL.sql) | ✅ Complete | 30+ diagnostic queries, 5 categories |
| 11 | Coverage Tracker | [COVERAGE_TRACKER.md](COVERAGE_TRACKER.md) | ✅ This file |

---

## 3. Round History

| Round | Date | Work Done | Coverage Delta |
|---|---|---|---|
| 1 | 2026-03-06 | Full brain build: all 10 files | 0% → 100% |
| 2 | 2026-03-24 | **UPGRADE** — Diffed 1 commit (3ccb4ac1: view DOM ID fix). New: FLOW_RISK_MATRIX.md (2 state machines, 8+8 contracts, 18% delete reversal score, 16 QA scenarios). Updated: MODULE_BRAIN.md (+upgrade notice, +1 risk, +JS line count). All headers → Round 2. Backed up to `_bk_scheme_20260324/` | 100% maintained |

---

## 4. File Sizes

| File | Round 1 | Round 2 | Growth |
|---|---|---|---|
| MODULE_BRAIN.md | 8.4KB | 8.8KB | +5% |
| METHOD_INDEX.md | 9.7KB | 9.7KB | — |
| DATA_FLOW.md | 7.3KB | 7.3KB | — |
| FLOW_RISK_MATRIX.md | — | 7.8KB | 🔄 NEW |
| BUSINESS_RULES.md | 6.0KB | 6.0KB | — |
| CROSS_MODULE_MAP.md | 5.1KB | 5.1KB | — |
| SCHEMA_ANALYSIS.md | 9.0KB | 9.0KB | — |
| INVARIANT_MATRIX.md | 5.2KB | 5.2KB | — |
| FORENSIC_TEMPLATE.md | 7.6KB | 7.6KB | — |
| DB_TRUTH_PROTOCOL.sql | 7.8KB | 7.8KB | — |
| COVERAGE_TRACKER.md | 4.0KB | 5.0KB | +25% |
| **TOTAL** | **70.1KB** | **78.2KB** | **+12%** |

---

## 5. Code Changes Since Last Brain (1 Commit)

| # | Commit | Date | Author | Description | Files Changed | Brain Impact |
|---|---|---|---|---|---|---|
| 1 | `3ccb4ac1` | 2026-03-24 | — | Changes from another client | `form.php` (+1/-1 line) | View DOM ID fixed: `pan_req_amt` → `aadhaar_required_amt` |

---

## 6. Bug Summary

| Category | Count | Severity Breakdown |
|---|---|---|
| Security (SQL Injection) | 4 | 🔴 4 HIGH |
| Logic (Wrong variable, double commit) | 3 | 🔴 3 HIGH |
| Code Quality (direct $_POST, die(), unlink) | 5 | 🟡 5 MED |
| Data Integrity (orphan records on delete) | 1 | 🟡 1 MED |
| Performance (SHOW COLUMNS) | 1 | 🟡 1 MED |
| Minor (dead code) | 1 | 🟡 1 LOW |
| View DOM ID (🔄 R2 — FIXED) | 1 | ✅ FIXED |
| **Total** | **16** | **7 HIGH, 7 MED, 1 LOW, 1 FIXED** |

---

## 7. Next Steps / Recommendations

1. **Fix BUG-SCH-001**: Change `$id` → `$res['id_scheme']` at Controller L524 (emp_closing_incentive Add)
2. **Fix BUG-SCH-002**: Skip deleteData call or use `$res['id_scheme']` at Controller L536 (GA Add)
3. **Fix BUG-SCH-003**: Remove inner trans_commit/rollback in TopUp edit path (L966-969)
4. **SQL Injection**: Parameterize all raw `$id` values in Model queries
5. **Delete Cascade**: Add cleanup for all 9 child tables in `delete_scheme()` — **DELETE reversal score: 18%**
6. **CSRF Fix**: Change scheme/delete/:id from GET to POST
7. **Performance**: Cache `SHOW COLUMNS` result instead of querying every insert/update
8. **JS Analysis**: Deep-dive into `scheme.js` (2,266 lines) for form validation coverage
9. **Validate FLOW_RISK_MATRIX test scenarios** FR-SCH-001 through FR-SCH-016
10. **Run `/module-bug-audit`** to systematically scan and prioritize fixes
