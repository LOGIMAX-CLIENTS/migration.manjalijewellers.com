# Branch Transfer — Consolidated Bug Report (Full Re-Audit)

> **Date**: 2026-03-11
> **Audit Type**: Full Re-Audit (previous audit backed up to `_backup_2026-03-11/`)
> **Brain Version**: Round 6 (100% coverage, 51 JS functions, 7 invariant dimensions)

---

## Executive Summary
**Total Bugs Found**: 76 (74 unique)
**Severity Breakdown**: 8 P0 | 22 P1 | 43 P2 | 3 P3
**Track Distribution**: 61 Track A (System) | 15 Track B (Business)

### Patterns Matched from Common Bug Patterns Library
| Pattern | Matches | Bug IDs |
|---|---|---|
| PAT-SEC-002 (Raw $_POST) | ✅ 104 instances | BRN-104, BRN-105 |
| PAT-RAW-001 (Raw SQL concat) | ✅ 5 methods | BRN-S01 |
| PAT-TXN-001 (trans_commit no status) | ✅ 1 method | BRN-102 |
| PAT-TXN-003 (Dangling trans) | ✅ 1 method | BRN-101 |
| PAT-CON-001 (TOCTOU sequence) | ✅ 1 method | BRN-S02 |
| PAT-SEC-004 (XSS unescaped) | ✅ views | BRN-R402 |
| PAT-LOGIC-004 (empty() on numeric) | ✅ multiple | BRN-R301 |
| PAT-VAR-003 (Return variable inconsistency) | ✅ 18 | BRN-R302 |

### New Patterns Discovered
- **Duplicate DOM ID** — Two elements with same `id` in same form (BRN-R401) → PAT-DOM-001
- **Blind Reload** — AJAX success handler ignores response and reloads page (BRN-R602)
- **Ternary Precedence** — PHP `.` concatenation binds tighter than `==` (BRN-106)
- **Null Object Reference** — `->row()->property` accessed without `num_rows()` guard — fatal crash on unconfigured lookup (BRN-D10, D16, D17, D09) → PAT-NULL-001

---

## Master Bug Index

