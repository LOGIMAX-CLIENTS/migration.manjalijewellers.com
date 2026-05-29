# Catalog_Inventory Module Brain

> **Built**: 2026-03-13 | **Round**: 6 (Refreshed 2026-03-24) | **Coverage**: See COVERAGE_TRACKER.md
> **Builder**: Antigravity AI

## 1. Module Overview

**Purpose**: Central master data management module for the entire retail ERP. Manages ALL catalog entities: categories, products, sub-products, designs, sub-designs, karigars (artisans), purity, color, cut, clarity, metals, stones, materials, tax groups, sections, financial years, diamond rates, wastage settings, and 30+ other master entities.

**This is the LARGEST module in the codebase** — 23,584 controller lines, 8,696 model lines, 164 controller methods, 358 model methods, 100+ DB tables.

> ⚠️ **Round 6 Discovery**: `catalog_master.js` (49,240 lines, 442 JS functions, 382 `$.ajax()` calls) is the PRIMARY JS file for this module — it was entirely undocumented in Rounds 1–5. See METHOD_INDEX.md §7c for full AJAX map.

### File Map

| File | Path | Lines | Purpose |
|---|---|---|---|
| Controller | `admin/application/controllers/admin_ret_catalog.php` | 23,584 | All master data CRUD operations |
| Model | `admin/application/models/ret_catalog_model.php` | 8,696 | All DB queries for master data |
| **JS (Primary)** | `admin/assets/js/catalog_master.js` | **49,240** | ⚠️ **PRIMARY JS** — all entity tables/forms (442 fns, 382 AJAX calls). Undocumented until Round 6. |
| JS (Legacy) | `admin/assets/js/catalog.js` | 450 | Legacy web catalog category/product list + image validation only |
| Views (catalog) | `admin/application/views/catalog/` | 4 files | Category/Product forms and list views |
| Views (master) | `admin/application/views/master/` | 85+ templates | All other master entity views |

### Connection Flow
```
Browser → JS (catalog_master.js) ← PRIMARY: handles ALL entity DataTables, forms, inline edits
       → AJAX → Controller (admin_ret_catalog.php)
       → Model (ret_catalog_model.php)
       → DB (100+ tables)
       → View (master/{entity}/form|list)
       → Browser

Browser → JS (catalog.js) ← LEGACY: only handles web catalog category/product list (2 AJAX calls)
```

## 2. Constructor

```php
function __construct() {
    $this->load->model('ret_catalog_model');       // Primary — all catalog methods
    $this->load->model('admin_settings_model');     // Access control (get_access)
    $this->load->model('sms_model');                // Vendor OTP/SMS
    $this->load->model('log_model');                // Audit logging
}
```

**Session Gate**: Redirects to `admin/login` if not logged in. Also checks `access_time_from/to` for time-restricted access — redirects to `chit_admin/logout` if outside allowed hours.

### Constants
| Constant | Value | Purpose |
|---|---|---|
| `CAT_MODEL` | `ret_catalog_model` | Primary model alias |
| `SETT_MOD` | `admin_settings_model` | Settings model alias |
| `CATE_VIEW` | `master/category/` | Legacy category view path |
| `SUBCATE_VIEW` | `master/sub_category/` | Legacy sub-category view path |
| `IMG_PATH` | `assets/img/` | Base image path |
| `PROD_PATH` | `assets/img/products/` | Product image path |
| `DESIGN_PATH` | `assets/img/designs/` | Design image path |
| `MAS_VIEW` | `master/` | Master view base path |

## 3. Architecture Pattern

**Every entity** in this controller follows the same switch-case pattern:

```php
public function entity_name($type = "", $id = "", $status = "") {
    switch($type) {
        case "add":    // POST — Insert new record
        case "save":   // POST — Alternative save action (some entities)
        case "edit":   // GET — Return record JSON for form population
        case "update": // POST — Update existing record
        case "delete": // GET/POST — Delete with stock-existence check
        case "list":   // GET — Load view template
        case "update_status": // Status toggle (active/inactive)
        case "active_xxx":    // Return active records JSON for dropdowns
        default:       // AJAX list data with access control
    }
}
```

**URL Pattern**: `/admin_ret_catalog/{entity}/{action}/{id}/{status}`

## 4. Entity Groups (164 controller methods)

