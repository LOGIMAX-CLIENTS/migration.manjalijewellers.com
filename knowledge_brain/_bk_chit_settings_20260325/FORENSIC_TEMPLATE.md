# Chit Settings Module — Forensic Template
> **Round**: 1 | **Date**: 2026-03-06

---

## Layer 1: Symptom Collection

### Common Symptoms for Settings Module
- [ ] Menu items not appearing for a profile
- [ ] Metal rates not saving / showing wrong values
- [ ] Branch not appearing in dropdowns
- [ ] General settings toggle not taking effect
- [ ] Permission changes not reflecting
- [ ] Rate notifications not sending
- [ ] Maintenance mode stuck ON/OFF
- [ ] Account number format changed unexpectedly
- [ ] Gateway settings not connecting
- [ ] Duplicate master data entries

---

## Layer 2: Reproduce & Isolate

### Quick Checks
1. **Settings state?** `SELECT * FROM chit_settings WHERE id_chit_settings = 1`
2. **User profile?** `SELECT id_profile FROM employee WHERE id_employee = {UID}`
3. **Menu access?** Use DB_TRUTH_PROTOCOL.sql Section 2.2
4. **Rate latest?** `SELECT * FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1`
5. **Branch mapped?** `SELECT * FROM branch_rate WHERE id_branch = {BRANCH}`

### Isolation Questions
- Is this profile-specific? (compare admin vs staff)
- Is this branch-specific? (compare branches)
- Is this rate-specific? (compare market vs selling)
- Is this a save vs display issue?

---

## Layer 3: Client-Side Trace

### Key JS Areas
| Module | JS File | Purpose |
|---|---|---|
| General Settings | `settings/general/*` | 60+ toggle form |
| Metal Rates | Rate form | Discount calculations |
| Permission | Permission form | Checkbox matrix |
| Master Data | Various entity forms | CRUD forms |
| Branch | Branch form/list | Branch management |

### Network Tab Checks
| AJAX Call | Expected Response | Common Failures |
|---|---|---|
| `ajax_get_bank` | `[{id_bank, bank_name}]` | Empty array |
| `ajax_get_paymentMode` | `[{id_mode, mode_name}]` | Empty |
| `ajax_get_branches` | `[{id_branch, name}]` | No branches |
| `metal_rates/Ajax` | Rate JSON with access | SQL error |
| Permission save | Plain text success | 500 error |

---

## Layer 4: Server-Side Trace

### Controller Trace Points

| Symptom | Method | Line | What To Check |
|---|---|---|---|
| Settings not saving | general_settings('Update') | L1970-2122 | 55-field array build, settingsDB() |
| Menu not showing | Controller constructor | L22-24 | is_logged session |
| Permission not saving | permission('Save') | L360-371 | JSON decode, exist check |
| Rate wrong value | metal_rates('Save') | L536-606 | Discount calc at L547-551 |
| Rate notification fails | send_RatesToAllUsers() | L3540-3648 | canSendNoti, token data |
| Branch not saving | branch_form('Update') | L2984-3132 | branchDB update |
| DB clear executed | clear_database() | L2147-2209 | ⚠️ Raw $_POST, no CSRF |
| Config not saved | general_settings('Save') | L1949 | ⚠️ configDB call COMMENTED OUT |

### Model Trace Points

| Symptom | Method | What To Check |
|---|---|---|
| SQL error | Any *DB() method | Raw $id in WHERE clause (SQL injection) |
| Menu hierarchy broken | menu_generation() | Recursive submenu generation |
| Duplicate insert | bankDB/paymodeDB/dept/design insert | Duplicate check logic |
| Wrong permission | get_access() | Profile + URL matching query |
| Settings NULL | settingsDB('get') | Row exists in chit_settings |

---

## Layer 5: Database Verification

Use `DB_TRUTH_PROTOCOL.sql`:
1. **Section 1**: Full settings state dump
2. **Section 2**: Permission matrix + orphan detection
3. **Section 3**: Metal rate integrity + discount impact
4. **Section 4**: Master data integrity
5. **Section 5**: Gateway/SMS configuration
6. **Section 6**: System health (menu hierarchy, backup history, negative rates)

---

## Layer 6: Root Cause Classification

| Category | Typical Cause | Example |
|---|---|---|
| SQL Injection | Raw variable in query | `WHERE id_bank = $id` in 50+ queries |
| Configuration | Commented-out code | config_settings NOT saved (L1949, L2094) |
| Data Loss | clear_database TRUNCATE | No CSRF, no confirmation gate |
| Orphan Data | Menu/access unsync | Delete menu but access stays |
| Security | Plain text passwords | mail_password stored raw |
| Performance | 178 methods in one model | Hard to maintain, load unused deps |

---

## Layer 7: Critical Bug Investigations

### BUG-SET-001: Config Settings NOT Being Saved
**Symptom**: Config changes (app versions, play store URL) not persisting  
**Root Cause**: Controller L1949 and L2094 — `configDB('insert'/'update')` calls are **COMMENTED OUT**  
**Impact**: Config table never updated via general settings form  
**Fix**: Uncomment `$status_config = $this->$model->configDB('update', $id, $config_dat);`

### BUG-SET-002: clear_database Uses Raw $_POST
**Symptom**: Database wipe possible via crafted POST  
**Root Cause**: Controller L2151 — `$clear_by = $_POST;` bypasses CI input filtering  
**Impact**: No CSRF protection, no input sanitization, full DB wipe  
**Fix**: Use `$this->input->post()`, add CSRF token, add admin-only guard

### BUG-SET-003: Undefined Constant `tab_name`
**Symptom**: PHP notice on general settings save  
**Root Cause**: Controller L1957 and L2108 — `$general[tab_name]` should be `$general['tab_name']`  
**Impact**: PHP warning, log entry has literal string "tab_name" as value  
**Fix**: Add quotes around array key

### BUG-SET-004: SQL Injection in 50+ Queries
**Symptom**: Any model method with raw `$id` in SQL  
**Root Cause**: Model uses string concatenation for all SQL queries  
**Example**: `WHERE id_bank = $id`, `WHERE id_menu = $id`, `WHERE id_profile = $id`  
**Impact**: Full database compromise via crafted URL parameters  
**Fix**: Use CI query builder or parameterized queries

### BUG-SET-005: Rate File Write with Non-JSON Data
**Symptom**: `../api/rate.txt` contains PHP array syntax instead of JSON  
**Root Cause**: Controller L570 — `file_put_contents('../api/rate.txt', $rate_array)` writes array, not JSON  
**Impact**: API consumers reading rate.txt get malformed data  
**Fix**: Use `json_encode($rate_array)` before writing
