# BUG SPRINT PLAN — Retail Dashboard
> Generated: 2026-03-16 | Module: Retail Dashboard | Builder: Antigravity  
> Based on 10-round brain build. **26 live bugs, 1 closed (#20). Updated Round 11.**

---

## Sprint 1: Production-Breaking Fixes (Must Fix NOW)

> These bugs cause fatal errors or return provably wrong data in production. Fix before any other work.

### Bug #5 — `getLedgerReportData()` Missing Method (FATAL)

| | |
|---|---|
| **File** | `admin_ret_dashboard.php` L2112 |
| **Symptom** | Ledger Balance Alert widget → **PHP Fatal Error** every load |
| **Root Cause** | `get_LedgerBalanceAlert()` calls `$this->ret_reports_model->getLedgerReportData($post_data)` — this method **does not exist** in `ret_reports_model.php` |
| **Fix** | Either: (a) Add stub `getLedgerReportData()` to `ret_reports_model.php` that queries `ledger_master` and returns running balance rows, OR (b) Port the logic directly into `ret_dashboard_model.php` |
| **Risk** | LOW — isolated method, no side effects |
| **Effort** | 2-4 hours |

### Bug #23 — `get_rate_cut_profit_loss()` Inverted Date Filter

| | |
|---|---|
| **File** | `ret_dashboard_api_model.php` L2632 |
| **Symptom** | Rate Cut P&L widget always shows data ≤ from_date; to_date completely ignored |
| **Fix** | Change `DATE(src.date_add) <= '{$from_date}'` → `DATE(src.date_add) BETWEEN '{$from_date}' AND '{$to_date}'` |
| **Effort** | 15 minutes |

### Bug #22 — `get_accountstock_inwards_details()` Undefined Variables

| | |
|---|---|
| **File** | `ret_dashboard_api_model.php` L2223 |
| **Symptom** | Account Stock Inwards widget shows **completely unfiltered** data — PHP notices + wrong results |
| **Root Cause** | Uses `$id_category` (never defined in function scope) and `$data['bt_code']`/`$data['id_branch']` (undefined `$data` array) in all 7 sub-queries |
| **Fix** | Add `$id_category` to function signature; replace `$data['id_branch']` with the existing `$id_branch` param |
| **Effort** | 1-2 hours |

### Bug #21 — `get_branch_wastage()` Missing `$id_metal` Parameter

| | |
|---|---|
| **File** | `ret_dashboard_api_model.php` L666 |
| **Symptom** | Metal filter on wastage widget silently ignored — returns data for ALL metals |
| **Fix** | Add `$id_metal` to function signature: `get_branch_wastage($from_date, $to_date, $id_branch, $group_by, $id_metal)` |
| **Effort** | 15 minutes |

### Bug #25 — Wildcard CORS on All API Endpoints

| | |
|---|---|
| **File** | `admin_ret_dashboard_api.php` L3 |
| **Symptom** | Any external website/script can call all API endpoints cross-origin |
| **Root Cause** | `Access-Control-Allow-Origin: *` set globally at file top |
| **Fix** | Restrict to specific allowed origins: `if (in_array($origin, $allowed, true)) { header("Access-Control-Allow-Origin: {$origin}"); }` |
| **Effort** | 15 minutes |

### Bug #26 — 4 API Methods Ignore Date Range (Hardcode Today)

| | |
|---|---|
| **File** | `admin_ret_dashboard_api.php` L1030, L1069, L1107, L1193 |
| **Methods** | `get_VitrualTag_post`, `get_SalesReturn_post`, `get_LotDetails_post`, `get_CoverUpReport_post` |
| **Symptom** | User's selected date range ignored — always shows today's data only |
| **Fix** | Pass `$from_date, $to_date` to model instead of `date('Y-m-d'), date('Y-m-d')` |
| **Effort** | 30 minutes (4 one-line fixes) |

---

## Sprint 2: Data Integrity Bugs (High Business Impact)

> These return silently wrong data — no PHP error, but reports show incorrect numbers.

### Bug #3 — `get_branch_transfer_details()` Ignores Date and Branch

| | |
|---|---|
| **File** | `admin_ret_dashboard.php` L1241 |
| **Root Cause** | Controller reads `from_date`, `to_date`, `id_branch` from POST but passes ZERO arguments to model method |
| **Fix** | Pass params: `$this->model->get_branch_transfer_details($from_date, $to_date, $id_branch)` and add WHERE clause to model |

### Bug #2 — `get_CustomerDetails()` Ignores Branch

| | |
|---|---|
| **File** | `ret_dashboard_model.php` L469-524 |
| **Root Cause** | Method receives `$id_branch` but query has no branch WHERE clause |
| **Fix** | Add `AND b.id_branch = '{$id_branch}'` (or use query builder) to both SELECT statements |

### Bug #8 — `get_BillClassficationDetails()` Old Customer Logic Wrong

| | |
|---|---|
| **Root Cause** | Old customer query uses same `AND NOT EXISTS(...)` as new customer — should use `AND EXISTS(...)` |
| **Fix** | Remove the `NOT` from the old customer sub-query |

### Bug #11 — `get_new_customer()` Always Returns All-Branch Data

| | |
|---|---|
| **Root Cause** | Model accepts `$id_branch` but SQL at L3570 has no branch WHERE clause |
| **Fix** | Add branch WHERE condition to query |

### Bug #12 — `get_store_sales()` Division by Zero Risk

| | |
|---|---|
| **File** | `admin_ret_dashboard.php` L1391 |
| **Root Cause** | `$estimation['tot_tag_sales']` used as divisor without zero check |
| **Fix** | Add `if ($estimation['tot_tag_sales'] > 0) { ... }` guard |

### Bug #13 — Duplicate Array Key in Stock Methods

| | |
|---|---|
| **Files** | `ret_dashboard_model.php` L3484-3486, L4318-4320 |
| **Root Cause** | `available_gwt` key defined twice; second definition (nwt formula) overwrites first |
| **Fix** | Rename second `available_gwt` to `available_nwt` |

### Bug #4 — `get_MetalBill_details()` No Date Filter

| | |
|---|---|
| **Root Cause** | Method has no date WHERE clause — always returns lifetime total metal bills |
| **Fix** | Add `AND DATE(bill_date) BETWEEN '{$from_date}' AND '{$to_date}'` |

---

## Sprint 3: Code Quality Fixes (Medium Priority)

### Bug #10 — `get_customerOrderDetails()` Uses `$_POST` Directly

| Fix | Replace `$_POST['from_date']` with `$this->input->post('from_date')` |
|---|---|

### Bug #11 — `karigar_orders()` Model Reads `$_POST`

| Fix | Remove model-level POST access; inject via controller parameter |
|---|---|

### Bug #15 — `get_MetalStockDetails()` JS Sends No Date Params

| Fix | Add `from_date` and `to_date` to the AJAX POST in JS at L5697 |
|---|---|

### Bug #14 — Wrong Table Name in `getLedgerBalanceAlertData`

| Fix | Verify actual table name in DB: `ledger` vs `ledger_master` and correct reference |
|---|---|

### Bug #16 — Cash Abstract Typo `cash_abstarct`

| | |
|---|---|
| **Note** | Both sides use the same typo — **do NOT fix one side only**. Fix both simultaneously: controller L1702 array key + JS L7461 key read. |

### Bug #24 — N+1 Query in `get_monthly_sales()`

| Fix | Rewrite with GROUP BY `id_branch, MONTH(bill_date)` single query |
|---|---|

---

## Sprint 4: Low-Priority / Cleanup

| # | Bug | Quick Fix |
|---|---|---|
| 17 | No Content-Type on JSON | Add `header('Content-Type: application/json')` to all AJAX methods |
| 18 | COLOUR_CODE 3-way sync | Centralize in PHP config, pass to JS via settings endpoint |
| 19 | Unformatted date in legacy method | Change `$from_date` to `date('Y-m-d', strtotime($from_date))` |
| 9 | Dead code `get_dashboard_cash_abstarct_details()` | Remove 624 lines |
| 27 | Diamond weight formula error | Fix `blc_diawt` calc at API Ctrl L1502: replace all `lotdiawt` refs with `tagdiawt`, `recdiawt`, `lmdiawt` |

---

## Bug Index (All 26 Live Bugs)

| # | Severity | File | Method | Description | Sprint |
|---|---|---|---|---|---|
| 1 | 🔴 CRITICAL | Both models | All methods | SQL injection via raw string interpolation | *(Systemic — Sprint 5 dedicated)* |
| 2 | 🔴 CRITICAL | Controller | `get_CustomerDetails` | Branch filter ignored | 2 |
| 3 | 🔴 CRITICAL | Controller | `get_branch_transfer_details` | Date+branch params not passed to model | 2 |
| 4 | 🔴 CRITICAL | Model | `get_MetalBill_details` | No date filter — lifetime data | 2 |
| 5 | 🔴 CRITICAL | Controller | `get_LedgerBalanceAlert` | Missing `getLedgerReportData()` → fatal error | **1** |
| 6 | 🟡 MEDIUM | Controller | `get_retail_dashboard_details` | Legacy mega-method, partial branch filter | 4 |
| 7 | 🟡 MEDIUM | Controller | `get_saleschart_details` | Division by zero risk | 2 |
| 8 | 🟡 MEDIUM | Model | `get_dashboard_bills_clasfications` | Old/new customer SQL identical | 2 |
| 9 | 🟡 MEDIUM | Model | `get_dashboard_cash_abstarct_details` | Dead code (624 lines, never called) | 4 |
| 10 | 🟡 MEDIUM | Controller | `get_customerOrderDetails` | `$_POST` direct access | 3 |
| 11 | 🟡 MEDIUM | Model | `karigar_orders` | Model reads `$_POST` directly, new_customer ignores branch | 3 |
| 12 | 🟡 MEDIUM | Model | `get_store_sales` | Division by zero | 2 |
| 13 | 🟡 MEDIUM | Model | `get_stock/branch_stock` | Duplicate array key overwrites nwt | 2 |
| 14 | 🟡 MEDIUM | Model | `getLedgerBalanceAlertData` | Possibly wrong table name (`ledger` vs `ledger_master`) | 3 |
| 15 | 🟡 MEDIUM | JS | `get_MetalStockDetails` call | No date params sent | 3 |
| 16 | 🟡 MEDIUM | JS+Controller | `cash_abstarct` key | Typo both sides — must fix simultaneously | 3 |
| 17 | 🟢 LOW | Controller | All AJAX methods | No Content-Type header | 4 |
| 18 | 🟢 LOW | PHP+JS | COLOUR_CODE | 3-way constant, sync risk | 4 |
| 19 | 🟢 LOW | Controller | `get_retail_dashboard_details` | Unformatted date param | 4 |
| 21 | 🔴 CRITICAL | API Model | `get_branch_wastage` | `$id_metal` undefined — metal filter ignored | **1** |
| 22 | 🔴 CRITICAL | API Model | `get_accountstock_inwards_details` | `$id_category`+`$data` undefined — always unfiltered | **1** |
| 23 | 🔴 CRITICAL | API Model | `get_rate_cut_profit_loss` | `to_date` ignored — wrong date range | **1** |
| 24 | 🟡 MEDIUM | API Model | `get_monthly_sales` | N+1 query anti-pattern | 3 |
| 25 | 🔴 CRITICAL | API Ctrl | Global header | Wildcard CORS — xorigin API access | **1** |
| 26 | 🔴 CRITICAL | API Ctrl | 4 methods | Date range silently replaced with today's date | **1** |
| 27 | 🟡 MEDIUM | API Ctrl | `get_weight_gain_loss_post` | Diamond weight formula always returns -2×lotdiawt | 4 |

---

## Sprint 5: SQL Injection Remediation (Systemic)

> This is the largest single effort. Requires changing ALL model methods from raw `$this->db->query($sql)` to CI query builder.

**Scope:** ~136 model methods across 2 model files  
**Approach:**
1. Fix `ret_dashboard_api_model.php` first (39 methods — smaller, fewer callers)
2. Fix `ret_dashboard_model.php` second (97 methods)
3. Use CI's `$this->db->where()`, `$this->db->like()`, `$this->db->query($sql, $bindings)` for IN-clause params
4. Test each method by verifying `$this->db->last_query()` output after substitution

**Estimated effort:** 3-5 days (1 developer)
