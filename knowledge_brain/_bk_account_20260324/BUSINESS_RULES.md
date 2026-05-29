# Account Module — Business Rules
> **Round**: 1 | **Date**: 2026-03-06

---

## RULE-ACC-001: Account Creation Limit
- **Source**: Controller L150-171
- **Rule**: If `limit_sch_acc == 1`, check current count < `sch_acc_max_count`
- **Formula**: `if (count < limit) → allow; else → reject with "limit exceeded"`
- **Impact**: Prevents exceeding license-based account limits

## RULE-ACC-002: Maturity Date Calculation
- **Source**: Controller L466
- **Rule**: Based on `maturity_type`:
  - `2` (Fixed Maturity): `today + maturity_days days`
  - `3` (Fixed Flexible): `today + total_installments months`
  - `1` (Flexible): NULL (no fixed maturity)
- **Formula**: `date('Y-m-d', strtotime(date('Y-m-d') . '+' . $days . ' days'))`

## RULE-ACC-003: Branch Resolution
- **Source**: Controller L455-463, L815-826
- **Rule**: 3-level prioritization:
  1. If `branch_settings == 1`:
     - If `branchWiseLogin == 1` OR `is_branchwise_cus_reg == 1`: Use POST branch || customer branch
     - Else: Use POST branch only
  2. If `branch_settings == 0`: NULL (no branch)
- **Risk**: Branch NULL can lead to data integrity issues

## RULE-ACC-004: Free Payment on Join
- **Source**: Controller L645-694
- **Rule**: If scheme has `free_payment == 1` AND `is_opening == 0`:
  1. Build payment data using `free_payment_data()`
  2. GST calculation: inclusive (`gst_type=0`) or exclusive (`gst_type=1`)
  3. Weight conversion: scheme_type 2 → calculate, scheme_type 1 → fixed
  4. Payment status: 2 (pending) if `approvalReqForFP == 1`, else 1 (approved)
  5. Generate receipt number if `receipt_no_set == 1`
  6. Insert payment + payment_mode_details
  7. Calculate and update paid installments

## RULE-ACC-005: Account Number Generation
- **Source**: Model L270-410
- **Rule**: 7 modes based on `scheme_wise_acc_no`:
  - `0`: Common (all schemes, sequential)
  - `1`: Common with branch-wise
  - `2`: Scheme-wise
  - `3`: Scheme-wise with branch-wise
  - `4`: Financial year (date range filter)
  - `5`: Financial year + scheme-wise
  - `6`: Financial year + scheme-wise + branch-wise
- **Formula**: `MAX(scheme_acc_number) + 1`, padded to 5 digits
- **Special**: Lucky draw schemes use group_code filter
- **Risk**: LOCK TABLES is commented out → race condition

## RULE-ACC-006: Client ID Generation
- **Source**: Controller L552
- **Formula**: `cliIDcode + "/" + scheme_code + "/" + insertID`
- **Example**: `LMX/GLD/1234`

## RULE-ACC-007: Referral Code Handling
- **Source**: Controller L445-641, L801-1025
- **Rule**: 
  - `is_refferal_by`: 0=Customer, 1=Employee, NULL=None
  - On edit, detect change type:
    - Type 2 (New code): Insert wallet credit transaction
    - Type 1 (Changed code): Update wallet transaction
    - Type 3 (Removed code): Insert wallet debit transaction
  - Credit triggers: based on `cusbenefitscrt_type` / `empbenefitscrt_type` (0=on join, 1=always)

## RULE-ACC-008: Scheme Closing Benefit Calculation
- **Source**: Controller L1306-1345
- **Rule**: Multi-path benefit calculation:
  1. If `apply_benefit_by_chart == 1` AND `sch_int_setting == 1`:
     - DigiGold (`calculate_by=1`): `getPaymentData()` with interest chart
     - Fixed Maturity (`calculate_by=2`):
       - If bonus available AND no limit exceed AND fully paid → `getBonusInsAmt()`
       - Else → `getPaymentData()` with interest chart
     - Common (`calculate_by=0`):
       - Pending installments OR maturity not reached: Use interest chart
       - Completed AND matured: Use stored `interest` value
  2. RAHUL fix: `$acc_interest = $allow_benefit ? $acc_interest : 0`

## RULE-ACC-009: Pre-Close Deduction
- **Source**: Controller L1350-1388
- **Rule**: If `apply_debit_on_preclose == 1`:
  - Get deduction settings → `getAccBlcDebitSettings()`
  - DigiGold/Maturity: Use `getPaymentData()` for deduction
  - Common: Percent or fixed amount deduction
  - Gift deduction (if `deduct_in == 1`): Calculate gift value deduction

## RULE-ACC-010: Allow Pre-Close Benefit
- **Source**: Controller L4368-4406
- **Rule**: Multi-factor determination:
  1. If `calculation_type == 1` (installment-based): Allow if paid ≥ total installments
  2. If `calculation_type == 2` (maturity-based): Allow if current date > maturity date
  3. Pre-close check: If `allow_preclose == 1` AND `preclose_months ≥ 1` AND `preclose_benefits == 1`:
     - `preclose_type == 1`: Compare paid_installments ≥ preclose_months
     - Else: Compare current date > start_date + preclose_months days

