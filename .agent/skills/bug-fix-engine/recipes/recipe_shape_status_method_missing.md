# Recipe: Shape Status Toggle — Missing Controller Method (404)

## Metadata
- **Pattern ID**: PAT-MST-001
- **Severity**: CRITICAL
- **Modules Affected**: Catalog Master (Diamond Shape)
- **Auto-fixable**: No (requires adding a new PHP function)

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal — the `shape_status()` method was never implemented in the controller

## Created By
- **Developer**: Antigravity (Black Horest)
- **Client**: retailsource (source repo)
- **Date**: 2026-06-03
- **Source Bug ID**: N/A

## Symptom
Clicking the **Active/Inactive** toggle button on a Shape record produces a **404 Page Not Found** error. The status is NOT changed. The list page links to `admin_ret_catalog/shape_status/{status}/{id}` but no corresponding PHP method exists.

## Root Cause
The `set_shape_table()` JS function in `catalog_master.js` generates status toggle links pointing to `admin_ret_catalog/shape_status/{status}/{id}`. However, the `shape_status()` method was **never created** in `admin_ret_catalog.php`. The shape master was likely copy-pasted from another master module but the status toggle method was missed.

## Detection
```command
grep -n "function shape_status" admin/application/controllers/admin_ret_catalog.php
```
If this returns no match, the bug is present.

## Files
- `admin/application/controllers/admin_ret_catalog.php` (new `shape_status()` method to add)

## Fix

### Before
```php
// Method does not exist — 404 on status toggle
function getExistingKarigars()
{
```

### After (add the new method BEFORE `getExistingKarigars`)
```php
function shape_status($status, $id)
{
    $data = array('status' => $status);

    $model = self::CAT_MODEL;

    $updstatus = $this->$model->update_shape($data, $id);

    if ($updstatus) {
        $this->session->set_flashdata('chit_alert', array('message' => 'Shape status updated as ' . ($status == 1 ? 'Active' : 'In Active') . ' successfully.', 'class' => 'success', 'title' => 'Shape Status'));
    } else {
        $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Shape Status'));
    }

    redirect('admin_ret_catalog/shape/list');
}

function getExistingKarigars()
{
```

## Verification
1. Navigate to Diamond Shape list page
2. Find a shape that is currently "Active"
3. Click the status toggle to "Inactive"
4. **Expected**: Shape status changes to "In Active", success flash message shown, list refreshes
5. Click again to re-activate → Status should toggle back to "Active"
6. **Before fix**: 404 error page on any toggle click

## Notes
- This pattern relies on an existing `update_shape($data, $id)` method in the catalog model (`ret_catalog_model.php`). Verify this exists before applying.
- The `self::CAT_MODEL` constant is used to reference the model, consistent with other methods in the controller.
- Flash data uses `chit_alert` key, which is the standard flash message key in this application.
- Redirect goes to `admin_ret_catalog/shape/list` to refresh the list page after status change.
- Same missing-method pattern should be checked for other master toggles (Category, Sub-Category, Design status toggles).
