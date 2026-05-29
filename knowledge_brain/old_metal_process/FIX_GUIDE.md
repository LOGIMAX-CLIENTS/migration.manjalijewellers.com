# Fix Guide — Old Metal Process Module

**Module**: `old_metal_process`
**Brain Round**: 15 (FINAL)
**Date**: 2026-03-19
**Total Bugs**: 63 (net of 1 retracted — OMP-014)
**Critical**: 14 | High: 13 | Medium: 16 | Low: 19 | Retracted: 1

> **Fix Order Rule**: Always run Sprint 0 (DB safety checks) before touching any code.
> Apply fixes in sprint order. Verify after each sprint using the regression tests in the relevant section.

---

## Sprint 0 — Pre-Fix DB Safety Audit (Run First, No Code Changes)

Run these queries to assess existing data damage before any fix is applied:

```sql
-- 1. Check payments with wrong NB amount (OMP-003)
SELECT id, payment_mode, payment_amount
FROM ret_old_metal_process_payment WHERE payment_mode = 'NB';

-- 2. Check polishing lot inwards with NULL branch (OMP-028)
SELECT COUNT(*) AS null_branch_count
FROM ret_lot_inwards WHERE created_branch IS NULL AND lot_from = 5;

-- 3. Check duplicate process numbers (OMP-005)
SELECT process_no, COUNT(*) AS cnt
FROM ret_old_metal_process
GROUP BY process_no HAVING cnt > 1;

-- 4. Check refining receipts with piece=1 that may be wrong (OMP-029)
SELECT id_old_metal_refining, piece
FROM ret_old_metal_refining_details WHERE piece = 1;

-- 5. Check stone stock rows that may have wrong id_branch (OMP-053)
-- Compare this with actual polishing receipt saves to identify bad rows
SELECT * FROM ret_purchase_item_stock_summary ORDER BY id DESC LIMIT 50;

-- 6. Check unsynced OMP records before touching financial data
SELECT COUNT(*) FROM ret_old_metal_process WHERE outistransfered = 0;
```

Document the output before proceeding. Coordinate any data corrections with a DB administrator.

---

## Sprint 1 — P0 Fixes: Production Blockers (Est. 45 min total)

These bugs directly break visible functionality or corrupt financial data on every transaction.

---

### ✏️ OMP-019: Payment Tab Commented Out in form.php

**File**: `admin/application/views/ret_metal_process/metal_process/form.php`
**Lines**: ~347 (tab `<li>`) and ~590–630 (tab pane)
**Effort**: 5 min

**Fix**: Remove the `<!-- ... -->` HTML comment wrapper around the payment tab `<li>` and the tab pane block. The controller save code at lines 1474–1500 already works.

```html
<!-- BEFORE: entire block wrapped like this -->
<!--
<li><a href="#payment_details">Payment</a></li>
<div class="tab-pane" id="payment_details">
  ...cash and net banking fields...
</div>
-->

<!-- AFTER: uncomment both the tab header and the tab pane -->
<li><a href="#payment_details">Payment</a></li>
<div class="tab-pane" id="payment_details">
  ...cash and net banking fields...
</div>
```

**Verify**: Submit a receipt with cash payment → check `ret_old_metal_process_payment` row exists.

---

### ✏️ OMP-028: Polishing Lot Inwards `created_branch` = NULL

**File**: `admin/application/controllers/admin_ret_metal_process.php`
**Line**: ~1315
**Effort**: 1 min

```php
// BEFORE:
'created_branch' => $id_branch,   // ❌ undefined variable

// AFTER:
'created_branch' => $branchDetails['id_branch'],  // ✅ in scope at this point
```

**Verify**: Save polishing receipt → `ret_lot_inwards.created_branch` must not be NULL.

---

### ✏️ OMP-003: Net Banking Payment Records Cash Amount

**File**: `admin/application/controllers/admin_ret_metal_process.php`
**Line**: ~1494
**Effort**: 1 min

```php
// BEFORE:
'payment_amount' => $receipt_payment['cash_amount'],  // ❌ wrong key

// AFTER:
'payment_amount' => $receipt_payment['net_banking_amount'],  // ✅
```

**Verify**: Submit NB payment of ₹5000 → `ret_old_metal_process_payment.payment_amount = 5000`.

---

### ✏️ OMP-002: Wrong Field for GST Tax Type (id_company vs id_state)

**File**: `admin/application/controllers/admin_ret_metal_process.php`
**Function**: `get_chg_tax_type()`
**Line**: ~1773
**Effort**: 5 min

```php
// BEFORE:
$company_state = $data['comp_details'][0]['id_company'];  // ❌ id_company, not id_state

// AFTER:
// getCompanyDetails("") returns row_array (not result_array), so no [0] index:
$company_state = $data['comp_details']['id_state'];  // ✅
```

