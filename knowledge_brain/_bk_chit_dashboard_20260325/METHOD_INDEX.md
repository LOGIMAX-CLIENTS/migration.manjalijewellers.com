# METHOD INDEX — chit_dashboard
> Round 1 — 2026-03-16 | Alphabetical lookup. 77 controller + 94 model methods.

---

## Part A: Controller Methods (Alphabetical)

| Method | Lines | Type | Tables Read | Tables Written | JS Caller |
|---|---|---|---|---|---|
| `about_to_close($type)` | L1264-1288 | Page | `scheme_account`,`payment`,`scheme`,`customer`,`chit_settings` | — | Direct URL |
| `acc_detail($type)` | L980-1008 | Page | `scheme_account`,`customer`,`scheme`,`chit_settings`,`branch` | — | Direct URL |
| `acc_wo_pay_details()` | L1166-1196 | Page | `scheme_account`,`payment`,`customer`,`scheme`,`branch`,`chit_settings` | — | Direct URL |
| `acc_wo_pay_details_bydate(...)` | L3249-3283 | JSON | `scheme_account`,`payment`,`customer`,`branch` | — | — |
| `account_bydate()` | L3285-3296 | JSON | `scheme_account`,`branch` | — | JS L2027 |
| `account_stat()` | L360-434 | Internal | `scheme_account`,`branch` | — | Index (deprecated) |
| `account_status()` | L1864-1918 | JSON | `scheme_account`,`payment`,`branch` | — | JS L812 |
| `ajax_collectionData()` | L2326-2346 | JSON | `payment`,`scheme_account`,`scheme` | — | JS L911 |
| `ajax_customer_wishes()` | L3003-3033 | JSON | `customer` | — | JS L323 |
| `ajax_daily_collection()` | L2386-2532 | JSON | `payment`,`scheme_account`,`branch` | — | DEPRECATED (JS L972 commented) |
| `ajax_get_account()` | L2060-2098 | JSON | `scheme_account`,`branch` | — | JS L381 |
| `ajax_get_account_joined()` | L2102-2132 | JSON | `scheme_account`,`branch` | — | JS L369 |
| `ajax_get_collection_list()` | L3355-3363 | JSON | `payment`,`scheme_account` | — | JS L14 (cockpit) |
| `ajax_get_payment_joined()` | L2138-2168 | JSON | `payment`,`scheme_account`,`branch` | — | JS L306 |
| `ajax_get_ratestat()` | L1524-1544 | JSON | `metal_rates` | — | — |
| `awaiting_detail()` | L1392-1416 | Page | `payment`,`scheme_account`,`customer`,`scheme`,`payment_status_message`,`chit_settings` | — | Direct URL |
| `birthday($birthday)` | L2767-2779 | Internal | — | — | Helper |
| `closed_acc_detail($type)` | L1232-1256 | Page | `scheme_account`,`payment`,`customer`,`scheme` | — | Direct URL |
| `cus_birthday()` | L2987-3001 | Internal | `customer` | — | Called by index() |
| `cus_wedding_day()` | L3035-3051 | Internal | `customer` | — | Called by index() |
| `cust_wo_acc_details()` | L1018-1048 | Page | `scheme_account`,`customer` | — | Direct URL |
| `cust_wo_acc_details_bydate(...)` | L3217-3247 | JSON | `customer`,`scheme_account` | — | — |
| `cust_wo_accounts_details()` | L1058-1148 | JSON | `scheme_account`,`customer` + ACC_MODEL | — | Internal |
| `customer_count()` | L3167-3191 | JSON | `customer`,`branch` | — | JS L2004 |
| `customer_detail_bydate()` | L950-974 | JSON | `customer` | — | JS L1632 |
| `customer_edit($mobile)` | L2578-2767 | Page | `customer`,`scheme_account`,`scheme`,`address` | `customer` | Direct GET URL |
| `customer_stat()` | L214-292 | Internal | `customer` | — | Called by index() |
| `customer_status()` | L2781-2845 | JSON | `customer`,`employee` | — | JS L1806 |
| `customer_wishes($type,$filterBy)` | L2971-2985 | JSON | `customer` | — | — |
| `dashboard()` | L106-128 | JSON | `scheme_account`,`payment`,`scheme_reg_request`,`cust_enquiry` | — | JS L670 |
| `dayClose()` | L2925-2969 | Page | `payment`,`scheme_account`,`branch` | `daily_collection` | Direct URL |
| `due_list($filterBy)` | L1456-1492 | Page | `scheme_account`,`payment`,`customer`,`scheme`,`chit_settings` | — | Direct URL |
| `due_stat()` | L852-892 | Internal | `scheme_account`,`payment` | — | dashboard() (deprecated) |
| `get_account(...)` | L2012-2056 | Page | `scheme_account`,`branch` | — | Direct URL |
| `get_account_joined(...)` | L2172-2210 | Page | `scheme_account`,`branch` | — | Direct URL |
| `get_cancelled_payment(...)` | L2536-2574 | Page | `payment`,`scheme_account`,`customer`,`scheme` | — | Direct URL |
| `get_closed($type)` | L1600-1616 | Internal | `scheme_account` | — | Called by dashboard() |
| `get_closed_accounts()` | L1576-1592 | Internal | `scheme_account` | — | Index (deprecated) |
| `get_collection_app_details()` | L3365-3375 | JSON | `payment`,`scheme_account` | — | JS L1850 |
| `get_collection_list()` | L3345-3353 | Page | `payment`,`scheme_account` | — | — |
| `get_existing_request()` | L760-842 | Internal | `scheme_reg_request`,`branch` | — | Called by dashboard() |
| `get_feedback()` | L1620-1636 | Internal | `cust_enquiry` | — | Called by dashboard() |
| `get_payment(...)` | L1924-1966 | Page | `payment`,`scheme_account`,`branch` | — | Direct URL |
| `get_payment_joined(...)` | L1970-2008 | Page | `payment`,`scheme_account`,`branch` | — | Direct URL |
| `get_renewals($type)` | L1640-1656 | Internal | `scheme_account`,`scheme` | — | Called by dashboard() |
| `get_renewals_list($type)` | L1664-1688 | Page | `scheme_account`,`scheme` | — | Direct URL |
| `get_total_wallets()` | L1552-1568 | Internal | `inter_wallet` | — | Index (deprecated) |
| `getsource_wiserrecord()` | L3432-3463 | JSON | `payment`,`scheme_account`,`branch`,`scheme` | — | JS L989 |
| `getsource_wiserrecord_old()` | L3376-3431 | ⚠️DEAD | — | — | Not called |
| `index()` | L130-204 | Page | (delegates) | — | Browser navigation |
| `inter_wallet()` | L300-356 | Internal | `inter_wallet` | — | Index (deprecated) |
| `inter_wallet_accounts()` | L1692-1712 | Internal | `inter_wallet` | — | regisert_list() |
| `inter_wallet_accounts__woc($from,$to)` | L1774-1796 | Page | `inter_wallet` | — | Direct URL |
| `inter_wallet_accounts__woc_det()` | L1800-1816 | JSON | `inter_wallet` | — | — |
| `inter_wallet_accounts_detail()` | L1740-1772 | Page | `inter_wallet` | — | Direct URL |
| `inter_wallet_accounts_woc()` | L1716-1736 | Internal | `inter_wallet` | — | regisert_list() |
| `inter_wallet_details($type)` | L1330-1364 | Page | `inter_wallet` | — | Direct URL |
| `inter_wallet_status()` | L2214-2284 | JSON | `inter_wallet`,`branch` | — | JS L872 |
| `inter_wallet_transcation_details(...)` | L2288-2322 | Page | `inter_wallet` | — | Direct URL |
| `paid_unpaid_status($filterBy,$id_scheme)` | L1424-1452 | Page | `scheme_account`,`payment`,`customer`,`scheme` | — | Direct URL |
| `pay_detail($type)` | L1296-1324 | Page | `payment`,`scheme_account`,`customer`,`payment_status_message` | — | Direct URL |
| `payment_stat()` | L444-630 | Internal | `payment`,`scheme_account`,`scheme`,`branch` | — | Index (deprecated) |
| `payment_status()` | L1820-1856 | JSON | `payment`,`scheme_account`,`branch` | — | JS L740 |
| `paydatewise_schemecoll_list($filterdate)` | L2350-2360 | Internal | `payment`,`scheme_account`,`scheme` | — | ajax_collectionData() |
| `pdc_stat()` | L638-754 | Internal | `payment`,`scheme_account` | — | Index (deprecated) |
| `postdated_pay_detail($filterBy,$mode,$status)` | L1500-1516 | Page | `payment`,`scheme_account` | — | Direct URL |
| `reg_detail($type)` | L910-946 | Page | `customer`,`scheme_account` | — | Direct URL |
| `reg_detail_bydate(...)` | L3193-3215 | JSON | `customer`,`branch` | — | — |
| `regisert_list()` | L2362-2382 | JSON | `inter_wallet` | — | JS L950 |
| `schWise_accounts_list()` | L3464-3483 | JSON | `scheme_account`,`scheme`,`branch` | — | JS L1759 |
| `send_customer_wishes()` | L3053-3149 | POST | `customer` | `sms_log` | — |
| `total_payment_details()` | L1200-1224 | Page | `payment`,`scheme_account`,`customer`,`payment_status_message` | — | Direct URL |
| `Upload_apk()` | L3298-3302 | Page | — | — | Direct URL |
| `upload()` | L3304-3343 | POST | — | disk file | APK upload |

