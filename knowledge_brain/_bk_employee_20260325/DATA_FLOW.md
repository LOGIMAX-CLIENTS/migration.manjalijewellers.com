# Employee Module — Data Flow

> **Brain Updated:** 2026-03-17 | **Round:** 1

---

## Flow 1: CREATE Employee (Add)

**Trigger:** Admin submits employee form at `/employee/emp_post/Add`

```
JS Pre-validation:
├─ emp_code uniqueness: AJAX → /admin_employee/checkempcode
├─ mobile uniqueness:   AJAX → /employee/check_mobile
└─ username uniqueness: AJAX → /employee/checkuser  ← ⚠️ BROKEN (EMP-BUG-001)

Controller emp_post('Add') [L437–582]:
│
├─ Validate emp_code length (≥3 chars) → redirect on fail
│
├─ Password hashing:
│    $basepwd = base64_encode($emp_data['passwd'])   ← legacy
│    $pwd     = password_hash($emp_data['passwd'])   ← bcrypt
│
├─ Build $emp['info'] (all employee fields)
├─ Build $emp['address'] (address fields with id_country/state/city)
│
├─ ⚠️ NO trans_begin() called (EMP-BUG-002)
│
├─ insert_employee([$info, $address]) [model L430–474]:
│    INSERT employee (info fields)
│    $emp_id = insert_id()
│    INSERT address (with id_employee = $emp_id)
│    Returns: $emp_id (or 0)
│
├─ IF emp_wallet_account_type == 1:
│    emp_wallet_acc($emp_id) → INSERT wallet_account
│    ⚠️ issued_date = date('y-m-d') → 2-digit year (EMP-BUG-011)
│
├─ IF $_FILES['emp_img']['name'] set AND $emp_id > 0:
│    set_image($emp_id):
│        mkdir(EMP_IMG_PATH/{id}/, 0777)  ← 0777 (EMP-BUG-014)
│        upload_img → save to assets/img/employee/{id}/employee.jpg
│        //$data['emp_img'] = $filename   ← COMMENTED OUT (EMP-BUG-013)
│
├─ IF $emp_id:
│    INSERT employee_settings (access_time_from=00:00:01, to=23:59:58)
│
├─ trans_status() check: ← ⚠️ ALWAYS TRUE (no trans_begin)
│    → set_flashdata success
│    → redirect('employee')
└─ (No redirect on failure — page hangs)
```

**Tables Written (Create):**
```
employee         ← INSERT
address          ← INSERT (via insert_employee)
wallet_account   ← INSERT (conditional)
employee_settings ← INSERT (default access times)
filesystem       ← WRITE (assets/img/employee/{id}/)
```

---

## Flow 2: EDIT Employee

**Trigger:** Admin submits edit form at `/employee/emp_post/Edit/{id}`

```
Form Load (emp_form('Edit', $id)):
├─ get_emp_record($id) → employee fields
├─ get_emp_address($id) → address fields
├─ IF id_branch == '' (superadmin):
│    passwd = __decrypt(passwd) → show decoded password in form
│    (branch-admins do NOT see password)
└─ build $data['emp'] array → render form

Save (emp_post('Edit', $id)):
├─ Build $emp['info'] with id_employee from POST
├─ Password change detection:
│    $pwd_check = check_password($id, raw_input)
│    IF pwd_check: store raw input as passwd ← broken (EMP-BUG-015)
│    ELSE: store base64_encode(raw_input)
│    Always: pwd_hash = password_hash(raw_input)
│
├─ update_employee($data) [model L480–538]:
│    UPDATE employee WHERE id_employee=$id
│    SELECT * FROM address WHERE id_employee='.$emp_id.'  ← BUG: literal string (EMP-BUG-003)
│    (Always falls to INSERT branch)
│    INSERT address (duplicate added on every edit)   ← DATA CORRUPTION
│
├─ IF status AND $_FILES['emp_img'] set:
│    set_image($emp_id) → upload image (path not saved to DB)
│
└─ redirect('employee')
```

**Critical Issue:** Every edit inserts a NEW duplicate address record. The `get_emp_address()` on next load fetches `row_array()` — returns only one row, so the user only sees one address. But over time the address table accumulates one duplicate per edit per employee.

---

## Flow 3: DELETE Employee

