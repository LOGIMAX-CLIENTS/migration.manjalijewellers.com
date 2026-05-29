# METHOD INDEX — Other Inventory
> **Alphabetical** | All line numbers from `admin_ret_other_inventory.php` (Controller) and `ret_other_inventory_model.php` (Model)

---

## Part A: Controller Methods (Alphabetical)

| Method | Lines | Type | Tables Read | Tables Written | JS Caller |
|---|---|---|---|---|---|
| `available_stock($type)` | L1113–1124 | Page/AJAX | `ret_other_inventory_purchase_items_details`, `ret_other_inventory_item`, `ret_other_inventory_size`, `branch` | — | `get_invnetory_item_list()` |
| `base64ToFile($imgBase64)` | L1464–1477 | Utility | — | FS (temp file) | — |
| `check_sku_id()` | L413–423 | AJAX | `ret_other_inventory_item` | — | `check_skuid_avail()` |
| `CheckIsNameDuplicate()` | L1503–1513 | AJAX | `ret_other_inventory_item_type` | — | `CheckIsNameDuplicate()` |
| `delete_product_mapping()` | L1155–1171 | AJAX | — | `ret_other_inventory_product_link` (DELETE) | `delete_product_mapping()` |
| `downloadFile($content, $filename)` | L1379–1387 | Utility | — | — | — |
| `generaterefCode($lastTagCode)` | L803–833 | Utility | — | — | — |
| `get_ActiveCategory()` | L1126–1131 | AJAX | `ret_other_inventory_item_type` | — | `get_other_item_category_list()` |
| `get_all_sizes()` | L1496–1500 | AJAX | `ret_other_inventory_size` | — | `fetchAllSizes()` |
| `get_bill_details()` | L997–1002 | AJAX | `ret_billing`, `customer`, `ret_day_closing` | — | `get_bill_details()` |
| `get_customer()` | L1009–1013 | AJAX | `customer` | — | `get_customer_list()` |
| `get_img_by_item_id()` | L713–718 | AJAX | `ret_other_inventory_purchase_images` | — | — |
| `get_inventory_category()` | L530–535 | AJAX | `ret_other_inventory_item_type` | — | `get_itemfor_list()`, `get_item_category_list()` |
| `get_invnetory_item()` | L1003–1008 | AJAX | `ret_other_inventory_item_type`, `ret_other_inventory_item`, `ret_other_inventory_purchase_items_details`, `ret_branch_transfer_other_inventory`, `ret_branch_transfer` | — | `get_invnetory_item()` |
| `get_other_inventory_details()` | L883–888 | AJAX | `ret_other_inventory_item`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items_details` | — | `get_other_inv_product_details()` |
| `get_other_inventory_item()` | L847–852 | AJAX | `ret_other_inventory_item` | — | `get_other_inventory_item()` |
| `get_other_inventory_product()` | L853–858 | AJAX | `ret_other_inventory_item`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items_details` | — | — |
| `get_other_inventory_ref_no()` | L877–882 | AJAX | `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_items_details` | — | `get_other_inventory_ref_no()` |
| `get_pro_detail_list()` | L859–863 | Page | — | — | — |
| `get_productMappedDetails()` | L1215–1220 | AJAX | `ret_product_master`, `ret_other_inventory_product_link`, `ret_other_inventory_item`, `ret_other_inventory_purchase_items_details` | — | `get_mapped_product_details()` |
| `get_supplier()` | L889–894 | AJAX | `ret_karigar` | — | `get_supplier()` |
| `imgTobase64($path)` | L1479–1485 | Utility | FS | — | — |
| `index()` | L33–35 | Default | — | — | — |
| `inventory_category($type)` | L425–516 | Page/AJAX | `ret_other_inventory_item_type` | `ret_other_inventory_item_type` | `get_item_category_list()`, category form |
| `isEmptySetDefault($value, $default)` | L1486–1493 | Utility | — | — | — |
| `issue_item($type)` | L895–995 | Page/AJAX | `ret_other_invnetory_issue`, `ret_other_inventory_item`, `ret_other_inventory_purchase_items_details`, `branch`, `employee`, `ret_billing`, `customer` | `ret_other_invnetory_issue`, `ret_other_inventory_purchase_items_details`, `ret_other_inventory_purchase_items_log`, `gift_mapping` | issue form |
| `item_size($type)` | L1016–1098 | Page/AJAX | `ret_other_inventory_size` | `ret_other_inventory_size` | `set_packing_item_size_list()` |
| `other_inventory($type)` | L117–412 | Page/AJAX | `ret_other_inventory_item`, `ret_other_inventory_reorder_settings`, `gift_mapping` | `ret_other_inventory_item`, `ret_other_inventory_reorder_settings`, `gift_mapping` | `set_other_inventory()` |
| `other_inventory_print($ref_no)` | L1295–1335 | Print | `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_items_details`, `ret_other_inventory_item` | FS (.prn download) | — |
| `otheritem_status($status, $id)` | L518–529 | AJAX | — | `ret_other_inventory_item_type` | category list status toggle |
| `packaging_item_size_status($status, $id)` | L1100–1110 | AJAX | — | `ret_other_inventory_size` | size list status toggle |
| `product_details($type)` | L719–802 | Page/AJAX | `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items` | `ret_other_inventory_purchase_items_details`, `ret_other_inventory_purchase_items_log` | product tagging form |
| `product_mapping($type)` | L1134–1153 | Page/AJAX | `ret_other_inventory_product_link`, `ret_product_master`, `ret_other_inventory_item` | — | `get_product_mapping_details()` |
| `product_other_inventory_print($id)` | L1336–1376 | Print | `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_items_details`, `ret_other_inventory_item` | FS (.prn download) | — |
| `product_tag_detail()` | L864–876 | AJAX | `ret_other_inventory_purchase_items_details`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase`, `ret_other_inventory_item` | — | `get_product_tag_detail()` |
| `purchase_entry($type)` | L538–712 | Page/AJAX | `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_images`, `ret_karigar`, `employee`, `country`, `state`, `city` | `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_images` | purchase form |
| `reorder_report($type)` | L1223–1241 | Page/AJAX | `ret_other_inventory_reorder_settings`, `ret_other_inventory_item`, `branch`, `ret_other_inventory_purchase_items_details` | — | `reorder_report()` |
| `set_image($id, $img_path, $file)` | L1426–1433 | Utility | FS | FS | — |
| `set_image_other($id, $skuid)` | L36–54 | Utility | FS | `ret_other_inventory_item` | — |
| `stock_details($type)` | L834–846 | Page/AJAX | `ret_other_inventory_item`, `ret_other_inventory_purchase_items_log`, `ret_other_inventory_item_type` | — | stock report page |
| `update_product_mapping()` | L1172–1213 | AJAX | `ret_product_master`, `ret_other_inventory_product_link` | `ret_other_inventory_product_link` | `update_product_mapping()` |
| `upload_img($outputImage, $dst, $img)` | L84–115 | Utility | — | FS | — |
| `get_tag_code($data, $no)` | L1388–1411 | Utility | — | — | — |
| `get_printer_code($tagprintcode)` | L1412–1425 | Utility | — | — | — |

---

## Part B: Model Methods (Alphabetical)

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `ajax_get_other_inventory()` | L656–671 | `ret_other_inventory_item`, `ret_uom`, `ret_other_inventory_item_type`, `ret_other_inventory_size`, `ret_other_inventory_purchase_items` | — | `other_inventory(default)` |
| `ajax_getotheritem()` | L220–235 | `ret_other_inventory_item_type`, `ret_other_inventory_item` | — | `inventory_category(default)` |
| `ajax_getPurchaseEntrylist($data)` | L292–310 | `ret_other_inventory_purchase`, `ret_karigar`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_items_details`, `ret_other_inventory_purchase_images` | — | `purchase_entry(default)` |
| `ajax_getProductlist()` | L154–166 | `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_items_details` | — | `product_details(default)` |
| `ajax_getOtherInventorySizeList($data)` | L477–481 | `ret_other_inventory_size` | — | `item_size(default)` |
| `check_other_inv_products_maping($id_product, $inv_des_otheritemid)` | L543–552 | `ret_other_inventory_product_link` | — | `update_product_mapping()` |
| `CheckIsNameDuplicate($name)` | L852–868 | `ret_other_inventory_item_type` | — | `CheckIsNameDuplicate()` |
| `deleteData($id_field, $id_value, $table)` | L47–52 | {any} | {any} | Many controller methods |
| `delete_gift_map_data($id)` | L679–685 | — | `gift_mapping` | `other_inventory(update)`, `issue_item` |
| `generateItemRefNo()` | L126–148 | `ret_other_inventory_purchase_items_details` | — | — (currently unused in controller) |
| `generatePurNo()` | L76–98 | `ret_other_inventory_purchase` | — | `purchase_entry(save)` |
| `get_ActiveCategory()` | L529–533 | `ret_other_inventory_item_type` | — | `get_ActiveCategory()` |
| `get_ActivePackagingItemSize()` | L490–495 | `ret_other_inventory_size` | — | `item_size(get_ActivePackagingItemSize)` |
| `get_ActiveProduct()` | L537–541 | `ret_product_master` | — | `update_product_mapping()` |
| `get_all_sizes()` | L846–850 | `ret_other_inventory_size` | — | `get_all_sizes()` |
| `get_AvailableStockDetails($data)` | L512–528 | `ret_other_inventory_purchase_items_details`, `ret_other_inventory_item`, `ret_other_inventory_item_type`, `ret_other_inventory_size`, `branch` | — | `available_stock(default)` |
| `get_bill_details($data)` | L499–508 | `ret_billing`, `customer`, `ret_day_closing` | — | `get_bill_details()` |
| `getBranchDayClosingData($id_branch)` | L70–74 | `ret_day_closing` | — | `other_inventory_stock()` |
| `get_currentBranchName($branch_id)` | L54–62 | `branch` | — | — |
| `get_customer()` | L427–433 | `customer` | — | `get_customer()` |
| `get_headOffice()` | L64–68 | `branch` | — | `purchase_entry(save)`, `product_details(save)` |
| `get_InventoryCategory($id_other_item_type)` | L243–251 | `ret_other_inventory_item`, `ret_other_inventory_item_type` | — | `issue_item(save)` |
| `get_inventory_category()` | L237–241 | `ret_other_inventory_item_type` | — | `get_inventory_category()` |
| `get_inv_chit_gift($id)` | L673–678 | `gift_mapping` | — | `other_inventory(edit)` |
| `get_inv_purchase_images($pur_id)` | L312–316 | `ret_other_inventory_purchase_images` | — | `ajax_getPurchaseEntrylist()`, `get_img_by_item_id()` |
| `get_inv_item_reorder_details($id_other_item)` | L188–195 | `ret_other_inventory_reorder_settings`, `branch` | — | `other_inventory(edit)` |
| `get_invnetory_item($data)` | L392–423 | `ret_other_inventory_item_type`, `ret_other_inventory_item`, `ret_branch_transfer_other_inventory`, `ret_branch_transfer`, `ret_other_inventory_purchase_items_details` | — | `get_invnetory_item()` |
| `get_item_mapping_details($data)` | L554–566 | `ret_other_inventory_product_link`, `ret_product_master`, `ret_other_inventory_item`, `ret_other_inventory_size` | — | `product_mapping(default)` |
| `get_OtherInventoryIssueDetails($data)` | L446–462 | `ret_other_invnetory_issue`, `branch`, `employee`, `ret_other_inventory_item`, `ret_billing`, `customer`, `ret_other_inventory_purchase_items_details` | — | `issue_item(default)` |
| `get_other_inventory_item()` | L279–284 | `ret_other_inventory_item` | — | `get_other_inventory_item()` |
| `get_other_inventory_details($data)` | L703–731 | `ret_other_inventory_item`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items_details` | — | `get_other_inventory_details()` |
| `get_other_inventory_print($ref_no)` | L625–639 | `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_items_details`, `ret_other_inventory_item` | — | `other_inventory_print()` |
| `get_other_inventory_product($id_other_item)` | L167–179 | `ret_other_inventory_purchase_items_details`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase`, `ret_other_inventory_item` | — | `product_tag_detail()` |
| `get_other_inventory_product_det($data)` | L687–702 | `ret_other_inventory_item`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items_details` | — | `get_other_inventory_product()` |
| `get_other_inventory_purchase_items_details($id, $branch, $pref, $pcs)` | L436–444 | `ret_other_inventory_purchase_items_details` | — | `issue_item(save)` |
| `get_other_inventory_records($id_other_item)` | L180–186 | `ret_other_inventory_item` | — | `other_inventory(edit)`, `other_inventory(update)`, `purchase_entry(print_qrcode)` |
| `get_other_inventory_ref_no()` | L732–763 | `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_items_details` | — | `get_other_inventory_ref_no()` |
| `get_other_item_records($id_other_item_type)` | L253–260 | `ret_other_inventory_item_type` | — | `inventory_category(edit)` |
| `get_packaging_size($id)` | L483–488 | `ret_other_inventory_size` | — | `item_size(edit)` |
| `get_product_linked_items($pro_id, $id_branch)` | L584–598 | `ret_other_inventory_product_link`, `ret_other_inventory_item`, `ret_other_inventory_purchase_items_details` | — | `get_productMappedDetails()` |
| `get_product_other_inventory_print($ref_no)` | L641–654 | `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_items_details`, `ret_other_inventory_item` | — | `product_other_inventory_print()` |
| `get_productMappedDetails($id_branch)` | L569–581 | `ret_product_master`, `ret_other_inventory_product_link`, `ret_other_inventory_item`, `ret_other_inventory_purchase_items_details` | — | `get_productMappedDetails()` |
| `get_purchase_gst_det($id)` | L821–835 | `ret_other_inventory_purchase_items` | — | `purchase_entry(purchase_details)` |
| `get_purchase_item_det($id)` | L798–820 | `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase`, `ret_other_inventory_item` | — | `purchase_entry(purchase_details)`, `ajax_getPurchaseEntrylist()` |
| `get_reorder_report($data)` | L604–622 | `ret_other_inventory_reorder_settings`, `ret_other_inventory_item`, `branch`, `ret_other_inventory_purchase_items_details` | — | `reorder_report(default)` |
| `get_ref_no_details($id)` | L837–843 | `ret_other_inventory_purchase_items_details` | — | `product_details(save)` |
| `get_supplier()` | L286–290 | `ret_karigar` | — | `get_supplier()` |
| `getActiveItemname()` | L271–275 | `ret_other_inventory_item_type` | — | `inventory_category(active_itemname)` |
| `getActiveskuid($SearchTxt, $searchField)` | L203–210 | `ret_other_inventory_item` | — | `other_inventory(active_skuid)` |
| `getBranchDayClosingData($id_branch)` | — | `ret_day_closing` | — | `other_inventory_stock()` |
| `getlastrefno()` | L149–153 | `ret_other_inventory_purchase_items_details` | — | `product_details(save)` |
| `getPurchaseDet($id)` | L766–796 | `ret_other_inventory_purchase`, `ret_karigar`, `country`, `state`, `city`, `employee`, `ret_other_inventory_purchase_items` | — | `purchase_entry(purchase_details)` |
| `insertBatchData($data, $table)` | L17–26 | — | {any} | — |
| `insertData($data, $table)` | L11–16 | — | {any} | Many controller methods |
| `other_inventory_stock($data)` | L319–388 | `ret_other_inventory_item`, `ret_other_inventory_item_type`, `ret_other_inventory_purchase_items_log` | — | `stock_details(default)` |
| `skuid_available($sku_id, $id_other_item)` | L464–472 | `ret_other_inventory_item` | — | `check_sku_id()` |
| `update_other_inventory($data, $id, $id_field, $table)` | L196–202 | — | `ret_other_inventory_item` | `other_inventory(save)`, `other_inventory(update)` |
| `update_otheritem($data, $id)` | L262–269 | — | `ret_other_inventory_item_type` | `inventory_category(update)`, `otheritem_status()` |
| `updateBatchData($data, $table, $id_field, $id_value)` | L27–39 | — | {any} | **⚠️ DEAD CODE — has print_r+exit** |
| `updateData($data, $id_field, $id_value, $table)` | L40–46 | — | {any} | Many controller methods |

---

## Part C: JS → Controller AJAX Map

### Internal Endpoints (same controller)

| JS Line | JS Function | AJAX URL | Controller Method |
|---|---|---|---|
| L288 | `get_item_category_list()` | `admin_ret_other_inventory/inventory_category/ajax` | `inventory_category(default)` |
| L427 | `set_other_inventory()` | `admin_ret_other_inventory/other_inventory/ajax` | `other_inventory(default)` |
| L563 | `check_skuid_avail()` | `admin_ret_other_inventory/check_sku_id` | `check_sku_id()` |
| L582 | `get_itemfor_list()` | `admin_ret_other_inventory/get_inventory_category` | `get_inventory_category()` |
| L632 | `get_item_size_list()` | `admin_ret_other_inventory/item_size/get_ActivePackagingItemSize` | `item_size(get_ActivePackagingItemSize)` |
| L657 | `get_other_item_category_list()` | `admin_ret_other_inventory/get_ActiveCategory` | `get_ActiveCategory()` |
| L681 | `get_other_inventory_ref_no()` | `admin_ret_other_inventory/get_other_inventory_ref_no` | `get_other_inventory_ref_no()` |
| L776 | `get_other_inv_product_details()` | `admin_ret_other_inventory/get_other_inventory_details` | `get_other_inventory_details()` |
| ~L850 | `get_other_inventory_item()` | `admin_ret_other_inventory/get_other_inventory_item` | `get_other_inventory_item()` |
| ~L900 | `get_supplier()` | `admin_ret_other_inventory/get_supplier` | `get_supplier()` |
| ~L950 | `get_other_inventory_purchase_items()` | `admin_ret_other_inventory/purchase_entry/ajax` | `purchase_entry(default)` |
| ~L1000 | `save_purchase_entry()` | `admin_ret_other_inventory/purchase_entry/save` | `purchase_entry(save)` |
| ~L1050 | `cancel_purchase_entry()` | `admin_ret_other_inventory/purchase_entry/cancel_purchase_entry` | `purchase_entry(cancel_purchase_entry)` |
| ~L1100 | `get_other_inventory_product_details()` | `admin_ret_other_inventory/product_details/ajax` | `product_details(default)` |
| ~L1150 | `save_product_details()` | `admin_ret_other_inventory/product_details/save` | `product_details(save)` |
| ~L1200 | `get_other_inventory_item_issue_details()` | `admin_ret_other_inventory/issue_item/ajax` | `issue_item(default)` |
| ~L1250 | `save_issue_item()` | `admin_ret_other_inventory/issue_item/save` | `issue_item(save)` |
| ~L1300 | `get_invnetory_item()` | `admin_ret_other_inventory/get_invnetory_item` | `get_invnetory_item()` |
| ~L1350 | `get_bill_details()` | `admin_ret_other_inventory/get_bill_details` | `get_bill_details()` |
| ~L1400 | `get_product_mapping_details()` | `admin_ret_other_inventory/product_mapping/ajax` | `product_mapping(default)` |
| ~L1450 | `update_product_mapping()` | `admin_ret_other_inventory/update_product_mapping` | `update_product_mapping()` |
| ~L1500 | `delete_product_mapping()` | `admin_ret_other_inventory/delete_product_mapping` | `delete_product_mapping()` |
| ~L1550 | `get_mapped_product_details()` | `admin_ret_other_inventory/get_productMappedDetails` | `get_productMappedDetails()` |
| ~L1600 | `reorder_report()` | `admin_ret_other_inventory/reorder_report/ajax` | `reorder_report(default)` |
| ~L1650 | `set_packing_item_size_list()` | `admin_ret_other_inventory/item_size/ajax` | `item_size(default)` |
| ~L1700 | `save_size()` | `admin_ret_other_inventory/item_size/save` | `item_size(save)` |
| ~L1750 | `update_size()` | `admin_ret_other_inventory/item_size/update` | `item_size(update)` |
| ~L1800 | `fetchAllSizes()` | `admin_ret_other_inventory/get_all_sizes` | `get_all_sizes()` |
| ~L1850 | `CheckIsNameDuplicate()` | `admin_ret_other_inventory/CheckIsNameDuplicate` | `CheckIsNameDuplicate()` |
| ~L1900 | `get_invnetory_item_list()` | `admin_ret_other_inventory/available_stock/ajax` | `available_stock(default)` |
| ~L1950 | `get_other_product_details()` | `admin_ret_other_inventory/get_pro_detail_list` / `product_tag_detail` | `product_tag_detail()` |
| ~L2000 | `get_customer_list()` | `admin_ret_other_inventory/get_customer` | `get_customer()` |

### Cross-Module Endpoints (other controllers)
| JS Line | JS Function | AJAX URL | External Controller |
|---|---|---|---|
| L608 | `get_uom_list()` | `admin_ret_catalog/uom/active_uom` | Catalog Controller |

---

## Part D: Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `ret_other_inventory_item` | `ajax_get_other_inventory`, `get_other_inventory_records`, `getActiveskuid`, `get_invnetory_item`, `get_other_inventory_stock`, `get_AvailableStockDetails`, `get_item_mapping_details`, `get_product_linked_items`, `skuid_available` | `insertData`, `update_other_inventory`, `set_image_other` |
| `ret_other_inventory_item_type` | `ajax_getotheritem`, `get_inventory_category`, `get_other_item_records`, `get_InventoryCategory`, `getActiveItemname`, `get_ActiveCategory`, `CheckIsNameDuplicate` | `insertData`, `update_otheritem` |
| `ret_other_inventory_size` | `ajax_getOtherInventorySizeList`, `get_packaging_size`, `get_ActivePackagingItemSize`, `get_all_sizes`, `get_AvailableStockDetails` | `insertData`, `updateData`, `deleteData` |
| `ret_other_inventory_reorder_settings` | `get_inv_item_reorder_details`, `get_reorder_report` | `insertData`, `deleteData` (on update) |
| `ret_other_inventory_purchase` | `ajax_getPurchaseEntrylist`, `ajax_getProductlist`, `generatePurNo`, `get_other_inventory_ref_no`, `getPurchaseDet`, `get_other_inventory_print`, `get_product_other_inventory_print` | `insertData`, `updateData` (cancel) |
| `ret_other_inventory_purchase_items` | `ajax_getPurchaseEntrylist`, `ajax_getProductlist`, `get_purchase_item_det`, `get_purchase_gst_det`, `get_other_inventory_details`, `get_other_inventory_product_det` | `insertData` |
| `ret_other_inventory_purchase_items_details` | `get_other_inventory_purchase_items_details`, `get_other_inventory_details`, `get_AvailableStockDetails`, `get_product_linked_items`, `get_reorder_report`, `getlastrefno`, `get_ref_no_details`, `get_other_inventory_stock`, `get_invnetory_item` | `insertData`, `updateData` (issue → status=1) |
| `ret_other_inventory_purchase_items_log` | `other_inventory_stock` | `insertData` (purchase + issue + transfer) |
| `ret_other_inventory_purchase_images` | `get_inv_purchase_images`, `ajax_getPurchaseEntrylist` | `insertData` |
| `ret_other_invnetory_issue` | `get_OtherInventoryIssueDetails` | `insertData` |
| `ret_other_inventory_product_link` | `check_other_inv_products_maping`, `get_item_mapping_details`, `get_productMappedDetails`, `get_product_linked_items` | `insertData`, `deleteData` |
| `gift_mapping` | `get_inv_chit_gift` | `insertData`, `delete_gift_map_data` |
