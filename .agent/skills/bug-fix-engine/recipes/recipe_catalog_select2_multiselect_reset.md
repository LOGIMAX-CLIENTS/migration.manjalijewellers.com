# Catalog Select2 Multi-Select Reset After Form Submission

## Metadata
- **Pattern ID**: PAT-CTG-004
- **Severity**: LOW
- **Modules Affected**: Retail Catalog (Design Mapping)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Affects all clients using multi-select Select2 dropdowns in catalog mapping forms

## Created By
- **Developer**: Antigravity (Black Horest) / Karthi
- **Client**: Source (retailsource)
- **Date**: 2026-06-08
- **Source Bug ID**: Design Mapping Bug #13

## Symptom
After adding a design mapping (selecting a product and multiple designs, then clicking Add), the Select Design multi-select field shows an empty box instead of resetting to its placeholder. The user sees remnant selection tokens or an empty search box occupying space.

## Root Cause
The reset code uses `$('#select_design').select2("val","")` which works for single-select Select2 but does NOT properly clear a multi-select Select2. The multi-select retains internal state and visual tokens.

Additionally, the button re-enable code references a wrong element ID `#update_design` instead of the actual button ID `#update_product_design_mapping`.

## Detection
```command
grep -n "select2(\"val\",\"\")" admin/assets/js/catalog_master.js | head -20
grep -n "#update_design" admin/assets/js/catalog_master.js
```

## Files
- `admin/assets/js/catalog_master.js`

## Fix

### Before
```javascript
$('#select_design').select2("val","");

$('#select_product').select2("val","");

$('#update_design').prop('disabled',false);
```

### After
```javascript
$('#select_design').val(null).trigger('change');

$('#select_product').select2("val","");

$('#update_product_design_mapping').prop('disabled',false);
```

## Verification
1. Navigate to Design Mapping page
2. Select a Product from the dropdown
3. Select 2-3 Designs from the multi-select dropdown
4. Click "Add" button
5. Verify the Select Design field resets completely — no empty boxes, no ghost tokens
6. Verify the placeholder text "Select Design" appears properly
7. Verify the Add button is re-enabled and can be clicked again
8. Add another mapping — verify the flow works repeatedly without visual glitches

## Notes
- **Rule**: For single-select Select2: use `select2("val","")` or `select2("val", null)`
- **Rule**: For multi-select Select2: use `.val(null).trigger('change')` — this is the only reliable method
- Always check if the element has the `multiple` attribute to determine which reset method to use
- Wrong button ID references (`#update_design` instead of `#update_product_design_mapping`) are silent failures — the button never gets re-enabled, which can freeze the UI
- Search for similar patterns: `grep -n "select2(\"val\"" catalog_master.js | grep -v "//"` and verify each one matches its Select2 mode (single vs multi)
