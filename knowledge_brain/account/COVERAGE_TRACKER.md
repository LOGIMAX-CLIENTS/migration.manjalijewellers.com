# Account Module — Coverage Tracker
> **Round**: 2 | **Date**: 2026-03-24 | **Status**: ✅ 100% Coverage

> **🔄 UPGRADED BRAIN**
> Previous version: Round 1 (2026-03-06)
> Upgraded to: Round 2 (2026-03-24)
> Changes: 0 methods added, 0 removed. `formatMetalWeight()` centralized. +1 new brain file (FLOW_RISK_MATRIX.md)

---

## 1. Brain Component Coverage

| # | Component | File | Status | Coverage |
|---|---|---|---|---|
| 1 | Module Brain | MODULE_BRAIN.md | ✅ | 100% — Architecture, 47 routes, 29 tables, 20 risks, upgrade notice |
| 2 | Data Flow | DATA_FLOW.md | ✅ | 100% — 13 flows covering all major operations |
| 3 | Flow Risk Matrix | FLOW_RISK_MATRIX.md | ✅ 🔄 NEW | 100% — 4 state machines, 18 contracts, 18 QA scenarios |
| 4 | Business Rules | BUSINESS_RULES.md | ✅ | 100% — 25 rules with formulas and source refs |
| 5 | Cross-Module Map | CROSS_MODULE_MAP.md | ✅ | 100% — 12 modules, 40+ AJAX, 29 tables, 4 APIs |
| 6 | Invariant Matrix | INVARIANT_MATRIX.md | ✅ | 100% — 10 dimensions, 4 grids, 15 edge cases |
| 7 | DB Truth Protocol | DB_TRUTH_PROTOCOL.sql | ✅ | 100% — 30+ queries in 10 categories |
| 8 | Forensic Template | FORENSIC_TEMPLATE.md | ✅ | 100% — 9 layers + 5 bug investigations |
| 9 | Method Index | METHOD_INDEX.md | ✅ | 100% — 93 controller + 65 model methods |
| 10 | Schema Analysis | SCHEMA_ANALYSIS.md | ✅ | 100% — 8 tables with column-level analysis |
| 11 | Coverage Tracker | COVERAGE_TRACKER.md | ✅ | This file |

---

## 2. Source File Coverage

### Controller: `admin_manage.php` (4,476 lines)

**Controller Coverage: 93/93 methods = 100%**

🔄 R2 Changes (Commit 45c218ff):
- `close_account_form('Close')` L1574-1598: Replaced 4× `trim_decimal()/number_format()` with `formatMetalWeight()` for weight-based closing balance
- Amount-based closing balance now always uses `number_format(..., 2)` (hardcoded 2 decimals)
- Weight-based closing balance now uses centralized `formatMetalWeight()` helper

### Model: `account_model.php` (3,656 lines)

**Model Coverage: 65/~65 core methods = 100%**

🔄 R2 Changes:
- **Commit 3ccb4ac1**: `get_closed_account_by_id()` L668+: Added `is_weight_scheme` calculation (type detection for amount vs weight) + `formatMetalWeight()` for `closing_balance` when weight scheme
- **Commit 45c218ff**: `get_outstanding_by_scheme()` L2871+: Replaced 2× `trim_decimal()/number_format()` with `formatMetalWeight()` for `opening_wgt` and `balance_weight`

### JS: `scheme_account.js` (4,779 lines)

🔄 R2 Changes (Commit 45c218ff):
- 10 occurrences of `trimDecimal()`/`.toFixed(3)` → `formatMetalWeight()`
- Key areas: `calculate_closing_balance()`, `calc_booking_amtwgt()`, `set_closed_acc_list()`, voucher weight calculations

### Views

🔄 R2 Changes (Commit 45c218ff):
- `closing/form.php`: Weight display `→ formatMetalWeight($tot_wgt)`
- `print/passbook_back.php`: Weight display `→ formatMetalWeight()`

---

## 3. Round History

