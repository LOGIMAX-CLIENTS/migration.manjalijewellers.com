# MODULE BRAIN — Other Inventory
> **Updated:** 2026-03-24 (Round 16 — FLOW_RISK_MATRIX.md built, all 10 standard docs now present)
> **Built:** 2026-03-14 | **Version:** 3.0 | **Round:** 16 | **Total Bugs:** 51

---

## 1. Module Overview

**Purpose:** Manages non-jewellery inventory items (packaging, accessories, gifts, consumables) used in a jewellery retail ERP. Covers full lifecycle: item master → purchase → stock tagging → branch issue → reports.

### File Map

| File | Lines | Bytes | Purpose |
|---|---|---|---|
| `admin/application/controllers/admin_ret_other_inventory.php` | 1515 | 59 KB | Main controller — 10 sub-modules via switch() routing |
| `admin/application/models/ret_other_inventory_model.php` | 870 | 38 KB | All DB operations |
| `admin/assets/js/ret_other_inventory.js` | 3393 | 157 KB | All client-side logic, AJAX, DataTables |
| `admin/application/views/other_inventory/` | **19 files** | — | Views organized by sub-module (all 19 audited Rounds 9–14, including `report/reorder.php`) |

### Connection Flow
```
Browser → JS (ret_other_inventory.js)
       → AJAX → Controller (admin_ret_other_inventory)
       → Model (ret_other_inventory_model)
       → DB (10+ tables)
       → View (other_inventory/*)
       → Browser
```

### Sub-Modules (Switch-Level Routing)
| Sub-Module | Controller Method | Route Prefix |
|---|---|---|
| Item Master | `other_inventory($type)` | `/other_inventory/` |
| Category | `inventory_category($type)` | `/inventory_category/` |
| Purchase Entry | `purchase_entry($type)` | `/purchase_entry/` |
| Product Details (Tagging) | `product_details($type)` | `/product_details/` |
| Issue Items | `issue_item($type)` | `/issue_item/` |
| Stock Report | `stock_details($type)` | `/stock_details/` |
| Available Stock | `available_stock($type)` | `/available_stock/` |
| Reorder Report | `reorder_report($type)` | `/reorder_report/` |
| Product Mapping | `product_mapping($type)` | `/product_mapping/` |
| Size Master | `item_size($type)` | `/item_size/` |

---

## 1b. Bug Registry Summary (Round 14 — TRUE FINAL)

> Full details in `BUG_CANDIDATES.md` (51 bugs, BRN-OI-001 through BRN-OI-051). Sprint plan in `SPRINT_PLAN.md`.

| Severity | Count | Bug IDs |
|---|---|---|
| 🔴 CRITICAL | 3 | BRN-OI-002, BRN-OI-008, BRN-OI-036 |
| 🔴 HIGH | 17 | BRN-OI-001,003,004,005,016,031,032,033,034,035,038,039,042,043,047,050 + BRN-OI-016 |
| 🟠 MEDIUM | 15 | BRN-OI-006,007,009,010,011,012,013,015,017,019,020,040,041,044,045 |
| 🟡 LOW | 16 | BRN-OI-014,018,021,022,023,024,025,026,027,028,029,030,037,046,048,049,051 |

**Sprint plan:** `SPRINT_PLAN.md` — 5 sprints (S1 Critical=11, S2 Data=9, S3 Security+XSS=16, S4 UX=6, S5 Cleanup=8)

**Notable cross-module bugs:** BRN-OI-047 (`admin_ret_billing.php` — billing commits before OI inserts), BRN-OI-050 (`ret_billing_model.php` — SQL injection)

**Fix sequence:** S1 → S3 → S2 → S4 → S5

**Most urgent cluster:** BRN-OI-036 + BRN-OI-042 (null cascade in `getlastrefno→generaterefCode` — PHP8 fatal on fresh install, 2 one-line fixes)

---

## 2. Constructor

```php
class Admin_ret_other_inventory extends CI_Controller
```

