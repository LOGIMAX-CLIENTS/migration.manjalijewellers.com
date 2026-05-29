# Tagging Module Brain

> **Module**: Tagging (Tag Management for Jewelry Retail)
> **Last Built**: 2026-02-24
> **Brain Version**: 1.6 (R7 — 100% coverage)

---

## 1. Module Overview

The Tagging module handles **jewelry item tag creation, editing, scanning, marking, re-tagging, printing, bulk editing, order linking/unlinking, and collection mapping**. It is a core inventory module that assigns unique tag codes to individual products from inward lots, tracks their lifecycle (creation → display → sale), and maintains traceability through QR codes, HUID (Hallmark Unique ID), and OTP verification.

### Key Business Functions

- **Tag Creation**: Assigns unique tag codes to items from lot inwards, with stones, materials, and attribute details
- **Tag Editing**: Modify existing tag properties (weight, purity, making charge, etc.)
- **Bulk Tag Edit**: Batch modification of tags with log tracking
- **Tag Scanning**: QR/barcode scan to retrieve tag details
- **Tag Marking**: Mark tags for specific operations (sales hold, reservation, etc.)
- **Re-tagging**: Create new tags from existing tags (e.g., after repair/modification)
- **Order Linking/Unlinking**: Associate/disassociate tags with customer orders (with OTP verification)
- **Collection Mapping**: Map tags to named collections
- **Tag Printing**: Print tag labels (supports duplicate print tracking)
- **Purchase Cost Update**: Update purchase cost on tags from PO data

### Industry Context

This is a **jewelry-specific tagging system** — each physical item (ring, necklace, etc.) receives a unique tag with detailed attributes: metal type, purity (karat), gross/net weight, stone details (diamond/gemstone count, weight, value), making charges, wastage percentages, and HUID compliance per Indian hallmarking regulations.

---

## 2. File Map

| Layer      | File                                                  | Lines | Size   |
| ---------- | ----------------------------------------------------- | ----- | ------ |
| Controller | `admin/application/controllers/admin_ret_tagging.php` | 9,791 | 225 KB |

> ⚠️ **R4 Verified**: 108 `function` declarations found, but 9 are inside `/* */` comment block (L8817-L9136, printer label builders). **107 active callable methods** (excl. `__construct`).
> | Model | `admin/application/models/ret_tag_model.php` | 10,242 | 187 KB |
> | JavaScript | `admin/assets/js/ret_tagging.js` | 55,766 | 732 KB |
> | **Total** | | **75,799** | **1.14 MB** |

### Views (16 files, 2 directories)

| View File                     | Size  | Purpose                              |
| ----------------------------- | ----- | ------------------------------------ |
| `tagging/form.php`            | 70 KB | Main tag creation/edit form          |
| `tagging/list.php`            | 17 KB | Tag list with DataTable              |
| `tagging/bulk_edit.php`       | 49 KB | Bulk tag edit form                   |
| `tagging/bulk_edit_log.php`   | 5 KB  | Bulk edit history log                |
| `tagging/retagging_form.php`  | 20 KB | Re-tagging form                      |
| `tagging/retagging_list.php`  | 3 KB  | Re-tagging list                      |
| `tagging/tag_list.php`        | 5 KB  | Tag listing page                     |
| `tagging/bt_tag_list.php`     | 4 KB  | Branch transfer tag list             |
| `tagging/tag_edit.php`        | 8 KB  | Tag edit page                        |
| `tagging/tag_link.php`        | 6 KB  | Order-tag linking page               |
| `tagging/tag_unlink.php`      | 9 KB  | Order-tag unlinking page (OTP-gated) |
| `tagging/tag_scan.php`        | 3 KB  | Tag scan/lookup page                 |
| `tagging/tag_mark.php`        | 7 KB  | Tag marking page                     |
| `tagging/re_print.php`        | 9 KB  | Duplicate print page                 |
| `tagging/collection/form.php` | 5 KB  | Collection mapping form              |
| `tagging/collection/list.php` | 4 KB  | Collection mapping list              |

---

## 3. Constructor Dependencies

