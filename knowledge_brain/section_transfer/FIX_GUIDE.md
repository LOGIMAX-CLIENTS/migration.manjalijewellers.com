# FIX GUIDE — Section Transfer
> Built: 2026-03-14 | Round 3 | Updated: R16 (2026-03-24) | Source: BUG_CANDIDATES.md

**27 bugs total (R3: 16 original + R5: +3 + R6: +3 + R7: +1 + R11-R14: +4). Fix order: Data Integrity first, Security second, Audit Trail third, UI last.**

---

## Sprint 1 — CRITICAL / Data Integrity (Fix Immediately)

### FIX-ST-004 — Home Counter Stock Decrement-Only Bug
**Bug**: BUG-ST-004 | **File**: `admin_ret_section_transfer.php` L181–311

**Root Cause**: Code checks if destination section already has stock, then DECREMENTS it (`'-'`). No increment path. Intent was: decrement SOURCE stock, increment DESTINATION stock.

**Fix Plan** (2-part):

**Part A — Add source-section decrement (check source, subtract)**:
```php
// BEFORE the existing checkSectionItemExist() call at L201,
// Add a query to find the SOURCE section's home_section_item and decrement it:

// Find source section home stock
$source_section_item = array(
    'id_branch'  => $val['id_branch'],
    'id_section' => $val['trans_from_section'],  // source section from JS
    'id_product' => $tag_details['product_id'],
    'no_of_piece' => $val['pcs'],
    'gross_wt'   => $val['grs_wt'],
    'net_wt'     => $val['net_wt'],
);
$sourceExists = $this->$model->checkSectionItemExist($source_section_item);
if($sourceExists['id_hometag_item'] != '') {
    $source_section_item['id_hometag_item'] = $sourceExists['id_hometag_item'];
    $source_section_item['updated_by'] = $this->session->userdata('uid');
    $source_section_item['updated_on'] = date('Y-m-d H:i:s');
    $this->$model->updatesecNTData($source_section_item, '-');  // Deduct from source
}
```

**Part B — Change existing code at L239 from `'-'` to `'+'` (increment destination)**:
```php
// CHANGE at L239:
// BEFORE:
$nt_status = $this->$model->updatesecNTData($section_item,'-');
// AFTER:
$nt_status = $this->$model->updatesecNTData($section_item,'+');
```

**Part C — Remove duplicate dead if-block at L233**:
```php
// DELETE lines L233–241 (duplicate check, now dead code after Parts A+B):
// if($isExists['id_hometag_item']!='')
// {
//     $section_item['id_hometag_item'] = $isExists['id_hometag_item'];
//     $nt_status = $this->$model->updatesecNTData($section_item,'-');
// }
```

**Verify**: After fix, `ret_home_section_item.no_of_piece` should INCREASE when a tag moves TO a home-counter section, and DECREASE when leaving one.

---

### FIX-ST-001 — SQL Injection in `getSectionTags`
**Bug**: BUG-ST-001 | **File**: `ret_section_transfer_model.php` L98–235

**Fix**: Wrap all dynamic values in `$this->db->escape()`:

```php
// Integer fields — cast to int:
.($data['id_section']!='' && (int)$data['id_section'] > 0 ? "and t.id_section=".(int)$data['id_section']:"")
.($data['id_branch']!='' && (int)$data['id_branch'] > 0 ? "and t.current_branch=".(int)$data['id_branch']:"")
.($data['id_product']!='' && (int)$data['id_product'] > 0 ? "and t.product_id=".(int)$data['id_product']:"")
.($data['est_no']!='' && (int)$data['est_no'] > 0 ? " AND est.esti_no=".(int)$data['est_no']:"")

// String fields — use $this->db->escape():
.($data['old_tag_id']!='' ? "and t.old_tag_id = ".$this->db->escape($data['old_tag_id']):"")
.($data['tag_code']!='' ? "and t.tag_code = ".$this->db->escape($data['tag_code']):"")
```

