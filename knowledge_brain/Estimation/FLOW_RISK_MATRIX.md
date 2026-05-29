# Estimation Module — Flow Risk Matrix

> **Module**: Estimation
> **Date Built**: 2026-03-24 (Round 1)
> **Input**: DATA_FLOW.md + CROSS_MODULE_MAP.md
> **Purpose**: QA-ready contracts between modules — what must be guaranteed at each handoff

---

## 3b-1. State Machine: `ret_estimation.is_eda`

The main estimation entity (`ret_estimation`) does not have a formal status lifecycle, but the EDA flag functions as a mini state machine.

| State | Value | Set By (Method) | Can Transition To | Guard / Precondition |
|---|---|---|---|---|
| Normal Estimation | `is_eda = 0` | Default on save | EDA Pending (`is_eda=1`) | None — employee can check EDA box anytime |
| EDA Pending | `is_eda = 1` | `estimation('save')` via checkbox | Approved / Rejected | Manager must act in EDA queue |
| EDA Approved | Approval flag set | `edaApprove()` | — (no further state) | Manager role required |
| EDA Rejected | Rejection flag set | `edaReject()` | — (no further state) | Manager role required |

> ⚠️ **NO GUARD**: An employee can set `is_eda = 0` on re-edit after EDA approval — bypassing the entire approval workflow. No check exists to prevent clearing the EDA flag post-approval.

## 3b-1b. State Machine: `ret_taging.tag_status` (External Table — Tag Reservation)

The estimation module reads and depends on tag status but **does not write** to this field. This is an inbound contract risk.

| State | Value | Who Sets It | Risk to Estimation |
|---|---|---|---|
| Available | `0` | Tagging module | Estimation can add this tag — SAFE |
| Reserved | `1` | Tagging module (order reservation) | Estimation may add a reserved tag — **no server guard in Estimation** |
| Sold | `2` | Billing module | Tag sold but could still appear in search results until cache clears |
| Deleted | `3` | Tagging module | Orphan reference risk if tag deleted after estimation was saved |

> ⚠️ **TAG STATUS CHECK IS CLIENT-SIDE ONLY** (CROSS_MODULE_MAP.md, section 1): `tag_status` is not re-validated server-side before save. A sold or reserved tag can be added to an estimation if the user bypasses the JS check.

---

## 3b-2. Inbound Contracts (What Estimation Expects from Upstream)

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| Tagging | `ret_taging.tag_status = 0` (Available) | ⚠️ PARTIAL — JS check only, no server-side guard | JS L~1029 | Sold/reserved tag added to estimation → phantom revenue |
| Tagging | `ret_taging` record exists with valid `tag_id` | NO explicit server check before insert | — | Orphaned `ret_estimation_items` row referencing deleted tag |
| Tagging | `ret_taging_stone` records exist for tag | NO check | — | Missing stone data → NaN/zero stone totals in estimation |
| Tagging | Tag not transferred to another branch | NO check | — | Tag physically moved but estimation references old branch |
| Customer | `customer.id_customer` valid and active | YES — FK constraint (implicit DB level) | — | DB error on save (fail-closed) |
| Orders | `ret_order.id_order` valid if linked | NO explicit validation | — | Estimation linked to nonexistent/cancelled order |
| Metal Rates | `ret_branchwise_rate` has rate for purity on date | NO — returns null if rate missing | `get_branchwise_rate()` | Estimation saves with `rate=0` → zero sale value |
| Chit/Scheme | `scheme_account` open and has balance | ⚠️ PARTIAL — UI reads balance, no re-validate at save | — | Chit adjustment exceeds available balance |
| Day Closing | Branch not day-closed for estimation date | ⚠️ UNCLEAR — gate reads `ret_day_closing` but controller enforcement unclear | `getBranchDayClosingData()` L393 | Estimation saved on a closed day |
| Gift Voucher | Voucher not already redeemed | NO row-level lock | — | Double redemption across concurrent estimation/billing |
| Sales Return | SR bill exists and has unreturned balance | NO validation | — | SR credit > original bill amount silently accepted |

---

## 3b-3. Outbound Contracts (What Estimation Guarantees to Downstream)

| Downstream Module | What Estimation Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| Billing | `ret_estimation.total_cost` = correct total | Client-side JS calculations only; **no server-side recalculation** | Billing inherits wrong total → wrong invoice amount |
| Billing | All `ret_estimation_items` have valid `tag_id` reference | No explicit server validation at save | Billing references deleted/stale tag → orphan billing item |
| Billing | `ret_estimation.estbillid = NULL` (not yet billed) | No explicit check in Estimation — Billing enforces this | Estimation could be double-billed if Billing check fails |
| Billing | Stone totals in `ret_estimation_item_stones` match item `stone_wt` | No server validation — client-side only | Bill stone deductions are incorrect |
| EDA Queue | `ret_estimation.is_eda = 1` correctly flags discount-requiring estimations | Employee checkbox only — no range/threshold check | Discount bypassed: high discounts approved without oversight |
| Print Templates | PHP template reads same data as form saved | Model loads from DB — **but recalculates** VA/MC amounts in PHP | ⚠️ CONTRACT GAP — Printed total may differ from saved total if PHP and JS calculation logic diverge |
| Reports | `ret_estimation.estimation_datetime` is the actual creation date | Set server-side — SAFE | — |

---

