# Recipe: Journal Entry Not Reversed on Bill Cancel

## Metadata
- **Pattern ID**: PAT-CANCEL-001
- **Severity**: CRITICAL
- **Modules Affected**: Billing (admin_ret_billing)
- **Auto-fixable**: No — requires per-bill-type implementation

## Symptom
When a bill is cancelled, the `ret_journal` entries remain active. Financial reports (Cash Abstract, Day Transactions) continue to show the cancelled bill's amounts, causing:
1. Cash drawer mismatch at day close
2. GST reports include cancelled transactions
3. Customer ledger shows phantom balances

## Root Cause
`cancel_bill()` in `admin_ret_billing.php` (line 7773) handles tag status, stock reversal, and bill status update — but **never touches** `ret_journal`.

Confirmed via grep: `ret_journal` appears **zero times** in `admin_ret_billing.php`. The journal entries created during bill save are never reversed or soft-deleted on cancellation.

## Detection
```command
grep -c "ret_journal" admin/application/controllers/admin_ret_billing.php
# Returns 0 — confirms no journal handling in billing controller

grep -n "cancel_bill" admin/application/controllers/admin_ret_billing.php
# Shows cancel_bill() at line 7773
```

## Files
- `admin/application/controllers/admin_ret_billing.php` — `cancel_bill()` (line ~7773)
- `admin/application/models/ret_billing_model.php` — needs new `reverse_journal_entries()` method

## Fix

> **NOT auto-fixable.** Requires adding journal reversal logic to cancel_bill(). The fix varies by bill type:
> - Sales (type=1): Reverse credit journal entries
> - Sales Return (type=7): Reverse debit journal entries
> - Purchase (type=4): Reverse purchase journal entries

### Model Addition Required
```php
// Add to ret_billing_model.php
function reverse_journal_entries($bill_id) {
    // Option A: Soft delete
    $this->db->where('bill_id', $bill_id);
    $this->db->update('ret_journal', array(
        'is_cancelled' => 1,
        'cancelled_date' => date('Y-m-d H:i:s')
    ));
    
    // Option B: Create reversing entries (preferred for audit trail)
    $entries = $this->db->get_where('ret_journal', array('bill_id' => $bill_id))->result_array();
    foreach ($entries as $entry) {
        $reversal = $entry;
        unset($reversal['id_journal']);
        $reversal['credit_amt'] = $entry['debit_amt'];
        $reversal['debit_amt'] = $entry['credit_amt'];
        $reversal['narration'] = 'REVERSAL: ' . $entry['narration'];
        $reversal['entry_date'] = date('Y-m-d H:i:s');
        $reversal['is_reversal'] = 1;
        $reversal['original_journal_id'] = $entry['id_journal'];
        $this->db->insert('ret_journal', $reversal);
    }
}
```

### Controller Addition Required
```php
// Add inside cancel_bill() AFTER bill status update, BEFORE stock reversal
$this->$model->reverse_journal_entries($cancel_original_bill_id);
```

## Verification
1. Save a sales bill → check ret_journal has entries
2. Cancel the bill → check ret_journal has reversal entries (or is_cancelled=1)
3. Run Cash Abstract → cancelled bill amounts should NOT appear
4. Run Day Transactions → cancelled transaction should show as reversed
5. Check customer balance → should not include cancelled amount

## Notes
- This MUST be fixed alongside PAT-STOCK-001 (stock reversal) — they're in the same function
- Need to check if `ret_journal` table has `is_cancelled` and `is_reversal` columns — may need migration
- Some clients may have partial fixes via model overrides — check model for existing reversal logic
- **Priority**: Fix in source first, then propagate to all clients
