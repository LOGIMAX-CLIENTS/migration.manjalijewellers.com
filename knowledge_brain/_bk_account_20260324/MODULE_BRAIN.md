# Account (Scheme Account) — Module Brain
> **Module**: Account / Scheme Account Management  
> **Controller**: `admin_manage.php` (4,478 lines)  
> **Model**: `account_model.php` (3,651 lines)  
> **Round**: 1 | **Date**: 2026-03-06  

---

## 1. Module Overview

The Account module manages the complete lifecycle of **Scheme Accounts** — from opening (joining a savings scheme), through the payment period, to closing (maturity/pre-close). It also handles:
- Scheme group management
- Existing scheme registration requests (approve/reject/revert)
- Gift/prize issuance with inventory tracking
- OTP-based verification for closing and scheme joining
- Referral code management and wallet incentive transactions
- Client data synchronization (online/offline JIL sync)
- Passbook printing (front/back/close), QR/barcode generation
- Rate fixing for one-time-premium schemes
- Benefit/deduction calculation during scheme closing
- SMS, Email, WhatsApp, and push notification dispatch

---

## 2. File Map

### Controllers
| File | Lines | Purpose |
|---|---|---|
| `admin/application/controllers/admin_manage.php` | 4,478 | Primary controller for all account operations |

### Models
| File | Lines | Purpose |
|---|---|---|
| `admin/application/models/account_model.php` | 3,651 | Core DB interaction for scheme accounts |

### Views (scheme/)
| Directory | Files | Purpose |
|---|---|---|
| `scheme/opening/` | 7 files | Account list, form, gift issue, customer scheme list, receipt, send login |
| `scheme/closing/` | 2 files | Close account list, close form |
| `scheme/group/` | — | Scheme group CRUD views |
| `scheme/requests/` | — | Existing scheme reg approval views |
| `scheme/enquiry/` | — | Customer enquiry views |
| `scheme/print/` | — | Passbook front/back/close, bond print, benefit report |
| `scheme/settlement/` | — | Settlement views |
| `scheme/sales_transfer/` | — | Sales transfer views |

### JavaScript
| File | Purpose |
|---|---|
| `admin/assets/js/scheme_account.js` | Account form/list JS logic |
| `admin/assets/js/scheme.js` | Scheme-related JS |

### Key Constants (Controller)
```php
const ACC_MODEL = "account_model";
const EMP_MODEL = "employee_model";
const ADM_MODEL = "chitadmin_model";
const API_MODEL = "syncapi_model";
const CHITAPI_MODEL = "chitapi_model";
const SET_MODEL = "Admin_settings_model";
const PAY_MODEL = "payment_model";
const CUS_MODEL = "customer_model";
const MAIL_MODEL = "email_model";
const SMS_MODEL = "admin_usersms_model";
const LOG_MODEL = "log_model";
const WALL_MODEL = "Wallet_model";
```

### Key Constants (Model)
```php
const ACC_TABLE = "scheme_account";
const CUSREG_TABLE = "customer_reg";
const TRANS_TABLE = "transaction";
const CUS_TABLE = "customer";
const SCH_TABLE = "scheme";
const PAY_TABLE = "payment";
const REG_TABLE = "registration";
const ADD_TABLE = "address";
const SYNC_TABLE = "sync_log";
const OTP_TABLE = "otp";
const ISSU_TABLE = "gift_issued";
const SCHGROUP_TABLE = "scheme_group";
const BRANCH = "branch";
```

---

## 3. Routes (47 mapped routes)

### Account Opening
| Route | Controller Method |
|---|---|
| `account/new` | `index()` → `open_account()` |
| `account/add` | `account_form('Add')` |
| `account/save` | `account_post('Add')` |
| `account/edit/:id` | `account_form('Edit', $id)` |
| `account/update/:id` | `account_post('Edit', $id)` |
| `account/delete/:id` | `account_post('Delete', $id)` |
| `account/status/:s/:id` | `account_status($s, $id)` |
| `account/list` | `open_account_list()` |
| `account/customer/:id` | `get_customer_accounts($id)` |

### Account Closing
| Route | Controller Method |
|---|---|
| `account/close` | `close_account_list()` |
| `account/close/scheme/:id` | `close_account_form('Close', $id)` |
| `account/close/update/:id` | `close_account_form('Save', $id)` |
| `account/revert/:id` | `close_account_form('Revert', $id)` |
| `account/close/reject/:id` | `close_account_form('Reject', $id)` |
| `account/close/otp/:m/:c/:n` | `acc_close_otp($m, $c, $n)` |
| `account/fetch/otp/:id/:otp` | `acc_fetch_otp($id, $otp)` |
| `account/close/scheme_history/:id` | `close_account_history_form($id)` |
| `account/close/invoice_history/:id` | `invoice_history_form($id)` |
| `account/close/invoice_his_custom/:id` | `invoice_his_custom($id)` |

