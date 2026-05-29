# chit_collection_app — Bug Register

> **Brain Updated:** 2026-03-25 | **Round:** R5-Upgrade  
> **Status Legend:** 🔴 Open | 🟡 Investigating | ✅ Fixed | ⚪ Won't Fix

---

## Critical Bugs (P0)

| Bug ID | Severity | File | Line | Description | Status |
|---|---|---|---|---|---|
| **COL-BUG-001** | P0 🔴 | `adminapp_api.php` | L1489, L3673 | `$sch_data['gst_type']` used but variable is `$chit`. Same bug in `insertOfflinepayments()`. GST type calculation broken for ALL collection app payments. Must be `$chit['gst_type']`. | 🔴 Open |
| **COL-BUG-002** | P0 🔴 | `paymt.php` | L2622 | `adminAppSuccess()` in paymt.php sets `$serviceID = 7` (failure service) instead of 3 (payment success). ALL cash payments via paymt.php send FAILURE SMS template to customers. | 🔴 Open |
| **COL-BUG-003** | P0 🔴 | `adminapp_api.php` | L4173-4174 | `getDataFromOffline()` swaps its own parameters on entry: `$cus_reference_no` always equals `$id_customer`, original `$cus_reference_no` arg is discarded. Khimji sync never uses correct reference no. | 🔴 Open |
| **COL-BUG-004** | P0 🔴 | `adminapp_api.php` | All routes | No API token authentication. Any request can hit any endpoint without auth token — only `authenticate_post` is gated. | 🔴 Open |

---

## High Bugs (P1)

| Bug ID | Severity | File | Line | Description | Status |
|---|---|---|---|---|---|
| **COL-BUG-005** | P1 🔴 | `adminapp_api.php` | L1243 | Static OTP `123456` hardcoded in `generateOTP_get()`. Completely bypasses OTP security. | 🔴 Open |
| **COL-BUG-006** | P1 🔴 | `adminapp_api.php` | L1272 | `$service['serv_whatsapp']` used but `$service` never defined in `generateOTP_get()`. PHP Notice/fatal on WhatsApp-enabled configs. | 🔴 Open |
| **COL-BUG-007** | P1 🔴 | `adminapp_api.php` | ~L1768 | In `adminAppSuccess()`, `$trans_id` is undefined — used in wallet debit (`'txnid' => $trans_id.'- D'`) but never set from input. Wallet debit for collection-app payments uses empty/null txnid. | 🔴 Open |
| **COL-BUG-008** | P1 🔴 | `adminapp_api.php` | L3458-3482 | `syncOfflineCustomers_post()`: outer `$data` (POST payload) overwritten by inner `$data = array(...)` inside foreach. `$data['login_type']` at L3482 reads from wrong context. ID agent assignment broken. | 🔴 Open |
| **COL-BUG-009** | P1 🔴 | `adminapp_api.php` | L3532 | `syncOfflineAccPayments_post()`: CURL to hardcoded URL `https://retail.logimaxindia.com/etail_v1/...`. Will fail on non-production environments, and is completely wrong for client deployments. | 🔴 Open |
| **COL-BUG-010** | P1 🔴 | `adminappapi_model.php` | L33-35 | `isValidLogin()`: `$username` concatenated raw into SQL — SQL injection on login endpoint. | 🔴 Open |
| **COL-BUG-011** | P1 🔴 | `adminapp_api.php` | L2461 | In `easebuzzResponse_post()` post-payment integration block: outer condition checks `integrationType==2` but inner `if` checks `integrationType==1` first — reverse of what's intended. Copy-paste error from JIL block. | 🔴 Open |

---

## Medium Bugs (P2)

