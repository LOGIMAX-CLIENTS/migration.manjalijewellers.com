# Branch Transfer — Bug Fix Execution Plan

> **Date**: 2026-03-11
> **Total Bugs**: 76 (74 unique)
> **Sprint 1 Focus**: 8 P0 (Critical) + 22 P1 (High)

---

## P0 — Critical System Bugs (Sprint 1)

### 1. BRN-D37: Hardcoded `transfer_to: 1` for Old Metal
### 1. BRN-D37: Hardcoded `transfer_to: 1` for Old Metal ✅ Fixed 2026-03-12
- **Problem**: Old metal transfers ignore user-selected destination branch.
- **Root Cause**: Hardcoded value `1` in `ret_branch_transfer.js:4039`.
- **Impact**: Critical data corruption; stock misrouted.
- **Proposed Fix**: Replace `1` with variable `to_brn`.
- **Safety**: Track A — Mechanical fix.
- **Pattern**: PAT-VAR-002 (Hardcoded Routing Values)
- **Rollback Plan**: `git checkout admin/assets/js/ret_branch_transfer.js`
- **GitHub**: TBD

### 2. BRN-D12: Dangerous DELETE without WHERE scope
- **Problem**: Table `retail_transfer_item_temp` cleared without user identifier.
- **Root Cause**: `model:123` uses `$this->db->empty_table()`.
- **Impact**: Concurrency bug; one user's save clears all users' temp data.
- **Proposed Fix**: Change to `$this->db->delete()` with `id_user` filter.
- **Safety**: Track A — Mandatory for multi-user safety.

### 3. BRN-D15: Systemic Null-Crash Pattern
- **Problem**: `->row()->column` access errors if no record found.
- **Root Cause**: Missing `$result ? $result->col : 0` guards in Model.
- **Impact**: Fatal PHP errors in production.
- **Proposed Fix**: Implement `if(!empty($row))` guards before property access.
- **Safety**: Track A — Stability fix.

### 4. BRN-R301: SQL Injection Vulnerability
- **Problem**: Unescaped user input in `LIKE` clauses.
- **Root Cause**: Direct concatenation in `$this->db->where()`.
- **Impact**: DB exploit risk.
- **Proposed Fix**: Use Query Bindings or CI3 Active Record array syntax.
- **Safety**: Track A — Security requirement.
### 4. BRN-D03: SQL Injection Vulnerabilities (50+ Concat Points) ✅ FIXED 2026-03-12
- **Problem**: Extensive unescaped user input concatenated directly into raw SQL. (Supersedes BRN-R301 / BRN-S01).
- **Root Cause**: Direct concatenation (`$this->db->query()`, `$this->db->like()`, etc.) across 12+ methods.
- **Impact**: DB exploit risk (SQLi).
- **Fix Applied**: Systematically applied `$this->db->escape()` and `$this->db->escape_like_str()` across all vulnerable concatenation points.
- **Pattern**: PAT-SEC-001
- **GitHub**: TBD

---

## P1 — High Priority Bugs (Sprint 1)

### 5. BRN-D38: Duplicate Tag Logic Failure
- **Problem**: Search results added even if they exist in list.
- **Root Cause**: `ret_branch_transfer.js:3365` compares against `data[0]` instead of `val`.
- **Proposed Fix**: Change `data[0].tag_id` to `val.tag_id`.

### 6. BRN-D50: Temporal Dead Zone in `send_otp()`
- **Problem**: OTPs for "Other Issue" transfers go to wrong branch.
- **Root Cause**: Variable used at L4941 before declaration at L4943.
- **Proposed Fix**: Move declaration to top of function.

### 7. BRN-D40/D49: Un-scoped Select-All Checkboxes
- **Problem**: Clicking "Select All" affects unrelated tables.
- **Root Cause**: `$("tbody ...")` selector is too global.
- **Proposed Fix**: Scope to parent container: `$(this).closest('table').find("tbody...")`.

