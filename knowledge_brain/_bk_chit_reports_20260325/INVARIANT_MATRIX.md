# INVARIANT MATRIX — chit_reports
> Round 5 — 2026-03-16 | Purpose: Define behavioral invariants (always-true contracts) to detect silent regressions.

---

## What is an Invariant?

An **invariant** is a condition that must **always be true** regardless of inputs, filters, or client configuration. If any invariant breaks, there is a bug.

---

## Dimension 1: Financial Accuracy Invariants

| ID | Invariant | Broken When | Verification SQL |
|---|---|---|---|
| INV-F01 | **Sum of mode details = payment amount**: `SUM(payment_mode_details.payment_amount) = payment.payment_amount` for every multi-mode payment | Modes are deleted/inserted incorrectly during edit | `SELECT p.id_payment, p.payment_amount, SUM(pmd.payment_amount) as modes_sum FROM payment p LEFT JOIN payment_mode_details pmd ON pmd.id_payment=p.id_payment WHERE p.payment_status=1 GROUP BY p.id_payment HAVING modes_sum != p.payment_amount` |
| INV-F02 | **GST deduction is display-only**: `payment_amount` column NEVER changes when GST is deducted in reports — only the displayed value changes | `updatePaymentDetails` mistakenly writes GST-deducted value to DB | `SELECT id_payment, payment_amount, sgst, cgst FROM payment WHERE (payment_amount - sgst - cgst) < 0` |
| INV-F03 | **Reports total = DB total**: Sum displayed in report = `SUM(payment_amount)` from DB for same date/branch/filter | GST deduction bug, branch filter bug, or `$pay` uninitialized | Run report total vs DB sum for same filter |
| INV-F04 | **Cancelled payment balance**: A cancelled payment (`payment_status=4`) MUST have a matching row in `payment_status_log` | `cancel_payment` fails after DB update but before log insert (XMOD-001) | `SELECT p.id_payment FROM payment p WHERE p.payment_status=4 AND p.id_payment NOT IN (SELECT id_payment FROM payment_status_log)` |
| INV-F05 | **Opening balance preserved**: Accounts with `is_opening=1` show `balance_amount` + actual payments, never just actual payments | Balance carry-forward logic stripped from query | Compare report total for `is_opening=1` accounts vs `balance_amount + SUM(p.payment_amount)` |

---

## Dimension 2: Data Visibility Invariants

| ID | Invariant | Broken When | Test |
|---|---|---|---|
| INV-V01 | **Branch-wise login hides other branches**: When `branchWiseLogin=1 AND branch_settings=1`, user in Branch A sees ONLY Branch A payments + `show_to_all=1` branches | SQL `id_branch` filter missing or AND/OR logic wrong | Log into Branch A user, run payment_list_daterange, verify no Branch B records |
| INV-V02 | **Admin sees all branches**: User with `uid=1` (Admin) sees all branches regardless of `branchWiseLogin` setting | Admin branch gate breaks | Log as admin uid=1, check all branches present |
| INV-V03 | **Cancelled payments excluded from collection totals**: `payment_outstanding_list`, `payment_list_daterange`, `paydatewise_schemecoll_list` NEVER include `payment_status=4` records | Status filter missing from where clause | Manually cancel a payment, check it disappears from collection reports |
| INV-V04 | **`show_to_all=1` branches always visible**: Branches with `show_to_all=1` appear in all reports regardless of user's branch | `show_to_all` flag not in SQL | Verify a `show_to_all=1` branch appears for branch-restricted user |
| INV-V05 | **KYC updates only touch the specific customer's records**: `update_kyc` updates exactly the `id_kyc` records in the POST array | Loop iterates wrong IDs | Check `customer_kyc.last_update` timestamps after bulk approve — only target records should change |

---

## Dimension 3: Write Safety Invariants

| ID | Invariant | Broken When | Location |
|---|---|---|---|
| INV-W01 | **`cancel_payment` only changes status to 4**: No other fields of `payment` table changed during cancel | Extra fields accidentally included in update array | Review `payment_model::payment_cancel()` — check UPDATE set clause |
| INV-W02 | **`updatePaymentDetails` updates only the specified `id_payment`**: The WHERE clause must use the specific ID from POST | Raw POST overwrite injects wrong ID | Review `payment_model::updatePaymentdata()` WHERE clause |
| INV-W03 | **`updateAccountDetails` requires branch validation**: `checkCommonSettings()` must pass before any write | `checkCommonSettings` returns falsy but write proceeds | Check L1726-1755 flow — ensure early return on validation failure |
| INV-W04 | **Purchase OTP delivery is idempotent**: `purch_delivered` only flips `is_delivered=1` — not re-deliverable | Missing `is_delivered` check before flip | `SELECT id_purch_payment, is_delivered FROM purch_payment WHERE id = :id` |
| INV-W05 | **Log files written to `admin/log/` only**: No path traversal via date variable | `date("Y-m-d")` is system-controlled — this invariant holds | ✅ Already verified: date param is PHP-generated |

