# Estimation Module — Schema Analysis (Round 7)

> **Module**: Estimation
> **Date Built**: 2026-02-18 (Round 7)
> **Source**: `dev_structure.sql` + `ret_estimation_model.php` + `admin_ret_estimation.php`
> **Purpose**: Full schema inventory for all 85 tables (13 owned + 72 referenced) used by the estimation module

---

## Part A — Estimation-Owned Tables (13)

Tables whose data lifecycle is controlled by the estimation module (INSERT/UPDATE/DELETE).

| # | Table | Engine | PK | Auto-Inc | Columns | Indexes | FK Constraints |
|---|---|---|---|---|---|---|---|
| 1 | `ret_estimation` | InnoDB ✅ | `estimation_id` | ✅ 5851 | 38 | 10 | None (soft) |
| 2 | `ret_estimation_items` | InnoDB ✅ | `est_item_id` | ✅ 6848 | 52 | 16 | None (soft) |
| 3 | `ret_estimation_old_metal_sale_details` | InnoDB ✅ | `old_metal_sale_id` | ✅ 2333 | 24 | 9 | None (soft) |
| 4 | `ret_estimation_item_stones` | InnoDB ✅ | `est_item_stone_id` | ✅ 2765 | 12 | 5 | None (soft) |
| 5 | `ret_estimation_item_other_materials` | InnoDB ✅ | `est_other_material_id` | ✅ — | 6 | 0 ⚠️ | None (soft) |
| 6 | `ret_estimation_other_charges` | InnoDB ✅ | `id_est_charge` | ✅ 3535 | 4 | 2 | None (soft) |
| 7 | `ret_estimation_other_inventory_issue` | InnoDB ✅ | `id_inv_issue` | ✅ 183 | 4 | 0 ⚠️ | None (soft) |
| 8 | `ret_est_chit_utilization` | InnoDB ✅ | UNIQUE(`chit_ut_id`) | ✅ 411 | 10 | 2 | None (soft) |
| 9 | `ret_est_gift_voucher_details` | InnoDB ✅ | — ❌ | ❌ | 5 | 0 ⚠️ | None (soft) |
| 10 | `ret_est_other_metals` | **MyISAM** ❌ | `est_other_itm_id` | ✅ 87 | 12 | 5 | None (soft) |
| 11 | `ret_est_sales_return_utilization` | InnoDB ✅ | `sr_ut_id` | ✅ — | 5 | 0 ⚠️ | None (soft) |
| 12 | `ret_est_tag_merge` | InnoDB ✅ | `id_est_tag_merge` | ✅ 25 | 3 | 0 ⚠️ | None (soft) |
| 13 | `ret_esti_old_metal_stone_details` | InnoDB ✅ | `est_old_metal_stone_id` | ✅ 177 | 12 | 0 ⚠️ | None (soft) |

---

## Part B — Referenced External Tables (72)

Tables read (SELECT/JOIN) or written to by the estimation module but owned by other modules.

### B1. Core / Master Data (14 tables)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 14 | `customer` | InnoDB | 117 | 1285 | Estimation linked via `cus_id` — also INSERT (new customer on-the-fly) |
| 15 | `address` | InnoDB | 36 | 73 | Customer address — INSERT with new customer |
| 16 | `company` | InnoDB | 50 | 907 | Company settings — READ for branding/tax config |
| 17 | `branch` | InnoDB | 58 | 332 | Branch config — READ for rate, location |
| 18 | `employee` | InnoDB | 52 | 2505 | Salesperson lookup |
| 19 | `employee_settings` | InnoDB | 26 | 2589 | Employee access/permission — READ |
| 20 | `country` | InnoDB | 16 | 1018 | Dropdown — READ |
| 21 | `state` | InnoDB | 14 | 14394 | Dropdown — READ |
| 22 | `city` | InnoDB | 13 | 884 | Dropdown — READ |
| 23 | `village` | InnoDB | 14 | 14799 | Dropdown — READ |
| 24 | `village_zone` | InnoDB | 9 | 14823 | Zone lookup — READ |
| 25 | `ret_financial_year` | InnoDB | 15 | 7479 | Bill numbering — READ |
| 26 | `bill_no_format` | InnoDB | 6 | 316 | Bill number format — READ |
| 27 | `ret_settings` | InnoDB | 17 | 11796 | Module-level settings — READ |

