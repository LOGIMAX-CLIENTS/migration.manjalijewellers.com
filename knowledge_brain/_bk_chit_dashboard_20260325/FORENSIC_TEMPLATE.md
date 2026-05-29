# FORENSIC TEMPLATE — chit_dashboard
> Round 1 — 2026-03-16 | Layer-by-layer bug investigation cheat sheet

---

## Layer 1 — Symptom Collection (Dashboard-Specific Checklist)

When a bug is reported for the dashboard, collect these first:

- [ ] **Which widget/section** is broken? (Summary cards / Payment chart / Account chart / Wallet / Collection / Due list / Drilldown page)
- [ ] **Which user role?** (Super admin uid=1 / branch-restricted user / read-only)
- [ ] **Which branch filter?** (All branches / specific branch name — note `dashboard_branch` session)
- [ ] **Which date range?** (Today / Yesterday / This Week / This Month / Custom range)
- [ ] **Is it a blank widget or wrong data or error?** (Blank = AJAX not responding; Wrong = query bug; Error = JS or PHP exception)
- [ ] **Does it happen on first load or after changing branch/date?** 
- [ ] **Does it affect all users or just specific branch users?**
- [ ] **Is the issue on the summary card or the drilldown page?** (These load via separate routes)

---

## Layer 2 — Reproduce & Isolate

**Step 1**: Open browser console → Network tab → reload dashboard → look for AJAX calls returning non-200 or empty.

**Step 2**: Filter Network by XHR. Identify which AJAX call is failing:
```
admin_dashboard/dashboard           → summary cards
admin_dashboard/payment_status      → payment chart
admin_dashboard/account_status      → account chart
admin_dashboard/inter_wallet_status → wallet chart
admin_dashboard/ajax_collectionData → collection table
admin_dashboard/getsource_wiserrecord → source-wise chart
```

**Step 3**: Replicate in Incognito as different user to rule out session/cache:
- Logged in as uid=1 (admin) → `dashboard_branch` = null → sees all
- Logged in as branch user → `branchWiseLogin=1`, `id_branch=X` → sees filtered

**Step 4**: Check if "Last Week" filter is selected → if yes, that's the garbled SQL bug (SCHEMA_ANALYSIS.md Part D). Replace with another time filter to confirm.

---

## Layer 3 — Client-Side Trace

**Key JS variables to inspect in console**:
```javascript
// Dashboard state
base_url           // Must end with /admin/
my_Date            // Used for cache-busting timestamps
dashboard_branch   // After branch filter selection

// Payment chart response
data.payment_status.paid
data.payment_status.unpaid
data.admin_paid.joined_thro
data.mob_paid.joined_thro

// Account chart response
data.account_stat
data.acc_wo_pay

// Collection response
result.pay_collection  // Array of {code, scheme_name, collection, opening_bal}
result.type            // 1=today (live calc), 2=historical (from DB)
```

**Common JS errors to check**:
- `Uncaught TypeError: Cannot read property of undefined` → AJAX response structure changed
- `NaN` in chart values → division by zero in `paid_avg` / `unpaid_avg` calculation
- Empty DataTable → check if `data.accounts` is null/empty vs `data.customer`/`data.payments`

**Network inspection for cross-module calls**:
- `rate/ajax/weekstat` → if fails, gold rate chart stays empty (non-critical)
- `branch/branchname_list` → if fails, branch dropdown empty (HIGH impact)
- `admin_customer/get_customer_by_mobile` → if fails, customer edit search breaks

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Summary cards blank | `admin_dashboard.php` | `dashboard()` | L106-128 | Is `dashboard_branch` session set? Check `get_closed()`, `get_renewals()`, `get_existing_request()`, `get_feedback()` — are they all returning data? |
| Payment chart empty/wrong | `admin_dashboard.php` | `payment_status()` | L1820-1856 | Are `from_date`/`to_date` POST params arriving? What does `dashboard_model::payment_status($from,$to)` return? |
| Collection table wrong | `admin_dashboard.php` | `ajax_collectionData()` | L2326-2346 | What `date` param arrives? Does `paydatewise_schemecoll($date)` have data for that date? |
| Due count wrong | `dashboard_model.php` | `due_stat($filterBy)` | L445-481 | The next_due subquery — add `echo $sql; exit;` to dump SQL and test in MySQL directly |
| "Last Week" error | `dashboard_model.php` | Any `switch('LW')` | Various | Garbled `â€"` char in SQL — replace with `-` in that specific method |
| Branch filter not working | `dashboard_model.php` | Any stat method | Various | Check `$dashboard_branch`, `$uid`, `$branchWiseLogin`, `$id_branch` — add debug dumps |
| Customer edit not saving | `admin_dashboard.php` | `customer_edit()` | L2578-2767 | Check `dashboard_model::updateData()` — what keys are in `$_POST`? Is WHERE clause correct? |
| Paid% shows ~100% always | `admin_dashboard.php` | `payment_stat()` | L594-614 | Mixed data sources bug (RULE-DAS-002) — `paid` is SUM of amounts, `unpaid` is COUNT of accounts |
| APK upload fails | `admin_dashboard.php` | `upload()` | L3304-3343 | Check `APK_UPLAOD_PATH` constant, disk permissions on `master/upload_apk/` |

