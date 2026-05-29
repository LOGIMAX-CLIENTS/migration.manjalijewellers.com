# 🧠 Module Brain: Old Metal Process
**Module**: `old_metal_process`
**Controller**: `admin/application/controllers/admin_ret_metal_process.php`
**Model**: `admin/application/models/ret_metal_process_model.php`
**JS**: `admin/assets/js/ret_metal_process.js`
**Views**: `admin/application/views/ret_metal_process/`
**Brain Created**: 2026-03-14
**Brain Last Updated**: 2026-03-24
**Brain Version**: 1.2 (17 rounds, 63 bugs, FLOW_RISK_MATRIX added)

---

## 1. Module Purpose

The **Old Metal Process** module manages the lifecycle processing of old/scrap metal collected from customers (usually during jewellery purchases/exchanges). The module handles:

- **Pocketing** – Grouping collected old metal items (loose metal, tagged items, non-tagged items) into a physical "pocket" for batch processing
- **Melting** – Sending pocket(s) to a karigar (artisan) for melting, tracking issue and receipt of melted material, categorised by product type and purity
- **Testing** – Sending melted output to testing karigar; recording tested purity/weight and receiving back certified materials
- **Refining** – Sending tested output for chemical refining; recording refined weight by category
- **Polishing** – Sending pocketed items for polishing and receiving back finished goods with stock-in to lot inwards

All process types share a master-detail structure driven by `id_metal_process` (1=Melting, 2=Testing, 3=Refining, 4=Polishing) and `process_for` (1=Issue, 2=Receipt).

---

## 2. File Inventory

| File | Lines | Role |
|---|---|---|
| `admin_ret_metal_process.php` | 1732 | Controller – routing, save logic, AJAX endpoints |
| `ret_metal_process_model.php` | 1867 | Model – all DB reads and writes |
| `ret_metal_process.js` | 5088 | Frontend – dynamic UI, AJAX calls, calculations (40 AJAX endpoints) |
| `views/ret_metal_process/metal_process/form.php` | 34753 bytes | Main process entry form |
| `views/ret_metal_process/metal_process/list.php` | 4527 bytes | Process list/data table |
| `views/ret_metal_process/metal_process/process_acknowladgement.php` | 31611 bytes | PDF acknowledgement view |
| `views/ret_metal_process/pocket/form.php` | 15458 bytes | Pocket creation form |
| `views/ret_metal_process/pocket/list.php` | 4390 bytes | Pocket list |
| `views/ret_metal_process/process_master/form.php` | 4321 bytes | Process master setup |
| `views/ret_metal_process/process_master/list.php` | 2944 bytes | Process master list |
| `views/ret_metal_process/reports/detailed_report.php` | 4151 bytes | Pocket-wise detailed report |
| `views/ret_metal_process/reports/process_report.php` | 6515 bytes | Process summary report |

---

## 3. Controller Map

### Class: `Admin_ret_metal_process` (extends CI_Controller)
**Constant**: `RET_PROCESS_MODEL = 'ret_metal_process_model'`
**Constant**: `VIEW_FOLDER = 'ret_metal_process/'`

| Method | Route Type | Purpose |
|---|---|---|
| `__construct` | — | Loads model, session, log_model, admin_settings_model |
| `index($type, $id)` | switch | Dashboard + process list |
| `metal_pocket($type)` | switch | Pocket CRUD: list/add/save/ajax |
| `get_metal_stock_list()` | AJAX GET | Returns available old metal stock (bills, categories, amounts) |
| `process_master($type, $id)` | switch | Process master CRUD |
| `get_ActiveMetalProcess()` | AJAX GET | Returns active process types |
| `get_melting_issue_details()` | AJAX POST | Karigar melting issue items |
| `metal_process($type)` | switch | Main process entry (list/add/save/ajax) |
| `process_acknowladgement($id)` | PDF | Generates PDF acknowledgement for any process |
| `metal_process_receipt($type)` | switch | Pocket receipt listing |
| `get_pocket_details()` | AJAX POST | Pocket items for melting issue |
| `get_polish_pocket_details()` | AJAX GET | Pockets available for polishing |
| `get_KarigarMeltingIssueDetilas()` | AJAX POST | Melting issues for a karigar |
| `get_testing_issue_details()` | AJAX POST | Melted items ready for testing |
| `get_testing_receipt_details()` | AJAX POST | Testing items pending receipt |
| `get_RefiningIssueDetails()` | AJAX GET | Testing-complete items for refining |
| `get_RefiningReceiptDetails()` | AJAX POST | Refining issues for receipt |
| `get_Active_Refining()` | AJAX GET | Active refining process nos |
| `get_PolishingReceiptDetails()` | AJAX POST | Polishing issues for receipt |
| `get_ActiveCategoryPurity()` | AJAX GET | Category-purity mappings |
| `process_report($type, $id)` | switch | Process summary report + detailed report |
| `get_active_design_products()` | AJAX GET | Design → product mappings |
| `get_active_sub_design_products()` | AJAX GET | Sub-design → product mappings |
| `get_opening_metal_stock_list()` | AJAX POST | Opening balance metal stock |
| `get_chg_tax_type()` | AJAX POST | Determines GST type (IGST/CGST+SGST) based on karigar state vs company state |
| `get_repair_pending_order()` | AJAX POST | Pending repair orders |

