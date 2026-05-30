# Recipe: Settings Field Missing Form Input (Silent Reset to 0)

> Controller saves a field with `isset()` defaulting to `0`, but the view form has no corresponding input element — silently resetting the DB value to `0` on every save.

## Metadata
- **Pattern ID**: PAT-SET-001
- **Severity**: HIGH
- **Modules Affected**: Settings (General Settings), any module using the same controller-save pattern
- **Auto-fixable**: No (requires adding HTML form inputs specific to each missing field)

## Client Scope
- **Applies to**: ALL
- **Reason**: This is a structural pattern in the shared codebase — any client using the same `admin_settings.php` controller and `form.php` view can be affected whenever a new `chit_settings` column is added to the controller's Save/Update arrays but not to the form view.

## Created By
- **Developer**: Antigravity AI
- **Client**: retail.ssmjewellery.in
- **Date**: 2026-04-02
- **Source Bug ID**: N/A

## Symptom
- A feature toggle (e.g., Digi Gold) that was enabled gets **silently disabled** every time an admin saves General Settings from any tab.
- The user reports: "DG Gold has been disabled in the app" even though nobody explicitly turned it off.
- No error messages or warnings — the value just quietly resets to `0`.

## Root Cause
The controller's `general_settings()` method builds a `$gen_info` array for both `Save` and `Update` cases using this pattern:

```php
'enable_digi_gold' => (isset($general['enable_digi_gold'])?$general['enable_digi_gold']:0),
```

This reads the POST data from `$this->input->post('general')`. If the form view has **no `<input>` element** with `name="general[enable_digi_gold]"`, then `isset()` is always `false`, and the value defaults to `0`.

Since **all tabs share the same `<form>` element** and all fields are included in every save operation (Save/Update don't filter by tab), saving ANY tab resets ALL fields that lack form inputs to `0`.

## Detection

### Find fields in controller Save/Update arrays
```command
grep -n "enable_digi_gold\|show_video_shop\|show_customer_order" admin/application/controllers/admin_settings.php
```

### Check if form inputs exist
```command
grep -n "enable_digi_gold\|show_video_shop\|show_customer_order" admin/application/views/settings/general/form.php
```

### Generic detection: find any field in controller not present in form
Compare the list of `general[xxx]` field names in the controller Save/Update arrays against the list of `name="general[xxx]"` attributes in the form view. Any field present in the controller but absent from the view is vulnerable.

## Files
- `admin/application/controllers/admin_settings.php` — `general_settings()` method, Save case (~line 2088) and Update case (~line 2249)
- `admin/application/views/settings/general/form.php` — General Settings form (Other settings tab)
- `admin/application/models/admin_settings_model.php` — `settingsDB()` method reads/writes `chit_settings` table

## Fix

### What to add (View)
Add Enable/Disable radio buttons in the appropriate tab of `form.php` for each missing field. Follow the existing pattern:

```php
<!-- Digi Gold settings -->
<h4 class="page-header">Digi Gold Enable and Disable based on the settings.</h4>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
           <h5 for="chargeseme_name" class="col-md-4">Digi Gold Settings</h5>
           <div class="col-md-6">
               <div class="col-md-5">
                   <input type="radio" name="general[enable_digi_gold]" value="1" <?php if($general['enable_digi_gold'] == 1){ ?> checked="true" <?php } ?> > Enable
               </div>
               <div class="col-md-5">
                   <input type="radio" name="general[enable_digi_gold]" value="0" <?php if($general['enable_digi_gold'] == 0){ ?> checked="true" <?php } ?> > Disable
               </div>
               <p class="help-block"></p>
           </div>
        </div><br><br>
    </div>
</div>
<!-- Digi Gold settings end -->
```

### After fix: also re-enable in DB if already reset
```sql
UPDATE chit_settings SET enable_digi_gold = 1 WHERE id_chit_settings = 1;
```

## Verification
1. Go to **Settings → General Settings → Other settings** tab
2. Confirm the Digi Gold, Video Shop, and Customer Order toggles are visible
3. Set Digi Gold to **Enable**, save
4. Switch to a different tab (e.g., Maintenance), save that tab
5. Go back to Other settings — confirm Digi Gold is still **Enable** (not reset)
6. Check DB: `SELECT enable_digi_gold, show_video_shop, show_customer_order FROM chit_settings;`
7. Verify the mobile API response includes the correct `show_digi` value

## Notes
- **Prevention**: Whenever a new column is added to `chit_settings` and included in the controller's Save/Update `$gen_info` array, a corresponding form input MUST also be added to `form.php`. This should be part of the code review checklist.
- **Related fields fixed together**: `enable_digi_gold`, `show_video_shop`, `show_customer_order` all had the same issue.
- **Mobile API impact**: `mobileapi_model.php` reads `cs.enable_digi_gold as show_digi` — this value controls whether Digi Gold is visible in the customer app.
- **Checkbox vs Radio**: Use **radio buttons** (Enable/Disable) instead of checkboxes for these toggles. Unchecked checkboxes don't send any POST value, which causes the same `isset()` → `0` problem. Radio buttons always send a value.
