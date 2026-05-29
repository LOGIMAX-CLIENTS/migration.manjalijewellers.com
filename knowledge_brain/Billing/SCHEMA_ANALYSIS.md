# Billing Module — Schema Analysis

> **Round 1** | Tables owned by and referenced by the Billing module

---

## Part A: Owned Tables (Primary Billing Tables)

### `ret_billing` — Bill Header

| Column                  | Type (est.) | Purpose                    | Notes                                                                            |
| ----------------------- | ----------- | -------------------------- | -------------------------------------------------------------------------------- |
| `bill_id`               | INT PK      | Primary key                | Auto-increment                                                                   |
| `bill_no`               | VARCHAR     | Bill number                | Metal code prefix + sequential                                                   |
| `fin_year_code`         | VARCHAR     | Financial year             | e.g., "2025-26"                                                                  |
| `bill_type`             | INT         | Bill type                  | 1=Normal, 2=Exchange, 3=Combined, 4=Purchase, 5=OrderAdv, 7=Return, 9/15=variant |
| `round_off_amt`         | DECIMAL     | Round-off amount           |                                                                                  |
| `goldrate_22ct`         | DECIMAL     | 22ct gold rate at billing  | Frozen at bill time                                                              |
| `silverrate_1gm`        | DECIMAL     | Silver rate                | Frozen at bill time                                                              |
| `goldrate_14ct`         | DECIMAL     | 14ct gold rate             |                                                                                  |
| `goldrate_9ct`          | DECIMAL     | 9ct gold rate              |                                                                                  |
| `goldrate_18ct`         | DECIMAL     | 18ct gold rate             |                                                                                  |
| `form_secret`           | VARCHAR     | Duplicate prevention token | Session-based                                                                    |
| `metal_type`            | INT FK      | Metal type                 |                                                                                  |
| `pan_no`                | VARCHAR     | Customer PAN               |                                                                                  |
| `aadhar_no`             | VARCHAR     | Customer Aadhaar           |                                                                                  |
| `bill_cus_id`           | INT FK      | Customer ID                | → customer.id_customer                                                           |
| `customer_name`         | VARCHAR     | Customer name              | Denormalized                                                                     |
| `tot_discount`          | DECIMAL     | Total discount             |                                                                                  |
| `tot_bill_amount`       | DECIMAL     | Total bill amount          |                                                                                  |
| `tot_amt_received`      | DECIMAL     | Total amount received      |                                                                                  |
| `is_credit`             | INT         | Credit flag                | 0=No, 1=Yes                                                                      |
| `credit_status`         | INT         | Credit status              | 1=paid, 2=pending                                                                |
| `credit_due_date`       | DATE        | Credit due date            |                                                                                  |
| `bill_date`             | DATETIME    | Bill date                  | From day closing                                                                 |
| `created_time`          | DATETIME    | Creation timestamp         |                                                                                  |
| `created_by`            | INT FK      | Created by user            | → employee                                                                       |
| `counter_id`            | INT         | Counter/POS ID             |                                                                                  |
| `id_branch`             | INT FK      | Branch                     | → branch                                                                         |
| `id_delivery`           | INT         | Delivery ID                |                                                                                  |
| `remark`                | TEXT        | Remarks                    |                                                                                  |
| `credit_disc_amt`       | DECIMAL     | Credit discount            |                                                                                  |
| `billing_for`           | INT         | Billing for type           | 1=Individual, 2=Company                                                          |
| `id_cmp_emp`            | INT FK      | Company employee           |                                                                                  |
| `make_as_advance`       | INT         | Advance flag               |                                                                                  |
| `advance_deposit`       | DECIMAL     | Advance deposit amount     |                                                                                  |
| `tcs_tax_amt`           | DECIMAL     | TCS tax amount             |                                                                                  |
| `tcs_tax_per`           | DECIMAL     | TCS percentage             |                                                                                  |
| `tds_percent`           | DECIMAL     | TDS percentage             |                                                                                  |
| `tds_tax_amt`           | DECIMAL     | TDS tax amount             |                                                                                  |
| `delivered_at`          | INT         | Delivery location          |                                                                                  |
| `delivery_address_type` | INT         | Address type               |                                                                                  |
| `is_eda`                | INT         | EDA flag                   | 1=Normal, 2=EDA/No2                                                              |
| `id_employee`           | INT FK      | Sales employee             |                                                                                  |
| `credit_ret_amt`        | DECIMAL     | Credit return amount       |                                                                                  |
| `credit_due_amt`        | DECIMAL     | Credit due amount          |                                                                                  |
| `is_to_be`              | INT         | To-be flag                 |                                                                                  |
| `eda_tax_calc`          | INT         | EDA tax calc flag          |                                                                                  |
| `is_bill_split`         | INT         | Split flag                 | 0=No, 1=Yes                                                                      |
| `bill_split_ref_id`     | VARCHAR     | Split reference            | Shared across split bills                                                        |
| `is_cancelled`          | INT         | Cancelled flag             | 0=Active, 1=Cancelled                                                            |
| `sales_ref_no`          | VARCHAR     | Sales reference number     | Sequential per branch                                                            |
| `bill_discount_type`    | INT         | Discount type              |                                                                                  |

