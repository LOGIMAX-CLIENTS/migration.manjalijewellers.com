# Recipe: Shape Special Characters + Length Overflow — No Input Validation

## Metadata
- **Pattern ID**: PAT-VAL-011
- **Severity**: HIGH
- **Modules Affected**: Catalog Master (Diamond Shape)
- **Auto-fixable**: No (requires adding validation code at 3 layers: HTML, JS, PHP)

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal — shape master input fields have no validation in any client

## Created By
- **Developer**: Antigravity (Black Horest)
- **Client**: retailsource (source repo)
- **Date**: 2026-06-03
- **Source Bug ID**: N/A

## Symptom
1. **Special characters**: Users can enter special characters like `@#$%^&*()` in shape names, which causes downstream issues in billing, reporting, and tagging where shape names are used in SQL queries and HTML rendering.
2. **Length overflow**: Shape names exceeding the DB column width (`varchar(30)`) are silently truncated by MySQL, leading to data corruption and unexpected shape name lookups failing.

## Root Cause
The shape Add/Update forms have:
- No HTML `pattern` or `maxlength` attributes on the input fields
- No JavaScript validation before AJAX submission
- No PHP server-side validation before DB insert/update

The input goes directly from `$this->input->post("name")` to `$data = array('name' => $shape, ...)` with zero sanitization.

## Detection
```command
grep -n "maxlength" admin/application/views/master/shape/list.php
grep -n "preg_match" admin/application/controllers/admin_ret_catalog.php
grep -n "namePattern" admin/assets/js/catalog_master.js
```
If no `maxlength` attribute on shape inputs, no `preg_match` in the shape controller block, and no `namePattern` in the JS handlers — bug is present.

## Files
- `admin/application/views/master/shape/list.php` (HTML input attributes)
- `admin/assets/js/catalog_master.js` (JS validation in click handlers)
- `admin/application/controllers/admin_ret_catalog.php` (PHP server-side validation)

## Fix

### File 1: admin/application/views/master/shape/list.php

#### Before (Add modal input)
```html
<input type="text" class="form-control" id="shape" name="name" placeholder="Enter Shape" required="true">
```

#### After (Add modal input)
```html
<input type="text" class="form-control" id="shape" name="name" placeholder="Enter Shape" required="true" maxlength="30" pattern="^[a-zA-Z0-9 ]+$" title="Only letters, numbers, and spaces are allowed">
```

#### Before (Edit modal input)
```html
<input type="text" class="form-control" id="ed_shape" name="name"  placeholder="Enter shape">
```

#### After (Edit modal input)
```html
<input type="text" class="form-control" id="ed_shape" name="name"  placeholder="Enter shape" maxlength="30" pattern="^[a-zA-Z0-9 ]+$" title="Only letters, numbers, and spaces are allowed">
```

### File 2: admin/assets/js/catalog_master.js

#### Before (Add click handler)
```javascript
$("#add_newshape").on('click',function(){

    if($('#shape').val() != '')

    {

        add_shape($('#shape').val(),$('#desc').val());

        $('#shape').val('');

    }

    else if($("#shape").val() == ''){

    msg='<div class = "alert alert-danger">...<strong>Warning!</strong> Enter Shape name.</div>';
```

#### After (Add click handler — with trim, pattern, length validation)
```javascript
$("#add_newshape").on('click',function(){

    var shapeVal = $.trim($('#shape').val());
    var namePattern = /^[a-zA-Z0-9 ]+$/;

    if(shapeVal == ''){

    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Shape name.</div>';

    $("div.overlay").css("display", "none");

    $('#error-msg').html(msg);

    return false;

   }
   else if(!namePattern.test(shapeVal)){

    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Special characters are not allowed. Only letters, numbers, and spaces are accepted.</div>';

    $("div.overlay").css("display", "none");

    $('#error-msg').html(msg);

    return false;

   }
   else if(shapeVal.length > 30){

    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Shape name cannot exceed 30 characters.</div>';

    $("div.overlay").css("display", "none");

    $('#error-msg').html(msg);

    return false;

   }
   else
   {

    add_shape(shapeVal,$('#desc').val());

    $('#shape').val('');

   }

});
```

#### Before (Update click handler)
```javascript
$("#update_shape").on('click',function(){

    var shape=($("#ed_shape").val());

    // ... direct call to update_shape without validation
```

#### After (Update click handler — same validation pattern)
```javascript
$("#update_shape").on('click',function(){

    var shape= $.trim($("#ed_shape").val());

    var id=$("#edit-id").val();

    var desc=$("#ed_desc").val();

    var namePattern = /^[a-zA-Z0-9 ]+$/;

    if(shape == ''){
        // empty check...
        return false;
    }
    else if(!namePattern.test(shape)){
        // special char check...
        return false;
    }
    else if(shape.length > 30){
        // length check...
        return false;
    }
    else
    {
        update_shape(shape,id,desc);
    }

});
```

### File 3: admin/application/controllers/admin_ret_catalog.php

#### Before (Add case — no validation)
```php
$shape = $this->input->post("name");
$description = $this->input->post("description");
$data = array('name' => $shape, ...);
```

#### After (Add case — 3 server-side checks)
```php
$shape = trim($this->input->post("name"));
$description = trim($this->input->post("description"));

// Validate shape name: only letters, numbers, and spaces allowed
if (empty($shape) || !preg_match('/^[a-zA-Z0-9 ]+$/', $shape)) {
    echo json_encode(array('status' => false, 'message' => 'Special characters are not allowed. Only letters, numbers, and spaces are accepted.'));
    return;
}

// Max length check: DB column is 30 characters
if (strlen($shape) > 30) {
    echo json_encode(array('status' => false, 'message' => 'Shape name cannot exceed 30 characters.'));
    return;
}

$data = array('name' => $shape, ...);
```

Same pattern applied to the **Update** case.

## Verification
1. **Special char test**: Enter `Round@#$` → Error shown at JS level, form doesn't submit
2. **Length test**: Enter a 35-character name → Error shown at JS level
3. **Bypass JS test**: Use browser DevTools to submit directly → PHP rejects with JSON error
4. **Valid name**: Enter `Marquise Cut` → Success
5. **Leading/trailing spaces**: Enter `  Round  ` → Gets trimmed to `Round`

## Notes
- 3-layer validation ensures security even if JS is bypassed (e.g., via curl or browser DevTools)
- DB column width was changed from `varchar(20)` to `varchar(30)` during the fix session — ensure ALTER TABLE is applied: `ALTER TABLE ret_shape MODIFY name VARCHAR(30);`
- Pattern `^[a-zA-Z0-9 ]+$` allows alphanumeric + spaces only — adjust if hyphens/dots needed
- Same validation pattern should be audited across: Category, Sub-Category, Design, and other master modules
