# Performance Risks — System-Wide Bottlenecks

> Cross-module performance risks identified from SCHEMA_ANALYSIS.md, FLOW_RISK_MATRIX.md, and FORENSIC_TEMPLATE.md.
> Last updated: 2026-03-26 (Round 3 refresh)

---

## Critical Performance Risks

### PERF-001: ret_taging Full Table Scans (ALL Modules)

| Risk | Details |
|---|---|
| **Table** | `ret_taging` — grows 1000+ rows/month per branch |
| **Problem** | 13+ modules query this table. Reports generate aggregation queries without date bounds in some cases |
| **Impact** | Slow stock reports, slow tag search, slow billing tag lookup |
| **Fix** | Add composite indexes on `(id_branch, tag_status)`, `(id_branch, id_section, tag_status)`, ensure all queries use branch + status filters |

### PERF-002: Retail Dashboard N+1 Query Storm

| Risk | Details |
|---|---|
| **Table** | Multiple — `ret_billing`, `ret_estimation`, `ret_taging`, `ret_lot_inwards`, etc. |
| **Problem** | Dashboard loads 15+ separate AJAX calls, each querying different tables with minimal caching |
| **Impact** | Dashboard page load > 5s on large datasets |
| **Fix** | Consolidate dashboard queries into materialized views or batch API |

### PERF-003: Chit Reports scheme_summary — 4 Sub-Queries Per Scheme

| Risk | Details |
|---|---|
| **Table** | `scheme`, `scheme_account`, `payment` |
| **Problem** | `scheme_summary_data()` runs 4 separate queries per scheme row (total/paid/pending/closed) |
| **Impact** | For 50 schemes × 4 queries = 200 queries per page load |
| **Fix** | Rewrite as single aggregate query with conditional counts |

### PERF-004: ret_nontag_item Arithmetic Updates Under Concurrency

| Risk | Details |
|---|---|
| **Table** | `ret_nontag_item` |
| **Problem** | Multiple modules use `+`/`-` arithmetic UPDATEs (non-atomic in multi-user). Branch Transfer, Section Transfer, LOT, Old Metal all write separately |
| **Impact** | Weight drift under concurrent operations — stock balance becomes inaccurate |
| **Fix** | Use `FOR UPDATE` row locks or stored procedure for atomic weight updates |

### PERF-005: Reports Model Full-Table Aggregations

| Risk | Details |
|---|---|
| **Table** | `ret_billing`, `ret_bill_details`, `ret_estimation`, `ret_taging` |
| **Problem** | `getBillDetails()` called from Retail Dashboard does full-table aggregation without adequate indexes |
| **Impact** | Dashboard cash abstract takes 10+ seconds on large databases |
| **Fix** | Add date-range indexes, consider summary tables |

### PERF-006: SMS Gateway Synchronous Calls in Transaction

| Risk | Details |
|---|---|
| **Modules** | Payment, Account, Customer, Stock Issue, Branch Transfer, Section Transfer |
| **Problem** | SMS/WhatsApp API calls made synchronously inside save transactions |
| **Impact** | If SMS gateway is slow/down, entire save hangs — 30s+ timeout possible |
| **Fix** | Queue SMS sends asynchronously (job queue or cron) |

### PERF-007: Email Inside Transaction (Customer Order)

| Risk | Details |
|---|---|
| **Module** | Customer Order |
| **Problem** | Email sent at L1993 inside `trans_begin`/`trans_commit` block |
| **Impact** | Slow email dispatch delays transaction commit, holds DB locks |
| **Fix** | Move email dispatch outside transaction boundary |

### PERF-008: POS cURL Calls in Billing Transaction

| Risk | Details |
|---|---|
| **Module** | Billing |
| **Problem** | `postcurlPOSRequests()` makes external HTTP call inside bill save |
| **Impact** | POS API slowness/downtime blocks billing for all users |
| **Fix** | Queue POS sync, save bill first, sync asynchronously |

### PERF-009: payment.js Monolith (357KB)

| Risk | Details |
|---|---|
| **Module** | Payment (frontend) |
| **Problem** | Single 357KB JS file loaded on every payment page, unminified |
| **Impact** | Slow initial page load, browser memory pressure on low-end devices |
| **Fix** | Split into page-specific bundles, enable minification |

### PERF-010: Receipt Number MAX() Full Table Scan

| Risk | Details |
|---|---|
| **Module** | Payment |
| **Problem** | `get_receipt_no()` uses `MAX(receipt_no)` across full payment table (model L31-123) |
| **Impact** | Slower over time as payments grow; blocks during high-traffic periods |
| **Fix** | Add index on `receipt_no` + relevant grouping columns, or use separate sequence table |

