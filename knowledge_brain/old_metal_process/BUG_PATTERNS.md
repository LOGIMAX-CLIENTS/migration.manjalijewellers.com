# Bug Patterns — Old Metal Process Module

**Module**: `old_metal_process`  
**Audit Date**: 2026-03-14  
**Total Bugs Found**: 63 (R1: 18, R2: +3/−1, R3: +6, R4: +2, R5: 0, R6: 0, R7: +3, R8: +4, R9: +4, R10: +6, R11: +4, R12: +4, R13: +5, R14: +5)

---

## 🔴 CRITICAL

### BUG-OMP-001: SQL Injection — Raw String Interpolation in All Queries
- **File**: `ret_metal_process_model.php` — entire file
- **Description**: Every SQL query in the model uses direct PHP variable concatenation without parameterized queries or escaping. Examples: `WHERE id_karigar=".$data['id_karigar']`, `WHERE id_old_metal_process=".$id_old_metal_process`. Any value passed from POST without sanitization can inject SQL.
- **Attack Surface**: `get_testing_receipt_details()`, `get_RefiningReceiptDetails()`, `get_PolishingReceiptDetails()`, `get_karrigar_state()` (raw `$_POST['karigar']`), etc.
- **Fix**: Use CI's query bindings: `$this->db->query("SELECT ... WHERE id=?", [$id])` or `$this->db->where()` Active Record.

---

### BUG-OMP-002 UPDATE: Exact Controller Location Confirmed (R7)
- **Controller**: `admin_ret_metal_process.php` function `get_chg_tax_type()` line 1773
- **Buggy code**:
  ```php
  $company_state = $data['comp_details'][0]['id_company'];  // ❌ id_company, not id_state
  ```
- **Fix** (R7 confirmed):
  ```php
  $company_state = $data['comp_details'][0]['id_state'];  // ✅ getCompanyDetails("") returns id_state
  ```
- `getCompanyDetails("")` returns a row_array, not result_array, so use `$data['comp_details']['id_state']` (no `[0]` index needed)
- **Priority**: P0 — Security

---

### BUG-OMP-002: Wrong Field Used for Company State in Tax Type Check
- **File**: `admin_ret_metal_process.php` → `get_chg_tax_type()` (line ~1773)
- **Code**:
  ```php
  $company_state = $data['comp_details'][0]['id_company'];  // ❌ WRONG
  if ($karigar_state == $company_state) { $tax_type = 1; }
  ```
- **Expected**: Should compare `id_state` of company to `id_state` of karigar
- **Impact**: All GST tax type computations may be wrong (IGST charged when same state, or CGST+SGST charged when different state)
- **Fix**: Replace `['id_company']` with the correct state field from `getCompanyDetails()` response. Inspect model to find correct column name (likely `id_state` or `state_id`).
- **Priority**: P1 — Financial/Compliance

---

### BUG-OMP-003: Net Banking Payment Records Cash Amount Instead of NB Amount
- **File**: `admin_ret_metal_process.php` → `metal_process('save')` (line ~1494)
- **Code**:
  ```php
  if ($receipt_payment['net_banking_amount'] > 0) {
      $payData = [
          ...
          'payment_amount' => $receipt_payment['cash_amount'],  // ❌ WRONG
      ];
  }
  ```
- **Description**: When net banking payment is saved, it records the `cash_amount` field value instead of the `net_banking_amount` field value.
- **Impact**: Payment records are wrong; double-counting if both are > 0.
- **Fix**: Change `$receipt_payment['cash_amount']` to `$receipt_payment['net_banking_amount']` in the NB payment block.
- **Priority**: P1 — Financial accuracy

---

### BUG-OMP-004: Double-Query Bug in `check_purity_stock_details()`
- **File**: `ret_metal_process_model.php` lines ~1113–1128
- **Code**:
  ```php
  $sql = $this->db->query("SELECT...");  // $sql is result object
  $res = $this->db->query($sql);         // ❌ Runs string representation of object
  if ($res->num_rows() > 0) { ... }
  ```
- **Description**: `$sql` is already a result object; passing it again to `query()` either errors or executes a nonsensical string.
- **Impact**: `check_purity_stock_details()` always returns FALSE (stock check fails silently), meaning stock is never updated correctly.
- **Fix**: Remove the re-query line; use `$sql` directly: `if ($sql->num_rows() > 0)`.
- **Priority**: P1 — Data integrity

---

## 🟠 HIGH

### BUG-OMP-005: Race Condition in Process Number Generation
- **File**: `ret_metal_process_model.php` → `generate_process_number()` (lines ~933–954)
- **Description**: `get_last_process_number()` reads the last number, increments in PHP, then inserts. No DB-level lock or transaction wrapping. Two concurrent requests may get the same number.
- **Impact**: Duplicate `process_no` values possible; unique constraint violation or silent data corruption.
- **Fix**: Use a dedicated sequence table with `FOR UPDATE` locking, or use `LAST_INSERT_ID()` after an atomic increment.
- **Priority**: P2

---

### BUG-OMP-006: No Server-Side Validation of Weight Limits
- **File**: `admin_ret_metal_process.php` → `metal_process('save')`
- **Description**: The controller does not validate that issued weight ≤ available pocket balance. Only the JS frontend performs this check (which can be bypassed).
- **Impact**: Over-issue of metals possible; negative pocket balances; downstream stock errors.
- **Fix**: Add server-side comparisons using `get_pocket_details()` or direct DB query before inserting issue records.
- **Priority**: P2

---

### BUG-OMP-007: Pocket Never Marked as Closed
- **File**: `ret_metal_process_model.php` — pocket queries filter `status = 0`
- **Description**: After all items in a pocket are issued and processed, the `ret_old_metal_pocket.status` is never set to `1` (closed). Pockets appear available indefinitely.
- **Impact**: Operators can repeatedly issue from the same pocket after it's exhausted; HAVING clause `issue_nwt < net_wt` is the only guard (unreliable after rounding).
- **Fix**: After full melting/polishing issue, update pocket status to 1 if `issue_nwt >= net_wt`.
- **Priority**: P2

---

### BUG-OMP-008: `melting_status` State Transitions Not Protected
- **File**: `admin_ret_metal_process.php` → `metal_process('save')`
- **Description**: No check that the current `melting_status` is a prerequisite before advancing to the next state. E.g., Testing Issue can be saved even if Melting Receipt was never done.
- **Impact**: Orphaned records in intermediate tables; incorrect status values.
- **Fix**: Add status pre-checks (e.g., `WHERE melting_status=1` in Testing Issue save).
- **Priority**: P2

