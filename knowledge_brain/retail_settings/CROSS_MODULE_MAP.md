# CROSS-MODULE MAP — Retail Settings
> **Module:** Retail Settings | **Round:** 3 | **Date:** 2026-03-16

> [!IMPORTANT]
> The Settings module is the **single configuration source** for the entire system. Every other module reads `ret_settings` and `chit_settings`. Changes here propagate to all modules instantly.

---

## §1 — ret_settings Consumers (Cross-Module Reads)

| Setting Key | Read By Module(s) | How Used |
|---|---|---|
| `is_tcs_required` | Billing, Reports | Toggle TCS calculation on bill |
| `tcs_tax_per` | Billing | TCS rate applied to bill total |
| `tcs_min_bill_amt` | Billing | Threshold to trigger TCS for B2B |
| `tcs_purchase_limit` | Purchase | TCS threshold for purchase bills |
| `tds_purchase_limit` | Purchase | TDS threshold for purchase bills |
| `max_cash_amt` | Billing | Cash payment limit enforcement |
| `validate_cash_amt` | Billing | Whether to enforce cash limit |
| `bill_discount_type` | Billing | How discount is applied (VA/MC) |
| `bill_discount_apply_on` | Billing | Which component discount applies to |
| `bill_split_min_amount` | Billing | When to trigger bill split |
| `bill_split_max_amount` | Billing | Upper limit for bill split |
| `bill_format` | Billing | Single/multi-tab bill layout |
| `is_section_required` | Billing, Tagging | Mandatory section selection |
| `is_counter_req` | Billing | Counter selection mandatory |
| `billing_emp_select_req` | Billing | Employee selection mandatory |
| `wastage_edit_in_bill` | Billing | Can user edit wastage in bill |
| `mc_edit_in_bill` | Billing | Can user edit MC in bill |
| `sell_mc_va_edit_access` | Billing | Sales MC/VA edit permissions |
| `pur_mc_va_edit_access` | Purchase | Purchase MC/VA edit permissions |
| `disc_blw_metal_rate` | Billing | Allow discount below metal rate |
| `is_credit_enable` | Billing | Credit payment enable/disable |
| `max_return_amt` | Billing, Returns | Max amount for returns |
| `repair_order_per` | Repair Orders | Repair markup percentage |
| `is_supplierbill_entry_req` | Purchase | Supplier bill entry required |
| `supplier_bill_entry_calc` | Purchase | Supplier bill calculation method |
| `is_purchase_cost_from_lot` | Purchase | Lot-based cost calculation |
| `allow_edit_mc_tag` | Tagging | Allow MC edit on tag |
| `allow_edit_mc_estimation` | Estimation | Allow MC edit in estimation |
| `est_emp_select_req` | Estimation | Employee mandatory in estimation |
| `est_old_metal_remarks_req` | Estimation | Remarks mandatory (old metal) |
| `subproduct_required` | Catalog | Sub-product required |
| `is_section_required` | all modules | Section mandatory |
| `weightschemecaltype` | Scheme Closure | Weight scheme VA/MC method |
| `weight_scheme_closure_type` | Scheme Closure | Closure basis |
| `branch_transfer_download` | Branch Transfer | Bulk vs scan transfer |
| `sales_transfer_download` | Sales Transfer | Download method |
| `is_va_mc_based_on_branch` | Billing, Catalog | Branch-wise VA & MC |
| `is_metal_for_billing` | Billing | Metal billing enabled |
| `on_exchange_in_billing` | Billing | Exchange value in billing |
| `per_gram_amt` | Reports/Scheme | Per gram amount base |
| `min_wt_gram` | Billing/Scheme | Minimum weight threshold |
| `customer_due_date` | Customer Orders | Default delivery date offset |
| `app_cart_expiry_days` | App/E-commerce | Cart item expiry |
| `app_wishlist_expiry_days` | App/E-commerce | Wishlist item expiry |
| `customer_order_remark_req` | Customer Orders | Remarks mandatory |
| `customer_order_duedate_req` | Customer Orders | Due date mandatory |
| `customer_order_description_req` | Customer Orders | Description mandatory |
| `order_delievery_otp` | Customer Orders | OTP on order delivery |
| `vendor_approval_otp` | Vendor/Purchase | OTP for vendor approval |
| `stock_issue_otp` | Stock | OTP for stock issue |
| `advance_transfer_otp` | Advance | OTP for advance transfer |
| `is_otp_required_for_approval` | Branch Transfer | OTP for BT creation |
| `otp_approval_nos` | Various OTP flows | OTP approval count |
| `min_old_gold_rate` | Old Metal | Min acceptable gold rate |
| `max_old_gold_rate` | Old Metal | Max acceptable gold rate |
| `min_old_silver_rate` | Old Metal | Min silver rate |
| `max_old_silver_rate` | Old Metal | Max silver rate |
| `old_metal_rate_type` | Old Metal | Rate type enum |
| `slow_moving_gold` | Stock Reports | Color threshold + days |
| `non_moving_gold` | Stock Reports | Color threshold + days |
| `fast_moving_gold` | Stock Reports | Color threshold + days |
| `slow_moving_silver` | Stock Reports | Color threshold + days |
| `non_moving_silver` | Stock Reports | Color threshold + days |
| `fast_moving_silver` | Stock Reports | Color threshold + days |
| `nontag_weight_per` | Non-tag Items | Weight % calculation |
| `branch_wise_deposit` | Deposits | Branch-separated deposits |
| `allow_eda_button` | EDA | Profile/branch that can see EDA |
| `max_cash_allowed` | Various | Separate cash cap |
| `pro_price_range` | CRM/Reports | Product price labels |
| `reasons_for_leaving` | CRM | Walk-away reason dropdown |
| `customer_review_options` | CRM | Customer type labels |
| `customer_sales_limit` | Customer/Reports | Customer spending cap |
| `emp_sales_incentive_gold_perg` | Incentives | Gold incentive/gram |
| `emp_sales_incentive_silver_perg` | Incentives | Silver incentive/gram |
| `sales_incentive_green_tag` | Incentives | Green tag incentive toggle |
| `estimation_app_devices_count` | Estimation App | Device count limit |
| `insurance_amount` | Insurance | Base insurance value |
| `chit_rate_calculation_type` | Scheme/Chit | Rate calc method |
| `is_direct_bill_required` | Billing | Direct bill mandatory |
| `bill_split_min_amount` / `max` | Billing | Split thresholds |

