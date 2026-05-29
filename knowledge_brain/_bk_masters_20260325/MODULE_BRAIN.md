# Masters / Admin Settings Module Brain

> **Module:** `masters` | **Controller:** `Admin_settings` | **Brain Built:** 2026-03-17  
> **Status:** Round 1 Complete (~100% coverage)

---

## 1. Module Overview

**Purpose:** This is the **largest and most central module** in the codebase. It manages every system-wide configuration master — company/branch settings, profile/permissions, metal rates, notifications, payment gateways, SMS/email APIs, villages, wallets, discount settings, chit scheme masters, retail product masters, and a full DB backup/clear facility. It is also the **RBAC engine** — providing `get_access()` used by ALL other modules.

### Scale

| Metric | Value |
|---|---|
| Controller lines | 4709 (270 KB) |
| Model lines | 2885 (129 KB) |
| JS lines | 6322 (271 KB) |
| Controller methods | 120 |
| Model methods | 178 |
| JS AJAX endpoints | 87 |
| View files | 150 |
| View subdirectories (entities) | 78 |

---

## 2. Constructor

```php
class Admin_settings extends CI_Controller
{
    const VIEW_FOLDER   = 'settings/';
    const MAS_VIEW      = 'master/';
    const SET_MODEL     = "admin_settings_model";
    const SCH_MODEL     = "scheme_model";
    const CUS_MODEL     = "customer_model";
    const ACC_MODEL     = "account_model";
    const PAY_MODEL     = "payment_model";
    const ADM_MODEL     = 'chitadmin_model';
    const LOG_MODEL     = "log_model";
    const MODEL         = "admin_usersms_model";
    const SETT_MOD      = "admin_settings_model";  // ← duplicate of SET_MODEL
}
```

**Models Loaded in Constructor:**
- `admin_usersms_model` — SMS notification dispatch
- `admin_settings_model` (×2 — via const SET_MODEL and SETT_MOD) — main model
- `customer_model` — used by `general_settings` for counts
- `scheme_model` — scheme counts in general settings
- `account_model` — scheme account counts
- `chitadmin_model` — utilities
- `log_model` — audit logging

**Session Variables Captured:**
```php
$this->id_employee       = session->uid
$this->branch_settings   = session->branch_settings
$this->branchWiseLogin   = session->branchWiseLogin
$this->usertype          = session->filerbybranch
$this->id_log            = session->id_log
```

**Libraries Loaded:** `excel` (PHPExcel for export), `form_validation`  
**Auth:** `is_logged` session gate → redirect to `admin/login`

---

## 3. Module Architecture — Entity Groups

The module manages **78 distinct master entities** via a consistent `switch($type)` pattern. Each entity follows this URL convention:

```
GET  /settings/{entity}/List          → list view
GET  /settings/{entity}/View          → form view (add)
GET  /settings/{entity}/View/{id}     → form view (edit)
POST /settings/{entity}/Save          → insert
POST /settings/{entity}/Update/{id}   → update
GET  /settings/{entity}/Delete/{id}   → delete
     /settings/{entity}               → AJAX data (default case)
```

### Entity Group Map (controller method → URL prefix)