**Note**: Also fix the duplicate query in the `else` block (L159–225) — same pattern, same fix.

---

### FIX-ST-002 — SQL Injection in `checkNonTagItemExist`
**Bug**: BUG-ST-002 | **File**: `ret_section_transfer_model.php` L251

**Fix**: Switch to ActiveRecord:
```php
// REPLACE raw SQL with:
$this->db->select('id_nontag_item');
$this->db->where('branch', (int)$data['branch']);
$this->db->where('product', (int)$data['product']);
$this->db->where('design', (int)$data['design']);
$this->db->where('id_section', (int)$data['id_section']);
$this->db->where('id_sub_design', (int)$data['id_sub_design']);
$res = $this->db->get('ret_nontag_item');
```

---

### FIX-ST-003 — SQL Injection in `updateNTData` / `updatesecNTData`
**Bug**: BUG-ST-003 | **File**: `ret_section_transfer_model.php` L277–287, L361–377

**Fix**: Cast all numeric fields before interpolation:
```php
function updateNTData($data, $arith) {
    // Validate arith is safe
    $arith = ($arith == '+' || $arith == '-') ? $arith : '+';
    $sql = "UPDATE ret_nontag_item rt SET 
      no_of_piece = (no_of_piece".$arith.(float)$data['no_of_piece']."),
      gross_wt    = (gross_wt".$arith.(float)$data['gross_wt']."),
      net_wt      = (net_wt".$arith.(float)$data['net_wt']."),
      updated_by  = ".(int)$data['updated_by'].",
      updated_on  = ".$this->db->escape($data['updated_on'])."
    WHERE id_nontag_item = ".(int)$data['id_nontag_item'];
    return $this->db->query($sql);
}
```
Apply same fix to `updatesecNTData` (L361–377) for `ret_home_section_item`.

---

## Sprint 2 — Security (Fix This Week)

### FIX-ST-007 — Multi-Mobile OTP Delimiter Bug
**Bug**: BUG-ST-007 | **File**: `admin_ret_section_transfer.php` L568–628

**Fix** — Two changes:

**Change 1** — Use comma delimiter in `send_counterchange_otp`:
```php
// BEFORE (L578):
$sent_otp .= $OTP;
// AFTER:
$sent_otp .= ($sent_otp ? ',' : '') . $OTP;
```

**Change 2** — The `explode(',', $session_otp)` in `verify_counter_change_otp` is already correct (L628); the delimiter just needs to be present.

---

### FIX-ST-006 — OTP Exposed in API Response
**Bug**: BUG-ST-006 | **File**: `admin_ret_section_transfer.php` L610

**Fix**:
```php
// BEFORE:
$status = array('status' => true, 'msg' => 'OTP sent Successfully', 'OTP' => $sent_otp);
// AFTER:
$status = array('status' => true, 'msg' => 'OTP sent Successfully');
```

---

### FIX-ST-015 — Session OTP Not Cleared After Verification
**Bug**: BUG-ST-015 | **File**: `admin_ret_section_transfer.php` L640–653

**Fix**: Add unset after successful commit:
```php
// After trans_commit() at L650, add:
$this->session->unset_userdata('counterchange_otp');
$this->session->unset_userdata('counterchange_otp_exp');
```

---

### FIX-ST-008 — NT Transfer No Server-Side Qty Cap
**Bug**: BUG-ST-008 | **File**: `admin_ret_section_transfer.php` L317–517

**Fix**: Add server-side validation before deducting:
```php
// Before the updateNTData('-') call at L353, verify stock exists:
$current_stock = $this->$model->checkNonTagItemExist($nt_data);
if(!$current_stock['id_nontag_item']) {
    $this->db->trans_rollback();
    echo json_encode(['message' => 'Stock not found for item', 'status' => false]);
    return;
}
// Optionally fetch actual balance and compare before deducting
```

---