### B2. Product / Tag / Inventory (19 tables)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 28 | `ret_taging` | InnoDB | 118 | 12575 | **Core lookup** — tag item details, weight, price |
| 29 | `ret_taging_stone` | InnoDB | 23 | 12809 | Stone details within a tag — READ |
| 30 | `ret_taging_charges` | InnoDB | 8 | 12703 | Tag-level charges — READ |
| 31 | `ret_taging_images` | InnoDB | 8 | 12741 | Tag images — READ |
| 32 | `ret_tag_other_metals` | **MyISAM** ❌ | 20 | 12457 | Other metals within tag — READ |
| 33 | `ret_tag_collection_mapping` | InnoDB | 18 | 12360 | Collection linkage — READ |
| 34 | `ret_tag_collection_mapping_details` | InnoDB | 7 | 12388 | Collection items — READ |
| 35 | `ret_product_master` | InnoDB | 67 | 10612 | Product definitions — READ |
| 36 | `ret_product_mapping` | **MyISAM** ❌ | 7 | 10595 | Product-to-section mapping — READ |
| 37 | `ret_category` | InnoDB | 28 | 6530 | Metal categories (Gold/Silver/Platinum) — READ |
| 38 | `ret_nontag_item` | InnoDB | 17 | 9079 | Non-tag item catalog — READ |
| 39 | `ret_section` | **MyISAM** ❌ | 11 | 11517 | Section (department) master — READ |
| 40 | `ret_section_branch` | InnoDB | 9 | 11538 | Section-branch mapping — READ |
| 41 | `ret_design_master` | InnoDB | 37 | 6896 | Design catalog — READ |
| 42 | `ret_design_weight_range_wc` | **MyISAM** ❌ | 17 | 7001 | Weight-range based wastage/MC — READ |
| 43 | `ret_sub_design_master` | InnoDB | 13 | 12142 | Sub-design catalog — READ |
| 44 | `ret_lot_inwards` | InnoDB | 34 | 8593 | Lot receipt headers — READ |
| 45 | `ret_lot_inwards_detail` | InnoDB | 68 | 8637 | Lot receipt items — READ |
| 46 | `ret_partlysold` | InnoDB | 22 | 10146 | Partially sold tag tracking — INSERT/UPDATE |

### B3. Metal / Purity / Rates (8 tables)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 47 | `metal` | InnoDB | 13 | 4007 | Metal master (Gold/Silver/Platinum) — READ |
| 48 | `metal_rates` | InnoDB | 30 | 4030 | Daily metal rates — READ |
| 49 | `branch_rate` | InnoDB | 10 | 400 | Branch-specific rate overrides — READ |
| 50 | `ret_purity` | InnoDB | 14 | 11288 | Purity master (22K, 18K, 916, etc.) — READ |
| 51 | `ret_metal_cat_purity` | InnoDB | 9 | 8998 | Category-purity mapping — READ |
| 52 | `ret_metal_purity_rate` | InnoDB | 15 | 9017 | Purity-specific rates — READ |
| 53 | `ret_uom` | InnoDB | 17 | 12999 | Unit of measure — READ |
| 54 | `ret_size` | InnoDB | 11 | 11843 | Size master — READ |

### B4. Stone / Material (3 tables)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 55 | `ret_stone` | InnoDB | 19 | 11992 | Stone master — READ |
| 56 | `ret_stone_discount_master` | InnoDB | 10 | 12021 | Stone discount rules — READ |
| 57 | `ret_material` | InnoDB | 11 | 8895 | Other material master — READ |

### B5. Charges / Tax / Wastage / Selling Settings (8 tables)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 58 | `ret_charges` | InnoDB | 13 | 6568 | Charge master — READ |
| 59 | `ret_taxmaster` | InnoDB | 15 | 12952 | Tax definitions — READ |
| 60 | `ret_taxgroupmaster` | InnoDB | 13 | 12929 | Tax group headers — READ |
| 61 | `ret_taxgroupitems` | InnoDB | 16 | 12903 | Tax group items — READ |
| 62 | `ret_wastage_discount_master` | InnoDB | 10 | 13355 | Wastage discount slabs — READ |
| 63 | `ret_selling_settings` | InnoDB | 23 | 11657 | Per-product selling rules — READ |
| 64 | `ret_karigar` | InnoDB | 52 | 8086 | Artisan/karigar master — READ |
| 65 | `ret_design_masterdes` | InnoDB | — | — | Design descriptions — READ (referenced in queries, verify in SQL) |

### B6. Old Metal (4 tables)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 66 | `ret_old_metal_type` | InnoDB | 10 | 9727 | Old metal type master — READ |
| 67 | `ret_old_metal_category` | InnoDB | 14 | 9201 | Old metal category — READ |
| 68 | `ret_old_metal_rate` | InnoDB | 12 | 9589 | Old metal rate master — READ |

