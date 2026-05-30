# Recipe: Billing Print 500 Error — Short Open Tags + HTACCESS POST Strip

## Metadata
- **Pattern ID**: PAT-BIL-PRINT-001
- **Severity**: HIGH
- **Modules Affected**: Billing (Invoice Print), Any module using AJAX POST through Apache
- **Auto-fixable**: Partially — short tag fix is auto-fixable; .htaccess must be reviewed manually

## Client Scope
- **Applies to**: ALL clients deployed on Apache (Laragon/cPanel/VPS)
- **Reason**: Apache `.htaccess` redirect rules and PHP short open tags are environment-agnostic bugs

## Created By
- **Developer**: Antigravity (AI)
- **Client**: erp.manepally.com
- **Date**: 2026-05-18
- **Source Bug ID**: BIL-PRINT-001

## Symptom
1. Clicking the Print button on a billing invoice renders a blank page or triggers a
   silent HTTP 500 error — no visible error message.
2. AJAX API calls that rely on POST data silently fail — controllers receive empty `$_POST`
   arrays even though the browser sent valid form data.
3. After deployment to a new server / Laragon environment, print and AJAX functionality
   breaks even though everything worked before.

## Root Cause

### Root Cause 1 — PHP Short Open Tags in View Files
PHP view files contain `<?` short open tags instead of the full `<?php` form:
```php
<? // ← Breaks if short_open_tag = Off in php.ini (default in PHP 8+)
```
When `short_open_tag` is disabled (the PHP 8.x default), the server fails to parse the
template and returns a silent 500 with no output — appearing as a blank print page.

### Root Cause 2 — .htaccess Trailing-Slash Redirect Strips POST Data
A `.htaccess` redirect rule forces a trailing slash on all URLs:
```apache
RewriteRule ^(.+[^/])$ $1/ [R=301,L]
```
When a 301 redirect is issued, browsers convert the POST request to a GET request
(per HTTP spec), discarding all POST body data. AJAX calls hit this redirect, lose
their payload, and the controller receives an empty `$_POST` — causing the action
to silently fail or return unexpected results.

## Detection

### Detect short open tags:
```powershell
# Find PHP files using short open tags in views
grep -rn "^<?" admin/application/views/ --include="*.php" | grep -v "<?php" | grep -v "<?xml"
```

### Detect the trailing-slash redirect in .htaccess:
```powershell
grep -n "trailing\|R=301\|\[R=" .htaccess
# Or manually inspect:
Get-Content .htaccess | Select-String "R=301|trailing"
```

## Files
- `admin/application/views/billing/print.php` (or whichever view has short tags)
- `.htaccess` (project root)

## Fix

### Fix 1 — Short Open Tags

#### Before
```php
<?
    echo $variable;
    foreach ($items as $item):
?>
    <td><?= $item['name'] ?></td>
<?
    endforeach;
?>
```

#### After
```php
<?php
    echo $variable;
    foreach ($items as $item):
?>
    <td><?php echo $item['name']; ?></td>
<?php
    endforeach;
?>
```

**Note:** `<?= ... ?>` (short echo) is acceptable in PHP 5.4+ regardless of
`short_open_tag` — only `<?` (without `=` or `php`) is broken.

### Fix 2 — Remove Trailing-Slash Redirect from .htaccess

#### Before
```apache
# In .htaccess — REMOVE this block:
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.+[^/])$ $1/ [R=301,L]
```

#### After
```apache
# Remove the trailing-slash redirect entirely.
# CodeIgniter does not require trailing slashes.
# Standard CI .htaccess (keep only this):
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

## Verification

1. **Short tag fix:** Open the print URL directly in browser. The invoice must render
   with all data. No blank page, no 500 error.

2. **PHP syntax check after fixing tags:**
   ```powershell
   php -l admin/application/views/billing/print.php
   # Expected: No syntax errors detected
   ```

3. **AJAX fix:** Open browser DevTools → Network tab. Trigger a form submit that uses
   POST. Confirm the request does NOT receive a 301 redirect. The request must go
   directly to the controller with full POST body.

4. **Smoke test:** Test Save, Edit, Print, and Cancel on the billing module — all must
   work without redirects stripping POST data.

5. **Check error_log:** No `PHP Parse error` entries related to the fixed view file.

## Notes
- This is a **dual-cause** bug. Both issues must be fixed; fixing only one leaves
  the other active.
- The trailing-slash redirect is sometimes added by cPanel auto-configuration or by
  developers who copy `.htaccess` from other projects. Always audit `.htaccess` when
  deploying to a new server.
- `short_open_tag = On` can be set in `php.ini` or `.user.ini` as a workaround, but
  this is not recommended. Fix the source files instead — it's portable across all
  PHP configurations.
- Related pattern: `recipe_ajax_form_submit_302_redirect.md` covers the 302 variant
  of POST-data loss from redirects.
