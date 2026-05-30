# Recipe Template

> Copy this file and fill in all sections when creating a new recipe.

## Metadata
- **Pattern ID**: PAT-JS-001
- **Severity**: HIGH
- **Modules Affected**: Scheme Account (Frontend)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Common JS/HTML DOM issue when using AJAX for forms that expect a native 302 redirect and contain file uploads.

## Created By
- **Developer**: Antigravity
- **Client**: ALL
- **Date**: 2026-04-12
- **Source Bug ID**: N/A

## Symptom
User clicks "Save" on a form with file inputs (e.g., scheme_account.js). The saving action either appears to do nothing or silently fails in the background. The user is not redirected to the next page as expected (e.g., payment/add), and any uploaded files are lost despite a 302 status returned by the server.

## Root Cause
The JavaScript is intercepting the form submission using `$.ajax()` with `serializeArray()`. However, the backend PHP controller explicitly uses `redirect()` instead of returning a JSON response. The browser's XMLHttpRequest follows the 302 silently, reads the HTML of the new page, and throws an error when trying to parse the HTML as JSON (`dataType: "JSON"`). Furthermore, `serializeArray()` cannot serialize `multipart/form-data` file inputs natively, discarding all file attachments before they even hit the server. Finally, because an element has `id="submit"`, standard `$('#form')[0].submit()` throws a TypeError as the DOM overrides the form's native `submit` function property with the button element.

## Detection
```command
grep -rn "$.ajax(" admin/assets/js/ | grep "serializeArray"
```

## Files
- `admin/assets/js/scheme_account.js`

## Fix

### Before
```javascript
        var url = base_url + 'index.php/account/' + ($('#id_scheme_account').val() > 0 ? 'update/' + $('#id_scheme_account').val() : 'save');
        var form_data = $('#acc_join').serializeArray();
        var execute_final_save = function() {
            $.ajax({
                url: url,
                data: form_data,
                dataType: "JSON",
                type: "POST",
                async: false,
                success: function(data) {
                    if (data.status) {
                        window.location.href = base_url + "index.php/" + (data.type == 1 ? "payment/add" : "account/new");
                    } else {
                        window.location.href = base_url + "index.php/account/new";
                    }
                },
                error: function(error) {
                    $("div.overlay").css("display", "none");
                    $('#submit').prop('disabled', false);
                }
            });
        };
```

### After
```javascript
        var url = base_url + 'index.php/account/' + ($('#id_scheme_account').val() > 0 ? 'update/' + $('#id_scheme_account').val() : 'save');
        var form_data = $('#acc_join').serializeArray();
        var execute_final_save = function() {
            $("div.overlay").show();
            HTMLFormElement.prototype.submit.call($('#acc_join')[0]);
        };
```

## Verification
1. Open the form with file uploads and submit valid data.
2. Confirm the browser physically redirects to the correct destination URL (no silent background HTML loading).
3. Confirm that file inputs (e.g. photos/documents) are correctly transferred and saved in the backend.

## Notes
Using `HTMLFormElement.prototype.submit.call(formElement)` avoids the standard JavaScript `submit is not a function` error which occurs whenever a form contains an input or a button with `name="submit"` or `id="submit"`.
