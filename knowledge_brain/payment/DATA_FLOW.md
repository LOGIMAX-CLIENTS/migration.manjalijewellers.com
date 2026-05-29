# Payment Module — Data Flow
> **Updated**: 2026-03-24 | **Round**: 3

## Flow 1: CREATE — Manual Payment (SaveAll) 

### Trigger
User fills payment form → clicks Save → JS submits via AJAX POST

### End-to-End Trace

```
[Browser] User fills form with customer, scheme account, amount, mode(s)
    ↓
[JS: payment.js ~L3346] AJAX POST to payment/save_all (or payment/save)
    - Collects: generic{}, cus_pay_mode{}, pdc{}, adv{}, type
    - Multi-mode: card_pay, chq_pay, net_bank_pay, adv_adj, vch_pay parsed from JSON
    ↓
[Controller: payment() L1299-2407, case 'SaveAll']
    1. Parse installments: if > 1, divide amount
    2. Determine payment_mode: CSH/CC/DC/CHQ/NB/VCH/ADV_ADJ/REF_WALLET/MULTI
       (L1333-1385 — complex if/elseif chain)
    3. verify_form_secret() — CSRF protection
    4. CREATE LOG → log/{date}/manual/create_payment_{date}.txt
    5. OTP verification via session
    6. Get customer account data: account_model->get_customer_acc()
    7. Wallet redemption calculation (L1428-1444)
    8. FOR LOOP (i=1 to installments):
       a. GST Calculation (L1447-1460):
          - Exclusive: gst_amt = actual_pay × (gst/100)
          - Inclusive: gst_amt = actual_pay - (actual_pay × (100/(100+gst)))
       b. Metal Weight Calculation (L1461-1484):
          - fix_weight=2 (fixed): amount_to_weight()
          - fix_weight=3 (flexible): depends on flexible_sch_type
          - else: use provided metal_weight
       c. Branch Resolution (L1525-1546):
          - branchwise_cus_reg + !payOtherBranch → cus_reg_branch
          - branchWiseLogin + !payOtherBranch → customer branch
          - branchWiseLogin + payOtherBranch + empLog_branch → emp branch
          - else → form id_branch or NULL
       d. Receipt Number Generation (L1551-1555):
          - If receipt_no_set==1 → generate_receipt_no()
          - Else → null
       e. DigiGold benefits (L1556-1598)
       f. Due Type resolution: PN→ND/PD, AN→ND/AD
       g. trans_begin()
       h. Build pay_array with 30+ fields
       i. payment_model->paymentDB("insert", "", pay_array)
          → INSERT into `payment` table
       j. Account Number Generation (if first payment):
          - isAcnoAvailable() check
          - updateGroupCode() (lucky draw)
          - account_model->account_number_generator()
          - account_model->update_account()
       k. Insert payment_mode_details for each mode:
          - CSH → insertData(arrayCashPay, 'payment_mode_details')
          - CC/DC → insertBatchData(arrayCardPay, 'payment_mode_details')
          - CHQ → insertBatchData(arraychqPay, 'payment_mode_details')
          - NB → insertBatchData(arrayNBPay, 'payment_mode_details')
          - ADV_ADJ → insertData(ret_advance_utilized) + insertData(payment_mode_details)
          - VCH → insertBatchData(arrayVchPay, 'payment_mode_details')
       l. Wallet Transaction (if used): insertWalletTrans()
       m. Insert payment_status record
       n. Sync: insert_common_data() or insert_common_data_jil()
       o. Referral: insert_referral_data()
       p. Agent/Employee Incentive: insertAgentIncentive(), insertEmployeeIncentive()
       q. Update paid installments in scheme_account
    9. trans_commit() or trans_rollback()
    10. Send SMS/Email: sendSMSMail()
    ↓
[Response] JSON with payment details, payid[], type, payment_status
```

### Tables Written (in order)
1. `payment` — Main payment record
2. `scheme_account` — Update account number (if first pay), update paid_ins
3. `payment_mode_details` — One row per payment mode used
4. `ret_advance_utilized` — If advance adjustment used
5. `payment_status` — Status log entry
6. `sync_*` tables — Integration sync data
7. `referral_*` tables — If referral applicable
8. `agent_incentive` / `employee_incentive` — If incentive applicable
9. `wallet_transaction` — If wallet used

---

## Flow 2: EDIT — Update Existing Payment (Update_payment)

### Trigger
User clicks Edit on a payment → loads data → modifies → submits

### End-to-End Trace

