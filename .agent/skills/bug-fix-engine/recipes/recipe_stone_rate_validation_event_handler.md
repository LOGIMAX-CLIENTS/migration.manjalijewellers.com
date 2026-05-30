# Recipe: Stone Rate Validation — Event Handler Never Fires in Billing Modal

## Metadata
- **Pattern ID**: PAT-JS-010
- **Severity**: HIGH
- **Modules Affected**: Ret_Billing (stone modal)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL (any client with stone rate settings feature)
- **Reason**: Stone modal DOM is destroyed on close, so `change` events registered on `.rate_per_gram` inside the modal never fire. This affects all clients using the billing stone modal.

## Created By
- **Developer**: Antigravity AI
- **Client**: etail_development_src
- **Date**: 2026-04-16
- **Source Bug ID**: N/A

## Symptom
- Stone rate validation never triggers in billing — user can enter any rate with no warning
- Works correctly in estimation module but not in billing
- `check_min_max_stone_rate()` function is never called when rate field loses focus

## Root Cause
Three issues in `ret_billing.js`:

1. **`change` event on dynamic modal elements**: The stone modal rows are appended dynamically inside `#stoneModal`. A `$(document).on("change", ".rate_per_gram", ...)` handler was used, but `change` only fires on `blur` for text inputs. Inside a modal that gets destroyed, the event timing is unreliable. `focusout` is the correct event — it bubbles and fires immediately when focus leaves the field.

2. **No validation on Save button**: When the user clicks "Update" (save), the stone amounts are recalculated but `check_min_max_stone_rate()` is never called on each row. The user can bypass validation entirely by never tabbing out of the rate field.

3. **`stone_pcs` / `stone_wt` wrapped in `if (stone_type == 1)` guard**: These variables were declared with `var` inside an `if` block. Due to JS hoisting, they exist in function scope but remain `undefined` when `stone_type ≠ 1`. The estimation version calculates them unconditionally.

## Detection
```powershell
# Check 1: Event handler uses 'change' instead of 'focusout'
findstr /n "change.*rate_per_gram\|change.*stone_wt" admin/assets/js/ret_billing.js

# Check 2: stone_pcs/stone_wt inside if(stone_type==1) guard
findstr /n "stone_type.*val.*== 1" admin/assets/js/ret_billing.js | findstr /v "//"

# Check 3: Trailing space in selector
findstr /n "rate_per_gram " admin/assets/js/ret_billing.js
```

## Files
- `admin/assets/js/ret_billing.js`

## Fix

### Fix 1: Change event handler from `change` to `focusout`

#### Before
```javascript
$(document).on("change", ".rate_per_gram,.stone_wt", function () {
  var row = $(this).closest("tr");
  check_min_max_stone_rate(row);
});
```

#### After
```javascript
$(document).on("focusout", ".rate_per_gram,.stone_wt", function () {
  var row = $(this).closest("tr");
  check_min_max_stone_rate(row);
});
```

### Fix 2: Add validation loop in Update/Save button handler

Find the Update button click handler (the one that iterates `#estimation_stone_item_details > tbody > tr` and pushes to `stone_details[]`). Add before that loop:

#### After (add before the `.each()` loop)
```javascript
// Validate stone rates before saving
$("#stoneModal .modal-body #estimation_stone_item_details > tbody > tr").each(function () {
  check_min_max_stone_rate($(this));
});
```

### Fix 3: Move `stone_pcs`/`stone_wt` outside the `if` guard

#### Before
```javascript
function check_min_max_stone_rate(curRow) {
  var stone_rate = curRow.find(".rate_per_gram ").val();
  $.each(stone_rate_settings, function (key, items) {
    var stone_centwt = 0;
    if (curRow.find(".stone_type").val() == 1) {
      var stone_pcs = isNaN(curRow.find(".stone_pcs").val()) || ...
      var stone_wt = isNaN(curRow.find(".stone_wt").val()) || ...
      stone_centwt = parseFloat((stone_wt / stone_pcs) * 100).toFixed(3);
    }
    // ... matching and validation
  });
}
```

#### After
```javascript
function check_min_max_stone_rate(curRow) {
  var stone_rate = curRow.find(".rate_per_gram").val();

  $.each(stone_rate_settings, function (key, items) {
    var stone_pcs =
      isNaN(curRow.find(".stone_pcs").val()) ||
      curRow.find(".stone_pcs").val() == ""
        ? 0
        : parseInt(curRow.find(".stone_pcs").val());

    var stone_wt =
      isNaN(curRow.find(".stone_wt").val()) ||
      curRow.find(".stone_wt").val() == ""
        ? 0
        : parseFloat(curRow.find(".stone_wt").val());

    var stone_centwt = parseFloat(((stone_wt) / (stone_pcs)) * 100).toFixed(3);

    // ... matching and validation (no if-guard around pcs/wt)
  });
}
```

### Fix 4: Clean up trailing spaces in selectors

#### Before
```javascript
curRow.find(".rate_per_gram ").val()
```

#### After
```javascript
curRow.find(".rate_per_gram").val()
```

## Verification
1. Open billing → add stone modal → enter a rate outside the configured min/max range
2. Tab out of the rate field → toaster warning should appear and rate should be clamped to max
3. Click Save/Update without tabbing out → validation should still run before save
4. Test with both `stone_type == 1` (precious) and other stone types
5. Test with multiple stone rows — each should validate independently

## Notes
- The estimation module (`ret_estimation.js`) already uses the correct pattern — always calculate `stone_pcs`/`stone_wt` unconditionally
- The `.rate_per_gram ` trailing space was present in multiple places and caused silent selector failures
- Related: `stone_type` data flow fix (see `recipe_stone_type_data_flow_billing.md`)
