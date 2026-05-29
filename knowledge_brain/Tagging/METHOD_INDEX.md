# Tagging Module — Method Index

> **Module**: Tagging
> **Last Updated**: 2026-02-24

---

## Controller Methods: `Admin_ret_tagging` (107 active methods)

> ⚠️ **Verified R4**: Regex finds 116 `function` declarations, but 9 are inside a `/* */` comment block (L8817-L9136: `get_printer_code` duplicate, `build_epl_header`, `build_dual_label_content`, `determine_product_type`, `build_gold_silver_layout`, `build_diamond_layout`, `build_silver_mrp_layout`, `build_loose_stone_layout`, `format_label_text`). Active callable methods = **107** (excl. `__construct`).

### Image Handling

| Method                   | Lines     | Params                     | Purpose                   | Tables Used         |
| ------------------------ | --------- | -------------------------- | ------------------------- | ------------------- |
| `set_image()`            | 85-101    | `$id, $img_path, $file`    | Upload product image      | —                   |
| `upload_img()`           | 103-161   | `$outputImage, $dst, $img` | Image resize/save         | —                   |
| `rrmdir()`               | 163-185   | `$path`                    | Recursive dir remove      | —                   |
| `remove_img()`           | 187-205   | `$file, $id`               | Remove product image      | —                   |
| `base64ToFile()`         | 207-233   | `$imgBase64`               | Base64 → temp file        | —                   |
| `imgTobase64()`          | 235-247   | `$path`                    | File → base64             | —                   |
| `save_base64_image()`    | 6398-6422 | `$base64_string, $path`    | Save base64 image to path | —                   |
| `update_tag_img_by_id()` | 8180-8313 | — (AJAX)                   | Update tag images         | `ret_taging_images` |
| `get_img_by_id()`        | 8315-8325 | — (AJAX)                   | Get tag images            | `ret_taging_images` |

### Tag CRUD

| Method                  | Lines     | Params                      | Purpose                                                 | Tables Written                                                                                                                                                        | Tables Read                                 |
| ----------------------- | --------- | --------------------------- | ------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------- |
| `tagging()`             | 255-3003  | `$type, $id, $tag_print_id` | **Router**: add/edit/list/save/clone/reprint/bulk       | `ret_taging, ret_taging_stone, ret_taging_material, ret_taging_images, ret_branch_transfer, ret_branch_transfer_items, ret_section_tag_status_log`                    | `ret_lot_inwards, ret_product_master, etc.` |
| `updateTag()`           | 3019-3225 | — (AJAX)                    | Update single tag record                                | `ret_taging, ret_taging_stone, ret_taging_material, ret_taging_images`                                                                                                | `ret_taging`                                |
| `update_tagging_data()` | 3667-5066 | — (AJAX)                    | **MEGA**: Full tag save with stones/materials/images/BT | `ret_taging, ret_taging_stone, ret_taging_material, ret_taging_images, ret_branch_transfer, ret_branch_transfer_items, ret_section_tag_status_log, ret_non_tag_stock` | Multiple                                    |
| `update_tag()`          | 5874-5965 | — (AJAX)                    | Quick tag update                                        | `ret_taging, ret_taging_stone`                                                                                                                                        | `ret_taging`                                |
| `update_tag_mark()`     | 5802-5860 | `$status, $id`              | Update tag mark status                                  | `ret_taging`                                                                                                                                                          | —                                           |

### Tag Query/Lookup