| Bug ID | Severity | File | Line | Description | Status |
|---|---|---|---|---|---|
| **COL-BUG-012** | P2 🟡 | `adminapp_api.php` | L2801-2803 | `cf_payment_status_post()`: `file_put_contents` to `log/cashfree/` with no `mkdir` guard. Will throw PHP warning/error if directory doesn't exist. | 🔴 Open |
| **COL-BUG-013** | P2 🟡 | `adminapp_api.php` | L2804-2813 | `cf_payment_status_post()`: Cashfree webhook signature verification is COMMENTED OUT. No payment authenticity check — any POST can fake a SUCCESS callback. | 🔴 Open |
| **COL-BUG-014** | P2 🟡 | `adminapp_api.php` | L2882-2890 | `easebuzzResponse_post()`: `insert_referral_data` block is COMMENTED OUT. Referral benefits never credited for Easebuzz mobile payments. | 🔴 Open |
| **COL-BUG-015** | P2 🟡 | `adminapp_api.php` | L4354-4374 | `customer_ledger_post()`: `fromdate` and `todate` are parsed from POST but NEVER passed to `customer_reports()`. All ledger results are unfiltered by date. | 🔴 Open |
| **COL-BUG-016** | P2 🟡 | `adminapp_api.php` | L4348 | `sendCusDataToOffline()`: uses `$cus_reference_no` which is never defined in this function scope. Last echo will throw PHP Notice. | 🔴 Open |
| **COL-BUG-017** | P2 🟡 | `adminappapi_model.php` | L26 | `__encrypt()` returns `base64_encode($str)` — used as password storage or comparison in some flows. base64 is NOT encryption. | 🔴 Open |
| **COL-BUG-018** | P2 🟡 | `adminapp_api.php` | L265,278,290,338 | `createCustomer_post()`: `mkdir()` with permissions `0777` — world-writable directories created for customer images. | 🔴 Open |
| **COL-BUG-019** | P2 🟡 | `adminapp_api.php` | L251, L515 | `createCustomer_post()`: Nested `trans_begin()` calls without matching `trans_complete()` pairing — outer begin at ~L251, inner begin at ~L515 before wallet creation. | 🔴 Open |
| **COL-BUG-020** | P2 🟡 | `paymt.php` | L11-40 | Global functions `payment_success()`, `payment_failure()`, `payment_cancel()` defined BEFORE the class — dead code that can never be called via CI routing. | ⚪ Won't Fix |
| **COL-BUG-025** | P2 🟡 | `adminappapi_model.php` | L571 | Dead ternary in `get_payment_details()`: both branches of `($current_paid_installments==0 ? paid+1 : paid+1)` return identical values. The condition is evaluated but has zero effect. | 🔴 Open |
| **COL-BUG-026** | P2 🟡 | `adminappapi_model.php` | L615-617 | SQL injection inside average calculation sub-query: `id_scheme_account=`.$record->id_scheme_account concatenated raw. Lower risk (model-internal, data from DB) but still bad practice. | 🔴 Open |
| **COL-BUG-027** | P2 🟡 | `adminappapi_model.php` | L499-503 | `total_paid_amount` multiply by `no_of_dues` — if no_of_dues is NULL (old records), those payments are excluded from total. Silent miscalculation for mixed-era accounts. | 🟡 Investigating |

---

## Low Bugs / Technical Debt (P3)

| Bug ID | Severity | File | Line | Description | Status |
|---|---|---|---|---|---|
| **COL-BUG-021** | P3 | `adminapp_api.php` | L110-111 | `current_android_version = "1.0.0"`, `new_android_version = "1.0.1"` hardcoded in constructor. App version check stale. | 🔴 Open |
| **COL-BUG-022** | P3 | `adminapp_api.php` | L2135-2137 | `_curlCall()`: `display_errors = 1` hardcoded — leaks PHP errors in production for Easebuzz curl calls. | 🔴 Open |
| **COL-BUG-023** | P3 | `adminapp_api.php` | L4498-4504 | `sch_enquiry_post()`: email always CC'd to `abinaya@vikashinfosolutions.com` — hardcoded dev email in production. | 🔴 Open |
| **COL-BUG-024** | P3 | `paymt.php` | L3379, L3703 | `wallet_transactionDB()` has 2 calling signatures: `($payamt, $totamtuse_wallet)` from paymt.php internally vs `('insert', '', $wallet_data)` from payment_modal. Mismatch may cause wallet debit failures. | 🔴 Open |
| **COL-BUG-028** | P1 🔴 | `adminappapi_model.php` | L444-446 | Metal rates fetched via `file_get_contents(base_url()+'api/rate.txt')`. No error handling — if file missing/stale, all payment amount calculations use NULL rates. Silent wrong rate display. | 🔴 Open |
| **COL-BUG-029** | P1 🔴 | `adminapp_api.php` | L3381, `paymt.php` L4620 | `customerIncentive()` uses `$this->session->userdata('uid')` for employee ID. In collection app context (REST, no session), this is always NULL — employee intro incentive never credited correctly. | 🔴 Open |
| **COL-BUG-030** | P2 🟡 | `adminapp_api.php` | L4157 | `generateTranUniqueId()`: on errorCode==1001, tries to use `$pay['id_payment']` — `$pay` is undefined in this scope. PHP Notice + payment error log never written. | 🔴 Open |
| **COL-BUG-031** | P0 🔴 | `paymt.php` | L4638 | `ipporesponseURL()`: uses `$_POST['publicKey']` and `$_POST['secretKey']` to make authenticated API call. Attacker can provide arbitrary credentials + order_id to forge a success response. Critical security hole. | 🔴 Open |
| **COL-BUG-032** | P0 🔴 | `paymt.php` | L4677-4679 | `razorresponseURL()` is a debug stub: `print_r($_POST); exit;`. All RazorPay callbacks dump sensitive payment data to browser in plaintext. RazorPay payments completely non-functional. | 🔴 Open |
| **COL-BUG-033** | P0 🔴 | `paymt.php` | L4354 | `cashfreemobile()`: `$paymentgateway['param_1']` used but `$paymentgateway` is never defined. Fatal PHP error on every Cashfree mobile payment return — all Cashfree mobile payments fail at callback. | 🔴 Open |

