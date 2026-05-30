# Missing Tag Cart Restriction in Transaction Queries

> Tags in active stock_issue, branch_transfer, ecom_tag_map, or advance_reservation carts can be picked up by other transactions because the query lacks exclusion JOINs/conditions.

## Metadata
- **Pattern ID**: PAT-SQL-012
- **Severity**: HIGH
- **Modules Affected**: Branch Transfer, Sales Transfer, Deemed Sales, Deemed Sales Return, Stock Issue
- **Auto-fixable**: No (each query structure is different, requires manual JOIN placement)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core tag exclusion logic — every client using tagged stock transactions is affected.

## Created By
- **Developer**: Antigravity AI
- **Client**: AMS-RetailAdmin
- **Date**: 2026-04-23
- **Source Bug ID**: N/A

## Symptom
A tag that is already added to one transaction's cart (e.g., stock issue pending, branch transfer pending/approved, ecom-mapped, advance-reserved) appears as available in another transaction (e.g., deemed sales return). This allows the same tag to be added to multiple concurrent transactions, causing data integrity issues: double transfers, ghost stock, or failed downloads.

## Root Cause
The SQL query fetching available tags for a transaction does not LEFT JOIN against the other transaction tables to exclude tags already in use. The `WHERE` clause is missing `IS NULL` checks for those JOINs.

The 4 exclusion checks needed are:
1. **Stock Issue**: `LEFT JOIN ret_stock_issue_detail si ON si.tag_id = t.tag_id AND si.status IN (0, 1)` → `si.tag_id IS NULL`
2. **Branch Transfer**: `LEFT JOIN (SELECT bti.tag_id FROM ret_branch_transfer bt2 LEFT JOIN ret_brch_transfer_tag_items bti ON bti.transfer_id = bt2.branch_transfer_id WHERE bt2.status IN (1, 2) AND bt2.transfer_item_type = 1 GROUP BY bti.tag_id) btrans ON btrans.tag_id = t.tag_id` → `btrans.tag_id IS NULL`
3. **Ecom Tag Map**: `t.ecom_id IS NULL`
4. **Advance Reservation**: `t.id_orderdetails IS NULL`

## Detection
```command
grep -rn "tag_status.*=.*0\|tag_status.*=.*6" application/models/ret_brntransfer_model.php | grep -i "WHERE" | grep -v "si\.tag_id IS NULL"
```

Also check any query selecting from `ret_taging` for available tags:
```command
grep -rn "ret_taging t" application/models/ret_brntransfer_model.php | grep -v "ret_stock_issue_detail"
```

## Files
- `application/models/ret_brntransfer_model.php` — functions that fetch available tags for transactions

## Functions Already Fixed (Reference Pattern)
- `get_tag_details()` — ✅ Has all 4 checks
- `get_sales_transfer_tag_details()` — ✅ Has all 4 checks (both bt_code and non-bt_code branches)
- `get_sales_return_trans_req_tag()` — ✅ Fixed 2026-04-23 (all 5 internal queries)

## Fix

### Pattern: Add to queries where `ret_taging t` is the tag source

Add these JOINs **before** the `WHERE` clause:
```php
LEFT JOIN ret_stock_issue_detail si ON si.tag_id = t.tag_id AND si.status IN (0, 1)
LEFT JOIN (SELECT bti.tag_id FROM ret_branch_transfer bt2
    LEFT JOIN ret_brch_transfer_tag_items bti ON bti.transfer_id = bt2.branch_transfer_id
    WHERE bt2.status IN (1, 2) AND bt2.transfer_item_type = 1
    GROUP BY bti.tag_id) btrans ON btrans.tag_id = t.tag_id
```

Add these conditions to the `WHERE` clause:
```php
AND si.tag_id IS NULL AND btrans.tag_id IS NULL AND t.ecom_id IS NULL AND t.id_orderdetails IS NULL
```

### Important Notes on Alias Conflicts
- If `bti` or `bt2` aliases are already used in the outer query, use `bti2`/`bt3` etc. for the subquery aliases.
- The branch_transfer subquery must use a DIFFERENT alias than the outer query's branch_transfer table if one exists.

### Before (Example — deemed sales return, bill+tag query)
```php
Left join ret_design_master d on d.design_no=t.design_id
WHERE (t.tag_status=0) and t.ecom_id is null and t.id_orderdetails is null and b.bill_status=1
```

### After
```php
Left join ret_design_master d on d.design_no=t.design_id
LEFT JOIN ret_stock_issue_detail si ON si.tag_id = t.tag_id AND si.status IN (0, 1)
LEFT JOIN (SELECT bti.tag_id FROM ret_branch_transfer bt2
    LEFT JOIN ret_brch_transfer_tag_items bti ON bti.transfer_id = bt2.branch_transfer_id
    WHERE bt2.status IN (1, 2) AND bt2.transfer_item_type = 1
    GROUP BY bti.tag_id) btrans ON btrans.tag_id = t.tag_id
WHERE (t.tag_status=0) and si.tag_id IS NULL and btrans.tag_id IS NULL and t.ecom_id is null and t.id_orderdetails is null and b.bill_status=1
```

## Verification
1. Open deemed sales return form, select a branch
2. Add a tag to stock issue (status pending) — verify it does NOT appear in deemed sales return tag listing
3. Add a tag to branch transfer (status pending/approved) — verify it does NOT appear
4. Map a tag to ecom — verify it does NOT appear
5. Reserve a tag for advance order — verify it does NOT appear
6. Tags NOT in any cart should still appear normally

## Notes
- This pattern applies to ANY function that fetches "available tags" for a transaction.
- When auditing a new module or client, search for `ret_taging` queries with `tag_status=0` or `tag_status=6` in WHERE but missing `ret_stock_issue_detail` JOIN — those are vulnerable.
- The `$sql_old` query in `get_sales_return_trans_req_tag` is kept for backward compatibility but the main `$sql` query is the active one.
- For `tag_status=6` (deemed sold) queries, the stock_issue and branch_transfer checks are defensive — those transactions normally only pick status=0 tags, but data inconsistencies can happen.
