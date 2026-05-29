# FORENSIC TEMPLATE — chit_reports
> Per-module investigation cheat sheet. Round R6-Upgrade — 2026-03-25

---

## Layer 1 — Symptom Collection

When a bug is reported for chit_reports, capture:

- [ ] Which report page? (payment_daterange / scheme_payment / closed_account / KYC / etc.)
- [ ] What filter was applied? (dates, branch, scheme, mode)
- [ ] What's wrong? Amount incorrect / missing data / blank page / error / wrong status
- [ ] Is it reproducible with a specific `id_payment` or `id_scheme_account`?
- [ ] Does it affect all branches or one specific branch?
- [ ] Does it affect amount schemes, weight schemes, or both?
- [ ] Is GST setting enabled for this client? (`chit_settings.gst_setting`)
- [ ] Is branch-wise login active? (`branchWiseLogin` session)
- [ ] Does the issue occur in the JS table display or in the API response?

---

## Layer 2 — Reproduce & Isolate

**Step-by-step reproduction**:
1. Log into admin panel
2. Navigate to the specific report page
3. Apply the same filters as reported
4. Open DevTools → Network tab
5. Trigger the AJAX call (click Search / submit)
6. Inspect the response JSON directly

**Isolation questions**:
- Is the JSON response from controller correct but JS renders wrong? → JS bug
- Is the JSON response wrong? → PHP/model bug
- Does the raw SQL return the correct data? → query bug
- Is the issue date-dependent? → timezone or date format mismatch

---

## Layer 3 — Client-Side Trace

**Console log points** (add temporarily to `reports.js`):
```javascript
// Before AJAX call - log the POST data being sent
console.log('[DEBUG] Sending to:', endpoint, 'Data:', formData);

// In success handler - log raw response
console.log('[DEBUG] Raw response:', data);
console.log('[DEBUG] Account count:', data.account ? data.account.length : 0);

// For GST amounts
console.log('[DEBUG] GST type:', row.gst_type, 'setting:', row.gst_setting, 'pay:', row.payment_amount);
```

**Key variables to inspect**:
- `data.account[]` — the rows rendered in DataTable
- `data.gst_number` — company GST for header display
- `data.schemes[]` / `data.mode_wise` — source-wise report data
- `ctrl_page[1]` — ensures correct JS block is running

**Network tab checks**:
- Request payload: Verify dates sent in correct format (`YYYY-MM-DD` vs `DD-MM-YYYY`)
- Response status: 200 vs 500
- Response body: valid JSON vs PHP error output mixed in

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Lines | What to Check |
|---|---|---|---|---|
| Wrong amount displayed | `admin_reports.php` | `payment_list_daterange` | L264-271 | Is `$pay` assigned? Check gst_type/gst_setting |
| Missing branch filter data | `payment_model.php` | `payment_list_daterange` | SQL WHERE clause | Check `id_branch` param, `show_to_all` flag |
| `$today` used before assignment | `admin_reports.php` | `scheme_daily_collection_details` | L1163 | BUG: `$today` not yet assigned at L1163 |
| Blank closed account report | `admin_reports.php` | `closedaccount_list` | L1219-1220 | `$model` overwritten: ACC_MODEL → PAY_MODEL immediately |
| Duplicate KYC function | `admin_reports.php` | `kycapproval_data` | L906 vs L911 | PHP uses second definition; first (L906) is dead code |
| GST number missing | `admin_reports.php` | Any list method | `$payment_list[0]['gst_number']` | Only set if `count($data) > 0`; check array not empty |
| Cancel payment not logging | `admin_reports.php` | `cancel_payment` | L677 | No transaction — check if `payment_statusDB` correctly inserted |
| Member report empty | `admin_reports.php` | `getMemberReport` | L1895 | Calls `payment_model::getMemberReport()` — check filter params |
| Branch filter not working | `account_model.php` | `getAmountSchemeAccounts` | L220-237 | Check `branchWiseLogin`, `id_branch`, `show_to_all` in SQL |
| Source-wise totals wrong | `admin_reports.php` | `scheme_payment_list_daterange` | L1331-1342 | `$admin_app[]` etc uninitialized if no records |

**Add to controller temporarily**:
```php
// Top of problematic method, before model calls
error_log("[DEBUG chit_reports] POST: " . json_encode($_POST));
// After model call
error_log("[DEBUG chit_reports] Result count: " . count($data));
// After DB call in model
error_log("[DEBUG payment_model] Last SQL: " . $this->db->last_query());
```

