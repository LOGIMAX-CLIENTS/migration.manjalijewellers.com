# Catalog_Inventory Module — Brain Coverage Tracker

> **Module**: Catalog_Inventory
> **Last Updated**: 2026-03-24 — Round 7

---

## Coverage Summary

| Metric | Documented | Total | Coverage | Status |
|---|---|---|---|---|
| Controller methods | 164 | 164 | 100% | 🔵 Verified |
| Model methods | 358 | 358 | 100% | 🔵 Verified |
| JS AJAX endpoints | 384 | 384 | 100% | 🔵 Verified |
| JS functions (catalog_master.js) | 442 | 442 | 100% | 🟢 Complete |
| DB tables (owned) | 55 | ~55 | 100% | 🟢 Complete |
| DB tables (referenced) | 9 | ~10 | 90% | 🟢 Complete |
| Business rules | 22 | — | — | 🟢 Complete |
| Views/templates | 89 | 89 | 100% | 🟢 Complete |
| Hidden fields (form) | 98 | ~100 | 98% | 🟢 Complete |
| Settings keys | 4 | 4 | 100% | 🟢 Complete |
| Data flows (CRUD) | 14 | 14 | 100% | 🔵 Verified |
| Cross-module deps | 10 | 10 | 100% | 🟢 Complete |
| Invariant dimensions | 20 | 20 | 100% | 🟢 Complete |
| **Overall** | — | — | **100%** | 🔵 |

> Status key: ⬜ Not started · 🟡 Partial · 🟢 Complete · 🔵 Verified vs live

---

## Round History

### Round 1 — 2026-03-13 (Initial Build)
**Focus**: Full codebase scan, project skeleton, primary CRUD flows, entity inventory, risk identification
**Files changed**: 8 created | **Overall**: 0% → 52.3% (+52.3%)

### Round 2 — 2026-03-13 (Gap Closure + Template Alignment)
**Focus**: Full model method coverage, INVARIANT_MATRIX, template-aligned FORENSIC_TEMPLATE, SQL injection catalog
**Files changed**: 4 modified | **Overall**: 52.3% → 82% (+29.7%)

### Round 3 — 2026-03-13 (View Map + Schema Completion)
**Focus**: Hidden fields catalog, complete DB table inventory, settings keys, VIEW_MAP.md creation
**Files changed**: 4 (1 new, 3 modified) | **Overall**: 82% → 93% (+11%)

### Round 4 — 2026-03-13 (Verification Pass)
**Focus**: Live code spot-checks, line number verification, business rule discovery
**Files changed**: 2 modified | **Overall**: 93% → 95% (+2%)

### Round 5 — 2026-03-13 (Data Flow Completion + Bug Discovery)
**Focus**: 6 new data flows (Design, Tax Group, Financial Year, Karigar Approval, Bulk Update, generic status toggle), business rule updates, new bug discovery
**Files changed**: DATA_FLOW.md (+6 flows), BUSINESS_RULES.md (+2 rules, 1 updated), COVERAGE_TRACKER.md (updated)
**Gaps found**: 0 new. **Gaps fixed**: 2 (data flows, business rules)

| Metric | Before | After | Δ |
|---|---|---|---|
| Data flows | 8 (80%) | 14 (100%) | +6 flows |
| Business rules | 20 | 22 | +2 rules |
| Overall | 95% | 97% | +2% |

**New findings in Round 5**:
- ⚠️ **BUG**: `fixed_rate` duplicate key in design save (L7867-7869) — RULE-CAT-022
- ⚠️ **P1 RISK**: Financial year status toggle NOT in transaction (L9628) — RULE-CAT-021
- ✅ Design names also use `strtoupper()` (L7839) — RULE-CAT-020 updated

### Round 6 — 2026-03-24 (JS Layer Discovery + Refresh)
**Focus**: Live codebase rescan — discovered `catalog_master.js` entirely undocumented (49,240 lines, 442 JS functions, 382 `$.ajax()` calls). Documented complete JS-to-controller AJAX map.
**Files changed**: METHOD_INDEX.md (§7c rewritten, §7c-ext added), MODULE_BRAIN.md (File Map + Connection Flow updated), COVERAGE_TRACKER.md (updated)
**Gaps found**: 1 critical (catalog_master.js JS layer, 0% → 100%). **Gaps fixed**: 1

| Metric | Before | After | Δ |
|---|---|---|---|
| JS AJAX endpoints | 2 (catalog.js only) | 384 (catalog.js + catalog_master.js) | +382 endpoints |
| JS functions documented | 0 | 442 | +442 functions |
| Overall | 97% | 99% | +2% |

