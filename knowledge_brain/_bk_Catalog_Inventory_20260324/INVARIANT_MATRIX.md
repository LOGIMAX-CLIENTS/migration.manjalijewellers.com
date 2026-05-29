# Invariant Matrix
# Module: Catalog_Inventory

> **Purpose**: Documents variant-specific behavior for the product master entity where different configurations trigger different behavior in downstream modules (Tagging, Billing, Estimation, Purchase).
> **Built**: 2026-03-13 — Round 2

---

## 1. Variant Dimensions

| # | Dimension Name | Possible Values | Controlling Field (DB) | Controlling Field (PHP) | Controlling Field (JS) |
|---|---|---|---|---|---|
| 1 | Sales Mode | 1=Weight-based, 2=Piece-based, 3=Fixed Price | `ret_product_master.sales_mode` | `$addData['sales_mode']` | Form select |
| 2 | Purchase Mode | 1=Weight-based, 2=Piece-based | `ret_product_master.purchase_mode` | `$addData['purchase_mode']` | Form select |
| 3 | Wastage Type | 1=Percentage, 2=Per-gram, 3=Fixed | `ret_product_master.wastage_type` | `$addData['wastage_type']` | Form select |
| 4 | Stock Type | 1=Tagged, 2=Untagged, 3=Both | `ret_product_master.stock_type` | `$addData['stock_type']` | Form select |
| 5 | Stone Type | 0=No Stone, 1=With Stone | `ret_product_master.stone_type` | `$addData['stone_type']` | Form checkbox |
| 6 | Calculation Based On | NULL=Default, 1=Net Weight, 2=Gross Weight | `ret_product_master.calculation_based_on` | `$addData['calculation_based_on']` | Form select |
| 7 | Metal Type | NULL, Gold, Silver, Platinum, etc. | `ret_product_master.metal_type` | `$addData['metal_type']` | Form select |
| 8 | Tag Type | 0=Normal, 1=Special | `ret_product_master.tag_type` | `$addData['tag_type']` | Form select |
| 9 | Has Stone | 0=No, 1=Yes | `ret_product_master.has_stone` | `$addData['has_stone']` | Form checkbox |
| 10 | Has Hook | 0=No, 1=Yes | `ret_product_master.has_hook` | `$addData['has_hook']` | Form checkbox |
| 11 | Has Screw | 0=No, 1=Yes | `ret_product_master.has_screw` | `$addData['has_screw']` | Form checkbox |
| 12 | Has Fixed Price | 0=No, 1=Yes | `ret_product_master.has_fixed_price` | `$addData['has_fixed_price']` | Form checkbox |
| 13 | Has Size | 0=No, 1=Yes | `ret_product_master.has_size` | `$addData['has_size']` | Form checkbox |
| 14 | Less Stone Weight | 0=No, 1=Yes | `ret_product_master.less_stone_wt` | `$addData['less_stone_wt']` | Form checkbox |
| 15 | Tag Split | 0=No, 1=Yes | `ret_product_master.tag_split` | `$addData['tag_split']` | Form checkbox |
| 16 | Tag Merge | 0=No, 1=Yes | `ret_product_master.tag_merge` | `$addData['tag_merge']` | Form checkbox |
| 17 | Stone Board Rate Calc | 0=No, 1=Yes | `ret_product_master.stone_board_rate_cal` | `$addData['stone_board_rate_cal']` | Form checkbox |
| 18 | Sales Markup | 0=No, 1=Yes | `ret_product_master.sales_markup` | `$addData['sales_markup']` | Form checkbox |
| 19 | Hallmark | 0=No, 1=Yes | `ret_product_master.hallmark` | `$addData['hallmark']` | Form checkbox |
| 20 | RFID Required | 0=No, 1=Yes | `ret_product_master.rfid_required` | `$addData['rfid_required']` | Form checkbox |

---

## 2. Behavior Grids

### Grid: Sales Mode × Wastage Type

> These two dimensions together determine how billing calculates the final amount.

