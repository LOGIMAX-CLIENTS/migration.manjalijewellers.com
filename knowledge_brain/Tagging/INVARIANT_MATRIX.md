# Tagging Module — Invariant Matrix

> **Module**: Tagging
> **Last Updated**: 2026-02-24 (Round 3)
> **Purpose**: Document config-driven behavior variants that change calculation and display logic

---

## 1. Variant Dimensions

| #   | Dimension                | Values                                  | Controlling Field                       | Where Set                     |
| --- | ------------------------ | --------------------------------------- | --------------------------------------- | ----------------------------- |
| V1  | **Category Type**        | 1=Ornament, 2=Bullion, 3=Stone, 4=Alloy | `ret_taging.cat_type`                   | Product master → flows to tag |
| V2  | **MC Type**              | 1=Per Piece, 2=Per Gram, 3=% on Price   | `ret_taging.tag_mc_type`                | Tag form / wastage settings   |
| V3  | **Calculation Based On** | 0=Gross Wt, 1=Net Wt, 2=Gross Wt        | `ret_taging.calculation_based_on`       | Tag form / settings           |
| V4  | **Stone Calc Based On**  | 1=Weight-based, 2=Pieces-based          | `ret_taging.stone_calculation_based_on` | Tag form                      |
| V5  | **Rate Calc Type**       | Touch-based, Purity-based               | `ret_taging.lot_rate_calc_type`         | Lot inward settings           |
| V6  | **Tag Status**           | 0-17 (see §5 below)                     | `ret_taging.tag_status`                 | Various modules               |
| V7  | **Tag Type**             | 0=Normal, 1=Suspense Stock              | `ret_taging.tag_type`                   | Tag form                      |

---

## 2. Behavior Grid: Category Type × MC Type

> This is the most critical variant interaction — determines how tag sale value is calculated.

|                           | MC Type 1 (Per Piece)                                | MC Type 2 (Per Gram)                                         | MC Type 3 (% on Price)                                               |
| ------------------------- | ---------------------------------------------------- | ------------------------------------------------------------ | -------------------------------------------------------------------- |
| **Ornament (cat_type=1)** | `sale = metal_value + mc_flat + wastage + stones`    | `sale = metal_value + (mc_rate × weight) + wastage + stones` | `sale = metal_value + (metal_value × mc_pct/100) + wastage + stones` |
| **Bullion (cat_type=2)**  | `sale = metal_value + mc_flat`                       | `sale = metal_value + (mc_rate × weight)`                    | `sale = metal_value × (1 + mc_pct/100)`                              |
| **Stone (cat_type=3)**    | `sale = stone_value + mc_flat` (uses UOM, not grams) | `sale = stone_value + (mc_rate × uom_wt)`                    | `sale = stone_value × (1 + mc_pct/100)`                              |
| **Alloy (cat_type=4)**    | Same as Ornament                                     | Same as Ornament                                             | Same as Ornament                                                     |

### Where Implemented

- **JS**: `calculateTagSaleValue()` L15757 — single function handles all combinations
- **PHP**: No server-side validation of the formula — trusts JS-computed `sales_value`
- **Risk**: If JS bug miscalculates, wrong value is stored permanently with no server guard

---

## 3. Behavior Grid: Category Type × Weight Base

|              | Calc Based On = 0 (Gross)    | Calc Based On = 1 (Net)    | Calc Based On = 2 (Gross)    |
| ------------ | ---------------------------- | -------------------------- | ---------------------------- |
| **Ornament** | `weight_for_calc = gross_wt` | `weight_for_calc = net_wt` | `weight_for_calc = gross_wt` |
| **Bullion**  | `weight_for_calc = gross_wt` | `weight_for_calc = net_wt` | `weight_for_calc = gross_wt` |
| **Stone**    | Uses `uom_gross_wt` (UOM)    | Uses `uom_gross_wt` (UOM)  | Uses `uom_gross_wt` (UOM)    |
| **Alloy**    | `weight_for_calc = gross_wt` | `weight_for_calc = net_wt` | `weight_for_calc = gross_wt` |

> ⚠️ **Note**: `calculation_based_on = 0` and `= 2` both use gross weight. This appears redundant. Investigate if there's a historical reason or if it's a bug.

### Where Implemented

- **JS**: Switch/if blocks in `calculateTagSaleValue()` — checks `calculation_based_on`
- **PHP**: `update_purchase_cost()` L9381 uses this for purchase value calc too
- **DB**: Stored in `ret_taging.calculation_based_on`

---

## 4. Behavior Grid: Stone Calculation

|                  | Stone Calc = 1 (Weight)              | Stone Calc = 2 (Pieces)                 |
| ---------------- | ------------------------------------ | --------------------------------------- |
| **Stone Amount** | `stone_amt = stone_wt × stone_rate`  | `stone_amt = stone_pieces × stone_rate` |
| **Balance Calc** | Deduct from lot stone weight balance | Deduct from lot stone piece balance     |

