# Chit Settings Module — Schema Analysis
> **Round**: R2-Upgrade | **Date**: 2026-03-25
> **Tables**: 35+ owned | **New in upgrade**: kyc_master, kyc_settings, kyc_rules, ledger_master, ledger_mapping

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

## 4. KYC Tables (NEW — R2-Upgrade)

### `kyc_master`
| Column | Type | Purpose |
|---|---|---|
| `id_mas_kyc` | INT PK | KYC document type ID |
| `name` | VARCHAR | Document name (e.g., PAN, Aadhaar) |
| `status` | TINYINT | 1=active, 0=inactive |

### `kyc_settings`
| Column | Type | Purpose |
|---|---|---|
| `id_kyc_settings` | INT PK | Settings row ID (singleton pattern) |
| `kyc_required` | TINYINT | 0=disabled, 1=enabled |
| `kyc_mode` | TINYINT | 0=scheme-wise, 1=customer-wise |
| `kyc_verification_type` | TINYINT | Verification type flag |
| `kyc_allow_type` | TINYINT | Allow type flag |

### `kyc_rules`
| Column | Type | Purpose |
|---|---|---|
| `id_scheme` | INT FK (nullable) | Scheme reference (NULL for customer-wise mode) |
| `id_mas_kyc` | INT FK (nullable) | KYC document type |
| `rules` | VARCHAR | When rule applies (e.g., "on_join", "after_amount") |
| `type` | VARCHAR | Rule type |
| `amount` | DECIMAL (nullable) | Trigger amount threshold |
| `status` | TINYINT | 1=active |
| `created_by` | INT FK | Employee who created |

> ⚠️ **Schema Risk**: `kyc_rules` uses soft-delete via `status`. `save_kyc_settings()` L2910 first deletes ALL rules with `status=1`, then re-inserts. If the transaction fails between delete and insert, all rules are lost.

---

## 5. Ledger Tables (NEW — R2-Upgrade)

### `ledger_master`
| Column | Type | Purpose |
|---|---|---|
| `id_ledger` | INT PK | Ledger ID |
| `ledger_name` | VARCHAR | Ledger account name |
| `opening_balance` | DECIMAL | Opening balance |
| `opening_date` | DATE | Opening date |
| `min_balance` | DECIMAL | Minimum balance threshold |
| `status` | TINYINT | Active flag |

### `ledger_mapping`
| Column | Type | Purpose |
|---|---|---|
| `id_ledger` | INT FK | Ledger reference |
| `type` | VARCHAR | `BANK` or `PAYMODE` |
| `reference_id` | INT | FK to `bank.id_bank` or `ret_bill_pay_device.id_device` |

> ⚠️ **Schema Risk**: `ledger_mapping.reference_id` is a polymorphic FK (points to bank or device table depending on `type`). No DB-level FK constraint is possible.

---

## 6. Key Relationships

```
chit_settings (singleton, id=1)
    ← read by ALL modules

profile ──1:N──▶ access ──N:1──▶ menu
profile ──1:N──▶ dashboard_access ──N:1──▶ dashboard_menu

metal_rates ──1:N──▶ branch_rate ──N:1──▶ branch
drawee_account ──N:1──▶ bank

gateway_settings ←→ company (payment config)
sms_api_settings ←→ company (SMS config)

kyc_settings (singleton) ──▶ kyc_rules ──N:1──▶ kyc_master
                                        ──N:1──▶ scheme (via id_scheme)

ledger_master ──1:N──▶ ledger_mapping ──▶ bank (type=BANK)
                                        ──▶ ret_bill_pay_device (type=PAYMODE)
```