### `ret_bill_details` — Bill Line Items

| Column                    | Type (est.) | Purpose                 |
| ------------------------- | ----------- | ----------------------- |
| `bill_det_id`             | INT PK      | Primary key             |
| `bill_id`                 | INT FK      | → ret_billing           |
| `esti_item_id`            | INT FK      | → ret_estimation_items  |
| `item_type`               | INT         | Item type               |
| `bill_type`               | INT         | Bill type               |
| `total_cgst`              | DECIMAL     | CGST amount             |
| `total_sgst`              | DECIMAL     | SGST amount             |
| `total_igst`              | DECIMAL     | IGST amount             |
| `product_id`              | INT FK      | → product               |
| `design_id`               | INT FK      | → product_design        |
| `id_sub_design`           | INT FK      | → sub_design            |
| `tag_id`                  | INT FK      | → ret_taging            |
| `quantity`                | INT         | Quantity                |
| `purity`                  | INT FK      | → purity                |
| `size`                    | VARCHAR     | Size                    |
| `uom`                     | INT FK      | → UOM                   |
| `piece`                   | INT         | Pieces                  |
| `less_wt`                 | DECIMAL     | Less weight (deduction) |
| `net_wt`                  | DECIMAL     | Net weight              |
| `gross_wt`                | DECIMAL     | Gross weight            |
| `calculation_based_on`    | INT         | Calc type               |
| `wastage_percent`         | DECIMAL     | Wastage %               |
| `mc_value`                | DECIMAL     | Making charges          |
| `mc_type`                 | INT         | MC type                 |
| `item_cost`               | DECIMAL     | Item cost before tax    |
| `item_total_tax`          | DECIMAL     | Total tax               |
| `tax_group_id`            | INT FK      | → tax_group             |
| `bill_discount`           | DECIMAL     | Item discount           |
| `rate_per_grm`            | DECIMAL     | Rate per gram           |
| `is_partial_sale`         | INT         | Partial sale flag       |
| `mc_discount`             | DECIMAL     | MC discount             |
| `wastage_discount`        | DECIMAL     | Wastage discount        |
| `item_blc_discount`       | DECIMAL     | Balance discount        |
| `id_orderdetails`         | INT FK      | → order_details         |
| `round_of_amt`            | DECIMAL     | Round-off per item      |
| `id_section`              | INT FK      | → section               |
| `item_emp_id`             | INT FK      | → employee              |
| `is_non_tag`              | INT         | Non-tag flag            |
| `id_collecion_maping_det` | INT         | Collection mapping      |

### `ret_billing_payment` — Payment Records

