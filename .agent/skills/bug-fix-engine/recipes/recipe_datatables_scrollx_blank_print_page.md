# DataTables scrollX Blank Second Print Page + Hidden Columns

## Metadata
- **Pattern ID**: PAT-RPT-PRINT-001
- **Severity**: MEDIUM
- **Modules Affected**: Reports (Cash Book, any DataTable with scrollX + Print button)
- **Auto-fixable**: Yes (column width % values are standard; copy pattern)

## Client Scope
- **Applies to**: ALL
- **Reason**: Any report view using DataTables with `scrollX: '100%'` and the Buttons print extension will exhibit this bug if the HTML `<table>` has no explicit column widths.

## Created By
- **Developer**: Antigravity
- **Client**: navratnajewellery
- **Date**: 2026-04-21
- **Source Bug ID**: RPT-CB001

## Symptom
When clicking the DataTables **Print** button on a report with `scrollX: '100%'`:
1. The print preview shows **2 pages** — page 1 has all data, page 2 is **completely blank**
2. Some columns (DEBIT, CREDIT) may be **clipped/invisible** in the print output
3. The **Layout option** (Landscape/Portrait) may be missing from Chrome's print dialog

## Root Cause
Three separate issues, all stemming from missing column width anchors in the HTML:

1. **Blank second page**: DataTables `scrollX` wraps the table in `.dataTables_scrollBody` with an inline `style="height: Xpx; overflow: auto;"` set from the on-screen viewport size. In the print window, this reserved height creates a phantom blank page after the content. Without explicit `%` column widths in the HTML, DataTables uses pixel widths that don't constrain cleanly → content bleeds → page 2.

2. **Clipped columns**: Without `table-layout: fixed` and column `%` widths, the print window inherits the scrollX pixel widths from screen layout. On a portrait page (~700px printable), columns 4-5 overflow and get clipped.

3. **Missing Layout dropdown**: If `@page { size: landscape; }` is injected into the print window via `customize`, Chrome interprets the orientation as CSS-controlled and **removes** the Layout selector from the print dialog.

## Detection
```command
# Find report views using DataTables without column width styles
grep -rn "table-bordered" admin/application/views/ret_reports/ | grep -v "width"

# Find scrollX usage in JS
grep -n "scrollX" admin/assets/js/ret_reports.js
```

## Files
- `admin/application/views/ret_reports/cash_book.php` (view — HTML table)
- `admin/assets/js/ret_reports.js` (DataTables init + print button customize callback)

## Fix

### Step 1: Update the view file — add column widths to HTML table

#### Before (`cash_book.php`)
```php
// @media print CSS
@media print {
    html, body {
        height: auto;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible;
    }
}

// Table element — no width or layout style
<table id="cash_book_list"
    class="table table-bordered table-striped text-center">
    <thead>
        <tr>
           <td>TRANSDATE</td>
           <td>TRANNO</td>
           <td>PARTICULAR</td>
           <td>DEBIT</td>
           <td>CREDIT</td>
        </tr>
    </thead>
```

#### After (`cash_book.php`)
```php
// @media print CSS — match etail reference exactly
@media print {
    html,
    body {
        height: auto;
        width: 100%;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden;
        table-layout: fixed;
    }
}

// Table element — explicit width + table-layout:fixed
<table id="cash_book_list"
    class="table table-bordered table-striped text-center" style="width: 100%;table-layout:fixed;">
    <thead>
        <tr>
           <td style="width:20% !important;">TRANSDATE</td>
           <td style="width:20% !important;">TRANNO</td>
           <td style="width:30% !important;">PARTICULAR</td>
           <td style="width:20% !important;">DEBIT</td>
           <td style="width:10% !important;">CREDIT</td>
        </tr>
    </thead>
```

### Step 2: Update the JS customize callback — match etail reference exactly

#### Before (`ret_reports.js` — inside DataTable print button config)
```javascript
// Any version that injects @page{size:landscape} or manipulates scroll wrappers
customize: function (win) {
    $(win.document.head).append(
        '<style>' +
        '@page { size: landscape; margin: 8mm; }' +
        '.dataTables_scrollBody { overflow: visible !important; ... }' +
        '</style>'
    );
    $(win.document.body).find('table')
        .css('table-layout', 'auto')
        ...
},
```

#### After (`ret_reports.js` — etail-exact pattern)
```javascript
customize: function (win) {
    $(win.document.body).find('table')
        .addClass('compact');
    $(win.document.body).find('table')
        .addClass('compact')
        .css('font-size', '10px')
        .css('font-family', 'sans-serif')
        .css('table-layout', 'fixed')
        .css('autoWidth', false)
        .css('width', '100%');
},
```

## Verification
1. Hard-refresh the browser (Ctrl+Shift+R) after applying fix
2. Run the Cash Book report for any date range
3. Click **Print** button
4. Open **More settings** → confirm **Layout** dropdown is visible (shows Portrait/Landscape)
5. Select **Landscape**
6. Confirm print preview shows **1 page only** (no blank second page)
7. Confirm all 5 columns (TRANSDATE, TRANNO, PARTICULAR, DEBIT, CREDIT) are visible and not clipped

## Notes
- **DO NOT** inject `@page { size: landscape; }` into the print window — this hides the Layout dropdown in Chrome's print dialog. The user should manually select Landscape.
- **DO NOT** use `removeAttr('style')` on `.dataTables_scrollBody` — this is a workaround that addresses symptoms, not root cause. Fix at the HTML level instead.
- **Root fix is always in the HTML**: Give every column an explicit `%` width + set `table-layout:fixed` on the `<table>`. The JS `customize` callback only needs compact font + width:100%.
- Column width distribution for 5-column cash book: `20% / 20% / 30% / 20% / 10%` (PARTICULAR gets extra width as it holds text descriptions)
- This pattern applies to ANY DataTables report with `scrollX` + print button. Check all report views if the same issue appears elsewhere.
