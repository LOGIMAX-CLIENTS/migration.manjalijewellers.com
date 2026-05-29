# Account Module — Data Flow Traces
> **Round**: 2 | **Date**: 2026-03-24

---

## Flow 1: Create Scheme Account (Add)

```
User → POST account/save
  → account_post('Add')
    1. Read POST data: scheme[], gift_list_data[], gift_quantity[], gift_barcode[]
    2. Verify agent code → account_model->verifyAgentCode()
    3. Verify referral code → account_model->veriflyreferral_code()
    4. Get customer data → customer_model->get_cust()
    5. Get settings → admin_settings_model->settingsDB()
    6. Get entry date → customer_model->get_entrydate()
    7. Resolve branch (branchWiseLogin / is_branchwise_cus_reg / NULL)
    8. Calculate maturity_date based on maturity_type (2=fixed days, 3=fixed months)
    9. Get financial year → account_model->get_financialYear()
    10. Build $account array (35+ fields)
    11. Validate: id_customer > 0 AND id_scheme > 0 AND !empty(id_branch)
    12. Insert KYC (PAN, Aadhaar) → account_model->insert_kyc()
    13. BEGIN TRANSACTION
    14. Insert account → account_model->insert_account() → returns insertID + sch_data
    15. Generate client ID: cliIDcode/code/insertID → update_account()
    16. Insert gifts (loop gift_table_length) → SQL query for gift_name → insert_gift_issued()
    17. Insert prizes (loop gift[]) → insert_gift_issued()
    18. Upload PAN file → set_image()
    19. Handle referral code → available_refcode()
    20. If free_payment == 1:
        a. Build payment data → free_payment_data()
        b. Generate receipt number → generate_receipt_no()
        c. Insert payment → payment_model->paymentDB('insert')
        d. Get due date → payment_model->get_due_date()
        e. Update cycle data → payment_model->updData()
        f. Update paid installments → payment_model->getPaidInsData() + updData()
        g. Insert payment mode → payment_model->insertBatchData()
        h. Generate scheme account number → account_number_generator()
    21. Insert voucher → insert_gift_card()
    22. CHECK TRANS STATUS
        ✅ COMMIT → log, send join SMS/Email/WhatsApp, redirect payment/add
        ❌ ROLLBACK → flash error, redirect account/new
```

## Flow 2: Edit Scheme Account

```
User → POST account/update/:id
  → account_post('Edit', $id)
    1. Read POST scheme[]
    2. Get settings (cusName_edit)
    3. Get existing account → get_account_open($id)
    4. Handle voucher (if has_voucher):
       - Deactivate old voucher (status=5) → update_giftcard()
       - Insert new voucher → insert_gift_card()
       - Upload voucher image → set_image()
    5. Detect referral code edit type:
       - Type 2: New ref code added
       - Type 1: Old ref code changed
       - Type 3: Old ref code removed
    6. Resolve branch
    7. Build $account array (25+ fields)
    8. BEGIN TRANSACTION
    9. Update account → update_account()
    10. Delete old gifts → delete_gift_issued()
    11. Re-insert gifts with inventory tracking → insert_gift_issued() + update_gift_master()
    12. Re-insert prizes
    13. WALLET TRANSACTION CASES based on referral edit:
       - Case 2 (new ref): Insert employee/customer incentive credit
       - Case 1 (changed ref): Update wallet details
       - Case 3 (removed ref): Insert debit entry
    14. CHECK TRANS STATUS
        ✅ COMMIT → log, flash success
        ❌ ROLLBACK → flash error
```

## Flow 3: Delete Scheme Account

```
User → GET account/delete/:id
  → account_post('Delete', $id)
    1. BEGIN TRANSACTION
    2. Delete account → account_model->delete_account($id)
    3. Check status==1
       ✅ COMMIT → log, flash success
       ❌ ROLLBACK → flash error
```

## Flow 4: Close Scheme Account (Form Load)

```
User → GET account/close/scheme/:id
  → close_account_form('Close', $id)
    1. Get full close data → get_close_account($id)
    2. Get gift data → get_gift_issued($id)
    3. Handle voucher deduction (if pre-close) → get_voucher_mode_detail($id)
    4. Determine calculation type:
       - calculate_by = 1: DigiGold
       - calculate_by = 2: Maturity days formula
       - calculate_by = 0: Common calculation
    5. Determine is_weight (0=amount, 1=weight) based on scheme_type + flexible_sch_type
    6. Get metal rate → get_metalrate_by_branch()
    7. Calculate allow_benefit → calculateAllowBenefit()
    8. Check employee/agent ref deduction eligibility
    9. Check maturity date → closing_maturity_days logic
    10. Get gift values → get_giftValue(), get_assigned_gift_value()
    11. BENEFIT CALCULATION (if apply_benefit_by_chart=1):
        - DigiGold: getPaymentData()
        - Fixed Maturity: getBonusInsAmt() or getPaymentData()
        - Common: percent or amount based
    12. DEDUCTION CALCULATION (if apply_debit_on_preclose=1):
        - getAccBlcDebitSettings()
        - Calculate debit amount/percent
        - Gift deduction calculation
    13. One-time premium discount → getDiscountByjoin()
    14. GA bonus calculation → SQL query + getPaymentData()
    15. MCVA purchase discount → is_MCVA_purchaseDiscount_available()
    16. DigiGold benefit calculation (NPR)
    17. Compute final closing_balance / closing_amount
    18. Load view with all computed data
```

## Flow 5: Save Closed Account

