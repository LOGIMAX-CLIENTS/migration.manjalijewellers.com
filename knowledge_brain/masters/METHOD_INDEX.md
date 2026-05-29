# Masters Module — Method Index

> **Brain Updated:** 2026-03-25 | **Round:** R5-Upgrade | 123 ctrl + 182 model methods

---

## A. Controller Methods (Alphabetical) — `admin_settings.php` (4,837 lines)

| Method | Line | Category | Tables Read | Tables Written | Notes |
|---|---|---|---|---|---|
| `__construct()` | L18 | Init | — | — | Loads 8 models, session vars, libraries |
| `add_gift()` | L4106 | Gift | — | gift | AJAX INSERT |
| `ajax_backup_list()` | L2314 | System | backup_list | — | DataTable backup list |
| `ajax_get_bank()` | L1555 | Entity | bank | — | DataTable bank list |
| `ajax_get_branches()` | L3176 | Entity | branch | — | DataTable branch list |
| `ajax_get_cardbrand()` | L2982 | Entity | card_brand | — | DataTable cardbrand list |
| `ajax_get_classifications()` | L1544 | Entity | classification | — | DataTable list |
| `ajax_get_depts()` | L1511 | Entity | department | — | DataTable list |
| `ajax_get_designs()` | L1522 | Entity | designation | — | DataTable list |
| `ajax_get_drawee()` | L1577 | Entity | drawee | — | DataTable list |
| `ajax_get_exportlist()` | L1588 | Export | customer, scheme_account | — | Export data list |
| `ajax_get_gift()` | L4048 | Gift | gift | — | DataTable gift list |
| `ajax_get_new_arrivals()` | L2909 | Promo | new_arrivals | — | DataTable list |
| `ajax_get_offers()` | L2750 | Promo | offers | — | DataTable list |
| `ajax_get_paymentgateway()` | L3527 | Gateway | payment_gateway | — | DataTable list |
| `ajax_get_paymentMode()` | L1566 | Entity | payment_mode | — | DataTable list |
| `ajax_get_profession()` | L4190 | Entity | profession | — | DataTable list |
| `ajax_get_version()` | L4305 | System | version | — | ⚠️ raw $_POST (MST-BUG-005) |
| `ajax_get_weights()` | L1533 | Entity | weight | — | DataTable list |
| `ajax_paymentgateway()` | L3726 | Gateway | payment_gateway, gateway_settings | — | AJAX gateway data |
| `ajax_village_list()` | L3786 | Entity | village | — | DataTable village list |
| `bank()` | L396 | Entity | bank, drawee | bank | CRUD switch |
| `branch_form()` | L3027 | Branch | branch, metal_rate_settings, chit_settings | branch, metal_rate_settings, chit_settings, employee_settings | Complex CRUD ⚠️ MST-BUG-028 |
| `branch_settings()` | L2989 | Branch | branch, chit_settings | chit_settings | Per-branch config |
| `cardbrand_form()` | L2920 | Entity | card_brand | card_brand | CRUD switch |
| `classification_form()` | L1316 | Entity | classification | classification | CRUD + image |
| `clear_database()` | L2190 | System | — | ALL core tables (TRUNCATE) | ⚠️ P0 (MST-BUG-001) |
| `comp_list()` | L1215 | Company | company | — | List view |
| `company_form()` | L1076 | Company | company, country | — | Form view |
| `company_post()` | L1093 | Company | — | company | Save/update |
| `compress()` | L3889 | Export | filesystem | filesystem (zip) | Dead $data (MST-BUG-035) |
| `config_setting()` | L4448 | System | config | — | Config view |
| `config_setupdate()` | L4458 | System | — | config | Config save |
| `contact_already_exist()` | L1233 | Validation | customer | — | Mobile uniqueness check |
| `db_backup()` | L2275 | System | ALL | filesystem | ⚠️ No role check (MST-BUG-033) |
| `dept_form()` | L1383 | Entity | department | department | CRUD switch |
| `design_form()` | L1447 | Entity | designation | designation | CRUD switch |
| `discount_settings()` | L2487 | Settings | chit_discount | chit_discount | Discount config |
| `download()` | L3874 | Export | filesystem | — | ⚠️ No role check (MST-BUG-034) |
| `drawee()` | L831 | Entity | drawee | drawee | CRUD switch |
| `gateway_form()` | L3449 | Gateway | payment_gateway | payment_gateway | Retail gateway CRUD |
| `gateway_settings()` | L2320 | Gateway | gateway_settings | gateway_settings | Chit gateway config |
| `general_settings()` | L1707 | Settings | chit_settings, chit_limit, chit_discount, config | chit_settings, chit_limit, chit_discount | Core settings CRUD |
| `get_acc_format_details()` | L4333 | System | config | — | Account format lookup |
| `get_access_rights()` | L987 | RBAC | access, menu | — | Access rights view |
| `get_active_schemes()` | L4758 | Scheme | scheme | — | Active scheme list |
| `get_all_gifts()` | L4077 | Gift | gift | — | All gifts JSON |
| `get_city()` | L1061 | Geo | city | — | ⚠️ raw $_POST (MST-BUG-006) |
| `get_country()` | L995 | Geo | country | — | Country dropdown |
| `get_countryCurrency()` | L1030 | Geo | country | — | Currency by country |
| `get_gift_name_byId()` | L4070 | Gift | gift | — | ⚠️ raw $_POST (MST-BUG-004) |
| `get_languages_known()` | L4610 | Entity | languages | — | Language list |
| `get_last_version()` | L4319 | System | version | — | Latest version |
| `get_otp_giftstatus()` | L4206 | Gift | gift | — | Gift OTP status |
| `get_page_title()` | L4326 | System | menu | — | Page title by URL |
| `get_profession()` | L4201 | Entity | profession | — | Profession by ID |
| `get_receipt_format_details()` | L4389 | System | chit_settings | — | Receipt format |
| `get_reports()` | L3869 | Reports | — | — | Report list view |
| `get_scheme_id()` | L1247 | Scheme | scheme | — | Scheme by name |
| `get_SMS_data()` | L775 | SMS | notification | — | SMS template data |
| `get_state()` | L1053 | Geo | state | — | ⚠️ raw $_POST (MST-BUG-006) |
| `get_village_by_pincode()` | L1069 | Geo | village | — | ⚠️ raw $_POST (MST-BUG-006) |
| `get_weight_scheme_id()` | L1254 | Scheme | scheme | — | Scheme by weight |
| `getCustomerByMobile()` | L1687 | Customer | customer | — | Customer lookup |
| `getSchemeAccountByCustomer()` | L1694 | Account | scheme_account | — | Account by customer |
| `gift()` | L3989 | Gift | gift | gift | CRUD switch |
| `index()` | L42 | Init | — | — | Redirect |
| `interWalletAcc_backup()` | L3556 | Wallet | inter_wallet_trans | — | Wallet backup |
| `is_mobile_exists()` | L1222 | Validation | customer | — | Mobile exists check |
| `is_refno_exists()` | L1701 | Validation | — | — | Ref number check |
| `kyc_master()` | L4752 | KYC | kyc_master | kyc_master | KYC config |
| `kyc_settings()` | L4764 | KYC | kyc_settings | kyc_settings | KYC rules |
| `ledger()` | L4616 | Entity | ledger | ledger | Ledger CRUD ⚠️ no numeric validation (MST-BUG-032) |
| `limit_settings()` | L2449 | Settings | chit_limit | chit_limit | Limit config |
| `mail_settings()` | L2412 | Settings | mail_settings | mail_settings | SMTP config |
| `matal_ratelist()` | L3186 | Rate | metal_rates, branch_rate | — | Rate list per branch |
| `menu()` | L48 | RBAC | menu | menu | Menu CRUD |
| `metal_rates()` | L515 | Rate | metal_rates, branch_rate, chit_settings | metal_rates, branch_rate, filesystem | ⚠️ MST-BUG-003/042/044 |
| `metal_rates_discount()` | L3753 | Rate | chit_settings | — | Rate discount data |
| `new_arrivals_form()` | L2770 | Promo | new_arrivals | new_arrivals | CRUD + image |
| `notification()` | L4584 | Push | notification | notification | CRUD |
| `offers_form()` | L2528 | Promo | offers | offers | CRUD + image |
| `onesignalNotificationToAll()` | L3692 | Push | — | — (external API) | OneSignal dispatch |
| `otpcredit_settings()` | L3316 | Settings | otp_credit_settings | otp_credit_settings | OTP credit |
| `payment_charges()` | L1602 | Entity | payment_charges | payment_charges | CRUD switch |
| `payment_mode()` | L890 | Entity | payment_mode | payment_mode | CRUD switch |
| `permission()` | L283 | RBAC | menu, access, dashboard_menu, dashboard_access | access, dashboard_access | Permission matrix |
| `profession_form()` | L4126 | Entity | profession | profession | CRUD switch |
| `profile()` | L116 | RBAC | profile | profile | Profile CRUD |
| `promotion_api_settings()` | L3285 | Settings | promotion_sms_settings | promotion_sms_settings | Promo SMS config |
| `promotioncredit__settings()` | L3251 | Settings | promotion_credit | promotion_credit | Promo credit |
| `quick_link()` | L4531 | System | quick_link | quick_link | Activation |
| `quick_link_revert()` | L4558 | System | quick_link | quick_link | Deactivation |
| `receipt_settings()` | L1035 | Settings | receipt_settings | receipt_settings | Receipt format |
| `ref_benefits_setting()` | L3386 | Settings | ref_benefits | ref_benefits | Referral config |
| `rrmdir()` | L2262 | Util | filesystem | filesystem | Recursive dir delete |
| `schemeacc_no_settings()` | L3215 | Settings | config | config | Account number format |
| `send_bulk_sms()` | L792 | SMS | — | — (external API) | ⚠️ Hardcoded creds (MST-BUG-040) |
| `send_RatesToAllUsers()` | L3583 | Push | notification, customer | — (external API) | ⚠️ MST-BUG-029/044 |
| `send_singlealert_rate_notification()` | L3954 | Push | notification | — (external API) | Single notify |
| `set__branch_image()` | L2657 | Image | — | branch (logo), filesystem | 0777 (MST-BUG-014) |
| `set__clsfy_image()` | L3426 | Image | — | classification (image), filesystem | 0777 |
| `set__paymentgateway_image()` | L3534 | Image | — | payment_gateway (image), filesystem | 0777 |
| `set_image()` | L2682 | Image | — | offers/new_arrivals (image), filesystem | 0777 |
| `sms_api_settings()` | L2381 | Settings | sms_api | sms_api | SMS API config |
| `terms_conditions()` | L3898 | Entity | terms_conditions | terms_conditions | CRUD |
| `truncateFromArray()` | L2253 | System | — | `{any}` (TRUNCATE) | Used by clear_database |
| `update_country()` | L1000 | Geo | — | country | Country update |
| `update_discout()` | L3762 | Rate | — | chit_settings | Discount update |
| `update_gateway()` | L3735 | Gateway | — | payment_gateway | Gateway update |
| `update_gift()` | L4084 | Gift | — | gift | AJAX UPDATE |
| `update_gift_status()` | L4059 | Gift | — | gift | Status toggle |
| `update_notification_status()` | L4597 | Push | — | notification | Status toggle |
| `update_prosms()` | L821 | SMS | — | promo_sms | SMS update |
| `update_rate_file()` | L494 | Rate | metal_rates | filesystem | ⚠️ Dead code (MST-BUG-042) |
| `upload_img()` | L2719 | Image | filesystem | filesystem | GD image processing |
| `valid_str()` | L387 | Validation | — | — | CI callback |
| `version_details()` | L4214 | System | version | version | Version CRUD |
| `village_form()` | L3795 | Entity | village | village | CRUD |
| `village_list()` | L3779 | Entity | village | — | List view |
| `wallettype_account()` | L3351 | Wallet | wallet_type | wallet_type | Wallet type CRUD |
| `weight_form()` | L1261 | Entity | weight | weight | CRUD switch |

