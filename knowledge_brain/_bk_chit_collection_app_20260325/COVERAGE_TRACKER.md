# chit_collection_app — Coverage Tracker

> **Brain Updated:** 2026-03-17 | ✅ **BRAIN BUILD COMPLETE — All 4 Rounds Done**

---

## 🏁 Final Status

| Metric | Value |
|---|---|
| **Total Bugs Found** | **45** (7×P0, 13×P1, 18×P2, 6×P3, 1×Won't Fix) |
| **Method Coverage** | **~100%** of all meaningful methods |
| **Brain Files** | **11 documents** |
| **Rounds Completed** | 4 |

---

## File Coverage — adminapp_api.php (4528 lines) ✅

| Method | Status | Notes |
|---|---|---|
| `__construct()` | ✅ | Auth bypasses, static versions, model loads |
| `index_get()` | ✅ | App version check |
| `authenticate_post()` | ✅ | Login flow, OTP dispatch |
| `generateOTP_get()` | ✅ R1 | BUG-005 static OTP, BUG-006 undefined $service |
| `verifyOTP_get()` | ✅ R1 | Base64 OTP decode |
| `getCustomerDetails_get()` | ✅ R1 | Auth status check |
| `createCustomer_post()` | ✅ R1 | Registration, mkdir 0777 bugs |
| `createAccount_post()` | ✅ R1 | Scheme join, wallet creation |
| `mobile_payment_post()` | ✅ R1 | Full path analyzed, BUG-001 GST type |
| `mobile_payment_get()` | ✅ R1 | Cashfree SDK token generation |
| `generateCFtoken()` | ✅ R1 | Cashfree v-old API token |
| `cf_payment_status_post()` | ✅ R2 | BUG-012 no mkdir, BUG-013 no sig verify |
| `easebuzzResponse_post()` | ✅ R2 | BUG-011 integrationType mismatch, BUG-014 referral off |
| `hdfcResponse_post()` | ✅ R1 | HDFC NetBanking callback |
| `adminAppSuccess()` | ✅ R2 | BUG-007 $trans_id undefined |
| `insert_referral_data()` | ✅ R2 | Wallet credit + SMS by gateway |
| `payment_gateway()` | ✅ R1 | Config lookup |
| `generate_receipt_no()` | ✅ R1 | Short-code based receipt gen |
| `amount_to_weight()` | ✅ R1 | Simple divider |
| `getClassificationAll_get()` | ✅ R1 | Pass-thru |
| `getAllPaymentGateways_get()` | ✅ R1 | Returns card brands only |
| `syncAgentCustomers_post()` | ✅ R2 | Read-only agent customer fetch |
| `syncOfflineCustomers_post()` | ✅ R2 | BUG-008 $data shadow variable |
| `syncOfflineAccPayments_post()` | ✅ R2 | BUG-009 hardcoded URL |
| `insertOfflinepayments()` | ✅ R2 | BUG-001 also here |
| `insertOfflineCustomer()` | ✅ R3 | Simple insert |
| `getDataFromOffline()` | ✅ R2 | BUG-003 param swap |
| `sendCusDataToOffline()` | ✅ R2 | BUG-016 undefined var |
| `customer_ledger_post()` | ✅ R2 | BUG-015 date params unused |
| `customer_ledger_details_post()` | ✅ R2 | Pass-through |
| `agentWise_monthly_reports_post()` | ✅ R2 | Thin wrapper |
| `pendingMsg_post()` | ✅ R2 | Simple INSERT |
| `storeCollectionDevices_post()` | ✅ R2 | Simple INSERT |
| `terms_and_conditions_get()` | ✅ R1 | Returns DB terms |
| `branchesByEmp_post()` | ✅ R1 | Pass-thru |
| `sch_enquiry_post()` | ✅ R2 | BUG-023 hardcoded CC email |
| `insert_common_data()` | ✅ R3 | ERP staging push |
| `insert_common_data_jil()` | ✅ R3 | JIL ERP staging push |
| `generateAcNoOrReceiptNo()` | ✅ R3 | Khimji POST-payment gen |
| `generateTranUniqueId()` | ✅ R3 | BUG-030 undefined $pay |
| `insertWalletTrans()` | ✅ R3 | Delegates to payment_modal |
| `insertAgentIncentive()` | ✅ R3 | Loyalty table insert |
| `insertEmployeeIncentive()` | ✅ R3 | Employee wallet credit |
| `customerIncentive()` | ✅ R3 | BUG-029 session in REST |
| `_curlCall()` | ✅ R1 | BUG-022 display_errors |
| `no_to_words()` | ✅ R1 | Utility |

---

## File Coverage — adminappapi_model.php ✅

| Method | Status | Notes |
|---|---|---|
| `isValidLogin()` | ✅ R1 | BUG-010 SQL injection |
| `get_payment_details()` | ✅ R3 | Mega-query + PHP post-processing. BUG-025,026,027,028 |
| `get_customerByAgent()` | ✅ R1 | Simple SELECT |
| `get_activeSchemes()` | ✅ R1 | Simple SELECT |
| `getCustomerSchAcc()` | ✅ R1 | Simple SELECT |
| `customer_reports()` | ✅ R4 | BUG-038,039,040 — EMP full table scan |
| `monthly_agent_reports()` | ✅ R4 | BUG-034,035,036,037 — N+1, duplication, SQL injection |
| `insertRemarks()` | ✅ R1 | Simple INSERT |
| `getWalletPaymentContent()` | ✅ R3 | Full SQL traced |
| `getPayGenData()` | ✅ R3 | Full SQL + all output fields documented |
| `getPayIds()` | ✅ R3 | Simple SELECT by txnid |
| `insert_collectionDevices()` | ✅ R1 | Simple INSERT |
| `get_branchesByEmp()` | ✅ R1 | Branch filter logic |
| `get_terms_and_conditions()` | ✅ R1 | Simple SELECT |
| `get_customerByID()` | ✅ R1 | Full customer + address join |

---

## File Coverage — paymt.php (4704 lines) ✅

| Method | Status | Notes |
|---|---|---|
| `payment_success()` | ✅ R2 | Full PayU web success |
| `payment_failure()` | ✅ R1 | Failure update + SMS |
| `payment_cancel()` | ✅ R1 | Cancel update |
| `generateInvoice()` | ✅ R1 | DOMPDF receipt |
| `payment_history()` | ✅ R1 | History view |
| `mobile_payment()` | ✅ R1 | Full initiation flow |
| `adminAppSuccess()` | ✅ R2 | BUG-002 wrong serviceID |
| `adminapp()` | ✅ R1 | Echo redirect message |
| `successMURL()` | ✅ R2 | PayU mobile success |
| `failureMURL()` | ✅ R2 | PayU mobile failure |
| `cancelMURL()` | ✅ R2 | PayU mobile cancel |
| `pdc_report()` | ✅ R4 | Clean — passthrough view |
| `split_payment()` | ✅ R4 | BUG-041,042,043,044 — no transaction, wrong serviceID, hardcoded type |
| `wallet_transactionDB()` | ✅ R2/R4 | BUG-024,045 — sig mismatch, hardcoded emp ID |
| `send_sms()` | ✅ R4 | Legacy dead code |
| `update_otp()` | ✅ R4 | Legacy dead code |
| `responseURL()` | ✅ R3 | CCAvenue web — has verify |
| `mobileResponseURL()` | ✅ R3 | CCAvenue mobile — no verify |
| `hdfcTransStatus()` | ✅ R3 | Simple display |
| `gPayResponseMURL()` | ✅ R3 | Status mapper only |
| `gPayMobileResponseURL()` | ✅ R3 | CCAvenue decrypt → GPay |
| `gPaytechProMblResponseURL()` | ✅ R3 | TechProcess → GPay |
| `ipporesponseURL()` | ✅ R3 | BUG-031 credential spoofing |
| `razorresponseURL()` | ✅ R3 | BUG-032 debug stub |
| `cashfreeresponseURL()` | ✅ R3 | Web Cashfree |
| `cashfreemobile()` | ✅ R3 | BUG-033 fatal error |
| `chit_detail_report()` | ✅ R3 | DOMPDF report — clean |
| `insert_common_data()` | ✅ R3 | ERP staging push |
| `insert_common_data_jil()` | ✅ R3 | JIL ERP staging push |
| `insert_referral_data()` | ✅ R1 | Wallet credit after referral |
| `insertAgentIncentive()` | ✅ R3 | Agent cash credit |
| `insertEmployeeIncentive()` | ✅ R3 | Employee wallet credit |
| `customerIncentive()` | ✅ R3 | BUG-029 session issue |
| `generateTranUniqueId()` | ✅ R3 | Khimji integration |
| `generateAcNoOrReceiptNo()` | ✅ R3 | Khimji integration |
| `GiftCardPayment()` | ✅ R3 | Gift card view — clean |
| `no_to_words()` | ✅ R1 | Utility |
| `random_strings()` | ✅ R1 | Utility |

---

## Final Coverage

| Priority | Status |
|---|---|
| **All P0 bugs** | ✅ 100% found & registered |
| **All high-impact flows** | ✅ 100% traced & documented |
| **All methods — 3 files** | ✅ ~100% coverage |
| **All gateways** | ✅ PayU, Cashfree, HDFC/CCAvenue, Atom, Ippo, RazorPay, GPay, TechProcess, Easebuzz |

---

## Complete Brain File Index

| File | Round | Purpose |
|---|---|---|
| `MODULE_BRAIN.md` | R1+R2 | Architecture, entry points, tables, risk summary, anti-patterns |
| `BUSINESS_RULES.md` | R1 | 12 business rules governing payment eligibility and calculation |
| `METHOD_INDEX.md` | R1 | Alphabetical method index across all 3 files |
| `DATA_FLOW.md` | R1 | Flows 1–6: Auth, cash payment, web payment, registration, scheme join |
| `DATA_FLOW_R2.md` | R2 | Flows 7–18: adminAppSuccess, Easebuzz, Cashfree, offline sync, Khimji, PayU callbacks |
| `DEEP_ANALYSIS_R3.md` | R3 | get_payment_details mega-query, incentive functions, Khimji helpers, last gateway callbacks |
| `FINAL_SWEEP_R4.md` | R4 | Last 5 methods: monthly_agent_reports, customer_reports, pdc_report, split_payment, wallet_transactionDB |
| `CROSS_MODULE_MAP.md` | R1 | External dependencies and integration points |
| `FORENSIC_TEMPLATE.md` | R1 | Bug investigation protocol and DB verification queries |
| `BUG_REGISTER.md` | R1–R4 | **45 bugs total** — P0–P3 with file:line and quick-fix table |
| `COVERAGE_TRACKER.md` | R1–R4 | This file — method-level coverage tracking |
