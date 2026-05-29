# Chit Settings Module — Method Index

> **🔄 UPGRADED BRAIN**
> Previous version: R1 (2026-03-06) — 120 ctrl / 178 model
> Upgraded to: R2-Upgrade (2026-03-25)
> Changes: +3 new controller methods (KYC), +4 new model methods (KYC), 2 controller methods documented, all line numbers updated, +128 controller lines, +78 model lines

---

## Controller Methods (`admin_settings.php` — 4,837 lines, 123 methods)

### Group 1: Core / Constructor
| # | Method | Lines | Purpose |
|---|---|---|---|
| 1 | `__construct()` | L18-41 | Load 9 models, 2 libraries, session gate |
| 2 | `index()` | L42-46 | Company settings page |
| 3 | `valid_str($str)` | L387-395 | Whitespace validation |

### Group 2: Menu Management
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 4 | `menu($type, $id)` | L48-115 | menu, access, profile | Menu CRUD + JSON |

### Group 3: Profile Management
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 5 | `profile($type, $id)` | L116-282 | profile, access | Profile CRUD (List/View/Save/Update/Delete) |

### Group 4: Permission System
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 6 | `permission($type, $id)` | L283-386 | menu, access, profile, dashboard_menu, dashboard_access | Menu + dashboard permission CRUD |

### Group 5: Bank Master
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 7 | `bank($type, $id)` | L396-493 | bank | Bank CRUD with duplicate check |

### Group 6: Metal Rates
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 8 | `update_rate_file($rates)` | L494-514 | — (file write) | Write rate.txt |
| 9 | `metal_rates($type, $id)` | L515-774 | metal_rates, branch_rate, chit_settings | Rate CRUD + branch mapping |
| 10 | `metal_rates_discount()` | L3753-3761 | chit_settings | Get rate discounts |
| 11 | `update_discout()` | L3762-3778 | chit_settings | Update rate discounts |
| 12 | `matal_ratelist($id)` | L3186-3214 | metal_rates, branch_rate | Branch rate list |

### Group 7: SMS/Notification
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 13 | `get_SMS_data($serviceID)` | L775-791 | customer (mobile) | Bulk SMS data prep |
| 14 | `send_bulk_sms($data)` | L792-820 | — | SMS API call |
| 15 | `update_prosms($mob_length)` | L821-830 | — | SMS credit update |
| 16 | `send_RatesToAllUsers($branchArr)` | L3583-3691 | — | Rate notification push |
| 17 | `onesignalNotificationToAll(...)` | L3692-3725 | — | OneSignal push API |
| 18 | `send_singlealert_rate_notification(...)` | L3954-3988 | — | Single rate notification |

### Group 8: Master Data CRUD (Drawee/PayMode/Weight/Classification/Dept/Design)
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 19 | `drawee($type, $id)` | L831-889 | drawee_account, bank | Drawee CRUD |
| 20 | `payment_mode($type, $id)` | L890-986 | payment_mode | Pay mode CRUD |
| 21 | `weight_form($type, $id)` | L1261-1315 | weight | Weight CRUD |
| 22 | `classification_form($type, $id)` | L1316-1382 | sch_classify | Classification CRUD |
| 23 | `dept_form($type, $id)` | L1383-1446 | department | Department CRUD |
| 24 | `design_form($type, $id)` | L1447-1510 | designation | Designation CRUD |
| 25 | `profession_form($type, $id)` | L4126-4189 | profession | Profession CRUD — ✅ improved duplicate detection (2025-07-10) |
| 26 | `village_form($type, $id)` | L3795-3868 | village | Village CRUD |

### Group 9: AJAX Data Providers
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 27 | `ajax_get_depts()` | L1511-1521 | department | JSON dept list |
| 28 | `ajax_get_designs()` | L1522-1532 | designation | JSON designation list |
| 29 | `ajax_get_weights()` | L1533-1543 | weight | JSON weight list |
| 30 | `ajax_get_classifications()` | L1544-1554 | sch_classify | JSON classifications |
| 31 | `ajax_get_bank()` | L1555-1565 | bank | JSON bank list |
| 32 | `ajax_get_paymentMode()` | L1566-1576 | payment_mode | JSON pay modes |
| 33 | `ajax_get_drawee()` | L1577-1587 | drawee_account | JSON drawees |
| 34 | `ajax_get_exportlist()` | L1588-1601 | export data | JSON exports |
| 35 | `ajax_get_branches()` | L3176-3185 | branch | JSON branches |
| 36 | `ajax_get_offers()` | L2750-2769 | offers | JSON offers |
| 37 | `ajax_get_new_arrivals()` | L2909-2919 | new_arrivals | JSON arrivals |
| 38 | `ajax_get_cardbrand()` | L2982-2988 | card_brand | JSON card brands |
| 39 | `ajax_get_gift()` | L4048-4058 | gift | JSON gifts |
| 40 | `ajax_get_profession()` | L4190-4200 | profession | JSON professions |
| 41 | `ajax_get_version()` | L4305-4318 | version_details | JSON versions |
| 42 | `ajax_backup_list()` | L2314-2319 | db_backup | JSON backups |
| 43 | `ajax_village_list()` | L3786-3794 | village | JSON villages |
| 44 | `ajax_paymentgateway()` | L3726-3734 | gateway_settings | JSON gateways |