```php
// admin_ret_tagging.php constructor (lines 19-77)
const IMG_PATH   = 'assets/img/';
const PROD_PATH  = 'assets/img/products/';
const SERV_MODEL = "admin_usersms_model";
const SETT_MOD   = "admin_settings_model";

function __construct() {
    parent::__construct();
    ini_set('date.timezone', 'Asia/Calcutta');

    // Models loaded:
    $this->load->model('ret_tag_model');            // Primary model
    $this->load->model('admin_settings_model');      // Settings, access rights, profiles
    $this->load->model(self::SETT_MOD);              // Same as above (duplicate load)
    $this->load->model("sms_model");                 // SMS/OTP handling
    $this->load->model("log_model");                 // Logging
    $this->load->model("ret_brntransfer_model");     // Branch transfer operations
    $this->load->model('ret_billing_model');          // Billing cross-reference
    $this->load->model('ret_catalog_model');          // Catalog/product master
    $this->load->model('ret_metal_process_model');    // Metal processing

    // Session gate + time-based access check
    // Redirects to login if not authenticated
    // Redirects to logout if outside allowed access time window
}
```

### External Libraries

- `Dompdf\Dompdf` — PDF generation (used in tag printing)
- `phpqrcode` — QR code generation for tags

---

## 4. Route Map (Entry Points)

The primary entry point is `tagging($type, $id, $tag_print_id)` which is a **switch-case router** (lines 255-3003).

| Route                                       | Type | View Loaded                    | Access Check |
| ------------------------------------------- | ---- | ------------------------------ | ------------ |
| `admin_ret_tagging/tagging/add`             | Page | `tagging/form`                 | No           |
| `admin_ret_tagging/tagging/list`            | Page | `tagging/tag_list`             | Yes          |
| `admin_ret_tagging/tagging/duplicate_print` | Page | `tagging/re_print`             | Yes          |
| `admin_ret_tagging/tagging/tag_scan`        | Page | `tagging/tag_scan`             | No           |
| `admin_ret_tagging/tagging/tag_mark`        | Page | `tagging/tag_mark`             | No           |
| `admin_ret_tagging/tagging/tag_edit`        | Page | `tagging/tag_edit`             | Yes          |
| `admin_ret_tagging/tagging/tag_link`        | Page | `tagging/tag_link`             | No           |
| `admin_ret_tagging/tagging/tag_unlink`      | Page | `tagging/tag_unlink`           | No           |
| `admin_ret_tagging/tagging/save`            | AJAX | — (JSON response)              | No           |
| `admin_ret_tagging/tagging/edit`            | Page | `tagging/form`                 | No           |
| `admin_ret_tagging/tagging/edit_lot`        | Page | `tagging/form`                 | No           |
| `admin_ret_tagging/tagging/clone`           | Page | `tagging/form`                 | No           |
| `admin_ret_tagging/tagging/reprint`         | AJAX | — (PDF download)               | No           |
| `admin_ret_tagging/tagging/bulk_edit`       | Page | `tagging/bulk_edit`            | No           |
| `admin_ret_tagging/retagging`               | Page | `tagging/retagging_form/list`  | No           |
| `admin_ret_tagging/collection_mapping`      | Page | `tagging/collection/form/list` | No           |
| `admin_ret_tagging/bulk_tag_edit_log`       | Page | `tagging/bulk_edit_log`        | No           |

### AJAX Endpoints (Controller Methods)

