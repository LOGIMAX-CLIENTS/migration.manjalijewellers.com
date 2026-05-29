# Chit Settings Module — Method Index
> **Round**: 1 | **Date**: 2026-03-06

---

## Controller Methods (`admin_settings.php` — 120 methods)

### Group 1: Core / Constructor
| # | Method | Lines | Purpose |
|---|---|---|---|
| 1 | `__construct()` | L18-40 | Load 7 models, 2 libraries, set session |
| 2 | `index()` | L41-45 | Company settings page |
| 3 | `valid_str($str)` | L386-393 | Whitespace validation |

### Group 2: Menu Management
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 4 | `menu($type, $id)` | L47-113 | menu, access, profile | Menu CRUD + JSON |

### Group 3: Profile Management
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 5 | `profile($type, $id)` | L115-280 | profile, access | Profile CRUD (List/View/Save/Update/Delete) |

### Group 4: Permission System
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 6 | `permission($type, $id)` | L282-385 | menu, access, profile, dashboard_menu, dashboard_access | Menu + dashboard permission CRUD |

### Group 5: Bank Master
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 7 | `bank($type, $id)` | L395-491 | bank | Bank CRUD with duplicate check |

### Group 6: Metal Rates
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 8 | `update_rate_file($rates)` | L493-512 | — (file write) | Write rate.txt |
| 9 | `metal_rates($type, $id)` | L514-705 | metal_rates, branch_rate, chit_settings | Rate CRUD + branch mapping |
| 10 | `metal_rates_discount()` | L3710-3718 | chit_settings | Get rate discounts |
| 11 | `update_discout()` | L3719-3735 | chit_settings | Update rate discounts |

### Group 7: SMS/Notification
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 12 | `get_SMS_data($serviceID)` | L774-790 | customer (mobile) | Bulk SMS data prep |
| 13 | `send_bulk_sms($data)` | L791-819 | — | SMS API call |
| 14 | `update_prosms($mob_length)` | L820-828 | — | SMS credit update |
| 15 | `send_RatesToAllUsers($branchArr)` | L3540-3648 | — | Rate notification push |
| 16 | `onesignalNotificationToAll(...)` | L3649-3681 | — | OneSignal push API |
| 17 | `send_singlealert_rate_notification(...)` | L3911-3945 | — | Single rate notification |

### Group 8: Master Data CRUD (Drawee/PayMode/Weight/Classification/Dept/Design)
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 18 | `drawee($type, $id)` | L830-888 | drawee_account, bank | Drawee CRUD |
| 19 | `payment_mode($type, $id)` | L889-985 | payment_mode | Pay mode CRUD |
| 20 | `weight_form($type, $id)` | L1260-1314 | weight | Weight CRUD |
| 21 | `classification_form($type, $id)` | L1315-1381 | sch_classify | Classification CRUD |
| 22 | `dept_form($type, $id)` | L1382-1445 | department | Department CRUD |
| 23 | `design_form($type, $id)` | L1446-1509 | designation | Designation CRUD |
| 24 | `profession_form($type, $id)` | L4083-4146 | profession | Profession CRUD |
| 25 | `village_form($type, $id)` | L3752-3825 | village | Village CRUD |

### Group 9: AJAX Data Providers
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 26 | `ajax_get_depts()` | L1510-1520 | department | JSON dept list |
| 27 | `ajax_get_designs()` | L1521-1531 | designation | JSON designation list |
| 28 | `ajax_get_weights()` | L1532-1542 | weight | JSON weight list |
| 29 | `ajax_get_classifications()` | L1543-1553 | sch_classify | JSON classifications |
| 30 | `ajax_get_bank()` | L1554-1564 | bank | JSON bank list |
| 31 | `ajax_get_paymentMode()` | L1565-1575 | payment_mode | JSON pay modes |
| 32 | `ajax_get_drawee()` | L1576-1586 | drawee_account | JSON drawees |
| 33 | `ajax_get_exportlist()` | L1587-1600 | export data | JSON exports |
| 34 | `ajax_get_branches()` | L3133-3142 | branch | JSON branches |
| 35 | `ajax_get_offers()` | L2707-2726 | offers | JSON offers |
| 36 | `ajax_get_new_arrivals()` | L2866-2876 | new_arrivals | JSON arrivals |
| 37 | `ajax_get_cardbrand()` | L2939-2945 | card_brand | JSON card brands |
| 38 | `ajax_get_gift()` | L4005-4015 | gift | JSON gifts |
| 39 | `ajax_get_profession()` | L4147-4157 | profession | JSON professions |
| 40 | `ajax_get_version()` | L4262-4275 | version_details | JSON versions |
| 41 | `ajax_backup_list()` | L2271-2276 | db_backup | JSON backups |
| 42 | `ajax_village_list()` | L3743-3751 | village | JSON villages |
| 43 | `ajax_paymentgateway()` | L3683-3691 | gateway_settings | JSON gateways |

