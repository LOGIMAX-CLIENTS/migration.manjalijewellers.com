# chit_collection_app — Deep Model Analysis (Round 3)

> **Brain Updated:** 2026-03-17 | **Round:** 3

---

## 1. `get_payment_details()` — The Core Due-Calculation Query

**File:** `adminappapi_model.php` | **Lines:** 440–900+  
**Called by:** `customerSchemes_post()` → mobile customer due list screen

### What It Does
This is the **most critical method in the entire module** — it drives the "what can this customer pay today?" screen. It is a single massive SQL query (~120 lines) with post-processing in PHP.

### SQL Structure

```
SELECT ... FROM scheme_account sa
LEFT JOIN scheme s ON sa.id_scheme = s.id_scheme
LEFT JOIN branch br ON br.id_branch = sa.id_branch
LEFT JOIN scheme_group sg ON sa.group_code = sg.group_code
LEFT JOIN payment p ON sa.id_scheme_account = p.id_scheme_account
           AND (p.payment_status IN (1, 2, 8))  -- success, awaiting, split
LEFT JOIN customer c ON sa.id_customer = c.id_customer AND c.active=1
LEFT JOIN (
    -- Current month paid payments subquery (cp)
    SELECT sa.id_scheme_account,
           COUNT(...) as paid_installment,
           SUM(payment_amount) as total_amount,
           SUM(metal_weight) as total_weight
    FROM payment WHERE (payment_status=1 OR 2) AND Date_Format = curdate month
) cp ON sa.id_scheme_account = cp.id_scheme_account
LEFT JOIN (
    -- Current day paid payments subquery (sp) — for payment_chances schemes
    SELECT ... WHERE Date_Format(date_add, curdate DAY)
) sp ON sa.id_scheme_account = sp.id_scheme_account
LEFT JOIN postdate_payment pp ON sa.id_scheme_account = pp.id_scheme_account
          AND pp.payment_status IN (2,7) AND pp.date_payment month = curdate month
JOIN chit_settings cs
WHERE sa.active=1 AND sa.is_closed=0 AND c.id_customer = {$id_customer}
[AND sa.id_scheme_account = {$id_sch_acc}]   -- if specific account requested
GROUP BY sa.id_scheme_account
```

### Key Computed Columns

| Column | Formula | Business Meaning |
|---|---|---|
| `paid_installments` | `SUM(no_of_dues)` or `COUNT(DISTINCT month)` | Varies by scheme_type. Weight flexible uses DISTINCT month. |
| `missed_ins` | `PERIOD_DIFF(curdate, start_date) - paid_installments` | Capped at total_installments |
| `totalunpaid` | `(months_elapsed + 1) - paid_installments` | How many unpaid dues |
| `daily_sch_allow_pay_till` | `date_add(start_date, total_installments-1 months)` | For daily scheme eligibility cutoff |
| `current_paid_installments` | From `cp` subquery — this month's count | Month payment lock logic |
| `previous_paid` | 1 if last paid is this month | Prevents double payment |
| `curday_total_paid_count` | Payments with `date(date_payment) = curdate` | Daily limit check |
| `avg_payable` | Updated in scheme_account after avg_calc_ins | Average installment for flexible schemes |

### Post-SQL PHP Processing: Average Payable Logic

```php
// After query, for each record:
IF scheme_type in (1-flexible-wgt or 3) AND avg_calc_ins > 0:
    IF avg_calc_by == 0:  // By installment count
        IF current_installments > avg_calc_ins:
            IF avg_payable already set:
                Apply stored avg_payable to record->max_amount / max_weight
            ELSE:
                // Calculate fresh average
                SELECT sum(metal_weight), sum(payment_amount) FROM payment
                WHERE payment_status=1 AND id_scheme_account=X
                GROUP BY YEAR, MONTH LIMIT avg_calc_ins
                // Compute average and UPDATE scheme_account.avg_payable
    ELSE IF avg_calc_by == 1:  // By calendar date (joining date + months)
        IF months_since_join >= avg_calc_ins:
            Similar calculation but for date range
```

### Critical Bugs Found in `get_payment_details()`

| Bug | Location | Description |
|---|---|---|
| **COL-BUG-025** | L571 | `$current_installments = ($record->current_paid_installments == 0 ? $record->paid_installments+1 : $record->paid_installments+1)` — BOTH branches of the ternary return the same value. The `current_paid_installments == 0` condition is never used. Dead ternary. |
| **COL-BUG-026** | L615-617 | Average calculation raw SQL: `"WHERE payment_status=1 and id_scheme_account=".$record->id_scheme_account`. Direct concatenation — SQL injection risk in the model itself. |
| **COL-BUG-027** | L499-503 | `total_paid_amount` uses `SUM(p.payment_amount * p.no_of_dues)` — if `no_of_dues` is null for some rows, those are excluded. Opening balance payments (`is_opening=1`) use `balance_amount + SUM`. Potential miscalculation when balance_amount is NULL. |

