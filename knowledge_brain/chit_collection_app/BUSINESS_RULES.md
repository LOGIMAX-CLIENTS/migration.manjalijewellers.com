# chit_collection_app — Business Rules

> **Brain Built:** 2026-03-17 | **Round:** 1

---

## RULE-COL-001: Allow Pay Determination

**Formula:** `allow_pay` is a cascaded multi-condition determination. Final value: `'Y'` or `'N'`

**Implementation:** `adminappapi_model.php::get_payment_details()` L959-1183 (PHP), `mobileapi_model.php::get_payment_details()` (referenced via MOD_MOB in controller)

**Conditions (in priority order):**

| Condition | allow_pay |
|---|---|
| `disable_payment == 1` | N |
| `payment_status == 2` (yet to approve) | N |
| Scheme type 3 + one_time_premium=1 + paid_installments > 0 | N |
| installment_cycle == 2 (days-based) | Recalculated by `get_due_date()` |
| Daily scheme (pay_duration == 0) | Based on curday_total_paid_count vs max_chance |
| Lucky draw: group not assigned AND paid > 1 | N |
| Maturity date exceeded | N |
| Normal: current month not paid → check PD/ND/AD/PN/AN | Y if allowed_due > 0 |
| Normal: current month paid → check advance/pending sub-rules | Y if advance/pending allowed |

**Validation:** PHP only (server-side). Mobile app trusts this value.

**Edge cases:**
- `disable_pay_amt` threshold: if total cash payments exceed threshold, payment is blocked even if criteria met
- Flexible weight schemes: `allowed_dues` always forced to 1

---

## RULE-COL-002: Due Type Cascade

**Formula:** Determines type of installment being paid

**Due Types:**
- `ND` = Normal Due (current month)
- `PD` = Pending Due (past missed)
- `AD` = Advance Due (future month)
- `PN` = Pending + Normal (mixed)
- `AN` = Advance + Normal (mixed)

**Implementation:** `adminappapi_model.php::get_payment_details()` L730-897, `adminapp_api.php::mobile_payment_post()` L1504-1533

**Rules:**
1. If `allow_unpaid=1` and `totalunpaid > 0` → `PD` or `PN`
2. If `allow_advance=1` and `advance_months > 0` → `AD` or `AN`
3. Default → `ND`
4. Days-based (installment_cycle=2): Computed via `payment_modal::get_due_date()`

**Validation:** PHP (server-side). `due_month` and `due_year` set to NULL for PD.

---

## RULE-COL-003: Collection App Payment Status

**Formula:** `payment_status = f(gateway, added_through, login_type)`

| Condition | payment_status |
|---|---|
| `gateway == 0` AND `added_through == 3` (collection app EMP) | `1` (SUCCESS — immediate) |
| `gateway == 0` AND `added_through == 2` (customer app) | `2` (AWAITING) |
| `gateway != 0` (online gateway) | `7` (PENDING — until callback) |

**Implementation:** `adminapp_api.php::mobile_payment_post()` L1579

**Risk:** Cash collection by employee marks payment SUCCESS without any admin approval. This means any employee can mark a payment as collected instantly.

---

## RULE-COL-004: GST Calculation on Collection Payments

**Formula:**

*Inclusive GST (gst_type=0):*
```
gst_removed_amt = insAmt_withoutDisc × 100 / (100 + gst%)
gst_amt = insAmt_withoutDisc - gst_removed_amt
metal_wgt = (gst_removed_amt + discount) / metal_rate
```

*Exclusive GST (gst_type=1):*
```
amt_with_gst = insAmt_withoutDisc × (100 + gst%) / 100
gst_amt = amt_with_gst - insAmt_withoutDisc
metal_wgt = amount / metal_rate
```

**Implementation:** `adminapp_api.php::mobile_payment_post()` L1486-1499

**⚠️ BUG RISK:** Variable `$sch_data['gst_type']` is used at L1489 but the chit data is in `$chit` (not `$sch_data`). This means gst_type from an outer scope (likely undefined/wrong) is used, breaking GST calculation.

---

## RULE-COL-005: Average Payable Calculation

**Formula:**
```
After avg_calc_ins installments: avg_payable = SUM(payment_amount over avg_calc_ins months) / avg_calc_ins
```

*avg_calc_by=0 (by installment count):*
- Triggers when paid_installments >= avg_calc_ins