### B7. Billing / Payment / Advance (8 tables)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 69 | `ret_billing` | InnoDB | 112 | 5855 | Main billing table — READ (billing conversion) |
| 70 | `ret_bill_details` | InnoDB | 74 | 5546 | Billing line items — READ |
| 71 | `ret_bill_old_metal_sale_details` | InnoDB | 43 | 5678 | Old metal in billing — READ |
| 72 | `ret_bill_return_details` | **MyISAM** ❌ | 13 | 5797 | Sales return items — READ |
| 73 | `ret_billing_advance` | InnoDB | 30 | 5977 | Advance adjustments in billing — READ |
| 74 | `ret_issue_receipt` | InnoDB | 59 | 8001 | Cash receipts/advances — READ + UPDATE |
| 75 | `ret_issue_credit_collection_details` | InnoDB | 7 | 7916 | Credit collection details — READ |
| 76 | `payment` | InnoDB | 140 | 4442 | Payment gateway records — READ |

### B8. Advance / Credit (3 tables)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 77 | `ret_advance_utilized` | InnoDB | 16 | 5419 | Advance utilization — INSERT |
| 78 | `ret_advance_refund` | InnoDB | 8 | 5383 | Advance refund — READ |
| 79 | `ret_advance_transfer` | InnoDB | 8 | 5401 | Advance transfer — READ |

### B9. Scheme / Chit (4 tables)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 80 | `scheme` | InnoDB | 203 | 13595 | Chit/savings scheme master — READ |
| 81 | `scheme_account` | InnoDB | 123 | 13808 | Customer scheme accounts — READ + UPDATE |
| 82 | `chit_settings` | InnoDB | 129 | 526 | Chit scheme config — READ |
| 83 | `ret_day_closing` | InnoDB | 16 | 6782 | Day close status — READ (blocked if closed) |

### B10. Customer Order (2 tables)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 84 | `customerorder` | InnoDB | 50 | 1734 | Customer order headers — READ + UPDATE |
| 85 | `customerorderdetails` | InnoDB | 95 | 1794 | Order line items — READ |

### B11. Branch Transfer (1 table)

| # | Table | Engine | Columns | SQL Line | Relationship to Estimation |
|---|---|---|---|---|---|
| 86 | `ret_branch_transfer` | InnoDB | 49 | 6237 | Branch transfer tracking — READ |

---

## 🚨 Schema Anomalies Found

### CRITICAL (P0/P1) — Estimation-Owned Tables

| # | Bug ID | Table | Issue | Risk | Recommendation |
|---|---|---|---|---|---|
| 1 | **SCH-001** | `ret_est_other_metals` | **MyISAM engine** — used inside `trans_begin()`/`trans_commit()` blocks | Transactions silently ignored. Data corruption on save failure | `ALTER TABLE ret_est_other_metals ENGINE=InnoDB;` |
| 2 | **SCH-002** | `ret_est_gift_voucher_details` | **No PRIMARY KEY** — `gift_voucher_id` is `int NOT NULL` but not PK, not AUTO_INCREMENT | No unique row identifier. DELETE/UPDATE can affect wrong rows. No InnoDB row-level locking | `ALTER TABLE ret_est_gift_voucher_details ADD PRIMARY KEY (gift_voucher_id), MODIFY gift_voucher_id INT NOT NULL AUTO_INCREMENT;` |
| 3 | **SCH-003** | `ret_estimation` | `goldrate_18ct` is `int` while `goldrate_22ct`, `silverrate_1gm`, `goldrate_9ct`, `goldrate_14ct` are all `decimal(10,2)` | Silent truncation — 18ct gold rate ₹4567.50 saved as ₹4567. Financial calculation error | `ALTER TABLE ret_estimation MODIFY goldrate_18ct decimal(10,2) DEFAULT NULL;` |
| 4 | **SCH-004** | `ret_estimation` | `disc_per` is `int` while monetary amounts are `decimal(10,2)` | Discount percentage 7.5% saved as 7%. Financial loss on every fractional discount | `ALTER TABLE ret_estimation MODIFY disc_per decimal(5,2) DEFAULT NULL;` |
| 5 | **SCH-005** | `ret_estimation` | `bulk_was_disc_per` is `int` — same truncation issue | Bulk wastage discount percentage truncated | `ALTER TABLE ret_estimation MODIFY bulk_was_disc_per decimal(5,2) DEFAULT NULL;` |
| 6 | **SCH-006** | `ret_estimation_items` | `discount` is `decimal(10,0)` — zero decimal places | Item-level discount ₹250.75 saved as ₹251. Penny discrepancies | `ALTER TABLE ret_estimation_items MODIFY discount decimal(10,2) DEFAULT NULL;` |
| 7 | **SCH-007** | `ret_estimation_items` | `max_va_per`, `max_VA`, `max_mc` are all `int` | VA percentage/MC values truncated if fractional | `ALTER TABLE ret_estimation_items MODIFY max_va_per decimal(5,2) DEFAULT NULL, MODIFY max_VA decimal(10,2) DEFAULT NULL, MODIFY max_mc decimal(10,2) DEFAULT NULL;` |
| 8 | **SCH-008** | `ret_estimation_items` | `act_wast_per` is `int` | Actual wastage percentage truncated | `ALTER TABLE ret_estimation_items MODIFY act_wast_per decimal(5,2) NOT NULL DEFAULT '0.00';` |

