# Recipe Customer Slider Integration

> Copy this file and fill in all sections when creating a new recipe.

## Metadata
- **Pattern ID**: PAT-UI-SLIDER-001
- **Severity**: HIGH
- **Modules Affected**: Stock Issue, Bill Split, Estimation, Order, Billing
- **Auto-fixable**: No

## Client Scope
- **Applies to**: ALL
- **Reason**: Applies to all client ERPs migrating to the unified `common_customerslider` offcanvas component.

## Created By
- **Developer**: Antigravity
- **Client**: Logimax
- **Date**: 2026-04-30
- **Source Bug ID**: N/A

## Symptom
1. **Stock Issue**: Clicking "Add" in the customer slider triggers a false "Enter the Mobile Number.." validation toast. If bypassed, saving throws a database error (`Incorrect integer value: 'undefined'`).
2. **Bill Split**: Clicking the `+` Add Customer button on a split row opens the legacy Bootstrap modal (`#confirm-add`) instead of the new unified offcanvas slider.

## Root Cause
1. **DOM ID Collision**: The `stock_issue` form contains a hidden input `<input id="cus_mobile" type="hidden">` which renders before the slider. jQuery's `$('#cus_mobile').val()` grabs this empty hidden field instead of the slider's visible input.
2. **JS Function Override**: `ret_stock_issue.js` defines an old parameterized `function add_customer(cus_name, cus_mobile, ...)`. Since it loads after `ret_general.js`, it overwrites the global parameterless `add_customer()` success handler, causing `undefined` values to be passed to the backend.
3. **Legacy Modal Targets**: In `ret_billing.js`, the `create_new_customer()` function explicitly targets `$('#confirm-add').modal('show')` instead of triggering the shared slider logic.

## Detection
```command
# Search for duplicate mobile hidden fields causing collisions
grep -rn 'id="cus_mobile".*type="hidden"' admin/application/views/

# Search for old overriding add_customer functions
grep -rn 'function add_customer(' admin/assets/js/ | grep -v 'ret_general.js'

# Search for legacy modal triggers
grep -rn 'confirm-add' admin/application/views/
```

## Files
- `admin/application/views/ret_stock_issue/form.php`
- `admin/assets/js/ret_stock_issue.js`
- `admin/assets/js/ret_billing.js`
- `admin/assets/js/ret_general.js`
- `admin/application/views/billing/billsplit.php`
- `admin/application/controllers/admin_ret_billing.php`

## Fix

### Change 1: Resolve DOM ID Collision (Stock Issue View)
**Before** `admin/application/views/ret_stock_issue/form.php`:
```php
<input class="form-control" id="cus_mobile" name="order[cus_mobile]" type="hidden" value="" />
```
**After**:
```php
<input class="form-control" id="selected_cus_mobile" name="order[cus_mobile]" type="hidden" value="" />
```
*(Also update `$('#cus_mobile').val()` references in `ret_stock_issue.js` to `$('#selected_cus_mobile').val()` where targeting the form data).*

### Change 2: Remove JS Function & Click Overrides
**Before** `admin/assets/js/ret_stock_issue.js`:
```javascript
function add_customer(cus_name, cus_mobile,id_village,cus_type,gst_no,img) {
    // Legacy ajax submission
}

$('#add_newcutomer').click(function(event) {
    // Local validation and submission
});

function create_customer(mobile_no) {
    $('#confirm-add').modal('show');
}
```
**After**:
```javascript
// `add_customer` and `#add_newcutomer` handlers completely deleted. 
// The system now falls back to the parameterless `add_customer()` and 
// global click handler defined in `ret_general.js`.

function create_customer(mobile_no) {
    open_customer_slider({ mobile_no: mobile_no });
}
```

### Change 3: Fix Mobile Number Scientific Notation Bug
**Before** `admin/assets/js/ret_stock_issue.js`:
```javascript
$('#cus_mobile').on('blur',function(){
	if(this.value.length==10) { ... }
```
**After**:
```javascript
// type="number" inputs can render scientific notation (e.g., 4.562115436e9)
// Regex strip non-digits to validate the true length
$('#cus_mobile').on('blur', function () {
	var digits = String(this.value).replace(/\D/g, '');
	if (digits.length == 10) {
		$('#cus_mobile').val(digits);
	}
...
```

### Change 4: Standardize Bill Split to use Offcanvas
**Before** `admin/assets/js/ret_billing.js`:
```javascript
function create_new_customer(curRow) {
  if (curRow != undefined) {
    $("#row_active_id").val(curRow.closest("tr").attr("id"));
  }
  $("#confirm-add").modal("toggle");
  // Manual field resets...
}
```
**After**:
```javascript
function create_new_customer(curRow) {
  if (curRow != undefined) {
    $("#row_active_id").val(curRow.closest("tr").attr("id"));
  }
  if (typeof open_customer_slider === 'function') {
      open_customer_slider();
  } else {
      $('.offcanvas').offcanvas('show');
  }
}
```

### Change 5: Include Slider in Bill Split
**Before** `admin/application/controllers/admin_ret_billing.php` (`bill_split` list action):
```php
$this->load->view('layout/template', $data);
```
**After**:
```php
$this->load->view('layout/template', $data);
$this->load->view('layout/common_customerslider');
```
*(Additionally, delete the `<div class="modal fade" id="confirm-add">` markup from `billing/billsplit.php`).*

## Verification
1. Add a customer in `Stock Issue`. It should successfully capture the mobile number and not throw an `Incorrect integer value: 'undefined'` error.
2. In `Bill Split`, click the `+` button in the `billing_split_sale_details` table. It should open the right-side offcanvas slider, not a center popup.
3. Save the customer in `Bill Split` and verify the customer is populated directly into the table row without refreshing the page.

## Notes
When migrating older pages to the new `common_customerslider`, always ensure no local `add_customer()` functions exist in the module's JS file. `ret_general.js` is the single source of truth for handling slider submissions and routing the UI success updates.
