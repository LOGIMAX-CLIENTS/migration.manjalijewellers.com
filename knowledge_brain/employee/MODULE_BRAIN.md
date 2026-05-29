# Employee Module Brain

> **Module:** employee | **Controller:** `Admin_employee` | **Round:** R2-Upgrade
> **Brain Built:** 2026-03-17 | **Upgraded:** 2026-03-25 | **Status:** R2-Upgrade Complete (100%)
>
> **🔄 UPGRADED BRAIN**
> Previous: R1 (2026-03-17) — 4 files, 26 ctrl methods, 44 model methods, ~100% coverage
> Upgraded: R2 (2026-03-25) — **+7 brain files** (METHOD_INDEX, BUSINESS_RULES, CROSS_MODULE_MAP, SCHEMA_ANALYSIS, FLOW_RISK_MATRIX, INVARIANT_MATRIX, FORENSIC_TEMPLATE)
> Code Δ: controller 0L (25M, -1 from R1 re-count), model 0L (47M, +3 from R1 re-count), JS +724L (2,152)
> New: 15 QA-ready test scenarios, 3 state machines, 10 inbound contracts, 7 outbound contracts, 10 reversal checks

---

## 1. Module Overview

**Purpose:** Complete employee lifecycle management — create, edit, delete employees; manage login credentials, device registration for collection/estimation apps, access time restrictions, discount limits, employee settings (per-employee permission policy). Also handles wallet account creation for employees.

### File Map

| File | Lines | Size | Purpose |
|---|---|---|---|
| `admin/application/controllers/admin_employee.php` | 1,366 | 31 KB | Main controller — CRUD, device management, settings, image upload |
| `admin/application/models/employee_model.php` | 1,106 | 26 KB | DB ops — employee, address, device, settings, authentication (47 methods) |
| `admin/assets/js/employee.js` | 2,152 | ~74 KB | Form logic, AJAX, device management UI (+724 lines from R1) |
| `admin/application/views/master/employee/form.php` | ~500 | 18 KB | Add/Edit employee form |
| `admin/application/views/master/employee/list.php` | ~460 | 17 KB | Employee list with DataTable + device panel |
| `admin/application/views/master/employee/emp_set_list.php` | ~220 | 7.4 KB | Employee settings (access time / discount limits) view |

### Connection Flow

```
Browser → employee.js
        → AJAX → Admin_employee controller
        → employee_model / Admin_settings_model / wallet_model / log_model
        → DB: employee, address, employee_devices, employee_settings
        → Views: master/employee/form, list, emp_set_list
```

---

## 2. Constructor

```php
class Admin_employee extends CI_Controller
{
    const VIEW_FOLDER   = 'master/';
    const SET_MODEL     = "Admin_settings_model";
    const EMP_MODEL     = "employee_model";
    const EMP_VIEW      = "master/employee/";
    const EMP_IMG_PATH  = 'assets/img/employee/';
    const EMP_IMG       = 'employee.jpg';
    const DEF_EMP_IMG_PATH = 'assets/img/default.png/';
}
```

**Models Loaded:**

| Model | Alias | Purpose |
|---|---|---|
| `employee_model` | `$this->employee_model` | Primary — all employee DB ops |
| `Admin_settings_model` | via const | Access control, settings |
| `log_model` | directly | Audit trail |
| `wallet_model` | directly | Employee wallet creation |

**Session Gate:** `is_logged` → redirect to `/admin/login`  
**Note:** No `get_access()` check in constructor — permissions checked per-method in `ajax_get_emp_list()` only.

---

## 3. Entry Points (Routes)

