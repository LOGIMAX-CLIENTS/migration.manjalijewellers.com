# SETTINGS SNAPSHOT — `ret_settings` Table
> **Module:** Retail Settings | **Source:** `retail_staging_20_02_26.sql` | **Captured:** 2026-03-16 | **Total Rows:** 74 (IDs 15–98, gaps = deleted rows)

> [!NOTE]
> This is a **key-value configuration table**. Every other module reads from it by `name` (not by `id`). Values are stored as plain strings. Gaps in ID sequence (IDs 1–14, 19, 62, 65, 82, 89, 91) = deleted rows. AUTO_INCREMENT is at 99.

---

## Table Schema

```sql
CREATE TABLE `ret_settings` (
  `id_ret_settings` int NOT NULL AUTO_INCREMENT,
  `name`            varchar(250) NOT NULL,
  `value`           text,
  `description`     text,
  `created_on`      datetime DEFAULT NULL,
  `created_by`      int unsigned DEFAULT NULL,
  `updated_on`      datetime DEFAULT NULL,
  `updated_by`      int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_ret_settings`),
  UNIQUE KEY `name` (`name`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=latin1;
```

---

## Live Data Snapshot

### 🔵 Weight / Scheme Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 15 | `min_wt_gram` | `0.500` | Minimum Weight in Gram (for scheme/billing threshold) |
| 45 | `weightschemecaltype` | `2` | 1→Manual VA & MC · **2→Highest VA & MC** · 3→Lowest · 4→Average |
| 46 | `weight_scheme_closure_type` | `3` | 1→General · 2→Based on MC & VA · **3→Based on Tag Split** |
| 16 | `per_gram_amt` | `30` | Per Gram Amount |
| 75 | `nontag_weight_per` | `1` | Non-tag weight percentage |

---

### 💳 Billing & Discount Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 17 | `is_counter_req` | `0` | Counter Required For Billing — **disabled** |
| 47 | `bill_discount_type` | `2` | 1→General Discount · **2→Apply in VA & MC** |
| 66 | `bill_split_min_amount` | `195000` | Minimum Bill Split Value amount |
| 67 | `bill_split_max_amount` | `199000` | Maximum Bill Split Value amount |
| 78 | `bill_format` | `1` | 0→multiple tab bill · **1→single tab bill** |
| 86 | `bill_discount_apply_on` | `1` | **1→VA** · 2→MC |
| 70 | `wastage_edit_in_bill` | `1` | Allow wastage edit in billing — **enabled** |
| 71 | `mc_edit_in_bill` | `1` | Allow MC edit in billing — **enabled** |
| 97 | `sell_mc_va_edit_access` | `1` | Sell MC/VA edit access — **1→enabled** |
| 98 | `pur_mc_va_edit_access` | `1` | Purchase MC/VA edit access — **1→enabled** |
| 96 | `disc_blw_metal_rate` | `1` | Discount below metal rate — **enabled** |
| 30 | `on_exchange_in_billing` | `150` | On-exchange value used in billing |

---

### 💰 TCS / TDS / Tax Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 18 | `is_tcs_required` | `1` | TCS GST for Total Bill Amount — **enabled** |
| 20 | `tcs_tax_per` | `0.1` | TCS Tax Percentage = **0.1%** |
| 21 | `tcs_min_bill_amt` | `5000000` | Min purchase amount for B2B TCS = ₹50,00,000 |
| 80 | `tds_purchase_limit` | `5000000` | TDS Purchase Limit = ₹50,00,000 |
| 81 | `tcs_purchase_limit` | `5000000` | TCS Purchase Limit = ₹50,00,000 |
| 83 | `customer_sales_limit` | `516097.06` | Customer Sales Limit |

---

### 💵 Cash / Payment Limits

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 35 | `validate_cash_amt` | `0` | 0→No validation · 1→validate · 2→Do Not Validate |
| 36 | `max_cash_amt` | `200000` | Maximum cash amount payable = ₹2,00,000 |
| 42 | `max_cash_allowed` | `10000` | _(separate limit — context TBD)_ |
| 77 | `max_return_amt` | `9999` | Maximum return amount |
| 32 | `is_credit_enable` | `1` | Credit — **enabled** |

---

### 🔐 OTP / Approval Gates

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 26 | `is_otp_required_for_approval` | `0` | OTP for branch transfer creation — **disabled** |
| 37 | `otp_approval_nos` | `0` | OTP approval number setting |
| 69 | `advance_transfer_otp` | `0` | OTP for advance transfer — **disabled** |
| 85 | `order_delievery_otp` | `1` | OTP for order delivery — **enabled** |
| 87 | `vendor_approval_otp` | `0` | OTP for vendor approval — **disabled** |
| 88 | `stock_issue_otp` | `1` | OTP for stock issue — **enabled** |

---

### 🏷️ Tagging / MC Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 27 | `allow_edit_mc_tag` | `0` | Allow edit MC on tag creation (except MRP) — **disabled** |
| 28 | `allow_edit_mc_estimation` | `0` | Allow edit MC on estimation — **disabled** |
| 29 | `subproduct_required` | `0` | Sub product required — **disabled** |
| 58 | `is_section_required` | `1` | Section required — **1→Yes** |
| 68 | `is_va_mc_based_on_branch` | `0` | Branch Wise VA & MC — **disabled** |

---

### 🛒 Customer Order Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 22 | `customer_due_date` | `15` | Default delivery date from order date (days) |
| 33 | `app_cart_expiry_days` | `30` | Cart expiry — items deleted after 30 days |
| 34 | `app_wishlist_expiry_days` | `30` | Wishlist expiry — deleted if not moved to cart |
| 72 | `customer_order_remark_req` | `1` | Remark required on customer order — **Yes** |
| 73 | `customer_order_duedate_req` | `1` | Due date required on customer order — **Yes** |
| 74 | `customer_order_description_req` | `1` | Description required on customer order — **Yes** |

---

### 👷 Employee / Incentive Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 23 | `emp_sales_incentive_gold_perg` | `4` | Employee incentive per gram (Gold) = ₹4 |
| 24 | `emp_sales_incentive_silver_perg` | `0` | Employee incentive per gram (Silver) = ₹0 |
| 25 | `sales_incentive_green_tag` | `1` | Green tag incentive for employees — **enabled** |
| 61 | `billing_emp_select_req` | `0` | Employee select mandatory in billing — **No** |
| 63 | `est_emp_select_req` | `1` | Employee select mandatory in estimation — **Yes** |
| 90 | `estimation_app_devices_count` | `10` | Max estimation app devices = 10 |

---

### 🪙 Old Metal Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 38 | `min_old_gold_rate` | `3000` | Min old gold rate = ₹3,000 |
| 39 | `max_old_gold_rate` | `10000` | Max old gold rate = ₹10,000 |
| 40 | `min_old_silver_rate` | `50` | Min old silver rate = ₹50 |
| 41 | `max_old_silver_rate` | `150` | Max old silver rate = ₹150 |
| 64 | `est_old_metal_remarks_req` | `0` | Remarks mandatory in estimation (old metal) — **No** |
| 92 | `old_metal_rate_type` | `1` | Old metal rate type = **1** |

---

### 📦 Stock / Inventory / Transfer Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 43 | `allow_eda_button` | `384` | Allow EDA button — value = profile/branch ID 384 |
| 44 | `branch_wise_deposit` | `1` | Branch-wise deposit — **enabled** |
| 48 | `branch_transfer_download` | `1` | **1→Bulk Transfer** · 2→Scan Transfer |
| 79 | `sales_transfer_download` | `1` | Sales transfer download — **enabled** |
| 84 | `is_purchase_cost_from_lot` | `1` | Purchase cost from lot — **1→Yes** |
| 59 | `is_metal_for_billing` | `0` | Metal for billing — **disabled** |

---

### 📊 Stock Movement Analysis (Color Thresholds)

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 49 | `slow_moving_gold` | `120,#E4D00A,0` | Gold slow-moving: after 120 days, yellow (#E4D00A) |
| 50 | `non_moving_gold` | `180,#D22B2B,0` | Gold non-moving: after 180 days, red (#D22B2B) |
| 51 | `fast_moving_gold` | `0,#228B22,0` | Gold fast-moving: 0 days, green (#228B22) |
| 55 | `fast_moving_silver` | `0,#228B22,0` | Silver fast-moving: 0 days, green (#228B22) |
| 56 | `slow_moving_silver` | `120,#E4D00A,0` | Silver slow-moving: after 120 days, yellow |
| 57 | `non_moving_silver` | `180,#D22B2B,0` | Silver non-moving: after 180 days, red |

> **Format:** `days,color_hex,flag` — parsed by the stock analysis report.

---

### 💬 CRM / Sales Analysis Config

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 52 | `pro_price_range` | `Less than 1L, Less than 2L, More than 2L` | Product price range labels |
| 53 | `reasons_for_leaving` | `Looking for latest design, size doesn't fit, VA% are high, Long wait time` | Walk-away reason options |
| 54 | `customer_review_options` | `shopper, Negotiator, Discount Customer, High valued customer` | Customer type labels |

---

### 🔧 Supplier / Purchase Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 60 | `supplier_bill_entry_calc` | `2` | 1→From Supplier Master · **2→Manual** |
| 76 | `is_supplierbill_entry_req` | `1` | Supplier bill entry required — **1→Yes** |
| 84 | `is_purchase_cost_from_lot` | `1` | Refer stock movement section above |

---

### 🔁 Repair / Service Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 31 | `repair_order_per` | `18` | Repair order percentage = **18%** |

---

### 💎 Chit / Scheme / Insurance Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 94 | `insurance_amount` | `10000` | Insurance amount = ₹10,000 |
| 95 | `chit_rate_calculation_type` | `1` | **1→Average rate** · 2→Weight scheme closure type |

---

### ⚙️ System / Misc Settings

| ID | Name | Value | Description / Notes |
|---|---|---|---|
| 93 | `is_direct_bill_required` | `1` | Direct bill required — **enabled** |
| 43 | `allow_eda_button` | `384` | Already listed above under Stock |

---

## Missing / Deleted Row IDs

The following IDs are absent (deleted rows):
`1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 19, 62, 65, 82, 89, 91`

These were likely early-stage settings removed during system evolution. No data recovery needed.

---

## Critical Settings Quick-Reference

> [!IMPORTANT]
> These settings directly affect billing, taxation, and financial calculations across all modules:

| Setting Key | Live Value | Impact |
|---|---|---|
| `is_tcs_required` | `1` | TCS applied to all bills |
| `tcs_tax_per` | `0.1` | TCS rate = 0.1% |
| `tcs_min_bill_amt` | `5000000` | TCS threshold = ₹50L (B2B) |
| `max_cash_amt` | `200000` | Cash payment cap = ₹2L |
| `bill_discount_type` | `2` | Discounts applied in VA & MC |
| `bill_split_min_amount` | `195000` | Bill split kicks in at ₹1.95L |
| `bill_split_max_amount` | `199000` | Bill split max = ₹1.99L |
| `weightschemecaltype` | `2` | Scheme closure: Highest VA & MC |
| `weight_scheme_closure_type` | `3` | Tag Split-based closure |
| `repair_order_per` | `18` | Repair markup = 18% |
| `validate_cash_amt` | `0` | Cash validation currently **OFF** |

---

## Usage Pattern in Code

```php
// Reading a setting (from admin_settings_model.php pattern)
$this->db->where('name', 'is_tcs_required');
$query = $this->db->get('ret_settings');
$row = $query->row();
$value = $row->value; // Always a string — cast as needed

// Bulk read (used in ret_settings_view)
$this->db->order_by('id_ret_settings');
$result = $this->db->get('ret_settings')->result();
// → returned as array of {id, name, value, description} objects
```

> [!WARNING]
> All values are `text` type. Booleans (0/1), floats, and comma-separated lists are all stored as strings. Always cast before comparison.

---

*Source: `retail_staging_20_02_26.sql` dump · Captured: 2026-03-16 · By: Antigravity*
