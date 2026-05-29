# Component 4 — Payment CRM: Cross-Module Mapping

> **Module:** Payment CRM  
> **Generated:** 2026-02-18
> **Updated:** 2026-02-18 (Verified table names)

---

## 1. External Module Dependencies

### 1.1 Scheme Account Module
| Direction | Function/Table | Data | Risk Level |
|-----------|---------------|------|------------|
| **IN** | `account_model::get_customer_acc()` | Customer + scheme account details | 🔴 High — core dependency |
| **IN** | `payment_model::get_paymentContent()` | 80+ fields from scheme_account joined with scheme, customer, branch | 🔴 High — "Big Query" |
| **IN** | `account_model::isAcnoAvailable()` | Account number status | 🟡 Medium |
| **OUT** | `account_model::update_account()` | Sets account number after first payment | 🔴 High — data mutating |
| **OUT** | `payment_model::updateGroupCode()` | Lucky draw group code assignment | 🟡 Medium |
| **OUT** | `payment_model::updFixedRate()` | Sets fixed metal rate on first payment (OTP schemes) | 🔴 High — data mutating |

### 1.2 Customer Module
| Direction | Function/Table | Data | Risk Level |
|-----------|---------------|------|------------|
| **IN** | `customer_model::get_entrydate()` | Day close date for branch | 🟡 Medium |
| **IN** | `admin_customer/searchcustomer` (AJAX) | Customer mobile/name search | 🟢 Low |
| **IN** | `customer` table | Customer master data | 🟢 Low |

### 1.3 Settings Module
| Direction | Function/Table | Data | Risk Level |
|-----------|---------------|------|------------|
| **IN** | `settings_model::paymodeDB('get')` | Payment mode master list | 🟢 Low |
| **IN** | `settings_model::bankDB('get')` | Bank master list | 🟢 Low |
| **IN** | `settings_model::draweeDB('get')` | Drawee master list | 🟢 Low |
| **IN** | `settings_model::get_access()` | RBAC access check | 🟡 Medium |

### 1.4 SMS/Notification Module
| Direction | Function/Table | Data | Risk Level |
|-----------|---------------|------|------------|
| **OUT** | `sms_model::check_noti_settings()` | Notification config | 🟢 Low |
| **OUT** | `sms_model::send_sms()` | SMS dispatch (5 gateways) | 🟡 Medium |
| **OUT** | `sms_model::send_whatsApp_message()` | WhatsApp notification | 🟡 Medium |
| **OUT** | `mail_model::send_mail()` | Email notification | 🟡 Medium |
| **IN** | `sms_template` | SMS template content | 🟢 Low |

### 1.5 DigiGold Module
| Direction | Function/Table | Data | Risk Level |
|-----------|---------------|------|------------|
| **IN** | `digigold_modal::digiGold_account()` | DigiGold account lookup | 🟡 Medium |
| **IN** | `digigold_modal::get_digi_benefit()` | Benefit percentage/amount | 🟡 Medium |
| **OUT** | `digigold_modal::update_digi_balance()` | Updates DigiGold balance after payment | 🔴 High — data mutating |

### 1.6 Integration Module
| Direction | Function/Table | Data | Risk Level |
|-----------|---------------|------|------------|
| **OUT** | `insert_common_data_jil()` | JIL integration sync | 🟡 Medium |
| **OUT** | `insert_common_data()` | Common integration sync | 🟡 Medium |
| **IN/OUT** | `customer_reg` | Third-party sync records | 🟡 Medium |

### 1.7 Wallet Module
| Direction | Function/Table | Data | Risk Level |
|-----------|---------------|------|------------|
| **IN** | `payment_model::wallet_balance()` | Current wallet balance + redeem percent | 🟡 Medium |
| **OUT** | `payment_model::deduct_wallet()` | Wallet amount deduction | 🔴 High — financial mutation |
| **IN/OUT** | `wallet_account`, `wallet_transaction` | Wallet transaction records | 🔴 High |

