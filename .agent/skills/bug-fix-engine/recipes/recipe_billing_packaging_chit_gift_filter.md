# Billing Packaging Items — Chit Gift Filter

> Chit-mapped gifts from the Other Inventory module leak into the Billing Page Packaging Items dropdown because the model query has no awareness of the `gift_mapping` table.

## Metadata
- **Pattern ID**: PAT-OTH-001
- **Severity**: MEDIUM
- **Modules Affected**: Billing, Other Inventory
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Any client using both the Chit Scheme Gift Mapping and the Billing Packaging Items feature will encounter this. The query is shared across all client codebases.

## Created By
- **Developer**: Antigravity
- **Client**: Portmax (RTM source)
- **Date**: 2026-06-13
- **Source Bug ID**: N/A

## Symptom
Gifts that are mapped to chit schemes (via Other Inventory → Gift Mapping) appear in the Billing Page under the **Packaging Items** tab dropdown. Users see chit-only items like appliances (cookers, fans, mixers) mixed in with actual packaging boxes and bags, causing confusion during retail billing.

## Root Cause
The model function `get_invnetory_item()` in `ret_other_inventory_model.php` fetches all inventory items with available stock (`tot_pcs > 0`) from `ret_other_inventory_item`. It has no filter to exclude items that are linked to chit schemes via the `gift_mapping` table. The billing JS (`ret_billing.js`) calls this same endpoint without any context parameter, so the model cannot distinguish between billing and other callers.

**Two gaps exist:**
1. No `LEFT JOIN` on `gift_mapping` to detect chit-mapped items
2. No `source` parameter to apply context-specific filtering

## Detection
```command
grep -n "get_invnetory_item" admin/application/models/ret_other_inventory_model.php
grep -n "gift_mapping" admin/application/models/ret_other_inventory_model.php
```
If `get_invnetory_item` exists but `gift_mapping` is NOT referenced in that function — the bug is present.

## Files
- `admin/application/models/ret_other_inventory_model.php` — model query (primary fix)
- `admin/assets/js/ret_billing.js` — AJAX caller (adds `source` parameter)

## Fix

### File 1: `admin/application/models/ret_other_inventory_model.php`

#### Before
```php
function get_invnetory_item($data)
{
    $responseData = array();
    $sql = $this->db->query("SELECT i.name as item_name,IFNULL(d.tot_pcs,0) as tot_pcs,i.id_other_item,IFNULL(i.item_image,'') as item_image,i.sku_id,
        IFNULL(bt.brch_pcs,0) as brch_pcs
        FROM ret_other_inventory_item_type t 
        LEFT JOIN ret_other_inventory_item i ON i.item_for=t.id_other_item_type
        LEFT JOIN (SELECT IFNULL(SUM(p.no_of_pcs),0) as brch_pcs,p.id_other_inv_item
        FROM ret_branch_transfer_other_inventory p
        LEFT JOIN ret_branch_transfer b ON b.branch_transfer_id = p.branch_transfer_id
        WHERE (b.status = 1) 
        GROUP BY p.id_other_inv_item) as bt ON bt.id_other_inv_item = i.id_other_item
        LEFT JOIN (SELECT d.other_invnetory_item_id,IFNULL(SUM(d.piece),0) as tot_pcs
        FROM ret_other_inventory_purchase_items_details d
        WHERE d.status=0 
        " . ($data['id_branch'] != '' ? " and d.current_branch=" . $data['id_branch'] . "" : '') . " 
        GROUP by d.other_invnetory_item_id) as d on d.other_invnetory_item_id=i.id_other_item
        having tot_pcs>0");
```

#### After
```php
function get_invnetory_item($data)
{
    $responseData = array();
    $source = isset($data['source']) ? $data['source'] : '';
    // Exclude chit-mapped gift items when loading for billing/estimation
    $chit_filter = '';
    if ($source == 'billing') {
        $chit_filter = " AND i.issue_to != 1 AND gm.id_item_mapping IS NULL";
    }
    $sql = $this->db->query("SELECT i.name as item_name,IFNULL(d.tot_pcs,0) as tot_pcs,i.id_other_item,IFNULL(i.item_image,'') as item_image,i.sku_id,
        IFNULL(bt.brch_pcs,0) as brch_pcs
        FROM ret_other_inventory_item_type t 
        LEFT JOIN ret_other_inventory_item i ON i.item_for=t.id_other_item_type
        LEFT JOIN (SELECT IFNULL(SUM(p.no_of_pcs),0) as brch_pcs,p.id_other_inv_item
        FROM ret_branch_transfer_other_inventory p
        LEFT JOIN ret_branch_transfer b ON b.branch_transfer_id = p.branch_transfer_id
        WHERE (b.status = 1) 
        GROUP BY p.id_other_inv_item) as bt ON bt.id_other_inv_item = i.id_other_item
        LEFT JOIN (SELECT d.other_invnetory_item_id,IFNULL(SUM(d.piece),0) as tot_pcs
        FROM ret_other_inventory_purchase_items_details d
        WHERE d.status=0 
        " . ($data['id_branch'] != '' ? " and d.current_branch=" . $data['id_branch'] . "" : '') . " 
        GROUP by d.other_invnetory_item_id) as d on d.other_invnetory_item_id=i.id_other_item
        LEFT JOIN (SELECT DISTINCT id_other_item, MIN(id_item_mapping) as id_item_mapping FROM gift_mapping GROUP BY id_other_item) as gm ON gm.id_other_item = i.id_other_item
        WHERE i.id_other_item IS NOT NULL " . $chit_filter . "
        having tot_pcs>0");
```

### File 2: `admin/assets/js/ret_billing.js`

#### Before
```javascript
data: { id_branch: id_branch },
```

#### After
```javascript
data: { id_branch: id_branch, source: 'billing' },
```

## Verification
1. Open Billing → Add (`index.php/admin_ret_billing/billing/add`)
2. Click the **Packaging Item** tab → Click **Add** button
3. Open the item dropdown — items present in the `gift_mapping` table (e.g., items mapped to chit schemes) should NOT appear
4. Items with `issue_to = 1` (Chit Customer only) should also NOT appear
5. Regular packaging items (boxes, purses, bags) should still appear normally
6. Open Other Inventory → Issue Item — verify ALL items still appear (no filter applied since no `source` param)
7. Open Branch Transfer → verify packaging items still show correctly

## Notes
- The `source` parameter approach ensures backward compatibility. Only the billing JS passes `source: 'billing'`; all other callers (Other Inventory, Branch Transfer, Estimation) are unaffected.
- The Estimation module uses a completely different endpoint (`get_productMappedDetails`) and is not affected by this change.
- The `issue_to` column values: `0` = All, `1` = Chit Customer, `2` = Retail Customer. The filter excludes `issue_to = 1` as an additional safety net.
- The `gift_mapping` LEFT JOIN uses a subquery with `GROUP BY` to produce one row per item, avoiding result multiplication.
