# 📋 Estimation Module — Consolidated Bug Report

> **Module**: Estimation (Full Module Audit)  
> **Audit Date**: 2026-02-17  
> **Method**: AI Static Analysis (6 Rounds: Code → Schema → Model Deep Dive → JS/View → JS Calculations → Validation/AJAX)  
> **Scope**: Controller (3,574 lines, 64 methods), Model (3,006 lines, 112 methods), JS (31,391 lines), Views (2,526 lines), DB Schema

---

## Executive Summary

**65 bugs identified** across the Estimation module: **9 P0 (critical)**, **22 P1 (major)**, **26 P2 (minor)**, **8 P3 (cosmetic)**.

> [!CAUTION]
> Round 6 discovered `cancel_order_tag()` calls `trans_commit()` on failure instead of `trans_rollback()` — failed order cancellations persist in a corrupt state. Also found old metal rate=0 passes validation due to `&&`/`||` logic error.

The top 10 most damaging bugs are:
1. **EST-R301** — SQL injection in 10+ model methods (`$searchField` controls column name!)
2. **EST-R501** — Market rate tax uses wrong variable — all market rate comparisons are invalid
3. **EST-R502** — `get_tag_barcode_data` uses undefined `items.tag_id` — duplicate check always bypasses
4. **EST-R601** — `cancel_order_tag` commits on failure instead of rollback — data corruption
5. **EST-R401** — EDA validates Home Bill with wrong function
6. **EST-R402** — EDA uses `length >= 0` (always true) — empty tables pass validation
7. **EST-001 + EST-S01** — Gift vouchers NEVER saved (wrong variable AND schema lacks auto-increment)
8. **EST-R302** — Cartesian JOIN in `get_bill_no_format_detail` (`b.bill_type = b.bill_type`)
9. **EST-R303** — Tax subquery missing GROUP BY — wrong tax percentages for all tag searches
10. **EST-002** — Custom item `market_rate_tax` stores cost instead of tax

---

## Bug Distribution

| Sub-Module | P0 | P1 | P2 | P3 | Total |
|---|---|---|---|---|---|
| Add Estimation (save/update/delete) | 2 | 5 | 5 | 2 | **14** |
| Estimation List | — | — | 1 | — | **1** |
| Estimate Discount Approval | — | — | 1 | — | **1** |
| **DB Schema (Round 2)** | **1** | **3** | **3** | — | **7** |
| **Model/Data-Flow (Round 3)** | **1** | **5** | **5** | **1** | **12** |
| **JS/View (Round 4)** | **2** | **4** | **3** | **2** | **11** |
| **JS Calculations (Round 5)** | **2** | **3** | **3** | **1** | **9** |
| **Validation/AJAX (Round 6)** | **1** | **2** | **5** | **2** | **10** |
| **Total** | **9** | **22** | **26** | **8** | **65** |

---

## All Bugs — Master Index

### Round 1: Code-Level Bugs (16)

| ID | Severity | Title | Fix Readiness |
|---|---|---|---|
| EST-001 | **P0** | Gift voucher save uses undefined `$arrayMaterials` | ✅ Ready |
| EST-002 | **P0** | Custom item `market_rate_tax` stores `market_rate_cost` | ✅ Ready |
| EST-003 | **P1** | Delete leaves orphan records in 6+ tables | ✅ Ready |
| EST-004 | **P1** | No `form_secret` validation — double-submit risk | ✅ Ready |
| EST-005 | **P1** | Child tag stones never saved — wrong index | ✅ Ready |
| EST-006 | **P1** | Update path missing deletes — duplicate rows | ✅ Ready |
| EST-007 | **P1** | Update chit missing `rate_per_gram` field | ✅ Ready |
| EST-008 | P2 | `est_date` format bug with `date()` function | ✅ Ready |
| EST-009 | P2 | Order items `net_wt` always equals `gross_wt` | ⚠️ Needs investigation |
| EST-010 | P2 | Custom charges loop only keeps last charge | ⚠️ Needs investigation |
| EST-011 | P2 | Catalog stones missing `uom_id` field | ✅ Ready |
| EST-012 | P2 | Diamond amount field name mismatch save vs update | ✅ Ready |
| EST-013 | P3 | `update_status` undefined variable + wrong redirect | ✅ Ready |
| EST-014 | P3 | 20+ debug statements in production | ✅ Ready |
| ESTL-001 | P2 | Default case accepts any action value | ✅ Ready |
| ESTDA-001 | P2 | No separation of duties on discount approval | ⚠️ Needs design |

