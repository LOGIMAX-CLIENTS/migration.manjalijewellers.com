# Customer Module Brain

> **Module:** customer | **Controller:** `Admin_customer` | **Round:** 1  
> **Brain Built:** 2026-03-17 | **Status:** Round 1 Complete

---

## 1. Module Overview

**Purpose:** Complete customer lifecycle management — create, edit, view, delete, and manage customer records, KYC documents, wallet accounts, agent/employee allocations, and profile status. This is the central identity module; almost every other module (scheme, payment, billing) depends on the `customer` table.

### File Map

| File | Lines | Size | Purpose |
|---|---|---|---|
| `admin/application/controllers/admin_customer.php` | 2628 | 117 KB | Main controller — CRUD, image upload, KYC, sync, zone, allocations |
| `admin/application/models/customer_model.php` | 1254 | 62 KB | DB operations — customer, address, kyc, sync tables |
| `admin/assets/js/customer.js` | 4679+ | ~200 KB | All client-side logic — form validation, AJAX, image capture, zone management |
| `admin/application/views/master/customer/form.php` | ~3200 | 114 KB | Mega-form: personal, address, KYC, bank, images tabs |
| `admin/application/views/master/customer/list.php` | ~420 | 15 KB | Customer list with DataTable |
| `admin/application/views/master/customer/profile.php` | ~350 | 13 KB | Customer profile quick-update view |

### Connection Flow

```
Browser → customer.js
        → AJAX → Admin_customer controller
        → customer_model / admin_settings_model / wallet_model / log_model
        → DB: customer, address, kyc, wallet_account, customer_reg, scheme_account
        → Views: master/customer/form, list, profile
        → Browser
```

---

## 2. Constructor

```php
class Admin_customer extends CI_Controller
{
    const CUS_MODEL   = "customer_model";
    const ADM_MODEL   = "chitadmin_model";
    const SET_MODEL   = "admin_settings_model";
    const WALL_MODEL  = "wallet_model";
    const LOG_MODEL   = "log_model";
    const SMS_MODEL   = "admin_usersms_model";
    const MAIL_MODEL  = "email_model";
    const CUS_VIEW    = "master/customer/";
    // Image path constants
    const CUS_IMG_PATH   = 'assets/img/customer/';
    const KYC_PAN_PATH   = 'assets/kyc/pan/';
    const KYC_AADHAR_PATH= 'assets/kyc/aadhar/';
    const KYC_PB_PATH    = 'assets/kyc/pb/';
    const KYC_CH_PATH    = 'assets/kyc/ch/';
}
```

| Model Loaded | Alias | Purpose |
|---|---|---|
| `customer_model` | `$this->customer_model` | Primary — all customer DB ops |
| `chitadmin_model` | via const | Settings — multiple chit check |
| `admin_settings_model` | via const | Access control, service config, company |
| `wallet_model` | via const | Wallet account creation |
| `log_model` | via const | Audit log writing |
| `admin_usersms_model` | via const | SMS dispatch |
| `email_model` | via const | Email dispatch |
| `sms_model` | directly | SMS gateway (loaded separately) |

**Session Gate:** `is_logged` required → redirect to `/admin/login`. Access check: `admin_settings_model::get_access('customer')` → `view==0` → redirect dashboard.

---

## 3. Entry Points (Routes)