| Method                             | Lines     | Purpose              | Model Method Called                      |
| ---------------------------------- | --------- | -------------------- | ---------------------------------------- |
| `get_tag_types()`                  | 3413-3421 | Fetch tag types      | `getAvailablePurities()`                 |
| `get_tag_purities()`               | 3423-3431 | Fetch purities       | `getAvailablePurities()`                 |
| `get_lot_ids()`                    | 3433-3443 | Available lots       | `getAvailableLots()`                     |
| `getDesignNosBySearch()`           | 3445-3453 | Design search        | `getAvailableDesigns()`                  |
| `getDesignDetails()`               | 3455-3463 | Design info          | `getDesignDetails()`                     |
| `getDesignPurityByDesignId()`      | 3465-3473 | Design purities      | `getDesignPurityByDesignId()`            |
| `getDesignStonesByDesignId()`      | 3475-3483 | Design stones        | `getDesignStonesByDesignId()`            |
| `getTagStoneByTagId()`             | 3485-3493 | Tag stones           | `getTagStoneByTagId()`                   |
| `getTagMaterialByTagId()`          | 3495-3503 | Tag materials        | `getTagMaterialByTagId()`                |
| `getDesignMaterialsByDesignId()`   | 3505-3513 | Design materials     | `getDesignMaterialsByDesignId()`         |
| `getAvailableTaxGroups()`          | 3515-3523 | Tax groups           | `getAvailableTaxGroups()`                |
| `getAvailableTaxGroupItems()`      | 3525-3533 | Tax group items      | `getAvailableTaxGroupItems()`            |
| `getStoneItems()`                  | 3535-3543 | Stone items          | `getAvailableStones()`                   |
| `getOtherCharges()`                | 3545-3553 | Other charges        | `getChargesDetails()`                    |
| `getStoneTypes()`                  | 3555-3563 | Stone types          | `getAvailableStoneTypes()`               |
| `get_ActiveUOM()`                  | 3565-3573 | Active UOM           | `get_ActiveUOM()`                        |
| `getAvailableMaterials()`          | 3575-3583 | Materials            | `getAvailableMaterials()`                |
| `get_metal_rates_by_branch()`      | 3585-3597 | Branch metal rates   | `get_branchwise_rate()`                  |
| `get_tag_number()`                 | 3601-3617 | Tags in lot/branch   | `get_tag_numbr()`                        |
| `get_prod_by_tagno()`              | 3619-3633 | Product by tag       | `get_prod_by_tagno()`                    |
| `get_tag_details()`                | 3635-3665 | Full tag details     | `get_tag_details()`                      |
| `get_lot_inward_details()`         | 5268-5288 | Lot inward           | `get_lot_inward_details()`               |
| `get_lot_products()`               | 5290-5304 | Lot products         | `get_lot_products()`                     |
| `get_lot_split_products()`         | 5306-5320 | Split products       | `get_lot_split_products()`               |
| `get_lot_designs()`                | 5322-5338 | Lot designs          | `get_lot_designs()`                      |
| `get_tagging_details()`            | 5340-5360 | Tagging details list | `get_tagging_details()`                  |
| `get_tag_detail_list()`            | 5362-5370 | Tag detail DataTable | `get_tag_details()`                      |
| `lot_tag_detail()`                 | 5372-5400 | Lot tag detail       | `ajax_getTaggingList()`                  |
| `get_duplicate_tag()`              | 5404-5416 | Duplicate check      | `get_duplicate_tag()`                    |
| `get_tag_scan_details()`           | 5598-5740 | Tag scan full detail | `get_scanned_details(), getTagDetails()` |
| `get_order_details()`              | 5744-5754 | Orders by lot        | `get_order_details()`                    |
| `get_tag_marking()`                | 5756-5800 | Tag marks            | `get_tag_marking()`                      |
| `get_tag_edit_det()`               | 5862-5872 | Tag edit details     | `get_tag_edit_det()`                     |
| `get_employee()`                   | 5967-5977 | Employee list        | `get_employee()`                         |
| `get_ActiveSize()`                 | 5979-5989 | Size list            | `get_ActiveSize()`                       |
| `get_active_design_products()`     | 6298-6308 | Design products      | `get_active_design_products()`           |
| `get_ActiveSubDesingns()`          | 6310-6320 | Sub-designs          | `get_ActiveSubDesingns()`                |
| `get_wastage_settings_details()`   | 6326-6336 | Wastage settings     | `get_wastage_settings_details()`         |
| `get_attributes_from_subdesign()`  | 6342-6358 | Subdesign attrs      | `getTagAttributes()`                     |
| `get_product_charges()`            | 6360-6372 | Product charges      | `getTagCharges()`                        |
| `get_tag_charges()`                | 6374-6384 | Tag charges          | `getTagCharges()`                        |
| `get_tag_attributes()`             | 6386-6396 | Tag attributes       | `getTagAttributes()`                     |
| `get_rate_from_metal_and_purity()` | 6424-6444 | Metal rate           | `get_rate_from_metal_and_purity()`       |
| `get_po_details()`                 | 6470-6482 | PO details           | `getPoDetailsforPC()`                    |
| `get_order_linked_tags()`          | 8104-8114 | Linked tags          | `get_order_linked_tags()`                |
| `get_CustomerOrders()`             | 8329-8339 | Customer orders      | `get_CustomerOrder()`                    |
| `get_customer_order_details()`     | 8341-8351 | Order details        | `get_CustomerOrdersDetails()`            |
| `get_mc_va_limit()`                | 8353-8371 | MC/VA limits         | `get_mc_va_limit()`                      |
| `get_old_tag()`                    | 8373-8383 | Old tag lookup       | `get_old_tag()`                          |
| `getTaggedLot()`                   | 8419-8429 | Tagged lots          | `getTaggedLot()`                         |
| `getTaggedRefNo()`                 | 8431-8441 | Tagged ref nos       | `getTaggedRefNo()`                       |
| `get_section_details()`            | 8444-8454 | Sections             | `get_section_details()`                  |
| `get_prev_huid()`                  | 9152-9162 | Previous HUID        | `get_prev_huid()`                        |
| `get_tax_details()`                | 9334-9340 | Tax details          | (settings model)                         |

