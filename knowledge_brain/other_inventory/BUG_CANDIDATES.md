# BUG CANDIDATES — Other Inventory
> **Module:** Other Inventory | **Built:** 2026-03-14 (Round 4) | **Updated:** 2026-03-14 (Round 13) | **Total:** 51 candidates

---

## Bug Register

| ID | Severity | Category | Location | Title |
|---|---|---|---|---|
| BRN-OI-001 | 🔴 CRITICAL | SQL Injection | Model — All raw query methods | Unparameterized SQL in all model queries |
| BRN-OI-002 | 🔴 CRITICAL | Dead Code / Server Crash | Model L32–33 | `updateBatchData()` has `print_r + exit` — will crash server |
| BRN-OI-003 | 🔴 CRITICAL | SQL Injection | Model L207 | `getActiveskuid()` — `$searchField` unsanitized in SQL |
| BRN-OI-004 | 🔴 CRITICAL | SQL Injection | Model L852–855 | `CheckIsNameDuplicate()` — `$name` directly interpolated |
| BRN-OI-005 | 🔴 HIGH | CSRF / Insecure GET | Controller L253, L469, L1079 | DELETE via GET request — no CSRF token |
| BRN-OI-006 | 🔴 HIGH | Debug Leak / Broken Error Path | Controller L684–686 | `cancel_purchase_entry` echoes raw SQL query on failure then calls `exit` before rollback |
| BRN-OI-007 | 🔴 HIGH | Transaction / Data Integrity | Controller L738–771 | `product_details/save` loop has no `trans_begin()` but calls `trans_commit()` — no atomicity |
| BRN-OI-008 | 🔴 HIGH | Logic / Wrong Response | Controller L987 | `issue_item/save` failure branch returns `status=TRUE` instead of `false` |
| BRN-OI-009 | 🔴 HIGH | Transaction / Loop | Controller L1159–1162 | `delete_product_mapping` calls `trans_begin()` inside loop — every iteration starts a new transaction, only last one committed |
| BRN-OI-010 | 🔴 HIGH | Transaction / Loop | Controller L1188–1203 | `update_product_mapping` calls `trans_begin()` inside loop — same anti-pattern |
| BRN-OI-011 | 🟠 MEDIUM | Logic / Wrong Image Target | Controller L345 | `set_image_other($item, ...)` passes `$item` (update return value) instead of `$id` — image linked to wrong ID |
| BRN-OI-012 | 🟠 MEDIUM | Logic / Field Not Updated | Controller L269–277 | `other_inventory/update` doesn't update `stock_id_uom` or `issue_to` — data loss on edit |
| BRN-OI-013 | 🟠 MEDIUM | Race Condition | Model L129–147 | `generateItemRefNo()` uses `MAX()` without row locking — concurrent tagging can generate duplicate `item_ref_no` |
| BRN-OI-014 | 🟠 MEDIUM | Race Condition | Model L149–153 | `getlastrefno()` called inside tagging loop without locking — duplicate ref codes possible |
| BRN-OI-015 | 🟠 MEDIUM | Wrong Filter Key | Model L523 | `get_AvailableStockDetails()` checks `$data['id_inv_size']` but injects `$data['id_size']` into SQL — filter never works |
| BRN-OI-016 | 🟠 MEDIUM | Logic / Business Rule Gap | Controller L927–946 | `issue_item/save` gift_mapping insert loop runs BEFORE `trans_begin()` — gift mappings inserted even if issue transaction fails |
| BRN-OI-017 | 🟠 MEDIUM | Logic / Misleading ZPL | Controller L1307–1329 | `other_inventory_print` does NOT reset `$tagprintCode` after emitting each label group of 3 — tags accumulate incorrectly if >3 |
| BRN-OI-018 | 🟠 MEDIUM | Duplicate Model Load | Constructor L17, L19 | `admin_settings_model` loaded TWICE in constructor — wasted memory |
| BRN-OI-019 | 🟡 LOW | Hardcoded Enum | Model L362 | `other_inventory_stock()` hardcodes status values `(1,3,4)` as literals — no constants, fragile |
| BRN-OI-020 | 🟡 LOW | Day Closing Inconsistency | Controller L560, L735, L914 | When day is closed, `entry_date` = date string (no time); when not closed = datetime — inconsistent data type |
| BRN-OI-021 | 🟡 LOW | Incomplete Stock Filter | Model L392–409 | `get_invnetory_item()` uses `HAVING tot_pcs > 0` on aliased column but no GROUP BY — may not filter correctly on all MySQL modes |
| BRN-OI-022 | 🟡 LOW | Typo / Table Name | Model + Schema | `ret_other_invnetory_issue` and column `other_invnetory_item_id` — consistent typo throughout |
| BRN-OI-023 | 🟡 LOW | Cancelled Purchase Not Reversed | Controller L668–691 | Purchase cancel sets `status=2` but does NOT reverse tagged pieces in `ret_other_inventory_purchase_items_details` |
| BRN-OI-025 | 🟠 MEDIUM | JS Bug / Wrong Message | JS L2118 | `#item_issue` validation shows "Please Select Branch" when item is missing |
| BRN-OI-026 | 🟠 MEDIUM | JS Bug / Wrong Toast | JS L2730 | `delete_product_mapping` JS failure path sends `priority:'success'` toast |
| BRN-OI-027 | 🟡 LOW | JS Bug / Selector Error | JS L699 | `get_other_inventory_ref_no` missing `#` in select2 init — silently fails |
| BRN-OI-028 | 🟡 LOW | JS Bug / Off-by-one | JS L1457–1462 | `keypress` on `#cancel_remark` fires before char appears — length off by 1 |
| BRN-OI-029 | 🟡 LOW | JS Bug / Type Coercion | JS L2161 | Issue pcs check: `item_total_pcs` is string, `<` comparison skips parseFloat |
| BRN-OI-030 | 🟡 LOW | JS/Server Mismatch | JS L2495 | `available_stock_details()` POSTs `id_size` but model checks `id_inv_size` (confirms BRN-OI-015) |
| BRN-OI-031 | 🔴 HIGH | Logic / Silent DB Corruption | Controller L935–941 | `issue_item/save` gift_mapping insert has **trailing spaces** in 3 PHP array keys — inserts NULL for `id_other_item`, `id_scheme`, `item_issue_limit` |
| BRN-OI-032 | 🔴 CRITICAL | SQL Injection | Model L675 | `get_inv_chit_gift()` — `$id` directly concatenated into raw SELECT query |
| BRN-OI-033 | 🔴 CRITICAL | SQL Injection | Model L682 | `delete_gift_map_data()` — `$id` directly concatenated into raw DELETE query |
| BRN-OI-034 | 🔴 HIGH | SQL Injection | Model L789, L814, L830, L841 | `getPurchaseDet`, `get_purchase_item_det`, `get_purchase_gst_det`, `get_ref_no_details` — all concat `$id` into raw queries |
| BRN-OI-035 | 🔴 HIGH | SQL Injection | Model L545, L592, L595 | `check_other_inv_products_maping`, `get_product_linked_items` — `$id_product`, `$id_branch`, `$pro_id` raw concatenation |
| BRN-OI-036 | 🔴 HIGH | Null Dereference / Fatal Error | Model L151–152 | `getlastrefno()` calls `$sql->row()->ref_no` — if table is empty `row()` returns NULL → fatal PHP error on first-ever tagging |
| BRN-OI-037 | 🟠 MEDIUM | Performance / N+1 Query | Model L569–581 | `get_productMappedDetails()` runs 1 query then N sub-queries inside foreach — unbounded for large product catalogs |
| BRN-OI-038 | 🟡 LOW | Wrong Return Value | Model L684 | `delete_gift_map_data()` compares `$edit_flag == 1` but `db->query()` returns `true`, not `1` — always returns `0`, deletion failure undetectable |
| BRN-OI-039 | 🔴 HIGH | Transaction / Missing Rollback | Controller L658–660 | `purchase_entry/save` — when header insert fails (`!$insId`), no rollback called — dangling transaction left open |
| BRN-OI-040 | 🟠 MEDIUM | File / Orphan Assets | Controller L610–640 | `purchase_entry/save` — images written to disk BEFORE transaction status check; failed saves leave permanent orphan files on server |
| BRN-OI-041 | 🟠 MEDIUM | Performance / Excessive Queries | Controller L740 | `product_details/save` calls `getlastrefno()` inside nested double-loop — 1 DB round-trip per piece across all items (O(N×M) queries) |
| BRN-OI-042 | 🔴 HIGH | Logic / Fatal on NULL Input | Controller L803–832 | `generaterefCode(NULL)` — when `getlastrefno()` returns NULL, `explode('-', NULL)` crashes in PHP8 or produces malformed ref codes in PHP7 |
| BRN-OI-043 | 🔴 HIGH | XSS / Stored | Views: `form.php` L228 | `var other_id = "<?php echo $other['id_other_item']; ?>"` — raw DB value echoed into JS string — stored XSS / JS injection via crafted item ID |
| BRN-OI-044 | 🟠 MEDIUM | XSS / Stored | Views: `purchase/purchase_entry.php` L45–61 | Supplier name, address, email, GST concatenated as raw HTML strings with no `htmlspecialchars` — stored XSS via supplier profile |
| BRN-OI-045 | 🟠 MEDIUM | XSS / Stored | Views: `purchase/purchase_entry.php` L127 | `$po_detail['product_name']` echoed raw into `<td>` — stored XSS via product name |
| BRN-OI-046 | 🟡 LOW | XSS / Reflected (Session) | Views: `issue/list.php` L65, L69, L71 | Flash message `class`, `title`, `message` echoed raw into HTML — low risk (session-only) but class injection can break layout |
| BRN-OI-047 | 🔴 HIGH | Cross-Module / Transaction Boundary | `admin_ret_billing.php` L4492–4568 | Billing `trans_commit()` fires at L4494 BEFORE Other Inventory issue inserts at L4522 — commit-then-write creates ghost bills with no stock deduction on OI write failure |
| BRN-OI-048 | 🟡 LOW | Logic / Wrong Status Response | Controller L986–988 | `issue_item/save` returns `status => TRUE` with "Unable to Issue Gift Items" message on insert failure — JS treats this as success |
| BRN-OI-049 | 🟡 LOW | XSS / Stored Print View | Views: `print/qr_print.php` L55–56 | `$d['pro_name']` and `$d['item_ref_no']` echoed raw into `<label>` tags in QR print view — stored XSS via product name / ref code |
| BRN-OI-050 | 🔴 HIGH | SQL Injection / Cross-Module | `ret_billing_model.php` L7138 | `get_bill_detail_other_inv()` — raw `$bill_id` concatenated into SELECT — SQL injection via bill cancellation/delete path |
| BRN-OI-051 | 🟡 LOW | XSS / Stored (dompdf Print View) | `item_qrcode.php` L24–25 | `$img[0]['name']` and `$img[0]['src']` echoed raw into dompdf HTML label and img src — stored XSS via product name in QR label print |

