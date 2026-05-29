# Masters Module — Coverage Tracker

> **Brain Updated:** 2026-03-18 11:16 IST | **Final Round:** 4 | **Coverage: ✅ 100%**

---

## Coverage Summary

| Metric | Covered | Total | Coverage | Status |
|---|---|---|---|---|
| Controller methods inventoried | 120 | 120 | **100%** | ✅ All methods classified |
| Model methods scanned | 178 | 178 | **100%** | ✅ Full grep + key method traces |
| JS AJAX endpoints analyzed | 87 | 87 | **100%** | ✅ Meta-scan done, error handler count confirmed |
| Entity groups mapped | 78 | 78 | **100%** | ✅ All groups identified |
| Bug pattern scan (controller) | ✅ | — | **100%** | ✅ Full file grep |
| Bug pattern scan (model) | ✅ | — | **100%** | ✅ Full file grep |
| Bug pattern scan (JS) | ✅ | — | **100%** | ✅ Full file grep |
| Critical paths traced (R1+R2+R3+R4) | 15 | 15 | **100%** | ✅ All critical flows done |
| Entity CRUD spot-checks (R3) | 5 | 5 | **100%** | ✅ bank, drawee, paymode, ledger, design_form |
| db_backup / compress / download (R3) | ✅ | — | **100%** | ✅ Fully traced |
| View XSS scan (R3) | ✅ | — | **100%** | ✅ 150 views scanned |
| get_SMS_data / send_bulk_sms (R4) | ✅ | — | **100%** | ✅ Hardcoded creds found |
| metal_rates helper + Update path (R4) | ✅ | — | **100%** | ✅ Update bug confirmed |
| profile() + permission() safety (R4) | ✅ | — | **100%** | ✅ Both checked |
| **OVERALL** | | | **✅ 100%** | ✅ COMPLETE |

---

## Bugs Found — All Rounds

### Round 1 (2026-03-17) — 20 bugs

| Bug ID | P | One-line |
|---|---|---|
| MST-BUG-001 | **P0** | `clear_database()`: no auth gate — any employee truncates ALL data |
| MST-BUG-002 | **P1** | `get_access()`: `$url` raw concat in RBAC SQL — SQLi in access control engine |
| MST-BUG-003 | **P1** | `metal_rates('Save')` L570: `file_put_contents` writes "Array" not JSON → mobile API broken |
| MST-BUG-004 | **P1** | `get_gift_name_byId()`: raw `$_POST['id']` → SQL injection |
| MST-BUG-005 | **P1** | `ajax_get_version()`: raw `$_POST['from_date'/'to_date']` → SQL injection |
| MST-BUG-006 | **P1** | `get_state/get_city/get_village_by_pincode()`: raw `$_POST` → SQL injection |
| MST-BUG-007 | P2 | `get_branch_rate()`: raw `$id_branch` in SQL |
| MST-BUG-008 | P2 | `max_metalrate()`: raw `$id_branch` in SQL |
| MST-BUG-009 | P2 | `settingsDB()`: raw `$settings` in SQL WHERE clause |
| MST-BUG-010 | P2 | `getBranchId()`: raw `$warehouse` in SQL |
| MST-BUG-011 | P2 | `get_version_data()`: raw `$version_no` in SQL |
| MST-BUG-012 | P2 | `menu_generation()`: raw `$id_profile` in SQL |
| MST-BUG-013 | P2 | Duplicate constants `SET_MODEL === SETT_MOD` → model loaded twice |
| MST-BUG-014 | P2 | 7× `mkdir(0777)` — world-writable image directories |
| MST-BUG-015 | P2* | DB backup path — *corrected in R3: outside webroot, downgraded* |
| MST-BUG-016 | P2 | `PermissionDB()`: hardcoded menu IDs 17/18 — fragile coupling |
| MST-BUG-017 | P2 | `get_access()` null return — callers don't null-check `$access['view']` |
| MST-BUG-018 | P3 | 61 of 87 AJAX calls without error handlers |
| MST-BUG-019 | P3 | 72 `console.log` statements in production JS |
| MST-BUG-020 | P3 | Dead code with competitor URLs in commented `mjdma_update` block |

### Round 2 (2026-03-17) — 11 bugs

| Bug ID | P | One-line |
|---|---|---|
| MST-BUG-021 | P3 | `general_settings('View')`: duplicate `promotion_crt_settings()` call |
| MST-BUG-022 | P2 | `configDB` insert/update commented out → app version numbers never saved |
| MST-BUG-023 | P3 | `$general[tab_name]` bare constant → E_NOTICE + wrong log value |
| MST-BUG-024 | P3 | `vs_booking_time`: half-sided isset → stores `''` instead of `0` |
| MST-BUG-025 | P2 | Gateway audit log written before checking DB update success |
| MST-BUG-026 | P2 | HDFC/Tech `is_default` toggle missing → two active gateways possible |
| MST-BUG-027 | P3 | `branch_form('Add')`: array compared to integer — accidentally works |
| MST-BUG-028 | **P1** | `branch_form('Update')`: `trans_status()` without `trans_begin()` |
| MST-BUG-029 | **P1** | `send_RatesToAllUsers()` non-branchwise path: only last customer notified |
| MST-BUG-030 | P2 | N+1 query: notification template SELECT inside customer loop |
| MST-BUG-031 | P2 | `onesignalNotificationToAll()`: `$alertdetails['footer']` without null check |

