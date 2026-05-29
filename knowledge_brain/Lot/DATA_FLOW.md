# LOT MODULE — DATA FLOW
> Module: Lot | Round 1 | 2026-03-17

---

## Flow 1: CREATE (lot_inward/save)

**Trigger**: User fills form.php and clicks `#save_all` button

### Step-by-step:
```
1. JS: validateItemDetailRow() [ret_lot.js]
   ↓ fails → alert("Please fill required fields..") & return false
   ↓ passes → submit #lot_form (standard HTML form POST)

2. HTML Form POST → admin_ret_lot/lot_inward/save

3. Controller (L337–1047):
   a. Read $_POST['inward'] as $addData
   b. Get day close date: admin_settings_model->getBranchDayClosingData($addData['lot_received_at'])
   c. Build $data array for ret_lot_inwards header
   d. trans_begin()
   e. insertData($data, 'ret_lot_inwards') → $insId

4. Image upload (if $_FILES['lot_image'] set):
   - mkdir('assets/img/lot/{$insId}', 0777, TRUE)
   - upload_img() → save as random .jpg
   - updateData(['lot_images'=>$lot_imgs], 'lot_no', $insId, 'ret_lot_inwards')

5. For each inward_item (L430–993):
   a. Process base64 stone certificate images → $_FILES['precious'/'semi'/'normal']
   b. Save certificate images to 'assets/img/lot/{$insId}/certificates/'
   c. Build $item_details array
   d. insertData($item_details, 'ret_lot_inwards_detail') → $detail_insId
   
   e. STONES (if stone_details JSON non-empty):
      - foreach stone_details → insertData($stones, 'ret_lot_inwards_stone_detail')
   
   f. OTHER METALS (if other_metal_details JSON non-empty):
      - foreach other_items_details → insertData($other_items, 'ret_lot_other_items')
   
   g. OTHER CHARGES (if other_charges_details JSON non-empty):
      - foreach other_charges_details → insertData($charges, 'ret_lot_other_charges')
   
   h. NON-TAG LOGIC (if stock_type==2):
      - checkNonTagItemExist($existData)
      - If exists: updateNTData($nt_data, '+')
      - If not exists: insertData($non_tag_data_insert, 'ret_nontag_item')
      - Always: insertData($non_tag_data, 'ret_nontag_item_log')
      - Always: insertData($item_non_tag_data, 'ret_section_nontag_item_log')

6. If trans_status()===TRUE:
   - log_model->log_detail('insert', '', $log_data)
   - trans_commit()
   - set_flashdata('chit_alert', success)
   - redirect('admin_ret_lot/lot_acknowladgement/1/{$insId}') ← ⚠️ then immediately:
   - redirect('admin_ret_lot/lot_inward/list') ← dead code, never reached

7. If trans_status()===FALSE:
   - echo last_query() ← ⚠️ DEBUG IN PRODUCTION
   - echo _error_message() ← ⚠️ DEBUG IN PRODUCTION
   - trans_rollback() ← ⚠️ AFTER the echo — too late
   - redirect('admin_ret_lot/lot_inward/list')
```

**Tables Written (in order)**:
1. `ret_lot_inwards` (INSERT header)
2. `ret_lot_inwards` (UPDATE lot_images if image uploaded)
3. `ret_lot_inwards_detail` (INSERT per item)
4. `ret_lot_inwards_stone_detail` (INSERT per stone)
5. `ret_lot_other_items` (INSERT per other metal)
6. `ret_lot_other_charges` (INSERT per charge)
7. `ret_nontag_item` (INSERT or arithmetic update, if NonTag)
8. `ret_nontag_item_log` (INSERT, if NonTag)
9. `ret_section_nontag_item_log` (INSERT, if NonTag)

---

## Flow 2: EDIT (lot_inward/update)

**Trigger**: User edits form and submits update POST

