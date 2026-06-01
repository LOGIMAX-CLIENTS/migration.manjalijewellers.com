# Recipe: Approval Rate Fixing Cancel Status Does Not Update

> Copy this file and fill in all sections when creating a new recipe.

## Metadata
- **Pattern ID**: PAT-RF-APR-001
- **Severity**: LOW
- **Modules Affected**: ret_purchase (Approval Rate Fixing)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core system bug

## Created By
- **Developer**: Antigravity
- **Client**: eTail
- **Date**: 2026-05-21
- **Source Bug ID**: N/A

## Symptom
After clicking the Cancel action in Approval Rate Fixing list, the system displays a “Cancel Successfully” message, but the status remains as “Yet to Approve” instead of updating to a cancelled status.

## Root Cause
The `get_approval_rate_fix_list` SQL query returns the `approve_status` based solely on `is_approved` status, completely ignoring the `bill_status` field which controls whether a record is cancelled or active. Since cancelled records have `bill_status = 2` but their `is_approved` remains `0`, the query incorrect maps it to "Yet to Approve".

## Detection
```command
grep -rn "if(rf.is_approved=0,'Yet to Approve','Approved') as approve_status" admin/application/models/ret_purchase_order_model.php
```

## Files
- `admin/application/models/ret_purchase_order_model.php`
- `admin/assets/js/ret_purchase_order.js`

## Fix

### Before
```php
if(rf.is_approved=0,'Yet to Approve','Approved') as approve_status
```
```javascript
if (row.is_approved == true) {
    chekbox = row.rate_fix_id;
}
```

### After
```php
if(rf.bill_status=2,'Cancelled', if(rf.is_approved=0,'Yet to Approve','Approved')) as approve_status
```
```javascript
if (row.is_approved == true || row.bill_status == 2) {
    chekbox = row.rate_fix_id;
}
```

## Verification
1. Open Admin Panel -> Rate Fix -> Entry -> Approval Rate Fixing
2. Ensure there is a "Yet to Approve" record (bill_status 1, is_approved 0)
3. Click the "Cancel" action button on a record and provide a reason
4. Validate the success toast message appears
5. Check that the record's status now shows "Cancelled" instead of "Yet to Approve"

## Notes
Any query that lists records with an approval flow must check `bill_status = 2` (cancelled) first, as cancellation overrides the approval status (approved/pending).
