# Estimation Module — Method Index

> **Module**: Estimation
> **Date Built**: 2026-02-18 (Round 5)
> **Last Updated**: 2026-03-24 (Round 2 — VA slab correction, ctrl method reconciliation)
> **Purpose**: Alphabetical lookup of ALL methods with file, lines, tables touched, and callers — optimized for AI agent grep

---

## Controller Methods (64 public + 11 utility)

### Route-Mapped Methods (alphabetical)

| Method | Lines | Tables Read | Tables Written | JS Caller |
|---|---|---|---|---|
| `ajax_get_village()` | 3078-3087 | `village` | — | `getVillageByPincode()` |
| `createNewCustomer()` | 2478-2555 | — | `customer`, `address` | `saveNewCustomer()` L5585 |
| `deleteEstimation()` | via `estimation('delete')` | `ret_estimation` | `ret_estimation*` (all child tables) | `deleteEstimation()` L5499 |
| ~~`edaApprove()`~~ | — | — | — | ⚠️ REMOVED (Round 2) — Not a controller public method. EDA approval is handled via estimation() or admin_settings_model. L3048-3074 in brain was wrong. |
| ~~`edaReject()`~~ | — | — | — | ⚠️ REMOVED (Round 2) — Not a controller public method. Same as above. |
| `estimation('add')` | 190-2474 | 15+ tables | — | Page load |
| `estimation('ajax')` | 190-2474 | `ret_estimation`, `ret_estimation_items` | — | `getEstimationList()` L10346 |
| `estimation('edit')` | 190-2474 | 15+ tables | — | Page load |
| `estimation('list')` | 190-2474 | — | — | Page load |
| `estimation('save')` | 190-2474 | Multiple | `ret_estimation`, `ret_estimation_items`, `ret_estimation_item_stones`, `ret_estimation_item_other_materials`, `ret_estimation_other_charges`, `ret_estimation_old_metal_sale_details`, `ret_esti_old_metal_stone_details`, `ret_est_chit_utilization`, `ret_est_gift_voucher_details`, `ret_estimation_va_slab_UNVERIFIED`, `ret_est_sales_return_utilization`, `ret_estimation_other_inventory_issue` | `saveEstimation()` |
| `generate_brief_copy()` | 3000-3046 | `ret_estimation`, `company_details` | — | List action btn |
| `generate_invoice()` | 2939-2996 | `ret_estimation`, `company_details` | — | List action btn |
| `getAvailableCustomers()` | 2556-2574 | `customer` | — | `getCustomersBySearch()` L5653 |
| `get_ActiveProduct()` | 2653-2662 | `ret_product_master` | — | Product dropdown |
| `get_Active_Purity()` | 2663-2673 | `ret_purity` | — | Purity dropdown |
| `get_all_old_metal_rates()` | 3197-3207 | `ret_old_metal_rate` | — | `getOldMetalRates()` L4261 |
| `get_branchwise_rate()` | 2782-2798 | `ret_branchwise_rate` | — | `getBranchwiseRate()` |
| `get_credit_pending_details()` | 2870-2917 | `ret_billing`, `ret_issue_receipt`, `ret_issue_credit_collection_details` | — | `getCreditPendingDetails()` |
| `get_employee()` | 2798-2846 | `employee` | — | Employee dropdown |
| `get_metal_purity_rate()` | 2674-2683 | `ret_branchwise_rate`, `ret_purity` | — | Multiple L2003/6329/6591 |
| `get_non_tag_stock()` | 2770-2781 | `ret_taging` (non-tag) | — | `getNonTagStock()` L8263 |
| `get_old_metal_category()` | 3220-3232 | `ret_old_metal_category` | — | `getOldMetalCategory()` L4289 |
| `get_old_metal_stone_details()` | 2857-2869 | `ret_esti_old_metal_stone_details` | — | Old metal stone detail |
| `get_old_metal_type()` | 3209-3218 | `ret_old_metal_type` | — | `getOldMetalType()` L4375 |
| `get_order_details()` | 2684-2712 | `ret_order`, `ret_order_items` | — | `getOrderDetails()` |
| `get_other_material_details()` | 2848-2856 | `ret_estimation_item_other_materials` | — | `getOtherMaterialDetails()` L15054 |
| `get_purity_rate()` | 2713-2722 | `ret_branchwise_rate` | — | `getPurityRate()` L9846 |
| `get_stone_details()` | 2838-2847 | `ret_estimation_item_stones` | — | `getStoneDetails()` L14550/15293 |
| `getCustomerDet()` | 2575-2629 | `customer`, `ret_estimation`, `ret_billing` | — | Customer history popup |
| `getCustomersBySearch()` | 2556-2574 | `customer` | — | `getCustomersBySearch()` L5653 |
| `getCustomProductBySearch()` | 2726-2756 | `ret_product_master` | — | `getCustomProductBySearch()` L6637 |
| `getdesigndetails()` | 2757-2769 | `ret_design_master` | — | Design dropdown |
| `getMetalTypes()` | 2723-2725 | `metal` | — | `getMetalTypes()` L4319/10760 |
| `getNonTagLots()` | 2636-2652 | `ret_taging` | — | `getNonTagLots()` L6215 |
| `getOrderBySearch()` | 2610-2635 | `ret_order` | — | `getOrderBySearch()` L559 |
| `getPartialTagSearch_old()` | 2630-2638 | `ret_taging` | — | Legacy search |
| `getProductBySearch()` | 2699-2725 | `ret_product_master` | — | `getProductBySearch()` L6375 |
| `getProductDesignBySearch()` | 2757-2769 | `ret_design_master` | — | `getProductDesignBySearch()` L6781/6967 |
| `getProductSubDesignBySearch()` | 2770-2781 | `ret_sub_design_master` | — | `getProductSubDesignBySearch()` L6895 |
| `getTaggingBySearch()` | 2574-2609 | `ret_taging`, `ret_taging_stone` | — | `getSearchTags()` L2345/5847 |
| `getTaggingScanBySearch()` | 2574-2609 | `ret_taging` (barcode) | — | `scanTag()` L1029/1529 |
| `getTaggingSearchByCollection()` | 2574-2609 | `ret_taging`, `tag_collection_mapping` | — | `getTaggingSearchByCollection()` L2049 |
| `getTagImageDetails()` | 3233-3247 | `ret_tag_image` | — | Tag image preview |
| `updateCustomer()` | 3120-3194 | `customer` | `customer`, `address` | `updateCustomerDetails()` |

