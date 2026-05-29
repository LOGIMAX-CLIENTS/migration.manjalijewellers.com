# Employee Module — Bug Register

> **Brain Updated:** 2026-03-17 | **Total Bugs:** 17

---

## Quick-Fix Reference

| Priority | Count | Notes |
|---|---|---|
| P1 Critical | 5 | Fix immediately — broken endpoints, missing transactions, raw POST |
| P2 Medium | 10 | Security, data integrity |
| P3 Low | 2 | Missing validation, UX |

---

## P1 — Critical Bugs

### EMP-BUG-001 | P1 | `isUserAvailable()` — Debug `print_r` + `exit` in Production

**File:** `admin/application/controllers/admin_employee.php`  
**Line:** L161  
**When:** Any time JS calls `/employee/checkuser` to validate username uniqueness

**Root Cause:**
```php
print_r($available); exit;  // ← LEFT IN PRODUCTION
// Lines L163–179 (the actual echo TRUE/FALSE response) are DEAD CODE
```

**Impact:** Username uniqueness check endpoint ALWAYS exits early with raw PHP `1` or empty string. JS receives `print_r` output, not a clean TRUE/FALSE. The employee form's username validation is broken — duplicates may slip through.

**Fix:** Remove `print_r($available); exit;`

---

### EMP-BUG-002 | P1 | `emp_post('Add')` — `trans_status()` Without `trans_begin()`

**File:** `admin/application/controllers/admin_employee.php`  
**Lines:** L526–580  
**When:** Admin creates a new employee

**Root Cause:**
```php
$emp_id = $this->$model->insert_employee($data); // DB insert
// ... (wallet, image, settings inserts)
if ($this->db->trans_status() === TRUE) {  // ← trans_begin() was NEVER called
    $this->session->set_flashdata(...success...);
    redirect('employee');
}
// No redirect on failure → page hangs silently
```

**Impact:**  
1. `trans_status()` is meaningless without `trans_begin()` — always returns TRUE  
2. No `trans_rollback()` — partial inserts (e.g. employee inserted but address insert failed) are not rolled back  
3. No redirect on failure path — page hangs after failed insert

**Fix:**
```php
$this->db->trans_begin();
$emp_id = $this->$model->insert_employee($data);
// ... other inserts
if ($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();
    redirect('employee');
} else {
    $this->db->trans_rollback();
    // set error flashdata + redirect
}
```

---

### EMP-BUG-003 | P1 | `update_employee()` — PHP String Bug in Address Lookup

**File:** `admin/application/models/employee_model.php`  
**Line:** L498  
**When:** Admin edits any employee

**Root Cause:**
```php
$chk_emp_add_exists = $this->db->query(
    "SELECT * from address where id_employee='.$emp_id.'"
);
// ← String is in double quotes but uses single-quote dots to interpolate
// → Literal string: WHERE id_employee='.$emp_id.'
// → Always queries with literal .$emp_id. as value → 0 rows
// → Always falls into INSERT branch
```

**Impact:** Every employee edit attempt tries to INSERT a new address record instead of checking whether to UPDATE. Over time, each employee accumulates duplicate address records.

**Fix:**
```php
$chk_emp_add_exists = $this->db->query(
    "SELECT * FROM address WHERE id_employee = " . $emp_id
);
```

---

### EMP-BUG-004 | P1 | `employee_settings('updateAccessTimeAll')` — Raw `$_POST`

**File:** `admin/application/controllers/admin_employee.php`  
**Lines:** L1006–1007  
**When:** Admin bulk-updates access times for all employees

**Root Cause:**
```php
$updatedata = array(
    'access_time_from' => $_POST['access_time_from'],  // ← raw $_POST
    'access_time_to'   => $_POST['access_time_to'],    // ← raw $_POST
    ...
);
$this->$model_name->updEmpAccessTime($updatedata);
// AND updEmpAccessTime() also injects these raw into SQL string (EMP-BUG-007)
```

**Impact:** SQL injection chained through two layers — raw POST value → raw SQL concatenation.

**Fix:** `$this->input->post('access_time_from')`

---

### EMP-BUG-005 | P1 | `enable_device()` — Raw `$_POST` for Device Operations

**File:** `admin/application/controllers/admin_employee.php`  
**Line:** L1178  
**When:** Admin enables/disables employee mobile devices

**Root Cause:**
```php
$device_details = $_POST['enable_device'];  // ← raw $_POST array
$id_employee = $device_details['id_employee'];  // untrusted, used in queries
$emp_device  = $device_details['uuid'];
```

**Fix:** `$device_details = $this->input->post('enable_device');`

---

## P2 — Medium Priority Bugs

### EMP-BUG-006 | P2 | `get_list_data()` — `$id_company` Undefined

**File:** `admin/application/models/employee_model.php`  
**Line:** L263  
**Root Cause:**
```php
$is_multi_company = $this->admin_settings_model->get_company_settings();
if ($is_multi_company == 1) {
    $this->db->where('employee.id_company', $id_company); // ← $id_company never defined
}
```
`$id_company` is never fetched in this method (forgot `session->userdata('id_company')`).  
**Impact:** PHP notice + company filter never applied — all employees shown regardless of company.  
**Fix:** Add `$id_company = $this->session->userdata('id_company');` before the if block.

---

### EMP-BUG-007 | P2 | `updEmpAccessTime()` — SQL Injection

