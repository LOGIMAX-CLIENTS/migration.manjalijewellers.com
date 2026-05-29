# Schema Analysis — Branch Transfer

> **Updated**: Round 10 (Upgrade) — 2026-03-24
> R10: `ret_billing_item_stones` corrected from "active only at L1553" to ACTIVE across 4 methods (confirmed in live model scan). `metal` table added (new, used in `get_purchase_items` L1466 for metal-agnostic loop). Part B count: 28 → 29.
> **R9**: Added 2 previously missing referenced tables. Part B count: 26 → 28.
> **Original**: Round 2 — 2026-03-11.

---

## Part A: Owned Tables (Direct Write)

### 1. `ret_branch_transfer` — Master Table
| Purpose | Primary transfer record — one row per branch transfer transaction |
|---|---|
| **Key Columns** | `branch_transfer_id` (PK), `branch_trans_code` (unique), `transfer_from_branch` (FK→ret_branches), `transfer_to_branch` (FK→ret_branches), `transfer_item_type` (1-5), `pieces`, `grs_wt`, `net_wt`, `status` (1=Pending, 2=Transit, 3=Cancelled, 4=Downloaded), `is_other_issue` (0/1), `is_eda` (0/1), `create_by`, `approved_by`, `downloaded_by`, `created_time`, `approved_datetime`, `dwnload_datetime`, `remark`, `form_secret`, `id_nontag_receipt` |
| **Write Operations** | INSERT (save), UPDATE (updateStatus, cancel — sets status) |
| **Indexes** | PK on `branch_transfer_id`, unique on `branch_trans_code` |
| **Risks** | `status` is the central state driver — concurrent updates possible if two users act on same transfer |

### 2. `ret_brch_transfer_tag_items` — Tagged Child Table
| Purpose | One row per tagged item in a transfer |
|---|---|
| **Key Columns** | `transfer_id` (FK→ret_branch_transfer), `tag_id` (FK→ret_taging), `tag_remark`, `id_lot_inward_detail`, `id_section`, `download_date`, `download_by`, `created_on` |
| **Write Operations** | INSERT (save Type 1), UPDATE via `updateDatamulti` (download_date/download_by on stock download) |
| **Risks** | No unique constraint on (`transfer_id`, `tag_id`) — duplicate tag inserts possible |

### 3. `ret_brch_transfer_non_tag_items` — Non-Tagged Child Table
| Purpose | One row per non-tagged item line in a transfer |
|---|---|
| **Key Columns** | `transfer_id` (FK→ret_branch_transfer), `id_lot_inward_detail`, `id_nontag_item` (FK→ret_nontag_item), `id_nontag_receipt`, `pieces`, `grs_wt`, `net_wt`, `created_by`, `created_date` |
| **Write Operations** | INSERT (save Type 2) |
| **Risks** | Weights are stored as-entered without server-side rounding |

### 4. `ret_brch_transfer_old_metal` — Old Metal / SR / PS Child Table
| Purpose | One row per old metal / sales return / partly sale item |
|---|---|
| **Key Columns** | `transfer_id` (FK→ret_branch_transfer), `item_type` (1=Old Metal, 2=Sales Return, 3=Partly Sale), `old_metal_sale_id`, `tag_id`, `sold_bill_det_id`, `is_non_tag`, `gross_wt`, `net_wt` |
| **Write Operations** | INSERT (save Type 3) |
| **Risks** | `item_type` magic numbers (1/2/3) — must match controller logic exactly |

### 5. `ret_branch_transfer_other_inventory` — Packaging Child Table
| Purpose | One row per packaging item in a transfer |
|---|---|
| **Key Columns** | `branch_transfer_id` (FK→ret_branch_transfer), `id_other_inv_item` (FK→ret_other_inventory_item), `no_of_pcs` |
| **Write Operations** | INSERT (save Type 4) |
| **Risks** | No validation that `no_of_pcs` doesn't exceed available stock before insert |

---

## Part B: Referenced Tables (Read or Write via update during status transitions)

### Log Tables (Write-only during transitions)

| # | Table | Read By | Written By | Purpose |
|---|---|---|---|---|
| 1 | `ret_taging_status_log` | `get_transit_status` | `insertData` (updateStatus) | Tracks all tag status changes during transit/download |
| 2 | `ret_section_tag_status_log` | — | `insertData` (updateStatus) | Section-level tag movement logs |
| 3 | `ret_nontag_item_log` | — | `insertData` (updateStatus) | Non-tag transit/download logs |
| 4 | `ret_section_nontag_item_log` | — | `insertData` (updateStatus) | Section-level NT movement logs |
| 5 | `ret_purchase_items_log` | — | `insertData` (updateStatus) | Old metal/SR/PS movement logs (status: 1=Inward, 2=Intransit) |
| 6 | `otp_logs` | `verify_otp`, `verify_other_issue_otp` | `insertData` (send_otp) | OTP verification records |

### Inventory Tables (Read/Write during stock operations)

