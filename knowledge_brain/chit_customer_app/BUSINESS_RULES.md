# BUSINESS RULES — chit_customer_app
> Round 1 — 2026-03-16

---

## RULE-APP-001: Password Storage (CRITICAL)

**Formula**: `stored_password = base64_encode(plain_password)`

**Used by**: `__encrypt()` L82 in controller. Called in `createCustomer_post`, `resetPassword_post`, `updateCustomerByMobile_post`, `updateCustomer_post`.

**Verification in `authenticate_post`** L737:
```php
$res = $this->$model->isValidLogin($data['mobile'], $this->__encrypt($data['passwd']));
```

**Risk**: Base64 is a reversible encoding — NOT a hash. If DB is compromised, ALL customer passwords are instantly readable.

**Edge case**: `updateCustomerByMobile_post` (L412-428) adds a check: new password ≠ old password (prevents re-use). But the comparison does `==` on base64 values — so `"password"` vs `"Password"` are treated as different. Comparison is case-sensitive.

---

## RULE-APP-002: OTP Expiry Validation

**Flow**: Generate → Store in `customer.last_generated_otp` + `customer.last_otp_expiry` → Verify.

**Validation** (controller `verifyOTP_post`, L874):
```php
if ($data['otp'] == $last_otp['last_generated_otp']) {
    if ($otp_time <= date('Y-m-d H:i:s', strtotime($last_otp['last_otp_expiry']))) {
        // valid
    }
}
```

**Edge case**: OTP check is string comparison (`==`) not strict (`===`). Integer `123456` from JSON would still match string `"123456"` due to PHP loose comparison. BUT if OTP starts with `0` (e.g., `012345`), the JSON integer would be `12345` which would NOT match the stored string `"012345"`.

**No brute-force protection**: No attempt count. An attacker can try all 6-digit OTPs (900,000 possibilities) with no lockout.

---

## RULE-APP-003: Multi-Chit Payment Validation

**Pre-condition check** (controller `mobile_payment_post`, L4516):
```
amt = redeemed_amount + amount + sum(pay_arr[].discount)
if amt == sum(pay_arr[].pay_amt):
    proceed to payment
else:
    reject
```

**Purpose**: Ensures the total posted payment matches the sum of individual chit amounts + wallet redemption.

**Edge case**: `amt` uses float arithmetic. For large amounts (e.g., `10000.50`), float precision can cause `==` to fail. No epsilon comparison used.

---

## RULE-APP-004: New vs Existing Scheme Join

**Rule** (`createAccount_post`, L1086):
```
if is_new == 'Y':  → new account (auto-generate scheme_acc_number)
if is_new == 'N':  → existing account (customer provides existing scheme_acc_number)
```

**Existing account join path**:
1. Verify `scheme_acc_number` doesn't already exist in `scheme_account` OR `scheme_reg_request`
2. If OTP required (`regExistingReqOtp=1`): OTP verification before join
3. Insert into `scheme_reg_request` (status=0 = processing)

**New account join path** (L1184):
- Wraps in `$this->db->trans_begin()`
- Referral code validated → scheme_account inserted → account number generated → referral data inserted

---

## RULE-APP-005: Branch Assignment for Scheme Account

**3-tier decision tree** (`createAccount_post`, L1098-1108):
```
if branch_settings == 1:
    if is_branchwise_cus_reg == 1:
        id_branch = customer.id_branch  (customer's registered branch)
    elif branchWiseLogin == 1:
        id_branch = posted id_branch param
    else:
        id_branch = posted id_branch param (if provided)
else:
    id_branch = NULL
```

**Risk**: If `branch_settings=0`, all new accounts are created with `id_branch=NULL`. This will exclude them from all branch-filtered admin reports.

---

## RULE-APP-006: Scheme Join Eligibility Gates

**Check 1 — New scheme joining allowed** (`scheme_modal->allowNewscheme_join()`):
- Reads `chit_settings.allow_new_scheme_join` — if 0, blocks all new joins

**Check 2 — Multiple chits allowed** (`scheme_modal->allowMultipleChits()`):
- If `allow_multiple=FALSE`: Customer cannot have >1 active unclosed account
- Gate: `hasUnclosedAccounts($id_customer)` — if TRUE, blocks join

