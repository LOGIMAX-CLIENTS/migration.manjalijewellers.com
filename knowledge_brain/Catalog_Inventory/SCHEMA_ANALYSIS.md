# Catalog_Inventory — SCHEMA ANALYSIS

> **Built**: 2026-03-13
> **Updated**: 2026-03-14 — Round 8 (11 tables added from model SQL scan)

## Part A: Owned Tables (Core)

### ret_product_master
**Purpose**: Retail product definitions (main product entity used across the system)
**Key Columns**: `pro_id` (PK), `product_name`, `product_short_code`, `cat_id` (FK→ret_category), `tgrp_id` (FK→ret_taxgroupmaster), `hsn_code`, `stock_type`, `stone_type`, `sales_mode`, `purchase_mode`, `wastage_type`, `min_wastage`, `max_wastage`, `has_stone`, `has_hook`, `has_screw`, `has_fixed_price`, `metal_type`, `has_size`, `less_stone_wt`, `tag_split`, `tag_merge`, `tag_type`, `other_charges`, `net_wt`, `stock_report`, `hallmark`, `counter`, `stone_board_rate_cal`, `calculation_based_on`, `sales_markup`, `product_status`, `rfid_required`, `rfid_in_stock`, `display_purity`, `uom_id`, `reorder_based_on`, `image`, `tax_type`, `tax_group_id`, `created_time`, `create_by`, `updated_time`, `updated_by`
**Write Methods**: `insertData()`, `updateData()`, `deleteData()`
**Indexes**: Primary on `pro_id`
**⚠️ Risk**: 40+ columns — very wide table. `product_status` for soft-delete.

### ret_category
**Purpose**: Product category hierarchy (Gold, Silver, Diamond, etc.)
**Key Columns**: `id_ret_category` (PK), `name`, `cat_code`, `hsn_code`, `description`, `id_metal` (FK→metal), `cat_type`, `tgrp_id`, `is_multimetal`, `image`, `status`, `created_on`, `created_by`, `updated_on`, `updated_by`
**Write Methods**: `insertData()`, `updateData()`, `deleteData()`
**Child Junction**: `ret_metal_cat_purity`

### ret_metal_cat_purity
**Purpose**: Junction table linking categories to their allowed purities
**Key Columns**: `id_category` (FK→ret_category), `id_purity` (FK→ret_purity), `created_on`, `created_by`
**⚠️ Risk**: DELETE-then-INSERT on category update. No cascade delete when category is deleted.

### ret_product_section
**Purpose**: Junction table linking products to store sections
**Key Columns**: `pro_id` (FK→ret_product_master), `id_section` (FK→ret_section)
**⚠️ Risk**: Not cleaned up on product delete — orphan rows remain

### ret_product_charges
**Purpose**: Charges associated with each product
**Key Columns**: `prod_id` (FK→ret_product_master), `charge_id` (FK→ret_charges), `charge_value`, `created_on`, `created_by`
**Write Pattern**: DELETE-then-INSERT on product update

### ret_purity
**Purpose**: Purity master (22K, 24K, 916, etc.)
**Key Columns**: `id_purity` (PK), `purity`, `description`, `status`, `created_by`, `created_on`, `updated_by`, `updated_on`
**⚠️ SQL Injection**: `get_purity($id)` uses string concatenation

### ret_color
**Purpose**: Diamond/gem color master
**Key Columns**: `id_color` (PK), `color`, `description`, `status`, `id_employee`, `created_by`, `created_on`
**⚠️ SQL Injection**: `get_color($id)` uses string concatenation

### ret_cut
**Purpose**: Diamond cut master
**Key Columns**: `id_cut` (PK), `cut`, `description`, `status`
**⚠️ SQL Injection**: `get_cut($id)` uses string concatenation

### ret_clarity
**Purpose**: Diamond clarity master
**Key Columns**: `id_clarity` (PK), `clarity`, `description`, `status`
**⚠️ SQL Injection**: `get_clarity($id)` uses string concatenation

