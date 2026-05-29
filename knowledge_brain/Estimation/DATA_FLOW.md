# Estimation Module — Data Flow Traces

> **Module**: Estimation
> **Date Built**: 2026-02-18 (Round 8 — Final)

---

## Flow 1: CREATE (Save New Estimation)

### Step-by-step trace

```
1. USER clicks "Save" button
   ↓
2. JS: Form submit handler fires (within $(document).ready switch)
   ↓
3. JS: Validation runs — validateTagDetailRow() (L7967-8217)
   - Checks each tag row: product, purity, gwt, nwt, rate must be filled
   - Returns true/false — but BUG EST-R403: single flag overwrite pattern
   ↓
4. JS: validateCatalogDetailRow() (L8325-8509)
   - Same structure for catalog items
   ↓
5. JS: validateCustomDetailRow() (L8529-8681)
   - Same structure for custom items
   ↓
6. JS: validateOldMatelDetailRow() (L8701-8758)
   - Old metal exchange validation
   ↓
7. JS: Collects all form data into arrays:
   - estimation[header fields]
   - estimation_items[n][field] — for each tag/catalog/custom row
   - estimation_stones[n][field]
   - estimation_materials[n][field]
   - estimation_old_metals[n][field]
   - estimation_chit[n][field]
   ↓
8. JS: AJAX POST to controller.estimation() method
   URL: base_url + 'index.php/admin_ret_estimation/estimation/save'
   ↓
9. CONTROLLER: estimation() method (L190), detects POST data, enters SAVE branch
   ↓
10. CONTROLLER: Reads all $_POST / $this->input->post() data
    - Customer ID, branch, date, employee
    - Bill number generation: model->generateEstiNo() (L1456-1472)
    - BUG EST-R605: Some endpoints use raw $_POST instead of input->post()
    ↓
11. CONTROLLER: $this->db->trans_begin()
    ↓
12. CONTROLLER: Inserts estimation header into `ret_estimation`
    - model->insertData($header_data, 'ret_estimation')
    - Returns id_estimation
    ↓
13. CONTROLLER: Loops through tag items array
    For each item:
    - Builds item data array (tag_id, product_id, gwt, nwt, rate, sales_value, mc, wastage...)
    - model->insertData($item_data, 'ret_estimation_items')
    - Returns est_item_id
    ↓
14. CONTROLLER: For each item's stones:
    - model->insertData($stone_data, 'ret_estimation_item_stones')
    ↓
15. CONTROLLER: For each item's other materials:
    - model->insertData($material_data, 'ret_estimation_item_other_materials')
    ↓
16. CONTROLLER: For each item's charges:
    - model->insertData($charge_data, 'ret_estimation_other_charges')
    ↓
17. CONTROLLER: Old metal exchange items
    For each old metal:
    - model->insertData($old_metal_data, 'ret_estimation_old_metal_sale_details')
    - For each stone in old metal:
      - model->insertData($om_stone_data, 'ret_esti_old_metal_stone_details')
    ↓
18. CONTROLLER: Chit scheme adjustments
    For each chit:
    - model->insertBatchData($chit_data, 'ret_est_chit_utilization')
    ↓
19. CONTROLLER: Image handling
    - set_image() — saves customer/tag images to filesystem
    - upload_img() — processes webcam captures
    ↓
20. CONTROLLER: $this->db->trans_status()
    ↓
21A. IF SUCCESS: $this->db->trans_commit()
     Return JSON: {status: 'success', estimation_id: ...}
     ↓
21B. IF FAILURE: $this->db->trans_commit() ← BUG EST-R601 — should be trans_rollback()
     Return JSON: {status: 'error', message: ...}
    ↓
22. JS: Response handler
    - Success: Show success toast, redirect to list
    - Error: Show error toast, keep form open
```

### Tables Written (in order)

