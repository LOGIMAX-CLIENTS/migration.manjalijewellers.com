# Recipe: Dead Code Cleanup (Date-Suffixed Methods)

## Metadata
- **Pattern ID**: PAT-DEAD-001
- **Severity**: MEDIUM
- **Modules Affected**: Reports (primary), Billing, potentially all modules
- **Auto-fixable**: Yes (identify → verify unused → delete)

## Symptom
No direct user symptom, but causes:
1. Report bugs when old method copy is called instead of current version
2. Codebase bloat (duplicate functions add 200-500 lines each)
3. Developer confusion about which version is active
4. Merge conflicts during updates

## Root Cause
Developers copy a method and rename it with a date suffix before modifying the original. The old copy is never deleted.

Example: `getBillDetails()` is the active method. `getBillDetails_30_08_2025()` is a dead copy from August 30, 2025. But some views or JS may still call the dated version, causing stale data.

## Detection
```command
grep -rn "function\s\+\w\+_[0-9]\{2\}_[0-9]\{2\}_[0-9]\{4\}" admin/application/models/ admin/application/controllers/
```

The fingerprint tool's dead code scanner (`--bug-scan`) auto-detects these.

## Files
- Any file containing functions matching the pattern `functionName_DD_MM_YYYY`

## Fix

### Step 1: Identify Dead Functions
Run fingerprint with `--bug-scan`:
```bash
php fingerprint.php --source="..." --client="..." --client-name="name" --bug-scan
```

### Step 2: Verify Each Function
For each dated function, check if it's still called:
```command
# Example: if getBillDetails_30_08_2025 exists
grep -rn "getBillDetails_30_08_2025" admin/
# If ZERO results outside the function definition → safe to delete
# If called from views/JS → need to update caller first
```

### Step 3: Delete or Redirect
**If unused**: Delete the entire function.

**If still called**: Update the caller to use the current version:
```php
// Before (in view/JS)
$result = $this->model->getBillDetails_30_08_2025($id);

// After
$result = $this->model->getBillDetails($id);
```

Then delete the dated function.

## Verification
1. Search for the deleted function name — should return zero results
2. Run the report/feature that uses the current version — should work correctly
3. Compare output of current vs old function — should be identical or improved

## Notes
- Found in srjewellery: `getBillDetails_30_08_2025` in Reports model (6 client-only dead functions)
- This is an ongoing problem — developers will keep creating dated copies
- Consider adding a pre-commit hook that warns on `function_name_DD_MM_YYYY` patterns
- Some dated functions may contain bug fixes NOT yet merged into the main function — review before deleting
