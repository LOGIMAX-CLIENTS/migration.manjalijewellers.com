# chit_collection_app — Method Index

> **Brain Built:** 2026-03-17 | **Round:** 1  
> **Total Controller Methods:** 73 (adminapp_api.php) + 69 (paymt.php)  
> **Total Model Methods:** 55 (adminappapi_model.php)

---

## 7a. adminapp_api.php — Controller Methods (Alphabetical)

| Method | Lines | Tables Read | Tables Written | Notes |
|---|---|---|---|---|
| `_checkArgumentValidation` | ~L | — | — | PayU helper |
| `_curlCall` | ~L | — | — | PayU helper |
| `_email_validation` | ~L | — | — | PayU helper |
| `_emptyValidation` | ~L | — | — | PayU helper |
| `_getHashKey` | ~L | — | — | PayU helper |
| `_getURL` | ~L | — | — | PayU helper |
| `_pay` | ~L | — | — | PayU helper |
| `_payment` | ~L | — | — | PayU primary entry for PayU |
| `_removeSpaceAndPreparePostArray` | ~L | — | — | PayU helper |
| `_typeValidation` | ~L | — | — | PayU helper |
| `adminAppSuccess` | ~L | payment | payment | Admin app payment success handler |
| `adminapp` | ~L | payment | payment | Admin app gateway flow |
| `agentWise_monthly_reports_post` | ~L | payment, scheme_account, agent | — | Agent report |
| `amount_to_weight` | ~L | metal_rates | — | Utility: amount÷rate |
| `array_sort` | L66-95 | — | — | Utility: array sort |
| `authenticate_post` | L139-183 | employee, agent, employee_devices, branch | employee_devices | Login + device check |
| `branchesByEmp_post` | ~L | branch, customer | — | Filter branches for employee |
| `cf_payment_status_post` | ~L | payment | payment | Cashfree status update |
| `check_regOTP_post` | L1318-1339 | — | — | OTP validation (client-side compare) |
| `checkMobileReg_get` | L1224-1235 | customer | — | Mobile registration check |
| `createAccount_post` | L406-710 | scheme_account, scheme, payment, gifts, gift_card | scheme_account, payment, gift_card | Scheme joining (new/existing) |
| `createCustomer_post` | L186-402 | customer, address | customer, address | Customer registration + image upload |
| `currency_get` | L125-137 | chit_settings, branch | — | Branch currency via mobileapi_model |
| `customer_ledger_details_post` | ~L | payment, scheme_account | — | Ledger detail |
| `customer_ledger_post` | ~L | payment, scheme_account | — | Ledger summary |
| `easebuzzResponse_post` | ~L | payment | payment | Easebuzz callback |
| `generateCFtoken` | ~L | — | — | Cashfree token gen |
| `generateOTP_get` | L1237-1316 | customer | — | OTP generation (⚠️ static 123456) |
| `generateTranUniqueId` | ~L | payment | — | Unique transaction ID (integrationType=5) |
| `generateAcNoOrReceiptNo` | ~L | scheme_account, payment | scheme_account, payment | Post-payment acc/receipt gen |
| `get_all_branch_get` | L1143-1148 | branch | — | All branches |
| `get_branch_get` | L1090-1096 | branch, scheme_branch | — | Branch list by profile |
| `getallAcitiveschemes_get` | ~L | scheme | — | Active scheme list |
| `getAllPaymentGateways_get` | ~L | payment_gateway | — | Gateway list |
| `getClassificationAll_get` | ~L | metal_classification | — | Metal classification |
| `getCusByMobile_post` | L890-915 | customer | — | Customer by mobile (lightweight) |
| `getDataFromOffline` | ~L | — | customer, scheme_account | Khimji offline sync pull |
| `getPaymentData` | L1042-1085 | payment, company, scheme_account, scheme | — | Thermal print string builder |
| `getScheme_get` | L917-1005 | scheme, chit_settings, scheme_group, scheme_account | — | Scheme details + join eligibility |
| `getVersion_get` | L106-123 | company, chit_settings | — | App version + maintenance |
| `insert_referral_data` | ~L | scheme_account, agent, employee | wallet_transaction, referral_data | Referral incentive insert |
| `insertAgentIncentive` | ~L | — | agent_incentive | Agent commission |
| `insertEmployeeIncentive` | ~L | — | employee_incentive | Employee commission |
| `insertOfflineCustomer` | ~L | customer, address | customer, address | Sync offline customer |
| `insertOfflinepayments` | ~L | payment, scheme_account | payment, scheme_account | Sync offline payment |
| `IsNullOrEmptyString` | L55-57 | — | — | Utility |
| `isNumberRegistered` | L1100-1141 | customer, employee, agent | — | Mobile+email check (internal) |
| `isNumberRegistered_get` | L1341-1362 | customer, employee, agent | — | Mobile+email check (REST) |
| `manualTUIDgen_get` | ~L | — | payment | Manual TUID generation |
| `mobile_payment_get` | ~L | — | — | Mobile payment GET (redirect?) |
| `mobile_payment_post` | L1381-~1800 | payment, scheme_account, customer, scheme, chit_settings, metal_rates | payment | **Core collection payment** |
| `no_to_words` / `no_to_words1` | ~L | — | — | Amount to words (receipt print) |
| `otp_sms` | L1364-1376 | company | — | OTP SMS message builder |
| `pay_collection_post` | L1166-1222 | payment, scheme_account, employee, agent | — | Collection report |
| `payment_gateway` | ~L | payment_gateway | — | Gateway credentials by branch+id |
| `paymentHistory_get` | L1007-1040 | payment, scheme_account, scheme, customer, branch | — | Employee collection history |
| `pendingMsg_post` | ~L | payment, customer | — | Push pending notification |
| `random_strings` | ~L | — | — | Random string gen (email gen) |
| `sch_enquiry_post` | ~L | scheme_enquiry | scheme_enquiry | Scheme enquiry registration |
| `sendCusDataToOffline` | ~L | customer | — | Push customer to Khimji |
| `storeCollectionDevices_post` | ~L | employee_devices | employee_devices | Device registration |
| `syncAgentCustomers_post` | ~L | customer, agent | customer | Agent customer sync |
| `syncOfflineAccPayments_post` | ~L | payment | payment | Offline payment batch sync |
| `syncOfflineCustomers_post` | ~L | customer | customer | Offline customer batch sync |
| `terms_and_conditions_get` | ~L | chit_settings | — | T&C text |
| `wallet_account_create` | L712-752 | services_modal | wallet_account | Wallet account creation |
| `customerIncentive` | ~L | — | customer_incentive | Customer referral benefit |
| `__encrypt` | L97-100 | — | — | BASE64 encode (NOT encryption!) |
| `get_values` | L59-63 | — | — | JSON POST body reader |

