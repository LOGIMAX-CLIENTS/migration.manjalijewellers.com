# Bill Split — Diamond Weight Double-Count in Partly Sold / Branch Vault Reports

> After a Bill Split, diamond stone weights appear doubled (or negative balance) in the Partly Sold and Branch Vault reports because two separate report SQL subqueries both sum ALL stone weights for a tag across ALL split bills using `GROUP BY tag_id`, then add both results together.

## Metadata
- **Pattern ID**: PAT-BILLING-043
- **Severity**: CRITICAL
- **Modules Affected**: Billing → Bill Split → Partly Sold Report, Branch Vault Report
- **Auto-fixable**: No (SQL logic refactor)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core report model shared across all clients

## Created By
- **Developer**: Antigravity AI
- **Client**: erp.manepally.com
- **Date**: 2026-05-19
- **Source Bug ID**: N/A

---

## Symptom

After a Bill Split involving diamond/stone items:
1. **Partly Sold report** → `Sold Dia Wt` shows **2× the actual diamond weight** (e.g., 15.162 instead of 7.581)
2. **Branch Vault report** → `DIA WT` shows **negative balance** (e.g., -7.582)
3. `Balance Dia Wt` in Partly Sold shows **large negative** value (e.g., -7.582)
4. A secondary issue: after fix, a **0.001 rounding discrepancy** remains because the JS split-save logic also introduces cumulative `.toFixed(3)` rounding error (sum of 9 bills = 7.581 instead of 7.580)

All other columns (Gross Wt, Net Wt, Pieces) are correct — only diamond/stone weight is affected.

---

## Root Cause

### Bug 1: SQL — `getPartlySold()` in `ret_reports_model.php`

The `sold_diawt` is calculated as:
```
sold_diawt = bill_det_stn.wt + ps.sold_diawt
```

Both subqueries independently `GROUP BY tag_id` and `SUM(wt)` across **all bills** for that tag, returning the full stone weight total (e.g., 7.581). When added together: **7.581 + 7.581 = 15.162**.

**Broken subquery 1 (`bill_det_stn`) — groups by tag_id:**
```sql
LEFT JOIN (SELECT bs.bill_det_id, bd.tag_id, SUM(bs.wt) as wt
    FROM ret_billing_item_stones bs
    LEFT JOIN ret_bill_details bd on bd.bill_det_id = bs.bill_det_id
    LEFT JOIN ret_stone s on s.stone_id = bs.stone_id
    WHERE s.stone_type = 1
GROUP BY bd.tag_id) as bill_det_stn on bill_det_stn.tag_id = bil_det.tag_id
```

**Broken subquery 2 (`bill_stn` inside `ps`) — also groups by tag_id:**
```sql
LEFT JOIN (SELECT bs.bill_det_id, SUM(bs.wt) as wt, bd.tag_id
    FROM ret_billing_item_stones bs
    LEFT JOIN ret_bill_details bd on bd.bill_det_id = bs.bill_det_id
    LEFT JOIN ret_stone s on s.stone_id = bs.stone_id
    WHERE s.stone_type = 1
GROUP BY bd.tag_id) as bill_stn on bill_stn.tag_id = d.tag_id
```

### Bug 2: JS — `createSaleBillSplitRow()` in `ret_billing.js`

Each split bill calculates its stone weight as `(ratio_per / 100) × full_stone_wt` independently. After `.toFixed(3)` rounding at each step, the cumulative sum can exceed the original by 0.001 (e.g., 9 bills summing to 7.581 instead of 7.580).

---

## Detection

### Detect Bug 1 (SQL):
```bash
grep -n "GROUP BY bd.tag_id" admin/application/models/ret_reports_model.php
```
If result appears inside `getPartlySold()`, bug exists.

### Detect Bug 2 (JS):
```bash
grep -n "availableBeginningStnweight" admin/assets/js/ret_billing.js
```
If found (old variable name), the remainder fix has NOT been applied.

```bash
grep -n "stn_accumulated" admin/assets/js/ret_billing.js
```
If found, the fix IS applied.

---

## Files

1. `admin/application/models/ret_reports_model.php` — `getPartlySold()` function
2. `admin/assets/js/ret_billing.js` — `createSaleBillSplitRow()` function

---

