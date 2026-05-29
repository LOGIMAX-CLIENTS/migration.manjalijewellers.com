# 🔬 Round 4 — JS & View Layer Deep Dive

> **Audit Date**: 2026-02-17  
> **Method**: Full JS save handler + view form analysis  
> **Scope**: `ret_estimation.js` (31,391 lines), `estimation/form.php` (2,526 lines)  
> **Files Analysed**:  
> - `admin/assets/js/ret_estimation.js`  
> - `admin/application/views/estimation/form.php`  

---

## Executive Summary

**11 new bugs identified**: **2 P0 (critical)**, **4 P1 (major)**, **3 P2 (minor)**, **2 P3 (cosmetic)**.

> [!CAUTION]
> Two critical logic bugs allow saving estimations with invalid data — one in the EDA flow and one in the main save flow.

---

## Bug Details

---

### EST-R401 — EDA Validation Uses Wrong Function for Home Bill Section (P0)

**File**: `ret_estimation.js` **Line**: 5349  
**Severity**: P0 — Data Integrity  

The EDA save handler (`#est_eda_print` click) validates the Home Bill (custom) section using `validateCatalogDetailRow()` instead of `validateCustomDetailRow()`.

```javascript
// Line 5345-5349 (EDA handler — Home Bill section)
if ($('#select_custom_details').is(":checked")) {
    if ($('#estimation_custom_details').length >= 0) {
        if (validateCatalogDetailRow()) {    // ❌ WRONG: should be validateCustomDetailRow()
            form_validate = true;
```

**Compare with the correct code in the main `#est_print` handler** (L5113):
```javascript
if (validateCustomDetailRow()) {    // ✅ CORRECT
    form_validate_custom = true;
```

**Impact**: Home Bill items are validated using catalog rules, not custom rules. Custom-specific validations are completely skipped in EDA flow.

---

### EST-R402 — EDA Validation Always Passes Due to jQuery Length Check (P0)

**File**: `ret_estimation.js` **Lines**: 5287, 5321, 5347, 5379  
**Severity**: P0 — Data Integrity  

The EDA handler checks `$('#estimation_tag_details').length >= 0` to see if the table has rows. But **jQuery's `.length` always returns ≥ 0** (the length of the jQuery object, which is 1 because the table element exists). This check always passes, bypassing the "no rows" validation.

```javascript
// Line 5287 — EDA tag validation
if ($('#estimation_tag_details').length >= 0) {    // ❌ ALWAYS true (jQuery returns 1)
    if (validateTagDetailRow()) {
        form_validate = true;
```

**Compare with the CORRECT check in the main `#est_print` handler** (L5043):
```javascript
if ($('#estimation_tag_details tbody tr').length > 0) {    // ✅ Checks actual rows
```

**Impact**: All 4 EDA section checks (tag, catalog, custom, old-metal) always pass the "has rows" test. An empty table with no items will still proceed to `validateTagDetailRow()`, which may throw a JS error or return `true` on empty data.

---

### EST-R403 — EDA Uses Single `form_validate` Flag — Last Section Wins (P1)

**File**: `ret_estimation.js` **Lines**: 5253–5487  
**Severity**: P1 — Data Integrity  

The EDA handler uses a single `form_validate` variable for ALL sections. Each section overwrites the previous result:

```javascript
// Tag section
if (validateTagDetailRow()) {
    form_validate = true;    // Tag passes → true
} else {
    form_validate = false;   // Tag fails → false
}

// Catalog section (runs after tag)
if (validateCatalogDetailRow()) {
    form_validate = true;    // Catalog passes → overwrites tag failure!
} else {
    form_validate = false;
}
```

**Compare with the main `#est_print` handler** which correctly uses 4 separate flags:
```javascript
// Lines 5041-5173: uses form_validate_tag, form_validate_nontag,
//                   form_validate_custom, form_validate_old
// Line 5175: AND-checks ALL flags
if (form_validate_tag && form_validate_nontag && form_validate_custom && form_validate_old) {
```

**Impact**: If tag validation fails but catalog passes, the form submits anyway. Only the LAST checkbox section's validation result matters.