### Utility Methods (non-route, internal)

| Method | Lines | Purpose |
|---|---|---|
| `base64ToFile()` | 3287-3313 | Convert webcam base64 → file |
| `getNonTagproducts()` | 2772-2781 | Non-tag product list |
| `get_old_metal_types()` | 3209-3218 | Old metal types (plural) |
| `isEmptySetDefault()` | 3412-3420 | Default value helper |
| `old_get_village()` | 3430-3452 | Legacy village lookup |
| `remove_img()` | 154-171 | Delete customer image |
| `rrmdir()` | 133-152 | Recursive dir delete |
| `set_image()` | 56-73 | Save customer image |
| `upload_img()` | 75-131 | Resize/compress image |

---

## Model Methods (116 total, alphabetical)

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `aadhar_available()` | 2900-2917 | `customer` | — | Controller AJAX |
| `advance_details_order_details()` | 2002-2020 | `ret_order`, `ret_issue_receipt` | — | Controller (edit load) |
| `advance_details_order_no()` | 1541-1557 | `ret_order`, `ret_issue_receipt` | — | Controller (edit load) |
| `ajax_getEstimationList()` | 169-235 | `ret_estimation`, `customer`, `employee`, `ret_billing`, `ret_estimation_items`, `ret_product_master`, `ret_category`, `metal`, `ret_customer_review`, `ret_est_chit_utilization`, `scheme_account`, `scheme`, `ret_est_sales_return_utilization`, `ret_estimation_old_metal_sale_details`, `ret_bill_old_metal_sale_details` | — | Controller `estimation('ajax')` |
| `child_tag_details()` | 2615-2636 | `ret_estimation_items`, `ret_est_tag_merge` | — | Controller (edit load) |
| `createNewCustomer()` | 775-829 | — | `customer` | Controller `createNewCustomer()` |
| `deleteData()` | 101-113 | — | `{any}` (generic) | Controller (save/delete) |
| `dl_available()` | 2990-3009 | `customer` | — | Controller AJAX |
| `encrypt()` | 771-774 | — | — | Internal utility |
| `est_home_bill()` | 2395-2521 | `ret_estimation_items`, `ret_product_master`, `ret_estimation_item_stones`, `ret_estimation_item_other_materials`, `ret_estimation_other_charges` | — | Controller (edit load) |
| `est_non_tag_items()` | 2266-2394 | `ret_estimation_items`, `ret_product_master`, `ret_taging`, `ret_estimation_item_stones`, `ret_estimation_item_other_materials`, `ret_estimation_other_charges` | — | Controller (edit load) |
| `generateEstiNo()` | 1456-1472 | `ret_estimation` | — | Controller (save) |
| `get_Active_Purity()` | 2646-2659 | `ret_purity` | — | Controller AJAX |
| `get_ActiveProduct()` | 1978-1982 | `ret_product_master` | — | Controller AJAX |
| `get_all_old_metal_rates()` | 1368-1376 | `ret_old_metal_rate` | — | Controller AJAX |
| `get_bill_no_format_detail()` | 239-381 | `ret_estimation`, `ret_billing`, `branch`, `financial_year` | — | Controller (save), `ajax_getEstimationList` |
| `get_branchwise_rate()` | 1435-1455 | `ret_branchwise_rate`, `metal` | — | Controller (form load) |
| `get_billing_advance_details()` | 2920-2954 | `ret_issue_receipt`, `ret_advance_transfer`, `ret_advance_utilized`, `ret_advance_refund` | — | Controller (form load — net advance balance for customer) |
| `get_charges()` | 398-402 | `ret_taging_charges`, `ret_charges` | — | Controller (tag load — default charges per tag) |
| `get_chit_details()` | 2021-2044 | `ret_est_chit_utilization`, `scheme_account`, `scheme` | — | Controller (edit load) |
| `get_child_tag_stone_details()` | 2637-2645 | `ret_estimation_item_stones`, `ret_stone`, `ret_uom` | — | Controller (child/split tag edit) — ⚠️ **BUG: missing return statement, always returns NULL** |
| `get_closed_accounts()` | 1377-1385 | `scheme_account` | — | Controller AJAX |
| `get_credit_collection_details()` | 2778-2798 | `ret_issue_credit_collection_details`, `ret_billing` | — | `get_credit_pending_details()` |
| `get_credit_pending_details()` | 2709-2777 | `ret_billing`, `ret_issue_receipt` | — | Controller AJAX |
| `get_order_advance_details()` | 2956-2968 | `ret_billing_advance`, `ret_billing` | — | Controller (form load — unadjusted order advance balance) |
| `get_data()` | 382-386 | `ret_estimation` | — | Controller (generic) |
| `get_employee()` | 1386-1434 | `employee`, `branch` | — | Controller (form load) |
| `get_employee_settings()` | 123-132 | `employee`, `emp_setting` | — | Controller (form load) |
| `get_empty_record()` | 747-760 | — | — | Controller (add: empty form) |
| `get_entry_records()` | 403-429 | `ret_estimation`, `customer`, `village`, `address`, `employee`, `branch`, `city`, `ret_estimation_items` | — | Controller (edit load) |
| `get_est_other_metal_details()` | 717-726 | `ret_estimation_item_other_materials` | — | Controller AJAX |
| `get_est_stone_wt()` | 2698-2702 | `ret_estimation_item_stones` | — | Internal helper |
| `get_est_tag_details()` | 2045-2265 | `ret_estimation_items`, `ret_taging`, `ret_taging_stone`, `ret_other_metal_details`, `ret_estimation_item_stones`, `ret_estimation_item_other_materials`, `ret_estimation_other_charges`, `ret_product_master`, `ret_design_master`, `ret_sub_design_master` | — | Controller (edit load) |
| `get_FinancialYear()` | 138-142 | `financial_year` | — | Internal |
| `get_IssueCreditCollectionDetails()` | 2828-2837 | `ret_issue_credit_collection_details` | — | Credit chain |
| `get_mc_va_limit()` | 1903-1924 | `ret_product_master`, `ret_design_master` | — | Controller (form load) |
| `get_metal_purity_rate()` | 1745-1749 | `ret_branchwise_rate`, `ret_purity` | — | Controller AJAX |
| `get_non_tag_stock_details()` | 1989-2001 | `ret_taging` | — | Controller AJAX |
| `get_old_metal_category()` | 1740-1744 | `ret_old_metal_category` | — | Controller AJAX |
| `get_old_metal_Product()` | 2580-2589 | `ret_product_master` | — | Controller (old metal) |
| `get_old_metal_rate()` | 1363-1367 | `ret_old_metal_rate` | — | Controller AJAX |
| `get_old_metal_stone_details()` | 692-703 | `ret_esti_old_metal_stone_details` | — | Controller AJAX |
| `get_old_metal_type()` | 1735-1739 | `ret_old_metal_type` | — | Controller AJAX |
| `get_old_metal_types()` | 2667-2671 | `ret_old_metal_type` | — | Controller AJAX |
| `get_oldmetalDetails()` | 387-392 | `ret_estimation_old_metal_sale_details` | — | `ajax_getEstimationList()` |
| `get_other_estcharges()` | 1885-1894 | `ret_estimation_other_charges` | — | Controller (edit load) |
| `get_other_material_details()` | 735-746 | `ret_estimation_item_other_materials` | — | Controller AJAX |
| `get_other_metal_details()` | 1181-1193 | `ret_other_metal_details` | — | Tag detail population |
| `get_partial_details()` | 1591-1615 | `ret_taging`, `ret_estimation_items` | — | Controller (partial tag) |
| `get_partly_home_bill_stones()` | 1715-1734 | `ret_taging_stone`, `ret_estimation_item_stones` | — | Controller (partial) |
| `get_profile_settings()` | 2610-2614 | `profile_setting` | — | Controller (form load) |
| `get_purchase_details()` | 1925-1943 | `ret_taging` (purchase data) | — | Controller (form load) |
| `get_purity_rate()` | 1944-1948 | `ret_branchwise_rate` | — | Controller AJAX |
| `get_ret_settings()` | 114-122 | `ret_settings` | — | Controller (form load) |
| `get_sectionBranchwise()` | 2672-2697 | `section`, `branch` | — | Controller (form dropdown) |
| `get_stone_details()` | 704-716 | `ret_estimation_item_stones` | — | Controller AJAX |
| `get_stone_disc()` | 3010-3017 | `ret_stone_discount` | — | Controller (form load) |
| `get_tag_details()` | 1576-1590 | `ret_taging`, `ret_product_master` | — | Controller (tag lookup) |
| `get_tag_status_details()` | 2590-2594 | `ret_taging` | — | Controller (tag status) |
| `get_tag_stone_details()` | 727-734 | `ret_taging_stone` | — | Tag detail population |
| `get_tag_stone_wt()` | 2703-2708 | `ret_taging_stone` | — | Internal helper |
| `get_taggedorder_details()` | 1983-1988 | `ret_order_items` | — | Controller (order tag) |
| `get_va_range()` | 3019-3027 | `ret_va_range` | — | Controller (form load) |
| `get_village()` | 133-137 | `village` | — | Controller AJAX |
| `get_village_by_pincode()` | 2660-2666 | `village` | — | Controller AJAX |
| `GetActiveFinancialYear()` | 766-770 | `financial_year` | — | Controller (save) |
| `GetFinancialYear()` | 761-765 | `financial_year` | — | `get_entry_records()` |
| `getAvailableCustomers()` | 872-917 | `customer` | — | Controller AJAX |
| `getBranchDayClosingData()` | 393-397 | `ret_day_closing` | — | Controller (save gate) |
| `getCollectionDetails()` | 1069-1076 | `tag_collection_mapping` | — | Controller (collection search) |
| `getCompanyDetails()` | 1949-1977 | `company_details`, `address`, `city`, `state`, `country` | — | Controller (print) |
| `getCreditCollection()` | 2799-2827 | `ret_issue_credit_collection_details` | — | Credit chain |
| `getCustomProductBySearch()` | 1270-1307 | `ret_product_master` | — | Controller AJAX |
| `getdesigndetails()` | 1242-1253 | `ret_design_master`, `ret_sub_design_master` | — | Controller AJAX |
| `getEstTags()` | 143-147 | `ret_estimation_items` | — | Controller (tag check) |
| `getMetalTypes()` | 1354-1357 | `metal` | — | Controller AJAX |
| `getNonTagLots()` | 157-168 | `ret_taging` | — | Controller AJAX |
| `getNonTagproducts()` | 1254-1269 | `ret_product_master` | — | Controller AJAX |
| `getOld_sales_detail()` | 2838-2848 | `ret_bill_old_metal_sale_details` | — | Credit chain |
| `getOld_sales_details()` | 2849-2858 | `ret_bill_old_metal_sale_details` | — | Credit chain |
| `getOldMetalRate()` | 683-691 | `ret_old_metal_rate` | — | Controller (old metal) |
| `getOrderBySearch()` | 1473-1540 | `ret_order`, `customer` | — | Controller AJAX |
| `getOtherEstimateItemsDetails()` | 430-682 | `ret_estimation_items`, `ret_estimation_item_stones`, `ret_estimation_item_other_materials`, `ret_estimation_other_charges`, `ret_estimation_old_metal_sale_details`, `ret_esti_old_metal_stone_details`, `ret_est_chit_utilization`, `ret_est_gift_voucher_details`, `ret_estimation_va_slab_UNVERIFIED`, `ret_est_sales_return_utilization`, `ret_estimation_other_inventory_issue` | — | Controller (edit load) |
| `getPartialTagSearch()` | 1680-1714 | `ret_taging` | — | Controller AJAX |
| `getPartialTagSearch_1()` | 1616-1679 | `ret_taging` | — | Controller AJAX (legacy) |
| `getProductBySearch()` | 1194-1241 | `ret_product_master`, `ret_category` | — | Controller AJAX |
| `getProductDesignBySearch()` | 1308-1353 | `ret_design_master` | — | Controller AJAX |
| `getProductSubDesignBySearch()` | 1895-1902 | `ret_sub_design_master` | — | Controller AJAX |
| `getTaggingBySearch()` | 918-1012 | `ret_taging`, `ret_taging_stone`, `ret_product_master`, `ret_purity` | — | Controller AJAX |
| `getTaggingScanBySearch()` | 1088-1167 | `ret_taging`, `ret_taging_stone`, `ret_product_master` | — | Controller AJAX |
| `getTaggingSearchByCollection()` | 1013-1068 | `ret_taging`, `tag_collection_mapping` | — | Controller AJAX |
| `getTagImageDetails()` | 2595-2602 | `ret_tag_image` | — | Controller AJAX |
| `getUOMDetails()` | 1358-1362 | `ret_uom` | — | Controller AJAX |
| `gst_available()` | 2880-2898 | `customer` | — | Controller AJAX |
| `insertBatchData()` | 85-100 | — | `{any}` (generic) | Controller (save) |
| `insertData()` | 11-45 | `{any}` (SHOW COLUMNS) | `{any}` (generic) | Controller (save) |
| `isEmptySetDefault()` | 2603-2609 | — | — | Internal utility |
| `old_metal()` | 2522-2579 | `ret_estimation_old_metal_sale_details`, `ret_old_metal_type`, `ret_old_metal_category`, `ret_esti_old_metal_stone_details` | — | Controller (edit load) |
| `order_details()` | 1567-1575 | `ret_order` | — | Controller (order link) |
| `pan_available()` | 2860-2878 | `customer` | — | Controller AJAX |
| `passport_available()` | 2969-2988 | `customer` | — | Controller AJAX |
| `tag_reserve_check()` | 1077-1087 | `ret_taging`, `ret_order_items` | — | Controller (tag validation) |
| `updateCustomer()` | 830-871 | — | `customer` | Controller `updateCustomer()` |
| `updateData()` | 47-83 | `{any}` (SHOW COLUMNS) | `{any}` (generic) | Controller (save/edit) |

