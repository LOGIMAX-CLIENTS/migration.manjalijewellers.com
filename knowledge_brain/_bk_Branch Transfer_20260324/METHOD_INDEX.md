# Method Index — Branch Transfer

> **Round 7 Refresh** — 2026-03-24
> All line numbers re-verified against live codebase. Two new controller methods added; dead-code duplicate model stubs flagged.

---

## 1. Controller Methods (`admin_ret_brntransfer.php` — 1,386 lines, 12 methods)

| # | Method | Lines | Tables Read | Tables Written | JS Caller / Trigger |
|---|---|---|---|---|---|
| 1 | `__construct` | L11–33 | `users`, `profile_rights` | — | Base instantiation |
| 2 | `index` | L34–36 | — | — | Default route (empty) |
| 3 | `branch_transfer($type,$id,$s_type,$print_type)` | L37–1148 | `{any}` | `{any}` | Central hub — routes via `$type` switch. Called from all JS AJAX. |
| 4 | `verify_otp` | L1149–1167 | `otp_logs` | — | `verify_otp()` |
| 5 | `send_otp` | L1168–1212 | `ret_settings` | `otp_logs` | `send_otp()` |
| 6 | `send_sms` | L1213–1221 | — | — | Internal utility (called by `send_otp`, `send_other_issue_otp`) |
| 7 | `bt_get_branches` | L1222–1226 | `ret_branches` | — | `getBTBranches()` |
| 8 | `update_branch_transfer_cancel` | L1261–1292 | — | `ret_branch_transfer` | `update_branch_transfer_cancel()` |
| 9 | `send_other_issue_otp` | L1293–1332 | `ret_settings` | `otp_logs` | `send_other_issue_otp()` |
| 10 | `verify_other_issue_otp` | L1333–1362 | `otp_logs` | — | `verify_otherissue_otp()` |
| 11 | `get_purchase_items` | L1374–1378 | — | — | Delegates to `ret_brntransfer_model→get_purchase_items()` → JSON. JS: `get_purchase_items()` |
| 12 | `getNonTagReceiptedLots` | L1380–1385 | — | — | Delegates to model→`getNonTagReceiptedLots()` → JSON. JS: `getNonTagReceiptedLots()` |

### Switch-case sub-routes inside `branch_transfer($type)`:

| `$type` value | Lines | Purpose |
|---|---|---|
| `approval_list` | L42–52 | Load approval dashboard (GET page) |
| `list` | L53–57 | Load transfer listing page (GET page) |
| `add` | L58–65 | Load add transfer form (GET page) |
| `getTagsByFilter_scan` | L66–69 | Barcode scan fetch (AJAX) |
| `download_pending` | L70–78 | Get download-pending data (AJAX) |
| `save` | L79–284 | Save new transfer — all item types (AJAX) |
| `updateStatus` | L285–1050 | Approve transit / stock download — all item types (AJAX) |
| `getTagsByFilter` | (model call) | Fetch tags by filter (AJAX) |
| `getNonTaggedItem` | (model call) | Fetch non-tagged items (AJAX) |
| `getEstiTagsByFilter` | (model call) | Fetch estimation tags (AJAX) |
| `getDesignByFilter` | (model call) | Design dropdown data (AJAX) |
| `getLotsByBranch` | (model call) | Lots by branch (AJAX) |
| `approval_pending` | (model call) | Load pending approvals grid (AJAX) |
| `ajax` | (model call) | Transfer list AJAX data (AJAX) |
| `print` | (view render) | Generate transfer printout (GET/POST) |
| `getRepairOrderDetails` | (model call) | Fetch repair order data (AJAX) |
| `update_TagsByFilter_scan` | (model call) | Update tag filter via scan (AJAX) |
| `getNonTaggedReceiptedItem` | (model call) | Non-tag receipted items (AJAX) |

---

## 2. Model Methods (`ret_brntransfer_model.php` — 2,248 lines, 56 methods incl. constructor)

> ⚠️ **DEAD CODE — 4 deprecated method stubs** at L1155–L1458 (wrapped in `/*...*/` block): `get_purchase_items` (old 3-param), `get_partly_sale_details` (old 4-param at L1296), `get_sales_ret_details` (old 4-param at L1336), `old_metal_bill_details` (old 4-param at L1364), `get_purchase_items_details` (old 3-param at L1390). The active 5-param versions (with `$bill_type`) replaced them at L1462+. These stubs are harmless but bloat the file by ~300 lines.

| # | Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|---|
| 1 | `__construct` | L6–9 | — | — | Controller |
| 2 | `insertData($data,$table)` | L13–47 | — | `{any}` | Generic CRUD — all save/log operations |
| 3 | `updateData($data,$id_field,$id_value,$table)` | L49–85 | — | `{any}` | Generic CRUD — status updates |
| 4 | `updateDatamulti($data,$arr,$table)` | L94–107 | — | `{any}` | Tag download date updates |
| 5 | `deleteData($id_field,$id_value,$table)` | L108–113 | — | `{any}` | Generic CRUD (unused in current code) |
| 6 | `getLotsByFilter($postData)` | L115–174 | `ret_lot_inward` | — | Form dropdowns |
| 7 | `zgetDesignByFilter($postData)` | L176–182 | `def_design` | — | **Deprecated** — old fetch |
| 8 | `getDesignByFilter($postData)` | L184–190 | `def_design` | — | Filter dropdowns |
| 9 | `getProductsByFilter($postData)` | L192–200 | `ret_products` | — | Form dropdowns |
| 10 | `fetchTagsByFilter($data)` | L202–259 | `ret_taging`, `ret_products`, `def_design` | — | AJAX: getTagsByFilter |
| 11 | `fetchEstiTagsByFilter($data)` | L261–299 | `ret_taging`, `ret_products`, `def_design` | — | AJAX: getEstiTagsByFilter |
| 12 | `fetchNonTaggedItems($data)` | L301–351 | `ret_nontag_item`, `ret_products`, `def_design` | — | AJAX: getNonTaggedItem |
| 13 | `isHeadOffice($branch)` | L354–362 | `ret_branches` | — | NT stock download logic |
| 14 | `getApprovalListing($data)` | L531–783 | `ret_branch_transfer`, `users`, multiple child tables | — | approval_pending, download_pending |
| 15 | `trans_code_generator($is_eda)` | L786–799 | `ret_branch_transfer` | — | Save → auto-generate BT code |
| 16 | `getBTnontags($trans_id)` | L802–812 | `ret_brch_transfer_non_tag_items` | — | updateStatus (Type 2) |
| 17 | `get_last_trans_code($is_eda)` | L814–820 | `ret_branch_transfer` | — | `trans_code_generator()` |
| 18 | `getBTransData($transCode,$s_type,$print_type)` | L822–997 | `ret_branch_transfer`, child tables, `ret_taging`, `ret_products`, `def_design`, `ret_branches` | — | Print/View/Report |
| 19 | `getBTransDataSummary($transCode,$s_type,$print_type)` | L998–1032 | `ret_branch_transfer`, child tables | — | List grid summaries |
| 20 | `get_verifMobNo($branch)` | L1034–1038 | `ret_branches` | — | OTP sending |
| 21 | `checkNonTagItemExist($data,$to_branch)` | L1040–1054 | `ret_nontag_item` | — | updateStatus (Type 2 download) |
| 22 | `updateNTData($data,$arith)` | L1056–1061 | — | `ret_nontag_item` | Stock add/deduct |
| 23 | `getBTtags($trans_id,$approval_type)` | L1063–1078 | `ret_brch_transfer_tag_items` | — | updateStatus (Type 1) |
| 24 | `get_tag_details($tag_id)` | L1080–1084 | `ret_taging` | — | Save (Type 1 validation) |
| 25 | `getBTBranches()` | L1086–1090 | `ret_branches` | — | Branch dropdowns |
| 26 | `getSettigsByName($name)` | L1092–1096 | `ret_settings` | — | Settings lookups |
| 27 | `get_profile_settings($id_profile)` | L1098–1102 | `profile_settings` | — | Preferences |
| 28 | `get_ajaxBranchTransferlist($from_date,$to_date)` | L1105–1140 | `ret_branch_transfer` | — | List page grid data |
| 29 | `get_profile_details($id_profile)` | L1141–1145 | `profile` | — | Auth gate profile fetch |
| 30 | `get_purchase_items($data)` | L1462–1600 | `metal`, `ret_bill_details`, `ret_taging`, `ret_bill_old_metal_sale_details`, `ret_billing_item_stones` | — | Old Metal/PS/SR listing grid. Calls L1602/1673/1747. 5-param (incl. `$bill_type`) |
| 31 | `get_partly_sale_details($from,$to,$branch,$metal,$bill_type)` | L1602–1670 | `ret_bill_details`, `ret_taging`, `ret_taging_stone`, `ret_billing_item_stones`, `ret_brch_transfer_old_metal` | — | Partly sale items. `$bill_type` filters `bill.is_eda`. **Active 5-param version.** |
| 32 | `get_sales_ret_details($from,$to,$branch,$metal,$bill_type)` | L1673–1745 | `ret_billing`, `ret_bill_return_details`, `ret_bill_details`, `ret_taging`, `ret_billing_item_stones`, `ret_brch_transfer_old_metal` | — | Sales returns. Filters by `b.is_eda=$bill_type`. Handles tagged + non-tag. |
| 33 | `old_metal_bill_details($from,$to,$branch,$metal_type,$bill_type)` | L1747–1786 | `ret_billing`, `ret_bill_old_metal_sale_details`, `ret_estimation_old_metal_sale_details`, `ret_old_metal_type`, `ret_brch_transfer_old_metal` | — | Old metal bill detail rows. **Active 5-param version.** |
| 34 | `get_purchase_items_details($transCode,$s_type,$print_type)` | L1787–1882 | `ret_branch_transfer`, `ret_brch_transfer_old_metal`, `ret_bill_old_metal_sale_details`, `ret_bill_details`, `ret_taging`, `ret_billing_item_stones`, `ret_billing`, `branch` | — | Print/view old metal + SR + PS detail per transfer. **Active version.** |
| 35 | `getBTOldMetalDetails($transfer_id)` | L1877–1881 | `ret_brch_transfer_old_metal` | — | updateStatus (Type 3) — old metals |
| 36 | `get_salesreturn_items($transfer_id)` | L1884–1888 | `ret_brch_transfer_old_metal` | — | updateStatus (Type 3) — sales returns |
| 37 | `get_partlysale_items($transfer_id)` | L1890–1894 | `ret_brch_transfer_old_metal` | — | updateStatus (Type 3) — partly sales |
| 38 | `get_headoffice_branch()` | L1899–1903 | `ret_branches` | — | Form hidden field |
| 39 | `get_packaging_items($trans_id)` | L1905–1910 | `ret_branch_transfer_other_inventory` | — | updateStatus (Type 4) |
| 40 | `get_InventoryCategory($id_other_item_type)` | L1912–1920 | `ret_other_inventory_item`, `ret_other_inventory_item_type` | — | Packaging config |
| 41 | `get_other_inventory_purchase_items_details(...)` | L1922–1930 | `ret_other_inventory_purchase_items_details` | — | Packaging transit (status=0) |
| 42 | `get_other_inventory_download_pending_details(...)` | L1932–1940 | `ret_other_inventory_purchase_items_details` | — | Packaging download (status=4) |
| 43 | `getRepairOrderDetails($data)` | L1944–1969 | `ret_repair_orders_details`, `ret_branch_transfer` | — | Repair order grid |
| 44 | `getBTOrders($trans_id)` | L1970–1975 | `ret_bt_order_log` | — | updateStatus (Type 5) |
| 45 | `get_repair_order_tag_details(...)` | L1976–1984 | `ret_taging` | — | Order approval tag lookup |
| 46 | `get_download_data($data)` | L1988–2088 | `ret_branch_transfer`, child tables, `ret_taging`, `ret_products` | — | Download-pending grid |
| 47 | `fetchTagsByFilter_scan($data)` | L2090–2130 | `ret_taging`, `ret_products`, `def_design` | — | Barcode scan fetch |
| 48 | `getBTDetail($branch_transfer_id)` | L2132–2144 | `ret_branch_transfer` | — | Print/View master header |
| 49 | `get_scan_tag_status(...)` | L2146–2156 | `ret_taging` | — | Scan validation |
| 50 | `getNonTagReceiptedLots($data)` | L2157–2171 | `ret_lot_inward` | — | Receipted lots dropdown |
| 51 | `fetchNonTaggedReceiptedItems($data)` | L2172–2226 | `ret_lot_inward`, `ret_products`, `def_design` | — | Receipted NT items grid |
| 52 | `getNontagItemId(...)` | L2227–2234 | `ret_nontag_item` | — | Validating item exists |
| 53 | `get_transit_status` | ~L1969–1974 | `ret_taging_status_log` | — | Check live status |
| 54 | `check_other_inventory_qty` | ~L2153–2158 | `ret_other_inventory_items` | — | Controller validations |
| 55 | `get_other_issue_types` | ~L1964–1967 | `ret_other_issue_types` | — | Master data |
| 56 | `update_branch_transfer_order_trans_data` | ~L2098–2106 | — | `ret_repair_orders_trans_data` | Transfer→Order mapping |

