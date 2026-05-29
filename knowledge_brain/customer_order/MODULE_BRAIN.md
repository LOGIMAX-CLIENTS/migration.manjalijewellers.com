# Customer Order Module Brain

> **🔄 REFRESHED — Round 15**
> Previous: R14 (2026-03-14) — 19 files, MASTER status
> Refreshed: 2026-03-24
> Changes: 1 method discovered (`taxGroupItems()`), 1 routing correction (`neworders()`), `ajax_order_cancel` advance-reversal logic documented, AP-11 added


## 1. Module Overview
- **Purpose**: Manages the creation, updating, routing, and tracking of customer orders, repair orders, and catalog orders from initial entry up to delivery or allocation to karigars (vendors).
- **Controller**: `admin/application/controllers/admin_ret_order.php` (2379 lines, 58 methods)
- **Model**: `admin/application/models/ret_order_model.php` (2321 lines, 112 methods)
- **JS**: `admin/assets/js/ret_reports.js` (shared script handling AJAX requests and UI logic)
- **Views**: `admin/application/views/order/` (7 files, 4 subdirectories including `neworder`, `repair_order`, `stock_order`, etc.)

**Connection Flow:**
```text
Browser → JS (ret_reports.js)
       → AJAX → Controller (admin_ret_order.php)
       → Model (ret_order_model.php)
       → DB (customerorder, customerorderdetails, etc.)
       → View (order/*)
       → Browser
```

## 2. Constructor Analysis (`__construct()`)
| Model/Library | Purpose |
|---|---|
| `ret_order_model` | Primary model for order database interactions |
| `admin_settings_model` | Fetching company settings, access permissions, and configurations |
| `ret_billing_model` | Billing and tax calculations |
| `admin_usersms_model` | Sending SMS notifications (e.g., OTPs) |
| `log_model` | Logging user activity and system events |
| Session Validation | Validates `id_log`, `uid`, `session_id`, and branch access-time restrictions |

## 3. Entry Points (Routes)
| URL Path | HTTP Method | Controller Method | Purpose |
|---|---|---|---|
| `/admin_ret_order/index` | GET | `index()` | Loads default view (new order) |
| `/admin_ret_order/order/{type}` | GET/POST | `order()` | Main dispatcher (list, add, save, edit, delete, update) for customer orders |
| `/admin_ret_order/repair_order/{type}` | GET/POST | `repair_order()` | Main dispatcher for repair orders (add, save, update) |
| `/admin_ret_order/customer_neworders` | GET | `customer_neworders()` | Loads new orders list |
| `/admin_ret_order/cart/{type}` | GET/POST | `cart()` | Cart operations (add, status, order_place) |
| `/admin_ret_order/assign_customer_order`| POST | `assign_customer_order()`| Assign orders to karigars or employees |
| `/admin_ret_order/get_img_by_order_id` | POST | `get_img_by_order_id()` | Fetch order images |
| `/admin_ret_order/vendor_acknowladgement`| GET | `vendor_acknowladgement()`| Generate vendor PDF acknowledgement |
| `/admin_ret_order/taxGroupItems` | POST | `taxGroupItems()` | ⭐ R15 — AJAX: returns tax group items by `tgrp_id` (calls model `getAvailableTaxGroupItems`) |

## 4. Model Methods Summary
- **Count**: 112 methods
- **Structure**: Extensive use of `insertData()` and `updateData()` wrappers. Complex methods like `get_repair_orders_list()` and `getCartDetails()` with huge joins across `customerorder`, `customerorderdetails`, `ret_product_master`, `ret_karigar`, etc.
- Detailed listing in [METHOD_INDEX.md](./METHOD_INDEX.md)

## 5. Data Flow Summary
1. **CREATE (Save New Record)**: User submits order. Generated `order_no` and saved via transaction to `customerorder` (header) and `customerorderdetails` (lines). Images optionally uploaded via base64 encoded strings in `customer_order_image`.
2. **EDIT**: Existing ID passed, data retrieved via joined queries, saved with a mix of `updateData()` for main records and delete-insert for child records (like stones or images).
3. **DELETE/REJECT**: Mostly status updates (e.g., `orderstatus=6` for rejected/cancelled) rather than hard row deletions.

