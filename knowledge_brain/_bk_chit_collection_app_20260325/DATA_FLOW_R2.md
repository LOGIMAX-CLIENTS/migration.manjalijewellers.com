# chit_collection_app — Data Flow (Round 2 Additions)

> **Brain Updated:** 2026-03-17 | **Round:** 2

---

## Flow 7: adminAppSuccess — Post-Payment Handler (Cash/Offline)

```
adminapp_api.php::adminAppSuccess($paymtData) [L1768-2051]
Called by: mobile_payment_post() after gateway==0 commit

Input: $paymtData = {
    schAc_ids: " {id1} {id2} ..."  (space-delimited)
    pay_ids:   ",{id1},{id2},..."  (comma-delimited, index 0 empty)
    login_type: EMP | AGENT
    id_employee: int | null
    id_agent: int | null
    payAmount: float
}

├─ db->trans_begin()
├─ services_modal::checkService(3) → payment success service config
│
├─ For each pay_id:
│    ├─ adminappapi_model::getPayGenData($pay_id) → payment + scheme + branch data
│    │
│    ├─ Multi-mode payment:
│    │    IF payment_mode != NULL:
│    │       IF due_type == 'GA' → INSERT general_advance_mode_detail
│    │       ELSE → UPDATE existing payment_mode_details (status=9/Cancelled) + INSERT new
│    │
│    ├─ RHR (Chit cycle) scheme:
│    │    mobileapi_model::get_due_date($due_type, $dt_pay, $id_scheme_account)
│    │    → UPDATE payment: due_date, due_date_to, grace_date, installment, is_limit_exceed
│    │
│    ├─ UPDATE scheme_account: total_paid_ins (from getPaidInsData count)
│    │
│    ├─ One-time premium + rate fixing:
│    │    IF one_time_premium==1 AND rate_fix_by==0 AND rate_select==1 AND fixed_wgt==null:
│    │       mobileapi_model::isRateFixed($id_scheme_account)
│    │       IF not fixed: mobileapi_model::updFixedRate(fixed_wgt, firstPayment_amt, fixed_metal_rate)
│    │
│    ├─ First payment amount fix (if auto_pay_approval in 1,2,3):
│    │    IF flexible_sch_type==4 → SET firstPayment_wgt = metal_weight
│    │    ELSE → SET firstPayment_amt = payment_amount
│    │
│    ├─ Receipt number generation:
│    │    IF receipt_no_set==1 AND empty(receipt_no):
│    │       generate_receipt_no() → UPDATE payment
│    │
│    ├─ Account number generation:
│    │    IF schemeacc_no_set==0 OR 3 AND scheme_acc_number empty:
│    │       IF is_lucky_draw==1 → updateGroupCode() → $ac_group_code
│    │       account_number_generator($id_scheme, $branch, $ac_group_code)
│    │       UPDATE scheme_account: scheme_acc_number
│    │       IF gent_clientid==1 → generateClientID() SET ref_no
│    │       IF receipt_no_set==1 AND integrationType==2 → update_cusreg()
│    │
│    ├─ Agent incentive (if login_type=='AGENT' AND agent_refferal==1):
│    │    payment_modal::get_Incentivedata(scheme, sa, type=2, payment)
│    │    → insertAgentIncentive(ag, id_sa, id_pay, id_agent)
│    │
│    ├─ Employee incentive (if login_type=='EMP' AND emp_refferal==1):
│    │    payment_modal::get_Incentivedata(scheme, sa, type=1, payment)
│    │    → insertEmployeeIncentive(emp, id_sa, id_pay, id_employee)
│    │    → IF credit_for==1: customerIncentive(emp, id_sa, id_pay)
│    │
│    └─ Integration sync (if approval_type==2 AND integrationType==2):
│         insert_common_data($pay_id)
│
├─ IF db->trans_status==TRUE:
│    ├─ db->trans_commit()
│    ├─ For each pay_id: send SMS + WhatsApp + Email
│    ├─ adminapp('success', amount) → { status: TRUE, msg: ... }
│    └─ response(result, 200)
│
└─ ELSE: trans_rollback() → adminapp('failed') → response(result, 200)
```

