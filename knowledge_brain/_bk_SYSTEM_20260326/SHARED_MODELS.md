# Shared Models — Cross-Module Function Map
> Last updated: 2026-03-16
> Changing a method in any of these models affects ALL modules that load it.

> [!WARNING]
> Before modifying ANY method in a shared model, grep ALL controllers to find callers:
> ```powershell
> Select-String -Pattern "method_name" -Path "c:\xampp-7.1\htdocs\etail_development_src\admin\application\controllers\*.php"
> ```

---

## `admin_settings_model` — The Universal Hub

**Loaded by**: ALL modules (every controller loads it)

| Method | Called By Modules | What It Returns | Risk if Changed |
|---|---|---|---|
| `get_access($url)` | ALL | Bool — whether logged-in profile can access this URL | 🔴 CRITICAL — breaks all permission checks system-wide |
| `settingsDB()` / `manage_chit_settings()` | ALL | All 60+ `chit_settings` columns | 🔴 HIGH — any removed column causes PHP notice/errors in all callers |
| `metal_ratesDB()` | Payment, Account, chit_settings, Mobile API | Metal rates by date/branch | 🔴 HIGH — payment calc relies on correct rate |
| `limitDB()` | Scheme, Account | Scheme count limits per customer | 🟡 MED — affects account opening enforcement |
| `discount_db()` | Scheme | Discount configuration | 🟡 MED |
| `get_gstsettings()` | Scheme, Payment | GST rate per scheme | 🟡 MED — wrong rate → wrong GST collected |
| `get_access($url)` | ALL | Profile-based permission | 🔴 CRITICAL |
| `menu_generation()` | Admin Login | Menu configuration per profile | 🟡 MED — wrong menu = hidden features |
| `PermissionDB()` | Admin Login | Permission CRUD | 🟡 MED |
| `profileDB()` | ALL | Role profile data | 🟡 MED |
| `get_company()` | Account, Payment, chit_reports | Company info for SMS/invoice | 🟡 LOW |
| `get_branches()` | Account, Scheme, Payment | Branch list | 🟢 LOW |
| `getBranchDetails()` | Account | Single branch details | 🟢 LOW |
| `receipt_type()` | Account | Receipt type settings | 🟡 MED — affects receipt generation |
| `get_service()` | Account, chit_settings | Service configuration | 🟡 LOW |
| `get_branchcompany()` | Account | Branch-company mapping | 🟢 LOW |
| `insert_metalrate()` | chit_settings | Insert new metal rate | 🟡 MED — affects all rate-based calculations |

---

## `payment_model` — Core Financial Model

**Loaded by**: account (PAY_MODEL), chit_reports (PAY_MODEL), chit_settings, Scheme, Billing

| Method | Called By | Tables | Risk if Changed |
|---|---|---|---|
| `paymentDB()` | Payment, Account, chit_reports | `payment` | 🔴 HIGH — core CRUD |
| `payment_list*()` / `payment_list_daterange()` | chit_reports | `payment` + joins | 🟡 MED — reports |
| `get_settings()` | Payment, Account | `chit_settings` | 🔴 HIGH — config broken if column removed |
| `get_metalrate_by_branch()` | Scheme, Account, Payment | `metal_rates` | 🔴 HIGH — rate accuracy |
| `generate_receipt_no()` | Account, Payment | `payment`, `chit_settings` | 🔴 HIGH — receipt duplication risk if changed |
| `get_account_payment()` | Account | `payment`, `scheme_account` | 🟡 MED |
| `insertData($table, $data)` | ALL modules | `{any}` | 🔴 CRITICAL — universal insert method |
| `updateData($table, $data, $where_col, $where_val, $table2)` | ALL modules | `{any}` | 🔴 CRITICAL — universal update method |
| `updData($data, $col, $id, $table)` | Payment, chit_reports | `{any}` | 🔴 HIGH |
| `getMemberReport()` | chit_reports | `scheme_account`, `customer`, `scheme`, `address` | 🟡 MED |
| `payment_cancel()` | chit_reports (`cancel_payment`) | `payment` (status=4) | 🔴 HIGH — cancel flow |
| `updatePaymentdata()` | chit_reports (`updatePaymentDetails`) | `payment` | 🔴 HIGH — edit payment |
| `updateDatacus()` | chit_reports (`updateAccountDetails`) | `scheme_account` | 🔴 HIGH — edit account |
| `get_all_closed_account_by_date()` | chit_reports (`closedaccount_list`) | `scheme_account`, `payment`, `customer`, `scheme` | 🟡 MED — report query |

