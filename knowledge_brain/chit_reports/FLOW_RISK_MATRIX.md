# FLOW RISK MATRIX — chit_reports

> **Round**: R6-Upgrade | **Date**: 2026-03-25
> **Primary Entity**: None owned — **reports module** reads from `payment`, `scheme_account`, `customer`, `scheme`
> **Write Paths**: 14 confirmed write paths (see SCHEMA_ANALYSIS.md Part D)
> **Note**: Despite being labeled a "reports" module, chit_reports has significant write operations (cancel, edit, KYC, sync)

---

## 1. State Machine

### `payment.payment_status` (Written by cancel_payment)
| State | Value | Set By | Guard | Can Transition To |
|---|---|---|---|---|
| Pending | 0 | Payment module | — | Success(1), Rejected(2) |
| Success | 1 | Payment module | — | Cancelled(4) via admin_reports |
| Rejected | 2 | Payment module | — | — |
| Cancelled | 4 | `cancel_payment()` L665 | ⚠️ No trans_begin/complete | — (terminal from reports) |
| Failure | -1 | Gateway | — | — |

> ⚠️ `cancel_payment()` can only cancel `payment_status=1` but no explicit guard in code checks current status before updating — cancelled payments could be re-cancelled.

### `customer.kyc_status` (Written by update_kyc)
| State | Value | Set By | Guard |
|---|---|---|---|
| Pending | 0 | Default | — |
| Verified | 1 | `update_kyc()` L930 | PARTIAL: only set when `verified_kycs==1` |

### `purch_payment.is_delivered` (Written by purch_delivered)
| State | Value | Set By | Guard |
|---|---|---|---|
| Not Delivered | 0 | Default | — |
| Delivered | 1 | `purch_delivered()` | ✅ OTP verify required |

---

## 2. Inbound Contracts (What Reports Expects from Upstream)

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| **Payment** | `payment.payment_status` ∈ {0, 1, 2, 4, -1} | NO — hardcoded in SQL WHERE | All model methods | Unknown status values silently excluded from reports |
| **Payment** | `payment.payment_amount` > 0 for confirmed payments | NO check | Model methods | SUM returns 0 → misleading report totals |
| **Payment** | `payment.gst_type` ∈ {0, 1} | PARTIAL — `gst_type=0 AND gst_setting=1` tested | Controller L265 | `$pay` uninitialized when condition false (KNOWN BUG) |
| **Account** | `scheme_account.is_closed` ∈ {0, 1} | NO — used in `=0`/`=1` filters | Model methods | NULL values excluded silently |
| **Account** | `scheme_account.active` ∈ {0, 1, 2} | NO — used in `=1` for active | Model methods | Status 2 (under_approval) excluded from reports |
| **Scheme** | `scheme.scheme_type` ∈ {0, 1, 2, 3} | NO — labels hardcoded | Controller | Unknown type gets wrong formula |
| **Settings** | `chit_settings` row must exist | NO — JOIN without null check | All reports | If chit_settings empty, all reports return empty |
| **Settings** | `gst_setting`, `has_lucky_draw` columns exist | NO guard | Model methods | Query fails if columns removed |
| **Branch** | `branch.show_to_all` column exists | NO guard | All branch-filtered | Branch visibility breaks |
| **Customer** | `customer.mobile` unique | NO — checked only in `updateAccountDetails` | L1726 | `cusexist` may match wrong customer |

---

## 3. Outbound Contracts (What Reports Guarantees to Downstream)

| Downstream Module | What Reports Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| **Payment** (via cancel_payment) | `payment.payment_status` set to 4 + audit row in `payment_status_log` | ❌ No transaction wrapping | STATUS updated but audit row may be missing |
| **Khimji ERP** (via syncapi_model) | External system updated when payment cancelled | PARTIAL — only when `integrationType=2` | External system out of sync if API fails (no retry) |
| **Customer** (via update_kyc) | `customer.kyc_status=1` when all KYCs verified | PARTIAL — count-based check | ⚠️ If one KYC update fails, kyc_status may flip to 1 prematurely |
| **Payment** (via updatePaymentDetails) | Payment record updated with admin corrections | ❌ No field validation — raw POST | Arbitrary field corruption possible |
| **Account** (via updateAccountDetails) | Account record updated with admin corrections | PARTIAL — `cusexist` + branch check | Customer swap without Account module validation |
| **Audit** (via log file write) | Admin edits logged to `admin/log/` | ✅ Written before DB update | 🔴 But logs are publicly web-accessible (P0) |

---

## 4. Reversal Contracts (Cancel/Delete/Reverse)

| Operation | Tables That Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| Cancel payment | `payment.payment_status` → 4 | ✅ Updated | `cancel_payment()` L665 | — |
| Cancel payment | `payment_status_log` audit row | ✅ Inserted | `payment_statusDB('insert')` L686 | — |
| Cancel payment | External ERP sync | ✅ If integrationType=2 | `updPayStatusInTrans()` L683 | ⚠️ No retry on API failure |
| Cancel payment | `wallet_transaction` (if benefit was given) | ❌ NOT reversed | — | ⚠️ GAP: Wallet benefit remains for cancelled payment |
| Cancel payment | `scheme_account.total_paid_ins` decrement | ❌ NOT updated | — | ⚠️ GAP: Installment count still includes cancelled |
| Undo edit payment | Previous payment values | ❌ No undo | — | ⚠️ GAP: Only log file preserves old values |
| Undo edit account | Previous account values | ❌ No undo | — | ⚠️ Same — log file only |
| Un-verify KYC | `customer.kyc_status` → 0 | ❌ No un-verify | — | ⚠️ GAP: Once verified, can't un-verify from reports |
| Un-deliver purchase | `purch_payment.is_delivered` → 0 | ❌ No un-deliver | — | ⚠️ Terminal state |

