# Catalog_Inventory — DATA FLOW

> **Built**: 2026-03-13 | **Module**: Catalog_Inventory | **Round**: 5

## Flow 1: Category CREATE

```
1. Browser     → Navigate to master/ret_category/list → Click "Add"
2. JS/View     → category form loads (master/ret_category/list view, modal-based)
3. User        → Fills: name, cat_code, hsn_code, description, id_metal, cat_type, tgrp_id, is_multimetal, purity selection, image
4. JS          → Form POST → /admin_ret_catalog/category/add
5. Controller  → category('add') at L3410
   5a. Build $data array from POST inputs
   5b. $this->db->trans_begin()
   5c. insertData($data, 'ret_category') → returns $result (new ID)
   5d. If file uploaded → upload_img() → updateData() to set image filename
   5e. Loop: foreach purity → insertData($purity_data, 'ret_metal_cat_purity')
   5f. trans_commit() or trans_rollback()
   5g. log_model->log_detail('insert', ...) for audit
6. Response    → JSON: {message, class, title}
```

**Tables Written**: `ret_category` (INSERT), `ret_metal_cat_purity` (INSERT × N)
**Transaction**: Yes — wraps entire operation

## Flow 2: Category UPDATE

```
1. Browser     → category list → Action → Edit
2. JS          → AJAX GET → /admin_ret_catalog/category/edit/{id}
3. Controller  → category('edit', $id) at L3541
   3a. get_ret_category($id) → category record
   3b. get_category_purity($id) → purity IDs
   3c. Return JSON with category + purities
4. JS          → Populates form modal with returned data
5. User        → Modifies fields → Submit
6. JS          → Form POST → /admin_ret_catalog/category/update
7. Controller  → category('update') at L3609
   7a. Build $data array from POST
   7b. trans_begin()
   7c. If new image → upload → set data['image']
   7d. updateData($data, 'id_ret_category', $id, 'ret_category')
   7e. ⚠️ deleteData('id_category', $id, 'ret_metal_cat_purity') — DELETES ALL existing purities
   7f. Loop: foreach purity → insertData() into ret_metal_cat_purity — RE-INSERTS
   7g. trans_commit() or trans_rollback()
   7h. Audit log
8. Response    → echo 1 (success) or 0 (fail)
```

**Risk**: DELETE-then-INSERT for purities. If INSERT fails mid-loop, some purities are lost.
**Tables Written**: `ret_category` (UPDATE), `ret_metal_cat_purity` (DELETE ALL then INSERT × N)

## Flow 3: Category DELETE

```
1. Browser     → category list → Action → Delete
2. JS          → Confirm modal → triggers /admin_ret_catalog/category/delete/{id}
3. Controller  → category('delete', $id) at L3555
   3a. getItemsinTagDetails('', $id, '', '', '', '') → checks if category exists in ret_taging
   3b. If count == 0 → deleteData('id_ret_category', $id, 'ret_category')
   3c. If exists in stock → flashdata error, no delete
   3d. trans_commit() or trans_rollback()
   3e. Audit log
4. Response    → redirect('admin_ret_catalog/category/list')
```

**⚠️ Risk**: Uses redirect (GET) after delete — CSRF possible
**⚠️ Does NOT delete** child `ret_metal_cat_purity` records → orphan risk

## Flow 4: Retail Product CREATE

```
1. Browser     → /admin_ret_catalog/ret_product/add
2. Controller  → ret_product('add') at L6134
   2a. Loading empty record from getProd_empty_record()
   2b. Load view master/ret_product/form
3. User        → Fills extensive form (40+ fields including sections[], charges[])
4. JS          → Form POST → /admin_ret_catalog/ret_product/save
5. Controller  → ret_product('save') at L6144
   5a. Build $data array from $_POST['product'] (40+ fields)
   5b. trans_begin()
   5c. insertData($data, 'ret_product_master') → returns $id_karigar (⚠️ variable name misleading)
   5d. Loop: foreach id_section → insertData() into ret_product_section
   5e. set_image_ret($id_karigar) → handles product image
   5f. Loop: foreach charges → insertData() into ret_product_charges
   5g. trans_commit() or trans_rollback()
   5h. Audit log
6. Response    → redirect to list
```

**Tables Written**: `ret_product_master`, `ret_product_section` (× N), `ret_product_charges` (× N)

## Flow 5: Retail Product UPDATE

```
1. Controller  → ret_product('edit', $id) at L6355
   1a. get_ret_product($id)
   1b. get_product_section($id)
   1c. get_charges_list() — all charge types
   1d. get_product_charges($id) — current charges
   1e. Load view master/ret_product/form with all data
2. User        → Modifies form → Submit
3. Controller  → ret_product('update') at L6413
   3a. Build $data from $_POST['product']
   3b. trans_begin()
   3c. updateData($data, 'pro_id', $id, 'ret_product_master')
   3d. ⚠️ deleteData('pro_id', $prod, 'ret_product_section') — DELETE ALL sections
   3e. Loop: re-insert sections
   3f. If new image → set_image_ret()
   3g. ⚠️ deleteData('prod_id', $prod, 'ret_product_charges') — DELETE ALL charges
   3h. Loop: re-insert charges
   3i. trans_commit() / trans_rollback()
```