| URL | HTTP | Method | Lines | Purpose |
|---|---|---|---|---|
| `/employee` | GET | `emp_list()` | L207–238 | Employee list view |
| `/employee/ajax_get_emp_list` | GET | `ajax_get_emp_list()` | L67–89 | DataTable AJAX load |
| `/employee/emp_form/Add` | GET | `emp_form('Add')` | L262–290 | Add employee form |
| `/employee/emp_form/Edit/{id}` | GET | `emp_form('Edit', $id)` | L294–408 | Edit employee form |
| `/employee/emp_post/Add` | POST | `emp_post('Add')` | L437–582 | Save new employee |
| `/employee/emp_post/Edit/{id}` | POST | `emp_post('Edit', $id)` | L586–707 | Update employee |
| `/employee/emp_post/Delete/{id}` | GET | `emp_post('Delete', $id)` | L711–741 | Delete employee |
| `/employee/check_mobile` | POST | `check_mobile()` | L747–772 | Mobile uniqueness check |
| `/employee/checkempcode` | POST | `checkempcode()` | L894–939 | Emp code uniqueness check |
| `/employee/checkuser` | POST | `isUserAvailable()` | L133–187 | Username uniqueness check |
| `/employee/dept` | GET | `get_dept()` | L97–111 | Department dropdown |
| `/employee/designation` | GET | `get_designation()` | L115–129 | Designation dropdown |
| `/employee/employee_status/{status}/{id}` | GET | `employee_status($s,$id)` | L950–964 | Toggle active flag |
| `/employee/branchname_list` | GET | `branchname_list()` | L967–972 | Branch dropdown |
| `/employee/employee_settings/list` | GET | `employee_settings('list')` | L1030–1036 | Settings list view |
| `/employee/employee_settings/empset_list` | POST | `employee_settings('empset_list')` | L995–998 | Employees without settings |
| `/employee/employee_settings/active_employee` | POST | `employee_settings('active_employee')` | L999–1002 | Active employee dropdown |
| `/employee/employee_settings/updateAccessTimeAll` | POST | `employee_settings('updateAccessTimeAll')` | L1003–1029 | Bulk access time update |
| `/employee/employee_settings/delete/{id}` | GET | `employee_settings('delete',$id)` | L978–994 | Delete setting record |
| `/employee/employee_settings` (default) | POST | `employee_settings()` | L1037–1049 | Load setting data by date/emp |
| `/employee/update_emp_data` | POST | `update_emp_data()` | L1051–1143 | Create/update employee settings (discount/access) |
| `/employee/enable_device` | POST | `enable_device()` | L1174–1297 | Enable/disable mobile device for app |
| `/employee/get_emp_name_list` | GET | `get_emp_name_list()` | L1298–1303 | Employee name list (device assign) |
| `/employee/get_device_list` | GET | `get_device_list()` | L1304–1309 | Device list |
| `/employee/get_employee_name_byid` | POST | `get_employee_name_byid()` | L1310–1315 | Employee name by ID |
| `/employee/get_employee_devices` | POST | `get_employee_devices()` | L1348–1360 | Devices by employee + app_type |
| `/employee/get_employee` | GET | `get_employee()` | L943–948 | All employees (debug/bulk) |

---

## 4. Key Tables

| Table | Role | Key Columns |
|---|---|---|
| `employee` | Primary | `id_employee`, `emp_code`, `username`, `passwd`, `pwd_hash`, `active`, `id_branch`, `login_branches`, `id_profile`, `id_company` |
| `address` | Employee address | `id_employee`, `id_country`, `id_state`, `id_city`, `address1-3`, `pincode` |
| `employee_devices` | Mobile device registry | `id_collection_device`, `emp_id`, `device_uuid`, `app_type`, `device_status`, `device_type`, `device_info` |
| `employee_settings` | Per-employee permissions | `id_emp_sett`, `id_employee`, `disc_limit`, `access_time_from`, `access_time_to`, `allow_day_close`, `allow_manual_rate`, `otp_dis_approval` |
| `department` | Dept reference | `id_dept`, `name` |
| `designation` | Designation reference | `id_design`, `name` |
| `profile` | User profile/role | `id_profile`, `profile_name`, `req_otplogin` |
| `branch` | Branch reference | `id_branch`, `name` |
| `chit_settings` | Device limits | `chitCollectionEmpCount`, `isOTPReqToLogin`, `loginOTP_exp` |
| `wallet_account` | Employee wallet | `id_wallet_account`, `idemployee`, `wallet_acc_number` |
| `ret_settings` | Retail settings | `name`, `value` (e.g. `estimation_app_devices_count`) |
| `ret_stone_discount_master` | Diamond discount limits | `id_stone_type`, `min_disc_per`, `max_disc_per` |
| `log` | Audit log | written by `log_model::log_detail()` |
| `company` | Company reference | `id_company`, `company_name` |