---

## Part B: Model Methods — dashboard_model (Alphabetical)

| Method | Lines | Tables Read | Tables Written | Called By (Controller) |
|---|---|---|---|---|
| `acc_detail_stat($filterBy)` | L536-591 | `scheme_account`,`customer`,`scheme`,`chit_settings`,`branch` | — | `acc_detail()` |
| `acc_wo_pay()` | L82-97 | `scheme_account`,`branch` | — | `account_stat()` |
| `acc_wo_pay_bydate()` | L2364-2408 | `scheme_account`,`payment`,`branch` | — | `acc_wo_pay_details_bydate()` |
| `acc_wo_pay_details()` | L223-241 | `scheme_account`,`customer`,`scheme`,`chit_settings` | — | `acc_wo_pay_details()` |
| `acc_wo_pay_details_bydate($from,$to,$branch)` | L2409-2435 | `scheme_account`,`payment`,`customer`,`scheme`,`chit_settings` | — | `acc_wo_pay_details_bydate()` |
| `acc_wo_payment($from,$to)` | L1576-1593 | `scheme_account`,`payment` | — | `account_status()` |
| `account_bydate()` | L2436-2478 | `scheme_account`,`branch` | — | `account_bydate()` |
| `account_join($filterBy)` | L418-442 | `scheme_account`,`branch` | — | `account_stat()` |
| `account_join_list($filterBy,$from,$to)` | L1594-1657 | `scheme_account`,`branch` | — | `account_status()` |
| `account_stat($filterBy)` | L379-416 | `scheme_account`,`branch` | — | `account_stat()` |
| `account_status($from,$to)` | L1398-1429 | `scheme_account`,`payment`,`branch` | — | `account_status()` |
| `ajax_get_collection_list()` | L2527-2561 | `payment`,`scheme_account` | — | `ajax_get_collection_list()` |
| `allBranches()` | L1824-1828 | `branch` | — | `inter_wallet_status()` |
| `await_detail_stat()` | L739-760 | `payment`,`scheme_account`,`customer`,`scheme`,`payment_status_message`,`chit_settings` | — | `awaiting_detail()` |
| `awaiting_pymt()` | L672-686 | `payment`,`scheme_account`,`branch` | — | `payment_stat()` |
| `closed_acc_detail_stat($filterBy)` | L1122-1157 | `scheme_account`,`payment`,`customer`,`scheme`,`chit_settings` | — | `about_to_close()` |
| `closed_acc_stat()` | L1223-1236 | `scheme_account`,`payment`,`customer`,`scheme` | — | `closed_acc_detail()` |
| `cus_birthday($filterBy)` | L2208-2230 | `customer` | — | `ajax_customer_wishes()` |
| `cus_wedding_day($filterBy)` | L2231-2254 | `customer` | — | `ajax_customer_wishes()` |
| `cus_wishes_list($type,$filterBy)` | L2144-2207 | `customer` | — | `customer_wishes()` |
| `cus_wishes_list_bydate($from,$to)` | L2698-2785 | `customer`,`scheme_account` | — | `send_customer_wishes()` |
| `cust_wo_acc()` | L71-81 | `customer`,`scheme_account` | — | (unused) |
| `cust_wo_acc_bydate()` | L2314-2341 | `customer`,`scheme_account` | — | `cust_wo_acc_details_bydate()` |
| `cust_wo_acc_details()` | L116-133 | `customer`,`scheme_account` | — | `cust_wo_acc_details()`, `cust_wo_accounts_details()` |
| `cust_wo_acc_details_bydate($from,$to,$branch)` | L2342-2363 | `customer`,`scheme_account` | — | `cust_wo_acc_details_bydate()` |
| `customer_detail_bydate($from,$to)` | L2286-2313 | `customer` | — | `customer_detail_bydate()` |
| `customer_join($filterBy)` | L289-312 | `customer` | — | `customer_stat()` |
| `customer_join_list($filterBy,$from,$to)` | L1970-2002 | `customer`,`branch` | — | `get_customer_joined()` |
| `customer_status($from,$to)` | L2028-2073 | `customer`,`employee` | — | `customer_status()` |
| `due_list($filterBy)` | L484-534 | `scheme_account`,`payment`,`customer`,`scheme`,`chit_settings` | — | `due_list()` |
| `due_stat($filterBy)` | L445-481 | `scheme_account`,`payment` | — | `due_stat()` |
| `enquiry_detail_report($filterBy)` | L44-69 | `cust_enquiry` | — | ⚠️ DEAD (not called in controller) |
| `enquiry_report($filterBy)` | L13-43 | `cust_enquiry` | — | ⚠️ DEAD (not called in controller) |
| `get_account_joined($from,$to,$type)` | L1730-1760 | `scheme_account`,`branch` | — | `get_account_joined()`,`ajax_get_account_joined()` |
| `get_account_list($branch,$from,$to,$type)` | L1694-1729 | `scheme_account`,`branch` | — | `get_account()`,`ajax_get_account()` |
| `get_cancelled_payment_list(...)` | L1932-1951 | `payment`,`scheme_account`,`customer`,`scheme`,`branch` | — | `get_cancelled_payment()` |
| `get_collection_app_details()` | L2479-2526 | `payment`,`scheme_account` | — | `get_collection_app_details()` |
| `get_collection_amt($id_branch)` | L2678-2697 | `payment`,`scheme_account` | — | `account_bydate()` |
| `get_cust($mobile)` | L1952-1969 | `customer` | — | `customer_edit()` |
| `get_customer_joined($from,$to,$type,$branch)` | L2003-2027 | `customer`,`branch` | — | `get_customer_joined()` |
| `get_enquiry()` | L1237-1242 | `cust_enquiry` | — | `get_feedback()` |
| `get_existingSchRequests_dashboard($status)` | ~L800 context | `scheme_reg_request`,`branch` | — | `get_existing_request()` |
| `get_newclosed_amt($id_branch)` | L2652-2677 | `scheme_account`,`payment` | — | `account_bydate()` |
| `get_oldclosed_amt($id_branch)` | L2638-2651 | `scheme_account`,`payment` | — | `account_bydate()` |
| `get_payment_list($branch,$from,$to,$type)` | L1430-1456 | `payment`,`scheme_account`,`customer`,`scheme`,`branch` | — | `get_payment()` |
| `get_scheme()` | L211-216 | `scheme` | — | Index (deprecated) |
| `get_scheme_group()` | L217-222 | `scheme_group` | — | Index (deprecated) |
| `getopening_bal_amt($id_branch)` | L2616-2637 | `scheme_account` | — | `account_bydate()` |
| `inter_wallet($filterBy)` | L1243-1300 | `inter_wallet`,`scheme_account`,`branch` | — | `inter_wallet()` |
| `inter_wallet_accounts()` | L1366-1373 | `inter_wallet` | — | `inter_wallet_accounts()`,`regisert_list()` |
| `inter_wallet_accounts_detail()` | L1380-1390 | `inter_wallet`,`scheme_account`,`customer` | — | `inter_wallet_accounts_detail()` |
| `inter_wallet_accounts_woc()` | L1374-1379 | `inter_wallet` | — | `inter_wallet_accounts_woc()`,`regisert_list()` |
| `inter_wallet_credit($from,$to)` | L1787-1798 | `inter_wallet`,`branch`,`chit_settings` | — | `inter_wallet_status()` |
| `inter_wallet_detail($branch,$from,$to,$type)` | L1829-1885 | `inter_wallet`,`scheme_account`,`branch` | — | `inter_wallet_transcation_details()` |
| `inter_wallet_detail_stat($type)` | L1301-1352 | `inter_wallet`,`scheme_account`,`customer`,`scheme` | — | `inter_wallet_details()` |
| `inter_wallet_redeem($from,$to)` | L1799-1809 | `inter_wallet`,`branch`,`chit_settings` | — | `inter_wallet_status()` |
| `inter_wallet_woc($from,$to)` | L1391-1397 | `inter_wallet` | — | `inter_wallet_accounts__woc()`, `inter_wallet_accounts__woc_det()` |
| `interCreditAndDebit($from,$to)` | L1810-1823 | `inter_wallet` | — | ⚠️ DEAD (commented in controller) |
| `old_account_join_list(...)` | L1658-1693 | `scheme_account`,`branch` | — | ⚠️ DEAD (old version) |
| `old_account_status($from,$to)` | L1552-1575 | `scheme_account`,`payment` | — | ⚠️ DEAD (old version) |
| `old_renewal_stat($filterBy)` | L1158-1181 | `scheme_account`,`scheme` | — | ⚠️ DEAD (old version) |
| `old_total_abt_to_cls($ins_type)` | L1075-1098 | `scheme_account`,`scheme` | — | ⚠️ DEAD (old version) |
| `paid_unpaid_detail($filterBy,$id_scheme)` | L806-847 | `scheme_account`,`payment`,`customer`,`scheme` | — | `paid_unpaid_status()` |
| `paid_unpaid_records($filterBy,$id_scheme)` | L761-805 | `scheme_account`,`payment` | — | (indirect/unused) |
| `pay_detail_stat($filterBy)` | L687-738 | `payment`,`scheme_account`,`customer`,`scheme`,`payment_status_message`,`chit_settings`,`branch` | — | `pay_detail()`,`awaiting_detail()` |
| `pay_stat($filterBy)` | L592-644 | `scheme_account`,`payment`,`branch` | — | `payment_stat()` |
| `paydatewise_schemecoll($date)` | L1885-1926 | `payment`,`scheme_account`,`scheme` | — | `paydatewise_schemecoll_list()` |
| `payment_join($filterBy)` | L263-288 | `payment`,`scheme_account`,`branch` | — | `payment_stat()` |
| `payment_join_through($filterBy,$from,$to)` | L1457-1505 | `payment`,`scheme_account`,`branch` | — | `payment_status()` |
| `payment_join_through_list($from,$to,$type)` | L1761-1786 | `payment`,`scheme_account`,`customer`,`scheme`,`branch` | — | `get_payment_joined()`,`ajax_get_payment_joined()` |
| `payment_status($from,$to)` | L1398-1429 | `payment`,`scheme_account`,`branch` | — | `payment_status()` |
| `pdc_report($filterBy,$mode,$payment_status)` | L954-987 | `payment`,`scheme_account` | — | `pdc_stat()` |
| `pdc_report_detail($filterBy,$mode,$status)` | L987-1048 | `payment`,`scheme_account`,`customer`,`scheme`,`chit_settings` | — | `postdated_pay_detail()` |
| `postdated_payments()` | L935-943 | `payment` | — | ⚠️ Possibly dead |
| `current_postdated_payments()` | L944-953 | `payment` | — | ⚠️ Possibly dead |
| `pymt_status($filterBy)` | L645-671 | `payment`,`scheme_account`,`branch` | — | `payment_stat()` |
| `rateWeekStat()` | L1048-1056 | `metal_rates` | — | `ajax_get_ratestat()` |
| `reg_detail_stat($filterBy)` | L314-378 | `customer`,`scheme_account` | — | `reg_detail()` |
| `reg_existing()` | L1927-1931 | `scheme_reg_request` | — | `get_existing_request()` |
| `reg_stat($filterBy)` | L135-171 | `customer` | — | `customer_stat()` |
| `reg_stat_count($from,$to,$branch)` | L2255-2285 | `customer`,`branch` | — | `customer_count()` |
| `renewal_stat($filterBy,$limit,$offset)` | L1182-1222 | `scheme_account`,`scheme`,`payment`,`customer`,`branch` | — | `get_renewals()`,`get_renewals_list()` |
| `req_stat($filterBy)` | L172-209 | `scheme_reg_request`,`branch` | — | `get_existing_request()` |
| `scheme_group()` | L1353-1358 | `scheme_group` | — | (unused) |
| `schWise_accounts($from,$to,$branch)` | L2562-2615 | `scheme_account`,`scheme`,`branch`,`payment` | — | `schWise_accounts_list()` |
| `schemewise_accounts($id_branch)` | L914-934 | `scheme_account`,`scheme` | — | (deprecated) |
| `schemewise_payment()` | L865-891 | `payment`,`scheme`,`scheme_account` | — | `payment_stat()` |
| `total_abt_to_cls($ins_type)` | L1099-1121 | `scheme_account`,`scheme` | — | `get_closed()` |
| `total_closed_accounts()` | L1064-1074 | `scheme_account` | — | `get_closed_accounts()` |
| `total_paid_unpaid()` | L892-913 | `scheme_account`,`payment` | — | (possibly unused) |
| `total_payment_details()` | L242-262 | `payment`,`scheme_account`,`customer`,`scheme`,`branch`,`payment_status_message`,`chit_settings` | — | `total_payment_details()` |
| `total_payments()` | L848-864 | `payment` | — | (possibly unused) |
| `total_wallets()` | L1057-1063 | `inter_wallet` | — | `get_total_wallets()` |
| `updateData($data,$id_field,$id_value,$table)` | L2074-2143 | `{any}` | `customer` | `customer_edit()` |
| `wallet_amt()` | L1359-1365 | `inter_wallet` | — | (unused) |