### Tag Code & QR Generation

| Method                      | Lines     | Purpose                                 |
| --------------------------- | --------- | --------------------------------------- |
| `generateTagCode()`         | 6148-6218 | Generate unique tag code from last code |
| `generateTagsByRefNo()`     | 3227-3411 | Generate batch tags by lot ref number   |
| `generate_tagqrcode()`      | 6248-6260 | QR code for single tag                  |
| `generate_retagqrcode()`    | 6262-6294 | QR code for retag process               |
| `generate_tagqrcode_bulk()` | 8385-8417 | Bulk QR generation                      |
| `get_tagArray()`            | 8455-8597 | Tag data array for printing             |
| `generate_printer_code()`   | 8599-8641 | Printer command generation              |
| `get_printer_code()`        | 8665-8813 | Get printer code data                   |
| `downloadFile()`            | 8645-8663 | Download generated file                 |

### Order Linking / Unlinking

| Method                      | Lines     | Purpose                    |
| --------------------------- | --------- | -------------------------- |
| `getTaggingBySearch()`      | 5993-6001 | Search tags for linking    |
| `getOrdersBySearch()`       | 6003-6013 | Search customer orders     |
| `getOrderDetailBySearch()`  | 6015-6025 | Order detail lookup        |
| `update_order_link()`       | 6027-6142 | Link tag to customer order |
| `unlink_order_tags()`       | 8116-8176 | Unlink tag from order      |
| `send_order_unlink_otp()`   | 9164-9227 | Send OTP for unlink        |
| `verify_order_unlink_otp()` | 9229-9271 | Verify unlink OTP          |

### Re-tagging

| Method                     | Lines     | Purpose                         |
| -------------------------- | --------- | ------------------------------- |
| `retagging()`              | 6608-6700 | Retagging page loader           |
| `create_retag()`           | 6704-7313 | Create retag process            |
| `generateRetaglot()`       | 7317-7518 | Generate retagged lot           |
| `generateRetagNontaglot()` | 7522-7850 | Generate non-tag lot from retag |

### OTP / Security

| Method             | Lines     | Purpose                    |
| ------------------ | --------- | -------------------------- |
| `admin_approval()` | 5068-5156 | Admin approval for changes |
| `resendotp()`      | 5158-5248 | Resend OTP                 |
| `send_sms()`       | 5250-5266 | SMS utility                |
| `send_tag_otp()`   | 5418-5516 | OTP for tag delete/edit    |
| `verify_otp()`     | 5518-5592 | OTP verification           |
| `validate_huid()`  | 6222-6246 | HUID validation            |

### Collection Mapping

| Method                    | Lines     | Purpose                     |
| ------------------------- | --------- | --------------------------- |
| `get_ActiveCollection()`  | 7858-7868 | Active collections dropdown |
| `collection_mapping()`    | 7870-7984 | Collection mapping page     |
| `create_tag_collection()` | 7986-8100 | Create/update collection    |

### Branch Transfer

| Method                  | Lines     | Purpose                  |
| ----------------------- | --------- | ------------------------ |
| `bt_tag_list()`         | 3005-3017 | Branch transfer tag list |
| `add_to_transfer_tag()` | 6486-6602 | Add tag to transfer      |

### Misc / Utility

| Method                           | Lines     | Purpose                        |
| -------------------------------- | --------- | ------------------------------ |
| `index()`                        | 79-83     | Empty default                  |
| `delete_tag_attribute()`         | 6446-6466 | Delete tag attribute           |
| `isEmptySetDefault()`            | 9138-9150 | Null check helper              |
| `calculate_base_value_tax()`     | 9274-9302 | Tax calc (base)                |
| `calculate_arrived_value_tax()`  | 9304-9332 | Tax calc (arrived)             |
| `bulk_tag_edit_log()`            | 9342-9378 | Bulk edit log page             |
| `update_purchase_cost()`         | 9381-9747 | **MEGA**: Purchase cost update |
| `ret_duplicate_print_log_save()` | 9748-9787 | Save dup print log             |

---

## Model Methods: `Ret_tag_model` (131 methods)

### Generic CRUD

