# BUSINESS RULES — chit_dashboard
> Round 1 — 2026-03-16

---

## RULE-DAS-001: Next Due Date Calculation

**Formula** (in `due_stat()` / `due_list()` model methods):
```
IF (is_opening = '1' AND no payments exist):
    next_due = last_paid_date + 1 MONTH

IF (is_opening = '0' AND no payments exist):
    next_due = scheme_account.date_add  (account creation date)

ELSE (payments exist):
    next_due = MAX(payment.date_payment) + 1 MONTH
```

**Implementation**: `dashboard_model::due_stat()` L445-481, `due_list()` L484-534 — both use identical subquery.

**Validation**: Server-side only (SQL CASE statement).

**Edge cases**:
- Account created but never paid, `is_opening=0` → overdue since `date_add`
- Account with `is_opening=1` carries a "legacy last paid date" from before system migration
- Multiple payments in same month: `MAX(date_payment)` picks latest

---

## RULE-DAS-002: Paid/Unpaid Percentage Display

**Formula** (in `payment_stat()` controller, L594-614):
```
paid_avg   = paid_amount / (paid_amount + unpaid_count) × 100
unpaid_avg = unpaid_count / (paid_amount + unpaid_count) × 100
```

> ⚠️ **CRITICAL BUG**: `paid` comes from `pymt_status('ALL')` → returns `SUM(payment_amount)` (a money value).
> `unpaid` comes from `pay_stat('ALL')['unpaid']` → returns `COUNT` of accounts (a count value).
> **Mixing money total with account count** in the denominator makes the % meaningless.

**Impact**: The `paid_avg` and `unpaid_avg` displayed on dashboard are mathematically invalid — they dividing ₹5,00,000 by (₹5,00,000 + 342) = ~99.93% which shows as "all paid" regardless.

**Location**: `admin_dashboard.php` L594-614 (controller), `dashboard_model::pymt_status` + `pay_stat`.

---

## RULE-DAS-003: Branch Visibility Filter (Dashboard-Specific)

**Rule**: The dashboard uses `dashboard_branch` (session, settable by admin per-refresh) as an OVERRIDE on top of the standard `branchWiseLogin` visibility:

```
IF dashboard_branch is set (≠ 0):
    Filter data to dashboard_branch only (overrides branchWiseLogin)
ELSE IF uid = 1 (admin):
    See all branches
ELSE IF branchWiseLogin = 1 AND id_branch set:
    See own branch + show_to_all=1 branches
ELSE:
    See all branches
```

**Implementation**: Applied as raw SQL concat in virtually every model method in `dashboard_model`.

**Edge case**: `dashboard_branch = 0` is treated as "not set" (i.e., no branch filter). Setting `dashboard_branch = 0` in session means "all branches."

---

## RULE-DAS-004: Closing Balance Calculation (Day Close / ajax_daily_collection)

**Formula** (controller L2460-2464):
```
closing_balance_amt = yesterday_closing_amt
                    + today_collection_amt
                    - today_closed_amt (amount-scheme closures)
                    - today_cancelled_amt

closing_balance_wgt = yesterday_closing_wgt
                    + today_collection_wgt
                    - today_closed_wgt (weight-scheme closures)
                    - today_cancelled_wgt
```

**Sources**:
- `yesterday_closing_*` → `services_model::daily_collection('get', $yesterday, '', $branch)`
- `today_collection_*` → `services_model::getTodaySummaryBranchWise(date, branch)`
- Result is NOT saved unless `dayClose()` is explicitly called

**Implementation**: `ajax_daily_collection()` L2386-2532 (DEPRECATED in JS though controller still exists).

---

## RULE-DAS-005: PDC Presentable vs Presented

**Rule**: PDC stats use two separate `payment_status` codes: 7 = presentable (upcoming), 2 = presented (already submitted).

**Implementation** (`pdc_stat()` L638-754):
```php
// CHQ presentable (status=7):
$data['chq_yp'] = $this->$model->pdc_report('Y','CHQ',7);

// CHQ presented/cleared (status=2):
$data['chq_ys'] = $this->$model->pdc_report('Y','CHQ',2);
```

**Time filters**: Y=Yesterday, T=Today, TW=This Week, TM=This Month, TT=Total-to-date

**Note**: PDC data is ONLY present in the old `index()` inline data (commented out in current version). PDC stats are not loaded by JS AJAX — this data path may be deprecated.

---

## RULE-DAS-006: Inter-Wallet Balance by Branch

**Formula** (in `inter_wallet_status()` L2214-2284):
```
net_balance_per_branch = SUM(credit transactions) - SUM(debit/redeem transactions)
```

**PHP-side merge**: The controller fetches all credits and all debits in two queries, then loops all branches and matches by `id_branch`. This is an N×M loop (branches × transactions).

**Edge case**: If a branch has no credit but has debit (or vice versa), it still appears with `credit=0` or `debit=0` respectively. Any branch in `allBranches()` that had no wallet activity still appears in the output.

---

## RULE-DAS-007: Renewal Identification

**Rule** (`renewal_stat()` in dashboard_model, L1182-1222): An account is flagged as "due for renewal" based on installment comparison:

```
Renewal accounts = accounts where paid_installments >= (total_installments - limit)
```

Where `limit` defaults to 25 (last 25 installments approaching maturity).

**Implementation**: `dashboard_model::renewal_stat($filterBy, $limit = 25, $offset = 0)` — paginated with limit/offset.

**Edge case**: `limit=25` is hardcoded in model default — changing this requires a model edit, not a config change.

---

## RULE-DAS-008: About-to-Close Threshold

**Rule** (`total_abt_to_cls($ins_type)`, L1099-1121): An account is "about to close" if:
- `is_closed = 0` (still active)
- Remaining installments ≤ `$ins_type` (either 1 or 2)

**Formula**:
```
remaining_installments = total_installments - paid_installments
about_to_close         = accounts WHERE remaining_installments <= ins_type
```

**Implementation**: Called by `get_closed()` controller method.

**Dashboard cards**: `one_pending` = accounts with 1 installment left, `two_pending` = accounts with 1 OR 2 installments left.

---

## RULE-DAS-009: Dashboard Access Gate

**Rule**: `index()` first calls `admin_settings_model::get_dashboard_access()` to get a list of dashboard widgets the current user is allowed to see. The `$data['dash_access']` variable controls which sections render in `dashboard.php`.

**Implementation**: `admin_dashboard.php` L132-140. The access model is auto-loaded globally (not in this controller's constructor).

**Edge case**: If `admin_settings_model` is not auto-loaded and is not in the constructor, calling `$this->admin_settings_model` will trigger a PHP fatal error. If it never errors, it's in `config/autoload.php`.
