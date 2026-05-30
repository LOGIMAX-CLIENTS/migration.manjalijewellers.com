# Recipe: Cash Abstract — Incorrect Partly Sale Weight for Chit Adj Bills

## Metadata
- **Pattern ID**: PAT-QRY-009
- **Severity**: HIGH
- **Modules Affected**: Reports (Cash Abstract)
- **Auto-fixable**: No (query logic requires understanding of business context)

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal Cash Abstract report logic

## Created By
- **Developer**: Antigravity
- **Client**: etail_development_src
- **Date**: 2026-04-20
- **Source Bug ID**: RPT-CLT06

## Symptom
In the Cash Abstract report, the "Partly Sale" section displays an incorrect weight for Chit Adjustment bills. The weight shown is `gross_wt + balance_partly_wt` instead of just the balance partly weight. This inflates the reported partly sale weight when the bill involves a chit adjustment.

## Root Cause
The partly sale query in `getBillDetails()` was using `(tag.gross_wt - d.gross_wt)` as the gross weight calculation without properly subtracting the already-sold portion from other partial sales on the same tag. For Chit Adjustment bills, the `d.gross_wt` (bill detail gross weight) didn't correctly represent the sold portion, leading to an inflated weight.

The fix adds a subquery that sums up all sold `gross_wt` from `ret_bill_details` for partial sales (`is_partial_sale=1`) and subtracts that from `tag.gross_wt`, giving the correct balance partly weight. The `HAVING gross_wt > 0` clause filters out fully-sold items.

## Detection
```command
grep -n "tag.gross_wt.*d.gross_wt.*partly\|partly_sale.*tag.gross_wt" admin/application/models/ret_reports_model.php
```

Look for partly sale queries in `getBillDetails()` that compute weight as `tag.gross_wt - d.gross_wt` without a subquery to aggregate all sold portions.

## Files
- `admin/application/models/ret_reports_model.php` — `getBillDetails()` function, partly sale query section

## Fix

### Before
```php
//PARTLY SALE
$partly_sale=$this->db->query("SELECT p.product_name,
(tag.gross_wt-d.gross_wt) as gross_wt,SUM(d.item_cost) as item_cost,SUM(d.piece) as pcs
FROM ret_billing b
LEFT JOIN ret_bill_details d ON d.bill_id=b.bill_id
LEFT JOIN ret_day_closing day_close ON day_close.id_branch=b.id_branch
LEFT JOIN ret_estimation_items e ON e.est_item_id=d.esti_item_id
LEFT JOIN ret_estimation est ON est.estimation_id=e.esti_id
LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
LEFT JOIN ret_taging tag on tag.tag_id=d.tag_id
LEFT JOIN ret_branch_floor_counter f on f.counter_id=b.counter_id
WHERE d.tag_id IS NOT null AND d.is_partial_sale=1 and b.bill_status=1
...
GROUP by d.product_id");
```

### After
```php
//PARTLY SALE
$partly_sale=$this->db->query("SELECT p.product_name,
(SUM(tag.gross_wt)-IFNULL(s.gross_wt,0)) as gross_wt,SUM(d.item_cost) as item_cost,SUM(d.piece) as pcs
FROM ret_billing b
LEFT JOIN ret_bill_details d ON d.bill_id=b.bill_id
LEFT JOIN ret_day_closing day_close ON day_close.id_branch=b.id_branch
LEFT JOIN ret_estimation_items e ON e.est_item_id=d.esti_item_id
LEFT JOIN ret_estimation est ON est.estimation_id=e.esti_id
LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
LEFT JOIN ret_taging tag on tag.tag_id=d.tag_id
LEFT JOIN ret_branch_floor_counter f on f.counter_id=b.counter_id
LEFT JOIN (SELECT IFNULL(SUM(d.gross_wt),0) as gross_wt,d.product_id
    FROM ret_bill_details d
    LEFT JOIN ret_billing bill ON bill.bill_id = d.bill_id
    LEFT JOIN ret_day_closing day_close ON day_close.id_branch=bill.id_branch
    LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
    LEFT JOIN ret_branch_floor_counter f on f.counter_id=bill.counter_id
    WHERE bill.bill_status = 1 AND tag.is_partial = 1
    ".(branch/date/counter/floor/eda/employee filters)."
GROUP BY d.product_id) as s ON s.product_id = d.product_id
WHERE d.tag_id IS NOT null AND d.is_partial_sale=1 and b.bill_status=1
...
GROUP by d.product_id HAVING gross_wt > 0");
```

**Key changes:**
1. Changed `(tag.gross_wt - d.gross_wt)` → `(SUM(tag.gross_wt) - IFNULL(s.gross_wt, 0))`
2. Added subquery `s` that sums all `gross_wt` from `ret_bill_details` where `tag.is_partial = 1` (already sold partial portions)
3. Added `HAVING gross_wt > 0` to filter out fully-sold items
4. Subquery respects same filters (branch, date, counter, floor, EDA, employee)

## Verification
1. Navigate to Cash Abstract report (`admin_ret_reports/cash_abstract/list`)
2. Select a date range that includes Chit Adjustment bills with partial sales
3. Check the "Partly Sale" section weight — should show only the balance (unsold) weight, NOT gross + balance
4. Verify regular (non-chit-adj) bills still show correct partly sale weights
5. Cross-verify with individual bill details — the sum of sold gross weight + balance partly weight should equal tag gross weight

## Notes
- The `get_partly_sales()` function at line ~290 uses a simpler calculation `(tag.gross_wt - d.gross_wt)` for the old metal detail report — this was NOT changed as it serves a different purpose (showing individual bill-level partly sold details)
- The fix only affects the aggregate Cash Abstract report view in `getBillDetails()`
- Related pattern: PAT-QRY-006 (scope leak in aggregate queries)
