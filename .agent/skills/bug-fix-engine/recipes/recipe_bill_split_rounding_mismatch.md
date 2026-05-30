# Bill Split Rounding Mismatch — Remainder Adjustment

> Sum of split amounts/weights drifts from original estimation total due to cumulative per-row rounding.

## Metadata
- **Pattern ID**: PAT-BILLING-042
- **Severity**: HIGH
- **Modules Affected**: Billing → Bill Split
- **Auto-fixable**: No (logic insertion, not string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core billing JS shared across all clients

## Created By
- **Developer**: Antigravity AI
- **Client**: erp.manepally.com
- **Date**: 2026-05-15
- **Source Bug ID**: N/A

## Symptom
When an estimation is split into multiple bills (Auto or Manual split), the sum of all split "Sales Amount" values differs from the original estimation total by ₹5–₹15. Similarly, the total split L.Wt (Less Weight) has a 0.001g mismatch. This causes accounting mismatches between bill total and split totals.

## Root Cause
In `calculateSaleBillSplitRowTotal()`, each split row **independently recalculates** its taxable amount from its proportional weight. Rounding occurs at 4 intermediate steps:
1. `.toFixed(3)` for weight calculations
2. `.toFixed(2)` for amount calculations  
3. GST tax computation rounding
4. `Math.round()` for final total per row

With N split rows, the cumulative rounding error is N × (up to ₹0.50/row) = ₹2–₹15.

## Detection
```command
grep -n "function calculateSaleBillSplitRowTotal" admin/assets/js/ret_billing.js
```
Then check if there's a "REMAINDER ADJUSTMENT" block after the `$.each` loop. If not, the bug exists.

```command
grep -n "REMAINDER ADJUSTMENT" admin/assets/js/ret_billing.js
```
If no results, the fix is not applied.

## Files
- `admin/assets/js/ret_billing.js` — `calculateSaleBillSplitRowTotal()` function

## Fix

### Location
Find the closing `});` of the `$('#billing_split_sale_details > tbody tr').each(...)` loop inside `calculateSaleBillSplitRowTotal()`, and the footer update lines that follow:

### Before
```javascript
		console.log('------------');
	});
	$('.total_bill_split_pcs').html(parseFloat(total_pcs));
	$('.total_bill_split_gwt').html(parseFloat(total_gwt).toFixed(3));
	$('.total_bill_split_lwt').html(parseFloat(total_lwt).toFixed(3));
	$('.total_bill_split_nwt').html(parseFloat(total_nwt).toFixed(3));
	$('.total_bill_split_amount').html(Math.round(parseFloat(total_amount).toFixed(2)));
```

### After
```javascript
		console.log('------------');
	});

	// ── REMAINDER ADJUSTMENT: ensure split totals match original estimate ──
	// Get the original bill total from the source items table
	var originalBillTotal = 0;
	$('#billing_sale_details > tbody tr').each(function() {
		var origAmt = parseFloat($(this).find('.bill_amount').val());
		if (!isNaN(origAmt)) originalBillTotal += origAmt;
	});
	var roundedOriginalTotal = parseFloat(Math.round(originalBillTotal));

	// Calculate current sum of split Sales Amounts
	var splitSalesTotal = 0;
	$('#billing_split_sale_details > tbody tr').each(function() {
		var salesAmt = parseFloat($(this).find('.total_sales_amount').val());
		if (!isNaN(salesAmt)) splitSalesTotal += salesAmt;
	});

	// Apply remainder to last row if mismatch is within safe range (< 50)
	var salesDiff = parseFloat((roundedOriginalTotal - splitSalesTotal).toFixed(2));
	if (salesDiff !== 0 && Math.abs(salesDiff) < 50) {
		var $lastRow = $('#billing_split_sale_details > tbody tr:last');

		// Adjust Sales Amount on last row
		var lastSalesAmt = parseFloat($lastRow.find('.total_sales_amount').val()) || 0;
		var adjSalesAmt = parseFloat((lastSalesAmt + salesDiff).toFixed(2));
		$lastRow.find('.total_sales_amount').val(adjSalesAmt.toFixed(2));
		$lastRow.find('.total_split_sales_amount').html(adjSalesAmt.toFixed(2));

		// Adjust Amount (bill_amount = sales_amount - purchase_old_metal)
		var lastPurOldMetal = parseFloat($lastRow.find('.purchase_old_metal_amt').html()) || 0;
		var adjBillAmt = parseFloat((adjSalesAmt - lastPurOldMetal).toFixed(2));
		$lastRow.find('.bill_amount').val(adjBillAmt.toFixed(2));
		$lastRow.find('.split_recd_amount').val(adjBillAmt.toFixed(2));

		// Recalculate footer total
		total_amount = 0;
		$('#billing_split_sale_details > tbody tr').each(function() {
			total_amount += parseFloat($(this).find('.bill_amount').val()) || 0;
		});

		console.log('SPLIT REMAINDER ADJ: original=' + roundedOriginalTotal + ' splitSum=' + splitSalesTotal + ' diff=' + salesDiff);
	}
	// ── END REMAINDER ADJUSTMENT ──

	// ── WEIGHT REMAINDER ADJUSTMENT: ensure split weights match original ──
	var originalGwt = 0, originalLwt = 0;
	$('#billing_sale_details > tbody tr').each(function() {
		originalGwt += parseFloat($(this).find('.bill_gross_val').val()) || 0;
		originalLwt += parseFloat($(this).find('.bill_less_val').val()) || 0;
	});
	originalGwt = parseFloat(originalGwt.toFixed(3));
	originalLwt = parseFloat(originalLwt.toFixed(3));

	var gwtDiff = parseFloat((originalGwt - total_gwt).toFixed(3));
	var lwtDiff = parseFloat((originalLwt - total_lwt).toFixed(3));

	if ((gwtDiff !== 0 && Math.abs(gwtDiff) < 1) || (lwtDiff !== 0 && Math.abs(lwtDiff) < 1)) {
		var $lastWtRow = $('#billing_split_sale_details > tbody tr:last');

		if (gwtDiff !== 0 && Math.abs(gwtDiff) < 1) {
			var lastGwt = parseFloat($lastWtRow.find('.bill_gross_val').val()) || 0;
			var adjGwt = parseFloat((lastGwt + gwtDiff).toFixed(3));
			$lastWtRow.find('.bill_gross_val').val(adjGwt.toFixed(3));
			total_gwt = originalGwt;
		}

		if (lwtDiff !== 0 && Math.abs(lwtDiff) < 1) {
			var lastLwt = parseFloat($lastWtRow.find('.bill_less_val').val()) || 0;
			var adjLwt = parseFloat((lastLwt + lwtDiff).toFixed(3));
			$lastWtRow.find('.bill_less_val').val(adjLwt.toFixed(3));
			$lastWtRow.find('.bill_split_lesswt').html(adjLwt.toFixed(3));
			total_lwt = originalLwt;
		}

		// Recalculate last row N.Wt from adjusted G.Wt and L.Wt
		var adjLastGwt = parseFloat($lastWtRow.find('.bill_gross_val').val()) || 0;
		var adjLastLwt = parseFloat($lastWtRow.find('.bill_less_val').val()) || 0;
		var adjLastNwt = parseFloat((adjLastGwt - adjLastLwt).toFixed(3));
		$lastWtRow.find('.bill_net_val').val(adjLastNwt.toFixed(3));
		$lastWtRow.find('.bill_sale_net_wt').html(adjLastNwt.toFixed(3));

		// Recalculate total N.Wt
		total_nwt = 0;
		$('#billing_split_sale_details > tbody tr').each(function() {
			total_nwt += parseFloat($(this).find('.bill_net_val').val()) || 0;
		});

		console.log('SPLIT WEIGHT ADJ: origGwt=' + originalGwt + ' origLwt=' + originalLwt + ' gwtDiff=' + gwtDiff + ' lwtDiff=' + lwtDiff);
	}
	// ── END WEIGHT REMAINDER ADJUSTMENT ──

	$('.total_bill_split_pcs').html(parseFloat(total_pcs));
	$('.total_bill_split_gwt').html(parseFloat(total_gwt).toFixed(3));
	$('.total_bill_split_lwt').html(parseFloat(total_lwt).toFixed(3));
	$('.total_bill_split_nwt').html(parseFloat(total_nwt).toFixed(3));
	$('.total_bill_split_amount').html(Math.round(parseFloat(total_amount).toFixed(2)));
```

## Verification
1. Go to Bill Split → load any estimation → click Split (Auto)
2. Verify: sum of all "Sales Amount" values == Math.round(original bill amount)
3. Verify: footer total matches original
4. Verify: total L.Wt in split rows matches original L.Wt
5. Verify: total G.Wt in split rows matches original G.Wt
6. Check browser console for `SPLIT REMAINDER ADJ` and `SPLIT WEIGHT ADJ` log lines
7. Test with Manual split (e.g., 20,20,20,20,20)
8. Test with different split counts (2, 3, 5, 7 splits)
9. Test Sales & Purchase flow

## Notes
- Safety guards: Amount diff < ₹50, Weight diff < 1g — prevents wild adjustments on bad data
- The adjustment always goes on the **last** split row (standard "remainder allocation" accounting method)
- Existing per-row calculation logic is completely untouched — this is a post-calculation correction only
