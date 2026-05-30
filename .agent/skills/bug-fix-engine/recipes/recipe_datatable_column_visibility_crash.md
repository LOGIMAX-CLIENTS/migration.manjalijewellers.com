# DataTable Column Visibility API Crash on Re-search

> DataTables `.column(N).visible()` API physically removes/adds DOM nodes. When combined with manual jQuery thead manipulation (`.hide()`, `.attr('colspan')`), the internal node cache becomes corrupted. Subsequent `.columns().visible(true)` or `.destroy()` calls crash with `Cannot read properties of undefined (reading 'style')`.

## Metadata
- **Pattern ID**: PAT-DT-001
- **Severity**: HIGH
- **Modules Affected**: ret_reports (Supplier Unified Transaction), any module using DataTables column visibility + custom thead
- **Auto-fixable**: No (requires understanding of column indices per report)

## Client Scope
- **Applies to**: ALL
- **Reason**: Any DataTable report that conditionally hides columns AND manually manipulates thead headers will hit this

## Created By
- **Developer**: Antigravity
- **Client**: erp.sriammanjewellers.in
- **Date**: 2026-05-09
- **Source Bug ID**: N/A

## Symptom
1. User searches with metal filter → columns hidden, page works fine
2. User removes metal filter → clicks Search again → page crashes with console error:
   ```
   Uncaught TypeError: Cannot read properties of undefined (reading 'style')
   at Ga (jquery.dataTables.min.js:175:78)
   at t.visible (jquery.dataTables.min.js:295:204)
   ```
3. No data loads, table is broken

## Root Cause
DataTables' `.column(N).visible(false)` **physically removes `<td>` elements** from DOM rows and caches node references internally. When you also manipulate `<thead>` with jQuery (`.hide()` on grouped header `<th>`, changing `colspan`), the cached nodes become stale/orphaned.

On next search, calling `dtApi.columns().visible(true)` attempts to re-insert cached nodes, but they reference undefined parent elements → crash on `.style` property access.

**Key insight**: DataTables column visibility is a DOM-level operation, NOT a CSS operation. It's fundamentally incompatible with manual thead manipulation.

## Detection
```command
grep -rn "\.column\(.*\)\.visible\(false\)" admin/assets/js/
grep -rn "\.columns()\.visible\(true\)" admin/assets/js/
```
Look for cases where the same table has BOTH:
1. `dtApi.column(N).visible(false)` calls
2. Manual jQuery thead manipulation (`.hide()`, `.attr('colspan')`)

## Files
- `admin/assets/js/ret_reports.js` (Supplier Unified Transaction section)

## Fix

### Strategy
Replace DataTables column visibility API with **CSS injection**. Inject a `<style>` tag with `nth-child` selectors to hide columns. This is display-only — DataTables has zero knowledge of hidden columns, so `destroy()` always works cleanly.

### Before (Destroy section)
```javascript
if ($.fn.DataTable.isDataTable('#smith_ledgere_list')) {
    var dtApi = $('#smith_ledgere_list').DataTable();
    dtApi.columns().visible(true);  // CRASH HERE
    dtApi.destroy();
}
```

### After (Destroy section)
```javascript
// Remove any previous metal-filter CSS overrides
$('#metal-col-hide-style').remove();
if ($.fn.DataTable.isDataTable('#smith_ledgere_list')) {
    $('#smith_ledgere_list').DataTable().destroy();
}
// Restore thead headers that may have been hidden/modified by metal filter
$('#smith_ledgere_list thead tr.tablerow th').each(function() {
    var txt = $(this).text().trim();
    if (txt === 'Amount') {
        $(this).show();
    }
    if (txt === 'Balance') {
        $(this).attr('colspan', 5);
    }
});
```

### Before (Column hide section)
```javascript
if (isMetalSelected && $.fn.DataTable.isDataTable('#smith_ledgere_list')) {
    var dtApi = $('#smith_ledgere_list').DataTable();
    dtApi.column(13).visible(false);
    dtApi.column(14).visible(false);
    dtApi.column(19).visible(false);
    // thead manipulation...
}
```

### After (Column hide section)
```javascript
if (isMetalSelected) {
    // CSS-based hiding — DataTables internal state untouched
    $('head').append('<style id="metal-col-hide-style">'
        + '#smith_ledgere_list tbody td:nth-child(14),'
        + '#smith_ledgere_list tbody td:nth-child(15),'
        + '#smith_ledgere_list tbody td:nth-child(20),'
        + '#smith_ledgere_list tfoot td:nth-child(14),'
        + '#smith_ledgere_list tfoot td:nth-child(15),'
        + '#smith_ledgere_list tfoot td:nth-child(20),'
        + '#smith_ledgere_list thead tr:not(.tablerow) th:nth-child(14),'
        + '#smith_ledgere_list thead tr:not(.tablerow) th:nth-child(15),'
        + '#smith_ledgere_list thead tr:not(.tablerow) th:nth-child(20)'
        + '{ display: none !important; }'
        + '</style>');
    // thead grouped header manipulation stays the same
    $('#smith_ledgere_list thead tr.tablerow th').each(function() {
        var txt = $(this).text().trim();
        if (txt === 'Amount') { $(this).hide(); }
        if (txt === 'Balance') { $(this).attr('colspan', 4); }
    });
}
```

## Verification
1. Select metal + karigar + date range → Search → columns should be hidden, data loads
2. Remove metal filter → Search → **no crash**, all columns visible, data loads
3. Any filter combo → Search → works consistently
4. Toggle metal on/off repeatedly → no crash on any cycle

## Notes
- **nth-child is 1-indexed** — column index 13 = `nth-child(14)`, column index 14 = `nth-child(15)`, etc.
- The `<style id="metal-col-hide-style">` tag must be removed at the START of each search (before destroy), not after init
- This pattern applies to ANY DataTable where you conditionally hide columns. Never use DataTables' `.visible()` API if you also manipulate thead manually
- Also fixed in same session: removed dead `metal_wise_required` variable and deleted ~95 lines of dead metal summary table code
