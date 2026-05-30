# Recipe Template

## Metadata
- **Pattern ID**: PAT-GEN-005
- **Severity**: CRITICAL
- **Modules Affected**: Payment, potentially others
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Common PHP behavior where passing an array to a method expecting a scalar causes a Notice or SQL Syntax Error, which leads to `false` return and then a Fatal Error.

## Created By
- **Developer**: Antigravity
- **Client**: Chinnannan Jewellery
- **Date**: 2026-04-03
- **Source Bug ID**: PAY-CLT-02

## Symptom
A script dies (Fatal Error) midway through execution (e.g. midway through a loop processing multiple payments). No errors are logged to standard user UI, but subsequent items in the loop are not processed.

## Root Cause
A method signature change or incorrect variable passing leads to a full array (like `$pay`) being passed to a DB model method that expects a scalar ID (like `$id_scheme_account`).
This causes:
1. `PHP Notice: Array to string conversion` when concatenating the array into SQL.
2. MySQL Error (`WHERE id_column = Array`)
3. `$this->db->query()` returns `FALSE`.
4. `$query->row_array()` is called on a boolean `FALSE`, triggering `PHP Fatal error: Call to a member function row_array() on boolean`.

## Detection
```command
grep -rn "updateGroupCode" admin/application/controllers/ admin/application/models/
```

## Files
- `admin/application/controllers/<controller>.php`
- `admin/application/models/<model>.php`

## Fix

### Before (Model)
```php
function updateGroupCode($id_scheme_account) {
    $sql = $this->db->query("SELECT * FROM scheme_account WHERE id_scheme_account = " . $id_scheme_account);
    // ...
```

### After (Model - Hardened)
```php
function updateGroupCode($id_scheme_account) {
    if (is_array($id_scheme_account)) {
        $id_scheme_account = $id_scheme_account['id_scheme_account'] ?? null;
    }
    if (empty($id_scheme_account)) return ['status' => false, 'group_code' => null];

    $sql = $this->db->query("SELECT * FROM scheme_account WHERE id_scheme_account = " . $id_scheme_account);
    // ...
```

### Before (Controller call)
```php
$updCode = $this->payment_model->updateGroupCode($pay);
```

### After (Controller call)
```php
$updCode = $this->payment_model->updateGroupCode($pay['id_scheme_account']);
```

## Verification
1. Manually test the operation with multiple items to ensure the loop doesn't die.
2. Check database for created records corresponding to the second iteration.
3. Assert that no PHP Fatal errors are logged out.

## Notes
Always check methods that execute raw SQL string concatenation `("WHERE id = " . $id)` and harden them by checking `is_array()` and extracting the primary identifier if an array was accidentally passed.
