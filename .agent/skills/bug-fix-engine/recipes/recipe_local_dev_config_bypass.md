# Recipe: Local Dev Config Bypass (Server Config Removal)

## Metadata
- Pattern ID: PAT-INF-001
- Severity: P0 (Blocker for local dev)
- Modules Affected: System Core (index.php, database.php)
- Auto-fixable: Yes

## Client Scope
- Applies to: ALL (Legacy clients with hardcoded global_configs.php requirement)

## Created By
- Antigravity (Requested by USER after manual verification)
- Date: 2026-04-21

## Symptom
The application dies with "Error occured. Please contact administrator" upon loading the index page locally. Database connection fails because it expects `Globals::$hostname` which is undefined.

## Root Cause
Legacy boilerplates hardcode a `require_once` for `global_configs.php`. On production servers, this file is symlinked from a shared directory. In local environments, it is missing.

## Detection Rule
```powershell
grep -r 'Error occured. Please contact administrator' admin/index.php
grep -r 'Globals::\$hostname' admin/application/config/database.php
```

## Fix

### admin/index.php
Comment out or make the `global_configs.php` check conditional.

**Before:**
```php
if (file_exists(__DIR__ . '/../global_configs.php')) {
    require_once __DIR__ . '/../global_configs.php';
} else {
    die("Error occured. Please contact administrator");
}
```

**After:**
```php
if (file_exists(__DIR__ . '/../global_configs.php')) {
    require_once __DIR__ . '/../global_configs.php';
} elseif (getenv('CLIENT_ID')) {
    // Only die if we are on a server that expects CLIENT_ID
    die("Error occured. Please contact administrator");
}
```

### admin/application/config/database.php
Use local credentials instead of shared `Globals` object.

**Before:**
```php
$db['default']['hostname'] = Globals::$hostname;
$db['default']['username'] = Globals::$username;
$db['default']['password'] = Globals::$password;
$db['default']['database'] = Globals::$database;
```

**After:**
```php
$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'root';
$db['default']['password'] = '';
$db['default']['database'] = 'tail_development_src'; // Or project-specific DB name
```

## Verification
- Run `php -l admin/index.php`
- Run `php -l admin/application/config/database.php`
- Attempt to load the admin dashboard locally.

## Notes
- This fix is for LOCAL DEVELOPMENT only.
- Do NOT push these hardcoded database credentials to production.
- Use `git update-index --assume-unchanged` for database.php if possible.
