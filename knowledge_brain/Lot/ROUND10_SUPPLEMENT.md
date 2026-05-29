# LOT MODULE — ROUND 10 SUPPLEMENT
> Module: Lot | Round 10 | 2026-03-17
> **Complete Reverse Dependency Map — All Models Scanned**

---

## 1. Complete Model Scan Results

All PHP models in `admin/application/models/` scanned for `ret_lot_inwards` references:

| Model File | References to Lot Tables | Status |
|---|---|---|
| `ret_tag_model.php` | **324+** | ⚠️ Major dependency |
| `ret_lot_model.php` | *(owner)* | Own module |
| `ret_billing_model.php` | **0** | ✅ Independent |
| `ret_dashboard_model.php` | **0** | ✅ Independent |
| `ret_estimation_model.php` | **0** | ✅ Independent |
| `ret_purchase_order_model.php` | **0** | ✅ Independent |
| `ret_order_model.php` | **0** | ✅ Independent |
| `ret_metal_process_model.php` | **0** | ✅ Independent |
| `ret_sales_transfer_model.php` | **0** | ✅ Independent |
| `ret_stock_issue_model.php` | **0** | ✅ Independent |
| `ret_brntransfer_model.php` | **0** | ✅ Independent |
| `ret_reports_model.php` | **0** | ✅ Independent |
| `ret_app_api_model.php` | **0** | ✅ Independent |
| `ret_dashboard_api_model.php` | **0** | *(backup/API — checked last round)* |
| `ret_dashboard_api_model_backup.php` | **0** | *(backup file)* |
| `ret_app_api_model_backup.php` | **0** | *(backup file)* |

### Definitive Finding:
> **Lot tables (`ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_lot_inwards_stone_detail`) are read by EXACTLY ONE external module: the Tagging module (`ret_tag_model.php`).**

All other modules (Billing, Dashboard, Estimation, Purchase, Orders, Metal Process, Sales Transfer, Stock Issue, Branch Transfer, Reports, App API) are **completely independent** of Lot tables.

---

## 2. Tagging Module Dependency Breakdown

The `ret_tag_model.php` references break down into categories:

| Category | What It Does | Risk Level |
|---|---|---|
| Tag creation queries | Joins `ret_lot_inwards` to get lot metadata for new tags | 🟠 Medium — depends on lot being alive |
| Balance calculation | Joins `ret_lot_inwards_detail` to calculate remaining LOT pieces for tagging | 🔴 HIGH — orphan detail rows = inflated balance |
| Eligibility checks | Joins `ret_lot_merge`, `ret_lot_split_details` to determine eligible lots | 🟠 Medium |
| Historical reads | Joins lot tables for tag history/reports | 🟡 Low — older data |
| Stone reconciliation | Joins `ret_lot_inwards_stone_detail` for stone balance during tagging | 🟠 Medium |

### The Deletion Risk Chain (Updated with Full Model Context)

```
User: GET /admin_ret_lot/lot_inward/delete/{id}          ← CSRF risk (R-LOT-001)
  ↓
Controller: deleteData('lot_no', $id, 'ret_lot_inwards') ← Only header deleted (R-LOT-012)
  ↓
ret_lot_inwards_detail rows → ORPHANED                   ← Child rows remain
ret_lot_inwards_stone_detail rows → ORPHANED
ret_lot_other_items rows → ORPHANED
ret_lot_other_charges rows → ORPHANED
  ↓
ret_tag_model reads ret_lot_inwards_detail for balance   ← Phantom lot items!
  ↓
Tag module shows: X pieces available (should be 0)       ← Inflated stock
  ↓
User tags against deleted lot → tag created with phantom parent
  ↓
Reports/prints join ret_lot_inwards → NULL (header gone) ← Broken reports
```

**This makes R-LOT-012 effectively CRITICAL**, not just HIGH.

---

## 3. METHOD_INDEX.md Cross-Module Corrections

The `METHOD_INDEX.md` §7c (JS → Controller AJAX Map) had unverified cross-module targets in Round 1. Verified version:

