# Order Lifecycle State Machine: Customer Order

> Formal transition diagram and rules for `orderstatus` in `customerorderdetails`.

---

## States

| Value | Name | Meaning | Terminal? |
|---|---|---|---|
| `0` | **Cart / Draft** | Item in cart, not yet an order | No |
| `1` | **Order Placed** | Confirmed order, awaiting assignment | No |
| `2` | **Cart Rejected** | Cart item rejected by HO (stock order only) | Yes |
| `3` | **Assigned (WIP)** | Assigned to karigar / vendor | No |
| `4` | **Completed** | Work done, ready for delivery | No |
| `5` | **Delivered** | Physically delivered to customer/branch | Yes |
| `6` | **Cancelled** | Rejected after placement (OTP flow) | Yes |
| `8` | **Reject-After-Assign** | Rejected after karigar assignment | Yes |

---

## Transition Map

```
                        ┌─────────┐
                        │  CART   │
                        │    0    │
                        └────┬────┘
                             │  cart('order_place') / assign_customer_order()
                    ┌────────┴────────┐
                    ▼                 ▼
            ┌─────────────┐    ┌──────────┐
            │  PLACED     │    │CART REJ  │
            │      1      │    │    2     │ ← Terminal
            └──────┬──────┘    └──────────┘
                   │  assign_customer_order(req_status=1)
                   │  or order('save') type=5 [Tag Reserve → skip to 4]
                   ▼
            ┌─────────────┐
            │  ASSIGNED   │
            │      3      │
            └──────┬──────┘
                   │  repair_order_status()
                   │  update_repair_order_other_details()
                   ▼
            ┌─────────────┐
            │  COMPLETED  │
            │      4      │
            └──────┬──────┘
                   │  repair_deliver_order_status()
                   ▼
            ┌─────────────┐
            │  DELIVERED  │
            │      5      │ ← Terminal
            └─────────────┘

  From any state < 6:
  ┌─────────────────────────────────────┐
  │ ajax_order_cancel()                 │ → orderstatus = 6 (CANCELLED) ← Terminal
  │ cancel_order_item()                 │
  └─────────────────────────────────────┘

  From state 3 (Assigned):
  ┌─────────────────────────────────────┐
  │ updatereject_reason()               │ → orderstatus = 8 (REJECT-AFTER-ASSIGN) ← Terminal
  │ assign_customer_order(req_status≠1) │
  └─────────────────────────────────────┘
```

---

## Transition Table (Method → Status Change)

| Method | File | Condition | From | To | Notes |
|---|---|---|---|---|---|
| `cart('order_place')` status=1 | Controller | Approve | 0 | 3 | Direct cart→assigned (skips 1) |
| `cart('order_place')` status≠1 | Controller | Reject | 0 | 2 | Cart rejected |
| `order('save')` type≠5 | Controller | New save | — | 1 | `orderstatus` default=0, set to 1 |
| `order('save')` type=5 | Controller | Tag Reserve | — | 4 | **Skips 1,3 — jumps to Completed** |
| `assign_customer_order` req_status=1 | Controller | Assign | 1 | 3 | Sets karigar/vendor |
| `assign_customer_order` req_status≠1 | Controller | Reject | 1 | 6 | `orderstatus=6` |
| `updatereject_reason` | Controller | Reject reason | 3 | 8 | Reject after assignment |
| `repair_order_status` | Controller | Work done | 3 | 4 | Sets `completed_weight`, `rate` |
| `update_repair_order_other_details` | Controller | Sub-details | 3 | 4 | Same, with metal breakdown |
| `repair_deliver_order_status` | Controller | Delivery | 4 | 5 | Sets `delivered_date` |
| `ajax_order_cancel` | Controller | Cancel order | any | 6 | Header + all items |
| `cancel_order_item` | Controller | Cancel item | any | 6 | Single item only |

---

## Header Status (`customerorder.order_status`)

| Value | Meaning | Set By |
|---|---|---|
| `0` | Active / Open | Default on INSERT |
| `6` | Cancelled | `ajax_order_cancel()` |

> Note: Header `order_status` and item `orderstatus` can be **out of sync** — there is no enforcement that all items must be in terminal state before header is closed.

---

## State Invariant Violations (Known Bugs)

| Violation | Bug ID | Detail |
|---|---|---|
| Tag Reserve (type=5) jumps 0→4, skipping assignment | BUG-CUSORD-021 | L272-273 — status set to 4 immediately |
| Status 4 (Completed) set without checking status=3 pre-condition | BUG-CUSORD new | `repair_order_status` sets 4 from any state |
| Status 5 (Delivered) set without checking status=4 pre-condition | BUG-CUSORD new | `repair_deliver_order_status` sets 5 from any state |
| Header cancelled (status=6) but items may still be in status=3/4 | MEDIUM | `ajax_order_cancel()` does update all items, but items may have been partially updated |
