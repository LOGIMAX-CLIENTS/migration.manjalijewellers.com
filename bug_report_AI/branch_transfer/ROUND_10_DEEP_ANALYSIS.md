# Branch Transfer — Deep Analysis Round 10: Controller L700-1378

> **Date**: 2026-03-11
> **Focus**: Controller approval flow (L700-950), scan download (L940-1060), print (L1088-1128), cancel (L1261-1292), OTP (L1149-1362)
> **Controller coverage**: 100% (1378/1378 lines read across R7+R8+R10)

---

## Bugs Found: 6

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-D21 | **P1** | Wrong variable `$partly_sale_log` used for insert (should be `$item_log`) | Ctrl L716, L750 | A |
| BRN-D22 | **P1** | Approval error leaks `last_query()` + `_error_message()` in AJAX response | Ctrl L932 | A |
| BRN-D23 | **P1** | `verify_otp()` orphan `trans_begin()` — never committed/rolled back | Ctrl L1154 | A |
| BRN-D24 | **P1** | `send_other_issue_otp()` calls `trans_begin()` inside loop — nested transactions | Ctrl L1303 | A |
| BRN-D25 | **P2** | `portriat` typo in PDF paper setting | Ctrl L1123 | A |
| BRN-D26 | **P2** | `Trasnfer` typo in 3 cancel log messages | Ctrl L1277, 1280, 1287 | A |

---

### BRN-D21 — Wrong Variable `$partly_sale_log` for Non-Tag SR Insert [P1]
```php
// Controller L704-716 (transit approval for non-tag SR items):
$item_log = array(
    'sold_bill_det_id' => $items['sold_bill_det_id'],
    'gross_wt'         => $items['gross_wt'],
    // ...
);
$this->$model->insertData($partly_sale_log, 'ret_purchase_items_log');  // ← BUG! $partly_sale_log

// Same at L738-750 (stock download for non-tag SR):
$item_log = array( ... );
$this->$model->insertData($partly_sale_log, 'ret_purchase_items_log');  // ← SAME BUG
```
**Root Cause**: Variable is defined as `$item_log` but the insert uses `$partly_sale_log` — which is defined at L759 only for the *partly sale* block that runs later. At L716, `$partly_sale_log` is either undefined (first iteration) or contains the value from a *previous transaction's partly sale* data.
**Impact**: Non-tag sales return items insert WRONG data (from a previous partly sale) or crash on first iteration with undefined variable warning. This corrupts `ret_purchase_items_log`.

### BRN-D22 — Approval Error Leaks DB Diagnostics [P1]
```php
// Controller L932:
$result = array('message' => 'Unable to proceed...', 'class' => 'danger',
    'q' => $this->db->last_query(),         // ← SQL query exposed
    'err' => $this->db->_error_message()    // ← DB error exposed
);
```
**Same pattern as BRN-103** (save flow L272-273), but this is in the approval flow's error response. Sent as JSON to the client.

### BRN-D23 — `verify_otp()` Orphan Transaction [P1]
```php
// Controller L1154:
$this->db->trans_begin();  // ← Transaction opened
// ... OTP verification logic ...
echo json_encode($status);  // ← Function exits WITHOUT trans_commit() or trans_rollback()
```
**Root Cause**: `trans_begin()` is called but the function never calls `trans_commit()` or `trans_rollback()`. The transaction is left dangling, holding locks until PHP garbage collection.
**Already documented as BRN-101** but confirmed here with line-level precision.

### BRN-D24 — `send_other_issue_otp()` Nested Transactions [P1]
```php
// Controller L1303 (inside foreach loop):
foreach ($mobile_num[0] as $mobile) {
    if ($mobile) {
        $this->db->trans_begin();  // ← Called on EVERY iteration
        // ... insert OTP, send SMS ...
    }
}
// L1324-1330:
$this->db->trans_commit();  // ← Only ONE commit outside the loop
```
**Root Cause**: `trans_begin()` is called inside the loop for each mobile number, but there's only one `trans_commit()`/`trans_rollback()` outside the loop. In MySQL, nested `trans_begin()` without savepoints will reset the transaction state. Also, `$insId` at L1324 may be undefined if `$mobile_num[0]` is empty.

### BRN-D25 — PDF Paper Setting Typo [P2]
```php
// Controller L1123:
$dompdf->set_paper("a4", "portriat");  // ← Should be "portrait"
```
DOMPDF may silently default to portrait anyway, but this is a code quality issue.

### BRN-D26 — "Trasnfer" Typo in Cancel Logs [P2]
```php
// Controller L1277: 'module' => 'Branch Trasnfer'  ← Typo
// Controller L1280: 'remark' => 'Reject Branch Trasnfer'  ← Typo
// Controller L1287: 'title' => 'Branch Trasnfer'  ← Typo
```
Three instances of "Trasnfer" (missing 'a') in cancel operation. Cosmetic, but makes log search/grep miss this module's entries.
