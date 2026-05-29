# Payment Module Brain
> **Built**: 2026-03-06 | **Round**: 2 | **Status**: ✅ Complete (100% Coverage)

## 1. Module Overview

The Payment module is the **core financial transaction engine** of the eTail CRM platform. It handles all scheme payment processing — manual payments, online gateway payments (PayU, Razorpay, Cashfree, HDFC, EaseBuzz, TechProcess), post-dated cheques, general advance payments, wallet redemptions, weight settlements, and payment verification/approval workflows.

### File Map

| File | Path | Lines | Bytes | Purpose |
|---|---|---|---|---|
| Controller | `admin/application/controllers/admin_payment.php` | 6,237 | 416KB | All payment entry points — CRUD, gateway integrations, settlement, verification |
| Model | `admin/application/models/payment_model.php` | 8,891 | 590KB | DB queries, CRUD ops, reports, receipt generation, account lookups |
| Account Model | `admin/application/models/account_model.php` | — | — | Account number generation, financial year, customer account ops |
| Services Ctrl | `admin/application/controllers/admin_services.php` | 7,776 | 223KB | SMS/notification/WhatsApp services, due alerts, birthday reminders |
| JS (Main) | `admin/assets/js/payment.js` | ~10K+ | 357KB | Payment form logic, AJAX calls, mode switching, validation |
| JS (Entry) | `admin/assets/js/payment_entry.js` | ~300 | 9KB | Payment entry form helpers |
| JS (Online) | `admin/assets/js/online_payments.js` | ~400 | 13KB | Online payment list/verify UI |
| JS (PDC) | `admin/assets/js/postdated_payment.js` | ~800 | 27KB | Post-dated cheque form/list |
| JS (Settled) | `admin/assets/js/settled_payments.js` | ~120 | 4KB | Settled payments view |
| JS (Verify) | `admin/assets/js/verify_payment.js` | ~300 | 10KB | Payment verification UI |
| Frontend API | `application/controllers/mobile_api.php` | — | — | Mobile app payment endpoints |
| Frontend API | `application/controllers/digigold.php` | — | — | DigiGold payment flow |
| Frontend Model | `application/models/digigold_modal.php` | — | — | DigiGold DB operations |
| Frontend Model | `application/models/mobileapi_model.php` | — | — | Mobile API payment queries |
| Frontend Model | `application/models/payment_modal.php` | — | — | Frontend payment DB ops |

### View Files (15 total)

| View | Path | Size | Purpose |
|---|---|---|---|
| `form.php` | `views/payment/` | 66KB | Main payment add form |
| `list.php` | `views/payment/` | 41KB | Payment list page |
| `edit_form.php` | `views/payment/` | 13KB | Payment edit form |
| `status_form.php` | `views/payment/` | 8KB | Payment status view |
| `online_payments.php` | `views/payment/` | 9KB | Online payments list |
| `verify_payment.php` | `views/payment/` | 7KB | Payment verification |
| `sync_settled_txns.php` | `views/payment/` | 5KB | Synced settled transactions |
| `list_branch.php` | `views/payment/` | 6KB | Branch-wise payment list |
| `insertTrans.php` | `views/payment/` | 12KB | Insert transaction form |
| `postdated/form.php` | `views/payment/postdated/` | 13KB | PDC add form |
| `postdated/list.php` | `views/payment/postdated/` | 6KB | PDC list |
| `postdated/entry_form.php` | `views/payment/postdated/` | 10KB | PDC entry form |
| `postdated/payment.php` | `views/payment/postdated/` | 7KB | PDC payment view |
| `postdated/payment_status.php` | `views/payment/postdated/` | 8KB | PDC status |
| `paymentdata/pay_list.php` | `views/payment/paymentdata/` | 6KB | Payment data list |

### Connection Flow
```
Browser → JS (payment.js + 5 others)
       → AJAX → Controller (admin_payment.php, 93 methods)
       → Model (payment_model.php, 278 methods)
       → DB (20+ tables — see §6)
       → View (15 files in payment/)
       → Browser
```

