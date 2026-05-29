# Flow Checklist: Gift Voucher (Issue / Redeem)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_gift_vocuher.php` (note typo in filename) + billing integration
> **Tables:** `ret_gift_voucher`, `ret_gift_voucher_trans`

---

## GIFT VOUCHER ISSUE (at billing)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_gift_voucher` | INSERT or UPDATE (voucher code, amount, expiry) | ⬜ |
| 2 | `ret_gift_voucher_trans` | INSERT issue transaction | ⬜ |
| 3 | Link to bill | `bill_id` reference on issue | ⬜ |
| 4 | Voucher code uniqueness | Check duplicate before issue | ⬜ |

## GIFT VOUCHER REDEEM (at billing)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_gift_voucher.balance` | Reduce by redeemed amount | ⬜ |
| 2 | `ret_gift_voucher_trans` | INSERT redeem transaction | ⬜ |
| 3 | Bill `net_amount` | Reduced by voucher amount | ⬜ |
| 4 | Expired check | Block redeem if past expiry | ⬜ **VERIFY** |
| 5 | Balance check | Redeem amount ≤ voucher balance | ⬜ **VERIFY** |

## CANCEL REVERSAL

| # | Table | Reversed? |
|---|---|---|
| 1 | Issued voucher cancelled | ✅ cancel_bill L7968 `get_gift_issue_details()` |
| 2 | Redeemed voucher reverted | ✅ cancel_bill L7970 `get_redeem_details()` |
| 3 | Voucher balance correct after cancel? | ⬜ **VERIFY** |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| GV-001 | Filename typo: `admin_gift_vocuher.php` | 🟢 LOW |
| GV-002 | Expiry check may be client-side only | 🟡 MED |
| GV-003 | Partial redeem + cancel: balance restore accuracy | 🟡 MED |
