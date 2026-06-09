# Catalog Duplicate Check — Double Toast Prevention

## Metadata
- **Pattern ID**: PAT-CTG-006
- **Severity**: MEDIUM
- **Modules Affected**: Retail Catalog (Product Mapping, Sub-Design, any module with both global AJAX and attribute-level duplicate checks)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Affects all clients where `data-duplicate-table` attributes coexist with global AJAX blur handlers

## Created By
- **Developer**: Antigravity (Black Horest) / Karthi
- **Client**: Source (retailsource)
- **Date**: 2026-06-08
- **Source Bug ID**: Design Mapping duplicate validation integration

## Symptom
When a user types a duplicate value in a Sub-Design Name or Code field and moves focus away (blur), TWO toast notifications appear:
1. One from the `data-duplicate-table` attribute handler in `validation.js`
2. One from the global `ajaxSetup/beforeSend` duplicate interceptor in `catalog_master.js`

## Root Cause
Both the element-level `Validation.initDuplicateChecks()` (reads `data-duplicate-*` HTML attributes) and the global `$.ajaxSetup({ beforeSend: ... })` interceptor try to check the same field for duplicates. They are independent systems that don't know about each other.

## Detection
```command
grep -n "data-duplicate-table" admin/application/views/master/ret_product/product_mapping.php
grep -n "ajaxSetup" admin/assets/js/catalog_master.js | head -5
```

## Files
- `admin/assets/js/catalog_master.js`

## Fix

### Before (global blur handler in catalog_master.js)
```javascript
// Global blur handler checks ALL fields, including those with data-duplicate-table
$('body').on('blur', 'input[type="text"]', function() {
    // ... duplicate check logic
});
```

### After
```javascript
// Skip elements that already have data-duplicate-table attribute — those are handled by validation.js
$('body').on('blur', 'input[type="text"]', function() {
    if ($(this).attr('data-duplicate-table')) {
        return; // Already handled by Validation.initDuplicateChecks()
    }
    // ... existing duplicate check logic continues for other fields
});
```

## Verification
1. Navigate to Product Mapping page → Open Add Sub Design modal
2. Enter a Sub Design name that already exists
3. Click/tab to the next field (trigger blur)
4. Verify ONLY ONE toast notification appears (from `validation.js`)
5. Verify the input field border turns red
6. Change the value to a unique name — verify the error clears
7. Submit the form — verify save works for unique values
8. Test the Edit modal similarly

## Notes
- This pattern occurs whenever two independent validation systems target the same field
- The `data-duplicate-table` attribute is the AUTHORITATIVE check — it uses `Validation.initDuplicateChecks()` which is purpose-built for this
- The global `ajaxSetup` interceptor is a LEGACY catch-all that should defer to attribute-based validation
- When adding `data-duplicate-*` attributes to new fields, always check if the global handler needs a skip guard
- The guard uses `$(this).attr('data-duplicate-table')` — this returns `undefined` for fields without the attribute (falsy), so the existing behavior for non-attribute fields is preserved
