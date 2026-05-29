# Chit Settings — Module Brain
> **Module**: System Configuration & Master Data Management  
> **Controller**: `admin_settings.php` (4,709 lines — 120 methods)  
> **Model**: `admin_settings_model.php` (2,866 lines — 178 methods)  
> **Round**: 1 | **Date**: 2026-03-06  

---

## 1. Module Overview

The Settings module is the **central nervous system** of the application — it manages ALL global configuration, master data, user permissions, and system-wide settings. This is the largest controller in the codebase. It covers:

### Functional Groups (16 domains)
1. **General Settings** — `chit_settings` table: global app behavior (~60+ flags)
2. **Menu & Permissions** — `menu`, `access`, `profile`, `dashboard_menu`, `dashboard_access`
3. **Profile Management** — User role profiles with feature toggles
4. **Metal Rates** — `metal_rates`, `branch_rate`: rate CRUD with discount logic
5. **Bank Master** — `bank`: bank CRUD with duplicate detection
6. **Drawee Master** — `drawee_account`: bank account CRUD
7. **Payment Mode** — `payment_mode`: payment method CRUD
8. **Weight Master** — `weight`: weight slab CRUD
9. **Classification** — `sch_classify`: scheme classification CRUD
10. **Department** — `department`: employee department CRUD
11. **Designation** — `designation`: employee designation CRUD
12. **Company** — `company`: company info CRUD
13. **Branch** — `branch`: branch CRUD with settings
14. **Offers & Arrivals** — promotions management
15. **Gateway/SMS/Mail** — payment gateway, SMS API, email config
16. **System Utilities** — DB backup, clear database, village, country/state/city

---

## 2. File Map

| File | Lines | Purpose |
|---|---|---|
| `admin/application/controllers/admin_settings.php` | 4,709 | Mega-controller: all settings & masters |
| `admin/application/models/admin_settings_model.php` | 2,866 | All DB operations for settings |
| `admin/application/views/settings/` | 8 dirs + 8 files | Forms and lists for all settings |
| `admin/application/views/settings/general/` | — | General settings forms |
| `admin/application/views/settings/menu/` | — | Menu & permission views |
| `admin/application/views/settings/profile/` | — | Profile management views |
| `admin/application/views/settings/company.php` | 15 KB | Company details form |
| `admin/application/views/settings/gateway_list.php` | 17 KB | Payment gateway list |

---

## 3. Constructor & Session Dependencies

```php
// Models loaded (7)
const SET_MODEL = "admin_settings_model";
const SCH_MODEL = "scheme_model";
const CUS_MODEL = "customer_model";
const ACC_MODEL = "account_model";
const PAY_MODEL = "payment_model";
const ADM_MODEL = "chitadmin_model";
const LOG_MODEL = "log_model";
const MODEL = "admin_usersms_model";

// Libraries loaded
- excel (PHPExcel)
- form_validation

// Session data used
- is_logged, uid (id_employee), profile
- branch_settings, branchWiseLogin, filerbybranch (usertype)
- id_branch, id_log
```

---

## 4. Route Map (70+ routes — key groups)

### General Settings
| Route | Controller Method | Purpose |
|---|---|---|
| `settings/general/list` | `general_settings/List` | View settings table |
| `settings/general/edit/:id` | `general_settings/View/$1` | Edit general settings form |
| `settings/general/save` | `general_settings/Save` | Save new settings |
| `settings/general/update/:id` | `general_settings/Update/$1` | Update settings |

### Menu & Permissions
| Route | Controller Method | Purpose |
|---|---|---|
| `settings/menu/list` | `menu/List` | Menu item list |
| `settings/menu/add → save → edit → update → delete` | `menu/View,Save,Update,Delete` | Menu CRUD |

### Profile
| Route | Controller Method | Purpose |
|---|---|---|
| `settings/profile/list` | — | Profile list |
| CRUD via profile() | — | Profile management |

### Metal Rates
| Route | Controller Method | Purpose |
|---|---|---|
| `settings/rate/list` | `metal_rates/List` | Rate list |
| `settings/rate/add → save → edit → update → delete` | `metal_rates/...` | Rate CRUD |
| `settings/rate/discount` | `metal_rates_discount()` | Rate discounts |

### Master Data (Bank / Drawee / PayMode / Gift / Branch)
| Route Pattern | Entity |
|---|---|
| `settings/bank/*` | Bank master CRUD |
| `settings/drawee/*` | Drawee account CRUD |
| `settings/paymode/*` | Payment mode CRUD |
| `settings/gift/*` | Gift master CRUD |
| `settings/branch*` | Branch CRUD |
| `settings/notification/*` | Notification management |

