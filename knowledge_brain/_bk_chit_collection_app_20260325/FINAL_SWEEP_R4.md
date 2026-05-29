# chit_collection_app — Round 4 Final Sweep

> **Brain Updated:** 2026-03-17 | **Round:** 4 (Final)

---

## 1. `monthly_agent_reports()` — Agent Monthly Dashboard

**File:** `adminappapi_model.php` | **Lines:** 2041–2103  
**Called by:** `agentWise_monthly_reports_post()`

### Flow

```
Input: ($id_employee, $login_type)
        ↑ $login_type is accepted but NEVER used in query filters

Date range: Always current month (Y-m-01 → Y-m-d)

Step 1: SELECT id_customer, mobile FROM customer WHERE id_agent = $id_employee
        ⚠️ BUG-034: $id_employee raw concatenation → SQL injection

Step 2: For each customer (N+1 query pattern):
    Query A — total_collection[]:
        SELECT c.firstname, c.mobile, SUM(payment_amount)
        FROM customer → scheme_account → payment
        WHERE payment_status=1 AND date(date_payment) BETWEEN month
        → appended to $total_collection[] array

    Query B — monthly_pending[]:
        Complex CTE-style subquery to find next_due date
        (CASE: is_opening + last_paid | or max(payment) + 1 month)
        WHERE date(next_due) BETWEEN current month
        → appended to $monthly_pending[] array

    Query C — completed[]:
        IDENTICAL to Query A — exact same SQL as total_collection
        ⚠️ BUG-035: $total_collection and $completed are populated
                    from the same query. Both arrays will always
                    return the same data. "completed" is meaningless
                    redundancy or a copy-paste error.

Returns: ['total' => $total_collection, 'pending' => $monthly_pending, 'completed' => $completed]
```

### Bugs Found

| Bug | Description |
|---|---|
| **COL-BUG-034** | SQL injection: `id_agent = $id_employee` raw concat in customer query (L2048) |
| **COL-BUG-035** | `$completed` and `$total_collection` are populated from identical SQL queries — `completed` always equals `total` (L2084-2088 vs L2058-2062) |
| **COL-BUG-036** | N+1 query problem: for each customer fetched in Step 1, 3 separate DB queries execute. An agent with 200 customers = 601 queries per API call. No pagination. |
| **COL-BUG-037** | `$login_type` parameter accepted but never used — for non-AGENT login types (e.g., EMP), the function still queries by `id_agent` which is wrong. Employee-login monthly report will return data for an agent ID that matches by coincidence or return empty. |

---

## 2. `customer_reports()` — Agent Customer Ledger List

**File:** `adminappapi_model.php` | **Lines:** 2105–2125  
**Called by:** `customer_ledger_post()`

### Flow

```
Input: ($id_employee, $login_type)

SELECT c.id_customer, c.firstname, c.lastname, c.mobile, c.email, c.id_branch
FROM customer c
[WHERE c.id_agent = $id_employee]   -- Only IF login_type == 'AGENT'
                                     -- EMP login: returns ALL customers

For each:
    $file = CUS_IMG_PATH . '/' . id_customer . '/customer.jpg'
    $img_path = (cus_img != null AND file_exists($file)) ? $file : null
    → Return subset: id_customer, firstname, lastname, email, mobile, cus_img
```

### Bugs Found

| Bug | Description |
|---|---|
| **COL-BUG-038** | For EMP login, the query has no WHERE clause → returns ALL customers in the database. Could be hundreds of thousands of records depending on client scale. No pagination. |
| **COL-BUG-039** | `$row['cus_img']` is checked but the column is NOT in the SELECT list. `$row['cus_img']` will always be `undefined/null` → `$img_path` will always be null regardless of actual customer image status. |
| **COL-BUG-040** | `ucfirst($row['mobile'])` at L2118 — `ucfirst()` on a mobile number is meaningless (capitalizes first digit, no-op on numbers). Left over from copy-paste. Low severity but reveals code quality issue. |

---

## 3. `pdc_report()` — Post-Dated Cheque Report View

**File:** `paymt.php` | **Lines:** 3249–3259

```
GET /paymt/pdc_report
├─ payment_modal::get_pdc_report() → PDC details
├─ payment_modal::get_pdcs() → PDC summary totals
├─ Load view: chitscheme/pdc_report (via template layout)
└─ No auth check — session check only at constructor level
```

**Assessment:** Thin passthrough view. No bugs detected. No session check inside function (relies on constructor `$this->comp` which only loads if session exists — implicit guard only).

---

## 4. `split_payment()` — Multi-Due Payment Split

**File:** `paymt.php` | **Lines:** 3297–3368

```
Input: ($id_payment)
Purpose: When a payment covers N installments (no_of_dues > 1),
         this creates N-1 additional payment records (split copies)

Flow:
├─ payment_modal::getPaymentByID($id_payment) → original payment
├─ $dues = no_of_dues - 1  (number of additional records to create)
├─ For $i = 1 to $dues:
│    $paid_date = date_payment + $i months
│    INSERT new payment: {
│        id_scheme_account,
│        id_transaction:  "{$txnid}-{$i}",  ← suffixed to create unique ref
│        payment_amount:  same as original,
│        payment_type:    "Payu Checkout",   ← ⚠️ HARDCODED — wrong for Cashfree/HDFC
│        date_payment:    future month,
│        payment_status:  success or awaiting (auto_pay_approval)
│    }
│    serviceID = 7  ← ⚠️ SAME BUG as COL-BUG-002: uses failure service for split success
│    IF service.sms==1: send SMS via gateway
│    IF service.email==1: send email
└─ No trans_begin/commit — split rows inserted without transaction guard!
```

