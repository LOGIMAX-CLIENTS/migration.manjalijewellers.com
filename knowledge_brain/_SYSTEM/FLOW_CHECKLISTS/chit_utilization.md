# Flow Checklist: Chit/Scheme Account → Utilization → Billing

> **Last Updated:** 2026-03-27
> **Source:** Chit Collection FLOW_RISK_MATRIX + Scheme FLOW_RISK_MATRIX + Billing FLOW_RISK_MATRIX
> **Covers:** Full chit lifecycle: scheme join → payment collection → utilization at billing (bill adjustment)

---

## Modules Involved
- **Scheme** — scheme creation, account opening
- **Chit Collection** — monthly payment collection (EMP app, online gateway)
- **Payment** — payment record management
- **Billing** — utilization at sale (advance adjustment from chit balance)
- **Account** — journal entries, wallet transactions

## Tables Touched
`scheme`, `scheme_account`, `payment`, `ret_billing`, `ret_billing_advance`, `ret_billing_payment`, `ret_journal`, `wallet_transaction`, `referral_data`

---

## SAVE Checklist (Scheme Account Creation)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `scheme_account` | INSERT (customer, scheme, start_date, account_number) | ⬜ |
| 2 | `scheme_account` audit fields | Set `scheme_acc_number` based on config (`schemeacc_no_set`) | ⚠️ |

## SAVE Checklist (Monthly Payment)

| # | Table | Expected Action | Status | Gap? |
|---|---|---|---|---|
| 1 | `payment` | INSERT (account_id, amount, payment_date, payment_status, paid_by) | ⬜ | — |
| 2 | `scheme_account` | UPDATE `paid_installments`, `total_amount_paid` | ⬜ | **VERIFY** |
| 3 | `wallet_transaction` | INSERT if wallet payment used | ⚠️ | **BUG**: `$trans_id` undefined → NULL txnid |
| 4 | `referral_data` | INSERT referral credit on correct installment | ⚠️ | **BUG**: Commented out for Easebuzz |

## SAVE Checklist (Utilization at Billing)

| # | Table | Expected Action | Status | Gap? |
|---|---|---|---|---|
| 1 | `ret_billing_advance` | INSERT chit adjustment row (scheme_account_id, amount adjusted) | ⬜ | — |
| 2 | `ret_billing_payment` | INSERT payment type = chit/scheme | ⬜ | — |
| 3 | `scheme_account` | UPDATE utilized amount / status | ⬜ | **VERIFY** |
| 4 | `ret_journal` | INSERT journal entry for chit adjustment | ⬜ | — |

## CANCEL Checklist (Payment Cancel)

| # | Table | Expected Reversal | Status | Gap? |
|---|---|---|---|---|
| 1 | `payment.payment_status` | → 4 (cancelled) or 3 (failed) | ✅ | — |
| 2 | `wallet_transaction` | Reverse wallet debit | ❌ | **BUG**: Wallet points lost on cancel |
| 3 | `scheme_account.paid_installments` | Decrement | ❌ | Not decremented for gateway cancel |
| 4 | Cash payment reversal | No mechanism exists | ❌ | **BUG**: No way to reverse cash collection |

## CANCEL Checklist (Bill Cancel with Chit Adjustment)

| # | Table | Expected Reversal | Status | Gap? |
|---|---|---|---|---|
| 1 | `ret_billing_advance` | Delete/reverse chit adjustment | ⚠️ | Not all advance types reversed |
| 2 | `scheme_account` | Restore utilized amount | ⬜ | **VERIFY** |
| 3 | `ret_billing_payment` | Reverse chit payment row | ✅ | — |
| 4 | `ret_journal` | Reverse chit journal entries | ✅ | — |

## EDIT Checklist

| # | Table | Expected | Status | Gap? |
|---|---|---|---|---|
| 1 | Chit adjustment amount | Update (not duplicate) | ⬜ | **VERIFY** |
| 2 | Previous scheme_account utilized | Restore old, apply new | ⬜ | **VERIFY** |

## PRINT Checklist

| # | Field | Source | Status | Gap? |
|---|---|---|---|---|
| 1 | Chit Adjustment Amount | `ret_billing_advance` or `ret_billing_payment` | ⬜ | **KNOWN BUG**: Mismatch in theniNPR bill_format_2 |
| 2 | Scheme Account Number | `scheme_account.scheme_acc_number` | ⬜ | — |
| 3 | Balance After Utilization | Calculated | ⬜ | **VERIFY**: same formula as save? |

## REPORT Checklist

| # | Report | Source | Status |
|---|---|---|---|
| 1 | Chit Collection Summary | `payment` grouped by status | ⬜ |
| 2 | Scheme Account Ledger | `payment` + `scheme_account` | ⬜ |
| 3 | Utilization Report | `ret_billing_advance` where type=chit | ⬜ |

## Known Bugs Found

| Bug ID | Missing Step | Severity | Source |
|---|---|---|---|
| BUG-007 | Wallet txnid is NULL (`$trans_id` undefined) | 🔴 HIGH | FLOW_RISK_MATRIX |
| BUG-002 | SMS serviceID=7 (failure template) for success | 🔴 HIGH | FLOW_RISK_MATRIX |
| BUG-003 | Khimji sync param self-swap | 🔴 HIGH | FLOW_RISK_MATRIX |
| BUG-013 | Cashfree callback no signature verify | 🔴 HIGH | FLOW_RISK_MATRIX |
| — | Wallet points not reversed on cancel | 🔴 HIGH | FLOW_RISK_MATRIX |
| — | Cash payment reversal not implemented | 🔴 HIGH | FLOW_RISK_MATRIX |
| — | Print: chit adjustment mismatch | 🟡 MED | Live trace |
| BUG-014 | Referral credit commented out for Easebuzz | 🟡 MED | FLOW_RISK_MATRIX |
