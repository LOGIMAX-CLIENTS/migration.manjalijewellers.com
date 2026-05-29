# METHOD INDEX — Stock Issue
> Verified Round 10 | 2026-03-19 | All 13 ctrl + 22 model + 15 AJAX entries confirmed accurate

---

## 7a. Controller Methods (Alphabetical)

| Method | Lines | Tables Read | Tables Written | JS Caller / Notes |
|---|---|---|---|---|
| `get_nontag_scan_details()` | L1219-1229 | `ret_nontag_item`, `ret_branch_transfer` | — | JS: `get_NonTagScanDetails()` |
| `get_receipt_tag_scan_details()` | L1169-1177 | `ret_taging`, `ret_stock_issue_detail`, `ret_taging_stone`, `ret_tag_other_metals` | — | JS: scan of return tag |
| `get_StockIssuedItems()` | L1199-1209 | `ret_stock_issue`, `ret_stock_issue_detail`, `ret_taging`, `ret_nontag_item` | — | JS: `get_StockIssueItems()` |
| `get_stock_issue_type()` | L1183-1193 | `ret_stock_issue_types` | — | JS: `get_stock_issue_type()` |
| `get_tag_scan_details()` | L1153-1163 | `ret_taging`, `ret_lot_inwards*`, `ret_taging_stone`, `ret_tag_other_metals`, `ret_section` | — | JS: `get_tag_scan_details()` |
| `index()` | L70-76 | — | — | Empty — no route body |
| `isValueset($field)` | L116-124 | — | — | Internal utility — returns field or '-' |
| `shortenurl($url)` | L92-112 | — | — | TinyURL API via cURL — unused in production |
| `stock_issue($type,$id,$received_time)` | L130-1149 | Multiple (see below) | Multiple (see below) | Primary route handler — switch dispatch |
| `stock_issue_sendotp()` | L1252-1329 | — | `otp` | JS: `stock_at_send_otp()` |
| `stock_issue_verify_otp()` | L1331-1407 | — | `otp` | JS: `stock_order_otp()` |
| `stock_trans_send_sms($mobile,$msg,$dlt)` | L1234-1250 | — | — | Called by `stock_issue_sendotp()` |

**`stock_issue()` sub-cases (internal dispatch)**:

| Case | Lines | Tables Read | Tables Written |
|---|---|---|---|
| `list` (GET) | L140-146 | — | — (loads view) |
| `add` (GET) | L150-162 | `profile` | — (loads view) |
| `save` / Tagged Issue | L165-423 | `ret_financial_year`, `ret_stock_issue_types`, `ret_taging` | `ret_stock_issue`, `ret_stock_issue_detail`, `ret_taging`, `ret_taging_status_log`, `ret_section_tag_status_log` |
| `save` / Tagged Receipt | L425-554 | `ret_stock_issue`, `ret_stock_issue_types`, `ret_taging` | `ret_taging`, `ret_stock_issue_detail`, `ret_taging_status_log`, `ret_section_tag_status_log` |
| `save` / NonTag Issue | L557-848 | `ret_financial_year`, `ret_stock_issue_types` | `ret_stock_issue`, `ret_stock_issue_detail`, `ret_nontag_item_log`, `ret_section_nontag_item_log`, `ret_nontag_item` |
| `save` / NonTag Receipt | L850-1052 | `ret_stock_issue_types` | `ret_nontag_item_log`, `ret_section_nontag_item_log`, `ret_nontag_item`, `ret_stock_issue_detail` |
| `issue_print` | L1074-1098 | `ret_stock_issue`, `ret_stock_issue_detail`, `ret_taging`, `ret_*` | — (PDF output) |
| `issue_print_detail` | L1099-1123 | `ret_stock_issue`, `ret_stock_issue_detail`, `ret_taging`, `ret_*` | — (PDF output) |
| `default` (AJAX list) | L1127-1144 | `ret_stock_issue`, `ret_stock_issue_detail`, `ret_taging`, `branch`, `employee`, `customerorder` | — |

---

