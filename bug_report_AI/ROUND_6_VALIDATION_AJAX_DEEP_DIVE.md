# 🔬 Round 6 — Validation Functions & Controller AJAX/Endpoints Deep Dive

> **Audit Date**: 2026-02-17  
> **Method**: Sequential analysis of JS validation functions, controller AJAX handlers, customer create/update, old metal flow  
> **Scope**: `ret_estimation.js` L7967–8800 (validation), `admin_ret_estimation.php` L2478–3574 (~30 AJAX endpoints)

---

## Executive Summary

**10 new bugs identified**: **1 P0 (critical)**, **3 P1 (major)**, **4 P2 (minor)**, **2 P3 (cosmetic)**.

> [!CAUTION]
> `cancel_order_tag()` calls `trans_commit()` on failure (L3362) instead of `trans_rollback()` — failed order cancellations are silently persisted in a corrupt state.

---

## Bug Details

---

### EST-R601 — `cancel_order_tag` Commits on Failure (P0)

**File**: `admin_ret_estimation.php` **Lines**: 3355–3365  
**Severity**: P0 — Data Integrity  

```php
// L3355-3365
if ($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();
    $return_data = array('status' => TRUE);
} else {
    $this->db->trans_commit();    // ❌ Should be trans_rollback()
    $return_data = array('status' => FALSE);
}
```

When the transaction fails (e.g., `updateData` at L3341 or L3351 throws a DB error), the code still calls `trans_commit()`. This persists partially-applied changes: the order may be marked as cancelled but the tags may not be unlinked (or vice versa), leaving the system in an inconsistent state.

**Impact**: Failed order cancellations corrupt the tag-to-order mapping. Tags may remain reserved for a "cancelled" order, or become orphaned.

---

### EST-R602 — `validateTagDetailRow`: Empty Tag Code Silently Continues (P1)

**File**: `ret_estimation.js` **Lines**: 8087–8091  
**Severity**: P1 — Validation Bypass  

```javascript
// L8087-8091
if (tag_code == "") {
    row_validate = false;
    // ❌ NO return false — continues to subsequent else-if checks
}
else if ($(this).find('.gwt').val() < 0) {
```

When `tag_code` is empty, `row_validate` is set to `false` but the `each()` loop does NOT `return false` to stop iteration. The code then enters the `else if` chain and eventually reaches:

```javascript
// L8177-8189
if ((calculation_type == 1 || calculation_type == 2 || calculation_type ==3) && (tag_details.length==0)) {
    if (parseFloat(total_mc_va) < parseFloat(total_min_mc_va)) {
        // ...
        row_validate = false;
    }
}
```

If this check passes (e.g., `tag_details.length > 0`), execution continues without resetting `row_validate`. BUT because the initial empty-tag doesn't stop iteration, subsequent rows could override `row_validate = false` with processing that doesn't fail.

**Impact**: Inconsistent validation — empty tag row detected but later rows can mask the failure if they pass and the function only returns the final value of `row_validate`.

**Wait — actual impact analysis**: On closer inspection, `row_validate` is only set to `false`, never back to `true` in the loop. So this is NOT a false-positive masking bug. However, the missing `return false` still means **all subsequent rows are validated even after an empty tag is found** — wasting computation and potentially showing confusing error toasters for later rows even though the real issue is the empty tag.

**Revised severity**: P2 — UX issue, not data integrity.

---

### EST-R603 — `validateOldMatelDetailRow`: Dead Code in Rate Check (P1)

**File**: `ret_estimation.js` **Line**: 8738  
**Severity**: P1 — Validation Bypass  

```javascript
// L8738
if (($(this).find('.old_rate').val() <= 0 && $(this).find('.old_rate').val() == ''))
//                                       ^^ ❌ Logical AND — impossible condition
```

This condition requires `old_rate <= 0` (numeric comparison) **AND** `old_rate == ''` (string comparison). In JavaScript:
- `'' <= 0` is `true` (empty string coerces to 0)
- But `0 == ''` is `true` only for empty string
- `0.00 == ''` is `false`

