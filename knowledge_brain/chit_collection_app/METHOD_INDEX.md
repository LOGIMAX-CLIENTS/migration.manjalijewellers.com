# chit_collection_app — Method Index

> **🔄 UPGRADED BRAIN**
> Previous version: R4 (2026-03-17)
> Upgraded to: R5-Upgrade (2026-03-25)
> Upgrade date: 2026-03-25
> Changes: 20 previously undocumented paymt.php methods added, ALL line numbers resolved from `~L` to exact values

---

## 7a. adminapp_api.php — Controller Methods (Alphabetical, 73 methods)

| Method | Lines | Tables Read | Tables Written | Notes |
|---|---|---|---|---|
| `__construct` | L24-50 | — | — | Loads 8 models, sets constants, payment_status map |
| `__encrypt` | L97-100 | — | — | BASE64 encode (NOT encryption!) |
| `_checkArgumentValidation` | L2204-2214 | — | — | PayU helper |
| `_curlCall` | L2132-2166 | — | — | PayU helper — BUG-022 display_errors=1 |
| `_email_validation` | L2215-2224 | — | — | PayU helper |
| `_emptyValidation` | L2238-2272 | — | — | PayU helper |
| `_getHashKey` | L2167-2183 | — | — | PayU helper |
| `_getURL` | L2184-2203 | — | — | PayU helper |
| `_pay` | L2110-2131 | — | — | PayU helper |
| `_payment` | L2070-2109 | — | — | PayU primary entry for PayU |
| `_removeSpaceAndPreparePostArray` | L2225-2237 | — | — | PayU helper |
| `_typeValidation` | L2273-2298 | — | — | PayU helper |
| `adminapp` | L2053-2069 | payment | payment | Admin app gateway (PayU) redirect |
| `adminAppSuccess` | L1768-2052 | payment, scheme_account, chit_settings | payment, scheme_account, wallet_transaction | Admin app payment success handler — BUG-007 |
| `agentWise_monthly_reports_post` | L4406-4422 | payment, scheme_account, agent | — | Agent report (thin wrapper) |
| `amount_to_weight` | L3129-3134 | metal_rates | — | Utility: amount÷rate |
| `array_sort` | L66-95 | — | — | Utility: generic array sort |
| `authenticate_post` | L139-183 | employee, agent, employee_devices, branch | employee_devices | Login + device check |
| `branchesByEmp_post` | L4467-4480 | branch, customer | — | Filter branches for employee |
| `cf_payment_status_post` | L2786-3047 | payment | payment | Cashfree status update — BUG-012, BUG-013 |
| `check_regOTP_post` | L1318-1339 | — | — | OTP validation (client-side compare) |
| `checkMobileReg_get` | L1224-1235 | customer | — | Mobile registration check |
| `createAccount_post` | L406-710 | scheme_account, scheme, payment, gifts, gift_card | scheme_account, payment, gift_card | Scheme joining (new/existing) |
| `createCustomer_post` | L186-402 | customer, address | customer, address | Customer registration + image upload — BUG-018 |
| `currency_get` | L125-137 | chit_settings, branch | — | Branch currency via mobileapi_model |
| `customer_ledger_details_post` | L4377-4405 | payment, scheme_account | — | Ledger detail pass-through |
| `customer_ledger_post` | L4354-4376 | payment, scheme_account | — | Ledger summary — BUG-015 date params unused |
| `customerIncentive` | L3350-3407 | — | customer_incentive | Customer referral benefit — BUG-029 session in REST |
| `customerSchemes_post` | L774-889 | customer, scheme_account, payment | — | Search customer & get scheme accounts due list |
| `easebuzzResponse_post` | L2299-2497 | payment | payment | Easebuzz callback — BUG-011 integrationType reversed |
| `generate_receipt_no` | L3135-3169 | payment_mode, chit_settings | — | Short-code based receipt gen |
| `generateAcNoOrReceiptNo` | L4017-4095 | scheme_account, payment | scheme_account, payment | Post-payment acc/receipt gen (Khimji) |
| `generateCFtoken` | L2738-2785 | — | — | Cashfree v-old API token gen |
| `generateOTP_get` | L1237-1316 | customer | — | OTP generation — BUG-005 static 123456 |
| `generateTranUniqueId` | L4104-4170 | payment | — | Unique transaction ID (integrationType=5) — BUG-030 |
| `get_all_branch_get` | L1143-1148 | branch | — | All branches |
| `get_branch_get` | L1090-1096 | branch, scheme_branch | — | Branch list by profile |
| `get_values` | L59-63 | — | — | JSON POST body reader |
| `getallAcitiveschemes_get` | L3408-3415 | scheme | — | Active scheme list |
| `getAllPaymentGateways_get` | L3170-3184 | payment_gateway | — | Gateway list (card brands only) |
| `getClassificationAll_get` | L3122-3128 | metal_classification | — | Metal classification pass-through |
| `getCusByMobile_post` | L890-916 | customer | — | Customer by mobile (lightweight) |
| `getDataFromOffline` | L4171-4303 | — | customer, scheme_account | Khimji offline sync pull — BUG-003 param swap |
| `getPaymentData` | L1042-1085 | payment, company, scheme_account, scheme | — | Thermal print string builder |
| `getScheme_get` | L917-1005 | scheme, chit_settings, scheme_group, scheme_account | — | Scheme details + join eligibility |
| `getVersion_get` | L106-123 | company, chit_settings | — | App version + maintenance |
| `insert_referral_data` | L3048-3114 | scheme_account, agent, employee | wallet_transaction, referral_data | Referral incentive insert |
| `insertAgentIncentive` | L3256-3287 | — | agent_incentive | Agent commission |
| `insertEmployeeIncentive` | L3288-3349 | — | employee_incentive | Employee wallet credit |
| `insertOfflineCustomer` | L3901-4016 | customer, address | customer, address | Sync offline customer (simple insert) |
| `insertOfflinepayments` | L3622-3900 | payment, scheme_account | payment, scheme_account | Sync offline payment — BUG-001 also here |
| `IsNullOrEmptyString` | L55-57 | — | — | Utility: null/empty check |
| `isNumberRegistered` | L1100-1141 | customer, employee, agent | — | Mobile+email check (internal) |
| `isNumberRegistered_get` | L1341-1362 | customer, employee, agent | — | Mobile+email check (REST) |
| `manualTUIDgen_get` | L4096-4103 | — | payment | Manual TUID generation |
| `mobile_payment_get` | L2503-2737 | — | — | Mobile payment GET (gateway redirect page) |
| `mobile_payment_post` | L1381-1767 | payment, scheme_account, customer, scheme, chit_settings, metal_rates | payment | **Core collection payment** — BUG-001 |
| `no_to_words` | L3185-3209 | — | — | Amount to words (receipt print) |
| `no_to_words1` | L3210-3255 | — | — | Amount to words variant |
| `otp_sms` | L1364-1376 | company | — | OTP SMS message builder |
| `pay_collection_post` | L1166-1222 | payment, scheme_account, employee, agent | — | Collection report |
| `payment_gateway` | L3115-3121 | payment_gateway | — | Gateway credentials by branch+id |
| `paymentHistory_get` | L1007-1040 | payment, scheme_account, scheme, customer, branch | — | Employee collection history |
| `pendingMsg_post` | L4423-4448 | payment, customer | — | Push pending notification |
| `random_strings` | L2498-2502 | — | — | Random string gen |
| `sch_enquiry_post` | L4481-4510 | scheme_enquiry | scheme_enquiry | Scheme enquiry — BUG-023 hardcoded CC email |
| `sendCusDataToOffline` | L4304-4353 | customer | — | Push customer to Khimji — BUG-016 |
| `storeCollectionDevices_post` | L4511-4528 | employee_devices | employee_devices | Device registration |
| `syncAgentCustomers_post` | L3416-3447 | customer, agent | customer | Agent customer sync |
| `syncOfflineAccPayments_post` | L3501-3621 | payment | payment | Offline payment batch sync — BUG-009 hardcoded URL |
| `syncOfflineCustomers_post` | L3448-3500 | customer | customer | Offline customer batch sync — BUG-008 $data shadow |
| `terms_and_conditions_get` | L4449-4466 | chit_settings | — | T&C text |
| `wallet_account_create` | L712-752 | services_modal | wallet_account | Wallet account creation |