## RULE-ACC-011: Weight vs Amount Scheme Detection
- **Source**: Controller L1251-1261
- **Rule**:
  - `is_weight = 0` (Amount): scheme_type 0, or scheme_type 3 with flexible types 1,6, or (flexible 2 + wgt_convert 2)
  - `is_weight = 1` (Weight): scheme_type 1 or 2, or scheme_type 3 with flexible types 2(wgt_convert 0/1),3,4,5,7,8

## RULE-ACC-012: Closing Balance Computation
- **Source**: Controller L1575-1598
- **Rule**: `closing_balance = paid_amount - tax - debit - bank_chgs - charges + interest + GA_bonus + DG_benefit`
- For weight schemes: debit is divided by metal_rate, paid amount is in weight
- Rounding: Based on `metal_wgt_roundoff` (0=trim, 1=format) and `metal_wgt_decimal`

## RULE-ACC-013: Closing Weight Storage
- **Source**: Controller L1616-1618
- **Rule**: `store_closing_balance_as == 1` OR (`is_weight == 1` AND `store_closing_balance == 0`) → store closing_balance as closing_weight

## RULE-ACC-014: Employee Incentive on Closing
- **Source**: Controller L1655-1719
- **Rule**: If `emp_incentive_closing == 1`:
  - Based on `closing_incentive_based_on`:
    - `2` (Weight): Match closing_weight to incentive range, calculate percent
    - `1` (Installments): Match paid_installments to incentive range, get fixed value
  - Credit wallet or create new wallet account + credit

## RULE-ACC-015: Pre-Close Referral Deduction
- **Source**: Controller L1724-1795
- **Rule**: If pre-close (paid ≠ total):
  - Employee ref: If `emp_refferal == 1` AND `emp_deduct_ins > paid_installments`:
    - Get benefit data → insert wallet debit transaction
    - Customer intro detection → debit customer's employee wallet
  - Agent ref: If `agent_refferal == 1` AND `agent_deduct_ins > paid_installments`:
    - Insert agent debit transaction + update cash points

## RULE-ACC-016: OTP Verification
- **Source**: Controller L1129-1143, L4047-4068
- **Rule**: Multiple OTP flows:
  - Closing OTP: Compare POST otp with DB otp_code
  - Scheme join OTP: Compare with session `OTP_scheme_join` + check expiry
  - Gift OTP: Compare with session `pay_OTP` + check expiry
- **Bug**: `verifyotp_gift()` uses `=` (assignment) instead of `==` at L4188

## RULE-ACC-017: SMS Gateway Routing
- **Source**: Controller L1941-1955
- **Rule**: Duplicated 6+ times:
  - `sms_gateway == '1'` → MSG91
  - `sms_gateway == '2'` → Nettyfish
  - `sms_gateway == '3'` → SpearUC
  - `sms_gateway == '4'` → Asterixt
  - `sms_gateway == '5'` → Qikberry

## RULE-ACC-018: Account Name Edit Control
- **Source**: Controller L197, L474, L834
- **Rule**: If `cusName_edit == 0` → use customer's `cus_name` (read-only)
  If `cusName_edit == 1` → use user-entered `account_name` (editable)

## RULE-ACC-019: Voucher Processing
- **Source**: Controller L697-721
- **Rule**: On account creation with voucher:
  - Insert `gift_card` record (free_card=4, gift_for=2, status=0)
  - Upload voucher image
  - On edit: Deactivate old (status=5), insert new

## RULE-ACC-020: Send Login Details
- **Source**: Controller L1905-1939, L1956-1963
- **Rule**: Sends mobile + password via SMS to selected customers
- **Risk**: Password sent in plaintext SMS

## RULE-ACC-021: DigiGold Revert Check
- **Source**: Controller L1836-1844
- **Rule**: Before reverting a closed account, check if customer has an active DigiGold account
- If active DigiGold exists → block revert with error

## RULE-ACC-022: Scheme Group Management
- **Source**: Controller L3062-3148
- **Rule**: Groups have: id_scheme, id_branch, group_code, start_date, end_date
- Code uniqueness check via `code_available()`
- Delete checks for associated accounts

## RULE-ACC-023: TopUp Scheme Handling
- **Source**: Controller L236-241, L512-514
- **Rule**: For topup schemes (`is_topup_scheme == 1`):
  - `topup_rate`, `topup_amount` (rounded), `topup_weight`
  - `topup_booking_type` determines behavior

## RULE-ACC-024: Lump Sum Weight Distribution
- **Source**: Controller L509-510, L848-849
- **Rule**: `lump_payable_weight = lump_joined_weight / total_installments`
- Only when `is_lumpSum == 1` AND `lump_joined_weight > 0`
- **Risk**: Division by zero if `total_installments == 0`

## RULE-ACC-025: Disable Payment Block
- **Source**: Controller L4113-4128
- **Rule**: Admin can block/unblock payments on specific accounts
- Sets `disable_payment` flag + `disable_pay_reason`
