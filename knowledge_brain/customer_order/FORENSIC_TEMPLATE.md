# Forensic Template: Customer Order

> Layer-by-layer investigation guide for diagnosing bugs in the Customer Order module.

---

## Layer 1 — Symptom Collection

| Symptom Category | Questions to Ask |
|---|---|
| **Data Not Saving** | Does the order appear at all? Is it partially saved (header but no items)? Check browser Network tab for error response. |
| **Wrong Amounts** | Is order type Fixed or UnFixed rate? Is balance_type Metal or Cash? Which calc fields are wrong? |
| **Missing Items** | Were items added via Tag Reserve scan or manual add? Check `customerorderdetails` count vs UI. |
| **Image Issues** | Upload failure or display failure? Check `customer_order_image` table and `assets/img/orders/` directory. |
| **Status Not Updating** | Which status transition failed? Was OTP required? Check `orderstatus` in `customerorderdetails`. |
| **PDF/Print Wrong** | Which print view? Compare data in DB vs what the print view query returns. |
| **Karigar Assignment** | Was it in-house or outsource? Check `work_at` value and `assign_to` field. |

## Layer 2 — Reproduce & Isolate

1. **Get the order number** → query `customerorder` by `order_no`
2. **Identify order type** → `order_type` (1-5) determines which controller case handles it
3. **Identify current status** → `orderstatus` on `customerorderdetails`
4. **Check branch context** → `order_from` and user's session branch
5. **Reproduce**: 
   - Same branch, same order type
   - Try with a different customer
   - Try with different product/design combination

## Layer 3 — Client-Side Trace

| Check Point | How | What to Look For |
|---|---|---|
| Form data collected | `console.log(JSON.stringify($('#order_submit').serializeArray()))` | Missing `order[order_from]`, `order[order_to]` |
| AJAX request | Network tab → filter `admin_ret_order` | Status 200 but `{status: false}` |
| Hidden field values | Inspect `#order_id`, `#cus_id`, `#id_branch` | Empty or stale values |
| Item table state | `$('#item_detail tbody tr').length` | Row count matches expected items |
| Image payload size | Network tab → request size | Base64 images can exceed PHP `post_max_size` |

### Key JS Variables to Inspect
- `ctrl_page[1]` — route segment controlling which JS block runs
- `$('#order_id').val()` — blank = new order, populated = edit
- `$('input[name="order[order_type]"]:checked').val()` — 2=customized, 5=tag reserve

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Order not saving | `admin_ret_order.php` | `order('save')` | ~340-575 | `trans_begin()`/`trans_commit()` — look for rollback |
| Order not updating | `admin_ret_order.php` | `order('update')` | ~576-790 | `id_orderdetails` empty → insert instead of update |
| Repair save fail | `admin_ret_order.php` | `repair_order('save')` | 1447-1619 | Same transaction pattern |
| Assign fail | `admin_ret_order.php` | `assign_customer_order()` | 932-1003 | `req_status` value + `id_vendor`/`id_employee` |
| Cancel OTP fail | `admin_ret_order.php` | `send_order_cancel_otp()` | 2238-2294 | OTP table insert + session `Ordercancel_otp` |
| Cart checkout fail | `admin_ret_order.php` | `cart('order_place')` | 1893-2043 | Email sending block can silently fail |
| Image upload fail | `admin_ret_order.php` | `upload_img()` | 1150-1179 | GD library + `getimagesize()` returns FALSE |
| generateOrderNo | `ret_order_model.php` | `generateOrderNo()` | ~190-210 | Concurrent inserts → duplicate order numbers |
| Wrong order list | `ret_order_model.php` | `ajax_getOrders()` | ~160-264 | Filter parameters not matching |

## Layer 5 — Database Verification

```sql
-- 1. Full order snapshot
SELECT o.*, b.name as branch_name, c.firstname as customer
FROM customerorder o
LEFT JOIN branch b ON b.id_branch = o.order_from
LEFT JOIN customer c ON c.id_customer = o.order_to
WHERE o.order_no = '{ORDER_NO}';

-- 2. All items for an order
SELECT od.*, p.product_name, m.order_status as status_label
FROM customerorderdetails od
LEFT JOIN ret_product_master p ON p.pro_id = od.id_product
LEFT JOIN order_status_message m ON m.id_order_msg = od.orderstatus
WHERE od.id_customerorder = {ID};

-- 3. Check orphaned stones (stones without valid order item)
SELECT s.* FROM ret_order_item_stones s
LEFT JOIN customerorderdetails od ON od.id_orderdetails = s.order_item_id
WHERE od.id_orderdetails IS NULL;

-- 4. Check header vs detail weight mismatch
SELECT o.id_customerorder, o.order_pcs, o.order_approx_wt,
       SUM(od.totalitems) actual_pcs, SUM(od.weight) actual_wt
FROM customerorder o
JOIN customerorderdetails od ON od.id_customerorder = o.id_customerorder
GROUP BY o.id_customerorder
HAVING o.order_pcs != actual_pcs OR ROUND(o.order_approx_wt,2) != ROUND(actual_wt,2);

-- 5. Check images table integrity
SELECT i.* FROM customer_order_image i
LEFT JOIN customerorderdetails od ON od.id_orderdetails = i.id_orderdetails
WHERE od.id_orderdetails IS NULL;
```

## Layer 6 — Root Cause Classification

| Category | Sub-Category | Typical Risk |
|---|---|---|
| **Transaction** | Partial commit | HIGH — items saved but header rolled back |
| **Transaction** | Duplicate order number | MEDIUM — race condition in `generateOrderNo` |
| **Data Integrity** | Image orphans | LOW — unlinked files in filesystem |
| **Data Integrity** | Stone delete-reinsert | HIGH — stones lost during edit if transaction fails |
| **UI** | Hidden field stale | MEDIUM — `#cus_id` or `#id_branch` not populated |
| **Business Logic** | Rate type mismatch | HIGH — fixed vs unfixed rate applied incorrectly |
| **Integration** | Email send failure | LOW — blocks but doesn't fail cart checkout |

---

## Layer 7 — Transaction Integrity (Module-Specific)

For every save/update operation in this module:

1. **Verify wrapping**: Every `insertData`/`updateData` loop is inside `trans_begin()`/`trans_commit()`
2. **Check stone delete-reinsert**: In `order('update')`, stones are DELETED then re-inserted. If transaction fails after delete, stones are lost.
3. **Check image delete-reinsert**: Same pattern — `customer_order_image` rows deleted before reinsert in update flow.
4. **Verify email doesn't block**: In `cart('order_place')`, email sending runs INSIDE the transaction — a mail timeout could hold the DB transaction open.

## Layer 8 — Variant Isolation (Module-Specific)

For any reported bug:
1. First determine `order_type` (1-5) — this selects the entire code path
2. Then determine `work_at` (1=in-house, 2=outsource) — affects assignment logic
3. Then determine `rate_type` (1=fixed, 2=unfixed) — affects pricing
4. **Always test the same bug with a different order type** to isolate whether it's type-specific or universal
