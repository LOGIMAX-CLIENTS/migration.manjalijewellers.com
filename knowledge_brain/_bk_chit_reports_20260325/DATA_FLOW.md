# DATA FLOW — chit_reports
> Rounds 1+3 consolidated — 2026-03-16 | 9 flows total (Round 1: Flows 1-6, Round 3: Flows 7-9)
> See DATA_FLOW_R3.md for original Round 3 source (superseded by this file).

---

## Flow 1: Payment Date-Range Collection Report (Primary Flow)

**User Action**: Admin selects date range + filters → clicks "Search"

**JS Side**:
```
reports.js → daterangepicker callback
  → get_payment_list(from_date, to_date, id_branch, type, id_employee, ...)
  → $.ajax POST → admin_reports/payment_list_daterange
```

**Controller** (`payment_list_daterange`, L245-320):
1. Reads POST: `from_date`, `to_date`, `type`, `limit`, `id`, `id_employee`, `acc`
2. Calls `payment_model::payment_list_daterange($from, $to, $type, $limit, $id, $id_employee, $acc)`
3. Loops result set — for each payment:
   - Calculates `$sgst = sprintf("%.3f", $payment['sgst'])`
   - Calculates `$cgst = sprintf("%.3f", $payment['cgst'])`
   - Calculates `$total_gst = $sgst + $cgst`
   - **Bug**: `$pay` only defined if `gst_type=0 AND gst_setting=1` — used unconditionally in ternary
   - Builds `$data['account'][]` array with 30+ fields
4. If `count($data) > 0`: returns `gst_number` from first record
5. `echo json_encode($data)`

**DB Tables Touched**:
- READ: `payment`, `scheme_account`, `customer`, `scheme`, `branch`, `chit_settings`

**Response Structure**:
```json
{
  "account": [...payment rows...],
  "gst_number": "GST123"
}
```

**JS Rendering**: DataTable populated from `data.account[]`

---

## Flow 2: Source-Wise Report (scheme_payment_list_daterange)

**User Action**: Admin selects scheme, mode, branch, date range → "Search"

**JS Side**:
```
reports.js → getSchemeDateRangeList()
  → $.ajax POST → admin_reports/scheme_payment_list_daterange
```

**Controller** (`scheme_payment_list_daterange`, L1313-1346):
1. Reads 8 POST params: `from_date`, `to_date`, `id_classfication`, `id_scheme`, `pay_mode`, `id_branch`, `mode`, `metal_date_type`
2. Three parallel model calls:
   - `payment_model::sheme_payment_list_daterange(...)` → `$data['schemes']`
   - `payment_model::get_Scheme_Payment_ModeWiseummaryDetails(...)` → `$data['mode_wise']`
   - `payment_model::payment_summary_modewise_data(...)` → `$data['mode_wise_sum']`
3. Calculates totals by iterating admin_app, offline, online arrays
4. Risks: `$admin_app[]`, `$offline[]`, `$online[]` not initialized before foreach — if empty, `array_sum` called on undefined

**DB Tables**: `payment`, `scheme`, `branch`, `payment_mode`, `chit_settings`

---

## Flow 3: KYC Approval (Only WRITE flow)

**User Action**: Admin views pending KYC → clicks Approve/Reject

**JS Side**:
```
reports.js → #update_kyc_btn click
  → collect kycdata array from table checkboxes
  → $.ajax POST → admin_reports/update_kyc
```

**Controller** (`update_kyc`, L930-971):
1. Reads `kyc_data[]` array and `kyc_type` from POST
2. For each KYC record:
   - Builds `$updatedata = ['status', 'emp_verified_by', 'last_update']`
   - If `kyc_type == 1`: calls `payment_model::updatekyc($updatedata, $id_kyc, $cus)` → customer KYC
     - If all KYCs verified: calls `payment_model::updatekyccus(['kyc_status'=>1], $cus)` → customer table
   - If `kyc_type == 2`: `payment_model::updateAgentkyc(...)` → agent KYC
