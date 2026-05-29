# FLOW RISK MATRIX — chit_dashboard

> **Round**: R2-Upgrade | **Date**: 2026-03-25
> **Primary Entity**: None owned — this is a **read-only aggregation module**
> **Write Paths**: Only 2 — `customer_edit()` (customer table) and `dayClose()` (daily_collection table)

---

## 1. State Machine

> The dashboard module does **not own any primary entity** — it reads state from other module's tables. However, it does expose state transitions via:

### `customer.status` (Write via customer_edit — L2578)
| State | Value | Set By | Guard |
|---|---|---|---|
| Active | 1 | `customer_edit()` → `updateData()` | ⚠️ NO GUARD — raw POST data written directly |

### `daily_collection.is_day_closed` (Write via dayClose — L2925)
| State | Value | Set By | Guard |
|---|---|---|---|
| Open | 0 | Default | — |
| Closed | 1 | `dayClose()` → model update | ✅ Checks current date closing status |

> ⚠️ Dashboard reads `payment.payment_status`, `scheme_account.is_closed`, and `scheme_account.active` from 20+ methods **without ever validating or writing** these fields.

---

## 2. Inbound Contracts (What Dashboard Expects from Upstream)

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| **Payment** | `payment.payment_status` values = {1, 2, 4} | NO — hardcoded in SQL WHERE | All model methods | Unknown status values silently excluded from stats |
| **Payment** | `payment.payment_amount` is numeric, not NULL | NO check | Model L652+ | SUM returns NULL, breaks chart display |
| **Account** | `scheme_account.is_closed` ∈ {0, 1} | NO — used in `=0` filters | Model L94+ | Accounts with NULL is_closed excluded from counts |
| **Account** | `scheme_account.active` ∈ {0, 1} | NO — used in `=1` filters | Model L390+ | Inactive accounts hidden silently |
| **Account** | `scheme_account.scheme_acc_number` NOT string 'null' | PARTIAL — `!= 'null'` check | Model L462 | String 'null' vs SQL NULL mismatch |
| **Scheme** | `scheme.scheme_type` ∈ {0, 1, 2, 3} | NO — labels hardcoded per value | Controller L998+ | Unknown type gets blank/wrong label |
| **Customer** | `customer.mobile` is unique, not NULL | NO check | Controller L2578 | `get_cust($mobile)` may return wrong customer |
| **Settings** | `admin_settings_model` is globally autoloaded | NO — not loaded in constructor | Controller L132 | `index()` crashes with "undefined model" |
| **Branch** | `branch.show_to_all` ∈ {0, 1} | NO — assumed in SQL | Model throughout | All-branch visibility logic breaks |
| **Employee** | `employee.id_employee` valid FK | NO — used in customer_status() | Controller L2781 | Employee-wise breakdown shows deleted employees |

---

## 3. Outbound Contracts (What Dashboard Guarantees to Downstream)

| Downstream Module | What Dashboard Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| **Customer** (via customer_edit) | customer row updated with form POST data | ❌ No field whitelist on updateData() | ⚠️ CONTRACT GAP — any POST field is written directly to customer table |
| **Services** (via dayClose) | daily_collection row created for branch/date | PARTIAL — checks if already closed | Day-close re-run may create duplicate entries |
| **SMS** (via send_customer_wishes) | SMS sent for birthday/wedding customers | ❌ No idempotency — re-sending doubles SMS | Customers receive duplicate wishes |

> ℹ️ Dashboard is primarily a **consumer** not a **producer**. Most of its "outbound" risk is unintended side effects from its 2 write paths.

---

## 4. Reversal Contracts (Cancel/Delete/Reverse)

| Operation | Tables That Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| Undo customer_edit | customer row | ❌ NO undo mechanism | — | ⚠️ No audit trail — previous values lost |
| Undo dayClose | daily_collection.is_day_closed → 0 | ❌ NO reopen mechanism | — | ⚠️ Once closed, day cannot be reopened from dashboard |
| Undo send_customer_wishes | SMS already sent | ❌ Cannot unsend | — | ⚠️ Irreversible — no send log checked before re-send |