## Sprint 3 — Audit Trail & Logic (Fix Next Sprint)

### FIX-ST-010 — NT Deduct Log Missing `to_section`
**Bug**: BUG-ST-010 | **File**: `admin_ret_section_transfer.php` L379

**Fix**:
```php
// BEFORE:
"to_section" => NULL,
// AFTER:
"to_section" => $transfer_to_section,
```

---

### FIX-ST-011 — Home Item Log Missing `from_section`
**Bug**: BUG-ST-011 | **File**: `admin_ret_section_transfer.php` L269

**Fix**:
```php
// BEFORE:
"from_section" => NULL,
// AFTER:
"from_section" => ($val['trans_from_section'] != '' ? $val['trans_from_section'] : NULL),
```

---

### FIX-ST-012 — OTP Insert Scope & Trans Safety
**Bug**: BUG-ST-012 | **File**: `admin_ret_section_transfer.php` L591–615

**Fix**: Move `trans_begin` outside loop and track all inserts:
```php
$this->db->trans_begin();
$all_inserted = true;
foreach($mobile_num[0] as $mobile) {
    if($mobile) {
        $insId = $this->$model->insertData($insData, 'otp');
        if(!$insId) { $all_inserted = false; break; }
    }
}
if($all_inserted) {
    $this->db->trans_commit();
    $status = array('status' => true, 'msg' => 'OTP sent Successfully');
} else {
    $this->db->trans_rollback();
    $status = array('status' => false, 'msg' => 'Unable To Send Try Again');
}
```

---

### FIX-ST-005 — `updatestatus()` Signature Cleanup
**Bug**: BUG-ST-005 | **File**: `ret_section_transfer_model.php` L381

**Fix**: Align the function signature with its actual behavior OR rename for clarity:
```php
// RENAME for clarity (and update controller call accordingly):
function setTagToHomeCounter($tag_id) {
    $sql = "UPDATE ret_taging SET tag_status = 14 WHERE tag_id = ".(int)$tag_id;
    return $this->db->query($sql);
}
// Controller L287 update:
$this->$model->setTagToHomeCounter($val['tag_id']);
```

---

### FIX-ST-009 — SQLi in `getBranchDayClosingData`
**Bug**: BUG-ST-009

**Fix in model** (`ret_section_transfer_model.php` L71):
```php
// Cast to int:
$sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=". (int)$id_branch);
```

---

## Sprint 4 — JS / UI Fixes (Low Priority)

### FIX-ST-013 — Product Validation Blocks Estimation Search
**Bug**: BUG-ST-013 | **File**: `ret_section_transfer.js` L424–430

**Fix**: Skip product validation when est_no is populated:
```javascript
else if(($('#prod_select').val()==="" || $('#prod_select').val()==null)
        && $('#est_no').val() === "") {
    $.toaster({priority:'danger', title:'Warning!', message:'</br>Select Product..'});
}
```

---

### FIX-ST-014 — `SectionTagData` Global Accumulation
**Bug**: BUG-ST-014 | **File**: `ret_section_transfer.js` L900 (Transfer button click)

**Fix**: Reset global before collecting:
```javascript
$('#section_transfer').on('click', function() {
    $('#section_transfer').prop('disabled', true);
    SectionTagData = [];  // ← ADD THIS LINE at top of handler
    ...
```

---

### FIX-ST-016 — `calculateSectiontotal()` Hard-coded Column Indexes
**Bug**: BUG-ST-016 | **File**: `ret_section_transfer.js` L865–873

**Fix**: Use class-based selectors:
```javascript
// BEFORE:
tot_pcs = tot_pcs + ... row.find('td:eq(6) .piece').val();
tot_gwt = tot_gwt + ... row.find('td:eq(7) .gross_wt').val();
// AFTER:
tot_pcs = tot_pcs + ... row.find('.piece').val();
tot_gwt = tot_gwt + ... row.find('.gross_wt').val();
// (The class selectors already exist on the hidden inputs — no HTML change needed)
```

