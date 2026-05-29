# Round 1 — Controller Code-Level Analysis

> **File**: `admin/application/controllers/admin_ret_tagging.php` (~9,787 lines)
> **Date**: 2026-02-24
> **Bugs Found**: 15

---

## TAG-001 — Zero Access Control Checks (P0)

**Category**: Security | **Track**: A
**Description**: The entire controller has **ZERO** access right / permission checks. Any authenticated user can access any tag operation regardless of role.
**Detection**: `grep access_right|check_access|permission|isAllowed` → 0 hits
**Impact**: Unauthorized users can create, edit, delete tags
**Fix**: Add `$this->check_access()` or equivalent to each route handler

---

## TAG-002 — Zero CSRF Protection (P0)

**Category**: Security | **Track**: A
**Description**: No CSRF/form_secret token checks anywhere in the controller. All POST handlers are vulnerable to CSRF attacks.
**Detection**: `grep form_secret|csrf|token` → 0 hits
**Impact**: Cross-site request forgery can create/modify/delete tags
**Fix**: Add CI3 CSRF token validation to all POST handlers

---

## TAG-003 — Zero Form Validation (P0)

**Category**: Security | **Track**: A
**Description**: No `$this->form_validation->set_rules()` calls. All input accepted without server-side validation.
**Detection**: `grep form_validation|set_rules|xss_clean` → 0 hits
**Impact**: Invalid/malicious data saved directly to DB
**Fix**: Add form validation rules for all SAVE/UPDATE endpoints

---

## TAG-004 — Raw $\_POST Usage (72 instances) (P1)

**Category**: Security | **Track**: A
**Description**: 72 uses of `$_POST[...]` bypassing CI3 input sanitization. Found across save, edit, AJAX handlers.
**Lines**: L359, L443, L1205, L1293, L2185, L2267, L2283, L2369, L2371, L3025, L3449, L3459, L3469, L3479, L3489, L3499, L3509, L3529, L5750, L5764, L5774, L5778, L5788, L5985, L5997, L6009, L6021, L6348, L6350, L6352 (+ 42 more)
**Fix**: Replace all with `$this->input->post('field')`

---

## TAG-005 — mkdir 0777 World-Writable (12 instances) (P2)

**Category**: Security | **Track**: A
**Lines**: L665, L775, L1141, L1471, L1581, L1953, L2509, L2867, L3055, L3275, L4813, L8236
**Fix**: Change to `mkdir($path, 0755, TRUE)`

---

## TAG-006 — Active Debug Statements in Production (10 instances) (P1)

**Category**: Code Quality | **Track**: A
**Description**: `print_r`, `echo last_query()`, `echo _error_message()` left active in production.
**Active Lines**:

- L389: `print_r($_POST)` — exposes all POST data
- L1161: `echo $this->db->last_query()` — exposes SQL
- L1163: `echo $this->db->_error_message();exit` — exposes DB errors + KILLS execution
- L1199: `print_r($_POST)` — exposes POST data
- L1201: `echo "</pre>"` — debug HTML
- L1239: `print_r($_POST)` — exposes POST data
- L1989: `echo $this->db->last_query()` — exposes SQL
- L1991: `echo $this->db->_error_message();exit` — exposes DB errors + KILLS execution
- L2057: `print_r($data)` — data leak
- L5004-5005: `print_r($previous_log_detail); print_r($updated_log_detail)` — data leak
  **Fix**: Remove or wrap in `ENVIRONMENT === 'development'` check

---

## TAG-007 — Unsafe Transactions: No trans_status Check (9 blocks) (P0)

**Category**: Transaction | **Track**: A
**Description**: 9 transaction blocks commit/rollback based on `if($insId)` boolean instead of `$this->db->trans_status()`. If the INSERT succeeds but a subsequent query fails silently, partial data persists.
**Unsafe blocks**:
| trans_begin Line | Method Context | Issue |
|---|---|---|
| L397 | LOT save (add_lot) | Commits on `$insId` only |
| L1247 | LOT save (duplicate path) | Same pattern |
| L3710 | Tag print/barcode | No status check |
| L5094 | Tag delete | No status check |
| L5178 | Tag bulk status update | No status check |
| L6888 | Re-tagging save | No status check |
| L9198 | Order unlink OTP send | Commits on `$insId` |
| L9252 | Order unlink OTP verify | Commits on `$update_otp` |
| L9503 | `update_purchase_cost()` MEGA | **Most critical**: 240-line block, commits without `trans_status()` |
**Fix**: Replace `if($insId)` with `if($this->db->trans_status() === TRUE)`

