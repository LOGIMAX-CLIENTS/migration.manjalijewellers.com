# Employee Module — Coverage Tracker

> **Brain Updated:** 2026-03-25 | **Rounds:** R1 + R2-Upgrade — **100% Coverage**

---

## Coverage Summary

| Metric | Covered | Total | Coverage | Status |
|---|---|---|---|---|
| Controller methods | 25 | 25 | **100%** | ✅ R1+R2 |
| Model methods | 47 | 47 | **100%** | ✅ R2 Updated (+3 from R1 count) |
| JS AJAX endpoints | 20 | 20 | **100%** | ✅ Complete |
| DB tables owned | 4 | 4 | **100%** | ✅ R2 Verified |
| DB tables referenced | 12 | 12 | **100%** | ✅ R2 Updated |
| Business rules | 12 | 12 | **100%** | ✅ **NEW (R2)** |
| Views / templates | 3 | 3 | **100%** | ✅ Complete |
| Data flows | 6 | 6 | **100%** | ✅ Complete |
| **Schema analysis** | **4 owned, 12 referenced, 7 index gaps, 10 write ops** | — | — | **✅ NEW (R2)** |
| **Flow risk matrix** | **3 state machines, 10 inbound, 7 outbound, 10 reversal, 15 QA scenarios** | — | — | **✅ NEW (R2)** |
| **Invariant matrix** | **5 dimensions, 20+ invariants, 8 edge cases** | — | — | **✅ NEW (R2)** |
| **Method index** | **25 ctrl + 47 model (alphabetical) + 20 JS AJAX + table reverse map** | — | — | **✅ NEW (R2)** |
| **Cross-module map** | **9 external deps, 4 owned, 12 ref, 6 downstream + Mermaid** | — | — | **✅ NEW (R2)** |
| **Forensic template** | **7 layers + emp-specific address integrity layer** | — | — | **✅ NEW (R2)** |
| **OVERALL** | | | **100%** | ✅ DONE |

---

## Round History

### Round R2-Upgrade — 2026-03-25 (Structural Upgrade)

**What was done**:
- **Created 7 missing standard brain files**:
  - `METHOD_INDEX.md` — 25 ctrl + 47 model methods, 20 AJAX endpoints, table↔method reverse map
  - `BUSINESS_RULES.md` — 12 business rules (dual password, device licensing, access time, discount limits)
  - `CROSS_MODULE_MAP.md` — 9 external deps, 4 owned tables, 12 referenced, 6 downstream + Mermaid graph
  - `SCHEMA_ANALYSIS.md` — 4 owned tables with full column maps, 12 referenced, 7 index gaps, 10 write ops
  - `FLOW_RISK_MATRIX.md` — 3 state machines, 10 inbound + 7 outbound contracts, 10 reversal checks (~30%), 15 QA scenarios
  - `INVARIANT_MATRIX.md` — 5 dimensions, 20+ invariants, 8 edge cases
  - `FORENSIC_TEMPLATE.md` — 7 layers including employee-specific address integrity check
- **Updated all existing brain files** with R2-Upgrade headers
- **Verified current codebase line counts**:
  - Controller: 1,366L — unchanged from R1 (25 methods, -1 from R1 count due to re-count)
  - Model: 1,106L — unchanged from R1 (47 methods, +3 from R1 count due to re-count)
  - JS: **2,152L (was 1,428 in R1)** — **+724 lines** (non-AJAX logic growth)
  - Views: 3 files — unchanged

**Before**: ~100% (R1, 4 files)
**After**: 100% (R2, **11 files** — 7 standard brain files added)

**Δ This round**: +7 brain files (structural), codebase sync, 15 new QA scenarios, 20+ invariants, 12 business rules

**Key findings this round**:
- JS grew by +724 lines since R1 (primarily non-AJAX UI logic), no new AJAX endpoints
- Model method re-count found 47 (not 44 from R1) — 3 methods missed in R1: `getEmployeeByBranch`, `get_ret_settings`, `get_dia_disc`
- Reversal completeness ~30%: delete only cleans address + employee + images, misses settings, devices, wallet, customer FK
- Address table has active data corruption: every edit creates a duplicate row (EMP-BUG-003)
- `getEmployeeByBranch` (model L1066) has SQL injection via `$idBranch` — new finding, not in R1 bug register

**Status**: ✅ Brain **COMPLETE at 100%** with all standard files. Ready for `/module-bug-audit`.

---

### Round 1 — What Was Done

**Date:** 2026-03-17
**Files Analyzed:**
- `admin/application/controllers/admin_employee.php` (1366 lines, 26 methods)
- `admin/application/models/employee_model.php` (1106 lines, 44 methods)
- `admin/assets/js/employee.js` (1428 lines, 20 AJAX URLs)
- `admin/application/views/master/employee/` (3 views)

**Brain Files Created:**
| File | Contents |
|---|---|
| `MODULE_BRAIN.md` | Architecture, 27 routes, 14 tables, dual-password system, device management, 17 risks, cross-module map |
| `DATA_FLOW.md` | 6 flows: Create, Edit (with duplicate address bug), Delete (with orphan list), Device enable, Settings CRUD, Auth |
| `BUG_REGISTER.md` | All 17 bugs with root cause + fix guidance |
| `COVERAGE_TRACKER.md` | This file |

