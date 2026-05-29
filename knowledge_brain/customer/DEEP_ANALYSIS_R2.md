# Customer Module — Deep Analysis Round 2

> **Date:** 2026-03-17 | **R3-Upgrade stamp:** 2026-03-25 | **Focus:** Edit path, remaining methods, JS security, model last-mile bugs

---

## R2-A: `cus_post('Edit')` — Full KYC Update Path

**Entry:** POST `/customer/cus_post/Edit/{id}`  
**Lines:** L818–1553

### Password Handling (Edit)
```php
L821: $pwd_check = $this->$model->check_password($id, $cus['passwd']);
L852: 'passwd' => ($pwd_check == TRUE ? $cus['passwd'] : $this->$model->__encrypt($cus['passwd']))
```
**Rule:** If password matches existing stored (base64) value → keep as-is. If changed → re-encode.  
**Bug:** `check_password()` queries `WHERE passwd = {$pswd}` — if user sends the currently stored base64 string, it will match and be stored as-is. If they change it, the new value is base64-encoded. **But**: the comparison in model `check_password()` compares raw input against stored base64. The UI must send the already-decoded password for this to work. This is fragile.

### Edit KYC Update Logic (Critical Bug Zone)

```php
L884: $cus_id = $this->$model->update_customer($cus_data, $id);
// update_customer() returns: $id (on success) OR 0 (on failure)

L904: $existingkyc = $this->$model->get_kyc_byid($cus_id);
// ⚠️ BUG: If update_customer() failed → $cus_id = 0
//          get_kyc_byid(0) → fetches kyc WHERE id_customer=0 (likely empty)
//          existingkyc = null → KYC update block skipped
//          BUT the transaction is still in progress (no early return)
```

**KYC Update Decision Tree (Edit path):**
```
get_kyc_byid($cus_id) → $existingkyc
│
├─ IF existingkyc != null:
│    │
│    ├─ get_kyc_byid($cus_id, 2)  → pan KYC
│    │    IF exists: updkycData(pan_data, $id, 2)   → UPDATE
│    │    ELSE:      insert_kyc(pan_data)             → INSERT
│    │
│    ├─ get_kyc_byid($cus_id, 3)  → aadhar KYC
│    │    IF exists: updkycData(aadhar_data, $id, 3) → UPDATE
│    │    ELSE:      insert_kyc(aadhar_data)          → INSERT
│    │
│    └─ get_kyc_byid($cus_id, 1)  → bank kyc list
│         FOR EACH bank in bank_details[]:
│             IF row['id_kyc'] set: update_kyc($id_kyc, data) → UPDATE by PK
│             ELSE:                 insert_kyc(data)           → INSERT
│
└─ ELSE (no kyc at all):
     → SKIP all kyc operations silently
```