## 7b. Model Methods (Alphabetical)

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `ajax_getStockIssueList($data)` | L158-242 | `ret_stock_issue`, `ret_stock_issue_detail`, `ret_taging`, `ret_product_master`, `ret_category`, `ret_stock_issue_types`, `branch`, `employee`, `customerorder` | — | `stock_issue(default)` |
| `deleteData($id_field,$id_value,$table)` | L51-61 | {any} | {any} (DELETE) | Not used in this module |
| `generateIssueNo()` | L102-152 | `ret_stock_issue`, `ret_financial_year` | — | `stock_issue(save)` |
| `get_FinancialYear()` | L80-88 | `ret_financial_year` | — | `generateIssueNo()`, `stock_issue(save)` |
| `get_IssueItems($id)` | L274-320 | `ret_stock_issue`, `ret_stock_issue_detail`, `branch`, `employee`, `customer`, `ret_karigar`, `address`, `city`, `state`, `country` | — | `stock_issue(save/print)` |
| `get_issue_item_details($id,$issue_type,$repair_type,$received_time,$stock_type)` | L324-489 | `ret_stock_issue`, `ret_stock_issue_detail`, `ret_taging`/`ret_nontag_item`, `ret_product_master`, `ret_category`, `ret_taxgroupitems`, `ret_taxmaster`, `ret_design_master`, `ret_sub_design_master` | — | `stock_issue(issue_print)` |
| `get_issue_item_tag($id,$issue_type,$repair_type,$received_time,$stock_type)` | L490-655 | same as above (per-tag, no GROUP BY) | — | `stock_issue(issue_print_detail)` |
| `get_nontag_scan_details($data)` | L1301-1341 | `ret_nontag_item`, `ret_product_master`, `ret_design_master`, `ret_sub_design_master`, `ret_section`, `ret_branch_transfer` | — | `get_nontag_scan_details()` ctrl |
| `get_other_metal_details($cat_id,$id_stock_issue,$received_time)` | L1237-1263 | `ret_stock_issue_detail`, `ret_stock_issue`, `ret_taging`, `ret_tag_other_metals`, `ret_product_master`, `ret_category` | — | `get_issue_item_details()`, `get_issue_item_tag()` |
| `get_profile_settings($id_profile)` | L70-78 | `profile` | — | `stock_issue(add)` |
| `get_receipt_tag_scan_details($data)` | L860-1009 | `ret_taging`, `ret_lot_inwards*`, `ret_product_master`, `ret_design_master`, `ret_sub_design_master`, `ret_purity`, `ret_category`, `metal`, `ret_stock_issue_detail`, `ret_taging_stone`, `ret_tag_other_metals` | — | `get_receipt_tag_scan_details()` ctrl |
| `get_ret_settings($settings)` | L91-99 | `ret_settings` | — | Referenced, commented-out in add case |
| `get_stock_issue_det($id_stock_issue)` | L246-268 | `ret_stock_issue_detail`, `employee`, `ret_taging` | — | `ajax_getStockIssueList()` (N+1!) |
| `get_StoneDetails($cat_id,$id_stock_issue,$received_time)` | L1267-1299 | `ret_taging_stone`, `ret_stone`, `ret_uom`, `ret_taging`, `ret_product_master`, `ret_stock_issue_detail` | — | `get_issue_item_details()`, `get_issue_item_tag()` |
| `get_StockIssuedItems($data)` | L1041-1095 | `ret_stock_issue`, `ret_stock_issue_detail`, `ret_taging`/`ret_nontag_item` | — | `get_StockIssuedItems()` ctrl |
| `get_stock_issue_StoneDetails($tag_id)` | L844-858 | `ret_taging_stone`, `ret_stone`, `ret_uom` | — | `get_tag_scan_details()` |
| `get_stock_issue_type()` | L1015-1023 | `ret_stock_issue_types` | — | `get_stock_issue_type()` ctrl |
| `get_tag_scan_details($data)` | L659-838 | `ret_taging`, `ret_lot_inwards*`, `ret_product_master`, `ret_design_master`, `ret_sub_design_master`, `ret_purity`, `ret_category`, `metal`, `ret_section`, `ret_taging_images`, `ret_taging_stone`, `ret_tag_other_metals`, `ret_metal_purity_rate`, `ret_stock_issue_detail` | — | `get_tag_scan_details()` ctrl |
| `getTagDetails($tag_id)` | L1225-1233 | `ret_taging` | — | `stock_issue(save)` |
| `insertData($data,$table)` | L23-33 | — | {any} | Multiple controller methods |
| `stock_issue_nontags($id_stock_issue)` | L1352-1365 | `ret_stock_issue_detail`, `ret_stock_issue`, `ret_nontag_item`, `ret_product_master`, `ret_design_master`, `ret_sub_design_master`, `ret_section` | — | `get_StockIssuedItems()` |
| `stock_issue_tags($id_stock_issue)` | L1117-1221 | `ret_stock_issue_detail`, `ret_taging`, `ret_lot_inwards*`, `ret_product_master`, `ret_design_master`, `ret_sub_design_master`, `ret_purity`, `ret_category`, `metal`, `ret_taging_stone`, `ret_tag_other_metals` | — | `get_StockIssuedItems()` |
| `stock_issue_type_detail($id)` | L1027-1035 | `ret_stock_issue_types` | — | `stock_issue(save)` |
| `updateData($data,$id_field,$id_value,$table)` | L37-49 | — | {any} | Multiple controller methods |
| `updateNTData($data,$arith)` | L1344-1349 | — | `ret_nontag_item` (arithmetic UPDATE) | `stock_issue(save nonTag)` |