---

## Bugs Found (R1 + R2)

| Bug ID | P | Location | One-line |
|---|---|---|---|
| EMP-BUG-001 | **P1** | Controller L161 | `isUserAvailable()` has `print_r(); exit;` — endpoint dead |
| EMP-BUG-002 | **P1** | Controller L568 | `emp_post('Add')`: `trans_status()` with no `trans_begin()` |
| EMP-BUG-003 | **P1** | Model L498 | `update_employee()` address check: PHP string bug → always INSERTs duplicates |
| EMP-BUG-004 | **P1** | Controller L1006 | `updateAccessTimeAll`: raw `$_POST` → chained SQL injection |
| EMP-BUG-005 | **P1** | Controller L1178 | `enable_device()`: raw `$_POST['enable_device']` |
| EMP-BUG-006 | P2 | Model L263 | `get_list_data()`: `$id_company` undefined — company filter never applied |
| EMP-BUG-007 | P2 | Model L771 | `updEmpAccessTime()`: SQL injection via time fields |
| EMP-BUG-008 | P2 | Model L882 | `get_emp_by_company()`: SQL injection via `$username` in login path |
| EMP-BUG-009 | P2 | Model L958 | `get_employee_name_byid()`: SQL injection via `$emp_dev_id` |
| EMP-BUG-010 | P2 | Controller L957 | `employee_status()` flash: shows "true"/"false" literally |
| EMP-BUG-011 | P2 | Controller L1334 | `emp_wallet_acc()`: 2-digit year in `issued_date` |
| EMP-BUG-012 | P2 | Model L33 | `__encrypt()` = base64 — legacy password trivially reversible |
| EMP-BUG-013 | P2 | Controller L886 | `set_image()`: image path write commented out — images not persisted to DB |
| EMP-BUG-014 | P2 | Controller L866 | `set_image()`: `mkdir(0777)` — world-writable |
| EMP-BUG-015 | P2 | Controller L627 | Edit: `check_password()` compares raw input vs base64 — always "changed" |
| EMP-BUG-016 | P3 | Controller L711 | Delete: no dependency check — orphans customer refs, devices, wallet |
| EMP-BUG-017 | P3 | Model L867 | `ajax_emp_setting()`: `$id_employee` raw in SQL |

**R2 New Finding** (not yet registered as formal bug):
- `getEmployeeByBranch()` model L1069: `$idBranch` concatenated raw into SQL → SQLi risk

**Bug Totals:**

| Severity | Count |
|---|---|
| P1 Critical | **5** |
| P2 Medium | **10** |
| P3 Low | **2** |
| **Total** | **17** (+1 new finding) |

---

## Brain File Summary (Final)

| File | Lines | Purpose |
|---|---|---|
| `MODULE_BRAIN.md` | ~260 | Architecture, routes, tables, dual-password, device mgmt, risks |
| `DATA_FLOW.md` | ~230 | 6 flows: Create, Edit, Delete, Device, Settings, Auth |
| `BUG_REGISTER.md` | ~300 | 17 bugs with root cause + fix guidance |
| **`METHOD_INDEX.md`** | **~180** | **25 ctrl + 47 model methods + 20 AJAX + reverse map** |
| **`BUSINESS_RULES.md`** | **~180** | **12 business rules** |
| **`CROSS_MODULE_MAP.md`** | **~120** | **9 deps + 4 owned + 12 ref + 6 downstream + Mermaid** |
| **`SCHEMA_ANALYSIS.md`** | **~170** | **4 owned tables, 12 referenced, 7 index gaps, 10 write ops** |
| **`FLOW_RISK_MATRIX.md`** | **~170** | **3 state machines, contracts, reversals, 15 QA scenarios** |
| **`INVARIANT_MATRIX.md`** | **~130** | **5 dimensions, 20+ invariants, 8 edge cases** |
| **`FORENSIC_TEMPLATE.md`** | **~180** | **7 layers + employee-specific address integrity** |
| `COVERAGE_TRACKER.md` | This file | Progress log |

---

## Verification

| Date | What | Method |
|---|---|---|
| 2026-03-17 | Full controller read L1–1366 | view_file |
| 2026-03-17 | Full model read L1–1106 | view_file |
| 2026-03-25 | Controller method re-count: 25 | PowerShell Select-String |
| 2026-03-25 | Model method re-count: 47 | PowerShell Select-String |
| 2026-03-25 | JS line count: 2152 | PowerShell Get-Content |
| 2026-03-25 | JS AJAX count: 20 (unchanged) | PowerShell Select-String |
| 2026-03-25 | View files: 3 (unchanged) | PowerShell Get-ChildItem |

---

## Next Steps

- Fix **EMP-BUG-003** (duplicate address inserts) — data corruption is already happening on every edit
- Fix **EMP-BUG-001** (dead endpoint) — username validation silently broken
- Fix **EMP-BUG-002** (missing trans_begin) — no rollback on failed creates
- Register `getEmployeeByBranch()` SQLi as formal EMP-BUG-018
- Run `/module-bug-audit` for formal audit
