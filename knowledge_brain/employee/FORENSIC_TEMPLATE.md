# Employee Module — Forensic Investigation Template

> **Brain Updated:** 2026-03-25 | **Round:** R2-Upgrade

---

## Layer 1 — Symptom Collection

When an employee bug is reported, collect this information first:

| # | Question | Why It Matters |
|---|---|---|
| 1 | What operation failed? (Add / Edit / Delete / Image / Device / Settings) | Different code path for each |
| 2 | What is the `id_employee`? | Needed for DB verification queries |
| 3 | Is multi-company mode active (`company_settings=1`)? | Company filter affects visibility |
| 4 | Was a wallet account expected? (`emp_wallet_account_type`) | Wallet creation is conditional |
| 5 | Was a device enable/disable attempted? Which app_type (1=Collection, 2=Estimation)? | Different limit sources |
| 6 | Was a settings update done (discount/access time)? | Settings CRUD has separate bugs |
| 7 | Did the user see an error or was it silent? | Flash message vs. silent failure |
| 8 | Is this a login/auth issue or admin panel issue? | Different method paths entirely |
| 9 | What is the user's `id_profile`? (1=superadmin, other=regular) | Profile gates visibility |
| 10 | Has this employee been edited before? How many times? | Address duplication bug (EMP-BUG-003) |

---

## Layer 2 — Reproduce & Isolate

**Standard reproduction checklist:**

```
□ Can you reproduce in fresh browser session? (rules out stale JS/session)
□ Is the issue company-specific? (test with different id_company)
□ Is the issue profile-specific? (test as superadmin id_profile=1 vs regular)
□ Does it happen on Add only, Edit only, or both?
□ Is wallet creation involved? (check emp_wallet_account_type setting)
□ Is it a device management issue? (check device count vs limit)
□ Is it a login/access time issue? (check employee_settings for this employee)
```

**Quick isolation shortcuts:**
- Add `echo json_encode($emp); exit;` at top of `emp_post()` to see raw POST
- Check `//echo $this->db->last_query();exit;` (many already exist in model)
- Query address count: `SELECT COUNT(*) FROM address WHERE id_employee={ID}`

---

## Layer 3 — Client-Side Trace (JS)

**Key DOM IDs to inspect:**

| Field | DOM ID / Name | Notes |
|---|---|---|
| Employee ID | `#id_employee` | Hidden field on Edit |
| Emp Code | `employee[emp_code]` | Min 3 chars validation |
| Username | `employee[username]` | Uniqueness checked via AJAX ⚠️ BROKEN |
| Mobile | `employee[mobile]` | Uniqueness checked via AJAX |
| Password | `employee[passwd]` | Sent plain, stored dual (base64+bcrypt) |
| Branch | `employee[id_branch]` / `employee[login_branches]` | May be multi-value |
| Profile | `employee[id_profile]` | Role selector |
| Image | `employee[emp_img]` | File upload |

**Network tab — check these AJAX calls:**
1. `/admin_employee/checkempcode` → should return `{status: true/false}`
2. `/employee/check_mobile` → should return `{status: true/false}`
3. `/employee/checkuser` → ⚠️ DEAD — returns raw `print_r()` (EMP-BUG-001)
4. `/employee/emp_post/Add` — main POST

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Employee not saved | admin_employee.php | `emp_post('Add')` | L437–582 | Check `insert_employee()` return → `$emp_id > 0`? |
| Address not saved | employee_model.php | `insert_employee()` | L448 | Is `$emp_address` successful? |
| Address duplicated | employee_model.php | `update_employee()` | L498 | String bug: `'.$emp_id.'` never interpolated |
| Image not uploaded | admin_employee.php | `set_image()` | L852–893 | Check `$_FILES['emp_img']` + directory exists |
| Image path not in DB | admin_employee.php | `set_image()` | L886 | ⚠️ Line is COMMENTED OUT (EMP-BUG-013) |
| Delete leaves orphans | employee_model.php | `delete_emp_record()` | L544–570 | Only deletes address + employee |
| Wallet not created | admin_employee.php | `emp_wallet_acc()` | L1316–1345 | Check `emp_wallet_account_type == 1` |
| Device enable blocked | admin_employee.php | `enable_device()` | L1174–1297 | Count vs limit: `get_enabled_device_count()` |
| Settings not saved | admin_employee.php | `update_emp_data()` | L1051–1143 | Check `trans_status()` |
| Access time SQL fail | employee_model.php | `updEmpAccessTime()` | L771 | Raw SQL injection vector |
| Login fails | employee_model.php | `authenticate_user_id()` | L325–351 | Check `password_verify()` + `active=1` |
| Access time blocked | employee_model.php | `checkAccessTime()` | L807–836 | Time comparison logic |

