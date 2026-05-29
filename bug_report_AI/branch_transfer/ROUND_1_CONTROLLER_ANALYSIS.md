# Branch Transfer — Round 1: Controller Code-Level Analysis

> **Date**: 2026-03-11
> **File**: `admin/application/controllers/admin_ret_brntransfer.php` (1,378 lines, 12 methods)

---

## Bugs Found: 7

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-101 | **P0** | Dangling Transaction in `verify_otp()` | L1154 | A |
| BRN-102 | **P0** | Missing trans_status + rollback in `verify_other_issue_otp()` | L1339-1348 | A |
| BRN-103 | **P1** | Live Error/Query Leak in Save Failure | L272-273 | A |
| BRN-104 | **P1** | Raw `$_POST` Used Throughout (104 instances) | L81-1374 | A |
| BRN-105 | **P2** | Raw `$_POST` Passed Directly to Model | L1367, L1374 | A |
| BRN-106 | **P2** | Ternary Precedence Bug in Log Remark | L265 | A |
| BRN-107 | **P3** | Commented Debug Statements (22 instances) | Various | A |

### BRN-101 — Dangling Transaction in `verify_otp()` [P0]
```php
// L1154: trans_begin() called
$this->db->trans_begin();
// L1155-1166: Only session checks + echo json_encode
// NO trans_commit() or trans_rollback() ever called!
echo json_encode($status);
```
**Root Cause**: `trans_begin()` opens a transaction but none of the code paths call `trans_commit()` or `trans_rollback()`. The transaction remains open until the DB connection closes, potentially holding locks.
**Pattern**: PAT-TXN-003 (Transaction State Leak)

### BRN-102 — Missing trans_status + rollback in `verify_other_issue_otp()` [P0]
```php
// L1339: trans_begin()
$this->db->trans_begin();
// L1348: trans_commit() on SUCCESS path only
$this->db->trans_commit();
// MISSING: No trans_status() check before commit
// MISSING: No trans_rollback() on any failure path
```
**Root Cause**: `trans_commit()` is called without checking `trans_status()`. Additionally, the failure paths (expired OTP L1346, invalid OTP L1355, empty OTP L1359) leave the transaction open.
**Pattern**: PAT-TXN-001 + PAT-TXN-003

### BRN-103 — Live Error/Query Leak in Save Failure [P1]
```php
// L272-273 (UNCOMMENTED — LIVE IN PRODUCTION):
echo $this->db->last_query();
echo $this->db->_error_message();
```
**Root Cause**: Debug statements left uncommented in the save failure branch. Leaks SQL queries and DB error messages to client.
**Impact**: Information disclosure — attacker can see table names, column names, query structure.

### BRN-104 — Raw `$_POST` Used Throughout [P1]
104 instances of `$_POST['field']` instead of `$this->input->post('field')`. Bypasses CI3 XSS filtering and input sanitization.
**Pattern**: PAT-SEC-002

### BRN-105 — Raw `$_POST` Array Passed to Model [P2]
```php
// L1367:
$data = $this->ret_brntransfer_model->get_purchase_items($_POST);
// L1374:
$data = $this->$model->getNonTagReceiptedLots($_POST);
```
**Root Cause**: Entire `$_POST` superglobal passed as parameter. Model receives unfiltered user input.

### BRN-106 — Ternary Precedence Bug in Log Remark [P2]
```php
// L265:
'remark' => "Trans code : " . $trans_code . " " . $_POST['item_tag_type'] == 1 ? "Tags added..." : "Non-Tag items added..."
```
**Root Cause**: PHP operator precedence: `.` (concatenation) binds tighter than `==`. The expression `"Trans code : " . $trans_code . " " . $_POST['item_tag_type']` is evaluated first, then compared to `1`. Since a non-empty string always evaluates truthy, the remark is always "Tags added..." regardless of item type.
**Fix**: Add parentheses: `($_POST['item_tag_type'] == 1 ? ... : ...)`

### BRN-107 — Commented Debug Statements [P3]
22 commented-out `print_r`, `echo`, `var_dump` throughout the controller. While not active, they indicate development artifacts.
