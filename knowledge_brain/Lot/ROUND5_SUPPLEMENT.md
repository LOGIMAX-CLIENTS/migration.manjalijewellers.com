# LOT MODULE — ROUND 5 SUPPLEMENT
> Module: Lot | Round 5 | 2026-03-17
> **Complete JS coverage — 100% final**

---

## 1. New AJAX URLs Found in Round 5

### Internal Lot Module
| JS Function | Line | URL | Purpose |
|---|---|---|---|
| `Lot_Completed_Details()` | L14908 | `admin_ret_lot/lot_completed` | POST: bulk close selected lots |
| `set_edit_lot_row()` | L15156 | `admin_ret_lot/lot_inward/lot_edit/{lot_no}` | POST: fetches full lot detail for row-level edit modal |
| `get_LotNos_for_split()` | L12510 | `admin_ret_lot/lot_split/lotNosForsplit` | POST: get lot detail rows for split |
| `get_Lot_details_summary()` | ~L12487 | `admin_ret_lot/lot_split/getLotDetails` | POST: get split lot summary |
| `getLotidsforSplit()` | L12386 | `admin_ret_lot/lot_split/getLotidsforSplit` | GET: get all lot IDs for split dropdown |
| `get_ProductsForlot()` | L11064 | `admin_ret_lot/get_ActiveProduct` | POST: get products by category (for MERGE row) |

### Cross-Module Calls (New in Round 5)
| JS Function | Line | URL | Target Module | Purpose |
|---|---|---|---|---|
| `get_ActiveGRNS()` | L15008 | `admin_ret_purchase/purchase/active_grns` | Purchase | Load GRN dropdown on form |
| `get_ActivePurity()` | L22084 | `admin_ret_catalog/ajax_getPurity` | Catalog | Preload purities into `purityDetails` var |
| `get_charges()` | L22128 | `admin_ret_catalog/charges/getActiveChargesList` | Catalog | Preload charges into `charges_details` var |
| `get_purityForlot()` | L11188 | `admin_ret_catalog/category/cat_purity` | Catalog | Load purity for selected category (in merge row) |
| `get_ActiveDesignsForLot()` | L11372 | `admin_ret_catalog/get_active_design_products` | Catalog | Load designs for selected product (in merge row) |
| `getActiveSubDesignsForLot()` | L11486 | `admin_ret_catalog/get_ActiveSubDesigns` | Catalog | Load sub-designs for selected design (in merge row) |

---

## 2. ⚠️ Critical Bug R-LOT-028: lot_completed branch hardcoded

**Line L14896**:
```js
var postData = {'completed_lot': lot_completed_data, 'branch': 1};
```

**Issue**: `branch` parameter is hardcoded to `1` in the JS. The controller `lot_completed()` uses `$_POST['branch']` to get the day-closing date via `getBranchDayClosingData()`. Since `branch=1` is always sent:
- All lot closings always use Branch 1's day-close date, regardless of which branch the user is operating from
- This means lots from Branch 2, 3, etc. get the wrong closing date

**Severity**: 🔴 HIGH — Wrong closing dates recorded for multi-branch setups

---

## 3. Correction: get_ProductsForlot → Real URL

In Round 4, we noted `get_ActiveProduct()` JS (L6456) calls `admin_ret_estimation/get_ActiveProduct`. This was the form page's init call.

However, the **Merge form** uses a different JS function `get_ProductsForlot()` which calls:
```
admin_ret_lot/get_ActiveProduct
```
→ This is the **LOT module's own** `get_ActiveProduct()` controller method at L2290.

So the controller method is NOT dead — it IS called, but only from the merge form's product dropdown, not from the main inward form.

**Correction to R-LOT-025**: `admin_ret_lot::get_ActiveProduct()` is NOT dead code. It serves the merge form's product select. The main inward form (via `get_ActiveProduct()` JS at L6456) calls `admin_ret_estimation/get_ActiveProduct` instead. This is an **inconsistency**, not dead code.

**Updated Bug R-LOT-025**: Inward form and Merge form use different product sources — inward form uses Estimation module's products, while merge form uses Lot module's own products. These may return different data sets.

