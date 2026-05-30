# Recipe: CI3 PHP 8.x Core Compatibility — filter_var flag + log_message signature

## Metadata
- Pattern ID: PAT-PHP8-CI3-001
- Severity: P0 — Fatal (blocks application startup)
- Modules Affected: ALL (system-level core)
- Auto-fixable: YES

## Client Scope
- Applies to: ALL clients running PHP 8.x with legacy CI3 system core
- Reason: PHP 8.x stricter type system exposes two long-standing CI3 bugs

## Created By
- Developer: Antigravity AI
- Date: 2026-04-04
- Source Bug ID: SYS-PHP8-001

## Symptom
Application is completely dead on startup. Fatal error in browser:

```
Fatal error: Uncaught TypeError: filter_var(): Argument #3 ($options) must be of type array|int, string given
in system/core/Input.php on line 1565
```

Often accompanied by:
```
Deprecated: Optional parameter $level declared before required parameter $message
is implicitly treated as a required parameter in system/core/Common.php on line 1042
```

And multiple "Creation of dynamic property" E_DEPRECATED warnings.

## Root Cause

**Issue 1 (Fatal — Input.php):**
In `CI_Input::valid_ip()`, the `switch($which)` default case sets `$flag = ''` (empty string).
On PHP 8.x, `filter_var($ip, FILTER_VALIDATE_IP, $flag)` requires the 3rd argument to be
`array|int`. Passing `''` throws a `TypeError`.

**Issue 2 (Deprecation — Common.php):**
`log_message($level = 'error', $message, ...)` has an optional parameter before a required one.
PHP 8.x treats this deprecated pattern as a required param and emits an E_DEPRECATED notice.

## Detection

```powershell
# Detect Issue 1
grep -n "\$flag = ''" admin/system/core/Input.php

# Detect Issue 2
grep -n "log_message(\$level = 'error'" admin/system/core/Common.php
```

## Files
- `admin/system/core/Input.php` — `valid_ip()` method
- `admin/system/core/Common.php` — `log_message()` function

## Fix

### Issue 1 — Input.php (FATAL)

```php
// BEFORE (line ~1549):
default:
    $flag = '';
    break;

// AFTER:
default:
    $flag = 0;
    break;
```

**Why:** PHP 8.x filter_var() requires int|array for 3rd arg. `0` means "no flags" — same behaviour as `''` was in PHP 7.x, but type-safe.

### Issue 2 — Common.php (Deprecation)

```php
// BEFORE (line ~1042):
function log_message($level = 'error', $message, $php_error = FALSE)

// AFTER:
function log_message($level, $message, $php_error = FALSE)
```

**Why:** PHP 8.x prohibits optional params before required params. All callers always pass `$level` explicitly (e.g. `log_message('error', '...')`), so removing the default is safe.

## Verification

```powershell
& "D:\xampp\php\php.exe" -l "admin/system/core/Input.php"
# Expected: No syntax errors detected

& "D:\xampp\php\php.exe" -l "admin/system/core/Common.php"
# Expected: No syntax errors detected
```

Then load the admin panel — the TypeError crash should be gone and the application should boot normally.

## Notes
- The "Creation of dynamic property" E_DEPRECATED notices on Controller.php:139 are harmless
  CI3 framework warnings. They do NOT crash the app — only the filter_var one does.
- These two are the only ones that need fixing to restore startup.
- The `preg_split(..., NULL, ...)` lint warning in Input.php line 1893 is pre-existing CI3 code;
  PHP 8 accepts NULL as int implicitly (converted to 0 = no limit). Non-fatal, leave it.