---

## 7c. JS → Controller AJAX Map

### Internal Endpoints (same controller: `admin_ret_stock_issue`)

| JS Line | JS Function | AJAX URL | Controller Method |
|---|---|---|---|
| L213 | `get_stock_issue_type()` | `admin_ret_stock_issue/get_stock_issue_type` | `get_stock_issue_type()` |
| L1301 | `#stock_issue_submit click` | `admin_ret_stock_issue/stock_issue/save` | `stock_issue('save')` |
| L1511 | `stock_at_send_otp()` | `admin_ret_stock_issue/stock_issue_sendotp` | `stock_issue_sendotp()` |
| ~L1699 | `stock_order_otp()` | `admin_ret_stock_issue/stock_issue_verify_otp` | `stock_issue_verify_otp()` |
| JS (set_stock_issue_list) | list load | `admin_ret_stock_issue/stock_issue` (POST default) | `stock_issue(default)` |
| JS | tag scan | `admin_ret_stock_issue/get_tag_scan_details` | `get_tag_scan_details()` |
| JS | receipt scan | `admin_ret_stock_issue/get_receipt_tag_scan_details` | `get_receipt_tag_scan_details()` |
| JS | `get_StockIssueItems()` | `admin_ret_stock_issue/get_StockIssuedItems` | `get_StockIssuedItems()` |
| JS | nontag scan | `admin_ret_stock_issue/get_nontag_scan_details` | `get_nontag_scan_details()` |

### Cross-Module AJAX Endpoints

| JS Line | JS Function | AJAX URL | Target Controller |
|---|---|---|---|
| L557 | `get_all_employee()` | `admin_ret_estimation/get_employee` | `Admin_ret_estimation` |
| L321 | `get_all_karigar()` | `admin_ret_catalog/karigar/active_list` | `Admin_ret_catalog` |
| JS | `get_ActiveMetals()` | (shared utility) | External |
| JS | `get_metal_rates_by_branch()` | (shared utility) | External |
| JS | `get_ActiveKarigars()` | (dedicated karigar endpoint) | External |
| JS | `get_ActiveSections()` | (dedicated section endpoint) | External |

---

## 7d. Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `ret_stock_issue` | `ajax_getStockIssueList`, `get_IssueItems`, `get_issue_item_details/tag`, `get_StockIssuedItems`, `generateIssueNo` | `insertData` (via save) |
| `ret_stock_issue_detail` | `ajax_getStockIssueList`, `get_stock_issue_det`, `get_IssueItems`, `get_issue_item_details/tag`, `get_tag_scan_details`, `stock_issue_tags`, `stock_issue_nontags` | `insertData` (save), `updateData` (receipt) |
| `ret_taging` | `get_tag_scan_details`, `get_receipt_tag_scan_details`, `stock_issue_tags`, `get_issue_item_details/tag` | `updateData` (tag_status=7 or 0) |
| `ret_taging_status_log` | — | `insertData` (save issue/receipt if is_remove_from_stock=1) |
| `ret_section_tag_status_log` | — | `insertData` (save issue/receipt if section exists) |
| `ret_nontag_item` | `get_nontag_scan_details`, `get_StockIssuedItems`, `stock_issue_nontags` | `updateNTData` (-/+) |
| `ret_nontag_item_log` | — | `insertData` (nontag issue/receipt if is_remove_from_stock=1) |
| `ret_section_nontag_item_log` | — | `insertData` (nontag if section exists) |
| `ret_stock_issue_types` | `get_stock_issue_type`, `stock_issue_type_detail` | — |
| `ret_financial_year` | `get_FinancialYear`, `generateIssueNo` | — |
| `profile` | `get_profile_settings` | — |
| `ret_settings` | `get_ret_settings` | — |
| `otp` | — | `insertData` (sendotp), `updateData` (verify) |
| `ret_taging_stone` | `get_stock_issue_StoneDetails`, `get_StoneDetails`, `get_tag_scan_details`, `get_receipt_tag_scan_details` | — |
| `ret_tag_other_metals` | `get_other_metal_details`, `get_tag_scan_details`, `get_receipt_tag_scan_details`, `stock_issue_tags` | — |
| `ret_branch_transfer` | `get_nontag_scan_details` | — |
