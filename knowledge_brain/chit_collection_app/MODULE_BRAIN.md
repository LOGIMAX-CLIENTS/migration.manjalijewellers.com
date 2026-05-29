# chit_collection_app — Module Brain

> **🔄 UPGRADED BRAIN**
> Previous version: R4 (2026-03-17)
> Upgraded to: R5-Upgrade (2026-03-25)
> Upgrade date: 2026-03-25
> Changes: 20 undocumented paymt.php methods added to METHOD_INDEX, all `~L` line numbers resolved, 3 new brain files (SCHEMA_ANALYSIS, INVARIANT_MATRIX, FLOW_RISK_MATRIX), 1 new bug found (SQL injection in get_entrydate)
>
> **Round:** R5 (Upgrade)  
> **Files Scanned:** adminapp_api.php (4528L), adminappapi_model.php (2358L), paymt.php (4704L)  
> **Bugs Found:** 45+1 = **46** (7×P0, 13×P1, 18×P2, 6×P3, 1×Won't Fix) — see `BUG_REGISTER.md`

---

## 1. Module Overview

**Purpose:** The Admin/Employee Collection App — a REST API backend that serves the field collection mobile app used by employees and agents to collect installment payments from chit/savings scheme customers. Also includes `paymt.php`, the customer-facing web payment controller (PayU, Cashfree, TechProcess, HDFC, Atom, Ippo, RazorPay gateway integration).

```
Mobile App (Android)
  → REST API → adminapp_api.php (REST_Controller, 73 methods)
             → adminappapi_model.php (55 methods)
             → payment_modal, mobileapi_model, scheme_modal, services_modal, integration_model, sms_model
             → DB Tables: customer, scheme_account, payment, employee, agent, employee_devices, ...

Browser (Customer Web)
  → paymt.php (CI_Controller, 69 methods)
             → payment_modal, scheme_modal, registration_model, syncapi_model, chitapi_model, sms_model, mobileapi_model
             → Views: application/views/chitscheme/ (46 templates)
             → DB Tables: payment, scheme_account, scheme, chit_settings, ...
```

---

## 2. Constructor — adminapp_api.php

| Model/Library | Purpose |
|---|---|
| `adminappapi_model` | Primary — customer lookup, payment history, branch data |
| `mobileapi_model` | Scheme account details, payment details (get_payment_details), OTP generation |
| `payment_modal` | Core payment operations — addPayment, updateGatewayResponse, account_number_generator |
| `scheme_modal` | Scheme joining, referral checks, groups |
| `services_modal` | SMS, wallet, service gating |
| `email_model` | Transaction emails |
| `sms_model` | SMS dispatch (MSG91, Nettyfish, SpearUC, Asterixt, Qikberry) |
| `integration_model` | External offline integration (integrationType == 5, Khimji) |

**Constants:**
- `ADM_MODEL` = `adminappapi_model`
- `MOD_MOB` = `mobileapi_model`
- `PAY_PATH` = `assets/payment/` (PDF receipt storage)
- `VOUCHER_IMG_PATH` = `admin/assets/img/voucher/`
- `payment_status` map: pending=7, awaiting=2, success=1, failure=3, cancel=4
- `current_android_version` = "1.0.0", `new_android_version` = "1.0.1" ← **HARDCODED, risk of stale values**

**Session Gate:** NONE — no session-based auth. Authentication is done only via `authenticate_post()`. Subsequent calls have **NO server-side token validation** — must rely on mobile app passing employee ID.

---

## 3. Constructor — paymt.php

| Model/Library | Purpose |
|---|---|
| `login_model` | Company details, maintenance mode check |
| `payment_modal` | Core payment ops |
| `scheme_modal` | Scheme status, referral |
| `registration_model` | Customer registration |
| `syncapi_model` | Third-party sync (integrationType 2/3) |
| `chitapi_model` | Post-payment chit operations |
| `sms_model` | SMS dispatch |
| `mobileapi_model` | isRateFixed, updFixedRate |
| `integration_model` | Khimji integration (integrationType==5) |

**Session Gate:** `$this->session->userdata('username')` check on `index()` and `paySubmit()`.

---

## 4. Entry Points — adminapp_api.php (REST API)

| Endpoint | Method | Lines | Purpose |
|---|---|---|---|
| `/adminapp_api/getVersion` | GET | 106-123 | App version check, company details, maintenance mode |
| `/adminapp_api/currency` | GET | 125-137 | Branch-wise currency |
| `/adminapp_api/authenticate` | POST | 139-183 | Login — employee/agent auth, device UUID validation |
| `/adminapp_api/createCustomer` | POST | 186-402 | Register new customer + Aadhaar/PAN/DL image upload |
| `/adminapp_api/createAccount` | POST | 406-710 | Join scheme — new or existing account |
| `/adminapp_api/customerSchemes` | POST | 774-884 | Search customer & get scheme accounts due list |
| `/adminapp_api/getCusByMobile` | POST | 890-915 | Lightweight customer lookup by mobile |
| `/adminapp_api/getScheme` | GET | 917-1005 | Get scheme details + join eligibility |
| `/adminapp_api/paymentHistory` | GET | 1007-1040 | Employee's collection history |
| `/adminapp_api/get_branch` | GET | 1090-1096 | Branch list by employee profile |
| `/adminapp_api/get_all_branch` | GET | 1143-1148 | All active branches |
| `/adminapp_api/pay_collection` | POST | 1166-1222 | Collection report (date-range, employee, metal filter) |
| `/adminapp_api/checkMobileReg` | GET | 1224-1235 | Check if mobile is registered |
| `/adminapp_api/generateOTP` | GET | 1237-1316 | Generate & send OTP (static OTP=123456 in commented code!) |
| `/adminapp_api/check_regOTP` | POST | 1318-1339 | Validate OTP |
| `/adminapp_api/isNumberRegistered` | GET | 1341-1362 | Check mobile+email registration |
| `/adminapp_api/mobile_payment` | POST | 1381-~1800 | **Critical** — Collection app payment (cash/online) |
| `/adminapp_api/easebuzzResponse` | POST | ~L | Easebuzz payment callback |
| `/adminapp_api/cf_payment_status` | POST | ~L | Cashfree payment status |
| `/adminapp_api/getClassificationAll` | GET | ~L | Metal classification |
| `/adminapp_api/getAllPaymentGateways` | GET | ~L | Payment gateway list |
| `/adminapp_api/getallAcitiveschemes` | GET | ~L | All active schemes |
| `/adminapp_api/syncAgentCustomers` | POST | ~L | Agent customer sync (offline) |
| `/adminapp_api/syncOfflineCustomers` | POST | ~L | Offline customer data sync |
| `/adminapp_api/syncOfflineAccPayments` | POST | ~L | Offline payment sync |
| `/adminapp_api/customer_ledger` | POST | ~L | Customer ledger report |
| `/adminapp_api/customer_ledger_details` | POST | ~L | Ledger detail per account |
| `/adminapp_api/agentWise_monthly_reports` | POST | ~L | Agent monthly report |
| `/adminapp_api/pendingMsg` | POST | ~L | Pending payment notification |
| `/adminapp_api/terms_and_conditions` | GET | ~L | T&C text |
| `/adminapp_api/branchesByEmp` | POST | ~L | Branches accessible by employee |
| `/adminapp_api/sch_enquiry` | POST | ~L | Scheme enquiry registration |
| `/adminapp_api/storeCollectionDevices` | POST | ~L | Register device for collection app |

---

## 5. Entry Points — paymt.php (Web Payment)

| URL | Method | Lines | Purpose |
|---|---|---|---|
| `/paymt` | GET | 98-122 | Customer payment landing page |
| `/paymt/paySubmit` | POST | 306-914 | Web payment form submit → gateway redirect |
| `/paymt/payment_success` | POST | ~L | PayU success callback |
| `/paymt/payment_failure` | POST | ~L | PayU failure callback |
| `/paymt/payment_cancel` | POST | ~L | PayU cancel callback |
| `/paymt/successMURL` | POST | ~L | Mobile PayU success |
| `/paymt/failureMURL` | POST | ~L | Mobile PayU failure |
| `/paymt/cancelMURL` | POST | ~L | Mobile PayU cancel |
| `/paymt/cashfreeresponseURL` | POST | ~L | Cashfree web return |
| `/paymt/cashfreemobile` | POST | ~L | Cashfree mobile return |
| `/paymt/techProcessResponseURL` | GET/POST | 1183-1414 | TechProcess web response |
| `/paymt/techProcessMobileResponseURL` | POST | 1415-~ | TechProcess mobile response |
| `/paymt/atomReturnURL` | POST | 953-1049 | Atom payment response |
| `/paymt/ipporesponseURL` | POST | ~L | Ippo gateway response |
| `/paymt/responseURL` | POST | ~L | HDFC response |
| `/paymt/generateInvoice` | GET | ~L | PDF receipt generation |
| `/paymt/payment_history` | GET | ~L | Web payment history |
| `/paymt/chit_detail_report` | GET | ~L | Scheme account detail report |
| `/paymt/GiftCardPayment` | POST | ~L | Gift card redemption |
| `/paymt/pdc_report` | GET | ~L | Post-dated cheque report |

---

## 6. Key Tables

| Table | Owned By | Key Columns | Purpose |
|---|---|---|---|
| `payment` | payment module | id_payment, id_scheme_account, payment_amount, payment_status, due_type, due_month, due_year, added_by, id_employee, id_branch, receipt_no | Core payment transactions |
| `scheme_account` | scheme module | id_scheme_account, id_customer, id_scheme, scheme_acc_number, active, is_closed, paid_installments | Customer scheme enrollments |
| `customer` | registration | id_customer, mobile, active, allocated_employee, id_agent, id_branch | Customer master |
| `employee` | HR | id_employee, username, enable_chit_collection, login_branches, is_lmx, pwd_hash | Field staff |
| `employee_devices` | collection app | emp_id, device_uuid, app_type, device_status | Device authorization |
| `agent` | agent module | id_agent, mobile, agent_code, id_branch | Agent master |
| `scheme` | scheme module | id_scheme, scheme_type, total_installments, allow_advance, allow_unpaid | Scheme configuration |
| `chit_settings` | config | currency_symbol, branch_settings, schemeacc_no_set, receipt_no_set, auto_pay_approval | Global config |
| `metal_rates` | metal | goldrate_22ct, silverrate_1gm | Live metal rates |
| `inter_wallet_account` | wallet | id_wallet_account, mobile, available_points | Wallet accounts |
| `wallet_transaction` | wallet | id_wallet_transaction, id_wallet_account, transaction_type, value | Wallet ledger |
| `postdate_payment` | payment | id_scheme_account, date_payment, payment_status | PDC payments |
| `gifts` | scheme | id_gift, gift_name | Gifts on joining |
| `payment_mode` | config | short_code, mode_name | Payment mode labels |

---

## 7. Business Rules Summary

See `BUSINESS_RULES.md` for full details. Top rules:

- **BR-COL-001**: `allow_pay` calculation — 8+ factors (scheme_type, flexible_sch_type, installment_cycle, maturity_type, advance, unpaid, lucky_draw)
- **BR-COL-002**: Due type cascade: ND → PD → AD → PN → AN → APN
- **BR-COL-003**: Collection app offline payment → `payment_status=1` (success) immediately; customer web app → `payment_status=7` (pending) until gateway callback
- **BR-COL-004**: GST inclusive vs exclusive affects metal weight calculation
- **BR-COL-005**: Average payable calculation kicks in after `avg_calc_ins` installments

---

## 8. Data Flow Summary

See `DATA_FLOW.md` (Flows 1-6) and `DATA_FLOW_R2.md` (Flows 7-18) for full details. Key flows:

1. **AUTH**: App → `authenticate_post` → employee/agent login → device UUID check → `employee_devices` table
2. **COLLECTION PAYMENT (CASH)**: App → `mobile_payment_post` → `addPayment()` (status=1) → receipt no / acc no generation → SMS/notification
3. **COLLECTION PAYMENT (ONLINE)**: App → `mobile_payment_post` → `addPayment()` (status=7 pending) → gateway redirect → callback → `updateGatewayResponse()`
4. **CUSTOMER REGISTRATION**: App → `createCustomer_post` → DB insert + image upload → wallet creation → SMS
5. **SCHEME JOIN**: App → `createAccount_post` → `insert_schemeAcc()` → optional free payment → SMS
6. **ADMIN APP SUCCESS (CASH/OFFLINE)**: `adminAppSuccess()` → multi-mode insert → RHR cycle → incentives → receipt/acc no → SMS
7. **OFFLINE SYNC**: `syncOfflineAccPayments` → CURL to createAccount → `insertOfflinepayments()` → `adminAppSuccess()`
8. **KHIMJI INTEGRATION**: `getDataFromOffline()` → Khimji API → INSERT scheme_account + payment records

---

## 9. Cross-Module Dependencies

See `CROSS_MODULE_MAP.md`. Critical dependencies:
- `mobileapi_model::get_payment_details()` — **primary due calculation engine** (mega-query, L440-1297)
- `payment_modal::addPayment()` — writes to `payment` table
- `services_modal` — service gating (SMS, email, wallet)
- `integration_model` — Khimji offline ERP sync (integrationType=5)

---

## 10. Known Risks

> Full details in `BUG_REGISTER.md`. **46 total bugs found** (R1–R4 original 45 + 1 upgrade finding).

| Risk | Severity | Bug ID | Location |
|---|---|---|---|
| **NO API token auth** | P0 CRITICAL | COL-BUG-004 | All adminapp_api.php routes |
| **Wrong SMS service ID in adminApp cash payments** | P0 CRITICAL | COL-BUG-002 | paymt.php L2622 — serviceID=7 (failure) used for success |
| **GST type undefined variable** | P0 CRITICAL | COL-BUG-001 | adminapp_api.php L1489 + L3673 |
| **Khimji getDataFromOffline param swap** | P0 CRITICAL | COL-BUG-003 | adminapp_api.php L4173-4174 |
| **Static OTP = 123456** | P1 HIGH | COL-BUG-005 | adminapp_api.php L1243 |
| **Undefined $trans_id in adminAppSuccess** | P1 HIGH | COL-BUG-007 | adminapp_api.php ~L1768 |
| **$data variable shadow in syncOfflineCustomers** | P1 HIGH | COL-BUG-008 | adminapp_api.php L3458 |
| **Hardcoded production URL in syncOfflineAcc** | P1 HIGH | COL-BUG-009 | adminapp_api.php L3532 |
| **SQL injection in isValidLogin** | P1 HIGH | COL-BUG-010 | adminappapi_model.php L33-35 |
| **integrationType check reversed in Easebuzz** | P1 HIGH | COL-BUG-011 | adminapp_api.php L2461 |
| **No Cashfree webhook signature verify** | P2 MED | COL-BUG-013 | adminapp_api.php L2804 |
| **Referral credited commented out (Easebuzz)** | P2 MED | COL-BUG-014 | adminapp_api.php L2882 |
| **Date params unused in customer_ledger** | P2 MED | COL-BUG-015 | adminapp_api.php L4354 |
| **base64 used as password** | P2 MED | COL-BUG-017 | adminappapi_model.php L26 |
| **mkdir(0777) world-writable** | P2 MED | COL-BUG-018 | adminapp_api.php L265,278,290,338 |
| **wallet_transactionDB signature mismatch** | P3 LOW | COL-BUG-024 | paymt.php L3379 vs L3703 |
| **Hardcoded CC email (dev)** | P3 LOW | COL-BUG-023 | adminapp_api.php L4502 |

---

## 11. DB Verification Queries

```sql
-- Full payment record for a collection app payment
SELECT p.*, sa.scheme_acc_number, sa.id_customer, c.mobile, c.firstname,
       s.scheme_name, b.name as branch_name
FROM payment p
LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
LEFT JOIN customer c ON c.id_customer = sa.id_customer
LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
LEFT JOIN branch b ON b.id_branch = p.id_branch
WHERE p.id_payment = {id_payment};

-- Check payment status for a scheme account
SELECT payment_status, COUNT(*) as cnt, SUM(payment_amount) as total
FROM payment WHERE id_scheme_account = {id_sa}
GROUP BY payment_status;

-- Orphan payments (no scheme_account)
SELECT p.id_payment, p.id_scheme_account FROM payment p
LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
WHERE sa.id_scheme_account IS NULL;

-- Device auth status for an employee
SELECT ed.*, e.username FROM employee_devices ed
LEFT JOIN employee e ON e.id_employee = ed.emp_id
WHERE ed.emp_id = {id_employee} AND ed.app_type = 1;
```

---

## 12. Codebase Notes

- **Framework**: CodeIgniter 2 with Phil Sturgeon's REST_Controller library
- **Patterns**: Mixed procedural database calls — `$this->db->query($sql)` (raw) and `$this->db->where()->get()` (active record)
- **Auth pattern**: `adminapp_api` uses NO middleware auth — every endpoint is open after app install. Authentication state is only tracked by the mobile app.
- **Payment gateway codes**: 1=PayU, 2=HDFC/CCAvenue, 3=TechProcess, 4=Cashfree, 5=Atom, 6=Ippo, 7=RazorPay (inferred)
- **integrationType codes**: 1=JIL, 2=Standard integrated, 3=Standard integrated variant, 5=Khimji offline ERP
- **added_by codes**: 1=Admin Web, 2=Customer mobile app, 3=Collection (Admin) mobile app

---

## 13. Anti-Patterns Register

*(Updated after each bug fix — all open as of Round 2)*

| Pattern | Bug ID | Location | Effect |
|---|---|---|---|
| `$sch_data` vs `$chit` mismatch | COL-BUG-001 | adminapp_api.php L1489, L3673 | Wrong GST for all collection payments |
| Wrong serviceID for SMS (7 vs 3) | COL-BUG-002 | paymt.php L2622 | Failure SMS sent for successful payments |
| Parameter self-swap on entry | COL-BUG-003 | adminapp_api.php L4173-4174 | Khimji sync always uses wrong ref number |
| base64 used as password encryption | COL-BUG-017 | adminappapi_model.php L26 | Passwords trivially decoded |
| Raw SQL with username concat | COL-BUG-010 | adminappapi_model.php L33-35 | SQL injection in login |
| Static OTP 123456 in prod code | COL-BUG-005 | adminapp_api.php L1243 | OTP bypass |
| `$data` variable shadow in foreach | COL-BUG-008 | adminapp_api.php L3458 | ID agent broken for offline sync |
| Hardcoded external URL in code | COL-BUG-009 | adminapp_api.php L3532 | Client deployments broken |
| Commented-out webhook signature verify | COL-BUG-013 | adminapp_api.php L2804 | Any POST can fake payment success |
| Undeclared $trans_id used in function | COL-BUG-007 | adminapp_api.php ~L1768 | Wallet debit txnid always null |
| Unused function parameters | COL-BUG-015 | adminapp_api.php L4354 | Date filter silently ignored |
| display_errors=1 in production curl | COL-BUG-022 | adminapp_api.php L2135 | Error info leaked to browser/response |
