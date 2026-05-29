# `chit_services` — Coverage Tracker

> **Brain Built:** 2026-03-17 17:41–17:55 IST | **Round 2 Done:** 2026-03-18 10:54 IST | **Coverage: ✅ 100%**

---

## Coverage Summary

| Area | Covered | Total | Coverage |
|---|---|---|---|
| Method groups mapped | 7 groups | 7 | ✅ 100% |
| Methods inventoried | 73 | 73 | ✅ 100% |
| Critical cron methods traced | 5 | 5 | ✅ 100% |
| Payment verify flows traced | 4 | 4 | ✅ 100% |
| Security pattern scan (raw POST/GET/SQL) | ✅ | — | ✅ 100% |
| Active print_r scan | ✅ | — | ✅ 100% |
| Curl/SSL audit | ✅ | — | ✅ 100% |
| Models loaded | ✅ | 10 | ✅ 100% |
| `dayDurationSchemeService` traced | ✅ | — | ✅ 100% |
| `add_daily_collection` traced | ✅ | — | ✅ 100% |
| `scheme_freepayment` full trace | ✅ Round 2 | — | ✅ 100% |
| `syncInterData` full trace | ✅ Round 2 | — | ✅ 100% |
| `update_client` full trace | ✅ Round 2 | — | ✅ 100% |
| `services_model.php` (44KB, 996 lines) | ✅ Round 2 | 29 functions | ✅ 100% |
| `admin_usersms_model.php` (73KB, 1920 lines) | ✅ Round 2 | 50+ functions | ✅ 100% |
| `sms.js` AJAX audit + error handler scan | ✅ Round 2 | — | ✅ 100% |
| `notification.js` AJAX audit + error handler scan | ✅ Round 2 | — | ✅ 100% |
| Log directory security | ✅ Round 2 | — | ✅ 100% |
| **Overall** | | | **✅ 100%** |

---

## Bug Register Summary

### Round 1 Bugs (2026-03-17)

| Bug ID | P | One-line |
|---|---|---|
| SVC-BUG-001 | **P0** | `dayDurationSchemeService()`: 10+ active `print_r();exit;` halts in production cron |
| SVC-BUG-002 | **P0** | `add_daily_collection()`: `echo print_r($instoday)` leaks financial data to output |
| SVC-BUG-003 | **P1** | `ajax_interWallet_trans()`: raw `$_GET['id_branch']` + `$_GET['date']` into DB |
| SVC-BUG-004 | **P1** | `ajaxtempTotest/Main()`: raw `$_POST['id_branch/entry_date/till_updated']` |
| SVC-BUG-005 | **P1** | Sync methods: raw `$_POST/$_GET` for sync params |
| SVC-BUG-006 | **P1** | `send_notification()` + `send_smsdue()`: `$alertcount` undefined before use |
| SVC-BUG-007 | **P1** | `syncExistingCusData()`: raw `$id_branch` concatenated in SQL |
| SVC-BUG-008 | **P2** | 18 curl calls with `CURLOPT_SSL_VERIFYPEER = FALSE` |
| SVC-BUG-009 | **P2** | `dayDurationSchemeService()`: N+1 query — 3 queries per payment record |
| SVC-BUG-010 | **P2** | `send_notification()` / `send_smsdue()`: trans_status() without trans_begin() |
| SVC-BUG-011 | **P2** | `add_daily_collection()`: `-2 days` vs `-1 days` asymmetry in rolling balance |
| SVC-BUG-012 | **P2** | 6× `mkdir(0777)` world-writable directories |
| SVC-BUG-013 | **P2** | Duplicate constants: `MODEL === SMS_MODEL`, `EMAIL_MODEL === MAIL_MODEL` |
| SVC-BUG-014 | **P2** | `test()` method: public debug endpoint, no auth gate |
| SVC-BUG-015 | **P3** | 35 active `print_r()` without exit (data leakage to cron logs/HTTP response) |
| SVC-BUG-016 | **P3** | `send_mjdmarate_notification()`: hardcoded 3rd-party client URL |
| SVC-BUG-017 | **P3** | `oldverify_cashfreepayment()`: `trans_begin()` commented out — no payment atomicity |
| SVC-BUG-018 | **P3** | `passwdord_hash()` typo in method name |

### Round 2 Bugs (2026-03-18)

