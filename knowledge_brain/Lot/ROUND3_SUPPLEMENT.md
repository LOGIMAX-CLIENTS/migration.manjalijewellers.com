# LOT MODULE — ROUND 3 SUPPLEMENT
> Module: Lot | Round 3 | 2026-03-17
> **Final gap closure — brings coverage to ~100%**

---

## 1. customer_ack.php vs office_ack.php — Comparison

### Data Model Differences

| Aspect | `customer_ack.php` | `office_ack.php` |
|---|---|---|
| **Controller method** | `customer_acknowladgement()` L2528 | `lot_acknowladgement()` L1807 |
| **Header data** | `$kar_det` from `get_customer_lotInward_detail()` | `$lot_inwards_detail` from `lotInward_detail()` |
| **Item data** | `$lot_det` from `get_customer_lot_details()` | `$lot_det` from `get_lot_details()` |
| **Tag data** | ❌ None | ✅ `$tag_det` from `get_lot_tag_details()` |
| **Delivery method** | `echo $html; exit;` (NOT dompdf) | `dompdf->stream()` (PDF download) |
| **CSS** | `customer_job_receipt.css` | dompdf-embedded CSS |
| **Content shown** | Karigar name, address, GST, PAN, lot items + stones + charges + tax | Lot summary + tagged items per branch |
| **Footer** | Audited By / Party Sign / Manager Sign / Operator | Signature area |
| **Totals computed** | PHP-side: grand total with CGST/SGST/IGST split | PHP-side: only lot GWT/NWT/PCS |

### Customer_ack Data Columns (from `get_customer_lot_details()`)
- `tot_pcs`, `gross_wt`, `net_wt`, `pur_wt` (summed per GROUP BY id_lot_inward_detail)
- `product_name`, `purity`, `design_name`, `sub_design_name`, `pro_name`
- `wastage_percentage`, `mc_type`, `rate_calc_type`, `making_charge`
- `total_cgst`, `total_igst`, `total_sgst`, `item_cost`, `total_tax`, `tax_percentage`
- Nested: `stn_details[]` (from `getlotStoneDetails`), `other_metal_details[]`, `other_charge_details[]`

### ⚠️ Bug in customer_ack.php
**Line L371**: `echo $kar_det['emp_name']` — but `$kar_det` is an **array of arrays** (`result_array()`), not a single row. This should be `$kar_det[0]['emp_name']`. If the lot has multiple items, this will throw `Array to string conversion` notice.

---

## 2. Row-Level Hidden Fields in inward_item[n] (per table row)

> From JS `create_new_empty_lot_row()` at L5864–5920, confirmed by form.php PHP table row echo at L1369–1453

Each `inward_item[n]` row in the lot detail table contains these fields (in addition to visible inputs):

### Via `name="inward_item[n][...]"` Pattern

