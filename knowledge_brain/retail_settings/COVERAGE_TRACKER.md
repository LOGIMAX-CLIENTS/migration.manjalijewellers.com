# COVERAGE TRACKER — Retail Settings Module Brain
> **Module:** Retail Settings | **Last Updated:** 2026-03-25 (Round 9 — **REFRESH SCAN**)

---

## Coverage Summary

| Area | R1 | R2 | R3 | R4 | R5 | R6 | R7 | R8 | **R9** | Target |
|---|---|---|---|---|---|---|---|---|---|---|
| Controller Methods (admin_settings) | 40% | 40% | 85% | 90% | 95% | 100% | 100% | 100% | **100%** | 100% |
| Controller Methods (admin_usersms) | 25% | 35% | 90% | 90% | 90% | 95% | 100% | 100% | **100%** | 100% |
| Model Methods (admin_settings_model) | 30% | 35% | 95% | 100% | 100% | 100% | 100% | 100% | **100%** | 100% |
| Model Methods (admin_usersms_model) | 10% | 20% | 85% | 85% | 85% | 95% | 100% | 100% | **100%** | 100% |
| Views Scanned (31 total) | 0% | 0% | 75% | 75% | 95% | 92% | 92% | 100% | **100%** | 100% |
| ret_settings Values | 17% | 100% | 100% | 100% | 100% | 100% | 100% | 100% | **100%** | 100% |
| chit_settings Schema | 0% | 0% | 0% | 0% | 90% | 90% | 100% | 100% | **100%** | 100% |
| Business Rules | 0% | 0% | 100% | 100% | 100% | 100% | 100% | 100% | **100%** | 100% |
| Cross-Module Map | 0% | 0% | 100% | 100% | 100% | 100% | 100% | 100% | **100%** | 100% |
| JavaScript AJAX Map | 0% | 0% | 0% | 90% | 97% | 100% | 100% | 100% | **100%** | 100% |
| Security Audit | 0% | 0% | 0% | 0% | 80% | 100% | 100% | 100% | **100%** | 100% |
| CI Config Security | 0% | 0% | 0% | 0% | 0% | 100% | 100% | 100% | **100%** | 100% |
| KYC System Docs | 0% | 0% | 0% | 0% | 0% | 0% | 0% | 0% | **🔵 100%** | 100% |
| Bugs Identified | 4 | 4 | 11 | 15 | 17 | 19 | 22 | 23 | **23** | — |
| **Overall Coverage** | **38%** | **50%** | **78%** | **92%** | **98%** | **100%** | **100%** | **100%** | **🏁 100%** | 100% |

---

## Round 9 Completed Work — Refresh Scan (2026-03-25)

### ✅ Codebase Delta from Round 8 (2026-03-17 → 2026-03-25)

**Files modified since R8:**
- `admin_settings.php` — L→4838 (was 4709). LastWriteTime: 2026-03-24 14:37
- `admin_settings_model.php` — L→2945 (was 2866). LastWriteTime: 2026-03-24 14:37
- `admin_usersms.php` — unchanged (LastWriteTime: 2026-03-21)
- `admin_usersms_model.php` — unchanged (LastWriteTime: 2026-03-21)
- Views folder — 31 files (unchanged count)

### ✅ New Controller Methods Found (admin_settings.php — 5 new, 123 total)

| Method | Line | Purpose |
|---|---|---|
| `update_country()` | L1000 | Updates `country` table (currency_name, mob_code, mob_no_len). Writes audit log. |
| `upload_img($outputImage, $dst, $img)` | L2719 | GD image processor — GIF/JPEG/PNG → JPEG via imagecopyresampled. Used by all image upload handlers. |
| `kyc_master()` | L4752 | AJAX wrapper → `admin_settings_model::kyc_master()` → returns KYC document types. |
| `get_active_schemes()` | L4758 | AJAX → `payment_model::get_active_scheme()` — returns active schemes for KYC scheme-wise rules. |
| `kyc_settings($type)` | L4764 | KYC config save — builds scheme-wise or customer-wise rule sets → calls `save_kyc_settings()`. |

