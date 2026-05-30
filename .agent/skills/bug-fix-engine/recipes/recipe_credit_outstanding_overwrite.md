# Recipe: Credit Outstanding — Old Metal Not Deducted

> Paid amount for credit collection bills was being overwritten by a buggy function, causing old_metal_amount to be excluded from outstanding calculation.

## Metadata
- **Pattern ID**: PAT-CALC-001
- **Severity**: HIGH
- **Modules Affected**: Billing
- **Auto-fixable**: Yes (comment out one line)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core billing model function used by all clients

## Created By
- **Developer**: Antigravity
- **Client**: VRS (vrsjwl)
- **Date**: 2026-04-04
- **Source Bug ID**: N/A (reported directly)

## Symptom
Customer outstanding amount in the `bill_list` table (customer detail popup) is higher than it should be. Specifically, when a credit collection bill includes old metal (exchange), the old metal amount is NOT deducted from the outstanding balance.

## Root Cause
In `get_credit_pending_details()`, there are TWO competing calculations for `$paid_amount`:

1. **Lines ~9354-9368**: `get_previous_credit_collections()` — correctly sums ALL payment components (old_metal, cash, chit, advance, discount)
2. **Line ~9370**: `get_credit_collection_details()` — a simpler/older function that has a bug: `$old__metal_amount` is reset to 0 **inside each loop iteration**, so when multiple credit collection bills exist, only the last one's old metal survives. If the last collection has no old metal, the total old metal contribution becomes 0.

Line 9370 **unconditionally overwrites** the correct `$paid_amount` from the newer function, discarding the old metal amount.

## Detection
```command
grep -n "get_credit_collection_details" admin/application/models/ret_billing_model.php
```
Look for a call to `get_credit_collection_details()` that appears immediately after a `foreach` loop calling `get_previous_credit_collections()`. The overwrite pattern is:
```php
// Loop calculates $paid_amount correctly...
$paid_amount = $this->get_credit_collection_details($r['bill_id']); // ← THIS overwrites the correct value
```

## Files
- `admin/application/models/ret_billing_model.php` — function `get_credit_pending_details()`

## Fix

### Before
```php
$paid_amount = $this->get_credit_collection_details($r['bill_id']);
```

### After
```php
//$paid_amount = $this->get_credit_collection_details($r['bill_id']); // BUG FIX: This was overwriting the correct $paid_amount from get_previous_credit_collections() which properly includes old_metal_amount
```

## Verification
1. Find a customer with a credit bill that has a credit collection containing old metal exchange
2. Call `getCustomerDet` API for that customer — check `outstanding` array
3. Verify `bal_amt` = `tot_bill_amount - tot_amt_received - (sum of all credit collections including old metal)`
4. The `paid_amount` field should include the old metal amount
5. Edge case: Multiple credit collections where only some have old metal — all should be summed correctly

## Notes
- The `get_credit_collection_details()` function at line ~7822 also has a secondary bug: `$old__metal_amount` (note double underscore) is initialized to 0 INSIDE the foreach loop, so only the last iteration's old metal is returned. This function should be refactored or deprecated in favor of `get_previous_credit_collections()`.
- This fix is surgical — just comment out the overwrite line. The upstream `get_previous_credit_collections()` query and loop already handles all payment types correctly.
- Tested with customer 5942, bill_id 8402: outstanding went from incorrect ₹105,548 to correct ≈₹0.