---

### BUG-OMP-009: Transaction Failure Silently Returns Data
- **File**: `admin_ret_metal_process.php` → `metal_process('save')` (line ~917–935, ~1452–1468)
- **Description**: The `if ($this->db->trans_status() === TRUE)` check only runs at the very end. If some insertions fail mid-loop (e.g., on the 3rd non-tag item), earlier records may have rolled back but `$responseData` is not set to FALSE before the check.
- **Impact**: In some edge cases, partial operations may succeed before rollback, and the response is ambiguous.
- **Fix**: Initialize `$responseData = ['status' => FALSE]` at function entry; only set to TRUE inside the trans_status success block.
- **Priority**: P2

---

## 🟡 MEDIUM

### BUG-OMP-010: Duplicate Event Handler Registration in JS
- **File**: `ret_metal_process.js` lines 1155 and 1272
- **Description**: `$('#select_metal_process').on('change', ...)` is bound twice with identical logic. On change, the handler fires twice, potentially causing double row insertions in the UI table.
- **Fix**: Remove the duplicate handler (line 1272 block).
- **Priority**: P3

---

### BUG-OMP-011: `updateStoneItemData` Wrong WHERE Clause Fields
- **File**: `ret_metal_process_model.php` → `updateStoneItemData()` (line ~1311)
- **Code**:
  ```php
  "...WHERE id_ret_category=".$data['id_ret_category']." and id_branch=".$data['id_product']." and id_product=".$data['id_product'].""
  ```
- **Description**: `id_branch=` is set to `$data['id_product']` instead of `$data['id_branch']` — copy-paste error in the WHERE clause.
- **Impact**: Stock summary updates for stone items go to wrong records.
- **Fix**: Change `id_branch=`.$data['id_product'] to `id_branch=`.$data['id_branch'].
- **Priority**: P2

---

### BUG-OMP-012: Polishing Receipt Report — Commented Code vs Active Code Mismatch
- **File**: `ret_metal_process_model.php` → `get_metal_process_reports()` lines ~1449–1478
- **Description**: The original query used `ret_lot_inwards_detail` joined through `ret_lot_inwards`; this is now commented out. The replacement query uses `ret_old_metal_polishing_recd_details` but this table may not be populated consistently in all cases.
- **Impact**: Polishing Receipt report may show incorrect or missing data.
- **Fix**: Verify which table is the authoritative source for polishing receipt data and use consistently.
- **Priority**: P3

---

### BUG-OMP-013: `get_karigar_state()` Uses Raw `$_POST` Without Sanitization
- **File**: `ret_metal_process_model.php` lines ~1841–1848
- **Code**:
  ```php
  "Select id_state from ret_karigar where id_karigar =".$_POST['karigar']
  ```
- **Description**: Directly uses `$_POST` in SQL without any sanitization — not even `$this->input->post()`.
- **Impact**: SQL injection vulnerability.
- **Fix**: Use `$this->db->where('id_karigar', $this->input->post('karigar'))`.
- **Priority**: P1 duplicates OMP-001 but specifically flagged for this direct `$_POST` usage.

---

### ~~BUG-OMP-014~~: RETRACTED — `get_Active_Refining_details()` Does Exist
- **Status**: ❌ RETRACTED (R2 verification)
- **Reason**: Function confirmed at model line 133. The R1 analysis was incorrect.

---

### BUG-OMP-015: `updatePurItemData` Updates `net_wt` with `gross_wt` Value
- **File**: `ret_metal_process_model.php` → `updatePurItemData()` (line ~1261)
- **Code**:
  ```php
  "...SET gross_wt=(gross_wt+$data['gross_wt']), net_wt=(net_wt+$data['gross_wt'])..."
  ```
- **Description**: Both `gross_wt` and `net_wt` are updated using the `gross_wt` value only; `net_wt` is not separately tracked.
- **Impact**: Stock summary has incorrect `net_wt` values after purchase item stock adjustments.
- **Priority**: P3

---

## 🟢 LOW / INFO

### BUG-OMP-016: Missing `no_of_piece` in Non-Tag Log Insert (Polishing — Existing NT Item)
- **File**: Controller → Polishing Receipt, existing NT item branch (line ~1372–1386)
- **Description**: `$non_tag_data` array in the existing-item-update branch is missing `'no_of_piece'` key, but the new-item insert branch includes it. Inconsistency in log records.
- **Priority**: P4

---

### BUG-OMP-017: `id_branch` Variable Used Before Assignment in Polishing Receipt
- **File**: `admin_ret_metal_process.php` → Polishing Receipt (line ~1315)
- **Code**: `'created_branch' => $id_branch` in `$Lotdata` array — `$id_branch` is only set by session/pocket at a higher scope but not explicitly set in this local loop; may reuse previous loop's value or be uninitialized.
- **Priority**: P3

---

### BUG-OMP-018: Typo in PDF Generation — Paper Orientation Typo
- **File**: `admin_ret_metal_process.php` → `process_acknowladgement()` (line ~1578)
- **Code**: `$dompdf->set_paper("a4", "portriat")` — "portriat" should be "portrait"
- **Impact**: DomPDF may default to landscape or throw a warning.
- **Priority**: P4

---

## 🔴 CRITICAL (R2 — New)

### BUG-OMP-019: Payment Tab Entirely Commented Out in form.php
- **File**: `admin/application/views/ret_metal_process/metal_process/form.php` lines 590–631
- **Description**: The entire payment tab (`#payment_details`) div including both Cash and Net Banking payment input fields is wrapped in a PHP/HTML comment (`<!-- ... -->`). The tab is rendered but the input form fields never appear. The JS payment totalling code (`calculate_receipt_payment()`) still runs, but with no fields present, no POST data is ever submitted for payments.
- **Impact**: **No payment data is ever collected from the UI.** The `ret_old_metal_process_payment` insert code in the controller is dead code — it can never fire from normal UI usage. All receipt transactions have zero payment records.
- **Fix**: Un-comment the payment form block in `form.php` lines 590–631 (remove `<!-- ... -->` wrapper). Then also fix OMP-003 for the NB amount bug.
- **Priority**: P0 — Feature broken

---

## 🟡 MEDIUM (R2 — New)

### BUG-OMP-020: Testing Receipt Charges Bypass JS Validation (Class Mismatch)
- **File**: `ret_metal_process.js` → `validateTestingReceiptRow()` (line ~2838)
- **Code**:
  ```javascript
  if(... $(this).find('.receipt_charges').val()=='' )  // looks for .receipt_charges
  // BUT field in HTML has:  class="form-control testing_receipt_charges"
  ```
