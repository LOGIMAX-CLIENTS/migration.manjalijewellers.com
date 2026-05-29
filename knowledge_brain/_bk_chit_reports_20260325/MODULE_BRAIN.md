# MODULE BRAIN — chit_reports
> Built: 2026-03-14 | Last Round: 5 (2026-03-16) | Status: ✅ Complete (100%)

---

## 1. Module Overview

**Purpose**: Read-only reporting module that surfaces payment collection, scheme accounts, KYC, referral, maturity, gift, and cancellation data across all branches. It is the **primary analytics surface** for chit fund operations.

**File Map**:
| File | Lines | Purpose |
|---|---|---|
| `admin/application/controllers/admin_reports.php` | 2137 | Main controller — all report routes |
| `admin/application/models/payment_model.php` | ~6500 (278 methods) | Primary data model — payments, accounts, schemes |
| `admin/application/models/account_model.php` | 3651 (170 methods) | Account/scheme account data |
| `admin/assets/js/reports.js` | 16149 | Single JS file for all report pages |
| `admin/application/views/reports/` | 69 files | One view per report type |

**Connection Diagram**:
```
Browser → reports.js ($.ajax)
        → POST → admin_reports (controller, L1-2137)
        → payment_model / account_model
        → DB (payment, scheme_account, customer, scheme, branch, ...)
        → JSON response → DataTable rendering in views
```

---

## 2. Constructor

**File**: `admin_reports.php` L15-35

| Model/Library | Constant | Purpose |
|---|---|---|
| `payment_model` | PAY_MODEL | Primary — all payment/account queries |
| `log_model` | LOG_MODEL | Log reports |
| `account_model` | ACC_MODEL | Account/closing reports |
| `dashboard_model` | DAS_MODEL | Scheme-wise account summary |
| `email_model` | MAIL_MODEL | (loaded, unused in reports) |
| `chitadmin_model` | ADM_MODEL | Admin-level data |
| `admin_usersms_model` | SMS_MODEL | SMS for OTP (purchase report) |
| `admin_settings_model` | SET_MODEL | Access control, settings |
| `admin_report_model` | — | Customer enquiry, KYC, gift, celeb dates |
| `sms_model` | — | SMS gateway dispatch |
| `metal_wgt_digit` helper | — | Metal weight/digit formatting |
| `lmx/functions/export_helper` | — | Excel export helper |

**Session Gate**: L31-33 — redirects to `admin/login` if `is_logged` session not set.
**Branch Settings**: L34 — `$this->branch_settings` set from session.

---

## 3. Entry Points (Routes)

> Full table in METHOD_INDEX.md. Key groupings:

| Report Category | Page Load Method | AJAX Data Method | View |
|---|---|---|---|
| Payment Due | `payment_due_list` | *(inline)* | `payment_due` |
| Payment Date Range | `payment_by_daterange` | `payment_list_daterange` | `payment_daterange` |
| Payment Scheme-wise | `payment_schemewise` | `payment_schemewise_detail` | `payment_schemewise` |
| Source-wise (Scheme+Mode) | `scheme_payment_daterange` | `scheme_payment_list_daterange` | `scheme_payment_daterange` |
| Payment Date-wise | `payment_datewise_data` | `payment_datewise_list` | `paymentschem_datewise` |
| Date-wise Scheme Coll | `paydatewise_schemecoll_data` | `paydatewise_schemecoll_list` | `payment_datewise_schcoll` |
| Outstanding | `payment_outstanding` | `payment_outstanding_list` | `payment_outstanding` |
| Cancel Payments | `payment_cancel_list` | `paymentcancel_list` | `payment_cancel_report` |
| Closed Accounts | `closed_account_list` | `closedaccount_list` | `closed_acc_report` |
| Outstanding (Scheme-wise) | `scheme_customer_daterange` | `scheme_customer_list_daterange` | `scheme_customer_daterange` |
| Member Report | `member_report` | `getMemberReport` | `member_report` |
| Maturity Report | `maturity_report_view` | `maturity_report_data` | `maturity_report` |
| Monthly Report | `monthly_report_view` | `monthly_report_data` | `monthly_chit_report` |
| General Advance | `general_advance_view` | `general_advance_list` | `general_adv_payment_list` |
| Gift Report | `get_gift_report` | `ajax_gift_report` | `gift_report` |
| KYC Data | `kycdata_list` | `kycapproval_data` / `update_kyc` | `kyc_table_data/kyc_data` |
| Customer Enquiry | `customer_enquiry` | `ajax_enquiry_list` | `customer_enquiry` |
| Emp Referral | `employee_ref_success` | `employee_ref_success_list` | `employee_ref_success` |
| Cus Referral | `cus_ref_success` | `cus_ref_success_list` | `cus_reff_report` |
| Online Payment | `online_payment_report` | `get_online_payment_report` | `online_payment_report` |
| Inter Table | `inter_table` | `intertable_list` / `intertable_translist` | `inter_table_rep/inter_table` |
| Edit Acc/Pay | `edit_acc_pay` | `editAccOrPayments`, `updatePaymentDetails`, `updateAccountDetails` | `editable_settings/acc_pay_form` |
| Collection Report | `collection_report` | `scheme_daily_collection_details` | `collection_report` |
| Log Report | `log` | *(switch-based AJAX)* | `log/list`, `log/view_list` |
| Celeb Days | `customer_wishes` | `cus_celeb_dates` | `celeb_days` |
| Renewal/Live | `renewal_live_report` | `getRenewalLive_arlData` | `renewal_live_report` |
| Mode+Group-wise | `payment_modeandgroupwise_data` | `payment_modeandgroupwise_list` | `payment_mode_groupwise_list` |
| Autodebit Sub | `get_autodebit_subscription` | `ajax_get_autodebit_subscription` | `autodebit_subscription_report` |
| Purchase History | `get_purchase_payment` | `ajax_get_purchase_payment` | `purchase_history` |

