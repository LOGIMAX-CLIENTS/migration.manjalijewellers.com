# MODULE BRAIN — chit_dashboard
> Built: 2026-03-16 | Round: 1 | Status: 🟢 Complete (100%)

---

## 1. Module Overview

**Purpose**: The admin dashboard is the **primary landing page** for logged-in admin users. It aggregates real-time statistics and drill-down reports across customers, accounts, payments, dues, PDC, inter-wallet transactions, daily collection, and renewal data — all filterable by branch and date range. It is the most-accessed module in the entire system.

**Architecture pattern**: Hybrid page-load + AJAX refresh. Initial page load via `index()` renders the shell view (`dashboard.php`). Subsequent data (stats, charts, tables) is loaded by `dashboard.js` via 15+ AJAX calls to separate endpoints.

**Connection diagram**:
```
Browser → layout/template (dashboard view shell)
       → dashboard.js
           → AJAX POST → admin_dashboard/dashboard        (branch/date filtered summary)
           → AJAX POST → admin_dashboard/payment_status   (payment chart data)
           → AJAX POST → admin_dashboard/account_status   (account trend data)
           → AJAX POST → admin_dashboard/inter_wallet_status
           → AJAX POST → admin_dashboard/ajax_collectionData
           → AJAX POST → admin_dashboard/regisert_list
           → AJAX POST → admin_dashboard/getsource_wiserrecord
           → AJAX POST → admin_dashboard/customer_count
           → AJAX POST → admin_dashboard/account_bydate
           → AJAX GET  → admin_dashboard/customer_status
           → AJAX GET  → admin_dashboard/get_collection_app_details
           → AJAX GET  → admin_dashboard/schWise_accounts_list
           → AJAX GET  → admin_dashboard/ajax_get_collection_list (cockpit.php)
           → AJAX POST → rate/ajax/weekstat (cross-module: rate module)
           → AJAX GET  → branch/branchname_list (cross-module: branch module)
           → AJAX POST → admin_customer/get_customer_by_mobile (cross-module)
           → AJAX POST → admin_ret_dashboard/get_customer_order_details (retail cross-module)
           → AJAX POST → admin_reports/payment_summary_modewise (cross-module)
       → dashboard_model → DB
       → Account_model (for cust_wo_accounts_details)
       → employee_model (for customer_status)
       → services_model (for ajax_daily_collection)
```

---

## 2. File Map

| File | Size | Lines | Purpose |
|---|---|---|---|
| `admin/application/controllers/admin_dashboard.php` | 55 KB | 3,483 | Main controller — 77 methods |
| `admin/application/models/dashboard_model.php` | 150 KB | 2,785 | Primary model — 94 methods |
| `admin/assets/js/dashboard.js` | 95 KB | 2,041 | Frontend — 26 AJAX endpoints, all DataTables & charts |
| `admin/application/views/dashboard/dashboard.php` | 103 KB | main view | Full dashboard HTML with inline JS |
| `admin/application/views/dashboard/cockpit.php` | 76 KB | cockpit view | Collection cockpit view |
| `admin/application/views/dashboard/customer_edit.php` | 24 KB | Customer edit form |
| `admin/application/views/dashboard/closed_list.php` | 7 KB | Closed account list view |
| `admin/application/views/dashboard/sale_gchart.php` | 55 KB | Sales Google charts |
| 8 other views | — | Various | Detailed drill-down views |

> ⚠️ `dashboard_model.php` at **150 KB / 94 methods** is the most complex and highest-risk file in this module.

---

## 3. Constructor

```php
public function __construct() {
    parent::__construct();
    if(!$this->session->userdata('is_logged')) { redirect('admin/login'); }
    $this->load->model(self::DAS_MODEL);    // dashboard_model
    $this->load->model(self::SERV_MODEL);   // services_model
    $this->load->model(self::EMP_MODEL);    // employee_model
    $this->load->model(self::ACC_MODEL);    // Account_model
}
```

