# Tagging Module — Data Flow

> **Module**: Tagging
> **Last Updated**: 2026-02-24

---

## 1. Tag Creation Flow (Save)

```
[User fills form.php] → [JS builds lt_item POST array] → [Controller: tagging('save')]
    │
    ├── 1. Read $_POST['lt_item'] (raw — bypasses CI input)
    ├── 2. Get day closing date for branch
    ├── 3. $this->db->trans_begin()
    ├── 4. Loop over lot items:
    │   ├── Sum piece, gross_wt, less_wt, net_wt
    │   └── If cross-branch → create ret_branch_transfer record
    ├── 5. Loop over items again:
    │   ├── Build $arrayTag with ~50 fields
    │   ├── INSERT into ret_taging → get $insId
    │   ├── If has section → INSERT ret_section_tag_status_log
    │   ├── Handle images (base64 → file → ret_taging_images)
    │   ├── Handle stones → INSERT ret_taging_stone (batch)
    │   ├── Handle materials → INSERT ret_taging_material (batch)
    │   ├── Handle charges → INSERT ret_taging_stone (other charges)
    │   ├── Generate tag_code → UPDATE ret_taging
    │   ├── Generate QR code → save file
    │   ├── If cross-branch → INSERT ret_branch_transfer_items
    │   ├── Update lot inward balance (decrement)
    │   └── If lot fully tagged → UPDATE ret_lot_inwards (status=1)
    ├── 6. $this->db->trans_complete()
    └── 7. Return JSON: {status, msg, tag_insert_id, ref_no}
```

### Tables Written During Save

| Table                        | Operation      | Fields                                                                       |
| ---------------------------- | -------------- | ---------------------------------------------------------------------------- |
| `ret_taging`                 | INSERT         | ~50 fields (tag_code, product_id, design_id, purity, weights, charges, etc.) |
| `ret_taging_stone`           | INSERT (batch) | stone_id, tag_id, wt, pieces, rate, amount                                   |
| `ret_taging_material`        | INSERT (batch) | material_id, tag_id, wt, rate, amount                                        |
| `ret_taging_images`          | INSERT         | tag_id, image, date_add, is_default                                          |
| `ret_branch_transfer`        | INSERT         | branch_trans_code, from/to branch, pieces, weights                           |
| `ret_branch_transfer_items`  | INSERT         | bt_id, tag_id, tag_code                                                      |
| `ret_section_tag_status_log` | INSERT         | tag_id, date, status, from/to branch/section                                 |
| `ret_lot_inward_detail`      | UPDATE         | Decrement balance fields                                                     |
| `ret_lot_inwards`            | UPDATE         | lot_status when fully tagged                                                 |

---

## 2. Tag Update Flow (update_tagging_data)

```
[JS collects form data] → [AJAX POST] → [Controller: update_tagging_data()]
    │
    ├── 1. $this->input->post() for all fields
    ├── 2. Build $tagData array (~50 fields)
    ├── 3. Handle old tag data restoration:
    │   ├── Get old lot_inward_detail → restore balance
    │   ├── Get old stone data → restore lot stone balance
    │   └── Get old non-tag stock → restore
    ├── 4. UPDATE ret_taging SET ... WHERE tag_id = $id
    ├── 5. DELETE old stones → INSERT new stones
    ├── 6. DELETE old materials → INSERT new materials
    ├── 7. Handle images (add/remove/replace)
    ├── 8. Handle branch transfer if branch changed
    ├── 9. Deduct from new lot_inward_detail balance
    ├── 10. Handle non-tag stock updates
    └── 11. Return JSON response
```

---

## 3. Tag Delete/OTP Flow

```
[User clicks delete] → [JS: send_tag_otp()]
    │
    ├── Controller: send_tag_otp()
    │   ├── Get branch OTP mobile
    │   ├── Generate 6-digit OTP
    │   ├── Store OTP in session
    │   ├── Send SMS via sms_model
    │   └── Return {status: true, msg: 'OTP Sent'}
    │
    └── [User enters OTP] → [JS: verify_otp()]
        │
        ├── Controller: verify_otp()
        │   ├── Compare session OTP with input
        │   ├── If match → UPDATE ret_taging SET tag_status = 2 (Deleted)
        │   ├── Restore lot inward balance
        │   ├── Restore non-tag stock
        │   └── Return {status: true}
        │
        └── If admin approval needed:
            └── Controller: admin_approval()
                ├── Check admin credentials
                └── Proceed with delete
```

