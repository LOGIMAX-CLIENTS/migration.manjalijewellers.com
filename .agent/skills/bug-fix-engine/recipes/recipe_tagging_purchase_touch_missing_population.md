# Recipe: Purchase Touch Missing Population in Tagging
<br>

## Metadata
- **Pattern ID**: PAT-JS-005
- **Severity**: HIGH
- **Modules Affected**: Tagging
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Common core tagging logic issue where field population was incomplete.

## Created By
- **Developer**: Antigravity
- **Client**: etail_development
- **Date**: 2026-04-18
- **Source Bug ID**: N/A

## Symptom
The "Purchase Touch" field in the Tagging Screen remains empty after selecting a lot or purchase entry product, even though the value is entered in the source module (Lot/Purchase Entry).

## Root Cause
The `#tag_lt_prod` change handler in `ret_tagging.js` was missing the logic to set the `#purchase_touch` input value, despite the value being available in the `data-touch` attribute of the product options. Commented-out code existed but was never activated.

## Detection
```command
grep -rn "tag_lt_prod" admin/assets/js/ret_tagging.js | grep "change"
```

## Files
- `admin/assets/js/ret_tagging.js`

## Fix

### Before
```javascript
			get_Activedesign(this.value);


		}

		else

		{

			$('#tag_lt_prodId').val('');

			$('.issuspensestock').val(0);

		}
```

### After
```javascript
			get_Activedesign(this.value);

			// Set purchase touch from lot product data attribute
			var touch = $('#tag_lt_prod option:selected').attr('data-touch');
			$('#purchase_touch').val(touch ? touch : '');

		}

		else

		{

			$('#tag_lt_prodId').val('');

			$('.issuspensestock').val(0);

			$('#purchase_touch').val('');

		}
```

## Verification
1. Navigate to Tagging -> Add.
2. Select a Section, then a Lot.
3. Select a Product from the dropdown.
4. Verify `#purchase_touch` populates with the correct value.
5. Change to another product with different touch; verify it updates.
6. Clear the lot/product; verify `#purchase_touch` clears.

## Notes
The `data-touch` attribute is consistently used across tagging and lot selection logic, so reading it on change is the standard fix pattern for this module.
