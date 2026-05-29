# Catalog_Inventory — VIEW MAP & HIDDEN FIELDS

> **Built**: 2026-03-13 — Round 3
> **Purpose**: Catalog of all views loaded by this controller, their hidden fields, and DOM IDs for forensic tracing.

---

## 1. View File Inventory

### Catalog Directory (`views/catalog/`)
| File | Lines | Hidden Fields | Purpose |
|---|---|---|---|
| `catalog/category/form.php` | — | 2 | Web catalog category form |
| `catalog/category/list.php` | 6439 | 1 (`edit-id`) | Web catalog category list |
| `catalog/product/form.php` | — | 2 | Web catalog product form |
| `catalog/product/list.php` | 6439 | 0 | Web catalog product list |

### Master Directory (`views/master/`) — Loaded by `admin_ret_catalog`
| View Path | Hidden Fields | Key DOM IDs |
|---|---|---|
| `master/ret_category/list.php` | 11 | `add_category_status`, `id_metal_category`, `tgrp_id`, `pur_id`, `edit-id`, `edit_category_status`, `id_metal_cate`, `ed_tgrp_id`, `ed_pur_id` |
| `master/ret_product/form.php` | 18 | `editid_product`, `product_status`, `metal_id`, `category_id`, `tax_id`, `pur_tax_id`, `id_section`, `selected_sections[]`, `size` |
| `master/ret_product/form - with all settings.php` | 4 | Same as form.php (archived copy) |
| `master/ret_product/bulk_prod_upd.php` | 3 | `product_name`, `tax_group_id`, `product_status` |
| `master/ret_product/product_mapping.php` | 3 | `sub_des_status`, `edit-id`, `ed_sd_status` |
| `master/ret_product/subdesign_mapping.php` | 5 | `id_sub_design_mapping`, `subdesign_images`, `id`, `id_sub_des`, `karigar` |
| `master/karigar/form.php` | 37 | `user_type`, `is_tcs`, `is_tds`, `bal_amount`, `user_status`, `id_country`, `ed_id_country`, `branch_id_state`, `branch_id_country`, `branch_id_city`, `id_state`, `ed_id_state`, `id_city`, `ed_id_city`, `id_karigar`, `i_increment`, `i_increment_stn`, `nb_increment`, `kyc_doc_file`, `image_type`, `document_type`, `wastImg_active_row`, `id_karikar_wast`, `wast_pro_image`, `image-tag`, `charge_active_row`, `frimg_active_row`, `bkimg_active_row`, `doc_active_row`, `opening_bal_amt` |
| `master/karigar/list.php` | 14 | `id`, `countryval`, `stateval`, `cityval`, `user_status`, `edit-id`, `ed_user_status` |
| `master/karigar/approval_list.php` | 5 | `is_otp_verfied`, `send_resend`, `resend_mobile`, `otp_required`, `charge_active_row` |
| `master/purity/list.php` | ~3 | `edit-id`, `purity_status`, `ed_purity_status` |
| `master/color/list.php` | ~3 | `edit-id`, `color_status`, `ed_color_status` |
| `master/cut/list.php` | ~3 | `edit-id`, `cut_status`, `ed_cut_status` |
| `master/clarity/list.php` | ~3 | `edit-id`, `clarity_status`, `ed_clarity_status` |
| `master/stone/list.php` | 11 | `uom_id`, `edit-id`, `is_certificate_req`, `is_4c_req`, `stone_status`, `ed_uom_id`, `ed_is_certificate_req`, `ed_is_4c_req`, `ed_stone_status` |
| `master/uom/list.php` | 4 | `add_uom_status`, `edit-id`, `edit_uom_status` |
| `master/screw/list.php` | 4 | `add_screw_status`, `edit-id`, `edit_screw_status` |
| `master/hook/list.php` | ~4 | `add_hook_status`, `edit-id`, `edit_hook_status` |
| `master/weight/list.php` | 1 | `edit-id` |
| `master/shape/list.php` | 1 | `edit-id` |
| `master/tag/list.php` | 2 | `adtag_status`, `ed_tag_status` |
| `master/tax/list.php` | 2 | `adtax_status`, `ed_tax_status` |
| `master/tax/tax group/form.php` | 3 | `edit-id/tgrp_id`, `ad_tgrp_status`, tgi count |
| `master/sub_category/form.php` | 3 | `id_category`, `id_subcategory`, `image` |
| `master/web_devices/form.php` | 3 | `id_branch`, `id_floor`, `id_counter` |

---

## 2. Hidden Fields by Category

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
| `id_branch`/`id_floor`/`id_counter` | web_devices/form | Branch/floor/counter tables | Cascading location select |

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

## 3. Settings Keys Used

| Setting Category | Source | Used In | Key/Value |
|---|---|---|---|
| Access Control | `admin_settings_model->get_access()` | All view loading (default cases) | Returns `{edit: 0/1, delete: 0/1}` |
| Session Time Gate | `access_time_from` / `access_time_to` | Constructor L30-55 | Restricts access to specific hours |
| OTP Required | `get_profile_settings()` → `vendor_approval_otp_req` | karigar/approval_list L83 | Passed as `$otp_settings` to view |
| Branch Settings | `session->userdata('branch_settings')` | Various views | Multi-branch aware views |

> **Note**: This controller does NOT directly call `get_ret_settings()`. All its settings are either session-based or from `admin_settings_model->get_access()`.

---

## 4. View Load Map (Controller → View)

| Controller Method | Switch Case | View Loaded |
|---|---|---|
| `category()` | `list` | `master/ret_category/list` |
| `ret_product()` | `add`/`edit` | `master/ret_product/form` |
| `ret_product()` | `list` | `master/ret_product/list` |
| `bulkprodupdated()` | default | `master/ret_product/bulk_prod_upd` |
| `karigar()` | `add`/`edit` | `master/karigar/form` |
| `karigar()` | `list` | `master/karigar/list` |
| `karigar_approval()` | default | `master/karigar/approval_list` |
| `purity()` | `list` | `master/purity/list` |
| `color()` | `list` | `master/color/list` |
| `cut()` | `list` | `master/cut/list` |
| `clarity()` | `list` | `master/clarity/list` |
| `stone()` | `list` | `master/stone/list` |
| `uom()` | `list` | `master/uom/list` |
| `tag()` | `list` | `master/tag/list` |
| `tax()` | `list` | `master/tax/list` |
| `tgrp()` | `list`/`add`/`edit` | `master/tax/tax group/form` |
| `ret_design()` | `list`/`add`/`edit` | `master/ret_product/design` |
| `ret_sub_design()` | `list` | `master/ret_product/subdesign` |
| `ret_products_mapping()` | `list` | `master/ret_product/product_mapping` |
| `ret_subdesign_mapping()` | `list` | `master/ret_product/subdesign_mapping` |
| `web_devices()` | `add`/`edit` | `master/web_devices/form` |
| `screw()` | `list` | `master/screw/list` |
| `hook()` | `list` | `master/hook/list` |
| `weight()` | `list` | `master/weight/list` |
| `shape()` | `list` | `master/shape/list` |
| `material()` | `list` | `master/material/list` |
| `making_type()` | `list` | `master/making_type/list` |
| `theme()` | `list` | `master/theme/list` |
| `branch_floor()` | `list` | `master/floor/list` |
| `floor_counter()` | `list` | `master/floor_ctr/list` |
| `financial_year()` | `list` | `master/financial_year/list` |
| `sub_product()` | search | `master/sub_category/form` |
