# Recipe: Section Transfer — Barcode Scan / Paste Auto-Tag-Add

## Metadata
- **Pattern ID**: ST-SCAN-SG-001
- **Severity**: MEDIUM — UX feature addition, non-blocking
- **Modules Affected**: Section Transfer (`admin_ret_section_transfer`, `ret_section_transfer`)
- **Auto-fixable**: YES (additive JS changes only)

## Client Scope
- **Applies to**: erp.silvergallerie.com
- **Reason**: Client-specific request; `etail_development_src` was intentionally left unchanged (source already had a debounce-based input handler). The scan-first approach (no product pre-selection required) is a Silver Gallerie operational requirement.

## Created By
- **Developer**: Antigravity AI
- **Client**: erp.silvergallerie.com
- **Date**: 2026-05-13
- **Source Bug ID**: N/A — Feature request (commit `dddd1ec`)
- **Reference Conversation**: `88c77506-a378-47ea-b35c-58a7c35454c1`

---

## Symptom
On the Section Transfer list page (`admin/index.php/admin_ret_section_transfer/ret_section_transfer/list`), scanning a barcode into the `#tag_code` field or pasting a tag code did **not** auto-add the tag to the transfer list. The user had to:
1. Select a product from the product dropdown
2. Click the **Search** button manually

This was unusable for barcode scanner workflows where users scan codes in rapid succession.

## Root Cause
The existing `input` event handler on `#tag_code` only debounced and triggered the `#section_tag_search` click handler, which **required product selection** (`#prod_select` check) before searching. No `keypress Enter` or `paste` event existed for direct scan-to-add.

Additionally, there was no dedicated scan function — the estimation module's `getTaggingScanBySearch` was initially considered as the endpoint, but the correct approach was to reuse the **same controller and endpoint** as the Search button: `admin_ret_section_transfer/ret_section_transfer/getSectionTags`, passing only the scanned `tag_code` (skipping product/section filters).

---

## Detection
```powershell
# Check if auto-scan handler already exists in silvergallerie ret_section_transfer.js
Select-String -Path "d:\xampp7\htdocs\erp.silvergallerie.com\admin\assets\js\ret_section_transfer.js" -Pattern "getSectionTagsByScan|isScanTriggered"
# If no output -> fix is missing
```

---

## Files Changed
- `erp.silvergallerie.com/admin/assets/js/ret_section_transfer.js`

**No controller or PHP changes were required.**

---

## Fix

### Before — Only typing-debounce handler existed (original, lines ~262-278):
```javascript
let typingTimer;
let typingDelay = 500;

$('#tag_code, #tag_code_old').on('input',()=> {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => {
        const tag_code_length = $('#tag_code').val().trim().length;
        const tag_code_old_length = $('#tag_code_old').val().trim().length;
        const shouldTriggerSearch = (tag_code_length >= 4 || tag_code_old_length >= 4);
        if(!shouldTriggerSearch) return;
        $('#section_tag_search').trigger('click');
    }, typingDelay);
});
// No paste handler. No keypress Enter handler. No getSectionTagsByScan function.
```

### After — Replace the input block and add three new handlers + new function:

**Step 1 — Replace the input handler block** (retain debounce, add scan flag and two new handlers):
```javascript
let typingTimer;
let typingDelay = 500;
let isScanTriggered = false;

// --- Barcode scanner: keypress Enter on tag_code auto-triggers scan immediately ---
$('#tag_code').on('keypress', function (e) {

    if (e.which == 13) {

        var tagVal = $(this).val().trim();

        if (tagVal != '' && tagVal.length >= 4) {

            isScanTriggered = true;

            setTimeout(function () {

                isScanTriggered = false;

                getSectionTagsByScan(function () {

                    $('#tag_code').val('');

                    $('#tag_code').focus();

                });

            }, 100);

        }

    }

});

// --- Paste on tag_code auto-triggers scan immediately (like estimation) ---
$('#tag_code').on('paste', function () {

    setTimeout(function () {

        var tagVal = $('#tag_code').val().trim();

        if (tagVal != '' && tagVal.length >= 4) {

            getSectionTagsByScan(function () {

                $('#tag_code').val('');

                $('#tag_code').focus();

            });

        }

    }, 100);

});

// --- Typing debounce: falls back to the product-based search button for regular filter usage ---
$('#tag_code, #tag_code_old').on('input', function () {

    clearTimeout(typingTimer);

    typingTimer = setTimeout(function () {

        if (isScanTriggered) return;

        const tag_code_length = $('#tag_code').val().trim().length;
        const tag_code_old_length = $('#tag_code_old').val().trim().length;
        const shouldTriggerSearch = (tag_code_length >= 4 || tag_code_old_length >= 4);

        if (!shouldTriggerSearch) return;

        $('#section_tag_search').trigger('click');

    }, typingDelay);

});
```

