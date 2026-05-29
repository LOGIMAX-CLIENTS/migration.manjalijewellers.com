# Remediation Plan — Old Metal Process Module

**Module**: `old_metal_process`  
**Date**: 2026-03-14  
**Total Bugs**: 18  
**Critical**: 4 | High: 5 | Medium: 6 | Low: 3

---

## Sprint Plan

### Sprint 1 — Critical Fixes (Financial + Runtime Blockers)

| Bug | Fix | File | Complexity |
|---|---|---|---|
| OMP-002 | Fix GST tax type comparison field | `admin_ret_metal_process.php` | Low |
| OMP-003 | Fix NB payment amount field | `admin_ret_metal_process.php` | Low |
| OMP-004 | Fix double-query in purity check | `ret_metal_process_model.php` | Low |
| OMP-014 | Fix missing model function name | `admin_ret_metal_process.php` | Low |

### Sprint 2 — Critical Security + High Severity

| Bug | Fix | File | Complexity |
|---|---|---|---|
| OMP-001 | Parameterize all SQL queries | `ret_metal_process_model.php` | High |
| OMP-013 | Remove direct `$_POST` in SQL | `ret_metal_process_model.php` | Low |
| OMP-006 | Add server-side weight validation | `admin_ret_metal_process.php` | Medium |
| OMP-005 | Atomic process number generation | `ret_metal_process_model.php` | Medium |
| OMP-009 | Fix transaction response logic | `admin_ret_metal_process.php` | Low |

### Sprint 3 — Medium + Data Integrity

| Bug | Fix | File | Complexity |
|---|---|---|---|
| OMP-011 | Fix updateStoneItemData WHERE clause | `ret_metal_process_model.php` | Low |
| OMP-015 | Fix net_wt in updatePurItemData | `ret_metal_process_model.php` | Low |
| OMP-010 | Remove duplicate JS event handler | `ret_metal_process.js` | Low |
| OMP-007 | Implement pocket closure logic | Both files | Medium |
| OMP-008 | Add melting_status pre-checks | `admin_ret_metal_process.php` | Medium |
| OMP-012 | Reconcile polishing receipt report query | `ret_metal_process_model.php` | Medium |

### Sprint 4 — Low Priority Polish

| Bug | Fix | File | Complexity |
|---|---|---|---|
| OMP-016 | Add no_of_piece to NT log | `admin_ret_metal_process.php` | Low |
| OMP-017 | Ensure id_branch is properly scoped | `admin_ret_metal_process.php` | Low |
| OMP-018 | Fix "portriat" → "portrait" typo | `admin_ret_metal_process.php` | Trivial |

---

## Fix Details

### OMP-002 Fix
```php
// BEFORE (line ~1773):
$company_state = $data['comp_details'][0]['id_company'];

// AFTER:
$company_state = $data['comp_details'][0]['id_state'];  // verify exact column name
```

### OMP-003 Fix  
```php
// BEFORE (line ~1494):
'payment_amount' => $receipt_payment['cash_amount'],

// AFTER:
'payment_amount' => $receipt_payment['net_banking_amount'],
```

### OMP-004 Fix
```php
// BEFORE:
$sql = $this->db->query("SELECT * FROM ...");
$res = $this->db->query($sql);  // ❌ double-query
if ($res->num_rows() > 0) { ... }

// AFTER:
$sql = $this->db->query("SELECT * FROM ...");
if ($sql->num_rows() > 0) { ... }
```

### OMP-010 Fix
```javascript
// Remove the duplicate handler at line ~1272:
// DELETE: $('#select_metal_process').on('change', function() { ... });  (second instance)
// KEEP: The first instance at line ~1155
```

### OMP-011 Fix
```php
// BEFORE (line ~1311):
"...WHERE id_ret_category=".$data['id_ret_category']."
 and id_branch=".$data['id_product']."   ← WRONG
 and id_product=".$data['id_product']

// AFTER:
"...WHERE id_ret_category=".$data['id_ret_category']."
 and id_branch=".$data['id_branch']."   ← CORRECT
 and id_product=".$data['id_product']
```

### OMP-013 Fix
```php
// BEFORE:
"Select id_state from ret_karigar where id_karigar =".$_POST['karigar']

// AFTER:
$karigar_id = $this->input->post('karigar', TRUE);
"Select id_state from ret_karigar where id_karigar = ?"
// Use: $this->db->query($sql, [$karigar_id])
```

### OMP-014 Fix
```php
// BEFORE (controller get_Active_Refining):
$data = $this->$model->get_Active_Refining_details($_POST);

// AFTER — verify which model function is intended:
// Option A: $data = $this->$model->get_RefiningIssueDetails();
// Option B: Create get_Active_Refining_details() in model if it's a separate concept
```

### OMP-018 Fix
```php
// BEFORE:
$dompdf->set_paper("a4", "portriat");

// AFTER:
$dompdf->set_paper("a4", "portrait");
```

---

## Regression Test Checklist

After each fix, verify:

### Financial (OMP-002, OMP-003)
- [ ] Create refining receipt for same-state karigar → confirm CGST+SGST (not IGST)
- [ ] Create refining receipt for different-state karigar → confirm IGST
- [ ] Save NB payment → confirm `ret_old_metal_process_payment.payment_amount` = NB amount

### Data Integrity (OMP-004, OMP-011, OMP-015)
- [ ] Save melting receipt → verify `ret_purchase_item_stock_summary` updated
- [ ] Save stone category receipt → verify stone stock updated in correct `id_branch`
- [ ] Save purchase item → verify `net_wt` uses correct net weight value

### Runtime (OMP-014)
- [ ] Open Metal Process form with Refining process
- [ ] Select Refining from dropdown → no PHP/500 error
- [ ] Dropdown populates with refining process numbers

### UI (OMP-010, OMP-018)
- [ ] Select/change melting process number → rows appear only once in UI table
- [ ] Generate PDF acknowledgement → pages render in portrait orientation

### Process Flow (OMP-006, OMP-007, OMP-008)
- [ ] Attempt to issue more weight than balance → server rejects with error
- [ ] Exhaust pocket fully → pocket no longer appears in issue dropdown
- [ ] Attempt testing issue without melting receipt → server rejects

---

## Code Health Notes

1. **Copy-paste debt**: The non-tag stock upsert pattern appears 4× (melting/testing/refining/polishing receipt). Extract into a private controller method: `private function _upsert_nontag_stock($item, $branchDetails)`.

2. **All model queries use string concatenation**: This is a systemic issue requiring a phased refactor. Prioritize functions that accept user input (POST/GET) first.

3. **Process acknowledgement function**: `process_acknowladgement($id)` is ~50 lines of if-else; consider a strategy pattern or at minimum extracting the data-loading logic to the model.

4. **JS file length (5088 lines)**: The JS file mixes pocket UI, process UI, report UI, and utility functions. Consider splitting into modules when refactoring.