### Group 10: General Settings (CRITICAL — chit_settings table)
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 44 | `general_settings($type, $id)` | L1706-2122 | chit_settings, config_settings, company, country, + more | 60+ flag save/update |
| 45 | `get_otpsettings()` | L2141-2146 | chit_settings | OTP config JSON |

### Group 11: System Utilities
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 46 | `clear_database()` | L2147-2209 | ALL (TRUNCATE) | ⚠️ Wipe all data |
| 47 | `truncateFromArray($tables)` | L2210-2217 | — | Truncate loop |
| 48 | `rrmdir($path)` | L2219-2231 | — | Recursive dir delete |
| 49 | `db_backup()` | L2232-2270 | db_backup | Full DB backup ZIP |

### Group 12: Integration Settings
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 50 | `gateway_settings($type, $id)` | L2277-2337 | gateway_settings | PayU demo/pro save |
| 51 | `sms_api_settings($type, $id)` | L2338-2368 | sms_api_settings | SMS API save |
| 52 | `mail_settings($type, $id)` | L2369-2404 | company | Email settings |
| 53 | `limit_settings($type, $id)` | L2406-2443 | chit_settings | Limit settings |
| 54 | `discount_settings($type, $id)` | L2444-2483 | chit_settings, scheme | Discount settings |

### Group 13: Company
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 55 | `company_form($type, $id)` | L1075-1091 | company | Company form |
| 56 | `company_post($type, $id)` | L1092-1213 | company | Company save/update |
| 57 | `comp_list()` | L1214-1220 | company | Company list |

### Group 14: Geography
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 58 | `get_country()` | L994-998 | country | Country JSON |
| 59 | `update_country()` | L999-1028 | country | Default country update |
| 60 | `get_countryCurrency($id)` | L1029-1033 | country | Currency by country |
| 61 | `get_state()` | L1052-1059 | state | States JSON |
| 62 | `get_city()` | L1060-1067 | city | Cities JSON |
| 63 | `get_village_by_pincode()` | L1068-1074 | village | Village lookup |
| 64 | `village_list()` | L3736-3742 | village | Village list page |

### Group 15: Branch
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 65 | `branch_settings($id)` | L2946-2983 | branch | Branch settings update |
| 66 | `branch_form($type, $id, $status)` | L2984-3132 | branch | Branch CRUD |
| 67 | `matal_ratelist($id)` | L3143-3171 | metal_rates, branch_rate | Branch rate list |

### Group 16: Offers/Arrivals/Cards/Gifts/Version/Terms/Notification/Ledger
| # | Method | Lines | Tables | Purpose |
|---|---|---|---|---|
| 68 | `offers_form($type, $id)` | L2485-2613 | offers | Offer CRUD |
| 69 | `new_arrivals_form($type, $id)` | L2727-2865 | new_arrivals | New arrivals CRUD |
| 70 | `cardbrand_form($type, $id)` | L2877-2938 | card_brand | Card brand CRUD |
| 71 | `gift($type, $id)` | L3946-4004 | gift | Gift CRUD |
| 72 | `version_details($type, $id)` | L4171-4261 | version_details | Version CRUD |
| 73 | `terms_conditions($type, $id)` | L3855-3910 | terms_and_conditions | T&C CRUD |
| 74 | `notification($type, $id)` | L4541-4553 | notification | Notification CRUD |
| 75 | `ledger($type, $id)` | L4573+ | ledger | Ledger account CRUD |