| Method                             | Lines     | Purpose                                        |
| ---------------------------------- | --------- | ---------------------------------------------- |
| `updateTag()`                      | 3019-3225 | Update tag record from form                    |
| `generateTagsByRefNo()`            | 3227-3411 | Generate tags from lot ref number              |
| `get_tag_types()`                  | 3413-3421 | Fetch tag type dropdown data                   |
| `get_tag_purities()`               | 3423-3431 | Fetch purity dropdown data                     |
| `get_lot_ids()`                    | 3433-3443 | Fetch available lot IDs                        |
| `getDesignNosBySearch()`           | 3445-3453 | Search designs by text                         |
| `getDesignDetails()`               | 3455-3463 | Get design info for selected design            |
| `getDesignPurityByDesignId()`      | 3465-3473 | Purities available for a design                |
| `getDesignStonesByDesignId()`      | 3475-3483 | Stones defined for a design                    |
| `getTagStoneByTagId()`             | 3485-3493 | Stones on an existing tag                      |
| `getTagMaterialByTagId()`          | 3495-3503 | Materials on an existing tag                   |
| `getDesignMaterialsByDesignId()`   | 3505-3513 | Materials for a design template                |
| `getAvailableTaxGroups()`          | 3515-3523 | Tax group dropdown                             |
| `getAvailableTaxGroupItems()`      | 3525-3533 | Items in a tax group                           |
| `getStoneItems()`                  | 3535-3543 | Stone master dropdown                          |
| `getOtherCharges()`                | 3545-3553 | Charges master data                            |
| `getStoneTypes()`                  | 3555-3563 | Stone type master                              |
| `get_ActiveUOM()`                  | 3565-3573 | Active UOM dropdown                            |
| `getAvailableMaterials()`          | 3575-3583 | Materials dropdown                             |
| `get_metal_rates_by_branch()`      | 3585-3597 | Branch-wise metal rates                        |
| `get_tag_number()`                 | 3601-3617 | Tags for a lot/branch                          |
| `get_prod_by_tagno()`              | 3619-3633 | Product lookup by tag number                   |
| `get_tag_details()`                | 3635-3665 | Full tag details for edit/view                 |
| `update_tagging_data()`            | 3667-5066 | **MEGA method**: save tag + stones + materials |
| `admin_approval()`                 | 5068-5156 | Admin approval for tag changes                 |
| `resendotp()`                      | 5158-5248 | Resend OTP for tag operations                  |
| `send_sms()`                       | 5250-5266 | SMS dispatch utility                           |
| `get_lot_inward_details()`         | 5268-5288 | Lot inward details for tagging                 |
| `get_lot_products()`               | 5290-5304 | Products in a lot                              |
| `get_lot_split_products()`         | 5306-5320 | Split products in a lot                        |
| `get_lot_designs()`                | 5322-5338 | Designs available in a lot                     |
| `get_tagging_details()`            | 5340-5360 | Tagging details list                           |
| `get_tag_detail_list()`            | 5362-5370 | Tag detail list for DataTable                  |
| `lot_tag_detail()`                 | 5372-5400 | Lot-level tag detail                           |
| `get_duplicate_tag()`              | 5404-5416 | Check for duplicate tag data                   |
| `send_tag_otp()`                   | 5418-5516 | OTP for tag delete/edit                        |
| `verify_otp()`                     | 5518-5592 | OTP verification                               |
| `get_tag_scan_details()`           | 5598-5740 | Tag scan full detail fetch                     |
| `get_order_details()`              | 5744-5754 | Order details by lot                           |
| `get_tag_marking()`                | 5756-5800 | Tag marking list                               |
| `update_tag_mark()`                | 5802-5860 | Update tag mark status                         |
| `get_tag_edit_det()`               | 5862-5872 | Tag edit details                               |
| `update_tag()`                     | 5874-5965 | Quick tag update                               |
| `get_employee()`                   | 5967-5977 | Employee dropdown                              |
| `get_ActiveSize()`                 | 5979-5989 | Size dropdown                                  |
| `getTaggingBySearch()`             | 5993-6001 | Tag search for linking                         |
| `getOrdersBySearch()`              | 6003-6013 | Customer orders search                         |
| `getOrderDetailBySearch()`         | 6015-6025 | Customer order detail                          |
| `update_order_link()`              | 6027-6142 | Link tag to customer order                     |
| `generateTagCode()`                | 6148-6218 | Tag code generation algorithm                  |
| `validate_huid()`                  | 6222-6246 | Validate HUID uniqueness                       |
| `generate_tagqrcode()`             | 6248-6260 | Generate QR for single tag                     |
| `generate_retagqrcode()`           | 6262-6294 | Generate QR for retag process                  |
| `get_active_design_products()`     | 6298-6308 | Active design products                         |
| `get_ActiveSubDesingns()`          | 6310-6320 | Sub-designs dropdown                           |
| `get_wastage_settings_details()`   | 6326-6336 | Wastage settings lookup                        |
| `get_attributes_from_subdesign()`  | 6342-6358 | Attributes from subdesign                      |
| `get_product_charges()`            | 6360-6372 | Product charges lookup                         |
| `get_tag_charges()`                | 6374-6384 | Charges on a tag                               |
| `get_tag_attributes()`             | 6386-6396 | Attributes on a tag                            |
| `get_rate_from_metal_and_purity()` | 6424-6444 | Metal rate by purity                           |
| `delete_tag_attribute()`           | 6446-6466 | Delete a tag attribute                         |
| `get_po_details()`                 | 6470-6482 | Purchase order details                         |
| `add_to_transfer_tag()`            | 6486-6602 | Add tag to branch transfer                     |
| `retagging()`                      | 6608-6700 | Re-tagging page loader                         |
| `create_retag()`                   | 6704-7313 | **MEGA method**: create retag process          |
| `generateRetaglot()`               | 7317-7518 | Generate retagged lot                          |
| `generateRetagNontaglot()`         | 7522-7850 | Generate non-tag lot from retag                |
| `get_ActiveCollection()`           | 7858-7868 | Collections dropdown                           |
| `collection_mapping()`             | 7870-7984 | Collection mapping page/ops                    |
| `create_tag_collection()`          | 7986-8100 | Create/update collection mapping               |
| `get_order_linked_tags()`          | 8104-8114 | Tags linked to orders                          |
| `unlink_order_tags()`              | 8116-8176 | Unlink tag from order                          |
| `update_tag_img_by_id()`           | 8180-8313 | Update tag images                              |
| `get_img_by_id()`                  | 8315-8325 | Get tag images                                 |
| `get_CustomerOrders()`             | 8329-8339 | Customer orders for linking                    |
| `get_customer_order_details()`     | 8341-8351 | Order details for linking                      |
| `get_mc_va_limit()`                | 8353-8371 | Making charge / VA limits                      |
| `get_old_tag()`                    | 8373-8383 | Old tag lookup                                 |
| `generate_tagqrcode_bulk()`        | 8385-8417 | Bulk QR generation                             |
| `getTaggedLot()`                   | 8419-8429 | Tagged lots dropdown                           |
| `getTaggedRefNo()`                 | 8431-8441 | Tagged ref numbers                             |
| `get_section_details()`            | 8444-8454 | Section details                                |
| `get_tagArray()`                   | 8455-8597 | Tag array for printing                         |
| `generate_printer_code()`          | 8599-8641 | Printer code generation                        |
| `get_printer_code()`               | 8665-8813 | Printer code data                              |
| `isEmptySetDefault()`              | 9138-9150 | Utility: empty check with default              |
| `get_prev_huid()`                  | 9152-9162 | Previous HUID lookup                           |
| `send_order_unlink_otp()`          | 9164-9227 | OTP for order unlinking                        |
| `verify_order_unlink_otp()`        | 9229-9271 | Verify order unlink OTP                        |
| `calculate_base_value_tax()`       | 9274-9302 | Tax calculation helper                         |
| `calculate_arrived_value_tax()`    | 9304-9332 | Arrived value tax calculation                  |
| `get_tax_details()`                | 9334-9340 | Tax details                                    |
| `bulk_tag_edit_log()`              | 9342-9378 | Bulk edit log page                             |
| `update_purchase_cost()`           | 9381-9747 | **MEGA method**: update purchase cost          |
| `ret_duplicate_print_log_save()`   | 9748-9787 | Save duplicate print log                       |

