# Catalog Master — Centralized Input Validation Library (validation.js + applyRules)

> Real-time input sanitization for ALL catalog master fields — strips invalid characters on keypress, enforces max length, and prevents special character injection across 40+ modules.

## Metadata
- **Pattern ID**: PAT-VAL-021
- **Severity**: HIGH
- **Modules Affected**: All Catalog Master sub-modules (40+ modules — Category, Metal, Stone, Material, UOM, Screw, Hook, Making Type, Theme, Tag, Tax, Section, Sub Design, Floor, Floor Counter, Collection, Product Division, Karigar, Charges, QC Cancel Reason, Old Metal Category, Repair, Stock, Device, Product Grouping, Profession, Wallet, Size, Purity, Color, Cut, Clarity, Shape, Product, Account, CR/DR Ledger, Ledger, Paymode, Bank, Deposit, New Arrivals)
- **Auto-fixable**: Yes (file copy + append to catalog_master.js)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core input validation gap — no client has centralized validation

## Created By
- **Developer**: Antigravity (Black Horest)
- **Client**: VBC Jewellery (retailsource)
- **Date**: 2026-06-04
- **Source Bug ID**: N/A (enhancement)

## Symptom
1. Special characters (`@#$%^&*()!`) could be entered in name, code, and description fields
2. Input exceeding DB column width silently truncated by MySQL — data corruption
3. Each module had ZERO input validation — raw `$this->input->post()` directly to DB
4. No real-time feedback — users only saw errors (if any) after form submission
5. Copy-paste of invalid characters bypassed any existing HTML `pattern` attributes

## Root Cause
No centralized validation library existed. Each module's JS had ad-hoc or NO validation. The architecture needed:
1. A **common validation.js** library with regex patterns, rules, and auto-sanitization
2. A **field-to-rule mapping** in catalog_master.js via `Validation.applyRules()`
3. **Delegated event handlers** for dynamically added elements (modals)
4. **Paste event handling** to catch clipboard-injected invalid characters

## Detection
```command
# Check if validation.js exists
ls admin/assets/js/validation.js

# Check if validation.js is loaded in footer
grep -n "validation.js" admin/application/views/layout/footer.php

# Check if applyRules block exists in catalog_master.js
grep -n "Validation.applyRules" admin/assets/js/catalog_master.js

# Check if any input has data-validate attribute
grep -rn "data-validate" admin/application/views/master/
```

## Files
- `admin/assets/js/validation.js` — **NEW FILE** — Centralized validation library
- `admin/assets/js/catalog_master.js` — `Validation.applyRules()` block + modal reset handler
- `admin/application/views/layout/footer.php` — Script include (must load BEFORE catalog_master.js)

## Fix

### File 1: `admin/assets/js/validation.js` (NEW — entire file)

This is a new centralized library. Key components:

#### Regex Patterns (inverse — characters to STRIP)
```javascript
var PATTERNS = {
    textOnly:          /[^A-Za-z\s]/g,              // Letters + spaces only
    alphanumeric:      /[^A-Za-z0-9\s]/g,           // Letters + numbers + spaces
    alphanumericStrict:/[^A-Za-z0-9_\-]/g,           // No spaces, underscore + hyphen OK
    nameField:         /[^A-Za-z0-9\s_\-]/g,         // Letters, numbers, spaces, _, -
    numericOnly:       /[^0-9]/g,                    // Digits only
    decimal:           /[^0-9.]/g,                   // Digits + dot
    codeField:         /[^A-Za-z0-9]/g,              // Alphanumeric, no spaces (for codes)
    hsnCode:           /[^0-9]/g,                    // HSN: digits only
    email:             /[^A-Za-z0-9@._\-]/g,         // Email chars
    phone:             /[^0-9+\-\s]/g,               // Phone: digits, +, -, space
    gstNumber:         /[^A-Za-z0-9]/g,              // GST: alphanumeric
    panNumber:         /[^A-Za-z0-9]/g,              // PAN: alphanumeric
    pincode:           /[^0-9]/g,                    // 6-digit pincode
    address:           /[^A-Za-z0-9\s,.\-\/#\(\)]/g, // Address with punctuation
    description:       null,                         // No stripping (free text)
    generalText:       /[^A-Za-z0-9\s,.\-\/\(\)&\'"]/g
};
```

