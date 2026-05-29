# Masters Module — Forensic Investigation Template

> **Brain Updated:** 2026-03-25 | **Round:** R5-Upgrade

---

## Layer 1 — Symptom Collection

| # | Question | Why It Matters |
|---|---|---|
| 1 | What entity/section failed? (Rate, Branch, Permission, Settings, Entity CRUD, Gateway, Notification, Backup) | Routes to specific controller method |
| 2 | Was it a save/update/delete/list operation? | Different code path per CRUD action |
| 3 | Is `branch_settings` enabled (multi-branch)? | Changes rate save, list filtering, and notification flow |
| 4 | What is the user's `id_profile`? (1=superadmin) | Profile gates visibility + feature access |
| 5 | Was this a mobile API issue (stale rates)? | `rate.txt` flat file — separate from DB rates |
| 6 | Did the user attempt a configuration change? (general settings, gateway, SMS config) | `chit_settings` single-row update |
| 7 | Was a notification expected but not received? | OneSignal push flow — multiple failure points |
| 8 | Was a file upload involved? (images, logos, Excel import) | Filesystem + GD processing |

---

## Layer 2 — Reproduce & Isolate

```
□ Can you reproduce with a different profile? (superadmin vs regular)
□ Is this branch-specific? (test with different or all branches)
□ Is the entity CRUD old-style (no validation) or new-style? (check MST-BUG-038)
□ For rate issues: check BOTH database AND rate.txt file
□ For notification issues: check OneSignal dashboard separately
□ For permission issues: check `access` table directly
□ Is this a multi-company setup? (company_settings flag)
```

---

## Layer 3 — Client-Side Trace (JS)

**Key AJAX Patterns:**

| Issue Area | AJAX URL Pattern | JS Line Area |
|---|---|---|
| Rate save | `settings/rate/*` | L3210 |
| Entity list load | `settings/{entity}_list` or `settings/{entity}/ajax_list` | Various |
| Permission save | `settings/access/add` | L3149 |
| Branch CRUD | `branch/branch_name/*` | L3672–3944 |
| Gateway config | `admin_settings/ajax_paymentgateway` | L4558 |
| Gift CRUD | `admin_settings/get_all_gifts`, `add_gift`, `update_gift` | L2468–2585 |

**Note:** 61/89 AJAX calls lack `error:` handlers — failures are SILENT in the browser.

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Rate not saving | admin_settings.php | `metal_rates('Save')` | L515–590 | `insert_metalrate()` return, discount calc logic |
| Mobile rate wrong | admin_settings.php | `metal_rates('Save')` | L570 | `rate.txt` — writes "Array" not JSON (MST-BUG-003) |
| Rate not updated on edit | admin_settings.php | `metal_rates('Update')` | L632 | `update_rate_file()` commented out (MST-BUG-042) |
| Permission not working | admin_settings_model.php | `get_access()` | L96 | Check `access` table for this profile + menu combination |
| Branch save failed | admin_settings.php | `branch_form('Save/Update')` | L3027+ | No trans_begin on Update (MST-BUG-028) |
| Settings not saved | admin_settings.php | `general_settings('Save')` | L1707+ | Check trans_status; configDB commented out (MST-BUG-022) |
| Gateway wrong default | admin_settings.php | `gateway_settings()` | L2320+ | Check `is_default` values: should be exactly 1 per type |
| Notification not sent | admin_settings.php | `send_RatesToAllUsers()` | L3583+ | Non-branchwise path sends only to last customer (MST-BUG-029) |
| Entity CRUD blank name saved | admin_settings.php | Entity method | Various | Check if old-style CRUD (no form_validation) |
| Country/state/city dropdown empty | admin_settings.php | `get_country/state/city()` | L995–1071 | Raw $_POST — check if SQLi payload caused error |
| DB backup downloadable by regular user | admin_settings.php | `db_backup()` | L2275 | No role check (MST-BUG-033) |
| DATABASE WIPED | admin_settings.php | `clear_database()` | L2190 | ⚠️ P0 — no auth gate (MST-BUG-001) |

---

## Layer 5 — Database Verification

### Config integrity
```sql
-- Must return exactly 1 row
SELECT COUNT(*) FROM chit_settings;

-- Current settings snapshot
SELECT * FROM chit_settings WHERE id_chit_settings = 1;
```

