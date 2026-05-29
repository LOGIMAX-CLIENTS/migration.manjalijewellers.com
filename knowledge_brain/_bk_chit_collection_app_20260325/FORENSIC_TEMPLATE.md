# chit_collection_app — Forensic Investigation Template

> **Brain Built:** 2026-03-17 | **Round:** 1  
> Use this template when investigating bugs in this module. Work layer by layer.

---

## 🔍 Investigation Protocol

### Step 1: Identify REST Endpoint

Is the bug in:
- [ ] **Collection App REST** (adminapp_api.php) — `_post()` or `_get()` suffix methods
- [ ] **Customer Web Payment** (paymt.php) — `paySubmit`, gateway callback handlers
- [ ] **Both** — shared logic in payment_modal, mobileapi_model

### Step 2: Reproduce the Symptom

```bash
# Log the raw POST to the API (add this to adminapp_api::mobile_payment_post):
error_log(json_encode($this->get_values()), 3, '/tmp/collection_debug.log');

# For paymt.php paySubmit — dump session + POST:
error_log(json_encode(array_merge($_POST, ['session' => $_SESSION])), 3, '/tmp/paymt_debug.log');
```

### Step 3: DB Snapshot Before Fix

```sql
-- Snapshot the affected payment rows
SELECT p.id_payment, p.id_scheme_account, p.payment_status, p.payment_amount,
       p.id_transaction, p.due_type, p.due_month, p.due_year, p.payment_mode,
       p.added_by, p.id_employee, p.metal_weight, p.metal_rate, p.gst_amount,
       p.receipt_no, p.date_payment, sa.scheme_acc_number
FROM payment p
LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
WHERE p.id_scheme_account = {ID} ORDER BY p.id_payment DESC LIMIT 20;
```

---

## Layer-by-Layer Investigation

### Layer A: Controller Input

**adminapp_api.php:** `get_values()` reads the raw JSON body:
```php
// Line 59-63
function get_values(){
    return json_decode(file_get_contents('php://input'),true);
}
```
Verify: Is `pay_arr` URL-encoded? It's decoded with `urldecode()` at L1435.

**paymt.php:** Uses `$this->input->post()`.

### Layer B: Model Method Entry

**Critical model methods to check first:**

| Symptom | Start Here |
|---|---|
| Wrong due count / allow_pay wrong | `adminappapi_model::get_payment_details()` L440 |
| GST wrong | `mobile_payment_post()` L1486-1499 — check `$sch_data` vs `$chit` |
| Payment inserted with wrong status | `mobile_payment_post()` L1579 — check gateway==0 + added_through logic |
| Account number not generated | `generateAcNoOrReceiptNo()` → `account_number_generator()` in payment_modal |
| Receipt number not generated | `generateAcNoOrReceiptNo()` → `generate_receipt_no()` in payment_modal |
| SMS not sent | `services_modal::checkService(7)` → check service config |
| Login fails for new device | `isValidLogin()` L67-96 — check device_status |
| Customer not found | `get_customerByMobile()` — check allocated_employee/allocated_agent |

### Layer C: DB State Verification

