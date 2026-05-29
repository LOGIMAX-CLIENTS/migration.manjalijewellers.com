# Branch Transfer — Deep Analysis Round 12: Remaining Views

> **Date**: 2026-03-11
> **Focus**: approval_list.php (986 lines), print.php (1813 lines), bt_category_print.php
> **View coverage**: 100% of ALL views read

---

## Bugs Found: 5

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-D32 | **P1** | Triplicate `id="appr_sel_all_nt"` — checkbox "select all" applies to wrong table | approval L624, L682, L734 | A |
| BRN-D33 | **P1** | `$tot_amount`/`$tot_dia_wt` uninitialized — wrong print totals for purchase items type=1 | print L568, L1217, L1227 | A |
| BRN-D34 | **P2** | `function group_by()` defined inline in view — fatal if another view declares same | print L174 | A |
| BRN-D35 | **P2** | Repair Orders type5 no permission check in approval_list (confirms BRN-D29) | approval L261 | B |
| BRN-D36 | **P2** | Broken HTML `title` attribute in "Get Approval Status" button | approval L979 | A |

---

### BRN-D32 — Triplicate `id="appr_sel_all_nt"` [P1]
```html
<!-- approval_list.php L624 (non-tagged table): -->
<input type="checkbox" id="appr_sel_all_nt" name="appr_sel_all_nt" value="all"/>

<!-- approval_list.php L682 (old metal table): -->
<input type="checkbox" id="appr_sel_all_nt" name="appr_sel_all_nt" value="all"/>

<!-- approval_list.php L734 (packaging table): -->
<input type="checkbox" id="appr_sel_all_nt" name="appr_sel_all_nt" value="all"/>
```
**Root Cause**: Three different tables (non-tagged, old metal, packaging) all use the same `id="appr_sel_all_nt"` for their "select all" checkboxes. HTML IDs must be unique.
**Impact**: `$('#appr_sel_all_nt')` in JS will only match the FIRST one (non-tagged). Old metal and packaging "Select All" checkboxes **do not work** — clicking them does nothing because the JS event handler binds to the first ID match only.

### BRN-D33 — Uninitialized Variables in Print View [P1]
```php
// print.php (type=2, purchase items → old metal section):
// L568: $tot_amount += $items['amount'];  ← $tot_amount never initialized!
// L570: $tot_dia_wt += $items['dia_wt'];  ← $tot_dia_wt never initialized!
//
// Result: PHP "Undefined variable" notice, and running total may be wrong
// (PHP initializes undefined to 0, but behavior depends on error_reporting)

// Same issue repeats in type=1 purchase items section:
// L1217: $tot_dia_wt += $items['dia_wt'];
// L1227: $tot_amount += $items['amount'];
```
**Root Cause**: In the old metal sections for both `$type==2` (L544-570) and `$type==1` (L1201-1228), variables `$tot_amount` and `$tot_dia_wt` are accumulated without being initialized to 0. While `$tot_gross_wt`, `$tot_net_wt`, `$tot_pcs` etc. are properly initialized at L165-172 for tagged items, the purchase items section reuses them without re-initializing the type-3-specific variables.
**Impact**: In strict mode or with warnings enabled, this generates PHP notices in the PDF output. If the old metal section is the only section (no tagged items ran first to implicitly init), totals could be wrong.

### BRN-D34 — Inline Function in View [P2]
```php
// print.php L174-195:
function group_by($key, $data)
{
    $result = array();
    foreach ($data as $val) {
        if (array_key_exists($key, $val)) {
            $result[$val[$key]][] = $val;
        } else {
            $result[""][] = $val;
        }
    }
    return $result;
}
```
**Root Cause**: A global PHP function is defined inside a view file. If this view is loaded twice in the same request (e.g., in a batch print), or if another view also defines `function group_by()`, PHP throws a **fatal error: Cannot redeclare group_by()**.
**Impact**: Batch printing or any code that includes multiple print views in one request will crash.
**Fix**: Move to a helper or use `if (!function_exists('group_by'))` guard.

### BRN-D35 — Type5 No Permission Check (Duplicate of BRN-D29) [P2]
```php
// approval_list.php L261 — same as form.php L93:
<input type="radio" name="transfer_item_type" id="type5" value="5">
// No permission check unlike types 1-4
```
Confirms BRN-D29 is systemic across both form.php and approval_list.php.

### BRN-D36 — Broken HTML Title Attribute [P2]
```html
<!-- approval_list.php L979: -->
<button type="button" class="btn btn-success btn-flat get_approval" title=">Get Mobile App Approval">Get Approval Status</button>
```
**Root Cause**: Title attribute has a stray `>` at the beginning: `title=">Get Mobile App Approval"`. This results in the title value being `>Get Mobile App Approval` (with a leading `>`).
**Impact**: Cosmetic — tooltip shows wrong text with a leading angle bracket.
