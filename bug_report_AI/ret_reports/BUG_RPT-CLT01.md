## RPT-CLT01 — Employee Filter Not Working in Cash Abstract Report

| Field         | Value                         |
| ------------- | ----------------------------- |
| Severity      | P1                            |
| Track         | A (System)                    |
| Category      | Logic                         |
| Sprint        | Sprint 1                      |
| Pattern Match | None                          |
| Module Brain  | ✅ Ready                      |
| Reporter      | User                          |
| Source         | Client                        |

### Steps to Reproduce

1. Navigate to `admin_ret_reports/cash_abstract/list`
2. Select a branch (if HO user)
3. Select an employee from the Employee dropdown
4. Select a date range
5. Click Search

### Expected Behavior

Cash Abstract report should filter all data (Sales, Purchase, Cash/Card/UPI payments, etc.) by the selected employee.

### Actual Behavior

Employee dropdown may remain empty (for HO users after changing branch). The filter appears non-functional because:
1. `get_employee("cash_abs")` is called on page load but NOT re-called when branch changes
2. For HO users (`id_branch == 0`), the initial call is blocked by `if (id_branch > 0)` guard

### Root Cause

**JavaScript layer** — missing `#branch_select` change handler in the `cash_abstract` init case.

The PHP model (`getBillDetails()`) correctly applies `$emp_ids` to all 20+ sub-queries.

### Evidence

- JS L503: `get_employee("cash_abs")` called on init only
- JS L38276: `if (id_branch > 0)` guard in `get_employee()`
- No `#branch_select` change handler in `cash_abstract` case (L468–508)
- Contrast with `old_metal_purchase` case (L320–324) which HAS a change handler
