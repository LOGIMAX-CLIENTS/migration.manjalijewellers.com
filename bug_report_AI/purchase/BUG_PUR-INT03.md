## PUR-INT03 — Missing CR/DR Indicator on Pure Balance & Amount Balance Labels

| Field | Value |
|---|---|
| Severity | P2 |
| Track | A (System) |
| Category | 7 — Integration/UI |
| Sprint | Sprint 3 |
| Pattern Match | None (novel UX issue) |
| Module Brain | ✅ Ready |
| Reporter | Internal |
| Source | Internal |

### Steps to Reproduce
1. Navigate to **Purchase → Pure Weight to Amount Conversion → Add**
2. Select a Cost Center and Supplier
3. Observe the "Pure Balance" field (left side) and "Amount Balance" field (left side)
4. Also observe "Outstanding Amt(Rs)" and "Outstanding Pure Wt(Grms)" on the right side

### Expected Behavior
- Each balance field should show a **CR** or **DR** indicator next to its label
- **Negative balance** → CR (shown in red/danger color)
- **Positive balance** → DR (shown in green/success color)
- This should apply to: Pure Balance, Amount Balance labels, and the Outstanding sections

### Actual Behavior
- Balance values display as plain numbers with no indication of whether they are CR or DR
- The JS already computes CR/DR and writes to `.weight_bln_type` and `.amt_bln_type` CSS classes, but the corresponding `<span>` elements are **missing from the HTML**
- The CR case for weight balance is **commented out** in JS (line 10405)

### Evidence
- `form.php` lines 464 (Pure Balance) and 482 (Amount Balance) — no CR/DR spans
- `ret_purchase_order.js` line 10421: `.weight_bln_type` set but no matching HTML element
- `ret_purchase_order.js` line 10433: `.amt_bln_type` set but no matching HTML element
- `ret_purchase_order.js` line 10405: CR case commented out

### Root Cause
The HTML form was built without the `<span>` elements that the JS expects to populate with CR/DR text. The `karigarmetalissue` flow (line 10333) correctly shows CR/DR in the Outstanding section, but the `supplier_rate_cut` flow doesn't display it in the balance fields.

### Files Involved
- `admin/application/views/ret_purchase/amt_weight_conversation/form.php`
- `admin/assets/js/ret_purchase_order.js`
