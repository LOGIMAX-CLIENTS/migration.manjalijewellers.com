# Recipe: Payment Gateway Webhook Implementation for Old Clients

> Ports the full Cashfree / Easebuzz / Razorpay webhook + auto/manual verification engine into old clients that were deployed before `Chit_transaction` existed. Apply this recipe end-to-end in order — do not skip or reorder steps.

## Metadata
- **Pattern ID**: PAT-PAY-WH-001
- **Severity**: HIGH
- **Modules Affected**: Payment, Services, Account, Mobile API
- **Auto-fixable**: No (multi-file, requires controller copy + model additions)

## Client Scope
- **Applies to**: Old clients that do NOT have `admin/application/controllers/chit_transaction.php`
- **Reason**: Newer clients ship with this controller. Old clients were onboarded before the webhook engine existed and need it backported.

## Created By
- **Developer**: Antigravity AI / Rahul J
- **Client**: srivallivilasjewellery.in (first backport reference)
- **Date**: 2026-04-21
- **Source Bug ID**: feature/https/pm.logimaxindia.com/admin/pm/tasks/e75655d7-84f3-469a-920b-8048e3a0684e

---

## Symptom
- Online payments via Cashfree / Easebuzz / Razorpay remain stuck in **Pending** status even after the customer successfully pays.
- Gateway posts the webhook but nothing updates in the database — no handler exists.
- Admin has no manual re-verification endpoint.
- No auto-verification cron job exists on this client.

## Root Cause
The old `mobile_api.php` has a legacy `cashfreeResponse_post()` that handles only Cashfree v2 payloads. There is no unified webhook controller, no gateway-specific payload parser, and the required `payment_model` methods for batch status updates do not exist.

Additionally, the Cashfree order creation call in `mobile_payment_post()` does NOT include `order_tags` in the payload. This means Cashfree has no way to pass the internal `id_payment` values back in the webhook — so even after the webhook engine is in place, the `parseCashfreeWebhook()` parser cannot identify which DB payment rows to update. The API URL also had a hardcoded `"pg/orders"` suffix that is now passed as part of `api_url` from the `gateway` table.

---

## Detection (Run First — Confirm Recipe Applies)

```command
# 1. Recipe applies ONLY if this file does NOT exist:
ls admin/application/controllers/chit_transaction.php

# 2. Confirm old handler is still active:
grep -n "function cashfreeResponse_post" application/controllers/mobile_api.php

# 3. Confirm new model methods are missing:
grep -n "function updateGatewayResponse\|function getBranchGatewayData\|function getPendpayment_Data" admin/application/models/payment_model.php
```

```command
# 4. Also check if order_tags is missing from Cashfree order creation (mobile_payment_post):
grep -n "order_tags" application/controllers/mobile_api.php
```

**Proceed only if:** `chit_transaction.php` does NOT exist AND `updateGatewayResponse` is NOT in `payment_model.php`.
**Step 3a applies additionally if:** `order_tags` is NOT found in `mobile_api.php`.

---

## Files Changed

| File | Action |
|---|---|
| `admin/application/controllers/chit_transaction.php` | NEW — copy from reference client |
| `admin/application/models/payment_model.php` | MODIFY — add 7 methods |
| `admin/application/models/services_model.php` | MODIFY — add 2 methods |
| `admin/application/models/account_model.php` | MODIFY — uncomment 1 fn + add 1 param |
| `application/controllers/mobile_api.php` | MODIFY — rename old handler + add stub + fix Cashfree order creation |
| `admin/application/config/routes.php` | MODIFY — add 3 routes |

---

## Fix Steps (Do In This Order)

### Step 1 — Pre-Flight: Check DB Tables Exist

Run these in the client's DB before touching any code. All must return rows:

```sql
DESCRIBE gateway;         -- must have: param_1, param_2, param_3, param_4, api_url, pg_code, id_pg, id_branch, is_default, active
DESCRIBE payment;         -- must have: ref_trans_id, payu_id, payment_status, id_payGateway, payment_ref_number
SHOW TABLES LIKE 'payment_mode';
SHOW TABLES LIKE 'services';
DESCRIBE services;        -- must have: id_services, serv_sms, serv_email, dlt_te_id
```

