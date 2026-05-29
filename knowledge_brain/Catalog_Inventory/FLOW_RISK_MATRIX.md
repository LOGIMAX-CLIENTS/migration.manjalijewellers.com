# Catalog_Inventory — FLOW RISK MATRIX

> **Built**: 2026-03-25 | **Round**: 11 (updated) | **Source**: DATA_FLOW.md (R5) + CROSS_MODULE_MAP.md (R9) + Live code scan (R11)
> **Builder**: Antigravity AI

---

## 3b-1. State Machines

### State Machine 1: `ret_product_master`.`product_status`

| State | Value | Set By (Module.Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Active | 1 | Catalog.`ret_product()` save/update | Inactive(0) | — |
| Inactive | 0 | Catalog.`ret_product()` update / `bulkprodupdated()` update | Active(1) | — |
| Deleted | (row removed) | Catalog.`ret_product()` delete | — (terminal) | `getItemsinTagDetails()` — blocks if stock exists |

> ⚠️ No formal "Discontinued" or "Archived" state — delete is hard-delete with only a stock-existence guard. **NO GUARD** on whether product is referenced in active estimations or sales orders.

---

### State Machine 2: `ret_karigar`.`approved_status` (Karigar Approval)

| State | Value | Set By (Module.Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Pending | 0 | Catalog.`karigar()` save — initial creation | Approved(1), Rejected(2) | — |
| Approved | 1 | Catalog.`karigar_approval()` save | — | OTP verification required if `vendor_approval_otp_req = 1` |
| Rejected | 2 | Catalog.`karigar_approval()` save | Pending(0) — resubmit | — |

> ⚠️ OTP check is **CONDITIONAL** — only enforced if `profile.vendor_approval_otp_req = 1`. If disabled, approval happens with no verification. **PARTIAL GUARD**.

---

### State Machine 3: `ret_financial_year`.`fin_status`

| State | Value | Set By (Module.Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Inactive | 0 | Catalog.`financial_status()` — deactivates ALL first | Active(1) | — |
| Active | 1 | Catalog.`financial_status()` — then activates selected | Inactive(0) | — |

> ⚠️ **CRITICAL GAP**: `financial_status()` (L9628) is NOT wrapped in a transaction (`trans_begin`/`trans_commit` missing). If the deactivate-all step succeeds but the activate-selected step fails → ALL financial years become inactive. **NO GUARD, NO TRANSACTION PROTECTION**.

---

## 3b-2. Inbound Contracts

> Catalog_Inventory is a **master data provider** — it has FEW inbound dependencies. Most inbound data comes via user input forms, not from other modules.

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| Settings (`profile`) | `vendor_approval_otp_req` flag | YES — `get_profile_settings()` reads flag | L203 (model) | If flag missing → OTP silently skipped, approval without verification |
| Settings (`chit_settings` / `ret_settings`) | App config values | YES — `get_ret_settings()` reads | Various | Incorrect config could break all entity forms |
| Tagging (`ret_taging`) | Tag records with product/category/purity/stone reference | YES — `getItemsinTagDetails()` delete guard | L3555 ctrl | Without check → deleting master data with live stock tags |
| Branch system (`branch`) | Valid branch IDs for branch-filtered views | NO explicit validation | — | Invalid branch ID causes silent filter failure |
| Payment system (`payment`, `payment_mode`) | Active payment modes for bank deposit | NO validation | — | Bank deposit could reference archived payment mode |

---

## 3b-3. Outbound Contracts

> As master data hub, Catalog_Inventory guarantees that downstream modules can read stable, active master data.

| Downstream Module | What This Module Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| Tagging | `ret_product_master` records are valid and active (`product_status=1`) | NO explicit check before Tagging reads | Tag created on inactive product — UI inconsistency |
| Tagging | `ret_category` records exist before tag detail references them | FK constraint only | Tag references deleted category — orphan data |
| Billing | `ret_taxgroupmaster` + `ret_taxgroupitems` are complete (all items sum correctly) | NO server-side sum validation | Bill calculates wrong tax |
| Billing | `ret_purity` records are valid | FK constraint only | Billing references deleted purity |
| Estimation | `ret_product_master` config fields (wastage_type, stone_type, sales_mode) are set | NO completeness check | Estimation calculation uses NULL or default — wrong pricing |
| Purchase | `ret_karigar` records are approved | NO check at Purchase side | PO issued to unapproved karigar |
| Reports | All master tables are consistent | No cross-module contract | Reports show stale/wrong labels |
| All modules | `ret_financial_year` has exactly 1 active year | `setFinancialYearStatus()` deactivates-all-then-activates-one | If transaction gap → 0 active years → financial year lookups fail system-wide |

---

## 3b-4. Reversal Contracts

### Entity CREATE → DELETE Restoration

| Operation | Tables Written at CREATE | Actually Cleaned at DELETE | Method & Line | Gap? |
|---|---|---|---|---|
| Category CREATE | `ret_category` (1) + `ret_metal_cat_purity` (N) | `ret_category` only | `category('delete')` L3555 | ⚠️ `ret_metal_cat_purity` orphans remain |
| Category UPDATE | `ret_category` + DELETE+INSERT `ret_metal_cat_purity` | N/A (update, not delete) | — | — |
| Product CREATE | `ret_product_master` + `ret_product_section` (N) + `ret_product_charges` (N) | `ret_product_master` + `ret_product_charges` | `ret_product('delete')` L6375 | ⚠️ `ret_product_section` NOT deleted → orphan rows |
| Design CREATE | `ret_design_master` + 5 child tables (`ret_design_karigars`, `ret_design_purity`, `ret_design_other_materials`, `ret_design_sizes`, `ret_design_stone`) | `ret_design_master` ONLY | `ret_design('delete')` **L8197-8253** ✅ Verified | ❌ **ALL 5 child tables orphaned** — no child cleanup in delete handler |
| Karigar CREATE | `ret_karigar` + 5 child tables (wastage/stones/charges/kyc/bank/products) | `ret_karigar` + `ret_karikar_items_wastage` + `ret_karigar_kyc` | `karigar('delete')` **L4625-4667** ✅ Verified | ⚠️ **PARTIAL** — `ret_karigar_stones`, `ret_karigar_charges`, `ret_karigar_bank_acc_details`, `ret_karigar_products` NOT cleaned |
| Tax Group CREATE | `ret_taxgroupmaster` (header) + `ret_taxgroupitems` (N items) | `ret_taxgroupmaster` ONLY | `tgrp('delete')` **L7334-7378** ✅ Verified | ❌ `ret_taxgroupitems` orphaned — FK child rows remain after parent delete |
| Purity/Color/Cut/Clarity CREATE | `ret_{entity}` only | `ret_{entity}` only (with stock guard) | `{entity}('Delete')` | ✅ Complete |

---

## 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-CAT-001 | Delete category that has tags in `ret_taging` | REJECT with error message | 🔴 HIGH | ❌ |
| FR-CAT-002 | Delete category with NO stock — verify `ret_metal_cat_purity` cleanup | Orphan rows should be cleaned up (currently they are NOT) | 🔴 HIGH | ❌ |
| FR-CAT-003 | Delete product with NO stock — verify `ret_product_section` cleanup | Orphan rows should be cleaned up (currently they are NOT) | 🔴 HIGH | ❌ |
| FR-CAT-004 | Update category purities — verify DELETE-then-INSERT atomicity | If INSERT fails mid-loop, all purities lost | 🔴 HIGH | ❌ |
| FR-CAT-005 | Financial year toggle when second UPDATE fails | ALL financial years should remain at prior state (currently: all go inactive) | 🔴 HIGH | ❌ |
| FR-CAT-006 | Approve karigar WITHOUT OTP when `vendor_approval_otp_req=0` | Approval succeeds — expected behavior | 🟡 MED | ❌ |
| FR-CAT-007 | Approve karigar WITH OTP — verify OTP is consumed | Should block re-use of same OTP | 🟡 MED | ❌ |
| FR-CAT-008 | Delete product that is referenced in active Estimation | Should REJECT (currently may allow — no estimation guard) | 🔴 HIGH | ❌ |
| FR-CAT-009 | Save design with `fixed_rate` duplicate key (L7867-7869) | Second value silently overwrites first | 🟡 MED | ❌ |
| FR-CAT-010 | Bulk product update — simulate crash mid-loop | Partial update should fully rollback | 🟡 MED | ❌ |
| FR-CAT-011 | Status toggle on purity/color/cut/clarity via GET | CSRF: unauthenticated GET can toggle status | 🔴 HIGH | ❌ |
| FR-CAT-012 | Karigar CREATE → DELETE → verify all 5 child tables cleaned | All child rows removed (currently: stones/charges/bank/products are NOT removed) | 🔴 HIGH | ❌ |
| FR-CAT-013 | Tax group DELETE → verify `ret_taxgroupitems` cleaned | No orphan items (currently: items are NOT deleted) | 🔴 HIGH | ❌ |
| FR-CAT-014 | Concurrent purity edit by 2 users — last-write-wins? | No conflict detection exists → data loss risk | 🟡 MED | ❌ |
| FR-CAT-015 | Admin_settings access control bypass — direct URL access | Should redirect to login or access denied | 🔴 HIGH | ❌ |
| FR-CAT-016 | Design CREATE → DELETE → verify `ret_design_karigars`, `ret_design_purity`, `ret_design_other_materials`, `ret_design_sizes`, `ret_design_stone` cleaned | All 5 child tables cleaned (currently: NONE are cleaned) | 🔴 HIGH | ❌ |
| FR-CAT-017 | Delete karigar → verify `ret_karigar_stones` and `ret_karigar_charges` are removed | Should be cleaned (currently: they are NOT) | 🔴 HIGH | ❌ |
| FR-CAT-018 | Delete tax group → verify child `ret_taxgroupitems` are removed | Should be cleaned (currently: they are NOT) | 🔴 HIGH | ❌ |
