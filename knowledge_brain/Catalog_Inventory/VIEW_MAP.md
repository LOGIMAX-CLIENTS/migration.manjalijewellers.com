# Catalog_Inventory — VIEW MAP & HIDDEN FIELDS

> **Built**: 2026-03-13 — Round 3
> **Updated**: 2026-03-14 — Round 7 (complete view inventory)
> **Purpose**: Catalog of all views loaded by this controller, their hidden fields, and DOM IDs for forensic tracing.

---

## 1. View File Inventory

### Loading Pattern
All views loaded via `$data['main_content'] = "path/to/view"` → `$this->load->view('layout/template', $data)`.
One exception: `master/karigar/karigar_print` loaded directly via `$this->load->view(..., $data, true)` for HTML return.

### Complete View List (82 unique paths, 80 exist on disk)

#### A. Core Product Views
| View Path | Hidden Fields | Exists | Notes |
|---|---|---|---|
| `master/ret_product/form` | 10 | ✅ | Main product add/edit form |
| `master/ret_product/list` | 0 | ✅ | Product list |
| `master/ret_product/bulk_prod_upd` | 3 | ✅ | Bulk product update |
| `master/ret_product/product_mapping` | 3 | ✅ | Product-design mapping |
| `master/ret_product/subdesign_mapping` | 5 | ✅ | Sub-design mapping |
| `master/ret_sub_product/form` | 4 | ✅ | Sub-product form |
| `master/ret_sub_product/list` | 0 | ✅ | Sub-product list |
| `master/ret_category/list` | 11 | ✅ | Category list with add/edit modals |
| `master/product_division/list` | 1 | ✅ | Product division list |
| `master/product_grouping/list` | 5 | ✅ | Product grouping list |

#### B. Design & Sub-Design Views
| View Path | Hidden Fields | Exists |
|---|---|---|
| `master/ret_design/form` | 13 | ✅ |
| `master/ret_design/list` | 1 | ✅ |
| `master/ret_design/bulk_edit_list` | 3 | ✅ |
| `master/ret_sub_design/form` | 0 | ✅ |
| `master/ret_sub_design/list` | 3 | ✅ |
| `master/attribute/form` | 2 | ✅ |
| `master/attribute/list` | 0 | ✅ |

#### C. Material & Quality Masters
| View Path | Hidden Fields | Exists |
|---|---|---|
| `master/purity/list` | 1 | ✅ |
| `master/color/list` | 1 | ✅ |
| `master/cut/list` | 1 | ✅ |
| `master/clarity/list` | 1 | ✅ |
| `master/stone/list` | 10 | ✅ |
| `master/material/list` | 4 | ✅ |
| `master/material/rate_list` | 5 | ✅ |
| `master/metal/list` | 6 | ✅ |
| `master/metal_category/list` | 1 | ✅ |
| `master/metal_type/list` | 2 | ✅ |
| `master/uom/list` | 4 | ✅ |
| `master/carat/list` | — | ❌ DEAD REF |

#### D. Karigar (Artisan) Views
| View Path | Hidden Fields | Exists |
|---|---|---|
| `master/karigar/form` | 30 | ✅ |
| `master/karigar/list` | 14 | ✅ |
| `master/karigar/approval_list` | 5 | ✅ |
| `master/karigar/ret_product_mapping` | 0 | ✅ |
| `master/karigar/karigar_print` | 0 | ✅ (direct load) |

#### E. Pricing & Rates Views
| View Path | Hidden Fields | Exists |
|---|---|---|
| `master/diamond/form` | 6 | ✅ |
| `master/diamond/list` | 0 | ✅ |
| `master/diamond/diamond_rate/form` | 1 | ✅ |
| `master/diamond/diamond_rate/list` | 0 | ✅ |
| `master/diamond/selling_diamond_rate/form` | 1 | ✅ |
| `master/diamond/selling_diamond_rate/list` | 0 | ✅ |
| `master/ret_rate_purity_master/list` | 7 | ✅ |
| `master/ret_stone_rate_settings/form` | 2 | ✅ |
| `master/ret_stone_rate_settings/list` | 1 | ✅ |
| `master/discount/wastage_form` | 6 | ✅ |
| `master/discount/wastage_list` | 5 | ✅ |
| `master/discount/stone_list` | 2 | ✅ |
| `master/ret_selling_settings/form` | 8 | ✅ |
| `master/ret_selling_settings/list` | 2 | ✅ |

