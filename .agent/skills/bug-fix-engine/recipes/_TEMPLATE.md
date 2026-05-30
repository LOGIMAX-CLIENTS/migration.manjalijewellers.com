# Recipe Template

> Copy this file and fill in all sections when creating a new recipe.

## Metadata
- **Pattern ID**: PAT-XXX-NNN
- **Severity**: CRITICAL / HIGH / MEDIUM / LOW
- **Modules Affected**: List of modules
- **Auto-fixable**: Yes / No (can string replacement fix this?)

## Client Scope
- **Applies to**: ALL | [specific client names, comma-separated]
- **Reason**: [if not ALL, explain why — custom code, custom settings, etc.]

## Created By
- **Developer**: [name]
- **Client**: [client where bug was first found]
- **Date**: [YYYY-MM-DD]
- **Source Bug ID**: [GitHub issue ID if exists, or N/A]

## Symptom
What the user sees or what goes wrong.

## Root Cause
Why the bug exists — the technical explanation.

## Detection
```command
grep -rn "search_pattern" admin/application/controllers/ admin/application/models/
```

## Files
- `admin/application/controllers/<file>.php`
- `admin/application/models/<file>.php`

## Fix

### Before
```php
// Exact code to find (copy from source)
$exact_buggy_code_here;
```

### After
```php
// Exact replacement code
$fixed_code_here;
```

## Verification
1. Step to verify the fix works
2. What to check in the UI/DB
3. Edge cases to test

## Notes
Any additional context, caveats, or related patterns.
