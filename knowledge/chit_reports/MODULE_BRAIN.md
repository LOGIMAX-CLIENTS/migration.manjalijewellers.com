# CHIT_REPORTS MODULE BRAIN — v2.0
**Created**: 2026-03-13 | **Author**: Antigravity AI
**Source files scanned**: admin_reports.php (2137L) · payment_model.php (8904L) · account_model.php (3651L) · reports.js (16149L) · 51 view files

---

## §1 — MODULE OVERVIEW

| Field | Value |
|---|---|
| Module Name | chit_reports |
| Bug Prefix | RPT |
| Framework | CodeIgniter (PHP 7.1) |
| Purpose | Full reporting engine: payments, collections, members, gifts, accounts, analytics |
| Controller | `admin/application/controllers/admin_reports.php` |
| Primary Model | `admin/application/models/admin_report_model.php` |
| Heavy Models | `payment_model.php` (8904 lines), `account_model.php` (3651 lines) |
| JS | `admin/assets/js/reports.js` (16,149 lines — monolithic) |
| Views | `admin/application/views/reports/` (51 PHP files + 5 subdirs) |
| Auth | Session `is_logged` check in `__construct()` → redirects to `admin/login` |
| Branch Filter | `branch_settings`, `branchWiseLogin`, `id_branch` session vars |

---

## §2 — FILE MANIFEST

| File | Lines | Role |
|---|---|---|
| `admin_reports.php` | 2137 | All routes, data assembly, GST calc, Excel export |
| `admin_report_model.php` | 591 | Enquiry, gift, msg91, celeb dates queries |
| `payment_model.php` | 8904 | Core payment queries — 180+ methods |
| `account_model.php` | 3651 | Scheme account queries — 120+ methods |
| `reports.js` | 16149 | All AJAX calls, table render, date pickers |
| `reports/monthly_chit_report.php` | 196 | Month/year/branch/scheme filter view |
| `reports/scheme_payment_daterange.php` | ~400 | Source-wise payment (most complex view) |
| `reports/payment_accountwise.php` | ~640 | Account-wise payment detail |
| `reports/payment_history.php` | ~650 | Payment history with cancel action |
| `reports/member_report.php` | ~340 | Member joins/city/area filters |
| `reports/maturity_report.php` | ~310 | Maturity/closure report |
| `reports/general_adv_payment_list.php` | ~400 | General advance payments |
| `reports/gift_report.php` | ~210 | Gift issued report |
| `reports/closed_acc_report.php` | ~260 | Closed accounts |
| `reports/dashboard.php` | 69636 | Master dashboard (largest file) |

---

## §3 — ROUTES & ENTRY POINTS

| Route | Method | AJAX? | Params | Model Method |
|---|---|---|---|---|
| `payment_due_list` | GET | ✗ | — | `payment_model->get_payment_dues_details()` |
| `ajax_customer_payment_details` | POST | ✓ | `id_scheme` | `payment_model->get_payment_report()` |
| `payment_employee` | POST | ✓ | `id_branch` | `payment_model->get_employee_name()` |
| `ajax_payment_list` | POST | ✓ | `id_emp,from_date,to_date,id_branch` | `payment_model->get_payment_list()` |
| `payment_schemewise_detail` | POST | ✓ | `id_branch,from_date,to_date` | `payment_model->total_paid_unpaid()` |
| `payment_datewise_ajax` | POST | ✓ | `date` | `payment_model->payment_datewise()` |
| `payment_list_daterange` | POST | ✓ | `from_date,to_date,type,limit,id,id_employee,acc` | `payment_model->payment_list_daterange()` |
| `payment_modewise_list` | POST | ✓ | `from_date,to_date,type,limit,id` | `payment_model->get_modewise_list()` |
| `payment_datewise_list` | POST | ✓ | `date` | `payment_model->payment_datewise_list()` |
| `paydatewise_schemecoll_list` | POST | ✓ | `date` | `payment_model->paydatewise_schemecoll()` |
| `payment_outstanding_list` | POST | ✓ | `date` | `payment_model->payment_outlist()` |
| `scheme_payment_list_daterange` | POST | ✓ | 8 params | `payment_model->sheme_payment_list_daterange()` + 2 more |
| `payment_summary_modewise` | POST | ✓ | 8 params | `payment_model->payment_summary_modewise_data()` |
| `ajax_gift_report` | POST | ✓ | `from_date,to_date,id_branch,id_metal,id_scheme,id_gift` | `admin_report_model->get_gift_list()` |
| `scheme_customer_list_daterange` | POST | ✓ | — | `account_model->get_all_scheme_account_by_range()` |
| `getMemberReport` | POST | ✓ | — | `payment_model->getMemberReport()` |
| `monthly_report_data` | POST | ✓ | `month,year,id_branch,id_scheme` | `payment_model->monthly_report_data()` |
| `maturity_report_data` | POST | ✓ | `from_date,to_date,id_branch,id_scheme,emp_code` | `payment_model->maturity_report_data()` |
| `general_advance_list` | POST | ✓ | `from_date,to_date,mode,id_scheme,added_by,id_branch` | `payment_model->general_advance_list()` |
| `closedaccount_list` | POST | ✓ | `from_date,to_date,id_branch,id_employee,close_id_branch` | `payment_model->get_all_closed_account_by_date()` |
| `ajax_get_emp_account_list` | POST | ✓ | `from_date,to_date,id_branch,id_employee` | `payment_model->get_all_emp_account_by_range()` |
| `cancel_payment` | POST | ✓ | `id_payment[]` | `payment_model->payment_cancel()` |
| `updatePaymentDetails` | POST | ✓ | `$_POST` | `payment_model->updatePaymentdata()` |
| `updateAccountDetails` | POST | ✓ | `$_POST` | `payment_model->updateDatacus()` |
| `ajax_enquiry_list` | POST | ✓ | `from_date,to_date,status,type` | `admin_report_model->get_customerenquiry_by_date()` |
| `cus_celeb_dates` | POST | ✓ | `from_date,to_date` | `admin_report_model->get_all_cus_celeb_dates()` |
| `getRenewalLive_arlData` | POST | ✓ | — | `payment_model->renewalLive_arlData()` |
| `exl_rep_outstanding/{type}` | GET | ✗ | `type` | `account_model->get_all_scheme_account_by_range()` |
| `ajax_enquiry_list` | POST | ✓ | `from_date,to_date,status,type` | `admin_report_model->get_customerenquiry_by_date()` |