---

## 4. Model Methods Summary

| Model | Method Count | Scope |
|---|---|---|
| `payment_model` | 278 | Full payment lifecycle + reporting |
| `account_model` | 170 | Account open/close/query |

→ See **METHOD_INDEX.md** for alphabetical lookup with tables & callers.

---

## 5. Data Flow Summary

Three primary user flows are detailed in DATA_FLOW.md:
- **Collection View**: Date filter → `payment_list_daterange` → `payment` + `scheme_account` + `customer` + `scheme` → GST calculation → JSON → DataTable
- **Closed Accounts**: Date filter → `closedaccount_list` → `payment_model::get_all_closed_account_by_date()` → `scheme_account` + `payment` + `customer` → JSON
- **KYC Approval**: List + Approve → `kycapproval_data` / `update_kyc` → `payment` (kyc tables) — **only WRITE operation in this controller**

---

## 6. Key Tables

| Table | Owner Module | Key Columns | Used For |
|---|---|---|---|
| `payment` | Payment | `id_payment`, `id_scheme_account`, `payment_amount`, `payment_status`, `payment_mode`, `date_payment`, `payment_type`, `sgst`, `cgst`, `branch` | All payment reports |
| `scheme_account` | Account | `id_scheme_account`, `id_customer`, `id_scheme`, `id_branch`, `active`, `is_closed`, `closing_date`, `group_code`, `scheme_acc_number` | Account reports |
| `customer` | Customer | `id_customer`, `firstname`, `lastname`, `mobile`, `id_branch`, `kyc_status` | Name/contact display |
| `scheme` | Scheme | `id_scheme`, `scheme_name`, `code`, `scheme_type`, `total_installments`, `amount`, `max_weight`, `is_lucky_draw` | Scheme info |
| `branch` | Settings | `id_branch`, `name`, `short_name`, `show_to_all` | Branch filtering |
| `chit_settings` | Settings | 60+ columns — see `COVERAGE_TRACKER.md` Known Gaps section | Config-driven behavior: GST, lucky draw, OTP flows, branch rules, wallet, rate, auto-debit |
| `gift_issued` | Reports | `id_scheme_account`, `type`, `status` | Gift report |
| `general_advance_payment` | Payment | `id_scheme_account`, `payment_amount` | Advance reports |
| `purch_customer`, `purch_payment` | Purchase | — | Akshaya Tritiya purchase module |

---

## 7. Business Rules Summary

→ See **BUSINESS_RULES.md** for full rules list (8 rules extracted).