**Total: 123 methods** (incl. constructor)

---

## B. Model Methods (Alphabetical) — `admin_settings_model.php` (2,944 lines)

> Due to the massive scale (182 methods), this section groups methods by table/domain. Each method includes line number and key table(s).

### B1. Core RBAC/Menu

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `DashboardPermissionDB()` | L291 | dashboard_access, dashboard_menu | Dashboard RBAC CRUD |
| `get_access($url)` | L96 | access, menu, profile | ⚠️ RBAC engine — SQLi risk (MST-BUG-002) |
| `get_AllProfile()` | L2459 | profile | All profiles list |
| `get_dashboard_access()` | L106 | dashboard_access, dashboard_menu, profile | Dashboard permissions |
| `get_menu_link()` | L398 | menu | Menu link by ID |
| `menu_generation($id_profile)` | L41 | menu, access, profile | ⚠️ HTML menu builder, raw $id_profile (MST-BUG-012) |
| `menuDB()` | L117 | menu | Menu CRUD switch |
| `menuPermission()` | L2708 | access, menu | Menu perm check |
| `PermissionDB()` | L194 | access, menu | ⚠️ Hardcoded IDs 17/18 (MST-BUG-016) |
| `profileDB()` | L160 | profile | Profile CRUD switch |
| `submenu()` | L83 | — | Helper for menu_generation |

