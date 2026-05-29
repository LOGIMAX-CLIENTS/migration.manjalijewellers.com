# JAVASCRIPT MAP — admin_settings.js
> **Module:** Retail Settings | **Round:** 4 | **Date:** 2026-03-16  
> **File:** `admin/assets/js/admin_settings.js` | **Size:** 270 KB | **Lines:** 6331

---

## §1 — Page Router (ctrl_page Switch on L700-963)

The JS uses a URL-path switch to load the right DataTable or functionality:

| ctrl_page[1] | Init Function Called | Notes |
|---|---|---|
| `general` | Display format handlers + limit_settings checkboxes + clear_database modal | L10-883 |
| `rate/list` | `load_metalrate_list()` | Metal rate history |
| `rate/add` | Input focus handlers | Rate entry form |
| `ledger` | `.select2()` initialization | Ledger form |
| `menu` | `set_menu_table()`, `load_parent_menu()` | Menu management |
| `payment_charges` | `set_charges_table()` | Payment charge rates |
| `paymode` | `set_paymode_table()` | Payment mode CRUD |
| `profile` | `set_profile_table()` | Profile CRUD |
| `weight` | `set_weight_table()` | Weight master |
| `cardbrand` | `set_cardbrand_table()` | Card brand CRUD |
| `classification` | `set_classification_table()` | Classification CRUD |
| `import/list` | `get_import_list()` | Import data |
| `import/customer_list` | `get_customer_list()` | Customer import |
| `profession` | `set_profession_table()` | Profession CRUD |
| `access` | `set_permission_view()` | Permission matrix |
| `bank` | `set_bank_table()` | Bank CRUD |
| `notification` | `set_notification_table()` | Notification management |
| `gift` | `get_all_gifts()`, `set_gift_table()` | Gift vouchers |
| `dept` | `set_dept_table()` | Department CRUD |
| `offers` | `set_offers_table()` | Offer management |
| `new_arrivals` | `set_new_arrivals_table()` | New arrivals |
| `design` | `set_design_table()` | Design master |
| `drawee/list` | `set_drawee_table()` | Drawee list |
| `drawee/add`, `drawee/edit` | `load_bank()` | Bank dropdown |
| `version` | `set_version_table()` | Version master |
| `village` | `get_village_list()` | Village master |
| `terms_and_conditions` | `get_terms_and_conditions()`, CKEditor | T&C content |
| `retail_setting` | `set_retail_settings_list()` | ret_settings CRUD list |
| `payment` | `get_offrate_list()`, `set_paymentgateway_list()`, `set_upi_paymentgateway_list()` | Payment settings |
| `module` | `set_modules_list()` | Module enable/disable |

---

## §2 — AJAX Endpoint Inventory

| JS Function | HTTP | URL | Controller Method | Notes |
|---|---|---|---|---|
| `get_country()` | GET | `settings/company/getcountry` | `admin_settings::get_city('country')` | Country cascade |
| `get_countryCurr()` | GET | `settings/company/getcountry` | Same | Currency country |
| `get_state($id)` | POST | `settings/company/getstate/` | `admin_settings::get_city('state', $id)` | State cascade |
| `get_city($id)` | POST | `settings/company/getcity` | `admin_settings::get_city('city', $id)` | City cascade |
| `add_weight()` | POST | `settings/weight/add` | `admin_settings::weight_form('Save')` | Weight add |
| `get_weight(id)` | GET/POST | `settings/weight/edit/{id}` | `admin_settings::weight_form('Edit', $id)` | Weight edit |
| `update_weight()` | POST | `settings/weight/update` | `admin_settings::weight_form('Update')` | Weight update |
| `add_classification()` | POST (FormData) | `settings/classification/add` | `admin_settings::classification_form('Save')` | With image upload |
| `get_classification(id)` | — | `settings/classification/edit/{id}` | — | Classification edit |
| `update_classification()` | POST (FormData) | `settings/classification/update/{id}` | — | With image upload |
| `load_bank()` | GET | `settings/bank/ajax_list` | `admin_settings::bank()` | Bank dropdown |
| `set_drawee_table()` | POST | `settings/drawee/ajax` | `admin_settings::drawee()` | Drawee DataTable |
| `set_paymode_table()` | POST | `settings/paymode/ajax` | `admin_settings::payment_mode()` | Paymode DataTable |
| `set_profile_table()` | POST | `settings/profile/ajax_list` | `admin_settings::profile()` | Profile DataTable |
| `set_menu_table()` | — | Serverside DataTable | `settings/menu/ajax` | Menu DataTable |
| `load_parent_menu()` | — | Parent menu dropdown | — | Menu hierarchy |
| `set_bank_table()` | POST | `settings/bank/ajax` | `admin_settings::bank()` | Bank DataTable |
| `set_notification_table()` | — | `settings/notification/ajax` | — | Notification DataTable |
| `set_classification_table()` | — | `settings/classification/ajax` | — | Classification DataTable |
| `set_dept_table()` | — | `settings/dept/ajax` | — | Dept DataTable |
| `set_design_table()` | — | `settings/design/ajax` | — | Design DataTable |
| `set_weight_table()` | — | `settings/weight/ajax` | — | Weight DataTable |
| `set_cardbrand_table()` | — | `settings/cardbrand/ajax` | — | Card brand DataTable |
| `set_offers_table()` | — | `settings/offers/ajax` | — | Offers DataTable |
| `set_new_arrivals_table()` | — | `settings/new_arrivals/ajax` | — | New arrivals DataTable |
| `get_all_gifts()` | — | `settings/gift/ajax` | — | Gift list |
| `set_gift_table()` | — | `settings/gift/list` | — | Gift DataTable |
| `set_charges_table()` | — | `settings/payment_charges/ajax` | — | Charges DataTable |
| `get_village_list()` | — | `settings/village/ajax` | — | Village DataTable |
| `get_terms_and_conditions()` | — | `settings/terms_and_conditions/ajax` | — | T&C AJAX |
| `set_retail_settings_list()` | — | `settings/retail_setting/ajax` _(via usersms)_ | `admin_usersms::ret_settings_form()` default | ret_settings DataTable |
| `set_modules_list()` | — | `settings/module/ajax` | `admin_usersms::catlog_module_form()` default | Module list |
| `set_paymentgateway_list()` | — | `settings/payment/gateway/ajax` | — | Gateway list |
| `set_upi_paymentgateway_list()` | — | `settings/payment/upi/ajax` | — | UPI gateway list |
| `load_metalrate_list()` | — | `settings/rate/list/ajax` | `admin_settings::metal_rates()` | Rate history |
| `get_offrate_list()` | POST | `settings/payment/offrate/list` | `admin_settings::gateway_form()` | Offline rate |
| `get_import_list()` | — | `settings/import/list` | — | Import list |
| `get_customer_list()` | — | `settings/import/customer_list` | — | Customer import list |
| `set_permission_view()` | — | `settings/access` | `admin_settings::permission()` | Permission matrix |
| `set_profession_table()` | — | `settings/profession/ajax` | `admin_settings::profession_form()` | Profession DataTable |
| `set_version_table(from, to)` | — | `settings/version/ajax` | `admin_settings::version_form()` | Version DataTable |
| Clear DB confirm | POST | `settings/clear/database` | `admin_settings::clear_database()` | **CRITICAL — no OTP** |
| Load DB backups | POST | `settings/general/edit` DB backup tab | `admin_settings::database_backup()` | Backup log |
| `__set_export_view` → export | POST | `settings/export_to_excel` | `admin_settings::export()` | Export payment data |
| `sendSMS`, `sendEmail` | POST | `settings/import/send_login`, `settings/import/send_login_email` | — | Bulk import login SMS |
| `#countryCurr` change | GET | `settings/country/getcurrency/{id}` | — | Currency lookup |