**Tables Written:** `payment_mode_details`, `general_advance_mode_detail`, `payment` (due_date, receipt_no), `scheme_account` (acc_no, firstPayment_amt, total_paid_ins), `agent_incentive`, `employee_incentive`, `customer_incentive`  
**Bug⚠️:** `$trans_id` is undefined in this function (used at servicelevel but never set from input). This means wallet debit in `adminAppSuccess` uses stale `$trans_id`.

---

## Flow 8: Easebuzz Callback (Mobile Collection App)

```
POST /adminapp_api/easebuzzResponse [L2299-2493]
  │
  ├─ get_values() → parse JSON body
  ├─ $trans_id = $postData['response']->txnid
  ├─ $txStatus = $postData['response']->status  ("success"|"failure"|"userCancelled")
  │
  ├─ LOG: write to log/easebuzz/mob_response_{date}.txt (mkdir 0777)
  │
  ├─ payment_modal::updateGatewayResponse($updateData, $trans_id)
  │    payment_mode: CC|DC|NB based on mode string
  │    payment_status: success/awaiting (based on auto_pay_approval) OR cancel OR failure
  │
  ├─ IF success:
  │    getPayIds($trans_id) → for each pay:
  │      INSERT payment_mode_details
  │      Employee/Agent incentive (by postData['login_type'])
  │      Account number generation
  │      Receipt number generation
  │      Update scheme_account
  │      Insert integration data (if auto_pay_approval==2, integrationType==2)
  │      Send SMS (mobileapi_model::send_sms — different from services_modal SMS!)
  │
  └─ response(result, 200)
```

**Key Difference from adminAppSuccess:** Easebuzz uses Easebuzz SHA512 hash.  
**⚠️ Bug:** Referral processing for easebuzz is COMMENTED OUT at L2882-2890 — `insert_referral_data` never called for Easebuzz mobile payments.  
**⚠️ Bug:** `insert_common_data` at L2461 always checks `integrationType==1` before `2` — the outer condition says `auto_pay_approval==2 AND integrationType==2` but then the inner `if` checks `integrationType==1` first. This means JIL (type=1) is called when type==2 is configured. Copy-paste error.

---

## Flow 9: Cashfree Payment Status (Mobile Collection App)

```
POST /adminapp_api/cf_payment_status [L2786-3045]
  │
  ├─ get_values() → parse JSON: orderId, txStatus, referenceId, paymentMode, txMsg, txTime
  ├─ LOG: write to log/cashfree/mob_response_{date}.txt (no mkdir check — bug if dir missing!)
  ├─ payment_modal::updateGatewayResponse(updateData, trans_id)
  │    txStatus: SUCCESS→success/awaiting, CANCELLED→cancel, FAILED→failure
  │
  ├─ IF SUCCESS:
  │    getPayIds($trans_id) → for each pay:
  │      integrationType==5 → generateAcNoOrReceiptNo(pay)
  │      INSERT payment_mode_details  
  │      Agent incentive (if postData['login_type']=='AGENT')
  │      Employee incentive (if postData['login_type']=='EMP')
  │      Account number + receipt number generation
  │      First payment amount fixing
  │      Integration sync (integrationType==2)
  │      SMS via mobileapi_model::send_sms (not services_modal)
  │
  └─ response(result, 200)
```

**⚠️ Bug:** No `mkdir` before `file_put_contents` for Cashfree log (L2801-2803). Will FAIL on fresh installs or if log/cashfree dir is missing.  
**⚠️ Bug:** Signature verification is COMMENTED OUT at L2804-2813. No webhook authentication!

---

## Flow 10: Offline Sync — Agent Customer Sync

```
POST /adminapp_api/syncAgentCustomers [L3416-3446]
  │
  ├─ get_values(): login_type, id_employee, id_branch
  ├─ IF login_type == 'AGENT' AND id_employee > 0:
  │    adminappapi_model::get_activeSchemes(id_branch) → scheme list
  │    adminappapi_model::get_customerByAgent(id_employee) → customer list
  │    → response({ schemes, customer })
  └─ ELSE: { msg: 'Not a valid Agent' }
```