If `gateway` table is missing, stop — the client has no PG configured yet.

---

### Step 2 — Bootstrap the Log Directory

```bash
mkdir -p /var/www/html/<TARGET_CLIENT>/log
chmod -R 0777 /var/www/html/<TARGET_CLIENT>/log
```

---

### Step 3 — Stub the Old `cashfreeResponse_post` in `mobile_api.php`

**File:** `application/controllers/mobile_api.php`

Find the existing `cashfreeResponse_post` method. Rename it to `old_cashfreeResponse_post` (keep the old code intact for reference). Then add the new passive stub directly after it.

#### Before
```php
public function cashfreeResponse_post()
{
    // ... old Cashfree v2 implementation (50–200 lines) ...
}
```

#### After
```php
// RENAMED — old Cashfree v2 handler kept for reference only
public function old_cashfreeResponse_post()
{
    // ... old implementation unchanged ...
}

// NEW — Cashfree V4 passive stub
// Real processing is handled by Chit_transaction::payment_verify(1,4)
// Returns HTTP 200 immediately so the PG does not retry the webhook
public function cashfreeResponse_post()
{
    $response = array(
        "status" => TRUE,
        "title"  => "PAYMENT PROCESS",
        "msg"    => "Your payment is currently being processed, please wait for some time, thank you for your patience..."
    );
    $this->response($response, 200);
}
```

---

### Step 3a — Fix Cashfree Order Creation in `mobile_payment_post()` (same file: `mobile_api.php`)

> **Why this matters:** The `order_tags` payload is how Cashfree passes your internal `id_payment` values back inside the webhook. Without this, `parseCashfreeWebhook()` in `Chit_transaction` cannot identify which payment rows to update — the webhook will be received but silently do nothing.

**File:** `application/controllers/mobile_api.php` — inside `mobile_payment_post()` (the Cashfree order creation section)

#### Change 1 — Initialise `$id_payments` array before the payment insert loop

Find the line `// $this->db->trans_begin();` just before the `foreach ($sch_payment as $pay)` loop. Add the initialisation on the line below it:

```php
// BEFORE:
// $this->db->trans_begin();

foreach ($sch_payment as $pay){
```

```php
// AFTER:
// $this->db->trans_begin();
$id_payments = [];          // collect inserted payment IDs for Cashfree order_tags

foreach ($sch_payment as $pay){
```

#### Change 2 — Collect each inserted `id_payment` inside the loop

Inside the same `foreach` loop, find the line `$payment = $this->payment_modal->addPayment($insertData);`. Add the collection block immediately after it:

```php
// BEFORE:
$payment = $this->payment_modal->addPayment($insertData);

$i++;
```

```php
// AFTER:
$payment = $this->payment_modal->addPayment($insertData);

if (isset($payment['insertID'])) {
    $id_payments[] = $payment['insertID'];  // accumulate for order_tags
}

$i++;
```

#### Change 3 — Pass `id_payments` into the `$params` array sent to Cashfree order creation

Find the `$params = array(...)` block that builds the Cashfree order payload. It ends with `"id_customer" => $cusData['id_customer']`. Add `id_payments` as a new key:

```php
// BEFORE:
"id_customer" => $cusData['id_customer']
```

```php
// AFTER:
"id_customer" => $cusData['id_customer'],
"id_payments" => $id_payments
```

#### Change 4 — Build `order_tags` from `$id_payments` and inject into Cashfree payload

Find the block where `$appId = $paycred['param_3'];` is set (just before the Cashfree cURL payload is built). Add the `order_tags` builder immediately after it:

```php
// BEFORE:
$appId = $paycred['param_3'];



// ... cURL payload array starts here ...
```

```php
// AFTER:
$appId = $paycred['param_3'];
$order_tags = [];
foreach ($params['id_payments'] as $index => $id) {
    $order_tags["payment_" . ($index + 1)] = (string)$id;
    // e.g. ["payment_1" => "10057", "payment_2" => "10058"]
}

// ... cURL payload array starts here ...
```

Then inside the Cashfree cURL payload array, add `order_tags` as a new field:

