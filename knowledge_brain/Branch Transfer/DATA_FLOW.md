# Data Flow - Branch Transfer

> **Round 10 Update** — 2026-03-24
> JS line numbers updated throughout (JS 8,786→4,483 lines in R10, dead duplicate blocks removed).
> **Round 4**: Added Flow 4 (PRINT), Flow 5 (Scan-Based Download), expanded Flow 2 with packaging/order sub-flows.

---

## Flow 1: CREATE (Save New Transfer)
**Entry Point:** POST `/admin_ret_brntransfer/branch_transfer/save` (AJAX)
**JS Function:** `add_to_trans()` at L3886 → collects form data → `$.ajax({url: .../branch_transfer/save})`
**Controller:** `branch_transfer('save')` L79–284

1. Checks CSRF token (`form_secret` matching session `FORM_SECRET`).
2. Generates transaction code using `trans_code_generator($is_eda)`.
3. Inserts master record into `ret_branch_transfer` table.
4. Branching logic based on `item_tag_type`:
    *   **Type 1 (Tagged):** Inserts items into `ret_brch_transfer_tag_items` validating with `get_tag_details()`. Fails if `$total_tag_pcs != $_POST['pieces']`.
    *   **Type 2 (Non-Tagged):** Inserts items into `ret_brch_transfer_non_tag_items` and updates master record counts (pieces, grs_wt, net_wt, id_nontag_receipt).
    *   **Type 3 (Old Metal, Partly Sale, Sales Return):** Inserts items into `ret_brch_transfer_old_metal`. Uses `trans_id` / `bill_det_id`. `item_type`: 1=Old Metal, 2=Sales Return, 3=Partly Sale.
    *   **Type 4 (Packaging Items):** Inserts into `ret_branch_transfer_other_inventory` with `id_other_inv_item` and `no_of_pcs`.
    *   **Type 5 (Order):** Extracts Day Close entry date from `getAllBranchDCData()` for `transfer_from_branch`. Inserts into `ret_bt_order_log` with `id_orderdetails`.
5. Commits transaction if successful, logs activity in `log_model` ("BT Entry" / "Add").
6. Returns JSON response `{'status': 1, 'trans_code': ..., 's_type': ...}`.

**Tables written:** `ret_branch_transfer`, `ret_brch_transfer_tag_items` OR `ret_brch_transfer_non_tag_items` OR `ret_brch_transfer_old_metal` OR `ret_branch_transfer_other_inventory` OR `ret_bt_order_log`

---

## Flow 2: EDIT / UPDATE STATUS (Approve Transit / Stock Download)
**Entry Point:** POST `/admin_ret_brntransfer/branch_transfer/updateStatus` (AJAX)
**JS Function:** `brnTransUpdStatus()` at L4734 → `approveBranchTransfer()` at L859 orchestrates
**Controller:** `branch_transfer('updateStatus')` L285–938

1. Checks CSRF token (`form_secret`).
2. Identifies Day Close date (`entry_date`) for `from_branch` and `to_branch` via `getAllBranchDCData()`.
3. Validates that `to_branch` Day Close date is not older than `from_branch` Day Close date for Stock Download (RULE-BRT-003).
4. Updates `status` in `ret_branch_transfer` (2 = Transit, 4 = Downloaded). Updates `approved_by`/`downloaded_by` + timestamps.
5. Iterates through transfer items based on `trans_type`:

### Type 1 — Tagged Items (L336–392)
- **Transit:** Updates `tag_status`=4 (In-Transit) + `current_branch`=to_branch in `ret_taging`. Logs in `ret_taging_status_log` + `ret_section_tag_status_log`.
- **Download:** Updates `tag_status`=0 (Available) or 3 (Other Issue, if `isOtherIssue`=1) in `ret_taging`. Updates `download_date`/`download_by` in `ret_brch_transfer_tag_items`. Logs status.

