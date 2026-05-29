# Component 2 — Payment CRM: Data Flow (Engine Reverse Engineering)

> **Module:** Payment CRM  
> **Generated:** 2026-02-18  
> **Updated:** 2026-02-18 (Verified table names)
> **Primary Flow:** UI Load → Account Detail → Pre-save Validation → SaveAll

---

## FLOW 1: UI Load (Payment Add)

### Route: `GET /payment/add`

```
Browser
  │
  ├─→ Route: payment/add → admin_payment/payment/View/
  │
  ├─→ Controller: admin_payment::payment('View')
  │     ├─→ payment_model::get_payModes()         → Payment mode master
  │     ├─→ payment_model::get_gstsettings()       → GST toggle
  │     ├─→ payment_model::checkSettings()         → Module settings
  │     ├─→ payment_model::allow_wallet()          → Wallet feature toggle  
  │     ├─→ payment_model::get_receipt_no_settings() → Receipt settings
  │     ├─→ payment_model::entry_date_settings()   → Day close settings
  │     ├─→ settings_model::get_access('payment/add') → RBAC check
  │     └─→ Load view: payment/form.php
  │           └─→ $data['pay'] = aggregated settings array
  │
  ├─→ View: form.php renders with:
  │     ├─→ Customer mobile autocomplete (#mobile_number)
  │     ├─→ Scheme account search (#Scheme_account_no)  
  │     ├─→ QR/Barcode scan (#qr_scan_scheme_account_id)
  │     ├─→ Branch select (#branch_select)
  │     ├─→ Employee select (#employee_select)
  │     ├─→ Hidden fields: 50+ generic[*] inputs
  │     ├─→ Payment mode table (dynamic from PHP)
  │     └─→ Make Payment box
  │
  └─→ JS: payment.js auto-runs on page load:
        ├─→ load_paystatus_select()     → AJAX: payment/get/ajax_data
        │     └─→ Populates: #pay_mode, #payment_status, banks, drawees
        ├─→ get_payment_device_details() → Card terminal devices
        ├─→ get_payment_bank_details()   → Bank list for NB
        ├─→ get_branchnames() / getBranchEmployee() → Branch/employee dropdowns
        └─→ If id_scheme_account preset:
              ├─→ load_account_detail(sch_id)
              └─→ loadschemeaccountbyidcus(id_cus)
```

---

## FLOW 2: Customer Search & Scheme Account Selection

### Sub-flow 2a: Customer Mobile Autocomplete
```
User types in #mobile_number
  │
  └─→ jQuery autocomplete:
        ├─→ AJAX GET: admin_customer/searchcustomer/{query}
        │     └─→ Returns: [{mobile, name, id_customer}]
        └─→ On select (id_customer):
              ├─→ AJAX GET: payment/get/ajax/customer/account/{id_customer}
              │     └─→ Controller: ajax_customer_schemes($id_customer)
              │           ├─→ payment_model::get_customer_schemes($id_customer)
              │           │     └─→ Returns: scheme accounts [{id, scheme_acc_number, scheme_name, status}]
              │           └─→ payment_model::wallet_balance($id_customer)
              │                 └─→ Returns: {wal_balance, redeem_percent}
              │
              └─→ JS populates:
                    ├─→ #scheme_account (select2 dropdown)
                    ├─→ .wallet_balance (hidden input)
                    └─→ #id_customer (hidden)
```

### Sub-flow 2b: Scheme Account Number Search
```
User types in #Scheme_account_no
  │
  └─→ jQuery autocomplete:
        ├─→ AJAX GET: admin_manage/searchSchemeAccountNo/{query}
        │     OR (if #oldAccountCheck checked): admin_manage/searchOldSchemeAccountNo/{query}
        │     └─→ Returns: [{scheme_acc_number, id_scheme_account, customer_name, mobile}]
        └─→ On select:
              ├─→ Sets #id_customer, #mobile_number, #id_scheme_account
              ├─→ loadschemeaccountbyidcus(id_cus)
              └─→ load_account_detail(id_scheme_account)
```

---

## FLOW 3: Account Detail Load ("The Big Query")

### Route: `GET /payment/get/ajax/account/{id_scheme_account}`

