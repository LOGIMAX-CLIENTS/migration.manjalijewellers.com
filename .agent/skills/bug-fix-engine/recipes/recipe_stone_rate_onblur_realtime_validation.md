# Stone Rate Onblur Real-Time Validation

> Stone rate min/max validation fires only on Save click, not when user enters invalid rate value. Fix: add inline `onblur` handler with a global `validate_stone_rate_onblur(el)` function.

## Metadata
- **Pattern ID**: PAT-LOT-010
- **Severity**: MEDIUM
- **Modules Affected**: Lot Inward, Tagging
- **Auto-fixable**: No (requires adding function + modifying HTML templates in JS)

## Client Scope
- **Applies to**: ALL
- **Reason**: Stone Rate Settings is a standard feature used across all clients.

## Created By
- **Developer**: Black Horest (AI Assistant)
- **Client**: VEDA SILVER PVT LTD
- **Date**: 2026-06-24
- **Source Bug ID**: N/A

## Symptom
- Stone Rate Settings configured with min/max rate ranges (e.g., min: 65000, max: 85000)
- User enters invalid rate (e.g., 50000) in Stone modal → **NO warning shown**
- Warning only appears **after clicking Save button**
- Expected: Warning should show **immediately** when user leaves the rate field

## Root Cause

### Why event delegation failed
1. jQuery `$(document).on('change',...)` and `$(document).on('blur',...)` event delegation did NOT reliably fire on dynamically created stone rate inputs inside Bootstrap modals
2. The existing `check_min_max_stone_rate(curRow)` function had scope issues:
   - When defined inside `$(document).ready()` — couldn't be called from inline handlers
   - When moved to global scope — `calculate_stone_amount()` (inside doc.ready) couldn't be called from it
   - Guard flag `is_checking_stone_rate` got stuck at `true`, blocking all subsequent calls

### Working solution
- **Inline `onblur` attribute** directly on the `<input>` element — guaranteed to fire
- **New simple global function** `validate_stone_rate_onblur(el)` that takes the raw DOM element
- Uses `for` loop instead of `$.each` for clarity
- Has `try-catch` to surface any hidden errors
- Clears rate AND amount fields on invalid entry (instead of auto-filling max_rate)

## Detection
```command
grep -n "class=\"stone_rate form-control\"" admin/assets/js/ret_lot.js admin/assets/js/ret_tagging.js
grep -n "validate_stone_rate_onblur" admin/assets/js/ret_lot.js admin/assets/js/ret_tagging.js
```

## Files
- `admin/assets/js/ret_lot.js` — Lot Inward stone rate validation
- `admin/assets/js/ret_tagging.js` — Tagging stone rate validation

## Fix

### Step 1: Add global variable (if not exists)

Near top of file, after other global vars:
```javascript
var stone_rate_settings = [];
```

### Step 2: Add `onblur` to ALL stone_rate input templates

Find all occurrences of:
```javascript
// Before (no onblur)
+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="..." style="..."/></td>'
```

Replace with:
```javascript
// After (with onblur)
+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="..." style="..." onblur="validate_stone_rate_onblur(this)"/></td>'
```

**Lot Inward (ret_lot.js)**: 4 templates (lines ~17457, ~17693, ~17801, ~19179)
**Tagging (ret_tagging.js)**: 6 templates (lines ~17296, ~17532, ~17640, ~20457, ~50874, ~54730)

### Step 3: Add validation function at END of file (OUTSIDE $(document).ready)

**For ret_lot.js** (uses `#id_branch`):
```javascript
// Simple stone rate validation for onblur - direct check
function validate_stone_rate_onblur(el)
{
    try {
        var curRow = $(el).closest('tr');
        var entered_rate = parseFloat($(el).val());
        if(isNaN(entered_rate) || entered_rate <= 0) return;
        if(!stone_rate_settings || stone_rate_settings.length == 0) return;

        var branch_id = $('#id_branch').val();
        var s_type = curRow.find('.stones_type').val();
        var s_id = curRow.find('.stone_id').val();
        var q_id = curRow.find('.quality_id').val();
        var u_id = curRow.find('.stone_uom_id').val();

        for(var i = 0; i < stone_rate_settings.length; i++){
            var item = stone_rate_settings[i];
            if(branch_id == item.id_branch && s_type == item.stone_type && s_id == item.stone_id && q_id == item.quality_id && u_id == item.uom_id){
                var min_r = parseFloat(item.min_rate);
                var max_r = parseFloat(item.max_rate);
                if(entered_rate < min_r || entered_rate > max_r){
                    $(el).val('');
                    curRow.find('.stone_price').val('');
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+'</br>'+'Entered Stone Rate Must be Within '+min_r+' and '+max_r+' !'});
                }
                break;
            }
        }
    } catch(e) {
        alert('Stone rate validation error: ' + e.message);
    }
}
```

**For ret_tagging.js** (uses `#branch_select`):
Same function but replace `$('#id_branch').val()` with `$('#branch_select').val()`.

### Step 4: Add save-time validation in validateStoneCusItemDetailRow()

Inside the `.each()` loop of `validateStoneCusItemDetailRow()`, after the empty field check, add:
```javascript
// Stone rate range validation against stone_rate_settings
if(row_validate && stone_rate_settings.length > 0){
    var curRow = $(this);
    var entered_rate = parseFloat(curRow.find('.stone_rate').val());
    $.each(stone_rate_settings, function(key, items){
        if($('#id_branch').val() == items.id_branch && curRow.find('.stones_type').val() == items.stone_type && curRow.find('.stone_id').val() == items.stone_id && curRow.find('.quality_id').val() == items.quality_id && curRow.find('.stone_uom_id').val() == items.uom_id){
            if(entered_rate < parseFloat(items.min_rate) || entered_rate > parseFloat(items.max_rate)){
                row_validate = false;
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+'</br>'+'Stone Rate Must be Within '+items.min_rate+' and '+items.max_rate+' ! Cannot Save.'});
                return false; // break $.each loop
            }
        }
    });
}
```

## Verification
1. Open Lot Inward → Add → Open Stone Modal
2. Fill stone type, name, quality, pcs, wt, uom
3. Enter rate BELOW min (e.g., 50000 when min is 65000) → Tab out → **Warning toaster should appear immediately + rate field cleared + amount cleared**
4. Enter rate WITHIN range → Tab out → **No warning, amount calculated**
5. Click Save with empty rate → **"Please Fill Required Stone Details" toaster**
6. Repeat steps 1-5 on Tagging → Add page

## Notes
- **Branch selector differs**: Lot Inward uses `#id_branch`, Tagging uses `#branch_select`
- **Do NOT use jQuery event delegation** (`$(document).on('blur',...)`) for this — it doesn't reliably fire on dynamically created inputs inside Bootstrap modals in this codebase
- **Do NOT use guard flags** (`is_checking_stone_rate`) — they get stuck and block all subsequent validations
- **Do NOT auto-fill max_rate** — clear the field instead so user must re-enter a valid value
- The `getStoneRateSettings()` AJAX call must be added to both `add` and `edit` init blocks to populate `stone_rate_settings`
- The old `check_min_max_stone_rate()` function can be left in place for backward compatibility but the onblur handler should call `validate_stone_rate_onblur(this)` instead
