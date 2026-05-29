## BIL-INT01 — Credit Due Amount Not Reduced on Second Partial Sales Return

| Field | Value |
|---|---|
| Severity | P0 — Critical |
| Track | B (Business) |
| Category | Logic |
| Sprint | Sprint 1 |
| Pattern Match | None (Novel) |
| Module Brain | ✅ Ready |
| Reporter | Junior Developer (Internal) |
| Source | Internal |
| Status | Fixed ✅ (2026-03-04) |

### Steps to Reproduce
1. Create a sales bill with 3 items, each costing ₹10,000 (total ₹30,000)
2. Mark the bill as full due (`is_credit=1, credit_status=2`)
3. Create a partial sales return for 1 item (₹10,000) — credit due correctly shows ₹30,000
4. Save the first partial return successfully
5. Start a second partial sales return for the same bill
6. Enter the bill number → `search_bill_no()` calls `getBillDetails()` → model `getBillData()`
7. Observe the "Credit Due Amount" field in the summary section

### Expected Behavior
Credit due amount should show **₹20,000** (₹30,000 total − ₹10,000 already returned)

### Actual Behavior
Credit due amount shows **₹30,000** (full amount, not accounting for the previously returned item)

### Root Cause Analysis

**File**: `admin/application/models/ret_billing_model.php`
**Function**: `getBillData()` at L5599-5627

The `due_amount` is computed as:
```
due_amount = (tot_bill_amount - tot_amt_received) - credit_collection
```

For a full-due bill:
- `tot_amt_received = 0` (nothing paid at point of sale)
- `credit_collection` = sum of credit collection payments (from `get_credit_collection_details()`)

**The problem**: Neither `credit_collection` nor the inline formula subtracts the **sum of previously returned item costs**. When a partial return is created, the returned items' total cost should reduce the due amount, but this deduction is absent from `getBillData()`.

### Evidence
The bug was reported by a junior developer during testing of the partial sales return workflow for due bills.