#### F. Location & Infrastructure Views
| View Path | Hidden Fields | Exists |
|---|---|---|
| `master/floor/list` | 5 | ✅ |
| `master/floor_counter/list` | 6 | ✅ |
| `master/ret_section/list` | 5 | ✅ |
| `master/ret_device/list` | 3 | ✅ |
| `master/web_devices/form` | 0 | ✅ |
| `master/ret_stock/list` | 3 | ✅ |

> **Note**: `web_devices/form` hidden field count = 0 from regex. The 3 previously documented (`id_branch`, `id_floor`, `id_counter`) may use dynamic generation or different patterns. Retained for reference.

#### G. Financial & Tax Views
| View Path | Hidden Fields | Exists |
|---|---|---|
| `master/finance_year/form` | 0 | ✅ |
| `master/finance_year/list` | 0 | ✅ |
| `master/tax/list` | 2 | ✅ |
| `master/tax/tax group/form` | 3 | ✅ |
| `master/tax/tax group/list` | 0 | ✅ |
| `master/charges/list` | 3 | ✅ |
| `master/ret_account/list` | 3 | ✅ |
| `master/ret_crdr_ledger/list` | 3 | ✅ |
| `master/deposit/form` | 1 | ✅ |
| `master/deposit/list` | 0 | ✅ |

#### H. Misc Master Views
| View Path | Hidden Fields | Exists |
|---|---|---|
| `master/making_type/list` | 4 | ✅ |
| `master/theme/list` | 2 | ✅ |
| `master/screw/list` | 4 | ✅ |
| `master/hook/list` | 4 | ✅ |
| `master/ret_weight/form` | 6 | ✅ |
| `master/ret_weight/list` | 10 | ✅ |
| `master/shape/list` | 1 | ✅ |
| `master/ret_reorder_settings/form` | 8 | ✅ |
| `master/ret_reorder_settings/list` | 14 | ✅ |
| `master/ret_delivery/list` | 1 | ✅ |
| `master/ret_size/ret_size` | 3 | ✅ |
| `master/old_metal_rate/list` | 2 | ✅ |
| `master/ret_collection/list` | 0 | ✅ |
| `master/ret_repair_master/list` | 1 | ✅ |
| `master/ret_breakeven_logs/list` | 6 | ✅ |
| `master/ret_qc_cancel_reason/list` | 3 | ✅ |
| `master/cover_up/list` | 2 | ✅ |
| `master/sub_category/form` | 3 | ✅ |
| `master/tag/list` | 2 | ✅ |
| `master/customer/feedback/list` | — | ❌ DEAD REF |
| `day_close/list` | 5 | ✅ |

### Summary Counts
| Category | Views | Hidden Fields | Dead Refs |
|---|---|---|---|
| A. Core Product | 10 | 42 | 0 |
| B. Design & Sub-Design | 7 | 22 | 0 |
| C. Material & Quality | 12 | 31 | 1 (`carat/list`) |
| D. Karigar | 5 | 49 | 0 |
| E. Pricing & Rates | 14 | 41 | 0 |
| F. Location & Infra | 6 | 22 | 0 |
| G. Financial & Tax | 10 | 15 | 0 |
| H. Misc Masters | 21 | 83 | 1 (`customer/feedback`) |
| **Total** | **85** | **305** | **2** |

> 85 total paths = 82 from `main_content` + 1 direct load (`karigar_print`) + 2 dead refs. Actual existing files = 81.

---

## 2. Dead View References

