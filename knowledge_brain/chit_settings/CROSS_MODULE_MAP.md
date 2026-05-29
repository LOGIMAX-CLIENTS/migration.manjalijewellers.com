# Chit Settings Module — Cross-Module Map
> **Round**: R2-Upgrade | **Date**: 2026-03-25

---

## 1. Module Dependencies (Outbound — Models loaded by this controller)

| Module | Model Used | Purpose |
|---|---|---|
| **Scheme** | `scheme_model` | scheme_count(), update_scheme_free_payment() |
| **Customer** | `customer_model` | customer_count(), getFormatFromDB() |
| **Account** | `account_model` | sch_acc_count() |
| **Payment** | `payment_model` | get_metalrate_by_branch(), **get_active_scheme()** (for KYC rules — NEW) |
| **Chit Admin** | `chitadmin_model` | Admin operations |
| **SMS** | `admin_usersms_model` | Notification/SMS operations |
| **Log** | `log_model` | Audit trail |

---

## 2. Module Dependencies (Inbound — Other modules reading settings)

| Calling Module | What It Reads | Method | Table |
|---|---|---|---|
| **Scheme** | Limits, discounts, GST | `limitDB()`, `discount_db()`, `get_gstsettings()` | chit_settings |
| **Account** | General settings, access | `settingsDB()`, `get_access()` | chit_settings, access |
| **Payment** | Metal rates, access | `metal_ratesDB()`, `get_access()` | metal_rates, access |
| **Admin Login** | Permissions, menu | `menu_generation()`, `PermissionDB()` | menu, access, profile |
| **Mobile API** | Settings, rates | `settingsDB()`, `metal_ratesDB()` | chit_settings, metal_rates |
| **ALL Controllers** | Access control | `get_access($url)` | access, menu, profile |

---

## 3. Owned Tables (30+)

| Table | Purpose | CRUD Methods |
|---|---|---|
| `chit_settings` | Global 60+ flag singleton | settingsDB, limitDB, discount_db |
| `config_settings` | Additional config | configDB |
| `menu` | Menu hierarchy | menuDB |
| `access` | Profile→menu permission | PermissionDB |
| `dashboard_menu` | Dashboard menu | DashboardPermissionDB |
| `dashboard_access` | Dashboard permissions | DashboardPermissionDB |
| `profile` | User role profiles | profileDB |
| `metal_rates` | Rate history | metal_ratesDB |
| `branch_rate` | Branch rate mapping | insert_metalrate |
| `bank` | Bank master | bankDB |
| `drawee_account` | Drawee accounts | draweeDB |
| `payment_mode` | Payment methods | paymodeDB |
| `weight` | Weight slabs | weight CRUD |
| `sch_classify` | Scheme classifications | classification CRUD |
| `department` | Departments | dept CRUD |
| `designation` | Designations | design CRUD |
| `company` | Company info | get/update_company |
| `branch` | Branch master | branchDB |
| `db_backup` | Backup log | database_backup |
| `country` / `state` / `city` | Geography | geography methods |
| `village` | Village | villageDB |
| `offers` | Promotions | offersDB |
| `new_arrivals` | New arrivals | newArrivalsDB |
| `gift` | Gifts | giftDB |
| `sms_api_settings` | SMS config | sms_apiDB |
| `gateway_settings` | Payment gateway | gateway_settingsDB |
| `notification` | Notification templates | notificationDB |
| `payment_charges` | Fee structure | payment_chargesDB |
| `terms_and_conditions` | T&C | terms_and_conditionsDB |
| `version_details` | App versions | versionDB |
| `card_brand` | Card brands | cardbrandDB |
| `profession` | Professions | professionDB |
| `ledger_master` | Bank ledger accounts | ledgerDB |
| `ledger_mapping` | Ledger → bank/paymode mapping | ledgerDB |
| `kyc_master` | KYC document types | kyc_master() — **NEW** |
| `kyc_settings` | KYC config (singleton) | save_kyc_settings() — **NEW** |
| `kyc_rules` | KYC rules per scheme/customer | save_kyc_settings() — **NEW** |
| `ret_quick_link` | Quick link menu | quick_link(), quick_link_revert() |

---

## 4. Dependency Diagram

```
   ┌────────────────────────────────────────────────────────┐
   │              SETTINGS MODULE (Hub)                      │
   │     admin_settings.php (4,837 lines)                    │
   │     admin_settings_model.php (2,944 lines)              │
   │                                                          │
   │  ┌─────────────┬───────────────┬────────────────────┐   │
   │  │ 35+ Tables  │ 123 Methods   │ 182 Model Methods  │   │
   │  └─────────────┴───────────────┴────────────────────┘   │
   └────────────┬──────────────────────────────┬─────────────┘
                │                              │
    ┌───────────▼──────────┐      ┌────────────▼────────────┐
    │ EVERY MODULE reads:  │      │ Settings writes to:      │
    │ - get_access()       │      │ - scheme.free_payment    │
    │ - settingsDB()       │      │ - branch (settings)      │
    │ - metal_ratesDB()    │      │ - OneSignal API          │
    │ - menu_generation()  │      │ - ../api/rate.txt        │
    │ - limitDB()          │      │ - ../data/backup/*.zip   │
    │ - discount_db()      │      │ - customer images (delete)│
    └──────────────────────┘      └──────────────────────────┘
```

---

## 5. External Integrations

| Integration | Method | Direction |
|---|---|---|
| OneSignal Push API | `onesignalNotificationToAll()` | Outbound |
| SMS Gateway | `send_bulk_sms()` | Outbound |
| PM LogimaxIndia | `version_details()` L4249 — CURL POST to `pm.logimaxindia.com` | Outbound — **NEW** |
| File System (rate.txt) | `update_rate_file()` | File write |
| File System (backup) | `db_backup()` | File write |
| File System (images) | `set_image()`, `rrmdir()` | File write/delete |
