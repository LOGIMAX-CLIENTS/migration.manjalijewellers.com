# Recipe: Missing Diamond Weight Column in Order Status Report

## Metadata
- **Pattern ID**: PAT-RPT-DIA01
- **Severity**: MEDIUM
- **Modules Affected**: Reports (Order Status Report)
- **Auto-fixable**: No (requires coordinated JS + PHP view edit)

## Client Scope
- **Applies to**: ALL clients using diamond/stone order workflow
- **Reason**: The SQL query already returns `dia_wt` via the `ordDia` subquery (stone_type=1), but the DataTable column was never wired up in the JS and the HTML header was never added.

## Created By
- **Developer**: Antigravity AI
- **Client**: erp.sparqlediamonds.com
- **Date**: 2026-04-23
- **Source Bug ID**: N/A

## Symptom
In the Order Status Report (`/admin_ret_reports/order_status/list`), the Diamond Weight field is missing from the table. Users cannot see diamond weight data even though orders have diamonds attached via `ret_order_item_stones`.

## Root Cause
The `order_status()` model function (line 5812 in `ret_reports_model.php`) already includes:
```sql
LEFT JOIN(SELECT co.id_customerorder, IFNULL(SUM(os.wt),0) as stn_wt
  FROM ret_order_item_stones os
  LEFT JOIN ret_stone st ON st.stone_id = os.stone_id
  WHERE st.stone_type = 1
  GROUP BY co.id_customerorder) as ordDia on ordDia.id_customerorder = c.id_customerorder
```
And SELECT: `round(IFNULL(ordDia.stn_wt,0),3) as dia_wt`

But the JavaScript DataTable (`set_order_status_report()` in `ret_reports.js`) never had a `{ "mDataProp": "dia_wt" }` entry in `aoColumns`, the HTML `<thead>` had no `<th>` for it, and the `<tfoot>` had no corresponding `<td>`. This caused the field to be silently discarded from the rendered table.

## Detection
```bash
# Check JS aoColumns for dia_wt in order status function
grep -n "dia_wt" admin/assets/js/ret_reports.js

# Check HTML header for Dia Wt
grep -n "Dia Wt" admin/application/views/ret_reports/order_status.php
```

## Files
- `admin/assets/js/ret_reports.js` — `set_order_status_report()` function
- `admin/application/views/ret_reports/order_status.php` — table `<thead>` and `<tfoot>`

## Fix

### JS — Before (`ret_reports.js`, inside `set_order_status_report()`)

**columnDefs targets** (was missing index 25 and 26→27 shift):
```javascript
targets: [7, 8, 9, 21, 22, 23, 24, 25, 26],
```

**aoColumns** (missing dia_wt between net_wt and stn_amt):
```javascript
{ "mDataProp": "net_wt" },
{ "mDataProp": "stn_amt" },
```

**footerCallback** (stone_value at 25, mc_value at 26):
```javascript
var stone_value = api.column(25, { page: 'current' }).data().reduce(...);
var mc_value    = api.column(26, { page: 'current' }).data().reduce(...);
$(api.column(25).footer()).html(...stone_value...);
$(api.column(26).footer()).html(...mc_value...);
```

### JS — After

**columnDefs targets:**
```javascript
targets: [7, 8, 9, 21, 22, 23, 24, 25, 26, 27],
```

**aoColumns** (dia_wt inserted at index 25):
```javascript
{ "mDataProp": "net_wt" },
{ "mDataProp": "dia_wt" },
{ "mDataProp": "stn_amt" },
```

**footerCallback** (dia_wt at 25, stn_amt shifted to 26, mc shifted to 27):
```javascript
var dia_wt_total = api.column(25, { page: 'current' }).data().reduce(...);
var stone_value  = api.column(26, { page: 'current' }).data().reduce(...);
var mc_value     = api.column(27, { page: 'current' }).data().reduce(...);
$(api.column(25).footer()).html(money_format_india(parseFloat(dia_wt_total).toFixed(3)));
$(api.column(26).footer()).html(money_format_india(parseFloat(stone_value).toFixed(2)));
$(api.column(27).footer()).html(money_format_india(parseFloat(mc_value).toFixed(2)));
```

### HTML — Before (`order_status.php`, inside `<thead>`)
```html
<th width="10%">NWT</th>
<!-- blank line with tabs -->
<th width="10%">stone Value</th>
```

### HTML — After
```html
<th width="10%">NWT</th>

<th width="10%">Dia Wt</th>

<th width="10%">stone Value</th>
```

Also add one extra `<td style="text-align:right"></td>` in the `<tfoot>` row to keep footer column count in sync.

## Verification
1. Navigate to `/admin_ret_reports/order_status/list`
2. Confirm "Dia Wt" header appears between NWT and stone Value in the table header
3. Search with a date range — rows with diamond orders should show `dia_wt` value (3dp)
4. Footer row should show summed Dia Wt total
5. No JS console errors (no DataTable "Requested unknown parameter" warnings)
6. Column visibility (colvis) and Excel export should include Dia Wt

## Notes
- The SQL already returns `dia_wt` so no backend changes needed.
- When inserting a new column between existing ones, ALWAYS update: (a) `columnDefs` targets array, (b) `aoColumns` array, (c) `footerCallback` variable column indexes AND footer html output column calls.
- The `<tfoot>` `<td>` count must match the number of `aoColumns` entries — DataTables will silently break footer rendering if they differ.
- Related pattern: PAT-DT-001 (footer callback column index mismatch).
