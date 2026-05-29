# Method Index — Old Metal Process Module

## Controller: `Admin_ret_metal_process` (1732 lines, 26 public methods)
> ⚠️ R16 Refresh: Controller shrank from 1792→1732 lines. Method count corrected from ~30 to 26 actual.

| # | Method | Line | Type | Description |
|---|---|---|---|---|
| 1 | `__construct` | 7 | Setup | Loads model, admin_settings_model, log_model; session guard |
| 2 | `index` | 31 | View | Empty stub |
| 3 | `get_old_metal_type` | 35 | AJAX GET | Returns ret_old_metal_type list |
| 4 | `metal_process` | 43 | Switch | Process Master CRUD: list/add/edit/save/update/ajax |
| 5 | `get_ActiveMetalProcess` | 170 | AJAX GET | All process master rows |
| 6 | `get_melting_issue_details` | 177 | AJAX GET | Melting-complete items for testing issue |
| 7 | `metal_pocket` | 185 | Switch | Pocket CRUD: list/add/save/ajax/metal_list |
| 8 | `get_metal_pocket_list` | ~280 | AJAX | Pocket list (DataTable) |
| 9 | `save_pocket` | ~300 | POST | Pocket creation save |
| 10 | `metal_process_issue` | ~450 | Switch | Process issue/receipt: list/add/save/ajax |
| 11 | `process_acknowladgement` | ~1578 | PDF | DomPDF acknowledgement by process type |
| 12 | `metal_process_receipt` | ~680 | Switch | Receipt listing: list/add |
| 13 | `get_pocket_details` | ~720 | AJAX POST | Returns pocket items for melting issue; filtered by trans_type and karigar |
| 14 | `get_polish_pocket_details` | ~760 | AJAX GET | Returns pockets for polishing issue |
| 15 | `get_KarigarMeltingIssueDetilas` | ~800 | AJAX POST | Pending melting issues by karigar |
| 16 | `get_testing_issue_details` | ~840 | AJAX POST | melting_status=1 items for testing issue |
| 17 | `get_testing_receipt_details` | ~880 | AJAX POST | Testing items pending receipt by karigar |
| 18 | `get_RefiningIssueDetails` | ~920 | AJAX POST | Testing-complete items for refining issue |
| 19 | `get_RefiningReceiptDetails` | ~960 | AJAX POST | Refining issues for receipt by karigar |
| 20 | `get_Active_Refining` | ~1000 | AJAX GET | Active refining process numbers |
| 21 | `get_PolishingReceiptDetails` | ~1040 | AJAX POST | Polishing pending receipt items by karigar |
| 22 | `get_ActiveCategoryPurity` | ~1080 | AJAX GET | Category to purity mapping |
| 23 | `process_report` | ~1120 | Switch | Report views: list/add/detailed |
| 24 | `get_active_design_products` | ~1160 | AJAX GET | Design → product map |
| 25 | `get_active_sub_design_products` | ~1200 | AJAX GET | Sub-design → product map |
| 26 | `get_opening_metal_stock_list` | ~1240 | AJAX POST | Opening balance old metal stock |
| 27 | `get_chg_tax_type` | ~1773 | AJAX POST | Determine IGST vs CGST+SGST by karigar state ⚠️ BUG OMP-002 |
| 28 | `get_repair_pending_order` | ~1760 | AJAX POST | Repair orders for pocket creation |

---

## Model: `Ret_metal_process_model` (1867 lines, 84 functions)
> ⚠️ R16 Refresh: Model shrank from 1981→1867 lines. Method count revised from ~65 to 84 actual (many small helpers undercounted previously).

### Generic CRUD (lines 13–70)
| Function | Tables | Notes |
|---|---|---|
| `insertData($data, $table)` | any | Returns insert_id; uses CI Active Record |
| `insertBatchData($data, $table)` | any | Returns TRUE/FALSE |
| `updateData($data, $id_field, $id_value, $table)` | any | WHERE by single col |
| `deleteData($id_field, $id_value, $table)` | any | Generic delete |