### Group A: Core Catalog — Product Hierarchy
| Entity | Controller Method | Lines | Primary Table | Child Tables |
|---|---|---|---|---|
| Product (WebCat) | `product()` | L253-278 | `product` | `product_details`, `product_images` |
| Sub-Product (WebCat) | `sub_product()` | L226-249 | — | — |
| Ret Product (Master) | `ret_product()` | L6125-6705 | `ret_product_master` | `ret_product_section`, `ret_product_charges` |
| Ret Sub-Product | `ret_sub_product()` | L6800+ | `ret_sub_product_master` | — |
| Category (Ret) | `category()` | L3401-3813 | `ret_category` | `ret_metal_cat_purity` |
| Product Division | `product_division()` | L16568+ | `ret_product_division` | — |
| Product Grouping | `product_grouping()` | L22977+ | `ret_product_grouping` | — |

### Group B: Material & Quality Masters
| Entity | Controller Method | Lines | Primary Table |
|---|---|---|---|
| Purity | `purity()` | L282-494 | `ret_purity` |
| Color | `color()` | L746-945 | `ret_color` |
| Cut | `cut()` | L969-1170 | `ret_cut` |
| Clarity | `clarity()` | L1194-1400 | `ret_clarity` |
| Metal | `metal()` | L7448+ | `metal` |
| Metal Type | `metal_type()` | L11300+ | — |
| Stone | `stone()` | L3109+ | `ret_stone` |
| Material | `material()` | L2866+ | `ret_material` |
| Material Rate | `material_rate()` | L5372+ | `ret_material_rate` |
| UOM | `uom()` | L1622+ | `ret_uom` |

### Group C: Design & Sub-Design
| Entity | Controller Method | Lines | Primary Table |
|---|---|---|---|
| Design | `design()` | L5838+ | `ret_design_master` |
| Ret Design | `ret_design()` | L7761+ | `ret_design_master` |
| Sub-Design | `ret_sub_design()` | L13893+ | `ret_sub_design_master` |
| Design Attribute | `attribute()` | L15094+ | `ret_attribute`, `ret_attribute_values` |

### Group D: Karigar (Artisan) Management
| Entity | Controller Method | Lines | Primary Table |
|---|---|---|---|
| Karigar | `karigar()` | L3830+ | `ret_karigar` |
| Karigar General | `karigar_general()` | L19392+ | `ret_karigar` |
| Karigar Wastage | `karigar_wastage()` | L19734+ | `ret_karikar_items_wastage` |
| Karigar Stone | `karigar_stone()` | L20158+ | `ret_karigar_stones` |
| Karigar KYC | `karigar_kyc()` | L20310+ | `ret_karigar_kyc` |
| Karigar Approval | `karigar_approval()` | L17926+ | `ret_karigar` |
| Karigar Product Mapping | `ret_karigar_product()` | L15679+ | `ret_karigar_products` |

### Group E: Pricing & Rates
| Entity | Controller Method | Lines | Primary Table |
|---|---|---|---|
| Diamond | `diamond()` | L18483+ | — |
| Diamond Rate | `diamond_rate()` | L20779+ | `ret_diamond_rate` |
| Selling Diamond Rate | `selling_diamond_rate()` | L21366+ | `ret_selling_diamond_rate` |
| Metal Purity Rates | `ret_metalpurity()` | L16846+ | `ret_metal_purity_rate` |
| Stone Rate Settings | `ret_stone_rate_settings()` | L21966+ | `ret_stone_rate_settings` |
| Wastage MC Settings | `wastage_mc_settings()` | L18816+ | — |
| Wastage Discount | `wastage_discount()` | L23337+ | — |
| Stone Discount | `stone_discount()` | L23088+ | `ret_stone_discount_master` |

### Group F: Location & Infrastructure
| Entity | Controller Method | Lines | Primary Table |
|---|---|---|---|
| Branch Floor | `branch_floor()` | L1866+ | `ret_branch_floor` |
| Floor Counter | `floor_counter()` | L2114+ | `ret_branch_floor_counter` |
| Section | `ret_section()` | L12056+ | `ret_section` |
| Tag | `tag()` | L5597+ | `ret_tag_type_master` |
| Web Devices | `web_devices()` | L15856+ | `web_registered_devices` |

### Group G: Financial & Tax
| Entity | Controller Method | Lines | Primary Table |
|---|---|---|---|
| Financial Year | `financial_year()` | L9402+ | `ret_financial_year` |
| Tax | `tax()` | L5873+ | `ret_taxmaster` |
| Tax Group | `tgrp()` | L7113+ | `ret_taxgroupmaster`, `ret_taxgroupitems` |
| Charges | `charges()` | L13575+ | `ret_charges` |
| Account | `ret_account()` | L12740+ | `ret_account_head` |
| CR/DR Ledger | `ret_crdr_ledger()` | L12989+ | — |