## Fix 1 — SQL: Group by `bill_det_id` instead of `tag_id`

### `getPartlySold()` — Fix `bill_det_stn` subquery

**Before:**
```sql
LEFT JOIN (SELECT bs.bill_det_id, bd.tag_id, SUM(bs.wt) as wt
    FROM ret_billing_item_stones bs
    LEFT JOIN ret_bill_details bd on bd.bill_det_id = bs.bill_det_id
    LEFT JOIN ret_stone s on s.stone_id = bs.stone_id
    WHERE s.stone_type = 1
GROUP BY bd.tag_id) as bill_det_stn on bill_det_stn.tag_id = bil_det.tag_id
```

**After:**
```sql
LEFT JOIN (SELECT bs.bill_det_id, SUM(bs.wt) as wt
    FROM ret_billing_item_stones bs
    LEFT JOIN ret_stone s on s.stone_id = bs.stone_id
    WHERE s.stone_type = 1
GROUP BY bs.bill_det_id) as bill_det_stn on bill_det_stn.bill_det_id = bil_det.bill_det_id
```

### Fix `ps` subquery (inner `bill_stn` + outer `sold_diawt`)

**Before:**
```sql
LEFT JOIN (SELECT IFNULL(SUM(d.gross_wt),0) as soldgwt, IFNULL(SUM(d.net_wt),0) as sold_nwt,
    IFNULL(bill_stn.wt,0) as sold_diawt, d.tag_id
    FROM ret_bill_details d
    LEFT JOIN ret_billing bill ON bill.bill_id = d.bill_id
    LEFT JOIN (SELECT bs.bill_det_id, SUM(bs.wt) as wt, bd.tag_id
        FROM ret_billing_item_stones bs
        LEFT JOIN ret_bill_details bd on bd.bill_det_id = bs.bill_det_id
        LEFT JOIN ret_stone s on s.stone_id = bs.stone_id
        WHERE s.stone_type = 1
    GROUP BY bd.tag_id) as bill_stn on bill_stn.tag_id = d.tag_id
    WHERE d.item_type = 2 AND bill.bill_status = 1
    GROUP BY d.tag_id) as ps ON ps.tag_id = bil_det.tag_id
```

**After:**
```sql
LEFT JOIN (SELECT IFNULL(SUM(d.gross_wt),0) as soldgwt, IFNULL(SUM(d.net_wt),0) as sold_nwt,
    IFNULL(SUM(bill_stn.wt),0) as sold_diawt, d.tag_id
    FROM ret_bill_details d
    LEFT JOIN ret_billing bill ON bill.bill_id = d.bill_id
    LEFT JOIN (SELECT bs.bill_det_id, SUM(bs.wt) as wt
        FROM ret_billing_item_stones bs
        LEFT JOIN ret_stone s on s.stone_id = bs.stone_id
        WHERE s.stone_type = 1
    GROUP BY bs.bill_det_id) as bill_stn on bill_stn.bill_det_id = d.bill_det_id
    WHERE d.item_type = 2 AND bill.bill_status = 1
    GROUP BY d.tag_id) as ps ON ps.tag_id = bil_det.tag_id
```

**Key changes:**
- `bill_det_stn`: `GROUP BY bd.tag_id` → `GROUP BY bs.bill_det_id`, join on `bill_det_id`
- `bill_stn`: same refactor
- `ps.sold_diawt`: `IFNULL(bill_stn.wt, 0)` → `IFNULL(SUM(bill_stn.wt), 0)` (aggregates per-row values correctly)

---

## Fix 2 — JS: Last split bill gets remainder (not another ratio calculation)

### In `createSaleBillSplitRow()` — add `stn_accumulated` tracker

**Add before the `$(splitRatios).each()` loop:**
```javascript
// Track per-stone accumulated wt to fix rounding on last split
var stn_accumulated = {};
var total_splits_count = splitRatios.length;
```

**Change the `applied_weight` line inside the loop:**
```javascript
var is_last_split = (idx === total_splits_count - 1);

// Last split: use remainder to prevent cumulative rounding drift
let applied_weight = is_last_split
    ? parseFloat(parseFloat(gross_wt) - parseFloat(splitted_weight)).toFixed(3)
    : parseFloat((ratio_per / 100) * availableBeginning).toFixed(3);
```

