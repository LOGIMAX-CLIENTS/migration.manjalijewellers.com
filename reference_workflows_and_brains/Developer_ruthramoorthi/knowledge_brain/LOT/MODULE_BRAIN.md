# LOT MODULE DIGITAL BRAIN v4.4

**Module**: LOT (Lot Inward Management)
**Built**: 2026-02-19 | **Updated**: 2026-02-19 (Rounds 2+3+4+5) | **Protocol**: build-module-brain.md workflow
**Sources**: admin_ret_lot.php, ret_lot_model.php, ret_lot.js, lot/ views, lot_ack.css

---

## 1. PURPOSE & SCOPE

The LOT module manages the lifecycle of jewelry lot inventory — from inward receipt through merge, split, tagging, and acknowledgement. It handles:
- **Lot Inward**: Receipt of goods from suppliers/karigar with multi-item rows
- **Lot Merge**: Combining multiple lots into one (creates `lot_from=7` records)
- **Lot Split**: Dividing a single lot into smaller lots (split by pcs/weight/stones)
- **Lot Tagging**: Assigning individual tags to lot items (via ret_taging)
- **Lot Item Deletion**: Deleting individual lot items (via `lot_inwards_detail` — checks tag status first)
- **Acknowledgements**: Vendor, office, branch, and customer acknowledgement PDFs
- **Lot Completion**: Closing lots (batch operation) after all items are processed
- **Stone Management**: 3 stone types per item — Precious, Semi-Precious, Normal — each with certificates
- **Other Metals/Charges**: Per-item other metal details and additional charges (modals)

---

## 2. FILE MANIFEST

