# FLOW RISK MATRIX — customer
> **Round**: R3-Upgrade | **Date**: 2026-03-25
> **Primary Entity**: `customer` table — full CRUD lifecycle
> **Write Paths**: 12 confirmed (see SCHEMA_ANALYSIS.md Part D)
> **Note**: Customer is the central identity module — almost every other module depends on it

---

## 1. State Machine

### `customer.active`
| State | Value | Set By | Guard | Can Transition To |
|---|---|---|---|---|
| Active | 1 | `insert_customer()` L250 (default) | — | Inactive(0) |
| Inactive | 0 | `customer_status()` L1745 | ⚠️ GET request (CSRF, CUS-BUG-012) | Active(1) |

### `customer.profile_complete`
| State | Value | Set By | Guard | Can Transition To |
|---|---|---|---|---|
| Incomplete | 0 | Default | — | Complete(1) |
| Complete | 1 | `profile_status()` L1733 | ⚠️ GET request (CSRF, CUS-BUG-012) | Incomplete(0) |

### `customer.kyc_status`
| State | Value | Set By | Guard | Can Transition To |
|---|---|---|---|---|
| Pending | 0 | Default | — | Verified(1) |
| Verified | 1 | `admin_reports::update_kyc()` | PARTIAL — count-based check | — (no un-verify from customer module) |

> ⚠️ `customer_status` and `profile_status` use **GET** routes — any link/image tag can toggling status (CSRF vulnerability).

---

## 2. Inbound Contracts (What Customer Module Expects from Upstream)

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| **Settings** | `chit_settings` row must exist | NO — used in JOIN without null check | Model L675 | `allow_multiple_chit()` fails → customer dropdown breaks |
| **Settings** | `branchWiseLogin` setting matches session | NO guard | Model L26-28 | Wrong branch filter → data leak |
| **Settings** | `wallet_account_type` ∈ {0, 1} | PARTIAL — `==1` check | Controller L1769 | Unknown type silently skips wallet |
| **Settings** | `limit_cust` / `cust_max_count` values valid | YES — `limitDB('get')` | Controller L435 | Stale count → wrong allow/block |
| **Branch** | `branch.id_branch` valid | NO explicit check | Model L496 | `getBranchCode()` throws PHP error on invalid branch |
| **Day Close** | `ret_day_closing` row exists for branch | NO — query returns empty | Model L500 | `custom_entry_date` falls back to system date silently |
| **Financial** | `ret_financial_year` has active row | NO check | Model L189 | `fin_year_code` assignment gets full table (incl. expired) |
| **ERP/Sync** | `customer_reg.record_to = 2` AND `is_registered_online = 0` | YES — WHERE clause | Model L616 | — |
| **ERP/Sync** | `transaction.is_transferred = 'N'` | YES — WHERE clause | Model L701 | — |
| **Agent** | `agent.active = 1` | YES — WHERE clause | Model L568 | Inactive agents filtered from dropdown |
| **Employee** | `employee` records valid | YES — FK join | Controller L2564 | — |
| **Village** | `village` records exist | PARTIAL — query returns empty | Model L485 | Only 1 row returned (CUS-BUG-016) |

---

## 3. Outbound Contracts (What Customer Guarantees to Downstream)

| Downstream Module | What Customer Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| **Scheme Account** | `customer.id_customer` valid and active | ✅ FK relationship | DB error if customer deleted with active accounts |
| **Payment** | Customer mobile unique per company | ✅ `mobile_available()` L417 | Duplicate mobile → payment confusion |
| **Billing** | `customer.id_customer` references valid | ✅ Delete guard checks `ret_billing` | — |
| **Wallet** | Wallet created when `wallet_account_type = 1` | PARTIAL — wallet create not in main transaction | ⚠️ Customer saved but wallet fails = inconsistent state |
| **KYC/Reports** | KYC records have valid `kyc_type` ∈ {1, 2, 3} | ✅ Hardcoded in controller | — |
| **ERP Sync** | Sync marks `customer_reg.is_registered_online = 1` | ✅ `updateInterTableStatus()` | ⚠️ No transaction — partial marking possible |
| **All Modules** | `customer.active` reflects true status | ⚠️ GET toggle (CSRF) | Any link click can deactivate customer |
| **Mobile App** | `customer.passwd` is base64 of actual password | ⚠️ Not actual encryption | 🔴 Anyone with DB access can read all passwords |

---

## 4. Reversal Contracts (Cancel/Delete/Reverse)

