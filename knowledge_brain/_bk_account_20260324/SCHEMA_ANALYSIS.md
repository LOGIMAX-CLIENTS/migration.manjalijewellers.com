# Account Module — Schema Analysis
> **Round**: 1 | **Date**: 2026-03-06

---

## 1. Core Table: `scheme_account`

### Key Columns (from code analysis)
| Column | Type (Inferred) | Purpose |
|---|---|---|
| `id_scheme_account` | INT PK | Primary key |
| `id_scheme` | INT FK | Links to scheme master |
| `id_customer` | INT FK | Links to customer |
| `id_branch` | INT FK | Branch association |
| `account_name` | VARCHAR | Display name |
| `scheme_acc_number` | VARCHAR | Generated sequential number |
| `ref_no` | VARCHAR | Client ID (e.g., LMX/GLD/1234) |
| `group_code` | VARCHAR | Lucky draw group code |
| `start_date` | DATETIME | Account opening date |
| `maturity_date` | DATE | Calculated maturity date |
| `start_year` | VARCHAR | Financial year prefix |
| `paid_installments` | INT | Count of paid installments |
| `balance_amount` | DECIMAL | Running amount balance |
| `balance_weight` | DECIMAL(10,3) | Running weight balance |
| `last_paid_weight` | DECIMAL(10,3) | Weight at last payment |
| `last_paid_chances` | INT | Chances at last payment |
| `last_paid_date` | DATETIME | Date of last payment |
| `firstPayment_amt` | DECIMAL | First payment amount |
| `referal_code` | VARCHAR | Referral code |
| `is_refferal_by` | INT | 0=customer, 1=employee, NULL=none |
| `employee_approved` | INT | Approving employee ID |
| `added_by` | INT | Source (6=external app) |
| `active` | TINYINT | 0=inactive, 1=active |
| `is_closed` | TINYINT | 0=open, 1=closed |
| `is_new` | CHAR(1) | Y/N for new/existing |
| `is_opening` | TINYINT | 1=existing scheme import |
| `is_registered` | TINYINT | Registration status |
| `remark_open` | TEXT | Opening remark |
| `remark_close` | TEXT | Closing remark |
| `pan_no` | VARCHAR | PAN number |
| `aadhaar_no` | VARCHAR | Aadhaar number |
| `has_gift` | TINYINT | Gift eligibility flag |
| `show_gift_article` | TINYINT | Gift UI toggle |
| `duplicate_passbook_issued` | INT | Passbook dup flag |
| `disable_payment` | TINYINT | Payment block flag |
| `disable_pay_reason` | TEXT | Block reason |

### Closing-Related Columns
| Column | Type (Inferred) | Purpose |
|---|---|---|
| `closing_date` | DATETIME | When account was closed |
| `closing_balance` | DECIMAL | Final balance (weight or amount) |
| `closing_weight` | DECIMAL(10,3) | Final weight |
| `closing_amount` | DECIMAL | Final amount |
| `closing_benefits` | DECIMAL | Calculated benefits |
| `closing_deductions` | DECIMAL | Calculated deductions |
| `closing_paid_amt` | DECIMAL | Total paid to customer |
| `closing_add_chgs` | DECIMAL | Additional charges at closing |
| `closing_interest_val` | DECIMAL(10,3) | Interest value applied |
| `closed_by` | INT | Customer/nominee who closed |
| `employee_closed` | INT | Employee who processed closing |
| `Closing_id_branch` | INT | Branch where closed |
| `bonus_percent` | DECIMAL | Bonus percentage |
| `additional_benefits` | DECIMAL | Additional benefits |
| `add_ben_type` | INT | Additional benefit type |
| `store_closing_balance_as` | INT | 0=amount, 1=weight |
| `dg_other_benefit_amt` | DECIMAL | DigiGold other benefit amount |
| `dg_other_benefit_wgt` | DECIMAL | DigiGold other benefit weight |
| `rep_name` | VARCHAR | Representative name |
| `rep_mobile` | VARCHAR | Representative mobile |
| `is_limit_exceed` | TINYINT | Over-payment flag |

### TopUp-Related Columns
| Column | Type (Inferred) | Purpose |
|---|---|---|
| `topup_rate` | DECIMAL | TopUp metal rate |
| `topup_amount` | DECIMAL | TopUp payment amount |
| `topup_weight` | DECIMAL | TopUp weight |
| `topup_booking_type` | INT | TopUp booking type |

### Rate Fixing Columns
| Column | Type (Inferred) | Purpose |
|---|---|---|
| `fixed_wgt` | DECIMAL | Fixed weight |
| `fixed_metal_rate` | DECIMAL | Fixed metal rate |
| `rate_fixed_in` | INT | 0=local, 2=ERP |
| `fixed_rate_on` | DATETIME | Rate fix date |

---

## 2. Supporting Table: `scheme`

