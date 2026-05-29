# QUICK REFERENCE — Retail Dashboard Module
> Brain Version: 1.3 | Rounds: 12 | Last updated: 2026-03-16 | Bugs: 26 live

---

## TL;DR — Start Here

| Question | Answer |
|---|---|
| What does this module do? | Read-only analytics dashboard — KPIs for sales, stock, orders, billing, finance by branch/metal/date |
| What files own this? | 2 controllers + 2 models + 1 JS file (see below) |
| Is it safe to write to the DB? | ✅ Yes — this module is **100% read-only** (no INSERT/UPDATE/DELETE anywhere) |
| Can I test locally? | Yes — all data is served via AJAX, no server-side state changes |
| What's broken right now? | Bug #5 (Ledger widget **PHP fatal in production**), #21/#22/#23 (API data wrong), #25/#26 (security) |

---

## File Map (5 Files)

```
admin/application/controllers/
  admin_ret_dashboard.php         2,151 lines  Primary ctrl  — 39 AJAX handlers (cockpit/orders/stock)
  admin_ret_dashboard_api.php     1,998 lines  REST ctrl     — 38 REST methods + CORS wildcard ⚠️

admin/application/models/
  ret_dashboard_model.php         5,271 lines  Primary model — 97 methods (all SQL injection ⚠️)
  ret_dashboard_api_model.php     2,642 lines  API model     — 39 methods (all SQL injection ⚠️)
  ret_dashboard_api_model_backup.php           Backup copy   — compare before editing!

admin/assets/js/
  ret_dashboard.js               19,383 lines  Tab switching, AJAX, Google Charts + Chart.js
                                               Primary ctrl calls: L1-12180
                                               API ctrl calls:     L12181-19252 (33 mapped endpoints)
```

---

## URL Routing

| URL Pattern | Controller | Method Suffix |
|---|---|---|
| `/admin_ret_dashboard/<method>` | `admin_ret_dashboard.php` | Plain function name |
| `/admin_ret_dashboard_api/<method>_post` | `admin_ret_dashboard_api.php` | Must end in `_post` |
| `/admin_ret_dashboard_api/<method>_get` | `admin_ret_dashboard_api.php` | Must end in `_get` |

**Note:** API controller uses REST_Controller routing convention. A method named `get_Sales_glance_post()` handles `POST /admin_ret_dashboard_api/get_Sales_glance`.

---

## The 26 Bugs — Quick Triage

### 🔴 Fix NOW (Sprint 1 — production-breaking)

| # | Bug | Where | Fix Time |
|---|---|---|---|
| 5 | `getLedgerReportData()` missing → **fatal error** | `admin_ret_dashboard.php` L2112 | 2-4h |
| 21 | `$id_metal` undefined in `get_branch_wastage()` | `ret_dashboard_api_model.php` L666 | 15min |
| 22 | `$id_category` + `$data` undefined in account stock | `ret_dashboard_api_model.php` L2223 | 1-2h |
| 23 | `to_date` ignored in rate-cut P&L | `ret_dashboard_api_model.php` L2632 | 15min |
| 25 | `Access-Control-Allow-Origin: *` — any origin can call API | `admin_ret_dashboard_api.php` L3 | 15min |
| 26 | 4 methods hardcode `date()` ignoring user date range | API ctrl L1030/1069/1107/1193 | 30min |

### 🟡 High data-impact (Sprint 2)

| # | Bug | Where |
|---|---|---|
| 2 | Branch filter ignored in `get_CustomerDetails()` | Primary model L469 |
| 3 | No params passed to `get_branch_transfer_details()` | Primary ctrl L1241 |
| 4 | No date filter in `get_MetalBill_details()` | Primary model |
| 7 | Division by zero in saleschart | Primary ctrl L1391 |
| 8 | Old/new customer SQL identical (copy-paste) | Primary model |
| 12 | Division by zero in `get_store_sales()` | Primary model |
| 13 | `available_gwt` array key overwritten with nwt value | Primary model L3484, L4318 |

### 🔴 Security (Sprint 1/5)

| # | Bug | Scope |
|---|---|---|
| 1 | SQL injection — raw string interpolation | **ALL 136 model methods** |
| 25 | Wildcard CORS | API controller header |

---

## Debugging Map — Symptom → Suspect

