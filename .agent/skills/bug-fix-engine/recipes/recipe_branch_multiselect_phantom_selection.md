# Recipe: Branch Multi-Select Phantom Selection, Defaulting & Placeholder Regression

## Metadata
- **Pattern ID**: PAT-JS-SELECT2-001
- **Severity**: MEDIUM
- **Modules Affected**: Assets / JS (`general.js`, `ret_general.js`) affecting report pages, estimation add form, and master settings page filters
- **Auto-fixable**: Yes (Regex/String replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: Shared frontend JavaScript assets (`general.js`, `ret_general.js`) are used across all retail client codebases.

## Created By
- **Developer**: Antigravity AI
- **Client**: RTM Source / Shared Assets
- **Date**: 2026-06-11 (Updated: 2026-06-17)
- **Source Bug ID**: N/A
- **Related Commit**: `7f9d9cf` ("Use loggedInBranch fallback for branch selection")

## Symptom
1. On report pages and master settings pages (e.g. `wastage_mc_settings/add`, `stock_details/list`, `netbanking_collection_report/list`), the branch select dropdown shows **two phantom `×` empty selection chips** upon initial page load for admin/all-branch logins.
2. For branch-restricted users, the branch dropdown does not auto-select their assigned branch by default on Page Load.
3. **(Regression from Fix 1)** On Estimation Add form (and other pages with `#branch_select`), the branch dropdown **auto-selects the first branch** (e.g., "Palani") instead of showing "Select Branch" placeholder. Attempting to save shows "Please select branch" validation error because the `#id_branch` hidden field was never populated by a change event.

## Root Cause
1. **Misused `select2()` API Call:** In `ret_general.js`, a call to `select2()` was passed a jQuery `<option>` element as an argument instead of a settings object. This incorrectly injected empty-value option tags into all matching branch selects.
2. **Auto-selecting Empty/Undefined Values:** Calling `.select2("val", '')` or `.select2("val", undefined)` on a `<select multiple>` element instructs Select2 (v3.x) to select all matching empty-value options, producing the phantom empty selection chips.
3. **Missing Default Value Fallback:** If `id_branch` is empty/undefined (which happens on "add" pages), the system did not fall back to `loggedInBranch` (the user's home branch), forcing single-branch users to select their branch manually.
4. **Dual JS File Execution:** Both `general.js` and `ret_general.js` executed branch select initialization functions (`get_branchname()` and `getBranchName()`) concurrently on retail pages, creating duplicate DOM options and double-initializing the Select2 widgets.
5. **(Regression)** **Missing Empty Placeholder Option:** Fix 1 removed the broken `select2($('<option>'))` call, but that call — despite being syntactically wrong — was the only thing injecting an empty-value `<option>` into the dropdown. Select2 v3.x requires an empty-value `<option>` as the first element to display its `placeholder` text. Without it, Select2 defaults to showing the first real option (e.g., Palani at `id_branch=1`) as visually selected, even though no `change` event fires, leaving `#id_branch` empty.

## Detection
Scan JavaScript assets for:
1. `select2()` call passing a jQuery element (like `$("<option>")`) instead of config.
2. Unconditional `.select2("val", ...)` calls without checking if the value is defined and greater than 0.
3. Overlapping execution conditions for `get_branchname()` and `getBranchName()`.

```bash
# Detect incorrect select2 jQuery element initialization
grep -rn "\.select2(\s*\$(\s*\"<option" admin/assets/js/

# Detect unconditional select2 val setters
grep -rn "\.select2(\"val\",(.*id_branch" admin/assets/js/
```

## Files
- `admin/assets/js/general.js`
- `admin/assets/js/ret_general.js`

## Fix

### Fix 1: Remove Broken `select2()` call in `ret_general.js`
Remove the broken initialization block that incorrectly injects empty options.

**File:** `admin/assets/js/ret_general.js`

#### Before
```javascript
		   $("#branch_select,#sync_branch,.ret_branch,.branch_filter").select2(

			$("<option></option>")

				   .attr("value", "")

				   .text('Select Branch' )

				   );
```

#### After
```javascript
		   // Placeholder is handled by select2 config below (line ~2849)
```

---

### Fix 2: Add Fallback to Home Branch and Target All Selector Elements in `ret_general.js`
Fallback `id_branch` to the session's `loggedInBranch` if it is empty, and apply pre-selection to all branch select elements.

**File:** `admin/assets/js/ret_general.js`

#### Before
```javascript
		   var id_branch =  $('#id_branch').val();

			
// ...
		if($(".ret_branch").length){

			   $(".ret_branch").select2("val",(id_branch!='' && id_branch>0?id_branch:''));

		   }
```

#### After
```javascript
		   var id_branch =  $('#id_branch').val();
		   if ((id_branch == undefined || id_branch == '' || id_branch == 0) && typeof loggedInBranch !== 'undefined' && loggedInBranch > 0) {
			   id_branch = loggedInBranch;
		   }
// ...
		if(id_branch != undefined && id_branch != '' && id_branch > 0){
			$("#branch_select,#sync_branch,.ret_branch,.branch_filter,#ed_branch_select").select2("val", id_branch);
		}
```

---

### Fix 3: Exclude `admin_ret_*` pages from `general.js` Branch Init
Prevent `get_branchname()` in `general.js` from executing when `getBranchName()` in `ret_general.js` is already active.

**File:** `admin/assets/js/general.js`

#### Before
```javascript
 if($('#branch_set').val()==1  && ctrl_page[0] != 'settings' && ctrl_page[1] != 'payment_employee_wise' && ctrl_page[1] != 'payment_daterange' && ctrl_page[1] != 'Employee_account' && ctrl_page[1] != 'payment_datewise_schemedata' && ctrl_page[1] != 'branch_transfer' && ctrl_page[1] != 'wastage_mc_settings'){
 	 	get_branchname();
 	}
```

#### After
```javascript
 if($('#branch_set').val()==1  && ctrl_page[0] != 'settings' && ctrl_page[1] != 'payment_employee_wise' && ctrl_page[1] != 'payment_daterange' && ctrl_page[1] != 'Employee_account' && ctrl_page[1] != 'payment_datewise_schemedata' && ctrl_page[1] != 'branch_transfer' && ctrl_page[1] != 'wastage_mc_settings' && !window.location.href.includes("admin_ret")){
 	 	get_branchname();
 	}
```

---

### Fix 4: Add Fallback and Pre-select in `general.js`
Apply the same home-branch fallback and selector targeting in the core `general.js`.

**File:** `admin/assets/js/general.js`

#### Before
```javascript
					var id_branch =  $('#id_branch').val();	
// ...
						if(id_branch != undefined && id_branch != '' && id_branch > 0){
						$("#branch_select,.ret_branch").select2("val", id_branch);
					}
```

#### After
```javascript
					var id_branch =  $('#id_branch').val();	
					if ((id_branch == undefined || id_branch == '' || id_branch == 0) && typeof loggedInBranch !== 'undefined' && loggedInBranch > 0) {
						id_branch = loggedInBranch;
					}
// ...
						if(id_branch != undefined && id_branch != '' && id_branch > 0){
						$("#branch_select,#sync_branch,.ret_branch,.branch_filter,#ed_branch_select").select2("val", id_branch);
					}
```

---

### Fix 5: Re-add Empty Placeholder Option for Select2 (Regression Fix)
After clearing options, prepend an empty `<option value="">` so Select2 can display the "Select Branch" placeholder instead of defaulting to the first branch.

**File:** `admin/assets/js/ret_general.js`

#### Before (after Fix 1 was applied)
```javascript
		   $("#branch_select option,.ret_branch option").remove();



		   var id_branch =  $('#id_branch').val();
```

#### After
```javascript
		   $("#branch_select option,.ret_branch option").remove();

		   $("#branch_select,#sync_branch,.ret_branch,.branch_filter,#ed_branch_select").prepend(
			   $('<option></option>').attr('value', '').text('')
		   );

		   var id_branch =  $('#id_branch').val();
```

**File:** `admin/assets/js/general.js`

#### Before
```javascript
					$("#branch_select option").remove(); // New code 05-12-2022

					var id_branch =  $('#id_branch').val();
```

#### After
```javascript
					$("#branch_select option").remove(); // New code 05-12-2022

					$("#branch_select,#sync_branch,.ret_branch,.branch_filter,#ed_branch_select").prepend(
						$('<option></option>').attr('value', '').text('')
					);

					var id_branch =  $('#id_branch').val();
```

---

## Verification
1. Log in with all-branch access credentials (`id_branch = 0`).
2. Load any affected page (e.g. `wastage_mc_settings/add` or `netbanking_collection_report/list`).
3. Verify the branch select field is empty and shows only the placeholder text (`Select Branch`).
4. Log in with single-branch credentials (`id_branch > 0`).
5. Verify the branch select field correctly pre-selects the user's logged-in branch.
6. Open Estimation Add form with all-branch login.
7. Verify branch dropdown shows **"Select Branch"** placeholder — NOT auto-selecting any branch.
8. Select a branch, then proceed with estimation — verify no "Please select branch" error.

## Notes
- In Select2 v3.x, passing `""` (empty string) to a multi-select control selects empty option items instead of clearing/ignoring them. To clear selection programmatically in multi-select, use `.select2("val", null)` or `.select2("val", [])`.
- Select2 v3.x `placeholder` config **requires an empty-value first `<option>`** in the DOM. Without it, Select2 shows the first real option as the default display, even though no selection event fires. This is why the dropdown "looks" selected but the hidden `#id_branch` field stays empty.
- When removing broken code that incidentally provided a required DOM element, always verify that the element's structural role is preserved by other means.