---

## 7b. adminappapi_model.php — Model Methods (Alphabetical)

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `__encrypt` | ~L | — | — | isValidLogin |
| `clientEmail` | L1375-1380 | customer | — | isNumberRegistered |
| `company_details` | L181-191 | company, chit_settings, country, state, city | — | authenticate_post, createCustomer_post |
| `cusByMobileBranchWise` | L1432-1450 | customer | — | getCusByMobile_post |
| `get_activeSchemes` | ~L | scheme | — | syncOffline* |
| `get_all_branch` | ~L | branch | — | get_all_branch_get |
| `get_branch` | L1589-~ | branch, scheme_branch | — | get_branch_get |
| `get_chit_settings` | L215-220 | chit_settings | — | admin screens |
| `get_company` | ~L | company | — | getPaymentData |
| `get_costcenter` | ~L | chit_settings | — | get_branch |
| `get_currency` | ~L | chit_settings, branch | — | currency_get |
| `get_customer_byAcc` | L~ | scheme_account, customer, scheme | — | customerSchemes_post |
| `get_customerByAgent` | ~L | customer, agent | — | syncAgentCustomers_post |
| `get_customerByID` | ~L | customer | — | createAccount_post |
| `get_customerByMobile` | L1401-1429 | customer | — | customerSchemes_post |
| `get_customerProfile` | ~L | customer, address | — | Internal |
| `get_entry_records` | ~L | payment, scheme_account, scheme, employee, branch | — | getPaymentData |
| `get_metalData` | ~L | metal_rates, metal_classification | — | pay_collection_post |
| `get_metalrate` | L422-438 | metal_rates, branch_rate | — | Internal |
| `get_metalrate_by_branch` | ~L | metal_rates, branch_rate, metal_purity | — | get_payment_details |
| `get_payment_collection` | ~L | payment, scheme_account, customer, scheme, employee, agent, branch | — | pay_collection_post |
| `get_payment_details` | L440-1294 | scheme_account, scheme, payment, customer, branch, chit_settings, postdate_payment, scheme_group | scheme_account | customerSchemes_post via mobileapi_model |
| `get_paymenthistory` | L1479-1579 | payment, scheme_account, scheme, customer, branch, chit_settings, village, payment_mode | — | paymentHistory_get |
| `get_settings` | ~L | chit_settings | — | Internal |
| `get_terms_and_conditions` | ~L | chit_settings | — | terms_and_conditions_get |
| `getActivecardBrands` | ~L | card_brands | — | Internal |
| `getallAcitiveschemes` | L193-211 | scheme, chit_settings, scheme_branch | — | getallAcitiveschemes_get |
| `getBranchGateways` | ~L | payment_gateway, branch_gateway | — | Internal |
| `getCustomerByMobile` | ~L | customer | — | syncOffline |
| `getCustomerSchAcc` | ~L | scheme_account | — | sync |
| `getInsNo` | L1581-1587 | payment, scheme_account | — | get_paymenthistory |
| `getLastTransaction` | L1300-1306 | payment | — | get_payment_details |
| `getPayGenData` | L1465-1477 | payment, scheme_account, scheme, branch, chit_settings | — | generateAcNoOrReceiptNo |
| `getWalletPaymentContent` | L1452-1463 | payment, chit_settings, scheme_account, customer, inter_wallet_account | — | adminAppSuccess |
| `insert_collectionDevices` | ~L | employee_devices | employee_devices | isValidLogin |
| `insert_customer` | L223-244 | customer, address | customer, address | createCustomer_post |
| `insert_sch_enquiry` | ~L | scheme_enquiry | scheme_enquiry | sch_enquiry_post |
| `insChitwallet` | L252-420 | inter_wallet_trans, inter_wallet_trans_detail, inter_wallet_trans_tmp* | wallet_transaction, inter_wallet_account | wallet_account_create |
| `insertRemarks` | ~L | remarks | remarks | pendingMsg_post |
| `isOfflineMobile` | ~L | customer | — | syncOffline |
| `isPaymentExist` | L1309-1326 | payment, scheme_account, customer | — | get_payment_details |
| `isPendingStatExist` | L1328-1346 | payment, scheme_account, customer | — | get_payment_details |
| `isValidLogin` | L22-138 | employee, agent, branch, employee_devices | employee_devices | authenticate_post |
| `isMobileExists` | L1349-1374 | employee, agent | — | isNumberRegistered |
| `monthly_agent_reports` | ~L | payment, agent, scheme_account | — | agentWise_monthly_reports_post |
| `old_get_customerByMobile` | L1381-1399 | customer | — | (deprecated, replaced by get_customerByMobile) |
| `send_sms` | L140-167 | sms_api_settings | sms_api_settings (debit_sms decrement) | Multiple post actions |
| `update_otp` | L168-179 | sms_api_settings | sms_api_settings | send_sms |
| `updData` | ~L | {any} | {any} | createCustomer_post (doc update) |
| `wallet_accno_generator` | L245-251 | chit_settings | — | createCustomer_post |
| `get_branchesByEmp` | ~L | branch, employee | — | branchesByEmp_post |
| `customer_reports` | ~L | payment, scheme_account, customer | — | report endpoints |
| `get_employee_details` | ~L | employee | — | createAccount_post |

