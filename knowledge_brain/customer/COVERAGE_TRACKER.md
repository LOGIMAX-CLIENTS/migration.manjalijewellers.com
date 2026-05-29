# Customer Module — Coverage Tracker

> **Brain Updated:** 2026-03-25 | **Rounds:** R1 + R2 + R3-Upgrade — **100% Coverage**

---

## Coverage Summary

| Metric | Covered | Total (actual) | Coverage | Status |
|---|---|---|---|---|
| Controller methods | 43 | 43 | **100%** | ✅ R1+R2 Complete |
| Model methods | 75 | 75 | **100%** | ✅ R1+R2 Complete |
| JS AJAX endpoints | 31 | 31 | **100%** | ✅ R1+R2 Complete |
| DB tables (owned) | 4 | 4 (customer, address, kyc, zone) | **100%** | ✅ R3 Verified |
| DB tables (referenced) | 20 | 20 | **100%** | ✅ R3 Updated |
| Business rules | 12 | 12 | **100%** | ✅ Complete |
| Views / templates | 3 | 3 | **100%** | ✅ Verified |
| Data flows | 7 | 7 | **100%** | ✅ Verified |
| **Schema analysis** | **4 owned, 20 referenced, 8 index gaps, 12 write ops** | — | — | **✅ NEW (R3)** |
| **Flow risk matrix** | **3 state machines, 12 inbound, 8 outbound, 10 reversal, 18 QA scenarios** | — | — | **✅ NEW (R3)** |
| **Invariant matrix** | **6 dimensions, 25+ invariants, 10 edge cases** | — | — | **✅ NEW (R3)** |
| Edit path KYC detail | Full | Full | **100%** | ✅ R2 Added |
| JS security scan | Full | Full | **100%** | ✅ R2 Added |
| **OVERALL** | | | **100%** | ✅ DONE |

---

## Round History

### Round R3-Upgrade — 2026-03-25 (Structural Upgrade)

**What was done**:
- **Created `SCHEMA_ANALYSIS.md`** — 4 owned tables (customer, address, kyc, zone) with full column maps, 20 referenced tables, 8 suspected missing indexes, 12 write operation inventory
- **Created `FLOW_RISK_MATRIX.md`** — 3 state machines (active, profile_complete, kyc_status), 12 inbound contracts, 8 outbound contracts, 10 reversal checks (~50% completeness), 18 QA-ready test scenarios (FR-CUS-001 to FR-CUS-018)
- **Created `INVARIANT_MATRIX.md`** — 6 dimensions (identity, visibility, write safety, auth, config grid, edge cases), 25+ invariants, 10 edge cases
- **Updated all 12 brain files** with R3-Upgrade headers
- **Verified current codebase line counts**:
  - Controller: 2,627L (was 2,628 in R2) — -1 line, 43 methods unchanged
  - Model: 1,254L — **identical** to R2, 75 methods unchanged
  - JS: 4,712L (was 4,679 in R2) — +33 lines
  - Views: 3 files — unchanged

**Before**: 100% (R2, 9 files)
**After**: 100% (R3, **12 files** — 3 standard brain files added)

**Δ This round**: +3 brain files (structural), codebase sync, 18 new QA scenarios, 25+ invariants

**Key findings this round**:
- Codebase essentially unchanged since R2 (8 days ago) — model identical, controller -1 line, JS +33 lines
- Reversal completeness ~50%: delete cleans address/customer/wallet but misses KYC records, log entries, and filesystem images
- Wallet creation race condition: `get_wallet_acc_number()` has no DB lock → concurrent adds may produce duplicate wallet numbers
- ERP sync has no transaction wrapping — partial sync possible across 3 tables

**Status**: ✅ Brain **COMPLETE at 100%** with all standard files. Ready for `/module-bug-audit`.

---

### Round 1 — What Was Done

**Date:** 2026-03-17  
**Files Analyzed:**
- `admin/application/controllers/admin_customer.php` (2628 lines, 43 methods)
- `admin/application/models/customer_model.php` (1254 lines, 75 methods)
- `admin/assets/js/customer.js` (~4679 lines, 31 AJAX URLs)
- `admin/application/views/master/customer/` (3 views: form, list, profile)