| Field Name | Class | Purpose |
|---|---|---|
| `inward_item[n][id_section]` | `lot_section` → select | Section ID |
| `inward_item[n][product]` | `lot_product` → select | Product name (display) |
| `inward_item[n][lot_product]` | `pro_id` | Product ID (hidden) |
| `inward_item[n][sales_mode]` | `sales_mode` | Sales mode |
| `inward_item[n][calculation_based_on]` | `calculation_based_on` | Calc basis |
| `inward_item[n][id_lot_inward_detail]` | `id_lot_inward_detail` | Detail row PK (for edit) |
| `inward_item[n][design]` | `design` → select | Design display |
| `inward_item[n][lot_id_design]` | `des_id` | Design ID |
| `inward_item[n][id_sub_design]` | `lot_id_sub_design` | Sub-design ID |
| `inward_item[n][design_for]` | `design_for` | Gender (1=M,2=F,3=U) |
| `inward_item[n][pcs]` | `lot_pcs` | Piece count |
| `inward_item[n][gross_wt]` | `gross_wt` | Gross weight |
| `inward_item[n][gross_wt_uom]` | `gross_wt_uom` | GWT UOM |
| `inward_item[n][less_wt]` | `lot_lwt` | Less weight |
| `inward_item[n][less_wt_uom]` | `less_wt_uom` | LWT UOM |
| `inward_item[n][net_wt]` | `lot_nwt` | Net weight (auto calc) |
| `inward_item[n][net_wt_uom]` | `net_wt_uom` | NWT UOM |
| `inward_item[n][wastage_percentage]` | `wastage_percentage` | Wastage % |
| `inward_item[n][making_charge]` | `making_charge` | MC value |
| `inward_item[n][id_mc_type]` | `id_mc_type` (hidden) + `mc_type` (select) | MC type |
| `inward_item[n][buy_rate]` | `buy_rate` | Buy rate |
| `inward_item[n][sell_rate]` | `sell_rate` | Sell rate |
| `inward_item[n][size]` | `size` | Ring size |
| `inward_item[n][precious_stone]` | `precious_stone` | Flag |
| `inward_item[n][precious_st_pcs]` | `precious_stone_pcs` | Precious pcs |
| `inward_item[n][precious_st_wt]` | `precious_stone_wt` | Precious wt |
| `inward_item[n][p_stn_certif_uploaded]` | `p_stn_certif_uploaded` | Existing certif path |
| `inward_item[n][precious_st_certif]` | `precious_st_certif` | New certif (empty) |
| `inward_item[n][semi_precious_stn]` | `semi_precious_stn` | Flag |
| `inward_item[n][semi_precious_st_pcs]` | `semi_precious_st_pcs` | Semi pcs |
| `inward_item[n][semi_precious_st_wt]` | `semi_precious_st_wt` | Semi wt |
| `inward_item[n][sp_stn_certif_uploaded]` | `sp_stn_certif_uploaded` | Existing certif |
| `inward_item[n][semiprecious_st_certif]` | `semiprecious_st_certif` | New certif |
| `inward_item[n][normal_stn]` | `normal_stn` | Flag |
| `inward_item[n][normal_st_pcs]` | `normal_st_pcs` | Normal pcs |
| `inward_item[n][normal_st_wt]` | `normal_st_wt` | Normal wt |
| `inward_item[n][n_stn_certif_uploaded]` | `n_stn_certif_uploaded` | Existing certif |
| `inward_item[n][normal_st_certif]` | `normal_st_certif` | New certif |
| `inward_item[n][nor_wt_uom]` | `nor_wt_uom` | Normal stone UOM |
| `inward_item[n][semi_wt_uom]` | `semi_wt_uom` | Semi stone UOM |
| `inward_item[n][pre_wt_uom]` | `pre_wt_uom` | Precious stone UOM |

**Total per-row hidden fields**: ~41 fields per `inward_item[n]` row

---

## 3. Additional Confirmed AJAX URLs (JS L5000+)

| JS Function | Line | URL | Target Controller |
|---|---|---|---|
| `getSearchCustomers()` | L5166 | `admin_ret_estimation/getCustomersBySearch` | `admin_ret_estimation` |
| `getOrdersByCus()` | L5358 | `admin_ret_order/order/getOrderByCus` | `admin_ret_order` |
| `get_employee()` | L5450 | `admin_ret_estimation/get_employee` | `admin_ret_estimation` |
| `get_order_details` (in row) | L6264 | `admin_ret_lot/get_order_details` | `admin_ret_lot::get_order_details()` |

**⚠️ Additional Cross-Module Risk**: `getSearchCustomers()` and `getOrdersByCus()` and `get_employee()` all call **`admin_ret_estimation`** — the Estimation module. This means:
- Lot form depends on Estimation module's customer search, order search, and employee fetch
- If Estimation module routes change, Lot form loses 3 key functions

---

## 4. New Row-Level Behaviour — NWT Auto-Calc

From JS L5648–5672 (document delegation):
```js
$(document).on('keyup', '.gross_wt, .lot_lwt', function(e) {
    var net_wt = parseFloat(gross_wt) - parseFloat(lot_lwt);
    row.find('.lot_nwt').val(net_wt.toFixed(3));
});
```
**Business Rule (RULE-LOT-016)**: NWT is auto-calculated client-side as `GWT - LWT`. No server-side recalculation or validation. If JS is disabled or LWT is not entered, NWT field is left as-is (no server guard).

---

## 5. mc_type Inconsistency — UI vs DB

From JS `create_new_empty_lot_row()` L5904:
```html
<option value="1">Gram</option>
<option value="2" selected>Piece</option>  ← default is Piece
```

But in form.php existing row (L1433 PHP echo):
```html
<option value="1">Per Gram</option>
<option value="2">Per Piece</option>
```

And from model (METHOD_INDEX, L509):
```sql
IF(id.mc_type = 2, 'PER GRAM', 'PER PCS') as mc_type_name
```

