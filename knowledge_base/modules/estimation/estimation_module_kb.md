# Retail Estimation Module - Knowledge Base

> **Purpose**: Reference documentation for building features and fixing bugs in the `ret_estimation` module.

---

## 📊 Database Schema

### Core Tables

#### `ret_estimation` (Header)

| Column             | Type          | Description                                          |
| ------------------ | ------------- | ---------------------------------------------------- |
| `estimation_id`    | int(11)       | Primary key                                          |
| `fin_year_code`    | varchar(15)   | Financial year reference                             |
| `esti_no`          | int(10)       | Estimation number                                    |
| `esti_for`         | tinyint(1)    | **1**=Customer, **2**=Branch Transfer, **3**=Company |
| `est_date`         | date          | Estimation date                                      |
| `cus_id`           | int(11)       | Customer FK → `customer`                             |
| `id_branch`        | int(10)       | Branch FK → `branch`                                 |
| `created_by`       | int(11)       | Employee FK                                          |
| `total_cost`       | decimal(10,2) | Total estimation value                               |
| `discount`         | decimal(10,2) | Discount applied                                     |
| `gift_voucher_amt` | decimal(10,2) | Voucher redemption                                   |
| `added_through`    | tinyint(1)    | **1**=Admin, **2**=Estimation App                    |
| `is_eda`           | int(11)       | **0**=No approval needed, **1**=Needs EDA approval   |
| `is_eda_approved`  | int(11)       | **0**=Pending, **1**=Approved, **2**=Rejected        |
| `estbillid`        | int(11)       | FK → `ret_billing` (if converted)                    |
| `goldrate_22ct`    | decimal(10,2) | Gold rate snapshot                                   |
| `silverrate_1gm`   | decimal(10,2) | Silver rate snapshot                                 |

---

#### `ret_estimation_items` (Line Items)

| Column                 | Type          | Description                                                                                                 |
| ---------------------- | ------------- | ----------------------------------------------------------------------------------------------------------- |
| `est_item_id`          | int(11)       | Primary key                                                                                                 |
| `esti_id`              | int(11)       | FK → `ret_estimation`                                                                                       |
| `item_type`            | int(11)       | **0**=Tag, **1**=Catalog, **2**=Custom, **3**=Order                                                         |
| `product_id`           | int(11)       | FK → `ret_product_master`                                                                                   |
| `tag_id`               | int(11)       | FK → `ret_taging` (for tagged items)                                                                        |
| `design_id`            | int(11)       | FK → `ret_design`                                                                                           |
| `purity`               | int(11)       | FK → `purity`                                                                                               |
| `gross_wt`             | decimal(12,3) | Gross weight                                                                                                |
| `net_wt`               | decimal(12,3) | Net weight (after stone/less deduction)                                                                     |
| `less_wt`              | decimal(12,3) | Weight deduction                                                                                            |
| `calculation_based_on` | tinyint(1)    | **0**=MC&Wast on Gross, **1**=on Net, **2**=MC Gross/Wast Net, **3**=Fixed Rate, **4**=Fixed Rate by Weight |
| `wastage_percent`      | decimal(10,2) | Wastage %                                                                                                   |
| `mc_value`             | decimal(10,2) | Making charge value                                                                                         |
| `mc_type`              | int(1)        | **1**=Per Piece, **2**=Per Gram, **3**=% on Price                                                           |
| `item_cost`            | decimal(10,2) | Total item cost                                                                                             |
| `est_rate_per_grm`     | decimal(10,2) | Rate per gram used                                                                                          |
| `tax_group_id`         | int(10)       | FK → tax group                                                                                              |
| `item_total_tax`       | decimal(10,2) | Tax amount                                                                                                  |
| `is_partial`           | tinyint(1)    | **1**=Partial/split sale                                                                                    |
| `is_non_tag`           | tinyint(1)    | **0**=Tagged, **1**=Non-Tagged                                                                              |
| `lot_no`               | int(11)       | Lot reference (for non-tag)                                                                                 |
| `purchase_status`      | tinyint(1)    | **0**=No, **1**=Purchased, **2**=Returned                                                                   |
| `bil_detail_id`        | int(11)       | FK to billing item (if converted)                                                                           |
| `istag_merged`         | tinyint(1)    | **1**=Merged tag item                                                                                       |