| Method                                                   | Lines     | Purpose                                    |
| -------------------------------------------------------- | --------- | ------------------------------------------ |
| `insertData($data, $table)`                              | 35-69     | Generic insert with default-value handling |
| `updateData($data, $id_field, $id_value, $table)`        | 71-107    | Generic update with default-value handling |
| `insertBatchData($data, $table)`                         | 133-169   | Batch insert                               |
| `updateBatchData($data, $table, $id_field, $id_value)`   | 173-217   | Batch update                               |
| `updateRecord($data, $where_array, $table)`              | 253-280   | Update with array-based WHERE              |
| `deleteData($id_field, $id_value, $table)`               | 284-304   | Generic delete                             |
| `DuplicateRecord($table, $pk, $where_val, $where_field)` | 8811-8831 | Duplicate check                            |

### Tag Listing & Details

| Method                                  | Lines       | Purpose                                 |
| --------------------------------------- | ----------- | --------------------------------------- |
| `ajax_getTaggingList(...)`              | 360-625     | Main tag listing with filters (raw SQL) |
| `get_entry_records($tag_id)`            | 629-697     | Single tag record (raw SQL)             |
| `get_entry_records_by_lot($tag_lot_id)` | 701-789     | Tags by lot (raw SQL)                   |
| `get_tag_details()`                     | 2089-2585   | Full tag details with joins (raw SQL)   |
| `getTagDetails($tag_id)`                | 4854-5077   | Alternative tag details (raw SQL)       |
| `get_tag_details_by_tag_id($tag_id)`    | 7735-7767   | Simple tag by ID                        |
| `get_tagging_details(...)`              | 4562-4694   | Tagging details list with filters       |
| `get_tagging_det($tag_id)`              | 8787-8803   | Simple tag detail                       |
| `get_scanned_details($tag_id)`          | 6134-6182   | Tag scan view                           |
| `get_tag_edit_det($data)`               | 6497-6705   | Tag edit details                        |
| `get_tag_marking($data)`                | 6258-6351   | Tag marking list                        |
| `getEstTaglist($data)`                  | 6383-6489   | Estimation tag list                     |
| `get_duplicate_tag($data)`              | 4702-4846   | Check duplicate tags                    |
| `get_bulk_tag_edit_log_list(...)`       | 10150-10198 | Bulk edit log data                      |

### Lot & Inward

| Method                                              | Lines     | Purpose                                    |
| --------------------------------------------------- | --------- | ------------------------------------------ |
| `getLotRefNo($tag_lot_id)`                          | 312-352   | Get lot ref_no                             |
| `getAvailableLots($include_closed)`                 | 2945-3177 | Available lots (raw SQL, complex)          |
| `get_lot_products($lot_no, $SearchTxt)`             | 3181-3274 | Products in lot                            |
| `get_lot_split_products($lot_no, $id_employee)`     | 3278-3366 | Split products                             |
| `get_lot_designs($lot_no, $lo_product, $searchTxt)` | 3370-3414 | Lot designs                                |
| `get_lot_inward_details(...)`                       | 3466-3898 | **LARGE**: Lot inward details with balance |
| `get_lotInward($id)`                                | 969-985   | Simple lot inward                          |

### Stone & Material Details

| Method                                         | Lines       | Purpose                               |
| ---------------------------------------------- | ----------- | ------------------------------------- |
| `get_stone_details($tag_id)`                   | 793-809     | Tag stone details                     |
| `get_stone_Detail($tag_id)`                    | 5314-5342   | Alternative stone details             |
| `get_stoneDetails(...)`                        | 3902-4270   | **LARGE**: Stone details with balance |
| `get_balance_details(...)`                     | 4274-4558   | Balance details                       |
| `getTagStoneByTagId($tag_id)`                  | 1609-1645   | Tag stones                            |
| `getTagMaterialByTagId($tag_id)`               | 1649-1689   | Tag materials                         |
| `getDesignStonesByDesignId($design_id)`        | 1569-1605   | Design stones                         |
| `getDesignMaterialsByDesignId($design_id)`     | 1693-1729   | Design materials                      |
| `getTagStoneDetails($tagid)`                   | 9983-10022  | Tag stone full details                |
| `getTagStoneEditByTagId($tag_id)`              | 10201-10221 | Stone edit details                    |
| `get_lot_stone_details($id_lot_inward_detail)` | 10223-10235 | Lot stone details                     |

### Re-tagging

