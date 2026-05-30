# Recipe: Stone Type Data Flow — Missing stone_type in Billing Stone Modal

## Metadata
- **Pattern ID**: PAT-JS-011
- **Severity**: HIGH
- **Modules Affected**: Ret_Billing (stone modal, stone rate validation)
- **Auto-fixable**: No (requires understanding of variable declaration order)

## Client Scope
- **Applies to**: ALL (any client with stone rate settings and billing stone modal)
- **Reason**: The billing stone data JSON (from tag/estimation) never includes `stone_type`, so the field is perpetually empty. The stone rate validation matching condition always fails silently.

## Created By
- **Developer**: Antigravity AI
- **Client**: etail_development_src
- **Date**: 2026-04-16
- **Source Bug ID**: N/A

## Symptom
- Stone "Type" dropdown shows "-Stone Type-" (placeholder) in billing stone modal even though stone name (e.g., "Diamond") is correctly selected
- Stone rate validation never matches any setting entry because `stone_type` is always empty
- `check_min_max_stone_rate()` runs but ALL setting entries show "NO MATCH"

## Root Cause
A chain of 4 linked bugs:

1. **`stn_data` missing `stone_type`**: When billing loads stone data from JSON (`stone_details` hidden input), the JSON was originally saved without `stone_type` because the field was empty at save time.

2. **Hidden input only checks `stn_data.stone_type`**: At row creation, the `stone_type` variable was set from `stn_data.stone_type` only. The estimation module stores it as `stones_type` (plural). Neither field exists in the billing data.

3. **No fallback to stones master**: The `stones` global array (from `getAvailableStones()` API which queries `ret_stone` table) contains `stone_type` for every stone. But the row creation function never looks it up.

4. **Variable declaration order**: Even after adding a fallback lookup, the `stone_type` variable was computed AFTER the `stones_type` select was already built. The select used `stn_data.stones_type || stn_data.stone_type` directly (also empty), so no option got `selected`.

5. **Matching condition used `.stone_type` hidden input**: The `check_min_max_stone_rate` function matched on `stone_type` from a hidden input that was always empty. Since `stone_id` already uniquely identifies the stone (and its type), the `stone_type` condition was redundant.

## Detection
```powershell
# Check 1: stone_type variable only reads from stn_data (no fallback)
findstr /n "stn_data.stone_type" admin/assets/js/ret_billing.js | findstr "var stone_type"

# Check 2: stones_type select uses stn_data directly instead of local variable
findstr /n "stn_data.stones_type" admin/assets/js/ret_billing.js

# Check 3: stone_type in matching condition of check_min_max_stone_rate
findstr /n "stone_type.*items.stone_type" admin/assets/js/ret_billing.js

# Check 4: Save logic only reads .stone_type hidden input
findstr /n "find.*stone_type.*val" admin/assets/js/ret_billing.js | findstr "stone_details"
```

## Files
- `admin/assets/js/ret_billing.js`

## Fix

### Fix 1: Compute `stone_id` and `stone_type` BEFORE the `stones_type` select loop

Move `stone_id` and `stone_type` variable declarations (with fallback lookup from `stones` master) to before the `$.each(stone_types, ...)` loop in `create_new_empty_stone_item()`.

#### Before (stone_type computed AFTER select is built)
```javascript
  // stones_type select built here — uses stn_data directly
  $.each(stone_types, function (pkey, pitem) {
    stones_type += "<option value='" + pitem.id_stone_type + "' " +
      (stn_data
        ? pitem.id_stone_type == (stn_data.stones_type || stn_data.stone_type)
          ? "selected" : ""
        : "") + ">" + pitem.stone_type + "</option>";
  });

  // ... many lines later ...

  var stone_id = stn_data ? (stn_data.stone_id == undefined ? 0 : stn_data.stone_id) : 0;
  
  var stone_type = stn_data ? (stn_data.stone_type == undefined ? "" : stn_data.stone_type) : "";
```

#### After (stone_id + stone_type computed FIRST with fallback, then used in select)
```javascript
  // Compute stone_id and stone_type early so the Type select can use them
  var stone_id = stn_data
    ? stn_data.stone_id == undefined ? 0 : stn_data.stone_id
    : 0;

  var stone_type = stn_data
    ? (stn_data.stone_type || stn_data.stones_type || "")
    : "";

  // Fallback: look up stone_type from stones master if missing
  if (!stone_type && stone_id && typeof stones !== 'undefined') {
    for (var si = 0; si < stones.length; si++) {
      if (stones[si].stone_id == stone_id) {
        stone_type = stones[si].stone_type;
        break;
      }
    }
  }

  $.each(stone_types, function (pkey, pitem) {
    stones_type += "<option value='" + pitem.id_stone_type + "' " +
      (stone_type
        ? pitem.id_stone_type == stone_type
          ? "selected" : ""
        : "") + ">" + pitem.stone_type + "</option>";
  });
```

Remove the duplicate `stone_id` and `stone_type` declarations that were further down in the function.

### Fix 2: Save logic should fallback to `.stones_type` select

#### Before
```javascript
"stone_type": $(this).find(".stone_type").val(),
```

#### After
```javascript
"stone_type": $(this).find(".stone_type").val() || $(this).find(".stones_type").val(),
```

### Fix 3: Remove `stone_type` from validation matching (redundant with `stone_id`)

#### Before
```javascript
if (
  $("#id_branch").val() == items.id_branch &&
  curRow.find(".stone_type").val() == items.stone_type &&
  curRow.find(".stone_id").val() == items.stone_id &&
  curRow.find(".quality_id").val() == items.quality_id &&
  curRow.find(".stone_uom_id").val() == items.uom_id
) {
```

#### After
```javascript
if (
  $("#id_branch").val() == items.id_branch &&
  curRow.find(".stone_id").val() == items.stone_id &&
  curRow.find(".quality_id").val() == items.quality_id &&
  curRow.find(".stone_uom_id").val() == items.uom_id
) {
```

## Verification
1. Open billing → click stone icon → modal opens with stones loaded from tag
2. **Type dropdown** should show correct type (e.g., "Precious") instead of "-Stone Type-"
3. **Hidden input** `.stone_type` should have a value (inspect via DevTools)
4. Enter a rate outside min/max range → toaster warning should appear
5. Click Save → reopen modal → Type should still be correct (persisted in JSON)
6. Check console: matching should show `✓ MATCHED` for the correct setting entry

## Notes
- The `stones` global array is loaded via `get_stones()` AJAX call (`admin_ret_tagging/getStoneItems`) which queries `ret_stone` table with `stone_type` column
- The `stone_types` global array (from `get_stone_types()`) maps `id_stone_type` → `stone_type` name
- The estimation module doesn't have this bug because its data includes `stones_type` from the estimation save flow
- Related: Event handler fix (see `recipe_stone_rate_validation_event_handler.md`)