### CRITICAL (P0/P1) — Referenced External Tables

| # | Bug ID | Table | Issue | Risk | Recommendation |
|---|---|---|---|---|---|
| 9 | **SCH-023** | `ret_bill_return_details` | **MyISAM engine** — sales return data used in estimation credit lookups | Transactions ignored. If estimation save + SR adjustment happens in same `trans_begin()` block, SR data is unprotected | `ALTER TABLE ret_bill_return_details ENGINE=InnoDB;` |
| 10 | **SCH-024** | `ret_tag_other_metals` | **MyISAM engine** — tag-level other metals read during tag load | No transaction safety when tag data is read and estimation items are built in same transaction | `ALTER TABLE ret_tag_other_metals ENGINE=InnoDB;` |
| 11 | **SCH-025** | `ret_product_mapping` | **MyISAM engine** — product-to-section mapping used in lookups | No row-level locking during concurrent product lookups | `ALTER TABLE ret_product_mapping ENGINE=InnoDB;` |
| 12 | **SCH-026** | `ret_section` | **MyISAM engine** — section/department master used in estimation display | MyISAM has no crash recovery — corrupts on unexpected shutdown | `ALTER TABLE ret_section ENGINE=InnoDB;` |
| 13 | **SCH-027** | `ret_design_weight_range_wc` | **MyISAM engine** — wastage/MC calculation lookup table | Read during price calculation — MyISAM table lock blocks concurrent estimations | `ALTER TABLE ret_design_weight_range_wc ENGINE=InnoDB;` |

### MODERATE (P2)

| # | Bug ID | Table | Issue | Risk | Recommendation |
|---|---|---|---|---|---|
| 14 | **SCH-009** | `ret_estimation_item_other_materials` | **Zero non-PK indexes** — `est_id`, `est_item_id`, `material_id` have no indexes | Slow JOINs on edit/load when retrieving materials for an estimation item | Add indexes on `est_id`, `est_item_id` |
| 15 | **SCH-010** | `ret_estimation_other_inventory_issue` | **Zero non-PK indexes** — `esti_id` not indexed | Slow lookup when loading estimation inventory issues | Add index on `esti_id` |
| 16 | **SCH-011** | `ret_est_sales_return_utilization` | **Zero non-PK indexes** — `est_id`, `bill_id` not indexed | Slow JOINs when checking SR utilization for an estimation | Add indexes on `est_id`, `bill_id` |
| 17 | **SCH-012** | `ret_est_tag_merge` | **Zero non-PK indexes** — `est_item_id`, `ref_est_item_id` not indexed | Slow merge/split lookups | Add indexes on `est_item_id`, `ref_est_item_id` |
| 18 | **SCH-013** | `ret_esti_old_metal_stone_details` | **Zero non-PK indexes** — `est_id`, `est_old_metal_sale_id` not indexed | Slow JOINs when loading old metal stone details | Add indexes on `est_id`, `est_old_metal_sale_id` |
| 19 | **SCH-014** | `ret_est_gift_voucher_details` | **Zero indexes** — no indexes at all, not even on `est_id` | Full table scan on every estimation gift voucher lookup | Add indexes on `est_id`, `voucher_no` |
| 20 | **SCH-015** | `ret_est_chit_utilization` | PK uses `UNIQUE KEY` instead of `PRIMARY KEY` | Functionally equivalent but non-standard. InnoDB uses first UNIQUE NOT NULL as implicit PK | `ALTER TABLE ret_est_chit_utilization DROP INDEX chit_ut_id, ADD PRIMARY KEY (chit_ut_id);` |
| 21 | **SCH-016** | `ret_estimation_items` | `wastage_percent` is `decimal(10,2)` but `wastage_slab_id` is `int` | Wastage can be slab-based or manual — OK, but no index on `wastage_slab_id` for slab lookup | Add index on `wastage_slab_id` |

### LOW (P3)

