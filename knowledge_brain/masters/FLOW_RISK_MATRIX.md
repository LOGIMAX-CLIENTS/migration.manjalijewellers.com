# FLOW RISK MATRIX — masters
> **Round**: R5-Upgrade | **Date**: 2026-03-25
> **Primary Entities**: `chit_settings`, `metal_rates`, `branch`, `access`
> **Write Paths**: 8+ high-risk (see SCHEMA_ANALYSIS.md Part D)

---

## 1. State Machines

### `offers.active`
| State | Value | Set By | Guard |
|---|---|---|---|
| Active | 1 | `offers_form('Save')` | — |
| Inactive | 0 | `offers_form('Delete')` | — (soft delete via active toggle) |

### `branch.active`
| State | Value | Set By | Guard |
|---|---|---|---|
| Active | 1 | `branch_form('Save')` default | — |
| Inactive | 0 | Admin toggle | — |

### `metal_rates` (append-only log)
| State | Notes |
|---|---|
| Insert only | Each rate save creates a new row; `max(id_metalrates)` is "current" |
| Update | Existing row can be edited; but `rate.txt` file NOT updated on edit (MST-BUG-042) |

### `payment_gateway.is_default`
| State | Value | Set By | Guard |
|---|---|---|---|
| Default | 1 | `gateway_settings('Update_*')` | ⚠️ No sibling toggle (MST-BUG-026) |
| Non-default | 0 | Should be set when sibling becomes default | ❌ Not implemented |

---

## 2. Inbound Contracts (What Masters Expects from Upstream)

| Upstream | Data/State Expected | Check in Code? | Risk if Violated |
|---|---|---|---|
| **Session** | `is_logged` session valid | YES (constructor) | Redirect to login |
| **Session** | `session.profile` = valid profile ID | YES (used in `get_access`) | RBAC fails |
| **OneSignal** | API key valid + endpoint reachable | NO validation | Push notifications silently fail |
| **SMS vendor** | HTTP endpoint reachable | NO validation | SMS silently fail |
| **Excel library** | PHPExcel loaded | YES (`require_once`) | Fatal error on import |
| **customer_model/scheme_model/account_model** | Models loaded | YES (constructor) | Fatal if missing |

---

## 3. Outbound Contracts (What Masters Guarantees to Downstream)

| Downstream | What Masters Guarantees | Enforced? | Risk if Broken |
|---|---|---|---|
| **ALL modules** | `get_access()` returns valid CRUD permissions | PARTIAL — returns NULL for unconfigured menus (MST-BUG-017) | PHP notices + access silently denied |
| **ALL modules** | `chit_settings` row exists (id=1) | ❌ No check | Fatal error if missing |
| **Payment** | `metal_rates` table has current rates | ✅ Insert on save | Stale rates if no new entry |
| **Mobile API** | `../api/rate.txt` has valid JSON | ❌ BROKEN — writes "Array" (MST-BUG-003) | 🔴 Mobile rate display broken |
| **Employee** | `branch` records valid with `active=1` | ✅ SQL filter | — |
| **Payment** | `gateway_settings` has exactly ONE `is_default=1` per type | ❌ BROKEN (MST-BUG-026) | 🔴 Dual active gateways |
| **Customer** | `village` records exist for assigned pincodes | ✅ FK relationship | — |

---

## 4. Reversal Contracts

| Operation | Tables to Clean | Actually Cleaned? | Gap? |
|---|---|---|---|
| Delete branch | `branch` | ✅ YES (soft delete) | — |
| Delete branch | `metal_rate_settings` | ❌ NO | ⚠️ Orphan settings |
| Delete branch | `branch_rate` | ❌ NO | ⚠️ Orphan rate links |
| Delete branch | `employee_settings` (branch employees) | ❌ NO | ⚠️ Orphan settings |
| Delete offer | `offers` (soft delete active=0) | ✅ YES | — |
| Delete offer | `filesystem` (image) | ❌ NO | ⚠️ Orphan images |
| Delete entity master | Row deleted | ✅ YES | ⚠️ FK references in other modules not checked |
| `clear_database()` | ALL core tables truncated | ✅ (by design) | ⚠️ No auth (MST-BUG-001) |

