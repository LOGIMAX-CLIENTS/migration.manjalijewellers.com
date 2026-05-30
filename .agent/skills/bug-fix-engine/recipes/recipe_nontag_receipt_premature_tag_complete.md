# Recipe: Non-Tag Receipt — Premature tag_status=1 on Partial Receipt

## Metadata
- **Pattern ID**: PAT-LOT-002
- **Severity**: CRITICAL
- **Modules Affected**: Non-Tag Receipt (`admin_ret_purchase/nontag_receipt`)
- **Auto-fixable**: No (requires parameter propagation)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core lot receipt module shared across all clients

## Created By
- **Developer**: Antigravity
- **Client**: Sri Amman Jewellers (erp.sriammanjewellers.in)
- **Date**: 2026-05-07
- **Source Bug ID**: N/A

## Symptom

After saving a **partial receipt** (e.g., 3g out of 6.190g), the item **immediately disappears** from the lot dropdown on the next visit — as if it is fully completed — even though significant weight remains unprocessed.

**DB evidence**: `ret_lot_inwards_detail.tag_status` is set to `1` even though `SUM(ret_nontag_receipt.grs_wt)` for that specific detail row is less than `ret_lot_inwards_detail.gross_wt`.

## Root Cause

The save function in `nontag_receipt('save')` uses two model functions to decide whether an item is fully receipted:

1. `getLottedNotTagWt($lot_no, $id_product, $id_design, $id_sub_design)` — returns the lot detail's `gross_wt`
2. `getReceiptedNontagWt($lot_no, $id_product, $id_design, $id_sub_design)` — returns `SUM(grs_wt)` from `ret_nontag_receipt`

**Neither function filters by `id_lot_inward_detail`.**

When the same product exists in multiple detail rows within a lot (e.g., SBASARY18C: detail 1461 = 6.190g, detail 1463 = 44.470g), `getReceiptedNontagWt` sums receipts from **all rows** for that product:

```
receiptedWt = 44.470 (from detail 1463, already done) + 3.000 (current) = 47.470
lottedWt    = 6.190  (detail 1461, the one being processed)
47.470 >= 6.190 → TRUE → tag_status = 1 ← WRONG!
```

## Detection

```powershell
# Find the save logic calling these two functions without id_lot_inward_detail
Select-String -Path "admin\application\controllers\admin_ret_purchase.php" -Pattern "getLottedNotTagWt|getReceiptedNontagWt"

# Verify the model functions lack id_lot_inward_detail parameter
Select-String -Path "admin\application\models\ret_purchase_order_model.php" -Pattern "function getLottedNotTagWt|function getReceiptedNontagWt"
```

**Confirm bug exists**: If neither function signature includes `$id_lot_inward_detail`, this bug is present.

## Files
- `admin/application/controllers/admin_ret_purchase.php`
- `admin/application/models/ret_purchase_order_model.php`

## Fix

### Step 1 — Update `getLottedNotTagWt()` in model

#### Before
```php
function getLottedNotTagWt($lot_no,$id_product,$id_design,$id_sub_design)
{
    $sql = $this->db->query("SELECT lt.lot_no,lt.stock_type,ltd.id_lot_inward_detail,ltd.gross_wt
    FROM ret_lot_inwards lt
    LEFT JOIN ret_lot_inwards_detail ltd on ltd.lot_no = lt.lot_no
    LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_po_id = lt.po_id
    WHERE lt.stock_type =2 and lt.is_closed = 0 and lt.lot_no = ".$lot_no." and ltd.lot_product = ".$id_product."
    and po_itm.po_item_des_id = ".$id_design."  and po_itm.po_item_sub_des_id = ".$id_sub_design."");
    return $sql->row_array();
}
```