| Column               | Type (est.) | Purpose               |
| -------------------- | ----------- | --------------------- |
| `bill_id`            | INT FK      | → ret_billing         |
| `payment_amount`     | DECIMAL     | Amount paid           |
| `payment_mode`       | VARCHAR     | Cash/CC/DC/CHQ/NB     |
| `id_pay_device`      | INT FK      | POS device            |
| `card_type`          | VARCHAR     | Card type             |
| `card_no`            | VARCHAR     | Card last 4 digits    |
| `payment_ref_number` | VARCHAR     | Reference/approval no |
| `NB_type`            | VARCHAR     | Net banking type      |
| `id_bank`            | INT FK      | → bank                |
| `cheque_no`          | VARCHAR     | Cheque number         |
| `cheque_date`        | DATE        | Cheque date           |
| `type`               | INT         | 1=billing payment     |
| `payment_for`        | INT         | Payment for type      |
| `payment_status`     | INT         | 1=active              |
| `payment_date`       | DATETIME    | Payment date          |

### `ret_billing_item_stones` — Stones per Bill Item

| Column            | Type (est.) | Purpose              |
| ----------------- | ----------- | -------------------- |
| `bill_id`         | INT FK      | → ret_billing        |
| `bill_det_id`     | INT FK      | → ret_bill_details   |
| `pieces`          | INT         | Stone pieces         |
| `wt`              | DECIMAL     | Stone weight         |
| `stone_id`        | INT FK      | → stone type         |
| `price`           | DECIMAL     | Stone price          |
| `uom_id`          | INT FK      | → UOM                |
| `item_type`       | INT         | 1=sale, others       |
| `is_apply_in_lwt` | INT         | Apply in less weight |
| `stone_cal_type`  | INT         | Calculation type     |
| `rate_per_gram`   | DECIMAL     | Rate per gram        |

### `ret_bill_other_metals` — Other Metals in Bill Items

| Column                     | Type (est.) | Purpose            |
| -------------------------- | ----------- | ------------------ |
| `bill_det_id`              | INT FK      | → ret_bill_details |
| `tag_other_itm_metal_id`   | INT         | Metal ID           |
| `tag_other_itm_pur_id`     | INT         | Purity ID          |
| `tag_other_itm_grs_weight` | DECIMAL     | Gross weight       |
| `tag_other_itm_wastage`    | DECIMAL     | Wastage            |
| `tag_other_itm_uom`        | INT         | UOM                |
| `tag_other_itm_cal_type`   | INT         | Calc type          |
| `tag_other_itm_mc`         | DECIMAL     | Making charges     |
| `tag_other_itm_rate`       | DECIMAL     | Rate               |
| `tag_other_itm_pcs`        | INT         | Pieces             |
| `tag_other_itm_amount`     | DECIMAL     | Amount             |

### `ret_service_bill` — Service Bill Header

| Column                  | Type (est.) | Purpose                     |
| ----------------------- | ----------- | --------------------------- |
| `id_service_bill`       | INT PK      | Primary Key                 |
| `bill_no`               | VARCHAR     | Service bill receipt number |
| `fin_year_code`         | VARCHAR     | Financial year context      |
| `id_customer`           | INT FK      | → customer                  |
| `id_branch`             | INT FK      | → branch                    |
| `total_bill_amount`     | DECIMAL     | Total cost of the service   |
| `total_amount_received` | DECIMAL     | Amount paid by customer     |
| `bill_status`           | INT         | 1=Success, 2=Cancelled      |
| `bill_date`             | DATETIME    | Billing timestamp           |

### `ret_service_bill_details` — Service Bill Items

| Column            | Type (est.) | Purpose             |
| ----------------- | ----------- | ------------------- |
| `id_service_bill` | INT FK      | Header ID           |
| `id_service`      | INT FK      | Service type/code   |
| `id_product`      | INT FK      | Target product      |
| `piece`           | INT         | Number of pieces    |
| `weight`          | DECIMAL     | Gross weight        |
| `item_total_tax`  | DECIMAL     | Total tax amount    |
| `item_total_cost` | DECIMAL     | Final cost for item |

### `ret_service_bill_payment` — Service Bill Payment Log

| Column            | Type (est.) | Purpose                    |
| ----------------- | ----------- | -------------------------- |
| `id_service_bill` | INT FK      | Header ID                  |
| `payment_amount`  | DECIMAL     | Amount paid                |
| `payment_mode`    | VARCHAR     | Cash, CC, DC, CHQ, NB      |
| `type`            | INT         | Transaction type indicator |

### `ret_cash_collection` — Cash Collection Header

