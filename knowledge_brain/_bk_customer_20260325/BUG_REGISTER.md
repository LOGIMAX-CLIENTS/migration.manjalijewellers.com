# Customer Module — Bug Register

> **Brain Updated:** 2026-03-17 | **Total Bugs:** 32 | **Rounds:** R1 + R2

---

## Quick-Fix Reference

| Priority | Count | Fix First |
|---|---|---|
| P0 Critical | 0 | — |
| P1 High | 8 | CUS-BUG-001, 002, 003, 004, 005, 006, 018, 019 |
| P2 Medium | 18 | CUS-BUG-007 through 017, 020–028 |
| P3 Low | 6 | CUS-BUG-016, 017, 029, 030, 031, 032 |
| **Total** | **32** | |

---

## P1 — High Priority Bugs

### CUS-BUG-001 | P1 | Agent Allocation Always Silently Fails

**File:** `admin/application/controllers/admin_customer.php`  
**Lines:** L1935–1936  
**When:** Any time admin tries to bulk-allocate an agent to customers

**Root Cause:**
```php
$total = count($cus);   // = N (correct)
$total = array();       // ← overwrites with empty array immediately
if ($total > 0) {       // always false → allocation loop never runs
```

**Effect:** POST returns `{status: 0, total: N, not_allocated: ...}` but user sees success or no clear error because `$i` is undefined at the outer `if(count($cus) == $i)`.

**Fix:**
```php
$total = count($cus);
// REMOVE: $total = array();
if ($total > 0) { ... }
```

---

### CUS-BUG-002 | P1 | Path Traversal in `download()`

**File:** `admin/application/controllers/admin_customer.php`  
**Lines:** L1758–1767  
**When:** Any authenticated admin hits `/customer/download/{id}/{file}`

**Root Cause:**
```php
$img_path = self::CUS_IMG_PATH . $id . "/" . $file . ".jpg";
$data = file_get_contents($img_path);
force_download($name, $data);
```
No sanitization of `$id` or `$file`. An attacker with admin access can craft:  
`/customer/download/../../config/database`  
→ reads `/admin/assets/img/customer/../../config/database.jpg` (will fail .jpg check)  
But: `$file` could be `pan%2F..%2F..` after URL decode → further traversal.

**Fix:** Validate `$id` is numeric and `$file` is in an allowed list (`['pan', 'voterid', 'rationcard', 'customer']`).

---

### CUS-BUG-003 | P1 | SQL Injection in `Searchcustomer()`

**File:** `admin/application/models/customer_model.php`  
**Line:** L546  
**When:** Admin uses customer profile search field

**Root Cause:**
```php
WHERE username like '%{$SearchTxt}%' OR mobile like '%{$SearchTxt}%'
```
Direct string interpolation. Input `%' OR 1=1-- -` dumps all customers.

**Fix:** Use CI query builder: `$this->db->like('username', $SearchTxt)->or_like('mobile', $SearchTxt)`

---

### CUS-BUG-004 | P1 | SQL Injection in `ajax_get_customers()`

**File:** `admin/application/models/customer_model.php`  
**Line:** L139  
**When:** Scheme join dropdown loads customer search

**Root Cause:**
```php
$customers = $this->db->query("... WHERE c.firstname LIKE '$param%' ...")
```

**Fix:** Use `$this->db->like()` or `$this->db->escape()`.

---

### CUS-BUG-005 | P1 | SQL Injection in `isCustomerExist()`

**File:** `admin/application/models/customer_model.php`  
**Line:** L581  
**When:** Manual import dedup check

**Root Cause:** `WHERE mobile={$mobile}` — unquoted numeric mobile injection.

**Fix:** `$this->db->where('mobile', $mobile);`

---

### CUS-BUG-006 | P1 | `delete_customer()` Leaves Orphan KYC Records

**File:** `admin/application/models/customer_model.php`  
**Lines:** L353–364  
**When:** Customer is deleted

**Root Cause:**
```php
DELETE FROM address WHERE id_customer=$id
DELETE FROM customer WHERE id_customer=$id
DELETE FROM wallet_account WHERE id_customer=$id
// ← kyc records NOT deleted
```

**Fix:** Add `$this->db->where('id_customer',$id); $this->db->delete('kyc');` before address delete.

---

### CUS-BUG-018 | P1 | Employee Allocation Always Silently Fails

**File:** `admin/application/controllers/admin_customer.php`  
**Lines:** L2574–2575  
**When:** Admin bulk-allocates employee to customers