---

## 5. Tag Status Codes

| Status Code | Meaning                                |
| ----------- | -------------------------------------- |
| 0           | On Sale (active stock)                 |
| 1           | Sold Out                               |
| 2           | Deleted                                |
| 3           | Other Issue                            |
| 4           | In Transit                             |
| 5           | Deleted for Stock                      |
| 6           | Sales Return                           |
| 7           | Stock Issue for Marketing / Photoshoot |
| 8           | Repair Item                            |
| 9           | Purchase Return                        |
| 10          | EDA Sale                               |
| 11          | Tag Booked for Advance                 |
| 13          | Added to Pocket                        |
| 14          | Transferred to home section            |
| 17          | Metal Issue                            |

> **Note**: Status 12, 15, 16 are unused/skipped.

---

## 6. Key Database Tables

| Table                        | Purpose                                |
| ---------------------------- | -------------------------------------- |
| `ret_taging`                 | **Primary**: Tag records               |
| `ret_taging_stone`           | Tag stone/diamond details              |
| `ret_taging_material`        | Tag material details (other metals)    |
| `ret_taging_images`          | Tag images                             |
| `ret_tag_attributes`         | Tag custom attributes                  |
| `ret_lot_inwards`            | Lot inward records (source data)       |
| `ret_lot_inward_detail`      | Lot inward line items                  |
| `ret_product_master`         | Product master (metal type categories) |
| `ret_design_master`          | Design master                          |
| `ret_sub_design_master`      | Sub-design master                      |
| `ret_purity`                 | Purity master (e.g., 22K, 18K)         |
| `ret_stone`                  | Stone master                           |
| `ret_karigar`                | Goldsmith/vendor master                |
| `ret_branch_transfer`        | Branch transfers                       |
| `ret_branch_transfer_items`  | Branch transfer line items             |
| `ret_section`                | Section master                         |
| `ret_section_tag_status_log` | Section tag status log                 |
| `ret_tag_collection`         | Collection mapping records             |
| `ret_tag_type_master`        | Tag type master                        |
| `ret_uom`                    | Unit of measure                        |
| `ret_size`                   | Size master                            |
| `ret_metal_rate`             | Metal rates by branch                  |
| `ret_retagging_process`      | Re-tagging process records             |
| `ret_non_tag_stock`          | Non-tag stock records                  |
| `ret_selling_settings`       | Wastage/MC settings                    |
| `ret_purchase_order`         | Purchase order header                  |
| `ret_purchase_order_items`   | Purchase order items                   |
| `ret_category`               | Product category master                |
| `ret_quality_master`         | Quality master                         |
| `ret_dup_print_log`          | Duplicate print log                    |