---

## 5. Auth / Password Architecture

**Dual-password system:** Employees have two password fields:

| Field | Format | Use |
|---|---|---|
| `passwd` | `base64_encode($pwd)` | Legacy — used by `authenticate_user()` (commented/old login path) |
| `pwd_hash` | `password_hash($pwd, PASSWORD_DEFAULT)` | Modern — used by `authenticate_user_id()` (active login path) |

**Password on Add:**
```php
$basepwd = $this->$model->__encrypt($emp_data['passwd']);   // base64
$pwd = password_hash(trim($emp_data['passwd']), PASSWORD_DEFAULT); // bcrypt
// Both stored: passwd = $basepwd, pwd_hash = $pwd
```

**Password on Edit:**
```php
$pwd_check = $this->$model->check_password($id, $emp_data['passwd']);
// check_password() queries: WHERE id_employee=$id AND passwd=$pswd
// → Compares raw input against stored base64 (not actual verification)
// → If match: store as-is (keep old base64 as passwd)
// → If no match: re-encrypt as base64 → $basepwd
// pwd_hash is ALWAYS regenerated: password_hash($emp_data['passwd'], PASSWORD_DEFAULT)
```

**Login:**
```php
// authenticate_user_id($username, $passwd):
//   1. Fetch employee by username WHERE active=1
//   2. password_verify($passwd, $row->pwd_hash)  ← bcrypt verification
```

---

## 6. Device Management System

Employees can register mobile devices for two app types:

| `app_type` | App | Limit Source |
|---|---|---|
| 1 | Collection App (Chit) | `chit_settings.chitCollectionEmpCount` |
| 2 | Estimation/Retail App | `ret_settings.estimation_app_devices_count` |

**Enable Flow (`enable_device()`):**
```
POST enable_device:
├─ Get $device_status (1=enable, 0=disable)
├─ IF app_type==1 AND device_status==1:
│    count = get_enabled_device_count()
│    IF count < chit_settings.chitCollectionEmpCount → allow
│    ELSE → block
├─ IF app_type==2 AND device_status==1:
│    count = get_enabled_device_count(1)  // estimation
│    IF count < ret_settings.estimation_app_devices_count → allow
│    ELSE → block
├─ Disabling always allowed
├─ update_emp_device_status($emp_dev_uuid, $device_status)
└─ redirect('employee')
```

**Device registration record:** Created by mobile app on first login (not via admin panel).

---

## 7. Employee Settings (Access Time + Discount Limits)

Each employee can have one `employee_settings` record configured by admin.

**Default values on employee creation:**
```php
'access_time_from' => '00:00:01'
'access_time_to'   => '23:59:58'
```
(Full-day access by default)

**Settings fields:**
| Field | Purpose |
|---|---|
| `disc_limit_type` | Discount type (fixed/percent) |
| `disc_limit` | Max discount allowed |
| `max_gold_tol` / `min_gold_tol` | Gold tolerance range |
| `max_silver_tol` / `min_silver_tol` | Silver tolerance range |
| `bulk_wast_disc_limit` | Bulk wastage discount limit |
| `dia_disc_limit` | Diamond discount limit |
| `allow_day_close` | Can close day? |
| `allow_manual_rate` | Can set manual rate? |
| `otp_dis_approval` | OTP required for discount approval? |
| `access_time_from/to` | Login time restriction window |

---

## 8. Known Risks

