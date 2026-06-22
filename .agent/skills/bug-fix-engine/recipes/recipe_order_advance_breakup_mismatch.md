# Recipe: Order Advance Breakup Mismatch

## Metadata
- **Pattern ID**: PAT-RPT-ADV-002
- **Severity**: HIGH
- **Modules Affected**: Reports (`ret_reports_model.php`)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: The drill-down order advance history query incorrectly uses `is_adavnce_adjusted` to compute `advance_adj`.

## Created By
- **Developer**: Antigravity
- **Client**: SRI AMMAN JEWELLERS
- **Date**: 2026-06-20
- **Source Bug ID**: RPT-CLT02

## Symptom
When viewing the breakup of an order advance payment in the history drilldown popup:
* The `ADVANCE ADJ` column incorrectly displays the total advance amount (duplicating it) if that advance receipt has been subsequently adjusted towards a final sales bill.
* The expected behavior is that it should only show the amount adjusted from a previous receipt to fund the advance receipt.

## Root Cause
In `get_order_advance_history($order_no)` inside `ret_reports_model.php`, the `advance_adj` field was computed as:
```sql
SUM(IF(a.is_adavnce_adjusted = 1, a.advance_amount, 0)) as advance_adj
```
This checks if the advance itself was adjusted later against a final sales invoice. To reflect the payment breakup correctly, it should check `ret_advance_utilized` for any adjusted previous advance used to pay for the advance receipt.

## Detection
```bash
grep -rn "SUM(IF(a.is_adavnce_adjusted = 1, a.advance_amount, 0)) as advance_adj" admin/application/models/ret_reports_model.php
```

## Files
- `admin/application/models/ret_reports_model.php`

## Fix

### Before
```php
			SUM(IF(a.is_adavnce_adjusted = 1, a.advance_amount, 0)) as advance_adj,
```

### After
```php
			IFNULL((SELECT SUM(utilized_amt) FROM ret_advance_utilized WHERE bill_id = a.bill_id), 0) as advance_adj,
```

## Verification
1. Verify the SQL query compiles and returns correct values for the targeted order number.
2. In the history drilldown popup, verify that `ADVANCE ADJ` shows 0 for advances paid via Cash and Old Gold, and only shows a value if paid by adjusting a previous receipt.
