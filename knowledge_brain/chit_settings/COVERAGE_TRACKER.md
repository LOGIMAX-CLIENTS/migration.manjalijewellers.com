# Chit Settings Module — Coverage Tracker
> **Round**: R2-Upgrade | **Date**: 2026-03-25 | **Status**: ✅ BRAIN UPGRADE COMPLETE

---

## 1. Coverage Summary

| Metric | Documented | Total | Coverage | Status |
|---|---|---|---|---|
| Controller methods | 123 | 123 | 100% | 🟢 |
| Model methods | 182 | 182 | 100% | 🟢 |
| Routes (mapped) | 70+ | 70+ | 100% | 🟢 |
| DB tables (owned) | 35+ | 35+ | 100% | 🟢 |
| Business rules | 13 | — | — | 🟢 |
| Data flows | 7 | 7 | 100% | 🟢 |
| Views/templates | 16+ | 16+ | 100% | 🟢 |
| Bugs identified | 16+ | — | — | 🟢 |
| Edge cases | 10 | — | — | 🟢 |

---

## 2. Brain Component Status

| # | Component | File | Status |
|---|---|---|---|
| 1 | Module Brain | MODULE_BRAIN.md | ✅ Upgraded |
| 2 | Method Index | METHOD_INDEX.md | ✅ Upgraded (123 ctrl + 182 model + exact line numbers) |
| 3 | Data Flow | DATA_FLOW.md | ✅ Complete (7 flows) |
| 4 | Business Rules | BUSINESS_RULES.md | ✅ Complete (13 rules) |
| 5 | Cross-Module Map | CROSS_MODULE_MAP.md | ✅ Complete (7 out, 6 in, 35+ tables) |
| 6 | Schema Analysis | SCHEMA_ANALYSIS.md | ✅ Complete (10 tables detailed) |
| 7 | Invariant Matrix | INVARIANT_MATRIX.md | ✅ Complete (7 dims, 2 grids, 10 edges) |
| 8 | Forensic Template | FORENSIC_TEMPLATE.md | ✅ Complete (7 layers + 5 bug investigations) |
| 9 | DB Truth Protocol | DB_TRUTH_PROTOCOL.sql | ✅ Complete (30+ queries, 6 categories) |
| 10 | Coverage Tracker | COVERAGE_TRACKER.md | ✅ This file |

---

## 3. Upgrade Changes (R2-Upgrade, 2026-03-25)

| Change | Details |
|---|---|
| **Controller delta** | +128 lines (4,709→4,837), +3 methods (120→123) |
| **Model delta** | +78 lines (2,866→2,944), +4 methods (178→182) |
| **New functional domain** | **KYC Settings** — document requirements per scheme or customer |
| **New tables** | `kyc_master`, `kyc_settings`, `kyc_rules`, `ledger_master`, `ledger_mapping` |
| **New controller methods** | `kyc_master()` L4752, `get_active_schemes()` L4758, `kyc_settings($type)` L4764 |
| **New model methods** | `kyc_master()` L2886, `get_kyc_settings()` L2892, `get_kyc_rules()` L2905, `save_kyc_settings()` L2910 |
| **Newly documented** | `update_notification_status()` L4597, `is_refno_exists()` L1701, `ledger()` L4616 |
| **New bugs found** | 3 new bugs (#14: hardcoded pm.logimaxindia.com, #15: raw SQL in update_notification_status, #16: raw SQL concat in getRef_nos) |
| **Line number updates** | All method line numbers adjusted for +128/+78 offset |
| **Backup** | Previous brain backed up to `_bk_chit_settings_20260325/` |

---

## 4. Bug Identification Summary

| Category | Count | Severity Breakdown |
|---|---|---|
| Security (SQL Injection — 50+ queries) | 1 class (50+) | 🔴 HIGH |
| Security (clear_database no CSRF) | 1 | 🔴 CRITICAL |
| Security (raw $_POST usage) | 1 | 🔴 HIGH |
| Security (update_notification_status raw SQL) | 1 | 🔴 HIGH — **NEW** |
| Security (getRef_nos raw SQL concat) | 1 | 🔴 HIGH — **NEW** |
| Logic (config NOT saved — commented out) | 1 | 🔴 HIGH |
| Logic (rate.txt written as PHP array) | 1 | 🟡 MED |
| Logic (undefined constant tab_name) | 1 | 🟡 MED |
| Security (plain text mail password) | 1 | 🟡 MED |
| Security (version_details hardcoded CURL URL) | 1 | 🟡 MED — **NEW** |
| Data Integrity (menu delete leaves access) | 1 | 🟡 MED |
| Code Quality (mega controller 4,837 lines) | 1 | 🟡 MED |
| Code Quality (mega model 2,944 lines) | 1 | 🟡 MED |
| Code Quality (HTML in model) | 1 | 🟡 LOW |
| Code Quality (commented-out prod URLs) | 1 | 🟡 LOW |
| Code Quality (delete via GET) | Multiple | 🟡 MED |
| **Total** | **16+** | **6 CRITICAL/HIGH, 8 MED, 2 LOW** |

---

## 5. Round History

| Round | Date | Work Done | Coverage Delta |
|---|---|---|---|
| 1 | 2026-03-06 | Full brain build: all 10 files | 0% → 100% |
| R2 (Upgrade) | 2026-03-25 | Codebase diff, +3 ctrl/+4 model methods (KYC), 5 new tables, 3 new bugs, all line numbers updated | 100% → 100% |

---

## 6. Module Comparison (vs Other Brains)

| Aspect | Chit Settings | Scheme | Account | Chit Collection |
|---|---|---|---|---|
| Controller lines | **4,837** | 1,241 | 4,478 | 4,528 |
| Model lines | **2,944** | 854 | 3,651 | 2,358 |
| Methods total | **305** (123+182) | 77 | 158 | 197 |
| Tables owned | **35+** | 11 | 5 | 0 (API layer) |
| Bugs found | 16+ (6 CRITICAL/HIGH) | 15 (7 HIGH) | 18 (9 HIGH) | 46 (7 P0) |
| Complexity | **HIGHEST** (hub) | Medium | High | High |

> ⚠️ This is the **largest and most critical** module in the system — every other module depends on it for access control, settings, and master data.

---

## 7. Next Steps / Recommendations

1. **🔴 CRITICAL**: Add CSRF protection to `clear_database()` 
2. **🔴 HIGH**: Replace all raw `$_POST` with `$this->input->post()`
3. **🔴 HIGH**: Fix `update_notification_status()` L2743 — parameterize SQL
4. **🔴 HIGH**: Fix `getRef_nos()` L2194-2214 — parameterize SQL
5. **🔴 HIGH**: Uncomment `configDB()` calls at L1949, L2094 (config settings not being saved)
6. **🔴 HIGH**: Parameterize ALL SQL queries (50+ injection points)
7. **🟡 MED**: Fix `file_put_contents` at L570 to use `json_encode()`
8. **🟡 MED**: Fix `$general[tab_name]` → `$general['tab_name']` at L1957, L2108, L4519
9. **🟡 MED**: Remove hardcoded pm.logimaxindia.com URL from version_details() L4249
10. **🟡 MED**: Add delete cascades for menu → access records
11. **🟡 MED**: Encrypt SMTP passwords
12. **Archive**: Consider splitting this mega-controller into sub-controllers
