# METHOD INDEX — Retail Settings
> **Module:** Retail Settings | **Round:** 9 | **Date:** 2026-03-25  
> All methods alphabetical within each section for grep-optimized lookup.

---

## §1 — `admin_settings` Controller Methods (127 total — 5 new in R9)

| Method | Lines (approx) | Type | Tables Read | Tables Written | JS Caller / Notes |
|---|---|---|---|---|---|
| `add_gift()` | L4063 | AJAX | — | `gift_voucher` | `ajax_get_gift` |
| `ajax_backup_list()` | L2271 | AJAX | — | — | JS backup list |
| `ajax_get_bank()` | L1554 | AJAX | `bank` | — | JS dropdowns |
| `ajax_get_branches()` | L3133 | AJAX | `branch` | — | JS branch select |
| `ajax_get_cardbrand()` | L2939 | AJAX | `cardbrand` | — | JS |
| `ajax_get_classifications()` | L1543 | AJAX | `sch_classify` | — | JS |
| `ajax_get_depts()` | L1510 | AJAX | `department` | — | JS |
| `ajax_get_designs()` | L1521 | AJAX | `designation` | — | JS |
| `ajax_get_drawee()` | L1576 | AJAX | `drawee_account`, `bank` | — | JS |
| `ajax_get_exportlist()` | L1587 | AJAX | — | — | JS |
| `ajax_get_gift()` | L4005 | AJAX | `gift_voucher` | — | JS |
| `ajax_get_new_arrivals()` | L2866 | AJAX | `new_arrivals` | — | JS |
| `ajax_get_offers()` | L2707 | AJAX | `offers` | — | JS |
| `ajax_get_paymentgateway()` | L3683 | AJAX | `payment_gateway` | — | JS |
| `ajax_get_paymentMode()` | L1565 | AJAX | `payment_mode` | — | JS |
| `ajax_get_profession()` | L4147 | AJAX | `profession` | — | JS |
| `ajax_get_version()` | L4262 | AJAX | `app_version` | — | JS |
| `ajax_get_weights()` | L1532 | AJAX | `weight` | — | JS |
| `ajax_paymentgateway()` | L3683 | AJAX | `payment_gateway` | — | JS |
| `ajax_village_list()` | L3743 | AJAX | `village` | — | JS |
| `bank($type, $id)` | L395 | Page+AJAX | `bank` | `bank` | Full CRUD — validates unique in code |
| `branch_form($type, $id, $status)` | L2984 | Page | `branch`, `company`, `state`, `city` | `branch` | Complex form — image upload |
| `branch_settings($id)` | L2946 | Page | `branch`, `ret_settings` | — | Branch-specific view |
| `cardbrand_form($type, $id)` | L2877 | Page | `cardbrand` | `cardbrand` | CRUD |
| `classification_form($type, $id)` | L1315 | Page | `sch_classify` | `sch_classify` | Image upload |
| `clear_database()` | L2147 | POST | — | **Multiple tables** | ⚠️ Truncates DB — NO OTP gate |
| `comp_list()` | L1214 | AJAX | `company` | — | JS |
| `company_form($type, $id)` | L1075 | Page | `company`, `state`, `city`, `country` | `company` | Core company profile |
| `company_post($type, $id)` | L1092 | POST | — | `company` | POST handler |
| `compress()` | L3846 | GET | — | — | DB backup compress |
| `config_setting($id)` | L4405 | Page | `app_config` | — | Mobile app config view |
| `config_setupdate($id)` | L4415 | POST | — | `app_config` | Mobile app config save |
| `contact_already_exist($mobile, $email)` | L1232 | AJAX | `customer` | — | Duplicate check |
| `db_backup()` | L2232 | GET | — | — | Download DB dump |
| `dept_form($type, $id)` | L1382 | Page | `department` | `department` | CRUD |
| `design_form($type, $id)` | L1446 | Page | `designation` | `designation` | CRUD |
| `discount_settings($type, $id)` | L2444 | Page | `ret_disc_settings` | `ret_disc_settings` | Gold/silver discount config |
| `download()` | L3831 | GET | — | — | Backup download |
| `drawee($type, $id)` | L830 | Page | `drawee_account`, `bank` | `drawee_account` | CRUD |
| `gateway_form($type, $id)` | L3406 | Page | `payment_gateway` | `payment_gateway` | With image upload |
| `gateway_settings($type, $id, $array)` | L2277 | Page | payment gateway tables | payment gateway tables | Config CRUD |
| `general_settings($type, $id)` | L1706 | Page | `ret_settings`, company, branch tables | `ret_settings` | **Core retail settings save** |
| `get_acc_format_details(...)` | L4333 | Internal | — | — | Utility |
| `get_access_rights()` | L987 | AJAX | `access`, `menu` | — | JS access check |
| `get_active_schemes()` | L4758 | AJAX | `ret_saving_scheme` | — | **[NEW R9]** Returns active schemes via payment_model for KYC scheme-wise rules |
| `get_all_gifts()` | L4034 | AJAX | `gift_voucher` | — | JS |
| `get_city()` | L1060 | AJAX | `city` | — | JS cascade |
| `get_countryCurrency($id)` | L1029 | AJAX | `country` | — | JS |
| `get_country()` | L994 | AJAX | `country` | — | JS cascade |
| `get_gift_name_byId()` | L4027 | AJAX | `gift_voucher` | — | JS |
| `get_languages_known()` | L4567 | AJAX | — | — | JS |
| `get_last_version()` | L4276 | AJAX | `app_version` | — | JS |
| `get_otp_giftstatus()` | L4163 | AJAX | — | — | JS |
| `get_page_title($url_name)` | L4283 | Internal | `menu` | — | Utility |
| `get_profession()` | L4158 | AJAX | `profession` | — | JS |
| `get_receipt_format_details(...)` | L4346 | Internal | — | — | Utility |
| `get_reports()` | L3826 | Page | — | — | Backup reports |
| `get_SMS_data($serviceID)` | L774 | Internal | SMS tables | — | SMS dispatch helper |
| `get_state()` | L1052 | AJAX | `state` | — | JS cascade |
| `get_weight_scheme_id($type, $scheme_code)` | L1253 | AJAX | `ret_saving_scheme` | — | Scheme lookup |
| `get_otpsettings()` | L2141 | AJAX | `otp_settings` | — | JS |
| `get_scheme_id($scheme_code)` | L1246 | AJAX | `ret_saving_scheme` | — | Scheme lookup |
| `getCustomerByMobile($mobile)` | L1686 | AJAX | `customer` | — | JS |
| `getSchemeAccountByCustomer($id)` | L1693 | AJAX | `ret_cus_scheme_acc` | — | JS |
| `gift($type, $id)` | L3946 | Page | `gift_voucher` | `gift_voucher` | CRUD |
| `index()` | L41 | GET | — | — | Loads company view |
| `interWalletAcc_backup()` | L3513 | GET | — | — | Wallet backup |
| `is_mobile_exists($mobile)` | L1221 | AJAX | `customer` | — | Duplicate check |
| `is_refno_exists($ref_no)` | L1700 | AJAX | `ret_billing` | — | Ref no check |
| `kyc_master()` | L4752 | AJAX | `kyc_master` | — | **[NEW R9]** Returns active KYC document types for settings form |
| `kyc_settings($type)` | L4764 | Page+POST | `kyc_settings`, `kyc_rules`, `ret_saving_scheme` | `kyc_settings`, `kyc_rules` | **[NEW R9]** KYC settings save — scheme-wise or customer-wise rule sets via `save_kyc_settings()` |
| `ledger($type, $id)` | L4616 | Page | `ledger_master`, `ledger_mapping`, `bank`, `ret_bill_pay_device` | `ledger_master`, `ledger_mapping` | Ledger settings CRUD |
| `limit_settings($type, $id)` | L2449 | Page | `limit_settings` | `limit_settings` | Billing limits |
| `mail_settings($type, $id)` | L2369 | Page | `mail_settings` | `mail_settings` | SMTP config |
| `matal_ratelist($id)` | L3143 | AJAX | `metal_rates` | — | JS rate list |
| `menu($type, $id)` | L47 | Page+AJAX | `menu`, `profile`, `access` | `menu`, `access` | Full CRUD + auto-permission |
| `metal_rates($type, $id)` | L514 | Page | `metal_rates`, `ret_disc_settings` | `metal_rates`, `branch_rate`, `rate.txt` | Rate save fires notifications |
| `metal_rates_discount()` | L3710 | Page | `ret_disc_settings` | — | Discount view |
| `new_arrivals_form($type, $id)` | L2727 | Page | `new_arrivals` | `new_arrivals` | New arrivals CRUD |
| `notification($type, $id)` | L4541 | Page | `notification_settings` | `notification_settings` | Push notif config |
| `offers_form($type, $id)` | L2485 | Page | `offers` | `offers` | Image + offer CRUD |
| `onesignalNotificationToAll(...)` | L3649 | Internal | — | — | OneSignal push dispatch |
| `otpcredit_settings($type, $id, $array)` | L3273 | Page | `otpcredit_settings` | `otpcredit_settings` | OTP credit config |
| `payment_charges($type, $id)` | L1601 | Page | `payment_charges` | `payment_charges` | Charge slab CRUD |
| `payment_mode($type, $id)` | L889 | Page | `payment_mode` | `payment_mode` | CRUD |
| `permission($type, $id)` | L282 | Page+AJAX | `menu`, `access`, `dashboard_menu`, `dashboard_access` | `access`, `dashboard_access` | Role permission matrix |
| `profession_form($type, $id)` | L4083 | Page | `profession` | `profession` | CRUD |
| `profile($type, $id)` | L115 | Page+AJAX | `profile` | `profile` | 50-field permission profile CRUD |
| `promotioncredit__settings($type, $id, $array)` | L3208 | Page | `promotion_settings` | `promotion_settings` | Promotion credit |
| `promotion_api_settings($type, $id, $array)` | L3242 | Page | `promotion_api` | `promotion_api` | Promotion API |
| `quick_link($id)` | L4488 | Page | `quick_links` | `quick_links` | Dashboard quick links |
| `quick_link_revert($id)` | L4515 | AJAX | `quick_links` | `quick_links` | Revert quick link |
| `receipt_settings($id)` | L1034 | Page | `receipt_settings` | `receipt_settings` | Receipt format config |
| `ref_benefits_setting($id)` | L3343 | Page | `ref_benefit_settings` | `ref_benefit_settings` | Referral benefits |
| `rrmdir($path)` | L2219 | Internal | — | — | Recursive dir delete |
| `schemeacc_no_settings($id)` | L3172 | Page | `scheme_acc_settings` | `scheme_acc_settings` | Scheme account number config |
| `send_bulk_sms($data)` | L791 | Internal | — | — | Multi-gateway SMS dispatch |
| `send_RatesToAllUsers($branchArr)` | L3540 | Internal | customer/token tables | — | Push notifications for rate |
| `send_singlealert_rate_notification(...)` | L3911 | Internal | — | — | OneSignal rate alert |
| `set__branch_image($id)` | L2614 | POST | — | `branch` | Branch image upload |
| `set__clsfy_image($id)` | L3383 | POST | — | `sch_classify` | Classification image |
| `set__paymentgateway_image($id)` | L3491 | POST | — | `payment_gateway` | Gateway image |
| `set_image($id)` | L2639 | POST | — | `company` | Company logo upload |
| `sms_api_settings($type, $id, $array)` | L2338 | Page | `sms_api` | `sms_api` | SMS API credentials |
| `terms_conditions($type, $id)` | L3855 | Page | `terms_and_conditions` | `terms_and_conditions` | T&C CRUD |
| `truncateFromArray($tables)` | L2253 | Internal | — | **Any table** | ⚠️ Helper called by clear_database |
| `update_country()` | L1000 | POST | `country` | `country` | **[NEW R9]** Updates currency_name, currency_code, mob_code, mob_no_len for a country ID. Writes audit log. Redirects to `settings/general/edit/1` |
| `update_discout()` | L3719 | POST | — | `ret_disc_settings` | Discount update |
| `update_gateway()` | L3692 | POST | — | `payment_gateway` | Gateway update |
| `update_gift()` | L4041 | POST | — | `gift_voucher` | Gift update |
| `update_gift_status($status, $id)` | L4016 | POST | — | `gift_voucher` | Status toggle |
| `update_notification_status($status, $id)` | L4554 | POST | — | `notification_settings` | Status toggle |
| `update_prosms($mob_length)` | L820 | Internal | — | SMS tables | SMS update |
| `update_rate_file($rates)` | L494 | Internal | — | `../api/rate.txt` | File write |
| `upload_img($outputImage, $dst, $img)` | L2719 | Internal | — | `$dst` (file) | **[NEW R9]** GD image processor — converts GIF/JPEG/PNG to JPEG with imagecopyresampled. Returns false if not valid image. Called by image upload handlers |
| `valid_str($str)` | L386 | Callback | — | — | Form validation callback |
| `version_details($type, $id)` | L4171 | Page | `app_version` | `app_version` | App versioning |
| `village_form($type, $id)` | L3752 | Page | `village` | `village` | Village CRUD |
| `village_list()` | L3736 | Page | `village` | — | Village list view |
| `wallettype_account($id)` | L3308 | Page | `wallet_type`, `account` | — | Wallet type config |
| `weight_form($type, $id)` | L1260 | Page | `weight` | `weight` | Weight master CRUD |

