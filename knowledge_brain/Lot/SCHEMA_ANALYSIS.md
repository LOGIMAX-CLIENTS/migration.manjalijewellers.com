# LOT MODULE — SCHEMA ANALYSIS
> Module: Lot | Round 1 | 2026-03-17

---

## Part A: Owned Tables (Written by Lot Module)

### Table: `ret_lot_inwards` — LOT HEADER

| Column (known) | Type | Purpose | Notes |
|---|---|---|---|
| `lot_no` | INT AUTO_INCREMENT | PK | Primary lot identifier |
| `lot_date` | DATETIME | Lot entry date | Set from day close override |
| `lot_type` | INT | Order type | 1=Normal, 2=Customer, 3=Repair |
| `lot_received_at` | INT | Receiving branch ID | FK → branch.id_branch |
| `created_branch` | INT | Creating branch | Same as lot_received_at on create |
| `stock_type` | INT | Tagged vs NonTag | 1=Tagged, 2=Non-Tagged |
| `gold_smith` | INT | Karigar ID | FK → ret_karigar.id_karigar |
| `order_no` | INT | Customer order ID | FK → customerorderdetails |
| `id_category` | INT | Category ID | FK → ret_category |
| `id_purity` | INT | Purity ID | FK → ret_purity |
| `product_division` | INT | Division | FK → ret_product_division |
| `narration` | TEXT | Notes | Freetext |
| `order_branch` | INT | Order branch | FK → branch |
| `grn_id` | INT | GRN reference | FK → ret_grn_entry.grn_id |
| `lot_images` | TEXT | Image filenames | `#`-delimited string |
| `lot_from` | INT | Lot origin | 1=Manual,2=Supplier,7=Merge etc |
| `po_id` | INT | Purchase order ID | FK → ret_purchase_order |
| `id_metal_process` | INT | Old metal process | FK → ret_old_metal_process |
| `is_closed` | TINYINT | Closed flag | 0=Open, 1=Closed |
| `is_lot_split` | TINYINT | Split flag | 0=No, 1=Yes (set on split) |
| `lot_status` | INT | Status | NULL/0=Active, 2=Cancelled |
| `cancel_reason` | TEXT | Cancellation reason | Free text |
| `cancelled_on` | DATETIME | Cancellation timestamp | — |
| `cancelled_by` | INT | Cancelling user | FK → employee or user session |
| `closed_by` | INT | Closing user | From session uid |
| `closed_on` | DATETIME | Close timestamp | From day close |
| `created_on` | DATETIME | Created timestamp | `date("Y-m-d H:i:s")` |
| `created_by` | INT | Creator user ID | From session uid |
| `updated_on` | DATETIME | Update timestamp | Set on update |
| `updated_by` | INT | Updating user | From session uid |

**Risk**: `lot_images` column stores filenames as `img1.jpg#img2.jpg` — no referential integrity, manual string manipulation on remove

---

### Table: `ret_lot_inwards_detail` — LOT LINE ITEMS

