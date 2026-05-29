# Ret_Reports Module Brain

> **Module**: Ret_Reports (Retail Reports)
> **Brain Version**: 1.7
> **Built**: 2026-04-29 — Round 9 (Added Section 10: Dynamic Column Manager)
> **Controller**: `admin/application/controllers/admin_ret_reports.php` (10,030 lines)
> **Model**: `admin/application/models/ret_reports_model.php` (37,491 lines)
> **JS**: `admin/assets/js/ret_reports.js` (~2.73 MB)
> **Views**: `admin/application/views/ret_reports/` (186 files across 6 subdirs)

---

## 1. Module Overview

**Purpose**: Ret_Reports is the **central reporting hub** for the entire retail ERP system. It does NOT create, update, or delete transactional records (except for `update_green_tag`). Instead, it **reads** data from virtually every other module and presents it in tabular/chart/export format.

**Architecture Pattern**: Nearly all 236 controller methods follow a uniform **list/ajax switch pattern** (unchanged in Round 7):
```
function report_name($type = "") {
    switch($type) {
        case 'list':  → Load view (HTML page)
        case 'ajax':  → Call model method + get_access → JSON response
    }
}
```

The only write operation in the entire controller is `update_green_tag()` (L183), which updates `ret_taging.tag_mark`.

### File Map

| File | Lines | Size | Purpose |
|---|---|---|---|
| `admin_ret_reports.php` (Controller) | 10,030 | ~196 KB | Route dispatch, access control, data aggregation |
| `ret_reports_model.php` (Model) | 37,491 | ~2.89 MB | All SQL queries (SELECT-heavy, ~182 unique tables read) |
| `ret_reports.js` (JS) | ~50K+ | 2.73 MB | DataTable configs, AJAX calls, export functions |
| Views (186 files) | — | — | Report HTML templates (filters + DataTable containers) |

### Connection Flow
```
Browser → JS (ret_reports.js)
       → AJAX → Controller (admin_ret_reports.php)
       → Model (ret_reports_model.php)
       → DB (reads ~182 tables, writes only ret_taging)
       → JSON response → JS → DataTable render
       
Browser → Controller (list case)
       → View (ret_reports/*.php)
       → Browser (HTML page with filter form)
```

---

## 2. Constructor (L15-55)

### Loaded Models

| Model | Purpose |
|---|---|
| `ret_reports_model` (self) | Primary — all report queries |
| `admin_settings_model` | Access control (`get_access()`), settings lookups |
| `ret_catalog_model` | Catalog data, deposit queries |
| `log_model` | Event logging (used by `update_green_tag`) |
| `ret_purchase_order_model` | Head office vault, retagging details |

### Session Gate (L35-54)
- Redirects to `admin/login` if not logged in
- Checks `access_time_from` / `access_time_to` session vars
- Redirects to `chit_admin/logout` if outside allowed time window

### No Libraries or Helpers loaded in constructor
(Libraries loaded per-method: `dompdf` in `generate_cash_abstract`, `Excel` in `export_csv`)

---

## 3. Entry Points — Report Categories