| Bug ID | Sev | Title | Category | Track | Round | Fix Readiness |
|---|---|---|---|---|---|---|
| **BRN-101** | P0 | Dangling Transaction in `verify_otp()` | Transaction | A | R1 | ✅ Fixed |
| **BRN-102** | P0 | Missing trans_status + rollback in `verify_other_issue_otp()` | Transaction | A | R1 | ✅ Fixed |
| **BRN-103** | P1 | Live Error/Query Leak in Save Failure | Security | A | R1 | ✅ Fixed |
| **BRN-104** | P1 | Raw `$_POST` Used Throughout (104 instances) | Security | A | R1 | ✅ Fixed |
| **BRN-105** | P2 | Raw `$_POST` Passed Directly to Model | Security | A | R1 | Ready |
| **BRN-106** | P2 | Ternary Precedence Bug in Log Remark | Logic | A | R1 | Ready |
| **BRN-107** | P3 | Commented Debug Statements (22 instances) | Cleanup | A | R1 | Ready |
| **BRN-S01** | P1 | Raw SQL with String Concatenation (5 methods) | Security | A | R2 | ✅ Fixed (via BRN-D03) |
| **BRN-S02** | P2 | TOCTOU in Trans Code Generator | Concurrency | A | R2 | Ready |
| **BRN-S03** | P3 | DomPDF Paper Orientation Typo | Print | A | R2 | Ready |
| **BRN-R301** | P2 | `empty()` Used on Numeric Fields | Data Logic | B | R3 | Ready |
| **BRN-R302** | P2 | Inconsistent Return Variable Naming | Code Qual | A | R3 | Ready |
| **BRN-R303** | P2 | Cancel Does Not Reverse Stock Changes | Data Integrity | B | R3 | Needs Design |
| **BRN-R401** | P1 | Duplicate DOM ID `id_product` | View/DOM | A | R4 | ✅ Fixed |
| **BRN-R402** | P2 | Unescaped PHP Output in Views (XSS) | Security | A | R4 | Ready |
| **BRN-R403** | P2 | `async:false` in AJAX Calls (UI Freeze) | UX/Perf | A | R4 | Ready |
| **BRN-R501** | P2 | Missing parseFloat/NaN Guards | Calc Logic | B | R5 | Ready |
| **BRN-R502** | P2 | No `error:` Handler on Several AJAX Calls | Robustness | A | R5 | Ready |
| **BRN-R601** | P2 | Checkbox State Loss After DataTable Re-init | UX/Logic | B | R6 | Ready |
| **BRN-R602** | P3 | Blind Page Reload on AJAX Success | Robustness | A | R6 | Ready |
| **BRN-D01** | P0 | `getProductsByFilter()` returns undefined `$data` — crash | Variable | A | R7 | ✅ Fixed |
| **BRN-D02** | P1 | `updateDatamulti()` returns undefined `$id_value` | Variable | A | R7 | ✅ Fixed |
| **BRN-D03** | P0 | SQL Injection: 12+ methods, 50+ concat points (supersedes BRN-S01) | Security | A | R7 | ✅ Fixed |
| **BRN-D04** | P2 | `SHOW COLUMNS` on every insert/update (perf hit) | Performance | A | R7 | Ready |
| **BRN-D05** | P3 | Dead function `zgetDesignByFilter()` | Cleanup | A | R7 | Ready |
| **BRN-D06** | P2 | Trans code generated before form_secret check | Logic | A | R7 | Ready |
| **BRN-D07** | P2 | Loop overwrites total with same POST values | Logic | B | R7 | Ready |
| **BRN-D08** | P2 | `get_last_trans_code()` no null guard — crash on first EDA use | Variable | A | R7 | Ready |
| **BRN-D09** | P0 | Undefined `$FromDt` in `get_ajaxBranchTransferlist()` — listing always empty | Variable | A | R8 | ✅ Fixed |
| **BRN-D10** | P1 | `get_verifMobNo()` no null guard — OTP flow crash | Variable | A | R8 | ✅ Fixed |
| **BRN-D11** | P1 | `net_wt` shows `gross_wt` in SR summary (copy-paste swap) | Business | B | R8 | ✅ Fixed |
| **BRN-D12** | P2 | `SELECT *` in `get_profile_settings()` | Cleanup | A | R8 | Ready |
| **BRN-D13** | P2 | `getSettigsByName()` function name misspelling | Cleanup | A | R8 | Ready |
| **BRN-D14** | P2 | 300+ lines dead commented code (old `get_purchase_items`) | Cleanup | A | R8 | Ready |
| **BRN-D15** | P2 | `updateNTData()` raw SQL concat for stock arithmetic | Security | A | R8 | Ready |
| **BRN-D16** | P0 | `getNontagItemId()` null-crash on `->row()->id_nontag_item` | Variable | A | R9 | ✅ Fixed |
| **BRN-D17** | P0 | `get_headoffice_branch()` null-crash on `->row()->id_branch` | Variable | A | R9 | ✅ Fixed |
| **BRN-D18** | P2 | `SELECT *` in 3 PS/SR detail functions | Cleanup | A | R9 | Ready |
| **BRN-D19** | P2 | Undefined index `$r['product']`/`$r['design']` in receipt items | Variable | A | R9 | Ready |
| **BRN-D20** | P2 | `get_InventoryCategory()` overwrites input param with query result | Cleanup | A | R9 | Ready |
| **BRN-D21** | P1 | Wrong variable `$partly_sale_log` for non-tag SR insert (data corruption) | Variable | A | R10 | ✅ Fixed |
| **BRN-D22** | P1 | Approval error leaks `last_query()` + `_error_message()` in AJAX | Security | A | R10 | ✅ Fixed (by BRN-103) |
| **BRN-D23** | P1 | `verify_otp()` orphan `trans_begin()` (confirms BRN-101) | Transaction | A | R10 | Ready |
| **BRN-D24** | P1 | `send_other_issue_otp()` nested `trans_begin()` in loop | Transaction | A | R10 | Ready |
| **BRN-D25** | P2 | `portriat` typo in PDF paper setting | Cleanup | A | R10 | Ready |
| **BRN-D26** | P2 | `Trasnfer` typo x3 in cancel log messages | Cleanup | A | R10 | Ready |
| **BRN-D27** | P2 | XSS: Flashdata echoed without `htmlspecialchars()` in 2 views | Security | A | R11 | Ready |
| **BRN-D28** | P2 | Duplicate `id="id_product"` — jQuery selector collision | HTML | A | R11 | Ready |
| **BRN-D29** | P2 | Repair Orders radio bypasses profile permission check | Access | B | R11 | Ready |
| **BRN-D30** | P2 | "Trasnfer" typo in list.php breadcrumb + heading | Cleanup | A | R11 | Ready |
| **BRN-D31** | P2 | "Cancell" typo + wrong module text in cancel modal | Cleanup | A | R11 | Ready |
| **BRN-D32** | P1 | Triplicate `id="appr_sel_all_nt"` — checkbox "select all" broken | HTML | A | R12 | Ready |
| **BRN-D33** | P1 | `$tot_amount`/`$tot_dia_wt` uninitialized in print — wrong totals | Variable | A | R12 | Ready |
| **BRN-D34** | P2 | `function group_by()` inline in view — fatal if loaded twice | Architecture | A | R12 | Ready |
| **BRN-D35** | P2 | Type5 no permission check in approval_list (confirms BRN-D29) | Access | B | R12 | Ready |
| **BRN-D36** | P2 | Broken HTML `title` attribute in approval button | Cleanup | A | R12 | Ready |
| **BRN-D37** | **P0** | Hardcoded `transfer_to:1` in old metal `add_to_trans()` — always routes to branch 1 | Data | A | R13 | ✅ Fixed |
| **BRN-D38** | P1 | Duplicate tag check uses `data[0].tag_id` not `val.tag_id` — only first tag checked | Logic | A | R13 | Ready |
| **BRN-D39** | P1 | `$(document).on('keypress')` bound inside radio change — event accumulation | Event | A | R13 | Ready |
| **BRN-D40** | P1 | Select-all checkbox affects ALL `tbody` elements, not scoped to its table | Selector | A | R13 | Ready |
| **BRN-D41** | P1 | `console.log(trans_data)` in `add_to_trans()` — exposes payload in production | Security | A | R13 | Ready |
| **BRN-D42** | P2 | 3 more `console.log()` debug statements in production JS | Cleanup | A | R13 | Ready |
| **BRN-D43** | P2 | Stray backtick syntax error in `btran_filter` click handler | Syntax | A | R13 | Ready |
| **BRN-D44** | P2 | `grs_wt`, `net_wt`, `pieces` undeclared in `add_to_trans()` — global leak | Scope | A | R13 | Ready |
| **BRN-D45** | P2 | `trans_type`, `approval_type`, `from_brn`, `to_brn` undeclared globals throughout | Scope | A | R13 | Ready |
| **BRN-D46** | P2 | `dia_wt` undeclared in `.tag_id` change handler | Scope | A | R13 | Ready |
| **BRN-D47** | P1 | Duplicate `id="ref_no"` generated in loop — breaks logic | Logic | A | R14 | Ready |
| **BRN-D48** | P1 | Silver preview uses gold variables (Copy-paste error) | Logic | B | R14 | Ready |
| **BRN-D49** | P1 | Un-scoped checkbox selectors in order/approval handlers | Selector | A | R14 | Ready |
| **BRN-D50** | P1 | `isOtherIssue` used before declaration in `send_otp()` | Logic | A | R14 | Ready |
| **BRN-D51** | P2 | Hardcoded Socket URL in production | Architecture | A | R14 | Ready |
| **BRN-D52** | P2 | Global leak `trans_type` in `send_mobile_approval_request` | Scope | A | R14 | Ready |
| **BRN-D53** | P2 | Missing local `my_Date` in `update_aprvl_status` | Scope | A | R14 | Ready |
| **BRN-D54** | P2 | Multiple global leaks in DataTable column renderers | Scope | A | R14 | Ready |
| **BRN-D55** | P2 | Typo with trailing space in class name `id_nontag_receipt ` | Cleanup | A | R14 | Ready |
| **BRN-D56** | P3 | UI Typos in error messages | Cleanup | A | R14 | Ready |

