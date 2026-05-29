# DATA FLOW (Round 3 original) — chit_reports
> R6-Upgrade stamp — 2026-03-25 | Superseded by DATA_FLOW.md (consolidated)

---

## Flow 7: Scheme Summary (Outstanding Balance Report)

**User Action**: Admin opens scheme summary page → no date = today's data; with date filter = historical snapshot

**JS**: `get_outstanding_summary()` or `getSchemeSummary()` → `$.ajax POST → admin_reports/scheme_summary`

**Controller** (`scheme_summary`, L1557-1578):
```php
$model = self::ACC_MODEL; // account_model
$details = $this->$model->scheme_summary_data();
foreach ($details as $r) {
    if ($r['is_lucky_draw']) {
        $r['group_scheme'] = $this->$model->scheme_group_summary_data($r['id_scheme']);
        $return_data[$r['code']][] = $r;     // keyed by scheme code
    } else {
        $return_data[$r['code']][] = $r;     // same path — the if/else is redundant!
    }
}
$data['scheme_summary'] = $return_data;
echo json_encode($data);
```

> ⚠️ **Bug**: Both `if` and `else` branches of `is_lucky_draw` do identical assignment (`$return_data[$r['code']][] = $r`). Lucky draw schemes DO get `group_scheme` populated, but the key grouping is the same. The `if/else` is misleading/redundant — old commented code shows it used to key by scheme_name vs code.

**`scheme_summary_data()` in account_model** (L2711-2873):
1. Reads `from_date`, `to_date`, `id_scheme`, `id_branch` from POST
2. Runs **4 parallel sub-queries** (via 4 helper methods):
   - `get_oldcollection_amt()` → payments before date range
   - `get_oldclosed_amt()` → closures before date range  
   - `get_newcollection_amt()` → payments in date range
   - `get_newclosed_amt()` → closures in date range
3. **PHP-side merge**: Results from 4 queries merged in 5 nested foreach loops per account
4. Calculates:
   - `opening_amount = oldcollection - oldclosed`
   - `current_collection_amt = newcollection`
   - `balance_amount = opening + newcollection - newclosed`

**DB Tables**: `payment`, `scheme_account`, `scheme`, `sch_classify`

**Performance Risk**: 4 separate queries + PHP-side merge loop runs O(n × 4m) for n accounts. On large datasets this is slow. Each sub-query has no index on `$branchfilter`/`$schemefilter` variable interpolation.

---

## Flow 8: Customer Celebration Dates (Birthday/Wedding)

**User Action**: Admin enters date range → "Search" → shows upcoming birthdays/anniversaries

**JS**: `get_celeb_dates(from, to)` → `$.ajax POST → admin_reports/cus_celeb_dates`

**Controller** (`cus_celeb_dates`, L2072-2099):
```php
$postData = $_POST;  // raw $_POST used directly
$details = $this->admin_report_model->get_all_cus_celeb_dates($postData);
if (!empty($details)) {
    $data = $details;
    $success = true;
} else {
    throw new Exception("No details found for the given date range.");
}
// Returns: { success, errCode, message, data }
```

**`get_all_cus_celeb_dates()` in admin_report_model** (L556-588):
```sql
SELECT c.id_customer, c.firstname, c.mobile,
       ct.name as city_name,
       date_format(c.date_of_birth,'%d-%m-%Y') as birthday,
       date_format(c.date_of_wed,'%d-%m-%Y') as wedday,
       (subquery: active_acc count),
       (subquery: closed_acc count)
FROM customer c
LEFT JOIN address ad ON...
LEFT JOIN city ct ON...
WHERE c.date_of_birth IS NOT NULL OR c.date_of_wed IS NOT NULL
AND date_format(birthday,'%m%d') BETWEEN date_format(from,'%m%d') AND date_format(to,'%m%d')
OR same for wedding
AND c.id_company = :id_company (if company_settings=1)
```

> ⚠️ **Key Behavioral Rule**: Date comparison uses `%m%d` format — strips year so it finds recurring annual occurrences (e.g., all customers born in March). Cross-year ranges (Dec 25 → Jan 10) do NOT work correctly with this approach — the BETWEEN comparison fails when `from_month > to_month`.

**DB Tables**: `customer`, `address`, `city`, `scheme_account` (subquery)

**Response Structure**:
```json
{
  "success": true,
  "errCode": 0,
  "message": "",
  "data": [{ "id_customer", "firstname", "mobile", "city_name", "birthday", "wedday", "active_acc", "closed_acc" }]
}
```

> ℹ️ This is the only controller method using `try/catch` exception handling — all others use if/else or silent failure.

---

## Flow 9: Log File Write (updatePaymentDetails / updateAccountDetails)

**Context**: When admins edit payment or account records via the `editable_settings/acc_pay_form.php` view:

```
edit_acc_pay page → JS get_acc_byId / get_pay_byId
→ admin edits fields
→ updatePaymentDetails / updateAccountDetails
```

**File Write** (`updatePaymentDetails`, L1717-1720):
```php
$log_path = 'log/payment' . date("Y-m-d") . '.txt';
$ldata = "\n" . date('d-m-Y H:i:s') . " \n Edit Payment : " . json_encode($_POST, true);
file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
```

**Path Resolution**: `'log/payment...'` is a **relative path from FCPATH** (CodeIgniter's front controller PHP file directory = `admin/`). So the actual path is:
- `c:\xampp-7.1\htdocs\etail_development_src\admin\log\payment2026-03-14.txt`

**Confirmed**: `admin/log/` directory exists with date-subdirectory structure from previous log file discovery. ✅

**Security Assessment**:
- `date("Y-m-d")` is system-generated — cannot be injected ✅
- `json_encode($_POST, true)` logs raw POST — includes potentially sensitive data (mobile, payment amounts) → log files must be excluded from web access ⚠️
- No log rotation implemented — files grow unbounded for active edit sessions
- `FILE_APPEND | LOCK_EX` → correct locking 

> ⚠️ **Risk**: If `admin/log/` directory is web-accessible (no `.htaccess` deny), anyone knowing the filename `admin/log/payment2026-03-14.txt` can read raw edit audit data including customer mobiles and payment amounts.
