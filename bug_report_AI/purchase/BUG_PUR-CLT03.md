## PUR-CLT03 — Incorrect Pure Weight Field Labels in Approval to Invoice Conversion
## PUR-CLT03 — Bill-wise Payment Selected but Total Outstanding Balance Shown

| Field | Value |
|---|---|
| Severity | P1 |
| Track | A (System) |
| Category | Logic (UI label mismatch) |
| Sprint | Sprint 2 |
| Pattern Match | None |
| Category | Logic |
| Sprint | Sprint 1 |
| Pattern Match | None (Novel) |
| Module Brain | ✅ Ready |
| Reporter | Client |
| Source | Client |

### Steps to Reproduce
1. Navigate to **admin_ret_purchase/supplier_rate_cut/add** (Pure Weight to Amount Conversion)
2. Select a Supplier and PO No to populate the balance fields
3. Observe the label "Pure Balance" on the left panel (field `#wt_balance`)
4. Observe the header "Outstanding Pure Wt(Grms)" on the right panel (`.availablepurebalance`)

### Expected Behavior
- The left-panel field labelled "Pure Balance" should display as **"Bill Pure WT"**
- The right-panel header labelled "Outstanding Pure Wt(Grms)" should display as **"Overall Pure Weight"**

### Actual Behavior
- "Pure Balance Weight" is showing in the place of "Bill Pure WT"
- "Outstanding Pure WT" is showing in the wrong label position

### Evidence
Screenshot attached by client — red arrows pointing to the swapped field labels.

### Root Cause
View file `admin/application/views/ret_purchase/amt_weight_conversation/form.php`:
- **Line 486**: Label reads `Pure Balance` — should be `Bill Pure WT`
- **Line 748**: Label reads `Outstanding Pure Wt(Grms)` — should be `Overall Pure Weight`

This is a view-only label fix. No JS or model changes required.
1. Navigate to Supplier Payment Form (`admin_ret_purchase/supplier_po_payment/add`)
2. Select a Karigar/Supplier from the dropdown
3. The Bill type defaults to "BILL" (value=1)
4. Pending bills appear in `#pending_bills_container` with checkboxes (all checked by default)
5. Uncheck some specific bills (select only a subset of bills)
6. Observe the `#balance_amount` (Net Amount) field — it may still show the total outstanding

### Expected Behavior
- When Bill-wise Payment is selected and specific bills are checked/unchecked, the **Balance (Net Amount)** field should show **only the sum of the selected (checked) bill amounts**
- The `.supplierblc` display should continue to show the total outstanding as reference, but the payment amount fields should reflect only selected bills

### Actual Behavior
- The `#balance_amount` field shows the **Total Outstanding Balance** of the supplier instead of the sum of selected bills
- This happens because `get_all_payment_pending_po_bills()` sets `#balance_amount` to the total outstanding **synchronously** (L82916-82917) before the AJAX response arrives and `calculateSelectedTotal()` can recalculate based on checked bills
- In `get_supplier_pay_details()` (L10457-10462), the balance is initially set to total outstanding regardless of bill selection

### Evidence
User-provided screenshots showing the mismatch between selected bills and balance display.

### Root Cause Analysis

**File**: `admin/assets/js/ret_purchase_order.js`

**Flow**:
1. `get_supplier_pay_details()` (L10261) calls AJAX to `/supplier_po_payment/get_supplier_pay_details`
2. On success (L10329), it calls `get_all_payment_pending_po_bills(data)` at L10331
3. Before that, at L10457-10462 (else branch), it sets:
   - `.balance_amount` → `Math.abs(data.balance_amount)` (total outstanding)
   - `.supplierblc` → total outstanding string (L10488-10492)
4. Inside `get_all_payment_pending_po_bills()` (L82818):
   - **L82916-82917**: Sets `#balance_amount` to `balance.balance_amount` (total outstanding) **synchronously** — this runs BEFORE the AJAX callback
   - **L82903**: Calls `calculateSelectedTotal()` inside the AJAX success — this runs AFTER and should override, but the initial set at the outer function scope (L10461-10462) also sets the value

**The timing issue**: The code in `get_supplier_pay_details()` at L10461-10462 sets `#balance_amount` to total outstanding. Then `get_all_payment_pending_po_bills()` is called. Lines 82916-82921 run synchronously and again set total outstanding. Then the inner AJAX fires, and `calculateSelectedTotal()` at L82903 should correct it. However, `get_supplier_pay_details()` at L10461-10462 runs AFTER `get_all_payment_pending_po_bills()` returns (since that function's inner AJAX is async), so the balance gets overwritten back to total outstanding.

**Primary fix needed**: Remove the `balance_amount` assignment at L10461-10462 in `get_supplier_pay_details()` when in `supplier_po_payment` mode, since `get_all_payment_pending_po_bills()` and `calculateSelectedTotal()` should be the sole authority for setting the balance. Also remove/move the synchronous balance set at L82916-82921 to inside the AJAX success callback.

### Affected Files
| File | Lines | Role |
|---|---|---|
| `admin/assets/js/ret_purchase_order.js` | L10261-10540 | `get_supplier_pay_details()` — sets balance to total outstanding |
| `admin/assets/js/ret_purchase_order.js` | L82818-82922 | `get_all_payment_pending_po_bills()` — synchronous balance set before AJAX |
| `admin/assets/js/ret_purchase_order.js` | L82925-82943 | `calculateSelectedTotal()` — correct logic but overridden by above |
| `admin/application/views/ret_purchase/popayment/form.php` | L434 | `#balance_amount` input field |