### Step-by-step:
```
1. Load edit page: lot_inward('edit', $id)
   - get_lotInward($id) → header data
   - get_lotInward_detail($id) → line items with stones
   - JS: set_edit_lot_row() populates form

2. Form POST → admin_ret_lot/lot_inward/update/{id}

3. Controller (L1251–1651):
   a. Optional image upload → lot/{id}/ folder
   b. Build $data array (note: lot_received_at is COMMENTED OUT — branch not updatable)
   c. trans_begin()
   d. updateData($data, 'lot_no', $id, 'ret_lot_inwards') → $updId

4. For each inward_item:
   if $itemData['id_lot_inward_detail'] is set: ← UPDATE path
     a. updateData($item_details, 'id_lot_inward_detail', ..., 'ret_lot_inwards_detail')
     b. deleteData('id_lot_inward_detail', ..., 'ret_lot_inwards_stone_detail') ← DELETE all stones
     c. Re-insert stone_details
     ⚠️ DOES NOT clean up ret_lot_other_items / ret_lot_other_charges
   
   else: ← INSERT new item path
     a. insertData($item_details, 'ret_lot_inwards_detail')
     b. Insert stones

5. trans_commit/rollback as usual
6. redirect('admin_ret_lot/lot_inward/list')
```

**Key Gap**: Fields in SELECT (edit load) but NOT in UPDATE array:
- `lot_received_at` / `created_branch` — commented out — prevents branch change
- `gold_smith` — included in update for header
- `ret_lot_other_items` / `ret_lot_other_charges` — **NOT cleaned up on update → stale charges remain**

---

## Flow 3: DELETE (lot_inward/delete/{id})

**Trigger**: Delete link click (GET request — ⚠️ CSRF risk)

```
1. GET /admin_ret_lot/lot_inward/delete/{id}
2. trans_begin()
3. deleteData('lot_no', $id, 'ret_lot_inwards')
   ⚠️ DOES NOT delete:
   - ret_lot_inwards_detail
   - ret_lot_inwards_stone_detail
   - ret_lot_other_items
   - ret_lot_other_charges
4. If success: rrmdir('assets/img/lot/{$id}') — delete image folder
5. log_model log
6. trans_commit
7. redirect('admin_ret_lot/lot_inward/list')
```

---

## Flow 4: LOT MERGE (lot_merge/save)

```
1. User selects lots to merge + defines new merged lot items
2. POST → admin_ret_lot/lot_merge/save
3. Controller:
   a. INSERT ret_lot_inwards (lot_from=7)
   b. For each merge_item: INSERT ret_lot_inwards_detail
   c. NonTag logic (same as save)
   d. For each lot_merge: INSERT ret_lot_merge (links old detail_id to new lot_no)
   e. If has stone_details: INSERT ret_lot_inwards_stone_detail
4. On success: redirect to lot_acknowladgement
   ⚠️ Source lots are NOT marked as merged in ret_lot_inwards
   ⚠️ echo last_query() before trans_rollback on failure (L2270)
```

---

## Flow 5: LOT SPLIT (lot_split/save)

```
1. User selects lot to split + defines split quantities per item
2. POST → admin_ret_lot/lot_split/save
3. Controller:
   a. updateData(['is_lot_split'=>1], 'lot_no', $itemData['lot_no'], 'ret_lot_inwards')
   b. INSERT ret_lot_split_details (per split item)
4. On success: redirect to lot_split/list
   ⚠️ echo last_query() + _error_message() before rollback on failure
   ⚠️ No new lot is created for split — only records the split amounts
```

---

## Flow 6: LOT CANCEL (lot_inward/cancel_lot_entry)

```
1. AJAX POST → admin_ret_lot/lot_inward/cancel_lot_entry
2. updateData(['lot_status'=>2, 'cancel_reason'=>..., 'cancelled_on'=>..., 'cancelled_by'=>...], 
              'lot_no', $_POST['lot_no'], 'ret_lot_inwards')
3. Returns JSON: {status: TRUE/FALSE, message: ...}
```

