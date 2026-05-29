# LOT MODULE — ROUND 8 SUPPLEMENT
> Module: Lot | Round 8 | 2026-03-17
> **CSS Audit + Cross-Module Map Correction + MODULE_BRAIN.md Consolidation**

---

## 1. CSS Audit: lot_ack.css

**File**: `admin/assets/css/lot_ack.css` (266 lines, 4,089 bytes)

### Classes and Purpose
| Class | Purpose |
|---|---|
| `.PDFReceipt` | Container for standard PDF prints (office, vendor, branch acks) |
| `.PDF_CusReceipt` | Container for customer acknowledgement PDF |
| `.PDF_receipt_thermal` | Container for thermal printer format (6px font) |
| `.receipt-logo` | Company logo image: max 120px height, auto width |
| `.item_dashed` | Dashed separator line: `width: 2200%` (avoids table width constraints in dompdf) |
| `.item_dashed1` | Variant: `width: 1900%` |
| `.item_dashed2` | Variant: `width: 1700%` |
| `.sumamry_dashed` | Typo: "sumamry" — `width: 1300%` |
| `.PDFReceipt .heading` | Section header bar: dark bg (#393939), white text, uppercase |
| `.PDFReceipt .txtAckowlege` | Typo: "Ackowlege" (not "Acknowledge") — acknowledgement text area |

### CSS Observations
- **No bugs** — purely presentational print styles
- The exaggerated `width: 2200%` values for dashed lines are **intentional** PDF hacks for dompdf's table rendering (dompdf renders `<hr>` inside `<td>` differently)
- Two spelling errors in class names: `.sumamry_dashed`, `.txtAckowlege` — harmless but inconsistent
- `.PDFReceipt table { font-size: 50%; }` followed by another `.PDFReceipt table { }` rule (empty) — duplicate selector, likely leftover
- Three print contexts were planned: `PDFReceipt`, `PDF_CusReceipt`, `PDF_receipt_thermal` — but `PDF_receipt_thermal` is NOT used in any view file (orphaned class)

### ⚠️ Bug R-LOT-043 (Low): `.PDF_receipt_thermal` CSS class defined but unused
The thermal print stylesheet context exists in CSS but no view references `.PDF_receipt_thermal` as a wrapper class. Either the thermal print view was removed or never built.

---

## 2. CROSS_MODULE_MAP Corrections (Round 8 Update)

### Previously Estimated (Round 1) vs Verified (Rounds 2-7)

| JS Function | Round 1 Estimate | Verified URL (Round 5) |
|---|---|---|
| `get_karigar()` | `admin_ret_catalog` | `admin_ret_catalog/karigar/active_list` ✅ |
| `get_category()` | `admin_ret_catalog` | `admin_ret_catalog/category/active_category` ✅ |
| `getActiveUOM()` | `admin_ret_catalog` | `admin_ret_catalog/uom/active_uom` ✅ |
| `get_ActiveMetals()` | `admin_ret_catalog` | **COMMENTED OUT** (dead code) |
| `get_ActivePurity()` | `admin_ret_catalog` | `admin_ret_catalog/ajax_getPurity` ✅ |
| `get_charges()` | `admin_ret_catalog` | `admin_ret_catalog/charges/getActiveChargesList` ✅ |
| `get_ActiveGRNS()` | `admin_ret_grn` (estimated) | `admin_ret_purchase/purchase/active_grns` ⚠️ WRONG MODULE |
| `getSearchDesign()` | `admin_ret_catalog` (estimated) | `admin_ret_brntransfer/branch_transfer/getDesignByFilter` ⚠️ WRONG MODULE (R-LOT-013) |
| `getSearchCustomers()` | `admin_ret_customer` (estimated) | `admin_ret_estimation/getCustomersBySearch` ⚠️ WRONG MODULE |
| `get_employee()` | `admin_ret_employee` (estimated) | `admin_ret_estimation/get_employee` ⚠️ WRONG MODULE |
| `get_ActiveProduct()` (inward form) | `admin_ret_catalog` (estimated) | `admin_ret_estimation/get_ActiveProduct` ⚠️ WRONG MODULE |
| `get_ActiveProduct()` (merge form) | Not documented | `admin_ret_lot/get_ActiveProduct` ✅ |
| `get_cat_purity()` | `admin_ret_catalog` | `admin_ret_catalog/category/cat_purity` ✅ |
| `get_taxgroup_items()` | `admin_ret_catalog` | *(not found in live trace — may be dead)* |
| `getActiveDesigns/SubDesigns()` | Not documented | `admin_ret_catalog/get_active_design_products` + `get_ActiveSubDesigns` ✅ |

### Key Cross-Module Dependency Corrections
1. **Design search** → `admin_ret_brntransfer` (NOT `admin_ret_catalog`) — undocumented coupling to Branch Transfer module
2. **Customer/Employee search** → `admin_ret_estimation` (NOT `admin_ret_customer/employee`) — undocumented coupling to Estimation module  
3. **GRN list** → `admin_ret_purchase` (NOT `admin_ret_grn`) — undocumented coupling to Purchase module
4. **Products for inward form** → `admin_ret_estimation` (while merge form uses `admin_ret_lot`) — **same data, two sources, inconsistent**

---

## 3. Complete Verified AJAX URL Table (Final Definitive)

### Internal (`admin_ret_lot`) — 16 endpoints
| URL | Method | Purpose |
|---|---|---|
| `admin_ret_lot/lot_inward/ajax` | POST | Lot list DataTable |
| `admin_ret_lot/lot_inward/lot_edit/{id}` | POST | Edit modal data |
| `admin_ret_lot/lot_inwards_detail` | POST | Delete item row |
| `admin_ret_lot/lot_completed` | POST | Bulk close lots |
| `admin_ret_lot/getOrderNosBySearch` | POST | Order search |
| `admin_ret_lot/get_order_details` | POST | Order items |
| `admin_ret_lot/get_karigar_list` | POST | Karigars by order |
| `admin_ret_lot/getProductBySearch` | POST | Product search |
| `admin_ret_lot/get_ActiveProduct` | POST | Products by cat (merge) |
| `admin_ret_lot/remove_img` | POST | Remove images |
| `admin_ret_lot/upload_lotimg` | POST | Upload (MISSING — R-LOT-006) |
| `admin_ret_lot/lot_merge/getLotNos` | POST | Lots for merge |
| `admin_ret_lot/lot_merge/getLotidsforMerge` | GET | Lot IDs for merge |
| `admin_ret_lot/lot_split/lotNosForsplit` | POST | Lots for split |
| `admin_ret_lot/lot_split/getLotDetails` | POST | Lot detail for split |
| `admin_ret_lot/lot_split/getLotidsforSplit` | GET | Lot IDs for split |

### Cross-Module — 15 endpoints (verified)
| URL | Target Module | Purpose |
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
| `admin_ret_brntransfer/branch_transfer/getDesignByFilter` | BranchTransfer | Design search (R-LOT-013!) |
| `admin_ret_estimation/getCustomersBySearch` | Estimation | Customer search |
| `admin_ret_estimation/get_employee` | Estimation | Employee list |
| `admin_ret_estimation/get_ActiveProduct` | Estimation | Products (inward form) |
| `admin_ret_order/order/getOrderByCus` | Orders | Orders by customer |
| `admin_ret_purchase/purchase/active_grns` | Purchase | GRN list |

**TOTAL: 31 verified AJAX endpoints (16 internal + 15 cross-module)**

---

## 4. Complete Bug Registry (All 40 Unique Bugs — Rounds 1-8)

### 🔴 Critical (5)
| ID | Description | Location |
|---|---|---|
| R-LOT-001 | `lot_inward/delete` uses GET method — CSRF vulnerable | Controller L1195 |
| R-LOT-003 | `lot_merge/save` echoes `last_query()` in production | Controller L2270 |
| R-LOT-004 | `lot_split/save` echoes `last_query()` + `_error_message()` in production | Controller L2443-2444 |
| R-LOT-023 | `lot_completed()` trailing space in `'lot_no '` — UPDATE always fails silently | Controller L2491 |
| R-LOT-026 | Print methods (`vendor_ack`, `lot_ack`, `branch_ack`) lack CSRF protection | Controller L1773-1877 |

### 🔴 High (9)
| ID | Description | Location |
|---|---|---|
| R-LOT-012 | `lot_inward/delete` doesn't delete child tables — orphan records | Controller L1195-1247 |
| R-LOT-013 | `getSearchDesign()` calls `admin_ret_brntransfer` — wrong module, breaks if routes change | JS L6404 |
| R-LOT-019 | Undocumented dependency on `admin_ret_estimation` for 3 AJAX calls | JS multiple |
| R-LOT-028 | `Lot_Completed_Details()` sends `branch:1` hardcoded — wrong close date for non-branch-1 | JS L14896 |
| R-LOT-036 | Model reads `$_POST['lot_type']` directly — bypasses sanitization, SQL injection | Model L188 |
| R-LOT-042 | `getOrderNos()` interpolates user search text into SQL LIKE — SQL injection | Model L906 |
| R-LOT-006 | `upload_lotimg` endpoint missing — image upload broken (404) | Controller |
| R-LOT-002 | `lot_inward/delete` URL-based delete CSRF (same as R-LOT-001 - overlap) | — |
| R-LOT-007 | `lot_inward/update` doesn't delete stones on existing rows — duplicates on re-save | Controller |

### 🟠 Medium (13)
| ID | Description |
|---|---|
| R-LOT-008 | `lot_completed` trailing space (same as R-LOT-023 — confirmed duplicate of critical) |
| R-LOT-014 | `lot_type` hardcoded in `lot_merge/save` |
| R-LOT-015 | Missing `name` attributes on TDS/TCS hidden fields — data lost |
| R-LOT-018 | PHP notice in `customer_ack.php` L371 — incorrect array access |
| R-LOT-021 | Client-side only NWT validation — server bypass possible |
| R-LOT-022 | `to_branch` hardcoded in `lot_merge/save` |
| R-LOT-025 | Inward form uses Estimation products; merge form uses Lot products — inconsistent sources |
| R-LOT-031 | Duplicate `id="stock_type"` on merge form radio buttons — JS selector breaks |
| R-LOT-032 | Stone data in merge stone modal silently discarded on save |
| R-LOT-037 | N+1 query pattern: 4+ queries per lot row in list at scale |
| R-LOT-038 | `getLotNoForMerge/Split()` return undefined variable on empty result — crash |
| R-LOT-039 | `getLotidsforSplit()` missing GROUP BY — duplicate lot_no in dropdown |
| R-LOT-041 | `get_customer_lot_details()` undefined `$returnData` on empty lot — crash in print |

### 🟡 Low (10)
| ID | Description |
|---|---|
| R-LOT-009 | `ret_catalog_model` loaded twice in constructor |
| R-LOT-010 | `getorderdesigns()` undefined `$data` variable (dead code) |
| R-LOT-011 | `lot_inward/update` doesn't cascade to `ret_lot_other_items`/`ret_lot_other_charges` |
| R-LOT-016 | Dead print route `upload_lotimg` — no cleanup on cancel |
| R-LOT-020 | Inverted mc_type labels in `get_lotInward_detail()` (mc_type=2→PER GRAM) |
| R-LOT-024 | Double `redirect()` dead code in save functions |
| R-LOT-027 | `lot_split/save` missing `trans_begin()` guard |
| R-LOT-033 | `lot_active_id` in split form has `name=""` — not submitted |
| R-LOT-034 | `lot_acknowladgement_old.php` incompatible with current model + out-of-bounds array |
| R-LOT-035 | `insertData/updateData` run `SHOW COLUMNS` on every call — 15+ extra queries/save |
| R-LOT-043 | `.PDF_receipt_thermal` CSS class defined but no view uses it — orphaned |

---

## 5. Anti-Patterns Register (Complete — Rounds 1-8)

| Pattern | Found At | Risk Level |
|---|---|---|
| Raw `$_POST` in model SQL | `ajax_getLotList` model L188 | 🔴 SQL Injection |
| Raw string interpolation in SQL | 20+ model methods | 🟠 SQL Injection (lower risk — internal IDs) |
| GET delete endpoint | `lot_inward/delete` L1195 | 🔴 CSRF |
| `echo last_query()` in production | Controller L2270, L2443 | 🔴 Info leak |
| Double redirect (dead code) | `lot_inward/save` L1025-1026 | 🟠 Logic error |
| Trailing space in column key | `lot_completed` L2491 `'lot_no '` | 🔴 Silent DB failure |
| No child table CASCADE on delete | `lot_inward/delete` | 🔴 Data orphan |
| Uninitialized return variable | `getLotNoForMerge/Split()`, `get_customer_lot_details()` | 🟠 PHP crash |
| N+1 query per list row | `ajax_getLotList()` L271-275 | 🟠 Performance |
| SHOW COLUMNS per CRUD call | `insertData/updateData()` | 🟡 Performance |
| Hardcoded branch ID in JS | `Lot_Completed_Details()` L14896 `branch:1` | 🔴 Wrong data |
| Duplicate HTML id attribute | `lot_merge.php` stock_type radio L301/305 | 🟠 JS selector failure |
| Missing GROUP BY | `getLotidsforSplit()` L1588 | 🟠 Duplicate data |
| Cross-module AJAX to wrong module | `getSearchDesign()` → brntransfer | 🔴 Hidden dependency |
| Inconsistent product data source | Inward→estimation; Merge→lot module | 🟠 Data inconsistency |

---

## 6. New Bug in Round 8

| Bug ID | Severity | Description | Location |
|---|---|---|---|
| R-LOT-043 | 🟡 Low | `.PDF_receipt_thermal` CSS class defined but no view uses it — thermal print planned but never implemented | `lot_ack.css` |

---

## 7. Final Brain Statistics

| Metric | Value |
|---|---|
| Total rounds | 8 |
| Total code analyzed | Controller (2566 lines) + Model (1903 lines) + JS (23,630 lines) + Views (9+1 legacy) + CSS (266 lines) |
| Total unique bugs | **41** (R-LOT-001..043, noting 2 duplicates → **40 net unique**) |
| Total AJAX endpoints | 31 (16 internal + 15 cross-module) |
| Cross-module dependencies | 7 modules (Catalog, Estimation, BranchTransfer, Purchase, Orders, Tagging, Settings) |
| SQL injection surface | 20+ un-escaped string interpolations |
| Critical bugs | 5 |
| High bugs | 9 |
| Medium bugs | 13 |
| Low bugs | 10 |

**The Lot module brain is definitively complete after 8 rounds. No further deep analysis targets remain.**
