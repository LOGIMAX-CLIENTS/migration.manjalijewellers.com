# METHOD INDEX — chit_customer_app
> Round 1 — 2026-03-16 | Controller: 183 methods | mobileapi_model: 187 methods | payment_modal: 113 methods

---

## Part A: Controller Methods (mobile_api.php) — Alphabetical

| Method | Lines | Type | Tables Read | Tables Written | Notes |
|---|---|---|---|---|---|
| `__decrypt($str)` | L89 | Internal | — | — | base64_decode |
| `__encrypt($str)` | L82 | Internal | — | — | base64_encode (NOT hash) |
| `appContentLangWise_get` | L6893 | GET | `app_content`, `language` | — | Language-based content |
| `array_sort($array,$on,$order)` | L118 | Internal | — | — | Sort array by key |
| `authenticate_post` | L732 | POST | `customer`, `chit_settings` | `device_data` | Login |
| `bookVideoSAppt_post` | L6239 | POST | `vs_slots` | `vs_booking` | Video showroom booking |
| `bookVSAppt_post` | L4246 | POST | `vs_slots` | `vs_booking` | Video shop booking |
| `cashfreeResponse_post` | L7859 | POST | — | — | Delegates to old_cashfreeResponse |
| `cf_payment_status_post` | L5873 | POST | `payment` | `payment` | Check Cashfree payment status |
| `checkMobileReg_get` | L704 | GET | `customer` | — | Check mobile registered |
| `checkNotPaidAcc($id_customer)` | L112 | Internal | `scheme_account` | — | Used in join validation |
| `check_regOTP_post` | L886 | POST | `customer` | — | Verify registration OTP |
| `chit_detail_report_get($id_scheme_account)` | L6853 | GET | `payment`, `scheme_account`, `scheme`, `customer` | — | Payment history report |
| `commodityForDigi_get` | L7904 | GET | `digi_commodity` | — | Digi gold commodities |
| `company_get` | L1043 | GET | `company`, `chit_settings` | — | Company info |
| `createAccount_post` | L1086 | POST | `scheme`, `scheme_account`, `scheme_group`, `chit_settings`, `customer` | `scheme_account`, `scheme_reg_request`, `inter_wallet` | Join scheme |
| `createCustomer_post` | L921 | POST | `chit_settings`, `customer` | `customer`, `address`, `device_data`, `wallet_account` | Register customer |
| `createPin_post` | L6788 | POST | `customer` | `customer` | Create MPIN |
| `currency_get` | L1035 | GET | `chit_settings`, `company`, `metal_rates`, `branch_rate`, `configuration` | — | App startup config |
| `custComplaintStatus_get` | L3362 | GET | `cust_enquiry` | — | Complaint status |
| `custComplaints_get` | L3356 | GET | `cust_enquiry` | — | Customer complaints |
| `custDTHRequests_get` | L3369 | GET | `cust_enquiry` | — | DTH requests |
| `custDTHStatus_get` | L3375 | GET | `cust_enquiry` | — | DTH status |
| `CustomerOrderDetails_post` | L6357 | POST | `customer_order` | — | Retail order details |
| `customerIncentive($refdata,$id_scheme_account,$id_payment)` | L6405 | Internal | `scheme_account`, `payment` | `wallet_transaction` | Customer referral incentive |
| `customerSchemes_get` | L344 | GET | `scheme_account`, `scheme`, `payment`, `chit_settings` | — | Customer's active schemes |
| `delete_customer_post` | L6844 | POST | `customer` | `customer` | Soft-delete customer |
| `deleteScheme_get` | L1072 | GET ⚠️ | `scheme_account` | `scheme_account` | Delete via GET (CSRF risk) |
| `digidata_post` | L6768 | POST | — | `kyc` | Submit DigiLocker data |
| `fetchApptBookDetail_post` | L4239 | POST | `vs_booking` | — | Appointment detail |
| `fetchApptBookings_post` | L4232 | POST | `vs_booking` | — | Customer's bookings |
| `fetchAvailableSlots_get` | L4212 | GET | `vs_slots` | — | Available appointment slots |
| `generateCashfreeSession(...)` | L6904 | Internal | `payment_gateway` | `payment` | Cashfree order create |
| `generateCFtoken(...)` | L5736 | Internal | `payment_gateway` | — | Cashfree token helper |
| `generateInvoice_get($payment_no,$id_scheme_account)` | L7538 | GET | `payment`, `scheme_account`, `scheme`, `customer`, `address` | — | Invoice data |
| `generateOrderData(...)` | L5783 | Internal | `payment_gateway` | — | Easebuzz order create |
| `generateRazorOrderData(...)` | L5834 | Internal | `payment_gateway` | — | Razorpay order create |
| `generateVsOTP_get` | L4380 | GET | — | `customer` | VS OTP send |
| `getAllBranchDetail_get` | L3917 | GET | `branch` | — | All branches |
| `getAllLocations_get` | L335 | GET | `country`, `state`, `city` | — | All locations |
| `getActiveSchemes_get` | L192 | GET | `scheme`, `sch_classify`, `chit_settings`, `scheme_branch` | — | Active schemes |
| `getBearerToken` | L4036 | Internal | — | — | Parse Bearer token from header |
| `getCity_get` | L329 | GET | `city`, `company` | — | Cities for state |
| `getCountry_get` | L317 | GET | `country` | — | Countries |
| `getCurrencyForDirectPay_get` | ~L1050 | GET | `chit_settings`, `company`, `metal_rates` | — | Currency for direct pay |
| `getCustomer_get` | L383 | GET | `customer` | — | Hardcoded id_customer=15 ⚠️ |
| `getCustomer_post` | L376 | POST | `customer` | — | Customer profile |
| `get_customerWallets_get` | L1059 | GET | `wallet_account`, `wallet_transaction`, `chit_settings`, `scheme_account`, `scheme` | — | Wallets + transactions |
| `getDashboard_get` | L652 | GET | `customer`, `scheme_account`, `payment`, `scheme` | — | App home dashboard |
| `getGiftcardstatus_get` | L4184 | GET | `gift_card` | — | Gift card status |
| `getGiftedCards_post` | L4177 | POST | `gift_card` | — | Customer's gifted cards |
| `getLanguage_get` | L6874 | GET | `language` | — | Available languages |
| `getMatchingCity_get` | L671 | GET | `city` | — | City typeahead |
| `getMatchingCountry_get` | L659 | GET | `country` | — | Country typeahead |
| `getMatchingState_get` | L665 | GET | `state` | — | State typeahead |
| `getMatchingVillage_get` | L4205 | GET | `village` | — | Village typeahead |
| `getMetalrate_get` | L152 | GET | `api/rate.txt` | — | Metal rates from file |
| `getModules_get` | L3349 | GET | `configuration` | — | Enabled modules |
| `getMyGifts_post` | L4170 | POST | `gift_issued` | — | Customer's gifts |
| `getScheme_get` | L254 | GET | `scheme`, `scheme_group`, `chit_settings`, `customer`, `scheme_account`, `metal_rates`, `kyc`, `scheme_branch` | — | Scheme info + join check |
| `getSchemeDetail_get` | L369 | GET | `scheme_account`, `scheme`, `payment`, `customer`, `chit_settings`, `postdate_payment` | — | Account detail + payable calc |
| `getSchemes_get` | L178 | GET | `scheme`, `chit_settings` | — | All schemes |
| `get_groups_get` | L185 | GET | `scheme_group`, `scheme` | — | Scheme groups |
| `get_image_typ_from_base64($image_url)` | L3831 | Internal | — | — | Detect image mime type from base64 |
| `get_invoiceData($payment_no,$mobile)` | L715 | Internal | `payment`, `scheme_account`, `scheme`, `customer`, `address` | — | Invoice raw data |
| `get_settings` | L4064 | Internal | `chit_settings` | — | Settings accessor |
| `get_settings_get` | L7620 | GET | `chit_settings`, `configuration` | — | App settings |
| `getState_get` | L323 | GET | `state`, `company` | — | States for country |
| `getValidate_pin_post` | L6814 | POST | `customer` | — | Validate MPIN |
| `getValues()` | L95 | Internal | — | — | Read JSON from `php://input` |
| `getVillage_get` | L6838 | GET | `village` | — | All villages |
| `getWeights_get` | L159 | GET | `weight`, `api/rate.txt` | — | Weights + calculated rates |
| `giftIssuedByAcId_get` | L3342 | GET | `gift_issued` | — | Gift per account |
| `insertkyc_post` | L3469 | POST | `kyc`, `customer` | `kyc`, `customer` | Insert KYC (DigiLocker) |
| `insert_common_data($id_payment)` | L7671 | Internal | `payment`, `scheme_account`, `scheme`, `customer`, `chit_settings` | `payment`, `payment_referral`, `wallet_transaction`, `employee_incentive` | ⚠️ No transaction wrapper |
| `insert_referral_data(...)` | L6183 | Internal | `scheme_account`, `scheme` | `payment_referral`, `wallet_transaction` | Referral incentive insert |
| `insertEmployeeIncentive(...)` | L6371 | Internal | `employee` | `employee_incentive` | Employee ref incentive |
| `isNumberRegistered_get` | L678 | GET | `customer`, `chit_settings` | — | Mobile + email check |
| `join_existing_byacc(...)` | Internal | Internal | `scheme_reg_request` | `scheme_reg_request` | OTP-based existing join |
| `joinTime_weight_slabs_get` | L7613 | GET | `scheme` | — | Weight slabs |
| `kyc_exists($cus_id)` | L7910 | Internal | `kyc` | — | Check KYC exists |
| `mobile_payment_get` | L5495 | GET | `payment`, `scheme_account`, `scheme`, `customer`, `chit_settings` | `payment` | Older GET payment path |
| `mobile_payment_post` | L4484 | POST | `payment`, `scheme_account`, `scheme`, `customer`, `chit_settings`, `payment_gateway` | `payment` (create pending) | ⚠️ Main payment entry |
| `no_to_words($no)` | L7596 | Internal | — | — | Number to words EN |
| `no_to_words1($nos1)` | L7564 | Internal | — | — | Number to words variant |
| `old_cashfreeResponse_post` | L7187 | POST | `payment` | `payment` | Cashfree callback (old) |
| `old_insert_common_data($id_payment)` | L7639 | Internal | `payment` | `payment` | Deprecated post-payment |
| `oldeasebuzzResponse_post` | L5167 | POST | `payment` | `payment` | Easebuzz callback (old) |
| `oldrazorResponse_post` | L6437 | POST | `payment` | `payment` | Razorpay callback (old) |
| `old_updateCustomerByMobile_post` | L391 | POST | `customer` | `customer` | Old password change (dead path) |
| `otp_sms($otpStr)` | L100 | Internal | — | — | Format OTP message |
| `payDuesData_get` | L4400 | GET | `payment`, `scheme_account`, `scheme`, `chit_settings`, `metal_rates` | — | Direct pay dues widget |
| `paymentHistory_get` | L357 | GET | `payment`, `scheme_account`, `scheme`, `customer` | — | Full payment history |
| `payment_gateway($id_branch,$id_pg)` | L71 | Internal | `payment_gateway` | — | Gateway credentials |
| `pinsearchArea_post` | L6885 | POST | `pincode_area` | — | Pincode area lookup |
| `random_strings($length)` | L4479 | Internal | — | — | Random string generator |
| `rate_history_post` | L3381 | POST | `metal_rates` | — | Rate history |
| `razorResponse_post` | L7869 | POST | — | — | Delegates to oldrazorResponse |
| `readKyc_get` | L3879 | GET | `kyc`, `customer` | — | KYC status |
| `resetPassword_post` | L907 | POST | `customer` | `customer` | Reset password via OTP |
| `sch_enquiry_post` | L3934 | POST | `sch_enquiry` | `sch_enquiry` | Scheme enquiry |
| `sendFeedback_mail_post` | L446 | POST | `cust_enquiry` | `cust_enquiry` | Feedback + email |
| `sendFeedback_post` | L517 | POST | `cust_enquiry` | `cust_enquiry` | Feedback |
| `sendVendorEnquiry_post` | L588 | POST | — | — | Vendor enquiry email only |
| `sendtoDirectApi($api,$postData)` | L7874 | Internal | — | — | Forward to external API |
| `sync_existing_data(...)` | L3962 | Internal | `scheme_account`, `payment` | `scheme_account`, `payment` | Third-party sync |
| `terms_and_conditions_get` | L4190 | GET | `chit_settings` | — | T&C text |
| `test_get` | L7819 | GET | — | — | Dev test endpoint ⚠️ Active |
| `testCURL_get` | L3928 | GET | — | — | Dev test endpoint ⚠️ Active |
| `update_paymentMode($mode)` | L7836 | Internal | `payment` | `payment` | Update payment mode |
| `updateCustomer_post` | L432 | POST | `customer` | `customer` | Change password |
| `updateCustomerByMobile_post` | L405 | POST | `customer` | `customer` | Change password (with dedup check) |
| `updateProfile_post` | L601 | POST | `customer`, `address` | `customer`, `address` | Update profile |
| `updVSFeedback_post` | L4351 | POST | `vs_booking` | `vs_booking` | Update VS feedback |
| `uploadAadhar_post` | L3439 | POST | `customer` | `customer`, disk file | Upload Aadhaar |
| `verifyOTP_post` | L867 | POST | `customer` | — | Verify OTP |
| `verifyRateFixOTP_post` | L4070 | POST | `customer` | — | Verify rate fix OTP |
| `videoshopComplaints_get` / `status_get` | L7627 | GET | `vs_complaint` | — | VS complaints |
| `wallet_account_create(...)` | Internal | Internal | — | `wallet_account` | Create wallet on registration |
| `zoopapiCurl($api,$postData)` | L3847 | Internal | — | — | Zoopweb API curl |

