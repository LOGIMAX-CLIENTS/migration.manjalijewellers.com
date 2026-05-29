# Flow Checklist: Other Inventory

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_other_inventory.php`

---

## Overview

Other Inventory = non-jewelry items (packaging, carry bags, covers, accessories). Tracked separately from tag-based inventory.

## SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_other_inventory_purchase` | INSERT purchase header | ⬜ |
| 2 | `ret_other_inventory_purchase_items` | INSERT line items | ⬜ |
| 3 | `ret_other_inventory_purchase_items_details` | INSERT individual unit details | ⬜ |
| 4 | Stock quantity updated | Total pieces/weight updated | ⬜ |

## ISSUE AT BILLING

| # | Table | Action | Status |
|---|---|---|---|
| 1 | `ret_other_invnetory_issue` (note typo in table name) | INSERT issue link to bill | ⬜ |
| 2 | `ret_other_inventory_purchase_items_details.status → 1` | Mark issued | ⬜ |

## CANCEL REVERSAL

| # | Table | Reversed? |
|---|---|---|
| 1 | `ret_other_inventory_purchase_items_details.status → 0, id_inventory_issue → NULL` | ✅ cancel_bill L8168-8181 |
| 2 | `ret_other_inventory_purchase_items_log` deleted | ✅ L8185 |
| 3 | `ret_other_invnetory_issue` deleted | ✅ L8186 |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| OI-001 | Table name typo: `ret_other_invnetory_issue` (missing 'n') | 🟢 LOW |
| OI-002 | L8185 deletes by `id_inventory_issue` of LAST item only (loop bug) | 🔴 HIGH |
