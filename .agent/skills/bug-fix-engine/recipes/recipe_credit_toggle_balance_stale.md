# Credit Toggle Payment Balance Not Recalculated

> When "Is Credit" changes from Yes to No, RECEIVED resets but payment BALANCE stays stale — allows saving with mismatched totals.

## Metadata
- **Pattern ID**: PAT-BILLING-043
- **Severity**: HIGH
- **Modules Affected**: Billing → Make Payment
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core billing JS shared across all clients

## Created By
- **Developer**: Antigravity AI
- **Client**: erp.manepally.com
- **Date**: 2026-05-15
- **Source Bug ID**: N/A

## Symptom
User flow:
1. Bill total is ₹1,35,317
2. User adjusts Received Amount (e.g., to ₹1,35,315) and sets "Is Credit" to "Yes" with a due date
3. User changes "Is Credit" back to "No"
4. RECEIVED resets to bill total, but BALANCE still shows the old credit-reduced value
5. System allows saving with mismatch between Bill Total and Payment Mode Total

## Root Cause
The `is_credit` radio change handler at line ~1055 in `ret_billing.js` resets `.receive_amount` to `$('#total_cost').val()` when switching to "No", but **never calls `calculateFinalCost()`** afterward. This leaves the payment summary (TOTAL/BALANCE fields) stale, showing values from the previous credit state.

## Detection
```command
grep -A 20 "input\[name='billing\[is_credit\]'\]:radio" admin/assets/js/ret_billing.js | head -25
```
Check if `calculateFinalCost()` is called inside the handler. If not, the bug exists.

## Files
- `admin/assets/js/ret_billing.js` — credit radio change handler (~line 1055)

## Fix

### Before
```javascript
	 $("input[name='billing[is_credit]']:radio").on('change',function(){
		   if($(this).val()==0)
		   {
		   		$('#credit_due_date').prop('disabled',true);
		   		$('.receive_amount').val($('#total_cost').val());
				// For Delivery Item
				$('.delivery_status').prop('disabled',true);
				$('.delivery_status').prop('checked',true);
				$('.is_delivered').val(1);
				$('#is_to_be_no').prop('checked',true);
		   }
		   else
		   {
		   		$('#credit_due_date').prop('disabled',false);
				// For Deilvery Item
				$('.delivery_status').prop('disabled',false);
		   }
	});
```

### After
```javascript
	 $("input[name='billing[is_credit]']:radio").on('change',function(){
		   if($(this).val()==0)
		   {
		   		$('#credit_due_date').prop('disabled',true);
		   		$('.receive_amount').val($('#total_cost').val());
				// For Delivery Item
				$('.delivery_status').prop('disabled',true);
				$('.delivery_status').prop('checked',true);
				$('.is_delivered').val(1);
				$('#is_to_be_no').prop('checked',true);
		   }
		   else
		   {
		   		$('#credit_due_date').prop('disabled',false);
				// For Deilvery Item
				$('.delivery_status').prop('disabled',false);
		   }
		   // Recalculate payment summary so BALANCE syncs with updated RECEIVED
		   calculateFinalCost();
	});
```

## Verification
1. Open billing → add items → go to Make Payment tab
2. Set "Is Credit" to "Yes" → enter a due date → reduce Received Amount
3. Switch "Is Credit" back to "No"
4. Verify: Received Amount resets to bill total
5. Verify: BALANCE recalculates and matches (should show bill total minus any payment mode amounts)
6. Verify: Save button behavior is correct (enables only at zero balance)
7. Verify: switching to "Yes" also recalculates correctly
8. Verify: normal billing without credit toggle still works

## Notes
- Single line fix: `calculateFinalCost()` added after the radio change handler's if/else block
- `calculateFinalCost()` internally calls `calculatePaymentCost()` which updates TOTAL and BALANCE
- The handler already correctly resets `.receive_amount` — only the downstream recalculation was missing