| Model/Library | Variable | Purpose |
|---|---|---|
| `ret_other_inventory_model` | `$this->ret_other_inventory_model` | Primary model — all OI operations |
| `admin_settings_model` | `$this->admin_settings_model` | Company details, branch day closing, access control |
| `log_model` | `$this->log_model` | Activity logging |
| `phpqrcode/qrlib` | loaded on-demand | QR code generation |
| `dompdf/autoload.inc.php` | required at top | PDF generation for purchase entry print |

### Session Gate (L20-31)
- Redirects to `admin/login` if `is_logged` session is not set
- Enforces access time window using `access_time_from` / `access_time_to` session keys
- On violation: sets flash error + redirects to `chit_admin/logout`

---

## 3. Entry Points (Route Table)

See full table in **[METHOD_INDEX.md](METHOD_INDEX.md)**.

### Page-Load Routes
| URL | Method | View |
|---|---|---|
| `/other_inventory/add` | GET | `other_inventory/form` |
| `/other_inventory/list` | GET | `other_inventory/list` |
| `/other_inventory/edit/{id}` | GET | `other_inventory/form` |
| `/inventory_category/add` | GET | `other_inventory/category/form` |
| `/inventory_category/list` | GET | `other_inventory/category/list` |
| `/inventory_category/edit/{id}` | GET | `other_inventory/category/form` |
| `/purchase_entry/add` | GET | `other_inventory/purchase/form` |
| `/purchase_entry/list` | GET | `other_inventory/purchase/list` |
| `/purchase_entry/purchase_details/{id}` | GET | `other_inventory/purchase/purchase_entry` |
| `/product_details/add` | GET | `other_inventory/product/form` |
| `/product_details/list` | GET | `other_inventory/product/list` |
| `/issue_item/list` | GET | `other_inventory/issue/list` |
| `/issue_item/add` | GET | `other_inventory/issue/form` |
| `/stock_details/list` | GET | `other_inventory/report/stock_report` |
| `/available_stock/list` | GET | `other_inventory/report/available_stock` |
| `/reorder_report/list` | GET | `other_inventory/report/reorder` |
| `/product_mapping/list` | GET | `other_inventory/product_mapping` |
| `/item_size/list` | GET | `other_inventory/size_list` |

### POST/AJAX Routes
| URL | Method | Purpose |
|---|---|---|
| `/other_inventory/save` | POST | Create item master |
| `/other_inventory/update/{id}` | POST | Update item master |
| `/other_inventory/delete/{id}` | GET | **CSRF Risk** — Hard delete item |
| `/other_inventory/active_skuid` | POST | SKU ID autocomplete search |
| `/other_inventory/ajax` | POST | List all items (AJAX DataTable) |
| `/other_inventory/print_qrcode/{id}` | GET | Generate QR PDF for item |
| `/inventory_category/save` | POST | Create category |
| `/inventory_category/update/{id}` | POST | Update category |
| `/inventory_category/delete/{id}` | GET | Hard delete category |
| `/inventory_category/active_itemname` | POST | Active category names |
| `/inventory_category/ajax` | POST | Category list (AJAX) |
| `/purchase_entry/save` | POST | Create purchase entry (+ images) |
| `/purchase_entry/cancel_purchase_entry` | POST | Cancel purchase (status=2) |
| `/purchase_entry/ajax` | POST | Purchase list (AJAX) |
| `/product_details/save` | POST | Tag individual pieces to purchase |
| `/product_details/ajax` | POST | Product detail list |
| `/issue_item/save` | POST | Issue items to customer/branch |
| `/issue_item/ajax` | POST | Issue history list |
| `/stock_details/ajax` | POST | Stock movement report |
| `/available_stock/ajax` | POST | Available stock by branch |
| `/reorder_report/ajax` | POST | Reorder level report |
| `/product_mapping/ajax` | POST | Product mapping list |
| `/item_size/save` | POST | Create size |
| `/item_size/update` | POST | Update size |
| `/item_size/delete/{id}` | GET | Delete size |
| `/check_sku_id` | POST | Check SKU ID uniqueness |
| `/get_inventory_category` | GET | All categories dropdown |
| `/get_other_inventory_item` | GET | All items dropdown |
| `/get_supplier` | POST | Supplier dropdown |
| `/get_bill_details` | POST | Active bills by branch |
| `/get_invnetory_item` | POST | Items with available stock |
| `/get_other_inventory_ref_no` | POST | Purchase ref numbers |
| `/get_ActiveCategory` | GET | Active categories |
| `/get_all_sizes` | GET | All size names |
| `/CheckIsNameDuplicate` | POST | Check category name uniqueness |
| `/update_product_mapping` | POST | Map product to OI item |
| `/delete_product_mapping` | POST | Delete product mapping |
| `/get_productMappedDetails` | POST | Mapped product-item details |
| `/get_other_inventory_details` | POST | OI details by purchase ref |
| `/other_inventory_print/{ref_no}` | GET | ZPL tag print (.prn download) |
| `/product_other_inventory_print/{id}` | GET | ZPL tag print by product ref |
| `/get_img_by_item_id` | POST | Purchase images by item |
| `/otheritem_status/{status}/{id}` | GET | Toggle category active/inactive |
| `/packaging_item_size_status/{status}/{id}` | GET | Toggle size active/inactive |
| `/get_other_inventory_product` | POST | Product detect list |
| `/get_pro_detail_list` | GET | Pro detail list page |
| `/product_tag_detail` | POST | Tag details by item |
| `/get_other_inventory_item` | GET | Items for dropdown |

