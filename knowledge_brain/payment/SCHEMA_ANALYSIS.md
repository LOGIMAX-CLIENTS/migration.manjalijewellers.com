# Payment Module — Schema Analysis
> **Updated**: 2026-03-24 | **Round**: 3

## Part A: Owned Tables

### 1. `payment` (Primary)
- **Purpose**: Stores all payment transactions (manual + online)
- **Engine**: InnoDB (assumed)
- **Key Columns**:

| Column | Type (estimated) | Purpose | Notes |
|---|---|---|---|
| `id_payment` | INT AUTO_INCREMENT | Primary key | — |
| `id_scheme_account` | INT | FK to scheme_account | — |
| `id_transaction` | VARCHAR | Transaction ID (generated) | `uniqid(time())` |
| `date_payment` | DATETIME | Payment date/time | — |
| `custom_entry_date` | DATE | Custom entry date (day close) | Nullable |
| `payment_type` | VARCHAR | Manual / PayU Checkout / etc | — |
| `payment_mode` | VARCHAR | CSH/CC/DC/CHQ/NB/VCH/ADV_ADJ/MULTI/Wallet/REF_WALLET | — |
| `payment_amount` | DECIMAL | Total payment amount | — |
| `act_amount` | DECIMAL | Actual amount (before discount) | — |
| `metal_rate` | DECIMAL | Gold/metal rate at time of payment | — |
| `metal_weight` | DECIMAL(10,3) | Metal weight calculated | — |
| `payment_ref_number` | VARCHAR | Reference number | — |
| `receipt_no` | VARCHAR | Generated receipt number | 7-digit padded |
| `receipt_year` | VARCHAR | Financial year for receipt | — |
| `payment_status` | INT | FK to payment_status_message | 1=Success,2=Awaiting,3=Fail,4=Cancel,6=Refund,7=Pending |
| `id_employee` | INT | Employee who processed | — |
| `id_branch` | INT | Branch where processed | — |
| `id_payGateway` | INT | FK to gateway table | NULL for manual |
| `payu_id` | VARCHAR | PayU transaction ID | — |
| `ref_trans_id` | VARCHAR | Reference transaction ID | For online payments |
| `cheque_no` | VARCHAR | Cheque number | — |
| `cheque_date` | DATE | Cheque date | — |
| `bank_acc_no` | VARCHAR | Bank account number | — |
| `bank_name` | VARCHAR | Bank name | — |
| `bank_branch` | VARCHAR | Bank branch | — |
| `bank_IFSC` | VARCHAR | Bank IFSC code | — |
| `card_no` | VARCHAR | Card number | ⚠️ PCI risk if not masked |
| `card_holder` | VARCHAR | Card holder name | — |
| `cvv` | VARCHAR | CVV | ⚠️ NEVER store CVV — PCI violation |
| `exp_date` | VARCHAR | Card expiry date | ⚠️ PCI risk |
| `id_drawee` | INT | FK to drawee_account | — |
| `id_post_payment` | INT | FK to postdate_payment | If converted from PDC |
| `due_type` | VARCHAR(2) | ND/PD/AD/GA/A/P/S | — |
| `no_of_dues` | INT | Number of dues this covers | — |
| `installment` | INT | Installment number | — |
| `remark` | TEXT | Payment remark | — |
| `added_by` | INT | 0=admin, 1=app, 2=website, 3=gateway | — |
| `is_offline` | INT | Offline payment flag | 0/1 |
| `is_print_taken` | INT | Receipt print flag | — |
| `gst` | DECIMAL | GST percentage | — |
| `gst_type` | INT | 0=Inclusive, 1=Exclusive | — |
| `redeemed_amount` | DECIMAL | Wallet/points redeemed | — |
| `actual_trans_amt` | DECIMAL | Actual transaction amount | — |
| `saved_benefits` | DECIMAL | DigiGold saved benefits (weight) | — |
| `saved_benefit_amt` | DECIMAL | DigiGold saved benefits (amount) | — |
| `dg_other_benefit_wgt` | DECIMAL | DigiGold other benefit weight | — |
| `add_charges` | DECIMAL | Additional charges | — |
| `mer_net_amount` | DECIMAL | Merchant net amount | — |
| `mer_service_fee` | DECIMAL | Merchant service fee | — |
| `igst` | DECIMAL | IGST amount | — |
| `is_settled` | INT | Settlement flag | 0/1 |
| `gateway_requestaction` | VARCHAR | Gateway request action | — |
| `id_agent` | INT | FK to agent | — |
| `is_editing_enabled` | INT | Metal rate edit flag | — |
| `metalrate_edit_date` | DATETIME | When metal rate was edited | — |
| `form_secret` | VARCHAR | CSRF form secret | — |
| `is_point_credited` | INT | Loyalty points credited flag | — |
| `date_upd` | DATETIME | Last update timestamp | — |
| `last_update` | DATETIME | Last update timestamp (duplicate?) | — |
| `date_add` | DATETIME | Created timestamp | — |
| `approval_date` | DATETIME | When approved | — |
| `start_year` | VARCHAR | Start year | — |