3. Sets flash data for success/rejection message
4. Returns count of updated records

**DB Tables Written**: `customer_kyc`, `agent_kyc`, `customer`, `agent`

> ⚠️ No transaction wrapping — partial updates possible if loop fails mid-way

---

## Flow 4: Closed Account Report

**JS**: `closedaccount_list` POST → `admin_reports/closedaccount_list`

**Controller** (`closedaccount_list`, L1217-1230):
1. **Bug**: `$model` = ACC_MODEL then immediately overwritten to PAY_MODEL (L1219-1220)
2. Reads `from_date`, `to_date`, `id_employee`, `close_id_branch`
3. Two parallel model calls:
   - `payment_model::get_closed_summary_by_date(...)` → `$data['closed_summary']`
   - `payment_model::get_all_closed_account_by_date(...)` → `$data['accounts']`

**DB**: `scheme_account` (is_closed=1, active=0), `payment`, `customer`, `scheme`, `branch`

---

## Flow 5: Cancel Payment

**JS**: `cancel_payment` form submit → `admin_reports/cancel_payment`

**Controller** (`cancel_payment`, L665-695):
1. Reads `$txns = $_POST` (raw)
2. Loops `$txns['id_payment'][]`:
   - `payment_model::payment_cancel('update', $id_payment, ['payment_status'=>4])`
   - `payment_model::paymentDB('get', $id_payment)`
   - If `integrationType==2`: loads `syncapi_model` → `updPayStatusInTrans`
   - `payment_model::payment_statusDB('insert', $id_payment, [...])`
3. Echoes `TRUE`

> ⚠️ No transaction — if `payment_statusDB` insert fails after `payment_cancel` update, payment is stuck in cancelled state without log entry

---

## Flow 6: Edit Payment/Account (Admin Override)

**JS**:
```
edit_acc_pay page loads
→ get_acc_by_id / get_pay_by_id (fetch current data)
→ user edits fields
→ updatePaymentDetails / updateAccountDetails
```

**`updatePaymentDetails`** (L1713-1725):
1. Logs raw `$_POST` to `log/payment{date}.txt`
2. Calls `payment_model::updatePaymentdata($_POST)`
3. Writes to `payment` table

**`updateAccountDetails`** (L1726-1755):
1. Logs to `log/account{date}.txt`
2. Validates customer mobile exists via `payment_model::cusexist`
3. Calls `checkCommonSettings($_POST)` — branch validation
4. If OK: `payment_model::updateDatacus(['id_customer'=>...], 'id_scheme_account', ...)` → `scheme_account` table

---

## Flow 7: Scheme Summary (Outstanding Balance Report)

**User Action**: Admin opens scheme summary page — no date = today's snapshot; with date filter = historical snapshot.

**JS**: `getSchemeSummary()` or `get_outstanding_summary()` → `$.ajax POST → admin_reports/scheme_summary`

**Controller** (`scheme_summary`, L1557-1578):
```php
$model = self::ACC_MODEL; // account_model
$details = $this->$model->scheme_summary_data();
foreach ($details as $r) {
    if ($r['is_lucky_draw']) {          // ⚠️ BOTH branches do the same thing:
        $r['group_scheme'] = ...;       // lucky draw schemes gets extra key
        $return_data[$r['code']][] = $r;
    } else {
        $return_data[$r['code']][] = $r; // identical assignment — redundant if/else
    }
}
$data['scheme_summary'] = $return_data;
echo json_encode($data);
```

**`scheme_summary_data()` in account_model** (L2711-2873): Runs **4 sub-queries** + PHP-side merge:
- `get_oldcollection_amt()` → payments before date range
- `get_oldclosed_amt()` → closures before date range
- `get_newcollection_amt()` → payments in date range
- `get_newclosed_amt()` → closures in date range

**Balance formula**: `balance = (oldcollection - oldclosed) + newcollection - newclosed`

