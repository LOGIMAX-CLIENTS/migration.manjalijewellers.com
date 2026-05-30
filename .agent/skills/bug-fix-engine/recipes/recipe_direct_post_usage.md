# Recipe: Direct $_POST usage → $this->input->post()

## Metadata
- **Pattern ID**: PAT-VAL-001
- **Severity**: MEDIUM
- **Modules Affected**: All controllers
- **Auto-fixable**: Yes (simple string replacement)

## Symptom
No direct user-visible symptom, but raw `$_POST` values bypass CodeIgniter's XSS filtering and CSRF protection. Makes the application vulnerable to injection attacks.

## Root Cause
Developers used native PHP `$_POST['key']` instead of CodeIgniter's `$this->input->post('key')` which applies `xss_clean` filtering when enabled in config.

## Detection
```command
grep -rn "\$_POST\[" admin/application/controllers/
```

## Files
- Any controller file containing `$_POST[`

## Fix

### Before
```php
$_POST['
```

### After
```php
$this->input->post('
```

> **Note**: This is a partial pattern match. The closing `']` needs context-specific handling.
> Full replacement: `$_POST['key']` → `$this->input->post('key')`
> Auto-fix applies to simple cases. Complex expressions like `$_POST['items'][$i]` need manual review.

### Manual Pattern
For each instance, replace:
```php
// Before
$value = $_POST['field_name'];

// After  
$value = $this->input->post('field_name');
```

## Verification
1. Search for remaining `$_POST[` in controllers — should be zero
2. Test form submissions still work (input->post returns the same values)
3. Check that array inputs like `$_POST['items']` are handled correctly with `$this->input->post('items')`

## Notes
- `$_POST` in models is also wrong but less critical (models shouldn't access request data directly)
- `$_GET` has the same issue — use `$this->input->get()` instead
- Some `$_POST` usages in file upload handlers (`$_FILES`) are legitimate — don't replace those
- Count of instances is a useful metric: >50 = systematic issue, <10 = isolated
