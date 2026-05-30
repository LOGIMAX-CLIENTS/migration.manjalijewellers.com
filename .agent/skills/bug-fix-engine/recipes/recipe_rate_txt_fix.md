# Recipe: rate.txt Array Bug Fix

## Metadata
- **Pattern ID**: PAT-FILE-001
- **Severity**: CRITICAL
- **Modules Affected**: Settings (metal_rates)
- **Auto-fixable**: Yes

## Symptom
Mobile app shows "Array" instead of metal rates. The `rate.txt` file that the mobile API reads contains the literal string "Array" instead of JSON data. All mobile users see broken rates.

## Root Cause
`file_put_contents()` is called with a PHP array as the second argument. PHP silently converts arrays to the string "Array" when written to file. The correct approach is `json_encode()` before writing.

**Source line 571** (metal_rates 'Save' case):
```php
file_put_contents('../api/rate.txt', $rate_array);
```

Note: The `update_rate_file()` function at line 494 already does this correctly:
```php
$content = json_encode($insertRate);
file_put_contents($file, $content);
```

## Detection
```command
grep -n "file_put_contents.*rate\.txt" admin/application/controllers/admin_settings.php
```

## Files
- `admin/application/controllers/admin_settings.php` (line 571)

## Fix

### Before
```php
                file_put_contents('../api/rate.txt', $rate_array);
```

### After
```php
                file_put_contents('../api/rate.txt', json_encode($rate_array));
```

## Verification
1. Save a new metal rate in Settings → Metal Rates
2. Check `api/rate.txt` — should contain valid JSON like `{"goldrate_22ct":"5800","goldrate_24ct":"6200",...}`
3. Open mobile app → rates should display correctly
4. Verify `json_decode(file_get_contents('api/rate.txt'))` returns valid object

## Notes
- There's a second instance at line 668 inside a commented-out `mjdma_update` case — if any client has uncommented this, they have the same bug
- The `update_rate_file()` function already does `json_encode` correctly — the bug is only in the `metal_rates('Save')` case
- Some clients may call `update_rate_file()` after save (line 593-594 is commented out) — those clients may not be affected
