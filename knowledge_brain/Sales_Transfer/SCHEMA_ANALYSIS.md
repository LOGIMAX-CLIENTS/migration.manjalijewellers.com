# Sales Transfer Module — Schema Analysis

> **Module**: Sales Transfer
> **Last Updated**: 2026-03-20 — Round 4 (Schema Verification)
> **Source**: `dev_structure.sql` (MySQL 8.4 dump from `retail_dev` RDS)
> **Note**: Columns verified against actual DB schema in Round 4.

---

## Part A: Tables Owned/Primarily Written by This Module

> This module does NOT own any tables directly. It writes to tables owned by the Billing and Tagging modules. See Part B.

---

## Part B: Referenced Tables (Verified Against Schema)

### 1. `ret_billing` (Owner: Billing) — L5855 in schema dump

**Purpose**: Header table for all billing transactions. Sales Transfers use `bill_type=13`, Sales Return Transfers use `bill_type=14`.
**Engine**: InnoDB | **PK**: `bill_id` (auto_increment)
**Unique Keys**: `form_secret`, `(fin_year_code, bill_no, id_branch, is_eda)`

| Column | DB Type | Usage in This Module | Write? | ⚠️ R4 Notes |
|---|---|---|---|---|
| `bill_id` | `int NOT NULL AUTO_INCREMENT` | PK — used everywhere as FK reference | INSERT (auto) | |
| `bill_no` | `varchar(45)` | Generated via `code_number_generator()` | INSERT | |
| `metal_type` | `int DEFAULT NULL` | From `$id_metal` POST param | INSERT | |
| `sales_ref_no` | `varchar(45)` | Generated via `generateRefNo()` for sales transfers | INSERT | |
| `s_ret_refno` | `varchar(45)` | Generated via `generateRefNo()` for return transfers | INSERT | |
| `fin_year_code` | `varchar(15)` | From `get_FinancialYear()` | INSERT | |
| `bill_type` | `tinyint NOT NULL` | 13 (Sales Transfer) or 14 (Sales Ret Transfer) | INSERT | Comment confirms: `13-Sales Transfer,14-Sales Ret Transfer` |
| `tot_bill_amount` | **`decimal(10,0)`** | Sum of item costs | INSERT + UPDATE | **⚠️ NO DECIMAL PRECISION! Amounts truncated to integers** |
| `tot_amt_received` | `decimal(10,0)` | Not set by this module | — | Also truncated to integers |
| `tot_sale_amt` | `decimal(10,0)` | Not set by this module | — | Also truncated |
| `bill_date` | `datetime` | From day-closing logic | INSERT | |
| `created_time` | `datetime` | `date("Y-m-d H:i:s")` | INSERT | |
| `created_by` | `int` | Session `uid` | INSERT | |
| `id_branch` | `int unsigned DEFAULT NULL` | From branch (= `from_branch`) | INSERT | NULLable, no FK |
| `goldrate_22ct` | `decimal(10,2)` | From `get_branchwise_rate()` | INSERT | **⚠️ R2 Bug: Return transfers hardcode this to 0** |
| `silverrate_1gm` | `decimal(10,2)` | From `get_branchwise_rate()` | INSERT | Not set in return transfers |
| `goldrate_9ct` | `decimal(10,2)` | Not used by this module | — | NEW: exists in schema but unused |
| `goldrate_14ct` | `decimal(10,2)` | Not used by this module | — | NEW: exists in schema but unused |
| `goldrate_18ct` | `decimal(10,2) NOT NULL DEFAULT '0.00'` | Not used by this module | — | NEW: exists in schema but unused |
| `remark` | `text` | User input or 'SALES RETURN TRASNFER' | INSERT | |
| `billing_for` | `tinyint(1) NOT NULL DEFAULT '1'` | Always `3` for transfers | INSERT | **Custom value 3 — not in DB comment (1-Customer,2-Company)** |
| `from_branch` | `int DEFAULT NULL` | Source branch | INSERT | **NULLable, no FK constraint** |
| `to_branch` | `int DEFAULT NULL` | Destination branch | INSERT | **NULLable, no FK constraint** |
| `is_credit` | `tinyint(1) NOT NULL DEFAULT '0'` | Always `1` for sales transfers | INSERT | **⚠️ DEFAULT is 0. Return transfers OMIT this → DEFAULT 0 ≠ 1 (R2 Risk #14 CONFIRMED)** |
| `credit_status` | `tinyint(1) NOT NULL DEFAULT '1'` | Always `2` for sales transfers | INSERT | **⚠️ DEFAULT is 1 (Paid). Return transfers OMIT this → stays 1 instead of 2 (Pending)** |
| `form_secret` | `varchar(100)` | CSRF token | INSERT | UNIQUE KEY — prevents duplicate submit |
| `ref_bill_id` | `int DEFAULT NULL` | Original bill_id for returns | INSERT | Indexed |
| `download_date` | `datetime DEFAULT NULL` | Set during download/approval | UPDATE | |
| `download_by` | `int DEFAULT NULL` | Session `uid` during download | UPDATE | |
| `is_eda` | `tinyint(1) NOT NULL DEFAULT '1'` | Always `1` (normal sale) | INSERT | Part of UNIQUE KEY |
| `istransfered` | `int NOT NULL DEFAULT '0'` | Tally integration flag | Not used | Indexed |
| `bill_status` | `tinyint(1) NOT NULL DEFAULT '1'` | Filter — always `=1` | READ only | Indexed |

### 2. `ret_bill_details` (Owner: Billing) — L5546 in schema dump

**Purpose**: Line items for each billing record. Each row = one tag/item in the transfer.
**Engine**: InnoDB | **PK**: `bill_det_id` (auto_increment)

| Column | DB Type | Usage in This Module | Write? | ⚠️ R4 Notes |
|---|---|---|---|---|
| `bill_det_id` | `int NOT NULL AUTO_INCREMENT` | PK | READ | |
| `bill_id` | `int DEFAULT NULL` | FK to `ret_billing` | INSERT | **No FK constraint in DB!** |
| `bill_type` | `int DEFAULT NULL` | Always `2` for transfers | INSERT | Comment: `1->Purchase 2->Sales` |
| `product_id` | `int DEFAULT NULL` | From tag data | INSERT | Indexed |
| `design_id` | `int DEFAULT NULL` | From tag data | INSERT | Indexed |
| `tag_id` | `int DEFAULT NULL` | FK to `ret_taging` | INSERT | Indexed, **no FK constraint** |
| `purity` | `int DEFAULT NULL` | From tag data | INSERT | |
| `piece` | `int DEFAULT NULL` | From tag data | INSERT | |
| `less_wt` | `decimal(12,3)` | From tag data | INSERT | 3 decimal places |
| `net_wt` | `decimal(12,3)` | From tag data | INSERT | 3 decimal places |
| `gross_wt` | `decimal(12,3)` | From tag data | INSERT | 3 decimal places, indexed |
| `calculation_based_on` | `int DEFAULT NULL` | From tag data | INSERT | Indexed |
| `item_cost` | `decimal(10,2)` | Calculated: taxable + tax | INSERT | **2 decimal places vs header decimal(10,0)** |
| `total_igst` | `decimal(10,2)` | IGST amount (inter-state) | INSERT | |
| `total_sgst` | `decimal(10,2)` | SGST amount (intra-state) | INSERT | |
| `total_cgst` | `decimal(10,2)` | CGST amount (intra-state) | INSERT | |
| `item_total_tax` | `decimal(10,2)` | Total tax amount | INSERT | |
| `rate_per_grm` | `decimal(10,2)` | From user input | INSERT | |
| `mc_value` | `decimal(10,2)` | Not set by this module (NULL) | — | |
| `wastage_percent` | `decimal(10,2)` | Not set by this module (NULL) | — | |
| `item_type` | `int DEFAULT NULL` | Always `0` | INSERT | Not in comment enum |
| `status` | `tinyint(1) NOT NULL DEFAULT '1'` | Filter in return queries | READ | 1=Sold, 2=Returned |

### 3. `ret_taging` (Owner: Tagging) — L12575 in schema dump

**Purpose**: Master table for physical jewelry items (tags). 92 columns.
**Engine**: InnoDB | **PK**: `tag_id` (auto_increment) | **UNIQUE**: `tag_code`, `old_tag_id`, `remarks`

| Column | DB Type | Usage in This Module | Write? | ⚠️ R4 Notes |
|---|---|---|---|---|
| `tag_id` | `int NOT NULL AUTO_INCREMENT` | PK | READ | |
| `tag_code` | `varchar(45)` | Unique tag identifier | READ | UNIQUE KEY |
| `old_tag_id` | `varchar(20) DEFAULT NULL` | Legacy tag code | READ (filter) | UNIQUE KEY — can cause duplicate errors if same value |
| `tag_status` | `int NOT NULL DEFAULT '0'` | Updated during transfer | UPDATE | **0=Not sold, 4=In Transit, 6=Sales Return** — 17+ statuses |
| `current_branch` | `int DEFAULT NULL` | Updated during download | UPDATE | Indexed |
| `product_id` | `int DEFAULT NULL` | FK to product master | READ | Indexed |
| `design_id` | `int DEFAULT NULL` | FK to design master | READ | Indexed |
| `id_sub_design` | `int DEFAULT NULL` | Sub-design reference | READ | Indexed |
| `gross_wt` | `decimal(12,3)` | Weight | READ | **12,3 — higher precision than bill_details** |
| `net_wt` | `decimal(12,3)` | Weight | READ | |
| `less_wt` | `decimal(12,3) DEFAULT '0.000'` | Weight | READ | Has default value |
| `piece` | `int DEFAULT '1'` | Count | READ | Default 1 |
| `calculation_based_on` | `int DEFAULT '2'` | Calc type | READ | Default 2 (wastage on net, MC on gross) |
| `purity` | `int DEFAULT NULL` | Metal purity | READ | |
| `tag_lot_id` | `int DEFAULT NULL` | FK to lots | READ | Indexed |
| `id_section` | `int DEFAULT NULL` | Section reference | READ | |

### 4. `ret_day_closing` — L6782 in schema dump

**Purpose**: Day-closing status per branch. **ONE row per branch** (UNIQUE KEY on `id_branch`).
**Engine**: InnoDB | **PK**: `id_day_closing` (auto_increment)

| Column | DB Type | Usage in This Module | ⚠️ R4 Notes |
|---|---|---|---|
| `id_day_closing` | `int NOT NULL AUTO_INCREMENT` | — | |
| `id_branch` | `int unsigned DEFAULT NULL` | Filter | **UNIQUE KEY — guarantees 1 row per branch** |
| `type` | `tinyint(1) NOT NULL DEFAULT '1'` | Not used | 1=Auto, 2=Manual |
| `is_day_closed` | `tinyint(1) NOT NULL DEFAULT '0'` | Day close check | 0=No |
| `entry_date` | `date NOT NULL` | **The key column** — bill_date source | **`date` type, NOT `datetime`** |
| `created_by` | `int unsigned DEFAULT NULL` | Not used | |
| `created_on` | `datetime NOT NULL` | Not used | |

### 5. `ret_branch_transfer` — L6237 in schema dump

**Purpose**: Physical branch-to-branch item transfers (tagged/non-tagged). NOT used by Sales Transfer directly, but referenced by shared JS.
**Engine**: InnoDB | **PK**: `branch_transfer_id` (auto_increment)

| Column | DB Type | ⚠️ R4 Notes |
|---|---|---|
| `branch_transfer_id` | `int NOT NULL AUTO_INCREMENT` | |
| `status` | `tinyint(1) NOT NULL DEFAULT '1'` | 1=Yet to Approve, 2=Approved(InTransit), 3=Rejected, 4=Stock Updated |
| `transfer_from_branch` | `int unsigned DEFAULT NULL` | |
| `transfer_to_branch` | `int unsigned DEFAULT NULL` | |

---

## R4 Schema-Based Risk Addendum

| # | Finding | Impact | Severity |
|---|---|---|---|
| 20 | `ret_billing.tot_bill_amount` is `decimal(10,0)` — all amounts truncated to integers | Financial precision loss on every transfer bill | P1 |
| 21 | `ret_billing.is_credit` DEFAULT is 0, `credit_status` DEFAULT is 1 (Paid) — return transfers omitting these get wrong defaults | Return transfer bills appear as non-credit, fully-paid when they should be credit+pending | P0 (R2 Risk #14 **ESCALATED with schema evidence**) |
| 22 | `ret_billing.from_branch` and `to_branch` are NULLable with no FK — no referential integrity on branch references | Orphaned branch references possible | P2 |
| 23 | `ret_bill_details.item_cost` is `decimal(10,2)` but header `tot_bill_amount` is `decimal(10,0)` — precision mismatch | Sum of detail costs (2 decimals) truncated at header level | P1 |
| 24 | `ret_billing.billing_for=3` is not in DB comment enum (only 1-Customer, 2-Company mentioned) | Undocumented value — reports may not account for billing_for=3 | P2 |
| 25 | `ret_day_closing.entry_date` is `date` type — code casts it with `date()` PHP function which expects datetime | Works but relies on PHP auto-conversion | P3 |

---

## SQL Injection Risk Assessment (Unchanged from R1)

| Method | Vulnerable Input | Severity |
|---|---|---|
| `get_sales_transfer_tag_details()` | `$data['from_brn']`, `$data['lotno']`, `$data['design_id']`, `$data['prodId']`, `$data['tag_code']`, `$data['old_tag_code']`, `$data['cat_id']`, `$data['id_metal']` | **P0** |
| `get_category_details()` | `$id_ret_category` | **P0** |
| `get_metal_details()` | `$id_metal` | **P0** |
| `get_category_tag_details()` | `$cat_id`, `$id_branch` | **P0** |
| `get_sales_trans_approval_tag()` | `$data['fin_year_code']`, `$data['bill_no']`, `$data['from_brn']`, `$data['to_brn']` | **P0** |
| `get_billed_details()` | `$bill_id` | **P0** |
| `get_branch_details()` | `$id_branch` | **P0** |
| `getSalesTrans_Tag()` | `$bill_id` | **P0** |
| `get_sales_return_trans_req_tag()` | Multiple `$data[]` fields | **P0** |
| `get_sales_return_req_tag_details()` | `$cat_id`, `$id_branch`, `$bill_id`, `$from_branch` | **P0** |
| `getBillId()` | `$to_brn`, `$sales_bill_no`, `$fin_year_code` | **P0** |
| `get_sales_return_trans_approval_tag()` | Multiple `$data[]` fields | **P0** |
| `get_sales_return_tag_details()` | `$cat_id`, `$id_branch`, `$bill_id` | **P0** |
| `getSettigsByName()` | `$name` | **P0** |
| `fetchTagsByFilter_scan()` | Multiple `$data[]` fields | **P0** |
| `get_TagBilledPcs()` | `$bill_id` | **P0** |
| `fetchReturnTagsByFilter_scan()` | `$data['from_brn']`, `$data['tag_code']`, `$data['bill_no']` | **P0** |

> ⚠️ **All 17 query methods** in the model use string concatenation for SQL. Zero use of parameterized queries.
