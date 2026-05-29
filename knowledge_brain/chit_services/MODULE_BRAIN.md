# `chit_services` Module Brain

> **Brain Built:** 2026-03-17 17:41 IST  
> **Controller:** `admin/application/controllers/admin_services.php`  
> **Lines:** 7,776 | **Methods:** 73 | **Models:** 10 | **JS:** sms.js, notification.js

---

## 1. Module Scale

| Metric | Value |
|---|---|
| Controller lines | **7,776** |
| Public methods | **73** |
| Models loaded | **10** |
| curl_init() calls | **18** (most with SSL_VERIFYPEER=FALSE) |
| Active `print_r()` (NOT commented) | **35** |
| `print_r(); exit;` halts in production | **10** ← **P0: crashes the service** |
| `$_GET` usages (raw) | **5** |
| `$_POST` usages (raw) | **5** |
| `trans_begin()` calls | **12** |
| `file_put_contents` log writes | **10** |
| `mkdir(0777)` calls | **4** |

---

## 2. Constructor

```php
const MODEL      = "admin_usersms_model";
const CUS_MODEL  = "customer_model";
const SET_MODEL  = "admin_settings_model";
const EMAIL_MODEL= "email_model";
const ADM_MODEL  = "chitadmin_model";
const PAY_MODEL  = "payment_model";
const ACC_MODEL  = "account_model";
const SMS_MODEL  = "admin_usersms_model";  // ← duplicate of MODEL
const LOG_MODEL  = "log_model";
const MAIL_MODEL = "email_model";          // ← duplicate of EMAIL_MODEL
const SERV_MODEL = "services_model";
const SYN_MODEL  = "syncapi_model";
```

**12 constants, 14 model loads in constructor (incl. `sms_model`, `sktm_syncapi_model`, `chit_transaction_model` manually loaded without constants)**

**Session dependencies:**
```php
$this->employee = $this->session->userdata('uid');
$this->company  = $this->admin_settings_model->get_company();
$this->chit_set = $this->admin_settings_model->get_settings();
$this->log_dir  = 'log/';
```

`get_settings()` fires on EVERY request to this controller — same as all other controllers.

---

## 3. Method Groups

### 3A — Cron / Service Methods (Called by scheduled job or URL hit, NOT by UI)

| Method | Line | Purpose |
|---|---|---|
| `check_expiry()` | 102 | Deactivate expired new-arrivals items |
| `send_lowestrate_noti()` | 228 | Push notification for lowest price schemes |
| `send_rate_noti()` | 280 | Push notification for today's metal rates |
| `send_notification()` | 330 | Due alert + birthday + wedding day notifications |
| `send_smsdue()` | 605 | SMS due reminder per configured dates |
| `add_daily_collection()` | 1566 | Nightly rollup of branch collection to `daily_collection` table |
| `update_dueMonthYear()` | 5719 | Recalculate `due_month`/`due_year` for all scheme accounts |
| `trigger_wishes()` | 5744 | Birthday/anniversary SMS+push for today |
| `send_wallet_reminder()` | 5897 | Wallet balance reminder SMS+push |
| `scheme_freepayment()` | 961 | Auto-process free instalment payments for qualifying schemes |
| `send_queue_sms()` | 2097 | Drain SMS queue table and dispatch |
| `deleteNoPayments_Acc()`| 3322 | Delete scheme accounts with no payment records |
| `deleteInvalidPay()` | 5688 | Delete invalid/zero-amount payments |
| `deleteNoPayAcc()` | 5647 | Delete accounts with no payments |

### 3B — Payment Verification (Webhook + Manual)

| Method | Line | Purpose |
|---|---|---|
| `oldverify_cashfreepayment()` | 3436 | OLD Cashfree verify (3-day window, LEGACY) |
| `verifyEasebuzzPayments()` | 4724 | NEW Easebuzz auto-verify (5-day window) |
| `oldverifyEasebuzzPayments()` | 4208 | OLD Easebuzz verify (LEGACY) |
| `cf_verify_pay_manual()` | 5212 | Manual Cashfree verify by transaction ID |
| `razorPay_verify()` | 5325 | Razorpay order verify |
| `verify_payment()` | 7404 | Route/dispatch payment verify by type |
| `payment_verify()` | 7430 | Sub-dispatch by type (cashfree/easebuzz/razorpay) |
| `new_webhook()` | 7494 | Unified webhook receiver (new architecture) |
| `new_verification()` | 7522 | New unified payment verification engine |
| `payment_update()` | 7658 | Final payment status update after verification |
| `checkTransExist()` | 7469 | Check if transaction ID already processed (new) |
| `oldcheckTransExist()` | 7448 | Same, legacy version |

### 3C — Sync / Data Migration

| Method | Line | Purpose |
|---|---|---|
| `syncExistingCusData()` | 2275 | Sync customers from old system |
| `createExistingCus()` | 2293 | Create customer records from legacy data |
| `sync_existing_data()` | 2414 | Sync individual customer data |
| `syncInterData()` | 2685 | Sync inter-branch/inter-system data |
| `loadTempToMainView()` | 1701 | Load temp transactions to main tables |
| `ajaxtempTotest()` | 1713 | AJAX temp-to-test migration |
| `ajaxtempToMain()` | 1739 | AJAX temp-to-main migration |
| `import_off_cusData()` | 3338 | Import offline customer registration CSV |
| `update_client()` | 3009 | Bulk client update — unknown scope |