### AJAX Endpoints
| Route | Controller Method |
|---|---|
| `account/get/ajax_account_list` | `ajax_get_account_list()` |
| `account/get/ajax_closed_acc_list` | `ajax_get_closed_account_list()` |
| `account/get/ajax_list` | `ajax_get_scheme_account()` |
| `account/get/ajax_account` | `get_all_scheme_account()` |
| `account/payment_detail/:id` | `ajax_get_pay_detail($id)` |
| `account/closed/view/:id` | `closed_acc_detail($id)` |

### Scheme Groups
| Route | Controller Method |
|---|---|
| `account/scheme_group/list` | `scheme_group()` |
| `account/ajaxscheme_group/list` | `ajax_scheme_group_list()` |
| `account/scheme_group/add` | `schemegroup_form('View')` |
| `account/scheme_group/edit/:id` | `schemegroup_form('Edit', $id)` |
| `account/scheme_group/update/:id` | `schemegroup_form('Update', $id)` |
| `account/scheme_group/save` | `schemegroup_form('Save')` |
| `account/scheme_group/delete/:id` | `schemegroup_form('Delete', $id)` |
| `account/check_group` | `check_group()` |

### Scheme Registration
| Route | Controller Method |
|---|---|
| `account/reg/:cus/:sch/:reg` | `account_registration($cus, $sch, $reg)` |
| `account/registration/save/:id` | `registration_form_post($id)` |
| `account/registration` | `registration_list()` |
| `account/scheme_reg/list` | `schemereg_list()` |

### Other
| Route | Controller Method |
|---|---|
| `account/update/client` | `update_client()` |
| `schemeaccount/update` | `manual_schemeaccount()` |
| `mail/closing` | `closing_request()` |
| `branch/branchname_list` | `get_branch_name()` |
| `metal/metalname_list` | `get_metal_name()` |
| `reports/accountRemarks` | `accountRemarks()` |

---

## 4. Constructor & Session Dependencies

```
Constructor loads 13 models:
  account_model, customer_model, employee_model, chitapi_model,
  syncapi_model, chitadmin_model, Admin_settings_model, payment_model,
  email_model, admin_usersms_model, log_model, Wallet_model, sms_model

Session data used:
  - is_logged, access_time_from, access_time_to
  - uid (employee ID)
  - branch_settings, branchWiseLogin, is_branchwise_cus_reg
  - id_branch, id_company, company_settings
  - profile, mob_no_len, id_log
  - metal_wgt_roundoff, metal_wgt_decimal
  - OTP, pay_OTP, OTP_scheme_join
  - pay_OTP_expiry, gift_OTP_expiry, sche_join_otp_expiry, rate_fixing_otp_exp
```

---

## 5. Key Tables

| Table | Role |
|---|---|
| `scheme_account` | Core — stores all scheme account records |
| `scheme` | Scheme master — defines rules, installments, benefits |
| `customer` | Customer master |
| `payment` | Payment records (joins with scheme_account) |
| `scheme_group` | Group codes for scheme batches |
| `gift_issued` | Gifts/prizes issued to accounts |
| `gifts` | Gift master catalog |
| `otp` | OTP records for verification |
| `wallet_account` | Employee/agent wallet accounts |
| `wallet_transaction` | Wallet credit/debit transactions |
| `gift_card` | Voucher/gift card records |
| `customer_kyc` | KYC data (PAN, Aadhaar) |
| `branch` | Branch master |
| `sync_log` | Data synchronization logs |
| `customer_reg` | Customer registration intermediate table |
| `reg_request` | Existing scheme registration requests |
| `chit_settings` | Global scheme settings |
| `ret_financial_year` | Financial year configuration |
| `scheme_interest_chart` | Benefit/deduction calculation chart |
| `scheme_general_advance_benefit_settings` | GA bonus settings |
| `ret_other_inventory_purchase_items_details` | Inventory items for gifts |
| `ret_other_inventory_purchase_items_log` | Inventory transaction log |
| `loyalty_transaction` | Agent loyalty/referral transactions |

---

## 6. Brain Components

