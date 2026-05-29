# DATA FLOW — Other Inventory
> **Module:** Other Inventory | **Built:** 2026-03-14

---

## Flow 1: CREATE Item Master (other_inventory/save)

```
1. JS: User fills form.php (name, size, category, UOM, issue_preference, unit_price, pieces[])
2. JS: scheme_add_row() prepopulates gift scheme mapping section
3. JS: On form submit → standard POST to /other_inventory/save
4. PHP: Controller other_inventory('save'):
   a. Reads $_POST['other'] → builds $data array
   b. trans_begin()
   c. insertData($data, 'ret_other_inventory_item') → $id_other_item
   d. Loop: insertData($insdata, 'ret_other_inventory_reorder_settings') for each branch piece
   e. update_other_inventory(['sku_id' => $id_other_item], ...) — auto-set sku_id = PK
   f. Loop: insertData($data, 'gift_mapping') for each scheme row
   g. If image uploaded: set_image_other($id_other_item, $sku_id)
        → JPEG resampled → saved to assets/img/other_inventory/{sku_id}/{timestamp}.jpg
        → updateData(['item_image'=> $filename], ...)
   h. QRcode::png($sku_id, $file_name) → generates PNG in other_product_qrcode/
   i. update_other_inventory(['qr_image' => $src], ...)
   j. trans_status() → commit or rollback
5. Redirect: /other_inventory/list
```

**Tables Written:** `ret_other_inventory_item`, `ret_other_inventory_reorder_settings`, `gift_mapping`

**⚠️ Risk:** No trans_begin before gift_mapping inserts — only trans_begin at step 4b. Gift map inserts happen outside this (L165-208), so partial commits are possible.

---

## Flow 2: EDIT Item Master (other_inventory/update)

```
1. JS: Edit link → GET /other_inventory/edit/{id}
2. PHP: Controller loads:
   - get_other_inventory_records($id) → header data
   - get_inv_item_reorder_details($id) → branch reorder settings
   - get_inv_chit_gift($id) → existing scheme mappings
3. View: form.php prepopulates with PHP data
4. JS: On form submit → POST /other_inventory/update/{id}
5. PHP: Controller other_inventory('update'):
   a. trans_begin()
   b. update_other_inventory($data, $id, ...) — MISSING: stock_id_uom and issue_to NOT updated
   c. delete then re-insert reorder_settings records
   d. delete_gift_map_data($id) → DELETE FROM gift_mapping WHERE id_other_item={id}
   e. Re-insert gift_mapping records
   f. If new image: set_image_other($item, $inv_data['sku_id']) — BUG: $item is update return value, not raw ID
   g. QR code regenerated + qr_image updated
   h. trans_status() → commit or rollback
6. Redirect: /other_inventory/list
```

**Tables Read:** `ret_other_inventory_item`, `ret_other_inventory_reorder_settings`, `gift_mapping`
**Tables Written:** `ret_other_inventory_item`, `ret_other_inventory_reorder_settings`, `gift_mapping`

---

## Flow 3: DELETE Item Master (other_inventory/delete)

```
1. JS: Delete button → GET /other_inventory/delete/{id}
   ⚠️ CSRF Risk — GET request with side effects
2. PHP: trans_begin()
3. PHP: deleteData('id_other_item', $id, 'ret_other_inventory_item')
   ⚠️ Does NOT clean up: reorder_settings, gift_mapping, purchase_items_details, images
4. trans_status() → commit or rollback
5. Redirect: /other_inventory/list
```

**⚠️ Orphan Risk:** Related records in `ret_other_inventory_reorder_settings`, `gift_mapping` are NOT deleted.

---

## Flow 4: PURCHASE ENTRY — Create

```
1. JS: purchase/form.php — user selects supplier, adds line items (item, qty, rate, GST)
       Webcam captures images (base64), stored in img_resource array
2. JS: save_purchase_entry() → POST /purchase_entry/save (JSON data + tag_img base64)
3. PHP: Controller purchase_entry('save'):
   a. get_headOffice() → get HO branch
   b. getBranchDayClosingData($branch_id) → entry_date
   c. generatePurNo() → padded 5-digit ref no (MAX+1)
   d. trans_begin()
   e. insertData($insData, 'ret_other_inventory_purchase') → $insId
   f. Loop per line item:
      insertData($itemData, 'ret_other_inventory_purchase_items') → $item_InsId
   g. Loop per base64 image:
      base64ToFile() → temp file
      upload_img() → JPEG to assets/img/purchase_entry/
      insertData($arrayimg_tag, 'ret_other_inventory_purchase_images')
   h. trans_status() → commit + log_model insert, or rollback
4. Response: JSON {status, otr_inv_pur_id, message}
```

**Tables Written:** `ret_other_inventory_purchase`, `ret_other_inventory_purchase_items`, `ret_other_inventory_purchase_images`

---