---

## §4 — EXECUTION FLOW DIAGRAMS

### Flow 1 — Source-Wise Payment Report (Most Complex)
```
[View: scheme_payment_daterange.php]
  User selects: from_date, to_date, id_classfication, id_scheme, pay_mode, id_branch, mode, metal_date_type
  └── JS ajax POST → scheme_payment_list_daterange
        Controller:
          ├── payment_model->sheme_payment_list_daterange(8 params)
          │     SQL: payment_mode_details pmd JOIN payment p JOIN scheme_account sa
          │          JOIN customer c JOIN scheme s JOIN branch b
          │          + LEFT JOIN bank, village, ret_bill_pay_device
          │          WHERE p.payment_status=1 AND pmd.payment_status=1 AND pmd.is_active=1
          │          GROUP BY pmd.id_pay_mode_details
          │     POST loops: foreach → format_accRcptNo() → metal_weight recalc by scheme_type/flexible_sch_type
          │     Returns: $return_data keyed by scheme_name OR village_name OR 'Payment'
          │
          ├── payment_model->get_Scheme_Payment_ModeWiseummaryDetails(8 params)
          │     SQL: SUM(payment_amount), GST calcs grouped by pmd.payment_mode
          │     ⚠️ RISK: uses undefined $limit variable at L7080
          │
          └── payment_model->payment_summary_modewise_data(9 params)
                SQL: date-split on edit_custom_entry_date → offline/online/admin_app sub-arrays
                Returns: nested array keyed by [offline/online/admin_app][date]
        Controller: foreach offline/online/admin_app → array_sum()
        ⚠️ RISK: array_sum() crashes if subarray null (no payments in range)
  └── JSON → JS renders 3 section DataTables
```

### Flow 2 — Monthly Chit Report
```
[View: monthly_chit_report.php]
  Filter: month (select), year (text input), branch, scheme
  └── JS #search_monthly_list click → POST monthly_report_data
        Model: monthly_report_data()
          SQL: payment p JOIN payment_mode_details pmd
               CASE WHEN pmd.payment_mode='CSH' THEN amount END as cash
               CASE WHEN CC||DC THEN amount END as card
               CASE WHEN CHQ THEN amount END as cheque
               CASE WHEN NB THEN amount END as nb
               CASE WHEN UPI THEN amount END as upi
               (everything else) as wallet
               WHERE month(p.date_payment)=$month AND year=$year
               GROUP BY date(p.date_payment)
          ⚠️ RISK: $month/$year read from POST inside model (not passed as params!)
  └── JSON → JS renders date-wise rows with totals in tfoot
```

### Flow 3 — Maturity Report (Per-Scheme Loop)
```
[View: maturity_report.php]
  └── JS POST → maturity_report_data
        Model: maturity_report_data()
          Queries ALL schemes first → foreach scheme:
            if installment_cycle==1 (days):
              mat_from = start_date - total_installments days
            else (months):
              mat_from = start_date - total_installments months
            SQL per scheme: WHERE date(sa.start_date) BETWEEN mat_from AND mat_to
                            AND sa.id_scheme=X AND sa.is_closed=0 AND sa.active=1
                            AND sa.total_paid_ins > 0
          ⚠️ RISK: N+1 queries — 1 query per scheme (can be 50+ schemes)
  └── JSON → JS renders
```

