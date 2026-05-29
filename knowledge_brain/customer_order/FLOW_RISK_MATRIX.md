# Flow Risk Matrix: Customer Order

> **QA-ready flow contracts, state machine, cancellation verification, and test scenarios.**
> Built: Round 16 (2026-03-24)
> Sources: TRANSACTION_TRACE.md (15 TX blocks), ORDER_STATE_MACHINE.md, CROSS_MODULE_MAP.md

---

## 3b-1. State Machine: `customerorderdetails.orderstatus`

| State | Value | Set By (Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Cart / Draft | `0` | `order('save')` type≠5 | Placed(1), Cart-Rejected(2), Assigned(3 via cart_place), Cancelled(6) | — |
| Placed | `1` | `order('save')` type≠5 default | Assigned(3), Cancelled(6) | — |
| Cart Rejected | `2` | `cart('order_place')` reject path | — (terminal) | — |
| Assigned (WIP) | `3` | `assign_customer_order()`, `cart('order_place')` approve | Completed(4), Cancelled(6), Reject-After-Assign(8) | — |
| Completed | `4` | `repair_order_status()`, `update_repair_order_other_details()`, `order('save')` type=5 | Delivered(5), Cancelled(6) | ⚠️ NO GUARD — sets 4 from any state |
| Delivered | `5` | `repair_deliver_order_status()` | — (terminal) | ⚠️ NO GUARD — sets 5 from any state |
| Cancelled | `6` | `ajax_order_cancel()`, `cancel_order_item()`, `assign_customer_order()` reject | — (terminal) | — |
| Reject-After-Assign | `8` | `updatereject_reason()`, `assign_customer_order()` reject path | — (terminal) | — |

> **Header status**: `customerorder.order_status` uses only `0` (active) / `6` (cancelled). Out-of-sync with item statuses is a known risk — no enforcement.

### State Machine Gaps
| Gap | Risk |
|---|---|
| Status 4 set without checking status=3 pre-condition | `repair_deliver_order_status()` can move delivered without completion |
| Status 5 set without checking status=4 pre-condition | Order shows Delivered without ever being Completed |
| Tag Reserve (type=5): 0→4, skipping 1,3 | Bypasses karigar assignment flow entirely |
| Header `order_status=6` but items may have `orderstatus=3/4` | Cancelling after partial completion leaves orphan work states |

---

## 3b-2. Inbound Contracts (What This Module Expects from Upstream)

| Upstream Module | Data / State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| **Tagging** | `ret_taging` record exists with matching `id` | NO check | — | Save links to ghost tag — order detail has dangling FK |
| **Tagging** | Tag not already reserved by another order | NO check | — | Race condition: 2 orders claim same tag simultaneously (see CROSS_MODULE_MAP #8) |
| **Billing** | No active billing against this order's items | NO check | — | Order update can change items already billed |
| **Estimation** | `customerorder.est_id` refers to valid estimation | NO check | L~140 | Estimation reference silently stores invalid ID |
| **Product** | `id_product` exists in `ret_product_master` | FK constraint only | — | DB error on save if orphan product ID supplied |
| **Customer** | `customer.id_customer` valid | FK constraint only | — | DB error on save if invalid customer ID |
| **Branch Day Closing** | Branch is open (day-closing not locked) | PARTIAL — `getBranchDayClosingData()` called | L~20 in construct | Orders created on closed-day dates if check fails silently |

---

## 3b-3. Outbound Contracts (What This Module Guarantees to Downstream)

| Downstream Module | What This Module Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| **Billing** | `customerorder.est_id` refers to a billable order | No explicit check | Double billing possible if est_id reused |
| **Karigar Ack PDF** | `get_karigar_orders($id)` returns valid order items | FK-only | PDF renders blank if order items deleted |
| **Email / Cart Accept** | Token in `ret_order_email_logs` is unique per order | No uniqueness constraint documented | Stale tokens can accept already-processed orders (BUG-023) |
| **Tagging** | On cancel, `ret_taging.id_orderdetails = NULL` (tag freed) | ✅ YES — `ajax_order_cancel` L665, `cancel_order_item` L731 | — |
| **Tagging** | On repair update (type=4), `tag_status=8` set | ⚠️ PARTIAL — only on UPDATE, not on SAVE (L1529-1531 save omits status change) | Tag appears available while repair order is open |
| **Job Order** | On cancel, `joborder.orderstatus` restored | ❌ NO — cancel flow does NOT update joborder | Job order shows stale WIP status after order cancel |
| **Reports** | Order totals are server-validated | ❌ NO — tax fields (SGST/CGST/IGST) are client-side only | Reports show tampered tax values |
| **Billing / Receipt** | On cancel with advance, `ret_issue_receipt` row created with `advance_amount` | ⚠️ PARTIAL — logic present (L667-695) but guard is always truthy (AP-11) | Zero-amount receipts created for orders with no advance |

---

## 3b-4. Reversal Contracts (Cancel / Delete / Reverse)

### Cancel Entire Order (`ajax_order_cancel`, L656-721)

| Table to Restore | Restored? | Method & Line | Gap? |
|---|---|---|---|
| `customerorder.order_status → 6` | ✅ YES | L661 | — |
| `customerorderdetails.orderstatus → 6` | ✅ YES | L662 | — |
| `customerorderdetails.reject_reason` set | ✅ YES | L661-662 | — |
| `ret_taging.id_orderdetails → NULL` (tag freed) | ✅ YES | L665 | — |
| `ret_issue_receipt` row created (advance refund) | ⚠️ PARTIAL | L695 | AP-11: guard `if($order_advance > 0)` always truthy — inserts zero-amount row even when advance=0 |
| `joborder.orderstatus` restored | ❌ NO | — | Job order stays in stale WIP state after cancel |
| `customer_order_image` cleaned up | ❌ NO (not needed) | — | Images retained (soft cancel, not hard delete) |
| `ret_order_item_stones` cleaned up | ❌ NO (not needed) | — | Stones retained |

### Cancel Single Item (`cancel_order_item`, L723-761)

| Table to Restore | Restored? | Method & Line | Gap? |
|---|---|---|---|
| `customerorderdetails.orderstatus → 6` | ✅ YES | L726 | — |
| `customerorderdetails.reject_reason` set | ✅ YES | L726 | — |
| `customerorderdetails.cancelled_by`, `order_cancelled_date` set | ✅ YES | L726 | — |
| `ret_taging.id_orderdetails → NULL` | ✅ YES | L731 | Only if `get_tagorder_details()` returns a match |
| `customerorder.order_status` update | ❌ NO | — | Header stays open even if all items are cancelled |
| Advance reversal | ❌ NO | — | Item-level cancel does not trigger advance refund calculation |

### Delete Order (`order('delete')`, L386-419)

| Table to Restore | Restored? | Method & Line | Gap? |
|---|---|---|---|
| `customerorder` row deleted | ✅ YES | L388 | — |
| `customerorderdetails` rows deleted | ✅ YES | L389 | — |
| `ret_order_advance_payment` rows deleted | ✅ YES | L390 | — |
| `customer_order_image` rows | ❌ NO | — | ⚠️ Orphan rows in image table |
| `ret_order_item_stones` rows | ❌ NO | — | ⚠️ Orphan rows in stones table |
| `ret_order_other_charges` rows | ❌ NO | — | ⚠️ Orphan rows in charges table |
| Image files on disk | ❌ NO | — | ⚠️ Disk leak |
| `ret_taging.id_orderdetails → NULL` | ❌ NO | — | ⚠️ Tag still points to deleted order detail |

---

## 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-CUSORD-001 | Save order with a tag already reserved by another open order | REJECT with "tag already in use" error | 🔴 HIGH | ❌ |
| FR-CUSORD-002 | Save order with a deleted tag ID | REJECT with DB error or validation message | 🔴 HIGH | ❌ |
| FR-CUSORD-003 | Update order items that have already been billed | REJECT or WARN user before allowing edit | 🔴 HIGH | ❌ |
| FR-CUSORD-004 | Cancel order with advance → verify `ret_issue_receipt` row created with correct amount | Receipt row exists, `advance_amount > 0` | 🔴 HIGH | ❌ |
| FR-CUSORD-005 | Cancel order with **zero advance** → verify no `ret_issue_receipt` row created | No receipt row should be inserted | 🔴 HIGH | ❌ — AP-11 means this currently FAILS |
| FR-CUSORD-006 | Cancel order → verify `joborder.orderstatus` is restored | joborder status reflects cancelled state | 🟡 MED | ❌ — known gap |
| FR-CUSORD-007 | Delete order → verify no orphan rows in `customer_order_image`, `ret_order_item_stones`, `ret_order_other_charges` | All child rows deleted | 🔴 HIGH | ❌ — known gap |
| FR-CUSORD-008 | Delete order → verify tag `id_orderdetails` is cleared to NULL | Tag freed after order deletion | 🔴 HIGH | ❌ — known gap |
| FR-CUSORD-009 | Cart-to-order email sent → cancel order → try to click accept URL again | URL should be rejected (order cancelled) | 🟡 MED | ❌ — token not single-use (BUG-023) |
| FR-CUSORD-010 | Repair save (type=4) → verify `tag_status` change | `tag_status` should be set (e.g. =8) on save | 🟡 MED | ❌ — only set on update, not save |
| FR-CUSORD-011 | Set repair to Completed (status=4) from status=1 (skipping assignment) | Should REJECT — must be in status=3 first | 🟡 MED | ❌ — NO GUARD |
| FR-CUSORD-012 | Set repair to Delivered (status=5) from status=1 (skipping Completed) | Should REJECT — must be in status=4 first | 🟡 MED | ❌ — NO GUARD |
| FR-CUSORD-013 | Tag Reserve order (type=5) save → verify karigar assignment is skipped | Status=4 immediately, no assignment email | 🟢 LOW | ❌ — by design, but needs verification |
| FR-CUSORD-014 | Concurrent save on same tag from 2 browser tabs | One succeeds, one gets error | 🔴 HIGH | ❌ — no lock |
| FR-CUSORD-015 | DB crash midway through `order('update')` per-item TX (simulate by killing DB) | No partial item updates, no orphan images/stones | 🔴 HIGH | ❌ — per-item TX makes this likely |
| FR-CUSORD-016 | Tax fields (SGST/CGST/IGST) tampered in POST before save | Server should reject/recalculate | 🔴 HIGH | ❌ — client-side only (BUG-005) |
| FR-CUSORD-017 | OTP cancel after 5 minutes — does it still verify? | Should expire | 🟡 MED | ❌ — no server-side OTP expiry (BUG-022) |
| FR-CUSORD-018 | Cancel + re-create same order (cancel-then-recreate cycle) | Should work normally | 🟡 MED | ❌ |

---

## Known Contract Gaps Summary

| Gap Category | Count | Severity |
|---|---|---|
| Missing precondition guard (state transitions) | 2 | 🟡 MED |
| Missing reversal step on cancel/delete | 5 | 🔴 HIGH |
| Client-side-only validation (no server check) | 2 | 🔴 HIGH |
| Race condition (no locks) | 2 | 🔴 HIGH |
| Partial implementation (bug in logic) | 2 | 🔴 HIGH |
| Token/OTP lifecycle violations | 2 | 🟡 MED |
| **Total contract gaps** | **15** | — |

> Cross-reference: See [BUG_CANDIDATES.md](./BUG_CANDIDATES.md) for full bug list | [TRANSACTION_TRACE.md](./TRANSACTION_TRACE.md) for TX-level detail
