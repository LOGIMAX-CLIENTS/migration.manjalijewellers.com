# LOT MODULE BRAIN
> **Module**: Lot (Lot Inward, Lot Merge, Lot Split)
> **Brain Built**: 2026-03-17 | **Rounds**: 8 | **Coverage**: 100% ✅
> **Next Action**: /fix-single-bug or /module-bug-audit

---

## 1. Module Overview

**Purpose**: Manages the inward receipt of jewellery lots from goldsmiths/suppliers, along with lot merge and split operations. Creates stock in `ret_lot_inwards` / `ret_lot_inwards_detail` for both Tagged (stock_type=1) and Non-Tagged (stock_type=2) inventory.

### File Map

| File | Lines | Bytes | Purpose |
|---|---|---|---|
| `admin/application/controllers/admin_ret_lot.php` | 2566 | 63,982 | Primary controller — all routes |
| `admin/application/models/ret_lot_model.php` | 1903 | 54,532 | DB queries — lot CRUD + reporting |
| `admin/assets/js/ret_lot.js` | 23,630 | 297,185 | UI logic — form, validation, AJAX |
| `admin/application/views/lot/form.php` | ~53KB | — | Add/Edit lot form (largest view) |
| `admin/application/views/lot/list.php` | ~9KB | — | Lot list with filters |
| `admin/application/views/lot/lot_merge.php` | ~22KB | — | Merge UI |
| `admin/application/views/lot/lot_split.php` | ~15KB | — | Split UI |
| `admin/application/views/lot/print/vendor_ack.php` | ~5KB | — | Vendor acknowledgement PDF |
| `admin/application/views/lot/print/office_ack.php` | ~11KB | — | Office acknowledgement PDF |
| `admin/application/views/lot/print/branch_ack.php` | ~10KB | — | Branch acknowledgement PDF |
| `admin/application/views/lot/print/customer_ack.php` | ~17KB | — | Customer acknowledgement |
| `admin/application/views/lot/print/lot_acknowladgement_old.php` | ~7KB | — | Legacy ack (unused) |
| `admin/assets/css/lot_ack.css` | ~4KB | — | Lot acknowledgement styles |

### Connection Flow
```
Browser → ret_lot.js (23630 lines)
       → $.ajax → admin_ret_lot controller (2566 lines)
              → lot_inward($type, $id)         ← main dispatcher
              → lot_merge($type, $id)          ← merge dispatcher
              → lot_split($type)               ← split dispatcher
              → model methods (ret_lot_model)
              → DB tables (ret_lot_inwards, ret_lot_inwards_detail, ret_lot_inwards_stone_detail, ...)
              → Views (lot/form, lot/list, lot/lot_merge, lot/lot_split)
              → Print PDFs via dompdf
```

---

## 2. Constructor Analysis

**File**: `admin_ret_lot.php` L21–71

| Model / Library | Purpose |
|---|---|
| `ret_lot_model` | Primary — all lot CRUD/query methods |
| `admin_settings_model` (×2) | Company info, day close, access control |
| `ret_catalog_model` | UOM lookup (`getActiveUOM`) |
| `log_model` | Activity audit logging |
| `dompdf` (require_once L5) | PDF generation for acknowledgements |

**Session Gate**: L41–70
- `is_logged` → redirect to `admin/login` if not set
- `access_time_from` / `access_time_to` → restrict by time window; redirects to `chit_admin/logout` if outside window

---

## 3. Entry Points (Route Table)

> Controller: `Admin_ret_lot` | URL base: `/admin_ret_lot/`

### 3a. Page Load Routes (lot_inward)

