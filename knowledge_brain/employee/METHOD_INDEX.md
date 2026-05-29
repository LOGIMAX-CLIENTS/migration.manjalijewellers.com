# Employee Module — Method Index

> **Brain Updated:** 2026-03-25 | **Round:** R2-Upgrade | 25 ctrl + 47 model methods

---

## A. Controller Methods (Alphabetical) — `admin_employee.php`

| Method | Lines | Tables Read | Tables Written | JS Caller |
|---|---|---|---|---|
| `ajax_get_emp_list()` | L67–89 | employee, department, profile, branch, employee_devices | — | `employee.js` L1093 |
| `branchname_list()` | L967–972 | branch | — | `employee.js` L1339 |
| `check_mobile()` | L747–772 | employee | — | `employee.js` L426 |
| `checkempcode()` | L894–939 | employee | — | `employee.js` L383 |
| `emp_form('Add')` | L262–290 | country, department, designation, branch, profile | — | — (GET form) |
| `emp_form('Edit', $id)` | L294–408 | employee, address, country, state, city, department, designation, branch, profile | — | — (GET form) |
| `emp_list()` | L207–238 | chit_settings | — | — (GET page) |
| `emp_post('Add')` | L437–582 | — | employee, address, wallet_account, employee_settings, filesystem | Form submit |
| `emp_post('Delete', $id)` | L711–741 | — | address (DELETE), employee (DELETE), filesystem | — (GET ⚠️) |
| `emp_post('Edit', $id)` | L586–707 | — | employee (UPDATE), address (INSERT or UPDATE) | Form submit |
| `emp_wallet_acc($emp_id)` | L1316–1345 | wallet_account | wallet_account | Internal |
| `employee_settings('list')` | L1030–1036 | ret_stone_discount_master, ret_settings | — | — (GET page) |
| `employee_settings('empset_list')` | L995–998 | employee, employee_settings | — | `employee.js` L1560 |
| `employee_settings('active_employee')` | L999–1002 | employee | — | `employee.js` L1526 |
| `employee_settings('updateAccessTimeAll')` | L1003–1029 | — | employee_settings | `employee.js` L1392 ⚠️ raw $_POST |
| `employee_settings('delete', $id)` | L978–994 | — | employee_settings (DELETE) | `employee.js` L1787 |
| `employee_settings()` (default) | L1037–1049 | employee, employee_settings | — | `employee.js` L1589 |
| `employee_status($s, $id)` | L950–964 | — | employee | List toggle (GET ⚠️ CSRF) |
| `enable_device()` | L1174–1297 | chit_settings, ret_settings, employee_devices | employee_devices | `employee.js` (device panel) ⚠️ raw $_POST |
| `get_dept()` | L97–111 | department | — | `employee.js` L959 |
| `get_designation()` | L115–129 | designation | — | `employee.js` L1023 |
| `get_device_list()` | L1304–1309 | employee_devices | — | `employee.js` L1882 |
| `get_emp_name_list()` | L1298–1303 | employee | — | `employee.js` L1847 |
| `get_employee()` | L943–948 | employee | — | — (debug/bulk) |
| `get_employee_devices()` | L1348–1360 | employee_devices | — | `employee.js` L1985 |
| `get_employee_name_byid()` | L1310–1315 | employee, employee_devices | — | JS device panel |
| `index()` | L44–47 | — | — | — |
| `isUserAvailable()` | L133–187 | employee | — | `employee.js` L917 ⚠️ DEAD (EMP-BUG-001) |
| `rrmdir($path)` | L1158–1173 | filesystem | filesystem | Internal |
| `set_image($emp_id)` | L852–893 | — | filesystem ⚠️ DB UPDATE commented (EMP-BUG-013) | Internal |
| `update_emp_data()` | L1051–1143 | — | employee_settings | `employee.js` L1806 |
| `upload_img($output, $dst, $img)` | L773–851 | filesystem | filesystem | Internal |

---

