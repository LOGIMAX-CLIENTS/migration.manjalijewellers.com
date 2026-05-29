# Branch Transfer — Deep Analysis Round 13: JavaScript Line-by-Line

> **Date**: 2026-03-11
> **Focus**: JS file `ret_branch_transfer.js` — sections L200-4200
> **Layer**: JavaScript (approval handlers, data binding, AJAX submission, tag search)
> **Previous JS coverage**: Pattern scan only (R5/R6). This is the first deep line-by-line reading.

---

## Bugs Found: 10

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-D37 | **P0** | Hardcoded `transfer_to: 1` in old metal transfers — always sends to branch 1 | L4039 | A |
| BRN-D38 | **P1** | Duplicate tag check uses `data[0].tag_id` instead of `val.tag_id` — only first tag ever checked | L3365 | A |
| BRN-D39 | **P1** | `$(document).on('keypress',...)` bound inside radio change handler — event accumulates | L2585, L2647 | A |
| BRN-D40 | **P1** | Select-all checkbox (`#appr_sel_all_tg`, `#appr_sel_all_nt`, `#nt_select_all`) applied to ALL `tbody`, not scoped table | L549-551, L647-649, L1243-1245 | A |
| BRN-D41 | **P1** | `console.log("trans_data")` in production `add_to_trans()` — exposes full transfer payload to browser console | L3981 | A |
| BRN-D42 | **P2** | Multiple `console.log()` debug statements across JS file | L903, L2289, L2899 | A |
| BRN-D43 | **P2** | Stray backtick (`` ` ``) syntax error in `btran_filter` click handler | L2221 | A |
| BRN-D44 | **P2** | `grs_wt`, `net_wt`, `pieces` used without `var` in `add_to_trans()` — global scope leak | L4007-4011 | A |
| BRN-D45 | **P2** | `trans_type`, `approval_type`, `from_brn`, `to_brn` undeclared globals throughout approval handlers | L705, L865, L2119, L2225 | A |
| BRN-D46 | **P2** | `dia_wt` used without declaration in `.tag_id` change handler | L1487 | A |

---

### BRN-D37 — Hardcoded `transfer_to: 1` for Old Metal [P0] 🔴

```javascript
// ret_branch_transfer.js L4039:
else if(trans_type == 3)  // Old Metal
{
    grs_wt = $(".old_prev_grs_wt").val();
    net_wt = $(".old_prev_net_wt").val();
    postData = {
        'trans_data': trans_data,
        'transfer_from' : from_brn,
        'transfer_to' : 1,           // ← HARDCODED TO BRANCH ID 1!
        'item_tag_type' : trans_type,
        ...
    };
}
```
**Root Cause**: `transfer_to` is hardcoded to `1` for old metal transfers. The selected `to_brn` variable is completely ignored.
**Impact**: **Every single old metal branch transfer is sent to branch ID 1** — regardless of what the user selects in the "To Branch" field. This is a data corruption bug: wrong branch receives old metal. If branch 1 doesn't exist or isn't the head office, old metal stock is silently misrouted.
**Fix**: Replace `1` with `to_brn` variable (or `$('#head_office_branch').val()` if headoffice is the intended mandatory destination — but this should be a named constant, not `1`).

---

### BRN-D38 — Duplicate Tag Check Wrong Variable [P1]

```javascript
// ret_branch_transfer.js L3349-3370 (getTagSearchList):
$.each(data, function (key, val) {
    $('#bt_search_list > tbody tr').each(function(bidx, brow){
        bt_tagid = $(this);
        if(bt_tagid.find('.tag_code').val() != '') {
            if( data[0].tag_id == bt_tagid.find('.tag_id').val()){  // ← BUG: data[0] not val
                rowExist = true;
                $.toaster({...message: 'Tag Already Exists..'});
            }
        }
    });
```
**Root Cause**: The outer `$.each` iterates over `data` with current item as `val`, but the duplicate check compares `data[0].tag_id` (always the FIRST search result) against existing rows. Only the first tag from search results ever gets duplicate-checked. If the user scans tag codes 2,3,4 in a search that returns multiple results, tags 2,3,4 are **never** checked for duplicates and will be added even if already in the transfer list.
**Fix**: Change `data[0].tag_id` → `val.tag_id`.

---

### BRN-D39 — Keypress Event Listener Accumulation [P1]

```javascript
// ret_branch_transfer.js L2571-2613 (inside radio change for value==3):
else if(this.value == 3) {
    if(ctrl_page[2]=='approval_list') {
        if($('#allow_approval_type').val()==3) {
            $(document).on('keypress', function(e) {  // ← BOUND INSIDE RADIO CHANGE!
                if(e.keyCode == 10) {
                    // toggle is_eda_appr
                }
            });
        }
    }
}
// Same pattern at L2637-2677 for ctrl_page[2]=='add'
```
**Root Cause**: `$(document).on('keypress', ...)` is bound inside the `transfer_item_type` radio change handler for type=3. Every time the user selects "Old Metal" radio, a new `keypress` listener is added to the document. After switching between tabs several times, pressing Ctrl+Enter fires the EDA toggle handler N times (once per switch to old metal).
**Impact**: EDA (No2) toggling becomes erratic — single Ctrl+Enter press may toggle EDA state multiple times, leading to wrong transfer mode being saved.
**Fix**: Use `.off('keypress.oldmetal').on('keypress.oldmetal', ...)` with a namespaced event, or bind once outside the radio handler.

---

### BRN-D40 — Select-All Checkbox Affects ALL Tables [P1]

```javascript
// L549-551 (for #appr_sel_all_tg — tagged table):
$("#appr_sel_all_tg").click(function(event) {
    $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
    // ↑ Selects ALL checkboxes in ALL tbodys on the page — not just tagged table!
});

// L647-649 (for #appr_sel_all_nt — non-tagged table): same pattern
// L1243-1245 (for #nt_select_all — form page NT): same pattern
```
**Root Cause**: Selector `$("tbody tr td input[type='checkbox']")` is not scoped to the specific table. It matches ALL checkboxes inside ANY `tbody` on the entire page.
**Impact**: Clicking "Select All" in the Tagged table also checks/unchecks all checkboxes in Non-Tagged, Old Metal, and Packaging tables. This causes approving items from wrong transfer types, or the counts to be wrong.
**Fix**: Scope selectors to parent table: `$(this).closest('table').find("tbody tr td input[type='checkbox']")`.

---

### BRN-D41 — `console.log` of Full Transfer Payload [P1]

```javascript
// ret_branch_transfer.js L3981:
function add_to_trans(trans_data) {
    console.log("trans_data : " , trans_data);  // ← Exposes full payload in production
```
**Impact**: Every branch transfer save exposes `trans_data` (containing tag IDs, lot IDs, branch IDs, pieces, weights) in the browser developer console. A mall employee or attacker with browser access can see all transfer data being submitted.

---

### BRN-D42 — Multiple `console.log()` Debug Artifacts [P2]

```javascript
// L903: console.log(dt);           // In approveBranchTransfer()
// L2289: console.log($("#filter_from_brn").val()+" "+$("#filtr_to_brn").val());  // In get_branch_transfer_download()
// L2899: console.log(i);           // In getSearchDesign() autocomplete response handler
// L3981: console.log("trans_data : " , trans_data);  // In add_to_trans() — elevated to BRN-D41
```
Four `console.log` calls left in production code expose sensitive data to browser console.

---

### BRN-D43 — Stray Backtick Syntax Error [P2]

```javascript
// L2221:
$("#btran_filter").on('click', function(e){``
//                                        ↑↑ Stray backtick pair
```
**Root Cause**: A stray template literal prefix ` `` ` appears right after `function(e){`. While modern JS engines may silently evaluate this as an empty template literal expression, it is a syntax anomaly that could break in some engines or minifiers.

---

### BRN-D44 — Global Variable Leaks in `add_to_trans()` [P2]

```javascript
// L4007-4011 (trans_type == 1 branch inside add_to_trans):
grs_wt = $(".prev_grs_wt").val();    // ← no 'var'
net_wt = $(".prev_net_wt").val();    // ← no 'var'
pieces = $(".prev_pieces").val();    // ← no 'var'
```
These are assigned to the global scope. They collide with the same variables used in approval handlers and calculation functions elsewhere in the file.

---

### BRN-D45 — Multiple Undeclared Global Variables in Approval Handlers [P2]

```javascript
// L705: trans_type = $("input[name='transfer_item_type']:checked").val();  // no var
// L865: trans_type = ...   (same, elsewhere)
// L2119, L2223, L2229, L2231: trans_type, approval_type, from_brn, to_brn — all no var
```
`trans_type`, `approval_type`, `from_brn`, `to_brn` are repeatedly used without `var`/`let`/`const` throughout the file. These become global, creating race conditions if multiple AJAX callbacks or event handlers run concurrently.

---

### BRN-D46 — `dia_wt` Undeclared in Tag Change Handler [P2]

```javascript
// L1487 (inside .tag_id change handler):
dia_wt = dia_wt + parseFloat(row.find('td:eq(8) .dia_wgt').val());
// ↑ dia_wt is never declared or initialized in this handler scope
```
**Root Cause**: The `.tag_id` change handler (L1469) declares `pieces`, `grs_wt`, `net_wt` with `var` at L1471-1475 but omits `dia_wt`. The `dia_wt` accumulation on L1487 reads from an undefined global variable.
