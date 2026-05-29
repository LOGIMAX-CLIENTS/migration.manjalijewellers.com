# Ret_Reports — Business Rules

> **Module**: Ret_Reports
> **Last Updated**: 2026-03-26 — Round 8 (header sync + line resync)

---

## Overview

Ret_Reports is primarily a read-only module, so business rules here are mostly about **data aggregation formulas**, **display logic**, and **access control** rather than transactional calculations.

---

## RULE-RPT-001: Access Control per Report
**Formula**: Every report checks user permissions before returning data.
**Implementation**: Controller AJAX handlers → `$this->admin_settings_model->get_access('admin_ret_reports/{method}/list')`
**Validation**: Server-side only
**Edge cases**: Access object returned in JSON — JS decides which buttons to show. If `get_access` fails silently, all buttons visible.

---

## RULE-RPT-002: Cash Deposit Opening/Closing Calculation
**Formula**:
```
opening = previous closing
cash_inward = total_cash_payments - previous_deposited - closing
closing = opening + cash_inward - deposit_amount
```
**Implementation**: Controller `deposit_report()` L7024-7178
**Validation**: Server-side only (calculated in controller loop)
**Edge cases**: First record has opening = 0. Negative closing possible if deposit > available cash.

---

## RULE-RPT-003: Day Transaction Payment Mode Aggregation
**Formula**:
```
total_receipts = cash + card + paytm + cheque + net_banking + cashfree
total_payments = cash + card + paytm + cheque + net_banking + cashfree
```
**Implementation**: Controller `daytransactions()` L6800-6920
**Validation**: Server-side only
**Edge cases**: `reporttype == 1` → Receipts, `reporttype == 2` → Payments. Any new payment mode NOT in this list gets silently dropped.

---

## RULE-RPT-004: Indian Money Format
**Formula**: INR formatting with commas: `XX,XX,XXX.XX`
**Implementation**: Controller `moneyFormatIndia()` L7246-7257
**Validation**: Server-side formatting only
**Edge cases**: Negative numbers not handled explicitly.

---

## RULE-RPT-005: Stock Book Closing Balance
**Formula**:
```
Section Tag Closing = Opening + Inward - Sold - Branch_Out - Section_Out - Return - Issue
```
Per weight type: gross_wt, net_wt, dia_wt, grm_wt, ct_wt
**Implementation**: Controller `ho_daily_stock_book()` L8848-8852
**Validation**: Server-side only
**Edge cases**: Any missing movement type zeros out → inaccurate closing if data source query changes.

---

## RULE-RPT-006: Stock Book Stone Type Classification
**Formula**:
```
if (stone_type == 0)         → Ornaments (gross_wt, net_wt, dia_wt)
if (stone_type == 1, uom ≠ 6) → Stone in Grams
if (stone_type == 1, uom == 6) → Stone in Carats
else                         → Diamond
```
**Implementation**: Controller `ho_daily_stock_book()` L8859-8873
**Validation**: Server-side only
**Edge cases**: New stone_type values not handled — fall into "Diamond" bucket.

---

## RULE-RPT-007: Green Tag Marking/Unmarking
**Formula**:
```
If marking (req_status == 1):
  tag_mark = 1, green_tag_date = now(), green_tag_marked_by = user_id
If unmarking (req_status == 0):
  tag_mark = 0, green_tag_date = NULL, green_tag_marked_by = NULL
  unmark_by = user_id, unmark_date = now()
```
**Implementation**: Controller `update_green_tag()` L183-240
**Validation**: Server-side with transaction wrapping
**Edge cases**: Uses `$tag['req_status']` outside the loop at L225 — references last iteration's value (bug in log message if processing mixed mark/unmark).

---

## RULE-RPT-008: Lot Profit/Loss Calculation
**Formula**:
```
balance_grswt = lot_grswt - tag_grswt - receipt_grswt - lot_merge_grswt
If balance < 0 → Profit (negative means more tagged than lot received = gain)
If balance > 0 → Loss (positive means lot received more than tagged = loss)
```
**Implementation**: Controller `ho_daily_stock_book()` L8974-8985
**Validation**: Server-side only
**Edge cases**: Counter-intuitive sign convention — negative = profit, positive = loss.

---

## RULE-RPT-009: Reorder Level Detection
**Formula**:
```
reorder_needed = (current_stock < reorder_min)
```
Based on `ret_reorder_settings` table configuration per product/design/branch.
**Implementation**: Model `get_reorder_details()` L27901
**Validation**: Server-side query-based
**Edge cases**: Reorder settings per branch — if not configured, item never triggers reorder.

---

## RULE-RPT-010: Stock Age Calculation
**Formula**:
```
stock_age_days = DATEDIFF(current_date, tag_entry_date)
```
Bucketed into age ranges (0-30, 31-60, 61-90, 91-180, 181-365, 365+).
**Implementation**: Model `getStockAgeDetails()`, `getDynamicStockAgeDetails()` L28668
**Validation**: Server-side SQL
**Edge cases**: Uses tag entry date, not purchase date. Re-tagged items reset age.

---

## RULE-RPT-011: Inventory Turnover Ratio
**Formula**:
```
Turnover = Total_Sold_Weight / Average_Stock_Weight
Average_Stock = (Opening + Closing) / 2
```
**Implementation**: Model `get_inventory_turnover_details()` L28069
**Validation**: Server-side calculation
**Edge cases**: Division by zero if average stock = 0. Module handles this with IFNULL/COALESCE in SQL.

---

## RULE-RPT-012: GST Abstract Calculation
**Formula**:
```
CGST = taxable_amount × cgst_rate / 100
SGST = taxable_amount × sgst_rate / 100
IGST = taxable_amount × igst_rate / 100
Total_Tax = CGST + SGST + IGST
```
**Implementation**: Model `get_gst_abstract_with_return_details()` L25530, references `ret_taxgroupitems`, `ret_taxmaster`
**Validation**: Server-side (SQL aggregation)
**Edge cases**: Inter-state vs intra-state determines IGST vs CGST+SGST split.

---

## RULE-RPT-013: Section Stock Closing (Non-Tag)
**Formula**:
```
closing_gwt = opening_gwt + inward_gwt - sales_gwt - branch_out_gwt - purchase_return_gwt - karigar_issue_gwt
closing_nwt = opening_nwt + inward_nwt - sales_nwt - branch_out_nwt - karigar_issue_nwt
```
Note: `closing_nwt` formula OMITS `purchase_return_nwt` — potential calculation gap.
**Implementation**: Controller `ho_daily_stock_book()` L8918-8919
**Validation**: Server-side only
**Edge cases**: Missing `pur_ret_nwt` in net weight closing — may cause discrepancy between gross and net totals.
