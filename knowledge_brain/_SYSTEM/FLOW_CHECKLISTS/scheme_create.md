# Flow Checklist: Scheme Create / Edit / Delete

> **Last Updated:** 2026-03-27
> **Controller:** `admin_scheme.php`
> **Tables:** `chit_schemes`, `chit_settings`

---

## SCHEME CREATE

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `chit_schemes` | INSERT (name, duration, amount, start_date, end_date, interest_rate) | ⬜ |
| 2 | `chit_settings` | Validate scheme settings match global chit config | ⬜ |
| 3 | Uniqueness | Scheme code/name unique? | ⬜ **VERIFY** |
| 4 | Date validation | end_date > start_date server-side | ⬜ |

## SCHEME EDIT

| # | Item | Status |
|---|---|---|
| 1 | Guard: active accounts exist → block certain edits | ⬜ **VERIFY** |
| 2 | Duration change impact on existing accounts | ⬜ |
| 3 | Amount change impact | ⬜ |

## SCHEME DELETE

| # | Table | Expected Reversal | Status |
|---|---|---|---|
| 1 | `chit_schemes.active → 0` (soft) or hard delete? | ⬜ **VERIFY** |
| 2 | Guard: accounts exist → block delete | ⬜ **VERIFY** |
| 3 | Related `scheme_account` records | Orphaned if hard delete | ⬜ |

## Known Risks

| Risk | Severity |
|---|---|
| Delete without checking active accounts | 🟡 MED |
| Division-by-zero risk in maturity calculation (from FLOW_RISK_MATRIX) | 🔴 HIGH |
