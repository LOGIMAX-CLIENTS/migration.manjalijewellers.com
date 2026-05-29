# Scheme Module — Business Rules
> **Round**: 1 | **Date**: 2026-03-06

---

## RULE-SCH-001: Scheme Creation Limit
- **Source**: Controller L137-152
- **Rule**: If `limit_sch == 1`, scheme_count() must be < `sch_max_count`
- **Enforcement**: Server-side only (controller check before rendering form)

## RULE-SCH-002: Scheme Type
- **Source**: Controller L226, Model L245
- **Values**: 0=Amount, 1=Weight, 2=Amount-to-Weight, 3=Flexible
- **Impact**: Controls form fields, payment calculations, and closing behavior

## RULE-SCH-003: Referral Value Calculation
- **Source**: Controller L287-288, L682-683
- **Formula**:
  - If `cus_refferal_by == 0` AND (scheme_type 0 or 2): `cus_refferal_value = amount × cus_ref_values / 100`
  - Else: `cus_refferal_value = cus_ref_values` (fixed amount)
  - Same logic for employee referral
- **Validation**: Server-side only

## RULE-SCH-004: Free Payment Settings
- **Source**: Controller L265-269
- **Rule**: Free payment requires:
  - `free_payment = 1` (enable)
  - `free_payInstallments` = number of free installments
  - `has_free_ins` = flag
  - `allowSecondPay` = allow second payment
  - `approvalReqForFP` = require approval for free payments

## RULE-SCH-005: Benefit Chart Configuration
- **Source**: Controller L427-448
- **Rule**: When `apply_benefit_by_chart == 1` AND `apply_debit_on_preclose == 0`:
  - Benefit chart is used (interest by installment range)
  - Each row: installment_from/to, interest_by, interest_type, interest_value
  - `int_calc_on` determines calculation basis
  - `commodity` links to metal for multi-commodity schemes

## RULE-SCH-006: Pre-Close Deduction Chart
- **Source**: Controller L488-502
- **Rule**: When `apply_benefit_by_chart == 0` AND `apply_debit_on_preclose == 1`:
  - Deduction chart is used
  - Each row: installment_from/to, deduction_type (0=percent, 1=fixed), deduction_value

## RULE-SCH-007: Agent Benefit Chart
- **Source**: Controller L504-518
- **Rule**: When `agent_refferal == 1`:
  - Agent benefit chart per installment range
  - Each row: installment_from/to, benefit_type, benefit_value

## RULE-SCH-008: Incentive Settings
- **Source**: Controller L467-485
- **Rule**: When any referral enabled (emp/cus/agent):
  - Multi-dimensional chart: credit_to, credit_for, from_range, to_range, credit_type, credit_value

## RULE-SCH-009: Employee Closing Incentive
- **Source**: Controller L520-533
- **Rule**: When `emp_incentive_closing == 1`:
  - Chart with: incentive_from/to, type, value
  - `closing_incentive_based_on`: 1=installments, 2=weight

## RULE-SCH-010: General Advance Benefit
- **Source**: Controller L534-553
- **Rule**: When `apply_adv_benefit == 1`:
  - GA benefit chart: installment_from/to, interest_by, interest_type, interest_value
  - Controls `adv_min_amt`, `adv_max_amt`, `adv_denomination`

## RULE-SCH-011: DigiGold Uniqueness
- **Source**: Model L801-815, Controller L1232-1239
- **Rule**: Only ONE DigiGold scheme per metal type can be active
- **Check**: `SELECT IF(EXISTS(SELECT 1 FROM scheme WHERE is_digi=1 AND active=1 AND id_metal=X), 1, 0)`
- **Enforcement**: Server-side check before allowing is_digi=1

## RULE-SCH-012: Branch-Wise Scheme Mapping
- **Source**: Controller L398-411
- **Rule**: When `branch_settings == 1`:
  - Schemes mapped to specific branches via `scheme_branch` table
  - On edit: delete-then-insert (loses created dates)

## RULE-SCH-013: GST Configuration
- **Source**: Controller L412-425
- **Rule**: Each scheme has GST split-up:
  - Rows with type=NULL: Total GST (updates scheme.gst field)
  - Rows with type=0: Intra-state (SGST/CGST)
  - Rows with type=1: Inter-state (IGST)
  - `gst_type`: 0=inclusive, 1=exclusive

## RULE-SCH-014: Scheme Deletion Guard
- **Source**: Controller L992-1019
- **Rule**: Cannot delete scheme if `scheme_account` records exist
- **Check**: `check_acc_records($id)` → count > 0 blocks delete
- **Gap**: Orphaned child settings tables NOT cleaned up on delete

## RULE-SCH-015: Default Value Handling
- **Source**: Model L466-492, L493-519, L689-715
- **Rule**: On insert/update, `SHOW COLUMNS` is called to get defaults
- **Logic**: If empty value → use column default; if `0` or `'0'` → keep as 0
- **Performance**: SHOW COLUMNS on every single save operation

## RULE-SCH-016: TopUp Scheme Settings
- **Source**: Controller L378-384, L557-577
- **Rule**: When `is_topup_scheme == 1`:
  - Requires: topup_booking_type, topup_min/max_value, topup_denomination, topup_store_slab
  - Chart: payable_by, range_from/to, payable_type, payable_value
  - On edit: soft-delete (status=0) then batch insert

## RULE-SCH-017: Flexible Scheme
- **Source**: Controller L303, L449-465
- **Rule**: When `scheme_type == 3` (Flexible):
  - `flexible_sch_type` determines sub-behavior (1-8)
  - `flx_denomintion` = denomination value
  - Flexi settings chart: ins_from/to, min_value, max_value

## RULE-SCH-018: Lucky Draw Settings
- **Source**: Controller L313-315
- **Rule**: When `is_lucky_draw == 1`:
  - `max_members` = max group members
  - `has_prize` = prize eligibility flag
  - Interacts with scheme_group in Account module

## RULE-SCH-019: Payment Control
- **Source**: Controller L341-342
- **Rule**: Multiple payment control flags:
  - `disable_pay`: Block payments at threshold
  - `disable_pay_amt`: Amount threshold for block
  - `disable_sch_payment`: Disable all payments for scheme
  - `stop_payment_installment`: Stop at specific installment

## RULE-SCH-020: Maturity Type
- **Source**: Controller L232
- **Rule**: 1=Flexible (no end), 2=Fixed days (start+maturity_days), 3=Fixed months
- **Related**: `maturity_installment`, `closing_maturity_days`

## RULE-SCH-021: Interest Configuration
- **Source**: Controller L250-254
- **Rule**: When `interest == 1`:
  - `interest_by`: 0=percent, 1=fixed
  - `interest_value`: The value
  - `interest_weight`: Weight value for weight schemes
  - `total_interest`: Pre-computed total
  - `interest_type`: Additional type classification