---

## JS → Controller AJAX Map

> Every `$.ajax` call in `ret_estimation.js` mapped to its backend endpoint.
> **Round 3 (2026-03-24)**: Complete scan of all 100 `url:` lines. Previous map had 46 calls; now 100% documented.

### Estimation Controller Endpoints

| JS Line | JS Context | AJAX URL | Controller Method |
|---|---|---|---|
| L559 | Order search autocomplete | `/admin_ret_estimation/getOrderBySearch` | `getOrderBySearch()` |
| L1035 | Tag barcode scan | `/admin_ret_estimation/getTaggingScanBySearch` | `getTaggingScanBySearch()` |
| L1535 | Tag barcode scan (2nd call) | `/admin_ret_estimation/getTaggingScanBySearch` | `getTaggingScanBySearch()` |
| L2009 | Metal purity rate — tag row | `/admin_ret_estimation/get_metal_purity_rate` | `get_metal_purity_rate()` |
| L2055 | Collection tag search | `/admin_ret_estimation/getTaggingSearchByCollection` | `getTaggingSearchByCollection()` |
| L2351 | Tag search by ID | `/admin_ret_estimation/getTaggingBySearch` | `getTaggingBySearch()` |
| L4267 | Old metal rate fetch | `/admin_ret_estimation/get_all_old_metal_rates` | `get_all_old_metal_rates()` |
| L4295 | Old metal category | `/admin_ret_estimation/get_old_metal_category` | `get_old_metal_category()` |
| L4325 | Metal types dropdown | `/admin_ret_estimation/getMetalTypes` | `getMetalTypes()` |
| L4381 | Old metal type | `/admin_ret_estimation/get_old_metal_type` | `get_old_metal_type()` |
| L5505 | Delete estimation | `/admin_ret_estimation/estimation/delete/{id}` | `estimation('delete')` |
| L5591 | Save new customer | `/admin_ret_estimation/createNewCustomer` | `createNewCustomer()` |
| L5659 | Customer search | `/admin_ret_estimation/getCustomersBySearch` | `getCustomersBySearch()` |
| L5853 | Tag search (2nd instance) | `/admin_ret_estimation/getTaggingBySearch` | `getTaggingBySearch()` |
| L6221 | Non-tag lots | `/admin_ret_estimation/getNonTagLots` | `getNonTagLots()` |
| L6335 | Purity rate (catalog) | `/admin_ret_estimation/get_metal_purity_rate` | `get_metal_purity_rate()` |
| L6381 | Product search (catalog) | `/admin_ret_estimation/getProductBySearch` | `getProductBySearch()` |
| L6597 | Purity rate (custom) | `/admin_ret_estimation/get_metal_purity_rate` | `get_metal_purity_rate()` |
| L6643 | Custom product search | `/admin_ret_estimation/getCustomProductBySearch` | `getCustomProductBySearch()` |
| L6787 | Design search | `/admin_ret_estimation/getProductDesignBySearch` | `getProductDesignBySearch()` |
| L6901 | Sub-design search | `/admin_ret_estimation/getProductSubDesignBySearch` | `getProductSubDesignBySearch()` |
| L6973 | Design search (2nd) | `/admin_ret_estimation/getProductDesignBySearch` | `getProductDesignBySearch()` |
| L8270 | Non-tag stock | `/admin_ret_estimation/get_non_tag_stock` | `get_non_tag_stock()` |
| L9853 | Purity rate helper | `/admin_ret_estimation/get_purity_rate` | `get_purity_rate()` |
| L9873 | Profile settings | `/admin_ret_estimation/estimation/profile` | `estimation('profile')` |
| L10353 | Estimation list DataTable | `/admin_ret_estimation/estimation/ajax` | `estimation('ajax')` |
| L10767 | Metal types (2nd) | `/admin_ret_estimation/getMetalTypes` | `getMetalTypes()` |
| L14558 | Stone details (edit load) | `/admin_ret_estimation/get_stone_details` | `get_stone_details()` |
| L15062 | Other material details | `/admin_ret_estimation/get_other_material_details` | `get_other_material_details()` |
| L15301 | Stone details (2nd) | `/admin_ret_estimation/get_stone_details` | `get_stone_details()` |
| L15678 | Old metal stone details | `/admin_ret_estimation/get_old_metal_stone_details` | `get_old_metal_stone_details()` |
| L16170 | Old metal rate (single) | `/admin_ret_estimation/get_old_metal_rate` | `get_old_metal_rate()` |
| L16210 | Village search | `/admin_ret_estimation/ajax_get_village` | `ajax_get_village()` |
| L16316 | Employee dropdown | `/admin_ret_estimation/get_employee` | `get_employee()` |
| L16408 | Order details lookup | `/admin_ret_estimation/get_order_details` | `get_order_details()` |
| L16464 | Order search (2nd) | `/admin_ret_estimation/getOrderBySearch` | `getOrderBySearch()` |
| L16738 | Partial tag search | `/admin_ret_estimation/getPartialTagSearch` | `getPartialTagSearch()` |
| L17146 | Update customer | `/admin_ret_estimation/updateCustomer` | `updateCustomer()` |
| L17206 | Customer by ID | `/admin_ret_estimation/get_customer` | `get_customer()` |
| L17304 | Customer by ID (2nd) | `/admin_ret_estimation/get_customer` | `get_customer()` |
| L17449 | Customer purchase history | `/admin_ret_estimation/getCustomerDet` | `getCustomerDet()` |
| L17731 | Customer bill history | `/admin_ret_estimation/getCustomerBill` | ⚠️ **NEW** `getCustomerBill()` — undocumented controller method |
| L18379 | Sub-design search (3rd) | `/admin_ret_estimation/getProductSubDesignBySearch` | `getProductSubDesignBySearch()` |
| L19045 | Active product list | `/admin_ret_estimation/get_ActiveProduct` | `get_ActiveProduct()` |
| L19135 | Cancel order tag (AJAX) | `/admin_ret_estimation/cancel_order_tag/ajax` | `cancel_order_tag()` — ajax sub-route variant |
| L19319 | Custom product search (2nd) | `/admin_ret_estimation/getCustomProductBySearch` | `getCustomProductBySearch()` |
| L19559 | Section by branch | `/admin_ret_estimation/get_sectionBranchwise` | `get_sectionBranchwise()` |
| L19576 | Non-tag products | `/admin_ret_estimation/getNonTagproducts` | `getNonTagproducts()` |
| L19641 | Product search (2nd) | `/admin_ret_estimation/getProductBySearch` | `getProductBySearch()` |
| L19778 | Non-tag stock (2nd) | `/admin_ret_estimation/get_non_tag_stock` | `get_non_tag_stock()` |
| L20085 | Load estimation for edit | `/admin_ret_estimation/estimation/est_edit/{id}` | ⚠️ **NEW** `estimation('est_edit')` — sub-route undocumented |
| L21524 | Tag status check | `/admin_ret_estimation/get_tag_status_details` | `get_tag_status_details()` |
| L21596 | Tag image by ID | `/admin_ret_estimation/get_tag_img_by_id` | `get_tag_img_by_id()` |
| L22342 | Tag barcode scan (3rd) | `/admin_ret_estimation/getTaggingScanBySearch` | `getTaggingScanBySearch()` |
| L23128 | Village management | `/admin_ret_estimation/get_village` | `get_village()` |
| L23208 | Village by pincode | `/admin_ret_estimation/get_village_by_pincode` | `get_village_by_pincode()` |
| L23358 | Purities list | `/admin_ret_estimation/get_purities` | `get_purities()` |
| L23414 | Purities list (2nd) | `/admin_ret_estimation/get_purities` | `get_purities()` |
| L23466 | Purities list (3rd) | `/admin_ret_estimation/get_purities` | `get_purities()` |
| L29513 | Old metal product | `/admin_ret_estimation/get_old_metal_Product` | `get_old_metal_Product()` |
| L29930 | VA range | `/admin_ret_estimation/get_va_range` | `get_va_range()` |
| L31130 | Financial year list | `/admin_ret_estimation/getFinancialYr` | `getFinancialYr()` |