**Verify**: Same-state karigar → response has `tax_type=1` (CGST+SGST). Different state → `tax_type=0` (IGST).

---

### ✏️ OMP-039: Duplicate `moneyFormatIndia()` in Acknowledgement View

**File**: `admin/application/views/ret_metal_process/metal_process/process_acknowladgement.php`
**Lines**: 100 and 659
**Effort**: 2 min

**Fix**: Remove the **second** declaration of `moneyFormatIndia()` at line ~659. Retain only the one at line 100.

**Verify**: Open any process acknowledgement with payment data → no PHP fatal error.

---

### ✏️ OMP-038: Non-Tagged Pocket Row Appended Twice

**File**: `admin/assets/js/ret_metal_process.js`
**Lines**: 1610–1614
**Effort**: 2 min

```javascript
// BEFORE (lines 1610–1614):
$('#non_tagged_pocket_details tbody').append(trHtml);  // line 1610
var ab = $('#non_tagged_pocket_details tbody').append(trHtml);  // line 1612 — DUPLICATE
console.log(ab);  // line 1614

// AFTER (keep only):
$('#non_tagged_pocket_details tbody').append(trHtml);
```

**Verify**: Add NT pocket items → each item appears exactly once in the table.

---

## Sprint 2 — P1 Fixes: Data Integrity (Est. 1.5 hrs)

---

### ✏️ OMP-055: `check_purity_stock_details()` Double-Query (PHP Fatal)

**File**: `admin/application/models/ret_metal_process_model.php`
**Lines**: 1113–1120
**Effort**: 5 min

```php
// BEFORE:
$sql = $this->db->query("SELECT * FROM ...");  // $sql is CI result object
$res = $this->db->query($sql);                  // ❌ passes object as query string
if ($res->num_rows() > 0) {

// AFTER:
$sql = $this->db->query("SELECT * FROM ...");
// remove the $res line entirely:
if ($sql->num_rows() > 0) {
```

**Verify**: Save polishing receipt → stock summary row is updated (not always inserting new row).

---

### ✏️ OMP-004: Same Fix — `check_purity_stock()` Double-Query (OMP-004 / OMP-055 are same function)

Same fix location as OMP-055 at model lines ~1113–1120. A single code change fixes both IDs.

---

### ✏️ OMP-053: `updateStoneItemData()` Wrong WHERE Field

**File**: `admin/application/models/ret_metal_process_model.php`
**Line**: ~1311
**Effort**: 2 min

```php
// BEFORE:
"...WHERE id_ret_category=".$data['id_ret_category']
." and id_branch=".$data['id_product']     // ❌ copies id_product into id_branch
." and id_product=".$data['id_product']

// AFTER:
"...WHERE id_ret_category=".$data['id_ret_category']
." and id_branch=".$data['id_branch']      // ✅ correct field
." and id_product=".$data['id_product']
```

**Verify**: Save old-metal polishing receipt with stone category → stone stock summary updates the correct row.

---

### ✏️ OMP-029: Refining Receipt Piece Count Hardcoded to 1

**File**: `admin/application/controllers/admin_ret_metal_process.php`
**Line**: ~1024
**Effort**: 1 min

```php
// BEFORE:
'piece' => 1,    // ❌ hardcoded

// AFTER:
'piece' => $cat['recd_pcs'],  // ✅ actual received pieces from JS payload
```

**Verify**: Save refining receipt with 3 pieces → `ret_old_metal_refining_details.piece = 3`.

---

### ✏️ OMP-054: `get_refining_process_details()` Missing WHERE Clause

**File**: `admin/application/models/ret_metal_process_model.php`
**Lines**: ~1538–1544
**Effort**: 3 min

```php
// BEFORE: query has no WHERE clause
$sql = $this->db->query("SELECT ... FROM ret_old_metal_process p
    LEFT JOIN ret_old_metal_refining r ON r.id_old_metal_process=p.id_old_metal_process
    ...");   // ❌ returns ALL records

// AFTER: add WHERE
$sql = $this->db->query("SELECT ... FROM ret_old_metal_process p
    LEFT JOIN ret_old_metal_refining r ON r.id_old_metal_process=p.id_old_metal_process
    ...
    WHERE p.id_old_metal_process = ".$this->db->escape($id_old_metal_process));
```

**Verify**: Request refining process details for ID 5 → only process 5 data returned.

---

### ✏️ OMP-056: `get_karigar_state()` Reads `$_POST` Directly in Model

**File**: `admin/application/models/ret_metal_process_model.php`
**Line**: ~1844
**Effort**: 10 min