## Flow 5: PRODUCT DETAILS — Tag Individual Pieces

```
1. JS: product/form.php → user selects purchase ref_no
       get_other_inv_product_details() loads items for that purchase
       User enters number of pieces per item
2. JS: save_product_details() → POST /product_details/save
3. PHP: Controller product_details('save'):
   a. get_headOffice() → branch
   b. getBranchDayClosingData() → entry_date  
   c. ⚠️ NO trans_begin() — but trans_commit() is called later
   d. Loop per item × pieces:
      getlastrefno() → last item_ref_no in details table
      generaterefCode() → increments alpha+numeric suffix
      item_ref_no = {itemid}-{ref_code} (e.g., "1-A00001")
      insertData($itemDetail, 'ret_other_inventory_purchase_items_details')
      insertData($logData, 'ret_other_inventory_purchase_items_log') [status=0]
      get_ref_no_details($insId) → get ref_no back
   e. trans_status() → commit or rollback
4. Response: JSON {status, ref_no, message}
```

**Tables Written:** `ret_other_inventory_purchase_items_details`, `ret_other_inventory_purchase_items_log`

---

## Flow 6: ISSUE ITEMS

```
1. JS: issue/list.php + form — user selects branch, item, quantity
       get_invnetory_item() loads available stock by branch
       get_bill_details() loads today's bills
2. JS: save_issue_item() → POST /issue_item/save
3. PHP: Controller issue_item('save'):
   a. getBranchDayClosingData() → entry_date
   b. Build $insData for ret_other_invnetory_issue
   c. gift_mapping inserts happen BEFORE trans_begin (L927-946)!
   d. trans_begin()
   e. insertData($insData, 'ret_other_invnetory_issue') → $insId
   f. get_InventoryCategory() → get issue_preference (FIFO/FILO)
   g. get_other_inventory_purchase_items_details() → select piece records FIFO or FILO with LIMIT
   h. Loop per piece:
      updateData(['id_inventory_issue'=>$insId, 'status'=>1], ..., 'ret_other_inventory_purchase_items_details')
   i. insertData($logData, 'ret_other_inventory_purchase_items_log') [status=1]
   j. trans_status() → commit or rollback
4. Response: JSON {status, message}
```

**Tables Written:** `ret_other_invnetory_issue`, `ret_other_inventory_purchase_items_details` (status→1), `ret_other_inventory_purchase_items_log`, `gift_mapping`

**⚠️ Bug:** gift_mapping inserts happen BEFORE trans_begin — they are NOT rolled back on failure.

---

## Flow 7: STOCK REPORT

```
1. JS: Date range selection → calls stock detail AJAX
2. JS: POST to /stock_details/ajax with {id_branch, from_date, to_date, id_other_item, id_other_item_type}
3. PHP: stock_details(default) → model other_inventory_stock($_POST)
4. SQL: Complex query against ret_other_inventory_purchase_items_log:
   - Opening balance: SUM pieces where date < from_date
   - Inward: SUM pieces where status=0 AND to_branch=id_branch in range
   - Outward: SUM pieces where (status=1 OR status=4 OR status=3) AND from_branch=id_branch in range
   - Closing = Opening + Inward - Outward
5. Response: JSON array with stock summary per item
```

---

## Flow 8: ZPL Tag Print

```
1. GET /other_inventory_print/{ref_no}
2. PHP: get_other_inventory_print($ref_no) → items matching ref_no
3. Loop per item → get_tag_code($d, $no) → ZPL template positioning for 3 tags per row
4. get_printer_code($tagprintCode) → wraps with ZPL header/footer (1600 width, Zebra format)
5. downloadFile($content, $filename) → Content-Disposition: attachment (.prn file)
```

---

## JS Function Map (Key Functions)

| JS Function | Trigger | Calls AJAX | Side Effects |
|---|---|---|---|
| `set_other_inventory()` | page load (list) | `other_inventory/ajax` | Renders DataTable |
| `check_skuid_avail()` | blur on #sku_id | `check_sku_id` | Clears field if duplicate |
| `get_itemfor_list()` | page load (add/edit) | `get_inventory_category` | Populates #itemfor select2 |
| `get_item_size_list()` | page load (add/edit) | `item_size/get_ActivePackagingItemSize` | Populates #select_size |
| `get_uom_list()` | page load (add/edit) | `admin_ret_catalog/uom/active_uom` | Populates #select_uom (**cross-module**) |
| `get_other_inventory_ref_no()` | page load (product/add) | `get_other_inventory_ref_no` | Populates purchase ref dropdown |
| `get_other_inv_product_details()` | ref_no select change | `get_other_inventory_details` | Loads product table |
| `scheme_add_row()` | page load (other_inv/add) | `get scheme data` | Adds gift scheme row |
| `get_item_category_list()` | page load (category/list) | `inventory_category/ajax` | Category DataTable |
