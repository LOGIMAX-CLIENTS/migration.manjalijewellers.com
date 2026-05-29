## ORD-CLT01 — Rejected Karigar Order Not Reflected Back to Cart for Reassignment

| Field         | Value                                          |
| ------------- | ---------------------------------------------- |
| Severity      | P1                                             |
| Track         | A (System)                                     |
| Category      | Logic                                          |
| Sprint        | Sprint 1                                       |
| Pattern Match | None (Novel — Missing Cascade on Cancel)       |
| Module Brain  | ✅ Ready                                       |
| Reporter      | Client                                         |
| Source        | Client                                         |

⛔ DANGER ZONE: Status transitions (`orderstatus`)
→ AI confidence: automatically LOW
→ Require senior developer review before applying any fix

### Steps to Reproduce

1. Go to `/admin_ret_order/cart/list` and select cart items
2. Assign selected items to a Karigar and place order
3. Cart item `orderstatus` changes from `0` to `1` and disappears from cart
4. PO is created in `customerorder` with `order_type=1` and `pur_no`
5. Navigate to Purchase → PO list, cancel the PO (or Karigar rejects via email)
6. PO status changes to `6` (Cancelled)
7. Navigate back to `/admin_ret_order/cart/list`

### Expected Behavior

The rejected/cancelled cart items should reappear in the cart list (`orderstatus` reset to `0`) so they can be reassigned to another Karigar.

### Actual Behavior

The cart items remain with `orderstatus=1`, invisible in the cart list. They are permanently stuck and cannot be reassigned.

### Root Cause

The cart list query in `ajax_getCartOrders()` (`ret_order_model.php` L982) filters `WHERE o.orderstatus=0`.

When an order is placed from cart, `order_cart.orderstatus` is set to `1` (L1949 of `admin_ret_order.php`).

When the PO is cancelled, two code paths handle it:
1. **`update_order_cancel()`** in `admin_ret_purchase.php` (L8475) — cancels `customerorder` and resets `customerorderdetails`, but **never touches `order_cart`**
2. **`update_order_rejection()`** in `ret_order_model.php` (L2293) — cancels via email rejection, but **never touches `order_cart`**

Neither path resets `order_cart.orderstatus` back to `0` or clears the `id_orderdetails` link.

### Evidence

- Cart list query: `ret_order_model.php` L1002 — `WHERE o.orderstatus=0`
- Cart status set to 1: `admin_ret_order.php` L1949
- PO cancel (no cart reset): `admin_ret_purchase.php` L8475-8498
- Email rejection (no cart reset): `ret_order_model.php` L2293-2319