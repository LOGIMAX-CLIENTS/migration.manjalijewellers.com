# MODULE BRAIN — Retail Settings
> **Module:** Retail Settings | **Round:** 9 | **Built:** 2026-03-16 | **Last Refresh:** 2026-03-25 | **Version:** etailv3

---

## 1. Module Overview

**Purpose:** Central configuration hub for the entire eTail system. Manages company setup, user profiles, menu permissions, master data (banks, payment modes, metal rates), SMS/email services, retail-specific settings (via `ret_settings`), branch configuration, gateway settings, and notification configuration.

**Scope:** This is NOT a transactional module — it is a CONFIGURATION module. Every other module reads settings managed here.

```
Browser → admin_settings.js (JS)
        → AJAX → admin_settings (Controller, 4709L, ~120 methods)
               → admin_usersms (Controller, 3320L, ~50+ methods)
        → Models:
               → admin_settings_model (2866L, ~80 methods)
               → admin_usersms_model (1818L, ~40 methods)
        → DB: ret_settings, profile, menu, access, bank, payment_mode,
              metal_rates, branch, company, ret_retail_settings, sms_service_settings, ...
        → Views: settings/ (8 subdirs + 8 root files)
                 settings/retail_setting/ (form.php, list.php)
```

---

## 2. Constructor Analysis

### `Admin_settings` Controller
| Loaded Model/Library | Purpose |
|---|---|
| `admin_settings_model` | Primary — all master/config DB ops (also aliased as `SETT_MOD`) |
| `admin_usersms_model` | SMS service config loading |
| `customer_model` | Customer data for SMS/OTP features |
| `scheme_model` | Scheme data lookups used in ref_benefits_setting |
| `account_model` | Account format settings |
| `chitadmin_model` | Notification & push alert dispatch |
| `log_model` | Audit log writing |
| `excel` (library) | Excel export for reports/backup |
| `form_validation` (library) | Input validation for bank/paymode save |

**Session Gate:** `is_logged` session check → redirect to `admin/login` if not set. No time-of-day restriction.

**Session Variables Read:**
- `uid` → `$this->id_employee`
- `branch_settings` → `$this->branch_settings`
- `branchWiseLogin` → `$this->branchWiseLogin`
- `filerbybranch` → `$this->usertype`
- `id_log` → `$this->id_log`

### `Admin_usersms` Controller
| Loaded Model/Library | Purpose |
|---|---|
| `admin_usersms_model` | SMS group, service, retail settings |
| `admin_settings_model` | Shared master data |
| `customer_model` | Customer mobile lists |
| `email_model` | Email dispatch |
| `chitadmin_model` | Notification dispatch |
| `sms_model` | Gateway-specific SMS send (MSG91/Nettyfish/SpearUC/Asterixt/Qikberry) |
| `log_model` | Audit log |

**Session Gate:** `is_logged` check only (no profile check in constructor).

---

## 3. Entry Points — Route Table

### `admin_settings` Controller Routes (Key ones)
| URL Pattern | Method | Type | Purpose |
|---|---|---|---|
| `/settings/index` | GET | Page | Company page (default) |
| `/settings/menu/{type}/{id}` | GET/POST | Page+AJAX | Menu CRUD |
| `/settings/profile/{type}/{id}` | GET/POST | Page+AJAX | Profile CRUD + 40-field permission profile |
| `/settings/permission/{type}` | GET/POST | Page+AJAX | Menu/Dashboard permission matrix |
| `/settings/bank/{type}/{id}` | GET/POST | Page | Bank master CRUD |
| `/settings/payment_mode/{type}/{id}` | GET/POST | Page | Payment mode CRUD |
| `/settings/drawee/{type}/{id}` | GET/POST | Page | Drawee account CRUD |
| `/settings/metal_rates/{type}/{id}` | GET/POST | Page | Metal rate entry — triggers file write + notifications |
| `/settings/general_settings/{type}/{id}` | GET/POST | Page | **Key:** Company-wide retail settings (reads/writes `ret_settings` indirectly via settingsDB) |
| `/settings/gateway_settings/{type}/{id}` | GET/POST | Page | Payment gateway config |
| `/settings/sms_api_settings/{type}/{id}` | GET/POST | Page | SMS API credentials |
| `/settings/mail_settings/{type}/{id}` | GET/POST | Page | Email SMTP config |
| `/settings/discount_settings/{type}/{id}` | GET/POST | Page | Gold/silver rate discount settings |
| `/settings/branch_settings/{id}` | GET | Page | Branch-level config |
| `/settings/gateway_form/{type}/{id}` | GET/POST | Page | Payment gateway image + form |
| `/settings/company_form/{type}/{id}` | GET/POST | Page | Company profile save |
| `/settings/classification_form/{type}/{id}` | GET/POST | Page | Scheme classification |
| `/settings/terms_conditions/{type}/{id}` | GET/POST | Page | Terms & conditions |
| `/settings/config_setting/{id}` | GET | Page | App config (mobile app) |
| `/settings/config_setupdate/{id}` | POST | AJAX | App config save |
| `/settings/notification/{type}/{id}` | GET/POST | Page | Push notification settings |
| `/settings/clear_database` | POST | Page | ⚠️ DB truncation (danger) |
| `/settings/db_backup` | GET | Page | DB backup download |