### Metal Rate Source ⚠️
```php
$filename = base_url().'api/rate.txt';
$data = file_get_contents($filename);
$result['metal_rates'] = (array) json_decode($data);
```
**COL-BUG-028:** Metal rates are fetched from a **text file** via `file_get_contents(base_url()+'api/rate.txt')`. If this file is missing, stale, or the HTTP request fails, `$result['metal_rates']` will be empty/null — but no error handling exists. Customers will see wrong/no rates on the payment screen.

---

## 2. `getWalletPaymentContent()` — Pre-Callback Wallet Check

**File:** `adminappapi_model.php` | **Lines:** 1452–1463

```sql
SELECT
    p.id_payment,
    iwa.available_points,
    sa.id_branch as branch,
    sa.id_scheme_account,
    cs.schemeacc_no_set,
    sa.id_scheme,
    cs.receipt_no_set,
    cs.scheme_wise_receipt,
    sa.scheme_acc_number,
    IFNULL(iwa.mobile, 0) as isAvail,  -- 0 if no wallet account
    c.email,
    c.mobile,
    redeemed_amount,
    actual_trans_amt,
    cs.allow_referral,
    cs.walletIntegration,
    c.id_customer,
    cs.wallet_points,
    cs.wallet_amt_per_points,
    cs.wallet_balance_type
FROM payment p
JOIN chit_settings cs
LEFT JOIN scheme_account sa ON p.id_scheme_account = sa.id_scheme_account
LEFT JOIN customer c ON c.id_customer = sa.id_customer
LEFT JOIN inter_wallet_account iwa ON iwa.mobile = c.mobile
WHERE p.id_payment = '{$id_payment}'
```

**Usage Pattern:** Called FIRST in every payment callback handler.
- `isAvail` → if `iwa.mobile` is null, returns 0 (no wallet account)
- `redeemed_amount` > 0 → trigger `insertWalletTrans()` to debit customer wallet

**Note:** `JOIN chit_settings cs` has no ON clause — **implicit cross join**. Works only because `chit_settings` has exactly 1 row. If ever multiple rows exist, result set multiplies. Low risk in practice.

---

## 3. `getPayGenData()` — Payment + Scheme Data for adminAppSuccess

**File:** `adminappapi_model.php` | **Lines:** 1465–1477

Returns a single row with payment details plus all scheme/account/branch settings needed for post-payment processing:

**Key fields returned:**
- `firstPayment_amt`, `firstPayamt_as_payamt`, `firstPayamt_maxpayable` → First payment fixing
- `schemeacc_no_set`, `receipt_no_set` → Generation flags
- `agent_refferal`, `emp_refferal`, `agent_credit_type` → Incentive eligibility
- `rate_fix_by`, `rate_select`, `fixed_wgt` → Metal rate fix logic
- `due_type`, `receipt_no`, `payment_status` → Current payment state
- `offline_tran_uniqueid` → Khimji pre-registered transaction ID
- `warehouse` → Khimji branch code for account number generation

---

## 4. `insertAgentIncentive()` — Agent Commission Credit

**Files:** `adminapp_api.php` (L3256), `paymt.php` (L4536) — **duplicate implementations**

```
Input: ($data, $id_sch_acc, $id_payment, $id_agent)

1. payment_modal::checkReferalExist($id_payment, $id_sch_acc)
   → Checks if incentive already credited for this payment
   IF already exists → return 0 (prevent double-credit)

2. INSERT loyalty_transaction:
   ly_trans_type = 3          (Agent incentive type)
   cus_loyal_cus_id = customer ID
   id_agent = $id_agent
   id_scheme_account
   id_payment
   cash_point = referal_amount
   status = 1 (active)
   tr_cus_type = 4
   cr_based_on = 3
   unsettled_cash_pts = referal_amount

3. payment_modal::updateAgentCash($id_agent, referal_amount)
   → UPDATE agent: total_cash = total_cash + referal_amount

4. UPDATE payment: id_agent = $id_agent
```

**⚠️ Difference between versions:**
- `adminapp_api.php` version: `credit_for = $data['credit_remark']`
- `paymt.php` version: `credit_for = $data['credit_remark'].'-Collection App'`
The paymt.php version adds a suffix distinguishing the source.

---

## 5. `insertEmployeeIncentive()` — Employee Wallet Credit for Referral

**Files:** `adminapp_api.php` (L3288), `paymt.php` (L4564) — **duplicate implementations**

