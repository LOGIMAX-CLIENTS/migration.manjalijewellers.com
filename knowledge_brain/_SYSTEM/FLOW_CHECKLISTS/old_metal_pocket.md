# Flow Checklist: Old Metal Pocket (Create / Melt / Test / Refine)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_metal_process.php`
> **Model:** `ret_metal_process_model.php`
> **CRITICAL:** 0% cancel coverage — any error requires manual DB surgery

---

## Overview

Old Metal Process handles customer's old jewelry exchange:
1. **Pocket Create** — old metal received from customer, stored in pocket
2. **Melting** — pocket sent for melting
3. **Testing** — purity testing post-melt
4. **Refining** — if needed, sent for refining
5. **Pocket Close** — final pure weight determined

### State Machine: `ret_old_metal_pocket.status`
| Status | Meaning |
|---|---|
| 0 | Open / Created |
| 1 | Melting In Progress |
| 2 | Testing / Tested |
| 3 | Refining |
| 4 | Closed (final weight done) |

---

## POCKET CREATE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_old_metal_pocket` | INSERT (pocket_no, date, branch, customer, metal_type, gross_wt) | ⬜ |
| 2 | `ret_old_metal_pocket_items` | INSERT individual old items | ⬜ |
| 3 | `ret_estimation_old_metal_sale_details` | Link to estimation if from billing | ⬜ |
| 4 | `ret_bill_old_metal_sale_details` | Link to bill if from billing | ⬜ |

## MELTING Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_old_metal_pocket.status → 1` | Mark as melting | ⬜ |
| 2 | Melting details | Record melt loss, result weight | ⬜ |

## TESTING Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_old_metal_pocket.status → 2` | Mark as tested | ⬜ |
| 2 | Purity result | Record actual purity from test | ⬜ |
| 3 | Pure weight calculation | gross × purity = pure_weight | ⬜ |

## CANCEL / DELETE Checklist

| # | Table | Expected Reversal | Reversed? | Gap? |
|---|---|---|---|---|
| 1 | `ret_old_metal_pocket.status` | Revert to previous | ❌ | 🔴 **NO CANCEL EXISTS** |
| 2 | `ret_old_metal_pocket_items` | Delete items | ❌ | 🔴 No cancel |
| 3 | Customer balance impact | If pocket linked to bill → reversal needed | ❌ | 🔴 No cancel |
| 4 | Financial impact | Pocket value committed once closed | ❌ | 🔴 No cancel |

> ⚠️ **CRITICAL: There is NO cancel/delete operation for old metal pockets.** Once created, errors can only be fixed by direct database manipulation.

## Known Bugs (from FLOW_RISK_MATRIX)

| Bug ID | Description | Severity |
|---|---|---|
| OMP-001 | **0/10+ cancel operations implemented** — complete reversal absence | 🔴 CRITICAL |
| OMP-002 | Status transitions have no guards — can skip states | 🔴 HIGH |
| OMP-003 | Linked billing old metal records not checked before pocket ops | 🟡 MED |
| OMP-004 | No transaction wrapping on multi-step operations | 🟡 MED |