---

#### `ret_estimation_old_metal_sale_details` (Old Metal Purchase)

| Column              | Type          | Description                                                  |
| ------------------- | ------------- | ------------------------------------------------------------ |
| `old_metal_sale_id` | int(11)       | Primary key                                                  |
| `est_id`            | int(11)       | FK → `ret_estimation`                                        |
| `type`              | tinyint(1)    | **1**=Melting, **2**=Re-tag                                  |
| `item_type`         | int(11)       | **1**=Ornament, **2**=Coin, **3**=Bar                        |
| `id_category`       | int(11)       | Metal type (Gold/Silver)                                     |
| `gross_wt`          | decimal(14,3) | Gross weight                                                 |
| `net_wt`            | decimal(14,3) | Net weight                                                   |
| `stone_wt`          | decimal(14,3) | Stone weight deduction                                       |
| `dust_wt`           | decimal(14,3) | Dust/wastage weight                                          |
| `touch`             | decimal(10,2) | Purity touch (default 100)                                   |
| `purity`            | decimal(10,2) | Purity percentage                                            |
| `rate_per_gram`     | decimal(10,2) | Purchase rate                                                |
| `amount`            | decimal(10,2) | Total amount                                                 |
| `purpose`           | tinyint(1)    | **1**=Cash, **2**=Exchange                                   |
| `purchase_status`   | tinyint(1)    | **0**=No, **1**=Purchased, **2**=Chit Deposit, **3**=Advance |

---

#### `ret_estimation_item_stones` (Stone Details)

| Column              | Type          | Description                      |
| ------------------- | ------------- | -------------------------------- |
| `est_item_stone_id` | int(11)       | Primary key                      |
| `est_item_id`       | int(11)       | FK → `ret_estimation_items`      |
| `stone_id`          | int(11)       | FK → stone master                |
| `pieces`            | int(11)       | Number of stones                 |
| `wt`                | decimal(12,4) | Stone weight                     |
| `price`             | decimal(10,2) | Stone price                      |
| `is_apply_in_lwt`   | tinyint(1)    | **1**=Apply to less weight       |
| `stone_cal_type`    | tinyint(4)    | **1**=By Weight, **2**=By Pieces |
| `rate_per_gram`     | decimal(10,2) | Rate per gram                    |
| `quality_id`        | int(11)       | Stone quality FK                 |

---

#### Supporting Tables

| Table                                 | Purpose                                  |
| ------------------------------------- | ---------------------------------------- |
| `ret_est_chit_utilization`            | Chit scheme redemption during estimation |
| `ret_est_gift_voucher_details`        | Gift voucher usage                       |
| `ret_est_other_metals`                | Other metal components in item           |
| `ret_est_tag_merge`                   | Tag merging (child→parent mapping)       |
| `ret_estimation_other_charges`        | Additional charges per item              |
| `ret_estimation_item_other_materials` | Other materials per item                 |

---

## 🔗 Key Relationships

```
ret_estimation (1) ────┬──── (N) ret_estimation_items
                       ├──── (N) ret_estimation_old_metal_sale_details
                       ├──── (N) ret_est_chit_utilization
                       └──── (N) ret_est_gift_voucher_details

ret_estimation_items (1) ────┬──── (N) ret_estimation_item_stones
                             ├──── (N) ret_est_other_metals
                             ├──── (N) ret_estimation_other_charges
                             └──── (N) ret_estimation_item_other_materials
```

---

## 📂 File Structure

