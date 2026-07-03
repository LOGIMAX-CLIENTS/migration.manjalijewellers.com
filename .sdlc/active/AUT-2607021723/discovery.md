# Discovery: AUT-2607021723

> **Query**: Document complete autodebit flow - subscription creation, authorization, webhook handling, payment processing, retry, and admin reporting
> **Module**: autodebit
> **Generated**: 2026-07-02 17:25

## 1. LCA Impact Analysis

> LCA disabled in config. Using grep + call tree instead.

LCA index not found. Run: `lca index build admin/application -m all -o .lca/index.json`

## 2. RAG Semantic Search

> RAG disabled in config. Skipped.

## 3. Recipe Check

**15 recipe match(es) found** (13 cluster(s)):

#### Cluster: webhook (1 recipes)

- **[🔴 HIGH score=7]** `recipe_webhook_pg_implementation.md`
  - Title: Recipe: Payment Gateway Webhook Implementation for Old Clients
  - Matched keywords: creation, complete, webhook, processing, payment, title_match:2
  ```markdown
  # Recipe: Payment Gateway Webhook Implementation for Old Clients
  
  > Ports the full Cashfree / Easebuzz / Razorpay webhook + auto/manual verification engine into old clients that were deployed before `Chit_transaction` existed. Apply this recipe end-to-end in order — do not skip or reorder steps.
  
  ## Metadata
  - **Pattern ID**: PAT-PAY-WH-001
  - **Severity**: HIGH
  - **Modules Affected**: Payment, Services, Account, Mobile API
  - **Auto-fixable**: No (multi-file, requires controller copy + model additions)
  
  ## Client Scope
  - **Applies to**: Old clients that do NOT have `admin/application/controllers/chit_transaction.php`
  - **Reason**: Newer clients ship with this controller. Old clients were onboarded before the webhook engine existed and need it backported.
  
  ## Created By
  - **Developer**: Antigravity AI / Rahul J
  - **Client**: srivallivilasjewellery.in (first backport reference)
  - **Date**: 2026-04-21
  - **Source Bug ID**: feature/https/pm.logimaxindia.com/admin/pm/tasks/e75655d7-84f3-469a-920b-8048e3a0684e
  
  ---
  
  ## Symptom
  - Online payments via Cashfree / Easebuzz / Razorpay remain stuck in **Pending** status even after the customer successfully pays.
  - Gateway posts the webhook but nothing updates in the database — no handler exists.
  - Admin has no manual re-verification endpoint.
  - No auto-verification cron job exists on this client.
  
  ## Root Cause
  The old `mobile_api.php` has a legacy `cashfreeResponse_post()` that handles only Cashfree v2 payloads. There is no unified webhook controller, no gateway-specific payload parser, and the required `payment_model` methods for batch status updates do not exist.
  
  Additionally, the Cashfree order creation call in `mobile_payment_post()` does NOT include `order_tags` in the payload. This means Cashfree has no way to pass the internal `id_payment` values back in the webhook — so even after the webhook engine is in place, the `parseCashfreeWebhook()` parser cannot identify which DB payment rows to update. The API URL also had a hardcoded `"pg/orders"` suffix that is now passed as part of `api_url` from the `gateway` table.
  
  ---
  
  ## Detection (Run First — Confirm Recipe Applies)
  
  ```command
  # 1. Recipe applies ONLY if this file does NOT exist:
  ls admin/application/controllers/chit_transaction.php
  
  # 2. Confirm old handler is still active:
  grep -n "function cashfreeResponse_post" application/controllers/mobile_api.php
  
  # 3. Confirm new model methods are missing:
  grep -n "function updateGatewayResponse\|function getBranchGatewayData\|function getPendpayment_Data" admin/application/models/payment_model.php
  ```
  
  ```command
  # 4. Also check if order_tags is missing from Cashfree order creation (mobile_payment_post):
  ```

#### Cluster: credit (2 recipes)

- **[🔴 HIGH score=5]** `recipe_credit_debit_ledger_porting.md`
  - Title: Recipe: Porting Credit/Debit Ledger & PO Adjustments
  - Matched keywords: document, flow, complete, payment, handling
  ```markdown
  # Recipe: Porting Credit/Debit Ledger & PO Adjustments
  
  > This recipe provides instructions to port the complete Credit/Debit Ledger management module, including the Ledger Master, Credit/Debit Entry pages, and the PO Payment Debit Note Adjustments flow to a new client environment.
  
  ## Metadata
  - **Pattern ID**: PAT-CDL-001
  - **Severity**: HIGH
  - **Modules Affected**: Purchase Orders, Purchase Order Payments, Masters (CR/DR Ledger Master), Credit/Debit Entry
  - **Auto-fixable**: No (requires structural schema additions, file copies, and code ports)
  
  ## Client Scope
  - **Applies to**: ALL
  - **Reason**: Any ERP client missing the new "Debit Note Adjustments" module in PO Payments and the standalone Credit/Debit Entry module.
  
  ## Created By
  - **Developer**: Antigravity
  - **Client**: LOGIMAX-CLIENTS/shop.gupthagem.com
  - **Date**: 2026-05-05
  - **Source Bug ID**: N/A (Feature Port)
  
  ## Symptom
  Clients do not have the ability to create Credit/Debit Notes for Suppliers/Smiths independently of Purchase Returns. The PO Payment form lacks the "Debit Note Adjustment" modal. When inspecting `admin_ret_purchase`, the `credit_debit_entry` endpoints are missing. The `ret_crdr_ledger` master is completely absent.
  
  ## Root Cause
  The Credit/Debit ledger tables, Master UI, Entry UI, and PO Adjustment logic (developed in `etail_development_src`) have not been pushed to the client repository.
  
  ## Files
  - `db_queries.txt` (DB changes)
  - `admin/application/config/routes.php`
  - `admin/application/controllers/admin_ret_catalog.php`
  - `admin/application/controllers/admin_ret_purchase.php`
  - `admin/application/models/ret_catalog_model.php`
  - `admin/application/models/ret_dashboard_api_model.php`
  - `admin/application/models/ret_purchase_order_model.php`
  - `admin/application/views/master/ret_crdr_ledger/list.php` [NEW]
  - `admin/application/views/ret_purchase/credit_entry/form.php` [NEW]
  - `admin/application/views/ret_purchase/credit_entry/list.php` [NEW]
  - `admin/application/views/ret_purchase/credit_entry/print.php` [NEW]
  - `admin/application/views/ret_purchase/popayment/form.php`
  - `admin/assets/js/catalog_master.js`
  - `admin/assets/js/ret_purchase_order.js`
  
  ## Fix
  
  ### Step 1: Database Schema Additions
  Execute the following table definitions to create the CR/DR ledger masters and PO adjustments linkage tables:
  ```sql
  CREATE TABLE `ret_crdr_ledger` ...
  CREATE TABLE `ret_crdr_note` ...
  CREATE TABLE `ret_crdr_note_po_adj` ...
  ```