```php
// BEFORE (end of payload array):
        ),



    CURLOPT_URL => ...
```

```php
// AFTER:
        ),
        "order_tags" => $order_tags,



    CURLOPT_URL => ...
```

#### Change 5 — Fix Cashfree API URL (remove hardcoded suffix)

The `api_url` from the `gateway` table now contains the full URL including `/pg/orders`. Remove the hardcoded suffix from the cURL call:

```php
// BEFORE:
CURLOPT_URL => $paycred['api_url']."pg/orders",
```

```php
// AFTER:
CURLOPT_URL => $paycred['api_url'],
// Note: api_url in the gateway table must now store the full endpoint
// e.g. https://api.cashfree.com/pg/orders
```

> **DB update required:** Update the `gateway` table row for Cashfree to include the full URL:
> ```sql
> UPDATE gateway SET api_url = 'https://api.cashfree.com/pg/orders' WHERE pg_code = 4;
> -- For sandbox/test:
> UPDATE gateway SET api_url = 'https://sandbox.cashfree.com/pg/orders' WHERE pg_code = 4;
> ```

---

### Step 4 — Update `account_model.php`

**File:** `admin/application/models/account_model.php`

#### 4a — Uncomment `generateClientID()`

Search for `generateClientID` — it will be commented out. Remove the comment block:

```php
// BEFORE (commented out):
/* function generateClientID($cliData){
    $clientID = 'LMX/'.$cliData['sync_scheme_code'].'/'.$cliData['ac_no'];
    return $clientID;
}*/

// AFTER (active):
function generateClientID($cliData){
    $clientID = 'LMX/'.$cliData['sync_scheme_code'].'/'.$cliData['ac_no'];
    return $clientID;
}
```

#### 4b — Add `$group_code` optional param to `account_number_generator()`

Search for `function account_number_generator`. Add the third param (backward-compatible — existing callers are unaffected):

```php
// BEFORE:
function account_number_generator($id_scheme, $branch)

// AFTER:
function account_number_generator($id_scheme, $branch, $group_code = '')
```

---

### Step 5 — Add 7 New Methods to `payment_model.php`

**File:** `admin/application/models/payment_model.php`

Append all 7 methods before the final closing `}` of the `Payment_model` class. Add them in the order listed.

#### Method 1: `updateGatewayResponse()` — batch update payment rows by ref_trans_id

```php
function updateGatewayResponse($gatewayResponse, $transactionDetail)
{
    $rows = $transactionDetail['rows'] ?? [];
    if (empty($rows)) {
        return ['status' => false, 'payids' => []];
    }
    $includeIds = array_column($rows, 'id_payment');
    $refTransId = $rows[0]['ref_trans_id'] ?? null;
    if (!$refTransId) {
        return ['status' => false, 'message' => 'Missing ref_trans_id'];
    }
    $this->db->where('ref_trans_id', $refTransId);
    if (!empty($includeIds)) {
        $this->db->where_in('id_payment', $includeIds);
    }
    $status = $this->db->update('payment', $gatewayResponse);

    $this->db->select('id_payment, payu_id, payment_status');
    $this->db->from('payment');
    $this->db->where('ref_trans_id', $refTransId);
    if (!empty($includeIds)) {
        $this->db->where_in('id_payment', $includeIds);
    }
    $payids = $this->db->get()->result_array();
    return ['status' => $status, 'payids' => $payids];
}
```

#### Method 2: `getBranchGatewayData()` — fetch active gateway credentials for a branch

```php
function getBranchGatewayData($branch_id, $pg_code)
{
    $sql = "SELECT param_1, param_2, param_3, param_4, pg_code, api_url, id_pg
            FROM gateway
            WHERE is_default=1 AND active=1 AND pg_code=" . $pg_code
         . ($branch_id != '' ? " AND id_branch=" . $branch_id : '') . "";
    return $this->db->query($sql)->row_array();
}
```

#### Method 3: `getPendpayment_Data()` — pending payments for auto-verification (last 3 days)