---

## 7. Cross-Module Dependencies

| External Model            | Used For                                    |
| ------------------------- | ------------------------------------------- |
| `admin_settings_model`    | Access rights, profiles, branch day closing |
| `sms_model`               | OTP SMS sending                             |
| `log_model`               | Activity logging                            |
| `ret_brntransfer_model`   | Branch transfer code generation             |
| `ret_billing_model`       | Check if tag is billed                      |
| `ret_catalog_model`       | Product catalog references                  |
| `ret_metal_process_model` | Metal process linkage                       |
| `admin_usersms_model`     | User SMS operations                         |

### Modules That Read Tagging Data

- **Billing** (`ret_billing_model`) — reads tag details for sale transactions
- **Branch Transfer** (`ret_brntransfer_model`) — reads tags for transfer operations
- **Reports** (`ret_reports_model`) — reads tag data for stock reports
- **Estimation** (`est_model`) — may reference tag data for estimates
- **Metal Process** (`ret_metal_process_model`) — reads tags for melting/processing

---

## 8. Known Complexity Hotspots

### Bug Root Cause Register

#### TAG-CLT02: Duplicate Tag Form Does Not Show Closed Lots ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| TAG-CLT02 | Client-side JS `is_closed==0` filter rejected closed lots even though backend already returned them | Added `duplicate_print` page exception to the condition at `ret_tagging.js` L6219 | 2026-03-24 |

**Prevention**: When backend supports conditional filtering (e.g., `include_closed`), ensure client-side rendering logic respects the same condition — don't apply a hardcoded filter client-side that contradicts the backend parameter.

> **Note (TAG-CLT02)**: The `get_received_lots()` JS function serves both the Add Tag form (which should exclude closed lots) and the Duplicate Tag form (which should include them). The backend was correctly parameterized but the JS had a blanket `is_closed==0` client-side filter applied to `#tag_lot_received_id` regardless of page context.

#### TAG-UI-01: Pur MC Type Displays Wrong Label in Tagging Form ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| TAG-UI-01 | `#tag_pur_id_mc_type` select options had reversed labels (value=1→"Per Piece", value=2→"Per Gram") — opposite of system-wide convention (value=1=Per Gram) | Corrected option labels in `tagging/form.php` lines 1072-1073 to match lot_inward/purchase convention | 2026-04-02 |

**Prevention**: When a form receives data from another module (e.g., `item.pur_mc_type` from lot_inward API), the receiving dropdown's value→label mapping **must match the source module's DB convention**. Cross-check value→label assignments against the source module's form before writing the view.

> **Note (TAG-UI-01)**: System-wide DB convention: `mc_type 1 = Per Gram`, `mc_type 2 = Per Piece`. This is consistent across `lot/form.php`, `ret_purchase/pur_entry_form.php`, and `tagging/bulk_edit.php`. Only `tagging/form.php`'s `#tag_pur_id_mc_type` had reversed labels. The `#tag_id_mc_type` (sell MC) in the same file uses a different JS calculation path — do NOT apply the same swap without verifying the JS math for that field.
>
> **Field-Level Trace**: `ret_lot_inward_detail.mc_type` (DB) → `item.pur_mc_type` (JSON/API) → `$('#tag_pur_id_mc_type').val(item.pur_mc_type)` (JS) → `#tag_pur_id_mc_type` (HTML select) → `pur_id_mc_type[]` (POST) → `lot_mc_type` (DB column in `ret_taging`).



### MEGA Methods (>300 lines)

1. **`tagging()` controller** (lines 255-3003, ~2748 lines) — Primary switch-case router with embedded save logic
2. **`update_tagging_data()`** (lines 3667-5066, ~1400 lines) — Tag save/update with stones, materials, images, branch transfer
3. **`create_retag()`** (lines 6704-7313, ~609 lines) — Re-tagging process creation
4. **`update_purchase_cost()`** (lines 9381-9747, ~366 lines) — Purchase cost update from PO data
5. **`generateRetagNontaglot()`** (lines 7522-7850, ~328 lines) — Non-tag lot from retag

### Security Observations

- `$_POST['lt_item']` used directly (line 359) — bypasses CI3 input filtering
- Raw SQL with string concatenation in model (e.g., `getLotRefNo()` line 320) — SQL injection risk
- `$_FILES` manipulated directly for image handling — file upload validation needed
- Multiple methods lack CSRF token checking