---

## TAG-008 — GET-Based Delete Without CSRF (P1)

**Category**: Security | **Track**: A
**Description**: `delete_tag_attribute($attr_tag_id)` at L6446 accepts the parameter from URL segment — GET-based delete, vulnerable to CSRF via `<img>` tag.
**Fix**: Change to POST with CSRF token

---

## TAG-009 — Echo-Based AJAX Responses Without Consistent Format (P2)

**Category**: Code Quality | **Track**: A
**Description**: ~80 AJAX endpoints use bare `echo json_encode($data)` without wrapping in standard `['status' => true/false, 'msg' => '...']` format. Some return raw arrays, others return single values.
**Fix**: Standardize AJAX response format

---

## TAG-010 — L1161/L1991 — `_error_message();exit` Kills Save Flow (P0)

**Category**: Transaction | **Track**: A
**Description**: At L1161-1163 and L1989-1991, `echo $this->db->_error_message(); exit;` is **active code** that terminates execution mid-save flow. This is inside the LOT save transaction blocks — if any DB error occurs, the debug statement kills the process without committing or rolling back the transaction.
**Fix**: Remove the debug lines entirely or wrap in environment check

---

## TAG-011 — print_r($\_POST) Exposes Sensitive Data (P1)

**Category**: Security | **Track**: A
**Description**: Active `print_r($_POST)` at L389, L1199, L1239 dumps full POST payload to browser including potentially sensitive customer/product data.
**Fix**: Remove all active print_r statements

---

## TAG-012 — Transaction Count Mismatch: 26 starts vs 25 commits (P2)

**Category**: Transaction | **Track**: A
**Description**: 26 `trans_begin()` calls but only 25 `trans_commit()` — one transaction block may leak (never committed). Needs manual trace to identify the orphan.
**Fix**: Audit each trans_begin to ensure matching commit/rollback

---

## TAG-013 — No Log Before Exit (P2)

**Category**: Code Quality | **Track**: A
**Description**: `exit;` calls at L1163, L1991 (and commented at L391, L1109, L1921) terminate without `log_message('error', ...)`. Per project rules, any `die()`/`exit()` must log first.
**Fix**: Add `log_message('error', ...)` before any exit

---

## TAG-014 — delete_tag_attribute Uses URL Segment (P1)

**Category**: Security | **Track**: A
**Location**: L6446-6466

```php
function delete_tag_attribute($attr_tag_id) {
    $status = $this->$model->deleteData('attr_tag_id', $attr_tag_id, 'ret_tagging_attributes');
}
```

**Description**: `$attr_tag_id` comes directly from URL segment — no validation, no type casting, potential SQL injection if `deleteData` doesn't sanitize.
**Fix**: Cast to int: `$attr_tag_id = (int) $attr_tag_id;`

---

## TAG-015 — Deprecated `_error_message()` Usage (P2)

**Category**: Code Quality | **Track**: A
**Description**: `$this->db->_error_message()` at L1163, L1991 is a **deprecated CI3 method** (underscore prefix indicates private/deprecated). Should use `$this->db->error()['message']`.
**Fix**: Replace with `$this->db->error()['message']`

---

## Summary

| Severity  | Count                                           |
| --------- | ----------------------------------------------- |
| **P0**    | 4 (TAG-001, TAG-002, TAG-003, TAG-007, TAG-010) |
| **P1**    | 5 (TAG-004, TAG-006, TAG-008, TAG-011, TAG-014) |
| **P2**    | 4 (TAG-005, TAG-009, TAG-012, TAG-013, TAG-015) |
| **Total** | **15**                                          |

| Track            | Count |
| ---------------- | ----- |
| **A (System)**   | 15    |
| **B (Business)** | 0     |