### B2. Entity Masters (Generic CRUD)

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `ajax_get_charges()` | L950 | payment_charges | DataTable |
| `ajax_get_classifications()` | L698 | classification | DataTable |
| `ajax_get_depts()` | L732 | department | DataTable |
| `ajax_get_designs()` | L784 | designation | DataTable |
| `ajax_get_weights()` | L660 | weight | DataTable |
| `bankDB()` | L405 | bank | Bank CRUD switch |
| `delete_card_brand()` | L1657 | card_brand | Delete |
| `delete_charges()` | L1012 | payment_charges | Delete |
| `delete_classification()` | L725 | classification | Delete |
| `delete_dept()` | L778 | department | Delete |
| `delete_design()` | L828 | designation | Delete |
| `delete_weight()` | L685 | weight | Delete |
| `draweeDB()` | L462 | drawee | Drawee CRUD switch |
| `get_card_brand()` | L1639 | card_brand | Get by ID |
| `get_charges()` | L962 | payment_charges | Get by ID |
| `get_charges_range()` | L969 | payment_charges | Get range |
| `get_classification()` | L705 | classification | Get by ID |
| `get_classifications()` | L692 | classification | Get all |
| `get_dept()` | L738 | department | Get by ID |
| `get_design()` | L790 | designation | Get by ID |
| `get_service()` | L955 | services | Get by ID |
| `get_service_by_code()` | L2464 | services | Get by code |
| `get_weight()` | L667 | weight | Get by ID |
| `get_weights()` | L653 | weight | Get all |
| `giftDB()` | L2501 | gift | Gift CRUD switch |
| `insert_card_brand()` | L1646 | card_brand | Insert |
| `insert_charges()` | L976 | payment_charges | Insert |
| `insert_classification()` | L711 | classification | Insert |
| `insert_dept()` | L746 | department | Insert |
| `insert_design()` | L798 | designation | Insert |
| `insert_weight()` | L674 | weight | Insert |
| `paymodeDB()` | L522 | payment_mode | PayMode CRUD switch |
| `update_cardbrand()` | L1651 | card_brand | Update |
| `update_charges()` | L993 | payment_charges | Update |
| `update_classification()` | L718 | classification | Update |
| `update_dept()` | L759 | department | Update |
| `update_design()` | L809 | designation | Update |
| `update_weight()` | L679 | weight | Update |

