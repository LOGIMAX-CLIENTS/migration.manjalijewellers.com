@ -1,115 +0,0 @@
# PURCHASE MODULE — MASTER BUG REPORT
> **Consolidation of Rounds 2-8** | **Date:** 2026-02-23 | **Total: 32 Bug Patterns**

---

## Priority Matrix

### Sprint 1 — Critical (Fix Immediately)

| ID | Bug | Severity | Location | Est. Effort | Round |
|---|---|---|---|---|---|
| PUR-001 | **3 ACTIVE debug statements in model** — `insertData()` L106 halts ALL inserts | 🔴 CRITICAL | Model L106, L2038, L4036 | 5 min | R4 |
| PUR-002 | **26 ACTIVE `echo last_query();exit;`** in controller, inside transaction blocks | 🔴 CRITICAL | Controller (26 lines) | 30 min | R4 |
| PUR-003 | **Typo `$clsoing_grm_wt`** → gram closing balance always shows 0 | 🔴 CRITICAL | Model L8045 | 2 min | R2 |
| PUR-004 | **Access control NOT checked on save/update/delete** — only on list views | 🔴 CRITICAL | Controller (15 sub-modules) | 2 hrs | R6 |

### Sprint 2 — High (Fix This Week)

| ID | Bug | Severity | Location | Est. Effort | Round |
|---|---|---|---|---|---|
| PUR-005 | 325 unescaped PHP outputs in views — **zero `htmlspecialchars()`** | 🔴 CRITICAL | 37 view files | 4 hrs | R8 |
| PUR-006 | XSS in email template (`vendor_ack_email.php`) | 🔴 CRITICAL | View | 30 min | R8 |
| PUR-007 | SQL injection in retagging — `$id_category`/`$id_metal` unsanitized | 🟠 HIGH | Model L5168+ | 1 hr | R2 |
| PUR-008 | 330 direct `$_POST` bypassing CI3 `$this->input->post()` | 🟠 HIGH | Controller | 3 hrs | R4 |
| PUR-009 | File upload: `base64ToFile()` — no extension whitelist, no size limit | 🟠 HIGH | Controller L202-228 | 1 hr | R6 |
| PUR-010 | 5× `mkdir 0777` — insecure file permissions | 🟡 MEDIUM | Controller | 15 min | R6 |

### Sprint 3 — Medium (Plan & Refactor)

| ID | Bug | Severity | Location | Est. Effort | Round |
|---|---|---|---|---|---|
| PUR-011 | 113 of 185 AJAX calls missing error handlers | 🟠 HIGH | JS | 4 hrs | R5 |
| PUR-012 | 12/12 save blocks with zero server-side validation | 🟠 HIGH | Controller | 6 hrs | R5 |
| PUR-013 | Pocket weight not subtracted in retagging closing balance | 🟡 MEDIUM | Model L8026 | 15 min | R2 |
| PUR-014 | 10 deprecated Select2 `.select2('val')` | 🟡 MEDIUM | JS | 30 min | R7 |
| PUR-015 | 25 N+1 query patterns (loop-with-inner-query) | 🟠 HIGH | Model (25 locations) | 8 hrs | R8 |
| PUR-016 | 168/181 queries without LIMIT | 🟠 HIGH | Model | 6 hrs | R8 |
| PUR-017 | 16 inline JS event handlers in views | 🟡 MEDIUM | Views | 1 hr | R8 |

### Sprint 4 — Structural (Refactoring Required)

| ID | Bug | Severity | Location | Est. Effort | Round |
|---|---|---|---|---|---|
| PUR-018 | 264 raw SQL `$this->db->query()` in model | 🟠 HIGH | Model | 20 hrs | R4 |
| PUR-019 | 20 `SELECT *` queries — should specify columns | 🟡 MEDIUM | Model | 3 hrs | R4 |
| PUR-020 | `purchase()` controller method = 1,714 lines | 🟠 HIGH | Controller | 8 hrs | R6 |
| PUR-021 | `get_retagging_details()` model method = 2,943 lines of raw SQL | 🟠 HIGH | Model L5109-8052 | 16 hrs | R2 |
| PUR-022 | Top 4 controller methods = 4,528 lines (33% of file) | 🟠 HIGH | Controller | 12 hrs | R6 |
| PUR-023 | 20 JS mega-functions totaling 19,081 lines | 🟠 HIGH | JS | 20 hrs | R7 |
| PUR-024 | 58 DataTable initializations — re-init risk | 🟡 MEDIUM | JS | 4 hrs | R7 |
| PUR-025 | 150 direct event bindings (should be delegated) | 🟡 MEDIUM | JS | 6 hrs | R7 |

### Backlog — Low Priority