---

## Flow 7: LOT CLOSE (lot_completed)

```
1. AJAX POST → admin_ret_lot/lot_completed
2. getBranchDayClosingData($id_branch) → get lot_closed_date
3. For each completed_lot:
   - updateData(['is_closed'=>1, ...], 'lot_no ', $val['lot_no'], 'ret_lot_inwards')
   ⚠️ TYPO: 'lot_no ' (trailing space) — UPDATE WHERE `lot_no ` = x — may fail silently
4. Returns JSON {status, message}
```

---

## Flow 8: PRINT FLOWS

| Print Type | Route | Model Methods | View |
|---|---|---|---|
| Vendor Ack | `vendor_acknowladgement/1/{lot_id}` | `lotInward_detail()`, `get_lot_details()` | `lot/print/vendor_ack` → dompdf |
| Office Ack | `lot_acknowladgement/1/{lot_id}` | `lotInward_detail()`, `get_lot_details()`, `get_lot_tag_details()` | `lot/print/office_ack` → dompdf |
| Branch Ack | `branch_acknowladgement/1/{lot_id}/{id_branch}` | `lotInward_detail()`, `get_tagdetails_by_lot()`, `get_branch_summary()` | `lot/print/branch_ack` → dompdf |
| Customer Ack | `customer_acknowladgement/1/{lot_id}` | `get_customer_lotInward_detail()`, `get_customer_lot_details()` | `lot/print/customer_ack` → echo HTML (NOT dompdf) |

---

## JS Function Map (Key Functions in ret_lot.js)

| JS Function | Line Range | Purpose |
|---|---|---|
| `get_lotInward_list()` | ~L2300+ | AJAX fetch lot list with date range/filters |
| `set_lot_preview()` | ~L347 ref | Set lot preview from lot_preview_item data |
| `set_edit_lot_row()` | ~L279 ref | Populate edit form rows from lot data |
| `validateItemDetailRow()` | ~L909 ref | Client-side validation before form submit |
| `getLotidsforMerge()` | ~L833 ref | Load merge lot dropdown |
| `getLotidsforSplit()` | ~L893 ref | Load split lot dropdown |
| `remove_img(file,folder,field,id,imgs)` | L2279–2335 | Remove single image via AJAX |
| `calculateLotTotal()` | ~L1113 ref | Calculate merge totals |
| `TotalLotMerge()` | ~L1117 ref | Calculate full merge total |
| `get_karigar()` | Called on add/edit/merge | Load goldsmith dropdown |
| `get_category()` | Called on add/edit/merge | Load category dropdown |
| `getActiveUOM()` | Called on add/edit | Load UOM dropdown |
| `get_Branchwise_Sections()` | Called on add/edit | Load section dropdown |
| `get_stones()` / `get_stone_types()` | Called on add/edit | Load stone data |
| `getQualityDiamondRates()` | Called on add/edit | Load diamond rates |
| `get_charges()` | Called on add/edit | Load charge definitions |
| `get_taxgroup_items()` | Called on add/edit | Load tax groups |
| `getSearchProd()` | On `#lt_product` keyup (len=3) | Search products |
| `getSearchDesign()` | On `#design` keyup (len≥2) | Search designs |
| `getSearchOrderNo()` | On `#lt_order_no` keyup (len≥2) | Search order nos |
| `getSearchCustomers()` | On `#cus_name` keyup (len≥3) | Search customers |
| `get_karigar_details()` | On `#lt_gold_smith` change | Load karigar details |
| `get_cat_purity()` | On `#category` change | Load purities for category |
| `validateCertifImg()` | On cert image change | Validate certificate images |
| `item_validateImage()` | On `#lot_images` change | Validate lot image |
| `get_ActiveGRNS()` | Called on add | Load active GRN entries |
