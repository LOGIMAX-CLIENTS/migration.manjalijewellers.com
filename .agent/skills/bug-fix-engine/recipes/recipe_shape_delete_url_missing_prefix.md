# Recipe: Shape Delete URL Missing Controller Prefix — 404

## Metadata
- **Pattern ID**: PAT-URL-001
- **Severity**: CRITICAL
- **Modules Affected**: Catalog Master (Diamond Shape)
- **Auto-fixable**: Yes (string replacement in JS)

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal — the JS builds the delete URL incorrectly in all deployments

## Created By
- **Developer**: Antigravity (Black Horest)
- **Client**: retailsource (source repo)
- **Date**: 2026-06-03
- **Source Bug ID**: N/A

## Symptom
Clicking the **Delete** button for any Shape record produces a **404 Page Not Found** error. The shape record is NOT deleted. The generated URL is `/shape/delete/{id}` instead of `/admin_ret_catalog/shape/Delete/{id}`.

## Root Cause
In the `set_shape_table()` JavaScript function inside `catalog_master.js`, the delete URL is constructed as:
```javascript
delete_url = base_url + 'index.php/shape/delete/' + id;
```
This is missing the `admin_ret_catalog/` controller prefix. CodeIgniter routes require the full controller path: `admin_ret_catalog/shape/Delete/{id}`.

## Detection
```command
grep -n "shape/delete" admin/assets/js/catalog_master.js
```
If the match does NOT contain `admin_ret_catalog/`, the bug is present.

## Files
- `admin/assets/js/catalog_master.js` (`set_shape_table()` function)

## Fix

### Before
```javascript
delete_url=(access.delete=='1' ? base_url+'index.php/shape/delete/'+id : '#' );
```

### After
```javascript
delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_catalog/shape/Delete/'+id : '#' );
```

## Verification
1. Navigate to Diamond Shape list page
2. Click "Delete" on any shape record
3. Confirm deletion in the confirmation dialog
4. **Expected**: Shape is deleted successfully, success message shown, list refreshes
5. **Before fix**: 404 error page

## Notes
- This is a classic "missing controller prefix" pattern in CI3 monolith apps where the JS URL doesn't match the PHP routing.
- The same pattern should be audited across all master modules: Category, Sub-Category, Design, etc.
- The method name casing matters: `Delete` (capital D) matches the PHP method name.