**Step 2 — Append `getSectionTagsByScan()` function at end of file** (after `$('#counterchange_close_modal')` handler):
```javascript
// --- Scan by tag code directly using admin_ret_section_transfer/getSectionTags ---
// Called on Enter/paste scan; no product selection required for direct scan
function getSectionTagsByScan(callback) {

    var tagData = $.trim($('#tag_code').val());

    if (tagData == '') {
        if (typeof callback === 'function') callback();
        return;
    }

    var id_branch = ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined
        ? $('#branch_filter').val()
        : $('#branch_select').val());

    $(".overlay").css("display", "block");

    my_Date = new Date();

    $.ajax({

        type: 'POST',

        url: base_url + 'index.php/admin_ret_section_transfer/ret_section_transfer/getSectionTags?nocache=' + my_Date.getUTCSeconds(),

        dataType: 'json',

        data: {
            'id_branch': id_branch,
            'id_section': '',
            'tag_code': tagData,
            'old_tag_id': '',
            'est_no': '',
            'id_product': ''
        },

        success: function (data) {

            $(".overlay").css("display", "none");

            if (data != null && data.length > 0) {

                var allow_submit = true;
                var val = data[0];  // take first matched tag

                // Duplicate check
                $('#section_trans_list > tbody > tr').each(function (idx, row) {
                    if (val.tag_id == $(this).find('.tag_id').val()) {
                        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Already Exists..' });
                        allow_submit = false;
                    }
                });

                // Order reservation check
                if (val.orderid != '' && val.orderno != '') {
                    $.toaster({
                        priority: 'danger', title: 'Warning!',
                        message: '' + "</br>" + 'Tag is reserved for the order "' + val.orderno + '".',
                        settings: { timeout: 5000 }
                    });
                    allow_submit = false;
                }

                if (allow_submit) {

                    var html = '<tr>' +
                        '<td><input type="checkbox" name="tag_id[]" class="tag_id" value=' + val.tag_id + ' checked></td>' +
                        '<td><input type="hidden" name="id_branch[]" class="id_branch" value=' + val.id_branch + '>' + val.branch_name + '</td>' +
                        '<td><input type="hidden" name="tag_code[]" class="tag_code" value=' + val.tag_code + '>' + val.tag_code + '</td>' +
                        '<td><input type="hidden" name="old_tag_id[]" class="old_tag_id" value=' + val.old_tag_id + '>' + val.old_tag_id + '</td>' +
                        '<td><input type="hidden" name="frm_id_section[]" class="frm_id_section" value=' + val.id_section + '>' + val.section_name + '</td>' +
                        '<td><input type="hidden" name="pro_id[]" class="pro_id" value=' + val.product_id + '>' + val.product_name + '</td>' +
                        '<td><input type="hidden" name="piece[]" class="piece" value=' + val.piece + '>' + val.piece + '</td>' +
                        '<td><input type="hidden" name="gross_wt[]" class="gross_wt" value=' + val.gross_wt + '>' + val.gross_wt + '</td>' +
                        '<td><input type="hidden" name="net_wt[]" class="net_wt" value=' + val.net_wt + '>' + val.net_wt + '</td>' +
                        '</tr>';

                    if ($('#section_trans_list > tbody > tr').length > 0) {
                        $('#section_trans_list > tbody > tr:first').before(html);
                    } else {
                        $('#section_trans_list tbody').append(html);
                    }

                    calculateSectiontotal();

                    $.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + 'Tag added: ' + val.tag_code });

                }

            } else {

                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Tag Found for: ' + tagData });

            }

            if (typeof callback === 'function') callback();

        },

        error: function () {

            $(".overlay").css("display", "none");

            if (typeof callback === 'function') callback();

        }

    });

}
```

---

## Key Design Decisions

| Point | Decision |
|---|---|
| **Endpoint** | Use `admin_ret_section_transfer/getSectionTags` — NOT `admin_ret_estimation/getTaggingScanBySearch`. Same controller, no cross-module dep. |
| **Product filter** | Skipped for scan mode (`id_product: ''`). Product is not required when scanning directly. |
| **`isScanTriggered` flag** | Prevents the debounced `input` handler from double-firing when Enter is pressed (Enter fires both `keypress` and `input`). |
| **First-match only** | `data[0]` — scan returns one tag by code; all items shown if search button used. |
| **Checkbox pre-checked** | Scan-added rows have checkbox pre-checked (`checked` attr); Search button rows are unchecked (user must select). |
| **Callback** | `getSectionTagsByScan(callback)` clears and refocuses `#tag_code` after scan for rapid multi-scan. |
| **Source unchanged** | `etail_development_src/ret_section_transfer.js` — reverted completely. Changes apply only to `erp.silvergallerie.com`. |

---

## Behavior Summary After Fix

| Trigger | Behavior |
|---|---|
| **Barcode scanner (Enter key)** on `#tag_code` | Immediately calls `getSectionTagsByScan()` → adds tag to list, clears field, refocuses |
| **Paste** into `#tag_code` | Immediately calls `getSectionTagsByScan()` → same as above |
| **Type** (≥4 chars, debounced 500ms) | Triggers `#section_tag_search` click → calls `getSectionTags()` with all filters |
| **Click Search button** | Unchanged — calls `getSectionTags()` with branch/section/product filters |

---

## Verification
1. Navigate to `http://localhost/erp.silvergallerie.com/admin/index.php/admin_ret_section_transfer/ret_section_transfer/list`
2. Select a branch from the branch dropdown
3. Place cursor in the **Tag Code** field
4. **Scan test**: Type a valid tag code and press Enter — the tag row should appear in the table immediately (checkbox pre-checked), field clears and refocuses
5. **Paste test**: Paste a valid tag code into the Tag Code field — same auto-add behavior
6. **Duplicate test**: Scan the same tag again — toast warning `Tag Already Exists..` appears, no duplicate row added
7. **Search button test**: Fill Tag Code + product filter + click Search — still works (no regression)
8. **Order-reserved test**: Scan a tag reserved for an order — toast warning appears with order number
9. **No tag found test**: Scan a non-existent code — toast `No Tag Found for: <code>`

---

## Notes
- The `product_id` check in the original `#section_tag_search` click handler was **already commented out** in the Silver Gallerie client file (not in source). This is why scan works without product selection.
- Inspiration pattern: `ret_branch_transfer.js` `#scan_tag_no` keypress handler + estimation debounce in `ret_estimation.js`.
- The `isScanTriggered` flag is reset after 100ms (inside the `setTimeout`) to allow normal input events after scan completion.
- `my_Date.getUTCSeconds()` is used as a `nocache` param (existing ERP convention) to prevent browser caching of AJAX responses.