| Column            | Type (est.) | Purpose                   |
| ----------------- | ----------- | ------------------------- |
| `id`              | INT PK      | Primary Key               |
| `branch_id`       | INT FK      | Originating branch        |
| `counter_id`      | INT FK      | Target counter            |
| `date`            | DATE        | Collection Date           |
| `cash_type`       | INT         | Collection categorization |
| `opening_balance` | DECIMAL     | Hand opening              |
| `cash_on_hand`    | DECIMAL     | Declared sum              |
| `sales_amount`    | DECIMAL     | Expected sales collected  |
| `total_amount`    | DECIMAL     | Final total               |

### `ret_cash_collection_details` — Cash Collection Denominations

| Column               | Type (est.) | Purpose                         |
| -------------------- | ----------- | ------------------------------- |
| `cash_collection_id` | INT FK      | → ret_cash_collection           |
| `denomination_id`    | INT FK      | Currency denomination value id  |
| `value`              | INT         | Number of notes                 |
| `amount`             | DECIMAL     | `denomination.value` \* `value` |

---

## Part B: Referenced Tables (Read by Billing)

| Table                   | Module     | How Used in Billing         |
| ----------------------- | ---------- | --------------------------- |
| `ret_estimation`        | Estimation | Estimation header lookup    |
| `ret_estimation_items`  | Estimation | Estimation line items       |
| `ret_taging`            | Tagging    | Tag availability, details   |
| `ret_taging_status_log` | Tagging    | Tag movement history        |
| `ret_taging_stones`     | Tagging    | Stone details from tags     |
| `customer`              | Customer   | Customer details            |
| `branch`                | Settings   | Branch details              |
| `metal_types`           | Catalog    | Metal type master           |
| `branch_metal_rate`     | Settings   | Branch-specific metal rates |
| `tax_group`             | Tax        | Tax group master            |
| `tax_group_items`       | Tax        | Tax rates                   |
| `product`               | Catalog    | Product master              |
| `product_design`        | Catalog    | Design master               |
| `purity`                | Catalog    | Purity master               |
| `uom`                   | Catalog    | Unit of measure             |
| `ret_size`              | Catalog    | Size master                 |
| `employee`              | HR         | Employee details            |
| `bank_accounts`         | Accounts   | Bank accounts               |
| `payment_devices`       | Settings   | POS device master           |
| `gift_voucher`          | Vouchers   | Gift voucher validation     |
| `ret_settings`          | Settings   | Retail module settings      |
| `financial_year`        | Settings   | Financial year              |
| `day_closing`           | Settings   | Day closing status          |
| `ret_otp_approval`      | OTP        | OTP records                 |
| `customer_order`        | Orders     | Customer orders             |
| `denomination`          | Settings   | Cash denominations          |
| `ret_journal`           | Accounts   | Journal entries             |
| `ret_non_tag_item`      | Inventory  | Non-tagged stock            |
| `delivery_address`      | Customer   | Delivery addresses          |
| `ret_payment_modes`     | Settings   | Payment mode master         |
| `company`               | Settings   | Company master              |
| `ret_billing_format`    | Billing    | Bill number format config   |
| `ret_ledger`            | Accounts   | Ledger master               |
| `ledger_master`         | Accounts   | Ledger accounts (balance, transfer) |

---

## Part C: POS Integration Tables (New in Round 8)

> 3 new tables introduced by the POS multi-provider integration feature.

### `ret_pos_providers` — POS Provider Configuration