```php
function getPendpayment_Data($previousDay, $currentDay, $id_branch, $id_pg)
{
    $sql = $this->db->query(
        "SELECT p.ref_trans_id as txn_ids, payment_status,
                p.payment_ref_number as order_id, p.payment_amount,
                p.pay_email as email, c.mobile
         FROM payment p
         LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
         LEFT JOIN customer c ON c.id_customer = sa.id_customer
         WHERE "
        . ($id_pg > 0    ? ' p.id_payGateway =' . $id_pg . ' AND ' : '')
        . ($id_branch > 0 ? ' p.id_branch =' . $id_branch . ' AND ' : '')
        . "p.date_payment >= '" . $previousDay . " 00:00:00'
         AND p.date_payment <= '" . $currentDay . " 23:59:59'
         AND p.date_payment <= DATE_SUB('" . date('Y-m-d H:i:s') . "', INTERVAL 2 MINUTE)
         AND (p.payment_status=3 OR p.payment_status=7 OR p.payment_status=4)
         ORDER BY p.id_payment DESC LIMIT 50"
    );
    return $sql->result_array();
}
```

#### Method 4: `getPayData()` — full payment row by ref_trans_id (Easebuzz manual verify)

```php
function getPayData($txnid)
{
    $sql = "SELECT sa.ref_no, flexible_sch_type, sa.id_customer, firstPayment_amt,
                   s.firstPayamt_as_payamt, s.firstPayamt_maxpayable, p.id_payment,
                   sa.id_scheme_account, sa.scheme_acc_number, sa.id_scheme,
                   cs.schemeacc_no_set, cs.receipt_no_set, cs.scheme_wise_receipt,
                   p.ref_trans_id, cs.edit_custom_entry_date, cs.custom_entry_date,
                   p.payment_amount, s.one_time_premium, sa.id_branch as branch,
                   cs.allow_referral, cs.gent_clientid, s.firstPayamt_maxpayable,
                   is_lucky_draw, c.mobile, p.pay_email as email,
                   sum(p.payment_amount) as payment_amount,
                   p.ref_trans_id as txn_ids, p.id_payGateway, g.pg_code
            FROM payment p
            LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
            LEFT JOIN customer c ON c.id_customer = sa.id_customer
            LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
            LEFT JOIN gateway g ON g.id_pg = p.id_payGateway
            JOIN chit_settings cs
            WHERE p.ref_trans_id='" . $txnid . "' GROUP BY p.ref_trans_id";
    return $this->db->query($sql)->result_array();
}
```

#### Method 5: `update_paymentMode()` — insert new payment mode if not already present

```php
function update_paymentMode($mode)
{
    $mode = strtolower(trim($mode));
    $this->db->from('payment_mode');
    $this->db->where('LOWER(short_code)', $mode);
    $query = $this->db->get();
    $short_code = strtoupper($mode);
    if ($query->num_rows() == 0 && !empty($mode)) {
        $formatted_mode_name = ucwords(str_replace('_', ' ', $mode));
        $data = [
            'mode_name'   => $formatted_mode_name,
            'short_code'  => $short_code,
            'status'      => 1,
            'sort_order'  => 0,
            'show_in_pay' => 0
        ];
        $this->db->insert('payment_mode', $data);
    }
    return $short_code;
}
```

#### Method 6: `getRazorOrderid()` — get Razorpay order ref from payment table

```php
function getRazorOrderid($txnid)
{
    $sql = $this->db->query(
        "SELECT payment_ref_number FROM payment
         WHERE payment_ref_number IS NOT NULL AND ref_trans_id='" . $txnid . "'
         GROUP BY payment_ref_number"
    );
    return $sql->row()->payment_ref_number;
}
```

#### Method 7: `getPayIds()` — full payment + account + scheme row for a set of id_payment values