| View Path | Referenced At | Status |
|---|---|---|
| `master/carat/list` | Carat entity (L682 — commented-out entity) | Code references exist but file doesn't — carat entity was deprecated |
| `master/customer/feedback/list` | `feedback()` method | Code references exist but file doesn't — feedback view missing from disk |

---

## 3. Hidden Fields by Category

### A. Record ID Fields (for edit/update operations)
| DOM ID | View | DB Column | Purpose |
|---|---|---|---|
| `edit-id` | Multiple lists | `{pk_column}` | Record ID for edit modal population |
| `editid_product` | ret_product/form | `pro_id` | Product ID in edit mode |
| `id_karigar` | karigar/form | `id_karigar` | Karigar ID in edit mode |
| `id_sub_design_mapping` | subdesign_mapping | `id_sub_design_mapping` | Sub-design mapping ID |

### B. Status Default Fields (hardcoded to 1 on add)
| DOM ID | View | Default Value | Purpose |
|---|---|---|---|
| `add_category_status` | ret_category/list | `1` | New category defaults to active |
| `product_status` | ret_product/form | `1` | New product defaults to active |
| `user_status` | karigar/form | `1` | New karigar defaults to active |
| `add_uom_status` | uom/list | `1` | New UOM defaults to active |
| `add_screw_status` | screw/list | `1` | New screw defaults to active |
| `adtag_status` | tag/list | `1` | New tag type defaults to active |
| `adtax_status` | tax/list | `1` | New tax defaults to active |
| `stone_status` | stone/list | `1` | New stone defaults to active |
| `ad_tgrp_status` | tax group/form | `1` | New tax group defaults to active |

### C. Cascading Select Fields (parent-child dropdowns)
| DOM ID | View | Source | Purpose |
|---|---|---|---|
| `id_metal_category`/`id_metal_cate` | ret_category/list | `metal` table | Metal type for add/edit |
| `tgrp_id`/`ed_tgrp_id` | ret_category/list | `ret_taxgroupmaster` | Tax group for add/edit |
| `pur_id`/`ed_pur_id` | ret_category/list | `ret_purity` | Purity selection for add/edit |
| `metal_id` | ret_product/form | `metal` table | Metal type for product |
| `category_id` | ret_product/form | `ret_category` | Category for product |
| `tax_id`/`pur_tax_id` | ret_product/form | `ret_taxgroupmaster` | Tax group for product |
| `size` | ret_product/form | `ret_uom` | UOM for product |
| `uom_id`/`ed_uom_id` | stone/list | `ret_uom` | UOM for stone |

### D. Location Cascade Fields (karigar address)
| DOM ID | View | Purpose |
|---|---|---|
| `id_country`/`ed_id_country` | karigar/form | Country ID (add/edit) |
| `id_state`/`ed_id_state` | karigar/form | State ID (add/edit) |
| `id_city`/`ed_id_city` | karigar/form | City ID (add/edit) |
| `branch_id_state`/`branch_id_country`/`branch_id_city` | karigar/form | Branch location IDs |
| `countryval`/`stateval`/`cityval` | karigar/list | Quick-add address values |

### E. Increment Counters (dynamic row adding)
| DOM ID | View | Purpose |
|---|---|---|
| `i_increment` | karigar/form | Wastage row counter |
| `i_increment_stn` | karigar/form | Stone row counter |
| `nb_increment` | karigar/form | Bank account row counter |
| `charge_active_row` | karigar/form+approval | Charge row counter |
| `wastImg_active_row` | karigar/form | Wastage image row counter |
| `frimg_active_row` | karigar/form | Front image row counter |
| `bkimg_active_row` | karigar/form | Back image row counter |
| `doc_active_row` | karigar/form | Document row counter |

### F. OTP/Approval Flow Fields
| DOM ID | View | Purpose |
|---|---|---|
| `is_otp_verfied` | karigar/approval_list | Flag: OTP verified (0/1) |
| `send_resend` | karigar/approval_list | Flag: send vs resend OTP |
| `resend_mobile` | karigar/approval_list | Mobile for OTP resend |
| `otp_required` | karigar/approval_list | From settings: is OTP required |