#### Validation Rules (regex + max length + message)
```javascript
var RULES = {
    nameField:     { regex: PATTERNS.nameField, max: 50,  msg: '...' },
    textOnly:      { regex: PATTERNS.textOnly,  max: 50,  msg: '...' },
    alphanumeric:  { regex: PATTERNS.alphanumeric, max: 30, msg: '...' },
    codeField:     { regex: PATTERNS.codeField, max: 10,  msg: '...' },
    shortCode:     { regex: PATTERNS.codeField, max: 6,   msg: '...' },
    numericOnly:   { regex: PATTERNS.numericOnly, max: 15, msg: '...' },
    decimal:       { regex: PATTERNS.decimal,   max: 15,  msg: '...' },
    hsnCode:       { regex: PATTERNS.hsnCode,   max: 10,  msg: '...' },
    // ... + catalog-specific rules (metalName, metalCode, categoryName, etc.)
};
```

#### Public API
```javascript
return {
    PATTERNS, RULES,
    sanitize(value, ruleName),      // Strip invalid chars + truncate
    isValid(value, ruleName),       // Returns true/false
    getMessage(ruleName),           // Get error message for a rule
    applyToElement($el, ruleName),  // Attach input+paste handlers to element
    applyRules(ruleMap),            // Bulk apply: { elementId: ruleName, ... }
    autoInit(),                     // Auto-scan data-validate attributes
    initDelegatedEvents(),          // Delegated handlers for dynamic elements
    checkDuplicate(options, cb)     // AJAX duplicate check (used by DUPLICATE_MAP)
};
```

### File 2: `admin/assets/js/catalog_master.js` — Field-to-Rule Mapping

#### Added Block (appended at end of file)
```javascript
$(function () {
  Validation.applyRules({
    // ── Category ──
    category_name:      'categoryName',
    ed_category_name:   'categoryName',
    hsn_code:           'hsnCode',
    ed_hsn_code:        'hsnCode',
    cat_code:           'shortCode',
    ed_cate_code:       'shortCode',

    // ── Metal ──
    metal_name:         'metalName',
    metal_code:         'metalCode',
    ed_metal_name:      'metalName',
    ed_metal_code:      'metalCode',

    // ── Stone ──
    stone_name:         'nameField',
    stone_code:         'shortCode',
    ed_stone_name:      'nameField',
    ed_stone_code:      'shortCode',

    // ── Material ──
    material_name:      'nameField',
    material_code:      'shortCode',
    ed_material_name:   'nameField',
    ed_material_code:   'shortCode',

    // ── UOM ──
    uom_name:           'nameField',
    uom_code:           'shortCode',
    ed_uom_name:        'nameField',
    ed_uom_code:        'shortCode',

    // ── Screw ──
    screw_name:         'nameField',
    screw_code:         'shortCode',
    ed_screw_name:      'nameField',
    ed_screw_code:      'shortCode',

    // ── Hook ──
    hook_name:          'nameField',
    hook_code:          'shortCode',
    ed_hook_name:       'nameField',
    ed_hook_code:       'shortCode',

    // ── Making Type ──
    making_name:        'nameField',
    making_short_code:  'shortCode',
    ed_making_name:     'nameField',
    ed_making_short_code:'shortCode',

    // ── Theme ──
    theme_name:         'nameField',
    theme_code:         'shortCode',
    ed_themename:       'nameField',
    ed_themeshortcode:  'shortCode',

    // ── Tag ──
    tag_name:           'nameField',
    ed_tagname:         'nameField',

    // ── Tax ──
    tax_name:           'nameField',
    tax_code:           'shortCode',
    ed_taxname:         'nameField',
    ed_tax_code:        'shortCode',

    // ── Section + Sub Design + Floor + Floor Counter + Collection + Division ──
    // ── Karigar + Charges + QC Reason + Old Metal + Repair + Stock + Device ──
    // ── Product Grouping + Profession + Wallet + Size + Purity + Color/Cut/Clarity ──
    // ── Account + Ledger + Paymode + Bank + Deposit ──
    // (120+ field-to-rule mappings total)
  });
});
```

### File 3: `admin/assets/js/catalog_master.js` — Add Modal Reset Handler