### B3. Metal Rates

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `get_branch_rate($id_branch)` | L20 | metal_rates, branch_rate | ⚠️ Raw SQL (MST-BUG-007) |
| `insert_metalrate()` | L1717 | metal_rates | Insert rate |
| `max_metalrate()` | L1018 | metal_rates, branch_rate | ⚠️ Raw SQL (MST-BUG-008) |
| `max_metalrate_list()` | L1744 | metal_rates | Latest rate |
| `metal_rate_type()` | L1722 | metal_rate_settings | Rate display config |
| `metal_rates_branch()` | L2264 | metal_rates, branch_rate | Branch rates list |
| `metal_rates_list()` | L1729 | metal_rates | All rates list |
| `metal_ratesDB()` | L1026 | metal_rates | Rate CRUD switch |
| `rates_by_branch()` | L1100 | metal_rates, branch_rate | Branch-specific rates |
| `update_metalrate_status()` | L14 | branch_rate | Status toggle |

### B4. Settings & Config

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `allow_autorate_update()` | L1378 | chit_settings | Auto-rate flag |
| `configDB()` | L2621 | config | Config CRUD |
| `discount_db()` | L1318 | chit_discount | Discount CRUD |
| `get_company_settings()` | L2494 | chit_settings | Multi-company flag |
| `get_gstsettings()` | L1757 | gst_settings | GST config |
| `get_ret_settings()` | L2402 | ret_settings | Retail settings |
| `get_schdebit_settings()` | L1763 | sch_debit_settings | Scheme debit config |
| `get_settings()` | L2287 | chit_settings | Settings by key |
| `limitDB()` | L1247 | chit_limit | Limit CRUD |
| `receipt_type()` | L1364 | receipt_type | Receipt type |
| `retail_settingsDB()` | L2373 | ret_settings | Retail settings CRUD |
| `setting_data()` | L1156 | chit_settings | Settings single-row |
| `settingsDB()` | L1162 | chit_settings | ⚠️ Central settings CRUD — raw $settings (MST-BUG-009) |

