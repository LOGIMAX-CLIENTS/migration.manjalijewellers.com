# `chit_services` — Bug Register

> **Brain Built:** 2026-03-17 | **Round 2 Complete:** 2026-03-18 | **Total Bugs:** 27

---

## Quick-Fix Reference

| Priority | Count | Notes |
|---|---|---|
| P0 Catastrophic | 5 | print_r;exit halts, undefined var crashes, broken function |
| P1 Critical | 7 | Raw input in DB, uninitialized vars, SMS double-fire |
| P2 Medium | 9 | SSL, N+1, mkdir, SQLi in model, division by zero |
| P3 Low | 6 | Duplicate constants, dead code, missing JS error handlers |

---

## P0 — Catastrophic

### SVC-BUG-001 | P0 | `dayDurationSchemeService()` — Active `print_r();exit;` Halts Production Cron

**File:** Controller L7056, L7066, L7085, L7106, L7112, L7125, L7132, L7161, L7188, L7191, L7204, L7209, L7230, L7234  
**When:** Cron job runs `dayDurationSchemeService()` — any qualifying payment triggers halt

Most blocks are wrapped in commented `/* if($pay['id_payment'] == XXXXX) { */` guards but several have misplaced or missing `*/`:

```php
// L7056 - ACTIVE:
print_r($this->db->last_query());exit;

// L7066 - ACTIVE:
print_r($paid_dueData);exit;

// L7085 - ACTIVE:
print_r($this->db->last_query());exit;
```

**Impact:** The scheduled cron job that classifies due types (ND/AD/PD) for ALL scheme accounts will halt mid-loop when it reaches the first payment where the guard condition triggers. All subsequent scheme accounts are left unprocessed. Customers' due classifications are incorrect — they cannot pay their dues correctly.

**Fix:** Remove all `print_r();exit;` blocks entirely. Use PHP error_log() for debugging.

---

### SVC-BUG-002 | P0 | `add_daily_collection()` — `echo print_r()` Dumps Sensitive Data to Output

**File:** Controller L1663  
```php
echo print_r($instoday);  // ← ACTIVE, not commented
```

`$instoday` contains branch-wise financial collection data (amounts, weights). Called nightly by cron — dumps raw PHP array to stdout. In cron context this goes to cron log files; if called via HTTP by accident it leaks financial summary to any requester.

**Fix:** Remove the line. The data is already being inserted two lines later.

---

## P1 — Critical

### SVC-BUG-003 | P1 | `ajax_interWallet_trans()` — Raw `$_GET` into DB Query

**File:** Controller L1686, L1688
```php
$id_branch = $_GET['id_branch'];   // flows into query
$date      = $_GET['date'];         // flows into query
```
No CI input filtering. `$id_branch` likely cast by model but `$date` used in SQL WHERE. SQL injection on the date field.  
**Fix:** `$this->input->get('id_branch')` + `(int)` cast; `$this->input->get('date')` + validate as date string.

---

### SVC-BUG-004 | P1 | `loadTempToMainView()` / `ajaxtempTotest()` — Raw `$_POST` into DB Query

**File:** Controller L1716–1748
```php
$id_branch  = $_POST['id_branch'];
$entry_date = $_POST['entry_date'];
$till_update = $_POST['till_updated'];
```
Repeated in both `ajaxtempTotest()` (L1713) and `ajaxtempToMain()` (L1739).  
**Fix:** Use `$this->input->post()` for all three fields.

---

### SVC-BUG-005 | P1 | `update_client()` / `syncInterData()` — Raw `$_POST/$_GET` in Sync Methods

**File:** Controller L3019, L3023
```php
$branch_id  = isset($_POST['sync_branch_id']) ? $_POST['sync_branch_id'] : NULL;
$trans_date = isset($_GET['sync_trans_date'])  ? $_GET['sync_trans_date'] : date('Y-m-d');
```
These flow into `syncInterData()` which passes them through to queries.  
**Fix:** Use CI input helper.

---

### SVC-BUG-006 | P1 | `send_notification()` — `$alertcount` Used Before Initialization

**File:** Controller L438, L450
```php
// No $alertcount = 0; declaration before the loop
if ($status) {
    $alertcount = $alertcount + 1;  // ← E_NOTICE: undefined variable
}
$msg = $alertcount . ' Customer(s) Due ALert Notification sent successfully.';
```
PHP emits `E_NOTICE: Undefined variable: alertcount` and the count in the success message is always wrong (PHP coerces undefined to 0 but only after the arithmetic).