---

## 4. Tag Scan Flow

```
[User scans QR / enters tag code] → [JS AJAX] → [Controller: get_tag_scan_details()]
    │
    ├── 1. Model: get_scanned_details($tag_id) → base tag + product + purity
    ├── 2. Model: getTagDetails($tag_id) → full details with stones, materials
    ├── 3. Model: getTagCharges($tag_id) → charges
    ├── 4. Model: get_rate_from_metal_and_purity() → current rate
    ├── 5. Calculate: total_value, tax_amount, final_price
    └── 6. Return comprehensive JSON with all tag data
```

---

## 5. Re-tagging Flow

```
[User selects tags for retag] → [Controller: create_retag()]
    │
    ├── 1. INSERT ret_retagging_process (header)
    ├── 2. For each old tag:
    │   ├── UPDATE ret_taging SET tag_status = 3 (Other Issue)
    │   ├── INSERT ret_retagging_process_items (link old tag to process)
    │   └── Copy old tag stones/materials to retag records
    ├── 3. Generate new lot:
    │   ├── For tagged items → generateRetaglot()
    │   │   ├── INSERT ret_lot_inwards (new lot)
    │   │   ├── INSERT ret_lot_inward_detail (lot items)
    │   │   └── INSERT ret_lot_stone_details (stones)
    │   └── For non-tag items → generateRetagNontaglot()
    │       ├── INSERT/UPDATE ret_non_tag_stock
    │       └── INSERT ret_lot_inwards if needed
    └── 4. Return {status, process_id}
```

---

## 6. Order Linking Flow

```
[User searches tag] → getTaggingBySearch() → displays matching tags
[User searches order] → getOrdersBySearch() → displays matching orders
[User links] → update_order_link()
    │
    ├── 1. UPDATE ret_taging SET id_orderdetails = $order_id WHERE tag_id = $tag_id
    ├── 2. UPDATE ret_customer_order_details SET tag_linked = 1
    └── 3. Return {status: true}
```

### Order Unlinking (OTP-gated)

```
[User clicks unlink] → send_order_unlink_otp() → SMS OTP
[User enters OTP] → verify_order_unlink_otp()
    │
    ├── 1. UPDATE ret_taging SET id_orderdetails = NULL WHERE tag_id = $tag_id
    ├── 2. UPDATE ret_customer_order_details SET tag_linked = 0
    └── 3. Return {status: true}
```

---

## 7. Collection Mapping Flow

```
[User selects tags + collection] → create_tag_collection()
    │
    ├── If new mapping:
    │   ├── Generate ref_no → generateRefNo()
    │   ├── INSERT ret_tag_collection (header)
    │   └── For each tag → INSERT ret_tag_collection_items
    └── If edit mapping:
        ├── DELETE old ret_tag_collection_items
        ├── INSERT new ret_tag_collection_items
        └── UPDATE ret_tag_collection header totals
```

---

## 8. Purchase Cost Update Flow

```
[User opens purchase cost page] → [update_purchase_cost()]
    │
    ├── 1. Get PO details → getPoDetailsforPC()
    ├── 2. Get PO stone details → get_po_item_stone_details()
    ├── 3. Calculate:
    │   ├── Metal value = net_wt × rate
    │   ├── Making charges (per gram / per piece / % on price)
    │   ├── Stone value from PO
    │   ├── Wastage value
    │   └── Total purchase cost
    ├── 4. UPDATE ret_taging SET tag_purchase_cost = $total
    └── 5. Return {status: true, purchase_cost: $total}
```

---

## 9. JS → Controller AJAX Endpoint Map (118 Endpoints)

> Added in Round 2. All `url:` patterns from `ret_tagging.js` mapped to controller endpoints.

