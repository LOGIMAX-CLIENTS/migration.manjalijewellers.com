# INVARIANT MATRIX — customer
> Round R3-Upgrade — 2026-03-25 | 6 dimensions, 25+ invariants, 10 edge cases

---

## What is an Invariant?

An **invariant** is a condition that must **always be true** regardless of inputs, filters, or client configuration. If any invariant breaks, there is a bug.

---

## Dimension 1: Identity Integrity Invariants

| ID | Invariant | Broken When | Verification SQL |
|---|---|---|---|
| INV-I01 | **Mobile unique per company**: No two active customers in the same company have the same mobile | `mobile_available()` bypassed (import, sync, API) | `SELECT mobile, id_company, COUNT(*) as cnt FROM customer WHERE active=1 GROUP BY mobile, id_company HAVING cnt > 1` |
| INV-I02 | **Username unique globally**: No two customers share a username | `username_available()` race condition | `SELECT username, COUNT(*) as cnt FROM customer WHERE username IS NOT NULL AND username != '' GROUP BY username HAVING cnt > 1` |
| INV-I03 | **Every customer has an address record**: `address.id_customer` = `customer.id_customer` for all active customers | Address INSERT fails after customer INSERT | `SELECT c.id_customer FROM customer c LEFT JOIN address a ON c.id_customer = a.id_customer WHERE a.id_address IS NULL AND c.active = 1` |
| INV-I04 | **`customer.id_address` matches `address` record**: Back-reference is correct | `update_customer()` doesn't update `id_address` on address re-create | `SELECT c.id_customer, c.id_address, a.id_address FROM customer c LEFT JOIN address a ON c.id_customer = a.id_customer WHERE c.id_address != a.id_address` |
| INV-I05 | **Password is base64 of actual password**: `base64_decode(customer.passwd)` = original password | Encoding scheme changed without migration | N/A (manual check) |

---

## Dimension 2: Data Visibility Invariants

| ID | Invariant | Broken When | Test |
|---|---|---|---|
| INV-V01 | **Branch-wise login hides other branches**: When `branchWiseLogin=1 AND branch_settings=1`, user in Branch A sees ONLY Branch A customers + `show_to_all` branches | SQL branch filter missing or wrong AND/OR | Login as Branch A user, verify no Branch B customers visible |
| INV-V02 | **Admin (uid=1 or uid=2) sees all**: Admins bypass branch filter | Admin gate removed | Login as uid=1, verify all branches visible |
| INV-V03 | **Company isolation**: When `company_settings=1`, user sees only `id_company` match | Company filter missing in query | Multi-tenant: login as Company A, verify no Company B data |
| INV-V04 | **Inactive customers visible in list**: Active/inactive flag is display-only, not filter | List query adds `WHERE active=1` filter | Add inactive customer, verify it appears in list with "Inactive" label |

---

## Dimension 3: Write Safety Invariants

| ID | Invariant | Broken When | Location |
|---|---|---|---|
| INV-W01 | **Customer add uses `trans_begin/commit`**: All customer+address+KYC inserts are atomic | Transaction wrapping removed | Controller L419 `trans_begin()` must exist |
| INV-W02 | **Customer edit uses `trans_begin/commit`**: Edit is atomic | Same | Controller L1060 |
| INV-W03 | **Delete requires dependency check first**: `check_customer_dependencies()` must pass before `delete_customer()` | Direct delete without pre-check | Controller L108 → `ajax_check_delete()` called before delete |
| INV-W04 | **KYC dedup on edit**: `kyc_exists()` checked before insert to prevent duplicate KYC records | Dedup check skipped or wrong params | Controller L1086+ (edit path) |
| INV-W05 | **Wallet creation not in main transaction**: `wallet_account_create()` runs AFTER `trans_commit()` | If wallet fails, customer already committed | Controller L1769 — by design, not wrapped |

---

## Dimension 4: Session / Auth Invariants

| ID | Invariant | Broken When | Check |
|---|---|---|---|
| INV-A01 | **All methods require `is_logged` session**: Constructor gate redirects on missing session | Session gate removed | Direct URL access without session → must redirect to login |
| INV-A02 | **Access control via `get_access('customer')`**: View permission required | `get_access()` returns false but controller serves | Restricted user navigates directly → must redirect to dashboard |
| INV-A03 | **Branch session drives customer filter**: `session('id_branch')` used in queries | Session value out of sync with DB | Clear session, login fresh, verify branch filter matches settings |

---

## Dimension 5: Configuration-Driven Behavior Grid

| Config Column | Value 0 | Value 1 | Affected Methods |
|---|---|---|---|
| `wallet_account_type` | No wallet created on add | Wallet auto-created with SMS + email | `cus_post('Add')`, `wallet_account_create()` |
| `branchWiseLogin` | All branches visible | Branch-restricted view | `get_all_customers()`, all list queries |
| `branch_settings` | Branch feature off | Branch feature on (filter applies) | Same as `branchWiseLogin` |
| `company_settings` | Single tenant (no company filter) | Multi-tenant (company filter active) | All list queries, mobile/email uniqueness |
| `allow_join_multiple` | Customer can have 1 scheme account | Customer can have multiple | `ajax_get_customers()` dropdown filter |
| `edit_custom_entry_date` | Date = system date | Admin can set custom entry date | `cus_form('Add')`, `get_entrydate()` |
| `autoSyncExisting` | No auto-sync on add | Sync offline data on customer add | `sync_existing_data()`, `insExisAcByMobile()` |
| `integrationType` (=3) | No ERP sync | Full ERP sync on add | `sync_existing_data()` |

---

## Dimension 6: Edge Case Registry

| ID | Scenario | Expected Behavior | Actual Behavior / Bug |
|---|---|---|---|
| EC-01 | Add customer with mobile that exists in `customer_reg` (sync staging) | Auto-create scheme accounts from offline data | ✅ Works if `integrationType=3` or `autoSyncExisting=1` — but ⚠️ no transaction |
| EC-02 | Add customer with `wallet_account_type=1` but wallet number generation fails | Customer saved without wallet | ⚠️ Customer committed first, wallet runs after → orphan customer without wallet |
| EC-03 | Delete customer with closed scheme accounts (`is_closed=1`) | Should allow deletion | ✅ `check_customer_dependencies()` only blocks `active=1 AND is_closed=0` |
| EC-04 | Delete customer → KYC records | KYC records should be cleaned | ❌ KYC NOT deleted — orphan records (CUS-BUG-006) |
| EC-05 | Edit customer → upload new webcam image | DB `cus_img` should update with new path | ❌ `set_image()` never calls `update_images()` — image path lost (CUS-BUG-019) |
| EC-06 | Allocate agent via bulk allocation | Customers should have `id_agent` updated | ❌ `$total` overwritten with `array()` — always fails (CUS-BUG-001) |
| EC-07 | `get_customer($id)` → scheme account count | Should show count for THIS customer | ❌ Subquery hardcoded `id_customer=1` — shows customer 1's count (CUS-BUG-007) |
| EC-08 | Profile search with special chars (SQL injection) | Search safely with escaping | ❌ `Searchcustomer()` raw concat — SQL injection (CUS-BUG-003) |
| EC-09 | Download KYC with path traversal (`../../config/database`) | Should be blocked | ❌ No path sanitization in `download()` — LFI (CUS-BUG-002) |
| EC-10 | Zone add with date — 2-digit year | Date should use 4-digit year | ❌ `date('y-m-d')` = 2-digit year (CUS-BUG-026) |