| URL | Method | Controller Method | Lines | Purpose |
|---|---|---|---|---|
| `/admin_ret_lot/lot_inward/add` | GET | `lot_inward('add')` | L307–325 | Load Add Lot form |
| `/admin_ret_lot/lot_inward/list` | GET | `lot_inward('list')` | L327–335 | Load Lot List page |
| `/admin_ret_lot/lot_inward/save` | POST | `lot_inward('save')` | L337–1047 | Save new lot + items + stones + non-tag |
| `/admin_ret_lot/lot_inward/cancel_lot_entry` | POST | `lot_inward('cancel_lot_entry')` | L1049–1115 | Cancel a lot (set lot_status=2) |
| `/admin_ret_lot/lot_inward/edit/{id}` | GET | `lot_inward('edit', $id)` | L1120–1164 | Load Edit form |
| `/admin_ret_lot/lot_inward/lot_edit/{id}` | GET | `lot_inward('lot_edit', $id)` | L1167–1190 | AJAX — get lot data JSON for edit |
| `/admin_ret_lot/lot_inward/delete/{id}` | GET | `lot_inward('delete', $id)` | L1195–1247 | Delete lot + images |
| `/admin_ret_lot/lot_inward/update/{id}` | POST | `lot_inward('update', $id)` | L1251–1651 | Update lot header + items |
| `/admin_ret_lot/lot_inward` (default POST) | POST | `lot_inward('default')` | L1653–1681 | AJAX list data |

### 3b. AJAX-Only Routes

| URL | Method | Controller Method | Lines | Purpose |
|---|---|---|---|---|
| `/admin_ret_lot/get_lotInward_detail` | POST | `get_lotInward_detail()` | L1687–1701 | Get lot detail by `id` (JSON) |
| `/admin_ret_lot/lot_inwards_detail` | POST | `lot_inwards_detail()` | L1703–1769 | Delete single lot item row |
| `/admin_ret_lot/getOrderNosBySearch` | POST | `getOrderNosBySearch()` | L1879–1888 | Search orders by text |
| `/admin_ret_lot/get_order_details` | POST | `get_order_details()` | L1891–1901 | Get order items by order no |
| `/admin_ret_lot/get_karigar_list` | POST | `get_karigar_list()` | L1907–1916 | Get karigar list for order no |
| `/admin_ret_lot/getProductBySearch` | POST | `getProductBySearch()` | L1921–1933 | Search products by text |
| `/admin_ret_lot/get_ActiveProduct` | POST | `get_ActiveProduct()` | L2290–2300 | Get active products |
| `/admin_ret_lot/upload_lotimg` | POST | *(not found — likely missing)* | — | Upload lot images |
| `/admin_ret_lot/remove_img` | POST | `remove_img()` | L173–257 | Remove image from lot/certificates |
| `/admin_ret_lot/lot_completed` | POST | `lot_completed()` | L2461–2525 | Close/complete lots |

### 3c. Print Routes

| URL | Method | Controller Method | Lines | View |
|---|---|---|---|---|
| `/admin_ret_lot/vendor_acknowladgement/{type}/{lot_id}` | GET | `vendor_acknowladgement()` | L1773–1805 | `lot/print/vendor_ack` |
| `/admin_ret_lot/lot_acknowladgement/{type}/{lot_id}` | GET | `lot_acknowladgement()` | L1807–1841 | `lot/print/office_ack` |
| `/admin_ret_lot/branch_acknowladgement/{type}/{lot_id}/{id_branch}` | GET | `branch_acknowladgement()` | L1843–1877 | `lot/print/branch_ack` |
| `/admin_ret_lot/customer_acknowladgement/{type}/{lot_id}` | GET | `customer_acknowladgement()` | L2528–2562 | `lot/print/customer_ack` |

### 3d. Lot Merge Routes

| URL | Method | Lines | Purpose |
|---|---|---|---|
| `/admin_ret_lot/lot_merge/list` | GET | L1948–1960 | Load Merge UI |
| `/admin_ret_lot/lot_merge/getLotNos` | POST | L1962–1972 | Get lot nos eligible for merge |
| `/admin_ret_lot/lot_merge/getLotidsforMerge` | POST | L1974–1980 | Get lot IDs for dropdown |
| `/admin_ret_lot/lot_merge/save` | POST | L1982–2284 | Save merge transaction |

### 3e. Lot Split Routes