### Flow 4 — Cancel Payment
```
[View: payment_history.php] → checkboxes → #cancel_payment_btn
  └── JS POST → cancel_payment {id_payment: [array]}
        Controller: foreach id_payment:
          1. payment_model->payment_cancel('update', id, {payment_status:4})
          2. payment_model->paymentDB('get', id)
          3. if integrationType==2: syncapi_model->updPayStatusInTrans()
          4. payment_model->payment_statusDB('insert', id, log_array)
          ⚠️ RISK: No DB transaction — partial cancels possible if step 4 fails
          ⚠️ RISK: echo TRUE (not JSON) — JS success handler receives raw "1"
```

### Flow 5 — General Advance List
```
Model: general_advance_list()
  Reads POST: from_date, to_date, mode, id_scheme, added_by, id_branch
  FROM general_advance_mode_detail gapd
  LEFT JOIN general_advance_payment gap
  LEFT JOIN scheme_account, scheme, customer, branch, employee
  WHERE date BETWEEN ... AND gap.payment_status=1 AND gapd.is_active=1
  → format_accRcptNo() for scheme_acc_number and receipt_no
```

---

## §5 — CONTROLLER METHOD CATALOG

| Method | Lines | Risk | Notes |
|---|---|---|---|
| `__construct()` | 15-35 | — | Loads 10 models |
| `payment_due_list()` | 37-43 | — | Simple view |
| `ajax_customer_payment_details()` | 52-58 | ⚠️ | `id_scheme` received but NOT passed to model |
| `payment_list_daterange()` | 245-320 | 🔴 | GST/discount `$pay` variable conflict |
| `payment_modewise_list()` | 327-365 | 🟡 | Same GST pattern |
| `payment_datewise_list()` | 372-407 | — | Returns mode_wise additionally |
| `payment_outstanding_list()` | 452-493 | 🟡 | `due_count = total - paid` (no null check) |
| `cancel_payment()` | 665-695 | 🔴 | No transaction, echo TRUE |
| `intertable_list()` | 747-758 | 🔴 | raw `$_POST` |
| `update_cusdatas()` | 769-785 | 🔴 | raw `$_POST` |
| `update_transdatas()` | 787-804 | 🔴 | raw `$_POST` |
| `ajax_enquiry_list()` | 600-615 | 🔴 | `last_query()` in JSON response |
| `scheme_daily_collection_details()` | 1154-1208 | 🔴 | `$today` used before assigned (L1163) |
| `closedaccount_list()` | 1217-1230 | 🔴 | `$model` assigned twice (account_model overwritten) |
| `scheme_payment_list_daterange()` | 1313-1346 | 🟡 | 3 model calls; array_sum on possibly null array |
| `ajax_gift_report()` | 1505-1522 | 🟡 | Loads customer_model inline |
| `exl_rep_outstanding()` | 1593-1670 | — | PHPExcel export |
| `updatePaymentDetails()` | 1713-1725 | 🟡 | `file_put_contents` relative path |
| `updateAccountDetails()` | 1726-1755 | 🟡 | Branch validation via `checkCommonSettings()` |
| `getMemberReport()` | 1895-1899 | 🟡 | No filters — returns ALL members |
| `monthly_report_data()` | 1940-1944 | 🟡 | Filters read inside model (not params) |
| `maturity_report_data()` | 1989-1993 | 🟡 | N+1 queries (1 per scheme) |
| `general_advance_list()` | 1922-1927 | — | Filters read inside model |
| `getRenewalLive_arlData()` | 2061-2065 | — | — |
| `cus_celeb_dates()` | 2072-2099 | ✅ | Good pattern: try/catch |
| `payment_summary_modewise()` | 2019-2049 | 🟡 | array_sum on null possible |
| `checkCommonSettings()` | 2101-2135 | ✅ | Clean validation helper |

---

## §6 — DATABASE INTERACTION MAP

| Table | Op | Key Columns | Risk |
|---|---|---|---|
| `payment` | R/W | `id_payment, payment_amount, payment_status, date_payment, id_scheme_account, id_employee, payment_mode, sgst, cgst, receipt_no, is_offline, added_by` | 🟡 SELECT * in some methods |
| `payment_mode_details` | R | `id_pay_mode_details, id_payment, payment_mode, payment_amount, payment_status, is_active, cheque_date, net_banking_date, NB_type` | New table — source-wise report joins here |
| `scheme_account` | R/W | `id_scheme_account, id_scheme, id_customer, id_branch, scheme_acc_number, start_date, active, is_closed, total_paid_ins, group_code` | — |
| `customer` | R | `id_customer, firstname, lastname, mobile, date_of_birth, date_of_wed, id_village` | — |
| `scheme` | R | `id_scheme, scheme_name, code, scheme_type, is_lucky_draw, gst, gst_type, total_installments, installment_cycle, flexible_sch_type` | — |
| `branch` | R | `id_branch, name, short_name, show_to_all` | — |
| `employee` | R | `id_employee, firstname, lastname, emp_code` | — |
| `cust_enquiry` | R/W | `id_enquiry, ticket_no, status, enq_from, type` | 🔴 SQL injection |
| `gift_issued` | R | `id_gift_issued, id_scheme_account, id_gift, status, quantity` | 🟡 Multiple outer joins |
| `general_advance_payment` | R | `id_adv_payment, date_payment, payment_status, id_branch` | Separate table for advances |
| `general_advance_mode_detail` | R | `id_pay_mode_details, payment_mode, payment_amount, is_active` | Parallel to payment_mode_details |
| `chit_settings` | R | `id=1 hardcoded` | 🟡 Single-row settings |
| `payment_status_message` | R | `id_status_msg, payment_status, color` | Joined for status display |
| `village` | R | `id_village, village_name` | Area-wise report grouping |
| `bank` | R | `id_bank, short_code` | Payment mode details |
| `ret_bill_pay_device` | R | `id_device, device_name` | Payment device name |

