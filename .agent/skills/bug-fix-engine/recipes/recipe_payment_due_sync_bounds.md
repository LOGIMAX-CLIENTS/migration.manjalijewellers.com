# Recipe Template

> Auto-generated bug remediation recipe by Antigravity

## Metadata
- **Pattern ID**: PAT-SYS-012
- **Severity**: CRITICAL
- **Modules Affected**: Payment Collections (`admin_payment.php` SaveAll), App API (`adminapp_api.php`), and Webhook Controllers.
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL 
- **Reason**: All CodeIgniter V3 source variations utilize the identical scheme account installment generation framework (`get_due_date` / `ins_cycle`).

## Created By
- **Developer**: Antigravity
- **Client**: RTM_newversion
- **Date**: 2026-04-08
- **Source Bug ID**: N/A

## Symptom
During multi-installment advance payments, or simultaneous same-month overlapping payments made from the Admin Panel or Mobile App, the transaction processes successfully but fails to update the `installment` number, `due_date`, and `due_type`. Database records show `NULL` for these fields, breaking chronological sequential flow algorithms across the dashboard.

## Root Cause
The `get_due_date` mathematical array dynamically expects bounded slot values. The frontend or controller matrix generically assigns `due_type = 'ND'` (Normal Due). `ND` natively restricts acceptable slots exclusively to overlapping dates: `BETWEEN date_payment`. Because the first payment consumes the current mathematical month slot, subsequent payments (`$i > 1`) query for future slots. Because future slots do not overlap mathematically with the `date_payment` (today), the array returns `NULL` (0 indices).

Furthermore, the response hooks evaluated arrays unsafely `sizeof($ins_cycle[0])` raising undocumented array offset failures that silently halted the update hooks, and explicitly omitted pushing the calculated `due_type` into `$cycle_data`.

## Detection
```command
grep -rn "sizeof(\$ins_cycle\[0\]) > 0" admin/application/controllers/ admin/application/models/ application/controllers/
```

## Files
- `admin/application/controllers/admin_payment.php`
- `application/controllers/adminapp_api.php`

## Fix

### Before
```php
if ($generic['due_type'] == 'PN') {
    $dueType = ($i == 1 ? 'ND' : 'PD');
} else if ($generic['due_type'] == 'AN') {
    $dueType = ($i == 1 ? 'ND' : 'AD');
} else {
    $dueType = $generic['due_type'];
}
//...
if (sizeof($ins_cycle[0]) > 0) {
    $cycle_data = array(
        'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
        'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
        'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
        'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
        'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
    );
```

### After
```php
if ($generic['due_type'] == 'PN') {
    $dueType = ($i == 1 ? 'ND' : 'PD');
} else if ($generic['due_type'] == 'AN') {
    $dueType = ($i == 1 ? 'ND' : 'AD');
} else {
    if ($generic['due_type'] == 'ND' && $i > 1) {
        $dueType = 'AD'; // Transform subsequent ND to reverse-Advance
    } else {
        $dueType = $generic['due_type'];
    }
}
//...
if (!empty($ins_cycle) && sizeof($ins_cycle) > 0 && isset($ins_cycle[0])) {
    $cycle_data = array(
        'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
        'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
        'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
        'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
        'due_type' => (isset($ins_cycle[0]['due_type']) ? $ins_cycle[0]['due_type'] : NULL),
        'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
    );
```

## Verification
1. Run a generic multi-installment `SaveAll` transaction (`installments = 3`) from the Admin payment panel.
2. Verify all three dynamically inserted IDs using `SELECT id_payment, due_date, due_type, installment`. 
3. Observe all DB flags populating chronologically (`ND` -> `AD` -> `AD` respectively) rather than NULL overrides.

## Notes
Webhook callback sequences must alternately rely on post-sync triggers leveraging `dayDurationSchemeService` specifically because hooks bypass this iterative `$i` multi-loop layer sequence.
