# Chit Settings Module — Invariant Matrix
> **Round**: R2-Upgrade | **Date**: 2026-03-25

---

## 1. Variant Dimensions

### D1: User Profile Type
| Profile | Menu Exclusions | Special Access |
|---|---|---|
| 1 (Admin) | None | Full access to all menus, can see profile 1 |
| 2 | Exclude id_menu 17 | Can see id_menu 18 |
| Other | Exclude id_menu 17, 18 | Cannot see profile 1 in list |

### D2: Branch Settings Mode
| Flag | Behavior |
|---|---|
| `branch_settings=0` | No branch filtering, global rates |
| `branch_settings=1` | Branch-specific rates, scheme-to-branch mapping, employee-to-branch |

### D3: Rate Discount Type
| Setting | Gold 22ct | Gold 18ct | Silver |
|---|---|---|---|
| Discounts OFF | mjdma = selling | market = selling | mjdma = selling |
| Discounts ON | selling = mjdma - goldDiscAmt | selling = market - goldDiscAmt_18k | selling = mjdma - silverDiscAmt |

### D4: Clear Database Mode
| Mode | Scope | Risk |
|---|---|---|
| 0 (Selected) | Only checked categories | 🟡 Partial data loss |
| 1 (All) | All 10 categories | 🔴 Complete data loss |

### D5: Account/Receipt Number Format
| Display Format | Pattern Used |
|---|---|
| 0 (Default) | System auto-generated |
| 1 (Standard) | Predefined format |
| 2 (Custom) | Admin-configured components (br_code + sch_code + ...) |

### D6: Notification Settings
| Setting | Behavior |
|---|---|
| `canSendNoti=1` | Rate changes trigger OneSignal push to all users |
| `canSendNoti=0` | No notifications on rate changes |

### D7: OTP Requirements
| Flag | Scope |
|---|---|
| `isOTPReqToLogin=1` | Customer app login requires OTP |
| `isOTPRegForPayment=1` | Payment requires OTP |
| `isOTPReqToGift=1` | Gift issue requires OTP |
| `otp_scheme_join=1` | Scheme join requires OTP |
| `enable_closing_otp=1` | Account closing requires OTP |

### D8: KYC Mode (NEW — R2-Upgrade)
| `kyc_mode` | Behavior |
|---|---|
| 0 (Scheme-wise) | KYC rules specified per scheme — `kyc_rules.id_scheme` is populated, each scheme can have different doc requirements |
| 1 (Customer-wise) | KYC rules applied globally to customer — `kyc_rules.id_scheme` is NULL, rules apply regardless of scheme |

| `kyc_verification_type` | Behavior |
|---|---|
| 0 | Manual verification by admin |
| 1 | Auto verification (if supported) |

---

## 2. Behavior Grids

### Grid 1: Profile × Permission Scope

| Profile / Operation | View | Add | Edit | Delete | Acc Closing |
|---|---|---|---|---|---|
| Admin (1) | ✅ All | ✅ All | ✅ All | ✅ All | Per toggle |
| Manager (2) | Per menu | Per menu | Per menu | Per menu | Per toggle |
| Staff (3+) | Per menu | Per menu | Per menu | Per menu | Per toggle |
| New Profile | ✅ NO access | ✅ NO access | ✅ NO access | ✅ NO access | — |

### Grid 2: Master Data × CRUD Pattern

| Entity | Duplicate Check | Validation | Delete Guard | Image Support |
|---|---|---|---|---|
| Bank | ✅ by name | ✅ not-blank | ❌ None | ❌ No |
| Payment Mode | ✅ by name | ❌ None | ❌ None | ❌ No |
| Department | ✅ by name | ❌ None | ❌ None | ❌ No |
| Designation | ✅ by name | ❌ None | ❌ None | ❌ No |
| Weight | ❌ None | ❌ None | ❌ None | ❌ No |
| Classification | ❌ None | ❌ None | ❌ None | ✅ Yes |
| Drawee | ❌ None | ❌ None | ❌ None | ❌ No |
| Branch | ❌ None | ❌ None | ❌ None | ✅ Yes |
| Offer | ❌ None | ❌ None | ❌ None | ✅ Yes |
| Gift | ❌ None | ❌ None | ❌ None | ❌ No |
| Village | ❌ None | ❌ None | ❌ None | ❌ No |
| Profession | ✅ by name | ✅ duplicate check on insert+update (2025-07-10) | ❌ None | ❌ No |
| Ledger | ❌ None | ❌ None | ✅ trans_start/complete | ❌ No |

---

## 3. Edge Cases

| # | Scenario | Behavior | Risk |
|---|---|---|---|
| EC-1 | clear_database with mode=1 | TRUNCATES ALL app data | 🔴 Catastrophic |
| EC-2 | clear_database with no CSRF token | Can be triggered by external POST | 🔴 Critical |
| EC-3 | Rate discount > market rate | Selling price becomes negative | 🟡 Data quality |
| EC-4 | Delete menu with access records | Menu deleted but access records remain | 🟡 Orphan |
| EC-5 | General settings save without config save | config_dat built but NOT saved (commented out) | 🟡 Lost data |
| EC-6 | `$general[tab_name]` — PHP constant undefined | Generates PHP notice, uses literal string 'tab_name' | 🟡 Warning |
| EC-7 | concurrent rate saves | No locking on metal_rates insert | 🟡 Race condition |
| EC-8 | Branch with no metal rate | `branch_rate` mapping incomplete | 🟡 Downstream |
| EC-9 | 182 model methods, single file | Extremely hard to navigate | 🟡 Maintenance |
| EC-10 | mail_password stored plain text | No encryption for SMTP password | 🟡 Security |
| EC-11 | `save_kyc_settings()` deletes all rules then re-inserts | If crash between delete and insert, all rules lost (uses trans though) | 🟡 Transaction |
| EC-12 | `get_active_schemes()` uses `payment_model` | Cross-model dependency — settings controller calling non-settings model | 🟡 Architecture |
| EC-13 | `version_details()` POSTs to pm.logimaxindia.com | Hardcoded external URL — fails silently if server down | 🟡 Integration |
