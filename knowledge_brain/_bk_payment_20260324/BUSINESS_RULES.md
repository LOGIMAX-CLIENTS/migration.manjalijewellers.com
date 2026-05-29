# Payment Module — Business Rules
> **Updated**: 2026-03-06 | **Round**: 2

## RULE-PAY-001: GST Calculation (Inclusive vs Exclusive)
- **Formula (Exclusive)**: `gst_amt = actual_pay × (gst / 100)`
- **Formula (Inclusive)**: `gst_amt = actual_pay - (actual_pay × (100 / (100 + gst)))`
- **Implementation**: Controller L1454-1460 (SaveAll), L485-491 (general_advance)
- **Validation**: Server-side only
- **Edge cases**: `gst_type=1` is Exclusive, `gst_type=0` is Inclusive (counterintuitive naming)

## RULE-PAY-002: Metal Weight Conversion
- **Formula**: `metal_weight = payment_amount / metal_rate`
- **Implementation**: `amount_to_weight()` L2480-2484
- **Variants**:
  - `fix_weight=2` (Fixed weight scheme): Uses scheme amount minus GST
  - `fix_weight=3` (Flexible scheme): Depends on `flexible_sch_type` (2,3,4,5,7,8)
  - `else`: Use provided metal_weight directly
- **Validation**: Server-side; JS also calculates for display
- **Rounding**: Controlled by session `metal_wgt_roundoff` and `metal_wgt_decimal`

## RULE-PAY-003: Payment Mode Detection
- **Rule**: If only one mode has amount > 0 → use that mode code. If 2+ modes → 'MULTI'
- **Modes**: CSH, CC, DC, CHQ, NB, VCH, ADV_ADJ, REF_WALLET
- **Implementation**: Controller L1333-1385 (SaveAll), L410-435 (general_advance), L848-892 (Update_payment)
- **⚠️ Risk**: Logic is duplicated 3 times with slight variations (VCH not in GA, REF_WALLET not in Update)

## RULE-PAY-004: Receipt Number Generation
- **7 modes** based on `chit_settings.scheme_wise_receipt`:
  1. Common (global sequential)
  2. Branch-wise
  3. Scheme-wise
  4. Scheme + Branch
  5. Financial Year
  6. FY + Scheme + Branch
  7. FY + Branch
- **Format**: `{company_short_code}{7-digit-padded-number}` or just `{7-digit-padded-number}`
- **Implementation**: Model `get_receipt_no()` L31-123, Controller `generate_receipt_no()` L84-113
- **Controlled by**: `chit_settings.receipt_no_set` (0=auto on save, 1=manual)
- **⚠️ Risk**: No database lock — could generate duplicates under concurrent access

## RULE-PAY-005: Branch Resolution for Payment
- **Priority order** (Controller L1525-1546):
  1. `is_branchwise_cus_reg=1 && payOtherBranch=0` → Customer registration branch
  2. `branchWiseLogin=1 && payOtherBranch=0` → Customer's account branch
  3. `payOtherBranch=0` → Customer's branch (fallback)
  4. `branchWiseLogin=1 && payOtherBranch=1 && empLog_branch!=N` → Employee's login branch
  5. Else → Form-provided `id_branch` or NULL
  6. If `branch_settings=0` → NULL (no branch)

## RULE-PAY-006: Wallet Redemption
- **Formula**: `allowed_redeem = totalamount × (redeem_percent / 100)` or full balance if percent=0
- **Cap**: `can_redeem = min(allowed_redeem, wal_balance)`
- **Actual**: `redeemed_amount = floor(min(redeem_request, can_redeem))`
- **Implementation**: Controller L1428-1444
- **Controlled by**: `chit_settings.allow_wallet`, `is_use_wallet` checkbox
- **Effect on mode**: If entire amount paid by wallet → `payment_mode = 'Wallet'`, `payment_type = 'Wallet Payment'`

## RULE-PAY-007: Installment & Due Type
- **Due Types**:
  - `ND` = Normal Due
  - `PD` = Pending Due (unpaid from previous months)
  - `AD` = Advance Due (paying ahead)
  - `GA` = General Advance
  - `A` = Advance (online)
  - `P` = Pending (online)
  - `S` = Split payment
- **Multi-installment handling**: If `installments > 1`:
  - Payment amount divided equally
  - First installment = ND, rest = PD (if PN) or AD (if AN)
  - Mode amounts also divided by installment count

## RULE-PAY-008: Account Number Generation (First Payment)
- **Trigger**: When `isAcnoAvailable()` returns eligible AND `schemeacc_no_set=0`
- **Flow**:
  1. Check if scheme has lucky draw → `updateGroupCode()`
  2. Call `account_model->account_number_generator(id_scheme, branch, group_code)`
  3. Update `scheme_account.scheme_acc_number`
