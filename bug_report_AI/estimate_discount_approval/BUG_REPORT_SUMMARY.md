# 🐛 Estimate Discount Approval — Bug Report Summary

> **Module**: Estimation → Estimate Discount Approval  
> **Audit Date**: 2026-02-17  
> **Auditor**: AI Static Analysis

---

## Module Snapshot

| Metric | Value |
|---|---|
| Controller | `admin_ret_estimation.php` — `discountApprovalList()`, `estimateDiscountApproval()` |
| Model | `ret_estimation_model.php` — `getDiscountApprovalList()`, `approve_estimation_discount()` |
| View | Inline JS / separate discount view |

---

## Bugs Found

> [!NOTE]
> The discount approval flow is relatively straightforward (list pending → approve/reject). Only one potential issue was identified.

### P2 — Minor

| ID | Title | Classification | Confidence |
|---|---|---|---|
| ESTDA-001 | Discount approval does not validate that the approving user has a different role than the requester — no separation of duties | Security | Low |

---

## Bug Details

### ESTDA-001: No separation of duties enforcement on discount approval

**Severity**: P2 | **Classification**: Security | **Confidence**: Low

**Observed Behavior**: The `estimateDiscountApproval()` method checks profile permissions but does not verify that the approving user is different from the user who created the estimation / requested the discount. A user with both roles could approve their own discounts.

**Expected Behavior**: The system should prevent self-approval of discounts (the approver should be a different user than the estimation creator).

**Risk**: Low to medium. Most implementations rely on role segregation at the admin level, but there's no code-level enforcement.

**Fix**: Add a check: `if ($this->session->userdata('id_user') == $estimation_creator_id) { reject; }`
