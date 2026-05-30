# Recipe: Tagging List — Excel Export & Grand Total Row

## Metadata
- **Pattern ID**: PAT-TAG-001
- **Severity**: MEDIUM
- **Modules Affected**: Tagging (admin_ret_tagging)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Standard DataTables enhancement — any client using tagging listing page benefits from Excel export and total row

## Created By
- **Developer**: Antigravity AI
- **Client**: Karpagam Jewels
- **Date**: 2026-04-29
- **Source Bug ID**: N/A (feature enhancement)

## Symptom
Tagging listing page (`admin_ret_tagging/tagging/list`) had no Excel export option and no grand total row. Users had to manually calculate totals across paginated data.

## Root Cause
Three missing pieces:
1. **No `<tfoot>`** in `tag_list.php` — DataTables needs `<tfoot>` to render a footer row
2. **No `footerCallback`** in `ret_tagging.js` — no logic to compute grand totals across all pages
3. **No DataTables Buttons libraries** in `footer.php` for the `admin_ret_tagging` segment — the JS/CSS for Excel/Print buttons were never loaded

### Important: tfoot must NOT use colspan
Using `<th colspan="4">Total :</th>` in tfoot causes Excel export to repeat "Total :" across 4 columns. DataTables Excel export treats each `<th>` individually — colspan doesn't translate. Use 9 individual `<th>` cells instead.

### Important: jquery.dataTables.min.css required for single-line button layout
The `dom: 'Blfrtip'` layout needs `jquery.dataTables.min.css` to provide float rules (`.dataTables_length { float:left }`, `.dataTables_filter { float:right }`). Without this CSS, buttons wrap to a second line. The reports page loads this CSS — tagging must too.

## Detection
```command
grep -n "tag_list" admin/application/views/tagging/tag_list.php | head -5
grep -n "footerCallback" admin/assets/js/ret_tagging.js
grep -n "admin_ret_tagging" admin/application/views/layout/footer.php
```

## Files
- `admin/application/views/tagging/tag_list.php`
- `admin/assets/js/ret_tagging.js`
- `admin/application/views/layout/footer.php`

## Fix

### File 1: tag_list.php — Add tfoot (after `</thead>`)

#### Before
```html
                    </thead>
                    <tbody></tbody>
                 </table>
```

#### After
```html
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                      <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th style="text-align:right">Total :</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                      </tr>
                    </tfoot> 
                 </table>
```

### File 2: ret_tagging.js — Add dom, buttons, lengthMenu, and footerCallback to DataTable init

#### Before
```javascript
	        oTable = $('#tag_list').dataTable({
			"bDestroy": true,
			"bInfo": true,
			"bFilter": true, 
			"order": [[ 0, "desc" ]],
			"bSort": true,
			"aaData": data,
```

#### After
```javascript
	        oTable = $('#tag_list').dataTable({
			"bDestroy": true,
			"bInfo": true,
			"bFilter": true, 
			"order": [[ 0, "desc" ]],
			"bSort": true,
			"dom": 'lBfrtip',
			 "lengthMenu": [ [ 10, 25, 50, -1], [10, 25, 50, "All"] ],
			 "buttons" : [
				{
					extend: 'excel',
					footer: true,
					title: 'Tagging List'
				}
			],
			"aaData": data,
```

And add footerCallback after aoColumns closing bracket:

```javascript
			"footerCallback": function (row, data, start, end, display) {
				var api = this.api();
				var intVal = function (i) {
					return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
				};

				var totalGross = api.column(4).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
				var totalNet   = api.column(5).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
				var totalStn   = api.column(6).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
				var totalDia   = api.column(7).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
				var totalPcs   = api.column(8).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

				$(api.column(3).footer()).html('Total :');
				$(api.column(4).footer()).html(parseFloat(totalGross).toFixed(3));
				$(api.column(5).footer()).html(parseFloat(totalNet).toFixed(3));
				$(api.column(6).footer()).html(parseFloat(totalStn).toFixed(3));
				$(api.column(7).footer()).html(parseFloat(totalDia).toFixed(3));
				$(api.column(8).footer()).html(parseFloat(totalPcs).toFixed(0));
			}
```

### File 3: footer.php — Add DataTables Buttons libraries for admin_ret_tagging

#### Before
```php
	<?php if($this->uri->segment(1)=='admin_ret_tagging'){ ?>
	    <script src="<?php echo base_url();?>assets/plugins/shortcutkeys/JQuery.ShortcutKeys-1.0.0.js" type="text/javascript"></script>
	    <script src="<?php echo base_url();?>assets/js/ret_tagging.js?v=<?php echo $version;?>" type="text/javascript"></script>
	<?php } ?>
```

#### After
```php
	<?php if($this->uri->segment(1)=='admin_ret_tagging'){ ?>
	    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/payment/dataTables.buttons.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/payment/buttons.flash.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/payment/jszip.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/payment/buttons.html5.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/payment/buttons.print.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
	    <script src="<?php echo base_url();?>assets/plugins/shortcutkeys/JQuery.ShortcutKeys-1.0.0.js" type="text/javascript"></script>
	    <script src="<?php echo base_url();?>assets/js/ret_tagging.js?v=<?php echo $version;?>" type="text/javascript"></script>
	<?php } ?>
```

## Verification
1. Navigate to `admin_ret_tagging/tagging/list` and click search
2. Verify Excel button appears on same line as "Show entries" dropdown
3. Verify "Total :" row at bottom with grand totals for Gross/Net/Stn/Dia Wgt and Pieces
4. Navigate to page 2, 3 — verify totals remain the same (all-data totals, not per-page)
5. Click Excel → verify downloaded file has all data rows + "Total :" row at bottom in ONE column only
6. Verify Search box is on the same line (right side)

## Notes
- The `footerCallback` uses `api.column(N).data()` which returns ALL data across all pages, not just the visible page — this is critical for accurate grand totals
- The `footer: true` in the buttons config ensures tfoot is included in Excel/Print exports
- Do NOT use `colspan` in tfoot — it causes "Total :" to repeat across multiple Excel columns
- The `jquery.dataTables.min.css` is required alongside `buttons.dataTables.min.css` for proper single-line button layout
- The buttons JS files are stored locally in `assets/js/payment/` directory (shared with reports/payment modules)