### Pocket / Stock Lookup (lines 70–800)
| Function | Key Tables | SQL Injection Risk |
|---|---|---|
| `get_melting_issue_process_details()` | melting, melting_recd, category | No user params |
| `get_process_testing_details($id)` | testing, melting_recd | `$id` raw int concat — safe if (int) cast |
| `get_process_melting_details($id)` | melting_recd, melting | `$id` raw concat |
| `get_Active_Refining_details()` | refining, testing, melting_recd | No user params |
| `get_process_refiining_details($id)` | refining, testing | `$id` raw concat |
| `ajax_getMetalProcess()` | ret_old_metal_process_master | Safe |
| `getMetalProcess($id)` | ret_old_metal_process_master | `$id` raw concat |
| `code_number_generator()` | ret_old_metal_pocket | Safe |
| `get_last_code_no()` | ret_old_metal_pocket | Safe |
| `get_old_metal_type()` | ret_old_metal_type | Safe |
| `get_metal_stock_list($data)` | billing, bill_old_metal, taging | `id_metal`, `id_branch`, `from_branch` raw concat |
| `get_partly_sale_details($from_date,$to_date,$id_branch,$id_metal,$from_branch)` | partlysold, taging, billing | `$from_branch`, `$id_metal`, `$id_branch` raw concat |
| `get_sales_ret_details($from_date,$to_date,$id_branch,$id_metal,$from_branch)` | billing, taging | `$id_metal`, `$id_branch`, `$from_branch` raw concat |
| `old_metal_bill_details($from_date,$to_date,$id_branch,$metal_type,$from_branch)` | billing, old_metal_sale | `$id_branch`, `$metal_type`, `$from_branch` raw concat |
| `get_old_metal_details($from_date,$to_date,$id_branch,$id_metal)` | purchase_items_log, old_metal_sale | `$id_branch`, `$id_metal` raw concat |
| `get_pocket_list($data)` | ret_old_metal_pocket | Date only; Safe |
| `get_melting_pocket_details($id_metal_pocket)` | pocket, pocket_details, melting_details | `$id_metal_pocket` raw concat |
| `get_melting_SalesPocket_details($id_metal_pocket)` | same as above | raw concat |
| `get_melting_tag_details($id_metal_pocket)` | pocket_details, taging | raw concat |
| `get_melting_non_tag_details($id_metal_pocket)` | pocket_details | raw concat |
| `get_polish_pocket()` | pocket, polishing | Safe |
| `get_polishing_pocket_details($id)` | pocket_details, polishing_details | `$id` raw concat |

### Process Functions (lines 800–1400)
| Function | Note |
|---|---|
| `generate_process_number($process_for, $id)` | Race condition: OMP-005 |
| `ajax_get_metal_process()` | Joined: process, karigar, process_master |
| `get_metal_process($id)` | Full process header with geo joins |
| `get_KarigarMeltingIssueDetilas($data)` | Filters by karigar; `$data['id_karigar']` raw concat |
| `get_melting_details()` | All melting records |
| `get_testing_receipt_details($data)` | `$data['id_karigar']` raw concat |
| `get_RefiningIssueDetails()` | Based on melting_status=3 |
| `get_RefiningReceiptDetails($data)` | `$data['id_karigar']` raw concat |
| `get_PolishingReceiptDetails($data)` | `$data['id_karigar']` raw concat |

### Acknowledgement Functions (lines 1400–1700)
| Function | Note |
|---|---|
| `get_melting_issue_details($id)` | For PDF: issue items |
| `get_melting_receipt_details($id)` | For PDF: receipt items |
| `get_testing_issue_details($id)` | For PDF: testing issue |
| `get_TestingReceiptAcknowladgement($id)` | For PDF: testing receipt |
| `get_RefiningIssueAcknowladgement($id)` | For PDF: refining issue |
| `get_refiningReceiptAcknowladgement($id)` | For PDF: refining receipt |
| `get_PolishingIssueAcknowladgement($id)` | For PDF: polishing issue |
| `get_PolishingReceiptAcknowladgement($id)` | For PDF: polishing receipt → uses lot_inwards_detail |

### Stock Update Helpers (lines 1700–1981)
| Function | Table Modified | Note |
|---|---|---|
| `updateNTData($data, $arith)` | ret_nontag_item | Arithmetic +/- |
| `updatePocketItem($id, $data, $arith)` | ret_old_metal_pocket | Arithmetic update |
| `checkNonTagItemExist($data)` | ret_nontag_item | Returns {status, id_nontag_item} |
| `updateStockItemData($id, $data, $arith)` | ret_purchase_item_stock_summary | Arithmetic update |
| `updateStoneItemData($id, $data, $arith)` | ret_purchase_item_stock_summary | ⚠️ OMP-011: id_branch uses id_product |
| `updatePurItemData($id, $data, $arith)` | ret_purchase_item_stock_summary | ⚠️ OMP-015: net_wt uses gross_wt |
| `get_karigar_state()` | ret_karigar | ⚠️ OMP-013: uses raw `$_POST` |
| `getCompanyDetails()` | smith_company_op_balance | Returns company state |
| `get_metal_process_reports($data)` | Multiple process tables | Polishing section inconsistent: OMP-012 |