---

## 4. Key Tables

| Table | PK | Purpose |
|---|---|---|
| `ret_other_inventory_item` | `id_other_item` | Item master (name, SKU, size, type, price, QR) |
| `ret_other_inventory_item_type` | `id_other_item_type` | Category (billable flag, expiry flag, reorder level) |
| `ret_other_inventory_size` | `id_inv_size` | Packaging size master |
| `ret_other_inventory_reorder_settings` | `id_inv_reorder_settings` | Min/max stock per branch |
| `ret_other_inventory_purchase` | `otr_inven_pur_id` | Purchase entry header |
| `ret_other_inventory_purchase_items` | `inv_pur_itm_id` | Purchase line items (qty, rate, GST) |
| `ret_other_inventory_purchase_items_details` | `pur_item_detail_id` | Individual piece records (tagging) — tracks status & branch |
| `ret_other_inventory_purchase_items_log` | `id_item_log` | Movement log (status 0=inward, 1=issue, 3/4=transfer) |
| `ret_other_inventory_purchase_images` | — | Images attached to purchase |
| `ret_other_invnetory_issue` | `id_inventory_issue` | Issue records (to customer/branch) |
| `ret_other_inventory_product_link` | `inv_des_id` | Maps OI item → retail product |
| `gift_mapping` | — | Maps OI item → scheme (for gift with purchase) |

**Referenced Tables:**
- `branch` — Branch master
- `ret_karigar` — Supplier master (karigar_for=4)
- `customer` — Customer master
- `ret_billing` — Billing records
- `ret_product_master` — Retail products
- `ret_uom` — Units of measure
- `ret_day_closing` — Day closing data
- `ret_branch_transfer_other_inventory` — Branch transfer of OI items
- `employee` — Employee/user master
- `state`, `city`, `country` — Address masters

---

## 5. Business Rules Summary

See **[BUSINESS_RULES.md](BUSINESS_RULES.md)** for full list (11 rules documented).

**Top Rules:**
- RULE-OI-001: Stock = Opening Balance + Inward – Outward (computed from log)
- RULE-OI-002: Issue preference — FIFO (issue_preference=1) or FILO (=2)
- RULE-OI-004: Piece ref_no format = `{item_id}-{alpha_prefix}{5-digit-sequence}`
- RULE-OI-007: Purchase cancel = status→2 (soft cancel, no stock reversal)
- RULE-OI-008: GST stored in 3 separate columns (rate, gst amount, total)

---

## 6. Data Flow Summary

See **[DATA_FLOW.md](DATA_FLOW.md)** for full traces.