---

### EST-R404 — Address Alerts Don't Block Submission (P1)

**File**: `ret_estimation.js` **Lines**: 4967–5003  
**Severity**: P1 — Data Integrity  

The main save handler shows alerts for missing address/pincode/country/state but doesn't set any blocking flags. The alerts fire independently, and the conditional flow falls through to the `est_checked` logic without returning:

```javascript
if (ask_cus_data && esti_for == 1){
    if(!ask_cus_addr1){
        alert('Customer Address Not Available..!');
        // ❌ No return, no flag set — execution continues
    }
    if(!ask_cus_pincode){
        alert('Customer Pincode Not Available..!');
        // ❌ Same — no blocking
    }
}
```

Later at line 5175, `ask_cus_addr1` IS checked in the final condition:
```javascript
if (ask_cus_data && … && ask_cus_addr1 && ask_cus_pincode && ask_cus_country && ask_cus_state) {
```

However, the user sees up to 4 consecutive alerts even though submission won't proceed. This is a UX issue — alerts should be consolidated, and the code should return early.

**Adjusted severity**: P2 (UX problem, not data loss, since the final AND-check does block)

---

### EST-R405 — `deleteEstimation` Uses GET Request for Destructive Action (P1)

**File**: `ret_estimation.js` **Line**: 5491–5500  
**Severity**: P1 — Security  

```javascript
function deleteEstimation(id) {
    $.ajax({
        url: base_url + "index.php/admin_ret_estimation/estimation/delete/" + id + "?nocache=…",
        // ❌ No 'type' specified — $.ajax defaults to GET
```

**Impact**: 
- GET requests can be triggered by link prefetching, browser pre-renders, or CSRF via `<img src="…/delete/123">`
- Destructive operations MUST use POST with CSRF token
- The controller's delete handler doesn't verify HTTP method

---

### EST-R406 — AJAX Cache-Buster Uses `getUTCSeconds()` (Only 0–59) (P2)

**File**: `ret_estimation.js` **Lines**: 5189, 5195, 5423, 5429  
**Severity**: P2 — Functional  

```javascript
var url = base_url + "index.php/admin_ret_estimation/estimation/save?nocache=" + my_Date.getUTCSeconds();
```

`getUTCSeconds()` returns 0–59. If a user submits twice within the same second of different minutes, they get the same cache-buster value. Should use `Date.now()` (millisecond timestamp).

**Impact**: Low — browser typically doesn't cache POST requests, but this is still incorrect for GET-like requests and indicates cargo-cult coding.

---

### EST-R407 — View: Duplicate `id="btn-submit"` (P2)

**File**: `form.php` **Lines**: 1334, 1337  
**Severity**: P2 — HTML Violation  

```html
<span id="btn-submit"><button … id="est_print">Save and Print</button></span>
…
<span id="btn-submit"><button … id="est_eda_print">Print for EDA</button></span>
```

HTML spec requires IDs to be unique. `document.getElementById('btn-submit')` will always return the FIRST element. Any JS targeting `#btn-submit` will only affect "Save and Print", not "Print for EDA".

---

### EST-R408 — View: Double Form Close (P2)

**File**: `form.php` **Lines**: 1343–1344  
**Severity**: P2 — HTML Violation  

```html
</form>
<?php echo form_close(); ?>
```

`form_close()` outputs another `</form>`. The form is closed twice, producing malformed HTML. The second `</form>` is a stray tag that browsers ignore, but it indicates code quality issues.

---

### EST-R409 — View: XSS in Unescaped Flashdata Output (P1)

**File**: `form.php` **Lines**: 83–89  
**Severity**: P1 — Security  

```php
$message = $this->session->flashdata('chit_alert');
?>
<div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
    …
    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
    <?php echo $message['message']; ?>
</div>
```

If `$message['class']`, `$message['title']`, or `$message['message']` contain user-controlled data, they're rendered without `htmlspecialchars()`. The `$message['class']` value is injected directly into a CSS class attribute — a potential XSS vector if the flashdata is set from user input.

