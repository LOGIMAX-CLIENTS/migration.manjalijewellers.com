# `chit_services` — Round 2: Model + JS + Deferred Method Analysis

> **Round 2 Completed:** 2026-03-18 10:54 IST  
> **Coverage Before:** ~90% | **Coverage After:** ~100%  
> **New Bugs Found:** 9 additional (SVC-BUG-019 to SVC-BUG-027)

---

## A. `scheme_freepayment()` — Full Trace (L961–L1046)

**Purpose:** Auto-credit a free instalment payment for any qualifying scheme account.

**Trigger:** Cron job or direct URL call — no auth check.

### Flow

```
scheme_freepayment()
├─ $model = PAY_MODEL (payment_model)
├─ get_freepaycust() → all accounts with has_free_ins=1
├─ trans_begin()
├─ FOREACH customer:
│    FOREACH free instalment number split from free_payInstallments:
│        IF paid_installments+1 == $ins AND last_paid_month != current month:
│            getFreePayData($data) → build $insertData (receipts, rates, GST)
│            payment_model->paymentDB("insert", "", $insertData)
│            IF insert success:
│                payment_model->payment_statusDB("insert", ...) 
│                sendSMSMail('3', ...) — payment SMS to customer
│                sendSMSMail('7', ...) — payment push notification
├─ trans_status() → commit/rollback
└─ echo result count
```

### Bugs Found in `scheme_freepayment()`

| Bug | Line | Description |
|---|---|---|
| SVC-BUG-019 | L1108 | `$receipt_no` used in `$insertData` even if `$sch_data['receipt_no_set'] != 1` — variable undefined, PHP Notice |
| SVC-BUG-020 | L1059 | `$gold_rate` used as divisor — if `goldrate_22ct` is 0 or null, division by zero silently produces INF/NaN |
| SVC-BUG-021 | L994 | `if ($status)` check is OUTSIDE inner `foreach ($free_ins)` — always evaluates last status, meaning SMS/push fires for EVERY customer even when no free instalment was due |

### `getFreePayData()` (L1049–L1118)

Builds payment row for free credit. Key logic:
- GST type 0: deduction (inclusive). GST type 1: addition (exclusive).  
- `$receipt_no` only set if `receipt_no_set == 1`. Used unconditionally in array → **PHP Undefined variable Notice** (SVC-BUG-019).

---

## B. `syncInterData()` — Full Trace (L2685–L3007)

**Purpose:** Sync inter-branch or remote customer/payment records back to the main database.

### Flow

```
syncInterData()
├─ $api_model = "sktm_syncapi_model"  (hard-coded string, not via constant)
├─ $record_to = 2 (Online)
├─ getcustomerByStatus('N', null, 2) → accounts with is_modified=1, is_registered_online>=1
│    FOREACH:
│        trans_begin()
│        Build acc_data array
│        IF id_scheme_account != null → update_account(...)
│        ELSE → update_accountByClientId(...)
│        trans_status() → commit/rollback
│        Track acc_rec, rejected_acc_id
├─ getTransactionByStatus('N', null, 2) → pending transactions
│    FOREACH transaction:
│        IF payment_type == 1 (Online): updatePayment(...)
│        IF payment_type == 2 (Offline): insertPayment(...)
│        Track trans_rec, pay_id
├─ echo summary JSON
```

### Bugs Found in `syncInterData()`

| Bug | Line | Description |
|---|---|---|
| — | L2688 | `$api_model = "sktm_syncapi_model"` — hard-coded string, not via SYN_MODEL constant. Model name mismatch: `update_client()` at L3014 uses `self::SYN_MODEL` = `"syncapi_model"` (different). Two different sync model classes used inconsistently. |

> Non-blocking architectural issue. `syncInterData()` uses a different sync model class than `update_client()`. Risk: if only one is updated/maintained, sync breaks silently.

---

## C. `update_client()` — Full Trace (L3009–L3316)

**Purpose:** Bulk sync of customer account and payment data from mobile app / inter-branch tablet system to main server.

### Flow

```
update_client()
├─ $api_model = self::SYN_MODEL = "syncapi_model"
├─ $branch_id = $_POST['sync_branch_id'] ← RAW (SVC-BUG-005 confirmed)
├─ $trans_date = $_GET['sync_trans_date'] ← RAW (SVC-BUG-005 confirmed)
├─ getcustomerByStatus('N', '-1', 2, $trans_date)
│    FOREACH:
│        IF is_modified=1 AND is_registered_online>=1:
│            checkClientID("", $client['clientid']) → verify offline>online mapping
│            IF isClientID → update_closed_ac(...)  [closing data]
│            ELSE → update_account(...)  [group/acc_no data]
│            IF success → updateData(is_transferred='Y', ...) + track
│        ⚠️ $branch used at L3106 → UNDEFINED VARIABLE (SVC-BUG-022)
├─ getRegisteredAccTransactions('N', '-1', 2, $trans_date)
│    FOREACH:
│        Online payment_type=1: checkClientID + updatePayment()
│        Offline payment_type=2: checkClientID + insertPayment()
│            ├─ insert payment_mode_details
│            ├─ cancel existing pmd (status=9)
│            ├─ get_due_date('service',...) → calculate cycle
│            └─ getPaidInsData + update total_paid_ins on scheme_account
├─ Record sync_log via acc_model->insert_sync()
└─ echo JSON result
```