- **Description**: the validator searches for class `.receipt_charges` but the actual field has class `.testing_receipt_charges`. The selector finds nothing, returns `''` for the missing-class element, and `row_validate` is never set to false for missing charges.
- **Impact**: Testing receipt can be saved with zero charges, bypassing the UI requirement.
- **Fix**: Change `.receipt_charges` to `.testing_receipt_charges` in `validateTestingReceiptRow()`.
- **Priority**: P3

---

### BUG-OMP-021: Refining Receipt Charges Bypass JS Validation (Class Mismatch)
- **File**: `ret_metal_process.js` → `validateRefiningReceiptRow()` (line ~2851)
- **Code**:
  ```javascript
  if(... $(this).find('.receipt_charges').val()=='' )  // looks for .receipt_charges
  // BUT field in HTML has:  class="form-control refining_receipt_charges"
  ```
- **Description**: Same class-name mismatch as OMP-020 but in the refining receipt validator.
- **Impact**: Refining receipt charges silently skipped in validation.
- **Fix**: Change `.receipt_charges` to `.refining_receipt_charges` in `validateRefiningReceiptRow()`.
- **Priority**: P3

---

## 🟡 MEDIUM (R3 — New)

### BUG-OMP-022: `#category_row` DOM ID Shared by Three Modals
- **File**: `admin/application/views/ret_metal_process/metal_process/form.php`
- **Description**: All three receipt category modals (melting, refining, polishing) use `id="category_row"` for their inner `<table>`. jQuery returns only the FIRST match when `$('#category_row tbody')` is called, causing category data from the refining/polishing modals to be appended to the melting modal's table instead.
- **Impact**: Refining and polishing receipt categories may be silently mis-classified or lost.
- **Fix**: Rename each table ID to `id="melting_category_row"`, `id="refining_category_row"`, `id="polishing_category_row"` and update all JS references.
- **Priority**: P2

---

### BUG-OMP-023: `get_refining_process_details()` Has No WHERE Clause
- **File**: `ret_metal_process_model.php` lines 1538–1545
- **Code**: Query has no `WHERE p.id_old_metal_process = $id_old_metal_process` — returns ALL processes
- **Impact**: Detailed process report footer shows aggregated totals from ALL refining records, not just the requested process. `row_array()` silently returns row #1.
- **Fix**: Add `WHERE p.id_old_metal_process = $id_old_metal_process` to the query.
- **Priority**: P3

---

### BUG-OMP-024: `get_pocket_details()` — `$sales_item_details` Uninitialized for trans_type 2 & 3
- **File**: `ret_metal_process_model.php` lines 831–845
- **Code**:
  ```php
  } else if($items['trans_type']==2) {
      $item_details = $this->get_melting_tag_details(...);
      // $sales_item_details — NEVER SET — PHP warning follows
  }
  if(sizeof($item_details) > 0 || sizeof($sales_item_details) > 0) // uses uninitialized var
  ```
- **Impact**: Tagged and non-tagged pockets may be invisible in the pocket dropdown if their tag list is empty, even if unissued items exist. PHP warning logged per request.
- **Fix**: Initialize `$sales_item_details = []` before the if/elseif chain.
- **Priority**: P3

---

### BUG-OMP-025: `validateTestingIssueRow()` Queries Wrong Table
- **File**: `ret_metal_process.js` lines 2877–2884
- **Code**:
  ```javascript
  function validateTestingIssueRow(){
      row_validate = false;
      $('#testing_receipt > tbody > tr').each(...)  // ❌ WRONG: should be #testing_process_details
  ```
- **Impact**: Testing issue validation always fails (no rows found in `#testing_receipt` during issue flow). The UI may silently block issue save or conversely always pass validation depending on the caller's logic.
- **Fix**: Change `#testing_receipt` to `#testing_process_details` or the correct table ID used in the testing issue tab.
- **Priority**: P3

---

## 🟢 LOW (R3 — New)

### BUG-OMP-026: Report Date Filter Uses `.html()` Instead of `.val()`
- **File**: `ret_metal_process.js` line 4128
- **Code**:
  ```javascript
  data: {'from_date': $('#rpt_payments1').html(), 'to_date': $('#rpt_payments2').html(), ...}
  ```
- **Description**: Date pickers typically store selected dates in the element's `.val()`. Using `.html()` returns the element's inner HTML, which evaluates to `""` unless the element contains text nodes. Reports may silently ignore the date range and return all historical data.
- **Fix**: Change `.html()` to `.val()` for both `from_date` and `to_date`.
- **Priority**: P4

---

### BUG-OMP-027: Polishing Category Validator Checks Disabled Select Dropdowns
- **File**: `ret_metal_process.js` line 3702 `validatePolishingReceiptCategoryRow()`
- **Code**:
  ```javascript
  || $(this).find('.id_design').val()=='' || $(this).find('.id_design').val()==null
  || $(this).find('.id_sub_design').val()=='' || $(this).find('.id_sub_design').val()==null
  ```
- **Description**: When `is_non_tag` is NOT checked, the `.id_design` and `.id_sub_design` selects are disabled via `.prop('disabled', true)`. A disabled select returns `null` in jQuery. The validator thus always blocks saving for non-tagged items even when all real required fields are filled.
- **Impact**: Non-tagged polishing receipt categories can never be saved through the normal UI. Users would be stuck.
- **Fix**: Skip validation of `.id_design` and `.id_sub_design` when `.is_non_tag` is unchecked (value=0).
- **Priority**: P3

---

---

## 🔴 CRITICAL (R4 — New)

### BUG-OMP-028: Polishing Receipt Lot Inwards `created_branch` is NULL — Undefined Variable
- **File**: `admin_ret_metal_process.php` line 1315
- **Code**:
  ```php
  $Lotdata = array(
      ....
      'created_branch' => $id_branch,  // ❌ undefined variable (not $branchDetails['id_branch'])
      ....
  );
  ```
- **Description**: Every `ret_lot_inwards` record created during Polishing Receipt has `created_branch = NULL`. PHP converts the undefined `$id_branch` to `NULL` silently. The variable `$branchDetails['id_branch']` is available in the same scope.
- **Impact**: Polishing receipt inventory entries have no branch attribution, breaking branch-wise stock reports and potentially causing data integrity errors. This is a critical production data defect.
- **Fix**: Replace `$id_branch` with `$branchDetails['id_branch']` at line 1315.
- **Priority**: P0 — Data Integrity

---

## 🟠 HIGH (R4 — New)

