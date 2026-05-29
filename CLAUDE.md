# CLAUDE.md — eTail ERP Bug Remediation System

> This file instructs any AI assistant (Antigravity/Claude/Gemini) working on this codebase.
> **Read this file FIRST before fixing any bug.**

---

## Product Overview

**eTail** is a retail jewellery ERP by Logimax Technologies, built on PHP/CodeIgniter 2.x with MySQL.
- **GitHub Repo:** `Logimax-Technologies/etail_development_src` (source codebase)
- **70+ client deployments** — each client runs a copy with varying customizations
- **Version:** 1.1.1.0196
- **Domain:** Jewellery retail — handles chit schemes, gold/silver tagging, billing (GST), branch transfers, old metal exchange, order management, and customer apps

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 7.x, CodeIgniter 2.x |
| Database | MySQL (via CI Active Record) |
| Frontend | jQuery, Bootstrap 3, DataTables, Select2 |
| Server | Apache/XAMPP (local), Linux VPS (production) |
| APIs | REST (CodeIgniter REST Server), SMS (Msg91/NettyFish/SpearUC), WhatsApp (QikChat), POS (Pinelab), eInvoice (NIC/TaxPro), Aadhaar (ZOOP), Payment (Cashfree) |
| Apps | Android/iOS customer app, Admin app (OneSignal push) |
| Deployment | Git + webhooks (mono-repo clients) / manual (standalone clients) |

---

## Codebase Structure

```
/admin/application/
  ├── controllers/          → admin_ret_<module>.php (business logic entry points)
  ├── models/               → ret_<module>_model.php (database operations)
  ├── views/                → <module>/ (HTML templates)
  │     └── billing/print/  → bill_format_1.php, bill_format_2.php (print templates)
  ├── helpers/              → receipt_helper.php, common_helper.php (shared logic)
  ├── config/               → config.php (integrations, settings), routes.php, database.php
  └── libraries/            → Custom libraries
/api/                       → REST API for mobile apps
/application/               → Frontend customer-facing app (CodeIgniter)
/webhooks/                  → Deployment webhook handlers
/knowledge_brain/           → AI module documentation (28+ modules)
/bug_report_AI/             → Bug patterns and audit tools
/AI_Bug_Fix_System/         → Fingerprint tool, fix system
```

---

## All Modules (28+)

### Retail Core
| Module | Controller | Brain |
|---|---|---|
| Billing | `admin_ret_billing.php` | `knowledge_brain/Billing/` |
| Estimation | `admin_ret_estimation.php` | `knowledge_brain/Estimation/` |
| Tagging | `admin_ret_tagging.php` | `knowledge_brain/Tagging/` |
| Lot Management | `admin_ret_lot.php` | `knowledge_brain/Lot/` |
| Stock Issue | `admin_ret_stock_issue.php` | `knowledge_brain/Stock_Issue/` |
| Catalog/Inventory | `admin_ret_catalog_inventory.php` | `knowledge_brain/Catalog_Inventory/` |
| Branch Transfer | `admin_ret_branch_transfer.php` | `knowledge_brain/Branch Transfer/` |
| Sales Transfer | `admin_ret_sales_transfer.php` | `knowledge_brain/Sales_Transfer/` |
| Section Transfer | `admin_ret_section_transfer.php` | `knowledge_brain/section_transfer/` |
| Customer Order | `admin_ret_customer_order.php` | `knowledge_brain/customer_order/` |
| Old Metal Process | `admin_ret_old_metal.php` | `knowledge_brain/old_metal_process/` |
| Purchase | `admin_ret_purchase.php` | `knowledge_brain/purchase/` |
| Reports | `admin_ret_reports.php` | `knowledge_brain/Ret_Reports/` |
| Retail Dashboard | - | `knowledge_brain/retail_dashboard/` |
| Retail Settings | - | `knowledge_brain/retail_settings/` |
| Other Inventory | - | `knowledge_brain/other_inventory/` |

### Chit/Scheme Module
| Module | Controller | Brain |
|---|---|---|
| Scheme Management | `admin_manage.php` | `knowledge_brain/scheme/` |
| Payment Collection | `admin_payment.php` | `knowledge_brain/payment/` |
| Chit Dashboard | - | `knowledge_brain/chit_dashboard/` |
| Chit Reports | - | `knowledge_brain/chit_reports/` |
| Chit Settings | - | `knowledge_brain/chit_settings/` |
| Chit Collection App | - | `knowledge_brain/chit_collection_app/` |
| Chit Customer App | - | `knowledge_brain/chit_customer_app/` |
| Chit Services | - | `knowledge_brain/chit_services/` |