| ID | Bug | Severity | Location | Est. Effort | Round |
|---|---|---|---|---|---|
| PUR-026 | 435 lines of commented dead code | 🟢 LOW | Ctrl + Model | 2 hrs | R6 |
| PUR-027 | 96+ commented debug `print_r;exit;` statements | 🟢 LOW | Model + Ctrl | 1 hr | R4 |
| PUR-028 | Manual `trans_begin/commit` pattern (non-standard but functional) | 🟢 LOW | Controller | 4 hrs | R4 |
| PUR-029 | `GetFinancialYear()` duplicate at L8148 (already at L133) | 🟢 LOW | Model | 5 min | R3 |
| PUR-030 | Email debugger output at Model L2038 | 🟢 LOW | Model | 5 min | R4 |
| PUR-031 | `moneyFormatIndia()` at L3978 — only 4 lines, could be helper | 🟢 LOW | Model | 10 min | R3 |
| PUR-032 | `get_headoffice_valut_report_old()` — dead code (replaced by new version) | 🟢 LOW | Model L3406 | 10 min | R3 |
| PUR-CLT01 | **Purchase dashboard Today PO Pending not showing** | 🟠 HIGH | Model (get_today_delivery_po_payments) | 15 min | CLT |

---

## Risk Heatmap by Sub-Module

| Sub-Module | Security | Performance | Validation | Maintainability | Overall |
|---|---|---|---|---|---|
| **Bill Entry** | 🔴 | 🟠 | 🔴 | 🔴 | 🔴 CRITICAL |
| **GRN Entry** | 🔴 | 🟠 | 🔴 | 🔴 | 🔴 CRITICAL |
| **Retagging** | 🔴 | 🔴 | 🟡 | 🔴 | 🔴 CRITICAL |
| **Purchase Return** | 🔴 | 🟠 | 🔴 | 🟠 | 🔴 CRITICAL |
| **PO Payment** | 🔴 | 🟡 | 🔴 | 🟠 | 🟠 HIGH |
| **Rate Fixing** | 🔴 | 🟡 | 🔴 | 🟡 | 🟠 HIGH |
| **Metal Issue** | 🔴 | 🟡 | 🔴 | 🟡 | 🟠 HIGH |
| **QC Issue/Receipt** | 🔴 | 🟡 | 🔴 | 🟡 | 🟠 HIGH |
| **HM Issue/Receipt** | 🟠 | 🟡 | 🔴 | 🟡 | 🟡 MEDIUM |
| **NonTag Lot/Receipt** | 🟠 | 🟡 | 🔴 | 🟡 | 🟡 MEDIUM |
| **Credit/Debit** | 🟠 | 🟢 | 🔴 | 🟡 | 🟡 MEDIUM |
| **Supplier Rate Cut** | 🟠 | 🟢 | 🔴 | 🟡 | 🟡 MEDIUM |
| **Approval Stock** | 🟠 | 🟢 | 🟠 | 🟡 | 🟡 MEDIUM |
| **Smith Op Bal** | 🟠 | 🟢 | 🔴 | 🟢 | 🟡 MEDIUM |
| **Order Description** | 🟠 | 🟢 | 🔴 | 🟢 | 🟡 MEDIUM |

---

## Effort Estimate Summary

| Sprint | Bugs | Estimated Effort |
|---|---|---|
| Sprint 1 — Critical | PUR-001 to PUR-004 | **2.5 hrs** |
| Sprint 2 — High | PUR-005 to PUR-010 | **9.75 hrs** |
| Sprint 3 — Medium | PUR-011 to PUR-017 | **25.75 hrs** |
| Sprint 4 — Structural | PUR-018 to PUR-025 | **89 hrs** |
| Backlog | PUR-026 to PUR-032 | **7.5 hrs** |
| **Total** | **32 bugs** | **~134.5 hrs** |

---

## Source Documents
| # | Document | Size |
|---|---|---|
| 1-8 | Core brain (R1) | 108 KB |
| 9 | `DEEP_TRACE_RETAGGING.md` (R2) | 7 KB |
| 10-11 | `AJAX_ENDPOINT_MAP.md`, `REMAINING_METHODS.md` (R3) | 15 KB |
| 12 | `BUG_DISCOVERY.md` (R4) | 7 KB |
| 13 | `SAVE_FLOW_ANALYSIS.md` (R5) | 6 KB |
| 14 | `CONTROLLER_ANALYSIS.md` (R6) | 6 KB |
| 15 | `JS_DEEP_DIVE.md` (R7) | 5 KB |
| 16 | `VIEW_XSS_AND_QUERY_AUDIT.md` (R8) | 5 KB |
| 17 | `COVERAGE_TRACKER.md` | 4 KB |
| **18** | **`MASTER_BUG_REPORT.md`** (R9 — this file) | — |
| **Total** | **18 brain documents** | **163 KB** |