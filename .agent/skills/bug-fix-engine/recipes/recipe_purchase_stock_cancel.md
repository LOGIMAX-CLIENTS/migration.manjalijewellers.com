# Recipe: Purchase Cancel Stock Inversion

## Metadata
- **Pattern ID**: PAT-STOCK-001
- **Severity**: CRITICAL
- **Modules Affected**: Billing (admin_ret_billing — cancel_bill for purchase type=4)
- **Auto-fixable**: No — requires conditional logic based on bill_type

## Symptom
When a purchase bill (type=4) is cancelled, the system incorrectly ADDS stock instead of REMOVING it. This inflates inventory counts, causing:
1. System shows more stock than physically exists
2. Stock In/Out reports are wrong
3. Billing allows selling non-existent items

## Root Cause
`cancel_bill()` uses the same stock reversal logic for ALL bill types. For sales (type=1), cancellation correctly adds stock back (item was sold → now unsold → restore stock). But for purchase (type=4), cancellation should REMOVE stock (item was purchased → purchase cancelled → remove stock). The code doesn't differentiate.

## Detection
```command
grep -n "cancel_bill\|updateNTData\|update_nt_stock" admin/application/controllers/admin_ret_billing.php
# Check if cancel_bill distinguishes between bill_type=1 (sales) and bill_type=4 (purchase)
```

## Files
- `admin/application/controllers/admin_ret_billing.php` — `cancel_bill()` (line ~7773)
- `admin/application/models/ret_billing_model.php` — stock update methods

## Fix

> **NOT auto-fixable.** Requires adding bill_type check inside cancel_bill() to determine stock direction.

### Concept
```php
// Inside cancel_bill(), BEFORE stock operations:
$bill_type = $cancel_bill_details['bill_type'];

// Determine stock direction
if (in_array($bill_type, [1, 2, 3, 9, 12])) {
    // Sales types: cancel → ADD stock back
    $stock_direction = 'add';
} elseif ($bill_type == 4) {
    // Purchase: cancel → REMOVE stock
    $stock_direction = 'subtract';
} elseif ($bill_type == 7) {
    // Sales return: cancel → REMOVE stock (return was adding it back)
    $stock_direction = 'subtract';
}

// Then use $stock_direction in all stock update calls
```

## Verification
1. Create a purchase bill (type=4) with tagged items
2. Check stock count after purchase → should be increased
3. Cancel the purchase bill
4. Check stock count after cancel → should be back to original (decreased)
5. Verify stock reports match physical count

## Notes
- This MUST be fixed alongside PAT-CANCEL-001 (journal reversal) — same function
- Bill type matrix for stock direction on cancel:
  - Type 1 (Sales): +stock (restore sold items)
  - Type 2 (Sales+Exchange): +stock for sold, -stock for exchanged-in old metal
  - Type 4 (Purchase): -stock (remove purchased items)
  - Type 7 (Sales Return): -stock (remove returned items)
  - Type 8 (Credit Collection): no stock change
  - Type 9 (Order Delivery): +stock (restore delivered items)
- Full audit of cancel_bill() is needed — this recipe provides the pattern, not the complete fix