| # | Bug ID | Table | Issue | Risk |
|---|---|---|---|---|
| 22 | **SCH-017** | `ret_estimation` | Charset is `latin1` — not `utf8mb4` | Cannot store non-Latin customer names or Unicode in remarks |
| 23 | **SCH-018** | `ret_estimation_items` | Charset is `latin1` | Same as SCH-017 |
| 24 | **SCH-019** | `ret_estimation_old_metal_sale_details` | Charset is `latin1` | Same — `remark` column is `text` but can't store Unicode |
| 25 | **SCH-020** | `ret_est_other_metals` | Charset is `latin1` | Same |
| 26 | **SCH-021** | Various tables | No FK constraints anywhere — all relationships are soft (app-enforced) | Orphan records possible if DELETE misses child tables |
| 27 | **SCH-022** | `ret_estimation_items` | Heavy indexing — 16 indexes on one table | Insert/update overhead. Some indexes may be redundant |
| 28 | **SCH-028** | `branch` | Charset is `latin1` — 58 columns including branch name | Non-Latin branch names break |
| 29 | **SCH-029** | `metal_rates` | Charset is `latin1` — 30 columns of rate data | Low risk but inconsistent with newer tables |
| 30 | **SCH-030** | `ret_billing` | Charset is `latin1` — 112 columns, core billing table | Billing remarks can't store Unicode |
| 31 | **SCH-031** | `scheme` | 203 columns in a single table | Extreme table width — maintenance concern |

---

## Detailed Column Maps

### `ret_estimation` (38 columns)

| Column | Type | Nullable | Default | Index | Comment |
|---|---|---|---|---|---|
| `estimation_id` | `int` | NO | AUTO_INCREMENT | **PK** | |
| `fin_year_code` | `varchar(15)` | YES | NULL | KEY | |
| `esti_no` | `int` | NO | — | UNIQUE (composite) | Combined with `est_date` + `id_branch` |
| `esti_for` | `tinyint(1)` | NO | 1 | KEY | 1=Customer, 2=Branch Transfer, 3=Company |
| `estimation_datetime` | `datetime` | YES | NULL | KEY | |
| `est_date` | `date` | YES | NULL | UNIQUE (composite) | |
| `cus_id` | `int unsigned` | YES | NULL | KEY | |
| `old_cus_id` | `int unsigned` | YES | NULL | — | CRM/Retail merge relic |
| `mobile` | `varchar(45)` | YES | NULL | — | CRM/Retail merge relic |
| `created_by` | `int` | YES | NULL | — | |
| `created_time` | `datetime` | YES | NULL | — | |
| `updated_by` | `int` | YES | NULL | — | |
| `updated_time` | `datetime` | YES | NULL | — | |
| `approved_by` | `int` | YES | NULL | — | |
| `approved_time` | `datetime` | YES | NULL | — | |
| `has_converted_order` | `int` | YES | NULL | KEY | 0=No, 1=Yes |
| `discount` | `decimal(10,2)` | YES | NULL | — | |
| `gift_voucher_amt` | `decimal(10,2)` | YES | NULL | — | |
| `total_cost` | `decimal(10,2)` | YES | NULL | — | |
| `id_branch` | `int unsigned` | YES | NULL | KEY + UNIQUE | |
| `id_other_item` | `int` | YES | NULL | — | |
| `no_of_pcs` | `int` | NO | 0 | — | |
| `added_through` | `tinyint(1)` | NO | 1 | KEY | 1=Admin, 2=Estimation App |
| `is_eda` | `int` | NO | 0 | KEY | 0=No, 1=Needs discount approval |
| `is_eda_approved` | `int` | NO | 0 | KEY | 0=Not, 1=Approved, 2=Rejected |
| `estimate_final_amt` | `decimal(10,2) unsigned` | YES | NULL | — | Final after EDA discount |
| `goldrate_22ct` | `decimal(10,2)` | NO | 0.00 | — | |
| `goldrate_18ct` | `int` ⚠️ | YES | NULL | — | **Type mismatch — should be decimal** |
| `silverrate_1gm` | `decimal(10,2)` | NO | 0.00 | — | |
| `goldrate_9ct` | `decimal(10,2)` | YES | NULL | — | |
| `goldrate_14ct` | `decimal(10,2)` | YES | NULL | — | |
| `form_secret` | `varchar(100)` | YES | NULL | UNIQUE | CSRF token |
| `estbillid` | `int` | YES | NULL | — | Link to billing |
| `created_through` | `tinyint(1)` | NO | 0 | — | 1=Web, 2=App |
| `disc_per` | `int` ⚠️ | YES | NULL | — | **Truncates fractional %** |
| `bulk_was_disc_per` | `int` ⚠️ | YES | NULL | — | **Truncates fractional %** |
| `manual_rate` | `tinyint` | NO | 0 | — | 0=Unchecked, 1=Checked |

### `ret_estimation_items` (52 columns)

