# Cross-Module Bug History
> Last updated: 2026-03-16
> Initial build — populated from 9 module brains

---

## Active Cross-Module Bugs

| Bug ID | Modules Affected | Description | Status | Source Brain |
|---|---|---|---|---|
| XMOD-001 | **chit_reports + Payment** | `cancel_payment` in `admin_reports` writes `payment_status=4` WITHOUT transaction wrapping. If `payment_statusDB` insert fails after `payment_cancel`, payment is cancelled but audit log is missing. Payment module's own cancel flow has transaction wrapping — reports does not. | 🔴 OPEN | chit_reports MODULE_BRAIN risk #5 |
| XMOD-002 | **chit_reports + Account** | `updateAccountDetails` in `admin_reports` modifies `scheme_account` directly, bypassing all of Account module's validation logic (branch-wise rules, OTP requirement, sync triggers). No sync API called. | 🔴 OPEN | chit_reports MODULE_BRAIN + account CROSS_MODULE_MAP |
| XMOD-003 | **Account + Payment** | OTP returned in JSON response in both modules: `acc_close_otp()` (account L1213), `generateotp()` (payment L5067), `generate_giftotp()` (account L3238), `rateFixing_otp()` (account L3359), `sendotp_scheme_join()` (account L4042). Same security anti-pattern duplicated across both modules independently. | 🔴 OPEN | account + payment MODULE_BRAIN |
| XMOD-004 | **Account + Payment** | OTP comparison uses `=` (assignment) instead of `==` (comparison): `verifyotp_gift()` account L4188, `generateotp()` payment L5081. Always evaluates true → OTP bypass. Same bug in 2 modules. | 🔴 OPEN | account + payment MODULE_BRAIN |
| XMOD-005 | **chit_reports + admin_log** | `admin/log/` directory contains 31+ PII-laden flat files (customer mobile, names, payment amounts, nominee data), with NO `.htaccess` protection at any level. Files written by both `admin_payment` (`create_payment_*.txt`) and `admin_reports` (`payment*.txt`, `account*.txt`). Directly web-accessible. | 🔴 OPEN P0 | chit_reports COVERAGE_TRACKER Round 4 |
| XMOD-006 | **chit_reports + account_model** | `account_model::scheme_group_summary_data($id)`, `is_luckly_draw_scheme($id)`, `get_group_scheme_code($id)` — all 3 methods use raw `$id` string concatenation in SQL. These methods are called from `admin_reports` controller which receives `$id` from `$this->input->post()`. SQL injection path from reports UI to account model. | 🔴 OPEN | chit_reports MODULE_BRAIN + account CROSS_MODULE_MAP |
| XMOD-007 | **chit_reports + admin_report_model** | `get_customerenquiry_by_date()` in `admin_report_model` (L26-30) concatenates `$status` and `$type` directly into SQL without casting. These params come from `$_POST` in `ajax_enquiry_list`. Critical SQL injection. | 🔴 OPEN CRITICAL | chit_reports COVERAGE_TRACKER Round 2 |
| XMOD-008 | **Tagging + Billing** | No centralized `tag_status` state machine. Billing writes `tag_status = 1` (Sold) directly. Tagging writes multiple other statuses. If billing save fails mid-transaction after `tag_status` update, tag is marked sold but no bill exists. | 🔴 OPEN | Tagging + Billing CROSS_MODULE_MAP |
| XMOD-009 | **Estimation + Billing** | Estimation→Billing linkage partial commit: billing save updates `ret_estimation.estbillid` and `ret_estimation_items.purchase_status`. If billing controller crashes after updating estimation but before committing the bill, estimation is partially linked to a non-existent bill. | 🔴 OPEN | Billing CROSS_MODULE_MAP |
| XMOD-010 | **Account + Payment** | `wallet_account` and `wallet_transaction` written by BOTH Account and Payment modules without distributed lock or transaction coordination. A simultaneous wallet debit (Payment) and wallet credit (Account) can result in incorrect balance. | 🟡 POTENTIAL | account + payment CROSS_MODULE_MAP |
| XMOD-011 | **Scheme + Account/Payment** | `DELETE via GET` on `scheme/delete/:id` (CSRF risk). Scheme delete after accounts/payments exist against it creates orphan records in `scheme_account` and `payment`. No cascade or soft-delete defined. | 🟡 POTENTIAL | Scheme MODULE_BRAIN |
| XMOD-012 | **chit_reports + Payment** | SMS gateway routing (if/elseif chain over 5 gateways) duplicated 8+ times across Payment and Account controllers. chit_reports also has SMS dispatch for purchase OTP using same pattern. A new gateway added in one file won't automatically apply to others. | 🟡 POTENTIAL | payment + account + chit_reports MODULE_BRAIN |

---

## Resolved Cross-Module Bugs

| Bug ID | Modules Affected | Root Cause | Fix Applied | Date |
|---|---|---|---|---|
| PAY-CLT-V01 | Payment + Mobile API | Undefined `$paymentgateway` variable in `cashfreemobile()` | Variable initialized before use | 2026-03-10 |
| PAY-CLT-V02 | Payment + Mobile API | Typo `$secretkey` instead of `$secretKey` | Corrected variable name | 2026-03-10 |
| RPT-BRANCH-01 | chit_reports + Payment | `branch` field displayed as "null" in source-wise report | `branch` enforced as compulsory in `SaveAll` and `mobile_payment_post` | 2026-03-12 |

---

## Cross-Module Anti-Patterns (System-Wide)

| Pattern | Frequency | Modules | Prevention |
|---|---|---|---|
| OTP returned in JSON response | 5 endpoints across Payment + Account | Payment, Account | Never include OTP in response — compare server-side only |
| OTP comparison `=` instead of `==` | 2 endpoints across Payment + Account | Payment, Account | Code review mandatory before merging OTP-related PRs |
| Raw `$id` SQL concatenation | 10+ locations across Account, Scheme, chit_reports | Account, Scheme, chit_reports | Use `$this->db->escape()` or `(int)$id` casting |
| Direct `$_POST` access without CI input class | 15+ in chit_reports, 8+ in Account, 8+ in Scheme | chit_reports, Account, Scheme | Use `$this->input->post()` consistently |
| DELETE operation via HTTP GET (CSRF) | `payment/delete/{id}`, `scheme/delete/{id}` | Payment, Scheme | Convert to POST with CSRF token |
| SMS gateway routing duplicated | 8+ copies across 3 controllers | Payment, Account, chit_reports | Extract to a single `send_sms($gateway, $data)` helper |
| No transaction wrapping around multi-table writes | cancel_payment, multiple account/payment flows | chit_reports, Account, Payment, Scheme | Always wrap multi-table mutations in `trans_start/trans_complete` |