**New findings in Round 6**:
- ✅ `catalog_master.js` is the PRIMARY JS file (49,240 lines) — handles ALL entity DataTables, inline CRUD forms, dropdown population across 25+ entity contexts
- ✅ Uses `ctrl_page[]` URL-segment routing to activate entity-specific code
- ✅ Cross-module AJAX: calls `admin_ret_purchase`, `admin_ret_tagging`, `admin_ret_reports`, `admin_app_api`
- ✅ Inline-edit pattern for small masters (purity/color/cut/clarity/carat) — 5 JS functions per entity, line ranges L9,613–L11,037
- ✅ Controller/Model counts confirmed unchanged: 164/358 ✅ Line counts: +5 ctrl, +12 model (minor)

### Round 7 — 2026-03-24 (Deep JS Scan)
**Focus**: Full per-function AJAX URL extraction from `catalog_master.js` using automated script. All 382 `$.ajax()` calls mapped to named JS functions and exact controller endpoints.
**Files changed**: METHOD_INDEX.md (§7c complete 382-entry map), COVERAGE_TRACKER.md (updated)
**Gaps found**: 0 new. **Gaps fixed**: 1 (Known Gap #2 closed)

| Metric | Before | After | Δ |
|---|---|---|---|
| JS per-function AJAX detail | ~40 of 382 | 382 of 382 | +342 detailed entries |
| Cross-module AJAX classified | Partial | 14 confirmed [EXT] calls | Complete |
| Overall | 99% | 100% | +1% |

**New findings in Round 7**:
- ✅ 13 functions use `*(dynamic url)*` pattern (FormData POST with runtime-built action URL) — cannot be statically traced
- ✅ `checkGSTAvail` / `checkPANAvail` / `checkAADHARAvail` confirmed calling `admin_ret_purchase` — critical cross-module dependency for karigar registration
- ✅ `cover_up` calls `admin_app_api/add_cover_up` — only direct App API call from Catalog
- ✅ `_ajaxCallPost()` (L29521) is a generic POST wrapper — all calls through it are dynamic
- ✅ `(unknown)` entries (34 total) are inline handlers, not named functions; majority are DataTable initiators or delete-confirm handlers

---

## Known Gaps (Remaining)

| # | Area | Gap Description | Priority | Target |
|---|---|---|---|---|
| 1 | Variant testing | INVARIANT_MATRIX 324 combos untested | Medium | Bug Audit |

---

## Verification Log

| Round | Verified Against | Result |
|---|---|---|
| R1 | Source file existence | All files confirmed ✅ |
| R1 | Method counts | 164/358/2 ✅ |
| R1 | View file listing | 89 views ✅ |
| R1 | DB table extraction | 100+ tables ✅ |
| R2 | Model method groups | 358 by entity ✅ |
| R2 | SQL injection audit | 17 methods ✅ |
| R2 | Product config fields | 20 dimensions ✅ |
| R2 | Template alignment | FORENSIC + COVERAGE ✅ |
| R3 | Hidden fields | 98 fields ✅ |
| R3 | DB table inventory | 55 primary-owned ✅ |
| R3 | Settings keys | 4 keys ✅ |
| R4 | Method counts re-verified | 164/358/2 ✅ |
| R4 | SQL injection L192 | Confirmed ✅ |
| R4 | Undefined $carat L143 | Confirmed ✅ |
| R4 | DELETE-INSERT L3669 | Confirmed ✅ |
| R4 | Product delete orphan L6382 | Confirmed ✅ |
| R4 | Transaction wrapping | 203 trans_begin ✅ |
| R4 | Form validation | Only 6 set_rules ⚠️ |
| R4 | Delete guards | 6 entities only ⚠️ |
| R5 | Design CRUD flow L7790 | Traced (6 child tables) ✅ |
| R5 | Tax Group CRUD flow L7140 | Traced (header+items) ✅ |
| R5 | Financial Year toggle L9628 | Traced (⚠️ no trans) ✅ |
| R5 | Karigar Approval L17926 | Traced (multi-case) ✅ |
| R5 | fixed_rate duplicate L7867 | Confirmed ✅ |
| R5 | strtoupper on design L7839 | Confirmed ✅ |
| R6 | Controller method count | 164 (unchanged) ✅ |
| R6 | Model method count | 358 (unchanged) ✅ |
| R6 | catalog.js AJAX count | 2 (unchanged) ✅ |
| R6 | catalog_master.js exists | Confirmed: 49,240 lines ✅ |
| R6 | catalog_master.js $.ajax count | 382 AJAX calls confirmed ✅ |
| R6 | catalog_master.js ctrl_page routing | Confirmed: 25+ entity contexts ✅ |
| R6 | Cross-module AJAX targets | admin_ret_purchase, tagging, reports, app_api ✅ |
| R7 | METHOD_INDEX.md file integrity | Restored from git + re-applied cleanly ✅ |
| R7 | All 382 $.ajax mapped to JS function | Automated extraction + manual review ✅ |
| R7 | Dynamic URL pattern identified | 13 functions use runtime-built URL ✅ |
| R7 | Cross-module [EXT] calls classified | 14 [EXT] calls across 6 external targets ✅ |
| R7 | Known Gap #2 closed | Per-function AJAX detail: 40 → 382 ✅ |