| File | Path | Lines | Role |
|---|---|---|---|
| Controller | [admin_ret_lot.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/controllers/admin_ret_lot.php) | 2566 | All HTTP endpoints; mega-method `lot_inward` (1386 lines) |
| Model | [ret_lot_model.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/models/ret_lot_model.php) | 1903 | 48 DB functions; all SQL queries |
| JavaScript | [ret_lot.js](file:///c:/xampp/htdocs/etail_development_src/admin/assets/js/ret_lot.js) | 23630 | 206 functions; UI logic, AJAX, form handling |
| View: Form | [form.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/views/lot/form.php) | 2960 | Add/Edit form — header fields, item table, stone/metal/charges modals, image upload, narration |
| View: List | [list.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/views/lot/list.php) | 447 | Lot listing (DataTables) + purchase details modal + cancel lot modal (with remarks) |
| View: Merge | [lot_merge.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/views/lot/lot_merge.php) | 1067 | Merge UI — lot search, detail table, merged items table, stone modal |
| View: Split | [lot_split.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/views/lot/lot_split.php) | 649 | Split UI — lot search, summary table, split detail table, preview table, stone modal |
| Print: Vendor Ack | [vendor_ack.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/views/lot/print/vendor_ack.php) | 136 | Vendor PDF: lot summary (S.No/Items/Pcs/Gwt/Purity), footer: Received By + Vendor Sign |
| Print: Office Ack | [office_ack.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/views/lot/print/office_ack.php) | 249 | Office PDF: lot summary (incl. LWT/NWT) + branch summary (DIA WT + Amount + Branch), Diff.Gwt/Diff.Pcs calc, 3 signatures |
| Print: Branch Ack | [branch_ack.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/views/lot/print/branch_ack.php) | 231 | Branch PDF: summary table + (type=2) tag-level details with Tag Code/Design/Sub Design/Pcs/Gwt/Rate |
| Print: Customer Ack | [customer_ack.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/views/lot/print/customer_ack.php) | 384 | 🔴 DOMPDF DISABLED. Full GST (CGST/SGST/IGST), stone+charges per item, grand total, moneyFormatIndia(), 4 signatures, auto window.print(). Uses different CSS: `customer_job_receipt.css` |
| Print: Legacy | [lot_acknowladgement_old.php](file:///c:/xampp/htdocs/etail_development_src/admin/application/views/lot/print/lot_acknowladgement_old.php) | — | Legacy acknowledgement file (unused) |
| CSS | [lot_ack.css](file:///c:/xampp/htdocs/etail_development_src/admin/assets/css/lot_ack.css) | 266 | 3 receipt classes: `.PDFReceipt`, `.PDF_CusReceipt`, `.PDF_receipt_thermal`. Logo, table layout, dashed borders |
| CSS | [customer_job_receipt.css](file:///c:/xampp/htdocs/etail_development_src/admin/assets/css/customer_job_receipt.css) | — | Used only by customer_ack.php (different from lot_ack.css) |

**External Dependencies (loaded in constructor L21-39):**
- `ret_lot_model` — primary model
- `admin_settings_model` — settings, access control, day closing
- `ret_catalog_model` — UOM lookups (loaded twice — 🔴 BUG)
- `log_model` — audit trail
- Libraries: `dompdf` (PDF generation — disabled for customer_ack, active for vendor/office/branch)

---

## 3. ENTRY POINTS & ROUTES

| Route | HTTP | Method | Lines | Description |
|---|---|---|---|---|
| `admin_ret_lot/lot_inward/add` | GET | `lot_inward('add')` | 307-325 | Load empty add form |
| `admin_ret_lot/lot_inward/list` | GET | `lot_inward('list')` | 327-335 | Load lot listing page |
| `admin_ret_lot/lot_inward/save` | POST | `lot_inward('save')` | 337-1047 | Save new lot + items + stones + images |
| `admin_ret_lot/lot_inward/edit/{id}` | GET | `lot_inward('edit', id)` | 1120-1164 | Load edit form with data |
| `admin_ret_lot/lot_inward/lot_edit/{id}` | AJAX | `lot_inward('lot_edit', id)` | 1167-1190 | AJAX: get lot data for edit |
| `admin_ret_lot/lot_inward/update/{id}` | POST | `lot_inward('update', id)` | 1251-1651 | Update lot + items |
| `admin_ret_lot/lot_inward/delete/{id}` | GET | `lot_inward('delete', id)` | 1195-1247 | Delete lot + images |
| `admin_ret_lot/lot_inward/cancel_lot_entry` | POST | `lot_inward('cancel_lot_entry')` | 1049-1115 | Cancel a lot |
| `admin_ret_lot/lot_inward` | POST | `lot_inward('')` (default) | 1653-1681 | AJAX: get lot list with filters |
| `admin_ret_lot/get_lotInward_detail` | POST | `get_lotInward_detail()` | 1687-1701 | AJAX: get lot detail by ID |
| `admin_ret_lot/lot_inwards_detail` | POST | `lot_inwards_detail()` | 1703-1769 | AJAX: get lot details for acknowledgement |
| `admin_ret_lot/vendor_acknowladgement/{type}/{id}` | GET | `vendor_acknowladgement()` | 1773-1805 | PDF: vendor ack |
| `admin_ret_lot/lot_acknowladgement/{type}/{id}` | GET | `lot_acknowladgement()` | 1807-1841 | PDF: office ack |
| `admin_ret_lot/branch_acknowladgement/{type}/{id}/{branch}` | GET | `branch_acknowladgement()` | 1843-1877 | PDF: branch ack |
| `admin_ret_lot/getOrderNosBySearch` | POST | `getOrderNosBySearch()` | 1879-1889 | AJAX: search order numbers |
| `admin_ret_lot/get_order_details` | POST | `get_order_details()` | 1891-1901 | AJAX: get order details |
| `admin_ret_lot/get_karigar_list` | POST | `get_karigar_list()` | 1907-1917 | AJAX: get karigar list |
| `admin_ret_lot/getProductBySearch` | POST | `getProductBySearch()` | 1921-1933 | AJAX: search products |
| `admin_ret_lot/lot_merge/{type}/{id}` | varies | `lot_merge()` | 1935-2288 | Lot merge (add/save/edit/update/delete) |
| `admin_ret_lot/get_ActiveProduct` | POST | `get_ActiveProduct()` | 2290-2300 | AJAX: get active products |
| `admin_ret_lot/lot_split/{type}` | varies | `lot_split()` | 2302-2459 | Lot split (add/save/list) |
| `admin_ret_lot/lot_completed` | POST | `lot_completed()` | 2461-2525 | Close lot |
| `admin_ret_lot/customer_acknowladgement/{type}/{id}` | GET | `customer_acknowladgement()` | 2528-2562 | PDF: customer ack |

---

## 3b. JAVASCRIPT ARCHITECTURE (ret_lot.js — 23,630 lines)

### Global Variables (L1-100)
20 global arrays initialized at file top:
`img_resource`, `pre_img_files`, `pre_img_resource`, `sp_img_files`, `sp_img_resource`, `n_img_files`, `n_img_resource`, `uom_details`, `total_files`, `lot_cat_details`, `section_details`, `lot_product_details`, `purities`, `lot_design`, `lot_sub_design`, `lot_designs`, `lot_sub_designs`, `modalStoneDetail`, `modalOthermetal`, `charges_list`, `category_lists`, `metal_details`

### `$(document).ready()` Block (L105-2275)

| Section | Lines | Purpose |
|---|---|---|
| Number input validation | L125-177 | Blocks arrow keys + non-numeric input on `input[type='number']` |
| Page routing switch | L185-901 | `ctrl_page[1]` routes: lot_inward (list/edit/add), lot_merge, lot_split |
| **`#save_all` click** | **L905-959** | **🔴 BUG-013: `$('#lot_form').submit()` then immediately `window.location.href` — race condition. The redirect fires before form submission completes** |
| Date range picker | L963-1035 | Standard daterangepicker with Today/Yesterday/Last 7-30/Month presets |
| Filter handlers | L1039-1051 | Branch change triggers list reload |
| Net wt styling | L1059-1087 | Colors negative net_wt red (visual only) |
| Stock type radio | L1091-1211 | Tagged(1): requires purity/category, shows `.tagged` elements. Non-tagged(2): hides tagged, enables section selector |
| Search by radio | L1215-1251 | Toggle between customer search and order number search |
| Category change | L1719-1947 | Confirm dialog if items exist → update all items' category → reload purities via `get_cat_purity()` |
| Purity change | L1975-2119 | Same confirm pattern if items already added |
| Image upload | L2183-2271 | AJAX POST to `admin_ret_lot/upload_lotimg` with FormData, appends cache-busting param |
| Stone checkbox handlers | L1507-1711 | 3 handlers for precious/semi-precious/normal — enable/disable pcs+wt+certificate fields |
| Gold smith change | L1455-1495 | Sets hidden `#lt_gold_smith_id`, calls `get_karigar_details()` |

### Edit Mode Initialization (L263-541)
Loads 13 data-fetch functions via `setTimeout(1000)`:
`get_Branchwise_Sections`, `get_ActiveProduct`, `getActiveUOM`, `get_category`, `get_stones`, `get_stone_types`, `getActive_quality_code`, `getQualityDiamondRates`, `get_ActiveMetals`, `get_ActivePurity`, `get_charges`, `get_taxgroup_items`, `set_edit_lot_row`

### AJAX Endpoint Inventory (JS → Controller)

#### LOT Controller Endpoints
| JS Function | AJAX URL | Method | Purpose |
|---|---|---|---|
| `get_lotInward_list()` | `admin_ret_lot/lot_inward/ajax` | POST | Fetch lot list with date/type/metal/emp filters |
| Image upload (#lot_img_upload) | `admin_ret_lot/upload_lotimg` | POST | Upload lot images (FormData) |
| `remove_img()` | `admin_ret_lot/remove_img` | POST | Delete image by file/folder/field |
| `getSearchOrderNo()` | `admin_ret_lot/getOrderNosBySearch` | POST | Autocomplete order number search |
| `getSearchProducts()` | `admin_ret_lot/getProductBySearch` | POST | Autocomplete product search (per-row) |
| `create_new_empty_lot_row()` [order] | `admin_ret_lot/get_order_details` | POST | Fetch order items to pre-fill row |
| `get_karigar_by_order()` | `admin_ret_lot/get_karigar_list` | POST | Auto-fill karigar/category/purity from order |
| `remove_row()` [edit mode] | `admin_ret_lot/lot_inwards_detail` | POST | Delete lot item (modal confirm) |
| `getLotidsforMerge()` | `admin_ret_lot/lot_merge/getLotidsforMerge` | **GET** | Fetch mergeable lot IDs |
| `Lot_Completed_Details()` | `admin_ret_lot/lot_completed` | POST | Close lots (batch) |
| `#lot_cancel` click | `admin_ret_lot/lot_inward/cancel_lot_entry` | POST | Cancel lot (requires >6 char remark) |

#### Cross-Controller Calls 🟡
| JS Function | AJAX URL | Controller | Purpose |
|---|---|---|---|
| `get_category()` | `admin_ret_catalog/category/active_category` | CATALOG | Load categories → `#category` select2 |
| `get_cat_purity()` | `admin_ret_catalog/category/cat_purity` | CATALOG | Load purities for category → `#purity` |
| `get_karigar()` | `admin_ret_catalog/karigar/active_list` | CATALOG | **GET** — Load karigar list |
| `getActiveUOM()` | `admin_ret_catalog/uom/active_uom` | CATALOG | Load UOM list |
| `getSearchProd()` | `admin_ret_catalog/product/active_prodBySearch` | CATALOG | Autocomplete product search |
| `getActiveDesigns()` | `admin_ret_catalog/get_active_design_products` | CATALOG | Design dropdown by product |
| `get_ActivelotSubDesigns()` | `admin_ret_catalog/get_ActiveSubDesigns` | CATALOG | Sub-design dropdown |
| `get_Branchwise_Sections()` | `admin_ret_catalog/get_sectionBranchwise` | CATALOG | Load sections by branch |
| `get_ActiveMetals()` | `admin_ret_catalog/ret_product/active_metal` | CATALOG | **GET** — Load metals list |
| `getSearchDesign()` | `admin_ret_brntransfer/branch_transfer/getDesignByFilter` | BRNTRANSFER | Design search |
| `getSearchDesigns()` | `admin_ret_estimation/getProductDesignBySearch` | ESTIMATION | Design autocomplete |
| `getSearchCustomers()` | `admin_ret_estimation/getCustomersBySearch` | ESTIMATION | Customer autocomplete |
| `get_employee()` | `admin_ret_estimation/get_employee` | ESTIMATION | Employee dropdown |
| `get_ActiveProduct()` | `admin_ret_estimation/get_ActiveProduct` | ESTIMATION | Product cache → `lot_product_details` |
| `getOrdersByCus()` | `admin_ret_order/order/getOrderByCus` | ORDER | Orders by customer |
| `get_karigar_details()` | `admin_ret_purchase/get_karigar_details` | PURCHASE | Karigar TCS/TDS/country/state details |
| `get_taxgroup_items()` | `admin_ret_billing/getAllTaxgroupItems` | BILLING | **GET** — Tax group items for GST calc |

### DataTable Configuration (L2903-3852)

16 columns: Lot No (with checkbox for unclosed), Date, Lot From, Pur Ref No, Product, Karigar, Employee, Pcs, Gwt, Tagged Pcs (calc), Tagged Gwt (calc), Drill-down, Purchase Details, Bal Pcs (calc), Bal Gwt (calc), Pure Wt, Actions

- Footer totals: pcs, gwt, tagged_pcs, tagged_gwt, bal_pcs, bal_wt, pure_wt
- Export: Print + Excel with report header
- Drill-down: `fnFormatRowTagDetails()` shows branch-wise tag summary with 2 branch print buttons (detail + summary)

### Action Button Conditions (L3484-3504)

| Button | Condition |
|---|---|
| Edit | `lot_from==1` AND `is_closed!=1` AND `access.edit==1` AND no tagged items (bal_pcs == tot_pcs) |
| Vendor Print | Always visible |
| Office Print | Always visible |
| Customer Print | Always visible |
| Cancel | `tag_lot_id` empty AND `lot_status!=2` AND (`lot_date==today` OR `profile.allow_lot_cancel==1`) AND `access.delete==1` AND no tagged items |

### Row Builder: `create_new_empty_lot_row()` (L5764-6440)

**Two variants**:
1. **Manual (no order)**: Builds 16-column `<tr>` with section/product/design/subdesign as select2, pcs, gwt+UOM, lwt+UOM, nwt+UOM, wastage%, making charge (Gram/Piece), buy rate, sell rate, size, stone modal button (18 hidden fields for 3 stone types), remove button
2. **Order-based**: AJAX to `admin_ret_lot/get_order_details` → pre-fills product_name, design_name, pcs, gross_wt, net_wt, wastage_per, size from order — fields become readonly

### Validation: `validateItemDetailRow()` (L7376-7524)

| sales_mode | calculation_based_on | Required Fields |
|---|---|---|
| 1 (Fixed Rate) | 3 (Per Piece) | pro_id, pcs |
| 1 (Fixed Rate) | 4 (Per Gram) | pro_id, pcs, gross_wt |
| 2 (Fixed Rate by Weight) | any | pro_id, wastage%, pcs, gross_wt, net_wt |
| other | any | pro_id, wastage%, pcs, gross_wt, net_wt |
| stock_type=2 (Tagged) | any | + product, design, sub_design, section |

### Row Management: `remove_row()` (L7092-7280)

| Mode | Behavior |
|---|---|
| Add mode | Immediate DOM removal + `get_lot_preview()` |
| Merge mode | Immediate DOM removal + `TotalLotMerge()` |
| Edit mode | Modal confirm → AJAX DELETE to `lot_inwards_detail` → remove row + `get_lot_preview()` |

### Design Duplicate Check (L8652-8708)
`getSearchDesigns()` prevents same product+design combination by iterating existing rows and alerting _"Already Same Product and design Exist."_

### Net Weight Calculation (L5648-5672)
```
gross_wt - lot_lwt = lot_nwt  (inline keyup handler, 3-decimal precision)
```

### Stone Certificate Images (L7596-8100)
3 separate image arrays per stone type: `pre_img_resource`, `sp_img_resource`, `n_img_resource`
- File validation: 1MB max, jpg/png/jpeg only
- Uses `b64toBlob()` helper for base64 conversion
- `remove_stn_img()` handles both new uploads (splice array) and existing uploads (AJAX `remove_img`)

### Lot Merge JS (L10220-11300)
- `getLotidsforMerge()`: GET endpoint, populates `#lot_no_merge` select2
- `getLotNoForMerge()`: POST, fetches lot details for merge preview with item breakdown
- `TotalLotMerge()`: Calculates aggregate totals (pcs, gwt, stn_pcs, stn_wt, dia_pcs, dia_wt)
- Merge row builder creates merge preview rows with branch/product/design info

### Lot Split JS (L13800-14558)
- `get_lotNoForSplit()`: Fetches split-eligible lots with balance tracking
- Balance validation (L14200-14538): Validates entered split quantities against remaining balance for **6 fields**: pcs, gwt, stn_pcs, stn_wt, dia_pcs, dia_wt — shows toaster warning for each
- `#lot_split_submit`: Direct `form.submit()` (no race condition here ✅)

### Lot Close/Cancel JS (L14800-23326)
- `#lot_closed` click (L14800): Collects checked lot_no checkboxes → batch `Lot_Completed_Details()` AJAX
- `#cancel_remark` keypress (L23244): Enables cancel button only when remark > 6 chars
- `#lot_cancel` click (L23278): Sends cancel_reason + lot_no → reloads page

### GST Tax Calculation Engine (L23196-23496)
- `get_taxgroup_items()`: Fetches tax group data from **BILLING** controller (GET)
- `calculate_base_value_tax()`: **Exclusive** GST — adds tax to base price
- `calculate_inclusiveGST()`: **Inclusive** GST — extracts tax from total: `base = total * 100 / (100 + tax%)`
- GST split logic: same state → CGST+SGST (50/50), different state → IGST (100%)
- Populates: `#item_cgst_cost`, `#item_sgst_cost`, `#item_igst_cost`, `#item_total_tax`, `#item_tax_percentage`

### Duplicate `$(document).ready()` (L23604) 🔴
A **second** `$(document).ready()` at L23604-23629 adds input validation for `#lot_gross_wt` (3 decimal places). This should be merged into the main `$(document).ready()` block (L105-2275).

---

## 4. EXECUTION FLOW DIAGRAMS

### Lot Inward — Save Flow
```
Browser → [jQuery AJAX/Form POST] → admin_ret_lot/lot_inward/save
  ↓
  Controller::lot_inward('save') [L337]
    ├─ Read $_POST['inward'] (raw — 🔴 no CI input)
    ├─ getBranchDayClosingData() → bill_date
    ├─ Build header $data array (18 fields)
    ├─ $this->db->trans_begin()
    ├─ insertData($data, 'ret_lot_inwards') → $insId
    ├─ Process lot_image (mkdir 0777 — 🔴)
    ├─ FOREACH $_POST['inward_item']:
    │   ├─ Process stone images (base64→file)
    │   ├─ Build $item_details (30+ fields)
    │   ├─ insertData($item_details, 'ret_lot_inwards_detail') → $detail_insId
    │   ├─ FOREACH stone_details: insertData → ret_lot_inwards_stone_detail
    │   ├─ FOREACH other_items: insertData → ret_lot_other_items
    │   ├─ FOREACH other_charges: insertData → ret_lot_other_charges
    │   └─ IF stock_type=2: insert/update ret_nontag_item + logs
    ├─ IF trans_status()===TRUE:
    │   ├─ log_detail('insert')
    │   ├─ trans_commit()
    │   ├─ redirect → lot_acknowladgement [L1025] ← 🔴 UNREACHABLE redirect below
    │   └─ redirect → lot_inward/list [L1026] ← DEAD CODE
    └─ ELSE:
        ├─ echo db->last_query() ← 🔴 INFO LEAK
        ├─ echo _error_message(); exit; ← 🔴 EXITS BEFORE ROLLBACK
        ├─ trans_rollback() ← UNREACHABLE
        └─ redirect ← UNREACHABLE
```

### Lot Inward — Edit Flow
```
Browser → admin_ret_lot/lot_inward/edit/{id}
  ↓
  Controller [L1120]:
    ├─ get_lotInward($id) → header data
    ├─ CHECK lot_from != 1 → reject (non-manual lots)
    ├─ CHECK is_closed == 1 → reject
    ├─ get_lotInward_detail($id) → items  ← N+1: calls 3 sub-queries per item
    ├─ Load form view with data
    ↓
  JS: populateEditForm() → FOREACH item:
    ├─ create_new_empty_lot_row()
    ├─ getActiveDesigns(row, pro_id) → AJAX → row.find('.design').append()
    ├─ get_ActivelotSubDesigns(row, des_id) → AJAX → row.find('.lot_sub_design').append()
    └─ Set field values from response
```

---

## 5. CONTROLLER METHOD CATALOG

### `lot_inward($type, $id)` — MEGA METHOD (L297-1683, 1386 lines)

| Case | Lines | Input | Output | Notes |
|---|---|---|---|---|
| `add` | 307-325 | — | View: lot/form | Loads empty record |
| `list` | 327-335 | — | View: lot/list | Loads list page |
| `save` | 337-1047 | POST inward, inward_item, FILES | Redirect | 🔴 Raw $_POST, mkdir 0777 |
| `cancel_lot_entry` | 1049-1115 | POST lot_no, cancel_reason | JSON | Uses raw $_POST |
| `edit` | 1120-1164 | URL $id | View: lot/form | Checks lot_from, is_closed |
| `lot_edit` | 1167-1190 | URL $id | JSON | AJAX endpoint |
| `delete` | 1195-1247 | URL $id | Redirect | Deletes with rrmdir |
| `update` | 1251-1651 | POST inward, inward_item | Redirect | 🔴 Same issues as save |
| `default` | 1653-1681 | POST filters | JSON | Uses CI input->post ✅ |

### Other Methods (< 50 lines each)

| Method | Lines | Purpose |
|---|---|---|
| `__construct` | 21-71 | Auth check, load models (🔴 ret_catalog_model loaded twice) |
| `upload_img` | 83-145 | Image processing (🟡 no MIME whitelist) |
| `rrmdir` | 147-169 | Recursive directory delete |
| `remove_img` | 173-257 | 🔴 Uses raw $_POST for file paths |
| `base64ToFile` | 267-293 | Convert base64 to temp file |
| `get_lotInward_detail` | 1687-1701 | AJAX proxy |
| `lot_inwards_detail` | 1703-1769 | 🔴 DELETES lot items (misleading name — not just retrieval). Checks tag status first via `check_is_tagged()`, then deletes from `ret_lot_inwards_detail`. Has proper transaction handling ✅ |
| `vendor_acknowladgement` | 1773-1805 | PDF generation |
| `lot_acknowladgement` | 1807-1841 | PDF generation |
| `branch_acknowladgement` | 1843-1877 | PDF generation |
| `customer_acknowladgement` | 2528-2562 | 🔴 DOMPDF commented out — echoes raw HTML via `echo $html; exit;` (L2552) |
| `lot_merge` | 1935-2288 | Mega-method: list/getLotNos/getLotidsforMerge/save. Save creates `lot_from=7`, handles stone details, non-tag items. 🔴 Same echo+exit before rollback bug at L2270-2274 |
| `lot_split` | 2302-2459 | Switch: list/lotNosForsplit/getLotDetails/getLotidsforSplit/save. 🔴 Missing `trans_begin()` before save loop + same echo+exit before rollback at L2443-2447 |
| `lot_completed` | 2461-2525 | Close lot (batch). 🔴 Uses raw `$_POST['completed_lot']` and `$_POST['branch']` (NOT CI input) |

---

## 6. DATABASE INTERACTION MAP

### Tables (Write)

| Table | Operation | Key Columns | Called From |
|---|---|---|---|
| `ret_lot_inwards` | INSERT/UPDATE/DELETE | lot_no (PK), lot_date, lot_type, lot_received_at, id_category, id_purity, gold_smith, order_no, stock_type, lot_status, is_closed, lot_from | save, update, delete, cancel |
| `ret_lot_inwards_detail` | INSERT/UPDATE/DELETE | id_lot_inward_detail (PK), lot_no (FK), lot_product, lot_id_design, id_sub_design, gross_wt, net_wt, less_wt, no_of_piece, mc_type, making_charge, item_cost, calc_type, rate, rate_calc_type | save, update |
| `ret_lot_inwards_stone_detail` | INSERT/DELETE | stone_pcs, stone_wt, stone_id, stone_quality_id, is_apply_in_lwt, stone_cal_type, rate_per_gram, price | save, update |
| `ret_lot_other_items` | INSERT | other_item_pcs, item_gross_weight, item_metal, other_item_purity | save |
| `ret_lot_other_charges` | INSERT | charge_id, calc_type, charge_value, total_charge_value | save |
| `ret_nontag_item` | INSERT/UPDATE | product, design, id_sub_design, no_of_piece, gross_wt, net_wt, branch | save (stock_type=2) |
| `ret_nontag_item_log` | INSERT | product, design, from_branch, to_branch | save (stock_type=2) |
| `ret_section_nontag_item_log` | INSERT | to_section, product, design | save (stock_type=2) |
| `ret_lot_merge` | INSERT/UPDATE | lot_no, id_lot_inward_detail | lot_merge |
| `ret_lot_split_details` | INSERT | lot_no, id_lot_inward_detail, split_pcs, split_grs_wt | lot_split |

### Tables (Read)

| Table | Purpose | Joined Via |
|---|---|---|
| `ret_product_master` | Product name, settings | pro_id |
| `ret_design_master` | Design name, code | design_no |
| `ret_sub_design_master` | Sub-design name | id_sub_design |
| `ret_category` | Category name, metal | id_ret_category |
| `ret_purity` | Purity value | id_purity |
| `ret_karigar` | Supplier/karigar name | id_karigar |
| `branch` | Branch name | id_branch |
| `ret_settings` | Module configuration | name='setting_key' |
| `profile` | User profile/permissions | id_profile |
| `customerorder` / `customerorderdetails` | Customer order data | id_customerorder |
| `joborder` | Job order association | id_order |
| `ret_taging` | Tag data (for merge/split eligibility) | tag_lot_id |
| `ret_stone` | Stone master | stone_id |
| `ret_uom` | Unit of measure | uom_id |
| `employee` | Employee name | id_employee |
| `ret_nontag_receipt` | Non-tag receipt for balance calc | id_lot_inward_detail |

### 🔴 SQL Injection Risks (String Concatenation)

| Function | Line | Risk |
|---|---|---|
| `ajax_getLotList` | L190-227 | `$id_metal`, `$emp_id`, `$lot_type` concatenated |
| `getTaggedDetails` | L309 | `$lot_no` concatenated |
| `get_tagged_branchwise_details` | L339 | `$lot_no` concatenated |
| `get_profile_settings` | L287 | `$id_profile` concatenated |
| `get_lotInward` | L485 | `$id` concatenated |
| `get_lotInward_detail` | L537 | `$id` concatenated |
| `getorderdesigns` | L566 | `$data['id_product']` — also `$data` undefined! 🔴 |
| `get_ret_settings` | L582 | `$settings` concatenated |
| `get_branchName` | L598 | `$branch` concatenated |
| `lotInward_detail` | L642 | `$id` concatenated |
| `get_lot_details` | L690 | `$lot_no` concatenated |
| `get_lot_tag_details` | L738-740 | `$lot_no` concatenated |
| `get_tag_details` | L784 | `$lot_no` concatenated |
| `get_branch_summary` | L814-816 | `$tag_lot_id`, `$id_branch` concatenated |
| `get_tagdetails_by_lot` | L870-872 | `$tag_lot_id`, `$id_branch` concatenated |
| `getOrderNos` | L906 | `$SearchTxt` concatenated |
| `get_order_details` | L930-932 | `$orderno`, `$id_karigar`, `$id_branch` concatenated |
| `get_karigar_list` | L960-976 | `$order_no` concatenated |
| `getProductBySearch` | L1034-1038 | `$SearchTxt`, `$category`, `$stock_type` concatenated |
| `checkNonTagItemExist` | L1062-1070 | Multiple fields concatenated |
| `updateNTData` | L1094 | `$data` fields + `$arith` operator concatenated |
| `getLotNoForMerge` | L1196 | `$data['lot_no']` concatenated |
| `getLotNoForSplit` | L1386 | `$data['lot_no']` concatenated |
| `getLotDetails` | L1532 | `$data['lot_no']` concatenated |
| `get_lotInward_data` | L1723 | `$id` concatenated |
| `get_customer_lotInward_detail` | L1777 | `$id` concatenated |
| `get_customer_lot_details` | L1819 | `$lot_no` concatenated |
| `getlotStoneDetails` | L1863 | `$lot_item_id` concatenated |
| `getlotOtherMetalDetails` | L1874 | `$lot_item_id` concatenated |
| `getlotOtherChargeDetails` | L1894 | `$lot_item_id` concatenated |

**Total: 30+ functions with SQL injection risk**

---

## 7. STATE MANAGEMENT MAP

### Global JS Variables (ret_lot.js L1-104)

| Variable | Purpose | Risk |
|---|---|---|
| `img_resource[]` | Lot images array | Global — shared across all rows |
| `pre_img_files[]` / `pre_img_resource[]` | Precious stone images | Global |
| `sp_img_files[]` / `sp_img_resource[]` | Semi-precious images | Global |
| `lot_design[]` / `lot_sub_design[]` | Design dropdowns | Panel-level → 🔴 was causing row-level bugs |
| `lot_designs[]` / `lot_sub_designs[]` | Alternate design arrays | Unclear usage |
| `modalStoneDetail[]` | Stone modal data | Global modal state |
| `modalOthermetal[]` | Other metal modal data | Global modal state |
| `charges_list[]` | Charges list | Global |
| `category_lists[]` | Category cache | Global |
| `metal_details[]` | Metal details cache | Global |

### DOM-Based State (Hidden Fields)

| Element | Purpose | Set By |
|---|---|---|
| `#id_category` | Category ID | category.select2.change |
| `#id_purity` | Purity ID | purity.select2.change |
| `#lt_order_id` | Order ID | lot_order_no.change |
| `#cur_id` | Current lot_no (edit mode) | PHP view |
| Row: `.pro_id` | Product ID per row | `.lot_product` change |
| Row: `.des_id` | Design ID per row | `.design` change |
| Row: `.lot_id_sub_design` | Sub-design ID per row | `.lot_sub_design` change |

---

## 8. DATA FLOW MAP

### Inward Save: User → DB

```
User fills form
  ↓
JS reads:          HTML element              → JS variable/hidden field
  category:        #category select2         → #id_category hidden
  purity:          #purity select2           → #id_purity hidden
  product:         .lot_product (per row)    → .pro_id (per row)
  design:          .design (per row)         → .des_id (per row)  [BUG-UI-005 FIXED]
  sub_design:      .lot_sub_design (per row) → .lot_id_sub_design (per row)
  ↓
Form POST:         name="inward[field]"       → $_POST['inward'][field]
                   name="inward_item[i][field]"→ $_POST['inward_item'][i][field]
  ↓
PHP Controller:    $_POST['inward']          → $addData
                   $_POST['inward_item']     → foreach $itemData
  ↓
PHP Model:         $data array               → insertData($data, 'table')
  ↓
DB:                ret_lot_inwards           → lot_no (auto-increment PK)
                   ret_lot_inwards_detail    → id_lot_inward_detail (PK)
```

### Field Name Mapping (Critical Fields)

| DB Column | PHP POST Key | HTML Element | JS Selector |
|---|---|---|---|
| `lot_product` | `inward_item[i][lot_product]` | `<select class="lot_product">` | `curRow.find('.lot_product')` |
| `lot_id_design` | `inward_item[i][lot_id_design]` | `<input class="des_id">` | `curRow.find('.des_id')` |
| `id_sub_design` | `inward_item[i][id_sub_design]` | `<input class="lot_id_sub_design">` | `curRow.find('.lot_id_sub_design')` |
| `gross_wt` | `inward_item[i][gross_wt]` | `<input name="...[gross_wt]">` | `curRow.find('[name*=gross_wt]')` |
| `net_wt` | `inward_item[i][net_wt]` | `<input name="...[net_wt]">` | `curRow.find('[name*=net_wt]')` |
| `less_wt` | `inward_item[i][less_wt]` | `<input name="...[less_wt]">` | `curRow.find('[name*=less_wt]')` |
| `no_of_piece` | `inward_item[i][pcs]` | `<input name="...[pcs]">` | `curRow.find('[name*=pcs]')` |
| `mc_type` | `inward_item[i][id_mc_type]` | `<select class="mc_type">` | `curRow.find('.mc_type')` |
| `making_charge` | `inward_item[i][making_charge]` | `<input name="...[making_charge]">` | `curRow.find('[name*=making_charge]')` |

---

## 9. VALIDATION RULES CATALOG

| Field | Frontend (JS) | Backend (PHP) | Mismatch? |
|---|---|---|---|
| category | ❌ MISSING | ❌ Only `isset()` check | ⚠️ No real validation |
| purity | ❌ MISSING | ❌ Only `isset()` check | ⚠️ |
| lot_received_at | ❌ MISSING | ❌ No check | ⚠️ |
| gold_smith | ❌ MISSING | ❌ Only `isset()` check | ⚠️ |
| lot_product | ❌ MISSING | ❌ Only `!=''` check | ⚠️ |
| gross_wt | ❌ MISSING | ❌ Only `!=''` → NULL | 🔴 No numeric check |
| net_wt | ❌ MISSING | ❌ Only `!=''` → NULL | 🔴 No numeric check |
| less_wt | ❌ MISSING | ❌ Only `!=''` → NULL | 🔴 No less_wt > gross_wt check |
| no_of_piece | ❌ MISSING | ❌ Only `!=''` → 0 | 🔴 No negative check |
| making_charge | ❌ MISSING | ❌ Only `!=''` → 0 | ⚠️ |
| rate | ❌ MISSING | ❌ Only `!=''` → 0.00 | ⚠️ |
| lot_from restrict | N/A | ✅ `lot_from != 1` → reject edit | ✅ |
| is_closed restrict | N/A | ✅ `is_closed == 1` → reject edit | ✅ |
| lot_image | ❌ No type check | ❌ No MIME/ext check | 🔴 Arbitrary upload |

---

## 10. BUG ROOT CAUSE REGISTER

### BUG-UI-001: Edit Form Fields Blank ✅ FIXED v3.0
- **Root Cause**: JS read from panel hidden `#id_design` instead of row `.des_id`
- **Fix**: `curRow.find('.des_id').val()` at ret_lot.js L6764

### BUG-UI-003: Design Dropdown Empty ✅ FIXED v3.0
- **Root Cause**: Options appended only to `$('#select_design')`, not `curRow.find('.design')`
- **Fix**: Append to both panel and row selects at ret_lot.js L6771

### BUG-UI-004: Sub-Design Empty ✅ FIXED v3.0
- **Root Cause**: Same pattern as BUG-UI-003 for sub-design
- **Fix**: Append to both panel and row selects at ret_lot.js L6906

### BUG-UI-005: Design Not Pre-Selected on Edit ✅ FIXED v3.1
- **Root Cause**: `getActiveDesigns` reads design_id from `$('#id_design').val()` (panel) instead of `curRow.find('.des_id').val()` (row)
- **Fix**: Changed to row-scoped read at ret_lot.js L6764

### BUG-001: Negative Net Weight Allowed ❌ OPEN
- **Symptom**: No validation for `less_wt > gross_wt`
- **Location**: Controller L637-654, no JS validation
- **Risk**: 🔴 Negative inventory values in DB

### BUG-005: Debugger Statement Left in Code ✅ FIXED
- **Fix**: Removed at ret_lot.js L7335

### BUG-007: Commented-Out Calculation Code ✅ FIXED
- **Fix**: Removed dead code at ret_lot.js L23627

### BUG-008: lot_split Missing trans_begin() ❌ OPEN
- **Symptom**: Split save at L2355-2405 iterates and inserts without starting a transaction
- **Location**: Controller lot_split 'save' case
- **Risk**: 🔴 No atomicity — partial data on failure, trans_commit/rollback become no-ops

### BUG-009: customer_ack DOMPDF Disabled ❌ OPEN
- **Symptom**: Customer acknowledgement at L2546-2560 has DOMPDF lines commented out
- **Location**: Controller customer_acknowladgement
- **Risk**: 🟡 No PDF — raw HTML echoed to browser. Other 3 ack types work fine.

### BUG-010: Merge/Split Error Paths Leak Info ❌ OPEN
- **Symptom**: `echo last_query(); echo _error_message(); exit;` at L2270-2274 (merge) and L2443-2447 (split)
- **Risk**: 🔴 Same as AP-003 — info leak + transaction never rolled back

### BUG-011: lot_completed Uses Raw $_POST ❌ OPEN
- **Symptom**: `$_POST['completed_lot']` and `$_POST['branch']` at L2467-2469
- **Risk**: 🔴 No CI input sanitization on batch lot closing

### BUG-012: Hardcoded purchase_touch=92 ❌ OPEN
- **Symptom**: form.php L930 hardcodes `value="92"` for purchase touch
- **Risk**: 🟡 Wrong purity default for silver/platinum items

---

## 11. CODE ANTI-PATTERNS MAP

### AP-001: Panel vs Row Selector ✅ FIXED v3.0
- **Pattern**: `$('#panel_element').val()` inside per-row logic
- **Fix**: `curRow.find('.row_element').val()`

### AP-002: Double Redirect (Unreachable Code)
- **File**: admin_ret_lot.php L1025-1026
- **Issue**: Two `redirect()` calls; second is unreachable
- **Risk**: Dead code, confusing

### AP-003: Echo Error + Exit Before Rollback 🔴
- **Files**: admin_ret_lot.php L1035-1039, L1103, L1641-1643, **L2270-2274** (merge), **L2443-2447** (split)
- **Issue**: `echo $this->db->last_query(); echo _error_message(); exit;` runs BEFORE `trans_rollback()`
- **Risk**: Transaction never rolled back + query/error info leaked to browser
- **Total Occurrences**: 5 (save, cancel, update, merge save, split save)

### AP-004: mkdir 0777 🔴
- **File**: admin_ret_lot.php L402, L532, L1271
- **Issue**: World-writable directories
- **Fix**: Use `0755`

### AP-005: Hardcoded mc_type=2 in JS Row HTML 🟡
- **File**: ret_lot.js L5900 (approx)
- **Issue**: `value="2"` hardcoded in new row template
- **Risk**: Wrong default for some products

### AP-006: Duplicate Model Loading 🟡
- **File**: admin_ret_lot.php L35-37
- **Issue**: `ret_catalog_model` loaded twice
- **Risk**: Wasted memory, confusion

### AP-007: Raw $_POST Instead of CI Input 🔴
- **File**: admin_ret_lot.php L340, L1055, L1065, L1085, L181-247, **L1885** (getOrderNosBySearch), **L1897** (get_order_details — 3 params), **L1913** (get_karigar_list), **L1925-1929** (getProductBySearch — 3 params), **L1984** (lot_merge save), **L2296** (get_ActiveProduct), **L2359-2363** (lot_split save), **L2467-2469** (lot_completed — 2 params)
- **Issue**: `$_POST` used directly without `$this->input->post()`
- **Risk**: No XSS filtering, no CSRF check
- **Total Raw $_POST Sites**: 15+ (only `lot_inward` default case uses CI input)

### AP-008: Undefined $data in Model 🔴
- **File**: ret_lot_model.php L566
- **Issue**: `getorderdesigns()` references `$data['id_product']` but `$data` is never passed
- **Risk**: PHP warning, incorrect query

### AP-009: Missing trans_begin() in lot_split Save 🔴
- **File**: admin_ret_lot.php L2355-2405
- **Issue**: Split save iterates and inserts records but never calls `$this->db->trans_begin()` before the loop. At L2409 it checks `trans_status()` but transaction was never started.
- **Risk**: No atomicity — partial saves on failure, trans_commit/rollback are no-ops

### AP-010: DOMPDF Disabled in customer_ack 🔴
- **File**: admin_ret_lot.php L2546-2560
- **Issue**: DOMPDF lines are commented out. Customer acknowledgement echoes raw HTML and exits (`echo $html; exit;`)
- **Risk**: No PDF generation for customer ack, raw HTML sent to browser

### AP-011: Duplicate HTML IDs in Views 🟡
- **Files**: lot_merge.php L187+L341, lot_split.php L169+L259+L389
- **Issue**: Multiple `<input type="hidden" id="curRow" value="-1">` elements in same view
- **Risk**: Invalid HTML, `$('#curRow')` only selects first — JS may read wrong value

### AP-012: Commented-Out Debug Statements 🟢
- **Files**: admin_ret_lot.php L1789, L1823, L1986, L2164, L2222, L2357, L2493, L2544, L720
- **Issue**: `//print_r(...)` and `//echo "<pre>"; print_r(...)` left throughout code
- **Risk**: Code clutter; if uncommented accidentally, info leak + exit

### AP-013: Hardcoded purchase_touch Default 🟡
- **File**: form.php L930
- **Issue**: `<input ... id="purchase_touch" value="92">` — purchase touch hardcoded to 92
- **Risk**: Wrong default for non-gold purities (e.g., silver, platinum)

---

## 12. FUNCTION-LEVEL CODE MAP

### Controller Functions

| Function | File:Line | Reads | Writes | Known Gaps |
|---|---|---|---|---|
| `lot_inward` | controller:297-1683 | $_POST, model | DB: ret_lot_inwards + 7 child tables | 🔴 Raw POST, no validation |
| `lot_merge` | controller:1935-2288 | $_POST, model | DB: ret_lot_merge, ret_lot_inwards | Similar issues |
| `lot_split` | controller:2302-2459 | $_POST, model | DB: ret_lot_split_details | Similar issues |
| `lot_completed` | controller:2461-2525 | POST lot_no | DB: ret_lot_inwards.is_closed | Uses CI input ✅ |
| `remove_img` | controller:173-257 | $_POST[file/folder/id] | FS: unlink, DB: update image | 🔴 Path traversal risk |

### Model Functions

| Function | File:Line | Reads | Writes | Known Gaps |
|---|---|---|---|---|
| `insertData` | model:19-53 | $data, $table | SHOW COLUMNS + INSERT | 🟡 SHOW COLUMNS on every insert |
| `updateData` | model:55-91 | $data, $id_field, $id_value | SHOW COLUMNS + UPDATE | 🟡 same |
| `ajax_getLotList` | model:182-283 | Dates, filters | SELECT aggregate | 🔴 SQLi, N+1 (calls 3 sub-funcs per row) |
| `get_lotInward_detail` | model:493-558 | lot_no | SELECT with 10 JOINs | 🔴 N+1 (stones, metals, charges per row) |
| `getLotNoForMerge` | model:1112-1244 | lot_no | SELECT with 7 subqueries | Complex but functional |
| `getLotNoForSplit` | model:1282-1452 | lot_no | SELECT with 6 subqueries | Complex |
| `get_customer_lot_details` | model:1785-1845 | lot_no | SELECT | 🔴 N+1 (3 sub-queries per row) |

### Key JS Functions

| Function | File:Line | Purpose |
|---|---|---|
| `create_new_empty_lot_row` | js:5764-6440 | Build HTML row, init controls |
| `getActiveDesigns` | js:6686-6884 | AJAX: populate design dropdown |
| `get_ActivelotSubDesigns` | js:6886+ | AJAX: populate sub-design dropdown |
| `get_ActiveProduct` | js:6444-6578 | AJAX: populate product dropdown |
| `set_lotInward_list` | js:2903-3924 | Build lot list DataTable |
| `get_lotInward_list` | js:2571-2643 | AJAX: fetch lot list |
| `get_category` | js:4070-4182 | AJAX: populate category |
| `get_cat_purity` | js:4186-4294 | AJAX: populate purity for category |
| `getSearchProd` | js:4374-4506 | Autocomplete product search |
| `getSearchDesign` | js:4638-4738 | Autocomplete design search |
| `validateImage` | js:2339-2551 | Client-side image validation |

---

## 13. FIELD-LEVEL DATA FLOW TRACE

### Design ID Trace ✅ FIXED v3.1
```
DB: ret_lot_inwards_detail.lot_id_design
  → PHP: $data['lot_id_design'] in get_lotInward_detail (model L501)
  → JSON: response.inward_details[i].lot_id_design
  → JS: row.find('.des_id').val(lot_id_design) [hidden field]
  → AJAX: getActiveDesigns(row, pro_id) → fetches designs
  → JS: row.find('.design option[value='+design_id+']').attr('selected')
  ✅ All steps now read from row-scoped fields (was broken: read from #id_design panel)
```

### Gross Weight / Net Weight / Less Weight Trace
```
DB: ret_lot_inwards_detail.gross_wt, .net_wt, .less_wt
  → PHP: $itemData['gross_wt'] → $item_details array
  → DB: INSERT/UPDATE with no numeric validation
  → JS: Client-side calc at L5648: net_wt = gross_wt - less_wt (3 decimal toFixed) ✅
  ⚠️ BREAK POINT: less_wt > gross_wt → negative net_wt allowed (no client or server guard)
```

---

## 14. DEBUG RUNBOOKS

### A. Design Dropdown Empty
1. Network tab → check `admin_ret_catalog/design/active_design` AJAX response
2. Verify `pro_id` param is not empty
3. Check JS console for errors in `getActiveDesigns()`
4. Verify row selector: `curRow.find('.design')` returns element
5. Fix ref: §10 BUG-UI-003

### B. Sub-Design Dropdown Empty
1. Check `getActiveDesigns` completed first
2. Network tab → check `admin_ret_catalog/sub_design/activesubdesign` AJAX
3. Verify `des_id` param from `curRow.find('.des_id').val()`
4. Fix ref: §10 BUG-UI-004

### C. Edit Form Blank / Wrong Values
1. Check `lot_edit` AJAX response has correct data
2. Verify `create_new_empty_lot_row()` creates row before populate
3. Check design dropdown populated before selection attempt
4. Fix ref: §10 BUG-UI-005

### D. Lot Not in Merge Dropdown
1. Check `getLotidsforMerge()` query — excludes: tagged, merged, split, closed
2. Run: `SELECT lot_no FROM ret_lot_inwards WHERE is_lot_split=0 AND is_closed=0`
3. Check: `ret_taging` has tag_status != 2 for this lot_no

### E. Save Fails Silently
1. Check PHP error log for fatal errors
2. Check DB: `ret_lot_inwards` for partially saved data
3. Look for: `echo $this->db->last_query()` → L1035 (outputs error to browser)
4. Verify transaction: `trans_begin()` matches `trans_commit()/trans_rollback()`

---

## 15. UNIT TEST DERIVATION MAP

| Test ID | Rule/Bug | Scenario | Input | Expected |
|---|---|---|---|---|
| TC-LOT-001 | BUG-001 | less_wt > gross_wt | gross_wt=10, less_wt=15 | Reject with error |
| TC-LOT-002 | Save | Normal save | Valid lot data | lot_no > 0, all items saved |
| TC-LOT-003 | Edit | Edit manual lot | lot_from=1, is_closed=0 | Form loads with data |
| TC-LOT-004 | Edit | Edit non-manual lot | lot_from=2 | Redirect with error |
| TC-LOT-005 | Edit | Edit closed lot | is_closed=1 | Redirect with error |
| TC-LOT-006 | Cancel | Cancel active lot | lot_status=1 | lot_status → 2 |
| TC-LOT-007 | Delete | Delete untagged lot | No tags | Lot + images deleted |
| TC-LOT-008 | Non-tag | Save stock_type=2 | product+branch | ret_nontag_item updated |
| TC-LOT-009 | Merge | Merge 2 eligible lots | 2 untagged, unsplit lots | New merged lot created |
| TC-LOT-010 | Split | Split lot with balance | lot with pieces > 0 | Split detail created |
| TC-LOT-011 | Security | SQL injection in lot_no | lot_no="1; DROP TABLE" | Rejected/sanitized |
| TC-LOT-012 | Calculation | net_wt formula | gross_wt=100, less_wt=10 | net_wt=90 |

---

## 16. CHANGE IMPACT MAP

| Change | Affected Files | Downstream Impact | Risk |
|---|---|---|---|
| Change `ret_lot_inwards` schema | controller, model, all views | All lot operations | 🔴 HIGH |
| Modify `insertData`/`updateData` | ALL modules using ret_lot_model | Global insert/update logic | 🔴 HIGH |
| Add validation to save | controller L337-1047 | Save flow | 🟡 MEDIUM |
| Fix SQL injection | model (30+ functions) | Query behavior | 🔴 HIGH — must test all |
| Change design dropdown logic | ret_lot.js + ret_catalog_model | Add/Edit/Merge/Split forms | 🟡 MEDIUM |
| Modify stone detail structure | controller, model, JS, views | Save, edit, merge, split, ack PDFs | 🔴 HIGH |

---

## 16b. ACKNOWLEDGEMENT PDF COMPARISON

| Ack Type | Lines | Columns | Tax? | Stones? | Charges? | Signatures | CSS File |
|---|---|---|---|---|---|---|---|
| Vendor | 136 | S.No, Items, Pcs, Gwt, Purity | ❌ | ❌ | ❌ | 2 (Received By, Vendor Sign) | lot_ack.css |
| Office | 249 | + LWT, NWT + Branch Summary (DIA WT, Amount, Branch) | ❌ | ❌ | ❌ | 3 (Verified, Received, Approved) | lot_ack.css |
| Branch | 231 | Summary + (type=2) Tag Code, Design, SubDesign, Pcs, Gwt, Rate | ❌ | ❌ | ❌ | 3 (Verified, Received, Approved) | lot_ack.css |
| Customer | 384 | Purity, Product, Design, SubDesign, Pcs, Gwt, Nwt, Touch, Rate, VA%, Pure, MC, Amount | ✅ CGST/SGST/IGST | ✅ per item | ✅ per item | 4 (Audited, Party, Manager, Operator) | customer_job_receipt.css |

> **Office Ack special**: Calculates `Diff.Gwt` (tagged_gwt − lot_gwt) and `Diff.Pcs` (tagged_pcs − lot_pcs) at footer
>
> **Customer Ack special**: Inline `moneyFormatIndia()` function for Indian number formatting + auto `window.print()` after 1s timeout

---

## 16c. FORM MODAL CATALOG

| Modal ID | Title | Columns/Fields | Purpose |
|---|---|---|---|
| `#stoneModal` | Stone Details | 3 checkboxes (Precious/Semi-Precious/Normal) each with Pcs + Wt + UOM + Certificate upload | Old-style stone entry. Values stored in row hidden fields |
| `#cus_stoneModal` | Add Stone | LWT, Type, Name, Code, Pcs, Wt, Cal.Type, Cut, Color, Clarity, Shape, Rate, Amount, Action (14 cols) | New table-based stone entry with quality attributes |
| `#cus_chargeModal` | Add Charges | SNo, Charge Name, Type, Charge, Action (5 cols) | Extra charges per item |
| `#other_metalmodal` | Other Metals | Metal, Purity, Pcs, Gwt, V.A(%), Mc Type, Mc, Rate, Amount, Action (10 cols) | Other metal components per item |
| `#lot_inwards_detail` | Delete Product | Confirmation text + success/error alerts | Delete lot item confirmation |
| `#Userconfirm` | Category Alert | Warning: "This Category Will Change in Your Added Items.." | Confirms category change affecting existing rows |

> **🔴 Duplicate IDs**: `#charge_active_row` appears at both L2788 and L2906 in form.php
>
> **Inline JS**: On edit mode, form.php passes `lot_preview_item` as JSON from PHP `$inward_details` (L2625)

---

## 17. BUSINESS RULES ENGINE

| Rule ID | Condition | Action | Location | Type |
|---|---|---|---|---|
| RULE-001 | `lot_type = 1/2/3` | Normal / Customer / Repair order | Controller L354 | CONFIG |
| RULE-002 | `lot_from != 1` | Reject edit (manual lots only) | Controller L1126 | HARD |
| RULE-003 | `is_closed = 1` | Reject edit | Controller L1137 | HARD |
| RULE-004 | `stock_type = 2` | Insert to ret_nontag_item | Controller L851 | HARD |
| RULE-005 | `lot_recv_branch = 1` | HO-only receiving | Model L365 | CONFIG |
| RULE-006 | `is_supplierbill_entry_req = 0` | Show GRN select dropdown | form.php L337, Controller | CONFIG |
| RULE-007 | `lot_status = 2` | Cancelled—view only | Controller cancel_lot_entry | HARD |
| RULE-008 | Merge eligibility | Not tagged, not merged, not split, not closed | Model L1190-1194 | HARD |
| RULE-009 | Split eligibility | Not tagged, not merged, stock_type=1, not closed | Model L1382-1390 | HARD |
| RULE-010 | `is_purchase_cost_from_lot = 1` | Show Purchase MC, Wastage, Touch, Type, Pure, Rate fields | form.php L877-992, Controller L315 | CONFIG |
| RULE-011 | `lot_from = 7` | Lot created via Merge (cannot be edited) | Controller lot_merge save L2000 | HARD |
| RULE-012 | `calculation_based_on` | 5 modes: 0=Mc&Wast on Gross, 1=Mc&Wast on Net, 2=Mc Gross/Wast Net, 3=Fixed Rate, 4=Fixed Rate by Weight | form.php L529-541 | CONFIG |
| RULE-013 | `karigar_calc_type` | 3 types: 1=Weight×Rate, 2=Purchase Touch, 3=Weight×Wastage% | form.php L947-955 | CONFIG |
| RULE-014 | Delete lot item | `lot_inwards_detail` checks `check_is_tagged()` — blocks delete if tagged | Controller L1711-1764 | HARD |
| RULE-015 | Stone types | 3 categories per item: Precious (checkbox+pcs+wt+cert), Semi-Precious (same), Normal (same) | form.php stone modal, Controller save L470-580 | HARD |

---

## 18. CALCULATION ENGINE

### Net Weight
```
FORMULA: net_wt = gross_wt - less_wt
TRIGGER: Server-side on save/update (PHP just stores POST value)
FRONTEND: ❌ NOT CALCULATED IN JS
RISK: less_wt > gross_wt → negative net_wt (no validation!)
```

### Item Cost (calc_type = 1: Weight × Rate)
```
IF rate_calc_type = 1 (Per Gram): base_cost = net_wt × rate
IF rate_calc_type = 2 (Per Pcs):  base_cost = no_of_piece × rate
IF mc_type = 1 (Per Pcs):  total_mc = no_of_piece × making_charge
IF mc_type = 2 (Per Gram): total_mc = net_wt × making_charge
item_cost = base_cost + total_mc + total_tax
FRONTEND: ❌ NOT CALCULATED IN JS
```

### Tax
```
IF tax_type = 'IGST':       total_igst = taxable × (tax% / 100)
IF tax_type = 'CGST+SGST':  total_cgst = total_sgst = taxable × (tax% / 2 / 100)
FRONTEND: ❌ NOT CALCULATED IN JS
```

### Pure Weight (pur_wt)
```
FORMULA: pur_wt = net_wt × (purchase_touch / 100)
TRIGGER: karigar_calc_type = 2 (Purchase Touch)
DEFAULT: purchase_touch = 92 (hardcoded in form.php L930 — 🟡)
FRONTEND: ❌ NOT CALCULATED IN JS
```

### Stone Weight (Less Weight)
```
Per item, 3 stone categories:
  Precious:      checkbox → pcs + wt + UOM + certificate upload
  Semi-Precious: checkbox → pcs + wt + UOM + certificate upload
  Normal:        checkbox → pcs + wt + UOM + certificate upload
FORMULA: less_wt = SUM(precious_wt + semi_precious_wt + normal_wt)
TRIGGER: Modal save → hidden fields on row → included in form POST
Hidden fields per row: precious_stone, precious_st_pcs, precious_st_wt, semi_precious_stn, semi_precious_st_pcs, semi_precious_st_wt, normal_stn, normal_st_pcs, normal_st_wt
```

### Tax Fields (Hidden in UI)
```
Hidden fields per item:
  #item_cgst_cost, #item_sgst_cost, #item_igst_cost
  #item_tax_percentage, #tax_type, #tax_group_id
  #item_total_taxable, #item_total_tax
Tax type determined by: supplier_state vs cmp_state comparison
  Same state → CGST+SGST, Different → IGST
Form also carries: #cmp_country, #cmp_state, #supplier_state, #supplier_country
```

### Other Metals & Charges (Modals)
```
Other Metals (per item):
  Hidden: #other_metal_wt, #other_metal_wast_wt, #other_metal_mc_amount, #other_metal_amount
  Added via "+" button → modal → stored in #other_metal_details (JSON)
  DB: ret_lot_other_items
Other Charges (per item):
  Hidden: #other_charges_amount, #other_charges_details
  DB: ret_lot_other_charges
```

---

## 19. STATUS WORKFLOW

### Lot Status Lifecycle
| Status | Value | Allowed Actions |
|---|---|---|
| Active | `lot_status=1` | Edit (if lot_from=1, not closed), Cancel, Delete (if untagged), Merge, Split, Tag |
| Cancelled | `lot_status=2` | View only |
| Closed | `is_closed=1` | View only |

### Status Dependency Matrix
| Current State | Can Merge? | Can Split? | Can Tag? | Can Delete? | Can Edit? |
|---|---|---|---|---|---|
| Fresh Lot (Active) | ✓ | ✓ | ✓ | ✓ | ✓ |
| Merged Lot | ✗ | ✗ | ✓ | ✗ | ✗ |
| Split Lot | ✗ | ✓ (balance) | ✓ | ✗ | ✗ |
| Tagged Lot | ✗ | ✗ | ✗ | ✗ | ✗ |
| Cancelled Lot | ✗ | ✗ | ✗ | ✗ | ✗ |

---

## 20. CLIENT-VARIANT SETTINGS

| Setting Key | Table | Values | Effect |
|---|---|---|---|
| `lot_recv_branch` | ret_settings | 1=HO Only, 2=Any branch | Restricts which branch receives lots |
| `is_supplierbill_entry_req` | ret_settings | 0/1 | Requires supplier bill before lot creation |
| `is_purchase_cost_from_lot` | ret_settings | 0/1 | Derives purchase cost from lot data |
| `allow_lot_cancel` | profile | 0/1 | Allows cancellation of lots from previous dates |

---

## 21. GAPS, RISKS & TECHNICAL DEBT

### 🔴 CRITICAL — Security
1. **SQL Injection**: 30+ model functions use string concatenation instead of parameterized queries
2. **Raw $_POST**: 15+ controller sites use `$_POST` directly — save, update, cancel, merge, split, completed, getOrderNos, get_order_details, get_karigar_list, getProductBySearch, get_ActiveProduct
3. **File Upload**: No MIME type / extension whitelist in `upload_img`, `base64ToFile`
4. **Path Traversal**: `remove_img` uses `$_POST['file']` in file path without sanitization
5. **Info Leak**: 5 error paths echo `last_query()` + `_error_message()` — save (L1035), cancel (L1103), update (L1641), merge (L2270), split (L2443)
6. **Insecure Permissions**: `mkdir 0777` at L402, L532, L1271

### 🔴 CRITICAL — Data Integrity
7. **No Weight Validation**: `less_wt > gross_wt` creates negative `net_wt`
8. **No Negative Checks**: Weights and amounts accept any value (including negative)
9. **No Numeric Validation**: Weight/rate fields only check `!= ''`
10. **Rollback Never Reached**: Echo + exit at 5 locations prevents rollback on failure (see #5)
11. **Double Redirect**: L1025-1026 — second redirect is unreachable
12. **Missing trans_begin() in Split**: lot_split save (L2355) never calls `trans_begin()` — insertions not atomic

### 🟡 MODERATE — Performance
13. **N+1 Queries**: `ajax_getLotList` calls 3 sub-functions per lot row (L271-275)
14. **N+1 in Detail**: `get_lotInward_detail` calls 3 sub-queries per item (L546-548)
15. **N+1 in Customer**: `get_customer_lot_details` calls 3 sub-queries per item (L1832-1836)
16. **SHOW COLUMNS on Every Insert/Update**: `insertData`/`updateData` query schema every time (L21, L57)

### 🟡 MODERATE — Code Quality
17. **Duplicate Model Loading**: `ret_catalog_model` loaded twice in constructor
18. **Undefined Variable**: `getorderdesigns()` references `$data` that doesn't exist
19. **Hardcoded Values**: mc_type=2 in JS, purchase_touch=92 in form.php, timezone='Asia/Calcutta'
20. **JS Does Have Some Calcs**: net_wt (L5648), GST (L23336/L23432) — but no server-side validation mirrors them
21. **23K+ Lines JS**: Monolithic file, should be split by feature
22. **Duplicate HTML IDs**: `#curRow` appears 2-3 times in merge/split views
23. **DOMPDF Disabled**: customer_acknowladgement echoes raw HTML instead of PDF (L2552)
24. **Misleading Method Name**: `lot_inwards_detail()` actually DELETES items, not retrieves
25. **Duplicate `$(document).ready()`**: Second block at L23604 (should be merged into main block L105-2275)
26. **16+ Cross-Controller Calls**: ret_lot.js calls CATALOG(9), ESTIMATION(4), BRNTRANSFER(1), ORDER(1), PURCHASE(1), BILLING(1) — tight coupling across 6 controllers

### 🟢 LOW — Cosmetic
27. **Debug Artifacts**: 9+ commented `print_r`/`echo "<pre>"` statements throughout code
28. **Spelling**: "acknowladgement" (should be "acknowledgement"), "portriat" (should be "portrait") at L1799, L1837, L1873
29. **Commented-Out Preview Table**: form.php L1611-1764 has entire preview table block commented out
30. **Commented-Out Stone Modal Handler** (L8104-8340): Entire `update_stone_details` click handler commented out
31. **Commented-Out Net Weight Block** (L1279-1367): Net weight calc in ready block commented out (active version at L5648)

---

## 22. OPERATIONAL SUPPORT GUIDE

### First-Level Support Checklist
| Check Point | How to Verify | Common Fix |
|---|---|---|
| User Access | Check profile permissions | Grant access to `admin_ret_lot/lot_inward/list` |
| Branch Selection | Verify user's assigned branch + `lot_recv_branch` setting | Check ret_settings |
| Master Data | Confirm Category/Product/Purity exists | Create missing masters |
| Lot Status | `SELECT lot_status FROM ret_lot_inwards WHERE lot_no=X` | Cancelled = read only |
| Tag Status | `SELECT tag_status FROM ret_taging WHERE tag_lot_id=X` | Tagged = locked |
| Merge/Split | `SELECT is_lot_split FROM ret_lot_inwards WHERE lot_no=X` | Restrictions apply |
| Supplier Bill | Check `is_supplierbill_entry_req` setting | Set to 0 to bypass |

### Common Issues → Quick Fix
| Issue | Go To |
|---|---|
| Design dropdown empty | §10 BUG-UI-003 + §14 Runbook A |
| Sub-design dropdown empty | §10 BUG-UI-004 + §14 Runbook B |
| Edit form fields blank | §10 BUG-UI-001 + §14 Runbook C |
| Lot not in merge dropdown | §14 Runbook D |
| Lot save fails silently | §14 Runbook E |
| Negative net weight | §21 Gap #7 |
| Cannot edit lot | §17 RULE-002, RULE-003 |

---

**END OF LOT MODULE DIGITAL BRAIN v4.4**
*Version 4.4 | Built 2026-02-19 | Rounds 2+3+4+5 Updated 2026-02-19 | Protocol: build-module-brain.md*
*Sources: admin_ret_lot.php (2566L), ret_lot_model.php (1903L), ret_lot.js (23630L), form.php (2960L), list.php (447L), lot_merge.php (1067L), lot_split.php (649L), 5 print views (vendor 136L, office 249L, branch 231L, customer 384L, legacy 1), 2 CSS files (lot_ack 266L, customer_job_receipt)*
*Round 2: 5 bugs, 5 anti-patterns, 5 business rules, stone/tax/charge documentation*
*Round 3: Print view deep scan (4 ack types compared), 6 form modals cataloged, CSS analyzed*
*Round 4: JS deep scan (L1-4800). Save handler race condition (BUG-013), 20 global vars, 10 AJAX endpoints, DataTable 16 cols, action button conditions, cross-controller design search, edit mode 13-function init*
*Round 5: JS full scan (L4800-23630). 28 total AJAX endpoints (11 LOT, 17 cross-controller across 6 modules), row builder architecture (2 variants), validation matrix (4 sales modes), remove_row (3 modes), split balance validation (6 fields), GST tax engine (inclusive+exclusive), lot cancel/complete JS, duplicate $(document).ready(), corrected net_wt calc note*