### Internal Endpoints (→ `admin_ret_tagging`) — 88 URL hits (68 unique methods)

> ⚠️ **R5 Update**: Added 6 previously missing endpoints + duplicate caller locations. True unique controller method count = 68; 20 lines are duplicate calls to same endpoint from different JS callers.

| JS Line | AJAX URL                                          | Controller Method                | JS Caller Function                 |
| ------- | ------------------------------------------------- | -------------------------------- | ---------------------------------- |
| 4583    | `admin_ret_tagging/lot_tag_detail`                | `lot_tag_detail()`               | `lot_tag_detail()`                 |
| 6064    | `admin_ret_tagging/get_tag_types`                 | `get_tag_types()`                | `get_metal()`                      |
| 3054    | `admin_ret_tagging/getAvailableTaxGroupItems`     | `getAvailableTaxGroupItems()`    | (init block)                       |
| 4590    | `admin_ret_tagging/get_tagging_details`           | `get_tagging_details()`          | (list page init)                   |
| 5192    | `admin_ret_tagging/lot_tag_detail`                | `lot_tag_detail()`               | (alt caller)                       |
| 5910    | `admin_ret_tagging/get_img_by_id`                 | `get_img_by_id()`                | (edit form init)                   |
| 6054    | `admin_ret_tagging/get_tag_types` _(via get/)_    | `get_tag_types()`                | (legacy wrapper)                   |
| 6130    | `admin_ret_tagging/get_tag_purities`              | `get_tag_purities()`             | `get_metal()`                      |
| 6344    | `admin_ret_tagging/get_tag_types`                 | `get_tag_types()`                | `get_tag_types()`                  |
| 6186    | `admin_ret_tagging/get_lot_ids`                   | `get_lot_ids()`                  | (form init with `include_closed`)  |
| 6334    | `admin_ret_tagging/get_tag_types`                 | `get_tag_types()`                | `get_tag_types()`                  |
| 6410    | `admin_ret_tagging/getAvailableTaxGroups`         | `getAvailableTaxGroups()`        | `get_tag_taxgroups()`              |
| 6496    | `admin_ret_tagging/getStoneItems`                 | `getStoneItems()`                | `get_tag_stones()`                 |
| 6552    | `admin_ret_tagging/getAvailableMaterials`         | `getAvailableMaterials()`        | `get_tag_materials()`              |
| 6608    | `admin_ret_tagging/getDesignNosBySearch`          | `getDesignNosBySearch()`         | `getSearchDesignNo()`              |
| 6864    | `admin_ret_tagging/getDesignStonesByDesignId`     | `getDesignStonesByDesignId()`    | `getDesignStoneDetails()`          |
| 6940    | `admin_ret_tagging/getDesignMaterialsByDesignId`  | `getDesignMaterialsByDesignId()` | `getDesignMaterialsByDesignId()`   |
| 7016    | `admin_ret_tagging/getTagStoneByTagId`            | `getTagStoneByTagId()`           | `load_tag_stone_list_on_edit()`    |
| 7180    | `admin_ret_tagging/getTagMaterialByTagId`         | `getTagMaterialByTagId()`        | `load_tag_material_list_on_edit()` |
| 7644    | `admin_ret_tagging/get_tag_number`                | `get_tag_number()`               | `get_tag_number()`                 |
| 7800    | `admin_ret_tagging/get_prod_by_tagno`             | `get_prod_by_tagno()`            | `getSearchProd()`                  |
| 7936    | `admin_ret_tagging/getDesignNosBySearch`          | `getDesignNosBySearch()`         | `getSearchDes()`                   |
| 8672    | `admin_ret_tagging/get_tagging_details`           | `get_tagging_details()`          | `BulkEditDetails()`                |
| 8220    | `admin_ret_tagging/get_tag_details`               | `get_tag_details()`              | `get_tag_details()`                |
| 9526    | `admin_ret_tagging/get_img_by_id`                 | `get_img_by_id()`                | `view_tag_imgs()`                  |
| 9698    | `admin_ret_tagging/update_tag_img_by_id`          | `update_tag_img_by_id()`         | `update_tag_img_by_id()`           |
| 11966   | `admin_ret_tagging/update_tagging_data`           | `update_tagging_data()`          | `update_tagging_data()`            |
| 12446   | `admin_ret_tagging/admin_approval`                | `admin_approval()`               | `admin_approval()`                 |
| 12666   | `admin_ret_tagging/resendotp`                     | `resendotp()`                    | `resendotp()`                      |
| 12354   | `admin_ret_tagging/get_metal_rates_by_branch`     | `get_metal_rates_by_branch()`    | `get_metal_rates_by_branch()`      |
| 12894   | `admin_ret_tagging/get_employee`                  | `get_employee()`                 | `get_employee()`                   |
| 13226   | `admin_ret_tagging/get_lot_products`              | `get_lot_products()`             | `get_lot_products()`               |
| 13504   | `admin_ret_tagging/get_order_details`             | `get_order_details()`            | (lot page caller)                  |
| 14098   | `admin_ret_tagging/get_lot_designs`               | `get_lot_designs()`              | `get_lot_designs()`                |
| 14768   | `admin_ret_tagging/get_lot_inward_details`        | `get_lot_inward_details()`       | (form init caller)                 |
| 16599   | `admin_ret_tagging/getAvailableTaxGroupItems`     | `getAvailableTaxGroupItems()`    | `get_taxgroup_items()`             |
| 16647   | `admin_ret_tagging/getOtherCharges`               | `getOtherCharges()`              | `get_charges()`                    |
| 18604   | `admin_ret_tagging/getStoneItems`                 | `getStoneItems()`                | `get_stones()`                     |
| 18656   | `admin_ret_tagging/getStoneTypes`                 | `getStoneTypes()`                | `get_stone_types()`                |
| 18686   | `admin_ret_tagging/get_ActiveUOM`                 | `get_ActiveUOM()`                | `get_ActiveUOM()`                  |
| 18746   | `admin_ret_tagging/tagging/edit/{id}`             | `tagging('edit')`                | `update_tagging_details()`         |
| 19857   | `admin_ret_tagging/updateTag`                     | `updateTag()`                    | —                                  |
| 21243   | `admin_ret_tagging/get_ActiveSize`                | `get_ActiveSize()`               | `get_ActiveSize()`                 |
| 21719   | `admin_ret_tagging/get_active_design_products`    | `get_active_design_products()`   | `get_active_design_products()`     |
| 21935   | `admin_ret_tagging/get_duplicate_tag`             | `get_duplicate_tag()`            | —                                  |
| 22009   | `admin_ret_tagging/get_duplicate_tag`             | `get_duplicate_tag()`            | —                                  |
| 22257   | `admin_ret_tagging/send_tag_otp`                  | `send_tag_otp()`                 | `send_otp()`                       |
| 22445   | `admin_ret_tagging/verify_otp`                    | `verify_otp()`                   | —                                  |
| 22745   | `admin_ret_tagging/get_tag_scan_details`          | `get_tag_scan_details()`         | `get_tag_scan_details()`           |
| 22923   | `admin_ret_tagging/get_tag_marking/ajax`          | `get_tag_marking()`              | `set_tag_marking()`                |
| 23419   | `admin_ret_tagging/get_tag_edit_det/ajax`         | `get_tag_edit_det()`             | `get_tag_edit_det()`               |
| 23659   | `admin_ret_tagging/get_lot_ids`                   | `get_lot_ids()`                  | `get_tag_edit_lots()`              |
| 23895   | `admin_ret_tagging/update_tag`                    | `update_tag()`                   | `update_tag()`                     |
| 23971   | `admin_ret_tagging/get_employee`                  | `get_employee()`                 | `get_branchwise_emp()`             |
| 24147   | `admin_ret_tagging/get_lot_inward_details`        | `get_lot_inward_details()`       | `set_multiple_rows()`              |
| 24683   | `admin_ret_tagging/getTaggingBySearch`            | `getTaggingBySearch()`           | `getSearchTags()`                  |
| 25535   | `admin_ret_tagging/get_customer_order_details`    | `get_customer_order_details()`   | `get_customer_order_details()`     |
| 25675   | `admin_ret_tagging/get_CustomerOrders`            | `get_CustomerOrders()`           | `get_CustomerOrders()`             |
| 25795   | `admin_ret_tagging/getOrdersBySearch`             | `getOrdersBySearch()`            | `getSearchOrders()`                |
| 25955   | `admin_ret_tagging/getOrderDetailBySearch`        | `getOrderDetailBySearch()`       | `get_order_details()`              |
| 26233   | `admin_ret_tagging/update_order_link`             | `update_order_link()`            | `update_order_link()`              |
| 29729   | `admin_ret_tagging/tagging/save`                  | `tagging('save')`                | `createTag()`                      |
| 30129   | `admin_ret_tagging/tagging/tag_update`            | `tagging('tag_update')`          | `createTag()`                      |
| 30860   | `admin_ret_tagging/get_img_by_id`                 | `get_img_by_id()`                | `view_tag_images()`                |
| 33039   | `admin_ret_tagging/get_ActiveSubDesingns`         | `get_ActiveSubDesingns()`        | `get_ActiveTagSubDesingns()`       |
| 33147   | `admin_ret_tagging/get_ActiveSubDesingns`         | `get_ActiveSubDesingns()`        | `get_ActiveSubDesingnsFilter()`    |
| 33401   | `admin_ret_tagging/get_wastage_settings_details`  | `get_wastage_settings_details()` | `get_wastage_settings_details()`   |
| 34603   | `admin_ret_tagging/get_po_details`                | `get_po_details()`               | `update_po_details()`              |
| 35351   | `admin_ret_tagging/get_tag_charges/{id}`          | `get_tag_charges()`              | `display_charges_details()`        |
| 35491   | `admin_ret_tagging/get_tag_attributes/{id}`       | `get_tag_attributes()`           | `display_attribute_details()`      |
| 37455   | `admin_ret_tagging/add_to_transfer_tag`           | `add_to_transfer_tag()`          | `add_to_transfer_tag()`            |
| 38041   | `admin_ret_tagging/retagging/partly_sale`         | `retagging()`                    | `get_partlySaleDetails()`          |
| 38415   | `admin_ret_tagging/retagging/old_metal`           | `retagging()`                    | `get_OldMetalDetails()`            |
| 38797   | `admin_ret_tagging/retagging/ajax`                | `retagging()`                    | `get_retagging_details()`          |
| 40729   | `admin_ret_tagging/retagging/non_tag_details`     | `retagging()`                    | `get_non_tag_return_details()`     |
| 44341   | `admin_ret_tagging/create_retag`                  | `create_retag()`                 | `create_retag()`                   |
| 44881   | `admin_ret_tagging/retagging/process_list`        | `retagging()`                    | `get_stock_process_list()`         |
| 45131   | `admin_ret_tagging/get_ActiveCollection`          | `get_ActiveCollection()`         | `get_ActiveCollection()`           |
| 45531   | `admin_ret_tagging/create_tag_collection`         | `create_tag_collection()`        | `create_tag_collection()`          |
| 45651   | `admin_ret_tagging/collection_mapping/ajax`       | `collection_mapping()`           | `set_collection_mapping_list()`    |
| 46163   | `admin_ret_tagging/get_order_linked_tags`         | `get_order_linked_tags()`        | —                                  |
| 47511   | `admin_ret_tagging/get_old_tag`                   | `get_old_tag()`                  | `old_tag_id()`                     |
| 47711   | `admin_ret_tagging/get_section_details`           | `get_section_details()`          | `get_tag_Sections()`               |
| 48158   | `admin_ret_tagging/tagging/save_bulk_tag`         | `tagging('save_bulk_tag')`       | `createTag_bulk()`                 |
| 49102   | `admin_ret_tagging/getLotsforEmp`                 | —                                | `getLotsforEmp()`                  |
| 49206   | `admin_ret_tagging/get_lot_split_products`        | `get_lot_split_products()`       | `get_lot_split_products()`         |
| 49470   | `admin_ret_tagging/get_order_details`             | `get_order_details()`            | —                                  |
| 49790   | `admin_ret_tagging/getTaggedLot`                  | `getTaggedLot()`                 | `getTaggedLot()`                   |
| 49906   | `admin_ret_tagging/getTaggedRefNo`                | `getTaggedRefNo()`               | `getTaggedRefNo()`                 |
| 51830   | `admin_ret_tagging/get_prev_huid`                 | `get_prev_huid()`                | —                                  |
| 52858   | `admin_ret_tagging/retagging/non_tag_other_issue` | `retagging()`                    | `get_NonTagOtherIssue_details()`   |
| 53216   | `admin_ret_tagging/send_order_unlink_otp`         | `send_order_unlink_otp()`        | `orderunlink_otp()`                |
| 53284   | `admin_ret_tagging/verify_order_unlink_otp`       | `verify_order_unlink_otp()`      | `orderunlink_otp()`                |
| 53366   | `admin_ret_tagging/unlink_order_tags`             | `unlink_order_tags()`            | `tag_unlink()`                     |
| 53522   | `admin_ret_tagging/bulk_tag_edit_log/ajax`        | `bulk_tag_edit_log()`            | `bulk_tag_edit_log_list()`         |
| 55162   | `admin_ret_tagging/update_purchase_cost`          | `update_purchase_cost()`         | `validate_purchase_cost()`         |
| 55613   | `admin_ret_tagging/ret_duplicate_print_log_save`  | `ret_duplicate_print_log_save()` | `ret_duplicate_print_log_save()`   |

