# Estimation Module Brain

> **Module**: Estimation
> **Date Built**: 2026-02-18 (Round 8 — Final)
> **Last Updated**: 2026-03-24 (Round 2 — 2 print templates added, VA slab corrected)
> **Built By**: Antigravity (AI)
> **Status**: Active — Round 2 Maintenance

---

## 1. Module Overview

The Estimation module handles **creating price quotations** for customers. It calculates the sale value of jewelry items (tagged, catalog/non-tag, custom/home-bill, old metal exchange) including tax, stone deductions, charges, gift vouchers, chit schemes, sales return credits, and packaging. Estimations can be printed, converted to billing, linked to customer orders, and routed through an **EDA (Estimate Discount Approval)** workflow.

### File Map

| File | Type | Lines | Purpose |
|---|---|---|---|
| `admin/application/controllers/admin_ret_estimation.php` | Controller | 3,574 | 64 public methods — main `estimation()` handles add/edit/save (2,284 lines alone) |
| `admin/application/models/ret_estimation_model.php` | Model | 3,030 | 112 methods — CRUD generics, queries, bill number generation (142 lines), customer/tag lookups |
| `admin/assets/js/ret_estimation.js` | JavaScript | 31,391 | 513 outline items — validation, calculations, AJAX, dynamic UI, tag split/merge |
| `admin/application/views/estimation/form.php` | View | 2,526 | Main estimation form (137KB) — shared for Add and Edit. Contains 5 item tables + 4 summary tables + 28 hidden config fields |
| `admin/application/views/estimation/list.php` | View | 128 | Estimation list view — DataTable with 14 columns (Est No, EMP Name/Code, Date, Customer, Mobile, Tot.Amount, Product, Bill No, Created through, Rating, Review, Remarks, Action) |
| `admin/application/views/estimation/eda/list.php` | View | 128 | **EDA (Estimate Discount Approval)** list — separate approve/reject workflow with 2 modals |
| `admin/application/views/estimation/print/` | Views | 9 files | Print templates — see Print Template Breakdown below |
| `admin/assets/css/estimation.css` | CSS | — | Primary styling |
| `admin/assets/css/estimation_2.css` | CSS | — | Additional styling |
| `admin/assets/css/estimation_thermal.css` | CSS | — | Thermal printer styling |

### Connection Flow

```
Browser → JS (ret_estimation.js)
       → AJAX → Controller (admin_ret_estimation.php)
       → Model (ret_estimation_model.php)
       → DB (85 tables: ret_estimation, ret_estimation_items, ret_estimation_item_stones, ...)
       → View (form.php / list.php) → Browser

Print: Controller → generate_invoice() / generate_brief_copy() → DomPDF → est_print_*.php → PDF
```

### Constructor (`__construct()`, L13-49)

**Loaded Models** (available as `$this->model_name` throughout controller):

| Model | Purpose |
|---|---|
| `ret_estimation_model` | Primary — all 112 estimation methods |
| `admin_settings_model` | Settings/config lookups (profile visibility, tolerances, feature flags) |
| `log_model` | Audit logging (write logs for create/edit/delete actions) |
| `ret_billing_model` | Billing cross-lookups (credit pending, bill details for sales return) |

**Session Gate** (L31-48):
- Checks `is_logged` session → redirects to `/admin/login` if not authenticated
- Checks `access_time_from` / `access_time_to` session → blocks access outside allowed time window
- On time violation: sets flash message "Exceeded allowed access time!!" → redirects to `/chit_admin/logout`

### Print Template Breakdown

| Template File | Lines | Page Size | Purpose | Key Difference from Default |
|---|---|---|---|---|
| `est_print.php` | 623 | 78mm×297mm | Default thermal receipt | Base template — items, old metal, chit, advance, totals |
| `est_print_2.php` | 1,261 | 75mm×600mm | Extended receipt with HUID | Adds HUID display, IGST vs CGST/SGST logic, `moneyFormatIndia()`, partly stone section |
| `est_print_anb.php` | — | — | ANB branch layout | Branch-specific branding/layout |
| `est_print_anika.php` | — | — | Anika branch layout | Branch-specific branding/layout |
| `est_print_arc.php` | — | — | ARC branch layout | Branch-specific branding/layout |
| `est_print_konika.php` | — | — | Konika branch layout | Branch-specific branding/layout *(added Round 2)* |
| `est_print_navratna.php` | — | — | Navratna branch layout | Branch-specific branding/layout |
| `est_print_new.php` | — | — | New format layout | Updated layout template |
| `est_print_sparqle.php` | — | — | Sparqle branch layout | Branch-specific branding/layout |
| `brief_copy.php` | — | — | Brief/summary copy | Condensed version for quick reference |
| `nsk_est_print.php` | — | — | NSK branch layout | Branch-specific branding/layout *(added Round 2)* |

