# Remediation Checklist — Old Metal Process Module

**Total Bugs**: 28 (6 Critical, 6 High, 10 Medium, 5 Low)  
**Status key**: ⬜ Not Started | 🔧 In Progress | ✅ Done | 🔴 Blocked

---

## Sprint 1 — P0 Fixes (30 min total, immediate production impact)

### ⬜ OMP-019: Uncomment Payment Tab in View
- **File**: `admin/application/views/ret_metal_process/metal_process/form.php`
- **Fix**: Uncomment line 347 (tab `<li>`) and lines 590–630 (tab pane + table)
- **Effort**: 5 min
- **Risk**: None — controller payment save code (lines 1474–1500) already works
- **Verify**: Submit a receipt with cash payment → check `ret_old_metal_process_payment` for a row

### ⬜ OMP-028: Fix Undefined `$id_branch` in Polishing Lot Inwards
- **File**: `admin/application/controllers/admin_ret_metal_process.php` line 1315
- **Fix**: `'created_branch' => $id_branch` → `'created_branch' => $branchDetails['id_branch']`
- **Effort**: 1 min
- **Risk**: None — `$branchDetails` is in scope at that point
- **Tally impact**: Existing polishing records with `created_branch=NULL` should be reviewed before Tally sync
- **Verify**: Save polishing receipt → check `ret_lot_inwards.created_branch` is not NULL

### ⬜ OMP-003: Fix NB Payment Uses `cash_amount`
- **File**: `admin/application/controllers/admin_ret_metal_process.php` line 1494
- **Fix**: `'payment_amount' => $receipt_payment['cash_amount']` → `'payment_amount' => $receipt_payment['net_banking_amount']`
- **Effort**: 1 min
- **Risk**: Low — only affects new NB payment records; existing records already corrupted
- **Verify**: Submit NB payment of ₹5000 → check `ret_old_metal_process_payment.payment_amount = 5000` not 0

---

## Sprint 2 — Data Integrity Fixes (2–4 hrs)

### ⬜ OMP-002: Fix GST Tax Type Comparison
- **File**: `admin/application/models/ret_metal_process_model.php`, `get_chg_tax_type()`
- **Current code** (model lines ~1838–1847):
  ```php
  $carrier_state = /* id from ret_karigar */;
  // compares company id_company to id_state — WRONG
  ```
- **Fix**:
  ```php
  $company_state = $this->db->query("SELECT id_state FROM company LIMIT 1")->row_array()['id_state'];
  // or use admin_settings_model->getCompanyDetails("")['id_state']
  $karigar_state = $this->db->query("SELECT id_state FROM ret_karigar WHERE id_karigar =".$_POST['karigar'])->row_array()['id_state'];
  return $company_state == $karigar_state ? 1 : 0; // 1=CGST+SGST, 0=IGST
  ```
- **Effort**: 20 min (including test)
- **Verify**: Create process with same-state vendor → CGST/SGST split; different state → IGST

### ⬜ OMP-029: Fix Refining Receipt Piece Count Hardcoded to 1
- **File**: `admin/application/controllers/admin_ret_metal_process.php` line 1024
- **Fix**: `'piece' => 1` → `'piece' => $cat['recd_pcs']`
- **Effort**: 1 min
- **Note**: Existing refining receipt records will have wrong piece counts — may need data correction
- **Verify**: Save refining receipt with 3 pieces → `ret_old_metal_refining_details.piece = 3`

### ⬜ OMP-001 / OMP-013: Parameterize Raw SQL Queries
- **File**: `admin/application/models/ret_metal_process_model.php`
- **Affected functions**: All functions using `.$_POST['...'].` or `.$id.` concatenation
- **Fix**: Use CI `$this->db->escape()` wrapper or `$this->db->where()` active record
- **Effort**: 2–4 hrs (many occurrences)
- **Priority**: P1 Security

### ⬜ OMP-004: Fix Double-Query in Purity Stock
- **File**: `admin/application/models/ret_metal_process_model.php`, `check_purity_stock()`
- **Fix**: Remove duplicate `$this->db->query()` call, use result from first query
- **Effort**: 15 min

### ⬜ OMP-022: Rename Duplicate `#category_row` Table IDs
- **File**: `admin/application/views/ret_metal_process/metal_process/form.php`
- **Fix**:
  - Line 682 (`#category_modal`): keep as `id="category_row"` (melting)
  - Line 724 (`#refining_category_modal`): rename to `id="refining_category_row"`
  - Line 767 (`#polishing_category_modal`): rename to `id="polishing_category_row"`
- **JS Update**: Update all `$('#category_row')` selectors in `ret_metal_process.js` to use the correct IDs per modal context
- **Effort**: 30–60 min (JS selector audit required)

---

## Sprint 3 — State Machine & Validation Fixes (4–8 hrs)

### ⬜ OMP-005: Add DB UNIQUE Constraint on `process_no`
```sql
ALTER TABLE ret_old_metal_process ADD UNIQUE KEY uq_process_no (process_no);
```
- **Warning**: Check for existing duplicates first with:
  ```sql
  SELECT process_no, COUNT(*) FROM ret_old_metal_process GROUP BY process_no HAVING COUNT(*) > 1;
  ```

### ⬜ OMP-007: Mark Pocket `status=1` When Fully Issued
- **File**: Controller save logic for all issue processes (melting, polishing)
- **Fix**: After issuing from a pocket, check if `blc_pcs=0 AND blc_wt=0` and set `status=1`

### ⬜ OMP-023: Fix `get_refining_process_details()` Missing WHERE Clause
- **File**: `admin/application/models/ret_metal_process_model.php`
- **Fix**: Add `WHERE id_old_metal_process = $id` to the refining report query

### ⬜ OMP-025: Fix `validateTestingIssueRow()` Wrong Table Selector
- **File**: `admin/assets/js/ret_metal_process.js`
- **Fix**: Change `$('#testing_receipt')` to `$('#testing_process_details')` in the validation function

### ⬜ Against-Melting Stock Block: Uncomment and Fix
- **File**: `admin/application/controllers/admin_ret_metal_process.php` lines 744–755
- **Fix**: Uncomment + set `melting_status=3` on linked `ret_old_metal_melting_recd_details`

---

## Sprint 4 — Low Priority / Polish

### ⬜ OMP-018: Fix DomPDF Orientation Typo
- **File**: `admin/application/controllers/admin_ret_metal_process.php` line 1578
- **Fix**: `"portriat"` → `"portrait"`

### ⬜ OMP-026: Fix Report Date Filter `.html()` → `.val()`
- **File**: `admin/assets/js/ret_metal_process.js`

### ⬜ OMP-027: Fix Polishing Non-Tag Validator Checks Disabled Fields
- **File**: `admin/assets/js/ret_metal_process.js`

### ⬜ OMP-020 / OMP-021: Fix Charges Validation Class Mismatches
- **File**: `admin/assets/js/ret_metal_process.js`

---

## Tally Sync Safety Checklist

Before applying any fixes, check these to avoid Tally sync corruption:

| Check | Query |
|---|---|
| Polishing records with NULL created_branch | `SELECT COUNT(*) FROM ret_lot_inwards WHERE created_branch IS NULL AND lot_from=5` |
| Refining receipts with wrong piece count | `SELECT * FROM ret_old_metal_refining_details WHERE piece=1` (manual review) |
| NB payments with cash_amount instead of NB amount | `SELECT * FROM ret_old_metal_process_payment WHERE payment_mode='NB'` |
| Unsynced OMP records | `SELECT COUNT(*) FROM ret_old_metal_process WHERE outistransfered=0` |
