# Recipe: Closing Stock Report Filter Alignment and Layout Fixes

## Metadata
- **Pattern ID**: PAT-UI-002
- **Severity**: LOW
- **Modules Affected**: Reports
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Common alignment/overlap layout issues with filter dropdowns in a Bootstrap grid container.

## Created By
- **Developer**: Antigravity
- **Client**: AMS-RetailAdmin
- **Date**: 2026-06-02
- **Source Bug ID**: N/A

## Symptom
1. The Select Branch dropdown and State Filter dropdown overlap on the Closing Stock report page.
2. The Date, Report Type, and Search fields are misaligned, wrapped, or pushed to the middle-right instead of starting cleanly on the left on the second row of filters.

## Root Cause
1. **Select2 Overlap**: The `<select id="branch_select">` element was missing `style="width: 100%;"`. Select2 fell back to calculating its width dynamically, causing it to overflow its container and overlap the floated State Filter column.
2. **Float Snagging/Alignment**: All filter fields were inside a single `.row` container. Because fields had varying heights (e.g., Stock Type uses native `<select>` which is shorter than Select2 components), the wrapped elements floated to the left but snagged on the taller elements from the first row, resulting in Date, Report Type, and Search starting from the middle-right of the second row instead of aligning to the left.

## Detection
```command
grep -rn 'id="branch_select"' application/views/ret_reports/closing_stock.php
```
Verify if the filter fields are split into distinct `.row` elements or if a single `.row` contains too many elements causing float misalignment.

## Files
- `application/views/ret_reports/closing_stock.php`

## Fix

### Fix 1: Select2 Width Spacing
#### Before
```php
<select id="branch_select" class="form-control branch_filter"></select>
```
#### After
```php
<select id="branch_select" class="form-control branch_filter" style="width: 100%;"></select>
```

### Fix 2: Split Row for Alignment & Spacing
#### Before
```php
                                                    <select id="category" style="width: 100%;"></select>
                                                 </div>
                                             </div>

                                             <div class="col-md-2">
```
#### After
```php
                                                     <select id="category" style="width: 100%;"></select>
                                                 </div>
                                             </div>
                                        </div>

                                        <div class="row" style="margin-top: 15px;">

                                             <div class="col-md-2">
```

## Verification
1. Load the Closing Stock report page (`/admin_ret_reports/closing_stock/list`).
2. Verify that both the Select Branch and State Filter dropdowns are rendered side-by-side with appropriate spacing and alignment.
3. Verify that the Date, Report Type, and Search fields are cleanly aligned starting from the left on the second row.
4. Verify that there is a clean vertical space between the first and second row of filters.

## Notes
Always ensure Select2 elements have `style="width: 100%;"` set, and group multi-row form layouts into separate `.row` containers (with appropriate margin-top spacing) to avoid CSS float snagging and maintain responsive layouts.
