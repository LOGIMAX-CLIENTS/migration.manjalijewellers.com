# Estimation Edit — Customer Address Slider Fields Not Populated

> Save validation fails with address/pincode/country/state alerts because customer slider fields are not populated on edit page load.

## Metadata
- **Pattern ID**: PAT-EST-SLIDER-001
- **Severity**: HIGH
- **Modules Affected**: Estimation
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal pattern — estimation edit form shares this validation/population mismatch in all deployments

## Created By
- **Developer**: Antigravity AI
- **Client**: Rajathangamaligai (staging)
- **Date**: 2026-06-12
- **Source Bug ID**: N/A

## Symptom
When editing an estimation and clicking "Save and Print", four sequential alerts appear:
1. "Customer Address Not Available..!"
2. "Customer Pincode Not Available..!"
3. "Customer Country Not Available..!"
4. "Customer State Not Available..!"

The estimation cannot be saved. Opening the customer slider (edit button) and closing it without changes makes the save work.

## Root Cause
The save button handler (`#est_print` click) validates customer address fields from the **customer slider form**:
- `$('#address1')`, `$('#pin_code_add')`, `$('#id_country')`, `$('#id_state')`

On edit page load, `estimation_tag_data()` is called after a 2-second timeout to load estimation data. It populates **different hidden fields**:
- `$('#cus_del_address1')`, `$('#cus_del_pincode')`, `$('#cus_del_state')`, `$('#cus_del_country')`

The slider fields only get populated when `get_customer()` is triggered by clicking the `#edit_customer` button (in `ret_general.js`). So the validation fields remain empty on edit load, causing all four checks to fail.

## Detection
```command
grep -n "ask_cus_addr1\|ask_cus_pincode\|ask_cus_country\|ask_cus_state" admin/assets/js/ret_estimation.js
```
Look for validation checks referencing `#address1`, `#pin_code_add`, `#id_country`, `#id_state` in the save handler.

Then check `estimation_tag_data()` to see if those fields are populated — if only `#cus_del_*` fields are set, the bug exists.

## Files
- `admin/assets/js/ret_estimation.js` — `estimation_tag_data()` success callback

## Fix

### Before
```javascript
$('#cus_del_address1').val(data.estimation.address1);

$('#cus_del_pincode').val(data.estimation.pincode);

$('#cus_del_state').val(data.estimation.id_state);

$('#cus_del_country').val(data.estimation.id_country);

$('#cmp_state').val(data.estimation.id_state);

$('#cmp_country').val(data.estimation.id_country);



				// CHIT DETAILS ENDS
```

### After
```javascript
$('#cus_del_address1').val(data.estimation.address1);

$('#cus_del_pincode').val(data.estimation.pincode);

$('#cus_del_state').val(data.estimation.id_state);

$('#cus_del_country').val(data.estimation.id_country);

$('#cmp_state').val(data.estimation.id_state);

$('#cmp_country').val(data.estimation.id_country);

$('#address1').val(data.estimation.address1);

$('#pin_code_add').val(data.estimation.pincode);

$('#id_country').val(data.estimation.id_country);

$('#id_state').val(data.estimation.id_state);



				// CHIT DETAILS ENDS
```

## Verification
1. Open any existing estimation in edit mode (`/admin_ret_estimation/estimation/edit/{id}`)
2. Click "Save and Print" immediately — **without** opening the customer slider
3. Verify no address/pincode/country/state alerts appear
4. Verify estimation saves and prints successfully
5. Open the customer slider — verify customer data still loads and displays correctly
6. Edge case: Test with a customer that has NULL/empty address fields — should still show the appropriate alert

## Notes
- The `est_edit` API returns `address1`, `pincode`, `id_country`, `id_state` from the `address` table via the `get_entry_records()` model method (LEFT JOIN on `address` table)
- The same pattern may exist in billing edit if similar slider validation is used — check `ret_billing.js` for similar `ask_cus_addr1` patterns
- The `get_customer()` function in `ret_general.js` (line ~3604) is the canonical place that populates slider fields — this fix duplicates that population from a different data source (estimation record vs customer record), which is safe since both come from the same underlying `address` table
