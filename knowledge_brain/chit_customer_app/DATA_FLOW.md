# DATA FLOW — chit_customer_app
> Round 1 — 2026-03-16

---

## Flow 1: Customer Registration

**Trigger**: New user opens app → taps Register.

**Steps**:
1. App calls `isNumberRegistered_get?mobile=9876543210` → check duplicate
2. App calls `generateOTP_get?mobile=9876543210&email=xyz@abc.com` → OTP sent via SMS + Email
3. App calls `check_regOTP_post` with `{sysotp, userotp, last_otp_expiry, mobile}` → validate OTP in PHP
4. App calls `createCustomer_post` with full registration data

**`createCustomer_post` internal flow** (L921 controller):
```
JSON POST: {firstname, lastname, mobile, passwd, email, id_branch, address1, token, uuid, device_type}
↓
mobileapi_model->insert_customer($customer)
  → INSERT INTO customer (...)            ← Step 1: Customer row
  → INSERT INTO address (...)             ← Step 2: Address row
↓
registration_model->wallet_accno_generator()
  → if wallet_account_type=1:
      wallet_account_create($id, $mobile)  ← Step 3: Wallet account (optional)
↓
mobileapi_model->insert_deviceData($deviceData)
  → INSERT INTO device_data (...)          ← Step 4: Push notification token
↓
mobileapi_model->update_customer({notification:1}, $id)
  → UPDATE customer SET notification=1     ← Step 5: Enable notifications
↓
services_modal->get_SMS_data(1, $id)
sms_model->sendSMS_*($mobile, $message)   ← Step 6: Welcome SMS
email_model->send_email($to, ...)         ← Step 7: Welcome Email (if email)
↓
Response: {status:true, id_customer:X, mobile:Y, msg:"registered"}
```

**Tables written**: `customer`, `address`, `device_data`, `wallet_account` (optional)

**Key risk**: If `insert_customer` succeeds but `wallet_account_create` fails — customer exists without wallet. No rollback.

---

## Flow 2: Customer Login

**Trigger**: Customer opens app → enters mobile + password.

**`authenticate_post` flow** (L732):
```
JSON POST: {mobile, passwd, token, uuid, device_type}
↓
mobileapi_model->isValidLogin($mobile, base64_encode($passwd))
  → SELECT id_customer, active FROM customer WHERE mobile=? AND passwd=?
↓
if found AND active=1:
  mobileapi_model->get_customerByMobile($mobile)
    → full customer data + KYC status + pin_req status
  ↓
  mobileapi_model->update_deviceData($device_data, $id_customer)
    → UPDATE device_data SET token=?, uuid=?, device_type=? WHERE id_customer=?
  ↓
  mobileapi_model->get_currency($id_branch)
    → Settings: currency_symbol, kyc_required, pin_required, metal_rates, ...
  ↓
  if integrationType==2: sync_existing_data($mobile, $id_customer, $id_branch)
  ↓
  Response: {mobile, is_valid:true, customer:{...}, currency:{...}}

elif active=0:
  Response: {is_valid:false, message:'account inactive'}

elif not found:
  Response: {is_valid:false, message:'Invalid Username or Password'}
```

**Tables read**: `customer`, `chit_settings`
**Tables written**: `device_data`

**Risk**: Password compare is `base64_encode(user_input) == stored_passwd` — no timing-safe comparison.

---

## Flow 3: Scheme Join (New Account)

**Trigger**: Customer taps "Join Scheme" → fills form → taps Submit.

**Pre-join calls**:
1. `getScheme_get?id_scheme=X&id_customer=Y&id_branch=Z` → eligibility check
2. `get_groups_get?id_scheme=X` → group code selection (if lucky draw scheme)