| URL | Method | Lines | Purpose |
|---|---|---|---|
| `/admin_ret_lot/lot_split/list` | GET | L2315–2327 | Load Split UI |
| `/admin_ret_lot/lot_split/lotNosForsplit` | POST | L2329–2335 | Get lots eligible for split |
| `/admin_ret_lot/lot_split/getLotDetails` | POST | L2339–2345 | Get lot details for split |
| `/admin_ret_lot/lot_split/getLotidsforSplit` | POST | L2347–2353 | Get lot IDs for dropdown |
| `/admin_ret_lot/lot_split/save` | POST | L2355–2455 | Save split records |

---

## 4. Key Tables

| Table | Owner | Purpose |
|---|---|---|
| `ret_lot_inwards` | **Lot (WRITE)** | Lot header — dates, branch, goldsmith, category, purity, status |
| `ret_lot_inwards_detail` | **Lot (WRITE)** | Lot line items — product, weights, making charge, cost |
| `ret_lot_inwards_stone_detail` | **Lot (WRITE)** | Stone details per lot item |
| `ret_lot_other_items` | **Lot (WRITE)** | Other metal details per lot item |
| `ret_lot_other_charges` | **Lot (WRITE)** | Charge details per lot item |
| `ret_lot_merge` | **Lot (WRITE)** | Merge relationship table |
| `ret_lot_split_details` | **Lot (WRITE)** | Split records |
| `ret_nontag_item` | **NonTag (WRITE)** | Non-tagged inventory stock |
| `ret_nontag_item_log` | **NonTag (WRITE)** | Non-tagged movement log |
| `ret_section_nontag_item_log` | **NonTag (WRITE)** | Section-wise non-tag log |
| `ret_taging` | **Tagging (READ)** | Tagged items for balance check |
| `ret_taging_stone` | **Tagging (READ)** | Stone details for tagged items |
| `ret_product_master` | Catalog (READ) | Product name, sales mode |
| `ret_design_master` | Catalog (READ) | Design name/code |
| `ret_category` | Catalog (READ) | Category + metal |
| `ret_purity` | Catalog (READ) | Purity values |
| `ret_karigar` | Vendor (READ) | Goldsmith details |
| `ret_settings` | Config (READ) | `lot_recv_branch`, `is_purchase_cost_from_lot`, `is_supplierbill_entry_req` |
| `branch` | Org (READ) | Branch names |
| `customerorder` / `customerorderdetails` | Orders (READ) | Customer orders |
| `ret_purchase_order` | Purchase (READ) | PO reference |
| `ret_grn_entry` | GRN (READ) | GRN reference |
| `ret_old_metal_process` | OldMetal (READ) | Old metal process no |
| `ret_stone` | Catalog (READ) | Stone types |
| `ret_uom` | Catalog (READ) | Unit of measure |
| `ret_charges` | Catalog (READ) | Charge definitions |
| `ret_product_division` | Catalog (READ) | Product divisions |
| `employee` | HR (READ) | Employee names |
| `profile` | Auth (READ) | Profile settings |
| `log_model` table | Audit (WRITE) | Activity log via `log_model->log_detail()` |

---

## 5. Data Flow Summary

**Full details in DATA_FLOW.md**

- **CREATE (lot_inward/save)**: JS validates → form POST → controller builds header data → `trans_begin` → INSERT `ret_lot_inwards` → foreach items: INSERT `ret_lot_inwards_detail` → INSERT `ret_lot_inwards_stone_detail` + `ret_lot_other_items` + `ret_lot_other_charges` → if NonTag: check/upsert `ret_nontag_item` + log → `trans_commit` → redirect to acknowledgement PDF
- **EDIT (lot_inward/update)**: Similar to save but UPDATE header + conditionally UPDATE/INSERT detail rows. Stones: DELETE-then-INSERT pattern
- **DELETE (lot_inward/delete)**: GET (CSRF risk!) → `trans_begin` → DELETE `ret_lot_inwards` (cascades or orphans?) → `rrmdir` image folder → `trans_commit`
- **MERGE**: Creates new `ret_lot_inwards` (lot_from=7) + new detail rows + inserts `ret_lot_merge` linking old detail IDs → redirect to lot_acknowladgement
- **SPLIT**: Updates `is_lot_split=1` on source lot → INSERT `ret_lot_split_details` per split item