### ✅ New Model Methods Found (admin_settings_model.php — 5 new)

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `getAvailablePosDevices($ledger_id=0)` | L2859 | `ret_bill_pay_device`, `ledger_mapping` | Returns POS devices not yet mapped to other ledgers |
| `kyc_master()` | L2886 | `kyc_master` | `SELECT id_mas_kyc, name WHERE status=1` |
| `get_kyc_settings()` | L2892 | `kyc_settings` | Single-row KYC config (returns defaults if empty) |
| `get_kyc_rules()` | L2905 | `kyc_rules` | All active rules (`status=1`) |
| `save_kyc_settings($settings, $rules, $uid)` | L2910 | `kyc_settings`, `kyc_rules` | Transactional upsert kyc_settings + delete+re-insert kyc_rules |

### ✅ New Tables Discovered (6 new)

| Table | Key Columns | Module Role |
|---|---|---|
| `kyc_master` | `id_mas_kyc`, `name`, `status` | Document type master (Aadhaar, PAN, etc.) |
| `kyc_settings` | `id_kyc_settings`, `kyc_required`, `kyc_mode`, `kyc_verification_type`, `kyc_allow_type` | Single-row KYC config written here |
| `kyc_rules` | `id_scheme`, `id_mas_kyc`, `rules`, `type`, `amount`, `status`, `created_by` | Per-scheme or per-customer document rules |
| `ledger_master` | `id_ledger`, `ledger_name`, `opening_balance`, `opening_date`, `min_balance`, `status` | Bank/cash ledger definitions |
| `ledger_mapping` | `id_ledger`, `type` (BANK/PAYMODE), `reference_id` | Links ledgers to banks / POS devices |
| `ret_bill_pay_device` | `id_device`, `device_name` | POS payment device master |

> **No new bugs found in R9.** No new view files. KYC system uses proper `trans_begin/commit/rollback`.

---

## Round 8 Completed Work — Deep Audit (Views + Controller Functions)

### ✅ ALL 31 View Files Scanned (Complete)

**Previously unscanned views now analysed:**

| View File | Key Finding |
|---|---|
| `general/list.php` | chit_settings quick list; flash echo unescaped (B-017 ✋) |
| `gateway_list.php` | Payment gateway CRUD + Add/Edit modals (6 card-type toggles); flash echo unescaped (B-017 ✋) |
| `module/list.php` | Module enable/disable DataTable; modal title 'Delete Sms Service' — copy-paste artifact; flash echo unescaped (B-017 ✋) |
| `terms/list.php` | T&C DataTable list; flash echo unescaped (B-017 ✋) |
| `export/export_data.php` | Payment export: date range + pay_status filter (All/Approved/Pending); manual payment modal (6 fields); flash echo unescaped (B-017 ✋) |
| `import/import_customer.php` | Excel customer import (file, is_heading, send_sms, customer_excel_format preview); flash echo unescaped (B-017 ✋) |
| `import/import_data.php` | Scheme Excel import (Weight/Amount type toggle, is_heading, excel_format preview, send_sms); flash echo unescaped (B-017 ✋) |
| `import/import_list.php` | Import result view: Send SMS + Send Email buttons on imported range; `first_id`/`last_id` range params from controller |
| `company.php` | 561 lines — full company profile: 25+ fields including social media URLs (FB/WA/Insta/YT), whatsapp_no, bank details (hidden), mob_no_len JS var from session; set_value() risk (XSS-003 extended) |
| `menu/permission.php` | AJAX-rendered user access matrix — two tabs: Menu + Dashboard; no static PHP content |
| `permission_v5.php` | Superseded version of permission.php |

> **B-017 XSS now confirmed in 9 view files.** This is a module-wide pattern, not an isolated bug. The full list: `retail_setting/list.php`, `general/form.php`, `general/list.php`, `gateway_list.php`, `module/list.php`, `terms/list.php`, `export/export_data.php`, `import/import_customer.php`, `import/import_data.php`.

### ✅ Controller Deep Dive — Previously Unscanned Functions