Same bug exists in `send_smsdue()` at L697, L742.  
**Fix:** Add `$alertcount = 0;` before the foreach loop in both methods.

---

### SVC-BUG-007 | P1 | `syncExistingCusData()` — Raw `$id_branch` in SQL

**File:** Controller L2296
```php
$sql = $this->db->query("select * from customer_reg where is_transferred='N' and id_branch=" . $id_branch);
```
`$id_branch` comes from the caller (which gets it from raw POST). SQL injection.  
**Fix:** `(int)$id_branch` cast before query, or use CI query builder.

---

## P2 — Medium

### SVC-BUG-008 | P2 | 18 curl Calls with `CURLOPT_SSL_VERIFYPEER = FALSE`

**File:** Controller lines 826, 1557, 2130, 2526, 2671, and 13 more  
All OneSignal, payment gateway, and sync curl calls disable SSL certificate verification. MITM attack can intercept push tokens and payment gateway API keys.  
**Fix:** Remove `curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE)` from all 18 calls. If needed for dev, gate on `ENVIRONMENT != 'production'`.

---

### SVC-BUG-009 | P2 | `dayDurationSchemeService()` — N+1 Query Per Payment

**File:** Controller L7054, L7060, L7082  
Three queries fired per payment record inside the main payment loop:
1. `get_due_date('current_range', ...)` — date range lookup
2. `SELECT COUNT due_type FROM payment WHERE ... GROUP BY due_type` — paid dues count
3. `get_due_date('allow_pay', ...)` — remaining dues

For 10,000 scheme accounts → 30,000+ queries per cron run.  
**Fix:** Batch the `due_date` lookups and the payment counts using JOINs before the loop.

---

### SVC-BUG-010 | P2 | `send_notification()` / `send_smsdue()` — `trans_status()` Without Full Transaction

**File:** Controller L446, L738  
```php
// After a loop of notifications:
if ($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();
} else {
    $this->db->trans_rollback();
}
```
`trans_begin()` is called at the top of `check_expiry()` (L108) — but NOT in `send_notification()` or `send_smsdue()`. The `trans_status()` check is meaningless — always returns TRUE.  
**Fix:** Either add `$this->db->trans_begin()` before the loop or remove the dead transaction check.

---

### SVC-BUG-011 | P2 | `add_daily_collection()` — `-2 days` vs `-1 days` Asymmetry

**File:** Controller L1572–1593  
```php
$previousDay = date('Y-m-d', strtotime("-2 days"));  // gets day before yesterday's closing
$today = $model->getTodaySummaryBranchWise(date('Y-m-d', strtotime("-1 days")), ...); // gets yesterday's data
```
The comment says "executed at 2AM of next day" — so `-1 days` for today's data is correct. But `$previousDay = -2 days` means the rolling balance is calculated from 2 days ago, not yesterday. This creates a gap in the daily collection rollup — yesterday's closing is never used as the starting balance.  
**Fix:** Verify intended behavior. If run at 2AM for the previous day, `$previousDay` should be `date('Y-m-d', strtotime("-2 days"))` and `$today` should use `strtotime("-1 days")`. The logic seems intentional but needs documentation.

---

### SVC-BUG-012 | P2 | `set_image()` / `send_queue_sms()` — `mkdir(0777)` World-Writable Dirs

**File:** Controller L149, L707, L723, L6344, L7699, L7708  
All log directories and image directories created with 0777.  
**Fix:** `mkdir($path, 0755, TRUE)` everywhere.

---

### SVC-BUG-013 | P2 | Duplicate Model Constant: Both `MODEL` and `SMS_MODEL` = `admin_usersms_model`

**File:** Controller L9, L37  
```php
const MODEL     = "admin_usersms_model";
const SMS_MODEL = "admin_usersms_model";  // exact duplicate
```
And similarly `EMAIL_MODEL` = `MAIL_MODEL` = `"email_model"`.  
Both models are loaded twice in the constructor (L59, L75).  
**Fix:** Remove `SMS_MODEL` and `MAIL_MODEL` constants; replace usages with `MODEL` and `EMAIL_MODEL`.