```sql
-- 1. PAYMENT STATUS CHECK
SELECT id_payment, payment_status, payment_amount, metal_weight, gst_amount,
       due_type, due_month, due_year, receipt_no, added_by, id_employee,
       payment_mode, id_branch, date_payment
FROM payment WHERE id_scheme_account = {SA_ID} ORDER BY id_payment DESC LIMIT 10;

-- 2. SCHEME ACCOUNT STATE
SELECT sa.id_scheme_account, sa.scheme_acc_number, sa.paid_installments,
       sa.active, sa.is_closed, sa.id_student, sa.is_opening, sa.avg_payable,
       s.scheme_name, s.scheme_type, s.total_installments, s.allow_advance,
       s.allow_unpaid, s.flexible_sch_type
FROM scheme_account sa
JOIN scheme s ON s.id_scheme = sa.id_scheme
WHERE sa.id_scheme_account = {SA_ID};

-- 3. DEVICE AUTH STATE
SELECT ed.*, e.username, e.enable_chit_collection
FROM employee_devices ed
LEFT JOIN employee e ON e.id_employee = ed.emp_id
WHERE ed.emp_id = {EMP_ID};

-- 4. CURRENT MONTH PAYMENT COUNT
SELECT COUNT(*) as current_month_count, SUM(payment_amount) as current_month_total
FROM payment
WHERE id_scheme_account = {SA_ID}
AND payment_status = 1
AND YEAR(date_payment) = YEAR(NOW())
AND MONTH(date_payment) = MONTH(NOW());

-- 5. WALLET STATE (if wallet redemption involved)
SELECT available_points, available_amount FROM inter_wallet_account WHERE mobile = '{MOBILE}';

-- 6. PENDING PAYMENTS (gateway callbacks not yet received)
SELECT id_payment, id_transaction, date_payment, payment_amount, payment_status
FROM payment WHERE payment_status = 7 AND id_scheme_account = {SA_ID};

-- 7. METAL RATE AT TIME OF PAYMENT
SELECT goldrate_22ct, silverrate_1gm, modified_date FROM metal_rates ORDER BY id_metal_rates DESC LIMIT 3;
```

### Layer D: Common Gotchas

| Gotcha | What to Check |
|---|---|
| `$sch_data['gst_type']` undefined | In `mobile_payment_post()` L1489 — variable should be `$chit['gst_type']` |
| `trans_begin` without `trans_complete` | `createCustomer_post()` has nested transaction — outer begin at L251, inner at L515 |
| `base64` treated as encrypted password | `__encrypt()` returns `base64_encode($str)` — NOT safe as encryption |
| Static OTP 123456 | `generateOTP_get()` L1243 — hardcoded for demo |
| `$service['serv_whatsapp']` undefined | In `generateOTP_get()` L1272 — `$service` never set in this function |
| `allowed_dues` forced to 1 for flexible_wgt | `get_payment_details()` L1276 — overrides calculated value |
| Employee vs Agent login divergence | `isValidLogin()` handles both — check `$data['login_type']` |
| `employee_devices.app_type=1` | Must be 1 for collection app; 2 for customer app |
| Receipt not generated for cash payments | `generateAcNoOrReceiptNo()` only called after gateway callback for online; called directly for offline |
| Branch NULL in payment | See Bug PAY-CLT from conversation f41c3164 — `id_branch` must be passed correctly |

### Layer E: Transaction Integrity

For any payment insertion issue:
1. Check `db->trans_status()` — returns FALSE if any query failed
2. Check `db->last_query()` to see actual SQL executed
3. Check MySQL error log for constraint violations
4. Verify `scheme_account.is_closed == 0` (closed accounts block payments)
5. Verify `allow_pay == 'Y'` before payment insertion

---

## Payment Status Code Reference

| Code | Label | Set By |
|---|---|---|
| 7 | Pending | Initial insert (online payment) |
| 1 | Success | Gateway callback (approved) or immediate cash |
| 2 | Awaiting Approval | Cash by customer app OR non-auto-approval gateway |
| 3 | Failed | Gateway failure callback |
| 4 | Cancelled | Gateway cancel callback |
| 5 | Returned | Admin-set |
| 6 | Refund | Admin-set |

---

## Quick Fix Targets

| Bug Pattern | File:Line | Fix |
|---|---|---|
| `$sch_data['gst_type']` → undefined | adminapp_api.php L1489 | Change to `$chit['gst_type']` |
| OTP always 123456 | adminapp_api.php L1243 | Remove/comment out the hardcode |
| `$service` undefined in generateOTP | adminapp_api.php L1272 | Fetch `$service` before use |
| base64 used as encryption | adminappapi_model.php | Replace with proper hashing |
| No API auth on endpoints | adminapp_api.php (all routes) | Add token validation middleware |
