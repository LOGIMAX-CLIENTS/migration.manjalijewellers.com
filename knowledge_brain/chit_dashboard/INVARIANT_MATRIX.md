# INVARIANT MATRIX — chit_dashboard
> Round R2-Upgrade — 2026-03-25 (no code changes — structural upgrade only)

---

## Dimension 1: Session / Authentication Invariants

| ID | Invariant | Source | Enforcement | Test Command |
|---|---|---|---|---|
| INV-DAS-S01 | `is_logged` session must be truthy before any controller method executes | Controller constructor L71 | PHP redirect to `admin/login` | `GET /admin/admin_dashboard` without session → must redirect |
| INV-DAS-S02 | `uid` session must be set before any branch filter calculation | Model L86, L177, L267 etc. | Used in raw SQL concat — no null guard | Verify session has `uid` after login |
| INV-DAS-S03 | `dashboard_branch = 0` means "no branch filter" (see all); dashboard_branch = X means "filter to branch X" | Controller L110-114, Model L94 | Convention only — no enum validation | Test with `id_branch=0` in dashboard POST |
| INV-DAS-S04 | `uid = 1` is the super-admin magic value — bypasses all branch restrictions | Model L94, L183, L274, etc. | Raw `$uid!=1` check | Do NOT change any user's id to 1 except super-admin |
| INV-DAS-S05 | `branchWiseLogin = 1` + `id_branch set` → branch-scoped model queries | Model (all stat methods) | Session-driven SQL concat | If `branchWiseLogin=1` and `id_branch` is null/empty, no branch filter applies |

---

## Dimension 2: Financial Accuracy Invariants

| ID | Invariant | Source | Enforcement | Risk |
|---|---|---|---|---|
| INV-DAS-F01 | `payment_status = 1` = confirmed payment — the ONLY status counted in financial totals | Model `total_payment_details()` L259, `payment_join()` L275 | SQL WHERE clause | ⚠️ Any change to this status code breaks all collection totals |
| INV-DAS-F02 | `payment_status = 4` = cancelled — excluded from all totals | `ajax_daily_collection()` L2430 | SQL WHERE exclusion | Cancellation reversal NOT shown in dashboard |
| INV-DAS-F03 | Paid/Unpaid % formula is BROKEN — mixes SUM(amount) with COUNT(accounts) | Controller `payment_stat()` L594-614 | ❌ No enforcement — active bug | See RULE-DAS-002 |
| INV-DAS-F04 | Closing balance formula: `yesterday_closing + today_collection - today_closed - today_cancelled` | Controller L2460-2464 | Arithmetic only — no DB constraint | Negative closing balance is possible if: closed > collected |
| INV-DAS-F05 | `SUM(payment_amount)` only counts `payment_status=1` (confirmed) — NOT 2 (awaiting), NOT 4 (cancelled) | All collection queries | SQL WHERE | If someone manually sets payment_status=1 without proper processing, it inflates totals |

---

## Dimension 3: Data Visibility Invariants

| ID | Invariant | Source | Enforcement | Risk |
|---|---|---|---|---|
| INV-DAS-V01 | Drilldown routes (`reg_detail`, `acc_detail`, etc.) inherit same branch filter from session | Controller (all `{type}` drilldown methods, L910, L980, L1232 etc.) | Session `dashboard_branch` in model | Mismatch: user changes branch on dashboard, banner cards update, but old drilldown tab still shows old branch |
| INV-DAS-V02 | Drilldown page for customers includes ALL-time records by default (type='ALL') unless type given | `reg_detail_stat('ALL')` | filterBy switch case 'ALL' | 'ALL' returns unbounded result — potential memory/timeout on large datasets |
| INV-DAS-V03 | `scheme_acc_number != 'null'` (string comparison) — must remain for due_stat to work correctly | `dashboard_model::due_stat()` ~L462 | String comparison | Any row with SQL NULL will be excluded (correct); rows with literal 'null' string will also be excluded |
| INV-DAS-V04 | `show_to_all = 1` branch flag causes branch to appear in all non-admin user views | Model all branch-scoped methods | SQL `OR b.show_to_all=1` | If a branch is incorrectly flagged `show_to_all=1`, it leaks to all branch-restricted users |
| INV-DAS-V05 | `customer_join('WEB')` uses `added_by=0`, while `payment_join('WEB')` uses `added_by=1` — different codes | Model L278, L302 | ❌ No enforcement — hardcoded inconsistency | Customer WEB = 0, Payment WEB = 1. The "source breakdown" charts are consistent within their domain but not cross-domain |

---

## Dimension 4: Write Safety Invariants

| ID | Invariant | Source | Enforcement | Risk |
|---|---|---|---|---|
| INV-DAS-W01 | `updateData($data, $id_field, $id_value, $table)` accepts arbitrary table, id_field, and id_value — no whitelist | `dashboard_model.php` L2074-2143 | ❌ No enforcement | If called with untrusted input, can update any table's any row |
| INV-DAS-W02 | `customer_edit($mobile)` is a GET route — mobile number is URL parameter, no CSRF token | Controller L2578 | ❌ No CSRF | Any logged-in user can forge a customer edit by crafting a URL |
| INV-DAS-W03 | `upload()` writes to `master/upload_apk/` path — no file type restriction confirmed | Controller L3304-3343 | ⚠️ Unknown — needs verification | If no extension check, arbitrary file upload is possible |
| INV-DAS-W04 | `dayClose()` triggers write to `daily_collection` via services_model — must be called only once per day per branch | Controller L2925-2969 | ⚠️ No DB unique constraint confirmed | Double day-close for same branch+date produces duplicate records |
| INV-DAS-W05 | `send_customer_wishes()` sends SMS for all customers in date range — no dedup guard | Controller L3053-3149 | ❌ No check if SMS already sent | Re-submitting the form sends duplicate wishes |