**Risk**: Triple DELETE-then-INSERT (sections + charges). Same atomicity concern.

## Flow 6: Retail Product DELETE

```
1. Controller  → ret_product('delete', $id) at L6375
   1a. getItemsinTagDetails('', '', $id, ...) → check stock existence
   1b. If 0 → deleteData('pro_id', $id, 'ret_product_master')
              deleteData('prod_id', $id, 'ret_product_charges')
   1c. ⚠️ Does NOT delete ret_product_section → ORPHAN RISK
```

## Flow 7: Purity/Color/Cut/Clarity CRUD (Identical Pattern)

All four diamond attribute masters follow the exact same CRUD pattern:

```
entity($type, $id):
  "Add"    → insertData/insert_{entity}() into ret_{entity}
  "Edit"   → get_{entity}($id) → return JSON
  "Delete" → getItemsinTagDetails() check → deleteData() from ret_{entity}
  "Update" → update_{entity}($data, $id)
  "List"   → load view master/{entity}/list
  default  → ajax_get{Entity}() → return JSON for DataTable
```

**entity_status($status, $id)** → Update status field → redirect to list

## Flow 8: Karigar (Artisan) CREATE

```
1. Controller  → karigar('add') → load form view
2. User        → Fills: name, mobile, email, address, bank details, wastage rates, stone rates, charges
3. Controller  → karigar('save')
   3a. Insert into ret_karigar
   3b. Loop: wastage rates → ret_karikar_items_wastage
   3c. Loop: stone rates → ret_karigar_stones
   3d. Loop: charges → ret_karigar_charges
   3e. Loop: KYC documents → ret_karigar_kyc
   3f. Loop: bank accounts → ret_karigar_bank_acc_details
   3g. Optional: karigar product mapping → ret_karigar_products
```

**Tables Written**: `ret_karigar` + 5 child tables

## JS Functions Map

| Function | Lines | Purpose |
|---|---|---|
| `load_category_list()` | L152-197 | AJAX load → DataTable for category list |
| `load_product_list()` | L199-255 | AJAX load → DataTable for product list (web catalog) |
| `validate_catImage()` | L264-352 | Image validation: 1MB max, jpg/png/jpeg only |
| `validateImage()` | L362-450 | Product image validation: 5MB max, jpg/png/jpeg only |
| jstree init (category) | L14-77 | Category tree for parent selection (jsTree plugin) |
| jstree init (product) | L79-148 | Product category tree for assignment (checkbox jsTree) |

---

> Flows 9-14 added in Round 5

## Flow 9: Design CREATE (Most Complex Entity)

```
1. Controller  → ret_design('add') at L7770
   1a. Load empty design record + karigars, purities, materials, stones, sizes
   1b. Load view master/ret_design/form
2. User        → Fills: design_name, product_id, theme, hook_type, screw_type,
                 design_for, dimensions (min/max length/width/dia/weight),
                 fixed_rate, sizes[], karigars, purities, materials, stones
3. Controller  → ret_design('save') at L7790
   3a. Parse comma-separated selections: karigars, purities, materials
   3b. Auto-generate design_code via genDesignShortCode()
   3c. ⚠️ design_name → strtoupper() at L7839
   3d. ⚠️ DUPLICATE KEY BUG: 'fixed_rate' defined twice at L7867 and L7869
   3e. trans_begin()
   3f. insertData($data, 'ret_design_master') → returns $id
   3g. Loop: karigars → insertData() into ret_design_karigars
   3h. Loop: purities → insertData() into ret_design_purity
   3i. Loop: materials → insertData() into ret_design_other_materials
   3j. Loop: sizes → insertData() into ret_design_sizes (with size + uom_id)
   3k. Loop: stones → insertData() into ret_design_stone
   3l. Image upload handling
   3m. trans_commit() / trans_rollback()
   3n. Audit log
4. Response    → redirect to list
```

**Tables Written**: `ret_design_master` + 5 child tables (`ret_design_karigars`, `ret_design_purity`, `ret_design_other_materials`, `ret_design_sizes`, `ret_design_stone`)
**⚠️ Bug**: `fixed_rate` key duplicate at L7867-7869 — second value overwrites first (both identical, so no data loss, but code smell)
**⚠️ Risk**: Same DELETE-then-INSERT pattern on UPDATE (not shown here but follows category pattern)

## Flow 10: Tax Group CREATE (Header + Line Items)