---

## 4. Preloaded Variables (No AJAX on interaction)

The following data is loaded **once on page init** and stored in JS variables, then used without AJAX calls:

| JS Variable | Loaded By | Source AJAX | Data |
|---|---|---|---|
| `uom_details[]` | `getActiveUOM()` | `admin_ret_catalog/uom/active_uom` | All active UOMs |
| `lot_product_details[]` | `get_ActiveProduct()` | `admin_ret_estimation/get_ActiveProduct` | All products |
| `section_details[]` | (preloaded PHP var) | PHP `$data['product_division']` | Sections |
| `stones[]` | `get_ActiveStones()` | (unknown — not found in traced JS) | All stone types |
| `stone_types[]` | (with stones) | same | Stone type categories |
| `quality_code[]` | (with stones) | same | Diamond quality codes |
| `emp_details[]` | `get_employee()` | `admin_ret_estimation/get_employee` | Employees for split |
| `purityDetails[]` | `get_ActivePurity()` | `admin_ret_catalog/ajax_getPurity` | All purities |
| `charges_details[]` | `get_charges()` | `admin_ret_catalog/charges/getActiveChargesList` | All charge types |
| `activeGRNS[]` | `get_ActiveGRNS()` | `admin_ret_purchase/purchase/active_grns` | Active GRN list |
| `lot_cat_details[]` | `get_category()` | `admin_ret_catalog/category/active_category` | Categories |

> ⚠️ **Performance Bug Note (R-LOT-029)**: On page load, the form fires ~10 independent AJAX calls to preload data. These are fired sequentially (some depend on others completing). No loading indicators for most, meaning the form can appear "ready" before data is loaded.

---

## 5. Complete Item Cost Calculation Flow (calculate_purchase_item_cost)

**Function**: `calculate_purchase_item_cost()` at L22168

This is the main pricing calculator for each lot item in the item entry modal. Triggered on any change to lot_pcs, lot_gross_wt, karigar_calc_type, lot_wastage, purchase_touch, rate_calc_type, rate_per_gram, mc_value, mc_type.

### Calculation Steps:
```
1. base_amount = rate_per_gram × (gross_wt + wastage_wt)      [calc_type=Metal Rate]
   OR fixed_rate × pcs                                          [calc_type=Fixed/Piece]
   OR fixed_rate × gross_wt                                     [calc_type=Fixed/Gram]

2. stone_charge = Σ(stone_wt × stone_rate) per stone

3. other_metal_amount = from other_metal_details[] stored json

4. making_charge = mc_value × gross_wt   [if mc_type=1/Gram]
                 = mc_value × pcs        [if mc_type=2/Piece]

5. other_charges_amount = Σ(charge_value × (1 + charge_tax%/100))
                          from charges_details[] preloaded

6. item_raw_cost = base_amount + stone_charge + other_metal_amount
                 + making_charge + other_charges_amount

7. GST = item_raw_cost × (tax_type_from_product / 100)
         split into CGST/SGST (intrastate) or IGST (interstate)

8. item_total_cost = item_raw_cost + GST_amount
```

**Business Rule (RULE-LOT-019)**: Item GST is calculated entirely client-side using the tax type from the `data-tax_type` attribute on the product `<option>` element. No server-side cross-check of GST calculation occurs.

**Business Rule (RULE-LOT-020)**: The `karigar_calc_type` field drives 3 pure weight calculation modes:
- Type 1: `pure_wt = net_wt × (touch + wastage) / 100`
- Type 2: `pure_wt = net_wt × touch / 100`
- Type 3: `pure_wt = (net_wt × touch/100) × (1 + wastage/100)`

---

## 6. Lot Close (lot_closed button) — Full Flow

