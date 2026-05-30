# Recipe: Estimation — Employee Filter Does Not Auto-Sync to Tag Rows

## Metadata
- **Pattern ID**: PAT-EST-001
- **Severity**: P2 — UX issue, non-blocking but causes manual repetitive work per tag
- **Modules Affected**: Estimation (admin_ret_estimation)
- **Auto-fixable**: Yes — single JS block addition

## Client Scope
- Applies to: ALL clients using the estimation module with per-tag employee selection
- Reason: The missing sync block was never ported from source to client builds

## Created By
- Developer: Antigravity AI
- Client: shop.gupthagem.com
- Date: 2026-04-27
- Source Bug ID: N/A (ad-hoc from user report)

## Symptom
When the employee is selected in the top "Select Employee" filter on the Estimation Add/Edit page, the employee dropdown within each tag row is NOT automatically updated. The employee has to manually select the employee for every single tag row, one by one.

## Root Cause
The `#emp_select` on-change handler in `admin/assets/js/ret_estimation.js` was missing the block that iterates over all existing `.item_emp_id` select2 dropdowns in tag rows and syncs them to the newly selected employee value.

The source codebase (`etail_development_src`) had this code, but it was not present in client deployments.

## Detection
```powershell
# Check if sync block is missing in client JS
Select-String -Path "admin\assets\js\ret_estimation.js" -Pattern "item_emp_id.*each|each.*item_emp_id" -CaseSensitive:$false
```
If no results, the fix is needed.

Also check for the pattern:
```powershell
Select-String -Path "admin\assets\js\ret_estimation.js" -Pattern "emp_select.*on.*change|on.*change.*emp_select" -CaseSensitive:$false
```
Then inspect that the block contains the `.item_emp_id` sync logic.

## Files
- `admin/assets/js/ret_estimation.js` — the `$('#emp_select').on('change', ...)` handler

## Fix

### Before (missing sync block)
```javascript
$('#emp_select').on('change',function(){

    if(this.value!='')

    {

        $('#id_employee').val(this.value);

         $.each(emp_details, function (key, item){

         var id_employee=$('#id_employee').val();
```

### After (with sync block added)
```javascript
$('#emp_select').on('change',function(){

    if(this.value!='')

    {

        let emp_id = this.value;

        $('#id_employee').val(emp_id);

        if ($('.item_emp_id').length) {

            $('.item_emp_id').each(function () {

                $(this).select2("val", emp_id);

            });

        }

         $.each(emp_details, function (key, item){

         var id_employee=$('#id_employee').val();
```

The key addition is:
```javascript
if ($('.item_emp_id').length) {
    $('.item_emp_id').each(function () {
        $(this).select2("val", emp_id);
    });
}
```

## Verification
1. Navigate to `admin/index.php/admin_ret_estimation/estimation/add`
2. Select a branch
3. Select an employee (e.g., "Admin C") from the "Select Employee" filter
4. Check the Tag checkbox and click "+ Add" to add a tag row
5. Note the row's Employee dropdown shows `-Select Employee-`
6. Now change the top filter to a DIFFERENT employee
7. **EXPECTED**: The Employee dropdown in the tag row automatically updates to match the newly selected employee
8. Confirm the per-row employee value is saved correctly with the tag on form submission

## Notes
- This fix only affects the **UI sync** — the per-tag employee is still independently saved as `est_tag[item_emp_id]` array values, so the employee sales referral report continues to work tag-by-tag as before.
- The fix does NOT affect new rows being added — new empty rows start with `-Select Employee-` in both source and client (by design).
- The fix does NOT clear manually overridden per-tag employee values unless the user changes the header filter again.
- Cross-module safe: only modifies the estimation JS handler, no PHP/DB changes.
