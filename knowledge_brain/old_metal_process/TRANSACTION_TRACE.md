# Transaction Trace — Old Metal Process Module

Each transaction block documented with: entry point, pre-conditions, DB operations, post-conditions, and failure modes.

---

## TX-1: Pocket Save (metal_pocket/save)

**Trigger**: Form submit from `pocket/form.php`  
**Pre-conditions**: At least one item selected in metal stock list  
**Controller**: `metal_pocket('save')`

### DB Operations
```
trans_begin()
  INSERT ret_old_metal_pocket                  → id_metal_pocket
  FOR each item in pocket_items[]:
    INSERT ret_old_metal_pocket_details         → id_pocket_details
    IF trans_type == 2 (Tagged):
      UPDATE ret_taging SET tag_process=1       → marks as pocketed
    IF trans_type == 1 (OldMetal):
      UPDATE ret_bill_old_metal_sale_details SET is_pocketed=1
  INSERT log_detail
trans_commit() / trans_rollback()
```
**Post-conditions**: `ret_old_metal_pocket` row exists with `status=0`  
**Failure Modes**:
- `pocket_no` race condition (OMP-005 pattern applies here too via `code_number_generator()`)
- If tagging update fails, pocket header still saved → inconsistent state
- `trans_status()` check guards final commit but intermediate partial writes may persist in edge cases

---

## TX-2: Melting Issue Save (metal_process/save, id_metal_process=1, process_for=1)

**Trigger**: Save button on process form, Melting + Issue selected  
**Pre-conditions**: At least one pocket row in `#pocket_details`, `#tagged_pocket_details`, or `#non_tagged_pocket_details`

### DB Operations
```
trans_begin()
  INSERT ret_old_metal_process                 → id_old_metal_process (process header)
  FOR each pocket[]:
    INSERT ret_old_metal_melting               → id_melting (per pocket)
    INSERT ret_old_metal_melting_details       → issue detail per category
    UPDATE ret_old_metal_pocket                → +issue_gwt, +issue_nwt, +issue_pcs (arithmetic)
  INSERT log_detail
trans_commit()
```
**Post-conditions**: `ret_old_metal_melting.melting_status=0`; pocket `issue_nwt` updated  
**Failure Modes**:
- No server check that `issue_nwt ≤ balance_nwt` (OMP-006)
- Pocket never checked for `status=0` by server → can over-issue

---

## TX-3: Melting Receipt Save (metal_process/save, id_metal_process=1, process_for=2)

**Trigger**: Melting Receipt form submission  
**Pre-conditions**: `receipt[is_melting_select][]` = 1 for at least one row; `cat_details` JSON populated

### DB Operations
```
trans_begin()
  INSERT ret_old_metal_process                 → id_old_metal_process_receipt
  FOR each selected receipt row:
    UPDATE ret_old_metal_melting SET melting_status=1, received_wt, received_less_wt, receipt_charges
    FOR each category in JSON(cat_details):
      INSERT ret_old_metal_melting_recd_details → received_category, recd_pcs, recd_gwt, id_product...
      IF is_non_tag:
        Check checkNonTagItemExist()
        IF exists: updateNTData('+')
        ELSE: insertData(ret_nontag_item)
        INSERT ret_nontag_item_log
        INSERT ret_section_nontag_item_log
      INSERT ret_lot_inwards                   → lot_from=2
      INSERT ret_lot_inwards_detail
  IF receipt_payment[cash_amount] > 0:
    INSERT ret_old_metal_process_payment        → type=cash
  IF receipt_payment[net_banking_amount] > 0:
    INSERT ret_old_metal_process_payment        → ⚠️ BUG OMP-003: saves cash_amount
  INSERT log_detail
trans_commit()
```
**Post-conditions**: `melting_status=1`; stock in `ret_nontag_item`; lot created  
**Failure Modes**:
- Payment tab is **commented out in view** (OMP-019) → no payment is saved from UI
- NB payment records wrong amount (OMP-003) 
- Non-tag upsert: `checkNonTagItemExist()` calls `check_purity_stock_details()` internally — double-query bug (OMP-004)

---

## TX-4: Testing Issue Save (metal_process/save, id_metal_process=2, process_for=1)

**Trigger**: Testing Issue form; items loaded from `get_testing_issue_details()`  
**Pre-conditions**: melting_status=1 records exist for karigar

### DB Operations
```
trans_begin()
  INSERT ret_old_metal_process                 → process header
  FOR each selected testing_issue row:
    UPDATE ret_old_metal_melting_recd_details SET melting_status=2
    INSERT ret_old_metal_testing               → net_wt, purity, amount, testing_status=0
  INSERT log_detail
trans_commit()
```
**Post-conditions**: `melting_status=2`; `ret_old_metal_testing` row exists  
**Failure Modes**:
- No weight limiter: can issue more weight than melting receipt shows
- `melting_status` not pre-checked: could issue already-testing items again

---

## TX-5: Testing Receipt Save (metal_process/save, id_metal_process=2, process_for=2)

**Trigger**: Testing Receipt form submission  
**Pre-conditions**: `testing_status=0` records exist for selected karigar

