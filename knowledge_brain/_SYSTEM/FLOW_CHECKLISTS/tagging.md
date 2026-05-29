# Flow Checklist: Tagging (Tag Create / Edit / Delete / Verify / OTP)

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_tagging.php`
> **Model:** `ret_tagging_model.php`
> **CRITICAL:** Tags are the FOUNDATION of the entire system — every billing flow depends on tag_status

---

## Overview

Tagging is the core inventory mechanism. Every piece of jewelry gets a unique tag with barcode. Tag status drives billing, transfers, returns, and all stock reports.

### Tag Status Values
| Value | Meaning | Set By |
|---|---|---|
| 0 | Available (in stock) | Create, Cancel sale, Cancel transfer |
| 1 | Sold | Sales billing |
| 2 | In Transit | Branch transfer send |
| 3 | Purchased (from supplier) | Purchase |
| 4 | Reserved (customer order) | Order assignment |
| 5 | Returned | Sales return |
| 6 | Cancelled (sale cancelled) | Cancel bill |
| 7 | Approval stock | Approval process |

---

## TAG CREATE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_taging` | INSERT (tag_id, barcode, lot_id, design, product, weight, stone details, purity, branch, section) | ⬜ |
| 2 | `ret_taging_status_log` | INSERT creation log | ⬜ |
| 3 | `ret_section_tag_status_log` | INSERT section assignment log | ⬜ |
| 4 | `ret_lot_inwards_detail` | Link tag to lot | ⬜ |
| 5 | Tag barcode uniqueness | Check duplicate barcode before insert | ⬜ **VERIFY** |
| 6 | Stone details | `ret_tag_stone` if stones present | ⬜ |
| 7 | Initial tag_status | Should be 0 (available) | ⬜ |

## TAG EDIT Checklist

| # | Item | Expected Action | Status |
|---|---|---|---|
| 1 | Weight change | Update `ret_taging` gross_wt, net_wt | ⬜ |
| 2 | Design change | Update design_id, sub_design_id | ⬜ |
| 3 | Status guard | Cannot edit sold/in-transit tags | ⬜ **VERIFY** |
| 4 | Stone update | Update `ret_tag_stone` | ⬜ |
| 5 | Section change | Update section + log | ⬜ |
| 6 | Audit log | Edit logged with old vs new values | ⬜ **VERIFY** |
| 7 | Retagging | Full retag creates new tag, marks old as retagged | ⬜ |

## TAG DELETE Checklist

| # | Table | Expected Reversal | Status |
|---|---|---|---|
| 1 | `ret_taging` | Soft delete (status change) or hard delete? | ⬜ **VERIFY** |
| 2 | `ret_taging_status_log` | Log deletion | ⬜ |
| 3 | `ret_lot_inwards_detail` | Unlink from lot | ⬜ |
| 4 | `ret_tag_stone` | Delete stone records | ⬜ |
| 5 | Status guard | Cannot delete sold/in-transit tags | ⬜ **VERIFY** |
| 6 | Reports impact | Deleted tag should not appear in stock reports | ⬜ |

## TAG VERIFY / OTP

| # | Item | Status |
|---|---|---|
| 1 | OTP verification for delete | Required? | ⬜ **VERIFY** |
| 2 | OTP verification for edit | Required for weight changes? | ⬜ **VERIFY** |
| 3 | OTP bypass | Settings-controlled or always required? | ⬜ |

## BARCODE / PRINT

| # | Item | Status |
|---|---|---|
| 1 | Barcode generation | Unique, sequential per lot | ⬜ |
| 2 | Duplicate print log | `ret_tag_print_log` tracks reprints | ⬜ |
| 3 | Tag format | Product-design-serial pattern | ⬜ |

## Known Risks

| Risk | Description | Severity |
|---|---|---|
| TAG-001 | Tag_status transitions not guarded — can sell an already-sold tag via direct POST | 🔴 CRITICAL |
| TAG-002 | Delete may not check for pending transactions (order, transfer) | 🟡 MED |
| TAG-003 | Bulk tag import (`file_upload_tags`) may bypass validations | 🟡 MED |
| TAG-004 | Retagging flow complexity — old tag marked but reports may still reference | 🟡 MED |