## 2. Constructor Analysis

### Loaded Models (admin_payment.php L26-65)

| Constant | Model | Purpose |
|---|---|---|
| `PAY_MODEL` | `payment_model` | **Primary** — All payment CRUD, queries, reports |
| `API_MODEL` | `syncapi_model` | Sync API integration |
| `CHITAPI_MODEL` | `chitapi_model` | Chit API operations |
| `WALL_API_MOD` | `sync_walletapi_model` | Wallet sync API |
| `ACC_MODEL` | `account_model` | Account number generation, OTP, financial year |
| `SET_MODEL` | `admin_settings_model` | Settings, pay modes, bank info, company config |
| `SMS_MODEL` | `admin_usersms_model` | SMS data retrieval |
| `ADM_MODEL` | `chitadmin_model` | Admin/profile settings |
| `LOG_MODEL` | `log_model` | Activity logging |
| `MAIL_MODEL` | `email_model` | Email sending |
| `WALL_MODEL` | `Wallet_model` | Wallet operations |
| `CUS_MODEL` | `customer_model` | Customer data, entry dates |
| `CHIT_MODEL` | `chitadmin_model` | Duplicate of ADM_MODEL |
| — | `sms_model` | SMS gateway sending (MSG91, Nettyfish, SpearUC, Asterixt, Qikberry) |
| — | `digigold_modal` | DigiGold account/benefit operations |
| — | `chit_transaction_model` | Chit transaction operations (AB-2407) |

### Session Gate (L49-51)
```php
if (!$this->session->userdata('is_logged')) {
    redirect('admin/login');
}
```

### Instance Variables
- `$this->employee` — UID from session
- `$this->company` — Company settings from DB
- `$this->branch_settings` — Branch settings flag
- `$this->id_log` — Log session ID
- `$this->payment_status` — Status code map: pending=7, awaiting=2, success=1, failure=3, cancel=4, refund=6
- `$this->log_dir` — `log/YYYY-MM-DD/` (auto-created)

## 3. Entry Points (Routes)

### Payment CRUD Routes

| URL Path | HTTP | Controller Method | Purpose |
|---|---|---|---|
| `/payment/add` | GET | `payment/View/` | Load add payment form |
| `/payment/edit/{id}` | GET | `payment/View/$1` | Load edit payment form |
| `/payment/save` | POST | `payment/Save/` | Save single payment |
| `/payment/save_all` | POST | `payment/SaveAll/` | Save with multi-installment support |
| `/payment/update/{id}` | POST | `payment/Update/$1` | Update existing payment |
| `/payment/delete/{id}` | GET | `payment/Delete/$1` | Delete payment ⚠️ GET=CSRF risk |
| `/payment/list` | GET | `payment/List` | Payment list page |
| `/payment/ajax_list/range` | POST | `ajax_payment_range` | Filtered list by date range |
| `/payment/invoice/{id}` | GET | `generateInvoice/$1` | Generate PDF invoice |
| `/payment/status/{id}` | GET | `payment/Status/$1` | View payment status log |
| `/payment/edit_payment/{id}` | POST | `payment/Edit_payment/$1` | AJAX get payment for edit |
| `/payment/update_payment/{id}` | POST | `payment/Update_payment/$1` | AJAX update payment |

### Online Payment Routes

| URL Path | HTTP | Controller Method | Purpose |
|---|---|---|---|
| `/verify/online/payment` | GET | `verify_payment_view` | Verify payment page |
| `/get/online/payment` | POST | `ajax_online_payment` | AJAX get online payment data |
| `/ajax/online/payment` | POST | `ajax_onlinePayments` | AJAX online payments list |
| `/online/payment` | GET | `online_payment_list` | Online payment list page |
| `/online/payment/update_status` | POST | `update_pay_status` | Update online payment status |
| `/online/get/ajax_payment/{id}` | GET | `ajax_get_payment/$1` | AJAX get single online payment |

