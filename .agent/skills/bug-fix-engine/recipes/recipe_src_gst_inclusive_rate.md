# Recipe: GST Rate Calculated as Exclusive Instead of Inclusive (Supplier Rate Cut)

## Metadata
- **Pattern ID**: PAT-CALC-GST-001
- **Severity**: HIGH
- **Modules Affected**: ret_purchase / supplier_rate_cut (Approval to Invoice Conversion)
- **Auto-fixable**: Yes (string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: Business requirement — rate entered by user is inclusive of GST. The base formula in `calculation_supplier_rate_cut()` must back-calculate the taxable amount from the inclusive rate. Any client using this module needs the inclusive formula.

## Created By
- **Developer**: Antigravity AI
- **Client**: amman (erp.sriammanjewellers.in)
- **Date**: 2026-04-24
- **Source Bug ID**: N/A

## Symptom
When a user enters a per-gram rate in the Approval to Invoice Conversion form, the system adds GST **on top** of the rate instead of treating the rate as already containing GST. This causes the total amount to be overcalculated.

**Example (wrong):** Rate = ₹6180, Weight = 10g, GST = 3%
- System calculates: Taxable = 61,800 → GST = 1,854 → Total = **63,654** ❌

**Expected (correct):** Rate = ₹6180 inclusive of 3% GST
- Taxable = 61,800 ÷ 1.03 = **60,000** → GST = 1,800 → Total = **61,800** ✅

## Root Cause
The function `calculation_supplier_rate_cut()` in `ret_purchase_order.js` uses a GST-exclusive formula:

```javascript
var taxable_amount = src_weight * src_rate;               // treats rate as taxable base
var tax_amount     = taxable_amount * tax_percentage / 100; // adds GST on top
var total_amount   = taxable_amount + tax_amount;           // overcalculates
```

The form label also incorrectly reads `Rate(Excl.GST)`, misleading the user.

## Detection
```powershell
# Find the exclusive calculation pattern in JS
Select-String -Path "admin\assets\js\ret_purchase_order.js" -Pattern "taxable_amount.*tax_percentage.*100"

# Find the form label
Select-String -Path "admin\application\views\ret_purchase\amt_weight_conversation\form.php" -Pattern "Excl.GST"
```

## Files
- `admin/assets/js/ret_purchase_order.js` (function `calculation_supplier_rate_cut`)
- `admin/application/views/ret_purchase/amt_weight_conversation/form.php` (rate label)

## Fix

### JS Fix — Before (GST-exclusive, no input guards)
```javascript
else if (src_type == 2) {
    var charges_amount = ($('.charges_amount').val()!='' ? $('.charges_amount').val() :0);
    var src_weight     = ($('.src_weight').val()!='' ? $('.src_weight').val():0);
    var src_rate       = ($('.src_rate').val()!='' ? $('.src_rate').val():0)

    var taxable_amount = parseFloat(parseFloat(src_weight) * parseFloat(src_rate)).toFixed(2);
    taxable_amount     = parseFloat(parseFloat(taxable_amount)+parseFloat(charges_amount)).toFixed(2);
    var tax_amount     = parseFloat(parseFloat(taxable_amount)*parseFloat(tax_percentage)/100).toFixed(2);
    var total_amount   = parseFloat(parseFloat(taxable_amount)+parseFloat(tax_amount)).toFixed(2);
```

### JS Fix — After (GST-inclusive + NaN guards)
```javascript
else if (src_type == 2) {
    // Guard all inputs — prevent NaN from empty or cleared fields
    var charges_amount = parseFloat($('.charges_amount').val()) || 0;
    var src_weight     = parseFloat($('.src_weight').val())     || 0;
    var src_rate       = parseFloat($('.src_rate').val())       || 0;

    // Rate entered by user already includes GST — back-calculate taxable amount
    var gross_amount   = parseFloat((src_weight * src_rate) + charges_amount).toFixed(2);
    var divisor        = (tax_percentage > 0) ? (1 + tax_percentage / 100) : 1;
    var taxable_amount = parseFloat(gross_amount / divisor).toFixed(2);
    var tax_amount     = parseFloat(gross_amount - taxable_amount).toFixed(2);
    var total_amount   = gross_amount; // no addition needed — rate was already inclusive
```

### Form Label Fix — Before
```html
<label type="text">Rate(Excl.GST)</label>
```

### Form Label Fix — After
```html
<label type="text">Rate(Incl.GST)</label>
```

## Verification
1. Enter Pure Weight = `10`, Rate = `6180`, GST = `3%` (convert_to = 1 → Supplier)
2. **Expected results:**
   - Taxable Amount = `6000.00` (= 6180 ÷ 1.03)
   - GST (3%) = `180.00`
   - Net Amount = `6180.00`
3. Enter Rate = `0` or leave blank → verify amounts show `0.00`, no NaN
4. Enter GST = `0%` (convert_to = Stone Supplier → 0.25% effectively 0 if manually tested) → divisor = 1, no errors
5. Verify the printed Job Receipt shows the same breakdown

## Notes
- The `|| 0` NaN guard pattern is the safest way to handle empty `<input type="number">` fields — `parseFloat('')` returns `NaN`, not `0`
- The divisor safety check `(tax_percentage > 0) ? ... : 1` prevents divide-by-zero when tax is 0%
- The `sgst_cost` / `cgst_cost` / `igst_cost` split logic below the main calculation remains unchanged — it correctly uses `tax_amount / 2` which still works with the inclusive formula
- Always update the form label when switching from exclusive to inclusive to avoid confusing users