---

## Sprint Assignment

### Sprint 1 — Critical Path (P0 + Critical P1)
| Bug | Fix Complexity | Dependencies |
|---|---|---|
| BRN-101 | Low — remove trans_begin or add commit/rollback | None |
| BRN-102 | Low — add trans_status check + rollback on failure paths | None |
| BRN-103 | Low — comment out/remove L272-273 | None |
| BRN-S01 | Medium — rewrite 5 methods to use active record | Test old metal/SR/PS flows |

### Sprint 2 — High Risk (P1 + High P2)
| Bug | Fix Complexity | Dependencies |
|---|---|---|
| BRN-R401 | Low — rename second `id_product` to `id_product_nt` | Check JS references |
| BRN-104 | High — 104 instances to change | All AJAX flows |
| BRN-R303 | High — needs stock reversal logic design | Business rule approval |
| BRN-S02 | Medium — add FOR UPDATE lock | Test concurrent saves |

### Sprint 3 — Debt/Minor (Remaining)
All remaining P2 and P3 bugs: BRN-105, BRN-106, BRN-107, BRN-S03, BRN-R301, BRN-R302, BRN-R402, BRN-R403, BRN-R501, BRN-R502, BRN-R601, BRN-R602

---

## Comparison with Previous Audit
Previous audit found 11 bugs. This re-audit found **20 bugs** — 9 more, plus stronger analysis:
- Previous missed: BRN-101 (P0 dangling trans), BRN-106 (ternary precedence), BRN-R303 (cancel no-reversal), BRN-R401 (duplicate DOM ID), BRN-R403 (async:false), BRN-R403, BRN-R601, BRN-R602, BRN-S03