### ret_karigar
**Purpose**: Artisan/karigar master (jewellery makers)
**Key Columns**: `karigar_id` (PK), `karigar_name`, `mobile`, `email`, `address`, bank details, approval status
**Child Tables**: `ret_karikar_items_wastage`, `ret_karigar_stones`, `ret_karigar_charges`, `ret_karigar_kyc`, `ret_karigar_bank_acc_details`, `ret_karigar_products`

### ret_karikar_items_wastage
**Purpose**: Per-karigar, per-product wastage rates
**Key Columns**: Karigar ID, product/design references, wastage percentage, approval status

### ret_karigar_stones
**Purpose**: Per-karigar stone rates
**Key Columns**: Karigar ID, stone type, rates

### ret_karigar_charges
**Purpose**: Per-karigar additional charges
**Key Columns**: Karigar ID, charge type, charge value

### ret_karigar_kyc
**Purpose**: Karigar KYC documents
**Key Columns**: Karigar ID, KYC type, document details

### ret_karigar_products
**Purpose**: Karigar-product mapping (which products each karigar can make)
**Key Columns**: Karigar ID, product ID

### ret_design_master
**Purpose**: Design master (jewellery designs)
**Key Columns**: Design ID, design name, code, images, specifications
**Child Tables**: `ret_design_images`, `ret_design_sizes`, `ret_design_attributes`, `ret_design_weight_range_wc`

### ret_sub_design_master
**Purpose**: Sub-design variants
**Key Columns**: Sub-design ID, parent design, variant details
**Child Tables**: `ret_sub_design_mapping`, `ret_sub_design_mapping_images`

### ret_stone
**Purpose**: Stone master (diamond, ruby, emerald, etc.)
**Key Columns**: Stone ID, name, type, status

### ret_material
**Purpose**: Material master (alloy, rhodium, etc.)
**Key Columns**: Material ID, name, status

### ret_material_rate
**Purpose**: Material rates
**Key Columns**: Material ID, rate, effective date

### ret_section
**Purpose**: Store sections (gold section, silver section, etc.)
**Key Columns**: Section ID, name, status
**Child**: `ret_section_branch` (branch-wise section mapping)

### ret_financial_year
**Purpose**: Financial year definitions
**Key Columns**: Year ID, start date, end date, status (active/closed)

### ret_taxmaster
**Purpose**: Tax definitions (CGST, SGST, IGST)
**Key Columns**: Tax ID, name, percentage

### ret_taxgroupmaster / ret_taxgroupitems
**Purpose**: Tax groups (header + line items)
**Key Columns**: Group ID, name; Line: group ID, tax ID

### ret_charges
**Purpose**: Charge type definitions (making charges, hallmark charges, etc.)
**Key Columns**: Charge ID, name, type

### ret_diamond_rate / ret_selling_diamond_rate
**Purpose**: Diamond purchase and selling rates
**Key Columns**: Quality, carat, rate, effective date

### ret_stone_rate_settings
**Purpose**: Stone rate configuration
**Key Columns**: Stone type, product, min/max rates

### ret_uom
**Purpose**: Unit of measurement
**Key Columns**: UOM ID, name

### ret_size
**Purpose**: Size master (ring sizes, bangle sizes)
**Key Columns**: Size ID, name, value

## Part A-ext: Additional Owned Tables (Round 3)

> Previously undocumented tables found in `dev_structure.sql`. Grouped by entity.

### Product Child Tables
| Table | Purpose | Key Columns |
|---|---|---|
| `ret_product_mapping` | Product-to-design mapping | `pro_id`, `design_id` |
| `ret_product_sub_product` | Product-to-sub-product mapping | `pro_id`, `sub_pro_id` |
| `ret_product_weight` | Product weight ranges | `pro_id`, weight range fields |
| `ret_product_division` | Product division grouping | Division definitions |
| `ret_product_grouping` | Product grouping | Grouping definitions |

### Design Child Tables
| Table | Purpose |
|---|---|
| `ret_design_images` | Design image gallery |
| `ret_design_karigars` | Design-karigar assignment |
| `ret_design_sizes` | Design available sizes |
| `ret_design_stone` | Design stone specifications |
| `ret_design_purity` | Design purity options |
| `ret_design_other_materials` | Design material specs |
| `ret_design_attributes` | Design attribute values |
| `ret_design_mapping_images` | Design mapping images |
| `ret_design_weight_range_wc` | Design weight range + wastage/charges |