### Post-Dated Cheque Routes

| URL Path | HTTP | Controller Method | Purpose |
|---|---|---|---|
| `/postdated/payment/add` | GET | `postdate_payment/View/` | PDC add form |
| `/postdated/payment/save` | POST | `postdate_payment/Save/` | Save PDC |
| `/postdated/payment/list` | GET | `postdate_payment/List` | PDC list |
| `/postdated/payment_entry/edit/{id}` | GET | `postdate_payment_form/Edit/$1` | PDC entry edit |
| `/postdated/payment_entry/save/{id}` | POST | `postdate_payment_form/Update/$1` | PDC entry update |

### Settlement Routes

| URL Path | HTTP | Controller Method | Purpose |
|---|---|---|---|
| `/settlement/weight/add` | GET | `weight_settlement/View` | Weight settlement form |
| `/settlement/weight/list` | GET | `weight_settlement/List` | Weight settlement list |
| `/settlement/update/account` | POST | `update_settlement` | Update settlement account |

## 4. Model Methods Summary

**Total: 278 methods** — See [METHOD_INDEX.md](METHOD_INDEX.md) for full alphabetical listing.

| Category | Count | Key Methods |
|---|---|---|
| Payment CRUD | ~15 | `paymentDB`, `payment_statusDB`, `postdated_paymentDB`, `insertData`, `insertBatchData`, `updateData` |
| Payment Lists/Reports | ~40 | `payment_list`, `payment_list_range`, `payment_online_range`, `onlinePayments`, `payment_datewise` |
| Receipt Number Gen | ~5 | `get_receipt_no`, `get_receipt_no_settings`, `get_rptnosettings` |
| Account/Customer Lookup | ~20 | `get_paymentContent`, `get_account_detail`, `get_customer_schemes`, `wallet_balance` |
| Settlement | ~10 | `weight_settlementDB`, `insert_settlement_detail`, `view_settlement_detail` |
| Referral/Incentive | ~15 | `checkReferalExist`, `cus_refferl_account`, `empreferral_account`, `calcIncentiveAmt` |
| Gateway/Online | ~10 | `getGatewayData`, `updateGatewayResponse`, `getOrderIds`, `getRazorOrderid` |
| Settings/Config | ~10 | `get_settings`, `checkSettings`, `allow_wallet`, `get_ret_settings`, `get_gstsettings` |
| Report/Analytics | ~25 | `getMemberReport`, `getSchemeWiseSummaryDetails`, `getModeWiseummaryDetails` |
| Generic CRUD | ~5 | `insertData`, `insertBatchData`, `updateData`, `updData`, `update_data` |

## 4b. DB Truth Protocol

See [DB_TRUTH_PROTOCOL.sql](DB_TRUTH_PROTOCOL.sql) — 11 sections, 30+ diagnostic queries.

| Section | Queries | Purpose |
|---|---|---|
| A: Entity-Level | 4 | Full payment record, mode details, status audit, advance utilization |
| B: Integrity | 8 | Amount vs mode sum, installment count, duplicate receipts, orphans |
| C: GST & Weight | 2 | GST amount validation, weight calculation validation |
| D: Gateway/Online | 4 | Stuck payments, mismatched gateway, settled vs unsettled, config check |
| E: PDC | 2 | PDC status mismatch, approaching maturity |
| F: Wallet | 2 | Missing wallet transactions, balance vs history mismatch |
| G: General Advance | 2 | GA integrity, GA amount vs mode details |
| H: Settlement | 1 | Settlement header vs detail mismatch |
| I: Referral/Incentive | 2 | Referral records, agent/employee incentive records |
| J: Configuration | 3 | Current chit_settings, active schemes, gateway-branch mapping |
| K: Daily Summary | 3 | Today's payments by mode, branch, and scheme |

## 5. Data Flow Summary

See [DATA_FLOW.md](DATA_FLOW.md) for detailed end-to-end traces.