```php
// BEFORE:
function get_karigar_state(){
    $sql = $this->db->query(
        "Select id_state from ret_karigar where id_karigar =".$_POST['karigar']
    );

// AFTER — add $id_karigar parameter, use parameterised query:
function get_karigar_state($id_karigar){
    $sql = $this->db->query(
        "SELECT id_state FROM ret_karigar WHERE id_karigar = ?",
        [(int)$id_karigar]
    );
```

**Controller call update** (wherever `get_karigar_state()` is called, pass the karigar ID):
```php
$karigar_id = $this->input->post('karigar', TRUE);
$state = $this->$model->get_karigar_state($karigar_id);
```

**Verify**: Call API with tampered `karigar` POST → no SQL error; query executes with integer-cast ID.

---

## Sprint 3 — P1 Security: SQL Injection Remediation (Est. 4–8 hrs)

---

### ✏️ OMP-001 / OMP-051 / OMP-057 / OMP-013: Parameterise All SQL Queries in Model

**File**: `admin/application/models/ret_metal_process_model.php` (entire file)
**Effort**: 4–8 hrs (phased)

**Pattern to apply throughout**:
```php
// BEFORE (raw concatenation):
$sql = $this->db->query("SELECT * FROM ret_old_metal_pocket WHERE id_metal_pocket = ".$id);

// AFTER (Option A — CI Active Record):
$this->db->where('id_metal_pocket', $id);
$sql = $this->db->get('ret_old_metal_pocket');

// AFTER (Option B — CI query bindings):
$sql = $this->db->query(
    "SELECT * FROM ret_old_metal_pocket WHERE id_metal_pocket = ?",
    [$id]
);
```

**Priority order** (user-input functions first):
1. `get_karigar_state()` — line 1844 — `$_POST['karigar']` (already fixed in OMP-056)
2. `check_purity_stock_details()` — lines 1113–1118 — 4 params from controller POST
3. `get_testing_receipt_details()` — line 1109 — `$data['id_karigar']` from POST
4. `get_RefiningReceiptDetails()` — line 1171 — `$data['id_karigar']` from POST
5. `get_PolishingReceiptDetails()` — line 1212 — `$data['id_karigar']` from POST
6. All remaining functions (30+ points documented in OMP-051/057)

---

### ✏️ OMP-052: `$arith` Operator Injection in Six UPDATE Functions

**File**: `admin/application/models/ret_metal_process_model.php`
**Lines**: 1134, 1261, 1274, 1606, 1791, 1802
**Effort**: 15 min (one change per function)

Add whitelist validation in the controller **before** calling any of these model functions:
```php
// In controller, before calling any *arith* model function:
$arith = ($arith === '-') ? '-' : '+';   // whitelist: only '+' or '-'
```

Or in each model function:
```php
function updateStockItemData($data, $arith){
    $arith = in_array($arith, ['+', '-'], true) ? $arith : '+'; // ✅ whitelist
    ...
}
```

**Apply to**: `updateStockItemData()`, `updatePurItemData()`, `updatePocketItem()`, `updateNTData()`, `update_test_metalItem()`, `update_refining()`.

---

### ✏️ OMP-047: Enable CSRF Protection on All POST Handlers

**File**: `admin/application/config/config.php` + all save/delete POST handlers
**Effort**: 30–60 min

```php
// In config.php:
$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'csrf_token_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;

// In all AJAX forms (JS), include csrf token:
var csrfData = {};
csrfData[$('meta[name="csrf-token-name"]').attr('content')] = $('meta[name="csrf-token"]').attr('content');
// merge into all $.ajax data objects
```

> ⚠️ **Warning**: Enabling CSRF globally may break existing AJAX calls that don't include the token. Test all AJAX endpoints after enabling.

---

## Sprint 4 — Process Lifecycle Fixes (Est. 2–3 hrs)

---

### ✏️ OMP-005: Add UNIQUE Constraint on `process_no`

```sql
-- Run this ONLY after verifying no duplicates exist (Sprint 0):
ALTER TABLE ret_old_metal_process
    ADD UNIQUE KEY uq_process_no (process_no);
```

For the PHP race condition, replace the read-then-increment pattern with an atomic DB operation:
```php
// In generate_process_number() — use SELECT ... FOR UPDATE:
$this->db->trans_start();
$last = $this->db->query(
    "SELECT process_no FROM ret_old_metal_process WHERE ... ORDER BY id DESC LIMIT 1 FOR UPDATE"
)->row_array();
$new_no = $last['process_no'] + 1;
// insert with new_no
$this->db->trans_complete();
```

---

### ✏️ OMP-006: Add Server-Side Weight Validation

**File**: `admin/application/controllers/admin_ret_metal_process.php`
**Location**: `metal_process('save')` — before melting issue insert
**Effort**: 30 min

```php
// Add before issuing:
$balance = $this->$model->get_pocket_balance($post_data['id_metal_pocket']);
if ($post_data['issue_gwt'] > $balance['blc_gwt']) {
    echo json_encode(['status' => FALSE, 'message' => 'Issue weight exceeds pocket balance']);
    return;
}
```

