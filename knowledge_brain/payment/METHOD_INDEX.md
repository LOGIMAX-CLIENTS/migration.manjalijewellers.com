# Payment Module — Method Index
> **Updated**: 2026-03-24 | **Round**: 3

## 7a. Controller Methods — `admin_payment.php` (93 methods, alphabetical)

| Method | Lines | Purpose | Tables Read | Tables Written | JS Caller |
|---|---|---|---|---|---|
| `__construct` | L26-65 | Load models, session gate, status codes | `chit_settings` | — | — |
| `acmeCheck` | L6132-6165 | ACME payment check | `payment` | — | — |
| `ajax_account_detail` | L114-121 | Get account detail for payment form | `scheme_account`, `payment`, `scheme` | — | `payment.js:L1947` |
| `ajax_account_detail_qr` | L6102-6116 | QR-based account detail | `scheme_account`, `payment` | — | `payment.js:L5838` |
| `ajax_customer_schemes` | L128-136 | Get customer's active schemes + wallet | `scheme_account`, `scheme`, `branch` | — | `payment.js:L182,260,336,483` |
| `ajax_customer_schemes_amount` | L137-142 | Get customer amount-type schemes | `scheme_account`, `scheme` | — | — |
| `ajax_form_data` | L66-77 | Get form dropdowns (modes, banks, status) | `payment_mode`, `bank`, `payment_status_message`, `drawee_account` | — | `payment.js:L1722,3703` |
| `ajax_get_payment` | L2947-2953 | Get single payment by ID | `payment` | — | `payment.js:L3431` |
| `ajax_get_scheme` | L169-179 | Get payments by scheme IDs | `payment` | — | — |
| `ajax_load_account` | L3744-3751 | Load account data | `scheme_account` | — | `payment.js:L2038` |
| `ajax_online_payment` | L2954-2970 | Get online payment data with filters | `payment`, `scheme_account` | — | — |
| `ajax_onlinePayments` | L2833-2852 | List online payments (settled/unsettled) | `payment`, `gateway` | — | — |
| `ajax_payment_range` | L149-168 | Payment list with date range filter | `payment`, `scheme_account`, `customer`, `scheme` | — | `payment.js:L1028` |
| `ajax_payment_stat` | L143-148 | Total payment statistics | `payment` | — | — |
| `ajax_payment_status` | L122-127 | Get payment status list | `payment_status_message` | — | — |
| `ajax_postpayment_data` | L180-188 | Post-dated payment data | `postdate_payment` | — | — |
| `amount_to_weight` | L2480-2484 | Convert amount to metal weight | — | — | — |
| `At_verify_payment` | L5634-5707 | AT (AirtimeTech) payment verification | `payment` | `payment` | — |
| `cashFreeCurl` | L5319-5347 | Cashfree API curl call | — | — | — |
| `cashfreeSettlement` | L5348-5425 | Cashfree settlement processing | `gateway`, `payment` | `payment` | — |
| `customerIncentive` | L5775-5810 | Customer incentive calculation | `scheme`, `scheme_account` | `payment` | — |
| `deleteCusandPaydata` | L3341-3347 | Delete customer and payment data | — | `payment`, `customer` | — |
| `fetch_settled_payments` | L5202-5318 | Fetch settled payments from gateways | `payment`, `gateway` | `payment` | — |
| `free_payment_data` | L3752-3787 | Free payment data retrieval | `payment`, `scheme` | — | — |
| `generate_receipt_no` | L84-113 | Generate sequential receipt number | `payment`, `scheme_account` | — | — |
| `generateInvoice` | L2692-2768 | Generate PDF invoice (DomPDF) | `payment`, `scheme_account`, `customer`, `company` | — | — |
| `generateotp` | L5023-5069 | Generate OTP for payment | — | `otp` | `payment.js:L3289` |
| `generateTranUniqueId` | L5841-5904 | Generate unique transaction ID | `payment` | — | — |
| `genInstallmentNo` | L5708-5713 | Generate installment number | `payment` | — | — |
| `genKhimjiAcNoOrReceiptNo` | L5905-5999 | Khimji-specific account/receipt gen | `payment`, `scheme_account` | `scheme_account`, `payment` | — |
| `get_chq_num` | L6195-6200 | Get cheque number | `payment_mode_details` | — | `payment.js:L4465` |
| `get_EstimationDetails` | L5626-5633 | Get estimation details | `estimation` | — | `payment.js:L3803` |
| `get_ref_num` | L6166-6171 | Get reference number | `payment_mode_details` | — | `payment.js:L4420` |
| `get_scheme_cash_total` | L6186-6194 | Get scheme cash total | `payment`, `payment_mode_details` | — | — |
| `get_settings` | L78-83 | Get chit_settings | `chit_settings` | — | — |
| `get_status` | L6180-6185 | Get payment status | `payment` | — | `payment.js:L5994` |
| `getFormattedYear` | L6201-6211 | Format financial year string | `ret_financial_year` | — | — |
| `getMetalRateBydate` | L3199-3209 | Get metal rate for specific date | `metal_rates` | — | — |
| `gtway_settlement` | L5192-5201 | Gateway settlement view page | — | — | — |
| `hdfcApiCurl` | L5426-5440 | HDFC API curl call | — | — | — |
| `hdfcSettlement` | L5441-5524 | HDFC settlement processing | `gateway`, `payment` | `payment` | — |
| `httpPost` | L3044-3062 | Generic HTTP POST curl | — | — | — |
| `insert_common_data` | L3242-3274 | Insert common sync data | `payment`, `scheme_account` | `sync_*` tables | — |
| `insert_common_data_jil` | L3210-3241 | Insert JIL sync data | `payment`, `scheme_account` | `sync_*` tables | — |
| `insert_referral_data` | L3417-3484 | Insert referral tracking data | `scheme_account`, `scheme` | `referral_*` tables | — |
| `insert_trans_record` | L5579-5583 | Insert transaction record | — | `transaction` | — |
| `insertAgentIncentive` | L5714-5741 | Insert agent incentive | `agent`, `scheme` | `agent_incentive` | — |
| `insertEmployeeIncentive` | L5742-5774 | Insert employee incentive | `employee`, `scheme` | `employee_incentive` | — |
| `insertTransInPayment` | L3275-3340 | Insert transaction in payment flow | `payment` | `transaction` | — |
| `insertWalletTrans` | L3485-3628 | Insert wallet transaction | `wallet`, `customer` | `wallet_transaction` | — |
| `instrans_post` | L5584-5625 | Post installment transaction | `payment` | `transaction` | — |
| `manual_receiptnumber` | L3348-3416 | Manual receipt number generation | `payment` | — | — |
| `monthly_rate` | L3063-3068 | Get monthly rate data | `metal_rates` | — | — |
| `no_to_words` | L2769-2787 | Number to words conversion | — | — | — |
| `no_to_words1` | L2788-2819 | Alternative number to words | — | — | — |
| `old_passbook` | L5811-5826 | Old passbook view | `payment`, `scheme_account` | — | — |
| `online_payment_list` | L2820-2824 | Online payment list page | — | — | — |
| `payment` | L382-2407 | **MEGA method** — handles all payment cases via switch: general_advance, List, Edit_payment, Update_payment, View, Status, SaveAll, Save, Delete | `payment`, `scheme_account`, `customer`, `scheme`, `chit_settings`, many more | `payment`, `payment_mode_details`, `payment_status`, `general_advance_payment`, `general_advance_mode_detail`, `ret_advance_utilized`, `wallet_*` | Multiple |
| `payment_modes` | L5827-5840 | Get payment modes | `payment_mode_details` | — | `payment.js:L5746` |
| `PaymentGateway` | L4944-4949 | Payment gateway redirect | — | — | — |
| `payments_data` | L5137-5143 | Payments data page view | — | — | — |
| `payments_data_list` | L5144-5191 | Payments data list AJAX | `payment` | — | `payment.js:L3518` |
| `postdate_payment` | L2485-2691 | PDC CRUD switch (View/Save/List/Ajax/Delete) | `postdate_payment`, `scheme_account` | `postdate_payment` | — |
| `postdate_payment_form` | L189-381 | PDC form actions (Edit/Update/Status) | `postdate_payment`, `payment` | `postdate_payment`, `payment`, `payment_status` | — |
| `resend_otp` | L5092-5136 | Resend OTP | — | `otp` | `payment.js:L159,460,590` |
| `retryAccOrReceiptGen` | L6000-6095 | Retry account/receipt generation | `payment`, `scheme_account` | `scheme_account`, `payment` | — |
| `revertApproval` | L3686-3743 | Revert payment approval | `payment` | `payment`, `scheme_account` | — |
| `revertApproval_jil` | L3647-3685 | Revert JIL approval | `payment` | `payment`, `sync_*` | `payment.js:L1010` |
| `send_notification` | L2408-2443 | Send payment notification | — | — | — |
| `send_singlealert_notification` | L2444-2479 | Send single OneSignal alert | — | — | — |
| `send_sms` | L3157-3171 | Send SMS for payment | — | — | — |
| `send_sms_wallet` | L3629-3646 | Send wallet SMS | — | — | — |
| `sendSMSMail` | L3172-3198 | Send SMS + Email combined | — | — | — |
| `split_payment` | L2971-3043 | Split payment into sub-payments | `payment` | `payment` | — |
| `thermal_invoice` | L4950-5022 | Generate thermal receipt | `payment`, `company` | — | — |
| `update_dueYear` | L6096-6101 | Update due year | `payment` | `payment` | — |
| `update_otp` | L5070-5091 | Update/verify OTP | `otp` | `otp` | `payment.js:L3310` |
| `update_pay_status` | L2853-2946 | Update online payment status (approve/reject) | `payment`, `scheme_account` | `payment`, `scheme_account`, `payment_status` | — |
| `update_paymentMode` | L6212-6237 | Update payment mode | `payment` | `payment`, `payment_mode_details` | — |
| `update_remark` | L6172-6179 | Update payment remark | — | `payment` | `payment.js:L6046` |
| `update_settlement` | L3069-3118 | Update settlement | `settlement` | `settlement`, `settlement_detail` | — |
| `updateGtwaySettlement` | L5525-5578 | Update gateway settlement status | `payment` | `payment` | — |
| `updateReceipt` | L6117-6131 | Update receipt number | — | `payment` | — |
| `verify_cashfreepayment` | L3846-4172 | Verify Cashfree payment | `payment`, `gateway` | `payment`, `scheme_account` | — |
| `verify_easebuzzpayment` | L4346-4632 | Verify EaseBuzz payment | `payment`, `gateway` | `payment`, `scheme_account` | — |
| `verify_hdfcpayment` | L4710-4819 | Verify HDFC payment | `payment`, `gateway` | `payment` | — |
| `verify_payment` | L3788-3845 | Verify payment (router to specific gateway) | `payment` | — | — |
| `verify_payment_view` | L2825-2832 | Verify payment page | — | — | — |
| `verify_PayUpayments` | L4633-4709 | Verify PayU payment | `payment`, `gateway` | `payment`, `scheme_account` | — |
| `verifyRazorPayments` | L4173-4345 | Verify Razorpay payment | `payment`, `gateway` | `payment`, `scheme_account` | — |
| `verifyWithTechProcess` | L4820-4943 | Verify TechProcess payment | `payment`, `gateway` | `payment` | — |
| `weight_settlement` | L3119-3138 | Weight settlement CRUD | `settlement` | `settlement` | — |
| `weight_settlement_detail` | L3139-3156 | Weight settlement detail | `settlement_detail` | — | — |