```php
function getPayIds($id_payment)
{
    if (!is_array($id_payment)) {
        $id_payment = [$id_payment];
    }
    $id_payment = array_filter(array_map('trim', $id_payment));
    if (empty($id_payment)) return [];

    $ids = "'" . implode("','", $id_payment) . "'";
    $sql = "SELECT p.due_type, sa.id_scheme_account, p.id_payment,
                   (SELECT e.id_employee FROM employee e
                    WHERE e.emp_code = sa.referal_code
                    AND sa.referal_code != '' AND sa.referal_code IS NOT NULL) as ref_emp_id,
                   sa.id_customer, firstPayment_amt, s.code as group_code,
                   s.sync_scheme_code, sa.id_branch as branch, cs.scheme_wise_acc_no,
                   cs.gent_clientid, firstPayamt_as_payamt, s.firstPayamt_maxpayable,
                   sa.scheme_acc_number, sa.id_scheme, cs.schemeacc_no_set,
                   cs.receipt_no_set, cs.scheme_wise_receipt, p.ref_trans_id,
                   cs.edit_custom_entry_date, sa.custom_entry_date, p.payment_amount,
                   flexible_sch_type, p.id_branch, s.is_lucky_draw, s.max_members,
                   s.code, s.one_time_premium, p.id_transaction, p.offline_tran_uniqueid,
                   b.warehouse, p.payment_type, p.payment_mode, cs.allow_referral,
                   s.agent_refferal, s.agent_credit_type, s.emp_refferal, sa.referal_code,
                   sa.firstpayment_wgt, p.metal_weight,
                   IFNULL(p.gst_amount,0) as gst_amount,
                   IFNULL(p.discountAmt,0) as discountAmt,
                   IFNULL(p.actual_trans_amt,0) as actual_trans_amt,
                   sa.firstPayment_wgt, p.redeemed_amount, p.date_payment,
                   p.receipt_no, s.id_scheme, p.payment_status, p.receipt_year,
                   sa.ref_no, s.clientid_gen_code as sync_scheme_code
            FROM payment p
            LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
            LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
            LEFT JOIN branch b ON b.id_branch = p.id_branch
            JOIN chit_settings cs
            WHERE p.id_payment IN ($ids)";
    return $this->db->query($sql)->row_array();
}
```

---

### Step 6 — Add 2 New Methods to `services_model.php`

**File:** `admin/application/models/services_model.php`

Append both before the final closing `}` of the `Services_model` class.

#### Method 1: `checkService()` — check if SMS/Email is enabled for a service ID

```php
function checkService($serviceID)
{
    $email     = 0;
    $sms       = 0;
    $dlt_te_id = '';
    $query = $this->db->get_where('services', ['id_services' => $serviceID]);
    if ($query->num_rows() > 0) {
        $row       = $query->row();
        $email     = $row->serv_email;
        $sms       = $row->serv_sms;
        $dlt_te_id = $row->dlt_te_id;
    }
    return ['email' => $email, 'sms' => $sms, 'dlt_te_id' => $dlt_te_id];
}
```

#### Method 2: `get_SMS_data()` — build SMS message for a given service and record ID

This is a large method (~700 lines). Copy verbatim from the reference client:

```bash
# 1. Find the line number in the reference client
grep -n "function get_SMS_data" /var/www/html/srivallivilasjewellery.in/admin/application/models/services_model.php

# 2. Copy from that line to the closing } at the same indent level
# 3. Paste into the target client's services_model.php before the final }
```

---

### Step 7 — Copy the New Controller

```bash
cp /var/www/html/srivallivilasjewellery.in/admin/application/controllers/chit_transaction.php \
   /var/www/html/<TARGET_CLIENT>/admin/application/controllers/chit_transaction.php
```

After copying, open the file and verify these **client-specific config keys** inside `payment_update()`:

| Config Key | How to Check | Fix If Wrong |
|---|---|---|
| `$this->config->item('sms_gateway')` | Check target `admin/application/config/config.php` | Update key name to match |
| `$this->config->item('auto_pay_approval')` | Same | Update key name |
| `$this->config->item('integrationType')` | Same | Update key name |
| `insert_common_data()` exists | `grep -n "function insert_common_data" admin/application/controllers/chit_transaction.php` | Add stub if missing |
| `sendSMS_MSG91()` / `sendSMS_Nettyfish()` | Check `admin/application/models/sms_model.php` | Confirm methods exist |
| `generate_receipt_no()` | `grep -n "function generate_receipt_no" admin/application/controllers/chit_transaction.php` | Add stub if missing |
| `insert_referral_data()` | Same grep pattern | Add stub if missing |