Key rules:
- **GST Deduction**: If `gst_type=0 AND gst_setting=1`, deduct `sgst+cgst` from `payment_amount` before display
- **Installment Count**: Uses `COUNT(DISTINCT DATE_FORMAT(date_payment,'%Y%m'))` for weight schemes; `SUM(no_of_dues)` for amount schemes
- **Payment Status Codes**: 0=Pending, 1=Success, 2=Rejected, 4=Cancelled, -1=Failure
- **Branch Visibility**: `show_to_all=1` overrides branch-wise login restriction
- **Opening Balance**: `is_opening=1` accounts carry `balance_amount`/`balance_weight` as carry-forward

---

## 8. Cross-Module Dependencies

→ See **CROSS_MODULE_MAP.md** for full dependency map.

Key dependencies:
- **admin_manage** controller: JS calls `passbook_reprint`, `passbook_print`, `set_remarks_byid`
- **integration_model**: `generateTranUniqueIdManually` → Khimji integration API
- **syncapi_model**: `cancel_payment` syncs cancellation to external system
- **sms_model**: OTP delivery for purchase report
- **msg91** external API: SMS balance/history

---

## 9. Known Risks

| Risk | Severity | Location | Details |
|---|---|---|---|
| Raw `$_POST` access | HIGH | `admin_reports.php` L524, L668, L751-754, L763, L772, L791, L1065, L1079 | 15+ uses of `$_POST[key]` bypassing CI input class |
| `$pay` uninitialized | HIGH | `admin_reports.php` L265-271, L344-349, L382-385, L1086-1090 | `$pay` only set when `gst_type=0 && gst_setting=1`, used in ternary without `else` path for `payment_modewise_list` |
| Array index before check | MED | `admin_reports.php` L314-315, L359-360, L400-402, L439-440, L487-489 | `$payment_list[0]['gst_number']` accessed when `count($data)>0` but `$payment_list` may be empty if `$_POST` was empty |
| `$today` not initialized | HIGH | `admin_reports.php` L1163 | `sizeof($today['collection'])` before `$today` is set — collection report crashes on large datasets |
| Duplicate constant override | MED | `admin_reports.php` L1219-1220 | `$model` assigned ACC_MODEL then immediately overwritten by PAY_MODEL in `closedaccount_list` |
| `kycapproval_data` duplicate | MED | `admin_reports.php` L906+L911 | Two methods with same name — PHP uses the last one (L906 is dead code) |
| `payment_summary_modewise` duplicate | MED | `admin_reports.php` L2019 + commented L1460 | Active method vs commented-out old version — dead code confusion |
| No transaction wrapping | LOW | `admin_reports.php` `cancel_payment` L665 | Loops updates without `trans_start/trans_complete` |
| Unguarded `$offline/online/admin_app` vars | MEDIUM | `admin_reports.php` L2035-2046 | `$offline[]` used in `array_sum` without initialization if `foreach` iterates 0 rows |
| CSRF on GET-style endpoints | MED | `exl_rep_outstanding` | Accessible via direct URL with predictable params |
| SSL verification disabled | LOW | `admin_reports.php` L830-831, L857-858 + `admin_report_model.php` L85-86 | `CURLOPT_SSL_VERIFYHOST/VERIFYPEER = 0` in Msg91 CURL calls |
| **SQL Injection** (CRITICAL) | **CRITICAL** | `admin_report_model.php` L26-30 | `$status` and `$type` directly concatenated into SQL in `get_customerenquiry_by_date()` — no casting or escaping |
| Dead `get_gift_list_old` method | LOW | `admin_report_model.php` L114-204 | 90-line dead code block — old gift query never cleaned up |
| Duplicate hidden field `id_type` | MED | `scheme_payment_daterange.php` view | `id_type` and `id_branch` appear twice — second silently overrides first in JS |
| Session value unescaped in HTML | LOW | `scheme_payment_daterange.php` view | `branch_filter`, `login_branch_name` written from session into `value=` without `htmlspecialchars()` |
| **Log file web exposure** | **🔴 CRITICAL** | `admin_reports.php` L1718, L1732 + `admin_payment` controller | **CONFIRMED** (Round 4): NO `.htaccess` at `admin/log/`, `admin/`, project root, or `htdocs/`. 31 PII log files in `admin/log/{date}/manual/` directly web-accessible. Files contain customer mobile, name, payment amount, nominee data, `form_secret` token. Direct URL: `http://{host}/admin/log/2025-04-03/manual/create_payment_2025-04-03.txt`. **Action: Add `Deny from all` `.htaccess` to `admin/log/` IMMEDIATELY.** |
| Scheme summary redundant branch | LOW | `admin_reports.php` L1562-1574 | `is_lucky_draw` if/else does identical assignment — dead logic remnant |
| Celeb date cross-year boundary | MEDIUM | `admin_report_model.php` `get_all_cus_celeb_dates` L576-577 | `%m%d` BETWEEN fails when from_month > to_month (e.g. Dec→Jan range) — no customers found for year-crossing ranges |
| Duplicate route registration | LOW | `routes.php` L1596+L1600 | `reports/inter_table/list` registered twice — second overwrites first silently |
| `$id` raw concat in SQL | HIGH | `account_model.php` `is_luckly_draw_scheme` L2989, `get_group_scheme_code` L2994, `scheme_group_summary_data` L2974 | `$id` parameter directly concatenated into SQL without casting or `$this->db->escape()` |