| Method                                             | Lines     | Purpose                  |
| -------------------------------------------------- | --------- | ------------------------ |
| `get_reTagDetails($id_process)`                    | 5530-5714 | Retag process details    |
| `getTagDetailsby_lot($lot_id)`                     | 5718-5922 | Tags by lot (for retag)  |
| `getTagByRefNo($ref_no)`                           | 5930-6126 | Tags by ref number       |
| `get_retagging_details($data)`                     | 8095-8219 | Retagging list           |
| `get_other_issue_retagging_details($data)`         | 7998-8085 | Other issue retag        |
| `get_retag_stone_details($tag_id)`                 | 8227-8270 | Retag stone details      |
| `get_retag_other_issue_stone_details($tag_id)`     | 8274-8315 | Other issue retag stones |
| `get_non_tag_details($data)`                       | 8323-8374 | Non-tag details          |
| `get_non_tag_otherissue_details($data)`            | 8378-8426 | Non-tag other issue      |
| `get_partly_sale_details($data)`                   | 8439-8556 | Partly sold details      |
| `get_partly_sold_stone_details($tag_id)`           | 8560-8624 | Partly sold stones       |
| `get_old_metal_details($data)`                     | 8629-8721 | Old metal details        |
| `getOldMetalSalesStoneDetails($old_metal_sale_id)` | 8727-8777 | Old metal stone          |

### Master Data Lookups

| Method                                   | Lines       | Purpose               |
| ---------------------------------------- | ----------- | --------------------- |
| `get_empty_record()`                     | 813-941     | Empty record template |
| `GetFinancialYear()`                     | 949-965     | Financial year        |
| `getAvailabletags()`                     | 989-1005    | Available tags        |
| `getUOMDetails()`                        | 1009-1025   | UOM data              |
| `getAvailablePurities()`                 | 1029-1045   | Purities              |
| `getAvailableDesigns($SearchTxt)`        | 1329-1341   | Designs by search     |
| `getDesignDetails($design_id)`           | 1345-1357   | Design info           |
| `getDesignPurityByDesignId($design_id)`  | 1361-1385   | Design purities       |
| `getDesignSizesByDesignId($design_id)`   | 1389-1421   | Design sizes          |
| `getAvailableStones()`                   | 1425-1457   | Stone master          |
| `getChargesDetails()`                    | 1469-1485   | Charges master        |
| `getTagCharges($tag_id, $tag_display)`   | 1489-1517   | Tag charges           |
| `getAvailableStoneTypes()`               | 1529-1541   | Stone types           |
| `get_ActiveUOM()`                        | 1549-1561   | Active UOM            |
| `getAvailableMaterials()`                | 1733-1765   | Materials             |
| `getAvailableTaxGroups()`                | 1769-1789   | Tax groups            |
| `getAvailableTaxGroupItems($taxgroupid)` | 1793-1821   | Tax group items       |
| `get_ActiveSize($id_product)`            | 6757-6773   | Sizes by product      |
| `get_ActiveCollection()`                 | 9149-9165   | Active collections    |
| `get_employee()`                         | 6713-6749   | Employees             |
| `get_section_details()`                  | 10087-10103 | Sections              |
| `getProductDivision()`                   | 8839-8851   | Product divisions     |

### Tag Code & Numbering

| Method                          | Lines     | Purpose                    |
| ------------------------------- | --------- | -------------------------- |
| `code_number_generator()`       | 1825-1893 | Generate next tag code     |
| `get_financialyear_by_status()` | 1897-1921 | Active financial year      |
| `get_last_code_no()`            | 1925-1941 | Last used code             |
| `getlastTagCode($product_id)`   | 1953-1973 | Last tag code for product  |
| `get_tag_numbr(...)`            | 1985-2049 | Tag number in lot/branch   |
| `get_prod_by_tagno(...)`        | 2053-2085 | Product by tag number      |
| `generateRefNo()`               | 9173-9233 | Generate collection ref no |
| `getLastCollectionDet()`        | 9237-9253 | Last collection detail     |

### Rate & Pricing

| Method                                | Lines     | Purpose                         |
| ------------------------------------- | --------- | ------------------------------- |
| `get_all_purities_for_product(...)`   | 2593-2681 | All purities + rates            |
| `check_is_mrp($tag_id)`               | 2685-2737 | MRP check                       |
| `get_branchwise_rate($id_branch)`     | 2741-2825 | Branch metal rates              |
| `get_rate_from_metal_and_purity(...)` | 7962-7993 | Rate by metal+purity            |
| `get_mc_va_limit(...)`                | 9715-9875 | MC/VA limits with weight ranges |

### Wastage & Settings

| Method                                           | Lines     | Purpose                                       |
| ------------------------------------------------ | --------- | --------------------------------------------- |
| `get_wastage_settings_details(...)`              | 7433-7571 | Wastage settings by product/design/sub-design |
| `get_weight_range_details($id_selling_settings)` | 7579-7595 | Weight ranges for a setting                   |
| `get_ret_settings($settings)`                    | 2865-2881 | Retail settings lookup                        |

### Tag Attributes

