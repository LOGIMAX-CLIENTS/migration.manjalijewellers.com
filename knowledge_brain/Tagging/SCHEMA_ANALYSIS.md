# Tagging Module — Schema Analysis

> **Module**: Tagging
> **Last Updated**: 2026-03-24 (Round 10)

---

## 1. Primary Table: `ret_taging`

### Core Fields

| Column          | Type         | Purpose                                           | Notes                                   |
| --------------- | ------------ | ------------------------------------------------- | --------------------------------------- |
| `tag_id`        | INT (PK, AI) | Primary key                                       | Auto-increment                          |
| `tag_code`      | VARCHAR      | Unique tag identifier                             | Format: `{short_code}-{seq_no}`         |
| `tag_status`    | INT          | Lifecycle status (0-17)                           | See MODULE_BRAIN §5                     |
| `tag_type`      | INT          | Tag type (suspense stock flag)                    | 0=normal                                |
| `tag_lot_id`    | INT (FK)     | Source lot                                        | → `ret_lot_inwards.lot_no`              |
| `product_id`    | INT (FK)     | Product master                                    | → `ret_product_master.pro_id`           |
| `design_id`     | INT (FK)     | Design                                            | → `ret_design_master.design_no`         |
| `id_sub_design` | INT (FK)     | Sub-design                                        | → `ret_sub_design_master.id_sub_design` |
| `purity`        | INT (FK)     | Purity                                            | → `ret_purity.id_purity`                |
| `cat_type`      | INT          | Category: 1=Ornament, 2=Bullion, 3=Stone, 4=Alloy |                                         |

### Weight Fields

| Column         | Type     | Purpose                               |
| -------------- | -------- | ------------------------------------- |
| `piece`        | INT      | Number of pieces                      |
| `gross_wt`     | DECIMAL  | Gross weight                          |
| `less_wt`      | DECIMAL  | Less weight (stone/other deductions)  |
| `net_wt`       | DECIMAL  | Net weight = gross - less             |
| `uom_gross_wt` | INT (FK) | UOM for gross weight (stone category) |

### Pricing Fields

| Column                       | Type    | Purpose                               |
| ---------------------------- | ------- | ------------------------------------- |
| `calculation_based_on`       | INT     | 0=gross, 1=net, 2=gross               |
| `retail_max_wastage_percent` | DECIMAL | Wastage %                             |
| `tag_mc_type`                | INT     | MC type: 1=per piece, 2=per gram, 3=% |
| `tag_mc_value`               | DECIMAL | Making charge value                   |
| `retail_max_mc`              | DECIMAL | Max MC limit                          |
| `sell_rate`                  | DECIMAL | Selling rate at tag time              |
| `item_rate`                  | DECIMAL | Adjusted item rate                    |
| `sales_value`                | DECIMAL | Computed sales value                  |
| `tag_purchase_cost`          | DECIMAL | Purchase cost                         |
| `stone_calculation_based_on` | INT     | 1=weight, 2=pieces                    |

### Location Fields

| Column            | Type     | Purpose                  |
| ----------------- | -------- | ------------------------ |
| `id_branch`       | INT (FK) | Origin branch            |
| `current_branch`  | INT (FK) | Current physical branch  |
| `cost_center`     | INT (FK) | Cost allocation branch   |
| `current_counter` | INT      | Counter/display location |
| `id_section`      | INT (FK) | Section assignment       |

### Identity Fields

| Column             | Type    | Purpose                |
| ------------------ | ------- | ---------------------- |
| `hu_id`            | VARCHAR | HUID 1                 |
| `hu_id2`           | VARCHAR | HUID 2                 |
| `cert_no`          | VARCHAR | Certificate number     |
| `cert_img`         | VARCHAR | Certificate image path |
| `manufacture_code` | VARCHAR | Manufacturer code      |
| `style_code`       | VARCHAR | Style code             |
| `old_tag_id`       | VARCHAR | Previous tag reference |
| `image`            | VARCHAR | Tag image filename     |
| `ref_no`           | VARCHAR | Reference number       |

### Lot Purchase Fields

| Column                   | Type    | Purpose                  |
| ------------------------ | ------- | ------------------------ |
| `lot_purchase_touch`     | DECIMAL | Lot purchase touch value |
| `purchase_touch`         | DECIMAL | Purchase touch           |
| `lot_rate_calc_type`     | INT     | Rate calculation type    |
| `lot_rate`               | DECIMAL | Lot rate                 |
| `lot_calc_type`          | INT     | Lot calculation type     |
| `lot_making_charge`      | DECIMAL | Lot making charge        |
| `lot_mc_type`            | INT     | Lot MC type              |
| `lot_wastage_percentage` | DECIMAL | Lot wastage %            |

### Other Fields