---

## §2 — `admin_usersms` Controller Methods (Key methods)

| Method | Lines (approx) | Type | Tables | Notes |
|---|---|---|---|---|
| `ajax_get_scheme($id)` | L594 | AJAX | `ret_saving_scheme` | Scheme data for SMS group |
| `ajax_get_schemes()` | L561 | AJAX | `ret_saving_scheme` | List |
| `ajax_get_schemes_list($id)` | L576 | AJAX | `ret_saving_scheme` | By ID |
| `compose_group_view()` | L425 | Page | — | Group email compose |
| `compose_view()` | L409 | Page | — | Email compose |
| `create_pushnotification()` | L650 | POST | — | GCM push (legacy Android) |
| `get_selectcustomer_list()` | L181 | AJAX | `customer` | Customer select |
| `index()` | L67 | Page | — | SMS dashboard |
| `module_settings($type)` | ~L1300 | Page | `sms_service_settings` | Module SMS toggles |
| `open_entry_form($type, $id)` | L85 | Page | — | SMS entry form |
| `open_group_form($type, $id)` | L447 | Page | SMS group tables | Group form |
| `open_group_post($type, $id)` | L479 | POST | SMS group tables | Group save |
| `open_listingform()` | L393 | Page | — | Group list |
| `open_notification_entry_form()` | L638 | Page | — | Notification form |
| `**ret_settings_post($id)**` | L3245 | POST | — | `ret_settings` | **KEY: Saves retail settings** |
| `**ret_settings_view($id)**` | ~L3200 | Page | `ret_settings` | **KEY: Retail settings form** |
| `scheme_business($id)` | L614 | Internal | `ret_saving_scheme`, `customer` | Internal helper |
| `send_email()` | L317 | POST | — | Email send |
| `send_group_email()` | L363 | POST | — | Bulk email |
| `send_group_sms()` | L280 | POST | — | Bulk SMS |
| `send_login($mobile)` | L744 | POST | `customer` | SMS login creds |
| `send_login_sms($mobile)` | L778 | Internal | SMS tables | SMS dispatch |
| `sendemail_allcustomer()` | L233 | POST | `customer` | Bulk email |
| `sendemail_selectedcustomer()` | L254 | POST | `customer` | Selected email |
| `sendsms_allcustomer()` | L139 | POST | `customer` | Bulk SMS |
| `sendsms_selectcustomer()` | L190 | POST | `customer` | Selected SMS |
| `service_settings($type)` | ~L800 | Page | `sms_service_settings` | SMS service config |

