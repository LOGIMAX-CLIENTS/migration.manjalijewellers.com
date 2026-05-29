# chit_collection_app — Invariant Matrix

> **Module**: chit_collection_app
> **Built**: 2026-03-25 (Upgrade Round)
> **Purpose**: Documents variant-specific behavior for config-driven code paths in the collection app.

---

## 1. Variant Dimensions

| # | Dimension Name | Possible Values | Controlling Field (DB) | Controlling Field (PHP) | Effect |
|---|---|---|---|---|---|
| 1 | **Scheme Type** | 1=Gold(weight-based), 2=Amount-based, 3=Flexible | `scheme.scheme_type` | `$chit['scheme_type']` | Metal weight calculation formula |
| 2 | **GST Type** | 0=Inclusive, 1=Exclusive | `scheme.gst_type` | `$chit['gst_type']` / `$sch_data['gst_type']` ⚠️ BUG-001 | GST amount and metal weight formula |
| 3 | **Integration Type** | 0=None, 1=JIL, 2=SyncAPI, 3=SyncAPI-v2, 5=Khimji | `config: integrationType` | `$this->config->item('integrationType')` | Post-payment sync destination |
| 4 | **Payment Gateway** | 0=Cash, 1=PayU, 2=HDFC/CCAvenue, 3=TechProcess, 4=Cashfree, 5=Atom, 6=Ippo, 7=RazorPay, Easebuzz | `payment_gateway.pg_code` | `$pg_code` | Callback handler + signature verification |
| 5 | **Auto Pay Approval** | 0=Manual, 1=Auto-online, 2=Auto+sync, 3=Auto+firstPayAmt | `config: auto_pay_approval` | `$this->config->item('auto_pay_approval')` | Payment status on callback success |
| 6 | **Login Type** | EMP, AGENT | POST data | `$data['login_type']` | Branch access, customer assignment, referral eligibility |
| 7 | **Added By** | 1=Admin Web, 2=Customer App, 3=Collection App | `payment.added_by` | `$insertData['added_by']` | Payment approval, SMS service ID |
| 8 | **Scheme Acc No Set** | 0=Auto on 1st pay, 1=Manual, 2=Pre-populated, 3=Auto-alt | `chit_settings.schemeacc_no_set` | `$pay['schemeacc_no_set']` | When account number is generated |
| 9 | **SMS Gateway** | 1=MSG91, 2=Nettyfish, 3=SpearUC, 4=Asterixt, 5=Qikberry | `config: sms_gateway` | `$this->config->item('sms_gateway')` | SMS dispatch handler |
| 10 | **Due Type** | ND, PD, AD, PN, AN, APN | Computed | `$dueType` | Installment type, month/year assignment |
| 11 | **Flexible Scheme Type** | 0=Fixed, 1=Standard, 2+=Weight-convert | `scheme.flexible_sch_type` | `$chit['flexible_sch_type']` | Weight conversion logic |
| 12 | **Search By Acc No** | 1=AccNo, 2=Mobile, 3=Both | `config: searchbyaccno` | `$searchbyaccno` | Customer search in collection app |

---

## 2. Behavior Grids

### Grid: Scheme Type × GST Type (Metal Weight Calculation)

| | **GST Inclusive (0)** | **GST Exclusive (1)** | **No GST (gst=0)** |
|---|---|---|---|
| **Gold / Type 1** | `wgt = (amt×100/(100+gst%) + disc) / rate` | `wgt = amt / rate` | `wgt = udf2` (from app) |
| **Amount / Type 2** | `wgt = amount_to_weight(actamt - gstAmt)` | `wgt = amount_to_weight(actamt)` | `wgt = amount_to_weight(actamt)` |
| **Flexible / Type 3 (flex≥2)** | Same as Type 1 | Same as Type 1 | `wgt = udf2 or 0` |
| **Flexible / Type 3 (flex<2)** | `wgt = 0` | `wgt = 0` | `wgt = 0` |
| **One-time (wgt_convert=0)** | `wgt = 0` | `wgt = 0` | `wgt = 0` |

> ⚠️ **BUG-001**: In `mobile_payment_post()` L1489, `$sch_data['gst_type']` is used instead of `$chit['gst_type']`. This means GST Type is read from wrong/undefined variable for ALL collection app payments.

### Grid: Integration Type × Payment Flow (Post-Payment Sync)

| | **Cash (gateway=0)** | **Online (gateway≠0)** |
|---|---|---|
| **None (0)** | No sync | No sync |
| **JIL (1)** | `insert_common_data_jil($id_payment)` | `insert_common_data_jil($id_payment)` |
| **SyncAPI (2)** | `insert_common_data($id_payment)` | `insert_common_data($id_payment)` |
| **SyncAPI-v2 (3)** | `insert_common_data($id_payment)` | `insert_common_data($id_payment)` |
| **Khimji (5)** | `generateTranUniqueId()` pre-payment | `generateTranUniqueId()` pre-payment |