1. `ret_estimation` — header
2. `ret_estimation_items` — line items (one row per tag/catalog/custom item)
3. `ret_estimation_item_stones` — stones per item
4. `ret_estimation_item_other_materials` — other materials per item
5. `ret_estimation_other_charges` — extra charges per item
6. `ret_estimation_old_metal_sale_details` — old metal exchange
7. `ret_esti_old_metal_stone_details` — stones deducted from old metal
8. `ret_est_chit_utilization` — chit scheme adjustments
9. `ret_est_gift_voucher_details` — gift voucher redemptions *(Round 2)*
10. `ret_est_sales_return_utilization` — sales return credits *(Round 2)*
11. `ret_estimation_other_inventory_issue` — packaging box items *(Round 2)*
12. `ret_est_other_metals` — other metal details per item *(Round 2)*
13. `ret_est_tag_merge` — tag merge tracking *(Round 2)*

---

## Flow 2: EDIT (Update Existing Estimation)

### Step-by-step trace

```
1. USER navigates to /admin_ret_estimation/estimation/edit/{id}
   ↓
2. CONTROLLER: estimation('edit', $id) — enters EDIT branch (within same 2,284-line method)
   ↓
3. CONTROLLER: Loads existing estimation data:
   - model->get_entry_records($est_id) — header data (L403-429)
   - model->get_est_tag_details($est_id) — tag items with complex JOINs (L2045-2265)
   - model->est_non_tag_items($est_id) — non-tag items (L2266-2394)
   - model->est_home_bill($est_id) — home bill items (L2395-2521)
   - model->old_metal($est_id) — old metal exchanges (L2522-2579)
   - model->get_chit_details($est_id) — chit adjustments (L2021-2044)
   - model->getOtherEstimateItemsDetails($est_id) — comprehensive item details (L430-682)
   ↓
4. CONTROLLER: Packages data into $data array, loads view
   ↓
5. VIEW: form.php renders with existing data pre-populated
   ↓
6. JS: $(document).ready detects ctrl_page[2] == 'edit'
   - Loads existing items into tables
   - Recalculates totals
   ↓
7-8. Same as CREATE steps 3-7 (user edits and validates)
   ↓
9. **CRITICAL DIFFERENCE — DELETE-THEN-INSERT pattern:**

   CONTROLLER: On save of edit:
   - model->deleteData('esti_id', $est_id, 'ret_estimation_items')
   - model->deleteData('est_id', $est_id, 'ret_estimation_item_stones')
   - model->deleteData('est_id', $est_id, 'ret_estimation_item_other_materials')
   - model->deleteData('est_id', $est_id, 'ret_estimation_other_charges')
   - model->deleteData('est_id', $est_id, 'ret_estimation_old_metal_sale_details')
   - model->deleteData('est_id', $est_id, 'ret_esti_old_metal_stone_details')
   - model->deleteData('est_id', $est_id, 'ret_est_chit_utilization')
   - model->deleteData('est_id', $est_id, 'ret_est_gift_voucher_details')
   ↓
   Then UPDATES the header (ret_estimation)
   Then re-INSERTS all items (same as CREATE Steps 13-18)
   ↓
10. Same commit/rollback as CREATE Steps 20-22

**RISK**: If delete succeeds but re-insert fails, ALL items are permanently lost.
The trans_rollback() bug (EST-R601) makes this even more dangerous.
```

### Data Loss Risk Fields

The following fields exist in the SELECT (Edit load) but could be lost if the re-insert uses different column mappings:

| Field | Loaded From | Risk |
|---|---|---|
| `est_item_id` | Auto-increment | New IDs generated on re-insert — any external references break |
| `image_path` | File system | If image move fails during re-insert, old path is deleted |
| Child stone/material records | Cascaded by `est_item_id` | Orphaned if parent fails |

---

## Flow 3: LIST (View Estimations)

```
1. USER navigates to /admin_ret_estimation/estimation/list
   ↓
2. CONTROLLER: estimation('list') — enters LIST branch
   ↓
3. CONTROLLER: Loads model->ajax_getEstimationList($id_branch, $from_date, $to_date) (L169-235)
   - Complex query joining estimation, customer, and computing totals
   ↓
4. VIEW: list.php renders with DataTable placeholder
   ↓
5. JS: set_estimation_list(data) (L10458-10592)
   - Populates DataTable with estimation records
   - Columns: ID, date, customer, type, total, item_type, print, actions (edit/view)
```

---

## Flow 4: PRINT (Generate PDF)