---

## Layer 5 — Database Verification

```sql
-- Q1: Check today's confirmed payments for a branch (verify payment chart)
SELECT COUNT(*) as count, SUM(payment_amount) as total
FROM payment p
JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
WHERE DATE(p.date_payment) = CURDATE()
AND p.payment_status = 1
AND sa.id_branch = :test_branch_id;

-- Q2: Check today's due accounts
SELECT sa.id_scheme_account, sa.scheme_acc_number,
    CASE
        WHEN sa.is_opening = '1' AND (SELECT MAX(p2.date_payment) FROM payment p2 WHERE p2.id_scheme_account = sa.id_scheme_account AND p2.payment_status = 1) IS NULL
            THEN DATE_ADD(sa.last_paid_date, INTERVAL 1 MONTH)
        WHEN (SELECT MAX(p2.date_payment) FROM payment p2 WHERE p2.id_scheme_account = sa.id_scheme_account AND p2.payment_status = 1) IS NULL
            THEN sa.date_add
        ELSE DATE_ADD((SELECT MAX(p2.date_payment) FROM payment p2 WHERE p2.id_scheme_account = sa.id_scheme_account AND p2.payment_status = 1), INTERVAL 1 MONTH)
    END AS next_due
FROM scheme_account sa
WHERE sa.active = 1 AND sa.is_closed = 0
HAVING DATE(next_due) = CURDATE();

-- Q3: Verify renewal count (accounts in last 25 installments)
SELECT COUNT(*) FROM scheme_account sa
JOIN scheme sc ON sc.id_scheme = sa.id_scheme
WHERE sa.active = 1 AND sa.is_closed = 0
AND (sc.total_installments - sa.paid_installments) <= 25;

-- Q4: Check inter-wallet balance by branch
SELECT b.branch_name, 
    SUM(CASE WHEN iw.type='credit' THEN iw.amount ELSE 0 END) as credits,
    SUM(CASE WHEN iw.type='debit' THEN iw.amount ELSE 0 END) as debits
FROM inter_wallet iw
JOIN branch b ON b.id_branch = iw.id_branch
WHERE DATE(iw.date_add) BETWEEN :from_date AND :to_date
GROUP BY b.id_branch;

-- Q5: Confirm customer record before/after edit
SELECT id_customer, firstname, lastname, mobile, profile_complete, active
FROM customer WHERE id_customer = :id_customer;

-- Q6: Check for accounts with 1 or 2 installments remaining (about-to-close)
SELECT COUNT(*) FROM scheme_account sa
JOIN scheme sc ON sc.id_scheme = sa.id_scheme
WHERE sa.active = 1 AND sa.is_closed = 0
AND (sc.total_installments - sa.paid_installments) <= 2;
```

---

## Layer 6 — Root Cause Classification

| Category | Risk | Examples |
|---|---|---|
| SQL injection | 🔴 CRITICAL | `$dashboard_branch`, `$id_branch` raw concat in 50+ model methods |
| Data source mismatch | 🔴 HIGH | Paid/unpaid % mixes SUM(amount) with COUNT(accounts) |
| Garbled SQL syntax | 🔴 HIGH | LW (Last Week) filter fails in 7+ methods due to `â€"` char |
| N+1 query loop | 🔴 PERF | `cust_wo_accounts_details()` calls 2 model methods per customer |
| CSRF on GET route | 🔴 HIGH | `customer_edit($mobile)` is GET-accessible |
| Session race condition | 🟡 MED | `dashboard_branch` session wiped on every `dashboard()` call |
| Missing error branches | 🟡 MED | `inter_wallet_status()` missing break — last-wins overwrites |
| Dead code | 🟡 LOW | `getsource_wiserrecord_old()`, `enquiry_report()`, `enquiry_detail_report()` |
| APK upload unvalidated | 🔴 HIGH | `upload()` no file type check |

---

## Layer 7 — Integration Point Trace (Cross-Module AJAX)

When a dashboard widget breaks due to a cross-module AJAX dependency:

| Failed Widget | Cross-Module Endpoint | What Breaks | How to Verify |
|---|---|---|---|
| Gold rate chart blank | `rate/ajax/weekstat` | Rate chart stays empty — non-critical | Open directly: `/admin/rate/ajax/weekstat` |
| Branch dropdown empty | `branch/branchname_list` | Cannot filter by branch — HIGH | Open: `/admin/branch/branchname_list` |
| Customer mobile search fails | `admin_customer/get_customer_by_mobile` | Customer edit search broken | Test: POST to endpoint with `mobile` param |
| Retail orders widget missing | `admin_ret_dashboard/get_customer_order_details` | Retail widget empty — usually client-specific | Check if retail module enabled |
| Payment mode-wise wrong | `admin_reports/payment_summary_modewise` | Mode-wise summary widget broken | Open: `/admin/admin_reports/payment_summary_modewise` |

**Timeout handling**: Most JS AJAX calls in dashboard.js do NOT have explicit timeout or error handlers — failure manifests as blank widget with no user feedback. Check browser console for failed XHR.