---

## `account_model` — Scheme Account Model

**Loaded by**: Payment, chit_reports (ACC_MODEL), chit_settings, Scheme, Billing

| Method | Called By | Tables | Risk if Changed |
|---|---|---|---|
| `get_scheme_account()` / `scheme_account*()` | Payment, chit_reports | `scheme_account` + joins | 🔴 HIGH — account lookups |
| `scheme_summary_data()` | chit_reports (`scheme_summary`) | `scheme`, `scheme_account`, `payment` × 4 queries | 🟡 MED — performance risk |
| `get_all_scheme_account_by_range()` | chit_reports (`exl_rep_outstanding`) | `scheme_account` + joins | 🟡 MED — outstanding report |
| `format_accRcptNo()` | chit_reports (via account_model → customer_model) | — | 🟢 LOW |
| `scheme_group_summary_data($id)` | chit_reports | `scheme_group` | 🟡 MED — **SQL injection: raw `$id`** |
| `is_luckly_draw_scheme($id)` | chit_reports | `scheme` | 🟡 MED — **SQL injection: raw `$id`** |
| `get_group_scheme_code($id)` | chit_reports | `scheme_group` | 🟡 MED — **SQL injection: raw `$id`** |
| `get_all_cus_celeb_dates($postData)` | chit_reports | `customer` | 🟡 MED — **cross-year date bug** |
| `get_due_date()` | Account, Payment | `payment`, `scheme_account` | 🔴 HIGH — installment scheduling |
| `generate_receipt_no()` | Account, Payment | `payment`, `chit_settings` | 🔴 HIGH |
| `sch_acc_count()` | chit_settings | `scheme_account` | 🟢 LOW |
| `insertData()` / `updateData()` | ALL | `{any}` | 🔴 CRITICAL |

---

## `customer_model` — Customer Master

**Loaded by**: Account, Payment, chit_reports, chit_settings, Billing, Estimation

| Method | Called By | Tables | Risk if Changed |
|---|---|---|---|
| `get_cust()` / `customer_data()` | Account, Payment, Estimation | `customer` + `address` | 🔴 HIGH — universal customer lookup |
| `get_entrydate()` | Account, Payment | `customer` | 🟡 MED — affects custom entry date logic |
| `update_customer_only()` | Account | `customer` | 🟡 MED |
| `format_accRcptNo()` / `getFormatFromDB()` | Account, chit_reports | — | 🟢 LOW |
| `customer_count()` | chit_settings | `customer` | 🟢 LOW |

---

## `log_model` — Audit Log

**Loaded by**: Account, Payment, chit_reports, Scheme, Tagging, Billing

| Method | Called By | Tables | Risk if Changed |
|---|---|---|---|
| `log_detail()` | Account, Payment, Scheme, Tagging | `log_detail` | 🟡 LOW — audit trail only |
| `get_log_detail_range()` | chit_reports | `log_detail` | 🟢 LOW |
| `get_form_logger_log_list()` | chit_reports | `log_detail` (or form_log) | 🟢 LOW |

---

## `admin_usersms_model` + `sms_model` — SMS Gateways

**Loaded by**: Account, Payment, chit_reports, chit_settings, Billing, Tagging

> [!WARNING]
> Both models are loaded by virtually every module. The SMS gateway routing (if/elseif chain over 5 gateways) is **duplicated 8+ times** across Payment and Account controllers. Changing one copy doesn't change others.

| Method | Called By | Risk if Changed |
|---|---|---|
| `sendSMS_MSG91()` / `sendSMS_Nettyfish()` etc. | ALL | 🟡 MED — SMS delivery failure |
| `send_whatsApp_message()` | Account | 🟡 MED |
| `get_SMS_data()` | Account, Payment, chit_reports | 🟢 LOW — template lookup |
| `check_noti_settings()` | Account | 🟢 LOW |