| # | Component | File | Status |
|---|---|---|---|
| 1 | Project Skeleton | [MODULE_BRAIN.md](MODULE_BRAIN.md) | ✅ Complete |
| 2 | Data Flow | [DATA_FLOW.md](DATA_FLOW.md) | ✅ Complete |
| 3 | Business Rules | [BUSINESS_RULES.md](BUSINESS_RULES.md) | ✅ Complete |
| 4 | Cross-Module Map | [CROSS_MODULE_MAP.md](CROSS_MODULE_MAP.md) | ✅ Complete |
| 5 | Invariant Matrix | [INVARIANT_MATRIX.md](INVARIANT_MATRIX.md) | ✅ Complete |
| 6 | DB Truth Protocol | [DB_TRUTH_PROTOCOL.sql](DB_TRUTH_PROTOCOL.sql) | ✅ Complete |
| 7 | Forensic Template | [FORENSIC_TEMPLATE.md](FORENSIC_TEMPLATE.md) | ✅ Complete |
| — | Method Index | [METHOD_INDEX.md](METHOD_INDEX.md) | ✅ Complete |
| — | Schema Analysis | [SCHEMA_ANALYSIS.md](SCHEMA_ANALYSIS.md) | ✅ Complete |
| — | Coverage Tracker | [COVERAGE_TRACKER.md](COVERAGE_TRACKER.md) | ✅ Complete |

---

## 7. Known Risks & Bugs

| # | Risk | Severity | Location |
|---|---|---|---|
| 1 | **SQL Injection** — Raw `$id` in model queries (`WHERE id_scheme =$id`) | 🔴 HIGH | Model L141, L156, L215, L301, throughout |
| 2 | **OTP returned in response** — `acc_close_otp()` returns OTP in JSON | 🔴 HIGH | Controller L1213 |
| 3 | **OTP comparison bug** — `verifyotp_gift()` uses `=` instead of `==` (L4188) | 🔴 HIGH | Controller L4188 |
| 4 | **Gift OTP also returns OTP** — `generate_giftotp()` sends OTP in response | 🔴 HIGH | Controller L3238 |
| 5 | **Rate fixing OTP in response** — `rateFixing_otp()` returns OTP in JSON | 🔴 HIGH | Controller L3359 |
| 6 | **Scheme join OTP in response** — `sendotp_scheme_join()` returns OTP | 🔴 HIGH | Controller L4042 |
| 7 | **Hardcoded date in SQL** — `'2024-10-07'` hardcoded in GA bonus query | 🔴 HIGH | Controller L1433 |
| 8 | **Division by zero** — `lump_payable_weight` divides by `total_installments` without zero check | 🟡 MED | Controller L510, L849 |
| 9 | **trans_commit inside loop** — `manual_schemeaccount()` commits inside foreach | 🔴 HIGH | Controller L2641 |
| 10 | **Undefined variable** — `$cus_single` / `$emp_single` used without declaration | 🟡 MED | Controller L620, L627, L635, L636 |
| 11 | **Undefined variable** — `$duration` not set before use in `generate_giftotp()` | 🟡 MED | Controller L3218 |
| 12 | **Undefined variable** — `$branch` in `update_client()` sync function | 🟡 MED | Controller L2329 |
| 13 | **Undefined variable** — `$params` not initialized in WhatsApp calls | 🟡 MED | Controller L2538, L2569, L2600, L2868 |
| 14 | **Unreachable code** — `exit` before `$rejected_pay_id` append (L2246-2247) | 🟡 MED | Controller L2246-2247 |
| 15 | **SMS gateway duplication** — Gateway routing (5 gateways) duplicated ~6 times | 🟡 LOW | Controller throughout |
| 16 | **Massive files** — Controller 4,478 lines, Model 3,651 lines | 🟡 MED | Both files |
| 17 | **Empty catch in gift debit** — `$gift_DebtArr = [];` then accesses `$gift_DebtArr['deduct_in']` | 🔴 HIGH | Controller L1372-1374 |
| 18 | **Login SMS sends password in plaintext** — `login_sms()` sends password via SMS | 🔴 HIGH | Controller L1962 |

---

## 8. Anti-Patterns Register

| # | Pattern | Occurrences | Risk |
|---|---|---|---|
| 1 | Direct `$_POST` access instead of `$this->input->post()` | ~15 places | XSS / input validation |
| 2 | Hardcoded SMS messages / DLT IDs | ~5 places | Maintenance |
| 3 | SMS gateway routing via if/elseif chain | ~6 duplications | DRY violation |
| 4 | OTP returned in JSON response | 5 endpoints | Security |
| 5 | Raw SQL with string concatenation | Throughout model | SQL injection |
| 6 | Undefined variables used without initialization | ~8 places | Runtime errors |
| 7 | `print_r()` / `echo` debug statements left in code | ~40 commented | Code quality |
| 8 | Commented-out legacy code blocks | ~200 lines | Maintenance debt |