> **Total internal**: 61 call sites → **39 unique controller routes** (2 new: `getCustomerBill`, `estimation/est_edit`)

### Cross-Module AJAX Calls (to other controllers)

| JS Line | AJAX URL | External Controller | Purpose |
|---|---|---|---|
| L4617 | `/admin_ret_tagging/getAvailableTaxGroupItems` | Tagging | Tax group items |
| L9826 | `/admin_ret_tagging/getStoneItems` | Tagging | Stone item types |
| L9886 | `/admin_ret_tagging/getStoneTypes` | Tagging | Stone types master |
| L9906 | `/admin_ret_tagging/get_ActiveUOM` | Tagging | UOM list |
| L9926 | `/admin_ret_tagging/getOtherCharges` | Tagging | Other charges list |
| L9946 | `/admin_ret_tagging/getAvailableMaterials` | Tagging | Materials list |
| L10602 | `/get/active_metals` | Global/API | Active metals |
| L10662 | `/admin_ret_tagging/get_lot_ids` | Tagging | Lot IDs |
| L10702 | `/admin_ret_tagging/get_tag_types` | Tagging | Tag types |
| L10740 | `/admin_ret_catalog/purity/active_purities` | Catalog | Active purities |
| L10780 | `/admin_ret_tagging/getAvailableTaxGroups` | Tagging | Tax groups |
| L10818 | `/admin_ret_tagging/getDesignPurityByDesignId` | Tagging | Design purity |
| L10870 | `/admin_ret_tagging/getDesignStonesByDesignId` | Tagging | Design stones |
| L10906 | `/admin_ret_tagging/getDesignMaterialsByDesignId` | Tagging | Design materials |
| L10942 | `/admin_ret_tagging/getTagStoneByTagId` | Tagging | Tag stones |
| L13593 | `/admin_ret_tagging/get_metal_rates_by_branch` | Tagging | Metal rates |
| L13684 | `/admin_ret_billing/get_scheme_accounts` | Billing | Scheme accounts |
| L13870 | `/admin_ret_billing/get_scheme_accounts` | Billing | Scheme accounts (2nd) |