#### After
```php
function getLottedNotTagWt($lot_no,$id_product,$id_design,$id_sub_design,$id_lot_inward_detail='')
{
    $where = "lt.stock_type =2 and lt.is_closed = 0 and lt.lot_no = ".$lot_no." and ltd.lot_product = ".$id_product;
    if($id_lot_inward_detail != '') {
        $where .= " and ltd.id_lot_inward_detail = ".$id_lot_inward_detail;
    }
    $sql = $this->db->query("SELECT lt.lot_no,lt.stock_type,ltd.id_lot_inward_detail,ltd.gross_wt
    FROM ret_lot_inwards lt
    LEFT JOIN ret_lot_inwards_detail ltd on ltd.lot_no = lt.lot_no
    WHERE ".$where."");
    return $sql->row_array();
}
```

---

### Step 2 — Update `getReceiptedNontagWt()` in model

#### Before
```php
function getReceiptedNontagWt($lot_no,$id_product,$id_design,$id_sub_design)
{
    $sql = $this->db->query("SELECT sum(ntr.grs_wt) as nt_grs_wt
    from ret_nontag_receipt ntr
    where ntr.lot_id =".$lot_no." and ntr.id_product=".$id_product." and ntr.id_design=".$id_design." and ntr.id_sub_design=".$id_sub_design."");
    return $sql->row()->nt_grs_wt;
}
```

#### After
```php
function getReceiptedNontagWt($lot_no,$id_product,$id_design,$id_sub_design,$id_lot_inward_detail='')
{
    $where = "ntr.lot_id =".$lot_no." and ntr.id_product=".$id_product;
    if($id_lot_inward_detail != '') {
        $where .= " and ntr.id_lot_inward_detail = ".$id_lot_inward_detail;
    } else {
        $where .= " and ntr.id_design=".$id_design." and ntr.id_sub_design=".$id_sub_design;
    }
    $sql = $this->db->query("SELECT sum(ntr.grs_wt) as nt_grs_wt
    from ret_nontag_receipt ntr
    where ".$where."");
    return $sql->row()->nt_grs_wt;
}
```

---

### Step 3 — Pass `id_lot_inward_detail` from controller

In `admin_ret_purchase.php`, inside `nontag_receipt('save')`:

#### Before
```php
$lottedWt = $this->$model->getLottedNotTagWt($addData['id_lot'],$addData['id_product'],$addData['id_design'],$addData['id_sub_design']);
$receiptedNontagWt = $this->$model->getReceiptedNontagWt($addData['id_lot'],$addData['id_product'],$addData['id_design'],$addData['id_sub_design']);
```

#### After
```php
$lottedWt = $this->$model->getLottedNotTagWt($addData['id_lot'],$addData['id_product'],$addData['id_design'],$addData['id_sub_design'],$addData['id_lot_inward_detail']);
$receiptedNontagWt = $this->$model->getReceiptedNontagWt($addData['id_lot'],$addData['id_product'],$addData['id_design'],$addData['id_sub_design'],$addData['id_lot_inward_detail']);
```

## Verification

1. Create a lot with the **same product in 2 detail rows** (different weights, e.g., 6g and 44g)
2. Fully receipt the 44g row → `tag_status = 1` for that row only ✓
3. Partially receipt the 6g row (e.g., 3g) → `tag_status` must stay `0` ✓
4. Open the add form again → the 6g item must still appear in the product dropdown ✓
5. Completed row shows 3g, Balance shows 3g ✓
6. Receipt the remaining 3g → `tag_status = 1` → item disappears from dropdown ✓

## Notes

- **DB recovery**: If a detail row was wrongly set to `tag_status=1`, run:
  ```sql
  UPDATE ret_lot_inwards_detail SET tag_status = 0 WHERE id_lot_inward_detail = {id};
  -- Also delete the bad receipt if needed:
  DELETE FROM ret_nontag_receipt WHERE lot_id = {lot} AND id_lot_inward_detail = {id};
  ```
- This bug is **silent** — no error is thrown, the item just silently disappears
- The `$id_lot_inward_detail` value is always available in `$_POST['nt_receipt']['id_lot_inward_detail']` — set by the hidden input `#id_lot_inward_detail` in the form, populated by `getNonTagLotItemDetails` AJAX response