| Controller Method | URL | Entity Type |
|---|---|---|
| `menu()` | `settings/menu` | Navigation menu CRUD |
| `profile()` | `settings/profile` | User profile/role management |
| `permission()` | `settings/permission` | RBAC permission matrix |
| `bank()` | `settings/bank` | Bank master |
| `metal_rates()` | `settings/rate` | Metal rate entry (22ct, 18ct, 14ct, silver, platinum) |
| `general_settings()` | `settings/general` | Core chit system settings |
| `config_setting()` | `settings/config` | Config table management |
| `branch_form()` | `settings/branch` | Branch CRUD (complex — image + metal rate) |
| `gateway_settings()` | `settings/gateway` | Payment gateway config (Cashfree, HDFC, Tech) |
| `gateway_form()` | `settings/payment_gateway` | Gateway form (Retail module) |
| `sms_api_settings()` | `settings/sms_api` | SMS API configuration |
| `mail_settings()` | `settings/mail` | Email SMTP settings |
| `limit_settings()` | `settings/limit` | Customer/scheme limits |
| `discount_settings()` | `settings/discount` | Discount settings |
| `offers_form()` | `settings/offers` | Promotional offers CRUD |
| `new_arrivals_form()` | `settings/new_arrivals` | New arrivals CRUD |
| `village_form()` | `settings/village` | Village master CRUD |
| `village_list()` | (direct) | Village list view |
| `gift()` | `settings/gift` | Gift master CRUD |
| `profession_form()` | `settings/profession` | Profession master CRUD |
| `version_details()` | `settings/version` | App version management |
| `terms_conditions()` | `settings/terms` | Terms & conditions CRUD |
| `notification()` | `settings/notification` | Push notification management |
| `quick_link()` | (direct) | Quick link activation |
| `ledger()` | `settings/ledger` | Ledger master |
| `promotioncredit__settings()` | `settings/promotion_credit` | Promotion credit settings |
| `promotion_api_settings()` | `settings/promotion_api` | Promotion SMS API |
| `otpcredit_settings()` | `settings/otp_credit` | OTP credit settings |
| `payment_charges()` | `settings/charges` | Payment charges |
| `clear_database()` | `settings/clear_database` | ⚠️ TRUNCATE all chit tables |
| `db_backup()` | `settings/db_backup` | Database backup |
| `branch_settings()` | `settings/branch_settings` | Per-branch config |
| `schemeacc_no_settings()` | `settings/schemeacc_no` | Scheme account number format |
| `matal_ratelist()` | (direct) | Metal rate list per branch |
| `send_RatesToAllUsers()` | (internal) | Push metal rates via OneSignal |

---

## 4. RBAC Engine — `get_access()`

This is one of the most critical model methods in the entire system, used by **every module** to check permissions:

```php
// admin_settings_model.php L96–105
function get_access($url) {
    $sql = "Select a.id_profile,a.id_menu,a.view,a.add,a.edit,a.delete,p.allow_acc_closing
            From access a
            Left Join menu m On(a.id_menu=m.id_menu)
            Left Join profile p On(p.id_profile=a.id_profile)
            where a.id_profile=" . $this->session->userdata('profile') .
            " and m.link='" . $url . "'";
    return $this->db->query($sql)->row_array();
}
```

**⚠️ CRITICAL: `$url` is NEVER escaped.** This is directly concatenated:
- `a.id_profile=` uses session data (safe)
- `m.link='$url'` — if any calling code ever passes user-controlled input as `$url`, this is a SQL injection in the access control engine. Currently callers use hardcoded strings, but this is a latent risk.

**Permission tables:**
- `menu` — navigation items
- `access` — menu-level RBAC (view/add/edit/delete per profile)
- `dashboard_access` — dashboard widget RBAC
- `profile` — role definitions with feature flags

---

## 5. Metal Rate Architecture

Metal rates are used by multiple downstream modules. The Save flow is complex:

```
POST /settings/rate/Save:
1. Read $metal POST data
2. Read discount_settings (enableGoldrateDisc, goldDiscAmt, enableSilver_rateDisc, silverDiscAmt)
3. Compute stored rates:
   - goldrate_22ct = mjdmagoldrate_22ct - goldDiscAmt  (if disc enabled)
   - goldrate_18ct = market_gold_18ct - goldDiscAmt_18k (if disc enabled)
   - silverrate_1gm = mjdmasilverrate_1gm - silverDiscAmt (if disc enabled)
4. INSERT metal_rates (full rate record)
5. IF branch_settings==1 AND is_branchwise_rate==1:
   INSERT branch_rate (one row per branch_id, linked to new metal_rates record)
6. file_put_contents('../api/rate.txt', $rate_array)  // ← update flat file cache
7. IF canSendNoti(1): send_RatesToAllUsers($branch_ids) → OneSignal push
```

**Key Tables:** `metal_rates`, `branch_rate`  
**Flat file:** `../api/rate.txt` — JSON cache for mobile API consumption  
**Bug at L570:** `file_put_contents('../api/rate.txt', $rate_array)` — `$rate_array` is a PHP array, not JSON. `file_put_contents` will call `$rate_array->__toString()` → writes `"Array"`. Should be `json_encode($rate_array)`.