## 7b. Model Methods — `payment_model.php` (278 methods, key groups)

> Full alphabetical listing of all 278 methods is too large for a single view. Below are the most critical methods grouped by function.

### Payment CRUD
| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `paymentDB` | L718-826 | `payment`, `scheme_account`, `scheme`, `chit_settings`, `drawee_account`, `payment_mode`, `payment_status_message`, `customer`, `inter_wallet_account` | `payment` | Controller: `payment`, `update_pay_status`, `verify_*` |
| `payment_statusDB` | L847-869 | `payment_status` | `payment_status` | Controller: `postdate_payment_form`, `payment` |
| `postdated_paymentDB` | L1164-1227 | `postdate_payment`, `scheme_account`, `payment_status_message`, `drawee_account`, `bank`, `chit_settings` | `postdate_payment` | Controller: `postdate_payment`, `postdate_payment_form` |
| `insertData` | — | — | `{any}` | Controller: multiple (generic INSERT) |
| `insertBatchData` | — | — | `{any}` | Controller: multiple (batch INSERT) |
| `updateData` | — | — | `{any}` | Controller: multiple (generic UPDATE) |
| `updData` | — | — | `{any}` | Controller: multiple (generic UPDATE) |

### Payment Lists & Reports
| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `payment_list` | L210-310 | `payment`, `scheme_account`, `customer`, `scheme`, `employee`, `branch`, `payment_mode`, `payment_status_message`, `chit_settings`, `agent` | — | Controller: `ajax_payment_range` |
| `payment_list_range` | L311-484 | Same as above + date filtering | — | Controller: `ajax_payment_range` |
| `payment_online_range` | L485-544 | `payment`, `scheme_account`, `customer`, `scheme`, `employee`, `gateway`, `branch`, `payment_status_message` | — | Controller: `ajax_online_payment` |
| `onlinePayments` | L657-708 | Same as above | — | Controller: `ajax_onlinePayments` |
| `onlinePayments_range` | L597-656 | Same as above + settlement filter | — | Controller: `ajax_onlinePayments` |

