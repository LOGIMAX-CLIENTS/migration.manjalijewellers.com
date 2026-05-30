# Recipe: Branch-Aware Location Defaults for Add/Edit Customer Modals

## Metadata
- **Pattern ID**: PAT-LOC-001
- **Severity**: HIGH
- **Modules Affected**: CRM, Estimation, Billing, Order, Repair Order, Receipt
- **Auto-fixable**: No (requires per-module controller, view, and JS changes)

## Client Scope
- **Applies to**: ALL retail ERP clients (admin_ret_* modules)
- **Reason**: All clients share the same controller/view/JS structure for Add/Edit Customer modals

## Created By
- **Developer**: Antigravity AI
- **Client**: Karpagam Jewels
- **Date**: 2026-04-22
- **Source Bug ID**: N/A

## Symptom

1. Add Customer modal (any module) shows wrong country/state/city default — either
   hardcoded values (India/Tamil Nadu/Sathyamangalam), blank, or the city of a
   **previously searched customer** on the same page.
2. Branch-wise login does not show the branch's city/state in the modal.
3. ALL-branch login does not show company master city/state.
4. The city field in the Branch master is never applied even when configured.

## Root Cause

Three independent defects combining to produce the symptom:

### Defect 1 — Model: `id_city` missing from branch query
`admin_settings_model::getCompanyDetails($id_branch)` has two SQL paths:
- `$id_branch == ''` (company master) → selects `c.id_city` ✅
- `$id_branch != ''` (branch master)  → joins `city ct` but **never selects `b.id_city`** ❌

The result array therefore has no `id_city` key for branch logins.

### Defect 2 — Controllers: always fetch company master, ignore session branch
All module controllers called:
```php
$data['comp_details'] = $this->admin_settings_model->getCompanyDetails('');
```
The empty string forces company-master lookup unconditionally, ignoring the
logged-in branch stored in `$this->session->userdata('id_branch')`.

### Defect 3 — JavaScript: stale `#id_city` bleeds into Add Customer modal
`ret_general.js` `get_city()` (and `get_state()`, `get_country()`) used:
```js
var id_city = $('#id_city').val() !== '' ? $('#id_city').val() : $('#cmp_city').val();
```
`#id_city` is populated whenever a customer is selected from the order/billing
search bar. When the user then opens "Add New Customer", this stale value
(e.g., Palani from the previously selected customer) takes priority over the
company/branch default in `#cmp_city`.

Additionally, all `get_country()` call sites passed no `context` argument, so
the same function was populating both `#country` (Add modal) and `#ed_cus_country`
(Edit modal) simultaneously, causing cross-contamination.

## Detection

```powershell
# Defect 1: Check if branch query is missing id_city
Select-String -Path "admin\application\models\admin_settings_model.php" -Pattern "getCompanyDetails" -Context 5,15

# Defect 2: Find controllers passing hardcoded '' to getCompanyDetails
Select-String -Path "admin\application\controllers\admin_ret_*.php","admin\application\controllers\admin_customer.php" -Pattern "getCompanyDetails\(''\)"

# Defect 3: Find the stale-field pattern in JS
Select-String -Path "admin\assets\js\ret_general.js" -Pattern "id_city.*val.*id_city.*val.*cmp_city"
```

## Files

### Model
- `admin/application/models/admin_settings_model.php`

### Controllers
- `admin/application/controllers/admin_ret_estimation.php`
- `admin/application/controllers/admin_ret_billing.php`
- `admin/application/controllers/admin_ret_order.php`
- `admin/application/controllers/admin_customer.php`

### Views (hidden fields)
- `admin/application/views/estimation/form.php`
- `admin/application/views/order/form.php`
- `admin/application/views/order/repair_order/form.php`
- `admin/application/views/master/customer/form.php`
- `admin/application/views/billing/issueReceipt/receiptForm.php`

### JavaScript
- `admin/assets/js/ret_general.js`
- `admin/assets/js/ret_billing.js`
- `admin/assets/js/ret_estimation.js`
- `admin/assets/js/ret_order.js`
- `admin/assets/js/customer.js` (CRM module — has its own standalone functions)

## Fix

---

