# Component 7 — Payment CRM: Forensic Investigation Template

> **Module:** Payment CRM  
> **Generated:** 2026-02-18  
> **Updated:** 2026-02-18 (Verified table names)
> **Purpose:** Layer-by-layer investigation template for diagnosing payment bugs

---

## How to Use This Template

1. **Start at Layer 1** (Symptom). Record exactly what the user reports.
2. **Proceed layer by layer**. Each layer narrows the scope.
3. **Never skip layers** — most misdiagnoses happen when jumping to conclusions.
4. **Use the SQL queries** from `DB_TRUTH_PROTOCOL.sql` at Layer 5.
5. **Record findings** in the Bug Ticket section at the bottom.

---

## Layer 1: Symptom Collection

```
┌─────────────────────────────────────────────────┐
│ SYMPTOM REPORT                                   │
│                                                   │
│ Reporter: ________________                        │
│ Date: ________________                            │
│ Environment: ________________                     │
│                                                   │
│ What happened? _________________________________  │
│ What was expected? _____________________________  │
│ When did it start? _____________________________  │
│ How often does it occur? [ ] Always [ ] Sometimes │
│ Affected payment ID(s): ________________________  │
│ Affected scheme account(s): ____________________  │
│ Screenshot/Video? [ ] Yes → Attach  [ ] No        │
│                                                   │
│ Symptom Category:                                 │
│ [ ] Wrong amount saved                            │
│ [ ] Wrong weight calculated                       │
│ [ ] Wrong installment count                       │
│ [ ] Wrong receipt number                          │
│ [ ] Payment saved but not reflected               │
│ [ ] Payment mode mismatch                         │
│ [ ] GST calculation error                         │
│ [ ] Discount not applied                          │
│ [ ] Wallet not deducted / over-deducted           │
│ [ ] Advance not utilized / over-utilized          │
│ [ ] DigiGold benefit error                        │
│ [ ] Branch assignment error                       │
│ [ ] Notification not sent                         │
│ [ ] UI/Form error                                 │
│ [ ] Other: ___________________________________    │
└─────────────────────────────────────────────────┘
```

---

## Layer 2: Reproduce & Isolate

### 2.1 Reproduction Checklist
| Step | Action | Observed | Expected | Match? |
|------|--------|----------|----------|--------|
| 1 | Select customer by mobile | | | |
| 2 | Select scheme account | | | |
| 3 | Verify loaded data (rate, dues, type) | | | |
| 4 | Select installment count | | | |
| 5 | Verify calculated amount | | | |
| 6 | Select payment mode(s) | | | |
| 7 | Enter mode detail amounts | | | |
| 8 | Click Save | | | |
| 9 | Check response | | | |
| 10 | Verify DB record | | | |

### 2.2 Isolation Questions
| Question | Answer | Narrows To |
|----------|--------|------------|
| Does it happen for ALL scheme types? | | Scheme type variant |
| Does it happen for ALL payment modes? | | Payment mode variant |
| Does it happen for single AND multi-installment? | | Installment logic |
| Does it happen for ALL branches? | | Branch resolution |
| Does it happen with GST ON and OFF? | | GST calculation |
| Does it happen with discount ON and OFF? | | Discount logic |
| Does it happen with wallet ON and OFF? | | Wallet logic |
| Did it work before a specific date? | | Git blame target |

---

## Layer 3: Client-Side (JS) Trace

### 3.1 Console Log Points
Open Browser DevTools → Console. These variables reveal the JS state:

```javascript
// In browser console before clicking save:
console.log({
  scheme_type: $('#scheme_type').text(),
  sch_type: $('#sch_type').val(),
  fix_weight: $('#fix_weight').val(),
  flexible_sch_type: $('#flexible_sch_type').val(),
  sel_due: $('#sel_due').val(),
  total_amt: $('#total_amt').val(),
  payment_amt: $('#payment_amt').val(),
  gst_type: $('#gst_type').val(),
  gst_percent: $('#gst_percent').val(),
  gst_amt: $('#gst_amt').val(),
  metal_rate: $('#metal_rate').val(),
  discount: $('#discount').val(),
  discountedAmt: $('#discountedAmt').val(),
  due_type: $('#due_type').val(),
  wallet_balance: $('.wallet_balance').val(),
  redeem_request: $('.redeem_request').val(),
  sum_of_amt: $('.sum_of_amt').html(),
  bal_amount: $('.bal_amount').html()
});
```

### 3.2 Network Tab Check
After clicking save, check:
- **Request URL**: Should be `payment/save_all` or `admin_payment/payment/general_advance`
- **Request Method**: POST
- **Form Data**: Expand `generic[*]` and `cus_pay_mode[*]`
- **Response**: Check `payment_status` and any `error` field

### 3.3 JS Files to Investigate
| Symptom | File | Function | Line Range |
|---------|------|----------|------------|
| Wrong total amount | `payment.js` | `$('#sel_due').change()` | 2900-2978 |
| Wrong GST display | `payment.js` | GST calc in change handler | 2958-2969 |
| Wrong weight display | `payment.js` | `weight_gold` change handler | 2980-3030 |
| Validation error | `payment.js` | checkbox change handler | 3111-3266 |
| Save not executing | `payment.js` | `insert_payment()` | 3271-3298 |
| AJAX error | `payment.js` | `payment_success()` | 3318-3382 |
| Wrong mode total | `payment.js` | `calculatePaymentCost()` | (search) |
| Wrong wallet calc | `payment.js` | `calc_walletbalance()` | (search) |

---

## Layer 4: Server-Side (PHP) Trace

