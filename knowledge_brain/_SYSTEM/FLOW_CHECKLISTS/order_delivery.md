# Flow Checklist: Order Delivery (Bill Type 9)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_billing.php` → `billing()` (bill_type=9)
> **Cancel:** `cancel_bill()` L8068-8100 — order + advance revert

---

## SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | All standard billing steps | See `sales_bill.md` | ✅ |
| 2 | `customerorderdetails.orderstatus` | Update to delivered | ⬜ |
| 3 | `ret_billing_advance.is_adavnce_adjusted → 1` | Mark advance as adjusted | ⬜ |
| 4 | Advance deduction | Net amount reduced by advance paid | ⬜ |
| 5 | Balance calculation | bill_total - advance = payable | ⬜ |

## CANCEL Checklist

| # | Expected Reversal | Reversed? |
|---|---|---|
| All shared steps | See `_cancel_master.md` | ✅ |
| `customerorderdetails.orderstatus → 4` | ✅ L8078 |
| `ret_billing_advance.is_adavnce_adjusted → 0` | ✅ L8095 |
| Advance balance recalculated | ✅ L8093 |
| Journal reversal | ❌ CRITICAL gap |

## Known Bugs

| Bug | Severity |
|---|---|
| Journal reversal missing | 🔴 CRITICAL |
| Typo: `is_adavnce_adjusted` (advance misspelled in DB column) | 🟢 LOW |