---

## §3 — `admin_settings_model` Key Methods

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `ajaxsettings_db` / `settingsDB` | ~L1100+ | various settings tables | various | General settings methods |
| `bankDB($type, $id, $array)` | L405 | `bank` | `bank` | `bank()` controller |
| `DashboardPermissionDB($type...)` | L291 | `dashboard_menu`, `dashboard_access`, `profile` | `dashboard_access` | `permission()` |
| `draweeDB($type, $id, $array)` | L462 | `drawee_account`, `bank` | `drawee_account` | `drawee()` |
| `get_access($url)` | L96 | `access`, `menu`, `profile` | — | Every controller method |
| `get_branch_rate($id_branch)` | L20 | `metal_rates`, `branch_rate` | — | Branch rate views |
| `get_city($id_state)` | L621 | `city`, `branch` | — | `get_city()` |
| `get_classifications()` / `ajax_get_classifications()` | L692/698 | `sch_classify` | — | Controller |
| `get_country()` | L591 | `country` | — | `get_country()` |
| `get_state($id_country)` | L607 | `state` | — | `get_state()` |
| `get_weights()` / `ajax_get_weights()` | L653/660 | `weight` | — | Controller |
| `insertData($data, $table)` | L27 | — | `{any}` | Generic insert |
| `insert_classification($data)` | L711 | — | `sch_classify` | Classification form |
| `insert_dept($dept)` | L746 | `department` | `department` | Dept form (duplicate check) |
| `insert_design($design)` | L798 | `designation` | `designation` | Design form |
| `menu_generation($id_profile)` | L41 | `menu`, `access`, `profile` | — | Layout generation |
| `menuDB($type, $id, $menu_array)` | L117 | `menu` | `menu` | `menu()` controller |
| `metal_ratesDB($type, $id, $data)` | ~L900 | `metal_rates` | `metal_rates` | `metal_rates()` |
| `paymodeDB($type, $id, $mode_array)` | L522 | `payment_mode` | `payment_mode` | `payment_mode()` |
| `PermissionDB($type, $id, $id_menu, $array)` | L194 | `menu`, `access`, `profile` | `access` | `permission()` |
| `profileDB($type, $id, $profile_array)` | L160 | `profile` | `profile` | `profile()` |
| `submenu($items, $parent)` | L83 | — | — | Menu generation |
| `update_metalrate_status($data, $id_branch)` | L14 | — | `branch_rate` | Branch rate update |
| `updateData($data, $id_field, $id_value, $table)` | L33 | — | `{any}` | Generic update |

