# Recipe: Dependent Dropdown Defaults Not Set (Fire-and-Forget AJAX Anti-Pattern)

## Metadata
- **Pattern ID**: PAT-AJAX-CHAIN-001
- **Severity**: MEDIUM
- **Modules Affected**: ret_purchase / supplier_rate_cut (Approval to Invoice Conversion)
- **Auto-fixable**: No (requires client-specific default IDs — not portable as-is)

## Client Scope
- **Applies to**: amman (erp.sriammanjewellers.in) — hardcoded IDs are client-specific
- **Reason**: Default IDs (Gold=1, Bullion Gold=5, Pure Metal=135) are specific to this client's database. Other clients may have different IDs for the same values.

## Created By
- **Developer**: Antigravity AI
- **Client**: amman (erp.sriammanjewellers.in)
- **Date**: 2026-04-24
- **Source Bug ID**: N/A

## Symptom
On the Approval to Invoice Conversion Add form, the Metal, Category, and Product dropdowns are empty or not pre-selected on page load. The user must manually select all three before they can proceed, even though the business always starts with Gold / Bullion Gold / Pure Metal.

## Root Cause
The `case 'add':` init block fires `get_ActiveMetals()`, `get_ActiveCategories()`, and `get_CategoryProducts()` as independent fire-and-forget AJAX calls. Because AJAX is asynchronous, there is no guarantee the metals dropdown is populated before trying to set its value, and no chain exists to load categories after metals, or products after categories.

Additionally, the `#select_metal` change handler is skipped for the `supplier_rate_cut` page (line ~7740), so the standard cascade does not trigger automatically.

**The anti-pattern:**
```javascript
// ❌ All three fire simultaneously — no sequencing, no defaults
get_ActiveMetals();       // async, no callback
get_ActiveCategories();   // fires immediately, metals not loaded yet
get_CategoryProducts();   // fires immediately, categories not loaded yet
```

## Detection
```powershell
# Find fire-and-forget AJAX calls in case 'add' blocks
Select-String -Path "admin\assets\js\ret_purchase_order.js" -Pattern "get_ActiveMetals\(\)|get_ActiveCategories\(\)|get_CategoryProducts\(\)"
```

## Files
- `admin/assets/js/ret_purchase_order.js` (case 'add' init block + new helper functions)

## Fix

### Pattern: Named Helper Functions + `.then()` Reference Chain

The key insight is: **pass the function reference to `.then()`, not the function call result**.

```javascript
// ✅ CORRECT — fn reference, runs AFTER previous promise resolves
src_load_metals().then(src_load_categories).then(src_load_products)

// ❌ WRONG — fn call, runs immediately (right now, not after)
src_load_metals().then(src_load_categories()).then(src_load_products())
```

### Before (fire-and-forget in case 'add')
```javascript
case 'add':
    get_returned_po_details();
    get_ActiveKaigar();
    get_bank_details();
    //get_ActiveCategories();    // ← dead comment
    get_ActiveCategories();      // ← parallel, no default
    get_CategoryProducts();      // ← parallel, no default
    get_ActiveMetals();          // ← parallel, no default
    src_get_branchname();
    $('#select_category').select2({ placeholder: "Select Category", allowClear: true });
    ...
    break;
```

### After (sequential .then() chain with .fail() safety net)
```javascript
case 'add':
    get_returned_po_details();
    get_ActiveKaigar();
    get_bank_details();
    src_get_branchname();
    $("#select_po_ref_no").select2({ placeholder: "Select PO NO", closeOnSelect: true });
    $('#pur_fin_year_select').select2({});

    // Sequential default-loading chain: Gold → Bullion Gold → Pure Metal
    src_load_metals()
        .then(src_load_categories)
        .then(src_load_products)
        .fail(function() {
            // Safety net: if chain fails, fall back to original independent calls
            get_ActiveMetals();
            get_ActiveCategories();
            get_CategoryProducts();
        });
    break;
```

### Helper Functions (added near get_supplier_ratecut_details)
```javascript
// Helper 1: load metals → set Gold as default (id_metal = 1)
function src_load_metals() {
    return $.ajax({ type: 'GET', url: base_url + 'index.php/admin_ret_catalog/active_metals', dataType: 'json' })
    .then(function(data) {
        metalDetails = data;
        $('#select_metal option').remove();
        $.each(data, function(key, item) {
            $('#select_metal').append($('<option>').val(item.id_metal).text(item.metal));
        });
        $('#select_metal').select2({ placeholder: "Metal", allowClear: true });
        $('#select_metal').val(1).trigger('change.select2'); // default: Gold
    });
}

// Helper 2: load categories for Gold → set Bullion Gold (id_ret_category = 5)
function src_load_categories() {
    return $.ajax({ type: 'POST', url: base_url + 'index.php/admin_ret_catalog/get_MetalCategory',
                    data: { 'id_metal': 1, 'id_cat_type': '' }, dataType: 'json' })
    .then(function(cats) {
        category_lists = cats;
        $('#select_category option').remove();
        $.each(cats, function(k, cat) {
            $('#select_category').append(
                $('<option>').val(cat.id_ret_category).attr('data-cattype', cat.cat_type).text(cat.name)
            );
        });
        $('#select_category').select2({ placeholder: "Select Category", allowClear: true });
        $('#select_category').val(5).trigger('change.select2'); // default: Bullion Gold
    });
}

// Helper 3: load products for Bullion Gold → set Pure Metal (pro_id = 135)
function src_load_products() {
    return $.ajax({ type: 'POST', url: base_url + 'index.php/admin_ret_catalog/get_ActiveProducts/',
                    data: { 'id_ret_category': 5 }, dataType: 'json' })
    .then(function(prods) {
        $('#select_product option').remove();
        $.each(prods, function(p, prod) {
            $('#select_product').append(
                $('<option>').val(prod.pro_id)
                    .attr('data-purmode', prod.purchase_mode)
                    .attr('data-tax_type', prod.tax_type)
                    .attr('data-stone_type', prod.stone_type)
                    .attr('data-calculation_based_on', prod.calculation_based_on)
                    .text(prod.product_name)
            );
        });
        $('#select_product').select2({ placeholder: "Product", allowClear: true });
        $('#select_product').val(135).trigger('change.select2'); // default: Pure Metal
    });
}
```

## Verification
1. Navigate to `Purchase → Approval to Invoice → Add`
2. Verify Metal = **Gold**, Category = **Bullion Gold**, Product = **Pure Metal** are pre-selected on load (without any user interaction)
3. Verify all three dropdowns are still user-changeable (not locked/disabled)
4. (Optional stress test) Throttle network in DevTools → verify `.fail()` fallback fires and dropdowns still load

## Notes
- **Why `.then()` and not `.done()`?** `.done(fn)` fires on the current promise only — all `.done()` handlers run simultaneously. `.then(fn)` returns a **new promise** that resolves to whatever `fn` returns (the next AJAX call), making the chain truly sequential.
- **The `.fail()` safety net** ensures the page degrades gracefully — dropdowns load independently (without defaults) even if the server is slow or any request errors
- For other clients: verify the IDs (Gold, Bullion Gold, Pure Metal) from the database before hardcoding. Use `SELECT id_metal FROM metal WHERE metal='Gold'` etc.
- Remove the original `get_ActiveMetals()`, `get_ActiveCategories()`, `get_CategoryProducts()` calls from the `case 'add'` block to prevent double-populating dropdowns
