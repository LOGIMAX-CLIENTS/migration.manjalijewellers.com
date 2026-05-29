# Flow Checklist: Supplier Sales Bill (Type 12)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_billing.php` → `billing()` (bill_type=12)
> **Cancel:** `cancel_bill()` (shared)

---

## Overview

Type 12 = Sale to supplier (reverse of normal sale — jeweler sells to another supplier).

## SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | All standard billing steps | See `sales_bill.md` | ⬜ |
| 2 | Supplier reference | Link to supplier instead of customer | ⬜ |
| 3 | tag_status | Tags sold to supplier → status change | ⬜ |

## CANCEL

| # | Expected | Status |
|---|---|---|
| 1 | All shared cancel steps | See `_cancel_master.md` | ✅ |
| 2 | Journal reversed | ❌ Missing |