---

## 4. Business Logic by Process Type

### 4.1 Pocketing (metal_pocket/save)
- Items come from 3 source types: `trans_type` 1=Old Metal (loose), 2=Tagged Items, 3=Non-tagged items
- `is_against_opening` flag supports opening stock pockets
- Validates total pieces and gross weight > 0
- Inserts: `ret_old_metal_pocket` (header), `ret_old_metal_pocket_details` (line items)
- Tags items: updates `ret_taging` status, `ret_old_metal_pocket_details.type = 1` marks as processed

### 4.2 Melting Issue (id_metal_process=1, process_for=1)
- Selects pocket(s) → generates `ret_old_metal_melting` header
- Per pocket/category, inserts `ret_old_metal_melting_details` (issue records)
- Updates `ret_old_metal_pocket` with issued weight/pcs/purity
- Validates: issue_pcs ≤ balance_pcs, issue_gwt ≤ balance_gwt, issue_purity ≤ 100

### 4.3 Melting Receipt (id_metal_process=1, process_for=2)
- Selects pending melting, enters: gross_wt, net_wt, received_less_wt, receipt_charges
- Inserts `ret_old_metal_melting` header with `id_old_metal_process_receipt`
- Per category-product, inserts `ret_old_metal_melting_recd_details`
- Updates non-tag stock: `ret_nontag_item` / `ret_nontag_item_log` / `ret_section_nontag_item_log`
- Updates lot inwards: `ret_lot_inwards` (lot_from=2 for melting) + `ret_lot_inwards_detail`
- On commit: logs to `ret_old_metal_melting.melting_status = 1`

### 4.4 Testing Issue (id_metal_process=2, process_for=1)
- Source: melting receipt records with `melting_status = 1` (completed melting)
- Inserts `ret_old_metal_testing` (net_wt, purity, amount per melting_recd)
- Sets `melting_status = 2` (Testing Issue) on `ret_old_metal_melting_recd_details`

### 4.5 Testing Receipt (id_metal_process=2, process_for=2)
- Updates `ret_old_metal_testing`: received_wt, received_purity, production_loss, receipt_charges
- Sets `testing_status = 1`, `melting_status = 3`
- Per product in `category_details` JSON payload:
  - Inserts `ret_lot_inwards` (lot_from=4) + `ret_lot_inwards_detail`
  - Conditionally upserts `ret_nontag_item` + logs

### 4.6 Refining Issue (id_metal_process=3, process_for=1)
- Source: `melting_status = 3` (testing complete) records
- Inserts `ret_old_metal_refining` with `issue_weight`, `refining_status=0`
- Sets `melting_status = 4`

### 4.7 Refining Receipt (id_metal_process=3, process_for=2)
- Updates `ret_old_metal_refining`: refining_status=1, receipt_charges, GST breakdown (CGST/SGST/IGST)
- Per category in `category_details` JSON: inserts `ret_old_metal_refining_details`
- Conditionally upserts `ret_nontag_item` stock

### 4.8 Polishing Issue (id_metal_process=4, process_for=1)
- Source: pockets with `trans_type=1` and `issue_nwt < net_wt`
- Inserts `ret_old_metal_polishing` header + `ret_old_metal_polishing_details` per pocket
- Updates `ret_old_metal_pocket` issued quantities

### 4.9 Polishing Receipt (id_metal_process=4, process_for=2)
- Source: `ret_old_metal_polishing_details` with `status=0`
- Updates `ret_old_metal_polishing_details` with received qty/wt
- Groups received items by (category × purity × is_non_tag)
- Creates `ret_lot_inwards` (lot_from=5) + `ret_lot_inwards_detail` per group
- Upserts non-tag stock and logs

### 4.10 Payment Recording
- For receipt transactions: supports Cash and Net Banking payments
- Inserts `ret_old_metal_process_payment` (id_old_metal_process, type, payment_mode, amount)
- **Bug**: Net Banking payment records `cash_amount` instead of `net_banking_amount`

---

## 5. Database Tables (Primary)