| # | Table | Read By | Written By | Purpose |
|---|---|---|---|---|
| 7 | `ret_taging` | `fetchTagsByFilter`, `fetchEstiTagsByFilter`, `fetchTagsByFilter_scan`, `get_tag_details`, many more | `updateData` (tag_status, current_branch, trans_to_acc_stock) | Master tag inventory — status 0/3/4 changes |
| 8 | `ret_nontag_item` | `fetchNonTaggedItems`, `checkNonTagItemExist`, `getNontagItemId` | `updateNTData` (+/−), `insertData` (new NT at to_branch) | Non-tag stock balances — arithmetic add/subtract |
| 9 | `ret_bill_details` | `get_purchase_items`, `get_partly_sale_details`, `get_sales_ret_details` | `updateData` (current_branch, transferred_to_acc_stock) | Sales bill detail — branch transfer flag |
| 10 | `ret_bill_old_metal_sale_details` | `old_metal_bill_details`, `get_purchase_items` | `updateData` (current_branch, is_transferred) | Old metal sale details — branch transfer flag |
| 11 | `ret_other_inventory_items` | `check_other_inventory_qty` | (controller direct update during packaging transit/download) | Packaging stock balances |
| 12 | `ret_other_inventory_purchase_items_details` | `get_other_inventory_purchase_items_details`, `get_other_inventory_download_pending_details` | (controller direct update) | Individual packaging item tracking |

### Master/Reference Tables (Read-only)

| # | Table | Read By | Purpose |
|---|---|---|---|
| 13 | `ret_branches` | `getBTBranches`, `get_verifMobNo`, `isHeadOffice`, `get_headoffice_branch` | Branch dropdown, OTP mobile, HO check |
| 14 | `ret_settings` | `getSettigsByName` | Config: `other_issue_branch`, `is_otp_required_for_approval`, `branch_transfer_download` |
| 15 | `ret_products` | `getProductsByFilter`, various joins | Product reference data |
| 16 | `def_design` | `getDesignByFilter`, various joins | Design reference data |
| 17 | `ret_lot_inward` | `getLotsByFilter`, `getNonTagReceiptedLots`, `fetchNonTaggedReceiptedItems` | Lot inward data |
| 18 | `ret_repair_orders_details` | `getRepairOrderDetails` | Repair order data for Type 5 transfers |
| 19 | `ret_repair_orders_trans_data` | — (write only: `update_branch_transfer_order_trans_data`) | Transfer→order mapping |
| 20 | `ret_bt_order_log` | `getBTOrders` | BT order tracking |
| 21 | `ret_other_inventory_item` | `get_InventoryCategory` | Packaging item master |
| 22 | `ret_other_inventory_item_type` | `get_InventoryCategory` | Packaging item type |
| 23 | `ret_other_issue_types` | `get_other_issue_types` | Other issue type master |
| 24 | `profile` | `get_profile_details` | User profile data |
| 25 | `profile_settings` | `get_profile_settings` | User preferences |
| 26 | `users` | `getApprovalListing` (join) | User names for approved_by display |
| 27 | `ret_billing_item_stones` | `get_purchase_items()` L1553, `get_partly_sale_details()` L1642+, `get_sales_ret_details()` L1704+, `get_purchase_items_details()` L1804/1832/1866 | Stone/diamond weight per bill line — calculates `dia_wt` in OM/SR/PS transfer items. ✅ **ACTIVE** in all 4 OM/PS/SR methods. |
| 28 | `ret_partlysold` | Dead-code stub `get_partly_sale_details(4-param)` L1317 — **commented-out block only** | Partly-sold weight tracking. ⚠️ Not used in active code (5-param version replaced with direct calculation). |
| 29 | `metal` | `get_purchase_items()` L1466 | Metal master table (`metal_status=1` filter). ✅ **New in R10** — replaces old Gold/Silver if-else hardcode. Used for metal-agnostic PS/SR loop. |

---

## Part C: Security Concerns

| # | Risk | Table(s) | Method(s) | Severity |
|---|---|---|---|---|
| 1 | **Raw SQL with string concatenation** | `ret_other_inventory_item`, `ret_other_inventory_item_type` | `get_InventoryCategory` | HIGH — SQL injection if `$id_other_item_type` is user-controlled |
| 2 | **Raw SQL with string concatenation** | `ret_other_inventory_purchase_items_details` | `get_other_inventory_purchase_items_details`, `get_other_inventory_download_pending_details` | HIGH — all 4 params injected |
| 3 | **Raw SQL with string concatenation** | `ret_bt_order_log` | `getBTOrders` | MEDIUM — `$trans_id` from POST data |
| 4 | **Raw SQL with string concatenation** | `ret_taging` | `get_repair_order_tag_details` | HIGH — 4 params injected with quotes |
| 5 | **No unique constraint** | `ret_brch_transfer_tag_items` | `insertData` | MEDIUM — duplicate tags possible |
| 6 | **No stock validation on insert** | `ret_branch_transfer_other_inventory` | save Type 4 | MEDIUM — could oversell packaging |