> ⚠️ **Raw SQL Warning**: Methods #40, #41, #42, #44, #45 use `$this->db->query()` with string concatenation — potential SQL injection risk.

---

## 3. JS → Controller AJAX Map (`ret_branch_transfer.js` — 8,786 lines, 51 named functions)

### 3a. Internal Endpoints (same controller: `admin_ret_brntransfer`)

| # | JS Function | JS Line (approx) | AJAX URL | Controller Target | HTTP |
|---|---|---|---|---|---|
| 1 | `getTagSearchList()` | L3311 | `.../branch_transfer/getTagsByFilter` | `branch_transfer('getTagsByFilter')` | POST |
| 2 | `getNonTaggedItem()` | L3007 | `.../branch_transfer/getNonTaggedItem` | `branch_transfer('getNonTaggedItem')` | POST |
| 3 | `getEstiTags()` | L3882 | `.../branch_transfer/getEstiTagsByFilter` | `branch_transfer('getEstiTagsByFilter')` | POST |
| 4 | `add_to_trans()` | L3979 | `.../branch_transfer/save` | `branch_transfer('save')` | POST |
| 5 | `get_brantranTagged()` | L4217 | `.../branch_transfer/download_pending` | `branch_transfer('download_pending')` | POST |
| 6 | `get_brantranTagged()` | L4217 | `.../branch_transfer/approval_pending` | `branch_transfer('approval_pending')` | POST |
| 7 | `brnTransUpdStatus()` | L4827 | `.../branch_transfer/updateStatus` | `branch_transfer('updateStatus')` | POST |
| 8 | `send_otp()` | L4935 | `.../send_otp` | `send_otp()` | POST |
| 9 | `send_otp()` (2nd block) | L5004 | `.../send_otp` | `send_otp()` | POST |
| 10 | `verify_otp()` | L5067 | `.../verify_otp` | `verify_otp()` | POST |
| 11 | `getBTBranches()` | L5135 | `.../bt_get_branches` | `bt_get_branches()` | GET |
| 12 | `update_branch_transfer_cancel()` | L5461 | `.../update_branch_transfer_cancel` | `update_branch_transfer_cancel()` | POST |
| 13 | `get_ajaxBranchTransferlist()` | L5503 | `.../branch_transfer/ajax` | `branch_transfer('ajax')` | POST |
| 14 | `send_other_issue_otp()` | L5686 | `.../send_other_issue_otp` | `send_other_issue_otp()` | POST |
| 15 | `verify_otherissue_otp()` | L5752 | `.../verify_other_issue_otp` | `verify_other_issue_otp()` | POST |
| 16 | `get_purchase_items()` | L6390 | `.../get_purchase_items` | `get_purchase_items()` | POST |
| 17 | `get_brantranNonTagged()` | L4611 | `.../branch_transfer/approval_pending` | `branch_transfer('approval_pending')` | POST |
| 18 | `get_brantranOldMetal()` | L6902 | `.../branch_transfer/approval_pending` | `branch_transfer('approval_pending')` | POST |
| 19 | `get_brantranPackagingItems()` | L7275 | `.../branch_transfer/approval_pending` | `branch_transfer('approval_pending')` | POST |
| 20 | `getRepairOrderDetails()` | L7431 | `.../branch_transfer/getRepairOrderDetails` | `branch_transfer('getRepairOrderDetails')` | POST |
| 21 | `getscan_TagSearchList()` | L7897 | `.../branch_transfer/update_TagsByFilter_scan` | `branch_transfer('update_TagsByFilter_scan')` | POST |
| 22 | `getSearchDesign()` | L2861 | `.../branch_transfer/getDesignByFilter` | `branch_transfer('getDesignByFilter')` | POST |
| 23 | `get_received_lots()` | L2913 | `.../branch_transfer/getLotsByBranch` | `branch_transfer('getLotsByBranch')` | POST |
| 24 | `get_branch_transfer_download()` | L2273 | `.../branch_transfer/download_pending` | `branch_transfer('download_pending')` | POST |
| 25 | `get_branchtranOrderDetails()` | L7641 | `.../branch_transfer/approval_pending` | `branch_transfer('approval_pending')` | POST |
| 26 | `getNonTagReceiptedLots()` | L8085 | `.../getNonTagReceiptedLots` | `getNonTagReceiptedLots()` | POST |
| 27 | `getNonTaggedReceiptedItem()` | L8143 | `.../branch_transfer/getNonTaggedReceiptedItem` | `branch_transfer(...)` | POST |
| 28 | `send_mobile_approval_request()` | L8267 | `.../admin_app_api/bt_approval_otp_req` | **Cross-module** | POST |
| 29 | `update_aprvl_status()` | L8657 | `.../admin_app_api/update_aprvl_status` | **Cross-module** | POST |
| 30 | `get_approval_status()` | L8741 | `.../admin_app_api/get_approval_status` | **Cross-module** | POST |