| Method                                     | Lines     | Purpose                |
| ------------------------------------------ | --------- | ---------------------- |
| `getTagAttributes($tag_id, $attr_id)`      | 7607-7683 | Tag attributes         |
| `get_category_from_productid($product_id)` | 7687-7731 | Category by product    |
| `get_active_design_products($data)`        | 7051-7159 | Active design products |
| `get_ActiveSubDesingns($data)`             | 7171-7293 | Sub-designs            |

### Stock Operations

| Method                                      | Lines     | Purpose                |
| ------------------------------------------- | --------- | ---------------------- |
| `checkNonTagItemExist($data)`               | 8863-8923 | Check non-tag stock    |
| `updateNTData($data, $arith)`               | 8951-8967 | Update non-tag stock   |
| `checkPurchaseItemsStockExist(...)`         | 8979-9023 | Purchase stock check   |
| `get_product_details($id_product)`          | 9031-9047 | Product details        |
| `updateOldMetalStockData(...)`              | 9055-9075 | Update old metal stock |
| `get_stock_process_details($data)`          | 9077-9125 | Stock process details  |
| `get_tagged_details($id_lot_inward_detail)` | 2829-2861 | Tagged details         |

### Branch & Misc

| Method                                | Lines     | Purpose          |
| ------------------------------------- | --------- | ---------------- |
| `get_headOffice()`                    | 2885-2901 | Head office info |
| `get_branchName($branch)`             | 2909-2941 | Branch name      |
| `getBranchDayClosingData($id_branch)` | 6359-6375 | Day closing data |
| `get_tax_percentage($lot_no)`         | 3418-3462 | Tax percentage   |

### Order & Customer

| Method                                    | Lines       | Purpose                |
| ----------------------------------------- | ----------- | ---------------------- |
| `get_order_details($lot_no)`              | 6186-6250   | Order details          |
| `getTaggingBySearch(...)`                 | 6782-6939   | Tag search for linking |
| `getOrdersBySearch(...)`                  | 6947-6983   | Order search           |
| `getOrderDetailBySearch(...)`             | 6991-7015   | Order detail           |
| `get_order_linked_tags(...)`              | 9413-9474   | Linked tags            |
| `get_cus_order_details($id_orderdetails)` | 10115-10146 | Customer order details |
| `get_CustomerOrdersDetails($data)`        | 9595-9643   | Customer order details |
| `get_CustomerOrder($data)`                | 9651-9707   | Customer orders        |

### Collection Mapping

| Method                                        | Lines     | Purpose                 |
| --------------------------------------------- | --------- | ----------------------- |
| `get_collection_mapping_list()`               | 9265-9333 | Collection mapping list |
| `get_collection_mapping_det($id_tag_mapping)` | 9345-9397 | Collection detail       |

### Image & HUID

| Method                         | Lines       | Purpose               |
| ------------------------------ | ----------- | --------------------- |
| `get_img_by_id($tag_id)`       | 9487-9503   | Tag images            |
| `deleteData_bulk_img($tag_id)` | 9507-9523   | Delete all tag images |
| `checkImageAvail($tag_id)`     | 9527-9563   | Check image exists    |
| `check_isDefault_img($tag_id)` | 9567-9583   | Default image check   |
| `validate_huid($sku_id)`       | 7023-7039   | HUID validation       |
| `getTagHuid($tag_id)`          | 10030-10062 | Tag HUID details      |
| `get_prev_huid($huid)`         | 10070-10086 | Previous HUID         |

### Purchase Order

| Method                                   | Lines     | Purpose                      |
| ---------------------------------------- | --------- | ---------------------------- |
| `getPoDetailsforPC($data)`               | 7775-7823 | PO details for purchase cost |
| `get_po_item_stone_details($po_item_id)` | 7831-7855 | PO item stones               |

### OTP

| Method                              | Lines       | Purpose           |
| ----------------------------------- | ----------- | ----------------- |
| `getBrnachOtpRegMobile($id_branch)` | 10105-10113 | Branch OTP mobile |

### Tagged Lot/Ref

| Method                      | Lines     | Purpose            |
| --------------------------- | --------- | ------------------ |
| `getTaggedLot()`            | 9899-9935 | Tagged lots        |
| `getTaggedRefNo()`          | 9939-9979 | Tagged ref numbers |
| `get_other_metals($tag_id)` | 5303-5310 | Other metals       |

---

## Key JS Functions (Primary — from `ret_tagging.js`)