```
Input: ($refdata, $id_scheme_account, $id_payment)

1. payment_modal::get_referral_code($id_scheme_account) → chkreferral
2. payment_modal::checkCreditTransExist($id_scheme_account, $id_payment)
   → Deduplication guard

3. IF referral_code set AND is_refferal_by==1 (emp referral):
   payment_modal::get_empreferrals_datas($id_scheme_account) → wallet + employee data

4. IF referal_amount > 0 AND id_wallet_account > 0:
   payment_modal::wallet_transactionDB($wallet_data)
   → INSERT wallet_transaction: transaction_type=0 (credit), value=referal_amount
```

**Key distinction:** `insertEmployeeIncentive` credits the EMPLOYEE's **wallet** (via wallet_transaction), while `insertAgentIncentive` credits the AGENT's **loyalty_transaction** table (cash points). Different tables, different fields.

---

## 6. `customerIncentive()` — Cross-Referral Customer-Introduces-Employee Staff Credit

**Files:** `adminapp_api.php` (L3350), `paymt.php` (L4599) — **duplicate implementations**

```
Purpose: When a customer is referred BY a customer (is_refferal_by==0),
         and the customer was originally introduced by an employee —
         credit that employee's wallet too.

Flow:
1. get_referral_code($id_scheme_account) → chkreferral
2. IF is_refferal_by==0 (customer referral):
    UPDATE customer: is_refbenefit_crt_cus = 1 (multiple credits enabled)
    payment_modal::get_empRefExist_datas($id_scheme_account) → find introducing employee account
    IF employee exists:
        get_empreferrals_datas(isEmpRef[id_scheme_account])
        wallet_transactionDB('insert', '', $wallet_data)
        credit_for = 'Customer Intro Scheme Incentive'
        id_employee = session 'uid'   ← ⚠️ Uses session, not $refdata
```

**⚠️ COL-BUG-029:** `customerIncentive()` uses `$this->session->userdata('uid')` to identify the employee (L3381, L4620). In the **collection app context**, there is NO session — the app is REST-based. This field will always be NULL for collection app payments.

---

## 7. `insert_common_data()` & `insert_common_data_jil()` — ERP Integration

**File:** `paymt.php` (L3647, L3611), duplicate exists in `adminapp_api.php`

### `insert_common_data($id_payment)` — Standard Integration (integrationType=2)

```
1. chitapi_model::getPaymentByID($id_payment) → payment details with ref_no
2. syncapi_model::checkCusRegExists($id_scheme_account, $ref_no)
   → IF customer registration not in intermediate table:
       syncapi_model::getCustomerByID($id_scheme_account) → customer data
       INSERT into customer_reg (staging): record_to=1, is_registered_online=2
3. syncapi_model::checkTransExists($ref_no)
   → IF payment transaction not in staging:
       INSERT payment into transaction staging: record_to=1, payment_type=1 (online)
```

### `insert_common_data_jil($id_payment)` — JIL Integration (integrationType=1)

```
1. chitapi_model::getPaymentByID($id_payment) → includes trans_date, approval_no, ref_no
2. chitapi_model::checkTransExists($trans_date, $approval_no, $ref_no)
   → Dedup check using 3 fields (more specific than standard)
3. IF not exists:
    INSERT transaction
    IF customer reg exists: INSERT customer registration too
```

**Purpose:** Both functions push payment data into an intermediate/staging table for external ERP sync. The ERP polls this table to confirm payments.

---

## 8. `generateTranUniqueId()` — Khimji Pre-Payment Registration

**Files:** `adminapp_api.php` (L4104), `paymt.php` (L4408) — **duplicate implementations**

```
Purpose: Pre-register a payment with Khimji ERP BEFORE inserting into DB.
         Khimji returns a tranUniqueId stored in payment.offline_tran_uniqueid.

API: POST khimji_curl('app/v1/saveSchemeOrInstallmentDetails', {
    isKycValidationCheck: false,
    customerCode: chit['reference_no'],
    transactionType: 1,
    schemeCode: chit['sync_scheme_code'],
    amount: amount,
    date: today,
    narration: "Requested from mobile app"
    IF is_new_ac (1st payment):
        action: 1  → New account creation
        salesmanName, employeeId, nominee {...}
    ELSE:
        action: 2  → Subsequent installment
        orderNo: scheme_acc_number
})

Returns: { status: true, tranUniqueId: "KHIMJI_TXN_ID" }
         OR { status: false, message: errorMsg }

Failure handling: Returns false → caller (mobile_payment_get/post) returns
                  { status: false, message: ... } to mobile app → payment ABORTED
```

**⚠️ COL-BUG-030:** In `adminapp_api.php::generateTranUniqueId()` (L4157), on `errorCode==1001`, it tries to UPDATE payment with `$pay['id_payment']` — but `$pay` is NOT defined in scope. This causes a PHP Notice and the error log never gets written.

---

## 9. `generateAcNoOrReceiptNo()` — Khimji Post-Payment Account/Receipt

**Files:** `adminapp_api.php` (L4017), `paymt.php` (L4471) — **duplicate implementations**

