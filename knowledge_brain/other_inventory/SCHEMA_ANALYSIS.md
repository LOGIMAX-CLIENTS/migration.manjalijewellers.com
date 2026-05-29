# SCHEMA ANALYSIS — Other Inventory
> **Module:** Other Inventory | **Built:** 2026-03-14

---

## Part A: Owned Tables

### 1. `ret_other_inventory_item` — Item Master
| Column | Type (inferred) | Notes |
|---|---|---|
| `id_other_item` | INT PK AUTO_INCREMENT | Primary key |
| `name` | VARCHAR | Stored as UPPERCASE (see `strtoupper()`) |
| `short_code` | VARCHAR | Short code (read in ajax_get, not written in save/update) |
| `sku_id` | VARCHAR | Auto-set to id_other_item after insert; must be unique |
| `id_inv_size` | INT FK | → `ret_other_inventory_size.id_inv_size` (nullable) |
| `stock_id_uom` | INT FK | → `ret_uom.uom_id` (written on save, NOT updated on edit) |
| `purchase_id_uom` | INT FK | → `ret_uom.uom_id` |
| `item_for` | INT FK | → `ret_other_inventory_item_type.id_other_item_type` |
| `issue_to` | INT | Target: 1=Scheme customer, 2=Walk-in (NOT updated on edit) |
| `issue_preference` | INT | 1=FIFO, 2=FILO |
| `unit_price` | DECIMAL | Unit cost price |
| `item_image` | VARCHAR | Image filename relative to `assets/img/other_inventory/{sku_id}/` |
| `item_hsn_code` | VARCHAR | HSN code (referenced in getActiveskuid filter) |
| `qr_image` | VARCHAR | QR image src (without folder path) |
| `created_on` | DATETIME | |
| `created_by` | INT FK | → `employee.id_employee` (session uid) |
| `updated_on` | DATETIME | |
| `updated_by` | INT FK | → `employee.id_employee` |

**⚠️ Risk:** `stock_id_uom` and `issue_to` are NOT updated in edit flow — data loss on edit.

---

### 2. `ret_other_inventory_item_type` — Category Master
| Column | Type (inferred) | Notes |
|---|---|---|
| `id_other_item_type` | INT PK AUTO_INCREMENT | |
| `name` | VARCHAR | Category name |
| `outward_type` | INT/VARCHAR | Outward type classification |
| `asbillable` | TINYINT | 1=Cost (billable), 0=Free |
| `expirydatevalidate` | TINYINT | 1=Has expiry, 0=No expiry |
| `reorderlevel` | INT | Reorder alert level |
| `status` | TINYINT | 1=Active, 0=Inactive |
| `qrcode` | VARCHAR | QR code reference |
| `created_on` | DATETIME | |
| `created_by` | INT FK | |
| `updated_on` | DATETIME | |
| `updated_by` | INT FK | |

---

### 3. `ret_other_inventory_size` — Size Master
| Column | Type (inferred) | Notes |
|---|---|---|
| `id_inv_size` | INT PK AUTO_INCREMENT | |
| `size_name` | VARCHAR | Size label |
| `status` | TINYINT | 1=Active, 0=Inactive |
| `created_on` | DATETIME | |
| `created_by` | INT FK | |
| `updated_on` | DATETIME | |
| `updated_by` | INT FK | |

---

### 4. `ret_other_inventory_reorder_settings` — Reorder Settings
| Column | Type (inferred) | Notes |
|---|---|---|
| `id_inv_reorder_settings` | INT PK AUTO_INCREMENT | |
| `id_branch` | INT FK | → `branch.id_branch` |
| `id_other_item` | INT FK | → `ret_other_inventory_item.id_other_item` |
| `min_pcs` | INT | Minimum pieces threshold |
| `max_pcs` | INT | Maximum pieces target |

**Pattern:** Delete-then-insert on every edit — no history retained.

---

