# ADMIN_REPORT_MODEL — chit_reports Brain Supplement
> Scanned: Round 2 — 2026-03-14
> File: `admin/application/models/admin_report_model.php` (591 lines)

---

## Model Overview

This model supports the supplementary report flows for customer enquiry, MSG91 SMS reporting, gift issuance, and customer celebration dates. It is lighter than `payment_model` and `account_model` — only 11 methods.

---

## Method Index — `admin_report_model.php`

| Method | Lines | Tables Read | Tables Written | Caller (Controller) | Risk |
|---|---|---|---|---|---|
| `__construct` | L6-9 | — | — | — | — |
| `get_customerenquiry` | L11-18 | `cust_enquiry`, `cust_enquiry_product` | — | `ajax_enquiry_list` | LOW — no filter, returns ALL enquiries |
| `get_customerenquiry_by_date` | L20-34 | `cust_enquiry`, `cust_enquiry_product` | — | `ajax_enquiry_list` | **HIGH — SQL injection** (L26-30: `$status`, `$type` directly concatenated) |
| `get_custEnqStatus` | L36-43 | `cust_enquiry_status`, `employee` | — | `enquiry/View/{id}` | MEDIUM — `$id` concatenated directly in SQL |
| `update_enqStatus` | L45-62 | — | `cust_enquiry`, `cust_enquiry_status` | `enquiry/UpdateStatus` | LOW — uses `$this->db->where()` (safe) |
| `getmsg91AuthKey` | L65-69 | `chit_settings` | — | `checkBalance` ctrl, `getCreditHistory` ctrl | LOW |
| `checkBalance` | L71-100 | External API (Msg91) | — | `checkBalance` ctrl | LOW (SSL disabled) |
| `getmsg91DelivryStat` | L103-107 | `msg91_delivery_status` | — | `msg91_delivReport` ctrl | LOW |
| `get_gift_list_old` | L114-204 | `gift_issued`, `scheme_account`, `payment`, `customer`, `scheme`, `employee`, `branch`, `gifts`, `metal`, `chit_settings` | — | **DEAD CODE** (old version, superseded) | DEAD CODE |
| `get_gift_list` | L383-488 | `gift_issued`, `scheme_account`, `payment`, `customer`, `scheme`, `employee`, `branch`, `gifts`, `metal`, `chit_settings` | — | `ajax_gift_report` ctrl | MEDIUM — SQL concatenation of `$id_branch`, `$id_metal`, etc. but all are int-checked |
| `gift_summary` | L490-543 | `gift_issued`, `scheme_account`, `scheme`, `gifts`, `branch` | — | `ajax_gift_report` ctrl | MEDIUM — same int-check concatenation |
| `get_chit_settings` | L547-552 | `chit_settings` | — | `payment_summary_modewise` ctrl | LOW |
| `get_all_cus_celeb_dates` | L556-588 | `customer`, `address`, `city`, `scheme_account` | — | `cus_celeb_dates` ctrl | MEDIUM — `$from_date`, `$to_date` from `$_POST` through `strtotime()` without validation |

---

## Critical Bugs Found in `admin_report_model.php`

### BUG: SQL Injection in `get_customerenquiry_by_date` (CRITICAL)

**Location**: L26-30

```php
$sql = $sql . " Where " 
     . ($status != '' ? 'status=' . $status . ' and' : '') 
     . " " 
     . ($type != '' ? 'type=' . $type . ' and' : '') 
     . " (date(date_add) BETWEEN '" . date('Y-m-d', strtotime($from_date)) 
     . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')";
```

**Risk**: `$status` and `$type` are passed directly from `ajax_enquiry_list` controller via `$this->input->post('status')` and `$this->input->post('type')`. No integer cast, no `$this->db->escape()`. A malicious value of `status` could inject arbitrary SQL.

**Fix needed**: 
```php
$status = (int) $status;  // integer cast before use
$type = (int) $type;
```

### BUG: Duplicate `get_gift_list` (Dead Code)

**Location**: `get_gift_list_old` L114-204

Old version of `get_gift_list` still exists in the file with the name `get_gift_list_old`. Consumes 90 lines. The active version at L383+ has gift status filtering and  deduction tracking. The old version is **dead code** — never called.

### NOTE: `get_customerenquiry` — No Date Filter

`get_customerenquiry()` (L11-18) has no WHERE clause — it returns all records from `cust_enquiry`. It's called when `ajax_enquiry_list` is triggered without date params. On large systems this could be slow/heavy.

---

## Tables Map — `admin_report_model.php`

| Table | Usage |
|---|---|
| `cust_enquiry` | Customer enquiry read + status write |
| `cust_enquiry_product` | Enquiry product details |
| `cust_enquiry_status` | Enquiry history log |
| `gift_issued` | Gift issued records |
| `gifts` | Gift master (name, weight) |
| `metal` | Metal type (Gold/Silver etc) |
| `msg91_delivery_status` | SMS delivery log |
| `chit_settings` | GST/config settings |
| `customer` | Celeb dates (birthday/wedding) |
| `address` | City join for celeb report |
| `city` | City name display |
| `scheme_account` | Subquery counts in celeb + gift report |
| `payment` | Subquery paid installment count in gift report |
| `employee` | Gift issuer/collector name |
| `branch` | Branch filter |
| `scheme` | Scheme name grouping |