### B5. Branch Management

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `ajax_get_branches()` | L1670 | branch | DataTable |
| `ajax_get_cardbrand()` | L1633 | card_brand | DataTable |
| `branchname_list()` | L2117 | branch | Active branches |
| `get_branch_by_id()` | L1700 | branch | Get by ID |
| `get_branch_edit()` | L2219 | branch, metal_rate_settings | Edit data |
| `get_branchcompany()` | L1833 | branch, company | Branch-company map |
| `get_branche()` | L1664 | branch | Single branch |
| `get_branches()` | L1680 | branch | All branches |
| `get_branches_for_rate()` | L2045 | branch | Rate entry branches |
| `getBranchDetails()` | L2761 | branch | Branch details |
| `getBranchId()` | L2165 | branch | ⚠️ Raw SQL (MST-BUG-010) |
| `insert_branch()` | L1708 | branch | Insert |
| `update_branch()` | L1693 | branch | Update |
| `update_branch_only()` | L1686 | branch | Partial update |

### B6. Company Management

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `company_empty_record()` | L834 | — | Empty form template |
| `create_company()` | L913 | company | Insert |
| `get_comp_list()` | L863 | company | All companies |
| `get_company()` | L896 | company | Get by ID |
| `get_company_detail()` | L869 | company | Detail view |
| `get_curr_detail()` | L889 | country | Currency detail |
| `getCompanyDetails()` | L2471 | company | Full company info |
| `update_company()` | L918 | company | Update |
| `update_default_country()` | L924 | company | Default country |

### B7. Geo (Country/State/City/Village)

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `ajax_village_list()` | L2054 | village | DataTable |
| `get_city()` | L621 | city | Cities by state |
| `get_country()` | L591 | country | All countries |
| `get_default_city()` | L884 | city | Default city |
| `get_default_country()` | L874 | country | Default country |
| `get_default_state()` | L879 | state | Default state |
| `get_state()` | L607 | state | States by country |
| `update_country()` | L2735 | country | Update |
| `village_settingDB()` | L2072 | village | Village CRUD |