| Operation | Tables That Must Be Restored/Cleaned | Actually Cleaned in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| Delete customer | `address` (DELETE) | ✅ YES | `delete_customer()` L355 | — |
| Delete customer | `customer` (DELETE) | ✅ YES | `delete_customer()` L358 | — |
| Delete customer | `wallet_account` (DELETE) | ✅ YES | `delete_customer()` L360 | — |
| Delete customer | `kyc` records (DELETE) | ❌ NO | — | ⚠️ GAP: Orphan KYC records remain (CUS-BUG-006) |
| Delete customer | `log` audit entries | ❌ NO | — | ⚠️ GAP: No audit trail for deletion |
| Delete customer | Filesystem images (`assets/img/customer/{id}/`) | ❌ NO | — | ⚠️ GAP: Orphan image files remain |
| Delete customer | `scheme_account` (check + block) | ✅ GUARDED | `check_customer_dependencies()` L366 | Pre-delete check prevents if active accounts exist |
| Deactivate customer | `customer.active` → 0 | ✅ YES | `customer_status()` L1745 | — |
| Reactivate customer | `customer.active` → 1 | ✅ YES | `customer_status()` L1745 | — |
| Un-complete profile | `customer.profile_complete` → 0 | ✅ YES | `profile_status()` L1733 | — |

> **Reversal completeness: ~50%** — Delete partially cleans up (misses KYC, images, audit log). Status toggles work but via CSRF-vulnerable GET.

---

## 5. Data Consistency Risks

### 5a. Wallet ↔ Customer Race Condition

| Scenario | Expected | Actual | Risk |
|---|---|---|---|
| Two concurrent customer adds | Each gets unique wallet number | `get_wallet_acc_number()` has no lock | 🔴 Duplicate wallet numbers possible |
| Customer add succeeds, wallet fails | Customer has wallet | Customer saved, wallet missing | 🟡 Wallet creation outside main transaction |

### 5b. Mobile Uniqueness ↔ Multi-Tenant

| Scenario | Expected | Actual |
|---|---|---|
| Same mobile, different company (`company_settings=1`) | Both allowed | ✅ `mobile_available()` scopes by `id_company` |
| Same mobile, single tenant (`company_settings=0`) | Rejected | ✅ No company filter applied |
| Same mobile via import vs admin add | Rejected | ⚠️ `isCustomerExist()` L579–586 has no company filter — may match wrong tenant's customer |

### 5c. ERP Sync Consistency

| Scenario | Risk |
|---|---|
| `insExisAcByMobile()` fails mid-loop | Some scheme_accounts created, others not — no transaction |
| `syncPayData()` fails mid-loop | Some payments inserted, others missed — no transaction |
| `updateInterTableStatus()` called after partial sync | Flags entire batch as synced, hiding missed records |

---

## 6. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-CUS-001 | Add customer → verify wallet created when `wallet_account_type=1` | Wallet record exists with unique number | 🔴 HIGH | ❌ |
| FR-CUS-002 | Add customer → two concurrent adds same mobile | Second add should be rejected | 🔴 HIGH | ❌ — No DB lock |
| FR-CUS-003 | Delete customer → verify KYC records cleaned | KYC table should have no records for deleted customer | 🔴 HIGH | ❌ — Known gap (CUS-BUG-006) |
| FR-CUS-004 | Delete customer → verify image files removed | `assets/img/customer/{id}/` should be deleted | 🟡 MED | ❌ — Known gap |
| FR-CUS-005 | Agent allocation → verify agents actually assigned | `customer.id_agent` should be updated | 🔴 HIGH | ❌ — Known broken (CUS-BUG-001) |
| FR-CUS-006 | Employee allocation → verify employees actually assigned | `customer.allocated_employee` updated | 🔴 HIGH | ❌ — Known broken (CUS-BUG-018) |
| FR-CUS-007 | Edit customer → change image → verify DB updated | `customer.cus_img` updated with new path | 🔴 HIGH | ❌ — Known broken (CUS-BUG-019) |
| FR-CUS-008 | SQL injection via search field | Should be sanitized | 🔴 CRITICAL | ❌ — Known SQLi (CUS-BUG-003) |
| FR-CUS-009 | Path traversal via download endpoint | Should reject `../../` in file parameter | 🔴 CRITICAL | ❌ — Known LFI (CUS-BUG-002) |
| FR-CUS-010 | ERP sync → partial failure → verify no orphan accounts | All-or-nothing sync | 🔴 HIGH | ❌ — No transaction |
| FR-CUS-011 | Toggle customer status via GET URL (CSRF test) | Should require POST + CSRF token | 🟡 MED | ❌ — Known CSRF (CUS-BUG-012) |
| FR-CUS-012 | Add customer with all KYC types → verify KYC records | Pan, Aadhar, Bank records all present | 🟡 MED | ❌ |
| FR-CUS-013 | Edit customer → KYC update when `kyc_exists()` returns true | Should UPDATE, not INSERT duplicate | 🟡 MED | ❌ |
| FR-CUS-014 | Profile quick update → verify audit log written | `log` table should have entry | 🟡 MED | ❌ |
| FR-CUS-015 | Customer limit reached → add attempt | Should block with clear message | 🟡 MED | ❌ |
| FR-CUS-016 | Customer with `get_customer()` → verify scheme count | Should show THIS customer's count, not customer 1 | 🔴 HIGH | ❌ — Known bug (CUS-BUG-007) |
| FR-CUS-017 | Wallet creation SMS failure → verify customer still saved | Customer should be saved even if SMS fails | 🟡 MED | ❌ |
| FR-CUS-018 | Delete customer with closed scheme accounts | Should allow deletion (only blocks on active+open) | 🟡 MED | ❌ |