```
Purpose: After payment success, call Khimji API to get the account number
         and/or receipt number assigned by ERP.

API: POST khimji_curl('app/v1/saveSchemeOrInstallmentDetails', {
    isKycValidationCheck: false,
    transactionType: 2,
    tranUniqueId: pay['offline_tran_uniqueid'],
    branchCode: pay['warehouse'],
    paymentDetail: {
        paymentType: 9,
        paymentTypeName: "Online",
        amount, authorizationNo, narration, originalAmt, marchantCharges: 0
    }
    IF scheme_acc_number already exists:
        orderNo: scheme_acc_number
})

On success (errorCode==0):
    IF result->orderNo exists:
        UPDATE scheme_account: scheme_acc_number = orderNo
        UPDATE payment: receipt_no = orderNo
    IF result->installmentNo exists (paymt.php version only, commented in adminapp_api):
        UPDATE payment: receipt_no = installmentNo
```

**Key Difference between versions:**
- `adminapp_api.php` version: `authorizationNo = id_transaction`
- `paymt.php` version: `authorizationNo = "ADM_REF_" + id_payment`

---

## 10. Remaining paymt.php Gateway Callbacks

### `responseURL()` — CCAvenue Web Payment (L3749)

```
POST CCAvenue encrypted response: $_POST['encResp']
├─ Decrypt: decrypt($encResponse, $workingKey)
├─ Parse pipe-separated key=value response
├─ Amount + txnid VERIFICATION vs session values (amount, txn_id)
│  
├─ IF verification passes:
│    order_status==Success → payment_success($updateData, 2)
│    order_status==Aborted → payment_cancel($updateData, 2)
│    order_status==Failure → payment_failure($updateData, 2)
├─ ELSE: Security error flash → redirect /paymt
```

### `mobileResponseURL()` — CCAvenue Mobile Payment (L3822)
Same as responseURL but: NO amount/txnid verification check. Accepts any response from CCAvenue gateway ID=13 (hardcoded).

### `gPayResponseMURL($type, $payData, $gateway)` — GPay Status Handler (L4122)
Simple status mapper — takes `$type` ('s'=success, 'f'=failure, 'c'=cancel) and calls `updateGatewayResponse`. No post-payment processing — only status update + redirect.

### `gPayMobileResponseURL()` — CCAvenue Mobile Decrypted → GPay Handler (L4154)
Decrypts CCAvenue response → maps to `gPayResponseMURL`. **Uses `$this->payment_gateway[1]['key']`** which means it requires `payment_gateway` to be a class property array keyed by `1`. If `payment_gateway` is not loaded, **fatal error**.

### `gPaytechProMblResponseURL()` — TechProcess GPay Mobile (L4205)
TechProcess specific — decrypts pipe-delimited response, maps `txn_status` codes:
- `0300` = success
- `0392` = cancel
- others = failure
→ Calls `gPayResponseMURL` with appropriate type flag.

### `ipporesponseURL()` — Ippo Gateway (L4635)

```
⚠️ DANGEROUS: Uses credentials from POST body to make API call!
$gatewayData = json_decode(file_get_contents(
    "https://" . $_POST['publicKey'] . ":" . $_POST['secretKey'] . "@api.ippopay.com/v1/order/" . $_POST['response']['order_id']
));
```
**COL-BUG-031:** Ippo callback uses `$_POST['publicKey']` and `$_POST['secretKey']` to construct an authenticated API URL. An attacker can POST arbitrary credentials and order_id to verify any order — or forge a success response by controlling what Ippo returns for their chosen order_id. **No signature verification.**

Status codes: `success` → successMURL(data, 6), `failure` → payment_failure(data, 6), `CANCELLED` → payment_cancel(data, 6).
Note: cancel status uses "CANCELLED" (uppercase) but Ippo API typically returns lowercase — potential mismatch.

### `razorresponseURL()` — RazorPay (L4677)

```php
function razorresponseURL() {
    print_r($_POST); exit;
}
```
**COL-BUG-032:** `razorresponseURL()` is a **debug stub** — it just dumps POST data and exits. Any RazorPay payment callback will dump raw POST data (including payment details) in plaintext to the browser and abort. RazorPay payments are completely broken.

### `cashfreeresponseURL()` — Cashfree Web (L4294)
Similar to mobileResponseURL but for web. Verifies amount + orderId vs session. Signature verification is **commented out** (same as mobile version). Calls `payment_success/failure/cancel($updateData, 4)`.

### `cashfreemobile()` — Cashfree Mobile Return (L4351)
**COL-BUG-033:** Line 4354: `$secretKey = $paymentgateway['param_1']` — `$paymentgateway` is **undefined** in this function scope. Fatal PHP error on every Cashfree mobile payment return. (This was previously tracked as PAY-CLT-V01 in the payment module.)