**Key Flows:**
1. **Item Master CRUD** — form.php → POST save/update → QR code generation
2. **Purchase Entry** → Line items → Image upload → Purchase ref generation
3. **Product Tagging** → Individual piece creation with unique `item_ref_no`
4. **Issue** — FIFO/FILO from `ret_other_inventory_purchase_items_details`, logs movement
5. **Stock Report** — Aggregates from `ret_other_inventory_purchase_items_log`

---

## 7. Form Sections (Item Master `form.php`)

**Hidden Fields:**
- `#id_size` — Selected size ID
- `#id_uom` — Selected UOM ID
- `#item_for` — Item category FK
- `#issue_to` — Issue target type
- `#table_length` — Scheme rows count
- `sku_id` (auto-set from id after save)

**Key DOM IDs:**
- `#other_item_img` — Item image upload
- `#other_item_img_preview` — Image preview
- `#scheme_map_table` — Gift scheme mapping table
- `#select_uom`, `#select_size`, `#itemfor` — Select2 dropdowns

---

## 8. Cross-Module Dependencies

See **[CROSS_MODULE_MAP.md](CROSS_MODULE_MAP.md)**.

External dependencies:
- `admin_settings_model` — company details, branch day closing, access control
- `log_model` — activity logging
- `ret_billing` table — for issue-to-bill linkage
- `ret_karigar` table — supplier data
- `ret_product_master` table — product mapping
- `admin_ret_catalog/uom/active_uom` — AJAX call to catalog controller

---

## 9. Known Risks / Bug Candidates

See **[BUG_CANDIDATES.md](BUG_CANDIDATES.md)** for the full register (24 candidates, with detailed analysis and fix hints).

**Critical Priority (fix immediately):**

| ID | Risk | Location | Severity |
|---|---|---|---|
| BRN-OI-002 | `updateBatchData()` has `print_r + exit` — DEAD CODE that will crash server | Model L32-33 | 🔴 CRITICAL |
| BRN-OI-008 | `issue_item/save` failure branch returns `status=TRUE` — misleads caller | Controller L987 | 🔴 HIGH |
| BRN-OI-006 | `cancel_purchase_entry` echoes raw SQL on error, exit before rollback | Controller L684–686 | 🔴 HIGH |
| BRN-OI-007 | `product_details/save` has no `trans_begin()` but calls `trans_commit()` | Controller L738–772 | 🔴 HIGH |
| BRN-OI-017 | `other_inventory_print()` — `$tagprintCode` not reset between label groups of 3 | Controller L1307–1329 | 🟠 MEDIUM |
| BRN-OI-009 | `delete_product_mapping` calls `trans_begin()` inside loop | Controller L1159–1162 | 🔴 HIGH |
| BRN-OI-010 | `update_product_mapping` calls `trans_begin()` inside loop | Controller L1188–1205 | 🔴 HIGH |

**Security (SQL injection):**

| ID | Risk | Location | Severity |
|---|---|---|---|
| BRN-OI-001 | SQL injection in all raw query methods | Model — all methods | 🔴 CRITICAL |
| BRN-OI-003 | `getActiveskuid()` — `$searchField` unsanitized in SQL | Model L207 | 🔴 CRITICAL |
| BRN-OI-004 | `CheckIsNameDuplicate()` — `$name` directly interpolated | Model L852-855 | 🔴 CRITICAL |
| BRN-OI-005 | DELETE via GET request — CSRF vulnerable | Controller L253, L469, L1079 | 🔴 HIGH |

**Data Integrity:**

| ID | Risk | Location | Severity |
|---|---|---|---|
| BRN-OI-011 | `set_image_other()` uses `$item` (update return value) instead of `$id` | Controller L345 | 🟠 MEDIUM |
| BRN-OI-012 | `update` case does NOT update `stock_id_uom` or `issue_to` | Controller L269-277 | 🟠 MEDIUM |
| BRN-OI-013 | `generateItemRefNo()` uses MAX() — race condition possible | Model L129-143 | 🟠 MEDIUM |
| BRN-OI-015 | `get_AvailableStockDetails()` uses wrong filter key `id_inv_size` vs `id_size` | Model L523 | 🟠 MEDIUM |
| BRN-OI-016 | Gift mapping inserted before `trans_begin()` in issue/save | Controller L927–948 | 🟠 MEDIUM |