### Critical SQL Pattern — payment_model.php `sheme_payment_list_daterange()`
```
FROM payment_mode_details pmd      ← NEW: uses pmd as primary, not payment
JOIN payment p ON p.id_payment = pmd.id_payment
LEFT JOIN bank bk ON bk.id_bank = pmd.id_bank
LEFT JOIN village v ON v.id_village = c.id_village
WHERE p.payment_status=1 AND pmd.payment_status=1 AND pmd.is_active=1
GROUP BY pmd.id_pay_mode_details   ← one row per payment split, NOT per payment
```
⚠️ **Key insight**: Multi-mode payments appear as MULTIPLE rows (one per mode detail). JS must sum for totals.

### `monthly_report_data()` — Key SQL Insight
```sql
SUM(CASE WHEN pmd.payment_mode='CSH' THEN pmd.payment_amount END) as cash
SUM(CASE WHEN pmd.payment_mode='CC'||'DC' THEN amount END) as card
SUM(CASE WHEN pmd.payment_mode='CHQ' THEN amount END) as cheque
SUM(CASE WHEN pmd.payment_mode='NB' THEN amount END) as nb
SUM(CASE WHEN pmd.payment_mode='UPI' THEN amount END) as upi
(everything_else) as wallet   ← catch-all: MCBA, wallet modes, etc.
GROUP BY date(p.date_payment)
```

---

## §7 — MODEL METHOD CATALOG (Report-Relevant)

### payment_model.php — Report Methods
| Method | Line | Params | Returns | Notes |
|---|---|---|---|---|
| `payment_list()` | 210 | `$id,$limit,$type` | array | Comprehensive payment fetch with 30+ columns |
| `payment_list_range()` | 311 | `$from,$to,$type,$limit,$date_type` | array | Reads `$_POST['id_status']`, `$_POST['id_customer']` 🔴 raw POST |
| `sheme_payment_list_daterange()` | 6910 | 8 params | nested array | Primary source-wise query; groups by report_type |
| `get_Scheme_Payment_ModeWiseummaryDetails()` | 7055 | 8 params | array | Mode-wise GST summary; `$limit` undefined at L7080 🔴 |
| `payment_summary_modewise_data()` | 7085 | 9 params | nested array | Splits on `edit_custom_entry_date` → offline/online/admin_app |
| `getMemberReport()` | 7883 | none | array | Reads area, city, joined_through data; massive query |
| `general_advance_list()` | 8297 | none | array | Reads all filters from POST; `general_advance_mode_detail` based |
| `monthly_report_data()` | 8392 | none | array | Reads month/year from POST; CASE-based mode breakdown |
| `maturity_report_data()` | 8436 | none | array | Per-scheme loop; N+1 queries; `installment_cycle` aware |
| `renewalLive_arlData()` | 8643 | none | array | ARL data for renewal live report |
| `updatePaymentdata()` | 7605 | `$postdata` | bool | Full payment edit with log |
| `payment_cancel()` | ~5000 | `$type,$id,$data` | result | Updates payment_status=4 |
| `paymentDB()` | 718 | `$type,$id,$pay_array` | mixed | CRUD switch for payment table |
| `payment_statusDB()` | ~800 | `$type,$id,$data` | mixed | CRUD for payment_status_message |

### account_model.php — Report Methods
| Method | Line | Returns | Notes |
|---|---|---|---|
| `get_all_account()` | 461 | array | All non-closed accounts (no filters!) |
| `get_all_scheme_account_by_range()` | 2421 | array | For outstanding/scheme customer reports |
| `get_all_closed_account()` | 678 | array | Uses CI query builder; branch-filtered |
| `get_closed_account_by_id()` | 627 | array | Single closed account detail |
| `get_all_account_by_range()` | 749 | array | All schemes with join/payment summary |
| `get_all_account_details()` | 528 | array | With cur_pay/total_pay subquery joins |
| `giftname_list()` | 3504 | array | Gift names for filter dropdown |
| `company_details()` | 493 | array | For OTP SMS message |
| `scheme_summary_data()` | 2711 | array | For scheme summary report |

---

## §8 — PAYMENT_MODEL CONSTANTS & TABLE MAP

```php
const ACC_TABLE  = "scheme_account"
const CUS_TABLE  = "customer"
const SCH_TABLE  = "scheme"
const PAY_TABLE  = "payment"
const BRANCH     = "branch"
const EMPLOYEE_TABLE = "employee"
const PAY_STATUS = "payment_status_message"
const MOD_TABLE  = "payment_mode"
const DC_TABLE   = "daily_collection"
const SETT_TABLE = "settlement"
const TRANS_TABLE= "transaction"
```