| Function             | Purpose                       | AJAX Target                        |
| -------------------- | ----------------------------- | ---------------------------------- |
| `get_tagging_list()` | Load tag list                 | `lot_tag_detail`                   |
| `set_tag_list()`     | Render tag DataTable          | —                                  |
| `lot_tag_detail()`   | Load lot tag detail           | `admin_ret_tagging/lot_tag_detail` |
| `set_tagging_list()` | Render tagging list DataTable | —                                  |
| `tagging_print()`    | Print tags                    | —                                  |
| `prodInfo_list()`    | Load product info list        | —                                  |
| `dia_remove()`       | Remove diamond row            | —                                  |
| `m_remove()`         | Remove material row           | —                                  |
| `deleteProdDetail()` | Delete product detail         | AJAX                               |
| `remove_img()`       | Remove image                  | AJAX                               |

> **Note**: The JS file has 532 functions. Only primary/frequently used functions are listed here. Full JS AJAX map (118 endpoints) and financial calc functions (30) are in `DATA_FLOW.md`.

---

## Table → Methods Reverse Map (Step 7d)

> For any table bug, instantly find all methods that touch it.

### `ret_taging` (Primary)

| Operation  | Controller Methods                                                                                                                                                                             | Model Methods                                                                                                                                                                                                                                                                                                                                                                                                           |
| ---------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **SELECT** | `get_tag_details()`, `get_tag_scan_details()`, `lot_tag_detail()`, `get_tagging_details()`, `get_tag_edit_det()`, `get_tag_marking()`, `get_duplicate_tag()`                                   | `ajax_getTaggingList()`, `get_entry_records()`, `get_entry_records_by_lot()`, `get_tag_details()`, `getTagDetails()`, `get_tag_details_by_tag_id()`, `get_tagging_details()`, `get_tagging_det()`, `get_scanned_details()`, `get_tag_edit_det()`, `get_tag_marking()`, `getEstTaglist()`, `get_duplicate_tag()`, `check_is_mrp()`, `getTaggingBySearch()`, `getTaggedLot()`, `getTaggedRefNo()`, `get_tagged_details()` |
| **INSERT** | `tagging('save')`, `tagging('save_bulk_tag')`                                                                                                                                                  | `insertData()` (generic)                                                                                                                                                                                                                                                                                                                                                                                                |
| **UPDATE** | `tagging('tag_update')`, `updateTag()`, `update_tagging_data()`, `update_tag()`, `update_tag_mark()`, `update_order_link()`, `unlink_order_tags()`, `create_retag()`, `update_purchase_cost()` | `updateData()` (generic), `updateRecord()`                                                                                                                                                                                                                                                                                                                                                                              |
| **DELETE** | `verify_otp()` (soft delete via status=2)                                                                                                                                                      | `deleteData()` (generic)                                                                                                                                                                                                                                                                                                                                                                                                |

### `ret_taging_stone`

| Operation  | Controller Methods                                                | Model Methods                                                                                                                                 |
| ---------- | ----------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| **SELECT** | `getTagStoneByTagId()`, `get_tag_scan_details()`                  | `get_stone_details()`, `get_stone_Detail()`, `get_stoneDetails()`, `getTagStoneByTagId()`, `getTagStoneDetails()`, `getTagStoneEditByTagId()` |
| **INSERT** | `tagging('save')`, `update_tagging_data()`                        | `insertBatchData()`                                                                                                                           |
| **UPDATE** | `updateTag()`, `update_tag()`                                     | `updateBatchData()`                                                                                                                           |
| **DELETE** | `tagging('save')` (delete-then-reinsert), `update_tagging_data()` | `deleteData()`                                                                                                                                |

### `ret_taging_material`

| Operation  | Controller Methods                         | Model Methods             |
| ---------- | ------------------------------------------ | ------------------------- |
| **SELECT** | `getTagMaterialByTagId()`                  | `getTagMaterialByTagId()` |
| **INSERT** | `tagging('save')`, `update_tagging_data()` | `insertBatchData()`       |
| **UPDATE** | —                                          | `updateBatchData()`       |
| **DELETE** | `tagging('save')`, `update_tagging_data()` | `deleteData()`            |

### `ret_taging_images`

| Operation  | Controller Methods                          | Model Methods                                                   |
| ---------- | ------------------------------------------- | --------------------------------------------------------------- |
| **SELECT** | `get_img_by_id()`                           | `get_img_by_id()`, `checkImageAvail()`, `check_isDefault_img()` |
| **INSERT** | `tagging('save')`, `update_tag_img_by_id()` | `insertData()`                                                  |
| **DELETE** | `update_tag_img_by_id()`                    | `deleteData_bulk_img()`, `deleteData()`                         |

### `ret_tag_attributes`

| Operation  | Controller Methods                                        | Model Methods        |
| ---------- | --------------------------------------------------------- | -------------------- |
| **SELECT** | `get_tag_attributes()`, `get_attributes_from_subdesign()` | `getTagAttributes()` |
| **INSERT** | `tagging('save')`, `update_tagging_data()`                | `insertBatchData()`  |
| **DELETE** | `delete_tag_attribute()`                                  | `deleteData()`       |

