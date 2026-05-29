# Payment Module — Flow Risk Matrix
> **Created**: 2026-03-24 | **Round**: 3 | 🔄 NEW in Upgrade

## 3b-1. State Machine: `payment.payment_status`

| State | Value | Set By (Module.Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Pending | 7 | Mobile/Web gateway callback | Awaiting(2), Success(1), Failure(3), Cancel(4) | — |
| Awaiting | 2 | `update_pay_status` (auto_pay_approval=0) | Success(1), Failure(3) | Admin must manually approve |
| Success | 1 | `payment/SaveAll` (manual), `update_pay_status`, `verify_*` | Awaiting(2) via revert | Manual: direct. Online: after gateway confirm |
| Failure | 3 | `verify_*` gateway methods | Pending(7) via re-verify | ⚠️ NO GUARD on re-verify attempt |
| Cancelled | 4 | `verify_*` gateway methods | — (terminal) | — |
| Refund | 6 | `update_pay_status` | — (terminal) | ⚠️ NO GUARD — no refund precondition check |

### State Machine: `postdate_payment.payment_status`

| State | Value | Set By | Can Transition To | Guard |
|---|---|---|---|---|
| Pending | 7 | `postdate_payment/Save` | Success(1), Failure(3), Cancel(4), Awaiting(2) | — |
| Awaiting | 2 | PDC status update | Success(1), Failure(3) | — |
| Success | 1 | `postdate_payment_form/Update` | — | Triggers payment creation |
| Failure | 3 | `postdate_payment_form/Update` | — | — |
| Cancel | 4 | `postdate_payment_form/Update` | — | — |

---

## 3b-2. Inbound Contracts (What Payment Module Expects from Upstream)

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| **Scheme** | `scheme.status = 1` (active) | PARTIAL — checked in `get_customer_schemes()` via JOIN | Model L1262+ | Inactive scheme still shows in some queries |
| **Scheme** | `scheme.gst`, `scheme.gst_type` valid (0 or 1) | NO explicit check | — | Wrong GST calculation if NULL or unexpected value |
| **Scheme Account** | `scheme_account.id_scheme_account` exists | YES (FK) | — | DB error on save |
| **Scheme Account** | `scheme_account.disable_payment != 1` | YES 🔄 | Model L2297-2302 | Payment blocked correctly. But APN/multi-chance edge cases not validated |
| **Customer** | `customer.id_customer` valid | YES (FK constraint) | — | DB error on save |
| **Customer** | `customer.mobile` exists for SMS | NO check before SMS send | Controller L5043+ | SMS call fails silently |
| **Settings** | `chit_settings` row exists (id=1) | NO — CROSS JOIN assumes existence | Model throughout | Fatal error if table is empty |
| **Metal Rates** | Current metal rate > 0 | NO check in `amount_to_weight()` | Controller L2480 | Division by zero |
| **Gateway** | `gateway.pg_code` matches known code (1,2,3,4,7,8) | NO — no default case | Controller L3788-3803 | Silent no-op for unknown gateway |
| **Branch** | `branch.id_branch` valid | PARTIAL — nullable field | — | NULL branch allowed but may affect reports |
| **Account Model** | `account_model` loaded and available | YES (constructor) | Controller L26-65 | Fatal error if model file missing |
| **Financial Year** | `ret_financial_year.fin_status = 1` exists | NO explicit check | Model L36-39 | NULL receipt_year |

---

## 3b-3. Outbound Contracts (What Payment Module Guarantees to Downstream)

| Downstream Module | What This Module Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| **Reports** | `payment.payment_amount` = sum of `payment_mode_details.payment_amount` (is_active=1) | No server-side validation — relies on correct insertion | ⚠️ CONTRACT GAP — Report shows wrong amount if mode details desync |
| **Reports** | `scheme_account.total_paid_ins` matches actual payment count | Updated via `updData()` after each save | ⚠️ CONTRACT GAP — Delete doesn't update count |
| **Scheme Account** | `scheme_acc_number` assigned after first successful payment | Conditional: only if `schemeacc_no_set=0` | Missing account number if setting changed mid-scheme |
| **Wallet** | Wallet debit matches `payment.redeemed_amount` | `insertWalletTrans()` in same transaction | ⚠️ PARTIAL — wallet trans in same txn but no post-verification |
| **Sync/Integration** | Payment data synced to external systems | `insert_common_data()` / `insert_common_data_jil()` | ⚠️ CONTRACT GAP — Sync failure doesn't rollback payment |
| **SMS/Email** | Notification sent after successful payment | Called after `trans_commit()` | Low risk — SMS failure doesn't affect payment data |
| **Referral** | Referral benefit credited at milestone installment | `insert_referral_data()` after save | ⚠️ No rollback on referral insert failure |
| **Billing/Close** | `payment.receipt_no` unique per scope | Generated via MAX+1, no DB lock | ⚠️ CONTRACT GAP — Duplicate receipts under concurrent access |

---

## 3b-4. Reversal Contracts (Cancel/Delete/Reverse)

### Operation: Delete Payment (`payment/Delete`)

| Tables That Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|
| `payment` → DELETE | ✅ YES (hard delete) | `paymentDB("delete")` | — |
| `payment_mode_details` → DELETE/soft-delete | ❌ NO | — | ⚠️ Orphaned mode details |
| `payment_status` → DELETE | ❌ NO | — | ⚠️ Orphaned status log |
| `ret_advance_utilized` → DELETE | ❌ NO | — | ⚠️ Stale advance utilization |
| `scheme_account.total_paid_ins` → Decrement | ❌ NO | — | ⚠️ Stale installment count |
| `wallet_transaction` → Reverse debit | ❌ NO | — | ⚠️ Wallet balance mismatch |
| `referral_*` → Remove benefit | ❌ NO | — | ⚠️ Unearned referral benefit |
| `receipt_no` → Release/mark voided | ❌ NO | — | ⚠️ Receipt gap in sequence |
| `sync_*` → Delete sync record | ❌ NO | — | ⚠️ External system has orphan |

**DELETE REVERSAL SCORE: 1/9 tables restored = ❌ 11% — CRITICAL GAP**

### Operation: Revert Approval (`revertApproval`)

| Tables That Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|
| `payment.payment_status` → 2 (Awaiting) | ✅ YES | Controller L3686+ | — |
| `scheme_account.total_paid_ins` → Decrement | ✅ YES | Controller L3700+ | — |
| `payment_status` → Log revert | ✅ YES | Controller L3710+ | — |
| `payment.receipt_no` → NULL/void | ❌ NO | — | ⚠️ Receipt number consumed |
| `wallet_transaction` → Reverse debit | ❌ NO | — | ⚠️ Wallet balance mismatch |
| `referral_*` → Remove benefit | ❌ NO | — | ⚠️ Unearned referral benefit |
| `agent_incentive`/`employee_incentive` → Remove | ❌ NO | — | ⚠️ Incentive remains |
| `sync_*` → Revert sync | ❌ NO (Standard) / ✅ YES (JIL) | `revertApproval_jil` | Partial |

**REVERT REVERSAL SCORE: 3/8 = ⚠️ 38% — SIGNIFICANT GAP**

---

## 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-PAY-001 | CREATE payment on inactive scheme | REJECT with clear error | 🔴 HIGH | ❌ |
| FR-PAY-002 | CREATE payment on `disable_payment=1` account | REJECT — `allow_pay='N'` | 🔴 HIGH | ❌ |
| FR-PAY-003 | CREATE payment with `metal_rate=0` on weight scheme | REJECT or handle gracefully | 🔴 HIGH | ❌ |
| FR-PAY-004 | CREATE payment with unknown `pg_code` gateway | Should show clear error | 🔴 HIGH | ❌ |
| FR-PAY-005 | DELETE payment → verify ALL child tables cleaned up | Full cleanup of mode_details, status, wallet, referral | 🔴 HIGH | ❌ |
| FR-PAY-006 | DELETE payment → verify `total_paid_ins` decremented | Installment count updated | 🔴 HIGH | ❌ |
| FR-PAY-007 | REVERT approval → verify receipt, wallet, referral restored | Full restoration | 🔴 HIGH | ❌ |
| FR-PAY-008 | CANCEL → re-create same payment | Should work normally | 🟡 MED | ❌ |
| FR-PAY-009 | Concurrent save creating 2 receipts simultaneously | One succeeds with unique receipt | 🔴 HIGH | ❌ |
| FR-PAY-010 | Save crashes midway (verify `trans_begin/complete` coverage) | No orphan records | 🔴 HIGH | ❌ |
| FR-PAY-011 | PDC conversion to payment — verify all fields transferred | All data including IFSC | 🔴 HIGH | ❌ |
| FR-PAY-012 | Payment with wallet redemption — verify wallet balance after | Balance reduced by exact redeemed_amount | 🟡 MED | ❌ |
| FR-PAY-013 | Online payment callback with expired gateway credentials | Clear error, no partial update | 🟡 MED | ❌ |
| FR-PAY-014 | Payment on quarterly scheme (installment_cycle=3) | Correct due count, correct date matching | 🟡 MED | ❌ |
| FR-PAY-015 | `disable_payment=1` with APN (advance payment) | Unclear behavior — dev flagged | 🟡 MED | ❌ |
| FR-PAY-016 | Edit payment after downstream consumed (scheme closed) | REJECT or warn | 🟡 MED | ❌ |
| FR-PAY-017 | Payment mode details sum ≠ payment_amount | Integrity check should catch | 🟡 MED | ❌ |