---

## JS File: `ret_metal_process.js` (4602 lines)
> ⚠️ R16 Refresh: JS shrank from 5088→4602 lines (486 lines removed). 40 AJAX url: endpoints confirmed by R16 scan.

### Global Variables Initialized on Page Load
```javascript
var metal_process = [];       // process master list
var pocket_details = [];      // pocket data cache
var PolishpocketDetails = []; // polish pocket data
var category_details = [];    // category list
var prod_details = [];        // product master cache
var design_details = [];      // design cache
var sub_design_details = [];  // sub-design cache
var section_details = [];     // section cache
var melting_process_details = []; // melting process list
```

### Key JS Functions
| Function | Lines | Description |
|---|---|---|
| `get_pocket_details()` | ~1 | Loads pockets, populates `#select_pocket` |
| `get_MetalPocketDetails(id)` | ~50 | Gets pocket items on dropdown select |
| `get_PolishPocketingDetails(id)` | 1843 | Populates polish issue table |
| `calculate_pocketing_details()` | 2019 | Sums old-metal pocket totals |
| `calculate_tag_pocketing_details()` | 2072 | Sums tagged pocket totals |
| `calculate_non_tag_pocketing_details()` | 2108 | Sums non-tagged totals |
| `calculate_polishing_pocketing_details()` | 1940 | Calculates polish issue balance purity |
| `get_melting_process_details()` | 2589 | Loads pending melting for receipt |
| `get_testing_issue_details()` | 2717 | Loads melted items for testing |
| `get_testing_receipt_details()` | 2767 | Loads testing for receipt |
| `get_RefiningIssueDetails()` | 2901 | Loads tested items for refining |
| `get_refining_receipt_details()` | 2939 | Loads refining for receipt |
| `get_polishing_receipt_details()` | ~3400 | Loads polishing for receipt |
| `validateMeltingReceiptRow()` | 2665 | Validates melting receipt rows |
| `validateTestingReceiptRow()` | 2833 | Validates testing receipt rows |
| `validateRefiningReceiptRow()` | 2846 | Validates refining receipt rows |
| `valiDatePolishingIssue()` | 2330 | Validates polishing issue rows |
| `validatePolishingReceiptRow()` | ~3500 | Validates polishing receipt rows |
| `create_new_category_row(curRow)` | 3096 | Opens category modal |
| `calculate_receipt_payment()` | 2701 | Sums cash + net banking |
| `get_chg_tax_type(karigar_id)` | ~200 | AJAX: determine GST type |
| `get_ActiveMetals()` | 2340 | Loads metal dropdown |
| `get_Activesections()` | 2370 | Loads sections |
| `saveProcess()` | ~2302 | Serializes form and POSTs to save |

### Form ID Reference
| Element ID | Used For |
|---|---|
| `#select_process` | Process type dropdown |
| `#karigar` | Karigar selector |
| `#select_pocket` | Pocket selector (melting) |
| `#select_polish_pocket` | Pocket selector (polishing) |
| `#select_metal_process` | Against-melting process selector |
| `#select_refining_process` | Refining process selector |
| `#pocket_details` | Old metal pocket table |
| `#tagged_pocket_details` | Tagged items table |
| `#non_tagged_pocket_details` | Non-tagged items table |
| `#polish_pocket_details` | Polish issue table |
| `#melting_receipt` | Melting receipt table |
| `#testing_process_details` | Testing issue table |
| `#testing_receipt` | Testing receipt table |
| `#refining_issue_details` | Refining issue table |
| `#refining_receipt` | Refining receipt table |
| `#polishing_receipt` | Polishing receipt table |
| `#category_modal` | Modal for melting receipt category |
| `#refining_category_modal` | Modal for refining receipt category |
| `#polishing_category_modal` | Modal for polishing receipt category |
| `#issue_submit` | Save button |
| `#process_filter` | Search/filter button |
| `#chg_tax_type` | Hidden field for GST type |
