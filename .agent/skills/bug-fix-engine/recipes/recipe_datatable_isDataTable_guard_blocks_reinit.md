# Recipe: DataTable isDataTable Guard Blocks Re-initialization on Repeated Search

## Metadata
- **Pattern ID**: PAT-DT-002
- **Severity**: MEDIUM
- **Modules Affected**: ret_reports (any report module using manual HTML injection + DataTable)
- **Auto-fixable**: Yes (string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal pattern — any report page using `isDataTable()` guard with manual tbody HTML injection will exhibit this bug

## Created By
- **Developer**: Antigravity AI
- **Client**: etail_development_src (source)
- **Date**: 2026-05-09
- **Source Bug ID**: N/A

## Symptom
On a report page using jQuery DataTables:
1. **First search** works correctly — data loads, table initializes, footer shows correct entry count, Print/Excel/Column visibility buttons work.
2. **Second search** (same or different filters) — the HTML rows appear in the table body visually, but DataTable components fail:
   - Footer shows **"Showing 0 to 0 of 0 entries"**
   - Pagination doesn't update
   - Excel export gets stuck in "Processing" state (DataTable internally believes 0 records)
   - Print exports empty table
3. **Page reload** fixes it temporarily (until the next second search).

## Root Cause
The AJAX success callback injects new HTML directly into `<tbody>` via `.html(trHTML)`, then wraps the DataTable initialization inside a guard:

```javascript
if (!$.fn.DataTable.isDataTable('#table_id')) {
    oTable = $('#table_id').dataTable({...});
}
```

**On the first search**, `isDataTable()` returns `false` → DataTable initializes correctly.

**On subsequent searches**, `isDataTable()` returns `true` (table was already initialized) → the `if` block is **skipped entirely**. The new HTML rows are in the DOM, but DataTable's internal state (row count, pagination, column data) is stale from the previous search. The DataTable object and the DOM are desynchronized.

Additionally, the code often has `fnClearTable()` / `fnDestroy()` calls at the top of the success callback to try to clean up, but these are unreliable — they can auto-initialize an empty DataTable on first load (when no DataTable exists yet) and cause race conditions with the guard check later.

## Detection
```command
grep -rn "isDataTable" admin/assets/js/*.js | grep -v "//" | grep "!"
```

Look for the pattern: `if (!$.fn.DataTable.isDataTable('#some_table'))` followed by a `.dataTable({...})` initialization. If the same success callback also injects HTML via `.html(trHTML)` before this check, the bug is present.

Also check for the pre-destroy pattern that often accompanies it:
```command
grep -rn "fnClearTable\|fnDestroy" admin/assets/js/*.js | grep -v "//"
```

## Files
- `admin/assets/js/ret_reports.js` (primary — multiple instances)
- Any JS file containing report AJAX with manual HTML injection + DataTable init

## Fix

### Before
```javascript
// Pattern 1: Guard that blocks re-initialization
$('#table_id > tbody').html(trHTML);
// Check and initialise datatable
if (!$.fn.DataTable.isDataTable('#table_id')) {
    oTable = $('#table_id').dataTable({
        // ... config ...
    });
}
```

```javascript
// Pattern 2: Unreliable pre-destroy at top of success callback
success: function (data) {
    $("div.overlay").css("display", "none");
    $("#table_id > tbody > tr").remove();
    $('#table_id').dataTable().fnClearTable();
    $('#table_id').dataTable().fnDestroy();
    var trHTML = '';
```

### After
```javascript
// Pattern 1 fix: Always destroy, then always initialize
$('#table_id > tbody').html(trHTML);
// Destroy existing datatable before re-initialising
if ($.fn.DataTable.isDataTable('#table_id')) {
    $('#table_id').DataTable().destroy();
}
    oTable = $('#table_id').dataTable({
        // ... config ...
    });
```

```javascript
// Pattern 2 fix: Remove unreliable pre-destroy (handled at init point now)
success: function (data) {
    $("div.overlay").css("display", "none");
    var trHTML = '';
```

### Key Changes
1. **Invert the guard logic**: Change `if (!isDataTable)` → `if (isDataTable) { destroy(); }` — this ensures any existing instance is cleaned up before a fresh init.
2. **Remove the closing `}` of the old `if` block** — the `.dataTable({...})` call is now unconditional (always runs).
3. **Remove the old `fnClearTable()` / `fnDestroy()` / `.remove()` calls** at the top of the success callback — they're redundant and can cause errors on first load.
4. **Use `.DataTable().destroy()`** (uppercase D) instead of `.dataTable().fnDestroy()` — the new API is more reliable.

## Verification
1. Navigate to the report page
2. Select filters and click **Search** → verify data loads with correct entry count in footer
3. Click **Search** again without changing filters → verify data still displays correctly with correct footer count
4. Change a filter (e.g., date range) and click **Search** → verify new data loads correctly
5. Click **Excel** button → verify export captures all visible rows (not 0 rows)
6. Click **Print** button → verify print preview shows all data
7. Test with empty results (e.g., today with no transactions) then switch back to a date range with data → verify table recovers

## Notes
- This is an extremely common pattern in the codebase. Any report page that manually builds HTML rows and uses `isDataTable()` guard is likely affected.
- The `"destroy": true` option in the DataTable config was supposed to handle this, but it only works when the DataTable is re-initialized via the API, not when it's guarded by the `if` check that prevents re-initialization entirely.
- The `bill_wise_smry_transactiton_list` table (Summary report type) had the same bug pattern — always check all table IDs in the same function.
- Related pattern: PAT-DT-001 (footer callback column index mismatch) — often found alongside this bug in the same report functions.
