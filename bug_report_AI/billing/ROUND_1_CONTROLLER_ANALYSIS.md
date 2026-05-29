# Billing Module Bug Audit — Round 1: Controller Code-Level Analysis

> **Module**: Billing | **Date**: 2026-02-24 | **Auditor**: Antigravity
> **Target**: `admin/application/controllers/admin_ret_billing.php` (12,173 lines)

---

## Summary

| Severity      | Count  |
| ------------- | ------ |
| P0 (Critical) | 3      |
| P1 (High)     | 4      |
| P2 (Medium)   | 3      |
| **Total**     | **10** |

---

## Bugs Found

### BIL-001 — Delete Path Targets Wrong Module Tables (P0)

**Location**: L4657–4689 (`case 'delete'` in `billing()`)
**Description**: The delete case deletes **estimation** tables instead of billing tables:

```php
case 'delete':
    $this->db->trans_begin();
    $this->$model->deleteData('estimation_id', $id, 'ret_estimation');       // ← WRONG TABLE
    $this->$model->deleteData('est_id', $id, 'ret_est_gift_voucher_details'); // ← WRONG TABLE
    $this->$model->deleteData('est_id', $id, 'ret_est_chit_utilization');     // ← WRONG TABLE
    $this->$model->deleteData('esti_id', $id, 'ret_estimation_items');        // ← WRONG TABLE
    $this->$model->deleteData('est_id', $id, 'ret_estimation_item_stones');   // ← WRONG TABLE
    $this->$model->deleteData('est_id', $id, 'ret_estimation_item_other_materials'); // ← WRONG
    $this->$model->deleteData('est_id', $id, 'ret_estimation_old_metal_sale_details'); // ← WRONG
```

**Root Cause**: Copy-paste from Estimation module controller. Tables should be `ret_billing`, `ret_bill_details`, `ret_billing_payment`, `ret_billing_item_stones`, `ret_bill_other_metals`, etc.
**Impact**: Clicking delete on a bill destroys the linked **estimation** record but leaves the bill intact. Estimation data corruption.
**Track**: B — Business logic (needs human to confirm correct billing tables for delete)

---

### BIL-002 — DB Error Messages Leaked to Client (P1)

**Location**: L1097, L4590, L6040, L6393, L6478, L7358, L7478, L9463, L10044 (9 instances)
**Description**: Active `echo $this->db->_error_message()` calls output raw database error details to the browser response. Exposes:

- Table names and column names
- SQL syntax details
- MySQL server version information

```php
// Example at L1097:
echo $this->db->_error_message() . "<br/>";
echo $this->db->last_query();
```

**Impact**: Information disclosure — attackers can learn DB schema, table names, and query structure.
**Fix**: Replace with `log_message('error', $this->db->_error_message())` + generic user error message.
**Track**: A — Security

---

### BIL-003 — No log_message() Usage Across Entire Controller (P2)

**Location**: Entire file (0 instances)
**Description**: The 12,173-line controller has **zero** `log_message()` calls. All error paths either silently fail or echo raw errors to the browser. No audit trail exists for:

- Failed transactions
- Invalid input rejections
- E-invoice API failures
- File upload failures
  **Impact**: Production issues are untraceable. No log forensics possible.
  **Track**: A — Infrastructure

---

### BIL-004 — 13+ Transaction Blocks Without trans_status() Check (P0)

**Location**: Multiple (see detailed list below)
**Description**: 36 `trans_begin()` calls vs only 23 `trans_status()` checks = 13+ transaction blocks that call `trans_commit()` directly without verifying success.

**Unguarded transaction blocks** (trans_begin → trans_commit with no trans_status):

- L8750–L8763 (issue receipt save)
- L8809–L8817 (issue receipt update)
- L8892–L8907 (receipt save)
- L8953–L8961 (receipt update)
- L9036–L9049 (receipt delete)
- L9095–L9103 (receipt cancel)
- L10073–L10110 (advance transfer)
- L10137–L10154 (advance transfer type 2)
- L10239–L10254 (insurance billing)
- L11387–L11424 (service bill save)
- L11452–L11469 (service bill cancel)
- L11519–L11556 (service bill item save)
- L11586–L11603 (service bill item delete)

**Impact**: Silently failed queries get committed → partial data persisted → data corruption.
**Track**: A — Transaction safety

---

### BIL-005 — OTP Leaked in AJAX Response (P1)

**Location**: L5589
**Description**: The `send_otp_for_chit_billing()` function returns the OTP in the AJAX response:

```php
$status = array('status' => true, 'msg' => 'OTP sent Successfully', 'otp' => $OTP);
```

