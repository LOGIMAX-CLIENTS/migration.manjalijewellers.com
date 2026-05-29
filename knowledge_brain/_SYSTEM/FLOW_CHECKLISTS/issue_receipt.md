# Flow Checklist: Issue / Receipt (Advance / Borrowing)

> **Last Updated:** 2026-03-27
> **Source:** `admin_ret_billing.php` → `issue()` L5867, `receipt()` L6708
> **Controller:** `admin_ret_billing`
> **Covers:** Issue of advance/borrowing + Receipt of advance/borrowing

---

## Modules Involved
- **Billing** — issue/receipt forms
- **Account** — journal entries for advance/borrowing cycle
- **Customer** — borrower reference
- **Payment** — payment record for receipt

## Tables Touched
`ret_issue_receipt`, `ret_issue_receipt_payment`, `ret_journal`, `customer` (balance ref)

---

## SAVE Checklist (Issue — Type 1)

| # | Table | Expected Action | Controller Line | Status |
|---|---|---|---|---|
| 1 | `ret_issue_receipt` | INSERT (type=1, bill_no, amount, issue_to, issue_type, id_customer) | L5919-5955 | ⬜ |
| 2 | `ret_issue_receipt_payment` | INSERT payment record (cash/card/cheque/NB) | L5959+ | ⬜ |
| 3 | `ret_journal` | INSERT journal entries (debit borrower, credit cash) | After save | ⬜ |
| 4 | Bill number generation | `bill_no_generate()` | L5911 | ⬜ |
| 5 | Day close date | `getBranchDayClosingData()` → bill_date | L5913 | ⬜ |

## SAVE Checklist (Receipt — Type 2)

| # | Table | Expected Action | Controller Line | Status |
|---|---|---|---|---|
| 1 | `ret_issue_receipt` | INSERT (type=2, or receipt against existing issue) | L6708+ | ⬜ |
| 2 | `ret_issue_receipt_payment` | INSERT payment record | L6708+ | ⬜ |
| 3 | `ret_journal` | INSERT journal entries (debit cash, credit borrower) | After save | ⬜ |
| 4 | Issue balance check | Validate receipt amount ≤ outstanding issue amount | — | ⚠️ **VERIFY** |

## CANCEL Checklist

| # | Table | Expected Reversal | Status | Gap? |
|---|---|---|---|---|
| 1 | `ret_issue_receipt.bill_status → 2` | Cancel via `cancel_bill()` step #2 (L7807-7809) | ✅ | — |
| 2 | `ret_journal` reversal | Reverse journal entries | ❌ | **CRITICAL**: Journal NOT reversed (same gap as all bills) |
| 3 | Issue balance restoration | If receipt cancelled: outstanding issue restored | ⬜ | **VERIFY** |

## EDIT Checklist

| # | Item | Status |
|---|---|---|
| 1 | Can issue/receipt be edited? | ⬜ **VERIFY**: likely no edit, only cancel+redo |

## PRINT Checklist

| # | Field | Source | Status |
|---|---|---|---|
| 1 | Issue/Receipt print | `ret_issue_receipt` data | ⬜ |
| 2 | Amount matches saved | Calculate from `ret_issue_receipt_payment` | ⬜ |

## REPORT Checklist

| # | Report | Source |
|---|---|---|
| 1 | Advance Report | `ret_issue_receipt` where type=1 |
| 2 | Receipt Report | `ret_issue_receipt` where type=2 |
| 3 | Day Transactions (includes issue/receipt) | Joins `ret_issue_receipt` |

## Known Bugs Found

| Bug ID | Missing Step | Severity |
|---|---|---|
| — | Journal NOT reversed on cancel (inherits from cancel_bill) | 🔴 CRITICAL |
| — | `$_POST` used instead of `$this->input->post()` in save (L5899) | 🟡 MED |
| — | Note: save case is commented out (L5897) — verify active save location | ⚠️ INVESTIGATE |