---

## 10. DB Verification Queries

```sql
-- Full purchase entry by ID
SELECT p.*, k.firstname as supplier, 
       GROUP_CONCAT(i.name, '×', pi.inv_pur_itm_qty) as items,
       SUM(pi.inv_pur_itm_total) as total_amount
FROM ret_other_inventory_purchase p
LEFT JOIN ret_karigar k ON k.id_karigar = p.otr_inven_pur_supplier
LEFT JOIN ret_other_inventory_purchase_items pi ON pi.otr_inven_pur_id = p.otr_inven_pur_id
LEFT JOIN ret_other_inventory_item i ON i.id_other_item = pi.inv_pur_itm_itemid
WHERE p.otr_inven_pur_id = {ID};

-- Check tagged vs purchased pieces
SELECT pi.inv_pur_itm_qty as purchased, COUNT(pd.pur_item_detail_id) as tagged, 
       (pi.inv_pur_itm_qty - COUNT(pd.pur_item_detail_id)) as balance
FROM ret_other_inventory_purchase_items pi
LEFT JOIN ret_other_inventory_purchase_items_details pd ON pd.inv_pur_itm_id = pi.inv_pur_itm_id
WHERE pi.otr_inven_pur_id = {ID} GROUP BY pi.inv_pur_itm_id;

-- Orphan piece records (no parent item)
SELECT pd.pur_item_detail_id, pd.inv_pur_itm_id
FROM ret_other_inventory_purchase_items_details pd
LEFT JOIN ret_other_inventory_purchase_items pi ON pi.inv_pur_itm_id = pd.inv_pur_itm_id
WHERE pi.inv_pur_itm_id IS NULL;

-- Stock integrity check per item per branch
SELECT l.item_id, i.name,
       SUM(IF(l.status=0, l.no_of_pieces, -l.no_of_pieces)) as log_balance,
       SUM(IF(d.status=0, d.piece, 0)) as detail_balance
FROM ret_other_inventory_purchase_items_log l
LEFT JOIN ret_other_inventory_item i ON i.id_other_item = l.item_id
LEFT JOIN ret_other_inventory_purchase_items_details d ON d.other_invnetory_item_id = l.item_id
WHERE l.to_branch = {BRANCH_ID}
GROUP BY l.item_id;
```

---

## 11. Codebase Notes

- Controller uses `const` for model names + `switch/case` routing pattern (all CRUD in one method)
- JS uses a global `ctrl_page` array to determine which initializer to call on page load
- QR printing uses ZPL (Zebra Printer Language) — generates `.prn` file for download
- PDF printing uses Dompdf library
- Image upload converts base64 → temp file → JPEG resample
- `item_ref_no` format: `{item_id}-{optional_alpha}{5-digit-seq}` (e.g., `1-A00001`)
- `ret_other_invnetory_issue` has a **typo** in table name (`invnetory` not `inventory`) — used consistently

---

## 12. Anti-Patterns Register

| Date | Bug ID | Anti-Pattern | Pattern |
|---|---|---|---|
| 2026-03-14 | BRN-OI-002 | Debug code left in production | `print_r + exit` inside model method |
| 2026-03-14 | BRN-OI-007 | Missing transaction begin | `trans_commit()` called without matching `trans_begin()` |
| 2026-03-14 | BRN-OI-009/010 | Transaction inside loop | `trans_begin()` called per iteration — only last commit is atomic |
| 2026-03-14 | BRN-OI-008 | Wrong boolean in error path | Error branch returns `status=TRUE` |
| 2026-03-14 | BRN-OI-016 | Side-effect before transaction | DB writes before `trans_begin()` — not rolled back on failure |
| 2026-03-14 | BRN-OI-017 | Accumulator not reset | `$tagprintCode` not cleared between ZPL label groups |
