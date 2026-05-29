# Business Rules - Branch Transfer

> **Round 9 Update** — 2026-03-24
> Added 2 new rules (BRT-014, BRT-015) from FLOW_RISK_MATRIX contract gap analysis.

---

## RULE-BRT-001: Transfer Types
**Rule:** Branch Transfers handle 5 distinct item types:
1 = Tagged Items
2 = Non-Tagged Items
3 = Old Metal / Sales Return / Partly Sale
4 = Packaging Items
5 = Order (BT Order Log)
**Implementation:** `admin_ret_brntransfer->branch_transfer('save')` L108-L247
**Validation:** Server-side `if ($_POST['item_tag_type'] == X)` blocks

## RULE-BRT-002: Piece Count Integrity for Tagged Transfers
**Rule:** When saving a tagged transfer, the total number of pieces scanned/added (`$total_tag_pcs`) must exactly match the header-level `pieces` declaration. If it doesn't, the entire transaction is rolled back and considered failed.
**Implementation:** `admin_ret_brntransfer->branch_transfer('save')` L248-L256
**Validation:** Server-side `if ($total_tag_pcs == $_POST['pieces'])`

## RULE-BRT-003: Day Close Date Validation for Download
**Rule:** Stock cannot be downloaded/received by the destination branch if their Day Closing date is older than the sending branch's Day Closing date at the time of transit approval.
**Implementation:** `branch_transfer('updateStatus')` L322-L327, `branch_transfer('update_TagsByFilter_scan')` L963-L967
**Validation:** Server-side `if (strtotime($tb_entry_date) < strtotime($fb_entry_date) && $_POST['approval_type'] == 2)`
**Edge Cases:** Cross-midnight transits or environments where one branch lags in closing the day. Also enforced in scan-based download.

## RULE-BRT-004: Status Transitions
**Rule:**
* `approval_type = 1` = Transit Approval (Sender). Master status becomes 2.
* `approval_type = 2` = Stock Download (Receiver). Master status becomes 4.
* Cancel action sets master status to 3.
* Order log: Transit status = 2, Download status = 3.
**Implementation:** `updateStatus` L300-L301 and `update_branch_transfer_cancel` L1261-L1292
**State Diagram:** `1 (Pending) → 2 (Transit) → 4 (Downloaded)` or `1/2 → 3 (Cancelled)`

## RULE-BRT-005: OTP Requirement for Approval
**Rule:** A setting (`is_otp_required_for_approval`) dictates whether OTP verification is needed to perform a transfer approval. Supports two OTP types: standard (`send_otp`/`verify_otp`) and other-issue (`send_other_issue_otp`/`verify_other_issue_otp`). Also supports mobile app approval via `admin_app_api` endpoints.
**Implementation:** Loaded in `approval_list` view. OTP type controlled by `BT_otp_approval_type` profile setting.
**JS Functions:** `send_otp()` L4935, `verify_otp()` L5067, `send_other_issue_otp()` L5686, `verify_otherissue_otp()` L5752, `send_mobile_approval_request()` L8267

## RULE-BRT-006: Security Form Secret
**Rule:** Saves, Status Updates, and Scan Downloads must match a `$form_secret` against the session `FORM_SECRET` to prevent duplicate submissions or CSRF.
**Implementation:** `if (strcasecmp($form_secret, ($this->session->userdata('FORM_SECRET'))) === 0)` at save L87-L91, updateStatus L288-L293, scan download L942-L947

---

## RULE-BRT-007: Other Issue Tag Status Differentiation
**Rule:** When a transfer is marked as "Other Issue" (`isOtherIssue = 1`), downloaded tags receive `tag_status = 3` (Other Issue) instead of `tag_status = 0` (Available). Also, for non-tagged items, stock is NOT added to the receiving branch when `isOtherIssue = 1`.
**Implementation:** Controller `updateStatus` L350-L354 (tagged), L578/L607-L610 (non-tagged)
**Validation:** Server-side only — `if ($_POST['is_other_issue'] == 1)`
**Edge Cases:** What happens if a transfer is created as Other Issue but the status doesn't get properly tracked — tags could end up in limbo status 3.

## RULE-BRT-008: Packaging FIFO/LIFO Issue Preference
**Rule:** Packaging items are issued FIFO or LIFO based on `issue_preference` from `ret_other_inventory_item`. When `issue_preference = 1`, items are ordered `ASC` (FIFO). Otherwise, ordered `DESC` (LIFO). The issue selects individual purchase detail records up to the requested `total_pcs`.
**Implementation:** Model `get_other_inventory_purchase_items_details()` L1922, `get_other_inventory_download_pending_details()` L1932
**Validation:** Server-side only
**Edge Cases:** If available items < requested `total_pcs`, LIMIT will return fewer records — no explicit validation catches the shortfall.