**Root Cause:** Identical to CUS-BUG-001 — `$total = array()` overwrites count.

**Fix:** Same as CUS-BUG-001 — remove the second `$total = array();` line.

---

### CUS-BUG-019 | P1 | `set_image()` — Image Not Persisted to DB

**File:** `admin/application/controllers/admin_customer.php`  
**Lines:** L1680–1719  
**When:** Traditional file upload for customer/pan/voter/ration images

**Root Cause:**
```php
$data = array();
$data['cus_img'] = $filename;  // assembled but...
// ...function ends without: $this->update_images($cus_id, $data);
```
File is saved to disk but DB column is never updated.

**Fix:** Add at the end of `set_image()`:
```php
if (!empty($data)) {
    $this->customer_model->update_images($cus_id, $data);
}
```
Or return `$data` and call `update_images()` from the caller. Note: function needs `$cus_id` parameter added.

---

## P2 — Medium Priority Bugs

### CUS-BUG-007 | P2 | `get_customer()` Hardcoded Subquery ID

**File:** `admin/application/models/customer_model.php`  
**Line:** L149  
**Root Cause:** `SELECT count(id_scheme_account) ... where id_customer=1` — hardcoded `1`.  
**Impact:** All customers shown with scheme count of customer 1 in RM view.  
**Fix:** Replace `=1` with `=c.id_customer` in the correlated subquery.

---

### CUS-BUG-008 | P2 | `added_by = 1` Hardcoded on Customer Add

**File:** `admin/application/controllers/admin_customer.php`  
**Line:** L481  
**Root Cause:** `'added_by' => 1` — always "Admin" regardless of session source.  
**Fix:** Dynamically set based on session data or add a hidden form field.

---

### CUS-BUG-009 | P2 | Password Stored as `base64_encode()` — Not Encrypted

**File:** `admin/application/models/customer_model.php`  
**Lines:** L13–20  
**Root Cause:** `__encrypt()` = `base64_encode()`. `base64_decode()` to recover.  
**Fix:** Use PHP `password_hash()` + `password_verify()` pattern (bcrypt).

---

### CUS-BUG-010 | P2 | `getImageData()` Defined 3× Inside Method Bodies

**File:** `admin/application/controllers/admin_customer.php`  
**Lines:** L593, L1161, L1454  
**Root Cause:** PHP function redeclaration — fatal error if executed twice in same request.  
**Fix:** Extract as a proper private method.

---

### CUS-BUG-011 | P2 | `mkdir(0777)` — World-Writable Directories

**File:** `admin/application/controllers/admin_customer.php`  
**Locations:** 8+ calls across `store_KycImages()`, `get_kyc_images()`, `set_image()`  
**Fix:** Use `mkdir(path, 0755, TRUE)` for production servers.

---

### CUS-BUG-012 | P2 | GET-based State Changes (CSRF)

**File:** `admin/application/controllers/admin_customer.php`  
**Lines:** L1733, L1745  
`profile_status($status, $id)` and `customer_status($status, $id)` called via GET.  
**Fix:** Change to POST with CSRF token validation.

---

### CUS-BUG-013 | P2 | SQL Injection in `get_customer_by_mobile()`

**File:** `admin/application/models/customer_model.php`  
**Line:** L477  
`WHERE mobile={$mobile}` — unquoted. **Fix:** Use CI binding.

---

### CUS-BUG-014 | P2 | JS URL Mismatch: `ajax_list` vs `ajax_customers`

**File:** `admin/assets/js/customer.js`  
**Lines:** L1000, L1598  
JS calls `customer/ajax_list` — controller has no `ajax_list()` method (method is `ajax_customers()`).  
**Fix:** Align JS URL with controller method name, or add route alias.

---

### CUS-BUG-015 | P2 | `customer/without_acc_details` — Unknown JS Endpoint

**File:** `admin/assets/js/customer.js`  
**Line:** L1236  
JS calls `customer/without_acc_details` — not found in controller.  
**Impact:** Feature broken (likely dropdown for unallocated customers).

---

### CUS-BUG-020 | P2 | Edit KYC Skipped if `update_customer()` Returns 0

**File:** `admin/application/controllers/admin_customer.php`  
**Line:** L904  
`get_kyc_byid($cus_id)` where `$cus_id` = 0 on DB failure → all KYC silently skipped.  
**Fix:** Use `$id` (the route parameter) instead of `$cus_id` for KYC fetch.

---

### CUS-BUG-021 | P2 | `$kycData` Undefined in Edit Pan Insert Path