---

## 5. Key Tables

| Table | Role | Owner |
|---|---|---|
| `chit_settings` | Global app settings (singleton, id=1) | ✅ This module |
| `menu` | Menu hierarchy | ✅ This module |
| `access` | Profile → menu permission map | ✅ This module |
| `dashboard_menu` | Dashboard menu hierarchy | ✅ This module |
| `dashboard_access` | Dashboard profile permissions | ✅ This module |
| `profile` | User role profiles | ✅ This module |
| `metal_rates` | Metal rate history | ✅ This module |
| `branch_rate` | Branch-specific rate mapping | ✅ This module |
| `bank` | Bank master | ✅ This module |
| `drawee_account` | Bank drawee accounts | ✅ This module |
| `payment_mode` | Payment modes | ✅ This module |
| `weight` | Weight slabs | ✅ This module |
| `sch_classify` | Scheme classifications | ✅ This module |
| `department` | Employee departments | ✅ This module |
| `designation` | Employee designations | ✅ This module |
| `company` | Company info | ✅ This module |
| `branch` | Branch master | ✅ This module |
| `db_backup` | Backup log | ✅ This module |
| `country` / `state` / `city` | Geography | ✅ This module |
| `village` | Village master | ✅ This module |
| `offers` / `new_arrivals` | Promotions | ✅ This module |
| `gift` | Gift master | ✅ This module |
| `sms_api_settings` | SMS gateway config | ✅ This module |
| `gateway_settings` | Payment gateway config | ✅ This module |
| `notification` | Notification template | ✅ This module |
| `config_settings` | Additional config | ✅ This module |
| `payment_charges` | Fee structure | ✅ This module |
| `terms_and_conditions` | T&C management | ✅ This module |
| `version_details` | App version tracking | ✅ This module |
| `ledger` | Ledger accounts | ✅ This module |
| `profession` | Profession master | ✅ This module |

---

## 6. Known Risks & Bugs

| # | Risk | Severity | Location |
|---|---|---|---|
| 1 | **SQL Injection** — Multiple raw `$id` in WHERE clauses | 🔴 HIGH | Model L58, L103, L123, L166, L199+, L269+, L370+, L410+, L478+, L527+ |
| 2 | **`clear_database()` uses raw `$_POST`** | 🔴 HIGH | Controller L2151 |
| 3 | **`clear_database()` TRUNCATES production tables** | 🔴 CRITICAL | Controller L2147-2209 |
| 4 | **Delete via GET** — Multiple delete operations exposed as GET | 🟡 MED | Multiple routes |
| 5 | **`file_put_contents('../api/rate.txt')` with raw data** | 🔴 HIGH | Controller L570 |
| 6 | **No CSRF protection** on `clear_database()` | 🔴 CRITICAL | Controller L2147 |
| 7 | **Email/password in plain text** — mail_settings stores plain password | 🟡 MED | Controller L2378 |
| 8 | **Hardcoded URLs** in commented code — old production URLs visible | 🟡 LOW | Controller L670-673 |
| 9 | **`$general[tab_name]` — undefined constant** used as array key | 🟡 MED | Controller L1957, L2108 |
| 10 | **Menu generation builds HTML in model** | 🟡 LOW | Model L61-80 |
| 11 | **DB backup creates ZIP with no auth** | 🟡 MED | Controller L2232-2270 |
| 12 | **`rrmdir()` recursively deletes directories** | 🔴 HIGH | Controller L2219-2231 |
| 13 | **No transaction in general_settings Save** | 🟡 MED | Controller L1834-1969 |

---

## 7. Anti-Patterns Register

| # | Pattern | Occurrences | Risk |
|---|---|---|---|
| 1 | Raw SQL with string concat | 50+ queries | SQL injection |
| 2 | Mega-controller (4,709 lines, 120 methods) | 1 file | Maintenance |
| 3 | Mega-model (2,866 lines, 178 methods) | 1 file | Maintenance |
| 4 | Switch-based multiplexing for CRUD | Every master entity | Complex routing |
| 5 | HTML generation in model | menu_generation() | Separation of concerns |
| 6 | Commented-out production URLs/code | ~200 lines | Code quality |
| 7 | Undefined constants used as keys | `tab_name` | Warnings |
| 8 | No input validation on system operations | clear_database, truncate | Data loss |
