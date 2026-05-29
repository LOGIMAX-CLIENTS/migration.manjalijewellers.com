# Scheme (Master) — Module Brain
> **Module**: Scheme Master Management  
> **Controller**: `admin_scheme.php` (1,241 lines)  
> **Model**: `scheme_model.php` (854 lines)  
> **Round**: 1 | **Date**: 2026-03-06  

---

## 1. Module Overview

The Scheme module is a **configuration/master data module** that defines the rules and settings for all savings schemes. It does NOT handle customer accounts or payments — those are in the Account and Payment modules. This module manages:

- Scheme CRUD (create, edit, delete, list)
- Scheme type definitions (Amount, Weight, Amount-to-Weight, Flexible)
- Interest/benefit charts, deduction charts, pre-close settings
- GST split-up configuration per scheme
- Branch-wise scheme mapping
- Agent/employee/customer referral incentive slab settings
- Employee closing incentive charts
- General Advance (GA) benefit charts
- DigiGold configuration
- Top-up scheme payable settings
- Lucky draw / prize settings
- Lump sum weight slabs
- Flexible installment range settings
- Metal/purity/classification master lookups

---

## 2. File Map

| File | Lines | Purpose |
|---|---|---|
| `admin/application/controllers/admin_scheme.php` | 1,241 | Primary controller for scheme CRUD + AJAX |
| `admin/application/models/scheme_model.php` | 854 | Core DB operations for scheme master |
| `admin/application/views/master/scheme/form.php` | 132,075 bytes | Massive scheme form (all settings) |
| `admin/application/views/master/scheme/list.php` | 4,241 bytes | Scheme list page |
| `admin/assets/js/scheme.js` | — | Scheme-specific JS |

---

## 3. Constructor & Session Dependencies

```php
// Models loaded
const SCH_MODEL = "scheme_model";
const ACC_MODEL = "account_model";
const SET_MODEL = "admin_settings_model";
const PAY_MODEL = "payment_model";
const LOG_MODEL = "log_model";

// Session checks
- is_logged (authentication gate)
- access_time_from / access_time_to (time-based access)
- uid, id_branch, branch_settings
- branchwise_scheme, is_branchwise_cus_reg
```

---

## 4. Routes (15 mapped)

| Route | HTTP | Controller Method | Purpose |
|---|---|---|---|
| `scheme` | GET | `index()` → `sch_list()` | Scheme list page |
| `scheme/ajax_scheme_list` | AJAX | `ajax_get_schemes_list()` | JSON list with access control |
| `scheme/add` | GET | `sch_form('Add')` | Add scheme form |
| `scheme/save` | POST | `sch_post('Add')` | Save new scheme |
| `scheme/edit/:id` | GET | `sch_form('Edit', $id)` | Edit scheme form |
| `scheme/update/:id` | POST | `sch_post('Edit', $id)` | Update scheme |
| `scheme/delete/:id` | GET | `sch_post('Delete', $id)` | Delete scheme |
| `scheme/get_metals` | AJAX | `get_metals()` | Metal dropdown |
| `scheme/get_classifications` | AJAX | `get_classifications()` | Classification dropdown |
| `scheme/get_branches` | AJAX | `get_branches()` | Branch dropdown |
| `scheme/get_units` | AJAX | `get_units()` | Installment units |
| `scheme/get_schemes` | AJAX | `ajax_get_schemes()` | Active schemes dropdown |
| `scheme/get_schemes/:type` | AJAX | `ajax_get_schemes($type)` | Filtered schemes |
| `scheme/get_scheme/:id` | AJAX | `ajax_get_scheme($id)` | Single scheme business data |
| `scheme/get/fix_schemes` | AJAX | `ajax_fixweight_schemes()` | Fix-weight schemes |

### Additional AJAX (no explicit route — called directly)
| Controller Method | Purpose |
|---|---|
| `getSchemeTypeByID($id)` | Scheme type by ID |
| `getFreeInsBySchId($id)` | Free installment settings by ID |
| `get_branch_edit($id)` | Branches for scheme edit |
| `gstsplitupinsert()` | Batch GST insert for existing schemes |
| `getActivePuritiesByMetal()` | Purities by metal |
| `joinTime_weight_slabs()` | Lump sum weight slabs |
| `get_weight_list()` | Weight list by range |
| `checkDigiAvailability()` | DigiGold availability check |
| `set_scheme_image($id)` | Image upload (internal) |

---

## 5. Model Methods Summary

**53 methods** in `scheme_model.php` — full index in [METHOD_INDEX.md](METHOD_INDEX.md).