- **Controlled by**: `chit_settings.schemeacc_no_set`

## RULE-PAY-009: Payment Status Flow
```
Pending (7) → Awaiting (2) → Success (1)
                            → Failure (3)
                            → Cancel (4)
                            → Refund (6)
```
- Online payments start at `Pending (7)` or `Awaiting (2)`
- Manual payments can start at `Success (1)` directly
- Status changes logged in `payment_status` table

## RULE-PAY-010: Discount Calculation
- **Formula**: `actual_pay = payment_amount - discountedAmt`
- **Types**: `discount_type` in scheme — Applied to first payment or specific installments
- **Implementation**: Controller L1451-1453 (SaveAll)
- **Note**: General Advance does NOT apply discount (`actual_pay = pay_amt`)

## RULE-PAY-011: DigiGold Benefits
- **Trigger**: `scheme.is_digi = 1 && scheme.interest = 1`
- **Same commodity**: `benefit_amt = payment_amount × (interest_value / 100)` → convert to weight
- **Other commodity**: `benefit_wgt = metal_wgt × (interest_value / 100)` → apply other metal rate
- **Implementation**: Controller L1556-1598

## RULE-PAY-012: Average Payable Calculation
- **Trigger**: After `avg_calc_ins` installments, system calculates average and locks max payable
- **Formula**: Sum payments for first N months → divide by N → set as max_amount
- **Implementation**: Model `get_paymentContent()` L1561-1620
- **Affects**: `max_amount`, `max_weight`, `payable` fields

## RULE-PAY-013: Payment Chances
- **Monthly Scheme**: Chances = number of payments in current month
- **Controlled by**: `scheme.max_chance` (0 = unlimited, else capped)
- **Daily Scheme**: Controlled by `scheme.daily_pay_limit`
- **Implementation**: Model `get_paymentContent()` — `current_chances_used` calculation

## RULE-PAY-014: Post-Dated Cheque to Payment Conversion
- **Trigger**: PDC status changed to SUCCESS (1)
- **Flow**: PDC data → build payment array → INSERT into `payment` table
- **Fields mapped**: `pay_mode → payment_mode`, `amount → payment_amount`, `cheque_no`, bank details
- **Implementation**: Controller `postdate_payment_form/Update` L284-372
- **⚠️ Typo bug**: L308 has `$pay['payee_ifsc]']` — bracket inside string

## RULE-PAY-015: Referral Benefits
- **Types**: Customer referral (`cus_refferal`), Employee referral (`emp_refferal`), Agent referral (`agent_refferal`)
- **Trigger**: Based on `ref_benifitadd_ins_type` and `ref_benifitadd_ins` installment milestones
- **Implementation**: Controller `insert_referral_data()` L3417-3484

## RULE-PAY-016: Financial Year for Receipt
- **Source**: `ret_financial_year` table where `fin_status = 1`
- **Used for**: Receipt year prefix (`receipt_year` column in payment)
- **Implementation**: Model `get_receipt_no()` L36-39

## RULE-PAY-017: OTP Verification for Payments
- **Trigger**: When `isOTPRegForPayment()` returns true (from `chit_settings`)
- **Flow**: `generateotp()` → sends 6-digit OTP via SMS + email → stores in session with expiry
- **Expiry**: Configurable via `payOTP_exp` (from DB), stored as `pay_OTP_expiry = time() + duration`
- **Verification**: `update_otp()` checks session `pay_OTP` match AND `pay_OTP_expiry > time()`
- **⚠️ Bug**: L5081 uses `=` (assignment) instead of `==` (comparison): `$otp = $this->session->userdata('pay_OTP')` — always true
- **Implementation**: Controller L5023-5091

## RULE-PAY-018: SMS Gateway Routing
- **Rule**: SMS dispatched via if/elseif chain based on `config('sms_gateway')`
- **Gateway Map**:
  - `1` → MSG91 (`sendSMS_MSG91`)
  - `2` → Nettyfish (`sendSMS_Nettyfish`)
  - `3` → SpearUC (`sendSMS_SpearUC`)
  - `4` → Asterixt (`sendSMS_Asterixt`)
  - `5` → Qikberry (`sendSMS_Qikberry`)
- **Service Check**: Service must have `serv_sms = 1` to send SMS
- **WhatsApp**: Sent if `serv_whatsapp = 1` — uses `send_whatsApp_message()`
- **⚠️ Risk**: If gateway code doesn't match 1-5, no SMS is sent (no fallback)
- **Duplicated at**: Controller L254-265, L340-351, L5043-5053, and multiple other places (~8 times total)

