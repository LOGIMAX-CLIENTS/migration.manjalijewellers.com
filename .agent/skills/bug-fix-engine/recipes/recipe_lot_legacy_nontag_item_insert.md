# Legacy Non-Tag Item Insert During Lot Save Bypasses Receipt Flow

> Lot inward save directly inserts/updates `ret_nontag_item` and `ret_nontag_item_log` for stock_type=2, bypassing the non-tag receipt pipeline and causing stock duplication.

## Metadata
- **Pattern ID**: PAT-LOT-001
- **Severity**: HIGH
- **Modules Affected**: Lot Inward, Non-Tag Receipt, Branch Transfer
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL (clients using non-tag receipt flow)
- **Reason**: Any client that has migrated to the `ret_nontag_receipt` pipeline will hit this if the legacy code path is not gated.

## Created By
- **Developer**: Antigravity
- **Client**: AMS-RetailAdmin
- **Date**: 2026-04-28
- **Source Bug ID**: N/A

## Symptom
When saving a lot inward with `stock_type = 2` (non-tag), the system directly inserts/updates records in `ret_nontag_item` and `ret_nontag_item_log` tables. If the non-tag receipt flow (`ret_nontag_receipt`) is active, this creates duplicate stock entries — once from the legacy lot save path and again when the receipt is processed through the proper receipt → branch transfer pipeline.

## Root Cause
The lot inward save (`admin_ret_lot.php`, `lot_inward/save` case) contains a legacy block (lines ~683–767) that directly manages non-tag item stock when `stock_type == 2`. This was the original flow before `ret_nontag_receipt` was introduced. With the new receipt-based flow, this block should be disabled to prevent double-counting of non-tag stock.

## Detection
```command
grep -rn "checkNonTagItemExist\|updateNTData\|ret_nontag_item_log" application/controllers/admin_ret_lot.php
```
Look for direct `ret_nontag_item` / `ret_nontag_item_log` insertions inside lot save that are NOT gated by a receipt-enabled flag.

## Files
- `application/controllers/admin_ret_lot.php`

## Fix

### Step 1: Add flag variable after POST data assignment

#### Before
```php
$addData = $_POST['inward'];
```

#### After
```php
$addData = $_POST['inward'];
$nontag_receipt_enabled = true; // flag: skip legacy non-tag item insertion (now handled via non-tag receipt flow)
```

### Step 2: Gate the legacy non-tag block with the flag

#### Before
```php
if ($addData['stock_type'] == 2 && $detail_insId) {
```

#### After
```php
if ($addData['stock_type'] == 2 && $detail_insId && !$nontag_receipt_enabled) {
```

## Verification
1. Create a lot inward with `stock_type = 2` (non-tag items)
2. Confirm NO new records appear in `ret_nontag_item` or `ret_nontag_item_log` from the lot save
3. Verify the lot inward detail record is created successfully in `ret_lot_inwards_detail`
4. Confirm the non-tag receipt flow (`ret_nontag_receipt` → branch transfer) still works independently
5. Check `id_nontag_log` in `ret_lot_inwards_detail` remains NULL (since the legacy log insert is skipped)

## Notes
- The flag is hardcoded as `true` for now. Can be migrated to `ret_settings` table (`nontag_receipt_enabled`) for per-client toggling if needed.
- Setting the flag to `false` re-enables the legacy path as a fallback.
- This recipe pairs with the `ret_nontag_receipt` schema setup — ensure the receipt table and branch transfer `id_nontag_receipt` column exist before applying.
- Related recipe: `recipe_bt_nontag_receipt_schema_mismatch.md`