### 5. `ret_other_inventory_purchase` — Purchase Header
| Column | Type (inferred) | Notes |
|---|---|---|
| `otr_inven_pur_id` | INT PK AUTO_INCREMENT | |
| `otr_inven_pur_supplier` | INT FK | → `ret_karigar.id_karigar` |
| `entry_date` | DATETIME | From day closing logic |
| `supplier_order_ref_no` | VARCHAR | External PO reference |
| `otr_inven_pur_order_ref` | VARCHAR | Internal 5-digit padded ref |
| `supplier_bill_date` | DATE | |
| `purchase_bill_status` | TINYINT | 1=Active, 2=Cancelled |
| `cancel_reason` | VARCHAR | Populated on cancel |
| `otr_inven_pur_created_on` | DATETIME | |
| `otr_inven_pur_created_by` | INT FK | |
| `otr_inven_pur_updated_on` | DATETIME | |
| `otr_inven_pur_updated_by` | INT FK | |

---

### 6. `ret_other_inventory_purchase_items` — Purchase Line Items
| Column | Type (inferred) | Notes |
|---|---|---|
| `inv_pur_itm_id` | INT PK AUTO_INCREMENT | |
| `otr_inven_pur_id` | INT FK | → `ret_other_inventory_purchase` |
| `inv_pur_itm_itemid` | INT FK | → `ret_other_inventory_item.id_other_item` |
| `inv_pur_itm_qty` | INT | Quantity purchased |
| `inv_pur_itm_rate` | DECIMAL | Per-piece rate |
| `inv_pur_itm_total` | DECIMAL | Total incl. GST |
| `inv_pur_itm_gst` | DECIMAL | GST rate % |
| `gst_amount` | DECIMAL | GST amount in currency |

**⚠️ Naming:** `inv_pur_itm_total` = total WITH GST. `gst_amount` = GST component only.

---

### 7. `ret_other_inventory_purchase_items_details` — Individual Piece Records
| Column | Type (inferred) | Notes |
|---|---|---|
| `pur_item_detail_id` | INT PK AUTO_INCREMENT | |
| `inv_pur_itm_id` | INT FK | → `ret_other_inventory_purchase_items` |
| `other_invnetory_item_id` | INT FK | **⚠️ TYPO: `invnetory`** → `ret_other_inventory_item` |
| `amount` | DECIMAL | Per-piece amount |
| `piece` | INT | Number of pieces (always 1 per row) |
| `item_ref_no` | VARCHAR | Unique tag code e.g., "1-A00001" |
| `current_branch` | INT FK | → `branch.id_branch` |
| `ref_no` | VARCHAR | Batch ref (timestamp at tagging time) |
| `status` | TINYINT | 0=Available, 1=Issued |
| `id_inventory_issue` | INT FK | → `ret_other_invnetory_issue` (set on issue) |

**Key:** This is the "piece-level" table. Every physical item has ONE row here.

---

### 8. `ret_other_inventory_purchase_items_log` — Movement Log
| Column | Type (inferred) | Notes |
|---|---|---|
| `id_item_log` | INT PK AUTO_INCREMENT | |
| `item_id` | INT FK | → `ret_other_inventory_item.id_other_item` |
| `no_of_pieces` | INT | Count for this movement |
| `amount` | DECIMAL | Value for this movement |
| `date` | DATE/DATETIME | Movement date |
| `status` | TINYINT | **0=Inward** (purchase/receipt), **1=Issue** (to customer via OI module or billing), **3=BT Other Issue** (Branch Transfer `is_other_issue=1`), **4=BT In-Transit** (items removed from source, pending branch download) |
| `from_branch` | INT FK | Source branch (NULL for inward) |
| `to_branch` | INT FK | Dest branch (NULL for issue) |
| `id_inventory_issue` | INT FK | → `ret_other_invnetory_issue` (if issue) |
| `created_on` | DATETIME | |
| `created_by` | INT FK | |

**Key table for all stock reports.** Status values 3 and 4 are referenced in outward calculation but meaning is undocumented.

