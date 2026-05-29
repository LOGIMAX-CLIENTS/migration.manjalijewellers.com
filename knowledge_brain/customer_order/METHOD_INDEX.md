# Method Index: Customer Order — Round 15 Refresh

> **Round 15 Refresh** — R5 base (58 ctrl / 114 model methods). R15 adds `taxGroupItems()` (missed in R5), corrects `neworders()` routing, and documents `ajax_order_cancel` advance-reversal logic.
> Scan date: 2026-03-24 | Last code change: Controller 2026-03-23, Model 2026-03-21, JS 2026-03-21

---

## 1. Controller Methods (`admin_ret_order.php` — 2325 lines, 58 methods + 1 newly documented)

> ⚠️ **R15 note**: `taxGroupItems()` at L76 was present in code since at least R14 but omitted from the brain. Added here. Also: old `neworders()` (L840) is now **commented out** in code — only the new `neworders()` at L849 is active.

### 1a. Page-Load / Dispatcher Methods

| # | Method | Lines | Type | View Loaded | Purpose |
|---|---|---|---|---|---|
| 1 | `__construct()` | ~1-50 | Init | - | Loads models (`ret_order_model`, `admin_settings_model`, `ret_billing_model`), validates session |
| 2 | `index()` | ~55 | Page | `order/form` | Default entry — loads order form |
| 3 | `order($type)` | ~300-809 | Dispatcher | `order/list`, `order/form` | Switch on `$type`: list, add, save, edit, delete, update, default(AJAX) |
| 4 | `customer_neworders()` | 828-838 | Page | `order/neworder/list` | Loads customer new-orders list view |
| 5 | `neworders()` | **849-863** (active) | Page | `order/repair_order/neworders` | Loads repair-order new orders view. ⚠️ **R15**: Old version at L840-848 was **commented out** in the 2026-03-23 edit |
| 6 | `repair_order($type,$id)` | 1412-1822 | Dispatcher | `order/repair_order/list`, `form`, `order_status` | Switch: list, add, edit, save, update, repair_order_list, repair_order_status, repair_ordered_list, default(AJAX) |
| 7 | `cart($type)` | 1878-2057 | Dispatcher | `order/cart_list`, `order/cart_status` | Switch: list, cart_status, order_place, order_status, default(AJAX) |

### 1b. AJAX Data Retrieval Methods

| # | Method | Lines | Tables Read | Returns |
|---|---|---|---|---|
| 8 | `get_img_by_order_id()` | 810-815 | `customer_order_image` | JSON: order images |
| 9 | `estimation($type)` | 816-827 | `ret_estimation` | JSON: estimation search/details |
| 10 | `ajax_get_neworder()` | 864-931 | `customerorder`, `customerorderdetails`, `customer`, `ret_karigar`, `employee`, `branch` | JSON: new order list with images |
| 11 | `get_all_branch()` | 1031-1036 | `branch` | JSON: all branches |
| 12 | `get_ordersby_id($id)` | 1037-1081 | `customerorderdetails`, `ret_product_master` | JSON: single order detail with images |
| 13 | `get_weight_range()` | 1203-1208 | `ret_weight` | JSON: weight ranges |
| 14 | `get_product_size()` | 1209-1214 | `ret_size` | JSON: product sizes |
| 15 | `get_img_by_id()` | 1230-1249 | `customerorderdetails` | JSON: images by `##` delimiter |
| 16 | `get_dec_by_id()` | 1374-1380 | `customerorderdetails` | JSON: description by order detail |
| 17 | `get_ActiveSubDesingns()` | 1383-1388 | `ret_sub_design_master` | JSON: sub-designs |
| 18 | `get_ActiveDesingns()` | 1389-1394 | `ret_design_master` | JSON: designs |
| 19 | `active_cat_product_list()` | 1396-1401 | `ret_product_master`, `ret_category` | JSON: products by category |
| 20 | `getIssueTaggingBySearch()` | 1402-1406 | `ret_taging` | JSON: tag search results |
| 21 | `get_tag_scan_details()` | 1407-1411 | `ret_taging`, many joins | JSON: detailed tag info |
| 22 | `get_repair_damage_master()` | 1871-1876 | `ret_repair_master` | JSON: repair types |
| 23 | `karigar_search()` | 2089-2094 | `ret_karigar` | JSON: karigar autocomplete |
| 24 | `get_metal()` | 2114-2119 | `metal` | JSON: active metals |
| 25 | `get_cus_product()` | 2120-2126 | `ret_product_master` | JSON: products by metal |
| 26 | `get_ActiveProducts()` | 2127-2132 | `ret_product_master` | JSON: active products |
| 27 | `get_active_design_products()` | 2133-2138 | `ret_product_mapping`, `ret_design_master` | JSON: designs by product |
| 28 | `get_metaltype()` | 2225-2231 | `ret_category` | JSON: metal type for category |
| 29 | `get_cmp_state()` | 2232-2237 | `branch` | JSON: company state |
| 30 | `get_estimation_tags()` | 2315-2325 | `ret_estimation`, `ret_taging` | JSON: estimation tags for billing |
| **31** | **`taxGroupItems()`** | **L76-80** | **`ret_taxgroupitems`** | **⭐ R15 NEW — AJAX: returns tax group items for a given `tgrp_id`; calls `getAvailableTaxGroupItems()` in model** |