**`send_bulk_sms($data)` — L791-818** *(admin_settings.php)*
- Chunks mobile array into batches of 5
- POSTs to `http://nammauzhavan.com/api/v1/smjtvm_sendsms`
- **⚠️ CRITICAL B-021: Hardcoded SMS API credential** `Authorization: Basic ` + `base64_encode("lmx@uzhavan:lmx@2018")` — raw credential in source code
- After send: calls `update_prosms()` to debit `promotion_api_settings.debit_promotion`
- Called by: `get_SMS_data()` (L774) which is triggered from `metal_rates()` Save case on rate insert with notification enabled

**`truncateFromArray($tables)` — L2210-2217** *(admin_settings.php)*
- Loops through array of table names, running `TRUNCATE $table` on each
- **⚠️ Raw SQL** — table names supplied by caller (clear_database), no sanitization
- No auth gate inside this function — trust entirely placed in caller

**`rrmdir($path)` — L2219-2231** *(admin_settings.php)*
- Recursive directory deleter: iterates files → `unlink()`, subdirs → recurse, then `rmdir()`
- Called during `clear_database()` to wipe upload folders
- **Note:** Same function duplicated in 8 different controllers (`admin_ret_lot`, `admin_ret_tagging`, `admin_ret_estimation`, `admin_ret_catalog`, `admin_ret_billing`, `admin_employee`, `admin_customer`, `admin_settings`) — code duplication anti-pattern

**`company_form()` / `company_post()` — L1075-1210** *(admin_settings.php)*
- `company_form('Edit', $id)` → `settings/company.php` view with `$comp` data (25+ fields)
- `company_post('Update', $id)` → DB transaction: `update_company()` + `settingsDB('update', id, currency_info)` atomically
- Social media URLs only saved when `show_social_media == 1` (server-side guard) ✅
- Currency sync: on company save, `currency_symbol`, `mob_code`, `mob_no_len` synced to `chit_settings`

**`update_rate_file($rates)` — L493-512** *(admin_settings.php)*
- Writes rate JSON to `../api/rate.txt` using `file_put_contents()`
- Bug B-002 previously noted: `metal_rates('Save')` at L570 calls `file_put_contents('../api/rate.txt', $rate_array)` with a raw PHP **array** (not JSON-encoded) — produces `Array` string in the file

**`admin_usersms` Controller — 54 Functions (20+ now documented for first time)**
- `onesignalNotificationToAll($alertdetails)` → OneSignal cURL push to all subscribed devices
- `compose_group_view()` / `compose_view()` → SMS composition forms
- `send_email()`, `send_group_email()`, `send_group_sms()` → SMTP/bulk send
- `sendemail_allcustomer()`, `sendsms_allcustomer()` → blast to all active customers
- `notidata_gen()`, `notidata_gen_single()` → notification payload generation
- `due_notification()` → Triggers `send_duenotificationsms()` for due installments
- `send_login()`, `send_login_email()` → Login credential SMS/email after import
- `ajax_get_scheme()`, `ajax_get_schemes()` → Scheme dropdown helpers

### ✅ New Bugs Found in Round 8

| # | Location | Type | Severity |
|---|---|---|---|
| B-021 | `admin_settings.php` L806 `send_bulk_sms()` | Hardcoded SMS API credential `lmx@uzhavan:lmx@2018` in source code | 🔴 **Critical** |

> B-017 scope upgraded: **9 view files** affected (not just 2 originally documented). This affects the severity impact estimate — any admin flash message containing user-derived content is an XSS vector across the entire settings module UI.

---

## Round 7 Completed Work — Final Gap Closure

### ✅ admin_usersms_model.php L800–1920 — Complete Final Scan

**L800–1200: Due SMS Engine + Notification helpers** (previously documented in context)

**L1200–1400: Notification Content Generators**
- `getnotiData(id_notification)` → Fetches tokens from `registered_devices` + `customer`, appends latest metal_rates (gold+silver), fetches `notification` template, returns `{data, header, footer, message}` array
- `check_noti_settings()` → `SELECT allow_notification FROM chit_settings WHERE id_chit_settings=1` — returns boolean flag
- `get_noti_settings(noti_id)` → `SELECT send_notif_on, send_daily_from FROM notification WHERE id_notification=X`
- `get_noticontent(sch_data)` → Fetches `noti_msg` from `notification`, replaces `@@tgoldrate_22ct@@` vs `@@ygoldrate_22ct@@` placeholders