| Column                 | Type     | Purpose                         |
| ---------------------- | -------- | ------------------------------- |
| `id_orderdetails`      | INT (FK) | Linked customer order           |
| `id_lot_inward_detail` | INT (FK) | Source lot inward detail        |
| `size`                 | INT (FK) | Size reference                  |
| `product_division`     | INT (FK) | Product division                |
| `quality_id`           | INT (FK) | Quality grade                   |
| `tag_split_emp`        | INT (FK) | Split employee                  |
| `is_new_arrival`       | INT      | New arrival flag                |
| `halmarking`           | VARCHAR  | Hallmarking info                |
| `remarks`              | TEXT     | Remarks                         |
| `narration`            | TEXT     | Narration                       |
| `tag_datetime`         | DATETIME | Tag date (based on day closing) |
| `created_time`         | DATETIME | Actual creation timestamp       |
| `created_by`           | INT (FK) | Creator employee                |
| `tot_print_taken`      | INT      | Print counter                   |

---

## 2. Supporting Tables

### `ret_taging_stone`

| Column         | Type     | Purpose                |
| -------------- | -------- | ---------------------- |
| `tag_stone_id` | INT (PK) | Primary key            |
| `tag_id`       | INT (FK) | → `ret_taging.tag_id`  |
| `stone_id`     | INT (FK) | → `ret_stone.stone_id` |
| `wt`           | DECIMAL  | Stone weight           |
| `pieces`       | INT      | Stone pieces           |
| `rate`         | DECIMAL  | Rate per unit          |
| `amount`       | DECIMAL  | Total amount           |
| `tag_display`  | VARCHAR  | Display label          |

### `ret_taging_material`

| Column            | Type     | Purpose               |
| ----------------- | -------- | --------------------- |
| `tag_material_id` | INT (PK) | Primary key           |
| `tag_id`          | INT (FK) | → `ret_taging.tag_id` |
| `material_id`     | INT (FK) | Material reference    |
| `purity_id`       | INT (FK) | Material purity       |
| `wt`              | DECIMAL  | Weight                |
| `rate`            | DECIMAL  | Rate                  |
| `amount`          | DECIMAL  | Total amount          |

### `ret_taging_images`

| Column         | Type     | Purpose                        |
| -------------- | -------- | ------------------------------ |
| `tag_image_id` | INT (PK) | Primary key                    |
| `tag_id`       | INT (FK) | → `ret_taging.tag_id`          |
| `image`        | VARCHAR  | Image filename                 |
| `date_add`     | DATETIME | Upload date                    |
| `is_default`   | INT      | Default image flag (1=default) |

### `ret_tag_attributes`

| Column            | Type     | Purpose               |
| ----------------- | -------- | --------------------- |
| `attr_tag_id`     | INT (PK) | Primary key           |
| `tag_id`          | INT (FK) | → `ret_taging.tag_id` |
| `attribute_id`    | INT (FK) | Attribute reference   |
| `attribute_value` | VARCHAR  | Value                 |

### `ret_section_tag_status_log`

| Column            | Type     | Purpose               |
| ----------------- | -------- | --------------------- |
| `id`              | INT (PK) | Primary key           |
| `tag_id`          | INT (FK) | → `ret_taging.tag_id` |
| `date`            | DATETIME | Log date              |
| `status`          | INT      | Status code           |
| `from_branch`     | INT      | Source branch         |
| `to_branch`       | INT      | Destination branch    |
| `from_section`    | INT      | Source section        |
| `to_section`      | INT      | Destination section   |
| `issuspensestock` | INT      | Suspense flag         |
| `created_on`      | DATETIME | Created timestamp     |
| `created_by`      | INT      | Creator               |

### `ret_tag_collection`

| Column            | Type     | Purpose              |
| ----------------- | -------- | -------------------- |
| `id_tag_mapping`  | INT (PK) | Primary key          |
| `ref_no`          | VARCHAR  | Collection reference |
| `collection_name` | INT (FK) | → collection master  |
| `total_tags`      | INT      | Count                |
| `total_pieces`    | INT      | Pieces               |
| `total_gross_wt`  | DECIMAL  | Gross weight         |
| `total_net_wt`    | DECIMAL  | Net weight           |

### `ret_retagging_process`

| Column             | Type     | Purpose                       |
| ------------------ | -------- | ----------------------------- |
| `id_process`       | INT (PK) | Primary key                   |
| `process_type`     | INT      | 1=tag-to-tag, 2=tag-to-nontag |
| `created_datetime` | DATETIME | Process date                  |
| `created_by`       | INT      | Creator                       |

### `ret_dup_print_log`