| Model Constant | Model Name | Purpose |
|---|---|---|
| `DAS_MODEL` | `dashboard_model` | Primary — all dashboard stat queries |
| `SERV_MODEL` | `services_model` | Daily collection calc (ajax_daily_collection) |
| `EMP_MODEL` | `employee_model` | Customer status by employee |
| `ACC_MODEL` | `Account_model` | Closed account details (cust_wo_accounts_details) |

**Session gate**: `is_logged` session check → redirects to `admin/login` if missing. No time-of-day restriction.

**⚠️ Notable**: `admin_settings_model` is called in `index()` at L132-134 but is NOT loaded in constructor — it must be auto-loaded globally.

---

## 4. Entry Points (Route Table)

### 4a. Page-Load Routes (return HTML views)

| Method | Lines | View Rendered | Purpose |
|---|---|---|---|
| `index()` | L130-204 | `dashboard/dashboard` | Main dashboard shell page |
| `reg_detail($type)` | L910-946 | `reports/detailed/customer` | Customer registration detail by type |
| `acc_detail($type)` | L980-1008 | `reports/detailed/account` | Account detail by type |
| `cust_wo_acc_details()` | L1018-1048 | `reports/detailed/customer` | Customers without accounts |
| `cust_wo_accounts_details()` | L1058-1148 | JSON + (implicit view) | Customers w/ closed accounts (hybrid) |
| `acc_wo_pay_details()` | L1166-1196 | `reports/detailed/account` | Accounts with no payments |
| `total_payment_details()` | L1200-1224 | `reports/detailed/payment` | All confirmed payments |
| `closed_acc_detail($type)` | L1232-1256 | `reports/detailed/closed_account` | Closed account details |
| `about_to_close($type)` | L1264-1288 | `reports/detailed/about_to_close` | Near-maturity accounts |
| `pay_detail($type)` | L1296-1324 | `reports/detailed/payment` | Payment detail by type |
| `inter_wallet_details($type)` | L1330-1364 | `reports/detailed/inter_wallet` | Inter-wallet transaction detail |
| `awaiting_detail()` | L1392-1416 | `reports/detailed/payment` | Awaiting-approval payments |
| `paid_unpaid_status($filterBy,$id_scheme)` | L1424-1452 | `reports/detailed/paid_due` | Paid/unpaid status drill-down |
| `due_list($filterBy)` | L1456-1492 | `reports/detailed/unpaid_due` | Due accounts list |
| `postdated_pay_detail($filterBy,$mode,$status)` | L1500-1516 | `reports/detailed/post_payment` | PDC detail list |
| `inter_wallet_accounts_detail()` | L1740-1772 | `reports/detailed/inter_wallet_account` | Inter-wallet account detail |
| `inter_wallet_accounts__woc($from_date,$to_date)` | L1774-1796 | `reports/detailed/inter_wallet_accounts` | Inter-wallet WOC |
| `inter_wallet_transcation_details(...)` | L2288-2322 | `reports/detailed/inter_wallet` | Detailed wallet transactions |
| `get_payment(...)` | L1924-1966 | `reports/detailed/payment` | Payment list with date/branch filter |
| `get_payment_joined(...)` | L1970-2008 | `reports/detailed/payment` | Payment by join-source |
| `get_account(...)` | L2012-2056 | `reports/detailed/account` | Account list with date/branch filter |
| `get_account_joined(...)` | L2172-2210 | `reports/detailed/account` | Account by join-source |
| `get_cancelled_payment(...)` | L2536-2574 | `reports/detailed/payment` | Cancelled payment detail |
| `customer_edit($mobile)` | L2578-2767 | `dashboard/customer_edit` | Customer edit form |
| `reg_detail_bydate(...)` | L3193-3215 | JSON response | Customer registration by date range |
| `cust_wo_acc_details_bydate(...)` | L3217-3247 | JSON | Customers w/o accounts by date |
| `acc_wo_pay_details_bydate(...)` | L3249-3283 | JSON | Accounts w/o payment by date |
| `get_renewals_list($type)` | L1664-1688 | `reports/detailed/renewal` | Renewal list |
| `Upload_apk()` | L3298-3302 | — | APK upload page |
| `dayClose()` | L2925-2969 | — | Day close operation |