### 2. `postdate_payment`
- **Purpose**: Post-dated cheque/payment records
- **Key Columns**: `id_post_payment`, `id_scheme_account`, `pay_mode`, `date_payment`, `cheque_no`, `payee_acc_no`, `payee_bank` (FK to bank), `payee_branch`, `payee_ifsc`, `id_drawee`, `amount`, `metal_rate`, `weight`, `payment_status`, `charges`, `date_presented`, `payment_ref_number`, `remark`

### 3. `payment_status`
- **Purpose**: Payment status change audit log
- **Key Columns**: `id_payment_status`, `id_payment`, `id_post_payment`, `id_status_msg`, `charges`, `id_employee`, `date_upd`

### 4. `payment_mode_details`
- **Purpose**: Multi-mode payment split details
- **Key Columns**: `id_pay_mode_details`, `id_payment`, `payment_amount`, `payment_mode` (CSH/CC/DC/CHQ/NB/ADV_ADJ/VCH), `card_type`, `card_no`, `payment_ref_number`, `id_pay_device`, `cheque_no`, `cheque_date`, `bank_name`, `bank_branch`, `bank_IFSC`, `NB_type`, `net_banking_date`, `id_bank`, `payment_status`, `payment_date`, `is_active`, `created_time`, `created_by`, `updated_time`, `updated_by`, `remark`, `adv_receipt_no`

### 5. `general_advance_payment`
- **Purpose**: General advance payment records
- **Columns**: Similar to `payment` table with `id_adv_payment` as PK, `due_type='GA'`

### 6. `general_advance_mode_detail`
- **Purpose**: Multi-mode splits for general advance
- **Columns**: Similar to `payment_mode_details` with `id_adv_payment` FK

### 7. `payment_status_message`
- **Purpose**: Status code lookup
- **Key Columns**: `id_status_msg`, `payment_status` (label), `color` (CSS color)
- **Values**: 1=Success, 2=Awaiting, 3=Failure, 4=Cancel, 6=Refund, 7=Pending

### 8. `payment_mode`
- **Purpose**: Payment mode master
- **Key Columns**: `id_mode`

### 9. `settlement`
- **Purpose**: Weight settlement records
- **Key Columns**: Standard settlement fields

### 10. `settlement_detail`
- **Purpose**: Settlement line items
- **Key Columns**: Standard settlement detail fields

### 11. `ret_advance_utilized`
- **Purpose**: Track advance receipt utilization in payments
- **Key Columns**: `id_issue_receipt`, `id_payment`/`id_adv_payment`, `adjusted_for` (2=CRM), `utilized_amt`, `cash_utilized_amt`

## Part B: Referenced Tables (owned by other modules)

