# Validation Gaps — Server-Trusts-Client Map
> Last updated: 2026-03-16
> Source: All 9 module brains + cross-module analysis

> [!CAUTION]
> These are locations where the server trusts client-supplied data without server-side validation. Each is a potential injection, bypass, or data-corruption vector.

---

## Critical SQL Injection (Unescaped Input in SQL)

| ID | Module | File | Line(s) | Input Vector | Vulnerable Code Pattern | Risk |
|---|---|---|---|---|---|---|
| VAL-001 | chit_reports | `admin_report_model.php` | L26-30 | `$_POST['status']`, `$_POST['type']` via `ajax_enquiry_list` | `WHERE status='$status' AND type='$type'` — no casting | 🔴 CRITICAL |
| VAL-002 | account | `account_model.php` | L141, L156, L215, L301+ | URL param `$id` passed to model | `WHERE id_scheme =$id` — no `(int)` cast | 🔴 HIGH |
| VAL-003 | chit_reports | `account_model.php` | L2974, L2989, L2994 | `$id` from `$this->input->post('id_scheme')` | `scheme_group_summary_data($id)` — raw concat | 🔴 HIGH |
| VAL-004 | scheme | `scheme_model.php` | L184, L199, L216 | URL param `$id` | `WHERE s.id_scheme =$id` — no cast | 🔴 HIGH |
| VAL-005 | scheme | `scheme_model.php` | L108, L130 | `$customerId` | Raw concat without escape | 🔴 HIGH |
| VAL-006 | scheme | `admin_scheme.php` | L1213 | `$_POST['id_metal']` | Direct in query | 🔴 HIGH |
| VAL-007 | scheme | `scheme_model.php` | L795 | `$_POST['wgt_min/wgt_max']` | Raw concat | 🔴 HIGH |
| VAL-008 | payment | `payment_model.php` | Throughout | Various `$id` params | Raw string concat | 🔴 HIGH |

---

## Direct `$_POST` Access (Bypassing CI Input Class)

| ID | Module | File | Count | Risk |
|---|---|---|---|---|
| VAL-010 | chit_reports | `admin_reports.php` | 15+ locations | XSS, type confusion — use `$this->input->post()` |
| VAL-011 | account | `admin_manage.php` | ~15 locations | XSS, type confusion |
| VAL-012 | scheme | `admin_scheme.php` | ~8 locations | XSS, type confusion |
| VAL-013 | payment | `admin_payment.php` | Multiple locations | XSS, type confusion |
| VAL-014 | chit_reports | `admin_reports.php` L2079 | `$postData = $_POST` in `cus_celeb_dates` | Entire `$_POST` passed to model without filtering |

---

## OTP Security Gaps

| ID | Module | File | Line | Issue | Risk |
|---|---|---|---|---|---|
| VAL-020 | account | `admin_manage.php` | L4188 | `=` instead of `==` in OTP comparison (`verifyotp_gift`) | 🔴 CRITICAL — always true |
| VAL-021 | payment | `admin_payment.php` | L5081 | `=` instead of `==` in OTP comparison (`generateotp`) | 🔴 CRITICAL — always true |
| VAL-022 | account | `admin_manage.php` | L1213 | OTP returned in JSON response (`acc_close_otp`) | 🔴 HIGH — OTP leakage |
| VAL-023 | payment | `admin_payment.php` | L5067 | OTP returned in JSON response (`generateotp`) | 🔴 HIGH |
| VAL-024 | account | `admin_manage.php` | L3238, L3359, L4042 | OTP in response for gift, rate-fix, scheme-join OTPs | 🔴 HIGH |

---

## Session/Auth Bypass Risks

| ID | Module | Issue | Risk |
|---|---|---|---|
| VAL-030 | chit_reports | `exl_rep_outstanding` accessible via direct URL with predictable params (GET, no CSRF) | 🟡 MED |
| VAL-031 | payment | `payment/delete/{id}` via GET — CSRF vulnerable | 🔴 HIGH |
| VAL-032 | scheme | `scheme/delete/{id}` via GET — CSRF vulnerable | 🟡 MED |

---

## Missing Server-Side Validation (Client-Only Validation)

| ID | Module | Feature | Client-Side | Server-Side | Risk |
|---|---|---|---|---|---|
| VAL-040 | Estimation | Tag reserve check | JS checks `reserve_status` | No server lock before save | Race condition — two users pick same tag |
| VAL-041 | Billing | Tag status check | Via `getTagStatus()` | No row-level lock between check + update | Tag double-sold race condition |
| VAL-042 | Estimation | Gift voucher uniqueness | Client validates | No server lock | Concurrent redemption possible |
| VAL-043 | chit_reports | `updatePaymentDetails` | No validation | Server only checks `!empty($_POST)` | Any POST data passes through |
| VAL-044 | account | Scheme join limits | Client checks `sch_limit` | `limitDB()` called but result not always enforced | User may join beyond limit |

---

## HTML Injection / XSS

| ID | Module | File | Issue | Risk |
|---|---|---|---|---|
| VAL-050 | chit_reports | `scheme_payment_daterange.php` view | Session values `branch_filter`, `login_branch_name` written to `value=` without `htmlspecialchars()` | 🟡 MED — stored XSS if sessions are poisoned |
| VAL-051 | chit_reports | Multiple views | Response data rendered in DataTables without escaping | 🟡 MED — if DB data is poisoned |
