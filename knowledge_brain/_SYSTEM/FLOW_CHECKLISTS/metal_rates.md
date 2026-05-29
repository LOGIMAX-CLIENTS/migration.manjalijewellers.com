# Flow Checklist: Metal Rates

> **Last Updated:** 2026-03-27
> **Controller:** `admin_settings.php` → metal rate functions
> **Tables:** `metal_rates`, `metal_rate_settings`, `rate.txt`
> **CRITICAL:** Mobile API broken — rate.txt writes "Array" instead of JSON

---

## RATE SAVE

| # | Table/File | Expected Action | Status |
|---|---|---|---|
| 1 | `metal_rates` | INSERT new row (append-only, `max(id)` = current) | ⬜ |
| 2 | `rate.txt` | Write JSON with current rates | ❌ **BROKEN** — MST-BUG-003 writes "Array" |
| 3 | Push notification | `send_RatesToAllUsers()` | ⬜ |
| 4 | Notification bug | Non-branchwise: only last customer notified | ❌ MST-BUG-029 |
| 5 | `$branch_id` undefined | `send_RatesToAllUsers()` when branch_settings=0 | ❌ MST-BUG-044 |

## RATE EDIT (Update Existing)

| # | Table/File | Expected Action | Status |
|---|---|---|---|
| 1 | `metal_rates` | UPDATE existing row | ⬜ |
| 2 | `rate.txt` update | `update_rate_file()` should update JSON | ❌ **Commented out** — MST-BUG-042 |

## BRANCH-WISE RATES

| # | Item | Status |
|---|---|---|
| 1 | `metal_rate_settings` | Branch-specific rate configurations | ⬜ |
| 2 | Default rate fallback | If no branch rate, use default | ⬜ |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| MST-BUG-003 | `rate.txt` writes "Array" string instead of JSON — **mobile API broken** | 🔴 CRITICAL |
| MST-BUG-042 | `update_rate_file()` commented out — rate edit doesn't update file | 🔴 HIGH |
| MST-BUG-029 | Non-branchwise notification: only last customer gets push | 🔴 HIGH |
| MST-BUG-044 | `$branch_id` undefined in non-branchwise rate notification | 🟡 MED |
