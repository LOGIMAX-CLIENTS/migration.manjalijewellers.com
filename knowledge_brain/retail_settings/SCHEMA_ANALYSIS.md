# SCHEMA ANALYSIS — Retail Settings
> **Module:** Retail Settings | **Built:** 2026-03-16

---

## Part A: Owned Tables (Primary Config Tables)

### 1. `ret_settings` — Global Key-Value Config Store ⭐ CRITICAL
> **Type:** Key-value store — 89 rows as of 2026-03-16  
> **Access:** Written by `admin_usersms` controller via `ret_settings_post()`. Read by EVERY module.

| Column | Type (inferred) | Notes |
|---|---|---|
| `id_ret_settings` | INT PK AUTO_INCREMENT | Numeric key — NOT the lookup key |
| `name` | VARCHAR | **Lookup key** — all code reads by `name` |
| `value` | VARCHAR/INT | Actual config value (string or numeric) |
| `description` | VARCHAR | Human-readable description (shown in form) |
| `created_on` | DATETIME | Creation timestamp |
| `created_by` | INT FK | → `employee.id_employee` |
| `updated_on` | DATETIME | Last update |
| `updated_by` | INT FK | → `employee.id_employee` |

**⚠️ Risk:** No unique constraint on `name` — duplicate setting names possible, causing ambiguous lookups.

---

## Part B: Settings Value Snapshot — `ret_settings`
> Captured from phpMyAdmin screenshot (2026-03-16). **All 89 rows must be captured from live DB.**  
> Run: `SELECT id_ret_settings, name, value, description FROM ret_settings ORDER BY id_ret_settings;`

### Confirmed Settings (from screenshot — first 15 rows):

| id | name | value | description | Affects Module |
|---|---|---|---|---|
| 1 | `allow_branch_transfer` | `1` | 0=No, 1=Yes | Branch Transfer |
| 2 | `lot_recv_branch` | `1` | 1=Any, 2=Head Office | LOT/Stock |
| 3 | `kangar_orderalert_remain_days` | `2` | No. of days before order due date alert | Karigar/Order |
| 5 | `min_pan_amt` | `199999` | PAN Amount threshold | Billing (PAN gate) |
| 6 | `is_pen_required` | `1` | Pan required for billing | Billing |
| 7 | `weight_per` | `1` | In Percentage | Weight calc |
| 8 | `other_issue_branch` | `1` | Default to branch for other issue | Other Inventory |
| 10 | `branchs` | `332423` | all (branch list string) | Multi-branch |
| 11 | `allow_tag_pcs` | `1` | Allow Pcs to Add in Tag | Tagging |
| 12 | `spc_gift_voucher` | `1` | Special Gift Voucher enabled | Scheme |
| 13 | `free_gift_validate_days` | `45` | No. of Days For Free Gift Voucher | Scheme |
| 15 | `min_wl_gram` | `0.500` | Minimum Weight in Gram | Billing/Scheme |

> **⚠️ ACTION REQUIRED:** Add remaining 74+ rows from live DB query before audit.

---