- **[🟡 MEDIUM score=3]** `recipe_credit_toggle_balance_stale.md`
  - Title: Credit Toggle Payment Balance Not Recalculated
  - Matched keywords: payment, flow, title_match:1

#### Cluster: payment (2 recipes)

- **[🔴 HIGH score=5]** `recipe_payment_subquery_full_scan_v2.md`
  - Title: Recipe: Full Payment Subquery Optimization — Admin + Mobile API + Index Fixes
  - Matched keywords: complete, creation, webhook, payment, title_match:1
  ```markdown
  # Recipe: Full Payment Subquery Optimization — Admin + Mobile API + Index Fixes
  
  > Complete optimization of all unscoped payment subqueries across admin payment_model and mobile mobileapi_model, plus missing database indexes on payment.ref_trans_id and customer search columns. Reduces page loads from 23s to 268ms.
  
  ## Metadata
  - **Pattern ID**: PAT-QUERY-001-v2
  - **Severity**: CRITICAL
  - **Modules Affected**: Payment (admin payment_model), Mobile API (mobileapi_model), Customer Search
  - **Auto-fixable**: Yes (code fixes) / Manual (index creation)
  - **Supersedes**: `recipe_payment_subquery_full_scan.md` (PAT-QUERY-001)
  
  ## Client Scope
  - **Applies to**: ALL (any client with 50K+ payment records)
  - **Reason**: `get_paymentContent()` and `get_payment_details()` exist in all deployments. The same 3 subqueries (cshpay, cp, sp) scan entire tables globally. Becomes critical at 100K+ payment rows.
  
  ## Created By
  - **Developer**: Antigravity AI
  - **Client**: erp.manepally.com
  - **Date**: 2026-05-30
  - **Source Bug ID**: Slow Query Log Process IDs 461944, 462027, 462040, 462042
  
  ## Symptom
  - Payment form takes 23+ seconds to load
  - Mobile API customer scheme listing takes 8+ seconds
  - Razorpay webhook UPDATE takes 6+ seconds
  - Customer search autocomplete takes 4+ seconds
  - MySQL slow query log shows full table scans on `payment`, `payment_mode_details`, `customer`
  
  ## Root Cause
  
  ### Problem 1: Three unscoped subqueries in payment queries
  The `get_paymentContent()` (admin) and `get_payment_details()` (mobile API) functions have 3 derived subqueries that scan the **ENTIRE** database even though the main query is for ONE account/customer:
  
  | Subquery | Table Scanned | What it does | Impact |
  |---|---|---|---|
  | `cshpay` | `payment_mode_details` (ALL rows) | Cash payment amounts | Scans 500K+ rows |
  | `cp` | `payment` (ALL current month) | Current month totals | Scans 10K+ rows |
  | `sp` | `payment` (ALL matching day+month) | Current day totals | Scans 50K+ rows |
  
  Additionally, `cp` and `sp` use `Date_Format()` on indexed columns, preventing index usage.
  
  ### Problem 2: Missing index on `payment.ref_trans_id`
  Razorpay webhook runs `UPDATE payment SET ... WHERE ref_trans_id = '...'` — no index exists, causing full table scan.
  
  ### Problem 3: Missing index on `customer.mobile` / `customer.firstname`
  Customer search uses `LIKE 'prefix%'` on 3 columns with OR — no indexes exist.
  
  ## Detection
  ```bash
  # Detect unscoped cp/sp subqueries in admin payment model
  ```

- **[🟡 MEDIUM score=3]** `recipe_payment_due_sync_bounds.md`
  - Title: Recipe Template
  - Matched keywords: webhook, payment, flow

#### Cluster: dynamic (1 recipes)

- **[🟡 MEDIUM score=4]** `recipe_dynamic_kyc_phase_I.md`
  - Title: Recipe: Dynamic KYC System — Phase I
  - Matched keywords: creation, document, payment, flow

#### Cluster: excess (1 recipes)

- **[🟡 MEDIUM score=4]** `recipe_excess_installment_payment.md`
  - Title: Recipe: Excess Installment Payment Not Blocked
  - Matched keywords: complete, processing, payment, title_match:1

#### Cluster: scheme (1 recipes)

- **[🟡 MEDIUM score=4]** `recipe_scheme_payment_prn_layout.md`
  - Title: Recipe: Scheme Payment Sticker Printing PRN Layout Config
  - Matched keywords: complete, handling, payment, title_match:1

#### Cluster: chit (1 recipes)

- **[🟡 MEDIUM score=3]** `recipe_chit_dynamic_metal_weight_decimal.md`
  - Title: Recipe: Dynamic Metal Weight Decimal Precision (Chit / Payment Module)
  - Matched keywords: complete, payment, title_match:1

#### Cluster: concurrent (1 recipes)

- **[🟡 MEDIUM score=3]** `recipe_concurrent_payment_race_condition.md`
  - Title: Recipe: Concurrent Payment Race Condition (Admin + Mobile)
  - Matched keywords: complete, payment, title_match:1

#### Cluster: digi (1 recipes)

- **[🟡 MEDIUM score=3]** `recipe_digi_gold_configuration.md`
  - Title: Recipe: Digi Gold Configuration — Phase I
  - Matched keywords: webhook, payment, flow

#### Cluster: enrollment (1 recipes)

- **[🟡 MEDIUM score=3]** `recipe_enrollment_502_ajax_race.md`
  - Title: Recipe: 502 Bad Gateway During Account Enrollment — AJAX Session Race
  - Matched keywords: complete, payment, flow

#### Cluster: po (1 recipes)

- **[🟡 MEDIUM score=3]** `recipe_po_stale_flags_lot_qc.md`
  - Title: Recipe: Stale PO Item Flags (is_lot_created, qc_failed_pcs)
  - Matched keywords: processing, payment, flow

#### Cluster: repair (1 recipes)

- **[🟡 MEDIUM score=3]** `recipe_repair_order_workflow_branch_validation.md`
  - Title: Repair Order Workflow — Missing Branch Validation & Visibility
  - Matched keywords: complete, creation, title_match:1

#### Cluster: sendmail (1 recipes)

- **[🟡 MEDIUM score=3]** `recipe_sendmail_502_hang.md`
  - Title: Recipe: Sendmail Protocol 502 Hang
  - Matched keywords: complete, handling, payment

> **Common pattern across matches**: payment, complete, flow, creation, webhook


## 4. Key Code Snippets

> Strategy: RAG_SEARCH (no URL-based call tree available)

No key code snippets extracted (URL or method not available).

## 5. Track Classification

- RAG unique files: 0
- LCA modules: 1 (autodebit)
- Recipe found: Yes
- Code snippets: No
- **Suggested track: EXPRESS**

## 6. Next Actions

2. Apply recipe variant
3. Form hypothesis and create requirement artifact

## 8. Investigator Starting Points

> **Read these files IN THIS ORDER.** Results ranked by confidence across all discovery sources.

1. 🔴 **[HIGH]** `See Section 3` — Matching recipe found — check if this exact fix pattern applies
   _Source: Recipe_

> **1 starting points**: 1 HIGH, 0 MEDIUM, 0 LOW

## 7. Discovery Quality

**Score: 25/80 (31%) 🔴 LOW**

- RAG: n/a (not configured)
- LCA: 5/15 (1 modules, call chain: no)
- Code snippets: 0/20 (none)
- L2 methods: 0/10 (0 resolved)
- L3 depth: 0/10 (0 methods)
- Brain context: 10/10 (via RAG)
- Tables: 0/5 (0 found)
- Recipe: 10/10 (found)
