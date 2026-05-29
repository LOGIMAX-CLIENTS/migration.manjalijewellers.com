## BIL-INT02 — Credit Due Amount Incorrect in Credit Collection after Sales Return

| Field | Value |
|---|---|
| Severity | P0 — Critical |
| Track | B (Business) |
| Category | Logic |
| Sprint | Sprint 1 |
| Pattern Match | PAT-LOGIC-002 (Return-Omission in Debt Calculation) |
| Module Brain | ✅ Ready |
| Reporter | Junior Developer (Internal) |
| Source | Internal |

### Steps to Reproduce
1. Create a credit bill (e.g., ₹10,000).
2. Process a partial sales return for ₹2,000 against that bill.
3. Navigate to **Credit Collection** (type 8).
4. Enter the bill number to fetch details.
5. Observe the "Credit Due Amount" in the total summary field.

### Expected Behavior
Credit due amount should show **₹8,000** (Total ₹10,000 - Return ₹2,000).

### Actual Behavior
Credit due amount shows **₹10,000** (ignores the return amount).

### Root Cause Analysis

**Model**: `admin/application/models/ret_billing_model.php` -> `getCreditBillDetails()`
The model correctly joins `ret_bill_return_details` aliased as `ret` to fetch `credit_ret_amt` (L5869).

**JS**: `admin/assets/js/ret_billing.js` -> `getCreditBillDetails()`
The JS function receives the data but ignores `credit_ret_amt` in its calculation:
```javascript
// L20397 in ret_billing.js
var blc_amt = parseFloat(bill_details.tot_bill_amount) - 
              (parseFloat(bill_details.tot_amt_received) + 
               parseFloat(bill_details.credit_pay_amount));
```
It fails to subtract `bill_details.credit_ret_amt`.

### Evidence
Reported during validation of BIL-INT01 fix; identified as a separate UI component (Credit Collection) with the same logic error pattern.