```
1. USER clicks Print button on estimation
   ↓
2. CONTROLLER: generate_invoice($est_id) (L2939-2996)
   ↓
3. CONTROLLER: Loads estimation data (header + items + stones + old metal)
   - model->getCompanyDetails() — company/branch info
   - model->get_entry_records() — header
   - model->getOtherEstimateItemsDetails() — comprehensive line items
   - model->old_metal() — old metal
   ↓
4. CONTROLLER: Selects print template based on branch settings
   - est_print.php (default)
   - est_print_2.php, est_print_anb.php, etc. (branch-specific)
   ↓
5. CONTROLLER: DomPDF renders HTML → PDF
   ↓
6. Browser: PDF streamed to user
```

---

## Key JS Function Map

### Calculation Functions

| Function | Lines | Purpose |
|---|---|---|
| `calculateSaleValue()` | 8854-9247 | Calculate catalog item sale values — iterates all catalog rows, computes per-row and totals |
| `calculateCustomItemSaleValue()` | 9249-9684 | Calculate custom item sale values — same pattern as catalog |
| `calculatetag_SaleValue()` | *(called from events, L18289)* | Calculate tagged item sale values |
| `calculateOldMatelItemSaleValue()` | 9706-9818 | Calculate old metal exchange values per row |
| `calculate_total_mc_va()` | 18619-18681 | Calculate MC/VA based on type (flat/%, per gram) |
| `calculate_purchase_details()` | *(event handler)* | Calculate overall purchase summary |
| `calculate_chit_closing_balance()` | *(event handler)* | Chit balance computation |
| `calculateOrderTag()` | *(event handler, L18287)* | Order-linked tag calculations |
| `calculate_tag_merge_data()` | 22530-22558 | Merged tag weight calculations |

### Validation Functions

| Function | Lines | Purpose |
|---|---|---|
| `validateTagDetailRow()` | 7967-8217 | Validate all tag item rows |
| `validateCatalogDetailRow()` | 8325-8509 | Validate catalog item rows |
| `validateCustomDetailRow()` | 8529-8681 | Validate custom item rows |
| `validateOldMatelDetailRow()` | 8701-8758 | Validate old metal rows |
| `validateStoneDetailRow()` | 8760-8776 | Validate stone detail rows |
| `validateMaterialDetailRow()` | 8778-8794 | Validate material rows |
| `validateVoucherDetailRow()` | 8796-8812 | Validate voucher rows |
| `validateChitDetailRow()` | 8836-8852 | Validate chit detail rows |

### Search / Tag Functions

| Function | Lines | Purpose |
|---|---|---|
| `get_tag_data()` | 1393-1967 | Fetch tag by search — primary tag lookup (574 lines) |
| `get_tag_barcode_data()` | 2265-2501 | Fetch tag by barcode scan (236 lines) |
| `get_tag_merge_data()` | 22202-22520 | Tag merge lookup (318 lines) |
| `getSearchTags()` | 5839-6207 | Tag autocomplete search (368 lines) |
| `getSearchProducts()` | 6367-6555 | Product autocomplete |
| `getSearchCustomProducts()` | 6629-6773 | Custom product search |
| `getSearchDesign()` | 6775-6887 | Design search |
| `getSearchOrders()` | 553-725 | Order search |

### Row Management Functions

| Function | Lines | Purpose |
|---|---|---|
| `create_new_empty_est_tag_row()` | 7055-7143 | Add empty tag row to table |
| `create_new_empty_est_catalog_row()` | 7265-7465 | Add empty catalog row |
| `create_new_empty_est_custom_row()` | 7467-7677 | Add empty custom row |
| `create_new_empty_est_oldmatel_row()` | 7679-7835 | Add empty old metal row |
| `create_new_empty_est_stone_row()` | 7847-7865 | Add empty stone row |
| `create_new_empty_est_material_row()` | 7867-7885 | Add empty material row |
| `create_new_empty_est_chit_row()` | 7899-7921 | Add empty chit row |
| `remove_tag_row()` | 7145-7194 | Remove tag row |
| `set_tag_split_details()` | 26321-28297 | Tag split logic (1,976 lines!) |

### Rate/Lookup Functions

