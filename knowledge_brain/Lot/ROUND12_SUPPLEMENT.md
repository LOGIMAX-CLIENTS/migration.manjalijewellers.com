# LOT MODULE — ROUND 12 SUPPLEMENT
> Module: Lot | Round 12 | 2026-03-17
> **Corrected Reverse Dependency Map + Navigation Audit**

---

## ⚠️ CORRECTION to Round 10 Findings

> **Round 10 incorrectly stated "only ret_tag_model reads Lot tables."**
> The file-level grep in Round 10 returned true results, but the line-by-line scan in Round 12 revealed additional models. Total modules reading Lot tables: **4 (not 1)**.

---

## 1. Complete Corrected Reverse Dependency Map

All models confirmed reading `ret_lot_inwards` / `ret_lot_inwards_detail`:

| Model | Line References | Tables Used | Purpose |
|---|---|---|---|
| `ret_tag_model.php` | **324+** | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_lot_inwards_stone_detail`, `ret_lot_merge`, `ret_lot_split_details` | Tag creation, balance calculations, split/merge eligibility |
| `ret_estimation_model.php` | **~12** | `ret_lot_inwards`, `ret_lot_inwards_detail` | Estimation views showing lot-linked tag details |
| `ret_stock_issue_model.php` | **~6** | `ret_lot_inwards`, `ret_lot_inwards_detail` | Stock issue views with lot-linked tag context |
| `ret_sales_transfer_model.php` | **2** | `ret_lot_inwards` | Sales transfer joins lot header via `tag_lot_id` |

### Usage Pattern: What Each Module Reads

#### `ret_estimation_model.php` (Lines 161, 162, 928-929, 965-966, 1030-1031, 1113-1114, 1604-1605, 1655-1656)
```sql
-- L161-162: Direct lot query
FROM ret_lot_inwards l
LEFT JOIN ret_lot_inwards_detail lt_det on lt_det.lot_no = l.lot_no

-- L928-929, L965-966, L1030-1031, L1113-1114: Pattern repeated ~6 times
Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
```
**Pattern**: All queries join through `ret_taging.id_lot_inward_detail → ret_lot_inwards_detail → ret_lot_inwards` to get lot metadata for estimation/approval views.

#### `ret_stock_issue_model.php` (Lines 693, 695, 891, 893, 1165, 1169)
```sql
-- L693-695, L891-893, L1165-1169: Pattern repeated 3 times
Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
```
**Pattern**: Identical join pattern as estimation model — stock issue tracking needs lot context per tag.

#### `ret_sales_transfer_model.php` (Lines 109, 327)
```sql
-- L109, L327: Two occurrences
Left join ret_lot_inwards l on t.tag_lot_id=l.lot_no
```
**Pattern**: Simple lot header join via `tag_lot_id` to get lot info for sales transfer records.

---

## 2. Updated Complete Reverse Dependency Map

```
Lot Tables (ret_lot_inwards, ret_lot_inwards_detail, ...)
    ↑ READ BY:
    ├── Tagging Module (ret_tag_model.php)          — 324+ queries
    ├── Estimation Module (ret_estimation_model.php) — ~12 queries
    ├── Stock Issue Module (ret_stock_issue_model.php) — ~6 queries
    └── Sales Transfer Module (ret_sales_transfer_model.php) — 2 queries