---

## P1 High — Round 4 Additions

| Bug ID | Severity | File | Line | Description | Status |
|---|---|---|---|---|---|
| **COL-BUG-034** | P1 🔴 | `adminappapi_model.php` | L2048 | `monthly_agent_reports()`: `id_agent = $id_employee` raw concat in SQL — SQL injection. | 🔴 Open |
| **COL-BUG-038** | P1 🔴 | `adminappapi_model.php` | L2108-2109 | `customer_reports()`: EMP login has no WHERE clause — returns ALL customers in DB. Full table scan, no pagination. | 🔴 Open |
| **COL-BUG-042** | P1 🔴 | `paymt.php` | L3324 | `split_payment()`: `$serviceID = 7` (failure service) used for split payment success SMS. Same anti-pattern as COL-BUG-002. | 🔴 Open |
| **COL-BUG-043** | P1 🔴 | `paymt.php` | L3297-3368 | `split_payment()`: No `trans_begin()`/`trans_commit()` — partial splits cannot be rolled back on failure. Orphaned payment records possible. | 🔴 Open |

---

## P2 Medium — Round 4 Additions

| Bug ID | Severity | File | Line | Description | Status |
|---|---|---|---|---|---|
| **COL-BUG-035** | P2 🟡 | `adminappapi_model.php` | L2084-2088 | `monthly_agent_reports()`: `$completed` uses identical SQL as `$total_collection` — both arrays always have same data. `completed` field meaningless. | 🔴 Open |
| **COL-BUG-036** | P2 🟡 | `adminappapi_model.php` | L2054-2091 | N+1 query in `monthly_agent_reports()`: 3 DB queries per customer, no pagination. Agent with 200 customers = 601 queries per call. | 🔴 Open |
| **COL-BUG-037** | P2 🟡 | `adminappapi_model.php` | L2041-2048 | `$login_type` unused in `monthly_agent_reports()` — EMP login queries by `id_agent` field, returns wrong data. | 🔴 Open |
| **COL-BUG-039** | P2 🟡 | `adminappapi_model.php` | L2117 | `customer_reports()`: `$row['cus_img']` checked but NOT in SELECT list. Always null. Customer images never returned. | 🔴 Open |
| **COL-BUG-041** | P2 🟡 | `paymt.php` | L3313 | `split_payment()`: `payment_type = 'Payu Checkout'` hardcoded for all split payments. Wrong for Cashfree/HDFC/Atom gateways. | 🔴 Open |
| **COL-BUG-044** | P2 🟡 | `paymt.php` | L3299 | `split_payment()`: `$serv_model` set but model not loaded with `$this->load->model()`. SMS in split may fatal if services_modal not already loaded by constructor. | 🔴 Open |

---

## P3 Low / Technical Debt