| Column | Type | Nullable | Default | Index | Comment |
|---|---|---|---|---|---|
| `est_item_id` | `int` | NO | AUTO_INCREMENT | **PK** | |
| `esti_id` | `int` | YES | NULL | KEY | FK to `ret_estimation.estimation_id` |
| `item_type` | `int` | YES | NULL | KEY | 0=Tag, 1=Catalog, 2=Custom |
| `product_id` | `int` | YES | NULL | KEY | |
| `tag_id` | `int` | YES | NULL | KEY | FK to `ret_taging.tag_id` |
| `item_emp_id` | `int` | YES | NULL | — | Salesperson |
| `tag_remark` | `varchar(50)` | YES | NULL | — | |
| `search_field` | `tinyint(1)` | NO | 1 | — | 1=Tag Code, 2=Old Tag Id |
| `orderno` | `varchar(20)` | YES | NULL | — | |
| `id_orderdetails` | `int` | YES | NULL | KEY | FK to order details |
| `design_id` | `int` | YES | NULL | KEY | |
| `id_sub_design` | `int` | YES | NULL | KEY | |
| `id_section` | `int` | YES | NULL | — ⚠️ | **No index — JOINed in queries** |
| `purity` | `int` | YES | NULL | KEY | |
| `size` | `decimal(10,2)` | YES | NULL | — | |
| `uom` | `int` | YES | NULL | — | |
| `piece` | `int` | YES | NULL | — | |
| `less_wt` | `decimal(12,3)` | YES | NULL | — | |
| `net_wt` | `decimal(12,3)` | YES | NULL | — | |
| `gross_wt` | `decimal(12,3)` | YES | NULL | — | |
| `calculation_based_on` | `tinyint(1)` | YES | 2 | KEY | 0=MC&Wast Gross, 1=MC&Wast Net, 2=MC Gross/Wast Net, 3=Fixed, 4=Fixed by Wt |
| `wastage_percent` | `decimal(10,2)` | YES | NULL | — | |
| `max_va_per` | `int` ⚠️ | YES | NULL | — | **Truncates fractional VA%** |
| `max_VA` | `int` ⚠️ | YES | NULL | — | **Truncates fractional VA** |
| `max_mc` | `int` ⚠️ | YES | NULL | — | **Truncates fractional MC** |
| `mc_value` | `decimal(10,2)` | YES | NULL | — | |
| `discount` | `decimal(10,0)` ⚠️ | YES | NULL | — | **Zero decimals — truncates** |
| `item_cost` | `decimal(10,2)` | YES | NULL | — | |
| `est_rate_per_grm` | `decimal(10,2)` | NO | 0.00 | — | |
| `is_partial` | `tinyint(1)` | NO | 0 | KEY | |
| `mc_type` | `int` | YES | NULL | KEY | 1=Per Pc, 2=Per Grm, 3=% on price |
| `tax_group_id` | `int` | NO | 1 | KEY | |
| `item_total_tax` | `decimal(10,2)` | YES | NULL | — | |
| `market_rate_cost` | `decimal(10,2)` | YES | NULL | — | |
| `market_rate_tax` | `decimal(10,2)` | YES | NULL | — | |
| `is_non_tag` | `tinyint(1)` | NO | 0 | KEY | 0=Tagged, 1=Non-tagged |
| `lot_no` | `int` | YES | NULL | — | |
| `purchase_status` | `tinyint(1)` | NO | 0 | KEY | 1=Purchased, 2=Returned |
| `bil_detail_id` | `int` | YES | NULL | KEY | FK to billing detail |
| `id_division` | `int` | NO | 0 | — | |
| `id_collecion_maping_det` | `int` | YES | NULL | — | |
| `esti_purchase_cost` | `decimal(12,2)` | YES | NULL | — | |
| `istag_merged` | `tinyint(1)` | NO | 0 | — | 0=No, 1=Yes |
| `isTagsplitted` | `tinyint(1)` | NO | 0 | — | Weight scheme split |
| `is_split_row` | `int` | NO | 0 | — | 0=Normal, 1=Split |
| `wastage_slab_id` | `int` | YES | NULL | — ⚠️ | **No index — used in slab lookups** |
| `tag_blk_disc` | `tinyint` | NO | 0 | — | 0=Not applied, 1=Applied |
| `act_wast_per` | `int` ⚠️ | NO | 0 | — | **Truncates fractional %** |
| `stone_amount` | `decimal(10,2)` | NO | 0.00 | — | |
| `diamond_amount` | `decimal(10,2)` | NO | 0.00 | — | |
| `wast_slab_value` | `decimal(10,2)` | YES | NULL | — | |

### `ret_estimation_old_metal_sale_details` (24 columns)