## B. Model Methods (Alphabetical) — `employee_model.php`

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `__construct()` | L17–23 | — | — | — |
| `__decrypt($str)` | L41–47 | — | — | `emp_form('Edit')` password decode |
| `__encrypt($str)` | L29–35 | — | — | `emp_post('Add')` password encode |
| `ajax_emp_setting($from, $to, $id_emp)` | L860–876 | employee_settings, employee | — | `employee_settings()` default ⚠️ SQLi (EMP-BUG-017) |
| `authenticate_user($user, $pwd)` | L576–598 | employee | — | Legacy login (base64) |
| `authenticate_user_id($user, $pwd)` | L325–351 | employee, branch, profile | — | Active login (bcrypt) |
| `branchname_list()` | L839–845 | branch | — | `branchname_list()` controller |
| `check_empcode($code, $id)` | L639–679 | employee | — | `checkempcode()` controller |
| `check_password($id, $pwd)` | L602–626 | employee | — | `emp_post('Edit')` ⚠️ broken comparison |
| `check_username($user, $id)` | L172–212 | employee | — | `isUserAvailable()` controller |
| `checkAccessTime($id_emp)` | L807–836 | employee_settings | — | Login controller ⚠️ raw SQL |
| `delete_emp_record($id)` | L544–570 | — | address (DELETE), employee (DELETE) | `emp_post('Delete')` |
| `deleteData($field, $value, $table)` | L775–780 | — | `{any}` | Generic delete |
| `get_chit_settings_data()` | L962–966 | chit_settings | — | `emp_list()`, device mgmt |
| `get_dept()` | L133–154 | department | — | `get_dept()` controller |
| `get_design()` | L158–168 | designation | — | `emp_form()` dropdown |
| `get_designation()` | L109–129 | designation | — | `get_designation()` controller |
| `get_device_from_useragent($ua)` | L1049–1063 | — | — | `get_employee_devices()` |
| `get_device_list()` | L941–946 | employee_devices | — | `get_device_list()` controller |
| `get_dia_disc()` | L1096–1102 | ret_stone_discount_master | — | `employee_settings('list')` |
| `get_emp_address($id)` | L396–408 | address | — | `emp_form('Edit')` |
| `get_emp_by_company($company, $user)` | L878–908 | employee, company | — | Login path ⚠️ SQLi (EMP-BUG-008) |
| `get_emp_by_username($name)` | L377–392 | employee | — | Login / internal |
| `get_emp_id($name)` | L412–424 | employee | — | Internal lookup |
| `get_emp_name_list()` | L935–940 | employee | — | `get_emp_name_list()` controller |
| `get_emp_record($id)` | L357–371 | employee | — | `emp_form('Edit')` |
| `get_empBranch_company($company, $branch, $user)` | L910–933 | employee, company | — | Login branch validation ⚠️ SQLi |
| `get_employee_devices($emp_id, $app_type)` | L987–1025 | employee_devices | — | `get_employee_devices()` controller |
| `get_employee_name_byid($emp_id, $dev_id)` | L947–961 | employee, employee_devices | — | `get_employee_name_byid()` controller ⚠️ SQLi (EMP-BUG-009) |
| `get_employee_records()` | L782–786 | employee | — | Bulk / debug |
| `get_empset_lst()` | L853–857 | employee, employee_settings | — | `employee_settings('empset_list')` |
| `get_empty_record()` | L51–105 | — | — | `emp_form('Add')` |
| `get_enabled_device_count($for_est)` | L967–984 | employee_devices | — | `enable_device()` controller |
| `get_list_data()` | L246–297 | employee, department, profile, branch, employee_devices | — | `ajax_get_emp_list()` ⚠️ $id_company undef (EMP-BUG-006) |
| `get_ret_settings($name)` | L1087–1095 | ret_settings | — | `enable_device()` (estimation limit) ⚠️ raw SQL |
| `getActiveemployee()` | L847–851 | employee | — | `employee_settings('active_employee')` |
| `getEmployeeByBranch($branch)` | L1066–1085 | employee | — | External modules ⚠️ SQLi |
| `insert_employee($data)` | L430–474 | — | employee (INSERT), address (INSERT) | `emp_post('Add')` |
| `insertData($data, $table)` | L681–715 | `{any}` schema | `{any}` | Generic insert |
| `isOTPReqToLogin()` | L628–632 | chit_settings | — | Login controller |
| `loginOTP_exp()` | L633–637 | chit_settings | — | Login controller |
| `mobile_available($mobile, $id)` | L216–240 | employee | — | `check_mobile()` controller |
| `update_emp_device_status($dev_id, $status)` | L1027–1047 | — | employee_devices | `enable_device()` controller |
| `update_employee($data)` | L480–538 | address | employee (UPDATE), address (INSERT/UPDATE) | `emp_post('Edit')` ⚠️ BUG (EMP-BUG-003) |
| `update_employee_only($data, $id)` | L790–804 | — | employee | `employee_status()` |
| `updateData($data, $field, $value, $table)` | L717–753 | `{any}` schema | `{any}` | Generic update |
| `updEmpAccessTime($data)` | L769–773 | — | employee_settings | `updateAccessTimeAll` ⚠️ SQLi (EMP-BUG-007) |