| Table | Purpose |
|---|---|
| `ret_old_metal_process` | Master process header (karigar, process type, number, date) |
| `ret_old_metal_process_master` | Process type definitions (Melting=1, Testing=2, Refining=3, Polishing=4) |
| `ret_old_metal_pocket` | Pocket header (pieces, wt, purity, trans_type, status) |
| `ret_old_metal_pocket_details` | Pocket line items (old_metal_sale_id or tag_id or product) |
| `ret_old_metal_melting` | Melting transaction header |
| `ret_old_metal_melting_details` | Melting issue detail per pocket/category |
| `ret_old_metal_melting_recd_details` | Melting receipt detail per product |
| `ret_old_metal_testing` | Testing issue/receipt record |
| `ret_old_metal_refining` | Refining issue record |
| `ret_old_metal_refining_details` | Refining receipt detail per category |
| `ret_old_metal_polishing` | Polishing header |
| `ret_old_metal_polishing_details` | Polishing detail per pocket |
| `ret_old_metal_polishing_recd_details` | Polishing receipt items |
| `ret_old_metal_process_payment` | Payment records for process receipts |
| `ret_old_metal_type` | Old metal type definitions |
| `ret_nontag_item` | Non-tagged item stock summary |
| `ret_nontag_item_log` | Non-tagged item movement log |
| `ret_section_nontag_item_log` | Section-wise non-tagged log |
| `ret_lot_inwards` | Lot creation table (stock inward header) |
| `ret_lot_inwards_detail` | Lot line items |
| `smith_company_op_balance` | Opening balance for old metal stock |

---

## 6. Status Code Reference

### `melting_status` (ret_old_metal_melting_recd_details)
| Value | Meaning |
|---|---|
| 0 | Melting Issued |
| 1 | Melting Receipt Done |
| 2 | Testing Issued |
| 3 | Testing Receipt Done |
| 4 | Refining Issued |
| 5 | Refining Completed |

### `process_for`
| Value | Meaning |
|---|---|
| 1 | Issue |
| 2 | Receipt |

### `id_metal_process`
| Value | Meaning |
|---|---|
| 1 | Melting |
| 2 | Testing |
| 3 | Refining |
| 4 | Polishing |

### `trans_type` (pocket)
| Value | Meaning |
|---|---|
| 1 | Old Metal (loose) |
| 2 | Tagged Items |
| 3 | Non-tagged Items |

### `lot_from` (ret_lot_inwards)
| Value | Meaning |
|---|---|
| 2 | Melting |
| 4 | Testing |
| 5 | Polishing |

---

## 7. Key Shared Models / Cross-Module Dependencies

| Model | Used For |
|---|---|
| `admin_settings_model` | Company details, access permissions |
| `log_model` | Audit trail logging |
| `ret_karigar` | Karigar (artisan) lookup |
| `ret_category` | Product category |
| `ret_purity` | Purity definitions |
| `ret_nontag_item` | Non-tagged item stock |
| `ret_lot_inwards` + `ret_lot_inwards_detail` | Lot/stock inward integration |
| `ret_billing` / `ret_bill_old_metal_sale_details` | Purchase bill old metal trace |
| `customerorder` / `customerorderdetails` | Repair order integration |

---

## 8. Known Bugs & Risk Areas

See [BUG_PATTERNS.md](./BUG_PATTERNS.md) for full list (63 bugs, 15 rounds).
See [FIX_GUIDE.md](./FIX_GUIDE.md) for surgical fix instructions per bug.

**Final Bug Count**: 63 (14 Critical | 13 High | 16 Medium | 19 Low | 1 Retracted)

### P0 — Production Blockers (Fix Immediately)
- **OMP-019** 🔴: Payment tab entirely commented out in `form.php` — all receipt transactions have zero payment records
- **OMP-039** 🔴: Duplicate `moneyFormatIndia()` in `process_acknowladgement.php` → PHP fatal on PDF print
- **OMP-038** 🟠: NT pocket rows appended twice → doubled weight/pieces on save
- **OMP-028** 🔴: Polishing receipt `ret_lot_inwards.created_branch = NULL` (undefined variable `$id_branch`)

### Critical Security / Financial
- **OMP-001 / OMP-051 / OMP-057** 🔴: 30+ raw SQL concat injection points throughout model
- **OMP-052** 🔴: `$arith` operator injection in 6 UPDATE functions (`+`/`-` not whitelisted)
- **OMP-047** 🔴: No CSRF protection on any POST handler
- **OMP-002** 🔴: `get_chg_tax_type()` uses `id_company` instead of `id_state` → wrong GST type
- **OMP-003** 🔴: NB payment records `cash_amount` instead of `net_banking_amount`
- **OMP-055 / OMP-004** 🔴: Double-query in `check_purity_stock_details()` → stock check always returns FALSE
- **OMP-053** 🔴: `updateStoneItemData()` uses `id_product` for `id_branch` in WHERE → wrong rows updated
- **OMP-056** 🔴: `get_karigar_state()` reads `$_POST` directly in model layer

