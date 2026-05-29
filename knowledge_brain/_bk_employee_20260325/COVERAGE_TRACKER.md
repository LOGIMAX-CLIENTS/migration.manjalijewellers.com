# Employee Module — Coverage Tracker

> **Brain Updated:** 2026-03-17 | **Round:** 1 Complete — **~100% Coverage**

---

## Coverage Summary

| Metric | Covered | Total | Coverage | Status |
|---|---|---|---|---|
| Controller methods | 26 | 26 | **100%** | ✅ Complete |
| Model methods | 44 | 44 | **100%** | ✅ Complete |
| JS AJAX endpoints | 20 | 20 | **100%** | ✅ Complete |
| DB tables owned | 4 | 4 | **100%** | ✅ Complete |
| DB tables referenced | 10 | 10 | **100%** | ✅ Complete |
| Business rules | 8 | 8 | **100%** | ✅ Complete |
| Views / templates | 3 | 3 | **100%** | ✅ Complete |
| Data flows | 6 | 6 | **100%** | ✅ Complete |
| **OVERALL** | | | **~100%** | ✅ DONE |

---

## Round 1 — What Was Done

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

## Bugs Found (Round 1)

| Bug ID | P | Location | One-line |
|---|---|---|---|
| EMP-BUG-001 | **P1** | Controller L161 | `isUserAvailable()` has `print_r(); exit;` — endpoint dead in production |
| EMP-BUG-002 | **P1** | Controller L568 | `emp_post('Add')`: `trans_status()` with no `trans_begin()` — no rollback |
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

**Bug Totals:**

| Severity | Count |
|---|---|
| P1 Critical | **5** |
| P2 Medium | **10** |
| P3 Low | **2** |
| **Total** | **17** |

---

## Key Architectural Notes

1. **Dual password system** — `passwd` (base64 legacy) + `pwd_hash` (bcrypt). Login uses bcrypt only but both are written.
2. **Device licensing** — `employee_devices` + `chit_settings.chitCollectionEmpCount` controls how many mobile devices can be enabled for collection app.
3. **Employee settings auto-created** — On Create, a default `employee_settings` record is inserted (full-day access). Admin must configure discount limits separately.
4. **Password visible to superadmin** — If `id_branch == ''` (superadmin), the encoded password is decoded and pre-filled in the edit form.

---

## Known Gaps

| Gap | Priority | Notes |
|---|---|---|
| CSS analysis | N/A | No business logic in CSS |
| `form.php` / `list.php` detailed HTML trace | Very Low | Display-only for brain purposes |

**All material code paths fully traced in Round 1.**

---

## Verification

| Date | What | Method |
|---|---|---|
| 2026-03-17 | Controller method count (26) | PowerShell Select-String |
| 2026-03-17 | Model method count (44) | PowerShell Select-String |
| 2026-03-17 | JS AJAX endpoint count (20) | PowerShell Select-String |
| 2026-03-17 | View files (3) | PowerShell Get-ChildItem |
| 2026-03-17 | Full controller read L1–1366 | view_file |
| 2026-03-17 | Full model read L1–1106 | view_file |

---

## Next Steps

- Fix **EMP-BUG-003** (duplicate address inserts) — data corruption is already happening on every edit
- Fix **EMP-BUG-001** (dead endpoint) — username validation silently broken
- Fix **EMP-BUG-002** (missing trans_begin) — no rollback on failed creates
- Run `/fix-single-bug` for each P1 bug
- Run `/github-bug-tracking` to register P1 bugs as issues