---

## Part B: Key mobileapi_model Methods (Selected — Alphabetical)

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `branchesData()` | L565 | `branch` | — | Various |
| `checkService($serviceID)` | L600 | `services` | — | SMS/email service check |
| `chit_scheme_detail($id_scheme_account)` | ~L1350 | `scheme_account`, `scheme`, `payment` | — | `getSchemeDetail_get` |
| `clientEmail($email)` | ~L820 | `customer` | — | `isNumberRegistered_get` |
| `company_details()` | L30 | `company`, `chit_settings`, `country`, `state`, `city` | — | Constructor (every request) |
| `forgetUser($mobile)` | ~L870 | `customer` | — | `generateOTP_get` |
| `generateOTP()` | ~L890 | `customer` | `customer.last_generated_otp` | `generateOTP_get` |
| `genTicketNo()` | ~L1100 | `cust_enquiry` | — | `sendFeedback_post` |
| `get_activeSchemes($id_branch)` | L482 | `scheme`, `sch_classify`, `chit_settings`, `scheme_branch` | — | `getActiveSchemes_get` |
| `get_chit_settings()` | L175 | `chit_settings` | — | `get_currency` |
| `get_currency($id_branch)` | L77 | `chit_settings`, `company`, `metal_rates`, `branch_rate`, `configuration` | — | `authenticate_post`, `currency_get` |
| `get_customer_dashboard($id_customer)` | ~L1500 | `customer`, `scheme_account`, `payment` | — | `getDashboard_get` |
| `get_customerByMobile($mobile)` | L197 | `customer`, `chit_settings` | — | `authenticate_post`, `check_regOTP_post` |
| `get_customerByID($id_customer)` | L238 | `customer`, `address`, `kyc` | — | `createAccount_post` |
| `get_customerID($mobile)` | L182 | `customer` | — | Various |
| `get_customerProfile($id_customer)` | L215 | `customer`, `address`, `country`, `state`, `city`, `kyc` | — | `getCustomer_post` |
| `get_lastOTP($id_customer)` | L190 | `customer` | — | `verifyOTP_post` |
| `get_metalrate($id_branch, $is_branchwise_rate)` | ~L1700 | `metal_rates`, `branch_rate` | — | `get_schemeaccount_detail` |
| `get_payment_details($id_customer,...)`  | ~L950 | `scheme_account`, `scheme`, `payment`, `chit_settings` | — | `customerSchemes_get` |
| `get_paymenthistory($mobile, ...)` | ~L1200 | `payment`, `scheme_account`, `scheme`, `customer` | — | `paymentHistory_get` |
| `get_scheme($id_scheme, $id_customer)` | L496 | `scheme`, `chit_settings`, `customer`, `scheme_account`, `metal_rates`, `kyc`, `scheme_branch`, `scheme_group` | — | `getScheme_get`, `createAccount_post` |
| `get_schemeaccount_detail($id_scheme_account)` | L636 | `scheme_account`, `scheme`, `payment`, `customer`, `chit_settings`, `postdate_payment` | — | `getSchemeDetail_get` |
| `get_schemesAll()` | L460 | `scheme`, `chit_settings` | — | `getSchemes_get` |
| `get_settings()` | ~L2295 | `chit_settings` | — | `getSchemes_get` |
| `get_wallet_accounts($id_customer)` | L259 | `wallet_account`, `wallet_transaction`, `customer`, `chit_settings` | — | `get_customerWallets_get` |
| `get_wallet_transactions($id_customer, $id_wallet_trans)` | L285 | `wallet_account`, `wallet_transaction`, `customer`, `scheme_account`, `scheme`, `chit_settings` | — | `get_customerWallets_get` |
| `getBranchGatewayData($branch_id,$pg_id)` | L2468 | `payment_gateway` | — | `payment_gateway()` |
| `getMatchingCity($char,$id_state)` | L449 | `city` | — | `getMatchingCity_get` |
| `getMatchingCountry($char)` | L439 | `country` | — | `getMatchingCountry_get` — ⚠️ LIKE '$char%' = SQL injection |
| `getMatchingState($char,$id_country)` | L444 | `state` | — | `getMatchingState_get` |
| `insCusFeedback($data)` | ~L1070 | — | `cust_enquiry` | `sendFeedback_post` |
| `insert_customer($customer)` | ~L2200 | `customer` | `customer`, `address` | `createCustomer_post` |
| `insert_deviceData($deviceData)` | ~L2380 | — | `device_data` | `createCustomer_post`, `authenticate_post` |
| `isAddressExist($id_customer)` | ~L2360 | `address` | — | `updateProfile_post` |
| `isMobileExists($mobile)` | ~L800 | `customer` | — | `isNumberRegistered_get` |
| `notPaidAccounts($id_customer)` | ~L1150 | `scheme_account` | — | `checkNotPaidAcc` |
| `update_customer($data, $id_customer)` | ~L2060 | — | `customer` | Profile updates |
| `update_customerByMobile($data,$mobile)` | ~L2070 | — | `customer` | Password change |
| `update_customerAdd($data,$id_customer)` | ~L2100 | — | `address` | `updateProfile_post` |
| `updData($data,$id_field,$id_value,$table)` | L41 | — | `any table` | Generic update ⚠️ |
| `wallet_balance($id_customer)` | ~L1750 | `wallet_account`, `wallet_transaction` | — | `getScheme_get`, `customerSchemes_get` |

