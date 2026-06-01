# Recipe - Duplicate Tag Printing SQL Join Syntax and Lifecycle Status

This recipe fixes the SQL parser syntax error caused by a malformed JOIN company statement (lacking an ON clause) and expands the restrictive tag status filter in the duplicate tag printing module.

## Metadata
- **Pattern ID**: PAT-SQL-JOIN-001
- **Severity**: HIGH
- **Modules Affected**: Tagging
- **Auto-fixable**: Yes (via string replacement in ret_tag_model.php)

## Client Scope
- **Applies to**: ALL
- **Reason**: Standard module query structure for duplicate printing contains legacy loose-join syntax that fails under strict SQL modes, and overly restrictive `tag_status=0` filters that prevent reprinting tags with other active statuses (e.g. Sold, In-Transit, etc.).

## Created By
- **Developer**: Antigravity
- **Client**: Retail ERP Development
- **Date**: 2026-05-28
- **Source Bug ID**: TAG-CLT04

## Symptom
The duplicate tag printing page (`admin/index.php/admin_ret_tagging/tagging/duplicate_print`) displays an empty/no-data table in the UI even when searching with valid, existing lot IDs. SQL query parser exceptions are raised in strict SQL modes.

## Root Cause
1. The `JOIN company c` clause in `ret_tag_model::get_duplicate_tag` is written as a loose standard join without an `ON` condition. Strict SQL mode parsers throw errors on this syntax.
2. The query includes a strict filter `tag.tag_status = 0` (Available). Tags that have transitioned to other lifecycle states such as Sold (`1`), In-Transit (`4`), or Partially Sold (`6`) are omitted from duplicate printing capability.

## Detection
Run the following search query to detect the malformed loose join and status filter:
```command
grep -rn -A 10 "join company c" admin/application/models/ret_tag_model.php
```

## Files
- `admin/application/models/ret_tag_model.php`

## Fix

### Before
```php
				from ret_taging as tag



				join company c



					LEFT JOIN ret_lot_inwards as lot ON lot.lot_no = tag.tag_lot_id



					LEFT JOIN ret_tag_type_master as tag_type ON tag_type.tag_id = tag.tag_type



					LEFT JOIN ret_product_master p on p.pro_id=tag.product_id



					LEFT JOIN ret_design_master d on d.design_no=tag.design_id



					LEFT JOIN ret_sub_design_master s on s.id_sub_design=tag.id_sub_design



					LEFT JOIN ret_karigar k on k.id_karigar=lot.gold_smith



				where tag.tag_id is not null and tag.tag_status=0
```

### After
```php
				from ret_taging as tag



				JOIN company c ON 1=1



					LEFT JOIN ret_lot_inwards as lot ON lot.lot_no = tag.tag_lot_id



					LEFT JOIN ret_tag_type_master as tag_type ON tag_type.tag_id = tag.tag_type



					LEFT JOIN ret_product_master p on p.pro_id=tag.product_id



					LEFT JOIN ret_design_master d on d.design_no=tag.design_id



					LEFT JOIN ret_sub_design_master s on s.id_sub_design=tag.id_sub_design



					LEFT JOIN ret_karigar k on k.id_karigar=lot.gold_smith



				where tag.tag_id is not null and tag.tag_status NOT IN (3, 5, 17)
```

## Verification
1. Run PHP lint to verify no compile-time regressions:
   `php -l admin/application/models/ret_tag_model.php`
2. Search for a lot that actually has active product tags (e.g. `9528`) using duplicate tag print search.
3. Verify that the table correctly populates with all tags associated with the lot, regardless of whether their status is Available (`0`) or Partially Sold (`6`), while correctly excluding Deleted (`5`) or Retagged (`3`) tags.

## Notes
- `tag_status` mappings are defined in the System Brain:
  * `0`: Available / In Stock
  * `1`: Sold Out
  * `3`: Retagged (old tag)
  * `4`: In Transit
  * `5`: Deleted
  * `6`: Partially Sold (Active Stock)
  * `7`: Issued (Exhibition)
  * `8`: Reserved (Order)
  * `17`: Melted / Metal Processed
- By checking `tag_status NOT IN (3, 5, 17)`, all active/sold/transit lifecycle states can be reprinted under duplicate print while excluding destroyed/replaced/deleted tags.