### Type 2 — Non-Tagged Items (L515–646)
- **Transit:** Fetches all NT items via `getBTnontags()`. Logs each in `ret_nontag_item_log` + `ret_section_nontag_item_log` with status 4.
- **Download:** For each NT item: checks `checkNonTagItemExist()` in to_branch. If exists → `updateNTData('-')` on from_branch, `updateNTData('+')` on to_branch (if not Other Issue). If not exists → `insertData` new `ret_nontag_item` for to_branch. Logs with status 0/3.

### Type 3 — Old Metal / Sales Return / Partly Sale (L647–793)
Three sub-loops iterate separately over `getBTOldMetalDetails`, `get_salesreturn_items`, `get_partlysale_items`:
- **Transit (Old Metal):** Inserts status=2 log in `ret_purchase_items_log`.
- **Download (Old Metal):** Updates `current_branch` + `is_transferred`=1 in `ret_bill_old_metal_sale_details`. Inserts status=1 log.
- **Transit (Sales Return, tagged):** Updates `trans_to_acc_stock`=1 in `ret_taging`. Logs in `ret_purchase_items_log`.
- **Transit (Sales Return, non-tagged):** Updates `transferred_to_acc_stock`=1 in `ret_bill_details`. Logs with `item_type`=9.
- **Download (Sales Return, tagged):** Updates `current_branch` in `ret_taging`. Logs status=1.
- **Download (Sales Return, non-tagged):** Updates `current_branch` in `ret_bill_details`. Logs with `item_type`=9.
- **Transit (Partly Sale):** Updates `trans_to_acc_stock`=1 in `ret_taging`. Logs with `item_type`=3.
- **Download (Partly Sale):** Updates `current_branch` in `ret_taging`. Logs status=1 with `item_type`=3.

### Type 4 — Packaging Items (L795–843)
- **Transit:** Gets `get_InventoryCategory()` → `get_other_inventory_purchase_items_details()` (FIFO/LIFO by `issue_preference`). Updates status=4 on each `ret_other_inventory_purchase_items_details` row. Sums `total_amount`. Logs in `ret_other_inventory_purchase_items_log`.
- **Download:** Gets `get_other_inventory_download_pending_details()` (status=4). Updates status=0, `current_branch`=to_branch. Logs in `ret_other_inventory_purchase_items_log`.

### Type 5 — Repair Orders (L844–911)
- Inserts `ret_bt_order_log` with status 2 (transit) or 3 (downloaded).
- Looks up `get_repair_order_tag_details()`. If tag exists:
  - **Transit:** Updates `tag_status`=4 in `ret_taging`, updates `current_branch` in `customerorderdetails`.
  - **Download:** Updates `tag_status`=0, `current_branch` in `ret_taging` and `customerorderdetails`.
  - Logs in `ret_taging_status_log` + `ret_section_tag_status_log`.
- If no tag: Just updates `current_branch` in `customerorderdetails`.

6. Commits transaction, logs "BT Approval" / "Status updated" in `log_model`.

---

## Flow 3: DELETE / CANCEL
**Entry Point:** POST `/admin_ret_brntransfer/update_branch_transfer_cancel`
**JS Function:** `update_branch_transfer_cancel()` at L5368
**Controller:** `update_branch_transfer_cancel()` L1262–1293

1. Receives array of `req_data` containing `branch_transfer_id`s.
2. Begins DB transaction.
3. Loops through each transfer and updates `status` = 3 in `ret_branch_transfer`.
4. Logs the cancellation operation in `log_model` ("Reject Branch Trasnfer" [sic]).
5. Commits transaction and returns alert message.

> ⚠️ **Gap:** Cancel does NOT reverse any stock changes. If a transfer was already in Transit (status=2), cancellation only updates the master — the tag/NT stock adjustments from the transit approval remain.

---

## Flow 4: PRINT (PDF Generation)
**Entry Point:** GET `/admin_ret_brntransfer/branch_transfer/print/{trans_code}/{s_type}/{print_type}`
**JS Trigger:** Link/button click from list page → direct URL navigation
**Controller:** `branch_transfer('print')` L1088–1129

