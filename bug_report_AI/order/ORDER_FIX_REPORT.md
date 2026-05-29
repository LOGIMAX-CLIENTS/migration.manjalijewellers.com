# Order Module — Fix Report

---
### Fix: ORD-CLT01 — Rejected Karigar Order Not Reflected Back to Cart for Reassignment
- **Date:** 2026-03-26
- **Track:** A (System)
- **Category:** Logic
- **Severity:** P1
- **Files Changed:** `admin/application/controllers/admin_ret_purchase.php` (L8497-8504), `admin/application/models/ret_order_model.php` (L2304-2312)
- **Root Cause:** PO cancel/reject flows updated `customerorder` and `customerorderdetails` but never reset `order_cart.orderstatus` back to `0` — items stayed stuck at `1`, invisible in cart
- **Fix Applied:** Added `order_cart` reset (`orderstatus=0`, `id_orderdetails=NULL`) in both `update_order_cancel()` (Purchase controller) and `update_order_rejection()` (Order model)
- **Tests:** PHP syntax check PASS. Manual verification pending.
- **Pattern:** NEW — PAT-CASCADE-001 (Missing Cascade on Cancel/Reject)
- **Rollback:** Remove the `order_cart` reset blocks (8 lines each) from both files
---