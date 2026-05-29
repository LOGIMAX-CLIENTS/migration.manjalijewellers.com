# Account Module — Forensic Template
> **Round**: 1 | **Date**: 2026-03-06

---

## Layer 1: User-Reported Symptom

| Question | Notes |
|---|---|
| What operation was being performed? | (Add / Edit / Close / Revert / Delete / Gift Issue / Sync / Print) |
| What error message/behavior was observed? | |
| What scheme type? | (0=Amount, 1=Weight, 2=AmtToWgt, 3=Flexible → sub-type?) |
| Branch? | (branch_settings=1 or 0?) |
| Was it a DigiGold account? | (is_digi=1?) |
| Was referral code involved? | (is_refferal_by: 0=customer, 1=employee, NULL=none) |
| Was this pre-close or full maturity? | |
| Is the account active or closed? | (active, is_closed) |

---

## Layer 2: Session & Configuration State

```
CHECK SESSION:
  - uid (employee performing action)
  - id_branch (login branch)
  - branchWiseLogin, is_branchwise_cus_reg
  - mob_no_len (mobile validation)
  - metal_wgt_roundoff, metal_wgt_decimal
  - OTP-related session vars

CHECK SETTINGS:
  - scheme_wise_acc_no (0-6)
  - branch_settings (0/1)
  - cusName_edit (0/1)
  - free_payment / approvalReqForFP
  - limit_sch_acc / sch_acc_max_count
  - receipt_no_set (0/1)

SQL:
  SELECT * FROM chit_settings;
  SELECT * FROM ret_financial_year WHERE fin_status = 1;
```

---

## Layer 3: Database State

```sql
-- Account state
SELECT sa.*, s.scheme_type, s.total_installments, s.amount, s.code,
       c.firstname, c.mobile, c.id_customer
FROM scheme_account sa
JOIN scheme s ON s.id_scheme = sa.id_scheme
JOIN customer c ON c.id_customer = sa.id_customer
WHERE sa.id_scheme_account = {ID};

-- Payment history
SELECT * FROM payment WHERE id_scheme_account = {ID} ORDER BY date_payment;

-- Gift history
SELECT * FROM gift_issued WHERE id_scheme_account = {ID};

-- Wallet transactions for this account
SELECT * FROM wallet_transaction WHERE id_sch_ac = {ID};

-- OTP records
SELECT * FROM otp ORDER BY otp_gen_time DESC LIMIT 5;

-- Referral details
SELECT sa.referal_code, sa.is_refferal_by, c.mobile, c.firstname
FROM scheme_account sa
JOIN customer c ON c.id_customer = sa.id_customer
WHERE sa.id_scheme_account = {ID};
```

---

## Layer 4: Transaction Integrity

```
CHECKLIST:
  ☐ Was trans_begin() called before the operation?
  ☐ Does all DB work happen BETWEEN trans_begin and trans_commit/rollback?
  ☐ Is the image upload AFTER trans_commit or BEFORE?
     - Close flow: Image upload happens BEFORE trans_status check (L1806-1808)
     - This means failed transactions may have orphaned images
  ☐ Does commit happen inside or outside loop?
     - manual_schemeaccount(): trans_commit INSIDE foreach → BUG
  ☐ Are all related tables updated atomically?
     - Close: account + wallet_transaction + agent + log
     - Add: account + kyc + gifts + payment + wallet
```

---

## Layer 5: Specific Bug Investigations

### BUG: Account Number Duplication
```
SYMPTOMS: Two accounts get same scheme_acc_number
ROOT CAUSE: LOCK TABLES is commented out in account_model.php L314
  → Concurrent requests can both get MAX() and increment

VERIFY:
  SELECT scheme_acc_number, COUNT(*) as cnt
  FROM scheme_account
  WHERE scheme_acc_number IS NOT NULL
  GROUP BY scheme_acc_number, id_scheme
  HAVING cnt > 1;

FIX: Re-enable table locking OR use SELECT ... FOR UPDATE
```

### BUG: Gift OTP Always Passes
```
SYMPTOMS: Any OTP value accepted for gift verification
ROOT CAUSE: Line 4188 uses = (assignment) instead of == (comparison)
  else if ($otp = $this->session->userdata('OTP'))  ← ALWAYS TRUE

VERIFY: Test with wrong OTP value → still returns result=1

FIX: Change to $otp == $this->session->userdata('OTP')
```

### BUG: trans_commit Inside Loop
```
SYMPTOMS: Partial account number updates, inconsistent state
ROOT CAUSE: manual_schemeaccount() L2636-2646
  - trans_begin() is outside the loop
  - trans_commit() is inside the loop
  - First successful iteration commits, subsequent failures can't rollback

VERIFY:
  SELECT id_scheme_account, scheme_acc_number, date_upd
  FROM scheme_account WHERE date_upd >= '2026-01-01'
  ORDER BY date_upd DESC;

FIX: Move trans_commit/rollback outside the loop
```

### BUG: Undefined $duration in generate_giftotp
```
SYMPTOMS: OTP_expiry set to current time (0 duration)
ROOT CAUSE: L3218 uses $duration but it's never assigned

VERIFY: Check session gift OTP expiry → always expired

FIX: Assign $duration from settings or default value
```

### BUG: Hardcoded Date in GA Bonus
```
SYMPTOMS: GA bonus calculated incorrectly after Oct 2024
ROOT CAUSE: Controller L1433 has hardcoded '2024-10-07'

VERIFY:
  SELECT * FROM scheme_general_advance_benefit_settings;
  -- Check if date filter is correct

FIX: Replace with configurable date or remove date filter
```