### FIX 1 — Model: Add `id_city` to branch SELECT

**File**: `admin/application/models/admin_settings_model.php`

#### Before
```php
$sql=$this->db->query("select b.name,b.address1,b.address2,c.company_name,
    cy.name as country,ct.name as city,s.name as state,b.pincode,s.id_state,s.state_code,cy.id_country
    from branch b
    join company c
    left join country cy on (b.id_country=cy.id_country)
    left join state s on (b.id_state=s.id_state)
    left join city ct on (b.id_city=ct.id_city)
    where b.id_branch=".$id_branch."");
```

#### After
```php
$sql=$this->db->query("select b.name,b.address1,b.address2,c.company_name,
    cy.name as country,ct.name as city,s.name as state,b.pincode,s.id_state,s.state_code,cy.id_country,b.id_city as id_city
    from branch b
    join company c
    left join country cy on (b.id_country=cy.id_country)
    left join state s on (b.id_state=s.id_state)
    left join city ct on (b.id_city=ct.id_city)
    where b.id_branch=".$id_branch."");
```

---

### FIX 2 — Controllers: Use session branch, not hardcoded empty string

Apply to every module's form-loading case (add AND edit). Pattern is identical
across all controllers:

#### Before
```php
$data['comp_details'] = $this->admin_settings_model->getCompanyDetails('');
```

#### After
```php
$id_branch_default = $this->session->userdata('id_branch');
$data['comp_details'] = $this->admin_settings_model->getCompanyDetails($id_branch_default);
```

- `id_branch` empty (ALL-branch login) → company master City/State/Country
- `id_branch` set (branch login) → that branch's City/State/Country

---

### FIX 3 — Views: Wire hidden fields from PHP comp_details

Add these three hidden inputs to each module's form view where the
Add/Edit Customer modal is present. Place near the Add Customer button:

#### Before (pattern — hidden fields absent or hardcoded empty)
```html
<input type="hidden" id="cmp_country" name="" value="">
<input type="hidden" id="cmp_state"   name="" value="">
<input type="hidden" id="cmp_city"    name="" value="">
```

#### After
```php
<?php if(isset($comp_details)){ ?>
<input type="hidden" id="cmp_country" value="<?php echo $comp_details['id_country']; ?>">
<input type="hidden" id="cmp_state"   value="<?php echo $comp_details['id_state']; ?>">
<input type="hidden" id="cmp_city"    value="<?php echo $comp_details['id_city']; ?>">
<?php } ?>
```

---

### FIX 4 — ret_general.js: Context-aware get_country/state/city

Add a `context` argument (`'add'` | `'edit'`) to each function.
In `'add'` context, read exclusively from `#cmp_*` fields (company default).
In `'edit'` context, read from `#ed_id_*` fields (customer stored data).
Never use `#id_*` fields in add context — they hold the last searched customer.

#### Before (get_country)
```js
function get_country() {
    // ...
    var id_country = $('#id_country').val() !== '' ? $('#id_country').val() : $('#cmp_country').val();
    if ($("#country").length && id_country)    { $("#country").selectpicker('val', id_country).trigger('change'); }
    if ($("#ed_cus_country").length && ed_id_country > 0) { $("#ed_cus_country").selectpicker('val', ed_id_country).trigger('change'); }
}
```

#### After (get_country)
```js
function get_country(context) {
    // ...
    var cmp_country  = $('#cmp_country').val();
    var ed_id_country = $('#ed_id_country').val();

    if (!context || context === 'add') {
        if ($("#country").length && cmp_country) {
            $("#country").selectpicker('val', cmp_country).trigger('change');
        }
    }
    if (!context || context === 'edit') {
        if ($("#ed_cus_country").length && ed_id_country > 0) {
            $("#ed_cus_country").selectpicker('val', ed_id_country).trigger('change');
        }
    }
}
```

Apply the same pattern to `get_state(id, context)` and `get_city(id, context)`:
- `'add'` → reads `#cmp_state` / `#cmp_city`
- `'edit'` → reads `#ed_id_state` / `#ed_id_city`