```
User → POST account/close/update/:id
  → close_account_form('Save', $id)
    1. Read POST account[] data
    2. Get full account → get_close_account($id)
    3. Build close data array (closing_date, closing_balance, closing_weight, etc.)
    4. BEGIN TRANSACTION
    5. Update account as closed (is_closed=1, active=0) → update_account()
    6. Employee incentive on closing:
       - If emp_incentive_closing=1:
         - Get scheme close benefits → checkSchemeCloseBeiefits()
         - Calculate credit amount (by weight/installments)
         - Credit wallet → insertData('wallet_transaction')
         - Create wallet account if not exists
    7. Pre-close deductions:
       - Employee referral debit → getEmpBenefit() + insertData('wallet_transaction')
       - Customer intro scheme detection + debit
       - Agent referral debit → getAgentBenefit() + insert_agent_transaction()
    8. Webcam image upload → base64ToFile() + set_image()
    9. CHECK TRANS STATUS
       ✅ COMMIT → log, send close SMS/Email/WhatsApp, redirect
       ❌ ROLLBACK → flash error
```

## Flow 6: Revert Closed Account

```
User → GET account/revert/:id
  → close_account_form('Revert', $id)
    1. BEGIN TRANSACTION
    2. Check if DigiGold account exists for customer → isDigiAcc($id)
       - If exists: ROLLBACK, redirect with error
    3. Update account (is_closed=0, active=1) → update_account()
    4. Check trans status:
       - Get incentive details → get_ClosedBenefitsDetails($id)
       - If wallet transaction exists: Insert DEBIT entry to reverse incentive
    5. COMMIT → log, send revert SMS/Email, redirect
```

## Flow 7: OTP for Closing

```
User → AJAX POST account/close/otp/:mobile/:id_customer/:name
  → acc_close_otp()
    1. Validate mobile length
    2. Generate OTP (mt_rand 100001-999999)
    3. Store OTP in session
    4. Get SMS service #25 settings
    5. Build SMS message with template fields
    6. Send SMS → send_sms()
    7. Send WhatsApp (if enabled) → send_whatsApp_message()
    8. Send Email (if enabled) → send_email()
    9. Upsert OTP in DB → otp_update() or otp_insert()
    10. Return JSON with OTP (⚠️ SECURITY: OTP exposed!)
```

## Flow 8: Existing Scheme Registration Request

```
Admin → POST (approve/reject multiple requests)
  → update_request()
    1. Loop through reqdata[]
    2. Status 1 (Approve):
       - Update request status → updateRequest()
       - Insert new scheme_account → insert_account()
       - Send approval SMS, WhatsApp, push notification, email
    3. Status 2 (Reject):
       - Update request status
       - Send rejection SMS, WhatsApp, push notification, email
    4. Status 3 (Revert - approved to reject):
       - Check if payment exists → isPaymentExist()
       - If no payment: delete account → deleteAcc()
       - Send rejection notifications
```

## Flow 9: Rate Fixing

```
User → AJAX POST
  → submit_ratefix()
    1. Verify session OTP
    2. If integrationType == 0 (local):
       - Check if rate already fixed → isRateFixed()
       - Calculate metal weight = firstPayment_amt / rate
       - Update fixed_wgt, fixed_metal_rate → updFixedRate()
    3. If integrationType != 0 (ERP):
       - Get bearer token → getBearerToken()
       - Get gold rate → getGold22ct()
       - POST to ERP API /RateFixing
       - Parse response → update local DB
```

## Flow 10: Data Sync (Online/Offline)

```
Admin → POST account/update/client
  → update_client()
    1. Get customer reg data → getcustomerByStatus('N')
    2. Loop: For each modified customer:
       - Check client ID exists
       - Update closing data or account data
       - Send scheme acc number SMS (service #31)
    3. Get transaction data → getRegisteredAccTransactions('N')
    4. Loop: For each payment:
       - Online (type=1): Update receipt, ref number, status
       - Offline (type=2): Insert new payment or cancel existing
    5. Log sync → insert_sync()
```

## Flow 11: Gift Issued (Inventory)

```
User → AJAX POST
  → save_giftissued()
    1. Loop each gift in POST gift[]
    2. If ref_no empty: Auto-pick from inventory → SQL query
    3. Insert gift_issued (type=1, status=1)
    4. Insert inventory log → insertData('ret_other_inventory_purchase_items_log')
    5. Update inventory item status=1 → updateData('ret_other_inventory_purchase_items_details')
```

## Flow 12: Cancel Gift Issued

```
User → AJAX POST
  → cancel_giftissued()
    1. Get gift details → get_gift_issued_byID()
    2. Update gift_issued status=2 (deducted)
    3. Insert inventory log (status=0 = inward)
    4. Revert inventory item status=0
```

## Flow 13: Passbook Print

```
User → GET
  → passbook_print($page, $id_scheme_account, $id_payment)
    1. Get account detail → get_account_detail()
    2. Get closed account data → get_closed_account_by_id()
    3. Get payment details → get_ac_paid_details()
    4. Get company details → get_company_details()
    5. Switch by page:
       - 'F': Front page → passbook_front view → DOMPDF
       - 'B' (open): Back page → passbook_back view → DOMPDF
       - 'B' (closed): Close page → passbook_close view → DOMPDF
       - 'PAY': Specific payment → base64 decode IDs → mark for print
       - 'bond': Bond receipt → get metal rates, purity → bond_print view
    6. Render PDF via DOMPDF → stream to browser
```