```
JS: load_account_detail(id_scheme_account)
  │
  ├─→ AJAX GET: payment/get/ajax/account/{id}
  │     └─→ Optional: ?date={metalrate_edit_date}
  │
  ├─→ Controller: ajax_account_detail($id, $date)
  │     └─→ payment_model::get_paymentContent($id, $date)
  │           ── THIS IS THE 1,043-LINE "BIG QUERY" (lines 1400-2443) ──
  │           │
  │           ├─→ Query 1: Main account data
|           │     SELECT sa.*, s.*, c.*, b.*, ...
|           │     FROM scheme_account sa
|           │     JOIN scheme s
|           │     JOIN customer c  
|           │     JOIN branch b
|           │     WHERE sa.id_scheme_account = $id
|           │
|           ├─→ Query 2: Payment summary
|           │     SELECT COUNT(*), SUM(payment_amount), SUM(metal_weight)
|           │     FROM payment
|           │     WHERE id_scheme_account = $id AND payment_status = 1
|           │
  │           ├─→ Query 3: Installment calculation
  │           │     Computes: paid_installments, unpaid_dues, allowed_dues
  │           │     Based on: total_installments, scheme_type, due_type
  │           │
  │           ├─→ Query 4: DigiGold benefits (if applicable)
  │           │     Computes: benefit amount/weight, benefit_chart data
  │           │
  │           ├─→ Query 5: Advance payments check
  │           │     SELECT pending general_advance records
  │           │
  │           ├─→ Query 6: GST configuration
  │           │     Maps gst%, gst_type (inclusive/exclusive)
  │           │
  │           ├─→ Query 7: Metal rate 
  │           │     Current rate OR date-specific rate
  │           │
  │           ├─→ Query 8: Discount rules
  │           │     First-payment discount, referral discount
  │           │
  │           └─→ Returns: $data['account'] (80+ fields)
  │
  └─→ JS processes response and populates form:
        ├─→ Scheme details: #start_date, #acc_name, #scheme_code, #scheme_type
        ├─→ Payment status: #paid_installments, #total_amount_paid, #total_weight_paid
        ├─→ Business logic: #unpaid_dues, #allow_pay, #allowed_dues, #due_type
        ├─→ Hidden inputs: #fix_weight, #sch_type, #flexible_sch_type, #gst_type, #gst_percent
        ├─→ Metal data: #metal_rate, #current_metal_rate
        ├─→ Discount: #discount, #discount_installment, #discount_type, #firstPayDisc_value
        ├─→ Wallet: .wallet_balance, .redeem_percent
        ├─→ Weight grid: weight selection checkboxes (if weight-based scheme)
        ├─→ Payment calendar: installment payment calendar with highlights
        ├─→ DigiGold: benefit amount/weight display
        └─→ Enables/disables form sections based on scheme type
```

---

## FLOW 4: Pre-Save Validation (Client-Side)

### Trigger: `$("input[name='type']:checkbox").change()`
```
User clicks Save checkbox (pay_save/pay_print)
  │
  ├─→ Validation chain (lines 3111-3266):
  │     │
  │     ├─→ [1] Weight scheme check: selected_weight ≤ eligible_weight
  │     ├─→ [2] Weight > 0 check (if weight scheme)
  │     ├─→ [3] Payment date not empty
  │     ├─→ [4] Scheme account selected
  │     ├─→ [5] Payment amount > 0 (sum_of_amt)
  │     ├─→ [6] Received ≥ Payment (no underpayment)
  │     ├─→ [7] Received ≤ Payment (no overpayment)
  │     ├─→ [8] Payment status selected
  │     ├─→ [9] Branch selected (if branch_settings enabled)
  │     ├─→ [10] Employee selected
  │     ├─→ [11] Metal rate date selected (if editing enabled)
  │     ├─→ [12] Metal rate selected (if editing enabled)
  │     ├─→ [13] Amount scheme type: total_amt + gst == payment_amt
  │     ├─→ [14] GST exclusive: total_amt + gst == payment_amt  
  │     ├─→ [15] Wallet: total_amt == payment_amt + redeem_request
  │     └─→ [16] Non-wallet: total_amt == payment_amt
  │
  ├─→ If all pass:
  │     ├─→ form_data = $('#pay_form').serialize()
  │     ├─→ Disable submit button
  │     └─→ insert_payment(form_data)
  │
  └─→ insert_payment(form_data):
        ├─→ If OTP required (#isOTPRegForPayment == 1):
        │     ├─→ AJAX POST: admin_payment/generateotp
        │     ├─→ Show OTP modal
        │     └─→ On verify: update_otp() → payment_success()
        └─→ Else: payment_success(form_data) directly
```

---

## FLOW 5: SaveAll (Primary Save)

### Route: `POST /payment/save_all` → `admin_payment/payment/SaveAll/`