```
[JS: payment.js L5150] AJAX POST to payment/edit_payment/{id}
    → Controller: payment/Edit_payment → model->edit_payment(id)
    → Returns payment data JSON
    ↓
[JS: payment.js L5253] Populates edit form
    ↓
[JS: payment.js L5724] AJAX POST to payment/update_payment/{id}
    ↓
[Controller: payment() case 'Update_payment' L826-1273]
    1. Parse multi-mode data (same as SaveAll)
    2. Determine new payment_mode
    3. trans_begin()
    4. Update payment table: paymentDB("update", id, data)
       → UPDATE payment SET payment_status, date_payment, metal_rate, 
         metal_weight, payment_mode, remark, date_upd, last_update, id_employee
    5. model->benifit(id, data) — Update benefits
    6. model->deleteUtilized(id) — Remove old advance utilization
    7. IF prev_pay_mode != new payment_mode:
       a. update_modestatus_data({is_active: 0}) — Soft-delete old mode records
       b. Insert new payment_mode_details for each mode (same as SaveAll)
       c. Insert ret_advance_utilized if ADV_ADJ
       d. Insert VCH details if voucher
    8. ELSE IF prev_pay_mode == new payment_mode:
       a. Same as above (soft-delete + re-insert)
    9. Update paid installments in scheme_account
    10. trans_commit() or trans_rollback()
    11. Log to log_detail
```

### ⚠️ Key Risk: Edit uses DELETE+INSERT pattern for mode details
- Old records: `is_active = 0` (soft delete)
- New records: fresh INSERT
- This means the edit is NOT an in-place UPDATE — it's a soft-delete + re-insert

---

## Flow 3: DELETE — Delete Payment

### Trigger
User clicks Delete button → browser navigates to GET URL

```
[Browser] GET /payment/delete/{id}
    ↓
[Controller: payment() case 'Delete' — within switch]
    1. No CSRF protection (GET request) ⚠️
    2. model->paymentDB("delete", id)
       → DELETE FROM payment WHERE id_payment = {id}
    3. Hard delete — no soft delete, no audit trail
    4. redirect('payment/list')
```

### ⚠️ CRITICAL RISKS
- **GET request** = CSRF vulnerable (can be triggered by image tag/link)
- **Hard delete** = No recovery, no audit log
- **No child cleanup** = `payment_mode_details`, `payment_status`, `ret_advance_utilized` records orphaned
- **No installment count update** = `scheme_account.total_paid_ins` becomes stale

---

## Flow 4: General Advance Payment

### Trigger
User submits general advance payment form

```
[Controller: payment() case 'general_advance' L392-816]
    1. Same multi-mode parsing as SaveAll
    2. Log to log/{date}/general_advance/
    3. Same GST, metal weight, branch, wallet logic
    4. trans_begin()
    5. Generate receipt_no for 'general_advance_payment' type
    6. get_curPaid_insNo() — Get current installment number
    7. Build pay_array with due_type='GA'
    8. paymentDB("general_advance_insert") 
       → INSERT INTO general_advance_payment
    9. Insert mode details into general_advance_mode_detail (not payment_mode_details!)
    10. Insert ret_advance_utilized if ADV_ADJ
    11. trans_commit() or trans_rollback()
    12. send SMS/Mail
```

### Key Difference from SaveAll
- Uses `general_advance_payment` table (not `payment`)
- Uses `general_advance_mode_detail` table (not `payment_mode_details`)
- `due_type = 'GA'`

---

## Flow 5: Post-Dated Cheque (PDC) → Payment Conversion

### Trigger
PDC record reaches maturity → Admin approves → Converts to payment

```
[Controller: postdate_payment_form() case 'Update' L200-373]
    1. Update postdate_payment status
    2. IF status == 1 (Success):
       a. isAcnoAvailable() — Check if account number exists
       b. If not, generate account number
       c. Generate receipt_no
       d. Build ins_pay_array from PDC data
       e. paymentDB("insert") → INSERT into payment table
       f. Insert payment_status record
       g. Sync: insert_common_data() or insert_common_data_jil()
       h. Send SMS/Email
       i. redirect('payment/list')
    3. IF status != 1 (other status change):
       a. Insert payment_status log
       b. Send SMS/Email for status change
       c. redirect('postdated/payment/list')
```

---

## Flow 6: Online Payment Verification

### Trigger
Admin views pending online payments → clicks Verify → system checks gateway

```
[Controller: verify_payment() L3788-3845]
    1. Get payment data by ref_trans_id
    2. Determine gateway from id_payGateway
    3. Route to specific verify method:
       - Cashfree → verify_cashfreepayment()
       - Razorpay → verifyRazorPayments()
       - EaseBuzz → verify_easebuzzpayment()
       - PayU → verify_PayUpayments()
       - HDFC → verify_hdfcpayment()
       - TechProcess → verifyWithTechProcess()
    4. Each verify method:
       a. API call to gateway
       b. Parse response
       c. UPDATE payment SET payment_status, gateway response fields
       d. If SUCCESS:
          - Generate receipt_no
          - Update account number if needed
          - Update paid installments
          - Send notification
       e. If FAILURE: Update status only
```

