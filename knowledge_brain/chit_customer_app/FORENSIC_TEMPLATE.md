# FORENSIC TEMPLATE — chit_customer_app
> Round 1 — 2026-03-16 | 8-layer investigation cheat sheet

---

## Layer 1 — Symptom Collection

When a mobile app bug is reported, collect:

- [ ] **Which app action failed?** (Login / Register / View Schemes / Join Scheme / Pay / View History / KYC / Profile / Wallet)
- [ ] **Which gateway?** (Cashfree / Easebuzz / Razorpay / PayU — for payment issues)
- [ ] **Error in app UI?** (Error message text — exact wording)
- [ ] **iOS or Android?** (iOS may have different SSL pinning / certificate issues)
- [ ] **Is it for ONE customer or ALL?** (One = data issue; All = code/config issue)
- [ ] **Single chit payment or multi-chit?** (For payment failures)
- [ ] **Is wallet used in payment?** (Wallet + gateway interaction bugs)
- [ ] **API response captured?** (Ask support to share raw API response if available)
- [ ] **Is latest app version installed?** (Old app may call deprecated endpoints like `mobile_payment_get`)
- [ ] **Which branch is customer in?** (`id_branch` affects scheme visibility, gateway selection, rate)
- [ ] **Time of failure?** (Gateway downtime windows, rate.txt staleness)

---

## Layer 2 — Reproduce & Isolate

**Step 1 — Check daily log files**:
```
log/{today_date}/cashfree/mob_postData_{date}.txt    ← Payment POST body log
log/{today_date}/cashfree/{gateway}_response_{date}.txt  ← Gateway response log
```
Look for the customer's mobile number in the log. The request log shows what the app sent; the response log shows what the gateway returned.

**Step 2 — Test the endpoint directly**:
Use Postman/curl to hit the failing endpoint:
```bash
# Test login
POST /index.php/mobile_api/authenticate
Content-Type: application/json
{"mobile":"9876543210","passwd":"test123","token":"","uuid":"","device_type":"A"}

# Test scheme list
GET /index.php/mobile_api/getActiveSchemes?id_branch=1

# Test payment status (after gateway callback)
SELECT * FROM payment WHERE id_scheme_account IN (X, Y) ORDER BY id_payment DESC LIMIT 10;
```

**Step 3 — Isolate user vs global**:
- Login with the specific customer's mobile → same error? = user data issue
- Create test customer → same error? = code/config issue

**Step 4 — Check if "old" endpoint is being called**:
The app may be calling `mobile_payment_get` (old) instead of `mobile_payment_post` (new). Check request method in logs.

---

## Layer 3 — Client-Side Trace

**Key API endpoint debug flow**:
```
App Request → mobile_api.php
 ↓ get_values() → json_decode(php://input)
 ↓ Business logic
 ↓ $this->response($data, 200)
 ↓ REST_Controller returns JSON
```

**Common app error patterns**:

| App Error | Likely API Response | Server Issue |
|---|---|---|
| "Unable to proceed your request" | `{status:false}` | DB insert failed |
| "Invalid username or password" | `{is_valid:false}` | Wrong mobile/pass OR account inactive |
| "Payment failed" | Gateway response JSON | Gateway rejection or timeout |
| Empty scheme list | `[]` | Branch filter selecting wrong branch, or scheme.active=0 |
| Wallet balance wrong | Balance mismatch | Wallet debit without payment success (see Flow 7) |
| "You have unclosed chits" | `{allow_join:{status:false}}` | `scheme_modal->hasUnclosedAccounts()` returns true |
| OTP "invalid" | `{is_valid:false}` | Wrong OTP or expired (10 min?) |
| KYC upload fails | `{status:false}` | Directory permission issue or base64 decode fail |

