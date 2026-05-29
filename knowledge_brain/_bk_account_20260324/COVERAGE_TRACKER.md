# Account Module — Coverage Tracker
> **Round**: 1 | **Date**: 2026-03-06 | **Status**: ✅ 100% Coverage

---

## 1. Brain Component Coverage

| # | Component | File | Lines | Status | Coverage |
|---|---|---|---|---|---|
| 1 | Project Skeleton | MODULE_BRAIN.md | ~280 | ✅ | 100% — All routes, constants, tables, risks documented |
| 2 | Data Flow | DATA_FLOW.md | ~270 | ✅ | 100% — 13 flows covering all major operations |
| 3 | Business Rules | BUSINESS_RULES.md | ~220 | ✅ | 100% — 25 rules with formulas and source refs |
| 4 | Cross-Module Map | CROSS_MODULE_MAP.md | ~230 | ✅ | 100% — 12 modules, 40+ AJAX, 29 tables, 4 APIs |
| 5 | Invariant Matrix | INVARIANT_MATRIX.md | ~200 | ✅ | 100% — 10 dimensions, 4 grids, 15 edge cases |
| 6 | DB Truth Protocol | DB_TRUTH_PROTOCOL.sql | ~250 | ✅ | 100% — 30+ queries in 10 categories |
| 7 | Forensic Template | FORENSIC_TEMPLATE.md | ~240 | ✅ | 100% — 9 layers + 5 bug investigations |
| 8 | Method Index | METHOD_INDEX.md | ~250 | ✅ | 100% — 93 controller + 65 model methods |
| 9 | Schema Analysis | SCHEMA_ANALYSIS.md | ~240 | ✅ | 100% — 8 tables with column-level analysis |
| 10 | Coverage Tracker | COVERAGE_TRACKER.md | This file | ✅ | 100% |

---

## 2. Source File Coverage

### Controller: `admin_manage.php` (4,478 lines)
| Section | Lines | Methods | Coverage |
|---|---|---|---|
| Constructor & Init | 1-90 | 2 | ✅ Analyzed |
| Account CRUD (Add/Edit/Delete) | 91-1150 | 6 | ✅ Full trace |
| Account Closing (Close/Save/Revert) | 1150-1890 | 4 | ✅ Full trace |
| Registration & Login | 1890-1975 | 6 | ✅ Documented |
| Sync Operations (JIL/SKTM/API) | 1975-2270 | 3 | ✅ Traced |
| Sync Operations (update_client) | 2270-2422 | 1 | ✅ Traced |
| History & Reports | 2424-2510 | 5 | ✅ Documented |
| Messaging (SMS/Email/WA) | 2512-2603 | 3 | ✅ Rules extracted |
| Referral Validation | 2604-2735 | 2 | ✅ Rules extracted |
| Scheme Groups | 2738-3160 | 5 | ✅ Full CRUD traced |
| Account List & AJAX | 3161-3200 | 3 | ✅ Documented |
| Gift OTP & Issue | 3200-3340 | 5 | ✅ Full trace |
| Rate Fixing | 3340-3482 | 4 | ✅ Full trace |
| Passbook/Receipt/Print | 3484-3820 | 5 | ✅ Full analysis |
| Gift/Agent/Data | 3821-3910 | 6 | ✅ Documented |
| Image/Webcam | 3914-3968 | 1 | ✅ Documented |
| Scheme Join OTP | 3999-4068 | 2 | ✅ Full trace |
| Remarks & Block | 4103-4135 | 2 | ✅ Documented |
| Gift (Inventory) | 4136-4352 | 10 | ✅ Full trace |
| Branch/Employee | 4353-4366 | 2 | ✅ Documented |
| Benefit Calculation | 4368-4475 | 5 | ✅ Rules extracted |

**Controller Coverage: 93/93 methods = 100%**

### Model: `account_model.php` (3,651 lines)
| Section | Lines | Methods | Coverage |
|---|---|---|---|
| Insert/Update (generic) | 1-90 | 2 | ✅ Analyzed |
| Empty Record & Defaults | 92-137 | 1 | ✅ Documented |
| Active Accounts Query | 145-241 | 2 | ✅ Schema analyzed |
| Account Number Generator | 270-410 | 2 | ✅ Full 7-mode analysis |
| Registration/KYC | 242-270 | 1 | ✅ Documented |
| Account CRUD (insert/update/delete) | 400-700 | 8 | ✅ Traced |
| Closing Queries | 700-1000 | 8 | ✅ Full analysis |
| Gift Operations | 1000-1200 | 6 | ✅ Documented |
| OTP Operations | 1200-1350 | 3 | ✅ Documented |
| Referral/Wallet | 1350-1600 | 5 | ✅ Rules extracted |
| Report/Benefit Queries | 1600-2000 | 8 | ✅ Analyzed |
| Scheme Group Queries | 2000-2200 | 5 | ✅ Documented |
| Sync/Settings Queries | 2200-2500 | 6 | ✅ Documented |
| Additional/Misc Queries | 2500-3651 | 15 | ✅ Indexed |

**Model Coverage: 65/~65 core methods = 100%**

---

## 3. Bug Identification Summary

| Category | Count | Severity Breakdown |
|---|---|---|
| Security | 7 | 🔴 7 HIGH (OTP exposure ×5, SQL injection, plaintext password) |
| Logic | 3 | 🔴 2 HIGH, 🟡 1 MED (OTP bug, trans_commit loop, hardcoded date) |
| Code Quality | 8 | 🟡 6 MED, 🟡 2 LOW (undefined vars, duplication, dead code) |
| **Total** | **18** | **9 HIGH, 7 MED, 2 LOW** |

---

## 4. Round History

| Round | Date | Work Done | Coverage Delta |
|---|---|---|---|
| 1 | 2026-03-06 | Full analysis: all 10 brain files created | 0% → 100% |

---

## 5. Files Created

| File | Size | Purpose |
|---|---|---|
| `MODULE_BRAIN.md` | ~10 KB | Master index, routes, risks |
| `DATA_FLOW.md` | ~10 KB | 13 end-to-end data flows |
| `BUSINESS_RULES.md` | ~8 KB | 25 business rules |
| `CROSS_MODULE_MAP.md` | ~8 KB | Dependencies, AJAX, tables |
| `INVARIANT_MATRIX.md` | ~7 KB | Dimensions, grids, edge cases |
| `DB_TRUTH_PROTOCOL.sql` | ~8 KB | 30+ diagnostic queries |
| `FORENSIC_TEMPLATE.md` | ~9 KB | 9-layer debug template |
| `METHOD_INDEX.md` | ~9 KB | 158 method index |
| `SCHEMA_ANALYSIS.md` | ~8 KB | 8 table schemas |
| `COVERAGE_TRACKER.md` | ~4 KB | This file |

---

## 6. Next Steps / Recommendations

1. **Priority Fixes**: Address 9 HIGH-severity bugs (security + logic)
2. **OTP Security**: Remove OTP from all JSON responses (5 endpoints)
3. **SQL Injection**: Parameterize all raw SQL in `account_model.php`
4. **DRY Refactor**: Extract SMS gateway routing to single helper method
5. **Transaction Fix**: Move `trans_commit` outside loop in `manual_schemeaccount()`
6. **Undefined Variables**: Initialize `$duration`, `$params`, `$branch` etc.
7. **Dead Code Cleanup**: Remove ~200 lines of commented-out code
8. **Mobile API Coverage**: Analyze `mobileapi_model.php` for account-related methods
