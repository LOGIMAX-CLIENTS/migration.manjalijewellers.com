# SCHEMA ANALYSIS — masters
> Round R5-Upgrade — 2026-03-25

---

## Part A: Critical Owned Tables

### Table: `chit_settings` (Single Row Config — MOST READ TABLE)
| Column Group | Key Fields | Notes |
|---|---|---|
| Core flags | `branch_settings`, `integrationType`, `autoSyncExisting` | System behavior toggles |
| Device limits | `chitCollectionEmpCount` | Max collection app devices |
| Rate discounts | `enableGoldrateDisc`, `goldDiscAmt`, `enableGoldrateDisc_18k`, `goldDiscAmt_18k`, `enableSilver_rateDisc`, `silverDiscAmt` | Applied before storing rates |
| SMS/OTP | `sms_gateway`, `isOTPReqToLogin`, `loginOTP_exp` | Auth and notification config |
| Wallet | `emp_wallet_account_type` | Employee wallet auto-creation |
| Display | `schemeaccNo_displayFrmt` | Account number format |
| Booking | `vs_booking_time` | Virtual showroom timing |

**Risk:** Single point of failure — every module reads this on every request. No cache layer.

### Table: `metal_rates`
| Column | Type | Notes |
|---|---|---|
| `id_metalrates` | INT PK AUTO | |
| `goldrate_22ct` | DECIMAL | After discount |
| `goldrate_18ct` | DECIMAL | After discount |
| `silverrate_1gm` | DECIMAL | After discount |
| `mjdmagoldrate_22ct` | DECIMAL | Market rate (pre-discount) |
| `market_gold_18ct` | DECIMAL | Market 18ct |
| `mjdmasilverrate_1gm` | DECIMAL | Market silver |
| `platinum_rate` | DECIMAL | |
| `date_add` | DATETIME | Entry timestamp |

**Risk:** Rates used by payment and billing for scheme calculations. Stale `rate.txt` file (MST-BUG-003/042).

### Table: `branch`
| Column | Type | Notes |
|---|---|---|
| `id_branch` | INT PK AUTO | |
| `name` | VARCHAR | Branch name |
| `active` | INT | 0/1 |
| `logo` | VARCHAR | Image path |
| `id_company` | INT FK | → `company.id_company` |
| `address`, `phone`, `email` | VARCHAR | Contact info |
| `expo_warehouse`, `warehouse` | VARCHAR | ERP warehouse IDs |

### Table: `access` (RBAC)
| Column | Type | Notes |
|---|---|---|
| `id_access` | INT PK AUTO | |
| `id_profile` | INT FK | → `profile.id_profile` |
| `id_menu` | INT FK | → `menu.id_menu` |
| `view` | INT | 0/1 |
| `add` | INT | 0/1 |
| `edit` | INT | 0/1 |
| `delete` | INT | 0/1 |

### Table: `profile`
| Column | Type | Notes |
|---|---|---|
| `id_profile` | INT PK AUTO | 1=superadmin |
| `profile_name` | VARCHAR | Role name |
| `allow_acc_closing` | INT | Can close accounts |
| `metalrate_edit` | INT | Can edit rates |
| `req_otplogin` | INT | OTP required |
| `metal_rate_datelimit` | INT | Rate edit date limit |

---

## Part B: Entity Master Tables (25+)

