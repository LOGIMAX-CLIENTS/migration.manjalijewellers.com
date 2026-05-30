# Recipe: Non-Tag Receipt — Lot & Product Dropdown Visibility

## Metadata
- **Pattern ID**: PAT-LOT-001
- **Severity**: HIGH
- **Modules Affected**: Non-Tag Receipt (`admin_ret_purchase/nontag_receipt`)
- **Auto-fixable**: No (requires SQL logic understanding)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core lot receipt module shared across all clients

## Created By
- **Developer**: Antigravity
- **Client**: Sri Amman Jewellers (erp.sriammanjewellers.in)
- **Date**: 2026-05-07
- **Source Bug ID**: N/A

## Symptom

1. **Lot not appearing in dropdown**: A partially processed lot (some items receipted, some pending) disappears from the Non-Tag Receipt "Lot No" dropdown, even though items with remaining balance still exist.
2. **Duplicate products in dropdown**: The "Select Product" dropdown shows completed items (tag_status=1) alongside pending items — sometimes the same product name appears twice.

## Root Cause

**Bug 1 — Lot dropdown:**
`get_NontagLots()` in `ret_purchase_order_model.php` had a subquery that summed receipts (`ret_nontag_receipt`) grouped only by `lot_id`. This meant if multiple detail rows existed for the same lot, the total receipted weight of ALL rows was compared against each individual row's weight. If any row was fully receipted, the cumulative sum would exceed a pending row's weight, causing it to fail the `>` check and exclude the lot.

**Bug 2 — Product dropdown:**
`get_lot_nontag_details()` had no `tag_status` filter, so fully receipted detail rows (`tag_status=1`) were included in the product dropdown. In lots where the same product appears in multiple detail rows (one completed, one pending), users saw the product listed twice — selecting the completed one caused "No Records to Update".

## Detection

```powershell
# Bug 1: Look for get_NontagLots missing id_lot_inward_detail in GROUP BY
Select-String -Path "admin\application\models\ret_purchase_order_model.php" -Pattern "GROUP BY nt\.lot_id\b" 

# Bug 2: Look for get_lot_nontag_details missing tag_status filter
Select-String -Path "admin\application\models\ret_purchase_order_model.php" -Pattern "function get_lot_nontag_details" 
# Then verify the WHERE clause lacks: ltd.tag_status = 0
```

## Files
- `admin/application/models/ret_purchase_order_model.php`
- `admin/assets/js/ret_purchase_order.js`

## Fix

### Bug 1 — `get_NontagLots()`: Group by lot + detail row

#### Before
```php
LEFT JOIN (
    SELECT nt.lot_id, SUM(nt.pcs) AS nt_pcs, SUM(nt.grs_wt) AS nt_grs_wt
    FROM ret_nontag_receipt nt
    LEFT JOIN ret_lot_inwards rlt ON rlt.lot_no = nt.lot_id
    GROUP BY nt.lot_id
) AS ntl ON ntl.lot_id = lt.lot_no
```

#### After
```php
LEFT JOIN (
    SELECT nt.lot_id, nt.id_lot_inward_detail, SUM(nt.pcs) AS nt_pcs, SUM(nt.grs_wt) AS nt_grs_wt
    FROM ret_nontag_receipt nt
    LEFT JOIN ret_lot_inwards rlt ON rlt.lot_no = nt.lot_id
    GROUP BY nt.lot_id, nt.id_lot_inward_detail
) AS ntl ON ntl.lot_id = lt.lot_no AND ntl.id_lot_inward_detail = ltd.id_lot_inward_detail
```

---

### Bug 1b — `getNonTagLotItemDetails()`: ntag subquery with GROUP BY + null-safe JOIN

#### Before
```php
LEFT JOIN (SELECT nt.lot_id,nt.id_lot_inward_detail,ifnull(sum(nt.pcs),0) as nt_pcs,ifnull(sum(nt.grs_wt),0) as nt_grswt,
ifnull(sum(nt.less_wt),0) as nt_less_wt,ifnull(sum(nt.net_wt),0) as nt_net_wt
FROM ret_nontag_receipt nt
WHERE nt.lot_id=".$data['lot_no']."  and nt.id_lot_inward_detail = ".$data['id_lot_inward_detail']." and nt.id_product=".$data['id_product'].")
as ntag on ntag.id_lot_inward_detail = ltd.id_lot_inward_detail
```

