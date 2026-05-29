# SCHEMA ANALYSIS — employee
> Round R2-Upgrade — 2026-03-25

---

## Part A: Owned Tables (employee module performs CRUD)

### Table: `employee`
| Column | Type | Notes |
|---|---|---|
| `id_employee` | INT PK AUTO | Primary key |
| `firstname` | VARCHAR | Required |
| `lastname` | VARCHAR | Nullable |
| `date_of_birth` | DATE | |
| `emp_code` | VARCHAR | Unique per company; min 3 chars enforced |
| `dept` | INT FK | → `department.id_dept` |
| `designation` | INT FK | → `designation.id_design` |
| `active` | INT | 0=Inactive, 1=Active |
| `date_of_join` | DATE | |
| `email` | VARCHAR | Optional |
| `mobile` | VARCHAR | Unique per company |
| `phone` | VARCHAR | Landline |
| `username` | VARCHAR | Login — unique per company |
| `passwd` | VARCHAR | ⚠️ `base64_encode()` — trivially reversible (EMP-BUG-012) |
| `pwd_hash` | VARCHAR | `password_hash()` — bcrypt (proper) |
| `comments` | TEXT | Admin notes |
| `id_profile` | INT FK | → `profile.id_profile` (role) |
| `id_branch` | INT FK | → `branch.id_branch` (home branch) |
| `login_branches` | VARCHAR | CSV of allowed branch IDs (e.g. `"1,3,5"` or `"0"` for all) |
| `id_company` | INT FK | → `company.id_company` (multi-tenant) |
| `image` | VARCHAR | Image path |
| `date_add` | DATETIME | Creation timestamp |
| `date_upd` | DATETIME | Last update |

### Table: `address`
| Column | Type | Notes |
|---|---|---|
| `id_address` | INT PK AUTO | |
| `id_employee` | INT FK | → `employee.id_employee` ⚠️ duplicates on edit (EMP-BUG-003) |
| `id_customer` | INT FK | Shared table — also used by customer module |
| `address1` | VARCHAR | Line 1 |
| `address2` | VARCHAR | Line 2 |
| `address3` | VARCHAR | Line 3 |
| `pincode` | VARCHAR | |
| `id_country` | INT FK | → `country.id_country` |
| `id_state` | INT FK | → `state.id_state` |
| `id_city` | INT FK | → `city.id_city` |

### Table: `employee_devices`
| Column | Type | Notes |
|---|---|---|
| `id_collection_device` | INT PK AUTO | |
| `emp_id` | INT FK | → `employee.id_employee` |
| `device_uuid` | VARCHAR | Unique device identifier |
| `app_type` | INT | 1=Collection App, 2=Estimation App |
| `device_status` | INT | 0=Disabled, 1=Enabled |
| `device_type` | INT | 1=Android, 2=iOS |
| `device_info` | TEXT | User-agent string |
| `date_add` | DATETIME | Registration timestamp |

### Table: `employee_settings`
| Column | Type | Notes |
|---|---|---|
| `id_emp_sett` | INT PK AUTO | |
| `id_employee` | INT FK | → `employee.id_employee` |
| `disc_limit_type` | INT | Fixed or percent |
| `disc_limit` | DECIMAL | Max discount amount |
| `max_gold_tol` / `min_gold_tol` | DECIMAL | Gold weight tolerance range |
| `max_silver_tol` / `min_silver_tol` | DECIMAL | Silver weight tolerance range |
| `bulk_wast_disc_limit` | DECIMAL | Bulk wastage discount limit |
| `dia_disc_limit` | DECIMAL | Diamond discount limit |
| `allow_day_close` | INT | 0/1 — can close day? |
| `allow_manual_rate` | INT | 0/1 — can set manual rate? |
| `otp_dis_approval` | INT | 0/1 — OTP required for discount? |
| `allowed_old_met_pur` | INT | Old metal purchase allowed? |
| `access_time_from` | TIME | Login window start |
| `access_time_to` | TIME | Login window end |
| `created_on` | DATETIME | Creation timestamp |

---

## Part B: Referenced Tables (other modules, read by employee)