### PERF-011: get_paymentContent() 10-Table JOIN

| Risk | Details |
|---|---|
| **Module** | Payment |
| **Problem** | `get_paymentContent()` (model L1386-1599) joins 10+ tables for a single account view |
| **Impact** | Slow account detail page, especially for accounts with many payments |
| **Fix** | Cache frequently accessed account data, paginate payment history |

### PERF-012: Customer Sync N+1 Pattern

| Risk | Details |
|---|---|
| **Module** | Customer |
| **Problem** | `sync_existing_data()` queries `customer_reg` + `transaction` tables per customer on Add, with individual inserts per scheme account/payment |
| **Impact** | Slow customer add when customer has many offline records |
| **Fix** | Batch inserts for scheme accounts and payments |

### PERF-013: Payment Verify Loop (Gateway cURL)

| Risk | Details |
|---|---|
| **Module** | Payment |
| **Problem** | `verify_*()` methods iterate all pending transaction IDs with individual cURL calls (8s timeout each) |
| **Impact** | Verification bottleneck — 100 pending payments = 800s (13 min) worst case |
| **Fix** | Use batch API if gateway supports it; parallelize cURL calls |

### PERF-014: LOT `SHOW COLUMNS` on Every Insert/Update

| Risk | Details |
|---|---|
| **Module** | LOT |
| **Problem** | `insertData()` / `updateData()` fire `SHOW COLUMNS` on every call — 15+ extra DB queries per lot save |
| **Impact** | Measurable overhead on lot-heavy operations (bulk tag creation) |
| **Fix** | Cache column list in static variable or use explicit column arrays |

### PERF-015: Employee Address Duplicate Accumulation

| Risk | Details |
|---|---|
| **Module** | Employee |
| **Problem** | Every employee edit inserts a NEW address row instead of updating — address table grows unbounded (EMP-BUG-003) |
| **Impact** | Address table bloat, JOINs return wrong address |
| **Fix** | Fix string comparison bug in address check, use UPDATE instead of INSERT |

### PERF-016: Scheme `SHOW COLUMNS` on Every Insert/Update

| Risk | Details |
|---|---|
| **Module** | Scheme |
| **Problem** | Same `SHOW COLUMNS` pattern as LOT — scheme table has 130+ columns |
| **Impact** | Higher overhead due to large column count |
| **Fix** | Same as PERF-014 |

---

## Table Growth Rate Estimates

| Table | Growth Rate | Modules Writing | Cleanup? |
|---|---|---|---|
| `ret_taging` | ~1000/month/branch | 8 modules | No — soft delete only (status=2/5) |
| `ret_taging_status_log` | ~5000/month/branch | 7 modules | No purge mechanism |
| `ret_billing` | ~200/month/branch | 2 modules | No — permanent |
| `ret_estimation` | ~500/month/branch | 1 module | No — permanent |
| `payment` | ~1000/month/client | 4 modules | No — permanent |
| `ret_nontag_item_log` | ~2000/month/branch | 4 modules | No purge |
| `ret_section_tag_status_log` | ~3000/month/branch | 3 modules | No purge |
| `log_detail` | ~10000/month | ALL modules | No purge |
| `customer_reg` | Sync-dependent | Customer | No purge (sync staging) |
| `transaction` | Sync-dependent | Customer | No purge (sync staging) |
| `payment_mode_details` | 1:N per payment | Payment | No purge (orphan risk on delete) |
| `payment_status` | 1:N per payment | Payment | No purge |
| `postdate_payment` | ~100/month/client | Payment | No purge |

---

## Priority Matrix

| Priority | Risk | Estimated Impact |
|---|---|---|
| **P0** | PERF-004: NT arithmetic drift | Data integrity (stock balance) |
| **P1** | PERF-006: SMS in transaction | User-facing hang on save |
| **P1** | PERF-008: POS cURL in transaction | Billing blocked by POS API |
| **P1** | PERF-013: Payment verify loop | 13-minute worst-case verification |
| **P2** | PERF-001: ret_taging indexes | Slow queries across 13 modules |
| **P2** | PERF-003: Chit scheme_summary | 200 queries/page on reports |
| **P2** | PERF-010: Receipt MAX() scan | Slower over time |
| **P3** | PERF-002: Dashboard N+1 | Slow dashboard load |
| **P3** | PERF-005: Full-table aggregation | Slow cash abstract |
| **P3** | PERF-009: payment.js monolith | Slow payment page load |
| **P3** | PERF-011: 10-table JOIN | Slow account detail |
| **P3** | PERF-012: Customer sync N+1 | Slow customer add |