---

## Table → Methods Reverse Map

> For any table bug, instantly find all methods reading/writing it.

| Table | Read By (Model Methods) | Written By |
|---|---|---|
| `ret_estimation` | `get_entry_records`, `ajax_getEstimationList`, `get_data`, `generateEstiNo`, `get_bill_no_format_detail` | Controller `estimation('save')` via `insertData`/`updateData` |
| `ret_estimation_items` | `getOtherEstimateItemsDetails`, `get_est_tag_details`, `est_non_tag_items`, `est_home_bill`, `child_tag_details`, `getEstTags`, `get_partial_details` | Controller `estimation('save')` via `insertBatchData`; `deleteData` on edit |
| `ret_estimation_item_stones` | `getOtherEstimateItemsDetails`, `get_stone_details`, `get_est_stone_wt`, `get_est_tag_details`, `est_non_tag_items`, `est_home_bill`, `get_child_tag_stone_details` ⚠️ (no return — always NULL) | Controller `estimation('save')` via `insertBatchData`; `deleteData` on edit |
| `ret_estimation_item_other_materials` | `getOtherEstimateItemsDetails`, `get_other_material_details`, `get_est_tag_details`, `est_non_tag_items`, `est_home_bill` | Controller `estimation('save')` via `insertBatchData`; `deleteData` on edit |
| `ret_estimation_other_charges` | `getOtherEstimateItemsDetails`, `get_other_estcharges`, `get_est_tag_details`, `est_non_tag_items`, `est_home_bill` | Controller `estimation('save')` via `insertBatchData`; `deleteData` on edit |
| `ret_estimation_old_metal_sale_details` | `getOtherEstimateItemsDetails`, `old_metal`, `get_oldmetalDetails` | Controller `estimation('save')` via `insertData`; `deleteData` on edit |
| `ret_esti_old_metal_stone_details` | `getOtherEstimateItemsDetails`, `get_old_metal_stone_details`, `old_metal` | Controller `estimation('save')` via `insertBatchData`; `deleteData` on edit |
| `ret_est_chit_utilization` | `getOtherEstimateItemsDetails`, `get_chit_details` | Controller `estimation('save')` via `insertData`; `deleteData` on edit |
| `ret_est_gift_voucher_details` | `getOtherEstimateItemsDetails` | Controller `estimation('save')` via `insertData`; `deleteData` on edit |
| `ret_estimation_items` *(VA slab columns)* | `getOtherEstimateItemsDetails`, `get_est_tag_details`, `est_non_tag_items`, `est_home_bill` (reads `wastage_slab_id`, `wast_slab_value`, `max_va_per`, `act_wast_per` columns) | ✅ **CORRECTED Round 2**: VA slab data is stored as **columns on `ret_estimation_items`** — NOT a separate table. Columns: `wastage_slab_id`, `wast_slab_value`, `max_va_per`, `act_wast_per`, `tag_blk_disc`. Confirmed in controller L439-447. |
| `ret_est_sales_return_utilization` | `getOtherEstimateItemsDetails` | Controller `estimation('save')` via `insertData`; `deleteData` on edit |
| `ret_estimation_other_inventory_issue` | `getOtherEstimateItemsDetails` | Controller `estimation('save')` via `insertData`; `deleteData` on edit |
| `ret_taging` | `getTaggingBySearch`, `getTaggingScanBySearch`, `getTaggingSearchByCollection`, `get_tag_details`, `get_non_tag_stock_details`, `get_partial_details`, `getNonTagLots`, `getPartialTagSearch`, `getPartialTagSearch_1`, `tag_reserve_check`, `get_purchase_details`, `est_non_tag_items`, `get_tag_status_details` | — (read-only from Estimation) |
| `ret_taging_charges` | `get_charges` | — (read-only) |
| `ret_charges` | `get_charges` | — (read-only) |
| `ret_issue_receipt` | `get_credit_pending_details`, `get_billing_advance_details` | — (read-only from Estimation) |
| `ret_advance_transfer` | `get_billing_advance_details` | — (read-only) |
| `ret_advance_utilized` | `get_billing_advance_details` | — (read-only) |
| `ret_advance_refund` | `get_billing_advance_details` | — (read-only) |
| `ret_billing_advance` | `get_order_advance_details` | — (read-only) |
| `ret_taging_stone` | `getTaggingBySearch`, `getTagStoneDetails`, `get_tag_stone_details`, `get_tag_stone_wt`, `get_partly_home_bill_stones` | — (read-only) |
| `customer` | `get_customer`, `getCustomerDet`, `getAvailableCustomers`, `get_entry_records`, `ajax_getEstimationList`, `pan_available`, `gst_available`, `aadhar_available`, `passport_available`, `dl_available` | `createNewCustomer`, `updateCustomer` |
| `ret_branchwise_rate` | `get_branchwise_rate`, `get_metal_purity_rate`, `get_purity_rate` | — (read-only) |
| `ret_billing` | `get_credit_pending_details`, `get_credit_collection_details`, `ajax_getEstimationList`, `get_bill_no_format_detail` | — (read-only from Estimation) |
| `ret_day_closing` | `getBranchDayClosingData` | — (read-only) |
| `ret_old_metal_rate` | `getOldMetalRate`, `get_old_metal_rate`, `get_all_old_metal_rates` | — (read-only) |
| `ret_product_master` | `getProductBySearch`, `getCustomProductBySearch`, `get_ActiveProduct`, `getNonTagproducts`, `get_old_metal_Product`, `get_mc_va_limit` | — (read-only) |