### G. Feature Toggle Fields
| DOM ID | View | DB Column | Effect |
|---|---|---|---|
| `is_tcs` | karigar/form | `ret_karigar.is_tcs` | TCS applicable flag |
| `is_tds` | karigar/form | `ret_karigar.is_tds` | TDS applicable flag |
| `user_type` | karigar/form | `ret_karigar.karigar_type` | Karigar type (internal/external) |
| `is_certificate_req` | stone/list | `ret_stone.is_certificate_req` | Stone certificate required |
| `is_4c_req` | stone/list | `ret_stone.is_4c_req` | 4C grading required (diamond) |

### H. Image/Document Upload Fields
| DOM ID | View | Purpose |
|---|---|---|
| `wast_pro_image` | karigar/form | Wastage proof image path |
| `id_karikar_wast` | karigar/form | Wastage record ID for image |
| `image-tag` | karigar/form | Image file tag |
| `kyc_doc_file` | karigar/form | KYC document file path |
| `image_type` | karigar/form | Image type (front/back) |
| `document_type` | karigar/form | Document type (Aadhaar/PAN/etc.) |
| `subdesign_images` | subdesign_mapping | Sub-design image paths |

---

## 4. Settings Keys Used

| Setting Category | Source | Used In | Key/Value |
|---|---|---|---|
| Access Control | `admin_settings_model->get_access()` | All view loading (default cases) | Returns `{edit: 0/1, delete: 0/1}` |
| Session Time Gate | `access_time_from` / `access_time_to` | Constructor L30-55 | Restricts access to specific hours |
| OTP Required | `get_profile_settings()` → `vendor_approval_otp_req` | karigar/approval_list L83 | Passed as `$otp_settings` to view |
| Branch Settings | `session->userdata('branch_settings')` | Various views | Multi-branch aware views |

> **Note**: This controller does NOT directly call `get_ret_settings()`. All its settings are either session-based or from `admin_settings_model->get_access()`.

---

## 5. View Load Map (Controller → View)

