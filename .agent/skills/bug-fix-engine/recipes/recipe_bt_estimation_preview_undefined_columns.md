# Recipe: Branch Transfer Estimation Preview Shows Undefined Values

## Metadata
- **Pattern ID**: PAT-BT-ESTI-PREVIEW-001
- **Severity**: MEDIUM
- **Modules Affected**: Branch Transfer (`ret_brntransfer_model.php`, `ret_branch_transfer.js`)
- **Auto-fixable**: Yes (SQL field addition + JS column insertion)

## Client Scope
- **Applies to**: ALL
- **Reason**: Branch transfer estimation fetch uses shared model/JS across all retail clients.

## Created By
- **Developer**: Antigravity AI
- **Client**: RTM Source
- **Date**: 2026-06-17
- **Source Bug ID**: N/A

## Symptom
When loading a branch transfer estimation and clicking the **Preview** button, all data columns show `undefined` except the `tag_code` column. The estimation data loads correctly into the search table but the preview table renders broken values.

## Root Cause
A **column index mismatch** between the estimation search results HTML and the preview table handler.

1. **Backend missing fields:** The `fetchEstiTagsByFilter()` model method did not include `old_tag_code` and `section` fields in its SQL SELECT, nor did it JOIN the `ret_section` table. The standard tag fetch method (`fetchTagsByFilter()`) included these fields, creating a schema inconsistency.

2. **Frontend missing columns:** The `getEstiTags()` JavaScript function in `ret_branch_transfer.js` did not generate `<td>` elements for `old_tag_code` and `section` in the search result rows. Since the preview handler (`#add_to_list` click) reads columns using fixed `td:eq(N)` index selectors, the missing columns caused every subsequent column index to shift by 2, producing `undefined` for all fields after `tag_code`.

The preview table expects a **13-column structure**. Estimation search results were only providing 11 columns, causing:
- `td:eq(2)` (expected `old_tag_code`) → read from `lot_no` → wrong value
- `td:eq(3)` (expected `lot_no`) → read from `product` → wrong value
- ... cascading shift for every column after

## Detection
```bash
# Check if fetchEstiTagsByFilter includes section/old_tag_code
grep -n "old_tag_code\|id_section\|ret_section" admin/application/models/ret_brntransfer_model.php

# Check if getEstiTags builds old_tag_code and section columns
grep -n "old_tag_code\|section" admin/assets/js/ret_branch_transfer.js | grep -i "esti"
```

## Files
- `admin/application/models/ret_brntransfer_model.php` — `fetchEstiTagsByFilter()`
- `admin/assets/js/ret_branch_transfer.js` — `getEstiTags()` HTML builder

## Fix

### Fix 1: Add missing fields and JOIN to `fetchEstiTagsByFilter()`

**File:** `admin/application/models/ret_brntransfer_model.php`

Add `old_tag_code` and `section` to the SELECT, and add the `ret_section` LEFT JOIN.

#### Before
```php
t.id_lot_inward_detail,ifnull(tag_dia_detail.stn_wt,0) as tag_dia_wt
FROM ret_estimation est
    LEFT JOIN ret_estimation_items est_itm ON est_itm.esti_id = est.estimation_id
    LEFT JOIN ret_taging t ON t.tag_id = est_itm.tag_id
    ...
    Left join ret_design_master d on d.design_no=t.design_id
WHERE ...
```

#### After
```php
t.id_lot_inward_detail,ifnull(tag_dia_detail.stn_wt,0) as tag_dia_wt,
IFNULL(sect.section_name,'') as section,ifnull(t.old_tag_id,'') as old_tag_code
FROM ret_estimation est
    LEFT JOIN ret_estimation_items est_itm ON est_itm.esti_id = est.estimation_id
    LEFT JOIN ret_taging t ON t.tag_id = est_itm.tag_id
    ...
    Left join ret_design_master d on d.design_no=t.design_id
    LEFT JOIN ret_section sect on sect.id_section = t.id_section
WHERE ...
```

---

### Fix 2: Add missing columns to `getEstiTags()` HTML builder

**File:** `admin/assets/js/ret_branch_transfer.js`

Insert `old_tag_code` and `section` `<td>` elements in the correct column positions (after `tag_code`, before `lot_no`).

#### Before
```javascript
html +=
'<tr>'+
'<td><input type="checkbox" ...></td>'+
'<td>...tag_code...</td>'+
'<td>...lot_no...</td>'+          // Was at index 2, should be at 4
'<td>...product...</td>'+
...
```

#### After
```javascript
html +=
'<tr>'+
'<td><input type="checkbox" ...></td>'+
'<td>...tag_code...</td>'+
'<td>...old_tag_code...</td>'+    // NEW — index 2
'<td>...lot_no...</td>'+          // index 3
'<td>...section...</td>'+         // NEW — index 4
'<td>...product...</td>'+         // index 5
...
```

---

## Verification
1. Navigate to Branch Transfer form.
2. Select a branch transfer estimation with tags.
3. Load estimation data — verify search table populates correctly.
4. Click **Preview** button — verify all 13 columns display correct values (no `undefined`).
5. Confirm `tag_code`, `old_tag_code`, `section`, `lot_no`, `product`, `design`, `datetime`, `pieces`, `gross_wt`, `net_wt`, `dia_wt` all show actual data.

## Notes
- This is a classic **column index coupling** bug. Any time columns are added/removed from a search table, the preview handler's fixed `td:eq(N)` selectors must be updated in sync.
- The `fetchTagsByFilter()` method (for regular tag search) already includes these fields — the estimation variant was simply never updated to match.
- Always compare column count between search result builders and preview/transfer handlers when modifying branch transfer tables.