```
payment_success(post_data)
  │
  ├─→ Determine URL:
  │     ├─→ If due_type == 'GEN_ADV': admin_payment/payment/general_advance
  │     └─→ Else: payment/save_all
  │
  ├─→ AJAX POST to save_all with: 
  │     - generic[*] — 50+ form fields
  │     - cus_pay_mode[card_pay|chq_pay|net_bank_pay|cash_payment|adv_adj|vch_pay]
  │     - adv[advance_muliple_receipt]
  │     - pdc[cheque_no|date_payment|payee_bank|payee_branch|payee_ifsc]
  │     - type (1=print+save, 2=save-only)
  │
  └─→ Controller: payment('SaveAll'):

     PHASE 1: INPUT PARSING (lines 1300-1348)
     ├─→ Parse generic[], cus_pay_mode[] from POST
     ├─→ Server-side installment validation (BUG-S31-007 fix)
     │     └─→ Re-query get_paymentContent() to verify allowed_dues
     ├─→ Calculate per-installment amounts:
     │     ├─→ amount = (payment_amount + discountedAmt) / installments
     │     └─→ payment_amount = payment_amount / installments
     ├─→ GST adjustment for non-weight schemes
     └─→ Decode JSON: card_pay, chq_pay, net_bank_pay, adv_adj, vch_pay

     PHASE 2: PAYMENT MODE DETERMINATION (lines 1350-1402)
     ├─→ Algorithm: Check sizeof() of each mode array + cash > 0
     │     ├─→ Single cash → CSH
     │     ├─→ Single card(s) → CC or DC (or MULTI if mixed)
     │     ├─→ Single cheque → CHQ
     │     ├─→ Single NB → NB
     │     ├─→ Single voucher → VCH
     │     ├─→ Single wallet → REF_WALLET
     │     ├─→ Single advance → ADV_ADJ
     │     └─→ Any combination → MULTI
     └─→ Write request log to file

     PHASE 3: PRE-LOOP SETUP (lines 1403-1466)
     ├─→ OTP validation
     ├─→ Customer account data (get_customer_acc)
     ├─→ Wallet calculation:
     │     ├─→ allowed_redeem = totalamount × (redeem_percent / 100)
     │     ├─→ can_redeem = min(allowed_redeem, wal_balance)
     │     └─→ redeemed_amount = floor(min(redeem_request, can_redeem))
     ├─→ GST scheme data (get_schgst)
     └─→ $this->db->trans_begin()  ← TRANSACTION START

     PHASE 4: INSTALLMENT LOOP (lines 1467-2100+)
     FOR $i = 1 TO installments:
     │
     ├─→ [4.1] GST Calculation
     │     ├─→ Exclusive: gst_amt = actual_pay × (gst / 100)
     │     └─→ Inclusive: gst_amt = actual_pay - (actual_pay × (100 / (100 + gst)))
     │
     ├─→ [4.2] Metal Weight Calculation
     │     ├─→ fix_weight=2: wgt = (sch_amt - gst_amt) / metal_rate
     │     ├─→ fix_weight=3 + flexible_type in [3,4,7,8]: wgt = (pay_amt - gst_amt) / metal_rate
     │     ├─→ fix_weight=3 + flexible_type=2 (wgt_convert≠2): wgt = (pay_amt - gst_amt) / metal_rate
     │     ├─→ fix_weight=3 + flexible_type=5 (wgt_store_as=1): wgt = (pay_amt - gst_amt) / metal_rate
     │     └─→ Else: wgt = generic[metal_weight]
     │
     ├─→ [4.3] Due Type Resolution
     │     ├─→ PN → first=ND, rest=PD
     │     ├─→ AN → first=ND, rest=AD
     │     └─→ Else → as-is
     │
     ├─→ [4.4] Branch Resolution (5-level cascade)
     │     ├─→ branchwise_cus_reg=1 && payOtherBranch=0 → cusData['cus_reg_branch']
     │     ├─→ branchWiseLogin=1 && payOtherBranch=0 → cusData['branch']
     │     ├─→ payOtherBranch=0 → cusData['branch']
     │     ├─→ branchWiseLogin=1 && payOtherBranch=1 && empLog_branch → session empLog_branch
     │     └─→ Else → generic[id_branch]
     │
     ├─→ [4.5] Receipt Number
     │     └─→ If rptnosettings=1: generate_receipt_no(id_scheme, id_branch)
     │
     ├─→ [4.6] DigiGold Benefits
     │     ├─→ If is_digi=1 && interest=1:
     │     │     ├─→ Get digi_account → get_digi_benefit
     │     │     ├─→ Same commodity: benefit_amt = payment × (interest_value / 100)
     │     │     │     └─→ benefit_wgt = benefit_amt / metal_rate
     │     │     └─→ Other commodity: benefit_wgt = metal_wgt × (interest_value / 100)
     │     │           └─→ benefit_amt = wgt × other_metal_rate
     │     └─→ Else: all benefits = 0
     │
     ├─→ [4.7] Build pay_array (50+ fields) → paymentDB("insert")
|     └─→ INSERT into payment → Returns {status, insertID}
     │
     ├─→ [4.8] OTP Rate Fix (if applicable)
     │     └─→ If one_time_premium && rate_fix_by=0 && rate_select=1:
     │           └─→ updFixedRate(fixed_wgt, firstPayment_amt, fixed_metal_rate)
     │
     ├─→ [4.9] Account Number Generation (if applicable)
     │     └─→ If isAcnoAvailable && schemeacc_no_set=0:
     │           ├─→ Lucky draw: updateGroupCode()
     │           └─→ account_number_generator() → update_account()
     │
     ├─→ [4.10] Integration Sync
     │     ├─→ integrationType=1: insert_common_data_jil()
     │     └─→ integrationType=2: insert_common_data()
     │
     ├─→ [4.11] Payment Mode Detail Insert
|     ├─→ Cash: insertData(arrayCashPay, 'payment_mode_details')
|     ├─→ Card(s): insertBatchData(arrayCardPay, 'payment_mode_details')
|     ├─→ Cheque(s): insertBatchData(arraychqPay, 'payment_mode_details')
|     ├─→ NB(s): insertBatchData(arrayNBPay, 'payment_mode_details')
|     ├─→ Voucher(s): insertBatchData(arrayVchPay, 'voucher_utilized')
|     ├─→ Advance: insertData(data_adv_amount, 'ret_advance_utilized')
|     │           + insertData(array_adj_pay, 'payment_mode_details')
     │     └─→ Wallet: deduct_wallet()
     │
     ├─→ [4.12] Payment Status Log
|     └─→ payment_statusDB("insert") → payment_status_message
     │
     ├─→ [4.13] Estimation Linkage
     │     └─→ If estimation data: update_estimation_status()
     │
     └─→ [4.14] Notification
           └─→ sendSMSMail('3', payData, subject, type, insertID)
                 ├─→ SMS (5 gateways: MSG91, Nettyfish, SpearUC, Asterixt, Qikberry)
                 ├─→ WhatsApp
                 └─→ Email (via mail_model)

     PHASE 5: TRANSACTION COMMIT (lines ~2050-2100)
     ├─→ If trans_status() === TRUE:
     │     ├─→ trans_commit()
     │     └─→ Return JSON: {payid: [...], type, payment_status, id_scheme_account}
     └─→ Else:
           ├─→ trans_rollback()
           └─→ Return error
```

