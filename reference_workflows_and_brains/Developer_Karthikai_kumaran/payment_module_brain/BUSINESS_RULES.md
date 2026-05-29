# Component 3 — Payment CRM: Canonical Business Rules

> **Module:** Payment CRM  
> **Generated:** 2026-02-18  
> **Rules Extracted From:** Controller (SaveAll), Model (get_paymentContent), JS (payment.js)

---

## Rule Category 1: Installment Calculation

### BR-1.1: Per-Installment Amount
**Formula (PHP, line 1320-1324):**
```
IF installments > 1:
  amount = (payment_amount + discountedAmt) / installments
  payment_amount_per = payment_amount / installments
ELSE:
  amount = payment_amount + discountedAmt
  payment_amount_per = payment_amount
```
**JS equivalent:** Yes — `$('#sel_due').val()` × `payable_amt`

### BR-1.2: Installment Count Validation (Server-Side)
**Rule (PHP, lines 1304-1318):**
```
IF installments <= 0 → force to 1
IF installments > allowed_dues → REJECT
IF (paid_installments + installments) > total_installments → REJECT
```
**JS equivalent:** Client enforces via #sel_due min/max bounds

### BR-1.3: Due Type Resolution
**Rule (PHP, lines 1538-1544):**
```
IF due_type = 'PN' (Pending + Normal):
  Installment 1 → 'ND' (Normal Due)
  Installment 2+ → 'PD' (Pending Due)
IF due_type = 'AN' (Advance + Normal):
  Installment 1 → 'ND' (Normal Due)
  Installment 2+ → 'AD' (Advance Due)
ELSE → as posted
```

### BR-1.4: Allowed Dues Calculation
**Rule (Model, get_paymentContent):**
```
allowed_dues = total_installments - paid_installments
IF scheme allows pending: allowed_dues = unpaid_dues + advance_allowance
```
**⚠ JS/PHP difference: JS allows user to increment/decrement within bounds**

---

## Rule Category 2: GST Calculation

### BR-2.1: GST Amount
**Formula (PHP, lines 1475-1481):**
```
IF gst_type = 1 (Exclusive):
  gst_amt = actual_pay × (gst_percent / 100)
  // Customer pays amount + GST on top
IF gst_type = 0 (Inclusive):
  gst_amt = actual_pay - (actual_pay × (100 / (100 + gst_percent)))
  // GST is already inside the paid amount
```

### BR-2.2: GST-Adjusted Payment Amount
**Rule (PHP, lines 1327-1333):**
```
IF sch_type ≠ 1 (not pure weight):
  IF sch_type = 3 AND flexible_sch_type ≠ 8:
    IF gst_type = 0 (inclusive):
      payment_amount = payment_amount - gst_amount
```
**⚠ This means for inclusive GST flexible schemes, payment_amount is NET of GST**

### BR-2.3: GST in JS
**Formula (JS, lines 2961-2968):**
```
gst_val = amount - (amount × (100 / (100 + gst_percent)))
gst_amt = gst_val × allowed_dues
IF gst_type = 1: gst = gst_amt  // Only exclusive adds to display
```
**⚠ JS/PHP Discrepancy: JS formula is always inclusive-style, then conditionally used**

---

## Rule Category 3: Metal Weight Calculation

### BR-3.1: Weight for Fix-Weight Schemes (fix_weight=2)
**Formula (PHP, lines 1484-1491):**
```
amt = (gst_type == 0) ? (sch_amt - gst_amt) : (sch_amt - gst_amt)
metal_weight = amt / metal_rate
```
**Note: Both branches do the same thing! Likely copy-paste bug — should be `sch_amt` for inclusive**

### BR-3.2: Weight for Flexible Schemes (fix_weight=3)
**Formula (PHP, lines 1492-1502):**
```
amt = (gst_type == 0) ? (pay_amt - gst_amt) : (pay_amt - gst_amt)
IF flexible_sch_type IN [3, 4, 7, 8]
   OR (flexible_sch_type = 2 AND wgt_convert ≠ 2)
   OR (flexible_sch_type = 5 AND wgt_store_as = 1):
  metal_weight = amt / metal_rate
```
**⚠ Same issue: Both GST branches subtract gst_amt**