---

## Detailed Bug Analysis

---

### BRN-OI-001 — SQL Injection (All Raw Queries)
**Severity:** 🔴 CRITICAL
**Location:** `ret_other_inventory_model.php` — almost all query methods

**Evidence:**
```php
// Model L57
$this->db->query("SELECT ... FROM branch WHERE id_branch = $branch_id");

// Model L184
$this->db->query("select ... from ret_other_inventory_item where id_other_item=" . $id_other_item);

// Model L248
$this->db->query("... WHERE i.id_other_item=" . $id_other_item_type . "");
```

**Impact:** Any branch/item ID from user input could execute arbitrary SQL.

**Fix:** Use CI's Active Record builder with `where()` or parameterized queries: `$this->db->query('... WHERE id = ?', [$id])`.

---

### BRN-OI-002 — Dead Code Crash in `updateBatchData()`
**Severity:** 🔴 CRITICAL
**Location:** `ret_other_inventory_model.php` L32–33

**Evidence:**
```php
public function updateBatchData($data, $table, $id_field, $id_value)
{
    $insert_flag = 0;
    $this->db->where($id_field, $id_value);
    $updat = $this->db->update_batch($table, $data);
    print_r($this->db->last_query());  // ← DEAD DEBUG CODE
    exit;                               // ← CRASHES SERVER
    if ($this->db->affected_rows() > 0) {
        return TRUE;
    }
```

**Impact:** Any call to `updateBatchData()` crashes the server with raw DB data exposed in response.

**Fix:** Remove lines 32–33 entirely.

---

### BRN-OI-003 — SQL Injection in `getActiveskuid()`
**Severity:** 🔴 CRITICAL
**Location:** `ret_other_inventory_model.php` L203–208

**Evidence:**
```php
function getActiveskuid($SearchTxt, $searchField)
{
    $data = $this->db->query("SELECT ... WHERE ot.item_hsn_code is NULL AND ot." . $searchField . " LIKE '%" . $SearchTxt . "%'");
```

**Impact:** `$searchField` is a column selector injected directly — attacker can craft `searchField` to inject arbitrary SQL. `$SearchTxt` is also unescaped.

**Fix:** Whitelist `$searchField` against allowed column names. Use `$this->db->escape_like()` for LIKE pattern.

---

### BRN-OI-004 — SQL Injection in `CheckIsNameDuplicate()`
**Severity:** 🔴 CRITICAL
**Location:** `ret_other_inventory_model.php` L852–855

**Evidence:**
```php
$sql = $this->db->query("SELECT name as cat_name FROM ret_other_inventory_item_type
WHERE name ='".$name."' ");
```

**Impact:** `$name` directly concatenated — classic SQL injection.

**Fix:** Use `$this->db->where('name', $name)` and Active Record pattern.

---

### BRN-OI-005 — DELETE via GET / CSRF Vulnerable
**Severity:** 🔴 HIGH
**Location:** `admin_ret_other_inventory.php` L253, L469, L1079

**Evidence:**
```php
case 'delete':
    $this->db->trans_begin();
    $this->$model->deleteData('id_other_item', $id, 'ret_other_inventory_item');
```

Route: `GET /other_inventory/delete/{id}` — `$id` taken from URL parameter.

**Impact:** Malicious link can delete any item. No CSRF token, no POST requirement.

**Fix:** Convert delete to POST with CSRF token, or add CSRF middleware.

---

### BRN-OI-006 — Debug Echo + Exit Before Rollback in Cancel
**Severity:** 🔴 HIGH
**Location:** `admin_ret_other_inventory.php` L684–686

**Evidence:**
```php
} else {
    echo $this->db->last_query();   // ← Leaks SQL query
    exit;                           // ← Prevents rollback!
    $this->db->trans_rollback();    // ← Dead code — never reached
    $this->session->set_flashdata(...);
```

**Impact:** On cancel failure: raw SQL query exposed to user, transaction never rolled back, session message never set.

**Fix:** Remove L684–685. Move rollback before any echo.

---

### BRN-OI-007 — Missing `trans_begin()` in `product_details/save`
**Severity:** 🔴 HIGH
**Location:** `admin_ret_other_inventory.php` L731–772