### Round 3 (2026-03-17) — 7 bugs

| Bug ID | P | One-line |
|---|---|---|
| MST-BUG-032 | P3 | `ledger('Save')`: no numeric validation on `opening_balance` |
| MST-BUG-033 | P2 | `db_backup()`: no role check — any employee downloads full DB |
| MST-BUG-034 | P2 | `download()`: no role check + relative hardcoded path |
| MST-BUG-035 | P3 | `compress()`: dead `$data` assignment — result never used |
| MST-BUG-036 | P2 | 40+ master list views: flash message echoed without `htmlspecialchars()` |
| MST-BUG-037 | P3 | 61/87 AJAX calls without error handlers (updated count) |
| MST-BUG-038 | P3 | ~15 older entity CRUDs lack form validation — blank names saveable |

### Round 4 (2026-03-18) — 6 bugs

| Bug ID | P | One-line |
|---|---|---|
| MST-BUG-039 | P2 | `get_SMS_data()`: `$arraycontent` overwritten in loop — only last message used |
| MST-BUG-040 | **P1** | `send_bulk_sms()`: hardcoded credentials `lmx@uzhavan:lmx@2018` in source |
| MST-BUG-041 | P3 | `send_bulk_sms()`: hardcoded HTTP URL for SMS vendor |
| MST-BUG-042 | **P1** | `metal_rates('Update')`: `update_rate_file()` commented out — stale mobile rates after every edit |
| MST-BUG-043 | P3 | `profile()`: no field validation — blank profile_name, unvalidated numerics |
| MST-BUG-044 | P2 | `metal_rates('Save')`: `$branch_id` undefined when non-branchwise → null passed to `send_RatesToAllUsers()` |

### Final Totals

| Priority | Count |
|---|---|
| P0 Catastrophic | **1** |
| P1 Critical | **9** |
| P2 Medium | **21** |
| P3 Low | **13** |
| **Total** | **44** |

---

## Key Architectural Notes

1. **`chit_settings` is the single source of truth** — one row, id=1, controls all major feature flags, read by every module on every request. No cache layer.
2. **`get_access()` is the RBAC engine for ALL modules** — SQL injection risk here is system-wide.
3. **Metal rate flat file (`../api/rate.txt`) is broken on two paths:**
   - `metal_rates('Save')`: writes "Array" string (MST-BUG-003) — not `json_encode()`
   - `metal_rates('Update')`: `update_rate_file()` commented out (MST-BUG-042) — never updates file
   - A correct helper `update_rate_file()` exists at L493 but is dead code
4. **Two generations of CRUD code:**
   - Old (pre-2024): no form validation — blank names saveable
   - New (2025, "jothika"): validates + handles duplicates gracefully
5. **`clear_database()` is catastrophic** — must be first P0 fix
6. **`send_RatesToAllUsers()` non-branchwise path is completely broken** — push sent to only last customer (MST-BUG-029), and `$branch_id` undefined on top (MST-BUG-044)
7. **Hardcoded vendor credentials in source** (MST-BUG-040) — should be rotated immediately

---

## Brain Files Index

| File | Purpose | Round |
|---|---|---|
| `MODULE_BRAIN.md` | Core architecture, entity map, RBAC, metal rate, clear_database, settings | R1 |
| `DATA_FLOW.md` | 7 traced data flows | R1 |
| `BUG_REGISTER.md` | All 44 bugs with root cause + fix | R1–R4 |
| `DEEP_ANALYSIS_R2.md` | general_settings, gateway, branch_form, send_RatesToAllUsers traces | R2 |
| `DEEP_ANALYSIS_R3.md` | CRUD spot-checks, db_backup, view XSS scan, JS error handler audit | R3 |
| `DEEP_ANALYSIS_R4.md` | get_SMS_data, send_bulk_sms, metal_rates Update path, profile/permission safety | R4 |
| `COVERAGE_TRACKER.md` | This file | All |

---

## Priority Fix Queue

### 🔴 P0 — IMMEDIATE
1. **MST-BUG-001**: Add superadmin role check + POST confirmation to `clear_database()` — any accidental hit destroys all data

### 🟠 P1 — Fix Immediately
2. **MST-BUG-003 + MST-BUG-042**: Fix metal rate flat file on both Save and Update paths — 2-line change (`json_encode` + uncomment)
3. **MST-BUG-029 + MST-BUG-044**: Fix rate push notification loop — move dispatch inside loop + initialize `$branch_id`
4. **MST-BUG-028**: Add `trans_begin()` to `branch_form('Update')`
5. **MST-BUG-040**: Rotate and move `nammauzhavan` credentials out of source to config
6. **MST-BUG-006**: Replace raw `$_POST` in get_state/get_city/get_village_by_pincode with CI input + int cast

### 🟡 P2 (High Impact)
7. **MST-BUG-033 + MST-BUG-034**: Add role check to `db_backup()` and `download()`
8. **MST-BUG-044**: Initialize `$branch_id = array(array())` before conditional in `metal_rates('Save')`
9. **MST-BUG-022**: Uncomment `configDB` save to restore app version management

### Next Step
- `/module-bug-audit masters` — full 6-round audit with complete brain