---

### ✏️ OMP-007: Mark Pocket as Closed After Full Issue

**File**: Controller save logic (melting issue + polishing issue save branches)
**Effort**: 20 min

```php
// After all issue rows are inserted, check if pocket is exhausted:
$updated_pocket = $this->$model->get_pocket_balance($id_metal_pocket);
if ($updated_pocket['blc_pcs'] == 0 && $updated_pocket['blc_gwt'] == 0) {
    $this->$model->close_pocket($id_metal_pocket);  // sets status=1
}
```

---

### ✏️ OMP-008: Protect `melting_status` State Transitions

**File**: Controller save branches for testing issue, refining issue, testing receipt
**Effort**: 30 min

```php
// Before saving Testing Issue:
$current_status = $this->$model->get_melting_recd_status($id_melting_recd);
if ($current_status !== 1) {
    echo json_encode(['status' => FALSE, 'message' => 'Melting receipt not complete']);
    return;
}
```

Repeat for each downstream step (Refining Issue requires melting_status=3, etc.).

---

### ✏️ OMP-009: Fix Transaction Response Logic

**File**: Controller `metal_process('save')` — all save branches
**Effort**: 15 min

```php
// BEFORE: $responseData only set if success
// AFTER: initialize at top of function:
$responseData = ['status' => FALSE, 'message' => 'Unknown error'];

// Then at bottom:
if ($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();
    $responseData = ['status' => TRUE, ...];
} else {
    $this->db->trans_rollback();
    $responseData = ['status' => FALSE, 'message' => 'Transaction failed'];
}
echo json_encode($responseData);
```

---

## Sprint 5 — JS Bug Fixes (Est. 2 hrs)

---

### ✏️ OMP-041 / OMP-020: Testing Receipt Charges Class Mismatch

**File**: `admin/assets/js/ret_metal_process.js`
**Lines**: ~2838

```javascript
// BEFORE:
if(... || $(this).find('.receipt_charges').val()=='' ){

// AFTER:
if(... || $(this).find('.testing_receipt_charges').val()=='' ){
```

---

### ✏️ OMP-021: Refining Receipt Charges Class Mismatch

**File**: `admin/assets/js/ret_metal_process.js`
**Line**: ~2851

```javascript
// BEFORE:
$(this).find('.receipt_charges').val()==''

// AFTER:
$(this).find('.refining_receipt_charges').val()==''
```

---

### ✏️ OMP-025: `validateTestingIssueRow()` Wrong Table Selector

**File**: `admin/assets/js/ret_metal_process.js`
**Lines**: ~2877

```javascript
// BEFORE:
$('#testing_receipt > tbody > tr').each(...)

// AFTER:
$('#testing_process_details > tbody > tr').each(...)
```

Also add missing return statement (OMP-044):
```javascript
// Add at end of function:
return row_validate;
```

---

### ✏️ OMP-037: Duplicate `#select_metal_process` Change Handler

**File**: `admin/assets/js/ret_metal_process.js`
**Lines**: 1155 and 1272

**Fix**: Remove the entire duplicate handler block at line 1272. Keep only the handler at line 1155.

---

### ✏️ OMP-043: `calculate_pocketing_details()` Wrong Purity Divisor

**File**: `admin/assets/js/ret_metal_process.js`
**Lines**: ~2050–2052

```javascript
// BEFORE:
avg_purity += parseFloat(tot_purity / $('#pocket_details > tbody tr').length);

// AFTER:
var checked_count = $('#pocket_details > tbody tr').filter(function(){
    return $(this).find('input[type=checkbox]').is(':checked');
}).length;
avg_purity += checked_count > 0
    ? parseFloat(tot_purity / checked_count)
    : 0;
```

---

### ✏️ OMP-060: `calculate_tag_list()` Wrong Purity Divisor (same flaw as OMP-043)

**File**: `admin/assets/js/ret_metal_process.js`
**Line**: ~4537

```javascript
// BEFORE:
$('.tag_total_avg_purity').val(
    parseFloat(purity_value / $('#tag_list > tbody > tr').length).toFixed(3)
);

// AFTER:
var checked_tag_count = $('#tag_list > tbody > tr').filter(function(){
    return $(this).find('.tag_id').is(':checked');
}).length;
$('.tag_total_avg_purity').val(
    checked_tag_count > 0
        ? parseFloat(purity_value / checked_tag_count).toFixed(3)
        : '0.000'
);
```

---

### ✏️ OMP-059: Duplicate `.refining_ret_category` Change Handler

**File**: `admin/assets/js/ret_metal_process.js`
**Lines**: 3335 and 3841

**Fix**: Remove the duplicate handler at line 3841, keep only line 3335.

