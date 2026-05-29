## PUR-INT02 — Duplicate Total Value Display in Supplier Payment Reports and Supplier Ledger (Multiple PO)

| Field | Value |
|---|---|
| Severity | P1 |
| Track | A (System) |
| Category | Database / Query Logic |
| Sprint | Sprint 1 |
| Pattern Match | PAT-QRY-001 (Cartesian JOIN) |
| Module Brain | ✅ Ready |
| Reporter | Internal |
| Source | Internal |

### Steps to Reproduce
1. Create a Purchase Order (PO) for a supplier — e.g., PO-001
2. Create a second Purchase Order for the SAME supplier — e.g., PO-002
3. Create a Supplier Payment against BOTH PO-001 and PO-002 in a single payment transaction
4. Open **Purchase Payments Report** (via Report menu)
5. Open **Supplier Ledger Report** (Amount Ledger) for the same supplier

### Expected Behavior
- The payment total should appear **once** per payment transaction
- Supplier Ledger should reflect the payment amount **once**, correctly reducing the outstanding balance

### Actual Behavior
- The payment total is **duplicated** — displayed N times where N = number of POs in the payment
- This causes inflated totals in the Payment Report and incorrect running balance in the Supplier Ledger

### Evidence
Reported by user — confirmed by code analysis.

### Root Cause Analysis

**Purchase Payments Report (`get_po_payments()` — `ret_reports_model.php` L20457-20476)**

The query joins:
```
ret_po_payment_detail d           (1 row per payment mode e.g. cash/bank)
  LEFT JOIN ret_po_payment p      (1 row per payment transaction)
  LEFT JOIN ret_po_bill_payment_details po_bill  (N rows — 1 per PO covered)
  LEFT JOIN ret_purchase_order po (1 row per PO)
```

When a single payment covers 2 POs, `ret_po_bill_payment_details` has 2 rows for that `pay_id`. Each `ret_po_payment_detail` row JOINs with BOTH `po_bill` rows, producing **2 identical rows per payment mode** instead of 1. This is a classic **Cartesian JOIN** (PAT-QRY-001).

**Supplier Ledger (`getSupplierLedger()` — L9231+)**

Uses the MySQL view `ret_view_supplier_amount_ledger`. The payment portion of this view likely suffers from the same JOIN multiplication, causing duplicate debit/credit entries.

**PO Payment Report (`get_popaymentreport()` — L9161-9198)**

Uses the older `ret_po_payment` table with `GROUP BY pay_po_id`. When one payment covers multiple POs, this generates one row per PO with the full `pay_amt` (since `pay_po_id` is singular), duplicating the payment amount across POs.

### Affected Files
| File | Method | Line |
|---|---|---|
| `ret_reports_model.php` | `get_po_payments()` | L20457-20476 |
| `ret_reports_model.php` | `get_popaymentreport()` | L9161-9198 |
| `ret_reports_model.php` | `getSupplierLedger()` | L9231+ (via `ret_view_supplier_amount_ledger` view) |
| MySQL DB View | `ret_view_supplier_amount_ledger` | N/A |
