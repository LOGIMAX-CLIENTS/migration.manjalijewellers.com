# Flow Checklist: Bill Cancel — Complete Reversal Map (All Bill Types)

> **Last Updated:** 2026-03-27
> **Source:** `admin_ret_billing.php` → `cancel_bill()` L7773-8200
> **Applies to:** ALL bill types (1-12). This is the shared cancel logic.

---

## Cancel Flow Trace: `cancel_bill()` L7773-8200

### Step-by-step reversal:

| # | Line | Table/Action | What It Does | All Types? |
|---|---|---|---|---|
| 1 | L7785-7801 | `ret_billing.bill_status → 2` | Mark bill as cancelled | ✅ ALL |
| 2 | L7807-7809 | `ret_issue_receipt.bill_status → 2` | Cancel linked issue/receipt | ✅ ALL |
| 3 | L7828 | `ret_estimation.estbillid → NULL` | Unlink estimation from bill | ✅ ALL |
| 4 | L7832-7834 | `ret_estimation_items.purchase_status → 0, bil_detail_id → NULL` | Free estimation items | ✅ ALL |
| 5 | L7838-7890 | `ret_taging.tag_status → 0` + status log + section log | Free sold tags (tagged items only, item_type=0) | ✅ ALL |
| 6 | L7892-7947 | `ret_nontag_item` (+) + `ret_nontag_item_log` | Restore NT stock (add back) | ✅ ALL |
| 7 | L7953-7955 | `ret_estimation_old_metal_sale_details.purchase_status → 0, bill_id → NULL` | Free old metal est details | ✅ ALL |
| 8 | L7957-7964 | `ret_bill_details.status → 1` (return items) | Restore return bill detail status | ✅ ALL |
| 9 | L7968 | Gift voucher issued → cancel | Call `get_gift_issue_details()` | ✅ ALL |
| 10 | L7970 | Gift voucher redeemed → revert | Call `get_redeem_details()` | ✅ ALL |
| 11 | L7976-7983 | `scheme_account.is_utilized → 0, utilized_type → NULL` | Revert chit utilization | ✅ ALL |
| 12 | L7987-8018 | `wallet_transaction` INSERT (debit reversal) | Reverse green tag wallet incentive | ✅ ALL |
| 13 | L8024-8034 | `payment.payment_status → 4` | Revert chit deposit payment (if make_as_advance=2) | Conditional |
| 14 | L8042-8050 | `customerorderdetails.orderstatus → 4` | Revert repair order (bill_type=11 only) | Type 11 |
| 15 | L8068-8100 | `customerorderdetails.orderstatus → 4` + `ret_billing_advance` revert | Revert order delivery (bill_type=9 only) | Type 9 |
| 16 | L8107-8109 | `ret_billing.credit_status → 2` (ref bill) | Revert credit collection (bill_type=8 only) | Type 8 |
| 17 | L8115-8138 | Recalculate credit balance | Revert sales return against credit (bill_type=7 with ref_bill) | Type 7 |
| 18 | L8142-8154 | `update_order_rate_type()` | Revert order advance rate type (bill_type=5 only) | Type 5 |
| 19 | L8162-8187 | `ret_other_inventory_purchase_items_details` + issue log + issue table | Revert other inventory items | ✅ ALL |
| 20 | L8189 | `trans_complete()` | Commit transaction | ✅ ALL |

---

## What's MISSING from cancel_bill() (Known Gaps)

| # | Missing Step | Impact | Severity |
|---|---|---|---|
| 1 | **POS transaction reversal** (`ret_bill_pay_device`) | POS device payment entries NOT reversed | 🔴 HIGH |
| 2 | **Journal reversal** (`ret_journal`) | Accounting journal entries NOT reversed | 🔴 CRITICAL |
| 3 | **Section NT item log** (`ret_section_nontag_item_log`) | NT stock log at section level NOT updated | 🟡 MED |
| 4 | **Bill advance table** (`ret_billing_advance`) | Advance adjustment NOT reversed for non-type-9 | 🟡 MED |
| 5 | **Old metal purchase reversal** (`ret_bill_old_metal_sale_details`) | Old metal rows NOT deleted/reversed | 🟡 MED |
| 6 | **Billing payment reversal** (`ret_billing_payment`) | Payment rows NOT status-updated | 🟡 MED |
| 7 | **Credit sale status** on regular cancel | Credit bill ref NOT updated for plain cancel | 🟡 MED |
| 8 | `trans_complete()` used instead of `trans_commit()` | CI2 `trans_complete()` auto-commits regardless of errors | ⚠️ VERIFY |

---

## Per-Bill-Type Cancel Coverage

| Bill Type | Type # | Steps Applied | Type-Specific Steps | Coverage |
|---|---|---|---|---|
| Sales Bill | 1 | 1-12, 19 | None | ~70% |
| Sales + Old Metal | 2 | 1-12, 19 | None (old metal NOT specifically reversed) | ~65% |
| Sales + Return | 3 | 1-12, 19 | None | ~70% |
| Purchase | 4 | 1-12, 19 | None | ~70% |
| Order Advance | 5 | 1-12, 18, 19 | Rate type revert | ~75% |
| Sales Return | 7 | 1-12, 17, 19 | Credit balance recalc | ~75% |
| Credit Collection | 8 | 1-12, 16, 19 | Reopen credit status | ~75% |
| Order Delivery | 9 | 1-12, 15, 19 | Order status + advance revert | ~80% |
| Chit Pre-Close | 10 | 1-12, 19 | None | ~70% |
| Repair Delivery | 11 | 1-12, 14, 19 | Order status → 4 | ~75% |
| Supplier Sales | 12 | 1-12, 19 | None | ~70% |

> **Average cancel coverage: ~72%** — Journal reversal (CRITICAL) is missing across ALL bill types.