**`createAccount_post` internal flow** (L1086):
```
JSON POST: {id_customer, id_scheme, id_branch, account_name, group_code, referal_code, pan_no, is_new:'Y', payable, ...}
↓
mobileapi_model->get_customerByID($id_customer)
mobileapi_model->is_branchwise_cus_reg()
→ Determine id_branch from 3-tier logic
↓
payment_modal->get_financialYear()   → start_year
↓
[Referral validation if referal_code provided]:
  scheme_modal->checkreferral_code($referal_code, $id_customer)
↓
$this->db->trans_begin()  ← Transaction start (NEW account path only)
↓
mobileapi_model->insert_schemeAccount($schAcc)
  → INSERT INTO scheme_account (...)
↓
mobileapi_model->account_number_generator(...)
  → Generate scheme_acc_number
  → UPDATE scheme_account SET scheme_acc_number=? WHERE id_scheme_account=?
↓
if referal_code provided:
  insert_referral_data($id_scheme_account, $referral_data)
↓
$this->db->trans_commit() / trans_rollback() on error
↓
SMS/notification (if service enabled)
↓
Response: {status:true, msg:'...', id_scheme_account:X}
```

**Tables written**: `scheme_account`, `scheme_reg_request` (existing join), `payment_referral` (referral)

---

## Flow 4: Payment Initiation (Mobile Payment)

**Trigger**: Customer taps "Pay Now" → selects payment amount(s) → taps "Proceed to Payment".

**`mobile_payment_post` flow** (L4484):
```
JSON POST: {
  phone, amount, redeemed_amount, id_branch, gateway, pg_code,
  pay_arr: [{id_scheme_account, pay_amt, discount, due_type}, ...],
  paidBy_id_customer, paidBy_mobile
}
↓
LOG: file_put_contents(log_path, data, FILE_APPEND) ← Request logged
↓
Validation:
  redeemed_amount + amount + sum(discount) == sum(pay_arr.pay_amt) → else REJECT
↓
payment_modal->get_customer($phone) → customer + branch + settings
payment_modal->payment_gateway($id_branch, $gateway) → gateway credentials (paycred)
payment_modal->get_financialYear() → start_year
↓
For EACH item in pay_arr:
  payment_modal->paymentDB($params) →
    INSERT INTO payment (id_scheme_account, payment_status=7, payment_amount, ...)
    → returns id_payment
  UPDATE scheme_account SET paid_installments += no_of_dues...
↓
if redeemed_amount > 0:
  payment_modal->wallet_transactionDB($data) →
    INSERT INTO wallet_transaction (transaction_type=1, value=redeemed_amount, ...)   ← Wallet debit
↓
gateway = Cashfree/Razorpay/Easebuzz selected by $gateway param:
  generateCashfreeSession / generateRazorOrderData / generateOrderData
  → curl to gateway API → get order_id / payment_session_id
↓
Response: {status:true, order_id:..., payment_session_id:..., gateway_response:...}
App redirects customer to gateway SDK/URL
```

**Tables read**: `customer`, `scheme_account`, `chit_settings`, `payment_gateway`
**Tables written**: `payment` (status=7 pending), `wallet_transaction` (wallet debit)

> ⚠️ **RISK**: Each `paymentDB()` call is a separate INSERT. Multiple chits = multiple inserts, no wrapping transaction across all of them. If insert 3 of 5 fails, 2 are already committed as status=7.

---

## Flow 5: Payment Gateway Callback (Success)

**Trigger**: Customer completes payment on gateway → gateway calls webhook OR customer returns → app calls status check.

**`old_cashfreeResponse_post` / `easebuzzResponse_post` pattern** (L7187, L5167, L6437):
```
POST from Gateway: {txnid/order_id/reference_no, status, amount, ...}
↓
LOG: gateway response to file
↓
Hash/signature verification:
  Recalculate expected hash from received params + salt
  Compare with received hash → if mismatch: LOG error, return failure
↓
Get payment IDs:
  payment_modal->getPayIds($txnid) → id_payment array
↓
For each payment:
  if gateway_status == 'SUCCESS' / 'TXN_SUCCESS':
    payment_modal->update_trans({payment_status:1}, $id)  ← Status → Success
  elif gateway_status == 'FAILED' / 'TXN_FAILURE':
    payment_modal->update_trans({payment_status:3}, $id)  ← Status → Failed
  elif gateway_status == 'PENDING':
    payment_modal->update_trans({payment_status:2}, $id)  ← Status → Awaiting
↓
if SUCCESS:
  insert_common_data($id_payment) for each payment:
    → get_receipt_no() → UPDATE payment.receipt_no
    → sendSMS (payment confirmation)
    → insert_referral_data() (referral incentive)
    → insertEmployeeIncentive() (employee incentive)
    → customerIncentive() (customer referral cashback)
    → updateGroupCode() (update scheme_account.group_code)
↓
Response: {status:true/false}
```

