# MODULE BRAIN — Purchase
> **Brain built:** 2026-03-20 | **Version:** 1.0 | **Builder:** Antigravity | **Status:** ✅ MAINTAINED

---

## 1. Module Overview

**Purpose:** Manages the entire procurement lifecycle from karigars (suppliers), including purchase orders, goods receipts (GRN), quality control (QC), hallmarking, rate fixing, and payments.

**Module type:** Transactional CRUD module with complex inventory and financial integrations.

### File Map
| File | Purpose |
|---|---|
| `admin/application/controllers/admin_ret_purchase.php` | **Primary controller** — Handles POs, payments, returns, and QC. |
| `admin/application/controllers/admin_ret_purchase_approval.php` | Handles approval stock and conversion to normal stock. |
| `admin/application/models/ret_purchase_order_model.php` | **Primary model** — Core SQL logic for POs and inventory. |
| `admin/application/models/ret_dashboard_api_model.php` | **Dashboard model** — Provides purchase analytics and overdue trackers. |
| `admin/application/views/ret_purchase/` | View templates for forms, lists, and receipts. |

---

## 2. Key Features & Business Logic

- **PO Workflow:** Draft → Approval → Metal Issue → GRN → QC → Hallmarking → Tagging.
- **Rate Fixing:** Ability to fix metal rates at different stages (Booked, Receipt, or Post-GRN).
- **Supplier Ledger:** Real-time tracking of pure metal and cash balances per karigar.
- **Dashboard Trackers:** "Today Customizable PO List" and "Delayed PO Tracker" for operational oversight.

---

## 3. Critical Fixes & Knowledge Capture

### [PUR-CLT01] Dashboard "Today Payable PO List" Missing Records
- **Issue:** POs due today were not showing if they lacked a category or were not specifically "pur_approval_type = 1".
- **Fix:** Removed the `pur_approval_type = 1` restriction and ensured the `LEFT JOIN ret_category` doesn't filter out pending items with NULL categories.
- **Pattern:** Avoid strict equality filters on optional metadata (like category) in dashboard summary queries.

### [PUR-CLT04] getPending_payment_po_bills_for_payment Returns Empty
- **Issue:** Payment page showed no pending bills — controller function was missing `$model` init and model method call (copy-paste oversight from neighboring function).
- **Fix:** Added `$model = self::RET_PUR_ORDER_MODEL;` and `$list = $this->$model->getPending_payment_po_bills_for_payment($_POST);`.
- **Pattern:** Always verify controller functions have model initialization and actual model calls — especially when functions are added near existing ones.

### [ORD-CLT01] Rejected Karigar Order Not Reflected Back to Cart ✅ FIXED
- **Issue:** When Karigar PO was cancelled/rejected, `order_cart.orderstatus` stayed at `1` — items never reappeared in cart for reassignment.
- **Fix:** Added `order_cart` reset (`orderstatus=0`, `id_orderdetails=NULL`) in `update_order_cancel()` (Purchase controller L8497) and `update_order_rejection()` (Order model L2304).
- **Pattern:** Always reset linked upstream tables when cancelling a downstream record. Check all foreign-key-like links in cancel/reject flows.


---

## 4. Anti-Patterns Register

| Pattern | Description | Impact |
|---|---|---|
| Strict Filter Bias | Over-filtering summary queries (e.g., by approval type) causing data gaps. | High |
| NULL Join Filtering | Using joins that exclude records with incomplete metadata on dashboards. | High |
| Copy-Paste Model Omission | Controller function body copied but `$model` init + model call lines omitted, leaving `$list` undefined. | Critical |
| Missing Cascade on Cancel | Cancel/reject flow updates downstream table but forgets to reset linked upstream table (`order_cart.orderstatus`). Items become stuck. | High |

> See [BUG_DISCOVERY.md](BUG_DISCOVERY.md) for full anti-pattern details.