| Column | Type | Nullable | Default | Index | Comment |
|---|---|---|---|---|---|
| `old_metal_sale_id` | `int` | NO | AUTO_INCREMENT | **PK** | |
| `est_id` | `int` | YES | NULL | KEY | FK to estimation |
| `id_old_metal_type` | `int` | YES | NULL | KEY | |
| `id_old_metal_category` | `int` | YES | NULL | KEY | |
| `id_category` | `int` | YES | NULL | KEY | Metal type (Gold/Silver) |
| `type` | `tinyint(1)` | NO | 1 | KEY | 1=Melting, 2=Re-tag |
| `item_type` | `int` | YES | NULL | KEY | 1=Ornament, 2=Coin, 3=Bar |
| `piece` | `int unsigned` | NO | 1 | — | |
| `gross_wt` | `decimal(14,3) unsigned` | YES | NULL | — | |
| `net_wt` | `decimal(14,3) unsigned` | YES | NULL | — | |
| `stone_wt` | `decimal(14,3) unsigned` | YES | NULL | — | |
| `dust_wt` | `decimal(14,3)` | YES | NULL | — | |
| `touch` | `decimal(10,2)` | NO | 100.00 | — | |
| `purity` | `decimal(10,2) unsigned` | YES | NULL | — | |
| `wastage_percent` | `decimal(10,2)` | YES | NULL | — | |
| `wastage_wt` | `decimal(14,3)` | YES | NULL | — | |
| `rate_per_gram` | `decimal(10,2) unsigned` | YES | NULL | — | |
| `amount` | `decimal(10,2) unsigned` | YES | NULL | — | |
| `purpose` | `tinyint(1)` | NO | 1 | — ⚠️ | 1=Cash, 2=Exchange. **No index** |
| `bill_id` | `int` | YES | NULL | KEY | FK to billing |
| `purchase_status` | `tinyint(1)` | NO | 0 | KEY | 0=No, 1=Purchased, 2=Deposited, 3=Advance |
| `tally_guid` | `varchar(100)` | YES | NULL | — | |
| `istransfered` | `int` | NO | 0 | KEY | |
| `tally_updated_on` | `datetime` | YES | NULL | — | |
| `remark` | `text` | YES | NULL | — | |
| `old_metal_prod_id` | `int` | YES | NULL | — | |
| `old_stone_set` | `tinyint(1)` | NO | 1 | — | |

### Child Tables (Compact)

#### `ret_estimation_item_stones` (12 columns)
| Key Columns | Type | Notes |
|---|---|---|
| `est_item_stone_id` | `int` PK AUTO_INCREMENT | |
| `est_id` | `int` KEY | FK to estimation |
| `est_item_id` | `int` KEY | FK to item |
| `stone_id` | `int` KEY | |
| `wt` | `decimal(12,4)` | 4 decimal places for stones |
| `price` | `decimal(10,2)` | |
| `is_apply_in_lwt` | `tinyint(1)` KEY | 1=Yes, 0=No |
| `stone_cal_type` | `tinyint` KEY | 1=By weight, 2=By pcs |
| `quality_id` | `int` | Diamond quality reference |
| `max_stn_amt` | `decimal(10,2)` | Amount before discount |

#### `ret_estimation_item_other_materials` (6 columns) ⚠️ NO INDEXES
| Key Columns | Type | Notes |
|---|---|---|
| `est_other_material_id` | `int` PK AUTO_INCREMENT | |
| `est_id` | `int` | **No index** |
| `est_item_id` | `int` | **No index** |
| `material_id` | `int` | **No index** |
| `wt` | `decimal(14,4)` | |
| `price` | `decimal(10,2)` | |

#### `ret_est_chit_utilization` (10 columns)
| Key Columns | Type | Notes |
|---|---|---|
| `chit_ut_id` | `int` UNIQUE (not PK) | Functionally PK but non-standard |
| `est_id` | `int` KEY | |
| `scheme_account_id` | `int unsigned` KEY | |
| `utl_amount` | `decimal(12,2)` | |
| `closing_weight` | `decimal(10,3)` | |
| `wastage_per` | `int` ⚠️ | **Truncates fractional — should be decimal** |
| `savings_in_wastage` | `decimal(10,3)` | |
| `mc_value` | `decimal(10,2)` | |
| `savings_in_making_charge` | `decimal(10,2)` | |
| `rate_per_gram` | `decimal(10,2)` | |

#### `ret_est_gift_voucher_details` (5 columns) ⚠️ NO PK, NO INDEXES
| Key Columns | Type | Notes |
|---|---|---|
| `gift_voucher_id` | `int` NOT NULL | **Not PK, not AUTO_INCREMENT** |
| `est_id` | `int` | **No index** |
| `voucher_no` | `varchar(45)` | |
| `gift_voucher_details` | `varchar(365)` | |
| `gift_voucher_amt` | `decimal(10,2)` | |

