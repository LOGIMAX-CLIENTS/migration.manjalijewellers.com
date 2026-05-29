# Shared Models — Cross-Module Function Map

> Models loaded by 2+ modules. Changing a method here affects ALL loading modules.
> Last updated: 2026-03-26

> [!WARNING]
> Before modifying ANY method in a shared model, grep ALL controllers to find callers:
> ```powershell
> Select-String -Pattern "method_name" -Path "d:\XAMPP\htdocs\retail_v5\application\controllers\*.php"
> ```

---

## `admin_settings_model` — The Universal Hub

**Loaded by**: ALL modules (every controller loads it in constructor)

| Method | Called By Modules | What It Returns | Risk if Changed |
|---|---|---|---|
| `get_access($url)` | ALL | Bool — profile can access this URL | 🔴 CRITICAL — breaks all permission checks |
| `settingsDB()` / `manage_chit_settings()` | ALL | All 60+ `chit_settings` columns | 🔴 HIGH — removed column → PHP notice in all callers |
| `getBranchDayClosingData($branch)` | Billing, Estimation, Tagging, LOT, Stock Issue, Branch Transfer, Sales Transfer, Section Transfer, Old Metal, Other Inventory, Customer, Customer Order | `{entry_date, is_day_closed}` | 🔴 HIGH — determines operational date for all modules |
| `getCompanyDetails($branch)` | ALL print flows | Company name, logo, address | 🟡 MED — print/PDF headers |
| `profileDB()` | ALL | Role profile data | 🟡 MED |
| `metal_ratesDB()` | Payment, Account, Settings, Mobile API | Metal rates by date/branch | 🔴 HIGH — payment calc |
| `limitDB()` | Scheme, Account | Scheme count limits per customer | 🟡 MED |
| `get_gstsettings()` | Scheme, Payment | GST rate per scheme | 🟡 MED |
| `get_company()` | Account, Payment, Reports | Company info for SMS/invoice | 🟢 LOW |
| `get_branches()` | Account, Scheme, Payment | Branch list | 🟢 LOW |
| `get_service()` / `get_service_by_code()` | Account, Customer, Section Transfer | Service configuration flags | 🟡 MED |

---

## `payment_model` — Core Financial Model

**Loaded by**: Account, Chit Reports, Settings, Scheme, Billing

| Method | Called By | Tables | Risk if Changed |
|---|---|---|---|
| `paymentDB()` | Payment, Account, Chit Reports | `payment` | 🔴 HIGH — core payment CRUD |
| `generate_receipt_no()` | Account, Payment | `payment`, `chit_settings` | 🔴 HIGH — receipt duplication risk |
| `get_metalrate_by_branch()` | Scheme, Account, Payment | `metal_rates` | 🔴 HIGH — rate accuracy |
| `payment_cancel()` | Chit Reports | `payment` (status=4) | 🔴 HIGH — cancel flow |
| `insertData($table, $data)` | ALL modules | `{any}` | 🔴 CRITICAL — universal insert |
| `updateData($table, $data, $where)` | ALL modules | `{any}` | 🔴 CRITICAL — universal update |
| `payment_list*()` | Chit Reports | `payment` + joins | 🟡 MED — reports |
| `get_account_payment()` | Account | `payment`, `scheme_account` | 🟡 MED |

---

## `account_model` — Scheme Account Model

**Loaded by**: Payment, Chit Reports, Settings, Scheme, Billing

| Method | Called By | Tables | Risk if Changed |
|---|---|---|---|
| `get_scheme_account()` / `scheme_account*()` | Payment, Chit Reports | `scheme_account` + joins | 🔴 HIGH — account lookups |
| `scheme_summary_data()` | Chit Reports | `scheme`, `scheme_account`, `payment` ×4 | 🟡 MED — performance risk |
| `scheme_group_summary_data($id)` | Chit Reports | `scheme_group` | 🟡 MED — ⚠️ raw `$id` SQL injection |
| `is_luckly_draw_scheme($id)` | Chit Reports | `scheme` | 🟡 MED — ⚠️ raw `$id` SQL injection |
| `get_due_date()` | Account, Payment | `payment`, `scheme_account` | 🔴 HIGH — installment scheduling |
| `insertData()` / `updateData()` | ALL | `{any}` | 🔴 CRITICAL |

---

## `customer_model` — Customer Master

**Loaded by**: Account, Payment, Chit Reports, Settings, Billing, Estimation

| Method | Called By | Tables | Risk if Changed |
|---|---|---|---|
| `get_cust()` / `customer_data()` | Account, Payment, Estimation | `customer` + `address` | 🔴 HIGH — universal customer lookup |
| `get_entrydate()` | Account, Payment | `customer` | 🟡 MED — custom entry date |
| `update_customer_only()` | Account | `customer` | 🟡 MED |
| `format_accRcptNo()` / `getFormatFromDB()` | Account, Chit Reports | — | 🟢 LOW |

---

## `ret_billing_model` — Retail Billing Core

**Loaded by**: Billing, Tagging, Branch Transfer, Sales Transfer, Customer Order, Purchase, Retail Dashboard

| Method | Called By | Tables | Risk if Changed |
|---|---|---|---|
| `code_number_generator()` | Sales Transfer | Bill numbering | 🔴 HIGH — duplicate bills |
| `generateRefNo()` | Sales Transfer | Reference numbers | 🔴 HIGH |
| `get_branchwise_rate()` | Customer Order (AJAX), Sales Transfer | `ret_branchwise_rate` | 🟡 MED — rate accuracy |
| `get_FinancialYear()` | Sales Transfer | `ret_financial_year` | 🟡 MED |
| `insertData()` / `updateData()` | ALL loading modules | `{any}` | 🔴 CRITICAL |

---

## `log_model` — Audit Trail

**Loaded by**: ALL modules with CRUD operations

| Method | Called By | Risk if Changed |
|---|---|---|
| `log_detail()` | ALL | 🟡 LOW — audit trail, failures are silent |

---

## `admin_usersms_model` + `sms_model` — SMS Gateways

**Loaded by**: Account, Payment, Chit Reports, Settings, Billing, Tagging, Customer, Employee, Stock Issue, Branch Transfer, Section Transfer

> [!WARNING]
> SMS gateway routing (if/elseif chain over 5 gateways) is **duplicated 8+ times** across controllers. Changing one copy doesn't change others.

| Method | Risk if Changed |
|---|---|
| `sendSMS_MSG91()` / `sendSMS_Nettyfish()` / `sendSMS_SpearUC()` / `sendSMS_Asterixt()` / `sendSMS_Qikberry()` | 🟡 MED — SMS delivery failure |
| `send_whatsApp_message()` | 🟡 MED |
| `get_SMS_data()` | 🟢 LOW — template lookup |

---

## `ret_reports_model` — Report Queries (Cross-Model)

**Loaded by**: Reports, Purchase, Retail Dashboard

| Method | Called By | Risk if Changed |
|---|---|---|
| `getBillDetails()` | Retail Dashboard (cash abstract) | 🟡 MED — dashboard breaks if output changes |
| `getLedgerReportData()` | Retail Dashboard | 🔴 HIGH — ⚠️ method may not exist → Fatal error |

---

## `Wallet_model` — Wallet CRUD

**Loaded by**: Account, Payment, Customer, Employee

| Method | Called By | Risk if Changed |
|---|---|---|
| `get_wallet_acc_number()` | Customer, Employee | 🟡 MED — race condition risk |
| `wallet_accountDB('insert')` | Customer, Employee | 🟡 MED — wallet creation |
| `wallet_transactionDB()` | Payment | 🟡 MED — transaction records |