### Supporting Modules
| Module | Controller | Brain |
|---|---|---|
| Customer | `admin_customer.php` | `knowledge_brain/customer/` |
| Employee | `admin_employee.php` | `knowledge_brain/employee/` |
| Account/Journal | `admin_account.php` | `knowledge_brain/account/` |
| Masters | `admin_masters.php` | `knowledge_brain/masters/` |

---

## System Brain Documents

All at `knowledge_brain/_SYSTEM/`:

| Document | What It Contains |
|---|---|
| `CROSS_MODULE_BUGS.md` | 113 known cross-module bugs with severity |
| `DANGER_ZONES.md` | 55 high-risk code areas with system-wide impact |
| `DATA_FLOW_CHAINS.md` | 10 end-to-end business flows |
| `DIAGNOSTIC_PLAYBOOK.md` | 9 symptom-based troubleshooting guides |
| `SHARED_TABLES.md` | Tables used across multiple modules |
| `SHARED_MODELS.md` | Models loaded by multiple controllers |
| `MODULE_DEPENDENCIES.md` | Which modules depend on which |
| `TAG_STATUS_MAP.md` | Tag lifecycle state machine (0→1→2) |
| `VALIDATION_GAPS.md` | Missing server-side validations |
| `HARDCODED_VALUES.md` | Magic numbers and strings in code |
| `CLEANUP_GAPS.md` | Orphaned data after cancel/delete |
| `PERFORMANCE_RISKS.md` | Slow queries and performance issues |
| `SYSTEM_COVERAGE.md` | Brain coverage status per module |

---

## Key Business Concepts

| Concept | Details |
|---|---|
| **Tag** | Physical jewellery piece. Lifecycle: LOT → Tag (status=0) → Estimated (1) → Billed (2) |
| **Bill Types** | 1=Sales, 2=Sales+Purchase, 3=Sales+Purch+Return, 4=Purchase, 5=Order Advance, 7=Sales Return, 8=Credit Collection, 9=Order Delivery, 10=Chit Pre-Close, 11=Repair Delivery |
| **Chit/Scheme** | Monthly saving scheme. Customer pays installments → matures → utilized in billing. Has closing_weight, closing_amount, utilized_amt, additional_benefits |
| **Gold Rate** | 22ct base rate (`goldrate_22ct`), used for weight-to-value conversions |
| **GST** | CGST + SGST (intra-state) or IGST (inter-state). TCS applied above threshold |
| **Old Metal** | Customer brings old gold → weighed, tested, valued → deducted from bill |
| **OTP** | Required for critical operations (cancel, high-value). Sent via SMS. Bypass = critical security bug |
| **Journal Entry** | Double-entry accounting. Every financial transaction creates debit + credit entries |

---

## How To Fix a Bug

### Step 1: IDENTIFY — Is this a known bug?
- Search `bug_report_AI/COMMON_BUG_PATTERNS.md` for matching symptom
- Search `knowledge_brain/_SYSTEM/CROSS_MODULE_BUGS.md` for the module
- If match found → jump to Step 3 with existing recipe

### Step 2: DEBUG — Where is the flow broken?
- Determine which business flow is affected (see Flow Checklists below)
- Load the module brain: `knowledge_brain/<Module>/MODULE_BRAIN.md`
- Load the flow risk matrix: `knowledge_brain/<Module>/FLOW_RISK_MATRIX.md`
- Trace: which step deviates from what the checklist says should happen?
- Output: exact file, exact function, exact deviation

### Step 3: FIX — Make the code match the flow
- If a recipe exists in `.agent/skills/bug-fix-engine/recipes/` → apply it
- If no recipe → write fix to match the flow checklist spec
- Run `php -l <file>` to verify syntax

### Step 4: VERIFY
- Trace the fix against the flow checklist — does it restore expected behavior?
- Check `SHARED_TABLES.md` and `MODULE_DEPENDENCIES.md` for downstream impact
- Check: does the print/report output match the save logic?

### Step 5: LEARN
- Save the fix as a recipe in `.agent/skills/bug-fix-engine/recipes/`
- Update the flow checklist if an edge case was missing

---

## Flow Checklists

