# Catalog Delete Handler — Toast Title Fix + AJAX Refresh

## Metadata
- **Pattern ID**: PAT-CTG-003
- **Severity**: LOW
- **Modules Affected**: Retail Catalog (Design Mapping)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal UX issue in all catalog delete operations

## Created By
- **Developer**: Antigravity (Black Horest) / Karthi
- **Client**: Source (retailsource)
- **Date**: 2026-06-08
- **Source Bug ID**: Design Mapping Bug #9, #14

## Symptom
1. After deleting a design mapping, the success toast shows "Warning!" as the title instead of "Success!"
2. The page does `location.reload()` immediately after showing the toast, causing the toast to disappear before the user can read it
3. No error handling for failed delete operations

## Root Cause
1. The `delete_product_mapping()` success callback uses `priority: 'success'` but `title: 'Warning!'` — contradictory
2. `location.reload(false)` is called immediately after the toast, so the page reloads before the toast animation completes
3. No `else` branch for non-success status, silently failing

## Detection
```command
grep -n "location.reload" admin/assets/js/catalog_master.js | grep -i "delete\|mapping"
grep -n "title : 'Warning!'" admin/assets/js/catalog_master.js
```

## Files
- `admin/assets/js/catalog_master.js`

## Fix

### Before
```javascript
success:function(data){

    if(data.status)

    {

        $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.msg});

    }

    location.reload(false);

    $("div.overlay").css("display", "none");
```

### After
```javascript
success:function(data){

    if(data.status)

    {

        $.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.msg});

    }
    else
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg});
    }

    get_product_mapping_details();

    $("div.overlay").css("display", "none");
```

## Verification
1. Navigate to Design Mapping list
2. Select a mapping checkbox and click Delete
3. Verify a green "Success!" toast appears (not yellow "Warning!")
4. Verify the toast stays visible for its full duration
5. Verify the table refreshes automatically without page reload
6. Test deleting a non-existent mapping — verify "Warning!" toast appears
7. Verify the overlay spinner hides properly after delete

## Notes
- This pattern (wrong toast title + location.reload) is common across multiple catalog functions
- Search for similar patterns in other delete handlers: `grep -n "location.reload" catalog_master.js`
- The `get_product_mapping_details()` function fetches fresh data via AJAX and re-renders the DataTable
- If the specific table refresh function doesn't exist for another module, create it before replacing `location.reload()`
- Never use `location.reload()` after a toast — the reload destroys the DOM before the toast animation finishes