| Table | Primary Key | Key Columns | CRUD Methods |
|---|---|---|---|
| `bank` | `id_bank` | `name`, `active` | `bankDB()` |
| `payment_mode` | `id_paymode` | `pay_mode`, `active` | `paymodeDB()` |
| `drawee` | `id_drawee` | `drawee`, `active` | `draweeDB()` |
| `department` | `id_dept` | `name` | `insert/update/delete_dept` |
| `designation` | `id_design` | `name` | `insert/update/delete_design` |
| `weight` | `id_weight` | `weight`, `active` | `insert/update/delete_weight` |
| `classification` | `id_classification` | `name`, `image` | `insert/update/delete_classification` |
| `card_brand` | `id_card_brand` | `brand_name` | `insert/update/delete_card_brand` |
| `payment_charges` | `id_charges` | `charge_type`, `amount`, `range_from`, `range_to` | `insert/update/delete_charges` |
| `offers` | `id_offer` | `offer_desc`, `offer_img_path`, `active`, `is_popup` | `insert/update/delete_offer` |
| `new_arrivals` | `id_new_arrivals` | `desc`, `img_path`, `active` | `insert/update/delete_new_arrivals` |
| `gift` | `id_gift` | `gift_name`, `from_instalment`, `active` | `giftDB()` |
| `profession` | `id_profession` | `name` | `insert/update/delete_profession` |
| `village` | `id_village` | `village_name`, `panchayat`, `pincode` | `village_settingDB()` |
| `terms_conditions` | `id_terms` | `title`, `description` | `terms_and_conditions()` |
| `version` | `id_version` | `version_no`, `platform`, `date_add` | `versionDB()` |
| `ledger` | `id_ledger` | `ledger_name`, `opening_balance`, `type` | `ledgerDB()` |
| `notification` | `id_notification` | `noti_name`, `noti_msg`, `noti_sub` | CRUD |

---

## Part C: Index Analysis (Suspected Missing)

| Table | Column(s) in WHERE | Query Pattern | Index Needed? |
|---|---|---|---|
| `chit_settings` | `id_chit_settings` (always 1) | PK lookup | ✅ Already PK |
| `metal_rates` | `id_metalrates` DESC | ORDER BY for latest | ✅ Covered by PK |
| `branch_rate` | `id_branch`, `id_metalrate` | JOIN + WHERE | ✅ Compound `(id_branch, id_metalrate)` |
| `access` | `id_profile`, `id_menu` | RBAC lookup | ✅ Unique `(id_profile, id_menu)` |
| `menu` | `link` | URL-based lookup in `get_access()` | ⚠️ Index on `link` column |
| `branch` | `active` | List filter | ⚠️ Low cardinality but frequent |
| `village` | `pincode` | Pincode lookup | ✅ Index on `pincode` |
| `offers` | `active`, `is_popup` | Popup check | ⚠️ Compound `(active, is_popup)` |

---

## Part D: Write Operation Inventory (High-Risk Only)

| Method | Table(s) Written | Guards | Risk |
|---|---|---|---|
| `settingsDB('update')` | `chit_settings` | ✅ trans_begin/commit | LOW — but affects ALL modules |
| `insert_metalrate()` | `metal_rates` | ❌ No transaction in controller | 🟡 Partial rate insert if crash |
| `insert_branch()` | `branch`, `metal_rate_settings`, `chit_settings`, `employee_settings` | ❌ No trans_begin on Update (MST-BUG-028) | 🔴 Partial branch setup |
| `PermissionDB('update')` | `access` | ❌ No transaction | 🟡 Partial permission save |
| `clear_database()` → `truncateFromArray()` | ALL core tables (TRUNCATE) | ❌ No auth, no confirm, no CSRF | 🔴 P0 CATASTROPHIC |
| `database_backup()` | filesystem | ❌ No role check (MST-BUG-033) | 🔴 Full DB downloadable |
| `gateway_settingsDB('update')` | `gateway_settings` | ❌ No sibling toggle (MST-BUG-026) | 🟡 Dual active gateways |
| `metal_rates('Save')` file | `../api/rate.txt` | ❌ Writes "Array" not JSON (MST-BUG-003) | 🔴 Mobile API broken |

---

## Risk Flags

| Table | Risk Level | Details |
|---|---|---|
| `chit_settings` | CRITICAL | Single row controls entire system; no cache |
| `access` + `menu` | CRITICAL | RBAC engine; SQLi in `get_access()` (MST-BUG-002) |
| `metal_rates` | HIGH | Downstream financial impact; flat file broken |
| `branch` | HIGH | Branch create has no transaction on Update |
| `gateway_settings` | HIGH | Payment gateway credentials; dual-active bug |
| ALL core tables | CATASTROPHIC | `clear_database()` truncates without auth |
