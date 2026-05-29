# PURCHASE MODULE — BUSINESS RULES
> **Module:** Purchase | **Version:** 1.0 | **Date:** 2026-02-23

---

## Purchase Order Rules

```
RULE-PUR-001: Purchase Order Number Generation
Formula: FY_CODE + '-' + Sequential_Number (e.g., 26-27-0001)
Implementation: Model generatePurNo() at L138-162
Validation: Server-side only
Edge cases: Financial year transition may cause duplicate if not handled
```

```
RULE-PUR-002: Order Type Determines Item Source
Values: 1=Stock Order (free items), 2=Customer Order (from pending cust orders), 3=Stock Repair
Implementation: Controller purchase('save') L728-752
Validation: Client-side (JS order type selector)
Edge cases: Customer order ref must exist if type=2
```

```
RULE-PUR-003: Rate Type Determines Cost Calculation
Values: 1=Per-piece (item_cost field used), 2=Per-gram (weight × rate)
Implementation: Controller L750, JS L8210-8234
Validation: Both client and server
Edge cases: rate_type defaults to 2 if not provided
```

```
RULE-PUR-004: Due Date Must Be Future
Formula: smith_due_dt > current_date
Implementation: JS L7689-7723 (change event on .smith_due_dt)
Validation: Client-side only
Edge cases: No server-side validation — stale page can submit past dates
```

```
RULE-PUR-005: Piece Count Cannot Exceed Order
Formula: entered_pcs <= ordered_pcs (cus_ord_pcs)
Implementation: JS L6939-6978 (change event on .piece)
Validation: Client-side only
Edge cases: No server-side check. Multiple partial entries could exceed total.
```

---

## Supplier Bill Entry Rules

```
RULE-PUR-006: Bill Reference Number Format
Formula: bill_type_prefix + Sequential_Number
  P-XXXX = GST Purchase (registered vendor)
  PM-XXXX = Non-GST Purchase (unregistered vendor)
  PA-XXXX = Approval Stock Purchase
Implementation: Model generatePurRefOrderNo($is_suspense_stock, $gst_bill_type) L187-212
Validation: Server-side
Edge cases: is_suspense_stock flag changes prefix
> **Note (PUR-INT01)**: The controller must pass the exact same `is_suspense_stock` value to the generation method as it saves to the DB. A mismatch causes prefix assignments to diverge from the sequence, breaking `MAX()` tracking.
```

```
RULE-PUR-007: Bill Status Values
  0 = Active/Open
  1 = Completed
  2 = Cancelled (with cancel_reason)
Implementation: Controller L583, L595
Validation: Server-side
Edge cases: Cancel sets bill_status=2, cancelled_date, cancelled_by
```

```
RULE-PUR-008: Double Submit Prevention
Formula: Session-based secret key checked before save
Implementation: Controller uses pur_entry_secret_key session
Validation: Server-side
Edge cases: Session expiry could block legitimate saves
```

---

## QC Process Rules

```
RULE-PUR-009: QC Reference Number Generation
Formula: QC + FY_CODE + Sequential
Implementation: Model get_qc_ref_no() L787-807
Validation: Server-side
```

```
RULE-PUR-010: QC Status Values
  0 = Pending
  1 = Passed
  2 = Failed/Rejected
Implementation: Controller update_qc_status() L2705-2877
Validation: Server-side
Edge cases: Failed items go to purchase return flow
```

```
RULE-PUR-011: Only GRN-Completed Items Can Enter QC
Formula: PO item must have GRN status = completed
Implementation: Model purchase_issue() L818-867 (WHERE clause filter)
Validation: Server-side (query filter)
```

---

## Hallmarking Rules

```
RULE-PUR-012: Only QC-Passed Items Can Be Sent for HM
Formula: QC status = 1 (passed) required
Implementation: Model get_pending_halmarking_items() L1017-1037
Validation: Server-side (query filter)
```

```
RULE-PUR-013: HM Rejection Weight Cannot Exceed Issued Weight
Formula: rejection_wt <= issued_wt
Implementation: Controller update_halmarking_receipt() L4661-4861
Validation: Should be both; verify JS validation exists
Edge cases: Partial rejection creates split lot
```

```
RULE-PUR-014: Hallmarking Charges Are Mandatory
Formula: hm_charges > 0 required for receipt
Implementation: Controller update_halmarking_receipt()
Validation: Check both sides
```

---

## Rate Fixing Rules

```
RULE-PUR-015: Rate Fixing Date Must Be On/After GRN Date
Formula: rate_fix_date >= grn_date
Implementation: Rate fixing form validation
Validation: Should be both; verify in JS
```

```
RULE-PUR-016: Rate Fix Requires Approval
Formula: rate_fix_status transitions: 0(pending) → 1(approved) / 2(rejected)
Implementation: Controller update_ratefix_approval() L6479-6551
Validation: Server-side
Edge cases: Rejected rate fix must be resubmitted with new rate
```