**Brain Files Created (R1):**
| File | Contents |
|---|---|
| `MODULE_BRAIN.md` | Architecture, constructor, 30+ routes, 12 tables, 12 risks, anti-pattern register |
| `METHOD_INDEX.md` | All 43 controller + 75 model methods (alpha sorted), JS→Controller map, Table→Method reverse map |
| `DATA_FLOW.md` | 7 flows: CREATE, EDIT, DELETE, KYC upload, agent allocation, profile update, ERP sync |
| `BUSINESS_RULES.md` | 12 business rules: customer limit, type, branch date, wallet creation, KYC types, delete guard |
| `CROSS_MODULE_MAP.md` | 14 external dependencies, shared table ownership, Mermaid diagram, cross-module AJAX URLs |
| `FORENSIC_TEMPLATE.md` | 7-layer investigation guide: symptom→DB queries→root cause classification |

---

## Round 2 — Deep Analysis

**Date:** 2026-03-17  
**Focus:** Edit path full trace, remaining methods, JS security scan, model last-mile bugs

**R2 Coverage Added:**
- `cus_post('Edit')` KYC update full decision tree (bug at L904 found)
- `allocate_employee_toCuctomers()` — copy-paste bug confirmed (CUS-BUG-018)
- `wallet_account_cus()` — else-branch undefined var (CUS-BUG-022)
- `getkycdata_byid()` — raw $_POST bypass (CUS-BUG-023)
- `mobile_available_import()` — undefined `$id_customer` (CUS-BUG-024)
- `set_image()` — missing `update_images()` call (CUS-BUG-019 P1)
- JS security: 19/31 AJAX no error handler, 35 console.log (CUS-BUG-031, 032)
- Zone CRUD: 2-digit year bug, dead flash message, no village dedup check
- `receiptFrmt()`: duplicate case 6 dead code
- `getFormatedNumber()`: uninitialized `$finalFormat`

**Brain Files Added (R2):**
| File | Contents |
|---|---|
| `DEEP_ANALYSIS_R2.md` | Full traces of 9 method groups + 15 new bugs (CUS-BUG-018 to 032) |
| `BUG_REGISTER.md` | All 32 bugs with root cause + fix guidance |

---

## All Bugs Found (R1 + R2)

> Full details with root cause + fix in `BUG_REGISTER.md`

| Bug ID | P | Location | One-line Description |
|---|---|---|---|
| CUS-BUG-001 | P1 | L1936 | `allocate_agent` `$total` overwrite — allocation always silent-fails |
| CUS-BUG-002 | P1 | L1758 | `download()` path traversal — no sanitization of `$file` |
| CUS-BUG-003 | P1 | model L546 | `Searchcustomer()` SQL injection |
| CUS-BUG-004 | P1 | model L139 | `ajax_get_customers()` SQL injection |
| CUS-BUG-005 | P1 | model L581 | `isCustomerExist()` SQL injection |
| CUS-BUG-006 | P1 | model L353 | `delete_customer()` orphans KYC records |
| CUS-BUG-007 | P2 | model L149 | `get_customer()` hardcoded `id_customer=1` in subquery |
| CUS-BUG-008 | P2 | L481 | `added_by=1` always hardcoded |
| CUS-BUG-009 | P2 | model L15 | Password is base64 not encrypted |
| CUS-BUG-010 | P2 | L593,1161,1454 | `getImageData()` defined 3× — redeclaration risk |
| CUS-BUG-011 | P2 | 8+ locations | `mkdir(0777)` world-writable dirs |
| CUS-BUG-012 | P2 | L1733,L1745 | GET-based status toggles — CSRF |
| CUS-BUG-013 | P2 | model L477 | `get_customer_by_mobile()` SQL injection |
| CUS-BUG-014 | P2 | JS L1000,1598 | JS calls `ajax_list` — no such controller method |
| CUS-BUG-015 | P2 | JS L1236 | JS calls `without_acc_details` — no such endpoint |
| CUS-BUG-016 | P3 | model L485 | `get_village()` returns 1 row for dropdown |
| CUS-BUG-017 | P3 | L1911 | `log_model` access style inconsistency |
| CUS-BUG-018 | P1 | L2575 | `allocate_employee` same `$total` overwrite bug |
| CUS-BUG-019 | P1 | L1680 | `set_image()` never calls `update_images()` — images lost |
| CUS-BUG-020 | P2 | L904 | Edit KYC: `get_kyc_byid($cus_id=0)` if update fails — KYC skipped |
| CUS-BUG-021 | P2 | L955 | Edit: `$kycData` undefined — should be `$kyc_data_img` |
| CUS-BUG-022 | P2 | L2622 | `wallet_account_cus()` else `$cus` undefined |
| CUS-BUG-023 | P2 | L2479 | `getkycdata_byid()` uses raw `$_POST` |
| CUS-BUG-024 | P2 | model L1125 | `mobile_available_import()` `$id_customer` undefined |
| CUS-BUG-025 | P2 | model L980 | `receiptFrmt()` duplicate case 6 — dead code |
| CUS-BUG-026 | P2 | L2494 | Zone add: `date('y-m-d')` = 2-digit year |
| CUS-BUG-027 | P2 | L2531 | Zone update: same 2-digit year bug |
| CUS-BUG-028 | P2 | L2538 | Zone update flash: "Rate successfully" copy-paste error |
| CUS-BUG-029 | P3 | L2515 | Zone delete: no village dependency check |
| CUS-BUG-030 | P3 | model L1116 | `getFormatedNumber()` uninitialized `$finalFormat` |
| CUS-BUG-031 | P3 | JS (19 calls) | 19/31 AJAX calls without error handlers |
| CUS-BUG-032 | P3 | JS (35 lines) | 35 `console.log` in production JS |

