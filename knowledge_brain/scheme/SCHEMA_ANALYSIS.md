# Scheme Module — Schema Analysis
> **Round**: 2 | **Date**: 2026-03-24

---

## 1. Core Table: `scheme`

**Role**: Master scheme configuration — contains 130+ columns defining ALL scheme behavior.

### Key Configuration Columns (grouped by feature)
| Column Group | Columns | Purpose |
|---|---|---|
| Identity | `id_scheme`, `scheme_name`, `code`, `sync_scheme_code`, `description`, `logo` | Scheme identity |
| Type | `scheme_type`, `flexible_sch_type`, `flx_denomintion`, `amt_based_on` | Scheme classification |
| Metal | `id_metal`, `id_purity`, `id_classification` | Metal & purity link |
| Amount | `amount`, `min_amount`, `max_amount`, `min_amt_chance`, `max_amt_chance`, `amt_restrict_by` | Amount limits |
| Weight | `min_weight`, `max_weight`, `wgt_convert`, `wgt_store_as`, `firstPayment_as_wgt` | Weight limits |
| Installments | `total_installments`, `max_total_installments`, `min_installments`, `maturity_installment`, `stop_payment_installment` | Installment config |
| Payment | `payment_chances`, `min_chance`, `max_chance`, `free_payment`, `free_payInstallments`, `has_free_ins`, `allowSecondPay`, `approvalReqForFP`, `pay_duration` | Payment rules |
| Maturity | `maturity_type`, `maturity_days`, `closing_maturity_days`, `calculation_type` | Maturity calc |
| Interest | `interest`, `interest_by`, `interest_value`, `interest_weight`, `total_interest`, `interest_type`, `interest_ins` | Interest config |
| Tax/GST | `tax`, `tax_by`, `tax_value`, `total_tax`, `gst_type`, `gst`, `hsn_code` | Tax settings |
| Benefit | `apply_benefit_by_chart`, `apply_debit_on_preclose`, `avg_calc_ins`, `apply_benefit_min_ins`, `avg_calc_by` | Benefit calculation |
| Pre-close | `allow_preclose`, `preclose_months`, `preclose_benefits`, `preclose_type` | Pre-close rules |
| Advance/Unpaid | `allow_advance`, `advance_months`, `advance_weight_limit`, `allow_advance_in`, `allow_unpaid`, `unpaid_months`, `unpaid_weight_limit`, `allow_unpaid_in` | Advanced payments |
| Discount | `discount_type`, `discount_installment`, `discount`, `firstPayDisc`, `firstPayDisc_by`, `firstPayDisc_value`, `all_pay_disc`, `allpay_disc_by`, `allpay_disc_value` | Discounts |
| Referral | `emp_refferal`, `emp_refferal_by`, `emp_refferal_value`, `Emp_ref_values`, `cus_refferal`, `cus_refferal_by`, `cus_refferal_value`, `cus_ref_values`, `agent_refferal`, `agent_credit_type`, `ref_benifitadd_ins_type`, `ref_benifitadd_ins` | Referral config |
| Deduct Installments | `emp_deduct_ins`, `agent_deduct_ins`, `cus_deduct_ins` | Min installments for referral |
| Incentive | `emp_incentive_closing`, `closing_incentive_based_on` | Closing incentive |
| DigiGold | `is_digi`, `daily_pay_limit`, `restrict_payment`, `chit_detail_days`, `total_days_to_pay` | DigiGold config |
| Lucky Draw | `is_lucky_draw`, `max_members`, `has_prize` | Lucky draw |
| Lump Sum | `is_lumpSum`, `joinTime_weight_slabs` | Lump sum scheme |
| TopUp | `is_topup_scheme`, `topup_booking_type`, `topup_min_value`, `topup_max_value`, `topup_denomination`, `topup_store_slab` | TopUp scheme |
| GA Advance | `allow_general_advance`, `adv_min_amt`, `adv_max_amt`, `adv_denomination`, `apply_adv_benefit` | GA config |
| Control | `active`, `visible`, `disable_sch_payment`, `disable_pay`, `disable_pay_amt`, `sch_approval` | Access control |
| Display | `show_ins_type`, `display_payable`, `key_benifits_description`, `noti_msg` | UI settings |
| First Pay | `get_amt_in_schjoin`, `firstPayamt_as_payamt`, `firstPayamt_maxpayable` | First payment |
| Cycle | `installment_cycle`, `ins_days_duration`, `grace_days` | Installment cycle |
| Rate Fixing | `rate_fix_by`, `rate_select`, `otp_price_fixing`, `otp_price_fix_type` | Rate fixing |
| Closing | `store_closing_balance`, `closing_maturity_days` | Closing config |
| Settlement | `setlmnt_type`, `setlmnt_adjust_by` | Settlement |
| Charges | `charge_head`, `charge_type`, `charge` | Convenience charges |
| KYC | `is_pan_required`, `pan_req_amt`, `is_aadhaar_required`, `aadhaar_required_amt`, `is_nominee_required` | KYC requirements |
| Other | `has_gift`, `has_voucher`, `is_enquiry`, `auto_debit_plan_type`, `is_partial_payment`, `set_as_min_from`, `set_as_max_from`, `sch_limit_value`, `one_time_premium`, `no_of_dues` | Misc flags |

---

## 2. Child Settings Tables