### BR-3.3: Weight for DigiGold Schemes
**Formula (PHP, lines 1586-1588):**
```
metal_weight = payment_amount / metal_rate
(Uses trim_decimal or number_format based on settings)
```

### BR-3.4: Amount-to-Weight Conversion
**Formula (PHP, line 2491-2495):**
```
function amount_to_weight($to_pay):
  return trim_decimal($to_pay['sch_amt'] / $to_pay['metal_rate'], metal_wgt_decimal)
  // OR number_format based on metal_wgt_roundoff setting
```

---

## Rule Category 4: Payment Mode Determination

### BR-4.1: Single Mode Detection
**Rule (PHP, lines 1350-1402):**
```
CSH:       cash > 0 AND all others = 0
CC/DC:     card(s) > 0 AND all others = 0
CHQ:       cheque(s) > 0 AND all others = 0
NB:        netbanking > 0 AND all others = 0
VCH:       voucher(s) > 0 AND all others = 0
REF_WALLET: wallet > 0 AND all others = 0
ADV_ADJ:   advance > 0 AND all others = 0
MULTI:     ANY combination of 2+ modes
```

### BR-4.2: Card Type Resolution
**Rule (PHP, lines 1353-1360):**
```
IF all cards same type: mode = CC or DC
IF mixed card types: mode = MULTI
```

---

## Rule Category 5: Wallet/Redemption

### BR-5.1: Wallet Redemption Amount
**Formula (PHP, lines 1447-1460):**
```
IF redeem_percent > 0:
  allowed_redeem = totalamount × (redeem_percent / 100)
  IF allowed_redeem > totalamount: can_redeem = totalamount
  ELSE: can_redeem = allowed_redeem
ELSE:
  allowed_redeem = wal_balance
  
redeemed_amount = floor(min(redeem_request, can_redeem))
```

### BR-5.2: Wallet Payment Mode Override
**Rule (PHP, lines 1642-1643):**
```
IF redeemed_amount == totalamount AND is_use_wallet:
  payment_type = 'Wallet Payment'
  payment_mode = 'Wallet'
```

---

## Rule Category 6: Branch Resolution

### BR-6.1: Branch Cascade (5 levels)
**Rule (PHP, lines 1546-1567):**
```
IF branch_settings = 1:
  Priority 1: branchwise_cus_reg=1 AND payOtherBranch=0 → cus_reg_branch
  Priority 2: branchWiseLogin=1 AND payOtherBranch=0 → cusData['branch']
  Priority 3: payOtherBranch=0 → cusData['branch']
  Priority 4: branchWiseLogin=1 AND payOtherBranch=1 AND empLog_branch → empLog_branch
  Priority 5: ELSE → generic[id_branch] (user-selected)
ELSE:
  id_branch = NULL
```

---

## Rule Category 7: Receipt Number

### BR-7.1: Receipt Number Generation
**Formula (PHP, lines 84-113):**
```
IF receipTcode config set:
  Last receipt = SELECT MAX(receipt_no) WHERE id_scheme AND branch
  Split by company short_code
  Increment number portion
  Format: {short_code}{7-digit-padded-number}
  Example: LMX0000001
ELSE:
  Last receipt = SELECT MAX(receipt_no)
  Increment
  Format: {7-digit-padded-number}
  Example: 0000001
```

### BR-7.2: Receipt Assignment Condition
**Rule (PHP, line 1572-1576):**
```
IF get_rptnosettings() == 1: Generate receipt
ELSE: receipt_no = NULL
```

---

## Rule Category 8: DigiGold Benefits

### BR-8.1: Same-Commodity Benefit
**Formula (PHP, lines 1596-1606):**
```
IF interest_type = 0 (Percentage):
  benefit_amt = payment_amount × (interest_value / 100)
IF interest_type = 1 (Fixed):
  benefit_amt = interest_value

benefit_wgt = benefit_amt / metal_rate
```

