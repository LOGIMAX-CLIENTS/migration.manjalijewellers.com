# Branch Transfer — Deep Analysis Round 9: Model L1700-2236 + JS Confirmation

> **Date**: 2026-03-11
> **Focus**: Model remaining methods L1700-2236, JS file structure validation
> **Model coverage**: 100% (2236/2236 lines read)
> **Controller coverage**: 100% (1378/1378 lines read)

---

## Bugs Found: 5

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-D16 | **P0** | `getNontagItemId()` null-crash on `->row()->id_nontag_item` | Model L2233 | A |
| BRN-D17 | **P0** | `get_headoffice_branch()` null-crash on `->row()->id_branch` | Model L1902 | A |
| BRN-D18 | **P2** | `SELECT *` in 3 PS/SR detail functions | Model L1879, 1886, 1892 | A |
| BRN-D19 | **P2** | Undefined index `$r['product']` and `$r['design']` | Model L2217-2218 | A |
| BRN-D20 | **P2** | `get_InventoryCategory()` overwrites input param with query result | Model L1914 | A |

---

### BRN-D16 — `getNontagItemId()` Null-Crash [P0]
```php
// Model L2227-2234:
function getNontagItemId($id_branch, $id_section, $id_product, $id_design, $id_sub_design) {
    $sql = $this->db->query("SELECT id_nontag_item 
        from ret_nontag_item
        where branch =" . $id_branch . " and id_section =" . $id_section . " 
        and product=" . $id_product . " and design=" . $id_design . " and id_sub_design=" . $id_sub_design);
    return $sql->row()->id_nontag_item;  // ← CRASH if no matching row
}
```
**Root Cause**: Same null-dereference pattern as BRN-D08/D10. Called from `fetchNonTaggedReceiptedItems()` L2220, which iterates over receipt items. If any item doesn't have a matching nontag entry, this crashes the entire receipt listing.
**Pattern**: 4th instance of PAT-NULL-DEREF in this model — a systemic issue.

### BRN-D17 — `get_headoffice_branch()` Null-Crash [P0]
```php
// Model L1899-1903:
function get_headoffice_branch() {
    $sql = $this->db->query("SELECT * FROM branch WHERE is_ho=1");
    return $sql->row()->id_branch;  // ← CRASH if no HO branch exists
}
```
**Root Cause**: If the `branch` table has no row with `is_ho=1`, `->row()` returns NULL. Also uses `SELECT *` (minor).
**Impact**: Called during approval flow to check head office status. If HO branch isn't configured, all approvals crash.

### BRN-D18 — `SELECT *` in 3 Functions [P2]
```php
// L1879: SELECT * FROM `ret_brch_transfer_old_metal` where transfer_id=...
// L1886: SELECT * FROM `ret_brch_transfer_old_metal` where transfer_id=...
// L1892: SELECT * FROM `ret_brch_transfer_old_metal` where transfer_id=...
```
Three functions (`getBTOldMetalDetails`, `get_salesreturn_items`, `get_partlysale_items`) query the same table with `SELECT *`, differing only by `item_type` filter. Violates "Never use SELECT *" rule. Could be refactored into a single parameterized function.

### BRN-D19 — Undefined Array Index in Receipt Items [P2]
```php
// Model L2217-2218 (inside fetchNonTaggedReceiptedItems):
"product"  => $r['product'],   // ← 'product' is NOT in the SELECT at L2185
"design"   => $r['design'],    // ← 'design' is NOT in the SELECT at L2185
```
**Root Cause**: The SELECT at L2185 has `nt.id_product` and `nt.id_design` but NOT `nt.product` or `nt.design` (using `ret_nontag_receipt` table which has columns named `id_product` and `id_design`). This will produce PHP "Undefined index" notices and populate the array with empty values.
**Impact**: Data sent to the JS frontend will have missing `product` and `design` fields, breaking the UI display for non-tag receipted items.

### BRN-D20 — Parameter Overwrite in `get_InventoryCategory()` [P2]
```php
// Model L1912-1920:
function get_InventoryCategory($id_other_item_type) {
    $id_other_item_type = $this->db->query("SELECT ... WHERE i.id_other_item=" . $id_other_item_type);  // ← input param overwritten
    return $id_other_item_type->row_array();
}
```
**Root Cause**: The function parameter `$id_other_item_type` is reused as the variable to store the query result. This works functionally but is confusing and breaks readability — the parameter name is misleading after L1914.

---

## JS File Scan Summary

| Check | Result | Notes |
|---|---|---|
| `parseFloat` calls | **0** found | No financial calculations in JS — all server-side |
| `error:` AJAX handlers | **0** found | All AJAX calls lack error callbacks (confirmed BRN-R502) |
| `save_` / `submit_` functions | **0** found | Save flow uses form POST, not JS function |
| `async: false` | Already flagged in R5 | BRN-R403 |
| Missing `return false` | Already flagged in R5/R6 | Various validation functions |

**Conclusion**: JS file is primarily a UI orchestration layer. All financial/weight calculations happen server-side in the PHP model. No new JS bugs beyond what R5/R6 already found.

---

## Model Coverage Summary (R7+R8+R9)

| Line Range | Methods Covered | Bugs Found |
|---|---|---|
| L1-107 | Generic: insertData, updateData, updateDatamulti | BRN-D01, BRN-D02, BRN-D04 |
| L108-355 | Filters: getLotsByFilter, fetchTagsByFilter, etc. | BRN-D03 (SQL injection), BRN-D05 |
| L356-800 | getApprovalListing, getNonTagItems, trans_code_generator | BRN-D08 |
| L800-1100 | Print: getBTransData, getBTtags, get_tag_details | BRN-D10 |
| L1100-1700 | Listing, purchase items, sales returns | BRN-D09, BRN-D11, BRN-D12-D15 |
| L1700-2236 | Download data, repair orders, receipt items | BRN-D16, BRN-D17, BRN-D18-D20 |

**Model: 100% complete — 2236/2236 lines read.**
**Controller: 100% complete — 1378/1378 lines read.**
