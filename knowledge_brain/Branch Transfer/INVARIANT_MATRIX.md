# Invariant Matrix - Branch Transfer

> **Round 5 Update** — 2026-03-11
> Added `is_eda`, `print_type`, `BT_otp_approval_type`, `branch_transfer_download` dimensions + cross-product grids.

---

## Dimension A: `item_tag_type` (Transfer Type) — PRIMARY
Controls table routing, validation, and approval logic for the entire module.

| Value | Type | Child Table | Save Validation | Approval Logic | Tables Modified on Download |
|---|---|---|---|---|---|
| **1** | Tagged | `ret_brch_transfer_tag_items` | `$total_tag_pcs == $_POST['pieces']` | Updates `ret_taging` (status 0/3/4) + logs | `ret_taging`, `ret_taging_status_log`, `ret_section_tag_status_log` |
| **2** | Non-Tagged | `ret_brch_transfer_non_tag_items` | Iterative — updates header weights/pcs | `checkNonTagItemExist()` → INSERT or UPDATE `ret_nontag_item` (+/−) | `ret_nontag_item`, `ret_nontag_item_log`, `ret_section_nontag_item_log` |
| **3** | Old Metal / SR / PS | `ret_brch_transfer_old_metal` | No specific — connects to `trans_id`/`bill_det_id` | Updates `current_branch` in source tables + purchase logs | `ret_bill_old_metal_sale_details`, `ret_bill_details`, `ret_taging`, `ret_purchase_items_log` |
| **4** | Packaging | `ret_branch_transfer_other_inventory` | No specific — connects to `id_other_inv_item` | FIFO/LIFO issue preference. Status 0→4→0. Amount tracked. | `ret_other_inventory_purchase_items_details`, `ret_other_inventory_purchase_items_log` |
| **5** | Order | `ret_bt_order_log` | Binds to `id_orderdetails` | Look up tag via `get_repair_order_tag_details()`. Update order table. | `customerorderdetails`, `ret_taging` (if tag exists), `ret_bt_order_log` |

## Dimension B: `approval_type` (State Transition)

| Value | Actor | Master Status | Timestamp Field | User Field | Day Close Check |
|---|---|---|---|---|---|
| **1** | Transit Approval (Sender) | 2 | `approved_datetime` | `approved_by` | Uses `fb_entry_date` |
| **2** | Stock Download (Receiver) | 4 | `dwnload_datetime` | `downloaded_by` | Must pass: `tb_entry_date >= fb_entry_date` |
| **Cancel** | Rejection | 3 | `updated_time` | — | None |

## Dimension C: `isOtherIssue` (Issue Context)

| Value | Tag Download Status | NT Stock Add to Receiver? | NT Log Status | Tag Log `from_branch` |
|---|---|---|---|---|
| **0** (Standard) | `tag_status = 0` (Available) | ✅ Yes — `updateNTData('+')` | `0` | Actual from_branch |
| **1** (Other Issue) | `tag_status = 3` (Other Issue) | ❌ No — skipped | `3` | `NULL` |

## Dimension D: `is_eda` (Mode Flag)
Controls the transaction code prefix and potentially different approval workflows.

| Value | Trans Code Prefix | Behavior |
|---|---|---|
| **0** | Standard prefix | Standard BT flow |
| **1** | EDA prefix | EDA (Express Direct Approval) flow — set as default in form.php hidden field |

## Dimension E: `print_type` (Print Output)

| Value | View File | Paper | Description |
|---|---|---|---|
| **1** | `branch_transfer/print.php` (85KB) | A4 portrait | Summary print |
| **2** | `branch_transfer/print.php` (85KB) | A4 portrait | Detailed print |
| **3** | `branch_transfer/bt_category_print.php` (96KB) | A4 portrait | Category-wise grouped print |

> Note: `s_type` in print URL controls whether `getBTransDataSummary()` runs. Only `s_type=1` triggers it.

## Dimension F: `BT_otp_approval_type` (OTP Mechanism)

| Value | OTP Mechanism | JS Function | Controller Method |
|---|---|---|---|
| **Standard** | SMS OTP via `sms_model` | `send_otp()` L4935 → `verify_otp()` L5067 | `send_otp()` → `verify_otp()` |
| **Mobile App** | Mobile app push → polling | `send_mobile_approval_request()` L8267 → `get_approval_status()` L8741 | `admin_app_api/bt_approval_otp_req` → `admin_app_api/get_approval_status` |
| **Other Issue** | SMS OTP (separate flow) | `send_other_issue_otp()` L5686 → `verify_otherissue_otp()` L5752 | `send_other_issue_otp()` → `verify_other_issue_otp()` |

## Dimension G: `branch_transfer_download` (Download Feature Toggle)

| Value | Behavior |
|---|---|
| **Enabled** | Download section visible in approval_list.php; scan-based download available |
| **Disabled** | Download section hidden; only transit approval is possible |

---

## Cross-Product Grid: `item_tag_type` × `approval_type`

| | Type 1 (Tagged) | Type 2 (Non-Tag) | Type 3 (Old Metal/SR/PS) | Type 4 (Packaging) | Type 5 (Order) |
|---|---|---|---|---|---|
| **Transit (1)** | tag_status→4, current_branch→to | Log only (status=4) | Purchase log (status=2), trans_to_acc_stock=1 | Status→4 (FIFO/LIFO), amount logged | BT order log (status=2), tag_status→4 if tag exists |
| **Download (2)** | tag_status→0/3, download_date set | NT qty: −from +to (or INSERT new) | current_branch→to, purchase log (status=1) | Status→0, current_branch→to, amount logged | BT order log (status=3), tag_status→0 if tag exists |
| **Cancel** | ⚠️ No reversal | ⚠️ No reversal | ⚠️ No reversal | ⚠️ No reversal | ⚠️ No reversal |

> ⚠️ **Cancel × Any Type**: Master status = 3 but NO stock/log reversals occur. This is a documented data integrity gap (RULE-BRT-012).

## Cross-Product Grid: `item_tag_type` × `isOtherIssue`

| | isOtherIssue = 0 | isOtherIssue = 1 |
|---|---|---|
| **Type 1 (Tagged)** | Download → tag_status = 0 (Available) | Download → tag_status = 3 (Other Issue) |
| **Type 2 (Non-Tag)** | Download → stock added to receiver branch | Download → stock NOT added (deducted from sender only) |
| **Type 3 (Old Metal)** | Standard flow | N/A (not applicable for Old Metal) |
| **Type 4 (Packaging)** | Standard flow | N/A (not applicable for Packaging) |
| **Type 5 (Order)** | Standard flow | N/A (not applicable for Orders) |
