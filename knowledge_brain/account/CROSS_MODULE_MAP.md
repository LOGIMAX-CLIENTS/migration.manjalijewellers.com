# Account Module — Cross-Module Map
> **Round**: 2 | **Date**: 2026-03-24

---

## 1. Module Dependencies (Outbound)

| Dependency Module | Model Used | Purpose | Methods Called |
|---|---|---|---|
| **Payment** | `payment_model` | Free payments, receipt gen, payment data | `paymentDB()`, `generate_receipt_no()`, `get_due_date()`, `get_account_payment()`, `get_settings()`, `get_empreferrals_datas()`, `get_customer()`, `get_metalrate_by_branch()`, `isRateFixed()`, `updFixedRate()`, `getGold22ct()`, `gift_expotp()`, `getPaidInsData()` |
| **Customer** | `customer_model` | Customer CRUD, cust data | `get_cust()`, `customer_data()`, `get_entrydate()`, `update_customer_only()` |
| **Settings** | `admin_settings_model` | Settings, branch, access | `settingsDB()`, `get_service()`, `get_company()`, `get_branches()`, `get_access()`, `receipt_type()`, `get_branchcompany()`, `getBranchDetails()` |
| **SMS** | `admin_usersms_model` | SMS, WhatsApp, notifications | `get_SMS_data()`, `sendSMS_MSG91/Nettyfish/SpearUC/Asterixt/Qikberry()`, `send_whatsApp_message()`, `check_noti_settings()`, `insert_sent_notification()` |
| **Email** | `email_model` | Email dispatch | `send_email()` |
| **Wallet** | `Wallet_model` | Wallet CRUD | `wallet_transactionDB()` |
| **Employee** | `employee_model` | Employee by branch | `getEmployeeByBranch()` |
| **Sync API** | `syncapi_model` | Offline/online sync | `getcustomerByStatus()`, `getRegisteredAccTransactions()`, `checkClientID()`, `updatePayment()`, `insertPayment()`, `update_account()`, `updateData()` |
| **SKTM Sync** | `sktm_syncapi_model` | SKTM group sync | Same as Sync API |
| **Chit API** | `chitapi_model` | JIL sync | `getNewCustomerByTranStatus()`, `update_newCustomer()` |
| **Chit Admin** | `chitadmin_model` | Admin operations | General admin queries |
| **Log** | `log_model` | Audit trail | `log_detail()` |

---

## 2. Module Dependencies (Inbound)

| Calling Module | How It Calls Account | Methods Used |
|---|---|---|
| **Payment Controller** | Looks up scheme account data | Via `payment_model->get_scheme_account()` |
| **Mobile API** | Customer app operations | Via `mobileapi_model` → `scheme_account` table |
| **Reports** | Account remarks, history | `accountRemarks()`, `getRemarkPayments()` |
| **Dashboard** | Summary counts | Direct DB queries on `scheme_account` |

---

## 3. External AJAX Calls

