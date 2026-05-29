# Hotspot Map — Old Metal Process Module

Risk heatmap of the most dangerous areas in this module, based on bug density, data-sensitivity, and fix complexity.

---

## 🔥 CRITICAL HOTSPOTS

### H1 — Melting Receipt Save Block (Controller ~line 1450–1550)
**Risk Score: 10/10**
| Issue | Bug |
|---|---|
| Payment tab commented out — no payment data saved | OMP-019 |
| Net banking records cash amount | OMP-003 |
| Non-tag stock double-query silently fails | OMP-004 |
| `sales_item_details` uninitialized for tagged pockets | OMP-024 |
| Transaction failure response may be misleading | OMP-009 |

> **Impact**: Every single melting receipt saves no payment data and may have corrupted non-tag stock totals. This is the most dangerous block in the module.

---

### H2 — GST Tax Type Logic (`get_chg_tax_type`, ~line 1773 controller + model)
**Risk Score: 9/10**
| Issue | Bug |
|---|---|
| `id_company` used instead of `id_state` for state comparison | OMP-002 |
| `get_karigar_state()` uses raw `$_POST['karigar']` (SQL injection) | OMP-013 |
| IGST/CGST+SGST split applied to every refining receipt | — |

> **Impact**: Every refining receipt may have incorrect GST split, creating compliance liability.

---

### H3 — Category Modal System (form.php + JS 3096–4036)
**Risk Score: 8/10**
| Issue | Bug |
|---|---|
| All 3 modals share `id="category_row"` DOM collision | OMP-022 |
| Testing/refining charges bypass JS validation (class mismatch) | OMP-020, OMP-021 |
| Polishing non-tag validator checks disabled fields | OMP-027 |
| Validation class inconsistency across modal variants | multiple |

> **Impact**: Any receipt type involving category modal classification may silently classify to the wrong row or skip validation.

---

### H4 — Stock Update Functions (model lines 1133–1313)
**Risk Score: 8/10**
| Issue | Bug |
|---|---|
| `updateStockItemData`: net_wt updated with gross_wt value | OMP-015 |
| `updateStoneItemData`: id_branch set to id_product | OMP-011 |
| `checkPurchaseItemStockExist`: different signature from `check_purity_stock_details` — may be called incorrectly | — |
| Arithmetic updates (+=) under concurrent saves may drift | — |

> **Impact**: Stock summary tables have incorrect net_wt and cross-contaminated branch data for stone/pur items.

---

## 🟠 HIGH HOTSPOTS

### H5 — SQL Injection Surface (entire model)
**Risk Score: 7/10**
- ~40 functions concat user-controlled values directly into SQL
- Especially dangerous: `$_POST['karigar']` at line 1844 (**no CI input wrapper**)
- AJAX endpoints called from frontend pass `id_karigar`, `id_metal_pocket`, `id_metal_process` directly

---

### H6 — Process Number Generator (model lines 933–954)
**Risk Score: 6/10**
- No DB lock — race condition under concurrent saves
- All 8 process types (melting/testing/refining/polishing × issue/receipt) use same generator
- No unique constraint in schema to catch duplicates after the fact

---

### H7 — Testing Issue Validation (JS line 2877)
**Risk Score: 6/10**
- `validateTestingIssueRow()` queries wrong table (`#testing_receipt` instead of `#testing_process_details`)
- Testing issue can be submitted with zero items selected

---

## 🟡 MEDIUM HOTSPOTS

### H8 — Report Query Subsystem (model lines 1317–1484)
**Risk Score: 5/10**
- `get_refining_process_details()` has no WHERE clause
- Polishing receipt report has commented-out code vs replacement — may be inconsistent
- Date filtering uses `.html()` not `.val()` in JS — may silently ignore date range

---

### H9 — Pocket Status Management (model + controller)
**Risk Score: 5/10**
- Pocket never closed (`status` never set to 1)
- `get_polishing_pocket_details()` HAVING guard depends on float precision
- Stock over-issue possible with crafted POST

---

### H10 — PDF Acknowledgement (controller lines ~1578–1792)
**Risk Score: 3/10**
- DomPDF paper orientation typo ("portriat")
- `get_PolishingReceiptAcknowladgement()` joins via `lot_no` not `id_lot_inwards` — fragile string match

---

## Quick-Fix Priority Matrix

| Priority | Area | Fix Time | Business Impact |
|---|---|---|---|
| NOW | OMP-019: Uncomment payment block | 5 min | Payment recording restored |
| NOW | OMP-022: Fix duplicate modal table IDs | 30 min | Category data integrity |
| NOW | OMP-002: Fix GST id_company vs id_state | 15 min | Tax compliance |
| NOW | OMP-004: Fix double-query in stock check | 10 min | Non-tag stock accuracy |
| HIGH | OMP-003: Fix NB payment amount | 10 min | Financial records |
| HIGH | OMP-020/021: Fix validation class names | 20 min | Charges enforcement |
| HIGH | OMP-011: Fix id_branch in stone update | 10 min | Stock summary integrity |
| HIGH | OMP-024: Init sales_item_details for tagged/NT | 10 min | Tagged pocket visibility |
| HIGH | OMP-025: Fix validateTestingIssueRow table ref | 5 min | Issue submission |
| MED | OMP-027: Fix polishing validator for disabled fields | 30 min | Polish receipt UX |
| MED | OMP-023: Add WHERE to refining report | 10 min | Report accuracy |
| MED | OMP-013: Replace raw `$_POST` with CI input | 15 min | Security |
