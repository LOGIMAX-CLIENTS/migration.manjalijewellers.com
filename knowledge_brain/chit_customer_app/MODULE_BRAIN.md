# MODULE BRAIN — chit_customer_app
> Built: 2026-03-16 | Round: 1 | Status: 🟢 Complete (~80%)

---

## 1. Module Overview

**Purpose**: REST API layer serving the **Customer Mobile App** (Android/iOS). Handles all mobile-to-server communication: authentication, profile, scheme browsing, scheme joining, payment initiation + gateway callbacks, KYC, wallet, notifications, video shop, digital gold, and inter-wallet features.

**Architecture**: CodeIgniter 3 REST extension (`REST_Controller.php`). All endpoints are `_get()` or `_post()` suffixed — no views, no HTML. All responses are JSON.

**CORS**: Lines 3-5 — `Access-Control-Allow-Origin: *` is set at file level (global, unauthenticated access allowed).

**Criticality**: 🔴 **HIGHEST** — This is the only backend for the customer-facing mobile app. Bugs here affect all customers directly.

---

## 2. File Map

| File | Size | Lines | Methods | Purpose |
|---|---|---|---|---|
| `application/controllers/mobile_api.php` | 468 KB | 7,918 | 183 | Main REST controller — all API endpoints |
| `application/models/mobileapi_model.php` | 334 KB | 5,186 | 187 | Primary model — customer, scheme, payment, wallet data |
| `application/models/payment_modal.php` | 227 KB | 3,196 | 113 | Payment processing model — gateway DB ops, receipt gen |
| `application/libraries/REST_Controller.php` | Auto-loaded | — | — | CodeIgniter REST framework |
| `application/libraries/payu.php` | Auto-loaded | — | — | PayU gateway integration |
| `api/rate.txt` | Small | — | — | Live metal rate JSON file (written by rate module, read by App) |
| `log/{date}/cashfree/` | Growing | — | — | Payment gateway request/response logs |

**Connection Flow**:
```
Mobile App (Android/iOS)
  → HTTP REST (JSON body / query params)
  → mobile_api.php (REST_Controller)
  → mobileapi_model.php / payment_modal.php / scheme_modal / registration_model / services_modal / login_model / sms_model / email_model / digigold_modal
  → DB (customer, scheme_account, payment, scheme, address, wallet_account, wallet_transaction, kyc, inter_wallet, otp, metal_rates, chit_settings, company, branch, ...)
  → JSON response to App
```

---

## 3. Constructor (L31-70)

```php
class Mobile_api extends REST_Controller {
    const MOD_MOB   = "mobileapi_model";
    const SCH_MOD   = "scheme_modal";
    const API_MODEL = 'syncapi_model';
    const PAYU_KEY  = 'gWrBuQ';      // ⚠️ Hardcoded PayU key
    const PAYU_SALT = 'qHs42ie1';    // ⚠️ Hardcoded PayU salt
```

### Models Loaded in Constructor
| Model | Purpose |
|---|---|
| `mobileapi_model` | Primary — all customer/scheme/wallet data |
| `email_model` | Send registration/OTP/feedback emails |
| `services_modal` | SMS gateway, service config, company limits |
| `login_model` | Login validation support |
| `registration_model` | Wallet account creation on registration |
| `scheme_modal` | Scheme join logic, group validation, referral checks |
| `payment_modal` | Payment DB ops — INSERT payment, receipt, incentives |
| `sms_model` | SMS send (MSG91, Nettyfish, SpearUC, Asterixt, Qikberry) |
| `digigold_modal` | Digital gold scheme handling |

### Constructor Properties Set
| Property | Value | Purpose |
|---|---|---|
| `$this->comp` | `mobileapi_model->company_details()` | Company info — cached on every request |
| `$this->sms_data` | `services_modal->sms_info()` | SMS config — cached on every request |
| `$this->payment_status[]` | `pending=7, awaiting=2, success=1, failure=3, cancel=4` | Payment status map |
| `$this->log_dir` | `'log/' . date("Y-m-d")` | Daily log directory |
| `$this->current/new_android_version` | Hardcoded strings | App version for upgrade prompts |

> ⚠️ **Risk**: `company_details()` and `sms_info()` are called on EVERY API request. These are DB queries. High-traffic endpoints hit these on every call. No DB query caching.