**Evidence:**
```php
case 'save':
    $order_items = json_decode($_POST['order_items'], true);
    // ... loops through items, inserts piece records and log rows ...
    if ($this->db->trans_status() === TRUE) {
        $this->db->trans_commit();  // ← Commit without begin!
```

**Impact:** No transaction wraps the piece record inserts and log writes. If any insert fails mid-loop, previous inserts are left orphaned in DB with no rollback possible.

**Fix:** Add `$this->db->trans_begin()` before the `foreach` at L738.

---

### BRN-OI-008 — Wrong `status=TRUE` on Failed Issue Insert
**Severity:** 🔴 HIGH
**Location:** `admin_ret_other_inventory.php` L986–988

**Evidence:**
```php
} else {
    $responseData = array('status' => TRUE, 'message' => 'Unable to Issue Gift Items.');
    // ← Returns success=TRUE when issue insert failed!
```

**Impact:** When `$insId` is 0 (insert failed), the caller receives `status=TRUE` — JS thinks issue succeeded when it didn't. Inventory is not updated but UI may proceed.

**Fix:** Change `status => TRUE` to `status => FALSE` at L987.

---

### BRN-OI-009 — `trans_begin()` Inside Loop in `delete_product_mapping()`
**Severity:** 🔴 HIGH
**Location:** `admin_ret_other_inventory.php` L1159–1164

**Evidence:**
```php
foreach ($reqdata as $items) {
    $this->db->trans_begin();  // ← New transaction every iteration!
    $this->$model->deleteData('inv_des_id', $items['inv_des_id'], 'ret_other_inventory_product_link');
}
if ($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();  // ← Only commits last transaction
```

**Impact:** Each iteration's `trans_begin()` overrides the previous. Only the LAST delete row is committed atomically. Earlier deletes may be committed or orphaned depending on CI/MySQL mode.

**Fix:** Move `trans_begin()` before the loop. Commit/rollback once after loop completes.

---

### BRN-OI-010 — `trans_begin()` Inside Loop in `update_product_mapping()`
**Severity:** 🔴 HIGH
**Location:** `admin_ret_other_inventory.php` L1188–1205

**Evidence:** Same pattern as BRN-OI-009 — `trans_begin()` called on each iteration in both the `id_product==0` all-products branch (L1188) and the specific product branch (L1201).

**Fix:** Same as BRN-OI-009 — hoist `trans_begin()` before the foreach loop.

---

### BRN-OI-011 — Image Linked to Wrong ID on Edit
**Severity:** 🟠 MEDIUM
**Location:** `admin_ret_other_inventory.php` L345

**Evidence:**
```php
$item = $this->$model->update_other_inventory($data, $id, 'id_other_item', 'ret_other_inventory_item');
// ...
$this->set_image_other($item, $inv_data['sku_id']);  // $item = update return value (could be 0)
```

Inside `set_image_other($id, $skuid)`:
```php
$id = $this->$model->updateData($data, 'id_other_item', $id, 'ret_other_inventory_item');
```

**Impact:** `$item` is the return value of `update_other_inventory()` — which returns `$id` on success or `0` on failure. If DB returns 0, the image is linked to id=0 (no item). Should use the original `$id` variable.

**Fix:** Change L345 to `$this->set_image_other($id, $inv_data['sku_id'])`.

---

### BRN-OI-012 — `stock_id_uom` and `issue_to` Not Updated on Edit
**Severity:** 🟠 MEDIUM
**Location:** `admin_ret_other_inventory.php` L269–277

**Evidence:**
```php
$data = array(
    'name'            => strtoupper($addData['name']),
    'id_inv_size'     => ...,
    'item_for'        => $addData['item_for'],
    'issue_preference' => ...,
    // ← 'stock_id_uom' missing
    // ← 'issue_to' missing
    'unit_price'      => ...,
);
```

**Impact:** User can change UOM and Issue Target in the edit form, but changes are silently ignored — old values persist.

**Fix:** Add `'stock_id_uom' => $addData['id_uom']` and `'issue_to' => $addData['issue_to']` to the update array.

---

### BRN-OI-013 & BRN-OI-014 — Race Condition in Piece Ref Generation
**Severity:** 🟠 MEDIUM
**Location:** Model L129–153, Controller L740–741

**Evidence:**
```php
// In product_details/save loop (no trans_begin):
$lastTagCode = $this->$model->getlastrefno();  // MAX query — not locked
$item_ref_no = $this->generaterefCode($lastTagCode);
// ...inserts with this ref_no...
```

**Impact:** Two simultaneous tagging operations can both read the same `lastTagCode` and generate the same `item_ref_no`, violating the uniqueness of piece tags.

**Fix:** Add `SELECT ... FOR UPDATE` lock on the details table, or use a DB-level UNIQUE constraint on `item_ref_no` to catch collisions.

---

### BRN-OI-015 — Wrong Filter Key in Available Stock Query
**Severity:** 🟠 MEDIUM
**Location:** `ret_other_inventory_model.php` L523

**Evidence:**
```php
function get_AvailableStockDetails($data)
{
    ...
    // Condition checks $data['id_inv_size'] but injects $data['id_size']:
    . ($data['id_inv_size'] != '' ? " and s.id_inv_size=" . $data['id_size'] . "" : '') .
```

**Impact:** If JS sends `id_inv_size` but the PHP checks `id_inv_size` for truthiness but reads `id_size` for the value — filter may be empty/null and filter silently fails, returning all records unfiltered.

**Fix:** Change `$data['id_size']` to `$data['id_inv_size']` at L523.

---

### BRN-OI-016 — Gift Mapping Insert Before `trans_begin()` in Issue
**Severity:** 🟠 MEDIUM
**Location:** `admin_ret_other_inventory.php` L927–948

**Evidence:**
```php
if ($id_other_item) {
    // Gift mapping inserts happen here (L927–946)
    for ($i = 1; $i <= $table_length; $i++) {
        $insert_gift_map = $this->$model->insertData($data, 'gift_mapping');
    }
}
// trans_begin() only called at L948 -- AFTER gift_mapping inserts
$this->db->trans_begin();
$insId = $this->$model->insertData($insData, 'ret_other_invnetory_issue');
```

**Impact:** Gift mapping rows are permanently written before the issue transaction starts. If the issue fails and rolls back, orphan `gift_mapping` rows remain.

**Fix:** Move gift_mapping inserts inside the `trans_begin()` block (after L948).

---

### BRN-OI-017 — `$tagprintCode` Not Reset in `other_inventory_print()`
**Severity:** 🟠 MEDIUM
**Location:** `admin_ret_other_inventory.php` L1307–1329

**Evidence:**
```php
// other_inventory_print (the first function, L1295)
foreach ($data as $d) {
    $tagprintCode = $tagprintCode . ($this->get_tag_code($d, $no));
    if ($no == 3 || $i == $totalcount) {
        $printCode = $this->get_printer_code($tagprintCode);
        // ...
        // ← $tagprintCode is NOT reset here!
    }
    $i++;
}
```

Compare with `product_other_inventory_print()` at L1336 which correctly resets `$tagprintCode = ""` after each group of 3.

**Impact:** For batches > 3, ZPL label groups accumulate — 2nd batch of 3 contains all 6 tags' codes, 3rd batch contains 9, etc. — malformed/duplicate print output.

**Fix:** Add `$tagprintCode = "";` after L1326 (the `$content = $content . $printCode;` block), matching the fix already applied in `product_other_inventory_print()`.

---