| File                                   | Role                               |
| -------------------------------------- | ---------------------------------- |
| `controllers/admin_ret_estimation.php` | Main controller (~3,549 lines)     |
| `models/ret_estimation_model.php`      | Database operations (~2,996 lines) |
| `assets/js/ret_estimation.js`          | Client-side logic (~31,338 lines)  |
| `views/estimation/form.php`            | Add/Edit form (~2,517 lines)       |
| `views/estimation/list.php`            | List view (~128 lines)             |

---

## ⚙️ Business Logic

### Calculation Types (`calculation_based_on`)

| Value | MC Applied On        | Wastage Applied On |
| ----- | -------------------- | ------------------ |
| 0     | Gross Weight         | Gross Weight       |
| 1     | Net Weight           | Net Weight         |
| 2     | Gross Weight         | Net Weight         |
| 3     | Fixed Rate           | N/A                |
| 4     | Fixed Rate by Weight | N/A                |

### Making Charge Types (`mc_type`)

| Value | Calculation          |
| ----- | -------------------- |
| 1     | Per Piece × Quantity |
| 2     | Per Gram × Weight    |
| 3     | Percentage on Price  |

### Item Types (`item_type`)

| Value | Description      | Source               |
| ----- | ---------------- | -------------------- |
| 0     | Tagged Item      | `ret_taging` table   |
| 1     | Catalog/Non-Tag  | `ret_nontagginglots` |
| 2     | Custom/Home Bill | Manual entry         |
| 3     | Order Item       | `customerorder`      |

---

## 🔑 Key Controller Methods

| Method                    | Purpose                       |
| ------------------------- | ----------------------------- |
| `estimation('add')`       | Load form with settings/data  |
| `estimation('save')`      | Insert new estimation + items |
| `estimation('list')`      | Load list view                |
| `getOrderBySearch()`      | AJAX: Search customer orders  |
| `estimationBySearchTag()` | AJAX: Search tags             |
| `get_purities()`          | AJAX: Get purity list         |

---

## 🔑 Key Model Methods

| Method                              | Purpose                            |
| ----------------------------------- | ---------------------------------- |
| `insertData($data, $table)`         | Smart insert with default handling |
| `get_entry_records($id)`            | Get estimation header with joins   |
| `getOtherEstimateItemsDetails($id)` | Get all line items & sub-details   |
| `getEstTags($branch, $search)`      | Search available tags              |
| `get_bill_no_format_detail()`       | Generate formatted bill numbers    |
| `createNewCustomer()`               | Quick customer creation            |

---

## 🔑 Key JS Functions

| Function                | Purpose                |
| ----------------------- | ---------------------- |
| `addTagRow()`           | Add new tag item row   |
| `addCatalogRow()`       | Add catalog item row   |
| `calculateItemCost()`   | Recalculate row totals |
| `calculateGrandTotal()` | Sum all items          |
| `getSearchOrders()`     | Order autocomplete     |
| `validateTagItems()`    | Pre-submit validation  |

---

## ⚠️ Common Patterns

### Adding New Item Field

1. Add column to `ret_estimation_items` table
2. Update `estimation('save')` in controller to capture field
3. Update `getOtherEstimateItemsDetails()` in model to retrieve
4. Add field to `form.php` view
5. Handle in `ret_estimation.js` calculation/validation

### Adding New Charge Type

1. Add master entry to `ret_other_charges` table
2. Charges are stored in `ret_estimation_other_charges`
3. UI handled in the "Other Charges" section of form

---

## 📝 Settings Dependencies

Key settings from `ret_settings` affecting estimation:

- `min_old_gold_rate`, `max_old_gold_rate` - Old metal rate limits
- `max_cash_allowed` - Cash transaction limit
- `weightschemecaltype` - Chit scheme calculation
- `bulk_wastage_discount` - Bulk wastage rules
- `wastage_rate_type` - Wastage calculation method

---

_Generated: 2026-01-26 | Source: etail_v3_sample.sql + code analysis_