---

## FLOW 6: Post-Save (Client-Side)

```
payment_success AJAX callback:
  │
  ├─→ If payment_status == 1 (Success):
  │     ├─→ If type == 1 (Print+Save):
  │     │     ├─→ If topup_scheme: window.open(generateInvoice/{id}/{sa_id})
  │     │     ├─→ If GEN_ADV: window.open(generateAdvanceInvoice/{id}/{sa_id})
  │     │     ├─→ If OTP price fix: window.open(get_scheme_receipt/{sa_id})
  │     │     └─→ Normal: window.open(generateInvoice/{id}/{sa_id})
  │     └─→ Redirect to listing page
  │
  └─→ Else (Failure):
        └─→ Redirect to listing page with error toast
```

---

## FLOW 7: Update Payment

### Route: `POST /payment/update/{id}` → `admin_payment/payment/Update/$1`

```
Similar to SaveAll but:
  ├─→ Receives id_payment in URL
  ├─→ Loads existing payment data
|  ├─→ Deletes existing payment_mode_details records
  ├─→ Re-inserts with new mode details
|  ├─→ Updates payment record
  └─→ Re-calculates metal weight, GST
```

---

## FLOW 8: Delete Payment

### Route: `POST /payment/delete/{id}` → `admin_payment/payment/Delete/$1`

```
Controller: payment('Delete', $id)
  ├─→ paymentDB("delete", $id)
|     └─→ UPDATE payment SET is_deleted=1 WHERE id_payment=$id
  ├─→ Reverse integration data
  ├─→ Reverse wallet deduction
  └─→ Redirect to listing
```

---

## FLOW 9: General Advance Payment

### Route: `POST /admin_payment/payment/general_advance`

```
Nearly identical to SaveAll but:
  ├─→ due_type = 'GA' (always)
|  ├─→ Inserts into general_advance_payment (not payment)
|  ├─→ Mode details → general_advance_mode_detail (not payment_mode_details)
  ├─→ Different receipt generation (general_advance_payment prefix)
  └─→ Redirects to reports/general_advance
```