### 3D — Scheme Duration / Due Type Engine

| Method | Line | Purpose |
|---|---|---|
| `old_dayDurationSchemeService()` | 6887 | OLD: classify payment due dates for day-duration schemes |
| `dayDurationSchemeService()` | 6979 | NEW: classify due_type (ND/AD/PD/MND) for all active payments |
| `upd_total_paid_ins()` | 7277 | Update total paid installments count per account |
| `upd_firstPayAs_startDate()` | 7315 | Set first payment as scheme start date |
| `upd_firstPayAs_firstPayAmt()` | 7355 | Set first payment amount on scheme account |
| `update_dueMonthYear()` | 5719 | Batch update due_month/due_year across all accounts |

### 3E — Notification / SMS Utilities

| Method | Line | Purpose |
|---|---|---|
| `send_singlealert_notification()` | 758 | OneSignal push to single player ID |
| `send_singlealert_rate_notification()` | 2472 | OneSignal push for metal rate (custom payload) |
| `onesignalNotificationToAll()` | 1501 | OneSignal broadcast to all subscribers |
| `sendSMSMail()` | 1177 | Dispatch SMS + email for a service event |
| `send_sms()` | 1235 | SMS gateway router (MSG91/Nettyfish/SpearUC/Asterixt/Qikberry) |
| `send_mjdmarate_notification()` | 1273 | MJDMA (3rd party) rate sync |
| `sendmjdmarate_noti()` | 1310 | MJDMA notification dispatch |
| `send_wishesToCus()` | 5799 | Send birthday/anniversary wishes to one customer |
| `sendwalletreminder()` | 5907 | Send wallet reminder to one customer |

### 3F — Refund Engine

| Method | Line | Purpose |
|---|---|---|
| `createRefund()` | 6028 | Initiate payment gateway refund (Cashfree/Easebuzz) |
| `getRefundStatusHook()` | 6333 | Check refund status via webhook-style poll |
| `getRefundStatus()` | 6498 | Poll refund status from gateway API |
| `generateRandomUniqueString()` | 6240 | Generate refund reference ID |

### 3G — Utility / Misc

| Method | Line | Purpose |
|---|---|---|
| `set_image()` | 135 | Upload notification image |
| `upload_img()` | 170 | Process and resize uploaded image |
| `scheme_freepayment()` | 961 | Free instalment auto-processing |
| `getFreePayData()` | 1049 | Get data for free payment processing |
| `generate_receipt_no()` | 1121 | Generate scheme receipt number |
| `genInstallmentNo()` | 3976 | Generate instalment number |
| `insert_common_data()` | 4045 | Common data insert after payment verify |
| `insert_common_data_jil()` | 3989 | JIL-specific common data insert |
| `insert_referral_data()` | 4103 | Insert referral reward after payment |
| `postRequest()` | 2141 | Generic HTTP POST via curl |
| `Nettyfish_smsGateway()` | 2228 | Nettyfish SMS gateway direct call |
| `parse_customer_reg()` | 3368 | Parse offline CSV customer reg data |
| `removeElementWithValue()` | 2027 | Array utility — remove by value |
| `test()` | 6684 | Test/debug endpoint |
| `update_paymentMode()` | 6834 | Update payment mode on a payment record |
| `passwdord_hash()` | 6872 | Password hashing utility |
| `ajax_interWallet_trans()` | 1682 | AJAX: inter-wallet transactions |
| `updateTransDetailInmain()` | 1988 | Update temp→main transaction details |

---

## 4. Critical Architecture: `dayDurationSchemeService()` (L6979)

**This is the most complex method in the controller.** It classifies what type of payment is due for each active scheme account on day-duration schemes.

```
dayDurationSchemeService($id_scheme, $id_scheme_account):
├─ Get all payments that match scheme/account filters
├─ FOR each payment:
│    ├─ Read payment settings (max_chance, allow_advance, allow_unpaid, advance_months)
│    ├─ get_due_date('current_range', $date, $id_scheme_account) → date range
│    ├─ SELECT COUNT due_type FROM payment WHERE paid + in_range GROUP BY due_type
│    ├─ get_due_date('allow_pay', $date, $id_scheme_account) → remaining dues
│    ├─ Calculate: chances_allowed_due = max_chance - (paid_multiple + paid_normal)
│    ├─ CLASSIFY due_type:
│    │    ND  = Normal Due (this month)
│    │    AD  = Advance Due (paid ahead)
│    │    PD  = Pending Due (overdue)
│    │    MND = Multiple Normal Due (multi-chance scheme)
│    │    PN  = Pending + Normal
│    │    AN  = Advance + Normal
│    └─ UPDATE payment SET due_type = $due_type, allowed_due = $allowed_due
└─ (One loop iteration = ~3 queries per payment)
```