### Bugs Found in `update_client()`

| Bug | Line | Description |
|---|---|---|
| SVC-BUG-022 | L3106 | `$this->$api_model->updateData($inter_data, $branch, 'customer_reg')` — `$branch` is undefined. Should be `$branch_id`. Fatal PHP Notice / wrong WHERE clause. |
| SVC-BUG-023 | L3023 | `$_GET['sync_trans_date']` unvalidated — date string directly used in DB queries via `getRegisteredAccTransactions()`. No `intval()`, `strtotime()`, or CI input helper. |

---

## D. `services_model.php` — Full Inventory (996 lines)

### Function List

| Function | Line | Purpose |
|---|---|---|
| `insertData` | 11 | Generic insert, returns insert_id |
| `daily_collection` | 18 | Switch: GET or INSERT daily collection data |
| `getTodaySummaryBranchWise` | 55 | Branch+scheme-type collection/close/cancel summary |
| `getTodaySummary` | 108 | Scheme-type-wise collection summary (raw SQL, no filter) |
| `allBranches` | 138 | All active branches for company |
| `getIwalTrans_temp` | 149 | Get inter-wallet temp transactions |
| `getIwalTrans_main` | 159 | Get inter-wallet main transactions |
| `getIwalTranDetail_tmp` | 171 | Get temp transaction details |
| `getIwalTranDetail_main` | 181 | Get main transaction details |
| `insertTransinMain` | 193 | Insert inter-wallet trans to main |
| `insertTransDetailInMain` | 219 | Insert trans detail to main |
| `delTransAndDetail` | 235 | Delete temp trans + detail |
| `updTransin_main` | 248 | Update main trans |
| `updateTransDetail_main` | 254 | Update main trans detail |
| `getWalletAccounts` | 264 | Get wallet accounts (paginated, 200 limit) |
| `getTempData` | 285 | Summary of temp inter-wallet data by date/branch |
| `isPaid` | 415 | Check if month was paid (helper) |
| `updMaturityDate` | 424 | Recalculate scheme maturity date |
| `limitDB` | 455 | Get/insert limit_settings record |
| `customer_count` | 525 | Count all customers |
| `checkService` | 531 | Check if service has SMS/email enabled |
| `get_wallet_acc_number` | 546 | Generate unique wallet account number |
| `walletacc_insert` | 558 | Insert wallet account |
| `get_walletacc` | 563 | Get wallet account with balance |
| `get_SMS_data` | 591 | Get SMS template content for given service_id + entity |
| `deleteNoPayAcc` | 886 | Delete scheme accounts with no valid payments |
| `deleteInvalidPay` | 952 | Delete failed/cancelled payments |
| `checkServiceCode` | 975 | Check if service code has SMS/email/whatsapp enabled |

**Total: 29 functions**

### SQL Injection Risks in `services_model.php`

| Function | Line | Risk |
|---|---|---|
| `daily_collection` | 32, 38 | `$date` concatenated directly (`where date='.$date.'`) |
| `daily_collection` | 32, 38 | `$id_company` concatenated directly |
| `getTodaySummaryBranchWise` | 68, 84, 100 | `$id_branch` concatenated in WHERE |
| `getIwalTrans_temp` | 151 | `$id_br`, `$bill_dt` raw concat |
| `getIwalTrans_main` | 161 | `$tran['id_branch']`, `$tran['bill_no']` raw concat |
| `getIwalTranDetail_tmp` | 173 | `$id` raw concat |
| `getIwalTranDetail_main` | 183 | `$id` raw concat |
| `updMaturityDate` | 428 | `$record->id_scheme_account` raw concat (using `$record` — UNDEFINED variable) |
| `limitDB` | 472 | `$id` raw concat |
| `get_walletacc` | 582 | `$id` conditional concat |
| `get_SMS_data` | 620, 677, 734 | `$id` raw concat in WHERE |
| `deleteNoPayAcc` | 889 | `$from_date`, `$to_date` raw concat |
| `deleteInvalidPay` | 955 | `$from_date`, `$to_date` raw concat |

**Total: 13+ SQL injection risk points in services_model.php**

### Active Debug Output in `services_model.php`

