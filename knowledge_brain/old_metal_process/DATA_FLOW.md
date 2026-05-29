# Data Flow — Old Metal Process Module

## 1. AJAX Endpoint Registry

| JS Function | HTTP | URL | Controller Method | Purpose |
|---|---|---|---|---|
| `get_metal_stock_list()` | POST | `.../admin_ret_metal_process/metal_pocket/metal_list` | `metal_pocket('metal_list')` | Load old metal items for pocketing |
| `pocket_save()` | POST | `.../admin_ret_metal_process/metal_pocket/save` | `metal_pocket('save')` | Save pocket |
| `get_pocket_details()` | POST | `.../admin_ret_metal_process/get_pocket_details` | `get_pocket_details()` | Pocket items for melting |
| `get_polish_pocket_details()` | GET | `.../admin_ret_metal_process/get_polish_pocket_details` | `get_polish_pocket_details()` | Pockets for polishing |
| `get_ActiveMetalProcess()` | GET | `.../admin_ret_metal_process/get_ActiveMetalProcess` | `get_ActiveMetalProcess()` | Process type dropdown |
| `get_melting_issue_details()` | GET | `.../admin_ret_metal_process/get_melting_issue_details` | `get_melting_issue_details()` | Pending melting issues for testing |
| `get_against_melting_Details()` | — | — | Client-side filter from cached `melting_process_details` | Filter by process number |
| `get_Active_Refining_Process()` | GET | `.../admin_ret_metal_process/get_Active_Refining` | `get_Active_Refining()` | Active refining for dropdown |
| `get_pocket_details()` | POST | `.../admin_ret_metal_process/get_pocket_details` | `get_pocket_details()` | Pockets (with trans_type filter) |
| `get_ActiveKarigars()` | GET | `.../admin_ret_catalog/karigar/active_list` | Catalog controller | Karigar dropdown |
| `get_ActiveCategory()` | GET | `.../admin_ret_catalog/category/active_category` | Catalog controller | Category list |
| `get_ActiveCategoryPurity()` | GET | `.../admin_ret_metal_process/get_ActiveCategoryPurity` | `get_ActiveCategoryPurity()` | Purity by category |
| `get_active_design_products()` | GET | `.../admin_ret_metal_process/get_active_design_products` | `get_active_design_products()` | Design→product map |
| `get_active_sub_design_products()` | GET | `.../admin_ret_metal_process/get_active_sub_design_products` | `get_active_sub_design_products()` | Sub-design map |
| `get_openingStockList()` | POST | `.../admin_ret_metal_process/get_opening_metal_stock_list` | `get_opening_metal_stock_list()` | Opening stock for against-opening pockets |
| `get_chg_tax_type()` | POST | `.../admin_ret_metal_process/get_chg_tax_type` | `get_chg_tax_type()` | IGST vs CGST/SGST |
| `get_repair_pending_order()` | POST | `.../admin_ret_metal_process/get_repair_pending_order` | `get_repair_pending_order()` | Repair orders for pocketing |
| `saveProcess()` | POST | `.../admin_ret_metal_process/metal_process/save` | `metal_process('save')` | Save any process |

---

## 2. POST Data Structure — Save Process

### Common Fields (all processes)
```
addData[id_metal_process]      → 1/2/3/4 (Melting/Testing/Refining/Polishing)  
addData[process_for]           → 1=Issue, 2=Receipt  
addData[id_karigar]            → karigar ID  
addData[piece]                 → total pieces  
addData[gross_wt]              → total gross weight  
addData[net_wt]                → total net weight  
addData[remark]                → optional memo  
addData[chg_tax_type]          → 0=IGST, 1=CGST+SGST  
```

### Melting Issue — additional arrays
```
pocket[id_metal_pocket][]      → pocket IDs
pocket[id_metal_type][]        → metal type IDs
pocket[id_category][]          → category IDs
pocket[piece][]                → pieces per pocket
pocket[gross_wt][]             → gross wt per pocket
pocket[net_wt][]               → net wt per pocket
pocket[dia_wt][]               → diamond wt
pocket[avg_purity][]           → purity
pocket[avg_rate_per_gram][]    → rate
pocket[amount][]               → value
pocket[trans_type][]           → 1/2/3
id_pocket_details[]            → for tagged/non-tagged items
```