**⚠️ N+1 pattern:** 3 queries fired per payment record. For 10,000 accounts → 30,000 queries per cron run.

**⚠️ Active debug code:** Multiple `print_r(...);exit;` inside this method that would halt the entire cron job if the wrong payment ID appears:
```php
// L7056: print_r($this->db->last_query());exit;    ← NOT commented
// L7066: print_r($paid_dueData);exit;              ← NOT commented
// L7085: print_r($this->db->last_query());exit;    ← NOT commented
// ...10 more active print_r/exit blocks
```
These are wrapped in `/* if($pay['id_payment'] == 71223) { ... } */` — but the `*/` closure may be misplaced in some cases.

---

## 5. Critical Architecture: `new_verification()` (L7522)

The new unified payment verification engine handles Cashfree, Easebuzz, and Razorpay webhook/manual verification.

```
new_verification($type, $data):
├─ Check trans exists (checkTransExist)
├─ IF type = 'cashfree':
│    Build CF API request → curl → parse response
│    IF payment_status == 'SUCCESS':
│        insert_common_data($id_payment)
│        insert_referral_data()
│        payment_update($payment)
│        write log to log/cashfree/
├─ IF type = 'easebuzz': similar flow
├─ IF type = 'razorpay': similar flow
└─ All 3 gateway verify calls → CURLOPT_SSL_VERIFYPEER = FALSE (disabled)
```

---

## 6. `add_daily_collection()` (L1566) — Critical Data Integrity Issue

```php
function add_daily_collection() // NOTE: executed on 2AM of next day
{
    $previousDay = date('Y-m-d', strtotime("-2 days"));  // ← -2 days, not -1
    foreach ($branch as $br) {
        $ydaytoday = $model->daily_collection('get', $previousDay, '', $br['id_branch']);
        $today = $model->getTodaySummaryBranchWise(date('Y-m-d', strtotime("-1 days")), $br['id_branch']);
        // Calculate rolling balance...
        echo print_r($instoday);  // ← L1663: ACTIVE print_r in production!
        $this->$model->daily_collection('insert', '', $instoday);
    }
}
```

**Bug:** `echo print_r($instoday)` at L1663 — `print_r` with no `return` param dumps to output AND `echo` wraps it, so every cron execution dumps all branch data as raw output to the browser/cron log.

---

## 7. Session + Input Inputs Security Map

| Raw Access | Lines | Risk |
|---|---|---|
| `$_GET['id_branch']` | L1686, L1874 | Used in DB query — SQLi if uncast |
| `$_GET['date']` | L1688, L1876 | Used in date field — SQLi if unvalidated |
| `$_GET['sync_trans_date']` | L3023 | Passed to sync function |
| `$_POST['id_branch']` | L1716, L1744 | DB query param |
| `$_POST['entry_date']` | L1718, L1746 | Date filter |
| `$_POST['till_updated']` | L1724, L1748 | Sync column marker |
| `$_POST['sync_branch_id']` | L3019 | Sync function param |

All 5 `$_GET` and 5 `$_POST` raw accesses are in methods that do NOT use CI input helper.

---

## 8. SMS Gateway Architecture

Supports 5 SMS gateways, selected by `config->item('sms_gateway')`:

| Code | Gateway | Method |
|---|---|---|
| 1 | MSG91 | `sms_model->sendSMS_MSG91()` |
| 2 | Nettyfish | `sms_model->sendSMS_Nettyfish()` |
| 3 | SpearUC | `sms_model->sendSMS_SpearUC()` |
| 4 | Asterixt | `sms_model->sendSMS_Asterixt()` |
| 5 | Qikberry | `sms_model->sendSMS_Qikberry()` |

Config-driven switch — changing gateway requires only `config/config.php` update. Clean architecture.

**Duplicate Model Issue:** Same pattern as `masters` — `MODEL` = `SMS_MODEL` = `admin_usersms_model`.

---

## 9. Cross-Module Dependencies

| Module | Relation | Tables/Methods |
|---|---|---|
| `payment` | Direct: verify and update payments | `payment`, `transaction`, `receipt` |
| `masters` | Settings on every request | `chit_settings`, `notification`, `branch` |
| `customer` | Fetch tokens/mobiles for push/SMS | `customer`, `customer_reg` |
| `account` | Read scheme account data | `scheme_account`, `wallet_account` |
| `scheme` | Free payment processing | `scheme`, `scheme_account` |
| `chit_collection_app` | Webhook/verify overlap | Mobile payment webhook handlers |
| `Billing` | Daily collection rollup | `daily_collection`, branch summaries |
| `Tagging` | Sync/transfer data | `customer_reg` sync |
| External: OneSignal | Push notifications | 18 curl_init calls |
| External: SMS gateways (5) | SMS dispatch | Via `sms_model` |
| External: Cashfree | Payment verify | `new_verification()`, `cf_verify_pay_manual()` |
| External: Easebuzz | Payment verify | `verifyEasebuzzPayments()` |
| External: Razorpay | Payment verify | `razorPay_verify()` |