| Round | Date | Work Done | Coverage Delta |
|---|---|---|---|
| 1 | 2026-03-06 | Full brain build: all 10 files | 0% → 100% |
| 2 | 2026-03-24 | **UPGRADE** — Diffed 2 commits (45c218ff: `formatMetalWeight()` centralization, 3ccb4ac1: model `is_weight_scheme` + weight formatting). New: FLOW_RISK_MATRIX.md (4 state machines, 10+8 contracts, 12.5% delete reversal score, 50% revert reversal, 18 QA scenarios). Updated: MODULE_BRAIN.md (+upgrade notice, +2 risks, +JS line count). All headers → Round 2. Backed up to `_bk_account_20260324/` | 100% maintained |

---

## 4. Code Changes Since Last Brain (2 Commits)

| # | Commit | Date | Author | Description | Files Changed | Brain Impact |
|---|---|---|---|---|---|---|
| 1 | `45c218ff` | 2026-03-11 | — | Unify metal weight formatting | Controller (+4/-4), Model (+2/-2), JS (+12/-11), 2 views | `formatMetalWeight()` replaces scattered `trim_decimal()`/`number_format()`/`.toFixed(3)` |
| 2 | `3ccb4ac1` | 2026-03-16 | — | Changes from another client | Model (+10/-0) | `get_closed_account_by_id()` adds `is_weight_scheme` + weight-aware closing_balance |

---

## 5. Bug Summary

| Category | Count | Severity Breakdown |
|---|---|---|
| Security | 7 | 🔴 7 HIGH (OTP exposure ×5, SQL injection, plaintext password) |
| Logic | 3 | 🔴 2 HIGH, 🟡 1 MED (OTP bug, trans_commit loop, hardcoded date) |
| Code Quality | 8 | 🟡 6 MED, 🟡 2 LOW (undefined vars, duplication, dead code) |
| 🔄 R2: Reversal Gaps | 2 | 🔴 1 HIGH (delete 12.5%), 🟡 1 MED (revert 50%) |
| **Total** | **20** | **10 HIGH, 8 MED, 2 LOW** |

---

## 6. File Sizes

| File | Round 1 | Round 2 | Growth |
|---|---|---|---|
| MODULE_BRAIN.md | 11.8KB | 12.8KB | +8% |
| DATA_FLOW.md | 10.7KB | 10.7KB | — |
| FLOW_RISK_MATRIX.md | — | 8.5KB | 🔄 NEW |
| BUSINESS_RULES.md | 8.8KB | 8.8KB | — |
| CROSS_MODULE_MAP.md | 7.0KB | 7.0KB | — |
| INVARIANT_MATRIX.md | 5.7KB | 5.7KB | — |
| DB_TRUTH_PROTOCOL.sql | 10.9KB | 10.9KB | — |
| FORENSIC_TEMPLATE.md | 9.3KB | 9.3KB | — |
| METHOD_INDEX.md | 11.8KB | 11.8KB | — |
| SCHEMA_ANALYSIS.md | 8.4KB | 8.4KB | — |
| COVERAGE_TRACKER.md | 5.4KB | 6.5KB | +20% |
| **TOTAL** | **89.9KB** | **100.5KB** | **+12%** |

---

## 7. Next Steps / Recommendations

1. **Priority Fixes**: Address 10 HIGH-severity bugs (security + logic + reversal gaps)
2. **OTP Security**: Remove OTP from all JSON responses (5 endpoints)
3. **SQL Injection**: Parameterize all raw SQL in `account_model.php`
4. **DELETE Cascade**: Add cleanup for all 8 child tables in `delete_account()` — current score: **12.5%**
5. **REVERT Reversal**: Add referral deduction reversal and agent loyalty reversal to `close_account_form('Revert')`
6. **DRY Refactor**: Extract SMS gateway routing to single helper method
7. **Transaction Fix**: Move `trans_commit` outside loop in `manual_schemeaccount()`
8. **Validate FLOW_RISK_MATRIX test scenarios** FR-ACC-001 through FR-ACC-018
9. **Run `/module-bug-audit`** to systematically scan and prioritize fixes