| Controller Method | Switch Case | View Loaded |
|---|---|---|
| `category()` | `list` | `master/ret_category/list` |
| `ret_product()` | `add`/`edit` | `master/ret_product/form` |
| `ret_product()` | `list` | `master/ret_product/list` |
| `ret_sub_product()` | `add`/`edit` | `master/ret_sub_product/form` |
| `ret_sub_product()` | `list` | `master/ret_sub_product/list` |
| `bulkprodupdated()` | default | `master/ret_product/bulk_prod_upd` |
| `ret_design()` | `add`/`edit` | `master/ret_design/form` |
| `ret_design()` | `list` | `master/ret_design/list` |
| `ret_design()` | bulk | `master/ret_design/bulk_edit_list` |
| `ret_sub_design()` | form | `master/ret_sub_design/form` |
| `ret_sub_design()` | `list` | `master/ret_sub_design/list` |
| `attribute()` | `add`/`edit` | `master/attribute/form` |
| `attribute()` | `list` | `master/attribute/list` |
| `karigar()` | `add`/`edit` | `master/karigar/form` |
| `karigar()` | `list` | `master/karigar/list` |
| `karigar_approval()` | default | `master/karigar/approval_list` |
| `ret_karigar_product()` | default | `master/karigar/ret_product_mapping` |
| `purity()` | `list` | `master/purity/list` |
| `color()` | `list` | `master/color/list` |
| `cut()` | `list` | `master/cut/list` |
| `clarity()` | `list` | `master/clarity/list` |
| `stone()` | `list` | `master/stone/list` |
| `material()` | `list` | `master/material/list` |
| `material_rate()` | `list` | `master/material/rate_list` |
| `metal()` | `list` | `master/metal/list` |
| `metal_cat()` | `list` | `master/metal_category/list` |
| `metal_type()` | `list` | `master/metal_type/list` |
| `uom()` | `list` | `master/uom/list` |
| `diamond()` | `add`/`edit` | `master/diamond/form` |
| `diamond()` | `list` | `master/diamond/list` |
| `diamond_rate()` | `add`/`edit` | `master/diamond/diamond_rate/form` |
| `diamond_rate()` | `list` | `master/diamond/diamond_rate/list` |
| `selling_diamond_rate()` | `add`/`edit` | `master/diamond/selling_diamond_rate/form` |
| `selling_diamond_rate()` | `list` | `master/diamond/selling_diamond_rate/list` |
| `ret_metalpurity()` | `list` | `master/ret_rate_purity_master/list` |
| `ret_stone_rate_settings()` | form | `master/ret_stone_rate_settings/form` |
| `ret_stone_rate_settings()` | `list` | `master/ret_stone_rate_settings/list` |
| `wastage_mc_settings()` | form | `master/discount/wastage_form` |
| `wastage_mc_settings()` | `list` | `master/discount/wastage_list` |
| `wastage_discount()` | `list` | `master/discount/wastage_list` |
| `stone_discount()` | `list` | `master/discount/stone_list` |
| `ret_selling_settings()` | form | `master/ret_selling_settings/form` |
| `ret_selling_settings()` | `list` | `master/ret_selling_settings/list` |
| `branch_floor()` | `list` | `master/floor/list` |
| `floor_counter()` | `list` | `master/floor_counter/list` |
| `ret_section()` | `list` | `master/ret_section/list` |
| `web_devices()` | `add`/`edit` | `master/web_devices/form` |
| `web_devices()` | `list` | `master/ret_device/list` |
| `tag()` | `list` | `master/tag/list` |
| `financial_year()` | `add`/`edit` | `master/finance_year/form` |
| `financial_year()` | `list` | `master/finance_year/list` |
| `tax()` | `list` | `master/tax/list` |
| `tgrp()` | `add`/`edit` | `master/tax/tax group/form` |
| `tgrp()` | `list` | `master/tax/tax group/list` |
| `charges()` | `list` | `master/charges/list` |
| `ret_account()` | `list` | `master/ret_account/list` |
| `ret_crdr_ledger()` | `list` | `master/ret_crdr_ledger/list` |
| `bank_deposit()` | form | `master/deposit/form` |
| `bank_deposit()` | `list` | `master/deposit/list` |
| `making_type()` | `list` | `master/making_type/list` |
| `theme()` | `list` | `master/theme/list` |
| `screw()` | `list` | `master/screw/list` |
| `hook()` | `list` | `master/hook/list` |
| `weight()` | form | `master/ret_weight/form` |
| `weight()` | `list` | `master/ret_weight/list` |
| `shape()` | `list` | `master/shape/list` |
| `reorder_settings()` | form | `master/ret_reorder_settings/form` |
| `reorder_settings()` | `list` | `master/ret_reorder_settings/list` |
| `ret_delivery()` | `list` | `master/ret_delivery/list` |
| `ret_size()` | default | `master/ret_size/ret_size` |
| `old_metal_rate()` | `list` | `master/old_metal_rate/list` |
| `ret_collection()` | `list` | `master/ret_collection/list` |
| `repair_master()` | `list` | `master/ret_repair_master/list` |
| `ret_breakeven_logs()` | `list` | `master/ret_breakeven_logs/list` |
| `ret_qc_cancel_reason()` | `list` | `master/ret_qc_cancel_reason/list` |
| `cover_up()` | `list` | `master/cover_up/list` |
| `day_close()` | `list` | `day_close/list` |
| `ret_stock()` | `list` | `master/ret_stock/list` |
| `sub_product()` | search | `master/sub_category/form` |
| `feedback()` | `list` | `master/customer/feedback/list` ⚠️ DEAD |
| `product_division()` | `list` | `master/product_division/list` |
| `product_grouping()` | `list` | `master/product_grouping/list` |
