# VIEW CATALOGUE — chit_reports
> Round 2 — 2026-03-14
> All 69 view files + 5 subdirectories documented.

---

## Root views — `admin/application/views/reports/` (55 files)

| View File | Controller Method | Purpose | Key Hidden Fields |
|---|---|---|---|
| `accounts_schemewise.php` | `accounts_schemewise` | Scheme-wise accounts summary | — |
| `autodebit_subscription_report.php` | `get_autodebit_subscription` | Auto-debit subscriptions | `payment_list1`, `payment_list2` (dates) |
| `celeb_days.php` | `customer_wishes` | Birthday/wedding celebration dates | — |
| `closed_acc_report.php` | `closed_account_list` | Closed accounts by date range | — |
| `collection_report.php` | `collection_report` | Daily scheme-wise collection | — |
| `cus_reff_report.php` | `cus_ref_success` | Customer referral success | — |
| `customer_enquiry.php` | `customer_enquiry` | Customer enquiry list | — |
| `employee_ref_success.php` | `employee_ref_success` | Employee referral success | — |
| `employee_report.php` | `payment_employee_wise` | Employee-wise payment report | `id_employee` |
| `failed_payment.php` | `failed_payments` | Failed payment list | — |
| `general_adv_payment_list.php` | `general_advance_view` | General advance payments | — |
| `gift_report.php` | `get_gift_report` | Gift issuance report | — |
| `gift_yet_to_issue.php` | `get_yet_to_issue` | Gifts pending issuance | — |
| `inter_table.php` | `inter_table` | Customer registration inter-table sync | — |
| `inter_wallet.php` | `interWalletTrans_list` | Inter-wallet transactions | — |
| `kyc_data.php` | `kycdata_list` | KYC verification list page | — |
| `log/list.php` | `log` (type=list) | Log viewer listing | — |
| `log/view_list.php` | `log` (type=view) | Log viewer detail | — |
| `log/form_logger.php` | `form_logger` (type=list) | Form change logger | — |
| `maturity_report.php` | `maturity_report_view` | Account maturity report | — |
| `member_report.php` | `member_report` | Customer member report | — |
| `monthly_chit_report.php` | `monthly_report_view` | Monthly collection summary | — |
| `msg91_delivery_report.php` | `msg91_delivReport` | SMS delivery report | — |
| `msg91_log.php` | `msg91_log` | MSG91 log view | — |
| `old_metal_report.php` | `old_metal_report` | Old metal payment report | `rpt_payments1`, `rpt_payments2` |
| `online_payment_report.php` | `online_payment_report` | Online payment tracking | `from_date`, `to_date` |
| `payment_accountwise.php` | `scheme_account_report` | Single account payment history | `id_scheme_account` (URL param) |
| `payment_cancel_report.php` | `payment_cancel_list` | Cancelled payments | `cancel_payment_list1/2` (dates) |
| `payment_daterange.php` | `payment_by_daterange` | Payment by date range | `id_type`, `id_schemes`, `id_branch`, `id_employee`, `id_pay` |
| `payment_due.php` | `payment_due_list` | Payment dues | — |
| `payment_modewise.php` | `payment_modewise` | Mode-wise payment summary | — |
| `payment_modewise_range.php` | `payment_modewise_data` | Mode-wise by date range | — |
| `payment_mode_groupwise_list.php` | `payment_modeandgroupwise_data` | Mode + group wise report | — |
| `payment_outstanding.php` | `payment_outstanding` | Outstanding payment accounts | — |
| `payment_report.php` | `payment_datewise` | Date-wise payment summary | — |
| `payment_schemewise.php` | `payment_schemewise` | Scheme-wise payment list | — |
| `paymentschem_datewise.php` | `payment_datewise_data` | Date+scheme wise detail | — |
| `payment_datewise_schcoll.php` | `paydatewise_schemecoll_data` | Scheme-wise collection by date | — |
| `payments_on_off_collection.php` | `payments_on_off_collection_data` | Online vs Offline collection | — |
| `purchase_history.php` | `get_purchase_payment` | Purchase payment (Akshaya) | `payment_list1`, `payment_list2` |
| `renewal_live_report.php` | `renewal_live_report` | Renewal + Live accounts | — |
| `scheme_customer_daterange.php` | `scheme_customer_daterange` | Scheme-wise outstanding | — |
| `scheme_daily_collection.php` | *(deprecated/unused?)* | — | — |
| `scheme_payment_daterange.php` | `scheme_payment_daterange` | Source-wise payment by date | `old_sch_acc_num`, `id_type`, `id_branch`, `branch_filter`, `login_branch_name`, `id_classifications`, `id_schemes`, `id_pay_mode` |
| `scheme_schemewise.php` | `accounts_schemewise_detail` (AJAX) | Scheme detail data (AJAX view?) | — |
| `scheme_summary.php` | `scheme_summary` | Scheme summary overview | — |
| `schenquiry_list.php` | `sch_enquirt_list` | Scheme enquiry list | — |
| `employee_wise_collection.php` | `employee_wise_summary` | Employee-wise collection report | — |
| `employee_account.php` | `employee_account` | Employee-opened accounts | — |
| `employee_wise_referaldata.php` | `emp_referral_account` | Employee referral data | — |
| `cus_referral_account.php` | `cus_refferl_account` | Customer referral data | — |
| `cus_ref_acc_detail.php` | `get_referral_code_byId` (indirect) | Referral account detail | — |
| `payment_report_range.php` | `payment_by_range` | Payment range list | — |
| `form_logger.php` | `form_logger` (list) | Form audit logger | — |

