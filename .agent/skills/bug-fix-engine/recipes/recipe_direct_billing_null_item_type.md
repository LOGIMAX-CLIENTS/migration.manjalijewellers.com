# Direct Billing Missing item_type — Stock Report Mismatch

> Billing flow for direct/bulk sales inserts into `ret_bill_details` with `item_type = NULL` instead of `0`, causing stock reports to silently exclude those items from sold calculations.

## Metadata
- **Pattern ID**: PAT-BILLING-012
- **Severity**: CRITICAL
- **Modules Affected**: Billing, Stock Reports, Day Close
- **Auto-fixable**: No (requires data fix + code fix)

## Client Scope
- **Applies to**: ALL
- **Reason**: Any client using direct billing (non-scan flow) for tagged products (coins, order ornaments) will hit this. The billing controller has a code path that doesn't set `item_type`.

## Created By
- **Developer**: Antigravity
- **Client**: DCNM (www.dcnmjewels.com)
- **Date**: 2026-04-22
- **Source Bug ID**: N/A

## Symptom
- Stock reports (`stock_report/list`, `cat_wise_closing_stock/list`) show **fewer sold items** than actually billed
- `tag_items_designwise/list` (live tag status) shows correct lower stock, but In/Out reports don't match
- `ret_stock_balance` opening/closing chain breaks — 19th closing ≠ 20th opening
- Massive stock mismatch during high-volume sale days (e.g., Akshaya Tritiya)
- Example: 798 coins sold, report shows only 173

## Root Cause

The billing flow has a code path (direct/bulk billing for items like Gold Coins, Silver Coins, Order Gold Ornaments) that inserts into `ret_bill_details` **without setting `item_type`**, leaving it as `NULL`.

The `stock_details()` method in `ret_reports_model.php` calculates sold items with:
```sql
WHERE bill.bill_status=1 AND b.item_type = 0
```

Items with `item_type = NULL` fail the `= 0` check and are **silently excluded** from the sold count, inflating closing stock.

## Detection

### Detect affected data (run on any client DB):
```sql
SELECT b.id_branch, br.name, d.product_id, p.product_name, 
  COUNT(*) as affected_rows, SUM(d.piece) as total_pcs
FROM ret_bill_details d
JOIN ret_billing b ON b.bill_id = d.bill_id
JOIN ret_product_master p ON p.pro_id = d.product_id
JOIN branch br ON br.id_branch = b.id_branch
WHERE b.bill_status = 1 
  AND d.item_type IS NULL
  AND d.tag_id IS NOT NULL
GROUP BY b.id_branch, d.product_id;
```

If this returns rows, the client is affected.

### Detect vulnerable code:
```command
grep -rn "item_type = 0" admin/application/models/ret_reports_model.php
```

## Files
- `admin/application/models/ret_reports_model.php` — `stock_details()` method (~line 9370)
- `admin/application/controllers/admin_ret_billing.php` — billing insert logic (code path that skips `item_type`)

## Fix

### Part A: Data Fix (immediate, per-client)

```sql
-- Fix all NULL item_type rows that have valid tag_id
UPDATE ret_bill_details d
JOIN ret_billing b ON b.bill_id = d.bill_id
SET d.item_type = 0
WHERE b.bill_status = 1 
  AND d.item_type IS NULL
  AND d.tag_id IS NOT NULL;
```

After this, recalculate `ret_stock_balance` for affected dates:
1. Find the last correct closing date (baseline)
2. For each subsequent day: `closing = opening + inward - sold - branch_out`
3. Each day's closing = next day's opening

### Part B: Query Defense (code fix)

#### Before (`ret_reports_model.php` → `stock_details()`)
```php
WHERE  bill.bill_status=1 and bill.bill_date BETWEEN '$FromDt 00:00:00' AND '$ToDt 23:59:59'  AND b.product_id=prod.pro_id AND b.item_type = 0
```

#### After
```php
WHERE  bill.bill_status=1 and bill.bill_date BETWEEN '$FromDt 00:00:00' AND '$ToDt 23:59:59'  AND b.product_id=prod.pro_id AND (b.item_type = 0 OR b.item_type IS NULL)
```

### Part C: Billing Code Fix (permanent prevention)

Find the billing insert that doesn't set `item_type` and ensure it defaults to `0` for tagged products. Search for:
```command
grep -rn "ret_bill_details" admin/application/controllers/admin_ret_billing.php | grep -i "insert"
```

## Verification
1. Run detection query — should return 0 rows after Part A
2. Check `ret_stock_balance` chain: each day's `closing_pcs` = next day's `op_blc_pcs`
3. Compare `stock_report/list` vs `tag_items_designwise/list` — tag counts should align
4. Verify on a high-volume sale day (like Akshaya Tritiya) that sold counts match actual billing

## Notes
- Products most commonly affected: **Gold Coins, Silver Coins, Order Gold Ornaments** — items typically sold in bulk via direct billing
- The `tag_items_designwise` report is unaffected because it reads live `tag_status = 0` from `ret_taging`, not the In/Out calculation
- After fixing `ret_bill_details`, the `ret_stock_balance` table must be recalculated manually for all affected dates or by re-running day close
- This pattern can silently accumulate over time — run the detection query periodically