---

## Fix Priority Matrix

| Bug ID | Severity | Effort | Risk if Unfixed | Sprint |
|---|---|---|---|---|
| BUG-ST-004 | 🔴 CRITICAL | Medium | Stock goes negative daily | 1 |
| BUG-ST-001 | 🔴 CRITICAL | Medium | Full DB compromise | 1 |
| BUG-ST-002 | 🔴 CRITICAL | Low | NT path DB compromise | 1 |
| BUG-ST-003 | 🔴 CRITICAL | Low | Stock table destruction | 1 |
| BUG-ST-020 | 🔴 CRITICAL | Low | CSRF — any session can replay transfer | 5 |
| BUG-ST-024 | 🔴 CRITICAL | Medium | Home-counter count inflated on every bill cancel | **Billing PR** |
| BUG-ST-021 | 🟠 HIGH | Medium | OTP never delivered — feature completely broken | 5 |
| BUG-ST-007 | 🟠 HIGH | Low | OTP always fails (multi-mobile) | 2 |
| BUG-ST-006 | 🟠 HIGH | Trivial | Auth bypass visible in DevTools | 2 |
| BUG-ST-008 | 🟠 HIGH | Low | Negative stock via crafted POST | 2 |
| BUG-ST-009 | 🟠 HIGH | Low | Branch ID SQLi | 2 |
| BUG-ST-017 | 🟠 HIGH | Low | NT stock silently disappears on transfer | 2 |
| BUG-ST-018 | 🟠 HIGH | Low | SQLi in shared BT fetchNonTaggedItems endpoint | **BT PR** |
| BUG-ST-025 | 🟠 HIGH | Large | No undo/reverse — mis-transfers uncorrectable | 6 (backlog) |
| BUG-ST-027 | 🟠 HIGH | Low–Med | Order reservation bypassed by barcode search | 6 |
| BUG-ST-015 | 🟢 LOW | Trivial | Session reuse for free transfer | 2 |
| BUG-ST-010 | 🟡 MEDIUM | Trivial | Broken audit trail (to_section NULL) | 3 |
| BUG-ST-011 | 🟡 MEDIUM | Trivial | Broken audit trail (from_section NULL) | 3 |
| BUG-ST-012 | 🟡 MEDIUM | Low | Partial OTP commit | 3 |
| BUG-ST-005 | 🟡 MEDIUM | Low | Code confusion / wrong tag_status | 3 |
| BUG-ST-013 | 🟡 MEDIUM | Trivial | Estimation search blocked | 4 |
| BUG-ST-014 | 🟡 MEDIUM | Trivial | Duplicate transfer possible | 4 |
| BUG-ST-019 | 🟢 LOW | Trivial | Unparameterized internal PKs | 3 |
| BUG-ST-022 | 🟢 LOW | Trivial | SMS fails for 2nd mobile (trailing space) | 5 |
| BUG-ST-023 | 🟢 LOW | Trivial | NT gross wt selector fragile | 4 |
| BUG-ST-016 | 🟢 LOW | Trivial | Totals wrong if cols reorder | 4 |
| BUG-ST-026 | 🟢 LOW | Trivial | Debug exit in billing exposes SQL | **Billing PR** |

---

## Sprint 5 — CSRF & OTP Delivery (Added R6+R7, Documented R16)

### FIX-ST-020 — CSRF Gap in `save` Endpoint
**Bug**: BUG-ST-020 | **File**: `admin_ret_section_transfer.php` L107

**Fix**: Add form-secret check at start of `save` case (copy pattern from Branch Transfer):
```php
// Add at start of case 'save':
if($this->session->userdata('FORM_SECRET') != $_POST['form_secret']) {
    echo json_encode(['status' => false, 'message' => 'Invalid request']);
    return;
}
```
Also add `form_secret` to `add_to_trans()` postData in JS (`ret_section_transfer.js` ~L1035):
```javascript
'form_secret': $('input[name="form_secret"]').val(),
```