| Function | Lines | Purpose |
|---|---|---|
| `get_search_tag_metal_rates()` | 1977-2039 | Get metal rate for tag row purity |
| `get_search_catalog_metal_rates()` | 6295-6365 | Get rate for catalog purity |
| `get_search_custom_metal_rates()` | 6565-6627 | Get rate for custom purity |
| `get_mc_va_limit()` | 18527-18617 | MC/VA limit based on product/design |
| `check_rate_is_valid()` | 18839-18895 | Rate range validation |
| `check_min_max_rate()` | 21976-22068 | Min/max rate checks |
| `set_tagging_wastage_and_mc()` | 21754-21832 | Set wastage and MC from tag data |

### VA Slab / Discount Functions *(Round 2)*

| Function | Lines | Purpose |
|---|---|---|
| `wastage_slab_value()` | 29994-30489 | **496 lines** — applies VA slab discounts to items based on metal, weight range, and slab config |
| `edit_wastage_slab()` | 30490-30534 | Load existing VA slab data during edit |
| `apply_va_slab_row()` | 29921-29953 | Apply single VA slab row to matching items |
| `remove_va_slab_row()` | 29972-29993 | Remove VA slab row and revert wastage |
| `validateVASlabDetailRow()` | 29756-29772 | Validate VA slab row before adding |
| `create_new_empty_est_va_slab_row()` | 29773-29830 | Create empty VA slab row with metal/range selects |
| `onDiscValueChange()` | 29954-29971 | Handle discount value change in VA slab |
| `check_disc_min_max_stone_rate()` | 30535-30603 | Validate stone discount against min/max limits |
| `check_min_max_stone_rate()` | 29311-29386 | Stone rate min/max boundary check |
| `getStoneRateSettings()` | 29283-29307 | AJAX fetch stone rate settings |

### Stone Discount Application (Bulk Discount)

| Function/Handler | Lines | Purpose |
|---|---|---|
| `#disc_apply` click | 29571-29725 | Apply stone discount % across ALL tag and custom rows — modifies stone_details JSON per row |
| `#disc_reset` click | 29439-29558 | Reset stone discount and restore original stone values |
| `#blk_disc_apply` click | 30642-30693 | Apply **bulk wastage discount** across tag rows |
| `#blk_disc_reset` click | 30694-30724 | Reset bulk wastage discount |
| `#summary_stn_dis_per` change | 29559-29570 | Auto-trigger discount apply on input change |
| `#summary_blk_dis_per` keyup | 30604-30641 | Validate bulk discount against limit before apply |

### Sales Return / Bill Functions *(Round 2)*

| Function | Lines | Purpose |
|---|---|---|
| `getBillDetails()` | 31062-31273 | **211 lines** — AJAX fetch bill items for sales return selection |
| `validateSRDetailRow()` | 30980-30995 | Validate sales return detail row |
| `create_new_empty_est_sr_row()` | 30998-31036 | Create empty sales return row |
| `remove_sr_row()` | 31321-31336 | Remove sales return row |
| `#update_bill_return` click | 31274-31320 | Confirm bill return item selection from modal |

### Other Functions *(Round 2)*

| Function | Lines | Purpose |
|---|---|---|
| `getFinancialYr()` | 31037-31059 | AJAX fetch financial year list for order search |
| `get_topup_chit_closing_balance()` | 30770-30943 | **173 lines** — recalculate chit closing balance after add/delete/SR |
| `get_old_metal_Product()` | 29424-29439 | Fetch old metal product dropdown by metal ID |
| `get_mc_va()` | 29045-29147 | Get MC and VA for home bill and non-tag items |
| `update_custom_wastage_mc()` | 29149-29187 | Update custom item wastage from MC |
| `update_custom_wastage_wt()` | 29189-29213 | Update custom item wastage weight |
| `update_catalog_wastage_mc()` | 29215-29249 | Update catalog wastage from MC |
| `calc_cat_wastage_wt()` | 29251-29279 | Calculate catalog wastage weight |
| `append_tag_details()` | 28303-28869 | **566 lines** — append tag row with full details (images, stones, charges, other metals) |
| `showTagCharges()` | 28875-28919 | Display tag other charges modal |
| `showTagothermetals()` | 28923-29007 | Display tag other metals modal |
| `isLikelyBase64()` | 30756-30766 | Check if string is base64-encoded (for image detection) |

---

## Flow 5: EDA (Estimate Discount Approval) *(Round 2)*