| Endpoint | Called From (JS) | Response Type |
|---|---|---|
| `account/get/ajax_account_list` | Account list page | JSON (accounts array) |
| `account/get/ajax_closed_acc_list` | Closed list page | JSON (closed accounts) |
| `account/get/ajax_list` | Scheme account dropdown | JSON (accounts) |
| `account/get/ajax_account` | All accounts by mobile | JSON |
| `account/payment_detail/:id` | Account detail modal | JSON |
| `branch/branchname_list` | Branch dropdown filter | JSON |
| `metal/metalname_list` | Metal dropdown | JSON |
| `account/ajaxscheme_group/list` | Group list page | JSON |
| `checkreferalcode` | Form referral input | echo TRUE/0 |
| `referralcode_check` | Form referral validation | JSON |
| `check_group` | Group code validation | echo TRUE/FALSE |
| `generate_giftotp` | Gift OTP button | JSON (otp) |
| `gift_verify_otp` | Gift OTP verify | JSON (result) |
| `gift_issue` | Gift save | echo status |
| `get_gift_issued_list` | Gift issued table | JSON |
| `rateFixing_otp` | Rate fix OTP button | JSON (otp) |
| `submit_ratefix` | Rate fix submit | JSON (result) |
| `getCustomersBySearch` | Customer autocomplete | JSON |
| `loadGiftData` | Gift dropdown | JSON |
| `is_agent_exist` | Agent code check | JSON |
| `update_gift_status` | Gift status toggle | JSON |
| `digi_wallet_screen` | DigiGold wallet popup | JSON |
| `set_remarks_byid` | Account remark update | JSON |
| `blk_payment_byid` | Block/unblock payment | JSON |
| `getRemarkPayments` | Remark report AJAX | JSON |
| `ajax_requests_list` | Scheme reg list | JSON |
| `update_request` | Request approve/reject | (side effects) |
| `manual_schemeaccount` | Manual acc no generate | echo count |
| `update_sche_acc_sts` | Bulk status update | JSON |
| `sendotp_scheme_join` | Scheme join OTP | JSON |
| `verifyotp_scheme_join` | Verify OTP | JSON |
| `sendotp_gift` | Gift OTP (inv) | JSON |
| `verifyotp_gift` | Gift OTP verify (inv) | JSON |
| `save_giftissued` | Save gift (inv) | JSON |
| `cancel_giftissued` | Cancel gift (inv) | JSON |
| `get_gift_bystock` | Gift by stock | JSON |
| `get_gift_account` | Gift account dropdown | JSON |
| `get_gift_issued_byaccount` | Gift list by account | JSON |
| `get_gift_validation` | Gift validation rules | JSON |
| `get_gifts_from_inv` | Inventory gifts | JSON |
| `getGiftByRef` | Gift by ref no | JSON |
| `getBranchDetails` | Branch detail lookup | JSON |
| `getEmployeeByBranch` | Employee dropdown | JSON |

---

## 4. External API Integrations

| Integration | Type | URL | Purpose |
|---|---|---|---|
| **ERP Rate Fixing** | POST | `config('erp_baseURL') . "RateFixing"` | Fix metal rate in ERP |
| **ERP Login** | POST | `config('erp_baseURL') . "loginRequest"` | Get bearer token |
| **OneSignal Push** | POST | `https://onesignal.com/api/v1/notifications` | Push notifications |
| **SMS Gateways** | Various | 5 different gateways | OTP / notification SMS |

---

## 5. Referenced Tables (Full List)

| Table | Read | Write | Delete | Key Usage |
|---|---|---|---|---|
| `scheme_account` | ✅ | ✅ | ✅ | Core CRUD |
| `customer` | ✅ | ✅ | — | Join for account details |
| `scheme` | ✅ | — | — | Scheme rules/config |
| `payment` | ✅ | ✅ | — | Free payments, history |
| `scheme_group` | ✅ | ✅ | ✅ | Group CRUD |
| `gift_issued` | ✅ | ✅ | ✅ | Gift tracking |
| `gifts` | ✅ | — | — | Gift master |
| `otp` | ✅ | ✅ | — | OTP management |
| `wallet_account` | ✅ | ✅ | — | Employee wallets |
| `wallet_transaction` | ✅ | ✅ | — | Referral credits/debits |
| `gift_card` | ✅ | ✅ | — | Voucher management |
| `customer_kyc` | — | ✅ | — | KYC insert |
| `branch` | ✅ | — | — | Branch lookup |
| `sync_log` | — | ✅ | — | Sync audit |
| `customer_reg` | ✅ | ✅ | — | Sync intermediate |
| `reg_request` | ✅ | ✅ | ✅ | Registration requests |
| `chit_settings` | ✅ | — | — | Global settings |
| `ret_financial_year` | ✅ | — | — | FY for acc number |
| `scheme_interest_chart` | ✅ | — | — | Benefit calculation |
| `scheme_general_advance_benefit_settings` | ✅ | — | — | GA bonus |
| `ret_other_inventory_purchase_items_details` | ✅ | ✅ | — | Gift inventory |
| `ret_other_inventory_purchase_items_log` | — | ✅ | — | Gift inventory log |
| `loyalty_transaction` | — | ✅ | — | Agent referral |
| `log` | — | ✅ | — | Audit log |
| `log_detail` | — | ✅ | — | Audit log details |
| `chit_metal` | ✅ | — | — | Metal master |
| `purity` | ✅ | — | — | Purity master |
| `payment_mode` | ✅ | — | — | Payment mode lookup |
| `metal_rate` | ✅ | — | — | Metal rate lookup |
