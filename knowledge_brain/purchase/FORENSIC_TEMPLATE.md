# PURCHASE MODULE — FORENSIC TEMPLATE
> **Module:** Purchase | **Version:** 1.0 | **Date:** 2026-02-23

---

## Layer 1 — Symptom Collection

### Purchase Module Symptom Checklist

| # | Symptom | Likely Root Cause Area |
|---|---|---|
| 1 | Cannot create purchase order | Karigar not approved, missing product master data |
| 2 | Bill amount is wrong | Making charge formula, wastage calculation, GST config |
| 3 | Payment amount mismatch | Rate fixing not approved, wallet balance stale |
| 4 | QC items not showing | GRN not completed, bill_status check, fin_year filter |
| 5 | Lot not generating | QC/HM not completed, duplicate lot check blocking |
| 6 | Bill number not generating | Financial year missing, sequence gap |
| 7 | Rate fixing not reflected | Approval pending, wrong po_id linked |
| 8 | Items missing from form | Category/product inactive, JS dropdown not loading |
| 9 | Save button not responding | JS validation blocking, double-submit prevention |
| 10 | PDF/Print blank | DomPDF error, view data missing, empty query result |
| 11 | Weight calculations wrong | Gross/less/net formula, pure_wt calculation |
| 12 | GST amounts incorrect | Vendor GST status wrong, rate lookup error |
| 13 | Payment history empty | Filter dates wrong, bill_type mismatch |
| 14 | Cheque print incorrect | Bank details missing from karigar, billing model not loaded |
| 15 | Purchase return failing | Balance validation, stock not available for return |

---

## Layer 2 — Reproduce & Isolate

### Reproduction Checklist
- [ ] Identify the exact sub-module (PO, Bill Entry, QC, HM, Payment, etc.)
- [ ] Note the URL path and parameters
- [ ] Check which `$type` parameter is active in the switch
- [ ] Verify user access permissions for this route
- [ ] Check financial year is active
- [ ] Verify karigar/vendor is approved and active
- [ ] Try with a different karigar/vendor
- [ ] Try with a different product category
- [ ] Check if issue is page-load or save-time

### Isolation Questions
1. Does the issue happen for ALL vendors or one specific vendor?
2. Does it happen for ALL bill types (P/PM/PA) or one?
3. Is it a calculation error or a save error?
4. Is the error visible in the browser (toaster/alert) or silent?
5. Does it happen on list view or form view?

---

## Layer 3 — Client-Side Trace

### Console Log Points
| Variable | Where to Check | What It Shows |
|---|---|---|
| `ctrl_page` | Console: `ctrl_page` | Current page context (route array) |
| `categoryDetails` | Console: `categoryDetails` | Loaded categories |
| `metalDetails` | Console: `metalDetails` | Loaded metals |
| `purityDetails` | Console: `purityDetails` | Loaded purities |
| `stones` | Console: `stones` | Loaded stone data |
| `bank_details` | Console: `bank_details` | Bank details for payment |

### Network Tab Checks
| AJAX Call | Expected Response | Check If |
|---|---|---|
| `get_customer_order_pending_details` | JSON array | Empty = no pending orders |
| `get_karigar_wise_wastage` | JSON with wastage config | Missing = karigar config incomplete |
| `get_supplier_pay_details` | JSON with bills | Empty = no approved bills |
| `get_ActiveWeightRange` | JSON array | Empty = weight range not configured |
| `get_ActiveProducts` | JSON array | Empty = no products for category |

### Key JS Variables to Inspect
- `$('#select_karigar').val()` — Selected vendor
- `$('#po_ref_no').val()` — Bill reference
- `$('#grand_total').val()` — Calculated total
- `parseFloat($('.receive_amount').val())` — Payment amount

---

## Layer 4 — Server-Side Trace

### Controller Trace Points

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Order save fails | Controller | `purchase('save')` | L710-L2239 | Check `$_POST['order']` structure |
| Bill save fails | Controller | `purchase('purchase_entry')` | inside purchase() | Check `pur_entry_secret_key` session |
| QC save fails | Controller | `update_qc_status()` | L2705-2877 | Check item iteration logic |
| HM receipt fails | Controller | `update_halmarking_receipt()` | L4661-4861 | Check weight validation |
| Payment fails | Controller | `supplier_po_payment('save')` | L5317-6127 | Check wallet update |
| Rate fix fails | Controller | `rate_fixing('save')` | L6585-6924 | Check rate calculation |
| Return fails | Controller | `updateporeturnitems()` | L7822-8331 | Check stock adjustment |
| Lot fails | Controller | `generateLot()` | L11093-11318 | Check QC/HM pre-checks |
| GRN fails | Controller | `grnentry('save')` | L9450-10565 | Check GRN ref generation |