**Impact**: OTP is visible in browser dev tools. Anyone with F12 access can read the OTP without receiving the SMS. Defeats the purpose of OTP verification entirely.
**Fix**: Remove `'otp' => $OTP` from the response. The OTP should only be sent via SMS and verified server-side.
**Track**: A — Security

---

### BIL-006 — CSRF/form_secret Protection Missing on AJAX Endpoints (P1)

**Location**: Multiple AJAX endpoint methods
**Description**: The `form_secret` validation is present in the main `billing()` save/split paths (L319, L353, L1139, L1239, L6584), but is **absent** from:

- `paymentmode_edit()` save (L8448)
- `bank_ledger_transfer()` save (L10073+)
- `cash_collection()` save (L11948+)
- All issue/receipt AJAX endpoints
- Customer create/update endpoints (L5196, L5224)
- All AJAX search endpoints (L5352–L5514)

**Impact**: These endpoints are vulnerable to CSRF attacks. An attacker could forge requests to edit payment modes, create ledger transfers, or modify customer data.
**Track**: A — Security

---

### BIL-007 — Payment Edit Uses DELETE-then-INSERT Pattern (P1)

**Location**: L8448 in `paymentmode_edit()`
**Description**: Payment mode edit path deletes all existing payment records before re-inserting:

```php
$status = $this->$model->deleteData('bill_id', $_POST['bill_id'], 'ret_billing_payment');
```

If the INSERT fails after DELETE succeeds, all payment data is lost permanently (no rollback observed in this block).
**Impact**: Data loss risk on payment mode edits if any INSERT operation fails mid-way.
**Track**: B — Business data integrity

---

### BIL-008 — Cancel Bill Uses Misspelled switch Case (P2)

**Location**: L5009 (`case 'cancell':` — double L)
**Description**: The cancel case in `billing()` uses `'cancell'` (misspelled). The URL must match this exact spelling. While functional (the URL uses the same misspelling), this creates maintenance confusion and breaks convention.
**Track**: A — Code quality

---

### BIL-009 — Delete Path Accessible via GET Request (P0)

**Location**: L4657 (`case 'delete'` in `billing()`)
**Description**: The delete operation is reachable via GET through the URL pattern `admin_ret_billing/billing/delete/{id}`. No POST-only guard, no form_secret check, no OTP verification.

```php
// URL: /admin_ret_billing/billing/delete/123  ← GET request triggers delete
case 'delete':
    $this->db->trans_begin();
    // ... deletes records immediately ...
```

**Impact**: CSRF vulnerability. A malicious link `<img src="/admin_ret_billing/billing/delete/123">` in an email or page would delete the record when an admin visits.
**Track**: A — Security

---

### BIL-010 — OTP Function Uses trans_begin but Commits Outside Conditional (P2)

**Location**: L5548–L5595 (`send_otp_for_chit_billing()`)
**Description**: `trans_begin()` is called inside the `if ($mobile_num != '')` block at L5548, but `trans_commit()` at L5585 and `trans_rollback()` at L5592 are controlled by `if ($insId)` which is OUTSIDE the mobile number check. If `$mobile_num` is empty, `$insId` is undefined → `trans_rollback()` is called without a prior `trans_begin()`.

```php
if ($mobile_num != '') {
    $this->db->trans_begin();       // ← inside IF
    // ...
    $insId = $this->$model->insertData(...);
}
if ($insId) {                        // ← outside IF, $insId possibly undefined
    $this->db->trans_commit();       // ← commits without matching begin?
} else {
    $this->db->trans_rollback();     // ← rollback without matching begin?
}
```

**Track**: A — Logic error

---

## Transaction Safety Summary

| Metric               | Count   |
| -------------------- | ------- |
| `trans_begin()`      | 36      |
| `trans_commit()`     | 37      |
| `trans_rollback()`   | 34      |
| `trans_status()`     | 23      |
| **Unguarded blocks** | **13+** |

---

## Round 1 Result

```
Round 1: Controller Analysis Complete
├── Total bugs: 10
├── P0 (Critical): 3 — BIL-001 (wrong delete tables), BIL-004 (unguarded trans), BIL-009 (GET delete)
├── P1 (High): 4 — BIL-002 (error leak), BIL-005 (OTP leak), BIL-006 (CSRF gaps), BIL-007 (DELETE-INSERT)
├── P2 (Medium): 3 — BIL-003 (no logging), BIL-008 (misspelling), BIL-010 (OTP trans logic)
├── Track A (System): 8
└── Track B (Business): 2
```
