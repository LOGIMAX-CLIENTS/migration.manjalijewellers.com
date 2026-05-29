# Flow Checklist: Chit Adjustment at Estimation

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_estimation.php`
> **Tables:** `scheme_account`, `ret_estimation`

---

## Overview

Chit adjustment at estimation = applying customer's matured chit/scheme balance toward purchase at estimation stage (before billing).

### Flow
```
Customer arrives → Estimation created → Chit balance looked up
→ Chit amount applied as discount/adjustment → Estimation saved
→ On billing: chit is_utilized → utilized_type set → scheme_account updated
```

---

## SAVE Checklist (Chit Adjustment at Estimation)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_estimation.chit_amount` | Set chit applied amount | ⬜ |
| 2 | `ret_estimation.scheme_account_id` | Link to scheme_account | ⬜ |
| 3 | Validation | Chit amount ≤ matured balance | ⬜ **VERIFY server-side** |
| 4 | Validation | Account status = matured/active | ⬜ **VERIFY** |
| 5 | Multiple chits | Can multiple chit accounts be applied to one estimation? | ⬜ **VERIFY** |

## ON BILLING (When estimation converts)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `scheme_account.is_utilized → 1` | Mark account as used | ⬜ |
| 2 | `scheme_account.utilized_type` | Set utilization type | ⬜ |
| 3 | Bill net_amount | Reduced by chit amount | ⬜ |
| 4 | Guard | Cannot utilize already-utilized account | ⬜ **VERIFY** |

## CANCEL REVERSAL

| # | Table | Expected Reversal | Reversed? |
|---|---|---|---|
| 1 | `scheme_account.is_utilized → 0, utilized_type → NULL` | ✅ cancel_bill L7976-7983 |
| 2 | Estimation chit_amount cleared | On bill cancel, estimation unlinked (but chit amount stays in est) | ⬜ **VERIFY** |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| CHIT-EST-001 | Server-side validation: chit amount > available balance not checked? | 🔴 HIGH |
| CHIT-EST-002 | Double utilization: same account applied to multiple estimations before billing | 🟡 MED |
| CHIT-EST-003 | Mismatch between chit amount at estimation vs billing (theniNPR known issue) | 🔴 HIGH |