## Part C: `profile` Table — Permission Profile
| Column | Type (inferred) | Notes |
|---|---|---|
| `id_profile` | INT PK | Profile master key |
| `profile_name` | VARCHAR | Display name |
| `allow_acc_closing` | TINYINT | Allow account closing |
| `req_otplogin` | TINYINT | OTP required for login |
| `show_pending_download` | TINYINT | Show pending download badge |
| `show_cart` | TINYINT | Show cart in billing |
| `allow_bill_cancel` | TINYINT | Allow billing cancellation |
| `allow_order_cancel` | TINYINT | Allow order cancellation |
| `allow_lot_cancel` | TINYINT | Allow LOT cancellation |
| `bill_cancel_otp` | TINYINT | OTP required for bill cancel |
| `credit_sales_otp_req` | TINYINT | OTP for credit sales |
| `vendor_approval_otp_req` | TINYINT | OTP for vendor approval |
| `stock_issue_otp_req` | TINYINT | OTP for stock issue |
| `allow_branch_transfer_cancel` | TINYINT | Allow BT cancel |
| `allow_other_issue` | TINYINT | Allow other inventory issue |
| `device_wise_login` | TINYINT | Single-device login enforce |
| `allow_bill_type` | TINYINT | Billing types allowed |
| `allow_stock_type` | TINYINT | Stock types allowed |
| `order_delivery_otp` | TINYINT | OTP for order delivery |
| `previous_bill_cancel` | TINYINT | Allow prior-date bill cancel |
| `tag_transfer` | TINYINT | Tagged item transfer |
| `non_tag_transfer` | TINYINT | Non-tagged item transfer |
| `purchase_item_transfer` | TINYINT | Purchase item BT |
| `packaging_item_transfer` | TINYINT | Packaging item BT |
| `allow_mc_edit` | TINYINT | Allow MC edit after save |
| `allow_va_edit` | TINYINT | Allow VA edit after save |
| `est_purity_edit` | TINYINT | Allow purity edit in estimation |
| `tag_details` | TINYINT | View tag details |
| `purchase_details` | TINYINT | View purchase details |
| `stone_details` | TINYINT | View stone details |
| `estimation` | TINYINT | Allow estimation access |
| `branch_transfer_details` | TINYINT | View BT details |
| `section_transfer_details` | TINYINT | View section transfer details |
| `scan_details` | TINYINT | View barcode scan details |
| `stock_issue_details` | TINYINT | View stock issue details |
| `est_tag` | TINYINT | Estimation - tag type |
| `est_non_tag` | TINYINT | Estimation - non-tag type |
| `est_home_bill` | TINYINT | Estimation - home bill type |
| `est_old_metal` | TINYINT | Estimation - old metal |
| `order_cancel_otp` | TINYINT | OTP for order cancel |
| `counter_change_otp` | TINYINT | OTP for counter change |
| `order_unlink_otp` | TINYINT | OTP for order unlink |
| `credit_collection_disc_otp` | TINYINT | OTP for credit discount |
| `bill_disc_approval_type` | TINYINT | Bill discount approval flow |
| `credit_sales_approval_type` | TINYINT | Credit sales approval flow |
| `BT_otp_approval_type` | TINYINT | BT OTP approval type |
| `pre_date_oi` | TINYINT | Pre-date other inventory |
| `metalrate_edit` | TINYINT | Allow metal rate backdating |
| `metal_rate_datelimit` | INT | Days back-dating limit for metal rate |
| `wedding_wastage_slab` | TINYINT | Wedding wastage slab enable |

**Key:** Profile flag of `1=Allowed, 0=Not allowed` for all TINYINT columns.

---

## Part D: Other Owned Tables

### `bank`
| Column | Notes |
|---|---|
| `id_bank` | INT PK |
| `bank_name` | VARCHAR UNIQUE (enforced in code, not DB) |
| `short_code` | VARCHAR |
| `acc_number` | VARCHAR |
| `ifsc_code` | VARCHAR |

### `payment_mode`
| Column | Notes |
|---|---|
| `id_mode` | INT PK |
| `mode_name` | VARCHAR UNIQUE (enforced in code) |
| `short_code` | VARCHAR |

### `metal_rates` — Rate History
| Column | Notes |
|---|---|
| `id_metalrates` | INT PK AUTO_INCREMENT |
| `mjdmagoldrate_22ct` | DECIMAL — raw MJDMA input rate |
| `goldrate_22ct` | DECIMAL — computed = mjdma - goldDiscAmt |
| `goldrate_18ct` / `market_gold_18ct` | DECIMAL — raw and computed 18k |
| `goldrate_14ct`, `goldrate_9ct`, `goldrate_24ct` | DECIMAL |
| `silverrate_1gm` / `mjdmasilverrate_1gm` | DECIMAL — raw and computed |
| `silverrate_1kg`, `platinum_1g`, `coin_gold_22ct` | DECIMAL |
| `market_gold_995`, `market_gold_20ct`, `mjdmasilverrate_999` | DECIMAL |
| `id_employee` | INT FK → employee |
| `updatetime` | DATETIME |
| `add_date` | DATETIME |

**⚠️ Risk:** `file_put_contents('../api/rate.txt', $rate_array)` at L570 writes raw PHP array (not JSON). Downstream API consumers reading this file will get invalid format.

---

## Schema Risk Summary

| Risk | Table | Detail |
|---|---|---|
| 🔴 No UNIQUE on `ret_settings.name` | `ret_settings` | Duplicate setting names = wrong value returned |
| 🔴 Raw SQL interpolation | All tables | No parameterized queries in `admin_settings_model` |
| 🟠 `clear_database()` is unprotected | Multiple tables | Truncates DB without OTP/approval check |
| 🟠 `rate.txt` not JSON | `metal_rates` | `file_put_contents` writes PHP array at L570 |
| 🟡 UNIQUE on `bank_name`, `mode_name` in code only | `bank`, `payment_mode` | DB allows duplicates if code check bypassed |
| 🟡 `order_cancel_otp_req` → `order_cancel_otp` mismatch | `profile` | POST field name differs from DB column name |
