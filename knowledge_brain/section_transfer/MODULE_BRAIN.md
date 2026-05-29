# MODULE BRAIN — Section Transfer
> Built: 2026-03-14 | Version: 1.0 | Round: 1 | **R14 Refreshed: 2026-03-24**

> **🔄 REFRESHED BRAIN (R14)**
> Code modified: 2026-03-21 (Controller, Model, JS). Brain built: 2026-03-14. Delta: +7 days.
> Changes: `getSectionTags()` — `customerorderdetails` JOIN added to BOTH SQL branches; `onlyBranchSelected` guard added (L229-231). RULE-ST-007 now enforced in code.

---

## 1. Module Overview

**Purpose**: Transfer jewellery items (tagged and non-tagged) between sections within or across branches. Supports OTP-gated counter-change transfers and logs all movements.

### File Map

| File | Lines | Purpose |
|---|---|---|
| `admin/application/controllers/admin_ret_section_transfer.php` | 673 | Route handler — section transfer, OTP send/verify |
| `admin/application/models/ret_section_transfer_model.php` | 413 | DB queries — tag lookup, stock updates, log inserts |
| `admin/assets/js/ret_section_transfer.js` | 1637 | UI — search, select, validate, transfer, OTP flow |
| `admin/application/views/section_transfer/list.php` | 443 | Single-page list view with search + transfer panels |

### Connection Flow
```
Browser → JS (ret_section_transfer.js)
        → AJAX POST → Controller (admin_ret_section_transfer.php)
        → Model (ret_section_transfer_model.php)
        → DB (ret_taging, ret_section_tag_status_log, ret_home_section_item,
               ret_home_section_item_log, ret_taging_status_log,
               ret_nontag_item, ret_section_nontag_item_log, otp)
        → JSON response → JS → UI reload
```

---

## 2. Constructor

| Model / Library | Purpose |
|---|---|
| `ret_section_transfer_model` | Primary — all data methods |
| `admin_settings_model` | Profile data, branch day-closing data, company info, OTP service config |
| `log_model` | Loaded but not visibly called in transfer flow |

**Session Gate**: Redirects to `admin/login` if not logged in. Enforces `access_time_from`/`access_time_to` window; redirects to `chit_admin/logout` on time violation.

---

## 3. Entry Points

| URL Path | Method | Controller Method | Lines | Type | Purpose |
|---|---|---|---|---|---|
| `/admin_ret_section_transfer/ret_section_transfer/list` | GET | `ret_section_transfer()` case `list` | L83–93 | Page load | Render list view |
| `/admin_ret_section_transfer/ret_section_transfer/getSectionTags` | POST | `ret_section_transfer()` case `getSectionTags` | L97–103 | AJAX | Fetch tagged items by filters |
| `/admin_ret_section_transfer/ret_section_transfer/save` | POST | `ret_section_transfer()` case `save` | L107–549 | AJAX | Execute section transfer |
| `/admin_ret_section_transfer/send_counterchange_otp` | POST | `send_counterchange_otp()` | L558–621 | AJAX | Generate & send OTP |
| `/admin_ret_section_transfer/verify_counter_change_otp` | POST | `verify_counter_change_otp()` | L623–664 | AJAX | Verify OTP and mark verified |
| `/admin_ret_section_transfer/index` | GET | `index()` | L61–67 | Page | Empty index (unused) |

---

## 4. Model Methods Summary

See **METHOD_INDEX.md** for full table.

- **Generic CRUD**: `insertData`, `updateData`, `deleteData`
- **Tag operations**: `get_tag_details`, `updatestatus`, `getSectionTags`
- **Section operations**: `sectionData`, `checkSectionItemExist`, `updatesecNTData`
- **Non-tag operations**: `checkNonTagItemExist`, `updateNTData`
- **Day closing**: `getBranchDayClosingData`
- **OTP**: `getBrnachOtpRegMobile`

Total model methods: **12** (3 generic + 9 specific)

---

## 5. Data Flow Summary

Three primary flows:
1. **Tagged Transfer (type=1)**: Search → select tags → (optional OTP) → save → updates `ret_taging.id_section`, logs to `ret_section_tag_status_log`, conditionally updates `ret_home_section_item` + logs `ret_home_section_item_log`, sets `tag_status=14`, logs `ret_taging_status_log`
2. **Non-Tagged Transfer (type=2)**: Search → select NT items → save → decrements source `ret_nontag_item`, logs to `ret_section_nontag_item_log`, increments/inserts destination `ret_nontag_item`, logs again
3. **OTP Flow**: Transfer button → OTP modal → `send_counterchange_otp` → 4-digit OTP in session → `verify_counter_change_otp` → proceed with `add_to_trans`