### BR-8.2: Other-Commodity Benefit
**Formula (PHP, lines 1608-1618):**
```
other_metal_rate = rate for OTHER commodity at same branch
IF interest_type = 0:
  benefit_wgt = metal_wgt × (interest_value / 100)
  benefit_amt = benefit_wgt × other_metal_rate
IF interest_type = 1:
  benefit_amt = interest_value
  benefit_wgt = benefit_amt × other_metal_rate  ← ⚠ LIKELY BUG: should be / not ×
```

---

## Rule Category 9: Discount

### BR-9.1: Discount Application
**Rule (PHP, lines 1679-1681):**
```
IF discount_installment == current_iteration OR discount_type == 0:
  pay_array['discountAmt'] = discountedAmt
```
**Meaning:** Type 0 = every installment gets discount, Type 1 = only specific installment

### BR-9.2: Discount in Amount Calculation
**Rule (PHP, line 1320):**
```
amount = (payment_amount + discountedAmt) / installments
// discountedAmt is ADDED BACK to compute the "actual scheme amount" before discount
```

---

## Rule Category 10: Validation Rules

### BR-10.1: Client-Side Validations (JS)
| # | Check | Error Message |
|---|-------|--------------|
| 1 | selected_weight ≤ eligible_weight | "Selected weight more than eligible" |
| 2 | selected_weight > 0 (weight schemes) | "Select at least one weight" |
| 3 | payment_date not empty | "Select payment date" |
| 4 | scheme_account selected | "Select Scheme A/C No" |
| 5 | sum_of_amt > 0 | "Invalid payment amount" |
| 6 | payment_amt ≤ sum_of_amt | "Received amount less than payment" |
| 7 | sum_of_amt ≤ payment_amt | "Received amount more than payment" |
| 8 | payment_status selected | "Select payment status" |
| 9 | branch selected | "Select branch" |
| 10 | employee selected | "Select Employee" |
| 11 | metal_rate_date (if editing) | "Select metal rate date" |
| 12 | metal_rate (if editing) | "Select metal rate" |
| 13 | total_amt + gst == payment_amt | "Received and Payment not tallied" |

### BR-10.2: Server-Side Validations (PHP)
| # | Check | Action |
|---|-------|--------|
| 1 | payment_amount > 0 | Skip save, show error |
| 2 | form_secret valid | Skip save, "Invalid Form Submit" |
| 3 | installments > 0 | Force to 1 |
| 4 | installments ≤ allowed_dues | Return JSON error |
| 5 | (paid + new) ≤ total_installments | Return JSON error |
| 6 | trans_status() | Commit or rollback |

---

## Rule Category 11: Multi-Installment Payment Mode Details

### BR-11.1: Amount Splitting for Multi-Installment
**Rule (PHP):**
```
IF installments > 1:
  Cash per installment = cash_payment / installments
  Card per installment = card_amt / installments (first card only)
  Cheque per installment = cheque_amount / installments (first cheque only)
  NB per installment = nb_amount / installments (first NB only)
  Advance per installment = adj_amount / installments (first advance only)
ELSE:
  Each mode detail gets full amount
  Multiple cards/cheques/NBs each create separate records
```
**⚠ Critical: Multi-installment only uses FIRST payment detail per mode. All other details are ignored.**

---

## ⚠ Known JS/PHP Discrepancies

| # | Area | JS Behavior | PHP Behavior | Risk |
|---|------|-------------|--------------|------|
| 1 | GST formula | Always inclusive-style calc | Separate inclusive/exclusive | Medium |
| 2 | Weight calc | `total_amt / metal_rate` | Complex GST-adjusted formula | High |
| 3 | Installment validation | Client-side only (max/min bounds) | Server re-validates | Low (fixed) |
| 4 | Discount | Applied to displayed amount | Applied specifically per installment | Medium |
| 5 | Payment mode detail | Not split in JS display | Split per installment in PHP | Low |
