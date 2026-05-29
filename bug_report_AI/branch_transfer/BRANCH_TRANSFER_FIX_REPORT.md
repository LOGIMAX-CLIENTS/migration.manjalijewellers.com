# Branch Transfer — Fix Report

> Running log of all applied fixes for the Branch Transfer module.
> Linked to: `BRANCH_TRANSFER_BUG_FIX_EXECUTION_PLAN.md` | `CONSOLIDATED_BUG_REPORT.md`

---

### Fix: BRN-101 — Dangling Transaction in verify_otp()
- **Date:** 2026-03-11
- **Track:** A (System)
- **Category:** Transaction (PAT-TXN-004 — Orphaned trans_begin in read-only function)
- **Severity:** P0
- **Files Changed:**
  - `admin/application/controllers/admin_ret_brntransfer.php` (1 line removed: L1154)
- **Root Cause:** `verify_otp()` performs zero DB writes (only session reads). The `$this->db->trans_begin()` was a copy-paste artifact from the sibling `send_otp()` function, which does write to the DB. Every OTP verification call left a dangling transaction handle on the MySQL connection.
- **Fix Applied:** Removed `$this->db->trans_begin()` at L1154. No other logic changed.
- **Tests:** PASS — 12/12 assertions (PHP 7.4 standalone test runner). PHPUnit test file ready at `admin/application/tests/BranchTransferTransactionTest.php` for when Composer is available.
- **Pattern:** PAT-TXN-004 (NEW — added to COMMON_BUG_PATTERNS.md)
- **Rollback:** Re-add `$this->db->trans_begin();` at L1154 in `verify_otp()`. Logged in `ROLLBACK_REGISTRY.md`.
- **GitHub Issue:** [#1165](https://github.com/Logimax-Technologies/etail_development_src/issues/1165) — Closed ✅

---

### Fix: BRN-102 — trans_commit Before updateData() in verify_other_issue_otp()
- **Date:** 2026-03-11
- **Track:** A (System)
- **Category:** Transaction (PAT-TXN-001 + PAT-TXN-003 composite — commit before write + missing trans_status)
- **Severity:** P0
- **Files Changed:**
  - `admin/application/controllers/admin_ret_brntransfer.php` (`verify_other_issue_otp()` restructured)
- **Root Cause:** `trans_commit()` was called at L1348 before `updateData()` at L1350 — the DB write ran entirely outside the transaction in auto-commit mode and could never be rolled back. Expired-OTP and invalid-OTP failure paths left `trans_begin()` open with no rollback. `updateData()` return value was ignored — a silent failure still returned `status:true`.
- **Fix Applied:** Moved `trans_begin()` to wrap only the DB write (success path). Added `trans_status() === FALSE || !$updStatus` guard before commit/rollback. All non-write paths (expired, invalid, empty OTP) exit cleanly with no transaction opened.
- **Tests:** PASS — 12/12 assertions (4 BRN-102 specific: success commits, failure rolls back, expired has no tx, commit index > update index)
- **Patterns:** PAT-TXN-001 + PAT-TXN-003 (composite match)
- **Rollback:** Restore original `verify_other_issue_otp()` — move `trans_begin()` before the if-block, restore `trans_commit()` at L1348 (before updateData), remove the `trans_status()` guard block. Logged in `ROLLBACK_REGISTRY.md`.
- **GitHub Issue:** [#1166](https://github.com/Logimax-Technologies/etail_development_src/issues/1166) — Closed ✅

---

### Fix: BRN-103 — Live Error/Query Leak in Save Failure
- **Date:** 2026-03-11
- **Track:** A (System)
- **Category:** Security
- **Severity:** P1
- **Files Changed:**
  - `admin/application/controllers/admin_ret_brntransfer.php` (`save()` and `updateStatus()`)
- **Root Cause:** Developer explicitly ran `echo $this->db->last_query(); echo $this->db->_error_message();` in the save failure path, garbling JSON and leaking internals. The `updateStatus` path also explicitly assigned `'q'` and `'err'` containing DB output to the JSON result payload.
- **Fix Applied:** Stripped the `echo` lines and result pairs. In both locations, substituted them with standard `log_message('error', ...)` to keep diagnostics strictly server-side.
- **Tests:** PASS (8 standalone tests — asserting no 'q' or 'err' keys in JSON response array, valid JSON payload generated, and grep confirm across file).
- **Pattern:** NEW — **PAT-SEC-005** (Live Database Error / SQL Query Leaks).
- **Rollback:** Reinsert `echo $this->db->last_query(); echo $this->db->_error_message();` under `$data['transfer_item_type']);` at `L272-273`. Add `'q' => $this->db->last_query(), 'err' => $this->db->_error_message()` back into `$result` array at `L932`.
- **GitHub Issue:** [#1173](https://github.com/Logimax-Technologies/etail_development_src/issues/1173) — Closed ✅

---

### Fix: BRN-104 — Raw `$_POST` Used Throughout
- **Date:** 2026-03-12
- **Track:** A (System)
- **Category:** Security
- **Severity:** P1
- **Files Changed:**
  - `admin/application/controllers/admin_ret_brntransfer.php` (global replacement)
- **Root Cause:** Direct access to `$_POST` array bypasses CodeIgniter's input filtering and XSS protections, leaving the application vulnerable to injection.
- **Fix Applied:** Automated regex replacement of all `$_POST` references to `$this->input->post()`. Safely handled `isset()` conversions to avoid syntax errors.
- **Tests:** PASS (Regression test verifying 0 instances of `$_POST` remain; PHP syntax linter).
- **Pattern:** PAT-SEC-002 (Raw $_POST Bypass)
- **Rollback:** Restore branch copy: `git checkout admin_ret_brntransfer.php`
- **GitHub Issue:** [#1174](https://github.com/Logimax-Technologies/etail_development_src/issues/1174) — Closed ✅

---
