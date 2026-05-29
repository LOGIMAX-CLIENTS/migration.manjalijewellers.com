# Chit Settings Module — Schema Analysis
> **Round**: 1 | **Date**: 2026-03-06

---

## 1. Core Table: `chit_settings` (Singleton)

**Role**: Global application settings — ONE row controls ALL behavior.

### Key Columns (60+ flags, grouped by feature)
| Column Group | Columns | Purpose |
|---|---|---|
| Payment | `edit_addpay_page`, `custom_entry_date`, `edit_custom_entry_date` | Payment page behavior |
| Scheme Accounts | `scheme_wise_receipt`, `scheme_wise_acc_no`, `allow_join_multiple`, `allow_join_unpaid`, `delete_unpaid` | Scheme join rules |
| Registration | `reg_existing`, `regExistingReqOtp`, `newSchjoinonline`, `otp_scheme_join` | Customer registration |
| Rate | `rate_update`, `enableGoldrateDisc`, `goldDiscAmt`, `enableSilver_rateDisc`, `silverDiscAmt`, `enableGoldrateDisc_18k`, `goldDiscAmt_18k` | Rate discount |
| Branch | `branchwise_scheme`, `cost_center` | Branch settings |
| Limits | `sch_limit` | Feature limiting |
| Display | `show_closed_list`, `has_lucky_draw`, `enable_dth`, `enable_coin_enq`, `vs_enable`, `enable_coin_book` | Feature toggles |
| GST | `gst_setting` | GST on/off |
| OTP | `enable_closing_otp`, `isOTPReqToLogin`, `isOTPRegForPayment`, `payOTP_exp`, `loginOTP_exp`, `isOTPReqToGift`, `giftOTP_exp`, `req_otp_login`, `req_gift_issue_otp`, `req_prize_issue_otp` | OTP control |
| Receipt/Account | `receipt_no_set`, `receipt`, `schemeacc_no_set`, `schemeaccNo_displayFrmt`, `receiptNo_displayFrmt`, `custom_AccDisplayFrmt`, `custom_ReceiptDisplayFrmt` | Number format |
| App Features | `allow_savecard`, `allow_catlog`, `cusName_edit`, `auto_debit`, `auto_debit_allow_app_pay` | App capabilities |
| Metal | `metal_wgt_decimal`, `metal_wgt_roundoff` | Weight precision |
| Maintenance | `maintenance_mode`, `maintenance_text` | Maintenance mode |
| KYC | `show_kyc_optional`, `pan_req_amt`, `pan_required_by`, `block_kyc_by` | KYC rules |
| Collection | `chitCollectionEmpCount`, `restrict_lastPayment_days` | Collection limits |
| Video Shop | `vs_booking_time` | Video shop time |
| Client | `gent_clientid` | Client ID |

---

## 2. Permission Tables

### `menu`
| Column | Type | Purpose |
|---|---|---|
| `id_menu` | INT PK | Menu item ID |
| `label` | VARCHAR | Display name |
| `link` | VARCHAR | Route/URL |
| `parent` | INT FK | Parent menu (1=top-level) |
| `sort` | INT | Sort order |
| `icon` | VARCHAR | FontAwesome icon class |
| `active` | TINYINT | 0=hidden, 1=visible |

### `access`
| Column | Type | Purpose |
|---|---|---|
| `id_profile` | INT FK | Profile ID |
| `id_menu` | INT FK | Menu item ID |
| `view` | TINYINT | Can view |
| `add` | TINYINT | Can add |
| `edit` | TINYINT | Can edit |
| `delete` | TINYINT | Can delete |

### `profile`
| Column | Type | Purpose |
|---|---|---|
| `id_profile` | INT PK | Profile ID |
| `profile_name` | VARCHAR | Name |
| `allow_acc_closing` | TINYINT | Account closing permission |
| `req_otplogin` | TINYINT | Require OTP for login |
| `show_pending_download` | TINYINT | Show pending downloads |
| `allow_bill_cancel` | TINYINT | Bill cancel permission |
| ... (~35 more toggles) | TINYINT | Various feature toggles |

### `dashboard_menu` / `dashboard_access`
Same structure as menu/access but for dashboard-specific items.

---

## 3. Master Data Tables

### `bank`
| Column | Type | Purpose |
|---|---|---|
| `id_bank` | INT PK | Bank ID |
| `bank_name` | VARCHAR | Bank name (unique enforced in code) |
| `short_code` | VARCHAR | Short code |
| `acc_number` | VARCHAR | Account number |
| `ifsc_code` | VARCHAR | IFSC code |

### `drawee_account`
| Column | Type | Purpose |
|---|---|---|
| `id_drawee` | INT PK | Drawee ID |
| `account_no` | VARCHAR | Account number |
| `account_name` | VARCHAR | Account name |
| `id_bank` | INT FK | Bank reference |
| `branch` | VARCHAR | Branch name |
| `ifsc_code` | VARCHAR | IFSC |

### `payment_mode`
| Column | Type | Purpose |
|---|---|---|
| `id_mode` | INT PK | Mode ID |
| `mode_name` | VARCHAR | Mode name (Cash/Cheque/UPI/etc.) |
| `short_code` | VARCHAR | Short code |

### `metal_rates`
| Column | Type | Purpose |
|---|---|---|
| `id_metalrates` | INT PK | Rate ID |
| `mjdmagoldrate_22ct` | DECIMAL | Market gold 22ct |
| `goldrate_22ct` | DECIMAL | Selling gold 22ct (after discount) |
| `goldrate_18ct` | DECIMAL | Selling gold 18ct |
| `market_gold_18ct` | DECIMAL | Market gold 18ct |
| `goldrate_24ct` | DECIMAL | Gold 24ct |
| `goldrate_14ct` | DECIMAL | Gold 14ct |
| `goldrate_9ct` | DECIMAL | Gold 9ct |
| `platinum_1g` | DECIMAL | Platinum per gram |
| `mjdmasilverrate_1gm` | DECIMAL | Market silver per gram |
| `silverrate_1gm` | DECIMAL | Selling silver per gram |
| `silverrate_1kg` | DECIMAL | Silver per kg |
| `market_gold_995` | DECIMAL | Gold 995 |
| `coin_gold_22ct` | DECIMAL | Coin gold 22ct |
| `mjdmasilverrate_999` | DECIMAL | Silver 999 |
| `market_gold_20ct` | DECIMAL | Gold 20ct |
| `id_employee` | INT FK | Who updated |
| `updatetime` | DATETIME | Last update |
| `add_date` | DATETIME | Created |

### `branch_rate`
| Column | Type | Purpose |
|---|---|---|
| `id_metalrate` | INT FK | Metal rate reference |
| `id_branch` | INT FK | Branch reference |
| `status` | TINYINT | Active flag |
| `date_add` | DATETIME | Created |

---

## 4. Key Relationships

```
chit_settings (singleton, id=1)
    ← read by ALL modules

profile ──1:N──▶ access ──N:1──▶ menu
profile ──1:N──▶ dashboard_access ──N:1──▶ dashboard_menu

metal_rates ──1:N──▶ branch_rate ──N:1──▶ branch
drawee_account ──N:1──▶ bank

gateway_settings ←→ company (payment config)
sms_api_settings ←→ company (SMS config)
```
