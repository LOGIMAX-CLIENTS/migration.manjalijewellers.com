# INVARIANT MATRIX — Stock Issue
> Updated Round 8 | 2026-03-19

---

## Variant Dimensions

This module has **3 behavioral dimensions** that control which code path executes:

| Dimension | Field | DB Column | PHP Variable | JS Variable | Values |
|---|---|---|---|---|---|
| Stock Type | `#stock_type` | `ret_stock_issue.stock_type` | `$stock_type` | `stock_type` | 1=Tagged, 2=Non-Tagged |
| Operation Type | `order[issue_receipt_type]` radio | — | `$addData['issue_receipt_type']` | `issue_receipt_type` | 1=Issue, 2=Receipt |
| Issue Type | `#issue_type` select | `ret_stock_issue.issue_type` | `$addData['issue_type']` | `issue_type` | FK → `ret_stock_issue_types` |
| Remove-from-Stock | (derived from issue_type) | `ret_stock_issue_types.is_remove_from_stock` | `$issue_type_det['is_remove_from_stock']` | — | 0=No log, 1=Write log |
| OTP Required | `#otp_required` | `profile.stock_issue_otp_req` | — | `otp_required` | 0=No OTP, 1=OTP |

---

## Behavior Grid: Stock Type × Operation Type

|  | **Issue (1)** | **Receipt (2)** |
|---|---|---|
| **Tagged (1)** | INSERT `ret_stock_issue` + `ret_stock_issue_detail`, UPDATE `ret_taging.tag_status=7`, optional status logs | UPDATE `ret_taging.tag_status=0`, UPDATE `ret_stock_issue_detail.status=3`, optional status logs |
| **Non-Tagged (2)** | INSERT `ret_stock_issue` + `ret_stock_issue_detail`, `updateNTData(-, deduct)`, INSERT nontag logs | `updateNTData(+, add back)`, UPDATE `ret_stock_issue_detail.status=3`, INSERT nontag logs |

---

## Behavior Grid: is_remove_from_stock × stock_type

| | **is_remove=0** | **is_remove=1** |
|---|---|---|
| **Tagged** | Insert issue detail, update tag status only | + Insert `ret_taging_status_log` (+ `ret_section_tag_status_log` if section exists) |
| **Non-Tagged** | Insert issue detail, deduct/add stock only | + Insert `ret_nontag_item_log` (+ `ret_section_nontag_item_log` if section exists) |

---

## Named Issue Type Behaviors (from `ret_stock_issue_types`)

> Actual values depend on DB. These are inferred from code comments and field names.

| Type ID (inferred) | Name | is_remove_from_stock | Key Behavior |
|---|---|---|---|
| 1 (est.) | Karigar Repair | 1 | Stock out, logs written, tag_status=7 |
| 2 (est.) | Photography | 0 | Tag tracked, no stock removal log |
| 3 (est.) | Customer Issue | 1 | Stock out, customer record linked |
| 4+ | Others | varies | Check `ret_stock_issue_types` table |

> ⚠️ Verify actual types in DB: `SELECT * FROM ret_stock_issue_types;`

---

## OTP Flow Variant

| `otp_required` | Effect |
|---|---|
| 0 | Submit proceeds directly on button click |
| 1 | OTP modal shown → SMS sent → user enters OTP → verify call → `is_otp_verfied=1` → submit fires |

---

## Issued-To Dimension (form UI control only)

| `issued_to` | Visible Section | Required Field |
|---|---|---|
| 1 (Customer) | `.customer` div | `cus_id` (hidden) must be populated |
| 2 (Employee) | `.employee` div | `#issue_employee` must be selected |
| 3 (Karigar) | `.karigar` div | `#karigar` must be selected |

---

## OTP Modal JS Safety Variant (R-22)

> Added Round 8. The OTP modal appends server `msg` directly without escaping.

| Path | Code | Risk |
|---|---|---|
| OTP success | `$('.otp_alert').append('<p...>' + data.msg + '</p>')` L1751 | 🔴 XSS if server returns HTML in msg |
| OTP failure | `$('.otp_alert').append('<p...>' + data.msg + '</p>')` L1801 | 🔴 XSS if server returns HTML in msg |

**Fix pattern**: Replace `.append('<p>' + data.msg + '</p>')` with `$('<p>').text(data.msg)`

---

## Async Behavior Variant (R-23)

| Call | async | Effect |
|---|---|---|
| `stock_at_send_otp()` | `false` (L1527) | Blocks browser UI until OTP SMS sent |
| `stock_order_otp()` | `false` (L1719) | Blocks browser UI until verify completes |

**All other AJAX calls**: `async: true` (default) — no blocking.