### Cross-Module Endpoints (→ Other Controllers) — 29 URL hits

> ⚠️ **R5 Update**: Added 12 previously missing cross-module endpoints including generic `get/` controller, estimation, billing, and legacy catalog endpoints.

#### Reports Controller (`admin_ret_reports`) — 7 hits

| JS Line | AJAX URL                                    | JS Caller Function          |
| ------- | ------------------------------------------- | --------------------------- |
| 21049   | `admin_ret_reports/get_ActiveProduct`       | `get_ActiveProduct()`       |
| 21161   | `admin_ret_reports/get_ActiveNontagProduct` | `get_ActiveNontagProduct()` |
| 21539   | `admin_ret_reports/get_Activedesign`        | `get_Activedesign()`        |
| 23335   | `admin_ret_reports/update_green_tag`        | `update_green_tag()`        |
| 33287   | `admin_ret_reports/get_ActiveSubDesign`     | `get_ActiveSubDesingns()`   |
| 44501   | `admin_ret_reports/active_category`         | `get_category()`            |
| 44637   | `admin_ret_reports/get_ActiveProduct`       | `get_category_product()`    |

#### Catalog Controller (`admin_ret_catalog`) — 10 hits

| JS Line | AJAX URL                                             | JS Caller Function                   |
| ------- | ---------------------------------------------------- | ------------------------------------ |
| 16637   | `admin_ret_catalog/charges/getActiveChargesList`     | `get_charges()`                      |
| 37047   | `admin_ret_catalog/active_metals`                    | `get_ActiveMetals()`                 |
| 37159   | `admin_ret_catalog/ajax_getPurity`                   | `get_ActivePurity()`                 |
| 37207   | `admin_ret_catalog/ret_metalpurity`                  | `get_metal_rate_purities()`          |
| 44769   | `admin_ret_catalog/category/cat_purity`              | `get_cat_purity()`                   |
| 47599   | `admin_ret_catalog/get_sectionBranchwise`            | `get_ActiveSections()`               |
| 50010   | `admin_ret_catalog/karigar/active_list`              | `get_ActiveKarigar()`                |
| 50314   | `admin_ret_catalog/get_quality_code`                 | `getActive_quality_code()`           |
| 50422   | `admin_ret_catalog/getQualityDiamondRates`           | `getQualityDiamondRates()`           |
| 51414   | `admin_ret_catalog/getStoneRateSettings`             | `getStoneRateSettings()`             |
| 52162   | `admin_ret_catalog/getLooseStoneProductRateSettings` | `getLooseStoneProductRateSettings()` |

