# Bug: Tag Wise Profit Report — Empty Purchase Stone Amounts for Non-PO Lots

## Symptom
In the Tag Wise Profit report (`admin_ret_reports/tag_wise_profit/list`), the **Purchase STN AMT**, **Purchase DIA AMT**, and **Purchase Rate** columns show `0.00` for tags that belong to lots without a Purchase Order (where `ret_lot_inwards.po_id IS NULL`).

The Sales columns show correct stone amounts. The tag history page shows stones with amounts (e.g., STUDDED DIAMOND=10000, Pearl=15000), but these don't appear in the profit report's purchase section.

## Root Cause
The `tag_stn` subquery in `get_wastagewisepandlreport()` calculates purchase stone amounts using:

```sql
billstn.wt * IFNULL(purstn.po_stone_rate, IFNULL(tagstn.pur_rate, 0))
```

**Three-level rate lookup fails for non-PO lots:**
1. `purstn.po_stone_rate` = NULL → No PO exists (`ret_lot_inwards.po_id IS NULL`), so the `ret_po_stone_items` JOIN returns nothing
2. `tagstn.pur_rate` = 0.00 → Stone purchase rates were never entered in `ret_taging_stone.pur_rate`
3. Result: `wt × 0 = 0` → purchase stone amounts show as empty

**But the actual stone purchase costs exist** in `ret_taging_stone.amount` (the total stone cost entered during tagging). The query never falls back to this column.

### Data pattern for affected records
```sql
-- Tags with stones that have amounts but no purchase rate, from non-PO lots
SELECT tag.tag_code, ts.stone_id, ts.pur_rate, ts.amount, l.po_id
FROM ret_taging tag
JOIN ret_taging_stone ts ON ts.tag_id = tag.tag_id
JOIN ret_lot_inwards l ON l.lot_no = tag.tag_lot_id
WHERE l.po_id IS NULL AND ts.pur_rate = 0 AND ts.amount > 0
```

## Fix (1 change)

### Model — Fall back to `tagstn.amount` when rate-based calculation yields 0
**File:** `admin/application/models/ret_reports_model.php`  
**Function:** `get_wastagewisepandlreport()` — the `tag_stn` LEFT JOIN subquery

**Before:**
```sql
SUM(IF(m.uom_id=6, round(IFNULL((billstn.wt * IFNULL(purstn.po_stone_rate, IFNULL(tagstn.pur_rate,0))),0),2),0)) as dia_amount,
SUM(IF(m.uom_id!=6, round(IFNULL((billstn.wt * IFNULL(purstn.po_stone_rate, IFNULL(tagstn.pur_rate,0))),0),2),0)) as stn_amount,
...
LEFT JOIN (SELECT tagstn.tag_id, tagstn.stone_id, pur_rate, wt FROM ret_taging_stone ...) tagstn
```

**After:**
```sql
SUM(IF(m.uom_id=6, round(IF(IFNULL(purstn.po_stone_rate,IFNULL(tagstn.pur_rate,0)) > 0, IFNULL((billstn.wt * IFNULL(purstn.po_stone_rate,IFNULL(tagstn.pur_rate,0))),0), IFNULL(tagstn.amount,0)),2),0)) as dia_amount,
SUM(IF(m.uom_id!=6, round(IF(IFNULL(purstn.po_stone_rate,IFNULL(tagstn.pur_rate,0)) > 0, IFNULL((billstn.wt * IFNULL(purstn.po_stone_rate,IFNULL(tagstn.pur_rate,0))),0), IFNULL(tagstn.amount,0)),2),0)) as stn_amount,
...
LEFT JOIN (SELECT tagstn.tag_id, tagstn.stone_id, pur_rate, wt, amount FROM ret_taging_stone ...) tagstn
```

**Key changes:**
1. Wrapped rate-based calculation in `IF(rate > 0, rate_calc, tagstn.amount)` — if rate is available, use `wt × rate`; otherwise use `tagstn.amount` directly (already a total cost, not per-unit)
2. Added `amount` to the `tagstn` subquery SELECT

### Purchase Rate column
The **Purchase Rate** showing `0.00` is a **data issue**, not a code bug. When `ret_lot_inwards_detail.rate = 0` and `ret_taging.lot_rate = 0`, the rate was never entered during lot inward. No code fix needed — the data needs to be corrected at source.

## Pattern
- When building purchase cost calculations with multi-level fallback (PO → tag → lot), always check if the **total cost** column (`amount`, `pur_cost`) exists as a last-resort fallback
- `ret_taging_stone.amount` = total stone cost from tagging (purchase cost for non-PO items)
- `ret_taging_stone.pur_rate` = per-unit purchase rate (often 0 for non-PO lots)
- `ret_taging_stone.pur_cost` = another purchase cost field (also often 0)
- For non-PO lots (`ret_lot_inwards.po_id IS NULL`), all PO-based JOINs return NULL — always need tag-level fallbacks

## Tags
tag-wise-profit, purchase-stone-amount, non-po-lot, ret_taging_stone, amount-fallback, diamond, studded, profit-report, stn_amt, dia_amt
