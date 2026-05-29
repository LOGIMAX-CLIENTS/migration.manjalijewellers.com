# Hardcoded Values — Magic Numbers & Constants
> Last updated: 2026-03-16
> Source: 9 module brains

---

## Hardcoded Dates

| ID | Module | File | Line | Value | Risk |
|---|---|---|---|---|---|
| HC-001 | account | `admin_manage.php` | L1433 | `'2024-10-07'` hardcoded in GA bonus query | 🔴 HIGH — GA bonus cutoff silently wrong after this date |
| HC-002 | Estimation | `ret_estimation_model.php` | Various | `%m%d` BETWEEN pattern fails year-crossing ranges (Dec→Jan) | 🟡 MED — birthday/anniversary reports wrong for Dec–Jan ranges |

---

## Hardcoded Payment Status Codes

| ID | Module | Values | Risk |
|---|---|---|---|
| HC-010 | payment | 1=Success, 2=Awaiting, 3=Failure, 4=Cancelled, 6=Refund, 7=Pending — used as raw integers throughout | 🟡 MED — no constants, typos cause silent wrong states |
| HC-011 | chit_reports | `cancel_payment` hardcodes `status=4` | 🟢 LOW — matches payment module's convention |
| HC-012 | Tagging | `tag_status = 1` (Sold), `tag_status = 4` (In Transit), `tag_status = 17` (Metal Issue) — all hardcoded | 🟡 MED — distributed across 3 modules, no central enum |

---

## Hardcoded Scheme/Config IDs

| ID | Module | File | Value | Risk |
|---|---|---|---|---|
| HC-020 | scheme | `admin_scheme.php` | L367-368 `emp_incentive_closing` key set twice | 🟡 MED — second value silently overwrites first |
| HC-021 | scheme | `admin_scheme.php` | L438+442 `interest_mode` key set twice | 🟡 MED |
| HC-022 | scheme | `admin_scheme.php` | L524 `'id_scheme' => $id` but `$id=NULL` in Add mode | 🔴 HIGH — scheme incentive inserted with null id |

---

## Hardcoded SMS Gateway Routing

| ID | Module | File | Pattern | Count | Risk |
|---|---|---|---|---|---|
| HC-030 | payment | `admin_payment.php` | if/elseif chain over `$sms_settings['sms_gateway']` for 5 gateways | ~8 duplications | 🟡 MED — new gateway requires 8 edits |
| HC-031 | account | `admin_manage.php` | Same pattern | ~6 duplications | 🟡 MED |
| HC-032 | chit_reports | `admin_reports.php` | Same pattern for purchase OTP | 1-2 | 🟡 LOW |

---

## Hardcoded API URLs / External Services

| ID | Module | File | Value | Risk |
|---|---|---|---|---|
| HC-040 | chit_reports | `admin_reports.php` | `control.msg91.com/api/balance.php` and `credit_history.php` — hardcoded Msg91 URLs, SSL verification disabled | 🔴 HIGH — SSL bypass exposes API |
| HC-041 | chit_settings | `admin_settings_model.php` | `update_rate_file()` writes to `../api/rate.txt` | 🟡 MED — path traversal risk if base changes |
| HC-042 | chit_settings | `admin_settings.php` | OneSignal API URL hardcoded | 🟢 LOW |
| HC-043 | account | `admin_manage.php` | ERP base URL from `config('erp_baseURL')` — not hardcoded | 🟢 OK |
| HC-044 | chit_reports | `admin_reports.php` | Khimji ERP URL from `integration_model` config | 🟢 OK |

---

## Hardcoded File Paths

| ID | Module | File | Value | Risk |
|---|---|---|---|---|
| HC-050 | chit_reports | `admin_reports.php` | L1718: `"log/payment{$date}.txt"` (relative path → `admin/log/`) | 🔴 HIGH — web exposed directory |
| HC-051 | payment | `admin_payment.php` | `$this->log_dir = "log/{$date}/"` — same parent directory | 🔴 HIGH — web exposed |
| HC-052 | chit_settings | `admin_settings.php` | `../data/backup/*.zip` path for DB backups | 🟡 MED — backup files potentially accessible |
| HC-053 | Estimation | `admin_ret_estimation.php` | `APPPATH . 'libraries/dompdf/autoload.inc.php'` | 🟢 LOW — standard library path |

---

## Hardcoded Tax / Rate Values

| ID | Module | File | Value | Risk |
|---|---|---|---|---|
| HC-060 | payment | Various | GST rates calculated from `chit_settings.gst_setting` — NOT hardcoded | 🟢 OK |
| HC-061 | Billing | ret_billing_model | Metal rates from `branch_metal_rate` — NOT hardcoded | 🟢 OK |
| HC-062 | chit_reports | `admin_reports.php` L327-365 | GST recalculated inline in controller (not delegated to model) | 🟡 MED — duplicated logic, may diverge from payment module's calc |