#### Billing Controller (`admin_ret_billing`) — 1 hit

| JS Line | AJAX URL                                | JS Caller Function     |
| ------- | --------------------------------------- | ---------------------- |
| 16589   | `admin_ret_billing/getAllTaxgroupItems` | `get_taxgroup_items()` |

#### Estimation Controller (`admin_ret_estimation`) — 2 hits

| JS Line | AJAX URL                                        | JS Caller Function         |
| ------- | ----------------------------------------------- | -------------------------- |
| 12892   | `admin_ret_estimation/get_employee`             | `get_employee()`           |
| 14604   | `admin_ret_estimation/getProductDesignBySearch` | `getSearchDesForProduct()` |

#### Generic / Legacy Controllers — 7 hits

| JS Line | AJAX URL                    | Target Controller | JS Caller Function    |
| ------- | --------------------------- | ----------------- | --------------------- |
| 3694    | `get/active_color`          | Generic `get`     | (diamond color init)  |
| 3786    | `get/active_purity`         | Generic `get`     | (diamond purity init) |
| 3914    | `get/active_color`          | Generic `get`     | (duplicate caller)    |
| 4002    | `get/active_masters`        | Generic `get`     | (master lookup init)  |
| 4226    | `get/metal_info_list/{id}`  | Generic `get`     | (metal info by prod)  |
| 4442    | `product/delect_prodDetail` | Legacy `product`  | (product delete)      |
| 4510    | `admin_catalog/remove_img`  | Legacy `catalog`  | `remove_img()`        |
| 6054    | `get/active_metals`         | Generic `get`     | (metals init)         |

