# Bug: Tag Wise Profit Report — Purchase V.A(%) Shows 0.00 Despite Lot Having Wastage

## Symptom
In the Tag Wise Profit report (`admin_ret_reports/tag_wise_profit/list`), the **Purchase V.A(%)** and **Purchase V.A(GM)** columns show `0.00` even though the lot inward clearly has a wastage percentage (e.g., 5.00%).

The lot acknowledgement page shows V.A(%) = 5.00, Touch = 92.00, Purity = 91.6000. But the report shows Purchase V.A(%) = 0.00.

## Root Cause
The SQL query correctly calculates `purwastage` using the lot's wastage:

```sql
-- Line 25111: SQL correctly gets 5.00 from lot_wastage_percentage
round((billdet.net_wt * (ifnull(puritm.item_wastage, IFNULL(tag.lot_wastage_percentage,0)) / 100)),3) as purwastagewt
```

**But the PHP code at line 25341-25342 overrides it** with a derived value:

```php
if ((float)$items['purchase_touch'] != "0.00") {
    $items['purwastage'] = (float)$items['purchase_touch'] - ceil((float)$items['pur_purity']);
}
```

**The math:**
- `purchase_touch` = 92.00 (from `tag.lot_purchase_touch`)
- `pur_purity` = 91.6000 (from `ret_purity` table via `tag.purity`)
- `ceil(91.6000)` = **92**
- `purwastage` = 92.00 - 92 = **0.00** ← BUG

The `ceil()` rounds purity UP to match touch, making the difference 0. This override replaces the actual lot wastage (5.00) with a derived value (0.00).

### Why it affects downstream fields
Lines 25343-25346 recalculate `purwastagewt`, `wastageprofit`, and `profitwastper` from the (now-zeroed) `purwastage`, cascading the error.

### Data pattern for affected records
```sql
-- Tags where ceil(purity) equals purchase_touch, causing 0 wastage
SELECT t.tag_code, t.lot_wastage_percentage, t.lot_purchase_touch, rp.purity,
       t.lot_purchase_touch - CEIL(rp.purity) as derived_wastage
FROM ret_taging t
LEFT JOIN ret_purity rp ON rp.id_purity = t.purity
WHERE t.lot_purchase_touch > 0
  AND t.lot_purchase_touch - CEIL(rp.purity) = 0
  AND t.lot_wastage_percentage > 0
```

## Fix (1 change)

### Model — Use actual `item_wastage` instead of touch-purity derivation
**File:** `admin/application/models/ret_reports_model.php`  
**Function:** `get_wastagewisepandlreport()` — PHP processing block

**Before:**
```php
if ((float)$items['purchase_touch'] != "0.00") {
    $items['purwastage'] = (float)$items['purchase_touch'] - ceil((float)$items['pur_purity']);
```

**After:**
```php
if ((float)$items['purchase_touch'] != "0.00") {
    $items['purwastage'] = (float)$items['item_wastage'];
```

**Key change:**
- `item_wastage` is the SQL alias for `IFNULL(puritm.item_wastage, IFNULL(tag.lot_wastage_percentage, 0))` (line 25129)
- This correctly picks: PO wastage → lot wastage → 0
- The old formula `purchase_touch - ceil(pur_purity)` was mathematically wrong because `ceil()` rounds fractional purity UP, eliminating the difference

### Why `item_wastage` is correct
The SQL already handles the fallback chain:
```sql
IFNULL(puritm.item_wastage, IFNULL(tag.lot_wastage_percentage, 0)) as item_wastage
```
1. **Priority 1**: PO item wastage (`ret_purchase_order_items.item_wastage`)
2. **Priority 2**: Tag's lot wastage (`ret_taging.lot_wastage_percentage`, populated from lot inward)
3. **Default**: 0

## Pattern
- Never derive wastage from `touch - ceil(purity)` — the `ceil()` rounding destroys fractional differences
- Always use the explicitly stored wastage percentage field (`item_wastage`, `lot_wastage_percentage`)
- When both SQL and PHP calculate the same field, the PHP override must use consistent source data, not a different formula

## Tags
tag-wise-profit, purchase-wastage, purwastage, VA-percent, ceil-rounding, purchase-touch, purity, lot-wastage, item_wastage, profit-report
