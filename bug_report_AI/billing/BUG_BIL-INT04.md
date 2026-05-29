## BIL-INT04 — Cash Refund Returns Incorrectly Reducing Credit Due Amount

| Field | Value |
|---|---|
| **Severity** | P1 — Major |
| **Track** | B (Business Logic) |
| **Category** | Logic — Wrong calculation, missing business rule |
| **Sprint** | Sprint 1 |
| **Pattern Match** | None (novel variant of BIL-INT01/02) |
| **Module Brain** | ✅ Ready (`knowledge_brain/Billing/`) |
| **Reporter** | Internal |
| **Source** | Internal |

### Steps to Reproduce
1. Create a credit sales bill (is_credit=1) with a due amount (e.g. ₹5000 due)
2. Process a partial sales return for one item on that bill
3. **Scenario A — Cash refund** (make_as_advance=0): refund cash to customer
4. **Scenario B — Keep as advance** (make_as_advance=1): store return amount as advance
5. On the next bill or credit report, search for the original credit bill

### Expected Behavior
- **Scenario A (Cash refund)**: Credit due should remain ₹5000 — money was returned to customer directly, debt unchanged
- **Scenario B (Advance)**: Credit due should reduce by the return amount — shop holds the money on behalf of customer

### Actual Behavior
Both scenarios reduce the credit due amount, even when cash was refunded directly to the customer. This overstates how much has been paid off the credit balance.

### Root Cause
Four `total_returned` / `credit_ret_amt` subqueries — in `getBillData()`, `getCreditBillDetails()` (`ret_billing_model.php`), and `get_credit_pending_details()`, `getcreditBill_history()` (`ret_reports_model.php`) — all sum return amounts via `ret_bill_return_details` **without filtering `make_as_advance = 1`**. This means cash refunds silently reduce the displayed credit due balance.

### Fix Applied
Added `AND rb.make_as_advance = 1` (where `rb` is the return bill joined via `ret_bill_return_details.bill_id`) to all four queries:

| File | Function | Line |
|---|---|---|
| `ret_billing_model.php` | `getBillData()` | ~L5626 |
| `ret_billing_model.php` | `getCreditBillDetails()` | ~L5888 |
| `ret_reports_model.php` | `get_credit_pending_details()` | ~L6893 |
| `ret_reports_model.php` | `getcreditBill_history()` | ~L2147 |

> **Note**: The change in `getcreditBill()` (ret_reports_model.php ~L2063) was **reverted**. The reports-side Credit Pending fix is instead applied inside `get_credit_pending_details()` which powers the Credit Pending report (`admin_ret_reports.php` controller, L3094/L3122).

### Business Rule Violated
> **RULE-BIL-032**: Only sales returns kept as advance (`make_as_advance=1`) should reduce the credit due. Cash refunds (`make_as_advance=0`) leave the credit debt unchanged because the customer already received their money back directly.

### Evidence
Code trace: `ret_bill_return_details.bill_id` → `ret_billing.make_as_advance` (1=advance kept, 0=cash refund). `ret_issue_receipt` row is created with `receipt_type=3` only when a return is kept as advance.