### Melting Receipt — additional arrays
```
melting_receipt[is_melting_select][]    → 1/0 (checkbox)
melting_receipt[id_melting][]           → id_melting FK
melting_receipt[received_wt][]          → received weight
melting_receipt[received_less_wt][]     → weight loss
melting_receipt[receipt_charges][]      → charges
category_details (JSON)                 → array of {id_ret_category, id_section, id_product, id_design, id_sub_design, recd_gwt, purity, recd_pcs, is_non_tag}
```

### Testing Receipt — additional arrays
```
testing_receipt[is_melting_select][]    → 1/0
testing_receipt[id_melting_recd][]      → melting_recd FK
testing_receipt[purity][]               → tested purity
testing_receipt[id_section][]           
testing_receipt[id_product][]           
testing_receipt[id_design][]            
testing_receipt[id_sub_design][]        
testing_receipt[recd_pcs][]             
testing_receipt[recd_gwt][]             
```

### Refining Issue — additional arrays  
```
refining_issue[is_melting_select][]     → 1/0
refining_issue[id_melting_recd][]       → melting_recd FK
refining_issue[weight][]                → issue weight
```

### Refining Receipt — additional arrays
```
refining_receipt[is_melting_select][]   → 1/0
refining_receipt[id_metal_refining][]   → refining FK
refining_receipt[receipt_charges][]     
refining_receipt[chg_tax_perc][]        
refining_receipt[chg_tax_value][]       
refining_receipt[receipt_ref_no][]      
category_details (JSON)                 → array of {id_ret_category, id_section, id_product, id_design, id_sub_design, recd_gross_wt, id_purity}
```

### Polishing Issue — additional arrays
```
pocket[id_metal_pocket][]              → pocket ID
pocket[id_metal_type][]                → metal type
pocket[issue_pcs][]                    → pieces issued
pocket[issue_gwt][]                    → gross wt issued
pocket[issue_nwt][]                    → net wt issued
pocket[issue_purity][]                 → purity
pocket[diawt][]                        → diamond wt
```

### Polishing Receipt — additional arrays
```
polishing_receipt[is_polishing_select][]  → 1/0
polishing_receipt[id_polishing_details][] → polishing_detail FK
polishing_receipt[id_polishing][]         → polishing header FK
polishing_receipt[recd_pcs][]             → received pieces
polishing_receipt[recd_gwt][]             → received gross wt
polishing_receipt[recd_nwt][]             → received net wt
category_details (JSON)                   → array of {id_ret_category, id_section, id_product, id_design, id_sub_design, recd_gross_wt, recd_nwt, recd_pcs, recd_diawt, id_purity, is_non_tag}
```

### Payment (all receipt transactions)
```
receipt_payment[cash_amount]           → cash paid
receipt_payment[net_banking_amount]    → net banking amount (⚠️ BUG: uses cash_amount in save)
receipt_payment[net_banking_ref_no]    → reference number
```

---

## 3. Model Function Map

### Pocket Functions
| Function | Tables Read | Tables Written |
|---|---|---|
| `get_metal_stock_list($data)` | ret_bill_old_metal_sale_details, ret_billing, ret_old_metal_pocket, ret_old_metal_type | — |
| `get_pocket_list($data)` | ret_old_metal_pocket | — |
| `get_pocket_details($data)` | ret_old_metal_pocket, ret_old_metal_pocket_details, ret_taging, ret_product_master, ret_old_metal_melting_details | — |
| `get_melting_pocket_details($id)` | ret_old_metal_pocket_details, ret_bill_old_metal_sale_details | — |
| `get_melting_SalesPocket_details($id)` | ret_old_metal_pocket_details, etc. | — |
| `get_melting_tag_details($id)` | ret_old_metal_pocket_details, ret_taging | — |
| `get_melting_non_tag_details($id)` | ret_old_metal_pocket_details | — |
| `get_polish_pocket()` | ret_old_metal_pocket, ret_old_metal_pocket_details, ret_old_metal_polishing | — |
| `get_polishing_pocket_details($id)` | ret_old_metal_pocket_details, ret_old_metal_polishing, ret_old_metal_polishing_details | — |