### Bugs Found

| Bug | Description |
|---|---|
| **COL-BUG-041** | `payment_type = "Payu Checkout"` hardcoded for ALL split payments regardless of actual gateway used. Cashfree/HDFC/Atom splits will have wrong payment_type. |
| **COL-BUG-042** | `$serviceID = 7` (failure service) used for split payment SMS notification — same pattern as COL-BUG-002. Split payment success notifications use failure SMS template. |
| **COL-BUG-043** | No `trans_begin()`/`trans_commit()` wrapping split inserts. If insert #3 of 5 fails, #1 and #2 are already committed — orphaned partial-split records with no rollback. |
| **COL-BUG-044** | `$serv_model` variable set to `self::SERV_MODEL` at L3299 but never actually loaded with `$this->load->model()`. The service model is accessed through `$this->services_modal` elsewhere, but here it would use `$this->$serv_model->get_SMS_data()` — will fail if serv_model isn't already loaded by constructor. |

---

## 5. `wallet_transactionDB()` (paymt.php local version) — Wallet Debit for Web Payments

**File:** `paymt.php` | **Lines:** 3379–3404

```
Input: ($payamt, $totamtuse_wallet) — 2-parameter local version

Purpose: THIS version debits the company-side wallet (settings-based wallet account),
         NOT the customer inter_wallet_account.

Flow:
├─ payment_modal::get_wallet_accounts() → active wallet account(s) from settings
├─ For each active wallet:
│    INSERT wallet_transaction: {
│        id_wallet_account,
│        id_employee: 2,      ← ⚠️ HARDCODED employee ID = 2
│        transaction_type: 1, ← debit
│        value: $payamt,
│        description: 'Scheme Payment'
│    }
│    payment_modal::wallet_settingDB($wallet_id, $insertData)
│    ← NOTE: redirect/success flash logic is COMMENTED OUT (L3393-3399)
│       So function always returns true silently
└─ return true (always)
```

**This is a DIFFERENT wallet** from `payment_modal::wallet_transactionDB()` which operates on `inter_wallet_account`. This local version operates on a "system wallet" (general wallet settings), used to record that the company received a payment via wallet.

**COL-BUG-045:** `id_employee: 2` hardcoded — always credits the transaction to employee ID 2 regardless of who processed the payment.

---

## 6. `send_sms()` / `update_otp()` — Legacy SMS Functions (paymt.php)

**Lines:** 3261–3294

These are **legacy SMS functions inside paymt.php** using the old URL-based SMS provider from `sms_api_settings` table. They are NOT used in the modern payment flows (those use `$this->sms_model->sendSMS_MSG91()` etc.). They appear to be dead code but remain in the file.

`update_otp()` at L3284: Decrements `sms_api_settings.debit_sms` WHERE `id_sms_api=1`. Hardcoded `id_sms_api=1` — only works if the legacy SMS provider row has ID 1.

---

## Final Bug Count Additions (Round 4)

| Bug ID | Severity | File | Line | Description |
|---|---|---|---|---|
| **COL-BUG-034** | P1 | `adminappapi_model.php` | L2048 | SQL injection in `monthly_agent_reports` — `id_agent = $id_employee` raw concat |
| **COL-BUG-035** | P2 | `adminappapi_model.php` | L2084-2088 | `$completed` array = duplicate of `$total_collection` — same query used twice |
| **COL-BUG-036** | P2 | `adminappapi_model.php` | L2054-2091 | N+1 query loop — 3 queries per customer, no pagination |
| **COL-BUG-037** | P2 | `adminappapi_model.php` | L2041 | `$login_type` unused — EMP login gets wrong data (agent ID used instead of employee ID) |
| **COL-BUG-038** | P1 | `adminappapi_model.php` | L2108-2109 | EMP login fetches ALL customers — no WHERE clause, full table scan |
| **COL-BUG-039** | P2 | `adminappapi_model.php` | L2117 | `$row['cus_img']` not in SELECT list — always null, image always missing |
| **COL-BUG-040** | P3 | `adminappapi_model.php` | L2118 | `ucfirst()` applied to mobile number — no-op, copy-paste error |
| **COL-BUG-041** | P2 | `paymt.php` | L3313 | `payment_type = "Payu Checkout"` hardcoded in split — wrong for all non-PayU gateways |
| **COL-BUG-042** | P1 | `paymt.php` | L3324 | `$serviceID=7` (failure) used for split payment success SMS — same class as COL-BUG-002 |
| **COL-BUG-043** | P1 | `paymt.php` | L3297 | No `trans_begin/commit` around split inserts — partial splits cannot be rolled back |
| **COL-BUG-044** | P2 | `paymt.php` | L3299 | `$serv_model` set but never loaded — SMS in split_payment may fatal if not loaded earlier |
| **COL-BUG-045** | P3 | `paymt.php` | L3386 | `id_employee: 2` hardcoded in local `wallet_transactionDB` |