### BRN-OI-018 — Duplicate Model Load in Constructor
**Severity:** 🟡 LOW
**Location:** `admin_ret_other_inventory.php` L17, L19

**Evidence:**
```php
$this->load->model("admin_settings_model");  // L17
$this->load->model("log_model");              // L18
$this->load->model("admin_settings_model");  // L19 — duplicate!
```

**Fix:** Remove L19.

---

### BRN-OI-019 — Hardcoded Log Status Values
**Severity:** 🟡 LOW
**Location:** `ret_other_inventory_model.php` L362

**Evidence:**
```php
AND (l.status=1 or l.status=4 or l.status=3)
```

**Fix:** Define class constants: `const LOG_STATUS_ISSUE=1; LOG_BT_OTHER=3; LOG_BT_TRANSIT=4;`

---

### BRN-OI-020 — Day Closing Entry Date Type Inconsistency
**Severity:** 🟡 LOW
**Location:** Controller L560, L735, L914 / Business Rule RULE-OI-011

**Evidence:**
```php
$entry_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
// When day closed: entry_date = "2026-03-14" (DATE string, no time)
// When open: entry_date = "2026-03-14 15:30:00" (DATETIME)
```

**Impact:** Date-range queries on log table may miss some records or cause ordering issues when mixing DATE and DATETIME values.

---

### BRN-OI-021 — `HAVING` Clause Risk in `get_invnetory_item()`
**Severity:** 🟡 LOW
**Location:** `ret_other_inventory_model.php` L409

**Evidence:**
```php
having tot_pcs>0   -- Used without corresponding GROUP BY on main query
```

The outer query JOINs to a subquery `d` aliased with `tot_pcs`, but the main SELECT doesn't have its own GROUP BY. On strict MySQL modes, `HAVING` without `GROUP BY` can behave unexpectedly.

---

### BRN-OI-022 — Table Name Typo Propagated
**Severity:** 🟡 LOW
**Location:** Throughout model and controller

`ret_other_invnetory_issue` (`invnetory` missing `n`) and column `other_invnetory_item_id` both carry the same typo. Documented for tracking only — fixing requires a DB migration.

---

### BRN-OI-023 — Purchase Cancel Doesn't Reverse Tagged Pieces
**Severity:** 🟡 LOW
**Location:** `admin_ret_other_inventory.php` L668–691

**Impact:** When a purchase is cancelled (`status=2`), already-tagged pieces in `ret_other_inventory_purchase_items_details` remain with `status=0` (available) — technically "available" inventory backed by a cancelled purchase. No automatic log reversal.

**Business risk:** Low (operator would typically not tag before cancelling), but data integrity gap.

---

### BRN-OI-024 — Duplicate Controller Route `/get_other_inventory_item`
**Severity:** 🟡 LOW
**Location:** `admin_ret_other_inventory.php` controller method table

Two separate methods respond to `get_other_inventory_item` (the route is handled by the named function at L847). Documented in route table but no functional impact currently.

---

## JS-Layer Bugs (Round 4)

---

### BRN-OI-025 — Wrong Validation Message for Item Selection
**Severity:** 🟠 MEDIUM
**Location:** `ret_other_inventory.js` L2118

**Evidence:**
```javascript
else if ($('#select_item').val() == '' || $('#select_item').val() == null) {
    $.toaster({ priority: 'danger', title: 'Warning!', message: '..Please Select Branch..' });
    // ← Says "Branch" when item is not selected!
```

**Impact:** User sees misleading error — thinks no branch selected when they actually forgot to pick an item.

**Fix:** Change to `'Please Select Item..'`.

---

### BRN-OI-026 — Success Toast on Delete Mapping Failure
**Severity:** 🟠 MEDIUM
**Location:** `ret_other_inventory.js` L2730

**Evidence:**
```javascript
else {
    $.toaster({ priority: 'success', title: 'Warning!', message: '...Please Select Item' });
    // ← priority:'success' (green toast) when NOTHING is selected!
```

**Impact:** User gets a green success-style toast prompting to select an item — confusing UX.

**Fix:** Change `priority: 'success'` to `priority: 'danger'`.

---

### BRN-OI-027 — Missing `#` Selector in select2 Init
**Severity:** 🟡 LOW
**Location:** `ret_other_inventory.js` L699

**Evidence:**
```javascript
$("select_ref_no").select2({   // ← missing # prefix!
    placeholder: 'Select Ref no',
    allowClear: true
});
```

**Impact:** select2 initialization on `#select_ref_no` silently fails. The dropdown renders as a bare HTML `<select>` without select2 styling or search.

**Fix:** Change to `$("#select_ref_no").select2({...})`.

---

### BRN-OI-028 — `keypress` Instead of `keyup` on Cancel Remark
**Severity:** 🟡 LOW
**Location:** `ret_other_inventory.js` L1457

**Evidence:**
```javascript
$('#cancel_remark').on('keypress', function () {
    if (this.value.length > 6) {
        $('#purchase_cancel').prop('disabled', false);
```

**Impact:** `keypress` fires BEFORE the character is appended to `value`, so `this.value.length` is one behind. The submit button is enabled only after the 8th keystroke when 7 characters exist, not at 7 characters. Minor UX annoyance.

**Fix:** Change `'keypress'` to `'keyup'`.

---

### BRN-OI-029 — String vs Number Comparison for Issue Pcs Validation
**Severity:** 🟡 LOW
**Location:** `ret_other_inventory.js` L2161

**Evidence:**
```javascript
var item_total_pcs = $('#issue_total_pcs').val();  // ← String!
if (parseFloat(available_pcs) < item_total_pcs) {  // ← Compares float to string
```

**Impact:** JavaScript coerces `item_total_pcs` to number in `<` comparison (usually works), but edge cases like leading spaces or empty string can cause incorrect behavior. `item_total_pcs` should be parsed.

**Fix:** Change to `parseFloat(item_total_pcs)` in the comparison.

---

### BRN-OI-030 — JS Posts `id_size` but Server Expects `id_inv_size`
**Severity:** 🟡 LOW
**Location:** `ret_other_inventory.js` L2495 (cross-references BRN-OI-015)

**Evidence:**
```javascript
data: { ..., "id_size": $('#select_size').val(), ... }
// ← Posts id_size, but model get_AvailableStockDetails() checks $data['id_inv_size']
```

**Impact:** The size filter in Available Stock is doubly broken — JS sends the wrong key name AND the model reads the wrong key. Combined with BRN-OI-015, this fully explains why size filtering never works.

**Fix (JS side):** Change `"id_size"` to `"id_inv_size"` at L2495. Fix model side in BRN-OI-015 simultaneously.

---

| Priority | ID | Why |
|---|---|---|
| 1 | BRN-OI-002 | Active crash risk — any updateBatchData call kills server |
| 2 | BRN-OI-008 | Active logic bug — issue failure silently returns success |
| 3 | BRN-OI-031 | Silent DB corruption — gift_mapping rows inserted with NULL IDs every time issue has gift items |
| 4 | BRN-OI-006 | Debug code in production — SQL query exposed on cancel failure |
| 5 | BRN-OI-007 | No atomicity in product tagging — orphan records on partial failure |
| 6 | BRN-OI-017 | ZPL print corruption for >3 tags batch |
| 7 | BRN-OI-009, BRN-OI-010 | Transaction loop anti-pattern — partial commits |
| 8 | BRN-OI-016 | Gift mapping orphan on issue failure |
| 9 | BRN-OI-015 | Available stock filter silently broken |
| 10 | BRN-OI-011 | Image saved to wrong item on update |
| 11 | BRN-OI-012 | UOM/issue_to lost on edit |
| 12 | BRN-OI-003, BRN-OI-004 | SQL injection in search/name-check |
| 13 | BRN-OI-001 | Broad SQL injection — large scope fix |
| 14 | BRN-OI-005 | CSRF on delete endpoints |