> ⚠️ **RISK-013 (NEW)**: Lines 3694, 3786, 3914, 4002, 4226, 6054 call a bare `get/` controller that may not exist in the CI3 routing. Lines 4442, 4510 call legacy controllers (`product/`, `admin_catalog/`) that differ from the standard `admin_ret_catalog` naming. These may be dead code from an older version.

### Dynamic/Variable Endpoint — 1

| JS Line | URL               | Note                                                         |
| ------- | ----------------- | ------------------------------------------------------------ |
| 34231   | `href` (variable) | `_ajaxCallPost()` — generic AJAX helper, URL passed as param |

---

## 10. JS Financial Calculation Functions

> Added in Round 2. These are the most bug-prone JS functions — all handle currency/weight calculations.

### Tag Sale Value Calculations

| Function                                 | Line  | Purpose                                   | Key Variables                                     |
| ---------------------------------------- | ----- | ----------------------------------------- | ------------------------------------------------- |
| `calculateTagSaleValue()`                | 15757 | Calculate sale value for new tag form     | `net_wt, sell_rate, mc_value, wastage, stone_amt` |
| `calculateOrderTagSaleValue()`           | 16265 | Calculate sale value for order-linked tag | Same as above + order premium                     |
| `calculateTagEditSaleValue()`            | 19409 | Sale value during tag edit                | Same + old vs new comparison                      |
| `calculateTagFormSaleValue()`            | 27243 | Form-level sale value                     | Aggregated from all rows                          |
| `calculateTagPreviewSaleValue()`         | 32071 | Preview panel sale value                  | Read-only display calc                            |
| `caculate_bulk_edit_purchase_sale()`     | 52642 | Bulk edit purchase/sale recalc            | Batch-level aggregation                           |
| `caculate_bulk_edit_purchase_sale_row()` | 54054 | Individual row in bulk edit               | Per-row calc                                      |