### 1c. Write / Status-Update Methods

| # | Method | Lines | Tables Written | Purpose |
|---|---|---|---|---|
| 31 | `assign_customer_order()` | 932-1003 | `customerorderdetails` | Assign karigar/employee (`orderstatus→3`) or reject (`orderstatus→6`) |

> ⭐ **R15 note — `ajax_order_cancel` (case inside `order()`, L656-722)**: This was enhanced post-R14. It now includes **advance-payment reversal logic**: if `get_order_total_advance()` returns an amount > 0, a new receipt is inserted into `ret_issue_receipt` (type=2, receipt_type=5) to transfer the advance back. Tables written: `customerorder`, `customerorderdetails`, `ret_taging`, `ret_issue_receipt`. The SQL injection anti-patterns at L660-661 remain unfixed (AP-3, AP-4 from R14).
| 32 | `updatereject_reason()` | 1082-1109 | `customerorderdetails` | Set reject reason + `orderstatus→8` |
| 33 | `update_order_image()` | 1250-1287 | `customerorderdetails`, `customer_order_image` (filesystem) | Append base64 images |
| 34 | `update_and_retrive_order_image()` | 1288-1321 | `customerorderdetails` | Replace image set |
| 35 | `insert_retrive_img()` | 1322-1326 | - | Stub (returns true) |
| 36 | `delete_order_img()` | 1327-1358 | `customerorderdetails` (filesystem unlink) | Delete single image |
| 37 | `update_order_des()` | 1359-1373 | `customerorderdetails` | Update description field |
| 38 | `add_to_cart()` | 2058-2088 | `order_cart` | Insert items into cart |
| 39 | `repair_order_status()` | 1841-1870 | `customerorderdetails` | Bulk status → 4 (completed) |
| 40 | `update_repair_order_other_details()` | 2158-2224 | `customer_order_other_details`, `customer_order_stone_details`, `customerorderdetails` | Add repair sub-items and stones |
| 41 | `repair_deliver_order_status()` | 2338-2366 | `customerorderdetails` | Bulk status → 5 (delivered) |

### 1d. Image / File Handling Methods