So this only catches literally empty values, NOT `0` or negative rates. A user can enter rate = 0 and it will pass validation. This should be `||` (OR) not `&&` (AND):

```javascript
// Should be:
if ($(this).find('.old_rate').val() <= 0 || $(this).find('.old_rate').val() == '')
```

**Impact**: Old metal items with rate = 0 pass validation and are saved. The customer gets credited for old metal exchange at zero rate, causing financial loss.

---

### EST-R604 — `validateCatalogDetailRow` Mutates Form Data During Validation (P1)

**File**: `ret_estimation.js` **Lines**: 8492–8504  
**Severity**: P1 — Side Effect  

```javascript
// L8492-8504 — Inside validateCatalogDetailRow()
if ((curRow.find('.nn_cat_mc').val() == '' || ...)) {
    if (curRow.find('.cat_mc').val() != '' && ...) {
        curRow.find('.nn_cat_mc').val(curRow.find('.cat_mc').val());   // ❌ MODIFYING form data
    }
}

if ((curRow.find('.act_va_per').val() == '' || ...)) {
    if (curRow.find('.cus_wastage').val() != '' && ...) {
        curRow.find('.act_va_per').val(curRow.find('.cat_wastage').val());   // ❌ MODIFYING form data
    }
}
```

A validation function should only **check** data, not **change** it. These mutations:
1. Silently backfill `nn_cat_mc` and `act_va_per` hidden fields
2. Run on EVERY call to validate — repeated invocations compound the issue
3. Make the form's state unpredictable (user enters values, validation silently changes other values)
4. Mask bugs — if `nn_cat_mc` was empty due to a bug, the validation function hides it

**Same pattern exists in `validateCustomDetailRow`** at L8664-8676.

---

### EST-R605 — Controller: Mixed `$_POST` vs `$this->input->post()` (P2)

**File**: `admin_ret_estimation.php` **Lines**: Throughout  
**Severity**: P2 — Security Inconsistency  

The controller uses **two different methods** to access POST data:

| Method | Used In | XSS Filtering |
|---|---|---|
| `$_POST['field']` | `createNewCustomer`, `updateCustomer`, `getTaggingBySearch`, `getProductBySearch`, `getCustomProductBySearch`, `getProductDesignBySearch`, `get_metal_purity_rate`, `pan_available`, `gst_available`, `aadhar_available`, `passport_available`, `dl_available` | **None** |
| `$this->input->post('field')` | `get_scheme_accounts`, `get_stone_details`, `cancel_order_tag`, `get_village`, `get_tag_img_by_id` | CI XSS filter (if enabled) |

12 endpoints use raw `$_POST` while 5 use CI's `input->post()`. The raw `$_POST` access bypasses CodeIgniter's global XSS filtering, making those endpoints vulnerable to stored XSS if the data is displayed elsewhere.

---

### EST-R606 — Controller: 11 Functions Missing Access Modifier (P2)

**File**: `admin_ret_estimation.php`  
**Severity**: P2 — Access Control  

The following functions use `function` instead of `public function`:

```
get_old_metal_type()        L3198
get_old_metal_types()       L3209
get_old_metal_category()    L3220
get_metal_purity_rate()     L3231
get_purity_rate()           L3276
cancel_order_tag()          L3321
get_tag_status_details()    L3385
get_village_by_pincode()    L3423
old_get_village()           L3430
get_village()               L3454
get_sectionBranchwise()     L3494
getNonTagproducts()         L2772
```

In CI2, `function` without `public` defaults to public, so these ARE accessible. However:
1. It breaks the convention used by all other methods in the class
2. Some CI2 configurations may handle visibility differently
3. Static analysis tools flag these as potential security issues

---

### EST-R607 — Controller: `mkdir` with 0777 Permissions (P2)

**File**: `admin_ret_estimation.php` **Lines**: 2524, 3165  
**Severity**: P2 — Security  

```php
// L2524 and L3165
mkdir($folder, 0777, TRUE);
```

Creates customer image directories with world-readable/writable/executable permissions. On shared hosting, any user on the server can read, modify, or place files in these directories. Should be `0755` (owner: rwx, group/others: r-x).