See **DATA_FLOW.md** for full trace with line numbers.

---

## 6. Key Tables

| Table | Role | Key Columns |
|---|---|---|
| `ret_taging` | Master tag record | `tag_id, tag_code, id_section, current_branch, product_id, tag_status, gross_wt, net_wt, piece` |
| `ret_section` | Section lookup | `id_section, section_name, is_home_bill_counter` |
| `ret_section_tag_status_log` | Tagged transfer log | `tag_id, from_section, to_section, from_branch, to_branch, created_by, created_on, date, status` |
| `ret_home_section_item` | Home-counter stock aggregation | `id_hometag_item, id_branch, id_section, id_product, no_of_piece, gross_wt, net_wt` |
| `ret_home_section_item_log` | Home-counter movement log | same fields + `tag_id, from_section, to_section, status, date` |
| `ret_taging_status_log` | Tag status history | `tag_id, status, from_branch, to_branch, created_by, date` |
| `ret_nontag_item` | Non-tag stock aggregation | `id_nontag_item, branch, product, design, id_sub_design, id_section, no_of_piece, gross_wt, net_wt` |
| `ret_section_nontag_item_log` | Non-tag transfer log | same fields + `from_section, to_section, from_branch, to_branch, status, date` |
| `otp` | OTP tracking | `mobile, otp_code, otp_gen_time, module, id_emp, is_verified, verified_time` |
| `ret_day_closing` | Day close state | `id_branch, is_day_closed, entry_date` |
| `branch` | Branch lookup + OTP mobile | `id_branch, name, otp_verif_mobileno` |
| `ret_product_master` | Product lookup | `pro_id, product_name` |

---

## 7. Form Sections & DOM IDs

| DOM Element | ID | Purpose |
|---|---|---|
| Radio: Tagged/Non-tagged | `type1`/`type2` (name=`section_item_type`) | Switch between transfer modes |
| Branch select (multi-branch) | `branch_select` | Multi-branch selector |
| Branch hidden (single-branch) | `branch_filter` | Pre-set from session |
| Product select | `prod_select` | Filter by product |
| From Section | `select_frm_section` | Source section filter |
| To Section | `select_to_section` | Destination section |
| Tag code search | `tag_code` | Barcode-style tag lookup |
| Old tag ID search | `tag_code_old` | Legacy tag search |
| Estimation no | `est_no` | Filter by estimation |
| Search button | `section_tag_search` | Trigger tag fetch |
| Transfer button | `section_transfer` | Execute transfer |
| OTP allow flag | `allow_order_item_cancel_otp` | Hidden field from PHP session |
| OTP input | `sectrans_otp` | OTP entry field |
| OTP modal | `confirm-sec_transotp` | Bootstrap modal |
| Tagged results table | `section_trans_list` | Tag rows with checkboxes |
| Non-tag results table | `bt_nt_search_list` | DataTable for NT items |

**Hidden fields in form** (PHP-injected): `branch_filter`, `allow_order_item_cancel_otp` — 2 hidden inputs.

---

## 8. Business Rules Summary

See **BUSINESS_RULES.md** for full details.

| Rule ID | Rule |
|---|---|
| RULE-ST-001 | Only tags with `tag_status=0` (available) can be transferred |
| RULE-ST-002 | Day-closing date determines `datetime` stamp for log entries |
| RULE-ST-003 | If target section `is_home_bill_counter=1`, update `ret_home_section_item` aggregation |
| RULE-ST-004 | Tag status set to 14 after home-counter transfer |
| RULE-ST-005 | NT transfer uses arithmetic increment/decrement (not delete-insert) |
| RULE-ST-006 | OTP is session-stored, 4-digit, expires in 300 seconds |
| RULE-ST-007 | Tags linked to customer orders blocked from transfer when only branch/section filter used (`onlyBranchSelected` guard, model L229-231 — R14 **CODE CONFIRMED**) |

---

## 9. Cross-Module Dependencies

See **CROSS_MODULE_MAP.md** for full table.

