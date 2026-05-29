# Billing Module — Bug Fix Execution Plan

> **Module**: Billing | **Date**: 2026-02-24 | **Total Bugs**: 33

---

## Recently Completed (3 bugs)

| Bug ID | Summary | Fixed Date |
|---|---|---|
| BIL-INT01 | Credit due amount failure in 2nd partial return | 2026-03-04 |
| BIL-INT02 | Credit due amount does not account for sales returns | 2026-03-05 |
| BIL-INT03 | Credit status stays Pending due to round-off residual after full return | 2026-03-06 |
| BIL-CLT03 | Frontend hidden input state fix and backend zero explicit save update | 2026-03-10 |
| BIL-CLT04 | Order advance date showing delivery date instead of payment date | 2026-03-23 |

---

## Sprint 1 — P0 Critical Fixes (5 bugs)

---

### BIL-001 — Delete Path Targets Wrong Module Tables

1. **Problem**: `case 'delete'` at L4657 deletes `ret_estimation*` tables instead of `ret_billing*`
2. **Root Cause**: Copy-paste from Estimation controller
3. **Impact**: Bill delete destroys estimation data; bill itself remains
4. **Location**: `admin_ret_billing.php` L4657–4689
5. **Current Code**:

```php
$this->$model->deleteData('estimation_id', $id, 'ret_estimation');
$this->$model->deleteData('est_id', $id, 'ret_est_gift_voucher_details');
// ...5 more estimation tables
```

6. **Proposed Fix**: Replace with billing tables — `ret_billing`, `ret_bill_details`, `ret_billing_payment`, `ret_billing_item_stones`, `ret_bill_other_metals`, `ret_billing_chit_utilization`, `ret_billing_advance`
7. **Safety**: Requires human verification of correct billing child tables
8. **Risk**: HIGH — wrong fix would delete wrong data
9. **Regression**: Test delete on test bill; verify no estimation records touched
10. **Rollback**: Revert file change
11. **Gate**: ⚠️ Track B — **Human must confirm the correct billing child tables before any code change**

---

### BIL-004 — 13+ Unguarded Transaction Blocks

1. **Problem**: `trans_commit()` called without `trans_status()` check
2. **Root Cause**: Omission during development
3. **Impact**: Silently commits failed queries → partial data
4. **Location**: 13+ blocks across controller (see Round 1 for specific lines)
5. **Current Code**: `$this->db->trans_begin(); ... $this->db->trans_commit();`
6. **Proposed Fix**: Add `if ($this->db->trans_status() === TRUE)` guard before each `trans_commit()`
7. **Safety**: Low risk — adds safety check, doesn't change happy path
8. **Risk**: Low
9. **Regression**: Test each affected save/update/cancel flow
10. **Rollback**: Revert file change
11. **Gate**: Track A — Auto-approve

---

### BIL-009 — Delete Path Accessible via GET

1. **Problem**: `case 'delete'` reached via URL with no POST/CSRF guard
2. **Root Cause**: Controller uses switch-case on URL segment
3. **Impact**: CSRF deletion via `<img>` tag or link
4. **Location**: `admin_ret_billing.php` L4657
5. **Current Code**: No POST check before delete
6. **Proposed Fix**: Add `if ($this->input->server('REQUEST_METHOD') !== 'POST')` guard + form_secret check
7. **Safety**: Low risk — adds guard
8. **Risk**: Low
9. **Regression**: Test delete still works via proper form submission
10. **Rollback**: Revert
11. **Gate**: Track A — Auto-approve

---

### BIL-PAT-SEC-001 — SQL Injection via Column Name

1. **Problem**: `$field` from input used in `SELECT $field FROM...`
2. **Root Cause**: Dynamic column name without whitelist
3. **Impact**: Full SQL injection
4. **Location**: `ret_billing_model.php` L293, L5024
5. **Proposed Fix**: Whitelist allowed column names, validate against list
6. **Risk**: Low
7. **Gate**: Track A — Auto-approve

---

### BIL-R601 — Raw $\_POST in Delete + No CSRF

1. **Problem**: `$_POST['bill_id']` used directly in `deleteData()` without sanitization or form_secret
2. **Root Cause**: Raw input usage + missing CSRF
3. **Impact**: Arbitrary payment record deletion
4. **Location**: `admin_ret_billing.php` L8448
5. **Proposed Fix**: Use `$this->input->post('bill_id')` + add form_secret validation
6. **Risk**: Low
7. **Gate**: Track A — Auto-approve

---

## Sprint 1 — P1 High Fixes (11 bugs)

> Due to volume, P1 fixes use abbreviated format. See individual round reports for full details.

| Bug ID          | Fix Summary                                                            | Risk   |
| --------------- | ---------------------------------------------------------------------- | ------ |
| BIL-002         | Replace 9 `echo _error_message()` with `log_message()` + generic error | Low    |
| BIL-005         | Remove `'otp' => $OTP` from AJAX response                              | Low    |
| BIL-006         | Add `form_secret` check to all AJAX endpoints                          | Medium |
| BIL-007         | Wrap payment DELETE-INSERT in transaction with rollback                | Medium |
| BIL-PAT-RAW-001 | Systemic — requires phased migration to query builder                  | High   |
| BIL-PAT-TXN-003 | Add `trans_status()` checks (overlaps BIL-004)                         | Low    |
| BIL-R302        | Refactor model to accept params instead of reading $\_POST             | Medium |
| BIL-R303        | Remove or restrict SHOW COLUMNS queries                                | Low    |
| BIL-R305        | Add mandatory `id_branch` and `is_cancelled` filters                   | Medium |
| BIL-R401        | Add meaningful error handlers to ~50 AJAX calls                        | Low    |
| BIL-R501        | Add `\|\| 0` to all parseFloat in accumulation loops                   | Low    |

---

## Sprint 2 — P2 Medium Fixes (12 bugs)

| Bug ID          | Fix Summary                                                | Risk    |
| --------------- | ---------------------------------------------------------- | ------- |
| BIL-003         | Add `log_message()` calls to all error/failure paths       | Low     |
| BIL-008         | Rename `'cancell'` to `'cancel'` + update all references   | Medium  |
| BIL-010         | Move `trans_begin/commit/rollback` to correct scope in OTP | Low     |
| BIL-PAT-SEC-002 | Replace 70+ `$_POST` with `$this->input->post()`           | Low-Med |
| BIL-PAT-SEC-003 | Change `0777` to `0755` in all `mkdir()` calls             | Low     |
| BIL-S01         | Add FK constraints to billing tables (ALTER TABLE)         | High    |
| BIL-S02         | Add CHECK constraint or ENUM for bill_type                 | Medium  |
| BIL-R304        | Replace `SELECT *` with specific columns                   | Low     |
| BIL-R402        | Create centralized billing validation function             | Medium  |
| BIL-R403        | Add `\|\| 0` to parseFloat on `.html()` calls              | Low     |
| BIL-R502        | Add `> 0` check to divided_by_value at L7873               | Low     |
| BIL-R504        | Add `pcs > 0` guard before centweight division             | Low     |

---

## Deferred Items (5 bugs — pending live DB)

| Bug ID   | Needs                                            |
| -------- | ------------------------------------------------ |
| BIL-S03  | Live DB `DESCRIBE` to check column types         |
| BIL-R404 | Manual review of duplicate isNaN selector intent |
| BIL-R405 | Manual XSS audit of 189KB + 149KB view files     |
| BIL-R503 | Low risk — toFixed consistency review            |
| BIL-R604 | Overlaps with BIL-005 fix                        |
