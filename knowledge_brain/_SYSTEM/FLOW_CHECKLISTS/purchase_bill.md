# Flow Checklist: Purchase Bill (Bill Type 4) — from Karigar/Supplier

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_purchase.php` → `purchase()` L528
> **Cancel:** `cancel_bill()` in `admin_ret_billing.php` L7773 (shared)

---

## Overview

Purchase Bill (type 4) = Receiving goods FROM supplier/karigar. Can include GRN, lot generation, and tagging.

### Key Entry Points in admin_ret_purchase.php
| Method | Line | Purpose |
|---|---|---|
| `purchase()` | L528 | Save/list/edit purchase bills |
| `purchasereturn()` | L7535 | Return items to supplier |
| `supplier_po_payment()` | L5263 | Payment to supplier against PO |
| `purchase_payment()` | L6153 | Direct purchase payment |
| `generate_lot()` | L2829 | Generate lot from PO receipt |
| `qc_issue_receipt()` | L3801 | QC issue/receipt process |
| `halmarking_issue_receipt()` | L4347 | Hallmarking issue/receipt |
| `rate_fixing()` | L6595 | Fix rate for PO items |

---

## SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_billing` | INSERT (bill_type=4, supplier reference) | ⬜ |
| 2 | `ret_bill_details` | INSERT purchase line items | ⬜ |
| 3 | `ret_billing_payment` | INSERT payment records | ⬜ |
| 4 | `ret_purchase_order` updates | Link PO if purchase against PO | ⬜ |
| 5 | `ret_taging` | tag_status updates for received tags | ⬜ |
| 6 | `ret_nontag_item` | NT stock increase | ⬜ |
| 7 | `ret_journal` | INSERT journal (debit purchase, credit supplier payable) | ⬜ |
| 8 | Bill number generation | `bill_no_generate()` for purchase prefix | ⬜ |

## CANCEL Checklist

| # | Expected Reversal | Reversed? | Gap? |
|---|---|---|---|
| All | See `_cancel_master.md` — same shared cancel_bill() | ✅ | See master gaps |
| P1 | Purchased tag → tag_status back to pre-purchase | ⬜ | **VERIFY** — cancel sets tag_status→0 |
| P2 | NT stock reversed (-) | ⬜ | cancel_bill adds back (+) which is WRONG for purchase cancel |
| P3 | PO status unlinked | ⬜ | **VERIFY** |
| P4 | Journal reversed | ❌ | CRITICAL gap |

## PURCHASE RETURN FLOW (`purchasereturn()` L7535)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_purchase_return` | INSERT return header | ⬜ |
| 2 | `ret_purchase_return_items` | INSERT return line items | ⬜ |
| 3 | `ret_taging.tag_status` | Tags returned to supplier → status change | ⬜ |
| 4 | PO tracking | Update PO received vs returned quantities | ⬜ |
| 5 | `ret_journal` | Reverse purchase journal entry | ⬜ |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| PUR-001 | cancel_bill() uses SAME logic for purchase cancel — NT stock ADD instead of SUBTRACT | 🔴 CRITICAL |
| PUR-002 | Journal reversal missing | 🔴 CRITICAL |
| PUR-003 | Tag_status revert may be incorrect for purchase (tag was created, not sold) | 🟡 MED |