---

### ✏️ OMP-035: Report Daterangepicker Calls Wrong Function

**File**: `admin/assets/js/ret_metal_process.js`
**Line**: ~203

```javascript
// BEFORE:
get_pocket_list();   // ❌ this is on the report page, not pocket page

// AFTER:
get_metal_process_report();  // ✅ or the correct report-fetch function name
```

---

### ✏️ OMP-022: `#category_row` DOM ID Collision Across 3 Modals

**File**: `admin/application/views/ret_metal_process/metal_process/form.php`
**Effort**: 30–60 min (includes JS selector update)

```html
<!-- BEFORE: all three modals use same ID -->
<table id="category_row">  <!-- melting modal ~line 682 -->
<table id="category_row">  <!-- refining modal ~line 724 -->
<table id="category_row">  <!-- polishing modal ~line 767 -->

<!-- AFTER: unique IDs -->
<table id="melting_category_row">
<table id="refining_category_row">
<table id="polishing_category_row">
```

**JS Update**: Audit all `$('#category_row')` calls in `ret_metal_process.js` and update selectors to use context-specific IDs.

---

### ✏️ OMP-061: `get_tag_search_list()` — 9 Unquoted HTML Attribute Values

**File**: `admin/assets/js/ret_metal_process.js`
**Lines**: ~4475–4484

```javascript
// BEFORE (unquoted example):
trHtml += '<input type="hidden" class="tag_id" value='+val.tag_id+'>';

// AFTER (quoted):
trHtml += '<input type="hidden" class="tag_id" value="'+val.tag_id+'">';
```

Apply to all 9 attributes in the tag row HTML builder.

---

### ✏️ OMP-027: Polishing Non-Tag Validator Checks Disabled Dropdowns

**File**: `admin/assets/js/ret_metal_process.js`
**Line**: ~3702

```javascript
// BEFORE: checks id_design and id_sub_design unconditionally
|| $(this).find('.id_design').val()==''
|| $(this).find('.id_sub_design').val()==null

// AFTER: only check when non-tag IS selected
var is_non_tag = $(this).find('.is_non_tag').is(':checked');
var row_invalid = /* required fields check */;
if (!is_non_tag) {
    row_invalid = row_invalid
        || $(this).find('.id_design').val()==''
        || $(this).find('.id_sub_design').val()==null;
}
```

---

### ✏️ OMP-063: `print_url` Implicit Global — Declare as Local Variable

**File**: `admin/assets/js/ret_metal_process.js`
**Lines**: ~4343, 4365, 4387

```javascript
// BEFORE (inside fnFormatRowProcessDetails):
print_url = base_url + '...';  // implicit global

// AFTER:
var print_url;
print_url = base_url + '...';
// or use const print_url = ... for the first assignment
```

---

### ✏️ OMP-062: Report AJAX Date — `.html()` → `.val()`

**File**: `admin/assets/js/ret_metal_process.js`
**Lines**: ~4128, 4244

```javascript
// BEFORE:
'from_date': $('#rpt_payments1').html(),

// AFTER:
'from_date': $('#rpt_payments1').val(),
```

---

### ✏️ OMP-026: Process Report Date `.html()` → `.val()`

**File**: `admin/assets/js/ret_metal_process.js`
**Line**: ~4128 (same as OMP-062 location — already covered above)

---

### ✏️ OMP-036 / OMP-064: Remove All 26 `console.log()` Calls

**File**: `admin/assets/js/ret_metal_process.js`

**Locations**:
- R8: Lines 736, 744, 788, 796, 843, 850, 859, 873
- R9: Lines 973, 1093, 1614, 1854, 1855
- R10: Lines 2003, 2088, 2089, 3103, 3107, 3114, 3124
- R14: Lines 4521, 4530, 4548, 4553, 4991

**Fix**: Delete all 26 lines. Or gate with a debug flag:
```javascript
if (window.DEBUG_OMP) console.log(...);
```

---

## Sprint 6 — View + DOM Fixes (Est. 1 hr)

---

### ✏️ OMP-032 / OMP-050: "Delete Estimation" in 5 View Files

**Files** (fix all 5):
1. `views/ret_metal_process/reports/process_report.php` line ~137
2. `views/ret_metal_process/reports/detailed_report.php` lines ~91, 94
3. `views/ret_metal_process/metal_process/list.php` line ~101
4. `views/ret_metal_process/pocket/list.php` line ~99
5. `views/ret_metal_process/list.php` line ~98

```html
<!-- BEFORE: -->
<h4 class="modal-title" id="myModalLabel">Delete Estimation</h4>

<!-- AFTER: -->
<h4 class="modal-title" id="myModalLabel">Delete Process</h4>
```

Also update modal body: change "this estimation" → "this process".

---

