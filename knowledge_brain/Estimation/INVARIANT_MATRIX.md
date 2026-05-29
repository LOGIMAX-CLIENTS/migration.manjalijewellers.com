# Estimation Module — Invariant Matrix

> **Module**: Estimation
> **Date Built**: 2026-03-24 (Round 1)
> **Purpose**: Config-driven behavior grids — variant × behavior matrices for settings that change calculation logic

---

## Overview of Variant Dimensions

The Estimation module has heavy config-driven behavior: 28 hidden form fields drive all JS calculations. The 4 most critical variant dimensions are:

| Dimension | DB Column | PHP Variable | JS Variable | Values |
|---|---|---|---|---|
| Weight Basis | `calculation_based_on` | `$calculation_based_on` (in form.php data) | `#calculation_based_on` (hidden field) | 0 = Gross WT, 1 = Net WT for both, 2 = Net WT for VA + Gross WT for MC |
| Wastage Rate Type | `wastage_rate_type` | `$wastage_rate_type` | `#wastage_rate_type` | 0 = Percent of sale value, 1 = Per gram |
| Scheme Type (Chit) | `scheme_type` | `$scheme_type` (in chit detail row) | chit row attribute | 0 = Amount-based, 1/2/3 = Weight-based |
| GST Mode | Company state vs Customer state | Loaded from `getCompanyDetails()` | — (PHP template only) | Same state → CGST+SGST split, Different state → IGST |

---

## Dimension 1: `calculation_based_on` (Weight Basis)

Controls how VA (Making Charge/Wastage) and MC are calculated.

| Mode | Value | VA Calculation | MC Calculation | PHP Print Recalculates? |
|---|---|---|---|---|
| Gross WT | `0` | VA % applied to Gross Weight | MC % applied to Gross Weight | ✅ YES — same mode |
| Net WT (both) | `1` | VA % applied to Net Weight | MC % applied to Net Weight | ✅ YES — same mode |
| Net WT for VA + Gross WT for MC | `2` | VA % applied to Net Weight | MC % applied to Gross Weight | ✅ YES — same mode |

**Controlling fields by file**:
- DB: `ret_settings.calculation_based_on` (or `ret_profile.calculation_based_on`)
- PHP form injection: `form.php` hidden field `#calculation_based_on`
- JS: `calculateSaleValue()`, `calculateCustomItemSaleValue()`, `calculate_total_mc_va()`
- PHP Print: `est_print.php`, `est_print_2.php` (modes 0/1/2 handled with if/else)

**Risk Grid — `calculation_based_on` × Item Type**:

| | Tag Item | Catalog Item | Custom Item | Old Metal |
|---|---|---|---|---|
| Mode 0 (Gross) | gwt × rate + VA(gwt) + MC(gwt) | gwt × rate + VA(gwt) + MC(gwt) | gwt × rate + VA(gwt) + MC(gwt) | Not affected by this setting |
| Mode 1 (Net both) | nwt × rate + VA(nwt) + MC(nwt) | nwt × rate + VA(nwt) + MC(nwt) | nwt × rate + VA(nwt) + MC(nwt) | Not affected |
| Mode 2 (Mixed) | nwt × rate + VA(nwt) + MC(gwt) | nwt × rate + VA(nwt) + MC(gwt) | nwt × rate + VA(nwt) + MC(gwt) | Not affected |

> ⚠️ **RISK**: If PHP print template and JS use different mode values (e.g., form.php injects wrong `calculation_based_on`), all printed totals differ from estimation totals. This is FR-EST-017.

---

## Dimension 2: `wastage_rate_type` (Wastage/VA Rate Type)

Controls whether VA/Wastage is expressed as a % of sale value or a per-gram rate.

| Mode | Value | Formula | Example |
|---|---|---|---|
| Percent of sale value | `0` | VA Amount = Sale Value × (Wastage% / 100) | Rate 5000/g, 10g net, 10% wastage → VA = (50,000 × 10%) = 5,000 |
| Per gram | `1` | VA Amount = Net WT × VA Rate Per Gram | Rate 5000/g, 10g net, 200/g VA rate → VA = (10 × 200) = 2,000 |

**Controlling fields**:
- DB: `ret_settings.wastage_rate_type`
- PHP: injection via `form.php` `#wastage_rate_type` hidden field
- JS: `calculate_total_mc_va()` branching logic

**Risk Grid — `wastage_rate_type` × Calculation modes**:

| | calc_based_on = 0 (Gross) | calc_based_on = 1 (Net) | calc_based_on = 2 (Mixed) |
|---|---|---|---|
| **wastage_rate_type = 0** (% of sale) | VA = (GWT × Rate) × Wastage% | VA = (NWT × Rate) × Wastage% | VA = (NWT × Rate) × Wastage% |
| **wastage_rate_type = 1** (per gram) | VA = GWT × VA_Rate_Per_Gram | VA = NWT × VA_Rate_Per_Gram | VA = NWT × VA_Rate_Per_Gram |