---

### FIX-ST-021 — Restore OTP SMS Block
**Bug**: BUG-ST-021 | **File**: `admin_ret_section_transfer.php` L596–601

**Fix**: Uncomment and correct the SMS block. The hardcoded `9486528828` is a placeholder — use `$mobile`:
```php
if($insId) {
    $service = $this->admin_settings_model->get_service_by_code('Counter_Change_Otp');
    if($service['serv_whatsapp'] == 1) {
        $message = "Hi Your OTP For Counter Change is: " . $OTP
                 . " Will expire within " . $expiry . " minute, REGARDS "
                 . strtoupper($comp_details['company_name']) . ".";
        $this->load->model('admin_usersms_model');
        $this->admin_usersms_model->send_whatsApp_message($mobile, $message);
    }
}
```
**Pre-condition**: Confirm `admin_usersms_model` is available in this controller's load context (it is — billing uses it successfully).

---

### FIX-ST-022 — Trailing Space on Multi-Mobile Split
**Bug**: BUG-ST-022 | **File**: `admin_ret_section_transfer.php` L568

**Fix**: Trim each mobile number in the foreach:
```php
foreach($mobile_num[0] as $mobile) {
    $mobile = trim($mobile);  // ← ADD THIS
    if($mobile) { ... }
}
```

---

## Sprint 5b — Round 5 Bugs (Added R5, Documented R16)

### FIX-ST-017 — NT Destination Skip (Wrong Source Guard)
**Bug**: BUG-ST-017 | **File**: `admin_ret_section_transfer.php` L431

**Problem**: Guard `if($val['id_nontag_item'] != '')` checks the **source** item's ID — if  source had no `id_nontag_item` (e.g., receipt-sourced item), destination increment is silently skipped.

**Fix**: Decouple the destination update from the source guard:
```php
// BEFORE (L431-439):
if($val['id_nontag_item'] != '') {
    $nt_data['id_nontag_item'] = $isExists['id_nontag_item'];
    $nt_status = $this->$model->updateNTData($nt_data, '+');
}

// AFTER — use existence of dest row, not source id:
if($isExists['id_nontag_item'] != '') {  // ← change val → isExists
    $nt_data['id_nontag_item'] = $isExists['id_nontag_item'];
    $nt_status = $this->$model->updateNTData($nt_data, '+');
}
```

---

### FIX-ST-018 — SQLi in Shared `fetchNonTaggedItems` (BT Model)
**Bug**: BUG-ST-018 | **File**: `ret_brntransfer_model.php` L326–328

**Fix location**: Branch Transfer model — must be fixed there (shared endpoint).
```php
// BEFORE:
"WHERE branch=" . $data['from_brn']
.($data['prodId'] != '' ? ' and nt.product=' . $data['prodId'] : '')

// AFTER:
"WHERE branch=" . (int)$data['from_brn']
.($data['prodId'] != '' ? ' and nt.product=' . (int)$data['prodId'] : '')
// Similarly cast id_section
```
**Note**: Fix in BT module PR. ST is a consumer — no ST code change needed, but ST team should request/validate the BT fix.

---

### FIX-ST-019 — `checkSectionItemExist` Unparameterized PKs
**Bug**: BUG-ST-019 | **File**: `ret_section_transfer_model.php` L329

**Fix**: Switch to ActiveRecord for consistency:
```php
$this->db->select('id_hometag_item');
$this->db->where('id_branch', (int)$data['id_branch']);
$this->db->where('id_section', (int)$data['id_section']);
$this->db->where('id_product', (int)$data['id_product']);
$res = $this->db->get('ret_home_section_item');
```

---

## Sprint 4 (cont.) — R7 Bug (Documented R16)

### FIX-ST-023 — `calculateNTtotal` Selector Space Typo
**Bug**: BUG-ST-023 | **File**: `ret_section_transfer.js` L1399

