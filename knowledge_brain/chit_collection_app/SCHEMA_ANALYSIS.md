# chit_collection_app — Schema Analysis

> **Module**: chit_collection_app
> **Built**: 2026-03-25 (Upgrade Round)
> **Note**: This module is an API layer — it does NOT own any tables directly. This analysis documents all tables READ or WRITTEN by the module.

---

## Primary Tables (Heavy Read/Write)

### 1. `payment` (Owned by: payment_modal)

**Purpose**: Core payment transactions — every installment payment creates a row.

| Key Column | Type (Inferred) | Purpose | Used In |
|---|---|---|---|
| `id_payment` | INT PK AUTO | Primary key | All payment ops |
| `id_scheme_account` | INT FK | Links to scheme_account | `mobile_payment_post`, `paySubmit`, all callbacks |
| `payment_amount` | DECIMAL | Installment amount paid | `addPayment()` |
| `payment_status` | TINYINT | 1=Success, 2=Awaiting, 3=Failure, 4=Cancel, 7=Pending, -1=PreInsert | State machine — see FLOW_RISK_MATRIX |
| `date_payment` | DATETIME | Payment timestamp | `addPayment()` |
| `id_transaction` | VARCHAR | Unique txn ID (e.g., `uniqid(time())`) | Gateway callbacks |
| `ref_trans_id` | VARCHAR | Reference transaction ID (batch grouping) | `paySubmit` multi-pay, gateway match |
| `metal_rate` | DECIMAL | Rate at time of payment | Metal weight calculation |
| `metal_weight` | DECIMAL | Calculated weight (scheme_type dependent) | Metal tracking |
| `payment_type` | VARCHAR | 'Payu Checkout', 'HDFC', 'Cash Free', etc. | Reports |
| `added_by` | TINYINT | 1=Admin, 2=Customer App, 3=Collection App | Payment origin tracking |
| `id_employee` | INT FK | Employee who collected | Cash collection attribution |
| `id_branch` | INT FK | Branch of transaction | Branch-wise reports |
| `receipt_no` | VARCHAR | Auto-generated receipt number | Post-payment gen |
| `due_type` | VARCHAR | ND/PD/AD/PN/AN | Installment categorization |
| `due_month` | INT | Due month (NULL for PD) | Installment tracking |
| `due_year` | INT | Due year (NULL for PD) | Installment tracking |
| `actual_trans_amt` | DECIMAL | Actual gateway amount (after wallet deduct) | Reconciliation |
| `gst` | DECIMAL | GST percentage applied | Tax tracking |
| `gst_type` | TINYINT | 0=Inclusive, 1=Exclusive | GST calc mode |
| `gst_amount` | DECIMAL | Calculated GST amount | Tax tracking |
| `redeemed_amount` | DECIMAL | Wallet points redeemed | Wallet reconciliation |
| `discountAmt` | DECIMAL | Discount applied | Discount tracking |
| `no_of_dues` | INT | Number of dues in this payment | Multi-due payments |
| `custom_entry_date` | DATE | Branch-wise custom entry date | Accounting period |
| `id_payGateway` | INT FK | Payment gateway used | Gateway tracking |
| `offline_tran_uniqueid` | VARCHAR | Khimji offline transaction ID | Integration sync |
| `payment_ref_number` | VARCHAR | Gateway reference number | Reconciliation |
| `bank_name` | VARCHAR | Issuing bank | Reports |
| `payment_mode` | VARCHAR | Card/NetBanking/UPI/etc. | Reports |
| `remark` | TEXT | Payment notes | Audit |

**Risks**:
- No unique constraint on `(id_scheme_account, due_month, due_year)` — duplicate payments possible
- `metal_rate` from `file_get_contents('api/rate.txt')` — no validation (BUG-028)
- `payment_status=1` for cash with no approval gate

---

### 2. `scheme_account` (Owned by: scheme/mobileapi module)

**Purpose**: Customer enrollments in savings schemes.

| Key Column | Type (Inferred) | Purpose | Used In |
|---|---|---|---|
| `id_scheme_account` | INT PK AUTO | Primary key | All payment ops |
| `id_customer` | INT FK | Customer who owns this account | Join, payment |
| `id_scheme` | INT FK | Scheme enrolled in | Eligibility |
| `scheme_acc_number` | VARCHAR | Account number (may be NULL until 1st payment) | Display, reports |
| `active` | TINYINT | 1=Active, 0=Inactive | Eligibility |
| `is_closed` | TINYINT | Closed/matured flag | Eligibility |
| `paid_installments` | INT | Count of successful payments | Due calculation |
| `start_date` | DATETIME | Enrollment date | Age calc |
| `firstPayment_amt` | DECIMAL | First payment amount | Rate fixing |
| `id_branch` | INT FK | Account branch | Branch-wise ops |
| `referal_code` | VARCHAR | Referral code used at join | Referral tracking |
| `is_refferal_by` | TINYINT | 0=Customer, 1=Employee, NULL=None | Referral type |
| `id_agent` | INT FK | Agent who enrolled | Agent tracking |
| `avg_payable` | DECIMAL | Average payable (computed) | Payment amount cap |
| `start_year` | VARCHAR | Financial year | Accounting |
| `custom_entry_date` | DATE | Custom entry date at join | Accounting |

**Risks**:
- `scheme_acc_number` can be NULL for extended periods (until first payment + auto-gen config)
- No soft-delete — `active=0` used but no audit trail