### Group 17: Misc/Internal
| # | Method | Lines | Purpose |
|---|---|---|---|
| 76-80 | Image uploads | Various | `set_image`, `set__branch_image`, `set__clsfy_image`, `set__paymentgateway_image`, `upload_img` |
| 81-85 | Lookups | Various | `is_mobile_exists`, `contact_already_exist`, `get_scheme_id`, `get_weight_scheme_id`, `get_access_rights` |
| 86-90 | Config/Format | Various | `config_setting`, `config_setupdate`, `get_acc_format_details`, `get_receipt_format_details`, `get_page_title` |
| 91-95 | Gift helpers | Various | `update_gift_status`, `get_gift_name_byId`, `get_all_gifts`, `update_gift`, `add_gift` |
| 96-100 | Various | Various | `receipt_settings`, `schemeacc_no_settings`, `payment_charges`, `getCustomerByMobile`, `getSchemeAccountByCustomer` |
| 101-105 | Settings sub-modules | Various | `promotioncredit__settings`, `promotion_api_settings`, `otpcredit_settings`, `wallettype_account`, `ref_benefits_setting` |
| 106-110 | Gateway/Misc | Various | `gateway_form`, `ajax_get_paymentgateway`, `update_gateway`, `interWalletAcc_backup`, `get_otp_giftstatus` |
| 111-115 | Quick Links | Various | `quick_link`, `quick_link_revert` |
| 116-120 | Reports/Download/Misc | Various | `get_reports`, `download`, `compress`, `get_languages_known`, `get_last_version`, `get_profession` |

---

## Model Methods (`admin_settings_model.php` — 178 methods)

> Due to 178 methods, grouped by functional area. Full table mappings in CROSS_MODULE_MAP.md.

| Group | Methods | Count | Tables |
|---|---|---|---|
| Generic CRUD | `insertData`, `updateData` | 2 | Any |
| Menu | `menu_generation`, `submenu`, `menuDB`, `get_menu_link` | 4 | menu, access, profile |
| Permission | `get_access`, `get_dashboard_access`, `PermissionDB`, `DashboardPermissionDB` | 4 | access, menu, profile, dashboard_menu, dashboard_access |
| Profile | `profileDB`, `get_AllProfile` | 2 | profile |
| Bank | `bankDB` | 1 | bank |
| Drawee | `draweeDB` | 1 | drawee_account, bank |
| Payment Mode | `paymodeDB` | 1 | payment_mode |
| Geography | `get_country`, `get_state`, `get_city`, `get_village_by_pincode` | 4+ | country, state, city, village |
| Weight | `get_weights`, `ajax_get_weights`, `get_weight`, `insert_weight`, `update_weight`, `delete_weight` | 6 | weight |
| Classification | `get_classifications`, `ajax_get_classifications`, `get_classification`, `insert_classification`, `update_classification`, `delete_classification` | 6 | sch_classify |
| Department | `ajax_get_depts`, `get_dept`, `insert_dept`, `update_dept`, `delete_dept` | 5 | department |
| Designation | `ajax_get_designs`, `get_design`, `insert_design`, `update_design`, `delete_design` | 5 | designation |
| Settings | `settingsDB`, `configDB`, `limitDB`, `discount_db`, `get_gstsettings`, `get_datas` | 6+ | chit_settings, config_settings |
| Metal Rates | `metal_ratesDB`, `max_metalrate`, `insert_metalrate`, `get_branch_edit`, `update_metalrate_status`, `get_branch_rate` | 6+ | metal_rates, branch_rate |
| Company | `get_company`, `update_company`, `get_curr_detail`, `get_default_country/state/city`, `update_default_country` | 7 | company, country, state, city |
| Gateway | `gateway_settingsDB`, `paymentgatewayDB` | 2+ | gateway_settings |
| SMS/OTP | `sms_apiDB`, `promotion_crt_settings`, `otp_crt_settings`, `canSendNoti` | 4+ | sms_api_settings, promotion_credit, otp_credit |
| Offers/Arrivals | `offersDB`, `offer_empty_record`, `newArrivalsDB`, `newarrivals_empty_record` | 4+ | offers, new_arrivals |
| Branch | `branchDB`, `ajax_get_branches`, `branchwise_metalrate` | 3+ | branch |
| Gift | `giftDB`, `gift_empty_record`, `get_all_gifts` | 3+ | gift |
| Backup | `database_backup`, `executeQry` | 2 | db_backup |
| Version | `versionDB` | 1 | version_details |
| T&C | `terms_and_conditionsDB` | 1 | terms_and_conditions |
| Village | `villageDB` | 1+ | village |
| Card Brand | `cardbrandDB` | 1 | card_brand |
| Profession | `professionDB` | 1+ | profession |
| Notification | `notificationDB`, `update_notification` | 2+ | notification |
| Ledger | `ledgerDB` | 1+ | ledger |
| Charges | `payment_chargesDB` | 1+ | payment_charges |
| Receipt | `receipt_settingsDB`, `schemeacc_no_settingsDB` | 2+ | chit_settings |