**Network timeout issues**: REST API has no timeout guards. Long-running queries (especially `get_schemeaccount_detail` with complex SQL) can cause gateway timeout (502/504) on slow DB days.

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Login returns `{is_valid:false}` | `mobileapi_model.php` | `isValidLogin` | ~L810 | Does `passwd` column match `base64_encode($input)`? Check for spaces/encoding issues |
| OTP not received | `mobile_api.php` | `generateOTP_get` | L783 | What `sms_gateway` config value? Check gateway API key. Check `services.id_services=23` |
| Register fails silently | `mobileapi_model.php` | `insert_customer` | ~L2200 | Enable `db->db_debug=true`, check for duplicate mobile or DB constraint violation |
| Scheme list empty | `mobileapi_model.php` | `get_activeSchemes` | L482 | Is `where s.active=1 and visible=1` satisfied? Check `scheme_branch` for branch filter |
| Join scheme fails | `mobile_api.php` | `createAccount_post` | L1086 | Is `trans_begin` committed or rolled back? Check `scheme_modal->checkreferral_code` result |
| Payment pending stays pending | `mobile_api.php` | gateway callback | L7187+ | Check log file for gateway response. Is webhook URL registered correctly? |
| Payment success but no receipt | `mobile_api.php` | `insert_common_data` | L7671 | Check `payment_modal->get_receipt_no()` — is it returning NULL? |
| Wallet balance wrong after payment | `payment_modal.php` | `wallet_transactionDB` | L2350 | Was transaction_type=1 (debit) inserted? Did gateway fail after wallet debit? |
| Profile update not saving | `mobileapi_model.php` | `update_customer` | ~L2060 | Is `id_customer` matching? Try `$this->db->last_query()` to see actual SQL |
| KYC upload fails | `mobile_api.php` | `uploadAadhar_post` | L3439 | Is `assets/aadhar_file/` directory writable? Is base64 valid image? |
| Rate.txt errors | `mobile_api.php` | `getMetalrate_get` | L152 | Does `api/rate.txt` exist? Is JSON valid? |

---

## Layer 5 — Database Verification

```sql
-- Q1: Check customer record for login/OTP issues
SELECT id_customer, mobile, passwd, active, kyc_status, last_generated_otp,
       last_otp_expiry, pin_no, is_new, notification, id_branch
FROM customer WHERE mobile = '9876543210';

-- Q2: Check all payments for a customer (payment history issues)
SELECT p.id_payment, p.id_scheme_account, p.payment_status, p.payment_amount,
       p.receipt_no, p.date_payment, p.added_by, p.due_type, p.no_of_dues,
       p.metal_weight, p.id_transaction
FROM payment p
JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
JOIN customer c ON c.id_customer = sa.id_customer
WHERE c.mobile = '9876543210'
ORDER BY p.id_payment DESC LIMIT 20;

-- Q3: Verify pending payments (status=7) that should be confirmed
SELECT p.id_payment, p.payment_status, p.payment_amount, p.id_transaction,
       sa.id_scheme_account, c.mobile, p.date_add
FROM payment p
JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
JOIN customer c ON c.id_customer = sa.id_customer
WHERE p.payment_status = 7
AND p.date_add < DATE_SUB(NOW(), INTERVAL 30 MINUTE)
ORDER BY p.date_add DESC;

-- Q4: Check scheme account state for a customer
SELECT sa.id_scheme_account, sa.scheme_acc_number, s.scheme_name,
       sa.active, sa.is_closed, sa.paid_installments, sa.disable_payment,
       sa.id_branch, sa.date_add
FROM scheme_account sa
JOIN scheme s ON s.id_scheme = sa.id_scheme
JOIN customer c ON c.id_customer = sa.id_customer
WHERE c.mobile = '9876543210';

-- Q5: Check wallet balance (wallet display mismatch)
SELECT wa.id_wallet_account, wa.wallet_acc_number, wa.active,
       SUM(CASE WHEN wt.transaction_type=0 THEN wt.value ELSE 0 END) as credits,
       SUM(CASE WHEN wt.transaction_type=1 THEN wt.value ELSE 0 END) as debits,
       (SUM(CASE WHEN wt.transaction_type=0 THEN wt.value ELSE 0 END) - 
        SUM(CASE WHEN wt.transaction_type=1 THEN wt.value ELSE 0 END)) as balance
FROM wallet_account wa
LEFT JOIN wallet_transaction wt ON wt.id_wallet_account = wa.id_wallet_account
WHERE wa.id_customer = :id_customer
GROUP BY wa.id_wallet_account;

-- Q6: Verify gateway transaction by txnid
SELECT p.id_payment, p.id_transaction, p.payment_status, p.payment_amount,
       p.receipt_no, p.date_payment, sa.id_scheme_account
FROM payment p
JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
WHERE p.id_transaction = 'TXN_ID_HERE';

-- Q7: Check KYC status
SELECT k.id_kyc, k.kyc_type, k.number, k.status, k.date_add, c.kyc_status
FROM kyc k
JOIN customer c ON c.id_customer = k.id_customer
WHERE c.mobile = '9876543210';
```