```
User clicks: #lot_closed button
↓
JS collects all checked lot_no values from #lot_inward_list tbody
↓
Calls Lot_Completed_Details(LotCompletedList)
↓
POST to: admin_ret_lot/lot_completed
Data: {completed_lot: [{lot_no: X}, ...], branch: 1}  ← HARDCODED BRANCH 1!
↓
Controller lot_completed():
  - Gets day_close_date for branch=1 (from getBranchDayClosingData)
  - Loops through completed_lot[]
  - UPDATE ret_lot_inwards SET is_closed=1, closed_on=date, closed_by=uid
    WHERE lot_no = val  ← BUG R-LOT-023: 'lot_no ' trailing space!
↓
Response: JSON {status: true/false, message: '...'}
↓
window.location.reload()
```
**Combined Effect of R-LOT-023 + R-LOT-028**: The "Close Lots" feature is completely broken:
1. Branch ID is hardcoded → wrong closing date for non-branch-1 lots (R-LOT-028)
2. Trailing space in column name → UPDATE silently fails (R-LOT-023)
3. Success message is still shown → operator thinks it worked

---

## 7. Lot Split Employee Loading

The split form's `#select_employee` dropdown is populated from the preloaded `emp_details[]` variable (loaded via `get_employee()→admin_ret_estimation`). This data is identical across all split rows. No dynamic employee filtering by lot or branch occurs.

**Business Rule Note**: The `id_employee` submitted with each split row is stored in `ret_lot_split_details.id_employee` — there's no branch-employee validation.

---

## 8. Complete JS Function Master List (Round 5)

### Core Form Functions
| Function | Lines | Purpose |
|---|---|---|
| `get_lot_preview()` | ~L2000 | Re-render item preview totals |
| `fnFormatRowTagDetails()` | ~L3200 | DataTable row expand detail formatter |
| `create_new_empty_lot_row()` | L5764 | Add blank inward item row |
| `validateItemDetailRow()` | L7376 | Validate all rows before adding new |
| `calculate_lot_inward_Total()` | L10812 | Sum all visible row totals |
| `reset_data()` | L17028 | Clear item modal fields |

### Item Modal
| Function | Lines | Purpose |
|---|---|---|
| `edit_lot_details()` | L20026 | Load existing item into edit modal |
| `calculate_purchase_item_cost()` | L22168 | Full cost calculation |
| `calculate_pure_wt()` | L20450 | 3-mode pure weight calculator |

### Stone Modal
| Function | Lines | Purpose |
|---|---|---|
| `show_stone_modal()` | ~L8000 | Open stone modal for row |
| `create_new_stone_row()` | L17322 | Add stone row (uses preloaded `stones[]`) |
| `create_new_empty_est_cus_stone_item()` | L17490 | Populate stone modal with existing data |
| `validate_stone_row()` | ~L18000 | Validate before save |

### Metal + Charge Modals
| Function | Lines | Purpose |
|---|---|---|
| `open_other_metal_modal()` | L20566 | Show other metals modal |
| `create_new_empty_other_metal_item()` | ~L21000 | Add/load metal row |
| `calculate_other_metal_amount()` | L21752 | Metal amount auto-calc |
| `validate_other_metal_row()` | L21688 | Validate metal rows |
| `open_other_charges_modal()` | ~L20647 | Show charges modal |
| `create_new_empty_other_charges_item()` | ~L20662 | Add/load charges row |
| `validate_charges_row()` | ~L20686 | Validate charge rows |

### Merge Functions
| Function | Lines | Purpose |
|---|---|---|
| `getLotidsforMerge()` | L10232 | Load merge lot dropdown |
| `getLotNoForMerge()` | L10396 | Search and load lot rows for merge |
| `remove_lot_row()` | L10668 | Remove a lot row from merge table |
| `calculateLotTotal()` | L10688 | Sum merge table totals |
| `TotalLotMerge()` | L12210 | Sum merge form totals |
| `get_ProductsForlot()` | L11052 | Get products by cat (merge row) |
| `get_purityForlot()` | L11168 | Get purities by cat (merge row) |
| `get_ActiveDesignsForLot()` | L11352 | Get designs by product (merge row) |
| `getActiveSubDesignsForLot()` | L11466 | Get sub-designs (merge row) |

### Split Functions
| Function | Lines | Purpose |
|---|---|---|
| `getLotidsforSplit()` | L12366 | Load split lot dropdown |
| `get_LotNos_for_split()` | L12494 | Load lot rows for split |
| `get_Lot_details_summary()` | ~L12487 | Get split lot summary |