### `ret_section_tag_status_log`

| Operation  | Controller Methods                                                                | Model Methods  |
| ---------- | --------------------------------------------------------------------------------- | -------------- |
| **SELECT** | —                                                                                 | —              |
| **INSERT** | `tagging('save')`, `update_tagging_data()`, `update_tag_mark()`, `create_retag()` | `insertData()` |

### `ret_lot_inwards` / `ret_lot_inward_detail`

| Operation  | Controller Methods                                                                                | Model Methods                                                                                                                                                                                                   |
| ---------- | ------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **SELECT** | `get_lot_inward_details()`, `get_lot_products()`, `get_lot_designs()`, `get_lot_split_products()` | `getLotRefNo()`, `getAvailableLots()`, `get_lot_products()`, `get_lot_split_products()`, `get_lot_designs()`, `get_lot_inward_details()`, `get_lotInward()`, `get_balance_details()`, `get_lot_stone_details()` |
| **UPDATE** | `tagging('save')` (balance deduction), `verify_otp()` (balance restore), `create_retag()`         | `updateData()`                                                                                                                                                                                                  |

### `ret_branch_transfer` / `ret_branch_transfer_items`

| Operation  | Controller Methods                                            | Model Methods  |
| ---------- | ------------------------------------------------------------- | -------------- |
| **INSERT** | `tagging('save')` (cross-branch tag), `add_to_transfer_tag()` | `insertData()` |
| **SELECT** | `bt_tag_list()`                                               | —              |

### `ret_tag_collection` / `ret_tag_mapping_detail`

| Operation  | Controller Methods        | Model Methods                                                   |
| ---------- | ------------------------- | --------------------------------------------------------------- |
| **SELECT** | `collection_mapping()`    | `get_collection_mapping_list()`, `get_collection_mapping_det()` |
| **INSERT** | `create_tag_collection()` | `insertData()`, `insertBatchData()`                             |
| **UPDATE** | `create_tag_collection()` | `updateData()`                                                  |

### `ret_retagging_process` / `ret_retagging_items`

| Operation  | Controller Methods | Model Methods                                                                |
| ---------- | ------------------ | ---------------------------------------------------------------------------- |
| **SELECT** | `retagging()`      | `get_reTagDetails()`, `get_retagging_details()`, `get_retag_stone_details()` |
| **INSERT** | `create_retag()`   | `insertData()`, `insertBatchData()`                                          |

### `ret_non_tag_stock`

| Operation         | Controller Methods                           | Model Methods                    |
| ----------------- | -------------------------------------------- | -------------------------------- |
| **SELECT**        | —                                            | `checkNonTagItemExist()`         |
| **INSERT/UPDATE** | `create_retag()`, `generateRetagNontaglot()` | `updateNTData()`, `insertData()` |

### `ret_dup_print_log`

| Operation  | Controller Methods               | Model Methods                  |
| ---------- | -------------------------------- | ------------------------------ |
| **SELECT** | `bulk_tag_edit_log()`            | `get_bulk_tag_edit_log_list()` |
| **INSERT** | `ret_duplicate_print_log_save()` | `insertData()`                 |

### Cross-Module Tables (Read Only)

| Table                        | Read By (Model Methods)                                                                          | Purpose                |
| ---------------------------- | ------------------------------------------------------------------------------------------------ | ---------------------- |
| `ret_product_master`         | `get_tag_details()`, `getTagDetails()`, `get_product_details()`, `get_category_from_productid()` | Product name, category |
| `ret_purity`                 | `getAvailablePurities()`, `getDesignPurityByDesignId()`, `get_all_purities_for_product()`        | Purity data + rates    |
| `ret_design_master`          | `getAvailableDesigns()`, `getDesignDetails()`, `get_lot_designs()`                               | Design name, specs     |
| `ret_sub_design_master`      | `get_ActiveSubDesingns()`                                                                        | Sub-design data        |
| `ret_stone`                  | `getAvailableStones()`, `getAvailableStoneTypes()`                                               | Stone master           |
| `ret_metal_rate`             | `get_branchwise_rate()`, `get_rate_from_metal_and_purity()`                                      | Metal pricing          |
| `ret_selling_settings`       | `get_wastage_settings_details()`, `get_weight_range_details()`                                   | MC/wastage config      |
| `employee`                   | `get_employee()`                                                                                 | Employee list          |
| `branch`                     | `get_branchName()`, `get_headOffice()`, `getBranchDayClosingData()`                              | Branch info            |
| `ret_customer_order_details` | `get_CustomerOrdersDetails()`, `get_order_linked_tags()`                                         | Order data             |