### Sub-Design Tables
| Table | Purpose |
|---|---|
| `ret_sub_design_mapping` | Sub-design to design mapping |
| `ret_sub_design_mapping_images` | Sub-design mapping images |

### Karigar Child Tables
| Table | Purpose |
|---|---|
| `ret_karigar_bank_acc_details` | Karigar bank accounts |
| `ret_karigar_advance_payment` | Advance payments to karigars |
| `ret_karigar_import` | Karigar bulk import records |
| `ret_karigar_item_wast_pro_images` | Wastage proof images |
| `ret_karigar_metal_issue` | Metal issued to karigar (header) |
| `ret_karigar_metal_issue_details` | Metal issue line items |
| `ret_karigar_metal_issue_po_details` | Metal issue PO details |
| `ret_karigar_metal_issue_stone_details` | Metal issue stone details |
| `ret_karigar_wallet` | Karigar wallet balance |
| `ret_karigar_wallet_transcation` | Karigar wallet transactions |

### Diamond/Stone Tables
| Table | Purpose |
|---|---|
| `ret_diamond_cent_rates` | Diamond cent-based rates |
| `ret_selling_diamond_cent_rates` | Selling diamond cent rates |
| `ret_stone_type` | Stone type master |
| `ret_stone_discount_master` | Stone discount rules |

### Section/Location Tables
| Table | Purpose |
|---|---|
| `ret_section_branch` | Section-branch mapping |
| `ret_section_nontag_item_log` | Non-tagged item section log |
| `ret_section_tag_status_log` | Tag status change log by section |

### Misc Master Tables
| Table | Purpose |
|---|---|
| `ret_attribute` | Design attribute master |
| `ret_attribute_values` | Attribute value options |
| `ret_collection_master` | Collection master |
| `ret_cover_up` | Cover-up entries |
| `ret_day_closing` | Day closing records |
| `ret_day_closing_log` | Day closing audit log |
| `ret_metal_purity_rate` | Metal-purity rate master |
| `ret_metal_stock` | Metal stock tracking |
| `ret_quality_code` | Quality code master |
| `ret_reorder_settings` | Inventory reorder settings |
| `ret_repair_master` | Repair type master |
| `ret_wastage_discount_master` | Wastage discount rules |
| `ret_old_metal_type` | Old metal type (exchange) |
| `ret_old_metal_category` | Old metal category |
| `ret_old_metal_rate` | Old metal exchange rates |

### Round 8 Additions — Owned Tables Found in Model SQL
| Table | Purpose | Model Lines |
|---|---|---|
| `ret_selling_settings` | Selling wastage/MC settings per product/design/sub-design. Full CRUD. **Key table** — drives billing calculations | L7806-8050 |
| `ret_breakeven_logs` | Breakeven log entries | L8486+ |
| `ret_account_head` | Account head master (GL accounts) | L4084-4189 |
| `ret_bank_deposit` | Bank deposit records with payment details | L5816-6428 |
| `ret_bill_pay_device` | Payment device master (POS terminals, etc.) | L4021-4106 |
| `ret_kyc_master` | KYC document type master (Aadhaar, PAN, etc.) | L7068+ |
| `product_details` | Web catalog product details (separate from `ret_product_master`) | L677, L709, L743 |

### Tagging-Related (Owned by Tagging module, referenced by Catalog)
| Table | Purpose |
|---|---|
| `ret_tag_type_master` | Tag type definitions (owned by Catalog) |
| `ret_tag_collection_mapping` | Tag-collection mapping |
| `ret_tag_collection_mapping_details` | Mapping details |
| `ret_tag_duplicate_copy` | Duplicate tag copies |
| `ret_tag_generation` | Tag generation records |
| `ret_tag_other_metals` | Tag other metal entries |
| `ret_tag_scan` / `ret_tag_scanned` | Tag scan records |

> **Total tables from dev_structure.sql matching catalog patterns**: **121**
> **Primary-owned by this module**: ~62 (includes child/junction tables) — 55 original + 7 added in Round 8
> **Owned by Tagging but referenced here**: ~15
> **Owned by Old Metal Processing**: ~12

