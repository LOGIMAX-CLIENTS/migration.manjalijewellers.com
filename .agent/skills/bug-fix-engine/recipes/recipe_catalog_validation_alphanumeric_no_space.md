# Catalog Input Validation — AlphanumericNoSpace + Select2 Restriction

## Metadata
- **Pattern ID**: PAT-CTG-002
- **Severity**: MEDIUM
- **Modules Affected**: Retail Catalog (Sub-Design, Design, Product Mapping)
- **Auto-fixable**: Partial (validation rule is auto-fixable, HTML attributes need manual review)

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients using Retail Catalog module need consistent input validation

## Created By
- **Developer**: Antigravity (Black Horest) / Karthi
- **Client**: Source (retailsource)
- **Date**: 2026-06-08
- **Source Bug ID**: Design Mapping Bug #2, #3; Sub-Design form validation

## Symptom
1. Users can enter special characters and spaces in Sub-Design Name, Sub-Design Code, and other catalog master fields
2. Select2 search boxes allow special characters which can cause backend issues
3. No consistent client-side validation pattern across catalog forms

## Root Cause
1. No `alphanumericNoSpace` validation rule existed in `validation.js`
2. Input fields in catalog forms lacked `data-validate` attributes
3. Select2 search fields had no input restriction

## Detection
```command
grep -n "alphanumericNoSpace" admin/assets/js/validation.js
grep -n "data-validate" admin/application/views/master/ret_product/product_mapping.php
grep -n "select2:open" admin/assets/js/catalog_master.js
```

## Files
- `admin/assets/js/validation.js`
- `admin/assets/js/catalog_master.js`
- `admin/application/views/master/ret_product/product_mapping.php`
- `admin/application/views/master/ret_sub_design/form.php`

## Fix

### Part 1: Add validation rule to validation.js

#### Before
```javascript
// No alphanumericNoSpace rule exists
```

#### After
```javascript
alphanumericNoSpace: {
    pattern: /[^A-Za-z0-9]/,
    message: 'Only letters and numbers are allowed. No spaces or special characters.'
},
```

### Part 2: Add Global Select2 restriction in catalog_master.js

#### Before
```javascript
// No Select2 input restriction
```

#### After
```javascript
$(document).on('select2:open', function() {
    if ($('.select2-search__field').length) {
        $('.select2-search__field').attr('maxlength', '20');
        $('.select2-search__field').off('input.s2val').on('input.s2val', function() {
            this.value = this.value.replace(/[^A-Za-z0-9\s_\-]/g, '');
        });
    }
});
```

### Part 3: Add HTML validation attributes to form fields

#### Before
```html
<input type="text" class="form-control" id="sub_design_name" name="sub_design_name" placeholder="Enter Sub Design" required="true">
```

#### After
```html
<input type="text" class="form-control" id="sub_design_name" name="sub_design_name" placeholder="Enter Sub Design" required="true" data-validate="alphanumericNoSpace" data-duplicate-table="ret_sub_design_master" data-duplicate-column="sub_design_name" data-duplicate-id-column="id_sub_design" data-duplicate-label="Sub Design Name">
```

## Verification
1. Navigate to Product Mapping → open Add Sub Design modal
2. Type special characters (@#$%^&) in the Name field — verify they are rejected
3. Type a space — verify it is rejected
4. Open any Select2 dropdown → type special characters in search — verify only alphanumeric, space, underscore, hyphen are allowed
5. Enter a duplicate Sub Design name — verify duplicate warning appears
6. Verify validation does NOT block legitimate alphanumeric entries

## Notes
- The `alphanumericNoSpace` rule blocks ALL non-alphanumeric characters including spaces
- The Select2 restriction is GLOBAL — applies to all Select2 fields in the catalog module
- Select2 allows: letters, numbers, spaces, underscores, hyphens (broader than form fields)
- The `data-duplicate-*` attributes require the `Validation.initDuplicateChecks()` function to be loaded via `validation.js`
- Ensure `validation.js` is loaded BEFORE `catalog_master.js` in the footer