### Group 10: General Settings (CRITICAL — chit_settings table)
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 45 | `general_settings($type, $id)` | L1707-2183 | chit_settings, config_settings, company, country, + more | 60+ flag save/update |
| 46 | `get_otpsettings()` | L2184-2189 | chit_settings | OTP config JSON |

### Group 11: System Utilities
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 47 | `clear_database()` | L2190-2252 | ALL (TRUNCATE) | ⚠️ Wipe all data |
| 48 | `truncateFromArray($tables)` | L2253-2261 | — | Truncate loop |
| 49 | `rrmdir($path)` | L2262-2274 | — | Recursive dir delete |
| 50 | `db_backup()` | L2275-2313 | db_backup | Full DB backup ZIP |

### Group 12: Integration Settings
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 51 | `gateway_settings($type, $id)` | L2320-2380 | gateway_settings | PayU demo/pro save |
| 52 | `sms_api_settings($type, $id)` | L2381-2411 | sms_api_settings | SMS API save |
| 53 | `mail_settings($type, $id)` | L2412-2448 | company | Email settings |
| 54 | `limit_settings($type, $id)` | L2449-2486 | chit_settings | Limit settings |
| 55 | `discount_settings($type, $id)` | L2487-2527 | chit_settings, scheme | Discount settings |

### Group 13: Company
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 56 | `company_form($type, $id)` | L1076-1092 | company | Company form |
| 57 | `company_post($type, $id)` | L1093-1214 | company | Company save/update |
| 58 | `comp_list()` | L1215-1221 | company | Company list |

### Group 14: Geography
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 59 | `get_country()` | L995-999 | country | Country JSON |
| 60 | `update_country()` | L1000-1029 | country | Default country update |
| 61 | `get_countryCurrency($id)` | L1030-1034 | country | Currency by country |
| 62 | `get_state()` | L1053-1060 | state | States JSON |
| 63 | `get_city()` | L1061-1068 | city | Cities JSON |
| 64 | `get_village_by_pincode()` | L1069-1075 | village | Village lookup |
| 65 | `village_list()` | L3779-3785 | village | Village list page |

### Group 15: Branch
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 66 | `branch_settings($id)` | L2989-3026 | branch | Branch settings update |
| 67 | `branch_form($type, $id, $status)` | L3027-3175 | branch | Branch CRUD |

### Group 16: Offers/Arrivals/Cards/Gifts/Version/Terms/Notification/Ledger
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 68 | `offers_form($type, $id)` | L2528-2656 | offers | Offer CRUD |
| 69 | `new_arrivals_form($type, $id)` | L2770-2908 | new_arrivals | New arrivals CRUD |
| 70 | `cardbrand_form($type, $id)` | L2920-2981 | card_brand | Card brand CRUD |
| 71 | `gift($type, $id)` | L3989-4047 | gift | Gift CRUD |
| 72 | `version_details($type, $id)` | L4214-4304 | version_details | Version CRUD — ⚠️ hardcoded CURL to pm.logimaxindia.com L4249 |
| 73 | `terms_conditions($type, $id)` | L3898-3953 | terms_and_conditions | T&C CRUD |
| 74 | `notification($type, $id)` | L4584-4596 | notification | Notification CRUD |
| 75 | `update_notification_status($status, $id)` | L4597-4607 | notification | ⚠️ Status via GET — $status raw in redirect |
| 76 | `ledger($type, $id)` | L4616-4750 | ledger_master, ledger_mapping, bank, ret_bill_pay_device | **NEW** — Bank ledger CRUD with mapping |