### BUG-OMP-029: Refining Receipt Non-Tag Piece Count Hardcoded to 1
- **File**: `admin_ret_metal_process.php` line 1024
- **Code**:
  ```php
  $cateegoryData = array(
      ....
      'piece' => 1,   // ❌ hardcoded — should be $cat['recd_pcs']
      ....
  );
  ```
- **Description**: When saving refining receipt category details into `ret_old_metal_refining_details`, the piece count is always 1 regardless of actual received pieces. The `$cat['recd_pcs']` value from the JS modal is available but ignored.
- **Impact**: All refining receipts with more than 1 piece per category row show incorrect inventory piece counts. Reports and non-tag stock summaries are understated.
- **Fix**: Change `'piece' => 1` to `'piece' => $cat['recd_pcs']`.
- **Priority**: P2

---

## Bug Summary Table

| ID | Severity | Area | One-liner |
|---|---|---|---|
| OMP-001 | 🔴 Critical | Security | SQL injection throughout model |
| OMP-002 | 🔴 Critical | Financial | Wrong field for GST tax type comparison |
| OMP-003 | 🔴 Critical | Financial | NB payment records cash_amount |
| OMP-004 | 🔴 Critical | Data | Double-query in purity stock check |
| OMP-019 | 🔴 Critical | Feature | Payment tab entirely commented out in form.php |
| OMP-028 | 🔴 Critical | Data | Polishing receipt lot inwards created_branch = NULL |
| OMP-005 | 🟠 High | Concurrency | Race condition in process numbering |
| OMP-006 | 🟠 High | Validation | No server-side weight limit check |
| OMP-007 | 🟠 High | Lifecycle | Pocket never marked closed |
| OMP-008 | 🟠 High | State Machine | melting_status transitions unprotected |
| OMP-009 | 🟠 High | Error Handling | Transaction failure response ambiguous |
| OMP-029 | 🟠 High | Data | Refining receipt piece count hardcoded to 1 |
| OMP-010 | 🟡 Medium | JS | Duplicate event handler |
| OMP-011 | 🟡 Medium | Data | updateStoneItemData wrong field |
| OMP-012 | 🟡 Medium | Reports | Polishing receipt report inconsistency |
| OMP-013 | 🟡 Medium | Security | Direct $_POST in SQL (karigar state) |
| ~~OMP-014~~ | ~~Retracted~~ | ~~Runtime~~ | ~~get_Active_Refining_details confirmed exists~~ |
| OMP-015 | 🟡 Medium | Data | net_wt updated with gross_wt value |
| OMP-020 | 🟡 Medium | JS Validation | Testing receipt charges bypass (class mismatch) |
| OMP-021 | 🟡 Medium | JS Validation | Refining receipt charges bypass (class mismatch) |
| OMP-022 | 🟡 Medium | DOM | #category_row ID collision across 3 modals |
| OMP-023 | 🟡 Medium | Reports | get_refining_process_details missing WHERE clause |
| OMP-024 | 🟡 Medium | Data | sales_item_details uninitialized for tagged pockets |
| OMP-025 | 🟡 Medium | JS | validateTestingIssueRow queries wrong table |
| OMP-016 | 🟢 Low | Data | Missing no_of_piece in NT log |
| OMP-017 | 🟢 Low | Data | id_branch possibly uninitialized in polishing |
| OMP-018 | 🟢 Low | UI/PDF | Typo in dompdf orientation string |
| OMP-026 | 🟢 Low | Reports | Report date uses .html() not .val() |
| OMP-027 | 🟢 Low | JS Validation | Polishing non-tag validator checks disabled dropdowns |
| OMP-030 | 🟢 Low | DOM | Duplicate `id="type2"` on both pocket form radio buttons |
| OMP-031 | 🟢 Low | Auth/ACL | metal_process_receipt ajax uses wrong ACL path |
| OMP-032 | 🟢 Low | UI | Report modal title says "Delete Estimation" instead of "Delete Process" |

---

## 🟢 LOW (R7 — New)

### BUG-OMP-030: Duplicate `id="type2"` on Pocket Form Radio Buttons
- **File**: `admin/application/views/ret_metal_process/pocket/form.php` lines 61–63
- **Code**:
  ```html
  <input type="radio" name="transfer_item_type" id="type2" value="2" checked>
  <input type="radio" name="transfer_item_type" id="type2" value="3">  <!-- duplicate id -->
  ```
- **Description**: Both "Tagged" and "Non Tag" radio buttons share `id="type2"`. Any JS/label selector using `#type2` will always hit the first element. Note: "Old Metal" type radio (value=1) is commented out — only 2 types active, making the ID collision functional in this case but fragile.
- **Fix**: Assign unique IDs (`id="type_tagged"` and `id="type_nontag"`)

---

### BUG-OMP-031: `metal_process_receipt` Ajax Uses Wrong ACL Permission Path
- **File**: `admin/application/controllers/admin_ret_metal_process.php` line 1604
- **Code**:
  ```php
  $access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_purchase/list');
  ```
- **Description**: The `metal_process_receipt` list page checks access against the **purchase module's** ACL path (`admin_ret_reports/old_metal_purchase/list`) instead of its own receipt module. Users who should not have purchase report access might be blocked, or vice versa.
- **Fix**: Change to the correct receipt ACL path (e.g., `admin_ret_metal_process/metal_process_receipt/list`)

---

### BUG-OMP-032: Report Modal Title Hardcoded as "Delete Estimation"
- **File**: `admin/application/views/ret_metal_process/reports/process_report.php` line 137
- **Code**:
  ```html
  <h4 class="modal-title" id="myModalLabel">Delete Estimation</h4>
  ```
- **Description**: Copy-paste error from estimation module — the modal purports to delete a process record but the title reads "Delete Estimation". Also, the delete confirmation body says "this estimation" instead of "this process".
- **Fix**: Update modal title and body text to reference Process instead of Estimation
- **Also in**: `reports/detailed_report.php` lines 91/94, `metal_process/list.php` line 101, `pocket/list.php` line 99, `list.php` (root) line 98 — same copy-paste error in **5 files total** — see also OMP-050

---

## 🟢 LOW (R8 — New)

### BUG-OMP-033: `get_Active_Refining_details()` Ignores `$_POST` Argument
- **File**: `admin/application/models/ret_metal_process_model.php` line 133
- **Code**: `function get_Active_Refining_details()` — no parameters declared
- **Controller call**: `$data=$this->$model->get_Active_Refining_details($_POST);` (controller line 1673)
- **Description**: Controller passes `$_POST` but the model function signature takes no arguments. The function always returns ALL active refining processes, ignoring any filter parameters (date, branch, karigar) the controller might want to pass. This results in an oversized dropdown with all records instead of filtered ones.
- **Fix**: Add `$data=[]` parameter to function signature, then apply filters if `$data` is not empty

