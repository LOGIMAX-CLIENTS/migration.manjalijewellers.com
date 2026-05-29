# Cleanup Gaps — System-Wide Orphan Data & Missing Reversals

> Where delete/cancel operations leave orphaned records or incomplete state.
> Last updated: 2026-03-26 (Round 5 refresh)
> Total gaps: 26

---

## Critical Cleanup Gaps

| ID | Module | Operation | Orphaned Data | Severity |
|---|---|---|---|---|
| CLN-001 | Branch Transfer | Cancel after transit | `ret_taging.tag_status` stays 4, NT weight not reversed, bill flags not cleared, packaging status stuck | 🔴 CRITICAL |
| CLN-002 | Tagging | Tag delete (OTP) | `ret_taging_stone`, `ret_taging_material`, `ret_taging_images` — NOT cleaned on soft delete | 🟡 MED |
| CLN-003 | Tagging | Tag delete (OTP) | `ret_branch_transfer` records (if cross-branch tag) — NOT cleaned | 🔴 HIGH |
| CLN-004 | Tagging | Tag delete (OTP) | `ret_lot_inward_detail` balance — restored via separate OTP flow, not in delete case itself | ⚠️ Decoupled |
| CLN-005 | Tagging | Retag failure midway | Old tags stuck at `tag_status=3`, ghost lot created, `ret_acc_stock_process` orphaned | 🔴 HIGH |
| CLN-006 | Old Metal | Pocket delete (not implemented) | `ret_taging.tag_process=1` stays permanently, `is_pocketed=1` not cleared | 🟡 MED |
| CLN-007 | Estimation | Edit (re-save) | `ret_est_other_metals`, `ret_est_tag_merge`, `ret_est_sales_return_utilization`, `ret_estimation_other_inventory_issue` — MAY not be in delete list | ⚠️ UNVERIFIED |
| CLN-008 | Billing | Cancel bill | POS transaction NOT auto-reversed via `cancelTransactionRequest()` | 🔴 HIGH |
| CLN-009 | Purchase/Order | PO Cancel/Reject | `customerorder`, `customerorderdetails` | `order_cart.orderstatus` not reset back to `0` — cart items stuck at status `1` | ✅ FIXED (ORD-CLT01, 2026-03-26) |
| CLN-009 | Billing | Cancel bill | Wallet debit reversal — PARTIAL, not all wallet flows verified | 🟡 MED |
| CLN-010 | Customer Order | Email sent before commit | No cleanup — email already dispatched even if transaction rolls back | 🟡 MED |
| CLN-011 | Customer | Customer delete | Delete guard checks Billing, Orders, Estimation but NOT Chit/Scheme accounts | 🟡 MED |
| CLN-012 | Customer | Customer delete | `kyc` table records NOT deleted — orphaned KYC with PII (CUS-BUG-006) | 🔴 HIGH |
| CLN-013 | Payment | Payment delete | Hard delete on `payment` table without cleaning `payment_mode_details`, `payment_status` child records | 🔴 HIGH |
| CLN-014 | Payment | Payment delete | Delete has NO `trans_begin`/`trans_commit` — partial delete possible | 🔴 HIGH |
| CLN-015 | Customer Order | Cancel with advance | Zero-amount `ret_issue_receipt` row created on every cancel due to array-to-int truthy eval (AP-11) | 🟡 MED |
| CLN-016 | LOT | Lot delete | `ret_nontag_item` stock NOT decremented on lot deletion — permanent stock inflation (R-LOT-012) | 🔴 CRITICAL |
| CLN-017 | LOT | Lot delete | Child tables `ret_lot_inwards_detail`, `ret_lot_inwards_stone_detail`, `ret_lot_other_items`, `ret_lot_other_charges` NOT cleaned on delete | 🔴 HIGH |
| CLN-018 | Employee | Employee delete | `employee_devices`, `employee_settings`, `wallet_account`, customer FKs NOT cleaned on delete (EMP-BUG-016) | 🔴 HIGH |
| CLN-019 | Employee | Employee edit | Address table gets duplicate INSERT on every edit instead of UPDATE (EMP-BUG-003) | 🟡 MED |
| CLN-020 | Scheme | Scheme delete | Only deletes `scheme` + `gst_splitup_detail` — 9 other child tables orphaned (benefit, branch, topup, incentive, etc.) | 🔴 CRITICAL |
| CLN-021 | Estimation | Estimation save error | MyISAM tables (`ret_est_other_metals`, `ret_tag_other_metals`) NOT rolled back even when transaction fails | 🔴 HIGH |
| CLN-022 | Stock Issue | Issue receipt (partial) | Each tag processed in its own `trans_begin` — partial receipt leaves some tags issued while others returned | 🟡 MED |
| CLN-023 | Catalog | Product delete | `ret_product_section` NOT cleaned on product delete — orphaned section links | 🟡 MED |
| CLN-024 | Catalog | Category edit | Purities DELETE-then-INSERT — if re-insert fails, all purities lost | 🔴 HIGH |
| CLN-025 | Customer Order | Order update | Stones DELETE-then-INSERT in edit — if tx fails after delete, stones lost | 🔴 HIGH |
| CLN-026 | Branch Transfer | BT cancel | NO reversal of stock/logs for ANY item type (tags, NT, OldMetal, packaging, orders) — tags stay status=4 | 🔴 CRITICAL |

---

## Reversal Gap Summary By Module

| Module | Total Reversal Points | Fully Restored | Partial | Missing |
|---|---|---|---|---|
| Billing (cancel bill) | 13 | 7 | 4 | 2 (POS, wallet) |
| Tagging (tag delete) | 8 | 3 | 2 | 3 (stones, BT, materials) |
| Tagging (retag fail) | 4 | 1 | 1 | 2 (status rollback, ghost lot) |
| Estimation (edit) | 13 | 8 | 1 | 4 (unverified child tables) |
| Branch Transfer (cancel) | 8 | 1 | 0 | 7 (zero reversal) |
| Old Metal (pocket cancel) | 2 | 0 | 0 | 2 (tag_process, is_pocketed) |
| Customer (delete) | 4 | 2 | 0 | 2 (kyc, scheme guard) |
| Payment (delete) | 4 | 1 | 0 | 3 (mode_details, status, no txn) |
| Customer Order (cancel) | 3 | 2 | 0 | 1 (zero-amount receipt) |
| LOT (delete) | 6 | 0 | 0 | 6 (child tables, stock, tags) |
| Employee (delete) | 5 | 2 | 0 | 3 (devices, settings, wallet) |
| Scheme (delete) | 11 | 2 | 0 | 9 (child table groups) |
| Stock Issue (receipt) | 3 | 2 | 0 | 1 (partial receipt per-tag txn) |

---

## File System Cleanup

| Issue | Module | Impact |
|---|---|---|
| Tag images accumulate on delete | Tagging | Storage grows — `ret_taging_images` files not removed |
| `admin/log/` PII files | System | 31 log files web-accessible — no `.htaccess` |
| `../api/rate.txt` written as PHP array | Settings | Not standard JSON — hard to parse by external consumers |
| KYC upload dirs created with 0777 | Customer | World-writable directories (CUS-BUG-011) |
| Customer image dir not cleaned on delete | Customer | `admin/assets/img/customer/{id}/` persists after delete |
| Payment log files with full POST data | Payment | PII in `log/{date}/manual/` — card numbers, amounts |
