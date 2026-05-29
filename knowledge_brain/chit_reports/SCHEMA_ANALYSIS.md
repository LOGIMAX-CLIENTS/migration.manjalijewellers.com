# SCHEMA ANALYSIS — chit_reports
> Round R6-Upgrade — 2026-03-25 | Updated file sizes: payment_model 8,903L, account_model 3,660L

---

## Part A: Owned/Primary Tables (chit_reports primarily reads these)

### Table: `payment`
| Column | Type | Notes |
|---|---|---|
| `id_payment` | INT PK | |
| `id_scheme_account` | INT FK | → `scheme_account` |
| `payment_amount` | DECIMAL | Gross amount (may include GST) |
| `act_amount` | DECIMAL | Actual amount collected |
| `payment_status` | INT | 0=pending, 1=success, 2=rejected, 4=cancelled, -1=failure |
| `payment_mode` | INT/VARCHAR | Cash/Cheque/Online etc |
| `date_payment` | DATE/DATETIME | Payment date |
| `payment_type` | INT | Offline=0/Online=1/Admin App=2 |
| `branch` | INT | Branch where payment was made |
| `sgst` | DECIMAL | GST component |
| `cgst` | DECIMAL | GST component |
| `gst_type` | INT | 0=inclusive, 1=exclusive |
| `receipt_no` | VARCHAR | Receipt number |
| `metal_weight` | DECIMAL | For weight-type schemes |
| `metal_rate` | DECIMAL | Gold rate at time of payment |
| `no_of_dues` | INT | Number of dues for this payment |
| `discountAmt` | DECIMAL | Discount applied |
| `add_charges` | DECIMAL | Bank/processing charges |
| `id_employee` | INT | Employee who took payment |
| `offline_tran_uniqueid` | VARCHAR | External ERP transaction ID |
| `installment` | INT | Installment number |
| `card_no` | VARCHAR | Cheque/card number |
| `id_transaction` | VARCHAR | Online payment transaction ID |
| `payment_ref_number` | VARCHAR | Reference number |

### Table: `scheme_account`
| Column | Type | Notes |
|---|---|---|
| `id_scheme_account` | INT PK | |
| `id_customer` | INT FK | → `customer` |
| `id_scheme` | INT FK | → `scheme` |
| `id_branch` | INT FK | → `branch` |
| `active` | INT | 0=inactive, 1=active, 2=under_approval |
| `is_closed` | INT | 0=open, 1=closed |
| `scheme_acc_number` | VARCHAR | Display account number |
| `group_code` | VARCHAR | For lucky draw schemes |
| `start_date` | DATE | Join date |
| `closing_date` | DATE | Close date |
| `balance_amount` | DECIMAL | Opening balance (migration carry-forward) |
| `balance_weight` | DECIMAL | Opening balance weight |
| `paid_installments` | INT | Pre-loaded installment count (opening accounts) |
| `is_opening` | INT | 1=migration account with pre-loaded balance |
| `closing_balance` | DECIMAL/VARCHAR | Amount/weight at closure |
| `closing_id_branch` | INT | Branch where closed |
| `start_year` | VARCHAR | Financial year segment |
| `total_paid_ins` | INT | Running paid installment count |
| `id_employee` | INT | Employee who opened account |
| `referal_code` | VARCHAR | Employee referral code |
| `id_agent` | INT | Agent who referred |

### Table: `customer`
| Key Columns | Notes |
|---|---|
| `id_customer`, `firstname`, `lastname`, `mobile` | Core identity |
| `pan_no`, `aadhaar_no` | KYC documents |
| `kyc_status` | 0=pending, 1=verified |
| `id_branch` | Customer registration branch |
| `is_new` | Y/N — new or existing customer |

### Table: `scheme`
| Key Columns | Notes |
|---|---|
| `id_scheme`, `scheme_name`, `code` | Identity |
| `scheme_type` | 0=amount, 1=weight, 2=amount-to-weight, 3=flexible |
| `total_installments`, `amount` | Scheme terms |
| `max_weight`, `min_weight` | Weight limits |
| `is_lucky_draw` | 0/1 |
| `gst_type`, `one_time_premium` | GST and premium settings |

### Table: `chit_settings`
| Key Columns | Notes |
|---|---|
| `gst_setting` | 0=no GST, 1=GST enabled |
| `has_lucky_draw` | Client-level lucky draw toggle |
| `currency_symbol`, `currency_name` | Display currency |
| `scheme_wise_acc_no` | 0-6: Account numbering strategy |
| `branchWiseLogin` | 0/1 — branch-wise login enforcement |
| `is_branchwise_cus_reg` | 0/1 — branch-wise customer reg |
| `edit_custom_entry_date` | Date lock setting |
| `currency_decimal` | Decimal places for currency |

---

## Part B: Referenced Tables (other modules, read by chit_reports)

