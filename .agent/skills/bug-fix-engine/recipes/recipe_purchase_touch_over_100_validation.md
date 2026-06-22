# Recipe: Decouple Purchase Touch from >100 Validation

## Metadata
- **Pattern ID**: PAT-VAL-004
- **Severity**: LOW
- **Modules Affected**: Lot Creation
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Standard business logic for purchase touch where touch = purity + wastage, meaning it can exceed 100.

## Created By
- **Developer**: Antigravity
- **Client**: Unknown (Retail Branch)
- **Date**: 2026-05-14
- **Source Bug ID**: 9063ba19de46

## Symptom
Users are unable to enter a `purchase_touch` value greater than 100 in the Lot Creation page. The system shows an "Invalid Value" warning and resets the field to 0.

## Root Cause
The `purchase_touch` input field was mistakenly grouped in the same jQuery `change` event listener as `lot_wastage`, which restricts values to a maximum of 100. Because touch incorporates both purity and wastage, its valid business range can exceed 100.

## Detection
```command
grep -rn "lot_wastage,#purchase_touch" admin/assets/js/
```

## Files
- `admin/assets/js/ret_lot.js`

## Fix

### Before
```javascript
$(document).on('change', '#lot_wastage,#purchase_touch', function (e) {
	var value = this.value;
	if(value > 100){
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+' You Have Entered Invalid Value'});
		this.value=0;
	}
});
```

### After
```javascript
$(document).on('change', '#lot_wastage', function (e) {
	var value = this.value;
	if(value > 100){
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+' You Have Entered Invalid Value'});
		this.value=0;
	}
});
```

## Verification
1. Open the Lot Creation page.
2. Enter a value greater than 100 (e.g., 105) in the Purchase Touch field.
3. Verify that no warning toast appears and the value is accepted.
4. Enter a value greater than 100 in the Lot Wastage field to ensure the validation still triggers there.

## Notes
Never group multiple fields in a single validation event listener unless you are absolutely certain their business logic constraints are identical.