| Line | Code | Risk |
|---|---|---|
| 446 | `echo $maturity;exit;` inside `updMaturityDate()` | **P0: HALTS execution** |
| 890 | `echo $this->db->last_query()." <br/>";` inside `deleteNoPayAcc()` | SQL query exposed |
| 891 | `echo $acc_sql->num_rows();` | Data count leaked |
| 903 | `echo "<pre>".$acc['id_scheme_account']."..."` | Account ID leaked |
| 942 | `echo "<pre>".$acc['id_scheme_account']." deleted ";var_dump($hasValidPay)` | Multiple leaks |
| 971 | `echo $this->db->_error_message();` | DB error exposed |

**SVC-BUG-024: `updMaturityDate()` L446 has `echo $maturity;exit;` — permanently broken (always exits before running update)**

### Critical Bug: `updMaturityDate()` Undefined `$record` Variable

`updMaturityDate()` (L424) uses `$record->is_fixed_maturity`, `$record->id_scheme_account`, `$record->start_date` etc. — but `$record` is not passed as a parameter nor set in scope. The function **never works** and would throw a fatal PHP error on any call.

**SVC-BUG-025: `updMaturityDate()` references `$record`, `$paidByMonth` which are undefined — function is completely broken.**

---

## E. `admin_usersms_model.php` — Inventory (1,920 lines)

### Function Groups

| Group | Functions | Lines |
|---|---|---|
| Encryption | `__decrypt` | 23 |
| Notification IDs | `getnotificationids` | 28 |
| SMS Service Listing | `empty_record`, `get_empty_record`, `get_entry_record`, `get_empty_records`, `get_entry_records` | 44–290 |
| Module Management | `get_modules`, `delete_module`, `update_module_status`, `insert_module`, `update_module` | 174–248 |
| Service CRUD | `get_sms_services`, `insert_service`, `update_service`, `insert_notification`, `update_notification`, `delete_service`, `delete_notification`, `update_sms_status` | 248–795 |
| SMS Data Generation | `Get_service_code_sms`, `get_SMS_data` | 317–732 |
| Customer Lists | `get_allcustomersms_list`, `get_selectcustomersms_data`, `get_customers_by_scheme`, `get_scheme_name`, `get_duecustomer` | 836–1411 |
| Notification Content | `getnotificationtext`, `getnotification_id`, `get_notiData`, `get_cusnotiData`, `getnotiData`, `get_metalnotiContent` | 909–1501 |
| Push Notification | `insert_sent_notification`, `check_noti_settings`, `get_noti_settings`, `get_noticontent`, `get_noti`, `getDevicetokens`, `metalrate_gold` | 1292–1442 |
| Due SMS | `get_SMS_due`, `get_sms_settings` | 1524–1677 |
| Rate Notifications | `get_account`, `getBranches`, `get_cusBranchRate`, `get_metal_rateby_branch`, `get_sendnotifi_cusBranch` | 1677–1748 |
| Cleanup / Delete | `deleteNoPayments_Acc` | 1749 |
| Retention Settings | `get_ret_settings`, `get_empty_recordss`, `get_entry_recordss`, `delete_ret_settings`, `insert_ret_settings`, `update_ret_settings` | 1779–1837 |
| WhatsApp | `send_whatsApp_message` | 1847 |
| Birthday/Wallet | `get_customer_wishes_data`, `get_cus_wallet_data` | 1894–1908+ |
| Schemes | `get_schemes`, `get_schemes_name` | 62–82 |
| Noti Listing | `get_noti_empty_record`, `get_noti_entry_record`, `get_notification_services` | 82–127 |

**Total: ~50+ functions across 1,920 lines**

**admin_usersms_model debug state:** All `print_r` / `echo` statements are commented out — **CLEAN** for production. No active debug output.

---

## F. `sms.js` — AJAX Endpoint Map (794 lines)

### AJAX Calls

| Function | Endpoint | Method | Has Error Handler? |
|---|---|---|---|
| `get_allcustomer_list()` | `sms/send/get_selectcustomer/` | POST | ✅ Yes |
| `get_schemes()` | `sms/get_schemes` | GET | ❌ **No** |
| `get_customer_list(id)` | `sms/get_scheme/{id}` | GET | ✅ Yes |
| `set_smsServices_list()` | `sms/service/ajax` | POST | ✅ Yes |
| Send SMS (all_cus) | `sms/send/group_message_allcus` | POST | ✅ Yes |
| Send SMS (sel_cus) | `sms/send/group_message_selectcus` | POST | ✅ Yes |
| Send SMS (sch_cus) | `sms/send/group_message` | POST | ✅ Yes |
| Send Email (all_cus) | `sms/send/group_email_allcus` | POST | ✅ Yes |
| Send Email (sel_cus) | `sms/send/group_email_cus` | POST | ✅ Yes |
| Send Email (sch_cus) | `email/send/group_message` | POST | ✅ Yes |