| JS Function | Was Listed As | Verified URL (Round 5/8) | Correct? |
|---|---|---|---|
| `get_karigar()` | `admin_ret_catalog` or similar | `admin_ret_catalog/karigar/active_list` | ✅ |
| `get_category()` | `admin_ret_catalog` | `admin_ret_catalog/category/active_category` | ✅ |
| `getActiveUOM()` | `admin_ret_catalog` | `admin_ret_catalog/uom/active_uom` | ✅ |
| `get_ActiveMetals()` | `admin_ret_catalog` | **DEAD CODE** (commented out) | ❌ wrong (dead) |
| `get_ActivePurity()` | `admin_ret_catalog` | `admin_ret_catalog/ajax_getPurity` | ✅ |
| `get_ActiveGRNS()` | GRN controller | `admin_ret_purchase/purchase/active_grns` | ❌ wrong module |
| `getSearchCustomers()` | Customer controller | `admin_ret_estimation/getCustomersBySearch` | ❌ wrong module |
| `get_employee()` | Admin controller | `admin_ret_estimation/get_employee` | ❌ wrong module |
| `getSearchDesign()` | `admin_ret_catalog` | `admin_ret_brntransfer/branch_transfer/getDesignByFilter` | ❌ wrong module |

---

## 4. Final Architecture Clarity

After 10 rounds of comprehensive analysis, the complete Lot module picture:

```
┌─────────────────────────────────────────────────────────────────┐
│                    LOT MODULE ARCHITECTURE                       │
│                                                                  │
│  WRITES TO:                                                      │
│  ├── ret_lot_inwards (header)                                    │
│  ├── ret_lot_inwards_detail (items)                              │
│  ├── ret_lot_inwards_stone_detail (stones)                       │
│  ├── ret_lot_other_items (other metals)                          │
│  ├── ret_lot_other_charges (charges)                             │
│  ├── ret_lot_merge (merge links)                                 │
│  ├── ret_lot_split_details (split records)                       │
│  ├── ret_nontag_item (±arithmetic)                               │
│  ├── ret_nontag_item_log (log)                                   │
│  └── ret_section_nontag_item_log (section log)                   │
│                                                                  │
│  READS FROM:                                                     │
│  ├── Catalog: product, design, category, purity, stone, uom...   │
│  ├── Orders: customerorder, customerorderdetails, joborder       │
│  ├── Purchase: ret_purchase_order, ret_grn_entry                 │
│  ├── Auth: profile, employee, branch                             │
│  └── Settings: ret_settings, admin_settings_model                │
│                                                                  │
│  IS READ BY:                                                     │
│  └── Tagging module (ret_tag_model.php): 324+ queries            │
│      ├── Tag creation: needs lot header + items                  │
│      ├── Balance calc: needs lot items (orphan risk!)            │
│      └── Stone reconciliation: needs lot stones                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 5. Brain Completeness Summary After 10 Rounds

| Round | Key Work Done | New Bugs |
|---|---|---|
| 1 | Initial brain build — controller/model skeleton, 12 bugs | 12 |
| 2 | Controller deep dive — hidden fields, save/update/cancel flows | 7 |
| 3 | Print view analysis — customer/office/vendor/branch ack | 4 |
| 4 | JS L1-14800 — AJAX map, stone modal, edit flow | 5 |
| 5 | JS L14800-23630 — complete JS trace, all 31 AJAX endpoints | 2 |
| 6 | View files — lot_merge, lot_split, vendor_ack, branch_ack, legacy | 5 |
| 7 | Model SQL deep audit — all 44 methods, injection map | 8 |
| 8 | CSS audit, CROSS_MODULE_MAP corrections, MODULE_BRAIN.md update | 1 |
| 9 | Reverse dependency: reports model (0), tag model (324+) | 1 |
| 10 | All model files scanned — definitive reverse dependency map | 0 |
| **Total** | | **45 IDs / 41 unique** |

> **BRAIN IS DEFINITIVELY COMPLETE AFTER 10 ROUNDS** — No further meaningful analysis targets remain in the Lot module codebase.

---

## 6. Recommended Next Steps (Priority Order)

| Priority | Action | Bug IDs |
|---|---|---|
| 🔴 P1 | Fix lot delete to cascade child tables | R-LOT-012 |
| 🔴 P2 | Fix trailing space in lot_completed UPDATE | R-LOT-023 |
| 🔴 P3 | Remove `echo last_query()` from merge + split error handlers | R-LOT-003, R-LOT-004 |
| 🟠 P4 | Fix `upload_lotimg` missing endpoint | R-LOT-006 |
| 🟠 P5 | Fix hardcoded `branch:1` in JS lot close | R-LOT-028 |
| 🟠 P6 | Fix `getLotidsforSplit()` missing GROUP BY | R-LOT-039 |
| 🟡 P7 | Fix uninitialized return vars in getLotNoForMerge/Split | R-LOT-038 |
| 🟡 P8 | Fix inverted mc_type label in get_lotInward_detail | R-LOT-020 |
