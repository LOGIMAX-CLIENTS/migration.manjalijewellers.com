# Recipe: Stock Report — Array in WHERE Clause (id_category Multi-select)

## Metadata
- **Pattern ID**: PAT-RPT-003
- **Severity**: P1
- **Modules Affected**: Reports (stock_details_v1, get_nontag_stock_details_v1)
- **Auto-fixable**: Yes (simple pattern substitution)
- **Source Bug ID**: RPT-SD01
- **GitHub Issue**: [#1804](https://github.com/Logimax-Technologies/etail_development_src/issues/1804)

## Client Scope
- Applies to: ALL clients
- Reason: All clients use the same `get_stock_details_v1()` function in `ret_reports_model.php`

## Created By
- Developer: Antigravity
- Date: 2026-04-03
- Source Bug ID: RPT-SD01

---

## Symptom

User opens **Stock Report v1** → selects a category (e.g., "22KT GOLD ORNAMENTS") → clicks Search.
Report fails with:

```
A Database Error Occurred
Error Number: 1054
Unknown column 'Array' in 'where clause'
```

The generated SQL contains `and p.cat_id=Array` in the WHERE clause.

---

## Root Cause

In `ret_reports_model.php → get_stock_details_v1()`, the `id_category` POST field is posted as an **array** (from Select2 multi-select on the view). The model used `$data['id_category']` directly in the SQL WHERE clause:

```php
// BUGGY — array interpolated directly into SQL string:
".($data['id_category']!='' && $data['id_category']!=0 ? " and p.cat_id=".$data['id_category']."" :'')."
```

When PHP interpolates an array into a string it produces `Array` → MySQL error 1054.

**Why this happens**: `id_metal`, `purity`, and `id_branch` were all correctly handled with `implode()` at the top of the function — but `id_category` was missed during the multi-select upgrade.

---

## Detection

```powershell
# Find functions that still use $data['id_category'] directly in SQL without implode
grep -n "id_category.*!=" admin/application/models/ret_reports_model.php | grep "cat_id="
```

---

## Files
- `admin/application/models/ret_reports_model.php`
  - Function: `get_stock_details_v1()`

---

## Fix

### Step 1 — Add implode block for `$id_category` (after `$id_branch` block, ~line 16708)

**Before** (add AFTER the `$id_branch` block which ends at ~L16708):
```php
        if($this->get_ret_settings('appr_stock_incl_in_reports') == 1)
```

**After**:
```php
        $multiple_id_category = implode(' , ', (is_array($data['id_category']) ? $data['id_category'] : [$data['id_category']]));
		if($multiple_id_category != '')
		{
			$id_category = $multiple_id_category;
		}else{
			$id_category = $data['id_category'];
		}
        if($this->get_ret_settings('appr_stock_incl_in_reports') == 1)
```

### Step 2 — Fix WHERE clause in `if(!empty($data['id_branch']))` branch (~line 17005)

**Before**:
```php
".($data['id_category']!='' && $data['id_category']!=0 ? " and p.cat_id=".$data['id_category']."" :'')."
```

**After**:
```php
".($id_category!='' && $id_category!=0 ? " and p.cat_id in (".$id_category.")" :'')."
```

### Step 3 — Fix WHERE clause in `else` branch (~line 17228)

**Before**:
```php
".($data['id_category']!='' && $data['id_category']!=0 ? " and p.cat_id=".$data['id_category']."" :'')."
```

**After**:
```php
".($id_category!='' && $id_category!=0 ? " and p.cat_id in (".$id_category.")" :'')."
```

---

## Verification

1. Run: `php -l admin/application/models/ret_reports_model.php` → must return "No syntax errors detected"
2. Open: `http://{host}/admin/index.php/admin_ret_reports/stock_details_v1/list`
3. Select Branch, Date range, Metal, Category → Click Search
4. Report must load without DB error
5. Verify rows are filtered to the selected category only

---

## Notes

- This pattern (array passed to SQL scalar filter) is common when Select2 multi-selects are introduced on report forms
- Look for similar issues in: `get_nontag_stock_details_v1()`, other report model functions using `$data['id_category']` directly
- Pattern ID: PAT-RPT-003 — "Multi-select array not imploded in model SQL filter"
- Related patterns: PAT-QRY-001 (Raw POST array in SQL), PAT-SEC-001 (SQL injection via direct POST concat)