### High-Level Flows
1. **Manual Payment (SaveAll)** — JS collects multi-mode data → AJAX POST → `payment/SaveAll` → GST calc → metal weight calc → branch logic → receipt gen → INSERT `payment` + `payment_mode_details` → wallet/referral/incentive → SMS/Email → response
2. **General Advance** — Similar to SaveAll but targets `general_advance_payment` table
3. **Online Payment** — Gateway callback → `verify_*` methods → status update → receipt gen → sync
4. **PDC** — Manual entry → `postdate_payment` table → status update → on success, converts to regular payment
5. **Edit Payment** — Load existing → update `payment` table → soft-delete old mode details (`is_active=0`) → insert new mode details
6. **Delete** — GET request ⚠️ → hard delete from `payment` table

## 6. Key Tables

| Table | Alias | Purpose | Key Columns |
|---|---|---|---|
| `payment` | PAY_TABLE | **Primary** — All payment records | `id_payment`, `id_scheme_account`, `payment_amount`, `payment_mode`, `payment_status`, `receipt_no`, `metal_rate`, `metal_weight` |
| `scheme_account` | ACC_TABLE | Customer scheme accounts | `id_scheme_account`, `id_customer`, `id_scheme`, `scheme_acc_number` |
| `customer` | CUS_TABLE | Customer master | `id_customer`, `firstname`, `lastname`, `mobile`, `email` |
| `scheme` | SCH_TABLE | Scheme definitions | `id_scheme`, `scheme_type`, `gst`, `gst_type`, `total_installments` |
| `payment_mode` | MOD_TABLE | Payment mode master | `id_mode` |
| `daily_collection` | DC_TABLE | Daily collection records | — |
| `settlement` | SETT_TABLE | Settlement records | — |
| `settlement_detail` | SETT_DET_TABLE | Settlement line items | — |
| `payment_status_message` | PAY_STATUS | Status code lookup | `id_status_msg`, `payment_status`, `color` |
| `branch` | BRANCH | Branch master | `id_branch`, `name`, `short_name` |
| `employee` | EMPLOYEE_TABLE | Employee master | `id_employee`, `firstname`, `emp_code` |
| `postdate_payment` | — | Post-dated cheque storage | `id_post_payment`, `cheque_no`, `amount`, `payment_status` |
| `payment_status` | — | Payment status change log | `id_payment_status`, `id_payment`, `id_status_msg` |
| `payment_mode_details` | — | Multi-mode payment splits | `id_pay_mode_details`, `id_payment`, `payment_mode`, `payment_amount` |
| `general_advance_payment` | — | General advance payments | `id_adv_payment`, `payment_amount` |
| `general_advance_mode_detail` | — | GA mode splits | `id_adv_payment`, `payment_mode` |
| `ret_advance_utilized` | — | Advance adjustment tracking | `id_issue_receipt`, `id_payment`, `utilized_amt` |
| `chit_settings` | — | Global settings | `scheme_wise_receipt`, `receipt_no_set`, `has_lucky_draw` |
| `gateway` | — | Payment gateway config | `id_pg`, `pg_code` |
| `inter_wallet_account` | — | Inter-wallet accounts | `mobile`, `available_points` |

## 7. Payment Modes

| Code | Description |
|---|---|
| `CSH` | Cash |
| `CC` | Credit Card |
| `DC` | Debit Card |
| `CHQ` | Cheque |
| `NB` | Net Banking |
| `VCH` | Voucher |
| `ADV_ADJ` | Advance Adjustment |
| `REF_WALLET` | Referral Wallet |
| `MULTI` | Multiple modes combined |
| `Wallet` | Wallet Payment |

## 8. Payment Status Codes

| Code | Status |
|---|---|
| 1 | Success |
| 2 | Awaiting Approval |
| 3 | Failure |
| 4 | Cancelled |
| 6 | Refund |
| 7 | Pending |

## 9. Payment Gateways Integrated