```

**Total external queries into Lot tables: ~344+ across 4 modules**

### Modules Confirmed Independent (NO lot table references):
- `ret_billing_model.php` ✅
- `ret_dashboard_model.php` ✅
- `ret_purchase_order_model.php` ✅
- `ret_order_model.php` ✅
- `ret_metal_process_model.php` ✅
- `ret_reports_model.php` ✅
- `ret_app_api_model.php` ✅
- `ret_brntransfer_model.php` ✅
- Backup model files ✅

---

## 3. Impact Assessment — 4-Module Dependency

The implications of 4 modules reading lot tables:

| Scenario | Modules Impacted |
|---|---|
| Lot header deleted (R-LOT-012, orphan rows) | Tagging, Estimation, Stock Issue, Sales Transfer **all get phantom/broken joins** |
| `ret_lot_inwards_detail` orphaned | Tagging (balance wrong), Estimation (lot context NULL), Stock Issue (lot NULL) |
| Schema change to `ret_lot_inwards` | All 4 modules affected at 344+ query locations |
| `is_closed=1` never written (R-LOT-023) | Tagging (tags can be created against closed lot); Estimation, Stock Issue see still-open lots |

**R-LOT-012 now impacts 4 modules, not 1. Severity remains Critical but blast radius is wider.**

---

## 4. Navigation Audit — footer.php

**File**: `admin/application/views/layout/footer.php` L631–643

```php
<?php if($this->uri->segment(1)=='admin_ret_lot'){ ?>
    <!-- lot-specific JS and CSS loaded here -->
    <script src="<?php echo base_url();?>assets/js/ret_lot.js?v=<?php echo $version;?>" type="text/javascript"></script>
<?php } ?>
```

**Finding**: `ret_lot.js` (23,630 lines) is **lazy-loaded** — only loaded when the URL segment 1 equals `admin_ret_lot`. This is performance-correct but means:
- Any page rendering lot data outside the `admin_ret_lot` route (e.g., if another module loads a lot partial view) would have no JS loaded
- The `$version` cache-busting variable from footer scope controls JS cache invalidation

**No direct navigation menu entries for lot were found in header.php** — the lot module is likely accessed from a sidebar navigation that is dynamically built (common CI pattern using a menu config or DB-driven nav).

---

## 5. Hooks & Config Scan

```
admin/application/config/    — no lot-specific config found
admin/application/hooks/     — no lot hooks found
```

No cron jobs, hooks, or CI filters reference the lot module.

---

## 6. ⚠️ Bug R-LOT-045 (Medium): Estimation Model Direct Lot Query (L161-162)

At `ret_estimation_model.php` L161-162, there is a query that selects **directly from `ret_lot_inwards`** without going through the tag layer:

```sql
FROM ret_lot_inwards l
LEFT JOIN ret_lot_inwards_detail lt_det on lt_det.lot_no = l.lot_no
```

This is not a tag-mediated join — it's a direct lot read from the estimation module. If a lot is deleted (R-LOT-012), this query returns missing data directly in estimation views (not just tag balance issues). 

The estimation model thus has a **hard data dependency** on Lot tables staying intact.

---

## 7. Updated Brain Statistics After Round 12

| Metric | Previous (R10) | Corrected (R12) |
|---|---|---|
| Models reading Lot tables | 1 (Tagging only) | **4** (Tagging, Estimation, Stock Issue, Sales Transfer) |
| Total external lot queries | 324+ | **~344+** |
| Models confirmed independent | 15 | **11** |
| New bugs | — | R-LOT-045 (Estimation direct lot reference) |
| Total unique bugs | 41 | **42** |

---

## 8. Corrected CROSS_MODULE_MAP Section (Reverse Dependencies)

Replace Round 10 summary with:

| Module | Model File | Lot Tables Read | Query Count | Risk |
|---|---|---|---|---|
| **Tagging** | `ret_tag_model.php` | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_lot_inwards_stone_detail`, `ret_lot_merge`, `ret_lot_split_details` | 324+ | 🔴 Critical |
| **Estimation** | `ret_estimation_model.php` | `ret_lot_inwards`, `ret_lot_inwards_detail` | ~12 | 🟠 Medium |
| **Stock Issue** | `ret_stock_issue_model.php` | `ret_lot_inwards`, `ret_lot_inwards_detail` | ~6 | 🟠 Medium |
| **Sales Transfer** | `ret_sales_transfer_model.php` | `ret_lot_inwards` | 2 | 🟡 Low |