---

## §9 — STATE MANAGEMENT MAP

### JS Global Variables (reports.js top-level)
| Variable | Line | Risk |
|---|---|---|
| `usernamedata` | 1 | 🟡 Username exposed in JS global scope |
| `path` | 2 | 🔴 Redeclared at line 20 — original value lost |
| `ctrl_page` | 17 | ⚠️ Depends on `path` L2 value — if L20 runs first, breaks page detection |
| `app_url` | 21 | — |
| `currencyFormat` | 3 | — |
| `summarytitleHTML` | 15 | Used for print — global state mutation |
| `outstandingSummaryForPrint` | 16 | Used for print — global state mutation |

### DOM Hidden Field Registry
| Element ID | Set By | Read By | Notes |
|---|---|---|---|
| `#branch_filter` | PHP session (view render) | JS reports | Session-locked branch ID |
| `#id_branch` | daterangepicker select | AJAX POST | Can be 0 (all branches) |
| `#id_schemes` | scheme dropdown | AJAX POST | ⚠️ Some views use `#id_scheme` (mismatch) |
| `#payment_list1/2` | daterangepicker | payment list AJAX | Text node — empty if picker not triggered |
| `#rpt_payments1/2` | daterangepicker | scheme payment AJAX | — |
| `#id_customer` | autocomplete select | filter AJAX | — |
| `#month_select→#id_month` | month picker | monthly report AJAX | — |
| `#id_year` | text input | monthly report AJAX | No format validation |

---

## §10 — VALIDATION RULES CATALOG

| Field | Frontend | Backend | Risk |
|---|---|---|---|
| `from_date` | daterangepicker | `date('Y-m-d', strtotime(...))` | 🔴 `strtotime("")` → false → `date()` returns 1970-01-01 |
| `to_date` | daterangepicker | Same | 🔴 Same |
| `month` | select dropdown | Direct SQL concat `month(p.date_payment)='$month'` | ⚠️ No type validation |
| `year` | text, `onkeypress` digit-only | Direct SQL `year(p.date_payment)='$year'` | ⚠️ Frontend only |
| `id_branch` | JS dropdown | None | ⚠️ Frontend only |
| `id_scheme` | JS dropdown | None | ⚠️ Frontend only |
| `payment_status` | Dropdown | None | ❌ No backend check |
| `status` (enquiry) | None | None | 🔴 SQL injected directly |
| `type` (enquiry) | None | None | 🔴 SQL injected directly |
| `id_payment[]` (cancel) | Checkbox | `sizeof()` check | ⚠️ No ownership/auth check |
| `mobile` (autocomplete) | min 4 chars | None | ⚠️ Frontend only |

---

## §11 — KNOWN BUGS REGISTER

| Bug ID | File | Line | Severity | Description |
|---|---|---|---|---|
| RPT-B01 | `admin_reports.php` | 264-271 | 🔴 HIGH | GST-adjusted `$pay` overwritten by discount check |
| RPT-B02 | `admin_reports.php` | 1163 | 🔴 HIGH | `$today['collection']` used before `$today` assigned |
| RPT-B03 | `admin_report_model.php` | 22-31 | 🔴 CRIT | SQL injection via `$status`, `$type` concat |
| RPT-B04 | `admin_report_model.php` | 41 | 🔴 CRIT | SQL injection via `$id` concat |
| RPT-B05 | `admin_reports.php` | 612 | 🔴 CRIT | `last_query()` exposed in JSON response |
| RPT-B06 | `admin_reports.php` | 751-754 | 🔴 HIGH | Raw `$_POST` in `intertable_list()` |
| RPT-B07 | `admin_reports.php` | 1219-1220 | 🔴 HIGH | `$model` double-assign: account_model immediately overwritten by payment_model |
| RPT-B08 | `reports.js` | 20 | 🟡 MED | `var path` redeclared — overwrites line-2 value |
| RPT-B09 | `payment_model.php` | 7080 | 🔴 HIGH | `$limit` undefined in `get_Scheme_Payment_ModeWiseummaryDetails()` |
| RPT-B10 | `payment_model.php` | 321-322 | 🔴 HIGH | `$_POST['id_status']`, `$_POST['id_customer']` raw in `payment_list_range()` |
| RPT-B11 | `payment_model.php` | ~8392 | 🟡 MED | `monthly_report_data()` reads filters from POST inside model (not params) |
| RPT-B12 | `payment_model.php` | ~8436 | 🟡 MED | `maturity_report_data()` N+1 query (1 per scheme) |
| RPT-B13 | `admin_reports.php` | 1718 | 🟡 MED | `file_put_contents()` relative path → log in web root |
| RPT-B14 | `account_model.php` | 26-28 | 🟡 MED | `mkdir(0777)` — overly permissive directory creation |
| RPT-B15 | `payment_model.php` | 26-28 | 🟡 MED | `mkdir(0777)` same issue |