### 8. BRN-101: Orphaned trans_begin() in verify_otp() ✅ FIXED 2026-03-11
- **Problem**: `verify_otp()` opened a DB transaction but never committed/rolled back. Zero DB writes in the function.
- **Root Cause**: Copy-paste artifact from `send_otp()` which does have DB writes.
- **Fix Applied**: Removed `$this->db->trans_begin()` at L1154.
- **Pattern**: PAT-TXN-004 (new)
- **GitHub**: [#1165](https://github.com/Logimax-Technologies/etail_development_src/issues/1165) — Closed

### 9. BRN-102: trans_commit BEFORE updateData() in verify_other_issue_otp() ✅ FIXED 2026-03-12
- **Problem**: `trans_commit()` called at L1348 before `updateData()` at L1350 — write runs outside the transaction. Expired-OTP and invalid-OTP paths left transaction open (no rollback).
- **Root Cause**: Misplaced `trans_commit()` in the success branch.
- **Fix Applied**: Moved `trans_begin()` to wrap only the DB write; added `trans_status()` check; added `trans_rollback()` to all failure exits.
- **Status**: ✅ FIXED
- **GitHub**: [#1166](https://github.com/Logimax-Technologies/etail_development_src/issues/1166) — Closed

### 10. BRN-103: Live Error / Query Leak in Save Failure ✅ FIXED 2026-03-11
- **Problem**: Naked `echo` of `last_query()` and `_error_message()` in standard output + JSON result array injected them into client responses.
- **Root Cause**: Diagnostic artifacts left in production DB failure paths.
- **Fix Applied**: Stripped strings and `q`/`err` JSON keys, replaced with server-side `log_message()`.
- **Pattern**: PAT-SEC-005
- **GitHub**: [#1173](https://github.com/Logimax-Technologies/etail_development_src/issues/1173) — Closed

### 11. BRN-104: Raw `$_POST` Used Throughout ✅ FIXED 2026-03-12
- **Problem**: 104 instances of raw `$_POST` access found in `admin_ret_brntransfer.php`.
- **Root Cause**: Bypassing CodeIgniter's XSS input filtering methods.
- **Fix Applied**: Automated regex replacement converting all `$_POST` usages to `$this->input->post()` and adjusting `isset()` syntax.
- **Pattern**: PAT-SEC-002
- **GitHub**: [#1174](https://github.com/Logimax-Technologies/etail_development_src/issues/1174) — Closed

---


### 12. BRN-D01: Variable Mismatch Crash in getProductsByFilter() ✅ FIXED 2026-03-12
- **Problem**: Fatal PHP crash returning undefined `$data` after resolving DB `$result`.
- **Root Cause**: Variable mismatch due to template copy-paste error.
- **Fix Applied**: Checked `$result` output and returned `$result->result_array()` safely.
- **Pattern**: PAT-VAR-001
- **GitHub**: [#1167](https://github.com/Logimax-Technologies/etail_development_src/issues/1167) — Closed

---

### 13. BRN-D02: updateDatamulti() Returns Undefined $id_value ✅ FIXED 2026-03-12
- **Problem**: Model logically failed to return operation state, referencing undefined `$id_value`.
- **Root Cause**: Copied boilerplate logic from another function without updating return signature.
- **Fix Applied**: Rewrote return statement to output `$edit_flag` which securely holds the boolean result of `$this->db->update()`.
- **Pattern**: PAT-VAR-001
- **GitHub**: [#1177](https://github.com/Logimax-Technologies/etail_development_src/issues/1177) — Closed

---

### 14. BRN-D09: Undefined $FromDt Array Clearing ✅ FIXED 2026-03-12
- **Problem**: Model logically failed to return dataset when `$profile_settings['allow_bill_type'] == 2` because of an undefined `$FromDt` comparison with `cur_entry_date` clearing the array.
- **Root Cause**: Capitalization typo or missed variable linking against the method's `$from_date` parameter.
- **Fix Applied**: Rewrote condition to check `$from_date != $cur_entry_date` ensuring expected method functionality.
- **Pattern**: PAT-VAR-001
- **GitHub**: TBD

---

### 16. BRN-R401: Duplicate DOM ID `id_product` ✅ Fixed 2026-03-12
- **Problem**: Two `id="id_product"` hidden inputs in `form.php` (L267 + L288) — non-tagged product autocomplete silently broken.
- **Root Cause**: Copy-paste of form section without adjusting element IDs.
- **Fix Applied**: Renamed second hidden input to `id_product_nt`; updated JS autocomplete handler to use `$(e.target).attr('id')` context routing.
- **Pattern**: PAT-DOM-001 (NEW)
- **GitHub**: [#1176](https://github.com/Logimax-Technologies/etail_development_src/issues/1176) — Closed ✅

---

### 15. BRN-D17: get_headoffice_branch Null-Crash ✅ Fixed 2026-03-12
- **Problem**: Missing `if ($sql->num_rows() > 0)` evaluation on `get_headoffice_branch()` expecting 1 row implicitly before calling `->row()->id_branch` will crash if no head office branch is currently defined.
- **Root Cause**: Unsafe blind trust assuming there is ALWAYS a result without a fallback return `0`.
- **Proposed Fix**: Wrap property access `->row()->id_branch` in a `num_rows() > 0` condition, defaulting to `return 0;` safely otherwise.
- **Risk Level**: Low.
- **Pattern**: PAT-VAR-001 (Null Object Reference / Pointer crash avoidance).
- **Rollback Plan**: `git checkout admin/application/models/ret_brntransfer_model.php`
- **GitHub**: TBD

---

### 17. BRN-D10: get_verifMobNo() Null-Crash ✅ Fixed 2026-03-12
- **Problem**: `get_verifMobNo()` called `->row()->otp_verif_mobileno` without a `num_rows()` guard — fatal PHP error when branch has no OTP mobile number configured, crashing the entire OTP send flow.
- **Root Cause**: Direct chained property access without null guard (PAT-VAR-001).
- **Fix Applied**: Added `num_rows() > 0` guard; applied `$this->db->escape()` to `$branch` parameter; returns `null` on empty — controller already handles `null` gracefully.
- **Pattern**: PAT-VAR-001
- **GitHub**: [#1178](https://github.com/Logimax-Technologies/etail_development_src/issues/1178) — Closed ✅


---

## Sprint 2 & 3: P2 Cleanup & Track B Logic
*See [CONSOLIDATED_BUG_REPORT.md](file:///c:/xampp_7.4\htdocs\etailv3\bug_report_AI\branch_transfer\CONSOLIDATED_BUG_REPORT.md) for the full index of 43 P2 and 3 P3 bugs.*

---

## Verification Plan
1. **Automated**: Run `tests/BranchTransferTest.php` (must be updated for P1 logic).
2. **Manual**: 
   - Perform Old Metal transfer and verify destination branch in DB.
   - Perform concurrent saves with two different users to verify no temp-table data loss.
   - Test "Select All" in multi-tab approval list.