### `admin_usersms` Controller Routes (Key ones)
| URL Pattern | Method | Type | Purpose |
|---|---|---|---|
| `/usersms/index` | GET | Page | SMS dashboard |
| `/usersms/ret_settings_view/{id}` | GET | Page | **Retail settings form** |
| `/usersms/ret_settings_post/{id}` | POST | AJAX | **Retail settings save** → writes `ret_settings` |
| `/usersms/service_settings/{type}` | GET/POST | Page | SMS service toggle config |
| `/usersms/module_settings/{type}` | GET/POST | Page | Module-level SMS toggles |
| `/usersms/send_sms` | POST | AJAX | Manual SMS blast |
| `/usersms/sendsms_allcustomer` | POST | AJAX | Bulk SMS to all customers |
| `/usersms/sendemail_allcustomer` | POST | AJAX | Bulk email to all customers |
| `/usersms/open_group_post/{type}/{id}` | POST | AJAX | Group SMS/email send |

---

## 4. Key Tables

| Table | Purpose | Value Capture Needed |
|---|---|---|
| `ret_settings` | **Global key-value config** — 89 rows controlling all modules | ✅ YES — full values in SCHEMA_ANALYSIS Part C |
| `profile` | User role profiles + 40+ permission flags | ✅ YES — flag semantics |
| `menu` | Navigation menu items | Column structure only |
| `access` | Profile × Menu permission matrix | Column structure only |
| `dashboard_access` | Dashboard widget permissions | Column structure only |
| `bank` | Bank master | Column structure only |
| `payment_mode` | Payment modes | ✅ YES — active modes affect billing |
| `metal_rates` | Daily metal rate log | Column structure only (transaction) |
| `branch_rate` | Branch-specific metal rates | Column structure only |
| `company` | Company profile | Column structure only |
| `branch` | Branch master | Column structure only |
| `sms_service_settings` | SMS toggle per module | ✅ YES — values control SMS triggers |
| `ret_retail_settings` | **Per-setting named rows** for retail config | ✅ YES — full values |
| `sch_classify` | Scheme classification master | Column structure + active rows |
| `department` | Employee departments | Column structure only |
| `designation` | Employee designations | Column structure only |
| `weight` | Weight master (for schemes) | Column structure only |
| `payment_charges` | Charge slabs | Column structure only |
| `offers` | Promotional offers | Column structure only |
| `gift_voucher` | Gift voucher config | ✅ YES — free_gift_validate_days |
| `terms_and_conditions` | T&C text | Column structure only |
| `app_config` | Mobile app config | ✅ YES |

---

## 5. Business Rules Summary

Key rules discovered in Round 1 — see BUSINESS_RULES.md for full detail:

- **BR-RSET-001**: Metal rate save auto-computes discounted rates: `goldrate_22ct = mjdmagoldrate_22ct - goldDiscAmt` (if `enableGoldrateDisc=1`)
- **BR-RSET-002**: SMS gateway selection is from config file (`config->item('sms_gateway')`), not DB — values 1=MSG91, 2=Nettyfish, 3=SpearUC, 4=Asterixt, 5=Qikberry
- **BR-RSET-003**: Profile permissions gate ADD/EDIT/DELETE/VIEW across all modules via `access` table
- **BR-RSET-004**: `ret_settings` is a named key-value store — values must be looked up by `name`, not by `id_ret_settings`
- **BR-RSET-005**: Metal rate also writes to `../api/rate.txt` flat file (legacy API integration)

---

## 6. Known Risks (Round 1)

| Risk | Severity | Detail |
|---|---|---|
| `clear_database()` is a public method | 🔴 Critical | Truncates multiple tables — no OTP/approval gate found |
| Raw SQL in model queries | 🔴 High | String interpolation throughout `admin_settings_model.php` |
| `metal_rates` `file_put_contents('../api/rate.txt', $rate_array)` | 🟠 Medium | Writes PHP array (not JSON) to file at L570 — downstream API reads invalid format |
| `permission` type=`Save` loops without transaction | 🟠 Medium | Partial permission save if loop fails mid-iteration |
| Profile `update` at L238: `order_cancel_otp_req` (POST key) → maps to `order_cancel_otp` (DB column) — inconsistent naming | 🟡 Low | Typo risk if form field name changes |

---

## 7. Anti-Patterns Register

> Empty in Round 1 — updated after each bug fix

---

## 8. Cross-Module Dependencies

→ See CROSS_MODULE_MAP.md for full map

**Key dependencies:** Every module in the system reads `ret_settings`. The Settings module writes it; all others read it. This makes `ret_settings` a **single point of failure** for all system-wide config.

---

## 9. DB Verification Queries

```sql
-- 1. Full ret_settings snapshot (use as baseline)
SELECT id_ret_settings, name, value, description FROM ret_settings ORDER BY id_ret_settings;

-- 2. All profile permissions for a given user
SELECT p.profile_name, m.label, m.link, a.view, a.add, a.edit, a.delete
FROM access a
JOIN menu m ON a.id_menu = m.id_menu
JOIN profile p ON a.id_profile = p.id_profile
WHERE a.id_profile = {id_profile}
ORDER BY m.parent, m.sort;

-- 3. Current metal rate (latest)
SELECT * FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1;

-- 4. Check orphan access rows (profile deleted but access rows remain)
SELECT a.id_profile FROM access a
LEFT JOIN profile p ON a.id_profile = p.id_profile
WHERE p.id_profile IS NULL;
```