| # | Method | Lines | Purpose |
|---|---|---|---|
| 42 | `shortenurl($url)` | ~70-95 | Shortens URL for SMS sharing |
| 43 | `base64ToFile($base64)` | ~96-130 | Decodes base64 string to temp file array |
| 44 | `imgTobase64($path)` | ~131-145 | Encodes image file to base64 string |
| 45 | `isValueset($value)` | ~146-155 | Returns value or empty string if null |
| 46 | `upload_orderimg()` | 1126-1149 | Multi-file upload to `assets/img/orders/` |
| 47 | `upload_img($outputImage,$dst,$img)` | 1150-1179 | Image resize/convert using GD library |
| 48 | `remove_img()` | 1180-1202 | Remove image from filesystem + update `#`-delimited image string |
| 49 | `get_order_images($orders)` | 1110-1125 | Parse `#`-delimited image string to array |

### 1e. PDF / Print Methods

| # | Method | Lines | View Used | Purpose |
|---|---|---|---|---|
| 50 | `get_karigar_acknowladgement()` | 1004-1030 | `order/stock_order/print/vendor_ack` | Generate PDF: karigar allocation slip |
| 51 | `vendor_acknowladgement($id)` | 1215-1228 | `order/stock_order/print/vendor_ack` | Generate PDF: vendor acknowledgement |
| 52 | `repair_acknowledgement($repair_id)` | 1823-1840 | `order/repair_order/repair_print` | Generate PDF: repair receipt |
| 53 | `repair_order_acknowladgement($id)` | 2096-2113 | `order/repair_order/repair_print` | Generate PDF: repair order receipt (alt) |
| 54 | `customer_order_acknowladgement($id)` | 2139-2156 | `order/cus_order_print` | Generate PDF: customer order print |

### 1f. OTP / Cancellation Methods

| # | Method | Lines | Tables Written | Purpose |
|---|---|---|---|---|
| 55 | `send_order_cancel_otp()` | 2238-2294 | `otp` | Generate OTP, send via SMS/WhatsApp |
| 56 | `verify_order_cancel_otp()` | 2295-2336 | `otp` | Verify OTP, update verified status |

### 1g. Remaining Methods (identified from switch-case internals)

| # | Method/Case | Lines | Purpose |
|---|---|---|---|
| 57 | `order('save')` | ~340-575 | Save new customer order (transaction) |
| 58 | `order('update')` | ~576-790 | Update existing customer order (transaction) |

---

## 2. Model Methods (`ret_order_model.php` — 2321 lines, 112 methods)

### 2a. Generic CRUD Methods

| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 1 | `insertData($data,$table)` | ~30-50 | `{any}` | Generic INSERT with default-value fill |
| 2 | `updateData($data,$key,$val,$table)` | ~60-90 | `{any}` | Generic UPDATE with default-value fill |
| 3 | `deleteData($key,$val,$table)` | ~100-110 | `{any}` | Generic DELETE by key |

### 2b. Order Number Generation

| # | Method | Lines | Tables Read | Purpose |
|---|---|---|---|---|
| 4 | `generateOrderNo($branch,$type)` | ~190-210 | `customerorder`, `branch` | Generates order number (branch+year+type+seq) |
| 5 | `generatePurNo()` | ~215-230 | `customerorder` | Generates purchase order number |
| 6 | `get_FinancialYear()` | ~240-250 | `chit_settings` | Returns current financial year code |

### 2c. Order List / Retrieval Methods

| # | Method | Lines | Key Tables Joined | Purpose |
|---|---|---|---|---|
| 7 | `ajax_getOrders($data)` | ~160-264 | `customerorder`, `customerorderdetails`, `ret_product_master`, `customer`, `employee`, `branch`, `order_status_message`, `ret_karigar`, `ret_design_master` | Main order list with filters |
| 8 | `ajax_getRepairOrders($data)` | ~270-350 | Same as above + `ret_repair_master` | Repair order list with filters |
| 9 | `getOrder($id)` | ~350-400 | `customerorder`, `customerorderdetails`, `ret_product_master`, `customer`, `ret_karigar` | Single order full detail |
| 10 | `empty_rec_order()` | ~150-180 | `branch`, `chit_settings` | Empty order template for new form |
| 11 | `get_orderdetails($id)` | 1533-1618 | 15+ tables | Full order detail array with charges, stones, images, purity, sizes |
| 12 | `get_repair_orderdetails($id)` | 1684-1787 | 15+ tables | Full repair order detail array with tag details, damage types, metals |
| 13 | `get_customer_orders($id_customerorder)` | 1483-1515 | `customerorderdetails`, `customerorder`, `ret_product_master`, `ret_design`, `ret_size`, `ret_purity`, `order_status_message`, `ret_taxgroupitems` | Order items with stones and images |
| 14 | `get_orderdetails_by_id($id)` | ~400-450 | `customerorderdetails` join many | Single order detail row |
| 15 | `get_customerorder_details($id)` | ~460-480 | `customerorderdetails` | Simple detail fetch |