---

## Layer 6: Benefit/Deduction Debugging

```
CHECKLIST FOR CLOSING CALCULATION ERRORS:

1. Determine calculation path:
   ☐ is_digi == 1? → calculate_by = 1 (DigiGold path)
   ☐ calculation_type == 2 && maturity_days > 0? → calculate_by = 2
   ☐ Default → calculate_by = 0 (common path)

2. Check interest chart:
   SELECT * FROM scheme_interest_chart WHERE id_scheme = {SCHEME_ID};

3. Check pre-close eligibility:
   ☐ allow_preclose == 1?
   ☐ preclose_months >= 1?
   ☐ preclose_benefits == 1?
   ☐ preclose_type: 1=installment, 2=days

4. Verify closing balance:
   closing_balance = paid_amount - tax_amount - debit_amount - bank_charges
                   - add_charges + interest + GA_bonus + DG_benefit

5. Metal rate issues:
   SELECT * FROM metal_rate
   WHERE metal_purity = {PURITY} AND id_branch = {BRANCH}
   ORDER BY date_rate DESC LIMIT 1;
```

---

## Layer 7: Sync Debugging

```
CHECKLIST FOR SYNC FAILURES:

1. Check sync source:
   ☐ update_client() → syncapi_model (operational sync)
   ☐ update_client_jil() → chitapi_model (JIL sync)
   ☐ syncInterData() → sktm_syncapi_model (SKTM group)

2. Check intermediate table:
   SELECT * FROM customer_reg
   WHERE is_transferred = 'N' AND record_to = 2
   ORDER BY date_add DESC;

3. Check sync log:
   SELECT * FROM sync_log ORDER BY sync_date DESC LIMIT 10;

4. Common failures:
   ☐ id_scheme_account is NULL (orphaned payment)
   ☐ Client ID already exists → skips update
   ☐ Database connection switch (default ↔ common_db)
   ☐ Unreachable code after exit (L2246)

5. Verify database switch:
   ☐ $this->load->database('default', true) called at end?
```

---

## Layer 8: OTP Debugging

```
CHECKLIST FOR OTP FAILURES:

1. Identify OTP type:
   ☐ Closing OTP: session 'pay_OTP', expiry 'pay_OTP_expiry'
   ☐ Gift OTP: session 'OTP', expiry 'gift_OTP_expiry'
   ☐ Scheme Join: session 'OTP_scheme_join', expiry 'sche_join_otp_expiry'
   ☐ Rate Fixing: session 'OTP', expiry 'rate_fixing_otp_exp'

2. Common issues:
   ☐ Session expired between generate and verify
   ☐ Mobile length validation fails (mob_no_len mismatch)
   ☐ SMS gateway down (no fallback)
   ☐ DLT template ID mismatch
   ☐ $params undefined for WhatsApp

3. CRITICAL BUG:
   ☐ verifyotp_gift() at L4188: Uses = instead of ==
   ☐ All 5 OTP endpoints return OTP in JSON response (security)

4. Verify DB:
   SELECT * FROM otp ORDER BY otp_gen_time DESC LIMIT 5;
```

---

## Layer 9: Print/PDF Debugging

```
CHECKLIST FOR PRINT FAILURES:

1. Check DOMPDF setup:
   ☐ dompdf helper loaded? → load->helper(array('dompdf', 'file'))
   ☐ DOMPDF class instantiated correctly?

2. Check paper size:
   ☐ Passbook back: array(0, 0, 529.13, 1035.6)
   ☐ Passbook front: a5 (one_time_premium) or custom (0, 0, 690, 350)
   ☐ Passbook close: array(0, 0, 529.13, 1020.5)
   ☐ Bond: A4 portrait
   ☐ History: A4 portrait
   ☐ Benefit report: A4 portrait
   ☐ QR receipt: custom (0, 0, 125, 60)

3. "portriat" typo: Multiple locations spell "portrait" as "portriat"
   - DOMPDF may still work but is technically incorrect

4. Check QR/Barcode:
   ☐ phpqrcode library loaded?
   ☐ File write permission on FCPATH . 'assets/img/account/'?
   ☐ QR file already exists? (uses caching)

5. Multi-payment print (base64):
   ☐ base64_decode of payment IDs
   ☐ SRINIDHI vs non-SRINIDHI classification routing
```

---

## Quick Reference Bug Lookup

| Symptom | Likely Cause | Location | Fix Complexity |
|---|---|---|---|
| Duplicate account numbers | Race condition (no locking) | Model L314 | Medium |
| Gift OTP always accepted | `=` vs `==` bug | Controller L4188 | Simple |
| Partial account updates | trans_commit in loop | Controller L2641 | Simple |
| Negative closing balance | No validation | Controller L1575+ | Medium |
| SMS not sent | Gateway down, no fallback | Controller L1944+ | Medium |
| WhatsApp error | `$params` undefined | Controller L2538/2569/2600 | Simple |
| OTP expired immediately | `$duration` undefined | Controller L3218 | Simple |
| GA bonus wrong | Hardcoded date filter | Controller L1433 | Simple |
| Password visible in SMS | Plaintext in login_sms() | Controller L1962 | Medium |
| Sync errors | exit before error tracking | Controller L2246 | Simple |
| Session timeout during close | Long calculation time | Controller L1200-1600 | Medium |
| Wrong benefit calculation | calculate_by path mismatch | Controller L1300-1350 | Complex |
