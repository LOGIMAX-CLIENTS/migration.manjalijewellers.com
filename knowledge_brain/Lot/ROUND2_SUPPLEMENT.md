# LOT MODULE — ROUND 2 SUPPLEMENT
> Module: Lot | Round 2 | 2026-03-17
> **Fills all gaps identified in COVERAGE_TRACKER Round 1**

---

## 1. Hidden Fields in form.php — Complete Register

> Scanned: form.php (2960 lines). Total hidden fields confirmed: **32**

### 1a. Header Section (Static/System-Set)

| `id` attribute | `name` attribute | Purpose | Line |
|---|---|---|---|
| `lot_no` | `inward[lot_no]` | Current lot PK (0 for add, >0 for edit) | L206 |
| `is_purchase_cost_from_lot` | `is_purchase_cost_from_lot` | Config: show purchase cost fields | L209 |
| `cmp_country` | `inward[cmp_country]` | Company country (for tax calc) | L211 |
| `cmp_state` | `inward[cmp_state]` | Company state (for IGST/CGST split) | L213 |
| `id_branch` | `inward[lot_received_at]` | Selected receiving branch ID | L253 |
| `lt_type_select` | `inward[lot_type]` | Lot type, hardcoded = "1" | L318 |
| `id_category` | `inward[id_category]` | Selected category ID (hidden, drives purity) | L329 |
| `id_purity` | `inward[id_purity]` | Selected purity ID | L373 |

### 1b. Goldsmith/Karigar Section

| `id` | `name` | Purpose | Line |
|---|---|---|---|
| `supplier_state` | *(none)* | Supplier state (for tax) | L273 |
| `supplier_country` | *(none)* | Supplier country (for tax) | L275 |
| `tds_percent` | *(none)* | TDS rate (from karigar data) | L277 |
| `tcs_percent` | *(none)* | TCS rate (from karigar data) | L279 |
| `lt_gold_smith_id` | `inward[gold_smith]` | Selected goldsmith ID | L282 |

### 1c. Item Detail Panel (Visible form area)

| `id` | `name` | Purpose | Line |
|---|---|---|---|
| `lot_inward_details_id` | *(none)* | Current editing detail row ID | L411 |
| `id_sub_design` | *(none)* | Selected sub-design ID | L413 |
| `id_design` | *(none)* | Selected design ID | L415 |
| `purchase_mode` | *(class: purchase_mode)* | Product purchase mode | L467 |
| `sales_mode` | *(class: sales_mode)* | Product sales mode | L469 |
| `pro_id` | *(none)* | Selected product ID | L471 |
| `design_id` | *(none)* | Design ID (duplicate for row use) | L485 |
| `sub_design_id` | *(none)* | Sub-design ID (duplicate for row use) | L501 |
| `lot_stone_details` | *(none)* | JSON-encoded stone details | L675 |
| `stone_price` | *(none)* | Total stone price | L677 |
| `other_metal_wt` | *(none)* | Other metal weight | L779 |
| `other_metal_wast_wt` | *(none)* | Other metal wastage weight | L781 |
| `other_metal_mc_amount` | *(none)* | Other metal making charge | L783 |
| `other_metal_details` | *(none)* | JSON-encoded other metal details | L789 |
| `other_charges_details` | *(none)* | JSON-encoded other charges | L809 |

### 1d. Tax Calculation Area

| `id` | Purpose | Line |
|---|---|---|
| `item_cgst_cost` | CGST amount | L1084 |
| `item_sgst_cost` | SGST amount | L1086 |
| `item_igst_cost` | IGST amount | L1088 |
| `item_tax_percentage` | Tax percentage | L1090 |
| `tax_type` | Tax type (IGST or CGST+SGST) | L1092 |
| `tax_group_id` | Tax group ID | L1094 |

### 1e. Table-level

| `id` | `name` | Purpose | Line |
|---|---|---|---|
| `curRow` | `curRow` | Current row index in table, starts -1 | L1155 |

**⚠️ Risk Notes:**
- `lt_type_select` is hardcoded to `value="1"` — lot_type is never changeable, despite having types 2/3
- `other_charges_details`, `other_metal_details`, `lot_stone_details` are plain-text JSON — not sanitized
- `supplier_state`, `supplier_country`, `tds_percent`, `tcs_percent` have empty `name=""` — never submitted in form POST, only used for JS logic

---

## 2. ret_settings Keys — Complete Register

> From controller code analysis

| Setting Name | Value Examples | Used By | What It Controls |
|---|---|---|---|
| `lot_recv_branch` | 1=Any Branch, 2=HO Only | `empty_record_inward()` model L357-375 | Who can receive lots |
| `is_purchase_cost_from_lot` | 0=No, 1=Yes | Controller L315, form.php L209, 877 | Show purchase cost fields |
| `is_supplierbill_entry_req` | 0=No, 1=Yes | Controller L311, form.php L337 | Show/require GRN selection |
| `allow_lot_cancel` | 0=Only today, 1=Any date | JS L3498 (list action buttons) | When cancel button is shown |
| *(others from profile table)* | — | `get_profile_settings()` | List display customization |

**Note**: `allow_lot_cancel` was found in JS (L3516: `profile.allow_lot_cancel`) — this comes from the `profile` table via `get_profile_settings()`, NOT from `ret_settings`. The full profile fields for lot are:
- `profile.allow_lot_cancel` — allow cancel on any date (not just today)

---

## 3. JS AJAX URLs — Confirmed Targets

> All URLs confirmed by reading ret_lot.js up to L4800.