### Group 17: Misc/Internal
| # | Method | Lines | Purpose |
|---|---|---|---|
| 77-81 | Image uploads | Various | `set_image` L2682, `set__branch_image` L2657, `set__clsfy_image` L3426, `set__paymentgateway_image` L3534, `upload_img` L2719 |
| 82-86 | Lookups | Various | `is_mobile_exists` L1222, `contact_already_exist` L1233, `get_scheme_id` L1247, `get_weight_scheme_id` L1254, `get_access_rights` L987 |
| 87-91 | Config/Format | Various | `config_setting` L4448, `config_setupdate` L4458, `get_acc_format_details` L4333, `get_receipt_format_details` L4389, `get_page_title` L4326 |
| 92-96 | Gift helpers | Various | `update_gift_status` L4059, `get_gift_name_byId` L4070, `get_all_gifts` L4077, `update_gift` L4084, `add_gift` L4106 |
| 97-101 | Settings sub-modules | Various | `receipt_settings` L1035, `schemeacc_no_settings` L3215, `payment_charges` L1602, `getCustomerByMobile` L1687, `getSchemeAccountByCustomer` L1694 |
| 102-106 | Settings sub-modules | Various | `promotioncredit__settings` L3251, `promotion_api_settings` L3285, `otpcredit_settings` L3316, `wallettype_account` L3351, `ref_benefits_setting` L3386 |
| 107-111 | Gateway/Misc | Various | `gateway_form` L3449, `ajax_get_paymentgateway` L3527, `update_gateway` L3735, `interWalletAcc_backup` L3556, `get_otp_giftstatus` L4206 |
| 112-116 | Quick Links/Reports | Various | `quick_link` L4531, `quick_link_revert` L4558, `get_reports` L3869, `download` L3874, `compress` L3889 |
| 117-118 | Misc | Various | `get_languages_known` L4610, `get_last_version` L4319, `get_profession` L4201, `is_refno_exists` L1701 |

### Group 18: KYC Settings (NEW — 2026-03-25)
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 119 | `kyc_master()` | L4752-4756 | kyc_master | **NEW** — Get KYC document types (active) |
| 120 | `get_active_schemes()` | L4758-4762 | scheme | **NEW** — Active schemes for KYC rules — ⚠️ uses payment_model not settings_model |
| 121 | `kyc_settings($type)` | L4764-4836 | kyc_settings, kyc_rules | **NEW** — KYC settings CRUD — scheme-wise or customer-wise rules |

---

## Model Methods (`admin_settings_model.php` — 2,944 lines, 182 methods)

> **Upgrade Note**: R1 had 178 methods grouped; R2 adds 4 new KYC methods. Full individual listings below for groups that expanded.