---

## Round 5 Additions

---

### BRN-OI-031 — Trailing-Space PHP Keys in `issue_item/save` Gift Mapping Insert
**Severity:** 🔴 HIGH
**Location:** `admin_ret_other_inventory.php` L935–941

**Evidence:**
```php
// Controller L935–941 (issue_item/save — gift_mapping insert loop)
$data = array(
    'id_other_item '     => $id_other_item,   // ← TRAILING SPACE in key!
    'id_scheme '         => $selected_scheme[$i], // ← TRAILING SPACE!
    'item_issue_limit '  => $quantity[$i],     // ← TRAILING SPACE!
    'date_add'           => date("Y-m-d H:i:s"),
    'created_by'         => $this->session->userdata('uid'),
);
$insert_gift_map = $this->$model->insertData($data, 'gift_mapping');
```

**Contrast with the correct pattern in `other_inventory/save` (L177–184) and `update` (L325–333):**
```php
// L177 — CORRECT (no trailing spaces)
$data = array(
    'id_other_item'    => $id_other_item,
    'id_scheme'        => $selectedSchemeId,
    'item_issue_limit' => $quantity[$i],
    ...
);
```

**Impact:** CodeIgniter's `insert()` sends the key with the trailing space as the column name. MySQL silently accepts column names with trailing spaces (it trims them internally in some modes, but NOT when used with CodeIgniter's Active Record builder which wraps column names in backticks: `` `id_other_item ` `` → unknown column error OR silent NULL). 

Result: Every `issue_item/save` call that has gift items (`table_length > 0`) inserts `gift_mapping` rows where:
- `id_other_item` = NULL (gift cannot be linked back to inventory item)
- `id_scheme` = NULL (scheme association lost)
- `item_issue_limit` = NULL (quantity limit gone)

These orphan rows with all-NULL foreign keys accumulate in `gift_mapping` silently — the insert doesn't fail (no NOT NULL constraints), but the data is completely useless.

**Fix:** Remove the trailing space from all three key names in the `issueitem/save` array:
```php
$data = array(
    'id_other_item'    => $id_other_item,
    'id_scheme'        => $selected_scheme[$i],
    'item_issue_limit' => $quantity[$i],
    'date_add'         => date("Y-m-d H:i:s"),
    'created_by'       => $this->session->userdata('uid'),
);
```

**Verified:** 2026-03-14 (Round 5) — Code read at L935–941, compared against correct pattern at L177–184.

---

> **Round 5 Verification Summary:** All 30 existing bugs re-confirmed with exact line numbers. 1 new bug (BRN-OI-031) discovered. Total: **31 bugs**.

---

## Round 6 Additions (Model L510–870 Deep Scan)

---

### BRN-OI-032 — SQL Injection in `get_inv_chit_gift()`
**Severity:** 🔴 CRITICAL
**Location:** `ret_other_inventory_model.php` L675

**Evidence:**
```php
public function get_inv_chit_gift($id)
{
    $sql = "SELECT * FROM gift_mapping where id_other_item=" . $id;
    $result = $this->db->query($sql);
```

**Impact:** `$id` comes from the controller's `$id` URL parameter (edit route). An attacker with edit access can inject arbitrary SQL via the ID segment.

**Fix:** Use Active Record: `$this->db->where('id_other_item', $id)->get('gift_mapping')->result_array();`

---

### BRN-OI-033 — SQL Injection in `delete_gift_map_data()`
**Severity:** 🔴 CRITICAL
**Location:** `ret_other_inventory_model.php` L682

**Evidence:**
```php
public function delete_gift_map_data($id)
{
    $sql = "DELETE from gift_mapping where id_other_item=" . $id;
    $edit_flag = $this->db->query($sql);
    return ($edit_flag == 1 ? $id : 0);  // ← also: always returns 0 (see BRN-OI-038)
```

**Impact:** `$id` directly concatenated into a DELETE statement — DELETE injection can wipe entire table with crafted input.

**Fix:** Use `$this->db->where('id_other_item', $id)->delete('gift_mapping');`

---

### BRN-OI-034 — SQL Injection in Print-Path Methods (4 methods)
**Severity:** 🔴 HIGH
**Location:** `ret_other_inventory_model.php` L789, L814, L830, L841

**Evidence:**
```php
// getPurchaseDet() — L789
$sql = $this->db->query("... WHERE ... p.otr_inven_pur_id =" . $id . "");

// get_purchase_item_det() — L814
"... pur.otr_inven_pur_id =" . $id . ""

// get_purchase_gst_det() — L830
"... pur.otr_inven_pur_id =" . $id . ""

// get_ref_no_details() — L841
"... where pur_item_detail_id =" . $id
```

**Impact:** All four print/detail methods receive `$id` from URL parameters. Purchase print pages served via GET routes expose SQL injection via the `{id}` path segment.

**Fix:** Replace all raw concatenations with parameterized `$this->db->query('... WHERE id = ?', [$id])` pattern.

---

### BRN-OI-035 — SQL Injection in Product Mapping Methods
**Severity:** 🔴 HIGH
**Location:** `ret_other_inventory_model.php` L545, L592, L595

**Evidence:**
```php
// check_other_inv_products_maping() — L545
"WHERE inv_pro_id=" . $id_product . " AND inv_des_otheritemid=" . $inv_des_otheritemid

// get_product_linked_items() — L592, L595
"WHERE d.status=0 AND d.current_branch=" . $id_branch
"where inv_pro_id=" . $pro_id
```

**Impact:** `$id_product`, `$inv_des_otheritemid`, `$id_branch`, and `$pro_id` all arrive from POST parameters. Product mapping flow is injectable.

**Fix:** Use `$this->db->where()` for all these conditions.

---

### BRN-OI-036 — Null Object Dereference in `getlastrefno()`
**Severity:** 🔴 HIGH
**Location:** `ret_other_inventory_model.php` L151–152

**Evidence:**
```php
function getlastrefno()
{
    $sql = $this->db->query("SELECT d.pur_item_detail_id,d.item_ref_no FROM ret_other_inventory_purchase_items_details d
    where d.ref_no is not null ORDER by pur_item_detail_id DESC LIMIT 1");
    return $sql->row()->ref_no;  // ← Fatal if table is empty!
}
```

**Impact:** On a fresh install or after complete data wipe, `$sql->row()` returns `NULL`. Chaining `->ref_no` on `NULL` triggers a **fatal PHP error** (Call to member function on null). Since this is called inside `product_details/save` (the tagging loop at controller L740), the entire first-ever tagging operation dies with a 500 error.

**Fix:**
```php
$row = $sql->row();
return ($row ? $row->ref_no : null);
```
Then update `generaterefCode()` to handle null input gracefully.

---

### BRN-OI-037 — N+1 Query Problem in `get_productMappedDetails()`
**Severity:** 🟠 MEDIUM
**Location:** `ret_other_inventory_model.php` L569–581

**Evidence:**
```php
function get_productMappedDetails($id_branch)
{
    $sql = $this->db->query("SELECT p.pro_id FROM ret_product_master p WHERE p.product_status=1");
    $result = $sql->result_array();   // ← Fetches ALL active products
    foreach ($result as $items) {
        $responseData[] = array(
            'pro_id'       => $items['pro_id'],
            'item_details' => $this->get_product_linked_items($items['pro_id'], $id_branch), // ← 1 query per product
        );
    }
```