---

## Layer 5 — Database Verification

### Complete employee record dump
```sql
SELECT e.*, a.address1, a.address2, a.id_country, a.id_state, a.id_city,
       d.name as dept_name, des.name as desig_name, p.profile_name,
       b.name as branch_name
FROM employee e
LEFT JOIN address a ON a.id_employee = e.id_employee
LEFT JOIN department d ON d.id_dept = e.dept
LEFT JOIN designation des ON des.id_design = e.designation
LEFT JOIN profile p ON p.id_profile = e.id_profile
LEFT JOIN branch b ON b.id_branch = e.id_branch
WHERE e.id_employee = {ID};
```

### Address duplication check (EMP-BUG-003)
```sql
SELECT id_employee, COUNT(*) as address_count
FROM address
WHERE id_employee IS NOT NULL
GROUP BY id_employee
HAVING address_count > 1
ORDER BY address_count DESC;
```

### Employee settings check
```sql
SELECT es.*, e.firstname, e.username
FROM employee_settings es
LEFT JOIN employee e ON e.id_employee = es.id_employee
WHERE es.id_employee = {ID};
```

### Device registration + status
```sql
SELECT ed.*, e.firstname, e.emp_code
FROM employee_devices ed
LEFT JOIN employee e ON e.id_employee = ed.emp_id
WHERE ed.emp_id = {ID}
ORDER BY ed.app_type, ed.device_status DESC;
```

### Wallet account check
```sql
SELECT wa.*
FROM wallet_account wa
WHERE wa.idemployee = {ID};
```

### Orphan check — settings without employee
```sql
SELECT es.id_emp_sett, es.id_employee
FROM employee_settings es
LEFT JOIN employee e ON e.id_employee = es.id_employee
WHERE e.id_employee IS NULL;
```

### Orphan check — devices without employee
```sql
SELECT ed.id_collection_device, ed.emp_id
FROM employee_devices ed
LEFT JOIN employee e ON e.id_employee = ed.emp_id
WHERE e.id_employee IS NULL;
```

---

## Layer 6 — Root Cause Classification

| Category | Examples | Risk |
|---|---|---|
| **SQL Injection** | `updEmpAccessTime`, `get_emp_by_company`, `get_empBranch_company`, `get_employee_name_byid`, `ajax_emp_setting`, `getEmployeeByBranch` | HIGH — raw string concat |
| **Silent Failure** | `isUserAvailable` dead endpoint, `set_image` DB update commented, `trans_status` without `trans_begin` | MEDIUM — no error surfaced |
| **Data Corruption** | Address duplicate on every edit | HIGH — silent accumulation |
| **Password Security** | base64 legacy `passwd` field | HIGH — trivially reversible |
| **CSRF** | GET-based status toggle | MEDIUM — state changed by forged link |
| **Orphan Records** | Delete misses settings, devices, wallet, customer FK | MEDIUM — data inconsistency |
| **Raw POST** | `enable_device`, `updateAccessTimeAll` use `$_POST` directly | MEDIUM — bypasses CI sanitization |

---

## Layer 7 — Address Integrity Check (Employee-Specific)

When address issues are reported:

```
1. Count address records for this employee:
   SELECT COUNT(*) FROM address WHERE id_employee = {ID};
   → Expected: 1. If > 1, EMP-BUG-003 is active.

2. Check which is the "latest" address:
   SELECT id_address, address1, pincode FROM address
   WHERE id_employee = {ID}
   ORDER BY id_address DESC;
   → First row = most recent (from last edit)

3. Verify get_emp_address() returns the correct one:
   → It uses row_array() (LIMIT 1 effectively) — returns FIRST row
   → If duplicates exist, it may return the OLDEST address

4. Cleanup (after fixing the root cause):
   DELETE FROM address
   WHERE id_employee = {ID}
   AND id_address != (SELECT MAX(id_address) FROM address WHERE id_employee = {ID});
```
