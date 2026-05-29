# BUSINESS RULES — chit_reports
> Round 1 — 2026-03-14 | Updated Round 5 — 2026-03-16 (Rules 9-11 added)

---

## RULE-RPT-001: GST Deduction from Payment Amount

**Formula**: If `gst_type = 0` AND `gst_setting = 1` → display amount = `payment_amount - (sgst + cgst)`  
**Otherwise**: display amount = `payment_amount` as-is

**Implementation**: PHP controller-side  
- `payment_list_daterange` L264-271  
- `payment_modewise_list` L344-346  
- `payment_datewise_list` L382-384  
- `payments_on_off_collection_list` L1088-1090  
- `paydatewise_schemecoll_list` (no GST deduct — shows raw amount)

**Validation**: PHP only (no client-side GST calc in reports)  
**Edge cases**: `$pay` variable is ONLY assigned in the `if` branch — if condition is false, `$pay` falls through to `payment_modewise_list` L349 as undefined, causing PHP notice. Bug in modewise list.

**GST values**: `sgst` and `cgst` stored in `payment` table per record, formatted to 3 decimal places via `sprintf("%.3f", ...)`

---

## RULE-RPT-002: Payment Status Codes

| Code | Meaning | Notes |
|---|---|---|
| `0` | Pending | Online payment pending confirmation |
| `1` | Success | Confirmed payment |
| `2` | Rejected | Rejected by gateway |
| `4` | Cancelled | Admin-cancelled in reports |
| `-1` | Failure | Gateway failure |

**Documented at**: `admin_reports.php` L36 (comment)  
**Used in DB queries**: `WHERE payment_status IN (0,1)` for reports, `WHERE payment_status = 1` for totals

---

## RULE-RPT-003: Installment Counting (Weight vs Amount Schemes)

**Amount Schemes** (`scheme_type=0`): Count = `SUM(no_of_dues)` — tracks number of dues per payment record  
**Weight Schemes** (`scheme_type=1`)**: Count = `COUNT(DISTINCT DATE_FORMAT(date_payment,'%Y%m'))` — counts unique months paid  
**Flexible Schemes** (`scheme_type=3`) with `payment_chances=1`: Uses `COUNT(DISTINCT month)` logic  

**Why**: Weight scheme customers can pay multiple times per month (partial weights), but it counts as 1 installment for that month.

**Implementation**: SQL subqueries in `account_model::get_all_account_by_range` and `payment_model` methods  
**Edge cases**: Opening balance (`is_opening=1`) accounts carry pre-loaded `paid_installments` from `scheme_account.balance_amount`

---

## RULE-RPT-004: Branch Visibility Rule

**Formula**: A payment/account is visible to logged-in user if:
- `branch_settings = 0` (branch feature off) → show all
- `branch_settings = 1` (branch on) + `branchWiseLogin = 1` → show only `id_branch = user_branch` OR `branch.show_to_all = 1`
- Admin (uid=1) → always show all

**Implementation**: `account_model::getAmountSchemeAccounts` L220-237, `get_all_closed_account` L731-735  
**Edge case**: `show_to_all=1` branches visible to everyone regardless of branch filter

---

## RULE-RPT-005: Opening Balance Carry-Forward

**Formula**: For accounts with `is_opening=1`, the `balance_amount` and `balance_weight` in `scheme_account` represent payments made in the previous system (before migration).

These are added to current payment totals:
- `total_amount = balance_amount + SUM(payment.payment_amount)`
- `total_weight = balance_weight + SUM(payment.metal_weight)`

**Implementation**: SQL in `account_model::get_all_account_details` L551-557  
**Impact**: Reports may show higher totals for migrated accounts than what's in the `payment` table alone

---

## RULE-RPT-006: Collection Report Closing Balance Formula

**Formula** (from `scheme_daily_collection_details`, controller L1186-1188):
```
closing_balance_amt = op_blc_amt 
                    + today_collection_amt 
                    + previous_blc_balance_amount 
                    - closing_paid_amt 
                    + closing_add_chgs

closing_balance_wgt = op_blc_weight 
                    + today_collection_wgt 
                    + previous_blc_balance_weight 
                    - (if scheme_type 2 or 3: closing_balance else 0)
```

**Known Bug**: Line L1163 accesses `$today['collection']` BEFORE `$today` is assigned at L1173. This causes `sizeof($today['collection'])` to throw PHP warning on first iteration of the scheme loop.