**Note:** READ-only. Does NOT sync data back — just fetches agent's customers for offline collection device.

---

## Flow 11: Offline Sync — Sync Customer Data Upload

```
POST /adminapp_api/syncOfflineCustomers [L3448-3499]
  │
  ├─ get_values() → JSON array of customer objects
  ├─ For each customer where id_customer != '':
  │    Build customer data array
  │    ⚠️ BUG: $data['login_type'] and $data['id_employee'] refer to outer $data (the array),
  │             but $data gets OVERWRITTEN with single customer fields at L3458!
  │             So id_agent line (L3482) uses $data['login_type'] from the NEW $data,
  │             which is a customer sub-field, likely NULL/wrong.
  │    insertOfflineCustomer($data) → INSERT customer + address
  └─ response({ customers, status, msg })
```

**⚠️ Critical Bug:** Variable `$data` shadow — outer `$data` (the post payload array) is overwritten by `$data = array(...)` inside the foreach at L3458. All subsequent references to `$data['login_type']` etc. read from the customer sub-object, not the original request.

---

## Flow 12: Offline Sync — Sync Offline Account Payments

```
POST /adminapp_api/syncOfflineAccPayments [L3501-3620]
  │
  ├─ get_values() → array of cus_schemes objects
  ├─ For each cus_schemes:
  │    ├─ IF is_new == 'Y' AND id_customer != "new":
  │    │    CURL to: https://retail.logimaxindia.com/etail_v1/index.php/adminapp_api/createAccount
  │    │    ⚠️ HARDCODED external URL — won't work if base URL changed!
  │    │    IF account created (id_scheme_account > 0):
  │    │       insertOfflinepayments($paydata) → internal payment creation
  │    │
  │    ├─ IF cus_schemes->udf1 > 0 (existing account ID):
  │    │       insertOfflinepayments($paydata)
  │    │
  └─ response({ status, msg })

insertOfflinepayments($data) [L3622-3901]:
  ├─ Similar to mobile_payment_post() but processes array (not JSON-decoded obj)
  ├─ added_through = 3 (hardcoded — always ADMIN APP)
  ├─ gateway = 0 (hardcoded — always offline/cash)
  ├─ ⚠️ SAME GST BUG: $sch_data['gst_type'] undefined at L3673 (same as mobile_payment_post)
  ├─ db->trans_begin() → addPayment() → trans_commit()
  └─ Returns result of adminAppSuccess()
```

---

## Flow 13: Khimji Integration — Pull Offline Data

```
adminapp_api.php::getDataFromOffline($id_customer, $cus_reference_no) [L4171-4300]
Only active when integrationType == 5

⚠️ Bug at L4173-4174: Parameters are SWAPPED immediately after entry:
    $cus_reference_no = $id_customer;    // writes arg1 into arg2 variable
    $id_customer = $cus_reference_no;    // writes the old arg1 value into arg1
    Net result: BOTH variables hold same value = original $id_customer!
    The actual $cus_reference_no argument is LOST.

├─ integration_model::khimji_curl('user/getSchemeDetails', {customerCode, uniqueCode})
├─ For each scheme returned:
│    isAccExist → IF not: INSERT scheme_account
│    For each installment:
│       isPayExist → IF not: INSERT payment (payment_mode='OFL', is_offline=1, payment_status=1)
│    db->trans_begin/commit per scheme
├─ UPDATE customer: last_sync_time
└─ Logs to log/khimji/{date}.txt (mkdir 0777)
```

---

## Flow 14: Customer Ledger Reports

```
POST /adminapp_api/customer_ledger [L4354-4375]
  ├─ get_values(): id_employee, login_type, fromdate (unused!), todate (unused!)
  ├─ adminappapi_model::customer_reports(id_employee, login_type)
  │    ⚠️ fromdate/todate parsed but never passed to model — report is not date-filtered
  └─ response(result, 200)

POST /adminapp_api/customer_ledger_details [L4377-4404]
  ├─ get_values(): id_customer
  ├─ adminappapi_model::getCustomerSchAcc(id_customer) → scheme account list
  ├─ For each: mobileapi_model::chit_scheme_detail(id_scheme_account)
  └─ response(result, 200)
```

---