## 3b-4. Reversal Contracts (Cancel / Edit / Delete)

> **Note**: Estimation has no "Cancel" operation. Edit = DELETE-then-INSERT pattern.

| Operation | Tables That Must Be Restored | Actually Restored in Code? | Method & Line | Gap? |
|---|---|---|---|---|
| Edit (re-save) | `ret_estimation_items` — deleted and re-inserted | ✅ YES — `deleteData` then `insertBatchData` | `estimation('save')` edit branch | No gap on success; **gap on partial failure** (EST-R601) |
| Edit (re-save) | `ret_estimation_item_stones` — deleted and re-inserted | ✅ YES | Same | Same partial failure risk |
| Edit (re-save) | `ret_estimation_item_other_materials` — deleted and re-inserted | ✅ YES | Same | Same |
| Edit (re-save) | `ret_estimation_other_charges` — deleted and re-inserted | ✅ YES | Same | Same |
| Edit (re-save) | `ret_estimation_old_metal_sale_details` — deleted and re-inserted | ✅ YES | Same | Same |
| Edit (re-save) | `ret_esti_old_metal_stone_details` — deleted and re-inserted | ✅ YES | Same | Same |
| Edit (re-save) | `ret_est_chit_utilization` — deleted and re-inserted | ✅ YES | Same | Same |
| Edit (re-save) | `ret_est_gift_voucher_details` — deleted and re-inserted | ✅ YES (added Round 2) | Same | Same |
| Edit (re-save) | `ret_est_other_metals` — deleted and re-inserted | ⚠️ UNVERIFIED — not in confirmed delete list | — | ⚠️ Possible orphan if not deleted on edit |
| Edit (re-save) | `ret_est_tag_merge` — deleted and re-inserted | ⚠️ UNVERIFIED — not in confirmed delete list | — | ⚠️ Possible stale merge records |
| Edit (re-save) | `ret_est_sales_return_utilization` — deleted and re-inserted | ⚠️ UNVERIFIED (added Round 2) | — | ⚠️ SR credits may stack on re-edit |
| Edit (re-save) | `ret_estimation_other_inventory_issue` — deleted and re-inserted | ⚠️ UNVERIFIED | — | ⚠️ Packaging items may duplicate |
| Transaction failure | ALL above tables — must rollback | ❌ NO — `trans_commit()` called in error branch (EST-R601) | `estimation('save')` L~88 of save branch | **CRITICAL GAP** — partial saves are permanently committed |
| Order tag cancel | `ret_order_items` cancel flag | ✅ YES | `cancel_order_tag()` | — |

---

## 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-EST-001 | Add sold/reserved tag to estimation (bypass JS check via direct POST) | Server REJECTS with clear error | 🔴 HIGH | ❌ |
| FR-EST-002 | Add tag that was deleted between search and save | Server REJECTS with clear error | 🔴 HIGH | ❌ |
| FR-EST-003 | Edit estimation after billing has been created from it | REJECT edit or warn that billing exists | 🔴 HIGH | ❌ |
| FR-EST-004 | Save estimation when transaction fails midway (simulate DB disconnect) | NO orphan records — full rollback | 🔴 HIGH | ❌ |
| FR-EST-005 | Edit estimation — verify ALL 13 child tables are deleted and re-inserted | All tables restored, no orphans | 🔴 HIGH | ❌ |
| FR-EST-006 | Save estimation with `is_eda=1`, approve via EDA, then re-edit and clear EDA flag | Should WARN or REJECT — EDA bypass check | 🟡 MED | ❌ |
| FR-EST-007 | Save estimation with chit scheme balance = 0 | REJECT — insufficient chit balance | 🟡 MED | ❌ |
| FR-EST-008 | Save estimation on a day-closed branch | REJECT with "day closed" error | 🔴 HIGH | ❌ |
| FR-EST-009 | Save estimation with gift voucher that was redeemed in billing concurrently | REJECT — voucher already used | 🔴 HIGH | ❌ |
| FR-EST-010 | Verify printed estimation total matches form total | Printed = saved = JS calculated | 🔴 HIGH | ❌ |
| FR-EST-011 | Save estimation with metal rate = NULL/missing for selected purity | REJECT or default to 0 with warning | 🟡 MED | ❌ |
| FR-EST-012 | Save estimation with SR credit > original bill amount | REJECT — invalid credit amount | 🟡 MED | ❌ |
| FR-EST-013 | Concurrent save of same estimation by 2 users | One succeeds, one gets conflict error | 🟡 MED | ❌ |
| FR-EST-014 | Edit estimation — verify `ret_est_other_metals` is deleted and re-inserted (MyISAM risk) | No orphan records in MyISAM table | 🔴 HIGH | ❌ |
| FR-EST-015 | Edit estimation — verify `ret_est_tag_merge` is cleaned up on re-save | No stale merge records | 🟡 MED | ❌ |
| FR-EST-016 | Billing created → estimation viewed → check totals match | All line items, stones, charges identical | 🔴 HIGH | ❌ |
| FR-EST-017 | `calculation_based_on` setting change → verify JS and PHP print template both use new mode | Print total = form total in new mode | 🔴 HIGH | ❌ |
| FR-EST-018 | Employee with `disc_limit_type = 1` (amount limit) saves estimation with discount > limit | REJECT at JS; verify server also rejects | 🟡 MED | ❌ |