> **236 controller methods** organized into functional categories.
> For full method-by-method detail, see [METHOD_INDEX.md](file:///d:/XAMPP/htdocs/retail_v5/knowledge_brain/Ret_Reports/METHOD_INDEX.md).

### Category Summary

| Category | Methods | Description |
|---|---|---|
| Sales Reports | ~30 | Item-wise, monthly, comparisons, GST, discounts |
| Stock & Inventory | ~35 | Stock details, age analysis, rotation, section-wise, reorder |
| Purchase & PO | ~15 | Purchase bills, GRN, returns, rate fixing, PO payments |
| Customer Reports | ~15 | History, analysis, credit, advance, ledger statements |
| Financial/Accounts | ~25 | Cash abstract, day transactions, deposit, cash book, receipts |
| Supplier/Karigar | ~15 | Supplier transactions, approval, smiths, metal issue |
| LOT Reports | ~10 | Lot-wise, history, details, merge, split, tag-vault |
| Dashboard Widgets | ~25 | Estimation, sales, greentag, oldmetal, credit, gift, orders, LOT |
| Tag & Scan Reports | ~15 | Tag history, tagged items, stone details, scan reports |
| Branch Transfer | ~5 | Branch transfer, section transfer, vault reports |
| GST/Tax Reports | ~10 | GSTR1, GSTR2, GST abstract, abstract with returns |
| HR/Employee | ~5 | Incentive reports, employee-wise tags |
| Utility/Data Import | ~8 | File uploads, old tag imports, CSV/PDF exports |
| Miscellaneous | ~10 | Telecalling, feedback, retagging, repair orders |

### URL Pattern
All routes follow: `/admin_ret_reports/{method_name}/{type}`
Where `{type}` is typically: `list`, `ajax`, `tag_list`, `dynamic`, `tagging`, etc.

---

## 4. Model Methods Summary

**392 model methods** in 21 groups — Almost all are SELECT queries. See [METHOD_INDEX.md](file:///d:/XAMPP/htdocs/retail_v5/knowledge_brain/Ret_Reports/METHOD_INDEX.md) for the full alphabetical index.

> **Round 7 change**: +1 method `get_partial_sale_details()` at L535 (reads `ret_partlysold`).

### Method Groups by Pattern

| Pattern | Count | Example |
|---|---|---|
| `get_*_details()` / `get*Details()` | ~120 | `get_stock_details_v1()`, `get_cus_details()` |
| `get*Report()` / `get*report()` | ~30 | `getBranchTransReport()`, `getSalesTransReport()` |
| `get_Active*()` / `getActive*()` | ~10 | `get_ActiveDesign()`, `get_ActiveBankname()` |
| Data aggregation | ~20 | `customer_sales_analysis()`, `get_inventory_turnover_details()` |
| View-based queries | ~10 | Queries against `ret_view_*` named views |
| Utility / CRUD | ~5 | `insertData()`, `updateData()`, `getData()` |
| Duplicate/versioned | ~15+ | `_15_02_2024`, `_28_04_2023`, `_v1`, `_v2` suffixed methods |

> ⚠️ **NOTE**: Several model methods exist in **date-suffixed versions** without cleanup. Additionally, 2 methods have **commented-out legacy versions** (wrapped in `/* */`) preceding the active definitions — these are NOT true PHP duplicates but dead code:
> - `day_transactions_report()` — legacy at L17520 (commented out), active at L18358
> - `chit_utilize_details()` — legacy at L2321 (commented out), active at L2367

---

## 5. Key Tables

### Owned Tables (written by this module)
Only **1 table** is written by this module:

| Table | Write Type | Method | Purpose |
|---|---|---|---|
| `ret_taging` | UPDATE | `update_green_tag()` L183 | Sets `tag_mark`, `green_tag_date`, `green_tag_marked_by` |

### Most-Referenced Read Tables (~182 total)

| Table | Read Frequency | Purpose |
|---|---|---|
| `ret_taging` | Very High | Tag master — most stock reports query this |
| `ret_billing` | Very High | Sales billing header |
| `ret_bill_details` | Very High | Sales billing line items |
| `ret_billing_payment` | High | Payment details per bill |
| `ret_branch_transfer` | High | Branch transfer header |
| `ret_brch_transfer_tag_items` | High | Branch transfer tag items |
| `ret_purchase_order` | High | PO header |
| `ret_purchase_order_items` | High | PO line items |
| `ret_grn_entry` / `ret_grn_items` | High | GRN header and items |
| `ret_lot_inwards` / `_detail` | High | LOT inward transactions |
| `ret_estimation` / `_items` | Medium | Estimation records |
| `ret_issue_receipt` / `_payment` | Medium | Issue receipts |
| `ret_karigar_metal_issue` | Medium | Karigar metal issue |
| `customer` | Medium | Customer master |
| `branch` | Medium | Branch master |
| `ret_category` | Medium | Product categories |
| `ret_product_master` | Medium | Product master |
| `ret_design_master` | Medium | Design master |
| `ret_section` | Medium | Section/counter master |
| `ret_nontag_item` | Medium | Non-tag inventory |
| `ret_settings` | Medium | Module settings |

For complete table→method reverse map, see [METHOD_INDEX.md § 7d](file:///d:/XAMPP/htdocs/retail_v5/knowledge_brain/Ret_Reports/METHOD_INDEX.md).

---

## 6. Known Risks

### R1: SQL Injection — Raw Variable Interpolation (P0) ✅ Verified R3
Model builds SQL by string concatenation with unsanitized inputs:
- L537: `$tag_id` injected directly: `"... s.tag_id=".$tag_id`
- L521/530: `$lot_id`, `$pro_id` in `getStartTagNo()`/`getEndTagNo()` — `"WHERE t.tag_lot_id = ".$lot_id`
- L620: `$tag_id` in `get_partly_sale_tag_details()` — `"AND d.tag_id = ".$tag_id`
- L1113: `"... id_branch=".$id_branch` — no parameterization
- L1137: `$id_branch` interpolated in subquery: `"est.id_branch = $id_branch"`
- L1173: `"... id_profile=".$id_profile` — profile table query
- L1180: `$bill_id` in `getOldMetalPurchaseAmount()` — `"WHERE s.bill_id=".$bill_id`
- Across **~182 tables** referenced, essentially all queries use direct interpolation

### R2: Green Tag Loop Bug (P1) ✅ Verified R3
`update_green_tag()` L225: `$tag['req_status']` used in log message **outside** the foreach loop, referencing only the **last iteration's** `$tag` variable. If mixed mark/unmark operations are submitted, the log message will always reflect the last tag's status.

### R3: DOMPDF Typo (P3) ✅ Verified R3
`generate_cash_abstract()` L440: `$dompdf->set_paper("a4", "portriat")` — should be `"portrait"`. DOMPDF may silently fallback to default orientation.

### R4: Commented-Out Legacy Methods (P2) ✅ Verified R3
Two methods have **commented-out** older versions immediately preceding active versions:
- `day_transactions_report()` — legacy L17520 (commented out), active L18358
- `chit_utilize_details()` — legacy L2321 (commented out), active L2367
These are NOT duplicate PHP definitions, but dead code blocks (~1,200 lines total).

### R5: Date Suffix Dead Code (P2)
12 date-suffixed method copies exist without cleanup (~3,000+ lines of dead code):
- `get_categorywise_bt_report()` — 4 extra versions
- `get_section_stock_inout_details()` — 4 extra versions
- `credit_pending()`, `getNetbankingCollectionReport()`, etc. — 2 versions each

### R6: `ho_daily_stock_book` Complexity (P2)
Controller method (L8686-9087) — ~400 lines of aggregation logic that should be in model.

### R7: Missing Transaction Wrapping
`update_green_tag()` uses `trans_begin/trans_commit` correctly, but individual failures within loop are not tracked.

### R8: Hardcoded `$_POST` Keys (P2)
Many controller methods directly access `$_POST` keys without existence checks.

---

## 7. DB Verification Queries

### 7a. Pull Complete Bill + Items
```sql
SELECT b.*, bd.*
FROM ret_billing b
LEFT JOIN ret_bill_details bd ON bd.bill_id = b.bill_id
WHERE b.bill_id = '{BILL_ID}';
```

### 7b. Verify Billing Total
```sql
SELECT
    b.bill_id,
    b.net_amount AS stored_total,
    SUM(bd.item_total) AS calculated_total,
    b.net_amount - SUM(bd.item_total) AS delta
FROM ret_billing b
JOIN ret_bill_details bd ON bd.bill_id = b.bill_id
GROUP BY b.bill_id
HAVING ABS(b.net_amount - SUM(bd.item_total)) > 0.01;
```

### 7c. Orphan Bill Details
```sql
SELECT bd.*
FROM ret_bill_details bd
LEFT JOIN ret_billing b ON b.bill_id = bd.bill_id
WHERE b.bill_id IS NULL;
```

### 7d. Orphan LOT Inward Details
```sql
SELECT lid.*
FROM ret_lot_inwards_detail lid
LEFT JOIN ret_lot_inwards li ON li.id_inward = lid.id_inward
WHERE li.id_inward IS NULL;
```

---

## 8. Codebase Notes

- **Coding pattern**: Controller follows a uniform switch-case pattern. Most methods are 30-50 lines.
- **Exception**: `ho_daily_stock_book`, `deposit_report`, `daytransactions`, `stock_details_v1` — these contain significant business logic in the controller (100-400 lines each).
- **JS convention**: DataTable-based rendering with AJAX data sources. Export buttons for Excel/PDF.
- **Access control**: Every AJAX handler calls `$this->admin_settings_model->get_access()` to check user permissions.
- **PDF generation**: `generate_cash_abstract()` uses DOMPDF library.
- **Excel export**: `export_csv()` uses PHPExcel library (old, deprecated).

---

## 9. Anti-Patterns Register

| # | Pattern | Where Found | Impact | Fix Status |
|---|---|---|---|---|
| AP-1 | Raw SQL interpolation | All model methods | SQL injection risk | Open |
| AP-2 | Loop var used outside loop | `update_green_tag()` L225 | Wrong log message | Open |
| AP-3 | Typo in string literal | `generate_cash_abstract()` L440 | PDF orientation wrong | Open |
| AP-4 | Commented-out code blocks | Model L2321-2367, L17520-18358 | ~1,200 lines dead code | Open |
| AP-5 | Date-suffixed method copies | 12 methods across model | ~3,000+ lines dead code | Open |
| AP-6 | Business logic in controller | `ho_daily_stock_book` L8686 | ~400 lines, hard to test | Open |
| AP-7 | Schema doc incomplete | SCHEMA_ANALYSIS was missing 12 tables | Brain gaps mask dependencies | Fixed R4 |
| AP-8 | Unary-plus in JS string concat (`+ +'<td>'`) | `set_chit_closing_table()` L7256 | NaN in HTML, missing table cells → DataTables crash | ✅ Fixed RPT-CLT04 |
| AP-8 | Missing dependent dropdown reload on parent change | `cash_abstract` case L468–508 | Employee dropdown empty for HO users after branch change | Fixed (RPT-CLT01) |

### RPT-CLT01: Employee Filter Not Working in Cash Abstract Report ✅ FIXED

| Bug ID     | Anti-Pattern                                           | Fix Applied                                                  | Date       |
| ---------- | ------------------------------------------------------ | ------------------------------------------------------------ | ---------- |
| RPT-CLT01  | Missing `#branch_select` change handler — dependent dropdown not reloaded | Added change handler to reload employees/floors/counters on branch change | 2026-03-26 |

**Prevention**: When adding a new report case with dependent dropdowns (employee, floor, counter), always add a `#parent_select` change handler alongside the initial load call. Follow the pattern in `old_metal_purchase` (L320–324).

### RPT-STK01: Stock In & Out Product Filter DB Error ✅ FIXED

| Bug ID     | Anti-Pattern                                           | Fix Applied                                                  | Date       |
| ---------- | ------------------------------------------------------ | ------------------------------------------------------------ | ---------- |
| RPT-STK01  | Multi-select array value used directly with `=` in SQL — PHP converts array to string "Array" causing Error 1054 | Added `implode()` + `IN()` handling for `id_product` across 6 functions (10 locations) | 2026-04-04 |

**Prevention**: Every multi-select dropdown filter (`id_product[]`, `id_metal[]`, `id_branch[]`, `purity[]`) MUST be converted via `implode(' , ', $data['field'])` before use in SQL. Use `IN()` operator, never `=`. Check existing handlers in same function for the correct pattern.

---

## 10. Dynamic Column Manager — Item Sales Detail Report

> **Feature added**: 2026-04-29
> **View**: `admin/application/views/ret_reports/detail_item-wise_sales.php`
> **JS**: `admin/assets/js/ret_reports.js` (~L31760–31980)
> **Storage**: Browser `localStorage` only — **no server/DB involvement**

---

### 10a. Architecture Overview

```
COL_MAP[]           — single source of truth: all 42 columns, default order/visibility
PRESETS{}           — 4 built-in views (default, customer, weight, finance)
localStorage        — 2 keys persist user preferences across sessions
activeCfg[]         — what the TABLE is rendering right now
workingCfg[]        — safe draft in the modal (isolated from table until Apply)
activePresetKey     — which preset pill is highlighted
```

---

### 10b. localStorage Keys

| Key | Type | What it stores | When written |
|---|---|---|---|
| `itemwise_col_cfg` | Array | Active column order + visibility (all 42 cols) | On "Apply & Save" click |
| `itemwise_custom_presets` | Object | Named custom views saved by the user | On "Save View" or delete |

> **Data loss risk**: These keys survive a simple cookie clear but ARE wiped when the user does "Clear browsing data → Cookies and other site data". No server-side persistence — settings are per-browser, per-device.

---

### 10c. JSON Structure Examples

**`itemwise_col_cfg`** — ORDER matters (array index = column position in table):
```json
[
  { "key": "branch",      "visible": true  },
  { "key": "bill_no",     "visible": true  },
  { "key": "karigar_name","visible": false },
  { "key": "total_cost",  "visible": true  }
]
```

**`itemwise_custom_presets`** — named snapshots of workingCfg:
```json
{
  "My Finance View": [
    { "key": "branch",     "visible": true },
    { "key": "total_cost", "visible": true }
  ]
}
```

---

### 10d. Key JS Functions

| Function | Purpose |
|---|---|
| `loadColCfg()` | Reads `itemwise_col_cfg` from localStorage |
| `saveColCfg(cfg)` | Writes `itemwise_col_cfg` to localStorage |
| `loadCustomPresets()` | Reads `itemwise_custom_presets` from localStorage |
| `saveCustomPresets(p)` | Writes `itemwise_custom_presets` to localStorage |
| `buildModalList(cfg)` | Renders drag-and-drop column list inside the drawer |
| `renderPresets(highlightKey)` | Renders all preset pills (built-in + custom), highlights active |
| `applyPreset(presetKey)` | Applies a named preset to `workingCfg` |
| `detectPreset(cfg)` | Auto-detects which preset matches current `activeCfg` on drawer open |
| `renderThead(cfg)` | Builds `<thead>` dynamically from `activeCfg` (page load + apply) |

---

### 10e. Flow Summary

- **Page load**: `loadColCfg()` → stored config or `PRESETS.default` → `renderThead()` → headers visible before search
- **Open drawer**: clone `activeCfg` → `workingCfg`, `detectPreset()` highlights pill, `buildModalList()` renders rows
- **Edit in drawer**: drag/toggle → only `workingCfg` mutated, `activeCfg` unchanged until Apply
- **Apply & Save**: `activeCfg = workingCfg` → `saveColCfg()` → destroy DataTable → `renderThead()` → re-init → AJAX fetch
- **Save View**: snapshot `workingCfg` under a name → `saveCustomPresets()` → new pill appears
- **Delete View**: remove from object → `saveCustomPresets()` → pill removed, table NOT affected

---

### 10f. DataTables Compatibility

- **Version**: DataTables `1.10.7` (legacy)
- `<thead>` column count MUST match `aoColumns` count exactly — `renderThead` ensures sync before every init
- Always call `DataTable().destroy()` before reinit to reset internal state
- Native `ColReorder` header drag disabled — incompatible with 1.10.7; drawer modal is the stable workaround

---

### 10g. Debug Commands (Browser Console)

```javascript
// Inspect active config
JSON.parse(localStorage.getItem('itemwise_col_cfg'))

// Inspect saved custom views
JSON.parse(localStorage.getItem('itemwise_custom_presets'))

// Full reset to factory default
localStorage.removeItem('itemwise_col_cfg')
localStorage.removeItem('itemwise_custom_presets')
```

---

### 10h. Known Risks

| ID | Risk | Impact | Status |
|---|---|---|---|
| CM-R1 | localStorage wiped by "Clear site data" | All prefs + custom views lost silently, revert to Default | Open — no server fallback |
| CM-R2 | New key added to COL_MAP but old stored config lacks it | Column missing until user clears storage | ✅ Fixed 2026-04-29 — `loadColCfg()` auto-merges missing keys, appended hidden |
| CM-R3 | Column deleted from COL_MAP, stale key stays in stored config | Blank cell render if row builder hits the key | ✅ Fixed 2026-04-29 — `loadColCfg()` auto-purges keys not in COL_MAP |
| CM-R4 | DataTable column count mismatch | "Unknown parameter N" error, table breaks | Fixed: `renderThead` always runs before init |