---

## Layer 6 — Root Cause Classification

| Category | Risk | Examples in This Module |
|---|---|---|
| No centralized auth | 🔴 P0 | Any caller with `id_customer` can get/update any customer's data |
| Password not hashed | 🔴 P0 | `base64_encode` is reversible — passwords exposed on DB breach |
| SQL injection | 🔴 P1 | `$id_customer`, `$mobile`, `$char` raw in SQL (100+ spots) |
| No payment transaction | 🔴 P1 | Multi-chit payment inserts not wrapped — partial commit possible |
| No wallet reversal | 🔴 P1 | Wallet debit before gateway → no rollback on gateway failure |
| No post-payment transaction | 🔴 P1 | `insert_common_data` multi-step with no rollback |
| CORS wildcard | 🔴 P0 | Any website can make authenticated API calls from browser |
| File upload unvalidated | 🔴 P1 | Aadhaar upload accepts any base64 content |
| Active test endpoints | 🟡 MED | `test_get`, `testCURL_get` reachable in production |
| World-writable log dirs | 🟡 MED | `mkdir(0777)` × 16 |
| OTP brute-force | 🟡 MED | No retry limit on OTP verification |
| Rate file dependency | 🟡 MED | `api/rate.txt` missing = scheme page broken |
| Hardcoded gateway keys | 🔴 P1 | PayU key in PHP constants (not env-based) |

---

## Layer 7 — Payment Transaction Integrity Trace

For any payment-related bug, trace through these checkpoints:

**Checkpoint 1 — Request log**:
```
log/{date}/cashfree/mob_postData_{date}.txt
```
Verify: `pay_arr` amounts match what customer saw in app.

**Checkpoint 2 — Pending payment created**:
```sql
SELECT id_payment, payment_status, payment_amount, id_transaction
FROM payment WHERE payment_status=7 AND date_add > NOW() - INTERVAL 1 HOUR
ORDER BY id_payment DESC;
```
Confirms `paymentDB()` executed successfully.

**Checkpoint 3 — Gateway response log**:
```
log/{date}/cashfree/{gateway}_response_{date}.txt
```
Look for `payment_status = SUCCESS/FAILED/PENDING` from gateway.

**Checkpoint 4 — Payment status after callback**:
```sql
SELECT id_payment, payment_status, receipt_no, id_transaction FROM payment WHERE id_payment IN (X,Y);
```
Should be `1` if callback processed correctly. Check `receipt_no` is not NULL.

**Checkpoint 5 — insert_common_data partial state**:
```sql
-- Check if referral was recorded (should exist for schemes with referral)
SELECT * FROM payment_referral WHERE id_payment IN (X, Y);
-- Check receipt number
SELECT receipt_no FROM payment WHERE id_payment IN (X, Y);
```

**Checkpoint 6 — Wallet debit verification**:
```sql
SELECT * FROM wallet_transaction WHERE id_sch_ac = :id_scheme_account ORDER BY id_wallet_transaction DESC LIMIT 5;
```

---

## Layer 8 — Gateway-Specific Trace

| Gateway | Callback Endpoint | Signature Field | Key Field | Log File |
|---|---|---|---|---|
| Cashfree (current) | `cashfreeResponse_post` → `old_cashfreeResponse_post` | Hmac SHA256 | `X-Webhook-Signature` header | `cashfree/` |
| Easebuzz | `easebuzzResponse_post` → `oldeasebuzzResponse_post` | SHA512 hash | `hash` in response | `cashfree/` |
| Razorpay | `razorResponse_post` → `oldrazorResponse_post` | HMAC SHA256 | `razorpay_signature` | `cashfree/` |
| PayU (legacy) | `_payment()` helper | SHA512 | `hash` | N/A |

**Common gateway failure patterns**:
- **Hash mismatch**: Server recomputes hash different from gateway → Check salt key in `payment_gateway` DB table for the customer's branch
- **Duplicate webhook**: Gateway sends webhook twice → Second call tries to update already-confirmed payment — check for status=1 before updating
- **Timeout**: Gateway timeout (30s) → payment stuck at status=7 → Manual recovery SQL needed
- **Branch gateway mismatch**: Customer's branch uses Cashfree but `pg_code` sent incorrectly → wrong gateway selected