### B8. Gateway & Payment

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `ajax_get_paymentgateway()` | L1861 | payment_gateway | DataTable |
| `ajax_gateway_settings()` | L2022 | gateway_settings | Gateway data |
| `delete_payment_gateway()` | L1882 | payment_gateway | Delete |
| `gateway_settingsDB()` | L1452 | gateway_settings | Gateway CRUD switch |
| `get_paymentgateway()` | L1870 | payment_gateway | Get by ID |
| `insert_payment_gateway()` | L1845 | payment_gateway | Insert |
| `update_gateway()` | L2031 | gateway_settings | Update |
| `update_paymentgateway()` | L1888 | payment_gateway | Update |

### B9. Promotions & Notifications

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `ajax_get_new_arrivals()` | L1578 | new_arrivals | DataTable |
| `ajax_get_offers()` | L1513 | offers | DataTable |
| `ajax_get_profession()` | L2568 | profession | DataTable |
| `canSendNoti()` | L2107 | chit_settings | Notification flag |
| `delete_new_arrivals()` | L1627 | new_arrivals | Delete |
| `delete_offer()` | L1564 | offers | Delete |
| `delete_profession()` | L2613 | profession | Delete |
| `get_new_arrivals()` | L1584 | new_arrivals | Get by ID |
| `get_newArival_imgpath()` | L2756 | new_arrivals | Image path |
| `get_offers()` | L1519 | offers | Get by ID |
| `getExpiredData()` | L1570 | offers | Expired offers |
| `getnotification()` | L2746 | notification | Notification config |
| `getNotificationDetails()` | L2418 | notification, chit_settings | Customer FCM tokens |
| `get_imgpath()` | L2751 | offers | Image path |
| `insert_new_arrivals()` | L1612 | new_arrivals | Insert |
| `insert_offer()` | L1548 | offers | Insert |
| `insert_profession()` | L2575 | profession | Insert |
| `isPopupExist()` | L1526 | offers | Popup check |
| `new_arrivals_empty_record()` | L1591 | — | Empty template |
| `offer_empty_record()` | L1533 | — | Empty template |
| `promotion_crt_settings()` | L1780 | promotion_credit | Promo credit |
| `update_gift_status()` | L2563 | gift | Status toggle |
| `update_new_arrivals()` | L1620 | new_arrivals | Update |
| `update_notification_status()` | L2741 | notification | Status toggle |
| `update_offer()` | L1556 | offers | Update |
| `update_profession()` | L2594 | profession | Update |

### B10. SMS/OTP

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `otp_crt_settings()` | L1808 | otp_credit_settings | OTP credit CRUD |
| `sms_apiDB()` | L1487 | sms_api | SMS API CRUD |
| `walSMS_settings()` | L1979 | wallet_sms_settings | Wallet SMS config |

### B11. Wallet & Inter-Wallet

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `checkBalance()` | L2133 | wallet_account | Balance check |
| `get_interWallet_trans()` | L1962 | inter_wallet_trans | Trans list |
| `get_interWallet_trans_by_Filter()` | L1901 | inter_wallet_trans | Filtered trans |
| `get_interWallet_trans_temp()` | L2007 | inter_wallet_trans_temp | Temp trans |
| `getWalletData()` | L1851 | wallet_type | Wallet types |

### B12. Misc/System