**Performance risk**: 4 SQL queries × n schemes in PHP loop = O(n×4m). Large clients will time out.

**DB Tables**: `payment`, `scheme_account`, `scheme`, `sch_classify`

---

## Flow 8: Customer Celebration Dates (Birthday / Anniversary)

**User Action**: Admin enters date range → "Search" → sees upcoming birthdays/anniversaries

**JS**: `get_celeb_dates(from, to)` → `$.ajax POST → admin_reports/cus_celeb_dates`

**Controller** (`cus_celeb_dates`, L2072-2099):
```php
$postData = $_POST;   // raw $_POST — no CI input class
$details = $this->admin_report_model->get_all_cus_celeb_dates($postData);
if (!empty($details)) {
    echo json_encode(['success'=>true, 'data'=>$details]);
} else {
    throw new Exception("No details found...");   // only method using try/catch
}
```

**`get_all_cus_celeb_dates()` SQL key filter** (L576-577):
```sql
WHERE DATE_FORMAT(c.date_of_birth,'%m%d') BETWEEN DATE_FORMAT(:from,'%m%d') AND DATE_FORMAT(:to,'%m%d')
```

> ⚠️ **Cross-year bug**: `%m%d` BETWEEN fails when `from_month > to_month` (Dec→Jan). Returns zero results. See RULE-RPT-009 and INVARIANT_MATRIX EC-01.

**DB Tables**: `customer`, `address`, `city`, `scheme_account` (subqueries for active/closed count)

---

## Flow 9: Admin Edit Audit Log Write

**Context**: When admins edit payment or account records via `editable_settings/acc_pay_form.php`:

**`updatePaymentDetails`** (L1713-1725):
```php
$log_path = 'log/payment' . date("Y-m-d") . '.txt'; // relative → admin/log/
$ldata = "\n" . date('d-m-Y H:i:s') . " \nEdit Payment: " . json_encode($_POST, true);
file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);  // correct locking ✅
// THEN calls payment_model::updatePaymentdata($_POST)
```

**`updateAccountDetails`** (L1726-1755): Same pattern for `log/account{date}.txt`

**Path resolution**: `'log/'` is relative to FCPATH (`admin/`) → actual path = `admin/log/payment2026-03-16.txt`

**Security finding** (confirmed Round 4): `admin/log/` has NO `.htaccess` at any level. 31 PII-containing files directly web-accessible. See MODULE_BRAIN.md Risk #14 and COVERAGE_TRACKER Round 4.

---



| JS Function | Trigger | Endpoint | Method |
|---|---|---|---|
| `get_payment_list()` | Daterange picker | `payment_list_daterange` | POST |
| `getSchemeDateRangeList()` | Daterange/filter | `scheme_customer_list_daterange` | POST |
| `closedaccount_list()` | Filter submit | `closedaccount_list` | POST |
| `get_cancel_pay_list()` | Page load / daterange | `paymentcancel_list` | POST |
| `get_kyc_list()` | Page load / filter | `kycapproval_data` | POST |
| `update_kyc_status()` | Approve/Reject button | `update_kyc` | POST |
| `getSchemeDateRangeList()` | Source-wise filter | `scheme_payment_list_daterange` | POST |
| `scheme_daily_collection_details()` | Date filter | `scheme_daily_collection_details` | POST |
| `getMemberReport()` | Filter submit | `getMemberReport` | POST |
| `get_maturity_data()` | Page load | `maturity_report_data` | POST |
| `get_online_payment_report()` | Daterange | `get_online_payment_report` | POST |
| `get_gen_adv_byid()` | Page load | `general_advance_list_byid` | POST |
| `get_member_rep()` | Filter submit | `getMemberReport` | POST |
| `get_acc_by_id()` | Edit button | `editAccOrPayments/get_acc_byId` | POST |
| `update_payment()` | Save button | `updatePaymentDetails` | POST |
| `generateOTP()` | OTP button | `generateotp` | POST |