---

### SVC-BUG-014 | P2 | `test()` Method (L6684) — Public Test/Debug Endpoint in Production

**File:** Controller L6684–6833  
A 150-line public `test()` function at `/services/test` that executes refund and payment status queries. No auth gate. Anyone can call this endpoint.  
**Fix:** Remove or gate behind `ENVIRONMENT === 'development'` check.

---

## P3 — Low Priority

### SVC-BUG-015 | P3 | 35 Active `print_r()` Calls Without `exit` (Not Counted in P0)

**File:** Various lines (1294, 1698, 1796, 1802, 1818, 2012, 2138, 2176, 2179, 2182, 2287, 2377, 2391, 2409, 3004, 3362, 3380, 5657, 5698, 6122, 6181, 6571, 6616, 6706, 6765, 6830)  
These `print_r()` calls aren't fatal but they leak internal data structures (customer data, payment details, sync responses) to HTTP responses/cron logs.  
**Fix:** Replace all with `error_log()` or remove.

---

### SVC-BUG-016 | P3 | `send_mjdmarate_notification()` (L1273) — Hardcoded Client URL

**File:** Controller L1273  
This method contains a hardcoded endpoint URL to what appears to be a client/competitor system (mjdma). Dead code risk similar to MST-BUG-020.  
**Fix:** Remove or move to config.

---

### SVC-BUG-017 | P3 | Commented-Out `trans_begin()` in `oldverify_cashfreepayment()` (L3591)

**File:** Controller L3591  
```php
//$this->db->trans_begin();   ← commented out
```
The payment verification process has no transaction wrapping — if `insert_common_data()` succeeds but a later step fails, data is partially committed.  
**Fix:** Uncomment or properly add `trans_begin()`, `trans_commit()`, `trans_rollback()`.

---

### SVC-BUG-018 | P3 | `passwdord_hash()` (L6872) — Typo in Method Name

**File:** Controller L6872  
`function passwdord_hash()` — "passwdord" is a typo for "password". While functional, this is confusing and unprofessional.  
**Fix:** Rename to `password_hash_util()` (avoid conflict with PHP built-in `password_hash()`).

---

## Round 2 — New Bugs (2026-03-18)

---

## P0 — Catastrophic (Round 2)

### SVC-BUG-022 | P0 | `update_client()` L3106 — `$branch` Undefined in updateData() WHERE Clause

**File:** Controller L3106  
```php
$this->$api_model->updateData($inter_data, $branch, 'customer_reg');
                               // ↑ $branch is UNDEFINED — should be $branch_id
```
`$branch` is never declared in `update_client()`. Only `$branch_id` exists (from `$_POST['sync_branch_id']`). The WHERE clause for the sync transfer update is built with `$branch` = NULL, silently updating **all** customer_reg records regardless of branch. Mass data corruption risk.

**Fix:** Replace `$branch` with `$branch_id` on L3106.

---

### SVC-BUG-024 | P0 | `services_model::updMaturityDate()` L446 — `echo $maturity;exit;` Permanently Halts Function

**File:** services_model.php L446  
```php
$maturity = date('Y-m-d', strtotime("+"...$skipped_months..." months"...));
echo $maturity;exit;  // ← ACTIVE: function ALWAYS exits here
$updData = array("maturity_date" => $maturity, ...);  // NEVER REACHED
$this->db->update("scheme_account", $updData);         // NEVER EXECUTED
```
Maturity date recalculation is permanently disabled — the database update after the `echo;exit;` is dead code. Called by any cron job managing maturity dates — silently broken forever.

**Fix:** Remove `echo $maturity;exit;`. Let function proceed to the `$updData` update.

---

### SVC-BUG-025 | P0 | `services_model::updMaturityDate()` — `$record` and `$paidByMonth` Undefined Variables

**File:** services_model.php L424–L453  
`updMaturityDate()` references `$record->is_fixed_maturity`, `$record->id_scheme_account`, `$record->start_date`, `$record->total_installments` and `$paidByMonth` — none of which are passed as parameters or declared in the function scope. Every call would throw `E_ERROR: Trying to get property of non-object`.  
**Fix:** Add `function updMaturityDate($record, $paidByMonth = [])` parameters.

---

## P1 — Critical (Round 2)

