# Non-Tag Receipt Branch Transfer — Schema Mismatch & Missing id_nontag_receipt

> Full end-to-end fix for branch transfers using non-tag receipted items where the schema uses `id_collection` + `id_sub_product` instead of the legacy `id_sub_design` + `id_section`, and the `id_nontag_receipt` field was never persisted — allowing duplicate transfers.

## Metadata
- **Pattern ID**: PAT-BT-NT-001
- **Severity**: CRITICAL
- **Modules Affected**: Branch Transfer, Non-Tag Receipt
- **Auto-fixable**: No (requires schema awareness — collection/sub_product vs section/sub_design varies per client)

## Client Scope
- **Applies to**: Clients migrated to collection-based non-tag tracking (id_collection, id_sub_product)
- **Reason**: Legacy clients still using id_section + id_sub_design will have different column names

## Created By
- **Developer**: Antigravity AI
- **Client**: AMS-RetailAdmin
- **Date**: 2026-04-27
- **Source Bug ID**: N/A

## Symptom
1. **fetchNonTaggedReceiptedItems** throws SQL errors or returns wrong data because it references `ret_sub_design_master`, `ret_section`, `id_sub_design`, and `id_section` — columns/tables that don't exist in the migrated schema.
2. **Duplicate transfers possible**: The same non-tag receipt can be transferred multiple times because `id_nontag_receipt` is never saved to `ret_branch_transfer`, so the deduction subquery always returns 0.
3. **Receipt items can't be selected for save**: The receipt DataTable checkbox uses `name="nt_item_sel[]"` but the save flow selector checks `$("input[name='id_nontag_item[]']:checked")` — items never get collected, `non_tagged.length` is always 0.

## Root Cause
Three layered issues:

### Issue 1: Schema mismatch in SQL query
`fetchNonTaggedReceiptedItems` and `getNontagItemId` were written for the old schema (`id_section`, `id_sub_design`, `ret_sub_design_master`, `ret_section`). The migrated schema uses `id_collection`, `id_sub_product`, `ret_collection`, `ret_product_master` (for sub_product).

### Issue 2: id_nontag_receipt never saved
- **JS**: `trans_data` object at save time doesn't include `id_nontag_receipt`
- **Controller**: `$data` array for insert doesn't extract `id_nontag_receipt` from POST
- **Result**: `ret_branch_transfer.id_nontag_receipt` is always NULL → deduction subquery never matches → full receipt balance always shown

### Issue 3: Checkbox name mismatch
Receipt DataTable renders `<input type="checkbox" name="nt_item_sel[]">` but save flow uses `$("input[name='id_nontag_item[]']:checked")`. The selector never finds receipt items.

## Detection

### Detect Issue 1 (schema mismatch):
```command
grep -rn "id_sub_design\|ret_sub_design_master\|id_section.*ret_section" application/models/ret_brntransfer_model.php
```
If matches appear in `fetchNonTaggedReceiptedItems` or `getNontagItemId`, the fix is needed.

### Detect Issue 2 (missing id_nontag_receipt save):
```command
grep -n "id_nontag_receipt" application/controllers/admin_ret_brntransfer.php
```
If no results in the `case "save"` block (around line 83-263), the field isn't being saved.

### Detect Issue 3 (checkbox name mismatch):
```command
grep -n "nt_item_sel\[\]" assets/js/ret_branch_transfer.js
```
If `name="nt_item_sel[]"` exists on a checkbox in a DataTable that should feed into the `id_nontag_item[]` save flow, it's mismatched.

## Files
- `application/models/ret_brntransfer_model.php` — `fetchNonTaggedReceiptedItems()`, `getNontagItemId()`
- `application/controllers/admin_ret_brntransfer.php` — `case "save"` non-tag block
- `assets/js/ret_branch_transfer.js` — trans_data builder + receipt DataTable column definition

## Fix

### Fix 1: fetchNonTaggedReceiptedItems — Schema update

#### Before
```php
$sql = ("SELECT CONCAT(design_code,' - ',design_name) as design_name,CONCAT(product_short_code ,' - ',product_name) as product_name,
    (nt.grs_wt - ifnull(bt.grs_wt,0) ) as gross_wt,(nt.net_wt - ifnull(bt.net_wt,0) ) as net_wt,(nt.pcs - ifnull(bt.pieces,0) ) as no_of_piece,'' as id_lot_inward_detail,IFNULL(nt.id_section,'') as id_section,IFNULL(rs.section_name,'') as section_name,
    nt.id_sub_design,subDes.sub_design_name,nt.id_product,nt.id_design,nt.id_sub_design,nt.id_nontag_receipt
    
    FROM  ret_nontag_receipt nt 
    Left join ret_product_master p on p.pro_id = nt.id_product
    Left join ret_design_master d on d.design_no = nt.id_design
    LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = nt.id_sub_design
    left join ret_section rs on rs.id_section = nt.id_section
    ...
```