---

## 10. DB Verification Queries

```sql
-- Pull complete payment record by id_payment
SELECT p.*, sa.scheme_acc_number, c.firstname, c.mobile, s.scheme_name, b.name as branch
FROM payment p
LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
LEFT JOIN customer c ON c.id_customer = sa.id_customer
LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
LEFT JOIN branch b ON b.id_branch = p.id_branch
WHERE p.id_payment = :id;

-- Verify collection total for a date
SELECT COUNT(*) as count, SUM(payment_amount) as total, payment_status
FROM payment
WHERE DATE(date_payment) = :date AND payment_status IN (0,1)
GROUP BY payment_status;

-- Orphan payments (no parent scheme_account)
SELECT p.id_payment FROM payment p
LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
WHERE sa.id_scheme_account IS NULL;

-- Scheme-wise outstanding (active, not closed)
SELECT sa.id_scheme_account, COUNT(p.id_payment) as paid_count,
       SUM(p.payment_amount) as total_paid
FROM scheme_account sa
LEFT JOIN payment p ON p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1
WHERE sa.is_closed = 0 AND sa.active = 1
GROUP BY sa.id_scheme_account;
```

---

## 11. Anti-Patterns Register

> Updated after each bug fix.

| ID | Pattern | Location | Notes |
|---|---|---|---|
| AP-RPT-001 | Raw `$_POST` access | Multiple methods | Use `$this->input->post()` consistently |
| AP-RPT-002 | `$pay` used before guaranteed assignment | `payment_list_daterange`, `payment_modewise_list` | Always initialize `$pay = $payment['payment_amount']` before conditional block |
| AP-RPT-003 | Variable used before loop | `scheme_daily_collection_details` L1163 | `$today` accessed before `$today=` assignment at L1173 |
| AP-RPT-004 | Duplicate method names | `kycapproval_data` x2 | PHP silently uses second definition — dead code risk |

---

## 12. Codebase Notes

- **Framework**: CodeIgniter 3 (CI_Controller / CI_Model)
- **JS**: Single monolithic file `reports.js` (16K lines) — all report pages share one file, gated by `ctrl_page[1]` checks
- **Date handling**: Mix of `str_replace("/","-",...)` normalization and direct `date()` calls — inconsistent timezone handling
- **GST calculation**: Done in PHP controller (not model) — repeated pattern in 5+ methods
- **Export**: PHPExcel library used for XLS export (`exl_rep_outstanding`)
- **PDF**: DomPDF used for referral report (`get_employee_details`)
- **No pagination**: All AJAX calls return full result sets — potential memory issue on large datasets
- **Cross-controller JS calls** (found Round 2): `reports.js` also calls `admin_manage`, `admin_employee`, `admin_customer`, `admin_payment`, `admin_dashboard`, `branch`, `get`, `payment`, `postdated`, `reports` (old route), `khimji_services` — 12 external controllers
- **Dual route system** (clarified Round 3): `index.php/reports/*` URLs are **CI route aliases** in `routes.php` mapping to `admin_reports/*` — there is NO separate `reports` controller. See `ROUTES_MAP.md` for full table (76 aliases)
- **Log files**: `admin/log/{date}/` subdirectory structure confirmed active — date-named subdirectories not files. But `updatePaymentDetails` writes to `admin/log/payment{date}.txt` (flat file, not subdir) — separate from the date-subdirectory system
- **scheme_summary_data perf**: 4 separate DB queries + O(n×4m) PHP merge loop — potential bottleneck at scale