### 4.1 Log File Check
```
File: application/logs/manual/create_payment_{YYYY-MM-DD}.txt
Contains: Full $_POST dump for every save attempt
```

### 4.2 Controller Trace Points
| Symptom | File | Method | Line Range | What to Check |
|---------|------|--------|------------|---------------|
| Wrong installment split | `admin_payment.php` | `payment('SaveAll')` | 1319-1325 | `$amount` and `$payment_amount` values |
| Wrong mode detection | `admin_payment.php` | `payment('SaveAll')` | 1350-1402 | `sizeof()` checks |
| Wrong GST adjust | `admin_payment.php` | `payment('SaveAll')` | 1327-1333 | `$payment_amount` after GST |
| Wrong weight | `admin_payment.php` | `payment('SaveAll')` | 1468-1505 | `$metal_wgt` calculation |
| Wrong due type | `admin_payment.php` | `payment('SaveAll')` | 1538-1544 | `$dueType` resolution |
| Wrong branch | `admin_payment.php` | `payment('SaveAll')` | 1546-1567 | 5-level cascade |
| Wrong receipt | `admin_payment.php` | `generate_receipt_no()` | 84-113 | Receipt format |
| Wrong DigiGold | `admin_payment.php` | `payment('SaveAll')` | 1584-1622 | Benefit calculation |
| Wrong wallet | `admin_payment.php` | `payment('SaveAll')` | 1446-1461 | `$redeemed_amount` |
| Wrong discount | `admin_payment.php` | `payment('SaveAll')` | 1679-1681 | Discount iteration match |
| Insert failure | `admin_payment.php` | `payment('SaveAll')` | 1683 | `$status` return value |
| Transaction failure | `admin_payment.php` | `payment('SaveAll')` | ~2050-2100 | `trans_status()` |

### 4.3 Model Trace Points
| Symptom | File | Method | Line Range | What to Check |
|---------|------|--------|------------|---------------|
| Wrong account data loaded | `payment_model.php` | `get_paymentContent()` | 1400-2443 | Query JOINs, WHERE |
| Insert returns null | `payment_model.php` | `paymentDB('insert')` | 732-840 | `$this->db->insert()` |
| Wrong installment count | `payment_model.php` | `get_curPaid_insNo()` | (search) | COUNT query |
| Wrong wallet balance | `payment_model.php` | `wallet_balance()` | (search) | SUM query |

---

## Layer 5: Database Verification

### 5.1 Payment Record Check
```sql
-- Replace {ID} with the payment ID from the symptom
SELECT * FROM payment WHERE id_payment = {ID};
SELECT * FROM payment_mode_details WHERE id_payment = {ID};
SELECT * FROM payment_status_message WHERE id_payment = {ID};
```

### 5.2 Account State Check
```sql
-- Replace {SA_ID} with the scheme account ID
SELECT * FROM scheme_account WHERE id_scheme_account = {SA_ID};
SELECT * FROM scheme WHERE id_scheme = (
    SELECT id_scheme FROM scheme_account WHERE id_scheme_account = {SA_ID}
);
SELECT COUNT(*), SUM(payment_amount), SUM(metal_weight)
FROM payment 
WHERE id_scheme_account = {SA_ID} 
  AND payment_status = 1 AND is_deleted = 0;
```

### 5.3 Financial Trail Check
```sql
-- Wallet
SELECT * FROM wallet_transaction WHERE id_payment = {ID};
-- Advance
SELECT * FROM ret_advance_utilized WHERE id_payment = {ID};
-- Voucher
SELECT * FROM voucher_utilized WHERE id_payment = {ID};
```

---

## Layer 6: Root Cause Classification

| Category | Sub-Category | Fix Location | Risk |
|----------|-------------|--------------|------|
| **System-Level** | NULL/undefined input | Controller input validation | Low |
| **System-Level** | Division by zero | Controller guard | Low |
| **System-Level** | Transaction not atomic | Controller trans_begin/commit | High |
| **System-Level** | Race condition (dupe) | Model lock/unique constraint | High |
| **Architecture-Level** | JS/PHP formula mismatch | Both JS + PHP | High |
| **Architecture-Level** | Missing server validation | Controller | Medium |
| **Architecture-Level** | Orphaned child records | Model cleanup | Medium |
| **Business-Level** | Wrong GST calculation | Controller + JS | Critical |
| **Business-Level** | Wrong installment count | Model query + Controller | Critical |
| **Business-Level** | Wrong weight formula | Controller | Critical |
| **Business-Level** | Wrong discount application | Controller loop | High |
| **Business-Level** | Wrong branch assignment | Controller cascade | Medium |

---

## Bug Ticket Template

```markdown
## BUG-PAY-{NNN}: {Title}

**Severity:** P0 / P1 / P2 / P3
**Type:** System / Architecture / Business
**Reported:** {date}
**Reporter:** {name}

### Symptom
{What the user sees}

### Reproduction Steps
1. Step 1
2. Step 2
3. Step 3

### Expected vs Actual
| | Expected | Actual |
|---|----------|--------|
| Amount | | |
| Weight | | |
| GST | | |
| Mode | | |

### Root Cause
**Layer:** {1-6}
**File:** {filename}:{line}
**Function:** {function_name}
**Description:** {Why it happens}

### Fix
**Approach:** {What to change}
**Files Changed:**
- `{file1}:{lines}` — {what changed}
- `{file2}:{lines}` — {what changed}

### Verification SQL
```sql
{Query to verify the fix worked}
```

### Regression Risk
{What else might break}
```