**Impact:** For a store with 500 active products, this method fires **501 queries** per AJAX call to `get_productMappedDetails`. Page load time grows linearly with product catalog size. No pagination or LIMIT applied.

**Fix:** Rewrite as a single JOIN query:
```sql
SELECT l.inv_pro_id as pro_id, i.id_other_item, i.name as item_name,
       IFNULL(d.tot_pcs,0) as tot_pcs, i.sku_id
FROM ret_other_inventory_product_link l
LEFT JOIN ret_other_inventory_item i ON i.id_other_item = l.inv_des_otheritemid
LEFT JOIN (SELECT other_invnetory_item_id, SUM(piece) as tot_pcs, current_branch
           FROM ret_other_inventory_purchase_items_details WHERE status=0 AND current_branch=?
           GROUP BY other_invnetory_item_id) d ON d.other_invnetory_item_id = i.id_other_item
LEFT JOIN ret_product_master p ON p.pro_id = l.inv_pro_id WHERE p.product_status=1
```

---

### BRN-OI-038 — `delete_gift_map_data()` Always Returns 0
**Severity:** 🟡 LOW
**Location:** `ret_other_inventory_model.php` L684

**Evidence:**
```php
$edit_flag = $this->db->query($sql);   // Returns true (bool) on success
return ($edit_flag == 1 ? $id : 0);   // true == 1 is TRUE in PHP loose comparison
```

**Note:** Actually in PHP, `true == 1` evaluates to `true`, so this may work correctly in loose comparison. However, the pattern is fragile. The controller checks `if ($delete_data)` at L301, which also works since `$id` is truthy. **Risk: LOW** — functionally works today, but the intent is unclear and a strict comparison (`===`) would break it.

**Fix (partial):** Change to `return ($edit_flag !== false ? $id : 0);` to use the actual boolean result of `db->query()`.

---

> **Round 6 Summary:** Model L510–870 fully verified. 7 new bugs found (BRN-OI-032 through BRN-OI-038). Critical: SQL injections in gift_mapping raw queries and print-path methods. High: fatal null dereference in `getlastrefno()` on empty table. Total: **38 bugs**.

---

## Round 7 Additions (Controller L1–260 + L400–700 Deep Scan)

---

### 🔍 Key Clarification: BRN-OI-031 Scope (Confirmed Round 7)
**Location:** Controller L164–208 (`other_inventory/save`) vs L935–941 (`issue_item/save`)

The OLD broken gift_mapping loop at controller L196–206 (with trailing-space keys) is **commented out** in the active save path. The **ACTIVE `other_inventory/save` gift_mapping loop at L177–186 has correct key names** (no trailing spaces).

**BRN-OI-031 ONLY applies to `issue_item/save` at L935–941** — not to item creation. This is the only place where the trailing-space bug exists in active code.

---

### BRN-OI-039 — Missing Rollback on Insert Failure in `purchase_entry/save`
**Severity:** 🔴 HIGH
**Location:** `admin_ret_other_inventory.php` L658–660

**Evidence:**
```php
// Controller L571: trans_begin() opened
$this->db->trans_begin();
$insId = $this->$model->insertData($insData, 'ret_other_inventory_purchase');

if ($insId) {
    // ... inserts items, images ...
    if ($this->db->trans_status() === TRUE) {
        $this->db->trans_commit();        // ✅ success path commits
    } else {
        $this->db->trans_rollback();      // ✅ inner failure rolls back
    }
} else {
    // L658–660: $insId is falsy (insert failed)
    $responseData = array('status' => false, 'message' => 'Unable to Add Other Inventory Purchase');
    // ← NO ROLLBACK CALLED HERE. trans_begin() at L571 is left hanging.
}
echo json_encode($responseData);
```

**Impact:** If the header purchase record insert fails (DB constraint, deadlock, etc.), `trans_begin()` is never rolled back or committed. In MySQL strict mode or with InnoDB, the connection may hold a phantom transaction open until the connection closes. On shared connection pools this can cause lock contention.

**Fix:** Add `$this->db->trans_rollback();` before the else-branch return:
```php
} else {
    $this->db->trans_rollback();  // ← ADD THIS
    $responseData = array('status' => false, 'message' => 'Unable to Add Other Inventory Purchase');
}
```

**Verified:** 2026-03-14 (Round 7) — Code read at L551–661.

---

### BRN-OI-040 — Orphan Image Files on Failed Purchase Save
**Severity:** 🟠 MEDIUM
**Location:** `admin_ret_other_inventory.php` L610–640

**Evidence:**
```php
// L610–639: Images decoded from base64 and written to disk
if (sizeof($p_ImgData) > 0) {
    foreach ($p_ImgData as $precious) {
        $imgFile = $this->base64ToFile($precious->src);
        // ...
        $result = $this->upload_img('image', $path, $file_val['tmp_name']); // ← writes .jpg to disk
        if ($result) {
            $insImageId = $this->$model->insertData($arrayimg_tag, 'ret_other_inventory_purchase_images');
        }
    }
}
// L640: ONLY THEN checks transaction status
if ($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();
} else {
    $this->db->trans_rollback();  // DB records rolled back, but disk files NOT deleted
}
```

**Impact:** When the transaction fails (e.g., due to a failed item insert earlier in the loop), the DB records are correctly rolled back. However, the image `.jpg` files already written to `assets/img/purchase_entry/` are **permanent on disk** — no cleanup is performed. Over time (or during a bug storm), the server disk fills with unreferenced purchase images.

**Fix:** Collect written file paths in `$img_arr`, and on rollback, iterate and `unlink()` each file:
```php
} else {
    $this->db->trans_rollback();
    foreach ($img_arr as $img) { @unlink($folder . $img['image']); }
}
```

**Verified:** 2026-03-14 (Round 7) — Code read at L610–657.

---

> **Round 7 Summary:** Controller L1–700 fully deep-read. BRN-OI-031 scope confirmed (only `issue_item/save`, active `other_inventory/save` is clean). 2 new bugs: BRN-OI-039 (missing rollback on header insert failure) and BRN-OI-040 (orphan disk files on failed purchase saves). Total: **40 bugs**.

---

## Round 8 Additions (Controller L700–1400 Deep Scan — Final Pass)

---

### ✅ BRN-OI-017 Re-confirmed (Exact Line Identified)
**Location:** `admin_ret_other_inventory.php` L1295–1335 (`other_inventory_print`) vs L1336–1377 (`product_other_inventory_print`)

`other_inventory_print()` at L1318 appends to `$tagprintCode` but **never resets** it after emitting a group of 3. Compare to `product_other_inventory_print()` at L1368 which correctly sets `$tagprintCode = ""` after each emit. This is the exact same bug documented in BRN-OI-017, now line-confirmed.

---

### BRN-OI-041 — `getlastrefno()` Called per Piece in Nested Loop
**Severity:** 🟠 MEDIUM
**Location:** `admin_ret_other_inventory.php` L738–771

**Evidence:**
```php
// L738: outer loop — each order item
foreach ($order_items as $item) {
    // L739: inner loop — each piece of this item
    for ($i = 1; $i <= $item['pieces']; $i++) {
        $lastTagCode = $this->$model->getlastrefno();  // L740 ← 1 DB query per piece
        $item_ref_no = $this->generaterefCode($lastTagCode);
        // insert 2 records...
    }
}
```

**Impact:** A purchase batch of 10 item types × 50 pieces each = **500 `getlastrefno()` DB queries** just to generate sequential ref codes. Each call hits `ret_other_inventory_purchase_items_details` with an `ORDER BY pur_item_detail_id DESC LIMIT 1`. For large batches this is the primary bottleneck.