| Symptom | Suspect | Check |
|---|---|---|
| Ledger Balance widget shows error / blank | Bug #5 | `getLedgerReportData` missing from `ret_reports_model` |
| Metal filter not working on wastage chart | Bug #21 | `get_branch_wastage()` signature missing `$id_metal` |
| Date range filter has no effect on VirtualTag/SalesReturn/Lot/CoverUp | Bug #26 | API ctrl L1030/1069/1107/1193 — hardcoded `date('Y-m-d')` |
| Account stock inwards always shows all data | Bug #22 | `$id_category` undefined in all sub-queries |
| Rate-cut P&L shows old data regardless of to_date | Bug #23 | SQL uses `from_date` as ceiling, not `BETWEEN` |
| Diamond weight always negative | Bug #27 | `blc_diawt` formula uses same var 4 times |
| Old vs New customer counts always equal | Bug #8 | SQL identical for both — `NOT EXISTS` bug |
| Stock GWT value shows NWT value | Bug #13 | `available_gwt` key overwritten |
| API tab accessible from foreign origin | Bug #25 | `Access-Control-Allow-Origin: *` at file top |

---

## External Model Dependencies

The dashboard controllers call into **3 other module models** — changes to these affect the dashboard:

| External Model | Method(s) Used | Context |
|---|---|---|
| `ret_reports_model` | `getBillDetails()` | Cash abstract flow (primary ctrl) |
| `ret_reports_model` | `get_LedgerBalanceAlert()` · `getLedgerReportData()` ⚠️ MISSING | Ledger alert widget |
| `ret_reports_model` | `gross_profit_report()` | GP report link |
| `ret_reports_model` | `getSupplierTransactionList()` | API ctrl supplier CR/DE + txn list |
| `ret_reports_model` | `getLotwiseTaggedVault()` | API ctrl weight gain/loss |
| `ret_reports_model` | `getTaggeditems()` | API ctrl tag detail drill |
| `ret_catalog_model` | `getActiveMetals()` | API ctrl metal filter list |

---

## Key Business Constants

```php
// Bill types
1=Sales  2=Sales+Purchase  3=Sales+Return  4=Purchase
5=OrderAdvance  6=Advance  7=SalesReturn  8=CreditBillPayment
9=OrderDelivery  10=ChitPreClose  11=RepairOrderDelivery
12=SupplierSalesBill  13=SalesTransfer  14=SalesRetTransfer

// Estimation status (est_status column)
1=Sold  0=Unbilled  2=Returned  else=In Process

// Karigar order status (order_for)
1=Karigar  2=Customer

// Customer order status
0=received  1=placed  2=allocated  3=WIP  4=ready  5=delivered

// Tag status (latest log entry)
0=Available  6=Returned-to-Branch
```

---

## COLOUR_CODE Warning

Defined in **3 files** — must stay in sync:
1. `ret_dashboard_model.php` (PHP const)
2. `ret_dashboard_api_model.php` (PHP const)
3. `ret_dashboard.js` (JS array)

---

## Brain Artifacts (11 total)

| File | Purpose |
|---|---|
| [MODULE_BRAIN.md](MODULE_BRAIN.md) | Full module overview, all bugs, all risks — start here |
| [METHOD_INDEX.md](METHOD_INDEX.md) | Every method in all 4 files (§7a–§7f) |
| [DATA_FLOW.md](DATA_FLOW.md) | 8 JS→ctrl→model→DB flow traces |
| [SCHEMA_ANALYSIS.md](SCHEMA_ANALYSIS.md) | 80+ tables, columns, relationships |
| [BUSINESS_RULES.md](BUSINESS_RULES.md) | 18 business rules encoded in SQL |
| [CROSS_MODULE_MAP.md](CROSS_MODULE_MAP.md) | 36+ external deps + Mermaid diagram |
| [FORENSIC_TEMPLATE.md](FORENSIC_TEMPLATE.md) | 8-layer debug runbook |
| [ANTI_PATTERNS.md](ANTI_PATTERNS.md) | 13 recurring code patterns with ✅/❌ examples |
| [BUG_SPRINT.md](BUG_SPRINT.md) | 5 sprints, 26 bugs, effort estimates |
| [SECURITY_AUDIT.md](SECURITY_AUDIT.md) | 8 security risks with attack vectors |
| [COVERAGE_TRACKER.md](COVERAGE_TRACKER.md) | 12-round history, progress log |
