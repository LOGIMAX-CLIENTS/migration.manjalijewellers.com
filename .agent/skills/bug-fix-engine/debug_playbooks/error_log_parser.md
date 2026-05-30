# Error Log Parser — PHP / Apache / JS

> How to parse the specific error formats in the eTail codebase.

---

## CodeIgniter PHP Log Format
**Location**: `admin/application/logs/log-YYYY-MM-DD.php`

```
ERROR - 2026-03-27 10:15:23 --> Severity: Notice --> Undefined variable: data
  admin/application/controllers/admin_ret_billing.php 4521
```

### Pattern → Root Cause Map

| Error Pattern | Root Cause | Fix Direction |
|---|---|---|
| `Undefined variable: $xxx` | Variable assigned inside if/else that didn't execute | Add initialization before the if/else |
| `Trying to access array offset on null` | `->row_array()` returned NULL (query got 0 rows) | Add `if ($result)` check before accessing |
| `Cannot modify header information` | Output (echo/print) before `redirect()` | Find and remove stray debug output |
| `Maximum execution time of 30 seconds exceeded` | N+1 query or unindexed WHERE | See `performance.md` playbook |
| `Allowed memory size of X bytes exhausted` | Unbounded query result or large file operation | Add LIMIT to query or chunk processing |
| `Call to undefined method` | Method renamed/deleted, or model not loaded | Check `$this->load->model()` + method name |
| `A Database Error Occurred` | SQL syntax error, constraint violation, or wrong column | Read the SQL shown in error carefully |
| `Class 'Xxx' not found` | File missing or filename case mismatch (Linux) | Check file exists, case matches class name |
| `The action you have requested is not allowed` | CSRF token expired (form open too long) | Re-submit form; check `csrf_token_name` config |
| `Unable to connect to your database` | DB credentials wrong or MySQL down | Check `database.php` config |
| `Deadlock found when trying to get lock` | Two transactions competing for same rows | Add retry logic or reorder operations |

---

## Apache Error Log Format
**Location**: `/var/log/apache2/error.log` or `/var/log/httpd/error_log`

```
[Thu Mar 27 10:15:23.123456 2026] [php:error] [pid 12345] [client 192.168.1.1:54321]
  PHP Parse error: syntax error, unexpected '}' in /path/to/file.php on line 123
```

### White Screen (HTTP 500) Diagnosis
1. Always check Apache log FIRST (CI log may not initialize if PHP parse error)
2. Common: `Parse error` → syntax error (missing semicolon, unmatched braces)
3. Common: `require_once failed` → file deleted or renamed
4. Run `php -l {file}` to check syntax locally

---

## Browser Console (JavaScript)
**Access**: F12 → Console tab

### Common JS Errors in eTail

| Error | Meaning | Fix Direction |
|---|---|---|
| `Uncaught TypeError: Cannot read property 'X' of undefined` | AJAX response didn't return expected field | Check controller JSON output |
| `Uncaught ReferenceError: X is not defined` | JS variable/function not loaded | Check if script tag is included in view |
| `DataTable warning: Requested unknown parameter` | DataTable column config doesn't match data keys | Check `columns` array in JS |
| `SyntaxError: Unexpected token < in JSON` | Server returned HTML (error page) instead of JSON | The PHP side has an error — check PHP log |
| `net::ERR_CONNECTION_REFUSED` | Server/Apache is down | Restart Apache/server |
| `404 Not Found` on AJAX | URL routing error | Controller method doesn't exist or wrong URL |
| `419 Page Expired` or CSRF error | Token expired | Page was open too long; reload |

### AJAX Response Debugging
```
F12 → Network tab → click the failed request:
- Status: 200 but wrong data? → PHP logic error
- Status: 302? → Redirect (usually session expired)
- Status: 403? → Permission/CSRF issue
- Status: 500? → PHP error (check PHP log)
- Response body starts with <? → PHP error dumped as output
```

---

## Quick Diagnosis Commands

```bash
# Check today's PHP error log
tail -50 admin/application/logs/log-$(date +%Y-%m-%d).php

# Check Apache errors
tail -20 /var/log/apache2/error.log

# PHP syntax check
php -l admin/application/controllers/admin_ret_billing.php

# Find recent PHP errors
grep -r "ERROR" admin/application/logs/log-$(date +%Y-%m-%d).php | tail -10

# Check if MySQL is running
mysqladmin -u root status

# Check PHP version
php -v
```
