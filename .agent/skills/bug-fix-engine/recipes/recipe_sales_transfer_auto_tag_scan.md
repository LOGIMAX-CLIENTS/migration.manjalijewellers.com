# Recipe: Sales Transfer Auto Tag Scan (No Enter Key Required)

## Metadata
- Pattern ID: ST-AUTO-001
- Severity: P2 — UX improvement, non-blocking
- Modules Affected: Sales Transfer (admin_ret_sales_transfer)
- Auto-fixable: YES

## Client Scope
- Applies to: ALL clients using Sales Transfer module
- Reason: Universal UX requirement for barcode scanner-driven workflows

## Created By
- Developer: Antigravity AI
- Client: navratnajewellery
- Date: 2026-04-22
- Source Bug ID: ST-AUTO-001

## Symptom
In Sales Transfer Add form, scanning a barcode tag into the Tag Code or Old Tag Code field
requires the user to click the "Search" button manually or press Enter. The system does not
auto-fetch product details upon scan completion.

## Root Cause
The `get_sales_transfer_tag_list()` function was only wired to the `.sales_transfer_search`
button click event. No `input`, `keypress`, or `paste` event handler existed on `#tag_no`
or `#old_tag_no` fields to auto-trigger the search when a scanner fires.

## Detection
```powershell
# Check if auto-scan handler exists in ret_sales_transfer.js
Select-String -Path "admin\assets\js\ret_sales_transfer.js" -Pattern "tagScanTimer|triggerTagScan"
# If no output -> fix is missing
```

## Files
- `admin/assets/js/ret_sales_transfer.js`

## Fix

### Before (only Search button triggered the lookup):
After `get_sales_transfer_tag_list()` function closes, only `$(document).on('change','.calc_type'...)` existed.

### After — add immediately after `get_sales_transfer_tag_list` function closes:
```javascript
    var tagScanTimer = null;

    function triggerTagScan(fieldId) {
        if ($('#from_brn').val() == '' || $('#from_brn').val() == null) {
            $.toaster({ priority: 'danger', title: 'Warning!', message: '</br>Please Select From Branch..' });
            return;
        }
        if ($('#to_brn').val() == '' || $('#to_brn').val() == null) {
            $.toaster({ priority: 'danger', title: 'Warning!', message: '</br>Please Select To Branch..' });
            return;
        }
        var tagVal = $('#' + fieldId).val();
        if (tagVal == '') { return; }
        get_sales_transfer_tag_list();
    }

    // 1) DEBOUNCE — auto-fires 400ms after scanner stops typing (no Enter needed)
    $(document).on('input', '#tag_no, #old_tag_no', function() {
        var fieldId = this.id;
        clearTimeout(tagScanTimer);
        tagScanTimer = setTimeout(function() {
            triggerTagScan(fieldId);
        }, 400);
    });

    // 2) ENTER KEY — instant trigger for scanners that send Enter
    $(document).on('keypress', '#tag_no, #old_tag_no', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            clearTimeout(tagScanTimer);
            triggerTagScan(this.id);
        }
    });

    // 3) PASTE — instant trigger for clipboard/paste-mode scanners
    $(document).on('paste', '#tag_no, #old_tag_no', function() {
        var fieldId = this.id;
        clearTimeout(tagScanTimer);
        setTimeout(function() { triggerTagScan(fieldId); }, 100);
    });
```

## Verification
1. Navigate to Sales Transfer Add page
2. Select From Branch and To Branch
3. Place cursor in Tag Code field — type/scan a tag code WITHOUT pressing Enter
4. After ~400ms, product row appears automatically in the table
5. Tag field clears and refocuses
6. Enter key also works (instant)
7. Search button still functions (no regression)

## Notes
- Debounce delay is 400ms — adjust to 300ms if scanner response feels slow
- The paste handler runs with a 100ms delay to let the DOM update first
- Pattern reference: `ret_branch_transfer.js` `#scan_tag_no` keypress handler (original inspiration)
