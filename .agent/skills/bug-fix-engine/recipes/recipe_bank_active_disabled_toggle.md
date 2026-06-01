# Recipe: Bank Master Active/Disabled Toggle & System-wide Filtering

## Metadata
- **Pattern ID**: PAT-UI-BANK-STATUS-TOGGLE
- **Severity**: MEDIUM
- **Modules Affected**: Bank Master, Settings, Billing, Schemes, Catalogs, Reports
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL Client ERP Repositories managing Bank Accounts.
- **Reason**: Standardizes the ability to soft-disable inactive/closed banks dynamically inside lists and checkout selectors.

## Created By
- **Developer**: Antigravity AI
- **Client**: LOGIMAX-CLIENTS/mydeengovindarajanjewellers.com
- **Date**: 2026-05-28
- **Source Bug ID**: Conversation 1ee42b33-c0d5-43bc-9480-a08928ad0432

## Symptom
Administrators cannot disable closed or inactive bank accounts, causing legacy accounts to clutter transactional checkout forms, scheme collections, purchase order screens, and report selection panels.

## Root Cause
The legacy system did not have a status column inside the Bank listing view or forms. Additionally, transactional database models fetched bank accounts using broad queries without filtering by active status.

## Detection
Identify where banks are fetched for listing or checkout:
```bash
grep -n "FROM bank" admin/application/models/*.php
```

---

## Files Affected
- `admin/application/config/routes.php`
- `admin/application/controllers/admin_settings.php`
- `admin/application/views/master/bank/list.php`
- `admin/application/views/master/bank/form.php`
- `admin/assets/js/admin_settings.js`
- `admin/application/models/ret_billing_model.php`
- `admin/application/models/payment_model.php`
- `admin/application/models/ret_catalog_model.php`
- `admin/application/models/ret_reports_model.php`

---

## Fix

### 1. Database Schema
Ensure the `bank` table has an `active_bank` column (usually `tinyint(1)` defaulting to `1`).

### 2. Route Registration (`routes.php`)
Register the AJAX toggle endpoint:
```php
$route['settings/bank/toggle_status/(:any)'] = 'admin_settings/bank/ToggleStatus/$1';
```

### 3. Controller AJAX Endpoint (`admin_settings.php`)
Add a secure case in the main `bank()` switch:
```php
case 'ToggleStatus':
    $bank = $this->$model->bankDB('get', $id);
    if ($bank) {
        $new_status = ($bank['active_bank'] == 1) ? 0 : 1;
        $status = $this->$model->bankDB('update', $id, array('active_bank' => $new_status));
        echo json_encode(array('status' => $status, 'new_status' => $new_status));
    } else {
        echo json_encode(array('status' => false));
    }
break;
```

### 4. Custom Labeled CSS Switch (`list.php`)
Inject sleek iOS-style labeled togglers:
```html
<style>
.ios-switch-labeled {
    position: relative;
    display: inline-block;
    width: 85px;
    height: 26px;
    cursor: pointer;
    user-select: none;
}
.ios-slider-labeled {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #e74c3c;
    border-radius: 26px;
    transition: all 0.3s ease;
}
.ios-slider-labeled:before {
    position: absolute;
    content: "";
    height: 20px; width: 20px;
    left: 3px; bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: all 0.3s ease;
}
.ios-slider-labeled:after {
    position: absolute;
    content: "DISABLE";
    right: 8px; top: 50%;
    transform: translateY(-50%);
    color: white;
    font-size: 8px; font-weight: 700;
}
.ios-switch-labeled input:checked + .ios-slider-labeled {
    background-color: #2ec771;
}
.ios-switch-labeled input:checked + .ios-slider-labeled:before {
    transform: translateX(59px);
}
.ios-switch-labeled input:checked + .ios-slider-labeled:after {
    content: "ACTIVE";
    left: 10px; right: auto;
}
</style>
```

### 5. Datatable Switch Rendering & JS Event Listener (`admin_settings.js`)
Render the labeled switch inside the DataTable columns:
```javascript
{
    "mDataProp": function (row, type, val, meta) {
        var isChecked = row.active_bank == '1' ? 'checked' : '';
        return '<label class="ios-switch-labeled">' +
            '<input type="checkbox" class="toggle-bank-status" data-id="' + row.id_bank + '" ' + isChecked + '>' +
            '<span class="ios-slider-labeled"></span>' +
        '</label>';
    }
}
```

Handle AJAX updates seamlessly with safety guards:
```javascript
$(document).on('change', 'input.toggle-bank-status', function(e) {
    var $input = $(this);
    var bankId = $input.data('id');
    var isChecked = $input.is(':checked');
    $input.prop('disabled', true);
    
    $.ajax({
        url: base_url + "index.php/settings/bank/toggle_status/" + bankId,
        dataType: "JSON",
        type: "POST",
        success: function(response) {
            $input.prop('disabled', false);
            if (!response || !response.status) {
                $input.prop('checked', !isChecked);
            }
        },
        error: function() {
            $input.prop('disabled', false);
            $input.prop('checked', !isChecked);
        }
    });
});
```

### 6. Dynamic Transactional Filters (All DB Models)
Apply filters inside all models managing transaction bank dropdowns:
- **Billing:** `WHERE active_bank = 1` inside `ret_billing_model::get_bank_acc_details()`
- **Scheme Payments:** `WHERE active_bank = 1` inside `payment_model::get_bank_acc_details()`
- **Catalog Purchases:** `WHERE active_bank = 1` inside `ret_catalog_model::get_banks()`
- **Reports:** `WHERE active_bank = 1` inside `ret_reports_model::get_ActiveBankname()`

---

## Verification
1. Open the Bank Master listing.
2. Toggle a bank's switch from `ACTIVE` to `DISABLE`.
3. Check the checkout forms inside billing, scheme payments, and catalog purchases.
4. Verify the disabled bank is completely excluded from all transactional selection panels.
5. Re-enable the bank status, and verify it returns immediately inside all forms.
