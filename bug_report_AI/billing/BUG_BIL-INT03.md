## BIL-INT03 — Credit Status Incorrectly Marked Paid on Partial Return

| Field | Value |
|---|---|
| Severity | P1 |
| Track | B (Business) |
| Category | Logic |
| Sprint | Sprint 1 |
| Pattern Match | None |
| Module Brain | ✅ Ready |
| Reporter | USER |
| Source | Internal |

### Steps to Reproduce
1. Create a credit sale bill.
2. Perform a partial sales return against the credit bill.
3. Check the `ret_billing` table for the reference bill.

### Expected Behavior
For a sales return, if the credit sale item is fully returned, the credit status should be updated to 1 (Paid). If the sales return is partial and there is still a due amount for the current bill, the credit status should be updated to 2 (Pending).

### Actual Behavior
During the `admin_ret_billing.php` save billing function, `credit_status` is updated to 1 unconditionally for sales returns, the same way it correctly does for credit collection (bill type 8). This causes the bill to incorrectly drop off the Credit Pending Details report, which only queries for bills with `credit_status` = 2.

### Evidence
Reported by USER indicating `admin_ret_billing.php` billing function save case credit status is updated as 1.
