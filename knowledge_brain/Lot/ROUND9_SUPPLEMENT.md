# LOT MODULE — ROUND 9 SUPPLEMENT
> Module: Lot | Round 9 | 2026-03-17
> **Reverse Dependency Audit — Who Reads Lot Tables**

---

## 1. Reports Model Audit

**File**: `admin/application/models/ret_reports_model.php`

**Result**: ✅ Zero references to `ret_lot_inwards`, `ret_lot_inwards_detail`, or any `ret_lot_*` table.

The Lot module is **not queried by the reports module**. Lot reporting (if any) is handled separately (e.g., through the Lot module's own print/acknowledgement routes) or not yet implemented.

---

## 2. 🔴 MAJOR FINDING: Tagging Module Has Massive Reverse Dependency on Lot Tables

**File**: `admin/application/models/ret_tag_model.php`

**References to Lot tables**: **324+ occurrences** (grep capped at 50 visible)

### Lot Tables Read by ret_tag_model.php

| Table | How Used |
|---|---|
| `ret_lot_inwards` | LEFT JOIN to get lot_no, lot_date, gold_smith, category, stock_type for tag context |
| `ret_lot_inwards_detail` | LEFT JOIN to get lot_product, gross_wt, net_wt, id_lot_inward_detail for tag balance calculations |
| `ret_lot_inwards_stone_detail` | LEFT JOIN for stone reconciliation on tag creation |
| `ret_lot_merge` | Referenced for merge-aware tag queries |
| `ret_lot_split_details` | Referenced for split-aware balance queries |

### Representative SQL Joins (from grep results)

```sql
-- tag_model L502: tag list JOIN
LEFT JOIN ret_lot_inwards as lot ON lot.lot_no = tag.tag_lot_id

-- tag_model L977: lot data query for tag creation
SELECT lot_no,lot_date,lot_type,lot_received_at,gold_smith,order_no,
   lot_product,lot_sub_product,no_of_piece,...
FROM ret_lot_inwards WHERE lot_no=...

-- tag_model L3149: merge-aware split query
LEFT JOIN ret_lot_inwards_detail lt_det on lt_det.id_lot_inward_detail = lm.id_lot_inward_detail

-- tag_model L3326: split-aware balance query
LEFT JOIN ret_lot_inwards_detail d on d.id_lot_inward_detail = ls.id_lot_inward_detail

-- tag_model L6843: tag detail with lot context
Left join ret_lot_inwards_detail lot_det ON tag.id_lot_inward_detail = lot_det.id_lot_inward_detail
LEFT JOIN ret_lot_inwards as lot_inw ON lot_inw.lot_no = lot_det.lot_no
```

### Impact Assessment

**Why this matters for bug diagnosis**:

1. **If `ret_lot_inwards_detail` gets orphaned rows** (R-LOT-012 — delete doesn't cascade): The Tagging module's `ret_tag_model` will still JOIN against these orphaned rows, producing incorrect tag balances and incorrect split/merge availability checks.

2. **If `lot_no` is wrong** (R-LOT-023 — trailing space in lot_completed UPDATE): The tagging module may continue to tag against a lot that was supposed to be closed, because `is_closed=1` was never written.

3. **If `is_lot_split=1` is set incorrectly** (Split flow bug): Tag module reads `is_lot_split` from `ret_lot_inwards` to determine eligibility — wrong flag = wrong tag eligibility.

4. **Schema Changes to Lot tables**: Any column rename or removal in `ret_lot_inwards` or `ret_lot_inwards_detail` would break 324+ queries in `ret_tag_model.php` silently at runtime.

### ⚠️ Bug R-LOT-044: Tagging module reads old `ret_lot_inwards` schema (L977)

**Line L977 in ret_tag_model.php**:
```sql
SELECT lot_no,lot_date,lot_type,lot_received_at,gold_smith,order_no,
  lot_product,lot_sub_product,no_of_piece,no_of_tags,metal,gross_wt,net_wt,
  precious_stone,semi_precious_stone,normal_stone,...
  FROM ret_lot_inwards WHERE lot_no=...
```

Fields like `lot_sub_product`, `no_of_tags`, `metal`, `making_per_grm`, `touch`, `rate_per_grm`, `normal_stn_certificate`, `precious_stn_certificate`, `semiprecious_stn_certificate` are **legacy column names** that no longer match the current schema documented in `ret_lot_model.php`. The current schema uses:
- `lot_id_design` (not `lot_sub_product`)
- `wastage_percentage` (not `touch`/`rate_per_grm` in the same column)
- `normal_st_certif` (not `normal_stn_certificate`)

This query in `ret_tag_model` appears to be using an **outdated column schema** — it may return NULL values silently for these old column names.

---

## 3. Other Models Reading Lot Tables

### Summary of Cross-Module Reads INTO Lot Tables

| Model File | References | Primary Use |
|---|---|---|
| `ret_tag_model.php` | **324+** | Tag creation from lot, balance calculations, split/merge checks |
| `ret_reports_model.php` | 0 | Not used |
| *(others not scanned)* | TBD | — |

### ⚠️ Updated CROSS_MODULE_MAP — REVERSE Dependencies (OTHER modules reading Lot tables)

This was completely missing from the original `CROSS_MODULE_MAP.md` which only documented LOT → others. The reverse direction is:

| Module | Tables Read from Lot | Impact if Lot table changes |
|---|---|---|
| **Tagging** (`ret_tag_model`) | `ret_lot_inwards`, `ret_lot_inwards_detail`, `ret_lot_inwards_stone_detail`, `ret_lot_merge`, `ret_lot_split_details` | 324+ query locations would break |

---

## 4. CROSS_MODULE_MAP.md — Round 9 Corrections

The original `CROSS_MODULE_MAP.md` showed estimated JS AJAX targets. After Rounds 5+8, the verified targets are now documented in ROUND8_SUPPLEMENT (Section 3). Key corrections vs estimates:

| Estimated (Round 1) | Verified (Round 9) | Change |
|---|---|---|
| `admin_ret_grn` for GRN list | `admin_ret_purchase/purchase/active_grns` | Wrong module |
| `admin_ret_customer` for customer search | `admin_ret_estimation/getCustomersBySearch` | Wrong module |
| `admin_ret_employee` for employee list | `admin_ret_estimation/get_employee` | Wrong module |
| `admin_ret_catalog` for design search | `admin_ret_brntransfer/branch_transfer/getDesignByFilter` | Wrong module |

---

## 5. DATA_FLOW.md — JS Function Correction

The Round 1 DATA_FLOW.md JS Function Map had estimated line numbers. Round 5 verified the actual lines:

| JS Function | Round 1 Estimate | Round 5 Verified |
|---|---|---|
| `getSearchProd()` | `#lt_product keyup (len=3)` | `#lt_product` keyup — `admin_ret_catalog/product/active_prodBySearch` |
| `getSearchDesign()` | `#design keyup (len≥2)` | `admin_ret_brntransfer/branch_transfer/getDesignByFilter` (WRONG MODULE) |
| `getSearchCustomers()` | `#cus_name keyup (len≥3)` | `admin_ret_estimation/getCustomersBySearch` |
| `Lot_Completed_Details()` | Not documented | JS L14873 — sends `branch:1` hardcoded (R-LOT-028) |
| `get_ActiveGRNS()` | `admin_ret_grn (estimated)` | `admin_ret_purchase/purchase/active_grns` |

---

## 6. New Bug Found in Round 9

| Bug ID | Severity | Description | Location |
|---|---|---|---|
| R-LOT-044 | 🟠 MEDIUM | `ret_tag_model.php` L977 reads old `ret_lot_inwards` column names (legacy schema) — will return NULL silently for updated columns | `ret_tag_model.php` L977 |

---

## 7. Actionable Insight: Deletion Risk Chain

The combination of R-LOT-012 (no cascade delete) + Tagging's 324+ lot table reads creates a dangerous data integrity chain:

```
User deletes a lot (GET /lot_inward/delete/{id})
    → ret_lot_inwards deleted ✅
    → ret_lot_inwards_detail NOT deleted ❌ (orphan)
    → ret_lot_inwards_stone_detail NOT deleted ❌ (orphan)
    → ret_tag_model.php joins ret_lot_inwards_detail → finds rows → wrong calculations
    → Tag balance shows phantom lot items that were "deleted"
    → New tags can be created against deleted lot rows
    → Data integrity broken silently
```

**Fix priority**: R-LOT-012 should be promoted from High → Critical given this cascading impact on the Tagging module.

---

## 8. Final Brain Statistics After Round 9

| Metric | Value |
|---|---|
| Total rounds | 9 |
| Unique bugs | **41** (R-LOT-001..044, with 1 dup = 40 addressed + 41 IDs) |
| Reverse dependencies found | Tagging module (324+ queries into Lot tables) |
| Models confirmed NOT reading Lot | ret_reports_model.php |
| New critical finding | ret_tag_model reads old schema at L977 |
