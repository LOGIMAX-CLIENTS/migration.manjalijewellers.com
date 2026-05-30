# Recipe: clear_database() without authentication

## Metadata
- **Pattern ID**: PAT-SEC-003
- **Severity**: CRITICAL
- **Modules Affected**: Settings (admin_settings.php)
- **Auto-fixable**: Yes

## Symptom
Any unauthenticated user (or bot) can call the `clear_database` endpoint and wipe all data from the client's production database. There is no role check, no admin verification, and no confirmation prompt.

## Root Cause
The `clear_database()` method in `admin_settings.php` directly executes TRUNCATE/DELETE operations without first checking:
1. If the user is logged in
2. If the user has admin/superadmin role
3. If a confirmation token was provided

## Detection
```command
grep -n "function clear_database" admin/application/controllers/admin_settings.php
```

## Files
- `admin/application/controllers/admin_settings.php`

## Fix

### Before
```php
public function clear_database()
{
```

### After
```php
public function clear_database()
{
    // Security: require superadmin authentication
    if (!$this->session->userdata('logged_in') || $this->session->userdata('role') != 'superadmin') {
        show_error('Access Denied: Superadmin authentication required', 403);
        return;
    }
    // Security: require confirmation token
    $token = $this->input->post('confirm_token');
    if ($token !== md5($this->session->userdata('user_id') . date('Y-m-d'))) {
        show_error('Invalid confirmation token', 403);
        return;
    }
```

## Verification
1. Try calling `/admin_settings/clear_database` without login → should get 403
2. Try calling with a normal user login → should get 403
3. Try calling with superadmin but no token → should get 403
4. Only superadmin + valid token should proceed

## Notes
- The `db_backup()` function has the same issue — add similar auth check
- Some clients may have already added their own auth checks — the Before pattern won't match, which is correct (skip)
- The confirmation token is a simple day-based hash — adequate for internal tool, not for public-facing