### 2d. New Order & Status Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 16 | `get_new_orderlist(...)` | ~500-560 | `customerorderdetails`, `customerorder`, `customer`, `ret_karigar`, `employee`, `branch` | Filtered new order list |
| 17 | `get_joborder_details($id)` | ~570-590 | `joborder` | Get job order linked to order detail |
| 18 | `getRepairDetails($id)` | ~780-818 | `customerorderdetails`, `ret_product_master`, `ret_design_master`, `ret_repair_master` | Repair detail items |
| 19 | `getRepairDetailsStatus($id)` | 821-835 | `customerorderdetails` | Check if any items have status ≥ 3 |

### 2e. Repair Order Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 20 | `get_repair_orders_list($data)` | 837-890 | `customerorderdetails`, `customerorder`, `ret_product_master`, `customer`, `ret_karigar`, `branch`, `order_status_message`, `customer_order_image`, `ret_taging` | Full repair order listing |
| 21 | `get_repair_item_details($order_id)` | 892-910 | `customer_order_other_details`, `ret_category`, `ret_purity`, `ret_product_master`, `ret_design_master`, `ret_sub_design_master`, `ret_section` | Repair sub-items |
| 22 | `repair_detail($order_id)` | 911-927 | `customerorder`, `customerorderdetails`, `branch`, `customer`, `village`, `address`, `state`, `city`, `employee` | Repair header for print |
| 23 | `get_repair_details($order_id)` | 928-963 | `customerorderdetails`, `ret_product_master`, `ret_taging`, `ret_repair_master`, `ret_design_master`, `ret_sub_design_master` | Repair items with stones |
| 24 | `get_repair_damage_master()` | 964-968 | `ret_repair_master` | All repair types |

### 2f. Karigar / Vendor Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 25 | `getActiveKarigar()` | 970-974 | `ret_karigar` | Active karigar list |
| 26 | `karigar_search($SearchTxt)` | 975-980 | `ret_karigar` | Karigar autocomplete search |
| 27 | `get_karigar_details($id_karigar)` | 1203-1215 | `ret_karigar`, `country`, `state`, `city` | Full karigar profile |
| 28 | `get_karigar_orders($id)` | ~600-650 | `customerorderdetails`, `ret_karigar`, `ret_product_master` | Karigar order allocation |
| 29 | `get_karigar_order_details($id)` | 1130-1150 | `customerorder`, `customerorderdetails`, `ret_karigar`, `ret_product_master`, `ret_design_master`, `ret_size`, `ret_weight`, `ret_uom`, `employee` | Karigar order detail |
| 30 | `get_karigar_order_products($id)` | 1151-1186 | Same as above | Grouped by product+design |
| 31 | `get_karigar_order_product_details(...)` | 1187-1202 | Same as above | Weight/size breakdown |
| 32 | `get_karigar_pending_orders($data)` | 1239-1250 | `customerorder`, `customerorderdetails`, `ret_karigar` | Pending karigar orders |
| 33 | `get_karigar_pending_order_details($data)` | 1251-1272 | Full join chain | Pending order item details |