## Flow 15: paymt.php PayU Web Callbacks

```
POST /paymt/payment_success [L1731-1948] (public)
  Uses $_POST; for fields: txnid, mihpayid, mode, cardnum, bank_ref_num
  ├─ getWalletPaymentContent(txnid) → IF redeemed_amount > 0: insertWalletTrans()
  ├─ payment_modal::updateGatewayResponse() → payment_status = success or awaiting
  ├─ IF auto_pay_approval in 1,2,3:
  │    For each pay linked to txnid:
  │       integrationType==5 → generateAcNoOrReceiptNo()
  │       insert_referral_data (if allow_referral==1)
  │       agent credit (agent_credit_type==1) → insert_agent_transaction()
  │       one_time_premium → UPDATE scheme_account firstPayment_amt
  │       receipt_no_set==1 → generate_receipt_no()
  │       schemeacc_no_set==0|3 → account_number_generator()
  │       firstPayment fixing
  │       integrationType==2 → insert_common_data()
  ├─ SMS + WhatsApp + Email
  └─ redirect to /paymt/payment_history

POST /paymt/payment_failure [L1663-1730]
  ├─ updateGatewayResponse() → payment_status = failure(3)
  ├─ SMS + Email notification
  └─ redirect to /paymt

POST /paymt/payment_cancel [L1949-2011]
  ├─ updateGatewayResponse() → payment_status = cancel(4)
  ├─ SMS + Email notification
  └─ redirect to /paymt
```

---

## Flow 16: paymt.php Mobile PayU Callbacks (successMURL/failureMURL/cancelMURL)

```
POST /paymt/successMURL [L2863-3112]
  Same structure as payment_success but for mobile URLs.
  ├─ Wallet check + insertWalletTrans
  ├─ updateGatewayResponse (success or awaiting)
  ├─ IF auto_pay_approval: full post-payment ops (same as payment_success)
  ├─ IF gateway!='' redirect to hdfcTransStatus
  └─ Else no redirect (mobile handles itself)

POST /paymt/failureMURL [L3114-3187]
  ├─ updateGatewayResponse → failure
  ├─ SMS + Email
  └─ IF gateway!='' → redirect to hdfcRedirect/failed

POST /paymt/cancelMURL [L3189-3250]
  ├─ updateGatewayResponse → cancel
  └─ No SMS/Email
```

---

## Flow 17: paymt.php wallet_transactionDB — Wallet Debit

```
paymt.php::wallet_transactionDB($payamt, $totamtuse_wallet) [L3379-~]
  ├─ Gets wallet account by mobile (from session)
  ├─ Calculates debit amount
  ├─ INSERT wallet_transaction (type=1 = debit)
  ├─ UPDATE inter_wallet_account: available_points/amount
  └─ Returns status

Note: There are TWO variants in the codebase:
  - paymt.php::wallet_transactionDB($payamt, $totamtuse_wallet)  [legacy 2-param]
  - payment_modal::wallet_transactionDB('insert', '', $wallet_data) [newer 3-param]
  Both are used in different places. Signature mismatch is a potential bug.
```

---

## Flow 18: paymt.php adminAppSuccess — Web Admin App Redirect Handler

```
paymt.php::adminAppSuccess($paymtData) [L2595-2857]
Called from: paymt.php::mobile_payment() after gateway==0 trans_commit

Differences from adminapp_api.php::adminAppSuccess():
  - Uses $trans_id (undefined! — BUG at L2611: $trans_id used inside transData but never defined)
  - Uses $serviceID = 7 (FAILURE SMS!) not serviceID=3 (PAYMENT SUCCESS SMS!)
    ⚠️ Bug: Uses wrong service ID → sends wrong SMS template for successful payments!
  - On trans_commit → redirect('paymt/adminapp/success')
  - On failure → redirect('paymt/adminapp/failed')
  - paymt.php::adminapp('success') → echo "Payment success.Please wait..." (browser redirect msg)
```

**⚠️ Critical Bug:** `paymt.php::adminAppSuccess` uses `$serviceID = 7` (the failure/pending service) to send SMS. Successful collection payments made through `paymt.php` will send failure-template SMS to customers.