**Implementation**: `scheme_daily_collection_details` L1154-1208  
**Called by**: `collection_report` view via JS `scheme_daily_collection_details`

---

## RULE-RPT-007: Lucky Draw Scheme Account Number Format

**Formula**:
- If `has_lucky_draw=1` AND `is_lucky_draw=1`: display as `{group_code}-{start_year}{scheme_acc_number} - {code}`
- Otherwise: `{code} {start_year}{scheme_acc_number}`

**Implementation**: `account_model::getAmountSchemeAccounts` SQL L206-226  
**Configuration**: Driven by `chit_settings.has_lucky_draw` and `scheme.is_lucky_draw`

---

## RULE-RPT-008: KYC Verification Rule

**Formula**: A customer's `kyc_status` is set to 1 (verified) only when ALL their KYC documents are verified (`verified_kycs = 1` count returned from model).

**Implementation**: `update_kyc` L944-948  
```php
if ($result['verified_kycs'] == 1) {
    $update = ["kyc_status" => 1];
    $result = $this->$model->updatekyccus($update, $data['cus']);
}
```
**Edge case**: `verified_kycs` check is a count (not boolean) — if model returns count > 1, condition still works. But if model returns `true/false` instead of count, condition fails silently.

**Write tables**: `customer_kyc` (type 1), `agent_kyc` (type 2), `customer` / `agent` (status update)

---

## RULE-RPT-009: Celebration Date Cross-Year Boundary

**Formula**: `get_all_cus_celeb_dates()` uses `DATE_FORMAT(date_of_birth, '%m%d') BETWEEN DATE_FORMAT(:from, '%m%d') AND DATE_FORMAT(:to, '%m%d')` to find annual occurrences.

**Behavior**:
- ✅ Works correctly when `from_month ≤ to_month` (e.g., March 1 → March 31)
- ❌ **Fails silently** when `from_month > to_month` (e.g., Dec 25 → Jan 10) — `%m%d` BETWEEN `1225` AND `0110` finds zero results because `1225 > 0110` in numeric comparison

**Impact**: Admins cannot generate birthday/anniversary reports for year-crossing date ranges (e.g., Dec–Jan new year window). No error shown — returns empty result.

**Fix**: Split query into two UNION parts: `(m%d >= from_mmdd)` OR `(%m%d <= to_mmdd)` when from > to.

**Location**: `admin_report_model.php` L576-577

---

## RULE-RPT-010: Scheme Summary 4-Query Merge Contract

**Formula**: `scheme_summary_data()` in `account_model` calculates outstanding balance using four sequential DB queries + PHP-side merge:

```
opening_amount      = oldcollection_amt - oldclosed_amt
current_collection  = newcollection_amt  
balance             = opening_amount + newcollection_amt - newclosed_amt
```

**Performance Contract**: This runs **4 SQL queries × n schemes** in PHP loop — O(n × 4m) complexity. On clients with 50+ schemes and 10,000+ accounts each sub-query, this will time out or return partial data.

**Implementation**: Called by `scheme_summary` controller L1557-1578 using `account_model::scheme_summary_data()`.

**Known Redundancy**: The `if ($r['is_lucky_draw'])` / `else` branch in the controller both execute identical code (`$return_data[$r['code']][] = $r`) — the if/else serves no current purpose. Lucky draw schemes DO get `group_scheme` populated as an extra key, but the array keying is identical.

---

## RULE-RPT-011: Admin Edit Audit Log Mandate

**Rule**: Every call to `updatePaymentDetails` and `updateAccountDetails` MUST write a log entry to `admin/log/` BEFORE writing to the database.

**Format**:
```
log/payment{YYYY-MM-DD}.txt  ← updatePaymentDetails
log/account{YYYY-MM-DD}.txt  ← updateAccountDetails  
```

**Content**: `json_encode($_POST, true)` — full raw POST including customer mobile, payment amount, branch, etc.

**Security Status**: ⚠️ CONFIRMED CRITICAL — `admin/log/` is web-accessible (no `.htaccess`). Log files contain PII. See MODULE_BRAIN.md Risk #14.

**Location**: `admin_reports.php` L1717-1720 (`updatePaymentDetails`), L1726-1730 (`updateAccountDetails`)

**Fix Required**: Add `admin/log/.htaccess` with `Deny from all` immediately.
