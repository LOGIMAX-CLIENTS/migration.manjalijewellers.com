# Branch Transfer — Deep Analysis Round 14: JavaScript (Final)

> **Date**: 2026-03-11
> **Focus**: JS file `ret_branch_transfer.js` — sections L4200-8879
> **Layer**: JavaScript (Old Metal, Packaging, Repair Orders, Mobile Approval)
> **Goal**: Complete 100% JS line-by-line coverage.

---

## Bugs Found: 10

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-D47 | P1 | Duplicate `id="ref_no"` generated in loop — breaks drill-down toggle | L4197, L4541 | A |
| BRN-D48 | P1 | Copy-paste error: "PARTLY SALE - SILVER" uses gold return variables | L6778-6780 | B |
| BRN-D49 | P1 | Un-scoped checkbox selectors in order/approval handlers (same as BRN-D40) | L7627, L7635 | A |
| BRN-D50 | P1 | Variable `isOtherIssue` used before declaration in `send_otp()` | L4941 | A |
| BRN-D51 | P2 | Hardcoded Socket URL in production environment | L8557 | A |
| BRN-D52 | P2 | Global leak `trans_type` in `send_mobile_approval_request` | L8289 | A |
| BRN-D53 | P2 | Missing local `my_Date` in `update_aprvl_status` (uses stale global) | L8675 | A |
| BRN-D54 | P2 | Multiple global leaks in DataTable renderers (`id`, `branch_trans_code`, etc.) | L5605-5609 | A |
| BRN-D55 | P2 | Typo with trailing space in class name `id_nontag_receipt ` | L8191 | A |
| BRN-D56 | P3 | UI Typos: "Approvel stauts" and "Approvel ID" | L8668, L8747 | A |

---

### BRN-D47 — Duplicate ID "ref_no" inside loop [P1]

```javascript
// L4197 (fnFormatRowDetails called in a loop):
'<td><input type="hidden" id="ref_no" value="'+ref_no+'"/><i class="fa ..."></i></td>'
```
**Impact**: `id="ref_no"` is non-unique if the transfer has multiple products. The click handler at L4541 `$('#ref_no',this).val()` will always return the value of the first instance in the DOM if selectors aren't perfectly narrow, but using `ID` as a recurrent row marker is an anti-pattern that breaks jQuery's ID optimization.
**Fix**: Change `id="ref_no"` to `class="ref_no"`.

---

### BRN-D48 — Copy-paste Error in Silver Preview [P1] 🟠

```javascript
// L6776-6782:
trHtml+='<tr>'
        +'<td>PARTLY SALE -SILVER</td>'
        +'<td>'+parseFloat(total_sales_ret_silver_gwt).toFixed(3)+'</td>' // ← BUG: Should be total_partly_sale_silver_gwt
        +'<td>'+parseFloat(total_sales_ret_silver_nwt).toFixed(3)+'</td>' // ← BUG: Should be total_partly_sale_silver_nwt
        +'</tr>';
```
**Impact**: The preview for "Partly Sale - Silver" displays weights from "Sales Return - Silver" instead of its own accumulated totals. Business logic error/UI misinformation.

---

### BRN-D49 — Un-scoped selectors (Repeat) [P1]

```javascript
// L7625-7631:
$('#order_select_all').click(function(event) {
    $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
});
```
**Impact**: Same as BRN-D40. Selecting all in the order list checks checkboxes in EVERY table on the page (tagged, non-tagged, etc.).

---

### BRN-D50 — Temporal Dead Zone (Variable hoisting) [P1]

```javascript
// L4935:
function send_otp(){ 
    var approval_type = $("input[name='bt_approval_type']:checked").val(); 
    var from_brn = ...
    var to_brn = ( isOtherIssue == 1? ... ); // ← isOtherIssue used here
    var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0); // ← defined here
```
**Root Cause**: `isOtherIssue` is used to calculate `to_brn` on L4941, but it's not defined/assigned until L4943. Because of `var` hoisting, it exists but is `undefined` at the moment of use.
**Impact**: The conditional logic for `to_brn` ALWAYS evaluates as if `isOtherIssue` is false. OTPs for "Other Issue" branch transfers will be sent to the wrong branch (or fail).

---

### BRN-D51 — Hardcoded Socket Server URL [P2]

```javascript
// L8557:
var socket = io('https://liverate.logimaxindia.com:3001/');
```
**Impact**: Architecture bug. The socket URL is hardcoded to a specific domain. If the application is moved to a different server or environment (testing/staging), it continues to point to the production socket server. 

---

### BRN-D53 — Missing local `my_Date` [P2]

```javascript
// L8675:
url: base_url + 'index.php/admin_app_api/update_aprvl_status/?nocache=' + my_Date.getUTCSeconds(),
```
**Root Cause**: `my_Date` is not declared or initialized inside `update_aprvl_status`. It relies on a global `my_Date` object which might be stale or undefined if this function is called directly (e.g. from the cancel button at L8545).
