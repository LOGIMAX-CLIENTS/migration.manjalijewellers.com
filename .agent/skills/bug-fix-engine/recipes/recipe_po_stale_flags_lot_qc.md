# Recipe: Stale PO Item Flags (is_lot_created, qc_failed_pcs)

> Two fields on `ret_purchase_order_items` are unreliable — never updated or conditionally skipped.
> Any feature that depends on these fields will produce incorrect results.

## Metadata
- **Pattern ID**: PAT-PUR-001
- **Severity**: HIGH
- **Modules Affected**: Purchase Order, Supplier Payment, Lot Generation, QC
- **Auto-fixable**: No (requires subquery redesign per usage)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core table design issue — all clients using the same PO/QC/Lot flow

## Created By
- **Developer**: Antigravity
- **Client**: AMS-RetailAdmin (ams_25)
- **Date**: 2026-04-18
- **Source Bug ID**: N/A (discovered during PO payment completion check feature)

## Symptom
1. **`is_lot_created` stays 0** even after lot is generated — happens when `is_halmarked` is NULL on the PO item. The lot generation update query (`get_purchase_orders_by_product`) filters `is_halmarked=1`, so non-hallmarked items are skipped.
2. **`qc_failed_pcs` stays NULL/0** even after QC receipt records failures — the QC completion flow writes `failed_pcs` to `ret_po_qc_issue_details` but never updates `qc_failed_pcs` on `ret_purchase_order_items`.

## Root Cause

### Bug 1: `is_lot_created` not updated
- `get_purchase_orders_by_product()` at model line ~1231 filters `i.is_halmarked=1 AND d.is_lot_created=0`
- If `is_halmarked = NULL`, the WHERE clause excludes the item
- The `updateMultipleWhereData()` at controller line ~2566 never fires for these items
- But `ret_lot_inwards_detail` DOES get the record with correct `po_item_id`

### Bug 2: `qc_failed_pcs` not updated
- QC receipt writes `failed_pcs` to `ret_po_qc_issue_details` only
- No code path updates `ret_purchase_order_items.qc_failed_pcs`
- The field exists but is always NULL/0

## Detection
```command
grep -rn "i.qc_failed_pcs" application/models/ret_purchase_order_model.php
grep -rn "i.is_lot_created" application/models/ret_purchase_order_model.php
```
Any query using these fields directly from `ret_purchase_order_items` is potentially wrong.

## Files
- `application/models/ret_purchase_order_model.php` — any query reading `is_lot_created`, `qc_failed_pcs`, `po_returned_pcs` from `ret_purchase_order_items`
- `application/controllers/admin_ret_purchase.php` — lot generation update at ~L2566 (root cause of stale `is_lot_created`)

## Fix

### Before
```sql
-- WRONG: Using stale fields directly from ret_purchase_order_items
WHEN i.is_lot_created = 1 THEN 'lot_done'
-- and
IFNULL(i.qc_failed_pcs, 0)
-- and
IFNULL(i.po_returned_pcs, 0)
```

### After
```sql
-- CORRECT: Using source tables via LEFT JOIN
-- For lot check:
LEFT JOIN (
    SELECT po_item_id FROM ret_lot_inwards_detail WHERE po_item_id IS NOT NULL GROUP BY po_item_id
) lot_det ON lot_det.po_item_id = i.po_item_id
-- Then: (i.is_lot_created = 1 OR lot_det.po_item_id IS NOT NULL)

-- For QC failed:
LEFT JOIN (
    SELECT d.po_item_id, SUM(d.failed_pcs) as failed_pcs
    FROM ret_po_qc_issue_details d
    LEFT JOIN ret_po_qc_issue_process p ON p.qc_process_id = d.qc_process_id
    WHERE p.qc_status = 1
    GROUP BY d.po_item_id
) qc ON qc.po_item_id = i.po_item_id
-- Then: IFNULL(qc.failed_pcs, 0)

-- For returned pcs:
LEFT JOIN (
    SELECT pri.pur_ret_po_item_id, SUM(pri.pur_ret_pcs) as returned_pcs
    FROM ret_purchase_return_items pri
    LEFT JOIN ret_purchase_return pr ON pr.pur_return_id = pri.pur_ret_id
    WHERE pr.bill_status = 1
    GROUP BY pri.pur_ret_po_item_id
) ret ON ret.pur_ret_po_item_id = i.po_item_id
-- Then: IFNULL(ret.returned_pcs, 0)
```

## Reliable Alternatives

### Instead of `ret_purchase_order_items.is_lot_created`:
```sql
-- Check BOTH the flag AND the lot detail table
LEFT JOIN (
    SELECT po_item_id FROM ret_lot_inwards_detail WHERE po_item_id IS NOT NULL GROUP BY po_item_id
) lot_det ON lot_det.po_item_id = i.po_item_id

-- Then use:
(i.is_lot_created = 1 OR lot_det.po_item_id IS NOT NULL)
```

### Instead of `ret_purchase_order_items.qc_failed_pcs`:
```sql
-- Get actual failed pcs from QC details table
LEFT JOIN (
    SELECT d.po_item_id, SUM(d.failed_pcs) as failed_pcs
    FROM ret_po_qc_issue_details d
    LEFT JOIN ret_po_qc_issue_process p ON p.qc_process_id = d.qc_process_id
    WHERE p.qc_status = 1
    GROUP BY d.po_item_id
) qc ON qc.po_item_id = i.po_item_id

-- Then use:
IFNULL(qc.failed_pcs, 0)
```

### Instead of `ret_purchase_order_items.po_returned_pcs`:
```sql
-- Get actual returned pcs from purchase return items
LEFT JOIN (
    SELECT pri.pur_ret_po_item_id, SUM(pri.pur_ret_pcs) as returned_pcs
    FROM ret_purchase_return_items pri
    LEFT JOIN ret_purchase_return pr ON pr.pur_return_id = pri.pur_ret_id
    WHERE pr.bill_status = 1
    GROUP BY pri.pur_ret_po_item_id
) ret ON ret.pur_ret_po_item_id = i.po_item_id
```

## Verification
1. Check PO with `is_halmarked = NULL` — lot should still be detected via `ret_lot_inwards_detail`
2. Check PO with QC failures — `failed_pcs` should come from `ret_po_qc_issue_details`, not `ret_purchase_order_items`
3. Check PO with purchase returns — `returned_pcs` should come from `ret_purchase_return_items`

## Notes
- Three stale fields identified on `ret_purchase_order_items`: `is_lot_created`, `qc_failed_pcs`, `po_returned_pcs`
- These fields were likely intended as denormalized caches but the update logic is incomplete
- Any new feature querying PO processing status MUST use the source tables, not these stale fields
- The lot generation code at controller line ~2566 should ideally be fixed to handle NULL `is_halmarked`, but that's a separate fix with wider impact
