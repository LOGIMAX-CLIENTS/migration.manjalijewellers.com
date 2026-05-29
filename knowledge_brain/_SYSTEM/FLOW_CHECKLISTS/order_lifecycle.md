# Flow Checklist: Order Advance → Order Delivery (Bill Types 5 & 9)

> **Last Updated:** 2026-03-27
> **Source:** Customer Order FLOW_RISK_MATRIX + Billing FLOW_RISK_MATRIX
> **Covers:** Full order lifecycle: advance payment → karigar assignment → completion → delivery billing

---

## Modules Involved
- **Customer Order** — order creation, assignment, status tracking
- **Billing** — advance bill (type=5) + delivery bill (type=9)
- **Tagging** — tag reservation (id_orderdetails), tag_status on delivery
- **Payment** — advance payment records
- **Account** — advance journal, delivery journal, advance adjustment

## Tables Touched
`customerorder`, `customerorderdetails`, `ret_order_advance_payment`, `ret_order_item_stones`, `customer_order_image`, `ret_order_other_charges`, `ret_taging` (id_orderdetails), `ret_billing` (type=5 advance, type=9 delivery), `ret_bill_details`, `ret_billing_payment`, `ret_billing_advance`, `ret_journal`, `ret_issue_receipt`, `joborder`

---

## SAVE Checklist (Order Advance — Bill Type 5)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `customerorder` | INSERT header (order_date, customer, branch, order_status=0/1) | ⬜ |
| 2 | `customerorderdetails` | INSERT per item (product, weight, rate, orderstatus=0/1) | ⬜ |
| 3 | `ret_order_advance_payment` | INSERT advance payment record | ⬜ |
| 4 | `ret_order_item_stones` | INSERT stone details per item | ⬜ |
| 5 | `customer_order_image` | INSERT uploaded images | ⬜ |
| 6 | `ret_order_other_charges` | INSERT additional charges | ⬜ |
| 7 | `ret_taging` | UPDATE `id_orderdetails = {order_detail_id}` (reserve tag) | ⬜ |
| 8 | `ret_billing` | INSERT advance bill (bill_type=5) | ⬜ |
| 9 | `ret_billing_payment` | INSERT advance payment | ⬜ |
| 10 | `ret_journal` | INSERT advance journal entries | ⬜ |

## SAVE Checklist (Order Delivery — Bill Type 9)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_billing` | INSERT delivery bill (bill_type=9) | ⬜ |
| 2 | `ret_bill_details` | INSERT delivery items | ⬜ |
| 3 | `ret_billing_payment` | INSERT remaining payment | ⬜ |
| 4 | `ret_billing_advance` | INSERT advance adjustment (deduct advance from total) | ⬜ |
| 5 | `ret_taging` | UPDATE `tag_status = 2` (sold) | ⬜ |
| 6 | `customerorderdetails` | UPDATE `orderstatus = 5` (delivered) | ⬜ |
| 7 | `ret_journal` | INSERT delivery journal + advance reversal | ⬜ |

## CANCEL Checklist (Order Cancel)

> Source: `ajax_order_cancel()` L656-721

| # | Table | Expected Reversal | Status | Gap? |
|---|---|---|---|---|
| 1 | `customerorder.order_status` | → 6 (cancelled) | ✅ | — |
| 2 | `customerorderdetails.orderstatus` | → 6 (cancelled) | ✅ | — |
| 3 | `ret_taging.id_orderdetails` | → NULL (tag freed) | ✅ | — |
| 4 | `ret_issue_receipt` | CREATE advance refund receipt | ⚠️ | **BUG**: Creates zero-amount row even when advance=0 |
| 5 | `joborder.orderstatus` | Should be restored | ❌ | **BUG**: Job order stays in stale WIP state |
| 6 | `customer_order_image` | Clean up images | ❌ | Images retained (acceptable for soft cancel) |
| 7 | `ret_order_item_stones` | Clean up stones | ❌ | Retained (acceptable for soft cancel) |

## CANCEL Checklist (Delete Order — Hard Delete)

| # | Table | Expected Cleanup | Status | Gap? |
|---|---|---|---|---|
| 1 | `customerorder` | DELETE | ✅ | — |
| 2 | `customerorderdetails` | DELETE | ✅ | — |
| 3 | `ret_order_advance_payment` | DELETE | ✅ | — |
| 4 | `customer_order_image` | DELETE | ❌ | **BUG**: Orphan rows |
| 5 | `ret_order_item_stones` | DELETE | ❌ | **BUG**: Orphan rows |
| 6 | `ret_order_other_charges` | DELETE | ❌ | **BUG**: Orphan rows |
| 7 | `ret_taging.id_orderdetails` | → NULL | ❌ | **BUG**: Tag still points to deleted order |
| 8 | Image files on disk | DELETE | ❌ | **BUG**: Disk leak |

## EDIT Checklist

| # | Table | Expected Update | Status | Gap? |
|---|---|---|---|---|
| 1 | Order items | UPDATE (not duplicate) | ⬜ | Per-item TX makes partial failure likely |
| 2 | Tag reservation | If tag changed: old tag freed, new tag reserved | ⬜ | **VERIFY** |
| 3 | Tax fields | Server-side validation | ❌ | **BUG**: Client-side only (BUG-005) |

## PRINT Checklist

| # | Field | Source | Status |
|---|---|---|---|
| 1 | Order Details | `customerorderdetails` | ⬜ |
| 2 | Advance Amount | `ret_order_advance_payment` | ⬜ |
| 3 | Delivery Bill Print | Same as sales_bill.md print | See sales_bill.md |

## REPORT Checklist

| # | Report | Source | Status |
|---|---|---|---|
| 1 | Pending Orders | `customerorderdetails.orderstatus < 5` | ⬜ |
| 2 | Advance Collection | `ret_order_advance_payment` | ⬜ |
| 3 | Delivery Report | `ret_billing` type=9 | ⬜ |

## Known Bugs Found

| Bug ID | Missing Step | Severity | Source |
|---|---|---|---|
| FR-CUSORD-005 | Cancel: zero-amount receipt row created | 🔴 HIGH | FLOW_RISK_MATRIX |
| FR-CUSORD-006 | Cancel: joborder status not restored | 🟡 MED | FLOW_RISK_MATRIX |
| FR-CUSORD-007 | Delete: orphan rows in images/stones/charges | 🔴 HIGH | FLOW_RISK_MATRIX |
| FR-CUSORD-008 | Delete: tag id_orderdetails not cleared | 🔴 HIGH | FLOW_RISK_MATRIX |
| BUG-005 | Tax fields client-side only — no server validation | 🔴 HIGH | FLOW_RISK_MATRIX |
| — | No guard on status 4→5 skip (delivered without completed) | 🟡 MED | FLOW_RISK_MATRIX |
| — | No guard on concurrent tag reservation (race condition) | 🔴 HIGH | FLOW_RISK_MATRIX |