### 2g. Cart Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 34 | `ajax_getCartOrders($data)` | 981-1087 | `order_cart`, `ret_product_master`, `ret_design_master`, `ret_weight`, `ret_size`, `branch`, `employee`, `ret_reorder_settings`, `ret_taging`, `ret_purchase_order_items` | Cart list with shortage/excess calc |
| 35 | `getCartDetails()` | 1088-1101 | `order_cart`, `branch`, `ret_product_master`, `ret_design_master`, `customerorderdetails`, `employee` | Cart status listing |
| 36 | `getReorderitems(...)` | 1102-1114 | `ret_reorder_settings`, `ret_weight` | Max pieces for reorder check |

### 2h. Purchase Order Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 37 | `get_purchase_order_Details()` | 1115-1129 | `customerorder`, `ret_karigar`, `branch`, `order_status_message` | Purchase order listing |
| 38 | `get_pur_order_details($data)` | 1216-1238 | `customerorderdetails`, `customerorder`, `ret_product_master`, `ret_design_master`, `ret_karigar`, `employee`, `order_status_message` | Purchase order item details |
| 39 | `update_partial_order_delivery($data,$arith)` | 1273-1279 | `customerorderdetails` | Arithmetic update for partial delivery qty/wt |
| 40 | `update_order_delivery($data,$arith)` | 1280-1286 | `customerorderdetails` | Arithmetic update for full delivery qty/wt |

### 2i. Customer Order Pending / Billing Integration

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 41 | `get_customer_order_pending_details($data)` | 1287-1306 | `customerorder`, `customerorderdetails` | Orders with `orderstatus < 3` |
| 42 | `get_customer_order_item_details($id)` | 1307-1330 | Full join chain + `ret_sub_design_mapping` | Item details with default image |
| 43 | `get_order_total_advance($orderid)` | 1384-1396 | `ret_billing_advance`, `ret_billing` | Sum of advance amounts for order |
| 44 | `get_order_customer_id($orderid)` | 1397-1405 | `customerorder` | Returns `order_to` customer ID |
| 45 | `get_order_details($orderid)` | 1406-1413 | `customerorderdetails`, `customerorder` | Order detail IDs + `order_from` |

### 2j. Billing / Bill Number Generation

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 46 | `bill_no_generate($id_branch,$is_eda)` | 1414-1429 | `ret_issue_receipt` | Generate next bill number |
| 47 | `get_max_bill_no($id_branch,$is_eda)` | 1430-1437 | `ret_issue_receipt` | Get max bill number by branch/year |
| 48 | `getCompanyDetails($id_branch)` | 1438-1464 | `company`, `branch`, `country`, `state`, `city`, `chit_settings` | Company/branch profile |

### 2k. Customer / Address Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 49 | `get_order_cus_details($id)` | 1465-1482 | `customerorder`, `customerorderdetails`, `customer`, `address`, `city`, `state`, `country`, `village`, `branch` | Customer details for order print |
| 50 | `repair_order_acknowladgement($insOrder)` | 1332-1350 | `customerorder`, `customerorderdetails`, `branch`, `customer`, `address`, `state`, `city`, `village`, `ret_product_master` | Repair acknowledgement data |

### 2l. Product / Catalog Lookup Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 51 | `get_ActiveProducts($data)` | 1363-1371 | `ret_product_master` | Active products by category |
| 52 | `get_active_design_products($data)` | 1372-1383 | `ret_product_mapping`, `ret_design_master` | Designs by product |
| 53 | `get_active_metal()` | 1351-1355 | `metal` | All metals |
| 54 | `get_cus_product($id_metal)` | 1356-1362 | `ret_product_master` | Products by metal type |
| 55 | `get_metal_for_category($id_category)` | 1516-1526 | `ret_category` | Metal ID by category |
| 56 | `get_cmp_state($id_branch)` | 1527-1532 | `branch` | Branch state ID |
| 57 | `get_weight_range($searchTxt)` | ~680-700 | `ret_weight` | Weight range lookup |
| 58 | `get_product_size($id_product)` | ~700-720 | `ret_size` | Product size lookup |
| 59 | `getActiveCategories()` | ~720-730 | `ret_category` | Active categories |
| 60 | `getSearchSubDesign($data)` | ~730-745 | `ret_sub_design_master` | Sub-design search |
| 61 | `getSearchDesign($data)` | ~745-760 | `ret_design_master` | Design search |
| 62 | `get_active_cat_product_list()` | ~760-780 | `ret_product_master`, `ret_category` | Category+product tree |

