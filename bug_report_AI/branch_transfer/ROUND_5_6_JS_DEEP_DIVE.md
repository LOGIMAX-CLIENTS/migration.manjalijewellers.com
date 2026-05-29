# Branch Transfer — Rounds 5 & 6: JS Calculations, Validation & AJAX

> **Date**: 2026-03-11
> **File**: `admin/assets/js/ret_branch_transfer.js` (8,879 lines, 51 functions)

---

## Bugs Found: 4

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-R501 | **P2** | Missing parseFloat/NaN Guards in Calculations | Various | B |
| BRN-R502 | **P2** | No `error:` Handler on Several AJAX Calls | Various | A |
| BRN-R601 | **P2** | Checkbox Enable After DataTable Re-init | L4505 area | B |
| BRN-R602 | **P3** | `brnTransUpdStatus` Reloads Without Checking Response | L4855 | A |

### BRN-R501 — Missing parseFloat/NaN Guards [P2]
Calculation functions `calcTaggedApprList()` L593, `calcNTaggedApprList()` L667, `calculateNTtotal()` L1343, and `calculateOrdertotal()` L7579 read hidden input values and sum them. While some use `parseFloat()`, there's no `isNaN()` fallback:
```javascript
// Example from calcTaggedApprList:
total_pcs += parseFloat($(this).find('.t_pieces').val());
// If .t_pieces is empty → parseFloat('') = NaN → NaN propagates through all sums
```
**Fix**: Use `parseFloat(val) || 0` pattern.
**Pattern**: Related to PAT-VAR-001

### BRN-R502 — Missing AJAX Error Handlers [P2]
Several AJAX calls lack `error:` callbacks:
- `brnTransUpdStatus()` L4839 — the approval/download workhorse
- `get_brantranNonTagged()` L4627
- `get_brantranTagged()` L4217
- `get_brantranOldMetal()` L6902
- `get_brantranPackagingItems()` L7275
- `get_branchtranOrderDetails()` L7641

**Impact**: If AJAX fails (network issue, 500 error), the overlay spinner stays visible forever, and the user sees a frozen page.

### BRN-R601 — Checkbox Enable After DataTable Re-init [P2]
In approval list DataTables, checkboxes are rendered with `disabled` attribute. After DataTable re-initialization via `bDestroy: true`, previously enabled checkboxes lose their state.
```javascript
// Checkbox rendered disabled by default (L4733):
return '<input type="checkbox" ... disabled>'
// JS later enables them, but on DataTable redraw, state is lost
```
**Impact**: Users need to re-select checkboxes after any grid refresh operation.

### BRN-R602 — `brnTransUpdStatus` Reloads Without Checking Response [P3]
```javascript
// L4849-4856:
success: function(data){
    $('#upd_status_btn').prop('disabled',false);
    $(".alert-msg").html('');
    window.location.reload();  // ← Always reloads, even if server returned error
    $(".overlay").css("display", "none");
}
```
**Root Cause**: `data` is never checked for success/failure status. Even if the server returns `{status: false, msg: 'Error'}`, the page reloads, hiding the error from the user.