| Column           | Type     | Purpose                    |
| ---------------- | -------- | -------------------------- |
| `id`             | INT (PK) | Primary key                |
| `tag_id`         | INT (FK) | → `ret_taging.tag_id`      |
| `print_taken_by` | INT      | Employee                   |
| `print_datetime` | DATETIME | Print date                 |
| `reason`         | TEXT     | Reason for duplicate print |

### `ret_taging_status_log` ⚠️ R10 DISCOVERED

> **R10 Note**: This table is **distinct** from `ret_section_tag_status_log`. It is written by `tagging('delete')` case (L2099–2119) for every tag status change. Not previously documented.

| Column            | Type     | Purpose                                |
| ----------------- | -------- | -------------------------------------- |
| `tag_id`          | INT (FK) | → `ret_taging.tag_id`                  |
| `date`            | DATETIME | Event date                             |
| `status`          | INT      | New status code (e.g. 2=deleted)       |
| `from_branch`     | INT      | Source branch                          |
| `to_branch`       | INT      | Destination branch (same as from on del) |
| `created_on`      | DATETIME | Log creation timestamp                 |
| `created_by`      | INT      | Employee who triggered the change      |
| `issuspensestock` | INT      | Copied from `ret_taging.tag_type`      |

---

## 3. Key Relationships (ER Summary)

```
ret_lot_inwards (1) ──→ (N) ret_lot_inward_detail
                                    │
                                    ↓
ret_taging (N) ←── FK: tag_lot_id, id_lot_inward_detail
    │
    ├── (1:N) ret_taging_stone
    ├── (1:N) ret_taging_material
    ├── (1:N) ret_taging_images
    ├── (1:N) ret_tag_attributes
    ├── (1:N) ret_section_tag_status_log
    ├── (N:1) ret_product_master (product_id)
    ├── (N:1) ret_design_master (design_id)
    ├── (N:1) ret_sub_design_master (id_sub_design)
    ├── (N:1) ret_purity (purity)
    ├── (N:1) branch (id_branch, current_branch, cost_center)
    ├── (N:1) employee (created_by)
    └── (N:1) ret_customer_order_details (id_orderdetails)
```

---

## 4. Query Pattern Observations

### ⚠️ Raw SQL Prevalence

The model uses **raw SQL** extensively via `$this->db->query()` rather than CI3 Active Record. Key observations:

1. **`ajax_getTaggingList()`** — 265-line raw SQL with string concatenation (SQL injection risk)
2. **`get_entry_records()`** — Raw SQL with `tag_id='$tag_id'` (no escaping)
3. **`get_tag_details()`** — ~500-line raw SQL query
4. **`getAvailableLots()`** — ~230-line raw SQL with subqueries

### N+1 Query Patterns

- `ajax_getTaggingList()` runs `getTagStoneDetails()` per tag in a foreach loop (line 613)
- This creates N+1 queries for every tag listing call

### Missing Indexes (Likely)

Based on query patterns, these columns likely need indexing:

- `ret_taging.tag_lot_id` (used in almost every WHERE clause)
- `ret_taging.tag_status` (used in listing filters)
- `ret_taging.current_branch` (used in branch filters)
- `ret_taging.created_by` (used in employee filters)
- `ret_taging.tag_datetime` (used in date range queries)
- `ret_taging.id_orderdetails` (used in order linking queries)
- `ret_taging_stone.tag_id` (used in all stone lookups)
- `ret_taging_material.tag_id` (used in all material lookups)

---

## 5. DB Truth Protocol (Verification Queries)

> Added in Round 3. Use these queries to diagnose data integrity issues.

### Q1: Complete Tag Record (Header + All Children)

```sql
-- Header
SELECT t.tag_id, t.tag_code, t.tag_status, t.cat_type,
       t.product_id, p.product_name,
       t.purity, pur.purity_name,
       t.design_id, d.design_name,
       t.piece, t.gross_wt, t.less_wt, t.net_wt,
       t.calculation_based_on, t.sell_rate, t.item_rate,
       t.tag_mc_type, t.tag_mc_value, t.sales_value,
       t.tag_purchase_cost, t.id_branch, t.current_branch,
       t.tag_lot_id, t.id_lot_inward_detail
FROM ret_taging t
LEFT JOIN ret_product_master p ON t.product_id = p.pro_id
LEFT JOIN ret_purity pur ON t.purity = pur.id_purity
LEFT JOIN ret_design_master d ON t.design_id = d.design_no
WHERE t.tag_id = {TAG_ID};

-- Stones
SELECT * FROM ret_taging_stone WHERE tag_id = {TAG_ID};

-- Materials
SELECT * FROM ret_taging_material WHERE tag_id = {TAG_ID};

-- Images
SELECT * FROM ret_taging_images WHERE tag_id = {TAG_ID};

-- Attributes
SELECT * FROM ret_tag_attributes WHERE tag_id = {TAG_ID};

-- Status log
SELECT * FROM ret_section_tag_status_log
WHERE tag_id = {TAG_ID} ORDER BY created_on DESC;
```

