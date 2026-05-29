# PURCHASE MODULE — VIEW XSS AUDIT & QUERY PERFORMANCE
> **Round:** 8 | **Date:** 2026-02-23 | **Focus:** XSS in views, N+1 queries, unbounded results

---

## 🔴 XSS: ZERO Output Escaping

### Stats
| Metric | Count |
|---|---|
| Unescaped `<?= $var` / `echo $var` in views | **325** |
| `htmlspecialchars()` usage in views | **0** |
| `htmlentities()` usage in views | **0** |
| `xss_clean()` usage in views | **0** |
| Inline JS event handlers (`onclick=`, etc.) | **16** |

**Every single PHP variable rendered in purchase views is unescaped.** If any stored data contains HTML/JS (e.g., karigar name with `<script>` tag, or a remark field with HTML), it will execute in the browser.

### Top Offending View Files

| # | File | Unescaped Outputs | Context |
|---|---|---|---|
| 1 | `vendor_ack.php` (×3 subfolders) | **27, 16, 16** | Vendor acknowledgement — sent externally |
| 2 | `job_receipt.php` | **19** | Job receipt printout |
| 3 | `form.php` (×6 subfolders) | **18, 12, 10, 8, 8, 7** | Entry forms across sub-modules |
| 4 | `invoice.php` | **18** | Invoice print view |
| 5 | `vendor_ack_new.php` | **16** | New vendor ack template |
| 6 | `pur_entry_form.php` | **15** | Purchase entry form |
| 7 | `vendor_ack_detailed.php` | **8** | Detailed vendor ack |
| 8 | `print.php` (×2) | **7, 4** | Print views |
| 9 | `vendor_ack_email.php` | **5** | Email template — XSS in emails! |
| 10 | `payment_ack.php` | **5** | Payment acknowledgement |
| 11 | `approvalentry.php` | **5** | Approval entry |
| 12 | `approval_tag.php` | **4** | Tag approval |

> ⚠️ **`vendor_ack_email.php`** is particularly dangerous — unescaped output in email templates can lead to XSS when viewed in webmail clients.

### Inline JS Event Handlers
| File | Count | Examples |
|---|---|---|
| `form.php` (various) | 10 | `onclick=`, `onchange=` |
| `order_form.php` | 2 | `onclick=` |
| `pur_entry_form.php` | 1 | `onchange=` |
| `approvalentry.php` | 1 | `onclick=` |

---

## 🟠 N+1 Query Patterns

25 loop-with-inner-query patterns found in model:

### Critical N+1 Locations
| Line | Loop | Inner Query Type | Impact |
|---|---|---|---|
| 297 | `foreach($result as $items)` | Stone details query per item | Performance on large POs |
| 547, 571 | `foreach($result as $items)` | Image/detail query per item | DB roundtrips |
| 698 | `foreach($result as $items)` | QC receipt items per PO item | Slow on bulk QC |
| 883, 917 | `foreach($result as $items)` | Stone/other-metal per item | Billing performance |
| 972 | `foreach($result as $val)` | QC details per item | Compound N+1 |
| 1028 | `foreach($result as $items)` | HM items per issue | HM performance |
| 1143 | `foreach($result as $items)` | HM stone details per item | Deep N+1 |
| 1736 | `foreach($result as $val)` | Payment details per PO | Payment list slow |
| 1807 | `foreach($purchaseitems...)` | Rate fix per item | Rate fixing perf |
| 4029-4030 | Nested foreach | GRN detail per item | Double N+1 |
| 4981, 5033 | `foreach($result as $val)` | Balance calc per PO | PO balance page slow |
| 8015 | `foreach(receipt_type...)` | Retagging types | Already documented in R2 |
| 8069, 8091 | `foreach($result as $val)` | Supplier details per item | Return perf |

**Estimated impact:** On a page with 100 items, each N+1 generates 100+ additional queries. With 25 N+1 patterns, this module has severe performance risk on large datasets.

---

## 🟡 Unbounded Queries

| Metric | Count |
|---|---|
| Result-returning queries (`.get()`, `.result_array()`) | **181** |
| Queries with `LIMIT` clause | **13** |
| **Queries WITHOUT `LIMIT`** | **168** (93%) |

168 of 181 queries can return unlimited rows. On a production database with years of data, these queries will load thousands of rows into PHP memory.

### High-Risk Unbounded Queries
- All DataTables AJAX endpoints (server-side pagination should be used)
- All report queries (vault report, retagging, weight gain/loss)
- All "get list" model methods

---

## Summary — Round 8 Findings

| # | Finding | Count | Severity |
|---|---|---|---|
| 1 | **Unescaped PHP output in views** | 325 | 🔴 CRITICAL (XSS) |
| 2 | **Zero `htmlspecialchars()` usage** | 0 of 325 | 🔴 CRITICAL |
| 3 | **N+1 query loops** | 25 | 🟠 HIGH (perf) |
| 4 | **Queries without LIMIT** | 168 of 181 | 🟠 HIGH (perf) |
| 5 | **Inline JS in views** | 16 | 🟡 MEDIUM |
| 6 | **XSS in email template** | 1 | 🔴 CRITICAL |

## Cumulative Bug Patterns (Rounds 2-8)
| Round | Patterns | Key Theme |
|---|---|---|
| R2 | 4 | Retagging method bugs |
| R4 | 8 | Security (debug, $_POST, raw SQL) |
| R5 | 4 | Validation gaps, error handlers |
| R6 | 5 | Access control, uploads, complexity |
| R7 | 5 | JS mega-functions, DataTable, events |
| R8 | 6 | XSS (325 outputs), N+1 queries, unbounded |
| **Total** | **32 patterns** |