**Secondary Bug (L955):**
```php
$doc_url = (isset($kycData['pan_doc_url']) ...
```
Uses `$kycData` (undefined at this point — it's set inside `get_kyc_images()` but not returned to this scope). Should be `$kyc_data_img`. Pan PDF URL will always be NULL on Edit path for new PAN inserts.

### Edit Post-Save Redirect
```
L807-L816 (Add path): redirect('account/add')  — creates account immediately
L1553 (Edit path): redirect('customer')         — returns to customer list
```
**Design Note:** After ADD, customer is auto-redirected to Account creation. After EDIT, returns to list.

---

## R2-B: `allocate_employee_toCuctomers()` — Same Bug as Agent

**Lines:** L2570–2609

```php
L2574:  $total = count($cus);  // e.g. = 3
L2575:  $total = array();      // ⚠️ SAME BUG — immediately overwrites with empty array
L2578:  if ($total > 0)        // IN PHP: array() > 0 evaluates to TRUE (array is non-zero)
                               // BUT $i is never initialized inside the block
L2600:  if (count($cus) == $i) // $i undefined here → PHP notice → comparison fails
```

**Exact same copy-paste bug as `allocate_agent_toCuctomers()`** (L1930–1958). Employee allocation also silently fails.

**Impact:** Both bulk allocation features (Agent & Employee) are broken by the same bug. No error is shown to the user — the response may show status=1 due to PHP loose comparison behavior.

---

## R2-C: `wallet_account_cus()` — HTMLOutput Bug + Reference Error

**Lines:** L2611–2625

```php
L2614: $customer = $this->$model->get_cus();
// get_cus() returns customers without wallet accounts

L2615: if (sizeof($customer) > 0) {
L2616:     foreach ($customer as $cus) {
L2617:         $this->wallet_account_create($cus['id_customer'], $cus['mobile']);
L2618:         echo '<br/>'; echo 'Mobile- ' . $cus['mobile'] . '| Created successfully';
L2619:     }
L2620: } else {
L2621:     echo '<br/>';
L2622:     echo 'Mobile- ' . $cus['mobile'] . '| Unable to proceed'; // ⚠️ BUG
// $cus is not defined in the ELSE branch (loop variable $cus only exists inside foreach)
// → PHP Notice: Undefined variable $cus → outputs "Mobile- | Unable to proceed"
}
```

**Impact:** When no customers need wallets, error message references undefined `$cus`. Minor UI bug.  
**Also:** This function echoes raw HTML — it's a debug/one-shot batch utility that was never converted to a proper API or admin tool.

---

## R2-D: `getkycdata_byid()` — Raw `$_POST` Bypass

**Lines:** L2477–2484

```php
function getkycdata_byid()
{
    $cus_id = $_POST['cus_id'];  // ⚠️ Direct $_POST — bypasses CI input sanitization
    ...
    echo json_encode($data);
}
```

CI's `$this->input->post()` provides XSS filtering and is the proper way to read POST data. Using `$_POST` directly bypasses this. While `$cus_id` here flows into `get_kyc_byid()` which uses CI's query builder (safe via binding), the pattern is inconsistent and should be corrected.

---

## R2-E: `mobile_available_import()` — Undefined Variable Fatal

**Model Lines:** L1121–1135

```php
function mobile_available_import($mobile)
{
    $this->db->select('mobile');
    $this->db->where('mobile', $mobile);
    if ($id_customer) {            // ⚠️ $id_customer NEVER DEFINED in this method
        $this->db->where('id_customer <>', $id_customer);
    }
    ...
}
```

`$id_customer` is never passed as a parameter or declared. PHP will use `NULL`, and the `if (null)` evaluates to `false`, so the `where` clause is skipped. The function effectively becomes a simple mobile existence check — but with a PHP Notice emitted on every call. It's a copy-paste of `mobile_available($mobile, $id_customer="")` but the second parameter was forgotten.

---

## R2-F: `zone()` — Clean CRUD, Minor Issues

**Lines:** L2485–2561

**Assessment:** Zone CRUD is clean. Uses `trans_begin/commit/rollback` properly.

**Minor Issues Found:**
- `L2494: 'date_add' => date('y-m-d H:i:s')` — lowercase `'y'` = 2-digit year (e.g. `'26-03-17'`). Should be `'Y-m-d'`. This is a date formatting bug (2-digit year stored).
- `L2531: 'date_upd' => date('y-m-d H:i:s')` — same 2-digit year bug on update.
- `L2538: 'message' => 'Rate successfully'` — copy-paste error in flash message: says "Rate" instead of "Zone updated".
- Zone delete (`L2515`) does NOT check if villages are assigned to the zone before deleting. Cascade-delete risk.

---

## R2-G: JS Security Scan Results

### AJAX Without Error Handlers: 19 / 31

Critical missing error handlers:
| JS Line | Endpoint | Risk |
|---|---|---|
| L1566 | `customer/cus_profile/update/` | Profile update failure is silent |
| L1726 | `allocate_agent_toCuctomers` | Agent allocation failure silent (compound bug) |
| L1821 | `allocate_employee_toCuctomers` | Employee allocation failure silent |
| L4364/4397 | `zone/add` | Zone add fail = silent, no user feedback |
| L4452 | `zone/update` | Zone update fail = silent |
| L4194 | `getkycdata_byid` | KYC fetch fail = silent, stale UI |

### console.log Left in Production
**Count:** 35 occurrences — should be removed before production deploy.

### Image Upload: No File Type Validation
Controller's `store_KycImages()` and `get_kyc_images()` accept any base64 payload and decode it to a file. There is **zero MIME type or file extension validation** — a malicious user could upload a PHP webshell as a "KYC image":
```
Craft: data:image/png;base64,{base64-of-php-webshell.php}
Upload via pan_img or aadhar_img field
Stored at: assets/kyc/pan/{id}/pan_front.png
→ PHP won't execute .png, so this specific path is safe
```
**But**: In `store_KycImages()` at L2002:
```php
file_put_contents($img_path . 'pan_' . $id . '.png', $panimagebase64)
```
Extension is always `.png` — webshell can't execute. **However**, in `get_kyc_images()` for pb/ch uploads (L2184):
```php
$filename = 'pb_' . time() . '_' . bin2hex(random_bytes(3)) . '.pdf';
```
Extension is extracted from the MIME type in the data URI — but MIME type in the data URI is **user-controlled**. If user sends `data:text/x-php;base64,...`, extension would be... actually the mime-to-extension mapping is done by regex, so `.php` wouldn't be mapped cleanly. However the risk surface exists and should be validated.

---

## R2-H: `set_image()` — Missing `update_images()` Call

**Lines:** L1680–1719

`set_image()` uploads images but **never calls `update_images()`** to save the filenames to the database. The `$data` array is assembled (L1682) with filenames but is never returned or passed to the model. The DB columns `cus_img`, `pan_proof`, `voterid_proof`, `rationcard_proof` are **never updated** by this function.

```php
L1682: $data = array();
L1693: $data['cus_img'] = $filename;
...
L1719: }  // ← function ends without update_images() or return $data
```

This means traditional file upload (not webcam base64) saves the file to disk but the DB still has the old path. The webcam path via `cus_post → base64ToFile → $_FILES['cus_img']` followed by `set_image()` similarly fails to persist the path.

**⚠️ This is a P1 bug** — image uploads via traditional file upload don't persist to DB.

---

## R2-I: Model — Account Formatting Edge Cases

### `receiptFrmt()` — Duplicate case
**Model L980:**
```php
} else if ($set['scheme_wise_receipt'] == 6) {   //Financial Year with Branch wise
    $rcptFrmt = $rcpt['receipt_year'] . $rcpt['branch_code'] . '-' . $rcpt['receipt_no'];
}
```
**Bug:** `scheme_wise_receipt == 6` is already handled at L978 (Financial Year with Scheme & Branch wise). This second case 6 **never executes** — the first matching `else if` wins. The branch-only year format is dead code. The actual value 7 for "Branch-wise only" is probably what was intended.

### `getFormatedNumber()` — Uninitialized $finalFormat
**Model L1116:**
```php
return $finalFormat;
```
If `$frmt_short_code` is empty or null, `$finalFormat` is never initialized → PHP notice: undefined variable. Returns `NULL` instead of `''`.

---

## New Bugs (Round 2)

| Bug ID | Severity | Location | Description |
|---|---|---|---|
| CUS-BUG-018 | P1 | `admin_customer.php` L2570–2575 | `allocate_employee_toCuctomers()`: identical `$total = array()` overwrite bug as agent allocation — employee allocation always silently fails |
| CUS-BUG-019 | P1 | `admin_customer.php` L1680–1719 | `set_image()`: builds `$data` with uploaded filenames but never calls `update_images()` — traditional file uploads don't persist image paths to DB |
| CUS-BUG-020 | P1 | `admin_customer.php` L904 | `cus_post('Edit')` KYC: `get_kyc_byid($cus_id)` where `$cus_id = update_customer()` result (0 on failure) — KYC update silently skipped if update failed |
| CUS-BUG-021 | P2 | `admin_customer.php` L955 | `cus_post('Edit')`: uses undefined `$kycData['pan_doc_url']` — should be `$kyc_data_img['pan_doc_url']`. PAN PDF URL always NULL for new KYC inserts on Edit |
| CUS-BUG-022 | P2 | `admin_customer.php` L2622 | `wallet_account_cus()` else-branch: `$cus['mobile']` is undefined in else scope — PHP notice + garbled message |
| CUS-BUG-023 | P2 | `admin_customer.php` L2479 | `getkycdata_byid()`: uses `$_POST['cus_id']` directly instead of `$this->input->post('cus_id')` |
| CUS-BUG-024 | P2 | `customer_model.php` L1125 | `mobile_available_import()`: `$id_customer` referenced but never defined as parameter — undefined variable PHP notice on every call |
| CUS-BUG-025 | P2 | `customer_model.php` L980 | `receiptFrmt()`: duplicate `case 6` — second branch (Branch-only year format) is dead code, value 7 likely intended |
| CUS-BUG-026 | P2 | `admin_customer.php` L2494 | `zone('add')`: `date('y-m-d')` — lowercase `y` = 2-digit year stored in `date_add` column |
| CUS-BUG-027 | P2 | `admin_customer.php` L2531 | `zone('update')`: same 2-digit year bug in `date_upd` |
| CUS-BUG-028 | P2 | `admin_customer.php` L2538 | `zone('update')`: flash message says "Rate successfully" — copy-paste error, should be "Zone updated successfully" |
| CUS-BUG-029 | P3 | `admin_customer.php` L2515 | `zone('delete')`: no check for villages assigned to zone before deletion — orphan village records possible |
| CUS-BUG-030 | P3 | `customer_model.php` L1116 | `getFormatedNumber()`: `$finalFormat` may be uninitialized if `$frmt_short_code` is empty — returns NULL instead of `''` |
| CUS-BUG-031 | P3 | `customer.js` (19 AJAX calls) | 19 out of 31 AJAX calls have no `error:` handler — silent failures for critical operations |
| CUS-BUG-032 | P3 | `customer.js` (35 instances) | 35 `console.log` statements left in production JS file |