### Process Functions
| Function | Tables Read | Tables Written |
|---|---|---|
| `generate_process_number($process_for, $id)` | ret_old_metal_process | — |
| `ajax_get_metal_process()` | ret_old_metal_process, ret_old_metal_process_master, ret_karigar | — |
| `get_metal_process($id)` | ret_old_metal_process, ret_karigar, country, state, city | — |
| `get_KarigarMeltingIssueDetilas($data)` | ret_old_metal_melting, ret_old_metal_process, ret_old_metal_melting_details | — |
| `get_melting_details()` | ret_old_metal_melting, ret_old_metal_melting_recd_details, ret_old_metal_testing | — |
| `get_testing_receipt_details($data)` | ret_old_metal_testing, ret_old_metal_melting_recd_details, ret_category | — |
| `get_RefiningIssueDetails()` | ret_old_metal_melting_recd_details, ret_old_metal_refining | — |
| `get_RefiningReceiptDetails($data)` | ret_old_metal_refining, ret_old_metal_melting_recd_details | — |
| `get_PolishingReceiptDetails($data)` | ret_old_metal_polishing_details, ret_old_metal_polishing | — |

### Acknowledgement Functions
| Function | Tables |
|---|---|
| `get_melting_issue_details($id)` | ret_old_metal_melting_details, ret_old_metal_melting, ret_old_metal_pocket |
| `get_melting_receipt_details($id)` | ret_old_metal_melting, ret_old_metal_melting_recd_details |
| `get_testing_issue_details($id)` | ret_old_metal_testing, ret_old_metal_melting_recd_details |
| `get_TestingReceiptAcknowladgement($id)` | ret_old_metal_testing |
| `get_RefiningIssueAcknowladgement($id)` | ret_old_metal_refining, ret_old_metal_testing |
| `get_refiningReceiptAcknowladgement($id)` | ret_old_metal_refining, ret_old_metal_refining_details |
| `get_PolishingIssueAcknowladgement($id)` | ret_old_metal_polishing_details, ret_old_metal_polishing |
| `get_PolishingReceiptAcknowladgement($id)` | ret_lot_inwards_detail, ret_lot_inwards, ret_old_metal_process |

### Generic CRUD
| Function | Description |
|---|---|
| `insertData($data, $table)` | Generic insert; returns insert_id on success |
| `updateData($data, $col, $val, $table)` | Generic update by single column |
| `updateNTData($data, $arith)` | Arithmetic update on ret_nontag_item (+/-) |
| `updatePocketItem($id, $data, $arith)` | Arithmetic update on ret_old_metal_pocket |
| `updateStockItemData($id, $data, $arith)` | Arithmetic update on ret_purchase_item_stock_summary |

---

## 4. Non-Tag Stock Upsert Pattern (Repeated in controller)

Whenever a receipt saves items that are non-tagged, this pattern runs:
```php
$existData = ['id_section', 'id_product', 'design', 'id_sub_design', 'id_branch'];
$isExist = checkNonTagItemExist($existData);

if ($isExist['status'] == TRUE) {
    updateNTData(['id_nontag_item' => ..., 'gross_wt' => ..., ...], '+');
    insertData($non_tag_data, 'ret_nontag_item_log');
    insertData($section_non_tag_data, 'ret_section_nontag_item_log');
} else {
    insertData($nt_data, 'ret_nontag_item');   // create new line
    insertData($non_tag_data, 'ret_nontag_item_log');
    insertData($section_non_tag_data, 'ret_section_nontag_item_log');
}
```
This pattern is copy-pasted across **Melting Receipt, Testing Receipt, Refining Receipt, Polishing Receipt** — any bug fix here must be applied to all 4 occurrences.

---

## 5. GST Tax Logic

```php
// In controller metal_process/save for Refining Receipt:
'receipt_charges_tax_cgst' => ($chg_tax_type==1) ? $tax_value/2 : 0,
'receipt_charges_tax_sgst' => ($chg_tax_type==1) ? $tax_value/2 : 0,
'receipt_charges_tax_igst' => ($chg_tax_type==0) ? $tax_value : 0,
```
`chg_tax_type` is determined by `get_chg_tax_type()`:
- Compares karigar `id_state` with company `id_state`
- **Bug**: uses `$data['comp_details'][0]['id_company']` as company state → should be `id_state`

---

## 6. Lot Inward Numbers by Process Type

| Process | `lot_from` | `lot_type` | `narration` |
|---|---|---|---|
| Melting Receipt | 2 | 1 | *(not explicitly set in view)* |
| Testing Receipt | 4 | 1 | *(from controller code)* |
| Polishing Receipt | 5 | 1 | 'From Polishing Process' |
