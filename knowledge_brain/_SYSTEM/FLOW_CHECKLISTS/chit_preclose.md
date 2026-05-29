# Flow Checklist: Chit Pre-Close (Bill Type 10)

> **Last Updated:** 2026-03-27
> **Source:** Scheme FLOW_RISK_MATRIX + Billing FLOW_RISK_MATRIX
> **Covers:** Early closure of a scheme account via billing (bill_type=10)

---

## Modules Involved
- **Billing** — pre-close bill creation (bill_type=10)
- **Scheme** — scheme_account status update, benefit/penalty calculation
- **Account** — journal entries for pre-close settlement
- **Payment** — payment reconciliation

## Tables Touched
`ret_billing` (type=10), `ret_billing_payment`, `scheme_account`, `ret_journal`, `scheme_benefit_deduct_settings`, `scheme_custom_payable_settings`

---

## SAVE Checklist

| # | Table | Expected Action | Status | Gap? |
|---|---|---|---|---|
| 1 | `ret_billing` | INSERT pre-close bill (bill_type=10, customer, amount) | ⬜ | — |
| 2 | `ret_billing_payment` | INSERT payment rows (cash/card/etc) | ⬜ | — |
| 3 | `scheme_account` | UPDATE: `is_closed=1` or equivalent closure flag | ⬜ | **VERIFY** |
| 4 | `scheme_account` | UPDATE: pre-close penalty/benefit applied | ⬜ | **VERIFY**: uses benefit chart vs deduction chart |
| 5 | `ret_journal` | INSERT pre-close journal entries | ⬜ | — |

## CANCEL Checklist

| # | Table | Expected Reversal | Status | Gap? |
|---|---|---|---|---|
| 1 | `ret_billing` | UPDATE `bill_status = 0` | ⬜ | **VERIFY** |
| 2 | `ret_billing_payment` | Reverse payments | ⬜ | **VERIFY** |
| 3 | `scheme_account` | REOPEN: `is_closed = 0`, reverse penalty/benefit | ⬜ | **HIGH RISK**: Does cancel reopen the account? |
| 4 | `ret_journal` | Reverse journal entries | ⬜ | **VERIFY** |

## EDIT Checklist

| # | Table | Expected | Status | Gap? |
|---|---|---|---|---|
| 1 | Pre-close amount | UPDATE (not duplicate) | ⬜ | **VERIFY**: Can pre-close bill be edited? |

## PRINT Checklist

| # | Field | Source | Status | Gap? |
|---|---|---|---|---|
| 1 | Pre-close amount | `ret_billing.tot_bill_amount` | ⬜ | — |
| 2 | Penalty/Benefit deducted | Calculated from `scheme_benefit_deduct_settings` | ⬜ | **VERIFY**: same formula as save? |
| 3 | Payment breakdown | `ret_billing_payment` | ⬜ | — |
| 4 | Scheme account details | `scheme_account` | ⬜ | — |

## REPORT Checklist

| # | Report | Source | Status |
|---|---|---|---|
| 1 | Pre-Close Report | `ret_billing` where bill_type=10 | ⬜ |
| 2 | Scheme Closure Summary | `scheme_account` where is_closed=1 | ⬜ |

## Known Bugs Found

| Bug ID | Missing Step | Severity | Source |
|---|---|---|---|
| FR-SCH-001 | `total_installments=0` causes division by zero downstream | 🔴 HIGH | Scheme FLOW_RISK_MATRIX |
| FR-SCH-006 | Delete scheme only cleans 2/11 child tables | 🔴 HIGH | Scheme FLOW_RISK_MATRIX |
| FR-SCH-011 | `apply_benefit_by_chart=1 AND apply_debit_on_preclose=1` undefined behavior | 🔴 HIGH | Scheme FLOW_RISK_MATRIX |
| — | Cancel pre-close: account reopening unverified | 🟡 MED | This audit |
| — | Benefit chart overlapping ranges → wrong calculation | 🟡 MED | Scheme FLOW_RISK_MATRIX |