---

## JS Function Map (payment.js — key functions)

| Function/Block | Line | Purpose |
|---|---|---|
| Customer selection handler | ~L104 | Load scheme accounts for customer |
| OTP resend | ~L159 | Resend payment OTP |
| Scheme account selection | ~L182 | Load account details: amount, weight, installments |
| Payment mode toggle | ~L998 | Switch between cash/card/cheque/NB/voucher |
| Revert approval | ~L1010 | Revert JIL approval |
| Payment list load | ~L1028 | Load payment list with filters |
| Receipt number update | ~L1595 | Update receipt number |
| Customer search autocomplete | ~L1622 | Search customers |
| Form data load | ~L1722 | Load payment modes, banks, status |
| Account detail load | ~L1947 | Load selected account details |
| OTP generate | ~L3289 | Generate OTP for payment |
| OTP verify | ~L3310 | Verify entered OTP |
| Save payment submit | ~L3346 | Main save handler |
| Payment update | ~L3400 | Update existing payment |
| Payments data list | ~L3518 | Load payments data |
| Estimation details | ~L3803 | Get estimation for account |
| Ref number lookup | ~L4420 | Check reference number uniqueness |
| Cheque number lookup | ~L4465 | Check cheque number uniqueness |
| Advance adjustment | ~L4808 | Load advance details for adjustment |
| Edit payment load | ~L5150 | Load payment for editing |
| Edit payment submit | ~L5724 | Submit edited payment |
| Payment modes list | ~L5746 | Get mode details for payment |
| QR account detail | ~L5838 | Load account from QR scan |
| Update remark | ~L6046 | Update payment remark |

---

## Flow 7: Online Payment Approval (update_pay_status)

### Trigger
Admin clicks Approve on a pending/awaiting online payment

```
[Controller: update_pay_status() L2853-2946]
    1. Get POST data: id_payment, payment_status, payment details
    2. Build update array with new status
    3. IF status == 1 (Approve):
       a. IF receipt_no_set == 0 → generate_receipt_no()
       b. Check isAcnoAvailable() → generate account number if needed
       c. paymentDB("update", id, updateData)
       d. Update paid installments: updData(scheme_account)
       e. Insert payment_status log record
       f. Send SMS + Email notification
       g. Sync: insert_common_data() or insert_common_data_jil()
    4. IF status != 1 (Reject/other):
       a. paymentDB("update", id, updateData)
       b. Insert payment_status log record
       c. Send notification
    5. Return JSON response
```

### Tables Written
1. `payment` — Status update, receipt_no, approval_date
2. `scheme_account` — total_paid_ins increment
3. `payment_status` — Audit log entry
4. `sync_*` — If integration active

---

## Flow 8: Invoice/Receipt Generation

### Flow 8a: PDF Invoice (generateInvoice)
```
[Controller: generateInvoice($id) L2692-2768]
    1. Get payment data: model->get_invoiceData($id)
    2. Get scheme account data: model->get_paymentContent()
    3. Get GST splitup: model->get_gstSplitupData()
    4. Get paid installment count for serial number
    5. Company details (branch-aware)
    6. Convert amount to words: no_to_words()
    7. Load view: 'include/receipt' with data
    8. DomPDF: load_html → set_paper("a4") → render → stream
```

### Flow 8b: Thermal Receipt (thermal_invoice)
```
[Controller: thermal_invoice($id, $type, $date) L4950-5022]
    TYPE = 'Payment':
        1. Get invoice data
        2. Get scheme account content
        3. Get GST splitup
        4. Get paid installment count for serial
        5. Company details (branch-aware)
        6. Load view: 'include/receipt_thermal_prn'
        7. Output as .prn file download (Content-Disposition: attachment)
    
    TYPE = 'Customer':
        1. Get customer data
        2. Load view: 'include/cusdetails_thermal'
        3. DomPDF render to PDF
    
    TYPE = 'CloseAccount':
        1. Get closed account data
        2. Load view: 'include/schemeaccount'
        3. DomPDF render A4 portrait PDF
    
    TYPE = 'WalletTransaction':
        1. Get wallet transaction data
        2. Load view: 'include/wallet_thermal'
        3. DomPDF render custom-size PDF
```

---

## Flow 9: OTP Generation & Verification