### Where Implemented

- **JS**: `calculate_stone_amount()` L17818
- **PHP**: `get_stoneDetails()` L3902 (model, for lot balance)
- **Risk**: If `stone_calculation_based_on` is changed after stones are saved, existing stone amounts won't recalculate

---

## 5. Tag Status State Transitions

### Valid Status Values

| Status | Meaning          | Set By          | Can Transition To          |
| ------ | ---------------- | --------------- | -------------------------- |
| 0      | On Sale          | Tag creation    | 1, 2, 3, 4, 5, 6, 7, 8, 17 |
| 1      | Sold Out         | Billing module  | 3 (if return)              |
| 2      | Deleted          | Tagging (OTP)   | — (terminal)               |
| 3      | Other Issue      | Retagging       | — (terminal for this tag)  |
| 4      | In Transit       | Branch Transfer | 0 (when received)          |
| 5      | Melted           | Metal Process   | — (terminal)               |
| 6      | Karigar Issue    | Tag marking     | 0 (when returned)          |
| 7      | Lost/Damaged     | Tag marking     | — (terminal)               |
| 8      | Under Repair     | Tag marking     | 0 (when returned)          |
| 9-16   | Various (custom) | Settings-driven | Varies                     |
| 17     | Metal Issue      | Metal Process   | 0 (when returned)          |

> ⚠️ **CRITICAL**: No centralized state machine enforces these transitions. Any module can set any status with a raw UPDATE. This means invalid transitions (e.g., Sold → On Sale without a return process) are possible.

### Invariant Rules for Status

| Invariant     | Rule                                                         | Violation Impact |
| ------------- | ------------------------------------------------------------ | ---------------- |
| INV-STATUS-01 | Deleted (2) tags must have lot balance restored              | Phantom stock    |
| INV-STATUS-02 | In Transit (4) tags must have a `ret_branch_transfer` record | Orphan status    |
| INV-STATUS-03 | Sold (1) tags must have a billing record                     | Ghost sale       |
| INV-STATUS-04 | Retagged (3) tags must have a `ret_retagging_process` record | Orphan status    |

---

## 6. Wastage Settings Cascade

The wastage/MC settings come from a cascading lookup with fallback:

```
1. Sub-Design level settings → get_wastage_settings_details(product, design, sub_design)
   ↓ (if not found)
2. Design level settings → get_wastage_settings_details(product, design, null)
   ↓ (if not found)
3. Product level settings → get_wastage_settings_details(product, null, null)
   ↓ (if not found)
4. Global defaults → No wastage, no MC limits
```

### Weight Range Based MC Limits

Within each settings level, MC/VA limits can vary by weight range:

| Weight Range | MC Min | MC Max | VA Min | VA Max |
| ------------ | ------ | ------ | ------ | ------ |
| 0 - 10g      | 300/g  | 500/g  | 2%     | 5%     |
| 10 - 50g     | 250/g  | 450/g  | 1.5%   | 4%     |
| 50g+         | 200/g  | 400/g  | 1%     | 3%     |

### Where Implemented

- **Model**: `get_wastage_settings_details()` L7433 — cascade query
- **Model**: `get_weight_range_details()` L7579 — weight ranges
- **JS**: `set_tagging_wastage_and_mc()` L33456/L33624 — apply to form (2 instances!)
- **JS**: `get_mc_va_limit()` L29122 — fetch from server

> ⚠️ **RISK**: `set_tagging_wastage_and_mc` defined twice (L33456 & L33624). Second overwrites first. Check which behavior is intended.

---

## 7. Rate Calculation Type Variants

| Rate Calc Type   | Formula                                               | Used For                      |
| ---------------- | ----------------------------------------------------- | ----------------------------- |
| **Touch-based**  | `rate = base_rate × (tag_touch / standard_touch)`     | Gold/silver with touch purity |
| **Purity-based** | `rate = purity_specific_rate` (from metal_rate table) | Standard purity-based pricing |

### Where Implemented

- **PHP**: `get_branchwise_rate()` L2741 — fetches rate based on type
- **PHP**: `get_all_purities_for_product()` L2593 — purity + rate lookup
- **JS**: `get_metal_rates_by_branch()` L12310 — fetches and applies rate

---

## 8. Config-Driven Form Visibility

Some form fields show/hide based on product or settings:

| Setting                    | Controls             | When Visible                              |
| -------------------------- | -------------------- | ----------------------------------------- |
| `cat_type = 3` (Stone)     | UOM fields           | Stone category only                       |
| `has_attributes`           | Attribute modal      | When sub-design has attributes configured |
| `has_other_metals`         | Other metals section | When product is mixed-metal               |
| `profile.order_unlink_otp` | OTP button on unlink | When profile has OTP enabled              |
| `is_mrp` flag              | MRP/non-MRP pricing  | Per-tag MRP toggle                        |