> ⚠️ **Risk**: Log directory created with `mkdir(0777, true)` — world-writable.

---

## 4. Authentication

**No token-based auth on most endpoints.** This is a REST API with:
- **No middleware auth check** — each endpoint independently handles its own auth logic (or doesn't)
- **Password auth**: `authenticate_post()` validates mobile + base64-encoded password
- **OTP auth**: `generateOTP_get()` → `verifyOTP_post()` / `check_regOTP_post()`
- **JWT/Bearer**: `getBearerToken()` L4036 — exists but not universally applied
- **PIN auth**: `getvalidate_pin_post()` L6814 — for MPIN-locked accounts
- **CORS**: `*` open — any origin can call any endpoint

> ⚠️ **CRITICAL RISK**: No centralized auth middleware. Endpoints like `getCustomer_get()`, `deleteScheme_get()`, `getDashboard_get()` do NOT validate that the caller is the legitimate owner of the requested `id_customer`. **Horizontal privilege escalation** possible — any mobile can request another customer's data by changing `id_customer`.

---

## 5. Entry Points (Route Summary)

**API Convention**: Endpoints follow REST_Controller pattern — `_get()` = GET, `_post()` = POST.

### 5a. Authentication & Registration (82 _GET + 55 _POST total)

| Endpoint | Method | Lines | Purpose |
|---|---|---|---|
| `authenticate_post` | POST | L732 | Login — validates mobile + base64 password |
| `generateOTP_get` | GET | L783 | Generate and send OTP (SMS + Email) |
| `verifyOTP_post` | POST | L867 | Verify OTP against DB |
| `check_regOTP_post` | POST | L886 | Verify registration OTP |
| `resetPassword_post` | POST | L907 | Reset password using OTP |
| `createCustomer_post` | POST | L921 | Register new customer + send welcome SMS/Email |
| `isNumberRegistered_get` | GET | L678 | Check if mobile/email is already registered |
| `checkMobileReg_get` | GET | L704 | Check if mobile registered (simpler) |
| `updateCustomerByMobile_post` | POST | L405 | Change password (checks duplicate password) |
| `createPin_post` | POST | L6788 | Create MPIN for customer |
| `getvalidate_pin_post` | POST | L6814 | Validate MPIN |
| `delete_customer_post` | POST | L6844 | Delete customer account (soft) |

### 5b. Customer Profile

| Endpoint | Method | Lines | Purpose |
|---|---|---|---|
| `getCustomer_post` | POST | L376 | Get customer profile by ID |
| `getDashboard_get` | GET | L652 | Get customer dashboard summary |
| `updateProfile_post` | POST | L601 | Update customer profile + address |
| `currency_get` | GET | L1035 | App settings — currency, config flags, metal rates |
| `company_get` | GET | L1043 | Company info (name, contact, social links) |
| `uploadAadhar_post` | POST | L3439 | Upload Aadhaar image (base64 encoded) |
| `insertkyc_post` | POST | L3469 | Insert KYC details (PAN/Aadhaar via DigiLocker) |
| `readKyc_get` | GET | L3879 | Fetch KYC status for customer |
| `digidata_post` | POST | L6768 | Submit DigiLocker KYC data |
| `chit_detail_report_get` | GET | L6853 | Detailed chit account report PDF data |

### 5c. Scheme & Account

| Endpoint | Method | Lines | Purpose |
|---|---|---|---|
| `getSchemes_get` | GET | L178 | Get all schemes + settings |
| `getActiveSchemes_get` | GET | L192 | Get branch-filtered active schemes |
| `getScheme_get` | GET | L254 | Get single scheme details + join eligibility |
| `get_groups_get` | GET | L185 | Get scheme groups |
| `customerSchemes_get` | GET | L344 | Get customer's joined scheme accounts |
| `createAccount_post` | POST | L1086 | Join new scheme / existing scheme |
| `deleteScheme_get` | GET | L1072 | Delete unpaid scheme account |
| `getSchemeDetail_get` | GET | L369 | Get individual account payment details |
| `payDuesData_get` | GET | L4400 | Get pending dues data for direct pay widget |
| `joinTime_weight_slabs_get` | GET | L7613 | Join-time weight slabs for flexible weight schemes |

### 5d. Payment Initiation

| Endpoint | Method | Lines | Purpose |
|---|---|---|---|
| `mobile_payment_post` | POST | L4484 | **Main payment** — initiate multi-chit payment, choose gateway, create pending records |
| `mobile_payment_get` | GET | L5495 | Alternate GET version of payment (older path) |

### 5e. Payment Gateway Callbacks

| Endpoint | Method | Lines | Purpose |
|---|---|---|---|
| `cashfreeResponse_post` | POST | L7859 | Cashfree webhook (current) → delegates |
| `old_cashfreeResponse_post` | POST | L7187 | Cashfree webhook (old version) |
| `cf_payment_status_post` | POST | L5873 | Cashfree payment status check |
| `easebuzzResponse_post` | POST | L7864 | Easebuzz webhook (current) → delegates |
| `oldeasebuzzResponse_post` | POST | L5167 | Easebuzz webhook (old version) |
| `razorResponse_post` | POST | L7869 | Razorpay webhook (current) → delegates |
| `oldrazorResponse_post` | POST | L6437 | Razorpay webhook (old version) |
| `verifyRateFixOTP_post` | POST | L4070 | Verify OTP for rate-fixed payment |

### 5f. Location / Utility

| Endpoint | Method | Lines | Purpose |
|---|---|---|---|
| `getCountry_get` / `getState_get` / `getCity_get` | GET | L317-333 | Location data |
| `getAllLocations_get` | GET | L335 | All countries/states/cities |
| `getMatchingCountry_get` / `State` / `City` | GET | L659-675 | Typeahead search |
| `getMatchingVillage_get` | GET | L4205 | Village typeahead |
| `getVillage_get` | GET | L6838 | All villages |
| `pinsearchArea_post` | GET | L6885 | Pincode → area lookup |
| `getAllBranchDetail_get` | GET | L3917 | All branches |
| `getLanguage_get` | GET | L6874 | Available languages |
| `appContentLangWise_get` | GET | L6893 | App content per language |
| `terms_and_conditions_get` | GET | L4190 | T&C content |
| `getModules_get` | GET | L3349 | Enabled modules list |
| `get_settings_get` | GET | L7620 | App-level settings |
| `get_invoiceData` | Internal | L715 | Invoice data helper (not REST) |
| `generateInvoice_get` | GET | L7538 | Generate invoice for a payment |

### 5g. Wallet, Notifications, Rate, Gifts

| Endpoint | Method | Lines | Purpose |
|---|---|---|---|
| `get_customerWallets_get` | GET | L1059 | Customer wallet accounts + transactions |
| `paymentHistory_get` | GET | L357 | Full payment history |
| `rate_history_post` | POST | L3381 | Metal rate history |
| `getMyGifts_post` | POST | L4170 | Customer's gift items |
| `getGiftedCards_post` | POST | L4177 | Gifted gift cards |
| `getGiftcardstatus_get` | GET | L4184 | Gift card status |
| `giftIssuedByAcId_get` | GET | L3342 | Gift issued per account |

### 5h. Feedback / CRM

| Endpoint | Method | Lines | Purpose |
|---|---|---|---|
| `sendFeedback_post` | POST | L517 | Submit customer feedback/complaint/enquiry |
| `sendFeedback_mail_post` | POST | L446 | Same but with email send |
| `sendVendorEnquiry_post` | POST | L588 | Vendor enquiry email |
| `custComplaints_get` | GET | L3356 | Get customer complaints |
| `custComplaintStatus_get` | GET | L3362 | Complaint status |

### 5i. Video Shop / DTH / Appointments

| Endpoint | Method | Lines | Purpose |
|---|---|---|---|
| `fetchAvailableSlots_get` | GET | L4212 | Get available showroom appointment slots |
| `fetchApptBookings_post` | POST | L4232 | Customer's appointments |
| `fetchApptBookDetail_post` | POST | L4239 | Appointment detail |
| `bookVSAppt_post` | POST | L4246 | Book video shop appointment |
| `bookVideoSAppt_post` | POST | L6239 | Book video showroom appointment |
| `updVSFeedback_post` | POST | L4351 | Update VS feedback |
| `generateVsOTP_get` | GET | L4380 | Generate OTP for VS |
| `custDTHRequests_get` | GET | L3369 | DTH requests |
| `custDTHStatus_get` | GET | L3375 | DTH status |
| `videoshopComplaints_get` | GET | L7627 | VS complaints |

### 5j. Internal / Helper Methods (not REST endpoints)

| Method | Lines | Purpose |
|---|---|---|
| `payment_gateway($id_branch, $id_pg)` | L71 | Get gateway credentials for branch |
| `__encrypt($str)` / `__decrypt($str)` | L82-90 | Base64 encode/decode passwords |
| `get_values()` | L95 | Read JSON body from `php://input` |
| `otp_sms($otpStr)` | L100 | Format OTP SMS message |
| `checkNotPaidAcc($id_customer)` | L112 | Check for unpaid scheme accounts |
| `array_sort($array, $on, $order)` | L118 | Sort array by key |
| `insert_referral_data(...)` | L6183 | Insert referral incentive records |
| `insert_common_data($id_payment)` | L7671 | Post-payment: SMS, receipt, incentives |
| `old_insert_common_data($id_payment)` | L7639 | Deprecated version of above |
| `insertEmployeeIncentive(...)` | L6371 | Insert employee referral incentive |
| `customerIncentive(...)` | L6405 | Insert customer referral incentive |
| `generateCFtoken / generateOrderData / generateRazorOrderData / generateCashfreeSession` | L5736+ | Payment gateway order generation |
| `_payment / _pay / _curlCall / _getHashKey` | L4928+ | PayU private helper methods |
| `sync_existing_data($mobile, $id_customer, $id_branch)` | L3962 | Sync existing chit data from third-party system |
| `wallet_account_create / join_existing_byacc` | internal | Wallet + existing scheme join helpers |
| `no_to_words / no_to_words1` | L7564+ | Number to words (invoice) |
| `kyc_exists($cus_id)` | L7910 | Check KYC exists |

---

## 6. Key Tables

| Table | Read By | Written By | Critical Columns |
|---|---|---|---|
| `customer` | authenticate, getCustomer, createCustomer, getCustomerByMobile, getDashboard | createCustomer, updateProfile, resetPassword, updateCustomerByMobile, delete_customer | `id_customer`, `mobile`, `passwd` (base64!), `active`, `kyc_status`, `id_branch`, `notification`, `pin_no`, `last_generated_otp`, `last_otp_expiry` |
| `scheme_account` | customerSchemes, getSchemeDetail, payDuesData, payment | createAccount, deleteScheme, mobile_payment (creates pending, updates after payment) | `id_scheme_account`, `id_customer`, `id_scheme`, `id_branch`, `active`, `is_closed`, `paid_installments`, `scheme_acc_number`, `disable_payment` |
| `payment` | paymentHistory, getSchemeDetail, getDashboard | mobile_payment_post, insert_common_data, gateway callbacks (update status) | `id_payment`, `id_scheme_account`, `payment_status` (1=success, 2=await, 3=fail, 4=cancel, 7=pending), `payment_amount`, `metal_weight`, `added_by` (2=customer mobile), `due_type` |
| `scheme` | getScheme, getActiveSchemes, createAccount | — | `id_scheme`, `scheme_type`, `total_installments`, `amount`, `max_weight`, `flexible_sch_type`, `is_digi`, `is_lumpSum` |
| `chit_settings` | currency_get, company_get, getScheme, getActiveSchemes | — | `currency_symbol`, `is_pin_required`, `branchwise_scheme`, `useWalletForChit`, `is_kyc_required`, `kyc_approval`, `allow_referral` |
| `address` | getCustomer, updateProfile | updateProfile (insert or update) | `id_customer`, `address1`, `id_country`, `id_state`, `id_city`, `pincode` |
| `wallet_account` | get_customerWallets | createCustomer (if wallet_account_type=1) | `id_wallet_account`, `id_customer`, `balance` |
| `wallet_transaction` | get_customerWallets | mobile_payment (wallet redemption) | `id_wallet_transaction`, `transaction_type`, `value` |
| `inter_wallet` | — | mobile_payment (inter-wallet payments) | `id_branch`, `type`, `amount` |
| `metal_rates` | getScheme, get_currency, getWeights | — (read from rate.txt file primarily) | `goldrate_22ct`, `goldrate_22ct`, `silverrate_1gm`, `updatetime` |
| `kyc` | getCustomer, getScheme | insertkyc_post, uploadAadhar_post | `id_customer`, `kyc_type` (2=PAN), `number`, `id_kyc` |
| `cust_enquiry` (or `sch_enquiry`) | — | sendFeedback_post | feedback records |
| `device_data` | authenticate | createCustomer, authenticate | `id_customer`, `token`, `uuid`, `device_type` |
| `scheme_reg_request` | — | createAccount (existing scheme join requests) | `id_reg_request`, `status`, `scheme_acc_number` |
| `postdate_payment` | getSchemeDetail | mobile_payment (PDC) | PDC payment records |

---

## 7. Payment Gateway Architecture

### Gateways Supported
| Gateway | Code | Active Endpoints | Notes |
|---|---|---|---|
| PayU | param_2=key, PAYU_KEY const | `_payment()` helper | Hardcoded key in constants |
| Cashfree | Branch DB `payment_gateway` | `generateCashfreeSession`, `cashfreeResponse_post` | Current primary |
| Easebuzz | Branch DB | `oldeasebuzzResponse_post` + `easebuzzResponse_post` | Active |
| Razorpay | Branch DB | `generateRazorOrderData` + `razorResponse_post` | Active |

### Gateway Selection Flow
1. Mobile sends `gateway` + `pg_code` in `mobile_payment_post`
2. `payment_gateway($id_branch, $pg_code)` → `mobileapi_model->getBranchGatewayData()` → DB lookup
3. Returns key/salt/access_code/merchant_id for the selected gateway
4. Order generation delegates to specific method (Cashfree/Razor/Easebuzz)

### Payment Status Codes
| Code | Meaning | Direction |
|---|---|---|
| 7 | Pending (pre-payment, app initiated) | Initial state |
| 2 | Awaiting (submitted to gateway, not confirmed) | After gateway call |
| 1 | Success | Gateway callback success |
| 3 | Failure | Gateway callback failure |
| 4 | Cancelled | Customer cancelled |

---

## 8. Business Rules Summary

> See `BUSINESS_RULES.md` for full rules. Summary:

- **RULE-APP-001**: Password stored as `base64_encode($password)` — NOT hashed. Critical security weakness.
- **RULE-APP-002**: OTP expiry checked in PHP string comparison (`$otp_time <= $last_otp_expiry`) — no DB-side expiry.
- **RULE-APP-003**: Multi-chit payment validation: `redeemed_amount + amount + sum(discount) == sum(pay_arr.pay_amt)` — intentional mismatch = reject.
- **RULE-APP-004**: New vs Existing scheme join: `is_new='Y'` = new account, `is_new='N'` = existing (with acc number).
- **RULE-APP-005**: Branch assignment for scheme join depends on 3-tier config: `branch_settings → is_branchwise_cus_reg → branchWiseLogin`.
- **RULE-APP-006**: Scheme joining blocked if: `allowNewscheme_join=0` / `allow_multiple=0 and unclosed accounts` / `allow_unpaid=0 and unpaid accounts`.
- **RULE-APP-007**: `insert_common_data()` — post-payment write: sends SMS, generates receipt_no, processes referral, updates incentives. Called from ALL gateway callbacks. **No transaction wrapper around this.**

---

## 9. Known Risks (Pre-Identified)

| # | Risk | Severity | Location |
|---|---|---|---|
| 1 | **CORS open** — `Access-Control-Allow-Origin: *` | 🔴 P0 | L3-5 controller |
| 2 | **No centralized auth** — any caller can access any endpoint with any `id_customer` | 🔴 P0 | All endpoints |
| 3 | **Passwords stored as base64** (NOT hashed) | 🔴 P0 | `__encrypt()` L82 + `customer.passwd` column |
| 4 | **SQL injection** — raw `$id_customer`, `$mobile`, `$id_scheme` concatenated in 100+ model SQL strings | 🔴 P1 | Throughout `mobileapi_model` |
| 5 | **payment_modal.php: NO trans_start** — 8+ INSERT operations in payment flow, no DB transaction | 🔴 P1 | `payment_modal.php` |
| 6 | **Hardcoded PayU keys** — `PAYU_KEY`, `PAYU_SALT` in controller constants | 🔴 P1 | L24-25 |
| 7 | **mkdir 0777** × 16 — world-writable directories created for logs/uploads | 🔴 P1 | Throughout controller |
| 8 | **No company_details() / sms_info() caching** — 2 DB queries on EVERY API request | 🟡 PERF | Constructor L57-58 |
| 9 | **`insert_common_data` not transactioned** — partial post-payment faliures (SMS sent, receipt not generated) | 🔴 P1 | L7671 |
| 10 | **`deleteScheme_get` uses GET** — Delete action via GET URL (CSRF risk) | 🟡 MED | L1072 |
| 11 | **48 raw `db->query()` calls** in controller (not model) — inline SQL in controller body | 🟡 MED | Various |
| 12 | **Log files write with FILE_APPEND** — growing unbounded log files, no rotation | 🟡 LOW | L4508-4510 |
| 13 | **`old_*` methods still active** — `old_cashfreeResponse`, `old_insert_common_data`, `oldeasebuzzResponse`, `oldrazorResponse` are reachable endpoints | 🟡 MED | L5167, L7187, L6437, L7639 |
| 14 | **`sendFeedback_mail_post` leaks `$ticketno` in success message even for non-complaints** | 🟡 LOW | L512 |

---

## 10. DB Verification Queries

```sql
-- Q1: Verify a customer's payment is correctly recorded
SELECT p.id_payment, p.payment_status, p.payment_amount, p.metal_weight, p.date_payment,
       p.added_by, p.receipt_no, p.id_scheme_account, sa.id_customer, c.mobile
FROM payment p
JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
JOIN customer c ON c.id_customer = sa.id_customer
WHERE c.mobile = :mobile AND p.payment_status IN (1,2)
ORDER BY p.id_payment DESC LIMIT 20;

-- Q2: Check for orphan payment rows (payment without valid scheme_account)
SELECT p.id_payment, p.id_scheme_account
FROM payment p
LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
WHERE sa.id_scheme_account IS NULL;

-- Q3: Verify OTP hasn't expired (within last 10 min)
SELECT id_customer, last_generated_otp, last_otp_expiry,
       TIMESTAMPDIFF(MINUTE, NOW(), last_otp_expiry) as mins_left
FROM customer WHERE mobile = :mobile;

-- Q4: Confirm multi-chit payment amount integrity
SELECT SUM(payment_amount) as total, COUNT(*) as chit_count
FROM payment
WHERE id_payment IN (:payment_ids) AND payment_status = 1;

-- Q5: Check scheme_account for disable_payment flag
SELECT id_scheme_account, disable_payment, active, is_closed
FROM scheme_account WHERE id_customer = :id_customer;

-- Q6: Verify wallet deduction on payment
SELECT wt.id_wallet_transaction, wt.transaction_type, wt.value, wt.description
FROM wallet_transaction wt
JOIN wallet_account wa ON wa.id_wallet_account = wt.id_wallet_account
WHERE wa.id_customer = :id_customer
ORDER BY wt.id_wallet_transaction DESC LIMIT 10;
```

---

## 11. Codebase Notes

- **REST framework**: `REST_Controller.php` — not CodeIgniter native. Methods must be `{name}_get` / `{name}_post`.
- **JSON body reading**: All POST data via `$this->get_values()` → `json_decode(file_get_contents('php://input'))` — NOT `$this->input->post()`.
- **SMS gateway config**: `sms_gateway` config key selects gateway (1=MSG91, 2=Nettyfish, 3=SpearUC, 4=Asterixt, 5=Qikberry).
- **integrationType=2**: External system sync is enabled for this config value — triggers `sync_existing_data()`.
- **Payment logging**: All payment requests logged to `log/{date}/cashfree/` even for non-Cashfree gateways.
- **Password**: `base64_encode()` referred to as `__encrypt()` — misleading name.
- **`old_*` prefix**: Methods prefixed `old_` are deprecated but still routable. Some are still called from `razorResponse_post`, etc.

---

## 12. Anti-Patterns Register

> Populated after each bug fix.

| # | Anti-Pattern | First Seen | Fix Applied |
|---|---|---|---|
| 1 | Base64 as password encryption | Round 1 | — |
| 2 | No API auth middleware | Round 1 | — |
| 3 | No DB transaction in payment flow | Round 1 | — |
| 4 | CORS wildcard | Round 1 | — |