## 6. Key Tables
| Table | Purpose | Main Columns |
|---|---|---|
| `customerorder` | Order Header | `id_customerorder`, `order_no`, `order_type`, `order_from` |
| `customerorderdetails` | Order Lines/Items | `id_orderdetails`, `id_customerorder`, `id_product`, `weight`, `rate` |
| `customer_order_image` | Image attachments | `id_image`, `id_orderdetails`, `image` |
| `order_cart` | Pending cart items | `id_cart_order`, `id_product`, `totalitems`, `orderstatus` |
| `ret_issue_receipt` | ⭐ R15: **Also written** by `ajax_order_cancel` when advance > 0 | `type=2`, `receipt_type=5` — advance transfer receipt on order cancel |

## 7. Form Sections — Hidden Field Inventory (`form.php`, 1722 lines)

### 7a. Order Header Hidden Fields
| DOM ID | `name` Attribute | Source / Purpose |
|---|---|---|
| `order_id` | `order[order_id]` | `$order['id_customerorder']` — blank=new, populated=edit |
| `cus_id` | `order[order_to]` | Customer ID (set by autocomplete) |
| `cus_order` | `order[order_no]` | Generated order number |
| `allow_bill_type` | — | Controls billing type visibility |
| `is_eda` | `order[is_eda]` | Always `1` — EDA flag |
| `id_branch` | `order[order_from]` | Branch ID (session or dropdown) |
| `id_order_to_br` | `order[id_branch]` | Branch "Order For" destination |
| `id_employee` | `order[order_taken_by]` | Employee who took the order |
| `is_eda_tax_calc` | `order[is_eda_tax_calc]` | EDA tax calculation toggle (default `0`) |

### 7b. Branch Geo Fields (duplicated for add vs edit paths)
| DOM ID | Purpose |
|---|---|
| `branch_id_country` | Branch country for GST calc |
| `branch_id_state` | Branch state for GST calc |
| `branch_id_city` | Branch city |
| `branch_pincode` | Branch pincode |
| `branch_id_village` | Branch area/village |

### 7c. Customer / State Context Fields
| DOM ID | Purpose |
|---|---|
| `cus_state` | Customer state (for GST inter/intra) |
| `cmp_country` | Company country |
| `cmp_state` | Company state |
| `id_customer` | Customer modal: selected customer ID |

### 7d. Date / Config Fields
| DOM ID | Purpose |
|---|---|
| `smith_remainder_date` | Karigar reminder date |
| `smith_due_date` | Karigar due date |
| `cus_due_date` | Customer due date |
| `customer_order_description_req` | Setting: whether description is required |

### 7e. Table Row Iterator
| DOM ID | Purpose |
|---|---|
| `i_increment` | JS row counter for dynamic item table (starts at `0`) |
| `cur_id` | Current editing row index |

### 7f. Rate Display Labels (hidden, set via JS)
| Class | Purpose |
|---|---|
| `.per-grm-sale-value` | Gold per-gram sale rate |
| `.silver_per-grm-sale-value` | Silver per-gram sale rate |
| `.mjdmagoldrate_24ct` | MJDMA gold 24ct rate |
| `.mjdmagoldrate_22ct` | MJDMA gold 22ct rate |
| `.mjdmasilverrate_1gm` | MJDMA silver 1gm rate |
| `.goldrate_24ct` | Gold 24ct rate |
| `.goldrate_22ct` | Gold 22ct rate |
| `.goldrate_18ct` | Gold 18ct rate |
| `.goldrate_14ct` | Gold 14ct rate |
| `.silverrate_1gm` | Silver per-gram rate |
| `.silverrate_999` | Silver 999 purity rate |
| `.silverrate_1kg` | Silver per-kg rate |
| `.platinum_1g` | Platinum per-gram rate |

> **Total**: 43 hidden inputs / hidden labels across header, branch, customer, date, and rate sections.