---

### 3. `customer` (Owned by: registration module)

**Purpose**: Customer master data.

| Key Column | Type (Inferred) | Purpose | Used In |
|---|---|---|---|
| `id_customer` | INT PK AUTO | Primary key | Registration, payment |
| `mobile` | VARCHAR | Mobile number (used as login) | Auth, search |
| `email` | VARCHAR | Email | Notifications |
| `firstname` | VARCHAR | First name | Display |
| `lastname` | VARCHAR | Last name | Display |
| `passwd` | VARCHAR | base64(mobile) — NOT encrypted (BUG-017) | Authentication |
| `active` | TINYINT | 1=Active | Registration |
| `id_employee` | INT FK | Allocated employee | Customer assignment |
| `id_agent` | INT FK | Agent who registered | Agent tracking |
| `id_branch` | INT FK | Customer branch | Branch-wise ops |
| `aadharid` | VARCHAR | Aadhaar number | KYC |
| `pan` | VARCHAR | PAN number | KYC |
| `added_by` | TINYINT | 1=Admin, 2=App, 3=Collection | Origin tracking |
| `app_cus_code` | VARCHAR | Khimji offline customer code | Integration |
| `reference_no` | VARCHAR | Khimji customer code | Integration |

---

## Secondary Tables (Read-Heavy / Config)

### 4. `employee` / `employee_devices`

| Table | Key Columns | Purpose in This Module |
|---|---|---|
| `employee` | `id_employee`, `username`, `pwd_hash`, `enable_chit_collection`, `login_branches`, `is_lmx` | Login auth, branch access |
| `employee_devices` | `emp_id`, `device_uuid`, `app_type`, `device_status` | Device authorization — app_type=1 for collection app |

### 5. `scheme`

| Key Column | Purpose in This Module |
|---|---|
| `id_scheme` | Scheme identifier |
| `scheme_type` | 1=Gold, 2=Amount, 3=Flexible — controls calculation |
| `gst`, `gst_type` | GST configuration |
| `total_installments` | Max installments |
| `allow_advance`, `allow_unpaid` | Due type eligibility |
| `free_payment` | Auto first payment on join |
| `flexible_sch_type` | Weight conversion control |
| `one_time_premium`, `rate_fix_by`, `rate_select` | Rate fixing behavior |

### 6. `chit_settings`

| Key Column | Purpose in This Module |
|---|---|
| `currency_symbol` | Display |
| `branch_settings` | Branch-wise operations control |
| `schemeacc_no_set` | Account number generation mode |
| `receipt_no_set` | Receipt number generation mode |
| `auto_pay_approval` | Auto-approval on gateway success |
| `maintenance_mode` | Block all operations |
| `cost_center` | Branch display mode |
| `wallet_account_type` | Wallet creation on registration |

### 7. `inter_wallet_account` / `wallet_transaction`

| Table | Key Columns | Purpose |
|---|---|---|
| `inter_wallet_account` | `id_wallet_account`, `mobile`, `available_points` | Wallet balance |
| `wallet_transaction` | `id_wallet_transaction`, `transaction_type` (0=credit, 1=debit), `value` | Wallet ledger |

### 8. Other Tables

| Table | Read/Write | Used By Methods | Purpose |
|---|---|---|---|
| `metal_rates` | READ | `get_metalrate()` L422, `get_metalrate_by_branch()` L2298 | Live metal rates |
| `branch` | READ | `get_branch()` L1589, `get_all_branch()` L1632 | Branch lookup |
| `agent` | READ | `get_customerByAgent()` L1985, auth | Agent data |
| `postdate_payment` | READ | `pdc_report()` L3249, `get_payment_details()` L440 | PDC tracking |
| `gifts` | READ | `createAccount_post()` L591 | Gift on scheme join |
| `gift_card` | READ+WRITE | `GiftCardPayment` L4265, `mGiftCardPayment` L3916 | Gift card purchase |
| `sms_api_settings` | READ+WRITE | `send_sms()` L140/L3261, `update_otp()` L168/L3284 | SMS quota |
| `scheme_enquiry` | WRITE | `insert_sch_enquiry()` L2264 | Enquiry registration |
| `remarks` | WRITE | `insertRemarks()` L2140 | Pending payment notes |
| `payment_gateway` | READ | `payment_gateway()` L88/L3115 | Gateway credentials |
| `ret_day_closing` | READ | `get_entrydate()` L1651 | Custom entry date ⚠️ raw SQL |
| `payment_mode` | READ | `get_paymenthistory()` L1479 | Payment mode labels |

---

## Schema Risk Summary

| Risk | Table | Description | Bug ID |
|---|---|---|---|
| No duplicate payment guard | `payment` | No unique constraint on (account, month, year) | — |
| NULL metal rate | `payment.metal_rate` | From `file_get_contents` with no validation | BUG-028 |
| base64 password | `customer.passwd` | Trivially decoded | BUG-017 |
| Raw SQL injection | `payment` (via model) | `isValidLogin`, `monthly_agent_reports` | BUG-010, BUG-034 |
| Raw SQL injection | `ret_day_closing` | `get_entrydate()` L1656 — `$id_branch` concat | New finding |
| No FK enforcement | All | CI2 doesn't enforce FK constraints — orphan records possible | — |
| World-writable dirs | Filesystem | `mkdir(0777)` for customer images | BUG-018 |