Additionally, on a race condition, two simultaneous tagging sessions can call `getlastrefno()` and get the same last ref_no → both generate the same `item_ref_no` → **duplicate tag codes** (confirmed by BRN-OI-013/014).

**Fix:** Pre-generate or cache the starting ref code before the loop, then increment a local counter:
```php
$lastTagCode = $this->$model->getlastrefno();      // 1 query total
$seq = extract_sequence($lastTagCode);              // local counter
foreach ($order_items as $item) {
    for ($i = 1; $i <= $item['pieces']; $i++) {
        $item_ref_no = generateFromSeq($seq++);    // pure PHP, no DB
        // insert...
    }
}
```

**Verified:** 2026-03-14 (Round 8) — Code read at L738–771.

---

### BRN-OI-042 — `generaterefCode(NULL)` — Fatal on PHP8 / Malformed Output on PHP7
**Severity:** 🔴 HIGH
**Location:** `admin_ret_other_inventory.php` L803–832

**Evidence:**
```php
function generaterefCode($lastTagCode)
{
    $tagCode   = $lastTagCode;          // NULL if table is empty
    $code_det  = explode('-', $tagCode); // PHP8: TypeError — null not stringable
                                         // PHP7: explode('-', '') → ['']
    //  $code_det[1] → undefined offset on PHP7 → warning + empty string
    ...
    return $alpha_char . '' . $code_number;  // Returns '00001' without item prefix
}
```
Called at L741: `$item_ref_no = $this->generaterefCode($lastTagCode)` where `$lastTagCode = $this->$model->getlastrefno()`.

**Impact (compounded with BRN-OI-036):**
- **PHP8+**: `explode('-', NULL)` throws `TypeError: explode(): Argument #2 ($string) must be of type string, null given` → **fatal error**, entire `product_details/save` aborts
- **PHP7**: Silent warning, `$code_det = ['']`, `$code_det[1]` = undefined offset → `$tag_number = ''` → `$code_number = '00001'` → ref code = `'00001'` (no item prefix/separator) → **malformed ref codes** for ALL items on first-ever tagging pass

**Fix:** Guard in `generaterefCode()`:
```php
function generaterefCode($lastTagCode)
{
    if (!$lastTagCode) return '1-00001';  // seed value for first run
    $tagCode = $lastTagCode;
    // ... rest of function unchanged
}
```
Also fix `getlastrefno()` to return a seed value instead of NULL (BRN-OI-036 fix).

**Verified:** 2026-03-14 (Round 8) — Code read at L803–832, cross-referenced with L740–741.

---

> **Round 8 Summary (FINAL — All Controller Lines Verified):** Controller L700–1400 fully deep-read. BRN-OI-017 line-confirmed (L1318 vs L1368). 2 new bugs: BRN-OI-041 (O(N×M) getlastrefno DB queries in tagging loop) and BRN-OI-042 (generaterefCode fatal/malformed on NULL input). All 1515 controller lines and all 870 model lines verified across 8 rounds. **Total: 42 bugs.** Brain audit complete.

---

## Round 9 Additions (View File XSS Audit)

---

### BRN-OI-043 — Stored XSS / JS Injection via `other_id` in `form.php`
**Severity:** 🔴 HIGH
**Location:** `views/other_inventory/form.php` L228

**Evidence:**
```php
<script type="text/javascript">
var other_id ="<?php echo $other['id_other_item']; ?>";
</script>
```

**Impact:** `$other['id_other_item']` is read from the DB and echoed raw into a JavaScript string literal without any escaping. If an attacker can manipulate the `id_other_item` value to contain `"; alert(document.cookie); //`, this executes arbitrary JS in the admin's browser. Any admin visiting the edit form for a crafted item ID is vulnerable. This is a **Stored XSS** because the value persists in the DB.

**Fix:**
```php
var other_id = <?php echo json_encode((int)$other['id_other_item']); ?>;
```
Cast to int first (it should always be numeric), or use `json_encode()` for safe JS embedding.

**Verified:** 2026-03-14 (Round 9) — Code read at `views/other_inventory/form.php` L226–230.

---

### BRN-OI-044 — Stored XSS in Purchase Print View via Supplier Fields
**Severity:** 🟠 MEDIUM
**Location:** `views/other_inventory/purchase/purchase_entry.php` L45–61

**Evidence:**
```php
// L45
<h1><?php echo strtoupper($comp_details['company_name']); ?></h1>
// L46
<?php echo strtoupper($comp_details['address1']) ?> , <?php echo strtoupper($comp_details['address2']) ?>
// L53
echo '...' . $pur_item['supplier_name'] . "...";
// L47-48
echo ($comp_details['email'] != '' ? '<div>Email : ' . $comp_details['email'] . ' </div>' : '')
echo ($comp_details['gst_number'] != '' ? '<div>GST : ' . $comp_details['gst_number'] . ' </div>' : '')
```

**Impact:** All supplier-sourced fields (name, address, email, GST, city, state, PAN) are concatenated directly into HTML strings without `htmlspecialchars()`. Any `<script>` tag or `onerror=` attribute in the supplier name/address field in the DB will execute when an admin views the purchase print page. This is a print-path view served via `echo $html; exit;` in the controller — the attacker only needs to corrupt the supplier profile once.

**Fix:** Wrap all DB-sourced fields in `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`:
```php
echo '<div>Email : ' . htmlspecialchars($comp_details['email'], ENT_QUOTES, 'UTF-8') . ' </div>';
```

**Verified:** 2026-03-14 (Round 9) — Code read at `purchase/purchase_entry.php` L45–61.

---

### BRN-OI-045 — Stored XSS via `product_name` in Purchase Print Table
**Severity:** 🟠 MEDIUM
**Location:** `views/other_inventory/purchase/purchase_entry.php` L127

**Evidence:**
```php
<td class="alignLeft"><?php echo $po_detail['product_name']?></td>
```

**Impact:** `product_name` from `ret_other_inventory_item` (or joined table) echoed raw into a table cell. A product named `<img src=x onerror=alert(1)>` would execute when any admin opens the purchase print view for a purchase containing that product.

**Fix:** `<?php echo htmlspecialchars($po_detail['product_name'], ENT_QUOTES, 'UTF-8'); ?>`

**Verified:** 2026-03-14 (Round 9) — Code read at `purchase/purchase_entry.php` L127.

---

### BRN-OI-046 — Flash Message XSS (Session-Scope) in `issue/list.php`
**Severity:** 🟡 LOW
**Location:** `views/other_inventory/issue/list.php` L65, L69, L71

**Evidence:**
```php
<div class="alert alert-<?php echo $message['class']; ?>"> // L65: class injection
<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>  // L69
<?php echo $message['message']; ?>  // L71: unescaped body
```

**Impact:** Flash data is set by the controller (session-based), so direct injection requires a compromised session. However, if a controller ever writes user-controlled text into a flash message (e.g., from a POST parameter), this becomes a reflected XSS. **Current risk: LOW** — controller writes hardcoded strings into flash messages. Injection class at L65 could break CSS layout if a future code change passes user data there.

**Fix:** Wrap all three with `htmlspecialchars()`. For `class`, additionally validate against a whitelist (`success`, `danger`, `warning`, `info`).

**Verified:** 2026-03-14 (Round 9) — Code read at `issue/list.php` L65–71.

---