**Replace stone weight calculation inside the `$.each(stone_details)` loop:**

Before:
```javascript
var stone_weight = val.stone_wt;
let availableBeginningStnweight = stone_weight
var splitted_stn_weight = 0;
let applied_stn_weight = parseFloat((ratio_per / 100) * availableBeginningStnweight).toFixed(3);
```

After:
```javascript
var stone_weight = val.stone_wt;
var splitted_stn_weight = 0;
// Initialize accumulator for this stone_id
if(!stn_accumulated[val.stone_id]) stn_accumulated[val.stone_id] = 0;
// Last split: exact remainder to avoid cumulative .toFixed(3) rounding error
let applied_stn_weight = is_last_split
    ? parseFloat(parseFloat(stone_weight) - parseFloat(stn_accumulated[val.stone_id])).toFixed(3)
    : parseFloat((ratio_per / 100) * stone_weight).toFixed(3);
stn_accumulated[val.stone_id] = parseFloat(parseFloat(stn_accumulated[val.stone_id]) + parseFloat(applied_stn_weight)).toFixed(3);
```

---

## Data Correction (for existing affected tags)

Run this SQL to fix already-stored split bill stone weights for a specific affected tag:

```sql
-- Step 1: Verify the discrepancy
SELECT rb.bill_no, rbis.bill_stone_id, rbis.wt as stored_wt,
    rbt.dia_wt as tag_dia_wt
FROM ret_billing_item_stones rbis
INNER JOIN ret_bill_details rbd ON rbd.bill_det_id = rbis.bill_det_id
INNER JOIN ret_billing rb ON rb.bill_id = rbd.bill_id
INNER JOIN ret_stone rs ON rs.stone_id = rbis.stone_id
INNER JOIN ret_taging rbt ON rbt.tag_id = rbd.tag_id
WHERE rbt.tag_code = '[TAG_CODE]' AND rb.bill_status = 1 AND rs.stone_type = 1
ORDER BY rb.bill_no DESC;

-- Step 2: Reduce the LAST split bill's stone weight by the diff amount
UPDATE ret_billing_item_stones
SET wt = ROUND(wt - [DIFF], 3)
WHERE bill_stone_id = (
    SELECT bill_stone_id FROM (
        SELECT rbis2.bill_stone_id
        FROM ret_billing_item_stones rbis2
        INNER JOIN ret_bill_details rbd2 ON rbd2.bill_det_id = rbis2.bill_det_id
        INNER JOIN ret_billing rb2 ON rb2.bill_id = rbd2.bill_id
        INNER JOIN ret_stone rs2 ON rs2.stone_id = rbis2.stone_id
        INNER JOIN ret_taging rbt2 ON rbt2.tag_id = rbd2.tag_id
        WHERE rbt2.tag_code = '[TAG_CODE]' AND rb2.bill_status = 1 AND rs2.stone_type = 1
        ORDER BY rb2.bill_no DESC
        LIMIT 1
    ) AS tmp
);
```

Replace `[TAG_CODE]` and `[DIFF]` with actual values.

---

## Verification

1. Go to **Partly Sold report** → search for the affected tag
2. `Sold Dia Wt` should equal `Stock Dia Wt` (tag's original weight) — no doubling
3. `Balance Dia Wt` should be `0.000` (fully sold) or positive (partial sale)
4. Go to **Branch Vault report** → `DIA WT` column should show **no negative values**
5. Test a new Bill Split with 3+ bills (unequal ratios, e.g., 40/35/25)
6. After save, check stone popup for each split bill → sum should equal original tag stone weight exactly
7. After hard refresh (Ctrl+Shift+R), re-test to confirm cached JS is not interfering

---

## Notes

- The SQL bug existed because `bill_det_stn` and `bill_stn` were designed for single-bill partial sales. When split bills are created (multiple `bill_det_id` rows per `tag_id`), grouping by `tag_id` aggregates across ALL bills, then both aggregations are added together → double count.
- The JS bug only affects the **last** split bill by 0.001–0.003g. It's imperceptible on bills but causes report balance to show a tiny negative value.
- Both bugs must be fixed together — the SQL fix alone will correctly report whatever is stored; the JS fix ensures what's stored is accurate.