---

## Layer 5 — Database Verification

```sql
-- Verify payment record is what the report shows
SELECT p.id_payment, p.payment_amount, p.payment_status, p.payment_mode,
       p.date_payment, p.branch, p.sgst, p.cgst, p.gst_type,
       sa.scheme_acc_number, c.firstname, c.mobile, s.scheme_name,
       b.name as branch_name
FROM payment p
LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
LEFT JOIN customer c ON c.id_customer = sa.id_customer
LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
LEFT JOIN branch b ON b.id_branch = p.id_branch
WHERE p.id_payment = :id_payment;

-- Check collection total for reported date
SELECT DATE(date_payment) as pay_date,
       COUNT(*) as count, SUM(payment_amount) as total,
       payment_status, payment_mode
FROM payment
WHERE DATE(date_payment) BETWEEN ':from' AND ':to'
GROUP BY DATE(date_payment), payment_status;

-- Check GST settings for this client
SELECT gst_setting, currency_symbol, has_lucky_draw, 
       scheme_wise_acc_no, branchWiseLogin, edit_custom_entry_date
FROM chit_settings LIMIT 1;

-- Check if branch filter is correctly applied
SELECT p.id_payment, p.branch, b.name, b.show_to_all
FROM payment p
LEFT JOIN branch b ON b.id_branch = p.branch
WHERE DATE(p.date_payment) = ':date'
AND b.id_branch = :id_branch OR b.show_to_all = 1;

-- Check cancelled payments
SELECT p.id_payment, p.payment_status, psl.id_status_msg, psl.date_upd
FROM payment p
LEFT JOIN payment_status_log psl ON psl.id_payment = p.id_payment
WHERE p.payment_status = 4
ORDER BY p.id_payment DESC LIMIT 20;

-- Verify KYC status
SELECT c.id_customer, c.firstname, c.kyc_status,
       ck.id_kyc, ck.doc_type, ck.status as kyc_doc_status,
       ck.emp_verified_by, ck.last_update
FROM customer c
LEFT JOIN customer_kyc ck ON ck.id_customer = c.id_customer
WHERE c.id_customer = :id_customer;
```

---

## Layer 6 — Root Cause Classification

| Category | Risk | Typical Symptom |
|---|---|---|
| GST calculation error | HIGH | Wrong amount displayed (extra or less GST deducted) |
| Branch filter bypass | HIGH | Records from wrong branch shown |
| Uninitialized variable | MEDIUM | PHP notice in error log; wrong data when condition not met |
| Dead code / duplicate function | MEDIUM | KYC update silently using wrong function |
| No DB transaction | MEDIUM | Partial cancel — payment status updated but no status log |
| External API failure | MEDIUM | Trans unique ID generation fails; no fallback |
| Date format mismatch | MEDIUM | Empty report despite having data (¿DD-MM-YYYY vs YYYY-MM-DD?) |
| `$today` not initialized | HIGH | Collection report crashes on server |
| Raw `$_POST` injection | HIGH (latent) | Any field with user-controlled input |

---

## Layer 7 — Financial Integrity Checks (Specialized)

Since this module **displays** financial collection data (but also **cancels** and **edits** payments):

```sql
-- Verify payment_amount matches what was actually recorded
SELECT p.id_payment,
       p.payment_amount as stored_amount,
       (p.sgst + p.cgst) as total_gst,
       (p.payment_amount - (p.sgst + p.cgst)) as net_amount_if_gst
FROM payment p
WHERE p.id_payment IN (:list_of_ids_from_report)
ORDER BY p.id_payment;

-- Check for orphan payment_status_log entries
SELECT psl.id_payment FROM payment_status_log psl
LEFT JOIN payment p ON p.id_payment = psl.id_payment
WHERE p.id_payment IS NULL;

-- Check for cancelled payments without status log
SELECT p.id_payment FROM payment p
WHERE p.payment_status = 4
AND p.id_payment NOT IN (
    SELECT id_payment FROM payment_status_log WHERE id_status_msg = 4
);

-- Verify total collection matches sum of individual payments
SELECT 
    SUM(p.payment_amount) as payment_table_total,
    cs.some_running_total  -- if system maintains a running total
FROM payment p, chit_settings cs
WHERE DATE(p.date_payment) = ':date'
AND p.payment_status = 1;
```

