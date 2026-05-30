# Bug: Reorder Report — Duplicate Weight Range Columns

## Symptom
Reorder report displays duplicate weight range columns (e.g., 10GM appears twice, 12GM appears twice).
The data rows (Branch, Section, Product, Design, Sub Design) are unique/correct — only the weight range column headers are duplicated.

## Root Cause
1. `ret_weight` master table has duplicate entries (same `id_product + from_weight + to_weight`) with different `id_weight` values.
2. `get_weight_range_details()` in `ret_reports_model.php` uses `SELECT *` which returns ALL rows — JS creates one column per row → duplicate columns.
3. `check_weight_range()` in `ret_catalog_model.php` had a broken validation that failed to block duplicates:
   - Used overlap logic instead of exact match
   - `weight_description` check was commented out
   - varchar comparison (`'10' != '10.000'`) bypassed the check

## Fix (3 changes)

### 1. Report query — GROUP BY to deduplicate columns
**File:** `admin/application/models/ret_reports_model.php`  
**Function:** `get_weight_range_details($id_product)`

```php
// Before
$sql = $this->db->query("SELECT * FROM ret_weight where id_product=".$id_product);

// After
$sql = $this->db->query("SELECT MIN(id_weight) as id_weight, id_product, from_weight, to_weight, weight_description, value, name, id_uom, id_design, id_sub_design FROM ret_weight where id_product=".$id_product." GROUP BY id_product, from_weight, to_weight, weight_description ORDER BY from_weight");
```

### 2. Duplicate prevention — exact match check (Add + Edit)
**File:** `admin/application/models/ret_catalog_model.php`  
**Function:** `check_weight_range()` — single function for both Add and Edit

```php
function check_weight_range($from_weight,$to_weight,$value,$id_product,$desc,$id_weight='')
{
    $sql=$this->db->query("SELECT w.id_weight FROM ret_weight w
     WHERE w.id_product = ".$id_product."
     AND w.from_weight = ".$from_weight."
     AND w.to_weight = ".$to_weight."
     ".($id_weight!='' ? " AND w.id_weight != ".$id_weight : "")."
     ");
    return $sql->num_rows();
}
```

Key: Check only `id_product + from_weight + to_weight` (decimal comparison handles `9.01 = 9.010`). Do NOT use `weight_description` (varchar — `'10' != '10.000'` bypasses).

### 3. Edit mode duplicate check added
**File:** `admin/application/controllers/admin_ret_catalog.php`  
**Case:** `Update` in `weight()` function

```php
$chkdn = $this->$model->check_weight_range($this->input->post('from_weight'), $this->input->post('to_weight'), $this->input->post('name'), $this->input->post('id_product'), $this->input->post('weight_desc'), $id);

if ($chkdn > 0) {
    $return_data = array('status' => false, 'msg' => 'Weight Range already Existed');
    echo json_encode($return_data);
    exit;
}
```

## Pattern
- When building dynamic columns from master data, always use `GROUP BY` on column-defining fields
- Duplicate checks on decimal fields: compare as numbers, not strings
- Single validation function for Add+Edit with optional `$exclude_id` parameter

## Tags
reorder, report, duplicate-columns, ret_weight, weight-range, GROUP BY, duplicate-prevention