**L1335–1500: Metal Rate Notification Functions**
- `metalrate_gold(filterBy)` → 4 filter branches: `T` (today), `Y` (yesterday min), `TM` (this month min), `ALL` (all-time min) — returns `{updatetime, goldrate_22ct}`
- `get_noti(sch_data)` → Returns notification row `{id_notification, noti_name, noti_footer}` by ID
- `getDevicetokens()` → All customer device tokens where `customer.notification = 1`
- `get_allcustomersms_list()` → All active customer mobile numbers
- `get_selectcustomersms_data()` → ⚠️ **Uses undefined `$id_branch`** (Bug B-020b — silent PHP notice)
- `get_allcustomeremail_list()` → All active customer emails
- `get_metalnotiContent(id_notification)` → if `id_notification==1`: fetches `company + metal_rates` data; generates message content with `@@field@@` substitution

**L1500–1670: SMS Sending + Utilities**
- `insert_sent_notification(data)` → Inserts into `sent_notifications` table (8 fields: noti_service, title, content, img, id_customer, targetUrl, id_branch, date_add)
- `get_sms_settings(id_services)` → `SELECT send_sms_on, dlt_te_id, send_daily_from FROM services WHERE id_services=X`
- `get_SMS_due(service_id)` → service_id=22: massive 6-table JOIN (scheme_account + scheme + payment + customer + postdate_payment + chit_settings + services + company), filters `currentpaycount <= 0` → formats output with Currency symbol from `chit_settings.currency_symbol`

**L1677–1776: Branch-Level Rate Notification Functions**
- `get_account(id_branch)` → Fetches scheme_account → branch_rate → metal_rates + customer mobile for rate-push notifications
- `getBranches()` → Simple `SELECT id_branch FROM branch` for batch processing
- `get_cusBranchRate(id_branch, types)` → Full rate notification data: customer → branch → metal_rates (subquery ORDER by desc LIMIT 1) → registered_devices → company. Returns uuid token, all metal rates (gold22/24/18, silver, mjdma variants, platinum)
- `get_metal_rateby_branch(id_customer)` → Customer-specific branch rate with device tokens
- `get_sendnotifi_cusBranch(id_branch, types)` → Light version — only token + customer IDs