> **Reversal completeness: ~0%** — None of the dashboard's 3 write operations have undo/reverse mechanisms.

---

## 5. Data Consistency Risks

### 5a. Branch Filter Consistency

The `dashboard_branch` session variable controls ALL data visibility:

| Scenario | Risk | Severity |
|---|---|---|
| Two browser tabs, different branches selected | Session overwrites — last branch wins for both tabs | 🟡 MED |
| `uid=1` (super admin) | Bypasses ALL branch filters — sees everything | 🟡 LOW (by design) |
| `branchWiseLogin=0` | Branch filter ignored — all branches visible | 🟡 LOW (by design) |
| `dashboard_branch` set to empty string | Some methods treat `''` as "no filter", others as "filter by branch 0" | 🟡 MED |

### 5b. Mixed Data Source Risk

| Stat Widget | Data Source | Mismatch |
|---|---|---|
| "Paid" amount | `pymt_status()` — SUM(payment_amount) | ✅ Correct metric |
| "Unpaid" count | `pay_stat()` — COUNT via subquery | ⚠️ Counts accounts, not amount — comparing quantity to value |
| "Paid/Unpaid %" | `paid / (paid + unpaid) × 100` | 🔴 MATHEMATICALLY INVALID — numerator is rupees, denominator mixes rupees + account count |

---

## 6. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-DASH-001 | Open dashboard with `uid=1` (super admin) | All branches visible, no filter | 🟡 MED | ❌ |
| FR-DASH-002 | Open dashboard with branch employee (`branchWiseLogin=1`) | Only own branch + `show_to_all=1` branches visible | 🔴 HIGH | ❌ |
| FR-DASH-003 | Two browser tabs → select different branches | Both tabs should maintain independent context | 🟡 MED | ❌ — Known to fail (session conflict) |
| FR-DASH-004 | Click "Today's Due" count → verify drilldown matches card count | Numbers must match exactly | 🔴 HIGH | ❌ |
| FR-DASH-005 | Due count for account with `is_opening=1` and no payments | Due date = `last_paid_date + 1 month` | 🔴 HIGH | ❌ |
| FR-DASH-006 | Due count for account with `is_opening=0` and no payments | Due date = `scheme_account.date_add` | 🔴 HIGH | ❌ |
| FR-DASH-007 | "Last Week" filter on any stat widget | Query must NOT fail with garbled `â€"` | 🔴 CRITICAL | ❌ — Known to fail |
| FR-DASH-008 | Payment chart shows paid/unpaid % | % should be based on comparable metrics (both amounts or both counts) | 🔴 HIGH | ❌ — Known invalid formula |
| FR-DASH-009 | Customer edit via GET URL without CSRF | Should reject or show warning | 🔴 HIGH | ❌ — Known no CSRF |
| FR-DASH-010 | Edit customer → raw POST to updateData() | Only whitelisted fields should be updated | 🔴 HIGH | ❌ — Known no whitelist |
| FR-DASH-011 | N+1 in cust_wo_accounts_details() with 1000+ customers | Page should load within 5 seconds | 🔴 PERF | ❌ — Known N+1 |
| FR-DASH-012 | Inter-wallet status with multiple branches having credits | Each branch should show its own credit, not last-in-loop | 🟡 MED | ❌ — Known missing break |
| FR-DASH-013 | Day close → re-run on same day | Should block or warn — not create duplicate entries | 🟡 MED | ❌ |
| FR-DASH-014 | Add new `payment_status` value (e.g., 5) | Dashboard should either show it or explicitly list unknown statuses | 🟡 MED | ❌ — Silently excluded |
| FR-DASH-015 | Add new `added_by` value (e.g., 4) | Source-wise chart should show "Unknown" category | 🟡 MED | ❌ — Silently excluded |
| FR-DASH-016 | Send birthday wishes → click again for same day | Should not re-send to already-wished customers | 🟡 MED | ❌ — No dedup |
| FR-DASH-017 | APK upload with `.exe` file | Should reject non-APK files | 🔴 HIGH | ❌ — Known no validation |
| FR-DASH-018 | `admin_settings_model` removed from autoload | `index()` should not crash | 🟡 LOW | ❌ |
