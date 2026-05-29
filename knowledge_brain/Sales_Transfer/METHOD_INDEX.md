# Sales Transfer Module — Method Index

> **Module**: Sales Transfer
> **Last Updated**: 2026-03-20 — Round 1

---

## 7a. Controller Methods (alphabetical)

| Method | Lines | Tables Read | Tables Written | JS Caller |
|---|---|---|---|---|
| `create_sales_ret_transfer()` | L287-380 | `ret_category`, `metal`, `ret_billing`, `ret_bill_details`, `ret_taging`, `ret_product_master` | `ret_billing`, `ret_bill_return_details`, `ret_taging`, `ret_taging_status_log` | `create_sales_ret_transfer()` L3238 |
| `create_sales_transfer()` | L84-214 | `branch`, `metal`, `ret_settings` | `ret_billing`, `ret_bill_details`, `ret_taging`, `ret_taging_status_log` | `create_sales_transfer()` L2444 |
| `index()` | L27-29 | — | — | — |
| `sales_transfer($type, $id)` | L33-81 | varies by $type | — | Multiple (route dispatcher) |
| `update_ret_TagScan()` | L522-589 | `ret_taging`, `ret_bill_details`, `ret_billing` | `ret_taging`, `ret_taging_status_log`, `ret_billing` | `get_retscan_TagSearchList()` L4012 |
| `update_sales_ret_transfer()` | L383-447 | `ret_bill_details`, `ret_taging`, `ret_billing` | `ret_taging`, `ret_taging_status_log`, `ret_billing` | `update_sales_ret_transfer_request()` L3324 |
| `update_sales_transfer_request()` | L217-283 | `ret_billing`, `ret_bill_details`, `ret_taging` | `ret_taging`, `ret_taging_status_log`, `ret_billing` | `update_sales_transfer_request()` L2564 |
| `update_TagScan()` | L450-516 | `ret_taging`, `ret_bill_details`, `ret_billing` | `ret_taging`, `ret_taging_status_log`, `ret_billing` | `getscan_TagSearchList()` L3588 |

---

## 7b. Model Methods (alphabetical)

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `deleteData($id_field, $id_value, $table)` | L45-55 | — | `{any}` | Not called in this module |
| `fetchReturnTagsByFilter_scan($data)` | L647-705 | `ret_billing`, `ret_bill_return_details`, `ret_bill_details`, `ret_taging`, `ret_product_master`, `ret_category`, `ret_design_master` | — | `sales_transfer('getReturnTagsByFilter')` |
| `fetchTagsByFilter_scan($data)` | L555-605 | `ret_billing`, `ret_bill_details`, `ret_taging`, `ret_product_master` | — | `sales_transfer('getTagsByFilter')` |
| `getBillId($to_brn, $sales_bill_no, $fin_year_code)` | L387-401 | `ret_billing` | — | `create_sales_ret_transfer()`, `update_sales_ret_transfer()` |
| `get_billed_details($bill_id)` | L225-247 | `ret_bill_details`, `ret_taging` | — | `get_sales_trans_approval_tag()`, `get_sales_return_trans_approval_tag()` |
| `get_branch_details($id_branch)` | L251-259 | `branch` | — | `create_sales_transfer()` |
| `get_category_details($id_ret_category)` | L71-83 | `ret_category`, `metal` | — | `create_sales_ret_transfer()` |
| `get_category_tag_details($cat_id, $id_branch)` | L145-165 | `ret_taging`, `ret_product_master`, `ret_category` | — | Not called in this controller |
| `get_FinancialYear()` | L59-67 | `ret_financial_year` | — | `sales_transfer('add')`, `sales_transfer('ret_add')` |
| `get_metal_details($id_metal)` | L87-95 | `metal` | — | `create_sales_transfer()` |
| `get_sales_return_req_tag_details($cat_id, $id_branch, $bill_id, $from_branch)` | L353-383 | `ret_billing`, `ret_bill_details`, `ret_taging`, `ret_product_master`, `ret_category` | — | `create_sales_ret_transfer()` |
| `get_sales_return_tag_details($cat_id, $id_branch, $bill_id)` | L499-533 | `ret_billing`, `ret_bill_return_details`, `ret_bill_details`, `ret_taging`, `ret_product_master`, `ret_category`, `ret_design_master` | — | `update_sales_ret_transfer()` |
| `get_sales_return_trans_approval_tag($data)` | L407-483 | `ret_billing`, `ret_bill_return_details`, `ret_bill_details`, `ret_taging`, `ret_product_master`, `ret_category` | — | `sales_transfer('sales_return_trans_approval_tag')` |
| `get_sales_return_trans_req_tag($data)` | L285-347 | `ret_billing`, `ret_bill_details`, `ret_taging`, `ret_product_master`, `ret_category`, `ret_lot_inwards`, `ret_design_master` | — | `sales_transfer('sales_return_trans_tag')` |
| `get_sales_trans_approval_tag($data)` | L169-221 | `ret_billing`, `ret_bill_details`, `ret_taging` | — | `sales_transfer('sales_trans_approval_tag')` |
| `get_sales_transfer_tag_details($data)` | L99-141 | `ret_taging`, `ret_lot_inwards`, `ret_product_master`, `ret_category`, `metal`, `ret_design_master` | — | `sales_transfer('sales_trans_tag')` |
| `getSalesTrans_Tag($bill_id)` | L263-279 | `ret_billing`, `ret_bill_details`, `ret_taging` | — | `update_sales_transfer_request()` |
| `getSettigsByName($name)` | L539-549 | `ret_settings` | — | `sales_transfer('add')`, `create_sales_transfer()`, `create_sales_ret_transfer()` |
| `get_TagBilledPcs($bill_id)` | L611-641 | `ret_bill_details`, `ret_taging` | — | `update_TagScan()`, `update_ret_TagScan()` |
| `insertData($data, $table)` | L19-29 | — | `{any}` | Multiple controller methods |
| `updateData($data, $id_field, $id_value, $table)` | L31-43 | — | `{any}` | Multiple controller methods |