### 2m. Image Retrieval Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 63 | `get_order_images($id_orderdetails)` | ~650-680 | `customer_order_image` | Images for order detail |
| 64 | `get_img_by_id($id_orderdetails)` | ~690-700 | `customerorderdetails` | Raw image field by ID |
| 65 | `get_dec_by_id($id_orderdetails)` | ~700-710 | `customerorderdetails` | Description field by ID |
| 66 | `getordersImages($id)` | 1660-1675 | `customer_order_image` | Images with base64 encoding |

### 2n. Order Detail Helper Methods (for edit/print)

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 67 | `getordercharges($id)` | 1619-1627 | `ret_order_other_charges`, `ret_charges` | Other charges for order item |
| 68 | `getorderstones($id)` | 1628-1639 | `ret_order_item_stones`, `ret_stone`, `ret_uom` | Stone details for order item |
| 69 | `getorderProducts()` | 1640-1645 | `ret_product_master` | All products (for dropdown) |
| 70 | `getorderpurity($id_purity)` | 1646-1652 | `ret_purity` | Purity lookup |
| 71 | `getordersize($id_size)` | 1653-1659 | `ret_size` | Size lookup |
| 72 | `get_tagorder_details($id)` | 1676-1683 | `ret_taging` | Tag-order link |
| 73 | `getorderdamage()` | 2040-2045 | `ret_repair_master` | All repair/damage types |
| 74 | `getordermetal()` | 2046-2051 | `metal` | All metals |
| 75 | `getorderdesigns()` | 2052-2057 | `ret_design_master` | All designs |
| 76 | `getordersubdesigns()` | 2058-2063 | `ret_sub_design_master` | All sub-designs |

### 2o. Tag / Scan Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 77 | `get_tag_scan_details($tag_code,$old)` | 1788-1957 | `ret_taging`, `ret_lot_inwards_detail`, `ret_product_master`, `ret_design_master`, `ret_purity`, `ret_category`, `ret_taging_images`, `ret_taging_stone`, `ret_tag_other_metals`, `ret_metal_purity_rate`, `ret_stock_issue_detail`, `ret_section` | Comprehensive tag scan with stones |
| 78 | `get_stock_issue_StoneDetails($tag_id)` | 1963-1977 | `ret_taging_stone`, `ret_stone`, `ret_uom` | Stone details for a tag |
| 79 | `get_tag_order_details($tag_code,$old)` | 1978-2039 | Same as 77 but without `id_orderdetails IS NOT NULL` filter | Tag lookup for orders |
| 80 | `getIssueTaggingBySearch(...)` | ~550-570 | `ret_taging`, `ret_product_master` | Tag search by barcode/code |

### 2p. OTP / Branch Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 81 | `get_order($order_id)` | 2064-2068 | `customerorder` | Get `order_from` branch |
| 82 | `getBrnachOtpRegMobile($id_branch)` | 2069-2073 | `branch` | Get OTP-registered mobile number |
| 83 | `getBranchDayClosingData($id_branch)` | 2244-2252 | `ret_day_closing` | Branch day-closing status/date |