| Group | Methods | Count | Tables |
|---|---|---|---|
| Generic CRUD | `insertData` L27, `updateData` L33, `deleteData` L2723 | 3 | Any |
| Menu | `menu_generation` L41, `submenu` L83, `menuDB` L117, `get_menu_link` L398, `menuPermission` L2708 | 5 | menu, access, profile |
| Permission | `get_access` L96, `get_dashboard_access` L106, `PermissionDB` L194, `DashboardPermissionDB` L291 | 4 | access, menu, profile, dashboard_* |
| Profile | `profileDB` L160, `get_AllProfile` L2459 | 2 | profile |
| Bank | `bankDB` L405 | 1 | bank |
| Drawee | `draweeDB` L462 | 1 | drawee_account, bank |
| Payment Mode | `paymodeDB` L522 | 1 | payment_mode |
| Geography | `get_country` L591, `get_state` L607, `get_city` L621, `update_country` L2735 | 4 | country, state, city |
| Weight | `get_weights` L653, `ajax_get_weights` L660, `get_weight` L667, `insert_weight` L674, `update_weight` L679, `delete_weight` L685 | 6 | weight |
| Classification | `get_classifications` L692, `ajax_get_classifications` L698, `get_classification` L705, `insert_classification` L711, `update_classification` L718, `delete_classification` L725 | 6 | sch_classify |
| Department | `ajax_get_depts` L732, `get_dept` L738, `insert_dept` L746, `update_dept` L759, `delete_dept` L778 | 5 | department |
| Designation | `ajax_get_designs` L784, `get_design` L790, `insert_design` L798, `update_design` L809, `delete_design` L828 | 5 | designation |
| Company | `company_empty_record` L834, `get_comp_list` L863, `get_company_detail` L869, `get_company` L896, `get_default_country` L874, `get_default_state` L879, `get_default_city` L884, `get_curr_detail` L889, `create_company` L913, `update_company` L918, `update_default_country` L924, `insert_import_log` L908, `getCompanyDetails` L2471, `get_company_settings` L2494 | 14 | company, country, state, city, chit_settings |
| Charges | `ajax_get_charges` L950, `get_service` L955, `get_charges` L962, `get_charges_range` L969, `insert_charges` L976, `update_charges` L993, `delete_charges` L1012 | 7 | payment_charges |
| Metal Rates | `metal_ratesDB` L1026, `max_metalrate` L1018, `rates_by_branch` L1100, `import_excel` L1125, `insert_metalrate` L1717, `metal_rate_type` L1722, `metal_rates_list` L1729, `max_metalrate_list` L1744, `update_metalrate_status` L14, `get_branch_rate` L20, `get_branch_edit` L2219, `metal_rates_branch` L2264, `get_branches_for_rate` L2045, `update_silverRate` L2185, `getMetalRateId` L2175, `getRef_nos` L2191, `getBranchId` L2165 | 17 | metal_rates, branch_rate |
| Settings | `setting_data` L1156, `settingsDB` L1162, `limitDB` L1247, `discount_db` L1318, `receipt_type` L1364, `get_gstsettings` L1757, `get_schdebit_settings` L1763, `accno_generatorset` L1770, `configDB` L2621, `get_settings` L2287, `allow_autorate_update` L1378 | 11 | chit_settings, configuration |
| Gateway | `gateway_settingsDB` L1452, `sms_apiDB` L1487, `ajax_gateway_settings` L2022, `update_gateway` L2031, `insert_payment_gateway` L1845, `ajax_get_paymentgateway` L1861, `get_paymentgateway` L1870, `delete_payment_gateway` L1882, `update_paymentgateway` L1888 | 9 | gateway_settings, sms_api_settings |
| SMS/Promotion | `promotion_crt_settings` L1780, `otp_crt_settings` L1808, `walSMS_settings` L1979, `canSendNoti` L2107, `get_service_by_code` L2464 | 5 | sms_api_settings, promotion_credit, otp_credit, services |
| Branch | `get_branche` L1664, `ajax_get_branches` L1670, `get_branches` L1680, `update_branch_only` L1686, `update_branch` L1693, `get_branch_by_id` L1700, `insert_branch` L1708, `get_branchcompany` L1833, `branchname_list` L2117, `getBranchDetails` L2761 | 10 | branch |
| Offers | `ajax_get_offers` L1513, `get_offers` L1519, `isPopupExist` L1526, `offer_empty_record` L1533, `insert_offer` L1548, `update_offer` L1556, `delete_offer` L1564, `getExpiredData` L1570, `get_imgpath` L2751 | 9 | offers |
| New Arrivals | `ajax_get_new_arrivals` L1578, `get_new_arrivals` L1584, `new_arrivals_empty_record` L1591, `insert_new_arrivals` L1612, `update_new_arrivals` L1620, `delete_new_arrivals` L1627, `get_newArival_imgpath` L2756 | 7 | new_arrivals |
| Card Brand | `ajax_get_cardbrand` L1633, `get_card_brand` L1639, `insert_card_brand` L1646, `update_cardbrand` L1651, `delete_card_brand` L1657 | 5 | card_brand |
| Gift | `giftDB` L2501, `get_all_gifts` L2550, `update_gift_status` L2563 | 3 | gifts |
| Profession | `ajax_get_profession` L2568, `insert_profession` L2575, `get_profession` L2586, `update_profession` L2594, `delete_profession` L2613 | 5 | profession |
| T&C | `terms_and_conditions` L2230 | 1 | general |
| Version | `versionDB` L2654, `get_version_data` L2695, `get_last_version` L2701 | 3 | version |
| Village | `village_settingDB` L2072, `ajax_village_list` L2054 | 2 | village |
| Notification | `update_notification_status` L2741, `getnotification` L2746, `getNotificationDetails` L2418 | 3 | notification, ret_noticeboard |
| Ledger | `ledgerDB` L2773, `getAvailablePosDevices` L2859 | 2 | ledger_master, ledger_mapping, ret_bill_pay_device |
| Backup | `database_backup` L1383, `executeQry` L1374 | 2 | db_backup |
| Quick Link | `get_quick_link` L2714 | 1 | ret_quick_link, menu |
| Reports/Misc | `get_reports` L2160, `get_rate_diff` L2127, `checkBalance` L2133, `get_interWallet_trans` L1962, `get_interWallet_trans_by_Filter` L1901, `getWalletData` L1851, `get_interWallet_trans_temp` L2007, `interWalletAcc_backup` (controller) | 7+ | inter_wallet_account, inter_wallet_trans |
| Retail | `retail_settingsDB` L2373, `get_ret_settings` L2402, `get_financial_data` L2325, `getHeaderData` L2336, `getBTDetails` L2362, `get_modules` L2368, `getBranchDayClosingData` L2407, `getAllBranchDCData` L2413, `import_off_excel` L2294, `getPreviousDateStatuslog` L2729 | 10 | ret_settings, ret_financial_year, ret_day_closing, ret_branch_transfer, modules |
| KYC (**NEW**) | `kyc_master` L2886, `get_kyc_settings` L2892, `get_kyc_rules` L2905, `save_kyc_settings` L2910 | 4 | kyc_master, kyc_settings, kyc_rules |
| Misc | `get_languages_known` L2767, `menuPermission` L2708, `deleteData` L2723 | 3 | languages, access, {any} |
