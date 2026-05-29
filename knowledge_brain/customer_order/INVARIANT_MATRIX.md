# Invariant Matrix: Customer Order

> This module has **config-driven behavior** based on three key dimensions: Order Type, Order Status, and Work Location.

---

## Dimension 1: Order Type (`order_type`)

| Value | Label | `order_for` | Flow | Controller Case |
|---|---|---|---|---|
| 1 | Stock Order | 1 (Company) | Cart → `order_place` → karigar assignment | `cart('order_place')` |
| 2 | Customer Order (Customized) | 2 (Customer) | Form → `order('save')` | `order('save')` |
| 3 | Customer Repair | 2 (Customer) | Repair form → `repair_order('save')` | `repair_order('save')` |
| 4 | Stock Repair | 1 (Company) | Repair form → `repair_order('save')` + tag link | `repair_order('save')` |
| 5 | Tag Reserve | 2 (Customer) | Form with tag scan → `order('save')` | `order('save')` |

## Dimension 2: Order Status (`orderstatus` on `customerorderdetails`)

| Value | Label | Can Transition To | Set By |
|---|---|---|---|
| 0 | In Cart | 1, 2 | `add_to_cart()` |
| 1 | Order Placed | 3, 6 | `cart('order_place')` |
| 2 | Rejected (Cart) | — (terminal) | `cart('order_place')` with status=0 |
| 3 | Work in Progress / Assigned | 4, 8 | `assign_customer_order()`, `update_order_acceptance()` |
| 4 | Completed / Ready | 5 | `repair_order_status()`, `update_repair_order_other_details()` |
| 5 | Delivered | — (terminal) | `repair_deliver_order_status()` |
| 6 | Rejected (New Order) | — (terminal) | `assign_customer_order()` with `req_status!=1`, `update_order_rejection()` |
| 8 | Rejected After Assignment | — (terminal) | `updatereject_reason()` |

## Dimension 3: Work Location (`work_at`)

| Value | Label | Effect |
|---|---|---|
| 1 | In-House | Karigar assigned from `ret_karigar`; `smith_due_date` on `customerorderdetails` |
| 2 | Out-Source | Creates a separate purchase order; `smith_due_date` on the outsourced `customerorderdetails` row |

## Dimension 4: Rate Type (`rate_type`)

| Value | Label | Effect |
|---|---|---|
| 1 | Order Rate (Fixed) | Rate locked at order creation time |
| 2 | Delivery Rate (UnFixed) | Rate calculated at delivery time from current metal rate |

## Dimension 5: Balance Type (`balance_type`)

| Value | Label | Effect |
|---|---|---|
| 1 | Metal Balance | Customer balance tracked in metal weight |
| 2 | Cash Balance | Customer balance tracked in currency |

---

## Behavior Grid: Order Type × Status Transitions

| | In Cart(0) | Placed(1) | Rejected-Cart(2) | Assigned(3) | Completed(4) | Delivered(5) | Rejected-New(6) | Rejected-Assigned(8) |
|---|---|---|---|---|---|---|---|---|
| **Stock Order (1)** | ✅ via cart | ✅ | ✅ | ✅ via karigar | ✅ | ✅ | ✅ | ✅ |
| **Customer Order (2)** | ❌ | ❌ direct save | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Customer Repair (3)** | ❌ | ❌ direct save | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Stock Repair (4)** | ❌ | ❌ direct save | ❌ | ✅ + tag_status=8 | ✅ | ✅ | ✅ | ✅ |
| **Tag Reserve (5)** | ❌ | ❌ direct save | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |

> ⚠️ **Risk**: Stock Order (type 1) is the only type that flows through the Cart system. All other types bypass cart directly to `orderstatus=1` or higher.