| | Wastage: Percentage (1) | Wastage: Per-gram (2) | Wastage: Fixed (3) |
|---|---|---|---|
| **Sales: Weight-based (1)** | ✅ Standard: `(weight + weight × wastage%) × rate` | ✅ `(weight + wastage_grams) × rate` | ✅ `(weight × rate) + fixed_wastage` |
| **Sales: Piece-based (2)** | ❓ Wastage on piece-based — unclear if weight is used at all | ❓ Same concern — weight component ambiguous | ✅ Piece rate + fixed wastage |
| **Sales: Fixed Price (3)** | ❓ Fixed price + wastage % — does wastage apply? | ❓ Fixed price + per-gram wastage — contradictory? | ✅ Pure fixed price |

> ⚠️ Combinations of Fixed Price sales mode with non-fixed wastage types are potentially **undefined behavior**. The Catalog module stores them without validation — downstream modules (Billing, Estimation) must handle these.

### Grid: Stock Type × Has Stone

| | No Stone (0) | With Stone (1) |
|---|---|---|
| **Tagged (1)** | ✅ Standard tagged item, weight-based | ✅ Tagged with stone weight deduction (if `less_stone_wt=1`) |
| **Untagged (2)** | ✅ Bulk stock, no individual tracking | ⚠️ Untagged with stone — how are stones tracked per-item? |
| **Both (3)** | ✅ Can be tagged or untagged | ❓ Stone handling depends on whether the specific item is tagged |

### Grid: Calculation Based On × Has Stone × Less Stone Weight

| | No Stone | Stone + Less Stone Wt OFF | Stone + Less Stone Wt ON |
|---|---|---|---|
| **Net Weight** | `billing_weight = net_wt` | `billing_weight = net_wt` (stone included) | `billing_weight = net_wt - stone_wt` |
| **Gross Weight** | `billing_weight = gross_wt` | `billing_weight = gross_wt` (stone included) | `billing_weight = gross_wt - stone_wt` |
| **Default (NULL)** | Falls through to module-specific logic | ❓ Depends on downstream handling | ❓ Undefined |

---

## 3. Edge Cases & Special Combinations

### Fixed Price + Markup
- **When**: `has_fixed_price=1` AND `sales_markup=1`
- **Expected**: Fixed price should override markup, OR markup should apply on top of fixed price
- **Actual**: Both flags stored independently — downstream module decides
- **Status**: ❓ Untested — potential conflict

### Tag Split + Tag Merge Both Enabled
- **When**: `tag_split=1` AND `tag_merge=1`
- **Expected**: Item can both split and merge — valid for composite items
- **Actual**: Both flags stored — no validation prevents both being set
- **Status**: ✅ Valid combination

### Hallmark + RFID
- **When**: `hallmark=1` AND `rfid_required=1`
- **Expected**: Item has both hallmark certification and RFID tracking
- **Actual**: Independent flags — no interaction
- **Status**: ✅ Correct

### Stone Board Rate + No Stone
- **When**: `stone_board_rate_cal=1` AND `has_stone=0`
- **Expected**: Should be impossible — meaningless combination
- **Actual**: No validation prevents this — `stone_board_rate_cal` ignored if no stone
- **Status**: ⚠️ No validation — data quality risk

---

## 4. Variant Test Coverage

| Dimension | Total Variants | Tested | Untested | Coverage |
|---|---|---|---|---|
| Sales Mode | 3 | 0 | 3 | 0% |
| Purchase Mode | 2 | 0 | 2 | 0% |
| Wastage Type | 3 | 0 | 3 | 0% |
| Stock Type | 3 | 0 | 3 | 0% |
| Stone Type | 2 | 0 | 2 | 0% |
| Calculation Based On | 3 | 0 | 3 | 0% |
| **Overall** | 324 combos | 0 | 324 | 0% |

> ⚠️ **No variant-specific testing** has been done. This is a significant gap because these configuration fields directly control how Billing, Estimation, and Tagging modules calculate prices and handle inventory.