---

## 7b. adminappapi_model.php — Model Methods (Alphabetical, 55 methods)

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `__construct` | L7-14 | — | — | CI autoload |
| `__encrypt` | L2222-2227 | — | — | isValidLogin |
| `clientEmail` | L1375-1380 | customer | — | isNumberRegistered |
| `company_details` | L181-191 | company, chit_settings, country, state, city | — | authenticate_post, createCustomer_post |
| `cusByMobileBranchWise` | L1432-1450 | customer | — | getCusByMobile_post |
| `customer_reports` | L2105-2126 | payment, scheme_account, customer | — | report endpoints — BUG-038,039,040 |
| `get_activeSchemes` | L2004-2017 | scheme | — | syncOffline* |
| `get_all_branch` | L1632-1672 | branch | — | get_all_branch_get |
| `get_branch` | L1589-1631 | branch, scheme_branch | — | get_branch_get |
| `get_branchesByEmp` | L2156-2191 | branch, employee | — | branchesByEmp_post |
| `get_chit_settings` | L215-222 | chit_settings | — | admin screens |
| `get_company` | L1962-1974 | company | — | getPaymentData |
| `get_costcenter` | L1890-1896 | chit_settings | — | get_branch |
| `get_currency` | L1897-1961 | chit_settings, branch | — | currency_get |
| `get_customer_byAcc` | L1975-1984 | scheme_account, customer, scheme | — | customerSchemes_post |
| `get_customerByAgent` | L1985-2003 | customer, agent | — | syncAgentCustomers_post |
| `get_customerByID` | L2192-2212 | customer | — | createAccount_post |
| `get_customerByMobile` | L1401-1429 | customer | — | customerSchemes_post |
| `get_customerProfile` | L2276-2297 | customer, address | — | Internal |
| `get_employee_details` | L1821-1835 | employee | — | createAccount_post |
| `get_entry_records` | L1836-1870 | payment, scheme_account, scheme, employee, branch | — | getPaymentData |
| `get_metalData` | L2242-2254 | metal_rates, metal_classification | — | pay_collection_post |
| `get_metalrate` | L422-438 | metal_rates, branch_rate | — | Internal — BUG-028 file_get_contents |
| `get_metalrate_by_branch` | L2298-2349 | metal_rates, branch_rate, metal_purity | — | get_payment_details |
| `get_payment_collection` | L1673-1820 | payment, scheme_account, customer, scheme, employee, agent, branch | — | pay_collection_post |
| `get_payment_details` | L440-1299 | scheme_account, scheme, payment, customer, branch, chit_settings, postdate_payment, scheme_group | scheme_account | customerSchemes_post via mobileapi_model — BUG-025,026,027,028 |
| `get_paymenthistory` | L1479-1579 | payment, scheme_account, scheme, customer, branch, chit_settings, village, payment_mode | — | paymentHistory_get |
| `get_settings` | L2350-2358 | chit_settings | — | Internal |
| `get_terms_and_conditions` | L2149-2155 | chit_settings | — | terms_and_conditions_get |
| `get_values` | L15-21 | — | — | JSON POST body reader |
| `getActivecardBrands` | L2213-2221 | card_brands | — | Internal |
| `getallAcitiveschemes` | L193-214 | scheme, chit_settings, scheme_branch | — | getallAcitiveschemes_get |
| `getBranchGateways` | L1871-1889 | payment_gateway, branch_gateway | — | Internal |
| `getCustomerByMobile` | L2034-2040 | customer | — | syncOffline |
| `getCustomerSchAcc` | L2127-2139 | scheme_account | — | sync |
| `getInsNo` | L1581-1587 | payment, scheme_account | — | get_paymenthistory |
| `getLastTransaction` | L1300-1306 | payment | — | get_payment_details |
| `getPayGenData` | L1465-1477 | payment, scheme_account, scheme, branch, chit_settings | — | generateAcNoOrReceiptNo |
| `getWalletPaymentContent` | L1452-1463 | payment, chit_settings, scheme_account, customer, inter_wallet_account | — | adminAppSuccess |
| `insChitwallet` | L252-420 | inter_wallet_trans, inter_wallet_trans_detail, inter_wallet_trans_tmp* | wallet_transaction, inter_wallet_account | wallet_account_create |
| `insert_collectionDevices` | L2228-2241 | employee_devices | employee_devices | isValidLogin |
| `insert_customer` | L223-244 | customer, address | customer, address | createCustomer_post |
| `insert_sch_enquiry` | L2264-2275 | scheme_enquiry | scheme_enquiry | sch_enquiry_post |
| `insertRemarks` | L2140-2148 | remarks | remarks | pendingMsg_post |
| `isMobileExists` | L1349-1374 | employee, agent | — | isNumberRegistered |
| `isOfflineMobile` | L2018-2033 | customer | — | syncOffline |
| `isPaymentExist` | L1309-1326 | payment, scheme_account, customer | — | get_payment_details |
| `isPendingStatExist` | L1328-1346 | payment, scheme_account, customer | — | get_payment_details |
| `isValidLogin` | L22-138 | employee, agent, branch, employee_devices | employee_devices | authenticate_post — BUG-010 SQL injection |
| `monthly_agent_reports` | L2041-2104 | payment, agent, scheme_account | — | agentWise_monthly_reports_post — BUG-034,035,036,037 |
| `old_get_customerByMobile` | L1381-1399 | customer | — | (deprecated) |
| `send_sms` | L140-167 | sms_api_settings | sms_api_settings (debit) | Multiple post actions |
| `update_otp` | L168-179 | sms_api_settings | sms_api_settings | send_sms |
| `updData` | L2255-2263 | {any} | {any} | createCustomer_post (doc update) |
| `wallet_accno_generator` | L245-251 | chit_settings | — | createCustomer_post |