---

## 6. Critical Danger Zone — `clear_database()`

**Lines:** L2147–2218

```php
function clear_database() {
    $truncate = array(
        'scheme_account', 'customer_reg', 'customer', 'address', 'kyc',
        'payment', 'receipt', 'transaction', 'wallet_account', ...
    );
    // ...
    foreach ($truncate as $selected) {
        $this->truncateFromArray($selected);  // ← TRUNCATE ALL
    }
}
```

**This is a catastrophic function:**
- Truncates ALL core transactional tables with NO confirmation gate
- No session role check — any logged-in user can hit `/settings/clear_database`
- No `trans_begin` — if web server dies mid-truncate, partial state is permanent
- Appears to exist for "demo reset" purposes
- **Risk Level: CATASTROPHIC** if reachable in production by any employee

---

## 7. General Settings — `settingsDB()`

`chit_settings` is the single-row configuration table (`id_chit_settings = 1`) that controls the entire system. Key fields:

| Field | Purpose |
|---|---|
| `integrationType` | ERP integration mode |
| `autoSyncExisting` | Auto-sync customers to ERP |
| `chitCollectionEmpCount` | Max devices for collection app |
| `isOTPReqToLogin` | Require OTP for employee login |
| `loginOTP_exp` | OTP expiry time (seconds) |
| `sms_gateway` | Selected SMS gateway |
| `enableGoldrateDisc` / `goldDiscAmt` | Gold rate discount |
| `enableSilver_rateDisc` / `silverDiscAmt` | Silver rate discount |
| `enableGoldrateDisc_18k` / `goldDiscAmt_18k` | 18ct gold discount |
| `emp_wallet_account_type` | Whether employee gets wallet account |
| `schemeaccNo_displayFrmt` | Account number display format |
| `branch_settings` | Multi-branch mode flag |

---

## 8. Branch Management

`branch_form()` is one of the most complex controller methods (L2984–3132):

```
GET /settings/branch/View: → render branch form
POST /settings/branch/Save:
  → INSERT branch record
  → IF logo uploaded: set__branch_image($id)  → mkdir(0777), save image
  → INSERT metal_rate_settings (default)
  → INSERT chit_settings clone for branch
  → INSERT employee_settings for admin user on new branch

POST /settings/branch/Update/{id}:
  → UPDATE branch
  → IF logo: set__branch_image($id)
  → Update linked metal rate, city/country associations
```

**Branch-wise customization:** Each branch can override rate display settings, account format, and chit settings via `branch_settings` table.

---

## 9. Known Risks (Quick Reference)