### Receipt Number Generation
| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `get_receipt_no` | L31-123 | `payment`, `scheme_account`, `customer`, `scheme`, `chit_settings`, `ret_financial_year` | — | Controller: `generate_receipt_no` |
| `get_receipt_no_settings` | L124-128 | `chit_settings` | — | — |
| `get_rptnosettings` | — | `chit_settings` | — | Controller: `payment` |

### Account & Customer Lookup
| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `get_paymentContent` | L1386-1599+ | `scheme_account`, `scheme`, `payment`, `customer`, `branch`, `scheme_group`, `chit_settings`, `postdate_payment`, `metal_rates`, `payment_mode_details` | — | Controller: `ajax_account_detail` |
| `get_account_detail` | L1328-1385 | `scheme_account`, `scheme`, `payment`, `customer`, `postdate_payment` | — | — |
| `get_customer_schemes` | L1262-1319 | `scheme_account`, `scheme`, `branch`, `chit_settings`, `employee` | — | Controller: `ajax_customer_schemes` |
| `wallet_balance` | — | `wallet`, `chit_settings` | — | Controller: `payment`, `ajax_customer_schemes` |

## 7c. JS → Controller AJAX Map (54+ AJAX calls in payment.js)

| JS Line | AJAX URL | Controller Method |
|---|---|---|
| L104 | `/admin_customer/ajax_get_scheme_account_list` | ⚡ Cross-module |
| L150 | `/customer/get_customer/{id}` | ⚡ Cross-module |
| L159 | `/admin_payment/resend_otp` | `resend_otp` |
| L182 | `/payment/get/ajax/customer/account/{id}` | `ajax_customer_schemes` |
| L998 | `/admin_ret_billing/get_bank_acc_details` | ⚡ Cross-module (Billing) |
| L1010 | `/admin_payment/revertApproval_jil` | `revertApproval_jil` |
| L1028 | `/payment/ajax_list` | `payment/Ajax` |
| L1595 | `/receipt_number/update` | ⚡ Cross-module |
| L1622 | `/customer/get_customers` | ⚡ Cross-module |
| L1722 | `/payment/get/ajax_data` | `ajax_form_data` |
| L1858 | `/settings/drawee/ajax_list/{id}` | ⚡ Cross-module (Settings) |
| L1900 | `{baseURL}/api/rate.txt` | ⚡ External API |
| L1915 | `/settings/weight_list` | ⚡ Cross-module (Settings) |
| L1947 | `/payment/get/ajax/account/{id}` | `ajax_account_detail` |
| L2038 | `/admin_payment/ajax_load_account` | `ajax_load_account` |
| L3289 | `/admin_payment/generateotp` | `generateotp` |
| L3310 | `/admin_payment/update_otp` | `update_otp` |
| L3346 | `{form_save_url}` | Dynamic — `payment/save_all` or `payment/save` |
| L3400 | `/payment/update/{id}` | `payment/Update` |
| L3431 | `/online/get/ajax_payment/{id}` | `ajax_get_payment` |
| L3472 | `/admin_scheme/ajax_get_schemes` | ⚡ Cross-module (Scheme) |
| L3518 | `/payment/payments_data_list` | `payments_data_list` |
| L3650 | `/admin_employee/get_employee` | ⚡ Cross-module (Employee) |
| L3803 | `/admin_payment/get_EstimationDetails/ajax` | `get_EstimationDetails` |
| L3956 | `/admin_ret_billing/get_payment_device_details` | ⚡ Cross-module (Billing) |
| L3966 | `/admin_ret_billing/get_bank_acc_details` | ⚡ Cross-module (Billing) |
| L4420 | `/admin_payment/get_ref_num/` | `get_ref_num` |
| L4465 | `/admin_payment/get_chq_num/` | `get_chq_num` |
| L4808 | `/admin_ret_billing/get_advance_details/` | ⚡ Cross-module (Billing) |
| L5096 | `/branch/branchname_list` | ⚡ Cross-module (Branch) |
| L5150 | `/payment/edit_payment/{id}` | `payment/Edit_payment` |
| L5724 | `/payment/update_payment/{id}` | `payment/Update_payment` |
| L5746 | `/admin_payment/payment_modes` | `payment_modes` |
| L5838 | `/admin_payment/ajax_account_detail_qr` | `ajax_account_detail_qr` |
| L5994 | `/admin_payment/get_status` | `get_status` |
| L6046 | `/admin_payment/update_remark` | `update_remark` |
| L6069 | `/admin_customer/ajax_get_customers_list` | ⚡ Cross-module (Customer) |
| L6123 | `/admin_ret_billing/bill_payment_details/` | ⚡ Cross-module (Billing) |