### Q2: Verify Sale Value vs Expected Calculation

```sql
SELECT t.tag_id, t.tag_code,
       t.gross_wt, t.less_wt, t.net_wt,
       t.calculation_based_on,
       CASE t.calculation_based_on
         WHEN 0 THEN t.gross_wt
         WHEN 1 THEN t.net_wt
         WHEN 2 THEN t.gross_wt
       END as calc_weight,
       t.sell_rate,
       t.tag_mc_type, t.tag_mc_value,
       t.retail_max_wastage_percent,
       t.sales_value as stored_value,
       -- Stone total
       (SELECT COALESCE(SUM(amount), 0)
        FROM ret_taging_stone WHERE tag_id = t.tag_id) as stone_total
FROM ret_taging t
WHERE t.tag_id = {TAG_ID};
-- Manual check: stored_value should ≈ (calc_weight × sell_rate) + MC + wastage + stone_total
```

### Q3: Orphan Records (Children Without Parent)

```sql
-- Stones without parent
SELECT s.* FROM ret_taging_stone s
LEFT JOIN ret_taging t ON s.tag_id = t.tag_id
WHERE t.tag_id IS NULL;

-- Materials without parent
SELECT m.* FROM ret_taging_material m
LEFT JOIN ret_taging t ON m.tag_id = t.tag_id
WHERE t.tag_id IS NULL;

-- Images without parent
SELECT i.* FROM ret_taging_images i
LEFT JOIN ret_taging t ON i.tag_id = t.tag_id
WHERE t.tag_id IS NULL;

-- Attributes without parent
SELECT a.* FROM ret_tag_attributes a
LEFT JOIN ret_taging t ON a.tag_id = t.tag_id
WHERE t.tag_id IS NULL;
```

### Q4: Lot Balance Integrity

```sql
-- Compare original lot qty vs total tagged qty
SELECT lid.id_lot_inward_detail, lid.product_id,
       lid.gross_wt as original_gwt, lid.net_wt as original_nwt,
       lid.piece as original_pcs,
       COALESCE(SUM(CASE WHEN t.tag_status NOT IN (2,3)
                    THEN t.gross_wt ELSE 0 END), 0) as active_tagged_gwt,
       COALESCE(SUM(CASE WHEN t.tag_status NOT IN (2,3)
                    THEN t.net_wt ELSE 0 END), 0) as active_tagged_nwt,
       lid.gross_wt - COALESCE(SUM(CASE WHEN t.tag_status NOT IN (2,3)
                    THEN t.gross_wt ELSE 0 END), 0) as expected_balance
FROM ret_lot_inward_detail lid
LEFT JOIN ret_taging t ON t.id_lot_inward_detail = lid.id_lot_inward_detail
WHERE lid.id_lot_inward = {LOT_ID}
GROUP BY lid.id_lot_inward_detail
HAVING expected_balance < 0;
-- Rows returned = negative balance = PROBLEM
```

### Q5: Duplicate Tag Codes (Active Tags)

```sql
SELECT tag_code, COUNT(*) as cnt,
       GROUP_CONCAT(tag_id) as tag_ids,
       GROUP_CONCAT(tag_status) as statuses
FROM ret_taging
WHERE tag_status NOT IN (2)  -- exclude deleted
GROUP BY tag_code
HAVING COUNT(*) > 1;
```

### Q6: Invalid Status Transitions

```sql
-- Tags marked as "Sold" (1) but no billing record exists
SELECT t.tag_id, t.tag_code, t.tag_status
FROM ret_taging t
WHERE t.tag_status = 1
AND NOT EXISTS (
    SELECT 1 FROM ret_billing_item bi WHERE bi.tag_id = t.tag_id
);

-- Tags in transit (4) but no branch transfer record
SELECT t.tag_id, t.tag_code, t.tag_status
FROM ret_taging t
WHERE t.tag_status = 4
AND NOT EXISTS (
    SELECT 1 FROM ret_branch_transfer_items bti WHERE bti.tag_id = t.tag_id
);
```

### Q7: Branch Mismatch Check

```sql
-- Tags where origin ≠ current (should only be after BT)
SELECT t.tag_id, t.tag_code, t.tag_status,
       t.id_branch as origin, t.current_branch as current,
       b1.branch_name as origin_name, b2.branch_name as current_name
FROM ret_taging t
LEFT JOIN branch b1 ON t.id_branch = b1.id_branch
LEFT JOIN branch b2 ON t.current_branch = b2.id_branch
WHERE t.id_branch != t.current_branch
AND t.tag_status NOT IN (2, 4);
```
