# Flow Checklist: Branch Transfer (Send/Receive/Cancel)

> **Last Updated:** 2026-03-27
> **Source:** Branch Transfer FLOW_RISK_MATRIX
> **Covers:** Full transfer lifecycle: Create → Transit Approval → Download → Cancel

---

## Modules Involved
- **Branch Transfer** (primary) — `admin_ret_branch_transfer.php`
- **Tagging** — tag_status changes (0→4 transit, 4→0/3 download)
- **Non-Tagging Stock** — weight deduction/addition between branches
- **Billing** — SR/PS transferred bill marking
- **Packaging** — other inventory transfer

## Tables Touched
`ret_branch_transfer`, `ret_branch_transfer_details`, `ret_taging` (tag_status, current_branch), `ret_nontag_item`, `ret_nontag_item_log`, `ret_purchase_items_log`, `ret_bill_details` (transferred_to_acc_stock), `ret_bill_old_metal_sale_details` (is_transferred), `ret_other_inventory_purchase_items_details`, `customerorderdetails` (current_branch)

---

## SAVE Checklist (Transfer Create)

| # | Table | Expected Action | Status | Gap? |
|---|---|---|---|---|
| 1 | `ret_branch_transfer` | INSERT header (from_branch, to_branch, trans_code, status=1) | ⬜ | — |
| 2 | `ret_branch_transfer_details` | INSERT per tag/item detail | ⬜ | — |
| 3 | `ret_taging` | ⚠️ NO STATUS CHANGE at create (stays 0) | — | **BUG**: No server-side tag_status check at save |

## SAVE Checklist (Transit Approval — Status 1→2)

| # | Table | Expected Action | Status | Gap? |
|---|---|---|---|---|
| 1 | `ret_branch_transfer.status` | → 2 (In Transit) | ✅ | — |
| 2 | `ret_taging.tag_status` | → 4 (In Transit) for tagged items | ✅ | — |
| 3 | `ret_nontag_item` | Deduct weight from from_branch | ✅ | — |
| 4 | `ret_bill_details.transferred_to_acc_stock` | → 1 for SR/PS items | ✅ | — |
| 5 | `ret_purchase_items_log` | INSERT transit log | ✅ | — |

## SAVE Checklist (Download — Status 2→4)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_branch_transfer.status` | → 4 (Downloaded) | ✅ |
| 2 | `ret_taging.tag_status` | → 0 (Available) at to_branch | ✅ |
| 3 | `ret_taging.current_branch` | → to_branch | ✅ |
| 4 | `ret_nontag_item` | Add weight at to_branch | ✅ |
| 5 | `ret_purchase_items_log` | INSERT download log | ✅ |

## CANCEL Checklist

> ⚠️ **CRITICAL**: Cancel only updates master status. ALL downstream effects are NOT reversed.

| # | Table | Expected Reversal | Status | Gap? |
|---|---|---|---|---|
| 1 | `ret_branch_transfer.status` | → 3 (Cancelled) | ✅ | — |
| 2 | `ret_taging.tag_status` | → 0 (freed from transit) | ❌ | **CRITICAL BUG**: Tags stuck at status=4 forever |
| 3 | `ret_taging.current_branch` | → original from_branch | ❌ | **BUG**: Branch stale |
| 4 | `ret_nontag_item` | Restore deducted weight to from_branch | ❌ | **BUG**: NT stock imbalance |
| 5 | `ret_bill_details.transferred_to_acc_stock` | → 0 | ❌ | **BUG**: SR/PS count inflated |
| 6 | `ret_bill_old_metal_sale_details.is_transferred` | → 0 | ❌ | **BUG**: OM recount wrong |
| 7 | `ret_purchase_items_log` | Reversal entry | ❌ | **BUG**: No audit |
| 8 | `ret_other_inventory_purchase_items_details.status` | Reversal | ❌ | **BUG**: Packaging stuck |

**Cancel reversal score: 1/8 = 12.5% — CRITICAL GAP**

## EDIT Checklist
Not applicable — transfers cannot be edited after creation.

## PRINT Checklist

| # | Field | Source | Status |
|---|---|---|---|
| 1 | Transfer details | `ret_branch_transfer` + details | ⬜ |
| 2 | Tag list | `ret_branch_transfer_details` joined with `ret_taging` | ⬜ |

## REPORT Checklist

| # | Report | Source | Status |
|---|---|---|---|
| 1 | Stock Transfer Report | `ret_branch_transfer` + details | ⬜ |
| 2 | In-Transit Stock | `ret_branch_transfer` where status=2 | ⬜ |

## Known Bugs Found

| Bug ID | Missing Step | Severity |
|---|---|---|
| FR-BRT-004 | Cancel after transit: ZERO stock tables restored | 🔴 CRITICAL |
| FR-BRT-001 | No server-side tag_status validation at save | 🔴 HIGH |
| FR-BRT-005 | Tags permanently stuck at status=4 after cancel | 🔴 HIGH |
| FR-BRT-006 | Race condition on trans_code generation | 🟡 MED |
| FR-BRT-009 | Packaging shortfall — silent partial delivery | 🟡 MED |