| URL | HTTP | Controller Method | Lines | Purpose |
|---|---|---|---|---|
| `/customer` / `/customer/index` | GET | `index()` | L44–47 | Customer list page |
| `/customer/cus_list` | GET | `cus_list($msg)` | L93–106 | Customer list view load |
| `/customer/cus_form/Add` | GET | `cus_form('Add')` | L150–392 | Add customer form |
| `/customer/cus_form/Edit/{id}` | GET | `cus_form('Edit', $id)` | L190–392 | Edit customer form |
| `/customer/cus_post/Add` | POST | `cus_post('Add')` | L419–1553 | Save new customer |
| `/customer/cus_post/Edit/{id}` | POST | `cus_post('Edit', $id)` | L1060–1553 | Update existing customer |
| `/customer/cus_profile/list` | GET | `cus_profile('list')` | L1858–1870 | Profile quick-update view |
| `/customer/cus_profile/edit` | POST | `cus_profile('edit')` | L1866–1869 | Search customer for profile |
| `/customer/cus_profile/update/{id}` | POST | `cus_profile('update', $id)` | L1870–1921 | Profile quick-update save |
| `/customer/ajax_customers` | POST | `ajax_customers()` | L126–143 | Customer list AJAX (DataTable) |
| `/customer/ajax_get_customer/{id}` | GET | `ajax_get_customer($id)` | L68–92 | Single customer AJAX |
| `/customer/ajax_get_customers` | GET | `ajax_get_customers()` | L54–66 | Customer dropdown (scheme join) |
| `/customer/ajax_check_delete/{id}` | GET | `ajax_check_delete($id)` | L108–125 | Pre-delete dependency check |
| `/customer/profile_status/{status}/{id}` | GET | `profile_status($status, $id)` | L1733–1744 | Toggle profile_complete flag |
| `/customer/customer_status/{status}/{id}` | GET | `customer_status($status, $id)` | L1745–1756 | Toggle customer active flag |
| `/customer/check_username/{username}` | GET | `check_username()` | L1554–1562 | Username availability check |
| `/customer/check_mobile` | POST | `check_mobile()` | L1563–1578 | Mobile availability check |
| `/customer/check_email` | POST | `check_email()` | L1579–1594 | Email availability check |
| `/customer/download/{id}/{file}` | GET | `download($id,$file)` | L1758–1767 | Download KYC document |
| `/customer/get_customer_by_mobile` | POST | `get_customer_by_mobile()` | L1836–1842 | Mobile lookup |
| `/customer/ajax_get_customers_list` | POST | `ajax_get_customers_list()` | L1843–1851 | Customer search (typeahead) |
| `/customer/ajax_get_village` | POST | `ajax_get_village()` | L1852–1857 | Village list |
| `/customer/ajax_get_scheme_account_list` | POST | `ajax_get_scheme_account_list()` | L1975–1983 | Scheme account lookup |
| `/customer/ajax_getAllActiveAgents` | GET | `ajax_getAllActiveAgents()` | L1924–1929 | Agent dropdown |
| `/customer/allocate_agent_toCuctomers` | POST | `allocate_agent_toCuctomers()` | L1930–1958 | Bulk agent allocation |
| `/customer/ajax_getAllActiveEmployee` | GET | `ajax_getAllActiveEmployee()` | L2564–2569 | Employee dropdown |
| `/customer/allocate_employee_toCuctomers` | POST | `allocate_employee_toCuctomers()` | L2570–2610 | Bulk employee allocation |
| `/customer/getkycdata_byid` | POST | `getkycdata_byid()` | L2477–2484 | KYC data by ID |
| `/customer/zone/{type}/{id}/{status}` | GET/POST | `zone($type, $id, $status)` | L2485–2563 | Zone CRUD |
| `/customer/wallet_account_cus` | GET/POST | `wallet_account_cus()` | L2611–2628 | Wallet account view/create |
| `/customer/format_accRcptNo/{type}/{id}` | GET | `format_accRcptNo()` | L1984–1988 | Account/receipt number format |

---

## 4. Key Tables

| Table | Role | Key Columns |
|---|---|---|
| `customer` | Primary — all customer identity | `id_customer`, `mobile`, `pan`, `aadharid`, `active`, `added_by`, `id_branch`, `id_agent`, `allocated_employee`, `profile_complete`, `cus_type` |
| `address` | Customer address 1:1 | `id_address`, `id_customer`, `address1-3`, `pincode`, `id_country`, `id_state`, `id_city` |
| `kyc` | KYC documents (multi-type per customer) | `id_kyc`, `id_customer`, `kyc_type` (1=bank,2=pan,3=aadhar), `number`, `img_url`, `back_img_url`, `document_url`, `status`, `verification_type` |
| `wallet_account` | Customer wallet (inter-wallet) | `id_wallet_account`, `id_customer`, `wallet_acc_number`, `available_points` |
| `scheme_account` | Customer chit scheme memberships | `id_scheme_account`, `id_customer`, `active`, `is_closed` |
| `customer_reg` | Offline/ERP sync staging | `clientid`, `mobile`, `id_branch`, `is_registered_online`, `is_transferred`, `record_to` |
| `transaction` | ERP payment sync staging | `id_transaction`, `client_id`, `is_transferred`, `record_to`, `is_modified` |
| `agent` | Agent reference | `id_agent`, `firstname`, `agent_code` |
| `employee` | Employee reference | `id_employee`, `firstname`, `emp_code` |
| `branch` | Branch reference | `id_branch`, `name`, `short_name` |
| `village` | Village/area reference | `id_village`, `village_name`, `post_office`, `taluk` |
| `zone` | Zone management (admin_customer handles this) | `id_zone`, `zone_name` |

---

## 5. Known Risks

