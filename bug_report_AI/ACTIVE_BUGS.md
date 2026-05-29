# Active Bug Tracker

> **Live dashboard of all in-flight bugs.** Updated automatically by workflows.
> Last Updated: 2026-03-28

## In-Progress Bugs

| Bug ID | Module | Severity | Track | Current Step | Workflow | Developer | Started | GitHub Issue |
| RPT-ADV-001 | Reports / Advance | P1 | B (Business) | Step 7: APPROVAL GATE | `/fix-single-bug` | Antigravity | 2026-04-10 | TBD |
|---|---|---|---|---|---|---|---|---|
| RPT-CLT06 | Reports / Settings | P1 | A (System) | Step 1: DIAGNOSE | `/fix-single-bug` | Antigravity | 2026-04-09 | TBD |
| RPT-SD01 | Reports | P1 | A (System) | Step 8: CLOSE | `/fix-single-bug` | Antigravity | 2026-04-03 | TBD |
| RPT-CLT02 | Reports | P1 | A (System) | Step 7: APPROVAL | `/fix-single-bug` | Antigravity | 2026-04-29 | TBD |
| BRN-D03 | Branch Transfer | P0 | A (System) | Step 1: DIAGNOSE | `/fix-architecture-bug` | Antigravity | 2026-03-12 | TBD |
| BIL-CLT02 | Billing | P1 | A (System) | Triaged — Awaiting Fix | `/fix-architecture-bug` | — | 2026-03-05 | TBD |
| **TAG-CLT03** | **Tagging** | **P1** | **A (System)** | **Step 7: APPROVAL GATE** | `/fix-single-bug` | Antigravity | 2026-04-18 | TBD |

