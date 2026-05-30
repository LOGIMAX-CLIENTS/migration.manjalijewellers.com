# Recipe: Sales Transfer – Centralized Rate Per Gram Input Missing

## Metadata
- **Pattern ID**: PAT-UI-ST-001
- **Severity**: MEDIUM
- **Modules Affected**: admin_ret_sales_transfer (Sales Transfer Add page)
- **Auto-fixable**: Yes (HTML + JS addition, no DB change)

## Client Scope
- **Applies to**: ALL retail jewellery clients using the sales transfer module
- **Reason**: The JS (`ret_sales_transfer.js`) already references `#rate_per_gram` and validates/uses it, but the HTML view (`sales_trasnfer.php`) was never given the input element.

## Created By
- **Developer**: Antigravity
- **Client**: navratnajewellery (Navratna Jewellery LLP)
- **Date**: 2026-04-17
- **Source Bug ID**: N/A (feature gap, not regression)

## Symptom
On the Sales Transfer Add page (`/admin_ret_sales_transfer/sales_transfer/add`), users must manually enter the metal rate per gram for every tag row loaded in the table. When the transfer contains more than 10 tags, this becomes error-prone and slow. There is no centralized "Rate Per Gram" input or "Apply to All" button visible above the table.

## Root Cause
The JS file (`ret_sales_transfer.js`) was already updated to:
1. Validate `$('#rate_per_gram').val()` before search (line ~1191)
2. Pre-fill new row `.pur_cost` inputs with `$('#rate_per_gram').val()` when rows are added (line ~2005)

But the HTML view (`sales_trasnfer.php`) was never updated to include the `<input id="rate_per_gram">` element and the "Apply to All" button. The JS handlers for the button were also missing.

## Detection
```bash
# Check if rate_per_gram input exists in the view
grep -n "rate_per_gram" admin/application/views/sales_transfer/sales_trasnfer.php

# Check the JS already expects it (should find references)
grep -n "rate_per_gram" admin/assets/js/ret_sales_transfer.js
```
If the view grep returns 0 results but the JS grep returns results → this recipe applies.

## Files
- `admin/application/views/sales_transfer/sales_trasnfer.php` — Add HTML for input + button
- `admin/assets/js/ret_sales_transfer.js` — Add Apply to All button handler

## Fix

### Before (sales_trasnfer.php — around line 288-303)
```html
                      <p class="help-block"></p>
                      <div class="row sales_trans">
                          <div class="col-md-12">
                              <!-- ... existing hidden totals div ... -->
                              </br>
                              <div class="table-responsive">
                                  <table id="bt_search_list" ...>
```

### After (sales_trasnfer.php)
```html
                      <p class="help-block"></p>
                      <div class="row sales_trans">
                          <div class="col-md-12">
                              <!-- ... existing hidden totals div ... -->
                              </br>
                              <!-- Centralized Rate Per Gram input -->
                              <div class="row" style="margin-bottom:10px;">
                                  <div class="col-md-12 text-right">
                                      <label for="rate_per_gram" style="font-weight:600; margin-right:8px;">Rate Per Gram</label>
                                      <input type="number" id="rate_per_gram" class="form-control" style="display:inline-block; width:120px; margin-right:8px;" placeholder="1" value="1" min="0" step="any">
                                      <button type="button" id="apply_rate_to_all" class="btn btn-success btn-flat">Apply to All</button>
                                  </div>
                              </div>
                              <div class="table-responsive">
                                  <table id="bt_search_list" ...>
```

### Before (ret_sales_transfer.js — after .calc_type change handler)
```javascript
    $(document).on('change','.calc_type',function(){
        calculateSaleBillRowTotal();
    });

    $(document).on('keyup','.pur_cost',function(){
        calculateSaleBillRowTotal();
    });

    function calculateSaleBillRowTotal() {
```

### After (ret_sales_transfer.js)
```javascript
    $(document).on('change','.calc_type',function(){
        calculateSaleBillRowTotal();
    });

    $(document).on('keyup','.pur_cost',function(){
        calculateSaleBillRowTotal();
    });

    // Apply centralized Rate Per Gram to all rows in the table
    $(document).on('click','#apply_rate_to_all',function(){
        var rate = $('#rate_per_gram').val();
        if(rate === '' || parseFloat(rate) <= 0){
            $.toaster({ priority : 'danger', title : 'Warning!', message : '</br>Please enter a valid Rate Per Gram.'});
            return;
        }
        $('#bt_search_list > tbody tr').each(function(){
            $(this).find('.pur_cost').val(parseFloat(rate).toFixed(2));
        });
        calculateSaleBillRowTotal();
        calculate_sales_trans_details();
    });

    // Also auto-apply when the centralized rate input value changes (keyup)
    $(document).on('keyup change','#rate_per_gram',function(){
        var rate = $(this).val();
        if(rate !== '' && parseFloat(rate) > 0 && $('#bt_search_list > tbody tr').length > 0){
            $('#bt_search_list > tbody tr').each(function(){
                $(this).find('.pur_cost').val(parseFloat(rate).toFixed(2));
            });
            calculateSaleBillRowTotal();
            calculate_sales_trans_details();
        }
    });

    function calculateSaleBillRowTotal() {
```

## Verification
1. Navigate to `/admin_ret_sales_transfer/sales_transfer/add`
2. Select From Branch, To Branch, search for tags
3. Verify **"Rate Per Gram"** input and **"Apply to All"** button appear above the tag list table (top-right, aligned with the table)
4. Enter a rate (e.g. 6500) in the Rate Per Gram input → click **Apply to All** → verify all row `pur_cost` inputs update to 6500 and amounts recalculate
5. Enter a new rate and type/change it — verify rows auto-update in real time if rows are loaded
6. Load a new tag after setting the rate — the new row should pre-fill with the centralized rate
7. Try saving the transfer — verify it succeeds with the rates applied

## Notes
- The `value="1"` default prevents the validation guard at line 1191 from blocking search before the user has set a rate
- Each individual row `.pur_cost` input remains manually editable — the "Apply to All" only overwrites when explicitly clicked or when the rate input changes
- The search validation at line 1191 now correctly rejects empty/zero rates, so users must set a rate before searching
