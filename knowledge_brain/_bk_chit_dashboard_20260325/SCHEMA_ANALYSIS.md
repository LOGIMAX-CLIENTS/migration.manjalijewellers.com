# SCHEMA ANALYSIS — chit_dashboard
> Round 1 — 2026-03-16

---

## Part A: Primary Tables (Owned / Heavily Used by Dashboard)

### `payment` (read-only from dashboard)
| Column | Use in Dashboard | Notes |
|---|---|---|
| `id_payment` | PK | Primary join key |
| `id_scheme_account` | FK | Links to `scheme_account` |
| `payment_amount` | SUM in all collection stats | Core financial value |
| `payment_status` | WHERE filter (1=confirmed, 2=await, 4=cancelled, 7=PDC-presentable, 2=PDC-presented) | Critical — multi-meaning for status=2 |
| `date_payment` | DATE filter in all time-range queries | ⚠️ Wrapped in `DATE()` → index-defeating |
| `added_by` | 0=Admin, 1=Web, 2=Mobile, 3=Collection | Source breakdown |
| `payment_mode` | CHQ/ECS flags for PDC | PDC type indicator |
| `id_branch` | Branch filter in PDC | Note: some queries use `sa.id_branch`, others `p.id_branch` — inconsistent |
| `metal_weight` | Weight-scheme payment tracking | Used in due list / payment stat |
| `payment_ref_number` | Display in drilldown tables | May be empty string |
| `bank_acc_no`, `bank_name`, `bank_IFSC` | Cheque detail display | — |
| `id_transaction` | External gateway ref | — |
| `remark` | Admin notes | — |

### `scheme_account` (read-only from dashboard)
| Column | Use in Dashboard | Notes |
|---|---|---|
| `id_scheme_account` | PK | Primary join key |
| `id_customer` | FK | Customer link |
| `id_scheme` | FK | Scheme link |
| `id_branch` | Branch filter | Critical for all branch-scoped stats |
| `is_closed` | 0/1 — active vs closed filter | **is_closed=0 AND active=1** = active account |
| `active` | 1=active | Used alongside `is_closed` |
| `paid_installments` | Renewal / about-to-close calc | Incremented by payment module |
| `last_paid_date` | Due date calc (is_opening cases) | Used in RULE-DAS-001 |
| `is_opening` | 1= legacy opening balance account | Changes due date calculation |
| `date_add` | Account creation date | Fallback due date if no payments |
| `scheme_acc_number` | Display / filtering | ⚠️ tested as `!= 'null'` (string) not `IS NOT NULL` in due_stat |
| `group_code` | Display in drilldown tables | IFNULL'd to empty string |
| `account_name` | Display in payment detail | — |
| `start_date` | Display in account detail | — |
| `closing_date`, `closing_balance` | Closed account detail display | — |
| `added_by` | Source breakdown (MOB/WEB/ADMIN) | Same codes as payment.added_by |

### `customer` (read from dashboard, written by customer_edit)
| Column | Use in Dashboard | Write Risk |
|---|---|---|
| `id_customer` | PK | — |
| `firstname`, `lastname` | Name display in all drilldown tables | Written by `updateData()` |
| `mobile` | Mobile display, lookup in customer_edit | Written |
| `date_add` | Registration date for reg_stat | — |
| `date_of_birth` | Birthday query (cus_birthday) | Written |
| `date_of_wed` | Wedding query (cus_wedding_day) | Written |
| `profile_complete` | 1=complete / null/0=incomplete | Written |
| `active` | 1=active | Written |
| `is_new` | 0=new / 'Y'=new (inconsistent — see bug) | — |
| `added_by` | Registration source | — |
| `kyc_status` | Not used by dashboard directly | — |
| `id_employee` | Employee-customer link | Used in `customer_join` (ADMIN filter) |

---

## Part B: Referenced Tables (From Other Modules)