---

## §4 — Table → Methods Reverse Map (Key Tables)

| Table | Read By | Written By |
|---|---|---|
| `ret_settings` | `general_settings()`, `branch_settings()`, `discount_settings()`, `ret_settings_form()` | `ret_settings_post('Update')` → `retail_settingsDB('update', $name, $data)` |
| `profile` | `profile()`, `profileDB()`, `get_access()`, `PermissionDB()` | `profile()` → Save/Update |
| `menu` | `menu()`, `menuDB()`, `PermissionDB()`, `menu_generation()` | `menu()` → Save/Update |
| `access` | `permission()`, `PermissionDB()`, `get_access()` | `permission()` → Save |
| `metal_rates` | `metal_rates()`, `matal_ratelist()`, `metal_rates_branch()` | `metal_rates()` → `metal_ratesDB('insert')` |
| `branch_rate` | `metal_rates_list()`, `getMetalRateId()`, `metal_rates_branch()` | Metal rate save |
| `bank` | `bank()`, `drawee()`, `ajax_get_bank()` | `bank()` → Save/Update |
| `payment_mode` | `payment_mode()`, `ajax_get_paymentMode()` | `payment_mode()` → `paymodeDB('insert'/'update')` |
| `company` | `company_form()`, `get_city()`, `getCompanyDetails()` | `company_post()` |
| `branch` | `branch_form()`, `ajax_get_branches()`, `get_branche()`, `get_branchcompany()` | `branch_form()` → `insert_branch()` / `update_branch()` |
| `sch_classify` | `classification_form()`, `ajax_get_classifications()` | `classification_form()` → `insert_classification()` |
| `gateway` | `gateway_form()`, `ajax_get_paymentgateway()`, `ajax_gateway_settings()` | `gateway_form()` → `insert_payment_gateway()` / `update_paymentgateway()` |
| `discount` | `discount_db('get')` | `discount_db('insert'/'update')` |
| `limit_settings` | `limit_settings_db('get')` | `limit_settings_db('update')` |
| `sms_api_settings` | `sms_apiDB('get')` | `sms_apiDB('update')` |
| `promotion_api_settings` | `promotion_crt_settings('get')` | `promotion_crt_settings('update')` |
| `village` | `ajax_village_list()`, `village_settingDB('get')` | `village_settingDB('insert'/'update'/'delete')` |
| `general` | `terms_and_conditions('get')` | `terms_and_conditions('insert'/'update'/'delete')` |
| `chit_settings` | `settingsDB('get')`, `get_gstsettings()`, `get_settings()`, `allow_autorate_update()`, `canSendNoti()` | `settingsDB('update')` |
| `inter_wallet_smssettings` | `walSMS_settings('get')` | `walSMS_settings('update')` |
| `ret_financial_year` | `get_financial_data()`, `getHeaderData()` | — (set externally) |
| `ret_noticeboard` | `getNotificationDetails()` | — (set by other module) |

