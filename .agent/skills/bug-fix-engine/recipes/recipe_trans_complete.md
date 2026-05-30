# Recipe: trans_complete() → trans_begin/commit/rollback

## Metadata
- **Pattern ID**: PAT-TXN-002
- **Severity**: HIGH
- **Modules Affected**: All controllers that use transactions
- **Auto-fixable**: No — requires manual review per instance

## Symptom
Database operations silently succeed even when they should roll back. Partial data gets saved: e.g., bill record created but journal entry missing because an intermediate step failed.

## Root Cause
CodeIgniter 2's `trans_complete()` auto-checks `trans_status()` — if any query failed, it rolls back. **But** it does this silently. The calling code continues executing as if the transaction succeeded, potentially triggering downstream operations (like stock updates) on data that was rolled back.

The correct pattern is explicit `trans_begin()` / `trans_commit()` / `trans_rollback()` with error checking.

## Detection
```command
grep -rn "trans_complete()" admin/application/controllers/
```

## Files
- Any controller file containing `trans_complete()`

## Fix

> **NOT auto-fixable as a simple replacement.** Each `trans_complete()` call needs to be replaced with explicit transaction control that matches the surrounding logic. The fix depends on context.

### Before
```php
$this->db->trans_complete();
```

### After (pattern — adapt per usage)
```php
if ($this->db->trans_status() === FALSE) {
    $this->db->trans_rollback();
    // Handle error: log, return error response, etc.
    $response = array('status' => 'error', 'message' => 'Transaction failed');
    echo json_encode($response);
    return;
}
$this->db->trans_commit();
```

## Verification
1. Check that the transaction wraps all related DB operations
2. Test the failure case: what happens if a query fails mid-transaction?
3. Verify no stock/journal/payment operations run after rollback

## Notes
- This is a **manual review** recipe — AI flags the instances, human decides the fix per context
- Count of instances gives severity: >20 instances = critical debt
- Some `trans_complete()` in read-only contexts are acceptable (no write = no risk)