---

## 6. Business Rules Summary

**Full details in BUSINESS_RULES.md** — 12 documented rules

Key rules:
- `lot_recv_branch` setting (ret_settings) controls whether only HO can receive lots (=1) vs any branch (=2)
- `stock_type=1` → Tagged stock; `stock_type=2` → Non-Tagged (triggers ret_nontag_item updates)
- `lot_from` values: 1=Manual, 2=Supplier Entry, 3=Import, 4=Tag Process, 5=Old Metal, 6=Retagging, 7=Merge, 8=NonTag Lot
- `is_closed=1` → Lot is closed; edit blocked
- `calc_type`: 1=Weight×Rate, 2=Purchase Touch, 3=Weight×Wastage%
- `mc_type`: 1=Per Gram, 2=Per Pcs
- `rate_calc_type`: 1=Gram, 2=Pcs

---

## 7. Form Sections (form.php)

**Estimated hidden fields**: 32 static + 41/row dynamic (detailed in ROUND2_SUPPLEMENT.md)

Key DOM IDs:
- `#id_category`, `#id_purity` — header category/purity (auto-propagate to detail rows)
- `#lt_item_list` — detail rows tbody
- `#lt_gold_smith` / `#lt_gold_smith_id` — goldsmith selector pair
- `#lt_rcvd_branch_sel` / `#id_branch` — branch selector pair
- `#status` — bootstrapSwitch
- `#lot_images`, `#lot_img_upload` — image upload

---

## 8. Cross-Module Dependencies Summary

**Full details in CROSS_MODULE_MAP.md** — 10+ external dependencies

Key cross-module reads:
- `ret_taging` (Tagging module) — checks tag status for merge/split eligibility
- `customerorder/customerorderdetails` (Orders module) — order reference lookup
- `ret_purchase_order` (Purchase module) — PO reference
- `ret_grn_entry` (GRN module) — GRN reference
- `ret_old_metal_process` (Old Metal module) — process reference
- `admin_settings_model` — day close, company info, access control

---

## 9. Known Risks / Bugs Registry

> **Total bugs**: 41 IDs (40 unique, 1 confirmed duplicate) across 8 rounds
> Full details in ROUND2-ROUND8 supplements.

### 🔴 Critical (5)
| ID | Description |
|---|---|
| R-LOT-001 | `lot_inward/delete` uses GET — CSRF vulnerable |
| R-LOT-003 | `lot_merge/save` echoes `last_query()` in production |
| R-LOT-004 | `lot_split/save` echoes debug info in production |
| R-LOT-023 | `lot_completed()` trailing space in `'lot_no '` — UPDATE always fails silently |
| R-LOT-026 | Print methods lack CSRF protection |

### 🟠 High (9)
| ID | Description |
|---|---|
| R-LOT-006 | `upload_lotimg` endpoint missing — image upload 404 |
| R-LOT-007 | `lot_inward/update` doesn’t delete stones on re-save — duplicates |
| R-LOT-012 | Delete doesn’t cascade to child tables — orphan records |
| R-LOT-013 | `getSearchDesign()` calls wrong module (`admin_ret_brntransfer`) |
| R-LOT-019 | Undocumented dependency on `admin_ret_estimation` for 3 AJAX calls |
| R-LOT-028 | JS `branch:1` hardcoded in lot close — wrong date for other branches |
| R-LOT-036 | Model reads `$_POST['lot_type']` directly — SQL injection |
| R-LOT-042 | `getOrderNos()` raw user text into SQL LIKE — SQL injection |
| R-LOT-002 | CSRF via URL-based delete (overlaps R-LOT-001) |

### 🟡 Medium / Low (26)
See ROUND2-ROUND8 supplements for R-LOT-008 through R-LOT-043.

---

## 10. DB Verification Queries