---

## Sub-directory Views

### `detailed/` — 14 files

These views are loaded by account-level detail pages (likely from `customer_account_details` or linked from admin_manage):

| File | Purpose |
|---|---|
| `about_to_close.php` | Accounts near maturity |
| `account.php` | Account detail panel |
| `closed_account.php` | Closed account detail |
| `collection_app_list.php` | App-side collection list |
| `customer.php` | Customer profile detail |
| `inter_wallet.php` | Inter-wallet data |
| `inter_wallet_account.php` | Inter-wallet account detail |
| `inter_wallet_accounts.php` | Inter-wallet accounts list |
| `joining_request.php` | Joining request detail |
| `paid_due.php` | Paid dues detail |
| `payment.php` | Payment detail (largest: 9246B) |
| `post_payment.php` | Post-dated payments detail |
| `renewal.php` | Renewal detail |
| `unpaid_due.php` | Unpaid dues detail |

### `editable_settings/` — 1 file

| File | Purpose | Notes |
|---|---|---|
| `acc_pay_form.php` | Edit account/payment admin tool | 12.7KB — complex form with JS inline for loading account/payment by ID and editing fields |

### `kyc_table_data/` — 1 file

| File | Purpose |
|---|---|
| `kyc_data.php` | KYC verification table with approval buttons |

### `sch_enquiry_list/` — 1 file

| File | Purpose |
|---|---|
| `sch_enquiry.php` | Scheme enquiry detail list |

### `inter_table_rep/` — 1 file

| File | Purpose |
|---|---|
| `inter_table.php` | Inter-table reconciliation tool (8.7KB) — shows customer_reg vs customer comparison |

---

## Hidden Fields Summary (Report State)

Hidden fields carry filter state between server page load (PHP session/config) and client-side AJAX calls:

| View | Hidden Fields | What They Carry |
|---|---|---|
| `payment_daterange.php` | `id_type`, `id_schemes`, `id_branch`, `id_employee`, `id_pay` | Filter state for collection AJAX call |
| `scheme_payment_daterange.php` | `old_sch_acc_num` (session), `id_type` (×2!), `id_branch` (×2!), `branch_filter` (session), `login_branch_name` (session), `id_classifications`, `id_schemes`, `id_pay_mode` | Source-wise report filters + session-injected branch |

> ⚠️ **Risk**: `id_type` and `id_branch` appear TWICE in `scheme_payment_daterange.php` — duplicate hidden fields with the same `id` attribute. The second occurrence silently overrides the first in JS. This could cause incorrect filter submission.

> ⚠️ **Risk**: `branch_filter` and `login_branch_name` are written directly from session in PHP into `value` attributes without escaping. If a session value contains quotes, it could break HTML structure. Use `htmlspecialchars($this->session->userdata('...'))`.