**Tables read**: `payment`, `customer`, `scheme`, `scheme_account`, `chit_settings`
**Tables written**: `payment` (status update, receipt_no), `payment_referral`, `wallet_transaction`, `employee_incentive`, `scheme_account` (group_code)

> ⚠️ **RISK**: `insert_common_data` has NO transaction. If receipt generation succeeds but SMS fails and referral insert throws exception — partial state. Payment is marked success, but referral/incentive not recorded.

---

## Flow 6: Customer Profile Update

**Trigger**: Customer opens Profile → edits → saves.

**`updateProfile_post` flow** (L601):
```
JSON POST: {id_customer, firstname, lastname, email, pan, date_of_birth, date_of_wed,
            nominee_name, nominee_relationship, nominee_mobile, address1, ..., id_city}
↓
mobileapi_model->update_customer($customer, $id_customer)
  → UPDATE customer SET firstname=?, lastname=?, email=?, ... WHERE id_customer=?
↓
mobileapi_model->isAddressExist($id_customer)
  → SELECT id_address FROM address WHERE id_customer=?

if exists:
  mobileapi_model->update_customerAdd($address, $id_customer)
    → UPDATE address SET address1=?, id_city=?, ... WHERE id_customer=?
else:
  mobileapi_model->insert_customerAdd($address)
    → INSERT INTO address (id_customer, ...) VALUES (...)
↓
Response: {status:true, msg:'Profile updated successfully'}
```

**Tables written**: `customer`, `address`

---

## Flow 7: Wallet Redemption within Payment

**Trigger**: Customer checks "Use Wallet Balance" on payment screen.

**In `mobile_payment_post`** (L4515):
```
amt = redeemed_amount + cash_amount + sum(discounts)
if amt != sum(pay_arr):
    reject

[After payment record inserts]
if redeemed_amount > 0:
  payment_modal->wallet_transactionDB({
    id_wallet_account: X,
    transaction_type: 1,   (debit)
    value: redeemed_amount,
    description: "Payment redemption"
  }) → INSERT INTO wallet_transaction
```

**Risk**: Wallet deducted BEFORE gateway payment succeeds. If gateway fails after wallet deduction, wallet balance is reduced but payment never happens. No auto-reversal.

---

## Flow 8: OTP-Based Password Reset

**Trigger**: Customer taps "Forgot Password".

**Steps**:
1. `isNumberRegistered_get?mobile=X` → confirm account exists
2. `generateOTP_get?mobile=X` → OTP generated + stored in `customer.last_generated_otp` + `customer.last_otp_expiry`
3. `verifyOTP_post` `{id_customer, otp}` → compare OTP + expiry
4. `resetPassword_post` `{id_customer, passwd}` → `UPDATE customer SET passwd=base64_encode($pwd)`

**Risk**: `resetPassword_post` does NOT re-verify OTP at password change stage. If someone has `id_customer`, they can change password at step 4 without going through OTP (if app flow is bypassed via direct API call).

---

## Flow 9: KYC Upload (Aadhaar / PAN)

**Trigger**: Customer opens KYC section → uploads image.

**`uploadAadhar_post` flow** (L3439):
```
JSON POST: {id_customer, image: base64_string, type: 'aadhar'/'pan'}
↓
get_image_typ_from_base64($image_url) → detect mime type
↓
$directory = 'assets/aadhar_file/' . $id_customer . '/'
if(!is_dir($directory)) mkdir($directory, 0777, true)  ← ⚠️ 0777
↓
file_put_contents($directory . filename, base64_decode($image))
↓
UPDATE customer SET aadhar_ImgName=? WHERE id_customer=?
↓
Response: {status:true, msg:'Aadhaar uploaded'}
```

**Tables written**: `customer` (aadhar_ImgName), disk file in `assets/aadhar_file/`

> ⚠️ **Risk**: No file type validation beyond MIME detection from base64 prefix. Attacker can upload PHP files by crafting base64 with valid image header.
> ⚠️ **Risk**: `mkdir(0777)` — world-writable directory.