### `scheme_benefit_deduct_settings`
| Column | Type | Purpose |
|---|---|---|
| `id` | INT PK | Primary key |
| `id_scheme` | INT FK | Parent scheme |
| `int_calc_on` | INT | Interest calculation basis |
| `installment_no` | INT | Specific installment number |
| `interest_by` | INT | 0=percent, 1=fixed |
| `installment_from` | INT | Range start |
| `installment_to` | INT | Range end |
| `interest_mode` | INT | Mode flag |
| `interest_type` | INT | Type classification |
| `interest_value` | DECIMAL | Interest value |
| `commodity` | INT | Metal type for multi-commodity |
| `created_by` | INT | Creator |
| `date_add` | DATETIME | Created date |

### `scheme_debit_settings`
| Column | Type | Purpose |
|---|---|---|
| `id` | INT PK | Primary key |
| `id_scheme` | INT FK | Parent scheme |
| `installment_from` | INT | Range start |
| `installment_to` | INT | Range end |
| `deduction_type` | INT | 0=percent, 1=fixed |
| `deduction_value` | DECIMAL | Deduction value |
| `created_by` | INT | Creator |
| `date_add` | DATETIME | Created date |

### `scheme_agent_benefit`
| Column | Type | Purpose |
|---|---|---|
| `id_scheme` | INT FK | Parent scheme |
| `installment_from` | INT | Range start |
| `installment_to` | INT | Range end |
| `benefit_type` | INT | Type |
| `benefit_value` | DECIMAL | Value |
| `created_by` | INT | Creator |
| `date_add` | DATETIME | Created date |

### `scheme_incentive_settings`
| Column | Type | Purpose |
|---|---|---|
| `id_scheme` | INT FK | Parent scheme |
| `credit_to` | INT | Recipient type (emp/cus/agent) |
| `credit_for` | INT | Trigger event |
| `from_range` | INT | Range start |
| `to_range` | INT | Range end |
| `credit_type` | INT | 0=percent, 1=fixed |
| `credit_value` | DECIMAL | Value |
| `date_add` | DATETIME | Created |
| `date_upd` | DATETIME | Updated |

### `scheme_flexi_settings`
| Column | Type | Purpose |
|---|---|---|
| `id_scheme` | INT FK | Parent scheme |
| `ins_from` | INT | Installment range start |
| `ins_to` | INT | Installment range end |
| `min_value` | DECIMAL | Minimum amount/weight |
| `max_value` | DECIMAL | Maximum amount/weight |
| `created_by` | INT | Creator |
| `created_on` | DATETIME | Created |
| `updated_by` | INT | Updater |
| `updated_on` | DATETIME | Updated |

### `emp_closing_incentive`
| Column | Type | Purpose |
|---|---|---|
| `id_scheme` | INT FK | Parent scheme |
| `incentive_from` | INT | Range start |
| `incentive_to` | INT | Range end |
| `type` | INT | Value type |
| `value` | DECIMAL | Incentive value |
| `date_add` | DATETIME | Created date |

### `scheme_general_advance_benefit_settings`
| Column | Type | Purpose |
|---|---|---|
| `id_scheme` | INT FK | Parent scheme |
| `interest_by` | INT | 0=percent, 1=fixed |
| `installment_from` | INT | Range start |
| `installment_to` | INT | Range end |
| `interest_type` | INT | Type classification |
| `interest_value` | DECIMAL | Value |
| `created_by` | INT | Creator |
| `date_add` | DATETIME | Created date |

### `scheme_custom_payable_settings`
| Column | Type | Purpose |
|---|---|---|
| `id_scheme` | INT FK | Parent scheme |
| `payable_by` | INT | Payable basis |
| `range_from` | DECIMAL | Range start |
| `range_to` | DECIMAL | Range end |
| `payable_type` | INT | Type |
| `payable_value` | DECIMAL | Value |
| `range_status` | INT | 0=inactive, 1=active |
| `created_by` | INT | Creator |
| `created_on` | DATETIME | Created |
| `updated_by` | INT | Updater |
| `updated_on` | DATETIME | Updated |

---

## 3. Supporting Tables

### `scheme_branch`
| Column | Purpose |
|---|---|
| `id_scheme` | FK to scheme |
| `id_branch` | FK to branch |
| `scheme_active` | 0/1 flag |
| `date_add` | Creation date |

### `gst_splitup_detail`
| Column | Purpose |
|---|---|
| `id_gst_splitup` | PK |
| `id_scheme` | FK to scheme |
| `splitup_name` | GST/SGST/CGST/IGST |
| `percentage` | Rate |
| `status` | 0=inactive, 1=active |
| `type` | NULL=total, 0=intra-state, 1=inter-state |
| `effective_date` | Start date |
| `date_upd` | Updated date |

---

## 4. Key Relationships

```
scheme ──1:N──▶ scheme_branch
scheme ──1:N──▶ scheme_benefit_deduct_settings
scheme ──1:N──▶ scheme_debit_settings
scheme ──1:N──▶ scheme_agent_benefit
scheme ──1:N──▶ scheme_incentive_settings
scheme ──1:N──▶ scheme_flexi_settings
scheme ──1:N──▶ emp_closing_incentive
scheme ──1:N──▶ scheme_general_advance_benefit_settings
scheme ──1:N──▶ scheme_custom_payable_settings
scheme ──1:N──▶ gst_splitup_detail
scheme ──1:N──▶ scheme_account (downstream consumer)
scheme ──N:1──▶ metal
scheme ──N:1──▶ sch_classify
```
