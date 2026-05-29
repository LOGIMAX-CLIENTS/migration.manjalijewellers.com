# Flow Checklist: Branch Management

> **Last Updated:** 2026-03-27
> **Controller:** `admin_settings.php` → branch functions
> **Tables:** `branch`, `metal_rate_settings`, `branch_rate`, `employee_settings`

---

## BRANCH CREATE

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `branch` | INSERT (name, code, address, active=1) | ⬜ |
| 2 | `metal_rate_settings` | Branch rate config created? | ⬜ |
| 3 | `branch_rate` | Default rate link | ⬜ |
| 4 | Transaction wrapping | `trans_begin` protects insert? | ❌ MST-BUG-028: missing |

## BRANCH DELETE

| # | Table | Cleaned? | Gap? |
|---|---|---|---|
| 1 | `branch.active → 0` | ✅ Soft delete | — |
| 2 | `metal_rate_settings` | ❌ Not cleaned | Orphan settings |
| 3 | `branch_rate` | ❌ Not cleaned | Orphan rate links |
| 4 | `employee_settings` | ❌ Not cleaned | Orphan employee settings |
| 5 | Active bills/orders check | ❌ Not checked | Can delete branch with active transactions |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| MST-BUG-028 | Branch update missing `trans_begin` — non-atomic | 🟡 MED |
| BRN-001 | Delete doesn't clean 3+ related tables | 🟡 MED |
| BRN-002 | No guard against deleting branch with active transactions | 🔴 HIGH |