---

### Step 8 — Register Webhook Routes

**File:** `admin/application/config/routes.php`

Add these 3 lines (before the closing of the routes file):

```php
$route['chit_transaction/payment_verify/(:num)/(:num)'] = 'chit_transaction/payment_verify/$1/$2';
$route['chit_transaction/easebuzz_webhook']             = 'chit_transaction/easebuzz_webhook';
$route['chit_transaction/manualVerify']                 = 'chit_transaction/manualVerify';
```

---

### Step 9 — Configure Webhook URLs in PG Dashboard

Log into each payment gateway dashboard and update the webhook/notify URL:

| Gateway | pg_code | Webhook URL |
|---|---|---|
| Cashfree | 4 | `https://<client-domain>/admin/index.php/chit_transaction/payment_verify/1/4` |
| Easebuzz | 8 | `https://<client-domain>/admin/index.php/chit_transaction/easebuzz_webhook` |
| Razorpay | 7 | `https://<client-domain>/admin/index.php/chit_transaction/payment_verify/1/7` |

---

## Verification (Run After All Steps Complete)

### 1. Webhook endpoint returns 200
```bash
curl -s -o /dev/null -w "%{http_code}" \
  -X POST https://<domain>/admin/index.php/chit_transaction/payment_verify/1/4 \
  -H "Content-Type: application/json" -d '{}'
# Expected: 200
```

### 2. Mobile API stub returns processing message
```bash
curl -s -X POST https://<domain>/index.php/mobile_api/cashfreeResponse_post \
  -H "Content-Type: application/json" -d '{"test":"1"}'
# Expected: {"status":true,"title":"PAYMENT PROCESS","msg":"Your payment is currently being processed..."}
```

### 3. Log directory created on first webhook hit
```bash
# After triggering one test webhook:
ls /var/www/html/<TARGET_CLIENT>/log/
# Expected: directory named today's date with Cashfree/ or Easebuzz/ subfolder inside
```

### 4. Payment status updated in DB
```sql
-- Trigger a sandbox payment, then check:
SELECT ref_trans_id, payment_status FROM payment WHERE payment_status IN (1,4) ORDER BY id_payment DESC LIMIT 5;
-- Expected: status changed from 3 → 1 (success) or 4 (failed/cancelled)
```

### 5. Manual verification works
```bash
# POST with a known pending txn_id from the DB:
curl -X POST https://<domain>/admin/index.php/chit_transaction/manualVerify \
  -d "pg_code=4&id_branch=1&txn_ids[]=<ref_trans_id>"
# Expected: "Total Records: 1 | Verified: 1"
```

### 6. Auto-verify cron works
```bash
# Call for Cashfree, branch 1:
curl https://<domain>/admin/index.php/chit_transaction/payment_verify/2/4/1
# Expected: output listing txn_ids processed + verified count
```

---

## Notes

- **Easebuzz sends URL-encoded POST, NOT JSON** — the controller uses `parse_str()` to decode it. Do not change this.
- **Cashfree payment IDs** are in `order_tags` object → extracted with `array_values((array)$order_tags)`.
- **Razorpay payment IDs** are in `notes->id_payments` as comma-separated string → `explode(',', ...)`.
- **Easebuzz payment IDs** are in `udf7` — single `"24673"` or comma-separated `"24673,24674"` — both handled.
- `payment_status` codes: `1=approved`, `3=pending`, `4=cancelled/failed`, `7=gateway-failed`. If client uses different codes adjust the WHERE clause in `getPendpayment_Data()`.
- Private helpers `mapPaymentMode()`, `mapPaymentStatus()`, `curlRequest()`, `curlPostRequest()` must all be inside `chit_transaction.php` after the copy — verify with `grep -n "function mapPaymentMode\|function curlRequest" admin/application/controllers/chit_transaction.php`.
- **Do NOT re-enable old `cashfreeResponse_post()`.** It will cause double-processing on webhook hits.
- **Related recipe**: PAT-PAY-002 — concurrent payment race condition guard. Apply that recipe after this one to prevent over-payment during webhook processing.