| JS Function | Actual URL | Target Controller | Line |
|---|---|---|---|
| `get_lotInward_list()` | `admin_ret_lot/lot_inward/ajax` | `admin_ret_lot::lot_inward(default)` | L2591 |
| `getActiveUOM()` | `admin_ret_catalog/uom/active_uom` | `admin_ret_catalog::uom('active_uom')` | L2687 |
| `get_category()` | `admin_ret_catalog/category/active_category` | `admin_ret_catalog::category('active_category')` | L4090 |
| `get_cat_purity()` | `admin_ret_catalog/category/cat_purity` | `admin_ret_catalog::category('cat_purity')` | L4206 |
| `get_karigar()` | `admin_ret_catalog/karigar/active_list` | `admin_ret_catalog::karigar('active_list')` | L4310 |
| `getSearchProd()` | `admin_ret_catalog/product/active_prodBySearch` | `admin_ret_catalog::product('active_prodBySearch')` | L4386 |
| `getSearchDesign()` | `admin_ret_brntransfer/branch_transfer/getDesignByFilter` | `admin_ret_brntransfer::branch_transfer('getDesignByFilter')` | L4650 |
| `getSearchOrderNo()` | `admin_ret_lot/getOrderNosBySearch` | `admin_ret_lot::getOrderNosBySearch()` | L4754 |
| `remove_img()` | `admin_ret_lot/remove_img` | `admin_ret_lot::remove_img()` | JS L2291 |
| `lot img upload` | `admin_ret_lot/upload_lotimg` | **MISSING** (404 bug) | JS L2219 |

**⚠️ Critical Finding — Wrong Controller for Design Search:**
`getSearchDesign()` calls `admin_ret_brntransfer/branch_transfer/getDesignByFilter` — this is a **Branch Transfer** module endpoint, not catalog. This means:
- Design search in Lot form depends on the Branch Transfer module working
- If Branch Transfer module routes change → Lot design search breaks
- This is cross-module coupling that's not documented in either module

---

## 4. lot_from Code Path Analysis

> `lot_from` is set only at INSERT/create time. Once set, it's read-only (edit is only allowed for lot_from=1).

| lot_from | Set By | How Created | Edit Allowed? |
|---|---|---|---|
| 1 | `lot_inward('save')` | Manual lot entry (user) | ✅ Yes (L1126 check) |
| 2 | Other modules (Purchase/GRN) | Comes from supplier bill / GRN processing | ❌ No |
| 3 | Other modules | Import (external process) | ❌ No |
| 4 | Tagging module | Tag returns / reprocessing | ❌ No |
| 5 | Old Metal module | Old metal processing | ❌ No |
| 6 | Retagging module | Retagging process | ❌ No |
| 7 | `lot_merge('save')` L2000 | Lot merge (hardcoded) | ❌ No |
| 8 | Unknown (Not in lot controller) | NonTag lot — created by another module | ❌ No |

**Conclusion**: Only `lot_from=1` is created by the Lot module's own controller. Values 2–8 are created by other modules or processes. The Lot module's edit guard (`if lot_from != 1 → redirect`) is correct — you can't edit externally-created lots.

**Display label mapping** (from `ajax_getLotList` SQL, L196):
```sql
IF(l.lot_from=1,'Manual',
   IF(l.lot_from=2,'Supplier Entry',
      IF(l.lot_from=3,'Import',
         IF(l.lot_from=4,'Tag Process',
            IF(l.lot_from=5,'Old Metal',
               IF(l.lot_from=6,'Retagging',
                  IF(l.lot_from=7,'Merge',
                     IF(l.lot_from=8,'NonTag Lot','-')))))))))
```

---

## 5. get_tag_details() — Dead Code Analysis

**Model method**: `get_tag_details($lot_no)` at L764–794
**Called by**: `ajax_getLotList()` model at L244-248

```php
// In ajax_getLotList():
$lot['tag_det'] = $this->getTaggedDetails($lot['lot_no']);
$lot['branch_wise'] = $this->get_tagged_branchwise_details($lot['lot_no']);
```

**NOT called**: `get_tag_details()` — a **separate method** from `getTaggedDetails()`
- `getTaggedDetails()` = design+branch summary (used in list)
- `get_tagged_branchwise_details()` = branch-wise totals only (used in list drill-down)
- `get_tag_details()` = simpler product+purity summary — **appears unused** (no controller call found)

**Verdict**: `get_tag_details()` at L764 is **likely dead code**. It's a simplified version of `getTaggedDetails()` — possibly a legacy function never cleaned up.

---

## 6. Profile Settings Used by Lot

From `get_profile_settings()` → `profile` table → JS uses `data.profile`:

| Profile Field | JS Reference | Purpose |
|---|---|---|
| `allow_lot_cancel` | `profile.allow_lot_cancel` (JS L3516) | 0=Cancel only today's lots; 1=Cancel any date's lot |
| (others) | Various JS usage | List display format |

---

## 7. Additional Bugs Found in Round 2

| Bug ID | Severity | Description | Evidence |
|---|---|---|---|
| R-LOT-013 | 🔴 HIGH | `getSearchDesign()` in JS calls `admin_ret_brntransfer` not a catalog endpoint — cross-module coupling hidden in Lot JS | L4650 |
| R-LOT-014 | 🟠 MEDIUM | `lt_type_select` hardcoded to `value="1"` — lot_type=2 (Customer) and lot_type=3 (Repair) can never be set from this form | L318 |
| R-LOT-015 | 🟠 MEDIUM | `supplier_state`, `supplier_country`, `tds_percent`, `tcs_percent` have empty `name=""` — never POSTed to server but goldsmith TDS/TCS is silently lost | L273–279 |
| R-LOT-016 | 🟡 LOW | `get_tag_details()` model method appears to be dead code — superseded by `getTaggedDetails()` | Model L764 |
| R-LOT-017 | 🟡 LOW | Multiple `console.log()` statements in production JS | JS L2783, L2946, L3512, L3516, L4458, L4714 |