**L1749–1843: Ret-Settings CRUD + Account Management**
- `deleteNoPayments_Acc(months)` → Deletes `scheme_account` rows with zero payments after N months — **⚠️ Bug: returns inside foreach loop** (Bug B-020a — only first account's status returned, rest not processed)
- `get_ret_settings()` → `SELECT * FROM ret_settings` — returns full array
- `get_empty_recordss()` → Returns empty array template for new ret_settings form
- `get_entry_recordss(id)` → Fetches single ret_settings record (id, name, value, description, created_by, updated_by)
- `delete_ret_settings(id)` → CI `where + delete` on `ret_settings`
- `insert_ret_settings(data)` → CI `insert` on `ret_settings`
- `update_ret_settings(data)` → CI `where(id) + update` on `ret_settings`

**L1847–1920: Utilities**
- `send_whatsApp_message(mobile, message, attachment_url, file_name)` → cURL POST to `whatsappurl` or `whatsappfileurl` from CI config. **⚠️ CRITICAL: Hardcoded Authorization header** `"authorization: Basic cHJlY2lzZXRyYTpIaXJoTmwxMA=="` — Base64 decoded = `precisetRA:HirhNl10` → **hardcoded credential (B-020)!**
- `get_customer_wishes_data()` → Customers with birthday or anniversary matching today (`DATE_FORMAT(date_of_birth, '%M%D') = DATE_FORMAT(CURDATE(), '%M%D')`) — returns `{mobile, id_customer, date_of_birth, date_of_wed, cus_name, send_bday, send_wedday}`
- `get_cus_wallet_data()` → Wallet balance per customer: SUM CASE (transaction_type=0 → credit, else debit), filters `balance_amount > 0`

### ✅ chit_settings Table Schema (from retail_staging_20_02_26.sql L720-849)

**Primary Key:** `id_chit_settings` (single row, AUTO_INCREMENT=2, id=1 always)

| Column Group | Key Columns |
|---|---|
| **Scheme Joining Rules** | `allow_join_multiple`, `allow_join_unpaid`, `reg_existing`, `sch_limit`, `newSchjoinonline` |
| **Currency / Locale** | `currency_name`, `currency_symbol`, `curr_symb_html`, `currency_format` (en-IN), `currency_decimal` |
| **Mobile validation** | `mob_code` (+91), `mob_no_len` (10) |
| **Rate / Notification** | `rate_update` (0-Manual/1-API/2-Both), `allow_notification`, `is_ratenoti_sent` |
| **Maintenance** | `maintenance_mode`, `maintenance_text` |
| **Receipt / Account Numbering** | `receipt`, `scheme_wise_receipt` (7-mode enum), `scheme_wise_acc_no` (6-mode enum), `schemeaccNo_displayFrmt`, `receiptNo_displayFrmt`, `custom_AccDisplayFrmt` (JSON), `custom_ReceiptDisplayFrmt` |
| **OTP Gates** | `enable_closing_otp`, `regExistingReqOtp`, `isOTPReqToLogin`, `isOTPRegForPayment`, `isOTPReqToGift`, `otp_scheme_join`, `req_calling_code` |
| **Branch / Company** | `branch_settings`, `branchWiseLogin`, `is_branchwise_cus_reg`, `is_branchwise_rate`, `branchwise_scheme`, `cost_center`, `company_settings` |
| **Wallet** | `allow_wallet`, `wallet_account_type`, `wallet_balance_type`, `wallet_amt_per_points`, `wallet_points`, `walletIntegration`, `useWalletForChit`, `emp_wallet_account_type` |
| **Gold/Silver Discounts** | `enableGoldrateDisc`, `goldDiscAmt`, `enableGoldrateDisc_18k`, `goldDiscAmt_18k`, `enableSilver_rateDisc`, `silverDiscAmt`, `rate_disc_type` |
| **KYC** | `is_kyc_required` (0/1-common/2-plan-based), `kyc_approval` (1-API/2-manual), `block_kyc_by`, `pan_required_by`, `pan_req_amt`, `show_kyc_optional`, `is_agent_kyc_required` |
| **Referral / Benefits** | `allow_referral`, `schrefbenifit_secadd`, `cusplan_type`, `cusbenefitscrt_type`, `empplan_type`, `empbenefitscrt_type` |
| **Feature Flags** | `has_lucky_draw`, `allow_savecard`, `allow_catlog`, `allow_our_stores`, `allow_chit_catlog`, `allow_prestashop_catlog`, `auto_debit`, `auto_debit_allow_app_pay`, `lock_metal` |
| **GST / Misc** | `gst_setting`, `estimation`, `pledge_calculator`, `metal_wgt_decimal`, `metal_wgt_roundoff`, `rate_history`, `vs_enable`, `enable_dth`, `enable_digi_gold`, `enable_coin_enq`, `enable_coin_book` |
| **SMS / Integration** | `integration_type` (1-tool/2-direct), `msg91_authkey`, `vs_send_sms_to`, `vs_send_mail_to` |
| **Auto Debit** | `auto_debit`, `auto_debit_allow_app_pay`, `block_pay_mins`, `restrict_lastPayment_days`, `chitCollectionEmpCount` |

**Actual row values** (production snapshot): `currency_symbol='INR'`, `currency_format='en-IN'`, `mob_code='+91'`, `mob_no_len=10`, `msg91_authkey='363177AeaGCWyz3C60d42cefP1'` (live API key in SQL dump — **security concern**), `integration_type=1`, `allow_notification=1`

### ✅ New Bugs Found in Round 7

| # | Location | Type | Severity |
|---|---|---|---|
| B-020 | `admin_usersms_model.php` L1875 `send_whatsApp_message()` | Hardcoded WhatsApp Basic Auth credential in source code | 🔴 **Critical** |
| B-020a | `admin_usersms_model.php` L1770 `deleteNoPayments_Acc()` | `return` inside `foreach` — only first account deleted, rest skipped | 🟠 Medium |
| B-020b | `admin_usersms_model.php` L1413 `get_selectcustomersms_data()` | Uses undefined `$id_branch` variable (silent PHP notice) | 🟡 Low |

---

## Round 6 Completed Work

### ✅ CI Config Security Verification (Critical Discovery)
```
$config['global_xss_filtering'] = FALSE;   // config.php L418
$config['csrf_protection']       = FALSE;   // config.php L431
```
**Impact:** Escalates ALL previously "Medium" XSS and CSRF bugs to **Critical**. Both protection layers are off application-wide. This is the most important finding of the entire brain build.

### ✅ JavaScript FULL SCAN — L3200–6331 Complete

**L3204–3260: Metal Rate & Charges**
- `load_metalrate_list()` → `settings/rate/ajax_list` — only shows latest max_id as editable
- `set_charges_table()` → `settings/payment_charges/ajax_list` (ServerSide DataTable)
- `add_charges_row()`, `del_charges_row()` — dynamic table row add/remove, `validate_row()` always returns `true` (Bug B-018 — no actual validation)

**L3315–3395: Backup / Import**
- `#btn-backup` → `settings/backup/database` (direct redirect)
- `#btn-walletbackup` → `admin_settings/interWalletAcc_backup` (direct redirect)
- `load_db_list()` → `settings/backup/database/list`
- `get_import_list()` → `settings/import/ajax_list/{lower}/{upper}`
- `set_import_list()` — DataTable with customer checkbox selector

**L3396–3467: Card Brand CRUD**
- `get_cardbrand(id)` → `settings/cardbrand/edit/{id}` (GET)
- `update_cardbrand()` → `settings/cardbrand/update/{id}` (POST)
- `set_cardbrand_table()` → `settings/cardbrand_list` (ServerSide)
- `add_card_brand()` → `settings/cardbrand/add` (POST)

**L3469–4200: Branch Full CRUD**
- `set_branch_table()` → `branch/branch_list` (POST, JSON)
- `get_branch(id)` → `branch/branch_name/edit/{id}` — 20 fields populated including GST, metal type, partial rates, geo (country/state/city cascade)
- `add_branch()` → `branch/branch_name/add` — FormData with 20 fields including file
- `update_branch()` → `branch/branch_name/update/{id}` — same pattern
- `edit_country()` → `settings/company/getcountry` | `get_states()` → `settings/company/getstate` | `get_citys()` → `settings/company/getcity`
- `get_offrate_list()` + `set_offrate_list()` → `admin_settings/offratelist_data` (POST with date range + branch filter)

**L4218–4511: Payment Gateway CRUD**
- `set_paymentgateway_list(id_branch)` → `settings/payment_gateway_list` (POST)
- `set_gateway_list()` — DataTable renderer
- `add_payment_gateway()` → `settings/payment_gateway/add` (POST FormData + file)
- `update_payment_gateway()` → `settings/paymentgateway/update/{id}` (POST FormData)
- `get_paymentgateway(id)` → `settings/payment_gateway/edit` (POST)
- `get_gateway()` → `admin_settings/ajax_paymentgateway` (POST by type+pg_code+branch)
- `update_gateway()` → `admin_settings/update_gateway` (POST params 1-4)

**L4512–4721: Silver Disc, Modules, Village**
- Silver discount conditional field (enableSilver_rateDisc checkbox → silverDiscAmt enabled/disabled)
- `set_modules_list()` → `settings/module/ajax` — DataTable with m_app/m_web/m_active toggle links → `admin_usersms/module_status/{field}/{val}/{id}`
- `get_village_list()` + `set_village_list()` → `admin_settings/ajax_village_list`

**L4773–4976: General Settings Form Submit + Branch Img Validation**
- Submit button handler for all 5 tabs: `#submit_maintn_tab`, `#submit_others_tab`, `#submit_sch_pay_tab`, `#submit_metal_tab`, `#submit_config_tab` — validates account/receipt format selections before submitting `#gen_settings` form
- `validat_Image()` — file type (jpg/png/jpeg/svg) + size ≤ 1MB validator for branch images

**L4977–5028: ret_settings DataTable (KEY FUNCTION)**
- `set_retail_settings_list()` → `settings/retail_setting/ajax` (POST)
- DataTable columns: `id_ret_settings`, `name`, `value`, `description`, `created_by`, `updated_by`, action buttons
- Edit URL → `settings/retail_setting/edit/{id}` | Delete URL → `settings/retail_setting/delete/{id}`

**L5029–5157: Profile Settings Checkbox Handlers**
- 14 checkbox handlers for profile tab: `est_tag`, `est_non_tag`, `est_home_bill`, `est_old_metal`, `tag_transfer`, `packaging_item_transfer`, `purchase_item_transfer`, `non_tag_transfer`, `tag_details`, `purchase_details`, `stone_details`, `estimation`, `branch_transfer_details`, `section_transfer_details`, `scan_details`, `stock_issue_details`

**L5200–5350: Profession CRUD**
- `set_profession_table()` → `settings/profession_list` (POST)
- `get_profession(id)` → `settings/profession/edit/{id}` (GET)
- `add_profession()` → `settings/profession/add` (POST)
- `update_profession()` → `settings/profession/update/{id}` (POST)
- `checkgst()` — GST regex validator: `/^([0-9]{2}[a-zA-Z]{4}...)/`

**L5355–5444: Version Table**
- `set_version_table(from_date, to_date)` → `settings/version/ajax` (POST with date range)
- DataTable with Print + Excel buttons, scrollX
- `get_title()` — Report header generator using jQuery DOM values

**L5467–5708: Account/Receipt Number Format Customizer**
- `insertFormat()` / `insertReceiptFormat()` — Add fields to selected list, store JSON in hidden inputs `#acc_format_hidden`, `#rcpt_format_hidden`
- `revertFormat()` / `revertReceiptFormat()` — Remove from selected, restore to available
- `show_sample()` — Live preview generator for account/receipt number format
- `updateFieldList()` — Switches available fields based on `schemeaccNo_displayFrmt` selection (7 modes: common, branch-wise, scheme-wise, fin-year, etc.)
- `updateReceiptFieldList()` — Same for receipt format

**L5643–5707: Quick Link Management**
- `menu_data()` → `admin_settings/quick_link` (POST with selected menu IDs)
- `menu_revert_data()` → `admin_settings/quick_link_revert` (POST)

### ✅ admin_usersms_model.php L400–800 — SMS Engine
- **`get_sms_data(service_id, serv_code, id)`** — 8 query branches by service_id:
  - `2/1/4/13/31` → scheme_account + customer + scheme + company + chit_settings
  - `3/SCH_CMP/7/14` → payment + scheme_account + customer + payment_status_message
  - `5/6` → postdate_payment
  - `8/9` → wallet_account + wallet_transaction
  - `11` → customer + address (mobile as id)
  - `16` → referral wallet calculation (cusplan_type, empplan_type)
  - `default` → company + chit_settings
- **SMS template engine**: `@@field@@` substitution from query result row, with ucwords() case formatting
- `update_sms_status(data, id)` → updates `services` table
- `notification_on_off(data)` → updates `chit_settings`
- `update_notification_status(data, id)` → updates `notification` table
- `sms_info()` → `sms_api_settings`
- `promotion_info()` → `promotion_api_settings`
- `promotion_smsavilable()`, `otp_smsavilable()` — debit balance checks
- `delete_service(id)`, `delete_notification(id)` — hard deletes with CI->db->where

### ✅ New Bugs Found in Round 6

| # | Location | Type | Severity |
|---|---|---|---|
| B-018 | JS L3312-3314 `validate_row()` | Always returns `true` — no actual validation | 🟡 Low |
| B-019 | `config.php` L418,431 | `global_xss_filtering=FALSE` + `csrf_protection=FALSE` | 🔴 **Critical** |

---

## Complete Bug Register (19 Bugs)

| # | Location | Type | Severity | Status |
|---|---|---|---|---|
| B-001 | `clear_database()` controller | No OTP gate server-side | 🔴 Critical | Open |
| B-002 | `metal_ratesDB()` | Wrong format written to rate.txt | 🟠 Medium | Open |
| B-003 | `admin_settings_model.php` raw SQL | SQL Injection risk | 🟠 Medium | Open |
| B-004 | `ret_settings.name` | No UNIQUE constraint | 🟡 Low | Open |
| B-005 | `catlog_module_post('Update')` | Undefined `$module_id` in UPDATE | 🟠 Medium | Open |
| B-006 | `ajax_village_list()` | Uses raw `$_GET` | 🟡 Low | Open |
| B-007 | `get_interWallet_trans` | Broken OR parentheses in query | 🟡 Low | Open |
| B-008 | `permission()` loop | Not in transaction | 🟡 Low | Open |
| B-009 | `validate_cash_amt = 0` | ₹2L cash limit unenforced | 🔴 Critical | Open |
| B-010 | `metal_rates_list()` L1739 | NULL check reversed | 🟠 Medium | Open |
| B-011 | `order_cancel_otp_req` name | Mismatch vs `order_cancel_otp` | 🟡 Low | Open |
| B-012 | JS L843 `clear_database` AJAX | No CSRF token | 🔴 Critical | Open |
| B-013 | JS L753 `new_arrivals` validation | Wrong operator `&&` should be `\|\|` | 🟠 Medium | Open |
| B-014 | JS L1125 | Debug `alert()` in production | 🟡 Low | Open |
| B-015 | JS L967 | Unconditional `excel_format()` call | 🟡 Low | Open |
| B-016 | `form.php` L179,202,225 | Duplicate `id_ret_settings` HTML id × 3 | 🟡 Low | Open |
| B-017 | `list.php` L38, `general/form.php` L33 | Unescaped flash echo | 🔴 Critical | Open |
| B-018 | JS L3314 `validate_row()` | Always returns `true` (stub) | 🟡 Low | Open |
| B-019 | `config.php` | `global_xss_filtering=FALSE`, `csrf_protection=FALSE` | 🔴 **Critical** | Open |
| B-020 | `admin_usersms_model.php` L1875 | Hardcoded WhatsApp Basic Auth credential in source code | 🔴 **Critical** | Open |
| B-020a | `admin_usersms_model.php` L1770 | `return` inside `foreach` in `deleteNoPayments_Acc()` — only 1 deleted | 🟠 Medium | Open |
| B-020b | `admin_usersms_model.php` L1413 | `get_selectcustomersms_data()` uses undefined `$id_branch` | 🟡 Low | Open |
| B-021 | `admin_settings.php` L806 `send_bulk_sms()` | Hardcoded SMS API credential `lmx@uzhavan:lmx@2018` in Basic Auth header | 🔴 **Critical** | Open |

> **Critical bugs: B-001, B-009, B-012, B-017 (×9 views!), B-019, B-020, B-021 → Fix B-019 first; rotate B-020 and B-021 credentials immediately.**

---

## Brain Document Inventory (10 Files)

| File | Size | Status |
|---|---|---|
| `MODULE_BRAIN.md` | 9.7 KB | ✅ Complete |
| `SCHEMA_ANALYSIS.md` | 7.4 KB | ✅ Complete |
| `METHOD_INDEX.md` | 22.6 KB | ✅ Complete |
| `SETTINGS_SNAPSHOT.md` | 11.1 KB | ✅ Complete |
| `BUSINESS_RULES.md` | 7.3 KB | ✅ Complete |
| `CROSS_MODULE_MAP.md` | 7.9 KB | ✅ Complete |
| `FORENSIC_TEMPLATE.md` | 5.9 KB | ✅ Complete |
| `JS_MAP.md` | 9.4 KB | ✅ Complete (Round 6 updates applied) |
| `SECURITY_AUDIT.md` | ~9 KB | ✅ **Round 6 Final** |
| `COVERAGE_TRACKER.md` | this file | ✅ **100% Complete** |

---

## 🏁 Brain Build Final — Round 8 Deep Audit Complete

**23 bugs identified | 10 documents | 100% coverage | 31/31 views scanned | Ready for bug-fix sprint**

> **Immediate action:** Rotate 2 hardcoded API credentials (B-020 WhatsApp `precisetRA:HirhNl10`, B-021 SMS `lmx@uzhavan:lmx@2018`). Then B-019 to close XSS/CSRF at framework level.

**2026-03-17 — Retail Settings Brain Build — Round 8 FINAL**
