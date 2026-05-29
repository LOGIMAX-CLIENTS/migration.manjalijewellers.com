# Flow Checklist: Purchase Order (Create / GRN / QC / Cancel)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_purchase.php` → `order_description()` L248, `purchase()` L528
> **Tables:** `ret_purchase_order`, `ret_purchase_order_items`, `ret_grn_entry`, `ret_grn_items`

---

## PO CREATE

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_purchase_order` | INSERT (supplier, order_date, expected_date, items summary) | ⬜ |
| 2 | `ret_purchase_order_items` | INSERT line items (product, design, weight, quantity, rate) | ⬜ |
| 3 | PO number generation | Sequential per branch | ⬜ |
| 4 | Images | `set_image()` for design references | ⬜ |
| 5 | Karigar SMS | Notification sent on PO create | ⬜ |

## GRN (Goods Receipt Note)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_grn_entry` | INSERT receipt header | ⬜ |
| 2 | `ret_grn_items` | INSERT individual received items | ⬜ |
| 3 | PO qty updated | Received quantity tracked against PO | ⬜ |
| 4 | PO status | Mark as partial/complete receipt | ⬜ |

## QC PROCESS (`qc_issue_receipt()` L3801)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | QC issue | Send items for quality check | ⬜ |
| 2 | QC receipt | Receive back with pass/fail | ⬜ |
| 3 | Failed items | Return to supplier flow | ⬜ |
| 4 | Passed items | Move to hallmarking / lot generation | ⬜ |

## HALLMARKING (`halmarking_issue_receipt()` L4347)

| # | Item | Status |
|---|---|---|
| 1 | Issue for hallmarking | Tags sent to hallmark center | ⬜ |
| 2 | Receipt from hallmarking | Tags received with hallmark number | ⬜ |
| 3 | Status tracking | `pohmstatus()` in reports | ⬜ |

## LOT GENERATION from PO (`generate_lot()` L2829)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_lot_inwards` | CREATE lot from received PO items | ⬜ |
| 2 | `ret_lot_inwards_detail` | Line items from PO items | ⬜ |
| 3 | `ret_taging` | CREATE tags from lot | ⬜ |
| 4 | PO linkage | Lot linked back to PO | ⬜ |

## RATE FIXING (`rate_fixing()` L6595)

| # | Item | Status |
|---|---|---|
| 1 | Fix gold/silver rate for PO | Rate locked at agreed price | ⬜ |
| 2 | Approval | Rate fix requires approval | ⬜ |
| 3 | Un-fixing | Rate can be unfixed before payment | ⬜ |

## SUPPLIER PAYMENT (`supplier_po_payment()` L5263)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_po_payment` | INSERT payment record | ⬜ |
| 2 | PO balance | Reduce outstanding | ⬜ |
| 3 | Multiple payments | Track partial payments | ⬜ |

## PO CANCEL / DELETE

| # | Item | Status |
|---|---|---|
| 1 | Can PO be cancelled after GRN? | ⬜ **VERIFY** |
| 2 | GRN reversal on PO cancel | ⬜ |
| 3 | Lot/tag cleanup if generated | ⬜ |
| 4 | Payment refund if paid | ⬜ |

## Known Risks

| Risk | Description | Severity |
|---|---|---|
| PO-001 | PO comment-out of old save code (L4819) — verify active code path | 🟡 MED |
| PO-002 | Complex multi-step flow → partial completion possible | 🔴 HIGH |
| PO-003 | Rate fixing approval bypass | 🟡 MED |
