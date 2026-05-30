# Chit Utilization Amount Not Saved With Wastage/MC Savings

## Metadata
- **Pattern ID**: PAT-BILL-048
- **Severity**: HIGH
- **Modules Affected**: Billing (ret_billing), Print (receipt_billing)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core billing JS shared across all clients with weight scheme chit utilization

## Created By
- **Developer**: Antigravity AI
- **Client**: karpagamjewels.com
- **Date**: 2026-04-20
- **Source Bug ID**: N/A

## Symptom
On billing invoice print, the "Net Amount" and "Payment TOTAL" don't match. The CHIT ADJ amount on the print is less than what was shown in the Total Summary tab during billing. The gap equals the wastage/MC savings benefit amount.

Example: Total Summary shows Chit Paid Amount = 36,813 but print shows CHIT ADJ = 36,365 (gap = 448).

## Root Cause
**Timing bug**: In `ret_billing.js`, the `#chit_details` hidden field (`name="billing[chit_uti]"`) is populated with raw `utl_amount` (= `closing_amount` from scheme_account) BEFORE the chit savings recalculation functions run. These functions recalculate the chit amount to include wastage weight savings and making charge savings, updating the UI display — but never update the `#chit_details` hidden field. When the form is submitted, the server saves the raw amount instead of the recalculated amount.

**Two code paths affected:**
1. **Estimation load path**: `$('#chit_details').val(...)` is set at the estimation AJAX callback, then `calculate_est_chit_closing_balance()` runs 1 second later via `setTimeout` but never updates the hidden field.
2. **Manual chit add path**: `#add_newchit_util` click handler builds `chit_details` JSON from modal, sets `#chit_details`, then calls `calculate_chit_closing_balance()` which updates DOM `.chit_amt` but never updates the hidden field.

## Detection
```command
grep -n "calculate_est_chit_closing_balance\|calculate_chit_closing_balance" admin/assets/js/ret_billing.js
```
Check if `$('#chit_details').val(...)` is called AFTER `calculate_est_chit_closing_balance()` or `calculate_chit_closing_balance()`.

Also check print view for undefined variables:
```command
grep -n "round_off_amt\|has_sales\|has_purchase\|has_return" admin/application/views/billing/print/receipt_billing.php | head -10
```

## Files
- `admin/assets/js/ret_billing.js`
- `admin/application/views/billing/print/receipt_billing.php`

## Fix

### Fix 1: Estimation load path — `calculate_est_chit_closing_balance()` in ret_billing.js

#### Before
```javascript
		chit_amount = val.closing_amount;

		}

		total_chit_amount += parseFloat(chit_amount);
```

#### After
```javascript
		chit_amount = val.closing_amount;

		}

		// Update the chit_details array item with recalculated amount
		val.utl_amount = parseFloat(Math.round(chit_amount)).toFixed(2);

		total_chit_amount += parseFloat(chit_amount);
```

And at the end of `calculate_est_chit_closing_balance()`, before `calculateFinalCost()`:

#### Before
```javascript
	$('.summary_chit_paid_wt').html(parseFloat(total_chit_wt).toFixed(3))

	calculateFinalCost();
```

#### After
```javascript
	$('.summary_chit_paid_wt').html(parseFloat(total_chit_wt).toFixed(3))

	// Write recalculated chit_details back to hidden field so POST data is correct
	$('#chit_details').val(chit_details.length > 0 ? JSON.stringify(chit_details) : '');

	calculateFinalCost();
```

### Fix 2: Manual add path — after `calculate_chit_closing_balance()` call in `#add_newchit_util` handler

#### Before
```javascript
        calculate_chit_closing_balance();

    }
```

#### After
```javascript
        calculate_chit_closing_balance();

        // Rebuild chit_details JSON with recalculated amounts (including wastage/MC savings)
        chit_details = [];
        var total_amount_recalc = 0;
        $('#estimation_chit_details > tbody > tr').each(function(index, tr) {
            if($(this).find('.chit_amt').val() != "") {
                total_amount_recalc += parseFloat($(this).find('.chit_amt').val());
                chit_details.push({
                    'scheme_account_id'          : $(this).find('.scheme_account_id').val(),
                    'utl_amount'                 : $(this).find('.chit_amt').val(),
                    'scheme_type'                : $(this).find('.scheme_type').val(),
                    'closing_amount'             : $(this).find('.closing_amount').val(),
                    'closing_weight'             : $(this).find('.closing_weight').val(),
                    'wastage_per'                : $(this).find('.wastage_per').val(),
                    'savings_in_wastage'         : $(this).find('.savings_in_wastage').val(),
                    'mc_value'                   : $(this).find('.mc_value').val(),
                    'savings_in_making_charge'   : $(this).find('.savings_in_mcvalue').val(),
                    'is_wast_and_mc_benefit_apply': $(this).find('.is_wast_and_mc_benefit_apply').val(),
                    'rate_per_gram'              : $(this).find('.rate_per_gram').val(),
                });
            }
        });
        $('.summary_chit_paid_amt').html(parseFloat(total_amount_recalc).toFixed(2));
        $('#payment_modes > tbody > tr').each(function(bidx, brow){
            $(this).find('#chit_details').val(chit_details.length > 0 ? JSON.stringify(chit_details) : '');
        });

    }
```

### Fix 3: Undefined variables in receipt_billing.php

Add after the existing variable definitions (around line 183):

```php
$round_off_amt = isset($billing['round_off_amt']) ? $billing['round_off_amt'] : 0;
$has_sales = sizeof($item_details) > 0 ? 1 : 0;
$has_purchase = sizeof($old_matel_details) > 0 ? 1 : 0;
$has_return = sizeof($return_details) > 0 ? 1 : 0;
```

## Verification
1. Create a Sales & Purchase bill with chit utilization (weight scheme with wastage savings)
2. In Total Summary, note the "Chit Paid Amount"
3. Save the bill
4. Check DB: `SELECT utilized_amt FROM ret_billing_chit_utilization WHERE bill_id = <new_bill>` — should match Total Summary
5. Open print: CHIT ADJ should match, Net Amount and TOTAL should reconcile
6. Test with a chit that has NO savings (scheme_type = amount) — should still work correctly

## Notes
- Only affects bills with **weight-based scheme** chit utilization where wastage/MC savings are calculated
- Amount-based schemes (closing_amount only) are unaffected since savings = 0
- Existing old bills with wrong `utilized_amt` are NOT auto-corrected — would need a DB migration script
- The `calculate_est_chit_closing_balance()` function (line ~16478) operates on the JS array; `calculate_chit_closing_balance()` (line ~16014) operates on DOM table rows — both paths need separate fixes