| Gateway | Verify Method | Settlement Method |
|---|---|---|
| PayU | `verify_PayUpayments` (L4633) | — |
| Razorpay | `verifyRazorPayments` (L4173) | — |
| Cashfree | `verify_cashfreepayment` (L3846) | `cashfreeSettlement` (L5348) |
| EaseBuzz | `verify_easebuzzpayment` (L4346) | — |
| HDFC | `verify_hdfcpayment` (L4710) | `hdfcSettlement` (L5441) |
| TechProcess | `verifyWithTechProcess` (L4820) | — |

## 10. Business Rules Summary

See [BUSINESS_RULES.md](BUSINESS_RULES.md) for full rule catalog — **28 rules documented**.

**Core Rules** (16): GST calculation (inclusive/exclusive), metal weight conversion, receipt number generation (7 modes), branch-wise payment routing, wallet redemption limits, installment counting, advance/pending due logic, referral benefits, discount calculation, DigiGold benefits, average payable calculation, payment chances, PDC conversion, financial year receipt.

**Extended Rules** (12): OTP verification, SMS gateway routing, payment approval workflow, thermal receipt generation, split payment logic, gateway verification routing, dynamic payment mode auto-insert, CSRF protection, custom entry dates, payment activity logging, auto-pay approval, revert approval.

## 11. Cross-Module Dependencies

See [CROSS_MODULE_MAP.md](CROSS_MODULE_MAP.md) for details.

**Key Dependencies**: Settings, Customer, Account, Scheme, Billing (device/bank details), SMS/Notification, DigiGold, Wallet, Tagging/Estimation, Sync APIs.

## 12. Known Risks & Bugs

| # | Risk | Severity | Location |
|---|---|---|---|
| 1 | **DELETE via GET** — `payment/delete/{id}` uses GET, CSRF vulnerable | 🔴 HIGH | Routes L372 |
| 2 | **SQL Injection** — Raw `$id` in many model queries without escaping | 🔴 HIGH | Model throughout |
| 3 | **Duplicate model load** — `chitadmin_model` loaded as both `ADM_MODEL` and `CHIT_MODEL` | 🟡 LOW | Controller L19,25 |
| 4 | **Typo in array key** — `$pay['payee_ifsc]']` has misplaced bracket | 🔴 HIGH | Controller L308 |
| 5 | **No transaction wrapping** — Delete, PDC update, some verify methods lack `trans_begin/commit` | 🔴 HIGH | Various |
| 6 | **Massive controller** — 6,237 lines, 93 methods — high maintenance risk | 🟡 MED | Controller |
| 7 | **Massive model** — 8,891 lines, 278 methods — high maintenance risk | 🟡 MED | Model |
| 8 | **Payment mode logic duplication** — Mode detection logic duplicated 3+ times | 🟡 MED | Controller L410-435, L848-892, L1333-1385 |
| 9 | **Hardcoded SMS gateway IDs** — 5 gateways checked via if/elseif chain, duplicated ~8 times | 🟡 LOW | Controller throughout |
| 10 | **payment.js is 357KB** — Extremely large, difficult to maintain | 🟡 MED | JS |
| 11 | **OTP comparison bug** — `=` (assignment) instead of `==` at L5081, always evaluates true | 🔴 HIGH | Controller L5081 |
| 12 | **OTP returned in response** — `generateotp()` sends OTP in JSON response | 🔴 HIGH | Controller L5067 |
| 13 | **No receipt number locking** — `LOCK TABLES` commented out, race condition risk | 🔴 HIGH | Model L42-43 |
| 14 | **Division by zero** — `amount_to_weight()` doesn't check for `metal_rate=0` | 🟡 MED | Controller L2480-2484 |
| 15 | **No gateway fallback** — `verify_payment()` has no default case for unknown `pg_code` | 🟡 MED | Controller L3788-3803 |
| 16 | **Revert doesn't cleanup** — `revertApproval()` doesn't revert receipt, wallet, or referral | 🟡 MED | Controller L3686-3743 |

## 13. Anti-Patterns Register

