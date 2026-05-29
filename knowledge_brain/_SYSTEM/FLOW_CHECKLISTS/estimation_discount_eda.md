# Flow Checklist: Estimation Discount / EDA Approval

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_estimation.php` + `admin_ret_eda.php`
> **Model:** `ret_estimation_model.php`
> **CRITICAL:** Discount bypass = direct revenue leakage

---

## Overview

Discounts in eTail follow a controlled workflow:
1. Employee enters discount at estimation
2. If discount exceeds employee's limit → EDA (Employee Discount Approval) triggered
3. Manager approves/rejects via `admin_ret_eda`
4. Approved discount applied when estimation converts to bill

---

## DISCOUNT SAVE (at Estimation level)

| # | Table/Field | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_estimation.discount_amount` | Set discount value | ⬜ |
| 2 | `ret_estimation.discount_percentage` | Set discount % | ⬜ |
| 3 | `ret_estimation_items.discount_*` | Per-item discount breakdown | ⬜ |
| 4 | Employee limit check | Compare against `employee_settings.discount_limit` | ⬜ **VERIFY** |
| 5 | EDA trigger | If discount > limit → flag for approval | ⬜ |

## EDA APPROVAL FLOW (`admin_ret_eda`)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | EDA approval table | Manager approves/rejects | ⬜ |
| 2 | Notification | Employee notified of decision | ⬜ |
| 3 | Lock estimation | Cannot bill until EDA decision | ⬜ **VERIFY** |
| 4 | Override | Can manager give higher discount than requested? | ⬜ |
| 5 | Audit trail | Who approved, when, what amount | ⬜ |

## DISCOUNT → BILLING TRANSFER

| # | Item | Status |
|---|---|---|
| 1 | Estimation discount carries to bill | Discount amount transferred on billing | ⬜ |
| 2 | Net amount recalculated | bill total = items - discount | ⬜ |
| 3 | EDA-denied estimation | Cannot convert to bill if EDA rejected | ⬜ **VERIFY** |
| 4 | Round-trip accuracy | Estimation discount = Bill discount (no rounding loss) | ⬜ |

## CANCEL / EDIT

| # | Item | Status |
|---|---|---|
| 1 | Edit discount after EDA | Re-triggers EDA or silent update? | ⬜ **VERIFY** |
| 2 | Bill cancel with discount | Discount reversed automatically | ⬜ |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| DISC-001 | Client-side-only discount limit check — bypass via direct POST | 🔴 CRITICAL |
| DISC-002 | EDA approval may not block billing (only JS validation) | 🔴 HIGH |
| DISC-003 | Discount amount at bill may differ from estimation (rounding) | 🟡 MED |
| DISC-004 | No audit trail for who changed discount amount | 🟡 MED |