## Part B: Referenced Tables (Other Modules)

| Table | Owning Module | How Used |
|---|---|---|
| `ret_taging` | Tagging | Delete guard — checks if entity exists in stock |
| `metal` | Shared/System | Metal types (Gold, Silver, Platinum) |
| `branch` | System | Branch info for multi-branch features |
| `employee` | System | Employee records |
| `company` | System | Company details |
| `chit_settings` / `ret_settings` | Settings | Application configuration |
| `city` / `state` / `country` | System | Location master |
| `payment` / `payment_mode` / `payment_mode_details` | Payment | Payment methods for bank deposits |
| `bank` | System | Bank master |
| `profile` | User/System | Vendor OTP settings — `get_profile_settings()` reads `vendor_approval_otp_req` (L199-203) |
| `sub_category` | Web Catalog | Web catalog sub-categories — `ajax_getProduct()` join |
| `ret_billing` | Billing | Billing records — bank deposit payment queries (L6222-6226) |
| `ret_issue_receipt` | Issue/Receipt | Issue receipt records — bank deposit payment queries (L6276) |
| `payment` / `payment_mode` | Payment | Payment methods for bank deposits |
| `bank` | System | Bank master — `bank_deposit()` karigar bank detail forms (model L6114) |
| `profile` | System/Settings | `get_profile_settings()` model L203 — reads `vendor_approval_otp_req` for karigar OTP approval |
| `dealer` | Purchase/CRM | Referenced in karigar validation queries (karigar linked to dealer accounts) |

> **R9 Update**: `profile` and `dealer` were previously undocumented (count was 9/~10 = 90%). Now 11 referenced tables confirmed. Status: 🔵 Verified.

## DB Verification Queries

### Q1: Complete Category with Purities
```sql
SELECT c.*, GROUP_CONCAT(p.purity) as purities
FROM ret_category c
LEFT JOIN ret_metal_cat_purity mcp ON c.id_ret_category = mcp.id_category
LEFT JOIN ret_purity p ON mcp.id_purity = p.id_purity
WHERE c.id_ret_category = {ID}
GROUP BY c.id_ret_category;
```

### Q2: Complete Product with Sections and Charges
```sql
SELECT pm.*, 
  GROUP_CONCAT(DISTINCT s.name) as sections,
  GROUP_CONCAT(DISTINCT CONCAT(ch.name, ':', pc.charge_value)) as charges
FROM ret_product_master pm
LEFT JOIN ret_product_section ps ON pm.pro_id = ps.pro_id
LEFT JOIN ret_section s ON ps.id_section = s.id
LEFT JOIN ret_product_charges pc ON pm.pro_id = pc.prod_id
LEFT JOIN ret_charges ch ON pc.charge_id = ch.id
WHERE pm.pro_id = {ID}
GROUP BY pm.pro_id;
```

### Q3: Orphan ret_product_section Records
```sql
SELECT ps.* FROM ret_product_section ps
LEFT JOIN ret_product_master pm ON ps.pro_id = pm.pro_id
WHERE pm.pro_id IS NULL;
```

### Q4: Orphan ret_metal_cat_purity Records
```sql
SELECT mcp.* FROM ret_metal_cat_purity mcp
LEFT JOIN ret_category c ON mcp.id_category = c.id_ret_category
WHERE c.id_ret_category IS NULL;
```

### Q5: Karigar with All Child Data
```sql
SELECT k.*, 
  (SELECT COUNT(*) FROM ret_karikar_items_wastage w WHERE w.karigar_id = k.karigar_id) as wastage_count,
  (SELECT COUNT(*) FROM ret_karigar_stones s WHERE s.karigar_id = k.karigar_id) as stone_count,
  (SELECT COUNT(*) FROM ret_karigar_charges c WHERE c.karigar_id = k.karigar_id) as charge_count,
  (SELECT COUNT(*) FROM ret_karigar_products p WHERE p.karigar_id = k.karigar_id) as product_count
FROM ret_karigar k
WHERE k.karigar_id = {ID};
```