### 4b. AJAX Endpoints (return JSON)

| Method | Lines | JS Trigger | Purpose |
|---|---|---|---|
| `dashboard()` | L106-128 | Page load + branch filter | Major: summary cards (pending closures, renewals, requests, feedback) |
| `customer_stat()` | L214-292 | Called by `index()` | Customer registration stats |
| `payment_stat()` | L444-630 | Called by `index()` (deprecated inline) | Payment stats |
| `account_stat()` | L360-434 | Called by `index()` (deprecated inline) | Account stats |
| `pdc_stat()` | L638-754 | Called by `index()` (deprecated inline) | PDC stats |
| `payment_status()` | L1820-1856 | JS L740 | Payment chart (date range) |
| `account_status()` | L1864-1918 | JS L812 | Account chart (date range) |
| `inter_wallet_status()` | L2214-2284 | JS L872 | Inter-wallet credit/debit by branch |
| `ajax_collectionData()` | L2326-2346 | JS L911 | Daily collection summary |
| `regisert_list()` | L2362-2382 | JS L950 | Inter-wallet account registration list |
| `getsource_wiserrecord()` | L3432-3463 | JS L989 | Source-wise record (new version) |
| `ajax_daily_collection()` | L2386-2532 | (commented in JS) | DEPRECATED: day-close live calc |
| `ajax_get_account()` | L2060-2098 | JS L381 | Account list by branch/date |
| `ajax_get_account_joined()` | L2102-2132 | JS L369 | Account by added_by type |
| `ajax_get_payment_joined()` | L2138-2168 | JS L306 | Payment by added_by type |
| `ajax_customer_wishes()` | L3003-3033 | JS L323 | Birthday/wedding wishes list |
| `customer_count()` | L3167-3191 | JS L2004 | Customer stat by date/branch |
| `account_bydate()` | L3285-3296 | JS L2027 | Account stat by date/branch |
| `customer_detail_bydate()` | L950-974 | JS L1632 | Customer detail for date range |
| `customer_status()` | L2781-2845 | JS L1806 | Customer status breakdown |
| `get_collection_app_details()` | L3365-3375 | JS L1850 | App-wise collection detail |
| `get_collection_list()` | L3345-3353 | — | Collection list |
| `ajax_get_collection_list()` | L3355-3363 | JS L14 (cockpit) | Collection list AJAX |
| `ajax_get_ratestat()` | L1524-1544 | — | Gold rate weekly stats |
| `schWise_accounts_list()` | L3464-3483 | JS L1759 | Scheme-wise accounts |
| `inter_wallet_accounts__woc_det()` | L1800-1816 | — | WOC accounts JSON |
| `send_customer_wishes()` | L3053-3149 | — | Send birthday/wedding SMS |
| `paydatewise_schemecoll_list($filterdate)` | L2350-2360 | Internal | Scheme-wise daily collection |

---

## 5. Model Methods Summary

- **`dashboard_model`**: 94 methods — see `METHOD_INDEX.md` for full alphabetical listing
- **`Account_model`**: Used only in `cust_wo_accounts_details()` for `get_all_closed_accdetails()` and `get_all_closed_acccount()`
- **`employee_model`**: Used in `customer_status()` for employee-wise filtering
- **`services_model`**: Used in `ajax_daily_collection()` for `daily_collection()`, `getTodaySummaryBranchWise()`, `allBranches()`

---

## 6. Key Tables Used