### ✏️ OMP-030: Duplicate `id="type2"` on Pocket Form Radios

**File**: `admin/application/views/ret_metal_process/pocket/form.php`
**Lines**: ~61–63

```html
<!-- BEFORE: -->
<input type="radio" name="transfer_item_type" id="type2" value="2" checked>
<input type="radio" name="transfer_item_type" id="type2" value="3">

<!-- AFTER: -->
<input type="radio" name="transfer_item_type" id="type_tagged" value="2" checked>
<input type="radio" name="transfer_item_type" id="type_nontag" value="3">
```

---

### ✏️ OMP-042: Duplicate `id="has_charge"` / `id="charge_type"` on Process Master Radios

**File**: `admin/application/views/ret_metal_process/process_master/form.php`
**Lines**: ~46–57

```html
<!-- BEFORE: -->
<input type="radio" id="has_charge" value="1"> Yes
<input type="radio" id="has_charge" value="0"> No

<!-- AFTER: -->
<input type="radio" id="has_charge_yes" value="1"> Yes
<input type="radio" id="has_charge_no" value="0"> No
```

Same fix for `id="charge_type"` → `id="charge_type_gram"` / `id="charge_type_flat"`.

---

### ✏️ OMP-048: Root List View Uses Wrong Table ID

**File**: `admin/application/views/ret_metal_process/list.php`
**Line**: ~66

```html
<!-- BEFORE: -->
<table id="pocket_list" ...>

<!-- AFTER: -->
<table id="old_metal_process_list" ...>
```

Also update the corresponding JS DataTable initialization to target `#old_metal_process_list`.

---

### ✏️ OMP-049: Root List View Add Button Missing ACL Guard

**File**: `admin/application/views/ret_metal_process/list.php`
**Line**: ~25

```php
<!-- BEFORE: -->
<a class="btn btn-success" href="...metal_process_issue/add">Add</a>

<!-- AFTER: -->
<?php if ($access['add'] == 1) { ?>
<a class="btn btn-success" href="...metal_process_issue/add">Add</a>
<?php } ?>
```

---

### ✏️ OMP-031: `metal_process_receipt` ACL Check Uses Wrong Path

**File**: `admin/application/controllers/admin_ret_metal_process.php`
**Line**: ~1604

```php
// BEFORE:
$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_purchase/list');

// AFTER:
$access = $this->admin_settings_model->get_access('admin_ret_metal_process/metal_process_receipt/list');
```

---

## Sprint 7 — Low / Polish Items (Est. 30 min)

---

### ✏️ OMP-018: DomPDF Orientation Typo

**File**: `admin/application/controllers/admin_ret_metal_process.php`
**Line**: ~1578

```php
// BEFORE:
$dompdf->set_paper("a4", "portriat");

// AFTER:
$dompdf->set_paper("a4", "portrait");
```

---

### ✏️ OMP-033: `get_Active_Refining_details()` Ignores `$_POST` Argument

**File**: `admin/application/models/ret_metal_process_model.php`
**Line**: ~133

```php
// BEFORE:
function get_Active_Refining_details()   // no params

// AFTER:
function get_Active_Refining_details($data = [])
{
    // apply optional filters if provided:
    if (!empty($data['id_branch'])) {
        $this->db->where('id_branch', $data['id_branch']);
    }
    return $this->db->get('ret_old_metal_refining')->result_array();
}
```

---

### ✏️ OMP-034: `get_pocket_list()` Missing Branch Filter

**File**: `admin/application/models/ret_metal_process_model.php`
**Lines**: ~741–745

```php
// AFTER fix: add branch filter
$id_branch = $this->session->userdata('id_branch');
$sql = $this->db->query("SELECT * FROM `ret_old_metal_pocket`
    WHERE (date(date) BETWEEN '".$from."' AND '".$to."')
    AND id_branch = ".(int)$id_branch);
```

---

### ✏️ OMP-040: Stray `-` Dash in Acknowledgement View Line 88

**File**: `admin/application/views/ret_metal_process/metal_process/process_acknowladgement.php`
**Line**: 88

```html
<!-- BEFORE: -->
</label>-

<!-- AFTER: -->
</label>
```

---

### ✏️ OMP-045: Duplicate `bDestroy: true` in DataTable Init

**File**: `admin/assets/js/ret_metal_process.js`
**Line**: ~2556

```javascript
// BEFORE:
"bDestroy": true,
"bDestroy": true,

// AFTER:
"bDestroy": true,
```

---

### ✏️ OMP-046: `get_old_metal_process()` Never Called on Process List Page

**File**: `admin/assets/js/ret_metal_process.js`
**Line**: ~2538

**Action**: Verify whether the `case:'metal_process_list'` switch block calls `get_old_metal_process()`. If missing, add:
```javascript
case 'metal_process_list':
    get_old_metal_process();
    break;
```

