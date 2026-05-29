# BIL-CLT01 — Bill Discount Applies to Items Without MC/VA Values

| Field             | Value                                |
| ----------------- | ------------------------------------ |
| **Severity**      | P1 (Major) — ✅ **Fixed 2026-02-25** |
| **Track**         | B (Business)                         |
| **Category**      | Logic                                |
| **Sprint**        | Sprint 1                             |
| **Pattern Match** | None — Novel business logic bug      |
| **Module Brain**  | ✅ Ready                             |
| **Reporter**      | Client (Internal)                    |
| **Source**        | Client                               |

---

## Business Rule (Violated)

> Bill discount (when `bill_discount_type = 2` — "Apply in V.A & M.C") should ONLY be distributed to line items that have Making Charges (MC) or Value Added (VA/Wastage) values greater than zero. Items with MC=0 AND VA=0 should receive **zero discount**. If the discount amount exceeds the total MC+VA of an eligible item, the excess should be stored in `item_blc_discount`.

---

## Steps to Reproduce

1. Open Billing → New Sale Bill
2. Add **Item A** with MC = ₹500, VA = ₹200 (has value added)
3. Add **Item B** with MC = 0, VA = 0 (plain gold/silver — no value added)
4. Enter a bill discount amount (e.g., ₹300) in `#summary_discount_amt`
5. Set `bill_discount_type = 2` (Apply in V.A & M.C)
6. Observe the discount distribution per row

## Expected Behavior

- **Item A**: Gets discount of ₹300 (applied against MC=₹500 first, then VA if needed)
- **Item B**: Gets **₹0 discount** (has no MC or VA to absorb discount)
- Discount ratio should be based on `total_mc_va_amt` (sum of MC+VA of eligible items only), NOT `total_sales_amt`

## Actual Behavior

- **Item A**: Gets discount proportional to its `rate_with_mc / total_sales_amt`
- **Item B**: **Also gets discount** proportional to its `rate_with_mc / total_sales_amt` — even though it has MC=0 and VA=0
- The discount is spread across ALL items based on sale value ratio, not limited to items with value-added components

---

## Root Cause

**File**: `admin/assets/js/ret_billing.js`
**Function**: Per-row calculation function (inside `calculateSaleBillRowTotal` loop)
**Lines**: L8582–L8587

```javascript
// L8585 — BUG: disc_per is based on total_sales_amt (ALL items)
var disc_per = parseFloat((disc_amt / total_sales_amt) * 100);

// L8587 — Every row gets discount regardless of MC/VA values
var discount = parseFloat((rate_with_mc * disc_per) / 100);
```

The discount percentage is calculated using `total_sales_amt` which includes ALL items. This ratio is then applied to EVERY row's `rate_with_mc`. The `bill_discount_type == 2` block at L8597 correctly handles the MC/VA waterfall (absorbs discount from MC first, then VA, then `item_blc_discount`), but by that point the damage is done — items with MC=0 and VA=0 already received a non-zero `discount` value.

### Correct Approach

When `bill_discount_type == 2`:

1. **First pass**: Calculate `total_eligible_amt` = sum of `rate_with_mc` for items WHERE `mc_type > 0 OR wast_wgt_amt > 0`
2. **Ratio**: `disc_per = disc_amt / total_eligible_amt * 100` (only eligible items)
3. **Per-row**: If `mc_type == 0 AND wast_wgt_amt == 0` → skip discount (set `discount = 0`)
4. **Overflow**: If `discount > mc_type + wast_wgt_amt` → store excess in `item_blc_discount`

---

## Impact Assessment

- **Financial**: Discount is incorrectly deducted from items that should not receive it, reducing `rate_with_mc` and therefore the item cost. This affects:
    - Per-item sale value in `ret_bill_details.item_cost`
    - Per-item tax calculation (lower base → lower tax)
    - Bill total amount
    - Reports and financial statements
- **Scope**: All bills where `bill_discount_type = 2` AND there's a mix of items with/without MC/VA values
- **Data already saved**: Existing bills may have incorrect discount distribution

---

## Evidence

From JS source (`ret_billing.js`):

```javascript
// L8091 — Settings that control discount behavior:
var bill_discount_type = $("#bill_discount_type").val(); // 1=General, 2=Apply in V.A & M.C
var bill_discount_apply_on = $("#bill_discount_apply_on").val(); // 1=VA, 2=MC

// L8107 — item_blc_discount is declared but only used inside the type==2 block:
var item_blc_discount = 0;

// L8582–8587 — The ratio is applied to ALL rows unconditionally:
if (disc_amt > 0) {
    var disc_per = parseFloat((disc_amt / total_sales_amt) * 100); // ← BUG: uses total_sales_amt
    var discount = parseFloat((rate_with_mc * disc_per) / 100); // ← APPLIED TO ALL ROWS
    rate_with_mc = parseFloat(rate_with_mc - discount).toFixed(2);
}
```

---

## Related Fields

| Column                   | Table              | Description                                            |
| ------------------------ | ------------------ | ------------------------------------------------------ |
| `bill_discount`          | `ret_bill_details` | Per-item discount amount (currently set for ALL items) |
| `mc_discount`            | `ret_bill_details` | MC portion of discount                                 |
| `wastage_discount`       | `ret_bill_details` | VA/wastage portion of discount                         |
| `item_blc_discount`      | `ret_bill_details` | Balance discount (excess beyond MC+VA)                 |
| `bill_discount_type`     | `ret_settings`     | 1=General, 2=Apply in V.A & M.C                        |
| `bill_discount_apply_on` | `ret_settings`     | 1=VA first, 2=MC first                                 |