---

## §12 — BUSINESS RULES ENGINE

| Rule | Condition | Action | Enforcement |
|---|---|---|---|
| BR-01 | `payment_status=0` | Pending | WHERE clause |
| BR-02 | `payment_status=1` | Success — included in collections | Model |
| BR-03 | `payment_status=4` | Cancelled — excluded from reports | WHERE clause |
| BR-04 | `gst_type=0 && gst_setting=1` | GST deducted from display amount | Controller |
| BR-05 | `discountAmt != 0.00` | Discount deducted from amount | Controller (conflicts with BR-04) |
| BR-06 | `branchWiseLogin=1 && id_branch=0` | Admin sees all | Model condition |
| BR-07 | `branchWiseLogin=1 && id_branch>0` | User sees own + `show_to_all` | Model |
| BR-08 | `report_type=0` | Group payments under 'Payment' key | `sheme_payment_list_daterange()` |
| BR-09 | `report_type=1` | Group by scheme_name | Same method |
| BR-10 | `report_type=2` | Group by village_name | Same method |
| BR-11 | `installment_cycle=1` | Maturity by days | `maturity_report_data()` |
| BR-12 | `installment_cycle!=1` | Maturity by months | Same |
| BR-13 | `metal_date_type=0` | Filter `is_editing_enabled=0` (original dates) | Source-wise SQL |
| BR-14 | `metal_date_type=1` | Filter `is_editing_enabled=1` (edited rate dates) | Source-wise SQL |
| BR-15 | `pmd.is_active=1` | Only active payment mode splits counted | Source-wise WHERE |
| BR-16 | `integrationType=2` | Sync cancellation to integration API | cancel_payment() |
| BR-17 | `is_branchwise_cus_reg=1` | Block cross-branch customer reassign | checkCommonSettings() |
| BR-18 | `branchwise_scheme=1` | Customer branch must match account branch | checkCommonSettings() |

---

## §13 — CALCULATION ENGINE

| Calculation | Formula | Location | Risk |
|---|---|---|---|
| GST Deduction | `pay = payment_amount - (sgst + cgst)` | admin_reports.php L265, L345, L383 | 🔴 Overwritten by BR-05 |
| Discount Deduction | `pay = payment_amount - discountAmt` | admin_reports.php L267 | 🔴 Overwrites GST-adjusted value |
| GST format | `sprintf("%.3f", sgst + cgst)` | L261-263 | String addition — PHP type coercion |
| Due Count | `due_count = total_installments - paid_installments` | L463 | ❌ No null check |
| Metal Weight (weight scheme) | `(payment_amount * (100/(100+gst))) / metal_rate` | payment_model.php L7028 | Division by zero if metal_rate=0 |
| Metal Weight (amount scheme, incl GST) | `metal_weight2` (from DB) | payment_model.php L7033 | — |
| Metal Weight (amount scheme, excl GST) | `(payment_amount * (100/(100+gst))) / metal_rate` | payment_model.php L7036 | Division by zero |
| Admin App Total | `round(array_sum($admin_app), 2)` | admin_reports.php L1334 | 🔴 Crash if null array |
| Offline Total | `round(array_sum($offline), 2)` | admin_reports.php L1338 | 🔴 Same |
| Online Total | `round(array_sum($online), 2)` | admin_reports.php L1342 | 🔴 Same |
| Mode SGST | `(payment_amount - (payment_amount*(100/(100+gst))))/2` | payment_model.php L7062 | gst_type=0 formula |
| Mode CGST | Same formula | Same | — |

---

## §14 — PAYMENT STATUS WORKFLOW

| Value | Label | CSS Color | Allowed Actions |
|---|---|---|---|
| 0 | Pending | — | View only |
| 1 | Success | green | Cancel (→4), Reprint, Edit |
| 2 | Rejected | red | View only |
| -1 | Failure | red | View only |
| 3 | Pending (gateway) | — | Gateway check |
| 4 | Cancelled | grey | View in cancel report |
| 7 | Refunded | — | Gateway refund |

---

## §15 — CLIENT-VARIANT SETTINGS

| Setting | Source | Values | Effect |
|---|---|---|---|
| `branch_settings` | session | 0/1 | Controls all branch filter visibility |
| `branchWiseLogin` | session | 0/1 | Restricts data to own branch |
| `gst_setting` | joined from scheme | 0/1 | GST deduction in amount display |
| `gst_type` | scheme table | 0=inclusive, 1=exclusive | GST formula selection |
| `integrationType` | CI config | 1/2 | Khimji sync in cancel flow |
| `edit_custom_entry_date` | chit_settings | date string | Modewise summary date cutoff |
| `scheme_wise_receipt` | chit_settings | 1-7 | Receipt number generation pattern |
| `scheme_wise_acc_no` | chit_settings | 0-6 | Account number generation pattern |
| `date_type` | POST param | 1=payment_date, 2=custom_entry_date | Which date column to filter on |
| `report_type` | POST param | 0=common, 1=scheme, 2=area | Source-wise grouping mode |
| `metal_date_type` | POST param | 0=original, 1=edited | Filter on is_editing_enabled |
| `acc_type` | POST param | 0=all, 1=active, 2=closed | Account status filter |
| `installment_cycle` | scheme table | 1=days, other=months | Maturity date calculation |
| `msg91_authkey` | chit_settings | string | MSG91 API key |
| `currency_symbol` | chit_settings | string | Display currency |
| `company_settings` | session | 0/1 | Multi-company filter active |

