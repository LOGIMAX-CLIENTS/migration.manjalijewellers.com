# Recipe: Catalog Sub Design Race Condition on Edit Screen

> Non-tag catalog row sub_design select is empty and id_sub_design sent as null on estimation edit screen due to AJAX race condition.

## Metadata
- **Pattern ID**: PAT-EST-RC01
- **Severity**: MEDIUM
- **Modules Affected**: Estimation (Edit Screen — Non-Tag/Catalog Items)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core estimation JS — all clients using non-tag catalog items on estimation edit are affected. The race condition exists in the shared `getNonTagDesignDetails()` function and the edit row rendering logic.

## Created By
- **Developer**: Antigravity
- **Client**: Konika
- **Date**: 2026-04-10
- **Source Bug ID**: N/A (reported by support team)

## Symptom
1. On the estimation **edit screen**, non-tag catalog rows show the **Sub Design dropdown as empty** (no option selected).
2. The `getNonTagDesignDetails()` AJAX call sends `id_sub_design` as **empty/null** to the server, causing the `get_non_tag_stock_details()` SQL query to return no results (stock balance not displayed).
3. Affects ONLY the edit screen — add screen works correctly because sub-design data loads before user interaction.

## Root Cause
**AJAX race condition** in the estimation edit screen initialization:

1. `non_tag_sub_design()` fires at **1s timeout** — fetches `cat_sub_design_details` via AJAX
2. `estimation_tag_data()` fires at **2s timeout** — fetches edit data and builds catalog rows

The 2s timeout does NOT guarantee the 1s AJAX response has arrived. When `cat_sub_design_details` is still `[]` (empty):
- The `$.each(cat_sub_design_details, ...)` loop produces **zero `<option>` elements**
- The `<select class="cat_sub_design">` has no options → `.val()` returns `null`
- `getNonTagDesignDetails()` reads `curRow.find('.cat_sub_design').val()` → sends `null` to server

**Two separate issues from the same root cause:**
1. **Visual**: Sub Design dropdown appears empty (no options to display)
2. **Data**: Wrong selector used to read `id_sub_design` for the stock AJAX call

## Detection
```command
REM Issue 1: Reading sub_design from select instead of hidden input
findstr /n "cat_sub_design.*\.val()" clients\{CLIENT}\assets\js\ret_estimation.js | findstr "id_sub_design"

REM Issue 2: No fallback for empty cat_sub_design_details
findstr /n "cat_sub_design_details" clients\{CLIENT}\assets\js\ret_estimation.js | findstr "each"
```

## Files
- `clients/{CLIENT}/assets/js/ret_estimation.js`

## Fix

### Fix 1: Read id_sub_design from hidden input instead of select

#### Before
```javascript
data: { 'id_branch': $('#id_branch').val(), 'id_section': curRow.find('.cat_section').val(), 'id_product': curRow.find('.cat_product').val(), 'id_design': curRow.find('.cat_design').val(), 'id_sub_design': curRow.find('.cat_sub_design').val() },
```

#### After
```javascript
data: { 'id_branch': $('#id_branch').val(), 'id_section': curRow.find('.cat_section').val(), 'id_product': curRow.find('.cat_product').val(), 'id_design': curRow.find('.cat_design').val(), 'id_sub_design': curRow.find('.cat_id_sub_design').val() },
```

**Why**: The hidden input `<input class="cat_id_sub_design">` is populated directly from `item.id_sub_design` during row construction — it doesn't depend on `cat_sub_design_details` being loaded. The `<select class="cat_sub_design">` depends on the async dropdown data.

---

### Fix 2: Add fallback option for sub design select

#### Before
```javascript
$.each(cat_sub_design_details, function (key, sitem) {
    var selected = "";
    if (sitem.id_product == item.product_id && sitem.id_design == item.design_id) {
        if (item.id_sub_design == sitem.id_sub_design) {
            selected = "selected";
        }
        select_sub_design += "<option value='" + sitem.id_sub_design + "' " + selected + ">" + sitem.sub_design_name + "</option>";
    }
});
```

#### After
```javascript
$.each(cat_sub_design_details, function (key, sitem) {
    var selected = "";
    if (sitem.id_product == item.product_id && sitem.id_design == item.design_id) {
        if (item.id_sub_design == sitem.id_sub_design) {
            selected = "selected";
        }
        select_sub_design += "<option value='" + sitem.id_sub_design + "' " + selected + ">" + sitem.sub_design_name + "</option>";
    }
});
// Fallback: if cat_sub_design_details hasn't loaded yet (race condition), add option from item data
if (select_sub_design == "" && item.id_sub_design != "" && item.id_sub_design != null) {
    select_sub_design = "<option value='" + item.id_sub_design + "' selected>" + (item.sub_design_name || '') + "</option>";
}
```

**Why**: When `cat_sub_design_details` is still empty due to AJAX timing, use the `id_sub_design` and `sub_design_name` that are already available from the edit item data to create a pre-selected option.

## Verification
1. Open an existing estimation with **non-tag catalog items** in edit mode
2. Verify the **Sub Design dropdown** shows the correct value (not empty)
3. Verify the **Stock** balance shows correctly next to Pcs and G.Wt fields
4. Check browser console for the `get_non_tag_stock` AJAX request — confirm `id_sub_design` is NOT empty in the POST payload
5. Change the Sub Design dropdown — verify stock updates correctly
6. Save the estimation — verify `id_sub_design` is saved correctly
7. **Edge case**: Test with items that have NO sub design (`id_sub_design` is null/empty) — should not break

## Notes
- This is a **race condition pattern** that may exist in other similar dropdown-dependent AJAX calls on edit screens. Check `cus_sub_design_details` (custom items) for the same issue.
- The hidden input pattern (`cat_id_sub_design` vs `cat_sub_design`) is a reliable fallback because hidden inputs are set synchronously from server data during row construction.
- Similar race conditions could exist for `cat_design_details` and `cat_product_details` but are less likely since they load earlier in the init sequence.