| PUR-CR02 | Purchase | P2 | B (Business) | Triaged — Awaiting Implementation | `/fix-business-bug` | — | 2026-02-27 | [#1040](https://github.com/Logimax-Technologies/etail_development_src/issues/1040) |
| PUR-CR04 | Purchase | P2 | A (System) | Triaged — Awaiting Implementation | `/fix-architecture-bug` | dev-rudra-619 | 2026-03-11 | [#1198](https://github.com/Logimax-Technologies/etail_development_src/issues/1198) |



---


| Bug ID    | Title | Module | Category | Date Fixed | PR / Branch | Notes |
|-----------|-------|--------|----------|------------|-------------|-------|
| BRN-D01   | getProductsByFilter() Variable Mismatch Crash | Branch | Variable | 2026-03-12 | TBD | Replaced `$data` with `$result` (PAT-VAR-001) |
| BRN-D02   | updateDatamulti() Returns Undefined $id_value | Branch | Variable | 2026-03-12 | TBD | Replaced `$id_value` with `$edit_flag` (PAT-VAR-001) |


## Blocked Bugs

| Bug ID    | Module | Blocked At | Reason | Since | GitHub Issue |
| --------- | ------ | ---------- | ------ | ----- | ------------ |
| _(empty)_ |        |            |        |       |              |

## Recently Completed (Last 10)

| Bug ID    | Module  | Severity | Fix Summary                                                                                                                                                         | Completed  | Duration | GitHub Issue |
| --------- | ------- | -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------- | -------- | ------------ |
| BIL-CLT07 | Billing | P1 | Created template 480 with Rate Benefit & Total columns and Approx Discount Benefits text element for split bills | 2026-05-27 | ~90 min | N/A |
| EST-RC01 | Estimation | P1 | Changed `.cat_sub_design` → `.cat_id_sub_design` selector for stock AJAX + added fallback `<option>` from item data when `cat_sub_design_details` hasn't loaded (race condition) | 2026-04-10 | ~30 min | N/A |
| ORD-CLT01 | Order | P1 | Reset `order_cart.orderstatus=0` + clear `id_orderdetails` in `update_order_cancel()` and `update_order_rejection()` when PO cancelled/rejected | 2026-03-26 | ~8 min | [#1567](https://github.com/Logimax-Technologies/etail_development_src/issues/1567) |
| BIL-CLT05 | Billing | P1 | Added `resetOrphanedEstimationItems()` to reset purchase_status/tag_status for items linked to cancelled bills before loading estimation | 2026-03-26 | ~345 min | [#1563](https://github.com/Logimax-Technologies/etail_development_src/issues/1563) |
| BIL-CLT01 | Billing | P1       | Two-tier multi-pass discount distribution — items with MC=VA=0 now skipped, overflow redistributed before item_blc_discount                                         | 2026-02-25 | 1 day    | #1012        |
| BIL-CLT02 | Billing | P2       | Three granular discount limit toggles — enable_emp_disc_limit, enable_mc_va_disc_limit, enable_disc_blw_metal_rate; status override pattern replaces old outer gate | 2026-03-03 | 2 days   | #1071        |
| Bug ID    | Module  | Severity | Fix Summary                                                                                                                 | Completed  | Duration | GitHub Issue |
| --------- | ------- | -------- | --------------------------------------------------------------------------------------------------------------------------- | ---------- | -------- | ------------ |
| PUR-CLT05 | Purchase | P1 | Added missing Touch `<td>` in tag flow row builder + UOM auto-select default in `ret_purchase_order.js` | 2026-03-27 | ~30 min | N/A |
| TAG-CLT02 | Tagging | P2 | Added `duplicate_print` page exception to client-side `is_closed` filter in `ret_tagging.js` L6219 | 2026-03-24 | ~20 min | [#1423](https://github.com/Logimax-Technologies/etail_development_src/issues/1423) |
| RPT-CLT05 | Reports | P1 | Aliased ambiguous `id_customer` in adv_rcvd subquery; changed AND→OR for cross-customer search | 2026-04-04 | ~10 min | N/A |
| BIL-CLT06 | Billing | P0 | Commented out `get_credit_collection_details()` overwrite in `get_credit_pending_details()` — old metal now correctly deducted from outstanding | 2026-04-04 | ~25 min | N/A |
| RPT-CLT04 | Reports | P2 | Fixed column alignment, added Bonus/Rate Benefits/Apx MC-VA/Total columns, fixed JS unary-plus & concat bugs in `set_chit_closing_table()` | 2026-03-28 | ~25 min | [#1642](https://github.com/Logimax-Technologies/etail_development_src/issues/1642) |
| RPT-CLT01 | Reports | P1 | Added `#branch_select` change handler in `ret_reports.js` to reload employees on branch change in Cash Abstract | 2026-03-26 | ~15 min | [#1543](https://github.com/Logimax-Technologies/etail_development_src/issues/1543) |
| BIL-CLT04 | Billing | P1 | Changed `order_adj` query to use `a.advance_date` instead of `b.bill_date` for correct advance date display | 2026-03-23 | ~45 min | [#1357](https://github.com/Logimax-Technologies/etail_development_src/issues/1357) |
| PUR-CLT04 | Purchase | P1 | Added missing `$model` init + model call in controller `getPending_payment_po_bills_for_payment()` — 2-line copy-paste omission | 2026-03-21 | ~12 min | [#1333](https://github.com/Logimax-Technologies/etail_development_src/issues/1333) |
| PUR-CLT01 | Purchase | P1 | Removed 'pur_approval_type' filter in dashboard PO pending query to show all billed/approved POs | 2026-03-20 | ~15 min | [#5879120](https://connect.zoho.in/portal/logimax-clients/task/124744000005879120) |
| PUR-CR04 | Purchase | P2 | Conditional Cheque Print button + dual-print on Payment Copy click (4 files: 2 models + 2 JS) | 2026-03-11 | ~40m | [#1198](https://github.com/Logimax-Technologies/etail_development_src/issues/1198) |
| BRN-D22 | Branch Transfer | P1 | Already fixed by BRN-103 — all `last_query()` and `_error_message()` leaks removed from controller | 2026-03-13 | ~5 min | N/A (dup of BRN-103) |
| BRN-D21 | Branch Transfer | P1 | Fixed copy-paste variable mismatch — `$partly_sale_log` → `$item_log` for non-tag SR insert in controller save() | 2026-03-13 | ~15 min | [#1214](https://github.com/Logimax-Technologies/etail_development_src/issues/1214) |
| BRN-D11 | Branch Transfer | P1 | Fixed copy-paste swap in `get_purchase_items()` SR summary — `'net_wt'` key was using `$gross_wt` instead of `$net_wt` | 2026-03-12 | ~21 min | [#1179](https://github.com/Logimax-Technologies/etail_development_src/issues/1179) |
| BRN-D10 | Branch Transfer | P1 | Added `num_rows() > 0` guard + `escape()` to `get_verifMobNo()` — prevents fatal PHP crash when branch has no OTP mobile configured | 2026-03-12 | ~12 min | [#1178](https://github.com/Logimax-Technologies/etail_development_src/issues/1178) |
| BRN-R401 | Branch Transfer | P1 | Renamed duplicate DOM ID to `id_product_nt` and contextually mapped JS autocomplete handlers | 2026-03-12 | ~15 min | [#1176](https://github.com/Logimax-Technologies/etail_development_src/issues/1176) |
| BRN-D37 | Branch Transfer | P0 | Replaced `transfer_to : 1` with `transfer_to : to_brn` for OM logic | 2026-03-12 | ~12 min | TBD |
| BRN-D17 | Branch Transfer | P0 | Added `num_rows()` null guard to `get_headoffice_branch()` protecting `->row()` | 2026-03-12 | ~25 min | TBD |
| BRN-D16 | Branch Transfer | P0 | Added `num_rows()` null guard to `getNontagItemId()` protecting `->row()` | 2026-03-12 | ~30 min | TBD |
| BRN-D09 | Branch Transfer | P0 | Renamed undefined `$FromDt` to parameter `$from_date` in `get_ajaxBranchTransferlist` | 2026-03-12 | ~31 min | TBD |
| BRN-D03 | Branch Transfer | P0 | Systematically applied CI3 SQL escape methods to 50+ RAW POST concatenation payloads in model | 2026-03-12 | ~45 min | [#1204](https://github.com/Logimax-Technologies/etail_development_src/issues/1204) |
| BRN-104 | Branch Transfer | P1 | Replaced 104 instances of raw `$_POST` with `$this->input->post()` for XSS protection + syntax fixes | 2026-03-12 | ~45 min | [#1174](https://github.com/Logimax-Technologies/etail_development_src/issues/1174) |
| BRN-103 | Branch Transfer | P1 | Removed `echo $this->db->last_query() / _error_message()` from output; replaced with server-side logs | 2026-03-11 | ~30 min | [#1173](https://github.com/Logimax-Technologies/etail_development_src/issues/1173) |
| BRN-102 | Branch Transfer | P0 | Restructured `verify_other_issue_otp()`: trans_commit moved after updateData(); rollback added to all failure paths | 2026-03-11 | ~38 min | [#1166](https://github.com/Logimax-Technologies/etail_development_src/issues/1166) |
| BRN-101 | Branch Transfer | P0 | Removed orphaned `trans_begin()` in `verify_otp()` — function had no DB writes | 2026-03-11 | ~40 min | [#1165](https://github.com/Logimax-Technologies/etail_development_src/issues/1165) |
| BIL-CLT03 | Billing | P1 | Frontend hidden input state fix and backend zero explicit save update | 2026-03-10 | ~24 hrs | TBD |
| RPT-CLT03 | Reports | P0 | Corrected due_amt formula for Issued; added credit_disc_amt to Received amount in `getcreditBill()` | 2026-03-14 | ~12m | [#1244](https://github.com/Logimax-Technologies/etail_development_src/issues/1244) |
| BIL-INT04 | Billing | P1 | Added `make_as_advance=1` filter to all credit-return balance subqueries in billing and reports models. | 2026-03-07 | 57m | [#1112](https://github.com/Logimax-Technologies/etail_development_src/issues/1112) |
| BIL-INT03 | Billing | P1 | Subtracted `round_off_amt` from balance in save/cancel credit-status blocks + report SQL; added to `get_BillAmount()` SELECT | 2026-03-06 | ~1 hr | [#1097](https://github.com/Logimax-Technologies/etail_development_src/issues/1097) |
| BIL-INT02 | Billing | P0 | Corrected return amount data source (ret_bill_return_details→ret_bill_details) + return bill status filter in `getCreditBillDetails()` and `getBillData()` | 2026-03-05 | ~2 hrs | [#1081](https://github.com/Logimax-Technologies/etail_development_src/issues/1081) |
| BIL-INT01 | Billing | P0 | Subtracted `return_item_cost` from `due_amount` in `getBillData()` | 2026-03-04 | ~2 hrs | [#1080](https://github.com/Logimax-Technologies/etail_development_src/issues/1080) |
| BIL-CLT01 | Billing | P1       | Two-tier multi-pass discount distribution — items with MC=VA=0 now skipped, overflow redistributed before item_blc_discount | 2026-02-25 | 1 day    | #1012        |
| RPT-CLT01 | Reports | P1 | IF/fallback for weight_name — uses from_weight/to_weight when weight_description is empty | 2026-03-03 | ~30 min | [#1072](https://github.com/Logimax-Technologies/etail_development_src/issues/1072) |
| PUR-CLT03 | Purchase | P1 | Corrected pure weight field labels and positions in Approval to Invoice Conversion | 2026-03-04 | ~30 min | [#1074](https://github.com/Logimax-Technologies/etail_development_src/issues/1074) |
| Bug ID | Module | Severity | Fix Summary | Completed | Duration | GitHub Issue |
|---|---|---|---|---|---|---|
| BIL-CLT02 | Billing | High | Cash Abstract Missing Split Home Bills | 2026-03-05 | Fixed | @Antigravity |
| PUR-CLT03 | Purchase | P1 | Guard balance_amount behind ctrl_page check + remove synchronous total outstanding set; calculateSelectedTotal() sole authority | 2026-03-03 | ~40 min | [#1068](https://github.com/Logimax-Technologies/etail_development_src/issues/1068) |
| PUR-INT03 | Purchase | P2 | CR/DR indicators + amount_type/weight_type columns in supplier rate cut | 2026-02-27 | ~90 min | [#1031](https://github.com/Logimax-Technologies/etail_development_src/issues/1031) |
| PUR-INT02 | Purchase | P1 | Used `IFNULL(po_bill.bill_amount, d.payment_amount)` for per-PO allocation | 2026-02-26 | ~180 min | #1026 |
| PUR-CLT02 | Purchase | P2 | Fixed event param, async timing, stale data in image upload | 2026-02-25 | ~30 min | #1023 |
| PUR-UI01 | Purchase | P2 | Product name "undefined" + column alignment fix in PO form (IFNULL, file_exists guard, JS fallback) | 2026-02-25 | 25 min | [#1022](https://github.com/Logimax-Technologies/etail_development_src/issues/1022) |
| PUR-CLT02 | Purchase | P0 | Remove other_charges from GST base, add after tax calc | 2026-02-28 | ~60 min | [#1060](https://github.com/Logimax-Technologies/etail_development_src/issues/1060) |
| PUR-INT01 | Purchase | P1 | FOR UPDATE locking + move ref gen inside transaction | 2026-02-25 | < 1 hr | [#1014](https://github.com/Logimax-Technologies/etail_development_src/issues/1014) |
| RPT-CLT02 | Reports | P1 | Removed rejected weight LEFT JOIN from payment/ratecut rows; only return rows show rejected wt | 2026-03-06 | ~30 min | [#1098](https://github.com/Logimax-Technologies/etail_development_src/issues/1098) |
| RPT-CLT01 | Reports | P1 | IF/fallback for weight_name — uses from_weight/to_weight when weight_description is empty | 2026-03-03 | ~30 min | [#1072](https://github.com/Logimax-Technologies/etail_development_src/issues/1072) |

---

## How This File Gets Updated

| Event         | Workflow                                                     | Action                                          |
| ------------- | ------------------------------------------------------------ | ----------------------------------------------- |
| Fix starts    | `/fix-single-bug` Step 0                                     | Add row to In-Progress                          |
| Fix blocked   | `/fix-architecture-bug` or `/fix-business-bug` at human gate | Move to Blocked                                 |
| Fix unblocked | After human approval                                         | Move back to In-Progress, update Current Step   |
| Fix completed | `/learn-and-improve` Step 4c                                 | Move to Recently Completed                      |
| Bug deferred  | Any workflow                                                 | Remove from In-Progress, note in execution plan |