---

## Part C: Key payment_modal Methods (Selected — Alphabetical)

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `account_number_generator($id_scheme,$branch,$group_code)` | L1851 | `scheme_account` | — | Account creation |
| `calculate_maturityLapse_Date($id_scheme_account)` | L3079 | `scheme_account`, `scheme`, `payment` | — | Maturity date calc |
| `checkDueDate($payData)` | L2528 | `payment`, `scheme_account` | — | Payment validation |
| `checkReferalExist($id_payment,$id_sch_acc)` | L2745 | `payment_referral` | — | Referral dedup check |
| `get_agentBenefits($id_scheme,$amount,$ins)` | L2224 | `scheme` | — | Agent incentive calc |
| `get_branchWiseLogin()` | L2362 | `chit_settings` | — | Branch-wise filter |
| `get_company()` | L2183 | `company`, `chit_settings` | — | Company info |
| `get_current_month_payment($id_scheme_account)` | L2533 | `payment` | — | Check currentmonth paid |
| `get_customer($phone)` | ~L100 | `customer`, `chit_settings`, `branch` | — | `mobile_payment_post` |
| `get_due_date($due_type,$date_payment,$id_scheme_account)` | L2879 | `scheme_account`, `scheme`, `payment` | — | Due date calculation |
| `get_due_date_old(...)` | L2824 | `scheme_account`, `scheme`, `payment` | — | Deprecated due date |
| `get_financialYear()` | L2786 | `chit_settings` | — | `createAccount_post`, `mobile_payment_post` |
| `get_Incentivedata(...)` | L2671 | `payment`, `scheme`, `scheme_account`, `employee` | — | Incentive calculation |
| `get_metalrate_by_branch(...)` | L3146 | `metal_rates`, `branch_rate` | — | Rate for weight schemes |
| `get_payment_history($id_metal)` | L2647 | `payment` | — | Rate-linked payment history |
| `get_paymentgateway()` | L1785 | `payment_gateway` | — | Gateway list |
| `get_receipt_no($id_scheme,$branch)` | L2096 | `payment`, `chit_settings` | — | `insert_common_data` |
| `get_refdata($id_scheme_account)` | L2195 | `payment_referral`, `scheme_account`, `customer`, `employee` | — | Referral data |
| `get_scheme_details($id_sch_acc)` | L2809 | `scheme_account`, `scheme` | — | Various |
| `get_settings()` | L2295 | `chit_settings` | — | Various |
| `getPayIds($txnid)` | L2422 | `payment` | — | Find payments by txn ID |
| `getSchemeData($id_sch_acc)` | L3039 | `scheme_account`, `scheme` | — | Various |
| `getStatusByTxnId($ref_id)` | L2540 | `payment` | — | Gateway callback |
| `insertBatchData($data,$table)` | L3051 | — | `any table` | Batch insert ⚠️ |
| `paymentDB($id="")` | L1800 | `payment`, `scheme_account`, `scheme`, `customer`, `chit_settings` | `payment` | Core payment insert |
| `updPayModeBRefTranID($data,$ref_tran_id)` | L2416 | `payment` | `payment` | Update txn ID |
| `update_account($data,$id)` | L2075 | — | `scheme_account` | After payment |
| `update_cusreg($data,$id)` | L2547 | — | `scheme_reg_request` | Update reg request status |
| `update_receipt($id,$data)` | L2081 | — | `payment` | Update receipt after gen |
| `update_trans($data,$id)` | L2553 | — | `payment` | Update transaction ref |
| `updateAgentCash($id_agent,$new_point)` | L2234 | — | `agent` | Agent incentive update |
| `updateAtData($data,$id_field,$id_value,$table)` | L2997 | — | `any table` | Generic update ⚠️ |
| `updateGroupCode($payData)` | L2565 | `scheme_account` | `scheme_account` | Update group code after payment |
| `wallet_transactionDB($data)` | L2350 | — | `wallet_transaction` | Wallet debit on payment |