| Column               | Type (est.) | Purpose                                      | Notes                              |
| -------------------- | ----------- | -------------------------------------------- | ---------------------------------- |
| `id_provider`        | INT PK      | Primary key                                  |                                    |
| `provider_code`      | VARCHAR     | Code used to identify provider               | e.g., 'phonepe', 'pinelabs'        |
| `provider_name`      | VARCHAR     | Display name                                 |                                    |
| `auth_type`          | VARCHAR     | Authentication type                          |                                    |
| `api_url_uat_init`   | VARCHAR     | UAT endpoint for init/trigger payment        |                                    |
| `api_url_uat_status` | VARCHAR     | UAT endpoint for payment status check        |                                    |
| `api_url_uat_cancel` | VARCHAR     | UAT endpoint for payment cancellation        |                                    |
| `api_url_live_init`  | VARCHAR     | Live endpoint for init/trigger payment       |                                    |
| `api_url_live_status`| VARCHAR     | Live endpoint for status check               |                                    |
| `api_url_live_cancel`| VARCHAR     | Live endpoint for cancellation               |                                    |
| `is_env_live`        | TINYINT     | 0=UAT/Sandbox, 1=Live                        | ⚠️ Toggled by `toggleProviderEnv()` |
| `has_qr_display`     | TINYINT     | Whether provider supports QR display         |                                    |
| `has_callback`       | TINYINT     | Whether provider uses webhook callback       |                                    |
| `status_method`      | VARCHAR     | How to check status (polling vs callback)    |                                    |

### `ret_pos_device_list` — POS Device Registry

| Column          | Type (est.) | Purpose                                | Notes                                     |
| --------------- | ----------- | -------------------------------------- | ----------------------------------------- |
| `id_device`     | INT PK      | Primary key                            |                                           |
| `dispname`      | VARCHAR     | Display name for device                |                                           |
| `poscode`       | VARCHAR     | POS machine code                       |                                           |
| `merchantid`    | VARCHAR     | Merchant ID (used to link transactions)|                                           |
| `securitytoken` | VARCHAR     | Security token for API auth            | Sensitive — not to be logged or exposed   |
| `salt_key`      | VARCHAR     | Salt key (used by PhonePe callback)    | Sensitive — verified in `getPhonePeSaltKey()` |
| `imei`          | VARCHAR     | Device IMEI                            |                                           |
| `is_default`    | TINYINT     | 1 = default device for billing         | Only one device should have `is_default=1` |
| `id_provider`   | INT FK      | → ret_pos_providers                    |                                           |
| `devicetype`    | VARCHAR     | Device type classification             |                                           |
| `is_active`     | TINYINT     | 1=active, 0=soft-deleted               | `deletePOSDevice()` sets to 0             |

### `ret_pos_requests` — POS Transaction Log

| Column                  | Type (est.) | Purpose                                | Notes                                          |
| ----------------------- | ----------- | -------------------------------------- | ---------------------------------------------- |
| `pos_req_id`            | INT PK      | Primary key                            |                                                |
| `pos_trans_no`          | VARCHAR     | Internal transaction number            |                                                |
| `pos_req_amount`        | DECIMAL     | Amount in paise (×100 for rupees)      | ⚠️ Verify paise vs rupee consistency            |
| `pos_req_status`        | TINYINT     | 0=INIT/PENDING, 1=SUCCESS, 2=CANCELLED, 3=FAILED | Auto-expired to 3 after 30 min |
| `pos_res_ref_id`        | VARCHAR     | Provider reference ID from response    | Used for cancel/status lookup                  |
| `pos_req_bill_id`       | INT FK      | → ret_billing                          |                                                |
| `pos_req_bill_cusid`    | INT FK      | → customer                             |                                                |
| `pos_req_createdby`     | INT FK      | → employee                             |                                                |
| `pos_req_created_at`    | DATETIME    | Creation timestamp (field name varies) | Use `_getPOSDateColumn()` to detect column     |
| `id_provider`           | INT FK      | → ret_pos_providers                    |                                                |
| `pos_mer_id`            | VARCHAR     | Merchant ID (links to device)          | Joins to `ret_pos_device_list.merchantid`      |

### `ret_ledger_transfer` — Bank/Ledger Transfer Records

| Column           | Type (est.) | Purpose                        | Notes                                   |
| ---------------- | ----------- | ------------------------------ | --------------------------------------- |
| `id`             | INT PK      | Primary key                    |                                         |
| `from_ledger_id` | INT FK      | → ledger_master                | Source ledger account                   |
| `to_ledger_id`   | INT FK      | → ledger_master                | Destination ledger                      |
| `transfer_date`  | DATETIME    | Date of transfer               |                                         |
| (other columns)  | —           | Amount, reference, branch, etc | Inferred from ledger_transfer_form.php  |