---

## Part C: JS → Controller AJAX Map

| JS Line | JS Context | AJAX URL | Controller Method | HTTP |
|---|---|---|---|---|
| L14 | cockpit init | `admin_dashboard/ajax_get_collection_list` | `ajax_get_collection_list()` | POST |
| L306 | payment joined picker | `admin_dashboard/ajax_get_payment_joined` | `ajax_get_payment_joined()` | POST |
| L323 | customer wishes tab | `admin_dashboard/ajax_customer_wishes` | `ajax_customer_wishes()` | POST |
| L369 | account joined picker | `admin_dashboard/ajax_get_account_joined` | `ajax_get_account_joined()` | POST |
| L381 | account date filter | `admin_dashboard/ajax_get_account` | `ajax_get_account()` | POST |
| L398 | customer mobile lookup | `admin_customer/get_customer_by_mobile` | ⚠️ CROSS: `admin_customer` | POST |
| L579 | rate chart init | `rate/ajax/weekstat` | ⚠️ CROSS: rate controller | GET |
| L597 | branch dropdown | `branch/branchname_list` | ⚠️ CROSS: branch controller | GET |
| L670 | dashboard refresh | `admin_dashboard/dashboard` | `dashboard()` | POST |
| L740 | payment chart | `admin_dashboard/payment_status` | `payment_status()` | POST |
| L812 | account chart | `admin_dashboard/account_status` | `account_status()` | POST |
| L872 | inter-wallet chart | `admin_dashboard/inter_wallet_status` | `inter_wallet_status()` | POST |
| L911 | collection table | `admin_dashboard/ajax_collectionData` | `ajax_collectionData()` | POST |
| L950 | register list | `admin_dashboard/regisert_list` | `regisert_list()` | POST |
| L972 | (COMMENTED OUT) | `admin_dashboard/ajax_daily_collection` | DEPRECATED | — |
| L989 | source-wise records | `admin_dashboard/getsource_wiserrecord` | `getsource_wiserrecord()` | POST |
| L1402 | retail orders | `admin_ret_dashboard/get_customer_order_details` | ⚠️ CROSS: retail | GET |
| L1601 | employee list | `reports/employee_list` | ⚠️ CROSS: reports | GET |
| L1632 | customer detail picker | `admin_dashboard/customer_detail_bydate` | `customer_detail_bydate()` | POST |
| L1759 | scheme accounts chart | `admin_dashboard/schWise_accounts_list` | `schWise_accounts_list()` | GET |
| L1806 | customer status | `admin_dashboard/customer_status` | `customer_status()` | GET |
| L1850 | collection app detail | `admin_dashboard/get_collection_app_details` | `get_collection_app_details()` | GET |
| L1912 | payment modewise | `admin_reports/payment_summary_modewise` | ⚠️ CROSS: admin_reports | GET |
| L2004 | customer count barchart | `admin_dashboard/customer_count` | `customer_count()` | GET |
| L2027 | account trend barchart | `admin_dashboard/account_bydate` | `account_bydate()` | GET |