### Tax Calculations

| Function                        | Line          | Purpose                                    |
| ------------------------------- | ------------- | ------------------------------------------ |
| `calculate_base_value_tax()`    | 16677 / 28549 | Tax from base sale value (2 instances!)    |
| `calculate_arrived_value_tax()` | 16741 / 28617 | Tax from arrived value (2 instances!)      |
| `calculate_inclusiveGST()`      | 28497         | Reverse-calculate GST from inclusive price |

> ⚠️ **RISK**: `calculate_base_value_tax` and `calculate_arrived_value_tax` are defined **twice** (lines 16677 & 28549, 16741 & 28617). The second definition overwrites the first. This is a bug or code smell.

### Stone/Material Calculations

| Function                         | Line          | Purpose                                                     |
| -------------------------------- | ------------- | ----------------------------------------------------------- |
| `calculate_stone_amount()`       | 17818         | Single stone row amount                                     |
| `calculate_total_stone_amount()` | 17890         | Sum all stone amounts                                       |
| `calculateBalanceStones()`       | 20697 / 20905 | Balance stone weight/pieces after allocation (2 instances!) |

### Wastage & Making Charge

| Function               | Line  | Purpose                         |
| ---------------------- | ----- | ------------------------------- |
| `calc_wastage()`       | 33918 | Calculate wastage weight/amount |
| `calc_pur_wastage()`   | 34015 | Calculate purchase wastage      |
| `calculate_total_mc()` | 29306 | Total making charge             |
| `get_mc_va_limit()`    | 29122 | Fetch MC/VA limits from server  |