| Bug ID | Severity | File | Line | Description | Status |
|---|---|---|---|---|---|
| **COL-BUG-021** | P3 | `adminapp_api.php` | L110-111 | `current_android_version = "1.0.0"`, `new_android_version = "1.0.1"` hardcoded. Stale app version check. | 🔴 Open |
| **COL-BUG-022** | P3 | `adminapp_api.php` | L2135-2137 | `_curlCall()`: `display_errors = 1` hardcoded — leaks PHP errors in production. | 🔴 Open |
| **COL-BUG-023** | P3 | `adminapp_api.php` | L4498-4504 | `sch_enquiry_post()`: hardcoded CC dev email in production. | 🔴 Open |
| **COL-BUG-024** | P3 | `paymt.php` | L3379, L3703 | `wallet_transactionDB()` 2-parameter vs 3-parameter signature mismatch. | 🔴 Open |
| **COL-BUG-040** | P3 | `adminappapi_model.php` | L2118 | `ucfirst()` applied to mobile number — copy-paste artifact, no-op. | 🔴 Open |
| **COL-BUG-045** | P3 | `paymt.php` | L3386 | `id_employee: 2` hardcoded in local `wallet_transactionDB`. | 🔴 Open |

---

## Upgrade Round — New Finding

| Bug ID | Severity | File | Line | Description | Status |
|---|---|---|---|---|---|
| **COL-BUG-046** | P2 🟡 | `paymt.php` | L1656 | `get_entrydate()`: `$id_branch` concatenated raw into SQL query for `ret_day_closing` table. SQL injection vulnerability — though lower risk since `$id_branch` typically comes from session/internal, it's unsanitized user input in some flows. | 🔴 Open |

---

## Bug Summary — R5-Upgrade (All Rounds + Upgrade)

| Severity | Count | Fixed |
|---|---|---|
| P0 (Critical) | 7 | 0 |
| P1 (High) | 13 | 0 |
| P2 (Medium) | 19 | 0 |
| P3 (Low) | 6 | 0 |
| ⚪ Won't Fix | 1 | — |
| **Total** | **46** | **0** |

---

## Quick Fix Reference — Top Priority

| Bug ID | File | Exact Fix |
|---|---|---|
| COL-BUG-001 | adminapp_api.php L1489, L3673 | `$sch_data['gst_type']` → `$chit['gst_type']` |
| COL-BUG-002 | paymt.php L2622 | `$serviceID = 7` → `$serviceID = 3` |
| COL-BUG-003 | adminapp_api.php L4173-4174 | Remove the 2 param-swap lines entirely |
| COL-BUG-005 | adminapp_api.php L1243 | `$otpStr = 123456` → `$otpStr = rand(100000, 999999)` |
| COL-BUG-007 | adminapp_api.php ~L1775 | Set `$trans_id` from `$paymtData` before wallet block |
| COL-BUG-008 | adminapp_api.php L3458 | Rename inner `$data` → `$cus_data`, fix L3482 reference |
| COL-BUG-009 | adminapp_api.php L3532 | Replace URL with `$this->config->item('base_url').'index.php/adminapp_api/createAccount'` |
| COL-BUG-011 | adminapp_api.php L2461 | Change inner `integrationType==1` → `integrationType==2` |
| COL-BUG-012 | adminapp_api.php L2801 | Add `mkdir` guard before `file_put_contents` |
| COL-BUG-023 | adminapp_api.php L4502 | Remove hardcoded CC dev email |
| COL-BUG-028 | adminappapi_model.php L444 | Add null check after `file_get_contents` + json_decode |
| COL-BUG-029 | adminapp_api.php L3381 | Pass `$id_employee` as param instead of session |
| COL-BUG-031 | paymt.php L4638 | Verify Ippo HMAC signature server-side, don't trust POST credentials |
| COL-BUG-032 | paymt.php L4677 | Implement RazorPay webhook handler (verify signature, call updateGatewayResponse) |
| COL-BUG-033 | paymt.php L4354 | Load `$paymentgateway` from `payment_modal::getBranchGatewayData()` |
| COL-BUG-034 | adminappapi_model.php L2048 | Use `$this->db->where('id_agent', $id_employee)->get()` instead of raw concat |
| COL-BUG-038 | adminappapi_model.php L2108 | Add WHERE clause for EMP login: `WHERE id_employee = $id_employee` |
| COL-BUG-042 | paymt.php L3324 | `$serviceID = 7` → correct split-payment service ID |
| COL-BUG-043 | paymt.php L3297 | Wrap entire split_payment loop in `trans_begin()` / `trans_commit()` |
| COL-BUG-046 | paymt.php L1656 | Use `$this->db->where('id_branch', $id_branch)->get('ret_day_closing')` instead of raw SQL concat |
