# Employee Module — Business Rules

> **Brain Updated:** 2026-03-25 | **Round:** R2-Upgrade

---

## RULE-EMP-001: Employee Creation Limit (No Hard Limit)

**Rule:** Unlike customers, there is no `limit_emp` setting. Any number of employees can be created.

**Validation:** None — no count-gating on employee creation.

---

## RULE-EMP-002: Password Dual-Storage

**Rule:** On creation and edit, passwords are stored in TWO formats simultaneously:
- `passwd` = `base64_encode($password)` (legacy, trivially reversible)
- `pwd_hash` = `password_hash($password, PASSWORD_DEFAULT)` (bcrypt, proper)

**Formula:**
```
Store: passwd  = base64_encode($plain) → __encrypt()
       pwd_hash = password_hash($plain) → bcrypt
Login: password_verify($plain, $pwd_hash)  → bcrypt only
```

**Implementation:** `emp_post('Add')` L449–455, `emp_post('Edit')` L627–640
**⚠️ Security:** Legacy `passwd` field is readable to any superadmin (shown decoded in Edit form when `id_branch == ''`)

---

## RULE-EMP-003: Emp Code Format and Uniqueness

**Rule:** Employee code must be ≥ 3 characters. It must be unique within the same `id_company` (if multi-company mode enabled).

**Formula:**
```
IF emp_code.length < 3:
    REJECT → redirect with error "emp_code length should be at least 3"
IF check_empcode(code, emp_id) returns TRUE:
    REJECT (existing code, JS-side block)
```

**Implementation:** `emp_post('Add')` L443–445 (length), `checkempcode()` L894–939
**Validation:** Server-side length check + JS AJAX uniqueness check

---

## RULE-EMP-004: Username Uniqueness

**Rule:** Username must be unique within `id_company` (if multi-company mode).

**Formula:**
```
check_username($username, $emp_id) → scoped by id_company if company_settings == 1
IF exists: return TRUE (taken)
```

**Implementation:** `isUserAvailable()` L133–187 ⚠️ DEAD (EMP-BUG-001)
**⚠️ Bug:** Endpoint has `print_r(); exit;` — real validation never executes

---

## RULE-EMP-005: Device License Control

**Rule:** Mobile devices for Collection and Estimation apps have configurable per-app limits.

**Formula:**
```
Collection App (app_type=1):
    max_devices = chit_settings.chitCollectionEmpCount
    IF get_enabled_device_count(0) < max_devices → ALLOW enable
    ELSE → BLOCK "Maximum devices reached"

Estimation App (app_type=2):
    max_devices = ret_settings.estimation_app_devices_count
    IF get_enabled_device_count(1) < max_devices → ALLOW enable
    ELSE → BLOCK

Disabling: ALWAYS allowed (no limit check)
```

**Implementation:** `enable_device()` L1174–1297
**Validation:** Server-side count check before enable

---

## RULE-EMP-006: Employee Settings Auto-Creation

**Rule:** Default `employee_settings` record is auto-created for every new employee with full-day access.

**Formula:**
```
ON employee create:
    INSERT employee_settings (
        id_employee = $emp_id,
        access_time_from = '00:00:01',
        access_time_to = '23:59:58'
    )
```
All other settings (disc_limit, tolerance, etc.) default to NULL/0 until admin configures.

**Implementation:** `emp_post('Add')` L557–562
**Validation:** Server-side only

---

## RULE-EMP-007: Access Time Restriction

**Rule:** Employees can have time-windowed login access. Login controller checks `checkAccessTime()` before allowing session.

**Formula:**
```
IF access_time_from IS NULL:
    ALLOW (no restriction)
ELSE:
    IF now() BETWEEN access_time_from AND access_time_to:
        ALLOW
    ELSE:
        BLOCK with message "Allowed access time {from} to {to}"
```

**Implementation:** `checkAccessTime()` model L807–836
**Validation:** Server-side in login flow

---

## RULE-EMP-008: Employee Wallet Creation

**Rule:** When `chit_settings.emp_wallet_account_type == 1`, a wallet account is auto-created for the employee on add.

**Formula:**
```
IF emp_wallet_account_type == 1:
    get_wallet_acc_number() → sequential number
    INSERT wallet_account (
        idemployee = $emp_id,
        wallet_acc_number = $next_number,
        issued_date = date('y-m-d H:i:s')  ← ⚠️ 2-digit year (EMP-BUG-011)
    )
```

**Implementation:** `emp_wallet_acc()` L1316–1345
**Validation:** Server-side; no rollback if wallet creation fails

---

## RULE-EMP-009: Branch-wise Employee Visibility (Profile Gate)

**Rule:** Non-superadmin users (`id_profile != 1`) cannot see superadmin employee records in the list.

**Formula:**
```
IF session.profile != 1:
    WHERE employee.id_profile != 1  (hide superadmin employees)
```

**Implementation:** `get_list_data()` model L267–273
**Validation:** Server-side SQL filter

---

## RULE-EMP-010: Multi-Company Employee Isolation

**Rule:** When `company_settings == 1` (multi-tenant), employee list is filtered by `id_company`.

**Formula:**
```
IF is_multi_company == 1:
    WHERE employee.id_company = $id_company
```

**Implementation:** `get_list_data()` model L261–264
**⚠️ Bug:** `$id_company` is never assigned in this method scope (EMP-BUG-006)

---

## RULE-EMP-011: Employee Delete (Cleanup Rules)

**Rule:** Employee deletion deletes address first, then employee record. Image folder is removed from filesystem.

**Formula:**
```
DELETE FROM address WHERE id_employee = $id
IF address deleted:
    DELETE FROM employee WHERE id_employee = $id
IF employee deleted AND image dir exists:
    recursive delete image folder
```

**Missing cleanup:** employee_settings, employee_devices, wallet_account, customer FK references

**Implementation:** `emp_post('Delete')` L711–741, `delete_emp_record()` model L544–570
**⚠️ Bug:** No dependency check before delete (EMP-BUG-016)

---

## RULE-EMP-012: Discount Limits per Employee

**Rule:** Each employee can have configured discount limits for gold, silver, diamond, and bulk wastage.

| Setting | Purpose | Default |
|---|---|---|
| `disc_limit_type` | Fixed or percent | NULL |
| `disc_limit` | Max discount amount | NULL |
| `max_gold_tol` / `min_gold_tol` | Gold tolerance range | NULL |
| `max_silver_tol` / `min_silver_tol` | Silver tolerance range | NULL |
| `bulk_wast_disc_limit` | Bulk wastage discount limit | NULL |
| `dia_disc_limit` | Diamond discount limit | NULL |

**Implementation:** `update_emp_data()` L1051–1143
**Validation:** Server-side insert/update via generic `insertData`/`updateData`