### Cross-Module AJAX Summary
- **Billing (admin_ret_billing)**: 4 calls — bank details, device details, advance details, bill payment
- **Customer**: 3 calls — customer list, get customer, scheme accounts
- **Settings**: 2 calls — drawee list, weight list
- **Scheme**: 1 call — scheme list
- **Employee**: 1 call — employee list
- **Branch**: 1 call — branch list
- **External**: 1 call — rate.txt API

## 7d. Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `payment` | `paymentDB`, `payment_list`, `payment_list_range`, `payment_online_range`, `onlinePayments`, `get_paymentContent`, `get_account_detail`, `get_receipt_no`, ~50+ more | `paymentDB(insert/update/delete)`, `update_pay_status`, `verify_*`, `split_payment` |
| `scheme_account` | `payment_list`, `get_paymentContent`, `get_customer_schemes`, `get_account_detail`, ~30+ more | `update_pay_status` (paid_ins), `retryAccOrReceiptGen` |
| `postdate_payment` | `postdated_paymentDB`, `post_paymentlist`, `pdc_detail_all`, `get_postpayment` | `postdated_paymentDB(insert/update/delete)` |
| `payment_status` | `payment_statusDB`, `payment_log`, `post_payment_log` | `payment_statusDB(insert)` |
| `payment_mode_details` | `getPaymentModeDetailsDataByID`, `getAllsubPayments`, `get_chq_num`, `get_ref_num` | `insertData`, `insertBatchData`, `update_modestatus_data` |
| `general_advance_payment` | `general_advance_list` | `paymentDB(general_advance_insert)` |
| `general_advance_mode_detail` | — | `insertData`, `insertBatchData` |
| `chit_settings` | `get_settings`, `checkSettings`, `allow_wallet`, `wallet`, `get_rptnosettings`, ~20+ more | — |
| `ret_advance_utilized` | — | `insertData`, `deleteUtilized` |
| `gateway` | `getGatewayData`, `getBranchGatewayData` | `updateGatewayResponse` |