**File:** `admin/application/controllers/admin_customer.php`  
**Line:** L955  
`$kycData['pan_doc_url']` — `$kycData` not defined at this scope. Should be `$kyc_data_img['pan_doc_url']`.  
**Fix:** `$doc_url = isset($kyc_data_img['pan_doc_url']) ? $kyc_data_img['pan_doc_url'] : NULL;`

---

### CUS-BUG-022 | P2 | `$cus['mobile']` Undefined in `wallet_account_cus()` Else Branch

**File:** `admin/application/controllers/admin_customer.php`  
**Line:** L2622  
`$cus` is the foreach loop variable — undefined in the else block.  
**Fix:** `echo 'No customers found.';`

---

### CUS-BUG-023 | P2 | `getkycdata_byid()` Uses Raw `$_POST`

**File:** `admin/application/controllers/admin_customer.php`  
**Line:** L2479  
`$cus_id = $_POST['cus_id']` — bypasses CI input helper.  
**Fix:** `$cus_id = $this->input->post('cus_id');`

---

### CUS-BUG-024 | P2 | `mobile_available_import()` — `$id_customer` Undefined

**File:** `admin/application/models/customer_model.php`  
**Line:** L1125  
`if ($id_customer)` — `$id_customer` never defined as parameter. PHP notice + wrong behavior.  
**Fix:** Add `$id_customer = ""` as second parameter or remove the if block.

---

### CUS-BUG-025 | P2 | `receiptFrmt()` — Duplicate case 6 (Dead Code)

**File:** `admin/application/models/customer_model.php`  
**Lines:** L978, L980  
Two `else if ($set['scheme_wise_receipt'] == 6)` blocks — second never reached.  
**Fix:** Change second case to `7` and define the correct branch-year format.

---

### CUS-BUG-026 | P2 | `zone('add')` — 2-Digit Year Format Bug

**File:** `admin/application/controllers/admin_customer.php`  
**Line:** L2494  
`'date_add' => date('y-m-d H:i:s')` — lowercase `y` = `26` not `2026`.  
**Fix:** `date('Y-m-d H:i:s')`

---

### CUS-BUG-027 | P2 | `zone('update')` — Same 2-Digit Year Bug

**File:** `admin/application/controllers/admin_customer.php`  
**Line:** L2531  
Same fix: `date('Y-m-d H:i:s')`.

---

### CUS-BUG-028 | P2 | Zone Update Flash Message Copy-Paste Error

**File:** `admin/application/controllers/admin_customer.php`  
**Line:** L2538  
`'message' => 'Rate successfully'` — should be `'Zone updated successfully'`.

---

## P3 — Low Priority Bugs

### CUS-BUG-016 | P3 | `get_village()` Returns Single Row for Dropdown

**File:** `admin/application/models/customer_model.php`  
**Line:** L485–489  
Uses `row_array()` — only 1 village returned. Should be `result_array()`.

---

### CUS-BUG-017 | P3 | `cus_profile` Log Written to `$this->log_model` (Direct Prop Access)

**File:** `admin/application/controllers/admin_customer.php`  
**Line:** L1911  
`$this->log_model->log_detail(...)` — model loaded as constant alias, should be `$this->$log_model_const` or `$this->log_model` (happens to work because CI loads it under its class name, but inconsistent with module pattern).

---

### CUS-BUG-029 | P3 | Zone Delete Without Village Dependency Check

**File:** `admin/application/controllers/admin_customer.php`  
**Line:** L2515–2524  
Zone deleted without checking if villages reference it.  
**Fix:** Add dependency check: `SELECT id_village FROM village WHERE id_zone=$id`

---

### CUS-BUG-030 | P3 | `getFormatedNumber()` — Uninitialized `$finalFormat`

**File:** `admin/application/models/customer_model.php`  
**Line:** L1116  
`$finalFormat` uninitialized if `$frmt_short_code` is null/empty → PHP notice + NULL return.  
**Fix:** Initialize `$finalFormat = '';` at top of function.

---

### CUS-BUG-031 | P3 | 19 AJAX Calls Without Error Handlers in JS

**File:** `admin/assets/js/customer.js`  
**Lines:** L717, L736, L1189, L1291, L1371, L1390, L1566, L1598, L1726, L1821, L1863, L4038, L4080, L4194, L4311, L4364, L4397, L4428, L4452  
19 of 31 AJAX calls have no `error:` callback — silent failures for critical operations.

---

### CUS-BUG-032 | P3 | 35 `console.log` Statements in Production JS

**File:** `admin/assets/js/customer.js`  
35 debug log statements should be removed for production.
