# BUG RPT-STK01: Stock In & Out Report — Product Filter DB Error

| Field | Value |
|---|---|
| **Bug ID** | RPT-STK01 |
| **Module** | Reports (RPT) |
| **Severity** | P1 |
| **Category** | Query / Filter |
| **Track** | A (System) |
| **Status** | ✅ Fixed |
| **Date Found** | 2026-04-04 |
| **Date Fixed** | 2026-04-04 |
| **GitHub Issue** | [#1813](https://github.com/Logimax-Technologies/etail_development_src/issues/1813) |

## Symptom

Selecting a product from the "Select Product" multi-select dropdown in Stock In & Out report (`admin_ret_reports/stock_details_v1/list`) causes:

```
A Database Error Occurred
Error Number: 1054
Unknown column 'Array' in 'where clause'
```

Report loads continuously and never returns data.

## Root Cause

The Product filter dropdown sends `id_product` as a **PHP array** (multi-select `id_product[]`), but 6 model functions used `$data['id_product']` directly with the `=` operator in SQL WHERE clauses. PHP's implicit array-to-string conversion produces the literal string `"Array"`, generating invalid SQL like:

```sql
and tag.product_id=Array
```

Other multi-select filters (`id_metal`, `purity`, `id_branch`) in the same functions already had proper `implode()` + `IN()` handling — `id_product` was simply missed.

## Fix Applied

### Pattern used (same as existing `id_metal`/`id_branch` handling):
```php
// Added in each function:
$multiple_id_product = implode(' , ', $data['id_product']);
if($multiple_id_product != '') {
    $id_product = $multiple_id_product;
} else {
    $id_product = $data['id_product'];
}

// Changed WHERE clause from:
".($data['id_product']!='' ? " and t.product_id=".$data['id_product']."" :'')."

// To:
".($id_product!='' ? " and t.product_id in(".$id_product.")" :'')."
```

### Affected Functions (ret_reports_model.php) — 10 changes across 6 functions:

| # | Function | Changes |
|---|---|---|
| 1 | `get_stock_details_v1()` | Added implode block + 2× WHERE clause fixes (branch-present + branch-empty queries) |
| 2 | `get_nontag_stock_details_v1()` | Added implode block + 1× WHERE clause fix |
| 3 | `branch_outward()` | Added implode block + 2× WHERE clause fixes (br_out + sales_ret_trans queries) |
| 4 | `other_ow()` | Added implode block + 2× WHERE clause fixes (partly_sale + deleted_tag queries) |
| 5 | `showroom_sales()` | Added implode block + 2× WHERE clause fixes (showroomsales + sales_trans queries) |
| 6 | `issued_stock()` | Added implode block + 1× WHERE clause fix |

## Files Changed

- `admin/application/models/ret_reports_model.php`

## Verification

- ✅ PHP syntax check — no errors
- ✅ User confirmed fix resolves the error

## Rollback

Revert the 10 changes in `ret_reports_model.php` — restore `$data['id_product']` with `=` operator and remove the `implode` blocks.
