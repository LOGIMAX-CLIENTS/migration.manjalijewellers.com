# Recipe Template

## Metadata
- **Pattern ID**: PAT-RPT-001
- **Severity**: LOW
- **Modules Affected**: Old Metal Purchase Report (`admin_ret_reports/old_metal_purchase`)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Standard reporting behaviour for chronological sorting.

## Created By
- **Developer**: Antigravity
- **Client**: ALL
- **Date**: 2026-05-19
- **Source Bug ID**: N/A

## Symptom
The "Old Metal Purchase" report sorts items by metal type (e.g., all Gold, then all Silver), rather than providing a continuous chronological list of purchases sorted by date.

## Root Cause
The SQL query was ordering by `metal_type, bill.bill_date ASC` and the PHP code was grouping the results by `$r['metal_type']` into a multi-dimensional array (`$old_matel_detail['item_details'][$r['metal_type']][] = $r;`). This forced the frontend to render the report grouped by metal.

## Detection
```command
grep -rn "ORDER BY metal_type,bill.id_branch" application/models/
```

## Files
- `application/models/ret_reports_model.php`

## Fix

### Before
```php
						    ORDER BY metal_type,bill.bill_date ASC,bill.id_branch");
		// print_r($this->db->last_query());exit;
		$result = $old_matel_query->result_array();
		foreach($result as $r){
			$old_matel_detail['item_details'][$r['metal_type']][] = $r;
		}
```

### After
```php
						    ORDER BY bill.bill_date ASC,bill.id_branch");
		// print_r($this->db->last_query());exit;
		$result = $old_matel_query->result_array();
		foreach($result as $r){
			$old_matel_detail['item_details']['ALL'][] = $r;
		}
```

## Verification
1. Open the Old Metal Purchase report.
2. Select "All" metals and generate the report.
3. Verify that the table displays a single, continuous list of entries.
4. Check that the entries are sorted strictly by Date (oldest to newest), mixing Gold and Silver rows chronologically.

## Notes
By changing the group key from `$r['metal_type']` to `'ALL'`, the JavaScript frontend seamlessly iterates over the single array block, rendering everything in the exact order returned by the modified SQL query.