---

### EST-R608 — Controller: `base64ToFile` No Content Validation (P2)

**File**: `admin_ret_estimation.php` **Lines**: 3287–3313  
**Severity**: P2 — Security  

```php
// L3290-3294
$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgBase64));
$temp_file_path = tempnam(sys_get_temp_dir(), 'tempimg');
file_put_contents($temp_file_path, $data);
```

The function:
1. Accepts any base64 string — no validation that it's actually an image
2. Writes it to disk without checking file type
3. Uses `getimagesize()` AFTER writing — if it's not an image, the file is already on disk
4. Temp files are not cleaned up if `getimagesize()` fails

A malicious user could submit a base64-encoded PHP file. While the file ends up in temp dir (not web-accessible), the temp cleanup issue means it persists.

---

### EST-R609 — Controller: `get_village` Does Raw DB Query in Controller (P3)

**File**: `admin_ret_estimation.php` **Lines**: 3454–3492  
**Severity**: P3 — Architecture  

```php
// L3462-3466 — Direct DB access in controller
$this->db->select('*');
$this->db->from('village');
$this->db->where('LOWER(village_name)', $village_name_LC);
$this->db->where('pincode', $pincode);
$query = $this->db->get();
```

This violates the MVC pattern — database queries should be in the model. The controller's `old_get_village()` at L3430 correctly delegates to the model, but the newer `get_village()` does not.

---

### EST-R610 — Controller: Commented-Out Debug Statements (P3)

**File**: `admin_ret_estimation.php` **Lines**: 2590, 2594, 3506, 3559, 3561  
**Severity**: P3 — Code Quality  

```php
// L2590  //print_r($_POST);exit;
// L2594  // print_r($data);exit;
// L3506  // echo "<pre>";print_r($_POST);exit;
// L3559  // print_r($_POST['id_metal']);exit;
// L3561  // print_r($data);exit;
```

5 commented-out `print_r/exit` statements left in production code. These are debug artifacts that should be removed, and if accidentally uncommented will dump raw POST data to the browser.

---

## Summary Table

| ID | Sev | Category | Description |
|---|---|---|---|
| EST-R601 | **P0** | Data Integrity | `cancel_order_tag` commits on failure instead of rollback |
| EST-R602 | P2 | Validation | `validateTagDetailRow` empty tag — no `return false`, continues loop |
| EST-R603 | **P1** | Validation Bypass | Old metal rate check uses `&& ''` (dead code) — rate=0 passes |
| EST-R604 | **P1** | Side Effect | Validation functions mutate form data (copy mc/va values) |
| EST-R605 | P2 | Security | Mixed `$_POST` vs `$this->input->post()` — 12 endpoints bypass XSS filter |
| EST-R606 | P2 | Access Control | 11 controller functions missing `public` access modifier |
| EST-R607 | P2 | Security | `mkdir` with `0777` permissions for customer image dirs |
| EST-R608 | P2 | Security | `base64ToFile` no content validation — potential file upload attack vector |
| EST-R609 | P3 | Architecture | Controller does raw DB query instead of delegating to model |
| EST-R610 | P3 | Code Quality | 5 commented-out debug `print_r/exit` in production |

---

## Sprint Recommendation

### Sprint 1 (Critical — Immediate)
- **EST-R601**: Change `trans_commit()` → `trans_rollback()` at L3362
- **EST-R603**: Change `&&` → `||` at L8738 in old metal rate check

### Sprint 2 (Important)
- **EST-R604**: Extract mutation logic from validation functions into separate pre-save function
- **EST-R605**: Standardize all endpoints to use `$this->input->post()`
- **EST-R607**: Change `0777` → `0755` at L2524/3165
- **EST-R608**: Add MIME type validation before writing temp file

### Sprint 3 (Cleanup)
- **EST-R602**: Add `return false` after empty tag check
- **EST-R606**: Add `public` to all 11 functions
- **EST-R609**: Move DB query to model
- **EST-R610**: Remove debug comments