```sql
-- Q1: Full lot transaction by lot_no
SELECT i.*, d.*, stn.*
FROM ret_lot_inwards i
LEFT JOIN ret_lot_inwards_detail d ON d.lot_no = i.lot_no
LEFT JOIN ret_lot_inwards_stone_detail stn ON stn.id_lot_inward_detail = d.id_lot_inward_detail
WHERE i.lot_no = ?;

-- Q2: Orphan lot details (no header)
SELECT d.* FROM ret_lot_inwards_detail d
LEFT JOIN ret_lot_inwards i ON i.lot_no = d.lot_no
WHERE i.lot_no IS NULL;

-- Q3: Orphan stone details (no detail row)
SELECT stn.* FROM ret_lot_inwards_stone_detail stn
LEFT JOIN ret_lot_inwards_detail d ON d.id_lot_inward_detail = stn.id_lot_inward_detail
WHERE d.id_lot_inward_detail IS NULL;

-- Q4: Lots with lot_completed=1 check tags
SELECT l.lot_no, l.is_closed, COUNT(t.tag_id) as tagged_items
FROM ret_lot_inwards l
LEFT JOIN ret_taging t ON t.tag_lot_id = l.lot_no AND t.tag_status != 2
WHERE l.is_closed = 1
GROUP BY l.lot_no;

-- Q5: Non-tag item mismatch check
SELECT ni.id_nontag_item, ni.product, ni.no_of_piece as nt_qty,
       SUM(ld.no_of_piece) as lot_qty
FROM ret_nontag_item ni
LEFT JOIN ret_lot_inwards_detail ld ON ld.lot_product = ni.product
GROUP BY ni.id_nontag_item
HAVING ABS(nt_qty - lot_qty) > 0;
```

---

## 11. Codebase Notes

- **PHP Pattern**: CodeIgniter MVC; heavy use of `$_POST` direct access (no `$this->input->post()` consistently)
- **Transactions**: `trans_begin/trans_commit/trans_rollback` used correctly for most writes; but `lot_completed` and cancel_lot_entry look structurally sound
- **Image Storage**: Images stored as `#`-concatenated filenames in `lot_images` / `precious_st_certif` / etc. columns — fragile
- **JS Pattern**: Single JS file with `switch(ctrl_page[1])` dispatch — page type determined from URL path segments
- **Form submission**: `#lot_form` standard HTML form POST for save/update; AJAX for lookup calls
- **Image upload**: Custom `$.ajax` with `FormData` to `upload_lotimg` endpoint (L2219)

---

## 12. Anti-Patterns Register

*(Updated after Rounds 1–8)*

| Pattern | Found At | Risk |
|---|---|---|
| Raw `$_POST` in model SQL | `ajax_getLotList` model L188 | 🔴 SQL Injection |
| Raw string interpolation in SQL | 20+ model methods | 🟠 SQL Injection |
| GET delete | `lot_inward('delete')` L1195 | 🔴 CSRF |
| `echo last_query()` in prod | Controller L2270, L2443 | 🔴 Info leak |
| Hardcoded `branch:1` in JS | `Lot_Completed_Details()` L14896 | 🔴 Wrong data written |
| Double redirect dead code | L1025–1026 | 🟠 Logic bug |
| Trailing space in column key | `lot_completed` L2491 `'lot_no '` | 🔴 Silent update failure |
| No child table cascade on delete | `lot_inward/delete` | 🔴 Data orphan |
| SHOW COLUMNS per CRUD call | `insertData/updateData()` | 🟡 Performance |
| N+1 query per list row | `ajax_getLotList()` L271-275 | 🟠 Performance |
| Uninitialized return variable | `getLotNoForMerge/Split()` on empty | 🟠 PHP crash |
| Missing GROUP BY | `getLotidsforSplit()` | 🟠 Duplicate data |
| Duplicate HTML `id` attribute | `lot_merge.php` stock_type radio | 🟠 JS selector failure |
| Cross-module AJAX wrong module | `getSearchDesign()` → brntransfer | 🔴 Hidden dependency |
| Inconsistent product data source | Inward form → Estimation; Merge → Lot | 🟠 Data inconsistency |