Also split the onChange handlers:
```js
$('#country, #ed_cus_country').change(function () {
    if (this.id === 'country') {
        get_state(this.value, 'add');
    } else {
        get_state(this.value, 'edit');
    }
});
// Same pattern for #state→get_city and #city syncing #id_city / #ed_id_city
```

---

### FIX 5 — All JS call sites: Pass 'add' or 'edit' context

#### Before (all modules — ret_billing.js, ret_order.js, ret_estimation.js)
```js
// In Add Customer button click handlers:
get_country();

// In Edit Customer AJAX callback:
get_country();
```

#### After
```js
// In Add Customer button click handlers:
get_country('add');

// In Edit Customer AJAX callback (after fetching customer data into #ed_id_*):
get_country('edit');
```

**Locations to update:**

| File | Line context | Context arg |
|------|-------------|-------------|
| `ret_estimation.js` | `case 'add':` switch | `'add'` |
| `ret_estimation.js` | `$('#add_new_customer').on('click')` | `'add'` |
| `ret_estimation.js` | `create_customer()` helper | `'add'` |
| `ret_billing.js` | `$('#add_new_customer').on('click')` | `'add'` |
| `ret_billing.js` | `create_new_customer()` helper | `'add'` |
| `ret_billing.js` | `$('#edit_estimation_detalis')` AJAX success | `'edit'` |
| `ret_billing.js` | Receipt edit-customer AJAX success | `'edit'` |
| `ret_order.js` | `$('#add_new_customer').on('click')` (×2) | `'add'` |
| `ret_order.js` | `$('#add_new_customer_repair').on('click')` | `'add'` |
| `ret_order.js` | Edit customer AJAX success (`get_customer()`) | `'edit'` |

---

### FIX 6 — customer.js (CRM): Use #cmp_* as fallback in standalone functions

CRM module has its own `get_country` / `get_state` / `get_city` functions
that do NOT share `ret_general.js`. Apply fallback logic there independently.

#### Before (get_city example)
```js
$("#city").select2("val", ($('#cityval').val()!=null?$('#cityval').val():''));
var selectid = $('#cityval').val();
if(selectid!=null && selectid>0) { $('#city').val(selectid); }
```

#### After
```js
// Use existing customer city > company/branch default city
var defaultCity = ($('#cityval').val() != null && $('#cityval').val() > 0)
    ? $('#cityval').val()
    : (($('#cmp_city').val() != null && $('#cmp_city').val() != '') ? $('#cmp_city').val() : '');

$('#city').select2('val', defaultCity);
var selectid = defaultCity;
if(selectid != null && selectid > 0) { $('#city').val(selectid); }
```

Apply same pattern to `get_country` (uses `#countryval` / `#cmp_country`) and
`get_state` (uses `#stateval` / `#cmp_state`).

## Verification

1. **ALL-branch login** → Open Add Customer modal in any module (Billing, Order,
   Estimation, CRM). Country, State, City should match company master settings.
2. **Branch login** (e.g., branch_id=16) → Add Customer modal should show that
   branch's configured Country/State/City.
3. **Edit Customer** → Click edit on an existing customer. Their stored
   Country/State/City should load, NOT the company defaults.
4. **Stale-field test** → Select an existing customer with city=Palani on the
   Order/Billing page. Then click "Add New Customer". The modal should show
   company/branch default city (NOT Palani).
5. **Browser inspector** → Verify `#cmp_city`, `#cmp_state`, `#cmp_country`
   hidden fields have the correct IDs from the PHP page source.

## Notes

- **Hard-refresh required after JS changes** (`Ctrl+Shift+R`) — browsers cache
  JS files aggressively; the old `get_country()` with no context arg is
  indistinguishable from a new one by URL unless versioned.
- The `#id_country`, `#id_state`, `#id_city` fields are the **customer form
  submission fields** (what gets saved). Do not confuse with `#cmp_*` (defaults)
  or `#ed_id_*` (edit modal state). All three sets have distinct roles.
- The `customer.js` CRM module is completely standalone — changes to
  `ret_general.js` do NOT affect it.
- The model fix (Defect 1) is safe — `b.id_city as id_city` only adds a column
  to the branch query result; it does not break anything that used the result
  before (callers were just getting `null` before).