---

## 7c. JS → Controller AJAX Map

### Internal Endpoints (same controller)

| JS Function | JS Line | AJAX URL | Controller Method |
|---|---|---|---|
| `get_sales_transfer_tag_list()` | L1885 | `admin_ret_sales_transfer/sales_transfer/sales_trans_tag` | `sales_transfer('sales_trans_tag')` |
| `get_sales_transfer_approval_list()` | L1515 | `admin_ret_sales_transfer/sales_transfer/sales_trans_approval_tag` | `sales_transfer('sales_trans_approval_tag')` |
| `get_sales_return_transfer_tag_list()` | L1673 | `admin_ret_sales_transfer/sales_transfer/sales_return_trans_tag` | `sales_transfer('sales_return_trans_tag')` |
| `get_sales_return_approval_list()` | L1311 | `admin_ret_sales_transfer/sales_transfer/sales_return_trans_approval_tag` | `sales_transfer('sales_return_trans_approval_tag')` |
| `create_sales_transfer()` | L2444 | `admin_ret_sales_transfer/create_sales_transfer` | `create_sales_transfer()` |
| `update_sales_transfer_request()` | L2564 | `admin_ret_sales_transfer/update_sales_transfer_request` | `update_sales_transfer_request()` |
| `create_sales_ret_transfer()` | L3238 | `admin_ret_sales_transfer/create_sales_ret_transfer` | `create_sales_ret_transfer()` |
| `update_sales_ret_transfer_request()` | L3324 | `admin_ret_sales_transfer/update_sales_ret_transfer` | `update_sales_ret_transfer()` |
| `getscan_TagSearchList()` | L3588 | `admin_ret_sales_transfer/sales_transfer/getTagsByFilter` | `sales_transfer('getTagsByFilter')` |
| (nested in getscan_TagSearchList) | L3700 | `admin_ret_sales_transfer/update_TagScan` | `update_TagScan()` |
| `get_retscan_TagSearchList()` | L4012 | `admin_ret_sales_transfer/sales_transfer/getReturnTagsByFilter` | `sales_transfer('getReturnTagsByFilter')` |
| (nested in get_retscan_TagSearchList) | L4118 | `admin_ret_sales_transfer/update_ret_TagScan` | `update_ret_TagScan()` |

### Cross-Module Endpoints (calls to other controllers)