## RULE-BRT-009: Packaging Amount Tracking
**Rule:** When packaging items transit, the `total_amount` is calculated by summing `amount` from each issued `ret_other_inventory_purchase_items_details` row. This amount is logged in `ret_other_inventory_purchase_items_log` for both transit (status=4) and download (status=0).
**Implementation:** Controller `updateStatus` Type 4 block L800-L841
**Tables:** `ret_other_inventory_purchase_items_details` (status update), `ret_other_inventory_purchase_items_log` (insert)

## RULE-BRT-010: Scan-Based Download Auto-Completion
**Rule:** When downloading tags by barcode scan, after each scan the system auto-checks if all pieces have been downloaded by comparing `btDetail.pieces == btDetail.downd_pcs`. If true, the master transfer status is automatically set to 4 (Downloaded) and `dwnload_datetime` is set.
**Implementation:** Controller `update_TagsByFilter_scan` L1021-L1030
**JS:** `getscan_TagSearchList()` L7897 — on success, checks `data.bt_status` to determine if transfer is complete
**Edge Cases:** If `downd_pcs` count gets out of sync with actual download records, the auto-completion may fire prematurely or never fire.

## RULE-BRT-011: Order Transfer Tag Handling
**Rule:** For Type 5 (Order) transfers, the system looks up the tag assigned to the repair order via `get_repair_order_tag_details()`. If a tag exists, it follows the same transit/download tag status logic as Type 1. If no tag exists (`tag_id = ''`), only the `customerorderdetails.current_branch` is updated.
**Implementation:** Controller `updateStatus` Type 5 block L844-L911
**Tables Written:** `ret_bt_order_log`, `customerorderdetails`, `ret_taging`, `ret_taging_status_log`, `ret_section_tag_status_log`

## RULE-BRT-012: Cancel Does NOT Reverse Stock
**Rule:** When a transfer is cancelled (status=3), only the master record status is updated. No reversal occurs for:
- Tag status changes (tags remain in-transit if already approved)
- Non-tag stock deductions/additions
- Old metal, SR, PS branch assignments
- Packaging quantity changes
**Implementation:** `update_branch_transfer_cancel` L1261-L1292 — only updates `ret_branch_transfer.status = 3`
**Risk:** CRITICAL — if a transfer has been transit-approved but then cancelled, stock remains in incorrect state. This is a data integrity gap.

## RULE-BRT-013: Non-Tag Head Office Exception
**Rule:** `isHeadOffice()` checks if a branch is the head office. For non-tagged downloads, if the from-branch IS the head office (no `ret_nontag_item` record exists for HO), the deduction step is skipped — only addition to the to-branch occurs.
**Implementation:** Model `isHeadOffice()` L354-L362, Controller `updateStatus` NT download L573/L596
**Validation:** Server-side — `if ($ntag['id_nontag_item'] != '')`

---

## RULE-BRT-014: Tag Availability Is Client-Side Validated Only
**Rule:** When adding tags to a branch transfer, the system relies entirely on `fetchTagsByFilter()` (which queries only `tag_status=0` tags) to ensure only available tags are shown. There is **no server-side guard** in `branch_transfer('save')` to verify that each submitted `tag_id` has `tag_status=0` at time of save. A crafted POST can submit a sold, in-transit, or deleted tag and it will be inserted without rejection.
**Risk:** Phantom transfer — tag appears in active transfer while simultaneously billed or in another transfer. Data integrity corruption.
**Implementation gap:** `admin_ret_brntransfer::branch_transfer('save')` L79–L283 — no `tag_status` pre-validation loop.
**Validation:** Client-side only (JS `fetchTagsByFilter` filters by status=0 before displaying)
**Discovered:** R8 FLOW_RISK_MATRIX.md §2 (Inbound Contracts) — FR-BRT-001
**Fix approach:** Add a pre-save loop: for each `tag_id` in the POST array, run `get_tag_details()` and verify `tag_status == 0`. Reject with error if any tag fails.

## RULE-BRT-015: Partly Sale / Sales Return Items Have No Duplicate-Transfer Guard
**Rule:** When saving a Type 3 (Old Metal/SR/PS) transfer, the `bill_det_id` (for SR) or `tag_id` (for PS) is inserted directly into `ret_brch_transfer_old_metal` without checking whether the same identifier is already present in another **active** transfer (status=1 or status=2). This means the same SR/PS item can appear in two parallel active branch transfers.
**Risk:** Double-transfer of the same bill item — once a second transfer is transit-approved for the same `bill_det_id`, `transferred_to_acc_stock` gets set twice, and the item gets double-counted in the receiving branch.
**Implementation gap:** `admin_ret_brntransfer::branch_transfer('save')` Type 3 path — no dedup check before `insertData()` to `ret_brch_transfer_old_metal`.
**Validation:** None — neither client-side nor server-side.
**Discovered:** R8 FLOW_RISK_MATRIX.md §2 (Inbound Contracts) — FR-BRT-002, FR-BRT-015
**Fix approach:** Before inserting each SR/PS item, query `ret_brch_transfer_old_metal JOIN ret_branch_transfer` to check if the same `sold_bill_det_id` or `tag_id` already exists in an active transfer (status IN (1,2)).