### DB Operations
```
trans_begin()
  INSERT ret_old_metal_process
  FOR each selected testing_receipt row:
    UPDATE ret_old_metal_testing SET received_wt, received_purity, production_loss, testing_status=1
    UPDATE ret_old_metal_melting_recd_details SET melting_status=3, tested_purity=received_purity
    FOR each category in JSON:
      IF is_non_tag:
        checkNonTagItemExist() → upsert nontag + logs
      INSERT ret_lot_inwards (lot_from=4) + ret_lot_inwards_detail
  INSERT log_detail
trans_commit()
```
**Post-conditions**: `melting_status=3`, `testing_status=1`; lot created  
**Failure Modes**: Same non-tag upsert pattern issues as TX-3

---

## TX-6: Refining Issue Save (metal_process/save, id_metal_process=3, process_for=1)

**Trigger**: Refining Issue form  
**Pre-conditions**: melting_status=3 records exist

### DB Operations
```
trans_begin()
  INSERT ret_old_metal_process
  FOR each refining_issue row:
    INSERT ret_old_metal_refining → issue_weight, id_metal_testing, refining_status=0
    UPDATE ret_old_metal_melting_recd_details SET melting_status=4
  INSERT log_detail
trans_commit()
```
**Post-conditions**: `melting_status=4`, `refining_status=0`  
**Failure Modes**: Can issue same testing item to multiple refineries (no uniqueness check)

---

## TX-7: Refining Receipt Save (metal_process/save, id_metal_process=3, process_for=2)

**Trigger**: Refining Receipt form; GST calculated via `get_chg_tax_type()`  
**Pre-conditions**: refining_status=0 records for karigar

### DB Operations
```
trans_begin()
  INSERT ret_old_metal_process
  FOR each refining_receipt row:
    UPDATE ret_old_metal_refining SET refining_status=1, receipt_charges, receipt_charges_tax_cgst/sgst/igst, receipt_ref_no
    FOR each category in JSON:
      INSERT ret_old_metal_refining_details
      IF is_non_tag EQUIVALENT ASSUMED:
        checkNonTagItemExist() → upsert nontag + logs
  INSERT log_detail
trans_commit()
```
**GST Split**:
```
chg_tax_type = 0 → IGST = tax_value
chg_tax_type = 1 → CGST = SGST = tax_value / 2
```
**Failure Modes**: Wrong GST type from OMP-002 (`id_company` vs `id_state`)

---

## TX-8: Polishing Issue Save (metal_process/save, id_metal_process=4, process_for=1)

**Trigger**: Polishing Issue form; pockets loaded via `get_polish_pocket_details()`

### DB Operations
```
trans_begin()
  INSERT ret_old_metal_process
  INSERT ret_old_metal_polishing (header)
  FOR each pocket[]:
    INSERT ret_old_metal_polishing_details → issue_pcs, issue_gwt, issue_nwt, issue_purity
    UPDATE ret_old_metal_pocket → +issue_gwt, +issue_nwt, +issue_pcs (arithmetic)
  INSERT log_detail
trans_commit()
```
**Post-conditions**: `ret_old_metal_polishing_details` rows with `status=0`

---

## TX-9: Polishing Receipt Save (metal_process/save, id_metal_process=4, process_for=2)

**Trigger**: Polishing Receipt form  
**Pre-conditions**: polishing_details with status=0 for karigar

### DB Operations
```
trans_begin()
  INSERT ret_old_metal_process
  FOR each polishing_receipt row:
    UPDATE ret_old_metal_polishing_details SET recd_pcs, recd_gwt, recd_nwt, status=1
    FOR each category in JSON:
      IF is_non_tag:
        checkNonTagItemExist() → upsert nontag + logs
      INSERT ret_lot_inwards (lot_from=5, narration='From Polishing Process')
      INSERT ret_lot_inwards_detail
  INSERT ret_old_metal_polishing_recd_details (separate summary record)
  INSERT log_detail
trans_commit()
```
**Post-conditions**: `status=1`; stock in nontag; lot created  
**Failure Modes**:
- Possible `id_branch` uninitialized in loop (OMP-017)
- `no_of_piece` missing in existing NT log branch (OMP-016)

---

## Transaction Status Pattern (Shared)

All TX blocks follow this pattern:
```php
$this->db->trans_begin();
// ... operations ...
if ($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();
    // success response
} else {
    $this->db->trans_rollback();
    // error response
}
```

**Weakness**: Some blocks set `trans_begin()` before a `for` loop but the status is only checked **after** the entire loop. If iteration 5 of 10 fails, the rollback applies to all 10, but the returned `$responseData` may have been set to TRUE inside iteration 4 before the failure — resulting in a misleading success response.

---

## Concurrency Risk Summary

| TX | Risk Level | Issue |
|---|---|---|
| TX-1 (Pocket) | Medium | Pocket number race condition |
| TX-2 (Melting Issue) | High | No weight limit check |
| TX-3 (Melting Receipt) | High | Payment tab disabled + NB amount bug |
| TX-4 (Testing Issue) | Medium | No status pre-check |
| TX-5 (Testing Receipt) | Medium | Purity stock double-query |
| TX-6 (Refining Issue) | Medium | No uniqueness guard |
| TX-7 (Refining Receipt) | Critical | Wrong GST type field |
| TX-8 (Polishing Issue) | Low | Stable |
| TX-9 (Polishing Receipt) | Medium | id_branch uninitialized risk |
