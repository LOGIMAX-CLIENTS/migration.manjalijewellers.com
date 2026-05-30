# Non-Tag Receipt Transfer Query Counts Rejected Transfers as Transferred Stock

> `getNonTagReceiptedLots()` used `status != 4` which included rejected transfers (status=3) in the transferred weight sum, inflating totals and hiding receipts that still have stock to transfer.

## Metadata
- **Pattern ID**: PAT-BT-NTR-001
- **Severity**: HIGH
- **Modules Affected**: Branch Transfer, Non-Tag Receipt
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL (clients using non-tag receipt flow)
- **Reason**: Universal query logic error in the branch transfer model.

## Created By
- **Developer**: Antigravity
- **Client**: AMS-RetailAdmin
- **Date**: 2026-04-28
- **Source Bug ID**: N/A

## Symptom
Non-tag receipts disappear from the "available for transfer" dropdown even though stock hasn't been fully transferred. This happens when a previous transfer against the receipt was **rejected** (status=3) — the rejected weight is still counted as "transferred", inflating the total and making the receipt appear fully consumed.

## Root Cause
`getNonTagReceiptedLots()` in `ret_brntransfer_model.php` used `WHERE status != 4` in its subquery. The status codes are:
- **3** = Rejected
- **4** = Updated to destination branch (completed)

By only excluding status 4, rejected transfers (status 3) were included in the `SUM(grs_wt)` / `SUM(net_wt)` aggregation. This inflated the transferred totals, causing the `!=` comparison to incorrectly evaluate as equal (fully transferred) when it shouldn't.

The sibling query `fetchNonTaggedReceiptedItems()` correctly used `status != 3`, creating an inconsistency between the two.

## Detection
```command
grep -rn "status != 4.*GROUP BY id_nontag_receipt" application/models/ret_brntransfer_model.php
```

## Files
- `application/models/ret_brntransfer_model.php` — `getNonTagReceiptedLots()` function

## Fix

### Before
```php
WHERE status != 4 GROUP BY id_nontag_receipt ) bt on bt.id_nontag_receipt = nt.id_nontag_receipt 
```

### After
```php
WHERE status != 3 GROUP BY id_nontag_receipt ) bt on bt.id_nontag_receipt = nt.id_nontag_receipt 
```

## Verification
1. Create a non-tag receipt with gross_wt = 100g
2. Create a branch transfer against it for 50g — verify receipt still appears in dropdown
3. **Reject** that transfer (set status=3) — verify receipt still shows with full 100g available
4. Create a new transfer for 100g, approve it (status=4) — verify receipt disappears from dropdown
5. Confirm `fetchNonTaggedReceiptedItems()` returns matching remaining quantities

## Notes
- **Branch transfer status codes**: 1=pending, 2=in-transit/approved, 3=rejected, 4=completed at destination
- Both `getNonTagReceiptedLots()` and `fetchNonTaggedReceiptedItems()` must use the same status exclusion (`!= 3`) for consistency
- The correct logic: exclude rejected (never moved), include everything else (pending, in-transit, completed) as "transferred weight"
