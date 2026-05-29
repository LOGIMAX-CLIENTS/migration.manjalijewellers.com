# `chit_services` — Data Flow Map

> **Brain Built:** 2026-03-17

---

## Flow 1: Nightly Cron — Payment Due Classification (`dayDurationSchemeService`)

```
Cron trigger → /services/dayDurationSchemeService
│
├─ services_model: get all active payments for day-duration schemes
│   WHERE payment_status = 0 (pending)
│
├─ FOR EACH payment:
│   ├─ Read scheme settings: max_chance, allow_advance, allow_unpaid, advance_months
│   ├─ payment_model.get_due_date('current_range', date, id_sch_ac)
│   │   → Returns date range for current due period
│   │
│   ├─ RAW SQL: SELECT COUNT(due_type) FROM payment
│   │   WHERE payment_status=1 AND id_sch_ac=X AND date BETWEEN range
│   │   GROUP BY due_type
│   │   → Returns: paid_normal_due, paid_advance_due, paid_pending_due
│   │
│   ├─ payment_model.get_due_date('allow_pay', date, id_sch_ac)
│   │   → Returns remaining dues allowed
│   │
│   ├─ Calculate: chances_allowed_due = max_chance - (paid_multiple + paid_normal)
│   │
│   ├─ CLASSIFY due_type:
│   │   chances_allowed <= 1 AND remaining > 0:
│   │     normal>0 → ND | pending>0 → PD | advance>0 → AD
│   │     normal + pending → PN | normal + advance → AN
│   │   chances_allowed > 1:
│   │     normal → ND | multiple → MND | pending → PD | advance → AD
│   │
│   └─ UPDATE payment SET due_type=X, allowed_due=X
│       ⚠️ BUG SVC-BUG-001: Active print_r;exit in this loop
│
└─ Complete (no transaction wrapping — each UPDATE is auto-commit)
```

**Tables touched:** `payment`, `scheme`, `scheme_account`

---

## Flow 2: Payment Gateway Webhook → New Unified Verification Engine

```
Gateway webhook → /services/new_webhook
│
├─ Parse gateway type from payload ($data)
├─ checkTransExist($transactionId, $transStatus, $id_payments)
│   → Check sent_notification / payment tables for duplicate
│
├─ payment_verify($type, $data)
│   │
│   ├─ CASHFREE:
│   │   curl_init → api.cashfree.com/pg/orders/{txn_id}/payments
│   │   Headers: x-client-id, x-client-secret, x-api-version: 2022-09-01
│   │   SSL_VERIFYPEER = FALSE ← ⚠️ SVC-BUG-008
│   │   IF payment_status == 'SUCCESS':
│   │       insert_common_data($id_payment)
│   │       insert_referral_data($id_sch_ac)
│   │       payment_update($payment)
│   │       LOG → log/cashfree/mob_response_YYYY-MM-DD.txt
│   │
│   ├─ EASEBUZZ:
│   │   Similar pattern — POST to Easebuzz verify API
│   │   Verify HMAC hash of response payload
│   │   IF success: insert_common_data → referral → update
│   │
│   └─ RAZORPAY:
│       Verify order via Razorpay API
│       IF success: insert_common_data → referral → update
│
└─ Echo result to webhook caller
```

**Tables touched:** `payment`, `transaction`, `receipt`, `wallet_account`, `scheme_account`, `referral`

---

## Flow 3: Daily Notification Service (`send_notification`)

```
Cron trigger → /services/send_notification
│
├─ check_noti_settings() → 0/1 (is notifications enabled?)
│
├─ IF enabled:
│   │
│   ├─ DUE ALERT (notification_service = 4):
│   │   get_noti_settings(4) → { send_daily_from, send_notif_on }
│   │   IF today >= send_daily_from OR today in send_notif_on:
│   │       get_cusnotiData('4') → { header, footer, data[{token, message, mobile, id_customer}] }
│   │       FOR each customer with currentpaycount == 0:
│   │           send SMS (via configured gateway 1-5) ← only if first notice
│   │           send_singlealert_notification() → OneSignal
│   │           insert_sent_notification() → log push sent
│   │   ⚠️ $alertcount used before init — SVC-BUG-006
│   │
│   ├─ BIRTHDAY WISH (notification_service = 7):
│   │   get_cusnotiData('7') → customers with birthday today
│   │   FOR each:
│   │       send SMS (gateway 1-5)
│   │       send_singlealert_notification() → OneSignal
│   │       insert_sent_notification()
│   │
│   └─ WEDDING DAY WISH (notification_service = 8):
│       get_cusnotiData('8') → customers with anniversary today
│       FOR each:
│           send SMS + push
│           insert_sent_notification()
│
└─ Return $result array
```

**Tables:** `notification`, `customer`, `scheme_account`, `sent_notification`

---

## Flow 4: Nightly Daily Collection Rollup (`add_daily_collection`)

```
Cron trigger at 2AM → /services/add_daily_collection
│
├─ $previousDay = date("-2 days")   ← day before yesterday
├─ services_model.allBranches() → all active branches
│
├─ FOR each branch:
│   ├─ daily_collection.get($previousDay, $id_branch) → yesterday's closing balance
│   ├─ services_model.getTodaySummaryBranchWise("-1 day", $id_branch)
│   │   → { collection: {amt, wgt}, closed: {amt, wgt}, canceled: {amt, wgt} }
│   │
│   ├─ Calculate:
│   │   closing_balance_amt = prev_closing + today_collection - today_closed - today_canceled
│   │   closing_balance_wgt = (same for weight)
│   │   closing_weight = (same for weight measure)
│   │
│   ├─ echo print_r($instoday)  ← ⚠️ SVC-BUG-002: leaks financial data to output
│   │
│   └─ daily_collection.insert($instoday) → INSERT to daily_collection
│
├─ trans_commit() IF trans_status === TRUE
└─ Echo result
```

**Tables:** `daily_collection`, `payment`, `scheme_account`, `branch`

---

## Flow 5: SMS Queue Drain (`send_queue_sms`)

```
Cron trigger → /services/send_queue_sms
│
├─ admin_usersms_model.get_pendingSMS() → pending SMS queue records
│
├─ FOR each queued SMS:
│   ├─ Build message from template
│   ├─ send via gateway (config-driven, gateways 1-5)
│   ├─ IF success:
│   │   LOG → log/queuesms/YYYY-MM-DD.txt
│   │   update_pendingSMS(id) → mark as sent
│   └─ ELSE: LOG failure
│
└─ return count sent
```

---

## Flow 6: Refund Creation + Status Check (`createRefund` + `getRefundStatus`)

```
UI trigger → /services/createRefund [POST]
│
├─ Fetch payment record + gateway info
├─ generateRandomUniqueString(16) → refund_id
│
├─ IF gateway = Cashfree:
│   curl → api.cashfree.com/pg/orders/{order_id}/refunds
│   POST: { refund_id, refund_amount, refund_note }
│   LOG → log/cashfree/refund_YYYY-MM-DD.txt
│
├─ IF gateway = Easebuzz:
│   curl → similar Easebuzz refund API
│
├─ IF success: INSERT to refund table, UPDATE payment status
└─ Echo result
│
Status Check → /services/getRefundStatus [GET]
│
├─ Fetch pending refunds
├─ FOR each: poll gateway API for status
├─ IF status = 'SUCCESS': update refund.status, payment.refund_status
└─ LOG result
```

**Tables:** `payment`, `refund`, `transaction`, `payment_gateway_settings`