#### `ret_est_other_metals` (12 columns) ⚠️ MyISAM
Engine is MyISAM — transactions are silently ignored.

| Key Columns | Type | Notes |
|---|---|---|
| `est_other_itm_id` | `int` PK AUTO_INCREMENT | |
| `est_item_id` | `int` KEY | |
| `tag_other_itm_metal_id` | `int` KEY | |
| `tag_other_itm_grs_weight` | `decimal(10,3)` | |
| `tag_other_itm_cal_type` | `int` KEY | 1=Weight, 2=Pcs |
| `tag_other_itm_mc` | `decimal(10,2)` | |
| `tag_other_itm_rate` | `decimal(10,2)` | |
| `tag_other_itm_amount` | `decimal(10,2)` | |

#### `ret_est_sales_return_utilization` (5 columns) ⚠️ NO NON-PK INDEXES
| Key Columns | Type | Notes |
|---|---|---|
| `sr_ut_id` | `int` PK AUTO_INCREMENT | |
| `est_id` | `int` | **No index** |
| `bill_id` | `int` | **No index** |
| `bill_det_id` | `int` | |
| `esti_date` | `date` | |

#### `ret_est_tag_merge` (3 columns) ⚠️ NO NON-PK INDEXES
| Key Columns | Type | Notes |
|---|---|---|
| `id_est_tag_merge` | `int` PK AUTO_INCREMENT | |
| `est_item_id` | `int` | **No index** |
| `ref_est_item_id` | `int` | **No index** |

#### `ret_esti_old_metal_stone_details` (12 columns) ⚠️ NO NON-PK INDEXES
| Key Columns | Type | Notes |
|---|---|---|
| `est_old_metal_stone_id` | `int` PK AUTO_INCREMENT | |
| `est_id` | `int` | **No index** |
| `est_old_metal_sale_id` | `int` | **No index** |
| `stone_id` | `int` | |
| `wt` | `decimal(12,4)` | |
| `price` | `decimal(10,2)` | |
| `is_apply_in_lwt` | `tinyint(1)` | |
| `stone_cal_type` | `tinyint` | 1=By weight, 2=By pcs |
| `rate_per_gram` | `decimal(10,2)` | |

#### `ret_estimation_other_inventory_issue` (4 columns) ⚠️ NO NON-PK INDEXES
| Key Columns | Type | Notes |
|---|---|---|
| `id_inv_issue` | `int` PK AUTO_INCREMENT | |
| `esti_id` | `int` | **No index** |
| `id_other_item` | `int` | |
| `no_of_piece` | `int` | |

#### `ret_estimation_other_charges` (4 columns)
| Key Columns | Type | Notes |
|---|---|---|
| `id_est_charge` | `int` PK AUTO_INCREMENT | |
| `est_item_id` | `int` KEY | |
| `id_charge` | `int` KEY | |
| `amount` | `decimal(10,2)` | |

---

## Summary of Findings

| Category | Count | Tables Affected |
|---|---|---|
| **Engine mismatch (MyISAM)** | 6 | `ret_est_other_metals`, `ret_bill_return_details`, `ret_tag_other_metals`, `ret_product_mapping`, `ret_section`, `ret_design_weight_range_wc` |
| **Missing PRIMARY KEY** | 1 | `ret_est_gift_voucher_details` |
| **Type mismatch (int vs decimal for $$$)** | 8 columns | `ret_estimation` (3), `ret_estimation_items` (5) |
| **Missing indexes on FK columns** | 6 tables | `ret_estimation_item_other_materials`, `ret_estimation_other_inventory_issue`, `ret_est_sales_return_utilization`, `ret_est_tag_merge`, `ret_esti_old_metal_stone_details`, `ret_est_gift_voucher_details` |
| **Charset issues (latin1 vs utf8mb4)** | 7+ tables | `ret_estimation`, `ret_estimation_items`, `ret_estimation_old_metal_sale_details`, `ret_est_other_metals`, `branch`, `metal_rates`, `ret_billing` |
| **Non-standard PK (UNIQUE vs PRIMARY)** | 1 | `ret_est_chit_utilization` |
| **Extreme table width (100+ cols)** | 6 | `scheme` (203), `payment` (140), `chit_settings` (129), `scheme_account` (123), `ret_taging` (118), `customer` (117), `ret_billing` (112) |
| **No FK constraints** | All 85 | Architectural — known tradeoff |

**Total scope**: 85 tables (13 owned + 72 referenced)
**Total anomalies**: 31 (13 critical, 8 moderate, 10 low)