### Re-tagging Calculations

| Function                            | Line  | Purpose                   |
| ----------------------------------- | ----- | ------------------------- |
| `calculate_SalesReturn_RowTotal()`  | 39219 | Sales return row total    |
| `calculate_old_metal_retag_row()`   | 39433 | Old metal retag value     |
| `calculate_PartlySale_RowTotal()`   | 38357 | Partly sold row total     |
| `calculate_OldMetal_RowTotal()`     | 38735 | Old metal row total       |
| `calculate_partly_sale_retag_row()` | 40665 | Partly sale retag value   |
| `calculateNT_OtherIssue_total()`    | 53016 | Non-tag other issue total |

### Collection & Summary

| Function                              | Line  | Purpose                            |
| ------------------------------------- | ----- | ---------------------------------- |
| `calculate_collection_tag()`          | 45303 | Collection mapping totals          |
| `calculate_tag_summary()`             | 37239 | Overall tag summary (other metals) |
| `calculate_other_metal_amount()`      | 36815 | Other metal row amount             |
| `calculate_average_purity_and_rate()` | 53854 | Weighted average purity/rate       |

---

## 11. JS Function Categorization (300+ functions)

### Category Counts

| Category                   | Count    | Key Functions                                                                      |
| -------------------------- | -------- | ---------------------------------------------------------------------------------- |
| **AJAX Data Fetch**        | ~55      | `get_metal()`, `get_lot_products()`, `get_lot_designs()`, etc.                     |
| **Financial Calculations** | ~30      | `calculateTagSaleValue()`, `calculate_stone_amount()`, etc.                        |
| **Form Validation**        | ~20      | `validateTagDetailRow()`, `validateStoneDetailRow()`, `validate_bulk_edit()`       |
| **UI/DOM Manipulation**    | ~80      | `create_new_stone_row()`, `remove_img()`, `prodInfo_list()`                        |
| **DataTable Rendering**    | ~15      | `set_tag_list()`, `set_tagging_list()`, `set_collection_mapping_list()`            |
| **Modal Handlers**         | ~15      | `openStoneModal()`, `openChargeModal()`, `openAttributeModal()`, `openHuidModal()` |
| **Retag Operations**       | ~20      | `create_retag()`, `get_retagging_details()`, `get_partlySaleDetails()`             |
| **Order Linking**          | ~10      | `getSearchTags()`, `getSearchOrders()`, `update_order_link()`                      |
| **Image Handling**         | ~15      | `preview_image()`, `remove_stn_img()`, `validate_image()`, `take_snapshot()`       |
| **Attribute/Charge**       | ~20      | `add_tag_attribute()`, `display_charges_details()`, `bulk_edit_attributes()`       |
| **Print/Export**           | ~5       | `tagging_print()`, `fntaggedItemsExcelReport()`                                    |
| **Utility/Helper**         | ~15      | `isInValid()`, `isLoaded()`, `formatJsonString()`, `s2ab()`                        |
| **TOTAL**                  | **~300** | —                                                                                  |
