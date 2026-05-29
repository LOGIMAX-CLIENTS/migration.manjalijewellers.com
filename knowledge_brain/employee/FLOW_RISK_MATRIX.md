# FLOW RISK MATRIX — employee
> **Round**: R2-Upgrade | **Date**: 2026-03-25
> **Primary Entity**: `employee` table — full CRUD lifecycle
> **Write Paths**: 10 confirmed (see SCHEMA_ANALYSIS.md Part D)

---

## 1. State Machine

### `employee.active`
| State | Value | Set By | Guard | Can Transition To |
|---|---|---|---|---|
| Active | 1 | `insert_employee()` (default) | — | Inactive(0) |
| Inactive | 0 | `employee_status()` L950 | ⚠️ GET request (CSRF) | Active(1) |

### `employee_devices.device_status`
| State | Value | Set By | Guard | Can Transition To |
|---|---|---|---|---|
| Disabled | 0 | Default / admin disable | ✅ Always allowed | Enabled(1) |
| Enabled | 1 | `enable_device()` L1174 | ✅ Device count < limit | Disabled(0) |

### `employee_settings` (Lifecycle)
| State | Set By | Guard |
|---|---|---|
| Created with defaults | `emp_post('Add')` L557 | — (auto on employee create) |
| Configured by admin | `update_emp_data()` L1051 | ✅ trans_begin/commit |
| Deleted | `employee_settings('delete')` L978 | ✅ trans_begin/commit |

---

## 2. Inbound Contracts (What Employee Module Expects from Upstream)

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| **Settings** | `chit_settings` row exists | NO null check | Model L962–966 | `get_chit_settings_data()` → PHP error on missing row |
| **Settings** | `company_settings` session value valid | YES — session read | Model L261 | |
| **Settings** | `emp_wallet_account_type` ∈ {0, 1} | PARTIAL — `==1` check | Controller L552 | Unknown type silently skips wallet |
| **Settings** | `chitCollectionEmpCount` is numeric | NO — used in comparison | Controller L1209 | Non-numeric → wrong limit |
| **Retail Settings** | `estimation_app_devices_count` exists | YES — key-based lookup | Model L1087 | Returns NULL → wrong comparison |
| **Branch** | `branch.id_branch` valid | NO explicit check | Model L281 | `branchname_list` works with LEFT JOIN |
| **Department** | `department` records exist | YES — query returns array | Model L133 | Empty array → empty dropdown |
| **Designation** | `designation` records exist | YES — query returns array | Model L109 | Empty array → empty dropdown |
| **Profile** | `profile` records exist | YES — FK join | Model L279 | NULL profile name in list |
| **Wallet** | `wallet_model` loaded | YES — loaded at runtime | Controller L1316 | — |

---

## 3. Outbound Contracts (What Employee Guarantees to Downstream)

| Downstream Module | What Employee Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| **Login** | `employee.active = 1` for login | ✅ `authenticate_user_id()` WHERE active=1 | Inactive employee can't login |
| **Login** | `pwd_hash` is valid bcrypt hash | ✅ `password_hash()` on every add/edit | — |
| **Login** | `employee_settings.access_time_from/to` valid times | PARTIAL — NULL means no restriction | ⚠️ Invalid time format → `strtotime()` fails silently |
| **Customer** | `customer.allocated_employee` references valid employee | ❌ No FK constraint enforcement | 🔴 Delete employee → broken FK in customer |
| **Billing/Estimation** | `employee_settings` disc_limit/tolerance values valid | PARTIAL — NULL means unconfigured | Modules must handle NULL |
| **Mobile App** | `employee_devices.device_status = 1` means active license | ✅ Device count enforced on enable | — |
| **OTP System** | `isOTPReqToLogin()` returns valid config | ✅ Direct chit_settings read | — |

---

## 4. Reversal Contracts (Cancel/Delete/Reverse)