> **Reversal completeness: ~40%** — Most deletes are simple row deletes without cascading cleanup.

---

## 5. Data Consistency Risks

### 5a. Metal Rate File Sync
| Scenario | Expected | Actual |
|---|---|---|
| Rate Save | `rate.txt` = valid JSON | ❌ Writes "Array" string (MST-BUG-003) |
| Rate Update (edit) | `rate.txt` updated | ❌ `update_rate_file()` commented out (MST-BUG-042) |
| Rate Save + notification | All customers notified | ❌ Non-branchwise: only last customer (MST-BUG-029) |

### 5b. RBAC Consistency
| Scenario | Expected | Actual |
|---|---|---|
| New menu added | Access record created for all profiles | ❌ Manual — admin must set permissions per profile |
| Profile deleted | Access records cleaned | ❌ Not checked — orphan access rows |
| Menu deleted | Access records cleaned | ❌ Not checked — orphan access rows |

### 5c. Gateway Default Consistency
| Scenario | Expected | Actual |
|---|---|---|
| Set Cashfree demo as default | Cashfree pro = non-default | ✅ Reciprocal toggle exists for Cashfree |
| Set HDFC demo as default | HDFC pro = non-default | ❌ No reciprocal toggle (MST-BUG-026) |

---

## 6. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-MST-001 | Hit `/settings/clear_database` as non-admin | REJECT | 🔴 CRITICAL | ❌ — No auth (MST-BUG-001) |
| FR-MST-002 | Save metal rate → check `rate.txt` content | Valid JSON | 🔴 CRITICAL | ❌ — Writes "Array" (MST-BUG-003) |
| FR-MST-003 | Edit metal rate → check `rate.txt` updated | Updated JSON | 🔴 HIGH | ❌ — Commented out (MST-BUG-042) |
| FR-MST-004 | Save rate (non-branchwise) → all customers get push | All receive notification | 🔴 HIGH | ❌ — Only last (MST-BUG-029) |
| FR-MST-005 | SQL injection via `get_state()` with `id_country` payload | Sanitized | 🔴 HIGH | ❌ — Raw $_POST (MST-BUG-006) |
| FR-MST-006 | Branch Update → verify trans_begin protects | Atomic | 🔴 HIGH | ❌ — Missing (MST-BUG-028) |
| FR-MST-007 | Set HDFC demo as default → verify HDFC pro deactivated | Only one active | 🟡 MED | ❌ — No toggle (MST-BUG-026) |
| FR-MST-008 | `db_backup()` as non-admin | REJECT | 🔴 HIGH | ❌ — No role check (MST-BUG-033) |
| FR-MST-009 | Download `unregistered_cus.csv` as non-admin | REJECT | 🟡 MED | ❌ — No role check (MST-BUG-034) |
| FR-MST-010 | Save blank entity name (old CRUD) | REJECT with validation error | 🟡 MED | ❌ — No validation (MST-BUG-038) |
| FR-MST-011 | `get_access()` for unconfigured menu | Graceful fallback | 🟡 MED | ❌ — Returns NULL (MST-BUG-017) |
| FR-MST-012 | Verify SMS vendor credentials not in source | Credentials in config/env | 🔴 HIGH | ❌ — Hardcoded (MST-BUG-040) |
| FR-MST-013 | Delete entity with FK references in other modules | Block or warn | 🟡 MED | ❌ — No FK checks |
| FR-MST-014 | `configDB` save after general settings update | Config data persisted | 🔴 HIGH | ❌ — Commented out (MST-BUG-022) |
| FR-MST-015 | `send_RatesToAllUsers()` with branch_settings=0 | $branch_id initialized | 🔴 HIGH | ❌ — Undefined (MST-BUG-044) |