### Round 2: Schema-Level Bugs (7)

| ID | Severity | Title | Fix Readiness |
|---|---|---|---|
| EST-S01 | **P0** | `gift_voucher_id` NOT NULL without AUTO_INCREMENT — inserts fail | ✅ Ready (ALTER TABLE) |
| EST-S02 | **P1** | `ret_est_other_metals` uses MyISAM — not transactional | ✅ Ready (ALTER TABLE) |
| EST-S03 | P2 | Integer truncation: `wastage_per`, `act_wast_per`, `disc_per`, `bulk_was_disc_per` | ✅ Ready (ALTER TABLE) |
| EST-S04 | **P1** | `form_secret` UNIQUE constraint exists but never populated | ✅ Ready |
| EST-S05 | P2 | `esti_date` is `date` but receives `datetime` string | ✅ Ready |
| EST-S06 | **P1** | Delete/update paths miss 6 child tables (confirmed with exact table list) | ✅ Ready |
| EST-S07 | P2 | `ret_est_gift_voucher_details` has no PRIMARY KEY | ✅ Ready (ALTER TABLE) |

### Round 3: Model & Data-Flow Bugs (12)

| ID | Severity | Title | Fix Readiness |
|---|---|---|---|
| EST-R301 | **P0** | SQL Injection in 10+ model methods (column-name injection via `$searchField`) | ⚠️ Systematic refactor |
| EST-R302 | **P1** | Cartesian JOIN: `b.bill_type = b.bill_type` in `get_bill_no_format_detail` | ✅ Ready (1-char fix) |
| EST-R303 | **P1** | Tax subquery missing GROUP BY — wrong tax for all tag searches (×3 methods) | ✅ Ready |
| EST-R304 | **P1** | `$returndata` typo in `getOrderBySearch` — PO details silently lost | ✅ Ready (1-char fix) |
| EST-R305 | **P1** | `$data` param overwritten in `getTaggingSearchByCollection` | ✅ Ready |
| EST-R306 | P2 | Uninitialized `$dateofbirth`/`$dateofwed` in `updateCustomer` | ✅ Ready |
| EST-R307 | P2 | Base64 "encryption" used as password hash | ⚠️ Needs migration |
| EST-R308 | **P1** | Cartesian JOIN in `getCompanyDetails` (JOIN without ON condition) | ✅ Ready |
| EST-R309 | P2 | Incomplete WHERE clause in `get_chit_details` | ✅ Ready |
| EST-R310 | P2 | Null dereference in `getOldMetalRate` when no rate exists | ✅ Ready |
| EST-R311 | P2 | Edit case missing access control | ✅ Ready |
| EST-R312 | P3 | Duplicate `getStoneRateSettings()` call in JS init | ✅ Ready |

### Round 4: JS & View Layer Bugs (11)

| ID | Severity | Title | Fix Readiness |
|---|---|---|---|
| EST-R401 | **P0** | EDA validates Home Bill with `validateCatalogDetailRow()` instead of `validateCustomDetailRow()` | ✅ Ready (1-word fix) |
| EST-R402 | **P0** | EDA uses `$('#table').length >= 0` — always true, bypasses empty-table check | ✅ Ready |
| EST-R403 | **P1** | EDA single `form_validate` flag — last checked section overwrites prior failures | ✅ Ready |
| EST-R404 | P2 | Multiple consecutive alerts for missing address fields (UX issue) | ✅ Ready |
| EST-R405 | **P1** | `deleteEstimation()` uses GET for destructive action — CSRF vulnerable | ✅ Ready |
| EST-R406 | P2 | Cache-buster `getUTCSeconds()` only returns 0-59, not unique | ✅ Ready |
| EST-R407 | P2 | Duplicate `id="btn-submit"` on two spans in view | ✅ Ready |
| EST-R408 | P2 | Double `</form>` close (HTML + CI `form_close()`) | ✅ Ready |
| EST-R409 | **P1** | XSS: Unescaped flashdata rendered in alert div | ✅ Ready |
| EST-R410 | P3 | 30+ hidden inputs expose server settings — client-side-only permission validation | ⚠️ Needs server-side refactor |
| EST-R411 | P3 | Duplicate `name` attribute on hidden input | ✅ Ready |