**Print Rendering Logic** (from `est_print.php` / `est_print_2.php`):
- Recalculates VA/MC/stone amounts in PHP (not just displaying stored values)
- Handles 3 `calculation_based_on` modes: (0) Gross WT, (1) Net WT for both, (2) Net WT for VA + Gross WT for MC
- Chit details render differently by `scheme_type`: 0=amount-based, 1/2/3=weight-based (closing_weight × gold rate)
- Old metal section renders with per-row stone details when `old_stone_set == 1`
- **IGST vs CGST/SGST**: `est_print_2.php` checks company state vs customer state → if same state: CGST+SGST split; if different: single IGST line
- **HUID**: `est_print_2.php` displays `hu_id` and `hu_id2` fields from tag data when present
- **EDA Printing**: When `is_eda == 1`, tax lines (CGST/SGST) are hidden in `est_print.php`

### Controller Utility Methods (non-route, internal)

| Method | Lines | Purpose |
|---|---|---|
| `set_image()` | 56-73 | Save customer image to disk |
| `upload_img()` | 75-131 | Resize and compress uploaded image |
| `rrmdir()` | 133-152 | Recursive directory delete (for image cleanup) |
| `remove_img()` | 154-171 | Remove specific customer image |
| `base64ToFile()` | 3287-3313 | Convert base64 webcam capture to file |
| `isEmptySetDefault()` | 3412-3420 | Return default value if empty |
| `old_get_village()` | 3430-3452 | Legacy village lookup (deprecated) |
| `getNonTagproducts()` | 2772-2781 | Get non-tag product list |
| `get_old_metal_types()` | 3209-3218 | All old metal types (plural) |
| `get_old_metal_stone_details()` | 2857-2868 | Stone details within old metal |
| `ajax_get_village()` | 3078-3087 | Village AJAX endpoint |
| `getPartialTagSearch_old()` | 2630-2638 | Legacy partial tag search (deprecated variant) |

### Model Methods: 112 total across 10 groups

