# Bug Recipe: Purchase Order Email Template Approx Wt Calculation Mismatch

## Metadata
- **Pattern ID**: PAT-REORDER-PO-EMAIL-WT
- **Severity**: HIGH
- **Modules Affected**: Purchase order approval emails, order accept public view
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: The purchase order email template did not multiply the single item weight by the number of pieces.

## Created By
- **Developer**: Antigravity AI
- **Client**: GEORGE AND SONS
- **Date**: 2026-06-02
- **Source Bug ID**: N/A

## Symptom
The approx weight column in the "New Purchase Order Received" notification email shows the single-item weight range value (e.g., 10.000) regardless of the piece count (e.g., displaying 10.000 for both 5 pieces and 2 pieces).

## Root Cause
In `admin/application/views/order/purchase_order_email_template.php`, the template printed `$itemDet['weight']` directly under the "Approx. Wt" column instead of multiplying it by the total piece count `$itemDet['tot_items']`.

## Detection
```command
grep -rn "number_format(\$itemDet\['weight'\], 3)" admin/application/views/order/
```

## Files
- `admin/application/views/order/purchase_order_email_template.php`

## Fix

### Before
```php
<td style='border: 1px solid #ddd; padding: 8px; font-size: 13px; text-align: right;'><b><?php echo number_format($itemDet['weight'], 3); ?></b></td>
```

### After
```php
<td style='border: 1px solid #ddd; padding: 8px; font-size: 13px; text-align: right;'><b><?php echo number_format($itemDet['weight'] * $itemDet['tot_items'], 3); ?></b></td>
```

## Verification
1. Place a purchase order with multiple pieces of a product (e.g. 5 pieces of L CHAIN).
2. Trigger the MD approval flow or PO notification email.
3. Verify that the Approx. Wt in the table displays the total weight (e.g. 50.000 instead of 10.000).

## Notes
The main public accept view page (`order_accept_public.php`) correctly displays the multiplied total weight: `$item['approx_wt'] * $item['tot_items']`.
