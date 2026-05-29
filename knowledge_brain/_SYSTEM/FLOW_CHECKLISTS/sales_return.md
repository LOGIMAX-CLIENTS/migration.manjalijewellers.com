# Flow Checklist: Sales Return (Standalone — Bill Type 7)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_billing.php` → `billing()` L219 (bill_type=7)
> **Cancel:** `cancel_bill()` L7773 + type-7-specific logic L8115-8138

---

## What Makes Type 7 Different

Type 7 = standalone customer return. Customer returns purchased items without buying new ones. Amount is refunded.

### Tables Touched (additional to standard bill)
| Table | Action on Save | Action on Cancel |
|---|---|---|
| `ret_bill_details` | INSERT return items (ref to original bill detail) | Standard cancel restores |
| `ret_taging.tag_status → 0` | Returned tags become available | Tag re-sold if cancel? |
| `ret_billing.credit_status` (ref bill) | If ref bill was credit, recalculate balance | ✅ L8115-8138 |

---

## SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_billing` | INSERT (bill_type=7, ref_bill_id=original) | ⬜ |
| 2 | `ret_bill_details` | INSERT return line items | ⬜ |
| 3 | `ret_taging.tag_status → 0` | Returned tags freed | ⬜ |
| 4 | `ret_taging_status_log` | Log return event | ⬜ |
| 5 | `ret_billing_payment` | INSERT refund payment | ⬜ |
| 6 | `ret_nontag_item` (+) | Add back non-tag stock | ⬜ |
| 7 | `ret_nontag_item_log` | Log stock restore | ⬜ |
| 8 | `ret_journal` | INSERT journal (debit sales return, credit cash) | ⬜ |
| 9 | ref bill `credit_status` | If ref bill is credit → recalculate balance | ⬜ |

## CANCEL Checklist

| # | Table | Expected Reversal | Reversed? | Gap? |
|---|---|---|---|---|
| All | See `_cancel_master.md` | 20 shared steps | ✅ | — |
| SR1 | `ret_billing.credit_status` on ref bill | Recalculate (L8115-8138) | ✅ | — |
| SR2 | Returned tags | Should re-mark as tag_status=1 (sold) | ⬜ | ⚠️ **VERIFY** — cancel sets tag_status→0, NOT back to 1 |
| SR3 | `ret_nontag_item` | Cancel re-adds stock that was already added on return? | ⬜ | ⚠️ **DOUBLE ADD BUG?** |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| SR-001 | Cancel sets returned tag_status→0 (available) instead of reverting to 1 (sold) | 🟡 MED |
| SR-002 | NT stock may be double-added: return save adds stock, cancel adds stock again | 🔴 HIGH |
| SR-003 | Journal reversal missing (inherits from cancel_master) | 🔴 CRITICAL |