| Table | Read By | Key Columns Used | Owner Module | Risk |
|---|---|---|---|---|
| `scheme` | Multiple dashboard methods | `scheme_name`, `code`, `scheme_type`, `total_installments`, `max_weight`, `amount`, `flexible_sch_type` | chit_settings | 🟡 Adding new `scheme_type` values breaks labels |
| `branch` | All branch-filtered queries | `id_branch`, `branch_name`, `active`, `show_to_all` | branch module | 🔴 `show_to_all` column critical for visibility logic |
| `chit_settings` | acc_wo_pay_details, due_list, total_payment | `has_lucky_draw`, `currency_symbol`, `branchWiseLogin` | chit_settings | 🟡 Config changes must be reflected in dashboard |
| `inter_wallet` | All inter-wallet stats | `id_branch`, `type` (credit/debit), `amount`, `date_add` | wallet module | 🟡 Wallet module schema changes break wallet charts |
| `scheme_reg_request` | req_stat, get_existingSchRequests_dashboard | `id_reg_request`, `id_branch`, `date_add`, `status` | account module | 🟡 Status codes must match |
| `cust_enquiry` | enquiry_report, get_enquiry | `id_enquiry`, `date_enquiry` | CRM module | 🟡 Low risk — only COUNT used |
| `metal_rates` | acc_wo_pay_details (amount calc) | `goldrate_22ct`, `id_metalrates` | settings module | 🟡 Subquery picks latest rate by DESC limit 1 |
| `employee` | customer_status | `id_employee` | HR module | 🟡 Low risk |
| `payment_status_message` | pay_detail_stat, total_payment_details | `id_status_msg`, `payment_status`, `color` | payment module | 🟡 Adding new statuses needs this table updated |
| `scheme_group` | get_scheme_group | `id_scheme_group` | chit_settings | 🟡 Count only |
| `daily_collection` | (written by dayClose via services_model) | `closing_balance_amt`, `closing_balance_wgt`, `date`, `id_branch` | services module | 🟡 Schema must match services_model expectations |

---

## Part C: Index / Performance Analysis

| Table | Column in WHERE | Index Status | Risk |
|---|---|---|---|
| `payment` | `DATE(date_payment)` | ❌ Function wrapper defeats index | Every timed payment query is full-scan |
| `payment` | `payment_status` | ✅ Should have index | Critical for `WHERE payment_status=1` filtering |
| `payment` | `id_branch` | ⚠️ Unknown — confirm | Used in PDC and collection queries |
| `payment` | `id_scheme_account` | ✅ FK should have index | Core join path |
| `scheme_account` | `is_closed`, `active` | ✅ Should be indexed | Used in almost every account query |
| `scheme_account` | `id_branch` | ⚠️ Unknown — confirm | Critical for branch-scoped stats |
| `scheme_account` | `paid_installments` | ⚠️ Unknown | Used in renewal/about-to-close calculations |
| `customer` | `date_add` | ⚠️ Unknown | Used in registration time-range queries |
| `customer` | `DATE(date_add)` | ❌ Function wrapper if used | Same issue as payment.date_payment |
| `inter_wallet` | `id_branch`, `type` | ⚠️ Unknown | Used in inter-wallet status queries |

---

## Part D: Garbled SQL Strings (Runtime-Breaking Bug)

> These appear in `dashboard_model.php` as `â€"` (garbled multi-byte encoding of `–`). Queries containing these will **fail at runtime** with SQL syntax error.

| Method | Line | Garbled Segment |
|---|---|---|
| `enquiry_report` | L25, L55 | `CURDATE() â€" INTERVAL DAYOFWEEK(...)` |
| `reg_stat` | L147 | Same pattern in LW (Last Week) case |
| `reg_detail_stat` | L341 | Same |
| `account_stat` | L396 | Same |
| `pay_stat` | L628 | Same |
| `acc_detail_stat` | ~L561 | Same |
| `pay_detail_stat` | ~L714 | Same |

**Affected filterBy cases**: `'LW'` (Last Week) in virtually all stat methods.

**Impact**: Selecting "Last Week" on any dashboard filter causes a PHP/MySQL error. This likely explains why "LW" is missing from many JS filter options while Y/T/TW/TM/ALL are present.

**Fix**: Replace `â€"` with `-` (hyphen/minus) in each garbled occurrence.

---

## Part E: Write Path Inventory

| Method | Table Written | Columns Changed | Guards | Risk |
|---|---|---|---|---|
| `customer_edit()` + `updateData()` | `customer` | All editable customer fields | ❌ No CSRF, no validation | 🔴 HIGH |
| `dayClose()` via services_model | `daily_collection` | Closing balance, weights | ✅ Requires login | 🟡 MED |
| `send_customer_wishes()` | `sms_log` (indirect) | SMS send record | ❌ No dedup protection | 🟡 MED |
| `upload()` | Disk (APK file) | APK file on server | ⚠️ No file type validation found | 🔴 HIGH |