```javascript
// Auto-clear ALL form fields when Add modal opens
$('#confirm-add').on('show.bs.modal', function () {
    var $modal = $(this);
    $modal.find('input[type="text"], input[type="number"], textarea').val('');
    $modal.find('input[type="hidden"]').each(function () {
        var id = $(this).attr('id') || '';
        var name = $(this).attr('name') || '';
        if (id.indexOf('status') === -1 && name.indexOf('csrf') === -1) {
            $(this).val('');
        }
    });
    $modal.find('select:not([multiple])').prop('selectedIndex', 0);
    $modal.find('.help-block').html('');
    $modal.find('#error-msg').html('');
    $modal.find('.has-error').removeClass('has-error');
    $modal.find('input[type="checkbox"].status').bootstrapSwitch('state', true);
});
```

### File 4: `admin/application/views/layout/footer.php` — Script Include

#### Before
```html
<script src="<?php echo base_url(); ?>admin/assets/js/catalog_master.js"></script>
```

#### After (validation.js MUST load first)
```html
<script src="<?php echo base_url(); ?>admin/assets/js/validation.js"></script>
<script src="<?php echo base_url(); ?>admin/assets/js/catalog_master.js"></script>
```

## How It Works

1. **On page load** → `Validation.autoInit()` scans all `[data-validate]` elements
2. **On DOM ready** → `Validation.applyRules({...})` maps 120+ fields to rules by ID
3. **On keypress** → `input.lmxValidate` event strips invalid chars in real-time, preserves cursor position
4. **On paste** → `paste.lmxValidate` event sanitizes pasted content after 10ms delay
5. **On dynamic elements** → `initDelegatedEvents()` uses document-level delegation for modals

## Module Coverage Table

| Module | Name Rule | Code Rule | Other Fields |
|--------|----------|----------|-------------|
| Category | categoryName (max 30) | shortCode (max 6) | hsnCode, description |
| Metal | metalName (max 20) | metalCode (max 6) | nameField (display) |
| Stone | nameField (max 50) | shortCode (max 6) | — |
| Material | nameField (max 50) | shortCode (max 6) | — |
| UOM | nameField (max 50) | shortCode (max 6) | — |
| Screw | nameField (max 50) | shortCode (max 6) | — |
| Hook | nameField (max 50) | shortCode (max 6) | — |
| Making Type | nameField (max 50) | shortCode (max 6) | — |
| Theme | nameField (max 50) | shortCode (max 6) | description |
| Tag | nameField (max 50) | — | — |
| Tax | nameField (max 50) | shortCode (max 6) | — |
| Section | nameField (max 50) | shortCode (max 6) | — |
| Shape | shapeName (max 30) | — | description |
| Product | productName (max 50) | productCode (max 6) | description, markup |
| Karigar | textOnly (max 50) | codeField (max 10) | description |
| Charges | nameField (max 50) | numericOnly (max 15) | charge_tax (decimal), description |
| Bank | nameField (max 50) | codeField (IFSC) | — |
| Wallet | nameField (max 50) | shortCode (max 6) | — |
| Profession | textOnly (max 50) | — | — |
| + 20 more modules... | | | |

## Verification
1. **Type special chars** → Characters are stripped instantly as you type (no alert needed)
2. **Paste invalid text** → Pasted text sanitized within 10ms
3. **Exceed max length** → Text truncated at max (e.g., 6 chars for shortCode)
4. **Check cursor position** → Cursor stays in correct position after stripping
5. **Dynamic modals** → Open Add modal → type special chars → stripped correctly
6. **Console check** → `Validation.isValid('Test@#$', 'nameField')` → returns `false`
7. **Console sanitize** → `Validation.sanitize('Test@#$', 'nameField')` → returns `'Test'`

## Notes
- validation.js uses the **IIFE module pattern** — all functions are namespaced under `Validation.*`
- Regex patterns use **inverse matching** (strip what's NOT allowed) — this is safer than allowlisting
- `description` rule has `regex: null` — descriptions allow free text, only max length enforced
- The `applyRules()` call in catalog_master.js covers both Add AND Edit fields (ed_ prefix)
- Modal form reset handler clears stale data when Add modal opens — prevents "edit leaks into add"
- This recipe supersedes `recipe_shape_special_chars_length_overflow.md` (PAT-VAL-011) which was a single-module inline fix
