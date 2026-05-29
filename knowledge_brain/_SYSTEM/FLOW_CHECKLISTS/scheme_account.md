# Flow Checklist: Scheme Account (Open / Payment / Close)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_scheme.php` + `admin_payment.php`
> **Tables:** `scheme_account`, `payment`, `wallet_transaction`

---

## ACCOUNT OPEN

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `scheme_account` | INSERT (customer, scheme, start_date, monthly_amount) | ⬜ |
| 2 | Link to customer | `id_customer` reference | ⬜ |
| 3 | Initial payment | First installment via `payment` table? | ⬜ |

## MONTHLY PAYMENT

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `payment` | INSERT (account_id, amount, date, mode) | ⬜ |
| 2 | `scheme_account` counters | installment_count++, total_paid updated | ⬜ |
| 3 | Wallet points | Bonus points credited? | ⬜ |
| 4 | Online payment integration | Cashfree/Razorpay webhook handling | ⬜ |

## ACCOUNT CLOSE / MATURITY

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `scheme_account.status` | Mark as matured/closed | ⬜ |
| 2 | Bonus calculation | Company bonus amount computed | ⬜ |
| 3 | Total maturity value | paid + bonus = maturity_amount | ⬜ |
| 4 | Guard | Cannot close before maturity date? | ⬜ **VERIFY** |

## PRE-CLOSE (before maturity)
→ See `chit_preclose.md` for bill type 10 flow

## CANCEL REVERSAL

| # | Item | Status |
|---|---|---|
| 1 | Payment cancel | Can individual payment be cancelled? | ⬜ **VERIFY** |
| 2 | Account cancel | `scheme_account` → what happens to payments? | ⬜ |
| 3 | Wallet points reversal | Points revoked on cancel | ⬜ **VERIFY** (from chit FLOW_RISK_MATRIX: lost on cancel) |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| SCH-ACC-001 | Wallet points NOT reversed on payment cancel (from FLOW_RISK_MATRIX) | 🔴 HIGH |
| SCH-ACC-002 | Cash reversal mechanism missing for payment cancel | 🔴 HIGH |
| SCH-ACC-003 | 18% delete reversal coverage (from scheme FLOW_RISK_MATRIX) | 🟡 MED |