#### After
```php
$sql = ("SELECT CONCAT(design_code,' - ',design_name) as design_name,
    CONCAT(p.product_short_code ,' - ',p.product_name) as product_name,
    sp.product_name as sub_product,
    coll.collection_name,
    (nt.grs_wt - ifnull(bt.grs_wt,0) ) as gross_wt,
    (nt.net_wt - ifnull(bt.net_wt,0) ) as net_wt,
    (nt.pcs - ifnull(bt.pieces,0) ) as no_of_piece,
    '' as id_lot_inward_detail,
    nt.id_product, nt.id_design, nt.id_collection, nt.id_sub_product, nt.id_nontag_receipt
    
    FROM  ret_nontag_receipt nt 
    Left join ret_product_master p on p.pro_id = nt.id_product
    LEFT JOIN ret_product_master sp on sp.pro_id = nt.id_sub_product
    Left join ret_design_master d on d.design_no = nt.id_design
    LEFT JOIN ret_collection coll ON coll.id_collection = nt.id_collection
    ...
```

Key column mapping:
| Old | New |
|---|---|
| `id_section` / `ret_section` | `id_collection` / `ret_collection` |
| `id_sub_design` / `ret_sub_design_master` | `id_sub_product` / `ret_product_master sp` |
| `section_name` | `collection_name` |
| `sub_design_name` | `sub_product` (sp.product_name) |

### Fix 1b: Deduction subquery — remove non-existent table

#### Before (references non-existent `ret_brch_transfer_non_tag_items`):
```php
Left join (SELECT bt_nontag.id_nontag_receipt,...
    FROM `ret_brch_transfer_non_tag_items` bt_nontag
    LEFT JOIN ret_branch_transfer btrans ON btrans.branch_transfer_id = bt_nontag.transfer_id
    WHERE btrans.status != 3 GROUP BY bt_nontag.id_nontag_receipt) bt ...
```

#### After (uses ret_branch_transfer directly):
```php
Left join (SELECT bt_nt.id_nontag_receipt, SUM(bt_nt.grs_wt) as grs_wt, SUM(bt_nt.net_wt) as net_wt, SUM(bt_nt.pieces) as pieces, bt_nt.status
    FROM ret_branch_transfer bt_nt
    WHERE bt_nt.status != 3 AND bt_nt.id_nontag_receipt IS NOT NULL GROUP BY bt_nt.id_nontag_receipt) bt on bt.id_nontag_receipt=nt.id_nontag_receipt and bt.status != 3
```

### Fix 1c: Result array — update keys

#### Before
```php
$result[] = array(
    "section_name"     => $r['section_name'],
    "sub_design_name"  => $r['sub_design_name'],
    "id_sub_design"    => $r['id_sub_design'],
    "id_section"       => $r['id_section'],
    "id_nontag_item"   => $this->getNontagItemId(1, $r['id_section'], $r['id_product'], $r['id_design'], $r['id_sub_design']),
);
```

#### After
```php
$result[] = array(
    "collection_name"  => $r['collection_name'],
    "sub_product"      => $r['sub_product'],
    "id_collection"    => $r['id_collection'],
    "id_sub_product"   => $r['id_sub_product'],
    "id_nontag_item"   => $this->getNontagItemId($data['id_branch'], $r['id_collection'], $r['id_product'], $r['id_sub_product'], $r['id_design']),
);
```

### Fix 1d: getNontagItemId — update signature and WHERE clause

#### Before
```php
function getNontagItemId($id_branch, $id_section, $id_product, $id_design, $id_sub_design)
{
    $sql = $this->db->query("SELECT id_nontag_item 
        from ret_nontag_item
        where branch =" . $id_branch . " and id_section =" . $id_section . " 
        and product=" . $id_product . " and design=" . $id_design . " and id_sub_design=" . $id_sub_design . "");
```

#### After
```php
function getNontagItemId($id_branch, $id_collection, $id_product, $id_sub_product, $id_design)
{
    $sql = $this->db->query("SELECT id_nontag_item 
        from ret_nontag_item
        where branch =" . $id_branch . " and id_collection =" . $id_collection . " 
        and product=" . $id_product . " and id_sub_product=" . $id_sub_product . " " . ($id_design != '' ? " and design=" . $id_design : '') . "");
```

### Fix 2: Save id_nontag_receipt — Controller

#### Before (admin_ret_brntransfer.php, case "save", item_tag_type == 2):
```php
$data['id_nontag_item'] = (isset($nt_data['id_nontag_item']) && $nt_data['id_nontag_item'] != '' ? $nt_data['id_nontag_item'] : NULL);
$data['pieces'] = (isset($nt_data['pieces']) ? $nt_data['pieces'] : 0);
```