---

## Dimension 5: Query Safety Invariants

| ID | Invariant | Source | Enforcement | Risk |
|---|---|---|---|---|
| INV-DAS-Q01 | `$dashboard_branch`, `$id_branch`, `$uid` are injected raw into SQL strings | Model L94, L183, L274, L390, L652 (50+ occurrences) | ❌ No parameterization | SQL injection if session is hijacked or manipulated |
| INV-DAS-Q02 | `DATE(date_payment)` and `DATE(date_add)` wrappers defeat MySQL indexes on those columns | Model throughout | ❌ No enforcement | Full table scans on payment (which can be millions of rows) |
| INV-DAS-Q03 | Case `'LW'` in all filterBy switches contains garbled `â€"` character (corrupted `–`) — causes SQL syntax error | Model L25, L55, L147, L189, L341, L396 etc. | ❌ No enforcement — active bug | Any "Last Week" filter request throws PHP/MySQL error |
| INV-DAS-Q04 | `cust_wo_accounts_details()` runs 2 DB queries per customer in a PHP loop (N+1) | Controller L1094-1138 | ❌ No enforcement | With 1,000 customers without accounts → 2,001 queries on one request |
| INV-DAS-Q05 | `enquiry_report()` and `enquiry_detail_report()` are never called by the controller | Model L13-69 | ❌ No enforcement | Dead code accumulating SQL syntax bugs including garbled chars |

---

## Dimension 6: Configuration-Driven Behavior Invariants

| ID | Invariant | Source | Enforcement | Risk |
|---|---|---|---|---|
| INV-DAS-C01 | `chit_settings.has_lucky_draw` column must exist — used in `acc_wo_pay_details()` and `total_payment_details()` | Model L225, L248 | ❌ No guard — query fails if column missing | If `chit_settings` table gains/loses this column, multiple dashboard queries break |
| INV-DAS-C02 | `chit_settings.currency_symbol` must exist — used in inter-wallet stats | Model `inter_wallet_credit()` L1787 | ❌ No guard | If column missing, all inter-wallet queries fail |
| INV-DAS-C03 | `branch.show_to_all` column must exist — critical for branch visibility logic | Model L94, L183, L274, etc. | ❌ No guard | If column removed, SQL errors on all branch-filtered queries |
| INV-DAS-C04 | `admin_settings_model` must be auto-loaded via CI autoload.php | Controller `index()` L132 | Checked only at runtime | If removed from autoload, `index()` throws fatal error |
| INV-DAS-C05 | `renewal_stat()` default `$limit = 25` — last 25 installments defines "renewal" threshold | Model L1182 | Hardcoded default | Changing renewal window requires code change, not config |

---

## Dimension 7: Dead Code / Deprecation Invariants

| ID | Invariant | Source | Notes |
|---|---|---|---|
| INV-DAS-D01 | `getsource_wiserrecord_old()` (Controller L3376) is dead — `getsource_wiserrecord()` is the active version | Controller L3376-3463 | Do NOT activate old version |
| INV-DAS-D02 | `ajax_daily_collection` URL is commented out in JS (L972) — controller method still exists | JS L972, Controller L2386 | Safe to leave but clean up eventual |
| INV-DAS-D03 | Large commented-out block in `index()` (L162-196) was the old inline-load pattern — now replaced by AJAX | Controller L162-196 | Comment retained as documentation |
| INV-DAS-D04 | `enquiry_report()` and `enquiry_detail_report()` in model (L13-69) not called by controller | Model L13-69 | Unreachable — safe to remove |
| INV-DAS-D05 | `old_account_status()`, `old_account_join_list()`, `old_renewal_stat()`, `old_total_abt_to_cls()` in model are old versions | Model ~L1075, L1158, L1552, L1658 | New versions without `old_` prefix are active |
| INV-DAS-D06 | `interCreditAndDebit()` model method (L1810) is commented out in `inter_wallet_status()` controller | Model L1810, Controller ~L2232 | Dead code |

---

## Quick Risk Summary Table

| Risk | Invariant | Severity |
|---|---|---|
| All "Last Week" filters fail | INV-DAS-Q03 | 🔴 CRITICAL |
| SQL injection on all branch-filtered queries | INV-DAS-Q01 | 🔴 CRITICAL |
| Paid/Unpaid % always shows ~100% | INV-DAS-F03 | 🔴 HIGH |
| customer_edit CSRF via GET URL | INV-DAS-W02 | 🔴 HIGH |
| N+1 loop in cust_wo_accounts_details | INV-DAS-Q04 | 🔴 PERF |
| dashboard_branch session race on multi-tab | INV-DAS-S03 | 🟡 MED |
| updateData writes to arbitrary table | INV-DAS-W01 | 🔴 HIGH |
| APK upload no type validation | INV-DAS-W03 | 🔴 HIGH |
| Full table scan on DATE(date_payment) | INV-DAS-Q02 | 🟡 MED PERF |
| Customer source codes inconsistent with payment source codes | INV-DAS-V05 | 🟡 MED |
