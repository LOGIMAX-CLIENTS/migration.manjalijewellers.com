# INVARIANT MATRIX — masters
> Round R5-Upgrade — 2026-03-25 | 6 dimensions, 25+ invariants, 8 edge cases

---

## What is an Invariant?

An **invariant** is a condition that must **always be true** regardless of inputs, filters, or client configuration. If any invariant breaks, there is a bug.

---

## Dimension 1: Configuration Integrity Invariants

| ID | Invariant | Broken When | Verification |
|---|---|---|---|
| INV-M01 | **`chit_settings` has exactly 1 row (id=1)** | Row deleted or duplicated | `SELECT COUNT(*) FROM chit_settings` → must be 1 |
| INV-M02 | **`metal_rates` latest row = current rate** | Race condition or gap | `SELECT MAX(id_metalrates) FROM metal_rates` |
| INV-M03 | **`../api/rate.txt` = valid JSON of current rate** | ❌ BROKEN — writes "Array" (MST-BUG-003) | `cat ../api/rate.txt` → should be parseable JSON |
| INV-M04 | **Each entity master has unique name** | Duplicate insert bypasses validation | Check `GROUP BY name HAVING COUNT(*) > 1` per entity table |

---

## Dimension 2: RBAC Consistency Invariants

| ID | Invariant | Broken When | Verification |
|---|---|---|---|
| INV-R01 | **Every menu has access records for all active profiles** | New menu added without permission setup | `SELECT m.id_menu, p.id_profile FROM menu m CROSS JOIN profile p WHERE NOT EXISTS (SELECT 1 FROM access a WHERE a.id_menu=m.id_menu AND a.id_profile=p.id_profile)` |
| INV-R02 | **`get_access()` never returns NULL for known menu URLs** | Menu.link not matching hardcoded URL | ❌ BROKEN (MST-BUG-017) — callers don't null-check |
| INV-R03 | **Profile id=1 (superadmin) has view=1 for ALL menus** | Admin permission accidentally removed | `SELECT * FROM access WHERE id_profile=1 AND view=0` → should be empty |
| INV-R04 | **No orphan access records** (profile or menu deleted) | Manual delete without cascade | `SELECT a.* FROM access a LEFT JOIN profile p ON a.id_profile=p.id_profile WHERE p.id_profile IS NULL` |

---

## Dimension 3: Gateway Consistency Invariants

| ID | Invariant | Broken When | Verification |
|---|---|---|---|
| INV-G01 | **Exactly ONE gateway has `is_default=1` per type pair** (demo/pro) | ❌ BROKEN for HDFC/Tech pairs (MST-BUG-026) | `SELECT COUNT(*) FROM gateway_settings WHERE is_default=1 GROUP BY gateway_type HAVING COUNT(*) > 1` |
| INV-G02 | **Active gateway has valid credentials** | Credentials blanked or expired | Manual check — no automatic validation |
| INV-G03 | **No hardcoded credentials in source** | ❌ BROKEN (MST-BUG-040) | `grep -r 'base64_encode.*:.*@' admin_settings.php` |

---

## Dimension 4: Branch Consistency Invariants

| ID | Invariant | Broken When | Verification |
|---|---|---|---|
| INV-B01 | **Every branch has a `metal_rate_settings` record** | Branch created pre-auto-setup era | `SELECT b.id_branch FROM branch b LEFT JOIN metal_rate_settings m ON b.id_branch=m.id_branch WHERE m.id_branch IS NULL` |
| INV-B02 | **Every branch has a `chit_settings` clone** | Clone skipped on old branches | Check `branch_wise_settings` table for each `id_branch` |
| INV-B03 | **`branch.active=1` branches have valid geo data** | Geo references deleted | `SELECT b.* FROM branch b WHERE b.active=1 AND (b.id_country IS NULL OR b.id_state IS NULL)` |

---

## Dimension 5: Configuration-Driven Behavior Grid

| Config | Value 0 | Value 1 | Affected Methods |
|---|---|---|---|
| `branch_settings` | Single branch mode | Multi-branch — rates per branch, branch filter | `metal_rates()`, `get_list_data()` (ALL modules), `insert_branch()` |
| `is_branchwise_rate` | Single rate for all | Rate per branch via `branch_rate` | `metal_rates('Save')`, `rates_by_branch()`, `max_metalrate()` |
| `enableGoldrateDisc` | Market rate stored as-is | Rate = market - discount | `metal_rates('Save')` L520+ |
| `company_settings` | Single company | Multi-tenant — company filter | `get_list_data()` in ALL modules, `check_username()`, etc. |
| `integrationType` | No ERP sync | ERP integration active | Customer module sync operations |
| `sms_gateway` | — | Selected SMS vendor (TextLocal, nammauzhavan) | `send_bulk_sms()`, notification dispatch |
| `emp_wallet_account_type` | No wallet | Auto-create wallet for employees | `emp_post('Add')` in employee module |

---

## Dimension 6: Data Integrity Invariants

| ID | Invariant | Broken When | Verification |
|---|---|---|---|
| INV-D01 | **All entity master records have non-empty names** | ❌ BROKEN for ~15 old CRUDs (MST-BUG-038) | `SELECT * FROM {entity} WHERE name IS NULL OR name = ''` |
| INV-D02 | **`payment_charges` ranges don't overlap** | Charges with overlapping from/to ranges | `SELECT * FROM payment_charges a JOIN payment_charges b ON a.id_charges < b.id_charges AND a.range_from < b.range_to AND a.range_to > b.range_from` |
| INV-D03 | **`version` records have sequential version numbers** | Out-of-order inserts | ORDER BY check |
| INV-D04 | **All `mkdir` calls use 0755, not 0777** | ❌ BROKEN — 7x 0777 (MST-BUG-014) | `grep -n '0777' admin_settings.php` |

---

## Edge Case Registry

| ID | Scenario | Expected | Actual |
|---|---|---|---|
| EC-01 | Any employee hits `/settings/clear_database` | Block | ❌ ALL data truncated (MST-BUG-001) |
| EC-02 | Metal rate save with discount enabled | `rate.txt` = JSON with discounted rates | ❌ `rate.txt` = "Array" (MST-BUG-003) |
| EC-03 | Metal rate edit (update, not new save) | `rate.txt` updated with new values | ❌ `update_rate_file()` commented out (MST-BUG-042) |
| EC-04 | Non-branchwise rate save + notification | All customers receive push | ❌ Only last customer (MST-BUG-029) |
| EC-05 | Set HDFC demo gateway as default | HDFC pro become non-default | ❌ No reciprocal toggle (MST-BUG-026) |
| EC-06 | Branch Update fails midway | Rollback all changes | ❌ No trans_begin (MST-BUG-028) |
| EC-07 | `get_access()` called for menu not in `access` table | Return safe default (view=0) | ❌ Returns NULL → PHP notices (MST-BUG-017) |
| EC-08 | General settings save — configDB data | App version saved | ❌ configDB calls commented out (MST-BUG-022) |
