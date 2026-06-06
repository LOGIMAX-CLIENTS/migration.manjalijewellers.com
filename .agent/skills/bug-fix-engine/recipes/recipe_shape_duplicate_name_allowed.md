# Recipe: Shape Duplicate Name Allowed — No Validation

## Metadata
- **Pattern ID**: PAT-VAL-010
- **Severity**: HIGH
- **Modules Affected**: Catalog Master (Diamond Shape)
- **Auto-fixable**: No (requires adding new code blocks, not simple string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal — shape master CRUD has no duplicate checks in any client

## Created By
- **Developer**: Antigravity (Black Horest)
- **Client**: retailsource (source repo)
- **Date**: 2026-06-03
- **Source Bug ID**: N/A

## Symptom
Users can add or update a Diamond Shape with a name that already exists in `ret_shape`. This leads to duplicate entries in the master table, causing downstream issues in tagging, billing, and reports where shape is referenced by name.

## Root Cause
The `shape()` method in `admin_ret_catalog.php` for both "Add" and "Update" cases performs zero duplicate checking before inserting/updating. The shape name is accepted as-is from `$this->input->post("name")` with no `trim()`, no case-insensitive comparison, and no DB lookup to verify uniqueness.

## Detection
```command
grep -n "case \"Add\"" admin/application/controllers/admin_ret_catalog.php | head -5
grep -n "input->post(\"name\")" admin/application/controllers/admin_ret_catalog.php
```
Look for shape Add/Update blocks that directly insert without any `SELECT` duplicate check query.

## Files
- `admin/application/controllers/admin_ret_catalog.php` (shape() method — Add and Update cases)
- `admin/assets/js/catalog_master.js` (add_shape() and update_shape() AJAX callbacks)
- `admin/application/views/master/shape/list.php` (Add/Update modal buttons — `data-dismiss="modal"` removal)

## Fix

### File 1: admin/application/controllers/admin_ret_catalog.php

#### Before (Add Case)
```php
case "Add":

    $shape = $this->input->post("name");

    $description = $this->input->post("description");

    $data = array('name' => $shape, 'description' => $description, 'created_by' => $this->session->userdata('uid'), 'created_on' => date("Y-m-d H:i:s"));
```

#### After (Add Case)
```php
case "Add":

    $shape = trim($this->input->post("name"));

    $description = trim($this->input->post("description"));

    // Duplicate check: prevent same shape name from being added again
    $existing = $this->db->query("SELECT shape_id FROM ret_shape WHERE LOWER(TRIM(name)) = LOWER('" . $this->db->escape_str($shape) . "')")->num_rows();
    if ($existing > 0) {
        echo json_encode(array('status' => false, 'message' => 'This shape name already exists. Duplicate entry is not allowed.'));
        return;
    }

    $data = array('name' => $shape, 'description' => $description, 'created_by' => $this->session->userdata('uid'), 'created_on' => date("Y-m-d H:i:s"));
```

#### Before (Update Case)
```php
$shape = $this->input->post('name');

$description = $this->input->post('description');

$data = array("name" => $shape, "description" => $description, 'created_by' => $this->session->userdata('uid'), 'updated_on' => date("Y-m-d H:i:s"));
```

#### After (Update Case)
```php
$shape = trim($this->input->post('name'));

$description = trim($this->input->post('description'));

// Duplicate check: prevent same shape name (exclude current record)
$existing = $this->db->query("SELECT shape_id FROM ret_shape WHERE LOWER(TRIM(name)) = LOWER('" . $this->db->escape_str($shape) . "') AND shape_id != " . (int)$id)->num_rows();
if ($existing > 0) {
    echo json_encode(array('status' => false, 'message' => 'This shape name already exists. Duplicate entry is not allowed.'));
    return;
}

$data = array("name" => $shape, "description" => $description, 'created_by' => $this->session->userdata('uid'), 'updated_on' => date("Y-m-d H:i:s"));
```

### File 2: admin/assets/js/catalog_master.js

#### Before (add_shape AJAX success callback)
```javascript
success:function(data){

    console.log(data)

    $('#shape').val('');

     msg='<div class = "alert alert-success">...shape added successfully.</div>';
```

#### After (add_shape AJAX success callback)
```javascript
success:function(data){

    console.log(data)

    // Check if server returned a validation error
    try {
        var response = (typeof data === 'string') ? JSON.parse(data) : data;
        if (response.status === false) {
            msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> ' + response.message + '</div>';
            $('#error-msg').html(msg);
            return;
        }
    } catch(e) {
        // Not JSON = success (server echoed TRUE)
    }

    $('#shape').val('');

    $('#confirm-add').modal('hide');

     msg='<div class = "alert alert-success">...shape added successfully.</div>';
```

#### Before (update_shape AJAX success callback)
```javascript
success:function(data){

    console.log(data);

      $("div.overlay").css("display", "none");

    window.location.reload(true);
```

#### After (update_shape AJAX success callback)
```javascript
success:function(data){

    console.log(data);

      $("div.overlay").css("display", "none");

    // Check if server returned a validation error
    try {
        var response = (typeof data === 'string') ? JSON.parse(data) : data;
        if (response.status === false) {
            msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> ' + response.message + '</div>';
            $('#error').html(msg);
            return;
        }
    } catch(e) {
        // Not JSON = success (server echoed TRUE)
    }

    $('#confirm-edit').modal('hide');

    window.location.reload(true);
```

### File 3: admin/application/views/master/shape/list.php

#### Before (Add button)
```html
<a href="#" id="add_newshape" class="btn btn-success" data-dismiss="modal" >Add</a>
```

#### After (Add button — remove data-dismiss so modal stays open for errors)
```html
<a href="#" id="add_newshape" class="btn btn-success" >Add</a>
```

#### Before (Update button)
```html
<a href="#" id="update_shape" class="btn btn-success" data-dismiss="modal" >Update</a>
```

#### After (Update button — remove data-dismiss so modal stays open for errors)
```html
<a href="#" id="update_shape" class="btn btn-success" >Update</a>
```

## Verification
1. Navigate to Diamond Shape list → Click "Add New Shape"
2. Enter a name that already exists (e.g., "Round") → Click Add
3. **Expected**: Modal stays open, red error: "This shape name already exists"
4. Enter "round" (lowercase) → Same error (case-insensitive check)
5. Edit an existing shape, change only description → Should succeed (own record excluded)
6. Edit an existing shape, change name to match another existing shape → Duplicate error

## Notes
- Duplicate check is case-insensitive using `LOWER(TRIM(...))` to catch "Round" vs "round" vs " Round "
- Update case excludes own record (`shape_id != $id`) so description-only edits work
- Modal `data-dismiss="modal"` was removed from both Add and Update buttons; modal now closes programmatically only on success via `$('#confirm-add').modal('hide')`
- Same pattern likely applies to other master modules (Category, Sub-Category, Design) — audit recommended