> **Reversal completeness: ~25%** — Only cancel_payment partially handles reversal (audit + status + ERP sync), but misses wallet benefit and installment count.

---

## 5. Data Consistency Risks

### 5a. GST Calculation Consistency

| Scenario | Expected Behavior | Actual Behavior | Risk |
|---|---|---|---|
| `gst_setting=1, gst_type=0` (inclusive) | Deduct GST from display amount | ✅ `$pay = payment_amount - sgst - cgst` | — |
| `gst_setting=1, gst_type=1` (exclusive) | Show full amount | ⚠️ `$pay` never assigned | 🔴 `$pay` uninitialized — possible undefined variable |
| `gst_setting=0` | No GST calc | ⚠️ `$pay` never assigned | Same risk |

### 5b. Branch Filter Consistency

| Context | Filter Source | Consistency |
|---|---|---|
| Page-load reports | `session('id_branch')` | ✅ Consistent within session |
| AJAX data calls | `$_POST['id_branch']` | ⚠️ POST can differ from session |
| Export to Excel | Same controller method | ✅ Uses same filter |

### 5c. Cross-Query Balance Formula Risk

| Scenario | Formula | Risk |
|---|---|---|
| Scheme summary | `balance = (oldcoll - oldclosed) + newcoll - newclosed` | 4 separate SQL queries → timing gap between queries can cause slight imbalance |
| Outstanding report | Derived differently (single query) | May not match scheme summary balance |

---

## 6. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-RPT-001 | Cancel payment → verify `payment_status_log` row exists | Audit row with old and new status | 🔴 HIGH | ❌ |
| FR-RPT-002 | Cancel payment with `integrationType=2` → verify ERP sync | External system acknowledges cancellation | 🔴 HIGH | ❌ |
| FR-RPT-003 | Cancel payment → verify wallet benefit NOT reversed | Wallet balance unchanged (known gap) | 🔴 HIGH | ❌ — Known gap |
| FR-RPT-004 | Cancel payment → verify `total_paid_ins` NOT decremented | Installment count still includes cancelled (known gap) | 🔴 HIGH | ❌ — Known gap |
| FR-RPT-005 | Edit payment via admin → verify old values in log file | `admin/log/payment{date}.txt` contains previous values | 🟡 MED | ❌ |
| FR-RPT-006 | Edit account → change customer mobile → verify `cusexist` | Should reject if mobile already exists for another customer | 🔴 HIGH | ❌ |
| FR-RPT-007 | KYC approve → partial failure mid-loop | Some KYCs approved, others not → `kyc_status` should NOT be 1 | 🔴 HIGH | ❌ — No transaction |
| FR-RPT-008 | Payment report with `gst_setting=0` | `$pay` should default to `payment_amount` (not undefined) | 🔴 HIGH | ❌ — Known bug |
| FR-RPT-009 | Celeb dates with Dec→Jan range | Should find all customers across year boundary | 🟡 MED | ❌ — Known cross-year bug |
| FR-RPT-010 | Scheme summary → compare balance with outstanding report | Totals should match | 🟡 MED | ❌ |
| FR-RPT-011 | Source-wise report with `$offline=[]` (no offline payments) | Should show zero, not PHP warning | 🟡 MED | ❌ — Known uninitialized |
| FR-RPT-012 | Collection report with `$today` not initialized | Should not crash on empty results | 🔴 HIGH | ❌ — Known bug |
| FR-RPT-013 | Export outstanding report via direct GET URL | Should require authentication | 🟡 MED | ❌ |
| FR-RPT-014 | SQL injection via `admin_report_model::get_customerenquiry_by_date` | Should sanitize `$status` and `$type` params | 🔴 CRITICAL | ❌ — Known SQLi |
| FR-RPT-015 | `admin/log/` directory → access via browser without auth | Should return 403 Forbidden | 🔴 CRITICAL | ❌ — Known P0 |
| FR-RPT-016 | Deliver purchase → click deliver again | Should warn "already delivered" | 🟡 MED | ❌ |
| FR-RPT-017 | `update_cusdatas` → verify customer_reg data integrity | All sync fields must match source | 🔴 HIGH | ❌ |
| FR-RPT-018 | `closedaccount_list` → verify correct model used | Should use payment_model consistently (not ACC→PAY swap) | 🟡 LOW | ❌ — Known dead assignment |
| FR-RPT-019 | Cancel payment loop → second payment fails | First should NOT be committed without rollback | 🔴 HIGH | ❌ — No transaction |
| FR-RPT-020 | `generateTransUniqId` → call twice for same payment | Should be idempotent (return same ID) | 🟡 MED | ❌ |
