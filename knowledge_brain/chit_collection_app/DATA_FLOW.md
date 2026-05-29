# chit_collection_app — Data Flow

> **Brain Built:** 2026-03-17 | **Round:** 1

---

## Flow 1: Employee Authentication (Mobile Login)

```
Mobile App → POST /adminapp_api/authenticate
  ↓
adminapp_api.php::authenticate_post() [L139-183]
  ↓
adminappapi_model.php::isValidLogin($data) [L22-138]
  │
  ├─ SELECT employee WHERE username=? (raw SQL concat ← SQL injection risk)
  ├─ password_verify($data['passwd'], pwd_hash)  ← Bcrypt check ✓
  │
  ├─ If uuid == '1234567890' OR is_lmx == 1:
  │    device_uuid_status = TRUE; enable_chit = TRUE
  │
  └─ Else: SELECT employee_devices WHERE emp_id=? AND device_uuid=? AND app_type=1
      ├─ Found + device_status=1 → enable_chit=TRUE
      ├─ Found + device_status=0 → enable_chit=FALSE
      └─ Not found → INSERT employee_devices (device_status=0) → enable_chit=FALSE

authenticate_post() back in controller:
  ├─ Checks: is_valid, active==1, device_uuid_status==TRUE, enable_chit==TRUE
  ├─ Also checks: login_type=='AGENT' (separate path)
  └─ Returns: { is_valid, employee, currency, username }
```

**Tables Written:** `employee_devices` (INSERT when new device)  
**Tables Read:** `employee`, `agent`, `branch`, `employee_devices`

---

## Flow 2: Customer Lookup + Payment Due List (Core Collection Flow)

```
Mobile App → POST /adminapp_api/customerSchemes
  ↓
adminapp_api.php::customerSchemes_post() [L774-884]
  │
  ├─ If searchbyaccno==1 or 3 AND scheme code+year+accno provided:
  │    adminappapi_model::get_customer_byAcc(code, year, accno)
  │    READ: scheme_account JOIN customer JOIN scheme
  │
  └─ Else: payment_modal::get_customer(mobile)
           READ: customer

→ adminappapi_model::get_customerByMobile(mobile, emp_branch, branch_settings)
   READ: customer
   CHECK: cus.allocated_employee == login_employee OR cus.allocated_agent == login_employee
   └─ If NOT allocated → { isValid: FALSE, msg: 'not allocated' }
   └─ If allocated + active:

→ mobileapi_model::get_payment_details(id_customer, id_branch, id_sch_acc)
   ← MASSIVE SQL QUERY [model L440-561] reading:
      scheme_account, scheme, branch, scheme_group, payment (multiple subqueries),
      customer, chit_settings, postdate_payment
   
   Then PHP post-processing [L562-1293]:
   ├─ Average payable calculation (avg_calc_ins logic)
   ├─ allow_pay determination (8+ conditions)
   ├─ allowed_due + due_type calculation
   ├─ min_amount / max_amount calculation
   └─ Possibly: UPDATE scheme_account SET avg_payable (if newly calculated)

→ Returns: { cusSchemes: [...account objects...], customer, currency, branches? }
```

**Tables Written:** `scheme_account` (avg_payable update, if triggered)  
**Tables Read:** See mega-query above

---

## Flow 3: Cash Collection Payment (Core — Offline/Cash)

