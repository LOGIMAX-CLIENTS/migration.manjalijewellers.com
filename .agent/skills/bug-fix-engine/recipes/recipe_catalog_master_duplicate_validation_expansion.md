# Catalog Master — Duplicate Validation Expansion (Name + Code, All Modules)

> Extends duplicate entry validation to cover ALL catalog master modules, ALL code/shortcode fields, and ALL edit-mode fields. Prevents duplicate entries at blur, save, and AJAX interceptor levels. Includes Account Head, Delivery Location, Old Metal Type, and Old Metal Category modules with critical ID collision and whitelist fixes.

## Metadata
- **Pattern ID**: PAT-VAL-020
- **Severity**: HIGH
- **Modules Affected**: All 35+ Catalog Master sub-modules (Metal, Stone, Material, UOM, Screw, Hook, Making Type, Theme, Tax, Section, Category, Floor, Floor Counter, Charges, Sub Design, Wallet, Purity, Color, Cut, Clarity, Shape, Tag, Collection, Product Division, QC Cancel Reason, Repair, Stock, Device, Product Grouping, Profession, Size, Old Metal Category, **Account Head**, **Delivery Location**, **Old Metal Type**)
- **Auto-fixable**: Yes (string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core validation gap — affects every client using Catalog Master modules

## Created By
- **Developer**: Antigravity (Black Horest)
- **Client**: VBC Jewellery (retailsource)
- **Date**: 2026-06-04 (updated 2026-06-04)
- **Source Bug ID**: N/A (enhancement request)

## Symptom
1. Duplicate **code/shortcode** values (e.g., metal_code, cat_code, stone_code) could be saved without any warning
2. **Edit-mode** fields (`ed_` prefix) had NO duplicate checks — updating a record could create duplicates
3. Several modules (Purity, Old Metal Category, Account Head, Delivery Location, Old Metal Type) had no blur-time or AJAX-interceptor duplicate checks at all
4. AJAX interceptor only checked ONE field per URL — if code was duplicate but name was unique, the save went through
5. Wrong table names in DUPLICATE_MAP: `ret_screw` instead of `ret_screw_type`, `ret_hook` instead of `ret_hook_type`, `ret_floor` instead of `ret_branch_floor`
6. **Account Head** — `id="name"` on the Add form collided with other modules sharing the same generic ID; the `check_duplicate` PHP endpoint had `ret_account` in `allowed_tables` but the actual DB table is `ret_account_head`, causing all duplicate checks to silently return `exists: false`
7. **Account Head** — Edit click handler cleared `$('#ed_name')` (non-existent element) instead of `$('#ed_account_name')`; toaster messages said "please select Enter Device Name" (copy-paste from Device module)
8. **Old Metal Category** — `$('old_metal_perc')` selector was missing the `#` prefix, so percentage validation never triggered
9. **Old Metal Category** — Save & Close / Update buttons had `data-dismiss="modal"` which closed the modal even when validation errors were present
10. **AJAX URL matching** — case-sensitive `indexOf` comparisons meant `ret_account/Add` wouldn't match `ret_account/add` pattern, causing interceptor misses on case-variant URLs
11. **Charges Column Mismatch** — `DUPLICATE_MAP` and `_urlFieldsMap` mapped the Charges name to the non-existent DB column `charge_name` instead of `name_charge`, causing duplicate validation to fail silently
12. **Charges Premature Close** — Save & Close and Update buttons in the Charges modal had `data-dismiss="modal"`, closing the modal before validation could block submission or show errors

## Root Cause
The original implementation only covered **name fields** for **Add mode** in both the `DUPLICATE_MAP` (blur check) and `_urlTableMap` (AJAX interceptor). Architectural gaps:

1. **No code/shortcode coverage** — `DUPLICATE_MAP` only had name entries, no code entries
2. **No edit-mode coverage** — `DUPLICATE_MAP` missed all `ed_` prefixed fields
3. **Single-field AJAX interceptor** — `_urlTableMap` mapped each URL to ONE field object, making it impossible to check both name AND code on save
4. **Wrong DB table references** — Screw uses `ret_screw_type`, Hook uses `ret_hook_type`, Floor uses `ret_branch_floor` (not `ret_screw`, `ret_hook`, `ret_floor`)
5. **Missing tables in PHP whitelist** — `ret_screw_type`, `ret_hook_type`, `ret_branch_floor`, `ret_account_head` were not in the `allowed_tables` array
6. **Generic input IDs** — Account Head used `id="name"` which collides with other modules on the same page (Delivery Location, CR/DR Ledger all used `#name`)
7. **Wrong table name in whitelist** — `ret_account` was in `allowed_tables` but the actual table is `ret_account_head`; the JS `DUPLICATE_MAP` correctly referenced `ret_account_head` but the server rejected it
8. **Missing `#` in jQuery selector** — `$('old_metal_perc')` looks for a `<old_metal_perc>` HTML element (doesn't exist), not `#old_metal_perc`
9. **Premature modal dismiss** — `data-dismiss="modal"` on save/update buttons fires Bootstrap's modal close handler synchronously, before any JS click handler can show validation errors
10. **Case-sensitive URL matching** — `options.url.indexOf(urlKey)` is case-sensitive, but CodeIgniter routes often have mixed-case method names (`Add` vs `add`, `Update` vs `update`)
11. **Edit-mode excludeId not resolved** — The blur handler didn't attempt to find the current record's ID to exclude from duplicate checks in edit mode; the AJAX interceptor had a fallback gap where `excludeId` wasn't extracted from `options.data` when the URL didn't contain the ID segment
12. **Wrong Column mapping for Charges** — The DB column name is `name_charge`, not `charge_name`. The original configuration mapped it to `charge_name`, so the duplicate check SQL failed to match the column
13. **Charges modal dismiss** — `data-dismiss="modal"` on Charges save/update buttons caused the modal to close immediately, preventing users from seeing duplicate checks or input validation alerts

## Detection
```command
# Check if DUPLICATE_MAP has code fields
grep -n "metal_code\|stone_code\|material_code\|cat_code\|uom_code\|screw_code\|hook_code\|theme_code\|tax_code" admin/assets/js/catalog_master.js

# Check if edit-mode fields exist in DUPLICATE_MAP
grep -n "ed_metal_name\|ed_stone_name\|ed_category_name\|ed_color\|ed_cut\|ed_clarity" admin/assets/js/catalog_master.js

# Check if _urlTableMap or _urlFieldsMap supports arrays
grep -n "_urlTableMap\|_urlFieldsMap" admin/assets/js/catalog_master.js

# Check if controller has ret_account_head in allowed_tables
grep -n "ret_account_head" admin/application/controllers/admin_ret_catalog.php

# Check if Account Head uses generic #name ID (collision risk)
grep -n 'id="name"' admin/application/views/master/ret_account/list.php

# Check for missing # in jQuery selectors (Old Metal Category)
grep -n "\$('old_metal_perc')" admin/assets/js/catalog_master.js

# Check for data-dismiss on save/update buttons (premature modal close)
grep -n 'id="add_old_metal_category".*data-dismiss\|id="update_old_metal_cat".*data-dismiss' admin/application/views/master/metal_category/list.php

# Check case-sensitive URL matching in AJAX interceptor
grep -n "options.url.indexOf(urlKey)" admin/assets/js/catalog_master.js
```

## Files
- `admin/assets/js/catalog_master.js` — DUPLICATE_MAP + AJAX interceptor + Account Head / Delivery / Old Metal Type event handlers
- `admin/assets/js/validation.js` — checkDuplicate() function (no changes needed)
- `admin/application/controllers/admin_ret_catalog.php` — check_duplicate() endpoint (allowed_tables + status_columns)
- `admin/application/views/master/ret_account/list.php` — Account Head Add/Edit modals (input IDs + data-validate)
- `admin/application/views/master/metal_category/list.php` — Old Metal Category modal buttons (data-dismiss removal)

## Fix

### Fix 1: Account Head — Unique Input IDs (list.php)

#### Before
```html
<!-- Add modal -->
<input type="text" class="form-control" id="name" name="name" placeholder="Account Name" style=" text-transform: uppercase;">

<!-- Edit modal -->
<input type="text" id="ed_account_name" class="form-control" placeholder="Enter Account Head" style=" text-transform: uppercase;">
```

#### After
```html
<!-- Add modal — unique ID, with validation attribute -->
<input type="text" class="form-control" id="account_name" name="name" placeholder="Account Name" style=" text-transform: uppercase;" data-validate="nameField">

<!-- Edit modal — with validation attribute -->
<input type="text" id="ed_account_name" class="form-control" placeholder="Enter Account Head" style=" text-transform: uppercase;" data-validate="nameField">
```

### Fix 2: Account Head — JS Event Handlers (catalog_master.js)

#### Before
```javascript
$("#add_acc_name").on('click', function () {
    if ($("#name").val() == '') {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'please select Enter Device Name..' });
    }
    // ...
    data: { "name": $("#name").val(), "account_status": $('#account_status').val() },
    // success:
    $('#name').val('');
});

// Edit click handler
$(document).on('click', "#accounthed_list a.btn-edit", function (e) {
    $("#ed_name").val('');  // WRONG — element doesn't exist
});

// Update click handler
$("#update_accsection").on('click', function () {
    if ($("#ed_account_name").val() == '') {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'please select Enter Device Name..' });
    }
});
```

#### After
```javascript
$("#add_acc_name").on('click', function () {
    if ($("#account_name").val() == '') {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Account Head Name..' });
    }
    // ...
    data: { "name": $("#account_name").val(), "account_status": $('#account_status').val() },
    // success:
    $('#account_name').val('');
});

// Edit click handler — FIXED selector
$(document).on('click', "#accounthed_list a.btn-edit", function (e) {
    $("#ed_account_name").val('');
});

// Update click handler — FIXED message
$("#update_accsection").on('click', function () {
    if ($("#ed_account_name").val() == '') {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Account Head Name..' });
    }
});
```

### Fix 3: DUPLICATE_MAP — New Module Entries (catalog_master.js)

#### Add to DUPLICATE_MAP
```javascript
// ── Delivery Location (Add + Edit) ──
delivery_name: { table: 'ret_sale_delivery', column: 'name', idColumn: 'id_sale_delivery' },
ed_delivery_name: { table: 'ret_sale_delivery', column: 'name', idColumn: 'id_sale_delivery' },
// ── Old Metal Type (Add + Edit) ──
metal_type: { table: 'ret_old_metal_type', column: 'metal_type', idColumn: 'id_metal_type' },
ed_metal_type: { table: 'ret_old_metal_type', column: 'metal_type', idColumn: 'id_metal_type' },
// ── Account Head (Add + Edit) ──
account_name: { table: 'ret_account_head', column: 'name', idColumn: 'id_acc_head' },
ed_account_name: { table: 'ret_account_head', column: 'name', idColumn: 'id_acc_head' }
```

### Fix 4: _urlFieldsMap — New URL Routes (catalog_master.js)

#### Add to _urlFieldsMap
```javascript
// ── Delivery Location ──
'ret_delivery/Add': [{ dataField: 'name', inputId: 'delivery_name', table: 'ret_sale_delivery', column: 'name', idColumn: 'id_sale_delivery' }],
'ret_delivery/add': [{ dataField: 'name', inputId: 'delivery_name', table: 'ret_sale_delivery', column: 'name', idColumn: 'id_sale_delivery' }],
// ── Old Metal Type ──
'metal_type/add': [{ dataField: 'metal_type', inputId: 'metal_type', table: 'ret_old_metal_type', column: 'metal_type', idColumn: 'id_metal_type' }],
// ── Account Head ──
'ret_account/Add': [{ dataField: 'name', inputId: 'account_name', table: 'ret_account_head', column: 'name', idColumn: 'id_acc_head' }],
'ret_account/add': [{ dataField: 'name', inputId: 'account_name', table: 'ret_account_head', column: 'name', idColumn: 'id_acc_head' }]
```

### Fix 5: PHP Whitelist + Status Columns (admin_ret_catalog.php)

#### Before
```php
$allowed_tables = array(
    // ...
    'ret_account', 'ret_crdr_ledger', 'ret_profession',
);

$status_columns = array(
    // ... missing ret_account_head
    'ret_size'             => 'status'
);
```

#### After
```php
$allowed_tables = array(
    // ...
    'ret_account', 'ret_account_head', 'ret_crdr_ledger', 'ret_profession',
);

$status_columns = array(
    // ...
    'ret_size'             => 'status',
    'ret_account_head'     => 'status'
);
```

### Fix 6: Old Metal Category — jQuery Selector Typo (catalog_master.js)

#### Before
```javascript
else if ($('old_metal_perc').val() == '') {  // Missing # — selects nothing
```

#### After
```javascript
else if ($('#old_metal_perc').val() == '') {  // Fixed selector
```

### Fix 7: Old Metal Category — Premature Modal Dismiss (list.php)

#### Before
```html
<a href="#" id="add_old_metal_category" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
<a href="#" id="update_old_metal_cat" class="btn btn-success" data-dismiss="modal">Update</a>
```

#### After
```html
<a href="#" id="add_old_metal_category" class="btn btn-warning">Save & Close</a>
<a href="#" id="update_old_metal_cat" class="btn btn-success">Update</a>
```

### Fix 8: Case-Insensitive URL Matching in AJAX Interceptor (catalog_master.js)

#### Before
```javascript
for (var urlKey in _urlFieldsMap) {
    if (options.url.indexOf(urlKey) !== -1) {
        matchedFields = _urlFieldsMap[urlKey];
        break;
    }
}
// ...
if (urlParts[u] === 'update' || urlParts[u] === 'Update') {
```

#### After
```javascript
var lowerUrl = options.url.toLowerCase();

for (var urlKey in _urlFieldsMap) {
    if (lowerUrl.indexOf(urlKey.toLowerCase()) !== -1) {
        matchedFields = _urlFieldsMap[urlKey];
        break;
    }
}
// ...
var partLower = urlParts[u].toLowerCase();
if (partLower === 'update') {
```

### Fix 9: Edit-Mode excludeId Resolution in Blur Handler (catalog_master.js)

#### Before
```javascript
// Blur handler had no excludeId logic
Validation.checkDuplicate({
    table: config.table,
    column: config.column,
    value: value,
    idColumn: config.idColumn
}, function (isDuplicate, msg) { ... });
```

#### After
```javascript
// Blur handler now resolves excludeId for edit-mode fields
var excludeId = '';
if (inputId.indexOf('ed_') === 0 || inputId.indexOf('_edit') !== -1) {
    var $form = $input.closest('form');
    if ($form.length) {
        var $excludeInput = $form.find('#' + config.idColumn + ', [name="' + config.idColumn + '"]');
        if ($excludeInput.length) {
            excludeId = $.trim($excludeInput.val());
        }
    }
}

Validation.checkDuplicate({
    table: config.table,
    column: config.column,
    value: value,
    idColumn: config.idColumn,
    excludeId: excludeId
}, function (isDuplicate, msg) { ... });
```

### Fix 10: AJAX Interceptor excludeId Fallback from POST Data (catalog_master.js)

#### Added after URL-based excludeId extraction
```javascript
// Fallback: If excludeId is not extracted from URL, check options.data
if (!excludeId || isNaN(excludeId)) {
    if (originalOptions.data) {
        if (originalOptions.data instanceof FormData) {
            try {
                excludeId = originalOptions.data.get(matchedFields[0].idColumn) || '';
            } catch (e) {}
        } else if (typeof originalOptions.data === 'object') {
            excludeId = originalOptions.data[matchedFields[0].idColumn] || '';
        } else if (typeof originalOptions.data === 'string') {
            var params = originalOptions.data.split('&');
            for (var i = 0; i < params.length; i++) {
                var pair = params[i].split('=');
                if (decodeURIComponent(pair[0]) === matchedFields[0].idColumn) {
                    excludeId = decodeURIComponent(pair[1] || '');
                    break;
                }
            }
        }
    }
}
```

### Fix 11: Charges — DB Column Mapping & Modal Fixes (catalog_master.js & charges/list.php)

#### 1. Correct DUPLICATE_MAP column name in `catalog_master.js`
```javascript
charge_name: { table: 'ret_charges', column: 'name_charge', idColumn: 'id_charge' },
charge_name_edit: { table: 'ret_charges', column: 'name_charge', idColumn: 'id_charge' },
```

#### 2. Correct `_urlFieldsMap` column name in `catalog_master.js`
```javascript
'charges/add': [
    { dataField: 'charge_name', inputId: 'charge_name', table: 'ret_charges', column: 'name_charge', idColumn: 'id_charge' },
    { dataField: 'charge_code', inputId: 'charge_code', table: 'ret_charges', column: 'code_charge', idColumn: 'id_charge' }
]
```

#### 3. Remove `data-dismiss="modal"` in `charges/list.php`
```html
<!-- Before -->
<a href="#" id="charge_save_and_close" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
<a href="#" id="update_charge" class="btn btn-success" data-dismiss="modal">Update</a>

<!-- After -->
<a href="#" id="charge_save_and_close" class="btn btn-warning">Save & Close</a>
<a href="#" id="update_charge" class="btn btn-success">Update</a>
```

#### 4. Programmatically Close Modal & Clear Borders in `catalog_master.js`
```javascript
// Hide modals programmatically in AJAX success handlers
function add_charges() {
    // ...
    success: function (data) {
        // Clear inputs, borders and close modal
        $('#charge_name').val('');
        $('#charge_code').val('');
        $('#charge_name, #charge_code').css('border-color', '');
        $('#charges_add').modal('hide');
        get_charges_list();
    }
}

function update_charges() {
    // ...
    success: function (data) {
        $('#edit_charges').modal('hide');
        get_charges_list();
    }
}
```

## Module-to-Field Coverage Table

| Module | Name Field (DB Column) | Code Field (DB Column) | DB Table |
|--------|----------------------|----------------------|----------|
| Metal | metal_name | metal_code | metal |
| Stone | stone_name | stone_code | ret_stone |
| Material | material_name | material_code | ret_material |
| UOM | uom_name | uom_short_code | ret_uom |
| Screw | screw_name | screw_short_code | ret_screw_type |
| Hook | hook_name | hook_short_code | ret_hook_type |
| Making Type | mak_name | mak_short_code | ret_making_type |
| Theme | theme_name | theme_code | ret_theme |
| Tax | tax_name | tax_code | ret_taxmaster |
| Section | section_name | section_short_code | ret_section |
| Category | category_name | cat_code | ret_category |
| Floor | floor_name | floor_short_code | ret_branch_floor |
| Floor Counter | counter_name | counter_short_code | ret_branch_floor_counter |
| Charges | name_charge | code_charge | ret_charges |
| Sub Design | sub_design_name | sub_design_code | ret_sub_design_master |
| Wallet | wallet_name | wallet_code | ret_wallet |
| Purity | purity | — | ret_purity |
| Color | color | — | ret_color |
| Cut | cut | — | ret_cut |
| Clarity | clarity | — | ret_clarity |
| Shape | name | — | ret_shape |
| Tag | tag_name | — | ret_tag_type_master |
| Collection | collection_name | — | ret_collection_master |
| Product Division | div_value | — | ret_product_division |
| QC Cancel Reason | cancel_reason | — | ret_qc_cancel_reason |
| Repair | name | — | ret_repair_master |
| Stock Issue Type | name | — | ret_stock_issue_types |
| Device | device_name | — | ret_bill_pay_device |
| Product Grouping | group_name | — | ret_product_grouping |
| Profession | profession | — | ret_profession |
| Size | units | — | ret_size |
| Old Metal Category | old_metal_cat | — | ret_old_metal_category |
| **Account Head** | **name** | — | **ret_account_head** |
| **Delivery Location** | **name** | — | **ret_sale_delivery** |
| **Old Metal Type** | **metal_type** | — | **ret_old_metal_type** |

## Verification
1. **Account Head Add** — Go to `admin_ret_catalog/ret_account/list`, click Add, type an existing account name (e.g., "TEA EXPENSES") → red border + toast on blur, save blocked on click
2. **Account Head Edit** — Click edit on an existing record, change name to a duplicate → blocked (current record excluded via excludeId from `#edit-id`)
3. **Old Metal Category Add** — Enter empty percentage field → validation error now correctly fires (was silently passing due to missing `#`)
4. **Old Metal Category modal** — When validation fails, modal stays open (previously closed immediately due to `data-dismiss="modal"`)
5. **Delivery Location** — Add a duplicate delivery name → blocked at blur and save
6. **Old Metal Type** — Add a duplicate metal type → blocked at blur and save
7. **Case-variant URLs** — `ret_account/Add` and `ret_account/add` both correctly intercepted
8. **Edit-mode blur** — Edit any record, change to duplicate value → red border (current record excluded, so changing to the SAME name doesn't flag)
9. **DB verify** — `SELECT name, COUNT(*) FROM ret_account_head WHERE status=1 GROUP BY name HAVING COUNT(*) > 1` should return 0 rows

## Notes
- The AJAX interceptor uses **synchronous XHR** for the duplicate check — this is intentional to block the save before it fires. Async would allow the save to proceed before the check completes.
- The `_extractFieldValue` helper checks FormData, object, and string data formats to handle all jQuery AJAX patterns used across the codebase.
- The `excludeId` for updates is extracted from the URL path (e.g., `/Update/123` → excludeId = `123`). A fallback now also checks `originalOptions.data` for the ID column when the URL doesn't contain the ID segment.
- Table name gotcha: `ret_screw` vs `ret_screw_type`, `ret_hook` vs `ret_hook_type` — the actual insert target is the `_type` variant. The non-`_type` aliases are kept in `allowed_tables` for backward compatibility.
- **Critical gotcha**: `ret_account` (module route name) ≠ `ret_account_head` (actual DB table). The `allowed_tables` and `status_columns` MUST reference the DB table name (`ret_account_head`), not the route name.
- **Input ID collisions**: Multiple modules share `id="name"` (Account Head, Delivery, CR/DR Ledger). Each MUST have a unique ID (e.g., `account_name`, `delivery_name`, `crdr_name`) to prevent jQuery selector cross-contamination.
- Case-insensitive URL matching (`lowerUrl.indexOf(urlKey.toLowerCase())`) is essential because CodeIgniter method names are case-sensitive in routes but developers use inconsistent casing (`Add` vs `add`).

## Changelog
- **2026-06-04 (v1)**: Initial recipe — 32 modules, name + code fields, edit-mode blur, multi-field AJAX interceptor
- **2026-06-04 (v2)**: Added Account Head (`ret_account_head`), Delivery Location (`ret_sale_delivery`), Old Metal Type (`ret_old_metal_type`) modules. Fixed `ret_account_head` missing from PHP `allowed_tables` + `status_columns`. Fixed Account Head `#name` → `#account_name` ID collision. Fixed `$('old_metal_perc')` → `$('#old_metal_perc')` selector typo. Removed `data-dismiss="modal"` from Old Metal Category save/update buttons. Added case-insensitive URL matching. Added edit-mode `excludeId` resolution in blur handler. Added AJAX interceptor `excludeId` fallback from POST data.
- **2026-06-04 (v3)**: Fixed Charges module DB column mapping from `charge_name` to `name_charge` in `DUPLICATE_MAP` and `_urlFieldsMap`. Documented Charges modal dismiss and programmatic close fixes.
