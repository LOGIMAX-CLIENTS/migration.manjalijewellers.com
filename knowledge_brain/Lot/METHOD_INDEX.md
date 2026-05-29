# LOT MODULE — METHOD INDEX
> Module: Lot | Round 1 | 2026-03-17
> Auto-grep all methods. **Use Ctrl+F / grep for fast lookup.**

---

## 7a. Controller Methods — Alphabetical

| Method | Lines | Tables Read | Tables Written | JS Caller |
|---|---|---|---|---|
| `base64ToFile($imgBase64)` | L267–293 | — | — | Internal only (save/update) |
| `branch_acknowladgement($type,$lot_id,$id_branch)` | L1843–1877 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_taging`, `branch`, `employee` | — | Print link click |
| `customer_acknowladgement($type,$lot_id)` | L2528–2562 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_karigar`, `country`, `state`, `city`, `employee` | — | Print link click |
| `get_ActiveProduct()` | L2290–2300 | `ret_product_master` | — | `get_ActiveProduct()` in JS |
| `get_karigar_list()` | L1907–1916 | `customerorder`, `customerorderdetails`, `joborder`, `ret_karigar`, `ret_product_master`, `ret_category`, `ret_purity` | — | `get_karigar_list()` in JS |
| `get_lotInward_detail()` | L1687–1701 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_product_master`, `ret_design_master`, `ret_section`, `ret_sub_design_master`, `customerorderdetails`, `branch`, `ret_category`, `ret_purity`, `ret_karigar` | — | `get_lotInward_detail()` in JS |
| `get_order_details()` | L1891–1901 | `customerorder`, `customerorderdetails`, `ret_product_master`, `ret_design_master`, `joborder` | — | `getSearchOrderNo()` in JS |
| `getOrderNosBySearch()` | L1879–1888 | `customerorder`, `branch` | — | `getSearchOrderNo()` in JS |
| `getProductBySearch()` | L1921–1933 | `ret_product_master`, `ret_category`, `metal`, `ret_taxgroupitems`, `ret_taxmaster` | — | `getSearchProd()` in JS |
| `index()` | L75–79 | — | — | — |
| `lot_acknowladgement($type,$lot_id)` | L1807–1841 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `branch`, `ret_kargar`, and tag tables | — | Print button click |
| `lot_completed()` | L2461–2525 | — | `ret_lot_inwards` (is_closed=1) | `lot_completed()` in JS |
| `lot_inward($type,$id)` | L297–1683 | Multiple (see routes) | Multiple (see routes) | Various JS calls |
| `lot_inwards_detail()` | L1703–1769 | `ret_taging` | `ret_lot_inwards_detail` (DELETE) | Delete row button in JS |
| `lot_merge($type,$id)` | L1935–2288 | Multiple merge tables | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_lot_merge`, `ret_nontag_item`, `ret_nontag_item_log`, `ret_lot_inwards_stone_detail` | Lot merge form submit |
| `lot_split($type)` | L2302–2459 | Multiple split tables | `ret_lot_inwards` (is_lot_split=1), `ret_lot_split_details` | Lot split form submit |
| `remove_img()` | L173–257 | — | `ret_lot_inwards`, `ret_lot_inwards_detail` (image fields) | `remove_img()` in JS |
| `rrmdir($path)` | L147–169 | — | — | Internal (delete) |
| `upload_img($outputImage,$dst,$img,$quality)` | L83–145 | — | — | Internal (save/update) |
| `vendor_acknowladgement($type,$lot_id)` | L1773–1805 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_karigar`, `branch`, `employee` | — | Print link click |

> **Total controller methods**: 20 (+ internal helpers `base64ToFile`, `upload_img`, `rrmdir`) = **23 total functions**

---

## 7b. Model Methods — Alphabetical

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `ajax_getLotList($from_date,$to_date,$id_metal,$emp_id)` | L182–283 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_product_master`, `ret_category`, `ret_karigar`, `ret_purchase_order`, `ret_grn_entry`, `ret_old_metal_process`, `employee` | — | `lot_inward` default case |
| `checkNonTagItemExist($data)` | L1054–1088 | `ret_nontag_item` | — | `lot_inward/save`, `lot_merge/save` |
| `check_is_tagged($id_lot_inward_detail)` | L1641–1651 | `ret_taging` | — | `lot_inwards_detail()` |
| `deleteData($id_field,$id_value,$table)` | L119–129 | — | `{any}` (generic) | `lot_inward/delete`, update (stone cleanup) |
| `empty_record_inward()` | L351–469 | `ret_settings`, `branch` | — | `lot_inward/add`, `lot_merge/list`, `lot_split/list` |
| `get_ActiveProduct($data)` | L1266–1280 | `ret_product_master` | — | `get_ActiveProduct()` ctrl |
| `get_branch_summary($tag_lot_id,$id_branch)` | L798–824 | `ret_taging`, `ret_product_master`, `ret_design_master`, `branch` | — | `branch_acknowladgement()` |
| `get_branchName($branch)` | L588–604 | `branch` | — | `empty_record_inward()` |
| `get_customer_lot_details($lot_no)` | L1785–1845 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_product_master`, `ret_design_master`, `ret_sub_design_master`, `ret_purity`, `ret_lot_inwards_stone_detail`, `ret_lot_other_items`, `ret_lot_other_charges`, `ret_charges` | — | `customer_acknowladgement()` |
| `get_customer_lotInward_detail($id)` | L1747–1783 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_karigar`, `country`, `state`, `city`, `employee` | — | `customer_acknowladgement()` |
| `get_karigar_list($order_no)` | L944–998 | `customerorder`, `customerorderdetails`, `joborder`, `ret_karigar`, `ret_product_master`, `ret_category`, `ret_purity` | — | `get_karigar_list()` ctrl |
| `get_lot_details($lot_no)` | L652–700 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_product_master`, `ret_design_master`, `ret_purity` | — | `lot_acknowladgement()`, `vendor_acknowladgement()` |
| `get_lot_othercharges_details($id)` | L1668–1677 | `ret_lot_other_charges` | — | `get_lotInward_detail()`, `get_lotInward_data()` |
| `get_lot_othermetals_details($id)` | L1654–1666 | `ret_lot_other_items` | — | `get_lotInward_detail()`, `get_lotInward_data()` |
| `get_lot_stones_details($id)` | L1625–1637 | `ret_lot_inwards_stone_detail`, `ret_stone` | — | `get_lotInward_detail()`, `get_lotInward_data()` |
| `get_lot_tag_details($lot_no)` | L704–758 | `ret_taging`, `ret_product_master`, `ret_design_master`, `ret_purity`, `branch`, `ret_taging_stone`, `ret_stone` | — | `lot_acknowladgement()` |
| `get_lotInward($id)` | L471–491 | `ret_lot_inwards`, `branch` | — | `lot_inward/edit`, `lot_inward/lot_edit` |
| `get_lotInward_data($id)` | L1679–1744 | `ret_lot_inwards`, `ret_lot_inwards_detail`, all catalog tables | — | `ajax_getLotList` (N+1 risk!) |
| `get_lotInward_detail($id)` | L493–558 | `ret_lot_inwards`, `ret_lot_inwards_detail`, all catalog tables + `ret_section`, `ret_sub_design_master` | — | `lot_inward/edit`, `get_lotInward_detail()` ctrl |
| `get_order_details($orderno,$id_karigar,$id_branch)` | L912–940 | `customerorder`, `customerorderdetails`, `ret_product_master`, `ret_design_master`, `joborder` | — | `get_order_details()` ctrl |
| `get_profile_settings($id_profile)` | L285–289 | `profile` | — | `lot_inward` default case |
| `get_ret_settings($settings)` | L578–586 | `ret_settings` | — | Multiple methods |
| `get_stone_details_lot($id_lot_inward_detail)` | L1246–1264 | `ret_lot_inwards_stone_detail`, `ret_uom`, `ret_stone` | — | `getLotNoForMerge()` |
| `get_tag_details($lot_no)` | L764–794 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_product_master`, `ret_design_master`, `ret_purity` | — | Unused (no ctrl reference found) |
| `get_tagdetails_by_lot($tag_lot_id,$id_branch)` | L830–892 | `ret_taging`, `ret_design_master`, `ret_product_master`, `ret_sub_design_master`, `branch`, `ret_lot_inwards_detail` | — | `branch_acknowladgement()` |
| `getlotOtherChargeDetails($lot_item_id)` | L1882–1898 | `ret_lot_other_charges`, `ret_charges` | — | `get_customer_lot_details()` |
| `getlotOtherMetalDetails($lot_item_id)` | L1870–1878 | `ret_lot_other_items` | — | `get_customer_lot_details()` |
| `getlotStoneDetails($lot_item_id)` | L1847–1866 | `ret_lot_inwards_stone_detail`, `ret_stone`, `ret_uom` | — | `get_customer_lot_details()` |
| `getLotDetails($data)` | L1454–1540 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_product_master`, `ret_purchase_order`, `ret_old_metal_process`, `ret_lot_inwards_stone_detail`, `ret_stone`, `ret_taging`, `ret_lot_merge`, `ret_lot_inwards_detail` | — | `lot_split/getLotDetails` |
| `getLotidsforMerge()` | L1542–1582 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_taging`, `ret_lot_merge` | — | `lot_merge/getLotidsforMerge` |
| `getLotidsforSplit()` | L1584–1622 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_taging`, `ret_lot_merge` | — | `lot_split/getLotidsforSplit` |
| `getLotNoForMerge($data)` | L1112–1244 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_product_master`, `ret_purchase_order`, `ret_old_metal_process`, `ret_lot_inwards_stone_detail`, `ret_stone`, `ret_uom`, `ret_taging`, `ret_nontag_receipt`, `ret_lot_merge` | — | `lot_merge/getLotNos` |
| `getLotNoForSplit($data)` | L1282–1452 | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_product_master`, `ret_category`, `ret_purity`, `ret_lot_inwards_stone_detail`, `ret_stone`, `ret_uom`, `ret_lot_split_details`, `ret_taging`, `ret_lot_merge` | — | `lot_split/lotNosForsplit` |
| `getOrderNos($SearchTxt)` | L894–910 | `customerorder`, `branch` | — | `getOrderNosBySearch()` ctrl |
| `getorderdesigns()` | L560–569 | `ret_product_mapping`, `ret_product_master`, `ret_design_master` | — | `lot_inward/lot_edit` |
| `getordersubdesigns()` | L571–576 | `ret_sub_design_master` | — | `lot_inward/lot_edit` |
| `getProductBySearch($SearchTxt,$category,$stock_type)` | L1002–1050 | `ret_product_master`, `ret_category`, `metal`, `ret_taxgroupitems`, `ret_taxmaster` | — | `getProductBySearch()` ctrl |
| `getProductDivision()` | L1104–1110 | `ret_product_division` | — | `lot_inward/add`, `lot_inward/edit` |
| `getTaggedDetails($lot_no)` | L291–317 | `ret_taging`, `ret_product_master`, `ret_design_master`, `ret_purity`, `branch` | — | `ajax_getLotList()` (N+1) |
| `get_tagged_branchwise_details($lot_no)` | L321–347 | `ret_taging`, `ret_product_master`, `ret_design_master`, `ret_purity`, `branch` | — | `ajax_getLotList()` (N+1) |
| `insertData($data,$table)` | L19–53 | `SHOW COLUMNS` (any table) | `{any}` (generic) | Multiple callers |
| `lotInward_detail($id)` | L606–648 | `ret_lot_inwards`, `ret_lot_inwards_detail`, all catalog + `employee` | — | `vendor_acknowladgement()`, `lot_acknowladgement()`, `branch_acknowladgement()` |
| `updateData($data,$id_field,$id_value,$table)` | L55–91 | `SHOW COLUMNS` (any table) | `{any}` (generic) | Multiple callers |
| `updateNTData($data,$arith)` | L1092–1100 | — | `ret_nontag_item` (arithmetic update) | `lot_inward/save`, `lot_merge/save` |

> **Total model methods**: ~44 methods

---

## 7c. JS → Controller AJAX Map

| JS Line | JS Function/Context | AJAX URL | Controller Method |
|---|---|---|---|
| L2219 | `#lot_img_upload` click | `admin_ret_lot/upload_lotimg` | **MISSING** (404) |
| L2291 | `remove_img()` | `admin_ret_lot/remove_img` | `remove_img()` |
| Various | `get_lotInward_list()` | `admin_ret_lot/lot_inward` (POST) | `lot_inward` default case |
| Various | `getLotNoForMerge()` | `admin_ret_lot/lot_merge/getLotNos` | `lot_merge('getLotNos')` |
| Various | `getLotidsforMerge()` | `admin_ret_lot/lot_merge/getLotidsforMerge` | `lot_merge('getLotidsforMerge')` |
| Various | `getLotidsforSplit()` | `admin_ret_lot/lot_split/getLotidsforSplit` | `lot_split('getLotidsforSplit')` |
| Various | `get_karigar_list()` | `admin_ret_lot/get_karigar_list` | `get_karigar_list()` |
| Various | `getSearchOrderNo()` / `getOrderNosBySearch()` | `admin_ret_lot/getOrderNosBySearch` | `getOrderNosBySearch()` |
| Various | `get_order_details()` | `admin_ret_lot/get_order_details` | `get_order_details()` |
| Various | `getSearchProd()` / `getProductBySearch()` | `admin_ret_lot/getProductBySearch` | `getProductBySearch()` |
| Various | `get_ActiveProduct()` | `admin_ret_lot/get_ActiveProduct` | `get_ActiveProduct()` |
| Various | `delete lot item` button | `admin_ret_lot/lot_inwards_detail` | `lot_inwards_detail()` |
| Various | `lot_completed()` | `admin_ret_lot/lot_completed` | `lot_completed()` |