**Bug Summary (R1 + R2):**

| Severity | R1 | R2 | Total |
|---|---|---|---|
| P1 (High) | 6 | 2 | **8** |
| P2 (Medium) | 9 | 9 | **18** |
| P3 (Low) | 2 | 4 | **6** |
| **Total** | **17** | **15** | **32** |

---

## Known Gaps (After R1 + R2)

| Gap | Priority | Notes |
|---|---|---|
| `customer.js` client-side form validation logic (non-AJAX) | Very Low | UI-only, no server bugs expected |
| CSS analysis | N/A | No business logic in CSS |
| View `form.php` HTML structure deep trace | Very Low | Display-only, no logic |

**All material gaps from R1 have been closed in R2.**

---

## Verification Log

| Date | Round | What Was Verified | Method |
|---|---|---|---|
| 2026-03-17 | R1 | Controller method count (43) | PowerShell Select-String |
| 2026-03-17 | R1 | Model method count (75) | PowerShell Select-String |
| 2026-03-17 | R1 | JS AJAX endpoints (31) | PowerShell Select-String |
| 2026-03-17 | R1 | View files (3: form, list, profile) | PowerShell Get-ChildItem |
| 2026-03-17 | R1 | Brain files: 7 created | File listing |
| 2026-03-17 | R2 | `cus_post('Edit')` KYC path full trace | Code read L818–1553 |
| 2026-03-17 | R2 | Employee allocation bug verified | Code read L2570–2609 |
| 2026-03-17 | R2 | JS error handler scan (19/31 missing) | PowerShell script |
| 2026-03-17 | R2 | console.log count (35) | PowerShell |
| 2026-03-17 | R2 | Model L800–1254 full read | Code read |
| 2026-03-17 | R2 | Brain files: 9 total | File listing |

---

## Brain File Summary (Final)

| File | Lines | Purpose |
|---|---|---|
| `MODULE_BRAIN.md` | ~200 | Architecture, routes, tables, risks, anti-patterns |
| `METHOD_INDEX.md` | ~190 | All 118 methods + JS→Controller map |
| `DATA_FLOW.md` | ~270 | 7 data flows fully traced |
| `BUSINESS_RULES.md` | ~240 | 12 business rules documented |
| `CROSS_MODULE_MAP.md` | ~110 | 14 deps + Mermaid diagram |
| `FORENSIC_TEMPLATE.md` | ~200 | 7-layer investigation guide + SQL queries |
| `DEEP_ANALYSIS_R2.md` | ~260 | Edit path, JS security, model deep bugs |
| `BUG_REGISTER.md` | ~370 | 32 bugs with root cause + fix guidance |
| **`SCHEMA_ANALYSIS.md`** | **~180** | **4 owned tables, 20 referenced, index gaps, write ops** |
| **`FLOW_RISK_MATRIX.md`** | **~180** | **3 state machines, contracts, reversals, 18 QA scenarios** |
| **`INVARIANT_MATRIX.md`** | **~160** | **6 dimensions, 25+ invariants, 10 edge cases** |
| `COVERAGE_TRACKER.md` | This file | Progress log |

## Next Steps

- Run `/fix-single-bug` for CUS-BUG-001 (agent allocation) — highest user-visible impact
- Run `/fix-single-bug` for CUS-BUG-019 (set_image missing DB persist) — P1 data loss
- Run `/github-bug-tracking` to register P1 bugs as issues
- Run `/module-bug-audit` if a formal audit report is needed
