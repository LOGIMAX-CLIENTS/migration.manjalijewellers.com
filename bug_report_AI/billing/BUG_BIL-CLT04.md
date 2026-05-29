## BIL-CLT04 — Order Advance Date Showing as Delivery Date Instead of Actual Payment Date

| Field         | Value                         |
| ------------- | ----------------------------- |
| Severity      | P1                            |
| Track         | B (Business)                  |
| Category      | Logic                         |
| Sprint        | Sprint 2                      |
| Pattern Match | None (Novel)                  |
| Module Brain  | ✅ Ready                      |
| Reporter      | Client                        |
| Source         | Client                        |

⛔ DANGER ZONE: Payment / financial data display
→ AI confidence: automatically LOW
→ Require senior developer review before applying any fix
→ Do NOT accept AI's first suggestion without verification

### Steps to Reproduce

1. Navigate to the Billing module
2. Open an order delivery bill that has an order advance payment
3. View or print the delivery bill
4. Observe the advance payment date displayed on the bill

### Expected Behavior

The order advance payment date should display the **actual date on which the customer paid the advance**, reflecting the correct payment history in both the order details and bill printout.

### Actual Behavior

The order advance payment date shows the **delivery date** instead of the actual advance payment date. This causes incorrect payment history on the order delivery bill.

### Evidence

Reported by client. No screenshot provided yet — request from reporter pending.

### Technical Notes

**Relevant Code Locations (from Module Brain + grep):**
- `ret_reports_model.php` L31171 — `order_advance_details` array in delivery bill data
- `ret_reports_model.php` L31414 — order advance detail population
- `ret_billing_model.php` L2215 — `get_order_advance($order_no)`
- `ret_billing_model.php` L11510 — `get_order_advance_details($bill_adv_id)`
- `ret_reports_model.php` L5588 — `get_order_advance($post)`
- Print templates in `admin/application/views/billing/print/`

**Likely Root Cause**: The query or code that populates the advance date in the delivery bill is using `bill_date` (which is the delivery bill date) or `delivery_date` instead of the original advance payment date from `ret_billing` where `bill_type = 5` (Order Advance). Need to trace which date field is being selected and displayed.

**Cross-Module Risk**: `ret_billing` is a shared table (Billing module owned). The fix should only affect the read path (report/print query), not the write path.

### Fix Approach (Pending Human Validation — Track B)

1. Trace the exact SQL query that populates `order_advance_details` in the delivery bill data
2. Identify which date column is being used (likely `bill_date` of the delivery bill instead of the original advance bill's `bill_date`)
3. Replace with the correct date from the original order advance bill (`ret_billing.bill_date WHERE bill_type = 5`)
4. Verify in print template that the correct date variable is rendered