### 2q. Estimation Tags (Billing Bridge)

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 84 | `get_estimation_tags($id_branch,$estId)` | 2076-2215 | `ret_estimation`, `ret_estimation_items`, `ret_taging`, `customerorderdetails`, `ret_estimation_item_stones`, `ret_estimation_item_other_materials`, `ret_taging_stone`, `ret_product_master`, `ret_category`, `ret_design_master`, `ret_purity`, `ret_taxgroupmaster`, `customer`, `address`, `village`, `ret_metal_purity_rate`, `ret_estimation_other_charges`, `ret_nontag_item` | Massive query linking estimations→tags→orders for billing |
| 85 | `getTagStoneDetails($tagid)` | 2217-2232 | `ret_taging_stone`, `ret_stone` | Tag stone details for estimation |
| 86 | `get_charges($tag_id)` | 2235-2242 | `ret_taging_charges`, `ret_charges` | Tag charges for estimation |

### 2r. Email / Acceptance Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 87 | `save_email_log($data)` | 2253-2256 | `ret_order_email_logs` | Insert email log |
| 88 | `get_email_log_by_token($token)` | 2258-2263 | `ret_order_email_logs` | Retrieve by token |
| 89 | `update_email_log($id,$data)` | 2265-2268 | `ret_order_email_logs` | Update email log status |
| 90 | `update_order_acceptance(...)` | 2270-2291 | `customerorder`, `customerorderdetails`, `ret_order_email_logs` | Accept order (status→3) + set due date |
| 91 | `update_order_rejection(...)` | 2293-2319 | `customerorder`, `customerorderdetails`, `ret_order_email_logs` | Reject order (status→6) + reason |

### 2s. Settings / Profile Helper Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 92 | `get_profile_settings($id_profile)` | 109-113 | `profile` | Get profile settings (controls `allow_bill_type`) |
| 93 | `get_ret_settings($settings)` | 212-216 | `ret_settings` | Get single setting value by name key |
| 94 | `get_headoffice_branch()` | 245-249 | `branch` | Get HO branch (where `is_ho=1`) |

### 2t. Order Search / Lookup by Customer

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 95 | `getOrderNos($SearchTxt)` | 204-207 | `customerorderdetails` | Autocomplete search by order number |
| 96 | `getOrderByCus($id_cus)` | 208-211 | `customerorderdetails`, `customerorder` | All orders for a customer |

### 2u. Estimation Integration Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 97 | `fetchEstiBySearch($SearchTxt)` | 250-253 | `ret_estimation` | Estimation autocomplete search |
| 98 | `getEstiDetailsById($SearchTxt)` | 254-282 | `ret_estimation`, `ret_estimation_items`, `ret_product_master`, `ret_design_master`, `ret_purity`, `ret_estimation_old_metal_sale_details` | Full estimation with items + old metal |

### 2v. Order Detail w/ Image Methods (used by list views)

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 99 | `getOrder($id)` | 333-342 | `customerorder`, `customer`, `branch` | Order header (customer name, branch, order_no) |
| 100 | `getOrderDetails($id)` | 343-366 | `customerorderdetails`, `customerorder`, `ret_product_master`, `ret_design_master`, `ret_sub_design_master`, `ret_size`, `ret_purity`, `ret_category`, `order_status_message`, `customer_order_image` | Full order items with charges + images |
| 101 | `get_order_charges($id_orderdetails)` | 367-374 | `ret_order_other_charges`, `ret_charges` | Charges for a specific order item |
| 102 | `getCustomerOrderDetails($id)` | 375-404 | Same as `getOrderDetails` + `less_wt`, `net_wt`, `description` | Variant for order list drilldown (includes extra fields) |
| 103 | `get_order_stones($id_orderdetails)` | 405-414 | `ret_order_item_stones` | Stones for a specific order item |
| 104 | `get_order_images($id_orderdetails)` | 415-419 | `customer_order_image` | All images for an order detail |

### 2w. SMS / Notification Helpers

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 105 | `getSms_data($id_customerorder)` | 609-617 | `customerorder`, `customerorderdetails` | SUM of pieces and weight for SMS text |

### 2x. Model-Level Write Helpers

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 106 | `delete_order_img($id_orderdetails)` | 630-639 | `customerorderdetails` | Set `image=NULL` in DB (⚠️ bug: `$edit_flag` never set to 1) |
| 107 | `update_order_des($id_orderdetails,$desc)` | 640-649 | `customerorderdetails` | Set `description` in DB (⚠️ same bug pattern) |