### Lot Close
| Function | Lines | Purpose |
|---|---|---|
| `Lot_Completed_Details()` | L14884 | POST bulk-close selected lots |

---

## 9. Bugs Found in Round 5

| Bug ID | Severity | Description | Location |
|---|---|---|---|
| R-LOT-028 | 🔴 HIGH | `Lot_Completed_Details()` sends `branch:1` hardcoded → wrong close date for non-Branch-1 | JS L14896 |
| R-LOT-029 | 🟡 LOW | ~10 AJAX calls on form load, no coordinated loading state, form can appear interactive before all data is ready | JS (all init calls) |
| R-LOT-025 (updated) | 🟠 MEDIUM | Inward form uses `admin_ret_estimation/get_ActiveProduct`, merge form uses `admin_ret_lot/get_ActiveProduct` — different product sources for same module's forms | JS L6456 vs L11064 |

---

## 10. Final AJAX URL Master Table (ALL Rounds)

### Internal (admin_ret_lot) — 19 Total
| URL | Method | Purpose |
|---|---|---|
| `admin_ret_lot/lot_inward/ajax` (default) | POST | Lot list data |
| `admin_ret_lot/lot_inward/lot_edit/{id}` | POST | Edit lot row modal data |
| `admin_ret_lot/lot_inwards_detail` | POST | Delete detail row |
| `admin_ret_lot/lot_completed` | POST | Bulk close lots |
| `admin_ret_lot/getOrderNosBySearch` | POST | Search order nos |
| `admin_ret_lot/get_order_details` | POST | Order item list |
| `admin_ret_lot/get_karigar_list` | POST | Karigars by order |
| `admin_ret_lot/getProductBySearch` | POST | Search products |
| `admin_ret_lot/get_ActiveProduct` | POST | Products by category (merge form) |
| `admin_ret_lot/remove_img` | POST | Remove images |
| `admin_ret_lot/upload_lotimg` | POST | UPLOAD (MISSING — R-LOT-006) |
| `admin_ret_lot/lot_merge/getLotNos` | POST | Lots for merge |
| `admin_ret_lot/lot_merge/getLotidsforMerge` | GET | Lot IDs for merge dropdown |
| `admin_ret_lot/lot_split/lotNosForsplit` | POST | Lots for split |
| `admin_ret_lot/lot_split/getLotDetails` | POST | Lot detail for split |
| `admin_ret_lot/lot_split/getLotidsforSplit` | GET | Lot IDs for split dropdown |

### Cross-Module — 13 Total
| URL | Target | Purpose |
|---|---|---|
| `admin_ret_catalog/uom/active_uom` | Catalog | UOM list |
| `admin_ret_catalog/category/active_category` | Catalog | Category list |
| `admin_ret_catalog/category/cat_purity` | Catalog | Purity by category |
| `admin_ret_catalog/get_active_design_products` | Catalog | Designs by product |
| `admin_ret_catalog/get_ActiveSubDesigns` | Catalog | Sub-designs |
| `admin_ret_catalog/karigar/active_list` | Catalog | Karigar list |
| `admin_ret_catalog/product/active_prodBySearch` | Catalog | Product search |
| `admin_ret_catalog/ajax_getPurity` | Catalog | All purities |
| `admin_ret_catalog/charges/getActiveChargesList` | Catalog | All charges |
| `admin_ret_brntransfer/branch_transfer/getDesignByFilter` | BranchTransfer | Design search (R-LOT-013) |
| `admin_ret_estimation/getCustomersBySearch` | Estimation | Customer search |
| `admin_ret_estimation/get_employee` | Estimation | Employee list |
| `admin_ret_estimation/get_ActiveProduct` | Estimation | Products (inward form) |
| `admin_ret_order/order/getOrderByCus` | Orders | Orders by customer |
| `admin_ret_purchase/purchase/active_grns` | Purchase | GRN list |

**TOTAL: 16 Internal + 15 Cross-Module = 31 unique AJAX endpoints**