| JS Function | JS Line | AJAX URL | External Controller |
|---|---|---|---|
| `getSearchProd()` | L93 | `admin_ret_catalog/product/active_prodBySearch` | `admin_ret_catalog` |
| `getSearchDesign()` | L155 | `admin_ret_brntransfer/branch_transfer/getDesignByFilter` | `admin_ret_brntransfer` |
| `get_received_lots()` | L217 | `admin_ret_brntransfer/branch_transfer/getLotsByBranch` | `admin_ret_brntransfer` |
| `getBTBranches()` | L287 | `admin_ret_brntransfer/bt_get_branches` | `admin_ret_brntransfer` |
| `get_metal_rates_by_branch()` | L2518 | `admin_ret_tagging/get_metal_rates_by_branch` | `admin_ret_tagging` |
| `get_ActiveMetals()` | L2902 | `admin_ret_catalog/ret_product/active_metal` | `admin_ret_catalog` |
| `get_ActiveCategory()` | L2964 | `admin_ret_catalog/category/active_category` | `admin_ret_catalog` |
| (On success redirect) | L2476 | `admin_ret_billing/billing_invoice/{id}` | `admin_ret_billing` |

---

## 7d. Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `ret_billing` | `get_sales_trans_approval_tag`, `get_sales_return_trans_req_tag`, `get_sales_return_req_tag_details`, `get_sales_return_trans_approval_tag`, `getSalesTrans_Tag`, `getBillId`, `fetchTagsByFilter_scan`, `fetchReturnTagsByFilter_scan`, `get_sales_return_tag_details` | `create_sales_transfer` (INSERT), `update_sales_transfer_request` (UPDATE download_date), `create_sales_ret_transfer` (INSERT), `update_TagScan` (UPDATE download_date), `update_ret_TagScan` (UPDATE download_date) |
| `ret_bill_details` | `get_billed_details`, `get_sales_trans_approval_tag`, `get_sales_return_req_tag_details`, `get_sales_return_trans_approval_tag`, `getSalesTrans_Tag`, `fetchTagsByFilter_scan`, `get_TagBilledPcs`, `fetchReturnTagsByFilter_scan`, `get_sales_return_tag_details` | `create_sales_transfer` (INSERT) |
| `ret_taging` | `get_sales_transfer_tag_details`, `get_category_tag_details`, `get_billed_details`, `get_sales_trans_approval_tag`, `get_sales_return_trans_req_tag`, `get_sales_return_req_tag_details`, `getSalesTrans_Tag`, `fetchTagsByFilter_scan`, `get_TagBilledPcs`, `fetchReturnTagsByFilter_scan`, `get_sales_return_tag_details`, `get_sales_return_trans_approval_tag` | `create_sales_transfer` (UPDATE tag_status=4), `update_sales_transfer_request` (UPDATE tag_status=0, current_branch), `create_sales_ret_transfer` (UPDATE tag_status=4), `update_sales_ret_transfer` (UPDATE tag_status=0, current_branch), `update_TagScan` (UPDATE tag_status=0, current_branch), `update_ret_TagScan` (UPDATE tag_status=0, current_branch) |
| `ret_taging_status_log` | — | `create_sales_transfer` (INSERT), `update_sales_transfer_request` (INSERT), `create_sales_ret_transfer` (INSERT), `update_sales_ret_transfer` (INSERT), `update_TagScan` (INSERT), `update_ret_TagScan` (INSERT) |
| `ret_bill_return_details` | `get_sales_return_trans_approval_tag`, `fetchReturnTagsByFilter_scan`, `get_sales_return_tag_details` | `create_sales_ret_transfer` (INSERT) |
| `ret_category` | `get_category_details`, `get_sales_transfer_tag_details`, `get_category_tag_details`, `get_sales_return_trans_req_tag`, `get_sales_return_req_tag_details`, `get_sales_return_trans_approval_tag`, `fetchReturnTagsByFilter_scan`, `get_sales_return_tag_details` | — |
| `ret_product_master` | `get_sales_transfer_tag_details`, `get_category_tag_details`, `get_sales_return_trans_req_tag`, `get_sales_return_req_tag_details`, `fetchTagsByFilter_scan`, `get_sales_return_trans_approval_tag`, `fetchReturnTagsByFilter_scan`, `get_sales_return_tag_details` | — |
| `metal` | `get_category_details`, `get_metal_details`, `get_sales_transfer_tag_details` | — |
| `branch` | `get_branch_details` | — |
| `ret_financial_year` | `get_FinancialYear` | — |
| `ret_settings` | `getSettigsByName` | — |
| `ret_lot_inwards` | `get_sales_transfer_tag_details`, `get_sales_return_trans_req_tag` | — |
| `ret_design_master` | `get_sales_transfer_tag_details`, `get_sales_return_trans_req_tag`, `get_sales_return_tag_details`, `fetchReturnTagsByFilter_scan` | — |