**Cross-module AJAX calls in ret_lot.js:**

| JS Function | Target Controller | Purpose |
|---|---|---|
| `get_karigar()` | `admin_ret_catalog` or similar | Load karigar list |
| `get_category()` | `admin_ret_catalog` | Load categories |
| `getActiveUOM()` | `admin_ret_catalog` | Load UOM |
| `get_ActiveMetals()` | `admin_ret_catalog` | Load metals |
| `get_ActivePurity()` | `admin_ret_catalog` | Load purities |
| `get_Branchwise_Sections()` | `admin_ret_catalog` | Load sections |
| `get_stones()` | `admin_ret_catalog` | Load stone list |
| `get_stone_types()` | `admin_ret_catalog` | Load stone types |
| `getActive_quality_code()` | `admin_ret_catalog` | Load quality codes |
| `getQualityDiamondRates()` | `admin_ret_catalog` | Load diamond rates |
| `get_charges()` | `admin_ret_catalog` | Load charges |
| `get_taxgroup_items()` | `admin_ret_catalog` | Load tax groups |
| `get_employee()` | Admin controller | Load employees |
| `get_karigar_details()` | `admin_ret_catalog` | Load karigar details |
| `get_cat_purity()` | `admin_ret_catalog` | Load purities for category |
| `get_ActiveGRNS()` | GRN controller | Load GRN entries |
| `getSearchCustomers()` | Customer controller | Search customers |