| Table | Module | Used In | Key Columns Read |
|---|---|---|---|
| `department` | Masters | Dropdown, list display | `id_dept`, `name` |
| `designation` | Masters | Dropdown, list display | `id_design`, `name` |
| `profile` | Settings | Role dropdown, list, auth | `id_profile`, `profile_name`, `req_otplogin` |
| `branch` | Settings | Branch dropdown, login, list | `id_branch`, `name`, `active` |
| `company` | Settings | Multi-company login path | `id_company`, `company_name` |
| `chit_settings` | Settings | Device limits, OTP config, wallet type | `chitCollectionEmpCount`, `isOTPReqToLogin`, `loginOTP_exp`, `emp_wallet_account_type` |
| `ret_settings` | Retail | Estimation device limit | `name`, `value` |
| `ret_stone_discount_master` | Retail | Diamond discount range | `min_disc_per`, `max_disc_per`, `id_stone_type` |
| `country` | Settings | Address dropdown | `id_country`, `name` |
| `state` | Settings | Address dropdown | `id_state`, `name` |
| `city` | Settings | Address dropdown | `id_city`, `name` |
| `wallet_account` | Wallet | Employee wallet create | `idemployee`, `wallet_acc_number` |

---

## Part C: Index Analysis (Suspected Missing)

| Table | Column(s) in WHERE | Query Pattern | Index Needed? |
|---|---|---|---|
| `employee` | `username` | Auth query, uniqueness check | ✅ Unique on `(username, id_company)` |
| `employee` | `mobile` | Uniqueness check | ✅ Unique on `(mobile, id_company)` |
| `employee` | `emp_code` | Uniqueness check | ✅ Compound `(emp_code, id_company)` |
| `employee` | `active` | List filter | ⚠️ Low cardinality |
| `address` | `id_employee` | Join for employee address | ✅ FK index — critical since **duplicates exist** (EMP-BUG-003) |
| `employee_devices` | `emp_id`, `app_type` | Device lookup per employee | ✅ Compound `(emp_id, app_type)` |
| `employee_settings` | `id_employee` | Settings lookup | ✅ Unique `(id_employee)` — 1:1 expected |

---

## Part D: Write Operation Inventory

| Method | Table(s) Written | Columns Changed | Guards Present | Risk |
|---|---|---|---|---|
| `insert_employee()` | `employee` (INSERT), `address` (INSERT) | All info + address | ❌ No `trans_begin()` in controller (EMP-BUG-002) | 🔴 No rollback |
| `update_employee()` | `employee` (UPDATE), `address` (INSERT or UPDATE) | All info + address | ❌ String bug in address check (EMP-BUG-003) | 🔴 Duplicate address every edit |
| `delete_emp_record()` | `address` (DELETE), `employee` (DELETE) | Entire rows | ❌ No dependency check (EMP-BUG-016) | 🔴 Orphans devices, settings, wallet, customer FKs |
| `update_employee_only()` | `employee` (UPDATE) | Status toggle field | ✅ Simple WHERE | — |
| `update_emp_device_status()` | `employee_devices` (UPDATE) | `device_status` | ✅ trans_begin/commit | — |
| `insertData()` (settings) | `employee_settings` (INSERT) | All settings fields | ✅ trans_begin/commit + log | — |
| `updateData()` (settings) | `employee_settings` (UPDATE) | All settings fields | ✅ trans_begin/commit + log | — |
| `deleteData()` (settings) | `employee_settings` (DELETE) | Entire row | ✅ trans_begin/commit | — |
| `updEmpAccessTime()` | `employee_settings` (UPDATE) | access_time_from/to | ❌ Raw SQL — no escaping (EMP-BUG-007) | 🔴 SQL injection |
| `emp_wallet_acc()` | `wallet_account` (INSERT) | Full wallet record | ❌ 2-digit year (EMP-BUG-011) | 🟡 Date bug |

---

## Risk Flags

| Table | Risk | Details |
|---|---|---|
| `employee` | HIGH | Central identity table — auth, customer FK, everywhere |
| `employee.passwd` | HIGH | base64 — trivially decoded (EMP-BUG-012) |
| `address` | HIGH | Duplicates accumulate on every edit (EMP-BUG-003) |
| `employee_devices` | MEDIUM | Device licensing depends on accurate count |
| `employee_settings` | MEDIUM | `updEmpAccessTime()` has SQLi (EMP-BUG-007) |