| Operation | Tables That Must Be Restored/Cleaned | Actually Cleaned in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| Delete employee | `address` (DELETE) | ✅ YES | `delete_emp_record()` L550–552 | — |
| Delete employee | `employee` (DELETE) | ✅ YES | `delete_emp_record()` L558–560 | — |
| Delete employee | `employee_settings` (DELETE) | ❌ NO | — | ⚠️ GAP: Orphan settings |
| Delete employee | `employee_devices` (DELETE) | ❌ NO | — | ⚠️ GAP: Orphan device records |
| Delete employee | `wallet_account` (DELETE) | ❌ NO | — | ⚠️ GAP: Orphan wallet |
| Delete employee | `customer.allocated_employee` (NULLIFY) | ❌ NO | — | ⚠️ GAP: Broken FK reference |
| Delete employee | Filesystem images | ✅ YES | `rrmdir()` L737 | — |
| Delete employee | `log` audit entries | ❌ NO | — | ⚠️ No audit trail for deletion |
| Deactivate employee | `employee.active` → 0 | ✅ YES | `employee_status()` L957 | — |
| Reactivate employee | `employee.active` → 1 | ✅ YES | `employee_status()` L957 | — |

> **Reversal completeness: ~30%** — Delete only cleans address + employee + images. Missing: settings, devices, wallet, customer FKs, audit.

---

## 5. Data Consistency Risks

### 5a. Address Duplication (Active Bug)

| Scenario | Expected | Actual | Risk |
|---|---|---|---|
| Edit employee | Address updated in-place | ❌ New address INSERT every time (EMP-BUG-003) | 🔴 `address` table grows by 1 row per edit |
| `get_emp_address()` after N edits | Returns latest address | Returns `row_array()` (first row found) | 🟡 May return stale address |

### 5b. Wallet ↔ Employee Race

| Scenario | Expected | Actual |
|---|---|---|
| Employee add + wallet creation | Atomic | ⚠️ Employee saved first, wallet after — no transaction wrapping |
| Employee add, wallet fails | Rollback employee | ❌ Employee persisted, orphan without wallet |

### 5c. Multi-Company Filter Gap

| Scenario | Expected | Actual |
|---|---|---|
| `get_list_data()` with `company_settings=1` | Filter by `session.id_company` | ❌ `$id_company` undefined in method scope (EMP-BUG-006) |

---

## 6. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-EMP-001 | Add employee → verify wallet created when `emp_wallet_account_type=1` | Wallet record exists | 🔴 HIGH | ❌ |
| FR-EMP-002 | Edit employee → verify address NOT duplicated | `address` table has 1 row per employee | 🔴 HIGH | ❌ — Known broken (EMP-BUG-003) |
| FR-EMP-003 | Delete employee → verify settings, devices, wallet cleaned | No orphan records | 🔴 HIGH | ❌ — Known gap (EMP-BUG-016) |
| FR-EMP-004 | Delete employee allocated to customer → verify customer FK | `allocated_employee` should be NULLed | 🔴 HIGH | ❌ — Known gap |
| FR-EMP-005 | Username validation via `checkuser` endpoint | Should return TRUE/FALSE | 🔴 HIGH | ❌ — DEAD (EMP-BUG-001) |
| FR-EMP-006 | Enable device beyond limit | Should block with message | 🟡 MED | ❌ |
| FR-EMP-007 | Bulk access time update — SQL injection test | Should be sanitized | 🔴 CRITICAL | ❌ — Known SQLi (EMP-BUG-004/007) |
| FR-EMP-008 | Employee status toggle via GET URL (CSRF test) | Should require POST + CSRF token | 🟡 MED | ❌ |
| FR-EMP-009 | `trans_begin()` + `trans_status()` on employee add | Should be atomic | 🔴 HIGH | ❌ — Missing trans_begin (EMP-BUG-002) |
| FR-EMP-010 | Multi-company: employee list isolation | Company A sees only Company A employees | 🔴 HIGH | ❌ — `$id_company` undef (EMP-BUG-006) |
| FR-EMP-011 | Edit employee → change password → verify bcrypt updated | `pwd_hash` should change | 🟡 MED | ❌ — broken check_password (EMP-BUG-015) |
| FR-EMP-012 | Employee image upload → verify DB path updated | `employee.image` should have new path | 🟡 MED | ❌ — Commented out (EMP-BUG-013) |
| FR-EMP-013 | Login after access time window closes | Should block with time message | 🟡 MED | ❌ |
| FR-EMP-014 | Wallet `issued_date` format check | Should be 4-digit year | 🟡 MED | ❌ — 2-digit year (EMP-BUG-011) |
| FR-EMP-015 | `get_emp_by_company()` with malicious username | Should be sanitized | 🔴 CRITICAL | ❌ — Known SQLi (EMP-BUG-008) |