### Sprint 3 — Debt/Minor (Remaining)
All remaining P2 and P3 bugs: BRN-105, BRN-106, BRN-107, BRN-S03, BRN-R301, BRN-R302, BRN-R402, BRN-R403, BRN-R501, BRN-R502, BRN-R601, BRN-R602, BRN-D04, BRN-D05, BRN-D06, BRN-D07

## Comparison with Previous Audits
| Metric | 1st Audit (old) | R0-R6 Re-Audit | R7 Deep Analysis | Δ |
|---|---|---|---|---|
| Total bugs | 11 | 20 | **28** | +17 vs original |
| P0 bugs | 2 | 2 | **4** | +2 crash bugs found |
| P1 bugs | 3 | 4 | **5** | +1 undefined var |
| SQL injection methods | ~5 | 5 | **12+ (50+ concat points)** | 10x larger surface |

## GitHub Issues Map

> **Triage Completed**: 2026-03-11 | All 76 bugs tracked across 33 GitHub issues.

### P0 — Critical (8 individual issues, Sprint 1)
| Bug ID | GitHub Issue | Title |
|---|---|---|
| BRN-101 | [#1165](https://github.com/Logimax-Technologies/etail_development_src/issues/1165) | Dangling Transaction in verify_otp() |
| BRN-102 | [#1166](https://github.com/Logimax-Technologies/etail_development_src/issues/1166) | commit on failure in verify_other_issue_otp() |
| BRN-D01 | [#1167](https://github.com/Logimax-Technologies/etail_development_src/issues/1167) | getProductsByFilter() undefined $data crash |
| BRN-D03 | [#1168](https://github.com/Logimax-Technologies/etail_development_src/issues/1168) | SQL Injection: 12+ methods, 50+ concat points |
| BRN-D09 | [#1169](https://github.com/Logimax-Technologies/etail_development_src/issues/1169) | Undefined $FromDt — listing always empty |
| BRN-D16 | [#1170](https://github.com/Logimax-Technologies/etail_development_src/issues/1170) | getNontagItemId() null-crash |
| BRN-D17 | [#1171](https://github.com/Logimax-Technologies/etail_development_src/issues/1171) | get_headoffice_branch() null-crash |
| BRN-D37 | [#1172](https://github.com/Logimax-Technologies/etail_development_src/issues/1172) | Hardcoded transfer_to:1 — all old metal misrouted |

### P1 — Major (22 individual issues, Sprint 1–2)
| Bug ID | GitHub Issue | Sprint |
|---|---|---|
| BRN-103 | [#1173](https://github.com/Logimax-Technologies/etail_development_src/issues/1173) | 1 |
| BRN-104 | [#1174](https://github.com/Logimax-Technologies/etail_development_src/issues/1174) | 1 |
| BRN-S01 | [#1175](https://github.com/Logimax-Technologies/etail_development_src/issues/1175) | 1 |
| BRN-R401 | [#1176](https://github.com/Logimax-Technologies/etail_development_src/issues/1176) | 2 |
| BRN-D02 | [#1177](https://github.com/Logimax-Technologies/etail_development_src/issues/1177) | 2 |
| BRN-D10 | [#1178](https://github.com/Logimax-Technologies/etail_development_src/issues/1178) | 2 |
| BRN-D11 | [#1179](https://github.com/Logimax-Technologies/etail_development_src/issues/1179) | 2 |
| BRN-D21 | [#1180](https://github.com/Logimax-Technologies/etail_development_src/issues/1180) | 1 |
| BRN-D22 | [#1181](https://github.com/Logimax-Technologies/etail_development_src/issues/1181) | 1 |
| BRN-D23 | [#1182](https://github.com/Logimax-Technologies/etail_development_src/issues/1182) | 1 |
| BRN-D24 | [#1183](https://github.com/Logimax-Technologies/etail_development_src/issues/1183) | 1 |
| BRN-D32 | [#1184](https://github.com/Logimax-Technologies/etail_development_src/issues/1184) | 2 |
| BRN-D33 | [#1185](https://github.com/Logimax-Technologies/etail_development_src/issues/1185) | 2 |
| BRN-D38 | [#1186](https://github.com/Logimax-Technologies/etail_development_src/issues/1186) | 2 |
| BRN-D39 | [#1187](https://github.com/Logimax-Technologies/etail_development_src/issues/1187) | 2 |
| BRN-D40 | [#1188](https://github.com/Logimax-Technologies/etail_development_src/issues/1188) | 2 |
| BRN-D41 | [#1189](https://github.com/Logimax-Technologies/etail_development_src/issues/1189) | 1 |
| BRN-D47 | [#1190](https://github.com/Logimax-Technologies/etail_development_src/issues/1190) | 2 |
| BRN-D48 | [#1191](https://github.com/Logimax-Technologies/etail_development_src/issues/1191) | 2 |
| BRN-D49 | [#1192](https://github.com/Logimax-Technologies/etail_development_src/issues/1192) | 2 |
| BRN-D50 | [#1193](https://github.com/Logimax-Technologies/etail_development_src/issues/1193) | 2 |

### P2/P3 — Omnibus (4 grouped issues, Sprint 2–3)
| Group | GitHub Issue | Covers |
|---|---|---|
| P2 Security (5 bugs) | [#1194](https://github.com/Logimax-Technologies/etail_development_src/issues/1194) | BRN-105, R402, D15, D22, D27 |
| P2 Logic/Business (10 bugs) | [#1195](https://github.com/Logimax-Technologies/etail_development_src/issues/1195) | BRN-106, R301, R501, R601, D06, D07, D29, D35, D43, D48 |
| P2 Scope/Perf (16 bugs) | [#1196](https://github.com/Logimax-Technologies/etail_development_src/issues/1196) | BRN-S02, R403, R502, R602, D04, D08, D19, D20, D34, D44-D46, D51-D54 |
| P2/P3 Cleanup (20 bugs) | [#1197](https://github.com/Logimax-Technologies/etail_development_src/issues/1197) | BRN-S03, R302, R303, D12-D14, D18, D25-D26, D28, D30-D31, D36, D42, D55-D56, D107 |

## Next Steps
- **Sprint 1**: Start with `/fix-single-bug BRN-D01` (fast P0 null-crash fix), then `BRN-D37` (data misrouting), then `BRN-101`/`BRN-102` (transaction integrity).
- **Sprint 2**: `BRN-D03` (SQL injection sweep), then P1 bugs by block (transactions → security → JS).
- **Track B review**: BRN-D11, BRN-D48, BRN-R301, BRN-R601, BRN-D07, BRN-D29, BRN-D35 — need business rule sign-off before coding.
- **BRN-R303** (Cancel stock reversal): Needs design proposal first — do not touch without explicit approval.