## 8. Business Rules
- Order number generation includes financial year, branch, and type (`generateOrderNo`).
- Statuses define workflow (0=Cart, 1=Order Placed, 2=Rejected by Cart, 3=Process/Assign, 4=Completed/Ready, 5=Delivered, 6=Rejected New Order, 8=Rejected assigned).
- Full details in [BUSINESS_RULES.md](./BUSINESS_RULES.md).

## 9. Cross-Module Dependencies
- Integration with Billing (`ret_billing_model`), Settings (`admin_settings_model`), Products/Catalog, Tags (`ret_taging`), and SMS.
- Detailed in [CROSS_MODULE_MAP.md](./CROSS_MODULE_MAP.md).

## 10. Known Risks
- **Large Joins**: Some methods like `get_repair_orders_list()` have 15+ LEFT JOINs; performance bottleneck risk.
- **Image Handling**: Base64 image decoding processing inside loops in `save()` and `update()` methods within controllers increases memory usage risk.
- **Cart/Order coupling**: `order_cart` rows mutate status alongside `customerorder` insertion.

## 11. DB Verification Queries
```sql
-- View full order with item count
SELECT co.order_no, co.order_date, b.name as branch, COUNT(cod.id_orderdetails) as items
FROM customerorder co
LEFT JOIN customerorderdetails cod ON co.id_customerorder = cod.id_customerorder
LEFT JOIN branch b ON co.order_from = b.id_branch
WHERE co.id_customerorder = ?
GROUP BY co.id_customerorder;
```

## 12. Codebase Notes
- Extensive inline JSON handling and dynamic AJAX responses.
- Method responses are usually `json_encode()` returning `status` and `message` properties.
- Image storage utilizes file system while keeping file names in DB.

## 13. Anti-Patterns Register

| # | Finding | File | Location | Severity | Risk/Impact |
|---|---|---|---|---|---|
| 1 | **Debug leak in production** | `admin_ret_order.php` | L649 | CRITICAL | `echo $this->db->last_query();exit;` in update rollback — exposes SQL to user |
| 2 | **Double-image string bug** | `admin_ret_order.php` | L470 | CRITICAL | `$d['image'].$d['image']` — duplicates image path string in DB |
| 3 | **SQL injection (cancel)** | `admin_ret_order.php` | L660-661 | CRITICAL | `$_POST['order_id']` and `$_POST['remarks']` directly in `updateData()` |
| 4 | **SQL injection (cancel item)** | `admin_ret_order.php` | L725-726 | HIGH | Same pattern — `$_POST` values directly into update |
| 5 | **Delete-reinsert orphan risk** | `admin_ret_order.php` | L481,520,542 | HIGH | Images/stones/charges deleted BEFORE re-insert — TX fail = data loss |
| 6 | **Per-item transaction** | `admin_ret_order.php` | L478 | HIGH | `trans_begin()` inside foreach — each item is a separate TX, partial saves possible |
| 7 | **Image name collision** | `admin_ret_order.php` | L217,507 | HIGH | `mt_rand(120,1230)` → only 1110 possible filenames → overwrites |
| 8 | **Delete without cascade** | `admin_ret_order.php` | L386-390 | HIGH | Hard DELETE of order + details but no cascade to images/stones/charges tables |
| 9 | **Unreachable return** | `ret_order_model.php` | L630,640 | MEDIUM | `$edit_flag` never set to 1 → `delete_order_img()` and `update_order_des()` always return 0 |
| 10 | Heavy controller logic | `admin_ret_order.php` | L155-314 | MEDIUM | Transaction handling + image processing in controller instead of model |
| 11 | **Advance-reversal logic bug** | `admin_ret_order.php` | L667-696 | HIGH | ⭐ R15 — `get_order_total_advance()` returns an array, but `if($order_advance > 0)` compares array to int (PHP coerces truthy) — condition ALWAYS true even if advance is 0. `ret_issue_receipt` row inserted with amount `$advance_amount` fetched from array correctly, but the guard condition is dangerously loose. Will insert zero-amount receipt if advance is 0. |

> Full transaction traces: [TRANSACTION_TRACE.md](./TRANSACTION_TRACE.md)