**Cross-module AJAX callers (5)**:
1. `admin_customer` → customer mobile lookup
2. `rate` → gold rate weekly chart
3. `branch` → branch dropdown filter
4. `admin_ret_dashboard` → retail customer orders
5. `admin_reports` → payment mode-wise summary

---

## Part D: Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `payment` | `pymt_status`, `pay_stat`, `payment_join`, `pay_detail_stat`, `await_detail_stat`, `schemewise_payment`, `pdc_report`, `paydatewise_schemecoll`, `get_payment_list`, `payment_status`, `payment_join_through` | ❌ Read-only |
| `scheme_account` | `account_stat`, `account_join`, `acc_wo_pay`, `acc_detail_stat`, `due_stat`, `due_list`, `closed_acc_stat`, `total_abt_to_cls`, `renewal_stat` | ❌ Read-only |
| `customer` | `reg_stat`, `reg_detail_stat`, `customer_join`, `cust_wo_acc_details`, `customer_status`, `cus_wishes_list`, `get_cust` | ⚠️ `updateData()` via `customer_edit()` |
| `scheme` | `schemewise_payment`, `renewal_stat`, `closed_acc_stat` | ❌ Read-only |
| `branch` | `account_stat`, `payment_join_through`, `allBranches`, `inter_wallet_credit` | ❌ Read-only |
| `chit_settings` | `acc_wo_pay_details`, `due_list`, `total_payment_details`, `pay_detail_stat` | ❌ Read-only |
| `inter_wallet` | `inter_wallet`, `inter_wallet_accounts`, `inter_wallet_detail_stat`, `inter_wallet_credit`, `inter_wallet_redeem`, `total_wallets` | ❌ Read-only |
| `scheme_reg_request` | `req_stat`, `get_existingSchRequests_dashboard`, `reg_existing` | ❌ Read-only |
| `cust_enquiry` | `enquiry_report`, `get_enquiry` | ❌ Read-only |
| `metal_rates` | `rateWeekStat`, `acc_wo_pay_details` (subquery) | ❌ Read-only |
| `employee` | `customer_status` | ❌ Read-only |
| `payment_status_message` | `total_payment_details`, `pay_detail_stat`, `await_detail_stat` | ❌ Read-only |
| `daily_collection` | — | ✅ Written by `dayClose()` via services_model |
