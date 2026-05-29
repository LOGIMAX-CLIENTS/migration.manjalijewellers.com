# PURCHASE MODULE — JAVASCRIPT DEEP DIVE
> **Round:** 7 | **Date:** 2026-02-23 | **File:** `ret_purchase_order.js` (82,888 lines, 1.3MB)

---

## File Stats
| Metric | Value |
|---|---|
| Total lines | 82,888 |
| Total functions | 744 |
| Named functions | 400+ |
| `parseFloat/parseInt/toFixed/isNaN` usage | 1,544 |
| `$.ajax()` calls | 185 |
| Global variables | 30+ |

---

## 🔴 20 Mega-Functions (>600 lines each)

| # | Function | Lines | Purpose | Risk |
|---|---|---|---|---|
| 1 | `calculate_purchase_item_cost()` | **1,603** | Bill entry cost calculation | 🔴 |
| 2 | `set_detailed_valut_report()` | **1,499** | HO vault report (detailed) | 🔴 |
| 3 | `set_summary_valut_report()` | **1,363** | HO vault report (summary) | 🔴 |
| 4 | `calculate_purchase_return_item_cost()` | **1,335** | Purchase return cost calc | 🔴 |
| 5 | `get_weight_gain_loss_report()` | **1,321** | Weight gain/loss report | 🔴 |
| 6 | `get_retagging_list()` | **1,160** | Retagging report render | 🔴 |
| 7 | `get_headoffice_valut_report_old()` | **1,107** | HO vault report (legacy) | 🔴 |
| 8 | `calculate_purchase_return_final_cost()` | **1,103** | Return final cost calc | 🔴 |
| 9 | `get_purchase_order_items()` | **1,019** | PO items render | 🟠 |
| 10 | `get_qc_issue_details()` | **895** | QC issue details render | 🟠 |
| 11 | `get_stock_repair_order_details()` | **875** | Stock repair render | 🟠 |
| 12 | `create_new_empty_stone_entry_row()` | **867** | Stone entry row creation | 🟠 |
| 13 | `get_unfixing_details()` | **859** | Unfixing details render | 🟠 |
| 14 | `get_karigar_bill()` | **825** | Karigar bill render | 🟠 |
| 15 | `send_karigar_sms()` | **755** | SMS sending | 🟡 |
| 16 | `get_rate_fixed_details()` | **739** | Rate fixed details | 🟡 |
| 17 | `calculate_grnItem_details()` | **723** | GRN item calc | 🟡 |
| 18 | `get_pur_order_Details()` | **682** | PO detail render | 🟡 |
| 19 | `set_qc_issue_preview_detaails()` | **679** | QC issue preview | 🟡 |
| 20 | `calculatedSelectedPosCost()` | **638** | Selected POs cost | 🟡 |

**Total lines in mega-functions:** 19,081 (23% of entire file)

> **Key Risk:** `calculate_purchase_item_cost()` (1,603 lines) is the financial core — ANY bug here affects every purchase bill. Same for `calculate_purchase_return_item_cost()` (1,335 lines) affecting every return.

---

## DataTable Initialization

| Metric | Count |
|---|---|
| `.DataTable()` / `.dataTable()` calls | **58** |
| Risk of re-initialization | ⚠️ HIGH |

**Pattern to check:** If any AJAX callback re-calls `.DataTable()` on the same `<table>` element, it will throw a warning and create duplicate event handlers.

---

## Select2 Deprecated API

| Metric | Count |
|---|---|
| Deprecated `.select2("val", ...)` usage | **10** |
| Should use `.val(...).trigger('change')` | — |

**Locations (sample):** Used in older sections of the file for setting values programmatically. Per project rules (Rule #9), must use `.val(...).trigger('change')`.

---

## Event Binding Patterns

| Pattern | Count | Context |
|---|---|---|
| `$(document).on('event', selector)` ← CORRECT | **112** | Delegated for dynamic rows |
| `$('#id').on()` / `.click()` / `.change()` ← DIRECT | **150** | Direct binding |

**Risk:** 150 direct event bindings. If any target elements created dynamically (e.g., DataTable rows, form rows added via JS), the events won't fire. This is a known pattern for "button doesn't work after adding a new row" bugs.

---

## Financial Calculation Functions — Architecture

The purchase module has **4 core financial calculation chains**:

### Chain 1: Bill Entry
```
validateBillEntryForm()
  → calculate_purchase_item_cost() [1,603 lines]
    → calculate_grnItem_details() [723 lines]
      → AJAX save
```

### Chain 2: Purchase Return
```
calculate_purchase_return_item_cost() [1,335 lines]
  → calculate_purchase_return_final_cost() [1,103 lines]
    → AJAX save
```

### Chain 3: GRN Entry
```
validate_grn_entry_form()
  → calculate_grnItem_details() [723 lines]
    → AJAX save
```

### Chain 4: Approval Rate Fix
```
calculatedSelectedPosCost() [638 lines]
  → AJAX save
```

---

## Positive Findings

| Finding | Detail |
|---|---|
| ✅ No hardcoded URLs | All AJAX uses `base_url +` prefix |
| ✅ Strong NaN protection | 1,544 `parseFloat/parseInt/toFixed/isNaN` |
| ✅ Good event delegation | 112 delegated bindings for dynamic content |
| ✅ Error handlers on some AJAX | 72 of 185 have error callbacks |

---

## Summary — Round 7 Findings

| # | Finding | Count | Severity |
|---|---|---|---|
| 1 | **Mega-functions (>600 lines)** | 20 functions, 19,081 lines | 🟠 HIGH (maintenance) |
| 2 | **DataTable initializations** | 58 (risk of re-init) | 🟡 MEDIUM |
| 3 | **Direct event bindings on dynamic content** | 150 | 🟡 MEDIUM |
| 4 | **Deprecated Select2 API** | 10 | 🟡 MEDIUM |
| 5 | **Financial calc chains** | 4 chains, 4,264 core lines | ⚠️ AUDIT TARGET |

## Cumulative Bug Patterns (Rounds 2-7)
| Round | Patterns | Key Theme |
|---|---|---|
| R2 | 4 | Retagging method bugs |
| R4 | 8 | Security (debug, $_POST, raw SQL) |
| R5 | 4 | Validation gaps, error handlers |
| R6 | 5 | Access control, uploads, complexity |
| R7 | 5 | JS mega-functions, DataTable, events |
| **Total** | **26 patterns** |

---

## Bug Root Cause Register

### PUR-CLT02: Tax Calculation Mismatch (Hallmark Charges) ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| PUR-CLT02 | `other_charges_amount` (including charge tax) added to `item_cost` pre-tax base → cascading GST | Removed from pre-tax base; add charges AFTER GST computation | 2026-02-28 |

**Prevention**: Any "composite amount" variable (value + its own tax) must NEVER be included in another tax calculation's base. Tax bases must be computed independently, then summed for the final cost.

> **Note (PUR-CLT02)**: The `calculate_purchase_item_cost()` function has separate code paths for grn_type==1 (Supplier Bill) and grn_type==2 (Job Work Receipt). Both had the same bug pattern. The GRN entry (`calculate_grnItem_details()`) correctly handles this by keeping other charges separate — the bill entry function should always mirror GRN logic.