### Round 5: JS Calculations & Data Binding Bugs (9)

| ID | Severity | Title | Fix Readiness |
|---|---|---|---|
| EST-R501 | **P0** | Market rate tax uses wrong variable (`base_value_tax` instead of `market_base_value_tax`) | ✅ Ready (4 variable fixes) |
| EST-R502 | **P0** | `get_tag_barcode_data` uses undefined `items.tag_id` — duplicate check always bypasses | ✅ Ready (1 variable fix) |
| EST-R503 | **P1** | `get_tag_barcode_data` uses undefined `rate_per_grm` — NaN in rate fields | ✅ Ready |
| EST-R504 | **P1** | Current `get_tag_data` removed metal type check — allows mixing gold/silver in estimation | ✅ Ready |
| EST-R505 | **P1** | Division by zero in diamond cent weight when `piece == 0` → Infinity | ✅ Ready |
| EST-R506 | P2 | `$('.cat_tax_per').val()` sets ALL catalog rows — class selector instead of row scope | ✅ Ready |
| EST-R507 | P2 | Collection confirm modal fires O(n²) times per non-matching row | ✅ Ready |
| EST-R508 | P2 | Employee dropdown adds duplicate `<option>` for pre-selected employee | ✅ Ready |
| EST-R509 | P3 | `var total_tax_rate` re-declaration inside `if` block — hoisting edge case | ✅ Ready |

### Round 6: Validation & AJAX/Controller Bugs (10)

| ID | Severity | Title | Fix Readiness |
|---|---|---|---|
| EST-R601 | **P0** | `cancel_order_tag` commits on failure instead of rollback — data corruption | ✅ Ready (1-word fix) |
| EST-R602 | P2 | `validateTagDetailRow` empty tag — no `return false`, continues loop unnecessarily | ✅ Ready |
| EST-R603 | **P1** | Old metal rate check uses `&& ''` (dead code) — rate=0 passes validation | ✅ Ready (1-char fix) |
| EST-R604 | **P1** | Validation functions mutate form data (copy mc/va values during validate) | ✅ Ready |
| EST-R605 | P2 | Mixed `$_POST` vs `$this->input->post()` — 12 endpoints bypass XSS filter | ⚠️ Systematic refactor |
| EST-R606 | P2 | 11 controller functions missing `public` access modifier | ✅ Ready |
| EST-R607 | P2 | `mkdir` with `0777` permissions for customer image directories | ✅ Ready |
| EST-R608 | P2 | `base64ToFile` no content validation — potential file upload vector | ⚠️ Needs review |
| EST-R609 | P3 | Controller does raw DB query instead of delegating to model | ✅ Ready |
| EST-R610 | P3 | 5 commented-out debug `print_r/exit` in production controller | ✅ Ready |

---

## Recommended Fix Priority

### Sprint 1 (Immediate — This Week)
1. **EST-R301** — SQL Injection: whitelist `$searchField`, add query bindings
2. **EST-R601** — Change `trans_commit()` → `trans_rollback()` at L3362
3. **EST-R501** — Fix 4 variable references in market rate tax (L9127-9133)
4. **EST-R502** — Change `items.tag_id` → `data[0].tag_id` in `get_tag_barcode_data`
5. **EST-R401 + R402 + R403** — EDA validation fix (copy pattern from `#est_print` handler)
6. **EST-001 + EST-S01 + EST-S07** — Gift voucher (variable fix + ALTER TABLE)
7. **EST-002** — Custom item `market_rate_tax` stores cost instead of tax
8. **EST-R302** — Cartesian JOIN fix (`bf.bill_type`)
9. **EST-R303** — Add GROUP BY to tax subquery (×3 methods)
10. **EST-R603** — Change `&&` → `||` in old metal rate validation at L8738

