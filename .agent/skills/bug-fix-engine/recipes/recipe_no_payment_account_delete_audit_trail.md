# Recipe: No-Payment Account Deletion — Missing Audit Trail & Response

## Metadata
- **Pattern ID**: PAT-AUD-001
- **Severity**: HIGH
- **Modules Affected**: Scheme Account (Services)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal pattern — any client using `deleteNoPayments_Acc_days()` cron service

## Created By
- **Developer**: Antigravity AI
- **Client**: erp.lakshmanaacharison.in
- **Date**: 2026-04-22
- **Source Bug ID**: N/A (enhancement)

## Symptom
The `deleteNoPayments_Acc_days($days)` service endpoint permanently deletes scheme accounts with no payment history, but:
1. No audit trail — deleted `id_scheme_account` values are lost forever
2. No transaction wrapping — partial deletes possible with no rollback
3. Returns plain text string instead of structured JSON — caller cannot parse which accounts were deleted
4. No `log_detail` entries — no record in the system log table

## Root Cause
The original implementation was a simple loop-and-delete with a plain string return (`"Has N Accounts Deleted"`). No transaction, no logging, no structured response. This follows a fire-and-forget anti-pattern for destructive operations.

## Detection
```bash
grep -rn "deleteNoPayments_Acc_days" admin/application/controllers/ admin/application/models/ --include="*.php"
```

Also check for the same pattern in `deleteNoPayments_Acc` (month-based variant):
```bash
grep -rn "deleteNoPayments_Acc" admin/application/controllers/ admin/application/models/ --include="*.php"
```

## Files
- `admin/application/controllers/admin_services.php` — Controller method
- `admin/application/models/admin_usersms_model.php` — Model method

## Fix

### Controller — Before
```php
function deleteNoPayments_Acc_days($days)
{
    $model = self::MODEL;

    if ($days > 0) {
        $delete  = $this->$model->deleteNoPayments_Acc_days($days);
        echo $delete;
    } else {
        echo "Invalid days";
    }
}
```

### Controller — After
```php
function deleteNoPayments_Acc_days($days)
{
    $model = self::MODEL;
    $log_model = self::LOG_MODEL;

    if ($days > 0) {

        $this->db->trans_begin();
        $result = $this->$model->deleteNoPayments_Acc_days($days);

        if ($result['status'] == 1 && $this->db->trans_status() === TRUE) {
            $this->db->trans_commit();

            // Log each deleted account
            foreach ($result['deleted_ids'] as $acc_id) {
                $log_data = array(
                    'id_log'     => $this->session->userdata('id_log') ? $this->session->userdata('id_log') : 0,
                    'event_date' => date("Y-m-d H:i:s"),
                    'module'     => 'Scheme Account',
                    'operation'  => 'Delete',
                    'record'     => $acc_id,
                    'remark'     => 'No-payment account auto-deleted (no payments in '.$days.' days)'
                );
                $this->$log_model->log_detail('insert', '', $log_data);
            }

            $response = array(
                'status'      => 1,
                'message'     => $result['deletedCount'].' Accounts Deleted',
                'deletedCount'=> $result['deletedCount'],
                'deleted_ids' => $result['deleted_ids']
            );
        } else {
            $this->db->trans_rollback();
            $response = array(
                'status'  => 0,
                'message' => $result['message'] ?? 'Transaction failed',
                'deleted_ids' => []
            );
        }

        echo json_encode($response);
    } else {
        echo json_encode(array('status' => 0, 'message' => 'Invalid days', 'deleted_ids' => []));
    }
}
```

### Model — Before
```php
function deleteNoPayments_Acc_days($days)
{
    // ... SQL query ...
    $inactive_acc = $this->db->query($sql);

    if($inactive_acc->num_rows()>0)
    {
        $deletedCount = 0;
        foreach($inactive_acc->result_array() as $record){
            $status = $this->db->delete(self::ACC_TABLE, array('id_scheme_account'=>$record['id_scheme_account']));
            if($status){
                $deletedCount++;
            }
        }
        return "Has ".$deletedCount." Accounts Deleted";
    }else{
        return "No accounts found";
    }
}
```

### Model — After
```php
function deleteNoPayments_Acc_days($days)
{
    // ... SQL query unchanged ...
    $inactive_acc = $this->db->query($sql);

    if($inactive_acc->num_rows()>0)
    {
        $deletedCount = 0;
        $deleted_ids = array();
        foreach($inactive_acc->result_array() as $record){
            $status = $this->db->delete(self::ACC_TABLE, array('id_scheme_account'=>$record['id_scheme_account']));
            if($status){
                $deletedCount++;
                $deleted_ids[] = $record['id_scheme_account'];
            }
        }
        return array('status' => 1, 'deletedCount' => $deletedCount, 'deleted_ids' => $deleted_ids, 'message' => $deletedCount.' Accounts Deleted');
    }else{
        return array('status' => 0, 'deletedCount' => 0, 'deleted_ids' => array(), 'message' => 'No accounts found');
    }
}
```

## Verification
1. Call the endpoint with a valid days value — verify JSON response contains `deleted_ids` array
2. Check `log_detail` table — verify one entry per deleted account with module='Scheme Account', operation='Delete'
3. Call with `days=0` — verify JSON `{"status":0,"message":"Invalid days","deleted_ids":[]}`
4. Call when no accounts match — verify `{"status":0,"message":"No accounts found","deleted_ids":[]}`
5. Simulate DB failure mid-loop — verify transaction rollback (no partial deletes)

## Notes
- The same anti-pattern exists in `deleteNoPayments_Acc($months)` (month-based variant) — should receive the same treatment
- `id_log` uses session fallback to `0` since this may be called from cron (no logged-in user)
- The SQL query conditions were also refined by the developer: removed `paid_installments = 0` and `is_new = 'Y'` checks, changed `<=` to `<` for the date comparison
- Pattern follows the established `admin_manage->account_post` Delete case as the reference implementation