| Table | Primary Use | Write Risk |
|---|---|---|
| `payment` | Core payment stats, PDC, collection | ❌ Read-only from dashboard |
| `scheme_account` | Account counts, open/closed stats, due calculation | ❌ Read-only |
| `customer` | Registration stats, birthday/wedding, customer edit | ⚠️ Written by `customer_edit()` |
| `scheme` | Scheme names, types | ❌ Read-only |
| `branch` | Branch list, branch filtering | ❌ Read-only |
| `chit_settings` | `has_lucky_draw`, `currency_symbol`, `branchWiseLogin` | ❌ Read-only |
| `scheme_reg_request` | Existing account requests (req_stat) | ❌ Read-only |
| `cust_enquiry` | Feedback/enquiry counts | ❌ Read-only |
| `inter_wallet` | Wallet credit/debit transactions | ❌ Read-only |
| `metal_rates` | Gold rate (goldrate_22ct) in scheme amount calc | ❌ Read-only |
| `payment_status_message` | Status labels and colors | ❌ Read-only |
| `employee` | Employee list for customer status breakdown | ❌ Read-only |

> ✅ Dashboard is **primarily a read-only module** — writes only happen in `customer_edit()` and `dayClose()`.

---

## 7. Business Rules Summary

See `BUSINESS_RULES.md` for 8 rules. Key rules:
- **Due Date Calculation**: Next due = `MAX(payment.date_payment) + 1 month`. If `is_opening=1 AND no payment`: `last_paid_date + 1 month`. If `is_opening=0 AND no payment`: `scheme_account.date_add`.
- **Paid/Unpaid %**: `paid% = paid / (paid + unpaid) × 100`, where `unpaid` uses `pay_stat` (OLD query, all-time) vs `paid` uses `pymt_status` (new query, sum-based) — MIXED DATA SOURCES.
- **Branch visibility**: `uid=1` → all branches. Others: `branchWiseLogin=1` and `id_branch set` → own branch + `show_to_all=1` branches. `dashboard_branch` filter from session overrides.
- **Closing balance formula**: `closing_balance = yesterday_closing + today_collection - today_closed_amt - today_cancelled_amt`

---

## 8. Known Risks

| # | Risk | Severity | Location |
|---|---|---|---|
| 1 | **SQL injection in `req_stat('ALL')`**: raw `$dashboard_branch` / `$id_branch` concatenated | 🔴 CRITICAL | `dashboard_model.php` L199 |
| 2 | **SQL injection in `acc_wo_pay()`**: raw `$dashboard_branch` / `$id_branch` concatenated | 🔴 CRITICAL | `dashboard_model.php` L94 |
| 3 | **SQL injection in `account_stat()`**: raw `$dashboard_branch` / `$id_branch` concatenated | 🔴 HIGH | `dashboard_model.php` L390-406 |
| 4 | **SQL injection in `pymt_status()`**: raw `$dashboard_branch` / `$id_branch` concatenated | 🔴 HIGH | `dashboard_model.php` L652 |
| 5 | **Mixed data sources in paid/unpaid %**: `paid` from `pymt_status` (SUM), `unpaid` from `pay_stat` (COUNT subquery) — comparing apples to oranges | 🟡 HIGH | Controller L594-614 |
| 6 | **`ajax_daily_collection()` uses `SERV_MODEL` not `DAS_MODEL`**: model swap mid-function | 🟡 MED | Controller L2394 |
| 7 | **`cust_wo_accounts_details()` runs a transaction for a SELECT-only operation** — `trans_begin/commit` wraps only a read query | 🟡 LOW | Controller L1070-1091 |
| 8 | **`due_stat()` accesses `scheme_acc_number != 'null'` as string** — filters out SQL NULL differently from string `'null'` | 🟡 MED | `dashboard_model.php` L462 |
| 9 | **`customer_edit()` — writes customer data without CSRF protection** (GET-accessible route with `$mobile` URL param) | 🔴 HIGH | Controller L2578 |
| 10 | **`dashboard()` method clears and resets `dashboard_branch` session on every call** — can lose branch context | 🟡 MED | Controller L110-114 |
| 11 | **`get_account()` / `due_list()` use bare `company_name` / `branch_name` without quotes as session key** — causes PHP warning | 🟡 LOW | Controller L2022, L1468 |
| 12 | **N+1 query in `cust_wo_accounts_details()`**: loops `acc_detail` and calls `get_all_closed_accdetails()` and `get_all_closed_acccount()` per customer | 🔴 PERF | Controller L1094-1138 |
| 13 | **`ajax_daily_collection()` builds non-persisted closing balance**: recalculates live from today's transactions without persisting — may timeout on large datasets | 🟡 MED | Controller L2386-2532 |
| 14 | **`getsource_wiserrecord_old()` exists alongside `getsource_wiserrecord()`** — dead code not removed | 🟡 LOW | Controller L3376-3431 |