```
1. Employee creates estimation with "IS EDA" checkbox checked
   → estimation.is_eda = 1 saved to ret_estimation
   ↓
2. Estimation appears in EDA list (/estimation/eda/list.php)
   → DataTable shows: Est No, Date, Customer, Mobile, Product, Total, Final, Discount
   ↓
3. Manager clicks Approve or Reject
   → Approve modal (#confirm-approve): stores esti_id and estimate_final_amt
   → Reject modal (#confirm-reject): stores esti_reject_id
   ↓
4. AJAX call to controller (approve/reject endpoint)
   → Updates estimation status in ret_estimation
```

## Flow 6: Sales Return in Estimation *(Round 2)*

```
1. Feature flag: enable_sales_return_estimations.value == 1 (view checks setting)
   ↓
2. User clicks "Add" in Sales Return section
   → JS: create_new_empty_est_sr_row() — adds row with Bill No select + Amount/Weight
   ↓
3. User selects Financial Year → getFinancialYr() populates year dropdown
   ↓
4. User enters Bill No → getBillDetails(row) fetches bill items via AJAX
   → Shows modal with bill line items (select_est_details checkboxes)
   ↓
5. User selects items → #update_bill_return handler sums amounts and weights
   → Populates SR row: total amount, total weight, bill_det_id (comma-separated)
   ↓
6. SAVE: SR data included in estimation save → ret_est_sales_return_utilization table
   → get_topup_chit_closing_balance('','sr_add') recalculates chit balance
```

## Flow 7: Print Rendering *(Round 3)*

```
1. User clicks Print button on estimation
   → Controller: generate_invoice(est_id) or generate_brief_copy(est_id)
   ↓
2. Controller loads estimation data via model:
   → get_entry_records(est_id) — header data
   → getOtherEstimateItemsDetails(est_id) — 252-line method building complete data:
     - item_details (tag/catalog/custom items with stone_details, charges, other_metal_details)
     - old_matel_details (old metal with stone_details)
     - advance_details (order advances)
     - chit_details (scheme accounts with closing weights, VA savings, MC savings)
   → getCompanyDetails(id_branch) — for GST state comparison
   → get_branchwise_rate(id_branch) — current metal rates
   ↓
3. Controller selects print template based on branch/type:
   → est_print.php (default) | est_print_2.php | est_print_anb.php | etc.
   ↓
4. PHP template RECALCULATES values (not just display):
   → VA/MC by calculation_based_on mode (0=Gross, 1=Net, 2=Mixed)
   → Stone totals per item
   → Charge totals per item
   → Chit benefits (weight×rate, VA savings, MC savings, additional_benefits, deductions)
   → Tax split: CGST/SGST (same state) or IGST (different state)
   → Grand total: Sales - Purchase - Chit - Advance
   ↓
5. DomPDF renders HTML → PDF returned to browser
```

**Risk**: PHP recalculation can diverge from JS calculations if logic changes in one but not the other.

## Flow 8: Credit Collection Lookup *(Round 3)*

```
1. During estimation save/edit for a customer with credit bills
   ↓
2. Model: get_credit_pending_details(id_branch, id_customer)
   → Queries ret_billing for credit bills (bill_type, status)
   → Returns pending credit amounts, bill details
   ↓
3. For each credit bill: getCreditCollection(bill_id)
   → Gets collection records from ret_billing (ref_bill_id match)
   → Calls getOld_sales_detail(bill_id, 8) — old metal in collections
   → Returns array: bill_no, tot_amt_received, credit_disc_amt, old_metal_amount
   ↓
4. Also: get_IssueCreditCollectionDetails(bill_id)
   → Queries ret_issue_receipt + ret_issue_credit_collection_details
   → Returns receipt-based collections with discount amounts
   ↓
5. Total collected = sum(billing_collections) + sum(issue_receipt_collections)
   → Used to calculate remaining credit balance for the customer
```

## Flow 9: Branch Day Closing Gate *(Round 3)*

```
1. Controller: estimation('add') or estimation('save')
   ↓
2. Model: getBranchDayClosingData(id_branch)
   → SELECT id_branch, is_day_closed, entry_date FROM ret_day_closing WHERE id_branch=?
   ↓
3. If is_day_closed == 1 → estimation should be blocked for that date
   NOTE: Controller-side enforcement is unclear — may need audit
```
