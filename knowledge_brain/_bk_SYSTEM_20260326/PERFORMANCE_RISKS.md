# Performance Risks — Missing Indexes, Slow Queries, Wide Tables
> Last updated: 2026-03-16
> Source: 9 module brains

---

## Missing or Suspected Missing Indexes

| ID | Table | Column | Queried By | Query Pattern | Risk |
|---|---|---|---|---|---|
| PERF-001 | `payment` | `id_scheme_account` + `payment_status` + `entry_date` | chit_reports (all reports), payment | Range queries with multiple WHERE conditions | 🔴 HIGH — full table scan on large payment table |
| PERF-002 | `payment` | `entry_date` | chit_reports `payment_date_range`, `payment_datewise` | `WHERE DATE(entry_date) BETWEEN :from AND :to` | 🔴 HIGH — `DATE()` wrapper prevents index use |
| PERF-003 | `scheme_account` | `is_closed` + `id_scheme` | chit_reports `closedaccount_list`, `payment_outstanding` | Filter by closed status + scheme | 🟡 MED |
| PERF-004 | `customer_enquiry` | `status` + `entry_date` | chit_reports `ajax_enquiry_list` | Range + status filter | 🟡 MED |
| PERF-005 | `log_detail` | `id_log` + `log_type` | chit_reports `log_model::get_log_detail_range` | Date range filter | 🟡 MED — log table grows unbounded |
| PERF-006 | `gift_issued` | `id_scheme_account` | chit_reports `ajax_gift_report`, account | JOIN on scheme_account | 🟡 MED |
| PERF-007 | `ret_taging` | `tag_status` + `current_branch` | Tagging, Billing, Estimation | Stock reports filter by status + branch | 🔴 HIGH — core stock table |
| PERF-008 | `ret_estimation_items` | `id_estimation` | Billing, Estimation | JOIN on estimation header | 🟡 MED |

---

## `DATE()` Wrapping on Indexed Columns (Index-Defeating Queries)

> [!WARNING]
> Using `DATE(column)` or `YEAR(column)` in WHERE clauses prevents MySQL from using indexes on that column, causing full table scans.

| ID | Module | File | Pattern | Fix |
|---|---|---|---|---|
| PERF-010 | chit_reports | `payment_model.php` | `WHERE DATE(entry_date) BETWEEN '$from' AND '$to'` | Use `WHERE entry_date >= '$from 00:00:00' AND entry_date < '$to+1 00:00:00'` |
| PERF-011 | chit_reports | `admin_report_model.php` | `WHERE DATE(cus_dob)` in celeb dates | Use date range bounds instead |
| PERF-012 | payment | `payment_model.php` | Multiple range queries using `DATE()` wrapper | Same fix as above |

---

## Wide Tables (100+ Columns)

| ID | Table | Owner | ~Columns | Risk |
|---|---|---|---|---|
| PERF-020 | `chit_settings` | chit_settings | 60+ | Every `SELECT *` loads all 60 cols — no module uses all of them |
| PERF-021 | `scheme` | Scheme | 130+ | `SELECT *` in many scheme queries — especially `ajax_get_scheme()` for account form |
| PERF-022 | `customer` | Customer | 30+ | `SELECT *` in customer joins across all modules |
| PERF-023 | `scheme_account` | Account | 20+ | `SELECT *` in account list/report queries |

---

## N+1 Query Patterns

| ID | Module | Location | Pattern | Risk |
|---|---|---|---|---|
| PERF-030 | chit_reports | `scheme_summary` controller | Calls `account_model::scheme_summary_data()` which runs 4 independent queries per scheme | 🟡 MED — multiplied by number of schemes in date range |
| PERF-031 | Billing | `ret_billing_model` | Journal entry inserted per line item inside billing loop | 🟡 MED — no batch insert |
| PERF-032 | Estimation | `getCustomerDet()` | Chains 3+ queries for customer purchase history | 🟡 MED |
| PERF-033 | chit_settings | `admin_scheme.php` | `SHOW COLUMNS` called on every insert/update (2x per save) | 🟡 MED — metadata query on every save |

---

## Unbounded Queries (No LIMIT)

| ID | Module | Method | Table | Risk |
|---|---|---|---|---|
| PERF-040 | chit_reports | `exl_rep_outstanding` (Excel export) | `scheme_account` | No LIMIT — full table export can time out or exhaust memory |
| PERF-041 | chit_reports | `getMemberReport` | `scheme_account`, `customer`, `scheme`, `address` | Server-side DataTables — should be paginated |
| PERF-042 | payment | `payment_list` | `payment` | DataTables query without explicit LIMIT in model |
| PERF-043 | account | `ajax_get_account_list` | `scheme_account` | Returns all accounts without pagination |

---

## Large File / Controller Complexity

| ID | Module | File | Size | Risk |
|---|---|---|---|---|
| PERF-050 | payment | `payment_model.php` | 8,891 lines / 590KB | Memory loaded on every payment request — PHP requires full parse |
| PERF-051 | payment | `payment.js` | ~10,000 lines / 357KB | Large JS download for every user session |
| PERF-052 | chit_settings | `admin_settings.php` | 4,709 lines | Loaded for every request requiring settings |
| PERF-053 | account | `admin_manage.php` | 4,478 lines | High memory footprint per request |
| PERF-054 | Scheme | `form.php` (view) | 132KB | Massive view file sent to browser |
| PERF-055 | payment | `admin_payment.php` | 6,237 lines | Largest controller |

---

## Log File Growth Risks

| ID | Module | Location | Growth Pattern | Risk |
|---|---|---|---|---|
| PERF-060 | payment + chit_reports | `admin/log/{date}/manual/` | 1 file per day, unlimited size | 🔴 HIGH — no rotation, no size limit, web-exposed (XMOD-005) |
| PERF-061 | All modules | `log_detail` table | 1 row per action, no archive | 🟡 MED — table will grow very large over time |