```
RULE-PUR-017: Metal Value Calculation
Formula: metal_value = net_weight × fixed_rate
Implementation: Rate fixing save logic
Validation: Server-side calculation
```

---

## Payment Rules

```
RULE-PUR-018: Payment Modes
Values: Cash | Bank | Metal Adjustment
Implementation: Controller supplier_po_payment() L5317-6127
Validation: Both
Edge cases: Metal adjustment requires weight-to-value conversion at current rate
```

```
RULE-PUR-019: Payment Amount Cannot Exceed Outstanding
Formula: payment_amount <= outstanding_amount
Implementation: JS L10566-10604 (keyup event on receive_amount)
Validation: Client-side (warning toaster); should also be server-side
Edge cases: Multiple simultaneous payments could exceed if both check before commit
```

```
RULE-PUR-020: Cheque Number Uniqueness
Formula: cheque_no must not exist in ret_po_payment
Implementation: Controller check_cheque_number_exist() L83-116, Model L8971-8982
Validation: Both (AJAX pre-check + server-side)
```

```
RULE-PUR-021: Payment Number Generation
Formula: PAY + FY_CODE + Sequential
Implementation: Model generatePaymentNo() L163-186
Validation: Server-side
```

---

## Lot Generation Rules

```
RULE-PUR-022: Lot Generation Prerequisites
Formula: All preceding steps must be complete:
  - GRN completed
  - Bill entry exists
  - QC passed (or HM passed if HM required)
Implementation: Controller generate_lot() L2883-3367, checks via check_purchase_halmarking_details()
Validation: Server-side
Edge cases: Items with HM go through different path than non-HM items
```

```
RULE-PUR-023: No Duplicate Lot Items
Formula: Check if po_item_id already exists in lot_inward_detail
Implementation: Model checkLotItemExist() L3262-3273
Validation: Server-side
```

---

## Purchase Return Rules

```
RULE-PUR-024: Return Reference Number Format
Formula: PR + type_prefix + Sequential
Implementation: Model pur_ret_refno($PurType) L1957-1974
Validation: Server-side
```

```
RULE-PUR-025: Return Stock Adjustment
Formula: On return, update ret_purchase_item_stock_summary (reduce stock)
  AND update ret_wallet_account (credit supplier ledger)
Implementation: Controller updateporeturnitems() L7822-8331
Validation: Server-side
Edge cases: Partial returns, returns with different weights than original
```

```
RULE-PUR-031: Rejected Weight Report Scope (RPT-CLT02)
Formula: rejected_gwt and rejected_purewt must only appear on Return rows in the Purchase Payments Report
  - Payment rows: rejected_gwt = 0, rejected_purewt = 0
  - Ratecut rows: rejected_gwt = 0, rejected_purewt = 0
  - Return rows: rejected_gwt = SUM(pur_ret_gwt), rejected_purewt = SUM(pur_ret_pur_wt)
Implementation: Model get_po_payments() L20487 in ret_reports_model.php — UNION query with 0-hardcoded in 1st SELECT
Validation: Server-side (SQL structure)
Edge cases: Footer totals in JS sum all rows — only return rows contribute non-zero values
```

---

## GST/Tax Rules

```
RULE-PUR-026: GST Computation
Formula:
  If same-state (CGST+SGST): CGST = base × cgst_rate, SGST = base × sgst_rate
  If inter-state (IGST): IGST = base × igst_rate
  TCS/TDS: Applied based on vendor configuration
Implementation: Controller purchase('purchase_entry'), bill form JS
Validation: Both
Edge cases: Vendor GST status (registered vs unregistered) changes bill type and tax
```

```
RULE-PUR-027: TDS Percentage Lookup
Formula: TDS% = karigar_wise_tds_percent based on id_karigar + financial_year
Implementation: Controller get_tds_percent() L12981-13000, Model get_karigar_wise_tds_percent() L4823-4841
Validation: Server-side
```

---

## General Rules

```
RULE-PUR-028: Financial Year Requirement
Formula: Active financial year must exist for all document number generation
Implementation: Model get_FinancialYear() L133-137
Validation: Server-side
Edge cases: Missing financial year causes empty doc numbers
```

```
RULE-PUR-029: Access Control per Page
Formula: Each page load checks get_access(route_path) from admin_settings_model
Implementation: Controller — every 'list'/'add' case
Validation: Server-side
Edge cases: Access object passed to view for button-level control
```

```
RULE-PUR-030: Audit Logging
Formula: All create/update/delete operations must log to sys_log via log_model
Implementation: Controller — after each successful transaction commit
Validation: Server-side
Edge cases: Log write failure should not rollback main transaction
```