---

## 7c. paymt.php — Key Controller Methods (Alphabetical)

| Method | Lines | Tables R/W | Notes |
|---|---|---|---|
| `adminapp` | ~L | payment | Admin app gateway (PayU) |
| `adminAppSuccess` | ~L | payment, scheme_account | Admin app PayU success |
| `atomReturnURL` | L953-1049 | payment, scheme_account | Atom gateway response handler |
| `cancelMURL` | ~L | payment | Mobile PayU cancel |
| `cashfreemobile` | ~L | payment | Cashfree mobile return |
| `cashfreeresponseURL` | ~L | payment | Cashfree web return URL |
| `chit_detail_report` | ~L | payment, scheme_account | Scheme detail report |
| `failureMURL` | ~L | payment | Mobile PayU failure |
| `gPayResponseMURL` | ~L | payment | G-Pay (TechProcess variant) mobile response |
| `generateInvoice` | ~L | payment, scheme_account | PDF invoice generation |
| `GiftCardPayment` | ~L | payment, gift_card | Gift card redemption |
| `index` | L98-122 | scheme_account, payment, payment_gateway | Customer payment landing page |
| `insert_common_data` | ~L | payment, trans | Standard integration sync |
| `insert_common_data_jil` | ~L | payment, trans | JIL integration sync |
| `insert_referral_data` | ~L | scheme_account, agent, employee | Referral incentive (duplicate of adminapp_api) |
| `insertWalletTrans` | ~L | wallet_transaction, inter_wallet_account | Wallet debit on payment |
| `ipporesponseURL` | ~L | payment | Ippo gateway response |
| `mobileResponseURL` | ~L | payment | HDFC mobile response |
| `payment_cancel` | ~L | payment | PayU web cancel |
| `payment_failure` | ~L | payment | PayU web failure |
| `payment_history` | ~L | payment, scheme_account | Customer web payment history |
| `payment_success` | ~L | payment, scheme_account | PayU web success |
| `paySubmit` | L306-914 | payment, scheme_account, scheme | Web payment form submit + gateway redirect |
| `pdc_report` | ~L | postdate_payment, payment | PDC report |
| `responseURL` | ~L | payment | HDFC web response |
| `split_payment` | ~L | payment | Split payment handler |
| `submitToAtom` | L915-952 | — | Atom payment redirect |
| `submitToTechProcess` | L1050-1182 | — | TechProcess redirect |
| `successMURL` | ~L | payment, scheme_account | Mobile PayU success |
| `techProcessMobileResponseURL` | L1415-~ | payment | TechProcess mobile response |
| `techProcessResponseURL` | L1183-1414 | payment, scheme_account | TechProcess web response + post-payment ops |
| `wallet_transaction` | L159-170 | wallet_account, wallet_transaction | Wallet view |