```
[JS: payment.js ~L3289] AJAX POST to /admin_payment/generateotp
    - Sends: id_customer
    ↓
[Controller: generateotp() L5023-5069]
    1. Get customer data (mobile, name, email)
    2. Get OTP expiry duration from DB: payOTP_exp()
    3. Generate 6-digit OTP: mt_rand(100001, 999999)
    4. Store in session: pay_OTP, pay_OTP_expiry
    5. Build SMS message with OTP
    6. Send via configured SMS gateway (1-5)
    7. If email exists → send email with OTP
    8. Insert into otp table: otp_insert()
    9. Return JSON: {result: 3, msg: 'OTP Sent', otp: OTP}
    ⚠️ OTP returned in response (security risk for production)
    ↓
[JS: payment.js ~L3310] AJAX POST to /admin_payment/update_otp
    - Sends: otp (user-entered)
    ↓
[Controller: update_otp() L5070-5091]
    1. Get submitted OTP from POST
    2. select_otp() from DB
    3. Check session pay_OTP match
    4. IF match AND NOT expired:
       - Update otp table: is_verified=1, verified_time
       - Return {result: 1, msg: 'OTP updated successfully'}
    5. IF expired: Return {result: 5, msg: 'OTP expired'}
    6. IF mismatch: Return {result: 6, msg: 'Invalid OTP'}
    ⚠️ BUG: L5081 uses = instead of ==, always evaluates true
```

---

## Flow 10: Revert Payment Approval

### Standard Revert
```
[JS: payment.js ~L1010] AJAX POST to /admin_payment/revertApproval
    ↓
[Controller: revertApproval() L3686-3743]
    1. Get payment ID from POST
    2. Update payment: set payment_status = 2 (Awaiting)
    3. Decrement scheme_account.total_paid_ins
    4. Insert payment_status log: status reverted
    5. Return success/fail JSON
    
    ⚠️ Does NOT revert: receipt_no, wallet transactions, referral benefits
```

### JIL Revert
```
[Controller: revertApproval_jil() L3647-3685]
    1. Same as standard revert
    2. ALSO: Delete/revert sync records in sync_* tables
    3. Revert JIL-specific integration data
```

---

## Flow 11: Weight Settlement

```
[Controller: weight_settlement() L3119-3138]
    CRUD switch: View/Save/List/Ajax
    
    Save:
        1. Parse settlement data from POST
        2. trans_begin()
        3. Insert into settlement table
        4. For each detail line: insert into settlement_detail
        5. Update payment records: is_settled = 1
        6. trans_commit()
        
    ↓
[Controller: update_settlement() L3069-3118]
    1. Get settlement update data
    2. Calculate weight from amount: amount_to_weight()
    3. Update settlement table
    4. Insert/update settlement_detail records
```

---

## Flow 12: Retry Account/Receipt Generation

```
[Controller: retryAccOrReceiptGen() L6000-6095]
    1. Get payment ID
    2. Get payment data
    3. IF account number missing:
       a. Check isAcnoAvailable()
       b. updateGroupCode() if lucky draw
       c. account_number_generator()
       d. update_account()
    4. IF receipt number missing AND receipt_no_set == 0:
       a. generate_receipt_no()
       b. Update payment record with receipt_no
    5. Return success JSON
```

---

## Flow 13: Cashfree Gateway Verification (Detailed)

```
[Controller: verify_cashfreepayment($data) L3846-4172]
    1. Get branch gateway data: getBranchGatewayData(branch, pg_code=4)
       → secretKey = param_1, appId = param_3, api_url
    2. FOR EACH transaction in txn_ids:
       a. Build cURL request:
          - URL: {api_url}/{orderId}/payments
          - Headers: x-client-id, x-client-secret, x-api-version: 2022-09-01
          - Method: POST
          - Timeout: 8 seconds
       b. Execute cURL
       c. Log response to log/{date}/cashfree/mob_response_{date}.txt
       d. IF response is array (multiple payments per order):
          foreach payment in response:
            - Map Cashfree status → internal status:
              SUCCESS → 1, FAILED → 3, CANCELLED → 4, 
              PENDING → 7, FLAGGED → 2
            - Extract: cf_payment_id, payment_group (mode)
            - Build updateGateData array
            - IF SUCCESS:
              * Generate receipt_no (if receipt_no_set==0)
              * Generate account number (if first payment)
              * map_paymentMode(): map gateway mode to internal mode
              * Update paid installments
              * update_paymentMode(): auto-create mode if not exists
              * Insert wallet transaction if applicable
              * paymentDB("update") with all gateway data
              * Send SMS/Email
              * Sync data
            - ELSE:
              * paymentDB("update") with status only
       e. IF response is object (single payment):
          Same logic as above but for single response
    3. Increment vCount for each verified
    4. Echo verification results
```

