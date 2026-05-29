# METHOD INDEX — chit_reports
> Alphabetical. Source of truth for method→table lookups. Round R6-Upgrade — 2026-03-25 | 132 ctrl + 278 PM + 170 AM + 13 ARM methods. No method count changes since R5.

---

## 7a. Controller Methods — `admin_reports.php` (132 total)

| Method | Lines | Type | Tables Read | Tables Written | JS Caller / Notes |
|---|---|---|---|---|---|
| `__construct` | L15-35 | INIT | — | — | Loads all models |
| `accounts_schemewise` | L123-130 | PAGE | — | — | View load only |
| `accounts_schemewise_detail` | L131-138 | AJAX | `dashboard_model::schemewise_accounts` | — | `accounts_schemewise_detail` |
| `ajax_customer_payment_details` | L52-58 | AJAX | `payment` | — | `ajax_customer_payment_details` |
| `ajax_enquiry_list` | L600-615 | AJAX | `customer_enquiry` | — | `ajax_enquiry_list` |
| `ajax_get_autodebit_subscription` | L1126-1135 | AJAX | `purch_customer`, autodebit tables | — | `ajax_get_autodebit_subscription` |
| `ajax_get_customers_list` | L1005-1011 | AJAX | `purch_customer` | — | `ajax_get_customers_list` (autocomplete) |
| `ajax_get_customers_lists` | L1119-1125 | AJAX | `customer` | — | `ajax_get_customers_lists` (autodebit autocomplete) |
| `ajax_get_emp_account_list` | L704-716 | AJAX | `payment`, `scheme_account`, `customer`, `employee` | — | `ajax_get_emp_account_list` |
| `ajax_get_purchase_payment` | L1012-1021 | AJAX | `purch_payment`, `purch_customer` | — | `ajax_get_purchase_payment` |
| `ajax_getPayModeList` | L1407-1412 | AJAX | `payment_mode` | — | `ajax_getPayModeList` |
| `ajax_gift_report` | L1505-1522 | AJAX | `gift_issued`, `scheme_account`, `scheme` | — | `ajax_gift_report` |
| `ajax_interWallet_trans` | L649-664 | AJAX | `inter_wallet_transaction` | — | `ajax_interWallet_trans` |
| `ajax_payment_list` | L75-84 | AJAX | `payment`, `employee` | — | `ajax_payment_list` |
| `branchwise_employee` | L1138-1147 | AJAX | `employee`, `branch` | — | `get_branchname` JS call |
| `cancel_payment` | L665-695 | AJAX | `payment` | `payment`, `payment_status_log` | JS cancel button; uses raw `$_POST` |
| `checkBalance` | L843-869 | AJAX | *external* Msg91 API | — | `checkBalance/1` or `/4` |
| `checkCommonSettings` | L2101-2135 | PRIVATE | `payment_model::getBranchwiseSettings`, `getBranchData` | — | Called by `updateAccountDetails` |
| `closed_account_list` | L1212-1216 | PAGE | — | — | View load |
| `closedaccount_list` | L1217-1230 | AJAX | `scheme_account`, `payment`, `customer`, `scheme`, `branch` | — | `closedaccount_list` |
| `collection_report` | L1149-1153 | PAGE | — | — | View load |
| `cus_celeb_dates` | L2072-2099 | AJAX | `customer` (birthday/wedding) | — | `cus_celeb_dates` |
| `cus_ref_success` | L541-546 | PAGE | — | — | View load |
| `cus_ref_success_list` | L547-558 | AJAX | `payment`, `customer`, `scheme_account` | — | `cus_ref_success_list` |
| `cus_refferl_account` | L559-566 | PAGE | `payment` (referral) | — | URL param: mobile |
| `customer_account_details` | L1233-1248 | DUAL | `scheme_account`, `payment`, `customer` | — | `customer_account_details/ajax` |
| `customer_enquiry` | L595-599 | PAGE | — | — | View load |
| `customer_wishes` | L2067-2071 | PAGE | — | — | View load (celeb_days) |
| `edit_acc_pay` | L1692-1696 | PAGE | — | — | View load |
| `editAccOrPayments` | L1697-1712 | AJAX | `scheme_account`, `payment` | — | `editAccOrPayments/get_acc_byId`, `/get_pay_byId` |
| `emp_referral_account` | L513-520 | PAGE | `payment` (referral) | — | URL param: referal_code |
| `employee_account` | L696-703 | PAGE | `scheme_account`, `customer` | — | View load |
| `employee_collection` | L726-736 | AJAX | `payment`, `employee`, `branch` | — | `employee_collection` |
| `employee_ref_success` | L495-500 | PAGE | — | — | View load |
| `employee_ref_success_list` | L501-512 | AJAX | `payment`, `customer`, `employee` | — | `employee_ref_success_list` |
| `employee_wise_summary` | L719-725 | PAGE | — | — | View load |
| `enquiry` | L616-630 | AJAX | `customer_enquiry` | `customer_enquiry` | `enquiry/UpdateStatus`, `enquiry/View/{id}` |
| `exl_rep_outstanding` | L1593-1670 | DUAL | `scheme_account`, `customer`, `address`, `scheme` | — | `exl_rep_outstanding/export_excel` |
| `failed_data` | L166-172 | AJAX | `payment` (failed) | — | `failed_data` |
| `failed_payments` | L161-165 | PAGE | — | — | View load |
| `form_logger` | L1995-2018 | DUAL | `form_log` | — | `form_logger/list`, `form_logger/ajax` |
| `general_advance_list` | L1922-1927 | AJAX | `general_advance_payment`, `scheme_account`, `customer` | — | `general_advance_list` |
| `general_advance_list_byid` | L1929-1933 | AJAX | `general_advance_payment` | — | `general_advance_list_byid` |
| `general_advance_view` | L1917-1921 | PAGE | — | — | View load |
| `generateotp` | L1022-1044 | AJAX | `purch_customer`, `company` | — | `generateotp` — sends SMS OTP |
| `generateTranUniqueIdManually` | L1774-1848 | INTERNAL | `payment`, `scheme_account`, `customer`, `scheme`, `employee` | `payment` | Called by `generateTransUniqId`; calls Khimji external API |
| `generateTransUniqId` | L1756-1773 | AJAX | `payment` | `payment` | `generateTransUniqId` |
| `get_area` | L1910-1913 | AJAX | `address` / area table | — | `get_area` (member report filter) |
| `get_autodebit_subscription` | L1114-1118 | PAGE | — | — | View load |
| `get_city` | L1905-1909 | AJAX | `city` table | — | `get_city` (member report filter) |
| `get_cus_birthwed` | L1680-1689 | AJAX | `customer` | — | (Commented out but listed) |
| `get_employee_details` | L574-594 | AJAX | `payment`/employee referral | — | PDF generation (DomPDF) |
| `get_gift_report` | L1500-1504 | PAGE | — | — | View load |
| `get_joined_through` | L1900-1904 | AJAX | `joined_through` table | — | `get_joined_through` (member report filter) |
| `get_online_gift_report` | L1879-1884 | AJAX | `gift_issued`, `scheme_account` | — | `get_online_gift_report` |
| `get_online_gift_summary` | L1873-1878 | AJAX | `gift_issued` | — | `get_online_gift_summary` |
| `get_online_payment_report` | L1255-1262 | AJAX | `payment` (online) | — | `get_online_payment_report` |
| `get_payment_status` | L1263-1268 | AJAX | `payment_status` lookup | — | `get_payment_status` |
| `get_purchase_payment` | L1000-1004 | PAGE | — | — | View load |
| `get_referral_code_byId` | L521-538 | AJAX | `payment`, `customer`, `employee` | — | `get_referral_code_byId`; uses raw `$_POST['emp_code']` |
| `get_yet_to_issue` | L2050-2054 | PAGE | — | — | View load (gift_yet_to_issue) |
| `getCreditHistory` | L817-842 | AJAX | *external* Msg91 API | — | `getCreditHistory` |
| `getkycdata_byid` | L972-977 | AJAX | KYC tables | — | `getkycdata_byid` |
| `getMemberReport` | L1895-1899 | AJAX | `scheme_account`, `customer`, `scheme`, `address` | — | `getMemberReport` |
| `getRenewalLive_arlData` | L2061-2065 | AJAX | `scheme_account`, `payment` | — | `getRenewalLive_arlData` |
| `getscheme_name` | L567-572 | AJAX | `scheme` | — | `getscheme_name` |
| `giftname_list` | L1866-1872 | AJAX | `gift_article` / gift name table | — | `giftname_list` |
| `inter_table` | L740-746 | PAGE | — | — | View load |
| `intertable_list` | L747-758 | AJAX | `customer_reg`, `customer` | — | `intertable_list`; raw `$_POST` |
| `intertable_translist` | L759-768 | AJAX | `transaction` | — | `intertable_translist`; raw `$_POST` |
| `interWalletTrans_list` | L631-635 | PAGE | — | — | View load |
| `is_luckly_draw_scheme` | L1579-1592 | AJAX | `scheme`, `scheme_group` | — | `is_luckly_draw_scheme` |
| `kycapproval_data` | L911-929 | AJAX | `customer_kyc` / KYC tables | — | `kycapproval_data` (second definition wins) |
| `kycdata_list` | L906-910 | PAGE | — | — | View load |
| `log` | L194-237 | DUAL | `log` | — | Switch: List/View/Detail |
| `maturity_report_data` | L1989-1993 | AJAX | `scheme_account`, `scheme`, `customer` | — | `maturity_report_data` |
| `maturity_report_view` | L1984-1988 | PAGE | — | — | View load |
| `member_report` | L1889-1893 | PAGE | — | — | View load |
| `monthly_report_data` | L1940-1944 | AJAX | `payment`, `scheme_account`, `scheme` | — | `monthly_report_data` |
| `monthly_report_view` | L1935-1939 | PAGE | — | — | View load |
| `msg91_delivReport` | L870-882 | DUAL | `msg91` delivery log | — | `msg91_delivReport/List`, `/ajax_report` |
| `msg91_log` | L812-816 | PAGE | — | — | View load |
| `old_metal_report` | L1269-1287 | DUAL | `old_metal` table | — | `old_metal_report/ajax` |
| `online_payment_report` | L1250-1254 | PAGE | — | — | View load |
| `paydatewise_schemecoll_data` | L409-413 | PAGE | — | — | View load |
| `paydatewise_schemecoll_list` | L414-445 | AJAX | `payment`, `scheme_account`, `scheme`, `branch` | — | `paydatewise_schemecoll_list` |
| `payment_by_daterange` | L240-244 | PAGE | — | — | View load |
| `payment_by_range` | L154-160 | PAGE | `payment` | — | View load (loads dues upfront) |
| `payment_cancel_list` | L1288-1292 | PAGE | — | — | View load |
| `payment_date_range` | L173-183 | AJAX | `payment` | — | `payment_date_range` |
| `payment_datewise` | L102-111 | PAGE | `payment` | — | View load (loads today's data) |
| `payment_datewise_ajax` | L112-121 | AJAX | `payment` | — | `payment_datewise_ajax` |
| `payment_datewise_data` | L367-371 | PAGE | — | — | View load |
| `payment_datewise_list` | L372-407 | AJAX | `payment`, `scheme_account`, `branch`, `scheme` | — | `payment_datewise_list` |
| `payment_details` | L45-51 | PAGE | — | — | View load |
| `payment_due_list` | L37-43 | PAGE | `payment` (dues) | — | View load |
| `payment_employee` | L68-74 | AJAX | `employee`, `branch` | — | `payment_employee` |
| `payment_employee_wise` | L60-67 | PAGE | `payment`, `employee` | — | View load |
| `payment_list_daterange` | L245-320 | AJAX | `payment`, `scheme_account`, `customer`, `scheme`, `branch` | — | `payment_list_daterange` — GST calc in controller |
| `payment_modeandgroupwise_data` | L1851-1855 | PAGE | — | — | View load |
| `payment_modeandgroupwise_list` | L1856-1865 | AJAX | `payment`, `scheme_group` | — | `payment_modeandgroupwise_list` |
| `payment_modewise` | L185-192 | PAGE | `payment` | — | View load (loads modewise upfront) |
| `payment_modewise_data` | L322-326 | PAGE | — | — | View load |
| `payment_modewise_list` | L327-365 | AJAX | `payment`, `payment_mode` | — | `payment_modewise_list` — GST calc |
| `payment_outstanding` | L447-451 | PAGE | — | — | View load |
| `payment_outstanding_list` | L452-493 | AJAX | `payment`, `scheme_account`, `customer`, `scheme` | — | `payment_outstanding_list` |
| `payment_schemewise` | L85-91 | PAGE | — | — | View load |
| `payment_schemewise_detail` | L92-100 | AJAX | `payment`, `scheme` | — | `payment_schemewise_detail` |
| `payment_summary_modewise` | L2019-2049 | AJAX | `payment` (offline/online/admin_app) | — | `payment_summary_modewise` |
| `paymentcancel_list` | L1293-1306 | AJAX | `payment` (cancelled) | — | `paymentcancel_list` |
| `payments_on_off_collection_data` | L1073-1077 | PAGE | — | — | View load |
| `payments_on_off_collection_list` | L1078-1111 | AJAX | `payment`, `branch` | — | `payments_on_off_collection_list` |
| `purch_delivered` | L1054-1069 | AJAX | `purch_payment` | `purch_payment` | `purch_delivered` — OTP verified delivery |
| `renewal_live_report` | L2056-2060 | PAGE | — | — | View load |
| `sch_enquirt_list` | L980-984 | PAGE | — | — | View load |
| `scheme_account_report` | L139-153 | PAGE | `scheme_account`, `payment` | — | URL param: id_scheme_account |
| `scheme_customer_daterange` | L1525-1529 | PAGE | — | — | View load |
| `scheme_customer_list_daterange` | L1530-1537 | AJAX | `scheme_account`, `customer`, `scheme`, `address` | — | `scheme_customer_list_daterange` |
| `scheme_daily_collection_details` | L1154-1208 | AJAX | `scheme`, `payment`, `scheme_account` | — | `scheme_daily_collection_details` — **BUG: `$today` used before init at L1163** |
| `scheme_payment_daterange` | L1308-1312 | PAGE | — | — | View load |
| `scheme_payment_list_daterange` | L1313-1346 | AJAX | `payment`, `scheme`, `branch`, `payment_mode` | — | `scheme_payment_list_daterange` |
| `scheme_summary` | L1557-1578 | AJAX | `scheme`, `scheme_group`, `scheme_account` | — | `scheme_summary` |
| `schenquiry_list` | L985-997 | AJAX | `scheme_enquiry` | — | `schenquiry_list` |
| `update_cusdatas` | L769-785 | AJAX | `customer_reg` | `customer_reg` | raw `$_POST['postData']` |
| `update_kyc` | L930-971 | AJAX | `customer_kyc`, `agent_kyc` | `customer_kyc`, `agent_kyc`, `customer`, `agent` | `update_kyc` |
| `update_transdatas` | L787-804 | AJAX | `transaction` | `transaction` | raw `$_POST['postData']` |
| `updateAccountDetails` | L1726-1755 | AJAX | `customer`, `scheme_account` | `scheme_account` | `updateAccountDetails` |
| `updatePaymentDetails` | L1713-1725 | AJAX | `payment` | `payment` | `updatePaymentDetails` — writes log file |
| `verify_otp` | L1045-1053 | AJAX | session | — | `verify_otp` |

---

## 7b. Model Methods — `payment_model.php` (Key Methods for Reports)

| Method | Lines* | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `ajax_get_customers_list` | — | `purch_customer` | — | `ajax_get_customers_list` ctrl |
| `ajax_get_purchase_payment` | — | `purch_payment`, `purch_customer` | — | `ajax_get_purchase_payment` ctrl |
| `ajax_getPayModeList` | — | `payment_mode` | — | `ajax_getPayModeList` ctrl |
| `failed_payments` | — | `payment` (status=-1) | — | `failed_data` ctrl |
| `general_advance_list` | — | `general_advance_payment`, `scheme_account`, `customer` | — | `general_advance_list` ctrl |
| `general_advance_list_byid` | — | `general_advance_payment` | — | `general_advance_list_byid` ctrl |
| `get_active_scheme` | — | `scheme` | — | `scheme_daily_collection_details` ctrl |
| `get_all_closed_account_by_date` | — | `scheme_account`, `payment`, `customer`, `scheme` | — | `closedaccount_list` ctrl |
| `get_all_emp_account_by_range` | — | `scheme_account`, `payment`, `employee`, `branch` | — | `ajax_get_emp_account_list` ctrl |
| `get_area` | — | area table | — | `get_area` ctrl |
| `get_branchwise_emp` | — | `employee`, `branch` | — | `branchwise_employee` ctrl |
| `get_cancel_payment` | — | `payment` (status=4) | — | `paymentcancel_list` ctrl |
| `get_city` | — | city table | — | `get_city` ctrl |
| `get_closed_summary_by_date` | — | `scheme_account`, `payment` | — | `closedaccount_list` ctrl |
| `get_customer_account_details` | — | `scheme_account`, `customer` | — | `customer_account_details` ctrl |
| `get_empreff_report` | — | `payment`, `employee`, `customer` | — | `employee_ref_success_list` ctrl |
| `get_empreff_report_by_range` | — | `payment`, `employee`, `customer` | — | `employee_ref_success_list` ctrl |
| `get_employee_name` | — | `employee`, `branch` | — | `payment_employee` ctrl |
| `get_group_modewise_list` | — | `payment`, `scheme_group`, `payment_mode` | — | `payment_modeandgroupwise_list` ctrl |
| `get_intertable_list` | — | `customer_reg`, `customer` | — | `intertable_list` ctrl |
| `get_intertable_translist` | — | `transaction` | — | `intertable_translist` ctrl |
| `get_kycdata_range` | — | `customer_kyc` | — | `kycapproval_data` ctrl |
| `get_modewise_list` | — | `payment`, `payment_mode` | — | `payment_modewise_list` ctrl |
| `get_online_payment_report_date` | — | `payment` (online) | — | `get_online_payment_report` ctrl |
| `get_payment_list` | — | `payment`, `employee` | — | `ajax_payment_list` ctrl |
| `get_payment_modewise` | — | `payment`, `payment_mode` | — | `payment_modewise` ctrl |
| `get_payment_status` | — | `payment_status` | — | `get_payment_status` ctrl |
| `get_Scheme_Payment_ModeWiseummaryDetails` | — | `payment`, `scheme`, `payment_mode` | — | `scheme_payment_list_daterange` ctrl |
| `getScheme_Opening_blc_details` | — | `scheme_account`, `payment` | — | `scheme_daily_collection_details` ctrl |
| `get_scheme_list` | — | `scheme` | — | `getscheme_name` ctrl |
| `get_today_collection_details` | — | `payment`, `scheme_account` | — | `scheme_daily_collection_details` ctrl |
| `getMemberReport` | — | `scheme_account`, `customer`, `scheme`, `address` | — | `getMemberReport` ctrl |
| `maturity_report_data` | — | `scheme_account`, `scheme`, `customer` | — | `maturity_report_data` ctrl |
| `monthly_report_data` | — | `payment`, `scheme_account`, `scheme` | — | `monthly_report_data` ctrl |
| `payment_cancel` | — | `payment` | `payment` (status=4) | `cancel_payment` ctrl |
| `payment_datewise` | — | `payment`, `branch` | — | `payment_datewise` / `payment_datewise_ajax` ctrl |
| `payment_datewise_by_mode` | — | `payment`, `payment_mode` | — | `payment_datewise_list` ctrl |
| `payment_datewise_list` | — | `payment`, `branch`, `scheme` | — | `payment_datewise_list` ctrl |
| `payment_list_daterange` | — | `payment`, `scheme_account`, `customer`, `scheme`, `branch` | — | `payment_list_daterange` ctrl |
| `payment_outlist` | — | `scheme_account`, `payment`, `customer` | — | `payment_outstanding_list` ctrl |
| `payment_summary_modewise_data` | — | `payment` (offline/online/adminapp split) | — | `scheme_payment_list_daterange`, `payment_summary_modewise` ctrl |
| `paymentcancel_list_range` | — | `payment` (cancelled, date range) | — | `paymentcancel_list` ctrl |
| `paymentDB` | — | `payment` | — | `cancel_payment` ctrl |
| `payment_statusDB` | — | `payment_status_log` | `payment_status_log` | `cancel_payment` ctrl |
| `paydatewise_schemecoll` | — | `payment`, `scheme`, `branch` | — | `paydatewise_schemecoll_list` ctrl |
| `payments_on_off_collection_list` | — | `payment`, `branch` | — | `payments_on_off_collection_list` ctrl |
| `renewalLive_arlData` | — | `scheme_account`, `payment` | — | `getRenewalLive_arlData` ctrl |
| `sheme_payment_list_daterange` | — | `payment`, `scheme`, `branch`, `payment_mode` | — | `scheme_payment_list_daterange` ctrl |
| `total_paid_unpaid` | — | `payment`, `scheme` | — | `payment_schemewise_detail` ctrl |
| `updatekyc` | — | `customer_kyc` | `customer_kyc` | `update_kyc` ctrl |
| `updatekyccus` | — | `customer` | `customer` | `update_kyc` ctrl |

*Line numbers require opening payment_model.php — omitted for brevity, grep by method name.

---

## 7c. JS → Controller AJAX Map (Internal `admin_reports` endpoints)

| JS Line* | JS Function | AJAX URL | Controller Method |
|---|---|---|---|
| ~50 | `set_remark` | `admin_manage/set_remarks_byid` | **Cross-module** |
| ~635 | `get_kyc_list` | `admin_reports/kycapproval_data` | `kycapproval_data` |
| ~670 | `update_kyc_status` | `admin_reports/update_kyc` | `update_kyc` |
| ~700 | `get_sch_enq_list` | `admin_reports/schenquiry_list` | `schenquiry_list` |
| ~800 | `get_purchase_payment` | `admin_reports/ajax_get_purchase_payment` | `ajax_get_purchase_payment` |
| ~830 | `generateOTP` | `admin_reports/generateotp` | `generateotp` |
| ~855 | `verifyOTP` | `admin_reports/verify_otp` | `verify_otp` |
| ~875 | `deliverPurchase` | `admin_reports/purch_delivered` | `purch_delivered` |
| ~910 | `get_autodebit_subscription` | `admin_reports/ajax_get_autodebit_subscription` | `ajax_get_autodebit_subscription` |
| ~950 | `getSchemeDateRangeList` | `admin_reports/scheme_customer_list_daterange` | `scheme_customer_list_daterange` |
| ~980 | `scheme_daily_collection_details` | `admin_reports/scheme_daily_collection_details` | `scheme_daily_collection_details` |
| ~1010 | `get_closed_list` | `admin_reports/closedaccount_list` | `closedaccount_list` |
| ~1040 | `get_acc_details` | `admin_reports/customer_account_details/ajax` | `customer_account_details` |
| ~1060 | `get_online_payment` | `admin_reports/get_online_payment_report` | `get_online_payment_report` |
| ~1080 | `get_payment_status` | `admin_reports/get_payment_status` | `get_payment_status` |
| ~1100 | `get_cancel_pay_list` | `admin_reports/paymentcancel_list` | `paymentcancel_list` |
| ~1130 | `getSchemeDateList` | `admin_reports/scheme_payment_list_daterange` | `scheme_payment_list_daterange` |
| ~1155 | `get_payModeList` | `admin_reports/ajax_getPayModeList` | `ajax_getPayModeList` |
| ~1180 | `getSummaryData` | `admin_reports/scheme_summary` | `scheme_summary` |
| ~1210 | `get_gift_report` | `admin_reports/get_online_gift_report` | `get_online_gift_report` |
| ~1230 | `get_acc_by_id` | `admin_reports/editAccOrPayments/get_acc_byId` | `editAccOrPayments` |
| ~1250 | `get_pay_by_id` | `admin_reports/editAccOrPayments/get_pay_byId` | `editAccOrPayments` |
| ~1270 | `update_payment` | `admin_reports/updatePaymentDetails` | `updatePaymentDetails` |
| ~1290 | `update_account` | `admin_reports/updateAccountDetails` | `updateAccountDetails` |
| ~1310 | `gen_trans_id` | `admin_reports/generateTransUniqId` | `generateTransUniqId` |
| ~1330 | `get_area_list` | `admin_reports/get_area` | `get_area` |
| ~1345 | `get_joined` | `admin_reports/get_joined_through` | `get_joined_through` |
| ~1360 | `get_member_rep` | `admin_reports/getMemberReport` | `getMemberReport` |
| ~1380 | `get_gen_adv_byid` | `admin_reports/general_advance_list_byid` | `general_advance_list_byid` |
| ~1400 | `get_maturity_data` | `admin_reports/maturity_report_data` | `maturity_report_data` |

*JS line numbers approximate — grep `url:` in reports.js for exact lines.

---

## 7d. Table → Methods Reverse Map

| Table | Read By (controller/model) | Written By |
|---|---|---|
| `payment` | Almost all methods | `cancel_payment` (status=4), `updatePaymentDetails`, `generateTransUniqId`, `purch_delivered` |
| `scheme_account` | `closedaccount_list`, `scheme_customer_list_daterange`, `payment_outstanding_list`, `getMemberReport` | `updateAccountDetails` |
| `customer` | Most account/member reports | `updateAccountDetails` (indirectly via customer_model) |
| `scheme` | `scheme_summary`, `scheme_daily_collection_details`, `getMemberReport` | — |
| `branch` | `branchwise_employee`, `payment_datewise_list`, `paydatewise_schemecoll_list` | — |
| `customer_kyc` | `kycapproval_data` | `update_kyc` |
| `gift_issued` | `ajax_gift_report`, `get_online_gift_report` | — |
| `general_advance_payment` | `general_advance_list`, `general_advance_list_byid` | — |
| `payment_status_log` | — | `cancel_payment` |
| `customer_enquiry` | `ajax_enquiry_list` | `enquiry` (UpdateStatus) |
| `transaction` | `intertable_translist` | `update_transdatas` |
| `customer_reg` | `intertable_list` | `update_cusdatas` |
