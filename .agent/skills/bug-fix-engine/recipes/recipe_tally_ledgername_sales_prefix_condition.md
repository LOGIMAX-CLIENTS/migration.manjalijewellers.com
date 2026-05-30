# Tally API LedgerName "Sales " Prefix — Inverted Condition

> The ternary condition controlling the "Sales " prefix on LedgerName was using `!= 1` instead of `== 1` for `transfer_item_type`, causing the prefix to appear on non-transfer items instead of transfer items with old metal categories.

## Metadata
- **Pattern ID**: PAT-TALLY-001
- **Severity**: HIGH
- **Modules Affected**: Tally API (ret_tally_api_model)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core Tally export logic — any client using Tally integration with old metal deemed sales

## Created By
- **Developer**: AI (Antigravity)
- **Client**: AMS-RetailAdmin
- **Date**: 2026-04-22
- **Source Bug ID**: N/A (Change Request)

## Symptom
In Tally voucher export, the `LedgerName` field gets the "Sales " prefix prepended for the **wrong** items. Items with `transfer_item_type != 1` and `id_ret_category` 13 or 14 get the prefix, when it should only appear for `transfer_item_type == 1` (transfer items) with those old metal categories (13, 14).

This causes incorrect ledger mapping in Tally for both transfer and non-transfer sale vouchers.

## Root Cause
The ternary condition used `!= 1` (not-equal) instead of `== 1` (equal) for `$row->transfer_item_type`, and used `&&` (AND) instead of `||` (OR) between the transfer type check and category check. The requirement is: prefix "Sales " when the item is a transfer item (type 1) **OR** when the category is 13 or 14 — not only when **both** conditions are true.

Additionally, in one occurrence (qty-type 1 branch), the false-branch of the ternary was `'Sales '` instead of `''`, making the condition completely pointless (both branches produced the same output).

## Detection
```command
grep -rn "transfer_item_type != 1 && (\$itemrow\['id_ret_category'\]" application/models/ret_tally_api_model.php
```

Also check for the inverted pattern across any other tally models:
```command
grep -rn "transfer_item_type != 1" application/models/ret_tally*
```

## Files
- `application/models/ret_tally_api_model.php` (two occurrences in sales voucher generation loop)

## Fix

### Location 1: qty-type 1 branch (around line 1250)

#### Before
```php
"LedgerName"            => ($row->transfer_item_type != 1 && ($itemrow['id_ret_category'] == 14 || $itemrow['id_ret_category'] == 13) ? 'Sales ':'Sales '). $dispLedgerName.$dipLedgerParent,//"Sales A/c",
```

#### After
```php
"LedgerName"            => ($row->transfer_item_type == 1 || $itemrow['id_ret_category'] == 14 || $itemrow['id_ret_category'] == 13 ? 'Sales ':''). $dispLedgerName.$dipLedgerParent,//"Sales A/c",
```

### Location 2: else branch (around line 1344)

#### Before
```php
"LedgerName"            => ($row->transfer_item_type != 1 && ($itemrow['id_ret_category'] == 14 || $itemrow['id_ret_category'] == 13) ? 'Sales ':''). $dispLedgerName.$dipLedgerParent,
```

#### After
```php
"LedgerName"            => ($row->transfer_item_type == 1 || $itemrow['id_ret_category'] == 14 || $itemrow['id_ret_category'] == 13 ? 'Sales ':''). $dispLedgerName.$dipLedgerParent,
```

## Verification
1. Export a Tally voucher for a bill containing transfer items (`transfer_item_type = 1`) with `id_ret_category` 13 or 14 — `LedgerName` should start with "Sales " followed by the product name
2. Export a Tally voucher for a regular (non-transfer) bill — `LedgerName` should have **no** "Sales " prefix, just the product name directly
3. Export a Tally voucher for a transfer item with a category other than 13/14 — `LedgerName` should have **no** "Sales " prefix
4. Verify both qty-type branches (tally_qty_type == 1 and else) produce consistent results

## Notes
- This pattern is a classic "inverted boolean" bug — easy to miss in code review because `!= 1` and `== 1` look similar
- The double-"Sales " bug in Location 1 (both ternary branches producing `'Sales '`) is a secondary indicator — if both branches return the same value, the condition is dead code and likely wrong
- Related to the Old Metal Deemed Sales integration (conversation 690cabad) where `id_ret_category` 13 and 14 represent old metal categories
- Watch for similar inverted conditions in other Tally export functions (purchase, credit note, etc.)
