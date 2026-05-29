# 🔬 Round 5 — JS Calculations & Data Binding Deep Dive

> **Audit Date**: 2026-02-17  
> **Method**: Sequential analysis of JS calculation functions, tag scan/search AJAX binding, row creation, and tax computation  
> **Scope**: `ret_estimation.js` — functions: `get_tag_data` (×3 copies), `get_tag_barcode_data`, `calculateCatalogItemSaleValue`, `calculateCustomItemSaleValue`, `create_new_empty_est_custom_row`  
> **Lines Analysed**: L580–2500 (tag scan/search), L6600–7600 (row creation), L9100–9500 (calculation engine)

---

## Executive Summary

**9 new bugs identified**: **2 P0 (critical)**, **3 P1 (major)**, **3 P2 (minor)**, **1 P3 (cosmetic)**.

> [!CAUTION]
> Two critical financial calculation bugs: (1) market rate tax computation uses the WRONG variable (`base_value_tax` instead of `market_base_value_tax`), causing market rate comparisons to display incorrect tax amounts. (2) `get_tag_barcode_data` has an undefined variable (`items.tag_id`) that silently bypasses the duplicate tag check, allowing the same tag to be added multiple times.

---

## Bug Details

---

### EST-R501 — Market Rate Tax Uses Wrong Variable (P0)

**File**: `ret_estimation.js` **Lines**: 9125–9133  
**Severity**: P0 — Financial Calculation  

The market rate tax calculation reuses the regular rate's `base_value_tax` and `base_value_amt` instead of computing from the market rate:

```javascript
// L9125-9133 — Market rate tax calculation
var market_base_value_tax = parseFloat(calculate_base_value_tax(market_rate_with_mc, tax_group)).toFixed(2);

var market_base_value_amt = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(base_value_tax)).toFixed(2);
//                                                                          ^^^^^^^^^^^^^ ❌ WRONG: should be market_base_value_tax

var market_arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);
//                                                                    ^^^^^^^^^^^^^^ ❌ WRONG: should be market_base_value_amt

var market_arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);
//                                                    ^^^^^^^^^^^^^^              ^^^^^^^^^^^^^^^^^^ ❌ BOTH WRONG

market_total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);
//                                            ^^^^^^^^^^^^^^              ^^^^^^^^^^^^^^^^^^ ❌ BOTH WRONG
// Should be: market_base_value_tax + market_arrived_value_tax
```

**Impact**: The market rate tax calculation is identical to the regular rate — `market_total_tax_rate` always equals `total_tax_rate`. The entire market rate comparison is meaningless. This affects all catalog and custom item calculations where market rate is displayed as a reference.

---

### EST-R502 — `get_tag_barcode_data` Uses Undefined `items.tag_id` (P0)

**File**: `ret_estimation.js` **Line**: 2363  
**Severity**: P0 — Data Integrity  

```javascript
// L2361-2367 — Inside get_tag_barcode_data()
$('#estimation_tag_details > tbody > tr').each(function (idx, row) {
    if (items.tag_id == $(this).find('.est_tag_id').val()) {    // ❌ 'items' is UNDEFINED in this scope
        $.toaster({ priority: 'danger', title: 'Warning!', message: 'Tag Already Exists ..' });
        rowExist = true;
    }
});
```

The variable iterating the AJAX response is `data`, not `items`. `items` doesn't exist in this function's scope, so `items.tag_id` is always `undefined`. The duplicate check will NEVER match, allowing the same tag to be added to the estimation unlimited times.

**Compare with `get_tag_data` (L1051)** which correctly uses `data[0].tag_id`.

---

### EST-R503 — `get_tag_barcode_data` Uses Undefined `rate_per_grm` (P1)

**File**: `ret_estimation.js` **Lines**: 2419, 2471  
**Severity**: P1 — Financial Calculation  

```javascript
// L2419 — Inside get_tag_barcode_data() — first branch
+ '<td><input type="text" class="market_rate_value" name="est_tag[est_rate_per_grm][]" ' + rate_readonly + ' value="' + rate_per_grm + '" /></td>'
//                                                                                                                     ^^^^^^^^^^^^^ ❌ NEVER DEFINED
```