**Fix**: Remove the leading space from the `parseFloat` call:
```javascript
// BEFORE:
grs_wt = grs_wt + (isNaN(row.find('.nt_gross_wt').val()) ? 0 : parseFloat(row.find(' .nt_gross_wt').val()));
// AFTER:
grs_wt = grs_wt + (isNaN(row.find('.nt_gross_wt').val()) ? 0 : parseFloat(row.find('.nt_gross_wt').val()));
```

---

## Cross-Module Fixes (Billing — Separate PR)

### FIX-ST-024 — `ret_home_section_item` Not Reversed on Any Billing Cancel/Delete
**Bug**: BUG-ST-024 | **File**: `admin_ret_billing.php` — all 4 reversal paths

**Scope**: **Billing module fix** — ST documents this as a cross-module dependency.

**Fix pattern** (add to each of the 4 billing reversal paths):
```php
// After resetting tag_status=0, add home-section reversal:
// (billing already has access to checkSectionItemExist via the billing model)
$section_item_check = [
    'id_branch'  => $branch,
    'id_section' => $section,  // tag's current id_section
    'id_product' => $product_id,
];
$isExists = $this->$model->checkSectionItemExist($section_item_check);
if($isExists['id_hometag_item'] != '') {
    $rollback_item = [
        'id_hometag_item' => $isExists['id_hometag_item'],
        'no_of_piece'     => $pcs,
        'gross_wt'        => $gwt,
        'net_wt'          => $nwt,
        'updated_by'      => $this->session->userdata('uid'),
        'updated_on'      => date('Y-m-d H:i:s'),
    ];
    $this->$model->updatesecNTData($rollback_item, '-');
}
```
**Apply to**: `cancell` path (L5082), delete path (L7740), receipt cancel path (L7560), `update_branch` path (L9875).

---

### FIX-ST-026 — Debug `exit` Exposes SQL in Billing
**Bug**: BUG-ST-026 | **File**: `admin_ret_billing.php` L10005

**Fix**:
```php
// BEFORE:
} else {
    echo $this->db->last_query(); exit;
    $this->db->trans_rollback();
    ...
}

// AFTER:
} else {
    $this->db->trans_rollback();
    $this->session->set_flashdata('chit_alert', [...'danger'...]);
    echo json_encode(['status' => false, 'message' => 'Transfer update failed']);
}
```

---

## Sprint 6 — Order Reservation Guard (Documented R16)

### FIX-ST-027 — `onlyBranchSelected` Guard Bypassed by Barcode Search
**Bug**: BUG-ST-027 | **File**: `ret_section_transfer_model.php` L89–91

**Option A — Unconditional guard (recommended)**:
```php
// Model getSectionTags() — REMOVE the onlyBranchSelected conditional:
// BEFORE (L229-231):
if ($onlyBranchSelected) {
    $sql .= " AND (t.id_orderdetails IS NULL OR t.id_orderdetails = '')";
}
// AFTER — apply unconditionally to BOTH SQL branches:
$sql .= " AND (t.id_orderdetails IS NULL OR t.id_orderdetails = '')";
```
Apply to both query branches (L98–153 and L159–225). Also remove the `$onlyBranchSelected` variable declaration at L89–91 if no longer needed.

**Option B — Warning badge** (more complex): Return `orderno` / `orderid` columns in tag search results (already fetched via JOIN since R14), and add JS badge logic in `section_trans_list` row builder. See SPRINT_PLAN.md ST-T027 for full acceptance criteria.

> ⚠️ **Team decision required before picking this ticket** — see SPRINT_PLAN.md ST-T027.

---

## Bugs Without Fix Entries (Out of ST Scope)

| Bug | File | Reason |
|---|---|---|
| BUG-ST-025 | `admin_ret_section_transfer.php` | **New feature** — no undo/reverse function exists. Requires product design decision before implementation. Backlog. |