---

## 7d. Table → Methods Reverse Map

| Table | Read By (Key Methods) | Written By |
|---|---|---|
| `payment` | `get_payment_details`, `get_paymenthistory`, `getInsNo`, `get_payment_collection`, `isPaymentExist`, `isPendingStatExist`, `getWalletPaymentContent`, `paymentHistory_get`, `techProcessResponseURL` | `mobile_payment_post`, `paySubmit`, `updateGatewayResponse` (payment_modal), `adminAppSuccess`, `successMURL` |
| `scheme_account` | `get_payment_details`, `get_paymenthistory`, `customerSchemes_post` | `createAccount_post` (insert_schemeAcc), `generateAcNoOrReceiptNo` (update_account) |
| `customer` | `get_customerByMobile`, `cusByMobileBranchWise`, `get_customer_byAcc`, `get_paymenthistory` | `insert_customer`, `createCustomer_post` (updData) |
| `employee` | `isValidLogin`, `get_branch` | `authenticate_post` (employee_devices only) |
| `employee_devices` | `isValidLogin` | `isValidLogin` (insert_collectionDevices), `storeCollectionDevices_post` |
| `chit_settings` | `company_details`, `get_payment_details`, `get_paymenthistory`, `wallet_accno_generator`, `get_branch` | — (config only) |
| `metal_rates` | `get_metalrate`, `get_metalrate_by_branch`, `get_payment_details` | — |
| `inter_wallet_account` | `getWalletPaymentContent`, `insChitwallet` | `insChitwallet`, `insertWalletTrans` |
| `wallet_transaction` | `get_wallet_transactions` (payment_modal) | `insChitwallet`, `insertWalletTrans`, `wallet_transactionDB` |
| `scheme` | `getScheme_get`, `get_payment_details`, `createAccount_post` | — |
| `sms_api_settings` | `send_sms` | `update_otp` |
| `gifts` | `createAccount_post` | — |
| `gift_card` (inferred) | `GiftCardPayment` | `createAccount_post` (insert_voucher) |