---

### EST-R410 — Security-Sensitive Settings Exposed as Hidden Inputs (P3)

**File**: `form.php` **Lines**: 127–155  
**Severity**: P3 — Security Design  

30+ hidden inputs expose server-side configuration to the client:

```html
<input type="hidden" id="min_old_gold_rate" value="<?php echo $min_old_gold_rate['value'] ?>" />
<input type="hidden" id="max_old_gold_rate" value="<?php echo $max_old_gold_rate['value'] ?>" />
<input type="hidden" id="allow_manual_rate" value="<?php echo $emp_setting['allow_manual_rate'] ?>" />
<input type="hidden" id="disc_limit_type">
<input type="hidden" id="disc_limit">
<input type="hidden" id="allow_mc_edit" value="<?php echo $profile_setting['allow_mc_edit'] ?>" />
<input type="hidden" id="blk_wast_disc_lmt" value="<?php echo $emp_setting['bulk_wast_disc_limit'] ?>" />
```

These values are used for client-side validation only. A user with DevTools can change `allow_mc_edit` from 0 to 1, or set `disc_limit` to 99999. The server DOES NOT re-validate these limits (verified in Round 1/3 controller analysis).

**Impact**: Client-side-only permission enforcement. An employee can bypass their discount/MC-edit/wastage limits by editing hidden fields.

---

### EST-R411 — View: `name` Attribute Duplicated on Hidden Input (P3)

**File**: `form.php` **Line**: 1385  
**Severity**: P3 — HTML  

```html
<input type="hidden" name="cus[id_village]" id="id_village" name="" value="">
```

The `name` attribute appears twice — once as `cus[id_village]` and again as `""`. HTML parsers typically use the first `name` value, but this is technically invalid and can cause unpredictable behaviour across browsers.

---

## Summary Table

| ID | Sev | Category | Description |
|---|---|---|---|
| EST-R401 | **P0** | Logic | EDA validates Home Bill with `validateCatalogDetailRow()` instead of `validateCustomDetailRow()` |
| EST-R402 | **P0** | Logic | EDA uses `$('#table').length >= 0` — always true, bypasses empty-table check |
| EST-R403 | **P1** | Logic | EDA single `form_validate` flag — last section overwrites previous failures |
| EST-R404 | **P2** | UX | Multiple consecutive alerts for missing address fields (doesn't block, but poor UX) |
| EST-R405 | **P1** | Security | `deleteEstimation()` uses GET for destructive action — CSRF vulnerable |
| EST-R406 | **P2** | Functional | Cache-buster `getUTCSeconds()` only 0-59, not unique |
| EST-R407 | **P2** | HTML | Duplicate `id="btn-submit"` on two different spans |
| EST-R408 | **P2** | HTML | Double `</form>` close (HTML `</form>` + CI `form_close()`) |
| EST-R409 | **P1** | Security | XSS: Unescaped flashdata rendered in alert div |
| EST-R410 | **P3** | Security | 30+ hidden inputs expose server settings — client-side-only validation |
| EST-R411 | **P3** | HTML | Duplicate `name` attribute on hidden input |

---

## Sprint Recommendation

### Sprint 1 (Critical — Immediate)
- **EST-R401**: Change `validateCatalogDetailRow()` → `validateCustomDetailRow()` at line 5349
- **EST-R402**: Change `.length >= 0` → `tbody tr').length > 0` at lines 5287, 5321, 5347, 5379
- **EST-R403**: Replace single `form_validate` with 4 separate flags (copy pattern from `#est_print`)

### Sprint 2 (Security)
- **EST-R405**: Change `deleteEstimation` to use `type: "POST"` with CSRF token
- **EST-R409**: Wrap flashdata output in `htmlspecialchars()`
- **EST-R410**: Add server-side re-validation of permission limits in save/update handler

### Sprint 3 (Cleanup)
- **EST-R404**: Consolidate into single alert with all issues listed
- **EST-R406**: Replace `getUTCSeconds()` with `Date.now()`
- **EST-R407/408/411**: Fix HTML violations