#### After
```php
$data['id_nontag_item'] = (isset($nt_data['id_nontag_item']) && $nt_data['id_nontag_item'] != '' ? $nt_data['id_nontag_item'] : NULL);
$data['id_nontag_receipt'] = (isset($nt_data['id_nontag_receipt']) && $nt_data['id_nontag_receipt'] != '' ? $nt_data['id_nontag_receipt'] : NULL);
$data['pieces'] = (isset($nt_data['pieces']) ? $nt_data['pieces'] : 0);
```

### Fix 3: JS trans_data — add id_nontag_receipt

#### Before (ret_branch_transfer.js, non-tag save data builder):
```javascript
var data = {
    'id_nontag_item': row.find('td:first .id_nontag_item').val(),
    'pieces': row.find('td:eq(3) .nt_piece').val(),
    'grs_wt': row.find('td:eq(4) .nt_gross_wt').val(),
    'net_wt': row.find('td:eq(5) .nt_net_wgt').val(),
    'id_lot_inward_detail': row.find('td:first .id_lot_inward_detail').val()
};
```

#### After
```javascript
var data = {
    'id_nontag_item': row.find('td:first .id_nontag_item').val(),
    'pieces': row.find('td:eq(3) .nt_piece').val(),
    'grs_wt': row.find('td:eq(4) .nt_gross_wt').val(),
    'net_wt': row.find('td:eq(5) .nt_net_wgt').val(),
    'id_lot_inward_detail': row.find('td:first .id_lot_inward_detail').val(),
    'id_nontag_receipt': row.find('td:first .id_nontag_receipt').val()
};
```

### Fix 4: Receipt DataTable checkbox name mismatch

#### Before (receipt DataTable column at getNonTaggedReceiptedItem):
```javascript
return '<input type="checkbox" name="nt_item_sel[]" class="nt_item_sel" value="'+row.nt_item_sel+'">' +
       '<input type="hidden" class="id_nontag_item" name="id_nontag_item[]" value="'+row.id_nontag_item+'">';
```

#### After
```javascript
return '<input type="checkbox" name="id_nontag_item[]" class="nt_item_sel id_nontag_item" value="'+row.id_nontag_item+'">' +
       '<input type="hidden" class="id_lot_inward_detail" name="id_lot_inward_detail[]" value="'+row.id_lot_inward_detail+'">' +
       '<input type="hidden" class="id_nontag_receipt" name="id_nontag_receipt[]" value="'+row.id_nontag_receipt+'">' +
       '<input type="hidden" class="id_nontag_item_val" name="id_nontag_item_val[]" value="'+row.id_nontag_item+'">';
```

Key changes:
- Checkbox `name` changed from `nt_item_sel[]` to `id_nontag_item[]` (matches save selector)
- Checkbox `class` keeps `nt_item_sel` (for calculateNTtotal click handler) and adds `id_nontag_item`
- Checkbox `value` changed from `row.nt_item_sel` to `row.id_nontag_item`
- Hidden `id_nontag_item` renamed to `id_nontag_item_val` (avoids name collision with checkbox)

## Verification
1. **Load receipt items**: Go to Branch Transfer > Add > select "To Branch" > select a Non-Tag Receipt from dropdown. Verify items load with Collection Name and Sub Product columns (not Section/Sub Design).
2. **Check items**: Select receipt items via checkbox. The totals (pieces, gross wt, net wt) should update.
3. **Save transfer**: Click save. Verify `ret_branch_transfer` record has `id_nontag_receipt` populated (not NULL).
4. **Verify deduction**: Load the same receipt again. Previously transferred quantities should be deducted from available balance.
5. **Verify no duplicate**: Try transferring the same receipt again — the available balance should reflect the previous transfer.
6. **DB check**: `SELECT id_nontag_receipt, grs_wt, net_wt FROM ret_branch_transfer WHERE id_nontag_receipt IS NOT NULL` — should show the receipt ID.

## Notes
- The `$type` variable (lot table vs non-tag table switch) was dead code — set but never used. Removed.
- The `tgrp` alias (legacy "tag group") was renamed to `sp` (sub product) for clarity.
- `design` is treated as optional in `getNontagItemId` since some non-tag items may not have a design.
- Regular non-tag items (not from receipts) are unaffected — `id_nontag_receipt` will be `undefined` in JS → NULL in PHP → no impact on existing flow.
- Prerequisite: `ret_branch_transfer` table must have `id_nontag_receipt` column (added via migration: `ALTER TABLE ret_branch_transfer ADD COLUMN id_nontag_receipt INT NULL`).
- Also ensure `ret_nontag_receipt` has `id_collection` and `id_sub_product` columns.
