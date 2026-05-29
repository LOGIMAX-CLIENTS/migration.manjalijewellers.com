# ROUTES MAP — chit_reports
> Round R6-Upgrade — 2026-03-25
> Source: `admin/application/config/routes.php`

---

## Key Finding: Dual Route System

`reports.js` uses two URL patterns for the same controller:
- `index.php/admin_reports/{method}` — **direct controller route**
- `index.php/reports/{alias}` — **CI route alias** → mapped to `admin_reports/{method}`

Both are fully functional. The route aliases in `routes.php` exist because the controller was **renamed from `reports` to `admin_reports`** during a refactoring. Old menu links and JS calls were updated to use `reports/` aliases instead of a full rename — migration incomplete.

> 🔑 **For bug diagnosis**: when a JS error shows URL `reports/payment_modewise`, look it up here, find the real controller method, then check `admin_reports.php`.

---

## Complete Route Alias Table

| Route Alias (`index.php/...`) | Maps To (`admin_reports/...`) | Notes |
|---|---|---|
| `reports/collection_report` | `admin_reports/collection_report` | |
| `reports/payment_modewise_data` | `admin_reports/payment_modewise_data` | |
| `reports/paymentmodewise_datalist` | `admin_reports/payment_modewise_list` | Note: alias ≠ method name |
| `reports/payment_datewise_schemedata` | `admin_reports/payment_datewise_data` | |
| `reports/payment_datewise_schemelist` | `admin_reports/payment_datewise_list` | |
| `reports/payment_online_offline_collec_data` | `admin_reports/payments_on_off_collection_data` | |
| `reports/payment_online_offline_collec_list` | `admin_reports/payments_on_off_collection_list` | |
| `reports/payment_cancel_report` | `admin_reports/payment_cancel_list` | |
| `reports/paydatewise_schcoll_data` | `admin_reports/paydatewise_schemecoll_data` | |
| `reports/paydatewise_schcoll_list` | `admin_reports/paydatewise_schemecoll_list` | |
| `reports/payment_outstanding` | `admin_reports/payment_outstanding` | |
| `reports/payment_outstanding_list` | `admin_reports/payment_outstanding_list` | |
| `reports/customer_enquiry` | `admin_reports/customer_enquiry` | |
| `reports/payment_pending` | `admin_reports/payment_due_list/` | |
| `reports/payment_list/(:any)` | `admin_reports/payment_list/$1` | URL param passthrough |
| `reports/payment_details` | `admin_reports/payment_details` | |
| `reports/payment_schemewise` | `admin_reports/payment_schemewise` | |
| `reports/payment_datewise` | `admin_reports/payment_datewise` | |
| `reports/payment_datewise_ajax` | `admin_reports/payment_datewise_ajax` | |
| `reports/payment_modewise` | `admin_reports/payment_modewise` | |
| `reports/accounts_schemewise` | `admin_reports/accounts_schemewise` | |
| `reports/payment/account/(:any)` | `admin_reports/scheme_account_report/$1` | |
| `reports/payment/range` | `admin_reports/payment_by_range` | |
| `reports/payment/range/date` | `admin_reports/payment_date_range` | |
| `reports/payment/failed` | `admin_reports/failed_payments` | |
| `reports/get/payment/failed` | `admin_reports/failed_data` | |
| `reports/update/daily_collection` | `admin_rateapi/update_daily_collection` | Cross-controller |
| `reports/employee_ref_success_list` | `admin_reports/employee_ref_success_list` | |
| `reports/payment_cus_ref_success` | `admin_reports/cus_ref_success_list` | |
| `reports/payment/refferl_account/(:any)` | `admin_reports/emp_referral_account/$1` | |
| `reports/payment/cus_refferl_account/(:any)` | `admin_reports/cus_refferl_account/$1` | |
| `reports/employee_ref_success` | `admin_reports/employee_ref_success` | |
| `reports/cus_ref_success` | `admin_reports/cus_ref_success` | |
| `reports/member_report` | `admin_reports/member_report` | |
| `reports/Employee_account` | `admin_reports/employee_account` | |
| `reports/ajax_emp_account_list` | `admin_reports/ajax_get_emp_account_list` | |
| `log/ajax_list` | `admin_reports/log/Ajax` | Switch-based |
| `log/ajax_list_detail` | `admin_reports/log/Detail` | |
| `log/list` | `admin_reports/log/List` | |
| `log/detail/(:any)` | `admin_reports/log/View/$1` | |
| `form_logger/(:any)` | `admin_reports/form_logger/$1` | |
| `form_logger/(:any)/(:any)` | `admin_reports/form_logger/$1/$2` | |
| `reports/detail/registration/(:any)` | `admin_dashboard/reg_detail/$1` | Cross-controller |
| `reports/detail/account/(:any)` | `admin_dashboard/acc_detail/$1` | Cross-controller |
| `reports/detail/renewals/(:any)` | `admin_dashboard/get_renewals_list/$1` | Cross-controller |
| `reports/detail/closed_acc/(:any)` | `admin_dashboard/closed_acc_detail/$1` | Cross-controller |
| `reports/detail/close_due/(:any)` | `admin_dashboard/about_to_close/$1` | Cross-controller |
| `reports/detail/payment/(:any)` | `admin_dashboard/pay_detail/$1` | Cross-controller |
| `reports/detail/awaiting` | `admin_dashboard/awaiting_detail` | Cross-controller |
| `reports/detail/enquiry/(:any)` | `admin_dashboard/enquiry_detail/$1` | Cross-controller |
| `reports/detail/pay_status/(:any)/(:any)` | `admin_dashboard/paid_unpaid_status/$1/$2` | Cross-controller |
| `reports/detail/all_pay_status` | `admin_dashboard/total_payment_details` | Cross-controller |
| `reports/detail/due/(:any)` | `admin_dashboard/due_list/$1` | Cross-controller |
| `reports/employee/referral` | `admin_reports/get_employee_details` | |
| `reports/inter_wallet_detail/(:any)` | `admin_dashboard/inter_wallet_details/$1` | Cross-controller |
| `reports/inter_wallet_woc` | `admin_dashboard/inter_wallet_accounts__woc` | Cross-controller |
| `reports/employee_wise_collection` | `admin_reports/employee_wise_summary` | |
| `reports/employee_wise_summary` | `admin_reports/employee_collection` | |
| `reports/inter_table/list` | `admin_reports/inter_table` | **DUPLICATE** ×2 |
| `reports/intertable_list` | `admin_reports/intertable_list` | |
| `reports/intertable_translist` | `admin_reports/intertable_translist` | |
| `reports/get_purchase_payment` | `admin_reports/get_purchase_payment` | |
| `reports/get_autodebit_subscription` | `admin_reports/get_autodebit_subscription` | |
| `reports/scheme_payment_daterange` | `admin_reports/scheme_payment_daterange` | |
| `reports/gift_report` | `admin_reports/get_gift_report` | |
| `reports/closed_acc_report` | `admin_reports/closed_account_list` | |
| `reports/closedaccount_list` | `admin_reports/closedaccount_list` | |
| `reports/scheme_customer_daterange` | `admin_reports/scheme_customer_daterange` | |
| `reports/customer_wishes/(:any)` | `admin_reports/customer_wishes/$1` | |
| `reports/edit_acc_pay` | `admin_reports/edit_acc_pay` | |
| `reports/payment_modeandgroupwise_data` | `admin_reports/payment_modeandgroupwise_data` | |
| `reports/payment_modeandgroupwise_datalist` | `admin_reports/payment_modeandgroupwise_list` | |
| `reports/general_advance` | `admin_reports/general_advance_view` | |
| `reports/general_advance_list` | `admin_reports/general_advance_list` | |
| `reports/monthly_chit_report` | `admin_reports/monthly_report_view` | |
| `reports/maturity_report` | `admin_reports/maturity_report_view` | |
| `reports/get_yet_to_issue` | `admin_reports/get_yet_to_issue` | |
| `reports/renewal_live_report` | `admin_reports/renewal_live_report` | |
| `get/schemename_list` | `admin_reports/getscheme_name` | |
| `get/giftname_list` | `admin_reports/giftname_list` | |
| `reports/ledger` | `admin_ret_reports/ledger_report/list` | Cross-module (retail) |
| `reports/ledger/ajax` | `admin_ret_reports/ledger_report/ajax` | Cross-module (retail) |
| `reports/detail/registration_bydate/(:any)/(:any)` | `admin_dashboard/reg_detail_bydate/$1/$2` | Cross-controller |

---

## Route Registration Bugs

| Issue | Lines | Details |
|---|---|---|
| **Duplicate route** | routes.php L1596+L1600 | `reports/inter_table/list` registered twice — second wins |
| **Alias mismatch** | Multiple | `paymentmodewise_datalist` → `payment_modewise_list` — alias name ≠ method name; confusing |
| **Cross-department routes** | L1878-1879 | `reports/ledger` maps to `admin_ret_reports` — retail module bleeding into chit routes |

---

## No Separate `reports` Controller

There is **no** `reports.php` controller file. The `index.php/reports/` URL prefix is purely a CI routing alias system. The `collection_report.php` file in the controllers directory is a separate controller for a different feature — not the old reports controller.