---

### 9. `ret_other_inventory_purchase_images` — Purchase Images
| Column | Type (inferred) | Notes |
|---|---|---|
| `otr_inven_pur_id` | INT FK | → `ret_other_inventory_purchase` |
| `image` | VARCHAR | Filename in `assets/img/purchase_entry/` |
| `date_add` | DATETIME | |

---

### 10. `ret_other_invnetory_issue` — Issue Records
⚠️ **TYPO in table name: `invnetory` (missing 'n' in inventory)**

| Column | Type (inferred) | Notes |
|---|---|---|
| `id_inventory_issue` | INT PK AUTO_INCREMENT | |
| `id_other_item` | INT FK | → `ret_other_inventory_item` |
| `issue_form` | INT | **1=Issued via Billing** (`admin_ret_billing` L4506), **2=Issued via OI Module directly** (OI controller L915). Determines the issue source/context. |
| `issue_date` | DATETIME | Entry date from day closing |
| `bill_id` | INT FK | → `ret_billing.bill_id` |
| `no_of_pieces` | INT | Total pieces issued |
| `id_branch` | INT FK | → `branch` |
| `remarks` | VARCHAR | User notes |
| `created_on` | DATETIME | |
| `created_by` | INT FK | |

---

### 11. `ret_other_inventory_product_link` — Product Mapping
| Column | Type (inferred) | Notes |
|---|---|---|
| `inv_des_id` | INT PK AUTO_INCREMENT | |
| `inv_pro_id` | INT FK | → `ret_product_master.pro_id` |
| `inv_des_otheritemid` | INT FK | → `ret_other_inventory_item.id_other_item` |
| `inv_des_created_by` | INT FK | |
| `inv_des_created_on` | DATETIME | |

---

### 12. `gift_mapping` — Gift Scheme Mapping
| Column | Type (inferred) | Notes |
|---|---|---|
| `id_other_item` | INT FK | → `ret_other_inventory_item` |
| `id_scheme` | INT FK | → scheme table |
| `item_issue_limit` | INT | Max pieces per scheme |
| `date_add` | DATETIME | |
| `from_ins` | INT | Installment from |
| `to_ins` | INT | Installment to |
| `created_by` | INT FK | |

**Shared table** — used by both Other Inventory and Scheme/Chit modules.

---

## Part B: Referenced Tables (Read-Only)

| Table | PK | Referenced For |
|---|---|---|
| `branch` | `id_branch` | Branch name, HO designation |
| `ret_karigar` | `id_karigar` | Supplier info (karigar_for=4) |
| `customer` | `id_customer` | Customer name + mobile |
| `employee` | `id_employee` | Employee name in reports |
| `ret_billing` | `bill_id` | Active bills for issue |
| `ret_product_master` | `pro_id` | Products for mapping |
| `ret_uom` | `uom_id` | Unit of measure name |
| `ret_day_closing` | `id_branch` | Entry date logic |
| `ret_branch_transfer_other_inventory` | — | Branch-transferred pieces |
| `ret_branch_transfer` | `branch_transfer_id` | Transfer status |
| `state`, `city`, `country` | respective PKs | Supplier address for print |

---

## Schema Risk Summary

| Risk | Table | Detail |
|---|---|---|
| 🔴 SQL Injection | All OI tables | Raw string interpolation in all model queries |
| 🟠 Typo in table name | `ret_other_invnetory_issue` | "invnetory" consistently misspelled |
| 🟠 Typo in column | `ret_other_inventory_purchase_items_details.other_invnetory_item_id` | FK column name has typo |
| 🟠 Missing FK constraints | Most relationships | No CASCADE deletes — orphan risk |
| 🟡 stock_id_uom not updated | `ret_other_inventory_item` | Edit flow omits this column |
| 🟡 Phase status undocumented | `ret_other_inventory_purchase_items_log.status` | Values 3 & 4 not explained in code |
