# Business Rules: Customer Order

---

## Order Lifecycle Rules

*RULE-CUSORD-001: Order Type Identification*
- **Formula**: Type 1 = Stock Order, Type 2 = Customer Order (Customized), Type 3 = Customer Repair, Type 4 = Stock Repair, Type 5 = Tag Reserve, Type 6 = Home Bill Order
- **Implementation**: Controller `ajax_getOrders()` → `IF(order_type=1,'Stock Order',IF(order_type=2,'Customer Order',...))` at L301
- **Validation**: Server-side via SQL `IF()` chain. UI radios in `form.php` lines 421-427.
- **Edge cases**: Type 6 (Home Bill) is commented out in form UI but still recognized in SQL

*RULE-CUSORD-002: Order Number Generation Format*
- **Formula**: `{BranchShortName}{FinYearCode}-{TypeCode}-{5-digit seq}` 
- **Type Codes**: 2→`OR`, 5→`RO`, 6→`HO`, 3→`RE`, 4→`SRE`
- **Implementation**: `ret_order_model::generateOrderNo()` at L138-202
- **Validation**: Server-side only. No duplicate protection (race condition risk)
- **Edge cases**: If branch has no `short_name` set, order number starts with just the year code

*RULE-CUSORD-003: Due Date Auto-Calculation*
- **Formula**: `cus_due_date = today + customer_due_date` (from ret_settings), `smith_due_date = today + (customer_due_date - 1)`, `smith_remainder_date = today + (customer_due_date - 3 - karigar_due_date)`
- **Implementation**: `ret_order_model::empty_rec_order()` at L233-235
- **Validation**: Server-side only. Settings keys: `customer_due_date`, `karigar_orderalert_remain_days`
- **Edge cases**: If settings not configured, dates collapse to today

---

## Financial / Rate Rules

*RULE-CUSORD-004: Rate Type Selection*
- **Formula**: Rate Type 1 = Order Rate (Fixed at creation), Rate Type 2 = Delivery Rate (calculated at delivery from current metal rate)
- **Implementation**: Radio `order[rate_type]` in `form.php` L433-434. Stored in `customerorderdetails.rate`
- **Validation**: Client-side radio selection only
- **Edge cases**: If rate type changed during edit, the old rate is overwritten

*RULE-CUSORD-005: Balance Type*
- **Formula**: Balance Type 1 = Metal Balance (tracked in weight), Type 2 = Cash Balance (tracked in currency)
- **Implementation**: Radio `order[balance_type]` in `form.php` L357-363
- **Validation**: Client-side only

*RULE-CUSORD-006: Making Charge (MC) Calculation*
- **Formula**: If `id_mc_type=1` → "Per Gram" (mc × weight), If `id_mc_type=2` → "Piece" (mc × pieces)
- **Implementation**: `getOrderDetails()` L347, displayed via JS item table
- **Validation**: Server-side in model queries

*RULE-CUSORD-007: Tax Calculation (GST)*
- **Formula**: Tax % retrieved via `getAvailableTaxGroupItems()` using product's `tgrp_id`. IGST if `cus_state ≠ cmp_state`, else CGST+SGST split
- **Implementation**: Hidden fields `#cus_state`, `#cmp_state` in form.php L237-241
- **Validation**: Server-side in billing module, client-side comparison for tax type selection

---

## Status Transition Rules

*RULE-CUSORD-008: Cancellation Requires OTP*
- **Formula**: Order cancellation generates 6-digit OTP → sent to admin mobile via SMS → user must verify within 300 seconds
- **Implementation**: `send_order_cancel_otp()` L2238-2294, `verify_order_cancel_otp()` L2295-2336
- **Validation**: Server-side OTP verification against `otp` table
- **Edge cases**: OTP expiry not explicitly enforced in verify — only checks match, not timestamp

*RULE-CUSORD-009: Vendor Email Acceptance Flow*
- **Formula**: On cart order place → email sent to vendor with tokenized link → vendor clicks accept/reject → status updated
- **Implementation**: `cart('order_place')` → `save_email_log()` → external `OrderAccept` controller → `update_order_acceptance()` / `update_order_rejection()`
- **Validation**: Token-based (no login required for vendor)
- **Edge cases**: Token can be reused — no single-use enforcement

*RULE-CUSORD-010: Karigar Assignment*
- **Formula**: If `req_status=1` → assign karigar (set `id_karigar`, `orderstatus=3`, due dates). If `req_status≠1` → reject (`orderstatus=6`)  
- **Implementation**: `assign_customer_order()` L932-1003
- **Validation**: Server-side status update

---

## Cart / Shortage Rules

*RULE-CUSORD-011: Cart Shortage/Excess Calculation*
- **Formula**: `shortage = max(0, min_pcs - total_available)`, `excess = max(0, total_available - max_pcs)` where `total_available = tagged + stock`
- **Implementation**: `ajax_getCartOrders()` L981-1087, uses `ret_reorder_settings` for min/max thresholds
- **Validation**: Server-side calculation displayed in cart UI columns

---

## Data Integrity Rules

*RULE-CUSORD-012: Stone Delete-Reinsert on Edit*
- **Formula**: When updating order items, ALL stones for the item are DELETED then re-inserted fresh
- **Implementation**: `order('update')` switch case → `deleteData('order_item_id', ...)` on `ret_order_item_stones`
- **Risk**: HIGH — if transaction fails after delete, stones are permanently lost

*RULE-CUSORD-013: Image Storage Pattern*
- **Formula**: Images stored as files in `assets/img/orders/`, file names stored in `customer_order_image` table linked by `id_orderdetails`
- **Implementation**: `upload_orderimg()` L1126, `base64ToFile()` for webcam capture
- **Edge cases**: Legacy orders may have `#`-delimited image strings in `customerorderdetails.image` column instead of using the image table