---

### ✏️ OMP-058: `section` Variable Has Wrong Placeholder Text

**File**: `admin/assets/js/ret_metal_process.js`
**Line**: ~3656

```javascript
// BEFORE:
var section = "<option value=''>- Select Category-</option>";

// AFTER:
var section = "<option value=''>- Select Section-</option>";
```

---

### ✏️ OMP-024: `$sales_item_details` Uninitialized for Tagged/NT Pockets

**File**: `admin/application/models/ret_metal_process_model.php`
**Lines**: ~831

```php
// BEFORE: $sales_item_details used without initialization for trans_type 2 & 3

// AFTER: initialize before the if/elseif chain:
$sales_item_details = [];
if ($items['trans_type'] == 1) {
    ...
} else if ($items['trans_type'] == 2) {
    $item_details = $this->get_melting_tag_details(...);
    // $sales_item_details stays [] for this branch
}
if (sizeof($item_details) > 0 || sizeof($sales_item_details) > 0) {
```

---

### ✏️ OMP-015: `updatePurItemData` Updates `net_wt` with `gross_wt` Value

**File**: `admin/application/models/ret_metal_process_model.php`
**Line**: ~1261

```php
// BEFORE:
"...SET gross_wt=(gross_wt+$data['gross_wt']), net_wt=(net_wt+$data['gross_wt'])..."

// AFTER (use net_wt field — verify correct key name from controller):
"...SET gross_wt=(gross_wt+$data['gross_wt']), net_wt=(net_wt+$data['net_wt'])..."
```

---

### ✏️ OMP-016: Missing `no_of_piece` in NT Log (Polishing Existing-Item Branch)

**File**: `admin/application/controllers/admin_ret_metal_process.php`
**Lines**: ~1372–1386 (Polishing Receipt existing NT item branch)

```php
// BEFORE: $non_tag_data array missing 'no_of_piece'
$non_tag_data = [
    'gross_wt'  => ...,
    'net_wt'    => ...,
    // no_of_piece missing ❌
];

// AFTER:
$non_tag_data = [
    'gross_wt'    => ...,
    'net_wt'      => ...,
    'no_of_piece' => $item['no_of_piece'],  // ✅ match new-item branch
];
```

---

### ✏️ OMP-017: `id_branch` Possibly Uninitialized in Polishing Receipt

See OMP-028 — fixed by using `$branchDetails['id_branch']` consistently throughout the polishing receipt block. Verify that after OMP-028 fix, `$id_branch` standalone variable usages are replaced.

---

### ✏️ OMP-012: Polishing Receipt Report — Confirm Authoritative Table

**File**: `admin/application/models/ret_metal_process_model.php`
**Lines**: ~1449–1478

**Action**: Verify with a production DB query whether `ret_old_metal_polishing_recd_details` or `ret_lot_inwards_detail` (with `lot_from=5`) is consistently populated on save. Use the authoritative one, remove or comment the alternative query. If polishing receipt data is only in `ret_lot_inwards_detail`, restore that query.

---

## Final Verification Checklist

After all sprints, run these end-to-end tests:

| Test | Expected Result |
|---|---|
| Save pocket → check UI | Each pocket item appears once ✅ |
| Save melting issue > pocket balance | Server rejects with error ✅ |
| Save NB payment → check DB | `payment_amount` = NB amount (not cash) ✅ |
| Same-state karigar refining receipt | GST shows CGST+SGST ✅ |
| Different-state karigar | GST shows IGST ✅ |
| Save refining receipt, 3 pieces | `ret_old_metal_refining_details.piece = 3` ✅ |
| Save polishing receipt | `ret_lot_inwards.created_branch` not NULL ✅ |
| Open process acknowledgement | No PHP fatal error (no duplicate function) ✅ |
| Open refining process PDF | Only shows data for requested process ID ✅ |
| Date range on process report | Filters data correctly (not all records) ✅ |
| NT pocket categories → polishing receipt | Save succeeds (non-tag validator fixed) ✅ |
| Testing receipt with blank charges | Form rejects (class mismatch fixed) ✅ |
| Browser DevTools — no `console.log` output | 26 calls removed ✅ |

---

## Tally Sync Safety — Post-Fix Verification

After applying Sprint 1 + Sprint 2 fixes, before allowing Tally sync:

```sql
-- All lot_inwards from polishing should now have created_branch set:
SELECT COUNT(*) FROM ret_lot_inwards WHERE created_branch IS NULL AND lot_from = 5;
-- Expected: 0 (or investigate existing NULL rows from before the fix)

-- All NB payments for upcoming transactions should be correct:
SELECT payment_mode, SUM(payment_amount) 
FROM ret_old_metal_process_payment
GROUP BY payment_mode;

-- Confirm outistransfered=0 records do not have data corruption:
SELECT * FROM ret_old_metal_process WHERE outistransfered=0 LIMIT 10;
```