| Category | Count |
|---|---|
| Scheme CRUD | 6 (insert, update, delete, count, get, empty_record) |
| Scheme Query/Lookup | 10 (get_scheme, get_scheme_active, get_schemes, etc.) |
| Settings Tables CRUD | 12 (benefit charts, deduct charts, agent, incentive, flexi, GA) |
| GST Operations | 4 (insert, update, get, batch insert) |
| Branch Operations | 4 (scheme_branch, delete, get_branches, get_branch_edit) |
| Lookup/Master | 6 (metals, classifications, purities, weight, chit_settings) |
| Configuration Queries | 5 (sch_limit, branchwise_scheme, enableDigiGold, etc.) |
| TopUp Scheme | 2 (getTopUpSchemeChart, topUpSchemeChartEditProcess) |
| Generic CRUD | 2 (insertData, deleteData) |

---

## 6. Key Tables

| Table | Role | Owner |
|---|---|---|
| `scheme` | Core scheme master — 130+ columns | ✅ This module |
| `scheme_branch` | Scheme ↔ branch mapping | ✅ This module |
| `scheme_benefit_deduct_settings` | Interest/benefit chart by installment | ✅ This module |
| `scheme_debit_settings` | Pre-close deduction chart | ✅ This module |
| `scheme_agent_benefit` | Agent benefit chart by installment | ✅ This module |
| `scheme_incentive_settings` | Employee/agent/customer incentive slabs | ✅ This module |
| `scheme_flexi_settings` | Flexible installment min/max ranges | ✅ This module |
| `emp_closing_incentive` | Employee closing incentive chart | ✅ This module |
| `scheme_general_advance_benefit_settings` | GA bonus chart | ✅ This module |
| `scheme_custom_payable_settings` | TopUp scheme payable chart | ✅ This module |
| `gst_splitup_detail` | GST split-up per scheme | ✅ This module |
| `metal` | Metal master | ❌ Shared |
| `ret_metal_purity_rate` | Metal-purity mapping | ❌ Shared |
| `ret_purity` | Purity master | ❌ Shared |
| `sch_classify` | Scheme classification | ✅ This module |
| `chit_settings` | Global settings | ❌ Settings module |
| `weight` | Weight master | ❌ Shared |
| `scheme_account` | Scheme accounts (read-only check) | ❌ Account module |

---

## 7. Known Risks & Bugs

| # | Risk | Severity | Location |
|---|---|---|---|
| 1 | **SQL Injection** — `WHERE s.id_scheme =$id` raw param | 🔴 HIGH | Model L184, L199, L216 |
| 2 | **SQL Injection** — `$customerId` in raw query | 🔴 HIGH | Model L108, L130 |
| 3 | **SQL Injection** — `$_POST['id_metal']` in raw query | 🔴 HIGH | Controller L1213 |
| 4 | **SQL Injection** — `$_POST['wgt_min/wgt_max']` | 🔴 HIGH | Model L795 |
| 5 | **Direct `$_POST` access** — Multiple places use raw `$_POST` | 🟡 MED | Controller L428, L489, L505, L537, L813 |
| 6 | **Double trans_commit** — TopUp chart edit commits inside `sch_post('Edit')` at L966, then again at L984 | 🔴 HIGH | Controller L966+L984 |
| 7 | **Duplicate key in array** — `emp_incentive_closing` set twice in Add data | 🟡 MED | Controller L367-368 |
| 8 | **Duplicate key in array** — `interest_mode` set twice in chart insert | 🟡 MED | Controller L438+442 |
| 9 | **Missing return** — `check_acc_records()` returns nothing on no rows | 🟡 MED | Model L537-544 |
| 10 | **`die()` in upload** — `die("Unknown filetype")` stops execution | 🟡 MED | Controller L1204 |
| 11 | **`unlink()` without exists check** — `set_scheme_image()` deletes file without checking | 🟡 MED | Controller L1167 |
| 12 | **@BUG: Add uses `$id` (NULL) for incentive insert** — L524 `'id_scheme' => $id` but `$id` is empty in Add | 🔴 HIGH | Controller L524 |
| 13 | **Delete via GET** — `scheme/delete/:id` is GET, CSRF vulnerable | 🟡 MED | Route L115 |
| 14 | **Commented-out code** — ~100 lines of dead code | 🟡 LOW | Throughout |
| 15 | **Form.php is 132KB** — Extremely large view file, hard to maintain | 🟡 LOW | View form.php |

---

## 8. Anti-Patterns Register

| # | Pattern | Occurrences | Risk |
|---|---|---|---|
| 1 | Raw SQL with string concat | ~10 queries | SQL injection |
| 2 | `$_POST` direct access without `$this->input->post()` | ~8 places | XSS / validation |
| 3 | SHOW COLUMNS on every insert/update | Every save (2x) | Performance |
| 4 | Delete-then-insert for child tables | 7 child table patterns | Race condition |
| 5 | Transaction started but double-committed | TopUp edit path | Data corruption |
| 6 | Massive form.php view (132KB) | 1 file | Maintenance |
| 7 | print_r/exit debug statements commented | Throughout | Code quality |