| Column (known) | Type | Purpose |
|---|---|---|
| `id_lot_inward_detail` | INT AUTO_INCREMENT | PK |
| `lot_no` | INT | FK → ret_lot_inwards |
| `id_section` | INT | Section ID |
| `lot_product` | INT | Product ID → ret_product_master |
| `lot_id_design` | INT | Design ID → ret_design_master |
| `id_sub_design` | INT | Sub-design ID |
| `lot_id_category` | INT | Category (denormalized) |
| `lot_id_purity` | INT | Purity (denormalized) |
| `gold_smith` | INT | Goldsmith (denormalized from header) |
| `no_of_piece` | DECIMAL | Piece count |
| `gross_wt` | DECIMAL | Gross weight |
| `gross_wt_uom` | INT | UOM for gross_wt |
| `net_wt` | DECIMAL | Net weight |
| `net_wt_uom` | INT | UOM for net_wt |
| `less_wt` | DECIMAL | Less weight (stones etc) |
| `less_wt_uom` | INT | UOM for less_wt |
| `pur_wt` | DECIMAL | Purchase weight |
| `wastage_percentage` | DECIMAL | Wastage % |
| `mc_type` | INT | Making charge type (1=g, 2=pcs) |
| `making_charge` | DECIMAL | Making charge value |
| `current_branch` | INT | Current branch (where item is) |
| `buy_rate` | DECIMAL | Buy rate |
| `sell_rate` | DECIMAL | Sell rate |
| `purchase_touch` | DECIMAL | Touch percentage |
| `calc_type` | INT | Purchase calculation type |
| `rate` | DECIMAL | Rate value |
| `rate_calc_type` | INT | Rate per gram or per pcs |
| `item_cost` | DECIMAL | Total item cost |
| `total_cgst` | DECIMAL | CGST amount |
| `total_sgst` | DECIMAL | SGST amount |
| `total_igst` | DECIMAL | IGST amount |
| `tax_percentage` | DECIMAL | Tax % |
| `tax_group` | INT | Tax group ID |
| `total_tax` | DECIMAL | Total tax |
| `tax_type` | INT | Tax type |
| `size` | VARCHAR | Item size |
| `design_for` | VARCHAR | Design for (gender etc) |
| `precious_stone` | TINYINT | Has precious stones flag |
| `semi_precious_stone` | TINYINT | Has semi-precious flag |
| `normal_stone` | TINYINT | Has normal stone flag |
| `precious_st_pcs` | INT | Precious stone count |
| `precious_st_wt` | DECIMAL | Precious stone weight |
| `precious_st_certif` | TEXT | Precious cert filenames (#-delimited) |
| `precious_st_uom` | INT | UOM for precious |
| `semi_precious_st_pcs` | INT | Semi-precious count |
| `semi_precious_st_wt` | DECIMAL | Semi-precious weight |
| `semiprecious_st_certif` | TEXT | Semi-precious cert filenames |
| `semi_precious_st_uom` | INT | UOM |
| `normal_st_pcs` | INT | Normal stone count |
| `normal_st_wt` | DECIMAL | Normal stone weight |
| `normal_st_certif` | TEXT | Normal cert filenames |
| `normal_st_wt_uom` | INT | UOM |
| `tag_status` | INT | 0=Available for tag |
| `id_lot_inward_detail` (duplicate ref) | — | PK col, used in queries |
| `updated_on` | DATETIME | Update timestamp |
| `updated_by` | INT | Updater |

---

### Table: `ret_lot_inwards_stone_detail` — STONE DETAILS PER LOT ITEM

| Column | Purpose |
|---|---|
| `id_lot_inward_detail` | FK → ret_lot_inwards_detail |
| `stone_id` | FK → ret_stone |
| `stone_pcs` | Stone piece count |
| `stone_wt` | Stone weight |
| `uom_id` | UOM for weight |
| `stone_quality_id` | Quality code |
| `is_apply_in_lwt` | Include in less weight? (note: column name has trailing space) |
| `stone_cal_type` | Stone calc type (note: column name has trailing space) |
| `rate_per_gram` | Rate |
| `price` | Total price |

**Risk**: `is_apply_in_lwt ` and `stone_cal_type  ` have trailing spaces in the insert array (L737–739) — this may cause column-not-found errors depending on DB strictness

---

### Table: `ret_lot_other_items` — OTHER METALS PER LOT ITEM

| Column | Purpose |
|---|---|
| `id_lot_inward_detail` | FK → ret_lot_inwards_detail |
| `other_item_pcs` | Piece count |
| `item_gross_weight` | Gross weight |
| `item_metal` | Metal FK |
| `other_item_purity` | Purity (trailing space in code: `other_item_purity  `) |
| `other_item_cal_type` | Calc type (trailing space: `other_item_cal_type `) |
| `other_item_rate` | Rate per gram |
| `other_item_amount` | Amount |
| `other_mc_type` | Making charge type |
| `other_wastage` | Wastage % |
| `other_mc_value` | Making charge value |

**Risk**: Trailing spaces on column keys at L783–784 may silently fail on insert

---

### Table: `ret_lot_other_charges` — OTHER CHARGES PER LOT ITEM

| Column | Purpose |
|---|---|
| `id_lot_inward_detail` | FK → ret_lot_inwards_detail |
| `charge_id` | FK → ret_charges |
| `calc_type` | Calculation type |
| `charge_value` | Pre-tax charge |
| `item_total_tax` | Tax on charge |
| `tax_percentage` | Tax % |
| `total_charge_value` | Final charge (trailing space in code: `total_charge_value `) |

---

### Table: `ret_lot_merge` — MERGE RELATIONSHIP

| Column | Purpose |
|---|---|
| `lot_no` | New merged lot no → ret_lot_inwards |
| `id_lot_inward_detail` | Source detail being merged → ret_lot_inwards_detail |
| `created_on` | Timestamp |
| `created_by` | User |

---

### Table: `ret_lot_split_details` — SPLIT RECORDS

| Column | Purpose |
|---|---|
| `id_lot_inward_detail` | Source detail being split |
| `id_employee` | Employee who split |
| `id_category` | Category |
| `id_purity` | Purity |
| `id_product` | Product |
| `split_pcs` | Pieces split |
| `split_grs_wt` | Gross weight split |
| `split_net_wt` | Net weight split |
| `split_stn_pcs` | Stone pieces split |
| `split_stn_wt` | Stone weight split |
| `split_dia_pcs` | Diamond pieces split |
| `split_dia_wt` | Diamond weight split |
| `created_on` | Timestamp |
| `created_by` | User |

---

## Part B: Referenced Tables (Read-Only by Lot Module)

| Table | Source Module | Key Columns Used |
|---|---|---|
| `ret_taging` | Tagging | `tag_lot_id`, `tag_status`, `tag_id`, `piece`, `gross_wt`, `net_wt`, `product_id`, `design_id`, `purity`, `current_branch` |
| `ret_taging_stone` | Tagging | `tag_id`, `stone_id`, `wt` |
| `ret_product_master` | Catalog | `pro_id`, `product_name`, `product_short_code`, `sales_mode`, `calculation_based_on`, `stock_type`, `cat_id` |
| `ret_design_master` | Catalog | `design_no`, `design_name`, `design_code` |
| `ret_sub_design_master` | Catalog | `id_sub_design`, `sub_design_name` |
| `ret_category` | Catalog | `id_ret_category`, `name`, `id_metal`, `cat_code` |
| `ret_purity` | Catalog | `id_purity`, `purity` |
| `ret_karigar` | Karigar | `id_karigar`, `firstname`, `code_karigar`, `gst_number`, `address*`, `contactno1` |
| `ret_stone` | Catalog | `stone_id`, `stone_type`, `stone_name` |
| `ret_uom` | Catalog | `uom_id`, `uom_short_code`, `divided_by_value` |
| `ret_section` | Catalog | `id_section` |
| `ret_charges` | Catalog | `id_charge`, `name_charge` |
| `ret_product_division` | Catalog | `id`, `status` |
| `ret_product_mapping` | Catalog | `pro_id`, `id_design` |
| `ret_settings` | Config | `name`, `value` |
| `ret_purchase_order` | Purchase | `po_id`, `po_ref_no` |
| `ret_grn_entry` | GRN | `grn_id`, `grn_ref_no` |
| `ret_old_metal_process` | Old Metal | `id_old_metal_process`, `process_no` |
| `ret_nontag_receipt` | NonTag | `id_lot_inward_detail`, `pcs`, `grs_wt`, `net_wt` |
| `customerorder` | Orders | `id_customerorder`, `order_no`, `order_from` |
| `customerorderdetails` | Orders | `id_orderdetails`, `id_customerorder`, `id_product`, `design_no`, `wast_percent`, `mc`, `weight`, `totalitems`, `id_purity`, `size` |
| `joborder` | Orders | `id_order`, `id_vendor` |
| `branch` | Org | `id_branch`, `name`, `short_name`, `is_ho` |
| `employee` | HR | `id_employee`, `firstname`, `emp_code` |
| `profile` | Auth | `id_profile`, all profile settings |
| `metal` | Catalog | `id_metal`, `tgrp_id` |
| `ret_taxgroupitems` | Tax | `tgi_tgrpcode`, `tgi_taxcode`, `tgi_calculation` |
| `ret_taxmaster` | Tax | `tax_id`, `tax_percentage` |
| `country`, `state`, `city` | Geo | Names for print |

---

## Key Risks Identified (Updated Rounds 1–11)

| # | Risk | Bug ID | Severity |
|---|---|---|---|
| 1 | Trailing spaces in column names in insert arrays (stone, other_items, other_charges, lot_complete) | R-LOT-008/023 | 🔴 Critical |
| 2 | `lot_images` field stores `#`-concatenated filenames — no atomic storage or referential integrity | — | 🟡 Low |
| 3 | No FK constraints assumed — child tables not cleaned on parent delete (`ret_lot_inwards` delete orphans detail/stone/other tables) | R-LOT-012 | 🔴 Critical |
| 4 | `ret_nontag_item` incremented on lot save but **NOT decremented on lot delete** — stock count wrong after deletion | R-LOT-012 | 🔴 Critical (cascading) |
| 5 | `ret_tag_model.php` has 324+ queries into lot tables — orphan rows from incomplete deletes cause phantom tagging stock | R-LOT-012 | 🔴 Critical (cascading) |
| 6 | Old column names (`lot_sub_product`, `no_of_tags`, `normal_stn_certificate`) referenced in `ret_tag_model` L977 — likely returning NULL silently | R-LOT-044 | 🟠 Medium |
| 7 | `get_lot_tag_details()` groups by `branch_name` string — if two branches share a name, data collides | — | 🟡 Low |
| 8 | `insertData()` / `updateData()` fire `SHOW COLUMNS` on every call — 15+ extra DB queries per lot save | R-LOT-035 | 🟡 Low |
| 9 | `getLotNoForMerge()` / `getLotNoForSplit()` return undefined variable when no lots match — PHP crash | R-LOT-038 | 🟠 Medium |
| 10 | `get_customer_lot_details()` returns undefined `$returnData` on empty lot — crash on print | R-LOT-041 | 🟠 Medium |
| 11 | `getLotidsforSplit()` missing `GROUP BY` — duplicate lot_no in split dropdown | R-LOT-039 | 🟠 Medium |
| 12 | `ret_lot_other_items` / `ret_lot_other_charges` not cleaned on lot edit — stale charge rows accumulate | R-LOT-011 | 🟡 Low |
| 13 | `debug echo last_query()` in production on merge and split failure paths | R-LOT-003/004 | 🔴 Critical |
| 14 | `lot_inward/delete` uses GET method — CSRF vulnerability | R-LOT-001 | 🔴 Critical |
| 15 | MC type label inverted in `get_lotInward_detail()` — mc_type=2 shows 'PER GRAM' (should be 'PER PCS') | R-LOT-020 | 🟡 Low |