| Method | Line | Tables | Purpose |
|---|---|---|---|
| `accno_generatorset()` | L1770 | config | Account number format |
| `database_backup()` | L1383 | — | DB backup via dbutil |
| `deleteData()` | L2723 | `{any}` | Generic delete |
| `executeQry()` | L1374 | `{any}` | Generic query executor |
| `get_all_gifts()` | L2550 | gift | All gifts |
| `get_financial_data()` | L2325 | financial_year | FY data |
| `get_languages_known()` | L2767 | languages | Language list |
| `get_last_version()` | L2701 | version | Latest version |
| `get_modules()` | L2368 | modules | Module list |
| `get_profession()` | L2586 | profession | Get by ID |
| `get_quick_link()` | L2714 | quick_link | Quick link status |
| `get_rate_diff()` | L2127 | metal_rates | Rate difference |
| `get_reports()` | L2160 | — | Report config |
| `get_version_data()` | L2695 | version | ⚠️ Raw SQL (MST-BUG-011) |
| `getAllBranchDCData()` | L2413 | — | Branch day close data |
| `getAvailablePosDevices()` | L2859 | pos_devices | POS devices |
| `getBTDetails()` | L2362 | bt_details | BT details |
| `getHeaderData()` | L2336 | — | Header data |
| `getMetalRateId()` | L2175 | metal_rates | Rate ID lookup |
| `getRef_nos()` | L2191 | ref_nos | Ref numbers |
| `getPreviousDateStatuslog()` | L2729 | status_log | Previous log |
| `import_excel()` | L1125 | `{any}` | Excel import |
| `import_off_excel()` | L2294 | `{any}` | Offline excel import |
| `insert_import_log()` | L908 | import_log | Import audit |
| `insertData()` | L27 | `{any}` | Generic insert |
| `ledgerDB()` | L2773 | ledger | Ledger CRUD |
| `terms_and_conditions()` | L2230 | terms_conditions | T&C CRUD |
| `update_silverRate()` | L2185 | metal_rates | Silver rate update |
| `updateData()` | L33 | `{any}` | Generic update |
| `versionDB()` | L2654 | version | Version CRUD |
| `get_kyc_rules()` | L2905 | kyc_rules | KYC rules |
| `get_kyc_settings()` | L2892 | kyc_settings | KYC settings |
| `kyc_master()` | L2886 | kyc_master | KYC master |
| `save_kyc_settings()` | L2910 | kyc_settings | Save KYC settings |
| `getBranchDayClosingData()` | L2407 | day_closing | Day close data |

**Total: 182 methods** (incl. constructor)

---

## C. JS AJAX Map Overview — `admin_settings.js` (6,614 lines, 89 endpoints)

> 89 AJAX endpoints. Key cross-module calls listed here; full map in JS file with `url:` grep.

### Cross-Module AJAX (JS calls to other controllers)

| JS Line | AJAX URL | Target Module |
|---|---|---|
| L634 | `settings/import/send_login` | Import |
| L653 | `settings/import/send_login_email` | Import |
| L3672 | `branch/branch_list` | Branch |
| L3756 | `branch/branch_name/add` | Branch |
| L3773 | `branch/branch_name/edit/{id}` | Branch |
| L3944 | `branch/branch_name/update/{id}` | Branch |
| L3978 | `branch/branchname_list` | Branch |
| L4031 | `branch/metal_rate/ajax_list/{id}` | Branch |
| L4630 | `branch/branchname_list` | Branch |

---

## D. Table → Method Reverse Map (Key Tables Only)

| Table | Read By (Model) | Written By (Model) |
|---|---|---|
| `chit_settings` | settingsDB, setting_data, get_settings, get_company_settings, canSendNoti, allow_autorate_update, getNotificationDetails | settingsDB |
| `metal_rates` | get_branch_rate, max_metalrate, max_metalrate_list, metal_rates_list, metal_rates_branch, rates_by_branch, getMetalRateId, metal_ratesDB | insert_metalrate, metal_ratesDB, update_silverRate |
| `branch` | ajax_get_branches, branchname_list, get_branch_by_id, get_branch_edit, get_branche, get_branches, get_branches_for_rate, getBranchDetails, getBranchId, get_branchcompany | insert_branch, update_branch, update_branch_only |
| `access` | get_access, get_dashboard_access, menuPermission | PermissionDB |
| `menu` | menu_generation, get_menu_link, menuPermission, PermissionDB | menuDB |
| `profile` | menu_generation, get_access, get_AllProfile, profileDB | profileDB |
| `offers` | ajax_get_offers, get_offers, get_imgpath, isPopupExist, getExpiredData | insert_offer, update_offer, delete_offer |
| `payment_gateway` | ajax_get_paymentgateway, get_paymentgateway | insert_payment_gateway, update_paymentgateway, delete_payment_gateway |
| `gateway_settings` | ajax_gateway_settings, gateway_settingsDB | gateway_settingsDB, update_gateway |
| `village` | ajax_village_list, village_settingDB | village_settingDB |
| `company` | get_comp_list, get_company, get_company_detail, getCompanyDetails, get_branchcompany | create_company, update_company |