---

### BUG-OMP-034: `get_pocket_list()` Has No Branch Filter
- **File**: `admin/application/models/ret_metal_process_model.php` line 741–745
- **Code**:
  ```php
  function get_pocket_list($data){
      $sql=$this->db->query("SELECT * FROM `ret_old_metal_pocket`
          WHERE (date(date) BETWEEN '...from_date...' AND '...to_date...')");
  ```
- **Description**: The query filters only by date. There is no `id_branch` filter. In a multi-branch setup, **all branches' pockets are returned** regardless of the logged-in user's branch. This exposes pocket creation data from other branches.
- **Fix**: Add `AND id_branch=` using the session branch or `$data['id_branch']` if passed

---

### BUG-OMP-035: Report Daterangepicker Fires Wrong Function
- **File**: `admin/assets/js/ret_metal_process.js` line 203
- **Code** (inside `process_report` case daterangepicker callback):
  ```javascript
  get_pocket_list();  // ❌ wrong — should be get_metal_process_report()
  ```
- **Description**: The `process_report` page's date filter daterangepicker callback calls `get_pocket_list()` instead of the report's own search function. This means changing the date range on the report page refreshes the pocket list (which doesn't even exist on the report page) rather than re-running the process report. The report date filter is effectively broken.
- **Fix**: Change `get_pocket_list()` at JS line 203 to `get_metal_process_report()` or the correct report-fetch function name

---

### BUG-OMP-036: Multiple `console.log()` Debug Calls Left in Production JS
- **File**: `admin/assets/js/ret_metal_process.js`
- **Locations**: Lines 736, 744, 788, 796, 843, 850, 859, 873 (R8) + 973, 1093, 1614, 1854, 1855 (R9) + 2003, 2088, 2089, 3103, 3107, 3114, 3124 (R10) — **21 total debug calls**
- **Description**: 21 `console.log()` calls left in production across pocket, melting, polishing and category modal handlers. Expose internal data arrays visible to any user with DevTools open.
- **Fix**: Remove all `console.log()` calls or gate with a `DEBUG` flag

---

## 🟠 HIGH (R9 — New)

### BUG-OMP-037: Duplicate `$('#select_metal_process').on('change')` Registered Twice
- **File**: `admin/assets/js/ret_metal_process.js` lines 1155 and 1272
- **Description**: The `#select_metal_process` change handler is registered TWICE in the same JS file. When the user selects a process, both handlers fire: the `get_against_melting_Details()` function runs twice, potentially duplicating rows in the `#against_melting_receipt` table and causing double-save on subsequent submission.
- **Impact**: High — users may see duplicate melting receipt rows and data may double-post
- **Fix**: Remove the duplicate handler at line 1272 (keep only line 1155)

---

### BUG-OMP-038: Non-Tagged Pocket Table Row Appended Twice
- **File**: `admin/assets/js/ret_metal_process.js` lines 1610–1614
- **Code**:
  ```javascript
  $('#non_tagged_pocket_details tbody').append(trHtml);  // line 1610
  var ab = $('#non_tagged_pocket_details tbody').append(trHtml);  // line 1612 — DUPLICATE
  console.log(ab); // line 1614
  ```
- **Description**: The non-tagged pocket rows are appended to the table TWICE in consecutive lines. Every NT pocket item is duplicated in the UI, leading to double the pieces and weight being saved when the form submits.
- **Impact**: High — financial data (weight, pcs) doubled for all non-tagged pocket entries
- **Fix**: Remove the duplicate append at line 1612 and the console.log at 1614

---

## 🔴 CRITICAL (R9 — New)

### BUG-OMP-039: Duplicate PHP Function `moneyFormatIndia()` Declaration in Acknowledgement View
- **File**: `admin/application/views/ret_metal_process/metal_process/process_acknowladgement.php` lines 100 and 659
- **Description**: The function `moneyFormatIndia()` is declared twice within the same PHP file. In PHP, declaring a function a second time in the same execution scope causes a **fatal error**: `Cannot redeclare moneyFormatIndia()`. This will crash the acknowledgement PDF print page for any process that has both melting content AND payment data (both blocks execute together).
- **Impact**: Critical — all acknowledgement PDF pages with payment data will crash with a PHP fatal error
- **Fix**: Remove the duplicate declaration at line 659 (retain only the one at line 100)

---

## 🟢 LOW (R9 — New)

### BUG-OMP-040: Acknowledgement View Has Stray `-` Dash in HTML at Line 88
- **File**: `admin/application/views/ret_metal_process/metal_process/process_acknowladgement.php` line 88
- **Code**: `</label>-` — a stray literal dash character appended after the closing label tag
- **Description**: This renders a visible dash character between the title label and the horizontal rule in all printed acknowledgement slips. Minor cosmetic defect.
- **Fix**: Remove the trailing `-` from line 88

---

## 🔴 CRITICAL (R10 — New)

### BUG-OMP-041: `validateTestingReceiptRow()` Checks Wrong Selector `.receipt_charges`
- **File**: `admin/assets/js/ret_metal_process.js` line 2838
- **Code**:
  ```javascript
  if(... || $(this).find('.receipt_charges').val()=='' ){
  ```
- **Description**: The validation function checks for `.receipt_charges` but the actual input has class `.testing_receipt_charges`. This means the empty-check **always passes** (`.receipt_charges` is never found, `val()` of undefined returns `''`). Users can submit a testing receipt without filling in the charges field — creating a zero-charge record silently.
- **Impact**: Critical — server receives blank `receipt_charges` undetected, creating corrupted receipt records
- **Fix**: Change `.receipt_charges` to `.testing_receipt_charges` at line 2838

---

## 🟠 HIGH (R10 — New)

### BUG-OMP-042: Duplicate `id` on Process Master Radio Buttons
- **File**: `admin/application/views/ret_metal_process/process_master/form.php` lines 46—47 and 56—57
- **Description**: Both radio buttons for "Has Charge" share `id="has_charge"`. Both radio buttons for "Charge Type" share `id="charge_type"`. Duplicate HTML `id` values violate W3C spec; label `for` bindings and CSS/JS selectors targeting these IDs will only target the first element. This makes the second radio in each pair effectively unlabelled and un-addressable.
- **Fix**: Assign unique IDs: `id="has_charge_yes"` / `id="has_charge_no"` and `id="charge_type_gram"` / `id="charge_type_flat"`

---

### BUG-OMP-043: `calculate_pocketing_details()` Averages Purity Over All Rows, Not Checked Rows
- **File**: `admin/assets/js/ret_metal_process.js` lines 2050–2052
- **Code**:
  ```javascript
  // loop only sums CHECKED rows via :checked selector
  avg_purity += parseFloat(tot_purity / $('#pocket_details > tbody tr').length); // ❌ uses TOTAL row count
  ```
- **Description**: The total purity is accumulated from checked rows only (`input[type=checkbox]:checked`), but the divisor uses the total row count (`$('#pocket_details > tbody tr').length`) instead of the checked row count. If 2 of 5 rows are selected, the average purity is divided by 5 instead of 2 — understating purity by 60%.
- **Impact**: High — incorrect purity value submitted for old-metal melting process, affects weight-based price calculation
- **Fix**: Capture checked count as a variable and use it as the divisor

---

### BUG-OMP-044: `validateTestingIssueRow()` Has No Return Statement
- **File**: `admin/assets/js/ret_metal_process.js` line 2874–2885
- **Description**: `validateTestingIssueRow()` sets a local `row_validate` flag but **never returns it**. The `$('#issue_submit')` handler never calls this function in its flow, but any future caller would receive `undefined`, which is truthy, allowing invalid submissions to proceed.
- **Fix**: Add `return row_validate;` at line 2885

---

## 🟢 LOW (R10 — New)

### BUG-OMP-045: Duplicate `bDestroy: true` in DataTable Initialization
- **File**: `admin/assets/js/ret_metal_process.js` line 2556
- **Code**:
  ```javascript
  "bDestroy": true,
  "bDestroy": true,  // ❌ duplicate
  ```
- **Description**: The `process_list` DataTable initialization object has the same option `bDestroy: true` declared twice. While harmless at runtime (JS last-wins), it indicates copy-paste negligence and will cause lint/code-review warnings.
- **Fix**: Remove the duplicate `bDestroy` line

---

### BUG-OMP-046: `get_old_metal_process()` Never Called on Process List View
- **File**: `admin/assets/js/ret_metal_process.js` line 2538
- **Description**: The function `get_old_metal_process()` initializes the `#process_list` DataTable. Investigation shows no `$(document).ready()` call or page-switch trigger that calls this function. The process list table will be initialized empty (blank DataTable) unless Datatable initialization is also triggered from a `switch` block elsewhere. If the `case:'metal_process_list'` JS block is missing, the table never populates.
- **Fix**: Confirm the switch block includes this load; if not, add `get_old_metal_process();` to the relevant `$(document).ready()` / page-init block

---

## 🔴 CRITICAL (R11 — New)

### BUG-OMP-047: No CSRF Protection on Any POST Handler
- **File**: `admin/application/controllers/admin_ret_metal_process.php` — all `save`, `update`, `delete` cases
- **Description**: Every write operation (process save line 63, pocket save, melting/testing/refining/polishing issue/receipt save, delete) reads raw `$_POST` without any CSRF token verification. CodeIgniter's built-in CSRF protection (`$config['csrf_protection'] = TRUE`) is either disabled globally or not enforced for these routes. An authenticated user visiting a malicious page could have arbitrary process records created, modified, or deleted on their behalf.
- **Impact**: Critical — all state-mutating operations exploitable via CSRF from any page
- **Fix**: Enable CI CSRF protection in `config.php`, or add per-route token verification; at minimum add `$this->security->get_csrf_hash()` checks on all POST handlers

---

## 🟡 MEDIUM (R11 — New)

### BUG-OMP-048: Root List View Uses Wrong Table ID `pocket_list`
- **File**: `admin/application/views/ret_metal_process/list.php` line 66
- **Code**: `<table id="pocket_list" ...>`
- **Description**: The root process list view (which lists all old-metal processes) has its DataTable initialized with `id="pocket_list"` instead of a process-specific ID like `process_list`. If the JS switch block targets `#process_list` to populate this table (or if `get_pocket_list()` ever fires on this page), the table will either never populate or bind to the wrong element entirely.
- **Fix**: Change `id="pocket_list"` to `id="old_metal_process_list"` (or match whatever the JS targets)

---

## 🟢 LOW (R11 — New)

### BUG-OMP-049: Root List View Add Button Has No ACL Guard
- **File**: `admin/application/views/ret_metal_process/list.php` line 25
- **Code**: `<a class="btn btn-success" id="add_estimation" href="...metal_process_issue/add">Add</a>` — no `if($access['add']==1)` check
- **Description**: Every other list view in this module (pocket/list.php, metal_process/list.php, process_master/list.php) wraps the Add button in `<?php if($access['add']==1){?>`. The root list.php shows the Add button unconditionally to all users regardless of their ACL permissions. A read-only user will see the button (though the controller itself may still deny the actual add action).
- **Fix**: Wrap line 25 in `<?php if($access['add']==1){ ?>` / `<?php } ?>`

### BUG-OMP-050: OMP-032 Expanded — "Delete Estimation" in 5th File
- **Note**: This is an expansion of **OMP-032**, not a new bug ID.
- **File**: `admin/application/views/ret_metal_process/list.php` line 98
- **Description**: The root list.php also has `<h4 class="modal-title">Delete Estimation</h4>` — the same copy-paste from the estimation module. OMP-032 now confirmed in **5 files**: process_report.php, detailed_report.php, metal_process/list.php, pocket/list.php, and list.php (root).
- **Fix**: Fix all 5 occurrences — change title to "Delete Process" and body to "this process"

---

## 🔴 CRITICAL (R12 — New)

### BUG-OMP-051: Systemic Raw SQL Concatenation — 20+ Unparameterised Query Points in Model
- **File**: `admin/application/models/ret_metal_process_model.php`
- **Locations** (beyond OMP-001 already catalogued):

| Line | Function | Vulnerable Param |
|---|---|---|
| 806 | `get_melting_pocket_details()` | `$id_metal_pocket` |
| 821 | `get_pocket_details()` | `$data['trans_type']` |
| 862 | `get_melting_tag_details()` | `$id_metal_pocket` |
| 876 | `get_melting_non_tag_details()` | `$id_metal_pocket` |
| 923, 925 | `get_polishing_pocket_details()` | `$id_metal_pocket` ×2 |
| 952 | `get_last_process_number()` | `$process_for`, `$id_metal_process` |
| 980 | `get_metal_process()` | `$id_old_metal_process` |
| 993, 1008 | `get_melting_issue/receipt_details()` | `$id_old_metal_process` |
| 1015, 1021 | `get_old_metal_process_payment()`, `getPocketingDetails()` | `$id_old_metal_process`, `$id_metal_pocket` |
| 1045 | `get_KarigarMeltingIssueDetilas()` | `$data['id_karigar']` |
| 1085, 1095 | `get_testing_issue_details()`, `get_TestingReceiptAcknowladgement()` | `$id_old_metal_process` |
| 1109 | `get_testing_receipt_details()` | `$data['id_karigar']` |
| 1113–1118 | `check_purity_stock_details()` | `$id_branch`, `$id_ret_category`, `$id_product`, `$purity` |
| 1171 | `get_RefiningReceiptDetails()` | `$data['id_karigar']` |
| 1185, 1198 | `get_RefiningIssueAcknowladgement()`, `get_refiningReceiptAcknowladgement()` | `$id_old_metal_process` |
| 1212 | `get_PolishingReceiptDetails()` | `$data['id_karigar']` |
| 1224, 1240 | `get_PolishingIssueAcknowladgement()`, `get_PolishingReceiptAcknowladgement()` | `$id_old_metal_process` |
| 1250, 1300 | `checkPurchaseItemStockExist()`, `checkStoneItemStockExist()` | `$id_branch`, `$id_product`, `$purity` |
| 1291–1292 | `getCategoryDetails()` | `$id_ret_category` |
| 1553, 1563, 1576 | `get_polishing/testing/melting_process_details()` | `$id_old_metal_process` |
| 1589–1594 | `checkNonTagItemExist()` | `$data['id_product']`, `$id_section`, `$id_design`, `$id_sub_design`, `$data['id_branch']` |

- **Description**: All parameters above are interpolated directly into query strings without `$this->db->escape()` or CI Active Record parameterised methods. Any parameter originating from user input (POST/GET or derived from prior DB reads of user-controlled data) is exploitable for SQL injection.
- **Impact**: Critical — full DB read/write exposure; all process, stock, and karigar data at risk
- **Fix**: Refactor all raw-concat queries to use CI Active Record (`$this->db->where()`, `$this->db->get()`) or at minimum wrap each value in `$this->db->escape()`

---

### BUG-OMP-052: `$arith` Operator Injection in **Six** UPDATE Model Functions
- **File**: `admin/application/models/ret_metal_process_model.php` lines 1134, 1261, 1274, 1606, 1791, 1802
- **Code** (example from line 1134):
  ```php
  $sql = "UPDATE ret_purchase_item_stock_summary SET gross_wt=(gross_wt".$arith." ".$data['gross_wt'].")...";
  ```
- **Description**: The `$arith` parameter (arithmetic operator: `+` or `-`) is passed from the controller directly into the SQL string without any whitelist validation. Six functions are affected: `updateStockItemData()` (1134), `updatePurItemData()` (1261), `updatePocketItem()` (1274), `updateNTData()` (1606), `update_test_metalItem()` (1791), `update_refining()` (1802).
- **Impact**: Critical — allows blind SQL injection into any UPDATE that flows through `$arith`
- **Fix**: Whitelist `$arith` to only `'+'` or `'-'` before use: `$arith = in_array($arith, ['+','-']) ? $arith : '+'`

---

## 🔴 CRITICAL (R12 — New)

### BUG-OMP-053: `updateStoneItemData()` Uses `$data['id_product']` for Both `id_branch` and `id_product` in WHERE
- **File**: `admin/application/models/ret_metal_process_model.php` line 1311
- **Code**:
  ```php
  $sql = "UPDATE ... WHERE id_ret_category=".$data['id_ret_category']
       ." and id_branch=".$data['id_product']   // ❌ should be $data['id_branch']
       ." and id_product=".$data['id_product']." ";
  ```
- **Description**: Copy-paste error — the `id_branch` column in the WHERE clause is filtered by `$data['id_product']` instead of `$data['id_branch']`. This means the stock summary UPDATE for stone items filters by product ID in the branch column, **effectively matching wrong rows or no rows** — stone stock updates silently fail or corrupt rows matching a different branch's product ID.
- **Impact**: Critical — stone item stock levels go wrong on every old-metal polishing receipt
- **Fix**: Change `$data['id_product']` to `$data['id_branch']` at line 1311

---

## 🟡 MEDIUM (R12 — New)

### BUG-OMP-054: `get_refining_process_details()` Missing `WHERE` Clause — Returns All Refining Records
- **File**: `admin/application/models/ret_metal_process_model.php` lines 1538–1544
- **Code**: The function signature accepts `$id_old_metal_process` but the query **never uses it**:
  ```php
  function get_refining_process_details($id_old_metal_process)
  {
      $sql = $this->db->query("SELECT ... FROM ret_old_metal_process p
          LEFT JOIN ret_old_metal_refining r ON r.id_old_metal_process=p.id_old_metal_process
          ...
          "); // ❌ no WHERE id_old_metal_process = $id_old_metal_process
      return $sql->row_array();
  }
  ```
- **Description**: The `WHERE` clause referencing `$id_old_metal_process` was never added. `->row_array()` returns the first row of the entire table join — always returning the oldest refining process's data regardless of which process is requested. Any report or acknowledgement that calls this function will show the wrong record.
- **Fix**: Add `WHERE p.id_old_metal_process = `.$id_old_metal_process before the closing query string

---

## 🔴 CRITICAL (R13 — New)

### BUG-OMP-055: `check_purity_stock_details()` Double-Query PHP Fatal
- **File**: `admin/application/models/ret_metal_process_model.php` lines 1113–1120
- **Code**:
  ```php
  $sql = $this->db->query("SELECT * FROM ...");  // $sql is now a CI result object
  $res = $this->db->query($sql);                  // ❌ passes result object as query string
  if($res->num_rows() > 0) { ...
  ```
- **Description**: `$this->db->query()` is called once to execute the query and returns a CI result object into `$sql`. Then on line 1120, `$this->db->query($sql)` is called again passing that result object as the query string argument. CI will call `->__toString()` on the object (which returns `''` or triggers a PHP notice/warning), and the result `$res` will be an invalid/false query result. `$res->num_rows()` will return 0 always or throw a fatal — meaning purity stock checks always return `status: FALSE` and purity-based stock lookups silently fail.
- **Impact**: Critical — polishing receipt stock validation never finds existing stock, potentially creating duplicate stock summary rows on every receipt save
- **Fix**: Remove line 1120 — use `$sql` directly: `if($sql->num_rows() > 0)`

### BUG-OMP-056: `get_karigar_state()` Reads `$_POST` Directly in Model Layer
- **File**: `admin/application/models/ret_metal_process_model.php` line 1844
- **Code**:
  ```php
  function get_karigar_state(){
      $sql = $this->db->query(
          "Select id_state from ret_karigar where id_karigar =".$_POST['karigar'].""
      );
  ```
- **Description**: The model accesses `$_POST['karigar']` directly — bypassing all controller validation and CodeIgniter's input layer. This is a double violation: (1) MVC separation — models must never touch superglobals; (2) raw SQL injection via `$_POST['karigar']` with no sanitization. Any user who can modify the POST body can inject arbitrary SQL into the karigar state lookup.
- **Impact**: Critical — SQL injection via POST data directly in model; architectural violation prevents safe refactoring
- **Fix**: Remove `$_POST` from model; pass `$id_karigar` as a parameter; escape it before use

---

## 🟡 MEDIUM (R13 — New)

### BUG-OMP-057: Model Lines 1600–1981 — 8 More Raw SQL Concat Points (Extends OMP-051)
- **File**: `admin/application/models/ret_metal_process_model.php`
- **Locations** (extending OMP-051):

| Line | Function | Vulnerable Param |
|---|---|---|
| 1652, 1667, 1683 | `get_pocket_melting_details()` | `$id_metal_pocket` ×3 |
| 1701 | `get_melting_recd_details()` | `$id_melting` |
| 1735, 1752 | `get_pocket_polish_details()`, `get_pocket_testing_details()` | `$id_metal_pocket` |
| 1780, 1785 | `get_testing()`, `get_refining()` | `$id_melting_recd`, `$id_metal_testing` |
| 1831 | `get_opening_metal_stock_list()` | `$data['id_metal']` |
| 1852 | `get_company_state()` | `$id` (raw param) |
| 1860 | `get_tag_details()` | `$tag_id` |
| 1929, 1972 | `get_purchase_cus_order_details()`, `get_tag_cus_order_details()` | `$id_customerorder` |

- **Note**: These extend OMP-051's already-documented surface. Collectively OMP-051/057 cover **30+ injection points**.
- **Fix**: Apply `$this->db->escape()` or Active Record throughout

---

## 🟢 LOW (R13 — New)

### BUG-OMP-058: JS `#add_new_polishing_category` — `section` Variable Wrong Placeholder Text
- **File**: `admin/assets/js/ret_metal_process.js` line 3656
- **Code**: `var section = "<option value=''>- Select Category-</option>";  // ❌ should be 'Select Section'`
- **Description**: The `section` select variable is initialized with the placeholder `"- Select Category-"` instead of `"- Select Section-"`. As a result, the section dropdown in the polishing receipt category modal shows `"Select Category"` as the blank/placeholder instead of `"Select Section"` — confusing UX.
- **Fix**: Change to `"- Select Section-"`

### BUG-OMP-059: Duplicate `refining_ret_category` Change Handler Registered Twice
- **File**: `admin/assets/js/ret_metal_process.js` lines 3335 and 3841
- **Description**: The `.on('change', '.refining_ret_category')` handler is registered identically at line 3335 and again at line 3841. Both populate the product and purity dropdowns from the same data. This doubles the options appended to both selects every time category changes in the refining receipt modal — all products/purities appear twice.
- **Fix**: Remove one of the two duplicate handlers (line 3841 is the redundant one)

---

## 🟠 HIGH (R14 — New)

### BUG-OMP-060: `calculate_tag_list()` Averages Purity Over All Rows, Not Checked Rows
- **File**: `admin/assets/js/ret_metal_process.js` line 4537
- **Code**:
  ```js
  $('.tag_total_avg_purity').val(
    parseFloat(purity_value / $('#tag_list > tbody > tr').length).toFixed(3)
  ); // ❌ divides by total rows, not checked rows
  ```
- **Description**: Same flaw as OMP-043. Purity sum is divided by total row count, not the number of **checked** tag rows, so unchecked rows (contributing 0 purity) dilute the average.
- **Fix**: Divide by checked-row count: `$('#tag_list > tbody > tr').filter(function(){ return $(this).find('.tag_id:checked').length > 0; }).length`

### BUG-OMP-061: `get_tag_search_list()` — Unquoted HTML Attribute Values Allow DOM Injection
- **File**: `admin/assets/js/ret_metal_process.js` lines 4475–4484
- **Code**: `value='+val.tag_id+'` (unquoted), `value='+val.tag_code+'` etc. — 9 attributes
- **Description**: All `value=` attributes in the dynamically-built tag row HTML are unquoted. A server-returned value containing a space, `>`, or `"` will break attribute parsing. `tag_code` values with dashes or slashes are especially likely to cause misparsing; malicious codes could trigger DOM-based injection.
- **Impact**: High — all hidden input values in the tag selection table are unquoted
- **Fix**: Quote every attribute: `value=\"'+val.tag_id+'\"` etc. on all 9 attributes

---

## 🟡 MEDIUM (R14 — New)

### BUG-OMP-062: AJAX Date Sourced from `.html()` Not `.val()` in Two Report Functions
- **File**: `admin/assets/js/ret_metal_process.js` lines 4128, 4244
- **Code**: `'from_date':$('#rpt_payments1').html(),'to_date':$('#rpt_payments2').html()`
- **Description**: Date ranges are read via `.html()` (innerHTML). If `#rpt_payments1/2` ever become `<input>` elements, `.html()` returns empty and the report silently sends blank dates. Also risks including stray HTML tags if the element contains them.
- **Fix**: Use `.text()` for display elements or `.val()` for input elements consistently

### BUG-OMP-063: `print_url` Implicit Global in `fnFormatRowProcessDetails()`
- **File**: `admin/assets/js/ret_metal_process.js` lines 4343, 4365, 4387
- **Code**: `print_url = base_url + '...'` — no `var`/`let`/`const`
- **Description**: Three assignments inside `fnFormatRowProcessDetails()` create an implicit global, which can be overwritten by concurrent execution (rapid drill-down clicks), sending the user to the wrong process acknowledgement URL.
- **Fix**: Declare `var print_url;` at the top of the function

---

## 🟢 LOW (R14 — New)

### BUG-OMP-064: OMP-036 Extended — 5 More `console.log` Calls Found in JS 4000–5088
- **File**: `admin/assets/js/ret_metal_process.js` lines 4521, 4530, 4548, 4553, 4991
- **Description**: 5 more debug `console.log` calls in `calculate_tag_list()`, `calculate_non_tag_list()`, and `get_chg_tax_type()`. OMP-036 total is now **26 console.log calls** across 5088 lines.
- **Fix**: Remove all 26 `console.log` calls