---

## Dimension 4: Session / Auth Invariants

| ID | Invariant | Broken When | Check |
|---|---|---|---|
| INV-A01 | **All methods require `is_logged` session**: Constructor gate at L31-33 must redirect on missing session | Session gate removed or bypassed | Direct URL access to any endpoint without session — must redirect to login |
| INV-A02 | **`branch_settings` session value drives report filtering**: If session `branch_settings=0`, ALL branch data visible | Session value out of sync with DB settings | Clear session, log in fresh, verify branch filter matches `chit_settings.branchWiseLogin` |
| INV-A03 | **Access control matches `get_access()` result**: No hidden menu items bypassed by direct URL | `get_access()` returns false but controller still serves data | Navigate directly to restricted report URL — must show access denied or blank |

---

## Dimension 5: Model Call Invariants

| ID | Invariant | Expected | Broken By |
|---|---|---|---|
| INV-M01 | **PAY_MODEL is always `payment_model`**: Constructor assigns `constant('self::PAY_MODEL')` → every method call uses the right model | Naming collision if another model is loaded with same constant | Review constructor L15-35 for constant assignments |
| INV-M02 | **`closedaccount_list` uses PAY_MODEL (not ACC_MODEL)**: Despite `$model = ACC_MODEL` at L1219, next line immediately overwrites to PAY_MODEL → both summary + list queries use PAY_MODEL | Adding a model call between L1219 and L1220 | Bug documented — `$model` at L1219 is dead code |
| INV-M03 | **`kycapproval_data()` second definition wins**: PHP uses last definition of duplicate method name | Calling the first definition (L906) which is dead code | PHP runtime always picks last def — only broken if class structure changes |

---

## Dimension 6: Configuration-Driven Behavior Grid

| Config Column | Value 0 | Value 1 | Affected Methods |
|---|---|---|---|
| `gst_setting` | GST not deducted from display | GST deducted from display amount | `payment_list_daterange`, `payment_modewise_list`, `payment_datewise_list`, `payments_on_off_collection_list` |
| `has_lucky_draw` | Lucky draw columns hidden in account reports | Lucky draw group info shown | `scheme_summary`, `is_luckly_draw_scheme`, `scheme_group_summary_data` |
| `branchWiseLogin` | All branches visible to all users | Branch-restricted view (only own branch + show_to_all) | All report list methods with `id_branch` filter |
| `branch_settings` | Branch feature off — no filtering | Branch feature active — filter applies | All report list methods |
| `gst_type` | GST-inclusive (deduct from `payment_amount` for display) | GST-exclusive (display `payment_amount` as-is) | Same as `gst_setting` methods |
| `edit_custom_entry_date` | Date locked to system date | Admin can set custom entry date on payment edit | `updatePaymentDetails`, `editAccOrPayments` |

---

## Dimension 7: Edge Case Registry

| ID | Scenario | Expected Behavior | Actual Behavior / Bug |
|---|---|---|---|
| EC-01 | Date range `Dec 25 → Jan 10` in celeb dates report | Should find birthdays across year boundary | ❌ `%m%d BETWEEN 1225 AND 0110` fails — no results returned |
| EC-02 | `payment_list_daterange` with `gst_type=1` (exclusive) | `$pay = payment_amount` (no deduction) | ❌ `$pay` never assigned → PHP undefined var notice → report may show `0` |
| EC-03 | `scheme_daily_collection_details` first iteration | `$today['collection']` access at L1163 before assignment at L1173 | ❌ PHP warning — `$today` uninitialized |
| EC-04 | Cancel payment with Khimji integration enabled (`integrationType=2`) | Sync to Khimji before log insert | ⚠️ If Khimji API fails, payment is cancelled in DB but sync fails — no rollback |
| EC-05 | Admin edits payment with empty POST | `updatePaymentDetails` skips write? | ⚠️ No empty POST guard found — `payment_model::updatePaymentdata($_POST)` would write empty array |
| EC-06 | Customer with both birthday and wedding date in same date range | Should appear once per type | ✅ Two separate OR clauses — customer appears twice (once for DOB, once for wedding) |
| EC-07 | `closedaccount_list` with no data in date range | Empty array returned | ✅ Both model calls return empty arrays — `json_encode` returns `{accounts:[], closed_summary:[]}` |
| EC-08 | `scheme_summary` with zero schemes in date range | Empty return | ✅ `scheme_summary_data()` returns empty array → `$return_data` unset → `$data['scheme_summary']` = `[]` |
| EC-09 | `update_kyc` with `kyc_type` not 1 or 2 | No update performed | ⚠️ No default/else branch — silently does nothing |
| EC-10 | `generateTransUniqId` called for already-assigned `offline_tran_uniqueid` | Should be idempotent | ⚠️ No check for existing `offline_tran_uniqueid` before Khimji API call — may create duplicate |