### Current metal rate
```sql
SELECT * FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1;
```

### RBAC check for specific profile + menu
```sql
SELECT a.*, m.link, m.label, p.profile_name
FROM access a
JOIN menu m ON a.id_menu = m.id_menu
JOIN profile p ON a.id_profile = p.id_profile
WHERE p.id_profile = {PROFILE_ID} AND m.link = '{MENU_URL}';
```

### Missing profile permissions
```sql
SELECT m.id_menu, m.link, m.label
FROM menu m
WHERE m.active = 1
AND m.id_menu NOT IN (
    SELECT id_menu FROM access WHERE id_profile = {PROFILE_ID}
);
```

### Gateway default status (should be exactly 1 per pair)
```sql
SELECT id_gateway, gateway_name, is_default
FROM gateway_settings
ORDER BY id_gateway;
```

### Orphan entity checks
```sql
-- Access records with no profile
SELECT a.* FROM access a LEFT JOIN profile p ON a.id_profile = p.id_profile WHERE p.id_profile IS NULL;

-- Access records with no menu
SELECT a.* FROM access a LEFT JOIN menu m ON a.id_menu = m.id_menu WHERE m.id_menu IS NULL;

-- Branch without rate settings
SELECT b.id_branch, b.name FROM branch b LEFT JOIN metal_rate_settings m ON b.id_branch = m.id_branch WHERE m.id_branch IS NULL AND b.active = 1;
```

---

## Layer 6 — Root Cause Classification

| Category | Examples | Risk |
|---|---|---|
| **SQL Injection** | `get_access`, `get_branch_rate`, `max_metalrate`, `settingsDB`, `getBranchId`, `get_version_data`, `menu_generation`, `get_state/city/village` | HIGH — 11+ raw SQL concat points |
| **No Auth Gate** | `clear_database`, `db_backup`, `download` | CRITICAL — any logged user |
| **Data Corruption** | `rate.txt` writes "Array", `configDB` commented out | HIGH — downstream consumers affected |
| **Silent Failure** | 61/89 AJAX without error handlers, `get_access` returns NULL | MEDIUM — user sees no feedback |
| **Hardcoded Secrets** | SMS vendor credentials in source | HIGH — credential exposure |
| **Missing Transactions** | `branch_form('Update')`, rate save, permission save | MEDIUM — partial updates |
| **CSRF** | GET-based entity deletes | MEDIUM — state change via link |

---

## Layer 7 — Configuration Impact Trace (Masters-Specific)

When a bug involves `chit_settings` or system config:

```
1. Identify which config field changed:
   SELECT * FROM chit_settings WHERE id_chit_settings = 1;

2. Check ALL modules that read this setting:
   → Every module calls settingsDB('get') on every request
   → Change to branch_settings affects list filtering everywhere
   → Change to enableGoldrateDisc affects rate calculations

3. Check if the change propagated:
   → DB value updated? (SELECT to verify)
   → rate.txt updated? (cat ../api/rate.txt)
   → Session still has old value? (config is NOT cached in session)

4. Check if notification was triggered:
   → canSendNoti() → returns chit_settings.canSendNoti
   → OneSignal API response (check server logs)
```

---

## Layer 8 — Metal Rate Integrity Check (Masters-Specific)

When rate issues are reported:

```
1. Verify DB rate:
   SELECT * FROM metal_rates ORDER BY id_metalrates DESC LIMIT 3;

2. Verify flat file:
   TYPE ..\api\rate.txt
   → Should be valid JSON with goldrate_22ct, silverrate_1gm, etc.
   → If it says "Array" → MST-BUG-003 is active

3. Verify branch-specific rates:
   SELECT br.id_branch, mr.goldrate_22ct, mr.silverrate_1gm
   FROM branch_rate br
   JOIN metal_rates mr ON br.id_metalrate = mr.id_metalrates
   WHERE br.status = 1
   ORDER BY br.id_branch;

4. Verify discount application:
   SELECT enableGoldrateDisc, goldDiscAmt,
          enableSilver_rateDisc, silverDiscAmt
   FROM chit_settings WHERE id_chit_settings = 1;
   → Then compute: stored_rate = market_rate - discount
   → Compare with actual stored value in metal_rates
```