#### After
```php
LEFT JOIN (SELECT nt.lot_id, nt.id_lot_inward_detail, ifnull(sum(nt.pcs),0) as nt_pcs, ifnull(sum(nt.grs_wt),0) as nt_grswt,
ifnull(sum(nt.less_wt),0) as nt_less_wt, ifnull(sum(nt.net_wt),0) as nt_net_wt
FROM ret_nontag_receipt nt
WHERE nt.lot_id=".$data['lot_no']." AND nt.id_product=".$data['id_product']."
GROUP BY nt.lot_id, nt.id_lot_inward_detail) as ntag ON ntag.lot_id = lt.lot_no
AND (ntag.id_lot_inward_detail = ltd.id_lot_inward_detail OR ntag.id_lot_inward_detail IS NULL OR ntag.id_lot_inward_detail = 0)
```

> **Why OR NULL?** Defensive guard for legacy receipts saved before `id_lot_inward_detail` was tracked.

---

### Bug 2 — `get_lot_nontag_details()`: Filter to pending items only

#### Before
```php
WHERE lt.stock_type=2 and lt.is_closed = 0 and lt.lot_no=".$data['lot_no']."
group by ltd.id_lot_inward_detail
```

#### After
```php
WHERE lt.stock_type=2 and lt.is_closed = 0 and lt.lot_no=".$data['lot_no']." and ltd.tag_status = 0
group by ltd.id_lot_inward_detail
```

---

### Bug 3 — JS Completed Display: Derive from Lot − Balance

In `ret_purchase_order.js`, inside the `getNonTagLotItemDetails` success callback:

#### Before
```javascript
if(item.completedLot.length>0) {
    $.each(item.completedLot, function (key, val) {
        $('.disp_lot_ntag_pcs').html(val.nt_pcs);
        $('.disp_lot_ntag_wt').html(val.nt_grswt);
        $('.disp_lot_ntag_nwt').html(val.nt_net_wt);
    });
}
```

#### After
```javascript
// Inside the lotdet loop — derive Completed = Lot − Balance
var comp_pcs = parseInt(val.no_of_piece || 0) - parseInt(item.pcs || 0);
var comp_wt  = parseFloat(val.gross_wt  || 0) - parseFloat(item.grs_wt  || 0);
var comp_nwt = parseFloat(val.net_wt    || 0) - parseFloat(item.net_wt   || 0);

$('.disp_lot_ntag_pcs').html(comp_pcs > 0 ? comp_pcs : 0);
$('.disp_lot_ntag_wt').html(comp_wt   > 0 ? parseFloat(comp_wt).toFixed(3)  : '0.000');
$('.disp_lot_ntag_nwt').html(comp_nwt  > 0 ? parseFloat(comp_nwt).toFixed(3) : '0.000');
// Remove the separate completedLot block entirely
```

## Verification

1. Open `nontag_receipt/add` — select a lot where some items are fully receipted and some are pending
2. **Lot dropdown** → Only lots with at least one pending detail row (`tag_status=0, balance>0`) should appear
3. **Product dropdown** → Only products with `tag_status=0` should appear — no duplicates
4. **Completed row** → Shows sum of prior receipts for the selected item (0 if never receipted)
5. **Balance row** → Shows Lot − Completed correctly

## Notes

- This bug typically surfaces when a lot contains the **same product in multiple detail rows** (e.g., SBASARY18C split into two rows of 6.190g and 44.470g)
- The `completedLot` PHP query (`getCompletedNonTag`) is still called server-side and is correct — the JS derived approach is a simpler, more reliable alternative
- If `getLotNotTag` shows the same weight as Balance, verify whether `getNonTagLotItemDetails` ntag subquery is matching receipts correctly (check GROUP BY and JOIN condition)