| Bug ID | P | One-line |
|---|---|---|
| SVC-BUG-019 | **P2** | `scheme_freepayment()`: `$receipt_no` undefined if `receipt_no_set != 1` → NULL receipt inserted |
| SVC-BUG-020 | **P2** | `getFreePayData()`: division by zero if gold rate = 0 → NaN metal weight in payment record |
| SVC-BUG-021 | **P1** | `scheme_freepayment()`: SMS/push fires for every customer in outer loop regardless of eligibility |
| SVC-BUG-022 | **P0** | `update_client()` L3106: `$branch` undefined — updateData() WHERE clause is broken → mass update risk |
| SVC-BUG-023 | **P1** | `update_client()`: `$_GET['sync_trans_date']` unvalidated raw string in DB queries |
| SVC-BUG-024 | **P0** | `services_model::updMaturityDate()` L446: `echo;exit;` permanently halts — maturity NEVER updated |
| SVC-BUG-025 | **P0** | `services_model::updMaturityDate()`: `$record` + `$paidByMonth` undefined — fatal on any call |
| SVC-BUG-026 | **P3** | `sms.js::get_schemes()`: no error handler → silent failure if scheme list fails |
| SVC-BUG-027 | **P3** | `notification.js`: ON/OFF toggle has no error handler → UI/server state desync |

**Totals (All Rounds):** P0: 5, P1: 7, P2: 9, P3: 6 = **27 bugs**

---

## Key Architectural Notes

1. **This is the cron engine** — `admin_services.php` is not a UI controller. It is called by scheduled jobs and webhooks. Bugs here silently corrupt data without user-visible errors.

2. **`dayDurationSchemeService()` is the due-type brain** — Classifying ND/AD/PD/MND for every payment. If it crashes (SVC-BUG-001), customers cannot pay their dues correctly across the entire system.

3. **Three generations of Easebuzz code coexist:**
   - `oldverifyEasebuzzPayments()` (L4208) — legacy
   - `verifyEasebuzzPayments()` (L4724) — current
   - `new_verification('easebuzz', ...)` via `new_webhook()` (L7522) — newest
   All three are active routes. Unclear which is canonical.

4. **SMS gateway is cleanly abstracted** — config-driven switch for 5 gateways is a good pattern.

5. **Log files are in webroot `log/`** — world-readable via HTTP. Contains payment verification responses, customer data, gateway keys in logs.

6. **`services_model.php` is heavily SQL-injected** — 13+ raw string concatenation points in queries across 29 functions.

7. **`admin_usersms_model.php` is clean** — All debug output is commented out. 50+ functions, well-organized into groups. Only risk is SQL concatenation patterns in complex queries.

8. **`update_client()` has a data-corruption class bug (SVC-BUG-022)** — The `$branch` undefined variable means every sync run attempts to update ALL `customer_reg` records without a branch filter.

9. **`services_model::updMaturityDate()` is completely dead (SVC-BUG-024, 025)** — The function has `echo;exit;` before the DB update AND uses undefined variables. Maturity date recalculation has never worked via this function.

---

## Brain Files Index

| File | Purpose |
|---|---|
| `MODULE_BRAIN.md` | Main brain (Round 1): controller architecture, method groups, critical flows |
| `BUG_REGISTER.md` | All 27 bugs with full details and fixes |
| `DATA_FLOW.md` | Detailed data flow traces (cron → DB → response) |
| `ROUND2_MODEL_JS_ANALYSIS.md` | Round 2: `services_model`, `admin_usersms_model`, `sms.js`, `notification.js`, deferred method traces |
| `COVERAGE_TRACKER.md` | This file — coverage audit and bug summary |

---

## Next Steps

### 🔴 IMMEDIATE P0 Actions
1. Remove `print_r();exit;` from `dayDurationSchemeService()` — cron is broken for any qualifier (SVC-001)
2. Remove `echo print_r($instoday)` from `add_daily_collection()` — one-line fix (SVC-002)
3. Fix `$branch` → `$branch_id` typo in `update_client()` L3106 — prevents mass data corruption (SVC-022)
4. Fix `services_model::updMaturityDate()` — remove `echo;exit;` and add `$record` parameter (SVC-024, SVC-025)

### 🟠 P1 Actions
5. `scheme_freepayment()` — move `if ($status)` block inside inner condition (SVC-021)
6. Fix `$alertcount` initialization in `send_notification()` and `send_smsdue()` (SVC-006)
7. Replace all raw `$_GET/$_POST` with CI input helper in all 7 affected methods (SVC-003, 004, 005, 023)

### 🟡 P2 Actions
8. Initialize `$receipt_no = NULL;` before conditional set in `getFreePayData()` (SVC-019)
9. Guard division by zero in `getFreePayData()` for gold rate (SVC-020)
10. Move `log/` directory outside webroot (architectural, Medium priority)

### Proceed To
- `/module-bug-audit chit_services` — Full 6-round audit now possible with 100% brain coverage