---

## 7d. Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `ret_lot_inwards` | `ajax_getLotList`, `get_lotInward`, `get_lotInward_detail`, `lotInward_detail`, `getLotNoForMerge`, `getLotNoForSplit`, `getLotDetails`, `getLotidsforMerge`, `getLotidsforSplit`, `get_lot_details`, `get_lot_tag_details`, `get_lotInward_data` | `insertData` (save/merge), `updateData` (update/cancel/close), `deleteData` (delete) |
| `ret_lot_inwards_detail` | Most model methods above | `insertData` (save/update/merge), `updateData` (update), `deleteData` (update stone cleanup, lot_inwards_detail) |
| `ret_lot_inwards_stone_detail` | `get_lot_stones_details`, `getlotStoneDetails`, `getLotNoForMerge`, `getLotNoForSplit`, `getLotDetails` | `insertData` (save/update), `deleteData` (update — stone cleanup only) |
| `ret_lot_other_items` | `get_lot_othermetals_details`, `getlotOtherMetalDetails` | `insertData` (save only — no update cleanup!) |
| `ret_lot_other_charges` | `get_lot_othercharges_details`, `getlotOtherChargeDetails` | `insertData` (save only — no update cleanup!) |
| `ret_lot_merge` | `getLotNoForMerge`, `getLotDetails`, `getLotidsforMerge`, `getLotidsforSplit`, `getLotNoForSplit` | `insertData` (lot_merge/save) |
| `ret_lot_split_details` | `getLotNoForSplit` | `insertData` (lot_split/save) |
| `ret_nontag_item` | `checkNonTagItemExist` | `insertData` (new), `updateNTData` (increment) |
| `ret_nontag_item_log` | — | `insertData` (save/merge) |
| `ret_section_nontag_item_log` | — | `insertData` (save) |
| `ret_settings` | `get_ret_settings` | — |
| `ret_taging` | `getTaggedDetails`, `get_tagged_branchwise_details`, `get_lot_tag_details`, `check_is_tagged`, `getLotNoForMerge`, etc. | — |
| `profile` | `get_profile_settings` | — |