1. Receives `$trans_code` (via `$id`), `$s_type`, `$print_type`.
2. Loads `admin_settings_model` for company details.
3. Fetches data:
   - `getBTransData($trans_code, $s_type, $print_type)` — master + child items
   - `getBTransDataSummary(...)` — summary (only if `$s_type == 1`)
   - `get_purchase_items_details(...)` — old metal/SR/PS detail for print
   - `get_company()` — company header info
4. Loads DomPDF helper.
5. Renders view based on `$print_type`:
   - **print_type = 3:** `branch_transfer/bt_category_print.php` (category-wise print — 96KB view)
   - **Other:** `branch_transfer/print.php` (standard print — 85KB view)
6. DomPDF renders HTML to PDF → streams as `btrans.pdf` (inline, not attachment).

**Print types:**
| `print_type` | View | Description |
|---|---|---|
| 1 | `print.php` | Summary print |
| 2 | `print.php` | Detailed print |
| 3 | `bt_category_print.php` | Category-wise grouped print |

**Tables read:** `ret_branch_transfer`, all child tables, `ret_taging`, `ret_products`, `def_design`, `ret_branches`, company settings

---

## Flow 5: SCAN-BASED DOWNLOAD (Tag-by-tag)
**Entry Point:** POST `/admin_ret_brntransfer/branch_transfer/update_TagsByFilter_scan` (AJAX)
**JS Function:** `getscan_TagSearchList()` at L7804 → triggered by `#scan_tag_no` keypress (Enter)
**Controller:** `branch_transfer('update_TagsByFilter_scan')` L940–1058

This is an **alternative download flow** where tags are downloaded one-by-one via barcode scan instead of bulk approval.

1. Checks CSRF token.
2. Calls `get_scan_tag_status($branch_trans_code, $tag_code)` — verifies tag is in-transit (status=4) and not already downloaded.
3. Validates Day Close dates (same rule as Flow 2 — RULE-BRT-003).
4. For each scanned tag (Type 1 only):
   - Updates `tag_status` (0 or 3) + `current_branch` in `ret_taging`.
   - Updates `download_date`/`download_by` in `ret_brch_transfer_tag_items`.
   - Logs in `ret_taging_status_log` + `ret_section_tag_status_log`.
5. **Auto-completion check:** After each scan, calls `getBTDetail()` and compares `pieces` vs `downd_pcs`. If equal → auto-updates master status to 4 (Downloaded) + sets `dwnload_datetime`.
6. Returns JSON with `bt_status` (true if all tags scanned) and `tag_details`.

> ⚠️ **Key difference from Flow 2:** Each tag gets its own transaction (`trans_begin`/`trans_commit`), and master status auto-updates when all pieces are scanned. Flow 2 does bulk approval.

---

## Flow 6: LIST / GRID DATA
**Entry Points:**
- **Transfer List:** POST `/admin_ret_brntransfer/branch_transfer/ajax` → `default` case L1139–1146
  - JS: `get_ajaxBranchTransferlist()` at L5410
  - Model: `get_ajaxBranchTransferlist($from_date, $to_date)`
  - Returns: list + profile data

- **Approval Pending:** POST `/admin_ret_brntransfer/branch_transfer/approval_pending` → L1130–1138
  - JS: `get_brantranTagged()`, `get_brantranNonTagged()`, `get_brantranOldMetal()`, `get_brantranPackagingItems()`, `get_branchtranOrderDetails()` (all call same endpoint with different `trans_type`)
  - Model: `getApprovalListing($data)` — 250-line method with complex conditional queries

- **Download Pending:** POST `/admin_ret_brntransfer/branch_transfer/download_pending` → L70–78
  - JS: `get_branch_transfer_download()`, `get_brantranTagged()`
  - Model: `get_download_data($data)` — 100-line method