### Key Columns (Referenced in Controller)
| Column | Purpose |
|---|---|
| `id_scheme` | Primary key |
| `scheme_name` | Display name |
| `code` | Short code (e.g., GLD) |
| `scheme_type` | 0=amount, 1=weight, 2=amt-to-wgt, 3=flexible |
| `flexible_sch_type` | Sub-type for scheme_type=3 |
| `total_installments` | Number of installments |
| `amount` | Installment amount |
| `max_chance` | Max payment chances |
| `max_weight` | Max weight per installment |
| `maturity_type` | 1=flexible, 2=fixed days, 3=fixed months |
| `maturity_days` | Days for fixed maturity |
| `maturity_installment` | Installment-based maturity |
| `free_payment` | Free first payment enabled |
| `one_time_premium` | One-time premium scheme |
| `is_lucky_draw` | Lucky draw enabled |
| `max_members` | Max group members |
| `is_digi` | DigiGold scheme |
| `is_lumpSum` | Lump sum scheme |
| `is_topup_scheme` | TopUp enabled |
| `calculation_type` | 1=installment, 2=maturity |
| `maturity_days` (closing) | Days for maturity calc |
| `allow_preclose` | Pre-close allowed |
| `preclose_months` | Pre-close threshold |
| `preclose_type` | 1=installment, 2=days |
| `preclose_benefits` | Pre-close benefits enabled |
| `apply_benefit_by_chart` | Use interest chart |
| `apply_debit_on_preclose` | Deduct on pre-close |
| `emp_incentive_closing` | Employee incentive on close |
| `closing_incentive_based_on` | 1=installments, 2=weight |
| `emp_refferal` | Employee referral enabled |
| `emp_deduct_ins` | Min installments for ref benefit |
| `agent_refferal` | Agent referral enabled |
| `agent_deduct_ins` | Min installments for agent |
| `wgt_convert` | Weight conversion mode |

---

## 3. Table: `otp`

| Column | Purpose |
|---|---|
| `id_otp` | Primary key |
| `otp_code` | Generated OTP |
| `otp_gen_time` | Generation timestamp |
| `is_verified` | 0/1 flag |
| `verified_time` | Verification timestamp |

---

## 4. Table: `gift_issued`

| Column | Purpose |
|---|---|
| `id_gift_issued` | Primary key |
| `id_scheme_account` | FK to scheme_account |
| `id_gift` | FK to gift master |
| `gift_desc` | Description |
| `item_ref_no` | Inventory ref number |
| `gift_amount` | Value |
| `type` | 0=simple, 1=inventory |
| `status` | 0=available, 1=issued, 2=deducted |
| `quantity` | Quantity issued |
| `id_employee` | Issuing employee |
| `id_branch` | Branch |
| `date_issued` | Issue date |
| `paid_installments` | Installments at issue |
| `deducted_date` | Deduction date |
| `deducted_by` | Deducting employee |
| `deduct_remark` | Deduction reason |

---

## 5. Table: `reg_request`

| Column | Purpose |
|---|---|
| `id_reg_request` | Primary key |
| `id_customer` | FK to customer |
| `id_scheme` | FK to scheme |
| `id_branch` | FK to branch |
| `scheme_acc_number` | Requested acc number |
| `ac_name` | Account name |
| `status` | 0=pending, 1=approved, 2=rejected |
| `remark` | Admin remark |
| `id_employee` | Processing employee |
| `is_opening` | Opening balance flag |
| `firstPayment_amt` | First payment amount |
| `balance_amount` | Opening balance |
| `balance_weight` | Opening weight |
| `last_paid_weight` | Last paid weight |
| `last_paid_chances` | Last paid chances |
| `last_paid_date` | Last paid date |
| `paid_installments` | Paid installments count |
| `pan_no` | PAN number |
| `group_code` | Scheme group |

---

## 6. Table: `scheme_group`

| Column | Purpose |
|---|---|
| `id_scheme_group` | Primary key |
| `id_scheme` | FK to scheme |
| `id_branch` | FK to branch |
| `group_code` | Unique group code |
| `start_date` | Group start date |
| `end_date` | Group end date |
| `status` | Active/inactive |
| `last_update` | Last modification |
| `date_add` | Creation date |

---

## 7. Table: `sync_log`

| Column | Purpose |
|---|---|
| `total_records` | Total records processed |
| `scheme_accounts` | Accounts updated |
| `payments` | Payments synced |
| `sync_date` | Sync timestamp |
| `remark` | JSON with acc/pay/error IDs |

---

## 8. Key Relationships

```
scheme_account ──1:N──▶ payment
scheme_account ──1:N──▶ gift_issued
scheme_account ──1:N──▶ wallet_transaction (via id_sch_ac)
scheme_account ──N:1──▶ scheme
scheme_account ──N:1──▶ customer
scheme_account ──N:1──▶ branch
scheme_account ──N:1──▶ scheme_group (via group_code + id_scheme)
customer ──1:N──▶ reg_request
scheme ──1:N──▶ scheme_group
scheme ──1:N──▶ scheme_interest_chart
```