---

## 7c. paymt.php — Controller Methods (Alphabetical, 69 methods)

> **Note:** Lines 11-40 contain 6 **global wrapper functions** defined outside the class. These are CI router workarounds for PayU callbacks. See line numbers below.

| Method | Lines | Tables R/W | Notes |
|---|---|---|---|
| `__construct` | L46-87 | login_model, payment_modal, scheme_modal, etc. | Session gate, maintenance mode check |
| `adminapp` | L2859-2862 | payment | Admin app gateway (PayU) — echo redirect |
| `adminAppSuccess` | L2595-2858 | payment, scheme_account | Admin app PayU success — BUG-002 serviceID=7 |
| `ajax_giftdetails` | L4286-4291 | gift_card | AJAX gift card details for web customer |
| `amount_to_weight` | L1646-1650 | — | Utility: amount÷rate |
| `array_sort` | L124-153 | — | Utility: generic array sort |
| `atomReturnURL` | L953-1049 | payment, scheme_account | Atom gateway response handler |
| `cancelMURL` | L3189-3248 | payment | Mobile PayU cancel |
| `cashfreemobile` | L4351-4392 | payment | Cashfree mobile return — BUG-033 $paymentgateway undefined |
| `cashfreeresponseURL` | L4294-4350 | payment | Cashfree web return URL |
| `chit_detail_report` | L4682-4704 | payment, scheme_account | Scheme detail report (DOMPDF) |
| `customerIncentive` | L4599-4634 | — | Customer referral benefit — BUG-029 session issue |
| `failureMURL` | L3114-3188 | payment | Mobile PayU failure |
| `generateAcNoOrReceiptNo` | L4471-4535 | scheme_account, payment | Post-payment acc/receipt gen |
| `generateInvoice` | L2012-2044 | payment, scheme_account | PDF invoice generation (DOMPDF) |
| `generateTranUniqueId` | L4408-4470 | payment | Khimji integration |
| `get_entrydate` | L1651-1662 | ret_day_closing, chit_settings | Custom entry date — ⚠️ raw SQL with `$id_branch` concat |
| `get_metal` | L2101-2105 | scheme_modal (metal) | AJAX metal list for history filter |
| `getPaymentContent` | L154-158 | payment, scheme_account | AJAX — get payment content by scheme account |
| `GiftCardPayment` | L4265-4285 | gift_card | Gift card payment page — web customer view |
| `gPayMobileResponseURL` | L4154-4204 | payment | CCAvenue decrypt → GPay mobile |
| `gPayResponseMURL` | L4122-4153 | payment | G-Pay (CCAvenue variant) mobile response |
| `gPaytechProMblResponseURL` | L4205-4264 | payment | TechProcess → GPay mobile |
| `hdfcRedirect` | L3886-3888 | — | Simple redirect page — echo "Please wait" |
| `hdfcTransStatus` | L3878-3885 | — | Simple HTML display of payment status |
| `index` | L98-122 | scheme_account, payment, payment_gateway | Customer payment landing page |
| `insert_common_data` | L3647-3680 | payment, trans | Standard integration sync (integrationType=2/3) |
| `insert_common_data_jil` | L3611-3645 | payment, trans | JIL integration sync (integrationType=1) |
| `insert_referral_data` | L3682-3748 | scheme_account, agent, employee | Referral incentive (duplicate of adminapp_api) |
| `insertAgentIncentive` | L4536-4563 | — | Agent cash credit |
| `insertEmployeeIncentive` | L4564-4598 | — | Employee wallet credit |
| `insertWalletTrans` | L3406-3582 | wallet_transaction, inter_wallet_account | Wallet debit on payment |
| `ipporesponseURL` | L4635-4676 | payment | Ippo gateway response — BUG-031 credential spoofing |
| `ispanReq` | L3370-3378 | payment_modal (checkPanNo) | PAN requirement check + update customer |
| `metal_report` | L2107-2112 | payment_modal (history) | AJAX metal filter in payment history page |
| `mGiftCardPayment` | L3916-4121 | payment, gift_card, scheme_account | Mobile gift card payment — full flow |
| `mobile_payment` | L2147-2594 | payment, scheme_account, scheme, chit_settings, metal_rates | Customer web mobile payment flow (GET-based) |
| `mobileResponseURL` | L3822-3877 | payment | HDFC mobile response |
| `no_to_words` | L2045-2063 | — | Amount to words utility |
| `no_to_words1` | L2064-2100 | — | Amount to words variant |
| `payment_cancel` | L1949-2011 | payment | PayU web cancel (class method, L1949) |
| `payment_failure` | L1663-1730 | payment | PayU web failure (class method, L1663) |
| `payment_gateway` | L88-97 | payment_gateway | Gateway credentials by branch+id |
| `payment_history` | L2114-2146 | payment, scheme_account | Customer web payment history |
| `payment_rejected` | L3889-3891 | — | Load request_status view |
| `payment_success` | L1731-1948 | payment, scheme_account | PayU web success (class method, L1731) |
| `paySubmit` | L306-914 | payment, scheme_account, scheme | Web payment form submit → gateway redirect |
| `pdc_report` | L3249-3260 | postdate_payment, payment | PDC report — clean passthrough |
| `random_strings` | L4393-4407 | — | Random string gen |
| `razorresponseURL` | L4677-4681 | — | ⚠️ Debug stub: print_r($_POST); exit — BUG-032 |
| `responseURL` | L3749-3821 | payment | HDFC/CCAvenue web response |
| `send_sms` | L3261-3283 | sms_api_settings | sms_api_settings | Legacy SMS via URL template curl |
| `send_sms_wallet` | L3583-3608 | — | — | Wallet-specific SMS via sms_model (serviceID=17) |
| `split_payment` | L3297-3369 | payment | payment | BUG-041,042,043,044 — no trans, wrong serviceID |
| `submitToAtom` | L915-952 | — | Atom payment redirect form |
| `submitToTechProcess` | L1050-1182 | — | TechProcess redirect form (SOAP) |
| `successMURL` | L2863-3113 | payment, scheme_account | Mobile PayU success |
| `techProcessMobileResponseURL` | L1415-1645 | payment | TechProcess mobile response |
| `techProcessResponseURL` | L1183-1414 | payment, scheme_account | TechProcess web response + post-payment ops |
| `test` | L3892-3914 | payment, wallet | ⚠️ Debug/testing method — hardcoded transaction ID |
| `update_otp` | L3284-3294 | sms_api_settings | sms_api_settings | SMS quota decrement |
| `wallet_transaction` | L159-170 | wallet_account, wallet_transaction | Wallet view page |
| `wallet_transactionDB` | L3379-3405 | wallet_account | wallet_transaction | BUG-024,045 — sig mismatch |