| Bug ID | Severity | Location | Description |
|---|---|---|---|
| MST-BUG-001 | P0 | Controller L2147 | `clear_database()`: No auth check, no confirmaton — any logged employee can truncate ALL data |
| MST-BUG-002 | P1 | Model L103 | `get_access()`: `$url` embedded raw in SQL — SQL injection in the RBAC engine |
| MST-BUG-003 | P1 | Controller L570 | `metal_rates('Save')`: `file_put_contents('../api/rate.txt', $rate_array)` — writes array not JSON → mobile API gets "Array" |
| MST-BUG-004 | P1 | Controller L4029 | `get_gift_name_byId()`: `$id = $_POST['id']` — raw POST bypass |
| MST-BUG-005 | P1 | Controller L4264/4265 | `ajax_get_version()`: raw `$_POST['from_date']` and `$_POST['to_date']` |
| MST-BUG-006 | P1 | Controller L1054–1071 | `getstate`/`getcity`/`getvillage` handlers: raw `$_POST` values passed directly to model |
| MST-BUG-007 | P2 | Model L22–25 | `get_branch_rate()`: raw `$id_branch` concatenated in SQL |
| MST-BUG-008 | P2 | Model L1022 | `max_metalrate()`: raw `$id_branch` in SQL |
| MST-BUG-009 | P2 | Model L1158 | `settingsDB()`: `$settings` embedded raw — `WHERE settings='$settings'` |
| MST-BUG-010 | P2 | Model L2167 | `getBranchId()`: `$warehouse` embedded raw in SQL |
| MST-BUG-011 | P2 | Model L2697 | `get_version_data()`: `$version_no` embedded raw |
| MST-BUG-012 | P2 | Model L24 | `get_branch_rate()`: `$id_branch` could be 0 or '' → wrong row returned |
| MST-BUG-013 | P2 | Controller L8 | `const SET_MODEL` and `const SETT_MOD` both = `"admin_settings_model"` — redundant constant, loaded twice in constructor |
| MST-BUG-014 | P2 | Controller, 7× | `mkdir($path, 0777, TRUE)` — world-writable dirs for offer/classification/gateway images |
| MST-BUG-015 | P2 | Model L58 | `menu_generation()`: `$id_profile` concatenated raw in SQL |
| MST-BUG-016 | P2 | Model L200 | `PermissionDB()`: SQL uses hardcoded menu IDs (17, 18) — fragile coupling |
| MST-BUG-017 | P3 | JS | 61 of 87 AJAX calls have no `error:` handler |
| MST-BUG-018 | P3 | JS | 72 `console.log` statements in production JS |
| MST-BUG-019 | P3 | Controller L5 | `const VIEW_FOLDER = 'settings/'` unused — all views use `self::MAS_VIEW` |
| MST-BUG-020 | P3 | Controller (commented code) | Large blocks of dead code (URLs to competitor sites in commented `mjdma_update` case L655–703) |

---

## 10. Cross-Module Dependencies

| Dependency Direction | Module | How Used |
|---|---|---|
| **Provides TO all modules** | `get_access($url)` | RBAC — every module calls this |
| **Provides TO all modules** | `settingsDB('get')` | System configuration |
| **Provides TO customer** | `village_settingDB()` | Village dropdown |
| **Provides TO payment** | `gateway_settingsDB()` | Payment gateway credentials |
| **Provides TO employee** | `profile` table | Role definitions |
| **Provides TO mobile API** | `../api/rate.txt` | Metal rate flat file cache |
| **Reads FROM** | `customer_model` | Customer count in general settings |
| **Reads FROM** | `scheme_model` | Scheme count in general settings |
| **Reads FROM** | `account_model` | Scheme account count |
| **Writes TO** | OneSignal API | Push notifications via `send_RatesToAllUsers()` |
| **Writes TO** | SMS gateway | Rate notifications via `admin_usersms_model` |
| **Reads FROM** | `chitadmin_model` | ChitAdmin utilities |

### Shared Tables (Owned by this module, read by others)

| Table | Owned By | Read By |
|---|---|---|
| `chit_settings` | masters | ALL modules — single point of truth |
| `metal_rates` | masters | payment, account, customer |
| `branch` | masters | ALL modules |
| `profile` | masters | employee, login |
| `access` | masters | ALL modules (via get_access) |
| `menu` | masters | login (menu generation) |
| `bank` | masters | payment, customer KYC |
| `payment_mode` | masters | payment |
| `village` | masters | customer, collections |
| `general` | masters | general settings display |
| `offers` / `new_arrivals` | masters | mobile API |

---

## 11. File System Operations

| Location | Operation | Risk |
|---|---|---|
| `../api/rate.txt` | Write JSON (actually writes Array — bug) | Wrong data to mobile API |
| `assets/img/offers/{id}/` | mkdir 0777 + image save | World-writable |
| `assets/img/new_arrivals/{id}/` | mkdir 0777 + image save | World-writable |
| `assets/img/sch_classify/{id}/` | mkdir 0777 + image save | World-writable |
| `assets/img/payment_gateway/{id}/` | mkdir 0777 + image save | World-writable |
| `assets/img/branch/{id}/` | mkdir 0777 + image save | World-writable |
| `backups/{datetime}/` | mkdir 0777 + mysqldump | Backup stored in webroot |

**DB Backup is stored in webroot** (`backups/` within public directory) — backup files are downloadable by anyone who knows the path.
