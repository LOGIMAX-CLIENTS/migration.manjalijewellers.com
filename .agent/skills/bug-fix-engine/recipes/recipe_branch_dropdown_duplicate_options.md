# Recipe: Branch Dropdown Duplicate Options

> `.ret_branch` class select dropdowns show each branch twice due to incomplete option clearing in `getBranchName()`.

## Metadata
- **Pattern ID**: PAT-JS-DROPDOWN-DUP-001
- **Severity**: MEDIUM
- **Modules Affected**: Lot Inward, Lot Merge, and any module using `.ret_branch` class select elements
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: The `getBranchName()` function in `ret_general.js` is shared across all clients. Any client where both `general.js` and `ret_general.js` are loaded (all retail modules) is at risk if JS file versions are out of sync.

## Created By
- **Developer**: Antigravity AI
- **Client**: Raja Thangamaligai (jrretail) — live server only
- **Date**: 2026-06-16
- **Source Bug ID**: N/A

## Symptom
On the Lot Inward Add form (`admin_ret_lot/lot_inward/add`), the "Lot Received At" branch dropdown (`#lt_rcvd_branch_sel`) shows each branch name twice. For example, instead of 3 branches (PALANI, HEAD OFFICE, MADATHUKULAM), the dropdown displays 6 entries — each branch repeated.

The DOM shows:
```html
<select id="lt_rcvd_branch_sel" class="ret_branch form-control">
    <option value="1">PALANI</option>
    <option value="2">HEAD OFFICE</option>
    <option value="4">MADATHUKULAM</option>
    <option></option>                         <!-- select2 placeholder -->
    <option value="1">PALANI</option>         <!-- DUPLICATE -->
    <option value="2">HEAD OFFICE</option>    <!-- DUPLICATE -->
    <option value="4">MADATHUKULAM</option>   <!-- DUPLICATE -->
</select>
```

## Root Cause
Two separate JS files both populate the `.ret_branch` dropdown:

1. **`general.js`** → `get_branchname()` — guarded by `!window.location.href.includes("admin_ret")` to skip retail pages
2. **`ret_general.js`** → `getBranchName()` — guarded by `window.location.href.includes("admin_ret")` to run only on retail pages

These guards make them **mutually exclusive**, so normally only one runs. However:

- On the **live server**, `general.js` may be an older cached/deployed version **missing the `admin_ret` guard**, causing both functions to run
- `getBranchName()` in `ret_general.js` only clears `#branch_select option` before appending, but **never clears `.ret_branch option`**
- So when `general.js` runs first and appends to `.ret_branch`, then `ret_general.js` runs and appends again without clearing → duplicates

**Why local/staging worked**: Their `general.js` has the guard, so `get_branchname()` never runs on `admin_ret_lot` pages.

### Root Cause Category
- **Primary**: Incomplete DOM cleanup before population — clearing `#branch_select` but not `.ret_branch`
- **Secondary**: Deployment version mismatch between `general.js` and `ret_general.js` on live server
- **Pattern**: Any `$.each(...).append()` loop that doesn't first clear the target element is vulnerable to duplicates if called more than once

## Detection
```command
grep -n 'branch_select option.*remove\|\.ret_branch option.*remove' admin/assets/js/ret_general.js
```

**Vulnerable state**: If you see `#branch_select option` being removed but NOT `.ret_branch option`:
```javascript
// VULNERABLE — only clears #branch_select, not .ret_branch
$("#branch_select option").remove();
```

**Fixed state**: Both selectors are cleared:
```javascript
// FIXED — clears both
$("#branch_select option,.ret_branch option").remove();
```

## Files
- `admin/assets/js/ret_general.js` — `getBranchName()` function (~line 2769)

## Fix

### Before
```javascript
		success:function(data){

		   $("#branch_select option").remove();
```

### After
```javascript
		success:function(data){

		   $("#branch_select option,.ret_branch option").remove();
```

### Explanation
By adding `.ret_branch option` to the removal selector, `getBranchName()` becomes **idempotent** — it clears all `.ret_branch` options before re-populating, regardless of whether another function already appended options. This is a **defensive fix** that works even if `general.js` also runs.

## Verification
1. Navigate to `admin_ret_lot/lot_inward/add` (Lot Inward → Add)
2. Click the "Lot Received At" dropdown
3. **Expected**: Each branch appears exactly once
4. **Fail**: Any branch appears more than once
5. Also verify on `admin_ret_lot/lot_merge` (Lot Merge page) — same `.ret_branch` class is used
6. Verify that branch selection still correctly sets `#id_branch` hidden field via the change handler

## Deployment Note
After deploying this fix to `ret_general.js`, also deploy the latest `general.js` to ensure the `!window.location.href.includes("admin_ret")` guard is present. This eliminates the root cause (double execution) in addition to the defensive fix.

**Cache-busting**: The footer loads JS files with `?v=` version param. Increment the version in `config.php` or advise client to hard-refresh (`Ctrl+Shift+R`) after deployment.

## Notes
- This is a **JS-layer-only** fix. No PHP controller/model changes needed.
- The same pattern could affect other class-based select elements (`.branch_filter`, `#sync_branch`, `#ed_branch_select`) if they also lack clearing — but currently only `.ret_branch` is affected because the others are either cleared or not present on duplicate-prone pages.
- **Related pattern**: Any dropdown populated via `$.each().append()` inside an AJAX callback should always clear existing options first. Search for other instances with: `grep -n '\.append.*option.*value' admin/assets/js/ret_general.js`