### Group H: Misc Masters
| Entity | Controller Method | Lines |
|---|---|---|
| Making Type | `making_type()` | L2370+ |
| Theme | `theme()` | L2620+ |
| Screw | `screw()` | L8918+ |
| Hook | `hook()` | L9160+ |
| Weight | `weight()` | L9945+ |
| Reorder Settings | `reorder_settings()` | L10243+ |
| Delivery | `ret_delivery()` | L10681+ |
| Size | `ret_size()` | L11013+ |
| Old Metal Category | `old_metal_cat()` | L11527+ |
| Old Metal Rate | `old_metal_rate()` | L11814+ |
| Pay Device | `ret_pay_device()` | L12477+ |
| Feedback | `feedback()` | L13362+ |
| Collection | `ret_collection()` | L16066+ |
| Repair Master | `repair_master()` | L16323+ |
| Shape | `shape()` | L21152+ |
| Quality Code | `get_quality_code()` | L20762+ |
| Day Close | `day_close()` | L22453+ |
| Cover Up | `cover_up()` | L22566+ |
| Breakeven Logs | `ret_breakeven_logs()` | L22687+ |
| QC Cancel Reason | `ret_qc_cancel_reason()` | L22865+ |
| Bank Deposit | `bank_deposit()` | L17093+ |
| Stock Issue Type | `stock_issue_type()` | L13123+ |

## 5. Data Flow Summary

> Detailed flows documented in DATA_FLOW.md

### Category CRUD Flow
- **Create**: POST → `category(add)` → `insertData('ret_category')` → loop insert purities into `ret_metal_cat_purity` → optional image upload → commit
- **Update**: POST → `category(update)` → `updateData('ret_category')` → DELETE all existing `ret_metal_cat_purity` → re-insert → commit  
  ⚠️ **DELETE-then-INSERT pattern** for child purities
- **Delete**: GET → Checks `getItemsinTagDetails()` for stock existence → `deleteData('ret_category')` → commit
  ⚠️ Redirect after delete NOT JSON response

### Ret Product CRUD Flow
- **Create**: POST → `ret_product(save)` → `insertData('ret_product_master')` → loop insert sections into `ret_product_section` → image upload → loop insert charges into `ret_product_charges` → commit
- **Update**: POST → `ret_product(update)` → `updateData('ret_product_master')` → DELETE sections → re-insert → image if changed → DELETE charges → re-insert → commit
  ⚠️ **DELETE-then-INSERT** for both sections and charges
- **Delete**: Checks stock existence → `deleteData('ret_product_master')` + `deleteData('ret_product_charges')`
  ⚠️ Does NOT delete `ret_product_section` on product delete — potential orphan risk

## 6. Key Tables (Owned)

| Table | Purpose | Key Columns |
|---|---|---|
| `ret_product_master` | Retail product definitions | `pro_id`, `product_name`, `cat_id`, `product_short_code`, `metal_type`, `stone_type`, `wastage_type` |
| `ret_category` | Product categories | `id_ret_category`, `name`, `id_metal`, `cat_type`, `hsn_code` |
| `ret_metal_cat_purity` | Category-purity junction | `id_category`, `id_purity` |
| `ret_product_section` | Product-section junction | `pro_id`, `id_section` |
| `ret_product_charges` | Product charge mappings | `prod_id`, `charge_id`, `charge_value` |
| `ret_purity` | Purity master (22K, 24K, etc.) | `id_purity`, `purity`, `status` |
| `ret_color` | Diamond/gem color master | `id_color`, `color`, `status` |
| `ret_cut` | Diamond cut master | `id_cut`, `cut`, `status` |
| `ret_clarity` | Diamond clarity master | `id_clarity`, `clarity`, `status` |
| `ret_design_master` | Design master | Design definitions |
| `ret_sub_design_master` | Sub-design master | Sub-design variants |
| `ret_karigar` | Artisan/karigar master | `karigar_id`, name, contact, bank details |
| `ret_karigar_products` | Karigar-product mapping | Products each karigar works on |
| `ret_karikar_items_wastage` | Karigar wastage rates | Per-karigar, per-product wastage |
| `ret_karigar_stones` | Karigar stone rates | Per-karigar stone charges |
| `ret_karigar_charges` | Karigar other charges | Per-karigar other charges |
| `ret_karigar_kyc` | Karigar KYC documents | ID documents for artisans |
| `ret_stone` | Stone master | Stone definitions |
| `ret_material` | Material master | Material definitions |
| `ret_material_rate` | Material rates | Material pricing |
| `ret_section` | Store sections | Section definitions |
| `ret_branch_floor` | Branch floor master | Floor definitions |
| `ret_branch_floor_counter` | Floor counters | Counter definitions per floor |
| `ret_financial_year` | Financial years | Year definitions with status |
| `ret_taxmaster` | Tax master | Tax definitions |
| `ret_taxgroupmaster` | Tax group master | Tax group header |
| `ret_taxgroupitems` | Tax group items | Tax items in each group |
| `ret_charges` | Charges master | Charge type definitions |
| `ret_diamond_rate` | Diamond rates | Diamond pricing |
| `ret_selling_diamond_rate` | Selling diamond rates | Retail diamond pricing |
| `ret_uom` | Unit of measurement | UOM definitions |
| `ret_size` | Size master | Size definitions |
| `product` | Web catalog product | `id_product` (used by web catalog, separate from `ret_product_master`) |
| `product_images` | Web catalog images | Product image records |
| `product_details` | Web catalog details | Product detail records |