---

## Layer 8 — Log File / PII Exposure Diagnosis (Round 4 Addition)

When investigating suspicious access to customer data or log files:

**Verify log directory access**:
```powershell
# Check if .htaccess exists at any level
Test-Path "c:\xampp-7.1\htdocs\etail_development_src\admin\log\.htaccess"
Test-Path "c:\xampp-7.1\htdocs\etail_development_src\admin\.htaccess"
Test-Path "c:\xampp-7.1\htdocs\etail_development_src\.htaccess"
# Expected: ALL should return True for security. Currently ALL return False.
```

**Check what log files exist**:
```powershell
Get-ChildItem "c:\xampp-7.1\htdocs\etail_development_src\admin\log\" -Recurse -Filter "*.txt" |
  Select-Object FullName, @{N='KB';E={[math]::Round($_.Length/1KB,1)}} |
  Sort-Object FullName
```

**Verify log content for PII**:
```powershell
# Read first 10 lines of latest log
(Get-Content "c:\xampp-7.1\htdocs\etail_development_src\admin\log\*.txt" | Select -First 10)
```

**Immediate fix**:
```apache
# Create: admin/log/.htaccess
Deny from all
```

**Web access test URL pattern**:
```
http://{host}/admin/log/{YYYY-MM-DD}/manual/create_payment_{YYYY-MM-DD}.txt
http://{host}/admin/log/payment{YYYY-MM-DD}.txt
```

**PII fields in these logs**: customer mobile, firstname, lastname, payment_amount, payment_mode, scheme_acc_number, nominee_name, form_secret token, branch.

---

## Layer 9 — Cross-Module Conflict Debugging

When a bug in `chit_reports` seems caused by another module:

| Symptom | Likely Owner | How to Verify |
|---|---|---|
| Report shows `branch=null` for some payments | Payment module | Check `admin_payment::SaveAll()` — was `branch` field compulsory? See fix RPT-BRANCH-01 (2026-03-12) |
| Cancel payment doesn't appear in `payment_status_log` | chit_reports cancel_payment | Add `trans_start` / `trans_complete` around L665-695; check if `payment_statusDB` insert fails |
| Scheme account edit bypasses business rules | Account module isolation | `updateAccountDetails` in reports direct-writes via `payment_model::updateDatacus` — no Account module validation called |
| SMS not sent after purchase OTP delivery | SMS model | `purch_delivered` calls `sms_model::sendSMS_*` — check which gateway is active in `chit_settings.sms_gateway` |
| Khimji trans ID generation fails silently | integration_model | Add error_log around `integration_model::khimji_curl` call in `generateTranUniqueIdManually` |
| Online gift summary wrong | account module | `get_online_gift_report` calls `payment_model` directly — verify `gift_issued` table is populated by Account module |

---

## Quick-Lookup Bug Table (All Known Bugs as of Round 5)

| ID | File | Line | Bug | Severity |
|---|---|---|---|---|
| BUG-001 | `admin_reports.php` | L265-271 | `$pay` uninitialized when `gst_type=1` | 🔴 HIGH |
| BUG-002 | `admin_reports.php` | L1163 | `$today` used before assignment | 🔴 HIGH |
| BUG-003 | `admin_reports.php` | L1219-1220 | `$model=ACC_MODEL` immediately overwritten — dead line | 🟡 MED |
| BUG-004 | `admin_reports.php` | L906+L911 | `kycapproval_data` defined twice — L906 is dead | 🟡 MED |
| BUG-005 | `admin_report_model.php` | L26-30 | SQL injection: `$status`/`$type` raw concat | 🔴 CRITICAL |
| BUG-006 | `admin_report_model.php` | L576-577 | Celeb date cross-year boundary BETWEEN fails | 🟡 MED |
| BUG-007 | `account_model.php` | L2974, 2989, 2994 | Raw `$id` concat in 3 SQL methods | 🔴 HIGH |
| BUG-008 | `admin_reports.php` | L665-695 | cancel_payment no transaction wrap | 🟡 MED |
| BUG-009 | `admin_reports.php` | L830-831 | SSL verify disabled in Msg91 curl | 🟡 LOW |
| BUG-010 | `admin_log/` | ALL | Log directory web-exposed — 31 PII files | 🔴 CRITICAL P0 |