*avg_calc_by=1 (by join date):*
- Triggers when months since join >= avg_calc_ins

*Effect:* Overrides `max_amount` and `max_weight` for subsequent payments.

**Implementation:** `adminappapi_model.php::get_payment_details()` L587-727

**Once set:** Stored in `scheme_account.avg_payable` column. Used as-is for future payments.

---

## RULE-COL-006: Device Authorization

**Formula:** Multi-step device check on login

1. If `uuid == '1234567890'` OR `employee.is_lmx == 1` → Bypass device check (browser/LMX login)
2. Otherwise: Query `employee_devices` WHERE `emp_id = ? AND device_uuid = ? AND app_type = 1`
3. If device found AND `device_status == 1` → `enable_chit = TRUE`
4. If device found AND `device_status == 0` → `enable_chit = FALSE` (device registered but not approved)
5. If device NOT found → Register new device with `device_status = 0`, deny access

**Implementation:** `adminappapi_model.php::isValidLogin()` L67-96

**Note:** Authentication gate only at login. After login, no per-request device verification occurs.

---

## RULE-COL-007: Metal Weight Calculation (Amount to Weight Conversion)

**Formula:**
```
metal_weight = amount / metal_rate   (scheme_type=2 or scheme_type=3 with wgt_convert)
metal_weight = payment udf2 value    (scheme_type=1 direct weight entry)
metal_weight = bcdiv(metal_wgt, 1, decimal)  (decimal precision control, when round_off=0)
```

**Implementation:** `adminapp_api.php::mobile_payment_post()` L1462-1538, `paymt.php::paySubmit()` L462-478

**Config:** `metal_wgt_decimal` and `metal_wgt_roundoff` from scheme settings control final precision.

---

## RULE-COL-008: Account Number Generation

**Triggered:** After first payment (when `schemeacc_no_set == 0` or `3`)

**Formula:** Determined by `payment_modal::account_number_generator($id_scheme, $branch, $group_code)`

**Settings:** `chit_settings.schemeacc_no_set`:
- `0` = Auto-generate on first payment
- `1` = Manual assignment
- `2` = Pre-populated at entry
- `3` = Auto on first payment (alternate logic)

**Implementation:** `paymt.php::techProcessResponseURL()` L1322-1358, `adminapp_api.php::generateAcNoOrReceiptNo()` ~L

---

## RULE-COL-009: Referral Benefits Processing

**Formula:** On payment success, check if referral benefit should be credited:
1. `ref_benifitadd_ins_type == 1` AND referral_code exists AND paid_installments matches ref_benifitadd_ins AND not yet added → Insert referral data
2. `ref_benifitadd_ins_type == 0` AND referral_code exists AND not yet added → Insert referral data (any installment)

**Implementation:** `adminapp_api.php::insert_referral_data()`, called from `techProcessResponseURL()` L1307-1315

**Side effects:** Creates entries in agent/employee incentive tables via `insertAgentIncentive()`, `insertEmployeeIncentive()`, `customerIncentive()`.

---

## RULE-COL-010: OTP Validation

**Formula:**
```
is_valid = (sysotp == userotp) AND (current_time <= expiry_time)
```

**Implementation:** `adminapp_api.php::check_regOTP_post()` L1318-1339

**⚠️ SECURITY NOTE:** OTP is set to hardcoded `123456` at L1243 in `generateOTP_get()`. Code comment says "For Demo or testing Purpose." This disables actual OTP security.

---

## RULE-COL-011: Free Payment (Enrollment Offer)

**Triggered:** When `scheme.free_payment == 1` on scheme joining

**Formula:** System auto-generates a payment record with `payment_mode='FP'` and `payment_amount = scheme.amount` for installment 1.

**Implementation:** `adminapp_api.php::createAccount_post()` L603-622

**After free payment:** If `schemeacc_no_set==0`, account number is also auto-generated.

---

## RULE-COL-012: Wallet Redemption

**Formula:**
```
allowed_redeem = total_amount × (redeem_percent / 100)
can_redeem = min(allowed_redeem, wallet_balance)
actual_redeem = floor(min(redeem_request, can_redeem))
```

**Implementation:** `paymt.php::paySubmit()` L387-396

**Full wallet payment:** If `redeemed_amount == calc_amt` → payment_status set to SUCCESS immediately (no gateway).