> ⚠️ **RISK**: Switching `wastage_rate_type` mid-deployment silently changes all VA amounts on ALL future estimations without any code change. Old vs. new estimations may not be comparable.

---

## Dimension 3: `scheme_type` (Chit Scheme Calculation)

Controls how chit scheme deduction is calculated from the estimation.

| Scheme Type | Value | Chit Deduction Formula | Print Rendering |
|---|---|---|---|
| Amount-based | `0` | Deduction = `utl_amount` (direct rupee amount) | Display rupee amount directly |
| Weight × Rate | `1` | Deduction = `closing_weight` × current gold rate | `closing_weight × goldrate_22ct` |
| Weight × Rate (variant) | `2` | Same as type 1 (slight UI difference) | Same |
| Weight × Rate (variant) | `3` | Same as type 1 (slight UI difference) | Same |

**Controlling fields**:
- DB: `scheme.scheme_type` (joined via `scheme_account.id_scheme`)
- PHP: fetched in `get_chit_details()` model method
- JS: `calculate_chit_closing_balance()`, `get_topup_chit_closing_balance()`
- PHP Print: `est_print.php` L~400-450, `est_print_2.php` similar logic

> ⚠️ **RISK**: If a customer's scheme type changes between estimation creation and billing conversion, the chit deduction amount may be recalculated with the new type, causing financial discrepancy.

---

## Dimension 4: GST Mode (IGST vs CGST/SGST)

Controls how tax is displayed on print (PHP template only).

| Condition | Mode | Display |
|---|---|---|
| Company state == Customer state | Intra-state | CGST (50% of GST) + SGST (50% of GST) shown separately |
| Company state ≠ Customer state | Inter-state | IGST (full GST %) shown as single line |
| `is_eda == 1` (EDA mode) | EDA | Tax lines **hidden** regardless of state comparison |

**Controlling fields**:
- Company: `company_details.id_state` (loaded via `getCompanyDetails()`)
- Customer: `customer.id_state` (loaded in estimation header)
- EDA: `ret_estimation.is_eda`
- PHP Print: `est_print_2.php` handles IGST/CGST switch; `est_print.php` hides tax for EDA

> ⚠️ **RISK**: Customer state stored at estimation creation time. If customer address is later updated, re-printing the same estimation may use the NEW state (different GST split). The state comparison runs dynamically from `customer` table, not from a snapshot stored at estimation time.

---

## Dimension 5: `allow_manual_rate` (Manual Metal Rate Entry)

| Value | Behavior |
|---|---|
| `0` | Metal rate is auto-fetched from `ret_branchwise_rate` — employee cannot change it |
| `1` | Employee can manually type a custom rate — no upper bound other than tolerance limits |

**Risk**: JS function `check_rate_is_valid()` validates rate against `min_gold_tol`/`max_gold_tol` (from `emp_setting`), but these tolerances are per-employee. If employee tolerances are set very wide (or zero), manual rate entry is unconstrained.

---

## Dimension 6: `allowed_old_met_pur` (Old Metal Purity Restriction)

| Value | Behavior |
|---|---|
| `1` | Employee can buy ALL old metal types (gold + silver) |
| `2` | Employee can only buy old gold |
| `3` | Employee can only buy old silver |

**Controlling field**: `emp_setting.allowed_old_met_pur` → injected as `#allowed_old_met_pur` hidden field in form.php

**Risk**: Switching employees mid-estimation does NOT refresh hidden config fields. An estimation started by Employee A (gold+silver allowed) and completed by Employee B (gold only) may contain silver old metal — the restriction is no longer active once the form is loaded.

---

## Full Variant Combination Coverage Summary

| Variant | # of Possible States | Test Coverage | Status |
|---|---|---|---|
| `calculation_based_on` | 3 | None confirmed | ❌ Unverified |
| `wastage_rate_type` × `calculation_based_on` | 3 × 2 = 6 | None confirmed | ❌ Unverified |
| `scheme_type` | 4 (0, 1, 2, 3) | None confirmed | ❌ Unverified |
| GST mode | 3 (intra, inter, EDA) | None confirmed | ❌ Unverified |
| `allow_manual_rate` | 2 | None confirmed | ❌ Unverified |
| `allowed_old_met_pur` | 3 | None confirmed | ❌ Unverified |

> **Recommendation**: Before running a bug audit, add these 6 variant dimensions as test parameters. Any bug reported in Estimation should first ask: "Which variant config was active when this bug occurred?"