**File:** `admin/application/models/employee_model.php`  
**Line:** L771  
**Root Cause:**
```php
$sql = $this->db->query(
    "update employee_settings set access_time_from='".$data['access_time_from']."' , access_time_to='".$data['access_time_to']."' where ..."
);
```
`access_time_from` and `access_time_to` from `$_POST` → raw concatenation.  
**Fix:** Use CI query builder with bound parameters.

---

### EMP-BUG-008 | P2 | `get_emp_by_company()` — SQL Injection via `$username`

**File:** `admin/application/models/employee_model.php`  
**Lines:** L882–903  
**Root Cause:** All query branches concatenate `$username` directly:
```php
'where e.username = "'.$username.'"'
```
`$username` comes from the login form — high-risk SQL injection point in authentication path.  
**Fix:** Use `$this->db->escape($username)` (already done in `authenticate_user_id()` but not here).

---

### EMP-BUG-009 | P2 | `get_employee_name_byid()` — SQL Injection via `$emp_dev_id`

**File:** `admin/application/models/employee_model.php`  
**Line:** L958  
```php
$sql = "... and ed.id_collection_device= '".$emp_dev_id."'";
```
`$emp_dev_id` from `$_POST` (via `enable_device()`) → raw injection.  
**Fix:** Use CI binding or `$this->db->escape()`.

---

### EMP-BUG-010 | P2 | `employee_status()` — Flash Message Shows "true"/"false" Literally

**File:** `admin/application/controllers/admin_employee.php`  
**Line:** L957  
**Root Cause:**
```php
'message' => 'Employee status updated as '.($status ? 'active' : 'inactive').' successfully.'
```
`$status` here is the DB update return (1/true), NOT the intended active value (1/0 passed in). The ternary `$status ? 'active' : 'inactive'` will always show "active" because DB update succeeds.  
**Fix:** Use the `$status` parameter (L950) or the `$data['active']` value for the display string.

---

### EMP-BUG-011 | P2 | `emp_wallet_acc()` — 2-Digit Year in `issued_date`

**File:** `admin/application/controllers/admin_employee.php`  
**Line:** L1334  
```php
'issued_date' => date('y-m-d H:i:s')  // ← 'y' = 2-digit year → '26-03-17'
```
**Fix:** `date('Y-m-d H:i:s')`

---

### EMP-BUG-012 | P2 | Employee Password Stored as `base64_encode()` — Not Encrypted

**File:** `admin/application/models/employee_model.php`  
**Lines:** L29–34  
Legacy `passwd` field uses `base64_encode()`. Although `pwd_hash` (bcrypt) is also stored, the raw-reversible `passwd` is still written and used by `authenticate_user()` (if that path is still reachable).  
**Risk:** `passwd` column exposes plain-text-equivalent passwords.  
**Fix:** Remove the `passwd` field writes; use only `pwd_hash`. Deprecate `authenticate_user()`.

---

### EMP-BUG-013 | P2 | `set_image()` — Image Path Never Written to DB

**File:** `admin/application/controllers/admin_employee.php`  
**Line:** L886  
```php
//$data['emp_img']= $filename;   // ← COMMENTED OUT
```
Even if the image is uploaded to disk, the employee record's `image` field is never updated.  
**Impact:** Employee images show default after upload — file is saved but DB doesn't point to it.  
**Fix:** Uncomment the line and add `$this->employee_model->update_employee_only(['image' => $filename], $id);` at end of `set_image()`.

---

### EMP-BUG-014 | P2 | `set_image()` — `mkdir(0777)` World-Writable

**File:** `admin/application/controllers/admin_employee.php`  
**Line:** L866  
**Fix:** `mkdir($img_path, 0755, TRUE)`

---

### EMP-BUG-015 | P2 | Edit: Password Change Detection Broken

**File:** `admin/application/controllers/admin_employee.php`  
**Line:** L627  
```php
$pwd_check = $this->$model->check_password($id, $emp_data['passwd']);
// check_password: WHERE id_employee=$id AND passwd=$pswd
// → Compares raw user input against stored base64
// → If user types the base64 string itself → match → treated as "unchanged"
```
**Root Cause:** `check_password()` (L602–626 model) compares `$pswd` (raw input) against stored `passwd` (base64). These will never match unless someone types the base64 string. So: `$pwd_check` is always FALSE → password always re-encoded, even if user left it unchanged.  
**Impact:** Every edit re-hashes the password unnecessarily. No true "unchanged" detection.  
**Fix:** Proper check: send empty password field to mean "unchanged", skip password update if empty.

---

## P3 — Low Priority Bugs

### EMP-BUG-016 | P3 | Delete Employee — No Dependency Check

**File:** `admin/application/controllers/admin_employee.php`  
**Lines:** L711–741  
No check before deleting employee:
- Customer `allocated_employee` references
- Payment records created by employee
- Collection app data
- Employee settings (deleted separately or orphaned)

**Fix:** Add dependency check similar to customer module's `ajax_check_delete()`.

---

### EMP-BUG-017 | P3 | `ajax_emp_setting()` — SQL Injection via `$id_employee`

**File:** `admin/application/models/employee_model.php`  
**Line:** L867  
```php
($id_employee!='' ? " and e.id_employee=".$id_employee."" :'')
```
`$id_employee` from POST → raw concatenation.  
**Fix:** `$id_employee = (int)$id_employee;`
