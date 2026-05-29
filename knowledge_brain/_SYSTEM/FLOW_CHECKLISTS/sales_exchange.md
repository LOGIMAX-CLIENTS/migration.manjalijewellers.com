# Flow Checklist: Sales + Old Metal Exchange (Bill Type 2)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_billing.php` → `billing()` L219 (bill_type=2)
> **Cancel:** `cancel_bill()` L7773 (shared flow — see `_cancel_master.md`)

---

## What Makes Type 2 Different from Type 1

Type 2 = Sales Bill + Old Metal exchange. Customer gives old jewelry, gets value deducted from bill.

### Additional Tables (beyond standard Sales Bill)
| Table | Action on Save | Action on Cancel |
|---|---|---|
| `ret_estimation_old_metal_sale_details` | INSERT — old metal tags, wt, amount | `purchase_status → 0, bill_id → NULL` (L7953-7955) |
| `ret_bill_old_metal_sale_details` | INSERT — per-item old metal breakdown | ⬜ **NOT REVERSED in cancel** |
| `ret_old_metal_pocket` | Create/update pocket for melting | ⬜ **NOT REVERSED in cancel** |

---

## SAVE Checklist (Type 2 additions over Type 1)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | All Type 1 steps | See `sales_bill.md` | ✅ |
| 2 | `ret_estimation_old_metal_sale_details` | INSERT purchase status=1, link to bill | ⬜ |
| 3 | `ret_bill_old_metal_sale_details` | INSERT old metal items per bill | ⬜ |
| 4 | `ret_old_metal_pocket` | Create pocket if new, update if existing | ⬜ |
| 5 | Old metal amount | Deducted from `net_amount` | ⬜ |

## CANCEL Checklist (Type 2 — inherits all from _cancel_master.md + gaps)

| # | Table | Expected Reversal | Reversed? | Gap? |
|---|---|---|---|---|
| All | See `_cancel_master.md` | 20 shared steps | ✅ | See master gaps |
| OM1 | `ret_estimation_old_metal_sale_details` | `purchase_status → 0, bill_id → NULL` | ✅ L7953 | — |
| OM2 | `ret_bill_old_metal_sale_details` | Rows should be deleted/status updated | ❌ | 🔴 **Not reversed** |
| OM3 | `ret_old_metal_pocket` | Pocket should be cancelled/reversed | ❌ | 🔴 **Not reversed** |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| EXCH-001 | `ret_bill_old_metal_sale_details` NOT reversed on cancel — old metal records orphaned | 🔴 HIGH |
| EXCH-002 | `ret_old_metal_pocket` NOT reversed — pocket remains active with cancelled bill's metal | 🔴 HIGH |
| EXCH-003 | Journal reversal missing (inherits from cancel_master) | 🔴 CRITICAL |
