# Recipe: Tag Rate Field Null DOM Lookup Fix

## Metadata
- **Pattern ID**: PAT-JS-DOM-001
- **Severity**: HIGH
- **Modules Affected**: Estimation (Tag Load), potentially Billing (Tag Load)
- **Auto-fixable**: Yes
- **Date**: 2026-04-18

## Client Scope
- **Applies to**: All clients using ret_estimation module with tag scanning

## Created By
- **Developer**: Antigravity
- **Client**: etail_development_src
- **Date**: 2026-04-18

## Symptom
When loading a tag in the Add Estimation screen, the Rate field shows 0.00 even though the Product, Design, Sub Design, and Purity are properly configured. However, the same item added through Home Bill section shows the correct rate.

## Root Cause
The `rate_field` value comes from a SQL `LEFT JOIN` on `ret_metal_purity_rate` table. When the LEFT JOIN returns NULL (no matching row for the metal+purity combination), JavaScript receives `null`. The condition `rate_field != ''` evaluates to `true` in JS (because `null != ''` is `true`), so the code enters the ornament branch and tries `$('.null').html()` which returns `undefined` and rate = 0.

Additionally, unlike the Home Bill section which uses a `purity_rate` array fallback and an AJAX call to `get_metal_purity_rate`, the tag load path had no fallback mechanism.

**Source**: `admin/assets/js/ret_estimation.js`
- `append_tag_details()` function - rate initialization on tag load
- `calculatetag_SaleValue()` function - rate recalculation

## Detection
```command
grep -n "rate_field != '' && val.stone_type==0" admin/assets/js/ret_estimation.js
grep -n "isNaN(\$('.'+rate_field).html())" admin/assets/js/ret_estimation.js
```
Look for DOM lookups using `rate_field` without null checks.

## Files
- `admin/assets/js/ret_estimation.js` (append_tag_details function ~line 28408, calculatetag_SaleValue ~line 11667)

## Fix

### Before (append_tag_details - rate initialization)
```javascript
else if (rate_field != '' && val.stone_type==0) // For Ornaments Product
{
    if (manualRates[val.tag_id] && manualRates[val.tag_id] != '') {
        rate_per_grm = manualRates[val.tag_id];
    } else {
        rate_per_grm = $('.' + rate_field).html() || 0;
    }
}
```

### After (append_tag_details - with null check + purity_rate fallback)
```javascript
else if (rate_field != null && rate_field != '' && rate_field != 'null' && val.stone_type==0) // For Ornaments Product
{
    if (manualRates[val.tag_id] && manualRates[val.tag_id] != '') {
        rate_per_grm = manualRates[val.tag_id];
    } else {
        rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();
    }

    // Fallback: if DOM lookup returned 0, try purity_rate array (same as Home Bill)
    if (rate_per_grm == 0 && typeof purity_rate !== 'undefined' && purity_rate.length > 0) {
        $.each(purity_rate, function (k, purval) {
            if (purval.id_purity == val.purity && purval.id_metal == val.id_metal) {
                let fallback_rate_field = purval.rate_field;
                if (fallback_rate_field != null && fallback_rate_field != '') {
                    let fallback_rate = (isNaN($('.' + fallback_rate_field).html()) || $('.' + fallback_rate_field).html() == '') ? 0 : $('.' + fallback_rate_field).html();
                    if (fallback_rate > 0) {
                        rate_per_grm = fallback_rate;
                    }
                }
            }
        });
    }
}
```

### Before (calculatetag_SaleValue - rate_field_value)
```javascript
var rate_field_value = (isNaN($('.'+rate_field).html()) ||$('.'+rate_field).html() == '')  ? 0 : $('.'+rate_field).html();
```

### After (calculatetag_SaleValue - with null check)
```javascript
var rate_field_value = (rate_field != null && rate_field != '' && rate_field != 'null' && !isNaN($('.'+rate_field).html()) && $('.'+rate_field).html() != '')  ? $('.'+rate_field).html() : 0;
```

## Verification
1. Go to Add Estimation -> Scan tag with known Product/Design/SubDesign/Purity
2. Rate field should populate with the correct metal rate
3. Verify Rate matches Home Bill section for same configuration
4. Test both New Tag Scan and OLD Tag Scan paths
5. Verify cost/tax calculations use the populated rate

## Notes
- The `purity_rate` array is loaded on page init via `get_purity_rate()` - it's always available
- The `id_metal` field is returned in the tag scan query at `c.id_metal` (line 1115 in model)
- This pattern (null rate_field from LEFT JOIN) can occur in any module that joins `ret_metal_purity_rate`
- There are ~13 instances of `$('.' + rate_field).html()` in the file; only the tag-load-specific ones were fixed as they're the only ones triggered before user interaction ensures rate_field is valid
