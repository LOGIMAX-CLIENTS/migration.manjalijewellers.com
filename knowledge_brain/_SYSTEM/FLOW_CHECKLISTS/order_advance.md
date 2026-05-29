# Flow Checklist: Order Advance (Bill Type 5)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_billing.php` → `billing()` (bill_type=5)
> **Cancel:** `cancel_bill()` L8142-8154 — rate type revert

---

## SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_billing` | INSERT (bill_type=5, customer order reference) | ⬜ |
| 2 | `ret_billing_advance` | INSERT advance record (amount, bill_adv_id) | ⬜ |
| 3 | `ret_billing_payment` | INSERT payment record | ⬜ |
| 4 | `customerorderdetails.orderstatus` | Update order status (advance received) | ⬜ |
| 5 | Rate type update | Lock rate for order at current rate | ⬜ |
| 6 | `ret_journal` | INSERT journal entry | ⬜ |

## CANCEL Checklist

| # | Expected Reversal | Reversed? |
|---|---|---|
| All shared steps | See `_cancel_master.md` | ✅ |
| Rate type revert | `update_order_rate_type()` L8150 | ✅ |
| Order status | Back to pre-advance state | ⬜ **VERIFY** |
| Advance record | `ret_billing_advance` reverted? | ⬜ — Not in cancel_bill for type 5 |

## Known Bugs

| Bug | Severity |
|---|---|
| `ret_billing_advance` NOT explicitly reverted for type 5 cancel | 🟡 MED |
| Journal reversal missing | 🔴 CRITICAL |
