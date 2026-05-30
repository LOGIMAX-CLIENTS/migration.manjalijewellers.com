# Recipe: Hallmarking Issue Screen Shows Wrong POs

## Metadata
- **Pattern ID**: PAT-QRY-007
- **Severity**: MEDIUM (P2)
- **Modules Affected**: Purchase (Hallmarking Issue / Approval Hallmarking)
- **Auto-fixable**: Yes — add `and p.is_po_halmarked = 0` to WHERE clause + fix `= null` → `IS NULL`

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients use the same hallmarking screen with the same model methods.

## Created By
- **Developer**: dev-rudra-619
- **Client**: coswan (etail_development_src)
- **Date**: 2026-04-03
- **Source Bug ID**: GitHub #1791 (PUR-HM-001)

## Symptom
The Hallmarking Issue screen's PO Ref No dropdown shows incorrect POs:
- POs where supplier already hallmarked items (`is_po_halmarked = 1`) appear — they should be hidden
- POs that legitimately need hallmarking (`is_po_halmarked = 0`) may not appear due to missing filter or wrong SQL null comparison

## Root Cause
**Dual issue in 4 SQL queries across 2 model files:**

1. **Missing `is_po_halmarked` filter** — The WHERE clause did not filter by `is_po_halmarked`, so both YES and NO hallmark POs appeared.

2. **Invalid null comparison** — `i.is_halmarked = null` is invalid SQL (always evaluates to UNKNOWN, never TRUE). Should be `IS NULL`. This caused items with NULL values to be silently excluded.

**Business Rule**:
| `is_po_halmarked` | Meaning | Should appear in Hallmarking Issue? |
|---|---|---|
| `0` (NO) | Items NOT yet hallmarked — need hallmarking | YES |
| `1` (YES) | Items already hallmarked by supplier | NO |

## Detection
```bash
# Find the affected methods in purchase models
grep -n "get_pending_halmarking_items\|get_halmarking_items" admin/application/models/ret_purchase_order_model.php admin/application/models/ret_purchase_approval_model.php

# Check for missing is_po_halmarked filter
grep -n "is_po_halmarked" admin/application/models/ret_purchase_order_model.php admin/application/models/ret_purchase_approval_model.php

# Check for invalid null comparison
grep -n "= null" admin/application/models/ret_purchase_order_model.php admin/application/models/ret_purchase_approval_model.php
```

## Files
- `admin/application/models/ret_purchase_order_model.php` (methods: `get_pending_halmarking_items`, `get_halmarking_items`)
- `admin/application/models/ret_purchase_approval_model.php` (methods: `get_pending_halmarking_items`, `get_halmarking_items`)

## Fix

### Before (ret_purchase_order_model.php — get_pending_halmarking_items)
```php
WHERE p.po_id IS NOT NULL and p.is_approved = 1 and i.is_halmarked=0 and i.is_lot_created=0
and qc.status =1
```

### After
```php
WHERE p.po_id IS NOT NULL and p.is_approved = 1 and p.is_po_halmarked = 0 and (i.is_halmarked=0 or i.is_halmarked IS NULL) and i.is_lot_created=0
and qc.status =1
```

---

### Before (ret_purchase_order_model.php — get_halmarking_items)
```php
WHERE (po_itm.is_halmarked=0 or po_itm.is_halmarked=null) and po_itm.is_lot_created = 0 and po.is_approved = 1
```

### After
```php
WHERE (po_itm.is_halmarked=0 or po_itm.is_halmarked IS NULL) and po_itm.is_lot_created = 0 and po.is_approved = 1 and po.is_po_halmarked = 0
```

---

### Before (ret_purchase_approval_model.php — get_pending_halmarking_items)
```php
WHERE p.po_id IS NOT NULL and (i.is_halmarked=0 or i.is_halmarked is null) and i.status=2 GROUP by i.po_item_po_id
```

### After
```php
WHERE p.po_id IS NOT NULL and p.is_po_halmarked = 0 and (i.is_halmarked=0 or i.is_halmarked is null) and i.status=2 GROUP by i.po_item_po_id
```

---

### Before (ret_purchase_approval_model.php — get_halmarking_items)
```php
WHERE (i.is_halmarked=0 or i.is_halmarked is null) and i.status!=3
```

### After
```php
WHERE (i.is_halmarked=0 or i.is_halmarked is null) and i.status!=3 and p.is_po_halmarked = 0
```

## Verification
1. Create a Supplier Bill Entry with **Hallmark = NO** (`is_po_halmarked = 0`)
2. Complete QC Issue + QC Receipt for that PO
3. Navigate to Hallmarking Issue screen -> PO Ref No dropdown
4. The Hallmark=NO PO should appear
5. POs with Hallmark=YES (`is_po_halmarked = 1`) should NOT appear
6. DB sanity check:
```sql
SELECT p.po_ref_no, p.is_po_halmarked
FROM ret_purchase_order_items i
LEFT JOIN ret_po_qc_issue_details qc ON qc.po_item_id = i.po_item_id
LEFT JOIN ret_purchase_order p ON p.po_id = i.po_item_po_id
WHERE p.is_approved = 1 AND p.is_po_halmarked = 0
  AND (i.is_halmarked = 0 OR i.is_halmarked IS NULL)
  AND i.is_lot_created = 0 AND qc.status = 1
GROUP BY i.po_item_po_id;
-- Should return ONLY is_po_halmarked=0 rows
```

## Notes
- `is_po_halmarked` is set from the "Hallmark" radio button (YES/NO) during Supplier Bill Entry form.
- `= null` in SQL is NEVER true — always use `IS NULL`. This is a common SQL mistake.
- The prerequisite for appearing in Hallmarking Issue is: QC must be completed (`qc.status = 1`) BEFORE Hallmarking Issue. This is correct business flow, not a bug.
- Workflow: Bill Entry -> QC Issue -> QC Receipt -> Hallmarking Issue -> Hallmarking Receipt
