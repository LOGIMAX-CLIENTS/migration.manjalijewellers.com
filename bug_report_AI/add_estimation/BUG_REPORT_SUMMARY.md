# 🐛 Add Estimation — Bug Report Summary

> **Module**: Estimation → Add Estimation  
> **Audit Date**: 2026-02-17  
> **Auditor**: AI Static Analysis  
> **KB Coverage**: ~40% (18 KB files reviewed)

---

## Module Snapshot

| Metric | Value |
|---|---|
| Controller | `admin_ret_estimation.php` (3,574 lines, 64 methods) |
| Model | `ret_estimation_model.php` (3,006 lines, 112 methods) |
| JS | `ret_estimation.js` (31,391 lines, 513 functions) |
| View | `estimation/form.php` (136 KB) |
| DB Tables | `ret_estimation`, `ret_estimation_items`, `ret_estimation_item_stones`, `ret_estimation_old_metal_sale_details`, `ret_esti_old_metal_stone_details`, `ret_est_chit_utilization`, `ret_est_gift_voucher_details`, `ret_est_other_metals`, `ret_estimation_other_charges`, `ret_estimation_item_other_materials`, `ret_est_tag_merge`, `ret_est_sales_return_utilization`, `ret_estimation_other_inventory_issue` |
| KB Files Used | `business_rules.md`, `edge_cases_and_errors.md`, `estimation_module_kb.md`, `workflow_03_save_estimation.md` |

---

## Prioritized Bug List

### P0 — Critical (Data Corruption / Financial Loss)

| ID | Title | Classification | Confidence |
|---|---|---|---|
| EST-001 | Gift voucher save uses undefined variable `$arrayMaterials` — crashes silently, vouchers never persisted | Functional | **High** |
| EST-002 | Custom item `market_rate_tax` stores `market_rate_cost` value — wrong tax in DB | Calculation | **High** |

### P1 — Major Functionality Broken

| ID | Title | Classification | Confidence |
|---|---|---|---|
| EST-003 | Delete estimation leaves orphan records in `ret_est_other_metals`, `ret_estimation_other_charges`, `ret_estimation_item_other_materials`, `ret_est_tag_merge`, `ret_est_sales_return_utilization`, `ret_estimation_other_inventory_issue` | Data Integrity | **High** |
| EST-004 | Update (edit) estimation uses delete-then-reinsert without `form_secret` — allows double-submit & data loss on concurrent edit | Concurrency | **High** |
| EST-005 | Child tag stone details use wrong index `[$key]` — stones never saved for merged child tags | Functional | **High** |
| EST-006 | Update path does NOT delete `ret_est_other_metals` or `ret_estimation_other_charges` before re-insert — duplicate charge/metal rows accumulate on every edit | Data Integrity | **High** |
| EST-007 | Update path missing `rate_per_gram` field for chit utilization — savings calculation data loss on edit | Data Integrity | **Medium** |

### P2 — Minor / Edge Case

| ID | Title | Classification | Confidence |
|---|---|---|---|
| EST-008 | `est_date` uses `date($estimation_datetime)` which silently produces wrong format when datetime contains time component | Functional | **Medium** |
| EST-009 | Order items save with `net_wt = gross_wt` always — ignores actual net weight if stone/less weight exists | Calculation | **Medium** |
| EST-010 | Custom item charges loop only captures LAST `id_charge` — prior charges silently discarded | Functional | **Medium** |
| EST-011 | Catalog item stone save (line 830-836) missing `uom_id` and `quality_id` fields compared to tag item stones | Data Integrity | **Low** |
| EST-012 | Update path custom item `diamond_amount` uses `tot_dia_amt` key vs save path uses `diamond_amt` — inconsistent field name causes zero value on one path | Data Integrity | **Medium** |

### P3 — Cosmetic / Rare

| ID | Title | Classification | Confidence |
|---|---|---|---|
| EST-013 | `update_status` case references undefined `$status` variable and redirects to wrong module (`admin_ret_lot`) | Functional | **High** |
| EST-014 | Debug `print_r` / `exit` statements left commented in production code (20+ instances) | Logging | **Low** |

---

## Quick Summary Table

| ID | Title | Severity | Classification | Confidence |
|---|---|---|---|---|
| EST-001 | Gift voucher undefined variable crash | P0 | Functional | High |
| EST-002 | Custom item market_rate_tax stores wrong value | P0 | Calculation | High |
| EST-003 | Delete leaves orphan records in 6 tables | P1 | Data Integrity | High |
| EST-004 | No form_secret on save/update — double-submit risk | P1 | Concurrency | High |
| EST-005 | Child tag stones never saved — wrong index | P1 | Functional | High |
| EST-006 | Update path missing deletes — duplicate charges | P1 | Data Integrity | High |
| EST-007 | Update chit missing rate_per_gram field | P1 | Data Integrity | Medium |
| EST-008 | est_date format bug with date() function | P2 | Functional | Medium |
| EST-009 | Order items net_wt always equals gross_wt | P2 | Calculation | Medium |
| EST-010 | Custom charges loop only keeps last charge | P2 | Functional | Medium |
| EST-011 | Catalog stones missing uom_id and quality_id | P2 | Data Integrity | Low |
| EST-012 | Diamond amount field name mismatch save vs update | P2 | Data Integrity | Medium |
| EST-013 | update_status references undefined variable | P3 | Functional | High |
| EST-014 | Debug statements in production code | P3 | Logging | Low |