### 1.8 Advance/Voucher Module
| Direction | Function/Table | Data | Risk Level |
|-----------|---------------|------|------------|
| **IN** | `payment_model::get_advance_details()` | Available advance amounts | 🟡 Medium |
| **OUT** | `ret_advance_utilized` INSERT | Records advance utilization | 🔴 High — data mutating |
| **OUT** | `voucher_utilized` INSERT | Records voucher utilization | 🟡 Medium |

### 1.9 Reports Module
| Direction | Function/Table | Data | Risk Level |
|-----------|---------------|------|------------|
| **OUT** | Redirect to `payment/list` | Post-save redirect | 🟢 Low |
| **OUT** | Redirect to `reports/general_advance` | Post-GA-save redirect | 🟢 Low |

### 1.10 Scheme Management Module
| Direction | Function/Table | Data | Risk Level |
|-----------|---------------|------|------------|
| **IN** | `admin_manage/searchSchemeAccountNo` (AJAX) | Scheme account autocomplete | 🟢 Low |
| **IN** | `admin_manage/get_scheme_receipt` | Scheme receipt PDF | 🟢 Low |
| **IN** | `admin_scheme/ajax_get_schemes` | Scheme master list | 🟢 Low |

---

## 2. Dangerous Cross-Module Connections

| # | Connection | Why Dangerous | Impact |
|---|-----------|---------------|--------|
| 1 | `updFixedRate()` after first payment | **Irreversible** OTP rate fix. Wrong rate = permanent scheme damage | 🔴 Critical |
| 2 | `update_account()` account number generation | Auto-assigns account numbers. Duplicate = accounting chaos | 🔴 Critical |
| 3 | `deduct_wallet()` | Financial deduction. If payment fails mid-transaction but wallet already deducted → money lost | 🔴 Critical |
| 4 | `ret_advance_utilized` INSERT | Links advance to payment. If payment deleted but advance not restored → advance "eaten" | 🔴 Critical |
| 5 | `voucher_utilized` INSERT | Marks voucher as used. If payment fails → voucher permanently consumed | 🟡 High |
| 6 | Integration sync (JIL/Common) | External system sync. If sync fails after local save → data inconsistency | 🟡 High |
| 7 | `updateGroupCode()` | Lucky draw group assignment. Wrong assignment = audit issue | 🟡 High |

---

## 3. Data IN/OUT Summary

```
┌─────────────────────────────────────────────────────────────────┐
│                     PAYMENT CRM MODULE                          │
│                                                                 │
│  ┌─── DATA IN ───────────────────────────────────────────────┐ │
│  │                                                           │ │
│  │  Scheme Account → 80+ fields (Big Query)                  │ │
│  │  Customer → name, mobile, branch, wallet                  │ │
│  │  Settings → modes, banks, drawees, RBAC                   │ │
│  │  DigiGold → account, benefits chart                       │ │
│  │  Metal Rate → current rate, date-specific rate            │ │
│  │  Advance → available advance amounts                      │ │
│  │  SMS Templates → notification templates                   │ │
│  │                                                           │ │
│  └───────────────────────────────────────────────────────────┘ │
│                                                                 │
│  ┌─── DATA OUT ──────────────────────────────────────────────┐ │
│  │                                                           │ │
│  │  payment → INSERT/UPDATE (main record)                      │ │
│  │  payment_mode_details → INSERT (per mode)                  │ │
│  │  payment_status_message → INSERT (status log)               │ │
│  │  scheme_account → UPDATE (account number, rate fix)         │ │
│  │  wallet_account, wallet_transaction → UPDATE/INSERT        │ │
│  │  ret_advance_utilized → INSERT (advance tracking)         │ │
│  │  voucher_utilized → INSERT (voucher tracking)             │ │
│  │  customer_reg → INSERT (sync records)                      │ │
│  │  SMS/WhatsApp/Email → SEND (notifications)                │ │
│  │  Log files → WRITE (manual/create_payment_{date}.txt)     │ │
│  │                                                           │ │
│  └───────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```