---

## §5 — admin_settings_model Methods Discovered in Round 3

| Method | Line | Tables Read | Tables Written | Purpose |
|---|---|---|---|---|
| `retail_settingsDB($type, $name, $array)` | L2373 | `ret_settings` | `ret_settings` | CRUD for ret_settings (get/update/delete) |
| `get_ret_settings($settings)` | L2402 | `ret_settings` | — | Single key lookup by name |
| `discount_db($type, $id, $data)` | L1318 | `discount` | `discount` | Discount settings CRUD |
| `limit_settings_db($type, $id, $data)` | L1262 | `limit_settings` | `limit_settings` | Limit settings CRUD |
| `receipt_type()` | L1364 | `chit_settings` | — | Receipt format lookup |
| `allow_autorate_update()` | L1378 | `chit_settings` | — | Gold/silver rate config |
| `database_backup($type, $id, $array)` | L1383 | `dbbackup_log` | `dbbackup_log` | Backup log management |
| `gateway_settingsDB($type, $id, $array)` | L1452 | `gateway` | `gateway` | Payment gateway config |
| `sms_apiDB($type, $id, $data)` | L1487 | `sms_api_settings` | `sms_api_settings` | SMS API settings |
| `ajax_get_offers()` | L1513 | `offers` | — | Offers list for settings |
| `insert_offer()`, `update_offer()`, `delete_offer()` | L1548-1568 | — | `offers` | Offer management |
| `ajax_get_new_arrivals()` | L1578 | `new_arrivals` | — | New arrivals listing |
| `insert_new_arrivals()`, `update_new_arrivals()`, `delete_new_arrivals()` | L1612-1631 | — | `new_arrivals` | New arrivals CRUD |
| `ajax_get_cardbrand()` | L1633 | `card_brand` | — | Card brand list |
| `insert_card_brand()`, `update_cardbrand()`, `delete_card_brand()` | L1646-1661 | — | `card_brand` | Card brand CRUD |
| `get_branche()` | L1664 | `branch` | — | Active branches with logos |
| `ajax_get_branches()` | L1670 | `branch` | — | Datatable branch list |
| `get_branch_by_id($id)` | L1700 | `branch` | — | Single branch details |
| `insert_branch()` | L1708 | — | `branch` | New branch create |
| `update_branch()`, `update_branch_only()` | L1693-1698 | — | `branch` | Branch update |
| `insert_metalrate()` | L1717 | — | `metal_rates` (branch) | Branch metal rate |
| `metal_rates_list()`, `max_metalrate_list()` | L1729-1753 | `metal_rates`, `branch_rate` | — | Branch rate history |
| `get_branchcompany()` | L1833 | `branch`, `chit_settings`, `country`, `state`, `city` | — | Full branch+company data |
| `insert_payment_gateway()` | L1845 | — | `gateway` | New gateway |
| `update_paymentgateway()`, `delete_payment_gateway()` | L1882-1897 | — | `gateway` | Gateway management |
| `ajax_get_paymentgateway()` | L1861 | `gateway`, `branch` | — | Gateway list |
| `get_interWallet_trans*` | L1901-1977 | `inter_wallet_trans`, `inter_wallet_trans_detail` | — | Wallet reports |
| `walSMS_settings($type, $id, $data)` | L1979 | `inter_wallet_smssettings` | `inter_wallet_smssettings` | Wallet SMS config |
| `village_settingDB($type, $id, $data)` | L2072 | `village` | `village` | Village master CRUD |
| `ajax_village_list($id_village)` | L2054 | `village`, `customer` | — | Village dropdown |
| `canSendNoti($id)` | L2107 | `notification`, `chit_settings` | — | Is service subscribed? |
| `branchname_list()` | L2117 | `branch` | — | Branch name dropdown |
| `checkBalance($type)` | L2133 | `chit_settings` (msg91_authkey) | — | MSG91 balance API |
| `getBranchId($warehouse)` | L2165 | `branch` | — | Branch by warehouse code |
| `getMetalRateId($id_branch)` | L2175 | `branch_rate` | — | Active rate for branch |
| `update_silverRate($data, $id_metalrate)` | L2185 | — | `metal_rates` | Silver rate update |
| `terms_and_conditions($type, $id, $data)` | L2230 | `general` | `general` | T&C CRUD for app |
| `metal_rates_branch()` | L2264 | `metal_rates`, `branch_rate`, `chit_settings` | — | Branch-wise rate lookup |
| `get_settings()` | L2287 | `chit_settings` | — | All chit_settings |
| `import_off_excel($path, $filename, $isHeading)` | L2294 | — | — | Excel import helper |
| `get_financial_data()` | L2325 | `ret_financial_year` | — | Active financial year |
| `getHeaderData()` | L2336 | `ret_financial_year`, `profile`, `employee_settings`, `ret_day_closing`, `ret_noticeboard`, `cust_enquiry` | — | Dashboard header |
| `getBTDetails()` | L2362 | `ret_branch_transfer` | — | Pending branch transfers |
| `get_modules($code)` | L2368 | `modules` | — | Module by code |
| `get_AllProfile()` | L2440 | `profile` | — | All profiles except super |
| `get_service_by_code($serv_code)` | L2445 | `services` | — | Service by serv_code |
| `getCompanyDetails($id_branch)` | L2452 | `company`, `chit_settings`, `country`, `state`, `city`, `branch` | — | Full company data |
| `get_company_settings()` | L2475 | `chit_settings` | — | Is multi-company? |
| `giftDB($type, $id, $gift_array)` | L2482 | `gifts`, `branch` | `gifts` | Gift voucher CRUD |