### Sprint 2 (Next Week)
11. **EST-R503** — Define `rate_per_grm` in `get_tag_barcode_data`
12. **EST-R504** — Re-add metal type check to current `get_tag_data`
13. **EST-R505** — Add `pcs > 0` guard before diamond cent weight calc
14. **EST-R604** — Extract form mutations from validation into separate pre-save function
15. **EST-R605** — Standardize all endpoints to `$this->input->post()`
16. **EST-R405** — Change `deleteEstimation` to POST with CSRF
17. **EST-R409** — XSS fix: wrap flashdata output in `htmlspecialchars()`
18. **EST-R607** — Change `mkdir 0777` → `0755`
19. **EST-S02** — Convert `ret_est_other_metals` to InnoDB
20. **EST-006 + EST-S06** — Update/delete orphan records
21. **EST-R304** — `$returndata` → `$return_data` typo fix
22. **EST-R308** — Add proper ON conditions to company JOINs
23. **EST-004 + EST-S04** — Implement `form_secret`

### Sprint 3 (Backlog)
24. **EST-R305** through **EST-R312** — Model-level fixes
25. **EST-R404** through **EST-R411** — JS/View cleanup
26. **EST-R506** through **EST-R509** — JS calculation display/UX fixes
27. **EST-R602, R606, R608–R610** — Validation and controller cleanup
28. **EST-007** through **EST-014**, **EST-S03**, **EST-S05** — Lower priority fixes
29. **EST-R410** — Server-side re-validation of permission limits

---

## Report File Structure

```
bug_report_AI/
├── CONSOLIDATED_BUG_REPORT.md              ← THIS FILE (master index)
├── ROUND_2_SCHEMA_ANALYSIS.md              ← DB schema cross-reference (Round 2)
├── ROUND_3_DEEP_DIVE.md                   ← Model & data-flow analysis (Round 3)
├── ROUND_4_JS_VIEW_DEEP_DIVE.md           ← JS save handler & view analysis (Round 4)
├── ROUND_5_JS_CALCULATIONS_DEEP_DIVE.md   ← JS calculations & data binding (Round 5)
├── ROUND_6_VALIDATION_AJAX_DEEP_DIVE.md   ← Validation functions & controller AJAX (Round 6)
├── add_estimation/
│   ├── BUG_REPORT_SUMMARY.md               ← Detailed summary with priority matrix
│   └── bugs_detailed.json                  ← Machine-readable with full implementation plans
├── estimation_list/
│   └── BUG_REPORT_SUMMARY.md
├── estimate_discount_approval/
│   └── BUG_REPORT_SUMMARY.md
└── KB_GAP_ANALYSIS.md                      ← Knowledge base coverage gaps
```

---

## KB Gap Analysis (Summary)

The knowledge base has **good coverage** for business rules and calculation types but is **missing**:

1. **Gift voucher save/update flow** — No documentation of the variable name convention or save path
2. **Delete cascade table list** — No complete list of child tables requiring cleanup
3. **Save vs Update field mapping** — No document comparing which fields are persisted in each path
4. **Custom item charge handling** — No documentation of id_division / charge relationship
5. **Post field name mapping** — No reference mapping JS POST field names to DB column names
6. **Child tag stone handling** — No documentation of merged tag stone indexing
7. **DB Schema reference** — No documentation of table engines, column types, or constraint inventory
8. **Model query patterns** — No documentation of N+1 query concerns or JOIN patterns
9. **EDA validation flow** — No documentation of how EDA differs from standard save
10. **Client-side vs server-side validation boundaries** — No documentation of which limits are enforced where

See `KB_GAP_ANALYSIS.md` for full details.