| Bug ID | Severity | Location | Description |
|---|---|---|---|
| EMP-BUG-001 | P1 | `admin_employee.php` L161 | `isUserAvailable()`: `print_r($available); exit;` — **debug statement never removed** — endpoint always terminates early, returns raw PHP output, real response (TRUE/FALSE) never reached |
| EMP-BUG-002 | P1 | `admin_employee.php` L568 | `emp_post('Add')`: `trans_status()` checked but `trans_begin()` was NEVER called — check is meaningless, always returns TRUE even on DB failure |
| EMP-BUG-003 | P1 | `employee_model.php` L498 | `update_employee()`: `WHERE id_employee='.$emp_id.'` — single quotes inside double quotes — literal string `'.$emp_id.'` never interpolated. Address lookup always returns all rows. |
| EMP-BUG-004 | P1 | `admin_employee.php` L1006-1007 | `employee_settings('updateAccessTimeAll')`: uses `$_POST['access_time_from']` and `$_POST['access_time_to']` directly — bypasses CI input sanitization |
| EMP-BUG-005 | P1 | `admin_employee.php` L1178 | `enable_device()`: uses `$_POST['enable_device']` directly — bypasses CI input |
| EMP-BUG-006 | P2 | `employee_model.php` L263 | `get_list_data()`: `$id_company` referenced but never assigned in this scope — PHP notice. `$is_multi_company` assigned but `$id_company` is undefined |
| EMP-BUG-007 | P2 | `employee_model.php` L771 | `updEmpAccessTime()`: raw SQL string interpolation — `access_time_from` and `access_time_to` from POST embedded directly without escaping — SQL injection |
| EMP-BUG-008 | P2 | `employee_model.php` L882 | `get_emp_by_company()`: all query strings use raw `$username` concatenation — SQL injection |
| EMP-BUG-009 | P2 | `employee_model.php` L958 | `get_employee_name_byid()`: `$emp_dev_id` embedded raw in SQL string — injection risk |
| EMP-BUG-010 | P2 | `admin_employee.php` L950 | `employee_status()`: flash message says `Employee status updated as true/false successfully` — `$status` is the return of `update_employee_only()` not the active value — shows "true" or "false" literally |
| EMP-BUG-011 | P2 | `admin_employee.php` L1334 | `emp_wallet_acc()`: `'issued_date' => date('y-m-d H:i:s')` — 2-digit year stored (same bug as customer zone) |
| EMP-BUG-012 | P2 | `employee_model.php` L33 | `__encrypt()` = `base64_encode()` — not real encryption. Legacy `passwd` field is trivially reversible |
| EMP-BUG-013 | P2 | `admin_employee.php` L886 | `set_image()`: `$data['emp_img'] = $filename` is commented out (`//$data['emp_img'] = $filename`) — employee image filename never written to DB |
| EMP-BUG-014 | P2 | `admin_employee.php` L864-866 | `set_image()`: `mkdir($img_path, 0777, TRUE)` — world-writable directory |
| EMP-BUG-015 | P2 | `admin_employee.php` L627 | `emp_post('Edit')`: `$pwd_check == true` check: `check_password()` compares raw input against stored `base64` — broken password change detection. If user types the base64 string, it is treated as "unchanged" |
| EMP-BUG-016 | P3 | `admin_employee.php` L711–741 | `emp_post('Delete')`: no dependency check — employee can be deleted even if they have active customers, payments, or assignments |
| EMP-BUG-017 | P3 | `employee_model.php` L867 | `ajax_emp_setting()`: `$id_employee` concatenated raw into SQL — SQLi risk (`and e.id_employee=$id_employee`) |

---

## 9. Cross-Module Dependencies

| External | Direction | Details |
|---|---|---|
| `Admin_settings_model` | Read | `get_access('employee')`, `settingsDB('get',1,'')` |
| `wallet_model` | Read/Write | `get_wallet_acc_number()`, `wallet_accountDB('insert')` — creates employee wallet |
| `log_model` | Write | `log_detail('insert', '', $log_data)` — audit on settings add/update |
| `ret_estimation_model` | Read | `get_ret_settings('estimation_app_devices_count')` — device limit for estimation app |
| `settings/company` (JS AJAX) | Read | Country, state, city dropdowns |
| `settings/profile` (JS AJAX) | Read | Profile/role dropdown |
| `chit_settings` (DB direct) | Read | `chitCollectionEmpCount`, `isOTPReqToLogin`, `loginOTP_exp` |
| `ret_settings` (DB direct) | Read | `estimation_app_devices_count`, `bulk_wastage_discount` |
| `ret_stone_discount_master` | Read | Diamond discount limits for settings |
| `employee` table | **Owned** | All CRUD |
| `address` table | **Owned** | Employee address CRUD |
| `employee_devices` | **Owned** | Device status management |
| `employee_settings` | **Owned** | Settings CRUD |
| `customer` module | Reads employee | `allocated_employee` FK — customers reference employees |
| Mobile/Collection app | Reads devices | App registers devices via API; admin enables/disables |