| Module | Direction | What |
|---|---|---|
| `admin_settings_model` | Read | Profile, day-closing, company, OTP service config |
| `admin_ret_reports` (JS) | AJAX Read | `get_ActiveProduct` — product dropdown |
| `admin_ret_catalog` (JS) | AJAX Read | `get_sectionBranchwise` — section dropdown |
| `admin_ret_brntransfer` (JS) | AJAX Read | `getNonTaggedItem` — non-tagged stock list |
| `customerorderdetails` | Read | Order reservation check on tags |
| `ret_estimation` / `ret_estimation_items` | Read (JOIN) | Estimation-based tag filter |

---

## 10. Known Risks

| Risk | Severity | Location |
|---|---|---|
| SQL injection — raw interpolation in `getSectionTags` | 🔴 HIGH | Model L132–221 — `id_section`, `id_branch`, `id_product`, `old_tag_id`, `tag_code`, `est_no` all unparameterized |
| SQL injection — `checkNonTagItemExist` | 🔴 HIGH | Model L251 — `branch`, `product`, `design`, `id_section`, `id_sub_design` unparameterized |
| SQL injection — `updateNTData` / `updatesecNTData` | 🔴 HIGH | Model L281, L365 — arithmetic expressions with raw user values |
| SQL injection — `getBranchDayClosingData` | 🟡 MED | Model L71 — `id_branch` unparameterized |
| SQL injection — `get_tag_details`, `sectionData`, `updatestatus`, `getBrnachOtpRegMobile` | 🟡 MED | Model L299, L313, L385, L402 |
| OTP stored in session as plain string — multi-mobile concatenation logic | 🟡 MED | Controller L568–578 — OTP concatenated not delimited properly |
| NT save: `from_section`/`to_section` hardcoded NULL on deduct log | 🟡 MED | Controller L379 — `to_section` is NULL when it should be `transfer_to_section` |
| `save` case: `trans_begin()` called INSIDE loop iteration but `trans_commit/rollback` OUTSIDE | 🔴 HIGH | Controller L125 vs L521 — only one `trans_begin` for entire foreach, correct but fragile |
| Missing CSRF protection — all AJAX endpoints use raw `$_POST` | 🟡 MED | No token validation anywhere in controller |
| `updatestatus` ignores passed `$id_field`/`$id_value` params — always uses `$tag_id` | 🟡 MED | Controller calls `updatestatus($val['tag_id'],'tag_id',$val['tag_id'],'ret_taging')` but model signature is `function updatestatus($tag_id)` (L381) |

---

## 11. DB Verification Queries

```sql
-- Complete tag transfer log for a tag
SELECT stl.*, t.tag_code, t.id_section as current_section
FROM ret_section_tag_status_log stl
JOIN ret_taging t ON t.tag_id = stl.tag_id
WHERE stl.tag_id = {TAG_ID}
ORDER BY stl.id DESC;

-- Non-tag item balance check
SELECT ni.*, s.section_name
FROM ret_nontag_item ni
JOIN ret_section s ON s.id_section = ni.id_section
WHERE ni.branch = {BRANCH_ID};

-- Orphan log check (tag logged but tag no longer exists)
SELECT stl.tag_id FROM ret_section_tag_status_log stl
LEFT JOIN ret_taging t ON t.tag_id = stl.tag_id
WHERE t.tag_id IS NULL;

-- Tags with status=0 that have order reservations
SELECT t.tag_id, t.tag_code, co.orderno
FROM ret_taging t
JOIN customerorderdetails co ON co.id_orderdetails = t.id_orderdetails
WHERE t.tag_status = 0;
```

---

## 12. Codebase Notes

- **Coding pattern**: CodeIgniter 3 — `$this->db->query()` raw SQL + ActiveRecord mixed
- **JS conventions**: jQuery, Select2, Bootstrap Switch, DataTables, $.toaster for alerts
- **No dedicated CSS** — uses shared `offcanvas.css`, Bootstrap
- **OTP transaction**: inner `trans_begin()` / `trans_commit()` per mobile number inside `send_counterchange_otp` — can partially commit if multiple mobiles
- **Branch handling**: dual mode — session branch (single branch user) vs. select (multi-branch user)

---

## 13. Anti-Patterns Register

> Populated after bug fixes.

| Pattern | Location | Fix Applied |
|---|---|---|
| — | — | — |
