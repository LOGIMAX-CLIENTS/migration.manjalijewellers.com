# Recipe: Branch Transfer — Sales Return Home Bill Items Missing

> Home bill sales return items (item_type=2) are invisible in Branch Transfer Purchase Items search because they have no tag record (tag_id=NULL), causing them to fall through all PHP filter branches.

## Metadata
- **Pattern ID**: PAT-BRN-008
- **Severity**: HIGH
- **Modules Affected**: Branch Transfer, Sales Return, Billing
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Any client using home billing (item_type=2) with sales returns will hit this — the filter logic gap exists in the base codebase.

## Created By
- **Developer**: Antigravity
- **Client**: Konika (first confirmed 2026-04-01), GupthaGem (confirmed 2026-04-10)
- **Date**: 2026-04-01 (updated 2026-04-10)
- **Source Bug ID**: N/A

## Symptom
In Inventory → Branch Transfer Request, when selecting Type = **Purchase Items**, only Old Metal transactions are shown. Sales Return transactions are **missing entirely**, even though they represent valid transferable stock.

## Root Cause
`get_sales_ret_details()` in `ret_brntransfer_model.php` has a post-query PHP filter with only two branches:

1. `is_non_tag == 0 && tag_status == 6` — tagged items with a tag record
2. `is_non_tag == 1` — non-tagged items

Home bill sales return items have `is_non_tag = 0` (marked as tagged mode) but `tag_id = NULL` (no actual tag record — the item was a home/exchange return). Since `tag_status` is NULL (no tag), they fail branch 1. Since `is_non_tag = 0`, they also fail branch 2. **Result: silently dropped.**

## Detection
```powershell
# Detect missing Home Bill filter branch
Select-String -Path "admin/application/models/ret_brntransfer_model.php" -Pattern "det_item_type"
# If NO match → bug is present (missing the Home Bill condition)

# Confirm: check trans_id uses IFNULL fallback
Select-String -Path "admin/application/models/ret_brntransfer_model.php" -Pattern "IFNULL\(t\.tag_id,d\.bill_det_id\)"
# If NO match → trans_id SQL fix also missing
```
Look for the `get_sales_ret_details()` function. Bug is present when:
1. No `det_item_type` column in SELECT
2. No `else if` branch checking `det_item_type == 2` in the PHP loop

## Files
- `admin/application/models/ret_brntransfer_model.php`

## Fix

### Before
```php
        // SQL: trans_id resolves to NULL for home bill items (tag_id IS NULL)
        if(d.is_non_tag=0,t.tag_id,d.bill_det_id) as trans_id,
        ...
        // SQL: no det_item_type column selected
        p.sales_mode,t.trans_to_acc_stock,d.is_non_tag,t.tag_status,IFNULL(btrans.tag_id,'') as btrans_tag_id,d.transferred_to_acc_stock,d.bill_det_id,
        IFNULL(non_tag_brch.sold_bill_det_id,'') as non_tag_brch_det_id
```

```php
        // PHP: only 2 filter branches — Home Bill falls through silently
		foreach ($result as $items) {
			if ($items['is_non_tag'] == 0 && $items['tag_status'] == 6 && $items['btrans_tag_id'] == '') {
				if (($items['sales_mode'] == 1) && ($items['trans_to_acc_stock'] == 0)) {
					$returnData[] = $items;
				} else if (($items['sales_mode'] == 2) && ($items['gross_wt'] > $items['transfered_wt'])) {
					$returnData[] = $items;
				}
			} else if ($items['is_non_tag'] == 1 && $items['non_tag_brch_det_id'] == '') {
				if ($items['transferred_to_acc_stock'] == 0) {
					$returnData[] = $items;
				}
			}
			// Home Bill (item_type=2): NO branch — silently dropped
		}
```

### After

**Change 1 — SQL: Fix `trans_id` to resolve bill_det_id when tag_id is NULL**
```php
        // IFNULL fallback: if tag_id is NULL (home bill), use bill_det_id as identifier
        if(d.is_non_tag=0,IFNULL(t.tag_id,d.bill_det_id),d.bill_det_id) as trans_id,
```

**Change 2 — SQL: Add `det_item_type` to SELECT**
```php
        p.sales_mode,t.trans_to_acc_stock,d.is_non_tag,t.tag_status,IFNULL(btrans.tag_id,'') as btrans_tag_id,d.transferred_to_acc_stock,d.bill_det_id,IFNULL(d.item_type,0) as det_item_type,
        IFNULL(non_tag_brch.sold_bill_det_id,'') as non_tag_brch_det_id
```

**Change 3 — PHP: Add Home Bill filter branch (between Tagged and Non-tag conditions)**
```php
		foreach ($result as $items) {
			if ($items['is_non_tag'] == 0 && $items['tag_status'] == 6 && $items['btrans_tag_id'] == '') {
				if (($items['sales_mode'] == 1) && ($items['trans_to_acc_stock'] == 0)) {
					$returnData[] = $items;
				} else if (($items['sales_mode'] == 2) && ($items['gross_wt'] > $items['transfered_wt'])) {
					$returnData[] = $items;
				}
			} else if ($items['is_non_tag'] == 0 && $items['det_item_type'] == 2 && ($items['tag_id'] == '' || $items['tag_id'] == NULL) && $items['transferred_to_acc_stock'] == 0) {
				// Home Bill / Custom item (item_type=2): no tag, not a non-tag stock item
				$returnData[] = $items;
			} else if ($items['is_non_tag'] == 1 && $items['non_tag_brch_det_id'] == '') {
				if ($items['transferred_to_acc_stock'] == 0) {
					$returnData[] = $items;
				}
			}
		}
```

## Verification
1. Go to Inventory → Branch Transfer Request
2. Select Type = **Purchase Items**, From Branch, Date Range covering a period with sales returns
3. Click **Search**
4. Verify: "SALES RETURN-GOLD" (and/or SILVER) rows appear alongside "OLD METAL" rows
5. Expand the Sales Return detail row — home bill return items (no tag) should appear
6. Test saving a transfer with sales return items — verify it saves correctly to `ret_brch_transfer_old_metal` with `item_type = 2`

## Notes
- `item_type` values in `ret_bill_details`: `0` = Tagged, `1` = Non-tag stock, `2` = Home Bill / Custom (no tag record)
- The `tag_status = 6` check remains correct for tagged items with actual tag records — this fix only adds coverage for items without tags (Home Bill)
- For `sales_mode = 2` tagged items, the existing weight comparison logic (`gross_wt > transfered_wt`) prevents double-transfer — this protection doesn't apply to home bill items since they use `transferred_to_acc_stock` flag instead
- The `trans_id` SQL fix (`IFNULL`) is **required** alongside the PHP filter fix — without it, `trans_id` is NULL for home bill items and the save logic cannot identify which `bill_det_id` to store in `ret_brch_transfer_old_metal`
- Impact verified safe: 3 conditions are mutually exclusive — `det_item_type=2` tagged items with non-NULL tag_id = 0 records in DB
- Confirmed in source repo (`etail_development_src`) — same fix applied