> ⚠️ **BUG-011**: In `easebuzzResponse_post()` L2461, inner condition checks `integrationType==1` before `==2`, reversed from intent.

### Grid: Payment Gateway × Signature Verification

| Gateway | Callback Method | Signature Verified? | Status |
|---|---|---|---|
| **PayU (1)** | `payment_success()` / `successMURL()` | ✅ Hash verified via PayU lib | OK |
| **HDFC/CCAvenue (2)** | `responseURL()` / `mobileResponseURL()` | ✅ AES decrypt verified | OK |
| **TechProcess (3)** | `techProcessResponseURL()` / `techProcessMobileResponseURL()` | ✅ TransactionResponseBean | OK |
| **Cashfree (4)** | `cashfreeresponseURL()` / `cashfreemobile()` | ⚠️ COMMENTED OUT (BUG-013) | ⚠️ **Vulnerable** |
| **Atom (5)** | `atomReturnURL()` | ✅ TransactionResponse bean | OK |
| **Ippo (6)** | `ipporesponseURL()` | ❌ Uses attacker-supplied credentials (BUG-031) | 🔴 **Critical** |
| **RazorPay (7)** | `razorresponseURL()` | ❌ Debug stub: print_r+exit (BUG-032) | 🔴 **Non-functional** |
| **Easebuzz** | `easebuzzResponse_post()` | ✅ Hash verified | OK |

### Grid: Added By × Payment Status on Save

| | **Cash (gateway=0)** | **Online (gateway≠0)** |
|---|---|---|
| **Admin Web (1)** | N/A (admin uses web panel) | `7` (pending) → callback updates |
| **Customer App (2)** | `2` (awaiting approval) | `7` (pending) → callback updates |
| **Collection App (3)** | `1` (SUCCESS — instant) | `7` (pending) → callback updates |

> ⚠️ **Risk**: Cash payments by collection app employees are marked SUCCESS instantly with NO admin approval gate.

### Grid: Scheme Acc No Set × Account Number Generation Timing

| Setting | When Generated | Method |
|---|---|---|
| **0 (Auto on 1st pay)** | After first successful payment | `account_number_generator()` in callback handler |
| **1 (Manual)** | Admin assigns manually | Admin panel (not in this module) |
| **2 (Pre-populated)** | At scheme join time | `createAccount_post()` |
| **3 (Auto-alt)** | After first payment (alt logic) | `account_number_generator()` |

---

## 3. Edge Cases & Special Combinations

### Khimji + Online Payment
- **When**: `integrationType==5` AND `gateway≠0`
- **Expected**: `generateTranUniqueId()` should run pre-payment, sync should happen post-callback
- **Actual**: `generateTranUniqueId()` runs at payment insert time. Post-callback sync relies on `generateAcNoOrReceiptNo()` which also calls Khimji
- **Status**: ⚠️ BUG-030 — `$pay` undefined in error path of `generateTranUniqueId()`

### Wallet-Only Payment (No Gateway)
- **When**: `redeemed_amount == calc_amt` (full wallet redemption)
- **Expected**: Payment marked SUCCESS immediately, no gateway redirect
- **Actual**: ✅ Correct — `payment_status = 1` set at insert time, `submitpay_flag = FALSE`
- **Status**: ✅ Correct

### EMP Login + Agent Customer Sync
- **When**: Employee logs in, then triggers `syncAgentCustomers_post()`
- **Expected**: Should return customers assigned to this employee
- **Actual**: Returns customers by `id_agent` field — wrong for EMP login
- **Status**: ⚠️ Potential issue (depends on data model)

### Split Payment After Success
- **When**: `split_payment($id_payment)` called on a multi-due payment
- **Expected**: Creates N-1 additional payment records with correct dates
- **Actual**: No transaction wrapping (BUG-043), wrong serviceID (BUG-042), hardcoded payment_type (BUG-041)
- **Status**: ⚠️ Multiple bugs

---

## 4. Variant Test Coverage

| Dimension | Total Variants | Tested (Brain) | Untested | Coverage |
|---|---|---|---|---|
| Scheme Type | 3 (+subtypes) | 3 | 0 | 100% |
| GST Type | 3 (incl, excl, none) | 3 | 0 | 100% |
| Integration Type | 5 | 4 | 1 (type 3 same as 2) | 80% |
| Payment Gateway | 8 | 8 | 0 | 100% |
| Auto Pay Approval | 4 | 3 | 1 (type 3) | 75% |
| Login Type | 2 | 2 | 0 | 100% |
| Added By | 3 | 3 | 0 | 100% |
| Due Type | 6 | 6 | 0 | 100% |
| SMS Gateway | 5 | 5 | 0 | 100% |
| **Overall** | **39** | **37** | **2** | **95%** |
