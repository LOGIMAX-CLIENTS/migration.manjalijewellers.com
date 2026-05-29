# Account Module — Flow Risk Matrix
> **Created**: 2026-03-24 | **Round**: 2 | 🔄 NEW in Upgrade

---

## 3b-1. State Machines

### State Machine: `scheme_account.is_closed`

| State | Value | Set By (Module.Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Open | 0 | Account.account_post('Add') | Closed(1) | — |
| Closed | 1 | Account.close_account_form('Save') | Open(0) via Revert | OTP required for close |
| Reverted | 0 | Account.close_account_form('Revert') | Closed(1) | ⚠️ DigiGold duplicate check only |

### State Machine: `scheme_account.active`

| State | Value | Set By | Can Transition To | Guard |
|---|---|---|---|---|
| Active | 1 | account_post('Add') | Inactive(0) | — |
| Inactive | 0 | close_account_form('Save') | Active(1) | Revert only |
| Blocked | — | blk_payment_byid() sets `blk_payment=1` | Unblocked(0) | No guard — any employee |

### State Machine: `gift_issued.status`

| State | Value | Set By | Can Transition To | Guard |
|---|---|---|---|---|
| Issued | 1 | save_giftissued() | Deducted(2) | — |
| Deducted | 2 | cancel_giftissued() | — (terminal) | — |
| Pending | 0 | insert_gift_issued() on account add | Issued(1) | ⚠️ NO GUARD |

### State Machine: `reg_request.status`

| State | Value | Set By | Can Transition To | Guard |
|---|---|---|---|---|
| Pending | 0 | Mobile API (external) | Approved(1), Rejected(2) | — |
| Approved | 1 | update_request() | Reverted(2) | ⚠️ Only checks isPaymentExist() |
| Rejected | 2 | update_request() | — (terminal) | — |

---

## 3b-2. Inbound Contracts (What Account Module Expects from Upstream)

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| **Scheme** | `scheme.id_scheme` valid and active | PARTIAL — checked only `id_scheme > 0` | Controller L424 | Account created for inactive scheme |
| **Scheme** | `scheme.total_installments > 0` for fixed schemes | NO check | — | ⚠️ Division by zero in installment calculations |
| **Scheme** | `scheme.amount > 0` for amount-based schemes | NO check | — | ⚠️ Zero-amount account created |
| **Customer** | `customer.id_customer` valid | YES — `id_customer > 0` | Controller L424 | DB error on insert |
| **Customer** | `customer.mob_no` valid for SMS | NO check | — | SMS fails silently |
| **Settings** | `chit_settings` row exists | NO check — assumed | Throughout | Fatal error |
| **Settings** | `ret_financial_year` exists for current year | NO check | Model L370+ | NULL in account number |
| **Branch** | `branch.id_branch` valid when branch_settings=1 | PARTIAL — conditional check | Controller L405+ | NULL branch on account |
| **Metal** | Metal rate > 0 for weight schemes | NO check | Controller L1500+ | ⚠️ Division by zero in weight calculations |
| **Payment** | `payment_model.get_metalrate_by_branch()` returns rate | NO validation | Controller L1508 | NULL rate → wrong closing calc |

---

## 3b-3. Outbound Contracts (What Account Module Guarantees to Downstream)

| Downstream Module | What This Module Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| **Payment** | `scheme_account.id_scheme_account` is unique | DB auto-increment | ✅ Handled |
| **Payment** | Account is active (`active=1, is_closed=0`) before payment | NO check in Account module — Payment module checks | ⚠️ Payment module must enforce |
| **Payment** | `scheme_account.schemeaccNo` is valid format | PARTIAL — `account_number_generator()` 7 modes | ⚠️ Edge cases in mode combinations |
| **Reports** | `closing_balance` and `closing_amount` are accurate | `formatMetalWeight()` (🔄 R2) centralizes formatting | ✅ Improved in R2 |
| **Reports** | `closing_weight` matches `closing_balance` for weight schemes | NO cross-validation | ⚠️ Could diverge |
| **Mobile API** | `scheme_account` fields are complete | NO completeness check | ⚠️ NULLs propagate |
| **Wallet** | Referral wallet transactions are balanced (credit = debit on close) | PARTIAL — close logic debits but doesn't verify balance | ⚠️ Wallet imbalance possible |
| **Gift Inventory** | `gift_issued` count matches inventory deductions | PARTIAL — insert_gift_issued + update inventory | ⚠️ No reconciliation check |

---

## 3b-4. Reversal Contracts (Cancel/Delete/Reverse)

### Operation: Delete Account (`account_post('Delete')`)

| Tables Written During CREATE | Restored During DELETE? | Method & Line | Gap? |
|---|---|---|---|
| `scheme_account` → INSERT | ✅ YES (hard delete) | account_model.delete_account() | — |
| `customer_kyc` → INSERT | ❌ NO | — | ⚠️ Orphaned KYC records |
| `gift_issued` → INSERT | ❌ NO | — | ⚠️ Orphaned gift records |
| `gift_card` → INSERT | ❌ NO | — | ⚠️ Orphaned voucher |
| `payment` → INSERT (free payment) | ❌ NO | — | ⚠️ Orphaned free payment |
| `wallet_transaction` → INSERT (referral) | ❌ NO | — | ⚠️ Orphaned wallet txn |
| `loyalty_transaction` → INSERT (agent) | ❌ NO | — | ⚠️ Orphaned loyalty txn |
| `ret_other_inventory_*` → UPDATE | ❌ NO | — | ⚠️ Inventory not reverted |
| `log_detail` → INSERT | ❌ NO | — | OK — logs should persist |

**DELETE REVERSAL SCORE: 1/8 tables restored = ❌ 12.5% — CRITICAL GAP**

### Operation: Revert Closed Account (`close_account_form('Revert')`)

| Tables Written During CLOSE | Restored During REVERT? | Method & Line | Gap? |
|---|---|---|---|
| `scheme_account.is_closed=1` | ✅ YES → is_closed=0, active=1 | Controller L1844+ | — |
| `wallet_transaction` (emp incentive credit) | ✅ YES (debit entry) | Controller L1860+ | — |
| `wallet_transaction` (ref deduction debits) | ❌ NO | — | ⚠️ Referral deductions not reversed |
| `loyalty_transaction` (agent debit) | ❌ NO | — | ⚠️ Agent loyalty not reversed |

**REVERT REVERSAL SCORE: 2/4 = 50% — PARTIAL**

---

## 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-ACC-001 | CREATE account with inactive scheme (`active=0`) | REJECT | 🔴 HIGH | ❌ |
| FR-ACC-002 | CREATE account with `total_installments=0` scheme | REJECT or handle | 🔴 HIGH | ❌ |
| FR-ACC-003 | CREATE account with zero-amount scheme | REJECT or handle | 🔴 HIGH | ❌ |
| FR-ACC-004 | DELETE account → verify ALL child tables cleaned | Full cleanup (currently 1/8) | 🔴 HIGH | ❌ |
| FR-ACC-005 | CLOSE account → REVERT → verify referral deductions reversed | Full reversal | 🔴 HIGH | ❌ |
| FR-ACC-006 | CLOSE weight scheme → verify `closing_balance` uses `formatMetalWeight()` | Consistent formatting | 🟡 MED | ✅ (R2) |
| FR-ACC-007 | CREATE with free payment → DELETE account | Free payment should be cleaned up | 🔴 HIGH | ❌ |
| FR-ACC-008 | OTP for closing → verify OTP NOT in response JSON | No OTP exposure | 🔴 HIGH | ❌ |
| FR-ACC-009 | CLOSE with `metal_rate=0` for weight scheme | REJECT (division by zero risk) | 🔴 HIGH | ❌ |
| FR-ACC-010 | REVERT already-reverted account | Should not double-revert | 🟡 MED | ❌ |
| FR-ACC-011 | Concurrent close by 2 users on same account | One succeeds, one fails | 🟡 MED | ❌ |
| FR-ACC-012 | CLOSE crashes after `is_closed=1` but before wallet txns | `trans_begin` should rollback | 🔴 HIGH | ❌ |
| FR-ACC-013 | Approve reg_request, then revert when payment exists | Should block revert | 🟡 MED | ❌ |
| FR-ACC-014 | CREATE account → verify `schemeaccNo` generates in correct format | Valid format per mode | 🟡 MED | ❌ |
| FR-ACC-015 | Gift issued → Cancel → verify inventory restored | Inventory item status=0 | 🟡 MED | ❌ |
| FR-ACC-016 | CLOSE with DigiGold benefit → verify NPR calculation | Correct benefit amount | 🟡 MED | ❌ |
| FR-ACC-017 | 🔄 R2: Closed account list → verify `closing_weight` formatted by `formatMetalWeight()` | Correct decimal places | ✅ LOW | ✅ |
| FR-ACC-018 | 🔄 R2: Passbook back print → verify weight formatted by `formatMetalWeight()` | Correct on printout | ✅ LOW | ✅ |
