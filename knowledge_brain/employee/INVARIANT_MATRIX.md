# INVARIANT MATRIX — employee
> Round R2-Upgrade — 2026-03-25 | 5 dimensions, 20+ invariants, 8 edge cases

---

## What is an Invariant?

An **invariant** is a condition that must **always be true** regardless of inputs, filters, or client configuration. If any invariant breaks, there is a bug.

---

## Dimension 1: Identity Integrity Invariants

| ID | Invariant | Broken When | Verification SQL |
|---|---|---|---|
| INV-E01 | **Username unique per company**: No two active employees share a username within the same company | `check_username()` bypassed or race condition | `SELECT username, id_company, COUNT(*) as cnt FROM employee WHERE active=1 AND username IS NOT NULL GROUP BY username, id_company HAVING cnt > 1` |
| INV-E02 | **Mobile unique per company**: No two active employees share a mobile within the same company | `mobile_available()` bypassed | `SELECT mobile, id_company, COUNT(*) as cnt FROM employee WHERE active=1 GROUP BY mobile, id_company HAVING cnt > 1` |
| INV-E03 | **Emp code unique per company**: No duplicates | `check_empcode()` bypassed | `SELECT emp_code, id_company, COUNT(*) as cnt FROM employee GROUP BY emp_code, id_company HAVING cnt > 1` |
| INV-E04 | **Every employee has exactly ONE address record** | ❌ BROKEN — duplicate address on every edit (EMP-BUG-003) | `SELECT id_employee, COUNT(*) as cnt FROM address WHERE id_employee IS NOT NULL GROUP BY id_employee HAVING cnt > 1` |
| INV-E05 | **`pwd_hash` is valid bcrypt hash**: `password_verify()` works | Encoding issue | N/A (manual test) |

---

## Dimension 2: Data Visibility Invariants

| ID | Invariant | Broken When | Test |
|---|---|---|---|
| INV-V01 | **Non-superadmin hides superadmin employees**: When `session.profile != 1`, users don't see `id_profile=1` employees | SQL filter removed | Login as profile=2, verify no superadmin in list |
| INV-V02 | **Multi-company isolation**: When `company_settings=1`, user sees only their company's employees | ❌ BROKEN — `$id_company` undefined (EMP-BUG-006) | Login as Company A, verify no Company B employees visible |
| INV-V03 | **Superadmin sees all profiles**: `profile=1` users bypass the profile filter | Filter wrongly applied | Login as profile=1, verify all profiles visible |

---

## Dimension 3: Write Safety Invariants

| ID | Invariant | Broken When | Location |
|---|---|---|---|
| INV-W01 | **Employee add should be atomic** | ❌ BROKEN — no `trans_begin()` (EMP-BUG-002) | Controller L568 `trans_status()` without matching `trans_begin()` |
| INV-W02 | **Address update on edit should UPDATE, not INSERT** | ❌ BROKEN — always INSERTs (EMP-BUG-003) | Model L498 string bug |
| INV-W03 | **Device enable uses transaction** | ✅ `trans_begin()` present | Controller L1243 |
| INV-W04 | **Settings CRUD uses transaction** | ✅ `trans_begin()/commit()` present | Controller L1051+ |
| INV-W05 | **Delete should clean ALL child tables** | ❌ BROKEN — only cleans address (EMP-BUG-016) | Model L544–570 |

---

## Dimension 4: Session / Auth Invariants

| ID | Invariant | Broken When | Check |
|---|---|---|---|
| INV-A01 | **All methods require `is_logged` session**: Constructor gate redirects on missing session | Session gate removed | Direct URL access without session → must redirect |
| INV-A02 | **Access control via `get_access('employee')`**: Only checked in `ajax_get_emp_list()` | ❌ Missing from other methods | Navigate directly to `/employee/emp_form/Add` without permission |
| INV-A03 | **Password decode only for superadmin**: `id_branch == ''` check gates decoded password display | Branch check bypassed | Non-superadmin can see decoded password in Edit form? |

---

## Dimension 5: Configuration-Driven Behavior Grid

| Config | Value 0 | Value 1 | Affected Methods |
|---|---|---|---|
| `emp_wallet_account_type` | No wallet created | Wallet auto-created on add | `emp_post('Add')`, `emp_wallet_acc()` |
| `company_settings` | Single tenant (no company filter) | Multi-tenant (company filter active) | `get_list_data()`, `check_username()`, `mobile_available()`, `check_empcode()` |
| `chitCollectionEmpCount` | — | Max collection app devices | `enable_device()` (app_type=1) |
| `estimation_app_devices_count` | — | Max estimation app devices | `enable_device()` (app_type=2) |
| `isOTPReqToLogin` | No OTP | OTP required at login | Login controller (reads from employee model) |
| `loginOTP_exp` | — | OTP expiry time | Login controller |

---

## Edge Case Registry

| ID | Scenario | Expected Behavior | Actual Behavior / Bug |
|---|---|---|---|
| EC-01 | Add employee with wallet_account_type=1 but wallet creation fails | Rollback employee | ❌ Employee saved, no wallet — orphan (no transaction wrapping) |
| EC-02 | Edit employee 10 times | 1 address record updated in-place | ❌ 10 duplicate address records created (EMP-BUG-003) |
| EC-03 | Delete employee with allocated customers | Block or NULLIFY FK | ❌ Delete proceeds, `customer.allocated_employee` → broken FK (EMP-BUG-016) |
| EC-04 | Username check via `isUserAvailable()` | Returns TRUE/FALSE JSON | ❌ Returns `print_r()` raw output then exits (EMP-BUG-001) |
| EC-05 | Enable device beyond license limit (concurrent) | Second enable blocked | ⚠️ No DB lock — race condition possible |
| EC-06 | Bulk access time update with SQL injection payload | Sanitized | ❌ Raw `$_POST` into SQL (EMP-BUG-004/007) |
| EC-07 | `employee_status()` toggle via forged GET link | Should require POST+CSRF | ❌ GET-based (same as customer module) |
| EC-08 | Multi-company list with `company_settings=1` | Show only company's employees | ❌ `$id_company` undefined — shows all (EMP-BUG-006) |