---

## §16 — DEBUG RUNBOOKS

### Runbook 1: "Report shows wrong payment amount"
1. Check `gst_setting` and `gst_type` for affected payments
2. Check `discountAmt` (firstPayDisc_value from scheme join)
3. If both GST and discount apply → RPT-B01 at L264-271
4. **Fix**: Change condition to: `$pay = ($gst_type==0 && $gst_setting==1) ? ($payment_amount - $total_gst) - $discountAmt : $payment_amount - $discountAmt`

### Runbook 2: "Source-wise report crashes / empty data"
1. Check if date range returns zero payments
2. If zero: `array_sum()` on uninitialized `$admin_app/$offline/$online` → PHP warning
3. **Fix**: Initialize arrays before foreach: `$admin_app = []; $offline = []; $online = [];`
4. Also check: `$limit` undefined in `get_Scheme_Payment_ModeWiseummaryDetails()` (RPT-B09)

### Runbook 3: "Monthly report shows data for wrong branch"
1. Check: model reads `id_branch` from POST AND session `id_branch`
2. Session branch takes priority (L8394-8398)
3. If session branch is set, POST branch is ignored
4. For admin (session id_branch=0/empty): POST branch is used

### Runbook 4: "Maturity report is very slow"
1. Count number of active schemes in `scheme` table
2. `maturity_report_data()` runs 1 SQL query per scheme → N+1 problem
3. For 50 schemes: 51 DB round-trips
4. **Workaround**: Limit scheme filter before loading report

### Runbook 5: "Closed account report shows wrong data"
1. Check `closedaccount_list()` at L1217-1220
2. `$model = $this->account_model` → immediately overwritten by `$model = $this->payment_model`
3. The `get_all_closed_account_by_date()` is called on `payment_model` — does method exist there?
4. **Check**: `account_model->get_all_closed_account_by_date()` may be the correct target

### Runbook 6: "Enquiry filter SQL error"
1. `status` and `type` POST values go directly into SQL string (RPT-B03)
2. Any non-integer value (including `'`) breaks the query
3. **Fix**: Replace concat with CI `$this->db->where()` method

### Runbook 7: "Source-wise report missing payments"
1. Source-wise uses `payment_mode_details` (pmd) as base — not `payment`
2. Payments with no `pmd` record (old data before pmd table) won't appear
3. Check: `pmd.is_active=1 AND pmd.payment_status=1` — inactive splits excluded
4. Multi-mode payments appear as MULTIPLE rows (one per mode split)

---

## §17 — UNIT TEST DERIVATION MAP

| Test ID | Scenario | Input | Expected |
|---|---|---|---|
| RPT-T01 | GST+Discount both active | `payment_amount=1000, sgst=9, cgst=9, discountAmt=50, gst_type=0, gst_setting=1` | `pay = 941` (GST deducted THEN discount) not `950` |
| RPT-T02 | Empty date range | `from_date=""` | Validation error, not 1970-01-01 data |
| RPT-T03 | SQL injection attempt | `status="1 OR 1=1"` in enquiry filter | Should escape/reject |
| RPT-T04 | Cancel non-existent payment | `id_payment[]=9999999` | Graceful fail, not echo TRUE |
| RPT-T05 | Source-wise no data | Date range with 0 payments | No PHP warning, returns empty JSON |
| RPT-T06 | Monthly report branch isolation | Session `id_branch=5`, POST `id_branch=0` | Returns branch 5 data only |
| RPT-T07 | Maturity daily cycle | `installment_cycle=1, total_ins=30, from_date=2026-03-13` | mat_from = 2026-02-11 |
| RPT-T08 | Metal weight calc | `scheme_type=1, payment_amount=1000, gst=3, metal_rate=5800` | `weight = (1000*(100/103))/5800` |
| RPT-T09 | Due count negative | `total_ins=10, paid_ins=12` | Should display 0, not -2 |
| RPT-T10 | Multi-mode payment in monthly | Cash=500, UPI=500 in one payment | Shows cash=500, upi=500 (2 pmd rows) |

---

## §18 — CHANGE IMPACT MAP