Each checklist audits **5 actions**: Save, Cancel, **Edit**, Print, **Report**

| # | Flow | Modules Involved |
|---|---|---|
| 1 | Sales Bill | Billing, Tag, Stock, Account |
| 2 | Sales + Old Metal Exchange | Billing, Purchase, Tag, Stock, Account |
| 3 | Sales + Return (Exchange) | Billing, Tag, Stock, Account |
| 4 | Order Advance → Order Delivery | Order, Billing, Payment, Tag |
| 5 | Chit Account → Utilization → Billing | Scheme, Billing, Account |
| 6 | Chit Pre-Close | Scheme, Billing, Account |
| 7 | Credit Sale → Credit Collection | Billing, Account, Payment |
| 8 | Branch Transfer (Send/Receive/Cancel) | Transfer, Tag, Stock (multi-branch) |
| 9 | Sales Return (standalone) | Billing, Tag, Stock, Account |
| 10 | Estimation → Billing Conversion | Estimation, Billing, Tag |
| 11 | Repair Order → Repair Delivery | Repair, Billing, Tag |
| 12 | Customer Registration + Loyalty | Customer, Account |

**Rule**: If save touches 8 tables but cancel only reverses 5 → those 3 are bugs.
**Rule**: If save uses formula A but print uses formula B → that's a bug.
**Rule**: If report aggregates differently from what save wrote → that's a bug.

---

## Batch Bug Fixing (Pattern-Level)

For bugs detectable by code search (grep):
1. Pick a pattern from `COMMON_BUG_PATTERNS.md`
2. Use the detection grep command to find ALL occurrences across ALL modules
3. Apply the same fix recipe to each occurrence
4. `php -l` each modified file
5. Log each fix

**Batch fixes work for pattern-level bugs only.** Flow-level bugs require individual tracing.

---

## How Fixes Reach Clients

Fixes are made in **source first**, then applied to clients:
1. Fix and verify in source (`etail_development_src`)
2. When a client reports the same bug → fix is already known → apply in 5-15 min
3. During scheduled maintenance → proactively apply accumulated fixes
4. Use `fingerprint.php` to compare client code vs source

**Customization ≠ Bug**: Client added extra fields/features but core flow intact → preserve it. Only fix deviations from core flow steps.

---

## Fingerprint Tool

**Location**: `AI_Bug_Fix_System/fingerprint/fingerprint.php`

**Current:** Compares source vs client at function level, shows % divergence.
**Planned Enhancement:** Line-level diff, batch scan all clients, bug impact report, flow checklist integration.

---

## Existing Workflows (`.agent/workflows/`)

| Command | Purpose |
|---|---|
| `/fix-single-bug` | Fix one bug end-to-end |
| `/module-bug-audit` | 6-round module bug audit |
| `/build-module-brain` | Build/refresh module documentation |
| `/build-system-brain` | Update cross-module system docs |
| `/bug-intake-triage` | Classify incoming bug reports |
| `/setup-existing-client` | Set up bug system for existing client |
| `/setup-new-client` | Set up bug system for new client |
| `/sync-brain` | Sync source brain to client |

---

## Critical Coding Conventions

| Rule | Details |
|---|---|
| Transactions | Always `trans_begin()` / `trans_commit()` / `trans_rollback()`. NEVER `trans_complete()` |
| Validation | Always server-side. Never trust `$_POST` directly for column/table names |
| Tag Status | 0=available, 1=estimated, 2=billed. Check `TAG_STATUS_MAP.md` |
| Cancel Logic | MUST reverse ALL tables that save touched. Check `CLEANUP_GAPS.md` |
| Print vs Save | Print templates MUST read from same source and use same formula as save |
| OTP | Required for cancel/high-value operations. `===` comparison, not `==` |
| Config-Driven | Many behaviors controlled by `config.php` and `retail_settings` table — check before hardcoding |
| Bill Format | Multiple print formats (bill_format_1, bill_format_2). Each client may use different format |

---

## Safety Checks Before Every Fix

1. **Check `DANGER_ZONES.md`** before modifying any file listed there
2. **Check `SHARED_TABLES.md`** for cross-module impact
3. **Check `MODULE_DEPENDENCIES.md`** for downstream effects
4. **Never fix a flow bug without the flow checklist** — you'll miss reversal steps
5. **Test cancel after fixing save** — incomplete cancel = data corruption
6. **Save formula must match print formula** — #1 cause of display bugs