### 2y. Master Data Loader Methods (for dropdowns/forms)

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 108 | `getActiveCategories()` | 545-555 | `ret_category`, `metal` | All active categories with `tgrp_id` |
| 109 | `getActiveproducts()` | 556-562 | `ret_product_master`, `ret_category` | All products with `cat_id` |
| 110 | `getActivedesigns()` | 563-571 | `ret_product_mapping`, `ret_product_master`, `ret_design_master` | All designs mapped to products |
| 111 | `getActivesubdesigns()` | 572-581 | `ret_sub_design_mapping`, `ret_product_master`, `ret_design_master`, `ret_sub_design_master` | All sub-designs mapped |
| 112 | `getAvailableTaxGroupItems($taxgroupid)` | 582-589 | `ret_taxgroupitems`, `ret_taxmaster` | Tax group items for GST calculation |

### 2z. Internal Helper Methods

| # | Method | Lines | Key Tables | Purpose |
|---|---|---|---|---|
| 113 | `getRepairOrderDet($id_customerorder)` | 793-815 | Same as `getOrderDetails` + `ret_repair_master`, `ret_taging` | Repair order items for list drilldown (called by `ajax_getRepairOrders`) |
| 114 | `get_active_product_byCatId($catId)` | 697-705 | `ret_product_master` | Products filtered by category (called by `get_active_cat_product_list`) |

> **Total: 114 methods documented** (112 actual + 2 constructor/overloaded variants). All methods now have line numbers, table references, and purpose.

---

## 3. Table → Methods Reverse Map

| Table | Read By (Method) | Written By (Method) |
|---|---|---|
| `customerorder` | `ajax_getOrders`, `ajax_getRepairOrders`, `getOrder`, `get_order_cus_details`, `get_customer_orders`, `get_purchase_order_Details`, `get_karigar_order_details`, `get_karigar_pending_orders`, `get_estimation_tags`, `repair_order_acknowladgement` | `order('save')`, `order('update')`, `repair_order('save')`, `repair_order('update')`, `cart('order_place')`, `update_order_acceptance`, `update_order_rejection` |
| `customerorderdetails` | `ajax_getOrders`, `get_orderdetails`, `get_repair_orderdetails`, `get_customer_orders`, `get_repair_orders_list`, `get_repair_details` | `order('save')`, `order('update')`, `repair_order('save')`, `repair_order('update')`, `assign_customer_order`, `updatereject_reason`, `repair_order_status`, `repair_deliver_order_status`, `update_repair_order_other_details` |
| `order_cart` | `ajax_getCartOrders`, `getCartDetails` | `add_to_cart`, `cart('order_place')` |
| `customer_order_image` | `get_order_images`, `getordersImages` | `order('save')`, `order('update')`, `update_order_image`, `delete_order_img` |
| `ret_order_item_stones` | `getorderstones` | `order('save')`, `order('update')`, `repair_order('save')`, `repair_order('update')` |
| `ret_order_other_charges` | `getordercharges` | (written via billing module) |
| `customer_order_other_details` | `get_repair_item_details` | `update_repair_order_other_details` |
| `customer_order_stone_details` | - | `update_repair_order_other_details` |
| `ret_taging` | `get_tag_scan_details`, `getIssueTaggingBySearch`, `get_tag_order_details` | `order('save')`, `order('update')`, `repair_order('update')` |
| `ret_taging_status_log` | - | `repair_order('update')` |
| `ret_order_email_logs` | `get_email_log_by_token` | `save_email_log`, `update_email_log`, `update_order_acceptance`, `update_order_rejection` |
| `otp` | - | `send_order_cancel_otp`, `verify_order_cancel_otp` |
| `ret_billing_advance` | `get_order_total_advance` | - |
| `ret_estimation` | `get_estimation_tags` | - |
