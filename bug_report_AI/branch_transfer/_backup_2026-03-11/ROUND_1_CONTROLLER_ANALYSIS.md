# Round 1: Controller Code-Level Analysis
**Target**: `admin_ret_brntransfer.php`

## Bugs Found

### BRT-101: Raw `$_POST` Usage bypassing Input Filter
**Track**: A
**Severity**: P2
**Location**: `branch_transfer('save')`, `updateStatus` (many lines)
**Description**: The controller uses raw `$_POST` array variables extensively instead of `$this->input->post()`, bypassing CI3's built-in XSS and injection filtering.
**Fix**: Convert all `$_POST['xx']` to `$this->input->post('xx')`.

### BRT-102: Missing Transaction Status Verification Before Commit
**Track**: A
**Severity**: P0
**Location**: `send_otp()` (L1199), `verify_other_issue_otp()` (L1348), `branch_transfer('updateStatus')` (multiple instances where it checks IDs instead of `trans_status()`)
**Description**: The code commits the database transaction `if ($insId)` or completely blindly `else { $this->db->trans_commit(); }` without checking `$this->db->trans_status() === TRUE`. If a database error occurs but `$insId` is truthy, the system writes partial data.
**Fix**: Enforce `$this->db->trans_status() === TRUE` before `trans_commit()`.

### BRT-103: Transaction State Leak (Dangling Transaction)
**Track**: A
**Severity**: P0
**Location**: `branch_transfer('save')` around L249-L256 for `item_tag_type == 1`
**Description**: If the total parsed tag pieces do not match the expected `pieces`, the code sets `$allow_submit = FALSE` and bypasses the main commit/rollback block. Because `trans_begin()` was called previously, the script exits the method leaving a dangling, open transaction on the MySQL connection until timeout.
**Fix**: Add an explicit `$this->db->trans_rollback();` inside the failure condition at L252 before setting `$allow_submit = FALSE`.

### BRT-104: Silent Blank Page on Cancellation
**Track**: B
**Severity**: P2
**Location**: `update_branch_transfer_cancel()` L1291
**Description**: The method successfully updates the status to cancelled, commits, and sets a flash alert, but it abruptly ends without redirecting or returning a JSON response. The user will be stuck on a blank screen.
**Fix**: Check if this is called via AJAX (should return JSON) or direct POST (needs `redirect()`). Given `reqdata` array structure, it's likely an AJAX call. Add `echo json_encode(['status' => true, 'msg' => '...']);`.

### BRT-105: N+1 Iteration Overwrites on Master Table
**Track**: A
**Severity**: P3
**Location**: `branch_transfer('save')` L134
**Description**: Inside the `foreach ($_POST['trans_data'] as $nt_data)` loop for non-tagged sets, the code calls `updateData(...)` to update the *entire* `ret_branch_transfer` master row with the same overall `nt_pieces`/`grs_wt` values continuously. If there are 20 items, the master row is pointlessly updated 20 times.
**Fix**: Move the `updateData` call outside the `foreach` loop.