---

## Bug-to-Sprint Cross-Reference

| ID | Severity | Sprint | Status |
|---|---|---|---|
| OMP-001 | 🔴 Critical | 3 | ⬜ |
| OMP-002 | 🔴 Critical | 1 | ⬜ |
| OMP-003 | 🔴 Critical | 1 | ⬜ |
| OMP-004 | 🔴 Critical | 2 | ⬜ |
| OMP-005 | 🟠 High | 4 | ⬜ |
| OMP-006 | 🟠 High | 4 | ⬜ |
| OMP-007 | 🟠 High | 4 | ⬜ |
| OMP-008 | 🟠 High | 4 | ⬜ |
| OMP-009 | 🟠 High | 4 | ⬜ |
| OMP-010 | 🟡 Medium | 5 | ⬜ |
| OMP-011 | 🟡 Medium | 2 | ⬜ (see OMP-053) |
| OMP-012 | 🟡 Medium | 7 | ⬜ |
| OMP-013 | 🟡 Medium | 3 | ⬜ (see OMP-056) |
| ~~OMP-014~~ | ~~Retracted~~ | — | ✅ |
| OMP-015 | 🟡 Medium | 7 | ⬜ |
| OMP-016 | 🟢 Low | 7 | ⬜ |
| OMP-017 | 🟢 Low | 7 | ⬜ |
| OMP-018 | 🟢 Low | 7 | ⬜ |
| OMP-019 | 🔴 Critical | 1 | ⬜ |
| OMP-020 | 🟡 Medium | 5 | ⬜ (see OMP-041) |
| OMP-021 | 🟡 Medium | 5 | ⬜ |
| OMP-022 | 🟡 Medium | 5 | ⬜ |
| OMP-023 | 🟡 Medium | 2 | ⬜ (see OMP-054) |
| OMP-024 | 🟡 Medium | 7 | ⬜ |
| OMP-025 | 🟡 Medium | 5 | ⬜ |
| OMP-026 | 🟢 Low | 5 | ⬜ (see OMP-062) |
| OMP-027 | 🟢 Low | 5 | ⬜ |
| OMP-028 | 🔴 Critical | 1 | ⬜ |
| OMP-029 | 🟠 High | 2 | ⬜ |
| OMP-030 | 🟢 Low | 6 | ⬜ |
| OMP-031 | 🟢 Low | 6 | ⬜ |
| OMP-032 | 🟢 Low | 6 | ⬜ |
| OMP-033 | 🟢 Low | 7 | ⬜ |
| OMP-034 | 🟢 Low | 7 | ⬜ |
| OMP-035 | 🟢 Low | 5 | ⬜ |
| OMP-036 | 🟢 Low | 5 | ⬜ |
| OMP-037 | 🟠 High | 5 | ⬜ |
| OMP-038 | 🟠 High | 1 | ⬜ |
| OMP-039 | 🔴 Critical | 1 | ⬜ |
| OMP-040 | 🟢 Low | 7 | ⬜ |
| OMP-041 | 🔴 Critical | 5 | ⬜ |
| OMP-042 | 🟠 High | 6 | ⬜ |
| OMP-043 | 🟠 High | 5 | ⬜ |
| OMP-044 | 🟡 Medium | 5 | ⬜ |
| OMP-045 | 🟢 Low | 7 | ⬜ |
| OMP-046 | 🟢 Low | 7 | ⬜ |
| OMP-047 | 🔴 Critical | 3 | ⬜ |
| OMP-048 | 🟡 Medium | 6 | ⬜ |
| OMP-049 | 🟢 Low | 6 | ⬜ |
| OMP-050 | 🟢 Low | 6 | ⬜ (see OMP-032) |
| OMP-051 | 🔴 Critical | 3 | ⬜ |
| OMP-052 | 🔴 Critical | 3 | ⬜ |
| OMP-053 | 🔴 Critical | 2 | ⬜ |
| OMP-054 | 🟡 Medium | 2 | ⬜ |
| OMP-055 | 🔴 Critical | 2 | ⬜ |
| OMP-056 | 🔴 Critical | 2 | ⬜ |
| OMP-057 | 🟡 Medium | 3 | ⬜ |
| OMP-058 | 🟢 Low | 7 | ⬜ |
| OMP-059 | 🟢 Low | 5 | ⬜ |
| OMP-060 | 🟠 High | 5 | ⬜ |
| OMP-061 | 🟠 High | 5 | ⬜ |
| OMP-062 | 🟡 Medium | 5 | ⬜ |
| OMP-063 | 🟡 Medium | 5 | ⬜ |
| OMP-064 | 🟢 Low | 5 | ⬜ |