```
Mobile App → POST /adminapp_api/mobile_payment
  ↓
adminapp_api.php::mobile_payment_post() [L1381-~1800]
  │
  ├─ payment_modal::get_customer(phone) → READ customer
  ├─ payment_gateway($id_branch, $gateway) → READ payment_gateway
  ├─ payment_modal::get_financialYear() → READ chit_settings
  │
  ├─ db->trans_begin()
  │
  ├─ For each payment in pay_arr (JSON decoded):
  │    ├─ payment_modal::get_schemeByChit(udf1) → READ scheme_account+scheme
  │    ├─ GST Calculation (⚠️ $sch_data bug at L1489)
  │    ├─ payment_modal::get_entrydate(id_branch) → READ ret_day_closing
  │    │
  │    ├─ Build $insertData:
  │    │    payment_status = 1 (SUCCESS) if gateway==0 AND added_through==3 (EMP)
  │    │    payment_status = 2 (AWAITING) if gateway==0 AND added_through==2 (CUS)
  │    │    payment_status = 7 (PENDING) if gateway!=0
  │    │    id_employee / id_agent from login_type
  │    │    payment_mode from payData['pay_mode'] (CSH/QR/etc.)
  │    │
  │    └─ payment_modal::addPayment($insertData) → WRITE payment
  │
  ├─ db->trans_commit()
  │
  ├─ If gateway == 0 (Offline/Cash):
  │    ├─ generateAcNoOrReceiptNo($pay) [for each pay]:
  │    │    ├─ READ payment, scheme_account, scheme, chit_settings
  │    │    ├─ account_number_generator → UPDATE scheme_account
  │    │    └─ generate_receipt_no → UPDATE payment
  │    │
  │    ├─ insert_referral_data (if applicable) → WRITE referral tables
  │    ├─ SMS notification (sms_model)
  │    └─ Return { status:TRUE, msg, payIds }
  │
  ├─ If gateway != 0 (Online payment):
  │    └─ Build gateway params → respond with { txnid, ... } for mobile to redirect
  │
  └─ Return result to mobile app
```

**Tables Written:** `payment` (INSERT), `scheme_account` (UPDATE acc_no, receipt_no), `sms_api_settings` (debit_sms decrement)  
**Tables Read:** `customer`, `payment_gateway`, `scheme_account`, `scheme`, `chit_settings`

---

## Flow 4: Customer Web Payment (paymt.php)

```
Browser → Customer Payment Page (/paymt)
  ↓
paymt.php::index() [L98-122]
  ├─ session check: username required
  ├─ payment_modal::get_payment_details(username) → scheme accounts
  ├─ payment_modal::getBranchGateways(id_branch) → gateway options
  └─ LOAD view: chitscheme/payment

→ Customer selects chits and submits form (POST /paymt/paySubmit)

paymt.php::paySubmit() [L306-914]
  ├─ payment_modal::wallet_balance() → wallet state
  ├─ For each payment:
  │    ├─ payment_modal::get_paymentContent(udf1) → scheme data
  │    ├─ amount validation (payable >= scheme amount)
  │    ├─ GST calculation
  │    └─ due month/year generation (payment_modal::generateDueDate)
  │
  ├─ db->trans_begin()
  │
  ├─ payment_modal::addPayment($insertData) → WRITE payment (status=7 PENDING)
  │    (or status=1 SUCCESS if full wallet redemption)
  │
  ├─ db->trans_commit()
  │
  ├─ If full wallet payment → insertWalletTrans → redirect to /paymt
  │
  └─ Else → Redirect to payment gateway (PayU/Cashfree/TechProcess/HDFC/Atom/Ippo)

→ Gateway callback → paymt.php::{gateway}ResponseURL()
  ├─ Parse gateway response
  ├─ payment_modal::updateGatewayResponse($updateData, $txnid)
  ├─ If SUCCESS:
  │    ├─ account_number_generator → UPDATE scheme_account
  │    ├─ generate_receipt_no → UPDATE payment
  │    ├─ updateGroupCode (lucky draw) → UPDATE scheme_account
  │    ├─ insert_referral_data → WRITE incentive tables
  │    ├─ insertWalletTrans (if wallet used)
  │    ├─ SMS/email notification
  │    └─ insert_common_data / insert_common_data_jil (if integrationType 2/3)
  └─ Redirect to /paymt or /paymt/payment_history
```

**Tables Written:** `payment` (INSERT + UPDATE), `scheme_account` (UPDATE), `wallet_transaction`, incentive tables  
**Tables Read:** `scheme_account`, `scheme`, `payment`, `chit_settings`, `payment_gateway`, `inter_wallet_account`

---

## Flow 5: Customer Registration (Collection App)