---

## §3 — Key UI Conditionals / Dependent Fields

| Field | Depends On | Logic |
|---|---|---|
| `#goldDiscAmt` | `enableGoldrateDisc` checkbox | Disabled unless checked |
| `#goldDiscAmt_18k` | `enableGoldrateDisc_18k` checkbox | Disabled unless checked |
| `#payOTP_exp` | `isOTPRegForPayment` checkbox | Disabled unless checked |
| `#loginOTP_exp` | `isOTPReqToLogin` checkbox | Disabled unless checked |
| `#giftOTP_exp` | `isOTPReqToGift` checkbox | Disabled unless checked |
| `#cust_max_count` | `#limit_cust` checkbox | Disabled unless checked |
| `#sch_max_count` | `#limit_sch` checkbox | Disabled unless checked |
| `#branch_max_count` | `#limit_branch` checkbox | Disabled unless checked |
| `#sch_acc_max_count` | `#limit_sch_acc` checkbox | Disabled unless checked |
| `#sch_benefit`, `#walllet_benefit` | `allow_referral` radio | Disabled unless 1 |
| `#wallet_points`, `#wallet_amt_per_points` | `wallet_balance_type` radio | Disabled unless 1 |
| `#credit_sms` | `enable_otpsms` checkbox | OTP SMS credit toggle |
| `#create_promotion` | `enable_promot` checkbox | Promotion credit toggle |
| `#smtp_pass`, `#smtp_host`, `#smtp_user` | `server_type` radio | Disabled unless SMTP |
| `.sel_block` | `clear_data` radio in Tab 5 | Enabled only in selective mode |
| `#partial_goldrate`, `#partial_silverate` | `metal_type` radio | Enabled only for partial rate type |
| `#entry_date` | `edit_custom_entry_date` checkbox | Disabled unless custom date enabled |
| `#ac_num_block` | `schemeaccNo_displayFrmt` radio (=2) | Show/hide account number format options |
| `#rcpt_num_block` | `receiptNo_displayFrmt` radio (=2) | Show/hide receipt number format options |
| Social media URLs | `.social-toggle` switches | URL inputs shown/hidden per toggle |

---

## §4 — New Bugs Found In JS (Round 4)

| # | Line | Bug | Severity |
|---|---|---|---|
| B-012 | L843 | `clear_database` AJAX POST called directly on modal confirm — no CSRF token, no server-side OTP | 🔴 Critical |
| B-013 | L753 | `#new_arrivals_submit` validation uses wrong operator precedence — always shows alert even when valid | 🟠 Medium |
| B-014 | L1125 | `alert(from_date + " to " + to_date)` — debug alert left in production code | 🟡 Low |
| B-015 | L967 | `excel_format(0)` and `customer_excel_format(0)` called unconditionally on every settings page | 🟡 Low |

---

*Source: admin/assets/js/admin_settings.js | Round 4 full AJAX map | 2026-03-16*