### High Severity
- **OMP-005** 🟠: Race condition in process number generation (no DB lock)
- **OMP-006** 🟠: No server-side weight limit validation (JS-only, bypassable)
- **OMP-007** 🟠: Pocket never marked closed after full issue (`status` stays 0)
- **OMP-008** 🟠: `melting_status` state transitions unprotected
- **OMP-009** 🟠: Transaction failures may return ambiguous response
- **OMP-029** 🟠: Refining receipt piece count hardcoded to 1
- **OMP-037** 🟠: Duplicate `#select_metal_process` change handler (double row insert)
- **OMP-041** 🔴: Testing receipt charges bypass JS validation (wrong class selector)
- **OMP-043** 🟠: Purity average divided by total rows not checked rows (same flaw in OMP-060)
- **OMP-060** 🟠: `calculate_tag_list()` same purity-average flaw as OMP-043
- **OMP-061** 🟠: `get_tag_search_list()` — 9 unquoted HTML attribute values → DOM injection
- **OMP-042** 🟠: Duplicate radio button IDs in process_master form

### Medium Severity (select)
- **OMP-022** 🟡: `#category_row` DOM ID collision across 3 receipt modals
- **OMP-025** 🟡: `validateTestingIssueRow()` queries wrong table selector
- **OMP-054** 🟡: `get_refining_process_details()` missing WHERE clause (returns all records)
- **OMP-048** 🟡: Root list view uses wrong DataTable ID `pocket_list`
- **OMP-044** 🟡: `validateTestingIssueRow()` has no return statement
- Full list: OMP-010 through OMP-013, OMP-015, OMP-020–023, OMP-024, OMP-025, OMP-054, OMP-057, OMP-062, OMP-063

---

## 9. Process Flow Diagram

```
Old Metal Collected (in bill)
        │
        ▼
  [POCKET CREATION]
  ret_old_metal_pocket
  (trans_type: 1=OldMetal, 2=Tagged, 3=NonTagged)
        │
        ▼
  [MELTING ISSUE]       ──► karigar
  ret_old_metal_melting
  ret_old_metal_melting_details
  melting_status = 0
        │
        ▼
  [MELTING RECEIPT]
  ret_old_metal_melting_recd_details
  melting_status = 1
  → lot_inwards (lot_from=2)
  → nontag_item stock updates
        │
        ▼
  [TESTING ISSUE]       ──► testing karigar
  ret_old_metal_testing
  melting_status = 2
        │
        ▼
  [TESTING RECEIPT]
  testing_status = 1
  melting_status = 3
  → lot_inwards (lot_from=4)
  → nontag_item stock updates
        │
        ├──► [REFINING ISSUE]  ──► refinery
        │    ret_old_metal_refining
        │    melting_status = 4
        │        │
        │        ▼
        │    [REFINING RECEIPT]
        │    ret_old_metal_refining_details
        │    refining_status = 1
        │    → nontag_item stock updates
        │
        └──► [POLISHING ISSUE] ──► polishing karigar
             ret_old_metal_polishing
             ret_old_metal_polishing_details
                 │
                 ▼
             [POLISHING RECEIPT]
             ret_old_metal_polishing_recd_details
             → lot_inwards (lot_from=5)
             → nontag_item stock updates
```

---

## 10. AI Diagnostic Quick Guide

### "Process not saving"
1. Check `db->trans_status()` — any DB error rolls back silently
2. Verify `id_metal_process` and `process_for` match the branch in controller `save` switch
3. Check if `process_no` duplicate exists (race condition in `generate_process_number`)

### "Wrong stock after receipt"
1. Check `checkNonTagItemExist()` — inserts vs updates branch logic
2. Verify `ret_lot_inwards.lot_from` value is set correctly per process type
3. Check `melting_status` value on `ret_old_metal_melting_recd_details`

### "Pocket items not showing"
1. Verify `trans_type` on `ret_old_metal_pocket` matches the filter in `get_pocket_details()`
2. Check `status = 0` filter — pocket may already be closed
3. Check `issue_nwt < net_wt` HAVING clause — partial issue pockets may drop out

### "PDF acknowledgement blank"
1. Verify `get_metal_process($id)` returns `id_metal_process` correctly
2. Check `process_for` — determines which sub-query runs in `process_acknowladgement()`

### "Testing receipt details missing"
1. `get_testing_receipt_details()` filters `testing_status = 0` AND matches `id_karigar`
2. Must have existing `ret_old_metal_testing` record linked to `id_melting_recd`