| If You Change | Affects | Risk |
|---|---|---|
| `payment.payment_status` values | ALL report WHERE clauses | 🔴 HIGH |
| `payment_mode_details` table structure | Source-wise, monthly, general advance reports | 🔴 HIGH |
| `scheme.gst` / `scheme.gst_type` | Amount display in 5+ reports | 🔴 HIGH |
| `scheme.installment_cycle` | Maturity report date calculation | 🔴 HIGH |
| Branch session structure | ALL branch-filtered reports | 🔴 HIGH |
| `chit_settings.edit_custom_entry_date` | Source-wise modewise cutoff | 🟡 MED |
| `payment_model->getMemberReport()` signature | member_report, JS call | 🟡 MED |
| `account_model->get_all_scheme_account_by_range()` | Outstanding, scheme customer reports | 🟡 MED |
| `integrationType` config | cancel_payment() sync behavior | 🟡 MED |
| `format_accRcptNo()` in customer_model | Account/receipt display in ALL reports | 🔴 HIGH |

---

## §19 — CROSS-MODULE DEPENDENCIES

| Dependency | Direction | Risk if Changed |
|---|---|---|
| `payment_model` | Reports → Model | 🔴 30+ methods used |
| `account_model` | Reports → Model | 🔴 8+ methods used |
| `customer_model->format_accRcptNo()` | Called inside payment_model loops | 🔴 Affects all account/receipt display |
| `admin_settings_model->get_access()` | Permission checks in views | 🟡 Show/hide action buttons |
| `log_model` | Log detail/range | 🟡 LOW |
| `syncapi_model` | cancel_payment() | 🟡 CONDITIONAL |
| `integration_model` | generateTranUniqueIdManually() | 🟡 CONDITIONAL |
| `sms_model` | OTP for purchase delivery | 🟡 LOW |

### Tables Written By This Module
| Table | Action | Method |
|---|---|---|
| `payment` | UPDATE `payment_status=4` | `cancel_payment()` |
| `payment_status_message` | INSERT log | `cancel_payment()` |
| `cust_enquiry` | UPDATE status | `update_enqStatus()` |
| `scheme_account` | UPDATE `id_customer` | `updateAccountDetails()` |
| `kyc_data` | UPDATE status | `update_kyc()` |
| `agent_kyc` | UPDATE status | `updateAgentkyc()` |
| `purch_payment` | UPDATE `is_delivered=1` | `purch_delivered()` |

---

## §20 — GAPS & TECHNICAL DEBT

### 🔴 CRITICAL — Security
1. **SQL Injection** — `get_customerenquiry_by_date()`: `$status`, `$type` → `admin_report_model.php` L22-31
2. **SQL Injection** — `get_custEnqStatus()`: `$id` → `admin_report_model.php` L41
3. **Debug leak** — `last_query()` in JSON → `admin_reports.php` L612
4. **Raw `$_POST`** — `payment_list_range()`: `$_POST['id_status']`, `$_POST['id_customer']` → `payment_model.php` L321-322
5. **Raw `$_POST`** — `intertable_list/update_cusdatas/update_transdatas` → `admin_reports.php`

### 🔴 HIGH — Logic Bugs
6. **GST+Discount** conflict — `$pay` overwritten (RPT-B01)
7. **Undefined `$today`** — used before assignment (RPT-B02)
8. **`$model` double-assign** — `closedaccount_list()` (RPT-B07)
9. **`$limit` undefined** — `get_Scheme_Payment_ModeWiseummaryDetails()` (RPT-B09)

### 🟡 MEDIUM — Performance
10. N+1 queries — `maturity_report_data()` (1 query per scheme)
11. Correlated subqueries in `get_gift_list()` — O(n²)
12. `get_all_account()` — no filters, loads all non-closed accounts
13. `reports.js` — 16,149 lines loaded for every report page

### 🟡 MEDIUM — Missing Validation
14. `strtotime("")` → 1970 dates (all date-range reports)
15. No auth check in `cancel_payment()` — any logged-in user can cancel
16. `year` field: frontend digit-only; no backend validation

### 🟡 MEDIUM — Code Debt
17. Large commented-out code blocks (L636-648, L885-905, L1414-1497 in controller)
18. `file_put_contents` relative path → logs to web root
19. `mkdir(0777)` in both payment_model and account_model constructors
20. 41+ AJAX calls in `reports.js` with no `.error()` / `.fail()` handler

---

## §21 — OPERATIONAL SUPPORT GUIDE

| Symptom | Check | Section |
|---|---|---|
| Report shows no data | POST params: check date values in DevTools Network | §16 Runbook 2 |
| Wrong payment amounts | GST+Discount combo | §13, §16 Runbook 1 |
| Source-wise PHP warning | Empty date range → null arrays | §16 Runbook 2 |
| Monthly wrong branch | Session id_branch vs POST id_branch priority | §16 Runbook 3 |
| Maturity report slow | N+1 queries per scheme | §16 Runbook 4 |
| Closed account wrong data | model double-assign bug | §16 Runbook 5, RPT-B07 |
| Enquiry SQL error | Raw status/type in SQL | §16 Runbook 6, RPT-B03 |
| Source-wise missing payments | pmd.is_active filter | §16 Runbook 7 |
| Multi-mode payment double-counted | pmd row per split | §6, §16 Runbook 7 |

---

*Brain v2.0 — 2026-03-13 | Sections: 21 | Bugs found: 15 | Models fully scanned: payment_model (8904L), account_model (3651L)*
*Next: `/module-bug-audit chit_reports`*