**Trigger:** GET `/employee/emp_post/Delete/{id}` (GET-based state change)

```
emp_post('Delete', $id):
├─ ⚠️ No dependency check (EMP-BUG-016)
│
├─ delete_emp_record($id) [model L544–570]:
│    DELETE FROM address WHERE id_employee=$id
│    IF address deleted:
│        DELETE FROM employee WHERE id_employee=$id
│    ← employee_settings NOT cleaned
│    ← employee_devices NOT cleaned
│    ← wallet_account NOT cleaned
│
├─ IF status AND dir exists:
│    rrmdir(EMP_IMG_PATH / $id) → delete image folder
│
└─ redirect('employee')
```

**Orphan records after delete:**
- `employee_settings` (settings remain for deleted employee ID)
- `employee_devices` (device registrations remain)
- `wallet_account` (wallet remains)
- `customer.allocated_employee` FK reference (broken reference)

---

## Flow 4: Device Enable/Disable

**Trigger:** Admin toggles device status button in employee list

```
POST /employee/enable_device:
├─ $device_details = $_POST['enable_device']  ← raw POST (EMP-BUG-005)
│
├─ Log to log/device_log{date}/{date}.txt
│
├─ IF app_type == 1 (Collection):
│    IF enable: check enabled count < chit_settings.chitCollectionEmpCount
│    IF disable: always allow
│
├─ IF app_type == 2 (Estimation):
│    IF enable: check enabled count < ret_settings.estimation_app_devices_count
│               (loads ret_estimation_model at runtime)
│    IF disable: always allow
│
├─ IF allow_update:
│    trans_begin()
│    update_emp_device_status(uuid, status) → UPDATE employee_devices
│    trans_commit/rollback
│    session flash data
│
└─ redirect('employee')
```

---

## Flow 5: Employee Settings (Discount / Access Time per Employee)

**Trigger:** Admin in emp_set_list view, adds/updates settings rows

```
GET /employee/employee_settings/list:
→ render emp_set_list.php view with bulk_wastage_discount + dia_disc data

AJAX /employee/employee_settings/empset_list:
→ employees with NO settings record (for assignment dropdown)

AJAX /employee/employee_settings/active_employee:
→ all active employees (for selection)

AJAX /employee/employee_settings (default):
POST from_date + to_date + id_employee
→ ajax_emp_setting() [model L860–876]
   → SELECT from employee_settings LEFT JOIN employee
   → Filtered by employee and date range
   ⚠️ $id_employee raw in SQL (EMP-BUG-017)

POST /employee/update_emp_data:
→ For each req_data[] item:
   IF create_new == 1:
       trans_begin → insertData(data, 'employee_settings')
       → trans_commit/rollback + log
   ELSE:
       trans_begin → updateData(data, 'id_emp_sett', id_emp_sett, 'employee_settings')
       → trans_commit/rollback + log

AJAX /employee/employee_settings/updateAccessTimeAll:
→ Raw $_POST['access_time_from'] (EMP-BUG-004)
→ updEmpAccessTime() → raw SQL update (EMP-BUG-007)
→ Updates ALL employee_settings records (no employee filter)

AJAX /employee/employee_settings/delete/{id}:
→ trans_begin → deleteData('id_emp_sett', $id, 'employee_settings')
→ trans_commit/rollback → redirect
```

---

## Flow 6: Authentication (Login — NOT in this controller)

**Used by:** Login controller (separate) — but model methods live here

```
authenticate_user_id($username, $passwd) [model L325–351]:
├─ $username = $this->db->escape($username)  ← safe
├─ SELECT ... FROM employee WHERE active=1 AND username=$username LIMIT 1
├─ IF 1 row found:
│    password_verify($passwd, $row->pwd_hash)  ← bcrypt
│    IF match: return employee row
│    ELSE: return ['status'=>2, 'msg'=>'Invalid password']
└─ ELSE: return ['status'=>2, 'msg'=>'Invalid']

checkAccessTime($id_emp) [model L807–836]:
├─ SELECT access_time_from, access_time_to FROM employee_settings WHERE id_employee=$id_emp
├─ IF settings found:
│    IF access_time_from == NULL → always allow
│    ELSE: compare now() BETWEEN from AND to
│    → returns {status: bool, msg: string}
└─ ELSE: returns {status: false, msg: 'not configured'}
```