`rate_per_grm` is never assigned in `get_tag_barcode_data()`. It's defined in `get_tag_data()` but not here. This will inject `undefined` or `NaN` into the rate field, resulting in either an empty rate or $NaN being displayed and submitted.

---

### EST-R504 — Mixed Metal Tags Allowed (Metal Type Check Removed) (P1)

**File**: `ret_estimation.js` **Line**: 1579–1601 (vs L1059–1065)  
**Severity**: P1 — Business Rule  

The old `get_tag_data_29_09` function (L1059) checks metal type:
```javascript
// L1059 — OLD version (get_tag_data_29_09)
if (data[0].metal_type != $(this).find('.metal_type').val()) {
    $.toaster({ priority: 'danger', message: 'Please Add The Same Metal Items ..' });
    rowExist = true;
}
```

The current `get_tag_data` function (L1579) only checks for duplicate tags, but **does NOT check metal type**:
```javascript
// L1589-1601 — CURRENT version (get_tag_data)
$('#estimation_tag_details > tbody > tr').each(function (idx, row) {
    if (val.tag_id == $(this).find('.est_tag_id').val()) {
        $.toaster({ message: 'Tag Already Exists..' });
        rowExist = true;
    }
    // ❌ NO metal_type check — removed during refactor
});
```

**Impact**: Users can now mix gold and silver tag items in the same estimation. This may cause incorrect total calculations, wrong tax grouping, and invalid invoice generation.

---

### EST-R505 — Division by Zero in Diamond Cent Weight (P1)

**File**: `ret_estimation.js` **Lines**: 1191, 1697  
**Severity**: P1 — Runtime Error  

```javascript
// L1191 and L1697
var pcs  = (isNaN(val.piece) || val.piece == '')  ? 0 : parseInt(val.piece);
var grs_wt  = (isNaN(val.gross_wt) || val.gross_wt == '')  ? 0 : parseFloat(val.gross_wt);
product_centwt = parseFloat(((grs_wt)/(pcs))*100).toFixed(3);
//                                     ^^^ ❌ Division by zero when pcs == 0 → Infinity
```

When a diamond product has `piece = 0` (or null/empty, which defaults to 0), the calculation produces `Infinity`, which then fails the cent weight comparison and results in `rate_per_grm` remaining 0.

**Impact**: Diamond products with 0 pieces will get a rate of 0 instead of throwing an error or using a fallback.

---

### EST-R506 — `$('.cat_tax_per').val()` Sets ALL Rows' Tax (P2)

**File**: `ret_estimation.js` **Line**: 9143  
**Severity**: P2 — Data Integrity  

```javascript
// L9143 — Inside calculateCatalogItemSaleValue
$('.cat_tax_per').val(taxitem.tax_percentage);    // ❌ Class selector — sets ALL rows
```

This uses a class selector (`.cat_tax_per`) instead of scoping to `curRow`. Every catalog row gets the same tax percentage — the one from the last matching tax item. Should be:
```javascript
curRow.find('.cat_tax_per').val(taxitem.tax_percentage);    // ✅ Scoped to current row
```

**Impact**: If different catalog items have different tax groups, all rows show the same tax %. The hidden fields are correctly scoped, so this only affects the displayed value — but it's misleading to the user.

---

### EST-R507 — Collection Confirm Modal Fires O(n²) Times (P2)

**File**: `ret_estimation.js` **Lines**: 1345–1357  
**Severity**: P2 — UX  

```javascript
// L1343-1357 — After adding a tag via get_tag_data_29_09
if (collection_details.length > 0) {
    $.each(collection_details, function (key, items) {           // For each collection item...
        $('#estimation_tag_details > tbody').each(function (idx, row) {   // ...check every row
            if (items.tag_id != $(this).find('.est_tag_id').val()) {
                $('#id_tag_mapping').val(data[0].id_tag_mapping);
                $('#collection_confirm').modal('show');           // ❌ Modal shown per non-match
            }
        });
    });
}
```