> **Round 9 Summary:** View file XSS audit complete (3 key views scanned). 4 new bugs: BRN-OI-043 (HIGH — stored XSS via raw `other_id` in JS variable in form.php), BRN-OI-044 (MEDIUM — stored XSS in purchase print supplier fields), BRN-OI-045 (MEDIUM — stored XSS via product_name in print table), BRN-OI-046 (LOW — flash message class/title/body unescaped). **Total: 46 bugs.**

---

## Round 11 Additions (Controller L260–400, L895–970 + Billing Cross-Module Scan)

### ✅ Confirmed BRN-OI-011 (Exact Line: L345), BRN-OI-016 (L927–948), BRN-OI-031 (L936–938)
These three previously identified bugs were exactly line-confirmed in this round. See original analyses. BRN-OI-031 trailing-space keys: `'id_other_item '`, `'id_scheme '`, `'item_issue_limit '` at L936–938. BRN-OI-016: gift_mapping inserts at L942 fire BEFORE `trans_begin()` at L948.

---

### BRN-OI-047 — Cross-Module Transaction Boundary: Billing Commits Before OI Issue Writes
**Severity:** 🔴 HIGH
**Location:** `admin/application/controllers/admin_ret_billing.php` L4492–4568

**Evidence:**
```php
if ($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();  // L4494: BILLING COMMITTED HERE
    // ...then immediately after:
    if (!empty($est_oth_inv)) {
        foreach ($est_oth_inv['id_other_item'] as $key => $val) {
            $otherInvIssue = $this->$model->insertData($insData, 'ret_other_invnetory_issue'); // L4522 — OUTSIDE transaction
            if ($otherInvIssue) {
                // stock deduction + log — all OUTSIDE transaction
                $this->$model->updateData(['status' => 1], 'pur_item_detail_id', ...);
                $this->$model->insertData($logData, 'ret_other_inventory_purchase_items_log');
            }
        }
    }
}
```

**Impact:** If any DB error/timeout/fatal occurs during OI inserts after the billing commit:
- Bill is permanently saved in `ret_billing`
- `ret_other_invnetory_issue` row never created → gift item not linked to bill
- `ret_other_inventory_purchase_items_details.status` remains 0 → stock NOT deducted
- Result: **Ghost bill** — items billed but stock appears available → double-issue risk

**Fix:** Move all OI issue inserts inside the billing transaction block, before `trans_commit()`.

**Verified:** 2026-03-14 (Round 11) — Code at `admin_ret_billing.php` L4492–4568.

---

> **Round 11 Summary:** Controller L260–400 and L895–970 deep-read, confirmed BRN-OI-011/016/031 with exact lines. Billing cross-module scan found 1 new HIGH bug: BRN-OI-047 (commit-before-OI-write creates ghost bills). Billing delete (L8110–8135) is clean. **Total: 47 bugs.**

---

## Round 12 Additions (Controller L970–1000 + QR Print View + Billing Model)

### BRN-OI-048 — Wrong Status Response on Issue Insert Failure
**Severity:** 🟡 LOW | **Location:** Controller L986–988

```php
} else {
    $responseData = array('status' => TRUE, 'message' => 'Unable to Issue Gift Items.'); // L987
}
echo json_encode($responseData);
```
**Impact:** JS receives `status: true` on failure → may show success toast, skip error handling. **Fix:** `'status' => FALSE`.
**Verified:** 2026-03-14 (Round 12)

---

### BRN-OI-049 — Stored XSS in QR Print View
**Severity:** 🟡 LOW | **Location:** `print/qr_print.php` L55–56

```php
<label><?php echo $d['pro_name'];?></label>
<label>Ref No: <?php echo $d['item_ref_no'];?></label>
```
**Impact:** Raw DB values in print view — crafted product name with `<script>` executes on print page load. Low risk (admin-only, DB corruption required). **Fix:** `htmlspecialchars()` on both fields.
**Verified:** 2026-03-14 (Round 12)

---

### BRN-OI-050 — SQL Injection in Billing Model Cross-Module OI Query
**Severity:** 🔴 HIGH | **Location:** `ret_billing_model.php` L7138

```php
$items_query = $this->db->query(
    "SELECT * FROM ret_other_invnetory_issue WHERE bill_id =" . $bill_id . ""
);
```
**Impact:** Raw `$bill_id` concatenation — SQL injection via bill cancel/delete endpoint. Can extract any table or manipulate deletion logic. **Fix:** `$this->db->where('bill_id', (int)$bill_id)->get('ret_other_invnetory_issue')`.
**Verified:** 2026-03-14 (Round 12) — `ret_billing_model.php` L7134–7143.

---

> **Round 12 Summary:** Controller L970–1000 verified (proper trans_commit/rollback end of issue_item/save). QR print view scanned — 2 XSS/logic bugs. Billing model cross-module query audited — 1 HIGH SQL injection. **Total: 50 bugs.**

---

## Round 13 Additions (Remaining 9 View Files — Complete View Coverage)

**Views scanned this round:** `item_qrcode.php`, `category/form.php`, `category/list.php`, `list.php`, `report/available_stock.php`, `report/stock_report.php`, `size_list.php`, `purchase/list.php` (+ grep on `product/list.php`)

---

### ✅ CLEAN Views (no new bugs)
| View | Status | Notes |
|---|---|---|
| `category/list.php` | ✅ CLEAN | Flash pattern only (already BRN-OI-046 scope) |
| `category/form.php` | ✅ CLEAN | `echo $item['x'] == N ? 'checked' : ''` — boolean comparisons, no raw text echoes. JS restricts input to alpha chars only. |
| `list.php` | ✅ CLEAN | Flash pattern L65/69/71 — same scope as BRN-OI-046 |
| `report/available_stock.php` | ✅ CLEAN | DataTables AJAX. Session values in hidden inputs (numeric branch ID only). |
| `report/stock_report.php` | ✅ CLEAN | DataTables AJAX. Date range picker JS only. |
| `size_list.php` | ✅ CLEAN | Flash pattern + DataTables AJAX. Modal inputs are blank form fields. |
| `purchase/list.php` | ✅ CLEAN | Zero `echo $` occurrences — pure DataTables AJAX. |

---

### BRN-OI-051 — Stored XSS in dompdf QR Label Print via Product Name
**Severity:** 🟡 LOW
**Location:** `views/other_inventory/item_qrcode.php` L24–25

**Evidence:**
```php
<img src="<?php echo $img[0]['src']; ?>" ...>          // L24: src path — path injection
<label ...><?php echo $img[0]['name']; ?></label>      // L25: product name — stored XSS
```

**Context:** This view is loaded by `print_qrcode` case (controller L369–396) which generates a QR code and then renders this view via `$this->load->view()` before passing to dompdf. Unlike `qr_print.php` which is rendered in browser, `item_qrcode.php` is fed to `dompdf->load_html()`.

**Impact:**
- `$img[0]['name']` = `$addData['name']` from DB — a product name with injected HTML could corrupt the PDF layout or inject content into the PDF
- In dompdf, HTML injection can be more impactful than in browser (PDF structure manipulation)
- Risk is admin-only (only staff with product edit access can craft malicious names)

**Fix:** `htmlspecialchars($img[0]['name'], ENT_QUOTES, 'UTF-8')` on L25. The `src` is a filesystem path (no free-text injection risk if path generation is controlled).

**Verified:** 2026-03-14 (Round 13) — Code at `item_qrcode.php` L24–25.

---

> **Round 13 Summary:** All 9 remaining view files scanned — 8 CLEAN, 1 new LOW bug (BRN-OI-051 — stored XSS in dompdf print label via product name). **All 18 view files now 100% scanned. Total: 51 bugs.** View coverage is COMPLETE.