---

## §2 — Module → Settings Write Map

| Module | Writes To | Via |
|---|---|---|
| **Settings (admin_usersms)** | `ret_settings` | `ret_settings_post()` → `retail_settingsDB('update')` |
| **Settings (admin_settings)** | `chit_settings` | `general_settings()` → `settingsDB('update')` |
| **Settings (admin_settings)** | `metal_rates` | `metal_rates()` → `metal_ratesDB('insert'/'update')` |
| **Settings (admin_settings)** | `../api/rate.txt` | `update_rate_file()` |
| **Settings (admin_settings)** | `branch_rate` | Metal rate save |
| **Settings (admin_settings)** | `sms_api_settings` | `sms_api_settings()` |
| **Settings (admin_settings)** | `gateway` / `payment_gateway` | `gateway_form()` |
| **Settings (admin_settings)** | `payment_mode` | `payment_mode()` → `paymodeDB()` |
| **Settings (admin_settings)** | `profile` | `profile()` → `profileDB()` |
| **Settings (admin_settings)** | `access`, `dashboard_access` | `permission()` |
| **Settings (admin_settings)** | `menu` | `menu()` → `menuDB()` |

---

## §3 — Direct Dependencies (Settings Reads Other Modules)

| Settings Gets Data From | Purpose |
|---|---|
| `customer` | Mobile duplicate check, customer lookups |
| `ret_saving_scheme` | Scheme dropdowns in SMS config |
| `scheme_account` | Scheme account lookups |
| `services` | SMS service toggle states |
| `chit_settings` | Most settings reads (not ret_settings) |
| `branch` | Branch name/data for various forms |
| `company` | Company profile save |
| `ret_noticeboard` | Noticeboard display in header |

---

## §4 — Cross-Module Risk Points

| Risk | Severity | Modules Affected |
|---|---|---|
| `validate_cash_amt = 0` → cash limit NOT enforced | 🔴 Critical | Billing |
| `ret_settings` UPDATE uses `WHERE name=?` → renaming a key breaks all consumers | 🔴 Critical | All modules |
| `clear_database()` reachable without OTP | 🔴 Critical | All modules |
| Metal rate file write uses PHP array format, not JSON | 🟠 Medium | API consumers |
| `chit_settings.is_branchwise_rate` vs `ret_settings` — dual config for branch rates | 🟠 Medium | Branch Transfer, Billing |
| `bill_split_min_amount` very close to `max_cash_amt` → easy to create compliance gap | 🟡 Low | Billing |

---

*Generated from: admin_settings.php + admin_usersms.php + admin_settings_model.php scan | Round 3 | 2026-03-16*
