# Data Flow Chains — End-to-End Business Flows
> Last updated: 2026-03-16
> Source: 9 module brains

---

## Flow 1: Customer Scheme Journey (Happy Path)

```
Customer Registration
      │
      ▼
[Customer Module] → INSERT customer
      │
      ▼
Scheme Account Opening
      │
[Account Module: admin_manage.php] → INSERT scheme_account (active=1, is_closed=0)
→ SMS/WhatsApp notification
→ Passbook print
→ Optional: Gift issue at join
      │
      ▼
Monthly Installment Payments
      │
[Payment Module: admin_payment.php → SaveAll()]
  → GST calculation
  → Metal weight calculation
  → receipt_no generation (7 modes via chit_settings)
  → INSERT payment (payment_status=1)
  → INSERT payment_mode_details
  → wallet/referral/incentive logic
  → SMS/Email dispatch
  → Sync API (if integrationType=2)
      │
      ▼
Reports / Monitoring
      │
[chit_reports: admin_reports.php]
  → payment_datewise, scheme_daily_collection_details
  → closedaccount_list, payment_outstanding
  → scheme_summary (4 sub-queries per scheme)
      │
      ▼
Account Closing (Maturity / Pre-close)
      │
[Account Module: admin_manage.php → close_account_form()]
  → OTP verification
  → Benefit/deduction calculation (scheme_interest_chart)
  → UPDATE scheme_account (is_closed=1)
  → Gift issue (final)
  → SMS/Email notification
  → Passbook close print
```

---

## Flow 2: Online Payment (Gateway)

```
Customer App / Portal
      │  POST payment_amount, gateway, scheme_account_id
      ▼
[Payment Module: admin_payment.php]
  → Order ID generation (Razorpay/Cashfree/etc.)
  → INSERT payment (payment_status=7 Pending)
      │
      ▼ (async — gateway processes)
      │
Gateway Webhook / Return URL
      │
[Payment Module: verify_* method]
  → Signature verification
  → UPDATE payment (payment_status=1 or 3)
  → Receipt generation
  → SMS confirmation
  → Sync API
      │
      ▼
Reports
[chit_reports: get_online_payment_report, payment_datewise]
```

---

## Flow 3: Tag Lifecycle (Retail)

```
Lot Inward
      │
      ▼
[Lot Inward Module] → INSERT ret_lot_inwards
      │
      ▼
Tagging (Create Tag)
      │
[Tagging Module: admin_ret_tagging.php]
  → INSERT ret_taging (tag_status=0 Available)
  → INSERT ret_taging_stones
  → DEDUCT ret_lot_inward_detail balance
  → Optional: INSERT ret_branch_transfer (if cross-branch)
      │
      ▼
Estimation (Customer Enquiry)
      │
[Estimation Module: admin_ret_estimation.php]
  → RESERVE ret_taging (reserve_status=1)
  → INSERT ret_estimation + ret_estimation_items
  → Optional: Link to customer order (ret_order_details)
      │
      ▼
Billing (Convert Estimation to Bill)
      │
[Billing Module: admin_ret_billing.php + ret_billing_model]
  → READ ret_estimation + ret_estimation_items
  → UPDATE ret_taging.tag_status = 1 (Sold)    ⚠️ Written by Billing directly
  → UPDATE ret_estimation.estbillid             ⚠️ Partial commit risk
  → INSERT ret_billing, ret_billing_details
  → INSERT ret_journal (accounts)
  → Wallet, tax, voucher logic
      │
      ▼
Stock Reports
[chit_reports / separate Reports module]
  → Reads ret_taging stock
```

---

## Flow 4: KYC Approval (Cross-Module)

```
Customer submits KYC docs (Mobile App / Portal)
      │
      ▼
[chit_reports: kycapproval_data] — lists pending KYC
      │
Admin reviews + approves/rejects
      │
[chit_reports: update_kyc controller method]
  → UPDATE customer_kyc (approved/rejected)
  → UPDATE agent_kyc (if agent)
  → UPDATE customer.kyc_status
  → UPDATE agent.kyc_status
```

> Note: chit_reports directly writes to customer and KYC tables — bypasses any Customer module validation.

---

## Flow 5: Payment Cancellation (Cross-Module Write)

```
Admin clicks Cancel on payment report
      │
[chit_reports: cancel_payment]
  1. POST: id_payment, remarks
  2. payment_model::paymentDB() → verify payment exists
  3. payment_model::payment_cancel() → UPDATE payment SET payment_status=4
  4. payment_model::payment_statusDB() → INSERT payment_status_log
                                         ⚠️ NO transaction wrap — step 3 can succeed, step 4 fail
  5. (optional) syncapi_model::updPayStatusInTrans() if Khimji integration
  6. Write to admin/log/ flat file
```

---

## Flow 6: Scheme Settings Propagation (Config Change Impact)

```
Admin changes chit_settings
      │
[chit_settings: admin_settings.php]
  → UPDATE chit_settings (60+ columns)
  → update ../api/rate.txt (if metal rate changed)
  → OneSignal push notification (if applicable)
      │
      ▼ (IMMEDIATE effect — settings read fresh on every request)
      │
[ALL modules] → settingsDB() reads updated values
  - Payment: receipt mode, GST flag, wallet limits
  - Account: OTP required, lucky draw, closing flow
  - Scheme: limits, discounts
  - chit_reports: access control, display flags
```

---

## Flow 7: Scheme Account Edit (chit_reports writes Account data)

```
Admin opens Edit Account modal in Reports
      │
[chit_reports: editAccOrPayments → get_acc_byId]
  → Read scheme_account + customer data
      │
Admin submits changes
      │
[chit_reports: updateAccountDetails]
  → checkCommonSettings() (validates branch rules)
  → payment_model::updateDatacus() → UPDATE scheme_account
  → (optional) customer_model::update_customer_only() → UPDATE customer
  → Generates log file entry
      ⚠️ No sync API call
      ⚠️ No Account module validation
      ⚠️ No audit to account_model log_detail
```

---

## Flow 8: Maturity Report Generation

```
Admin opens Maturity Report
      │
[chit_reports: maturity_report_view]
      │
AJAX: maturity_report_data
      │
[payment_model::maturity_report_data() or similar]
  → Query scheme_account WHERE maturity_date BETWEEN :from AND :to
  → JOIN customer, scheme
  → JOIN payment (paid count vs total installments)
  → Return: customer name, mobile, scheme name, paid months, maturity date, closing amount
      │
[chit_reports view: DataTables render]
  → Export Excel option available
```

---

## Flow 9: Khimji ERP Integration (generateTransUniqId)

```
Admin triggers transaction ID generation
      │
[chit_reports: generateTransUniqId → generateTranUniqueIdManually]
  → Fetch payment + account + customer + scheme data from DB
  → POST to Khimji ERP API (integration_model::khimji_curl)
  → On success: UPDATE payment.unique_trans_id
  → On failure: log error
      ⚠️ External API dependency — SSL issues or downtime breaks this
      ⚠️ Non-idempotent if ERP has the record but response fails mid-way
```