## 7. Form Sections

> Full hidden field catalog in [VIEW_MAP.md](VIEW_MAP.md)

### Key Hidden Fields by View
| View | Count | Critical Fields |
|---|---|---|
| `ret_product/form.php` | 18 | `editid_product`, `product_status`, `metal_id`, `category_id`, `tax_id`, `selected_sections[]` |
| `karigar/form.php` | 37 | `id_karigar`, `user_type`, `is_tcs`, `is_tds`, `user_status`, location IDs, KYC docs, wastage images |
| `karigar/approval_list.php` | 5 | `is_otp_verfied`, `otp_required`, `send_resend` |
| `ret_category/list.php` | 11 | `add_category_status`, `id_metal_category`, `tgrp_id`, `pur_id` |
| `stone/list.php` | 11 | `is_certificate_req`, `is_4c_req`, `stone_status` |

### Settings Keys
| Key | Source | Purpose |
|---|---|---|
| `get_access()` | `admin_settings_model` | Per-view edit/delete permission |
| `access_time_from/to` | Session | Constructor time-gate |
| `vendor_approval_otp_req` | `get_profile_settings()` | Karigar OTP approval |
| `branch_settings` | Session | Multi-branch view filtering |

### Invariant Dimensions (20)
> Full matrix in [INVARIANT_MATRIX.md](INVARIANT_MATRIX.md)

Key product configuration dimensions: `sales_mode` (3 values), `wastage_type` (3), `stock_type` (3), `calculation_based_on` (3), `stone_type` (2), plus 15 boolean toggles. These drive downstream Billing/Tagging/Estimation behavior.

## 8. Known Risks

### 🔴 Critical
1. **SQL Injection** — Model methods like `get_purity($id)`, `get_color($id)`, `get_cut($id)`, `get_clarity($id)` concatenate `$id` directly into SQL without parameterization (e.g., L192, L275, L347, L419 of model)
2. **Product delete partial cleanup** — `ret_product()` delete case deletes `ret_product_master` and `ret_product_charges` but NOT `ret_product_section` → orphan rows
3. **Undefined variable $carat** — `active_masters()` at L143 references `$carat` which is only from a commented-out call (L135)
4. **Generic insertData/updateData** — These methods (`L48-120`) query `SHOW COLUMNS FROM` on every call, creating performance overhead and potential SQL injection via table name

### 🟡 Medium
5. **DELETE-then-INSERT** pattern for category purities, product sections, and product charges — not atomic if the INSERT fails after DELETE
6. **GET-based deletes** — Some delete handlers use GET parameters (e.g., `purity/Delete/$id`) making them CSRF vulnerable
7. **Session-based access control only** — No RBAC at controller method level; any logged-in user can potentially access any master data endpoint
8. **Inconsistent response patterns** — Some CRUD operations return JSON, others use redirects, some echo raw 0/1

### 🟢 Low
9. **Image paths hardcoded** — Category images go to `assets/img/ret_category/`, product images handled via `set_image_ret()`
10. **Web catalog vs Retail catalog** — Two parallel product systems (`product` table for web, `ret_product_master` for retail) that are independently managed

## 9. Codebase Notes

- **Largest controller in the codebase** at 23,579 lines — candidate for refactoring into sub-controllers
- Uses `self::CAT_MODEL` constant indirection to reference the model — makes grep harder
- Many commented-out code blocks (e.g., carat entity L534-742)
- `$this->db->trans_begin()` / `trans_commit()` / `trans_rollback()` used consistently for all write operations
- Audit logging via `$this->log_model->log_detail()` on all CRUD operations
- `admin_settings_model->get_access()` called for permission-gated view loading 

## 10. Anti-Patterns Register

_(Empty — updated after each bug fix)_

---
> **Detailed lookups**: See [METHOD_INDEX.md](METHOD_INDEX.md) | [DATA_FLOW.md](DATA_FLOW.md) | [SCHEMA_ANALYSIS.md](SCHEMA_ANALYSIS.md) | [VIEW_MAP.md](VIEW_MAP.md) | [INVARIANT_MATRIX.md](INVARIANT_MATRIX.md)
