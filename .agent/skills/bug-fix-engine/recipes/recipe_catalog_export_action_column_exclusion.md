# Catalog DataTables Export — Action Column Exclusion

## Metadata
- **Pattern ID**: PAT-CTG-001
- **Severity**: MEDIUM
- **Modules Affected**: Retail Catalog (Design Mapping, Sub-Design Mapping, Karigar Product Mapping, Design List)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients using Retail Catalog module with DataTables export buttons

## Created By
- **Developer**: Antigravity (Black Horest) / Karthi
- **Client**: Source (retailsource)
- **Date**: 2026-06-08
- **Source Bug ID**: Design Mapping Bug #4, #5; Sub-Design Bug #12, #13, #14

## Symptom
When clicking Excel or Print buttons on DataTables, the Action column (containing Delete/Edit buttons as HTML) is included in the export. This results in garbage HTML content in exported files and print copies.

## Root Cause
DataTables `buttons` configuration uses shorthand `['excel','print']` which exports ALL columns including the Action column. No `exportOptions` with specific column indices is defined.

## Detection
```command
grep -n "\"buttons\" : \['excel','print'\]" admin/assets/js/catalog_master.js
```

## Files
- `admin/assets/js/catalog_master.js`

## Fix

### Before
```javascript
"buttons" : ['excel','print'],
```

### After
```javascript
"buttons": [ 
    { extend: 'excel', exportOptions: { columns: [0,1,2] } },
    { extend: 'print', exportOptions: { columns: [0,1,2] } }
],
```

> **Important**: The column indices in `exportOptions` must match the actual data columns, EXCLUDING the Action column (which is always the last column). Count from 0. For a 4-column table (ID, Product, Design, Action), use `[0,1,2]`. For a 5-column table, use `[0,1,2,3]`.

## Verification
1. Navigate to Design Mapping list page
2. Click the "Excel" button — verify the downloaded file does NOT contain an Action column
3. Click the "Print" button — verify the print preview does NOT contain an Action column
4. Repeat for Sub-Design Mapping and Karigar Product Mapping pages
5. Verify that all data columns (ID, Product, Design) are still present in exports

## Notes
- Each DataTable instance may have different column counts — always verify the column indices match the visible data columns
- The `exportOptions.columns` array is 0-indexed
- This pattern applies to ANY DataTable where Action columns contain HTML elements (buttons, icons, links)
- Already-configured tables with `exportOptions` should NOT be modified