**Check 3 — Unpaid restriction** (`scheme_modal->allowUnpaid()`):
- If `allow_unpaid=FALSE`: Customer with any account where `paid_installments=0` cannot join new scheme
- Gate: `check_unpaid_schemes($id_customer)`

**Final message on active account** (L310):
- Even if join is allowed, if customer already has active accounts in same scheme: warns "You have X live accounts, continue?"

---

## RULE-APP-007: Post-Payment Data Processing (`insert_common_data`)

**Trigger**: Called from ALL gateway callback handlers after payment confirmed.

**Steps** (L7671-7818):
1. Get payment details from DB
2. Generate `receipt_no` via `payment_modal->get_receipt_no()`
3. Update `payment.receipt_no` in DB
4. Send SMS notification (if service enabled)
5. Send WhatsApp message (if enabled)
6. Process referral data → `insert_referral_data()`
7. Process employee incentive → `insertEmployeeIncentive()`
8. Process customer incentive → `customerIncentive()`
9. Update group code → `updateGroupCode()`

> ⚠️ **NO TRANSACTION WRAPPER** around this method. Steps 2-9 are independent DB calls. If step 5 fails, steps 2-4 already committed. If step 7 fails, referral is not recorded but payment is already success.

---

## RULE-APP-008: Due Type Calculation (`get_schemeaccount_detail` model)

**Due types** (complex logic, L716-778 of model):
```
ND  = Normal Due (current month unpaid, no arrear)
PD  = Previous/Pending Due (arrear payment)
PN  = Normal + Pending (has arrear AND current month due)
AD  = Advance Due (paying next month early)
AN  = Advance + Normal Due
```

**Priority logic**:
1. Is current month paid? → if YES → check advance/pending
2. Is current month unpaid? → if YES → check if arrear allowed
3. Allow advance only if `scheme.allow_advance=1` AND `advance_months` quota left

**Used in**: `getSchemeDetail_get()` → `mobileapi_model->get_schemeaccount_detail()` → passed to App to show payment button status.

---

## RULE-APP-009: Wallet Redemption in Payment

**Rule** (`mobile_payment_post`, L4515):
```
total_payable = redeemed_amount + cash_amt
redeemed_amount is deducted from wallet_balance first
cash_amt goes to payment gateway
```

**Implemented by**: `payment_modal->wallet_transactionDB()` — writes a debit row to `wallet_transaction`.

**Validation**: `useWalletForChit` setting must be `1` in `chit_settings` for wallet use to be allowed on scheme payments.

**Edge case**: If gateway payment succeeds but wallet deduction fails (or vice versa), no rollback. Partial redemption possible.

---

## RULE-APP-010: Rate-Fixed Payment (Weight Scheme)

**Rule**: For weight-type schemes, the customer can "fix" the rate at a specific gold rate. This locks the amount for the installment.

**Flow**:
1. Customer selects rate fix → `verifyRateFixOTP_post()` validates OTP (L4070)
2. Rate lock stored in session/payload with the rate value
3. `mobile_payment_post` uses the submitted `amount` (which reflects the locked rate)

**Risk**: Rate is sent from the mobile app. Server does NOT independently validate that the `amount` matches the locked rate at time of OTP issuance. A malicious user could modify `amount` in the POST body.

---

## RULE-APP-011: KYC Gate for Payment

**Rule** (`getScheme_get`, model L506-507):
```
if is_kyc_required=1 AND kyc_status != 'APPROVED':
    app shows KYC required message (enforced client-side)
```

**Server-side enforcement**: ⚠️ NOT enforced in `mobile_payment_post` — controller does NOT check `kyc_status` before allowing payment. KYC gate is CLIENT-SIDE ONLY.

---

## RULE-APP-012: Flexible Scheme Amount Calculation

**Formula** (model `get_schemeaccount_detail`, L789):
```
if scheme_type=3 (FLEXIBLE) AND flexible_sch_type IN (1,2):
    if (firstPayamt_payable=1 OR firstPayamt_as_payamt=1) AND (paid_installments>0 OR get_amt_in_schjoin=1) OR is_registered=1:
        payable = firstPayment_amt (first payment amount locked)
    else:
        payable = max_amount - current_total_amount
elif scheme_type=3 AND max_weight>0:
    payable = (max_weight - current_total_weight) × metal_rate
else:
    payable = scheme.amount
```

This is the most complex payable calculation — governs how much the customer is asked to pay each installment for flexible schemes.
