## PUR-CLT01 — Purchase dashboard Today PO Pending not showing

| Field         | Value                         |
| ------------- | ----------------------------- |
| Severity      | P1                            |
| Track         | A (System)                    |
| Category      | Logic / Database              |
| Sprint        | Sprint 1                      |
| Pattern Match | PAT-LOGIC-003 (Overly Restrictive Filter) |
| Module Brain  | ✅ Ready                      |
| Reporter      | ruthramoorthi                 |
| Source        | Client                        |

### Steps to Reproduce

1. Open the Purchase Dashboard.
2. Check "TODAY PAYABLE PO LIST" for POs due today (e.g., PO-TEST-005).
3. The table is empty despite POs having a delivery date of today.

### Expected Behavior

Purchase orders with `po_delivery_date = CURRENT_DATE()` should appear in the table.

### Actual Behavior

The table was empty.

### Evidence

[Zoho Task](https://connect.zoho.in/portal/logimax-clients/task/124744000005879120)

### Root Cause Analysis

The query in `ret_dashboard_api_model.php::get_today_delivery_po_payments` had two issues:
1. `AND po.pur_approval_type = 1`: This filter excluded any PO that didn't have this specific approval type.
2. `LEFT JOIN ret_category cat ON ...`: If a PO has `id_category = NULL`, any subsequent metal filter would fail to match, even if the user selected "All Metals".

### Fix Applied

Updated `admin/application/models/ret_dashboard_api_model.php`:
1. Removed `and po.pur_approval_type = 1` from the `WHERE` clause.
2. (Manual Edit by User) Removed unnecessary debug print statements.
