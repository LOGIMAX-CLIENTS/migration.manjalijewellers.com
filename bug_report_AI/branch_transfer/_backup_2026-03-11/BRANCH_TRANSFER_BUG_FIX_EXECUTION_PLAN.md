# Branch Transfer — Bug Fix Execution Plan

*Generated for Sprint 1 (Critical Path)*

---

## 1. BRT-102: Missing trans_status Check Before Commit (P0)
### 1. Problem Summary
Transactions are committed blindly across OTP and update endpoints without validating if any underlying row inserts/updates actually failed.
### 2. Root Cause
`$this->db->trans_commit();` is called conditionally based on `$insId` (an arbitrary returned ID) rather than explicitly verifying `$this->db->trans_status() === TRUE`.
### 3. Impact Assessment
If the OTP log insert succeeds but a subsequent table fails, partial data is committed, destroying DB integrity.
### 4. Location 
`admin_ret_brntransfer.php` : `send_otp()` (L1199), `verify_other_issue_otp()` (L1348)
### 5. Current Code
```php
if ($insId) {
    $this->db->trans_commit();
    $status = array('status' => true, 'msg' => 'OTP sent Successfully');
}
```
### 6. Proposed Fix
```php
if ($insId && $this->db->trans_status() === TRUE) {
    $this->db->trans_commit();
    $status = array('status' => true, 'msg' => 'OTP sent Successfully');
} else {
    $this->db->trans_rollback();
}
```
### 7. Safety Rationale
Standard CI3 rollback protection; non-destructive.
### 8. Risk Level
Low
### 9. Regression Checklist
- [ ] Ensure OTP can still be triggered and verified.
### 10. Rollback Plan
Revert controller lines to omit `trans_status()`.
### 11. Confirmation Gate
**Track A:** Auto-approve diff.

---

## 2. BRT-103: Transaction State Leak (Dangling DB Conn) (P0)
### 1. Problem Summary
The system leaves a MySQL transaction open and exits if validation fails mid-save.
### 2. Root Cause
`save()` line ~252: `$allow_submit = FALSE;` skips the commit/rollback block, but `trans_begin()` was called earlier.
### 3. Impact Assessment
Database connection row-locks are held open until the MySQL engine times out, crashing the module for other users.
### 4. Location
`admin_ret_brntransfer.php` : `branch_transfer('save')` L252
### 5. Current Code
```php
$result = array("status" => 0);
$allow_submit = FALSE;
```
### 6. Proposed Fix
```php
$result = array("status" => 0);
$this->db->trans_rollback(); // <--- explicitly close
$allow_submit = FALSE;
```
### 7. Safety Rationale
Unblocks stalled MySQL connections safely.
### 8. Risk Level
Low
### 9. Regression Checklist
- [ ] Trigger an error in `item_tag_type == 1` and ensure the page doesn't hang.
### 10. Rollback Plan
Remove the added explicit rollback.
### 11. Confirmation Gate
**Track A:** Auto-approve diff.

---

## 3. BRT-R301: Zero Value Loss During Fallback Checks (P1)
### 1. Problem Summary
Explicit `0` values parsed from the frontend are silently dropped during save/update resulting in incorrect calculations.
### 2. Root Cause
PHP's `empty("0")` returns true. Model `insertData` and `updateData` loops discard these values.
### 3. Impact Assessment
If a branch transfer edits a weight to explicitly be `0.00`, the system will ignore it and overwrite with `NULL` or the previous value.
### 4. Location
`ret_brntransfer_model.php` : L32, L67
### 5. Current Code
```php
if ((empty($value) || $value == 'null')  ) {
```
### 6. Proposed Fix
```php
if (($value === '' || $value === null || $value === 'null')  ) {
```
### 7. Safety Rationale
Strict type checking prevents silent truncation of valid financial 0 integers.
### 8. Risk Level
Medium (Model level change impacts all controllers)
### 9. Regression Checklist
- [ ] Verify creation of an item with a 0 weight saves as 0 in DB.
### 10. Rollback Plan
Restore `empty()` in `insertData`.
### 11. Confirmation Gate
**Track B:** Human completes code review to ensure zero logic is deliberate for Branch Transfers.