---

## Part D: Table → Methods Reverse Map

| Table | Read By (Key Methods) | Written By |
|---|---|---|
| `customer` | `authenticate_post`, `getCustomer_post`, `isNumberRegistered_get`, `getDashboard_get`, `createAccount_post`, `paymentHistory_get` | `createCustomer_post`, `updateProfile_post`, `resetPassword_post`, `createPin_post`, `delete_customer_post`, `uploadAadhar_post`, `insertkyc_post` |
| `scheme_account` | `customerSchemes_get`, `getSchemeDetail_get`, `payDuesData_get`, `paymentHistory_get`, `createAccount_post`, `mobile_payment_post` | `createAccount_post`, `deleteScheme_get`, `insert_common_data`, gateway callbacks |
| `payment` | `paymentHistory_get`, `getSchemeDetail_get`, `getDashboard_get`, `chit_detail_report_get`, `cf_payment_status_post` | `mobile_payment_post` (create pending), gateway callbacks (update status), `insert_common_data` (update receipt_no) |
| `scheme` | `getScheme_get`, `getActiveSchemes_get`, `customerSchemes_get`, `createAccount_post` | — |
| `chit_settings` | `currency_get`, `getScheme_get`, `getActiveSchemes_get`, `createCustomer_post`, `mobile_payment_post` | — |
| `device_data` | — | `createCustomer_post`, `authenticate_post` |
| `wallet_account` | `get_customerWallets_get`, `getScheme_get` | `createCustomer_post` (if wallet_account_type=1) |
| `wallet_transaction` | `get_customerWallets_get` | `customerIncentive`, `wallet_transactionDB` (mobile_payment) |
| `kyc` | `getCustomer_post`, `getScheme_get`, `readKyc_get` | `insertkyc_post`, `digidata_post` |
| `metal_rates` | `getMetalrate_get`, `getWeights_get`, `get_currency`, `getScheme_get` | — (written by rate module, read by API) |
| `scheme_reg_request` | `createAccount_post` (check existing) | `createAccount_post` (new request) |
| `payment_gateway` | `payment_gateway()` | — (managed by admin) |
| `cust_enquiry` | `custComplaints_get` | `sendFeedback_post`, `sendFeedback_mail_post` |
| `address` | `getCustomer_post`, `generateInvoice_get` | `updateProfile_post` |