### SVC-BUG-021 | P1 | `scheme_freepayment()` — SMS/Push Double-Fires for Every Customer

**File:** Controller L994–L1031  
```php
foreach ($freepaycust as $data) {
    foreach ($free_ins as $ins) {
        if (condition) {
            $status = paymentDB('insert', ...);
        }
    }
    if ($status) {  // ← OUTSIDE inner loop — evaluates LAST $status from any previous iteration
        sendSMSMail('3', ...);  // fires for ALL customers, not just those who got free instalment
```
The `if ($status)` SMS dispatch block is indented inside the outer `foreach ($freepaycust)` but OUTSIDE `foreach ($free_ins)`. It fires using whatever `$status` happened to be from the last execution, regardless of whether this customer needed a free instalment.

**Fix:** Move `if ($status) { ... }` inside `if ($data['paid_installments'] + 1 == $ins ...)` block, after the insert.

---

### SVC-BUG-023 | P1 | `update_client()` — Raw `$_GET['sync_trans_date']` in DB Queries

**File:** Controller L3023  
```php
$trans_date = (isset($_GET['sync_trans_date']) ? $_GET['sync_trans_date'] : date('Y-m-d'));
```
Flows into `getRegisteredAccTransactions()` and `getcustomerByStatus()` as date filter. No validation, no CI input helper. Malformed date or SQL injection payload passes directly to query.

**Fix:** `$this->input->get('sync_trans_date')` + validate with `DateTime::createFromFormat('Y-m-d', ...)` before use.

---

## P2 — Medium (Round 2)

### SVC-BUG-019 | P2 | `scheme_freepayment()` — `$receipt_no` Used Before Being Set

**File:** Controller L1108  
```php
if ($sch_data['receipt_no_set'] == 1) {
    $receipt_no = $this->generate_receipt_no();  // only set under condition
}
$insertData = array(
    ....
    "receipt_no" => $receipt_no,  // ← PHP Notice: undefined if receipt_no_set != 1
);
```
If `receipt_no_set != 1`, `$receipt_no` was never set. PHP emits `E_NOTICE: Undefined variable` and inserts NULL (or previous loop value in repeat runs) as receipt number.

**Fix:** Initialize `$receipt_no = NULL;` before the `if` block.

---

### SVC-BUG-020 | P2 | `getFreePayData()` — Division by Zero if Gold Rate is 0

**File:** Controller L1059  
```php
$converted_wgt = number_format((float)($sch_data['amount'] / $gold_rate), 3, '.', '');
```
If `getMetalRate()` returns 0 (metal rate not set, or DB empty), this is a division by zero. PHP emits `E_WARNING: Division by zero` and `$converted_wgt` = INF/NAN, which is inserted as the metal weight in the free payment record. Financial data corruption.

**Fix:** Guard: `if ($gold_rate > 0) { ... } else { $gold_rate = 1; /* log warning */ }`

---

### SVC-BUG-Model-01 | P2 | `services_model::daily_collection()` — `$date` and `$id_branch` Concatenated Directly in SQL

**File:** services_model.php L32, L38  
```php
$sql = "select ... where date='".$date."'";
// $date comes from controller calling add_daily_collection() — not sanitized
```
No parameterization. Controller-supplied date string injected directly.  
**Fix:** Use CI query builder: `$this->db->where('date', $date)` or `$this->db->query($sql, [$date])`.

---

## P3 — Low Priority (Round 2)

### SVC-BUG-026 | P3 | `sms.js::get_schemes()` — No Error Handler

**File:** `admin/assets/js/sms.js` L262–291  
The `$.ajax` call to `sms/get_schemes` populates the scheme selection dropdown. No `error:` handler defined. If the server returns a 500 or the call fails, the dropdown silently remains empty.

**Fix:** Add `error: function() { alert('Failed to load schemes. Please refresh.'); }` to AJAX options.

---

### SVC-BUG-027 | P3 | `notification.js` — ON/OFF Toggle AJAX Has No Error Handler

**File:** `admin/assets/js/notification.js` L31–54  
Notification ON/OFF toggle switch makes POST to `notification/on_off/{0|1}` but has no `error:` callback. If request fails, toggle visually flips but server state is unchanged — user sees wrong status without any feedback.

**Fix:** Add error handler that reverts the switch position on failure.
