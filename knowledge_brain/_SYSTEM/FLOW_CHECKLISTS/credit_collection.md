# Flow Checklist: Credit Collection (Bill Type 8)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_billing.php` → `billing()` L219 (bill_type=8)
> **Cancel:** `cancel_bill()` L7773 + type-8-specific logic L8107-8109

---

## What Makes Type 8 Different

Type 8 = Payment collection against a previously unpaid (credit) bill. Customer pays partial/full amount against an existing credit bill.

### Tables Touched
| Table | Action on Save | Action on Cancel |
|---|---|---|
| `ret_billing` | INSERT (bill_type=8, ref_bill_id=credit bill) | `bill_status → 2` |
| `ret_billing_payment` | INSERT payment(s) | Not reversed |
| Original `ret_billing.credit_status` | → 1 (paid) or recalculated | → 2 (reopen) L8109 |
| `ret_journal` | INSERT journal entries | NOT reversed |

---

## SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_billing` | INSERT (bill_type=8, ref_bill_id, net_amount=collection) | ⬜ |
| 2 | `ret_billing_payment` | INSERT payment record (cash/card/cheque/NB) | ⬜ |
| 3 | Original `ret_billing.credit_status` | Update to 1 (if fully paid) or partial | ⬜ |
| 4 | `ret_journal` | INSERT journal (debit cash, credit customer receivable) | ⬜ |
| 5 | Old metal collection? | Can old metal be applied against credit? | ⬜ **VERIFY** |
| 6 | Partial amount validation | Validate collection ≤ outstanding balance | ⬜ |

## CANCEL Checklist

| # | Table | Expected Reversal | Reversed? | Gap? |
|---|---|---|---|---|
| All | See `_cancel_master.md` | 20 shared steps | ✅ | — |
| CC1 | Original `ret_billing.credit_status → 2` | Reopen credit | ✅ L8107-8109 | — |
| CC2 | `ret_billing_payment` | Payment records NOT status-updated | ❌ | 🟡 MED |
| CC3 | `ret_journal` | NOT reversed | ❌ | 🔴 CRITICAL |
| CC4 | Partial payment history | Running total correct after cancel? | ⬜ | **VERIFY** |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| CC-001 | Credit status hardcoded to 2 on cancel — should recalculate if partial collections exist | 🔴 HIGH |
| CC-002 | Journal reversal missing | 🔴 CRITICAL |
| CC-003 | If multiple collections, cancelling one should recalculate balance, not just set to 2 | 🟡 MED |
