# Flow Checklist: Sales Return Exchange (Bill Type 3)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_billing.php` → `billing()` L219 (bill_type=3)
> **Cancel:** `cancel_bill()` L7773 (shared flow — see `_cancel_master.md`)

---

## What Makes Type 3 Different

Type 3 = Customer returns tag(s) from a previous bill and buys new items. The return value is deducted from the new bill.

### Additional Tables (beyond standard Sales Bill)
| Table | Action on Save | Action on Cancel |
|---|---|---|
| `ret_bill_details` (return items) | INSERT with `status=5` (return items linked to new bill) | `status → 1` (L7957-7964) |
| Previous bill tags | `tag_status → 5` (returned) | Tag status should revert |

---

## SAVE Checklist (Type 3 additions over Type 1)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | All Type 1 steps | See `sales_bill.md` | ✅ |
| 2 | `ret_bill_details` (return items) | INSERT — linked to original bill, status=5 | ⬜ |
| 3 | `ret_taging.tag_status` (returned) | Original sale tags → status=5 | ⬜ |
| 4 | Return amount | Deducted from new bill `net_amount` | ⬜ |
| 5 | `ret_taging_status_log` | Log return event for original tags | ⬜ |

## CANCEL Checklist (Type 3)

| # | Table | Expected Reversal | Reversed? | Gap? |
|---|---|---|---|---|
| All | See `_cancel_master.md` | 20 shared steps | ✅ | See master gaps |
| RT1 | `ret_bill_details.status → 1` | Restore return item status | ✅ L7957-7964 | — |
| RT2 | Original returned tag_status | Should revert from 5 back to sold(1) | ⬜ | ⚠️ **VERIFY** |
| RT3 | New tags (sold in this bill) | `tag_status → 0` via standard cancel | ✅ L7838 | — |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| RTEX-001 | Original returned tag_status may not revert on cancel (verify) | 🟡 MED |
| RTEX-002 | Journal reversal missing (inherits from cancel_master) | 🔴 CRITICAL |