### Global Functions (Outside Class, L11-40)

| Function | Lines | Purpose |
|---|---|---|
| `payment_success()` | L11-15 | Global wrapper → `Paymt::payment_success()` |
| `payment_failure()` | L16-20 | Global wrapper → `Paymt::payment_failure()` |
| `payment_cancel()` | L21-25 | Global wrapper → `Paymt::payment_cancel()` |
| `mobile_failure()` | L26-30 | Global wrapper → `Paymt::failureMURL()` |
| `mobile_cancel()` | L31-35 | Global wrapper → `Paymt::cancelMURL()` |
| `mobile_success()` | L36-40 | Global wrapper → `Paymt::successMURL()` |

---

## 7d. Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `payment` | `get_payment_details` L440, `get_paymenthistory` L1479, `getInsNo` L1581, `get_payment_collection` L1673, `isPaymentExist` L1309, `isPendingStatExist` L1328, `getWalletPaymentContent` L1452, `paymentHistory_get` L1007, `techProcessResponseURL` L1183, `mobile_payment` L2147 | `mobile_payment_post` L1381, `paySubmit` L306, `updateGatewayResponse` (payment_modal), `adminAppSuccess` L1768/L2595, `successMURL` L2863, `split_payment` L3297, `mGiftCardPayment` L3916 |
| `scheme_account` | `get_payment_details` L440, `get_paymenthistory` L1479, `customerSchemes_post` L774 | `createAccount_post` L406 (insert_schemeAcc), `generateAcNoOrReceiptNo` L4017/L4471 (update_account) |
| `customer` | `get_customerByMobile` L1401, `cusByMobileBranchWise` L1432, `get_customer_byAcc` L1975, `get_paymenthistory` L1479, `get_customerByID` L2192 | `insert_customer` L223, `createCustomer_post` L186 (updData) |
| `employee` | `isValidLogin` L22, `get_branch` L1589, `get_employee_details` L1821 | `authenticate_post` L139 (employee_devices only) |
| `employee_devices` | `isValidLogin` L22 | `isValidLogin` L22 (insert_collectionDevices L2228), `storeCollectionDevices_post` L4511 |
| `chit_settings` | `company_details` L181, `get_payment_details` L440, `get_paymenthistory` L1479, `wallet_accno_generator` L245, `get_branch` L1589, `get_entrydate` L1651 | — (config only) |
| `metal_rates` | `get_metalrate` L422, `get_metalrate_by_branch` L2298, `get_payment_details` L440 | — |
| `inter_wallet_account` | `getWalletPaymentContent` L1452, `insChitwallet` L252 | `insChitwallet` L252, `insertWalletTrans` L3406 |
| `wallet_transaction` | `get_wallet_transactions` (payment_modal) | `insChitwallet` L252, `insertWalletTrans` L3406, `wallet_transactionDB` L3379 |
| `scheme` | `getScheme_get` L917, `get_payment_details` L440, `createAccount_post` L406 | — |
| `sms_api_settings` | `send_sms` L140/L3261 | `update_otp` L168/L3284 |
| `gifts` | `createAccount_post` L406 | — |
| `gift_card` | `GiftCardPayment` L4265, `ajax_giftdetails` L4286, `mGiftCardPayment` L3916 | `createAccount_post` L406 (insert_voucher) |
| `ret_day_closing` | `get_entrydate` L1651 | — |
| `postdate_payment` | `pdc_report` L3249, `get_payment_details` L440 | — |
| `remarks` | `insertRemarks` L2140 | `insertRemarks` L2140 |
| `scheme_enquiry` | `insert_sch_enquiry` L2264 | `insert_sch_enquiry` L2264 |