---

## §6 — admin_usersms_model Methods for ret_settings CRUD (Discovered Round 3)

| Method | Tables | Purpose |
|---|---|---|
| `get_ret_settings()` | `ret_settings` | Get all ret_settings rows (list page) |
| `get_entry_recordss($id)` | `ret_settings` | Get single row for edit form |
| `get_empty_recordss()` | — | Empty record template |
| `insert_ret_settings($data)` | `ret_settings` | Insert new setting |
| `update_ret_settings($data)` | `ret_settings` | Update existing setting (WHERE `id_ret_settings`) |
| `delete_ret_settings($id)` | `ret_settings` | Hard delete by id |

> **Note:** The admin_usersms controller uses these for CRUD UI, while `admin_settings_model::retail_settingsDB()` + `get_ret_settings()` are used by other modules to read settings by name.

---

## §7 — `admin_settings_model` New Methods (Round 9 — KYC System + Ledger)

| Method | Line | Tables Read | Tables Written | Purpose |
|---|---|---|---|---|
| `getAvailablePosDevices($ledger_id)` | L2859 | `ret_bill_pay_device`, `ledger_mapping` | — | Returns POS devices NOT mapped to any other ledger (or excludes current ledger on edit). Used by Ledger CRUD |
| `kyc_master()` | L2886 | `kyc_master` | — | Returns `{id_mas_kyc, name}` for all active KYC document types |
| `get_kyc_settings()` | L2892 | `kyc_settings` | — | Returns single-row KYC settings (kyc_required, kyc_mode, kyc_verification_type, kyc_allow_type). Returns defaults if empty |
| `get_kyc_rules()` | L2905 | `kyc_rules` | — | Returns all active KYC rules with `status=1` |
| `save_kyc_settings($settings_data, $rules_data, $uid)` | L2910 | `kyc_settings`, `kyc_rules` | `kyc_settings`, `kyc_rules` | **Transactional**: upsert `kyc_settings` (1 row) + delete all `kyc_rules` WHERE status=1 + re-insert all new rules. Uses trans_begin/commit/rollback |

### New Tables Discovered (Round 9)

| Table | Schema Notes | Purpose |
|---|---|---|
| `kyc_master` | `id_mas_kyc`, `name`, `status` | KYC document type master (Aadhaar, PAN, etc.) |
| `kyc_settings` | `id_kyc_settings`, `kyc_required`, `kyc_mode`, `kyc_verification_type`, `kyc_allow_type` | Single-row KYC config |
| `kyc_rules` | `id_scheme`, `id_mas_kyc`, `rules`, `type`, `amount`, `status`, `created_by` | Per-scheme or per-customer KYC document requirements |
| `ledger_master` | `id_ledger`, `ledger_name`, `opening_balance`, `opening_date`, `min_balance`, `status` | Bank/cash ledger definitions |
| `ledger_mapping` | `id_ledger`, `type` (BANK/PAYMODE), `reference_id` | Links ledgers to banks / POS devices |
| `ret_bill_pay_device` | `id_device`, `device_name` | POS payment devices linked to ledgers |