| Table | Module | Used In | Key Columns Read |
|---|---|---|---|
| `branch` | Settings | All branch-filtered reports | `id_branch`, `name`, `short_name`, `show_to_all` |
| `employee` | HR | Employee referral, collection, branch reports | `id_employee`, `firstname`, `lastname`, `emp_code` |
| `payment_mode` | Payment | Mode-wise reports | `id_payment_mode`, `mode_name` |
| `gift_issued` | Gift | Gift report | `id_scheme_account`, `type`, `status` |
| `general_advance_payment` | Advance | Advance report | `id_scheme_account`, `payment_amount` |
| `customer_kyc` | KYC | KYC approval | `id_kyc`, `id_customer`, `status`, `doc_type` |
| `agent_kyc` | Agent | KYC approval (agent) | `id_kyc`, `id_agent`, `status` |
| `customer_enquiry` | Enquiry | Enquiry report | `id_enquiry`, `status`, dates |
| `customer_reg` | Registration | Inter-table tool | `id_customer_reg`, `mobile`, `scheme_ac_no` |
| `transaction` | Transaction | Inter-table tool | `id_transaction`, `is_transferred` |
| `purch_customer` | Purchase | Akshaya Third purchase | `id_purch_customer`, `firstname`, `mobile` |
| `purch_payment` | Purchase | Akshaya Third purchase | `id_purch_payment`, `is_delivered` |
| `payment_status_log` | Payment | Cancel audit | `id_payment`, `id_status_msg`, `charges`, `date_upd` |
| `scheme_group` | Scheme | Lucky draw groups | `group_code`, `id_scheme`, `id_branch`, `status` |
| `company` | Settings | OTP SMS company name | `company_name`, `id_company` |
| `agent` | Agent | Agent KYC | `id_agent`, `firstname`, `lastname` |
| `ret_financial_year` | Finance | Account number generation | `fin_year_from`, `fin_status` |
| `metal_rates` | Metal | Flexible scheme closing | `goldrate_22ct` |
| `inter_wallet_transaction` | Wallet | Wallet transfer report | `id_transaction`, various |

---

## Risk Flags

| Table | Risk | Details |
|---|---|---|
| `payment` | HIGH | chit_reports **writes** to this table (cancel, edit) — not just reads |
| `scheme_account` | HIGH | chit_reports writes via `updateAccountDetails` |
| `customer_kyc` | MEDIUM | Update without transaction wrapping |
| `chit_settings` | HIGH | Single-row config table — all reports depend on it; JOIN failures make all reports break |
| `payment` (no index on `branch`) | MEDIUM | Branch-filtered queries may be slow — check if `branch` column has index |

---

## Part C: Index Analysis (chit_reports Query Patterns)

> Indexes cannot be verified without DB access — this section documents **suspected missing indexes** based on WHERE clauses in model queries called by `admin_reports`.

| Table | Column(s) in WHERE | Query Pattern | Index Needed? | Why Critical |
|---|---|---|---|---|
| `payment` | `date_payment`, `branch`, `payment_status`, `id_scheme_account` | All collection reports, date-range filters | ✅ Compound index on `(payment_status, branch, date_payment)` strongly recommended | Full table scan on large `payment` table if missing |
| `payment` | `DATE(date_payment)` | Date-wise reports use `DATE()` wrapper | ❌ `DATE()` wrapper prevents index use | Rewrite as range: `date_payment >= X AND date_payment < X+1` |
| `scheme_account` | `is_closed`, `id_scheme`, `active` | Outstanding/closed account reports | ✅ Compound `(is_closed, active, id_scheme)` | Outstanding queries join this + payment per scheme |
| `customer` | `date_of_birth`, `date_of_wed` | Celeb dates query uses `DATE_FORMAT(%m%d)` | ❌ `DATE_FORMAT()` defeats any DOB index | No index benefit possible without function-based index (MySQL 8+) |
| `payment_status_log` | `id_payment` | Cancel audit, financial integrity checks | ✅ Should have FK index on `id_payment` | JOIN from payment table to status log |
| `gift_issued` | `id_scheme_account` | Gift report joins | ✅ FK index expected | JOIN from scheme_account |
| `customer_enquiry` | `status`, `entry_date` | Enquiry report filter | ✅ Compound `(status, entry_date)` | Filter + date filter combined |

---

## Part D: Write Operation Inventory

> `admin_reports` is primarily a **read module** — but these are the confirmed write paths:

| Method | Table(s) Written | Columns Changed | Guards Present | Risk |
|---|---|---|---|---|
| `cancel_payment` | `payment` (status=4) | `payment_status` | ✅ `paymentDB` verify before cancel | 🔴 No transaction — partial cancel possible |
| `cancel_payment` | `payment_status_log` | New audit row | None | Same — if insert fails, no rollback |
| `cancel_payment` | Khimji ERP (via syncapi_model) | External sync | Only if `integrationType=2` | External failure not rolled back |
| `updatePaymentDetails` | `payment` | All editable fields | ✅ Log first, then write | 🟡 No validation on individual fields |
| `updateAccountDetails` | `scheme_account` | All editable fields | ✅ `checkCommonSettings()` + `cusexist` | 🟡 No Account module validation |
| `updateAccountDetails` | `customer` | `mobile` (if changed) | ✅ `cusexist` checks new mobile | 🟡 No uniqueness validation |
| `update_kyc` | `customer_kyc` | `status`, `emp_verified_by`, `last_update` | None (raw loop) | 🔴 No transaction wrapping |
| `update_kyc` | `agent_kyc` | Same fields | None | Same risk |
| `update_kyc` | `customer` | `kyc_status=1` | ✅ Only if `verified_kycs==1` | 🟡 Count check may be fragile |
| `update_kyc` | `agent` | `kyc_status=1` | Same condition | Same |
| `purch_delivered` | `purch_payment` | `is_delivered=1`, `delivered_by`, `date` | ✅ OTP verify before flip | 🟡 Not idempotent — no guard if already delivered |
| `update_cusdatas` | `customer_reg` | Sync columns | None documented | 🔴 Cross-module sync — no validation |
| `update_transdatas` | `transaction` | Sync columns | None documented | 🔴 Cross-module sync — no validation |
| `generateTransUniqId` | `payment.offline_tran_uniqueid` | External ERP assigned ID | None | 🟡 No idempotency check |