```
Mobile App → POST /adminapp_api/createCustomer
  ↓
adminapp_api.php::createCustomer_post() [L186-402]
  │
  ├─ Base64 decode profile image → file_put_contents('assets/img/customer_profile/...')
  ├─ mobileapi_model::isBranchWiseReg() → check branch required
  ├─ isNumberRegistered($data) → check mobile+email uniqueness
  │
  ├─ db->trans_begin()
  │
  ├─ adminappapi_model::insert_customer($customer)
  │    ├─ INSERT customer
  │    ├─ INSERT address
  │    └─ UPDATE customer SET id_address
  │
  ├─ If trans_status OK:
  │    ├─ Save Aadhaar image → admin/assets/img/customer/{id}/aadhar_{id}.png
  │    ├─ UPDATE customer SET aadhar_ImgName
  │    ├─ Save PAN image → UPDATE customer SET pan_ImgName
  │    ├─ Save DL image → UPDATE customer SET dl_ImgName
  │    │
  │    ├─ If integrationType==5: integration_model::khimji_curl('registerCustomerWithoutValidateOtp')
  │    │
  │    ├─ wallet_accno_generator() → if wallet_account_type==1:
  │    │    wallet_account_create($id, $mobile)
  │    │
  │    ├─ db->trans_commit()
  │    ├─ SMS (services_modal::checkService(1) → get_SMS_data → send_sms)
  │    ├─ Email notification
  │    └─ Return { status:TRUE, id_customer, msg }
  │
  └─ Else: db->trans_rollback() → { status:FALSE }
```

**Tables Written:** `customer`, `address`, `employee_devices` (via wallet creation)  
**Tables Read:** `chit_settings`, `sms_api_settings`  
**File System Writes:** `assets/img/customer_profile/`, `admin/assets/img/customer/{id}/`

---

## Flow 6: Scheme Joining (Collection App)

```
Mobile App → POST /adminapp_api/createAccount
  ↓
adminapp_api.php::createAccount_post() [L406-710]
  │
  ├─ Determine id_branch
  ├─ payment_modal::get_financialYear() + get_entrydate()
  │
  ├─ If is_new == 'N' (Existing account):
  │    ├─ If regExistingReqOtp == 0:
  │    │    scheme_modal::verify_existing($schAcc)
  │    │    → If not exists: scheme_modal::join_existing($scheme_acc)
  │    │         WRITE: scheme_request table
  │    │    → Else: return exists error
  │    └─ Else: join_existing_byacc($schAcc) (OTP verified path)
  │
  └─ If is_new == 'Y' (New account):
       ├─ If referral_code given: scheme_modal::checkreferral_code()
       ├─ If agent_code given: mobileapi_model::verifyAgentCode()
       │
       ├─ db->trans_begin()
       │
       ├─ mobileapi_model::insert_schemeAcc($schAcc) → WRITE scheme_account
       │
       ├─ Save ID front/back images → WRITE files
       ├─ If id_gift: INSERT gift table
       │
       ├─ If free_payment==1:
       │    ├─ mobileapi_model::free_payment_data() → build free pay data
       │    ├─ If receipt_no_set==1: generate_receipt_no()
       │    └─ payment_modal::addPayment($pay_insert_data) → WRITE payment (FP)
       │
       ├─ If voucher_no + voucher_value: INSERT gift_card (voucher)
       │
       ├─ db->trans_commit()
       │
       ├─ mobileapi_model::get_schemeaccount_detail()
       ├─ scheme_modal::getJoinedScheme()
       ├─ SMS + Email
       └─ Return { status:TRUE, chit: schData1, free_pay: flash_msg }
```

**Tables Written:** `scheme_account`, `payment` (free payment), `gifts`, `gift_card`  
**Tables Read:** `scheme`, `payment_gateway`, `chit_settings`, `agent`, `employee`

---

## JS → Controller Map (No dedicated JS file — REST API)

The collection app is a native Android app consuming the REST API. No web JS files relevant for this module.

For `paymt.php` (customer web), JS is in `application/views/chitscheme/payment.php` (inline) and `assets/js/chitscheme/payment.js` (if present). Key AJAX patterns exist in the view but are not separate files for this module.