See [METHOD_INDEX.md](file:///c:/xampp/htdocs/retail_v5/knowledge_brain/Estimation/METHOD_INDEX.md) for full alphabetical listing with tables read/written, callers, and reverse mapping.

### Schema Analysis: 85 tables (13 owned + 72 referenced), 31 anomalies

See [SCHEMA_ANALYSIS.md](file:///c:/xampp/htdocs/retail_v5/knowledge_brain/Estimation/SCHEMA_ANALYSIS.md) for full column-level schema from `dev_structure.sql`.

| Severity | Count | Key Issues |
|---|---|---|
| **P0–P1 Critical** | 13 | 6 MyISAM tables (1 owned + 5 referenced), missing PK, `int` storing decimal rates/% (8 columns) |
| **P2 Moderate** | 8 | 6 tables with zero non-PK indexes, non-standard PK |
| **P3 Low** | 10 | `latin1` charset (7+ tables), no FK constraints, over-indexing, extreme table widths |

| Group | Count | Key Methods |
|---|---|---|
| CRUD Generics | 4 | `insertData`, `updateData`, `deleteData`, `insertBatchData` |
| Core Retrieval | 7 | `get_entry_records`, `getOtherEstimateItemsDetails` (252 lines), `ajax_getEstimationList`, `get_bill_no_format_detail` (142 lines) |
| Search | 11 | `getTaggingBySearch` (94 lines), `getTaggingScanBySearch`, `getOrderBySearch` |
| Customer/Validation | 9 | `createNewCustomer`, `updateCustomer`, `getCustomerDet`, 5× duplicate checks |
| Credit Collection | 8 | 6-method chain: `get_credit_pending_details` → `getCreditCollection` → `getOld_sales_detail` |
| Tag Detail Retrieval | 6 | `get_est_tag_details` (220 lines), `est_non_tag_items`, `est_home_bill` |
| Tag Helpers | 10 | `getEstTags`, `getTagStoneDetails`, `tag_reserve_check` |
| Stone/Weight | 3 | `get_est_stone_wt`, `get_tag_stone_wt`, `get_partly_home_bill_stones` |
| Lookup Helpers | 38 | Settings, design, purity, order, chit, village lookups |
| Misc | 16 | Financial year, metal types, encryption, old metal rates |

### Complexity Warning

- **JS file is 31K lines** — the largest single file in the module. Contains ALL client-side logic.
- **Controller `estimation()` method is 2,284 lines** (L190-L2474) — handles Add, Edit, Save, and multiple sub-table operations in one monolithic function.
- **Model `getOtherEstimateItemsDetails()` is 252 lines** (L430-L682) — single method with complex JOINs.
- **Model `get_est_tag_details()` is 220 lines** (L2045-L2265) — fetches tag items joining 10+ tables.

---

## 2. Entry Points (Routes)

| URL Path | Method | Controller Function | Lines | Purpose |
|---|---|---|---|---|
| `/admin_ret_estimation` | GET | `index()` | 51-54 | Redirect to list |
| `/admin_ret_estimation/estimation/add` | GET | `estimation('add')` | 190-2474 | Load Add estimation form |
| `/admin_ret_estimation/estimation/edit/{id}` | GET | `estimation('edit', id)` | 190-2474 | Load Edit estimation form |
| `/admin_ret_estimation/estimation/save` | POST | `estimation()` save branch | 190-2474 | Save new/update estimation |
| `/admin_ret_estimation/estimation/list` | GET | `estimation('list')` | 190-2474 | Load estimation list |
| `/admin_ret_estimation/generate_invoice/{id}` | GET | `generate_invoice()` | 2939-2996 | Generate PDF invoice |
| `/admin_ret_estimation/generate_brief_copy/{id}` | GET | `generate_brief_copy()` | 3000-3046 | Generate brief copy PDF |
| `/admin_ret_estimation/createNewCustomer` | POST/AJAX | `createNewCustomer()` | 2478-2555 | Create customer inline |
| `/admin_ret_estimation/updateCustomer` | POST/AJAX | `updateCustomer()` | 3120-3194 | Update customer inline |
| `/admin_ret_estimation/getCustomersBySearch` | POST/AJAX | `getCustomersBySearch()` | 2559-2571 | Customer search autocomplete |
| `/admin_ret_estimation/getTaggingBySearch` | POST/AJAX | `getTaggingBySearch()` | 2573-2581 | Tag ID search |
| `/admin_ret_estimation/getTaggingScanBySearch` | POST/AJAX | `getTaggingScanBySearch()` | 2585-2604 | Tag barcode scan search |
| `/admin_ret_estimation/getTaggingSearchByCollection` | POST/AJAX | `getTaggingSearchByCollection()` | 2608-2616 | Collection tag search |
| `/admin_ret_estimation/getPartialTagSearch` | POST/AJAX | `getPartialTagSearch()` | 2620-2628 | Partial tag/home bill search |
| `/admin_ret_estimation/getProductBySearch` | POST/AJAX | `getProductBySearch()` | 2716-2734 | Product search for catalog |
| `/admin_ret_estimation/getCustomProductBySearch` | POST/AJAX | `getCustomProductBySearch()` | 2758-2768 | Custom product search |
| `/admin_ret_estimation/getProductDesignBySearch` | POST/AJAX | `getProductDesignBySearch()` | 2785-2803 | Design search |
| `/admin_ret_estimation/getProductSubDesignBySearch` | POST/AJAX | `getProductSubDesignBySearch()` | 3258-3272 | Sub-design search |
| `/admin_ret_estimation/get_order_details` | POST/AJAX | `get_order_details()` | 2640-2649 | Order search |
| `/admin_ret_estimation/getOrderBySearch` | POST/AJAX | `getOrderBySearch()` | 2666-2714 | Order autocomplete search |
| `/admin_ret_estimation/getMetalTypes` | POST/AJAX | `getMetalTypes()` | 2807-2815 | Get metal types list |
| `/admin_ret_estimation/getNonTagLots` | POST/AJAX | `getNonTagLots()` | 2817-2825 | Non-tag lot search |
| `/admin_ret_estimation/get_purities` | POST/AJAX | `get_purities()` | 179-188 | Get purity list |
| `/admin_ret_estimation/get_purity_rate` | POST/AJAX | `get_purity_rate()` | 3276-3285 | Get rate for a purity |
| `/admin_ret_estimation/get_metal_purity_rate` | POST/AJAX | `get_metal_purity_rate()` | 3231-3240 | Get metal rate by purity |
| `/admin_ret_estimation/get_scheme_accounts` | POST/AJAX | `get_scheme_accounts()` | 2829-2840 | Chit scheme search |
| `/admin_ret_estimation/get_stone_details` | POST/AJAX | `get_stone_details()` | 2844-2855 | Stone details by item |
| `/admin_ret_estimation/get_other_material_details` | POST/AJAX | `get_other_material_details()` | 2870-2881 | Other material details |
| `/admin_ret_estimation/get_old_metal_rate` | POST/AJAX | `get_old_metal_rate()` | 2883-2894 | Old metal rate lookup |
| `/admin_ret_estimation/get_all_old_metal_rates` | POST/AJAX | `get_all_old_metal_rates()` | 2896-2905 | All old metal rates |
| `/admin_ret_estimation/get_old_metal_type` | POST/AJAX | `get_old_metal_type()` | 3198-3207 | Old metal types |
| `/admin_ret_estimation/get_old_metal_category` | POST/AJAX | `get_old_metal_category()` | 3220-3229 | Old metal category |
| `/admin_ret_estimation/get_employee` | POST/AJAX | `get_employee()` | 3050-3061 | Employee for branch |
| `/admin_ret_estimation/get_customer` | POST/AJAX | `get_customer()` | 3091-3116 | Customer details by ID |
| `/admin_ret_estimation/getCustomerDet` | POST/AJAX | `getCustomerDet()` | 3246-3254 | Customer purchase history |
| `/admin_ret_estimation/cancel_order_tag` | POST/AJAX | `cancel_order_tag()` | 3321-3368 | Cancel order tag assignment |
| `/admin_ret_estimation/get_non_tag_stock` | POST/AJAX | `get_non_tag_stock()` | 3372-3381 | Non-tag stock details |
| `/admin_ret_estimation/get_tag_status_details` | POST/AJAX | `get_tag_status_details()` | 3385-3402 | Tag status lookup |
| `/admin_ret_estimation/get_tag_img_by_id` | POST/AJAX | `get_tag_img_by_id()` | 3404-3410 | Tag image |
| `/admin_ret_estimation/get_partial_details` | POST/AJAX | `get_partial_details()` | 3065-3074 | Partial tag details |
| `/admin_ret_estimation/get_ActiveProduct` | POST/AJAX | `get_ActiveProduct()` | 2653-2662 | Active products list |
| `/admin_ret_estimation/get_sectionBranchwise` | POST/AJAX | `get_sectionBranchwise()` | 3494-3502 | Section list by branch |
| `/admin_ret_estimation/pan_available` | POST/AJAX | `pan_available()` | 3504-3513 | PAN duplicate check |
| `/admin_ret_estimation/gst_available` | POST/AJAX | `gst_available()` | 3515-3523 | GST duplicate check |
| `/admin_ret_estimation/aadhar_available` | POST/AJAX | `aadhar_available()` | 3525-3534 | Aadhaar duplicate check |
| `/admin_ret_estimation/passport_available` | POST/AJAX | `passport_available()` | 3535-3542 | Passport duplicate check |
| `/admin_ret_estimation/dl_available` | POST/AJAX | `dl_available()` | 3543-3550 | DL duplicate check |
| `/admin_ret_estimation/get_old_metal_Product` | POST/AJAX | `get_old_metal_Product()` | 3551-3556 | Old metal product list |
| `/admin_ret_estimation/get_va_range` | POST/AJAX | `get_va_range()` | 3557-3563 | VA range for metal |
| `/admin_ret_estimation/getFinancialYr` | POST/AJAX | `getFinancialYr()` | 3565-3572 | Financial year list |
| `/admin_ret_estimation/get_village_by_pincode` | POST/AJAX | `get_village_by_pincode()` | 3423-3428 | Village lookup by pincode |
| `/admin_ret_estimation/get_village` | POST/AJAX | `get_village()` | 3454-3492 | Village/area management |

**Total**: 64 public methods. **50+ AJAX endpoints**.

---

## 3. Data Flow Summary

Detailed flows in [DATA_FLOW.md](file:///c:/xampp/htdocs/retail_v5/knowledge_brain/Estimation/DATA_FLOW.md).

**Three primary flows:**

1. **CREATE (Save New Estimation)**
   - JS collects form data → validates sections (tag, catalog, custom, old metal, stones, materials, chit) → AJAX POST to `estimation()` → Controller reads POST data → builds arrays for each item type → calls `trans_begin()` → inserts header (ret_estimation) → inserts items (ret_estimation_items) → inserts sub-tables (stones, materials, charges, old metals, chit adjustments) → `trans_commit()` → response to JS

2. **EDIT (Update Existing)**
   - Controller loads existing data via model → populates form → user edits → JS validates → AJAX POST → Controller **deletes existing items** then re-inserts (DELETE-INSERT pattern, NOT UPDATE) → same flow as CREATE for items

3. **DELETE**
   - Not implemented as a standalone action in this module. Estimations are used as references for billing, not directly deletable.

---

## 4. Key Tables

> Column names below match the actual schema in [SCHEMA_ANALYSIS.md](file:///c:/xampp/htdocs/retail_v5/knowledge_brain/Estimation/SCHEMA_ANALYSIS.md).

**Estimation-Owned (13 tables)**:

| Table | Purpose | Key Columns (actual) |
|---|---|---|
| `ret_estimation` | Header — one row per estimation | `estimation_id` (PK), `esti_no`, `cus_id`, `id_branch`, `estimation_datetime`, `total_cost`, `esti_for`, `is_eda`, `manual_rate`, `disc_per`, `goldrate_22ct` |
| `ret_estimation_items` | Line items — tagged/catalog/custom | `est_item_id` (PK), `esti_id` (FK), `tag_id`, `product_id`, `gross_wt`, `net_wt`, `est_rate_per_grm`, `item_cost`, `mc_value`, `wastage_percent`, `item_type` (0=Tag, 1=Catalog, 2=Custom), `discount` |
| `ret_estimation_item_stones` | Stone details per item | `est_item_stone_id` (PK), `est_id` (FK), `est_item_id` (FK), `stone_id`, `wt`, `price` |
| `ret_estimation_item_other_materials` | Other materials per item | `est_other_material_id` (PK), `est_id`, `est_item_id`, `material_id`, `wt`, `price` |
| `ret_estimation_other_charges` | Extra charges per item | `id_est_charge` (PK), `est_item_id` (FK), `id_charge`, `amount` |
| `ret_estimation_old_metal_sale_details` | Old metal exchange (24 cols) | `old_metal_sale_id` (PK), `est_id` (FK), `id_old_metal_type`, `id_old_metal_category`, `id_category`, `gross_wt`, `net_wt`, `touch`, `purity`, `rate_per_gram`, `amount`, `purpose` (1=Cash, 2=Exchange) |
| `ret_esti_old_metal_stone_details` | Stones in old metal | `est_old_metal_stone_id` (PK), `est_id`, `est_old_metal_sale_id`, `stone_id`, `wt`, `price` |
| `ret_est_chit_utilization` | Chit scheme adjustments | `chit_ut_id` (UNIQUE), `est_id`, `scheme_account_id`, `utl_amount`, `closing_weight` |
| `ret_est_gift_voucher_details` | Gift voucher redemptions | `gift_voucher_id`, `est_id`, `voucher_no`, `gift_voucher_amt` |
| `ret_est_other_metals` | Other metals on items (MyISAM ⚠️) | `est_other_itm_id` (PK), `est_item_id`, `tag_other_itm_metal_id`, `tag_other_itm_grs_weight`, `tag_other_itm_amount` |
| `ret_est_sales_return_utilization` | Sales return credits | `sr_ut_id` (PK), `est_id`, `bill_id`, `bill_det_id` |
| `ret_est_tag_merge` | Tag merge tracking | `id_est_tag_merge` (PK), `est_item_id`, `ref_est_item_id` |
| `ret_estimation_other_inventory_issue` | Packaging box items | `id_inv_issue` (PK), `esti_id`, `id_other_item`, `no_of_piece` |

**Key Referenced Tables (top 6)**:

| Table | Purpose | Key Columns |
|---|---|---|
| `ret_taging` | Master tag inventory (118 cols) | `tag_id` (PK), `tag_code`, `product_id`, `gwt`, `nwt`, `id_branch` |
| `ret_taging_stone` | Stones attached to tags | `tag_stone_id` (PK), `tag_id`, `stone_id`, `wt`, `price` |
| `ret_tag_other_metals` | Other metals on tags (MyISAM ⚠️) | `tag_other_itm_id` (PK), `tag_id`, `metal_id`, `grs_weight` |
| `customer` | Customer master (117 cols) | `id_customer` (PK), `firstname`, `mobile`, `gst_number` |
| `metal_rates` | Daily metal rates | `id_metal_rates` (PK), `metal_type_id`, `goldrate_22ct`, `silverrate_1gm` |
| `ret_purity` | Purity master | `purity_id` (PK), `purity_name`, `purity_value`, `id_metal` |

### Form Hidden Configuration Fields (28 fields from form.php L127-L154)

These fields are injected from server-side settings and drive JS calculation logic:

| Hidden Field ID | Source | Purpose |
|---|---|---|
| `min_old_gold_rate`, `max_old_gold_rate` | Settings | Old gold rate boundary limits |
| `min_old_silver_rate`, `max_old_silver_rate` | Settings | Old silver rate boundary limits |
| `allow_manual_rate` | `emp_setting` | Toggle manual metal rate entry |
| `min_gold_tol`, `max_gold_tol` | `emp_setting` | Gold rate tolerance range |
| `min_silver_tol`, `max_silver_tol` | `emp_setting` | Silver rate tolerance range |
| `gold_metal_id`, `silver_metal_id` | DB | Dynamic metal IDs for tolerance matching |
| `weightschemecaltype` | Settings | Chit weight scheme calculation type |
| `weight_scheme_closure_type` | Settings | Weight scheme closure type |
| `est_emp_select_req` | Settings | Is sales employee mandatory? |
| `est_old_metal_remarks_req` | Settings | Is old metal remarks mandatory? |
| `allow_mc_edit`, `allow_va_edit` | `profile_setting` | MC/VA edit permissions |
| `tag_split_wt` | Settings | Tag split weight type |
| `max_cash_allowed` | Settings | Max cash return for old gold |
| `min_stn_disc_limit`, `max_stn_disc_limit` | `stn_disc_per` | Stone discount % limits |
| `blk_wast_disc_lmt` | `emp_setting` | Bulk wastage discount limit |
| `dia_disc_lmt` | `emp_setting` | Diamond discount limit |
| `allow_wed_wast` | `profile_setting` | Wedding wastage slab enabled? |
| `wastage_rate_type` | Settings | Wastage rate type |
| `chit_rate_cal_type` | Settings | Chit rate calculation type |
| `disc_limit_type`, `disc_limit` | Employee-based | Employee-specific discount limits |
| `allowed_old_met_pur` | Employee-based | Old metal purity restrictions (1=All, 2=Gold only, 3=Silver only) |

### Form Sections (5 item types + 4 summary/deduction sections)

| Section | Toggle Control | Table ID (DOM) | Item Type |
|---|---|---|---|
| **Tag Details** | `profile.est_tag == 1` + checkbox | `estimation_tag_details` | `item_type=0` |
| **Non-Tag (Catalog) Details** | `profile.est_non_tag == 1` + checkbox | `estimation_catalog_details` | `item_type=1` |
| **Home Bill (Custom) Details** | `profile.est_home_bill == 1` + checkbox | `estimation_custom_details` | `item_type=2` |
| **Old Metal Details** | `profile.est_old_metal == 1` + checkbox | `estimation_old_matel_details` | N/A |
| **Gift Voucher Details** | Always visible if data exists | `estimation_gift_voucher_details` | N/A |
| **Wedding Purchase Discount (VA Slab)** | `profile_setting.wedding_wastage_slab == 1` | `estimation_va_slab_details` | N/A |
| **Chit Details** | Always available | `estimation_chit_details` | N/A |
| **Sales Return Details** | `enable_sales_return_estimations.value == 1` | `estimation_sr_details` | N/A |
| **Packaging Box** | Always available | `estimation_other_inv_details` | N/A |

### EDA (Estimate Discount Approval) Workflow

- **Trigger**: Checkbox `IS EDA` on form → sets `estimation.is_eda = 1`
- **View**: `estimation/eda/list.php` — separate DataTable with columns: Est No, Date, Customer, Mobile, Product, Total Amount, Final Amount, Discount, Action
- **Actions**: Approve (modal `#confirm-approve`) / Reject (modal `#confirm-reject`)
- **Flow**: Employee creates estimation with EDA flag → appears in EDA queue → Manager approves/rejects discount

---

## 5. Business Rules Summary

24 Business Rules + 11 Validations extracted. See [BUSINESS_RULES.md](file:///c:/xampp/htdocs/retail_v5/knowledge_brain/Estimation/BUSINESS_RULES.md) for full details.

**Top Rules:**
- Net Weight = Gross Weight − Less Weight (stone wt + other material wt)
- Sale Value = Net Weight × Rate Per Gram (for weight-based products)
- MC/VA can be calculated as flat, percentage of net weight value, or per gram
- Wastage Weight = Net Weight × Wastage% / 100
- Old Metal Deduction = (GWT × Touch%) × Rate
- Total = Σ(Tag Items) + Σ(Catalog Items) + Σ(Custom Items) − Σ(Old Metal) + Tax

---

## 6. Cross-Module Dependencies

See [CROSS_MODULE_MAP.md](file:///c:/xampp/htdocs/retail_v5/knowledge_brain/Estimation/CROSS_MODULE_MAP.md) for full mapping.

**Key Dependencies:**
- **Tagging (Inventory)**: Reads `ret_taging`, `ret_taging_stone`, `ret_other_metal_details` — tag details populate estimation items
- **Customer**: Reads/Writes `customer` — inline customer creation and updates
- **Orders**: Reads order details — links estimations to customer orders
- **Billing**: Billing reads estimations — estimation is a pre-billing step. Credit collection chain reads `ret_billing`, `ret_bill_old_metal_sale_details`, `ret_issue_receipt`, `ret_issue_credit_collection_details`
- **Accounts/Chit**: Reads `scheme_account` — chit balance adjustments
- **Print (DomPDF)**: Uses DomPDF library for PDF generation. 9 branch-specific templates. Company details loaded via `getCompanyDetails()` for IGST/CGST state comparison
- **Day Closing**: Reads `ret_day_closing` via `getBranchDayClosingData()` — gates whether estimations can be saved for a branch on a given date

---

## 7. Known Risks

1. **Monolithic Save** — The `estimation()` method (2,284 lines) handles add/edit/save/list — any change risks breaking other modes
2. **DELETE-then-INSERT on Edit** — Editing deletes all items and re-inserts instead of updating. Risk: if re-insert fails, items are lost
3. **Client-Side Calculations** — All financial calculations run in JS (31K lines). Server has minimal re-validation
4. **Copy-Paste Functions** — `get_tag_data()` and `get_tag_barcode_data()` are near-identical functions with subtle variable differences
5. **No Transaction Rollback** — Known bug (EST-R601): `trans_commit()` called in error branches
6. **SQL Injection in Model** — Known bug (EST-R301): User-controlled column names passed directly to queries
7. **Large JS File** — Any edit to `ret_estimation.js` (31K lines) requires extreme precision to avoid affecting other functions
8. **Hidden Config Cascading** — 28 hidden fields drive calculation behavior. Changing a setting like `wastage_rate_type` can silently alter all wastage calculations without changing any code
9. **Feature Flag Fragility** — Sales Return section controlled by `enable_sales_return_estimations` setting; Wedding VA by `wedding_wastage_slab`; EDA by checkbox. Missing/null settings can hide or break entire sections
10. **`wastage_slab_value()` is 496 lines** (L29994-L30489) — handles all VA slab application logic. One of the largest single JS functions
11. **Employee Permission Coupling** — Old metal purity (`allowed_old_met_pur`), discount limits (`disc_limit_type`), MC/VA edit permissions all employee-dependent — switching employees mid-estimation can cause invalid states
12. **Print Template Duplication** — 9 print templates with largely duplicated logic but subtle per-branch differences. A bug fix in one template must be manually replicated to all others
13. **Print Recalculation** — Print templates recalculate VA/MC amounts in PHP instead of reading stored values. If PHP and JS calculation logic diverge, printed totals will differ from form totals
14. **Day Closing Gate Missing Validation** — `getBranchDayClosingData()` reads `ret_day_closing` but the gate check implementation in the controller is unclear — may allow saves on closed days
15. **Credit Collection Chain Complexity** — 6 model methods chain together (`get_credit_pending_details` → `getCreditCollection` → `get_credit_collection_details` → `getOld_sales_detail`) with no error handling between links
16. **VA Slab columns in ret_estimation_items** *(✅ Corrected Round 2: NOT a separate table)* — `wastage_slab_id`, `wast_slab_value`, `max_va_per`, `act_wast_per`, `tag_blk_disc` are columns on `ret_estimation_items`. Any schema migration affecting this table must preserve these columns.
17. **Two undocumented print templates** *(found Round 2)* — `est_print_konika.php` and `nsk_est_print.php` were not in the original brain. Now 11 print templates total. Bug fixes to print templates must cover all 11.

---

## 8. DB Verification Queries

```sql
-- 1. Pull complete estimation by ID (header + items + stones + old metal)
SELECT e.*, ei.*, es.*, eom.*
FROM ret_estimation e
LEFT JOIN ret_estimation_items ei ON e.estimation_id = ei.esti_id
LEFT JOIN ret_estimation_item_stones es ON ei.est_item_id = es.est_item_id
LEFT JOIN ret_estimation_old_metal_sale_details eom ON e.estimation_id = eom.est_id
WHERE e.estimation_id = {ID};

-- 2. Check total ≠ sum of items (integrity mismatch detector)
SELECT e.estimation_id, e.esti_no, e.total_cost,
       SUM(ei.item_cost) AS calculated_total,
       ABS(e.total_cost - SUM(ei.item_cost)) AS diff
FROM ret_estimation e
JOIN ret_estimation_items ei ON e.estimation_id = ei.esti_id
GROUP BY e.estimation_id
HAVING ABS(e.total_cost - calculated_total) > 0.01;

-- 3. Orphan items (items without parent estimation)
SELECT ei.*
FROM ret_estimation_items ei
LEFT JOIN ret_estimation e ON ei.esti_id = e.estimation_id
WHERE e.estimation_id IS NULL;

-- 4. Orphan stones (stones without parent item)
SELECT es.*
FROM ret_estimation_item_stones es
LEFT JOIN ret_estimation_items ei ON es.est_item_id = ei.est_item_id
WHERE ei.est_item_id IS NULL;

-- 5. Orphan old metal stone details (stones without parent old metal)
SELECT eosd.*
FROM ret_esti_old_metal_stone_details eosd
LEFT JOIN ret_estimation_old_metal_sale_details eom ON eosd.est_old_metal_sale_id = eom.old_metal_sale_id
WHERE eom.old_metal_sale_id IS NULL;

-- 6. MyISAM table transaction safety check
-- Run this BEFORE any fix — if ret_est_other_metals has orphans, MyISAM transactions are silently failing
SELECT eom.*
FROM ret_est_other_metals eom
LEFT JOIN ret_estimation_items ei ON eom.est_item_id = ei.est_item_id
WHERE ei.est_item_id IS NULL;

-- 7. Chit utilization without parent estimation
SELECT cu.*
FROM ret_est_chit_utilization cu
LEFT JOIN ret_estimation e ON cu.est_id = e.estimation_id
WHERE e.estimation_id IS NULL;
```

---

## 9. Codebase Notes

- **Framework**: CodeIgniter 3.x
- **PHP Version**: 8.5.0
- **JS Libraries**: jQuery, jQuery UI (autocomplete), Select2, DataTables, Webcam.js (customer photo capture), DomPDF (PDF generation)
- **Image Handling**: Controller has 5 image utility methods (`set_image`, `upload_img`, `rrmdir`, `remove_img`, `base64ToFile`) for customer photo management including webcam capture
- **HUID Tracking**: `est_print_2.php` renders `hu_id` and `hu_id2` from tag data (Hallmark Unique ID — BIS compliance)
- **Naming Convention**: Inconsistent — some methods use camelCase (`createNewCustomer`), others use snake_case (`get_tag_data`), and some are mixed (`getPartialTagSearch_old`)
- **Comments**: Extensive commented-out code blocks throughout model and controller — indicates evolution without cleanup
- **Double-line spacing**: JS file uses double newlines between every line — inflating the line count (effective logic is ~15K lines)

---

## 10. Anti-Patterns Register

| Bug ID | Pattern | What Was Broken | How Fixed | Date |
|---|---|---|---|---|
| EST-RC01 | AJAX Race Condition — edit screen dropdown depends on async data not yet loaded | `getNonTagDesignDetails()` read `id_sub_design` from `<select class="cat_sub_design">` which was empty because `cat_sub_design_details` AJAX hadn't completed; Sub Design dropdown also rendered with zero options | (1) Changed selector to `.cat_id_sub_design` (hidden input set from server data); (2) Added fallback option from `item.id_sub_design`/`item.sub_design_name` when dropdown data hasn't loaded | 2026-04-10 |
| *(populated after each bug fix)* | | | | |