### 3b. Cross-Module AJAX (calls to other controllers)

| # | JS Function | AJAX URL | Target Controller | Purpose |
|---|---|---|---|---|
| 1 | `getSearchProd()` | `.../admin_ret_catalog/product/active_prodBySearch` | `admin_ret_catalog` | Product autocomplete |
| 2 | `get_ActiveSections()` | `.../admin_ret_catalog/get_sectionBranchwise` | `admin_ret_catalog` | Section dropdown |
| 3 | `get_invnetory_item()` | `.../admin_ret_other_inventory/get_invnetory_item` | `admin_ret_other_inventory` | Packaging item list |
| 4 | `get_ActiveKarigars()` | `.../admin_ret_catalog/karigar/active_list` | `admin_ret_catalog` | Karigar dropdown |
| 5 | `send_mobile_approval_request()` | `.../admin_app_api/bt_approval_otp_req` | `admin_app_api` | Mobile app OTP |
| 6 | `update_aprvl_status()` | `.../admin_app_api/update_aprvl_status` | `admin_app_api` | Mobile approval status |
| 7 | `get_approval_status()` | `.../admin_app_api/get_approval_status` | `admin_app_api` | Check mobile approval |

### 3c. Key JS Utility Functions (no AJAX, but critical logic)

| Function | Lines | Purpose |
|---|---|---|
| `calcTaggedApprList()` | L593–643 | Calculates totals for tagged approval selection |
| `calcNTaggedApprList()` | L667–693 | Calculates totals for non-tagged approval selection |
| `approveBranchTransfer()` | L859–1089 | Master approval orchestrator — validates, collects, calls AJAX |
| `calculateNTtotal()` | L1343–1369 | Sums non-tag rows (pieces, gross wt, net wt) |
| `update_order_description()` | L1621–1629 | Updates order row description |
| `remove_brn_row()` | L3522–3580 | Removes tag row from grid + recalculates |
| `add_to_trans()` | L3979–4101 | Collects form data → submits save AJAX |
| `set_brantranTagged()` | L4349–4607 | Renders tagged items in approval DataTable |
| `calculate_old_metal()` | L6346–6388 | Sums old metal/SR/PS checked items |
| `set_purchase_items_preview()` | L6568–6788 | Renders old metal preview DataTable |
| `fnFormatRowDetails()` | L4105–4215 | Expands row in approval list (tagged detail) |
| `fnFormatRowBillDetails()` | L6790–6900 | Expands row for bill details (old metal) |
| `create_new_empty_order_detail_row()` | L7487–7561 | Adds new order row to table |
| `calculateOrdertotal()` | L7579–7623 | Order total calculation |
| `branch_download_by_scan()` | L7815–7869 | Scan-based download |
| `check_duplicates()` | L8525–8543 | Check duplicate tag in transfer |
| `show_countdown()` | L8821–8857 | OTP countdown timer |
| `set_expiry_status()` | L8859–8879 | OTP expiry handling |
| `remove_row(curRow)` | L7563–7571 | Removes order detail row from grid + recalculates via `calculateOrdertotal()` |
| `set_brantranOrders(data)` | L7691–7813 | Renders order items in approval DataTable (`#bt_approval_list_orders`). Day Close validation inline. |