### Model Trace Points

| Symptom | Model Method | Line | What to Check |
|---|---|---|---|
| Wrong items shown | `get_purchase_entry_details()` | L626-668 | Filter conditions |
| Missing bill | `getPurchaseOrderDet()` | L2640-2667 | JOIN conditions |
| Wrong balance | `get_po_balance()` | L4842-4929 | Payment SUM query |
| Rate data wrong | `get_rate_fixing_items()` | L1600-1620 | Rate fix join |
| Empty dropdown | `get_KarigarOrders()` | L421-433 | WHERE filters |

---

## Layer 5 — Database Verification

### Quick Diagnostic Queries

```sql
-- Check purchase order and its items
SELECT * FROM ret_purchase_order WHERE po_id = {ID};
SELECT * FROM ret_purchase_order_item WHERE po_item_po_id = {ID};

-- Check QC status
SELECT qp.*, qid.* FROM ret_qc_process qp
JOIN ret_qc_issue_details qid ON qp.qc_process_id = qid.qc_process_id
WHERE qp.po_id = {PO_ID};

-- Check payment history
SELECT pp.*, ppd.* FROM ret_po_payment pp
LEFT JOIN ret_po_payment_detail ppd ON pp.pay_id = ppd.pay_id
WHERE pp.id_karigar = {KARIGAR_ID};

-- Check wallet balance
SELECT * FROM ret_wallet_account WHERE id_karigar = {KARIGAR_ID};

-- Check rate fixing status
SELECT * FROM pur_rat_fix_detail WHERE po_id = {PO_ID};

-- Check lot generation
SELECT li.*, lid.* FROM ret_lot_inward li
JOIN ret_lot_inward_detail lid ON li.id_lot_inward = lid.id_lot_inward
WHERE lid.po_item_id = {PO_ITEM_ID};
```

---

## Layer 6 — Root Cause Classification

| Category | Risk Level | Common in Purchase Module |
|---|---|---|
| **Data Integrity** | HIGH | Orphan items, unbalanced payments, missing stones |
| **Financial Calculation** | CRITICAL | Wrong making charge, GST miscalculation, rate fixing error |
| **Status Transition** | HIGH | Stuck in pending, skipped approval, wrong status code |
| **Cross-Module** | MEDIUM | Catalog changes breaking dropdowns, tagging dependency |
| **Session/Auth** | LOW | Secret key expiry, access time restriction |
| **UI/DOM** | MEDIUM | Dropdown not loading, form not submitting, toaster error |
| **Query Logic** | HIGH | Wrong JOIN type, missing WHERE clause, N+1 queries |

---

## Layer 7 — Transaction Integrity (Financial Module)

### Verify Transaction Wrapping
Check that these critical operations use `trans_start/trans_complete`:

| Operation | Controller Method | Expected Wrapping |
|---|---|---|
| Purchase order save | `purchase('save')` L754 | `$this->db->trans_begin()` ✓ |
| Bill entry save | `purchase('purchase_entry')` | Verify in code |
| QC issue save | `update_qc_issue()` L4055 | Verify |
| HM receipt save | `update_halmarking_receipt()` L4661 | Verify |
| Payment save | `supplier_po_payment('save')` | Verify |
| Return save | `updateporeturnitems()` L7822 | Verify |
| Lot generation | `generateLot()` L11093 | Verify |

### Partial Commit Risk Areas
| Risk | Where | Impact |
|---|---|---|
| `echo $this->db->last_query();exit;` before rollback | L322, L452, L631 | Kills rollback on failure, data corruption |
| Multiple table inserts in loop | `purchase('save')` item loop | If item N fails, items 1..N-1 already committed? |
| Wallet + payment in same tx | `supplier_po_payment` | Wallet updated but payment insert fails |

---

## Layer 8 — Tax Calculation Trace (GST Module)

### Tax Verification Steps
1. **Identify vendor GST status**: `ret_karigar.gst_no` — registered vs unregistered
2. **Determine bill type**: P (GST) vs PM (Non-GST)
3. **Calculate expected tax**:
   - Same state: CGST = base × cgst_rate, SGST = base × sgst_rate
   - Inter-state: IGST = base × igst_rate
4. **Verify in DB**: `SELECT * FROM ret_purchase_gst_detail WHERE po_id = {ID}`
5. **Compare**: Displayed value vs DB value vs calculated value
6. **Check TDS**: `get_karigar_wise_tds_percent()` — configured for karigar + FY?