```
1. Controller  → tgrp('add') at L7120
   1a. Load empty tax group + empty tax group item
   1b. Load view master/tax/tax group/form
2. User        → Fills: tgrp_name, tgrp_status + N tax line items (tax_id, calculation, type)
3. Controller  → tgrp('save') at L7140
   3a. Build header: {tgrp_name, tgrp_status, created_by, created_time}
   3b. trans_begin()
   3c. insertData($data, 'ret_taxgroupmaster') → returns $tgrp_id
   3d. Loop: foreach tgi → insertData({tgi_tgrpcode, tgi_taxcode, tgi_calculation, tgi_type}, 'ret_taxgroupitems')
   3e. trans_commit() / trans_rollback()
   3f. Audit log
4. Response    → redirect to list
```

**Tables Written**: `ret_taxgroupmaster` (header), `ret_taxgroupitems` (× N line items)
**Pattern**: Classic header-detail — FK from items back to group via `tgi_tgrpcode`

## Flow 11: Financial Year Status Toggle

```
1. Browser     → financial_year list → click Active/Inactive toggle
2. Controller  → financial_status($status, $id) at L9628
   2a. Build $data = {fin_status: $status}
   2b. setFinancialYearStatus() → ⚠️ DEACTIVATES ALL financial years first
   2c. updateData($data, 'fin_id', $id, 'ret_financial_year') → activates selected one
   2d. Flashdata success/fail message
3. Response    → redirect('admin_ret_catalog/financial_year/list')
```

**Tables Written**: `ret_financial_year` (UPDATE all rows to inactive, then UPDATE one to active)
**⚠️ Risk**: NOT wrapped in trans_begin/trans_commit — if the second UPDATE fails, ALL financial years are inactive
**Business Logic**: Only one financial year can be active at a time (enforced by deactivate-all-then-activate-one pattern)

## Flow 12: Karigar Approval (Multi-Case Workflow)

```
1. Controller  → karigar_approval('list') at L17940
   1a. get_profile_settings(profile) → OTP settings
   1b. get_access('karigar_approval/list') → permissions
   1c. Load view master/karigar/approval_list
2. User        → Reviews pending wastage/stone approvals → Approve/Reject
3. JS          → POST with: status (1=approve, 0=reject), approval_for (0=wastage, 1=stone), approved_data[]
4. Controller  → karigar_approval('save') at L17957
   4a. trans_begin()
   4b. If approval_for == 1 (stones) AND status == 1 (approved):
       → Loop: update_karigar_stones() for each approved stone record
   4c. Loop through all approved_data:
       → If status == 1 (approved):
         → If approval_for == 0 (wastage):
           → Check if cat_id == 0 (all categories) → update_karigar_wastages(karigar_id)
           → Else check existing categories for overlap → update accordingly
         → If approval_for == 1 (stones):
           → update_karigar_stones() with new rates
       → If status == 0 (rejected):
         → Update approval_status to rejected
   4d. trans_commit() / trans_rollback()
   4e. Audit log
5. Response    → redirect to approval list
```

**Tables Written**: `ret_karikar_items_wastage` (UPDATE), `ret_karigar_stones` (UPDATE)
**Complexity**: ⚠️ Complex nested conditionals — approval_for × status × cat_id combinations
**OTP Flow** (if settings enabled): `vendor_sendotp()` → SMS → `vendor_verify_otp()` → then save

## Flow 13: Bulk Product Update

```
1. Controller  → bulkprodupdated() → load view master/ret_product/bulk_prod_upd
2. User        → Selects multiple products + new tax_group_id and/or product_status
3. Controller  → bulkprodupdated('update') at L6716
   3a. Parse $_POST: product_ids[], product_name, tax_group_id, product_status
   3b. trans_begin()
   3c. Loop: foreach product_id → updateData({tax_group_id, product_status}, 'pro_id', $id, 'ret_product_master')
   3d. trans_commit() / trans_rollback()
4. Response    → redirect to list
```

**Tables Written**: `ret_product_master` (UPDATE × N)
**⚠️ Risk**: No batch size limit — large selections could timeout. Partial failures inside the transaction could leave some products updated and others not.

## Flow 14: Generic Entity Status Toggle (Pattern)

> All simple master entities (purity, color, cut, clarity, stone, material, making_type, theme, screw, hook, weight, UOM, section, tag, etc.) follow this identical pattern:

```
1. Browser     → entity list → click status toggle button
2. JS          → Triggers GET → /admin_ret_catalog/{entity}_status/{status}/{id}
3. Controller  → {entity}_status($status, $id)
   3a. Build $data = {status_field: $status}
   3b. updateData($data, 'pk_field', $id, 'ret_{entity}')
   3c. Flashdata success/fail
4. Response    → redirect to entity list
```

**⚠️ Risk**: All status toggles use GET requests — CSRF vulnerable (no POST/token required)
**Pattern**: Consistent across all ~30 simple entity modules