| # | Anti-Pattern | Where | Impact | Priority |
|---|---|---|---|---|
| 1 | God method — `payment()` handles 9 cases in one 2000-line switch | Controller L382-2407 | Untestable, hard to debug | 🟡 MED |
| 2 | Magic numbers — Status codes (1,2,3,4,6,7) used inline without constants | Controller/Model throughout | Confusion, typos | 🟡 LOW |
| 3 | Copy-paste SMS logic — Same 10-line if/elseif block repeated 8+ times | Controller L254-265 etc | Maintenance burden, missed updates | 🟡 MED |
| 4 | Generic CRUD methods — `insertData`, `updateData` accept any table/data | Model | No validation, no type safety | 🟡 LOW |
| 5 | Mixed concerns — Controller does DB queries directly (`get_settings` L78-83, `get_status` L6180-6184) | Controller | Breaks MVC pattern | 🟡 LOW |
| 6 | No input validation — POST data used directly without sanitization in several methods | Controller | Security risk, data integrity | 🔴 HIGH |
| 7 | Debug code in production — `print_r()`, `exit`, commented `echo` statements throughout | Controller/Model | Information leakage risk | 🟡 LOW |
| 8 | Inconsistent naming — `digigold_modal` (typo: should be `model`), mixed camelCase/snake_case | Codebase | Confusion, autocomplete issues | 🟡 LOW |

## 14. Receipt Number Generation Modes

| Mode | `scheme_wise_receipt` | Description |
|---|---|---|
| 1 | Default | Common receipt number across all |
| 2 | Branch-wise | Receipt per branch |
| 3 | Scheme-wise | Receipt per scheme |
| 4 | Scheme + Branch | Receipt per scheme per branch |
| 5 | Financial Year | Receipt per financial year |
| 6 | FY + Scheme + Branch | Most granular |
| 7 | FY + Branch | Financial year per branch |

## 15. Brain Components — Completion Tracker

| # | Component | File | Status | Coverage |
|---|---|---|---|---|
| 1 | Project Skeleton | [MODULE_BRAIN.md](MODULE_BRAIN.md) §1-3 | ✅ Complete | File map, connection flow, constructor, routes |
| 2 | Engine Reverse Engineering | [DATA_FLOW.md](DATA_FLOW.md) | ✅ Complete | 13 flows: CRUD, GA, PDC, Online, Approval, Invoice, OTP, Revert, Settlement, Retry, Cashfree detailed |
| 3 | Canonical Business Rules | [BUSINESS_RULES.md](BUSINESS_RULES.md) | ✅ Complete | 28 rules covering all payment calculations and behaviors |
| 4 | Cross-Module Mapping | [CROSS_MODULE_MAP.md](CROSS_MODULE_MAP.md) | ✅ Complete | 14 module dependencies, mermaid graph, 12 JS AJAX cross-calls, table map |
| 5 | Invariant Matrix | [INVARIANT_MATRIX.md](INVARIANT_MATRIX.md) | ✅ Complete | 9 dimensions, 4 behavior grids, 12 edge cases, config controls |
| 6 | DB Truth Protocol | [DB_TRUTH_PROTOCOL.sql](DB_TRUTH_PROTOCOL.sql) | ✅ Complete | 11 sections, 30+ diagnostic queries |
| 7 | Forensic Template | [FORENSIC_TEMPLATE.md](FORENSIC_TEMPLATE.md) | ✅ Complete | 10 layers including gateway-specific debugging, performance hotspots, 20-row bug lookup table |
| — | Method Index | [METHOD_INDEX.md](METHOD_INDEX.md) | ✅ Complete | 93 controller methods, 278 model methods, 54 JS AJAX calls |
| — | Schema Analysis | [SCHEMA_ANALYSIS.md](SCHEMA_ANALYSIS.md) | ✅ Complete | 11 owned tables, 15 referenced tables, SQL injection risks |

> **Total Coverage: 100%** — All 7 required brain components + 2 supplementary documents complete.