| Risk | Severity | Location | Description |
|---|---|---|---|
| `added_by = 1` hardcoded | P2 | `cus_post()` L481 | `added_by` always set to `1` (Admin) even during add. Should be dynamic |
| `getImageData()` defined 3× inside methods | P2 | L593, L1161, L1454 | PHP function redefinition — will fatal if both Add and Edit paths hit in same request (unlikely but wrong) |
| `allocate_agent_toCuctomers()` bug | P1 | L1936 | `$total = count($cus)` immediately overwritten by `$total = array()` — the count is lost. `if ($total > 0)` is always `false` (empty array) → **agent allocation always silently fails** |
| `get_customer()` hardcoded ID in subquery | P2 | model L149 | `SELECT count(...) where id_customer=1` — hardcoded `1`, not the actual `$id`. All customers show scheme count of customer 1 |
| `Searchcustomer()` SQL injection | P1 | model L546 | `WHERE username like '%{$SearchTxt}%'` — raw string interpolation. User can break SQL via search field |
| `ajax_get_customers()` SQL injection | P1 | model L139 | `c.firstname LIKE '$param%'` — raw param injection (no binding) |
| `isCustomerExist()` SQL injection | P1 | model L581 | `WHERE mobile={$mobile}` — unquoted, unbound |
| `delete_customer()` incomplete cleanup | P1 | model L353–363 | Deletes from `customer`, `address`, `wallet_account` but NOT from `kyc`, `scheme_account` (if guard fails), `customer_reg`. Orphan records possible |
| `0777` directory permissions | P2 | Multiple store_KycImages/get_kyc_images | All `mkdir()` calls use 0777 — world-writable on Linux |
| Password encryption = base64 | P2 | model L15–20 | `__encrypt` = `base64_encode()`. Not actual encryption — easily reversed. Passwords stored in base64 |
| Profile update (`cus_profile/update`) | P2 | L1899–1920 | `trans_begin()` used but no explicit `trans_rollback()` on failure path — relies on CI auto-rollback |
| `download()` no path validation | P1 | L1758–1767 | `$id` and `$file` passed direct to `file_get_contents` — a crafted URL like `/customer/download/../../config/database` could read arbitrary files |

---

## 6. Data Flow Summary

See `DATA_FLOW.md` for full traces of:
1. **CREATE Customer** — trans_begin → insert customer+address → sync_existing_data → wallet_account_create → KYC images → commit
2. **EDIT Customer** — get_cust → render form → POST → trans_begin → update_customer + address → KYC update → commit
3. **DELETE Customer** — ajax_check_delete (dependency guard) → delete address/customer/wallet_account
4. **KYC Upload** — base64 decode → mkdir 0777 → file_put_contents → URL stored in kyc table
5. **Agent/Employee Bulk Allocation** — POST id_customer[] + id_agent → loop allocate_agent

---

## 7. Cross-Module Dependencies

| External | Direction | Details |
|---|---|---|
| `chitadmin_model` | Read | `allow_multiple_chit()` — controls customer dropdown filter for scheme join |
| `admin_settings_model` | Read | `get_access()`, `settingsDB()`, `limitDB()`, `get_service()`, `get_company()` |
| `wallet_model` | Write | `get_wallet_acc_number()`, `wallet_accountDB()` — creates wallet on customer add |
| `log_model` | Write | `log_detail()` — audit trail on edit/update |
| `admin_usersms_model` | Write | `get_SMS_data()` — wallet creation SMS |
| `email_model` | Write | `send_email()` — wallet creation email |
| `sms_model` | Write | `sendSMS_*()` — multi-gateway SMS dispatch |
| `scheme_account` table | Read | Check dependency before delete; sync existing data |
| `customer_reg` table | Read/Write | Sync staging for offline/ERP data |
| `transaction` table | Read/Write | ERP payment sync |
| `ret_billing`, `customerorder`, `ret_estimation`, `gift_card` | Read | dependency checks before delete |
| `admin_ret_estimation` controller | Cross-JS | Village lookup AJAX calls |
| `settings/company` controller | Cross-JS | Country/state/city/profession dropdowns |

---

## 8. Anti-Patterns Register

*(Updated after each bug fix)*

| Pattern | Occurrences | Description |
|---|---|---|
| Raw SQL string interpolation | ≥8 | `LIKE '$param%'`, `WHERE id=$id` — no CI query binding |
| `added_by = 1` hardcoded | 1 | Customer source always logged as "Admin" |
| `getImageData()` defined inside method body | 3 | PHP function redefinition risk |
| `mkdir(0777)` | 8+ | World-writable directories on all KYC uploads |
| `base64_encode` as password | 1 | Not actual encryption — trivially reversible |
| GET routes for state change | 2+ | `profile_status`, `customer_status` use GET — CSRF vulnerable |