---

## 9. DB Verification Queries

```sql
-- Q1: Verify today's payment collection for a branch
SELECT SUM(payment_amount) as today_total
FROM payment p
JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
WHERE DATE(date_payment) = CURDATE()
AND p.payment_status = 1
AND sa.id_branch = :id_branch;

-- Q2: Verify due accounts count for today
SELECT COUNT(DISTINCT sa.id_scheme_account)
FROM scheme_account sa
WHERE sa.active = 1 AND sa.is_closed = 0
AND DATE(
    CASE
        WHEN sa.is_opening = '1' AND (SELECT MAX(p.date_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1) IS NULL
            THEN DATE_ADD(sa.last_paid_date, INTERVAL 1 MONTH)
        WHEN (SELECT MAX(p.date_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1) IS NULL
            THEN sa.date_add
        ELSE DATE_ADD((SELECT MAX(p.date_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1), INTERVAL 1 MONTH)
    END
) = CURDATE();

-- Q3: Verify inter-wallet balance for a branch
SELECT id_branch, SUM(CASE WHEN type='credit' THEN amount ELSE 0 END) as credits,
       SUM(CASE WHEN type='debit' THEN amount ELSE 0 END) as debits
FROM inter_wallet
WHERE id_branch = :id_branch
AND DATE(date_add) BETWEEN :from_date AND :to_date
GROUP BY id_branch;

-- Q4: Confirm customers without any open account
SELECT COUNT(*) FROM customer c
WHERE NOT EXISTS(SELECT 1 FROM scheme_account sa WHERE sa.id_customer = c.id_customer AND sa.is_closed = 0);
```

---

## 10. Anti-Patterns Register

*(Updated after each bug fix)*

| Date | Pattern | Location | Fix Applied |
|---|---|---|---|
| *(empty — Round 1)* | | | |

---

## 11. Codebase Notes

- **Huge files**: Both controller (55KB) and model (150KB) are very large — the model has 94 methods and is the largest model in the system
- **String SQL**: All DB queries use raw string concatenation — **zero use of CI's Query Builder in dashboard_model** — this is the root cause of all SQL injection risks
- **Branch filter pattern**: Repeated 50+ times: `($uid!=1 ? ($branchWiseLogin==1? ($id_branch!='' ? " and( sa.id_branch=".$id_branch." or b.show_to_all=1 )":'')):''): ''`
- **`filterBy` switch pattern**: Almost every model method uses a `switch($filterBy)` with cases T/Y/TW/TM/ALL — identical pattern repeated 15+ times
- **No `SELECT *`**: ✅ Most queries list specific columns (good practice)
- **Dead code**: `getsource_wiserrecord_old()` (L3376), `ajax_daily_collection` commented in JS (L972), large commented-out block in `index()` (L162-196)
- **Garbled characters**: `â€"` appears in BETWEEN clauses (characters like `–` corrupted to multi-byte) in `enquiry_report`, `reg_stat`, `reg_detail_stat` etc. — these queries will **fail at runtime**