## RULE-PAY-019: Payment Approval Workflow (update_pay_status)
- **Trigger**: Admin approves/rejects online payment from verification page
- **Status Flow**:
  - `auto_pay_approval=1` → Status set to `1` (Success) directly
  - `auto_pay_approval=0` → Status set to `2` (Awaiting), needs manual approval
  - `update_pay_status` handles manual status change from `2` → `1` or `3`
- **On Approval (status=1)**:
  1. Generate receipt number (if `receipt_no_set=0`)
  2. Generate account number (if first payment)
  3. Update paid installments
  4. Send SMS + Email notification
  5. Sync data (JIL or Standard)
- **Implementation**: Controller L2853-2946

## RULE-PAY-020: Thermal Receipt Generation
- **Types**: 4 types handled by `thermal_invoice($id, $type, $date)`:
  - `'Payment'` → Payment thermal receipt (.prn file download)
  - `'Customer'` → Customer details print
  - `'CloseAccount'` → Scheme account closure receipt (PDF)
  - `'WalletTransaction'` → Wallet transaction receipt (PDF)
- **Payment Receipt**: Generates `.prn` file via `receipt_thermal_prn` view
- **Others**: Generate PDF via DomPDF library
- **Company Details**: Branch-aware (`get_branchcompany` if `branch_settings=1`)
- **Implementation**: Controller L4950-5022

## RULE-PAY-021: Split Payment Logic
- **Trigger**: `split_payment()` method
- **Rule**: Splits a single payment into sub-payments
- **Used**: When a payment needs to be distributed across multiple scheme accounts
- **Implementation**: Controller L2971-3043

## RULE-PAY-022: Payment Gateway Verification Routing
- **Trigger**: `verify_payment()` routes to gateway-specific verifier based on `pg_code`
- **Routing Map**:
  - `pg_code=1` → PayU → `verify_PayUpayments()`
  - `pg_code=2` → HDFC/CCAvenue → `verify_hdfcpayment()`
  - `pg_code=3` → TechProcess → `verifyWithTechProcess()`
  - `pg_code=4` → Cashfree → `verify_cashfreepayment()`
  - `pg_code=7` → Razorpay → `verifyRazorPayments()`
  - `pg_code=8` → EaseBuzz → `verify_easebuzzpayment()`
- **⚠️ Risk**: No fallback for unknown pg_codes, no default case
- **Implementation**: Controller L3788-3803

## RULE-PAY-023: Dynamic Payment Mode Auto-Insert
- **Trigger**: `update_paymentMode($mode)` called during online payment processing
- **Rule**: If `payment_mode` short_code doesn't exist in `payment_mode` table → auto-create it
- **Format**: `mode_name` = `ucwords(str_replace('_', ' ', $mode))`, `short_code` = `strtoupper($mode)`
- **Defaults**: `status=1`, `sort_order=0`, `show_in_pay=0`
- **Implementation**: Controller L6212-6234

## RULE-PAY-024: CSRF Protection (form_secret)
- **Trigger**: `verify_form_secret()` called before SaveAll and general_advance
- **Mechanism**: Compares submitted `form_secret` with session-stored `form_secret`
- **⚠️ Gap**: NOT applied to Delete (GET request), online payment callbacks, or PDC updates

## RULE-PAY-025: Custom Entry Date
- **Trigger**: When `chit_settings.edit_custom_entry_date = 1` AND `custom_entry_date = 1`
- **Rule**: Allows admin to set a custom date (different from payment processing date)
- **Used for**: Day-close scenarios where payments are recorded after business hours
- **Stored as**: `payment.custom_entry_date`

## RULE-PAY-026: Payment Activity Logging
- **Format**: JSON-encoded POST data written to `log/{YYYY-MM-DD}/{type}/create_payment_{YYYY-MM-DD}.txt`
- **Types**: `manual/`, `general_advance/`, `cashfree/`
- **Additional**: `log_model->log_detail()` writes to `log_detail` DB table for audit trail
- **Gateway Logs**: Each gateway writes curl responses to its own log subfolder

## RULE-PAY-027: Auto-Pay Approval Configuration
- **Key**: `config('auto_pay_approval')`
- **Values**: `1` = auto-approve online payments, `0` = require manual approval
- **Effect**: When `1`, `payment_status` goes directly to `1` (Success) after gateway confirms
- **When `0`**: Goes to `2` (Awaiting), admin must manually approve via `update_pay_status`

## RULE-PAY-028: Revert Approval
- **Types**: Standard (`revertApproval`) and JIL-specific (`revertApproval_jil`)
- **Standard**: Sets `payment_status` back to `2` (Awaiting), decrements `total_paid_ins`
- **JIL**: Additionally reverts sync data and JIL-specific records
- **Implementation**: Controller L3647-3743
- **⚠️ Risk**: Only decrements paid installments count — doesn't revert receipt number, wallet transactions, or referral benefits