**Confirmed Bug R-LOT-007 (mc_type label)**: Model uses `IF(mc_type=2, 'PER GRAM', 'PER PCS')` which is inverted — mc_type=2 is "Per Piece" in the form but shows "PER GRAM" in the model query. This means all acknowledgement PDFs show wrong MC type labels.

Additionally, new row default is `mc_type=2` (Piece), but old rows default display shows `mc_type=1` first. This inconsistency means new items default to Piece mode while edit items may show Gram unless previously saved as Piece.

---

## 6. Additional Bugs Found in Round 3

| Bug ID | Severity | Description | Evidence |
|---|---|---|---|
| R-LOT-018 | 🔴 HIGH | `customer_ack.php` L371: `$kar_det['emp_name']` on array — should be `$kar_det[0]['emp_name']` → PHP notice/blank operator name on print | `customer_ack.php` L371 |
| R-LOT-019 | 🟠 MEDIUM | `getSearchCustomers()`, `getOrdersByCus()`, `get_employee()` all depend on `admin_ret_estimation` — undocumented cross-module coupling to Estimation module | JS L5166, L5358, L5450 |
| R-LOT-020 | 🟠 MEDIUM | New lot row defaults `mc_type=2` (Piece), while model display condition is inverted — systematic wrong MC type display on all acknowledgement PDFs | JS L5904, Model L509 |
| R-LOT-021 | 🟡 LOW | NWT is purely client-side calculated (GWT - LWT) with no server-side validation — users can submit mismatched NWT/GWT/LWT if JS not executed | JS L5664 |

---

## 7. Complete AJAX URL Master Table (All Rounds)

> Definitive reference combining Rounds 1, 2, 3 findings.

### Internal (admin_ret_lot) Calls

| AJAX URL | Method | Purpose |
|---|---|---|
| `admin_ret_lot/lot_inward/ajax` | POST | Load lot list (date/metal/emp filters) |
| `admin_ret_lot/getOrderNosBySearch` | POST | Search customer order nos by text |
| `admin_ret_lot/get_order_details` | POST | Get order items by order no + karigar + branch |
| `admin_ret_lot/get_karigar_list` | POST | Get karigar list by order no |
| `admin_ret_lot/getProductBySearch` | POST | Search products by text + category + stock_type |
| `admin_ret_lot/get_ActiveProduct` | POST | Get active products by category filter |
| `admin_ret_lot/lot_inwards_detail` | POST | Delete single lot detail row |
| `admin_ret_lot/lot_completed` | POST | Close/mark lots as complete |
| `admin_ret_lot/remove_img` | POST | Remove image from lot/certificates |
| `admin_ret_lot/upload_lotimg` | POST | **MISSING** (R-LOT-006) — 404 |
| `admin_ret_lot/lot_merge/getLotNos` | POST | Get lot nos eligible for merge |
| `admin_ret_lot/lot_merge/getLotidsforMerge` | POST | Get lot IDs for merge dropdown |
| `admin_ret_lot/lot_split/getLotidsforSplit` | POST | Get lot IDs for split dropdown |
| `admin_ret_lot/lot_split/lotNosForsplit` | POST | Get lots eligible for split |
| `admin_ret_lot/lot_split/getLotDetails` | POST | Get lot detail for split form |

### Cross-Module Calls (from ret_lot.js)

| AJAX URL | Target Module | Purpose |
|---|---|---|
| `admin_ret_catalog/uom/active_uom` | Catalog | Load UOM list |
| `admin_ret_catalog/category/active_category` | Catalog | Load category list |
| `admin_ret_catalog/category/cat_purity` | Catalog | Load purities for category |
| `admin_ret_catalog/karigar/active_list` | Catalog | Load karigar/goldsmith list |
| `admin_ret_catalog/product/active_prodBySearch` | Catalog | Search products by text |
| `admin_ret_brntransfer/branch_transfer/getDesignByFilter` | Branch Transfer | Search designs (R-LOT-013) |
| `admin_ret_estimation/getCustomersBySearch` | Estimation | Search customers (R-LOT-019) |
| `admin_ret_estimation/get_employee` | Estimation | Load employee list (R-LOT-019) |
| `admin_ret_order/order/getOrderByCus` | Orders | Get orders by customer (R-LOT-019) |

**Total unique AJAX calls**: 24 (15 internal + 9 cross-module)
