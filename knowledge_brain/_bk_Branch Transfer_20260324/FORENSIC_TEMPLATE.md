# Forensic Template - Branch Transfer

> **Round 9 Update** — 2026-03-24
> Added 3 new symptom/trace entries for R8 contract gaps (cancel reversal, tag availability, PS/SR duplicate transfer).

---

## Layer 1 — Symptom Collection
When investigating a Branch Transfer bug, first verify:
- [ ] What is the `item_tag_type`? (1=Tagged, 2=Non-Tagged, 3=Old Metal/SR/PS, 4=Packaging, 5=Order)
- [ ] What is the current `status` of the master record? (1=Pending, 2=Transit, 3=Cancelled, 4=Downloaded)
- [ ] What `approval_type` was being performed? (1=Transit Approval, 2=Stock Download)
- [ ] Is this a cross-branch issue? (What are `transfer_from_branch` and `transfer_to_branch`?)
- [ ] Is the "Other Issue" flag (`isOtherIssue`) = 1?
- [ ] Is the module in EDA mode (`is_eda` = 1)?
- [ ] Was this a bulk approval or scan-based download?
- [ ] What is the OTP approval type? (`BT_otp_approval_type` from profile)
- [ ] Is the `branch_transfer_download` setting enabled?
- [ ] **[R9 — Contract Gap 1]** If a tag is stuck in status=4 AND the master transfer status=3 (Cancelled): this is a cancel-post-transit reversal gap (Anti-Pattern #5). No code fixes it.
- [ ] **[R9 — Contract Gap 2]** If a tag in the transfer was NOT at tag_status=0 when the transfer was created: the tag was added via a crafted/race-condition POST (RULE-BRT-014). Check `ret_taging_status_log` for concurrent transitions.
- [ ] **[R9 — Contract Gap 3]** If the same PS/SR item appears in two active transfers: the duplicate was allowed due to missing server-side dedup (RULE-BRT-015). Query `ret_brch_transfer_old_metal` for the duplicate `sold_bill_det_id` or `tag_id`.

## Layer 2 — Reproduce & Isolate
- [ ] Does the issue happen during **Save** (CREATE), **Transit Approval** (UPDATE), **Stock Download** (UPDATE), or **Print** (READ)?
- [ ] Is the Day Close date for the receiving branch behind the sending branch?
- [ ] If a tag is stuck, what is its `tag_status` in `ret_taging`? (0=Available, 3=OtherIssue, 4=InTransit)
- [ ] Can you reproduce with a different `item_tag_type`?
- [ ] Does the bug appear in bulk approval but NOT in scan-based download (or vice versa)?
- [ ] Check if the form_secret has already been consumed (duplicate submission guard)
- [ ] For print bugs: Does it affect standard print (`print_type` 1/2) or category print (`print_type` 3)?

## Layer 3 — Client-Side Trace (JS)
In `ret_branch_transfer.js` (8,879 lines):

| Symptom | JS Function | What to Check |
|---|---|---|
| Items not adding to grid | `getTagSearchList()` L3311 | Check AJAX response from `getTagsByFilter`. Inspect `trans_data` array formation |
| Scan not working | `getscan_TagSearchList()` L7897 | Check `#scan_tag_no` keypress handler. Verify `branch_trans_code` and `tag_code` in payload |
| Save button does nothing | `add_to_trans()` L3979 | Check if validation passes. Look at `pieces`, `grs_wt`, `net_wt` values. Check for NaN |
| Approval fails silently | `approveBranchTransfer()` L859 | Check checkbox selection collection. Verify `trans_ids` array construction |
| OTP not sending | `send_otp()` L4935 | Check `is_otp_required_for_approval` hidden field value |
| Totals wrong | `calcTaggedApprList()` L593 / `calcNTaggedApprList()` L667 | Check arithmetic — `parseFloat()` applied? |
| Old metal items missing | `get_purchase_items()` L6390 | Check filters passed. Verify bill ID in payload |
| Packaging items empty | `get_invnetory_item()` (x-module) | Cross-module AJAX — check `admin_ret_other_inventory` endpoint |
| Download count wrong | `branch_download_by_scan()` L7815 | Check `actual_pcs_dnload` / `actual_weights_dnload` hidden fields |
| Duplicate tag error | `check_duplicates()` L8525 | Check tag_id comparison logic in transfer grid |

**Console Trace Points:**
```javascript
// Before save AJAX
console.log('trans_data:', JSON.stringify(trans_data));
console.log('item_tag_type:', $('#item_tag_type').val());
console.log('pieces:', $('#pieces').val(), 'grs_wt:', $('#grs_wt').val());

// Before approval AJAX
console.log('trans_ids:', JSON.stringify(trans_ids));
console.log('approval_type:', $('#approval_type').val());
console.log('form_secret:', $('#form_secret').val());
```

## Layer 4 — Server-Side Trace (PHP)
| Symptom | File | Method/Case | Line | What to Check |
|---|---|---|---|---|
| Piece count mismatch blocks save | `admin_ret_brntransfer.php` | `save` | L248-256 | Compare `$total_tag_pcs` vs `$_POST['pieces']` |
| "Form Already Submitted" error | `admin_ret_brntransfer.php` | `save` / `updateStatus` | L87-91 / L288-293 | `form_secret` mismatch — check session `FORM_SECRET` |
| Stock Download fails: DC date | `admin_ret_brntransfer.php` | `updateStatus` | L322-327 | `strtotime($tb_entry_date) < strtotime($fb_entry_date)` |
| Tag stuck in status 4 | `admin_ret_brntransfer.php` | `updateStatus` | L345-354 | Check `approval_type` 2 logic — is `isOtherIssue` correct? |
| Non-tag qty not added to branch | `admin_ret_brntransfer.php` | `updateStatus` | L564-605 | Check `checkNonTagItemExist()` — is `isExist['status']` TRUE? |
| Old metal not transferring | `admin_ret_brntransfer.php` | `updateStatus` | L647-684 | Check `getBTOldMetalDetails()` return. Verify `old_metal_sale_id` |
| Packaging amount = 0 | `admin_ret_brntransfer.php` | `updateStatus` | L800-819 | Check `get_other_inventory_purchase_items_details()` — FIFO/LIFO return empty? |
| Order tag not moving | `admin_ret_brntransfer.php` | `updateStatus` | L858-881 | `get_repair_order_tag_details()` returning empty tag_id |
| Scan download marks complete early | `admin_ret_brntransfer.php` | `update_TagsByFilter_scan` | L1023 | `btDetail['pieces'] == btDetail['downd_pcs']` — count sync? |
| Print shows wrong data | `admin_ret_brntransfer.php` | `print` | L1088-1129 | Check `$s_type`, `$print_type` params. Verify `getBTransData()` return |
| Cancel doesn't restore stock | `admin_ret_brntransfer.php` | `update_branch_transfer_cancel` | L1261-1292 | **By design** — see RULE-BRT-012. Only sets status=3. ALL transit-phase effects remain. |
| **[R9]** Tag stuck at status=4 + master status=3 | `ret_taging` (read) | Check `ret_taging.tag_status` + `ret_branch_transfer.status` | — | Cancel-post-transit reversal gap. Must manually reset `tag_status=0` and `current_branch` for affected tag_ids. Use Layer 5 Query #6 to identify affected transfers. |
| **[R9]** Tag in transfer with status ≠ 0 at save time | `admin_ret_brntransfer.php` | `save` | L79–L283 | No server-side tag_status guard. Verify `ret_taging.tag_status` for each `tag_id` in `ret_brch_transfer_tag_items`. See RULE-BRT-014. |
| **[R9]** Same SR/PS `bill_det_id` in two active transfers | `ret_brch_transfer_old_metal` (read) | — | — | Duplicate transfer. Query: `SELECT sold_bill_det_id, COUNT(*) FROM ret_brch_transfer_old_metal bom JOIN ret_branch_transfer bt ON bom.transfer_id=bt.branch_transfer_id WHERE bt.status IN (1,2) GROUP BY sold_bill_det_id HAVING COUNT(*) > 1`. See RULE-BRT-015. |
| Trans code generator duplicates | `ret_brntransfer_model.php` | `trans_code_generator` | L786-799 | Race condition if two users save simultaneously |
| `_error_message()` leaked to client | `admin_ret_brntransfer.php` | `save` | L272-273 | `$this->db->_error_message()` exposed — information leak |

## Layer 5 — Database Verification
```sql
-- 1. Full Transaction Trace (Master + ALL child types)
SELECT bt.*, 'HEADER' as record_type
FROM ret_branch_transfer bt
WHERE bt.branch_trans_code = '{BT_CODE}'
UNION ALL
SELECT btt.transfer_id, btt.tag_id, btt.id_section, NULL, NULL, NULL, NULL,
       NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
       'TAG_ITEM' as record_type
FROM ret_brch_transfer_tag_items btt
WHERE btt.transfer_id = (SELECT branch_transfer_id FROM ret_branch_transfer WHERE branch_trans_code = '{BT_CODE}');

-- 2. Tagged items vs master piece count (integrity check)
SELECT bt.branch_trans_code, bt.pieces as header_pcs,
       COUNT(btt.tag_id) as actual_tag_count
FROM ret_branch_transfer bt
LEFT JOIN ret_brch_transfer_tag_items btt ON bt.branch_transfer_id = btt.transfer_id
WHERE bt.transfer_item_type = 1
GROUP BY bt.branch_transfer_id
HAVING bt.pieces != COUNT(btt.tag_id);

-- 3. Orphan check (child items without master)
SELECT 'tag_items' as table_name, COUNT(*) as orphan_count
FROM ret_brch_transfer_tag_items
WHERE transfer_id NOT IN (SELECT branch_transfer_id FROM ret_branch_transfer)
UNION ALL
SELECT 'non_tag_items', COUNT(*)
FROM ret_brch_transfer_non_tag_items
WHERE transfer_id NOT IN (SELECT branch_transfer_id FROM ret_branch_transfer)
UNION ALL
SELECT 'old_metal', COUNT(*)
FROM ret_brch_transfer_old_metal
WHERE transfer_id NOT IN (SELECT branch_transfer_id FROM ret_branch_transfer)
UNION ALL
SELECT 'packaging', COUNT(*)
FROM ret_branch_transfer_other_inventory
WHERE branch_transfer_id NOT IN (SELECT branch_transfer_id FROM ret_branch_transfer);

-- 4. Tags stuck in transit > 7 days
SELECT bt.branch_trans_code, t.tag_id, t.tag_status, t.current_branch,
       bt.created_time, DATEDIFF(NOW(), bt.created_time) as days_stuck
FROM ret_taging t
JOIN ret_brch_transfer_tag_items btt ON t.tag_id = btt.tag_id
JOIN ret_branch_transfer bt ON btt.transfer_id = bt.branch_transfer_id
WHERE t.tag_status = 4
  AND bt.status = 2
  AND bt.created_time < (NOW() - INTERVAL 7 DAY);

-- 5. Non-tag weight integrity (header vs child sum)
SELECT bt.branch_trans_code, bt.grs_wt as header_grs, bt.net_wt as header_net,
       SUM(nt.grs_wt) as child_grs, SUM(nt.net_wt) as child_net
FROM ret_branch_transfer bt
JOIN ret_brch_transfer_non_tag_items nt ON bt.branch_transfer_id = nt.transfer_id
WHERE bt.transfer_item_type = 2
GROUP BY bt.branch_transfer_id
HAVING ABS(bt.grs_wt - SUM(nt.grs_wt)) > 0.01
    OR ABS(bt.net_wt - SUM(nt.net_wt)) > 0.01;

-- 6. Cancelled transfers that had been transit-approved (potential stock desync)
SELECT bt.branch_trans_code, bt.status, bt.approved_by,
       bt.approved_datetime, bt.updated_time as cancel_time
FROM ret_branch_transfer bt
WHERE bt.status = 3
  AND bt.approved_by IS NOT NULL;

-- 7. Packaging FIFO/LIFO integrity check
SELECT bt.branch_trans_code, bto.id_other_inv_item, bto.no_of_pcs,
       (SELECT COUNT(*) FROM ret_other_inventory_purchase_items_details
        WHERE other_invnetory_item_id = bto.id_other_inv_item AND status = 4) as items_in_transit
FROM ret_branch_transfer bt
JOIN ret_branch_transfer_other_inventory bto ON bt.branch_transfer_id = bto.branch_transfer_id
WHERE bt.transfer_item_type = 4 AND bt.status = 2;
```

## Layer 6 — Root Cause Classification
| Category | Risk | Module Specifics | RULE Ref |
|---|---|---|---|
| **Data Integrity** | HIGH | Pieces/Weight mismatch between header and child item rows | BRT-002 |
| **State Desync** | HIGH | `ret_taging.tag_status` vs `ret_branch_transfer.status` out of sync | BRT-004 |
| **Concurrency** | MED | Two users at different branches approving/downloading simultaneously | BRT-004 |
| **Cancel Orphan** | HIGH | Cancel doesn't reverse stock — tags/NT stuck in wrong state | BRT-012 |
| **Day Close Gap** | MED | Temporal desync between branches blocks downloads silently | BRT-003 |
| **Duplicate Submit** | LOW | form_secret can fail if session resets between page load and submit | BRT-006 |
| **SQL Injection** | HIGH | 5 model methods use raw `$this->db->query()` with string concat | SCHEMA Part C |
| **Info Leak** | MED | `$this->db->_error_message()` exposed to client on transaction failure | L272-273, L932 |

---

## Layer 7a — Approval Flow Trace (Multi-step)
This module has a 3-step workflow: **Create → Transit Approve → Stock Download**

| Step | Trigger | Status | Who | Validates | Tables Modified |
|---|---|---|---|---|---|
| Create | `save` AJAX | 1 (Pending) | Any user | form_secret, piece count (Type 1) | Master + child table |
| Transit Approve | `updateStatus` (type=1) | 2 (Transit) | Sender branch | form_secret, OTP (if enabled) | Master status, tag/NT status, logs |
| Stock Download | `updateStatus` (type=2) | 4 (Downloaded) | Receiver branch | form_secret, Day Close dates, OTP | Master status, tag/NT stock, logs |
| Cancel | `update_branch_transfer_cancel` | 3 (Cancelled) | Any authorized | None beyond auth | Master status ONLY |

**Diagnostic questions:**
- [ ] Was the correct `approval_type` (1 or 2) sent?
- [ ] Did OTP verification pass before calling `updateStatus`?
- [ ] For scan-download: was `update_TagsByFilter_scan` used instead of `updateStatus`?
- [ ] Is the transfer doing a valid state transition? (1→2, 2→4, 1/2→3 are valid; 4→anything is invalid)
- [ ] Check `ret_taging_status_log` for the tag's full movement history

## Layer 7b — Stock Integrity (Inventory)
Branch Transfer moves stock between branches. Verify stock balance after any bug:

**Tagged Items:**
```sql
-- Tag should be at exactly ONE branch with correct status
SELECT tag_id, tag_status, current_branch,
       (SELECT COUNT(*) FROM ret_brch_transfer_tag_items WHERE tag_id = t.tag_id
        AND transfer_id IN (SELECT branch_transfer_id FROM ret_branch_transfer WHERE status IN (1,2))) as pending_transfers
FROM ret_taging t
WHERE tag_id = {TAG_ID};
-- Expected: pending_transfers = 0 if tag_status = 0. pending_transfers = 1 if tag_status = 4
```

**Non-Tagged Items:**
```sql
-- Qty at branch should match accounting (no negative stock)
SELECT nt.id_nontag_item, nt.branch, nt.no_of_piece, nt.gross_wt, nt.net_wt,
       b.branch_name
FROM ret_nontag_item nt
JOIN ret_branches b ON nt.branch = b.id_branch
WHERE nt.product = {PRODUCT_ID} AND nt.design = {DESIGN_ID}
ORDER BY nt.branch;
-- Check: no negative values, sum across branches should match total issued
```

**Packaging:**
```sql
-- Packaging items status check (0=available, 4=in-transit)
SELECT item_id, status, current_branch, COUNT(*) as count
FROM ret_other_inventory_purchase_items_details
WHERE other_invnetory_item_id = {ITEM_ID}
GROUP BY status, current_branch;
-- Check: no items permanently stuck in status 4
```

## Layer 7c — Print Template Trace
When print output is wrong:

| Symptom | Check |
|---|---|
| Wrong items shown | Verify `$s_type` and `$print_type` params in URL — `print/{trans_code}/{s_type}/{print_type}` |
| Missing items in print | Check `getBTransData()` query — does it filter by `transfer_item_type`? |
| Summary missing | `getBTransDataSummary()` only runs when `$s_type == 1` |
| Old metal details missing | `get_purchase_items_details()` returns empty — check `$trans_code` param |
| Category print wrong layout | `bt_category_print.php` (96KB) — check category grouping logic |
| PDF won't open | DomPDF render failure — check `$html` output for invalid HTML chars |
| Portrait/Landscape wrong | `$dompdf->set_paper("a4", "portriat")` — note: "portriat" is a ⚠️ typo (should be "portrait") but DomPDF may accept it |