---

## C. JS → Controller AJAX Map — `employee.js`

### Internal Endpoints (admin_employee controller)

| JS Line | JS Context | AJAX URL | Controller Method |
|---|---|---|---|
| L383 | Emp code uniqueness | `admin_employee/checkempcode` | `checkempcode()` |
| L426 | Mobile uniqueness | `employee/check_mobile` | `check_mobile()` |
| L917 | Username uniqueness | `employee/checkuser` | `isUserAvailable()` ⚠️ DEAD |
| L959 | Department dropdown | `employee/dept` | `get_dept()` |
| L1023 | Designation dropdown | `employee/designation` | `get_designation()` |
| L1093 | Employee list load | `employee/ajax_emp_list` | `ajax_get_emp_list()` ⚠️ URL mismatch |
| L1339 | Branch dropdown | `admin_employee/branchname_list` | `branchname_list()` |
| L1392 | Bulk access time update | `admin_employee/employee_settings/updateAccessTimeAll` | `employee_settings('updateAccessTimeAll')` |
| L1526 | Active employee dropdown | `admin_employee/employee_settings/active_employee` | `employee_settings('active_employee')` |
| L1560 | Emp without settings | `admin_employee/employee_settings/empset_list` | `employee_settings('empset_list')` |
| L1589 | Settings data load | `admin_employee/employee_settings` | `employee_settings()` default |
| L1787 | Delete setting | `admin_employee/employee_settings/delete/{id}` | `employee_settings('delete', $id)` |
| L1806 | Create/update settings | `admin_employee/update_emp_data` | `update_emp_data()` |
| L1847 | Emp name list (device) | `admin_employee/get_emp_name_list` | `get_emp_name_list()` |
| L1882 | Device list | `admin_employee/get_device_list` | `get_device_list()` |
| L1985 | Employee devices by emp | `admin_employee/get_employee_devices` | `get_employee_devices()` |

### Cross-Module Endpoints

| JS Line | JS Context | AJAX URL | Module |
|---|---|---|---|
| L569 | Country dropdown | `settings/company/getcountry` | Settings |
| L645 | Profile/role dropdown | `settings/profile/ajax_list` | Settings |
| L751 | State dropdown | `settings/company/getstate` | Settings |
| L839 | City dropdown | `settings/company/getcity` | Settings |

---

## D. Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `employee` | get_list_data, get_emp_record, get_emp_by_username, get_emp_id, get_employee_records, getActiveemployee, get_emp_name_list, get_employee_name_byid, check_username, mobile_available, check_empcode, check_password, authenticate_user, authenticate_user_id, get_emp_by_company, get_empBranch_company, getEmployeeByBranch | insert_employee, update_employee, update_employee_only, delete_emp_record |
| `address` | get_emp_address, update_employee (check) | insert_employee, update_employee ⚠️, delete_emp_record |
| `employee_devices` | get_list_data, get_device_list, get_employee_devices, get_employee_name_byid, get_enabled_device_count | update_emp_device_status |
| `employee_settings` | ajax_emp_setting, get_empset_lst, checkAccessTime | insertData (via update_emp_data), updateData (via update_emp_data), updEmpAccessTime, deleteData |
| `department` | get_dept, get_list_data | — |
| `designation` | get_designation, get_design | — |
| `profile` | get_list_data, authenticate_user_id | — |
| `branch` | get_list_data, branchname_list, authenticate_user_id | — |
| `chit_settings` | get_chit_settings_data, isOTPReqToLogin, loginOTP_exp | — |
| `ret_settings` | get_ret_settings | — |
| `ret_stone_discount_master` | get_dia_disc | — |
| `company` | get_emp_by_company, get_empBranch_company | — |
| `wallet_account` | — | emp_wallet_acc (INSERT) |
| `log` | — | log_model::log_detail (audit) |