The modal fires for every tag row that DOESN'T match each collection item. With 3 collection items and 5 table rows, the modal tries to show `3 × 5 = 15` times (minus matches). jQuery modals stack, causing flickering and potential browser freeze.

**Fix**: Should check if ANY collection tag exists, then show modal once.

---

### EST-R508 — Employee Dropdown Creates Duplicate Options (P2)

**File**: `ret_estimation.js` **Lines**: 7487–7503  
**Severity**: P2 — UX  

```javascript
// L7487-7503 — create_new_empty_est_custom_row
$.each(emp_details, function (pkey, emp) {
    select_emp += "<option value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";

    var lastrowemp = $('#estimation_custom_details > tbody').find('tr:last td:eq(1) .item_emp_id').val();

    if (lastrowemp == emp.id_employee) {
        // ❌ Adds a SECOND <option> for the selected employee
        select_emp += "<option  selected = 'selected' value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";
    }
});
```

When an employee matches the last row's selected employee, both a non-selected AND a selected `<option>` are added. The dropdown shows the employee name twice — once unselected and once selected.

---

### EST-R509 — Inclusive Tax Re-Declares `total_tax_rate` with `var` (P3)

**File**: `ret_estimation.js` **Line**: 9167  
**Severity**: P3 — Code Quality  

```javascript
// L9161-9169
if ((calculation_type == 3 || calculation_type == 4) && (tax_type == 1)) {
    total_price = rate_with_mc;
    var total_tax_rate = parseFloat(calculate_inclusiveGST(rate_with_mc, tax_group)).toFixed(2);
    //  ^^^ ❌ 'var' re-declaration inside block — hoisted to function scope, overwrites the outer total_tax_rate
}
```

Using `var` inside the `if` block re-declares and overwrites the `total_tax_rate` set earlier at L9123. While the logic appears intentional (override for inclusive tax), the `var` also causes the variable to be `undefined` if the `if` condition is false but the variable is referenced later — a subtle hoisting edge case. Should use assignment without `var`.

---

## Summary Table

| ID | Sev | Category | Description |
|---|---|---|---|
| EST-R501 | **P0** | Financial | Market rate tax uses wrong variable — `base_value_tax` instead of `market_base_value_tax` |
| EST-R502 | **P0** | Logic | `get_tag_barcode_data` uses undefined `items.tag_id` — duplicate check always bypasses |
| EST-R503 | **P1** | Financial | `get_tag_barcode_data` uses undefined `rate_per_grm` — NaN in rate fields |
| EST-R504 | **P1** | Business Rule | Current `get_tag_data` removed metal type check — allows mixing gold/silver |
| EST-R505 | **P1** | Runtime Error | Division by zero in diamond cent weight when `piece == 0` |
| EST-R506 | **P2** | Display | `$('.cat_tax_per').val()` sets ALL catalog rows' displayed tax (class selector vs row) |
| EST-R507 | **P2** | UX | Collection confirm modal fires O(n²) times — once per non-matching row per collection |
| EST-R508 | **P2** | UX | Employee dropdown adds duplicate `<option>` for pre-selected employee |
| EST-R509 | **P3** | Code Quality | `var total_tax_rate` re-declaration inside `if` block — hoisting edge case |

---

## Sprint Recommendation

### Sprint 1 (Critical — Immediate)
- **EST-R501**: Fix 4 variable references at L9127-9133 (market_base_value_tax, market_base_value_amt, market_arrived_value_tax)
- **EST-R502**: Change `items.tag_id` → `data[0].tag_id` at L2363
- **EST-R503**: Define `rate_per_grm` from rate lookup (copy pattern from `get_tag_data`)

### Sprint 2 (Important)
- **EST-R504**: Re-add metal type check to current `get_tag_data` at L1579
- **EST-R505**: Add guard `if (pcs > 0)` before division at L1191/1697
- **EST-R506**: Change `$('.cat_tax_per')` → `curRow.find('.cat_tax_per')` at L9143

### Sprint 3 (Cleanup)
- **EST-R507**: Refactor collection confirm to single-modal pattern
- **EST-R508**: Use `selected` attribute instead of duplicate option
- **EST-R509**: Remove `var` keyword at L9167
