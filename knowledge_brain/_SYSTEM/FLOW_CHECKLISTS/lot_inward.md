# Flow Checklist: Lot Inward (Stock Entry Point)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_lot.php`
> **Model:** `ret_lot_model.php`
> **CRITICAL:** Lot Inward is the stock entry point — all tags are created from lots

---

## Overview

Lot Inward = Receiving stock from supplier. Creates lot header → lot details → triggers tag creation. This is the primary stock entry mechanism.

### Flow: Supplier → PO → GRN → Lot Inward → Tags
```
Purchase Order → GRN Receipt → Lot Inward (creates lot header + items)
                                    ↓
                              Tag Generation (ret_taging records)
```

---

## LOT CREATE / SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_lot_inwards` | INSERT header (lot_no, supplier, date, branch, total_wt, total_pcs) | ⬜ |
| 2 | `ret_lot_inwards_detail` | INSERT line items (product, design, wt, stone, count per design) | ⬜ |
| 3 | `ret_taging` | INSERT tags from lot details (one tag per piece) | ⬜ |
| 4 | `ret_taging_status_log` | Log creation event per tag | ⬜ |
| 5 | Lot number generation | Sequential per branch | ⬜ |
| 6 | PO linkage | Link to `ret_purchase_order` if from PO | ⬜ |
| 7 | `ret_tag_stone` | INSERT stone details per tag if applicable | ⬜ |
| 8 | Section assignment | Default section for new tags | ⬜ |

## LOT EDIT Checklist

| # | Item | Status |
|---|---|---|
| 1 | Can lot be edited after tags created? | ⬜ **VERIFY** |
| 2 | Weight/count changes | Update lot_detail + affected tags? | ⬜ |
| 3 | Status guard | Block edit if any tag sold/transferred | ⬜ **VERIFY** |

## LOT DELETE / CANCEL Checklist

| # | Table | Expected Reversal | Status |
|---|---|---|---|
| 1 | `ret_lot_inwards.status` | Soft delete or hard? | ⬜ **VERIFY** |
| 2 | `ret_lot_inwards_detail` | Delete/status line items | ⬜ |
| 3 | `ret_taging` | Delete tags created from lot? | ⬜ **VERIFY** |
| 4 | `ret_taging_status_log` | Log deletion | ⬜ |
| 5 | PO linkage | PO received qty reverted? | ⬜ |
| 6 | Guard | Cannot delete lot if tags sold/transferred | ⬜ **VERIFY** |

## LOT MERGE / SPLIT

| # | Operation | Status |
|---|---|---|
| 1 | Lot Merge | Combine two lots → tags re-assigned | ⬜ |
| 2 | Lot Split | Split lot into two → tags re-distributed | ⬜ |
| 3 | Merge/Split audit trail | Log events | ⬜ |

## Known Risks

| Risk | Description | Severity |
|---|---|---|
| LOT-001 | Delete without checking tag_status → orphan tags in reports | 🔴 HIGH |
| LOT-002 | Merge/Split may not update all tag references | 🟡 MED |
| LOT-003 | PO linkage not reverted on lot delete | 🟡 MED |