| Table | Owner | How Used | Join Type |
|---|---|---|---|
| `scheme_account` | Account | FK from payment, account details | LEFT JOIN |
| `customer` | Customer | Customer name/mobile/email | LEFT JOIN |
| `scheme` | Scheme | Scheme rules (type, GST, installments) | LEFT JOIN |
| `scheme_group` | Scheme | Lucky draw group code | LEFT JOIN |
| `employee` | Employee | Employee name/code | LEFT JOIN |
| `agent` | Agent | Agent name/code | LEFT JOIN |
| `branch` | Branch | Branch name/short_name | LEFT JOIN |
| `bank` | Settings | Bank name/short_code | LEFT JOIN |
| `drawee_account` | Settings | Drawee bank details | LEFT JOIN |
| `chit_settings` | Settings | Global config (JOIN without condition!) | CROSS JOIN |
| `metal_rates` | Settings | Current gold rate | Subquery |
| `ret_financial_year` | Settings | Financial year code | Subquery |
| `gateway` | Settings | Payment gateway config | LEFT JOIN |
| `inter_wallet_account` | Wallet | Wallet balance/points | LEFT JOIN |
| `customer_reg` | Customer | Customer registration | — |
| `purchase_customer` | Purchase | Purchase customer reference | — |
| `transaction` | Transaction | Transaction records | — |
| `daily_collection` | Collection | Daily collection | — |

## SQL Injection Risks

⚠️ **Multiple raw concatenations found in model queries**:
- `get_receipt_no()` — `$id_scheme`, `$branch` concatenated directly
- `payment_list()` — `$id` concatenated in WHERE
- `pdc_detail_all()` — `$status` concatenated with quotes
- `get_paymentContent()` — `$id_scheme_account` concatenated
- `get_customer_schemes()` — `$id_customer` concatenated

Most queries use string concatenation instead of parameterized queries. While CodeIgniter's Active Record provides some escaping, raw `$this->db->query()` calls do NOT auto-escape.

## DB Verification Queries

### Q1: Complete payment transaction with all child records
```sql
SELECT p.*, sa.scheme_acc_number, c.firstname, c.mobile, 
       pmd.id_pay_mode_details, pmd.payment_mode as sub_mode, pmd.payment_amount as sub_amount,
       ps.id_status_msg, ps.date_upd as status_date
FROM payment p
LEFT JOIN scheme_account sa ON p.id_scheme_account = sa.id_scheme_account
LEFT JOIN customer c ON sa.id_customer = c.id_customer
LEFT JOIN payment_mode_details pmd ON p.id_payment = pmd.id_payment AND pmd.is_active = 1
LEFT JOIN payment_status ps ON p.id_payment = ps.id_payment
WHERE p.id_payment = {ID}
ORDER BY pmd.id_pay_mode_details, ps.date_upd;
```

### Q2: Check payment amount matches sum of mode details
```sql
SELECT p.id_payment, p.payment_amount, 
       SUM(pmd.payment_amount) as mode_total,
       p.payment_amount - SUM(pmd.payment_amount) as diff
FROM payment p
JOIN payment_mode_details pmd ON p.id_payment = pmd.id_payment AND pmd.is_active = 1
GROUP BY p.id_payment
HAVING ABS(diff) > 0.01;
```

### Q3: Orphan payment_mode_details (no parent payment)
```sql
SELECT pmd.* FROM payment_mode_details pmd
LEFT JOIN payment p ON pmd.id_payment = p.id_payment
WHERE p.id_payment IS NULL;
```

### Q4: Orphan payment_status records
```sql
SELECT ps.* FROM payment_status ps
LEFT JOIN payment p ON ps.id_payment = p.id_payment
WHERE p.id_payment IS NULL AND ps.id_payment IS NOT NULL;
```

### Q5: Payments without mode details (should have at least one)
```sql
SELECT p.id_payment, p.payment_mode, p.payment_amount
FROM payment p
LEFT JOIN payment_mode_details pmd ON p.id_payment = pmd.id_payment AND pmd.is_active = 1
WHERE pmd.id_pay_mode_details IS NULL
AND p.payment_type = 'Manual'
AND p.payment_status = 1;
```