**SVC-BUG-026:** `get_schemes()` (L260-291) — AJAX call to `sms/get_schemes` has **no error handler**. If the schemes call fails (network or server error), the scheme `<select>` silently remains empty with no user feedback.

### Global Variables / State
- `var path`, `ctrl_page` — global, set on load from URL
- No significant global mutable state
- All AJAX calls properly disable/re-enable `#btn-send` to prevent double submission

### `console.log` Count
- L304: `console.log(data)` in `get_customer_list()`
- L342: `console.log(row)` inside DataTable render
- L365: `console.log(group)` in `set_email_list()`
- L548, L549: `console.log(selected)`, `console.log(msg)` in send SMS handler
- **5 active `console.log` statements** — minor, but production noise

---

## G. `notification.js` — AJAX Endpoint Map (268 lines)

### AJAX Calls

| Function | Endpoint | Method | Has Error Handler? |
|---|---|---|---|
| `noti_on_off` toggle (ON) | `notification/on_off/1` | POST | ❌ **No** |
| `noti_on_off` toggle (OFF) | `notification/on_off/0` | POST | ❌ **No** |
| `set_notiServices_list()` | `notification/ajax` | POST | ✅ Yes |

**SVC-BUG-027:** `notification.js` — ON/OFF toggle AJAX calls (L31-54) have **no error handler**. If the toggle request fails, the UI switch state changes visually but the server state doesn't update. User sees false confirmation.

### Input Validation
- `#noti_footer`: max 7 chars enforced on blur
- `#send_notif_on`: max 14 chars enforced on blur
- `#send_daily_from`: max 2 chars enforced on blur
- Image upload: size < 1MB, extension JPG/PNG/JPEG enforced

### Global Variables
- `path`, `ctrl_page` — global, set on load
- No other mutable globals

### `console.log` Count
- L145: `console.log(fileName)` in `validateImage()`
- **1 active `console.log`**

---

## H. New Bugs Summary (Round 2)

| Bug ID | P | Description |
|---|---|---|
| SVC-BUG-019 | **P2** | `scheme_freepayment()`: `$receipt_no` used in insert array before being set if `receipt_no_set != 1` → PHP Notice / null receipt |
| SVC-BUG-020 | **P2** | `getFreePayData()`: `$gold_rate` used as divisor — division by zero if metal rate is 0 |
| SVC-BUG-021 | **P1** | `scheme_freepayment()`: SMS/push double-fires for every customer regardless of free instalment gate |
| SVC-BUG-022 | **P0** | `update_client()` L3106: `$branch` undefined → wrong WHERE clause on `updateData()` — sync silently corrupts or skips records |
| SVC-BUG-023 | **P1** | `update_client()`: `$_GET['sync_trans_date']` unvalidated raw date string passed to DB queries |
| SVC-BUG-024 | **P0** | `services_model::updMaturityDate()` L446: `echo $maturity;exit;` — **function permanently halted** — maturity date NEVER updated |
| SVC-BUG-025 | **P0** | `services_model::updMaturityDate()`: `$record` and `$paidByMonth` undefined → function throws fatal error on call |
| SVC-BUG-026 | **P3** | `sms.js::get_schemes()`: no error handler → silent failure if scheme list fails to load |
| SVC-BUG-027 | **P3** | `notification.js`: ON/OFF toggle AJAX has no error handler → UI/server state desync |

---

## I. Log Directory Security Audit

As flagged in Round 1, `log/` directory is inside webroot. Confirmed log path:

```php
$this->log_dir = 'log/';
// used as: 'log/duesms/2026-03-18.txt'
//          'log/cashfree/2026-03-18.txt'
//          'log/easebuzz/...'
//          'log/notification/...'
```

Any log file is directly accessible via:
```
https://[domain]/log/cashfree/2026-03-18.txt
```

Log files contain: payment verification responses, customer names, mobile numbers, gateway transaction IDs, potentially API keys in response bodies.

**Priority: MEDIUM (requires Apache/Nginx config fix outside PHP code)**

---

## J. Deferred Items — All Resolved

| Item | Status |
|---|---|
| `scheme_freepayment()` full trace | ✅ Traced — bugs SVC-019, 020, 021 found |
| `syncInterData()` full trace | ✅ Traced — architectural issue flagged |
| `update_client()` full trace | ✅ Traced — bugs SVC-022, 023 found |
| `services_model.php` (44KB) — full inventory + SQL audit | ✅ Complete — 13+ SQLi points, 2 P0 bugs |
| `admin_usersms_model.php` (73KB) — function inventory | ✅ Inventoried (50+ functions) — clean debug state |
| `sms.js` + `notification.js` — AJAX audit | ✅ Complete — 2 missing error handlers |
| Log directory security | ✅ Confirmed webroot exposure |