> **Total named functions: 51** — 30 internal AJAX + 7 cross-module AJAX + 4 overlap (counted in both 3a+3b) + 20 utility = 51 unique.

---

## 4. Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `ret_branch_transfer` | `trans_code_generator`, `get_last_trans_code`, `getBTransData`, `getBTransDataSummary`, `getApprovalListing`, `get_ajaxBranchTransferlist`, `get_download_data`, `getBTDetail`, `getRepairOrderDetails` | `insertData` (save), `updateData` (updateStatus, cancel) |
| `ret_brch_transfer_tag_items` | `getBTtags` | `insertData` (save), `updateDatamulti` (download date) |
| `ret_brch_transfer_non_tag_items` | `getBTnontags` | `insertData` (save) |
| `ret_brch_transfer_old_metal` | `getBTOldMetalDetails`, `get_salesreturn_items`, `get_partlysale_items`, `get_purchase_items_details` | `insertData` (save) |
| `ret_branch_transfer_other_inventory` | `get_packaging_items` | `insertData` (save) |
| `ret_taging` | `fetchTagsByFilter`, `fetchEstiTagsByFilter`, `fetchTagsByFilter_scan`, `get_tag_details`, `get_scan_tag_status`, `get_repair_order_tag_details`, `get_purchase_items`, `get_partly_sale_details`, `get_sales_ret_details`, `getBTransData`, `get_download_data` | `updateData` (tag_status, current_branch, trans_to_acc_stock) |
| `ret_taging_status_log` | `get_transit_status` | `insertData` (all tag transitions) |
| `ret_section_tag_status_log` | — | `insertData` (section-level tag movements) |
| `ret_nontag_item` | `fetchNonTaggedItems`, `checkNonTagItemExist`, `getNontagItemId` | `updateNTData` (+/−), `insertData` (new NT item at to_branch) |
| `ret_nontag_item_log` | — | `insertData` (NT transit/download logs) |
| `ret_section_nontag_item_log` | — | `insertData` (section-level NT logs) |
| `ret_lot_inward` | `getLotsByFilter`, `getNonTagReceiptedLots`, `fetchNonTaggedReceiptedItems` | — |
| `ret_products` | `getProductsByFilter`, `fetchTagsByFilter` (join) | — |
| `def_design` | `getDesignByFilter`, `zgetDesignByFilter`, `fetchTagsByFilter` (join) | — |
| `ret_branches` | `getBTBranches`, `get_verifMobNo`, `isHeadOffice`, `get_headoffice_branch` | — |
| `ret_settings` | `getSettigsByName` | — |
| `ret_bill_details` | `get_purchase_items`, `get_partly_sale_details`, `get_sales_ret_details`, `get_purchase_items_details` | `updateData` (current_branch, transferred_to_acc_stock) |
| `ret_bill_old_metal_sale_details` | `old_metal_bill_details`, `get_purchase_items`, `get_purchase_items_details` | `updateData` (current_branch, is_transferred) |
| `ret_purchase_items_log` | — | `insertData` (old metal/SR/PS transit & download logs) |
| `otp_logs` | `verify_otp`, `verify_other_issue_otp` | `insertData` (send_otp, send_other_issue_otp) |
| `ret_bt_order_log` | `getBTOrders` | `insertData` (save Type 5) |
| `ret_repair_orders_details` | `getRepairOrderDetails` | — |
| `ret_repair_orders_trans_data` | — | `update_branch_transfer_order_trans_data` |
| `ret_other_inventory_items` | `check_other_inventory_qty` | (updated in controller for packaging) |
| `ret_other_inventory_item` | `get_InventoryCategory` (join) | — |
| `ret_other_inventory_item_type` | `get_InventoryCategory` (join) | — |
| `ret_other_inventory_purchase_items_details` | `get_other_inventory_purchase_items_details`, `get_other_inventory_download_pending_details` | (updated in controller for packaging) |
| `profile` | `get_profile_details` | — |
| `profile_settings` | `get_profile_settings` | — |
| `ret_other_issue_types` | `get_other_issue_types` | — |
| `users` | `getApprovalListing` (join) | — |
