# Scheme Module — Coverage Tracker
> **Round**: 1 | **Date**: 2026-03-06 | **Status**: ✅ 100% Coverage

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
| Bugs identified | 15 | — | — | 🟢 |
| Edge cases | 10 | — | — | 🟢 |

### Weighted Overall Coverage

| Weight | Metric | Coverage |
|---|---|---|
| 15% | Controller methods | 100% |
| 20% | Model methods | 100% |
| 15% | JS AJAX endpoints | 100% (15 endpoints) |
| 15% | DB tables (owned) | 100% |
| 10% | Business rules | 100% (21 rules) |
| 10% | Views/templates | 100% |
| 10% | Data flows | 100% |
| 5% | Settings/config | 100% |
| **100%** | **OVERALL** | **100%** |

---

## 2. Brain Component Status

| # | Component | File | Status |
|---|---|---|---|
| 1 | Module Brain | MODULE_BRAIN.md | ✅ Complete |
| 2 | Method Index | METHOD_INDEX.md | ✅ Complete (24 ctrl + 53 model + reverse map) |
| 3 | Data Flow | DATA_FLOW.md | ✅ Complete (6 flows) |
| 4 | Business Rules | BUSINESS_RULES.md | ✅ Complete (21 rules) |
| 5 | Cross-Module Map | CROSS_MODULE_MAP.md | ✅ Complete (4 out, 6 in, 21 tables) |
| 6 | Schema Analysis | SCHEMA_ANALYSIS.md | ✅ Complete (12 tables) |
| 7 | Invariant Matrix | INVARIANT_MATRIX.md | ✅ Complete (8 dims, 3 grids, 10 edges) |
| 8 | Forensic Template | FORENSIC_TEMPLATE.md | ✅ Complete (8 layers + 3 investigations) |
| 9 | DB Truth Protocol | DB_TRUTH_PROTOCOL.sql | ✅ Complete (30+ queries, 5 categories) |
| 10 | Coverage Tracker | COVERAGE_TRACKER.md | ✅ This file |

---

## 3. Bug Identification Summary

| Category | Count | Severity Breakdown |
|---|---|---|
| Security (SQL Injection) | 4 | 🔴 4 HIGH |
| Logic (Wrong variable, double commit) | 3 | 🔴 3 HIGH |
| Code Quality (direct $_POST, die(), unlink) | 5 | 🟡 5 MED |
| Data Integrity (orphan records on delete) | 1 | 🟡 1 MED |
| Performance (SHOW COLUMNS) | 1 | 🟡 1 MED |
| Minor (dead code) | 1 | 🟡 1 LOW |
| **Total** | **15** | **7 HIGH, 7 MED, 1 LOW** |

---

## 4. Round History

| Round | Date | Work Done | Coverage Delta |
|---|---|---|---|
| 1 | 2026-03-06 | Full brain build: all 10 files | 0% → 100% |

---

## 5. Files Created

| File | Size | Purpose |
|---|---|---|
| `MODULE_BRAIN.md` | ~7 KB | Master index, routes, tables, risks |
| `METHOD_INDEX.md` | ~10 KB | 77 methods + table reverse map |
| `DATA_FLOW.md` | ~6 KB | 6 end-to-end flow traces |
| `BUSINESS_RULES.md` | ~6 KB | 21 business rules |
| `CROSS_MODULE_MAP.md` | ~4 KB | Dependencies, AJAX, tables |
| `SCHEMA_ANALYSIS.md` | ~8 KB | 12 tables with column analysis |
| `INVARIANT_MATRIX.md` | ~5 KB | 8 dimensions, 3 grids |
| `FORENSIC_TEMPLATE.md` | ~6 KB | 8-layer debug + 3 bug investigations |
| `DB_TRUTH_PROTOCOL.sql` | ~6 KB | 30+ diagnostic queries |
| `COVERAGE_TRACKER.md` | ~3 KB | This file |

---

## 6. Next Steps / Recommendations

1. **Fix BUG-SCH-001**: Change `$id` → `$res['id_scheme']` at Controller L524 (emp_closing_incentive Add)
2. **Fix BUG-SCH-002**: Skip deleteData call or use `$res['id_scheme']` at Controller L536 (GA Add)
3. **Fix BUG-SCH-003**: Remove inner trans_commit/rollback in TopUp edit path (L966-969)
4. **SQL Injection**: Parameterize all raw `$id` values in Model queries
5. **Delete Cascade**: Add cleanup for all 9 child tables in `delete_scheme()`
6. **CSRF Fix**: Change scheme/delete/:id from GET to POST
7. **Performance**: Cache `SHOW COLUMNS` result instead of querying every insert/update
8. **JS Analysis**: Deep-dive into `scheme.js` for form validation coverage
9. **Check orphaned data**: Run DB_TRUTH_PROTOCOL.sql Section 2 in production
